// ============================================================
// VoiceTracker.js — Standalone Voice Navigation
// Voice navigation for users who cannot type or use a mouse.
// Works in any web project — no framework dependencies.
//
// Usage:
//   <script type="module" src="VoiceTracker.js"></script>
//   window.VoiceTracking.start();   // enable voice navigation
//   window.VoiceTracking.stop();    // disable
//
// Events fired on document:
//   vtStatus  — { detail: { status: 'listening'|'dictating'|'processing'|'inactive'|... } }
//
// SPA frameworks supported: Livewire, Turbo/Hotwire, React Router, Vue Router,
//   or any framework using the History API (pushState / replaceState).
// ============================================================

// ============================================================
// Section 1: CONFIG
// ============================================================
const CONFIG = Object.freeze({
  HIGH_CONF:       0.68,
  MED_CONF:        0.40,
  DUP_BAND:        0.03,
  SCROLL_AMOUNT:   400,
  OVERLAY_TIMEOUT: 25000,
  NO_SPEECH_RETRY: 1500,
  SCAN_DEBOUNCE:   300,        // debounce ms for MutationObserver (#14, #31)
  USE_CDN: 'https://esm.sh/@tensorflow-models/universal-sentence-encoder',
  AUDIO_CUES:      true,
  SESSION_KEY: 'vt_model_ready',   // sessionStorage flag — suppress repeat load messages
});

// ============================================================
// Section 2: State
// ============================================================
const state = {
  active:            false,
  mode:              'navigate',
  model:             null,
  modelReady:        false,
  modelLoading:      false,
  modelType:         'none',       // 'use-lite' | 'trigram' | 'none'
  forceTrigram:      false,
  elementVectors:    [],
  scanDirty:         true,         // true = rescan needed
  _scanning:         false,        // concurrent scanPage() guard (#4)
  _scanDebounceId:   null,         // MutationObserver debounce timer (#14, #31)
  _starting:         false,        // concurrent start() guard (#45)
  _vtFocusing:       false,        // true while VT programmatically focuses an input (#8)
  dictationField:    null,
  dictationPrev:     '',
  dictationOrigPlaceholder: '',    // save original placeholder before dictation (#7, #42)
  selectOptions:     [],
  overlayMatches:      [],
  overlayPage:         0,
  overlayTimeoutId:    null,
  overlayJustHandled:  false,      // prevents double-processing when interim already handled choice
  lastAction:          null,
  recognition:         null,
  recognitionActive:   false,
  interimText:         '',
};

function _emitStatus(status) {
  document.dispatchEvent(new CustomEvent('vtStatus', { detail: { status } }));
}

const AudioCues = {
  _ctx: null,

  _enabled() {
    if (!CONFIG.AUDIO_CUES) return false;
    try { return localStorage.getItem('vt_audio_cues') !== 'false'; } catch { return true; }
  },

  _get() {
    const Ctx = window.AudioContext || window.webkitAudioContext;
    if (!Ctx) return null;
    if (!this._ctx) this._ctx = new Ctx();
    return this._ctx;
  },

  beep(freq = 880, ms = 80, vol = 0.12) {
    if (!this._enabled()) return;
    try {
      const ctx = this._get();
      if (!ctx) return;
      if (ctx.state === 'suspended') ctx.resume().catch(() => {});
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.frequency.value = freq;
      gain.gain.value = vol;
      osc.start();
      osc.stop(ctx.currentTime + (ms / 1000));
    } catch {}
  },

  match()   { this.beep(880, 80);  },
  dictate() { this.beep(440, 60);  },
  error()   { this.beep(200, 150); },
  done()    { this.beep(660, 60);  },
};

// ============================================================
// Section 3: Utils
// ============================================================
const utils = {
  _timers: new Set(),

  setTimeout(fn, ms) {
    const id = window.setTimeout(() => { this._timers.delete(id); fn(); }, ms);
    this._timers.add(id);
    return id;
  },

  clearTimeout(id) {
    if (id != null) { window.clearTimeout(id); this._timers.delete(id); }
    return null;
  },

  clearAllTimers() {
    this._timers.forEach(id => window.clearTimeout(id));
    this._timers.clear();
  },

  sanitize(text) {
    // Chrome SpeechRecognition capitalises first word and appends punctuation like "Scroll down."
    return String(text || '')
      .toLowerCase()
      .trim()
      .replace(/[.!?,;:]+$/, '')
      .trim();
  },

  cosineSim(a, b) {
    let dot = 0, ma = 0, mb = 0;
    for (let i = 0; i < a.length; i++) {
      dot += a[i] * b[i]; ma += a[i] * a[i]; mb += b[i] * b[i];
    }
    const denom = Math.sqrt(ma) * Math.sqrt(mb);
    return denom === 0 ? 0 : dot / denom;
  },

  _trigrams(str) {
    const s = ' ' + String(str).toLowerCase().replace(/\s+/g, ' ').trim() + ' ';
    const map = new Map();
    for (let i = 0; i < s.length - 2; i++) {
      const t = s.slice(i, i + 3);
      map.set(t, (map.get(t) || 0) + 1);
    }
    return map;
  },

  trigramSim(a, b) {
    const ta = this._trigrams(a); const tb = this._trigrams(b);
    let dot = 0, ma = 0, mb = 0;
    ta.forEach((v, k) => { ma += v * v; if (tb.has(k)) dot += v * tb.get(k); });
    tb.forEach(v => mb += v * v);
    const denom = Math.sqrt(ma) * Math.sqrt(mb);
    return denom === 0 ? 0 : dot / denom;
  },

  // Fast variant: element trigram Map + norm pre-computed at scan time.
  trigramSimFast(queryMap, queryNorm, elemMap, elemNorm) {
    const denom = queryNorm * elemNorm;
    if (denom === 0) return 0;
    let dot = 0;
    queryMap.forEach((v, k) => { if (elemMap.has(k)) dot += v * elemMap.get(k); });
    return dot / denom;
  },
};

