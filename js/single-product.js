/**
 * Single Product JavaScript
 * VieProxy Theme
 */

(function ($) {
  "use strict";

  $(document).ready(function () {
    // ================================
    // PRICING WIDGET FUNCTIONALITY
    // ================================

    const $range = $("#single-product-range");
    const $qtyDisplay = $("#single-product-qty-display");
    const $total = $("#single-product-total");
    const $per = $("#single-product-per");
    const $monthlyPrice = $("#single-product-monthly-price");
    const $discount = $("#single-product-discount");
    const $durationList = $("#single-product-duration-list");

    if ($range.length > 0) {
      const minQty = parseInt($range.data("min")) || 1;
      const maxQty = parseInt($range.data("max")) || 100;

      // Update range slider background
      function updateRangeBackground() {
        const value = parseInt($range.val());
        const percentage = ((value - minQty) / (maxQty - minQty)) * 100;
        $range.css(
          "background",
          `linear-gradient(to right, #0d4ef7 0%, #0d4ef7 ${percentage}%, #ddd ${percentage}%, #ddd 100%)`,
        );
      }

      // Calculate and update price
      function updatePrice() {
        const qty = parseInt($range.val());
        const $activeOption = $durationList.find(
          ".single-product-widget-duration-item.active",
        );
        const monthlyPrice =
          parseFloat($activeOption.data("monthly-price")) || 100;
        const months = parseFloat($activeOption.data("months")) || 1;
        const totalPrice = qty * monthlyPrice * months;

        // Update displays
        $qtyDisplay.text(qty);
        $total.text("$" + totalPrice.toLocaleString());
        $per.text("/ " + qty + " IPs");
        $monthlyPrice.text(monthlyPrice);

        // Update all duration prices
        $durationList
          .find(".single-product-widget-duration-item")
          .each(function () {
            const $item = $(this);
            const itemMonthlyPrice =
              parseFloat($item.data("monthly-price")) || 100;
            const itemMonths = parseFloat($item.data("months")) || 1;
            const itemPrice = qty * itemMonthlyPrice * itemMonths;
            $item
              .find(".single-product-duration-price")
              .text(itemPrice.toLocaleString());
          });

        updateRangeBackground();
      }

      // Range slider change event
      $range.on("input change", function () {
        updatePrice();
      });

      // Duration plan selection
      $durationList.on("change", 'input[type="radio"]', function () {
        $durationList
          .find(".single-product-widget-duration-item")
          .removeClass("active");
        $(this)
          .closest(".single-product-widget-duration-item")
          .addClass("active");
        updatePrice();
      });

      // Initialize
      updatePrice();
    }

    // ================================
    // TABLE OF CONTENTS GENERATOR
    // ================================

    const $tocList = $("#single-product-toc-list");
    const $shortDesc = $(".single-product-short-description-content");

    if ($tocList.length > 0 && $shortDesc.length > 0) {
      const headings = $shortDesc.find("h1, h2, h3, h4");

      if (headings.length > 0) {
        headings.each(function (index) {
          const $heading = $(this);
          const text = $heading.text().trim();
          const level = $heading.prop("tagName").toLowerCase().replace("h", "");
          const id = "toc-heading-" + index;

          // Add ID to heading for anchor linking
          $heading.attr("id", id);

          // Create TOC item
          const $tocItem = $("<a>", {
            href: "#" + id,
            class: "single-product-toc-item",
            "data-level": level,
            text: text,
          });

          $tocList.append($tocItem);
        });

        // Smooth scroll to heading
        $tocList.on("click", "a", function (e) {
          e.preventDefault();
          const targetId = $(this).attr("href");
          const $target = $(targetId);

          if ($target.length) {
            $("html, body").animate(
              {
                scrollTop: $target.offset().top - 100,
              },
              500,
            );

            // Update active state
            $tocList.find("a").removeClass("active");
            $(this).addClass("active");
          }
        });

        // Highlight TOC item on scroll
        $(window).on("scroll", function () {
          let scrollPos = $(window).scrollTop() + 150;

          headings.each(function () {
            const $heading = $(this);
            const headingTop = $heading.offset().top;
            const headingBottom = headingTop + $heading.outerHeight();
            const id = $heading.attr("id");

            if (scrollPos >= headingTop && scrollPos <= headingBottom) {
              $tocList.find("a").removeClass("active");
              $tocList.find('a[href="#' + id + '"]').addClass("active");
            }
          });
        });
      } else {
        // No headings found, hide TOC
        $(".single-product-toc").hide();
      }
    }

    // ================================
    // ADD TO CART FUNCTIONALITY
    // ================================

    $(".single-product-widget-btn-cart").on("click", function () {
      // TODO: Implement add to cart with custom pricing
      alert("Add to cart functionality - Coming soon!");
    });
  });
})(jQuery);
