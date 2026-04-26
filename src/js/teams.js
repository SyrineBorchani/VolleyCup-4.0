class TeamCard {
  constructor(element, index) {
    this.el         = element;
    this.index      = index;
    this.rank       = parseInt(element.querySelector(".rank")?.textContent.replace("#", "")) || index + 1;
    this.name       = element.querySelector(".team-name")?.textContent.trim() || "";
    this.uni        = element.querySelector(".uni-name")?.textContent.trim()  || "";
    this.sets       = parseInt(element.querySelector(".sets-number")?.textContent) || 0;
    this.players    = Array.from(element.querySelectorAll(".key-players p:not(.players-label)")).map(p => p.textContent.trim());
    const rankEl    = element.querySelector(".rank");
    this.badgeClass = rankEl ? (Array.from(rankEl.classList).find(c => c.startsWith("rank-")) || "") : "";
  }

  getBadgeColor() {
    switch (this.badgeClass) {
      case "rank-red":     return "#e05c5c";
      case "rank-blue":    return "#5b8dee";
      case "rank-darkred": return "#a03030";
      case "rank-orange":  return "#e0893a";
      case "rank-gold":    return "#c9a227";
      default:             return "#6378ff";
    }
  }
}

class LiveTicker {
  constructor(teams) {
    this.teams    = teams;
    this.timer    = null;
    this.paused   = false;
    this.position = 0;
    this.speed    = 0.6;
    this.track    = null;
  }

  buildHTML() {
    const items = this.teams.map(tc =>
      `<span class="ticker-item">
        <span class="ticker-rank">#${tc.rank}</span>
        <span class="ticker-name">${tc.name}</span>
        <span class="ticker-uni">${tc.uni}</span>
        <span class="ticker-sep">⟡</span>
      </span>`
    ).join("");

    const wrapper = document.createElement("div");
    wrapper.className = "live-ticker";
    wrapper.innerHTML = `
      <span class="ticker-label">🔴 LIVE</span>
      <div class="ticker-viewport">
        <div class="ticker-track">${items}${items}</div>
      </div>
    `;
    return wrapper;
  }

  mount(container) {
    const tickerEl = this.buildHTML();
    container.insertAdjacentElement("beforebegin", tickerEl);
    this.track = tickerEl.querySelector(".ticker-track");
    tickerEl.addEventListener("mouseenter", () => { this.paused = true;  });
    tickerEl.addEventListener("mouseleave", () => { this.paused = false; });
    this.start();
  }

  start() {
    this.timer = setInterval(() => {
      if (this.paused) return;
      this.position -= this.speed;
      const halfWidth = this.track.scrollWidth / 2;
      if (Math.abs(this.position) >= halfWidth) this.position = 0;
      this.track.style.transform = `translateX(${this.position}px)`;
    }, 16);
  }

  stop() {
    clearInterval(this.timer);
  }
}

class Spotlight {
  constructor(useTeamColor) {
    this.useTeamColor = useTeamColor;
    this.overlay      = null;
    this.activeTimer  = null;
    this._onKey       = this._onKey.bind(this);
  }

  _buildOverlay() {
    const div = document.createElement("div");
    div.className = "spotlight-overlay";
    div.innerHTML = `
      <div class="spotlight-beam"></div>
      <div class="spotlight-card">
        <button class="spotlight-close" title="Close (ESC)">✕</button>
        <div class="spotlight-rank-badge"></div>
        <h2 class="spotlight-team-name"></h2>
        <p class="spotlight-uni"></p>
        <div class="spotlight-arc-wrap">
          <svg class="spotlight-arc-svg" viewBox="0 0 120 120">
            <circle class="arc-bg"   cx="60" cy="60" r="50" />
            <circle class="arc-fill" cx="60" cy="60" r="50" />
          </svg>
          <div class="spotlight-sets-label">
            <span class="spotlight-sets-num">0</span>
            <span class="spotlight-sets-text">Sets Won</span>
          </div>
        </div>
        <div class="spotlight-players-label">Key Players</div>
        <div class="spotlight-players-chips"></div>
      </div>
    `;
    div.querySelector(".spotlight-close").addEventListener("click", () => this.close());
    div.addEventListener("click", e => { if (e.target === div) this.close(); });
    document.body.appendChild(div);
    this.overlay = div;
  }

