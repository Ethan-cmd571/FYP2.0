let userId = null;
let currentTaskId = null;
let selectedAnswer = null;

let selectedBuilding = "road";
let turns = 0;

const BUILDINGS = [
  { key: "road", icon: "🛣️", name: "Road", cost: 10, effect: "Connect city zones" },
  { key: "house", icon: "🏠", name: "House", cost: 60, effect: "+income, +happiness" },
  { key: "factory", icon: "🏭", name: "Factory", cost: 120, effect: "+++income, -happiness" },
  { key: "park", icon: "🌳", name: "Park", cost: 80, effect: "+happiness" },
  { key: "lab", icon: "🧠", name: "ML Lab", cost: 200, effect: "Unlock ML learning hub" },
  { key: "empty", icon: "🧹", name: "Bulldoze", cost: 0, effect: "Refund small amount" },
];

function $(id) { return document.getElementById(id); }

async function api(action, method = "GET", body = null) {
  const url = method === "GET"
    ? `api.php?action=${encodeURIComponent(action)}${userId ? `&user_id=${userId}` : ""}`
    : `api.php?action=${encodeURIComponent(action)}`;

  const opts = { method, headers: { "Content-Type": "application/json" } };
  if (body) opts.body = JSON.stringify(body);

  const res = await fetch(url, opts);
  return await res.json();
}

function iconFor(building) {
  switch (building) {
    case "road": return "🛣️";
    case "house": return "🏠";
    case "factory": return "🏭";
    case "park": return "🌳";
    case "lab": return "🧠";
    default: return "·";
  }
}

function renderState(bundle) {
  if (!bundle || !bundle.state) return;

  $("revenue").textContent = bundle.state.revenue;
  $("level").textContent = bundle.state.level;
  $("boosters").textContent = bundle.state.booster_tokens;
  $("income").textContent = bundle.state.income_per_turn ?? 0;
  $("happiness").textContent = bundle.state.happiness ?? 0;
  $("turns").textContent = turns;

  // leaderboard
  $("leaderboard").innerHTML = "";
  bundle.leaderboard.forEach(row => {
    const li = document.createElement("li");
    li.textContent = `${row.username} — ${row.revenue}`;
    $("leaderboard").appendChild(li);
  });

  // activity
  $("activity").innerHTML = "";
  bundle.activity.forEach(ev => {
    const li = document.createElement("li");
    li.innerHTML = `<span class="tag">${ev.event_type}</span> ${ev.message} <span class="muted">(${ev.created_at})</span>`;
    $("activity").appendChild(li);
  });
}

function renderBuildMenu() {
  const menu = $("buildMenu");
  menu.innerHTML = "";
  BUILDINGS.forEach(b => {
    const div = document.createElement("div");
    div.className = "build-item" + (b.key === selectedBuilding ? " active" : "");
    div.onclick = () => {
      selectedBuilding = b.key;
      renderBuildMenu();
      $("buildHint").textContent = `Selected: ${b.name}. Click a tile to place.`;
    };
    div.innerHTML = `
      <div class="build-left">
        <div class="build-icon">${b.icon}</div>
        <div>
          <div><strong>${b.name}</strong> <span class="muted">(${b.cost})</span></div>
          <div class="build-meta">${b.effect}</div>
        </div>
      </div>
      <div class="muted">Select</div>
    `;
    menu.appendChild(div);
  });
}

async function loadMap() {
  const data = await api("get_map");
  if (data.error) return alert(data.error);
  renderMap(data.tiles);
}

function renderMap(tiles) {
  const map = $("map");
  map.innerHTML = "";

  // tiles returned ordered y,x; build a lookup
  const lookup = new Map();
  tiles.forEach(t => lookup.set(`${t.x},${t.y}`, t));

  for (let y = 0; y < 10; y++) {
    for (let x = 0; x < 10; x++) {
      const t = lookup.get(`${x},${y}`) || { x, y, building: "empty" };
      const btn = document.createElement("div");
      btn.className = "tile";
      btn.dataset.x = x;
      btn.dataset.y = y;
      btn.dataset.building = t.building;
      btn.title = `(${x},${y}) ${t.building}`;

      btn.textContent = iconFor(t.building);

      btn.onclick = async () => {
        $("tileInfo").textContent = `Tile (${x},${y}) currently: ${t.building}. Placing: ${selectedBuilding}...`;
        const res = await api("place_building", "POST", { user_id: userId, x, y, building: selectedBuilding });
        if (res.error) {
          $("tileInfo").textContent = `❌ ${res.error}`;
          return;
        }
        if (res.ok === false) {
          $("tileInfo").textContent = `❌ ${res.error || "Could not place."}`;
          return;
        }
        renderState(res);
        await loadMap();
        $("tileInfo").textContent = `✅ Updated tile (${x},${y}).`;
      };

      map.appendChild(btn);
    }
  }
}

