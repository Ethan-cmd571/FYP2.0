<?php
require_once "config.php";
header("Content-Type: application/json");

$action = $_GET['action'] ?? '';

function json_input() {
  $raw = file_get_contents("php://input");
  return $raw ? json_decode($raw, true) : [];
}

function decode_json_field($value) {
  if (is_array($value)) return $value;
  if (!is_string($value) || trim($value) === '') return null;
  $decoded = json_decode($value, true);
  return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
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
    ensure_tiles($pdo, $user_id);
    return $user_id;
  }
  $user_id = (int)$u['id'];
  $pdo->prepare("INSERT IGNORE INTO user_state(user_id) VALUES(?)")->execute([$user_id]);
  ensure_tiles($pdo, $user_id);
  return $user_id;
}

function award_badge($pdo, $user_id, $code) {
  $b = $pdo->prepare("SELECT id FROM badges WHERE code=?");
  $b->execute([$code]);
  $badge_id = $b->fetchColumn();
  if (!$badge_id) return false;

  $ins = $pdo->prepare("INSERT IGNORE INTO user_badges(user_id, badge_id) VALUES(?,?)");
  $ins->execute([$user_id, (int)$badge_id]);
  return ($ins->rowCount() > 0);
}

function log_event($pdo, $user_id, $type, $message, $meta=null) {
  $stmt = $pdo->prepare("INSERT INTO activity_log(user_id,event_type,message,meta_json) VALUES(?,?,?,?)");
  $stmt->execute([$user_id, $type, $message, $meta ? json_encode($meta) : null]);
}