  open(tc) {
    if (!this.overlay) this._buildOverlay();

    const o      = this.overlay;
    const accent = this.useTeamColor ? tc.getBadgeColor() : "#6378ff";

    o.style.setProperty("--spotlight-accent", accent);
    o.querySelector(".spotlight-rank-badge").textContent = `#${tc.rank}`;
    o.querySelector(".spotlight-team-name").textContent  = tc.name;
    o.querySelector(".spotlight-uni").textContent        = tc.uni;

    const chipsEl = o.querySelector(".spotlight-players-chips");
    chipsEl.innerHTML = "";
    for (let i = 0; i < tc.players.length; i++) {
      const chip = document.createElement("span");
      chip.className   = "spotlight-chip";
      chip.textContent = tc.players[i];
      chipsEl.appendChild(chip);
    }

    const arcFill       = o.querySelector(".arc-fill");
    const numEl         = o.querySelector(".spotlight-sets-num");
    const circumference = 2 * Math.PI * 50;

    arcFill.style.strokeDasharray  = `${circumference}`;
    arcFill.style.strokeDashoffset = `${circumference}`;
    arcFill.style.stroke           = accent;
    numEl.textContent = "0";

    o.classList.add("active");
    document.body.classList.add("spotlight-open");
    document.addEventListener("keydown", this._onKey);

    const maxSets = tc.sets;
    let current   = 0;
    if (this.activeTimer) clearInterval(this.activeTimer);

    this.activeTimer = setInterval(() => {
      current++;
      numEl.textContent = current;
      arcFill.style.strokeDashoffset = `${circumference * (1 - current / (maxSets || 1))}`;
      if (current >= maxSets) {
        clearInterval(this.activeTimer);
        this.activeTimer = null;
      }
    }, 200);
  }

  close() {
    if (!this.overlay) return;
    this.overlay.classList.remove("active");
    document.body.classList.remove("spotlight-open");
    document.removeEventListener("keydown", this._onKey);
    if (this.activeTimer) {
      clearInterval(this.activeTimer);
      this.activeTimer = null;
    }
  }

  _onKey(e) {
    if (e.key === "Escape") this.close();
  }
}

document.addEventListener("DOMContentLoaded", () => {

  const cardEls   = Array.from(document.querySelectorAll(".team-card"));
  const teamCards = [];
  let i = 0;
  while (i < cardEls.length) {
    teamCards.push(new TeamCard(cardEls[i], i));
    i++;
  }

  const container = document.querySelector(".teams-container");
  if (container) {
    const ticker = new LiveTicker(teamCards);
    ticker.mount(container);
    window.addEventListener("visibilitychange", () => {
      if (document.hidden) ticker.stop();
      else ticker.start();
    });
  }

  const spotlightUnified = new Spotlight(false);
  const spotlightColored = new Spotlight(true);
  let useColored = false;

  const toggle = document.getElementById("spotlightToggle");
  if (toggle) {
    toggle.addEventListener("click", () => {
      useColored = !useColored;
      toggle.textContent = useColored ? "🎨 Team Color Mode" : "🎨 Unified Mode";
      toggle.classList.toggle("colored", useColored);
    });
  }

  teamCards.forEach(tc => {
    const btn = document.createElement("button");
    btn.className   = "card-spotlight-btn";
    btn.textContent = "✦ Spotlight";
    btn.addEventListener("click", () => {
      useColored ? spotlightColored.open(tc) : spotlightUnified.open(tc);
    });
    tc.el.style.position = "relative";
    tc.el.appendChild(btn);
  });

  const scrollBtn = document.getElementById("scrollTopBtn");
  if (scrollBtn) {
    window.addEventListener("scroll", () => {
      scrollBtn.style.opacity       = window.scrollY > 300 ? "1" : "0";
      scrollBtn.style.pointerEvents = window.scrollY > 300 ? "auto" : "none";
    });
    scrollBtn.addEventListener("click", () => {
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  }

});