/**
 * info-pages.js
 * VieProxy — Chính Sách Bảo Mật & Điều Khoản Dịch Vụ
 */

(function () {
  "use strict";

  /* ── Utils ─────────────────────────────────────────────── */
  function qs(sel, ctx) {
    return (ctx || document).querySelector(sel);
  }
  function qsa(sel, ctx) {
    return Array.from((ctx || document).querySelectorAll(sel));
  }

  /* ────────────────────────────────────────────────────────
     1. SMOOTH SCROLL for TOC links
  ─────────────────────────────────────────────────────── */
  function initSmoothScroll() {
    var tocLinks = qsa(".info-toc__link");
    if (!tocLinks.length) return;

    tocLinks.forEach(function (link) {
      link.addEventListener("click", function (e) {
        var href = link.getAttribute("href");
        if (!href || !href.startsWith("#")) return;

        var target = qs(href);
        if (!target) return;

        e.preventDefault();

        var offset = getScrollOffset();
        var top =
          target.getBoundingClientRect().top + window.pageYOffset - offset;

        window.scrollTo({ top: top, behavior: "smooth" });

        // Update hash without page jump
        if (history.pushState) {
          history.pushState(null, null, href);
        }
      });
    });
  }

  /* ── Calculate sticky header offset dynamically ─────── */
  function getScrollOffset() {
    var header =
      qs(".site-header") || qs("header.site-header") || qs("#masthead");
    var headerHeight = header ? header.offsetHeight : 80;
    return headerHeight + 24;
  }

  /* ────────────────────────────────────────────────────────
     2. ACTIVE TOC LINK on scroll (Intersection Observer)
  ─────────────────────────────────────────────────────── */
  function initTocHighlight() {
    var tocLinks = qsa(".info-toc__link");
    if (!tocLinks.length) return;

    var linkMap = {};
    tocLinks.forEach(function (link) {
      var href = link.getAttribute("href");
      if (href && href.startsWith("#")) {
        linkMap[href.slice(1)] = link;
      }
    });

    var sections = qsa(".info-section");
    if (!sections.length) return;

    var activeId = null;

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            var id = entry.target.id;
            if (id && linkMap[id]) {
              setActive(id);
            }
          }
        });
      },
      {
        rootMargin: "-20% 0px -70% 0px",
        threshold: 0,
      },
    );

    sections.forEach(function (sec) {
      observer.observe(sec);
    });

    function setActive(id) {
      if (activeId === id) return;
      activeId = id;

      tocLinks.forEach(function (l) {
        l.classList.remove("is-active");
      });

      var activeLink = linkMap[id];
      if (activeLink) {
        activeLink.classList.add("is-active");
        scrollTocToLink(activeLink);
      }
    }
  }

  function scrollTocToLink(link) {
    var toc = qs(".info-page-toc");
    if (!toc) return;

    var tocNav = qs(".info-toc__nav");
    if (!tocNav) return;

    var isHorizontal = getComputedStyle(tocNav).flexDirection === "row";

    if (isHorizontal) {
      var linkLeft = link.offsetLeft;
      var linkWidth = link.offsetWidth;
      var navWidth = tocNav.offsetWidth;
      tocNav.scrollTo({
        left: linkLeft - navWidth / 2 + linkWidth / 2,
        behavior: "smooth",
      });
    } else {
      var tocInner = qs(".info-toc__inner");
      if (!tocInner) return;

      var linkTop = link.offsetTop;
      var linkH = link.offsetHeight;
      var tocH = toc.offsetHeight;
      toc.scrollTo({
        top: linkTop - tocH / 2 + linkH / 2,
        behavior: "smooth",
      });
    }
  }

  /* ────────────────────────────────────────────────────────
     4. INITIAL SECTION from URL hash
  ─────────────────────────────────────────────────────── */
  function initHashScroll() {
    if (!window.location.hash) return;
    var hash = window.location.hash;
    var target = qs(hash);
    if (!target) return;

    setTimeout(function () {
      var offset = getScrollOffset();
      var top =
        target.getBoundingClientRect().top + window.pageYOffset - offset;
      window.scrollTo({ top: top, behavior: "smooth" });
    }, 300);
  }

  /* ────────────────────────────────────────────────────────
     6. SECTION ENTRANCE ANIMATION
  ─────────────────────────────────────────────────────── */
  function initSectionAnimations() {
    var sections = qsa(".info-section");
    if (!sections.length) return;

    var prefersReduced = window.matchMedia(
      "(prefers-reduced-motion: reduce)",
    ).matches;
    if (prefersReduced) return;

    sections.forEach(function (sec) {
      sec.style.transition = "none";
      void sec.offsetHeight; // force reflow
      sec.style.opacity = "0";
      sec.style.transform = "translateY(18px)";
    });

    var animObserver = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            var el = entry.target;
            requestAnimationFrame(function () {
              requestAnimationFrame(function () {
                el.style.transition = "opacity 0.5s ease, transform 0.5s ease";
                el.style.opacity = "1";
                el.style.transform = "translateY(0)";
              });
            });
            animObserver.unobserve(el);
          }
        });
      },
      { rootMargin: "0px 0px -60px 0px", threshold: 0.05 },
    );

    sections.forEach(function (sec) {
      animObserver.observe(sec);
    });
  }

  /* ────────────────────────────────────────────────────────
     7. CARD STAGGER ANIMATION
  ─────────────────────────────────────────────────────── */
  function initCardAnimations() {
    var prefersReduced = window.matchMedia(
      "(prefers-reduced-motion: reduce)",
    ).matches;
    if (prefersReduced) return;

    var cardGroups = qsa(
      ".info-cards-grid, .info-contact-grid, .info-prohibited-list",
    );

    cardGroups.forEach(function (group) {
      var cards = qsa(
        ".info-card, .info-contact-card, .info-prohibited-item",
        group,
      );

      /*
       * FIX: cùng pattern — transition:none + reflow trước,
       * rồi set opacity:0, sau đó animate khi vào viewport.
       */
      cards.forEach(function (card, i) {
        card.style.transition = "none";
        void card.offsetHeight; // force reflow
        card.style.opacity = "0";
        card.style.transform = "translateY(14px)";
      });

      var groupObserver = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              var children = qsa(
                ".info-card, .info-contact-card, .info-prohibited-item",
                entry.target,
              );
              children.forEach(function (card, i) {
                requestAnimationFrame(function () {
                  requestAnimationFrame(function () {
                    card.style.transition =
                      "opacity 0.4s ease " +
                      i * 0.08 +
                      "s, transform 0.4s ease " +
                      i * 0.08 +
                      "s";
                    card.style.opacity = "1";
                    card.style.transform = "translateY(0)";
                  });
                });
              });
              groupObserver.unobserve(entry.target);
            }
          });
        },
        { rootMargin: "0px 0px -40px 0px", threshold: 0.1 },
      );

      groupObserver.observe(group);
    });
  }

  /* ────────────────────────────────────────────────────────
   8. MOBILE TOC TOGGLE (≤1024px)
─────────────────────────────────────────────────────── */
  function initMobileToc() {
    var tocMobile = qs(".info-toc-mobile");
    var toggle = qs(".info-toc-mobile__toggle");
    var mobileContent = qs(".info-toc-mobile__content");

    if (!tocMobile || !toggle || !mobileContent) return;

    var sidebarNav = qs(".info-page-toc .info-toc__nav");
    if (sidebarNav && mobileContent.children.length === 0) {
      var clonedNav = sidebarNav.cloneNode(true);
      mobileContent.appendChild(clonedNav);
    }

    // ── Toggle open/close ──
    toggle.addEventListener("click", function () {
      var isOpen = tocMobile.classList.toggle("is-open");
      this.setAttribute("aria-expanded", isOpen ? "true" : "false");
    });

    // ── Đóng khi click link ──
    var links = qsa(".info-toc-mobile .info-toc__link");
    links.forEach(function (link) {
      link.addEventListener("click", function () {
        tocMobile.classList.remove("is-open");
        toggle.setAttribute("aria-expanded", "false");
      });
    });
  }

  /* ────────────────────────────────────────────────────────
     INIT
  ─────────────────────────────────────────────────────── */
  function init() {
    if (!qs(".main-info-page")) return;

    initSmoothScroll();
    initTocHighlight();
    initHashScroll();
    initSectionAnimations();
    initCardAnimations();
    initMobileToc();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
