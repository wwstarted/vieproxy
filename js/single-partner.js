/**
 * single-partner.js
 * VieProxy — Single Partner Detail Page
 *
 * Fetch: GET /wp-json/wp/v2/provider?slug={slug}
 * provider_data shape:
 *   logo             — URL ảnh logo
 *   content          — text mô tả (paragraph cách nhau \r\n\r\n)
 *   tags[]           — mảng string tag
 *   website          — URL website đối tác
 *   contact_link     — URL trang liên hệ
 *   features_overview[{title, items:[{title}]}] — sidebar sections
 */
(function () {
  "use strict";

  var API_BASE = "https://tradeproxy.proxyflowpxp.com/wp-json/wp/v2";

  // ── Lấy slug từ path /partners/{slug}/ ────────────────────
  // Fallback ?slug= cho môi trường dev không có rewrite
  function getSlug() {
    var parts = location.pathname.split("/").filter(Boolean);
    // /vieproxy/partners/maslogin/ → ["vieproxy", "partners", "maslogin"]
    var idx = parts.indexOf("partners");
    if (idx !== -1 && parts[idx + 1]) {
      return parts[idx + 1];
    }
    // Fallback query param
    return new URLSearchParams(window.location.search).get("slug") || null;
  }

  // ── DOM refs ───────────────────────────────────────────────
  var contentEl = document.getElementById("partnerContent");
  var breadcrumbCur = document.getElementById("breadcrumbCurrent");

  // ── Helpers ────────────────────────────────────────────────
  function escHtml(str) {
    var d = document.createElement("div");
    d.textContent = str;
    return d.innerHTML;
  }

  // Tách content thành paragraphs
  function formatContent(raw) {
    if (!raw) return "<p>Không có nội dung.</p>";
    return raw
      .split(/\r\n\r\n|\n\n/)
      .map(function (para) {
        return para.trim();
      })
      .filter(Boolean)
      .map(function (para) {
        return "<p>" + para.replace(/\r\n|\n/g, "<br>") + "</p>";
      })
      .join("");
  }

  // ── Render toàn bộ nội dung ────────────────────────────────
  function render(post) {
    var data = post.provider_data || {};
    var title =
      post.title && post.title.rendered ? post.title.rendered : "Không có tên";

    var logo = data.logo || "";
    var content = data.content || "";
    var tags = Array.isArray(data.tags) ? data.tags : [];
    var website = data.website || "";
    var contactLink = data.contact_link || "";
    var features = Array.isArray(data.features_overview)
      ? data.features_overview
      : [];

    // Cập nhật breadcrumb
    if (breadcrumbCur) breadcrumbCur.textContent = title;

    // ── Tags ─────────────────────────────────────────────
    var tagsHtml = "";
    if (tags.length > 0) {
      tagsHtml =
        '<div class="single-partner-tags">' +
        tags
          .map(function (tag) {
            return (
              '<span class="single-partner-tag">' + escHtml(tag) + "</span>"
            );
          })
          .join("") +
        "</div>";
    }

    // ── Action buttons ────────────────────────────────────
    var actionsHtml =
      '<div class="single-partner-actions">' +
      (website
        ? '<a href="' +
          escHtml(website) +
          '" class="sp-btn sp-btn--outline" target="_blank" rel="noopener noreferrer">' +
          '<i class="fa-solid fa-arrow-up-right-from-square"></i> Tìm hiểu thêm' +
          "</a>"
        : "") +
      (contactLink
        ? '<a href="' +
          escHtml(contactLink) +
          '" class="sp-btn sp-btn--primary" target="_blank" rel="noopener noreferrer">' +
          '<i class="fa-solid fa-link"></i> Liên hệ chúng tôi' +
          "</a>"
        : '<a href="' +
          escHtml(window._vieproxyHome || "/") +
          'contact" class="sp-btn sp-btn--primary">' +
          '<i class="fa-solid fa-link"></i> Liên hệ chúng tôi' +
          "</a>") +
      "</div>";

    // ── Link box ──────────────────────────────────────────
    var linkboxHtml = "";
    if (website) {
      linkboxHtml =
        '<div class="single-partner-linkbox">' +
        '<p class="single-partner-linkbox__label">Website</p>' +
        '<a href="' +
        escHtml(website) +
        '" target="_blank" rel="noopener noreferrer">' +
        '<i class="fa-solid fa-globe"></i>' +
        escHtml(website) +
        "</a>" +
        "</div>";
    }

    // ── Sidebar: logo + name ──────────────────────────────
    var logoHtml = logo
      ? '<img src="' +
        escHtml(logo) +
        '" alt="' +
        escHtml(title) +
        '" loading="lazy" />'
      : '<i class="fa-solid fa-link" style="font-size:22px;color:#d1d5db;"></i>';

    // ── Sidebar: features_overview sections ───────────────
    var featuresHtml = "";
    if (features.length > 0) {
      featuresHtml = features
        .map(function (section) {
          var sectionTitle = section.title || "";
          var items = Array.isArray(section.items) ? section.items : [];
          var itemsHtml = items
            .map(function (item) {
              return (
                '<div class="single-partner-sidebar__item">' +
                '<i class="fa-solid fa-check"></i>' +
                "<span>" +
                escHtml(item.title || "") +
                "</span>" +
                "</div>"
              );
            })
            .join("");

          return (
            '<div class="single-partner-sidebar__section">' +
            (sectionTitle
              ? '<h3 class="single-partner-sidebar__section-title">' +
                escHtml(sectionTitle) +
                "</h3>"
              : "") +
            itemsHtml +
            "</div>"
          );
        })
        .join("");
    }

    // ── Assemble full HTML ────────────────────────────────
    var html =
      '<h1 class="single-partner-title">' +
      title +
      "</h1>" +
      '<div class="single-partner-grid">' +
      // Left
      '<div class="left-col">' +
      tagsHtml +
      actionsHtml +
      '<div class="single-partner-description">' +
      formatContent(content) +
      "</div>" +
      linkboxHtml +
      "</div>" +
      // Right
      '<div class="right-col">' +
      '<div class="single-partner-sidebar-card">' +
      '<div class="single-partner-sidebar__logo-row">' +
      '<div class="single-partner-sidebar__logo">' +
      logoHtml +
      "</div>" +
      '<h2 class="single-partner-sidebar__name">' +
      escHtml(title) +
      "</h2>" +
      "</div>" +
      featuresHtml +
      "</div>" +
      "</div>" +
      "</div>"; // .single-partner-grid

    contentEl.innerHTML = html;
    contentEl.classList.add("is-loaded");
  }

  // ── Error state ────────────────────────────────────────────
  function showError(msg) {
    if (breadcrumbCur) breadcrumbCur.textContent = "Không tìm thấy";
    contentEl.innerHTML =
      '<div class="single-partner-error">' +
      '<i class="fa-solid fa-triangle-exclamation"></i>' +
      "<h2>Không tìm thấy đối tác</h2>" +
      "<p>" +
      (msg || "Trang này không tồn tại hoặc đã bị xoá.") +
      "</p>" +
      '<a href="/partners"><i class="fa-solid fa-arrow-left"></i> Quay lại danh sách đối tác</a>' +
      "</div>";
  }

  // ── Fetch + init ───────────────────────────────────────────
  function init() {
    var slug = getSlug();

    if (!slug) {
      showError("Không xác định được trang đối tác.");
      return;
    }

    fetch(API_BASE + "/provider?slug=" + encodeURIComponent(slug))
      .then(function (res) {
        if (!res.ok) throw new Error("HTTP " + res.status);
        return res.json();
      })
      .then(function (posts) {
        if (!posts || posts.length === 0) {
          showError("Không tìm thấy đối tác với slug: " + slug);
          return;
        }
        render(posts[0]);
      })
      .catch(function (err) {
        console.error("[single-partner] Fetch error:", err);
        showError("Không thể tải dữ liệu. Vui lòng thử lại sau.");
      });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