function ensure_tiles($pdo, $user_id, $w=10, $h=10) {
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

function get_building_meta() {
  return [
    'road' => ['name' => 'Road', 'icon' => '🛣️'],
    'house' => ['name' => 'House', 'icon' => '🏠'],
    'factory' => ['name' => 'Factory', 'icon' => '🏭'],
    'park' => ['name' => 'Park', 'icon' => '🌳'],
    'lab' => ['name' => 'ML Lab', 'icon' => '🧠'],
  ];
}

function get_building_counts($pdo, $user_id) {
  ensure_tiles($pdo, $user_id);
  $rows = $pdo->prepare("SELECT building, COUNT(*) c FROM city_tiles WHERE user_id=? GROUP BY building");
  $rows->execute([$user_id]);

  $counts = ['road'=>0, 'house'=>0, 'factory'=>0, 'park'=>0, 'lab'=>0];
  foreach ($rows->fetchAll() as $r) {
    if ($r['building'] !== 'empty') {
      $counts[$r['building']] = (int)$r['c'];
    }
  }
  return $counts;
}

function compute_city_stats($pdo, $user_id) {
  $counts = get_building_counts($pdo, $user_id);
  $houses = $counts['house'] ?? 0;
  $factories = $counts['factory'] ?? 0;
  $parks = $counts['park'] ?? 0;
  $labs = $counts['lab'] ?? 0;

  $income = 5 + ($houses * 3) + ($factories * 6) + ($labs > 0 ? 2 : 0);
  $happiness = 50 + ($parks * 4) - ($factories * 3) + ($houses * 2) + ($labs > 0 ? 3 : 0);
  $happiness = max(0, min(100, $happiness));

  $upd = $pdo->prepare("UPDATE user_state SET income_per_turn=?, happiness=? WHERE user_id=?");
  $upd->execute([$income, $happiness, $user_id]);

  return ["income_per_turn" => $income, "happiness" => $happiness];
}

function get_state_row($pdo, $user_id) {
  compute_city_stats($pdo, $user_id);
  $state = $pdo->prepare("SELECT * FROM user_state WHERE user_id=?");
  $state->execute([$user_id]);
  return $state->fetch();
}

function get_unlocked_buildings($state) {
  $unlocked = ['road'];
  if ((int)$state['tasks_correct'] >= 1) $unlocked[] = 'house';
  if ((int)$state['ordering_correct'] >= 1 || (int)$state['tasks_correct'] >= 3) $unlocked[] = 'park';
  if ((int)$state['tasks_correct'] >= 2 || (int)$state['revenue'] >= 300) $unlocked[] = 'factory';
  if ((int)$state['code_correct'] >= 1 || (int)$state['tasks_correct'] >= 5) $unlocked[] = 'lab';
  return array_values(array_unique($unlocked));
}

function get_districts($state, $counts) {
  $total = array_sum($counts);
  $tasks = (int)$state['tasks_correct'];
  $code = (int)$state['code_correct'];
  $revenue = (int)$state['revenue'];
  $happiness = (int)$state['happiness'];

  return [
    [
      'key' => 'central',
      'icon' => '🏙️',
      'name' => 'Central District',
      'description' => 'Your starting city core. Bright boulevards, skyline silhouettes, and early neighbourhood growth appear here first.',
      'theme' => 'Sunrise skyline',
      'buildings' => ['road', 'house'],
      'unlocked' => true,
      'requirement' => 'Available from the start.',
      'progress_text' => "{$total} total buildings placed so far."
    ],
    [
      'key' => 'industry',
      'icon' => '🏭',
      'name' => 'Industry District',
      'description' => 'Focused on production, logistics, and high-output expansion with smokestacks and freight energy.',
      'theme' => 'Steelworks dusk',
      'buildings' => ['factory', 'road'],
      'unlocked' => ($tasks >= 2 || $revenue >= 300),
      'requirement' => 'Unlock by getting 2 correct answers or reaching 300 revenue.',
      'progress_text' => "Progress: {$tasks}/2 correct answers or {$revenue}/300 revenue."
    ],
    [
      'key' => 'green',
      'icon' => '🌿',
      'name' => 'Green District',
      'description' => 'Adds wellbeing spaces, tree-lined parks, and calmer neighbourhoods to balance growth and happiness.',
      'theme' => 'Garden canopy',
      'buildings' => ['park', 'house'],
      'unlocked' => ((int)$state['ordering_correct'] >= 1 || $tasks >= 3 || $happiness >= 55),
      'requirement' => 'Unlock by solving a pipeline task, reaching 3 correct answers, or hitting 55 happiness.',
      'progress_text' => "Progress: {$tasks}/3 correct answers, happiness {$happiness}/55."
    ],
    [
      'key' => 'innovation',
      'icon' => '🧠',
      'name' => 'Innovation District',
      'description' => 'Advanced analytics, glowing labs, and futuristic research towers live here.',
      'theme' => 'Neon research hub',
      'buildings' => ['lab', 'park'],
      'unlocked' => ($code >= 1 || $tasks >= 5),
      'requirement' => 'Unlock by answering 1 code question correctly or reaching 5 correct answers.',
      'progress_text' => "Progress: {$code}/1 code answers, {$tasks}/5 total correct answers."
    ]
  ];
}

function next_unlock_text($state) {
  if ((int)$state['tasks_correct'] < 1) return 'Next unlock: House after your first correct answer.';
  if ((int)$state['tasks_correct'] < 2 && (int)$state['revenue'] < 300) return 'Next unlock: Factory after 2 correct answers or 300 revenue.';
  if ((int)$state['ordering_correct'] < 1 && (int)$state['tasks_correct'] < 3 && (int)$state['happiness'] < 55) return 'Next unlock: Park after 1 ordering task, 3 correct answers, or 55 happiness.';
  if ((int)$state['code_correct'] < 1 && (int)$state['tasks_correct'] < 5) return 'Next unlock: ML Lab after 1 code question or 5 correct answers.';
  return 'All core buildings unlocked. Keep answering correctly to keep growing districts.';
}

function get_city_overview($pdo, $user_id, $state = null) {
  $state = $state ?: get_state_row($pdo, $user_id);
  $counts = get_building_counts($pdo, $user_id);
  return [
    'counts' => $counts,
    'total_buildings' => array_sum($counts),
    'tasks_correct' => (int)$state['tasks_correct'],
    'unlocked_buildings' => get_unlocked_buildings($state),
    'districts' => get_districts($state, $counts),
    'next_unlock' => next_unlock_text($state)
  ];
}

function get_state_bundle($pdo, $user_id) {
  $s = get_state_row($pdo, $user_id);

  $badges = $pdo->prepare("SELECT b.code, b.name, b.description, ub.earned_at FROM user_badges ub JOIN badges b ON b.id=ub.badge_id WHERE ub.user_id=? ORDER BY ub.earned_at DESC");
  $badges->execute([$user_id]);

  $activity = $pdo->prepare("SELECT event_type, message, created_at FROM activity_log WHERE user_id=? ORDER BY created_at DESC LIMIT 12");
  $activity->execute([$user_id]);

  $leader = $pdo->query("SELECT u.username, us.revenue FROM user_state us JOIN users u ON u.id=us.user_id ORDER BY us.revenue DESC LIMIT 10")->fetchAll();

  return [
    'state' => $s,
    'badges' => $badges->fetchAll(),
    'activity' => $activity->fetchAll(),
    'leaderboard' => $leader,
    'city_overview' => get_city_overview($pdo, $user_id, $s)
  ];
}

function district_for_building($building) {
  switch ($building) {
    case 'factory': return 'Industry District';
    case 'park': return 'Green District';
    case 'lab': return 'Innovation District';
    default: return 'Central District';
  }
}

function choose_reward_building($task, $state, $counts) {
  $unlocked = get_unlocked_buildings($state);
  $topic = $task['ml_topic'] ?? '';
  $type = $task['task_type'] ?? '';

  if (in_array('lab', $unlocked, true) && ($counts['lab'] ?? 0) === 0 && $type === 'code_mcq') return 'lab';
  if (in_array('park', $unlocked, true) && ($type === 'ordering' || $topic === 'preprocessing' || $topic === 'evaluation')) return 'park';
  if (in_array('factory', $unlocked, true) && ($topic === 'clustering' || $topic === 'regression')) return 'factory';
  if (in_array('house', $unlocked, true) && ($topic === 'classification' || $topic === 'evaluation')) return 'house';
  if (in_array('factory', $unlocked, true) && ($counts['factory'] ?? 0) < ($counts['house'] ?? 0)) return 'factory';
  if (in_array('park', $unlocked, true) && ($counts['park'] ?? 0) < max(1, floor(($counts['house'] ?? 0) / 2))) return 'park';
  if (in_array('house', $unlocked, true)) return 'house';
  return 'road';
}

function auto_place_building($pdo, $user_id, $building) {
  ensure_tiles($pdo, $user_id);
  if ($building === 'lab') {
    $c = $pdo->prepare("SELECT COUNT(*) FROM city_tiles WHERE user_id=? AND building='lab'");
    $c->execute([$user_id]);
    if ((int)$c->fetchColumn() >= 1) return null;
  }

  $slot = $pdo->prepare("SELECT x,y FROM city_tiles WHERE user_id=? AND building='empty' ORDER BY y,x LIMIT 1");
  $slot->execute([$user_id]);
  $empty = $slot->fetch();
  if (!$empty) return null;

  $upd = $pdo->prepare("UPDATE city_tiles SET building=?, blevel=1 WHERE user_id=? AND x=? AND y=?");
  $upd->execute([$building, $user_id, (int)$empty['x'], (int)$empty['y']]);

  return ['x' => (int)$empty['x'], 'y' => (int)$empty['y']];
}

function award_progress_badges($pdo, $user_id, $state, $totalBuildings) {
  $earned = [];
  if ((int)$state['tasks_correct'] >= 1 && award_badge($pdo, $user_id, 'FIRST_WIN')) $earned[] = 'FIRST_WIN';
  if ((int)$state['ordering_correct'] >= 1 && award_badge($pdo, $user_id, 'PIPELINE_PRO')) $earned[] = 'PIPELINE_PRO';
  if ((int)$state['code_correct'] >= 3 && award_badge($pdo, $user_id, 'CODE_APPRENTICE')) $earned[] = 'CODE_APPRENTICE';
  if ((int)$state['match_correct'] >= 3 && award_badge($pdo, $user_id, 'MATCH_MASTER')) $earned[] = 'MATCH_MASTER';
  if ((int)$state['boosters_used'] >= 3 && award_badge($pdo, $user_id, 'HYPERPARAM_HERO')) $earned[] = 'HYPERPARAM_HERO';
  if ((int)$state['revenue'] >= 300 && award_badge($pdo, $user_id, 'DATA_CLEANING_EXPERT')) $earned[] = 'DATA_CLEANING_EXPERT';
  if ((int)$state['revenue'] >= 600 && award_badge($pdo, $user_id, 'EDA_SPECIALIST')) $earned[] = 'EDA_SPECIALIST';
  if ((int)$state['revenue'] >= 1000 && award_badge($pdo, $user_id, 'RICH_CITY')) $earned[] = 'RICH_CITY';
  if ($totalBuildings >= 10 && award_badge($pdo, $user_id, 'CITY_PLANNER')) $earned[] = 'CITY_PLANNER';
  return $earned;
}

if ($action === 'login') {
  $data = json_input();
  $username = trim($data['username'] ?? '');
  if ($username === '' || strlen($username) > 50) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid username']);
    exit;
  }
  $user_id = ensure_user($pdo, $username);
  log_event($pdo, $user_id, 'login', "Logged in as $username");
  echo json_encode(['ok' => true, 'user_id' => $user_id] + get_state_bundle($pdo, $user_id));
  exit;
}

