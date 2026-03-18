let userId = null;
let currentTaskId = null;
let selectedAnswer = null;
let currentFeedback = null;
let currentTaskContext = null;
let currentTaskStartedAt = null;
let lessonAcknowledged = false;
let hintViewed = false;

const DISTRICT_META = {
  central: { name: "Central District", theme: "Downtown core", className: "central" },
  industry: { name: "Industry District", theme: "Factories and logistics", className: "industry" },
  green: { name: "Green District", theme: "Parks and wellbeing", className: "green" },
  innovation: { name: "Innovation District", theme: "Research and ML hub", className: "innovation" }
};

const BUILDING_META = {
  house: { icon: "🏠", name: "House" },
  factory: { icon: "🏭", name: "Factory" },
  park: { icon: "🌳", name: "Park" },
  lab: { icon: "🧠", name: "ML Lab" }
};

function $(id){ return document.getElementById(id); }
function hasEl(id){ return !!document.getElementById(id); }

function setStoredUser(id, username = "") {
  userId = Number(id) || null;
  if (userId) {
    localStorage.setItem("mlcb_user_id", String(userId));
    if (username) localStorage.setItem("mlcb_username", username);
  } else {
    localStorage.removeItem("mlcb_user_id");
    localStorage.removeItem("mlcb_username");
  }
}

function restoreStoredUser() {
  const savedId = localStorage.getItem("mlcb_user_id");
  const savedUsername = localStorage.getItem("mlcb_username");
  if (savedId && Number(savedId) > 0) userId = Number(savedId);
  if (savedUsername && hasEl("username")) $("username").value = savedUsername;
}

async function api(action, method = "GET", body = null) {
  const url = method === "GET"
    ? `api.php?action=${encodeURIComponent(action)}${userId ? `&user_id=${encodeURIComponent(userId)}` : ""}`
    : `api.php?action=${encodeURIComponent(action)}`;

  const opts = { method, headers: { "Content-Type": "application/json" } };
  if (body) opts.body = JSON.stringify(body);

  const res = await fetch(url, opts);
  const text = await res.text();
  let data;
  try {
    data = text ? JSON.parse(text) : {};
  } catch {
    throw new Error(`Server returned invalid JSON for ${action}`);
  }
  if (!res.ok) throw new Error(data.error || `Request failed (${res.status})`);
  return data;
}

function renderState(bundle) {
  if (!bundle || !bundle.state) return;

  if (hasEl("revenue")) $("revenue").textContent = bundle.state.revenue;
  if (hasEl("income")) $("income").textContent = bundle.state.income_per_turn ?? 0;
  if (hasEl("happiness")) $("happiness").textContent = bundle.state.happiness ?? 0;
  if (hasEl("level")) $("level").textContent = bundle.state.level ?? 1;
  if (hasEl("boosters")) $("boosters").textContent = bundle.state.booster_tokens ?? 0;
  if (hasEl("tasksCorrect")) $("tasksCorrect").textContent = bundle.state.tasks_correct ?? 0;
  if (hasEl("statusText")) {
    $("statusText").textContent = `XP ${bundle.state.xp ?? 0} • ${bundle.city?.placed_total ?? 0} buildings placed`;
  }
  if (hasEl("citySummary")) {
    const c = bundle.city || {};
    $("citySummary").textContent = `Houses ${c.house || 0} • Factories ${c.factory || 0} • Parks ${c.park || 0} • Labs ${c.lab || 0}`;
  }

  renderBadges(bundle.badges || []);
  renderTopicProgress(bundle.topic_progress || []);
  renderAnalytics(bundle.analytics || {});
  renderLeaderboard(bundle.leaderboard || []);
  renderActivity(bundle.activity || []);
  renderUnlockedBuildings(bundle.unlocked || []);
  renderDistricts(bundle.districts || {});
  renderSkyline(bundle.city || {}, bundle.districts || {});
}

function renderBadges(badges) {
  if (!hasEl("badges")) return;
  $("badges").innerHTML = "";
  if (!badges.length) {
    $("badges").innerHTML = '<li class="muted">No badges yet.</li>';
    return;
  }
  badges.forEach(b => {
    const li = document.createElement("li");
    li.innerHTML = `<strong>${b.name}</strong><br><span class="muted">${b.description}</span>`;
    $("badges").appendChild(li);
  });
}


