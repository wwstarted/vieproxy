<?php
/**
 * VieProxy — page-profile.php
 * Profile sub-page fragment. Loaded via AJAX into #page-content.
 * Do NOT add get_header() / get_footer() — this is a content fragment only.
 */
?>

<div class="page-header">
    <h1>Hồ sơ</h1>
    <p class="page-subtitle">Quản lý thông tin cá nhân và email đăng nhập</p>
</div>

<div class="form-section" id="profileForm">

    <!-- Full name -->
    <div class="form-group">
        <label class="form-label" for="profileFullname">Họ và tên</label>
        <div class="form-input-wrap">
            <input type="text" id="profileFullname" class="form-input" placeholder="Nhập họ và tên"
                autocomplete="name" />
        </div>
    </div>

    <!-- Email -->
    <div class="form-group">
        <label class="form-label" for="profileEmail">Email</label>
        <div class="form-input-wrap" style="gap:8px;">
            <input type="email" id="profileEmail" class="form-input is-disabled" placeholder="email@example.com"
                disabled autocomplete="email" />
            <button type="button" class="btn-icon" id="profileEditEmailBtn" title="Đổi email">
                <i class="fa-regular fa-pen-to-square"></i>
            </button>
        </div>
        <p class="form-helper" id="profileEmailHelper">
            Nhấn vào biểu tượng bút để thay đổi email. Yêu cầu xác thực OTP.
        </p>
    </div>

    <!-- Phone -->
    <div class="form-group">
        <label class="form-label" for="profilePhone">Số điện thoại</label>
        <div class="form-input-wrap phone-wrap">
            <div class="phone-prefix" id="phonePrefixBtn">
                <img src="https://flagcdn.com/w40/vn.png" alt="VN" id="phonePrefixFlag" />
                <span id="phonePrefixCode">+84</span>
                <i class="fa-solid fa-chevron-down" style="font-size:10px;"></i>
            </div>
            <input type="tel" id="profilePhone" class="form-input" placeholder="912 345 678" />

            <!-- Country dropdown -->
            <div class="phone-dropdown" id="phoneDropdown">
                <div class="phone-dropdown-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="phoneCountrySearch" placeholder="Tìm quốc gia..." />
                </div>
                <ul class="phone-dropdown-list" id="phoneCountryList">
                    <li class="phone-dropdown-item is-active" data-code="+84" data-flag="vn">
                        <img src="https://flagcdn.com/w40/vn.png" alt="VN" />
                        <span class="country-name">Việt Nam</span>
                        <span class="country-dial">+84</span>
                    </li>
                    <li class="phone-dropdown-item" data-code="+1" data-flag="us">
                        <img src="https://flagcdn.com/w40/us.png" alt="US" />
                        <span class="country-name">United States</span>
                        <span class="country-dial">+1</span>
                    </li>
                    <li class="phone-dropdown-item" data-code="+44" data-flag="gb">
                        <img src="https://flagcdn.com/w40/gb.png" alt="GB" />
                        <span class="country-name">United Kingdom</span>
                        <span class="country-dial">+44</span>
                    </li>
                    <li class="phone-dropdown-item" data-code="+86" data-flag="cn">
                        <img src="https://flagcdn.com/w40/cn.png" alt="CN" />
                        <span class="country-name">China</span>
                        <span class="country-dial">+86</span>
                    </li>
                    <li class="phone-dropdown-item" data-code="+81" data-flag="jp">
                        <img src="https://flagcdn.com/w40/jp.png" alt="JP" />
                        <span class="country-name">Japan</span>
                        <span class="country-dial">+81</span>
                    </li>
                    <li class="phone-dropdown-item" data-code="+82" data-flag="kr">
                        <img src="https://flagcdn.com/w40/kr.png" alt="KR" />
                        <span class="country-name">South Korea</span>
                        <span class="country-dial">+82</span>
                    </li>
                    <li class="phone-dropdown-item" data-code="+65" data-flag="sg">
                        <img src="https://flagcdn.com/w40/sg.png" alt="SG" />
                        <span class="country-name">Singapore</span>
                        <span class="country-dial">+65</span>
                    </li>
                    <li class="phone-dropdown-item" data-code="+66" data-flag="th">
                        <img src="https://flagcdn.com/w40/th.png" alt="TH" />
                        <span class="country-name">Thailand</span>
                        <span class="country-dial">+66</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn-primary" id="profileSaveBtn">
            <i class="fa-solid fa-floppy-disk"></i>
            Lưu thay đổi
        </button>
    </div>
</div>

<!-- OTP Modal — email change -->
<div class="vp-modal" id="profileOtpModal" role="dialog" aria-modal="true">
    <div class="vp-modal-overlay" id="profileOtpOverlay"></div>
    <div class="vp-modal-box">
        <div class="vp-modal-head">
            <h3>Xác thực email mới</h3>
            <button class="vp-modal-close" id="profileOtpClose" aria-label="Đóng">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="vp-modal-body">
            <p class="vp-modal-desc">
                Mã OTP đã được gửi đến<br>
                <strong id="profileOtpTarget"></strong>
            </p>
            <div class="otp-boxes" id="profileOtpBoxes">
                <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
            </div>
            <p class="otp-resend">
                Không nhận được mã?
                <a href="#" id="profileOtpResend">Gửi lại</a>
                <span class="otp-countdown" id="profileOtpCountdown"></span>
            </p>
        </div>
        <div class="vp-modal-foot">
            <button class="btn-ghost" id="profileOtpCancel">Hủy</button>
            <button class="btn-primary" id="profileOtpVerify">
                <i class="fa-solid fa-check"></i>
                Xác nhận
            </button>
        </div>
    </div>
</div>