if ($action === 'next_crisis') {
  $user_id = (int)($_GET['user_id'] ?? 0);
  if ($user_id <= 0) { http_response_code(400); echo json_encode(['error' => 'Missing user_id']); exit; }

  $crisis = $pdo->query("SELECT * FROM crises ORDER BY RAND() LIMIT 1")->fetch();
  $taskStmt = $pdo->prepare("SELECT t.*, c.ml_topic FROM tasks t JOIN crises c ON c.id=t.crisis_id WHERE t.crisis_id=? ORDER BY RAND() LIMIT 1");
  $taskStmt->execute([(int)$crisis['id']]);
  $t = $taskStmt->fetch();

  $correct = json_decode($t['correct_json'], true);
  $pdo->prepare("UPDATE user_state SET last_crisis_id=? WHERE user_id=?")->execute([(int)$crisis['id'], $user_id]);
  log_event($pdo, $user_id, 'narration', 'Crisis: ' . $crisis['title']);

  $payload = [
    'task_id' => (int)$t['id'],
    'prompt' => $t['prompt'],
    'task_type' => $t['task_type'],
    'reward_points' => (int)$t['reward_points'],
    'context' => decode_json_field($correct['context'] ?? null),
    'feedback' => decode_json_field($correct['feedback'] ?? null)
  ];
  if ($t['task_type'] === 'mcq') $payload['options'] = $correct['options'] ?? [];
  if ($t['task_type'] === 'code_mcq') { $payload['code'] = $correct['code'] ?? ''; $payload['options'] = $correct['options'] ?? []; }
  if ($t['task_type'] === 'ordering') $payload['items'] = $correct['items'] ?? [];
  if ($t['task_type'] === 'match') { $payload['left'] = $correct['left'] ?? []; $payload['right'] = $correct['right'] ?? []; }

  echo json_encode(['crisis' => $crisis, 'task' => $payload]);
  exit;
}

