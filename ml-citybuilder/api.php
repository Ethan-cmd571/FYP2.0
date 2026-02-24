<?php
require_once "config.php";
header("Content-Type: application/json");

$action = $_GET['action'] ?? '';

function json_input() {
  $raw = file_get_contents("php://input");
  return $raw ? json_decode($raw, true) : [];
}

function ensure_user($pdo, $username) {
  $stmt = $pdo->prepare("SELECT id FROM users WHERE username=?");
  $stmt->execute([$username]);
  $u = $stmt->fetch();
  if (!$u) {
    $ins = $pdo->prepare("INSERT INTO users(username) VALUES(?)");
    $ins->execute([$username]);
    $user_id = (int)$pdo->lastInsertId();
    $pdo->prepare("INSERT INTO user_state(user_id) VALUES(?)")->execute([$user_id]);
    return $user_id;
  }
  // ensure state exists
  $user_id = (int)$u['id'];
  $pdo->prepare("INSERT IGNORE INTO user_state(user_id) VALUES(?)")->execute([$user_id]);
  return $user_id;
}

function get_state_bundle($pdo, $user_id) {
  $state = $pdo->prepare("SELECT * FROM user_state WHERE user_id=?");
  $state->execute([$user_id]);
  $s = $state->fetch();

  $badges = $pdo->prepare("
    SELECT b.code, b.name, b.description, ub.earned_at
    FROM user_badges ub JOIN badges b ON b.id=ub.badge_id
    WHERE ub.user_id=?
    ORDER BY ub.earned_at DESC
  ");
  $badges->execute([$user_id]);

  $activity = $pdo->prepare("
    SELECT event_type, message, created_at
    FROM activity_log
    WHERE user_id=?
    ORDER BY created_at DESC
    LIMIT 12
  ");
  $activity->execute([$user_id]);

  $leader = $pdo->query("
    SELECT u.username, us.revenue
    FROM user_state us JOIN users u ON u.id=us.user_id
    ORDER BY us.revenue DESC
    LIMIT 10
  ")->fetchAll();

  return [
    "state" => $s,
    "badges" => $badges->fetchAll(),
    "activity" => $activity->fetchAll(),
    "leaderboard" => $leader
  ];
}

function log_event($pdo, $user_id, $type, $message, $meta=null) {
  $stmt = $pdo->prepare("INSERT INTO activity_log(user_id,event_type,message,meta_json) VALUES(?,?,?,?)");
  $stmt->execute([$user_id, $type, $message, $meta ? json_encode($meta) : null]);
}

// --- ROUTES ---
if ($action === "login") {
  $data = json_input();
  $username = trim($data['username'] ?? '');
  if ($username === '' || strlen($username) > 50) {
    http_response_code(400);
    echo json_encode(["error"=>"Invalid username"]);
    exit;
  }
  $user_id = ensure_user($pdo, $username);
  log_event($pdo, $user_id, "login", "Logged in as $username");
  echo json_encode(["ok"=>true, "user_id"=>$user_id] + get_state_bundle($pdo, $user_id));
  exit;
}

if ($action === "next_crisis") {
  $user_id = (int)($_GET['user_id'] ?? 0);
  if ($user_id <= 0) { http_response_code(400); echo json_encode(["error"=>"Missing user_id"]); exit; }

  // pick a random crisis (simple)
  $crisis = $pdo->query("SELECT * FROM crises ORDER BY RAND() LIMIT 1")->fetch();
  $task = $pdo->prepare("SELECT * FROM tasks WHERE crisis_id=? ORDER BY RAND() LIMIT 1");
  $task->execute([(int)$crisis['id']]);
  $t = $task->fetch();

  $correct = json_decode($t['correct_json'], true);

  // store last crisis
  $pdo->prepare("UPDATE user_state SET last_crisis_id=? WHERE user_id=?")->execute([(int)$crisis['id'], $user_id]);

  log_event($pdo, $user_id, "narration", "Crisis: ".$crisis['title']);

$payload = [
  "task_id" => (int)$t['id'],
  "prompt" => $t['prompt'],
  "task_type" => $t['task_type'],
  "reward_points" => (int)$t['reward_points']
];

if ($t['task_type'] === 'mcq') {
  $payload["options"] = $correct['options'] ?? [];
}
if ($t['task_type'] === 'code_mcq') {
  $payload["code"] = $correct['code'] ?? '';
  $payload["options"] = $correct['options'] ?? [];
}
if ($t['task_type'] === 'ordering') {
  $payload["items"] = $correct['items'] ?? [];
}
if ($t['task_type'] === 'match') {
  $payload["left"] = $correct['left'] ?? [];
  $payload["right"] = $correct['right'] ?? [];
}

echo json_encode(["crisis"=>$crisis, "task"=>$payload]);
exit;
}

if ($action === "submit_answer") {
  $data = json_input();
  $user_id = (int)($data['user_id'] ?? 0);
  $task_id = (int)($data['task_id'] ?? 0);
  $answer = trim((string)($data['answer'] ?? ''));
  $use_booster = (bool)($data['use_booster'] ?? false);

  if ($user_id<=0 || $task_id<=0) { http_response_code(400); echo json_encode(["error"=>"Missing params"]); exit; }

  $taskStmt = $pdo->prepare("SELECT * FROM tasks WHERE id=?");
  $taskStmt->execute([$task_id]);
  $t = $taskStmt->fetch();
  if (!$t) { http_response_code(404); echo json_encode(["error"=>"Task not found"]); exit; }

  $correct = json_decode($t['correct_json'], true);
  $right = false;

if ($t['task_type'] === 'mcq' || $t['task_type'] === 'code_mcq') {
  $right = (trim($answer) === trim((string)($correct['answer'] ?? '')));
}

if ($t['task_type'] === 'ordering') {
  // answer comes as JSON array string from frontend
  $ansArr = is_string($data['answer'] ?? null) ? json_decode($data['answer'], true) : ($data['answer'] ?? []);
  $rightArr = $correct['answer'] ?? [];
  if (is_array($ansArr) && is_array($rightArr)) {
    $right = (count($ansArr) === count($rightArr));
    if ($right) {
      for ($i=0; $i<count($rightArr); $i++) {
        if (($ansArr[$i] ?? null) !== $rightArr[$i]) { $right = false; break; }
      }
    }
  }
}

if ($t['task_type'] === 'match') {
  // answer comes as JSON object string: { "Classification":"...", ... }
  $ansObj = is_string($data['answer'] ?? null) ? json_decode($data['answer'], true) : ($data['answer'] ?? []);
  $rightObj = $correct['answer'] ?? [];
  if (is_array($ansObj) && is_array($rightObj)) {
    $right = true;
    foreach ($rightObj as $k => $v) {
      if (!isset($ansObj[$k]) || $ansObj[$k] !== $v) { $right = false; break; }
    }
  }
}

  // Booster: if used, grant a hint effect by allowing one retry-like bonus:
  // Here: booster increases reward by 25% if correct, but consumes a token.
  $stateStmt = $pdo->prepare("SELECT * FROM user_state WHERE user_id=?");
  $stateStmt->execute([$user_id]);
  $s = $stateStmt->fetch();

  $booster_bonus = 0;
  if ($use_booster) {
    if ((int)$s['booster_tokens'] <= 0) {
      echo json_encode(["ok"=>false, "error"=>"No boosters left"]);
      exit;
    }
    $pdo->prepare("UPDATE user_state SET booster_tokens = booster_tokens - 1 WHERE user_id=?")->execute([$user_id]);
    log_event($pdo, $user_id, "booster", "Used a booster (Auto-Tune tool)");
  }

  if ($right) {
    $reward = (int)$t['reward_points'];
    if ($use_booster) {
      $booster_bonus = (int)round($reward * 0.25);
      $reward += $booster_bonus;
    }

    $pdo->prepare("UPDATE user_state SET revenue = revenue + ?, xp = xp + ? WHERE user_id=?")
        ->execute([$reward, $reward, $user_id]);

    log_event($pdo, $user_id, "earn_points", "Earned +$reward revenue for correct ML decision", [
      "task_id"=>$task_id, "base_points"=>(int)$t['reward_points'], "booster_bonus"=>$booster_bonus
    ]);

    // Badge rules (simple prototype thresholds)
    // preprocessing badge: count correct tasks by topic using logs meta_json is heavy; easier: award based on revenue milestones + booster usage.
    // We'll do:
    // - DATA_CLEANING_EXPERT when revenue >= 300
    // - EDA_SPECIALIST when revenue >= 600
    // - HYPERPARAM_HERO when booster used successfully 3 times (tracked via count booster logs + correct submits w/ booster)
    $bundle = get_state_bundle($pdo, $user_id);
    $rev = (int)$bundle['state']['revenue'];

    $earned = [];

    $awardBadge = function($code) use ($pdo, $user_id, &$earned) {
      $b = $pdo->prepare("SELECT id FROM badges WHERE code=?");
      $b->execute([$code]);
      $bid = $b->fetchColumn();
      if (!$bid) return;

      $pdo->prepare("INSERT IGNORE INTO user_badges(user_id,badge_id) VALUES(?,?)")->execute([$user_id, (int)$bid]);
      if ($pdo->lastInsertId() || $pdo->query("SELECT ROW_COUNT()")->fetchColumn() >= 0) {
        $earned[] = $code;
      }
    };

    if ($rev >= 300) $awardBadge("DATA_CLEANING_EXPERT");
    if ($rev >= 600) $awardBadge("EDA_SPECIALIST");

    // hyperparam hero: count booster events
    $cnt = $pdo->prepare("SELECT COUNT(*) FROM activity_log WHERE user_id=? AND event_type='booster'");
    $cnt->execute([$user_id]);
    $boosterUses = (int)$cnt->fetchColumn();
    if ($boosterUses >= 3) $awardBadge("HYPERPARAM_HERO");

    // Leaderboard snapshot (optional)
    $pdo->prepare("INSERT INTO leaderboard_scores(user_id, score) VALUES(?,?)")->execute([$user_id, $rev]);

    echo json_encode(["ok"=>true, "correct"=>true, "reward"=>$reward, "earned_badges"=>$earned] + get_state_bundle($pdo, $user_id));
    exit;
  } else {
    log_event($pdo, $user_id, "mistake", "Incorrect ML decision. Try again.");
    echo json_encode(["ok"=>true, "correct"=>false] + get_state_bundle($pdo, $user_id));
    exit;
  }
}

if ($action === "get_state") {
  $user_id = (int)($_GET['user_id'] ?? 0);
  if ($user_id<=0) { http_response_code(400); echo json_encode(["error"=>"Missing user_id"]); exit; }
  echo json_encode(["ok"=>true] + get_state_bundle($pdo, $user_id));
  exit;
}

if ($action === "init_map") {
  $user_id = (int)($_GET['user_id'] ?? 0);
  if ($user_id<=0) { http_response_code(400); echo json_encode(["error"=>"Missing user_id"]); exit; }

  ensure_tiles($pdo, $user_id);
  compute_city_stats($pdo, $user_id);
  log_event($pdo, $user_id, "map", "City map initialised (10x10).");
  echo json_encode(["ok"=>true] + get_state_bundle($pdo, $user_id));
  exit;
}

if ($action === "get_map") {
  $user_id = (int)($_GET['user_id'] ?? 0);
  if ($user_id<=0) { http_response_code(400); echo json_encode(["error"=>"Missing user_id"]); exit; }

  ensure_tiles($pdo, $user_id);
  $tiles = $pdo->prepare("SELECT x,y,building,blevel FROM city_tiles WHERE user_id=? ORDER BY y,x");
  $tiles->execute([$user_id]);
  echo json_encode(["ok"=>true, "tiles"=>$tiles->fetchAll()]);
  exit;
}

if ($action === "place_building") {
  $data = json_input();
  $user_id = (int)($data['user_id'] ?? 0);
  $x = (int)($data['x'] ?? -1);
  $y = (int)($data['y'] ?? -1);
  $building = trim((string)($data['building'] ?? ''));
  if ($user_id<=0 || $x<0 || $y<0 || $x>9 || $y>9) { http_response_code(400); echo json_encode(["error"=>"Bad params"]); exit; }

  $catalog = [
    "road"   => ["cost"=>10,  "name"=>"Road"],
    "house"  => ["cost"=>60,  "name"=>"House"],
    "factory"=> ["cost"=>120, "name"=>"Factory"],
    "park"   => ["cost"=>80,  "name"=>"Park"],
    "lab"    => ["cost"=>200, "name"=>"ML Lab"],
    "empty"  => ["cost"=>0,   "name"=>"Bulldoze"],
  ];
  if (!isset($catalog[$building])) { http_response_code(400); echo json_encode(["error"=>"Unknown building"]); exit; }

  ensure_tiles($pdo, $user_id);

  // current tile
  $cur = $pdo->prepare("SELECT building FROM city_tiles WHERE user_id=? AND x=? AND y=?");
  $cur->execute([$user_id, $x, $y]);
  $row = $cur->fetch();
  if (!$row) { http_response_code(404); echo json_encode(["error"=>"Tile not found"]); exit; }

  // Prevent placing same building again
  if ($row['building'] === $building) {
    echo json_encode(["ok"=>false, "error"=>"That building is already here."]);
    exit;
  }

  // Revenue check
  $st = $pdo->prepare("SELECT revenue FROM user_state WHERE user_id=?");
  $st->execute([$user_id]);
  $rev = (int)$st->fetchColumn();
  $cost = (int)$catalog[$building]['cost'];

  // Bulldoze gives a small refund (20% of original, rough)
  if ($building === "empty") {
    $refund = 0;
    // simple refund table by old building
    $refundTable = ["road"=>2, "house"=>12, "factory"=>24, "park"=>16, "lab"=>40];
    $refund = $refundTable[$row['building']] ?? 0;

    $pdo->prepare("UPDATE city_tiles SET building='empty', blevel=1 WHERE user_id=? AND x=? AND y=?")
        ->execute([$user_id, $x, $y]);
    $pdo->prepare("UPDATE user_state SET revenue = revenue + ? WHERE user_id=?")->execute([$refund, $user_id]);

    compute_city_stats($pdo, $user_id);
    log_event($pdo, $user_id, "build", "Bulldozed tile ($x,$y) (+$refund refund).");
    echo json_encode(["ok"=>true, "refund"=>$refund] + get_state_bundle($pdo, $user_id));
    exit;
  }

  if ($rev < $cost) {
    echo json_encode(["ok"=>false, "error"=>"Not enough revenue.", "needed"=>$cost, "have"=>$rev]);
    exit;
  }

  // Only allow 1 ML Lab in prototype
  if ($building === "lab") {
    $cnt = $pdo->prepare("SELECT COUNT(*) FROM city_tiles WHERE user_id=? AND building='lab'");
    $cnt->execute([$user_id]);
    if ((int)$cnt->fetchColumn() >= 1) {
      echo json_encode(["ok"=>false, "error"=>"Only one ML Lab allowed in this prototype."]);
      exit;
    }
  }

  $pdo->beginTransaction();
  $pdo->prepare("UPDATE user_state SET revenue = revenue - ? WHERE user_id=?")->execute([$cost, $user_id]);
  $pdo->prepare("UPDATE city_tiles SET building=?, blevel=1 WHERE user_id=? AND x=? AND y=?")
      ->execute([$building, $user_id, $x, $y]);
  $pdo->commit();

  compute_city_stats($pdo, $user_id);
  log_event($pdo, $user_id, "build", "Placed ".$catalog[$building]['name']." at ($x,$y) (-$cost).");

  echo json_encode(["ok"=>true] + get_state_bundle($pdo, $user_id));
  exit;
}

if ($action === "end_turn") {
  $user_id = (int)($_GET['user_id'] ?? 0);
  if ($user_id<=0) { http_response_code(400); echo json_encode(["error"=>"Missing user_id"]); exit; }

  compute_city_stats($pdo, $user_id);
  $st = $pdo->prepare("SELECT income_per_turn FROM user_state WHERE user_id=?");
  $st->execute([$user_id]);
  $income = (int)$st->fetchColumn();

  $pdo->prepare("UPDATE user_state SET revenue = revenue + ?, xp = xp + ? WHERE user_id=?")
      ->execute([$income, $income, $user_id]);

  log_event($pdo, $user_id, "turn", "End turn: +$income revenue from city income.");
  echo json_encode(["ok"=>true, "income"=>$income] + get_state_bundle($pdo, $user_id));
  exit;
}

function ensure_tiles($pdo, $user_id, $w=10, $h=10) {
  // Create missing tiles lazily
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM city_tiles WHERE user_id=?");
  $stmt->execute([$user_id]);
  $count = (int)$stmt->fetchColumn();
  if ($count >= $w*$h) return;

  $pdo->beginTransaction();
  $ins = $pdo->prepare("INSERT IGNORE INTO city_tiles(user_id,x,y,building,blevel) VALUES(?,?,?,?,1)");
  for ($y=0; $y<$h; $y++) {
    for ($x=0; $x<$w; $x++) {
      $ins->execute([$user_id, $x, $y, 'empty']);
    }
  }
  $pdo->commit();
}

function compute_city_stats($pdo, $user_id) {
  // Simple, explainable city simulation
  // Income:
  // - base 5
  // - +3 per house
  // - +6 per factory
  // - +2 if has ML lab (education/efficiency)
  // Happiness:
  // - base 50
  // - +4 per park
  // - -3 per factory
  // - +2 per house (community)
  // - +3 if has ML lab (public services)

  $rows = $pdo->prepare("SELECT building, COUNT(*) c FROM city_tiles WHERE user_id=? GROUP BY building");
  $rows->execute([$user_id]);
  $counts = [];
  foreach ($rows->fetchAll() as $r) $counts[$r['building']] = (int)$r['c'];

  $houses = $counts['house'] ?? 0;
  $factories = $counts['factory'] ?? 0;
  $parks = $counts['park'] ?? 0;
  $labs = $counts['lab'] ?? 0;

  $income = 5 + ($houses * 3) + ($factories * 6) + ($labs > 0 ? 2 : 0);
  $happiness = 50 + ($parks * 4) - ($factories * 3) + ($houses * 2) + ($labs > 0 ? 3 : 0);

  if ($happiness < 0) $happiness = 0;
  if ($happiness > 100) $happiness = 100;

  $upd = $pdo->prepare("UPDATE user_state SET income_per_turn=?, happiness=? WHERE user_id=?");
  $upd->execute([$income, $happiness, $user_id]);

  return ["income_per_turn"=>$income, "happiness"=>$happiness];
}

http_response_code(404);
echo json_encode(["error"=>"Unknown action"]);