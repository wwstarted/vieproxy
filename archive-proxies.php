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
$meta_key = '';

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

// Category filter 
if ($selected_category !== 'all' && !empty($selected_category)) {
    $args['tax_query'] = [
        [
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => $selected_category,
        ]
    ];
}

// Search filter — tìm trong post_title
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

// ── Collect all products for price sort (chỉ khi sort theo giá) ──
$all_products_data = [];
if ($products_query->have_posts()) {

    if (in_array($sort_by, ['price-low', 'price-high'])) {
        $all_args = $args;
        $all_args['posts_per_page'] = -1;
        $all_args['paged'] = 1;
        unset($all_args['s']);

    }
}

// Hero description
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
        <p class="proxy-hero__desc"><?php echo esc_html($hero_desc); ?></p>
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
                            <span><?php echo esc_html($cat->name); ?></span>
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

                    <!-- Count label -->
                    <span class="product-count-label" id="productCountLabel">
                        <?php
                        $total = $products_query->found_posts;
                        if ($total === 0) {
                            echo 'Không tìm thấy sản phẩm';
                        } else {
                            $start = ($paged - 1) * $posts_per_page + 1;
                            $end = min($paged * $posts_per_page, $total);
                            echo "Hiển thị {$start}–{$end} trên {$total} sản phẩm";
                        }
                        ?>
                    </span>

                    <!-- Sort -->
                    <div class="product-sort-wrapper">
                        <label for="sortSelect" class="sort-label">Sắp xếp theo:</label>
                        <select name="sort" id="sortSelect" class="product-sort-select" onchange="this.form.submit()">
                            <option value="bestseller" <?php selected($sort_by, 'bestseller'); ?>>Bán chạy nhất</option>
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
                    <?php
                    // Collect all products this page
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

                            $pricing_config = get_post_meta($product_id, '_vieproxy_pricing_config', true);

                            if (empty($pricing_config) || empty($pricing_config['duration_plans'])) {
                                continue;
                            }

                            $plans = $pricing_config['duration_plans'];
                            $default_plan_idx = $pricing_config['default_plan_index'] ?? 0;
                            $default_plan = $plans[$default_plan_idx] ?? $plans[0];

                            $monthly_price = $default_plan['monthly_price'] ?? 0;
                            $months = $default_plan['months'] ?? 1;
                            $default_qty = $pricing_config['default_qty'] ?? 1;
                            $unit_label = $pricing_config['unit_label'] ?? 'IPs';

                            $price_usd = $default_qty * $monthly_price * $months;
                            $price_vnd = $price_usd * 25000;

                            $page_products[] = compact(
                                'product_id',
                                'product_name',
                                'product_url',
                                'thumb_url',
                                'price_usd',
                                'price_vnd',
                                'default_qty',
                                'unit_label'
                            );
                        endwhile;
                        wp_reset_postdata();

                        // Sort theo giá nếu cần (client meta không thể orderby trực tiếp)
                        if ($sort_by === 'price-low') {
                            usort($page_products, fn($a, $b) => $a['price_vnd'] <=> $b['price_vnd']);
                        } elseif ($sort_by === 'price-high') {
                            usort($page_products, fn($a, $b) => $b['price_vnd'] <=> $a['price_vnd']);
                        }

                        foreach ($page_products as $p):
                            ?>
                    <a href="<?php echo esc_url($p['product_url']); ?>" class="product-card-v2"
                        data-product-id="<?php echo esc_attr($p['product_id']); ?>">

                        <div class="product-logo-v2">
                            <img src="<?php echo esc_url($p['thumb_url']); ?>"
                                alt="<?php echo esc_attr($p['product_name']); ?>" />
                        </div>

                        <h3 class="product-name-v2"><?php echo esc_html($p['product_name']); ?></h3>

                        <ul class="pricing-card__features">
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
                                <span>Windows, Mac, Linux</span>
                            </li>
                        </ul>

                        <div class="product-info-grid-v2">
                            <div class="product-price-col-v2">
                                <div class="price-amount-v2">
                                    <?php echo number_format($p['price_usd'], 0, ',', '.'); ?>$
                                </div>
                                <div class="price-package-v2">
                                    /<?php echo esc_html($p['default_qty'] . ' ' . $p['unit_label']); ?>
                                </div>
                            </div>
                            <div class="product-action-col-v2">
                                <span class="product-detail-btn">Chi tiết</span>
                            </div>
                        </div>

                    </a>
                    <?php
                        endforeach;

                    else: ?>
                    <div class="no-products-v2">
                        <p>Không tìm thấy sản phẩm nào.</p>
                    </div>
                    <?php endif; ?>
                </div>

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

                </div>
                <?php endif; ?>

            </section>
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
                        <span><?php echo esc_html($cat->name); ?></span>
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