// ============================================================
// Section 4: UIManager (notifications, highlights, overlays)
// ============================================================
const UIManager = {
  notifEl: null, _notifTimer: null,
  _highlightEl: null, _dictatingEl: null, _overlayBadges: [],
  _elListTimerId: null,

  init() {
    if (document.getElementById('vt-notif')) {
      this.notifEl = document.getElementById('vt-notif'); return;
    }
    const el = document.createElement('div');
    el.id = 'vt-notif';
    el.setAttribute('aria-live', 'assertive');
    el.setAttribute('role', 'status');
    el.setAttribute('aria-atomic', 'true');
    Object.assign(el.style, {
      position: 'fixed', bottom: '90px', left: '50%', transform: 'translateX(-50%)',
      background: 'rgba(17,24,39,0.95)', color: '#fff', padding: '10px 22px',
      borderRadius: '10px', fontSize: '15px', zIndex: '999999', display: 'none',
      maxWidth: '420px', textAlign: 'center', boxShadow: '0 4px 20px rgba(0,0,0,0.4)',
      fontFamily: 'system-ui,sans-serif', lineHeight: '1.4', pointerEvents: 'none',
    });
    document.body.appendChild(el);
    this.notifEl = el;

    if (!document.getElementById('vt-styles')) {
      const style = document.createElement('style');
      style.id = 'vt-styles';
      style.textContent = `
        .vt-highlight { outline: 3px solid #f59e0b !important; outline-offset: 3px !important; }
        .vt-dictating {
          outline: 3px solid #7c3aed !important; outline-offset: 3px !important;
          animation: vt-pulse 1.2s ease-in-out infinite;
        }
        @keyframes vt-pulse { 0%,100% { outline-color: #7c3aed; } 50% { outline-color: #c4b5fd; } }
        @media (prefers-reduced-motion: reduce) { .vt-dictating { animation: none; } }
        .vt-overlay-badge {
          position: fixed; background: #1d4ed8; color: #fff; border-radius: 5px;
          font-size: 13px; font-weight: 700; padding: 3px 8px; z-index: 999998;
          pointer-events: none; font-family: system-ui,sans-serif;
          box-shadow: 0 2px 10px rgba(0,0,0,0.35);
          animation: vt-badge-pulse 1s ease-in-out infinite;
        }
        @keyframes vt-badge-pulse { 0%,100% { opacity:1; transform:scale(1); } 50% { opacity:0.75; transform:scale(0.97); } }
        @media (prefers-reduced-motion: reduce) { .vt-overlay-badge { animation: none; } }
      `;
      document.head.appendChild(style);
    }
  },

  notify(msg, duration = 3500) {
    if (!this.notifEl) this.init();
    clearTimeout(this._notifTimer);
    this.notifEl.textContent = msg;
    this.notifEl.style.display = 'block';
    if (duration > 0) {
      this._notifTimer = setTimeout(() => { if (this.notifEl) this.notifEl.style.display = 'none'; }, duration);
    }
  },
  notifyPersist(msg) { this.notify(msg, 0); },
  hide() { clearTimeout(this._notifTimer); if (this.notifEl) this.notifEl.style.display = 'none'; },

  highlight(el) {
    if (this._highlightEl) this._highlightEl.classList.remove('vt-highlight');
    this._highlightEl = el || null;
    if (el) el.classList.add('vt-highlight');
  },
  clearHighlights() {
    if (this._highlightEl) { this._highlightEl.classList.remove('vt-highlight'); this._highlightEl = null; }
  },

  showOverlays(matches) {
    this.clearOverlays();
    const LABELS = ['1A', '2B', '3C', '4D'];
    matches.forEach((m, i) => {
      const rect = m.el.getBoundingClientRect();
      if (rect.width < 2) return;
      const badge = document.createElement('div');
      badge.className = 'vt-overlay-badge';
      badge.textContent = LABELS[i] || String(i + 1);
      // Clamp badge within viewport so it's always visible (#15)
      const top  = Math.min(Math.max(4, rect.top  - 14), window.innerHeight - 30);
      const left = Math.min(Math.max(4, rect.left - 12), window.innerWidth  - 40);
      badge.style.top  = `${top}px`;
      badge.style.left = `${left}px`;
      document.body.appendChild(badge);
      this._overlayBadges.push(badge);
    });
  },
  clearOverlays() { this._overlayBadges.forEach(b => b.remove()); this._overlayBadges = []; },

  showElementList(elements, headerText = null) {
    this.closeElementList();
    const panel = document.createElement('div');
    panel.id = 'vt-element-list';
    Object.assign(panel.style, {
      position: 'fixed', top: '50%', left: '50%', transform: 'translate(-50%,-50%)',
      background: 'rgba(10,10,20,0.97)', color: '#e2e8f0',
      borderRadius: '14px', border: '1px solid rgba(255,255,255,0.12)',
      boxShadow: '0 8px 40px rgba(0,0,0,0.7)', zIndex: '999997',
      width: 'min(460px, 92vw)', maxHeight: '65vh',
      display: 'flex', flexDirection: 'column',
      fontFamily: 'system-ui,sans-serif', overflow: 'hidden',
    });

    const header = document.createElement('div');
    header.style.cssText = 'padding:11px 14px;border-bottom:1px solid rgba(255,255,255,0.08);display:flex;justify-content:space-between;align-items:center;flex-shrink:0';
    const title = headerText || `${elements.length} elements - say a name to click`;
    header.innerHTML = `
      <span style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em">
        ${title}
      </span>
      <button id="vt-el-close" style="background:none;border:none;color:#64748b;cursor:pointer;font-size:16px;padding:0 2px;line-height:1" aria-label="Close">✕</button>
    `;

    const list = document.createElement('div');
    list.style.cssText = 'overflow-y:auto;padding:6px;flex:1';

    elements.forEach((ev, i) => {
      const row = document.createElement('div');
      row.style.cssText = 'display:flex;align-items:center;gap:8px;padding:7px 10px;border-radius:7px;cursor:default';
      row.innerHTML = `
        <span style="font-size:10px;color:#475569;width:22px;text-align:right;flex-shrink:0">${i + 1}</span>
        <span style="font-size:13px;color:#e2e8f0;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${ev.label}</span>
        ${ev.isInput ? '<span style="font-size:10px;background:rgba(168,85,247,0.2);color:#a855f7;padding:1px 7px;border-radius:3px;flex-shrink:0">input</span>' : ''}
      `;
      row.addEventListener('mouseenter', () => row.style.background = 'rgba(255,255,255,0.05)');
      row.addEventListener('mouseleave', () => row.style.background = '');
      list.appendChild(row);
    });

    panel.appendChild(header);
    panel.appendChild(list);
    document.body.appendChild(panel);
    document.getElementById('vt-el-close')?.addEventListener('click', () => this.closeElementList());
    this._elListTimerId = utils.setTimeout(() => this.closeElementList(), 30000);  // (#10, #51)
  },

  closeElementList() {
    this._elListTimerId = utils.clearTimeout(this._elListTimerId);  // (#10)
    document.getElementById('vt-element-list')?.remove();
  },

  setDictatingField(el) {
    if (this._dictatingEl) this._dictatingEl.classList.remove('vt-dictating');
    this._dictatingEl = el || null;
    if (el) el.classList.add('vt-dictating');
  },
  clearDictatingField() {
    if (this._dictatingEl) { this._dictatingEl.classList.remove('vt-dictating'); this._dictatingEl = null; }
  },

  destroy() {
    clearTimeout(this._notifTimer);
    this.clearHighlights(); this.clearOverlays(); this.clearDictatingField();
    this.closeElementList();
    if (this.notifEl) { this.notifEl.remove(); this.notifEl = null; }
    const st = document.getElementById('vt-styles'); if (st) st.remove();
  },
};

