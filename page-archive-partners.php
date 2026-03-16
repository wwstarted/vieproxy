<?php
/**
 * Template Name: Archive Partners
 * Description: Trang danh sách đối tác - VieProxy
 */

get_header();
?>

<main class="site-main">
    <section class="archive-partners-section">
        <div class="wrapper">

            <!-- Section Header -->
            <div class="archive-partners-header">
                <h1 class="archive-partners-title">
                    ĐỐI TÁC CỦA
                    <span class="archive-partners-title__highlight">VIEPROXY</span>
                </h1>
                <p class="archive-partners-desc">
                    Khám phá hệ sinh thái đối tác đa dạng — trình duyệt ẩn danh, công cụ tự động hóa,
                    giải pháp captcha và nhiều hơn nữa, tất cả tích hợp hoàn hảo với dịch vụ proxy của chúng tôi.
                </p>
            </div>

            <!-- Filter Tabs -->
            <div class="archive-partners-tabs" id="partnerTabs">
                <!-- Render bằng JS -->
                <button class="archive-partners-tab is-active" data-category="all">Tất cả các loại</button>
            </div>

            <!-- Partner Cards Grid -->
            <div class="archive-partners-grid" id="partnerGrid">
                <!-- Skeleton loading -->
                <?php for ($i = 0; $i < 8; $i++): ?>
                    <div class="partner-card partner-card--skeleton">
                        <div class="partner-card__header">
                            <div class="skeleton skeleton--logo"></div>
                            <div class="skeleton skeleton--title"></div>
                        </div>
                        <div class="skeleton skeleton--line"></div>
                        <div class="skeleton skeleton--line skeleton--line-short"></div>
                    </div>
                <?php endfor; ?>
            </div>

            <!-- Pagination -->
            <div class="archive-partners-pagination" id="partnerPagination">
                <button class="pagination-btn" id="prevBtn" disabled>
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <div class="pagination-numbers" id="paginationNumbers"></div>
                <button class="pagination-btn" id="nextBtn" disabled>
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>

            <!-- CTA Banner -->
            <div class="archive-partners-cta">
                <div class="archive-partners-cta__inner">
                    <h2 class="archive-partners-cta__title">Kết nối và phát triển cùng VieProxy</h2>
                    <p class="archive-partners-cta__sub">
                        Sử dụng proxy chất lượng cao kết hợp với các đối tác hàng đầu để tối ưu hiệu suất công việc.
                    </p>
                    <a href="<?php echo esc_url(home_url('/register')); ?>" class="archive-partners-cta__btn">
                        Đăng ký ngay
                    </a>
                </div>
            </div>

        </div>
    </section>
</main>

<?php get_footer(); ?>