<?php
/**
 * Template Name: Register
 * VieProxy — Trang đăng ký với xác thực OTP email
 */

if (is_user_logged_in()) {
    wp_redirect(home_url('/account'));
    exit;
}

get_header();
?>

<div class="vp-reg-wrap">

    <!-- ── Cột trái: Visual ─────────────────────────────────── -->
    <div class="vp-reg-visual" aria-hidden="true">
        <div class="vp-reg-visual__inner">
            <div class="vp-reg-visual__blob vp-reg-visual__blob--1"></div>
            <div class="vp-reg-visual__blob vp-reg-visual__blob--2"></div>
            <div class="vp-reg-visual__blob vp-reg-visual__blob--3"></div>

            <div class="vp-reg-visual__content">
                <div class="vp-reg-logo">
                    <?php if (has_custom_logo()):
                        the_custom_logo();
                    else: ?>
                    <span class="vp-reg-logo__text">
                        <?php bloginfo('name'); ?>
                    </span>
                    <?php endif; ?>
                </div>

                <h2 class="vp-reg-visual__title">Bắt đầu hành trình<br>kết nối của bạn.</h2>
                <p class="vp-reg-visual__sub">Tạo tài khoản miễn phí và trải nghiệm mạng proxy tốc độ cao ngay hôm nay.
                </p>

                <!-- Steps indicator -->
                <div class="vp-reg-steps">
                    <div class="vp-reg-step vp-reg-step--active" id="vis-step-1">
                        <div class="vp-reg-step__num">1</div>
                        <span>Thông tin tài khoản</span>
                    </div>
                    <div class="vp-reg-step__line"></div>
                    <div class="vp-reg-step" id="vis-step-2">
                        <div class="vp-reg-step__num">2</div>
                        <span>Xác thực email</span>
                    </div>
                    <div class="vp-reg-step__line"></div>
                    <div class="vp-reg-step" id="vis-step-3">
                        <div class="vp-reg-step__num">3</div>
                        <span>Hoàn tất</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Cột phải: Form ───────────────────────────────────── -->
    <div class="vp-reg-form-col">
        <div class="vp-reg-card" id="vp-reg-card">

            <!-- Logo mobile -->
            <div class="vp-reg-card__logo-mobile">
                <?php bloginfo('name'); ?>
            </div>

            <!-- ══ STEP 1: Form đăng ký ══════════════════════ -->
            <div id="vp-step-1" class="vp-reg-step-panel">
                <h1 class="vp-reg-card__title">Tạo tài khoản</h1>
                <p class="vp-reg-card__sub">Trở thành thành viên để giao dịch dễ dàng hơn!</p>

                <div class="vp-alert" id="vp-reg-alert-1" role="alert" aria-live="polite" style="display:none;"></div>

                <form id="vp-reg-form" novalidate autocomplete="off">

                    <!-- Họ + Tên -->
                    <div class="vp-field-row">
                        <div class="vp-field">
                            <label class="vp-field__label" for="vp-first-name">Họ <span class="vp-req">*</span></label>
                            <div class="vp-field__wrap">
                                <span class="vp-field__icon">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                </span>
                                <input type="text" id="vp-first-name" name="first_name" class="vp-field__input"
                                    placeholder="Nguyễn" required>
                            </div>
                            <span class="vp-field__error" id="vp-err-first-name"></span>
                        </div>
                        <div class="vp-field">
                            <label class="vp-field__label" for="vp-last-name">Tên <span class="vp-req">*</span></label>
                            <div class="vp-field__wrap">
                                <span class="vp-field__icon">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                </span>
                                <input type="text" id="vp-last-name" name="last_name" class="vp-field__input"
                                    placeholder="Văn A" required>
                            </div>
                            <span class="vp-field__error" id="vp-err-last-name"></span>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="vp-field">
                        <label class="vp-field__label" for="vp-reg-email">Email <span class="vp-req">*</span></label>
                        <div class="vp-field__wrap">
                            <span class="vp-field__icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="2" y="4" width="20" height="16" rx="2" />
                                    <path d="m2 7 10 7 10-7" />
                                </svg>
                            </span>
                            <input type="email" id="vp-reg-email" name="email" class="vp-field__input"
                                placeholder="you@example.com" required>
                        </div>
                        <span class="vp-field__error" id="vp-err-email"></span>
                    </div>

                    <!-- Password -->
                    <div class="vp-field">
                        <label class="vp-field__label" for="vp-reg-password">Mật khẩu <span
                                class="vp-req">*</span></label>
                        <div class="vp-field__wrap">
                            <span class="vp-field__icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                            </span>
                            <input type="password" id="vp-reg-password" name="password"
                                class="vp-field__input vp-field__input--padded-r" placeholder="Tối thiểu 8 ký tự"
                                required>
                            <button type="button" class="vp-field__toggle-pass" data-target="vp-reg-password"
                                aria-label="Hiện/Ẩn mật khẩu">
                                <svg class="vp-eye vp-eye--open" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg class="vp-eye vp-eye--closed" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" style="display:none;">
                                    <path
                                        d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                                    <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                                    <line x1="1" y1="1" x2="23" y2="23" />
                                </svg>
                            </button>
                        </div>
                        <!-- Password strength bar -->
                        <div class="vp-strength" id="vp-strength" style="display:none;">
                            <div class="vp-strength__bar">
                                <div class="vp-strength__fill" id="vp-strength-fill"></div>
                            </div>
                            <span class="vp-strength__text" id="vp-strength-text"></span>
                        </div>
                        <span class="vp-field__error" id="vp-err-password"></span>
                    </div>

                    <!-- Confirm Password -->
                    <div class="vp-field">
                        <label class="vp-field__label" for="vp-reg-confirm">Xác nhận mật khẩu <span
                                class="vp-req">*</span></label>
                        <div class="vp-field__wrap">
                            <span class="vp-field__icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                            </span>
                            <input type="password" id="vp-reg-confirm" name="confirm_password"
                                class="vp-field__input vp-field__input--padded-r" placeholder="Nhập lại mật khẩu"
                                required>
                            <button type="button" class="vp-field__toggle-pass" data-target="vp-reg-confirm"
                                aria-label="Hiện/Ẩn mật khẩu">
                                <svg class="vp-eye vp-eye--open" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg class="vp-eye vp-eye--closed" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" style="display:none;">
                                    <path
                                        d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                                    <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                                    <line x1="1" y1="1" x2="23" y2="23" />
                                </svg>
                            </button>
                        </div>
                        <span class="vp-field__error" id="vp-err-confirm"></span>
                    </div>

                    <!-- reCAPTCHA -->
                    <div class="vp-recaptcha-wrap">
                        <div class="g-recaptcha"
                            data-sitekey="<?php echo esc_attr(defined('RECAPTCHA_SITE_KEY') ? RECAPTCHA_SITE_KEY : 'YOUR_SITE_KEY'); ?>"
                            data-theme="light">
                        </div>
                        <span class="vp-field__error" id="vp-err-captcha"></span>
                    </div>

                    <!-- Terms -->
                    <div class="vp-terms">
                        <label class="vp-checkbox">
                            <input type="checkbox" id="vp-terms" name="terms" required>
                            <span class="vp-checkbox__box"></span>
                            <span class="vp-checkbox__label">
                                Tôi đồng ý với
                                <a href="<?php echo esc_url(home_url('/terms-of-service')); ?>" target="_blank">Điều
                                    khoản dịch vụ</a>
                                của VieProxy.
                            </span>
                        </label>
                        <span class="vp-field__error" id="vp-err-terms"></span>
                    </div>

                    <button type="submit" class="vp-btn vp-btn--primary" id="vp-reg-btn">
                        <span class="vp-btn__text">Tiếp tục</span>
                        <span class="vp-btn__spinner" style="display:none;">
                            <svg class="vp-spinner-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                            </svg>
                        </span>
                    </button>

                </form>

                <p class="vp-reg-card__footer">
                    Đã có tài khoản? <a href="<?php echo esc_url(home_url('/dang-nhap')); ?>">Đăng nhập</a>
                </p>
            </div>

            <!-- ══ STEP 2: Xác thực OTP ═══════════════════════ -->
            <div id="vp-step-2" class="vp-reg-step-panel" style="display:none;">
                <div class="vp-otp-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <rect x="2" y="4" width="20" height="16" rx="2" />
                        <path d="m2 7 10 7 10-7" />
                    </svg>
                </div>
                <h1 class="vp-reg-card__title">Xác thực email</h1>
                <p class="vp-reg-card__sub">
                    Mã OTP đã được gửi đến<br>
                    <strong id="vp-otp-email-display"></strong>
                </p>

                <div class="vp-alert" id="vp-reg-alert-2" role="alert" aria-live="polite" style="display:none;"></div>

                <!-- OTP inputs -->
                <div class="vp-otp-inputs" id="vp-otp-inputs">
                    <input class="vp-otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                        autocomplete="off">
                    <input class="vp-otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                        autocomplete="off">
                    <input class="vp-otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                        autocomplete="off">
                    <input class="vp-otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                        autocomplete="off">
                    <input class="vp-otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                        autocomplete="off">
                    <input class="vp-otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                        autocomplete="off">
                </div>

                <!-- Countdown -->
                <div class="vp-otp-timer">
                    <span id="vp-otp-countdown">Mã hết hạn sau <strong id="vp-otp-time">05:00</strong></span>
                    <button type="button" class="vp-otp-resend" id="vp-otp-resend" style="display:none;">Gửi lại
                        mã</button>
                </div>

                <button type="button" class="vp-btn vp-btn--primary" id="vp-otp-verify-btn">
                    <span class="vp-btn__text">Xác nhận</span>
                    <span class="vp-btn__spinner" style="display:none;">
                        <svg class="vp-spinner-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                        </svg>
                    </span>
                </button>

                <button type="button" class="vp-btn-ghost" id="vp-back-to-form">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                    Quay lại
                </button>
            </div>

            <!-- ══ STEP 3: Thành công ═════════════════════════ -->
            <div id="vp-step-3" class="vp-reg-step-panel" style="display:none;">
                <div class="vp-success-icon">
                    <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <circle cx="12" cy="12" r="10" />
                        <path d="m9 12 2 2 4-4" />
                    </svg>
                </div>
                <h1 class="vp-reg-card__title">Đăng ký thành công!</h1>
                <p class="vp-reg-card__sub">Tài khoản của bạn đã được tạo. Đang chuyển đến trang đăng nhập…</p>
                <div class="vp-success-progress">
                    <div class="vp-success-progress__fill" id="vp-success-bar"></div>
                </div>
                <a href="<?php echo esc_url(home_url('/dang-nhap')); ?>" class="vp-btn vp-btn--primary"
                    style="margin-top:24px;">
                    Đăng nhập ngay
                </a>
            </div>

        </div><!-- /.vp-reg-card -->
    </div><!-- /.vp-reg-form-col -->

</div><!-- /.vp-reg-wrap -->

<!-- Scripts -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
window.vpRegData = {
    apiBase: '<?php echo esc_js(get_rest_url(null, 'vieproxy/v1')); ?>',
    loginUrl: '<?php echo esc_js(home_url('/dang-nhap')); ?>',
    accountUrl: '<?php echo esc_js(home_url('/account')); ?>',
};
</script>

<?php get_footer(); ?>