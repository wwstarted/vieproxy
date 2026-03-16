<?php
/*
 Template Name: Archive Proxies V2
*/
?>

<?php get_header(); ?>

<?php
// ── SSR Params ────────────────────────────────────────────
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$posts_per_page = 12;

$selected_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : 'all';
$search_term = isset($_GET['proxy_s']) ? sanitize_text_field($_GET['proxy_s']) : '';
$sort_by = isset($_GET['sort']) ? sanitize_text_field($_GET['sort']) : 'bestseller';

// ── Map sort param → WP_Query orderby ────────────────────
$orderby = 'date';
$order = 'DESC';

switch ($sort_by) {
    case 'latest':
        $orderby = 'date';
        $order = 'DESC';
        break;
    case 'name-az':
        $orderby = 'title';
        $order = 'ASC';
        break;
    case 'name-za':
        $orderby = 'title';
        $order = 'DESC';
        break;
    case 'price-low':
    case 'price-high':
        // Sắp xếp giá xử lý sau loop (PHP usort)
        $orderby = 'date';
        $order = 'DESC';
        break;
    case 'bestseller':
    default:
        $orderby = 'date';
        $order = 'DESC';
        break;
}

// ── WP_Query args ─────────────────────────────────────────
$args = [
    'post_type' => 'product',
    'posts_per_page' => $posts_per_page,
    'paged' => $paged,
    'post_status' => 'publish',
    'orderby' => $orderby,
    'order' => $order,
];

if ($selected_category !== 'all' && !empty($selected_category)) {
    $args['tax_query'] = [
        [
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => $selected_category,
        ]
    ];
}

if (!empty($search_term)) {
    $args['s'] = $search_term;
}

$products_query = new WP_Query($args);

// ── Lấy categories cho sidebar ────────────────────────────
$categories = get_terms([
    'taxonomy' => 'product_cat',
    'hide_empty' => true,
]);

$cat_icons = [
    'proxy-dan-cu-isp' => 'fa-house',
    'proxy-dan-cu-traffic' => 'fa-arrow-trend-up',
    'proxy-xoay-dan-cu' => 'fa-rotate',
    'proxy-co-dinh' => 'fa-house-signal',
    'proxy-unlimited' => 'fa-house-laptop',
    'modem-proxy' => 'fa-tower-cell',
    'phan-mem-mmo' => 'fa-circle-dot',
];

// ── Helper: build filter URL ──────────────────────────────
function vp_filter_url($params = [])
{
    $base = remove_query_arg(['category', 'proxy_s', 'sort', 'paged']);
    return add_query_arg(array_filter($params, fn($v) => $v !== '' && $v !== null && $v !== 'all'), $base);
}

// ── USD rate (dùng chung toàn file) ──────────────────────
$usd_rate = (float) get_option('vieproxy_usd_rate', 25000);
if ($usd_rate <= 0)
    $usd_rate = 25000;

// ── Hero description ──────────────────────────────────────
$hero_desc = '';
if (is_page())
    $hero_desc = get_the_excerpt();
if (empty($hero_desc)) {
    global $post;
    $hero_desc = get_post_meta(get_the_ID(), '_proxy_archive_description', true);
}
if (empty($hero_desc)) {
    $hero_desc = 'Khám phá danh mục Proxy với đa dạng giải pháp IP chất lượng cao, bảo mật và ổn định, đáp ứng mọi nhu cầu từ ẩn danh, thu thập dữ liệu một cách an toàn và hiệu quả.';
}

// ── Collect products (loop + pricing) ─────────────────────
$page_products = [];

