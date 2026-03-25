const $ = id => document.getElementById(id);

let userId = null;
let username = null;
let selectedDifficulty = "easy";
let currentTask = null;
let selectedAnswer = null;
let lessonTimer = null;
let lessonSeconds = 0;
let lessonUnlocked = false;
let taskStartedAt = null;

async function api(action, method = "GET", payload = null) {
  let url = `api.php?action=${encodeURIComponent(action)}`;
  const opts = { method, headers: {} };

  if (method === "GET" && payload) {
    url += `&${new URLSearchParams(payload).toString()}`;
  } else if (method !== "GET") {
    opts.headers["Content-Type"] = "application/json";
    opts.body = JSON.stringify(payload || {});
  }

  const res = await fetch(url, opts);
  const text = await res.text();

  let data;
  try {
    data = JSON.parse(text);
  } catch {
    throw new Error(`Server returned invalid JSON for ${action}: ${text}`);
  }

  if (!res.ok || data.error) {
    throw new Error(data.error || `Request failed for ${action}`);
  }

  return data;
}

function saveSession() {
  localStorage.setItem("mlcb_curriculum_session", JSON.stringify({ userId, username }));
}

function clearSession() {
  localStorage.removeItem("mlcb_curriculum_session");
}

function restoreSession() {
  const raw = localStorage.getItem("mlcb_curriculum_session");
  if (!raw) return;
  try {
    const parsed = JSON.parse(raw);
    userId = parsed.userId || null;
    username = parsed.username || null;
  } catch {}
}

function setDifficulty(diff) {
  selectedDifficulty = diff;
  document.querySelectorAll(".difficulty-btn").forEach(btn => {
    btn.classList.toggle("active", btn.dataset.difficulty === diff);
  });
}

function renderState(data) {
  $("statRevenue").textContent = data.state.revenue;
  $("statHappiness").textContent = data.state.happiness;
  $("statXP").textContent = data.state.xp;
  renderDistricts(data.districts || {});
  renderMastery(data.mastery || []);
  renderCodeMastery(data.code_mastery || null);
  renderAccuracyStats(data.accuracy_stats || {});
  renderLeaderboard(data.leaderboard || []);
  renderActivity(data.activity || []);
  renderBadges(data.badges || []);
}

function renderBadges(rows) {
  const el = $("badgeList");
  if (!el) return;

  if (!rows.length) {
    el.innerHTML = "Complete a full course to earn badges.";
    el.classList.add("empty-state");
    return;
  }

  el.classList.remove("empty-state");
  el.innerHTML = rows.map(row => `
    <div class="badge-card">
      <div class="badge-icon">🏅</div>
      <div>
        <strong>${row.name}</strong>
        <div class="muted small">${row.description}</div>
        <div class="muted small">Earned: ${new Date(row.earned_at).toLocaleString()}</div>
      </div>
    </div>
  `).join("");
}

function renderDistricts(districts) {
  const grid = $("districtGrid");
  if (!grid) return;

  grid.innerHTML = "";
  Object.keys(districts).forEach(key => {
    const d = districts[key];
    const card = document.createElement("div");
    card.className = "district-card";
    const skyline = d.count > 0 ? new Array(Math.min(d.count, 18)).fill(d.building).join(" ") : "—";
    card.innerHTML = `
      <div class="district-head">
        <strong>${d.label}</strong>
        <span>${d.progress}%</span>
      </div>
      <div class="progress"><span style="width:${d.progress}%"></span></div>
      <div class="skyline">${skyline}</div>
      <small>${d.count} building${d.count === 1 ? "" : "s"}</small>
    `;
    grid.appendChild(card);
  });
}

function renderMastery(mastery) {
  const grid = $("masteryGrid");
  if (!grid) return;

  grid.innerHTML = "";
  if (!mastery.length) {
    grid.innerHTML = `<p class="muted">No mastery data yet. Complete some curriculum tasks first.</p>`;
    return;
  }

  mastery.forEach(item => {
    const card = document.createElement("div");
    card.className = `mastery-card mastery-${item.level}`;
    card.innerHTML = `
      <strong>${item.topic}</strong>
      <p>${item.accuracy}% accuracy</p>
      <small>${item.attempts} attempt${item.attempts === 1 ? "" : "s"}</small>
    `;
    grid.appendChild(card);
  });
}

