<?php
/**
 * Template Name: Điều Khoản Dịch Vụ
 * Description: Template trang Điều Khoản Dịch Vụ của VieProxy
 */

get_header();

$last_updated = '01/02/2025';
$company_name = 'VieProxy';
$company_email = 'support@vieproxy.vn';
$company_zalo = '034.770.0437';
?>

<main class="main-info-page">


    <section class="info-page-hero info-page-hero--terms">
        <div class="wrapper">

            <!-- Breadcrumb — inside hero -->
            <nav class="info-page-hero__breadcrumb" aria-label="breadcrumb">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="breadcrumb-link">Trang chủ</a>
                <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
                <span class="breadcrumb-current">Điều Khoản Dịch Vụ</span>
            </nav>

            <!-- Hero body -->
            <div class="info-page-hero__inner">
                <!-- <div class="info-page-hero__icon">
                    <i class="fa-solid fa-file-contract"></i>
                </div> -->

                <div class="info-page-hero__content">
                    <h1 class="info-page-hero__title">Điều Khoản Dịch Vụ</h1>
                    <p class="info-page-hero__subtitle">
                        Vui lòng đọc kỹ các điều khoản và điều kiện này trước khi sử dụng dịch vụ proxy của
                        <?php echo esc_html($company_name); ?>. Việc sử dụng dịch vụ đồng nghĩa với việc bạn chấp nhận
                        toàn
                        bộ điều khoản.
                    </p>
                    <div class="info-page-hero__meta">
                        <span class="info-page-hero__badge">
                            <i class="fa-regular fa-calendar"></i>
                            Cập nhật lần cuối: <?php echo esc_html($last_updated); ?>
                        </span>
                        <span class="info-page-hero__badge">
                            <i class="fa-solid fa-scale-balanced"></i>
                            Áp dụng theo pháp luật Việt Nam
                        </span>
                    </div>
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
                        <a class="info-toc__link is-active" href="#section-1">1. Chấp nhận điều khoản</a>
                        <a class="info-toc__link" href="#section-2">2. Mô tả dịch vụ</a>
                        <a class="info-toc__link" href="#section-3">3. Đăng ký tài khoản</a>
                        <a class="info-toc__link" href="#section-4">4. Sử dụng được chấp nhận</a>
                        <a class="info-toc__link" href="#section-5">5. Thanh toán & Hoàn tiền</a>
                        <a class="info-toc__link" href="#section-6">6. Chất lượng dịch vụ (SLA)</a>
                        <a class="info-toc__link" href="#section-7">7. Sở hữu trí tuệ</a>
                        <a class="info-toc__link" href="#section-8">8. Giới hạn trách nhiệm</a>
                        <a class="info-toc__link" href="#section-9">9. Đình chỉ & Chấm dứt dịch vụ</a>
                        <a class="info-toc__link" href="#section-10">10. Thay đổi điều khoản</a>
                        <a class="info-toc__link" href="#section-11">11. Luật áp dụng</a>
                        <a class="info-toc__link" href="#section-12">12. Liên hệ</a>
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

                <!-- Disclaimer box -->
                <div class="info-highlight-box info-highlight-box--blue" style="margin-bottom: 40px;">
                    <i class="fa-solid fa-circle-info"></i>
                    <p>Bằng cách đăng ký hoặc sử dụng dịch vụ của
                        <strong><?php echo esc_html($company_name); ?></strong>, bạn xác nhận
                        rằng bạn đã đọc, hiểu và đồng ý bị ràng buộc bởi các Điều khoản Dịch vụ này.
                    </p>
                </div>

                <!-- Section 1 -->
                <section class="info-section" id="section-1">
                    <div class="info-section__heading">
                        <div class="info-section__num">01</div>
                        <h2>Chấp nhận điều khoản</h2>
                    </div>
                    <p>
                        Các Điều khoản Dịch vụ này ("Điều khoản") tạo thành một hợp đồng ràng buộc pháp lý giữa bạn
                        ("Người dùng") và <strong><?php echo esc_html($company_name); ?></strong> ("Chúng tôi", "Công
                        ty").
                    </p>
                    <p>Bạn phải đáp ứng đủ điều kiện để sử dụng dịch vụ:</p>
                    <ul class="info-list info-list--check">
                        <li>Từ đủ 18 tuổi hoặc có sự đồng ý của người giám hộ hợp pháp.</li>
                        <li>Có đủ năng lực pháp lý để ký kết hợp đồng tại quốc gia của bạn.</li>
                        <li>Không bị cấm sử dụng dịch vụ theo pháp luật hiện hành.</li>
                        <li>Cam kết cung cấp thông tin đăng ký trung thực và chính xác.</li>
                    </ul>
                </section>

                <!-- Section 2 -->
                <section class="info-section" id="section-2">
                    <div class="info-section__heading">
                        <div class="info-section__num">02</div>
                        <h2>Mô tả dịch vụ</h2>
                    </div>
                    <p>
                        <?php echo esc_html($company_name); ?> cung cấp các dịch vụ proxy bao gồm nhưng không giới hạn:
                    </p>

                    <div class="info-cards-grid info-cards-grid--3col">
                        <div class="info-card">
                            <div class="info-card__icon"><i class="fa-solid fa-network-wired"></i></div>
                            <h4>Proxy IPv4 / IPv6</h4>
                            <p>Địa chỉ IP tĩnh, băng thông cao, hỗ trợ HTTP và SOCKS5.</p>
                        </div>
                        <div class="info-card">
                            <div class="info-card__icon"><i class="fa-solid fa-mobile-screen"></i></div>
                            <h4>Mobile Proxy</h4>
                            <p>IP từ mạng di động thực tế, luân phiên linh hoạt.</p>
                        </div>
                        <div class="info-card">
                            <div class="info-card__icon"><i class="fa-solid fa-house-signal"></i></div>
                            <h4>Residential Proxy</h4>
                            <p>IP dân cư xác thực từ hơn 190 quốc gia trên toàn cầu.</p>
                        </div>
                    </div>

                    <p>
                        Dịch vụ được cung cấp "nguyên trạng" (as-is). Chúng tôi có quyền thay đổi, nâng cấp hoặc
                        ngừng cung cấp bất kỳ tính năng nào với thông báo trước hợp lý.
                    </p>
                </section>

                <!-- Section 3 -->
                <section class="info-section" id="section-3">
                    <div class="info-section__heading">
                        <div class="info-section__num">03</div>
                        <h2>Đăng ký tài khoản</h2>
                    </div>
                    <p>
                        Để sử dụng dịch vụ của <?php echo esc_html($company_name); ?>, bạn cần tạo tài khoản với các
                        điều kiện sau:
                    </p>
                    <ul class="info-list">
                        <li><strong>Thông tin chính xác:</strong> Bạn cam kết cung cấp thông tin đăng ký đầy đủ, trung
                            thực và cập nhật kịp thời khi có thay đổi.</li>
                        <li><strong>Bảo mật tài khoản:</strong> Bạn chịu trách nhiệm bảo mật thông tin đăng nhập và mọi
                            hoạt động xảy ra dưới tài khoản của bạn.</li>
                        <li><strong>Không chia sẻ:</strong> Nghiêm cấm chia sẻ thông tin đăng nhập với bên thứ ba hoặc
                            sử dụng chung tài khoản có tính phí.</li>
                        <li><strong>Một tài khoản:</strong> Mỗi người dùng chỉ được phép đăng ký một tài khoản. Việc tạo
                            nhiều tài khoản để lách các giới hạn dịch vụ bị nghiêm cấm.</li>
                        <li><strong>Thông báo xâm phạm:</strong> Nếu bạn nghi ngờ tài khoản bị xâm phạm, hãy thông báo
                            ngay cho chúng tôi.</li>
                    </ul>
                    <div class="info-highlight-box info-highlight-box--yellow">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <p><?php echo esc_html($company_name); ?> không chịu trách nhiệm đối với bất kỳ tổn thất nào
                            phát sinh từ việc bạn không bảo mật thông tin tài khoản.</p>
                    </div>
                </section>

                <!-- Section 4 -->
                <section class="info-section" id="section-4">
                    <div class="info-section__heading">
                        <div class="info-section__num">04</div>
                        <h2>Sử dụng được chấp nhận</h2>
                    </div>
                    <p>
                        Dịch vụ <?php echo esc_html($company_name); ?> được thiết kế cho các mục đích hợp pháp. Bạn đồng
                        ý
                        <strong>KHÔNG</strong> sử dụng dịch vụ để:
                    </p>

                    <div class="info-prohibited-list">
                        <div class="info-prohibited-item">
                            <i class="fa-solid fa-ban"></i>
                            <div>
                                <strong>Vi phạm pháp luật</strong>
                                <p>Thực hiện bất kỳ hoạt động nào vi phạm pháp luật Việt Nam hoặc pháp luật quốc tế.</p>
                            </div>
                        </div>
                        <div class="info-prohibited-item">
                            <i class="fa-solid fa-ban"></i>
                            <div>
                                <strong>Tấn công mạng</strong>
                                <p>DDoS, spam, brute force, khai thác lỗ hổng bảo mật, hoặc bất kỳ hình thức tấn công
                                    mạng nào.</p>
                            </div>
                        </div>
                        <div class="info-prohibited-item">
                            <i class="fa-solid fa-ban"></i>
                            <div>
                                <strong>Nội dung bất hợp pháp</strong>
                                <p>Truy cập, lưu trữ hoặc phân phối nội dung khiêu dâm trẻ em, tài liệu bạo lực hoặc nội
                                    dung vi phạm bản quyền.</p>
                            </div>
                        </div>
                        <div class="info-prohibited-item">
                            <i class="fa-solid fa-ban"></i>
                            <div>
                                <strong>Gian lận & Lừa đảo</strong>
                                <p>Lừa đảo tài chính, đánh cắp danh tính, gian lận quảng cáo hoặc các hoạt động lừa đảo
                                    khác.</p>
                            </div>
                        </div>
                        <div class="info-prohibited-item">
                            <i class="fa-solid fa-ban"></i>
                            <div>
                                <strong>Vi phạm điều khoản bên thứ ba</strong>
                                <p>Sử dụng dịch vụ vi phạm điều khoản của các nền tảng bên thứ ba một cách có hại hoặc
                                    gây thiệt hại cho họ.</p>
                            </div>
                        </div>
                        <div class="info-prohibited-item">
                            <i class="fa-solid fa-ban"></i>
                            <div>
                                <strong>Bán lại trái phép</strong>
                                <p>Bán lại, nhượng quyền hoặc cấp phép lại dịch vụ mà không có sự chấp thuận bằng văn
                                    bản của <?php echo esc_html($company_name); ?>.</p>
                            </div>
                        </div>
                    </div>

                    <p style="margin-top: 24px;">
                        Vi phạm các điều khoản sử dụng có thể dẫn đến đình chỉ hoặc chấm dứt tài khoản ngay lập tức
                        mà không hoàn tiền, và có thể bị truy cứu trách nhiệm pháp lý.
                    </p>
                </section>

                <!-- Section 5 -->
                <section class="info-section" id="section-5">
                    <div class="info-section__heading">
                        <div class="info-section__num">05</div>
                        <h2>Thanh toán &amp; Hoàn tiền</h2>
                    </div>

                    <h3>5.1. Thanh toán</h3>
                    <ul class="info-list">
                        <li>Tất cả giao dịch được tính bằng <strong>VND (Việt Nam Đồng)</strong> hoặc USD tùy gói dịch
                            vụ.</li>
                        <li>Chúng tôi chấp nhận thanh toán qua: Chuyển khoản ngân hàng, ví điện tử (MoMo, ZaloPay,
                            VNPay), và một số phương thức quốc tế.</li>
                        <li>Giá dịch vụ có thể thay đổi và sẽ được thông báo trước ít nhất 7 ngày.</li>
                        <li>Tất cả giao dịch được xử lý tự động sau khi xác nhận thanh toán thành công.</li>
                    </ul>

                    <h3>5.2. Chính sách hoàn tiền</h3>

                    <div class="info-table-wrap">
                        <table class="info-table">
                            <thead>
                                <tr>
                                    <th>Tình huống</th>
                                    <th>Điều kiện hoàn tiền</th>
                                    <th>Mức hoàn tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Dịch vụ không hoạt động do lỗi của chúng tôi</td>
                                    <td>Trong 72 giờ đầu sử dụng</td>
                                    <td><span class="info-badge info-badge--green">100%</span></td>
                                </tr>
                                <tr>
                                    <td>Downtime vượt mức SLA cam kết</td>
                                    <td>Theo mức downtime thực tế</td>
                                    <td><span class="info-badge info-badge--blue">Tính theo tỷ lệ</span></td>
                                </tr>
                                <tr>
                                    <td>Hủy trước khi kích hoạt</td>
                                    <td>Chưa sử dụng dịch vụ</td>
                                    <td><span class="info-badge info-badge--green">100%</span></td>
                                </tr>
                                <tr>
                                    <td>Hủy sau khi đã sử dụng</td>
                                    <td>Không áp dụng</td>
                                    <td><span class="info-badge info-badge--gray">Không hoàn</span></td>
                                </tr>
                                <tr>
                                    <td>Vi phạm điều khoản dẫn đến khóa tài khoản</td>
                                    <td>Không áp dụng</td>
                                    <td><span class="info-badge info-badge--gray">Không hoàn</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <p>
                        Yêu cầu hoàn tiền phải được gửi qua email đến
                        <a
                            href="mailto:<?php echo esc_attr($company_email); ?>"><?php echo esc_html($company_email); ?></a>
                        kèm theo mã đơn hàng và lý do cụ thể.
                    </p>
                </section>

                <!-- Section 6 -->
                <section class="info-section" id="section-6">
                    <div class="info-section__heading">
                        <div class="info-section__num">06</div>
                        <h2>Chất lượng dịch vụ (SLA)</h2>
                    </div>
                    <p>
                        <?php echo esc_html($company_name); ?> cam kết cung cấp dịch vụ với các tiêu chuẩn chất lượng
                        sau:
                    </p>
                    <ul class="info-list info-list--check">
                        <li><strong>Uptime 99%:</strong> Hệ thống hoạt động liên tục, downtime theo kế hoạch được thông
                            báo trước 24 giờ.</li>
                        <li><strong>Tốc độ kết nối:</strong> Băng thông tốc độ cao lên đến 1Gbps cho proxy datacenter.
                        </li>
                        <li><strong>Hỗ trợ 24/7:</strong> Đội ngũ kỹ thuật sẵn sàng hỗ trợ qua Telegram và email.</li>
                        <li><strong>Thay thế IP lỗi:</strong> IP bị block hoặc không hoạt động sẽ được thay thế miễn phí
                            trong vòng 24 giờ.</li>
                    </ul>
                    <div class="info-highlight-box info-highlight-box--blue">
                        <i class="fa-solid fa-circle-info"></i>
                        <p>SLA không áp dụng cho các trường hợp downtime do bất khả kháng như thiên tai, sự cố mạng
                            Internet quốc tế, hoặc các cuộc tấn công DDoS quy mô lớn.</p>
                    </div>
                </section>

                <!-- Section 7 -->
                <section class="info-section" id="section-7">
                    <div class="info-section__heading">
                        <div class="info-section__num">07</div>
                        <h2>Sở hữu trí tuệ</h2>
                    </div>
                    <p>
                        Tất cả nội dung trên website và dịch vụ <?php echo esc_html($company_name); ?>, bao gồm nhưng
                        không giới hạn: logo, thiết kế, giao diện, phần mềm, tài liệu, và nội dung văn bản, đều thuộc
                        quyền sở hữu của <?php echo esc_html($company_name); ?> hoặc các nhà cấp phép tương ứng.
                    </p>
                    <ul class="info-list">
                        <li>Bạn được cấp quyền sử dụng dịch vụ theo giấy phép có giới hạn, không độc quyền, không thể
                            chuyển nhượng.</li>
                        <li>Nghiêm cấm sao chép, sửa đổi, phân phối hoặc tạo ra sản phẩm phái sinh từ dịch vụ mà không
                            có phép bằng văn bản.</li>
                        <li>Nghiêm cấm dịch ngược (reverse engineer), giải mã (decompile) hoặc phá vỡ bất kỳ cơ chế bảo
                            mật nào.</li>
                    </ul>
                </section>

                <!-- Section 8 -->
                <section class="info-section" id="section-8">
                    <div class="info-section__heading">
                        <div class="info-section__num">08</div>
                        <h2>Giới hạn trách nhiệm</h2>
                    </div>
                    <p>Trong phạm vi tối đa được pháp luật cho phép:</p>
                    <ul class="info-list">
                        <li><?php echo esc_html($company_name); ?> <strong>không chịu trách nhiệm</strong> về các thiệt
                            hại gián tiếp, ngẫu nhiên, đặc biệt, hậu quả hoặc thiệt hại trừng phạt, kể cả mất dữ liệu,
                            mất doanh thu hoặc mất lợi nhuận.</li>
                        <li>Tổng trách nhiệm pháp lý của chúng tôi đối với bất kỳ khiếu nại nào sẽ không vượt quá
                            số tiền bạn đã thanh toán cho dịch vụ trong 30 ngày trước khi phát sinh khiếu nại.</li>
                        <li><?php echo esc_html($company_name); ?> không chịu trách nhiệm về cách bạn sử dụng dịch vụ
                            proxy, bao gồm bất kỳ vi phạm pháp luật hoặc điều khoản dịch vụ của bên thứ ba.</li>
                        <li>Chúng tôi không đảm bảo dịch vụ không có lỗi, không bị gián đoạn, hoặc phù hợp cho
                            mục đích cụ thể của bạn.</li>
                    </ul>
                    <div class="info-highlight-box info-highlight-box--yellow">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <p>Bạn hoàn toàn chịu trách nhiệm về mọi hoạt động thực hiện qua dịch vụ proxy của
                            <?php echo esc_html($company_name); ?>.
                        </p>
                    </div>
                </section>

                <!-- Section 9 -->
                <section class="info-section" id="section-9">
                    <div class="info-section__heading">
                        <div class="info-section__num">09</div>
                        <h2>Đình chỉ &amp; Chấm dứt dịch vụ</h2>
                    </div>

                    <h3>9.1. Đình chỉ tạm thời</h3>
                    <p>Chúng tôi có thể đình chỉ tài khoản của bạn nếu:</p>
                    <ul class="info-list">
                        <li>Phát hiện hoạt động đáng ngờ hoặc vi phạm điều khoản sử dụng.</li>
                        <li>Tài khoản bị báo cáo vi phạm bởi bên thứ ba có căn cứ.</li>
                        <li>Thanh toán thất bại hoặc quá hạn thanh toán.</li>
                        <li>Theo yêu cầu của cơ quan pháp luật có thẩm quyền.</li>
                    </ul>

                    <h3>9.2. Chấm dứt bởi người dùng</h3>
                    <p>
                        Bạn có thể chấm dứt tài khoản bất kỳ lúc nào bằng cách liên hệ với chúng tôi.
                        Dịch vụ đã thanh toán sẽ tiếp tục đến hết thời hạn đã đăng ký.
                    </p>

                    <h3>9.3. Chấm dứt bởi VieProxy</h3>
                    <p>
                        Chúng tôi có thể chấm dứt tài khoản và từ chối cung cấp dịch vụ trong tương lai
                        đối với người dùng vi phạm nghiêm trọng các điều khoản, với thông báo trước
                        <strong>3 ngày</strong> (trừ trường hợp vi phạm nghiêm trọng cần xử lý ngay lập tức).
                    </p>
                </section>

                <!-- Section 10 -->
                <section class="info-section" id="section-10">
                    <div class="info-section__heading">
                        <div class="info-section__num">10</div>
                        <h2>Thay đổi điều khoản</h2>
                    </div>
                    <p>
                        <?php echo esc_html($company_name); ?> có quyền sửa đổi các Điều khoản này bất kỳ lúc nào.
                        Khi có thay đổi quan trọng, chúng tôi sẽ:
                    </p>
                    <ul class="info-list">
                        <li>Thông báo qua email đến địa chỉ đã đăng ký của bạn ít nhất <strong>7 ngày</strong> trước khi
                            có hiệu lực.</li>
                        <li>Hiển thị banner thông báo trên website với link đến phiên bản mới.</li>
                        <li>Cập nhật ngày "Cập nhật lần cuối" ở đầu trang này.</li>
                    </ul>
                    <p>
                        Việc tiếp tục sử dụng dịch vụ sau ngày có hiệu lực đồng nghĩa với việc bạn chấp nhận
                        các điều khoản mới. Nếu bạn không đồng ý, hãy dừng sử dụng dịch vụ và liên hệ để hủy tài khoản.
                    </p>
                </section>

                <!-- Section 11 -->
                <section class="info-section" id="section-11">
                    <div class="info-section__heading">
                        <div class="info-section__num">11</div>
                        <h2>Luật áp dụng</h2>
                    </div>
                    <p>
                        Các Điều khoản Dịch vụ này được điều chỉnh và giải thích theo pháp luật
                        <strong>Cộng hòa Xã hội Chủ nghĩa Việt Nam</strong>.
                    </p>
                    <p>
                        Mọi tranh chấp phát sinh từ hoặc liên quan đến các Điều khoản này trước tiên sẽ được
                        giải quyết thông qua thương lượng thiện chí giữa các bên trong vòng 30 ngày.
                        Nếu không thể giải quyết bằng thương lượng, tranh chấp sẽ được đưa ra
                        <strong>Tòa án nhân dân có thẩm quyền tại Việt Nam</strong>.
                    </p>
                </section>

                <!-- Section 12 -->
                <section class="info-section" id="section-12">
                    <div class="info-section__heading">
                        <div class="info-section__num">12</div>
                        <h2>Liên hệ</h2>
                    </div>
                    <p>
                        Nếu bạn có bất kỳ câu hỏi nào về Điều khoản Dịch vụ này, vui lòng liên hệ với chúng tôi:
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
                    <a href="<?php echo esc_url(home_url('/chinh-sach-bao-mat/')); ?>"
                        class="info-page-nav__btn info-page-nav__btn--back">
                        <i class="fa-solid fa-arrow-left"></i>
                        Chính sách bảo mật
                    </a>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="info-page-nav__btn info-page-nav__btn--next">
                        Về trang chủ
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

            </article>
        </div>
    </div>

</main>

<!-- Back to Top Button -->
<!-- <button class="info-back-to-top" id="backToTop" aria-label="Về đầu trang">
    <i class="fa-solid fa-chevron-up"></i>
</button> -->

<?php get_footer(); ?>