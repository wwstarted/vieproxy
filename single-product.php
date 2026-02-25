<?php

/**
 * Template: Single Product — VieProxy Theme
 */

get_header();

// Get product object
global $product;
if (!is_a($product, 'WC_Product')) {
    $product = wc_get_product(get_the_ID());
}
?>

<main class="site-main">
    <div class="wrapper">
        <div class="single-product-container">

            <!-- Product Header Section -->
            <section class="single-product-header">
                <h1 class="single-product-title"><?php echo get_the_title(); ?></h1>
            </section>

            <!-- Product Description Section -->
            <section class="single-product-description">
                <div class="single-product-description-content">
                    <?php echo apply_filters('the_content', get_post_field('post_content', get_the_ID())); ?>
                </div>
            </section>

            <!-- Main Content Grid: 2 Columns -->
            <div class="single-product-main-grid">

                <!-- Left Column -->
                <div class="single-product-left-column">

                    <!-- Characteristics Section -->
                    <section class="single-product-characteristics">
                        <h2 class="single-product-characteristics-title">Characteristics</h2>

                        <div class="single-product-characteristics-list">
                            <?php
                            // Get characteristics from post meta (using plugin's meta key)
                            $characteristics = get_post_meta(get_the_ID(), '_vieproxy_characteristics', true);

                            if (!empty($characteristics) && is_array($characteristics)) {
                                foreach ($characteristics as $char) {
                                    if (!empty($char['key']) && !empty($char['value'])) {
                            ?>
                            <div class="single-product-characteristics-item">
                                <span
                                    class="single-product-characteristics-key"><?php echo esc_html($char['key']); ?>:</span>
                                <span
                                    class="single-product-characteristics-value"><?php echo esc_html($char['value']); ?></span>
                            </div>
                            <?php
                                    }
                                }
                            }
                            ?>
                        </div>
                    </section>

                    <!-- Feature Cards Section -->
                    <section class="single-product-features">
                        <div class="single-product-features-grid">
                            <?php
                            // Get feature cards from post meta (using plugin's meta key)
                            $feature_cards = get_post_meta(get_the_ID(), '_vieproxy_feature_cards', true);

                            // SVG Icons (same as plugin)
                            $svg_icons = [
                                'shield' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
                                'flash'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
                                'wrench' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
                                'money'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
                                'lock'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>',
                                'globe'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
                                'speed'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>',
                                'support' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
                            ];

                            if (!empty($feature_cards) && is_array($feature_cards)) {
                                foreach ($feature_cards as $card) {
                                    $icon = !empty($card['icon']) ? $card['icon'] : 'shield';
                                    $label = !empty($card['label']) ? $card['label'] : '';
                                    $custom_svg = !empty($card['custom_svg']) ? $card['custom_svg'] : '';

                                    if ($label) {
                                        $svg_output = $custom_svg ? $custom_svg : ($svg_icons[$icon] ?? $svg_icons['shield']);
                            ?>
                            <div class="single-product-feature-card">
                                <div class="single-product-feature-icon">
                                    <?php echo $svg_output; ?>
                                </div>
                                <div class="single-product-feature-title"><?php echo esc_html($label); ?></div>
                            </div>
                            <?php
                                    }
                                }
                            }
                            ?>
                        </div>
                    </section>

                    <!-- Product Short Description Section -->
                    <section class="single-product-short-description">
                        <div class="single-product-short-description-content">
                            <?php echo apply_filters('woocommerce_short_description', $product->get_short_description()); ?>
                        </div>
                    </section>

                </div>

                <!-- Right Column (40%) - Placeholder for now -->
                <div class="single-product-right-column">

                    <!-- Pricing Widget -->
                    <div class="single-product-pricing-widget">
                        <?php
                        // Get pricing config from post meta
                        $pricing_config = get_post_meta(get_the_ID(), '_vieproxy_pricing_config', true);

                        if (!empty($pricing_config)) {
                            $attr_label     = !empty($pricing_config['attr_label']) ? $pricing_config['attr_label'] : 'Số lượng IPs';
                            $unit_label     = !empty($pricing_config['unit_label']) ? $pricing_config['unit_label'] : 'IPs';
                            $min_qty        = !empty($pricing_config['min_qty']) ? absint($pricing_config['min_qty']) : 1;
                            $max_qty        = !empty($pricing_config['max_qty']) ? absint($pricing_config['max_qty']) : 100;
                            $duration_plans = !empty($pricing_config['duration_plans']) ? $pricing_config['duration_plans'] : [];

                            // Calculate first plan price
                            $first_plan_monthly = 100;
                            $first_plan_months = 1;
                            $first_plan_price = $min_qty * $first_plan_monthly * $first_plan_months;

                            if (!empty($duration_plans[0])) {
                                $first_plan_monthly = $duration_plans[0]['monthly_price'] ?? 100;
                                $first_plan_months = $duration_plans[0]['months'] ?? 1;
                                $first_plan_price = $min_qty * $first_plan_monthly * $first_plan_months;
                            }
                        ?>

                        <div class="single-product-widget-inner">
                            <!-- Header -->
                            <div class="single-product-widget-header">
                                <?php
                                    $product_image_id = $product->get_image_id();
                                    if ($product_image_id):
                                    ?>
                                <div class="single-product-widget-logo">
                                    <?php echo wp_get_attachment_image($product_image_id, array(50, 50)); ?>
                                </div>
                                <?php else: ?>
                                <div class="single-product-widget-logo single-product-widget-logo-placeholder">
                                    &lt;logo&gt;
                                </div>
                                <?php endif; ?>
                                <div class="single-product-widget-name"><?php echo get_the_title(); ?></div>
                            </div>

                            <!-- Quantity Row -->
                            <div class="single-product-widget-qty-row">
                                <span
                                    class="single-product-widget-qty-label"><?php echo esc_html($attr_label); ?></span>
                                <span class="single-product-widget-qty-value"
                                    id="single-product-qty-display"><?php echo $min_qty; ?></span>
                            </div>

                            <!-- Range Slider -->
                            <input type="range" class="single-product-widget-range" id="single-product-range"
                                min="<?php echo $min_qty; ?>" max="<?php echo $max_qty; ?>"
                                value="<?php echo $min_qty; ?>" step="1" data-min="<?php echo $min_qty; ?>"
                                data-max="<?php echo $max_qty; ?>" />

                            <!-- Price Display -->
                            <div class="single-product-widget-price-row">
                                <span class="single-product-widget-total"
                                    id="single-product-total">$<?php echo number_format($first_plan_price, 0); ?></span>
                                <span class="single-product-widget-per" id="single-product-per">/
                                    <?php echo $min_qty . ' ' . esc_html($unit_label); ?></span>
                            </div>

                            <!-- Meta Info -->
                            <div class="single-product-widget-meta">
                                <span>Giá: <span id="single-product-base-price">1 IP</span> – $<span
                                        id="single-product-monthly-price"><?php echo $first_plan_monthly; ?></span></span>
                                <span>Giảm: <span id="single-product-discount">-0</span>%</span>
                            </div>

                            <!-- Duration Plans -->
                            <?php if (!empty($duration_plans)): ?>
                            <div class="single-product-widget-duration-list" id="single-product-duration-list">
                                <?php foreach ($duration_plans as $i => $plan):
                                            $plan_price = $min_qty * ($plan['monthly_price'] ?? 100) * ($plan['months'] ?? 1);
                                        ?>
                                <label
                                    class="single-product-widget-duration-item <?php echo $i === 0 ? 'active' : ''; ?>"
                                    data-index="<?php echo $i; ?>"
                                    data-monthly-price="<?php echo esc_attr($plan['monthly_price'] ?? 100); ?>"
                                    data-months="<?php echo esc_attr($plan['months'] ?? 1); ?>">
                                    <input type="radio" name="single_product_duration" value="<?php echo $i; ?>"
                                        <?php checked($i, 0); ?> />
                                    <span class="single-product-duration-text">
                                        <?php echo esc_html($plan['label']); ?> – $<span
                                            class="single-product-duration-price"><?php echo number_format($plan_price, 0); ?></span>
                                    </span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>

                            <!-- Add to Cart Button -->
                            <button type="button" class="single-product-widget-btn-cart">
                                Thêm vào giỏ hàng
                            </button>
                        </div>

                        <?php
                        }
                        ?>
                    </div>

                    <!-- Table of Contents -->
                    <div class="single-product-toc">
                        <h3 class="single-product-toc-title">Tổng quan về <?php echo get_the_title(); ?></h3>
                        <div class="single-product-toc-list" id="single-product-toc-list">
                            <!-- TOC will be generated by JavaScript from short description headings -->
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- ═══════════════════════════════════════════════
         RELATED PRODUCTS SECTION
        ════════════════════════════════════════════════ -->
    <section class="single-product-related-section">
        <div class="wrapper">

            <!-- Header -->
            <div class="single-product-related-header">
                <h2 class="single-product-related-title">
                    <span class="main">CÁC GÓI PROXY</span>
                    <span class="highlight">PHỔ BIẾN KHÁC</span>
                </h2>
                <p class="single-product-related-description">
                    Cung cấp cho bạn một mức giá hợp lý tại các đối tác proxy thông dụng với hình thức thanh toán dễ
                    dàng và tiết kiệm nhất
                </p>
            </div>

            <!-- Slider Container -->
            <div class="single-product-related-slider-container">

                <!-- Prev Arrow -->
                <div class="single-product-related-slider__arrow single-product-related-slider__arrow--prev">
                    <i class="fa-solid fa-chevron-left"></i>
                </div>

                <!-- Slider Wrapper -->
                <div class="single-product-related-slider-wrapper">
                    <div class="single-product-related-slider">

                        <?php
                        // ── Query sản phẩm mới nhất (exclude current product) ────
                        $current_product_id = get_the_ID();

                        $related_args = array(
                            'post_type'      => 'product',
                            'posts_per_page' => -1,
                            'orderby'        => 'date',
                            'order'          => 'DESC',
                            'post__not_in'   => array($current_product_id), // Exclude current product
                        );

                        $related_query = new WP_Query($related_args);

                        if ($related_query->have_posts()) :
                            while ($related_query->have_posts()) : $related_query->the_post();
                                global $product;

                                $rp_id   = get_the_ID();
                                $rp_name = get_the_title();
                                $rp_url  = get_permalink();

                                // Logo / thumbnail
                                $rp_thumb_id  = get_post_thumbnail_id();
                                $rp_thumb_url = $rp_thumb_id
                                    ? wp_get_attachment_image_url($rp_thumb_id, 'thumbnail')
                                    : '';

                                // VieProxy meta
                                $rp_meta = vieproxy_get_product_meta($rp_id);

                                // Mô tả ngắn
                                $rp_desc = $rp_meta['sub_description'] ?? '';
                                if (empty($rp_desc)) {
                                    $rp_desc = $product->get_short_description();
                                }
                                if (empty($rp_desc)) {
                                    $rp_desc = get_the_excerpt();
                                }

                                // Pricing
                                $rp_pricing       = $rp_meta['pricing_config'] ?? [];
                                $rp_plans         = $rp_pricing['duration_plans'] ?? [];
                                if (empty($rp_plans)) continue; // skip nếu chưa cấu hình

                                $rp_plan_idx      = $rp_pricing['default_plan_index'] ?? 0;
                                $rp_plan          = $rp_plans[$rp_plan_idx] ?? $rp_plans[0];
                                $rp_monthly_price = $rp_plan['monthly_price'] ?? 0;
                                $rp_months        = $rp_plan['months']        ?? 1;
                                $rp_default_qty   = $rp_pricing['default_qty'] ?? $rp_pricing['min_qty'] ?? 1;
                                $rp_unit_label    = $rp_pricing['unit_label']  ?? 'IPs';

                                // Giá VND = qty × monthly_price × months × 25000
                                $rp_price_vnd = $rp_default_qty * $rp_monthly_price * $rp_months * 25000;

                                // Nhãn giá: "X.XXXđ / Y IPs"
                                $rp_price_label = number_format($rp_price_vnd, 0, ',', '.') . 'đ'
                                    . ' / ' . $rp_default_qty . ' ' . $rp_unit_label;

                                // Features: ưu tiên meta → fallback mô tả ngắn thành bullet
                                $rp_features = $rp_meta['features'] ?? [];
                        ?>

                        <div class="pricing-card">

                            <!-- Logo -->
                            <div class="pricing-card__logo">
                                <?php if ($rp_thumb_url) : ?>
                                <img src="<?php echo esc_url($rp_thumb_url); ?>" alt="<?php echo esc_attr($rp_name); ?>"
                                    style="width:100%;height:100%;object-fit:contain;border-radius:50%;" />
                                <?php endif; ?>
                            </div>

                            <!-- Top: blue bg -->
                            <div class="pricing-card__top">
                                <h3 class="pricing-card__title">
                                    <?php echo esc_html($rp_name); ?>
                                </h3>
                                <?php if ($rp_desc) : ?>
                                <p class="pricing-card__desc">
                                    <?php echo esc_html(wp_trim_words($rp_desc, 20, '...')); ?>
                                </p>
                                <?php endif; ?>
                                <div class="pricing-card__price">
                                    <?php echo esc_html($rp_price_label); ?>
                                </div>
                            </div>

                            <!-- Bottom: features + button -->
                            <div class="pricing-card__bottom">
                                <ul class="pricing-card__features">
                                    <?php if (!empty($rp_features)) : ?>
                                    <?php foreach ($rp_features as $feat) :
                                                    $feat_text = is_array($feat) ? ($feat['text'] ?? $feat['title'] ?? '') : $feat;
                                                    if (empty($feat_text)) continue;
                                                ?>
                                    <li class="pricing-card__feature">
                                        <i class="fa-solid fa-circle-check"></i>
                                        <span><?php echo esc_html($feat_text); ?></span>
                                    </li>
                                    <?php endforeach; ?>
                                    <?php else : ?>
                                    <?php
                                                // Fallback: lấy từ product attributes hoặc hiển thị thông tin kỹ thuật cơ bản
                                                $rp_attrs = $product->get_attributes();
                                                $feat_count = 0;
                                                foreach ($rp_attrs as $attr) :
                                                    if ($feat_count >= 3) break;
                                                    $attr_name  = wc_attribute_label($attr->get_name());
                                                    $attr_value = implode(', ', $attr->get_terms() ? wp_list_pluck($attr->get_terms(), 'name') : []);
                                                    if (empty($attr_value)) continue;
                                                ?>
                                    <li class="pricing-card__feature">
                                        <i class="fa-solid fa-circle-check"></i>
                                        <span><?php echo esc_html($attr_name . ': ' . $attr_value); ?></span>
                                    </li>
                                    <?php
                                                    $feat_count++;
                                                endforeach;

                                                // Nếu vẫn không có attr → hiển thị fallback text
                                                if ($feat_count === 0) :
                                                ?>
                                    <li class="pricing-card__feature">
                                        <i class="fa-solid fa-circle-check"></i>
                                        <span>Kết nối ổn định, tốc độ cao</span>
                                    </li>
                                    <li class="pricing-card__feature">
                                        <i class="fa-solid fa-circle-check"></i>
                                        <span>Hỗ trợ HTTP, SOCKS5</span>
                                    </li>
                                    <li class="pricing-card__feature">
                                        <i class="fa-solid fa-circle-check"></i>
                                        <span>Hỗ trợ 24/7</span>
                                    </li>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </ul>

                                <a href="<?php echo esc_url($rp_url); ?>" class="pricing-card__btn">
                                    Mua ngay
                                </a>
                            </div>

                        </div>

                        <?php
                            endwhile;
                            wp_reset_postdata();
                        else :
                            ?>
                        <p style="color:#666;padding:40px;text-align:center;">Chưa có sản phẩm liên quan.</p>
                        <?php endif; ?>
                    </div><!-- /.single-product-related-slider -->
                </div><!-- /.single-product-related-slider-wrapper -->

                <!-- Next Arrow -->
                <div class="single-product-related-slider__arrow single-product-related-slider__arrow--next">
                    <i class="fa-solid fa-chevron-right"></i>
                </div>

            </div><!-- /.single-product-related-slider-container -->

            <!-- Slider Controls (Dots) -->
            <div class="single-product-related-slider-controls">
                <div class="single-product-related-slider__dots">
                    <!-- Dots will be generated by JS -->
                </div>
            </div>

        </div>
    </section>

</main>

<?php get_footer(); ?>