// ============================================================
// Section 5: DebugWidget — draggable status panel
// ============================================================
const DebugWidget = {
  el: null, minimized: false,
  _dragOffX: 0, _dragOffY: 0, _dragging: false,
  _els: null,            // cached element refs — avoids getElementById on every update
  _onMouseMove: null,    // bound drag listener refs for cleanup (#9, #50)
  _onMouseUp:   null,

  init() {
    if (document.getElementById('vt-debug')) { this.el = document.getElementById('vt-debug'); return; }

    const style = document.createElement('style');
    style.id = 'vt-debug-styles';
    style.textContent = `
      #vt-debug {
        position: fixed; top: 16px; right: 16px; z-index: 2147483640;
        width: 260px; background: rgba(10,10,20,0.93); color: #e2e8f0;
        border-radius: 12px; border: 1px solid rgba(255,255,255,0.12);
        box-shadow: 0 8px 32px rgba(0,0,0,0.5); backdrop-filter: blur(12px);
        font-family: 'Segoe UI', system-ui, monospace; font-size: 12px;
        user-select: none; cursor: grab; overflow: hidden;
        transition: box-shadow 0.2s;
      }
      #vt-debug:active { cursor: grabbing; box-shadow: 0 12px 40px rgba(0,0,0,0.7); }
      #vt-debug-header {
        display: flex; align-items: center; gap: 8px;
        padding: 8px 10px; background: rgba(255,255,255,0.06);
        border-bottom: 1px solid rgba(255,255,255,0.08);
      }
      #vt-debug-dot {
        width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;
        background: #4b5563; transition: background 0.3s, box-shadow 0.3s;
      }
      #vt-debug-dot.listening  { background: #22c55e; box-shadow: 0 0 8px #22c55e; animation: vt-dot-pulse 1.4s ease-in-out infinite; }
      #vt-debug-dot.processing { background: #f59e0b; box-shadow: 0 0 8px #f59e0b; }
      #vt-debug-dot.dictating  { background: #a855f7; box-shadow: 0 0 10px #a855f7; animation: vt-dot-pulse 0.9s ease-in-out infinite; }
      #vt-debug-dot.overlay    { background: #3b82f6; box-shadow: 0 0 8px #3b82f6; animation: vt-dot-pulse 1s ease-in-out infinite; }
      #vt-debug-dot.error      { background: #ef4444; box-shadow: 0 0 8px #ef4444; animation: vt-dot-flash 0.6s infinite; }
      #vt-debug-dot.inactive   { background: #4b5563; box-shadow: none; }
      @keyframes vt-dot-pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.7;transform:scale(1.15)} }
      @keyframes vt-dot-flash  { 0%,100%{opacity:1} 50%{opacity:0.4} }
      @media (prefers-reduced-motion: reduce) { #vt-debug-dot { animation: none !important; } }
      #vt-debug-title { flex: 1; font-weight: 700; font-size: 11px; letter-spacing: 0.05em; color: #94a3b8; text-transform: uppercase; }
      #vt-debug-status-text { font-size: 11px; color: #e2e8f0; font-weight: 600; }
      #vt-debug-guide, #vt-debug-min { background: none; border: none; color: #64748b; cursor: pointer; font-size: 14px; padding: 0 3px; line-height: 1; }
      #vt-debug-guide:hover, #vt-debug-min:hover { color: #e2e8f0; }
      #vt-debug-guide { font-weight: 700; font-size: 13px; }
      #vt-debug-body { padding: 10px; display: flex; flex-direction: column; gap: 6px; }
      .vt-row { display: flex; gap: 6px; align-items: baseline; }
      .vt-label { color: #64748b; font-size: 10px; text-transform: uppercase; letter-spacing: 0.06em; width: 46px; flex-shrink: 0; }
      .vt-val { color: #e2e8f0; font-size: 12px; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1; }
      .vt-val.mode-navigate { color: #22c55e; }
      .vt-val.mode-dictate  { color: #a855f7; }
      .vt-val.mode-overlay  { color: #3b82f6; }
      .vt-val.mode-select   { color: #06b6d4; }
      .vt-val.model-use     { color: #22c55e; }
      .vt-val.model-trigram { color: #f59e0b; }
      .vt-val.model-loading { color: #94a3b8; }
      #vt-debug-interim { color: #94a3b8; font-style: italic; font-size: 11px; min-height: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
      #vt-debug-sep { border: none; border-top: 1px solid rgba(255,255,255,0.07); margin: 2px 0; }
    `;
    document.head.appendChild(style);

    const el = document.createElement('div');
    el.id = 'vt-debug';
    el.setAttribute('role', 'complementary');
    el.setAttribute('aria-label', 'VoiceTracker debug panel');

    el.innerHTML = `
      <div id="vt-debug-header">
        <div id="vt-debug-dot" class="inactive"></div>
        <span id="vt-debug-title">VoiceTracker</span>
        <span id="vt-debug-status-text">Inactive</span>
        <button id="vt-debug-guide" title="Open voice command guide" aria-label="Open guide">?</button>
        <button id="vt-debug-min"   title="Minimise" aria-label="Minimise debug panel">−</button>
      </div>
      <div id="vt-debug-body">
        <div class="vt-row"><span class="vt-label">Mode</span><span class="vt-val mode-navigate" id="vt-d-mode">navigate</span></div>
        <div class="vt-row" id="vt-model-row" title="Click to toggle between USE-lite and trigram" style="cursor:pointer">
          <span class="vt-label">Model</span>
          <span class="vt-val model-loading" id="vt-d-model">loading…</span>
          <span id="vt-d-model-toggle" style="font-size:10px;color:#475569;margin-left:4px">⇄</span>
        </div>
        <hr id="vt-debug-sep">
        <div class="vt-row"><span class="vt-label">Heard</span><span class="vt-val" id="vt-d-heard">—</span></div>
        <div class="vt-row"><span class="vt-label">Match</span><span class="vt-val" id="vt-d-match">—</span></div>
        <div id="vt-debug-interim"></div>
      </div>
    `;

    document.body.appendChild(el);
    this.el = el;

    // Cache element refs — avoids getElementById on every voice result
    this._els = {
      dot:    el.querySelector('#vt-debug-dot'),
      status: el.querySelector('#vt-debug-status-text'),
      mode:   el.querySelector('#vt-d-mode'),
      model:  el.querySelector('#vt-d-model'),
      heard:  el.querySelector('#vt-d-heard'),
      match:  el.querySelector('#vt-d-match'),
      interim:el.querySelector('#vt-debug-interim'),
    };

    // Guide button
    el.querySelector('#vt-debug-guide').addEventListener('click', e => {
      e.stopPropagation();
      window.open('/voice-guide.html', '_blank');
    });

    // Model toggle — switch between USE-lite and trigram
    el.querySelector('#vt-model-row').addEventListener('click', () => {
      if (!state.active) return;
      state.forceTrigram = !state.forceTrigram;
      if (state.forceTrigram) {
        this.setModel('trigram');
        UIManager.notify('Switched to trigram mode', 2000);
      } else if (state.modelReady) {
        this.setModel('use');
        UIManager.notify('Switched to USE-lite mode', 2000);
      } else {
        this.setModel('loading');
        ModelManager.load(() => {}).then(loaded => {
          if (!state.active) return;   // (#11, #30, #43) stop() called during load
          this.setModel(loaded ? 'use' : 'trigram');
          if (!loaded) state.forceTrigram = true;
        });
      }
    });

    // Minimise toggle
    el.querySelector('#vt-debug-min').addEventListener('click', e => {
      e.stopPropagation();
      this.minimized = !this.minimized;
      const body = el.querySelector('#vt-debug-body');
      const sep  = el.querySelector('#vt-debug-sep');
      body.style.display = this.minimized ? 'none' : 'flex';
      if (sep) sep.style.display = this.minimized ? 'none' : '';
      e.target.textContent = this.minimized ? '+' : '−';
      e.target.title = this.minimized ? 'Expand' : 'Minimise';
    });

    // Drag support — store bound refs so destroy() can remove them (#9, #50)
    el.addEventListener('mousedown', e => {
      if (e.target.tagName === 'BUTTON') return;
      this._dragging = true;
      const rect = el.getBoundingClientRect();
      this._dragOffX = e.clientX - rect.left;
      this._dragOffY = e.clientY - rect.top;
      el.style.cursor = 'grabbing';
    });
    this._onMouseMove = e => {
      if (!this._dragging) return;
      const x = Math.min(Math.max(0, e.clientX - this._dragOffX), window.innerWidth  - el.offsetWidth);
      const y = Math.min(Math.max(0, e.clientY - this._dragOffY), window.innerHeight - el.offsetHeight);
      el.style.left = x + 'px'; el.style.top = y + 'px';
      el.style.right = 'auto';
    };
    this._onMouseUp = () => {
      this._dragging = false;
      if (this.el) this.el.style.cursor = 'grab';
    };
    document.addEventListener('mousemove', this._onMouseMove);
    document.addEventListener('mouseup',   this._onMouseUp);
  },

  setStatus(status, text) {
    if (!this._els) return;
    this._els.dot.className = status;
    this._els.status.textContent = text;
    _emitStatus(status);
  },

  setMode(mode) {
    if (!this._els) return;
    this._els.mode.textContent = mode;
    this._els.mode.className = `vt-val mode-${mode}`;
  },

  setModel(type) {
    if (!this._els) return;
    const labels = { loading: 'loading…', use: 'USE-lite ✓', trigram: 'trigram fallback' };
    this._els.model.textContent = labels[type] || type;
    this._els.model.className = `vt-val model-${type}`;
  },

  setHeard(text) {
    if (!this._els) return;
    this._els.heard.textContent = text || '—';
    this._els.interim.textContent = '';
  },

  setInterim(text) {
    if (!this._els) return;
    this._els.interim.textContent = text ? `"${text}"` : '';
  },

  setMatch(label, score) {
    if (!this._els) return;
    if (label) {
      this._els.match.textContent = `${label} (${score.toFixed(2)})`;
      this._els.match.style.color = score >= CONFIG.HIGH_CONF ? '#22c55e' : score >= CONFIG.MED_CONF ? '#f59e0b' : '#ef4444';
    } else {
      this._els.match.textContent = '— no match';
      this._els.match.style.color = '#ef4444';
    }
  },

  destroy() {
    // Remove document-level listeners before removing element (#9, #50)
    if (this._onMouseMove) { document.removeEventListener('mousemove', this._onMouseMove); this._onMouseMove = null; }
    if (this._onMouseUp)   { document.removeEventListener('mouseup',   this._onMouseUp);   this._onMouseUp   = null; }
    if (this.el) { this.el.remove(); this.el = null; }
    this._els = null;
    const st = document.getElementById('vt-debug-styles');
    if (st) st.remove();
  },
};

