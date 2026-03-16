<?php
/**
 * Template Name: Forgot Password
 * VieProxy — Quên mật khẩu (Email → OTP → Đặt lại mật khẩu)
 */

if (is_user_logged_in()) {
    wp_redirect(home_url('/account'));
    exit;
}

get_header();
?>

<div class="vp-fp-wrap">

    <!-- ── Cột trái: Visual ─────────────────────────── -->
    <div class="vp-fp-visual" aria-hidden="true">
        <div class="vp-fp-visual__inner">
            <div class="vp-fp-blob vp-fp-blob--1"></div>
            <div class="vp-fp-blob vp-fp-blob--2"></div>
            <div class="vp-fp-blob vp-fp-blob--3"></div>
            <div class="vp-fp-visual__content">
                <div class="vp-fp-logo">
                    <?php if (has_custom_logo()):
                        the_custom_logo();
                    else: ?>
                    <span class="vp-fp-logo__text">
                        <?php bloginfo('name'); ?>
                    </span>
                    <?php endif; ?>
                </div>
                <h2 class="vp-fp-visual__title">Đặt lại<br>mật khẩu của bạn.</h2>
                <p class="vp-fp-visual__sub">Nhập email đã đăng ký, chúng tôi sẽ gửi mã OTP để xác minh danh tính và
                    giúp bạn tạo mật khẩu mới.</p>

                <!-- Steps -->
                <div class="vp-fp-steps">
                    <div class="vp-fp-step vp-fp-step--active" id="vis-fp-step-1">
                        <div class="vp-fp-step__num">1</div>
                        <span>Xác minh email</span>
                    </div>
                    <div class="vp-fp-step__line"></div>
                    <div class="vp-fp-step" id="vis-fp-step-2">
                        <div class="vp-fp-step__num">2</div>
                        <span>Đặt lại mật khẩu</span>
                    </div>
                    <div class="vp-fp-step__line"></div>
                    <div class="vp-fp-step" id="vis-fp-step-3">
                        <div class="vp-fp-step__num">3</div>
                        <span>Hoàn tất</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Cột phải: Form ──────────────────────────── -->
    <div class="vp-fp-form-col">
        <div class="vp-fp-card">

            <!-- Logo mobile -->
            <div class="vp-fp-card__logo-mobile">
                <?php bloginfo('name'); ?>
            </div>

            <!-- ══ STEP 1: Nhập email + reCAPTCHA ══ -->
            <div id="vp-fp-step-1" class="vp-fp-panel">
                <div class="vp-fp-icon">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <rect x="2" y="4" width="20" height="16" rx="2" />
                        <path d="m2 7 10 7 10-7" />
                    </svg>
                </div>
                <h1 class="vp-fp-card__title">Quên mật khẩu?</h1>
                <p class="vp-fp-card__sub">Nhập email đăng ký, chúng tôi sẽ gửi mã OTP xác minh.</p>

                <div class="vp-fp-alert" id="vp-fp-alert-1" role="alert" style="display:none;"></div>

                <form id="vp-fp-form-1" novalidate>

                    <div class="vp-fp-field">
                        <label class="vp-fp-label" for="vp-fp-email">Email <span class="vp-fp-req">*</span></label>
                        <div class="vp-fp-input-wrap">
                            <span class="vp-fp-input-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="2" y="4" width="20" height="16" rx="2" />
                                    <path d="m2 7 10 7 10-7" />
                                </svg>
                            </span>
                            <input type="email" id="vp-fp-email" class="vp-fp-input" placeholder="you@example.com"
                                required autocomplete="email">
                        </div>
                        <span class="vp-fp-err" id="vp-fp-err-email"></span>
                    </div>

                    <!-- reCAPTCHA -->
                    <div class="vp-fp-recaptcha">
                        <div class="g-recaptcha"
                            data-sitekey="<?php echo esc_attr(defined('RECAPTCHA_SITE_KEY') ? RECAPTCHA_SITE_KEY : 'YOUR_SITE_KEY'); ?>"
                            data-theme="light">
                        </div>
                        <span class="vp-fp-err" id="vp-fp-err-captcha"></span>
                    </div>

                    <button type="submit" class="vp-fp-btn" id="vp-fp-send-btn">
                        <span class="vp-fp-btn__text">Gửi mã OTP</span>
                        <span class="vp-fp-btn__spinner" style="display:none;">
                            <svg class="vp-fp-spin" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                            </svg>
                        </span>
                    </button>

                </form>

                <p class="vp-fp-card__footer">
                    <a href="<?php echo esc_url(home_url('/dang-nhap')); ?>">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                        Quay lại đăng nhập
                    </a>
                </p>
            </div>

            <!-- ══ STEP 2: OTP + mật khẩu mới ══════ -->
            <div id="vp-fp-step-2" class="vp-fp-panel" style="display:none;">
                <div class="vp-fp-icon vp-fp-icon--lock">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <rect x="3" y="11" width="18" height="11" rx="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                </div>
                <h1 class="vp-fp-card__title">Đặt lại mật khẩu</h1>
                <p class="vp-fp-card__sub">
                    Nhập mã OTP đã gửi đến<br>
                    <strong id="vp-fp-email-display"></strong>
                </p>

                <div class="vp-fp-alert" id="vp-fp-alert-2" role="alert" style="display:none;"></div>

                <!-- OTP 6 ô -->
                <div class="vp-fp-otp-inputs" id="vp-fp-otp-inputs">
                    <input class="vp-fp-otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                        autocomplete="off">
                    <input class="vp-fp-otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                        autocomplete="off">
                    <input class="vp-fp-otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                        autocomplete="off">
                    <input class="vp-fp-otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                        autocomplete="off">
                    <input class="vp-fp-otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                        autocomplete="off">
                    <input class="vp-fp-otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                        autocomplete="off">
                </div>

                <!-- Countdown -->
                <div class="vp-fp-timer">
                    <span id="vp-fp-countdown-wrap">Mã hết hạn sau <strong id="vp-fp-time">05:00</strong></span>
                    <button type="button" id="vp-fp-resend" class="vp-fp-resend" style="display:none;">Gửi lại
                        mã</button>
                </div>

                <form id="vp-fp-form-2" novalidate>

                    <!-- Mật khẩu mới -->
                    <div class="vp-fp-field">
                        <label class="vp-fp-label" for="vp-fp-new-pass">Mật khẩu mới <span
                                class="vp-fp-req">*</span></label>
                        <div class="vp-fp-input-wrap">
                            <span class="vp-fp-input-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                            </span>
                            <input type="password" id="vp-fp-new-pass" class="vp-fp-input vp-fp-input--pr"
                                placeholder="Tối thiểu 8 ký tự" required>
                            <button type="button" class="vp-fp-toggle-pass" data-target="vp-fp-new-pass"
                                aria-label="Hiện/ẩn">
                                <svg class="vp-fp-eye vp-fp-eye--on" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg class="vp-fp-eye vp-fp-eye--off" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" style="display:none;">
                                    <path
                                        d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                                    <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                                    <line x1="1" y1="1" x2="23" y2="23" />
                                </svg>
                            </button>
                        </div>
                        <!-- Strength bar -->
                        <div class="vp-fp-strength" id="vp-fp-strength" style="display:none;">
                            <div class="vp-fp-strength__bar">
                                <div class="vp-fp-strength__fill" id="vp-fp-strength-fill"></div>
                            </div>
                            <span class="vp-fp-strength__text" id="vp-fp-strength-text"></span>
                        </div>
                        <span class="vp-fp-err" id="vp-fp-err-new-pass"></span>
                    </div>

                    <!-- Xác nhận mật khẩu -->
                    <div class="vp-fp-field">
                        <label class="vp-fp-label" for="vp-fp-confirm-pass">Xác nhận mật khẩu <span
                                class="vp-fp-req">*</span></label>
                        <div class="vp-fp-input-wrap">
                            <span class="vp-fp-input-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                            </span>
                            <input type="password" id="vp-fp-confirm-pass" class="vp-fp-input vp-fp-input--pr"
                                placeholder="Nhập lại mật khẩu" required>
                            <button type="button" class="vp-fp-toggle-pass" data-target="vp-fp-confirm-pass"
                                aria-label="Hiện/ẩn">
                                <svg class="vp-fp-eye vp-fp-eye--on" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg class="vp-fp-eye vp-fp-eye--off" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" style="display:none;">
                                    <path
                                        d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                                    <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                                    <line x1="1" y1="1" x2="23" y2="23" />
                                </svg>
                            </button>
                        </div>
                        <span class="vp-fp-err" id="vp-fp-err-confirm-pass"></span>
                    </div>

                    <button type="submit" class="vp-fp-btn" id="vp-fp-reset-btn">
                        <span class="vp-fp-btn__text">Xác nhận đặt lại</span>
                        <span class="vp-fp-btn__spinner" style="display:none;">
                            <svg class="vp-fp-spin" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                            </svg>
                        </span>
                    </button>

                </form>

                <button type="button" class="vp-fp-ghost" id="vp-fp-back">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                    Quay lại
                </button>
            </div>

            <!-- ══ STEP 3: Thành công ════════════════ -->
            <div id="vp-fp-step-3" class="vp-fp-panel" style="display:none;">
                <div class="vp-fp-success-icon">
                    <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <circle cx="12" cy="12" r="10" />
                        <path d="m9 12 2 2 4-4" />
                    </svg>
                </div>
                <h1 class="vp-fp-card__title">Đổi mật khẩu thành công!</h1>
                <p class="vp-fp-card__sub">Mật khẩu của bạn đã được cập nhật. Đang chuyển đến trang đăng nhập…</p>
                <div class="vp-fp-progress">
                    <div class="vp-fp-progress__fill" id="vp-fp-progress-bar"></div>
                </div>
                <a href="<?php echo esc_url(home_url('/dang-nhap')); ?>" class="vp-fp-btn"
                    style="margin-top:24px;text-decoration:none;">
                    Đăng nhập ngay
                </a>
            </div>

        </div><!-- /.vp-fp-card -->
    </div><!-- /.vp-fp-form-col -->

</div><!-- /.vp-fp-wrap -->

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
window.vpFpData = {
    apiBase: '<?php echo esc_js(get_rest_url(null, 'vieproxy/v1')); ?>',
    loginUrl: '<?php echo esc_js(home_url('/dang-nhap')); ?>',
};
</script>

<?php get_footer(); ?>