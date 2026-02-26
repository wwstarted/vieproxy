/**
 * speed-dial.js
 * VieProxy — Global Contact Speed Dial Widget
 * Depends on: speed-dial.css
 */

(function () {
  "use strict";

  /* ── Icons to cycle through (order matches quick-strip) ── */
  var CYCLE_ICONS = [
    "fa-solid fa-headset", // 1. initial / hỗ trợ
    "fa-solid fa-envelope", // 2. Email
    "fa-brands fa-facebook-f", // 3. Facebook
    "fa-solid fa-comment-dots", // 4. Zalo
    "fa-brands fa-telegram", // 5. Telegram
  ];

  var CYCLE_INTERVAL = 2000; // ms per icon
  var FADE_DURATION = 220; // ms — phải khớp CSS transition

  /* ── State ───────────────────────────────────────────── */
  var isOpen = false;
  var cycleIndex = 0;
  var cycleTimer = null;

  /* ── DOM refs (populated in init) ───────────────────── */
  var dial, mainBtn, cycleIconEl;

  /* ── Helpers ─────────────────────────────────────────── */
  function qs(sel, ctx) {
    return (ctx || document).querySelector(sel);
  }

  /* ─────────────────────────────────────────────────────
     ICON CYCLING
  ───────────────────────────────────────────────────── */
  function setIcon(cls) {
    cycleIconEl.className = "";
    cls.split(" ").forEach(function (c) {
      cycleIconEl.classList.add(c);
    });
  }

  function cycleNext() {
    cycleIndex = (cycleIndex + 1) % CYCLE_ICONS.length;

    // Fade out
    cycleIconEl.classList.add("is-fading");

    setTimeout(function () {
      setIcon(CYCLE_ICONS[cycleIndex]);
      // Fade in
      cycleIconEl.classList.remove("is-fading");
    }, FADE_DURATION);
  }

  function startCycling() {
    if (cycleTimer) return;
    cycleTimer = setInterval(cycleNext, CYCLE_INTERVAL);
  }

  function stopCycling() {
    clearInterval(cycleTimer);
    cycleTimer = null;
  }

  /* ─────────────────────────────────────────────────────
     OPEN / CLOSE
  ───────────────────────────────────────────────────── */
  function openDial() {
    isOpen = true;
    dial.classList.add("is-open");
    mainBtn.setAttribute("aria-expanded", "true");
    stopCycling();
  }

  function closeDial() {
    isOpen = false;
    dial.classList.remove("is-open");
    mainBtn.setAttribute("aria-expanded", "false");
    startCycling();
  }

  function toggleDial() {
    if (isOpen) {
      closeDial();
    } else {
      openDial();
    }
  }

  /* ─────────────────────────────────────────────────────
     KEYBOARD
  ───────────────────────────────────────────────────── */
  function onKeyDown(e) {
    if (e.key === "Escape" && isOpen) {
      closeDial();
      mainBtn.focus();
    }
  }

  /* ─────────────────────────────────────────────────────
     INIT
  ───────────────────────────────────────────────────── */
  function init() {
    dial = qs(".speed-dial");
    if (!dial) return;

    mainBtn = qs(".speed-dial__main", dial);
    cycleIconEl = qs(".speed-dial__icon--cycle i", dial);

    if (!mainBtn || !cycleIconEl) return;

    /* Set initial icon */
    setIcon(CYCLE_ICONS[0]);

    /* Main button click */
    mainBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      toggleDial();
    });

    /* Child links: close on click (allow href to fire) */
    var children = dial.querySelectorAll(".speed-dial__child");
    children.forEach(function (child) {
      child.addEventListener("click", function () {
        closeDial();
      });
    });

    /* Keyboard */
    document.addEventListener("keydown", onKeyDown);

    /* Start cycling */
    startCycling();

    /* Pause cycling when tab is hidden (battery saving) */
    document.addEventListener("visibilitychange", function () {
      if (document.hidden) {
        stopCycling();
      } else if (!isOpen) {
        startCycling();
      }
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