// ============================================================
// Section 6: ModelManager
// ============================================================
const ModelManager = {
  _obs: null,

  startObserving() {
    if (this._obs) return;
    this._obs = new MutationObserver(() => {
      // Debounce rapid DOM changes — Livewire updates, animations, etc. (#14, #31, #32, #33)
      if (state._scanDebounceId != null) window.clearTimeout(state._scanDebounceId);
      state._scanDebounceId = window.setTimeout(() => {
        state._scanDebounceId = null;
        state.scanDirty = true;
      }, CONFIG.SCAN_DEBOUNCE);
    });
    this._obs.observe(document.body, {
      childList: true, subtree: true, attributes: true,
      attributeFilter: ['aria-label', 'title', 'placeholder', 'value'],  // (#6)
    });
  },

  stopObserving() {
    if (this._obs) { this._obs.disconnect(); this._obs = null; }
    // Also clear any pending debounce timer (#14)
    if (state._scanDebounceId != null) {
      window.clearTimeout(state._scanDebounceId);
      state._scanDebounceId = null;
    }
  },

  async load(onProgress) {
    if (state.modelReady)   return true;
    if (state.modelLoading) return false;
    state.modelLoading = true;
    DebugWidget.setModel('loading');
    try {
      onProgress('Loading voice model…');
      // Must register TF.js backends (WebGL/CPU) before loading any model
      await import('https://esm.sh/@tensorflow/tfjs');
      const use   = await import(CONFIG.USE_CDN);
      state.model = await use.load();
      state.modelReady   = true;
      state.modelLoading = false;
      state.modelType    = 'use-lite';
      DebugWidget.setModel('use');
      sessionStorage.setItem(CONFIG.SESSION_KEY, '1');
      return true;
    } catch (err) {
      console.warn('[VoiceTracker] USE-lite unavailable, using trigram fallback:', err);
      state.model        = null;
      state.modelReady   = false;
      state.modelLoading = false;
      state.modelType    = 'trigram';
      DebugWidget.setModel('trigram');
      return false;
    }
  },

  // Embedding cache: label string → vector. Avoids re-encoding unchanged labels on rescan.
  _embedCache: new Map(),

  async encode(texts) {
    if (!state.model) return null;
    try {
      const embeddings = await state.model.embed(texts);
      const data = await embeddings.array();
      embeddings.dispose();
      return data;
    } catch { return null; }
  },

  async scanPage() {
    if (!state.scanDirty) return;    // DOM hasn't changed since last scan
    if (state._scanning)  return;    // prevent double scan (#4, race condition)
    state._scanning  = true;
    state.scanDirty  = false;

    const candidates = this._collectElements();
    const labels = candidates.map(c => c.label);

    // Pre-compute trigrams + norms for every element once.
    const withTrigrams = candidates.map(c => {
      const tg = utils._trigrams(c.label);
      let n2 = 0; tg.forEach(v => n2 += v * v);
      return { ...c, tg, tgNorm: Math.sqrt(n2) };
    });

    if (state.modelReady && state.model) {
      // Only encode labels not already cached — skip re-encoding unchanged elements
      const uncached = labels.filter(l => !this._embedCache.has(l));
      if (uncached.length > 0) {
        const newVecs = await this.encode(uncached);
        // DOM changed during async encode — mark for re-scan next utterance
        if (state.scanDirty) { state._scanning = false; return; }
        if (newVecs) {
          uncached.forEach((l, i) => this._embedCache.set(l, newVecs[i]));
          // Prevent unbounded cache growth on highly dynamic pages
          if (this._embedCache.size > 800) this._embedCache.clear();
        }
      }
      // Build elementVectors from cache (fall back to trigram if any vector missing)
      if (labels.every(l => this._embedCache.has(l))) {
        state.elementVectors = withTrigrams.map(c => ({ ...c, vec: this._embedCache.get(c.label) }));
        state._scanning = false;
        return;
      }
    }
    state.elementVectors = withTrigrams.map(c => ({ ...c, vec: null }));
    state._scanning = false;
  },

  _collectElements() {
    const SELECTORS = [
      'a','button','input','select','textarea',
      '[role="button"]','[role="link"]','[role="menuitem"]',
      '[role="tab"]','[role="checkbox"]','[role="radio"]','label',
    ].join(',');

    // Pass 1: dedup + style filter (all getComputedStyle reads together)
    const seen = new Set();
    const visible = [];
    document.querySelectorAll(SELECTORS).forEach(el => {
      if (seen.has(el)) return; seen.add(el);
      if (el.getAttribute('aria-hidden') === 'true') return;
      const cs = window.getComputedStyle(el);
      if (cs.display === 'none' || cs.visibility === 'hidden' || parseFloat(cs.opacity) < 0.01) return;
      visible.push(el);
    });

    // Pass 2: batch all getBoundingClientRect reads (single layout reflow)
    const rects = visible.map(el => el.getBoundingClientRect());

    // Pass 3: rect filter + label extraction
    const results = [];
    for (let i = 0; i < visible.length; i++) {
      const el = visible[i];
      const rect = rects[i];
      if (rect.width < 2 && rect.height < 2) continue;
      const label = this._getLabel(el);
      if (!label || label.length < 2) continue;
      results.push({ el, label, isInput: ['INPUT','TEXTAREA','SELECT'].includes(el.tagName) });
    }
    return results;
  },

  _getLabel(el) {
    const ariaLabelledBy = el.getAttribute('aria-labelledby');
    const fromLabelledBy = ariaLabelledBy ? (document.getElementById(ariaLabelledBy)?.textContent || '') : '';
    const raw =
      el.getAttribute('aria-label') || fromLabelledBy || el.getAttribute('title') ||
      el.getAttribute('placeholder') || el.textContent ||
      el.getAttribute('name') || el.getAttribute('id') || '';
    return raw.replace(/\s+/g, ' ').trim().toLowerCase().slice(0, 80);
  },
};

// ============================================================
// Section 7: IntentRouter
// ============================================================
const SYSTEM_CMDS = new Set([
  'cancel','stop','help','reset','undo',
  'show elements','show commands','elements','show',
  'done','done writing','stop writing','stop dictating',
  // delete variants
  'delete','erase','scratch that','delete that','remove that',
  'delete word','delete last',
  'backspace','delete character','delete char',
  'clear all','start over',
  'new line','next field',
  'submit','submit form',
  'refresh','refresh page','reload',
  'repeat',
  'select all',
  'open in new tab',
  'more',
]);

const SCROLL_PATTERNS = [
  { re: /^(scroll\s+)?down$/,                              fn: () => ScrollHandler.down()     },
  { re: /^(scroll\s+)?up$/,                               fn: () => ScrollHandler.up()       },
  { re: /^(go\s+to\s+)?(top|beginning)$/,                 fn: () => ScrollHandler.top()      },
  { re: /^(go\s+to\s+)?(bottom|end)$/,                    fn: () => ScrollHandler.bottom()   },
  { re: /^page\s+down$/,                                  fn: () => ScrollHandler.pageDown() },
  { re: /^page\s+up$/,                                    fn: () => ScrollHandler.pageUp()   },
  { re: /^(go\s+)?(back|previous(\s+page)?)$/,            fn: () => ScrollHandler.goBack()   },
  { re: /^(go\s+)?(forward|next(\s+page)?)$/,             fn: () => ScrollHandler.goForward()},
];

const IntentRouter = {
  async route(utterance) {
    const text = utils.sanitize(utterance);

    if (state.mode === 'overlay') {
      DebugWidget.setStatus('overlay', 'Choosing');
      DebugWidget.setHeard(utterance);
      if (SYSTEM_CMDS.has(text)) CommandHandler.handle(text);
      else NavigationHandler.handleOverlayChoice(text);
      return;
    }

    if (state.mode === 'select') {
      DebugWidget.setStatus('processing', 'Selecting');
      DebugWidget.setHeard(utterance);
      if (SYSTEM_CMDS.has(text)) {
        CommandHandler.handle(text);
      } else {
        for (const { re, fn } of SCROLL_PATTERNS) {
          if (re.test(text)) {
            DebugWidget.setStatus('processing', 'Scroll');
            fn();
            return;
          }
        }
        SelectHandler.choose(text);
      }
      return;
    }

    if (SYSTEM_CMDS.has(text)) {
      DebugWidget.setStatus('processing', 'Command');
      DebugWidget.setHeard(utterance);
      CommandHandler.handle(text);
      return;
    }

    // Scroll commands work in ANY mode — checked before dictation so they're never typed as text
    for (const { re, fn } of SCROLL_PATTERNS) {
      if (re.test(text)) {
        DebugWidget.setStatus('processing', 'Scroll');
        DebugWidget.setHeard(utterance);
        fn();
        utils.setTimeout(() => DebugWidget.setStatus('listening', 'Listening'), 1500);
        return;
      }
    }

    if (state.mode === 'dictate') {
      DebugWidget.setStatus('dictating', 'Dictating');
      DebugWidget.setHeard(utterance);
      DictationHandler.commit(utterance);
      return;
    }

    DebugWidget.setStatus('processing', 'Matching…');
    DebugWidget.setHeard(utterance);
    await SemanticMatcher.findAndAct(utterance, text);
  },
};

// ============================================================
// Section 8: SemanticMatcher
// ============================================================
const SemanticMatcher = {

  async findAndAct(utterance, text) {
    await ModelManager.scanPage();

    if (state.elementVectors.length === 0) {
      DebugWidget.setMatch(null, 0);
      UIManager.notify('No interactive elements found on page.', 3000);
      AudioCues.error();
      DebugWidget.setStatus('listening', 'Listening');
      return;
    }

    const ranked = (!state.forceTrigram && state.modelReady && state.model)
      ? await this._rankWithUSE(utterance)
      : this._rankWithTrigram(text);

    if (!ranked || ranked.length === 0) {
      DebugWidget.setMatch(null, 0);
      AudioCues.error();
      UIManager.notify("No match — say 'show elements' to see what's available.", 4000);
      DebugWidget.setStatus('listening', 'Listening');
      return;
    }

    const best = ranked[0];
    DebugWidget.setMatch(best.label, best.score);

    if (best.score < CONFIG.MED_CONF) {
      AudioCues.error();
      UIManager.notify("No match — say 'show elements' to see what's available.", 4000);
      DebugWidget.setStatus('listening', 'Listening');
      return;
    }

    const dupes = ranked.filter(r => r.score >= CONFIG.MED_CONF && best.score - r.score <= CONFIG.DUP_BAND);
    if (dupes.length > 1 && best.score < CONFIG.HIGH_CONF) {
      NavigationHandler.showDisambiguation(dupes);
      return;
    }

    if (best.score >= CONFIG.HIGH_CONF) {
      NavigationHandler.clickElement(best);
    } else {
      NavigationHandler.showDisambiguation([best]);
    }
  },

  async _rankWithUSE(utterance) {
    const vecs = await ModelManager.encode([utterance]);
    if (!vecs) return this._rankWithTrigram(utils.sanitize(utterance));
    const qv = vecs[0];
    return state.elementVectors.map(ev => ({ ...ev, score: utils.cosineSim(qv, ev.vec) })).sort((a,b) => b.score - a.score);
  },

  _rankWithTrigram(text) {
    const qt = utils._trigrams(text);
    let qn2 = 0; qt.forEach(v => qn2 += v * v);
    const qNorm = Math.sqrt(qn2);
    return state.elementVectors
      .map(ev => ({ ...ev, score: utils.trigramSimFast(qt, qNorm, ev.tg, ev.tgNorm) }))
      .sort((a, b) => b.score - a.score);
  },
};

