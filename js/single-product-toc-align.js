/**
 * Single Product - Auto Align TOC với Short Description
 * Tự động tính toán và align TOC sidebar với dòng đầu tiên của short description
 */
(function ($) {
  "use strict";

  $(window).on("load", function () {
    alignTOCWithShortDescription();

    // Re-align khi resize window
    let resizeTimer;
    $(window).on("resize", function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function () {
        alignTOCWithShortDescription();
      }, 150);
    });
  });

  function alignTOCWithShortDescription() {
    const $toc = $(".single-product-toc");
    const $shortDesc = $(".single-product-short-description");
    const $rightColumn = $(".single-product-right-column");

    if (!$toc.length || !$shortDesc.length || !$rightColumn.length) {
      return;
    }

    // Reset margin-top trước khi tính
    $toc.css("margin-top", "0");

    // Lấy position của short description và right column
    const rightColumnTop = $rightColumn.offset().top;
    const shortDescTop = $shortDesc.offset().top;

    // Tính khoảng cách từ top của right column đến short description
    const offsetNeeded = shortDescTop - rightColumnTop;

    // Trừ đi khoảng cách của pricing widget + gap
    const $pricingWidget = $(".single-product-pricing-widget");
    const pricingWidgetHeight = $pricingWidget.length
      ? $pricingWidget.outerHeight(true) // include margin
      : 0;

    // Gap giữa pricing widget và TOC (từ CSS)
    const gap = parseInt($rightColumn.css("gap")) || 30;

    // Margin-top cần thiết = offset cần - (pricing widget height + gap)
    let marginTop = offsetNeeded - pricingWidgetHeight - gap;

    // Đảm bảo không âm
    marginTop = Math.max(0, marginTop);

    // Apply margin-top
    $toc.css("margin-top", marginTop + "px");

    // Debug log (có thể bỏ comment để kiểm tra)
    // console.log('TOC Alignment:', {
    //   rightColumnTop,
    //   shortDescTop,
    //   offsetNeeded,
    //   pricingWidgetHeight,
    //   gap,
    //   marginTop
    // });
  }
})(jQuery);
