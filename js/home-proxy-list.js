(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    // ════════════════════════════════════════════════════════
    // PROXY TABS — FILTER TABLE ROWS
    // Tab "Tất cả"  → hiện tất cả rows
    // Tab sản phẩm → chỉ hiện row khớp data-filter
    // ════════════════════════════════════════════════════════
    var proxyTabs = document.querySelectorAll(".home-proxy-tab");
    var proxyRows = document.querySelectorAll(".home-proxy-row");

    function filterRows(filterKey) {
      proxyRows.forEach(function (row) {
        if (filterKey === "all") {
          row.style.display = "";
        } else {
          var rowFilter = row.getAttribute("data-filter");
          row.style.display = rowFilter === filterKey ? "" : "none";
        }
      });
    }

    if (proxyTabs.length > 0) {
      proxyTabs.forEach(function (tab) {
        tab.addEventListener("click", function (e) {
          // Prevent default if it's a button
          e.preventDefault();

          // Remove active class from all tabs
          proxyTabs.forEach(function (t) {
            t.classList.remove("is-active");
          });

          // Add active class to clicked tab
          tab.classList.add("is-active");

          // Get filter value and apply filter
          var filterValue = tab.getAttribute("data-filter") || "all";
          filterRows(filterValue);
        });
      });

      // Initialize: show all rows on page load
      filterRows("all");
    }

    // ════════════════════════════════════════════════════════
    // MOUSE DRAG TO SCROLL — TABS
    // ════════════════════════════════════════════════════════
    var tabsContainer = document.querySelector(".home-proxy-tabs");

    if (tabsContainer) {
      var isDraggingTabs = false;
      var tabsStartX, tabsScrollLeft;

      tabsContainer.addEventListener("mousedown", function (e) {
        // Don't interfere with tab clicks
        if (
          e.target.classList.contains("home-proxy-tab") ||
          e.target.closest(".home-proxy-tab")
        ) {
          return;
        }

        isDraggingTabs = true;
        tabsStartX = e.pageX - tabsContainer.offsetLeft;
        tabsScrollLeft = tabsContainer.scrollLeft;
        tabsContainer.classList.add("is-grabbing");
        tabsContainer.style.userSelect = "none";
      });

      tabsContainer.addEventListener("mouseleave", function () {
        isDraggingTabs = false;
        tabsContainer.classList.remove("is-grabbing");
      });

      tabsContainer.addEventListener("mouseup", function () {
        isDraggingTabs = false;
        tabsContainer.classList.remove("is-grabbing");
      });

      tabsContainer.addEventListener("mousemove", function (e) {
        if (!isDraggingTabs) return;
        e.preventDefault();
        var x = e.pageX - tabsContainer.offsetLeft;
        tabsContainer.scrollLeft = tabsScrollLeft - (x - tabsStartX) * 2;
      });

      tabsContainer.style.cursor = "grab";
    }

    // ════════════════════════════════════════════════════════
    // MOUSE DRAG TO SCROLL — TABLE
    // ════════════════════════════════════════════════════════
    var tableContainer = document.querySelector(".home-proxy-table-container");

    if (tableContainer) {
      var isDraggingTable = false;
      var tableStartX, tableScrollLeft;

      tableContainer.addEventListener("mousedown", function (e) {
        if (
          e.target.closest("button") ||
          e.target.closest("a") ||
          e.target.closest("select")
        )
          return;
        isDraggingTable = true;
        tableStartX = e.pageX - tableContainer.offsetLeft;
        tableScrollLeft = tableContainer.scrollLeft;
        tableContainer.classList.add("is-grabbing");
        tableContainer.style.userSelect = "none";
      });

      tableContainer.addEventListener("mouseleave", function () {
        isDraggingTable = false;
        tableContainer.classList.remove("is-grabbing");
      });

      tableContainer.addEventListener("mouseup", function () {
        isDraggingTable = false;
        tableContainer.classList.remove("is-grabbing");
      });

      tableContainer.addEventListener("mousemove", function (e) {
        if (!isDraggingTable) return;
        e.preventDefault();
        var x = e.pageX - tableContainer.offsetLeft;
        tableContainer.scrollLeft = tableScrollLeft - (x - tableStartX) * 2;
      });

      tableContainer.style.cursor = "grab";
    }

    // ════════════════════════════════════════════════════════
    // QUANTITY SELECT — TÍNH GIÁ ĐỘNG
    // ════════════════════════════════════════════════════════
    document
      .querySelectorAll(".home-quantity-select")
      .forEach(function (select) {
        select.addEventListener("change", function () {
          var productId = select.getAttribute("data-product-id");
          var qty = parseInt(select.value) || 1;
          var monthlyPrice =
            parseFloat(select.getAttribute("data-monthly-price")) || 0;
          var months = parseFloat(select.getAttribute("data-months")) || 1;

          var totalUSD = qty * monthlyPrice * months;
          var totalVND = totalUSD * 25000;

          var priceCell = document.querySelector(
            '.home-price-cell[data-product-id="' + productId + '"]',
          );
          if (!priceCell) return;

          var amountEl = priceCell.querySelector(".home-price-amount");
          if (amountEl) {
            amountEl.textContent =
              new Intl.NumberFormat("vi-VN").format(Math.round(totalVND)) + "đ";
          }

          var perUnitEl = priceCell.querySelector(".home-price-per-unit");
          if (perUnitEl) {
            var icon = perUnitEl.querySelector("i");
            perUnitEl.innerHTML =
              (icon
                ? icon.outerHTML + " "
                : '<i class="fa-solid fa-circle-info"></i> ') +
              "$" +
              totalUSD.toFixed(2);
          }

          priceCell.style.transform = "scale(1.05)";
          setTimeout(function () {
            priceCell.style.transform = "";
          }, 200);
        });
      });

    // ════════════════════════════════════════════════════════
    // CART & BUY BUTTONS
    // ════════════════════════════════════════════════════════
    document.querySelectorAll(".home-btn-cart").forEach(function (btn) {
      btn.addEventListener("click", function (e) {
        e.preventDefault();
        btn.style.transform = "scale(0.95)";
        setTimeout(function () {
          btn.style.transform = "";
        }, 150);
        console.log("Cart:", btn.getAttribute("data-product-id"));
      });
    });

    document.querySelectorAll(".home-btn-buy").forEach(function (btn) {
      btn.addEventListener("click", function (e) {
        e.preventDefault();
        console.log("Buy:", btn.getAttribute("data-product-id"));
      });
    });
  });
})();