// ============================================================
// Section 9: NavigationHandler
// ============================================================

// Module-level constants — allocated once, not on every _resolveChoice call
const _OL = { a: 1, b: 2, c: 3, d: 4 };
const _ON = { one:1, won:1, first:1, two:2, to:2, too:2, second:2, three:3, third:3, four:4, for:4, fore:4, fourth:4 };
const _COMPACT_RE = /^([1-4])\s*([a-d])$/;

const NavigationHandler = {

  clickElement({ el, label }) {
    // Guard against stale element refs from pre-navigation scans (#3, #5)
    if (!document.contains(el)) {
      UIManager.notify(`"${label}" no longer in page - rescanning...`, 2500);
      state.scanDirty = true;
      DebugWidget.setStatus('listening', 'Listening');
      return;
    }
    UIManager.closeElementList();
    UIManager.clearOverlays();
    UIManager.highlight(el);
    AudioCues.match();
    UIManager.notify(`Clicking: ${label}`, 2000);
    DebugWidget.setStatus('processing', 'Clicking');
    state.lastAction = {
      type: 'click',
      el,
      label,
      href: el?.tagName === 'A' ? el.href : null,
    };
    // Only scroll if element is outside the viewport — skip if already visible
    const _r = el.getBoundingClientRect();
    const _inView = _r.top >= 0 && _r.bottom <= window.innerHeight && _r.left >= 0 && _r.right <= window.innerWidth;
    if (!_inView) { try { el.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); } catch {} }
    // 50ms when already in view (no scroll needed), 200ms when scrolling to let it settle
    utils.setTimeout(() => {
      try {
        const textInput = el.tagName === 'INPUT'
          && !['submit', 'button', 'hidden', 'checkbox', 'radio', 'file', 'range', 'color', 'image', 'reset']
            .includes((el.type || '').toLowerCase());
        if (el.tagName === 'TEXTAREA' || el.tagName === 'SELECT' || textInput) {
          state._vtFocusing = true;   // (#8) prevent _onFocusIn from double-triggering dictation
          el.focus();
          state._vtFocusing = false;
          DictationHandler.enter(el); // explicitly start dictation - _onFocusIn was blocked above
        } else {
          el.click();
        }
      } catch (e) {
        console.warn('[VoiceTracker] Click failed:', e);
        state.scanDirty = true;
        state._vtFocusing = false;
      }
      UIManager.clearHighlights();
      if (!['dictate', 'select'].includes(state.mode)) DebugWidget.setStatus('listening', 'Listening');
    }, _inView ? 50 : 200);
  },

  showDisambiguation(matches) {
    state.mode = 'overlay';
    state.overlayPage = 0;
    state.overlayMatches = matches.map(m => ({ el: m.el, label: m.label }));
    this._renderOverlayPage();
    DebugWidget.setStatus('overlay', 'Disambiguating');
    DebugWidget.setMode('overlay');
  },

  _renderOverlayPage() {
    if (state.mode !== 'overlay') return;
    const total = state.overlayMatches.length;
    if (!total) { this.cancelOverlay(); return; }

    const pageSize = 4;
    const maxPage = Math.max(0, Math.ceil(total / pageSize) - 1);
    if (state.overlayPage > maxPage) state.overlayPage = maxPage;

    const start = state.overlayPage * pageSize;
    const end = Math.min(total, start + pageSize);
    const page = state.overlayMatches
      .slice(start, end)
      .map((m, i) => ({ number: i + 1, el: m.el, label: m.label }));

    UIManager.showOverlays(page);
    state.overlayTimeoutId = utils.clearTimeout(state.overlayTimeoutId);
    state.overlayTimeoutId = utils.setTimeout(() => this.cancelOverlay(), CONFIG.OVERLAY_TIMEOUT);
    const moreHint = end < total ? " Say 'more' for next options." : '';
    UIManager.notify(`Showing ${start + 1}-${end} of ${total}. Say one, two, three, four, or an option name - or 'cancel'.${moreHint}`, 0);
  },

  moreOverlayPage() {
    if (state.mode !== 'overlay') return;
    const pageSize = 4;
    const total = state.overlayMatches.length;
    const nextStart = (state.overlayPage + 1) * pageSize;
    if (nextStart >= total) {
      UIManager.notify('No more options. Say one, two, three, four - or cancel.', 2500);
      return;
    }
    state.overlayPage += 1;
    this._renderOverlayPage();
  },

  // Resolve overlay choice. Accepts any of:
  //   digit alone:  "1", "2", "3", "4"
  //   letter alone: "a", "b", "c", "d"
  //   number word:  "one", "two", "three", "four" (+ homophones won/to/too/for/fore)
  //   combined:     "2b", "2 b", "one a", "two b", "for d" (Chrome often merges into one token)
  _resolveChoice(text) {
    // Single digit
    if (text.length === 1 && text >= '1' && text <= '4') return +text;
    // Single letter or number word
    if (_OL[text] !== undefined) return _OL[text];
    if (_ON[text] !== undefined) return _ON[text];
    // Compact token: "2b", "1a", "3 c"
    const m = _COMPACT_RE.exec(text);
    if (m) return +m[1];
    // Multi-word: scan tokens for number word first, then letter
    const parts = text.split(' ');
    for (const p of parts) { if (_ON[p] !== undefined) return _ON[p]; }
    for (const p of parts) { if (_OL[p] !== undefined) return _OL[p]; }
    return null;
  },

  _matchOverlayByLabel(text, minScore = 0.62) {
    const spoken = utils.sanitize(text);
    if (!spoken || spoken.length < 2) return null;

    const start = state.overlayPage * 4;
    const page = state.overlayMatches.slice(start, start + 4);
    let best = null;

    for (const item of page) {
      const label = utils.sanitize(item.label);
      if (!label) continue;

      let score = utils.trigramSim(spoken, label);
      if (spoken === label) score = 1;
      else if (label.includes(spoken) || spoken.includes(label)) score = Math.max(score, 0.90);

      if (!best || score > best.score) best = { ...item, score };
    }

    return best && best.score >= minScore ? best : null;
  },

  handleOverlayChoice(text) {
    if (/^(cancel|never\s*mind|stop)$/.test(text)) { this.cancelOverlay(); return; }
    if (text === 'more') { this.moreOverlayPage(); return; }
    const n = this._resolveChoice(text);
    const start = state.overlayPage * 4;
    const page = state.overlayMatches.slice(start, start + 4);
    const byNumber = n ? page[n - 1] : null;
    const byLabel = byNumber ? null : this._matchOverlayByLabel(text, 0.55);
    const match = byNumber || byLabel;
    if (match) {
      this.cancelOverlay();
      this.clickElement(match);
    } else {
      UIManager.notify('Say one, two, three, four, an option name, or more. Say cancel to go back.', 2500);
    }
  },

  cancelOverlay() {
    state.overlayTimeoutId = utils.clearTimeout(state.overlayTimeoutId);
    UIManager.clearOverlays(); UIManager.hide();
    state.mode = 'navigate';
    state.overlayMatches = [];
    state.overlayPage = 0;
    DebugWidget.setMode('navigate');
    DebugWidget.setStatus('listening', 'Listening');
    // NOTE: do NOT reset overlayJustHandled here - it is set AFTER this call returns (#1, #2)
  },
};