function renderCodeMastery(codeMastery) {
  const attemptsEl = $("codeAttempts");
  const accuracyEl = $("codeAccuracyPct");
  const avgEl = $("codeAvgResponse");
  const levelEl = $("codeMasteryLevel");
  const grid = $("codeMasteryGrid");

  if (!attemptsEl || !accuracyEl || !avgEl || !levelEl || !grid) return;

  const summary = codeMastery?.summary || {
    attempts: 0,
    accuracy_pct: 0,
    avg_response_seconds: 0,
    level: "weak"
  };

  attemptsEl.textContent = summary.attempts ?? 0;
  accuracyEl.textContent = `${summary.accuracy_pct ?? 0}%`;
  avgEl.textContent = `${summary.avg_response_seconds ?? 0}s`;

  const labelMap = {
    strong: "Strong",
    medium: "Developing",
    weak: "Needs practice"
  };
  levelEl.textContent = labelMap[summary.level] || "Needs practice";

  const topics = codeMastery?.topics || [];
  if (!topics.length) {
    grid.innerHTML = `<p class="muted">Complete code-based questions to unlock code mastery tracking.</p>`;
    return;
  }

  grid.innerHTML = "";
  topics.forEach(item => {
    const card = document.createElement("div");
    card.className = `mastery-card mastery-${item.level}`;
    card.innerHTML = `
      <strong>${item.topic}</strong>
      <p>${item.accuracy}% code accuracy</p>
      <small>${item.attempts} attempt${item.attempts === 1 ? "" : "s"} • Avg ${item.avg_response_seconds}s</small>
    `;
    grid.appendChild(card);
  });
}

function renderAccuracyStats(stats) {
  $("statAccuracyPct").textContent = `${stats.accuracy_pct ?? 0}%`;
  $("statAttempts").textContent = `${stats.attempts ?? 0}`;
  $("statCorrect").textContent = `${stats.correct_total ?? 0}`;
  $("statIncorrect").textContent = `${stats.incorrect_total ?? 0}`;
  $("statAvgResponse").textContent = `${stats.avg_response_seconds ?? 0}s`;
}

function renderLeaderboard(rows) {
  const el = $("leaderboardList");
  if (!el) return;

  if (!rows.length) {
    el.innerHTML = "Complete some tasks to populate the leaderboard.";
    el.classList.add("empty-state");
    return;
  }

  el.classList.remove("empty-state");
  el.innerHTML = rows.map((row, idx) => `
    <div class="list-item">
      <div><strong>#${idx + 1} ${row.username}</strong><div class="muted small">XP: ${row.xp}</div></div>
      <div class="pill-value">${row.revenue}</div>
    </div>
  `).join("");
}

function renderActivity(rows) {
  const el = $("activityList");
  if (!el) return;

  if (!rows.length) {
    el.innerHTML = "Your recent activity will appear here.";
    el.classList.add("empty-state");
    return;
  }

  el.classList.remove("empty-state");
  el.innerHTML = rows.map(row => `
    <div class="list-item vertical">
      <strong>${row.message}</strong>
      <div class="muted small">${new Date(row.created_at).toLocaleString()}</div>
    </div>
  `).join("");
}

function resetLessonGate() {
  if (lessonTimer) {
    clearInterval(lessonTimer);
    lessonTimer = null;
  }
  lessonSeconds = 0;
  lessonUnlocked = false;
  selectedAnswer = null;
  $("btnSubmit").disabled = true;
}

function updateLessonCountdown() {
  $("lessonCountdown").textContent = lessonUnlocked ? "Question unlocked" : `Question unlocks in ${lessonSeconds}s`;
}

