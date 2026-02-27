/**
 * about.js
 * VieProxy — Trang Về Chúng Tôi
 * Tái sử dụng TOC logic từ info-pages.js + counter animation riêng
 */

(function () {
  "use strict";

  /* ── Utils ─────────────────────────────────────────────── */
  function qs(sel, ctx) {
    return (ctx || document).querySelector(sel);
  }
  function qsa(sel, ctx) {
    return Array.from((ctx || document).querySelectorAll(sel));
  }

  /* ────────────────────────────────────────────────────────
     STAT COUNTER ANIMATION
  ─────────────────────────────────────────────────────── */
  function initCounters() {
    var statBar = qs(".about-stat-bar");
    if (!statBar) return;

    var nums = qsa(".about-stat-item__num", statBar);
    if (!nums.length) return;

    var prefersReduced = window.matchMedia(
      "(prefers-reduced-motion: reduce)",
    ).matches;

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;

          nums.forEach(function (el) {
            var target = parseInt(el.getAttribute("data-count"), 10);
            if (!target) return;

            if (prefersReduced) {
              el.textContent = target.toLocaleString();
              return;
            }

            var start = 0;
            var duration = 1800;
            var startTime = null;

            function step(timestamp) {
              if (!startTime) startTime = timestamp;
              var elapsed = timestamp - startTime;
              var progress = Math.min(elapsed / duration, 1);
              // easeOutExpo
              var eased = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
              var current = Math.floor(eased * target);
              el.textContent = current.toLocaleString();
              if (progress < 1) requestAnimationFrame(step);
            }

            requestAnimationFrame(step);
          });

          observer.disconnect();
        });
      },
      { threshold: 0.4 },
    );

    observer.observe(statBar);
  }

  /* ────────────────────────────────────────────────────────
     TIMELINE ENTRANCE ANIMATION
  ─────────────────────────────────────────────────────── */
  function initTimelineAnimation() {
    var items = qsa(".about-timeline-item");
    if (!items.length) return;

    var prefersReduced = window.matchMedia(
      "(prefers-reduced-motion: reduce)",
    ).matches;
    if (prefersReduced) return;

    items.forEach(function (item) {
      item.style.opacity = "0";
      item.style.transform = "translateX(-16px)";
      item.style.transition = "none";
    });

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          var el = entry.target;
          var idx = items.indexOf(el);
          setTimeout(function () {
            el.style.transition = "opacity 0.45s ease, transform 0.45s ease";
            el.style.opacity = "1";
            el.style.transform = "translateX(0)";
          }, idx * 100);
          observer.unobserve(el);
        });
      },
      { threshold: 0.15 },
    );

    items.forEach(function (item) {
      observer.observe(item);
    });
  }

  /* ────────────────────────────────────────────────────────
     COMMITMENT ITEM ANIMATION
  ─────────────────────────────────────────────────────── */
  function initCommitmentAnimation() {
    var items = qsa(".about-commitment-item");
    if (!items.length) return;

    var prefersReduced = window.matchMedia(
      "(prefers-reduced-motion: reduce)",
    ).matches;
    if (prefersReduced) return;

    items.forEach(function (item) {
      item.style.opacity = "0";
      item.style.transform = "translateY(12px)";
      item.style.transition = "none";
    });

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          var el = entry.target;
          var idx = items.indexOf(el);
          setTimeout(function () {
            el.style.transition = "opacity 0.4s ease, transform 0.4s ease";
            el.style.opacity = "1";
            el.style.transform = "translateY(0)";
          }, idx * 80);
          observer.unobserve(el);
        });
      },
      { threshold: 0.1 },
    );

    items.forEach(function (item) {
      observer.observe(item);
    });
  }

  /* ────────────────────────────────────────────────────────
     CULTURE / TECH CARD ANIMATION (reuse pattern)
  ─────────────────────────────────────────────────────── */
  function initCardGroupAnimation() {
    var prefersReduced = window.matchMedia(
      "(prefers-reduced-motion: reduce)",
    ).matches;
    if (prefersReduced) return;

    var groups = qsa(
      ".about-culture-cards, .about-tech-grid, .about-mission-grid",
    );
    groups.forEach(function (group) {
      var cards = qsa(
        ".about-culture-card, .about-tech-item, .about-mission-card",
        group,
      );

      cards.forEach(function (card) {
        card.style.opacity = "0";
        card.style.transform = "translateY(14px)";
        card.style.transition = "none";
        void card.offsetHeight;
      });

      var obs = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            var children = qsa(
              ".about-culture-card, .about-tech-item, .about-mission-card",
              entry.target,
            );
            children.forEach(function (card, i) {
              requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                  card.style.transition =
                    "opacity 0.4s ease " +
                    i * 0.07 +
                    "s, transform 0.4s ease " +
                    i * 0.07 +
                    "s";
                  card.style.opacity = "1";
                  card.style.transform = "translateY(0)";
                });
              });
            });
            obs.unobserve(entry.target);
          });
        },
        { threshold: 0.1 },
      );

      obs.observe(group);
    });
  }

  /* ────────────────────────────────────────────────────────
     INIT — chạy sau info-pages.js đã khởi tạo TOC
  ─────────────────────────────────────────────────────── */
  function init() {
    if (!qs(".main-about-page")) return;

    initCounters();
    initTimelineAnimation();
    initCommitmentAnimation();
    initCardGroupAnimation();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