if ($products_query->have_posts()):
    while ($products_query->have_posts()):
        $products_query->the_post();

        $product_id = get_the_ID();
        $product_name = get_the_title();
        $product_url = get_permalink();

        $thumb_id = get_post_thumbnail_id();
        $thumb_url = $thumb_id
            ? wp_get_attachment_image_url($thumb_id, 'medium')
            : get_template_directory_uri() . '/images/placeholder.png';

        // ── Pricing: dùng vieproxy_get_product_meta() giống home ──
        $vp_meta = function_exists('vieproxy_get_product_meta')
            ? vieproxy_get_product_meta($product_id)
            : [];
        $pricing_cfg = $vp_meta['pricing_config'] ?? [];

        // Skip nếu hoàn toàn không có config
        if (empty($pricing_cfg)) {
            continue;
        }

        $ptype = $pricing_cfg['pricing_type'] ?? 'ip_time';
        $default_plan_idx = (int) ($pricing_cfg['default_plan_index'] ?? 0);
        $default_qty = (int) ($pricing_cfg['default_qty'] ?? 1);
        $unit_label = $pricing_cfg['unit_label'] ?? 'IPs';
        $price_usd = 0;
        $price_vnd = 0;

        if ($ptype === 'time_only') {
            // ── time_only: time_plans[] ───────────────────────────
            $time_plans = $pricing_cfg['time_plans'] ?? [];
            if (empty($time_plans))
                continue;

            $plan = $time_plans[$default_plan_idx] ?? $time_plans[0];
            $price_vnd = (float) ($plan['price_vnd'] ?? 0);
            if (!$price_vnd) {
                $price_vnd = round((float) ($plan['price'] ?? 0) * $usd_rate);
            }
            $price_usd = round($price_vnd / $usd_rate, 2);
            $unit_label = $plan['label'] ?? $unit_label;

        } elseif ($ptype === 'dollar_time') {
            // ── dollar_time: dollar_time_tiers[] × duration_plans_dt[] ──
            $dtt_tiers = $pricing_cfg['dollar_time_tiers'] ?? [];
            $dt_plans = $pricing_cfg['duration_plans_dt'] ?? [];
            if (empty($dtt_tiers) || empty($dt_plans))
                continue;

            $dt_plan = $dt_plans[$default_plan_idx] ?? $dt_plans[0];
            $months = (float) ($dt_plan['months'] ?? 1);
            $dtt_tier = $dtt_tiers[0];
            $dtt_qty = (float) ($dtt_tier['qty'] ?? $dtt_tier['label'] ?? 10);
            $price_unit = (float) ($dtt_tier['price_per_unit'] ?? $dtt_tier['amount'] ?? 0);

            $price_usd = ($months == 0)
                ? $dtt_qty * $price_unit
                : $dtt_qty * $price_unit * $months;
            $price_vnd = round($price_usd * $usd_rate);
            $unit_label = $pricing_cfg['dt_unit_label'] ?? '$';

        } else {
            // ── ip_time / gb_time / default: qty_tiers[] × duration_plans[] ──
            $plans = $pricing_cfg['duration_plans'] ?? [];
            $tiers = $pricing_cfg['qty_tiers'] ?? [];
            if (empty($plans) || empty($tiers))
                continue;

            $plan = $plans[$default_plan_idx] ?? $plans[0];
            $months = (float) ($plan['months'] ?? 1);

            // Tìm tier khớp default_qty, fallback tier đầu tiên
            $tier = $tiers[0];
            foreach ($tiers as $t) {
                if ((int) ($t['value'] ?? 0) === $default_qty) {
                    $tier = $t;
                    break;
                }
            }

            $price_per_unit = (float) ($tier['price_per_unit'] ?? 0);
            $price_usd = ($months == 0)
                ? $default_qty * $price_per_unit
                : $default_qty * $price_per_unit * $months;
            $price_vnd = round($price_usd * $usd_rate);
        }

        // ── Features — 4 tầng fallback (giống home) ───────────────
        $features = [];

        // Tầng 1: vieproxy_get_product_meta → features
        if (!empty($vp_meta['features'])) {
            $features = $vp_meta['features'];
        }

        // Tầng 2: _vieproxy_product_highlights
        if (empty($features)) {
            $highlights = get_post_meta($product_id, '_vieproxy_product_highlights', true);
            if (!empty($highlights) && is_array($highlights)) {
                $features = array_values(
                    array_filter(array_map('sanitize_text_field', $highlights))
                );
            }
        }

        // Tầng 3: WooCommerce product attributes (tối đa 3)
        if (empty($features)) {
            $_product = wc_get_product($product_id);
            if ($_product) {
                $count = 0;
                foreach ($_product->get_attributes() as $attr) {
                    if ($count >= 3)
                        break;
                    $attr_name = wc_attribute_label($attr->get_name());
                    $terms = $attr->get_terms();
                    $attr_value = $terms ? implode(', ', wp_list_pluck($terms, 'name')) : '';
                    if (empty($attr_value))
                        continue;
                    $features[] = $attr_name . ': ' . $attr_value;
                    $count++;
                }
            }
        }

        // Tầng 4: Static fallback
        if (empty($features)) {
            $features = [
                'Kết nối ổn định, tốc độ cao',
                'Hỗ trợ HTTP, SOCKS5',
                'Hỗ trợ 24/7',
            ];
        }

        $page_products[] = compact(
            'product_id',
            'product_name',
            'product_url',
            'thumb_url',
            'price_usd',
            'price_vnd',
            'default_qty',
            'unit_label',
            'features'
        );

    endwhile;
    wp_reset_postdata();

    // Sort theo giá sau khi có dữ liệu đầy đủ
    if ($sort_by === 'price-low') {
        usort($page_products, fn($a, $b) => $a['price_vnd'] <=> $b['price_vnd']);
    } elseif ($sort_by === 'price-high') {
        usort($page_products, fn($a, $b) => $b['price_vnd'] <=> $a['price_vnd']);
    }

