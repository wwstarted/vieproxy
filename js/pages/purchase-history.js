/**
 * VieProxy — js/pages/purchase-history.js
 *
 * Fetches orders via WP AJAX (get_purchase_history action).
 * Renders order cards with status, product details, and actions.
 * Client-side filter by search term + status.
 */

window.initPurchaseHistoryPage = function () {
  "use strict";

  // ── Config ────────────────────────────────────────────────────────────────
  var AJAX =
    (window.wpAccountData && window.wpAccountData.ajaxUrl) ||
    window.location.origin + "/wp-admin/admin-ajax.php";
  var NONCE = (window.wpAccountData && window.wpAccountData.nonce) || "";
  var BASE =
    (window.wpAccountData && window.wpAccountData.baseUrl) ||
    window.location.origin;
  var notify = window.accountUtils
    ? window.accountUtils.showNotification
    : function () {};

  // ── DOM refs ──────────────────────────────────────────────────────────────
  var listEl = document.getElementById("phOrderList");
  var emptyEl = document.getElementById("phEmpty");
  var searchEl = document.getElementById("phSearch");
  var statusEl = document.getElementById("phStatusFilter");

  // ── State ─────────────────────────────────────────────────────────────────
  var allOrders = [];

  // ── Status map ────────────────────────────────────────────────────────────
  var STATUS_MAP = {
    completed: { label: "Hoàn thành", cls: "ph-status--success" },
    processing: { label: "Đang xử lý", cls: "ph-status--info" },
    pending: { label: "Chờ thanh toán", cls: "ph-status--warning" },
    cancelled: { label: "Đã hủy", cls: "ph-status--danger" },
    refunded: { label: "Đã hoàn tiền", cls: "ph-status--neutral" },
  };

  function statusInfo(key) {
    return STATUS_MAP[key] || { label: key, cls: "" };
  }

  // ── Fetch orders ─────────────────────────────────────────────────────────
  function fetchOrders() {
    var fd = new FormData();
    fd.append("action", "get_purchase_history");
    fd.append("nonce", NONCE);

    fetch(AJAX, { method: "POST", body: fd })
      .then(function (r) {
        if (!r.ok) throw new Error("HTTP " + r.status);
        return r.json();
      })
      .then(function (data) {
        if (!data.success)
          throw new Error(data.data || "Không thể tải đơn hàng");
        allOrders = (data.data && data.data.orders) || [];
        renderOrders(allOrders);
      })
      .catch(function (err) {
        console.error("[purchase-history.js]", err);
        if (listEl) {
          listEl.innerHTML =
            '<div class="ac-error">' +
            '<i class="fa-solid fa-circle-exclamation"></i>' +
            "<h3>Không thể tải đơn hàng</h3>" +
            "<p>" +
            err.message +
            "</p>" +
            "</div>";
        }
      });
  }

  // ── Render list ───────────────────────────────────────────────────────────
  function renderOrders(orders) {
    if (!listEl) return;

    if (!orders || orders.length === 0) {
      listEl.innerHTML = "";
      if (emptyEl) emptyEl.style.display = "flex";
      return;
    }
    if (emptyEl) emptyEl.style.display = "none";

    listEl.innerHTML = orders.map(renderOrderCard).join("");
  }

  function renderOrderCard(order) {
    var st = statusInfo(order.status);
    var isPending = order.status === "pending";

    var itemsHtml = (order.items || [])
      .map(function (item) {
        var initial = item.name ? item.name.charAt(0).toUpperCase() : "?";
        return (
          '<div class="ph-product">' +
          '<div class="ph-product-logo">' +
          initial +
          "</div>" +
          '<div class="ph-product-info">' +
          '<strong class="ph-product-name">' +
          escHtml(item.name) +
          "</strong>" +
          '<span class="ph-product-config">' +
          escHtml(item.config || "") +
          "</span>" +
          "</div>" +
          '<div class="ph-product-cdk">' +
          "<span>" +
          escHtml(item.cdk_info || "") +
          "</span>" +
          "</div>" +
          '<div class="ph-product-price">' +
          escHtml(item.price || "") +
          "</div>" +
          "</div>"
        );
      })
      .join("");

    var paymentBtn = isPending
      ? '<a href="' +
        escHtml(order.payment_url || "#") +
        '" class="btn-primary ph-btn" style="text-decoration:none;">' +
        '<i class="fa-solid fa-qrcode"></i> Thanh toán' +
        "</a>"
      : '<button class="btn-ghost ph-btn ph-rebuy-btn" data-order="' +
        escHtml(order.order_number) +
        '">' +
        '<i class="fa-solid fa-rotate-right"></i> Mua lại' +
        "</button>";

    return (
      '<div class="ph-order" data-status="' +
      order.status +
      '">' +
      '<div class="ph-order-head">' +
      '<div class="ph-order-meta">' +
      '<span class="ph-order-code">Đơn <strong>#' +
      escHtml(order.order_number) +
      "</strong></span>" +
      '<span class="ph-order-date">' +
      escHtml(order.date || "") +
      "</span>" +
      "</div>" +
      '<span class="ph-status ' +
      st.cls +
      '">' +
      st.label +
      "</span>" +
      "</div>" +
      '<div class="ph-products">' +
      itemsHtml +
      "</div>" +
      '<div class="ph-order-foot">' +
      '<div class="ph-actions">' +
      paymentBtn +
      "</div>" +
      '<div class="ph-total">' +
      '<span class="ph-total-label">Tổng cộng</span>' +
      '<span class="ph-total-amount">' +
      escHtml(order.total || "") +
      "</span>" +
      "</div>" +
      "</div>" +
      "</div>"
    );
  }

  // ── Client-side filter ────────────────────────────────────────────────────
  function applyFilter() {
    var q = searchEl ? searchEl.value.trim().toLowerCase() : "";
    var status = statusEl ? statusEl.value : "";

    var filtered = allOrders.filter(function (order) {
      var matchSearch =
        !q ||
        (order.order_number && order.order_number.toLowerCase().includes(q));
      var matchStatus = !status || order.status === status;
      return matchSearch && matchStatus;
    });

    renderOrders(filtered);
  }

  if (searchEl) {
    var searchTimer;
    searchEl.addEventListener("input", function () {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(applyFilter, 280);
    });
  }

  if (statusEl) {
    statusEl.addEventListener("change", applyFilter);
  }

  // ── Delegate: "Mua lại" ───────────────────────────────────────────────────
  if (listEl) {
    listEl.addEventListener("click", function (e) {
      var btn = e.target.closest(".ph-rebuy-btn");
      if (btn) {
        notify("Tính năng mua lại sẽ sớm được cập nhật!", "info");
      }
    });
  }

  // ── Helpers ───────────────────────────────────────────────────────────────
  function escHtml(str) {
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  // ── Boot ──────────────────────────────────────────────────────────────────
  fetchOrders();
};
