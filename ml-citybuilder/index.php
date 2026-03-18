<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ML CityBuilder</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="shell">
    <header class="hero">
      <div>
        <p class="eyebrow">Final Year Project Prototype</p>
        <h1>ML CityBuilder</h1>
        <p class="sub">Solve machine learning crises, unlock districts, and watch your skyline grow automatically.</p>
      </div>
      <div class="hero-badge">No tile placement needed</div>
    </header>

    <section class="panel login-panel" id="loginCard">
      <div>
        <h2>Start game</h2>
        <p class="muted">Prototype login only. Progress is stored in MySQL.</p>
      </div>
      <div class="login-row">
        <input id="username" placeholder="Enter a username" />
        <button id="btnLogin">Start</button>
      </div>
    </section>

    <main class="layout">
      <section class="panel dashboard-panel">
        <div class="section-head">
          <h2>City Dashboard</h2>
          <span class="mini" id="statusText">Log in to begin</span>
        </div>
        <div class="stats-grid">
          <div class="stat-card"><span>Revenue</span><strong id="revenue">0</strong></div>
          <div class="stat-card"><span>Income / turn</span><strong id="income">0</strong></div>
          <div class="stat-card"><span>Happiness</span><strong id="happiness">0</strong></div>
          <div class="stat-card"><span>Level</span><strong id="level">1</strong></div>
          <div class="stat-card"><span>Boosters</span><strong id="boosters">0</strong></div>
          <div class="stat-card"><span>Correct tasks</span><strong id="tasksCorrect">0</strong></div>
        </div>
        <div class="action-row">
          <button id="btnTurn" disabled>End Turn (+income)</button>
          <button id="btnCrisis" disabled>New ML Challenge</button>
        </div>
      </section>

      <section class="panel skyline-panel wide">
        <div class="section-head">
          <h2>City Growth</h2>
          <span class="mini" id="citySummary">Your skyline will expand as you answer correctly.</span>
        </div>
        <div id="skylineScene" class="skyline-scene">
          <div class="skyline-sun"></div>
          <div class="skyline-stars"></div>
          <div class="skyline-ground"></div>
          <div id="districtMarkers" class="district-markers"></div>
          <div id="skylineBuildings" class="skyline-buildings"></div>
        </div>
      </section>

      <section class="panel district-panel wide">
        <div class="section-head">
          <h2>Districts</h2>
          <span class="mini">Each district unlocks with your ML progress</span>
        </div>
        <div id="districts" class="district-grid"></div>
      </section>

      <section class="panel">
        <div class="section-head">
          <h2>Unlocked Buildings</h2>
          <span class="mini">Buildings are auto-placed into districts</span>
        </div>
        <div id="unlockedBuildings" class="chip-list"></div>
      </section>


      <section class="panel">
        <div class="section-head">
          <h2>ML Knowledge Progress</h2>
          <span class="mini">Topics you are mastering</span>
        </div>
        <ul id="mlTopics" class="stack-list"></ul>
      </section>

      <section class="panel">
        <div class="section-head">
          <h2>Learning Analytics</h2>
          <span class="mini">Automatic engagement + performance tracking</span>
        </div>
        <div id="analyticsGrid" class="analytics-grid"></div>
      </section>

      <section class="panel">
        <div class="section-head">
          <h2>Badges</h2>
          <span class="mini">Earned through progress</span>
        </div>
        <ul id="badges" class="stack-list"></ul>
      </section>

      <section class="panel">
        <div class="section-head">
          <h2>Leaderboard</h2>
          <span class="mini">Top city revenues</span>
        </div>
        <ol id="leaderboard" class="stack-list ordered"></ol>
      </section>

      <section class="panel task-panel wide">
        <div class="section-head">
          <h2>Crisis + ML Task</h2>
          <span class="mini">Correct answers unlock buildings and districts</span>
        </div>
        <div id="crisisBox" class="crisis-box">
          <p class="muted">Press <strong>New ML Challenge</strong> to receive a city crisis.</p>
        </div>
        <div id="taskBox" class="task-box hidden">
          <p id="taskPrompt" class="task-prompt"></p>
          <div id="taskOptions"></div>
          <div class="action-row submit-row">
            <label class="chk"><input type="checkbox" id="useBooster" /> Use Booster (+25% if correct)</label>
            <button id="btnSubmit">Submit Answer</button>
          </div>
          <p id="feedback" class="feedback"></p>
        </div>
      </section>

      <section class="panel wide">
        <div class="section-head">
          <h2>Activity Stream</h2>
          <span class="mini">Latest events in your city</span>
        </div>
        <ul id="activity" class="stack-list"></ul>
      </section>
    </main>
  </div>

  <script src="app.js"></script>
</body>
</html>