endif;

// ── Count chính xác sau khi loop ─────────────────────────
// found_posts = tổng DB (dùng cho pagination)
// actual_count = số thực tế render được trên trang này (sau khi skip)
$total_in_db = $products_query->found_posts;
$actual_count = count($page_products);
$start_display = ($paged - 1) * $posts_per_page + 1;
$end_display = $start_display + $actual_count - 1;
?>

<!-- ══════════════════════════════════════════
     HERO BANNER
══════════════════════════════════════════ -->
<section class="proxy-hero">
    <div class="wrapper">
        <nav class="proxy-hero__breadcrumb" aria-label="breadcrumb">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="breadcrumb-link">Trang chủ</a>
            <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
            <span class="breadcrumb-current">Mua Proxy</span>
        </nav>
        <h1 class="proxy-hero__title">MUA PROXY</h1>
        <p class="proxy-hero__desc">
            <?php echo esc_html($hero_desc); ?>
        </p>
    </div>
</section>

<!-- ══════════════════════════════════════════
     MAIN CONTENT
══════════════════════════════════════════ -->
<div class="main-content bg-[#f9fdff]">
    <div class="product-container-v2 wrapper">

        <!-- Page Title Row -->
        <div class="product-header-v2">
            <h2 class="product-title-v2">Danh sách sản phẩm</h2>
            <button class="filter-btn-mobile" id="openFilterModal">
                <i class="fa-solid fa-sliders"></i>
                Bộ lọc
            </button>
        </div>

        <!-- Layout: Sidebar + Products -->
        <div class="product-layout-v2">

            <!-- ── Sidebar Filters - Desktop ── -->
            <aside class="product-sidebar-v2">
                <div class="sidebar-header-v2">
                    <h2 class="sidebar-title-v2">Chọn danh mục</h2>
                    <a href="<?php echo esc_url(remove_query_arg(['category', 'proxy_s', 'sort', 'paged'])); ?>"
                        class="clear-filter-v2">Xóa lọc</a>
                </div>

                <div class="filter-group-v2">
                    <div class="filter-options-v2">

                        <!-- Tất cả -->
                        <a href="<?php echo esc_url(vp_filter_url(['sort' => $sort_by !== 'bestseller' ? $sort_by : null])); ?>"
                            class="filter-option-v2 <?php echo ($selected_category === 'all') ? 'active' : ''; ?>"
                            data-category="all">
                            <i class="fa-solid fa-border-all"></i>
                            <span>Tất cả</span>
                        </a>

                        <?php if (!empty($categories) && !is_wp_error($categories)):
                            foreach ($categories as $cat):
                                $slug = $cat->slug;
                                $icon = $cat_icons[$slug] ?? 'fa-circle';
                                $is_active = ($selected_category === $slug);
                                $cat_url = vp_filter_url([
                                    'category' => $slug,
                                    'sort' => $sort_by !== 'bestseller' ? $sort_by : null,
                                    'proxy_s' => !empty($search_term) ? $search_term : null,
                                ]);
                                ?>
                        <a href="<?php echo esc_url($cat_url); ?>"
                            class="filter-option-v2 <?php echo $is_active ? 'active' : ''; ?>"
                            data-category="<?php echo esc_attr($slug); ?>">
                            <i class="fa-solid <?php echo esc_attr($icon); ?>"></i>
                            <span>
                                <?php echo esc_html($cat->name); ?>
                            </span>
                        </a>
                        <?php
                            endforeach;
                        endif; ?>
                    </div>
                </div>
            </aside>

            <!-- ── Products Section ── -->
            <section class="products-section-v2">

                <!-- Controls: Search + Sort + Count -->
                <form method="get" action="<?php echo esc_url(get_permalink()); ?>" class="product-controls"
                    id="proxyFilterForm">

                    <!-- Giữ lại category khi search -->
                    <?php if ($selected_category !== 'all'): ?>
                    <input type="hidden" name="category" value="<?php echo esc_attr($selected_category); ?>">
                    <?php endif; ?>

                    <div class="product-controls__left">
                        <div class="product-search-wrapper">
                            <i class="fa-solid fa-magnifying-glass search-icon"></i>
                            <input type="text" name="proxy_s" class="product-search-input"
                                placeholder="Tìm kiếm proxy..." value="<?php echo esc_attr($search_term); ?>"
                                id="proxySearchInput" />
                        </div>
                    </div>

                    <!-- Count label — dùng actual_count để hiển thị đúng -->
                    <span class="product-count-label" id="productCountLabel">
                        <?php
                        if ($actual_count === 0) {
                            echo 'Không tìm thấy sản phẩm';
                        } else {
                            echo "Hiển thị {$start_display}–{$end_display} trên {$total_in_db} sản phẩm";
                        }
                        ?>
                    </span>

                    <!-- Sort -->
                    <div class="product-sort-wrapper">
                        <label for="sortSelect" class="sort-label">Sắp xếp theo:</label>
                        <select name="sort" id="sortSelect" class="product-sort-select" onchange="this.form.submit()">
                            <option value="bestseller" <?php selected($sort_by, 'bestseller'); ?>>Bán chạy nhất
                            </option>
                            <option value="latest" <?php selected($sort_by, 'latest'); ?>>Mới cập nhật</option>
                            <option value="price-low" <?php selected($sort_by, 'price-low'); ?>>Giá thấp đến cao
                            </option>
                            <option value="price-high" <?php selected($sort_by, 'price-high'); ?>>Giá cao đến thấp
                            </option>
                            <option value="name-az" <?php selected($sort_by, 'name-az'); ?>>Tên từ A → Z</option>
                            <option value="name-za" <?php selected($sort_by, 'name-za'); ?>>Tên từ Z → A</option>
                        </select>
                    </div>

                </form>

                <!-- Products Grid -->
                <div class="products-grid-v2" id="productsGrid">
                    <?php if (!empty($page_products)): ?>

                    <?php foreach ($page_products as $p): ?>
                    <a href="<?php echo esc_url($p['product_url']); ?>" class="product-card-v2"
                        data-product-id="<?php echo esc_attr($p['product_id']); ?>">

                        <!-- Logo -->
                        <div class="product-logo-v2">
                            <img src="<?php echo esc_url($p['thumb_url']); ?>"
                                alt="<?php echo esc_attr($p['product_name']); ?>" />
                        </div>

                        <!-- Tên sản phẩm -->
                        <h3 class="product-name-v2">
                            <?php echo esc_html($p['product_name']); ?>
                        </h3>

                        <!-- Features -->
                        <ul class="pricing-card__features">
                            <?php foreach (array_slice($p['features'], 0, 3) as $feat):
                                        $feat_text = is_array($feat)
                                            ? ($feat['text'] ?? $feat['title'] ?? '')
                                            : $feat;
                                        if (empty($feat_text))
                                            continue;
                                        ?>
                            <li class="pricing-card__feature">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>
                                    <?php echo esc_html($feat_text); ?>
                                </span>
                            </li>
                            <?php endforeach; ?>
                        </ul>

                        <!-- Giá + Action -->
                        <div class="product-info-grid-v2">
                            <div class="product-price-col-v2">
                                <span class="price-amount-v2">
                                    <?php echo number_format($p['price_vnd'], 0, ',', '.'); ?>đ
                                </span>
                                <span class="price-sep-v2">/</span>
                                <span class="price-package-v2">
                                    <?php echo esc_html($p['default_qty'] . ' ' . $p['unit_label']); ?>
                                </span>
                            </div>
                            <div class="product-action-col-v2">
                                <span class="product-detail-btn">Chi tiết</span>
                            </div>
                        </div>

                    </a>
                    <?php endforeach; ?>

                    <?php else: ?>
                    <div class="no-products-v2">
                        <p>Không tìm thấy sản phẩm nào.</p>
                    </div>
                    <?php endif; ?>
                </div><!-- /.products-grid-v2 -->

                <!-- ── Pagination (SSR) ── -->
                <?php if ($products_query->max_num_pages > 1): ?>
                <div class="pagination-v2">

                    <!-- Prev -->
                    <?php if ($paged > 1):
                            $prev_args = ['paged' => $paged - 1];
                            if ($selected_category !== 'all')
                                $prev_args['category'] = $selected_category;
                            if (!empty($search_term))
                                $prev_args['proxy_s'] = $search_term;
                            if ($sort_by !== 'bestseller')
                                $prev_args['sort'] = $sort_by;
                            ?>
                    <a href="<?php echo esc_url(add_query_arg($prev_args)); ?>" class="pagination-btn">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                    <?php else: ?>
                    <button class="pagination-btn" disabled>
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <?php endif; ?>

                    <!-- Page numbers -->
                    <?php
                        $big = 999999999;
                        $pagination_args = [
                            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                            'format' => '?paged=%#%',
                            'current' => max(1, $paged),
                            'total' => $products_query->max_num_pages,
                            'type' => 'array',
                            'end_size' => 1,
                            'mid_size' => 2,
                            'prev_next' => false,
                        ];

                        $add_args = [];
                        if ($selected_category !== 'all')
                            $add_args['category'] = $selected_category;
                        if (!empty($search_term))
                            $add_args['proxy_s'] = $search_term;
                        if ($sort_by !== 'bestseller')
                            $add_args['sort'] = $sort_by;
                        if (!empty($add_args))
                            $pagination_args['add_args'] = $add_args;

                        $links = paginate_links($pagination_args);
                        if ($links):
                            foreach ($links as $link):
                                $is_current = strpos($link, 'current') !== false;
                                $is_dots = strpos($link, 'dots') !== false;
                                if ($is_dots): ?>
                    <span class="pagination-ellipsis">...</span>
                    <?php else:
                                    $class = $is_current ? 'pagination-btn active' : 'pagination-btn';
                                    $custom = preg_replace('/class=["\']page-numbers[^"\']*["\']/', 'class="' . $class . '"', $link);
                                    echo $custom;
                                endif;
                            endforeach;
                        endif;
                        ?>

                    <!-- Next -->
                    <?php if ($paged < $products_query->max_num_pages):
                            $next_args = ['paged' => $paged + 1];
                            if ($selected_category !== 'all')
                                $next_args['category'] = $selected_category;
                            if (!empty($search_term))
                                $next_args['proxy_s'] = $search_term;
                            if ($sort_by !== 'bestseller')
                                $next_args['sort'] = $sort_by;
                            ?>
                    <a href="<?php echo esc_url(add_query_arg($next_args)); ?>" class="pagination-btn">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                    <?php else: ?>
                    <button class="pagination-btn" disabled>
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                    <?php endif; ?>

                </div><!-- /.pagination-v2 -->
                <?php endif; ?>

            </section><!-- /.products-section-v2 -->
        </div><!-- /.product-layout-v2 -->
    </div><!-- /.product-container-v2 -->
