/**
 * VieProxy — account.js
 * SPA router for the user dashboard.
 * Loads sub-page content via WP AJAX (load_account_page action).
 *
 * Sub-pages: profile | change-password | purchase-history
 */
(function () {
  "use strict";

  // ── Config ──────────────────────────────────────────────────────────────────
  var D = window.wpAccountData || {};
  var BASE = D.baseUrl || window.location.origin;
  var AJAX = D.ajaxUrl || BASE + "/wp-admin/admin-ajax.php";
  var NONCE = D.nonce || "";
  var API = D.apiBase || BASE + "/wp-json/vieproxy/v1";
  var LOGIN = D.loginUrl || BASE + "/dang-nhap";

  var SPA_PAGES = ["profile", "change-password", "purchase-history"];

  // ── Token helper (VpAuth-aware) ─────────────────────────────────────────────
  function getToken() {
    if (window.VpAuth && typeof window.VpAuth.getToken === "function") {
      return window.VpAuth.getToken() || "";
    }
    return localStorage.getItem("vp_jwt") || "";
  }

  // ── Auth guard ──────────────────────────────────────────────────────────────
  function guardAuth() {
    if (!getToken()) {
      window.location.href = LOGIN;
      return false;
    }
    return true;
  }

  // ── Sub-page init registry ───────────────────────────────────────────────────
  var PAGE_INIT = {
    profile: function () {
      typeof window.initProfilePage === "function" && window.initProfilePage();
    },
    "change-password": function () {
      typeof window.initChangePasswordPage === "function" &&
        window.initChangePasswordPage();
    },
    "purchase-history": function () {
      typeof window.initPurchaseHistoryPage === "function" &&
        window.initPurchaseHistoryPage();
    },
  };

  // ── DOM refs ─────────────────────────────────────────────────────────────────
  var contentArea;

  function getContentArea() {
    if (!contentArea) contentArea = document.getElementById("page-content");
    return contentArea;
  }

  // ── Load page via WP AJAX ────────────────────────────────────────────────────
  function loadPage(page) {
    var area = getContentArea();
    if (!area) return;

    if (!SPA_PAGES.includes(page)) {
      showError(area, "Trang không tồn tại.");
      return;
    }

    // Show skeleton
    area.style.opacity = "0.4";
    area.innerHTML =
      '<div class="ac-loading"><i class="fa-solid fa-spinner fa-spin"></i><p>Đang tải...</p></div>';

    var fd = new FormData();
    fd.append("action", "load_account_page");
    fd.append("page_slug", page);
    fd.append("nonce", NONCE);

    fetch(AJAX, { method: "POST", body: fd })
      .then(function (r) {
        if (!r.ok) throw new Error("HTTP " + r.status);
        return r.json();
      })
      .then(function (data) {
        if (!data.success) throw new Error(data.data || "Lỗi tải trang");

        area.innerHTML = data.data;

        // Re-execute inline scripts from loaded content
        area.querySelectorAll("script").forEach(function (old) {
          var s = document.createElement("script");
          Array.from(old.attributes).forEach(function (a) {
            s.setAttribute(a.name, a.value);
          });
          s.textContent = old.textContent;
          old.parentNode.replaceChild(s, old);
        });

        // Call page init after brief delay (let scripts settle)
        setTimeout(function () {
          if (PAGE_INIT[page]) PAGE_INIT[page]();
        }, 30);

        area.style.opacity = "1";
        area.style.transition = "opacity 0.15s ease";
      })
      .catch(function (err) {
        console.error("[account.js] loadPage error:", err);
        showError(area, err.message);
      });
  }

  function showError(area, msg) {
    area.style.opacity = "1";
    area.innerHTML =
      '<div class="ac-error">' +
      '<i class="fa-solid fa-circle-exclamation"></i>' +
      "<h3>Không thể tải trang</h3>" +
      "<p>" +
      msg +
      "</p>" +
      "</div>";
  }

  // ── Update active sidebar item ───────────────────────────────────────────────
  function setActiveMenu(page) {
    document.querySelectorAll(".sidebar-item").forEach(function (el) {
      el.classList.remove("is-active");
    });
    var active = document.querySelector(
      '.sidebar-item[data-page="' + page + '"]',
    );
    if (active) active.classList.add("is-active");
  }

  // ── Navigate (load + update URL + menu) ─────────────────────────────────────
  function navigate(page) {
    if (!guardAuth()) return;
    history.pushState({ page: page }, "", BASE + "/" + page);
    loadPage(page);
    setActiveMenu(page);

    // Close mobile menu if open
    var headerRight = document.querySelector(".header-right");
    if (headerRight) headerRight.classList.remove("is-open");
  }

  // ── Get page from current URL ────────────────────────────────────────────────
  function pageFromURL() {
    var segs = window.location.pathname
      .replace(/\/$/, "")
      .split("/")
      .filter(Boolean);
    var last = segs[segs.length - 1];
    return SPA_PAGES.includes(last) ? last : null;
  }

  // ── Logout ───────────────────────────────────────────────────────────────────
  function handleLogout() {
    if (window.vpHeader && typeof window.vpHeader.logout === "function") {
      window.vpHeader.logout();
    } else {
      // Fallback: call API directly
      var token = getToken();
      fetch(API + "/auth/logout", {
        method: "POST",
        headers: { Authorization: "Bearer " + token },
      })
        .catch(function () {})
        .then(function () {
          localStorage.removeItem("vp_jwt");
          window.location.href = LOGIN;
        });
    }
  }

  // ── Notification helper (used by sub-pages via window.accountUtils) ──────────
  function showNotification(message, type) {
    type = type || "success";
    var n = document.createElement("div");
    n.className = "ac-toast ac-toast--" + type;
    n.innerHTML =
      '<i class="fa-solid fa-' +
      (type === "success" ? "circle-check" : "circle-exclamation") +
      '"></i>' +
      "<span>" +
      message +
      "</span>";
    document.body.appendChild(n);

    // Trigger entrance
    requestAnimationFrame(function () {
      n.classList.add("is-visible");
    });

    setTimeout(function () {
      n.classList.remove("is-visible");
      setTimeout(function () {
        n.remove();
      }, 350);
    }, 3200);
  }

  // ── Init ─────────────────────────────────────────────────────────────────────
  document.addEventListener("DOMContentLoaded", function () {
    if (!guardAuth()) return;

    // Sidebar nav clicks
    document
      .querySelectorAll(".sidebar-item[data-page]")
      .forEach(function (el) {
        el.addEventListener("click", function (e) {
          e.preventDefault();
          navigate(el.dataset.page);
        });
      });

    // Logout button(s)
    ["sidebarLogoutBtn"].forEach(function (id) {
      var btn = document.getElementById(id);
      if (btn) btn.addEventListener("click", handleLogout);
    });

    // Browser back/forward
    window.addEventListener("popstate", function (e) {
      var page = (e.state && e.state.page) || pageFromURL() || "profile";
      loadPage(page);
      setActiveMenu(page);
    });

    // Determine initial page
    var area = getContentArea();
    var initial =
      (area && area.dataset.initialPage) || pageFromURL() || "profile";

    // Normalise URL to the page slug
    history.replaceState({ page: initial }, "", BASE + "/" + initial);
    setActiveMenu(initial);
    loadPage(initial);
  });

  // ── Public API for sub-pages ─────────────────────────────────────────────────
  window.accountUtils = {
    navigate: navigate,
    showNotification: showNotification,
    getToken: getToken,
    apiBase: API,
  };
})();
