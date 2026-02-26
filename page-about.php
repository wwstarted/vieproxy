<?php
/**
 * Template Name: Về Chúng Tôi
 * Description: Template trang Về Chúng Tôi của VieProxy
 */

get_header();

$company_name = 'VieProxy';
$company_email = 'support@vieproxy.vn';
$company_tg = 'https://t.me/VieProxyVN';
$company_zalo = 'https://zalo.me/0347700437';
$company_fb = 'https://fb.com/VieProxyVN';
?>

<main class="main-info-page main-about-page">

    <!-- ═══════════════════════════════════════════════
         HERO
    ════════════════════════════════════════════════ -->
    <section class="about-hero">
        <div class="wrapper">

            <nav class="about-hero__breadcrumb" aria-label="breadcrumb">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="breadcrumb-link">Trang chủ</a>
                <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
                <span class="breadcrumb-current">Về Chúng Tôi</span>
            </nav>

            <div class="about-hero__inner">
                <div class="about-hero__content">
                    <h1 class="about-hero__title">
                        Về <span class="about-hero__title-accent"><?php echo esc_html($company_name); ?></span>
                    </h1>
                    <p class="about-hero__subtitle">
                        Nhà cung cấp proxy uy tín tại Việt Nam — được xây dựng bởi những người
                        hiểu rõ nhu cầu thực tế của developer, marketer và doanh nghiệp trong nước.
                    </p>
                    <div class="about-hero__meta">
                        <span class="about-hero__badge">
                            <i class="fa-solid fa-flag"></i>
                            Made in Vietnam
                        </span>
                        <span class="about-hero__badge">
                            <i class="fa-solid fa-shield-halved"></i>
                            Bảo mật &amp; Tin cậy
                        </span>
                        <span class="about-hero__badge">
                            <i class="fa-solid fa-headset"></i>
                            Hỗ trợ 7 ngày / tuần
                        </span>
                    </div>
                </div>
            </div>

        </div>

        <div class="about-hero__blob about-hero__blob--1"></div>
        <div class="about-hero__blob about-hero__blob--2"></div>
    </section>

    <!-- ═══════════════════════════════════════════════
         ARTICLE CONTENT
    ════════════════════════════════════════════════ -->
    <div class="wrapper about-page-wrapper">
        <article class="about-article">

            <p>
                Chào mừng bạn đến với <strong><?php echo esc_html($company_name); ?></strong> — nơi giải pháp
                proxy trở nên rõ ràng, đơn giản và thực sự hữu ích với mọi người.
            </p>
            <p>
                <?php echo esc_html($company_name); ?> được xây dựng bởi những người đã từng là khách hàng —
                những developer, marketer và data analyst hiểu rõ nỗi đau khi phải vật lộn với proxy kém chất lượng,
                hỗ trợ chậm và giá cả thiếu minh bạch. Sau nhiều lần thử nghiệm và rút kinh nghiệm thực tế,
                chúng tôi quyết định tự xây dựng dịch vụ mà chính mình muốn sử dụng.
            </p>

            <h2>Sứ mệnh của chúng tôi</h2>
            <p>
                Tại <?php echo esc_html($company_name); ?>, sứ mệnh của chúng tôi là cung cấp giải pháp proxy
                chất lượng cao, ổn định và minh bạch — giúp các cá nhân và doanh nghiệp Việt Nam tiếp cận
                internet toàn cầu một cách hiệu quả, an toàn và tiết kiệm chi phí.
            </p>
            <p>
                Chúng tôi tin rằng quyết định đúng đắn đến từ thông tin chính xác và dịch vụ thực sự tốt.
                Vì vậy, mỗi gói proxy, mỗi IP và mỗi hỗ trợ kỹ thuật đều được đảm bảo chất lượng trước khi
                đến tay khách hàng.
            </p>

            <h2>Điều gì khiến chúng tôi khác biệt</h2>
            <p>
                Chúng tôi hiểu proxy có thể là khái niệm phức tạp, đặc biệt với người mới bắt đầu.
                <?php echo esc_html($company_name); ?> tập trung vào việc biến những khái niệm kỹ thuật
                thành giải pháp dễ tiếp cận và dễ sử dụng cho mọi đối tượng.
            </p>
            <p>
                Thay vì chỉ bán dịch vụ, chúng tôi đồng hành cùng khách hàng — giải thích rõ từng loại proxy
                hoạt động như thế nào, phù hợp với usecase nào, và đưa ra lời khuyên trung thực dựa trên
                nhu cầu thực tế. Đội ngũ hỗ trợ người Việt, nói tiếng Việt, hiểu văn hoá làm việc Việt Nam.
            </p>

            <h2>Bạn sẽ tìm thấy gì tại <?php echo esc_html($company_name); ?></h2>
            <p>
                Tại đây, bạn có thể truy cập đầy đủ các loại proxy từ IPv4/IPv6 Datacenter, Mobile Proxy
                đến Residential Proxy phủ sóng 190+ quốc gia — tất cả với hạ tầng tự vận hành, không qua
                trung gian, đảm bảo tốc độ và độ ổn định cao nhất.
            </p>
            <p>
                <?php echo esc_html($company_name); ?> không chỉ là nhà cung cấp proxy — mà là người đồng hành
                đáng tin cậy cho bất kỳ ai muốn làm việc trên internet một cách hiệu quả, bảo mật và tự tin.
            </p>

            <!-- Social Icons -->
            <div class="about-social">
                <p class="about-social__label">Kết nối với chúng tôi</p>
                <div class="about-social__icons">

                    <a href="mailto:<?php echo esc_attr($company_email); ?>"
                        class="about-social__icon about-social__icon--email" aria-label="Email hỗ trợ">
                        <i class="fa-solid fa-envelope"></i>
                    </a>

                    <a href="<?php echo esc_url($company_tg); ?>" target="_blank" rel="noopener noreferrer"
                        class="about-social__icon about-social__icon--tg" aria-label="Telegram">
                        <i class="fa-brands fa-telegram"></i>
                    </a>

                    <a href="<?php echo esc_url($company_zalo); ?>" target="_blank" rel="noopener noreferrer"
                        class="about-social__icon about-social__icon--zalo" aria-label="Zalo">
                        <i class="fa-solid fa-comment-dots"></i>
                    </a>

                    <a href="<?php echo esc_url($company_fb); ?>" target="_blank" rel="noopener noreferrer"
                        class="about-social__icon about-social__icon--fb" aria-label="Facebook">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>

                </div>
            </div>

        </article>
    </div>

</main>

<?php get_footer(); ?>