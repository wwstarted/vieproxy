/**
 * VieProxy — js/pages/changepass.js
 *
 * Change password flow:
 *  1. User fills: current password, new password, confirm.
 *  2. Click "Đổi mật khẩu" → validate → POST /otp/send-change-password.
 *  3. OTP modal → 6-box → POST /otp/verify-change-password.
 *  4. After OTP verified → POST /user/change-password.
 *  5. On success → force logout (token invalidated by password reset).
 */

window.initChangePasswordPage = function () {
  "use strict";

  // ── Config ────────────────────────────────────────────────────────────────
  var API =
    (window.wpAccountData && window.wpAccountData.apiBase) ||
    window.location.origin + "/wp-json/vieproxy/v1";
  var LOGIN =
    (window.wpAccountData && window.wpAccountData.loginUrl) ||
    window.location.origin + "/dang-nhap";
  var notify = window.accountUtils
    ? window.accountUtils.showNotification
    : function () {};
  var getToken = window.accountUtils
    ? window.accountUtils.getToken
    : function () {
        return window.VpAuth && window.VpAuth.getToken
          ? window.VpAuth.getToken()
          : localStorage.getItem("vp_jwt") || "";
      };

  // ── DOM refs ──────────────────────────────────────────────────────────────
  var currentPassInput = document.getElementById("cpCurrentPass");
  var newPassInput = document.getElementById("cpNewPass");
  var confirmPassInput = document.getElementById("cpConfirmPass");
  var confirmError = document.getElementById("cpConfirmError");
  var saveBtn = document.getElementById("cpSaveBtn");

  // Strength
  var strengthBar = document.getElementById("cpStrength");
  var strengthFill = document.getElementById("cpStrengthFill");
  var strengthLabel = document.getElementById("cpStrengthLabel");

  // Rules
  var ruleLength = document.getElementById("ruleLength");
  var ruleLower = document.getElementById("ruleLower");
  var ruleUpper = document.getElementById("ruleUpper");
  var ruleNumber = document.getElementById("ruleNumber");

  // OTP modal
  var otpModal = document.getElementById("cpOtpModal");
  var otpOverlay = document.getElementById("cpOtpOverlay");
  var otpClose = document.getElementById("cpOtpClose");
  var otpCancel = document.getElementById("cpOtpCancel");
  var otpVerifyBtn = document.getElementById("cpOtpVerify");
  var otpResend = document.getElementById("cpOtpResend");
  var otpCountdown = document.getElementById("cpOtpCountdown");
  var otpBoxes = document.querySelectorAll("#cpOtpBoxes .otp-box");

  // ── State ─────────────────────────────────────────────────────────────────
  var pendingCurrentPass = "";
  var pendingNewPass = "";
  var countdownTimer = null;

  // ── Auth fetch ────────────────────────────────────────────────────────────
  function authFetch(url, options) {
    options = options || {};
    options.headers = Object.assign({}, options.headers || {}, {
      Authorization: "Bearer " + getToken(),
      "Content-Type": "application/json",
    });
    return fetch(url, options);
  }

  // ── Password toggle ───────────────────────────────────────────────────────
  document.querySelectorAll(".pw-toggle").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var targetId = this.dataset.target;
      var input = document.getElementById(targetId);
      if (!input) return;
      var isText = input.type === "text";
      input.type = isText ? "password" : "text";
      var icon = this.querySelector("i");
      if (icon) {
        icon.className = isText
          ? "fa-regular fa-eye"
          : "fa-regular fa-eye-slash";
      }
    });
  });

  // ── Password strength ─────────────────────────────────────────────────────
  function checkStrength(val) {
    var rules = {
      length: val.length >= 8,
      lower: /[a-z]/.test(val),
      upper: /[A-Z]/.test(val),
      number: /[0-9]/.test(val),
    };

    // Update rule items
    setRule(ruleLength, rules.length);
    setRule(ruleLower, rules.lower);
    setRule(ruleUpper, rules.upper);
    setRule(ruleNumber, rules.number);

    var score = Object.values(rules).filter(Boolean).length;

    if (!val) {
      if (strengthBar) strengthBar.style.display = "none";
      return;
    }
    if (strengthBar) strengthBar.style.display = "flex";

    var labels = ["", "Yếu", "Trung bình", "Tốt", "Mạnh"];
    if (strengthFill) {
      strengthFill.setAttribute("data-level", score);
    }
    if (strengthLabel) {
      strengthLabel.textContent = labels[score] || "";
      strengthLabel.setAttribute("data-level", score);
    }
  }

  function setRule(el, valid) {
    if (!el) return;
    el.classList.toggle("is-valid", valid);
    var xmark = el.querySelector(".fa-circle-xmark");
    var check = el.querySelector(".fa-circle-check");
    if (!check) {
      // Inject check icon if not present
      var i = document.createElement("i");
      i.className = "fa-solid fa-circle-check";
      el.insertBefore(i, el.firstChild);
      check = i;
    }
    if (xmark) xmark.style.display = valid ? "none" : "";
    check.style.display = valid ? "" : "none";
  }

  if (newPassInput) {
    newPassInput.addEventListener("input", function () {
      checkStrength(this.value);
      checkConfirmMatch();
    });
  }

  // ── Confirm match ─────────────────────────────────────────────────────────
  function checkConfirmMatch() {
    if (!confirmPassInput || !confirmError) return;
    var match =
      confirmPassInput.value === (newPassInput ? newPassInput.value : "");
    confirmError.style.display =
      confirmPassInput.value && !match ? "block" : "none";
    confirmPassInput.classList.toggle(
      "is-error",
      !!(confirmPassInput.value && !match),
    );
  }

  if (confirmPassInput) {
    confirmPassInput.addEventListener("input", checkConfirmMatch);
  }

  // ── Validate & kick off OTP ───────────────────────────────────────────────
  function validateAndSendOtp() {
    var current = currentPassInput ? currentPassInput.value.trim() : "";
    var newPass = newPassInput ? newPassInput.value : "";
    var confirm = confirmPassInput ? confirmPassInput.value : "";

    var err = "";
    if (!current) err = "Vui lòng nhập mật khẩu hiện tại.";
    else if (newPass.length < 8) err = "Mật khẩu mới phải có ít nhất 8 ký tự.";
    else if (!/[a-z]/.test(newPass))
      err = "Mật khẩu cần có ít nhất 1 chữ thường.";
    else if (!/[A-Z]/.test(newPass)) err = "Mật khẩu cần có ít nhất 1 chữ hoa.";
    else if (!/[0-9]/.test(newPass)) err = "Mật khẩu cần có ít nhất 1 chữ số.";
    else if (newPass === current)
      err = "Mật khẩu mới không được trùng mật khẩu cũ.";
    else if (newPass !== confirm) err = "Mật khẩu xác nhận không khớp.";

    if (err) {
      notify(err, "error");
      return;
    }

    pendingCurrentPass = current;
    pendingNewPass = newPass;

    // Send OTP to server
    if (saveBtn) {
      saveBtn.disabled = true;
      saveBtn.innerHTML =
        '<i class="fa-solid fa-spinner fa-spin"></i> Đang gửi OTP...';
    }

    authFetch(API + "/otp/send-change-password", {
      method: "POST",
      body: JSON.stringify({}),
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (data) {
        if (data.success) {
          openOtpModal();
        } else {
          throw new Error(data.message || "Không thể gửi OTP");
        }
      })
      .catch(function (err) {
        notify(err.message, "error");
      })
      .finally(function () {
        if (saveBtn) {
          saveBtn.disabled = false;
          saveBtn.innerHTML = '<i class="fa-solid fa-lock"></i> Đổi mật khẩu';
        }
      });
  }

  if (saveBtn) {
    saveBtn.addEventListener("click", validateAndSendOtp);
  }

  // ── OTP Modal ─────────────────────────────────────────────────────────────
  function openOtpModal() {
    if (!otpModal) return;
    clearOtpBoxes();
    otpModal.classList.add("is-open");
    startCountdown(300);
    if (otpBoxes[0])
      setTimeout(function () {
        otpBoxes[0].focus();
      }, 60);
  }

  function closeOtpModal() {
    if (!otpModal) return;
    otpModal.classList.remove("is-open");
    clearCountdown();
    clearOtpBoxes();
  }

  if (otpClose) otpClose.addEventListener("click", closeOtpModal);
  if (otpCancel) otpCancel.addEventListener("click", closeOtpModal);
  if (otpOverlay) otpOverlay.addEventListener("click", closeOtpModal);

  // ── Countdown ─────────────────────────────────────────────────────────────
  function startCountdown(sec) {
    clearCountdown();
    if (otpResend) otpResend.classList.add("is-disabled");
    countdownTimer = setInterval(function () {
      sec -= 1;
      var m = Math.floor(sec / 60);
      var s = sec % 60;
      if (otpCountdown) {
        otpCountdown.textContent =
          "(" + m + ":" + (s < 10 ? "0" : "") + s + ")";
      }
      if (sec <= 0) {
        clearCountdown();
        if (otpResend) otpResend.classList.remove("is-disabled");
        if (otpCountdown) otpCountdown.textContent = "";
      }
    }, 1000);
  }

  function clearCountdown() {
    if (countdownTimer) {
      clearInterval(countdownTimer);
      countdownTimer = null;
    }
  }

  if (otpResend) {
    otpResend.addEventListener("click", function (e) {
      e.preventDefault();
      if (this.classList.contains("is-disabled")) return;
      authFetch(API + "/otp/send-change-password", {
        method: "POST",
        body: JSON.stringify({}),
      })
        .then(function (r) {
          return r.json();
        })
        .then(function (d) {
          if (d.success) {
            notify("Đã gửi lại mã OTP.", "info");
            startCountdown(300);
            clearOtpBoxes();
          } else throw new Error(d.message);
        })
        .catch(function (err) {
          notify(err.message, "error");
        });
    });
  }

  // ── OTP 6-box logic ───────────────────────────────────────────────────────
  function clearOtpBoxes() {
    otpBoxes.forEach(function (b) {
      b.value = "";
      b.classList.remove("is-filled", "is-error");
    });
  }

  function getOtpValue() {
    return Array.from(otpBoxes)
      .map(function (b) {
        return b.value;
      })
      .join("");
  }

  otpBoxes.forEach(function (box, i) {
    box.addEventListener("input", function () {
      var val = this.value.replace(/\D/g, "").slice(-1);
      this.value = val;
      this.classList.toggle("is-filled", val !== "");
      if (val && i < otpBoxes.length - 1) otpBoxes[i + 1].focus();
      if (getOtpValue().length === 6) setTimeout(verifyAndChangePass, 80);
    });

    box.addEventListener("keydown", function (e) {
      if (e.key === "Backspace" && !this.value && i > 0) {
        otpBoxes[i - 1].focus();
        otpBoxes[i - 1].value = "";
        otpBoxes[i - 1].classList.remove("is-filled");
      }
    });

    box.addEventListener("paste", function (e) {
      e.preventDefault();
      var paste = (e.clipboardData || window.clipboardData)
        .getData("text")
        .replace(/\D/g, "")
        .slice(0, 6);
      paste.split("").forEach(function (ch, j) {
        if (otpBoxes[j]) {
          otpBoxes[j].value = ch;
          otpBoxes[j].classList.add("is-filled");
        }
      });
      otpBoxes[Math.min(paste.length, otpBoxes.length - 1)].focus();
    });
  });

  // ── Verify OTP → Change Password ──────────────────────────────────────────
  function verifyAndChangePass() {
    var otp = getOtpValue();
    if (otp.length < 6) {
      otpBoxes.forEach(function (b) {
        b.classList.add("is-error");
      });
      return;
    }

    if (otpVerifyBtn) {
      otpVerifyBtn.disabled = true;
      otpVerifyBtn.textContent = "Đang xác thực...";
    }

    // Step 1: verify OTP
    authFetch(API + "/otp/verify-change-password", {
      method: "POST",
      body: JSON.stringify({ otp: otp }),
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (data) {
        if (!data.success) throw new Error(data.message || "Mã OTP không đúng");

        // Step 2: actually change password
        return authFetch(API + "/user/change-password", {
          method: "POST",
          body: JSON.stringify({
            current_password: pendingCurrentPass,
            new_password: pendingNewPass,
          }),
        }).then(function (r) {
          return r.json();
        });
      })
      .then(function (data) {
        if (!data.success)
          throw new Error(data.message || "Đổi mật khẩu thất bại");

        closeOtpModal();
        notify("Đổi mật khẩu thành công! Vui lòng đăng nhập lại.", "success");

        // Clear form
        if (currentPassInput) currentPassInput.value = "";
        if (newPassInput) newPassInput.value = "";
        if (confirmPassInput) confirmPassInput.value = "";
        if (strengthBar) strengthBar.style.display = "none";

        // Force logout after 2s (server invalidated password)
        setTimeout(function () {
          if (window.vpHeader && window.vpHeader.logout) {
            window.vpHeader.logout();
          } else {
            localStorage.removeItem("vp_jwt");
            window.location.href = LOGIN;
          }
        }, 2000);
      })
      .catch(function (err) {
        otpBoxes.forEach(function (b) {
          b.classList.add("is-error");
        });
        notify(err.message, "error");
      })
      .finally(function () {
        if (otpVerifyBtn) {
          otpVerifyBtn.disabled = false;
          otpVerifyBtn.innerHTML = '<i class="fa-solid fa-check"></i> Xác nhận';
        }
      });
  }

  if (otpVerifyBtn) {
    otpVerifyBtn.addEventListener("click", verifyAndChangePass);
  }
};
