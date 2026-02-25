(function ($) {
  "use strict";

  $(window).on("load", function () {
    const $wrapper = $(".home-pricing-slider-wrapper");
    const $slider = $(".home-pricing-slider");
    const $cards = $slider.find(".pricing-card");
    const $prevBtn = $(".home-pricing-slider__arrow--prev");
    const $nextBtn = $(".home-pricing-slider__arrow--next");
    const $dotsContainer = $(".home-pricing-slider__dots");

    if (!$slider.length || $cards.length === 0) return;

    const totalCards = $cards.length;
    let currentPage = 0;
    let cardsPerPage = 4;
    let cardWidth = 0; // tính trong setCardWidths(), dùng trong goToPage()
    let $dots;

    /* ── Tính cardsPerPage theo viewport ──────────────────── */
    function updateCardsPerPage() {
      const vw = $(window).width();
      if (vw <= 480) {
        cardsPerPage = 1; // Mobile: 1 card
      } else if (vw <= 1024) {
        cardsPerPage = 3; // Tablet: 3 cards
      } else if (vw <= 1080) {
        cardsPerPage = 5; // HD: 5 cards
      } else {
        cardsPerPage = 4; // Full HD+: 4 cards
      }
    }

    /* ── Tổng số trang ────────────────────────────────────── */
    function getTotalPages() {
      return Math.ceil(totalCards / cardsPerPage);
    }

    /* ── Sinh dots ────────────────────────────────────────── */
    function generateDots() {
      const total = getTotalPages();
      $dotsContainer.empty();

      for (let i = 0; i < total; i++) {
        const $dot = $('<div class="home-pricing-slider__dot"></div>');
        if (i === 0) $dot.addClass("is-active");
        $dotsContainer.append($dot);
      }

      $dots = $(".home-pricing-slider__dot");

      $dots.on("click", function () {
        const idx = $(this).index();
        goToPage(idx);
      });
    }

    /* ── Di chuyển đến trang ──────────────────────────────── */
    function goToPage(page, animate) {
      const total = getTotalPages();
      page = Math.max(0, Math.min(page, total - 1));
      currentPage = page;

      // Offset = trang * số card/trang * (cardWidth + gap)
      const stepW = getStepWidth();
      const offset = -(currentPage * cardsPerPage * stepW);

      if (animate === false) {
        $slider.css({
          transition: "none",
          transform: `translateX(${offset}px)`,
        });
        // Force reflow rồi bật lại transition
        $slider[0].offsetHeight;
        $slider.css("transition", "");
      } else {
        $slider.css("transform", `translateX(${offset}px)`);
      }

      updateControls();
    }

    /* ── Cập nhật arrows + dots ───────────────────────────── */
    function updateControls() {
      const total = getTotalPages();

      $prevBtn.toggleClass("is-disabled", currentPage <= 0);
      $nextBtn.toggleClass("is-disabled", currentPage >= total - 1);

      if ($dots && $dots.length) {
        $dots.removeClass("is-active");
        $dots.eq(currentPage).addClass("is-active");
      }
    }

    /* ── Đặt width cố định cho từng card theo trang ──────── */
    function setCardWidths() {
      const wrapperEl = $wrapper[0];
      const wrapperTotal = wrapperEl.offsetWidth;
      const wrapperPadL = parseInt($wrapper.css("padding-left")) || 0;
      const wrapperPadR = parseInt($wrapper.css("padding-right")) || 0;
      const availWidth = wrapperTotal - wrapperPadL - wrapperPadR;
      const gap = parseInt($slider.css("gap")) || 0;

      cardWidth = Math.floor(
        (availWidth - gap * (cardsPerPage - 1)) / cardsPerPage,
      );

      $cards.css({ width: cardWidth + "px", "flex-shrink": "0" });
    }

    /* ── Tính width 1 bước trượt (card + gap) ────────────── */
    function getStepWidth() {
      const gap = parseInt($slider.css("gap")) || 0;
      return cardWidth + gap;
    }

    /* ── Khởi tạo ─────────────────────────────────────────── */
    function init() {
      updateCardsPerPage();
      setCardWidths();
      generateDots();
      currentPage = 0;
      goToPage(0, false);
    }

    /* ── Arrow buttons ────────────────────────────────────── */
    $prevBtn.on("click", function () {
      if (currentPage > 0) goToPage(currentPage - 1);
    });

    $nextBtn.on("click", function () {
      if (currentPage < getTotalPages() - 1) goToPage(currentPage + 1);
    });

    /* ── Resize ───────────────────────────────────────────── */
    let resizeTimer;
    $(window).on("resize", function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function () {
        const oldCpp = cardsPerPage;
        updateCardsPerPage();
        setCardWidths();

        if (oldCpp !== cardsPerPage) {
          currentPage = 0;
          generateDots();
          goToPage(0, false);
        } else {
          // Breakpoint không đổi, chỉ recalculate position
          goToPage(currentPage, false);
        }
      }, 150);
    });

    /* ── Touch / Swipe ────────────────────────────────────── */
    let touchStartX = 0;
    let touchEndX = 0;

    $slider.on("touchstart", function (e) {
      touchStartX = e.originalEvent.touches[0].clientX;
    });

    $slider.on("touchmove", function (e) {
      touchEndX = e.originalEvent.touches[0].clientX;
    });

    $slider.on("touchend", function () {
      const diff = touchStartX - touchEndX;
      if (Math.abs(diff) > 50) {
        if (diff > 0) {
          $nextBtn.trigger("click"); // Swipe left → next
        } else {
          $prevBtn.trigger("click"); // Swipe right → prev
        }
      }
    });

    /* ── Start ────────────────────────────────────────────── */
    init();
  });
})(jQuery);
