/**
 * VieProxy — forgot-password.js
 * Flow: Email + reCAPTCHA → OTP 6 ô → Mật khẩu mới → Thành công
 */

(function () {
  "use strict";

  const API_BASE = window.vpFpData?.apiBase || "/wp-json/vieproxy/v1";
  const LOGIN_URL = window.vpFpData?.loginUrl || "/dang-nhap";

  // ── Panels ───────────────────────────────────────────────────────────
  const panels = {
    1: document.getElementById("vp-fp-step-1"),
    2: document.getElementById("vp-fp-step-2"),
    3: document.getElementById("vp-fp-step-3"),
  };
  const visSteps = {
    1: document.getElementById("vis-fp-step-1"),
    2: document.getElementById("vis-fp-step-2"),
    3: document.getElementById("vis-fp-step-3"),
  };

  // ── Step 1 elements ──────────────────────────────────────────────────
  const form1 = document.getElementById("vp-fp-form-1");
  const sendBtn = document.getElementById("vp-fp-send-btn");
  const alert1 = document.getElementById("vp-fp-alert-1");
  const emailEl = document.getElementById("vp-fp-email");

  // ── Step 2 elements ──────────────────────────────────────────────────
  const otpBoxes = document.querySelectorAll(".vp-fp-otp-box");
  const resetBtn = document.getElementById("vp-fp-reset-btn");
  const resendBtn = document.getElementById("vp-fp-resend");
  const timeEl = document.getElementById("vp-fp-time");
  const countdownWrap = document.getElementById("vp-fp-countdown-wrap");
  const emailDisplay = document.getElementById("vp-fp-email-display");
  const backBtn = document.getElementById("vp-fp-back");
  const alert2 = document.getElementById("vp-fp-alert-2");
  const form2 = document.getElementById("vp-fp-form-2");

  // ── State ─────────────────────────────────────────────────────────────
  let savedEmail = "";
  let otpTimer = null;
  let otpSeconds = 300;

  // ══════════════════════════════════════════════════════════════════════
  //  UTILS
  // ══════════════════════════════════════════════════════════════════════

  function showAlert(el, msg, type = "error") {
    el.textContent = msg;
    el.className = `vp-fp-alert vp-fp-alert--${type}`;
    el.style.display = "flex";
  }
  function hideAlert(el) {
    el.style.display = "none";
  }

  function setLoading(btn, loading) {
    const text = btn.querySelector(".vp-fp-btn__text");
    const spinner = btn.querySelector(".vp-fp-btn__spinner");
    btn.disabled = loading;
    if (text) text.style.display = loading ? "none" : "";
    if (spinner) spinner.style.display = loading ? "flex" : "none";
  }

  function clearFieldErrors() {
    document
      .querySelectorAll(".vp-fp-err")
      .forEach((el) => (el.textContent = ""));
    document
      .querySelectorAll(".vp-fp-input")
      .forEach((el) => el.classList.remove("is-error"));
  }

  function setFieldError(errId, inputId, msg) {
    const errEl = document.getElementById(errId);
    const inEl = document.getElementById(inputId);
    if (errEl) errEl.textContent = msg;
    if (inEl) inEl.classList.add("is-error");
  }

  async function apiPost(endpoint, body) {
    const res = await fetch(`${API_BASE}${endpoint}`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify(body),
    });
    const data = await res.json();
    return { ok: res.ok, data };
  }

  function goToStep(step) {
    Object.values(panels).forEach((el, i) => {
      if (el) el.style.display = i + 1 === step ? "" : "none";
    });
    Object.entries(visSteps).forEach(([k, el]) => {
      if (!el) return;
      const n = parseInt(k);
      el.classList.remove("vp-fp-step--active", "vp-fp-step--done");
      if (n < step) el.classList.add("vp-fp-step--done");
      else if (n === step) el.classList.add("vp-fp-step--active");
    });
  }

  // ══════════════════════════════════════════════════════════════════════
  //  TOGGLE PASSWORD VISIBILITY
  // ══════════════════════════════════════════════════════════════════════

  document.querySelectorAll(".vp-fp-toggle-pass").forEach((btn) => {
    btn.addEventListener("click", function () {
      const input = document.getElementById(this.dataset.target);
      if (!input) return;
      const isPass = input.type === "password";
      input.type = isPass ? "text" : "password";
      const eyeOn = this.querySelector(".vp-fp-eye--on");
      const eyeOff = this.querySelector(".vp-fp-eye--off");
      if (eyeOn) eyeOn.style.display = isPass ? "none" : "";
      if (eyeOff) eyeOff.style.display = isPass ? "" : "none";
    });
  });

  // ══════════════════════════════════════════════════════════════════════
  //  PASSWORD STRENGTH
  // ══════════════════════════════════════════════════════════════════════

  const passInput = document.getElementById("vp-fp-new-pass");
  const strengthWrap = document.getElementById("vp-fp-strength");
  const strengthFill = document.getElementById("vp-fp-strength-fill");
  const strengthText = document.getElementById("vp-fp-strength-text");

  function checkStrength(pw) {
    if (!strengthWrap) return;
    if (!pw) {
      strengthWrap.style.display = "none";
      return;
    }
    strengthWrap.style.display = "block";

    let score = 0;
    if (pw.length >= 8) score++;
    if (pw.length >= 12) score++;
    if (/[a-z]/.test(pw)) score++;
    if (/[A-Z]/.test(pw)) score++;
    if (/\d/.test(pw)) score++;
    if (/[^a-zA-Z0-9]/.test(pw)) score++;

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

  if (passInput)
    passInput.addEventListener("input", (e) => checkStrength(e.target.value));

  // ══════════════════════════════════════════════════════════════════════
  //  OTP TIMER
  // ══════════════════════════════════════════════════════════════════════

  function startTimer(seconds = 300) {
    clearInterval(otpTimer);
    otpSeconds = seconds;

    if (countdownWrap) countdownWrap.style.display = "";
    if (resendBtn) resendBtn.style.display = "none";
    updateTimer();

    otpTimer = setInterval(() => {
      otpSeconds--;
      if (otpSeconds <= 0) {
        clearInterval(otpTimer);
        if (timeEl) {
          timeEl.textContent = "00:00";
          timeEl.classList.add("expired");
        }
        if (countdownWrap) countdownWrap.style.display = "none";
        if (resendBtn) resendBtn.style.display = "";
      } else {
        updateTimer();
      }
    }, 1000);
  }

  function updateTimer() {
    if (!timeEl) return;
    const m = String(Math.floor(otpSeconds / 60)).padStart(2, "0");
    const s = String(otpSeconds % 60).padStart(2, "0");
    timeEl.textContent = `${m}:${s}`;
    timeEl.classList.remove("expired");
  }

  // ══════════════════════════════════════════════════════════════════════
  //  OTP BOX INTERACTIONS
  // ══════════════════════════════════════════════════════════════════════

  function getOtp() {
    return Array.from(otpBoxes)
      .map((b) => b.value)
      .join("");
  }

  function clearOtp() {
    otpBoxes.forEach((b) => {
      b.value = "";
      b.classList.remove("is-filled", "is-error");
    });
  }

  function focusFirst() {
    if (otpBoxes[0]) otpBoxes[0].focus();
  }

  otpBoxes.forEach((box, idx) => {
    box.addEventListener("input", function () {
      this.value = this.value.replace(/\D/g, "").slice(-1);
      if (this.value) {
        this.classList.add("is-filled");
        if (idx < otpBoxes.length - 1) otpBoxes[idx + 1].focus();
      } else {
        this.classList.remove("is-filled");
      }
    });

    box.addEventListener("keydown", function (e) {
      if (e.key === "Backspace" && !this.value && idx > 0) {
        otpBoxes[idx - 1].value = "";
        otpBoxes[idx - 1].classList.remove("is-filled");
        otpBoxes[idx - 1].focus();
      }
      if (e.key === "ArrowLeft" && idx > 0) otpBoxes[idx - 1].focus();
      if (e.key === "ArrowRight" && idx < otpBoxes.length - 1)
        otpBoxes[idx + 1].focus();
    });

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
      const last = Math.min(pasted.length, otpBoxes.length) - 1;
      if (otpBoxes[last]) otpBoxes[last].focus();
    });
  });

  // ══════════════════════════════════════════════════════════════════════
  //  STEP 1: GỬI OTP
  // ══════════════════════════════════════════════════════════════════════

  if (form1) {
    form1.addEventListener("submit", async function (e) {
      e.preventDefault();
      hideAlert(alert1);
      clearFieldErrors();

      const email = emailEl ? emailEl.value.trim() : "";
      const captcha =
        typeof grecaptcha !== "undefined" ? grecaptcha.getResponse() : "";

      if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        setFieldError("vp-fp-err-email", "vp-fp-email", "Email không hợp lệ.");
        return;
      }
      if (!captcha) {
        document.getElementById("vp-fp-err-captcha").textContent =
          "Vui lòng xác minh reCAPTCHA.";
        return;
      }

      setLoading(sendBtn, true);

      try {
        const { ok, data } = await apiPost("/otp/send-forgot-password", {
          email: email,
          recaptcha_token: captcha,
        });

        if (ok && data.success) {
          savedEmail = email;
          if (emailDisplay) emailDisplay.textContent = email;
          goToStep(2);
          startTimer();
          focusFirst();
        } else {
          showAlert(
            alert1,
            data?.message || "Không thể gửi OTP. Vui lòng thử lại.",
            "error",
          );
          if (typeof grecaptcha !== "undefined") grecaptcha.reset();
        }
      } catch {
        showAlert(
          alert1,
          "Không thể kết nối máy chủ. Vui lòng thử lại.",
          "error",
        );
        if (typeof grecaptcha !== "undefined") grecaptcha.reset();
      } finally {
        setLoading(sendBtn, false);
      }
    });
  }

  // ══════════════════════════════════════════════════════════════════════
  //  STEP 2: VERIFY OTP + ĐẶT LẠI MẬT KHẨU
  // ══════════════════════════════════════════════════════════════════════

  if (form2) {
    form2.addEventListener("submit", async function (e) {
      e.preventDefault();
      hideAlert(alert2);
      clearFieldErrors();

      const otp = getOtp();
      const newPass = document.getElementById("vp-fp-new-pass")?.value || "";
      const confirmPass =
        document.getElementById("vp-fp-confirm-pass")?.value || "";

      // Validate OTP
      if (otp.length < 6) {
        showAlert(alert2, "Vui lòng nhập đủ 6 chữ số OTP.", "error");
        focusFirst();
        return;
      }

      // Validate mật khẩu
      if (newPass.length < 8) {
        setFieldError(
          "vp-fp-err-new-pass",
          "vp-fp-new-pass",
          "Mật khẩu phải có ít nhất 8 ký tự.",
        );
        return;
      }
      if (
        !/[a-z]/.test(newPass) ||
        !/[A-Z]/.test(newPass) ||
        !/\d/.test(newPass)
      ) {
        setFieldError(
          "vp-fp-err-new-pass",
          "vp-fp-new-pass",
          "Cần có chữ hoa, chữ thường và số.",
        );
        return;
      }
      if (newPass !== confirmPass) {
        setFieldError(
          "vp-fp-err-confirm-pass",
          "vp-fp-confirm-pass",
          "Mật khẩu xác nhận không khớp.",
        );
        return;
      }

      setLoading(resetBtn, true);

      try {
        const { ok, data } = await apiPost("/otp/verify-forgot-password", {
          email: savedEmail,
          otp: otp,
          new_password: newPass,
          confirm_password: confirmPass,
        });

        if (ok && data.success) {
          clearInterval(otpTimer);
          goToStep(3);
          setTimeout(() => {
            const bar = document.getElementById("vp-fp-progress-bar");
            if (bar) bar.style.width = "100%";
          }, 100);
          setTimeout(() => {
            window.location.href = LOGIN_URL;
          }, 3200);
        } else {
          const msg = data?.message || "OTP không đúng hoặc đã hết hạn.";
          showAlert(alert2, msg, "error");
          otpBoxes.forEach((b) => {
            b.classList.add("is-error");
            setTimeout(() => b.classList.remove("is-error"), 400);
          });
          clearOtp();
          focusFirst();
        }
      } catch {
        showAlert(alert2, "Không thể kết nối máy chủ.", "error");
      } finally {
        setLoading(resetBtn, false);
      }
    });
  }

  // ── Resend OTP ────────────────────────────────────────────────────────
  if (resendBtn) {
    resendBtn.addEventListener("click", async function () {
      hideAlert(alert2);
      this.disabled = true;
      this.textContent = "Đang gửi…";

      try {
        const { ok, data } = await apiPost("/otp/send-forgot-password", {
          email: savedEmail,
          recaptcha_token: "resend",
        });
        if (ok && data.success) {
          clearOtp();
          focusFirst();
          startTimer();
          showAlert(alert2, `Mã mới đã được gửi đến ${savedEmail}`, "success");
        } else {
          showAlert(alert2, data?.message || "Không thể gửi lại.", "error");
          this.style.display = "";
        }
      } catch {
        showAlert(alert2, "Lỗi kết nối.", "error");
        this.style.display = "";
      } finally {
        this.disabled = false;
        this.textContent = "Gửi lại mã";
      }
    });
  }

  // ── Back button ───────────────────────────────────────────────────────
  if (backBtn) {
    backBtn.addEventListener("click", function () {
      clearInterval(otpTimer);
      clearOtp();
      hideAlert(alert2);
      goToStep(1);
      if (typeof grecaptcha !== "undefined") grecaptcha.reset();
    });
  }

  // ── Init ──────────────────────────────────────────────────────────────
  goToStep(1);
})();