function renderTopicProgress(items) {
  if (!hasEl("mlTopics")) return;
  const wrap = $("mlTopics");
  wrap.innerHTML = "";
  if (!items.length) {
    wrap.innerHTML = '<li class="muted">No concepts mastered yet. Correct answers will appear here.</li>';
    return;
  }
  items.forEach(item => {
    const li = document.createElement("li");
    li.innerHTML = `<strong>${item.topic}</strong><br><span class="muted">Correct answers for this topic: ${item.correct_count}</span>`;
    wrap.appendChild(li);
  });
}

function renderAnalytics(analytics) {
  if (!hasEl("analyticsGrid")) return;
  const grid = $("analyticsGrid");
  const a = analytics || {};
  const cards = [
    ["Challenges started", a.challenges_started ?? 0],
    ["Total attempts", a.attempts_total ?? 0],
    ["Correct answers", a.correct_total ?? 0],
    ["Accuracy", `${a.accuracy_pct ?? 0}%`],
    ["Hints used", a.hints_viewed ?? 0],
    ["Avg response time", `${(a.attempts_total ?? 0) > 0 ? Math.round((a.total_response_seconds ?? 0)/(a.attempts_total ?? 1)) : 0}s`]
  ];
  grid.innerHTML = "";
  cards.forEach(([label, value]) => {
    const card = document.createElement("div");
    card.className = "analytics-card";
    card.innerHTML = `<span>${label}</span><strong>${value}</strong>`;
    grid.appendChild(card);
  });
}

function buildRichFeedback(data, ctx) {
  const correctAnswer = data.correct_answer ? `<div><strong>Correct answer:</strong> ${data.correct_answer}</div>` : "";
  const explanation = ctx?.explanation ? `<div><strong>Explanation:</strong> ${ctx.explanation}</div>` : "";
  const why = ctx?.why_it_matters ? `<div><strong>Why it matters:</strong> ${ctx.why_it_matters}</div>` : "";
  const mistake = ctx?.common_mistake ? `<div><strong>Common mistake:</strong> ${ctx.common_mistake}</div>` : "";
  const example = ctx?.city_example ? `<div><strong>City example:</strong> ${ctx.city_example}</div>` : "";
  return `${correctAnswer}${explanation}${why}${mistake}${example}`;
}

function renderLeaderboard(rows) {
  if (!hasEl("leaderboard")) return;
  $("leaderboard").innerHTML = "";
  rows.forEach(r => {
    const li = document.createElement("li");
    li.textContent = `${r.username} — ${r.revenue}`;
    $("leaderboard").appendChild(li);
  });
}

function renderActivity(items) {
  if (!hasEl("activity")) return;
  $("activity").innerHTML = "";
  items.forEach(ev => {
    const li = document.createElement("li");
    li.innerHTML = `<span class="tag">${ev.event_type}</span>${ev.message} <span class="muted">(${ev.created_at})</span>`;
    $("activity").appendChild(li);
  });
}

function renderUnlockedBuildings(unlocked) {
  if (!hasEl("unlockedBuildings")) return;
  const wrap = $("unlockedBuildings");
  wrap.innerHTML = "";
  const order = ["house", "factory", "park", "lab"];
  order.forEach(key => {
    const info = BUILDING_META[key];
    const isUnlocked = unlocked.includes(key);
    const el = document.createElement("div");
    el.className = `chip${isUnlocked ? "" : " locked"}`;
    el.textContent = `${info.icon} ${info.name}${isUnlocked ? "" : " (locked)"}`;
    wrap.appendChild(el);
  });
}