if ($action === 'submit_answer') {
  $data = json_input();
  $user_id = (int)($data['user_id'] ?? 0);
  $task_id = (int)($data['task_id'] ?? 0);
  $use_booster = (bool)($data['use_booster'] ?? false);

  if ($user_id <= 0 || $task_id <= 0) { http_response_code(400); echo json_encode(['error' => 'Missing params']); exit; }

  $taskStmt = $pdo->prepare("SELECT t.*, c.ml_topic, c.title AS crisis_title FROM tasks t JOIN crises c ON c.id=t.crisis_id WHERE t.id=?");
  $taskStmt->execute([$task_id]);
  $t = $taskStmt->fetch();
  if (!$t) { http_response_code(404); echo json_encode(['error' => 'Task not found']); exit; }

  $correct = json_decode($t['correct_json'], true);
  $right = false;
  $answer = $data['answer'] ?? '';

  if ($t['task_type'] === 'mcq' || $t['task_type'] === 'code_mcq') {
    $right = (trim((string)$answer) === trim((string)($correct['answer'] ?? '')));
  } elseif ($t['task_type'] === 'ordering') {
    $ansArr = is_string($answer) ? json_decode($answer, true) : $answer;
    $rightArr = $correct['answer'] ?? [];
    if (is_array($ansArr) && is_array($rightArr) && count($ansArr) === count($rightArr)) {
      $right = true;
      for ($i = 0; $i < count($rightArr); $i++) {
        if (($ansArr[$i] ?? null) !== $rightArr[$i]) { $right = false; break; }
      }
    }
  } elseif ($t['task_type'] === 'match') {
    $ansObj = is_string($answer) ? json_decode($answer, true) : $answer;
    $rightObj = $correct['answer'] ?? [];
    if (is_array($ansObj) && is_array($rightObj)) {
      $right = true;
      foreach ($rightObj as $k => $v) {
        if (!isset($ansObj[$k]) || $ansObj[$k] !== $v) { $right = false; break; }
      }
    }
  }

  $beforeState = get_state_row($pdo, $user_id);
  $beforeOverview = get_city_overview($pdo, $user_id, $beforeState);
  $beforeBuildings = $beforeOverview['unlocked_buildings'];
  $beforeDistricts = array_map(fn($d) => $d['name'], array_filter($beforeOverview['districts'], fn($d) => $d['unlocked']));

  if ($use_booster) {
    if ((int)$beforeState['booster_tokens'] <= 0) {
      echo json_encode(['ok' => false, 'error' => 'No boosters left']);
      exit;
    }
    $pdo->prepare("UPDATE user_state SET booster_tokens = booster_tokens - 1 WHERE user_id=?")->execute([$user_id]);
    log_event($pdo, $user_id, 'booster', 'Used a booster (Auto-Tune tool)');
  }

  if (!$right) {
    log_event($pdo, $user_id, 'mistake', 'Incorrect ML decision. Try again.');
    echo json_encode(['ok' => true, 'correct' => false] + get_state_bundle($pdo, $user_id));
    exit;
  }

  $reward = (int)$t['reward_points'];
  $booster_bonus = 0;
  if ($use_booster) {
    $booster_bonus = (int)round($reward * 0.25);
    $reward += $booster_bonus;
  }

  $fieldUpdates = ['tasks_correct = tasks_correct + 1'];
  if ($t['task_type'] === 'mcq') $fieldUpdates[] = 'mcq_correct = mcq_correct + 1';
  if ($t['task_type'] === 'ordering') $fieldUpdates[] = 'ordering_correct = ordering_correct + 1';
  if ($t['task_type'] === 'match') $fieldUpdates[] = 'match_correct = match_correct + 1';
  if ($t['task_type'] === 'code_mcq') $fieldUpdates[] = 'code_correct = code_correct + 1';
  if ($use_booster) $fieldUpdates[] = 'boosters_used = boosters_used + 1';
  $fieldUpdates[] = 'revenue = revenue + ' . $reward;
  $fieldUpdates[] = 'xp = xp + ' . $reward;

  $pdo->prepare('UPDATE user_state SET ' . implode(', ', $fieldUpdates) . ' WHERE user_id=?')->execute([$user_id]);

  log_event($pdo, $user_id, 'earn_points', "Earned +$reward revenue for correct ML decision", [
    'task_id' => $task_id,
    'base_points' => (int)$t['reward_points'],
    'booster_bonus' => $booster_bonus
  ]);

  $midState = get_state_row($pdo, $user_id);
  $counts = get_building_counts($pdo, $user_id);
  $building = choose_reward_building($t, $midState, $counts);
  $placed = auto_place_building($pdo, $user_id, $building);
  $autoReward = null;

  if ($placed) {
    compute_city_stats($pdo, $user_id);
    $meta = get_building_meta()[$building] ?? ['name' => ucfirst($building), 'icon' => '🏗️'];
    $district = district_for_building($building);
    log_event($pdo, $user_id, 'build', "Unlocked {$meta['name']} and added it to $district.", [
      'building' => $building,
      'district' => $district,
      'tile' => $placed
    ]);
    $autoReward = [
      'key' => $building,
      'name' => $meta['name'],
      'icon' => $meta['icon'],
      'district' => $district
    ];
  }

  $afterState = get_state_row($pdo, $user_id);
  $afterOverview = get_city_overview($pdo, $user_id, $afterState);
  $afterBuildings = $afterOverview['unlocked_buildings'];
  $afterDistricts = array_map(fn($d) => $d['name'], array_filter($afterOverview['districts'], fn($d) => $d['unlocked']));
  $earned = award_progress_badges($pdo, $user_id, $afterState, $afterOverview['total_buildings']);

  $newBuildings = array_values(array_diff($afterBuildings, $beforeBuildings));
  $meta = get_building_meta();
  $newBuildingsPretty = array_map(fn($k) => $meta[$k]['name'] ?? ucfirst($k), $newBuildings);
  $newDistricts = array_values(array_diff($afterDistricts, $beforeDistricts));

  $rev = (int)$afterState['revenue'];
  $pdo->prepare("INSERT INTO leaderboard_scores(user_id, score) VALUES(?,?)")->execute([$user_id, $rev]);

  echo json_encode([
    'ok' => true,
    'correct' => true,
    'reward' => $reward,
    'earned_badges' => $earned,
    'new_unlocks' => [
      'buildings' => $newBuildingsPretty,
      'districts' => $newDistricts
    ],
    'auto_reward' => $autoReward
  ] + get_state_bundle($pdo, $user_id));
  exit;
}