// ============================================================
// Section 10: DictationHandler
// ============================================================
const DictationHandler = {

  enter(field) {
    if (!field) return;
    if (state.mode === 'select') SelectHandler.exit(false);
    if (state.mode === 'dictate' && state.dictationField && state.dictationField !== field) this.exit(false);

    state.dictationField = field;
    state.interimText = '';

    if (field.tagName === 'SELECT') {
      state.selectOptions = Array.from(field.options || [])
        .filter(opt => !opt.disabled && !opt.hidden)
        .map((opt, idx) => {
          const text = String(opt.textContent || opt.label || opt.value || '').trim();
          return {
            value: opt.value,
            text: text || `(option ${idx + 1})`,
            norm: String(text || '').toLowerCase(),
            index: idx,
          };
        });

      state.mode = 'select';
      UIManager.setDictatingField(field);
      UIManager.showElementList(
        state.selectOptions.map(opt => ({ label: opt.text, isInput: false })),
        `${state.selectOptions.length} options - say an option name`
      );
      UIManager.notify(`Select open - ${state.selectOptions.length} options. Say an option name or 'cancel'.`, 5000);
      AudioCues.dictate();
      DebugWidget.setMode('select');
      DebugWidget.setStatus('dictating', 'Selecting');
      return;
    }

    state.selectOptions = [];
    state.dictationPrev = field.value;
    state.dictationOrigPlaceholder = field.getAttribute('placeholder') || '';
    state.mode = 'dictate';
    UIManager.setDictatingField(field);
    UIManager.notify('Dictating - speak to type. Say "done writing" to stop.', 4000);
    AudioCues.dictate();
    DebugWidget.setMode('dictate');
    DebugWidget.setStatus('dictating', 'Dictating');
  },

  exit(notify = true) {
    if (state.mode === 'select') { SelectHandler.exit(notify); return; }

    UIManager.clearDictatingField();
    this._clearInterim();
    if (notify) UIManager.notify('Dictation stopped.', 2000);
    AudioCues.done();

    state.dictationField = null;
    state.dictationOrigPlaceholder = '';
    state.interimText = '';
    state.mode = 'navigate';
    DebugWidget.setMode('navigate');
    DebugWidget.setStatus('listening', 'Listening');
  },

  preview(text) {
    const f = state.dictationField;
    if (!f || !text || state.mode !== 'dictate') return;
    DebugWidget.setInterim(text);
    if (f.value === '' || f.dataset.vtInterim) {
      f.setAttribute('placeholder', `${text}...`);
      f.dataset.vtInterim = '1';
    }
  },

  commit(utterance) {
    const f = state.dictationField;
    if (!f || state.mode !== 'dictate') return;
    const cmd = utils.sanitize(utterance);
    // Delegate ALL system commands to CommandHandler - single source of truth (#17)
    if (SYSTEM_CMDS.has(cmd)) { CommandHandler.handle(cmd); return; }
    state.dictationPrev = f.value;
    this._clearInterim();
    const formatted = this._smartFormat(utterance, f);
    const noAutoSpace = (f.type || '').toLowerCase() === 'password';
    const sep = (!noAutoSpace && f.value && !f.value.endsWith('\n') && !f.value.endsWith(' ')) ? ' ' : '';
    f.value = f.value + sep + formatted;
    this._dispatch(f);
  },

  _dispatch(f) {
    f.dispatchEvent(new Event('input', { bubbles: true }));
    f.dispatchEvent(new Event('change', { bubbles: true }));
  },

  _clearInterim() {
    const f = state.dictationField;
    if (!f || f.tagName === 'SELECT') return;
    delete f.dataset.vtInterim;
    // Restore original placeholder instead of unconditionally removing (#7, #42)
    if (state.dictationOrigPlaceholder) {
      f.setAttribute('placeholder', state.dictationOrigPlaceholder);
    } else {
      f.removeAttribute('placeholder');
    }
    DebugWidget.setInterim('');
  },

  _deleteLastWord(f) {
    state.dictationPrev = f.value;
    const trimmed = f.value.trimEnd();
    const lastSpace = trimmed.lastIndexOf(' ');
    f.value = lastSpace === -1 ? '' : trimmed.slice(0, lastSpace);
    this._dispatch(f);
  },

  _backspaceChar(f) {
    state.dictationPrev = f.value;
    const start = typeof f.selectionStart === 'number' ? f.selectionStart : null;
    const end = typeof f.selectionEnd === 'number' ? f.selectionEnd : null;

    if (start != null && end != null) {
      if (start !== end) {
        f.value = f.value.slice(0, start) + f.value.slice(end);
        try { f.setSelectionRange(start, start); } catch {}
      } else if (start > 0) {
        const nextPos = start - 1;
        f.value = f.value.slice(0, nextPos) + f.value.slice(end);
        try { f.setSelectionRange(nextPos, nextPos); } catch {}
      } else {
        return false;
      }
      this._dispatch(f);
      return true;
    }

    if (!f.value) return false;
    f.value = f.value.slice(0, -1);
    this._dispatch(f);
    return true;
  },

  nextField() {
    const f = state.dictationField;
    if (state.mode === 'select') SelectHandler.exit(false);
    else this.exit(false);
    if (!f) return;

    // Exclude submit/button/hidden inputs from Tab cycle (#54)
    const inputs = Array.from(document.querySelectorAll(
      'input:not([type=hidden]):not([type=submit]):not([type=button]),textarea,select'
    ));
    const next = inputs[inputs.indexOf(f) + 1];
    if (next) {
      next.focus();
      this.enter(next);
    }
  },

  _smartFormat(text, field) {
    const type = field?.type?.toLowerCase() || '';
    if (type === 'email') return text.toLowerCase().replace(/\s/g, '');
    if (type === 'number') return text.replace(/[^\d.,-]/g, '');

    // Keep only explicitly spoken punctuation words; strip punctuation that
    // speech engines may add automatically (e.g., trailing periods).
    let out = String(text || '')
      .replace(/\bquestion mark\b/gi, ' __VT_QM__ ')
      .replace(/\bexclamation (mark|point)\b/gi, ' __VT_EX__ ')
      .replace(/\bsemicolon\b/gi, ' __VT_SC__ ')
      .replace(/\bcolon\b/gi, ' __VT_COL__ ')
      .replace(/\bcomma\b/gi, ' __VT_COM__ ')
      .replace(/\bperiod\b/gi, ' __VT_PER__ ')
      .replace(/\bdot\b/gi, ' __VT_PER__ ')
      .replace(/\bnew line\b/gi, ' __VT_NL__ ');

    out = out.replace(/[.,!?;:]/g, '');
    out = out
      .replace(/__VT_QM__/g, '?')
      .replace(/__VT_EX__/g, '!')
      .replace(/__VT_SC__/g, ';')
      .replace(/__VT_COL__/g, ':')
      .replace(/__VT_COM__/g, ',')
      .replace(/__VT_PER__/g, '.')
      .replace(/__VT_NL__/g, '\n')
      .replace(/[ \t]+\n/g, '\n')
      .replace(/\n[ \t]+/g, '\n')
      .replace(/[ \t]{2,}/g, ' ')
      .replace(/\s+([.,!?;:])/g, '$1')
      .trim();

    // Do not auto-capitalise passwords.
    if (type === 'password') return out;

    out = out.replace(/([.?!]\s+)(\w)/g, (_, p, c) => p + c.toUpperCase());
    if (out.length > 0) out = out.charAt(0).toUpperCase() + out.slice(1);
    return out;
  },
};

const SelectHandler = {
  choose(rawText) {
    if (state.mode !== 'select') return;

    const spoken = utils.sanitize(rawText);
    if (/^(cancel|never\s*mind|stop)$/.test(spoken)) { this.exit(true); return; }

    const field = state.dictationField;
    if (!field || field.tagName !== 'SELECT' || !document.contains(field)) {
      this.exit(false);
      return;
    }

    const options = state.selectOptions;
    if (!options.length) {
      UIManager.notify('No options available. Say cancel to leave select mode.', 3000);
      AudioCues.error();
      return;
    }

    const directNumber = Number.parseInt(spoken, 10);
    if (Number.isInteger(directNumber) && directNumber >= 1 && directNumber <= options.length) {
      const byNumber = options[directNumber - 1];
      field.selectedIndex = byNumber.index;
      field.dispatchEvent(new Event('input', { bubbles: true }));
      field.dispatchEvent(new Event('change', { bubbles: true }));
      UIManager.notify(`Selected: ${byNumber.text}`, 2000);
      AudioCues.done();
      this.exit(false);
      return;
    }

    let best = null;
    for (const opt of options) {
      let score = utils.trigramSim(spoken, opt.norm);
      if (spoken === opt.norm) score = 1;
      else if (opt.norm.includes(spoken) || spoken.includes(opt.norm)) score = Math.max(score, 0.86);
      if (!best || score > best.score) best = { ...opt, score };
    }

    if (!best || best.score < 0.30) {
      UIManager.notify('No option matched. Say an option name or cancel.', 3000);
      AudioCues.error();
      return;
    }

    field.selectedIndex = best.index;
    field.dispatchEvent(new Event('input', { bubbles: true }));
    field.dispatchEvent(new Event('change', { bubbles: true }));
    UIManager.notify(`Selected: ${best.text}`, 2000);
    AudioCues.done();
    this.exit(false);
  },

  exit(notify = true) {
    UIManager.clearDictatingField();
    UIManager.closeElementList();
    state.selectOptions = [];
    state.dictationField = null;
    state.dictationOrigPlaceholder = '';
    state.interimText = '';
    state.mode = 'navigate';
    if (notify) UIManager.notify('Selection closed.', 1500);
    DebugWidget.setMode('navigate');
    DebugWidget.setStatus('listening', 'Listening');
  },
};

