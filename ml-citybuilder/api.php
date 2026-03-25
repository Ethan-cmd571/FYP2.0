<?php
require_once "config.php";
header("Content-Type: application/json");

$action = $_GET["action"] ?? "";

function json_input(): array {
    $raw = file_get_contents("php://input");
    if (!$raw) return [];
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function respond($data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function normalize_difficulty(?string $d): string {
    $d = strtolower(trim((string)$d));
    return in_array($d, ["easy", "medium", "hard"], true) ? $d : "easy";
}

function reward_multiplier(string $difficulty): float {
    return match ($difficulty) {
        "medium" => 1.35,
        "hard" => 1.8,
        default => 1.0
    };
}

function penalties_for(string $difficulty): array {
    return match ($difficulty) {
        "medium" => ["revenue" => 20, "happiness" => 6, "xp" => 10],
        "hard" => ["revenue" => 35, "happiness" => 10, "xp" => 20],
        default => ["revenue" => 10, "happiness" => 3, "xp" => 5]
    };
}

function progress_column(string $difficulty): string {
    return match ($difficulty) {
        "medium" => "current_medium",
        "hard" => "current_hard",
        default => "current_easy"
    };
}


function task_looks_code_based(array|string $taskOrPrompt, ?array $context = null): bool {
    $prompt = is_array($taskOrPrompt) ? (string)($taskOrPrompt["prompt"] ?? "") : (string)$taskOrPrompt;
    $context = is_array($taskOrPrompt) ? (json_decode((string)($taskOrPrompt["context"] ?? ""), true) ?: []) : ($context ?? []);

    $haystack = strtolower($prompt . " " . json_encode($context));
    $codeSignals = [
        "import ", "from sklearn", "train_test_split", "fit(", "predict(", "transform(",
        "logisticregression", "decisiontree", "randomforest", "kmeans", "classification_report",
        "confusion_matrix", "df[", "iloc[", "loc[", "x_train", "x_test", "y_train", "y_test",
        "model.", ".fit", ".predict", "plt.", "np.", "pd.", "axis=1", "drop(", "read_csv(",
        "def ", "return ", "for ", "while ", "if ", "elif ", "else:"
    ];

    foreach ($codeSignals as $signal) {
        if (str_contains($haystack, $signal)) {
            return true;
        }
    }

    $hasFormattingHints = str_contains($prompt, "\n") || preg_match('/[`=\[\]\(\){}._]/', $prompt);
    return (bool)$hasFormattingHints;
}

function mastery_level(int $accuracy): string {
    return $accuracy >= 75 ? "strong" : ($accuracy >= 50 ? "medium" : "weak");
}

function compute_code_mastery(PDO $pdo, int $userId): array {
    $stmt = $pdo->prepare("
        SELECT ua.ml_topic, ua.was_correct, ua.response_seconds, t.prompt, t.context
        FROM user_attempts ua
        JOIN tasks t ON t.task_id = ua.task_id
        WHERE ua.user_id = ?
        ORDER BY ua.id ASC
    ");
    $stmt->execute([$userId]);

    $topics = [];
    $attempts = 0;
    $correctTotal = 0;
    $responseTotal = 0.0;

    foreach ($stmt->fetchAll() as $row) {
        $context = json_decode((string)($row["context"] ?? ""), true) ?: [];
        if (!task_looks_code_based((string)$row["prompt"], $context)) {
            continue;
        }

        $topic = (string)$row["ml_topic"];
        $attempts++;
        $correct = (int)$row["was_correct"];
        $correctTotal += $correct;
        $responseTotal += (int)$row["response_seconds"];

        if (!isset($topics[$topic])) {
            $topics[$topic] = [
                "topic" => $topic,
                "attempts" => 0,
                "correct_total" => 0,
                "avg_response_seconds" => 0
            ];
        }

        $topics[$topic]["attempts"]++;
        $topics[$topic]["correct_total"] += $correct;
        $topics[$topic]["avg_response_seconds"] += (int)$row["response_seconds"];
    }

    $topicRows = [];
    foreach ($topics as $topic => $row) {
        $topicAttempts = max(1, (int)$row["attempts"]);
        $accuracy = (int)round(((int)$row["correct_total"] / $topicAttempts) * 100);
        $topicRows[] = [
            "topic" => $topic,
            "attempts" => (int)$row["attempts"],
            "accuracy" => $accuracy,
            "avg_response_seconds" => round(((float)$row["avg_response_seconds"]) / $topicAttempts, 1),
            "level" => mastery_level($accuracy)
        ];
    }

    usort($topicRows, function (array $a, array $b): int {
        return [$b["accuracy"], $b["attempts"], $a["topic"]] <=> [$a["accuracy"], $a["attempts"], $b["topic"]];
    });

    $accuracyPct = $attempts > 0 ? (int)round(($correctTotal / $attempts) * 100) : 0;
    $avgResponse = $attempts > 0 ? round($responseTotal / $attempts, 1) : 0;

    return [
        "summary" => [
            "attempts" => $attempts,
            "correct_total" => $correctTotal,
            "accuracy_pct" => $accuracyPct,
            "avg_response_seconds" => $avgResponse,
            "level" => mastery_level($accuracyPct)
        ],
        "topics" => $topicRows
    ];
}

function safe_exec(PDO $pdo, string $sql): void {
    try { $pdo->exec($sql); } catch (Throwable $e) {}
}

function bootstrap_schema(PDO $pdo): void {
    safe_exec($pdo, "CREATE TABLE IF NOT EXISTS activity_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        event_type VARCHAR(40) NOT NULL,
        message VARCHAR(255) NOT NULL,
        meta_json JSON NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user_created (user_id, created_at),
        CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    safe_exec($pdo, "CREATE TABLE IF NOT EXISTS badges (
        id INT AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(60) NOT NULL UNIQUE,
        name VARCHAR(100) NOT NULL,
        description VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    safe_exec($pdo, "CREATE TABLE IF NOT EXISTS user_badges (
        user_id INT NOT NULL,
        badge_id INT NOT NULL,
        earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (user_id, badge_id),
        CONSTRAINT fk_ub_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        CONSTRAINT fk_ub_badge FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $seed = $pdo->prepare("INSERT IGNORE INTO badges (code, name, description) VALUES (?, ?, ?)");
    $seed->execute(['course_easy_complete', 'Foundations Finisher', 'Completed the full Foundations / Easy curriculum.']);
    $seed->execute(['course_medium_complete', 'Intermediate Finisher', 'Completed the full Intermediate / Medium curriculum.']);
    $seed->execute(['course_hard_complete', 'Advanced Finisher', 'Completed the full Advanced / Hard curriculum.']);
}

function log_activity(PDO $pdo, int $userId, string $eventType, string $message, ?array $meta = null): void {
    $stmt = $pdo->prepare("INSERT INTO activity_log (user_id, event_type, message, meta_json) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $eventType, $message, $meta ? json_encode($meta) : null]);
}

function award_badge(PDO $pdo, int $userId, string $badgeCode): ?array {
    $stmt = $pdo->prepare("SELECT id, code, name, description FROM badges WHERE code = ?");
    $stmt->execute([$badgeCode]);
    $badge = $stmt->fetch();
    if (!$badge) return null;

    $ins = $pdo->prepare("INSERT IGNORE INTO user_badges (user_id, badge_id) VALUES (?, ?)");
    $ins->execute([$userId, (int)$badge["id"]]);
    if ($ins->rowCount() > 0) {
        return [
            "code" => $badge["code"],
            "name" => $badge["name"],
            "description" => $badge["description"]
        ];
    }
    return null;
}

function maybe_award_course_badge(PDO $pdo, int $userId, string $difficulty): ?array {
    $progressCol = progress_column($difficulty);
    $stmt = $pdo->prepare("SELECT {$progressCol} AS progress FROM user_state WHERE user_id = ?");
    $stmt->execute([$userId]);
    $progress = (int)($stmt->fetchColumn() ?: 1);

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE difficulty = ?");
    $countStmt->execute([$difficulty]);
    $taskCount = (int)$countStmt->fetchColumn();

    if ($taskCount > 0 && $progress > $taskCount) {
        $code = match ($difficulty) {
            "medium" => "course_medium_complete",
            "hard" => "course_hard_complete",
            default => "course_easy_complete"
        };
        return award_badge($pdo, $userId, $code);
    }
    return null;
}

function ensure_user(PDO $pdo, string $username): int {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $row = $stmt->fetch();
    if ($row) {
        $userId = (int)$row["id"];
        $pdo->prepare("INSERT IGNORE INTO user_state (user_id) VALUES (?)")->execute([$userId]);
        return $userId;
    }
    $pdo->prepare("INSERT INTO users (username) VALUES (?)")->execute([$username]);
    $userId = (int)$pdo->lastInsertId();
    $pdo->prepare("INSERT INTO user_state (user_id, houses) VALUES (?, 1)")->execute([$userId]);
    return $userId;
}

function state_bundle(PDO $pdo, int $userId): array {
    $stmt = $pdo->prepare("SELECT * FROM user_state WHERE user_id = ?");
    $stmt->execute([$userId]);
    $state = $stmt->fetch();
    if (!$state) respond(["error" => "State not found"], 404);

    $districts = [
        "easy" => ["label" => "Foundations District", "building" => "🏠", "count" => (int)$state["houses"], "progress" => min(100, (int)$state["current_easy"] * 2)],
        "medium" => ["label" => "Evaluation District", "building" => "🏭", "count" => (int)$state["factories"], "progress" => min(100, (int)$state["current_medium"] * 2)],
        "hard" => ["label" => "Advanced District", "building" => "🔬", "count" => (int)$state["labs"], "progress" => min(100, (int)$state["current_hard"] * 2)],
        "wellbeing" => ["label" => "Wellbeing District", "building" => "🌳", "count" => (int)$state["parks"], "progress" => max(0, min(100, (int)$state["happiness"]))]
    ];

    $masteryStmt = $pdo->prepare("SELECT ml_topic, attempts, correct_count FROM user_topic_stats WHERE user_id = ? ORDER BY ml_topic ASC");
    $masteryStmt->execute([$userId]);
    $mastery = [];
    foreach ($masteryStmt->fetchAll() as $row) {
        $attempts = (int)$row["attempts"];
        $correct = (int)$row["correct_count"];
        $acc = $attempts > 0 ? round(($correct / $attempts) * 100) : 0;
        $level = $acc >= 75 ? "strong" : ($acc >= 50 ? "medium" : "weak");
        $mastery[] = ["topic" => $row["ml_topic"], "attempts" => $attempts, "accuracy" => $acc, "level" => $level];
    }

    $accStmt = $pdo->prepare("
        SELECT 
            COUNT(*) AS attempts,
            SUM(was_correct) AS correct_total,
            SUM(CASE WHEN was_correct = 0 THEN 1 ELSE 0 END) AS incorrect_total,
            AVG(response_seconds) AS avg_response_seconds
        FROM user_attempts
        WHERE user_id = ?
    ");
    $accStmt->execute([$userId]);
    $accRow = $accStmt->fetch() ?: [];
    $attempts = (int)($accRow["attempts"] ?? 0);
    $correctTotal = (int)($accRow["correct_total"] ?? 0);
    $incorrectTotal = (int)($accRow["incorrect_total"] ?? 0);
    $accuracyPct = $attempts > 0 ? (int)round(($correctTotal / $attempts) * 100) : 0;
    $avgResponse = $attempts > 0 ? round((float)($accRow["avg_response_seconds"] ?? 0), 1) : 0;

    $leaderboard = $pdo->query("
        SELECT u.username, us.revenue, us.xp
        FROM user_state us
        JOIN users u ON u.id = us.user_id
        ORDER BY us.revenue DESC, us.xp DESC, u.username ASC
        LIMIT 10
    ")->fetchAll();

    $activityStmt = $pdo->prepare("
        SELECT event_type, message, created_at
        FROM activity_log
        WHERE user_id = ?
        ORDER BY created_at DESC, id DESC
        LIMIT 12
    ");
    $activityStmt->execute([$userId]);
    $activity = $activityStmt->fetchAll();

    $badgesStmt = $pdo->prepare("
        SELECT b.code, b.name, b.description, ub.earned_at
        FROM user_badges ub
        JOIN badges b ON b.id = ub.badge_id
        WHERE ub.user_id = ?
        ORDER BY ub.earned_at DESC, b.id DESC
    ");
    $badgesStmt->execute([$userId]);
    $badges = $badgesStmt->fetchAll();

    return [
        "state" => [
            "revenue" => (int)$state["revenue"],
            "happiness" => (int)$state["happiness"],
            "xp" => (int)$state["xp"],
            "current_easy" => (int)$state["current_easy"],
            "current_medium" => (int)$state["current_medium"],
            "current_hard" => (int)$state["current_hard"]
        ],
        "districts" => $districts,
        "mastery" => $mastery,
        "code_mastery" => compute_code_mastery($pdo, $userId),
        "accuracy_stats" => [
            "attempts" => $attempts,
            "correct_total" => $correctTotal,
            "incorrect_total" => $incorrectTotal,
            "accuracy_pct" => $accuracyPct,
            "avg_response_seconds" => $avgResponse
        ],
        "leaderboard" => $leaderboard,
        "activity" => $activity,
        "badges" => $badges
    ];
}

bootstrap_schema($pdo);

if ($action === "login") {
    $data = json_input();
    $username = trim((string)($data["username"] ?? ""));
    if ($username === "" || strlen($username) > 50) respond(["error" => "Invalid username"], 400);
    $userId = ensure_user($pdo, $username);
    log_activity($pdo, $userId, "login", "Logged in as {$username}");
    respond(["ok" => true, "user_id" => $userId, "username" => $username] + state_bundle($pdo, $userId));
}

if ($action === "sign_out") respond(["ok" => true]);

if ($action === "get_state") {
    $userId = (int)($_GET["user_id"] ?? 0);
    if ($userId <= 0) respond(["error" => "Missing user_id"], 400);
    respond(["ok" => true] + state_bundle($pdo, $userId));
}

if ($action === "topic_mastery") {
    $userId = (int)($_GET["user_id"] ?? 0);
    if ($userId <= 0) respond(["error" => "Missing user_id"], 400);
    respond(["ok" => true, "mastery" => state_bundle($pdo, $userId)["mastery"]]);
}

if ($action === "next_task") {
    $userId = (int)($_GET["user_id"] ?? 0);
    $difficulty = normalize_difficulty($_GET["difficulty"] ?? "easy");
    if ($userId <= 0) respond(["error" => "Missing user_id"], 400);

    $progressCol = progress_column($difficulty);
    $stmt = $pdo->prepare("SELECT {$progressCol} AS current_step FROM user_state WHERE user_id = ?");
    $stmt->execute([$userId]);
    $currentStep = max(1, (int)($stmt->fetchColumn() ?: 1));

    $taskStmt = $pdo->prepare("SELECT * FROM tasks WHERE difficulty = ? AND order_index = ? ORDER BY task_id ASC LIMIT 1");
    $taskStmt->execute([$difficulty, $currentStep]);
    $task = $taskStmt->fetch();

    if (!$task) {
        $taskStmt = $pdo->prepare("SELECT * FROM tasks WHERE difficulty = ? ORDER BY order_index ASC, task_id ASC LIMIT 1");
        $taskStmt->execute([$difficulty]);
        $task = $taskStmt->fetch();
    }
    if (!$task) respond(["error" => "No task found"], 404);

    log_activity($pdo, $userId, "task_start", "Started a {$difficulty} task on {$task['ml_topic']}", [
        "difficulty" => $difficulty,
        "topic" => $task["ml_topic"],
        "order_index" => (int)$task["order_index"],
            "is_code_task" => task_looks_code_based($task)
    ]);

    respond([
        "ok" => true,
        "task" => [
            "task_id" => (int)$task["task_id"],
            "ml_topic" => $task["ml_topic"],
            "task_type" => $task["task_type"],
            "prompt" => $task["prompt"],
            "options" => json_decode($task["options"], true),
            "correct_answer" => json_decode($task["correct_answer"], true),
            "context" => json_decode($task["context"], true),
            "reward_points" => (int)round((int)$task["reward_points"] * reward_multiplier($difficulty)),
            "difficulty" => $difficulty,
            "order_index" => (int)$task["order_index"],
            "is_code_task" => task_looks_code_based($task)
        ]
    ]);
}

if ($action === "submit_answer") {
    $data = json_input();
    $userId = (int)($data["user_id"] ?? 0);
    $taskId = (int)($data["task_id"] ?? 0);
    $answer = $data["answer"] ?? null;
    if ($userId <= 0 || $taskId <= 0) respond(["error" => "Missing params"], 400);

    $taskStmt = $pdo->prepare("SELECT * FROM tasks WHERE task_id = ?");
    $taskStmt->execute([$taskId]);
    $task = $taskStmt->fetch();
    if (!$task) respond(["error" => "Task not found"], 404);

    $difficulty = normalize_difficulty($task["difficulty"]);
    $correctAnswer = json_decode($task["correct_answer"], true);
    $context = json_decode($task["context"], true);
    $isCorrect = trim((string)$answer) === trim((string)$correctAnswer);
    $reward = (int)round((int)$task["reward_points"] * reward_multiplier($difficulty));
    $happinessGain = $difficulty === "hard" ? 6 : ($difficulty === "medium" ? 4 : 2);

    $stateStmt = $pdo->prepare("SELECT * FROM user_state WHERE user_id = ?");
    $stateStmt->execute([$userId]);
    $state = $stateStmt->fetch();
    if (!$state) respond(["error" => "User state missing"], 404);

    $responseSeconds = max(0, (int)($data["response_seconds"] ?? 0));

    $pdo->prepare("
        INSERT INTO user_topic_stats (user_id, ml_topic, attempts, correct_count)
        VALUES (?, ?, 1, ?)
        ON DUPLICATE KEY UPDATE attempts = attempts + 1, correct_count = correct_count + VALUES(correct_count)
    ")->execute([$userId, $task["ml_topic"], $isCorrect ? 1 : 0]);

    $pdo->prepare("INSERT INTO user_attempts (user_id, task_id, ml_topic, difficulty, was_correct, response_seconds) VALUES (?, ?, ?, ?, ?, ?)")
        ->execute([$userId, $taskId, $task["ml_topic"], $difficulty, $isCorrect ? 1 : 0, $responseSeconds]);

    if ($isCorrect) {
        $progressCol = progress_column($difficulty);
        $buildingCol = match ($difficulty) {
            "medium" => "factories",
            "hard" => "labs",
            default => "houses"
        };
        $pdo->prepare("
            UPDATE user_state
            SET revenue = revenue + ?,
                happiness = LEAST(100, happiness + ?),
                xp = xp + ?,
                {$progressCol} = {$progressCol} + 1,
                {$buildingCol} = {$buildingCol} + 1
            WHERE user_id = ?
        ")->execute([$reward, $happinessGain, $reward, $userId]);

        if ((int)$state["revenue"] + $reward >= 200 && (int)$state["parks"] < 1) {
            $pdo->prepare("UPDATE user_state SET parks = parks + 1 WHERE user_id = ?")->execute([$userId]);
        }

        log_activity($pdo, $userId, "task_correct", "Correct answer in {$task['ml_topic']} (+{$reward} revenue)", [
            "difficulty" => $difficulty,
            "topic" => $task["ml_topic"],
            "reward" => $reward,
            "happiness_gain" => $happinessGain
        ]);

        $newBadge = maybe_award_course_badge($pdo, $userId, $difficulty);
        if ($newBadge) {
            log_activity($pdo, $userId, "badge", "Earned badge: {$newBadge['name']}", ["badge_code" => $newBadge["code"]]);
        }

        respond(["ok" => true, "correct" => true, "reward" => $reward, "happiness_gain" => $happinessGain, "context" => $context, "new_badge" => $newBadge] + state_bundle($pdo, $userId));
    } else {
        $penalties = penalties_for($difficulty);
        $pdo->prepare("
            UPDATE user_state
            SET revenue = GREATEST(0, revenue - ?),
                happiness = GREATEST(0, happiness - ?),
                xp = GREATEST(0, xp - ?)
            WHERE user_id = ?
        ")->execute([$penalties["revenue"], $penalties["happiness"], $penalties["xp"], $userId]);

        log_activity($pdo, $userId, "task_wrong", "Wrong answer in {$task['ml_topic']} (-{$penalties['revenue']} revenue)", [
            "difficulty" => $difficulty,
            "topic" => $task["ml_topic"],
            "penalty_revenue" => $penalties["revenue"],
            "penalty_happiness" => $penalties["happiness"]
        ]);

        respond([
            "ok" => true,
            "correct" => false,
            "penalty_revenue" => $penalties["revenue"],
            "penalty_happiness" => $penalties["happiness"],
            "penalty_xp" => $penalties["xp"],
            "correct_answer" => $correctAnswer,
            "context" => $context
        ] + state_bundle($pdo, $userId));
    }
}

respond(["error" => "Unknown action"], 404);
?>