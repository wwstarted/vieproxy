// ============================================
// ARCHIVE PROXIES V2 - JAVASCRIPT (SSR version)
// ============================================

(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    // ── 1. MOBILE FILTER MODAL ────────────────────────────────
    const filterModal = document.getElementById("filterModal");
    const openModalBtn = document.getElementById("openFilterModal");
    const closeModalBtn = document.getElementById("closeFilterModal");
    const cancelBtn = document.getElementById("cancelFilter");

    function openModal() {
      if (!filterModal) return;
      filterModal.classList.add("active");
      document.body.style.overflow = "hidden";
    }

    function closeModal() {
      if (!filterModal) return;
      filterModal.classList.remove("active");
      document.body.style.overflow = "";
    }

    if (openModalBtn) openModalBtn.addEventListener("click", openModal);
    if (closeModalBtn) closeModalBtn.addEventListener("click", closeModal);
    if (cancelBtn) cancelBtn.addEventListener("click", closeModal);

    // Click backdrop để đóng
    if (filterModal) {
      filterModal.addEventListener("click", function (e) {
        if (e.target === filterModal) closeModal();
      });
    }

    // Escape key
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && filterModal?.classList.contains("active")) {
        closeModal();
      }
    });

    // ── 2. LOADING STATE khi submit filter/search ─────────────
    // Search form submit
    const filterForm = document.getElementById("proxyFilterForm");
    const productsGrid = document.getElementById("productsGrid");

    if (filterForm) {
      filterForm.addEventListener("submit", function () {
        showLoading();
      });
    }

    // Category/sort link clicks → loading
    document
      .querySelectorAll(
        ".filter-option-v2, .filter-sort-link, .pagination-btn:not([disabled])",
      )
      .forEach(function (el) {
        el.addEventListener("click", function () {
          if (this.tagName === "A") showLoading();
        });
      });

    function showLoading() {
      if (productsGrid) {
        productsGrid.style.opacity = "0.45";
        productsGrid.style.transition = "opacity 0.2s ease";
        productsGrid.style.pointerEvents = "none";
      }
    }

    // ── 3. SEARCH: submit khi nhấn Enter ─────────────────────
    // (đã handle bởi form submit ở trên, nhưng đảm bảo UX)
    const searchInput = document.getElementById("proxySearchInput");
    if (searchInput) {
      searchInput.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
          showLoading();
          // Form sẽ tự submit
        }
      });
    }

    // ── 4. CARD ENTRANCE ANIMATION ────────────────────────────
    const cards = document.querySelectorAll(".product-card-v2");
    cards.forEach(function (card, index) {
      card.style.opacity = "0";
      card.style.transform = "translateY(16px)";
      card.style.transition = "none";

      setTimeout(function () {
        card.style.transition = "opacity 0.35s ease, transform 0.35s ease";
        card.style.opacity = "1";
        card.style.transform = "translateY(0)";
      }, index * 60);
    });

    console.log("Archive Proxies V2 (SSR) loaded ✓");
  });
})();
