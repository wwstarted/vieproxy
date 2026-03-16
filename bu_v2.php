<?php

/**
 * Template Name: Home Page
 * Description: Template cho trang Home của VieProxy
 */

get_header();
?>

<main class="site-main">

    <?php if (vieproxy_section_visible('hero')): ?>
        <!-- ═══════════════════════════════════════════════
         HERO SECTION
    ════════════════════════════════════════════════ -->
        <section class="home-hero-section">
            <div class="wrapper home-hero-inner">

                <!-- Left: Text Content -->
                <div class="home-hero-content">
                    <h1 class="home-hero-title">
                        <?php echo nl2br(esc_html(vieproxy_get_option('hero_title', 'Mua proxy cao cấp – giá tốt nhất tại VieProxy'))); ?>
                    </h1>

                    <?php $desc = vieproxy_get_option('hero_description', ''); ?>
                    <?php if ($desc): ?>
                        <p class="home-hero-description">
                            <?php echo esc_html($desc); ?>
                        </p>
                    <?php endif; ?>

                    <?php
                    /**
                     * Dynamic Hero Order Widget
                     * Thay thế widget tĩnh — render từ WooCommerce data thực tế.
                     * File: /inc/hero-widget-dynamic.php
                     */
                    $hero_widget_file = get_template_directory() . '/inc/hero-widget-dynamic.php';
                    if (file_exists($hero_widget_file)) {
                        include $hero_widget_file;
                    }
                    ?>

                </div><!-- /.home-hero-content -->

                <!-- Right: Image -->
                <div class="home-hero-image-wrap">
                    <?php $img = vieproxy_get_option('hero_image', ''); ?>
                    <?php if ($img): ?>
                        <img
                            src="<?php echo esc_url($img); ?>"
                            alt="VieProxy illustration"
                            class="home-hero-image" />
                    <?php else: ?>
                        <!-- Placeholder globe SVG khi chưa có ảnh -->
                        <div class="home-hero-image-placeholder">
                            <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" class="home-hero-globe-svg">
                                <circle cx="100" cy="100" r="90" fill="url(#globeGrad)" opacity="0.9" />
                                <ellipse cx="100" cy="100" rx="50" ry="90" stroke="rgba(255,255,255,0.3)" stroke-width="1.5" fill="none" />
                                <ellipse cx="100" cy="100" rx="90" ry="40" stroke="rgba(255,255,255,0.3)" stroke-width="1.5" fill="none" />
                                <line x1="10" y1="100" x2="190" y2="100" stroke="rgba(255,255,255,0.25)" stroke-width="1.5" />
                                <line x1="100" y1="10" x2="100" y2="190" stroke="rgba(255,255,255,0.25)" stroke-width="1.5" />
                                <!-- Pins -->
                                <circle cx="75" cy="60" r="5" fill="white" opacity="0.85" />
                                <circle cx="130" cy="75" r="5" fill="white" opacity="0.85" />
                                <circle cx="60" cy="120" r="5" fill="white" opacity="0.85" />
                                <circle cx="145" cy="130" r="5" fill="white" opacity="0.85" />
                                <circle cx="100" cy="85" r="5" fill="white" opacity="0.85" />
                                <defs>
                                    <radialGradient id="globeGrad" cx="40%" cy="35%" r="65%">
                                        <stop offset="0%" stop-color="#4facf7" />
                                        <stop offset="100%" stop-color="#007BF3" />
                                    </radialGradient>
                                </defs>
                            </svg>
                        </div>
                    <?php endif; ?>
                </div><!-- /.home-hero-image-wrap -->

            </div><!-- /.home-hero-inner -->
        </section>
    <?php endif; ?>

    <?php if (vieproxy_section_visible('proxy_list')): ?>
        <!-- ═══════════════════════════════════════════════
         PROXY LIST SECTION
    ════════════════════════════════════════════════ -->
        <section class="home-proxy-list-section">
            <div class="wrapper">

                <!-- Section Header -->
                <div class="home-proxy-list-header">
                    <h2 class="home-proxy-list-title">
                        <?php
                        $title_main = vieproxy_get_option('proxy_list_title_main', 'DANH SÁCH CÁC LOẠI PROXY');
                        $title_highlight = vieproxy_get_option('proxy_list_title_highlight', 'TỐT NHẤT');
                        echo esc_html($title_main);
                        ?>
                        <span class="home-highlight-text"><?php echo esc_html($title_highlight); ?></span>
                    </h2>
                </div>

                <?php
                // ════════════════════════════════════════════════════════
                // Query 1 lần — dùng cho cả Tabs lẫn bảng
                // ════════════════════════════════════════════════════════
                $proxy_products = [];

                $pl_query = new WP_Query([
                    'post_type'      => 'product',
                    'posts_per_page' => -1,
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                ]);

                if ($pl_query->have_posts()) :
                    while ($pl_query->have_posts()) : $pl_query->the_post();
                        global $product;

                        $pl_id    = get_the_ID();
                        $pl_name  = get_the_title();
                        $pl_url   = get_permalink();

                        $pl_thumb_id  = get_post_thumbnail_id();
                        $pl_thumb_url = $pl_thumb_id
                            ? wp_get_attachment_image_url($pl_thumb_id, 'thumbnail')
                            : get_template_directory_uri() . '/images/placeholder.png';

                        $pl_meta  = vieproxy_get_product_meta($pl_id);
                        $pl_desc  = $pl_meta['sub_description'] ?? '';
                        if (empty($pl_desc)) $pl_desc = $product->get_short_description();

                        $pl_cfg      = $pl_meta['pricing_config'] ?? [];
                        $pl_ptype    = $pl_cfg['pricing_type'] ?? 'ip_time';
                        $pl_usd_rate = (float) get_option('vieproxy_usd_rate', 25000);
                        if ($pl_usd_rate <= 0) $pl_usd_rate = 25000;

                        // Tính giá hiển thị tùy theo pricing_type
                        $pl_unit    = $pl_cfg['unit_label'] ?? 'IPs';
                        $pl_defqty  = (int)($pl_cfg['default_qty'] ?? 1);
                        $pl_defplan = (int)($pl_cfg['default_plan_index'] ?? 0);
                        $pl_vnd     = 0;
                        $pl_usd     = 0;

                        if ($pl_ptype === 'time_only') {
                            // Buy Time: lấy từ time_plans
                            $pl_time_plans = $pl_cfg['time_plans'] ?? [];
                            if (empty($pl_time_plans)) continue;
                            $pl_plan   = $pl_time_plans[$pl_defplan] ?? $pl_time_plans[0];
                            $pl_vnd    = (float)($pl_plan['price_vnd'] ?? 0);
                            if (!$pl_vnd) $pl_vnd = round((float)($pl_plan['price'] ?? 0) * $pl_usd_rate);
                            $pl_usd    = (float)($pl_plan['price'] ?? ($pl_vnd / $pl_usd_rate));
                        } elseif ($pl_ptype === 'dollar_time') {
                            // $ × Time: dollar_time_tiers + duration_plans_dt
                            $pl_dtt_tiers = $pl_cfg['dollar_time_tiers'] ?? [];
                            $pl_dt_plans  = $pl_cfg['duration_plans_dt'] ?? [];
                            if (empty($pl_dtt_tiers) || empty($pl_dt_plans)) continue;
                            $pl_plan      = $pl_dt_plans[$pl_defplan] ?? $pl_dt_plans[0];
                            $pl_months    = (float)($pl_plan['months'] ?? 1);
                            // Lấy mốc $ mặc định (dùng mốc đầu tiên làm default)
                            $pl_dtt_tier  = $pl_dtt_tiers[0];
                            $pl_dtt_qty   = (float)($pl_dtt_tier['qty'] ?? $pl_dtt_tier['label'] ?? 10);
                            $pl_price_unit_usd = (float)($pl_dtt_tier['price_per_unit'] ?? $pl_dtt_tier['amount'] ?? 0);
                            // Tổng = Số $ × Giá/1$ × Số tháng (0 tháng = vô tận, không nhân tháng)
                            $pl_usd = $pl_months == 0
                                ? $pl_dtt_qty * $pl_price_unit_usd
                                : $pl_dtt_qty * $pl_price_unit_usd * $pl_months;
                            $pl_vnd = round($pl_usd * $pl_usd_rate);
                            $pl_unit = $pl_cfg['dt_unit_label'] ?? '$';
                        } else {
                            // IP×Time / GB×Time / Bộ×Time: qty_tiers + duration_plans
                            $pl_plans  = $pl_cfg['duration_plans'] ?? [];
                            $pl_tiers  = $pl_cfg['qty_tiers']      ?? [];
                            if (empty($pl_plans) || empty($pl_tiers)) continue;
                            $pl_plan   = $pl_plans[$pl_defplan] ?? $pl_plans[0];
                            $pl_months = (float)($pl_plan['months'] ?? 1);
                            $pl_tier   = $pl_tiers[0];
                            foreach ($pl_tiers as $t) {
                                if ((int)$t['value'] === $pl_defqty) {
                                    $pl_tier = $t;
                                    break;
                                }
                            }
                            $pl_price_unit_usd = (float)($pl_tier['price_per_unit'] ?? 0);
                            $pl_usd = $pl_months == 0
                                ? $pl_defqty * $pl_price_unit_usd
                                : $pl_defqty * $pl_price_unit_usd * $pl_months;
                            $pl_vnd = round($pl_usd * $pl_usd_rate);
                        }

                        // Build qty options (IP/GB×Time) hoặc time_plans options (time_only/dollar_time)
                        $pl_opts = [];
                        if ($pl_ptype === 'dollar_time') {
                            // dollar_time: opts là các mốc $ từ dollar_time_tiers
                            foreach ($pl_dtt_tiers as $t) {
                                $v = (float)($t['qty'] ?? $t['label'] ?? 0);
                                if ($v > 0) $pl_opts[] = (int)$v;
                            }
                            sort($pl_opts);
                            if (empty($pl_opts)) $pl_opts = [10];
                        } elseif ($pl_ptype !== 'time_only') {
                            foreach ($pl_tiers as $t) {
                                $v = (int)($t['value'] ?? 0);
                                if ($v > 0) $pl_opts[] = $v;
                            }
                            sort($pl_opts);
                            if (empty($pl_opts)) $pl_opts = [$pl_defqty ?: 1];
                            if (!in_array($pl_defqty, $pl_opts)) {
                                $pl_opts[] = $pl_defqty;
                                sort($pl_opts);
                            }
                        }

                        // Encode data cho JS
                        $pl_js_price_unit  = isset($pl_price_unit_usd) ? $pl_price_unit_usd : 0;
                        $pl_js_months      = isset($pl_months) ? $pl_months : 1;
                        $pl_tiers_json     = '';
                        $pl_time_plans_json = '[]';
                        if ($pl_ptype === 'time_only') {
                            // Encode toàn bộ time_plans để JS render select
                            $pl_tp_arr = [];
                            foreach ($pl_time_plans as $i => $tp) {
                                $tp_vnd = (float)($tp['price_vnd'] ?? 0);
                                if (!$tp_vnd) $tp_vnd = round((float)($tp['price'] ?? 0) * $pl_usd_rate);
                                $pl_tp_arr[] = [
                                    'label' => $tp['label'] ?? '',
                                    'vnd'   => $tp_vnd,
                                    'usd'   => round((float)($tp['price'] ?? ($tp_vnd / $pl_usd_rate)), 2),
                                    'def'   => ($i === $pl_defplan),
                                ];
                            }
                            $pl_time_plans_json = json_encode($pl_tp_arr);
                        } elseif ($pl_ptype === 'dollar_time') {
                            // dollar_time: encode time_plans_json dạng tương tự time_only để JS dùng select
                            // Mỗi option = một mốc $ × gói thời hạn mặc định
                            $pl_tp_arr = [];
                            $pl_dt_plan_def = isset($pl_dt_plans[$pl_defplan]) ? $pl_dt_plans[$pl_defplan] : ($pl_dt_plans[0] ?? []);
                            $pl_dt_months   = (float)($pl_dt_plan_def['months'] ?? 1);
                            $pl_dt_label    = $pl_dt_plan_def['label'] ?? '';
                            foreach ($pl_dtt_tiers as $i => $dtt) {
                                $dtt_qty = (float)($dtt['qty'] ?? $dtt['label'] ?? 0);
                                $dtt_ppu = (float)($dtt['price_per_unit'] ?? $dtt['amount'] ?? 0);
                                $dtt_usd = $pl_dt_months == 0
                                    ? $dtt_qty * $dtt_ppu
                                    : $dtt_qty * $dtt_ppu * $pl_dt_months;
                                $dtt_vnd = round($dtt_usd * $pl_usd_rate);
                                $pl_tp_arr[] = [
                                    'label' => '$' . (int)$dtt_qty . ($pl_dt_label ? ' / ' . $pl_dt_label : ''),
                                    'vnd'   => $dtt_vnd,
                                    'usd'   => round($dtt_usd, 2),
                                    'def'   => ($i === 0),
                                ];
                            }
                            $pl_time_plans_json = json_encode($pl_tp_arr);
                        } else {
                            $pl_tiers_map = [];
                            foreach ($pl_tiers as $t) {
                                $tv = (int)($t['value'] ?? 0);
                                if ($tv > 0) $pl_tiers_map[$tv] = (float)($t['price_per_unit'] ?? 0);
                            }
                            $pl_tiers_json = json_encode($pl_tiers_map);
                        }

                        $pl_instock = $product->get_stock_status() === 'instock';

                        $proxy_products[] = [
                            'id'              => $pl_id,
                            'name'            => $pl_name,
                            'url'             => $pl_url,
                            'thumb'           => $pl_thumb_url,
                            'desc'            => $pl_desc,
                            'unit'            => $pl_unit,
                            'defqty'          => $pl_defqty,
                            'opts'            => $pl_opts,
                            'pricing_type'    => $pl_ptype,
                            'price_per_unit'  => $pl_js_price_unit,   // USD/unit
                            'months'          => $pl_js_months,
                            'usd_rate'        => $pl_usd_rate,
                            'tiers_json'      => $pl_tiers_json,
                            'time_plans_json' => $pl_time_plans_json,
                            'vnd'             => $pl_vnd,
                            'usd'             => $pl_usd,
                            'instock'         => $pl_instock,
                            'filter_key'      => 'product-' . $pl_id,
                        ];
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>

                <!-- Filter Tabs: 1 tab = 1 sản phẩm WooCommerce -->
                <div class="home-proxy-tabs">
                    <button class="home-proxy-tab is-active" data-filter="all">Tất cả</button>

                    <?php foreach ($proxy_products as $pp) : ?>
                        <button class="home-proxy-tab" data-filter="<?php echo esc_attr($pp['filter_key']); ?>">
                            <img src="<?php echo esc_url($pp['thumb']); ?>"
                                alt="<?php echo esc_attr($pp['name']); ?>"
                                class="home-tab-icon" />
                            <?php echo esc_html($pp['name']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <!-- Table Container with Horizontal Scroll -->
                <div class="home-proxy-table-container">
                    <table class="home-proxy-table">
                        <thead>
                            <tr>
                                <th>Dịch vụ</th>
                                <th>Số lượng</th>
                                <th>Hiện có</th>
                                <th>Giá</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($proxy_products)) : ?>
                                <?php foreach ($proxy_products as $pp) : ?>
                                    <tr class="home-proxy-row"
                                        data-product-id="<?php echo esc_attr($pp['id']); ?>"
                                        data-filter="<?php echo esc_attr($pp['filter_key']); ?>">

                                        <!-- Dịch vụ -->
                                        <td>
                                            <div class="home-service-cell">
                                                <a href="<?php echo esc_url($pp['url']); ?>" class="home-service-link">
                                                    <img src="<?php echo esc_url($pp['thumb']); ?>"
                                                        alt="<?php echo esc_attr($pp['name']); ?>"
                                                        class="home-service-logo" />
                                                    <div class="home-service-info">
                                                        <div class="home-service-name">
                                                            <?php echo esc_html($pp['name']); ?>
                                                        </div>
                                                        <?php if ($pp['desc']) : ?>
                                                            <div class="home-service-desc">
                                                                <?php echo esc_html(wp_trim_words($pp['desc'], 15, '...')); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </a>
                                            </div>
                                        </td>

                                        <!-- Số lượng -->
                                        <td>
                                            <?php if ($pp['pricing_type'] === 'time_only' || $pp['pricing_type'] === 'dollar_time') : ?>
                                                <?php
                                                $pp_plans = json_decode($pp['time_plans_json'], true) ?: [];
                                                ?>
                                                <select class="home-quantity-select"
                                                    data-product-id="<?php echo esc_attr($pp['id']); ?>"
                                                    data-type="time_only"
                                                    data-usd-rate="<?php echo esc_attr($pp['usd_rate']); ?>">
                                                    <?php foreach ($pp_plans as $tp) : ?>
                                                        <option value="<?php echo esc_attr($tp['vnd']); ?>"
                                                            data-usd="<?php echo esc_attr($tp['usd']); ?>"
                                                            <?php echo !empty($tp['def']) ? 'selected' : ''; ?>>
                                                            <?php echo esc_html($tp['label']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php else : ?>
                                                <select class="home-quantity-select"
                                                    data-product-id="<?php echo esc_attr($pp['id']); ?>"
                                                    data-price-per-unit="<?php echo esc_attr($pp['price_per_unit']); ?>"
                                                    data-months="<?php echo esc_attr($pp['months']); ?>"
                                                    data-usd-rate="<?php echo esc_attr($pp['usd_rate']); ?>"
                                                    data-tiers="<?php echo esc_attr($pp['tiers_json']); ?>"
                                                    data-unit-label="<?php echo esc_attr($pp['unit']); ?>">
                                                    <?php foreach ($pp['opts'] as $qty) : ?>
                                                        <option value="<?php echo esc_attr($qty); ?>"
                                                            <?php selected($qty, $pp['defqty']); ?>>
                                                            <?php
                                                            $label = $qty . ' ' . $pp['unit'];
                                                            // Thêm thời hạn: "50 IPs / 1 Tháng"
                                                            if ($pp['months'] == 0) {
                                                                $label .= ' / Vô tận';
                                                            } elseif ($pp['months'] == 1) {
                                                                $label .= ' / 1 Tháng';
                                                            } else {
                                                                $label .= ' / ' . $pp['months'] . ' Tháng';
                                                            }
                                                            echo esc_html($label);
                                                            ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Hiện có -->
                                        <td>
                                            <?php if ($pp['instock']) : ?>
                                                <span class="home-status-badge home-status-available">Có sẵn</span>
                                            <?php else : ?>
                                                <span class="home-status-badge home-status-out">Hết hàng</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Giá -->
                                        <td>
                                            <div class="home-price-cell"
                                                data-product-id="<?php echo esc_attr($pp['id']); ?>">
                                                <div class="home-price-amount">
                                                    <?php echo number_format($pp['vnd'], 0, ',', '.'); ?>đ
                                                </div>
                                                <div class="home-price-per-unit">
                                                    <i class="fa-solid fa-circle-info"></i>
                                                    $<?php echo number_format($pp['usd'], 2, '.', ','); ?>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Actions -->
                                        <td>
                                            <div class="home-action-buttons">
                                                <button class="home-btn-cart"
                                                    data-product-id="<?php echo esc_attr($pp['id']); ?>"
                                                    <?php echo !$pp['instock'] ? 'disabled' : ''; ?>>
                                                    <i class="fa-solid fa-cart-shopping"></i>
                                                </button>
                                                <button class="home-btn-buy"
                                                    data-product-id="<?php echo esc_attr($pp['id']); ?>"
                                                    <?php echo !$pp['instock'] ? 'disabled' : ''; ?>>
                                                    Mua ngay
                                                </button>
                                            </div>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5" style="text-align:center;padding:40px;">
                                        <p style="color:#666;font-size:16px;">Chưa có sản phẩm nào.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- View More Button -->
                <div class="home-proxy-list-footer">
                    <a href="#" class="home-btn-view-more">Xem thêm</a>
                </div>

            </div>
        </section>
    <?php endif; ?>

    <?php if (vieproxy_section_visible('why_choose')): ?>
        <!-- ═══════════════════════════════════════════════
         WHY CHOOSE SECTION
    ════════════════════════════════════════════════ -->
        <section class="home-why-choose-section">
            <!-- Decorative Circle Background -->
            <div class="home-why-circle home-why-circle--left"></div>

            <!-- Title & Description (inside wrapper) -->
            <div class="wrapper">
                <div class="home-why-inner">
                    <div class="home-why-content">
                        <h2 class="home-why-title">
                            <?php
                            $why_title_main = vieproxy_get_option('why_choose_title_main', 'VÌ SAO NÊN LỰA CHỌN');
                            $why_title_highlight = vieproxy_get_option('why_choose_title_highlight', 'VIEPROXY ?');
                            echo esc_html($why_title_main);
                            ?><br>
                            <span class="home-highlight-text"><?php echo esc_html($why_title_highlight); ?></span>
                        </h2>

                        <?php $why_desc = vieproxy_get_option('why_choose_description', 'Cung cấp cho bạn một mức giá hợp lý tại các đối tác proxy thông dụng với hình thức thanh toán dễ dàng và tiết kiệm nhất'); ?>
                        <?php if ($why_desc): ?>
                            <p class="home-why-description">
                                <?php echo esc_html($why_desc); ?>
                            </p>
                        <?php endif; ?>
                    </div><!-- /.home-why-content -->
                </div><!-- /.home-why-inner -->
            </div><!-- /.wrapper -->

            <!-- Cards Slider — Full Width (outside wrapper) -->
            <?php
            $cards = vieproxy_get_option('why_choose_cards', array());
            if (empty($cards)) {
                $cards = array(
                    array(
                        'title' => 'Dễ dàng sử dụng',
                        'description' => 'Bạn chỉ cần tạo tài khoản, lựa chọn nhà cung cấp Proxy mà bạn mong muốn, tiếp tục điền phần giá hàng và thực hiện thanh toán.',
                    ),
                    array(
                        'title' => 'Thủ tục nhanh chóng',
                        'description' => 'Việc kích hoạt Proxy bằng CProxy sẽ tối ưu hoá thời gian tạo và giảm rủi ro thông qua chúng tôi.',
                    ),
                    array(
                        'title' => 'Địa chỉ IP đa dạng',
                        'description' => 'Với hơn 190 quốc gia và hơn 2 nghìn trung tâm dữ liệu, cung cấp cho bạn một lựa chọn IP đa dạng.',
                    ),
                    array(
                        'title' => 'Hỗ trợ 24/7',
                        'description' => 'Đội ngũ hỗ trợ luôn sẵn sàng giải đáp mọi thắc mắc của bạn bất cứ lúc nào.',
                    ),
                );
            }
            ?>
            <div class="home-why-slider-container">
                <div class="home-why-slider">
                    <?php foreach ($cards as $card): ?>
                        <div class="home-why-card">
                            <h3 class="home-why-card-title">
                                <?php echo esc_html($card['title']); ?>
                            </h3>
                            <p class="home-why-card-desc">
                                <?php echo esc_html($card['description']); ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div><!-- /.home-why-slider -->

                <!-- Slider Navigation Lines -->
                <div class="home-why-slider-lines">
                    <?php foreach ($cards as $index => $card): ?>
                        <div class="home-why-line <?php echo $index === 0 ? 'is-active' : ''; ?>"></div>
                    <?php endforeach; ?>
                </div>
            </div><!-- /.home-why-slider-container -->

        </section>
    <?php endif; ?>


    <?php if (vieproxy_section_visible('commitment')): ?>
        <!-- ═══════════════════════════════════════════════
         COMMITMENT SECTION
        ════════════════════════════════════════════════ -->
        <section class="home-commitment-section">
            <!-- <div class="wrapper"> -->
            <div class="home-commitment-inner">

                <!-- Left: Text + Features -->
                <div class="home-commitment-content">

                    <h2 class="home-commitment-title">
                        <?php echo esc_html(vieproxy_get_option('commitment_title_main', 'CAM KẾT GIÁ')); ?><br>
                        <span class="home-commitment-title__big"><?php echo esc_html(vieproxy_get_option('commitment_title_highlight', 'TỐT NHẤT THỊ TRƯỜNG')); ?></span>
                    </h2>

                    <?php $desc = vieproxy_get_option('commitment_description', ''); ?>
                    <?php if ($desc): ?>
                        <p class="home-commitment-desc"><?php echo esc_html($desc); ?></p>
                    <?php endif; ?>

                    <!-- Feature list -->
                    <?php
                    $features = vieproxy_get_option('commitment_features', array());
                    if (empty($features)) {
                        $features = array(
                            array(
                                'title'       => 'Băng thông tốc độ cao',
                                'description' => 'Tốc độ thời gian thực có thể đạt đến 1M-5M/s,99% đảm bảo tỷ lệ thành công cho các hoạt động thu thập dữ liệu. Hỗ trợ tối đa nhu cầu của bạn.',
                                'icon'        => '<i class="fa-solid fa-gauge-high"></i>',
                            ),
                            array(
                                'title'       => 'An toàn, ổn định',
                                'description' => 'Tốc độ thời gian thực có thể đạt đến 1M-5M/s,99% đảm bảo tỷ lệ thành công cho các hoạt động thu thập dữ liệu. Hỗ trợ tối đa nhu cầu của bạn.',
                                'icon'        => '<i class="fa-solid fa-chart-simple"></i>',
                            ),
                        );
                    }
                    ?>

                    <ul class="home-commitment-features">
                        <?php foreach ($features as $feature):
                            if (!is_array($feature)) continue;

                            // Get icon value
                            $icon_html = isset($feature['icon']) ? trim($feature['icon']) : '';
                        ?>
                            <li class="home-commitment-feature">
                                <div class="home-commitment-feature__icon">
                                    <?php if (!empty($icon_html)): ?>
                                        <?php
                                        // Render icon HTML - cho phép thẻ <i> với class
                                        $allowed_html = array(
                                            'i' => array(
                                                'class' => array(),
                                                'aria-hidden' => array(),
                                            )
                                        );
                                        echo wp_kses($icon_html, $allowed_html);
                                        ?>
                                    <?php else: ?>
                                        <i class="fa-solid fa-circle-check"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="home-commitment-feature__body">
                                    <h4 class="home-commitment-feature__title">
                                        <?php echo esc_html(isset($feature['title']) ? $feature['title'] : ''); ?>
                                    </h4>
                                    <?php if (!empty($feature['description'])): ?>
                                        <p class="home-commitment-feature__desc">
                                            <?php echo esc_html($feature['description']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                </div><!-- /.home-commitment-content -->

                <!-- Right: Image -->
                <div class="home-commitment-image-wrap">
                    <?php $img = vieproxy_get_option('commitment_image', ''); ?>
                    <?php if ($img): ?>
                        <img src="<?php echo esc_url($img); ?>" alt="VieProxy commitment" class="home-commitment-image" />
                    <?php endif; ?>
                </div>

            </div><!-- /.home-commitment-inner -->
            <!-- </div> -->
        </section>
    <?php endif; ?>

    <?php if (vieproxy_section_visible('pricing')): ?>
        <!-- ═══════════════════════════════════════════════
         PRICING SECTION
        ════════════════════════════════════════════════ -->
        <section class="home-pricing-section">
            <div class="wrapper">
                <div class="home-pricing-section-inner">

                    <!-- Header -->
                    <div class="home-pricing-header">
                        <h2 class="home-pricing-title">
                            <?php echo esc_html(vieproxy_get_option('pricing_title_main', 'THUÊ PROXY GIÁ ƯU ĐÃI NHẤT TẠI')); ?>
                            <span class="home-pricing-title__highlight"><?php echo esc_html(vieproxy_get_option('pricing_title_highlight', 'VIEPROXY')); ?></span>
                        </h2>

                        <?php $desc = vieproxy_get_option('pricing_description', ''); ?>
                        <?php if ($desc): ?>
                            <p class="home-pricing-description">
                                <?php echo esc_html($desc); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Slider Container -->
                    <div class="home-pricing-slider-container">

                        <!-- Prev Arrow -->
                        <div class="home-pricing-slider__arrow home-pricing-slider__arrow--prev">
                            <i class="fa-solid fa-chevron-left"></i>
                        </div>

                        <!-- Slider Wrapper -->
                        <div class="home-pricing-slider-wrapper">
                            <div class="home-pricing-slider">

                                <?php
                                // ── Query sản phẩm ────────────────────────────────
                                $pricing_args = array(
                                    'post_type'      => 'product',
                                    'posts_per_page' => -1,
                                    'orderby'        => 'date',
                                    'order'          => 'DESC',
                                );

                                $pricing_query = new WP_Query($pricing_args);

                                if ($pricing_query->have_posts()) :
                                    while ($pricing_query->have_posts()) : $pricing_query->the_post();
                                        global $product;

                                        $pc_id   = get_the_ID();
                                        $pc_name = get_the_title();
                                        $pc_url  = get_permalink();

                                        // Logo / thumbnail
                                        $pc_thumb_id  = get_post_thumbnail_id();
                                        $pc_thumb_url = $pc_thumb_id
                                            ? wp_get_attachment_image_url($pc_thumb_id, 'thumbnail')
                                            : '';

                                        // VieProxy meta
                                        $pc_meta = vieproxy_get_product_meta($pc_id);

                                        // Mô tả ngắn
                                        $pc_desc = $pc_meta['sub_description'] ?? '';
                                        if (empty($pc_desc)) {
                                            $pc_desc = $product->get_short_description();
                                        }
                                        if (empty($pc_desc)) {
                                            $pc_desc = get_the_excerpt();
                                        }

                                        // Pricing
                                        $pc_pricing   = $pc_meta['pricing_config'] ?? [];
                                        $pc_ptype     = $pc_pricing['pricing_type'] ?? 'ip_time';
                                        $pc_usd_rate  = (float) get_option('vieproxy_usd_rate', 25000);
                                        if ($pc_usd_rate <= 0) $pc_usd_rate = 25000;

                                        $pc_plan_idx    = (int)($pc_pricing['default_plan_index'] ?? 0);
                                        $pc_default_qty = (int)($pc_pricing['default_qty'] ?? 1);
                                        $pc_unit_label  = $pc_pricing['unit_label'] ?? 'IPs';
                                        $pc_price_vnd   = 0;

                                        if ($pc_ptype === 'time_only') {
                                            $pc_time_plans = $pc_pricing['time_plans'] ?? [];
                                            if (empty($pc_time_plans)) continue;
                                            $pc_plan      = $pc_time_plans[$pc_plan_idx] ?? $pc_time_plans[0];
                                            $pc_price_vnd = (float)($pc_plan['price_vnd'] ?? 0);
                                            if (!$pc_price_vnd) {
                                                $pc_price_vnd = round((float)($pc_plan['price'] ?? 0) * $pc_usd_rate);
                                            }
                                            // Nhãn: "X.XXXđ / Tên gói"
                                            $pc_price_label = number_format($pc_price_vnd, 0, ',', '.') . 'đ'
                                                . ' / ' . ($pc_plan['label'] ?? 'Gói');
                                        } elseif ($pc_ptype === 'dollar_time') {
                                            // $ × Time: dollar_time_tiers + duration_plans_dt
                                            $pc_dtt_tiers = $pc_pricing['dollar_time_tiers'] ?? [];
                                            $pc_dt_plans  = $pc_pricing['duration_plans_dt'] ?? [];
                                            if (empty($pc_dtt_tiers) || empty($pc_dt_plans)) continue;
                                            $pc_dt_plan   = $pc_dt_plans[$pc_plan_idx] ?? $pc_dt_plans[0];
                                            $pc_months    = (float)($pc_dt_plan['months'] ?? 1);
                                            // Lấy mốc $ đầu tiên làm default để hiển thị
                                            $pc_dtt_tier  = $pc_dtt_tiers[0];
                                            $pc_dtt_qty   = (float)($pc_dtt_tier['qty'] ?? $pc_dtt_tier['label'] ?? 10);
                                            $pc_price_unit = (float)($pc_dtt_tier['price_per_unit'] ?? $pc_dtt_tier['amount'] ?? 0);
                                            $pc_price_usd  = $pc_months == 0
                                                ? $pc_dtt_qty * $pc_price_unit
                                                : $pc_dtt_qty * $pc_price_unit * $pc_months;
                                            $pc_price_vnd  = round($pc_price_usd * $pc_usd_rate);
                                            $pc_unit_label = $pc_pricing['dt_unit_label'] ?? '$';
                                            // Nhãn: "X.XXXđ / $Y"
                                            $pc_price_label = number_format($pc_price_vnd, 0, ',', '.') . 'đ'
                                                . ' / $' . (int)$pc_dtt_qty;
                                        } else {
                                            $pc_plans  = $pc_pricing['duration_plans'] ?? [];
                                            $pc_tiers  = $pc_pricing['qty_tiers']      ?? [];
                                            if (empty($pc_plans) || empty($pc_tiers)) continue;
                                            $pc_plan   = $pc_plans[$pc_plan_idx] ?? $pc_plans[0];
                                            $pc_months = (float)($pc_plan['months'] ?? 1);
                                            $pc_tier   = $pc_tiers[0];
                                            foreach ($pc_tiers as $t) {
                                                if ((int)$t['value'] === $pc_default_qty) {
                                                    $pc_tier = $t;
                                                    break;
                                                }
                                            }
                                            $pc_price_unit = (float)($pc_tier['price_per_unit'] ?? 0);
                                            $pc_price_usd  = $pc_months == 0
                                                ? $pc_default_qty * $pc_price_unit
                                                : $pc_default_qty * $pc_price_unit * $pc_months;
                                            $pc_price_vnd  = round($pc_price_usd * $pc_usd_rate);
                                            // Nhãn: "X.XXXđ / Y IPs"
                                            $pc_price_label = number_format($pc_price_vnd, 0, ',', '.') . 'đ'
                                                . ' / ' . $pc_default_qty . ' ' . $pc_unit_label;
                                        }

                                        // Features: ưu tiên meta → fallback mô tả ngắn thành bullet
                                        $pc_features = $pc_meta['features'] ?? [];
                                ?>

                                        <div class="pricing-card">

                                            <!-- Logo -->
                                            <div class="pricing-card__logo">
                                                <?php if ($pc_thumb_url) : ?>
                                                    <img src="<?php echo esc_url($pc_thumb_url); ?>"
                                                        alt="<?php echo esc_attr($pc_name); ?>"
                                                        style="width:70%;object-fit:contain;" />
                                                <?php endif; ?>
                                            </div>

                                            <!-- Top: blue bg -->
                                            <div class="pricing-card__top">
                                                <h3 class="pricing-card__title">
                                                    <?php echo esc_html($pc_name); ?>
                                                </h3>
                                                <?php if ($pc_desc) : ?>
                                                    <p class="pricing-card__desc">
                                                        <?php echo esc_html(wp_trim_words($pc_desc, 20, '...')); ?>
                                                    </p>
                                                <?php endif; ?>
                                                <div class="pricing-card__price">
                                                    <?php echo esc_html($pc_price_label); ?>
                                                </div>
                                            </div>

                                            <!-- Bottom: features + button -->
                                            <div class="pricing-card__bottom">
                                                <ul class="pricing-card__features">
                                                    <?php if (!empty($pc_features)) : ?>
                                                        <?php foreach ($pc_features as $feat) :
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
                                                        // Ưu tiên 1: _vieproxy_product_highlights từ plugin
                                                        $pc_highlights = get_post_meta($pc_id, '_vieproxy_product_highlights', true);
                                                        if (!empty($pc_highlights) && is_array($pc_highlights)) :
                                                            foreach ($pc_highlights as $highlight) :
                                                                $highlight = sanitize_text_field($highlight);
                                                                if (empty($highlight)) continue;
                                                        ?>
                                                                <li class="pricing-card__feature">
                                                                    <i class="fa-solid fa-circle-check"></i>
                                                                    <span><?php echo esc_html($highlight); ?></span>
                                                                </li>
                                                            <?php
                                                            endforeach;
                                                        else :
                                                            // Ưu tiên 2: lấy từ product attributes WooCommerce
                                                            $pc_attrs = $product->get_attributes();
                                                            $feat_count = 0;
                                                            foreach ($pc_attrs as $attr) :
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

                                                            // Ưu tiên 3: fallback text tĩnh
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
                                                    <?php endif; ?>
                                                </ul>

                                                <a href="<?php echo esc_url($pc_url); ?>" class="pricing-card__btn">
                                                    Mua ngay
                                                </a>
                                            </div>

                                        </div>

                                    <?php
                                    endwhile;
                                    wp_reset_postdata();
                                else :
                                    ?>
                                    <p style="color:#666;padding:40px;text-align:center;">Chưa có sản phẩm nào.</p>
                                <?php endif; ?>
                            </div><!-- /.home-pricing-slider -->
                        </div><!-- /.home-pricing-slider-wrapper -->

                        <!-- Next Arrow -->
                        <div class="home-pricing-slider__arrow home-pricing-slider__arrow--next">
                            <i class="fa-solid fa-chevron-right"></i>
                        </div>

                    </div><!-- /.home-pricing-slider-container -->

                    <!-- Slider Controls (Dots only) -->
                    <div class="home-pricing-slider-controls">
                        <div class="home-pricing-slider__dots">
                            <div class="home-pricing-slider__dot is-active"></div>
                            <div class="home-pricing-slider__dot"></div>
                        </div>
                    </div>

                </div><!-- /.home-pricing-section-inner -->
            </div><!-- /.wrapper -->
        </section>
    <?php endif; ?>

    <?php if (vieproxy_section_visible('coverage')): ?>
        <section class="home-coverage-section">
            <div class="wrapper">
                <div class="home-coverage-inner">

                    <!-- Header -->
                    <div class="home-coverage-header">
                        <h2 class="home-coverage-title">
                            <?php echo esc_html(vieproxy_get_option('coverage_title_main', 'IP PROXY DÂN CƯ TỪ KHẮP NƠI')); ?>
                            <span class="home-coverage-title__highlight"><?php echo esc_html(vieproxy_get_option('coverage_title_highlight', 'TRÊN THẾ GIỚI')); ?></span>
                        </h2>
                        <?php $coverage_desc = vieproxy_get_option('coverage_description', ''); ?>
                        <?php if ($coverage_desc): ?>
                            <p class="home-coverage-description"><?php echo esc_html($coverage_desc); ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- World Map -->
                    <div class="home-coverage-map">

                        <!-- SVG dotted world map -->
                        <div class="home-coverage-map__bg">
                            <img
                                class="home-coverage-map__svg"
                                src="<?php echo get_stylesheet_directory_uri(); ?>/images/global-map.png"
                                alt="Global coverage map"
                                loading="lazy" />
                        </div>

                        <!-- Location Pins - static -->
                        <div class="home-coverage-map__pins">

                            <div class="coverage-pin" style="left: 22%; top: 28%;">
                                <div class="coverage-pin__dot"></div>
                                <div class="coverage-pin__label">
                                    <img src="https://flagcdn.com/w20/ca.png" width="16" height="12" alt="Canada" loading="lazy">
                                    <span>Canada</span>
                                </div>
                            </div>

                            <div class="coverage-pin" style="left: 18%; top: 42%;">
                                <div class="coverage-pin__dot"></div>
                                <div class="coverage-pin__label">
                                    <img src="https://flagcdn.com/w20/us.png" width="16" height="12" alt="United States" loading="lazy">
                                    <span>United States</span>
                                </div>
                            </div>

                            <div class="coverage-pin" style="left: 28%; top: 60%;">
                                <div class="coverage-pin__dot"></div>
                                <div class="coverage-pin__label">
                                    <img src="https://flagcdn.com/w20/br.png" width="16" height="12" alt="Brazil" loading="lazy">
                                    <span>Brazil</span>
                                </div>
                            </div>

                            <div class="coverage-pin" style="left: 52%; top: 20%;">
                                <div class="coverage-pin__dot"></div>
                                <div class="coverage-pin__label">
                                    <img src="https://flagcdn.com/w20/gb.png" width="16" height="12" alt="UK" loading="lazy">
                                    <span>UK</span>
                                </div>
                            </div>

                            <div class="coverage-pin" style="left: 67%; top: 32%;">
                                <div class="coverage-pin__dot"></div>
                                <div class="coverage-pin__label">
                                    <img src="https://flagcdn.com/w20/ae.png" width="16" height="12" alt="UAE" loading="lazy">
                                    <span>UAE</span>
                                </div>
                            </div>

                            <div class="coverage-pin" style="left: 73%; top: 48%;">
                                <div class="coverage-pin__dot"></div>
                                <div class="coverage-pin__label">
                                    <img src="https://flagcdn.com/w20/in.png" width="16" height="12" alt="India" loading="lazy">
                                    <span>India</span>
                                </div>
                            </div>

                            <div class="coverage-pin coverage-pin--right" style="left: 95%; top: 40%;">
                                <div class="coverage-pin__dot"></div>
                                <div class="coverage-pin__label">
                                    <img src="https://flagcdn.com/w20/jp.png" width="16" height="12" alt="Japan" loading="lazy">
                                    <span>Japan</span>
                                </div>
                            </div>

                            <div class="coverage-pin coverage-pin--right" style="left: 85%; top: 56%;">
                                <div class="coverage-pin__dot"></div>
                                <div class="coverage-pin__label">
                                    <img src="https://flagcdn.com/w20/au.png" width="16" height="12" alt="Australia" loading="lazy">
                                    <span>Australia</span>
                                </div>
                            </div>

                        </div><!-- /.home-coverage-map__pins -->
                    </div><!-- /.home-coverage-map -->

                </div><!-- /.home-coverage-inner -->
            </div><!-- /.wrapper -->
        </section>
    <?php endif; ?>

    <?php if (vieproxy_section_visible('rating')): ?>
        <!-- ═══════════════════════════════════════════════
         RATING SECTION - Đánh giá từ khách hàng
    ════════════════════════════════════════════════ -->
        <section class="home-rating-section">
            <div class="wrapper home-rating-inner">

                <!-- Left: Vertical Slider -->
                <div class="home-rating-slider-wrapper">
                    <?php
                    $rating_cards = vieproxy_get_option('rating_cards', array());

                    // Default cards if none set
                    if (empty($rating_cards)) {
                        $rating_cards = array(
                            array(
                                'avatar' => '',
                                'name' => 'Sarah J.',
                                'position' => 'Digital Marketer',
                                'comment' => 'Great service! Fast, reliable proxies and excellent customer support. I have been using for 3 months and very satisfied.',
                            ),
                            array(
                                'avatar' => '',
                                'name' => 'Michael C.',
                                'position' => 'E-commerce Owner',
                                'comment' => 'Outstanding connection quality and great value for price. Highly recommend for anyone needing stable proxies.',
                            ),
                            array(
                                'avatar' => '',
                                'name' => 'Emma W.',
                                'position' => 'SEO Specialist',
                                'comment' => 'VieProxy has been a game changer for my SEO projects. Fast speeds and never blocked. Worth every penny!',
                            ),
                            array(
                                'avatar' => '',
                                'name' => 'David M.',
                                'position' => 'Data Analyst',
                                'comment' => 'Excellent value for money. The proxy rotation is seamless and I can manage everything easily through the dashboard.',
                            ),
                            array(
                                'avatar' => '',
                                'name' => 'Lisa A.',
                                'position' => 'Social Media Manager',
                                'comment' => 'Perfect for managing multiple accounts. The IPs are clean and stable. Great customer service team!',
                            ),
                            array(
                                'avatar' => '',
                                'name' => 'James W.',
                                'position' => 'Web Developer',
                                'comment' => 'Best proxy service I have used. The proxies are reliable and fast. No complaints at all!',
                            ),
                        );
                    }

                    // Split cards into two columns
                    $total_cards = count($rating_cards);
                    $half = ceil($total_cards / 2);
                    $left_cards = array_slice($rating_cards, 0, $half);
                    $right_cards = array_slice($rating_cards, $half);
                    ?>

                    <div class="home-rating-columns">

                        <!-- Left Column (Scroll Down) -->
                        <div class="home-rating-column home-rating-column--left">
                            <div class="home-rating-column__track">
                                <?php foreach ($left_cards as $card): ?>
                                    <div class="home-rating-card">
                                        <div class="home-rating-card__quote">
                                            <i class="fa-solid fa-quote-left"></i>
                                        </div>
                                        <p class="home-rating-card__comment">
                                            <?php echo esc_html($card['comment']); ?>
                                        </p>
                                        <div class="home-rating-card__user">
                                            <div class="home-rating-card__avatar">
                                                <?php if (!empty($card['avatar'])): ?>
                                                    <img src="<?php echo esc_url($card['avatar']); ?>" alt="<?php echo esc_attr($card['name']); ?>">
                                                <?php else: ?>
                                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($card['name']); ?>&background=60a5fa&color=fff&size=80&bold=true" alt="<?php echo esc_attr($card['name']); ?>">
                                                <?php endif; ?>
                                            </div>
                                            <div class="home-rating-card__info">
                                                <h4 class="home-rating-card__name"><?php echo esc_html($card['name']); ?></h4>
                                                <p class="home-rating-card__position"><?php echo esc_html($card['position']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Right Column (Scroll Up) -->
                        <?php if (!empty($right_cards)): ?>
                            <div class="home-rating-column home-rating-column--right">
                                <div class="home-rating-column__track">
                                    <?php foreach ($right_cards as $card): ?>
                                        <div class="home-rating-card">
                                            <div class="home-rating-card__quote">
                                                <i class="fa-solid fa-quote-left"></i>
                                            </div>
                                            <p class="home-rating-card__comment">
                                                <?php echo esc_html($card['comment']); ?>
                                            </p>
                                            <div class="home-rating-card__user">
                                                <div class="home-rating-card__avatar">
                                                    <?php if (!empty($card['avatar'])): ?>
                                                        <img src="<?php echo esc_url($card['avatar']); ?>" alt="<?php echo esc_attr($card['name']); ?>">
                                                    <?php else: ?>
                                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($card['name']); ?>&background=60a5fa&color=fff&size=80&bold=true" alt="<?php echo esc_attr($card['name']); ?>">
                                                    <?php endif; ?>
                                                </div>
                                                <div class="home-rating-card__info">
                                                    <h4 class="home-rating-card__name"><?php echo esc_html($card['name']); ?></h4>
                                                    <p class="home-rating-card__position"><?php echo esc_html($card['position']); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div><!-- /.home-rating-columns -->
                </div><!-- /.home-rating-slider-wrapper -->

                <!-- Right: Header Content -->
                <div class="home-rating-header">
                    <h2 class="home-rating-title">
                        <span class="home-rating-title__main">
                            <?php echo esc_html(vieproxy_get_option('rating_title_main', 'ĐÁNH GIÁ TỪ')); ?>
                        </span>
                        <span class="home-rating-title__highlight">
                            <?php echo esc_html(vieproxy_get_option('rating_title_highlight', 'KHÁCH HÀNG')); ?>
                        </span>
                    </h2>
                    <?php $rating_desc = vieproxy_get_option('rating_description', ''); ?>
                    <?php if ($rating_desc): ?>
                        <p class="home-rating-description"><?php echo esc_html($rating_desc); ?></p>
                    <?php endif; ?>
                </div>

            </div><!-- /.home-rating-inner -->
        </section>
    <?php endif; ?>
	
	<?php if (vieproxy_section_visible('partners')): ?>
        <!-- ═══════════════════════════════════════════════
             PARTNERS SECTION — data từ API tradeproxy
        ════════════════════════════════════════════════ -->
        <?php
        $partners_title_main      = vieproxy_get_option('partners_title_main',      'ĐỐI TÁC CỦA');
        $partners_title_highlight = vieproxy_get_option('partners_title_highlight',  'VIEPROXY');
        $partners_description     = vieproxy_get_option('partners_description',      'Khám phá hệ sinh thái đối tác đa dạng — trình duyệt ẩn danh, công cụ tự động hóa, giải pháp captcha và nhiều hơn nữa, tất cả tích hợp hoàn hảo với dịch vụ proxy của chúng tôi.');

        // ── Fetch providers từ API (giống TradeProxy) ──────────────────────
        $providers = function_exists('get_providers_from_api') ? get_providers_from_api() : [];

        if (!empty($providers)) :

            // Chia providers thành 2 hàng (chẵn → row1, lẻ → row2)
            $row1_providers = [];
            $row2_providers = [];
            foreach ($providers as $index => $provider) {
                if ($index % 2 === 0) {
                    $row1_providers[] = $provider;
                } else {
                    $row2_providers[] = $provider;
                }
            }

            // Nhân đôi để infinite scroll không bị hụt
            $row1_providers = array_merge($row1_providers, $row1_providers);
            $row2_providers = array_merge($row2_providers, $row2_providers);
        ?>
            <section class="home-partners">
                <div class="container">

                    <!-- Header -->
                    <div class="section-header">
                        <h2 class="section-title">
                            <span class="main"><?php echo esc_html($partners_title_main); ?></span>
                            <span class="highlight"><?php echo esc_html($partners_title_highlight); ?></span>
                        </h2>
                        <p class="section-description">
                            <?php echo esc_html($partners_description); ?>
                        </p>
                    </div>

                    <!-- Partners Slider — giữ nguyên structure VieProxy -->
                    <div class="partners-slider-container">

                        <!-- Row 1: Trái → Phải -->
                        <div class="partners-slider-row partners-slider-row-1">
                            <?php foreach ($row1_providers as $provider) :
                                $name = $provider['title']['rendered'] ?? 'Partner';
                                $slug = $provider['slug'] ?? '';
                                $logo = $provider['provider_data']['logo'] ?? '';

                                // URL → trang single partner của vieproxy
                                $partner_url = $slug
                                    ? home_url('/partners/' . $slug . '/')
                                    : home_url('/partners/');
                            ?>
                                <?php if (!empty($logo)) : ?>
                                    <a href="<?php echo esc_url($partner_url); ?>"
                                       class="partner-logo"
                                       title="<?php echo esc_attr($name); ?>">
                                        <img src="<?php echo esc_url($logo); ?>"
                                             alt="<?php echo esc_attr($name); ?>"
                                             loading="lazy" />
                                    </a>
                                <?php else : ?>
                                    <a href="<?php echo esc_url($partner_url); ?>"
                                       class="partner-logo"
                                       title="<?php echo esc_attr($name); ?>">
                                        <span class="partner-logo__fallback">
                                            <?php echo esc_html(strtoupper(substr($name, 0, 2))); ?>
                                        </span>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>

                        <!-- Row 2: Phải → Trái -->
                        <?php if (!empty($row2_providers)) : ?>
                            <div class="partners-slider-row partners-slider-row-2">
                                <?php foreach ($row2_providers as $provider) :
                                    $name = $provider['title']['rendered'] ?? 'Partner';
                                    $slug = $provider['slug'] ?? '';
                                    $logo = $provider['provider_data']['logo'] ?? '';

                                    $partner_url = $slug
                                        ? home_url('/partners/' . $slug . '/')
                                        : home_url('/partners/');
                                ?>
                                    <?php if (!empty($logo)) : ?>
                                        <a href="<?php echo esc_url($partner_url); ?>"
                                           class="partner-logo"
                                           title="<?php echo esc_attr($name); ?>">
                                            <img src="<?php echo esc_url($logo); ?>"
                                                 alt="<?php echo esc_attr($name); ?>"
                                                 loading="lazy" />
                                        </a>
                                    <?php else : ?>
                                        <a href="<?php echo esc_url($partner_url); ?>"
                                           class="partner-logo"
                                           title="<?php echo esc_attr($name); ?>">
                                            <span class="partner-logo__fallback">
                                                <?php echo esc_html(strtoupper(substr($name, 0, 2))); ?>
                                            </span>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    </div><!-- /.partners-slider-container -->

                    <!-- View More Button -->
                    <div class="home-proxy-list-footer">
                        <a href="<?php echo esc_url(home_url('/partners')); ?>"
                           class="home-btn-view-more">Xem thêm</a>
                    </div>

                </div>
            </section>

        <?php
        else :
            // Fallback: nếu API không trả về data → dùng lại partners_logos từ WP options
            $partners_logos = vieproxy_get_option('partners_logos', []);
            if (!empty($partners_logos)) :
                $total = count($partners_logos);
                $half  = ceil($total / 2);
                $row1_logos = array_slice($partners_logos, 0, $half);
                $row2_logos = array_slice($partners_logos, $half);
        ?>
            <section class="home-partners">
                <div class="container">
                    <div class="section-header">
                        <h2 class="section-title">
                            <span class="main"><?php echo esc_html($partners_title_main); ?></span>
                            <span class="highlight"><?php echo esc_html($partners_title_highlight); ?></span>
                        </h2>
                        <p class="section-description"><?php echo esc_html($partners_description); ?></p>
                    </div>
                    <div class="partners-slider-container">
                        <div class="partners-slider-row partners-slider-row-1">
                            <?php foreach ($row1_logos as $item) :
                                if (empty($item['logo'])) continue;
                                $url  = !empty($item['url']) ? $item['url'] : '#';
                                $name = !empty($item['name']) ? $item['name'] : 'Partner';
                            ?>
                                <a href="<?php echo esc_url($url); ?>" class="partner-logo">
                                    <img src="<?php echo esc_url($item['logo']); ?>"
                                         alt="<?php echo esc_attr($name); ?>" />
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <?php if (!empty($row2_logos)) : ?>
                            <div class="partners-slider-row partners-slider-row-2">
                                <?php foreach ($row2_logos as $item) :
                                    if (empty($item['logo'])) continue;
                                    $url  = !empty($item['url']) ? $item['url'] : '#';
                                    $name = !empty($item['name']) ? $item['name'] : 'Partner';
                                ?>
                                    <a href="<?php echo esc_url($url); ?>" class="partner-logo">
                                        <img src="<?php echo esc_url($item['logo']); ?>"
                                             alt="<?php echo esc_attr($name); ?>" />
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="home-proxy-list-footer">
                        <a href="<?php echo esc_url(home_url('/partners')); ?>"
                           class="home-btn-view-more">Xem thêm</a>
                    </div>
                </div>
            </section>
        <?php
            endif;
        endif;
        ?>
    <?php endif; ?>

<!--     <?php if (vieproxy_section_visible('partners')): ?>
        <!-- ═══════════════════════════════════════════════
         PARTNERS SECTION
        ════════════════════════════════════════════════ -->
        <?php
        $partners_title_main = vieproxy_get_option('partners_title_main', 'ĐỐI TÁC CỦA');
        $partners_title_highlight = vieproxy_get_option('partners_title_highlight', 'VIEPROXY');
        $partners_description = vieproxy_get_option('partners_description', 'TradeProxy cung cấp đại IP proxy đa dạng, phủ sóng hơn 200 quốc gia và khu vực trên toàn cầu, nhằm hỗ trợ ẩn danh an toàn một cách tối ưu.');
        $partners_logos = vieproxy_get_option('partners_logos', array());

        // Nếu có logo
        if (!empty($partners_logos)):
            // Chia logo thành 2 hàng
            $total = count($partners_logos);
            $half = ceil($total / 2);
            $row1_logos = array_slice($partners_logos, 0, $half);
            $row2_logos = array_slice($partners_logos, $half);
        ?>
            <section class="home-partners">
                <div class="container">

                    <!-- Header -->
                    <div class="section-header">
                        <h2 class="section-title">
                            <span class="main"><?php echo esc_html($partners_title_main); ?></span>
                            <span class="highlight"><?php echo esc_html($partners_title_highlight); ?></span>
                        </h2>
                        <p class="section-description">
                            <?php echo esc_html($partners_description); ?>
                        </p>
                    </div>

                    <!-- Partners Slider -->
                    <div class="partners-slider-container">

                        <!-- Row 1: Trái → Phải -->
                        <div class="partners-slider-row partners-slider-row-1">
                            <?php foreach ($row1_logos as $item): ?>
                                <?php if (!empty($item['logo'])): ?>
                                    <?php
                                    $partner_url = !empty($item['url']) ? $item['url'] : '#';
                                    $partner_name = !empty($item['name']) ? $item['name'] : 'Partner';
                                    $has_url = !empty($item['url']) && $item['url'] !== '#';
                                    ?>

                                    <?php if ($has_url): ?>
                                        <a href="<?php echo esc_url($partner_url); ?>" class="partner-logo" target="_blank" rel="noopener noreferrer">
                                            <img src="<?php echo esc_url($item['logo']); ?>" alt="<?php echo esc_attr($partner_name); ?>" />
                                        </a>
                                    <?php else: ?>
                                        <div class="partner-logo">
                                            <img src="<?php echo esc_url($item['logo']); ?>" alt="<?php echo esc_attr($partner_name); ?>" />
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>

                        <!-- Row 2: Phải → Trái -->
                        <?php if (!empty($row2_logos)): ?>
                            <div class="partners-slider-row partners-slider-row-2">
                                <?php foreach ($row2_logos as $item): ?>
                                    <?php if (!empty($item['logo'])): ?>
                                        <?php
                                        $partner_url = !empty($item['url']) ? $item['url'] : '#';
                                        $partner_name = !empty($item['name']) ? $item['name'] : 'Partner';
                                        $has_url = !empty($item['url']) && $item['url'] !== '#';
                                        ?>

                                        <?php if ($has_url): ?>
                                            <a href="<?php echo esc_url($partner_url); ?>" class="partner-logo" target="_blank" rel="noopener noreferrer">
                                                <img src="<?php echo esc_url($item['logo']); ?>" alt="<?php echo esc_attr($partner_name); ?>" />
                                            </a>
                                        <?php else: ?>
                                            <div class="partner-logo">
                                                <img src="<?php echo esc_url($item['logo']); ?>" alt="<?php echo esc_attr($partner_name); ?>" />
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    </div>

                    <!-- View More Button -->
                    <div class="home-proxy-list-footer">
                        <a href="<?php echo esc_url(home_url('/archive-partners')); ?>" class="home-btn-view-more">Xem thêm</a>
                    </div>

                </div>
            </section>
        <?php endif; ?>
    <?php endif; ?>
 -->
    <!-- ═══════════════════════════════════════════════
         DECORATIVE CIRCLE - Between Partners & FAQ
        ════════════════════════════════════════════════ -->
    <div class="section-divider-circle"></div>

    <?php if (vieproxy_section_visible('faq')): ?>
        <!-- ═══════════════════════════════════════════════
         FAQ SECTION
        ════════════════════════════════════════════════ -->
        <?php
        $faq_title_main = vieproxy_get_option('faq_title_main', 'CÂU HỎI');
        $faq_title_highlight = vieproxy_get_option('faq_title_highlight', 'THƯỜNG GẶP');
        $faq_description = vieproxy_get_option('faq_description', 'Bạn đang có thắc mắc về các dịch vụ? Trade Proxy sẽ giúp bạn giải đáp nhanh chóng các câu hỏi được quan tâm nhiều nhất.');
        $faq_items = vieproxy_get_option('faq_items', array());

        // Nếu có FAQ
        if (!empty($faq_items)):
        ?>
            <section class="home-faq">
                <div class="wrapper">
                    <div class="faq-grid">

                        <!-- Left: Header (Sticky) -->
                        <div class="faq-header-col">
                            <div class="faq-header-sticky">
                                <h2 class="section-title">
                                    <span class="main"><?php echo esc_html($faq_title_main); ?></span>
                                    <span class="highlight"><?php echo esc_html($faq_title_highlight); ?></span>
                                </h2>
                                <p class="section-description">
                                    <?php echo esc_html($faq_description); ?>
                                </p>
                            </div>
                        </div>

                        <!-- Right: FAQ List -->
                        <div class="faq-list-col">
                            <div class="faq-list">
                                <?php foreach ($faq_items as $index => $item): ?>
                                    <?php
                                    $question = isset($item['question']) ? $item['question'] : '';
                                    $answer = isset($item['answer']) ? $item['answer'] : '';

                                    if (empty($question) || empty($answer)) {
                                        continue;
                                    }
                                    ?>

                                    <div class="faq-item" data-faq-index="<?php echo esc_attr($index); ?>">
                                        <!-- Question (clickable) -->
                                        <div class="faq-question" role="button" tabindex="0" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                                            <span class="faq-question-text">
                                                <?php echo esc_html($question); ?>
                                            </span>
                                            <span class="faq-icon">
                                                <i class="fa-solid fa-chevron-down"></i>
                                            </span>
                                        </div>

                                        <!-- Answer (collapsible) -->
                                        <div class="faq-answer">
                                            <p class="faq-answer-text">
                                                <?php echo nl2br(esc_html($answer)); ?>
                                            </p>
                                        </div>
                                    </div>

                                <?php endforeach; ?>
                            </div>
                        </div>

                    </div><!-- /.faq-grid -->
                </div>
            </section>
        <?php endif; ?>
    <?php endif; ?>


</main>

<?php get_footer(); ?>