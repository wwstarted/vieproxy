/**
 * VieProxy - FAQ Accordion
 * - Câu hỏi đầu tiên mở mặc định
 * - Click vào câu hỏi khác: đóng câu hỏi đang mở, mở câu hỏi mới
 */
(function ($) {
  "use strict";

  $(document).ready(function () {
    const $faqSection = $(".home-faq");

    if (!$faqSection.length) return;

    const $faqItems = $faqSection.find(".faq-item");

    if (!$faqItems.length) return;

    // ============================================================
    // MỞ MẶC ĐỊNH CÂU HỎI ĐẦU TIÊN
    // ============================================================
    const $firstItem = $faqItems.first();
    $firstItem.addClass("is-open");
    $firstItem.find(".faq-answer").slideDown(0); // mở ngay không animation

    // ============================================================
    // XỬ LÝ CLICK VÀO CÂU HỎI
    // ============================================================
    $faqItems.on("click", ".faq-question", function () {
      const $clickedItem = $(this).closest(".faq-item");
      const $clickedAnswer = $clickedItem.find(".faq-answer");
      const isCurrentlyOpen = $clickedItem.hasClass("is-open");

      if (isCurrentlyOpen) {
        // Nếu đang mở → đóng lại
        $clickedItem.removeClass("is-open");
        $clickedAnswer.slideUp(300);
      } else {
        // Đóng tất cả câu hỏi khác
        $faqItems.not($clickedItem).each(function () {
          const $otherItem = $(this);
          const $otherAnswer = $otherItem.find(".faq-answer");

          if ($otherItem.hasClass("is-open")) {
            $otherItem.removeClass("is-open");
            $otherAnswer.slideUp(300);
          }
        });

        // Mở câu hỏi được click
        $clickedItem.addClass("is-open");
        $clickedAnswer.slideDown(300);
      }
    });

    // ============================================================
    // HỖ TRỢ KEYBOARD ACCESSIBILITY (OPTIONAL)
    // ============================================================
    $faqItems.find(".faq-question").on("keydown", function (e) {
      // Enter hoặc Space
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        $(this).trigger("click");
      }
    });
  });
})(jQuery);
