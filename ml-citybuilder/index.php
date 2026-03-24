<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ML CityBuilder - Curriculum Structure</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="app-shell">
    <header class="topbar">
      <div>
        <h1>ML CityBuilder</h1>
        <p class="sub">Curriculum structure version: foundations → intermediate → advanced</p>
      </div>
      <button id="btnLogout" class="ghost hidden">Sign out</button>
    </header>

    <section id="loginCard" class="panel login-card">
      <h2>Login</h2>
      <p>Enter a username to begin your structured machine-learning curriculum.</p>
      <div class="row">
        <input id="username" type="text" placeholder="Enter username" maxlength="50" />
        <button id="btnLogin">Start</button>
      </div>
      <p id="statusText" class="muted"></p>
    </section>

    <section class="grid two-col">
      <div class="panel">
        <h2>Curriculum Progress</h2>
        <div class="stats">
          <div class="stat-card"><span>Revenue</span><strong id="statRevenue">0</strong></div>
          <div class="stat-card"><span>Happiness</span><strong id="statHappiness">50</strong></div>
          <div class="stat-card"><span>XP</span><strong id="statXP">0</strong></div>
        </div>

        <div class="row difficulty-row">
          <button class="difficulty-btn active" data-difficulty="easy">Foundations</button>
          <button class="difficulty-btn" data-difficulty="medium">Intermediate</button>
          <button class="difficulty-btn" data-difficulty="hard">Advanced</button>
          <button id="btnTask" disabled>New Curriculum Task</button>
        </div>

        <div id="districtGrid" class="district-grid"></div>
      </div>

      <div class="panel">
        <h2>Topic Mastery</h2>
        <p class="muted">See strengths and weaknesses across curriculum topics.</p>
        <div id="masteryGrid" class="mastery-grid"></div>
      </div>
    </section>

    <section class="grid three-col analytics-row">
      <div class="panel">
        <h2>Accuracy Statistics</h2>
        <div class="mini-stats">
          <div class="mini-stat"><span>Overall Accuracy</span><strong id="statAccuracyPct">0%</strong></div>
          <div class="mini-stat"><span>Total Attempts</span><strong id="statAttempts">0</strong></div>
          <div class="mini-stat"><span>Correct</span><strong id="statCorrect">0</strong></div>
          <div class="mini-stat"><span>Incorrect</span><strong id="statIncorrect">0</strong></div>
          <div class="mini-stat"><span>Avg Response</span><strong id="statAvgResponse">0s</strong></div>
        </div>
      </div>

      <div class="panel">
        <h2>Leaderboard</h2>
        <div id="leaderboardList" class="stack-list empty-state">Complete some tasks to populate the leaderboard.</div>
      </div>

      <div class="panel">
        <h2>Activity Stream</h2>
        <div id="activityList" class="stack-list empty-state">Your recent activity will appear here.</div>
      </div>
    </section>

    <section class="panel">
      <div class="section-head">
        <h2>Badges</h2>
        <span class="muted">Earn a badge for completing each course tier</span>
      </div>
      <div id="badgeList" class="badge-grid empty-state">Complete a full course to earn badges.</div>
    </section>

    <section id="taskBox" class="panel hidden">
      <div class="section-head">
        <h2>Lesson → Question → Explanation</h2>
        <span id="taskStage" class="muted">Stage 1 of 3 • Read the lesson</span>
      </div>

      <div class="lesson-panel">
        <div class="section-head compact">
          <h3 id="lessonConcept">Mini-lesson</h3>
          <span id="lessonCountdown" class="muted">Question unlocks in 30s</span>
        </div>
        <p id="lessonMeta" class="muted"></p>
        <div id="lessonBody" class="lesson-copy">Press <strong>New Curriculum Task</strong> to load the next lesson.</div>
      </div>

      <div id="questionPanel" class="question-panel challenge-locked">
        <p id="taskPrompt" class="task-prompt">Read the mini-lesson before answering.</p>
        <div id="taskOptions"></div>
        <div class="row">
          <button id="btnSubmit" disabled>Submit Answer</button>
        </div>
        <p id="feedback" class="feedback hidden"></p>
      </div>

      <div id="explanationPanel" class="explanation-panel hidden">
        <div class="section-head compact">
          <h3>Explanation</h3>
          <span class="muted">Review what the curriculum task was testing</span>
        </div>
        <div id="explanationContent"></div>
      </div>
    </section>
  </div>

  <script src="app.js"></script>
</body>
</html>