// ============================================================
// Section 11: ScrollHandler
// ============================================================
const ScrollHandler = {
  down()      { window.scrollBy({ top:  CONFIG.SCROLL_AMOUNT,      behavior: 'smooth' }); UIManager.notify('Scrolled down',    1500); },
  up()        { window.scrollBy({ top: -CONFIG.SCROLL_AMOUNT,      behavior: 'smooth' }); UIManager.notify('Scrolled up',      1500); },
  top()       { window.scrollTo({ top: 0,                          behavior: 'smooth' }); UIManager.notify('Jumped to top',    1500); },
  bottom()    { window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' }); UIManager.notify('Jumped to bottom', 1500); },
  pageDown()  { window.scrollBy({ top:  window.innerHeight * 0.8,  behavior: 'smooth' }); UIManager.notify('Page down',        1500); },
  pageUp()    { window.scrollBy({ top: -window.innerHeight * 0.8,  behavior: 'smooth' }); UIManager.notify('Page up',          1500); },
  goBack()    { history.back();    UIManager.notify('Going back',    1500); },
  goForward() { history.forward(); UIManager.notify('Going forward', 1500); },
};

// ============================================================
// Section 12: CommandHandler
// ============================================================
const CommandHandler = {
  _isVisible(el) {
    if (!el || !document.contains(el)) return false;
    const cs = window.getComputedStyle(el);
    if (cs.display === 'none' || cs.visibility === 'hidden' || parseFloat(cs.opacity) < 0.01) return false;
    const rect = el.getBoundingClientRect();
    return rect.width >= 2 || rect.height >= 2;
  },

  _findSubmitForm() {
    const fromField = state.dictationField?.closest?.('form');
    if (fromField && this._isVisible(fromField)) return fromField;
    return Array.from(document.querySelectorAll('form')).find(form => this._isVisible(form)) || null;
  },

  _findSubmitButton(form) {
    if (!form) return null;
    return form.querySelector(
      'button[type="submit"]:not([disabled]),input[type="submit"]:not([disabled]),button:not([type]):not([disabled])'
    );
  },

  _submitActiveForm() {
    const form = this._findSubmitForm();
    if (!form) {
      UIManager.notify('No form found to submit.', 3000);
      AudioCues.error();
      return;
    }

    const submitter = this._findSubmitButton(form);
    if (submitter && document.contains(submitter)) {
      state.lastAction = {
        type: 'click',
        el: submitter,
        label: 'submit',
        href: submitter?.tagName === 'A' ? submitter.href : null,
      };
      try { submitter.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); } catch {}
      utils.setTimeout(() => {
        try {
          submitter.click();
          UIManager.notify('Submitting form.', 1800);
          AudioCues.done();
        } catch {
          UIManager.notify('Could not click submit button.', 2500);
          AudioCues.error();
        }
      }, 200);
      return;
    }

    if (typeof form.requestSubmit === 'function') {
      form.requestSubmit();
      UIManager.notify('Submitting form.', 1800);
      AudioCues.done();
      return;
    }

    UIManager.notify('No submit button found for this form.', 3000);
    AudioCues.error();
  },

  _repeatLastAction() {
    const action = state.lastAction;
    if (!action) {
      UIManager.notify('Nothing to repeat yet.', 2500);
      AudioCues.error();
      return;
    }

    if (action.type === 'click') {
      if (action.el && document.contains(action.el)) {
        NavigationHandler.clickElement({ el: action.el, label: action.label || 'last action' });
        return;
      }
      if (action.href) {
        window.location.href = action.href;
        return;
      }
    }

    UIManager.notify('Cannot repeat the last action on this page.', 3000);
    AudioCues.error();
  },

  handle(cmd) {
    const f = state.dictationField;   // null-safe reference used throughout (#8, #27)
    switch (cmd) {
      case 'cancel': case 'stop':
        if (state.mode === 'overlay') NavigationHandler.cancelOverlay();
        else if (state.mode === 'dictate') DictationHandler.exit();
        else if (state.mode === 'select') SelectHandler.exit(true);
        break;
      case 'reset':
        // Voice-accessible reset - cancel any active state and return to navigate mode
        if (state.mode === 'overlay') NavigationHandler.cancelOverlay();
        if (state.mode === 'dictate') DictationHandler.exit(false);
        if (state.mode === 'select') SelectHandler.exit(false);
        UIManager.notify('Voice control reset.', 2000);
        DebugWidget.setMode('navigate');
        DebugWidget.setStatus('listening', 'Listening');
        break;
      case 'undo':
        if (state.mode === 'dictate' && f) {
          f.value = state.dictationPrev;
          f.dispatchEvent(new Event('input', { bubbles: true }));
          UIManager.notify('Undone', 1500);
          AudioCues.done();
        }
        break;
      case 'show elements': case 'show commands': case 'elements': case 'show': {
        UIManager.notify('Scanning page...', 1000);
        state.scanDirty = true;   // force fresh scan so stale vectors are not shown (#5)
        ModelManager.scanPage().then(() => {
          if (!state.active) return;   // guard against stop() during async scan (#11)
          if (state.elementVectors.length === 0) {
            UIManager.notify('No elements found on this page.', 3000);
          } else {
            UIManager.showElementList(state.elementVectors);
          }
        });
        break;
      }
      case 'help':
        if (state.mode === 'dictate') {
          UIManager.notify('Dictating. Say text to type. Commands: "backspace", "delete word", "clear all", "new line", "next field", "select all", "done writing".', 8000);
        } else if (state.mode === 'overlay') {
          UIManager.notify('Say "one", "two", "three", "four" or the option name to choose. Say "more" for next options. Say "cancel" to go back.', 6000);
        } else if (state.mode === 'select') {
          UIManager.notify('Select open. Say the option name or "cancel".', 5000);
        } else {
          UIManager.notify('Say any element name to click it. "show elements" to see all. "scroll down/up", "top", "bottom". "submit", "refresh", "help".', 7000);
        }
        break;
      case 'done': case 'done writing': case 'stop writing': case 'stop dictating':
        if (state.mode === 'dictate') DictationHandler.exit();
        else if (state.mode === 'select') SelectHandler.exit(true);
        break;
      case 'delete': case 'erase': case 'scratch that': case 'delete that': case 'remove that':
      case 'delete word': case 'delete last':
        if (state.mode === 'dictate' && f) {
          DictationHandler._deleteLastWord(f);
          UIManager.notify('Deleted last word', 1500);
          AudioCues.done();
        }
        break;
      case 'backspace': case 'delete character': case 'delete char':
        if (state.mode === 'dictate' && f) {
          const ok = DictationHandler._backspaceChar(f);
          if (ok) {
            UIManager.notify('Deleted one character', 1200);
            AudioCues.done();
          } else {
            UIManager.notify('Nothing to delete.', 1200);
            AudioCues.error();
          }
        }
        break;
      case 'clear all': case 'start over':
        if (state.mode === 'dictate' && f) {
          state.dictationPrev = f.value;
          f.value = '';
          f.dispatchEvent(new Event('input', { bubbles: true }));
          UIManager.notify('Cleared', 1500);
          AudioCues.done();
        }
        break;
      case 'new line':
        // Guard against non-text input types that do not support newlines (#18, #54)
        if (state.mode === 'dictate' && f) {
          if (['', 'text', 'search', 'url', 'tel', 'textarea'].includes(f.type || '')) {
            const p = f.selectionStart ?? f.value.length;
            f.value = f.value.slice(0, p) + '\n' + f.value.slice(p);
            f.dispatchEvent(new Event('input', { bubbles: true }));
            AudioCues.done();
          }
        }
        break;
      case 'next field':
        if (state.mode === 'dictate' || state.mode === 'select') DictationHandler.nextField();
        break;
      case 'select all':
        if (state.mode === 'dictate' && f && typeof f.select === 'function') {
          f.select();
          UIManager.notify('Selected all text.', 1500);
          AudioCues.done();
        }
        break;
      case 'submit': case 'submit form':
        this._submitActiveForm();
        break;
      case 'refresh': case 'refresh page': case 'reload':
        window.location.reload();
        return;
      case 'repeat':
        this._repeatLastAction();
        break;
      case 'open in new tab':
        if (state.lastAction?.href) {
          window.open(state.lastAction.href, '_blank', 'noopener');
          UIManager.notify('Opened link in new tab.', 1800);
          AudioCues.done();
        } else {
          UIManager.notify('No recent link click to open in a new tab.', 2500);
          AudioCues.error();
        }
        break;
      case 'more':
        if (state.mode === 'overlay') NavigationHandler.moreOverlayPage();
        else UIManager.notify('No additional options right now.', 1500);
        break;
      default:
        break;
    }

    utils.setTimeout(() => {
      if (state.active && state.mode === 'navigate') DebugWidget.setStatus('listening', 'Listening');
    }, 1500);
  },
};