function renderDistricts(districts) {
  if (!hasEl("districts")) return;
  const wrap = $("districts");
  wrap.innerHTML = "";
  ["central", "industry", "green", "innovation"].forEach(key => {
    const meta = DISTRICT_META[key];
    const d = districts[key] || { unlocked: false, buildings: 0, progress: 0, note: "" };
    const card = document.createElement("div");
    card.className = `district-card ${meta.className}${d.unlocked ? "" : " locked"}`;
    card.innerHTML = `
      <span class="district-tag">${d.unlocked ? "Unlocked" : "Locked"}</span>
      <h3>${meta.name}</h3>
      <p class="muted">${meta.theme}</p>
      <p>${d.note || "Complete more ML tasks to grow this district."}</p>
      <p><strong>${d.buildings || 0}</strong> buildings placed</p>
      <div class="progress-line"><span style="width:${Math.max(4, Math.min(100, d.progress || 0))}%"></span></div>
    `;
    wrap.appendChild(card);
  });
}

function renderSkyline(city, districts) {
  if (!hasEl("skylineBuildings")) return;
  const skyline = $("skylineBuildings");
  skyline.innerHTML = "";

  const total = [];
  const pushMany = (type, count) => {
    for (let i = 0; i < (count || 0); i += 1) total.push(type);
  };
  pushMany("house", city.house);
  pushMany("factory", city.factory);
  pushMany("park", city.park);
  pushMany("lab", city.lab);

  if (!total.length) {
    const empty = document.createElement("div");
    empty.className = "muted";
    empty.style.alignSelf = "center";
    empty.textContent = "Answer a challenge correctly to grow your skyline.";
    skyline.appendChild(empty);
  } else {
    total.forEach((type, idx) => {
      const el = document.createElement("div");
      el.className = `sky-build ${type}`;
      if (type === "house") {
        el.style.height = `${62 + (idx % 3) * 10}px`;
        el.style.width = `${36 + (idx % 2) * 6}px`;
      }
      if (type === "factory") {
        el.style.height = `${92 + (idx % 2) * 18}px`;
        el.style.width = "52px";
      }
      if (type === "lab") {
        el.style.height = `${118 + (idx % 2) * 26}px`;
        el.style.width = "46px";
      }
      if (type === "park") {
        el.style.width = "44px";
        el.style.height = "40px";
        el.innerHTML = '<div class="park-shape"></div>';
      }
      skyline.appendChild(el);
    });
  }

  if (hasEl("districtMarkers")) {
    const markers = $("districtMarkers");
    markers.innerHTML = "";
    ["central", "industry", "green", "innovation"].forEach(key => {
      const meta = DISTRICT_META[key];
      const d = districts[key] || {};
      const chip = document.createElement("div");
      chip.className = "marker";
      chip.textContent = `${meta.name} ${d.unlocked ? `• ${d.buildings || 0}` : "• locked"}`;
      markers.appendChild(chip);
    });
  }
}

function parseMaybeJSON(value) {
  if (typeof value !== "string") return value;
  try { return JSON.parse(value); } catch { return value; }
}

