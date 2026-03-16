// ============================================
// VIEPROXY SINGLE BLOG POST - JAVASCRIPT
// ============================================

(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    const articleBody = document.getElementById("articleBody");
    const tocList = document.getElementById("tocList");
    const tocListMobile = document.getElementById("tocListMobile");

    if (articleBody && tocList) {
      const headings = articleBody.querySelectorAll("h2, h3");
      const tocItems = [];

      headings.forEach(function (heading, index) {
        if (!heading.id) {
          const slugBase = heading.textContent
            .trim()
            .toLowerCase()
            .replace(/[^a-z0-9\s\u00C0-\u024F\u1E00-\u1EFF]/g, "")
            .replace(/\s+/g, "-")
            .substring(0, 60);
          heading.id = slugBase + "-" + index;
        }

        const isSub = heading.tagName === "H3";
        const li = document.createElement("li");
        if (isSub) li.classList.add("toc-sub");

        const a = document.createElement("a");
        a.href = "#" + heading.id;
        a.textContent = heading.textContent.trim();

        // Smooth scroll khi click TOC
        a.addEventListener("click", function (e) {
          e.preventDefault();
          const target = document.getElementById(heading.id);
          if (target) {
            const offset = 80; // height của header sticky
            const top =
              target.getBoundingClientRect().top + window.scrollY - offset;
            window.scrollTo({ top: top, behavior: "smooth" });

            // Đóng mobile TOC sau khi click
            closeMobileToc();
          }
        });

        li.appendChild(a);
        tocList.appendChild(li);

        // Clone cho mobile TOC
        if (tocListMobile) {
          const liMobile = li.cloneNode(true);
          // Gán lại event vì cloneNode không clone event listeners
          liMobile.querySelector("a").addEventListener("click", function (e) {
            e.preventDefault();
            const target = document.getElementById(heading.id);
            if (target) {
              const offset = 80;
              const top =
                target.getBoundingClientRect().top + window.scrollY - offset;
              window.scrollTo({ top: top, behavior: "smooth" });
              closeMobileToc();
            }
          });
          tocListMobile.appendChild(liMobile);
        }

        tocItems.push({ id: heading.id, el: heading });
      });

      // ── 2. SCROLL SPY — highlight TOC item đang xem ─────────
      if (tocItems.length > 0) {
        const allTocLinks = document.querySelectorAll(
          "#tocList a, #tocListMobile a",
        );

        let ticking = false;

        function onScroll() {
          if (!ticking) {
            window.requestAnimationFrame(function () {
              updateActiveToc();
              ticking = false;
            });
            ticking = true;
          }
        }

        function updateActiveToc() {
          const scrollY = window.scrollY;
          const offset = 120;
          let activeId = tocItems[0].id;

          for (let i = 0; i < tocItems.length; i++) {
            const top = tocItems[i].el.getBoundingClientRect().top + scrollY;
            if (scrollY >= top - offset) {
              activeId = tocItems[i].id;
            }
          }

          allTocLinks.forEach(function (link) {
            const href = link.getAttribute("href").replace("#", "");
            link.classList.toggle("toc-active", href === activeId);
          });
        }

        window.addEventListener("scroll", onScroll, { passive: true });
        updateActiveToc(); // Chạy ngay khi load
      }
    }

    // ── 3. MOBILE TOC TOGGLE ──────────────────────────────────
    const tocMobile = document.getElementById("tocMobile");
    const tocMobileToggle = document.getElementById("tocMobileToggle");

    function closeMobileToc() {
      if (tocMobile) {
        tocMobile.classList.remove("open");
        if (tocMobileToggle) {
          tocMobileToggle.setAttribute("aria-expanded", "false");
        }
      }
    }

    if (tocMobileToggle && tocMobile) {
      tocMobileToggle.addEventListener("click", function () {
        const isOpen = tocMobile.classList.toggle("open");
        this.setAttribute("aria-expanded", isOpen ? "true" : "false");
      });
    }

    const faqQuestions = document.querySelectorAll(".blog-faq-question");

    faqQuestions.forEach(function (question) {
      question.addEventListener("click", function () {
        const faqItem = this.closest(".blog-faq-item");
        const isOpen = faqItem.classList.contains("active-faq");

        document
          .querySelectorAll(".blog-faq-item.active-faq")
          .forEach(function (item) {
            item.classList.remove("active-faq");
          });

        if (!isOpen) {
          faqItem.classList.add("active-faq");
        }
      });
    });

    // ── 5. RELATED CARDS — entrance animation ─────────────────
    const relatedCards = document.querySelectorAll(".related-card");
    relatedCards.forEach(function (card, index) {
      card.style.opacity = "0";
      card.style.transform = "translateY(16px)";
      card.style.transition = "none";

      setTimeout(
        function () {
          card.style.transition = "opacity 0.4s ease, transform 0.4s ease";
          card.style.opacity = "1";
          card.style.transform = "translateY(0)";
        },
        200 + index * 100,
      );
    });

    // ── 6. Related card image lazy fade-in ─────────────────────
    const relatedImages = document.querySelectorAll(".related-card__image");
    relatedImages.forEach(function (img) {
      img.style.opacity = "0";
      img.style.transition = "opacity 0.4s ease";

      if (img.complete && img.naturalWidth > 0) {
        img.style.opacity = "1";
      } else {
        img.addEventListener("load", function () {
          this.style.opacity = "1";
        });
      }
    });

    console.log("Vieproxy Single Blog loaded ✓");
  });
})();
