<?php
/**
 * Template Name: Về Chúng Tôi
 * Description: Template trang Về Chúng Tôi của VieProxy
 */

get_header();

$company_name = 'VieProxy';
$company_email = 'support@vieproxy.vn';
$company_zalo = '034.770.0437';
$founded_year = '2022';
?>

<main class="main-info-page main-about-page">

    <!-- ══════════════════════════════════════════
         HERO
    ══════════════════════════════════════════ -->
    <section class="info-page-hero info-page-hero--about">
        <div class="wrapper">

            <nav class="info-page-hero__breadcrumb" aria-label="breadcrumb">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="breadcrumb-link">Trang chủ</a>
                <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
                <span class="breadcrumb-current">Về Chúng Tôi</span>
            </nav>

            <div class="info-page-hero__inner">
                <div class="info-page-hero__content">
                    <h1 class="info-page-hero__title">Về <?php echo esc_html($company_name); ?></h1>
                    <p class="info-page-hero__subtitle">
                        Chúng tôi là đội ngũ đam mê công nghệ, sứ mệnh mang đến giải pháp proxy
                        chất lượng cao, ổn định và giá cả hợp lý cho mọi nhu cầu kinh doanh tại Việt Nam.
                    </p>
                    <div class="info-page-hero__meta">
                        <span class="info-page-hero__badge">
                            <i class="fa-solid fa-flag"></i>
                            Thành lập năm <?php echo esc_html($founded_year); ?>
                        </span>
                        <span class="info-page-hero__badge">
                            <i class="fa-solid fa-location-dot"></i>
                            Việt Nam
                        </span>
                        <span class="info-page-hero__badge">
                            <i class="fa-solid fa-earth-asia"></i>
                            190+ Quốc gia
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- ══════════════════════════════════════════
         MAIN CONTENT LAYOUT
    ══════════════════════════════════════════ -->
    <div class="wrapper info-page-wrapper">
        <div class="info-page-layout">

            <!-- ── Sidebar TOC ── -->
            <aside class="info-page-toc" id="infoPageToc">
                <div class="info-toc__inner">
                    <div class="info-toc__header">
                        <i class="fa-solid fa-list-ul"></i>
                        <span>Mục lục</span>
                    </div>
                    <nav class="info-toc__nav" id="tocNav">
                        <a class="info-toc__link is-active" href="#section-1">1. Câu chuyện của chúng tôi</a>
                        <a class="info-toc__link" href="#section-2">2. Sứ mệnh & Tầm nhìn</a>
                        <a class="info-toc__link" href="#section-3">3. Dịch vụ nổi bật</a>
                        <a class="info-toc__link" href="#section-4">4. Cam kết chất lượng</a>
                        <a class="info-toc__link" href="#section-5">5. Công nghệ & Hạ tầng</a>
                        <a class="info-toc__link" href="#section-6">6. Đội ngũ & Văn hóa</a>
                        <a class="info-toc__link" href="#section-7">7. Liên hệ</a>
                    </nav>

                    <div class="info-toc__contact">
                        <p class="info-toc__contact-title">Liên hệ ngay</p>
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

            <!-- ── Mobile TOC ── -->
            <div class="info-toc-mobile" id="infoTocMobile">
                <button class="info-toc-mobile__toggle" id="infoTocToggle" aria-expanded="false"
                    aria-controls="infoTocMobileContent">
                    <span class="info-toc-mobile__toggle-left">
                        <i class="fa-solid fa-list-ul"></i>
                        Mục lục
                    </span>
                    <i class="fa-solid fa-chevron-down info-toc-mobile__arrow"></i>
                </button>
                <div class="info-toc-mobile__content" id="infoTocMobileContent"></div>
            </div>

            <!-- ── Main Article ── -->
            <article class="info-page-content" id="infoPageContent">

                <!-- Section 1: Câu chuyện -->
                <section class="info-section" id="section-1">
                    <div class="info-section__heading">
                        <div class="info-section__num">01</div>
                        <h2>Câu chuyện của chúng tôi</h2>
                    </div>

                    <p>
                        <strong><?php echo esc_html($company_name); ?></strong> được thành lập năm
                        <?php echo esc_html($founded_year); ?>
                        bởi những người trẻ đam mê công nghệ, với mong muốn mang đến giải pháp proxy
                        chất lượng cao, minh bạch và giá cả phải chăng cho thị trường Việt Nam.
                    </p>
                    <p>
                        Xuất phát điểm từ nhu cầu thực tế của chính chúng tôi — những developer, marketer
                        và doanh nhân kỹ thuật số gặp khó khăn khi tìm kiếm dịch vụ proxy uy tín với mức
                        giá hợp lý — chúng tôi quyết định tự xây dựng hạ tầng và cung cấp dịch vụ trực tiếp
                        đến người dùng, loại bỏ tầng trung gian để tối ưu chi phí.
                    </p>
                </section>

                <!-- Section 2: Sứ mệnh & Tầm nhìn -->
                <section class="info-section" id="section-2">
                    <div class="info-section__heading">
                        <div class="info-section__num">02</div>
                        <h2>Sứ mệnh &amp; Tầm nhìn</h2>
                    </div>

                    <!-- ── Đã bỏ --blue / --light, dùng style đồng nhất no-icon ── -->
                    <div class="about-mission-grid">
                        <div class="about-mission-card">
                            <h3>Sứ mệnh</h3>
                            <p>
                                Democratize quyền truy cập Internet — mang đến cho mọi doanh nghiệp,
                                cá nhân tại Việt Nam khả năng truy cập thông tin toàn cầu một cách
                                an toàn, ổn định và chi phí tối ưu thông qua hạ tầng proxy đẳng cấp quốc tế.
                            </p>
                        </div>
                        <div class="about-mission-card">
                            <h3>Tầm nhìn</h3>
                            <p>
                                Trở thành nền tảng proxy số 1 Đông Nam Á vào năm 2027 — được lựa chọn
                                bởi hàng trăm nghìn doanh nghiệp nhờ độ tin cậy, minh bạch và
                                chất lượng dịch vụ vượt trội.
                            </p>
                        </div>
                    </div>

                    <h3>Giá trị cốt lõi</h3>
                    <ul class="info-list info-list--check">
                        <li><strong>Minh bạch:</strong> Không phí ẩn, không điều khoản khó hiểu — mọi thứ rõ ràng từ
                            đầu.</li>
                        <li><strong>Đáng tin cậy:</strong> Cam kết SLA 99% uptime, hỗ trợ 24/7 và hoàn tiền khi không
                            đạt tiêu chuẩn.</li>
                        <li><strong>Sáng tạo:</strong> Liên tục cải tiến công nghệ, lắng nghe phản hồi khách hàng để
                            phát triển sản phẩm tốt hơn.</li>
                        <li><strong>Cộng đồng:</strong> Xây dựng hệ sinh thái lành mạnh, hỗ trợ cộng đồng developer và
                            doanh nghiệp Việt Nam.</li>
                    </ul>
                </section>

                <!-- Section 3: Dịch vụ nổi bật -->
                <section class="info-section" id="section-3">
                    <div class="info-section__heading">
                        <div class="info-section__num">03</div>
                        <h2>Dịch vụ nổi bật</h2>
                    </div>
                    <p>
                        <?php echo esc_html($company_name); ?> cung cấp đa dạng loại proxy đáp ứng
                        mọi nhu cầu từ cá nhân đến doanh nghiệp lớn:
                    </p>

                    <!-- ── Đã bỏ icon, dùng --no-icon style ── -->
                    <div class="info-cards-grid info-cards-grid--no-icon">
                        <div class="info-card">
                            <h4>Proxy IPv4 / IPv6</h4>
                            <p>IP tĩnh tốc độ cao, hỗ trợ HTTP và SOCKS5. Lý tưởng cho automation và scraping.</p>
                        </div>
                        <div class="info-card">
                            <h4>Mobile Proxy</h4>
                            <p>IP từ mạng di động thực tế, luân phiên linh hoạt. Vượt qua mọi cơ chế chặn.</p>
                        </div>
                        <div class="info-card">
                            <h4>Residential Proxy</h4>
                            <p>IP dân cư xác thực từ 190+ quốc gia. Tỷ lệ thành công cao nhất thị trường.</p>
                        </div>
                        <div class="info-card">
                            <h4>Rotating Proxy</h4>
                            <p>Tự động xoay IP theo thời gian hoặc request, phù hợp cho data collection quy mô lớn.</p>
                        </div>
                        <div class="info-card">
                            <h4>API Proxy</h4>
                            <p>Tích hợp trực tiếp vào ứng dụng qua REST API. Tài liệu đầy đủ và SDK đa ngôn ngữ.</p>
                        </div>
                        <div class="info-card">
                            <h4>Dịch vụ tư vấn</h4>
                            <p>Tư vấn chọn loại proxy phù hợp và tối ưu chi phí theo nhu cầu cụ thể của bạn.</p>
                        </div>
                    </div>
                </section>

                <!-- Section 4: Cam kết -->
                <section class="info-section" id="section-4">
                    <div class="info-section__heading">
                        <div class="info-section__num">04</div>
                        <h2>Cam kết chất lượng</h2>
                    </div>
                    <p>
                        Chúng tôi không chỉ bán dịch vụ — chúng tôi cam kết kết quả thực sự.
                        Đây là những tiêu chuẩn chất lượng mà <?php echo esc_html($company_name); ?> tuân thủ nghiêm
                        ngặt:
                    </p>

                    <ul class="info-list info-list--check">
                        <li><strong>SLA 99% Uptime:</strong> Downtime theo kế hoạch được thông báo trước 24 giờ. Vượt
                            quá SLA → hoàn tiền theo tỷ lệ thực tế.</li>
                        <li><strong>Tốc độ cao lên đến 1Gbps:</strong> Hạ tầng datacenter chuẩn quốc tế, đường truyền
                            quốc tế tối ưu cho proxy Việt Nam và toàn cầu.</li>
                        <li><strong>Thay thế IP lỗi trong 24 giờ:</strong> IP bị block hoặc không hoạt động sẽ được thay
                            thế miễn phí, không cần giải thích lý do.</li>
                        <li><strong>Hỗ trợ 24/7 trong 30 phút:</strong> Thời gian phản hồi trung bình dưới 30 phút qua
                            Telegram và email, kể cả cuối tuần.</li>
                        <li><strong>Hoàn tiền 100% trong 72 giờ đầu:</strong> Nếu dịch vụ không đáp ứng kỳ vọng, chúng
                            tôi hoàn tiền toàn bộ — không câu hỏi, không rắc rối.</li>
                    </ul>
                </section>

                <!-- Section 5: Công nghệ & Hạ tầng -->
                <section class="info-section" id="section-5">
                    <div class="info-section__heading">
                        <div class="info-section__num">05</div>
                        <h2>Công nghệ &amp; Hạ tầng</h2>
                    </div>
                    <p>
                        Chúng tôi đầu tư nghiêm túc vào hạ tầng kỹ thuật để đảm bảo
                        hiệu suất và độ tin cậy cao nhất:
                    </p>

                    <!-- ── Đã bỏ icon, chỉ giữ content div ── -->
                    <div class="about-tech-grid">
                        <div class="about-tech-item">
                            <div>
                                <strong>Máy chủ đặt tại VN &amp; SG</strong>
                                <span>Latency thấp, tốc độ cao cho khu vực Đông Nam Á</span>
                            </div>
                        </div>
                        <div class="about-tech-item">
                            <div>
                                <strong>Mã hóa SSL/TLS end-to-end</strong>
                                <span>Bảo mật toàn bộ lưu lượng kết nối</span>
                            </div>
                        </div>
                        <div class="about-tech-item">
                            <div>
                                <strong>Giám sát real-time 24/7</strong>
                                <span>Phát hiện và xử lý sự cố tự động trong vài giây</span>
                            </div>
                        </div>
                        <div class="about-tech-item">
                            <div>
                                <strong>Sao lưu dữ liệu đa vùng</strong>
                                <span>Đảm bảo không mất dữ liệu với backup theo lịch</span>
                            </div>
                        </div>
                        <div class="about-tech-item">
                            <div>
                                <strong>API RESTful chuẩn quốc tế</strong>
                                <span>Tài liệu đầy đủ, SDK cho Python, Node.js, PHP</span>
                            </div>
                        </div>
                        <div class="about-tech-item">
                            <div>
                                <strong>BGP Anycast routing</strong>
                                <span>Định tuyến thông minh, tự động chọn đường truyền tối ưu</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 6: Đội ngũ & Văn hóa -->
                <section class="info-section" id="section-6">
                    <div class="info-section__heading">
                        <div class="info-section__num">06</div>
                        <h2>Đội ngũ &amp; Văn hóa</h2>
                    </div>
                    <p>
                        <?php echo esc_html($company_name); ?> được xây dựng bởi đội ngũ nhỏ nhưng đầy
                        năng lực — những kỹ sư, designer và chuyên gia kinh doanh cùng chung một niềm đam mê:
                        làm cho Internet trở nên dễ tiếp cận hơn.
                    </p>

                    <!-- ── Đã bỏ emoji div ── -->
                    <div class="about-culture-cards">
                        <div class="about-culture-card">
                            <h4>Tốc độ &amp; Hiệu quả</h4>
                            <p>Chúng tôi làm việc nhanh, quyết định nhanh và triển khai nhanh. Không quan liêu, không
                                chờ đợi.</p>
                        </div>
                        <div class="about-culture-card">
                            <h4>Khách hàng là trung tâm</h4>
                            <p>Mọi quyết định sản phẩm đều bắt đầu từ câu hỏi: "Điều này có thực sự giúp ích cho khách
                                hàng không?"</p>
                        </div>
                        <div class="about-culture-card">
                            <h4>Học hỏi liên tục</h4>
                            <p>Công nghệ thay đổi mỗi ngày, chúng tôi cũng vậy. Cập nhật, thích nghi và không ngừng cải
                                thiện.</p>
                        </div>
                        <div class="about-culture-card">
                            <h4>Tư duy toàn cầu</h4>
                            <p>Sản phẩm Việt Nam, tiêu chuẩn quốc tế. Chúng tôi tự hào là cầu nối cho doanh nghiệp Việt
                                ra thế giới.</p>
                        </div>
                    </div>
                </section>

                <!-- Section 7: Liên hệ -->
                <section class="info-section" id="section-7">
                    <div class="info-section__heading">
                        <div class="info-section__num">07</div>
                        <h2>Liên hệ với chúng tôi</h2>
                    </div>
                    <p>
                        Dù bạn là khách hàng mới muốn tìm hiểu, đối tác muốn hợp tác,
                        hay chỉ đơn giản là muốn chat — chúng tôi luôn sẵn lòng lắng nghe.
                    </p>

                    <!-- ── Thêm Facebook, 4 cards giống trang chính sách ── -->
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
                        <a href="https://fb.com/VieProxyVN" target="_blank" rel="noopener noreferrer"
                            class="info-contact-card">
                            <div class="info-contact-card__icon info-contact-card__icon--fb">
                                <i class="fa-brands fa-facebook-f"></i>
                            </div>
                            <div class="info-contact-card__body">
                                <strong>Facebook</strong>
                                <span>VieProxyVN</span>
                            </div>
                        </a>
                    </div>

                    <p style="margin-top: 24px;">
                        Thời gian phản hồi: <strong>Trong vòng 30 phút</strong> (Thứ Hai – Thứ Bảy, 8:00 – 22:00 GMT+7).
                    </p>
                </section>

                <!-- Page Footer Nav -->
                <div class="info-page-nav">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="info-page-nav__btn info-page-nav__btn--back">
                        <i class="fa-solid fa-arrow-left"></i>
                        Về trang chủ
                    </a>
                    <a href="<?php echo esc_url(home_url('/chinh-sach-bao-mat/')); ?>"
                        class="info-page-nav__btn info-page-nav__btn--next">
                        Chính sách bảo mật
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

            </article>
        </div>
    </div>

</main>

<?php get_footer(); ?>