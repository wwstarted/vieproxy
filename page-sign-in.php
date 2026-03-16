<?php
/**
 * Template Name: Login
 * VieProxy — Trang đăng nhập
 *
 * Redirect nếu đã đăng nhập (kiểm tra server-side cookie)
 */

// Nếu đã có cookie WP auth thì redirect luôn
if (is_user_logged_in()) {
    wp_redirect(home_url('/account'));
    exit;
}

get_header();
?>

<div class="vp-login-wrap">

    <!-- Cột trái: hình minh họa -->
    <div class="vp-login-visual" aria-hidden="true">
        <div class="vp-login-visual__inner">
            <div class="vp-login-visual__blob vp-login-visual__blob--1"></div>
            <div class="vp-login-visual__blob vp-login-visual__blob--2"></div>
            <div class="vp-login-visual__blob vp-login-visual__blob--3"></div>

            <div class="vp-login-visual__content">
                <div class="vp-login-logo">
                    <?php
                    if (has_custom_logo()) {
                        the_custom_logo();
                    } else {
                        echo '<span class="vp-login-logo__text">' . get_bloginfo('name') . '</span>';
                    }
                    ?>
                </div>
                <h2 class="vp-login-visual__title">Proxy cao tốc,<br>kết nối toàn cầu.</h2>
                <p class="vp-login-visual__sub">Quản lý toàn bộ gói proxy của bạn chỉ trong một tài khoản.</p>

                <ul class="vp-login-features">
                    <li><span class="vp-login-features__icon">⚡</span>Tốc độ cao, ổn định 24/7</li>
                    <li><span class="vp-login-features__icon">🌏</span>IP từ hơn 50 quốc gia</li>
                    <li><span class="vp-login-features__icon">🔒</span>Bảo mật tuyệt đối</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Cột phải: form đăng nhập -->
    <div class="vp-login-form-col">
        <div class="vp-login-card" id="vp-login-card">

            <!-- Logo mobile -->
            <div class="vp-login-card__logo-mobile">
                <?php bloginfo('name'); ?>
            </div>

            <h1 class="vp-login-card__title">Đăng nhập</h1>
            <p class="vp-login-card__sub">Chào mừng bạn đã quay trở lại!</p>

            <!-- Alert box -->
            <div class="vp-alert" id="vp-login-alert" role="alert" aria-live="polite" style="display:none;"></div>

            <!-- Form email/password -->
            <form id="vp-login-form" novalidate autocomplete="off">

                <div class="vp-field">
                    <label class="vp-field__label" for="vp-email">Email</label>
                    <div class="vp-field__wrap">
                        <span class="vp-field__icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <rect x="2" y="4" width="20" height="16" rx="2" />
                                <path d="m2 7 10 7 10-7" />
                            </svg>
                        </span>
                        <input type="email" id="vp-email" name="email" class="vp-field__input"
                            placeholder="you@example.com" autocomplete="email" required>
                    </div>
                    <span class="vp-field__error" id="vp-email-error"></span>
                </div>

                <div class="vp-field">
                    <label class="vp-field__label" for="vp-password">
                        Mật khẩu
                        <a href="<?php echo esc_url(home_url('/forgetpassword')); ?>" class="vp-field__label-link">Quên
                            mật khẩu?</a>
                    </label>
                    <div class="vp-field__wrap">
                        <span class="vp-field__icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </span>
                        <input type="password" id="vp-password" name="password"
                            class="vp-field__input vp-field__input--padded-r" placeholder="••••••••"
                            autocomplete="current-password" required>
                        <button type="button" class="vp-field__toggle-pass" id="vp-toggle-pass"
                            aria-label="Hiện/Ẩn mật khẩu">
                            <!-- eye-open -->
                            <svg class="vp-eye vp-eye--open" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            <!-- eye-closed -->
                            <svg class="vp-eye vp-eye--closed" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" style="display:none;">
                                <path
                                    d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                                <line x1="1" y1="1" x2="23" y2="23" />
                            </svg>
                        </button>
                    </div>
                    <span class="vp-field__error" id="vp-pass-error"></span>
                </div>

                <!-- reCAPTCHA v2 -->
                <div class="vp-recaptcha-wrap">
                    <div class="g-recaptcha" id="vp-recaptcha"
                        data-sitekey="<?php echo esc_attr(defined('RECAPTCHA_SITE_KEY') ? RECAPTCHA_SITE_KEY : 'YOUR_RECAPTCHA_SITE_KEY'); ?>"
                        data-theme="light" data-size="normal"></div>
                    <span class="vp-field__error" id="vp-captcha-error"></span>
                </div>

                <button type="submit" class="vp-btn vp-btn--primary" id="vp-login-btn">
                    <span class="vp-btn__text">Đăng nhập</span>
                    <span class="vp-btn__spinner" style="display:none;">
                        <svg class="vp-spinner-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                        </svg>
                    </span>
                </button>

            </form>

            <!-- Divider -->
            <div class="vp-divider"><span>hoặc</span></div>

            <!-- Google Login -->
            <form id="vp-google-form" method="post" style="display:none;">
                <input type="hidden" name="credential" id="vp-google-credential">
            </form>

            <button type="button" class="vp-btn vp-btn--google" id="vp-google-btn">
                <svg class="vp-google-icon" width="20" height="20" viewBox="0 0 24 24">
                    <path
                        d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                        fill="#4285F4" />
                    <path
                        d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                        fill="#34A853" />
                    <path
                        d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                        fill="#FBBC05" />
                    <path
                        d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                        fill="#EA4335" />
                </svg>
                <span>Đăng nhập với Google</span>
            </button>

            <!-- Hidden Google GSI render target -->
            <div id="vp-gsi-target" style="display:none;"></div>

            <p class="vp-login-card__footer">
                Chưa có tài khoản?
                <a href="<?php echo esc_url(home_url('/register')); ?>">Đăng ký ngay</a>
            </p>

        </div>
    </div>

</div>

<!-- Google GSI -->
<script src="https://accounts.google.com/gsi/client" async defer></script>
<!-- reCAPTCHA v2 -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<!-- Truyền data xuống JS -->
<script>
window.vpLoginData = {
    apiBase: '<?php echo esc_js(get_rest_url(null, 'vieproxy/v1')); ?>',
    accountUrl: '<?php echo esc_js(home_url('/account')); ?>',
    googleClientId: '<?php echo esc_js(GOOGLE_CLIENT_ID); ?>',
};
</script>

<?php get_footer(); ?>