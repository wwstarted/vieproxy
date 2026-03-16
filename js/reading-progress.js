(function () {
  "use strict";

  function initReadingProgress() {
    if (document.getElementById("readingProgressBar")) return;

    var bar = document.createElement("div");
    bar.id = "readingProgressBar";
    bar.setAttribute("role", "progressbar");
    bar.setAttribute("aria-valuemin", "0");
    bar.setAttribute("aria-valuemax", "100");
    bar.setAttribute("aria-valuenow", "0");
    document.body.appendChild(bar);

    var siteHeader = document.querySelector(".site-header");

    function syncTop() {
      var h = siteHeader ? siteHeader.offsetHeight : 0;
      bar.style.top = h + "px";
    }

    syncTop();

    window.addEventListener("resize", syncTop, { passive: true });

    var rafId = null;
    var lastPct = -1;

    function calcPercent() {
      var scrollTop = window.scrollY || window.pageYOffset;
      var docHeight =
        document.documentElement.scrollHeight -
        document.documentElement.clientHeight;
      if (docHeight <= 0) return 0;
      return Math.min(100, (scrollTop / docHeight) * 100);
    }

    // ── Ghi DOM (chỉ khi % thực sự thay đổi) ─────────────────
    function renderBar(pct) {
      var rounded = Math.round(pct * 10) / 10;
      if (rounded === lastPct) return;
      lastPct = rounded;
      bar.style.width = rounded + "%";
      bar.setAttribute("aria-valuenow", Math.round(rounded));
    }

    function onScroll() {
      if (rafId) return;
      rafId = requestAnimationFrame(function () {
        renderBar(calcPercent());
        rafId = null;
      });
    }

    window.addEventListener("scroll", onScroll, { passive: true });
    renderBar(calcPercent());
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initReadingProgress);
  } else {
    initReadingProgress();
  }
})();
