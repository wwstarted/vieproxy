/**
 * VieProxy — js/pages/profile.js
 *
 * Profile page: load data, save name/phone, change email with OTP.
 *
 * API (vieproxy/v1):
 *   GET  /user/profile                → { display_name, email, phone }
 *   POST /user/profile                → update display_name, email, phone
 *   POST /otp/send-email              → send OTP to new email
 *   POST /otp/verify-email            → verify OTP (authorize email change)
 */

window.initProfilePage = function () {
  "use strict";

  // ── Config ────────────────────────────────────────────────────────────────
  var API =
    (window.wpAccountData && window.wpAccountData.apiBase) ||
    window.location.origin + "/wp-json/vieproxy/v1";
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
  var fullnameInput = document.getElementById("profileFullname");
  var emailInput = document.getElementById("profileEmail");
  var phoneInput = document.getElementById("profilePhone");
  var editEmailBtn = document.getElementById("profileEditEmailBtn");
  var emailHelper = document.getElementById("profileEmailHelper");
  var saveBtn = document.getElementById("profileSaveBtn");

  // OTP modal
  var otpModal = document.getElementById("profileOtpModal");
  var otpOverlay = document.getElementById("profileOtpOverlay");
  var otpClose = document.getElementById("profileOtpClose");
  var otpCancel = document.getElementById("profileOtpCancel");
  var otpVerifyBtn = document.getElementById("profileOtpVerify");
  var otpResendLink = document.getElementById("profileOtpResend");
  var otpTarget = document.getElementById("profileOtpTarget");
  var otpCountdown = document.getElementById("profileOtpCountdown");
  var otpBoxes = document.querySelectorAll("#profileOtpBoxes .otp-box");

  // Phone prefix
  var phonePrefixBtn = document.getElementById("phonePrefixBtn");
  var phonePrefixFlag = document.getElementById("phonePrefixFlag");
  var phonePrefixCode = document.getElementById("phonePrefixCode");
  var phoneDropdown = document.getElementById("phoneDropdown");
  var phoneSearch = document.getElementById("phoneCountrySearch");
  var countryItems = document.querySelectorAll(
    "#phoneCountryList .phone-dropdown-item",
  );

  // ── State ─────────────────────────────────────────────────────────────────
  var currentEmail = "";
  var pendingNewEmail = "";
  var emailVerified = false;
  var countdownTimer = null;
  var selectedDial = "+84";

  // ── Auth fetch helper ─────────────────────────────────────────────────────
  function authFetch(url, options) {
    options = options || {};
    options.headers = Object.assign({}, options.headers || {}, {
      Authorization: "Bearer " + getToken(),
      "Content-Type": "application/json",
    });
    return fetch(url, options);
  }

  // ── Load profile data ─────────────────────────────────────────────────────
  function loadProfile() {
    if (saveBtn) {
      saveBtn.disabled = true;
    }

    authFetch(API + "/user/profile")
      .then(function (r) {
        if (!r.ok) throw new Error("HTTP " + r.status);
        return r.json();
      })
      .then(function (data) {
        if (fullnameInput) fullnameInput.value = data.display_name || "";
        if (emailInput) emailInput.value = data.email || "";
        if (phoneInput) phoneInput.value = data.phone || "";
        currentEmail = data.email || "";
        if (saveBtn) saveBtn.disabled = false;
      })
      .catch(function (err) {
        console.error("[profile.js] loadProfile error:", err);
        notify("Không thể tải dữ liệu hồ sơ.", "error");
        if (saveBtn) saveBtn.disabled = false;
      });
  }

  // ── Save profile ──────────────────────────────────────────────────────────
  function saveProfile() {
    var name = (fullnameInput && fullnameInput.value.trim()) || "";
    var email = (emailInput && emailInput.value.trim()) || "";
    var phone = (phoneInput && phoneInput.value.trim()) || "";

    if (!name) {
      notify("Vui lòng nhập họ và tên.", "error");
      if (fullnameInput) {
        fullnameInput.classList.add("is-error");
        fullnameInput.focus();
      }
      return;
    }

    // If email changed, must be OTP-verified first
    if (email !== currentEmail && !emailVerified) {
      notify("Vui lòng xác thực email mới trước khi lưu.", "error");
      return;
    }

    var payload = { display_name: name, phone: phone };
    if (emailVerified) payload.email = email;

    if (saveBtn) {
      saveBtn.disabled = true;
      saveBtn.textContent = "Đang lưu...";
    }

    authFetch(API + "/user/profile", {
      method: "POST",
      body: JSON.stringify(payload),
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (data) {
        if (data.success) {
          notify("Cập nhật hồ sơ thành công!", "success");
          currentEmail =
            data.data && data.data.email ? data.data.email : currentEmail;
          emailVerified = false;
          lockEmail();

          // Sync sidebar name
          if (window.vpHeader && data.data) {
            window.vpHeader.setLoggedIn({
              display_name: data.data.display_name,
              email: currentEmail,
            });
          }
        } else {
          throw new Error(data.message || "Cập nhật thất bại");
        }
      })
      .catch(function (err) {
        notify(err.message || "Lỗi cập nhật. Vui lòng thử lại.", "error");
      })
      .finally(function () {
        if (saveBtn) {
          saveBtn.disabled = false;
          saveBtn.innerHTML =
            '<i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi';
        }
      });
  }

  // ── Email edit flow ───────────────────────────────────────────────────────
  function lockEmail() {
    if (!emailInput) return;
    emailInput.disabled = true;
    emailInput.classList.add("is-disabled");
    if (emailHelper) {
      emailHelper.textContent =
        "Nhấn vào biểu tượng bút để thay đổi email. Yêu cầu xác thực OTP.";
      emailHelper.classList.remove("is-error");
    }
    pendingNewEmail = "";
  }

  function unlockEmail() {
    if (!emailInput) return;
    emailInput.disabled = false;
    emailInput.classList.remove("is-disabled");
    emailInput.focus();
    if (emailHelper) {
      emailHelper.textContent =
        "Nhập email mới, sau đó mã OTP sẽ được gửi để xác thực.";
    }
  }

  if (editEmailBtn) {
    editEmailBtn.addEventListener("click", function () {
      if (emailInput && emailInput.disabled) {
        unlockEmail();
      } else {
        // User clicked confirm edit → send OTP
        var newEmail = emailInput ? emailInput.value.trim() : "";
        if (!newEmail || newEmail === currentEmail) {
          if (emailHelper) {
            emailHelper.textContent = "Email không thay đổi.";
            emailHelper.classList.add("is-error");
          }
          return;
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(newEmail)) {
          if (emailHelper) {
            emailHelper.textContent = "Email không hợp lệ.";
            emailHelper.classList.add("is-error");
          }
          return;
        }
        pendingNewEmail = newEmail;
        sendEmailOtp(newEmail);
      }
    });
  }

  // ── Send OTP for email change ─────────────────────────────────────────────
  function sendEmailOtp(email) {
    authFetch(API + "/otp/send-email", {
      method: "POST",
      body: JSON.stringify({ email: email, purpose: "change_email" }),
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (data) {
        if (data.success) {
          openOtpModal(email);
        } else {
          throw new Error(data.message || "Không thể gửi OTP");
        }
      })
      .catch(function (err) {
        notify(err.message, "error");
      });
  }

  // ── OTP Modal ─────────────────────────────────────────────────────────────
  function openOtpModal(email) {
    if (!otpModal) return;
    if (otpTarget) otpTarget.textContent = email;
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
  function startCountdown(seconds) {
    clearCountdown();
    if (otpResendLink) {
      otpResendLink.classList.add("is-disabled");
    }

    countdownTimer = setInterval(function () {
      seconds -= 1;
      var m = Math.floor(seconds / 60);
      var s = seconds % 60;
      if (otpCountdown) {
        otpCountdown.textContent =
          "(" + m + ":" + (s < 10 ? "0" : "") + s + ")";
      }
      if (seconds <= 0) {
        clearCountdown();
        if (otpResendLink) {
          otpResendLink.classList.remove("is-disabled");
        }
        if (otpCountdown) {
          otpCountdown.textContent = "";
        }
      }
    }, 1000);
  }

  function clearCountdown() {
    if (countdownTimer) {
      clearInterval(countdownTimer);
      countdownTimer = null;
    }
  }

  if (otpResendLink) {
    otpResendLink.addEventListener("click", function (e) {
      e.preventDefault();
      if (this.classList.contains("is-disabled")) return;
      sendEmailOtp(pendingNewEmail);
      startCountdown(300);
      clearOtpBoxes();
    });
  }

  // ── OTP 6-box logic ───────────────────────────────────────────────────────
  function clearOtpBoxes() {
    otpBoxes.forEach(function (box) {
      box.value = "";
      box.classList.remove("is-filled", "is-error");
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
      if (val && i < otpBoxes.length - 1) {
        otpBoxes[i + 1].focus();
      }
      // Auto-submit when all filled
      if (getOtpValue().length === 6) {
        setTimeout(verifyEmailOtp, 80);
      }
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
      var next = Math.min(paste.length, otpBoxes.length - 1);
      otpBoxes[next].focus();
    });
  });

  // ── Verify OTP ────────────────────────────────────────────────────────────
  function verifyEmailOtp() {
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

    authFetch(API + "/otp/verify-email", {
      method: "POST",
      body: JSON.stringify({ otp: otp, email: pendingNewEmail }),
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (data) {
        if (data.success) {
          emailVerified = true;
          closeOtpModal();
          if (emailInput) {
            emailInput.value = pendingNewEmail;
            emailInput.disabled = true;
            emailInput.classList.add("is-disabled");
          }
          if (emailHelper) {
            emailHelper.textContent =
              '✓ Email mới đã xác thực. Nhấn "Lưu thay đổi" để áp dụng.';
            emailHelper.classList.remove("is-error");
          }
          notify("Xác thực email thành công!", "success");
        } else {
          throw new Error(data.message || "Mã OTP không đúng");
        }
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
    otpVerifyBtn.addEventListener("click", verifyEmailOtp);
  }

  // ── Phone prefix dropdown ─────────────────────────────────────────────────
  if (phonePrefixBtn && phoneDropdown) {
    phonePrefixBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      phoneDropdown.classList.toggle("is-open");
    });

    document.addEventListener("click", function () {
      phoneDropdown.classList.remove("is-open");
    });

    phoneDropdown.addEventListener("click", function (e) {
      e.stopPropagation();
    });

    countryItems.forEach(function (item) {
      item.addEventListener("click", function () {
        var code = this.dataset.code;
        var flag = this.dataset.flag;
        selectedDial = code;
        if (phonePrefixCode) phonePrefixCode.textContent = code;
        if (phonePrefixFlag) {
          phonePrefixFlag.src = "https://flagcdn.com/w40/" + flag + ".png";
          phonePrefixFlag.alt = flag.toUpperCase();
        }
        countryItems.forEach(function (i) {
          i.classList.remove("is-active");
        });
        item.classList.add("is-active");
        phoneDropdown.classList.remove("is-open");
      });
    });

    if (phoneSearch) {
      phoneSearch.addEventListener("input", function () {
        var q = this.value.toLowerCase();
        countryItems.forEach(function (item) {
          var name = item.querySelector(".country-name");
          var code = item.querySelector(".country-dial");
          var match =
            (name && name.textContent.toLowerCase().includes(q)) ||
            (code && code.textContent.includes(q));
          item.style.display = match ? "" : "none";
        });
      });
    }
  }

  // ── Save button ───────────────────────────────────────────────────────────
  if (saveBtn) {
    saveBtn.addEventListener("click", saveProfile);
  }

  // ── Boot ──────────────────────────────────────────────────────────────────
  loadProfile();
};
