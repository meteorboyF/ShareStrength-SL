/* ============================================================
 * annyang v2.6.1 — bundled inline (no CDN required)
 * https://www.TalAter.com/annyang/ | License: MIT
 * ============================================================ */
"use strict";var _typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e};
//! annyang
//! version : 2.6.1
//! author  : Tal Ater @TalAter
//! license : MIT
//! https://www.TalAter.com/annyang/
!function(e,n){"function"==typeof define&&define.amd?define([],function(){return e.annyang=n(e)}):"object"===("undefined"==typeof module?"undefined":_typeof(module))&&module.exports?module.exports=n(e):e.annyang=n(e)}("undefined"!=typeof window?window:void 0,function(r,i){var t,o=r.SpeechRecognition||r.webkitSpeechRecognition||r.mozSpeechRecognition||r.msSpeechRecognition||r.oSpeechRecognition;if(!o)return null;var a,c,s=[],u={start:[],error:[],end:[],soundstart:[],result:[],resultMatch:[],resultNoMatch:[],errorNetwork:[],errorPermissionBlocked:[],errorPermissionDenied:[]},f=0,l=0,d=!1,p="font-weight: bold; color: #00f;",g=!1,m=!1,h=/\s*\((.*?)\)\s*/g,y=/(\(\?:[^)]+\))\?/g,b=/(\(\?)?:\w+/g,v=/\*\w+/g,w=/[\-{}\[\]+?.,\\\^$|#]/g,S=function(e){for(var n=arguments.length,t=Array(1<n?n-1:0),o=1;o<n;o++)t[o-1]=arguments[o];e.forEach(function(e){e.callback.apply(e.context,t)})},e=function(){return a!==i},k=function(e,n){-1!==e.indexOf("%c")||n?console.log(e,n||p):console.log(e)},x=function(){e()||t.init({},!1)},R=function(e,n,t){s.push({command:e,callback:n,originalPhrase:t}),d&&k("Command successfully loaded: %c"+t,p)},P=function(e){var n;S(u.result,e);for(var t=0;t<e.length;t++){n=e[t].trim(),d&&k("Speech recognized: %c"+n,p);for(var o=0,r=s.length;o<r;o++){var i=s[o],a=i.command.exec(n);if(a){var c=a.slice(1);return d&&(k("command matched: %c"+i.originalPhrase,p),c.length&&k("with parameters",c)),i.callback.apply(this,c),void S(u.resultMatch,n,i.originalPhrase,e)}}}S(u.resultNoMatch,e)};return t={init:function(e){var n=!(1<arguments.length&&arguments[1]!==i)||arguments[1];a&&a.abort&&a.abort(),(a=new o).maxAlternatives=5,a.continuous="http:"===r.location.protocol,a.lang="en-US",a.onstart=function(){m=!0,S(u.start)},a.onsoundstart=function(){S(u.soundstart)},a.onerror=function(e){switch(S(u.error,e),e.error){case"network":S(u.errorNetwork,e);break;case"not-allowed":case"service-not-allowed":c=!1,(new Date).getTime()-f<200?S(u.errorPermissionBlocked,e):S(u.errorPermissionDenied,e)}},a.onend=function(){if(m=!1,S(u.end),c){var e=(new Date).getTime()-f;(l+=1)%10==0&&d&&k("Speech Recognition is repeatedly stopping and starting. See http://is.gd/annyang_restarts for tips."),e<1e3?setTimeout(function(){t.start({paused:g})},1e3-e):t.start({paused:g})}},a.onresult=function(e){if(g)return d&&k("Speech heard, but annyang is paused"),!1;for(var n=e.results[e.resultIndex],t=[],o=0;o<n.length;o++)t[o]=n[o].transcript;P(t)},n&&(s=[]),e.length&&this.addCommands(e)},start:function(e){x(),g=(e=e||{}).paused!==i&&!!e.paused,c=e.autoRestart===i||!!e.autoRestart,e.continuous!==i&&(a.continuous=!!e.continuous),f=(new Date).getTime();try{a.start()}catch(e){d&&k(e.message)}},abort:function(){c=!1,l=0,e()&&a.abort()},pause:function(){g=!0},resume:function(){t.start()},debug:function(){var e=!(0<arguments.length&&arguments[0]!==i)||arguments[0];d=!!e},setLanguage:function(e){x(),a.lang=e},addCommands:function(e){var n,t;for(var o in x(),e)if(e.hasOwnProperty(o))if("function"==typeof(n=r[e[o]]||e[o]))R((t=(t=o).replace(w,"\\$&").replace(h,"(?:$1)?").replace(b,function(e,n){return n?e:"([^\\s]+)"}).replace(v,"(.*?)").replace(y,"\\s*$1?\\s*"),new RegExp("^"+t+"$","i")),n,o);else{if(!("object"===(void 0===n?"undefined":_typeof(n))&&n.regexp instanceof RegExp)){d&&k("Can not register command: %c"+o,p);continue}R(new RegExp(n.regexp.source,"i"),n.callback,o)}},removeCommands:function(t){t===i?s=[]:(t=Array.isArray(t)?t:[t],s=s.filter(function(e){for(var n=0;n<t.length;n++)if(t[n]===e.originalPhrase)return!1;return!0}))},addCallback:function(e,n,t){var o=r[n]||n;"function"==typeof o&&u[e]!==i&&u[e].push({callback:o,context:t||this})},removeCallback:function(e,n){var t=function(e){return e.callback!==n};for(var o in u)u.hasOwnProperty(o)&&(e!==i&&e!==o||(u[o]=n===i?[]:u[o].filter(t)))},isListening:function(){return m&&!g},getSpeechRecognizer:function(){return a},trigger:function(e){t.isListening()?(Array.isArray(e)||(e=[e]),P(e)):d&&k(m?"Speech heard, but annyang is paused":"Cannot trigger while annyang is aborted")}}});

/**
 * VoiceTracking.js - Secure & Robust Voice Recognition Module with Integrated Dictation
 *
 * Version: 4.0.0 - Enhanced with Voice Dictation for Text Input
 * License: MIT
 * Enhanced: Integrated voice dictation, preview system, field navigation, and input-type intelligence
 */

  'use strict';

  // Prevent multiple initializations with proper cleanup
  if (window.VoiceTrackingLoaded) {
    console.warn('VoiceTracking.js already loaded. Cleaning up previous instance.');
    if (window.VoiceTracking && typeof window.VoiceTracking.destroy === 'function') {
      window.VoiceTracking.destroy();
    }
  }

  // Secure namespace protection
  const NAMESPACE_KEY = Symbol('VoiceTracking');
  window[NAMESPACE_KEY] = true;

  // Enhanced configuration with dictation settings
  const CONFIG = Object.freeze({
    enabled: false,
    maxDistance: 0.7,
    ignoreWords: Object.freeze(['click', 'press', 'select', 'open', 'go to', 'navigate', 'the', 'on', 'to', 'button']),
    scanInterval: 1000,
    autoRecover: true,
    errorRetryDelay: 1000,
    maxErrorRetries: 3,
    useEnhancedMatching: true,
    statusDisplayTimeout: 1500,
    forceBrowserCompatibility: true,
    autoStart: false,
    widgetPosition: 'top-right',
    widgetSize: 'small',
    showVisualFeedback: true,
    highlightColor: 'rgba(255, 215, 0, 0.3)',
    highlightBorder: '2px solid orange',
    commandCooldown: 500,
    persistSettings: true,
    maxElementsToShow: 50,
    maxElementsToScan: 1000,
    debounceDelay: 200,
    cacheTimeout: 3000,
    // Enhanced timeout settings
    duplicateOverlayTimeout: 20000, // 20 seconds for letter selection
    duplicateErrorTimeout: 3000,
    // Visual feedback options
    showCountdown: true,
    pulseOverlaysWhileWaiting: true,
    extendTimeOnSpeech: true, // Extend timeout if speech detected but not recognized
    autoExtendTimeout: 15000, // Additional 15 seconds when speech detected
    // Performance and safety options
    maxInputLength: 100,
    debugLevel: 3, // 0=none, 1=error, 2=warn, 3=info, 4=debug
    maxConsecutiveErrors: 5,
    elementScanBatchSize: 100,
    intersectionObserverEnabled: true,
    // NEW: Dictation settings
    dictationEnabled: true,
    autoStartDictation: true,
    dictationPreviewColor: '#4c6ef5',
    dictationPreviewOpacity: 0.7,
    commandPrefix: 'command',
    commandPrefixTimeout: 7000,
    previewAcceptTimeout: 10000,
    dictationHistoryLimit: -1, // -1 = unlimited
    sleepModeTimeout: 600000, // 10 minutes
    recognizerSwitchDelay: 200,
    smartPunctuation: true,
    inputTypeDetection: true,
    showDictationBorder: true,
    dictationBorderColor: '#f44336',
    autoAcceptOnContinue: true,
    showRecognizerSwitchIndicator: true
  });

  // Mutable configuration (deep clone for safety)
  let activeConfig = JSON.parse(JSON.stringify(CONFIG));

  // Secure clickable selectors with validation
  const CLICKABLE_SELECTORS = Object.freeze([
    'button',
    'a[href]',
    'input[type="button"]',
    'input[type="submit"]',
    'input[type="reset"]',
    '[role="button"]',
    'input[type="checkbox"]',
    'input[type="radio"]',
    'select',
    'textarea',
    'input[type="text"]',
    'input[type="email"]',
    'input[type="password"]',
    'input[type="number"]',
    'input[type="search"]',
    'input[type="tel"]',
    'input[type="url"]',
    'input[type="date"]',
    'input[type="time"]',
    'input[type="file"]',
    '[onclick]',
    '[tabindex]:not([tabindex="-1"])',
    '.btn',
    '.button',
    '.clickable',
    '[data-clickable]'
  ]);

  // Text-capable input selectors
  const TEXT_INPUT_SELECTORS = Object.freeze([
    'textarea',
    'input[type="text"]',
    'input[type="email"]',
    'input[type="password"]',
    'input[type="search"]',
    'input[type="tel"]',
    'input[type="url"]',
    'input[type="number"]',
    '[contenteditable="true"]'
  ]);

  // Dictation commands
  const DICTATION_COMMANDS = Object.freeze({
    'delete word': 'deleteLastWord',
    'delete last': 'deleteLastWord',
    'clear all': 'clearInput',
    'start over': 'clearInput',
    'undo': 'undoLastAction',
    'accept': 'acceptPreview',
    'reject': 'rejectPreview',
    'next field': 'showFieldNavigation',
    'previous field': 'showFieldNavigation',
    'done writing': 'stopDictation',
    'stop writing': 'stopDictation',
    'new line': 'insertNewLine',
    'go back': 'continueDictation'
  });

  // Enhanced state management with dictation
  let state = {
    listeningState: 'inactive',
    clickableElements: [],
    lastScanTime: 0,
    elementHighlights: new Map(),
    highlightTimeout: null,
    errorRetryCount: 0,
    autoRecoveryTimer: null,
    isInitialized: false,
    detectedBrowser: 'unknown',
    microphoneAccessGranted: false,
    hasSpeechRecognitionSupport: false,
    isProcessingCommand: false,
    lastCommandTime: 0,
    elements: {},
    eventListeners: new Map(),
    timers: new Set(),
    cache: new Map(),
    lastCacheTime: 0,
    mutationObserver: null,
    intersectionObserver: null,
    isDragging: false,
    dragOffset: { x: 0, y: 0 },
    libraryLoadAttempts: 0,
    maxLibraryLoadAttempts: 3,
    commandCounter: 0,
    lastClickedElement: null,
    numberedOverlays: new Map(),
    numberedElements: [],
    numberedOverlayTimeout: null,
    currentDuplicateBase: null,
    speechRecognitionRestartCount: 0,
    lastSpeechRecognitionRestart: 0,
    // Visual feedback elements
    listeningIndicator: null,
    countdownElement: null,
    countdownInterval: null,
    // Enhanced state management
    isLoadingLibrary: false,
    loadingLibraryPromise: null,
    consecutiveErrors: 0,
    lastErrorTime: 0,
    documentObserverActive: false,
    visibleElements: new Set(),
    localStorageAvailable: null,
    // NEW: Dictation state
    dictationState: 'inactive', // inactive, listening, preview, command-waiting
    dictationRecognizer: null,
    currentInputElement: null,
    previewText: '',
    previewElement: null,
    dictationHistory: [],
    dictationHistoryIndex: -1,
    isInCommandMode: false,
    commandModeTimeout: null,
    lastDictationActivity: Date.now(),
    sleepModeTimer: null,
    isSwitchingRecognizers: false,
    switchIndicatorElement: null,
    acceptRejectTimeout: null,
    fieldNavigationActive: false,
    fieldNavigationElements: [],
    dictationIndicatorElement: null
  };

  // Enhanced utility functions for security and validation
  const utils = {
    // Enhanced debug logging with levels
    debugLog: function(level, ...args) {
      const levels = { none: 0, error: 1, warn: 2, info: 3, debug: 4 };
      const currentLevel = levels[activeConfig.debugLevel] || activeConfig.debugLevel || 3;
      
      if (level <= currentLevel) {
        const prefix = level <= 1 ? 'ERROR' : level <= 2 ? 'WARN' : level <= 3 ? 'INFO' : 'DEBUG';
        console.log(`VoiceTracking [${prefix}]:`, ...args);
      }
    },

    // Enhanced text sanitization
    sanitizeText: function(text) {
      if (typeof text !== 'string') return '';
      
      // Truncate overly long inputs
      if (text.length > activeConfig.maxInputLength) {
        text = text.substring(0, activeConfig.maxInputLength);
      }
      
      return text.replace(/[<>&"'`]/g, function(match) {
        const escapeMap = {
          '<': '&lt;',
          '>': '&gt;',
          '&': '&amp;',
          '"': '&quot;',
          "'": '&#x27;',
          '`': '&#x60;'
        };
        return escapeMap[match];
      });
    },

    // Enhanced input validation
    validateInput: function(input, maxLength = activeConfig.maxInputLength) {
      if (typeof input !== 'string') return false;
      if (input.length === 0 || input.length > maxLength) return false;
      
      // Check for suspicious patterns
      const suspiciousPatterns = [
        /<script/i,
        /javascript:/i,
        /data:text\/html/i,
        /vbscript:/i
      ];
      
      return !suspiciousPatterns.some(pattern => pattern.test(input));
    },

    validateConfig: function(config) {
      if (!config || typeof config !== 'object') return false;
      
      const validators = {
        enabled: (v) => typeof v === 'boolean',
        maxDistance: (v) => typeof v === 'number' && v >= 0 && v <= 1,
        scanInterval: (v) => typeof v === 'number' && v >= 100,
        maxElementsToScan: (v) => typeof v === 'number' && v > 0 && v <= 10000,
        commandCooldown: (v) => typeof v === 'number' && v >= 0,
        maxInputLength: (v) => typeof v === 'number' && v > 0 && v <= 1000,
        debugLevel: (v) => typeof v === 'number' && v >= 0 && v <= 4
      };

      for (const [key, validator] of Object.entries(validators)) {
        if (key in config && !validator(config[key])) {
          utils.debugLog(2, `Invalid config value for ${key}:`, config[key]);
          return false;
        }
      }
      return true;
    },

    // Enhanced debounce with better cleanup
    debounce: function(func, wait) {
      let timeout;
      return function executedFunction(...args) {
        const later = () => {
          utils.clearTimeout(timeout);
          func(...args);
        };
        utils.clearTimeout(timeout);
        timeout = utils.setTimeout(later, wait);
      };
    },

    // Enhanced throttle with better cleanup
    throttle: function(func, limit) {
      let inThrottle;
      return function executedFunction(...args) {
        if (!inThrottle) {
          func.apply(this, args);
          inThrottle = true;
          const timer = utils.setTimeout(() => inThrottle = false, limit);
        }
      };
    },

    // Enhanced event listener management
    addEventListener: function(element, event, handler, options = {}) {
      try {
        if (!element || typeof element.addEventListener !== 'function') {
          utils.debugLog(2, 'Invalid element for addEventListener:', element);
          return false;
        }
        
        element.addEventListener(event, handler, options);
        
        if (!state.eventListeners.has(element)) {
          state.eventListeners.set(element, new Map());
        }
        state.eventListeners.get(element).set(event, { handler, options });
        return true;
      } catch (e) {
        utils.debugLog(1, 'Error adding event listener:', e);
        return false;
      }
    },

    // Enhanced timer management with proper cleanup
    setTimeout: function(callback, delay) {
      const timer = setTimeout(() => {
        state.timers.delete(timer);
        try {
          callback();
        } catch (e) {
          utils.debugLog(1, 'Timer callback error:', e);
        }
      }, delay);
      state.timers.add(timer);
      return timer;
    },

    // Proper clearTimeout wrapper
    clearTimeout: function(timer) {
      if (timer) {
        clearTimeout(timer);
        state.timers.delete(timer);
      }
    },

    // Enhanced localStorage availability check
    isLocalStorageAvailable: function() {
      if (state.localStorageAvailable !== null) {
        return state.localStorageAvailable;
      }
      
      try {
        const test = '__voicetracking_test__';
        localStorage.setItem(test, test);
        localStorage.removeItem(test);
        state.localStorageAvailable = true;
        return true;
      } catch (e) {
        state.localStorageAvailable = false;
        utils.debugLog(2, 'localStorage not available:', e.message);
        return false;
      }
    },

    // Enhanced DOM safety check
    isDOMReady: function() {
      return document && document.body && document.head;
    },

    // Enhanced feature detection
    detectFeatures: function() {
      const features = {
        speechRecognition: !!(window.SpeechRecognition || window.webkitSpeechRecognition),
        mediaDevices: !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia),
        secureContext: window.isSecureContext || location.protocol === 'https:' || 
                       location.hostname === 'localhost' || location.hostname === '127.0.0.1',
        touchEvents: 'ontouchstart' in window || navigator.maxTouchPoints > 0,
        intersectionObserver: 'IntersectionObserver' in window,
        mutationObserver: 'MutationObserver' in window,
        localStorage: utils.isLocalStorageAvailable(),
        performanceObserver: 'PerformanceObserver' in window
      };

      if (features.speechRecognition) {
        window.SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        state.hasSpeechRecognitionSupport = true;
      }

      return features;
    },

    // Enhanced command state reset
    resetCommandState: function() {
      state.isProcessingCommand = false;
      clearLeteredOverlays();
      
      // Clear any stuck timers
      if (state.numberedOverlayTimeout) {
        utils.clearTimeout(state.numberedOverlayTimeout);
        state.numberedOverlayTimeout = null;
      }
      
      utils.debugLog(3, 'Command state forcefully reset');
    },

    // Enhanced error tracking
    trackError: function(error, context = '') {
      const currentTime = Date.now();
      
      // Prevent error spam
      if (currentTime - state.lastErrorTime < 1000) {
        return;
      }
      
      state.consecutiveErrors++;
      state.lastErrorTime = currentTime;
      
      utils.debugLog(1, `Error in ${context}:`, error);
      
      if (state.consecutiveErrors >= activeConfig.maxConsecutiveErrors) {
        utils.debugLog(1, 'Too many consecutive errors, forcing recovery');
        utils.setTimeout(() => {
          if (window.VoiceTracking && typeof window.VoiceTracking.emergencyRecovery === 'function') {
            window.VoiceTracking.emergencyRecovery();
          }
        }, 1000);
      }
    },

    // Reset error tracking on success
    resetErrorTracking: function() {
      state.consecutiveErrors = 0;
      state.lastErrorTime = 0;
    }
  };

  // Enhanced browser compatibility check
  function ensureBrowserCompatibility() {
    const features = utils.detectFeatures();
    state.hasSpeechRecognitionSupport = features.speechRecognition;

    if (!features.secureContext) {
      showNotification('Voice recognition requires HTTPS (or localhost)', 5000, 'warning');
      utils.debugLog(2, 'Insecure context detected.');
    }

    if (!features.speechRecognition) {
      showNotification('Speech Recognition not supported in this browser', 5000, 'error');
    }

    if (!features.mediaDevices) {
      showNotification('Microphone access not supported', 5000, 'error');
    }

    if (!features.localStorage) {
      utils.debugLog(2, 'localStorage not available, settings will not persist');
    }

    return features;
  }

  // Enhanced CSS injection with dictation styles
  function createStyles() {
    if (document.getElementById('voicetracking-styles')) return;

    if (!utils.isDOMReady()) {
      utils.debugLog(2, 'DOM not ready for style injection');
      return false;
    }

    const style = document.createElement('style');
    style.id = 'voicetracking-styles';
    
    // Enhanced CSP compliance
    if (window.voiceTrackingNonce) {
      style.nonce = window.voiceTrackingNonce;
    }

    style.textContent = `
      .voicetracking-widget {
        position: fixed;
        z-index: 2147483647;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        border: 2px solid rgba(255,255,255,0.2);
        background: rgba(0,0,0,0.8);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
        cursor: grab;
        user-select: none;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        touch-action: none;
      }
      .voicetracking-widget.small { width: 480px; height: 40px; }
      .voicetracking-widget.medium { width: 220px; height: 80px; }
      .voicetracking-widget.large { width: 260px; height: 100px; }
      .voicetracking-widget.top-right { top: 20px; right: 20px; }
      .voicetracking-widget.top-left { top: 20px; left: 20px; }
      .voicetracking-widget.bottom-right { bottom: 20px; right: 20px; }
      .voicetracking-widget.bottom-left { bottom: 20px; left: 20px; }

      .voicetracking-status {
        position: absolute;
        top: 8px;
        left: 8px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #666;
        transition: all 0.3s ease;
        box-shadow: 0 0 8px rgba(0,0,0,0.5);
      }
      .voicetracking-status.inactive { background: #666; }
      .voicetracking-status.listening {
        background: #4caf50;
        box-shadow: 0 0 15px rgba(76,175,80,0.5);
        animation: voicetracking-pulse 1.5s infinite ease-in-out;
      }
      .voicetracking-status.processing {
        background: #ff9800;
        animation: voicetracking-spin 1s infinite linear;
      }
      .voicetracking-status.recognized {
        background: #2196f3;
        transform: scale(1.2);
      }
      .voicetracking-status.error {
        background: #f44336;
        animation: voicetracking-flash 0.5s infinite;
      }
      .voicetracking-status.dictating {
        background: #9c27b0;
        box-shadow: 0 0 20px rgba(156,39,176,0.8);
        animation: voicetracking-dictation-pulse 1s infinite ease-in-out;
      }

      @keyframes voicetracking-pulse {
        0% { transform: scale(0.95); opacity: 0.7; }
        50% { transform: scale(1.05); opacity: 0.9; }
        100% { transform: scale(0.95); opacity: 0.7; }
      }

      @keyframes voicetracking-spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
      }

      @keyframes voicetracking-flash {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
      }

      @keyframes voicetracking-dictation-pulse {
        0% { transform: scale(1); box-shadow: 0 0 20px rgba(156,39,176,0.8); }
        50% { transform: scale(1.1); box-shadow: 0 0 30px rgba(156,39,176,1); }
        100% { transform: scale(1); box-shadow: 0 0 20px rgba(156,39,176,0.8); }
      }

      .voicetracking-content {
        padding: 8px 12px;
        color: white;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
      }

      .voicetracking-text {
        flex: 1;
        min-width: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .voicetracking-controls {
        display: flex;
        gap: 4px;
        opacity: 0;
        transition: opacity 0.3s ease;
      }
      .voicetracking-widget:hover .voicetracking-controls,
      .voicetracking-widget:focus-within .voicetracking-controls {
        opacity: 1;
      }

      .voicetracking-btn {
        padding: 2px 6px;
        font-size: 10px;
        border: none;
        border-radius: 3px;
        background: #4c6ef5;
        color: white;
        cursor: pointer;
        transition: background 0.2s;
      }
      .voicetracking-btn:hover,
      .voicetracking-btn:focus { 
        background: #364fc7; 
        outline: 2px solid rgba(255,255,255,0.5);
      }
      .voicetracking-btn:disabled { 
        background: #666; 
        cursor: not-allowed; 
      }

      .voicetracking-feedback {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0,0,0,0.9);
        color: white;
        padding: 16px 24px;
        border-radius: 8px;
        font-family: inherit;
        font-size: 16px;
        z-index: 2147483646;
        transition: opacity 0.3s ease;
        box-shadow: 0 8px 32px rgba(0,0,0,0.5);
        pointer-events: none;
        opacity: 0;
        max-width: 300px;
        text-align: center;
        word-wrap: break-word;
      }

      .voicetracking-feedback.show {
        opacity: 1;
      }

      .voicetracking-notification {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,0,0,0.9);
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        font-family: inherit;
        font-size: 14px;
        z-index: 2147483645;
        transition: opacity 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        max-width: 400px;
        text-align: center;
        word-wrap: break-word;
      }

      .voicetracking-notification.warning {
        background: rgba(255, 152, 0, 0.9);
        color: black;
      }

      .voicetracking-notification.error {
        background: rgba(244, 67, 54, 0.9);
      }

      .voicetracking-highlight {
        position: fixed;
        background: ${activeConfig.highlightColor};
        border: ${activeConfig.highlightBorder};
        border-radius: 4px;
        z-index: 2147483644;
        pointer-events: none;
        transition: opacity 0.5s ease;
        box-shadow: 0 0 12px rgba(255,165,0,0.6);
      }

      .voicetracking-element-label {
        position: fixed;
        background: rgba(0,0,0,0.9);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-family: inherit;
        z-index: 2147483643;
        pointer-events: none;
        max-width: 200px;
        word-wrap: break-word;
      }

      .voicetracking-numbered-overlay {
        position: fixed;
        background: rgba(33, 150, 243, 0.9);
        color: white;
        border: 2px solid #2196f3;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: inherit;
        font-size: 18px;
        font-weight: bold;
        z-index: 2147483642;
        pointer-events: none;
        box-shadow: 0 4px 16px rgba(33, 150, 243, 0.6);
        animation: voicetracking-number-appear 0.4s ease-out;
      }

      @keyframes voicetracking-number-appear {
        0% { transform: scale(0) rotate(180deg); opacity: 0; }
        50% { transform: scale(1.2) rotate(0deg); opacity: 0.8; }
        100% { transform: scale(1) rotate(0deg); opacity: 1; }
      }

      /* Enhanced lettered overlay with waiting animation */
      .voicetracking-numbered-overlay.waiting {
        animation: voicetracking-waiting-pulse 2s infinite ease-in-out;
        box-shadow: 0 4px 20px rgba(33, 150, 243, 0.8);
        border-width: 3px;
      }

      @keyframes voicetracking-waiting-pulse {
        0% { 
          transform: scale(1); 
          box-shadow: 0 4px 20px rgba(33, 150, 243, 0.8);
        }
        50% { 
          transform: scale(1.1); 
          box-shadow: 0 6px 30px rgba(33, 150, 243, 1);
        }
        100% { 
          transform: scale(1); 
          box-shadow: 0 4px 20px rgba(33, 150, 243, 0.8);
        }
      }

      /* Countdown timer display */
      .voicetracking-countdown {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(33, 150, 243, 0.95);
        color: white;
        padding: 20px 30px;
        border-radius: 50%;
        font-family: inherit;
        font-size: 28px;
        font-weight: bold;
        z-index: 2147483648;
        pointer-events: none;
        text-align: center;
        min-width: 90px;
        min-height: 90px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 32px rgba(33, 150, 243, 0.6);
        animation: voicetracking-countdown-pulse 1s infinite ease-in-out;
      }

      @keyframes voicetracking-countdown-pulse {
        0%, 100% { transform: translate(-50%, -50%) scale(1); }
        50% { transform: translate(-50%, -50%) scale(1.05); }
      }

      .voicetracking-listening-indicator {
        position: fixed;
        top: 20px;
        right: 20px;
        background: rgba(76, 175, 80, 0.9);
        color: white;
        padding: 12px 18px;
        border-radius: 25px;
        font-family: inherit;
        font-size: 14px;
        font-weight: bold;
        z-index: 2147483647;
        pointer-events: none;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: voicetracking-listening-glow 1.5s infinite ease-in-out;
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
      }

      @keyframes voicetracking-listening-glow {
        0%, 100% { box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4); }
        50% { box-shadow: 0 6px 20px rgba(76, 175, 80, 0.8); }
      }

      .voicetracking-mic-icon {
        width: 14px;
        height: 14px;
        background: currentColor;
        border-radius: 50%;
        position: relative;
      }

      .voicetracking-mic-icon::after {
        content: '';
        position: absolute;
        top: -4px;
        left: -4px;
        right: -4px;
        bottom: -4px;
        border: 2px solid currentColor;
        border-radius: 50%;
        animation: voicetracking-mic-pulse 1.2s infinite ease-in-out;
      }

      @keyframes voicetracking-mic-pulse {
        0% { transform: scale(0.8); opacity: 1; }
        100% { transform: scale(1.4); opacity: 0; }
      }

      /* NEW: Dictation-specific styles */
      .voicetracking-dictating {
        border: 3px solid ${activeConfig.dictationBorderColor} !important;
        box-shadow: 0 0 20px rgba(244,67,54,0.5) !important;
        transition: all 0.3s ease;
      }

      .voicetracking-preview-text {
        color: ${activeConfig.dictationPreviewColor};
        opacity: ${activeConfig.dictationPreviewOpacity};
        font-style: italic;
      }

      .voicetracking-dictation-indicator {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: rgba(156,39,176,0.9);
        color: white;
        padding: 10px 16px;
        border-radius: 20px;
        font-family: inherit;
        font-size: 13px;
        z-index: 2147483641;
        pointer-events: none;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(156,39,176,0.4);
        animation: voicetracking-dictation-glow 1.5s infinite ease-in-out;
      }

      @keyframes voicetracking-dictation-glow {
        0%, 100% { box-shadow: 0 4px 12px rgba(156,39,176,0.4); }
        50% { box-shadow: 0 6px 20px rgba(156,39,176,0.8); }
      }

      .voicetracking-switch-indicator {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(255,152,0,0.95);
        color: white;
        padding: 16px 24px;
        border-radius: 8px;
        font-family: inherit;
        font-size: 14px;
        z-index: 2147483648;
        pointer-events: none;
        text-align: center;
        box-shadow: 0 8px 24px rgba(255,152,0,0.6);
      }

      .voicetracking-accept-reject {
        position: fixed;
        background: rgba(0,0,0,0.95);
        border: 2px solid #4c6ef5;
        border-radius: 8px;
        padding: 12px;
        z-index: 2147483642;
        display: flex;
        gap: 8px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.6);
      }

      .voicetracking-accept-reject button {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        font-family: inherit;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.2s ease;
      }

      .voicetracking-accept-reject .accept {
        background: #4caf50;
        color: white;
      }

      .voicetracking-accept-reject .accept:hover {
        background: #45a049;
      }

      .voicetracking-accept-reject .reject {
        background: #f44336;
        color: white;
      }

      .voicetracking-accept-reject .reject:hover {
        background: #da190b;
      }

      .voicetracking-sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
      }
    `;
    
    try {
      document.head.appendChild(style);
      return true;
    } catch (e) {
      utils.trackError(e, 'createStyles');
      return false;
    }
  }

  // Enhanced UI creation with dictation controls
  function createUI() {
    if (!utils.isDOMReady()) {
      utils.debugLog(2, 'DOM not ready for UI creation');
      return false;
    }

    try {
      const widget = document.createElement('div');
      widget.className = `voicetracking-widget ${activeConfig.widgetSize} ${activeConfig.widgetPosition}`;
      widget.setAttribute('role', 'application');
      widget.setAttribute('aria-label', 'Voice Control Widget');
      widget.setAttribute('tabindex', '0');

      const status = document.createElement('div');
      status.className = 'voicetracking-status inactive';
      status.setAttribute('aria-live', 'polite');
      status.setAttribute('aria-label', 'Voice control status');

      const content = document.createElement('div');
      content.className = 'voicetracking-content';

      const text = document.createElement('div');
      text.className = 'voicetracking-text';
      text.textContent = 'Voice Control';
      text.setAttribute('aria-live', 'polite');

      const controls = document.createElement('div');
      controls.className = 'voicetracking-controls';
      controls.setAttribute('role', 'group');
      controls.setAttribute('aria-label', 'Voice control buttons');

      const toggleBtn = document.createElement('button');
      toggleBtn.className = 'voicetracking-btn';
      toggleBtn.textContent = 'Start';
      toggleBtn.setAttribute('aria-label', 'Toggle voice control');
      toggleBtn.onclick = toggle;

      const showBtn = document.createElement('button');
      showBtn.className = 'voicetracking-btn';
      showBtn.textContent = 'Show';
      showBtn.setAttribute('aria-label', 'Show clickable elements');
      showBtn.onclick = showClickableElements;

      const dictateBtn = document.createElement('button');
      dictateBtn.className = 'voicetracking-btn';
      dictateBtn.textContent = 'Dictate';
      dictateBtn.setAttribute('aria-label', 'Toggle dictation mode');
      dictateBtn.onclick = toggleDictation;

      const debugBtn = document.createElement('button');
      debugBtn.className = 'voicetracking-btn';
      debugBtn.textContent = 'Reset';
      debugBtn.setAttribute('aria-label', 'Reset command state');
      debugBtn.onclick = () => {
        utils.resetCommandState();
        resetDictationState();
        state.cache.clear();
        scanPageForClickableElements();
        showNotification('State reset', 1000);
      };

      controls.appendChild(toggleBtn);
      controls.appendChild(showBtn);
      controls.appendChild(dictateBtn);
      controls.appendChild(debugBtn);
      content.appendChild(text);
      content.appendChild(controls);
      widget.appendChild(status);
      widget.appendChild(content);

      const feedback = document.createElement('div');
      feedback.className = 'voicetracking-feedback';
      feedback.setAttribute('role', 'status');
      feedback.setAttribute('aria-live', 'assertive');

      const srAnnouncement = document.createElement('div');
      srAnnouncement.className = 'voicetracking-sr-only';
      srAnnouncement.setAttribute('aria-live', 'assertive');
      srAnnouncement.setAttribute('role', 'status');

      makeDraggable(widget);

      state.elements = { 
        widget, 
        status, 
        text, 
        controls, 
        toggleBtn, 
        showBtn,
        dictateBtn,
        debugBtn,
        feedback,
        srAnnouncement
      };

      document.body.appendChild(widget);
      document.body.appendChild(feedback);
      document.body.appendChild(srAnnouncement);
      
      return true;
    } catch (e) {
      utils.trackError(e, 'createUI');
      return false;
    }
  }

  // Enhanced draggable functionality
  function makeDraggable(element) {
    if (!element) return false;

    let startX = 0, startY = 0, initialX = 0, initialY = 0;

    function handleStart(e) {
      try {
        const target = e.target || e.touches?.[0]?.target;
        if (target && (target === element || target.closest('.voicetracking-content')) && 
            !target.closest('.voicetracking-controls')) {
          
          state.isDragging = true;
          
          const clientX = e.clientX || e.touches?.[0]?.clientX || 0;
          const clientY = e.clientY || e.touches?.[0]?.clientY || 0;
          
          startX = clientX;
          startY = clientY;
          initialX = element.offsetLeft;
          initialY = element.offsetTop;
          
          element.style.transition = 'none';
          element.style.cursor = 'grabbing';
          
          e.preventDefault();
        }
      } catch (error) {
        utils.trackError(error, 'makeDraggable.handleStart');
      }
    }

    function handleMove(e) {
      if (!state.isDragging) return;
      
      try {
        e.preventDefault();
        
        const clientX = e.clientX || e.touches?.[0]?.clientX || 0;
        const clientY = e.clientY || e.touches?.[0]?.clientY || 0;
        
        const deltaX = clientX - startX;
        const deltaY = clientY - startY;
        
        const newX = Math.max(0, Math.min(window.innerWidth - element.offsetWidth, initialX + deltaX));
        const newY = Math.max(0, Math.min(window.innerHeight - element.offsetHeight, initialY + deltaY));
        
        element.style.left = newX + 'px';
        element.style.top = newY + 'px';
        element.style.right = 'auto';
        element.style.bottom = 'auto';
      } catch (error) {
        utils.trackError(error, 'makeDraggable.handleMove');
      }
    }

    function handleEnd() {
      if (state.isDragging) {
        try {
          state.isDragging = false;
          element.style.transition = 'all 0.3s ease';
          element.style.cursor = 'grab';
          saveWidgetPosition();
        } catch (error) {
          utils.trackError(error, 'makeDraggable.handleEnd');
        }
      }
    }

    utils.addEventListener(element, 'mousedown', handleStart);
    utils.addEventListener(document, 'mousemove', handleMove);
    utils.addEventListener(document, 'mouseup', handleEnd);

    if (utils.detectFeatures().touchEvents) {
      utils.addEventListener(element, 'touchstart', handleStart, { passive: false });
      utils.addEventListener(document, 'touchmove', handleMove, { passive: false });
      utils.addEventListener(document, 'touchend', handleEnd);
    }

    return true;
  }

  // Enhanced notification system
  function showNotification(message, duration = 3000, type = 'info') {
    if (!utils.isDOMReady()) {
      utils.debugLog(2, 'Cannot show notification, DOM not ready');
      return false;
    }

    try {
      const notification = document.createElement('div');
      notification.className = `voicetracking-notification ${type}`;
      notification.textContent = utils.sanitizeText(message);
      notification.setAttribute('role', 'alert');
      
      document.body.appendChild(notification);
      
      const timer = utils.setTimeout(() => {
        notification.style.opacity = '0';
        utils.setTimeout(() => {
          if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
          }
        }, 300);
      }, duration);
      
      if (state.elements.srAnnouncement) {
        state.elements.srAnnouncement.textContent = message;
      }
      
      return true;
    } catch (e) {
      utils.trackError(e, 'showNotification');
      return false;
    }
  }

  // Enhanced feedback
  function showFeedback(message, duration = 2000) {
    if (!activeConfig.showVisualFeedback || !state.elements.feedback) return false;

    try {
      state.elements.feedback.textContent = utils.sanitizeText(message);
      state.elements.feedback.classList.add('show');

      utils.setTimeout(() => {
        if (state.elements.feedback) {
          state.elements.feedback.classList.remove('show');
        }
      }, duration);
      
      return true;
    } catch (e) {
      utils.trackError(e, 'showFeedback');
      return false;
    }
  }

  // Enhanced status updates
  function updateListeningStatus(newState) {
    if (!state.elements.status || !state.elements.text || !state.elements.toggleBtn) {
      utils.debugLog(2, 'UI elements not ready for status update.');
      return false;
    }

    const validStates = ['inactive', 'listening', 'processing', 'recognized', 'error', 'dictating'];
    if (!validStates.includes(newState)) {
      utils.debugLog(2, 'Invalid state:', newState);
      return false;
    }

    try {
      state.listeningState = newState;
      
      state.elements.status.classList.remove(...validStates);
      state.elements.status.classList.add(newState);

      const statusTexts = {
        inactive: 'Voice Control',
        listening: 'Listening...',
        processing: 'Processing...',
        recognized: 'Recognized!',
        error: 'Error',
        dictating: 'Dictating...'
      };
      
      const statusText = statusTexts[newState] || 'Voice Control';
      state.elements.text.textContent = statusText;
      state.elements.toggleBtn.textContent = newState === 'inactive' ? 'Start' : 'Stop';
      
      state.elements.status.setAttribute('aria-label', `Voice control status: ${statusText}`);
      
      if (['listening', 'error', 'dictating'].includes(newState) && state.elements.srAnnouncement) {
        state.elements.srAnnouncement.textContent = statusText;
      }
      
      return true;
    } catch (e) {
      utils.trackError(e, 'updateListeningStatus');
      return false;
    }
  }

  // Enhanced microphone access testing
  async function testMicrophoneAccess() {
    if (state.microphoneAccessGranted) return true;
    
    try {
      const constraints = {
        audio: {
          noiseSuppression: true,
          echoCancellation: true,
          autoGainControl: true,
          sampleRate: 44100
        }
      };
      
      const stream = await navigator.mediaDevices.getUserMedia(constraints);
      
      const tracks = stream.getAudioTracks();
      if (tracks.length === 0) {
        throw new Error('No audio tracks available');
      }
      
      tracks.forEach(track => {
        track.stop();
        utils.debugLog(3, 'Audio track stopped:', track.label);
      });
      
      state.microphoneAccessGranted = true;
      utils.resetErrorTracking();
      return true;
    } catch (err) {
      utils.trackError(err, 'testMicrophoneAccess');
      state.microphoneAccessGranted = false;
      
      let errorMessage = 'Microphone access error';
      if (err.name === 'NotAllowedError') {
        errorMessage = 'Microphone access denied. Please allow microphone access in browser settings.';
      } else if (err.name === 'NotFoundError') {
        errorMessage = 'No microphone found. Please connect a microphone.';
      } else if (err.name === 'NotReadableError') {
        errorMessage = 'Microphone is being used by another application.';
      } else {
        errorMessage = `Microphone error: ${err.message}`;
      }
      
      showNotification(errorMessage, 5000, 'error');
      return false;
    }
  }

  // Enhanced intersection observer for visible elements
  function setupIntersectionObserver() {
    if (!activeConfig.intersectionObserverEnabled || !utils.detectFeatures().intersectionObserver) {
      return false;
    }

    try {
      state.intersectionObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            state.visibleElements.add(entry.target);
          } else {
            state.visibleElements.delete(entry.target);
          }
        });
      }, {
        rootMargin: '50px',
        threshold: 0.1
      });

      utils.debugLog(4, 'IntersectionObserver setup completed');
      return true;
    } catch (e) {
      utils.trackError(e, 'setupIntersectionObserver');
      return false;
    }
  }

  // Enhanced element scanning
  function scanPageForClickableElements() {
    const currentTime = Date.now();
    
    // Cache validation with enabled state consideration
    if (activeConfig.enabled) {
      state.cache.delete('clickableElements');
    } else {
      if (currentTime - state.lastCacheTime < activeConfig.cacheTimeout && 
          state.cache.has('clickableElements')) {
        state.clickableElements = state.cache.get('clickableElements');
        utils.debugLog(4, 'Using cached clickable elements:', state.clickableElements.length);
        return state.clickableElements.length;
      }
    }
    
    // Throttling for performance
    if (!activeConfig.enabled && currentTime - state.lastScanTime < activeConfig.scanInterval) {
      return state.clickableElements.length;
    }

    state.lastScanTime = currentTime;
    state.clickableElements = [];

    const selectorString = CLICKABLE_SELECTORS.join(',');
    if (!selectorString) {
      utils.debugLog(2, "No selectors available for scanning.");
      return 0;
    }

    try {
      const allElements = document.querySelectorAll(selectorString);
      let processedCount = 0;

      // Process in batches for better performance
      const batchSize = activeConfig.elementScanBatchSize || 100;
      const totalElements = Math.min(allElements.length, activeConfig.maxElementsToScan);

      for (let i = 0; i < totalElements; i++) {
        const element = allElements[i];
        
        if (processedCount >= activeConfig.maxElementsToScan) {
          utils.debugLog(2, `Element scan limit reached (${activeConfig.maxElementsToScan})`);
          break;
        }

        // Skip own widget elements
        if (state.elements.widget && 
            (element === state.elements.widget || element.closest('.voicetracking-widget'))) {
          continue;
        }

        // Enhanced visibility check
        if (!isElementVisible(element)) continue;

        const text = extractElementText(element);
        if (!text || text.length < 1) continue;

        const elementData = {
          element: element,
          text: text,
          normalizedText: normalizeText(text),
          id: element.id || '',
          tagName: element.tagName.toLowerCase(),
          isTextInput: isTextInputElement(element)
        };

        state.clickableElements.push(elementData);

        // Observe element if intersection observer is available
        if (state.intersectionObserver) {
          state.intersectionObserver.observe(element);
        }

        processedCount++;

        // Yield to browser every batch
        if (processedCount % batchSize === 0) {
          if (processedCount < totalElements - batchSize) {
            utils.setTimeout(() => {
              // Batch processing can continue
            }, 0);
          }
        }
      }

      // Cache results if not actively enabled
      if (!activeConfig.enabled) {
        state.cache.set('clickableElements', [...state.clickableElements]);
        state.lastCacheTime = currentTime;
      }

      if (activeConfig.enabled) {
        setupElementAwareCommands();
      }

      utils.debugLog(3, `Scanned ${state.clickableElements.length} clickable elements.`);
      utils.resetErrorTracking();
      return state.clickableElements.length;

    } catch (e) {
      utils.trackError(e, 'scanPageForClickableElements');
      showNotification("Error scanning page elements. Voice commands might be limited.", 4000, 'warning');
      return 0;
    }
  }

  // Check if element is a text input
  function isTextInputElement(element) {
    const textInputTypes = ['text', 'email', 'password', 'search', 'tel', 'url', 'number'];
    
    if (element.tagName === 'TEXTAREA') return true;
    if (element.tagName === 'INPUT' && textInputTypes.includes(element.type)) return true;
    if (element.getAttribute('contenteditable') === 'true') return true;
    
    return false;
  }

  // Enhanced element visibility detection
  function isElementVisible(element) {
    try {
      if (!element || !element.isConnected) return false;

      const rect = element.getBoundingClientRect();
      const style = window.getComputedStyle(element);
      
      // Basic visibility checks
      if (rect.width === 0 || rect.height === 0) return false;
      if (style.visibility === 'hidden' || style.display === 'none') return false;
      if (style.opacity === '0') return false;
      
      // Viewport checks with margin
      const margin = 50;
      if (rect.bottom < -margin || rect.top > window.innerHeight + margin ||
          rect.right < -margin || rect.left > window.innerWidth + margin) {
        return false;
      }

      // Use intersection observer data if available
      if (state.intersectionObserver && state.visibleElements.has(element)) {
        return true;
      }
      
      return true;
    } catch (e) {
      utils.debugLog(2, 'Error checking element visibility:', e);
      return false;
    }
  }

  // Enhanced text extraction
  function extractElementText(element) {
    try {
      if (!element) return '';

      let text = '';

      if (element.tagName === 'INPUT') {
        if (['button', 'submit', 'reset'].includes(element.type)) {
          text = element.value || element.getAttribute('aria-label') || element.title || element.name || element.id || '';
        } else if (element.placeholder) {
          text = element.placeholder;
        } else if (element.labels && element.labels.length > 0) {
          text = element.labels[0].textContent || '';
        }
      } else {
        text = element.getAttribute('aria-label') ||
               element.getAttribute('title') ||
               element.getAttribute('alt') ||
               (element.tagName === 'IMG' && element.alt) ||
               element.textContent ||
               element.innerText ||
               '';
      }

      text = text.trim().replace(/\s+/g, ' ');
      
      // Fallback to child text nodes
      if (!text) {
        for (const node of element.childNodes) {
          if (node.nodeType === Node.TEXT_NODE) {
            text += node.textContent.trim() + ' ';
          }
        }
        text = text.trim();
      }

      return text.substring(0, activeConfig.maxInputLength);
    } catch (e) {
      utils.debugLog(2, 'Error extracting element text:', e);
      return '';
    }
  }

  // Enhanced text normalization
  function normalizeText(text) {
    if (typeof text !== 'string') return '';
    
    try {
      // Input validation
      if (!utils.validateInput(text)) {
        utils.debugLog(2, 'Invalid text input for normalization');
        return '';
      }

      let normalized = text.toLowerCase();
      
      // Remove special characters
      normalized = normalized.replace(/[^\w\s]/g, ' ');
      normalized = normalized.replace(/\s+/g, ' ').trim();

      // Remove ignore words
      for (const word of activeConfig.ignoreWords) {
        const regex = new RegExp(`\\b${word.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}\\b`, 'gi');
        normalized = normalized.replace(regex, '');
      }

      return normalized.replace(/\s+/g, ' ').trim();
    } catch (e) {
      utils.debugLog(2, 'Error normalizing text:', e);
      return '';
    }
  }

  // Enhanced command parsing
  function parseCommand(spokenText) {
    if (!spokenText || typeof spokenText !== 'string') return null;
    
    // Input validation
    if (!utils.validateInput(spokenText)) {
      utils.debugLog(2, 'Invalid spoken text input');
      return null;
    }
    
    const normalizedText = spokenText.toLowerCase().trim();
    
    // Check for command prefix
    if (normalizedText.startsWith(activeConfig.commandPrefix + ' ')) {
      const commandText = normalizedText.substring(activeConfig.commandPrefix.length + 1);
      return {
        type: 'prefixed-command',
        command: commandText,
        isCommand: true
      };
    }
    
    // Letter-to-index mapping (A=0, B=1, C=2, etc.)
    const letterToIndex = {
      'a': 0, 'b': 1, 'c': 2, 'd': 3, 'e': 4, 'f': 5, 'g': 6, 'h': 7, 
      'i': 8, 'j': 9, 'k': 10, 'l': 11, 'm': 12, 'n': 13, 'o': 14, 'p': 15,
      'q': 16, 'r': 17, 's': 18, 't': 19, 'u': 20, 'v': 21, 'w': 22, 'x': 23, 'y': 24, 'z': 25,
      // Phonetic variations
      'ay': 0, 'eh': 0, 'alpha': 0,
      'be': 1, 'bee': 1, 'beta': 1,
      'see': 2, 'sea': 2, 'charlie': 2,
      'dee': 3, 'delta': 3,
      'ee': 4, 'echo': 4,
      'ef': 5, 'eff': 5, 'foxtrot': 5,
      'gee': 6, 'golf': 6,
      'aitch': 7, 'hotel': 7
    };

    // If we have lettered overlays active, prioritize letter commands
    if (state.numberedElements.length > 0 && state.currentDuplicateBase) {
      utils.debugLog(4, 'Lettered overlays active, checking for letter commands');
      utils.debugLog(4, `Original text: "${spokenText}", normalized: "${normalizedText}"`);
      
      // Strategy 1: Look for single letters
      const singleLetterMatch = normalizedText.match(/\b([a-z])\b/);
      if (singleLetterMatch) {
        const letter = singleLetterMatch[1];
        const index = letterToIndex[letter];
        if (index !== undefined && index < state.numberedElements.length) {
          utils.debugLog(4, `Found single letter: ${letter.toUpperCase()} -> index ${index}`);
          return {
            type: 'letter',
            letter: letter.toUpperCase(),
            index: index,
            isNumbered: true,
            baseCommand: state.currentDuplicateBase
          };
        }
      }
      
      // Strategy 2: Look for letter words/phonetic variations
      for (const [word, index] of Object.entries(letterToIndex)) {
        if (normalizedText.includes(word) && index < state.numberedElements.length) {
          const actualLetter = String.fromCharCode(65 + index);
          utils.debugLog(4, `Found letter word "${word}" -> ${actualLetter} (index ${index})`);
          return {
            type: 'letter',
            letter: actualLetter,
            index: index,
            isNumbered: true,
            baseCommand: state.currentDuplicateBase
          };
        }
      }
      
      // Strategy 3: Check if the entire text is just a letter word
      if (letterToIndex.hasOwnProperty(normalizedText)) {
        const index = letterToIndex[normalizedText];
        if (index < state.numberedElements.length) {
          const actualLetter = String.fromCharCode(65 + index);
          utils.debugLog(4, `Exact letter match "${normalizedText}": ${actualLetter}`);
          return {
            type: 'letter',
            letter: actualLetter,
            index: index,
            isNumbered: true,
            baseCommand: state.currentDuplicateBase
          };
        }
      }
      
      // Strategy 4: Handle common phrases
      const phrasePatterns = [
        /(?:letter|option|choice|pick|select|choose)\s+([a-z]|[a-z]{2,})/i,
        /([a-z]|alpha|beta|charlie|delta|echo|foxtrot|golf|hotel)(?:\s+please|\s+now)?$/i
      ];
      
      for (const pattern of phrasePatterns) {
        const match = normalizedText.match(pattern);
        if (match && match[1]) {
          const letterWord = match[1].toLowerCase();
          
          if (letterToIndex.hasOwnProperty(letterWord)) {
            const index = letterToIndex[letterWord];
            if (index < state.numberedElements.length) {
              const actualLetter = String.fromCharCode(65 + index);
              utils.debugLog(4, `Found letter in phrase: ${actualLetter}`);
              return {
                type: 'letter',
                letter: actualLetter,
                index: index,
                isNumbered: true,
                baseCommand: state.currentDuplicateBase
              };
            }
          }
        }
      }
      
      // If speech was detected but not a valid letter, extend timeout
      if (activeConfig.extendTimeOnSpeech && normalizedText.length > 0) {
        utils.debugLog(3, 'Speech detected but not a valid letter, extending timeout');
        extendLeteredTimeout();
        
        // Give more helpful feedback
        const maxLetter = String.fromCharCode(65 + state.numberedElements.length - 1);
        showFeedback(`Say a letter A-${maxLetter}`);
        showNotification(`I heard "${spokenText}" - please say a letter A through ${maxLetter}`, 3000, 'warning');
        
        utils.debugLog(4, 'Failed to match any letter patterns for:', {
          original: spokenText,
          normalized: normalizedText,
          availableLetters: `A-${maxLetter}`,
          letterToIndexKeys: Object.keys(letterToIndex).slice(0, state.numberedElements.length)
        });
      }
    }

    // Regular command parsing for non-lettered scenarios
    return {
      type: 'command',
      command: normalizedText,
      isNumbered: false
    };
  }

  // Enhanced lettered timeout extension
  function extendLeteredTimeout() {
    if (state.numberedElements.length === 0) return false;
    
    try {
      // Clear existing timeout
      if (state.numberedOverlayTimeout) {
        utils.clearTimeout(state.numberedOverlayTimeout);
      }
      
      // Set new extended timeout
      state.numberedOverlayTimeout = utils.setTimeout(() => {
        utils.debugLog(3, 'Extended lettered overlays timeout reached');
        clearLeteredOverlays();
      }, activeConfig.autoExtendTimeout);
      
      // Update countdown if it exists
      if (state.countdownElement) {
        // Add extra time to countdown
        let currentTime = parseInt(state.countdownElement.textContent) || 0;
        const extraSeconds = Math.floor(activeConfig.autoExtendTimeout / 1000);
        currentTime += extraSeconds;
        state.countdownElement.textContent = currentTime.toString();
        state.countdownElement.style.background = 'rgba(33, 150, 243, 0.95)'; // Reset color
        
        utils.debugLog(3, `Added ${extraSeconds} seconds to countdown`);
      }
      
      utils.debugLog(3, 'Lettered overlay timeout extended');
      return true;
    } catch (e) {
      utils.trackError(e, 'extendLeteredTimeout');
      return false;
    }
  }

  // Enhanced lettered overlays
  function showLeteredDuplicates(elements, baseCommand) {
    clearLeteredOverlays();
    
    if (!Array.isArray(elements) || elements.length === 0) {
      utils.debugLog(2, 'Invalid elements array for lettered duplicates');
      return false;
    }

    if (!utils.isDOMReady()) {
      utils.debugLog(2, 'DOM not ready for lettered overlays');
      return false;
    }
    
    utils.debugLog(3, `Showing ${elements.length} lettered duplicates for "${baseCommand}"`);
    utils.debugLog(4, 'Elements to letter:', elements.map((el, i) => `${String.fromCharCode(65 + i)}: "${el.text}"`));
    
    try {
      state.currentDuplicateBase = baseCommand;
      state.numberedElements = elements;
      
      // Create lettered overlays with waiting animation
      elements.forEach((elementData, index) => {
        try {
          const element = elementData.element;
          const rect = element.getBoundingClientRect();
          const letter = String.fromCharCode(65 + index); // A, B, C, D, etc.
          
          const overlay = document.createElement('div');
          overlay.className = 'voicetracking-numbered-overlay waiting';
          overlay.textContent = letter;
          
          overlay.style.cssText = `
            position: absolute;
            top: ${rect.top + window.scrollY - 12}px;
            left: ${rect.right + window.scrollX - 28}px;
          `;
          
          document.body.appendChild(overlay);
          state.numberedOverlays.set(element, overlay);
          
          utils.debugLog(4, `Added letter ${letter} to element: "${elementData.text}" (${elementData.element.tagName})`);
        } catch (e) {
          utils.trackError(e, 'showLeteredDuplicates.overlay');
        }
      });
      
      // Create listening indicator
      const listeningIndicator = document.createElement('div');
      listeningIndicator.className = 'voicetracking-listening-indicator';
      listeningIndicator.innerHTML = `
        <div class="voicetracking-mic-icon"></div>
        Listening for letter...
      `;
      document.body.appendChild(listeningIndicator);
      state.listeningIndicator = listeningIndicator;
      
      // Create countdown display if enabled
      let countdownElement = null;
      let timeLeft = Math.floor(activeConfig.duplicateOverlayTimeout / 1000);
      
      if (activeConfig.showCountdown) {
        countdownElement = document.createElement('div');
        countdownElement.className = 'voicetracking-countdown';
        countdownElement.textContent = timeLeft.toString();
        document.body.appendChild(countdownElement);
        state.countdownElement = countdownElement;
        
        // Update countdown every second
        const countdownInterval = setInterval(() => {
          timeLeft--;
          if (countdownElement && countdownElement.parentNode) {
            countdownElement.textContent = timeLeft.toString();
            
            // Change color as time runs out
            if (timeLeft <= 5) {
              countdownElement.style.background = 'rgba(244, 67, 54, 0.95)';
            } else if (timeLeft <= 10) {
              countdownElement.style.background = 'rgba(255, 152, 0, 0.95)';
            }
            
            if (timeLeft <= 0) {
              clearInterval(countdownInterval);
            }
          } else {
            clearInterval(countdownInterval);
          }
        }, 1000);
        
        state.countdownInterval = countdownInterval;
      }
      
      // Set main timeout
      if (state.numberedOverlayTimeout) {
        utils.clearTimeout(state.numberedOverlayTimeout);
      }
      
      state.numberedOverlayTimeout = utils.setTimeout(() => {
        utils.debugLog(3, 'Lettered overlays timeout reached');
        clearLeteredOverlays();
      }, activeConfig.duplicateOverlayTimeout);
      
      // Enhanced feedback
      const maxLetter = String.fromCharCode(65 + elements.length - 1);
      showFeedback(`Say a letter A-${maxLetter}`, 3000);
      showNotification(`Letters ready! Say A, B, C... up to ${maxLetter}. You have ${Math.floor(activeConfig.duplicateOverlayTimeout/1000)} seconds.`, 8000);
      
      // Force speech recognition to be active
      if (typeof annyang !== 'undefined' && activeConfig.enabled) {
        try {
          if (!annyang.isListening()) {
            annyang.start({ autoRestart: false, continuous: false });
          }
          utils.debugLog(4, 'Speech recognition confirmed active for letter input');
        } catch (e) {
          utils.debugLog(2, 'Could not confirm speech recognition state:', e);
        }
      }
      
      return true;
    } catch (e) {
      utils.trackError(e, 'showLeteredDuplicates');
      return false;
    }
  }

  // Enhanced lettered overlays cleanup
  function clearLeteredOverlays() {
    try {
      // Clear lettered overlays
      for (const [element, overlay] of state.numberedOverlays) {
        if (overlay && overlay.parentNode) {
          overlay.parentNode.removeChild(overlay);
        }
      }
      
      // Clear listening indicator
      if (state.listeningIndicator && state.listeningIndicator.parentNode) {
        state.listeningIndicator.parentNode.removeChild(state.listeningIndicator);
        state.listeningIndicator = null;
      }
      
      // Clear countdown
      if (state.countdownElement && state.countdownElement.parentNode) {
        state.countdownElement.parentNode.removeChild(state.countdownElement);
        state.countdownElement = null;
      }
      
      // Clear countdown interval
      if (state.countdownInterval) {
        clearInterval(state.countdownInterval);
        state.countdownInterval = null;
      }
      
      // Clear main timeout
      if (state.numberedOverlayTimeout) {
        utils.clearTimeout(state.numberedOverlayTimeout);
        state.numberedOverlayTimeout = null;
      }
      
      // Reset state
      state.numberedOverlays.clear();
      state.numberedElements = [];
      state.currentDuplicateBase = null;
      
      utils.debugLog(4, 'All lettered overlays and indicators cleared');
      return true;
    } catch (e) {
      utils.trackError(e, 'clearLeteredOverlays');
      return false;
    }
  }

  // Alias for backward compatibility
  const clearNumberedOverlays = clearLeteredOverlays;

  // Optimized Levenshtein distance
  function levenshteinDistance(a, b, maxDistance = Infinity) {
    if (!a || !b) return Math.max(a?.length || 0, b?.length || 0);
    if (a === b) return 0;
    
    // Input validation
    if (typeof a !== 'string' || typeof b !== 'string') return Infinity;
    
    const aLen = a.length;
    const bLen = b.length;
    
    if (Math.abs(aLen - bLen) > maxDistance) return maxDistance + 1;
    
    if (aLen === 0) return bLen;
    if (bLen === 0) return aLen;

    const matrix = [];
    
    for (let i = 0; i <= bLen; i++) {
      matrix[i] = [i];
    }
    for (let j = 0; j <= aLen; j++) {
      matrix[0][j] = j;
    }

    for (let i = 1; i <= bLen; i++) {
      let minRowValue = Infinity;
      for (let j = 1; j <= aLen; j++) {
        if (b.charAt(i-1) === a.charAt(j-1)) {
          matrix[i][j] = matrix[i-1][j-1];
        } else {
          matrix[i][j] = Math.min(
            matrix[i-1][j-1] + 1,
            matrix[i][j-1] + 1,
            matrix[i-1][j] + 1
          );
        }
        minRowValue = Math.min(minRowValue, matrix[i][j]);
      }
      
      if (minRowValue > maxDistance) {
        return maxDistance + 1;
      }
    }

    return matrix[bLen][aLen];
  }

  // Enhanced similarity calculation
  function calculateSimilarity(str1, str2) {
    if (typeof str1 !== 'string' || typeof str2 !== 'string') return 1;
    
    const maxLen = Math.max(str1.length, str2.length);
    if (maxLen === 0) return 0;
    
    const distance = levenshteinDistance(str1, str2, activeConfig.maxDistance * maxLen);
    return distance / maxLen;
  }

  // Enhanced element matching
  function findBestElementMatch(spokenText) {
    if (!spokenText || typeof spokenText !== 'string') return null;
    
    // Input validation
    if (!utils.validateInput(spokenText)) {
      utils.debugLog(2, 'Invalid spoken text for element matching');
      return null;
    }
    
    const parsedCommand = parseCommand(spokenText);
    utils.debugLog(4, 'Parsed command:', parsedCommand);
    utils.debugLog(4, 'Current state - numberedElements:', state.numberedElements.length, 'currentDuplicateBase:', state.currentDuplicateBase);
    
    try {
      scanPageForClickableElements();
      
      // Handle standalone letter commands when overlays are active
      if (parsedCommand.isNumbered && parsedCommand.type === 'letter' && state.numberedElements.length > 0) {
        utils.debugLog(4, 'Processing standalone letter command...');
        
        const index = parsedCommand.index;
        
        utils.debugLog(4, `Letter command ${parsedCommand.letter}, index: ${index}, available elements: ${state.numberedElements.length}`);
        
        if (index >= 0 && index < state.numberedElements.length) {
          const selectedElement = state.numberedElements[index];
          utils.debugLog(3, `Selected lettered element ${parsedCommand.letter}: "${selectedElement.text}"`);
          clearLeteredOverlays();
          return selectedElement;
        } else {
          const maxLetter = String.fromCharCode(65 + state.numberedElements.length - 1);
          utils.debugLog(3, `Invalid letter ${parsedCommand.letter}, only A-${maxLetter} available`);
          showNotification(`Only letters A-${maxLetter} are available`, activeConfig.duplicateErrorTimeout, 'warning');
          
          // Reset the timeout
          if (state.numberedOverlayTimeout) {
            utils.clearTimeout(state.numberedOverlayTimeout);
          }
          state.numberedOverlayTimeout = utils.setTimeout(() => {
            clearLeteredOverlays();
          }, activeConfig.duplicateOverlayTimeout);
          
          return null;
        }
      }
      
      // Clear overlays for new searches (non-letter commands)
      if (parsedCommand.type === 'command') {
        clearLeteredOverlays();
      }
      
      const searchText = parsedCommand.command;
      const normalizedSpoken = normalizeText(searchText);
      if (!normalizedSpoken) return null;

      utils.debugLog(3, `Looking for element matching: "${normalizedSpoken}" among ${state.clickableElements.length} elements`);

      // Find exact matches first
      const exactMatches = [];
      for (const element of state.clickableElements) {
        if (element.normalizedText === normalizedSpoken) {
          exactMatches.push(element);
        }
      }
      
      if (exactMatches.length > 1) {
        utils.debugLog(3, `Found ${exactMatches.length} exact matches, showing lettered overlays`);
        showLeteredDuplicates(exactMatches, searchText);
        return null;
      }
      
      if (exactMatches.length === 1) {
        utils.debugLog(3, `Single exact match found: "${exactMatches[0].normalizedText}"`);
        return exactMatches[0];
      }

      // Strategy 2: ID-based match
      const numberMatch = normalizedSpoken.match(/\b(\d+)\b/);
      if (numberMatch) {
        const number = numberMatch[1];
        
        const idPatterns = [`btn${number}`, `button${number}`, `item${number}`];
        for (const pattern of idPatterns) {
          const elementById = document.getElementById(pattern);
          if (elementById) {
            const foundElement = state.clickableElements.find(el => el.element === elementById);
            if (foundElement) {
              utils.debugLog(3, `ID-based match found: ${pattern}`);
              return foundElement;
            }
          }
        }
        
        const numberMatches = [];
        for (const element of state.clickableElements) {
          if (new RegExp(`\\b${number}\\b`).test(element.normalizedText)) {
            numberMatches.push(element);
          }
        }
        
        if (numberMatches.length > 1) {
          utils.debugLog(3, `Found ${numberMatches.length} number matches, showing lettered overlays`);
          showLeteredDuplicates(numberMatches, searchText);
          return null;
        } else if (numberMatches.length === 1) {
          utils.debugLog(3, `Single number match found: "${numberMatches[0].normalizedText}"`);
          return numberMatches[0];
        }
      }

      // Strategy 3: Partial matching
      const partialMatches = [];
      for (const element of state.clickableElements) {
        if (element.normalizedText.length < 2) continue;

        if (normalizedSpoken.includes(element.normalizedText)) {
          partialMatches.push(element);
        }
        else if (normalizedSpoken.length >= 3 && element.normalizedText.includes(normalizedSpoken)) {
          partialMatches.push(element);
        }
      }
      
      if (partialMatches.length > 1) {
        utils.debugLog(3, `Found ${partialMatches.length} partial matches, showing lettered overlays`);
        showLeteredDuplicates(partialMatches, searchText);
        return null;
      } else if (partialMatches.length === 1) {
        utils.debugLog(3, `Single partial match found: "${partialMatches[0].normalizedText}"`);
        return partialMatches[0];
      }

      // Strategy 4: Fuzzy matching
      if (activeConfig.useEnhancedMatching) {
        const fuzzyMatches = [];
        
        for (const element of state.clickableElements) {
          if (element.normalizedText.length < 2) continue;

          const similarity = calculateSimilarity(normalizedSpoken, element.normalizedText);
          if (similarity < activeConfig.maxDistance) {
            fuzzyMatches.push({ element, similarity });
          }
        }
        
        fuzzyMatches.sort((a, b) => a.similarity - b.similarity);
        
        const bestSimilarity = fuzzyMatches[0]?.similarity;
        const bestMatches = fuzzyMatches.filter(match => match.similarity === bestSimilarity).map(match => match.element);
        
        if (bestMatches.length > 1) {
          utils.debugLog(3, `Found ${bestMatches.length} fuzzy matches with similarity ${bestSimilarity.toFixed(3)}, showing lettered overlays`);
          showLeteredDuplicates(bestMatches, searchText);
          return null;
        } else if (bestMatches.length === 1) {
          utils.debugLog(3, `Single fuzzy match found: "${bestMatches[0].normalizedText}" (similarity: ${bestSimilarity.toFixed(3)})`);
          return bestMatches[0];
        }
      }

      utils.debugLog(3, `No match found for: "${normalizedSpoken}"`);
      return null;
    } catch (e) {
      utils.trackError(e, 'findBestElementMatch');
      return null;
    }
  }

  // Enhanced element highlighting
  function highlightElement(element) {
    if (!element || !utils.isDOMReady()) return false;
    
    clearElementHighlights();

    try {
      const rect = element.getBoundingClientRect();
      const highlight = document.createElement('div');

      highlight.className = 'voicetracking-highlight';
      highlight.style.cssText = `
        position: absolute;
        top: ${rect.top + window.scrollY}px;
        left: ${rect.left + window.scrollX}px;
        width: ${rect.width}px;
        height: ${rect.height}px;
      `;

      document.body.appendChild(highlight);
      state.elementHighlights.set(element, highlight);

      if (state.highlightTimeout) {
        utils.clearTimeout(state.highlightTimeout);
      }
      
      state.highlightTimeout = utils.setTimeout(() => {
        clearElementHighlights();
      }, 1000);
      
      return true;
    } catch (e) {
      utils.trackError(e, 'highlightElement');
      return false;
    }
  }

  // Enhanced highlight cleanup
  function clearElementHighlights() {
    try {
      for (const [element, highlight] of state.elementHighlights) {
        if (highlight && highlight.parentNode) {
          highlight.parentNode.removeChild(highlight);
        }
      }
      state.elementHighlights.clear();
      
      if (state.highlightTimeout) {
        utils.clearTimeout(state.highlightTimeout);
        state.highlightTimeout = null;
      }
      
      return true;
    } catch (e) {
      utils.trackError(e, 'clearElementHighlights');
      return false;
    }
  }

  // Enhanced click execution with dictation awareness
  function doClickElement(element, text) {
    const currentTime = Date.now();
    
    utils.debugLog(3, `doClickElement called - element: ${element.tagName}, text: "${text}"`);
    
    if (currentTime - state.lastCommandTime < activeConfig.commandCooldown) {
      utils.debugLog(3, `Command on cooldown (${currentTime - state.lastCommandTime}ms since last)`);
      return false;
    }

    if (state.isProcessingCommand) {
      utils.debugLog(2, 'Command already processing, forcing reset');
      state.isProcessingCommand = false;
    }

    state.commandCounter++;
    utils.debugLog(3, `Processing command #${state.commandCounter} for element:`, {
      tagName: element.tagName,
      text: text,
      id: element.id,
      className: element.className,
      visible: isElementVisible(element)
    });

    try {
      state.isProcessingCommand = true;
      state.lastCommandTime = currentTime;
      state.lastClickedElement = element;

      highlightElement(element);
      showFeedback(`Clicking: "${utils.sanitizeText(text)}"`);

      updateListeningStatus('recognized');

      // Verify element
      if (!element.isConnected) {
        utils.debugLog(1, 'Element is no longer in DOM');
        throw new Error('Element no longer exists in DOM');
      }
      
      if (!isElementVisible(element)) {
        utils.debugLog(2, 'Element is not visible, but attempting click anyway');
      }

      const resetStateTimer = utils.setTimeout(() => {
        state.isProcessingCommand = false;
        updateListeningStatus('listening');
        utils.debugLog(4, `Command #${state.commandCounter} state reset via timeout`);
      }, 1000);

      let clickSuccess = false;

      // Special handling for input fields
      if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA') {
        try {
          utils.debugLog(4, `Command #${state.commandCounter} - Special input field handling`);
          
          // Click the element
          element.click();
          
          // Focus it
          element.focus();
          
          // For text input fields
          if (isTextInputElement(element)) {
            // Check if dictation should auto-start
            if (activeConfig.autoStartDictation && activeConfig.dictationEnabled) {
              utils.setTimeout(() => {
                if (document.activeElement === element) {
                  startDictationForElement(element);
                }
              }, 500);
            }
            
            // Set cursor position
            if (element.value && element.value.length > 0) {
              element.select();
            } else {
              element.setSelectionRange(0, 0);
            }
            
            // Double-check focus
            if (document.activeElement !== element) {
              utils.debugLog(2, 'Focus failed, trying again...');
              utils.setTimeout(() => {
                element.focus();
                if (element.value) {
                  element.select();
                }
              }, 100);
            }
            
            showFeedback(`Ready to type in: "${utils.sanitizeText(text)}"`);
            utils.debugLog(3, `Input field focused and ready for typing: ${element.tagName}`);
          }
          
          clickSuccess = true;
          utils.debugLog(3, `Command #${state.commandCounter} - Input field click and focus succeeded`);
          
        } catch (e) {
          utils.debugLog(2, `Command #${state.commandCounter} - Input field handling failed:`, e);
          clickSuccess = regularClickHandling(element);
        }
      } else {
        // Regular click handling for non-input elements
        clickSuccess = regularClickHandling(element);
      }

      // Always reset state after click attempt
      utils.setTimeout(() => {
        state.isProcessingCommand = false;
        if (state.dictationState === 'inactive') {
          updateListeningStatus('listening');
        }
        utils.debugLog(4, `Command #${state.commandCounter} completed, state reset`);
        
        // Restart speech recognition for better reliability
        if (activeConfig.enabled && typeof annyang !== 'undefined' && state.dictationState === 'inactive') {
          try {
            annyang.start({ autoRestart: false, continuous: false });
          } catch (e) {
            utils.debugLog(2, 'Failed to restart recognition after click:', e);
          }
        }
      }, 500);

      if (clickSuccess) {
        utils.resetErrorTracking();
      }

      return clickSuccess;

    } catch (e) {
      utils.trackError(e, `doClickElement.command#${state.commandCounter}`);
      state.isProcessingCommand = false;
      updateListeningStatus('listening');
      return false;
    }

    // Helper function for regular (non-input) click handling
    function regularClickHandling(element) {
      let success = false;
      
      try {
        utils.debugLog(4, `Command #${state.commandCounter} - Attempting native click`);
        element.click();
        success = true;
        utils.debugLog(3, `Command #${state.commandCounter} - Native click succeeded`);
      } catch (e) {
        utils.debugLog(2, `Command #${state.commandCounter} - Native click failed:`, e);
        
        try {
          utils.debugLog(4, `Command #${state.commandCounter} - Attempting mouse event click`);
          const rect = element.getBoundingClientRect();
          const clickEvent = new MouseEvent('click', {
            bubbles: true,
            cancelable: true,
            view: window,
            detail: 1,
            clientX: rect.left + rect.width / 2,
            clientY: rect.top + rect.height / 2
          });
          const dispatched = element.dispatchEvent(clickEvent);
          success = true;
          utils.debugLog(3, `Command #${state.commandCounter} - Mouse event click succeeded, dispatched: ${dispatched}`);
        } catch (e2) {
          utils.debugLog(2, `Command #${state.commandCounter} - Mouse event click failed:`, e2);
          
          try {
            utils.debugLog(4, `Command #${state.commandCounter} - Attempting focus/activate strategy`);
            if (element.focus) element.focus();
            
            if (element.tagName === 'INPUT' || element.tagName === 'BUTTON') {
              if (element.form && element.type === 'submit') {
                utils.debugLog(3, 'Submitting form');
                element.form.submit();
              } else if (element.type === 'button' || element.tagName === 'BUTTON') {
                const activateEvent = new Event('activate', { bubbles: true });
                element.dispatchEvent(activateEvent);
              }
            } else if (element.tagName === 'A' && element.href) {
              utils.debugLog(3, 'Navigating to link:', element.href);
              if (element.target === '_blank') {
                window.open(element.href, '_blank');
              } else {
                window.location.href = element.href;
              }
            }
            
            success = true;
            utils.debugLog(3, `Command #${state.commandCounter} - Focus/activate strategy succeeded`);
          } catch (e3) {
            utils.trackError(e3, `doClickElement.command#${state.commandCounter}`);
            showNotification('Could not interact with element', 2000, 'error');
          }
        }
      }
      
      return success;
    }
  }

  // Enhanced command processing
  function findAndClickElementByName(spokenText) {
    utils.debugLog(3, `Processing voice command: "${spokenText}"`);
    utils.debugLog(4, `Current state - isProcessing: ${state.isProcessingCommand}, enabled: ${activeConfig.enabled}`);
    utils.debugLog(4, `Lettered state - elements: ${state.numberedElements.length}, base: "${state.currentDuplicateBase}"`);
    
    if (!spokenText || typeof spokenText !== 'string') {
      utils.debugLog(2, 'Invalid spoken text:', spokenText);
      return false;
    }

    // Input validation
    if (!utils.validateInput(spokenText)) {
      utils.debugLog(2, 'Spoken text failed validation');
      return false;
    }

    const wasProcessing = state.isProcessingCommand;
    state.isProcessingCommand = true;
    
    updateListeningStatus('processing');

    try {
      const bestMatch = findBestElementMatch(spokenText);

      if (bestMatch) {
        utils.debugLog(3, `Found match, attempting click:`, {
          text: bestMatch.text,
          tagName: bestMatch.element.tagName,
          id: bestMatch.element.id,
          className: bestMatch.element.className
        });
        
        const clickSuccess = doClickElement(bestMatch.element, bestMatch.text);
        
        utils.setTimeout(() => {
          state.isProcessingCommand = false;
          if (state.dictationState === 'inactive') {
            updateListeningStatus('listening');
          }
        }, 500);
        
        return clickSuccess;
      } else {
        if (state.numberedElements.length > 0) {
          state.isProcessingCommand = false;
          updateListeningStatus('listening');
          utils.debugLog(4, `Waiting for letter selection... (${state.numberedElements.length} options available)`);
        } else {
          state.isProcessingCommand = false;
          updateListeningStatus('listening');
          showFeedback(`No match: "${utils.sanitizeText(spokenText)}"`);
          utils.debugLog(3, `No element found for "${spokenText}"`);
          
          if (state.clickableElements.length > 0) {
            utils.debugLog(4, 'Available elements (first 10):');
            state.clickableElements.slice(0, 10).forEach((el, i) => {
              utils.debugLog(4, `  ${i+1}: "${el.normalizedText}" (${el.element.tagName})`);
            });
          }
        }
        
        return false;
      }
    } catch (e) {
      utils.trackError(e, 'findAndClickElementByName');
      state.isProcessingCommand = false;
      updateListeningStatus('listening');
      
      utils.setTimeout(() => {
        forceRestart();
      }, 1000);
      
      return false;
    }
  }

  // Enhanced element display
  function showClickableElements() {
    try {
      clearElementHighlights();
      clearLeteredOverlays();
      
      // Clean up existing labels
      document.querySelectorAll('.voicetracking-element-label').forEach(el => {
        if (el.parentNode) el.parentNode.removeChild(el);
      });

      scanPageForClickableElements();

      const elementsToShow = state.clickableElements
        .filter(element => {
          const rect = element.element.getBoundingClientRect();
          return rect.top >= 0 && rect.top <= window.innerHeight &&
                 rect.left >= 0 && rect.left <= window.innerWidth;
        })
        .slice(0, activeConfig.maxElementsToShow);

      elementsToShow.forEach((element, index) => {
        try {
          const el = element.element;
          const rect = el.getBoundingClientRect();

          const highlight = document.createElement('div');
          highlight.className = 'voicetracking-highlight';
          highlight.style.cssText = `
            position: absolute;
            top: ${rect.top + window.scrollY}px;
            left: ${rect.left + window.scrollX}px;
            width: ${rect.width}px;
            height: ${rect.height}px;
            background: rgba(100, 100, 255, 0.2);
            border: 1px solid rgba(100, 100, 255, 0.8);
          `;
          document.body.appendChild(highlight);
          state.elementHighlights.set(el, highlight);

          const label = document.createElement('div');
          label.className = 'voicetracking-element-label';
          label.textContent = `"${utils.sanitizeText(element.normalizedText)}"`;
          label.style.cssText = `
            position: absolute;
            top: ${Math.max(rect.top + window.scrollY - 25, 5 + window.scrollY)}px;
            left: ${rect.left + window.scrollX}px;
            max-width: ${Math.min(rect.width, 200)}px;
          `;

          document.body.appendChild(label);
        } catch (e) {
          utils.debugLog(2, 'Error showing element:', e);
        }
      });

      utils.setTimeout(() => {
        clearElementHighlights();
        document.querySelectorAll('.voicetracking-element-label').forEach(el => {
          if (el.parentNode) el.parentNode.removeChild(el);
        });
      }, 8000);

      const message = `Showing ${elementsToShow.length} clickable elements`;
      showNotification(message, 3000);
      
      if (state.elements.srAnnouncement) {
        state.elements.srAnnouncement.textContent = message;
      }
      
      return true;
    } catch (e) {
      utils.trackError(e, 'showClickableElements');
      return false;
    }
  }

  // Enhanced command setup
  function setupElementAwareCommands() {
    if (typeof annyang === 'undefined') {
      utils.debugLog(2, 'annyang not available for command setup');
      return false;
    }

    try {
      const commands = {};

      commands['*element'] = function(element) {
        if (element) {
          utils.debugLog(3, `Wildcard command triggered with: "${element}"`);
          
          // Check if this is a command prefix
          if (element.toLowerCase().startsWith(activeConfig.commandPrefix + ' ')) {
            handlePrefixedCommand(element);
          } else {
            findAndClickElementByName(element);
          }
        }
      };

      commands['show elements'] = function() {
        clearLeteredOverlays();
        showClickableElements();
      };
      
      commands['show buttons'] = function() {
        clearLeteredOverlays();
        showClickableElements();
      };
      
      commands['what can I say'] = function() {
        clearLeteredOverlays();
        showClickableElements();
      };
      
      commands['help'] = function() {
        clearLeteredOverlays();
        showClickableElements();
      };
	  
	    // catch "scroll up", "scroll down", "scroll down.", "scroll-up?" etc.
  commands['scroll *direction'] = function(direction) {
    clearLeteredOverlays();
    try {
      // normalize the spoken word: remove punctuation & lowercase
      const dir = direction.replace(/[^\w]/g, '').toLowerCase();
      if (dir === 'down') {
        window.scrollBy({ top: 300, behavior: 'smooth' });
        showFeedback("Scrolling down");
      } else if (dir === 'up') {
        window.scrollBy({ top: -300, behavior: 'smooth' });
        showFeedback("Scrolling up");
      } else {
        showFeedback(`Unrecognized scroll direction: ${direction}`);
      }
    } catch (e) {
      utils.trackError(e, 'scrollDirection:' + direction);
    }
  };

      commands['/^scroll down\\.?$/i'] = function() {
        clearLeteredOverlays();
        try {
          window.scrollBy({ top: 300, behavior: 'smooth' });
          showFeedback("Scrolling down");
        } catch (e) {
          utils.trackError(e, 'scrollDown');
        }
      };

      // → matches “scroll up”, “Scroll Up.”, “scroll-up?”, etc.
      commands['/^scroll up\\.?$/i'] = function() {
        clearLeteredOverlays();
        try {
          window.scrollBy({ top: -300, behavior: 'smooth' });
          showFeedback("Scrolling up");
        } catch (e) {
          utils.trackError(e, 'scrollUp');
        }
      };
      commands['cancel'] = function() {
        clearLeteredOverlays();
        showFeedback("Cancelled");
      };

      commands['reset state'] = function() {
        utils.resetCommandState();
        resetDictationState();
        state.cache.clear();
        scanPageForClickableElements();
        showFeedback("State reset");
      };

      commands['extend time'] = function() {
        if (state.numberedElements.length > 0) {
          extendLeteredTimeout();
          showFeedback("Time extended");
        }
      };

      commands['enable clicks'] = function() {
        clearLeteredOverlays();
        if (window.conf) {
          window.conf.enableClicks = true;
          showNotification("Clicks enabled via voice command");
        }
      };

      commands['disable clicks'] = function() {
        clearLeteredOverlays();
        if (window.conf) {
          window.conf.enableClicks = false;
          showNotification("Clicks disabled via voice command");
        }
      };

      commands['test'] = function() {
        clearLeteredOverlays();
        showFeedback("Test command recognized successfully!");
        showNotification("Speech recognition working correctly", 2000);
      };

      // NEW: Dictation command
      commands['write'] = function() {
        const activeElement = document.activeElement;
        if (isTextInputElement(activeElement)) {
          startDictationForElement(activeElement);
        } else {
          showNotification("Please focus on a text input field first", 3000, 'warning');
        }
      };

      // Debug commands
      commands['debug state'] = function() {
        utils.debugLog(3, 'VoiceTracking Debug State:');
        utils.debugLog(3, '- numberedElements:', state.numberedElements.length);
        utils.debugLog(3, '- currentDuplicateBase:', state.currentDuplicateBase);
        utils.debugLog(3, '- isProcessingCommand:', state.isProcessingCommand);
        utils.debugLog(3, '- lettered overlays:', state.numberedOverlays.size);
        utils.debugLog(3, '- dictationState:', state.dictationState);
        utils.debugLog(3, '- currentInputElement:', state.currentInputElement);
        
        if (state.numberedElements.length > 0) {
          utils.debugLog(3, '- lettered element details:');
          state.numberedElements.forEach((el, i) => {
            const letter = String.fromCharCode(65 + i);
            utils.debugLog(3, `  ${letter}: "${el.text}" (${el.element.tagName})`);
          });
        }
      };

      annyang.removeCommands();
      annyang.addCommands(commands);
      utils.debugLog(3, 'Commands registered successfully');
      return true;
    } catch (e) {
      utils.trackError(e, 'setupElementAwareCommands');
      return false;
    }
  }

  // Handle prefixed commands
  function handlePrefixedCommand(fullCommand) {
    const commandText = fullCommand.toLowerCase().substring(activeConfig.commandPrefix.length + 1);
    utils.debugLog(3, `Handling prefixed command: "${commandText}"`);
    
    // Check if we're in dictation mode
    if (state.dictationState !== 'inactive' && DICTATION_COMMANDS.hasOwnProperty(commandText)) {
      const handler = DICTATION_COMMANDS[commandText];
      executeDictationCommand(handler);
    } else {
      // Handle as regular command
      showNotification(`Command not recognized: ${commandText}`, 3000, 'warning');
    }
  }

  // Enhanced voice recognition reset
  function resetVoiceRecognition(force = false) {
    if (typeof annyang === 'undefined') return false;

    try {
      updateListeningStatus('inactive');

      if (force) {
        annyang.abort();

        if (state.autoRecoveryTimer) {
          utils.clearTimeout(state.autoRecoveryTimer);
          state.autoRecoveryTimer = null;
        }

        if (activeConfig.enabled) {
          utils.setTimeout(() => {
            try {
              if (activeConfig.enabled && typeof annyang !== 'undefined' && state.dictationState === 'inactive') {
                annyang.start({
                  autoRestart: false,
                  continuous: false
                });
                state.errorRetryCount = 0;
                updateListeningStatus('listening');
                utils.debugLog(3, 'Force restart successful');
                utils.resetErrorTracking();
              }
            } catch (e) {
              utils.trackError(e, 'resetVoiceRecognition.forceRestart');
              showNotification("Voice recognition restart failed", 3000, 'error');
            }
          }, 500);
        }
      }
      return true;
    } catch (e) {
      utils.trackError(e, 'resetVoiceRecognition');
      return false;
    }
  }

  // Enhanced force restart
  function forceRestart() {
    utils.debugLog(3, 'Force restarting speech recognition...');
    
    try {
      if (typeof annyang !== 'undefined') {
        annyang.abort();
      }
      
      state.isProcessingCommand = false;
      clearLeteredOverlays();
      state.recoverySession = null;
      
      if (state.autoRecoveryTimer) {
        utils.clearTimeout(state.autoRecoveryTimer);
        state.autoRecoveryTimer = null;
      }
      
      updateListeningStatus('inactive');
      
      utils.setTimeout(() => {
        if (activeConfig.enabled) {
          try {
            if (state.dictationState === 'inactive') {
              annyang.start({
                autoRestart: false,
                continuous: false
              });
              updateListeningStatus('listening');
            }
            utils.debugLog(3, 'Force restart successful');
            showNotification('Voice recognition restarted', 2000);
            utils.resetErrorTracking();
          } catch (e) {
            utils.trackError(e, 'forceRestart');
            showNotification('Restart failed - please refresh page', 4000, 'error');
          }
        }
      }, 1000);
      
      return true;
    } catch (e) {
      utils.trackError(e, 'forceRestart');
      return false;
    }
  }

  // Enhanced error recovery
  function attemptErrorRecovery(errorType) {
    if (!activeConfig.autoRecover) return false;

    const sessionKey = `recovery_${Date.now().toString().slice(-6)}`;
    if (!state.recoverySession || Date.now() - state.recoverySession.start > 60000) {
      state.recoverySession = { start: Date.now(), count: 0, key: sessionKey };
    }

    state.recoverySession.count++;
    
    if (state.recoverySession.count > activeConfig.maxErrorRetries) {
      utils.debugLog(2, 'Max recovery attempts reached for session');
      showNotification('Voice recognition needs manual restart', 5000, 'warning');
      return false;
    }

    if (state.autoRecoveryTimer) {
      utils.clearTimeout(state.autoRecoveryTimer);
    }

    const baseDelay = activeConfig.errorRetryDelay;
    const backoffMultiplier = Math.pow(2, state.recoverySession.count - 1);
    const recoveryDelay = Math.min(baseDelay * backoffMultiplier, 10000);

    let shouldRecover = true;

    switch(errorType) {
      case 'network':
        showNotification(`Network error. Retrying in ${recoveryDelay/1000}s...`, recoveryDelay);
        break;
      case 'not-allowed':
        showNotification("Microphone access denied", 5000, 'error');
        state.microphoneAccessGranted = false;
        return false;
      case 'aborted':
        showNotification('Voice recognition restarting...', recoveryDelay);
        break;
      case 'audio-capture':
        showNotification('Microphone issue. Retrying...', recoveryDelay, 'warning');
        break;
      case 'no-speech':
        utils.debugLog(4, 'No speech detected, continuing...');
        return false;
      default:
        showNotification(`Voice error (${errorType}). Retrying...`, recoveryDelay, 'warning');
        break;
    }

    if (shouldRecover) {
      state.autoRecoveryTimer = utils.setTimeout(() => {
        resetVoiceRecognition(true);
      }, recoveryDelay);
    }

    return shouldRecover;
  }

  // annyang is bundled inline at the top of this file — no CDN or network request needed
  async function loadAnnyangLibrary() {
    const available = typeof annyang !== 'undefined' && annyang !== null;
    if (!available) {
      utils.debugLog(1, 'annyang not available — bundle may be incomplete');
      showNotification('Voice library failed to initialise', 5000, 'error');
    }
    return available;
  }

  // Enhanced annyang initialization
  function initializeAnnyang() {
    if (typeof annyang === 'undefined') {
      utils.debugLog(1, 'annyang not available');
      return false;
    }

    try {
      setupElementAwareCommands();

      const callbacks = {
        soundstart: () => {
          if (state.dictationState === 'inactive') {
            updateListeningStatus('listening');
          }
          utils.debugLog(4, 'Sound detected');
          
          // If we're waiting for a letter and sound is detected, give visual feedback
          if (state.numberedElements.length > 0 && state.listeningIndicator) {
            state.listeningIndicator.style.background = 'rgba(33, 150, 243, 0.9)';
            state.listeningIndicator.innerHTML = `
              <div class="voicetracking-mic-icon"></div>
              Processing...
            `;
          }
        },

        result: (phrases) => {
          if (state.dictationState === 'inactive') {
            updateListeningStatus('processing');
          }
          utils.debugLog(3, 'Speech result:', phrases);
          
          if (!state.isProcessingCommand && state.dictationState === 'inactive') {
            utils.setTimeout(() => {
              if (state.listeningState === 'processing' && !state.isProcessingCommand) {
                updateListeningStatus('listening');
                utils.debugLog(4, 'Auto-reset from processing to listening');
                
                // Reset listening indicator if active
                if (state.listeningIndicator) {
                  state.listeningIndicator.style.background = 'rgba(76, 175, 80, 0.9)';
                  state.listeningIndicator.innerHTML = `
                    <div class="voicetracking-mic-icon"></div>
                    Listening for letter...
                  `;
                }
              }
            }, 2000);
          }
        },

        resultMatch: (userSaid, commandText, phrases) => {
          utils.debugLog(3, `Command matched: "${userSaid}" -> "${commandText}"`);
          utils.resetErrorTracking();
        },

resultNoMatch: (phrases) => {
  utils.debugLog(3, 'No command match, trying element match:', phrases[0]);
  if (phrases && phrases.length > 0 && state.dictationState === 'inactive') {
    utils.setTimeout(() => {
      const raw = phrases[0];
      // Strip punctuation and lowercase
      const clean = raw.replace(/[^\w\s]/g, '').toLowerCase();
      // Intercept “scroll down” / “scroll up”
      if (clean === 'scroll down' || clean === 'scroll up') {
        clearLeteredOverlays();
        const delta = clean === 'scroll down' ? 300 : -300;
        window.scrollBy({ top: delta, behavior: 'smooth' });
        showFeedback(`Scrolling ${clean.split(' ')[1]}`);
        updateListeningStatus('listening');
        return;  // don’t fall back to element-match
      }
      // Fallback: try clicking an element by name
      const matched = findAndClickElementByName(raw);
      if (!matched) {
        updateListeningStatus('listening');
        showFeedback("Command not recognized");
      }
    }, 100);
  } else if (state.dictationState === 'inactive') {
    updateListeningStatus('listening');
  }
},
        error: (error) => {
          const errorType = error?.error || 'unknown';
          utils.trackError(error, 'annyangCallback.error');

          if (state.isProcessingCommand) {
            utils.debugLog(3, 'Error during command processing, forcing reset');
            state.isProcessingCommand = false;
            clearLeteredOverlays();
          }

          if (errorType === 'aborted') {
            if (activeConfig.enabled && state.dictationState === 'inactive') {
              utils.debugLog(3, 'Restarting after abort');
              utils.setTimeout(() => {
                try {
                  annyang.start({ autoRestart: false, continuous: false });
                } catch (e) {
                  utils.trackError(e, 'annyangCallback.restartAfterAbort');
                }
              }, 500);
            }
          } else if (errorType === 'not-allowed') {
            attemptErrorRecovery(errorType);
          } else if (!['no-speech'].includes(errorType)) {
            if (state.dictationState === 'inactive') {
              updateListeningStatus('error');
              utils.setTimeout(() => {
                updateListeningStatus('listening');
              }, 1000);
            }
            
            const recoverableErrors = ['network', 'audio-capture'];
            if (recoverableErrors.includes(errorType)) {
              attemptErrorRecovery(errorType);
            }
          }
        },

        start: () => {
          if (state.dictationState === 'inactive') {
            updateListeningStatus('listening');
          }
          utils.debugLog(3, 'Speech recognition started');
          
          state.isProcessingCommand = false;
          state.recoverySession = null;
          utils.resetErrorTracking();
        },

        end: () => {
          utils.debugLog(3, 'Speech recognition ended, enabled:', activeConfig.enabled);
          
          if (activeConfig.enabled && !state.isProcessingCommand && state.dictationState === 'inactive') {
            utils.debugLog(3, 'Auto-restarting speech recognition');
            utils.setTimeout(() => {
              try {
                if (activeConfig.enabled && typeof annyang !== 'undefined' && state.dictationState === 'inactive') {
                  annyang.start({ 
                    autoRestart: false,
                    continuous: false
                  });
                }
              } catch (e) {
                utils.trackError(e, 'annyangCallback.autoRestart');
                updateListeningStatus('error');
              }
            }, 300);
          } else if (!activeConfig.enabled) {
            updateListeningStatus('inactive');
          }
        }
      };

      for (const [event, callback] of Object.entries(callbacks)) {
        try {
          annyang.addCallback(event, (...args) => {
            try {
              callback(...args);
            } catch (e) {
              utils.trackError(e, `annyangCallback.${event}`);
              if (state.isProcessingCommand) {
                state.isProcessingCommand = false;
                updateListeningStatus('listening');
              }
            }
          });
        } catch (e) {
          utils.trackError(e, `initializeAnnyang.${event}Callback`);
        }
      }

      return true;
    } catch (e) {
      utils.trackError(e, 'initializeAnnyang');
      return false;
    }
  }

  // NEW: Dictation functionality
  
  // Initialize dictation recognizer
  function initializeDictationRecognizer() {
    if (!utils.detectFeatures().speechRecognition) {
      utils.debugLog(1, 'Speech recognition not supported for dictation');
      return false;
    }

    try {
      const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
      state.dictationRecognizer = new SpeechRecognition();
      
      state.dictationRecognizer.continuous = true;
      state.dictationRecognizer.interimResults = true;
      state.dictationRecognizer.lang = 'en-US';
      state.dictationRecognizer.maxAlternatives = 1;

      // Dictation recognizer callbacks
      state.dictationRecognizer.onstart = () => {
        utils.debugLog(3, 'Dictation recognizer started');
        state.lastDictationActivity = Date.now();
        updateDictationUI(true);
      };

      state.dictationRecognizer.onresult = (event) => {
        const result = event.results[event.results.length - 1];
        const transcript = result[0].transcript;
        
        utils.debugLog(4, 'Dictation result:', transcript, 'Final:', result.isFinal);
        
        state.lastDictationActivity = Date.now();
        
        if (state.isInCommandMode) {
          handleCommandModeDictation(transcript, result.isFinal);
        } else {
          handleRegularDictation(transcript, result.isFinal);
        }
      };

      state.dictationRecognizer.onerror = (error) => {
        utils.trackError(error, 'dictationRecognizer.error');
        
        if (error.error === 'aborted') {
          utils.debugLog(3, 'Dictation aborted');
        } else if (error.error === 'no-speech') {
          // Normal timeout, don't show error
          utils.debugLog(4, 'No speech detected in dictation');
        } else {
          showNotification(`Dictation error: ${error.error}`, 3000, 'error');
        }
        
        if (state.dictationState !== 'inactive') {
          stopDictation();
        }
      };

      state.dictationRecognizer.onend = () => {
        utils.debugLog(3, 'Dictation recognizer ended');
        
        if (state.dictationState !== 'inactive') {
          // Auto-restart if still in dictation mode
          utils.setTimeout(() => {
            if (state.dictationState !== 'inactive' && state.currentInputElement) {
              try {
                state.dictationRecognizer.start();
              } catch (e) {
                utils.debugLog(2, 'Failed to restart dictation:', e);
                stopDictation();
              }
            }
          }, 100);
        }
      };

      return true;
    } catch (e) {
      utils.trackError(e, 'initializeDictationRecognizer');
      return false;
    }
  }

  // Start dictation for a specific element
  function startDictationForElement(element) {
    if (!element || !isTextInputElement(element)) {
      utils.debugLog(2, 'Invalid element for dictation');
      return false;
    }

    if (state.dictationState !== 'inactive') {
      utils.debugLog(3, 'Dictation already active');
      return false;
    }

    utils.debugLog(3, 'Starting dictation for element:', element.tagName, element.type);

    try {
      // Initialize dictation recognizer if needed
      if (!state.dictationRecognizer) {
        if (!initializeDictationRecognizer()) {
          showNotification('Failed to initialize dictation', 3000, 'error');
          return false;
        }
      }

      // Set up state
      state.currentInputElement = element;
      state.dictationState = 'listening';
      state.previewText = '';
      state.dictationHistory = [];
      state.dictationHistoryIndex = -1;
      state.isInCommandMode = false;
      
      // Save initial state for history
      saveDictationHistory();

      // Switch recognizers
      switchToDictationMode();

      // Visual feedback
      element.classList.add('voicetracking-dictating');
      updateListeningStatus('dictating');
      showDictationIndicator();
      
      showNotification('Dictation started. Say "command" for commands.', 3000);
      
      // Reset sleep timer
      resetSleepTimer();
      
      return true;
    } catch (e) {
      utils.trackError(e, 'startDictationForElement');
      stopDictation();
      return false;
    }
  }

  // Switch from annyang to dictation recognizer
  function switchToDictationMode() {
    if (state.isSwitchingRecognizers) {
      utils.debugLog(2, 'Already switching recognizers');
      return;
    }

    state.isSwitchingRecognizers = true;
    
    // Show switching indicator
    if (activeConfig.showRecognizerSwitchIndicator) {
      showSwitchIndicator('Switching to dictation mode...');
    }

    // Stop annyang
    if (typeof annyang !== 'undefined') {
      try {
        annyang.abort();
      } catch (e) {
        utils.debugLog(2, 'Error stopping annyang:', e);
      }
    }

    // Small delay for clean switch
    utils.setTimeout(() => {
      try {
        // Start dictation recognizer
        state.dictationRecognizer.start();
        state.isSwitchingRecognizers = false;
        hideSwitchIndicator();
      } catch (e) {
        utils.trackError(e, 'switchToDictationMode.start');
        state.isSwitchingRecognizers = false;
        hideSwitchIndicator();
        stopDictation();
        showNotification('Failed to start dictation', 3000, 'error');
      }
    }, activeConfig.recognizerSwitchDelay);
  }

  // Switch from dictation back to annyang
  function switchToCommandMode() {
    if (state.isSwitchingRecognizers) {
      utils.debugLog(2, 'Already switching recognizers');
      return;
    }

    state.isSwitchingRecognizers = true;
    
    // Show switching indicator
    if (activeConfig.showRecognizerSwitchIndicator) {
      showSwitchIndicator('Switching to command mode...');
    }

    // Stop dictation recognizer
    if (state.dictationRecognizer) {
      try {
        state.dictationRecognizer.stop();
      } catch (e) {
        utils.debugLog(2, 'Error stopping dictation recognizer:', e);
      }
    }

    // Small delay for clean switch
    utils.setTimeout(() => {
      try {
        // Restart annyang
        if (activeConfig.enabled && typeof annyang !== 'undefined') {
          annyang.start({ autoRestart: false, continuous: false });
        }
        state.isSwitchingRecognizers = false;
        hideSwitchIndicator();
        updateListeningStatus('listening');
      } catch (e) {
        utils.trackError(e, 'switchToCommandMode.start');
        state.isSwitchingRecognizers = false;
        hideSwitchIndicator();
        updateListeningStatus('error');
      }
    }, activeConfig.recognizerSwitchDelay);
  }

  // Handle regular dictation text
  function handleRegularDictation(transcript, isFinal) {
    if (!state.currentInputElement || !state.currentInputElement.isConnected) {
      utils.debugLog(2, 'Input element no longer valid');
      stopDictation();
      return;
    }

    const normalizedTranscript = transcript.toLowerCase().trim();
    
    // Check for command prefix
    if (normalizedTranscript.endsWith(activeConfig.commandPrefix)) {
      utils.debugLog(3, 'Command prefix detected, entering command mode');
      state.isInCommandMode = true;
      state.previewText = '';
      
      // Start command mode timeout
      if (state.commandModeTimeout) {
        utils.clearTimeout(state.commandModeTimeout);
      }
      
      state.commandModeTimeout = utils.setTimeout(() => {
        if (state.isInCommandMode) {
          state.isInCommandMode = false;
          showNotification('Command mode timeout, returning to dictation', 2000);
        }
      }, activeConfig.commandPrefixTimeout);
      
      showFeedback('Command mode active');
      return;
    }

    // Handle preview
    if (activeConfig.autoAcceptOnContinue && state.previewText && isFinal) {
      // Auto-accept previous preview
      acceptPreview();
    }

    // Apply smart formatting if enabled
    let formattedText = transcript;
    if (activeConfig.smartPunctuation && isFinal) {
      formattedText = applySmartPunctuation(formattedText);
    }
    
    if (activeConfig.inputTypeDetection && isFinal) {
      formattedText = applyInputTypeFormatting(formattedText, state.currentInputElement);
    }

    // Show preview
    if (!isFinal) {
      showInlinePreview(formattedText);
    } else {
      state.previewText = formattedText;
      showInlinePreview(formattedText);
      
      // Auto-accept if continuous speech
      if (activeConfig.autoAcceptOnContinue) {
        // Wait a bit to see if more speech is coming
        utils.setTimeout(() => {
          if (state.previewText === formattedText) {
            // No new speech, keep preview
          }
        }, 1000);
      }
    }
  }

  // Handle command mode dictation
