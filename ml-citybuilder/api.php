<?php
require_once "config.php";
header("Content-Type: application/json");

$action = $_GET['action'] ?? '';

$pdo->exec("CREATE TABLE IF NOT EXISTS user_topic_progress (
  user_id INT NOT NULL,
  topic VARCHAR(120) NOT NULL,
  correct_count INT NOT NULL DEFAULT 0,
  updated_at TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (user_id, topic)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

$pdo->exec("CREATE TABLE IF NOT EXISTS user_learning_analytics (
  user_id INT NOT NULL PRIMARY KEY,
  challenges_started INT NOT NULL DEFAULT 0,
  attempts_total INT NOT NULL DEFAULT 0,
  correct_total INT NOT NULL DEFAULT 0,
  incorrect_total INT NOT NULL DEFAULT 0,
  hints_viewed INT NOT NULL DEFAULT 0,
  boosters_used INT NOT NULL DEFAULT 0,
  lessons_acknowledged INT NOT NULL DEFAULT 0,
  total_response_seconds INT NOT NULL DEFAULT 0,
  last_answered_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");


function json_input() {
  $raw = file_get_contents("php://input");
  if (!$raw) return [];
  $decoded = json_decode($raw, true);
  return is_array($decoded) ? $decoded : [];
}

function respond($data, $status = 200) {
  http_response_code($status);
  echo json_encode($data);
  exit;
}

function ensure_user($pdo, $username) {
  $stmt = $pdo->prepare("SELECT id FROM users WHERE username=?");
  $stmt->execute([$username]);
  $existing = $stmt->fetch();
  if ($existing) {
    $user_id = (int)$existing['id'];
    $pdo->prepare("INSERT IGNORE INTO user_state(user_id) VALUES(?)")->execute([$user_id]);
    return $user_id;
  }

  $pdo->prepare("INSERT INTO users(username) VALUES(?)")->execute([$username]);
  $user_id = (int)$pdo->lastInsertId();
  $pdo->prepare("INSERT INTO user_state(user_id) VALUES(?)")->execute([$user_id]);
  return $user_id;
}

function ensure_tiles($pdo, $user_id, $w = 10, $h = 10) {
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM city_tiles WHERE user_id=?");
  $stmt->execute([$user_id]);
  $count = (int)$stmt->fetchColumn();
  if ($count >= $w * $h) return;

  $pdo->beginTransaction();
  $ins = $pdo->prepare("INSERT IGNORE INTO city_tiles(user_id,x,y,building,blevel) VALUES(?,?,?,?,1)");
  for ($y = 0; $y < $h; $y++) {
    for ($x = 0; $x < $w; $x++) {
      $ins->execute([$user_id, $x, $y, 'empty']);
    }
  }
  $pdo->commit();
}

function log_event($pdo, $user_id, $type, $message, $meta = null) {
  $stmt = $pdo->prepare("INSERT INTO activity_log(user_id,event_type,message,meta_json) VALUES(?,?,?,?)");
  $stmt->execute([$user_id, $type, $message, $meta ? json_encode($meta) : null]);
}

function award_badge($pdo, $user_id, $code) {
  $stmt = $pdo->prepare("SELECT id FROM badges WHERE code=?");
  $stmt->execute([$code]);
  $badge_id = $stmt->fetchColumn();
  if (!$badge_id) return false;
  $ins = $pdo->prepare("INSERT IGNORE INTO user_badges(user_id,badge_id) VALUES(?,?)");
  $ins->execute([$user_id, (int)$badge_id]);
  return $ins->rowCount() > 0;
}

function parse_task_payload($taskRow) {
  $correct = json_decode($taskRow['correct_json'], true);
  if (!is_array($correct)) $correct = [];

  $payload = [
    'task_id' => (int)$taskRow['id'],
    'prompt' => $taskRow['prompt'],
    'task_type' => $taskRow['task_type'],
    'reward_points' => (int)$taskRow['reward_points'],
    'context' => $correct['context'] ?? null,
    'feedback' => $correct['feedback'] ?? null,
  ];

  if ($taskRow['task_type'] === 'mcq') {
    $payload['options'] = $correct['options'] ?? [];
  }
  if ($taskRow['task_type'] === 'code_mcq') {
    $payload['code'] = $correct['code'] ?? '';
    $payload['options'] = $correct['options'] ?? [];
  }
  if ($taskRow['task_type'] === 'ordering') {
    $payload['items'] = $correct['items'] ?? [];
  }
  if ($taskRow['task_type'] === 'match') {
    $payload['left'] = $correct['left'] ?? [];
    $payload['right'] = $correct['right'] ?? [];
  }
  return [$payload, $correct];
}


function ensure_learning_rows($pdo, $user_id) {
  $pdo->prepare("INSERT IGNORE INTO user_learning_analytics(user_id) VALUES(?)")->execute([$user_id]);
}

function get_topic_progress($pdo, $user_id) {
  ensure_learning_rows($pdo, $user_id);
  $stmt = $pdo->prepare("SELECT topic, correct_count FROM user_topic_progress WHERE user_id=? ORDER BY correct_count DESC, topic ASC");
  $stmt->execute([$user_id]);
  return $stmt->fetchAll();
}

function get_learning_analytics($pdo, $user_id) {
  ensure_learning_rows($pdo, $user_id);
  $stmt = $pdo->prepare("SELECT * FROM user_learning_analytics WHERE user_id=?");
  $stmt->execute([$user_id]);
  $row = $stmt->fetch();
  if (!$row) return [
    'challenges_started' => 0, 'attempts_total' => 0, 'correct_total' => 0, 'incorrect_total' => 0,
    'hints_viewed' => 0, 'boosters_used' => 0, 'lessons_acknowledged' => 0, 'total_response_seconds' => 0,
    'accuracy_pct' => 0
  ];
  $attempts = (int)$row['attempts_total'];
  $correct = (int)$row['correct_total'];
  $row['accuracy_pct'] = $attempts > 0 ? (int)round(($correct / $attempts) * 100) : 0;
  return $row;
}

function record_topic_mastery($pdo, $user_id, $topic) {
  if (!$topic) return;
  $stmt = $pdo->prepare("
    INSERT INTO user_topic_progress(user_id, topic, correct_count)
    VALUES(?,?,1)
    ON DUPLICATE KEY UPDATE correct_count = correct_count + 1
  ");
  $stmt->execute([$user_id, $topic]);
}

function district_rules($state, $city, $districtBuildings = null) {
  $tasks = (int)$state['tasks_correct'];
  $revenue = (int)$state['revenue'];
  $ordering = (int)$state['ordering_correct'];
  $code = (int)$state['code_correct'];
  $happy = (int)$state['happiness'];
  $labCount = (int)($city['lab'] ?? 0);

  $districtBuildings = is_array($districtBuildings) ? $districtBuildings : [
    'central' => 0,
    'industry' => 0,
    'green' => 0,
    'innovation' => 0,
  ];

  $buildingProgress = [
    'central' => min(100, (int)$districtBuildings['central'] * 20),
    'industry' => min(100, (int)$districtBuildings['industry'] * 25),
    'green' => min(100, (int)$districtBuildings['green'] * 25),
    'innovation' => min(100, (int)$districtBuildings['innovation'] * 35),
  ];

  $rules = [
    'central' => [
      'unlocked' => true,
      'note' => 'Your starting downtown zone for homes and early growth.',
      'progress' => max($buildingProgress['central'], min(100, max(15, $tasks * 20))),
    ],
    'industry' => [
      'unlocked' => ($tasks >= 2 || $revenue >= 250),
      'note' => 'Unlocked by building economic momentum.',
      'progress' => max($buildingProgress['industry'], min(100, max(($tasks * 25), min(100, (int)round($revenue / 2.5))))),
    ],
    'green' => [
      'unlocked' => ($ordering >= 1 || $tasks >= 3 || $happy >= 55),
      'note' => 'Unlocked by planning the ML pipeline and improving wellbeing.',
      'progress' => max($buildingProgress['green'], min(100, max($ordering * 50, ($tasks * 20), ($happy - 40) * 5))),
    ],
    'innovation' => [
      'unlocked' => ($code >= 1 || $tasks >= 5 || $labCount >= 1),
      'note' => 'Unlocked by coding or advanced ML progress.',
      'progress' => max($buildingProgress['innovation'], min(100, max($code * 60, $tasks * 15, $labCount ? 100 : 0))),
    ],
  ];

  foreach ($rules as $key => &$rule) {
    $rule['buildings'] = (int)($districtBuildings[$key] ?? 0);
    if ($rule['unlocked'] && $rule['buildings'] > 0) {
      $rule['note'] .= ' Visible growth now contributes directly to this progress bar.';
    }
  }
  unset($rule);

  return $rules;
}

function unlocked_buildings($state, $city) {
  $tasks = (int)$state['tasks_correct'];
  $revenue = (int)$state['revenue'];
  $ordering = (int)$state['ordering_correct'];
  $code = (int)$state['code_correct'];
  $happy = (int)$state['happiness'];

  $out = ['house'];
  if ($tasks >= 2 || $revenue >= 300 || ($city['factory'] ?? 0) >= 1) $out[] = 'factory';
  if ($ordering >= 1 || $tasks >= 3 || $happy >= 55 || ($city['park'] ?? 0) >= 1) $out[] = 'park';
  if ($code >= 1 || $tasks >= 5 || ($city['lab'] ?? 0) >= 1) $out[] = 'lab';
  return array_values(array_unique($out));
}

function city_counts($pdo, $user_id) {
  ensure_tiles($pdo, $user_id);
  $counts = ['house' => 0, 'factory' => 0, 'park' => 0, 'lab' => 0, 'placed_total' => 0];
  $stmt = $pdo->prepare("SELECT building, COUNT(*) c FROM city_tiles WHERE user_id=? GROUP BY building");
  $stmt->execute([$user_id]);
  foreach ($stmt->fetchAll() as $row) {
    $building = $row['building'];
    $count = (int)$row['c'];
    if ($building !== 'empty') $counts['placed_total'] += $count;
    if (isset($counts[$building])) $counts[$building] = $count;
  }
  return $counts;
}

function district_building_counts($pdo, $user_id) {
  ensure_tiles($pdo, $user_id);
  $districts = ['central' => 0, 'industry' => 0, 'green' => 0, 'innovation' => 0];
  $stmt = $pdo->prepare("SELECT x, COUNT(*) c FROM city_tiles WHERE user_id=? AND building <> 'empty' GROUP BY x");
  $stmt->execute([$user_id]);
  foreach ($stmt->fetchAll() as $row) {
    $x = (int)$row['x'];
    $count = (int)$row['c'];
    if ($x <= 2) $districts['central'] += $count;
    elseif ($x <= 4) $districts['industry'] += $count;
    elseif ($x <= 7) $districts['green'] += $count;
    else $districts['innovation'] += $count;
  }
  return $districts;
}

function compute_city_stats($pdo, $user_id) {
  $city = city_counts($pdo, $user_id);
  $houses = $city['house'];
  $factories = $city['factory'];
  $parks = $city['park'];
  $labs = $city['lab'];

  $income = 5 + ($houses * 3) + ($factories * 6) + ($labs * 2) + max(0, intdiv($parks, 2));
  $happiness = 50 + ($parks * 4) - ($factories * 3) + ($houses * 2) + ($labs * 3);
  if ($happiness < 0) $happiness = 0;
  if ($happiness > 100) $happiness = 100;

  $stateStmt = $pdo->prepare("SELECT xp FROM user_state WHERE user_id=?");
  $stateStmt->execute([$user_id]);
  $xp = (int)$stateStmt->fetchColumn();
  $level = max(1, 1 + intdiv($xp, 250));

  $pdo->prepare("UPDATE user_state SET income_per_turn=?, happiness=?, level=? WHERE user_id=?")
      ->execute([$income, $happiness, $level, $user_id]);
}

function get_state_bundle($pdo, $user_id) {
  ensure_starter_house($pdo, $user_id);
  compute_city_stats($pdo, $user_id);
  $stateStmt = $pdo->prepare("SELECT * FROM user_state WHERE user_id=?");
  $stateStmt->execute([$user_id]);
  $state = $stateStmt->fetch();

  $city = city_counts($pdo, $user_id);
  $districtBuildCounts = district_building_counts($pdo, $user_id);
  $districtRules = district_rules($state, $city, $districtBuildCounts);

  $badgesStmt = $pdo->prepare("SELECT b.code,b.name,b.description,ub.earned_at FROM user_badges ub JOIN badges b ON b.id=ub.badge_id WHERE ub.user_id=? ORDER BY ub.earned_at DESC");
  $badgesStmt->execute([$user_id]);

  $activityStmt = $pdo->prepare("SELECT event_type,message,created_at FROM activity_log WHERE user_id=? ORDER BY created_at DESC, id DESC LIMIT 12");
  $activityStmt->execute([$user_id]);

  $leaderboard = $pdo->query("SELECT u.username, us.revenue FROM user_state us JOIN users u ON u.id=us.user_id ORDER BY us.revenue DESC, u.username ASC LIMIT 10")->fetchAll();

  return [
    'state' => $state,
    'city' => $city,
    'districts' => $districtRules,
    'unlocked' => unlocked_buildings($state, $city),
    'badges' => $badgesStmt->fetchAll(),
    'activity' => $activityStmt->fetchAll(),
    'leaderboard' => $leaderboard,
    'topic_progress' => get_topic_progress($pdo, $user_id),
    'analytics' => get_learning_analytics($pdo, $user_id),
  ];
}

function choose_auto_building($state, $city, $newUnlocks) {
  if (in_array('house', $newUnlocks, true)) return 'house';
  if (in_array('lab', $newUnlocks, true)) return 'lab';
  if (in_array('park', $newUnlocks, true)) return 'park';
  if (in_array('factory', $newUnlocks, true)) return 'factory';

  $houses = (int)($city['house'] ?? 0);
  $factories = (int)($city['factory'] ?? 0);
  $parks = (int)($city['park'] ?? 0);
  $labs = (int)($city['lab'] ?? 0);
  $tasks = (int)$state['tasks_correct'];
  $ordering = (int)$state['ordering_correct'];
  $code = (int)$state['code_correct'];

  if ($houses < 3) return 'house';
  if ($tasks >= 2 && $factories < max(1, intdiv($houses + 1, 2))) return 'factory';
  if ($ordering >= 1 && $parks < max(1, intdiv($houses + $factories, 3))) return 'park';
  if ($code >= 1 && $labs < 1) return 'lab';
  return 'house';
}

function find_slot_for_building($pdo, $user_id, $building) {
  $ranges = [
    'house' => [[0,2],[0,9]],
    'factory' => [[3,4],[0,9]],
    'park' => [[5,7],[0,9]],
    'lab' => [[8,9],[0,9]],
  ];
  [$xr, $yr] = $ranges[$building] ?? [[0,9],[0,9]];
  $stmt = $pdo->prepare("SELECT x,y FROM city_tiles WHERE user_id=? AND building='empty' AND x BETWEEN ? AND ? AND y BETWEEN ? AND ? ORDER BY y,x LIMIT 1");
  $stmt->execute([$user_id, $xr[0], $xr[1], $yr[0], $yr[1]]);
  $slot = $stmt->fetch();
  if ($slot) return $slot;

  $fallback = $pdo->prepare("SELECT x,y FROM city_tiles WHERE user_id=? AND building='empty' ORDER BY y,x LIMIT 1");
  $fallback->execute([$user_id]);
  return $fallback->fetch();
}


function ensure_starter_house($pdo, $user_id) {
  ensure_tiles($pdo, $user_id);
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM city_tiles WHERE user_id=? AND building <> 'empty'");
  $stmt->execute([$user_id]);
  if ((int)$stmt->fetchColumn() > 0) return false;

  $slot = find_slot_for_building($pdo, $user_id, 'house');
  if (!$slot) return false;

  $pdo->prepare("UPDATE city_tiles SET building='house', blevel=1 WHERE user_id=? AND x=? AND y=?")
      ->execute([$user_id, (int)$slot['x'], (int)$slot['y']]);

  log_event($pdo, $user_id, 'build', 'Starter house placed in Central District.', [
    'building' => 'house',
    'x' => (int)$slot['x'],
    'y' => (int)$slot['y'],
    'starter' => true
  ]);
  return true;
}

function auto_place_building($pdo, $user_id, $building) {
  ensure_tiles($pdo, $user_id);
  if ($building === 'lab') {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM city_tiles WHERE user_id=? AND building='lab'");
    $stmt->execute([$user_id]);
    if ((int)$stmt->fetchColumn() >= 1) return false;
  }

  $slot = find_slot_for_building($pdo, $user_id, $building);
  if (!$slot) return false;
  $pdo->prepare("UPDATE city_tiles SET building=?, blevel=1 WHERE user_id=? AND x=? AND y=?")
      ->execute([$building, $user_id, (int)$slot['x'], (int)$slot['y']]);
  log_event($pdo, $user_id, 'build', 'Auto-placed '.ucfirst($building).' in the skyline.', ['building' => $building, 'x' => (int)$slot['x'], 'y' => (int)$slot['y']]);
  return true;
}

function evaluate_answer($taskRow, $data, $correctPayload) {
  $taskType = $taskRow['task_type'];
  $answerRaw = $data['answer'] ?? '';
  if ($taskType === 'mcq' || $taskType === 'code_mcq') {
    return trim((string)$answerRaw) === trim((string)($correctPayload['answer'] ?? ''));
  }
  if ($taskType === 'ordering') {
    $ansArr = is_string($answerRaw) ? json_decode($answerRaw, true) : $answerRaw;
    $rightArr = $correctPayload['answer'] ?? [];
    return is_array($ansArr) && is_array($rightArr) && $ansArr === $rightArr;
  }
  if ($taskType === 'match') {
    $ansObj = is_string($answerRaw) ? json_decode($answerRaw, true) : $answerRaw;
    $rightObj = $correctPayload['answer'] ?? [];
    return is_array($ansObj) && is_array($rightObj) && $ansObj === $rightObj;
  }
  return false;
}

if ($action === 'login') {
  $data = json_input();
  $username = trim((string)($data['username'] ?? ''));
  if ($username === '' || strlen($username) > 50) respond(['error' => 'Invalid username'], 400);
  $user_id = ensure_user($pdo, $username);
  ensure_tiles($pdo, $user_id);
  ensure_learning_rows($pdo, $user_id);
  log_event($pdo, $user_id, 'login', 'Logged in as '.$username);
  respond(['ok' => true, 'user_id' => $user_id] + get_state_bundle($pdo, $user_id));
}

if ($action === 'init_map') {
  $user_id = (int)($_GET['user_id'] ?? 0);
  if ($user_id <= 0) respond(['error' => 'Missing user_id'], 400);
  ensure_tiles($pdo, $user_id);
  ensure_learning_rows($pdo, $user_id);
  ensure_learning_rows($pdo, $user_id);
  respond(['ok' => true] + get_state_bundle($pdo, $user_id));
}

if ($action === 'get_state') {
  $user_id = (int)($_GET['user_id'] ?? 0);
  if ($user_id <= 0) respond(['error' => 'Missing user_id'], 400);
  respond(['ok' => true] + get_state_bundle($pdo, $user_id));
}

if ($action === "next_crisis") {

  $user_id = (int)($_GET['user_id'] ?? 0);

  if ($user_id <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "Missing user_id"]);
    exit;
  }

  // Read previous crisis/task so we can avoid repeating the same one
  $stateStmt = $pdo->prepare("
    SELECT last_crisis_id, last_task_id
    FROM user_state
    WHERE user_id=?
  ");
  $stateStmt->execute([$user_id]);
  $state = $stateStmt->fetch(PDO::FETCH_ASSOC);

  $lastCrisisId = (int)($state['last_crisis_id'] ?? 0);
  $lastTaskId   = (int)($state['last_task_id'] ?? 0);

  /* ------------------------------
     Pick a different crisis if possible
  ------------------------------ */
  $crisisStmt = $pdo->prepare("
    SELECT * FROM crises
    WHERE id <> ?
    ORDER BY RAND()
    LIMIT 1
  ");
  $crisisStmt->execute([$lastCrisisId]);
  $crisis = $crisisStmt->fetch(PDO::FETCH_ASSOC);

  if (!$crisis) {
    $crisis = $pdo->query("
      SELECT * FROM crises
      ORDER BY RAND()
      LIMIT 1
    ")->fetch(PDO::FETCH_ASSOC);
  }

  if (!$crisis) {
    http_response_code(500);
    echo json_encode(["error" => "No crises found"]);
    exit;
  }

  /* ------------------------------
     Pick a different task if possible
     NEW SCHEMA: uses task_id, not crisis_id
  ------------------------------ */
  $crisisTopic = strtolower(trim((string)($crisis["ml_topic"] ?? "")));

$crisisTopic = strtolower(trim((string)($crisis["ml_topic"] ?? "")));

$taskStmt = $pdo->prepare("
  SELECT * FROM tasks
  WHERE LOWER(TRIM(ml_topic)) = ?
    AND task_id <> ?
  ORDER BY RAND()
  LIMIT 1
");
$taskStmt->execute([$crisisTopic, $lastTaskId]);
$task = $taskStmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
  $taskStmt = $pdo->prepare("
    SELECT * FROM tasks
    WHERE LOWER(TRIM(ml_topic)) = ?
    ORDER BY RAND()
    LIMIT 1
  ");
  $taskStmt->execute([$crisisTopic]);
  $task = $taskStmt->fetch(PDO::FETCH_ASSOC);
}

if (!$task) {
  $task = $pdo->query("SELECT * FROM tasks ORDER BY RAND() LIMIT 1")->fetch(PDO::FETCH_ASSOC);
}
// final fallback: any task at all
if (!$task) {
  $task = $pdo->query("
    SELECT * FROM tasks
    ORDER BY RAND()
    LIMIT 1
  ")->fetch(PDO::FETCH_ASSOC);
}

  if (!$task) {
    http_response_code(500);
    echo json_encode(["error" => "No tasks found"]);
    exit;
  }

  /* ------------------------------
     Decode TEXT/JSON columns
  ------------------------------ */
  $options = $task["options"] ? json_decode($task["options"], true) : null;
  $correct_answer = $task["correct_answer"] ? json_decode($task["correct_answer"], true) : null;
  $context = $task["context"] ? json_decode($task["context"], true) : null;

  if ($task["options"] && $options === null && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode([
      "error" => "Invalid options JSON",
      "task_id" => (int)$task["task_id"]
    ]);
    exit;
  }

  if ($task["correct_answer"] && $correct_answer === null && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode([
      "error" => "Invalid correct_answer JSON",
      "task_id" => (int)$task["task_id"]
    ]);
    exit;
  }

  if ($task["context"] && $context === null && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode([
      "error" => "Invalid context JSON",
      "task_id" => (int)$task["task_id"]
    ]);
    exit;
  }

  /* ------------------------------
     Save last crisis/task
  ------------------------------ */
  $pdo->prepare("
    UPDATE user_state
    SET last_crisis_id=?, last_task_id=?
    WHERE user_id=?
  ")->execute([
    (int)$crisis["id"],
    (int)$task["task_id"],
    $user_id
  ]);

  ensure_learning_rows($pdo, $user_id);
  $pdo->prepare("UPDATE user_learning_analytics SET challenges_started = challenges_started + 1 WHERE user_id=?")->execute([$user_id]);
  log_event($pdo, $user_id, "narration", "Crisis: " . $crisis["title"]);

  /* ------------------------------
     Build task payload for frontend
  ------------------------------ */
  $payload = [
    "task_id"       => (int)$task["task_id"],
    "prompt"        => $task["prompt"],
    "task_type"     => $task["task_type"],
    "reward_points" => (int)$task["reward_points"],
    "context"       => $context
  ];

  if ($task["task_type"] === "mcq" || $task["task_type"] === "scenario") {
    $payload["options"] = $options ?? [];
  }

  if ($task["task_type"] === "code_mcq") {
    $payload["options"] = $options ?? [];
    $payload["code"] = is_array($context) ? ($context["code"] ?? "") : "";
  }

  if ($task["task_type"] === "ordering") {
    $payload["items"] = $options ?? [];
  }

  if ($task["task_type"] === "match") {
    $payload["left"] = is_array($correct_answer) ? array_keys($correct_answer) : [];
    $payload["right"] = is_array($correct_answer) ? array_values($correct_answer) : [];
  }

  echo json_encode([
    "ok" => true,
    "crisis" => $crisis,
    "task" => $payload
  ]);
  exit;
}

if ($action === "submit_answer") {
  $data = json_input();

  $user_id = (int)($data["user_id"] ?? 0);
  $task_id = (int)($data["task_id"] ?? 0);
  $answer = $data["answer"] ?? null;
  $use_booster = (bool)($data["use_booster"] ?? false);
  $response_seconds = max(0, (int)($data["response_seconds"] ?? 0));
  $lesson_acknowledged = !empty($data["lesson_acknowledged"]);
  $hint_used = !empty($data["hint_used"]);

  if ($user_id <= 0 || $task_id <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "Missing params"]);
    exit;
  }

  ensure_learning_rows($pdo, $user_id);

  $taskStmt = $pdo->prepare("SELECT * FROM tasks WHERE task_id=?");
  $taskStmt->execute([$task_id]);
  $t = $taskStmt->fetch();

  if (!$t) {
    http_response_code(404);
    echo json_encode(["error" => "Task not found"]);
    exit;
  }

  $options = $t["options"] ? json_decode($t["options"], true) : null;
  $correct_answer = $t["correct_answer"] ? json_decode($t["correct_answer"], true) : null;
  $context = $t["context"] ? json_decode($t["context"], true) : null;

  $right = false;

  if ($t["task_type"] === "mcq" || $t["task_type"] === "scenario" || $t["task_type"] === "code_mcq") {
    $right = trim((string)$answer) === trim((string)$correct_answer);
  }

  if ($t["task_type"] === "ordering") {
    $ansArr = is_string($answer) ? json_decode($answer, true) : $answer;
    if (is_array($ansArr) && is_array($correct_answer)) {
      $right = ($ansArr === $correct_answer);
    }
  }

  if ($t["task_type"] === "match") {
    $ansObj = is_string($answer) ? json_decode($answer, true) : $answer;
    if (is_array($ansObj) && is_array($correct_answer)) {
      $right = ($ansObj == $correct_answer);
    }
  }

  $stateStmt = $pdo->prepare("SELECT * FROM user_state WHERE user_id=?");
  $stateStmt->execute([$user_id]);
  $s = $stateStmt->fetch();

  if ($use_booster) {
    if ((int)$s["booster_tokens"] <= 0) {
      echo json_encode(["ok" => false, "error" => "No boosters left"]);
      exit;
    }
    $pdo->prepare("UPDATE user_state SET booster_tokens = booster_tokens - 1 WHERE user_id=?")
        ->execute([$user_id]);
    log_event($pdo, $user_id, "booster", "Used a booster");
  }

  $pdo->prepare("
    UPDATE user_learning_analytics
    SET attempts_total = attempts_total + 1,
        correct_total = correct_total + ?,
        incorrect_total = incorrect_total + ?,
        hints_viewed = hints_viewed + ?,
        boosters_used = boosters_used + ?,
        lessons_acknowledged = lessons_acknowledged + ?,
        total_response_seconds = total_response_seconds + ?,
        last_answered_at = NOW()
    WHERE user_id=?
  ")->execute([
    $right ? 1 : 0,
    $right ? 0 : 1,
    $hint_used ? 1 : 0,
    $use_booster ? 1 : 0,
    $lesson_acknowledged ? 1 : 0,
    $response_seconds,
    $user_id
  ]);

  if ($right) {
    $reward = (int)$t["reward_points"];
    if ($use_booster) {
      $reward += (int)round($reward * 0.25);
    }

    $pdo->prepare("UPDATE user_state SET revenue = revenue + ?, xp = xp + ?, tasks_correct = tasks_correct + 1 WHERE user_id=?")
        ->execute([$reward, $reward, $user_id]);

    if ($t["task_type"] === "ordering") {
      $pdo->prepare("UPDATE user_state SET ordering_correct = ordering_correct + 1 WHERE user_id=?")->execute([$user_id]);
    }
    if ($t["task_type"] === "code_mcq") {
      $pdo->prepare("UPDATE user_state SET code_correct = code_correct + 1 WHERE user_id=?")->execute([$user_id]);
    }

    $topic = is_array($context) ? trim((string)($context["concept"] ?? $t["ml_topic"] ?? "")) : trim((string)($t["ml_topic"] ?? ""));
    record_topic_mastery($pdo, $user_id, $topic);

    log_event($pdo, $user_id, "earn_points", "Earned +$reward revenue for correct answer", [
      "task_id" => $task_id,
      "topic" => $topic,
      "response_seconds" => $response_seconds
    ]);

    echo json_encode([
      "ok" => true,
      "correct" => true,
      "correct_answer" => is_string($correct_answer) ? trim($correct_answer, '"') : $correct_answer,
      "context" => $context
    ] + get_state_bundle($pdo, $user_id));
    exit;
  } else {
    log_event($pdo, $user_id, "mistake", "Incorrect ML decision. Try again.", [
      "task_id" => $task_id,
      "response_seconds" => $response_seconds
    ]);

    echo json_encode([
      "ok" => true,
      "correct" => false,
      "correct_answer" => is_string($correct_answer) ? trim($correct_answer, '"') : $correct_answer,
      "context" => $context
    ] + get_state_bundle($pdo, $user_id));
    exit;
  }
}

if ($action === 'end_turn') {
  $user_id = (int)($_GET['user_id'] ?? 0);
  if ($user_id <= 0) respond(['error' => 'Missing user_id'], 400);
  compute_city_stats($pdo, $user_id);
  $stmt = $pdo->prepare("SELECT income_per_turn FROM user_state WHERE user_id=?");
  $stmt->execute([$user_id]);
  $income = (int)$stmt->fetchColumn();
  $pdo->prepare("UPDATE user_state SET revenue = revenue + ?, xp = xp + ? WHERE user_id=?")
      ->execute([$income, max(1, intdiv($income, 2)), $user_id]);
  log_event($pdo, $user_id, 'turn', 'End turn: +'.$income.' revenue from city income.');
  respond(['ok' => true, 'income' => $income] + get_state_bundle($pdo, $user_id));
}

respond(['error' => 'Unknown action'], 404);
