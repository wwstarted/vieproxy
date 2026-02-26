/**
 * page-about.js
 * VieProxy — Trang Về Chúng Tôi
 * Handles: Entrance animations cho hero + article
 */
(function () {
  "use strict";

  function qs(sel, ctx) {
    return (ctx || document).querySelector(sel);
  }
  function qsa(sel, ctx) {
    return Array.from((ctx || document).querySelectorAll(sel));
  }

  /* ────────────────────────────────────────────────────────
     ENTRANCE ANIMATIONS
  ─────────────────────────────────────────────────────── */
  function initAnimations() {
    var reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    if (reduced) return;

    var targets = [qs(".about-hero__content"), qs(".about-article")];

    targets.forEach(function (el, i) {
      if (!el) return;
      el.style.opacity = "0";
      el.style.transform = "translateY(22px)";
      el.style.transition = "none";

      var obs = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                  el.style.transition =
                    "opacity 0.55s ease " +
                    i * 0.1 +
                    "s, " +
                    "transform 0.55s ease " +
                    i * 0.1 +
                    "s";
                  el.style.opacity = "1";
                  el.style.transform = "translateY(0)";
                });
              });
              obs.unobserve(entry.target);
            }
          });
        },
        { rootMargin: "0px 0px -30px 0px", threshold: 0.05 },
      );

      obs.observe(el);
    });
  }

  /* ────────────────────────────────────────────────────────
     INIT
  ─────────────────────────────────────────────────────── */
  function init() {
    if (!qs(".main-about-page")) return;
    initAnimations();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