// ============================================================
// Section 13: VoiceController
// ============================================================
const VoiceController = {
  _focusInHandler: null, _focusOutHandler: null,

  init() {
    const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SR) {
      UIManager.notify('Voice control requires Chrome or Edge — not supported in this browser.', 0);
      DebugWidget.setStatus('error', 'Unsupported');
      return false;
    }
    const rec = new SR();
    rec.continuous = true; rec.interimResults = true; rec.lang = 'en-US'; rec.maxAlternatives = 1;

    rec.onstart = () => {
      state.recognitionActive = true;
      DebugWidget.setStatus('listening', 'Listening');
    };

    rec.onresult = (event) => {
      let interim = '';
      for (let i = event.resultIndex; i < event.results.length; i++) {
        const result = event.results[i];
        const transcript = result[0].transcript.trim();
        if (result.isFinal) {
          state.interimText = '';
          DebugWidget.setInterim('');
          // If interim already handled an overlay choice, skip routing the final echo
          if (state.overlayJustHandled) { state.overlayJustHandled = false; continue; }
          IntentRouter.route(transcript);
        } else {
          interim += transcript;
          // In overlay mode, act on interim immediately — single words are often never marked final
          if (state.mode === 'overlay') {
            const t = utils.sanitize(interim);
            const n = NavigationHandler._resolveChoice(t);
            const isCancelWord = /^(cancel|never\s*mind|stop)$/.test(t);
            const isMoreWord = t === 'more';
            const interimLabelMatch = t.length >= 3 ? NavigationHandler._matchOverlayByLabel(t, 0.84) : null;
            if (n || isCancelWord || isMoreWord || interimLabelMatch) {
              DebugWidget.setHeard(`[interim] ${t}`);
              if (isMoreWord) CommandHandler.handle('more');
              else NavigationHandler.handleOverlayChoice(interimLabelMatch ? interimLabelMatch.label : t);
              // Set AFTER the call — cancelOverlay() runs inside and must not clear this (#1, #2)
              state.overlayJustHandled = true;
              return;
            }
          }
        }
      }
      if (interim && state.mode === 'dictate' && state.dictationField) {
        state.interimText = interim;
        DictationHandler.preview(interim);
      } else if (interim) {
        DebugWidget.setInterim(interim);
      }
    };

    rec.onerror = (event) => {
      state.recognitionActive = false;
      AudioCues.error();
      if (event.error === 'no-speech') {
        DebugWidget.setStatus('listening', 'Waiting…');
        utils.setTimeout(() => { if (state.active) this._startRec(); }, CONFIG.NO_SPEECH_RETRY);
      } else if (event.error === 'not-allowed' || event.error === 'denied') {
        UIManager.notify('Microphone blocked — allow microphone in browser settings and refresh.', 0);
        DebugWidget.setStatus('error', 'Mic blocked');
        state.active = false;
      } else if (event.error === 'network') {
        UIManager.notify('Network error — retrying…', 3000);   // (#25)
        DebugWidget.setStatus('error', 'Network error');
        utils.setTimeout(() => { if (state.active) this._startRec(); }, CONFIG.NO_SPEECH_RETRY);
      } else if (event.error === 'aborted') {
        // Aborted (e.g. during SPA body swap) — retry after short delay
        utils.setTimeout(() => { if (state.active) this._startRec(); }, 500);
      } else {
        console.warn('[VoiceTracker] Recognition error:', event.error);
        DebugWidget.setStatus('error', event.error);
      }
    };

    rec.onend = () => {
      state.recognitionActive = false;
      if (state.active) utils.setTimeout(() => { if (state.active) this._startRec(); }, 200);
    };

    state.recognition = rec;
    return true;
  },

  _startRec() {
    if (state.recognitionActive || !state.recognition) return;
    try { state.recognition.start(); } catch (e) {
      if (!String(e).includes('InvalidStateError')) console.warn('[VoiceTracker] start():', e);  // (#24)
    }
  },

  _stopRec() {
    if (!state.recognition) return;
    try { state.recognition.stop(); } catch {}
    state.recognitionActive = false;
  },

  _onFocusIn(e) {
    if (state._vtFocusing) return;   // (#8) VT itself focused this element - do not trigger dictation
    if (!state.active || state.mode === 'overlay') return;
    const el = e.target;
    if (!['INPUT', 'TEXTAREA', 'SELECT'].includes(el.tagName)) return;

    if (el.tagName === 'INPUT') {
      const blocked = ['submit', 'button', 'hidden', 'checkbox', 'radio', 'file', 'range', 'color', 'image', 'reset'];
      if (blocked.includes((el.type || '').toLowerCase())) return;
    }

    if (state.dictationField !== el || !['dictate', 'select'].includes(state.mode)) DictationHandler.enter(el);
  },

  _onFocusOut() {
    if (!state.active || !['dictate', 'select'].includes(state.mode)) return;
    utils.setTimeout(() => {
      if (!state.active) return;   // (#16, #29) stop() called during 150ms delay
      const active = document.activeElement;
      if (!active || !['INPUT', 'TEXTAREA', 'SELECT'].includes(active.tagName)) {
        if (state.mode === 'select') SelectHandler.exit(false);
        else DictationHandler.exit(false);
      }
    }, 150);
  },

  attachFocusListeners() {
    this._focusInHandler  = this._onFocusIn.bind(this);
    this._focusOutHandler = this._onFocusOut.bind(this);
    document.addEventListener('focusin',  this._focusInHandler);
    document.addEventListener('focusout', this._focusOutHandler);
  },

  detachFocusListeners() {
    if (this._focusInHandler)  document.removeEventListener('focusin',  this._focusInHandler);
    if (this._focusOutHandler) document.removeEventListener('focusout', this._focusOutHandler);
    this._focusInHandler = null; this._focusOutHandler = null;
  },
};

// ============================================================
// Section 14: Public API
// ============================================================

async function start() {
  if (state.active || state._starting) return;   // (#45) double-start guard
  state._starting = true;
  state.active = true;

  UIManager.init();
  DebugWidget.init();
  DebugWidget.setStatus('inactive', 'Starting…');
  DebugWidget.setMode('navigate');

  const ok = VoiceController.init();
  if (!ok) { state.active = false; state._starting = false; return; }

  // Only show persistent "loading" notification on FIRST load this browser session.
  const firstLoad = !sessionStorage.getItem(CONFIG.SESSION_KEY);

  ModelManager.load(msg => {
    if (firstLoad) UIManager.notifyPersist(msg);
    else DebugWidget.setModel('loading');
  }).then(async (loaded) => {
    if (!state.active) return;   // (#1, #30) stop() called before model finished loading
    await ModelManager.scanPage();
    if (!state.active) return;   // (#13) stop() called during async scan
    UIManager.notify(
      loaded ? 'Voice control ready — say an element name or start speaking.'
             : 'Voice control ready (basic mode).',
      4000
    );
    DebugWidget.setModel(loaded ? 'use' : 'trigram');
  });

  VoiceController._startRec();
  VoiceController.attachFocusListeners();
  ModelManager.startObserving();
  state._starting = false;   // synchronous init complete
}

function stop() {
  if (!state.active) return;
  state.active = false;

  VoiceController._stopRec();
  VoiceController.detachFocusListeners();
  ModelManager.stopObserving();
  utils.clearAllTimers();

  if (state.mode === 'overlay') NavigationHandler.cancelOverlay();
  if (state.mode === 'dictate') DictationHandler.exit(false);
  if (state.mode === 'select') SelectHandler.exit(false);

  UIManager.closeElementList();
  UIManager.destroy();
  DebugWidget.destroy();
  _emitStatus('inactive');

  // Clear embedding cache (vectors are session-specific, free memory on stop)
  ModelManager._embedCache.clear();

  // Reset all state including new fields (#16, #29, #45)
  state.mode                    = 'navigate';
  state.elementVectors          = [];
  state.scanDirty               = true;
  state._scanning               = false;
  state._starting               = false;
  state._vtFocusing             = false;
  state._scanDebounceId         = null;
  state.dictationField          = null;
  state.dictationPrev           = '';
  state.dictationOrigPlaceholder = '';
  state.selectOptions           = [];
  state.overlayMatches          = [];
  state.overlayPage             = 0;
  state.overlayTimeoutId        = null;
  state.overlayJustHandled      = false;
  state.lastAction              = null;
  state.recognition             = null;
  state.recognitionActive       = false;
  state.interimText             = '';
}

// ============================================================
// SPA navigation: reinitialize after any framework's page transition
// ============================================================
// During a body swap (Livewire, Turbo, React Router, etc.) two things break:
//   1. The SpeechRecognition instance gets aborted by the browser
//   2. VoiceTracker's DOM elements (debug widget, notifications, overlays) are removed
// Fix: detect navigation, then do stop() + start(). stop() cleans up broken state.
// start() skips the CDN/model load entirely (state.modelReady survives stop()) and
// just reinitializes recognition + DOM on the new page. ~instant.
(function () {
  let _pending = null;

  function _onNav() {
    if (!state.active) return;          // voice wasn't running — nothing to do
    if (_pending) return;               // already scheduled — deduplicate multiple events
    _pending = window.setTimeout(() => {
      _pending = null;
      if (!state.active) return;        // user stopped manually during the delay
      stop();
      start();
    }, 100);
  }

  // Livewire / Alpine (fires after DOM is ready — preferred timing)
  document.addEventListener('livewire:navigated', _onNav);

  // Turbo / Hotwire
  document.addEventListener('turbo:load', _onNav);

  // Generic History API — catches React Router, Vue Router, Next.js, etc.
  const _push    = history.pushState.bind(history);
  const _replace = history.replaceState.bind(history);
  history.pushState    = function () { _push(...arguments);    _onNav(); };
  history.replaceState = function () { _replace(...arguments); _onNav(); };
  window.addEventListener('popstate', _onNav);
}());

// ============================================================
// Expose on window so blade widget can call start() / stop()
// ============================================================
window.VoiceTracking       = { start, stop };
window.VoiceTrackingLoaded = true;







