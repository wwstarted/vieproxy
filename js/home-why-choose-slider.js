(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    const slider = document.querySelector(".home-why-slider");
    const track = document.querySelector(".home-why-slider-lines");
    const cards = document.querySelectorAll(".home-why-card");

    if (!slider || !track || cards.length === 0) return;

    const total = cards.length;
    let currentSlide = 0;
    let autoInterval = null;

    // ── Inject thanh xanh ─────────────────────────
    const bar = document.createElement("div");
    bar.className = "home-why-slider-lines__bar";
    track.appendChild(bar);

    // =========================================================
    // Detect card active thật dựa trên scroll position
    // =========================================================
    function updateBarFromScroll() {
      const sliderRect = slider.getBoundingClientRect();

      let activeIndex = 0;
      let maxVisible = 0;

      cards.forEach((card, i) => {
        const rect = card.getBoundingClientRect();

        const visible =
          Math.min(rect.right, sliderRect.right) -
          Math.max(rect.left, sliderRect.left);

        const visibleWidth = Math.max(0, visible);

        if (visibleWidth > maxVisible) {
          maxVisible = visibleWidth;
          activeIndex = i;
        }
      });

      // FIX CUỐI SLIDER
      const maxScroll = slider.scrollWidth - slider.clientWidth;

      if (slider.scrollLeft >= maxScroll - 2) {
        activeIndex = total - 1;
      }

      currentSlide = activeIndex;

      const segment = 100 / total;
      bar.style.width = segment + "%";
      bar.style.left = segment * activeIndex + "%";
    }

    // cập nhật khi scroll (drag / touch / auto)
    slider.addEventListener("scroll", () => {
      requestAnimationFrame(updateBarFromScroll);
    });

    // =========================================================
    // Scroll tới card
    // =========================================================
    function goTo(index) {
      if (index < 0 || index >= total) return;

      const gap = parseInt(getComputedStyle(slider).gap) || 20;
      const cardW = cards[0].offsetWidth;

      slider.scrollTo({
        left: (cardW + gap) * index,
        behavior: "smooth",
      });
    }

    // =========================================================
    // Auto slide
    // =========================================================
    function startAuto() {
      autoInterval = setInterval(() => {
        goTo((currentSlide + 1) % total);
      }, 4000);
    }

    function stopAuto() {
      clearInterval(autoInterval);
      autoInterval = null;
    }

    slider.addEventListener("mouseenter", stopAuto);
    slider.addEventListener("mouseleave", () => {
      if (!isDragging) setTimeout(startAuto, 2000);
    });

    // =========================================================
    // Drag mouse
    // =========================================================
    let isDragging = false;
    let startX, startLeft;

    slider.addEventListener("mousedown", (e) => {
      isDragging = true;
      startX = e.pageX - slider.offsetLeft;
      startLeft = slider.scrollLeft;
      slider.classList.add("is-grabbing");
      stopAuto();
    });

    document.addEventListener("mouseup", () => {
      if (!isDragging) return;
      isDragging = false;
      slider.classList.remove("is-grabbing");
      setTimeout(startAuto, 2000);
    });

    slider.addEventListener("mousemove", (e) => {
      if (!isDragging) return;
      e.preventDefault();
      slider.scrollLeft =
        startLeft - (e.pageX - slider.offsetLeft - startX) * 1.5;
    });

    // =========================================================
    // Touch
    // =========================================================
    let tX, tLeft;

    slider.addEventListener(
      "touchstart",
      (e) => {
        tX = e.touches[0].pageX;
        tLeft = slider.scrollLeft;
        stopAuto();
      },
      { passive: true },
    );

    slider.addEventListener(
      "touchmove",
      (e) => {
        slider.scrollLeft = tLeft - (e.touches[0].pageX - tX) * 1.5;
      },
      { passive: true },
    );

    slider.addEventListener("touchend", () => {
      setTimeout(startAuto, 2000);
    });

    // =========================================================
    // Init
    // =========================================================
    updateBarFromScroll();
    startAuto();

    window.addEventListener("resize", updateBarFromScroll);
  });
})();
