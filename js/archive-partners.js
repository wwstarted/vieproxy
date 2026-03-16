/**
 * archive-partners.js
 * VieProxy — Archive Partners Page
 * Fetch data từ API cũ của tradeproxy, render + filter + paginate.
 *
 * Data source:
 *   Categories : GET /wp-json/wp/v2/provider_category
 *   Providers  : GET /wp-json/wp/v2/provider?per_page=100
 *
 * Provider object shape:
 *   title.rendered         — tên
 *   provider_data.logo     — URL logo
 *   provider_data.summary  — mô tả ngắn
 *   slug                   — slug dùng để ghép URL sang trang single vieproxy
 *   class_list[]           — mảng class, chứa "provider_category-{slug}"
 *
 * URL single được ghép từ VieProxyPartners.singleBase (wp_localize_script) + provider.slug
 * Thêm vào functions.php trong block archive-partners:
 *   wp_localize_script('archive-partners', 'VieProxyPartners', [
 *       'singleBase' => get_permalink(get_page_by_path('partner')),
 *   ]);
 */
(function () {
  "use strict";

  // ── Config ─────────────────────────────────────────────────
  var API_BASE = "https://tradeproxy.proxyflowpxp.com/wp-json/wp/v2";
  var CATEGORIES_URL = API_BASE + "/provider_category";
  var PROVIDERS_URL = API_BASE + "/provider?per_page=100";
  var PER_PAGE = 12;

  // ── State ──────────────────────────────────────────────────
  var allProviders = [];
  var filteredProviders = [];
  var currentCategory = "all";
  var currentPage = 1;

  // ── DOM refs ───────────────────────────────────────────────
  var tabsEl = document.getElementById("partnerTabs");
  var gridEl = document.getElementById("partnerGrid");
  var paginationEl = document.getElementById("partnerPagination");
  var prevBtn = document.getElementById("prevBtn");
  var nextBtn = document.getElementById("nextBtn");
  var numbersEl = document.getElementById("paginationNumbers");

  // ── Helpers ────────────────────────────────────────────────
  function stripHtml(html) {
    var tmp = document.createElement("div");
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || "";
  }

  function getCategorySlug(classList) {
    if (!classList || !Array.isArray(classList)) return "other";
    var found = classList.find(function (c) {
      return c.startsWith("provider_category-");
    });
    return found ? found.replace("provider_category-", "") : "other";
  }

  // ── Fetch helpers ──────────────────────────────────────────
  function fetchJson(url) {
    return fetch(url).then(function (res) {
      if (!res.ok) throw new Error("HTTP " + res.status);
      return res.json();
    });
  }

  // ── Render categories as tabs ──────────────────────────────
  function renderTabs(categories) {
    // Reset về "Tất cả" + categories từ API
    tabsEl.innerHTML = "";

    var allTab = makeTab("all", "Tất cả các loại", true);
    tabsEl.appendChild(allTab);

    categories.forEach(function (cat) {
      tabsEl.appendChild(makeTab(cat.slug, cat.name, false));
    });
  }

  function makeTab(slug, label, active) {
    var btn = document.createElement("button");
    btn.className = "archive-partners-tab" + (active ? " is-active" : "");
    btn.dataset.category = slug;
    btn.textContent = label;

    btn.addEventListener("click", function () {
      // Update active tab
      tabsEl.querySelectorAll(".archive-partners-tab").forEach(function (t) {
        t.classList.remove("is-active");
      });
      btn.classList.add("is-active");

      currentCategory = slug;
      currentPage = 1;
      applyFilter();
    });

    return btn;
  }

  // ── Build provider cards (hidden initially) ────────────────
  function buildCards(providers) {
    // Xóa skeleton
    gridEl.innerHTML = "";

    providers.forEach(function (provider) {
      var logo =
        provider.provider_data && provider.provider_data.logo
          ? provider.provider_data.logo
          : "";
      var title =
        provider.title && provider.title.rendered
          ? stripHtml(provider.title.rendered)
          : "Không có tên";
      var summary =
        provider.provider_data && provider.provider_data.summary
          ? stripHtml(provider.provider_data.summary)
          : "Không có mô tả";
      var providerSlug = provider.slug || "";
      var catSlug = getCategorySlug(provider.class_list);

      // Ghép URL sang trang single partner của vieproxy
      // singleBase được truyền từ PHP qua wp_localize_script
      var singleBase =
        window.VieProxyPartners && window.VieProxyPartners.singleBase
          ? window.VieProxyPartners.singleBase.replace(/\/$/, "")
          : window.location.origin + "/partner";
      // URL dạng /partners/{slug}/ — prefix cố định tránh conflict với WP pages
      var homeUrl =
        window.VieProxyPartners && window.VieProxyPartners.homeUrl
          ? window.VieProxyPartners.homeUrl.replace(/\/$/, "")
          : window.location.origin + "/partners";
      var cardUrl = providerSlug ? homeUrl + "/" + providerSlug + "/" : "#";

      var card = document.createElement("a");
      card.className = "partner-card";
      card.href = cardUrl;
      card.dataset.category = catSlug;
      card.style.display = "none"; // ẩn, renderPage sẽ mở

      card.innerHTML =
        '<div class="partner-card__header">' +
        '<div class="partner-card__logo">' +
        (logo
          ? '<img src="' + logo + '" alt="' + title + '" loading="lazy" />'
          : '<i class="fa-solid fa-link" style="font-size:22px;color:#d1d5db;"></i>') +
        "</div>" +
        '<h3 class="partner-card__name">' +
        title +
        "</h3>" +
        "</div>" +
        '<p class="partner-card__desc">' +
        summary +
        "</p>";

      gridEl.appendChild(card);
    });
  }

  // ── Filter theo category ───────────────────────────────────
  function applyFilter() {
    var allCards = Array.from(gridEl.querySelectorAll(".partner-card"));

    if (currentCategory === "all") {
      filteredProviders = allCards;
    } else {
      filteredProviders = allCards.filter(function (card) {
        return card.dataset.category === currentCategory;
      });
    }

    if (filteredProviders.length === 0) {
      // Ẩn hết cards
      allCards.forEach(function (c) {
        c.style.display = "none";
      });
      // Hiển thị empty state
      var empty = document.createElement("div");
      empty.className = "archive-partners-empty";
      empty.innerHTML =
        '<i class="fa-solid fa-box-open"></i>' +
        "<p>Không tìm thấy đối tác nào trong danh mục này.</p>";

      // Xóa empty cũ nếu có
      var old = gridEl.querySelector(".archive-partners-empty");
      if (old) old.remove();
      gridEl.appendChild(empty);
      paginationEl.style.visibility = "hidden";
    } else {
      var e = gridEl.querySelector(".archive-partners-empty");
      if (e) e.remove();
      paginationEl.style.visibility = "visible";
      renderPage();
    }
  }

  // ── Render trang hiện tại ─────────────────────────────────
  function renderPage() {
    var allCards = Array.from(gridEl.querySelectorAll(".partner-card"));
    // Ẩn tất cả
    allCards.forEach(function (c) {
      c.style.display = "none";
    });

    var totalPages = Math.ceil(filteredProviders.length / PER_PAGE);
    var start = (currentPage - 1) * PER_PAGE;
    var end = start + PER_PAGE;

    filteredProviders.slice(start, end).forEach(function (card, idx) {
      card.style.display = "block";
      card.style.animation = "none";
      // reflow trick để restart animation
      void card.offsetWidth;
      card.style.animation = "cardFadeUp 0.4s ease " + idx * 0.045 + "s both";
    });

    updatePagination(totalPages);
  }

  // ── Pagination controls ────────────────────────────────────
  function updatePagination(totalPages) {
    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === totalPages || totalPages === 0;

    numbersEl.innerHTML = "";

    if (totalPages <= 1) {
      paginationEl.style.visibility = "hidden";
      return;
    }
    paginationEl.style.visibility = "visible";

    // Window: max 5 số
    var start = Math.max(1, currentPage - 2);
    var end = Math.min(totalPages, start + 4);
    if (end - start < 4) start = Math.max(1, end - 4);

    if (start > 1) {
      numbersEl.appendChild(makePageBtn(1));
      if (start > 2) numbersEl.appendChild(makeEllipsis());
    }

    for (var i = start; i <= end; i++) {
      numbersEl.appendChild(makePageBtn(i));
    }

    if (end < totalPages) {
      if (end < totalPages - 1) numbersEl.appendChild(makeEllipsis());
      numbersEl.appendChild(makePageBtn(totalPages));
    }
  }

  function makePageBtn(num) {
    var btn = document.createElement("button");
    btn.className =
      "pagination-number" + (num === currentPage ? " is-active" : "");
    btn.textContent = num;
    btn.addEventListener("click", function () {
      currentPage = num;
      renderPage();
      scrollToTabs();
    });
    return btn;
  }

  function makeEllipsis() {
    var span = document.createElement("span");
    span.className = "pagination-ellipsis";
    span.textContent = "…";
    return span;
  }

  prevBtn.addEventListener("click", function () {
    if (currentPage > 1) {
      currentPage--;
      renderPage();
      scrollToTabs();
    }
  });

  nextBtn.addEventListener("click", function () {
    var totalPages = Math.ceil(filteredProviders.length / PER_PAGE);
    if (currentPage < totalPages) {
      currentPage++;
      renderPage();
      scrollToTabs();
    }
  });

  function scrollToTabs() {
    var offset = tabsEl.getBoundingClientRect().top + window.pageYOffset - 100;
    window.scrollTo({ top: offset, behavior: "smooth" });
  }

  // ── Error state ────────────────────────────────────────────
  function showError() {
    gridEl.innerHTML =
      '<div class="archive-partners-error">' +
      '<i class="fa-solid fa-triangle-exclamation"></i>' +
      "<p>Không thể tải dữ liệu. Vui lòng thử lại sau.</p>" +
      "</div>";
    paginationEl.style.visibility = "hidden";
  }

  // ── Init ───────────────────────────────────────────────────
  function init() {
    // Fetch categories + providers song song
    Promise.all([fetchJson(CATEGORIES_URL), fetchJson(PROVIDERS_URL)])
      .then(function (results) {
        var categories = results[0];
        var providers = results[1];

        allProviders = providers;

        renderTabs(categories);
        buildCards(providers);

        // Áp filter mặc định "all"
        filteredProviders = Array.from(
          gridEl.querySelectorAll(".partner-card"),
        );
        renderPage();
      })
      .catch(function (err) {
        console.error("[archive-partners] Fetch failed:", err);
        showError();
      });
  }

  // ── Boot ───────────────────────────────────────────────────
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