function renderTask(crisis, task) {
  if (hasEl("crisisBox")) {
    $("crisisBox").innerHTML = `
      <h3>${crisis.title}</h3>
      <p>${crisis.story}</p>
      <p class="muted">ML topic: <strong>${crisis.ml_topic}</strong> • Reward: <strong>${task.reward_points}</strong></p>
    `;
  }

  currentTaskId = task.task_id;
  currentTaskStartedAt = Date.now();
  selectedAnswer = null;
  lessonAcknowledged = false;
  hintViewed = false;
  currentFeedback = parseMaybeJSON(task.feedback) || null;
  currentTaskContext = parseMaybeJSON(task.context) || null;
  if (hasEl("taskPrompt")) $("taskPrompt").textContent = task.prompt;
  if (hasEl("taskOptions")) $("taskOptions").innerHTML = "";
  if (hasEl("feedback")) { $("feedback").textContent = ""; $("feedback").className = "feedback"; }
  if (hasEl("taskBox")) $("taskBox").classList.remove("hidden");
  if (hasEl("useBooster")) $("useBooster").checked = false;

  const taskOptions = $("taskOptions");
  const ctx = currentTaskContext;
  const lessonBox = document.createElement("div");
  lessonBox.className = "learnbox";
  lessonBox.innerHTML = `
    <div class="learnbox-head"><strong>Mini-lesson:</strong> ${ctx?.concept || "Machine Learning Concept"}</div>
    ${ctx?.explanation ? `<p><strong>What it is:</strong> ${ctx.explanation}</p>` : "<p>Read the short lesson before answering the question.</p>"}
    ${ctx?.city_example ? `<p><strong>City example:</strong> ${ctx.city_example}</p>` : ""}
    <p class="learnbox-callout">Read the mini-lesson, then unlock the answer area.</p>
    <div class="lesson-ack">
      <button type="button" id="btnAckLesson">I have read the mini-lesson</button>
      ${ctx?.hint ? `<button type="button" id="btnHint" class="opt">Show hint</button>` : ""}
    </div>
    ${ctx?.hint ? `<div id="hintBox" class="inline-note hidden"><strong>Hint:</strong> ${ctx.hint}</div>` : ""}
  `;
  taskOptions.appendChild(lessonBox);

  const challengeArea = document.createElement("div");
  challengeArea.id = "challengeArea";
  challengeArea.className = "challenge-locked";
  taskOptions.appendChild(challengeArea);

  if (task.task_type === "mcq" || task.task_type === "code_mcq" || task.task_type === "scenario") {
    if (task.task_type === "code_mcq") {
      const pre = document.createElement("pre");
      pre.className = "codebox";
      pre.textContent = task.code || "";
      challengeArea.appendChild(pre);
    }
    const wrap = document.createElement("div");
    wrap.className = "options";
    (task.options || []).forEach(opt => {
      const btn = document.createElement("button");
      btn.className = "opt";
      btn.textContent = opt;
      btn.onclick = () => {
        document.querySelectorAll(".opt").forEach(b => b.classList.remove("selected"));
        btn.classList.add("selected");
        selectedAnswer = opt;
      };
      wrap.appendChild(btn);
    });
    challengeArea.appendChild(wrap);
  }

  if (task.task_type === "ordering") {
    const chosen = [];
    const remaining = [...(task.items || [])];
    const extra = document.createElement("div");
    const remBox = document.createElement("div");
    const chBox = document.createElement("div");
    remBox.innerHTML = `<p class="muted"><strong>Click steps to add them in order:</strong></p>`;
    chBox.innerHTML = `<p class="muted"><strong>Your order:</strong></p>`;
    const remList = document.createElement("div");
    const chList = document.createElement("div");
    remList.className = "pillwrap";
    chList.className = "pillwrap";

    function rerender() {
      remList.innerHTML = "";
      remaining.forEach((item, idx) => {
        const pill = document.createElement("button");
        pill.className = "pill";
        pill.textContent = item;
        pill.onclick = () => {
          chosen.push(item);
          remaining.splice(idx, 1);
          rerender();
        };
        remList.appendChild(pill);
      });
      chList.innerHTML = "";
      chosen.forEach((item, idx) => {
        const pill = document.createElement("button");
        pill.className = "pill selected";
        pill.textContent = `${idx + 1}. ${item}`;
        pill.onclick = () => {
          remaining.push(item);
          chosen.splice(idx, 1);
          rerender();
        };
        chList.appendChild(pill);
      });
      selectedAnswer = JSON.stringify(chosen);
    }

    remBox.appendChild(remList);
    chBox.appendChild(chList);
    extra.appendChild(remBox);
    extra.appendChild(chBox);
    challengeArea.appendChild(extra);
    rerender();
  }

  if (task.task_type === "match") {
    const tbl = document.createElement("div");
    tbl.className = "matchgrid";
    const answers = {};
    (task.left || []).forEach(leftItem => {
      const row = document.createElement("div");
      row.className = "matchrow";
      const label = document.createElement("div");
      label.textContent = leftItem;
      const sel = document.createElement("select");
      const blank = document.createElement("option");
      blank.value = "";
      blank.textContent = "Select match...";
      sel.appendChild(blank);
      (task.right || []).forEach(r => {
        const opt = document.createElement("option");
        opt.value = r;
        opt.textContent = r;
        sel.appendChild(opt);
      });
      sel.onchange = () => {
        answers[leftItem] = sel.value;
        selectedAnswer = JSON.stringify(answers);
      };
      row.appendChild(label);
      row.appendChild(sel);
      tbl.appendChild(row);
    });
    challengeArea.appendChild(tbl);
  }

  const ackBtn = $("btnAckLesson");
  if (ackBtn) {
    ackBtn.onclick = () => {
      lessonAcknowledged = true;
      challengeArea.classList.remove("challenge-locked");
      ackBtn.disabled = true;
      ackBtn.textContent = "Mini-lesson completed";
    };
  }
  const hintBtn = $("btnHint");
  if (hintBtn) {
    hintBtn.onclick = () => {
      hintViewed = true;
      const hintBox = $("hintBox");
      if (hintBox) hintBox.classList.remove("hidden");
      hintBtn.disabled = true;
      hintBtn.textContent = "Hint shown";
    };
  }
}