</div><!-- /.main-content -->


<!-- ══════════════════════════════════════════
     MOBILE FILTER MODAL
══════════════════════════════════════════ -->
<div class="filter-modal-v2" id="filterModal">
    <div class="filter-modal-content-v2">
        <div class="filter-modal-header-v2">
            <h2 class="filter-modal-title-v2">Bộ lọc</h2>
            <div class="filter-modal-close-v2" id="closeFilterModal">
                <i class="fa-solid fa-xmark"></i>
            </div>
        </div>

        <div class="filter-modal-body-v2">
            <!-- Category -->
            <div class="filter-group-v2">
                <h3 class="filter-group-title-v2">Chọn danh mục</h3>
                <div class="filter-options-v2">
                    <a href="<?php echo esc_url(vp_filter_url(['sort' => $sort_by !== 'bestseller' ? $sort_by : null])); ?>"
                        class="filter-option-v2 <?php echo ($selected_category === 'all') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-border-all"></i>
                        <span>Tất cả</span>
                    </a>
                    <?php if (!empty($categories) && !is_wp_error($categories)):
                        foreach ($categories as $cat):
                            $slug = $cat->slug;
                            $icon = $cat_icons[$slug] ?? 'fa-circle';
                            $is_active = ($selected_category === $slug);
                            $cat_url = vp_filter_url([
                                'category' => $slug,
                                'sort' => $sort_by !== 'bestseller' ? $sort_by : null,
                                'proxy_s' => !empty($search_term) ? $search_term : null,
                            ]);
                            ?>
                    <a href="<?php echo esc_url($cat_url); ?>"
                        class="filter-option-v2 <?php echo $is_active ? 'active' : ''; ?>">
                        <i class="fa-solid <?php echo esc_attr($icon); ?>"></i>
                        <span>
                            <?php echo esc_html($cat->name); ?>
                        </span>
                    </a>
                    <?php
                        endforeach;
                    endif; ?>
                </div>
            </div>

            <!-- Sort -->
            <div class="filter-group-v2">
                <h3 class="filter-group-title-v2">Sắp xếp theo</h3>
                <div class="filter-sort-links">
                    <?php
                    $sort_options = [
                        'bestseller' => 'Bán chạy nhất',
                        'latest' => 'Mới cập nhật',
                        'price-low' => 'Giá thấp đến cao',
                        'price-high' => 'Giá cao đến thấp',
                        'name-az' => 'Tên từ A → Z',
                        'name-za' => 'Tên từ Z → A',
                    ];
                    foreach ($sort_options as $val => $label):
                        $sort_url = vp_filter_url([
                            'sort' => $val !== 'bestseller' ? $val : null,
                            'category' => $selected_category !== 'all' ? $selected_category : null,
                            'proxy_s' => !empty($search_term) ? $search_term : null,
                        ]);
                        ?>
                    <a href="<?php echo esc_url($sort_url); ?>"
                        class="filter-sort-link <?php echo ($sort_by === $val) ? 'active' : ''; ?>">
                        <?php echo esc_html($label); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="filter-modal-footer-v2">
            <button class="btn-cancel-v2" id="cancelFilter">Đóng</button>
        </div>
    </div>
</div>

<?php get_footer(); ?>