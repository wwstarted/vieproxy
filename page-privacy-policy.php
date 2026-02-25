<?php
/**
 * Template Name: Chính Sách Bảo Mật
 * Description: Template trang Chính Sách Bảo Mật của VieProxy
 */

get_header();

$last_updated = '01/02/2025';
$company_name = 'VieProxy';
$company_email = 'support@vieproxy.vn';
$company_zalo = '034.770.0437';
?>

<main class="main-info-page">

    <!-- ═══════════════════════════════════════════════
         BREADCRUMB
    ════════════════════════════════════════════════ -->
    <div class="info-breadcrumb">
        <div class="wrapper">
            <nav aria-label="breadcrumbs" class="info-breadcrumb__nav">
                <a href="<?php echo esc_url(home_url('/')); ?>">Trang chủ</a>
                <span class="info-breadcrumb__sep"><i class="fa-solid fa-chevron-right"></i></span>
                <span class="info-breadcrumb__current">Chính Sách Bảo Mật</span>
            </nav>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════
         PAGE HERO
    ════════════════════════════════════════════════ -->
    <section class="info-page-hero">
        <div class="info-page-hero__bg"></div>
        <div class="wrapper info-page-hero__inner">
            <div class="info-page-hero__icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="info-page-hero__content">
                <h1 class="info-page-hero__title">Chính Sách Bảo Mật</h1>
                <p class="info-page-hero__subtitle">
                    Chúng tôi cam kết bảo vệ thông tin cá nhân và quyền riêng tư của bạn.
                    Vui lòng đọc kỹ chính sách này để hiểu cách chúng tôi xử lý dữ liệu của bạn.
                </p>
                <div class="info-page-hero__meta">
                    <span class="info-page-hero__badge">
                        <i class="fa-regular fa-calendar"></i>
                        Cập nhật lần cuối: <?php echo esc_html($last_updated); ?>
                    </span>
                    <span class="info-page-hero__badge">
                        <i class="fa-solid fa-lock"></i>
                        GDPR Compliant
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         MAIN CONTENT LAYOUT
    ════════════════════════════════════════════════ -->
    <div class="wrapper info-page-wrapper">
        <div class="info-page-layout">

            <!-- ── Sidebar TOC ───────────────────────── -->
            <aside class="info-page-toc" id="infoPageToc">
                <div class="info-toc__inner">
                    <div class="info-toc__header">
                        <i class="fa-solid fa-list-ul"></i>
                        <span>Mục lục</span>
                    </div>
                    <nav class="info-toc__nav" id="tocNav">
                        <a class="info-toc__link is-active" href="#section-1">1. Giới thiệu</a>
                        <a class="info-toc__link" href="#section-2">2. Thông tin chúng tôi thu thập</a>
                        <a class="info-toc__link" href="#section-3">3. Cách chúng tôi sử dụng thông tin</a>
                        <a class="info-toc__link" href="#section-4">4. Chia sẻ thông tin với bên thứ ba</a>
                        <a class="info-toc__link" href="#section-5">5. Bảo mật dữ liệu</a>
                        <a class="info-toc__link" href="#section-6">6. Cookie và công nghệ theo dõi</a>
                        <a class="info-toc__link" href="#section-7">7. Quyền của người dùng</a>
                        <a class="info-toc__link" href="#section-8">8. Lưu trữ dữ liệu</a>
                        <a class="info-toc__link" href="#section-9">9. Thay đổi chính sách</a>
                        <a class="info-toc__link" href="#section-10">10. Liên hệ</a>
                    </nav>

                    <!-- Contact Card -->
                    <div class="info-toc__contact">
                        <p class="info-toc__contact-title">Cần hỗ trợ?</p>
                        <a href="mailto:<?php echo esc_attr($company_email); ?>" class="info-toc__contact-link">
                            <i class="fa-solid fa-envelope"></i>
                            <?php echo esc_html($company_email); ?>
                        </a>
                        <a href="#" class="info-toc__contact-link">
                            <i class="fa-brands fa-telegram"></i>
                            Telegram: @VieProxyVN
                        </a>
                    </div>
                </div>
            </aside>

            <!-- ── Main Article ──────────────────────── -->
            <article class="info-page-content" id="infoPageContent">

                <!-- Section 1 -->
                <section class="info-section" id="section-1">
                    <div class="info-section__heading">
                        <div class="info-section__num">01</div>
                        <h2>Giới thiệu</h2>
                    </div>
                    <p>
                        Chào mừng bạn đến với <strong><?php echo esc_html($company_name); ?></strong>. Chúng tôi tôn
                        trọng quyền riêng tư của bạn
                        và cam kết bảo vệ thông tin cá nhân mà bạn chia sẻ khi sử dụng dịch vụ proxy của chúng tôi.
                    </p>
                    <p>
                        Chính sách bảo mật này mô tả loại thông tin chúng tôi thu thập, cách chúng tôi sử dụng,
                        lưu trữ và bảo vệ thông tin đó, cũng như các quyền của bạn liên quan đến dữ liệu cá nhân.
                        Bằng việc truy cập và sử dụng dịch vụ <?php echo esc_html($company_name); ?>, bạn đồng ý với các
                        điều khoản
                        được nêu trong chính sách này.
                    </p>
                    <div class="info-highlight-box info-highlight-box--blue">
                        <i class="fa-solid fa-circle-info"></i>
                        <p>Chính sách này áp dụng cho tất cả người dùng dịch vụ <?php echo esc_html($company_name); ?>,
                            bao gồm
                            website, API, ứng dụng và các dịch vụ liên quan khác.</p>
                    </div>
                </section>

                <!-- Section 2 -->
                <section class="info-section" id="section-2">
                    <div class="info-section__heading">
                        <div class="info-section__num">02</div>
                        <h2>Thông tin chúng tôi thu thập</h2>
                    </div>
                    <p>Chúng tôi có thể thu thập các loại thông tin sau đây:</p>

                    <h3>2.1. Thông tin bạn cung cấp trực tiếp</h3>
                    <ul class="info-list">
                        <li><strong>Thông tin tài khoản:</strong> Họ tên, địa chỉ email, mật khẩu (được mã hóa) khi bạn
                            đăng ký tài khoản.</li>
                        <li><strong>Thông tin thanh toán:</strong> Thông tin giao dịch, lịch sử mua hàng. Chúng tôi
                            không lưu trữ thông tin thẻ tín dụng trực tiếp.</li>
                        <li><strong>Thông tin liên lạc:</strong> Email, số điện thoại, tài khoản mạng xã hội khi bạn
                            liên hệ hỗ trợ.</li>
                    </ul>

                    <h3>2.2. Thông tin thu thập tự động</h3>
                    <ul class="info-list">
                        <li><strong>Dữ liệu sử dụng:</strong> Địa chỉ IP, loại trình duyệt, hệ điều hành, thời gian truy
                            cập, các trang đã xem.</li>
                        <li><strong>Dữ liệu kết nối proxy:</strong> Thông tin lưu lượng kết nối để đảm bảo dịch vụ hoạt
                            động ổn định và ngăn chặn lạm dụng.</li>
                        <li><strong>Cookie và dữ liệu theo dõi:</strong> Thông tin phiên đăng nhập, tùy chọn cá nhân
                            hóa.</li>
                    </ul>

                    <div class="info-highlight-box info-highlight-box--green">
                        <i class="fa-solid fa-shield-check"></i>
                        <p><strong><?php echo esc_html($company_name); ?> không ghi lại nội dung lưu lượng
                                proxy</strong> — chúng tôi chỉ ghi lại
                            metadata kết nối (thời gian, lượng dữ liệu sử dụng) cần thiết cho việc quản lý dịch vụ.</p>
                    </div>
                </section>

                <!-- Section 3 -->
                <section class="info-section" id="section-3">
                    <div class="info-section__heading">
                        <div class="info-section__num">03</div>
                        <h2>Cách chúng tôi sử dụng thông tin</h2>
                    </div>
                    <p>Thông tin thu thập được sử dụng cho các mục đích sau:</p>

                    <div class="info-cards-grid">
                        <div class="info-card">
                            <div class="info-card__icon"><i class="fa-solid fa-server"></i></div>
                            <h4>Cung cấp dịch vụ</h4>
                            <p>Xử lý đơn hàng, kích hoạt proxy, quản lý tài khoản và giao hàng dịch vụ đúng hạn.</p>
                        </div>
                        <div class="info-card">
                            <div class="info-card__icon"><i class="fa-solid fa-headset"></i></div>
                            <h4>Hỗ trợ khách hàng</h4>
                            <p>Phản hồi yêu cầu hỗ trợ, giải quyết tranh chấp và cải thiện trải nghiệm người dùng.</p>
                        </div>
                        <div class="info-card">
                            <div class="info-card__icon"><i class="fa-solid fa-chart-line"></i></div>
                            <h4>Cải thiện dịch vụ</h4>
                            <p>Phân tích dữ liệu sử dụng để tối ưu hóa hiệu suất, phát triển tính năng mới.</p>
                        </div>
                        <div class="info-card">
                            <div class="info-card__icon"><i class="fa-solid fa-bell"></i></div>
                            <h4>Thông báo & Marketing</h4>
                            <p>Gửi thông báo dịch vụ, khuyến mãi (bạn có thể hủy đăng ký bất kỳ lúc nào).</p>
                        </div>
                        <div class="info-card">
                            <div class="info-card__icon"><i class="fa-solid fa-shield-halved"></i></div>
                            <h4>Bảo mật & Phòng chống gian lận</h4>
                            <p>Phát hiện và ngăn chặn hoạt động gian lận, bảo vệ hệ thống và người dùng khác.</p>
                        </div>
                        <div class="info-card">
                            <div class="info-card__icon"><i class="fa-solid fa-scale-balanced"></i></div>
                            <h4>Tuân thủ pháp lý</h4>
                            <p>Đáp ứng các nghĩa vụ pháp lý, yêu cầu của cơ quan nhà nước có thẩm quyền.</p>
                        </div>
                    </div>
                </section>

                <!-- Section 4 -->
                <section class="info-section" id="section-4">
                    <div class="info-section__heading">
                        <div class="info-section__num">04</div>
                        <h2>Chia sẻ thông tin với bên thứ ba</h2>
                    </div>
                    <p>
                        <?php echo esc_html($company_name); ?> <strong>không bán, không cho thuê</strong> thông tin cá
                        nhân của bạn cho bên thứ ba
                        vì mục đích thương mại. Chúng tôi có thể chia sẻ thông tin trong các trường hợp sau:
                    </p>
                    <ul class="info-list">
                        <li>
                            <strong>Đối tác cung cấp dịch vụ:</strong> Các nhà cung cấp dịch vụ thanh toán, dịch vụ lưu
                            trữ,
                            phân tích dữ liệu hoạt động theo hướng dẫn của chúng tôi và có nghĩa vụ bảo mật thông tin.
                        </li>
                        <li>
                            <strong>Yêu cầu pháp lý:</strong> Khi được yêu cầu bởi pháp luật, lệnh tòa án hoặc cơ quan
                            nhà nước
                            có thẩm quyền tại Việt Nam.
                        </li>
                        <li>
                            <strong>Bảo vệ quyền lợi:</strong> Khi cần thiết để bảo vệ quyền lợi, tài sản hoặc sự an
                            toàn của
                            <?php echo esc_html($company_name); ?>, người dùng hoặc cộng đồng.
                        </li>
                        <li>
                            <strong>Chuyển nhượng kinh doanh:</strong> Trong trường hợp mua lại, sáp nhập hoặc bán tài
                            sản,
                            thông tin người dùng có thể được chuyển giao cùng với thông báo trước.
                        </li>
                    </ul>
                    <div class="info-highlight-box info-highlight-box--yellow">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <p>Chúng tôi yêu cầu tất cả đối tác bên thứ ba duy trì mức độ bảo mật dữ liệu tương đương với
                            tiêu chuẩn của <?php echo esc_html($company_name); ?>.</p>
                    </div>
                </section>

                <!-- Section 5 -->
                <section class="info-section" id="section-5">
                    <div class="info-section__heading">
                        <div class="info-section__num">05</div>
                        <h2>Bảo mật dữ liệu</h2>
                    </div>
                    <p>
                        Chúng tôi thực hiện các biện pháp kỹ thuật và tổ chức phù hợp để bảo vệ thông tin cá nhân của
                        bạn
                        khỏi truy cập trái phép, mất mát, tiết lộ hoặc phá hủy:
                    </p>
                    <ul class="info-list">
                        <li><strong>Mã hóa SSL/TLS:</strong> Tất cả dữ liệu truyền qua kết nối HTTPS được mã hóa.</li>
                        <li><strong>Mã hóa mật khẩu:</strong> Mật khẩu được lưu trữ dưới dạng hash bcrypt một chiều,
                            không thể giải mã ngược.</li>
                        <li><strong>Kiểm soát truy cập:</strong> Chỉ nhân viên được ủy quyền mới có quyền truy cập dữ
                            liệu người dùng.</li>
                        <li><strong>Giám sát hệ thống:</strong> Hệ thống theo dõi 24/7 để phát hiện và ứng phó với các
                            mối đe dọa bảo mật.</li>
                        <li><strong>Sao lưu định kỳ:</strong> Dữ liệu được sao lưu thường xuyên để đảm bảo khả năng phục
                            hồi.</li>
                    </ul>
                    <p>
                        Dù vậy, không có phương thức truyền qua internet hay lưu trữ điện tử nào là 100% an toàn.
                        Nếu bạn phát hiện bất kỳ lỗ hổng bảo mật nào, vui lòng liên hệ ngay với chúng tôi qua
                        <a
                            href="mailto:<?php echo esc_attr($company_email); ?>"><?php echo esc_html($company_email); ?></a>.
                    </p>
                </section>

                <!-- Section 6 -->
                <section class="info-section" id="section-6">
                    <div class="info-section__heading">
                        <div class="info-section__num">06</div>
                        <h2>Cookie và công nghệ theo dõi</h2>
                    </div>
                    <p>
                        Chúng tôi sử dụng cookie và các công nghệ theo dõi tương tự để cải thiện trải nghiệm của bạn:
                    </p>

                    <div class="info-table-wrap">
                        <table class="info-table">
                            <thead>
                                <tr>
                                    <th>Loại Cookie</th>
                                    <th>Mục đích</th>
                                    <th>Thời hạn</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="info-badge info-badge--blue">Thiết yếu</span></td>
                                    <td>Duy trì phiên đăng nhập, bảo mật CSRF</td>
                                    <td>Phiên / 30 ngày</td>
                                </tr>
                                <tr>
                                    <td><span class="info-badge info-badge--green">Chức năng</span></td>
                                    <td>Ghi nhớ tùy chọn ngôn ngữ, giao diện</td>
                                    <td>1 năm</td>
                                </tr>
                                <tr>
                                    <td><span class="info-badge info-badge--yellow">Phân tích</span></td>
                                    <td>Google Analytics — thống kê lưu lượng truy cập ẩn danh</td>
                                    <td>2 năm</td>
                                </tr>
                                <tr>
                                    <td><span class="info-badge info-badge--gray">Marketing</span></td>
                                    <td>Cá nhân hóa quảng cáo (chỉ khi bạn đồng ý)</td>
                                    <td>90 ngày</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p>
                        Bạn có thể kiểm soát hoặc xóa cookie thông qua cài đặt trình duyệt của mình. Lưu ý rằng
                        việc tắt cookie thiết yếu có thể ảnh hưởng đến chức năng đăng nhập và sử dụng dịch vụ.
                    </p>
                </section>

                <!-- Section 7 -->
                <section class="info-section" id="section-7">
                    <div class="info-section__heading">
                        <div class="info-section__num">07</div>
                        <h2>Quyền của người dùng</h2>
                    </div>
                    <p>Bạn có các quyền sau đây đối với dữ liệu cá nhân của mình:</p>
                    <ul class="info-list info-list--check">
                        <li><strong>Quyền truy cập:</strong> Yêu cầu bản sao dữ liệu cá nhân mà chúng tôi đang lưu trữ
                            về bạn.</li>
                        <li><strong>Quyền chỉnh sửa:</strong> Yêu cầu cập nhật hoặc sửa đổi thông tin không chính xác.
                        </li>
                        <li><strong>Quyền xóa:</strong> Yêu cầu xóa dữ liệu cá nhân ("quyền được lãng quên"), trừ các
                            trường hợp pháp lý yêu cầu lưu trữ.</li>
                        <li><strong>Quyền hạn chế xử lý:</strong> Yêu cầu hạn chế cách chúng tôi xử lý dữ liệu của bạn
                            trong một số trường hợp nhất định.</li>
                        <li><strong>Quyền phản đối:</strong> Phản đối việc xử lý dữ liệu cho mục đích marketing trực
                            tiếp.</li>
                        <li><strong>Quyền di chuyển dữ liệu:</strong> Nhận dữ liệu của bạn ở định dạng có thể đọc được
                            bằng máy để chuyển sang dịch vụ khác.</li>
                        <li><strong>Quyền hủy đăng ký email:</strong> Hủy nhận email marketing bất kỳ lúc nào qua link
                            unsubscribe trong email.</li>
                    </ul>
                    <p>
                        Để thực hiện bất kỳ quyền nào ở trên, vui lòng liên hệ chúng tôi qua
                        <a
                            href="mailto:<?php echo esc_attr($company_email); ?>"><?php echo esc_html($company_email); ?></a>.
                        Chúng tôi sẽ phản hồi trong vòng <strong>30 ngày làm việc</strong>.
                    </p>
                </section>

                <!-- Section 8 -->
                <section class="info-section" id="section-8">
                    <div class="info-section__heading">
                        <div class="info-section__num">08</div>
                        <h2>Lưu trữ dữ liệu</h2>
                    </div>
                    <p>
                        Chúng tôi chỉ lưu trữ dữ liệu cá nhân của bạn trong thời gian cần thiết để cung cấp dịch vụ
                        hoặc theo yêu cầu pháp lý:
                    </p>
                    <ul class="info-list">
                        <li><strong>Dữ liệu tài khoản:</strong> Được lưu trữ trong suốt thời gian tài khoản còn hoạt
                            động và tối đa 3 năm sau khi hủy tài khoản.</li>
                        <li><strong>Dữ liệu giao dịch:</strong> Được lưu trữ tối thiểu 5 năm theo quy định kế toán tài
                            chính Việt Nam.</li>
                        <li><strong>Nhật ký kết nối:</strong> Được xóa tự động sau 90 ngày.</li>
                        <li><strong>Cookie phân tích:</strong> Tối đa 2 năm.</li>
                    </ul>
                    <p>
                        Dữ liệu của bạn được lưu trữ trên máy chủ đặt tại Việt Nam và/hoặc Singapore với các tiêu chuẩn
                        bảo mật đạt chuẩn quốc tế.
                    </p>
                </section>

                <!-- Section 9 -->
                <section class="info-section" id="section-9">
                    <div class="info-section__heading">
                        <div class="info-section__num">09</div>
                        <h2>Thay đổi chính sách</h2>
                    </div>
                    <p>
                        <?php echo esc_html($company_name); ?> có thể cập nhật chính sách bảo mật này theo thời gian để
                        phản ánh những
                        thay đổi trong thực tiễn hoặc yêu cầu pháp lý. Khi có thay đổi đáng kể, chúng tôi sẽ:
                    </p>
                    <ul class="info-list">
                        <li>Thông báo qua email đến địa chỉ đã đăng ký của bạn.</li>
                        <li>Hiển thị thông báo nổi bật trên website.</li>
                        <li>Cập nhật ngày "Cập nhật lần cuối" ở đầu trang này.</li>
                    </ul>
                    <p>
                        Việc tiếp tục sử dụng dịch vụ sau khi chính sách được cập nhật đồng nghĩa với việc bạn
                        chấp nhận phiên bản mới. Nếu không đồng ý, bạn có quyền yêu cầu xóa tài khoản.
                    </p>
                </section>

                <!-- Section 10 -->
                <section class="info-section" id="section-10">
                    <div class="info-section__heading">
                        <div class="info-section__num">10</div>
                        <h2>Liên hệ</h2>
                    </div>
                    <p>
                        Nếu bạn có bất kỳ câu hỏi, thắc mắc hoặc yêu cầu nào liên quan đến chính sách bảo mật
                        hoặc cách chúng tôi xử lý dữ liệu cá nhân của bạn, vui lòng liên hệ:
                    </p>

                    <div class="info-contact-grid">
                        <a href="mailto:<?php echo esc_attr($company_email); ?>" class="info-contact-card">
                            <div class="info-contact-card__icon">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <div class="info-contact-card__body">
                                <strong>Email hỗ trợ</strong>
                                <span><?php echo esc_html($company_email); ?></span>
                            </div>
                        </a>
                        <a href="#" class="info-contact-card">
                            <div class="info-contact-card__icon info-contact-card__icon--tg">
                                <i class="fa-brands fa-telegram"></i>
                            </div>
                            <div class="info-contact-card__body">
                                <strong>Telegram</strong>
                                <span>@VieProxyVN</span>
                            </div>
                        </a>
                        <a href="#" class="info-contact-card">
                            <div class="info-contact-card__icon info-contact-card__icon--zalo">
                                <i class="fa-solid fa-comment-dots"></i>
                            </div>
                            <div class="info-contact-card__body">
                                <strong>Zalo</strong>
                                <span><?php echo esc_html($company_zalo); ?></span>
                            </div>
                        </a>
                    </div>

                    <p style="margin-top: 24px;">
                        Thời gian phản hồi: <strong>Trong vòng 24 giờ làm việc</strong> (Thứ Hai – Thứ Bảy, 8:00 – 22:00
                        GMT+7).
                    </p>
                </section>

                <!-- Page Footer Nav -->
                <div class="info-page-nav">
                    <a href="<?php echo esc_url(home_url('/')); ?>"
                        class="info-page-nav__btn info-page-nav__btn--back">
                        <i class="fa-solid fa-arrow-left"></i>
                        Về trang chủ
                    </a>
                    <a href="<?php echo esc_url(home_url('/dieu-khoan-dich-vu/')); ?>"
                        class="info-page-nav__btn info-page-nav__btn--next">
                        Điều khoản dịch vụ
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

            </article>
        </div>
    </div>

</main>

<!-- Back to Top Button -->
<button class="info-back-to-top" id="backToTop" aria-label="Về đầu trang">
    <i class="fa-solid fa-chevron-up"></i>
</button>

<?php get_footer(); ?>