<?php
/**
 * VieProxy — page-change-password.php
 * Change password sub-page fragment.
 * Loaded via AJAX into #page-content. No get_header/get_footer.
 */
?>

<div class="page-header">
    <h1>Đổi mật khẩu</h1>
    <p class="page-subtitle">Sử dụng ký tự đặc biệt, chữ hoa và số để tăng độ bảo mật</p>
</div>

<div class="form-section" id="changepassForm">

    <!-- Current password -->
    <div class="form-group">
        <label class="form-label" for="cpCurrentPass">
            Mật khẩu hiện tại <span class="required">*</span>
        </label>
        <div class="form-input-wrap pw-wrap">
            <input type="password" id="cpCurrentPass" class="form-input" placeholder="Nhập mật khẩu hiện tại"
                autocomplete="current-password" />
            <button type="button" class="pw-toggle" data-target="cpCurrentPass" aria-label="Hiện/ẩn mật khẩu">
                <i class="fa-regular fa-eye"></i>
            </button>
        </div>
    </div>

    <!-- New password -->
    <div class="form-group">
        <label class="form-label" for="cpNewPass">
            Mật khẩu mới <span class="required">*</span>
        </label>
        <div class="form-input-wrap pw-wrap">
            <input type="password" id="cpNewPass" class="form-input" placeholder="Nhập mật khẩu mới"
                autocomplete="new-password" />
            <button type="button" class="pw-toggle" data-target="cpNewPass" aria-label="Hiện/ẩn mật khẩu">
                <i class="fa-regular fa-eye"></i>
            </button>
        </div>
        <!-- Strength meter -->
        <div class="pw-strength" id="cpStrength" style="display:none;">
            <div class="pw-strength-bar">
                <div class="pw-strength-fill" id="cpStrengthFill"></div>
            </div>
            <span class="pw-strength-label" id="cpStrengthLabel"></span>
        </div>
        <ul class="pw-rules" id="cpPwRules">
            <li class="pw-rule" id="ruleLength"><i class="fa-solid fa-circle-xmark"></i> Ít nhất 8 ký tự</li>
            <li class="pw-rule" id="ruleLower"><i class="fa-solid fa-circle-xmark"></i> Ít nhất 1 chữ thường</li>
            <li class="pw-rule" id="ruleUpper"><i class="fa-solid fa-circle-xmark"></i> Ít nhất 1 chữ hoa</li>
            <li class="pw-rule" id="ruleNumber"><i class="fa-solid fa-circle-xmark"></i> Ít nhất 1 chữ số</li>
        </ul>
    </div>

    <!-- Confirm password -->
    <div class="form-group">
        <label class="form-label" for="cpConfirmPass">
            Xác nhận mật khẩu mới <span class="required">*</span>
        </label>
        <div class="form-input-wrap pw-wrap">
            <input type="password" id="cpConfirmPass" class="form-input" placeholder="Nhập lại mật khẩu mới"
                autocomplete="new-password" />
            <button type="button" class="pw-toggle" data-target="cpConfirmPass" aria-label="Hiện/ẩn mật khẩu">
                <i class="fa-regular fa-eye"></i>
            </button>
        </div>
        <p class="form-helper is-error" id="cpConfirmError" style="display:none;">
            Mật khẩu xác nhận không khớp.
        </p>
    </div>

    <div class="form-actions">
        <button type="button" class="btn-primary" id="cpSaveBtn">
            <i class="fa-solid fa-lock"></i>
            Đổi mật khẩu
        </button>
    </div>
</div>

<!-- OTP Modal -->
<div class="vp-modal" id="cpOtpModal" role="dialog" aria-modal="true">
    <div class="vp-modal-overlay" id="cpOtpOverlay"></div>
    <div class="vp-modal-box">
        <div class="vp-modal-head">
            <h3>Xác thực đổi mật khẩu</h3>
            <button class="vp-modal-close" id="cpOtpClose" aria-label="Đóng">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="vp-modal-body">
            <p class="vp-modal-desc">
                Mã OTP đã được gửi đến email của bạn<br>
                để xác thực thao tác đổi mật khẩu.
            </p>
            <div class="otp-boxes" id="cpOtpBoxes">
                <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
            </div>
            <p class="otp-resend">
                Không nhận được mã?
                <a href="#" id="cpOtpResend">Gửi lại</a>
                <span class="otp-countdown" id="cpOtpCountdown"></span>
            </p>
        </div>
        <div class="vp-modal-foot">
            <button class="btn-ghost" id="cpOtpCancel">Hủy</button>
            <button class="btn-primary" id="cpOtpVerify">
                <i class="fa-solid fa-check"></i>
                Xác nhận
            </button>
        </div>
    </div>
</div>