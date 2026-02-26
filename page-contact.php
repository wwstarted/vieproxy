<?php
/**
 * Template Name: Liên Hệ
 * Description: Template trang Liên Hệ của VieProxy
 */

get_header();

$company_name = 'VieProxy';
$company_email = 'support@vieproxy.vn';
$company_zalo = '034.770.0437';
$company_tg = '@VieProxyVN';
$company_fb = 'VieProxyVN';
?>

<main class="main-info-page main-contact-page">

    <!-- ═══════════════════════════════════════════════
         PAGE HERO  (breadcrumb embedded — same pattern as proxy-hero)
    ════════════════════════════════════════════════ -->
    <section class="contact-hero">
        <div class="wrapper">

            <!-- Breadcrumb — inside hero, mirrors proxy-hero__breadcrumb -->
            <nav class="contact-hero__breadcrumb" aria-label="breadcrumb">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="breadcrumb-link">Trang chủ</a>
                <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
                <span class="breadcrumb-current">Liên Hệ</span>
            </nav>

            <!-- Hero body -->
            <div class="contact-hero__inner">
                <!-- <div class="contact-hero__icon">
                    <i class="fa-solid fa-headset"></i>
                </div> -->
                <div class="contact-hero__content">
                    <h1 class="contact-hero__title">Liên Hệ Với Chúng Tôi</h1>
                    <p class="contact-hero__subtitle">
                        Đội ngũ hỗ trợ <?php echo esc_html($company_name); ?> luôn sẵn sàng giải đáp mọi thắc mắc.
                        Hãy liên hệ với chúng tôi qua bất kỳ kênh nào bạn thuận tiện.
                    </p>
                    <div class="contact-hero__meta">
                        <span class="contact-hero__badge">
                            <i class="fa-solid fa-clock"></i>
                            Hỗ trợ 7 ngày / tuần
                        </span>
                        <span class="contact-hero__badge">
                            <i class="fa-solid fa-bolt"></i>
                            Phản hồi trong 1 giờ
                        </span>
                        <span class="contact-hero__badge">
                            <i class="fa-solid fa-globe"></i>
                            8:00 – 22:00 GMT+7
                        </span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Decorative blobs -->
        <div class="contact-hero__blob contact-hero__blob--1"></div>
        <div class="contact-hero__blob contact-hero__blob--2"></div>
    </section>

    <!-- ═══════════════════════════════════════════════
         QUICK CONTACT CARDS (full-width strip)
    ════════════════════════════════════════════════ -->
    <div class="contact-quick-strip">
        <div class="wrapper">
            <div class="contact-quick-grid">

                <a href="mailto:<?php echo esc_attr($company_email); ?>" class="contact-quick-card">
                    <div class="contact-quick-card__icon contact-quick-card__icon--email">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div class="contact-quick-card__body">
                        <span class="contact-quick-card__label">Email hỗ trợ</span>
                        <strong class="contact-quick-card__value"><?php echo esc_html($company_email); ?></strong>
                    </div>
                    <i class="fa-solid fa-arrow-up-right-from-square contact-quick-card__arrow"></i>
                </a>

                <a href="https://t.me/VieProxyVN" target="_blank" rel="noopener noreferrer" class="contact-quick-card">
                    <div class="contact-quick-card__icon contact-quick-card__icon--tg">
                        <i class="fa-brands fa-telegram"></i>
                    </div>
                    <div class="contact-quick-card__body">
                        <span class="contact-quick-card__label">Telegram</span>
                        <strong class="contact-quick-card__value"><?php echo esc_html($company_tg); ?></strong>
                    </div>
                    <i class="fa-solid fa-arrow-up-right-from-square contact-quick-card__arrow"></i>
                </a>

                <a href="https://zalo.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', $company_zalo)); ?>"
                    target="_blank" rel="noopener noreferrer" class="contact-quick-card">
                    <div class="contact-quick-card__icon contact-quick-card__icon--zalo">
                        <i class="fa-solid fa-comment-dots"></i>
                    </div>
                    <div class="contact-quick-card__body">
                        <span class="contact-quick-card__label">Zalo</span>
                        <strong class="contact-quick-card__value"><?php echo esc_html($company_zalo); ?></strong>
                    </div>
                    <i class="fa-solid fa-arrow-up-right-from-square contact-quick-card__arrow"></i>
                </a>

                <a href="https://fb.com/VieProxyVN" target="_blank" rel="noopener noreferrer"
                    class="contact-quick-card">
                    <div class="contact-quick-card__icon contact-quick-card__icon--fb">
                        <i class="fa-brands fa-facebook-f"></i>
                    </div>
                    <div class="contact-quick-card__body">
                        <span class="contact-quick-card__label">Facebook</span>
                        <strong class="contact-quick-card__value"><?php echo esc_html($company_fb); ?></strong>
                    </div>
                    <i class="fa-solid fa-arrow-up-right-from-square contact-quick-card__arrow"></i>
                </a>

            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════
         MAIN CONTENT LAYOUT
    ════════════════════════════════════════════════ -->
    <div class="wrapper contact-page-wrapper">
        <div class="contact-page-layout">

            <!-- ── Left Sidebar ──────────────────────── -->
            <aside class="contact-sidebar">

                <!-- Support Hours -->
                <div class="contact-sidebar-card">
                    <div class="contact-sidebar-card__header">
                        <i class="fa-solid fa-clock"></i>
                        <span>Giờ hỗ trợ</span>
                    </div>
                    <ul class="contact-hours-list">
                        <li class="contact-hours-item">
                            <span class="contact-hours-item__day">Thứ Hai – Thứ Sáu</span>
                            <span class="contact-hours-item__time">8:00 – 22:00</span>
                        </li>
                        <li class="contact-hours-item">
                            <span class="contact-hours-item__day">Thứ Bảy</span>
                            <span class="contact-hours-item__time">9:00 – 21:00</span>
                        </li>
                        <li class="contact-hours-item">
                            <span class="contact-hours-item__day">Chủ Nhật</span>
                            <span class="contact-hours-item__time">9:00 – 18:00</span>
                        </li>
                    </ul>
                    <div class="contact-hours-note">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Telegram bot tự động hỗ trợ 24/7</span>
                    </div>
                </div>

                <!-- Response Time -->
                <div class="contact-sidebar-card">
                    <div class="contact-sidebar-card__header">
                        <i class="fa-solid fa-bolt"></i>
                        <span>Thời gian phản hồi</span>
                    </div>
                    <ul class="contact-response-list">
                        <li class="contact-response-item">
                            <div class="contact-response-item__channel">
                                <i class="fa-brands fa-telegram"></i> Telegram
                            </div>
                            <span class="contact-response-badge contact-response-badge--fast">≤ 30 phút</span>
                        </li>
                        <li class="contact-response-item">
                            <div class="contact-response-item__channel">
                                <i class="fa-solid fa-comment-dots"></i> Zalo
                            </div>
                            <span class="contact-response-badge contact-response-badge--fast">≤ 1 giờ</span>
                        </li>
                        <li class="contact-response-item">
                            <div class="contact-response-item__channel">
                                <i class="fa-solid fa-envelope"></i> Email
                            </div>
                            <span class="contact-response-badge contact-response-badge--medium">≤ 24 giờ</span>
                        </li>
                        <li class="contact-response-item">
                            <div class="contact-response-item__channel">
                                <i class="fa-brands fa-facebook-f"></i> Facebook
                            </div>
                            <span class="contact-response-badge contact-response-badge--medium">≤ 4 giờ</span>
                        </li>
                    </ul>
                </div>

                <!-- Quick Links -->
                <!-- <div class="contact-sidebar-card">
                    <div class="contact-sidebar-card__header">
                        <i class="fa-solid fa-link"></i>
                        <span>Truy cập nhanh</span>
                    </div>
                    <ul class="contact-quicklinks">
                        <li>
                            <a href="<?php echo esc_url(home_url('/')); ?>">
                                <i class="fa-solid fa-house"></i>
                                Trang chủ
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa-solid fa-cart-shopping"></i>
                                Mua proxy ngay
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa-solid fa-circle-question"></i>
                                Câu hỏi thường gặp
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa-solid fa-book-open"></i>
                                Hướng dẫn sử dụng
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/chinh-sach-bao-mat/')); ?>">
                                <i class="fa-solid fa-shield-halved"></i>
                                Chính sách bảo mật
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/dieu-khoan-dich-vu/')); ?>">
                                <i class="fa-solid fa-file-contract"></i>
                                Điều khoản dịch vụ
                            </a>
                        </li>
                    </ul>
                </div> -->

            </aside>

            <!-- ── Main Content ──────────────────────── -->
            <div class="contact-main">

                <!-- Contact Form -->
                <div class="contact-form-card" id="contactFormCard">
                    <div class="contact-form-card__header">
                        <div class="contact-form-card__header-icon">
                            <i class="fa-solid fa-paper-plane"></i>
                        </div>
                        <div>
                            <h2 class="contact-form-card__title">Gửi tin nhắn cho chúng tôi</h2>
                            <p class="contact-form-card__subtitle">Điền vào form bên dưới, chúng tôi sẽ phản hồi trong
                                vòng 24 giờ.</p>
                        </div>
                    </div>

                    <!-- Success / Error messages -->
                    <div class="contact-form-alert contact-form-alert--success" id="formSuccess" style="display:none;">
                        <i class="fa-solid fa-circle-check"></i>
                        <div>
                            <strong>Gửi thành công!</strong>
                            <p>Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi sớm nhất có thể.</p>
                        </div>
                    </div>

                    <div class="contact-form-alert contact-form-alert--error" id="formError" style="display:none;">
                        <i class="fa-solid fa-circle-xmark"></i>
                        <div>
                            <strong>Có lỗi xảy ra!</strong>
                            <p>Vui lòng kiểm tra lại thông tin và thử lại.</p>
                        </div>
                    </div>

                    <!-- The form -->
                    <form class="contact-form" id="contactForm" novalidate
                        action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="POST">

                        <?php wp_nonce_field('vieproxy_contact_form', 'contact_nonce'); ?>
                        <input type="hidden" name="action" value="vieproxy_contact_submit">

                        <!-- Row: Name + Email -->
                        <div class="contact-form-row contact-form-row--2col">

                            <div class="contact-form-field" id="field-name">
                                <label class="contact-form-label" for="contactName">
                                    Họ và tên
                                    <span class="contact-form-required">*</span>
                                </label>
                                <div class="contact-form-input-wrap">
                                    <i class="fa-solid fa-user contact-form-input-icon"></i>
                                    <input type="text" id="contactName" name="contact_name" class="contact-form-input"
                                        placeholder="Vo Cao Thuy Mai" autocomplete="name" required />
                                </div>
                                <span class="contact-form-error-msg" id="error-name">Vui lòng nhập họ tên.</span>
                            </div>

                            <div class="contact-form-field" id="field-email">
                                <label class="contact-form-label" for="contactEmail">
                                    Địa chỉ email
                                    <span class="contact-form-required">*</span>
                                </label>
                                <div class="contact-form-input-wrap">
                                    <i class="fa-solid fa-envelope contact-form-input-icon"></i>
                                    <input type="email" id="contactEmail" name="contact_email"
                                        class="contact-form-input" placeholder="email@example.com" autocomplete="email"
                                        required />
                                </div>
                                <span class="contact-form-error-msg" id="error-email">Vui lòng nhập email hợp lệ.</span>
                            </div>

                        </div>

                        <!-- Row: Phone + Subject -->
                        <div class="contact-form-row contact-form-row--2col">

                            <div class="contact-form-field" id="field-phone">
                                <label class="contact-form-label" for="contactPhone">
                                    Số điện thoại
                                    <span class="contact-form-optional">(tuỳ chọn)</span>
                                </label>
                                <div class="contact-form-input-wrap">
                                    <i class="fa-solid fa-phone contact-form-input-icon"></i>
                                    <input type="tel" id="contactPhone" name="contact_phone" class="contact-form-input"
                                        placeholder="0xxxxxxxxx" autocomplete="tel" />
                                </div>
                            </div>

                            <div class="contact-form-field" id="field-subject">
                                <label class="contact-form-label" for="contactSubject">
                                    Chủ đề
                                    <span class="contact-form-required">*</span>
                                </label>
                                <div class="contact-form-input-wrap contact-form-input-wrap--select">
                                    <i class="fa-solid fa-tag contact-form-input-icon"></i>
                                    <select id="contactSubject" name="contact_subject"
                                        class="contact-form-input contact-form-select" required>
                                        <option value="">-- Chọn chủ đề --</option>
                                        <option value="mua-proxy">Tư vấn mua proxy</option>
                                        <option value="ky-thuat">Hỗ trợ kỹ thuật</option>
                                        <option value="thanh-toan">Vấn đề thanh toán</option>
                                        <option value="hoan-tien">Yêu cầu hoàn tiền</option>
                                        <option value="bao-cao-loi">Báo cáo lỗi</option>
                                        <option value="hop-tac">Hợp tác kinh doanh</option>
                                        <option value="khac">Khác</option>
                                    </select>
                                    <i class="fa-solid fa-chevron-down contact-form-select-arrow"></i>
                                </div>
                                <span class="contact-form-error-msg" id="error-subject">Vui lòng chọn chủ đề.</span>
                            </div>

                        </div>

                        <!-- Row: Message -->
                        <div class="contact-form-row">
                            <div class="contact-form-field" id="field-message">
                                <label class="contact-form-label" for="contactMessage">
                                    Nội dung tin nhắn
                                    <span class="contact-form-required">*</span>
                                </label>
                                <div class="contact-form-input-wrap contact-form-input-wrap--textarea">
                                    <i
                                        class="fa-solid fa-comment-dots contact-form-input-icon contact-form-input-icon--top"></i>
                                    <textarea id="contactMessage" name="contact_message"
                                        class="contact-form-input contact-form-textarea"
                                        placeholder="Mô tả chi tiết vấn đề hoặc câu hỏi của bạn..." rows="6"
                                        required></textarea>
                                    <span class="contact-form-char-count" id="charCount">0 / 1000</span>
                                </div>
                                <span class="contact-form-error-msg" id="error-message">Vui lòng nhập nội dung (tối
                                    thiểu 20 ký tự).</span>
                            </div>
                        </div>

                        <!-- Row: Order ID (optional) -->
                        <div class="contact-form-row">
                            <div class="contact-form-field" id="field-order">
                                <label class="contact-form-label" for="contactOrder">
                                    Mã đơn hàng
                                    <span class="contact-form-optional">(nếu có)</span>
                                </label>
                                <div class="contact-form-input-wrap">
                                    <i class="fa-solid fa-hashtag contact-form-input-icon"></i>
                                    <input type="text" id="contactOrder" name="contact_order" class="contact-form-input"
                                        placeholder="VD: #12345" />
                                </div>
                            </div>
                        </div>

                        <!-- Privacy consent -->
                        <div class="contact-form-row">
                            <div class="contact-form-field contact-form-field--checkbox" id="field-consent">
                                <label class="contact-form-checkbox-label">
                                    <input type="checkbox" id="contactConsent" name="contact_consent" required />
                                    <span class="contact-form-checkbox-custom"></span>
                                    <span class="contact-form-checkbox-text">
                                        Tôi đồng ý với
                                        <a href="<?php echo esc_url(home_url('/chinh-sach-bao-mat/')); ?>"
                                            target="_blank">Chính sách bảo mật</a>
                                        và cho phép <?php echo esc_html($company_name); ?> liên hệ lại theo thông tin
                                        tôi đã cung cấp.
                                    </span>
                                </label>
                                <span class="contact-form-error-msg" id="error-consent">Vui lòng đồng ý với chính sách
                                    bảo mật.</span>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="contact-form-row">
                            <button type="submit" class="contact-form-submit" id="contactSubmit">
                                <span class="contact-form-submit__text">
                                    <i class="fa-solid fa-paper-plane"></i>
                                    Gửi tin nhắn
                                </span>
                                <span class="contact-form-submit__loading" style="display:none;">
                                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                                    Đang gửi...
                                </span>
                            </button>
                        </div>

                    </form>
                </div><!-- /.contact-form-card -->

                <!-- FAQ mini section -->
                <!-- <div class="contact-faq-section">
                    <h3 class="contact-faq-title">
                        <i class="fa-solid fa-circle-question"></i>
                        Câu hỏi thường gặp
                    </h3>
                    <p class="contact-faq-subtitle">Có thể bạn sẽ tìm được câu trả lời ngay tại đây trước khi cần liên
                        hệ.</p>

                    <div class="contact-faq-list" id="contactFaqList">

                        <div class="contact-faq-item is-open">
                            <button class="contact-faq-question" aria-expanded="true">
                                <span>Làm thế nào để mua proxy tại VieProxy?</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                            <div class="contact-faq-answer">
                                <p>Bạn chọn loại proxy phù hợp trên trang chủ → điền số lượng, thời hạn → nhấn
                                    "Mua ngay" → đăng nhập/đăng ký tài khoản → thanh toán → proxy được kích hoạt
                                    tự động trong vài phút.</p>
                            </div>
                        </div>

                        <div class="contact-faq-item">
                            <button class="contact-faq-question" aria-expanded="false">
                                <span>VieProxy hỗ trợ những hình thức thanh toán nào?</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                            <div class="contact-faq-answer">
                                <p>Chúng tôi hỗ trợ: Chuyển khoản ngân hàng nội địa, ví MoMo, ZaloPay,
                                    VNPay và một số phương thức thanh toán quốc tế. Tất cả giao dịch được
                                    xử lý tự động sau khi xác nhận thanh toán.</p>
                            </div>
                        </div>

                        <div class="contact-faq-item">
                            <button class="contact-faq-question" aria-expanded="false">
                                <span>Tôi có thể đổi/thay IP nếu bị block không?</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                            <div class="contact-faq-answer">
                                <p>Có. IP bị block hoặc không hoạt động sẽ được thay thế miễn phí trong
                                    vòng 24 giờ kể từ khi bạn báo cáo qua Telegram hoặc email hỗ trợ.</p>
                            </div>
                        </div>

                        <div class="contact-faq-item">
                            <button class="contact-faq-question" aria-expanded="false">
                                <span>Proxy của VieProxy hỗ trợ giao thức nào?</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                            <div class="contact-faq-answer">
                                <p>Tất cả proxy của chúng tôi đều hỗ trợ <strong>HTTP/HTTPS</strong> và
                                    <strong>SOCKS5</strong>. Xác thực bằng IP whitelist hoặc
                                    username:password tùy gói dịch vụ.
                                </p>
                            </div>
                        </div>

                        <div class="contact-faq-item">
                            <button class="contact-faq-question" aria-expanded="false">
                                <span>Chính sách hoàn tiền như thế nào?</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                            <div class="contact-faq-answer">
                                <p>Hoàn tiền 100% nếu dịch vụ không hoạt động trong 72 giờ đầu do lỗi của
                                    chúng tôi, hoặc nếu bạn hủy trước khi kích hoạt. Xem chi tiết tại
                                    <a href="<?php echo esc_url(home_url('/dieu-khoan-dich-vu/')); ?>">Điều khoản dịch
                                        vụ</a>.
                                </p>
                            </div>
                        </div>

                    </div>
                </div> -->

            </div><!-- /.contact-main -->
        </div>
    </div><!-- /.wrapper -->

</main>

<!-- Back to Top Button -->
<!-- <button class="info-back-to-top" id="backToTop" aria-label="Về đầu trang">
    <i class="fa-solid fa-chevron-up"></i>
</button> -->

<?php get_footer(); ?>