function syncSubmitState() {
  $("btnSubmit").disabled = !(lessonUnlocked && currentTask && selectedAnswer);
}

function startLessonGate(seconds = 30) {
  resetLessonGate();
  lessonSeconds = seconds;
  $("taskStage").textContent = "Stage 1 of 3 • Read the lesson";
  $("questionPanel").classList.add("challenge-locked");
  $("taskPrompt").textContent = "Read the mini-lesson before answering.";
  updateLessonCountdown();

  lessonTimer = setInterval(() => {
    lessonSeconds -= 1;
    if (lessonSeconds <= 0) {
      clearInterval(lessonTimer);
      lessonTimer = null;
      lessonUnlocked = true;
      $("questionPanel").classList.remove("challenge-locked");
      $("taskPrompt").textContent = currentTask.prompt;
      $("taskStage").textContent = "Stage 2 of 3 • Answer the question";
    }
    updateLessonCountdown();
    syncSubmitState();
  }, 1000);
}

function isCodeQuestion(task) {
  const prompt = (task?.prompt || "").toLowerCase();
  const lesson = JSON.stringify(task?.context || {}).toLowerCase();
  const haystack = `${prompt} ${lesson}`;

  const signals = [
    "import ",
    "from sklearn",
    "train_test_split",
    "fit(",
    "predict(",
    "transform(",
    "df[",
    "iloc[",
    "loc[",
    "x_train",
    "x_test",
    "y_train",
    "y_test",
    "model.",
    ".fit",
    ".predict",
    "drop(",
    "read_csv(",
    "plt.",
    "np.",
    "pd.",
    "axis=1",
    "def ",
    "return "
  ];

  return signals.some(s => haystack.includes(s)) || /[`=\[\]\(\){}._]/.test(task?.prompt || "");
}

function renderTask(task) {
  currentTask = task;
  taskStartedAt = Date.now();

  $("taskBox").classList.remove("hidden");
  $("feedback").className = "feedback hidden";
  $("feedback").textContent = "";
  $("explanationPanel").classList.add("hidden");

  const codeQuestion = isCodeQuestion(task);

  $("lessonConcept").textContent = codeQuestion
    ? `${task.context?.concept || task.ml_topic} • Code Challenge`
    : (task.context?.concept || task.ml_topic);

  $("lessonMeta").innerHTML = `Difficulty: <strong>${task.difficulty}</strong> • Reward: <strong>${task.reward_points}</strong> • Topic: <strong>${task.ml_topic}</strong>`;
  $("lessonBody").textContent = task.context?.lesson || "Read the lesson.";

  const promptEl = $("taskPrompt");
  promptEl.classList.toggle("code-prompt", codeQuestion);

  $("taskOptions").innerHTML = "";

  const optionsWrap = document.createElement("div");
  optionsWrap.className = "options";

  (task.options || []).forEach(opt => {
    const btn = document.createElement("button");
    btn.className = "opt";
    btn.type = "button";
    btn.textContent = opt;
    btn.onclick = () => {
      document.querySelectorAll(".opt").forEach(el => el.classList.remove("selected"));
      btn.classList.add("selected");
      selectedAnswer = opt;
      syncSubmitState();
    };
    optionsWrap.appendChild(btn);
  });

  $("taskOptions").appendChild(optionsWrap);
  startLessonGate(30);
}

function renderExplanation(result) {
  const ctx = result.context || {};
  const good = !!result.correct;
  const rewardLine = good
    ? `<p class="result-line good">+${result.reward || 0} revenue • +${result.happiness_gain || 0} happiness</p>`
    : `<p class="result-line bad">-${result.penalty_revenue || 0} revenue • -${result.penalty_happiness || 0} happiness</p>`;
  const answerLine = !good ? `<p><strong>Correct answer:</strong> ${String(result.correct_answer)}</p>` : "";
  const badgeLine = result.new_badge
    ? `<div class="new-badge-banner">🏅 New badge earned: <strong>${result.new_badge.name}</strong></div>`
    : "";

  $("explanationContent").innerHTML = `
    ${badgeLine}
    <div class="explanation-result ${good ? "good" : "bad"}">
      <strong>${good ? "Correct" : "Not quite"}</strong>
      ${rewardLine}
    </div>
    ${ctx.explanation ? `<p><strong>What it means:</strong> ${ctx.explanation}</p>` : ""}
    ${ctx.why_it_matters ? `<p><strong>Why it matters:</strong> ${ctx.why_it_matters}</p>` : ""}
    ${ctx.city_example ? `<p><strong>City example:</strong> ${ctx.city_example}</p>` : ""}
    ${ctx.common_mistake ? `<p><strong>Common mistake:</strong> ${ctx.common_mistake}</p>` : ""}
    ${answerLine}
  `;

  $("explanationPanel").classList.remove("hidden");
  $("taskStage").textContent = "Stage 3 of 3 • Review the explanation";
}

async function login() {
  const name = $("username").value.trim();
  if (!name) {
    $("statusText").textContent = "Enter a username first.";
    return;
  }

  try {
    const data = await api("login", "POST", { username: name });
    userId = data.user_id;
    username = data.username || name;
    saveSession();

    $("loginCard").classList.add("hidden");
    $("btnLogout").classList.remove("hidden");
    $("btnTask").disabled = false;
    $("statusText").textContent = "";

    renderState(data);
  } catch (err) {
    $("statusText").textContent = err.message;
  }
}

async function loadState() {
  if (!userId) return;
  try {
    const data = await api("get_state", "GET", { user_id: userId });
    renderState(data);
  } catch (err) {
    console.error(err);
  }
}

async function loadTask() {
  if (!userId) return;
  try {
    const data = await api("next_task", "GET", { user_id: userId, difficulty: selectedDifficulty });
    renderTask(data.task);
  } catch (err) {
    console.error(err);
  }
}

async function submitTask() {
  if (!currentTask || !selectedAnswer) return;

  const responseSeconds = Math.max(1, Math.round((Date.now() - taskStartedAt) / 1000));

  try {
    const data = await api("submit_answer", "POST", {
      user_id: userId,
      task_id: currentTask.task_id,
      answer: selectedAnswer,
      response_seconds: responseSeconds
    });

    renderState(data);
    $("feedback").className = `feedback ${data.correct ? "good" : "bad"}`;
    $("feedback").textContent = data.correct
      ? "Answer submitted successfully."
      : "Answer submitted. Review the explanation below.";

    renderExplanation(data);
    if (data.correct) currentTask = null;
    syncSubmitState();
  } catch (err) {
    console.error(err);
    $("feedback").className = "feedback bad";
    $("feedback").textContent = err.message;
    $("feedback").classList.remove("hidden");
  }
}

async function signOut() {
  try {
    await api("sign_out", "POST", { user_id: userId });
  } catch {}

  resetLessonGate();
  currentTask = null;
  selectedAnswer = null;
  taskStartedAt = null;
  userId = null;
  username = null;
  clearSession();

  $("loginCard").classList.remove("hidden");
  $("btnLogout").classList.add("hidden");
  $("btnTask").disabled = true;
  $("taskBox").classList.add("hidden");
  $("username").value = "";
  $("statusText").textContent = "Signed out.";
}

window.addEventListener("DOMContentLoaded", async () => {
  restoreSession();

  document.querySelectorAll(".difficulty-btn").forEach(btn => {
    btn.addEventListener("click", () => setDifficulty(btn.dataset.difficulty));
  });

  $("btnLogin").addEventListener("click", login);
  $("btnTask").addEventListener("click", loadTask);
  $("btnSubmit").addEventListener("click", submitTask);
  $("btnLogout").addEventListener("click", signOut);

  $("username").addEventListener("keydown", e => {
    if (e.key === "Enter") login();
  });

  if (userId) {
    $("loginCard").classList.add("hidden");
    $("btnLogout").classList.remove("hidden");
    $("btnTask").disabled = false;
    await loadState();
  }
});