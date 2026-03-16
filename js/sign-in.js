/**
 * VieProxy — sign-in.js
 * AJAX login (email/password) + Google Sign-In + reCAPTCHA v2
 *
 * Requires:  window.vpLoginData  (injected by page-login.php)
 */

;(function () {
    'use strict';

    // ── Config ─────────────────────────────────────────────────────────────
    const API_BASE     = window.vpLoginData?.apiBase     || '/wp-json/vieproxy/v1';
    const ACCOUNT_URL  = window.vpLoginData?.accountUrl  || '/account';
    const GG_CLIENT_ID = window.vpLoginData?.googleClientId || '';

    // ── Selectors ──────────────────────────────────────────────────────────
    const form         = document.getElementById('vp-login-form');
    const alertBox     = document.getElementById('vp-login-alert');
    const emailInput   = document.getElementById('vp-email');
    const passInput    = document.getElementById('vp-password');
    const togglePassBtn= document.getElementById('vp-toggle-pass');
    const loginBtn     = document.getElementById('vp-login-btn');
    const googleBtn    = document.getElementById('vp-google-btn');
    const gsiTarget    = document.getElementById('vp-gsi-target');

    const emailErr     = document.getElementById('vp-email-error');
    const passErr      = document.getElementById('vp-pass-error');
    const captchaErr   = document.getElementById('vp-captcha-error');

    // ══════════════════════════════════════════════════════════════════════
    //  UTILS
    // ══════════════════════════════════════════════════════════════════════

    function showAlert(msg, type = 'error') {
        alertBox.textContent = msg;
        alertBox.className   = `vp-alert vp-alert--${type}`;
        alertBox.style.display = 'flex';
        alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function hideAlert() {
        alertBox.style.display = 'none';
        alertBox.textContent   = '';
    }

    function setLoading(loading) {
        const text    = loginBtn.querySelector('.vp-btn__text');
        const spinner = loginBtn.querySelector('.vp-btn__spinner');
        loginBtn.disabled          = loading;
        googleBtn.disabled         = loading;
        text.style.display         = loading ? 'none'  : '';
        spinner.style.display      = loading ? 'flex'  : 'none';
    }

    function clearFieldErrors() {
        emailErr.textContent   = '';
        passErr.textContent    = '';
        captchaErr.textContent = '';
        emailInput.classList.remove('is-error');
        passInput.classList.remove('is-error');
    }

    function validateFields(email, password) {
        let valid = true;
        if (!email) {
            emailErr.textContent = 'Vui lòng nhập email.';
            emailInput.classList.add('is-error');
            valid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            emailErr.textContent = 'Email không đúng định dạng.';
            emailInput.classList.add('is-error');
            valid = false;
        }
        if (!password) {
            passErr.textContent = 'Vui lòng nhập mật khẩu.';
            passInput.classList.add('is-error');
            valid = false;
        }
        return valid;
    }

    /** Lưu JWT vào localStorage + cookie backup (không httpOnly) */
    function saveToken(token) {
        localStorage.setItem('vp_jwt', token);
    }

    /** Redirect sau login */
    function redirectAfterLogin() {
        const redirectUrl = sessionStorage.getItem('vp_redirect_after_login') || ACCOUNT_URL;
        sessionStorage.removeItem('vp_redirect_after_login');
        window.location.href = redirectUrl;
    }

    /** Gọi REST API JSON */
    async function apiPost(endpoint, body) {
        const res = await fetch(`${API_BASE}${endpoint}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify(body),
        });
        const data = await res.json();
        return { ok: res.ok, status: res.status, data };
    }

    // ══════════════════════════════════════════════════════════════════════
    //  TOGGLE PASSWORD VISIBILITY
    // ══════════════════════════════════════════════════════════════════════

    if (togglePassBtn) {
        togglePassBtn.addEventListener('click', function () {
            const isPass = passInput.type === 'password';
            passInput.type = isPass ? 'text' : 'password';

            const eyeOpen   = togglePassBtn.querySelector('.vp-eye--open');
            const eyeClosed = togglePassBtn.querySelector('.vp-eye--closed');
            eyeOpen.style.display   = isPass ? 'none' : '';
            eyeClosed.style.display = isPass ? ''     : 'none';
        });
    }

    // ══════════════════════════════════════════════════════════════════════
    //  EMAIL / PASSWORD LOGIN
    // ══════════════════════════════════════════════════════════════════════

    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            hideAlert();
            clearFieldErrors();

            const email    = emailInput.value.trim();
            const password = passInput.value;

            // Client-side validation
            if (!validateFields(email, password)) return;

            // reCAPTCHA check
            const captchaToken = grecaptcha?.getResponse?.() || '';
            if (!captchaToken) {
                captchaErr.textContent = 'Vui lòng xác minh reCAPTCHA.';
                return;
            }

            setLoading(true);

            try {
                const { ok, data } = await apiPost('/auth/login', {
                    email,
                    password,
                    recaptcha_token: captchaToken,
                });

                if (ok && data.success) {
                    saveToken(data.token);
                    showAlert('Đăng nhập thành công! Đang chuyển hướng…', 'success');
                    setTimeout(redirectAfterLogin, 700);
                } else {
                    const msg = data?.message || 'Email hoặc mật khẩu không đúng.';
                    showAlert(msg, 'error');
                    grecaptcha?.reset?.();
                }
            } catch (err) {
                console.error('[VieProxy Login]', err);
                showAlert('Không thể kết nối máy chủ. Vui lòng thử lại.', 'error');
                grecaptcha?.reset?.();
            } finally {
                setLoading(false);
            }
        });
    }

    // ══════════════════════════════════════════════════════════════════════
    //  GOOGLE SIGN-IN
    // ══════════════════════════════════════════════════════════════════════

    /**
     * Callback nhận từ GSI sau khi user chọn tài khoản Google
     */
    window.vpGoogleCallback = async function (googleResponse) {
        if (!googleResponse?.credential) {
            showAlert('Đăng nhập Google thất bại. Vui lòng thử lại.', 'error');
            return;
        }

        setLoading(true);
        hideAlert();

        try {
            const { ok, data } = await apiPost('/auth/google', {
                credential: googleResponse.credential,
            });

            if (ok && data.success) {
                saveToken(data.token);
                showAlert('Đăng nhập Google thành công! Đang chuyển hướng…', 'success');
                setTimeout(redirectAfterLogin, 700);
            } else {
                showAlert(data?.message || 'Đăng nhập Google thất bại.', 'error');
            }
        } catch (err) {
            console.error('[VieProxy Google Login]', err);
            showAlert('Không thể kết nối máy chủ. Vui lòng thử lại.', 'error');
        } finally {
            setLoading(false);
        }
    };

    /**
     * Khởi tạo GSI + render nút ẩn để popup có thể trigger
     */
    function initGoogleSignIn() {
        if (typeof google === 'undefined' || !google.accounts) return;

        google.accounts.id.initialize({
            client_id    : GG_CLIENT_ID,
            callback     : window.vpGoogleCallback,
            auto_select  : false,
            cancel_on_tap_outside: true,
            ux_mode      : 'popup',
        });

        // Render nút Google thật vào target ẩn
        google.accounts.id.renderButton(gsiTarget, {
            theme : 'outline',
            size  : 'large',
            width : 400,
        });

        // Tắt One Tap prompt
        google.accounts.id.cancel();
        google.accounts.id.disableAutoSelect();

        // Custom button click → click nút thật
        if (googleBtn) {
            googleBtn.addEventListener('click', function () {
                const realBtn = gsiTarget.querySelector('div[role="button"]');
                if (realBtn) {
                    realBtn.click();
                } else {
                    // Fallback: OAuth2 redirect
                    const nonce   = Math.random().toString(36).slice(2);
                    const authUrl = `https://accounts.google.com/o/oauth2/v2/auth`
                        + `?client_id=${encodeURIComponent(GG_CLIENT_ID)}`
                        + `&redirect_uri=${encodeURIComponent(window.location.origin + '/login')}`
                        + `&response_type=id_token`
                        + `&scope=openid email profile`
                        + `&nonce=${nonce}`;
                    window.location.href = authUrl;
                }
            });
        }
    }

    // GSI có thể load trước hoặc sau DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            // Đợi GSI script load xong
            window.addEventListener('load', initGoogleSignIn);
        });
    } else {
        window.addEventListener('load', initGoogleSignIn);
    }

    // ══════════════════════════════════════════════════════════════════════
    //  AUTH MODULE (global — dùng cho các trang khác)
    // ══════════════════════════════════════════════════════════════════════

    window.VpAuth = {
        /** Lấy JWT từ localStorage */
        getToken() {
            return localStorage.getItem('vp_jwt') || null;
        },

        /** Lưu JWT */
        setToken(token) {
            localStorage.setItem('vp_jwt', token);
        },

        /** Xóa JWT (logout client-side) */
        removeToken() {
            localStorage.removeItem('vp_jwt');
        },

        /** Đã đăng nhập chưa (kiểm tra localStorage) */
        isAuthenticated() {
            return !!this.getToken();
        },

        /**
         * Bảo vệ trang: gọi đầu trang, redirect nếu chưa login
         * @param {object} opts
         *   verifyWithServer  {boolean} — gọi API validate (default: false)
         *   message           {string}  — thông báo khi chưa login
         */
        async requireAuth(opts = {}) {
            const {
                verifyWithServer = false,
                message = 'Vui lòng đăng nhập để tiếp tục!',
            } = opts;

            if (!this.isAuthenticated()) {
                sessionStorage.setItem('vp_redirect_after_login', window.location.href);
                alert(message);
                window.location.href = '/login';
                return Promise.reject(new Error('Not authenticated'));
            }

            if (verifyWithServer) {
                const valid = await this._verifyWithServer();
                if (!valid) {
                    this.removeToken();
                    sessionStorage.setItem('vp_redirect_after_login', window.location.href);
                    alert('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại!');
                    window.location.href = '/login';
                    return Promise.reject(new Error('Token expired'));
                }
            }

            return Promise.resolve(true);
        },

        /** Gọi API có kèm Bearer token */
        async authFetch(url, options = {}) {
            const token = this.getToken();
            const headers = {
                'Content-Type': 'application/json',
                ...(options.headers || {}),
            };
            if (token) headers['Authorization'] = `Bearer ${token}`;
            return fetch(url, { ...options, headers, credentials: 'include' });
        },

        /** Logout: gọi API + xóa token */
        async logout(redirectUrl = '/') {
            try {
                await fetch(`${API_BASE}/auth/logout`, {
                    method: 'POST',
                    credentials: 'include',
                });
            } catch (_) {}

            this.removeToken();
            window.location.href = redirectUrl;
        },

        /** Internal: validate token với server */
        async _verifyWithServer() {
            try {
                const res = await this.authFetch(`${API_BASE}/user/profile`);
                return res.ok;
            } catch {
                return false;
            }
        },
    };

    // Backward compat alias
    window.AuthModule = window.VpAuth;

})();