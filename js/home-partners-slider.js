/**
 * VieProxy - Partners Slider
 * 2 hàng logo tự động chạy ngược chiều nhau
 * - Hàng trên: trái → phải
 * - Hàng dưới: phải → trái
 * - Hover vào card: chỉ dừng hàng đó, không dừng cả section
 * - Loop vô hạn mượt mà không bị giật
 */
(function ($) {
  "use strict";

  $(document).ready(function () {
    const $partnersSection = $(".home-partners");

    if (!$partnersSection.length) return;

    const $row1 = $partnersSection.find(".partners-slider-row-1");
    const $row2 = $partnersSection.find(".partners-slider-row-2");

    // Kiểm tra nếu không có logo nào
    if (!$row1.find(".partner-logo").length) return;

    // ============================================================
    // NHÂN ĐÔI LOGO NHIỀU LẦN ĐỂ ĐẢM BẢO LUÔN ĐẦY MÀN HÌNH
    // ============================================================
    function duplicateLogos($row) {
      const $logos = $row.find(".partner-logo").clone();

      // Nhân đôi 3 lần để đảm bảo không bao giờ bị trống
      for (let i = 0; i < 3; i++) {
        $row.append($logos.clone());
      }
    }

    duplicateLogos($row1);
    duplicateLogos($row2);

    // ============================================================
    // TÍNH TOÁN CHIỀU RỘNG & THỜI GIAN
    // ============================================================
    function setupAnimation($row, direction) {
      // Tính tổng chiều rộng của TẤT CẢ logo gốc (trước khi nhân đôi)
      let originalWidth = 0;
      const logoCount = $row.find(".partner-logo").length / 4; // Chia 4 vì đã nhân x4

      $row
        .find(".partner-logo")
        .slice(0, logoCount)
        .each(function () {
          originalWidth += $(this).outerWidth(true);
        });

      // Tốc độ: 50px/giây
      const speed = 50;
      const duration = originalWidth / speed;

      // Tạo animation name dựa vào direction
      const animationName = direction === "left" ? "slide-left" : "slide-right";

      // Áp dụng animation
      $row.css({
        animation: `${animationName} ${duration}s linear infinite`,
      });

      // Đảm bảo animation reset mượt mà bằng cách sử dụng animationiteration
      $row[0].addEventListener("animationiteration", function () {
        // Animation tự động loop lại, không cần làm gì
      });
    }

    setupAnimation($row1, "left");
    setupAnimation($row2, "right");

    // ============================================================
    // TẠM DỪNG CHỈ HÀNG ĐƯỢC HOVER
    // ============================================================
    // Sử dụng event delegation để xử lý cả logo gốc và logo được clone
    $row1.on("mouseenter", ".partner-logo", function () {
      $row1.css("animation-play-state", "paused");
    });

    $row1.on("mouseleave", ".partner-logo", function () {
      $row1.css("animation-play-state", "running");
    });

    $row2.on("mouseenter", ".partner-logo", function () {
      $row2.css("animation-play-state", "paused");
    });

    $row2.on("mouseleave", ".partner-logo", function () {
      $row2.css("animation-play-state", "running");
    });
  });
})(jQuery);
