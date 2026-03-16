// ============================================
// VIEPROXY BLOG - JAVASCRIPT
// ============================================

(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    // ── 1. Entrance animation cho blog cards ──────────────────
    const cards = document.querySelectorAll(".blog-card");
    if (cards.length > 0) {
      cards.forEach(function (card, index) {
        card.style.opacity = "0";
        card.style.transform = "translateY(20px)";
        card.style.transition = "none";

        // Staggered entrance
        setTimeout(function () {
          card.style.transition = "opacity 0.4s ease, transform 0.4s ease";
          card.style.opacity = "1";
          card.style.transform = "translateY(0)";
        }, index * 80);
      });
    }

    // ── 2. Image lazy-load fade-in ────────────────────────────
    const images = document.querySelectorAll(".blog-card__image");
    images.forEach(function (img) {
      img.style.opacity = "0";
      img.style.transition = "opacity 0.45s ease";

      if (img.complete && img.naturalWidth > 0) {
        img.style.opacity = "1";
      } else {
        img.addEventListener("load", function () {
          this.style.opacity = "1";
        });
        img.addEventListener("error", function () {
          // Fallback nếu ảnh lỗi: ẩn wrap để không bị vỡ layout
          this.closest(".blog-card__image-wrap").style.background = "#f0f7ff";
          this.style.opacity = "0";
        });
      }
    });

    // ── 3. Filter loading state khi click category ────────────
    const filterBtns = document.querySelectorAll(".blog-filter-btn");
    filterBtns.forEach(function (btn) {
      btn.addEventListener("click", function () {
        const grid = document.getElementById("blogGrid");
        if (grid) {
          grid.style.opacity = "0.45";
          grid.style.transition = "opacity 0.2s ease";
          grid.style.pointerEvents = "none";
        }
      });
    });

    // ── 4. Search: submit khi nhấn Enter, debounce không cần
    const searchInput = document.querySelector(".blog-search-input");
    if (searchInput) {
      searchInput.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
          const grid = document.getElementById("blogGrid");
          if (grid) {
            grid.style.opacity = "0.45";
            grid.style.pointerEvents = "none";
          }
        }
      });
    }

    // ── 5. Smooth scroll to top khi click pagination ──────────
    const paginationLinks = document.querySelectorAll(
      ".blog-pagination__number, .blog-pagination__btn",
    );
    paginationLinks.forEach(function (el) {
      if (!el.disabled) {
        el.addEventListener("click", function () {
          if (this.tagName === "A") {
            window.scrollTo({ top: 0, behavior: "smooth" });
          }
        });
      }
    });

    // ── 6. Active filter button sync  ──
    const currentUrl = new URL(window.location.href);
    const activeCat = currentUrl.searchParams.get("category") || "all";

    filterBtns.forEach(function (btn) {
      const btnHref = btn.getAttribute("href");
      if (!btnHref) return;

      const btnUrl = new URL(btnHref, window.location.origin);
      const btnCat = btnUrl.searchParams.get("category") || "all";

      btn.classList.toggle("active", btnCat === activeCat);
    });

    console.log("Vieproxy Blog loaded ✓");
  });
})();