async function restoreSessionUI() {
  if (!userId) return;
  try {
    const state = await api("get_state");
    if (hasEl("loginCard")) $("loginCard").classList.add("hidden");
    if (hasEl("btnTurn")) $("btnTurn").disabled = false;
    if (hasEl("btnCrisis")) $("btnCrisis").disabled = false;
    renderState(state);
  } catch (err) {
    console.error(err);
    setStoredUser(null);
  }
}

if (hasEl("btnLogin")) {
  $("btnLogin").onclick = async () => {
    try {
      const username = $("username").value.trim();
      if (!username) return alert("Enter a username.");
      const data = await api("login", "POST", { username });
      setStoredUser(data.user_id, username);
      $("loginCard").classList.add("hidden");
      $("btnTurn").disabled = false;
      $("btnCrisis").disabled = false;
      const init = await api("init_map");
      renderState(init);
    } catch (err) {
      console.error(err);
      alert("Login failed: " + err.message);
    }
  };
}

if (hasEl("btnTurn")) {
  $("btnTurn").onclick = async () => {
    try {
      if (!userId) return alert("You are not logged in.");
      const data = await api("end_turn");
      renderState(data);
    } catch (err) {
      console.error(err);
      alert("End turn failed: " + err.message);
    }
  };
}

if (hasEl("btnCrisis")) {
  $("btnCrisis").onclick = async () => {
    try {
      if (!userId) return alert("You are not logged in.");
      const data = await api("next_crisis");
      renderTask(data.crisis, data.task);
    } catch (err) {
      console.error(err);
      alert("Could not load challenge: " + err.message);
    }
  };
}

if (hasEl("btnSubmit")) {
  $("btnSubmit").onclick = async () => {
    try {
      if (!userId) return alert("You are not logged in.");
      if (!currentTaskId) return alert("Start a challenge first.");
      if (!selectedAnswer) return alert("Select an answer first.");
      if (!lessonAcknowledged) return alert("Read and acknowledge the mini-lesson first.");
      const responseSeconds = currentTaskStartedAt ? Math.max(1, Math.round((Date.now() - currentTaskStartedAt) / 1000)) : 0;
      const data = await api("submit_answer", "POST", {
        user_id: userId,
        task_id: currentTaskId,
        answer: selectedAnswer,
        use_booster: hasEl("useBooster") ? $("useBooster").checked : false,
        response_seconds: responseSeconds,
        lesson_acknowledged: lessonAcknowledged,
        hint_used: hintViewed
      });
      renderState(data);
      if (hasEl("feedback")) {
        const rich = buildRichFeedback(data, data.context || currentTaskContext || {});
        if (data.correct) {
          const msg = currentFeedback?.correct || "Correct.";
          $("feedback").innerHTML = `✅ <strong>Correct.</strong><br>${msg}<div class="inline-note">${rich}</div>`;
          $("feedback").className = "feedback good";
          currentTaskId = null;
        } else {
          const msg = currentFeedback?.incorrect || "Not quite.";
          $("feedback").innerHTML = `❌ <strong>Not quite.</strong><br>${msg}<div class="inline-note">${rich}</div>`;
          $("feedback").className = "feedback bad";
        }
      }
    } catch (err) {
      console.error(err);
      alert("Submit failed: " + err.message);
    }
  };
}

setStoredUser(null);
if (hasEl("username")) $("username").value = "";
if (hasEl("loginCard")) $("loginCard").classList.remove("hidden");
if (hasEl("btnTurn")) $("btnTurn").disabled = true;
if (hasEl("btnCrisis")) $("btnCrisis").disabled = true;
