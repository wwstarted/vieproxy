/**
 * back-to-top.js
 * VieProxy — Global Back To Top Button
 */
(function () {
  "use strict";

  function initBackToTop() {
    var btn = document.getElementById("backToTop");
    if (!btn) return;

    // Đảm bảo dùng class global, không phụ thuộc info-pages
    window.addEventListener(
      "scroll",
      function () {
        if (window.pageYOffset > 400) {
          btn.classList.add("is-visible");
        } else {
          btn.classList.remove("is-visible");
        }
      },
      { passive: true },
    );

    btn.addEventListener("click", function () {
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initBackToTop);
  } else {
    initBackToTop();
  }
})();
