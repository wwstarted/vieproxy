/**
 * Header interactions
 * - Language dropdown toggle
 * - Mobile hamburger menu
 */
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    // ── Language dropdown ──────────────────────────────────────
    var langToggle = document.getElementById("langToggle");
    var langDropdown = document.getElementById("langDropdown");

    if (langToggle && langDropdown) {
      langToggle.addEventListener("click", function (e) {
        e.stopPropagation();
        var isOpen = langDropdown.classList.contains("is-open");
        langDropdown.classList.toggle("is-open", !isOpen);
        langToggle.setAttribute("aria-expanded", String(!isOpen));
      });

      // Close when clicking outside
      document.addEventListener("click", function () {
        langDropdown.classList.remove("is-open");
        langToggle.setAttribute("aria-expanded", "false");
      });

      langDropdown.addEventListener("click", function (e) {
        e.stopPropagation();
      });
    }

    // ── Mobile hamburger ───────────────────────────────────────
    var hamburger = document.getElementById("mobileMenuToggle");
    var headerRight = document.querySelector(".header-right");

    if (hamburger && headerRight) {
      hamburger.addEventListener("click", function () {
        hamburger.classList.toggle("is-active");
        headerRight.classList.toggle("is-open");
        hamburger.setAttribute(
          "aria-label",
          headerRight.classList.contains("is-open") ? "Đóng menu" : "Mở menu",
        );
      });
    }
  });
})();
