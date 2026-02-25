(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    var fields = document.querySelectorAll(
      ".home-hero-order-field.has-dropdown",
    );
    var activePositioner = null;

    fields.forEach(function (field) {
      var trigger = field.querySelector(".home-order-field__select");
      var dropdown = field.querySelector(".home-order-dropdown");

      if (!trigger || !dropdown) return;

      // Function to position dropdown for this specific field
      var positionDropdown = function () {
        var rect = field.getBoundingClientRect();

        // Position dropdown below the field (fixed position)
        dropdown.style.top = rect.bottom + 10 + "px";
        dropdown.style.left = rect.left - 1 + "px";
        dropdown.style.minWidth = rect.width + "px";
      };

      // Toggle dropdown on click
      trigger.addEventListener("click", function (e) {
        e.stopPropagation();
        var isOpen = field.classList.contains("is-open");

        // Close all first
        closeAllDropdowns();

        if (!isOpen) {
          field.classList.add("is-open");
          trigger.setAttribute("aria-expanded", "true");

          // Position dropdown
          positionDropdown();

          // Store current positioner and add listeners
          activePositioner = positionDropdown;
          window.addEventListener("scroll", activePositioner);
          window.addEventListener("resize", activePositioner);
        }
      });

      // Select item
      var items = dropdown.querySelectorAll(".home-order-dropdown__item");
      items.forEach(function (item) {
        item.addEventListener("click", function (e) {
          e.stopPropagation();

          // Update selected state
          items.forEach(function (i) {
            i.classList.remove("is-selected");
          });
          item.classList.add("is-selected");

          // Update trigger display value
          var valueEl = trigger.querySelector(".home-order-field__value");
          if (valueEl) {
            // Clone item content except the emoji/flag span for display
            var itemText = item.textContent.trim();

            // Handle flag image
            var flagImg = item.querySelector("img.home-order-flag");
            var existingFlag = trigger.querySelector("img.home-order-flag");

            if (flagImg && existingFlag) {
              existingFlag.src = flagImg.src;
              existingFlag.alt = flagImg.alt;
            } else if (flagImg && !existingFlag) {
              // Insert flag before value text
              var clonedFlag = flagImg.cloneNode(true);
              trigger.insertBefore(clonedFlag, valueEl);
            }

            // Handle emoji flag
            var flagEmoji = item.querySelector(".home-order-flag-emoji");
            var existingEmoji = trigger.querySelector(".home-order-flag-emoji");
            if (flagEmoji) {
              if (existingEmoji) {
                existingEmoji.textContent = flagEmoji.textContent;
              } else {
                var clonedEmoji = flagEmoji.cloneNode(true);
                trigger.insertBefore(clonedEmoji, valueEl);
              }
            }

            valueEl.textContent = itemText
              .replace(/[\u{1F1E0}-\u{1F1FF}]{2}/gu, "") // strip emoji flags
              .trim();
          }

          closeAllDropdowns();
        });
      });
    });

    // Close on outside click
    document.addEventListener("click", closeAllDropdowns);

    function closeAllDropdowns() {
      fields.forEach(function (f) {
        f.classList.remove("is-open");
        var t = f.querySelector(".home-order-field__select");
        if (t) t.setAttribute("aria-expanded", "false");
      });

      // Remove scroll/resize listeners if active
      if (activePositioner) {
        window.removeEventListener("scroll", activePositioner);
        window.removeEventListener("resize", activePositioner);
        activePositioner = null;
      }
    }

    // Keyboard: close on Escape
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") closeAllDropdowns();
    });
  });
})();
