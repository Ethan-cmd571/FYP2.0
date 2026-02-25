<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>ML CityBuilder (Tile Map)</title>
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
  <div class="wrap">
    <header>
      <h1>ML CityBuilder</h1>
      <p class="sub">Tile-based city + ML learning crises (XAMPP Prototype)</p>
    </header>

    <section class="card" id="loginCard">
      <h2>Login</h2>
      <div class="row">
        <input id="username" placeholder="Enter a username (e.g., alex)" />
        <button id="btnLogin">Start</button>
      </div>
      <p class="hint">Prototype login (no password). Progress stored in MySQL.</p>
    </section>

    <section class="grid">
      <div class="card">
        <h2>City Dashboard</h2>
        <div class="stats">
          <div><span>Revenue</span><strong id="revenue">0</strong></div>
          <div><span>Income/turn</span><strong id="income">0</strong></div>
          <div><span>Happiness</span><strong id="happiness">0</strong></div>
        </div>

        <div class="stats" style="margin-top:10px;">
          <div><span>Level</span><strong id="level">1</strong></div>
          <div><span>Boosters</span><strong id="boosters">0</strong></div>
          <div><span>Turns</span><strong id="turns">0</strong></div>
        </div>

        <div class="card">
          <h2>Badges</h2>
          <ul id="badges"></ul>
        </div>

        <div class="row" style="margin-top:10px;">
          <button id="btnTurn" disabled>End Turn (+income)</button>
          <button id="btnCrisis" disabled>City Crisis (ML Task)</button>
        </div>

        <p class="muted" style="margin-top:8px;">
          Tip: Build an <strong>ML Lab</strong> to justify ML learning in the city narrative.
        </p>
      </div>

      <div class="card">
        <h2>Build Menu</h2>
        <div id="buildMenu" class="build-menu"></div>
        <p class="muted" id="buildHint">Select a building, then click a tile.</p>
      </div>

      <div class="card">
        <h2>Leaderboard</h2>
        <ol id="leaderboard"></ol>
      </div>

      <div class="card wide">
        <h2>City Map (10×10)</h2>
        <div class="row" style="justify-content:space-between; align-items:flex-start;">
          <div>
            <div id="map" class="map"></div>
            <p class="muted" id="tileInfo">Select a building and click a tile to place it.</p>
          </div>

          <div class="legend">
            <h3 style="margin-top:0;">Legend</h3>
            <div class="legend-item"><span class="icon empty">·</span> Empty</div>
            <div class="legend-item"><span class="icon road">🛣️</span> Road</div>
            <div class="legend-item"><span class="icon house">🏠</span> House</div>
            <div class="legend-item"><span class="icon factory">🏭</span> Factory</div>
            <div class="legend-item"><span class="icon park">🌳</span> Park</div>
            <div class="legend-item"><span class="icon lab">🧠</span> ML Lab</div>
          </div>
        </div>
      </div>

      <div class="card wide">
        <h2>Crisis + ML Task</h2>
        <div id="crisisBox" class="crisis">
          <p class="muted">Build your city, then trigger a crisis to complete an ML learning task.</p>
        </div>

        <div id="taskBox" class="task hidden">
          <p id="taskPrompt"></p>
          <div id="taskOptions" class="options"></div>

          <div class="row">
            <label class="chk">
              <input type="checkbox" id="useBooster" />
              Use Booster (Auto-Tune) +25% reward if correct
            </label>
            <button id="btnSubmit">Submit</button>
          </div>

          <p id="feedback" class="feedback"></p>
        </div>
      </div>

      <div class="card wide">
        <h2>Activity Stream</h2>
        <ul id="activity"></ul>
      </div>
    </section>
  </div>

  <script src="assets/app.js"></script>
</body>
</html>