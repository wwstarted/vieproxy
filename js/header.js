
(function () {
  "use strict";

  // ── Config from PHP ─────────────────────────────────────────────────────────
  var cfg = window.vpHeaderData || {};
  var api = cfg.apiBase || "/wp-json/vieproxy/v1";
  var init = cfg.initialState || {};

  // ── Auth state ──────────────────────────────────────────────────────────────
  var isLoggedIn = !!init.isLoggedIn;
  var currentUser = {
    name: init.userName || "",
    email: init.userEmail || "",
    avatar: init.userAvatar || "",
  };

  // ── Token helpers (VpAuth-aware) ────────────────────────────────────────────
  function vpGetToken() {
    if (window.VpAuth && typeof window.VpAuth.getToken === "function") {
      return window.VpAuth.getToken() || "";
    }
    return localStorage.getItem("vp_jwt") || "";
  }

  function vpRemoveToken() {
    if (window.VpAuth && typeof window.VpAuth.removeToken === "function") {
      window.VpAuth.removeToken();
    } else {
      localStorage.removeItem("vp_jwt");
    }
  }

  // ── Name parser — mirrors PHP vp_header_parse_name() ───────────────────────
  function parseUserName(displayName) {
    if (!displayName) return { name: "", avatar: "" };
    var words = displayName.trim().split(/\s+/);
    return {
      name: words.slice(-2).join(" "),
      avatar: words[words.length - 1].charAt(0).toUpperCase(),
    };
  }

  // ── DOM helpers ─────────────────────────────────────────────────────────────
  function $id(id) {
    return document.getElementById(id);
  }

  function show(id, displayValue) {
    var el = $id(id);
    if (el) el.style.display = displayValue || "flex";
  }

  function hide(id) {
    var el = $id(id);
    if (el) el.style.display = "none";
  }

  function setText(id, value) {
    var el = $id(id);
    if (el) el.textContent = value;
  }

  // ── Sync user text across all elements ─────────────────────────────────────
  function syncUserElements() {
    setText("desktopAvatarLetter", currentUser.avatar);
    setText("desktopUserName", currentUser.name);
    setText("mobileAvatarLetter", currentUser.avatar);
    setText("mobileUserName", currentUser.name);
    setText("mobileUserEmail", currentUser.email);
  }

  // ── Update header UI based on isLoggedIn ────────────────────────────────────
  function updateAuthUI() {
    if (isLoggedIn) {
      // Desktop: show user dropdown, hide auth buttons
      show("userDropdown", "flex");
      hide("authButtons");

      // Mobile: show user info + nav, hide auth buttons
      show("mobileUserRow", "flex");
      show("mobileUserMenu", "block");
      hide("authButtons"); // authButtons also serves as mobile auth row

      syncUserElements();
    } else {
      // Desktop: hide user dropdown, show auth buttons
      hide("userDropdown");
      show("authButtons", "flex");

      // Mobile: hide user section
      hide("mobileUserRow");
      hide("mobileUserMenu");
    }
  }

  // ── Fetch user profile — background verification ────────────────────────────
  function fetchUserData() {
    var token = vpGetToken();
    if (!token) return;

    // Use VpAuth.authFetch if available, otherwise manual Bearer header
    var request;
    if (window.VpAuth && typeof window.VpAuth.authFetch === "function") {
      request = window.VpAuth.authFetch(api + "/user/profile");
    } else {
      request = fetch(api + "/user/profile", {
        headers: { Authorization: "Bearer " + token },
      });
    }

    request
      .then(function (res) {
        if (!res.ok) throw new Error("HTTP " + res.status);
        return res.json();
      })
      .then(function (data) {
        var parsed = parseUserName(data.display_name || "");
        currentUser.name = parsed.name;
        currentUser.avatar = parsed.avatar;
        currentUser.email = data.email || "";
        isLoggedIn = true;
        updateAuthUI();
      })
      .catch(function () {
        // Token invalid or expired — force logged-out state
        vpRemoveToken();
        isLoggedIn = false;
        currentUser = { name: "", email: "", avatar: "" };
        updateAuthUI();
      });
  }

  // ── Logout ──────────────────────────────────────────────────────────────────
  function handleLogout() {
    var token = vpGetToken();

    // 1. Call /auth/logout on server → clears httpOnly vp_jwt cookie
    fetch(api + "/auth/logout", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: "Bearer " + token,
      },
    })
      .catch(function () {
        /* network error — proceed with client logout anyway */
      })
      .then(function () {
        // 2. Clear client-side token
        vpRemoveToken();

        // 3. Update state + UI
        isLoggedIn = false;
        currentUser = { name: "", email: "", avatar: "" };
        updateAuthUI();

        // 4. Close any open dropdowns
        closeUserDropdown();

        // 5. Redirect to login
        window.location.href = cfg.loginUrl || "/dang-nhap";
      });
  }

  // ── User Dropdown (Desktop) ─────────────────────────────────────────────────
  var _userDropdown = null;
  var _userTrigger = null;

  function openUserDropdown() {
    if (!_userDropdown) return;
    _userDropdown.classList.add("is-open");
    if (_userTrigger) _userTrigger.setAttribute("aria-expanded", "true");
  }

  function closeUserDropdown() {
    if (!_userDropdown) return;
    _userDropdown.classList.remove("is-open");
    if (_userTrigger) _userTrigger.setAttribute("aria-expanded", "false");
  }

  function toggleUserDropdown(e) {
    e.stopPropagation();
    if (_userDropdown && _userDropdown.classList.contains("is-open")) {
      closeUserDropdown();
    } else {
      openUserDropdown();
    }
  }

  // ── DOMContentLoaded ────────────────────────────────────────────────────────
  document.addEventListener("DOMContentLoaded", function () {
    // ────────────────────────────────────────────────────────────────
    // 1. Language dropdown (Desktop)
    // ────────────────────────────────────────────────────────────────
    var langToggle = $id("langToggle");
    var langDropdown = $id("langDropdown");

    if (langToggle && langDropdown) {
      langToggle.addEventListener("click", function (e) {
        e.stopPropagation();
        var open = langDropdown.classList.contains("is-open");
        langDropdown.classList.toggle("is-open", !open);
        langToggle.setAttribute("aria-expanded", String(!open));
      });

      // Close when clicking anywhere outside
      document.addEventListener("click", function () {
        langDropdown.classList.remove("is-open");
        langToggle.setAttribute("aria-expanded", "false");
      });

      langDropdown.addEventListener("click", function (e) {
        e.stopPropagation();
      });
    }

    // ────────────────────────────────────────────────────────────────
    // 2. Language dropdown (Mobile)
    // ────────────────────────────────────────────────────────────────
    var mobileLangToggle = $id("mobileLangToggle");
    var mobileLangDropdown = $id("mobileLangDropdown");

    if (mobileLangToggle && mobileLangDropdown) {
      mobileLangToggle.addEventListener("click", function (e) {
        e.stopPropagation();
        var open = mobileLangDropdown.classList.contains("is-open");
        mobileLangDropdown.classList.toggle("is-open", !open);
        mobileLangToggle.setAttribute("aria-expanded", String(!open));
      });

      mobileLangDropdown.addEventListener("click", function (e) {
        e.stopPropagation();
      });
    }

    // ────────────────────────────────────────────────────────────────
    // 3. Mobile hamburger menu
    // ────────────────────────────────────────────────────────────────
    var hamburger = $id("mobileMenuToggle");
    var headerRight = document.querySelector(".header-right");
    var siteHeader = document.querySelector(".site-header");

    function closeMobileMenu() {
      if (!headerRight || !hamburger) return;
      hamburger.classList.remove("is-active");
      headerRight.classList.remove("is-open");
      hamburger.setAttribute("aria-label", "Mở menu");
    }

    if (hamburger && headerRight) {
      hamburger.addEventListener("click", function (e) {
        e.stopPropagation();
        hamburger.classList.toggle("is-active");
        headerRight.classList.toggle("is-open");
        hamburger.setAttribute(
          "aria-label",
          headerRight.classList.contains("is-open") ? "Đóng menu" : "Mở menu",
        );
      });

      // Close when clicking outside the header
      document.addEventListener("click", function (e) {
        if (
          headerRight.classList.contains("is-open") &&
          siteHeader &&
          !siteHeader.contains(e.target)
        ) {
          closeMobileMenu();
        }
      });

      // Prevent clicks inside header-right from closing the menu
      headerRight.addEventListener("click", function (e) {
        e.stopPropagation();
      });
    }

    // ────────────────────────────────────────────────────────────────
    // 4. User dropdown (Desktop)
    // ────────────────────────────────────────────────────────────────
    _userDropdown = $id("userDropdown");
    _userTrigger = $id("userTrigger");

    if (_userTrigger) {
      _userTrigger.addEventListener("click", toggleUserDropdown);
    }

    // Close user dropdown on outside click
    document.addEventListener("click", function (e) {
      if (
        _userDropdown &&
        _userDropdown.classList.contains("is-open") &&
        !_userDropdown.contains(e.target)
      ) {
        closeUserDropdown();
      }
    });

    // ────────────────────────────────────────────────────────────────
    // 5. Logout buttons (desktop + mobile)
    // ────────────────────────────────────────────────────────────────
    var logoutBtn = $id("logoutBtn");
    var mobileLogoutBtn = $id("mobileLogoutBtn");

    if (logoutBtn) {
      logoutBtn.addEventListener("click", function (e) {
        e.preventDefault();
        handleLogout();
      });
    }

    if (mobileLogoutBtn) {
      mobileLogoutBtn.addEventListener("click", function (e) {
        e.preventDefault();
        handleLogout();
      });
    }

    var clientToken = vpGetToken();
    var serverLoggedIn = init.isLoggedIn;

    if (serverLoggedIn && clientToken) {
      fetchUserData();
    } else if (!serverLoggedIn && clientToken) {
      // Edge case: PHP cookie absent/expired but localStorage token exists.
      // Validate the token — if valid, update UI to logged-in.
      fetchUserData();
    } else if (serverLoggedIn && !clientToken) {
      // Edge case: PHP cookie present but no localStorage token
      // (e.g. another browser tab cleared it, or user bypassed JS login).
      // Trust PHP for UI display; JS can't make auth API calls without token.
      // No action needed — PHP render is already correct.
    }
    // else: both say logged-out → nothing to do.
  }); // end DOMContentLoaded

  // ── Public API for sign-in.js / other scripts ────────────────────────────
  /**
   * Call window.vpHeader.setLoggedIn(userData) after a successful login
   * to immediately update the header without a page reload.
   *
   * @param {Object} userData  - { display_name, email }
   */
  window.vpHeader = {
    setLoggedIn: function (userData) {
      isLoggedIn = true;
      if (userData) {
        var parsed = parseUserName(userData.display_name || "");
        currentUser.name = parsed.name;
        currentUser.avatar = parsed.avatar;
        currentUser.email = userData.email || "";
      }
      updateAuthUI();
    },

    setLoggedOut: function () {
      isLoggedIn = false;
      currentUser = { name: "", email: "", avatar: "" };
      updateAuthUI();
    },

    logout: handleLogout,
  };
})();
