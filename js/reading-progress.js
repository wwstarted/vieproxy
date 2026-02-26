/**
 * reading-progress.js
 * VieProxy — Reading Progress Bar
 * Load riêng cho từng page template cần thiết.
 * Không phụ thuộc vào info-pages.js hay bất kỳ file nào khác.
 */
(function () {
  "use strict";

  function initReadingProgress() {
    // Tránh tạo bar trùng nếu script bị load 2 lần
    if (document.getElementById("readingProgressBar")) return;

    var bar = document.createElement("div");
    bar.id = "readingProgressBar";
    document.body.appendChild(bar);

    var docEl = document.documentElement;

    function updateProgress() {
      var scrollTop = window.pageYOffset || docEl.scrollTop;
      var docHeight = docEl.scrollHeight - docEl.clientHeight;
      if (docHeight <= 0) return;
      var pct = Math.min(100, Math.round((scrollTop / docHeight) * 100));
      bar.style.width = pct + "%";
    }

    window.addEventListener("scroll", updateProgress, { passive: true });
    updateProgress(); // Chạy ngay khi load trang
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initReadingProgress);
  } else {
    initReadingProgress();
  }
})();