function handleCommandModeDictation(transcript, isFinal) {
  if (!isFinal) return;

  let command = transcript.toLowerCase().trim();
  utils.debugLog(3, 'Command mode received (raw):', command);

  const prefixToCheck = activeConfig.commandPrefix.toLowerCase() + ' ';
  if (command.startsWith(prefixToCheck)) {
    command = command.substring(prefixToCheck.length).trim();
    utils.debugLog(3, 'Command mode (stripped prefix):', command);
  }

  // NEW: Remove common trailing punctuation
  command = command.replace(/[.,!?]$/, "").trim();
  utils.debugLog(3, 'Command mode (sanitized for punctuation):', command);


  if (state.commandModeTimeout) {
    utils.clearTimeout(state.commandModeTimeout);
    state.commandModeTimeout = null;
  }

  if (DICTATION_COMMANDS.hasOwnProperty(command)) {
    state.isInCommandMode = false;
    const handler = DICTATION_COMMANDS[command]; // Make sure this line is there
    executeDictationCommand(handler);
  } else if (command.startsWith('go to field ')) {
    state.isInCommandMode = false; // Make sure this line is there
    // For 'go to field ', the actual field letter is part of the command,
    // so the punctuation removal above might affect it if not handled carefully.
    // However, "go to field A." would become "go to field A" which is fine.
    const letter = command.substring('go to field '.length).toUpperCase().trim(); // Ensure letter is also trimmed
    navigateToField(letter);
  } else {
    showNotification(`Unknown command: ${command}`, 2000, 'warning');
    state.commandModeTimeout = utils.setTimeout(() => {
      if (state.isInCommandMode) {
        state.isInCommandMode = false;
        showNotification('Command mode timeout, returning to dictation', 2000);
        utils.debugLog(3, 'Command mode timed out after unknown command.');
      }
    }, activeConfig.commandPrefixTimeout);
  }
}
  // Execute dictation command
  function executeDictationCommand(handler) {
    utils.debugLog(3, 'Executing dictation command:', handler);
    
    switch (handler) {
      case 'deleteLastWord':
        deleteLastWord();
        break;
      case 'clearInput':
        clearInput();
        break;
      case 'undoLastAction':
        undoLastAction();
        break;
      case 'acceptPreview':
        acceptPreview();
        break;
      case 'rejectPreview':
        rejectPreview();
        break;
      case 'showFieldNavigation':
        showFieldNavigation();
        break;
      case 'stopDictation':
        stopDictation();
        break;
      case 'insertNewLine':
        insertNewLine();
        break;
      case 'continueDictation':
        // Just continue
        showFeedback('Continuing dictation');
        break;
      default:
        utils.debugLog(2, 'Unknown dictation command handler:', handler);
    }
  }

  // Delete last word
  function deleteLastWord() {
    if (!state.currentInputElement) return;
    
    saveDictationHistory();
    
    const currentValue = state.currentInputElement.value;
    const words = currentValue.trim().split(/\s+/);
    
    if (words.length > 0 && words[0] !== '') {
      words.pop();
      state.currentInputElement.value = words.join(' ') + (words.length > 0 ? ' ' : '');
      showFeedback('Last word deleted');
    } else {
      showFeedback('Nothing to delete');
    }
  }

  // Clear input
  function clearInput() {
    if (!state.currentInputElement) return;
    
    saveDictationHistory();
    
    state.currentInputElement.value = '';
    state.previewText = '';
    clearInlinePreview();
    showFeedback('Input cleared');
  }

  // Undo last action
  function undoLastAction() {
    if (state.dictationHistory.length === 0 || state.dictationHistoryIndex <= 0) {
      showFeedback('Nothing to undo');
      return;
    }
    
    state.dictationHistoryIndex--;
    const previousState = state.dictationHistory[state.dictationHistoryIndex];
    
    if (state.currentInputElement) {
      state.currentInputElement.value = previousState;
      showFeedback('Undone');
    }
  }

  // Save dictation history
  function saveDictationHistory() {
    if (!state.currentInputElement) return;
    
    const currentValue = state.currentInputElement.value;
    
    // Don't save if it's the same as the last entry
    if (state.dictationHistory.length > 0 && 
        state.dictationHistory[state.dictationHistory.length - 1] === currentValue) {
      return;
    }
    
    // Add to history
    state.dictationHistory.push(currentValue);
    state.dictationHistoryIndex = state.dictationHistory.length - 1;
    
    // Limit history size
    if (activeConfig.dictationHistoryLimit > 0 && 
        state.dictationHistory.length > activeConfig.dictationHistoryLimit) {
      state.dictationHistory.shift();
      state.dictationHistoryIndex--;
    }
  }

  // Show inline preview
  function showInlinePreview(text) {
    if (!state.currentInputElement || !text) return;
    
    clearInlinePreview();
    
    // Create preview element
    const preview = document.createElement('span');
    preview.className = 'voicetracking-preview-text';
    preview.textContent = text;
    preview.style.cssText = `
      color: ${activeConfig.dictationPreviewColor};
      opacity: ${activeConfig.dictationPreviewOpacity};
      font-style: italic;
    `;
    
    // For now, show in feedback
    showFeedback(`Preview: ${text}`);
    state.previewElement = preview;
  }

  // Clear inline preview
  function clearInlinePreview() {
    if (state.previewElement && state.previewElement.parentNode) {
      state.previewElement.parentNode.removeChild(state.previewElement);
    }
    state.previewElement = null;
  }

  // Accept preview
  function acceptPreview() {
    if (!state.previewText || !state.currentInputElement) {
      showFeedback('No preview to accept');
      return;
    }
    
    saveDictationHistory();
    
    // Add preview text to input
    const currentValue = state.currentInputElement.value;
    const newValue = currentValue + (currentValue && !currentValue.endsWith(' ') ? ' ' : '') + state.previewText;
    state.currentInputElement.value = newValue;
    
    // Clear preview
    state.previewText = '';
    clearInlinePreview();
    showFeedback('Accepted');
  }

  // Reject preview
  function rejectPreview() {
    state.previewText = '';
    clearInlinePreview();
    showFeedback('Rejected');
  }

  // Insert new line
  function insertNewLine() {
    if (!state.currentInputElement) return;
    
    if (state.currentInputElement.tagName === 'TEXTAREA') {
      saveDictationHistory();
      state.currentInputElement.value += '\n';
      showFeedback('New line added');
    } else {
      showFeedback('New lines only work in text areas');
    }
  }

  // Show field navigation
  function showFieldNavigation() {
    // Find all text input fields
    const textInputs = Array.from(document.querySelectorAll(TEXT_INPUT_SELECTORS.join(',')))
      .filter(el => isElementVisible(el) && el !== state.currentInputElement);
    
    if (textInputs.length === 0) {
      showNotification('No other text fields found', 2000);
      return;
    }
    
    // Show lettered overlays for fields
    const fieldElements = textInputs.slice(0, 26).map(el => ({
      element: el,
      text: extractElementText(el) || el.placeholder || 'Text field',
      normalizedText: normalizeText(extractElementText(el) || el.placeholder || 'Text field'),
      isTextInput: true
    }));
    
    state.fieldNavigationActive = true;
    state.fieldNavigationElements = fieldElements;
    
    showLeteredDuplicates(fieldElements, 'field navigation');
    showNotification('Say "go to field [letter]" to navigate', 5000);
  }

  // Navigate to field
  function navigateToField(letter) {
    const index = letter.charCodeAt(0) - 65; // Convert A to 0, B to 1, etc.
    
    if (index >= 0 && index < state.fieldNavigationElements.length) {
      const targetElement = state.fieldNavigationElements[index].element;
      
      // Accept current preview if any
      if (state.previewText) {
        acceptPreview();
      }
      
      // Stop current dictation
      stopDictation();
      
      // Focus and start dictation on new field
      targetElement.focus();
      targetElement.click();
      
      utils.setTimeout(() => {
        startDictationForElement(targetElement);
      }, 500);
      
      clearLeteredOverlays();
      state.fieldNavigationActive = false;
      state.fieldNavigationElements = [];
    } else {
      showNotification('Invalid field selection', 2000, 'warning');
    }
  }

  // Stop dictation
  function stopDictation() {
    if (state.dictationState === 'inactive') {
      utils.debugLog(3, 'Dictation already inactive');
      return;
    }
    
    utils.debugLog(3, 'Stopping dictation');
    
    // Accept any pending preview
    if (state.previewText) {
      acceptPreview();
    }
    
    // Clean up
    if (state.currentInputElement) {
      state.currentInputElement.classList.remove('voicetracking-dictating');
    }
    
    // Stop recognizer
    if (state.dictationRecognizer) {
      try {
        state.dictationRecognizer.stop();
      } catch (e) {
        utils.debugLog(2, 'Error stopping dictation recognizer:', e);
      }
    }
    
    // Clear state
    state.dictationState = 'inactive';
    state.currentInputElement = null;
    state.previewText = '';
    state.isInCommandMode = false;
    state.dictationHistory = [];
    state.dictationHistoryIndex = -1;
    
    // Clear timers
    if (state.commandModeTimeout) {
      utils.clearTimeout(state.commandModeTimeout);
      state.commandModeTimeout = null;
    }
    
    if (state.sleepModeTimer) {
      utils.clearTimeout(state.sleepModeTimer);
      state.sleepModeTimer = null;
    }
    
    // Clear UI
    clearInlinePreview();
    hideDictationIndicator();
    clearLeteredOverlays();
    
    // Switch back to command mode
    switchToCommandMode();
    
    showNotification('Dictation stopped', 2000);
  }

  // Toggle dictation
  function toggleDictation() {
    if (state.dictationState === 'inactive') {
      const activeElement = document.activeElement;
      if (isTextInputElement(activeElement)) {
        startDictationForElement(activeElement);
      } else {
        showNotification('Please focus on a text input field first', 3000, 'warning');
      }
    } else {
      stopDictation();
    }
  }

  // Reset dictation state
  function resetDictationState() {
    state.dictationState = 'inactive';
    state.currentInputElement = null;
    state.previewText = '';
    state.previewElement = null;
    state.dictationHistory = [];
    state.dictationHistoryIndex = -1;
    state.isInCommandMode = false;
    state.fieldNavigationActive = false;
    state.fieldNavigationElements = [];
    
    if (state.commandModeTimeout) {
      utils.clearTimeout(state.commandModeTimeout);
      state.commandModeTimeout = null;
    }
    
    if (state.sleepModeTimer) {
      utils.clearTimeout(state.sleepModeTimer);
      state.sleepModeTimer = null;
    }
    
    clearInlinePreview();
    hideDictationIndicator();
  }

  // Update dictation UI
  function updateDictationUI(active) {
    if (active) {
      showDictationIndicator();
    } else {
      hideDictationIndicator();
    }
  }

  // Show dictation indicator
  function showDictationIndicator() {
    if (state.dictationIndicatorElement) return;
    
    const indicator = document.createElement('div');
    indicator.className = 'voicetracking-dictation-indicator';
    indicator.innerHTML = `
      <div class="voicetracking-mic-icon"></div>
      <span>Dictating...</span>
    `;
    
    document.body.appendChild(indicator);
    state.dictationIndicatorElement = indicator;
  }

  // Hide dictation indicator
  function hideDictationIndicator() {
    if (state.dictationIndicatorElement && state.dictationIndicatorElement.parentNode) {
      state.dictationIndicatorElement.parentNode.removeChild(state.dictationIndicatorElement);
      state.dictationIndicatorElement = null;
    }
  }

  // Show switch indicator
  function showSwitchIndicator(message) {
    if (state.switchIndicatorElement) return;
    
    const indicator = document.createElement('div');
    indicator.className = 'voicetracking-switch-indicator';
    indicator.textContent = message;
    
    document.body.appendChild(indicator);
    state.switchIndicatorElement = indicator;
  }

  // Hide switch indicator
  function hideSwitchIndicator() {
    if (state.switchIndicatorElement && state.switchIndicatorElement.parentNode) {
      state.switchIndicatorElement.parentNode.removeChild(state.switchIndicatorElement);
      state.switchIndicatorElement = null;
    }
  }

  // Apply smart punctuation
  function applySmartPunctuation(text) {
    if (!text) return text;
    
    // Capitalize first letter
    text = text.charAt(0).toUpperCase() + text.slice(1);
    
    // Add period at end if missing
    if (!/[.!?]$/.test(text.trim())) {
      text = text.trim() + '.';
    }
    
    // Capitalize after periods
    text = text.replace(/\. ([a-z])/g, (match, letter) => `. ${letter.toUpperCase()}`);
    
    return text;
  }

  // Apply input type specific formatting
