/**
 * VieProxy — register.js
 * Flow: Form → Send OTP → Verify OTP → Create Account → Success
 */

(function () {
  "use strict";

  const API_BASE = window.vpRegData?.apiBase || "/wp-json/vieproxy/v1";
  const LOGIN_URL = window.vpRegData?.loginUrl || "/dang-nhap";

  // ── Panels & Steps ────────────────────────────────────────────────────
  const stepPanels = {
    1: document.getElementById("vp-step-1"),
    2: document.getElementById("vp-step-2"),
    3: document.getElementById("vp-step-3"),
  };
  const visSteps = {
    1: document.getElementById("vis-step-1"),
    2: document.getElementById("vis-step-2"),
    3: document.getElementById("vis-step-3"),
  };

  // ── Step 1 elements ───────────────────────────────────────────────────
  const regForm = document.getElementById("vp-reg-form");
  const regBtn = document.getElementById("vp-reg-btn");
  const alert1 = document.getElementById("vp-reg-alert-1");

  // ── Step 2 elements ───────────────────────────────────────────────────
  const otpBoxes = document.querySelectorAll(".vp-otp-box");
  const otpVerifyBtn = document.getElementById("vp-otp-verify-btn");
  const otpResendBtn = document.getElementById("vp-otp-resend");
  const otpTimeEl = document.getElementById("vp-otp-time");
  const otpCountdown = document.getElementById("vp-otp-countdown");
  const emailDisplay = document.getElementById("vp-otp-email-display");
  const backToForm = document.getElementById("vp-back-to-form");
  const alert2 = document.getElementById("vp-reg-alert-2");

  // ── State ─────────────────────────────────────────────────────────────
  let formData = {}; // dữ liệu form lưu tạm
  let otpTimer = null;
  let otpSeconds = 300; // 5 phút

  // ══════════════════════════════════════════════════════════════════════
  //  UTILS
  // ══════════════════════════════════════════════════════════════════════

  function showAlert(el, msg, type = "error") {
    el.textContent = msg;
    el.className = `vp-alert vp-alert--${type}`;
    el.style.display = "flex";
  }
  function hideAlert(el) {
    el.style.display = "none";
    el.textContent = "";
  }

  function setLoading(btn, loading) {
    const text = btn.querySelector(".vp-btn__text");
    const spinner = btn.querySelector(".vp-btn__spinner");
    btn.disabled = loading;
    text.style.display = loading ? "none" : "";
    spinner.style.display = loading ? "flex" : "none";
  }

  function clearErrors() {
    document
      .querySelectorAll(".vp-field__error")
      .forEach((el) => (el.textContent = ""));
    document
      .querySelectorAll(".vp-field__input")
      .forEach((el) => el.classList.remove("is-error"));
  }

  function setFieldError(errId, inputId, msg) {
    const errEl = document.getElementById(errId);
    const inputEl = document.getElementById(inputId);
    if (errEl) errEl.textContent = msg;
    if (inputEl) inputEl.classList.add("is-error");
  }

  async function apiPost(endpoint, body, token = null) {
    const headers = { "Content-Type": "application/json" };
    if (token) headers["Authorization"] = `Bearer ${token}`;
    const res = await fetch(`${API_BASE}${endpoint}`, {
      method: "POST",
      headers,
      credentials: "include",
      body: JSON.stringify(body),
    });
    const data = await res.json();
    return { ok: res.ok, data };
  }

  /** Chuyển panel */
  function goToStep(step) {
    Object.values(stepPanels).forEach((el, i) => {
      if (el) el.style.display = i + 1 === step ? "" : "none";
    });
    // Cập nhật visual steps
    Object.entries(visSteps).forEach(([k, el]) => {
      if (!el) return;
      const n = parseInt(k);
      el.classList.remove("vp-reg-step--active", "vp-reg-step--done");
      if (n < step) el.classList.add("vp-reg-step--done");
      else if (n === step) el.classList.add("vp-reg-step--active");
    });
  }

  // ══════════════════════════════════════════════════════════════════════
  //  PASSWORD STRENGTH
  // ══════════════════════════════════════════════════════════════════════

  const passInput = document.getElementById("vp-reg-password");
  const strengthWrap = document.getElementById("vp-strength");
  const strengthFill = document.getElementById("vp-strength-fill");
  const strengthText = document.getElementById("vp-strength-text");

  function checkStrength(password) {
    if (!strengthWrap) return;

    if (!password) {
      strengthWrap.style.display = "none";
      return;
    }
    strengthWrap.style.display = "block";

    let score = 0;
    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    if (/[a-z]/.test(password)) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/\d/.test(password)) score++;
    if (/[^a-zA-Z0-9]/.test(password)) score++;

    let label, color, width;
    if (score <= 2) {
      label = "Yếu";
      color = "#ef4444";
      width = "33%";
    } else if (score <= 4) {
      label = "Trung bình";
      color = "#f59e0b";
      width = "66%";
    } else {
      label = "Mạnh";
      color = "#10b981";
      width = "100%";
    }

    strengthFill.style.width = width;
    strengthFill.style.background = color;
    strengthText.style.color = color;
    strengthText.textContent = `Độ mạnh: ${label}`;
  }

  if (passInput) {
    passInput.addEventListener("input", (e) => checkStrength(e.target.value));
  }

  // ══════════════════════════════════════════════════════════════════════
  //  TOGGLE PASSWORD VISIBILITY
  // ══════════════════════════════════════════════════════════════════════

  document.querySelectorAll(".vp-field__toggle-pass").forEach((btn) => {
    btn.addEventListener("click", function () {
      const targetId = this.dataset.target;
      const input = document.getElementById(targetId);
      if (!input) return;
      const isPass = input.type === "password";
      input.type = isPass ? "text" : "password";
      const eyeOpen = this.querySelector(".vp-eye--open");
      const eyeClosed = this.querySelector(".vp-eye--closed");
      if (eyeOpen) eyeOpen.style.display = isPass ? "none" : "";
      if (eyeClosed) eyeClosed.style.display = isPass ? "" : "none";
    });
  });

  // ══════════════════════════════════════════════════════════════════════
  //  STEP 1: VALIDATE & SEND OTP
  // ══════════════════════════════════════════════════════════════════════

  function validateForm(data) {
    let valid = true;

    if (!data.first_name) {
      setFieldError("vp-err-first-name", "vp-first-name", "Vui lòng nhập họ.");
      valid = false;
    }
    if (!data.last_name) {
      setFieldError("vp-err-last-name", "vp-last-name", "Vui lòng nhập tên.");
      valid = false;
    }
    if (!data.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) {
      setFieldError("vp-err-email", "vp-reg-email", "Email không hợp lệ.");
      valid = false;
    }
    if (!data.password || data.password.length < 8) {
      setFieldError(
        "vp-err-password",
        "vp-reg-password",
        "Mật khẩu phải có ít nhất 8 ký tự.",
      );
      valid = false;
    } else if (
      !/[a-z]/.test(data.password) ||
      !/[A-Z]/.test(data.password) ||
      !/\d/.test(data.password)
    ) {
      setFieldError(
        "vp-err-password",
        "vp-reg-password",
        "Cần có chữ hoa, chữ thường và số.",
      );
      valid = false;
    }
    if (data.password !== data.confirm_password) {
      setFieldError(
        "vp-err-confirm",
        "vp-reg-confirm",
        "Mật khẩu xác nhận không khớp.",
      );
      valid = false;
    }
    if (!data.terms) {
      document.getElementById("vp-err-terms").textContent =
        "Vui lòng đồng ý điều khoản dịch vụ.";
      valid = false;
    }
    return valid;
  }

  if (regForm) {
    regForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      hideAlert(alert1);
      clearErrors();

      const captchaToken =
        typeof grecaptcha !== "undefined" ? grecaptcha.getResponse() : "";
      if (!captchaToken) {
        document.getElementById("vp-err-captcha").textContent =
          "Vui lòng xác minh reCAPTCHA.";
        return;
      }

      formData = {
        first_name: document.getElementById("vp-first-name").value.trim(),
        last_name: document.getElementById("vp-last-name").value.trim(),
        email: document.getElementById("vp-reg-email").value.trim(),
        password: document.getElementById("vp-reg-password").value,
        confirm_password: document.getElementById("vp-reg-confirm").value,
        recaptcha_token: captchaToken,
        terms: document.getElementById("vp-terms").checked,
      };

      if (!validateForm(formData)) return;

      setLoading(regBtn, true);

      try {
        // Gửi OTP qua endpoint register-send-otp
        const { ok, data } = await apiPost("/auth/register-send-otp", {
          email: formData.email,
          recaptcha_token: formData.recaptcha_token,
        });

        if (ok && data.success) {
          // Chuyển sang step 2
          if (emailDisplay) emailDisplay.textContent = formData.email;
          goToStep(2);
          startOtpTimer();
          focusFirstOtpBox();
        } else {
          const msg =
            data?.message || "Không thể gửi mã OTP. Vui lòng thử lại.";
          showAlert(alert1, msg, "error");
          if (typeof grecaptcha !== "undefined") grecaptcha.reset();
        }
      } catch (err) {
        console.error("[VP Register]", err);
        showAlert(
          alert1,
          "Không thể kết nối máy chủ. Vui lòng thử lại.",
          "error",
        );
        if (typeof grecaptcha !== "undefined") grecaptcha.reset();
      } finally {
        setLoading(regBtn, false);
      }
    });
  }

  // ══════════════════════════════════════════════════════════════════════
  //  OTP INPUTS — keyboard navigation & auto-advance
  // ══════════════════════════════════════════════════════════════════════

  function focusFirstOtpBox() {
    if (otpBoxes.length > 0) otpBoxes[0].focus();
  }

  function getOtpValue() {
    return Array.from(otpBoxes)
      .map((b) => b.value)
      .join("");
  }

  function clearOtpBoxes() {
    otpBoxes.forEach((b) => {
      b.value = "";
      b.classList.remove("is-filled", "is-error");
    });
  }

  otpBoxes.forEach((box, idx) => {
    box.addEventListener("input", function () {
      // Chỉ giữ số
      this.value = this.value.replace(/\D/g, "").slice(-1);

      if (this.value) {
        this.classList.add("is-filled");
        // Auto-advance
        if (idx < otpBoxes.length - 1) otpBoxes[idx + 1].focus();
        // Auto-submit khi đủ 6 số
        if (getOtpValue().length === 6) verifyOtp();
      } else {
        this.classList.remove("is-filled");
      }
    });

    box.addEventListener("keydown", function (e) {
      if (e.key === "Backspace") {
        if (!this.value && idx > 0) {
          otpBoxes[idx - 1].value = "";
          otpBoxes[idx - 1].classList.remove("is-filled");
          otpBoxes[idx - 1].focus();
        }
      }
      if (e.key === "ArrowLeft" && idx > 0) otpBoxes[idx - 1].focus();
      if (e.key === "ArrowRight" && idx < otpBoxes.length - 1)
        otpBoxes[idx + 1].focus();
    });

    // Handle paste
    box.addEventListener("paste", function (e) {
      e.preventDefault();
      const pasted = (e.clipboardData || window.clipboardData)
        .getData("text")
        .replace(/\D/g, "")
        .slice(0, 6);
      pasted.split("").forEach((ch, i) => {
        if (otpBoxes[i]) {
          otpBoxes[i].value = ch;
          otpBoxes[i].classList.add("is-filled");
        }
      });
      // Focus cuối hoặc auto-verify
      const last = Math.min(pasted.length, otpBoxes.length) - 1;
      if (otpBoxes[last]) otpBoxes[last].focus();
      if (pasted.length === 6) verifyOtp();
    });
  });

  // ══════════════════════════════════════════════════════════════════════
  //  OTP COUNTDOWN TIMER
  // ══════════════════════════════════════════════════════════════════════

  function startOtpTimer(seconds = 300) {
    clearInterval(otpTimer);
    otpSeconds = seconds;
    updateTimerDisplay();

    if (otpCountdown) otpCountdown.style.display = "";
    if (otpResendBtn) otpResendBtn.style.display = "none";

    otpTimer = setInterval(() => {
      otpSeconds--;
      if (otpSeconds <= 0) {
        clearInterval(otpTimer);
        if (otpTimeEl) {
          otpTimeEl.textContent = "00:00";
          otpTimeEl.classList.add("expired");
        }
        if (otpCountdown) otpCountdown.style.display = "none";
        if (otpResendBtn) otpResendBtn.style.display = "";
      } else {
        updateTimerDisplay();
      }
    }, 1000);
  }

  function updateTimerDisplay() {
    if (!otpTimeEl) return;
    const m = String(Math.floor(otpSeconds / 60)).padStart(2, "0");
    const s = String(otpSeconds % 60).padStart(2, "0");
    otpTimeEl.textContent = `${m}:${s}`;
    otpTimeEl.classList.remove("expired");
  }

  // ══════════════════════════════════════════════════════════════════════
  //  STEP 2: VERIFY OTP → CREATE ACCOUNT
  // ══════════════════════════════════════════════════════════════════════

  async function verifyOtp() {
    const otp = getOtpValue();
    if (otp.length < 6) return;

    hideAlert(alert2);
    setLoading(otpVerifyBtn, true);

    try {
      const { ok, data } = await apiPost("/auth/register-verify-otp", {
        email: formData.email,
        otp: otp,
        // Truyền toàn bộ formData để server tạo account sau khi verify
        first_name: formData.first_name,
        last_name: formData.last_name,
        password: formData.password,
      });

      if (ok && data.success) {
        clearInterval(otpTimer);
        goToStep(3);
        // Start success progress bar
        setTimeout(() => {
          const bar = document.getElementById("vp-success-bar");
          if (bar) bar.style.width = "100%";
        }, 100);
        // Auto-redirect sau 3 giây
        setTimeout(() => {
          window.location.href = LOGIN_URL;
        }, 3200);
      } else {
        const msg = data?.message || "Mã OTP không đúng.";
        showAlert(alert2, msg, "error");
        // Shake + clear OTP boxes
        otpBoxes.forEach((b) => {
          b.classList.add("is-error");
          setTimeout(() => b.classList.remove("is-error"), 400);
        });
        clearOtpBoxes();
        focusFirstOtpBox();
      }
    } catch (err) {
      console.error("[VP OTP Verify]", err);
      showAlert(alert2, "Không thể kết nối máy chủ.", "error");
    } finally {
      setLoading(otpVerifyBtn, false);
    }
  }

  if (otpVerifyBtn) {
    otpVerifyBtn.addEventListener("click", verifyOtp);
  }

  // ── Resend OTP ────────────────────────────────────────────────────────
  if (otpResendBtn) {
    otpResendBtn.addEventListener("click", async function () {
      hideAlert(alert2);
      this.disabled = true;
      this.textContent = "Đang gửi…";

      try {
        const { ok, data } = await apiPost("/auth/register-send-otp", {
          email: formData.email,
          recaptcha_token: "resend", // bypass recaptcha khi resend
        });

        if (ok && data.success) {
          clearOtpBoxes();
          focusFirstOtpBox();
          startOtpTimer();
          showAlert(
            alert2,
            `Mã mới đã được gửi đến ${formData.email}`,
            "success",
          );
        } else {
          showAlert(alert2, data?.message || "Không thể gửi lại mã.", "error");
          this.style.display = "";
        }
      } catch {
        showAlert(alert2, "Lỗi kết nối. Vui lòng thử lại.", "error");
        this.style.display = "";
      } finally {
        this.disabled = false;
        this.textContent = "Gửi lại mã";
      }
    });
  }

  // ── Back to form ──────────────────────────────────────────────────────
  if (backToForm) {
    backToForm.addEventListener("click", function () {
      clearInterval(otpTimer);
      clearOtpBoxes();
      hideAlert(alert2);
      goToStep(1);
      if (typeof grecaptcha !== "undefined") grecaptcha.reset();
    });
  }

  // ── Init ──────────────────────────────────────────────────────────────
  goToStep(1);
})();
