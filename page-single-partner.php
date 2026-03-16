<?php
/**
 * Template Name: Single Partner
 * Description: Trang chi tiết đối tác - VieProxy
 */

get_header();
?>

<main class="site-main">
    <section class="single-partner-section">
        <div class="wrapper">

            <!-- Breadcrumb -->
            <nav class="single-partner-breadcrumb" aria-label="Breadcrumb">
                <a href="<?php echo esc_url(home_url('/')); ?>">Trang chủ</a>
                <i class="fa-solid fa-chevron-right"></i>
                <a href="<?php echo esc_url(home_url('/partners')); ?>">Chương trình đối tác</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span class="single-partner-breadcrumb__current" id="breadcrumbCurrent">Đang tải...</span>
            </nav>

            <!-- Dynamic content — JS sẽ render vào đây -->
            <div class="single-partner-content" id="partnerContent">

                <!-- Skeleton loading -->
                <div class="single-partner-skeleton">
                    <div class="skeleton skeleton--heading"></div>
                    <div class="single-partner-grid">
                        <!-- Left skeleton -->
                        <div class="left-col">
                            <div class="skeleton skeleton--tag"></div>
                            <div class="skeleton skeleton--btn-row"></div>
                            <div class="skeleton skeleton--para"></div>
                            <div class="skeleton skeleton--para"></div>
                            <div class="skeleton skeleton--para skeleton--para-short"></div>
                            <div class="skeleton skeleton--linkbox"></div>
                        </div>
                        <!-- Right skeleton -->
                        <div class="right-col">
                            <div class="single-partner-sidebar-card">
                                <div class="single-partner-sidebar__logo-row">
                                    <div class="skeleton skeleton--logo"></div>
                                    <div class="skeleton skeleton--name"></div>
                                </div>
                                <div class="skeleton skeleton--section-title"></div>
                                <div class="skeleton skeleton--item"></div>
                                <div class="skeleton skeleton--item skeleton--item-short"></div>
                                <div class="skeleton skeleton--section-title" style="margin-top:20px;"></div>
                                <div class="skeleton skeleton--item"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- /#partnerContent -->

            <!-- CTA Banner -->
            <div class="single-partner-cta">
                <div class="single-partner-cta__inner">
                    <h2 class="single-partner-cta__title">Kết nối và phát triển cùng VieProxy</h2>
                    <p class="single-partner-cta__sub">
                        Kết hợp proxy chất lượng cao với các đối tác hàng đầu để tối ưu hiệu suất công việc của bạn.
                    </p>
                    <a href="<?php echo esc_url(home_url('/register')); ?>" class="single-partner-cta__btn">
                        Đăng ký ngay
                    </a>
                </div>
            </div>

        </div>
    </section>
</main>

<?php get_footer(); ?>