if ($action === 'get_state') {
  $user_id = (int)($_GET['user_id'] ?? 0);
  if ($user_id <= 0) { http_response_code(400); echo json_encode(['error' => 'Missing user_id']); exit; }
  echo json_encode(['ok' => true] + get_state_bundle($pdo, $user_id));
  exit;
}

if ($action === 'init_map') {
  $user_id = (int)($_GET['user_id'] ?? 0);
  if ($user_id <= 0) { http_response_code(400); echo json_encode(['error' => 'Missing user_id']); exit; }
  ensure_tiles($pdo, $user_id);
  compute_city_stats($pdo, $user_id);
  log_event($pdo, $user_id, 'map', 'City systems initialised. Buildings now unlock automatically from correct answers.');
  echo json_encode(['ok' => true] + get_state_bundle($pdo, $user_id));
  exit;
}

if ($action === 'get_map') {
  $user_id = (int)($_GET['user_id'] ?? 0);
  if ($user_id <= 0) { http_response_code(400); echo json_encode(['error' => 'Missing user_id']); exit; }
  ensure_tiles($pdo, $user_id);
  $tiles = $pdo->prepare("SELECT x,y,building,blevel FROM city_tiles WHERE user_id=? ORDER BY y,x");
  $tiles->execute([$user_id]);
  echo json_encode(['ok' => true, 'tiles' => $tiles->fetchAll(), 'city_overview' => get_city_overview($pdo, $user_id)]);
  exit;
}

if ($action === 'place_building') {
  http_response_code(400);
  echo json_encode(['error' => 'Manual placement has been removed. Buildings now unlock automatically from correct answers.']);
  exit;
}

if ($action === 'end_turn') {
  $user_id = (int)($_GET['user_id'] ?? 0);
  if ($user_id <= 0) { http_response_code(400); echo json_encode(['error' => 'Missing user_id']); exit; }

  compute_city_stats($pdo, $user_id);
  $st = $pdo->prepare("SELECT income_per_turn FROM user_state WHERE user_id=?");
  $st->execute([$user_id]);
  $income = (int)$st->fetchColumn();

  $pdo->prepare("UPDATE user_state SET revenue = revenue + ?, xp = xp + ? WHERE user_id=?")->execute([$income, $income, $user_id]);
  log_event($pdo, $user_id, 'turn', "End turn: +$income revenue from district income.");
  echo json_encode(['ok' => true, 'income' => $income] + get_state_bundle($pdo, $user_id));
  exit;
}

http_response_code(404);
echo json_encode(['error' => 'Unknown action']);
