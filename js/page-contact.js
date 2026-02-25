/**
 * page-contact.js
 * VieProxy — Trang Liên Hệ
 * Handles: Form validation, FAQ accordion, char counter,
 *          back-to-top, entrance animations, AJAX submit
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
  function on(el, ev, fn, opts) {
    if (el) el.addEventListener(ev, fn, opts);
  }

  /* ────────────────────────────────────────────────────────
     1. FAQ ACCORDION
  ─────────────────────────────────────────────────────── */
  function initFaqAccordion() {
    var items = qsa(".contact-faq-item");
    if (!items.length) return;

    items.forEach(function (item) {
      var btn = qs(".contact-faq-question", item);
      var answer = qs(".contact-faq-answer", item);
      if (!btn || !answer) return;

      // Set initial state for open item
      if (item.classList.contains("is-open")) {
        answer.style.display = "block";
        btn.setAttribute("aria-expanded", "true");
      }

      on(btn, "click", function () {
        var isOpen = item.classList.contains("is-open");

        // Close all
        items.forEach(function (other) {
          other.classList.remove("is-open");
          var otherBtn = qs(".contact-faq-question", other);
          var otherAnswer = qs(".contact-faq-answer", other);
          if (otherBtn) otherBtn.setAttribute("aria-expanded", "false");
          if (otherAnswer) slideUp(otherAnswer);
        });

        // Open clicked if it was closed
        if (!isOpen) {
          item.classList.add("is-open");
          btn.setAttribute("aria-expanded", "true");
          slideDown(answer);
        }
      });
    });
  }

  /* ── Slide helpers ───────────────────────────────────── */
  function slideDown(el) {
    el.style.display = "block";
    el.style.overflow = "hidden";
    el.style.maxHeight = "0";
    el.style.transition = "max-height 0.3s ease";
    // Trigger reflow
    void el.offsetHeight;
    el.style.maxHeight = el.scrollHeight + "px";

    on(el, "transitionend", function handler() {
      el.style.maxHeight = "none";
      el.style.overflow = "";
      el.style.transition = "";
      el.removeEventListener("transitionend", handler);
    });
  }

  function slideUp(el) {
    if (el.style.display === "none") return;
    el.style.overflow = "hidden";
    el.style.maxHeight = el.scrollHeight + "px";
    el.style.transition = "max-height 0.25s ease";
    void el.offsetHeight;
    el.style.maxHeight = "0";

    on(el, "transitionend", function handler() {
      el.style.display = "none";
      el.style.maxHeight = "";
      el.style.overflow = "";
      el.style.transition = "";
      el.removeEventListener("transitionend", handler);
    });
  }

  /* ────────────────────────────────────────────────────────
     2. CHARACTER COUNTER for textarea
  ─────────────────────────────────────────────────────── */
  function initCharCounter() {
    var textarea = qs("#contactMessage");
    var counter = qs("#charCount");
    var maxLength = 1000;

    if (!textarea || !counter) return;

    textarea.setAttribute("maxlength", maxLength);

    function updateCounter() {
      var len = textarea.value.length;
      counter.textContent = len + " / " + maxLength;
      counter.classList.toggle("is-warning", len > maxLength * 0.8);
      counter.classList.toggle("is-limit", len >= maxLength);
    }

    on(textarea, "input", updateCounter);
    updateCounter();
  }

  /* ────────────────────────────────────────────────────────
     3. FORM VALIDATION
  ─────────────────────────────────────────────────────── */
  var validators = {
    name: function (val) {
      return val.trim().length >= 2;
    },
    email: function (val) {
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val.trim());
    },
    subject: function (val) {
      return val !== "";
    },
    message: function (val) {
      return val.trim().length >= 20;
    },
    consent: function (_, el) {
      return el && el.checked;
    },
  };

  function getFieldEl(id) {
    return qs("#field-" + id);
  }

  function validateField(id, value, el) {
    var fn = validators[id];
    var isValid = fn ? fn(value, el) : true;
    var field = getFieldEl(id);
    if (!field) return isValid;

    field.classList.toggle("has-error", !isValid);
    field.classList.toggle("has-success", isValid && value.trim() !== "");
    return isValid;
  }

  function clearFieldState(id) {
    var field = getFieldEl(id);
    if (field) {
      field.classList.remove("has-error", "has-success");
    }
  }

  /* Live validation on blur */
  function initLiveValidation() {
    var fields = [
      { id: "name", input: qs("#contactName") },
      { id: "email", input: qs("#contactEmail") },
      { id: "subject", input: qs("#contactSubject") },
      { id: "message", input: qs("#contactMessage") },
      { id: "consent", input: qs("#contactConsent") },
    ];

    fields.forEach(function (f) {
      if (!f.input) return;

      on(f.input, "blur", function () {
        var val = f.input.type === "checkbox" ? "" : f.input.value;
        validateField(f.id, val, f.input);
      });

      on(f.input, "input", function () {
        // Clear error on typing (don't show success until blur)
        clearFieldState(f.id);
      });
    });
  }

  /* Full form validation */
  function validateForm() {
    var valid = true;
    var fields = [
      { id: "name", val: (qs("#contactName") || {}).value || "" },
      { id: "email", val: (qs("#contactEmail") || {}).value || "" },
      { id: "subject", val: (qs("#contactSubject") || {}).value || "" },
      { id: "message", val: (qs("#contactMessage") || {}).value || "" },
      { id: "consent", val: "", el: qs("#contactConsent") },
    ];

    fields.forEach(function (f) {
      if (!validateField(f.id, f.val, f.el)) valid = false;
    });

    return valid;
  }

  /* ────────────────────────────────────────────────────────
     4. FORM SUBMIT (AJAX)
  ─────────────────────────────────────────────────────── */
  function initFormSubmit() {
    var form = qs("#contactForm");
    var btn = qs("#contactSubmit");
    var success = qs("#formSuccess");
    var error = qs("#formError");

    if (!form) return;

    on(form, "submit", function (e) {
      e.preventDefault();

      // Hide alerts
      if (success) success.style.display = "none";
      if (error) error.style.display = "none";

      // Validate
      if (!validateForm()) {
        // Scroll to first error
        var firstError = qs(".contact-form-field.has-error");
        if (firstError) {
          var offset = 120;
          var top =
            firstError.getBoundingClientRect().top +
            window.pageYOffset -
            offset;
          window.scrollTo({ top: top, behavior: "smooth" });
        }
        return;
      }

      // Show loading state
      setLoading(btn, true);

      var formData = new FormData(form);

      // Check if WordPress AJAX is available
      var ajaxUrl =
        typeof ajaxurl !== "undefined" ? ajaxurl : form.getAttribute("action");

      fetch(ajaxUrl, {
        method: "POST",
        body: formData,
        headers: { "X-Requested-With": "XMLHttpRequest" },
      })
        .then(function (res) {
          return res.json();
        })
        .then(function (data) {
          setLoading(btn, false);
          if (data && data.success) {
            showAlert(success, error, "success");
            form.reset();
            // Reset validation states
            qsa(".contact-form-field", form).forEach(function (f) {
              f.classList.remove("has-error", "has-success");
            });
            // Reset char counter
            var counter = qs("#charCount");
            if (counter) counter.textContent = "0 / 1000";
          } else {
            showAlert(success, error, "error");
            var errEl = qs(".contact-form-alert--error p", form.parentNode);
            if (errEl && data && data.data && data.data.message) {
              errEl.textContent = data.data.message;
            }
          }
        })
        .catch(function () {
          setLoading(btn, false);
          showAlert(success, error, "error");
        });
    });
  }

  function setLoading(btn, loading) {
    if (!btn) return;
    var textEl = qs(".contact-form-submit__text", btn);
    var loadingEl = qs(".contact-form-submit__loading", btn);

    btn.disabled = loading;
    if (textEl) textEl.style.display = loading ? "none" : "";
    if (loadingEl) loadingEl.style.display = loading ? "" : "none";
  }

  function showAlert(successEl, errorEl, type) {
    if (type === "success" && successEl) {
      successEl.style.display = "flex";
      successEl.scrollIntoView({ behavior: "smooth", block: "nearest" });
    } else if (type === "error" && errorEl) {
      errorEl.style.display = "flex";
      errorEl.scrollIntoView({ behavior: "smooth", block: "nearest" });
    }
  }

  /* ────────────────────────────────────────────────────────
     5. BACK TO TOP
  ─────────────────────────────────────────────────────── */
  function initBackToTop() {
    var btn = qs("#backToTop");
    if (!btn) return;

    on(
      window,
      "scroll",
      function () {
        btn.classList.toggle("is-visible", window.pageYOffset > 400);
      },
      { passive: true },
    );

    on(btn, "click", function () {
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  }

  /* ────────────────────────────────────────────────────────
     6. ENTRANCE ANIMATIONS
  ─────────────────────────────────────────────────────── */
  function initAnimations() {
    var reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    if (reduced) return;

    // Animate quick cards
    var quickCards = qsa(".contact-quick-card");
    animateGroup(quickCards, "0 0 -30px 0");

    // Animate sidebar cards
    var sideCards = qsa(".contact-sidebar-card");
    animateGroup(sideCards, "0 0 -40px 0", 0.1);

    // Animate form card
    var formCard = qs(".contact-form-card");
    if (formCard) {
      animateElement(formCard, "0 0 -50px 0");
    }

    // Animate FAQ items
    var faqItems = qsa(".contact-faq-item");
    animateGroup(faqItems, "0 0 -40px 0", 0.06);
  }

  function animateElement(el, rootMargin, delay) {
    el.style.transition = "none";
    void el.offsetHeight;
    // el.style.opacity = "0";
    // el.style.transform = "translateY(20px)";
    el.style.opacity = "1";

    var obs = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            requestAnimationFrame(function () {
              requestAnimationFrame(function () {
                el.style.transition =
                  "opacity 0.5s ease " +
                  (delay || 0) +
                  "s, transform 0.5s ease " +
                  (delay || 0) +
                  "s";
                el.style.opacity = "1";
                el.style.transform = "translateY(0)";
              });
            });
            obs.unobserve(entry.target);
          }
        });
      },
      { rootMargin: rootMargin || "0px 0px -30px 0px", threshold: 0.05 },
    );

    obs.observe(el);
  }

  function animateGroup(els, rootMargin, stagger) {
    els.forEach(function (el, i) {
      animateElement(el, rootMargin, i * (stagger || 0.08));
    });
  }

  /* ────────────────────────────────────────────────────────
     7. READING PROGRESS BAR
  ─────────────────────────────────────────────────────── */
  function initProgressBar() {
    if (qs("#infoReadingProgress") || qs("#contactReadingProgress")) return;

    var bar = document.createElement("div");
    bar.id = "contactReadingProgress";
    bar.style.cssText = [
      "position:fixed",
      "top:0",
      "left:0",
      "height:3px",
      "width:0%",
      "background:linear-gradient(90deg,#4facf7 0%,#007bf3 100%)",
      "z-index:9999",
      "transition:width 0.1s ease",
      "pointer-events:none",
    ].join(";");
    document.body.appendChild(bar);

    on(
      window,
      "scroll",
      function () {
        var docH = document.documentElement.scrollHeight;
        var winH = window.innerHeight;
        var scrolled = window.pageYOffset;
        var pct = Math.min(100, Math.round((scrolled / (docH - winH)) * 100));
        bar.style.width = pct + "%";
      },
      { passive: true },
    );
  }

  /* ────────────────────────────────────────────────────────
     8. FLOATING LABEL EFFECT (focus highlight for input icon)
  ─────────────────────────────────────────────────────── */
  function initInputEffects() {
    var inputs = qsa(".contact-form-input");
    inputs.forEach(function (input) {
      var wrap = input.closest(".contact-form-input-wrap");
      var icon = wrap ? qs(".contact-form-input-icon", wrap) : null;

      on(input, "focus", function () {
        if (icon) icon.style.color = "#007bf3";
      });

      on(input, "blur", function () {
        if (icon) icon.style.color = input.value ? "#007bf3" : "";
      });
    });
  }

  /* ────────────────────────────────────────────────────────
     INIT
  ─────────────────────────────────────────────────────── */
  function init() {
    if (!qs(".main-contact-page")) return;

    initFaqAccordion();
    initCharCounter();
    initLiveValidation();
    initFormSubmit();
    initBackToTop();
    initAnimations();
    initProgressBar();
    initInputEffects();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
