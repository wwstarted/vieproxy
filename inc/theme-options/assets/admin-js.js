/**
 * VieProxy Admin JS
 * - Tab switching (URL-based)
 * - WordPress Media image uploader
 * - Repeater field (add/remove/sort cards)
 * - AJAX Save
 */
(function ($) {
  "use strict";

  $(document).ready(function () {
    // ═══════════════════════════════════════════════════════════
    // ICON HTML LIVE PREVIEW
    // ═══════════════════════════════════════════════════════════
    $(document).on("input", ".vp-icon-html-input", function () {
      var $input = $(this);
      var $preview = $input.siblings(".vp-icon-html-preview");
      var iconHtml = $input.val().trim();

      if (iconHtml) {
        // Clean the HTML and extract just the <i> tag
        var cleanHtml = iconHtml.replace(/&lt;/g, "<").replace(/&gt;/g, ">");

        // Try to parse and display the icon
        try {
          var $tempDiv = $("<div>").html(cleanHtml);
          var $icon = $tempDiv.find("i").first();

          if ($icon.length > 0) {
            // Clone the icon and display it
            $preview.html($icon.clone());
          } else {
            // If input contains <i> tag directly, try to display it
            $preview.html(cleanHtml);
          }
        } catch (e) {
          // If error, show placeholder
          $preview.html(
            '<i class="fa-solid fa-image" style="color: #cbd5e0;"></i>',
          );
        }
      } else {
        // Empty input: show placeholder
        $preview.html(
          '<i class="fa-solid fa-image" style="color: #cbd5e0;"></i>',
        );
      }
    });

    // Trigger initial preview on page load for existing values
    $(".vp-icon-html-input").each(function () {
      var $input = $(this);
      var $preview = $input.siblings(".vp-icon-html-preview");
      var iconHtml = $input.val().trim();

      if (iconHtml) {
        var cleanHtml = iconHtml.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
        try {
          var $tempDiv = $("<div>").html(cleanHtml);
          var $icon = $tempDiv.find("i").first();
          if ($icon.length > 0) {
            $preview.html($icon.clone());
          } else {
            $preview.html(cleanHtml);
          }
        } catch (e) {
          // Keep default placeholder
        }
      }
    });

    // ═══════════════════════════════════════════════════════════
    // TAB SWITCHING
    // ═══════════════════════════════════════════════════════════
    // Tabs are URL-based (page reload) — already handled by PHP.
    // This just adds smooth active state without full reload if needed.

    // ═══════════════════════════════════════════════════════════
    // WORDPRESS MEDIA IMAGE UPLOADER
    // ═══════════════════════════════════════════════════════════
    $(document).on("click", ".vp-btn-upload-image", function (e) {
      e.preventDefault();

      var $btn = $(this);
      var $uploader = $btn.closest(".vp-image-uploader");
      var $preview = $uploader.find(".vp-image-preview");
      var $urlInput = $uploader.find(".vp-image-url-input");

      // Create a NEW media uploader instance for each click
      // This prevents the issue where all cards share the same uploader
      var mediaUploader = wp.media({
        title: "Chọn ảnh",
        button: { text: "Sử dụng ảnh này" },
        multiple: false,
        library: { type: "image" },
      });

      // When an image is selected, run a callback
      mediaUploader.on("select", function () {
        var attachment = mediaUploader
          .state()
          .get("selection")
          .first()
          .toJSON();
        var url = attachment.url;

        // Update preview
        $preview.html('<img src="' + url + '" alt="" />');
        $preview.addClass("has-image");

        // Update hidden input
        $urlInput.val(url);

        // Update button text
        $btn.html(
          '<span class="dashicons dashicons-upload"></span> Thay đổi ảnh',
        );

        // Show remove button if not present
        if ($uploader.find(".vp-btn-remove-image").length === 0) {
          $btn.after(
            '<button type="button" class="button vp-btn-remove-image">' +
              '<span class="dashicons dashicons-trash"></span> Xoá ảnh</button>',
          );
        }
      });

      // Open the media uploader
      mediaUploader.open();
    });

    // Remove image
    $(document).on("click", ".vp-btn-remove-image", function (e) {
      e.preventDefault();

      var $btn = $(this);
      var $uploader = $btn.closest(".vp-image-uploader");
      var $preview = $uploader.find(".vp-image-preview");
      var $urlInput = $uploader.find(".vp-image-url-input");
      var $uploadBtn = $uploader.find(".vp-btn-upload-image");

      // Clear preview
      $preview
        .removeClass("has-image")
        .html(
          '<div class="vp-image-placeholder">' +
            '<span class="dashicons dashicons-format-image"></span>' +
            "<span>Chưa có ảnh</span>" +
            "</div>",
        );

      // Clear input
      $urlInput.val("");

      // Update button text
      $uploadBtn.html(
        '<span class="dashicons dashicons-upload"></span> Tải lên ảnh',
      );

      // Remove this button
      $btn.remove();
    });

    // ═══════════════════════════════════════════════════════════
    // REPEATER FIELD - ADD/REMOVE/SORT CARDS
    // ═══════════════════════════════════════════════════════════

    // Add new repeater item
    $(document).on("click", ".vp-repeater-add", function (e) {
      e.preventDefault();

      var $button = $(this);
      var $repeater = $button.closest(".vp-field--repeater");
      var $itemsContainer = $repeater.find(".vp-repeater-items");
      var repeaterKey = $repeater.data("repeater-key");
      var $template = $("#vp-repeater-template-" + repeaterKey);

      if (!$template.length) {
        console.error("Template not found for repeater:", repeaterKey);
        return;
      }

      // Get current item count
      var currentIndex = $itemsContainer.find(".vp-repeater-item").length;

      // Get template HTML
      var templateHtml = $template.html();

      // Replace placeholders
      var newItemHtml = templateHtml
        .replace(/__INDEX__/g, currentIndex)
        .replace(/__NUMBER__/g, currentIndex + 1);

      // Append new item
      $itemsContainer.append(newItemHtml);

      // Scroll to new item
      var $newItem = $itemsContainer.find(".vp-repeater-item").last();
      $("html, body").animate(
        {
          scrollTop: $newItem.offset().top - 100,
        },
        300,
      );

      // Update indices after adding
      updateRepeaterIndices($itemsContainer);
    });

    // Remove repeater item
    $(document).on("click", ".vp-repeater-item__remove", function (e) {
      e.preventDefault();

      var $item = $(this).closest(".vp-repeater-item");
      var $itemsContainer = $item.closest(".vp-repeater-items");

      // Don't allow removing the last item
      if ($itemsContainer.find(".vp-repeater-item").length <= 1) {
        alert("Phải có ít nhất 1 card!");
        return;
      }

      // Confirm deletion
      if (!confirm("Bạn có chắc muốn xóa card này?")) {
        return;
      }

      // Remove with animation
      $item.fadeOut(300, function () {
        $(this).remove();
        updateRepeaterIndices($itemsContainer);
      });
    });

    // Update indices and titles after add/remove/sort
    function updateRepeaterIndices($itemsContainer) {
      $itemsContainer.find(".vp-repeater-item").each(function (index) {
        var $item = $(this);
        var $title = $item.find(".vp-repeater-item__title");

        // Update data-index
        $item.attr("data-index", index);

        // Update title
        $title.text("Card " + (index + 1));

        // Update all input names within this item
        var repeaterKey = $item
          .closest(".vp-field--repeater")
          .data("repeater-key");
        $item.find("input, textarea, select").each(function () {
          var $input = $(this);
          var name = $input.attr("name");

          if (name && name.indexOf(repeaterKey) !== -1) {
            // Replace old index with new index
            // Pattern: vieproxy_theme_options[why_choose_cards][OLD_INDEX][field]
            // Replace: vieproxy_theme_options[why_choose_cards][NEW_INDEX][field]
            var regex = new RegExp(
              "(" +
                repeaterKey.replace(/[.*+?^${}()|[\]\\]/g, "\\$&") +
                "\\[)\\d+(\\])",
            );
            var newName = name.replace(regex, "$1" + index + "$2");
            $input.attr("name", newName);
          }
        });
      });
    }

    // Make repeater items sortable (drag and drop)
    if (typeof $.fn.sortable !== "undefined") {
      $(".vp-repeater-items").sortable({
        handle: ".vp-repeater-item__drag",
        placeholder: "ui-sortable-placeholder",
        helper: "clone",
        axis: "y",
        cursor: "move",
        opacity: 0.8,
        tolerance: "pointer",
        update: function (event, ui) {
          var $itemsContainer = $(this);
          updateRepeaterIndices($itemsContainer);
        },
      });
    }

    // ═══════════════════════════════════════════════════════════
    // AJAX SAVE
    // ═══════════════════════════════════════════════════════════
    $("#vp-settings-form").on("submit", function (e) {
      e.preventDefault();

      var $form = $(this);
      var $btn = $("#vp-save-btn");
      var $notice = $("#vp-save-notice");

      // Show loading state
      $btn
        .addClass("is-saving")
        .find("span.dashicons")
        .attr("class", "dashicons dashicons-update spin");
      $notice.removeClass("is-success is-error").text("");

      // Serialize form data
      var formData = $form.serializeArray();

      // Handle unchecked checkboxes — already handled by hidden input trick,
      // but we filter out duplicate keys keeping the last value (checkbox wins over hidden)
      var dataMap = {};
      var dataArr = [];

      $.each(formData, function (_, field) {
        dataMap[field.name] = field.value;
      });

      $.each(dataMap, function (name, value) {
        dataArr.push({ name: name, value: value });
      });

      // Add nonce
      dataArr.push({ name: "nonce", value: vieproxyAdmin.nonce });

      // AJAX request
      $.ajax({
        url: vieproxyAdmin.ajaxurl,
        type: "POST",
        data: dataArr,
        success: function (response) {
          if (response.success) {
            $notice.addClass("is-success").text("✓ " + response.data.message);
            $btn
              .find("span.dashicons")
              .attr("class", "dashicons dashicons-yes-alt");

            setTimeout(function () {
              $notice.removeClass("is-success").text("");
            }, 3500);
          } else {
            $notice
              .addClass("is-error")
              .text("✗ Lưu thất bại. Vui lòng thử lại.");
            $btn
              .find("span.dashicons")
              .attr("class", "dashicons dashicons-warning");
          }
        },
        error: function () {
          $notice.addClass("is-error").text("✗ Lỗi kết nối. Vui lòng thử lại.");
          $btn
            .find("span.dashicons")
            .attr("class", "dashicons dashicons-warning");
        },
        complete: function () {
          $btn.removeClass("is-saving");
        },
      });
    });
  });
})(jQuery);