function renderTask(crisis, task) {
  $("crisisBox").innerHTML = `
    <h3>${crisis.title}</h3>
    <p>${crisis.story}</p>
    <p class="muted">ML topic: <strong>${crisis.ml_topic}</strong> | Reward: <strong>${task.reward_points}</strong></p>
  `;

  $("taskPrompt").textContent = task.prompt;
  $("taskOptions").innerHTML = "";
  selectedAnswer = null;
  currentTaskId = task.task_id;

  // clear any previous custom UI
  const extra = document.createElement("div");
  extra.id = "taskExtra";
  extra.style.marginTop = "10px";

  // MCQ / CODE-MCQ
  if (task.task_type === "mcq" || task.task_type === "code_mcq") {
    if (task.task_type === "code_mcq") {
      const pre = document.createElement("pre");
      pre.className = "codebox";
      pre.textContent = task.code || "";
      $("taskOptions").appendChild(pre);
    }

    const optWrap = document.createElement("div");
    optWrap.className = "options";
    (task.options || []).forEach(opt => {
      const btn = document.createElement("button");
      btn.className = "opt";
      btn.textContent = opt;
      btn.onclick = () => {
        document.querySelectorAll(".opt").forEach(b => b.classList.remove("selected"));
        btn.classList.add("selected");
        selectedAnswer = opt;
      };
      optWrap.appendChild(btn);
    });
    $("taskOptions").appendChild(optWrap);
  }

  // ORDERING: click to build the order
  if (task.task_type === "ordering") {
    const items = task.items || [];
    const chosen = [];
    const remaining = [...items];

    const remainingBox = document.createElement("div");
    const chosenBox = document.createElement("div");

    remainingBox.innerHTML = `<p class="muted"><strong>Click steps to add them in order:</strong></p>`;
    chosenBox.innerHTML = `<p class="muted"><strong>Your order:</strong></p>`;

    const remList = document.createElement("div");
    remList.className = "pillwrap";

    const chList = document.createElement("div");
    chList.className = "pillwrap";

    function rerender() {
      remList.innerHTML = "";
      remaining.forEach((s, idx) => {
        const pill = document.createElement("button");
        pill.className = "pill";
        pill.textContent = s;
        pill.onclick = () => {
          chosen.push(s);
          remaining.splice(idx, 1);
          rerender();
        };
        remList.appendChild(pill);
      });

      chList.innerHTML = "";
      chosen.forEach((s, idx) => {
        const pill = document.createElement("button");
        pill.className = "pill selected";
        pill.textContent = `${idx + 1}. ${s}`;
        pill.onclick = () => {
          // allow undo
          remaining.push(s);
          chosen.splice(idx, 1);
          rerender();
        };
        chList.appendChild(pill);
      });

      selectedAnswer = JSON.stringify(chosen);
    }

    remainingBox.appendChild(remList);
    chosenBox.appendChild(chList);

    extra.appendChild(remainingBox);
    extra.appendChild(chosenBox);

    const hint = document.createElement("p");
    hint.className = "muted";
    hint.textContent = "Tip: click a step in your order to undo it.";
    extra.appendChild(hint);

    rerender();
    $("taskOptions").appendChild(extra);
  }

  // MATCHING: dropdown for each left item
  if (task.task_type === "match") {
    const left = task.left || [];
    const right = task.right || [];
    const answers = {}; // leftItem -> rightItem

    const tbl = document.createElement("div");
    tbl.className = "matchgrid";

    left.forEach(l => {
      const row = document.createElement("div");
      row.className = "matchrow";

      const ldiv = document.createElement("div");
      ldiv.className = "matchleft";
      ldiv.textContent = l;

      const sel = document.createElement("select");
      const blank = document.createElement("option");
      blank.value = "";
      blank.textContent = "Select match...";
      sel.appendChild(blank);

      right.forEach(r => {
        const opt = document.createElement("option");
        opt.value = r;
        opt.textContent = r;
        sel.appendChild(opt);
      });

      sel.onchange = () => {
        answers[l] = sel.value;
        selectedAnswer = JSON.stringify(answers);
      };

      row.appendChild(ldiv);
      row.appendChild(sel);
      tbl.appendChild(row);
    });

    extra.appendChild(tbl);
    $("taskOptions").appendChild(extra);
  }

  $("feedback").textContent = "";
  $("taskBox").classList.remove("hidden");
  $("useBooster").checked = false;
}

// Login
$("btnLogin").onclick = async () => {
  const username = $("username").value.trim();
  if (!username) return alert("Enter a username.");

  const data = await api("login", "POST", { username });
  if (data.error) return alert(data.error);

  userId = data.user_id;
  $("btnTurn").disabled = false;
  $("btnCrisis").disabled = false;
  $("loginCard").classList.add("hidden");

  // init map tiles on first login
  const init = await api("init_map");
  if (init.error) return alert(init.error);

  renderBuildMenu();
  renderState(init);
  await loadMap();
};

// End turn (income tick)
$("btnTurn").onclick = async () => {
  const res = await api("end_turn");
  if (res.error) return alert(res.error);
  turns += 1;
  renderState(res);
};

// Crisis (ML task) — recommended only if player has ML Lab, but not required
$("btnCrisis").onclick = async () => {
  // Soft rule: encourage lab
  const state = await api("get_state");
  if (state?.state) {
    // continue
  }

  const data = await api("next_crisis", "GET");
  if (data.error) return alert(data.error);
  renderTask(data.crisis, data.task);
};

// Submit ML answer
$("btnSubmit").onclick = async () => {
  if (!currentTaskId) return;
  if (!selectedAnswer) return alert("Select an option.");

  const useBooster = $("useBooster").checked;

  const data = await api("submit_answer", "POST", {
    user_id: userId,
    task_id: currentTaskId,
    answer: selectedAnswer,
    use_booster: useBooster
  });

  if (data.error) return alert(data.error);

  if (data.correct) {
    $("feedback").textContent = `✅ Correct! +${data.reward} revenue.`;
    $("feedback").className = "feedback good";
  } else {
    $("feedback").textContent = `❌ Not quite. Try another crisis or think again.`;
    $("feedback").className = "feedback bad";
  }

  renderState(data);
};