function applyInputTypeFormatting(text, element) {
    if (!element) return text; //
    
    // --- Add this line to remove trailing punctuation ---
    if (typeof text === 'string') { // Ensure text is a string before using string methods
        text = text.replace(/[.,!?]$/, "").trim();
    }
    // --- End of addition ---

    const inputType = element.type || element.tagName.toLowerCase(); //
    
    switch (inputType) { //
      case 'email':
        // Convert "at" to "@" and "dot" to "." //
        text = text.replace(/\s+at\s+/gi, '@'); //
        text = text.replace(/\s+dot\s+/gi, '.'); //
        text = text.replace(/\s+/g, ''); // Remove all spaces //
        break;
        
      case 'number':
        const numberWords = { //
          'zero': '0', 'one': '1', 'two': '2', 'three': '3', 'four': '4', //
          'five': '5', 'six': '6', 'seven': '7', 'eight': '8', 'nine': '9', //
          'ten': '10', 'eleven': '11', 'twelve': '12', 'thirteen': '13', //
          'fourteen': '14', 'fifteen': '15', 'sixteen': '16', 'seventeen': '17', //
          'eighteen': '18', 'nineteen': '19', 'twenty': '20', 'thirty': '30', //
          'forty': '40', 'fifty': '50', 'sixty': '60', 'seventy': '70', //
          'eighty': '80', 'ninety': '90', 'hundred': '100', 'thousand': '1000' //
        };
        
        for (const [word, digit] of Object.entries(numberWords)) { //
          const regex = new RegExp(`\\b${word}\\b`, 'gi'); //
          text = text.replace(regex, digit); //
        }
        
        // Handle dash as minus //
        text = text.replace(/\s+dash\s+/gi, '-'); //
        
        // Remove non-numeric characters except . and - //
        text = text.replace(/[^0-9.-]/g, ''); //
        break;
        
      case 'tel':
        // Handle phone numbers //
        text = text.replace(/\s+dash\s+/gi, '-'); //
        text = text.replace(/[^0-9-+()]/g, ''); //
        break;
        
      case 'url':
        // Basic URL formatting //
        text = text.replace(/\s+dot\s+/gi, '.'); //
        text = text.replace(/\s+slash\s+/gi, '/'); //
        text = text.replace(/\s+colon\s+/gi, ':'); //
        text = text.replace(/\s+/g, ''); // Remove spaces //
        break;
    }
    
    return text;
  }

  // Reset sleep timer
  function resetSleepTimer() {
    if (state.sleepModeTimer) {
      utils.clearTimeout(state.sleepModeTimer);
    }
    
    state.sleepModeTimer = utils.setTimeout(() => {
      if (state.dictationState !== 'inactive') {
        utils.debugLog(3, 'Sleep mode activated due to inactivity');
        stopDictation();
        showNotification('Dictation stopped due to inactivity', 3000);
      }
    }, activeConfig.sleepModeTimeout);
  }

  // Enhanced start function
  // Enhanced start function - UPDATED to not load external scripts
  export async function start() {
    if (activeConfig.enabled) {
      utils.debugLog(3, 'Already enabled');
      return true;
    }

    try {
      // Check if annyang is available (should be loaded via manifest)
      if (typeof annyang === 'undefined') {
        showNotification('Speech recognition library not loaded', 5000, 'error');
        return false;
      }

      if (!await testMicrophoneAccess()) {
        return false;
      }

      if (!initializeAnnyang()) {
        showNotification('Failed to initialize voice recognition', 5000, 'error');
        return false;
      }

      setupElementAwareCommands();
      
      // Add scroll command handling at start
      annyang.addCallback('result', phrases => {
        const raw = phrases[0];
        // normalize: strip punctuation, lowercase
        const text = raw.replace(/[^\w\s]/g, '').toLowerCase().trim();

        if (text === 'scroll down' || text === 'scroll up') {
          clearLeteredOverlays();
          const delta = (text === 'scroll down') ? 300 : -300;
          window.scrollBy({ top: delta, behavior: 'smooth' });
          showFeedback(`Scrolling ${text.split(' ')[1]}`);
          updateListeningStatus('listening');
          return;   // exit early so Annyang won't try any other commands
        }
      });
      
      annyang.start({
        autoRestart: false,
        continuous: false
      });

      activeConfig.enabled = true;
      state.isProcessingCommand = false;
      clearLeteredOverlays();
      updateListeningStatus('listening');

      showNotification("Voice control activated! Say element names, then letters to select. Say 'write' to dictate text.", 6000);

      utils.setTimeout(() => {
        showClickableElements();
      }, 1500);

      saveSettings();
      utils.debugLog(3, 'Started successfully with enhanced dictation support');
      utils.resetErrorTracking();
      return true;

    } catch (e) {
      utils.trackError(e, 'start');
      showNotification(`Failed to start: ${e.message}`, 5000, 'error');
      return false;
    }
  }


  // Enhanced stop function
  export function stop() {
    if (!activeConfig.enabled) {
      utils.debugLog(3, 'Already disabled');
      return true;
    }

    try {
      // Stop dictation if active
      if (state.dictationState !== 'inactive') {
        stopDictation();
      }

      if (typeof annyang !== 'undefined') {
        annyang.abort();
      }

      activeConfig.enabled = false;
      state.isProcessingCommand = false;
      clearLeteredOverlays();
      updateListeningStatus('inactive');
      clearElementHighlights();

      if (state.autoRecoveryTimer) {
        utils.clearTimeout(state.autoRecoveryTimer);
        state.autoRecoveryTimer = null;
      }

      showNotification("Voice control deactivated", 2000);
      saveSettings();
      utils.debugLog(3, 'Stopped successfully');
      return true;

    } catch (e) {
      utils.trackError(e, 'stop');
      return false;
    }
  }

  // Enhanced toggle function
  function toggle() {
    try {
      return activeConfig.enabled ? stop() : start();
    } catch (e) {
      utils.trackError(e, 'toggle');
      return false;
    }
  }

  // Enhanced mutation observer
  function setupMutationObserver() {
    if (!utils.detectFeatures().mutationObserver) {
      utils.debugLog(2, 'MutationObserver not supported');
      return false;
    }

    try {
      const debouncedRescan = utils.debounce(() => {
        if (activeConfig.enabled) {
          utils.debugLog(4, 'DOM changed, rescanning...');
          scanPageForClickableElements();
        }
      }, activeConfig.debounceDelay);

      state.mutationObserver = new MutationObserver(mutations => {
        try {
          const shouldRescan = mutations.some(mutation =>
            mutation.type === 'childList' ||
            (mutation.type === 'attributes' &&
             ['style', 'class', 'value', 'aria-label', 'title'].includes(mutation.attributeName))
          );

          if (shouldRescan) {
            state.cache.delete('clickableElements');
            debouncedRescan();
          }
        } catch (e) {
          utils.trackError(e, 'mutationObserver.callback');
        }
      });

      state.mutationObserver.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['style', 'class', 'value', 'aria-label', 'title']
      });

      state.documentObserverActive = true;
      utils.debugLog(3, 'MutationObserver configured');
      return true;
    } catch (e) {
      utils.trackError(e, 'setupMutationObserver');
      return false;
    }
  }

  // Enhanced settings management
  function saveSettings() {
    if (!activeConfig.persistSettings || !utils.isLocalStorageAvailable()) return false;

    try {
      const settings = {
        enabled: activeConfig.enabled,
        widgetPosition: activeConfig.widgetPosition,
        widgetSize: activeConfig.widgetSize,
        showVisualFeedback: activeConfig.showVisualFeedback,
        debugLevel: activeConfig.debugLevel,
        dictationEnabled: activeConfig.dictationEnabled,
        autoStartDictation: activeConfig.autoStartDictation,
        commandPrefix: activeConfig.commandPrefix,
        timestamp: Date.now()
      };

      if (state.elements.widget) {
        settings.widgetLeft = state.elements.widget.style.left;
        settings.widgetTop = state.elements.widget.style.top;
      }

      const settingsString = JSON.stringify(settings);
      localStorage.setItem('voicetracking-settings', settingsString);
      utils.debugLog(4, 'Settings saved');
      return true;
    } catch (e) {
      utils.trackError(e, 'saveSettings');
      return false;
    }
  }

  // Enhanced settings loading
  function loadSettings() {
    if (!activeConfig.persistSettings || !utils.isLocalStorageAvailable()) return false;

    try {
      const saved = localStorage.getItem('voicetracking-settings');
      if (!saved) return false;

      const settings = JSON.parse(saved);
      
      if (!settings.timestamp || Date.now() - settings.timestamp > 7 * 24 * 60 * 60 * 1000) {
        utils.debugLog(3, 'Saved settings too old, ignoring');
        return false;
      }

      if (utils.validateConfig(settings)) {
        Object.assign(activeConfig, settings);

        if (state.elements.widget && settings.widgetLeft && settings.widgetTop) {
          state.elements.widget.style.left = settings.widgetLeft;
          state.elements.widget.style.top = settings.widgetTop;
          state.elements.widget.style.right = 'auto';
          state.elements.widget.style.bottom = 'auto';
        }

        utils.debugLog(3, 'Settings loaded');
        return true;
      } else {
        utils.debugLog(2, 'Invalid saved settings, using defaults');
        return false;
      }
    } catch (e) {
      utils.trackError(e, 'loadSettings');
      return false;
    }
  }

  // Enhanced widget position saving
  function saveWidgetPosition() {
    if (!activeConfig.persistSettings || !utils.isLocalStorageAvailable() || !state.elements.widget) return false;

    try {
      const current = JSON.parse(localStorage.getItem('voicetracking-settings') || '{}');
      current.widgetLeft = state.elements.widget.style.left;
      current.widgetTop = state.elements.widget.style.top;
      current.timestamp = Date.now();
      
      localStorage.setItem('voicetracking-settings', JSON.stringify(current));
      utils.debugLog(4, 'Widget position saved');
      return true;
    } catch (e) {
      utils.trackError(e, 'saveWidgetPosition');
      return false;
    }
  }

  // Enhanced compatibility checking
  function checkCompatibility() {
    const features = utils.detectFeatures();
    const issues = [];

    if (!features.speechRecognition) {
      issues.push('Speech Recognition API not supported');
    }

    if (!features.mediaDevices) {
      issues.push('Microphone access not supported');
    }

    if (!features.secureContext) {
      issues.push('HTTPS required for microphone access');
    }

    if (!features.localStorage) {
      issues.push('localStorage not available - settings will not persist');
    }

    return { features, issues };
  }

  // Enhanced cleanup function
  function cleanup() {
    try {
      if (activeConfig.enabled) {
        stop();
      }

      // Stop dictation if active
      if (state.dictationState !== 'inactive') {
        stopDictation();
      }

      // Clear all timers
      for (const timer of state.timers) {
        clearTimeout(timer);
      }
      state.timers.clear();

      // Remove all event listeners
      for (const [element, events] of state.eventListeners) {
        for (const [event, {handler, options}] of events) {
          try {
            element.removeEventListener(event, handler, options);
          } catch (e) {
            utils.debugLog(2, 'Error removing event listener:', e);
          }
        }
      }
      state.eventListeners.clear();

      // Disconnect observers
      if (state.mutationObserver) {
        state.mutationObserver.disconnect();
        state.mutationObserver = null;
        state.documentObserverActive = false;
      }

      if (state.intersectionObserver) {
        state.intersectionObserver.disconnect();
        state.intersectionObserver = null;
      }

      clearElementHighlights();
      clearLeteredOverlays();

      // Remove all DOM elements
      const elementsToRemove = [
        '#voicetracking-styles',
        '.voicetracking-widget',
        '.voicetracking-feedback',
        '.voicetracking-notification',
        '.voicetracking-sr-only',
        '.voicetracking-countdown',
        '.voicetracking-listening-indicator',
        '.voicetracking-element-label',
        '.voicetracking-dictation-indicator',
        '.voicetracking-switch-indicator',
        '.voicetracking-accept-reject'
      ];

      elementsToRemove.forEach(selector => {
        document.querySelectorAll(selector).forEach(el => {
          if (el.parentNode) el.parentNode.removeChild(el);
        });
      });

      // Reset state
      Object.assign(state, {
        listeningState: 'inactive',
        clickableElements: [],
        elementHighlights: new Map(),
        isInitialized: false,
        isProcessingCommand: false,
        elements: {},
        cache: new Map(),
        libraryLoadAttempts: 0,
        commandCounter: 0,
        numberedOverlays: new Map(),
        numberedElements: [],
        numberedOverlayTimeout: null,
        currentDuplicateBase: null,
        listeningIndicator: null,
        countdownElement: null,
        countdownInterval: null,
        isLoadingLibrary: false,
        loadingLibraryPromise: null,
        consecutiveErrors: 0,
        lastErrorTime: 0,
        documentObserverActive: false,
        visibleElements: new Set(),
        localStorageAvailable: null,
        // Dictation state
        dictationState: 'inactive',
        dictationRecognizer: null,
        currentInputElement: null,
        previewText: '',
        previewElement: null,
        dictationHistory: [],
        dictationHistoryIndex: -1,
        isInCommandMode: false,
        commandModeTimeout: null,
        lastDictationActivity: Date.now(),
        sleepModeTimer: null,
        isSwitchingRecognizers: false,
        switchIndicatorElement: null,
        acceptRejectTimeout: null,
        fieldNavigationActive: false,
        fieldNavigationElements: [],
        dictationIndicatorElement: null
      });

      utils.debugLog(3, 'Cleanup completed');
      return true;
    } catch (e) {
      utils.debugLog(1, 'Cleanup error:', e);
      return false;
    }
  }

  // Enhanced initialization
  async function initialize() {
    utils.debugLog(3, 'Initializing v4.0.0 - Enhanced with Voice Dictation...');

    try {
      if (!utils.isDOMReady()) {
        utils.debugLog(2, 'DOM not ready, retrying initialization...');
        utils.setTimeout(initialize, 100);
        return;
      }

      const compatibility = checkCompatibility();
      
      if (compatibility.issues.length > 0) {
        showNotification(`Voice control may not work: ${compatibility.issues.join(', ')}`, 8000, 'warning');
        utils.debugLog(2, 'Compatibility issues:', compatibility.issues);
      }

      createStyles();
      createUI();
      loadSettings();
      setupMutationObserver();
      setupIntersectionObserver();
      scanPageForClickableElements();

      showNotification('Voice control ready! Click Start or press Ctrl+Shift+V', 5000);

      if (activeConfig.autoStart && activeConfig.enabled) {
        utils.debugLog(3, 'Auto-starting...');
        utils.setTimeout(() => start(), 1000);
      }

      state.isInitialized = true;
      utils.debugLog(3, 'Initialization complete');
      utils.resetErrorTracking();

    } catch (e) {
      utils.trackError(e, 'initialize');
      showNotification('Voice control initialization failed', 5000, 'error');
    }
  }



  // Set the loaded flag
  window.VoiceTrackingLoaded = true;

  // Enhanced keyboard shortcuts
  utils.addEventListener(document, 'keydown', (e) => {
    try {
      // Ctrl+Shift+V to toggle
      if (e.ctrlKey && e.shiftKey && e.key === 'V') {
        e.preventDefault();
        toggle();
      }

      // Ctrl+Shift+S to show elements
      if (e.ctrlKey && e.shiftKey && e.key === 'S') {
        e.preventDefault();
        showClickableElements();
      }

      // Ctrl+Shift+R to reset state
      if (e.ctrlKey && e.shiftKey && e.key === 'R') {
        e.preventDefault();
        utils.resetCommandState();
        resetDictationState();
        state.cache.clear();
        scanPageForClickableElements();
        showNotification('Voice control state reset', 2000);
      }

      // Ctrl+Shift+F to force restart
      if (e.ctrlKey && e.shiftKey && e.key === 'F') {
        e.preventDefault();
        forceRestart();
      }

      // Ctrl+Shift+E for emergency recovery
      if (e.ctrlKey && e.shiftKey && e.key === 'E') {
        e.preventDefault();
        window.VoiceTracking.emergencyRecovery();
      }

      // Ctrl+Shift+T to test lettered overlays
      if (e.ctrlKey && e.shiftKey && e.key === 'T') {
        e.preventDefault();
        window.VoiceTracking.testLeteredOverlays();
      }

      // Ctrl+Shift+X to extend timeout
      if (e.ctrlKey && e.shiftKey && e.key === 'X') {
        e.preventDefault();
        window.VoiceTracking.extendTimeout();
      }

      // Ctrl+Shift+D for debug info
      if (e.ctrlKey && e.shiftKey && e.key === 'D') {
        e.preventDefault();
        window.VoiceTracking.debugLeteredState();
        console.log('Performance Info:', window.VoiceTracking.getPerformanceInfo());
        console.log('Error Info:', window.VoiceTracking.getErrorInfo());
      }

      // Ctrl+Shift+W to toggle dictation
      if (e.ctrlKey && e.shiftKey && e.key === 'W') {
        e.preventDefault();
        toggleDictation();
      }
    } catch (error) {
      utils.trackError(error, 'keyboardShortcut');
    }
  });

  // Enhanced cleanup on unload
  utils.addEventListener(window, 'beforeunload', cleanup);

  // Enhanced visibility change handling
  utils.addEventListener(document, 'visibilitychange', () => {
    if (typeof annyang === 'undefined' || !activeConfig.enabled) return;

    try {
      if (document.hidden) {
        if (annyang.isListening && annyang.isListening()) {
          annyang.pause();
          utils.debugLog(3, 'Paused due to page hidden');
        }
        
        if (state.dictationState !== 'inactive') {
          stopDictation();
        }
      } else {
        if (state.dictationState === 'inactive' && annyang.resume) {
          annyang.resume();
        } else if (state.dictationState === 'inactive') {
          annyang.start({ autoRestart: false, continuous: false });
        }
        utils.debugLog(3, 'Resumed due to page visible');
      }
    } catch (e) {
      utils.trackError(e, 'visibilityChange');
    }
  });

  // Enhanced window focus/blur handling
  utils.addEventListener(window, 'focus', () => {
    if (activeConfig.enabled && typeof annyang !== 'undefined' && state.dictationState === 'inactive') {
      utils.debugLog(3, 'Window focused, ensuring speech recognition is active');
      utils.setTimeout(() => {
        try {
          if (annyang.isListening && !annyang.isListening()) {
            annyang.start({ autoRestart: false, continuous: false });
          }
        } catch (e) {
          utils.trackError(e, 'windowFocus.restartRecognition');
        }
      }, 500);
    }
  });

  utils.addEventListener(window, 'blur', () => {
    if (activeConfig.enabled && typeof annyang !== 'undefined') {
      utils.debugLog(4, 'Window blurred');
    }
  });

  // Handle page reload gracefully
  utils.addEventListener(window, 'beforeunload', (e) => {
    if (activeConfig.enabled && state.isProcessingCommand) {
      utils.debugLog(3, 'Page unloading while processing command');
      state.isProcessingCommand = false;
      clearLeteredOverlays();
    }
    
    if (state.dictationState !== 'inactive') {
      stopDictation();
    }
  });

  // Enhanced error boundary
  utils.addEventListener(window, 'error', (e) => {
    if (e.error && e.error.toString().includes('VoiceTracking')) {
      utils.trackError(e.error, 'unhandledError');
      
      if (state.isProcessingCommand) {
        utils.debugLog(3, 'Resetting state due to unhandled error');
        state.isProcessingCommand = false;
        clearLeteredOverlays();
        updateListeningStatus('listening');
      }
    }
  });

  // Enhanced promise rejection handler
  utils.addEventListener(window, 'unhandledrejection', (e) => {
    if (e.reason && e.reason.toString().includes('VoiceTracking')) {
      utils.trackError(e.reason, 'unhandledPromiseRejection');
      
      if (state.isProcessingCommand) {
        utils.debugLog(3, 'Resetting state due to unhandled promise rejection');
        state.isProcessingCommand = false;
        clearLeteredOverlays();
        updateListeningStatus('listening');
      }
    }
  });

  // Enhanced performance monitoring
  let lastPerformanceCheck = Date.now();
  const performanceMonitor = utils.throttle(() => {
    const currentTime = Date.now();
    const timeSinceLastCheck = currentTime - lastPerformanceCheck;
    
    if (timeSinceLastCheck > 30000) { // 30 seconds
      const perfInfo = window.VoiceTracking.getPerformanceInfo();
      utils.debugLog(3, 'Performance check - Elements:', state.clickableElements.length, 
                  'State:', state.listeningState, 'Processing:', state.isProcessingCommand,
                  'Timers:', perfInfo.timersActive, 'Listeners:', perfInfo.eventListenersActive,
                  'Dictation:', state.dictationState);
      
      // Memory leak detection
      if (perfInfo.timersActive > 50) {
        utils.debugLog(2, 'Potential timer leak detected:', perfInfo.timersActive, 'active timers');
      }
      
      if (perfInfo.eventListenersActive > 100) {
        utils.debugLog(2, 'Potential event listener leak detected:', perfInfo.eventListenersActive, 'active listeners');
      }
      
      // Clean up any stuck states
      if (state.isProcessingCommand && currentTime - state.lastCommandTime > 10000) {
        utils.debugLog(2, 'Command processing stuck, force resetting');
        state.isProcessingCommand = false;
        clearLeteredOverlays();
        updateListeningStatus('listening');
      }
      
      // Clean up orphaned visual elements
      if (!state.numberedElements.length && (state.countdownElement || state.listeningIndicator)) {
        utils.debugLog(2, 'Found orphaned visual elements, cleaning up');
        clearLeteredOverlays();
      }
      
      // Check dictation state
      if (state.dictationState !== 'inactive' && (!state.currentInputElement || !state.currentInputElement.isConnected)) {
        utils.debugLog(2, 'Dictation active but input element lost, stopping');
        stopDictation();
      }
      
      lastPerformanceCheck = currentTime;
    }
  }, 5000);

  // Run performance monitor periodically
  setInterval(performanceMonitor, 10000);

  // Auto-focus detection for dictation
  utils.addEventListener(document, 'focusin', (e) => {
    if (activeConfig.enabled && 
        activeConfig.dictationEnabled && 
        activeConfig.autoStartDictation &&
        state.dictationState === 'inactive' &&
        isTextInputElement(e.target)) {
      
      utils.debugLog(3, 'Text input focused, considering auto-dictation');
      
      // Small delay to ensure focus is stable
      utils.setTimeout(() => {
        if (document.activeElement === e.target && state.dictationState === 'inactive') {
          startDictationForElement(e.target);
        }
      }, 500);
    }
  });

  utils.debugLog(3, 'VoiceTracking.js v4.0.0 - Enhanced with Voice Dictation loaded successfully!');
  utils.debugLog(3, '');
  utils.debugLog(3, '🎯 ENHANCED WORKFLOW WITH LETTER-BASED SELECTION:');
  utils.debugLog(3, '• Say element name (e.g., "submit") → lettered overlays appear with countdown');
  utils.debugLog(3, '• Get 20 FULL SECONDS with visual countdown timer');
  utils.debugLog(3, '• Say just the LETTER (e.g., "A", "B", "C") → clicks that element');
  utils.debugLog(3, '');
  utils.debugLog(3, '✍️ NEW VOICE DICTATION FEATURES:');
  utils.debugLog(3, '• Say "write" when focused on text input to start dictation');
  utils.debugLog(3, '• Auto-starts when clicking text fields (configurable)');
  utils.debugLog(3, '• Live preview of text before committing');
  utils.debugLog(3, '• Say "command" to enter command mode for 7 seconds');
  utils.debugLog(3, '');
  utils.debugLog(3, '📝 DICTATION COMMANDS:');
  utils.debugLog(3, '• "command delete word" - Delete last word');
  utils.debugLog(3, '• "command clear all" - Clear entire field');
  utils.debugLog(3, '• "command undo" - Undo last action');
  utils.debugLog(3, '• "command next field" - Show field navigation');
  utils.debugLog(3, '• "command done writing" - Stop dictation');
  utils.debugLog(3, '• "command new line" - Add line break (textarea only)');
  utils.debugLog(3, '');
  utils.debugLog(3, '🔤 SMART FORMATTING:');
  utils.debugLog(3, '• Email fields: "john at gmail dot com" → "john@gmail.com"');
  utils.debugLog(3, '• Number fields: "twenty three" → "23"');
  utils.debugLog(3, '• Auto-capitalization and punctuation');
  utils.debugLog(3, '• Input-type specific formatting');
  utils.debugLog(3, '');
  utils.debugLog(3, '⌨️ KEYBOARD SHORTCUTS:');
  utils.debugLog(3, '• Ctrl+Shift+V: Toggle voice control');
  utils.debugLog(3, '• Ctrl+Shift+W: Toggle dictation mode');
  utils.debugLog(3, '• Ctrl+Shift+S: Show clickable elements');
  utils.debugLog(3, '• Ctrl+Shift+R: Reset state');
  utils.debugLog(3, '• Ctrl+Shift+F: Force restart speech recognition');
  utils.debugLog(3, '• Ctrl+Shift+E: Emergency recovery');
  utils.debugLog(3, '• Ctrl+Shift+T: Test lettered overlays');
  utils.debugLog(3, '• Ctrl+Shift+X: Extend timeout manually');
  utils.debugLog(3, '• Ctrl+Shift+D: Show debug information');
  utils.debugLog(3, '');
  utils.debugLog(3, 'Ready to use with full voice control and dictation!');
   
