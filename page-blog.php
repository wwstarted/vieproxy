<?php
/*
 Template Name: Vieproxy blog page
*/
?>

<?php get_header(); ?>

<?php
// Pagination settings
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$posts_per_page = 9;

// Get selected category from URL
$selected_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : 'all';

// Get search term from URL

$search_term = isset($_GET['blog_s']) ? sanitize_text_field($_GET['blog_s']) : '';

// Build query args
$args = array(
    'post_type' => 'post',
    'posts_per_page' => $posts_per_page,
    'paged' => $paged,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
);

if ($selected_category !== 'all' && !empty($selected_category)) {
    $args['category_name'] = $selected_category;
}

if (!empty($search_term)) {
    $args['s'] = $search_term;
}

$posts_query = new WP_Query($args);

// Get all categories
$categories = get_categories(array(
    'hide_empty' => false,
));

// Helper: estimate read time
function vp_read_time($post_id)
{
    $content = get_post_field('post_content', $post_id);
    $word_count = str_word_count(strip_tags($content));
    $minutes = max(1, round($word_count / 200));
    return $minutes . ' phút đọc';
}
?>

<!-- ══════════════════════════════════════════
     HERO BANNER
══════════════════════════════════════════ -->
<section class="blog-hero">
    <div class="wrapper">

        <!-- Breadcrumb -->
        <nav class="blog-hero__breadcrumb" aria-label="breadcrumb">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="breadcrumb-link">Trang chủ</a>
            <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
            <span class="breadcrumb-current">Blog</span>
        </nav>

        <h1 class="blog-hero__title">Kiến thức MMO</h1>
        <p class="blog-hero__desc">Chia sẻ các tips, thuật thuật, kiến thức và tut trong ngành Make Money Online!</p>

    </div>
</section>

<!-- ══════════════════════════════════════════
     MAIN CONTENT
══════════════════════════════════════════ -->
<section class="blog-main">
    <div class="wrapper">

        <!-- ── Category Filter Tabs ── -->
        <div class="blog-filter-bar">

            <!-- All -->
            <a href="<?php echo esc_url(remove_query_arg(array('category', 'blog_s', 'paged'))); ?>"
                class="blog-filter-btn <?php echo ($selected_category === 'all') ? 'active' : ''; ?>">
                Tất cả
            </a>

            <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
            <a href="<?php echo esc_url(add_query_arg('category', $cat->slug, remove_query_arg(array('blog_s', 'paged')))); ?>"
                class="blog-filter-btn <?php echo ($selected_category === $cat->slug) ? 'active' : ''; ?>"
                data-cat-slug="<?php echo esc_attr($cat->slug); ?>">
                <?php echo esc_html($cat->name); ?>
            </a>
            <?php endforeach; ?>
            <?php endif; ?>

            <!-- Search  -->
            <form method="get" action="<?php echo esc_url(get_permalink()); ?>" class="blog-search-form">
                <?php if ($selected_category !== 'all'): ?>
                <input type="hidden" name="category" value="<?php echo esc_attr($selected_category); ?>">
                <?php endif; ?>
                <div class="blog-search-wrapper">
                    <i class="fa-solid fa-magnifying-glass blog-search-icon"></i>
                    <input type="text" name="blog_s" class="blog-search-input" placeholder="Tìm kiếm bài viết..."
                        value="<?php echo esc_attr($search_term); ?>">
                </div>
            </form>

        </div>

        <!-- ── Blog Grid ── -->
        <?php if ($posts_query->have_posts()): ?>

        <div class="blog-grid" id="blogGrid">
            <?php while ($posts_query->have_posts()):
                    $posts_query->the_post(); ?>
            <?php
                    $post_id = get_the_ID();
                    $post_url = get_permalink();
                    $post_title = get_the_title();

                    // Thumbnail
                    $thumbnail = get_the_post_thumbnail_url($post_id, 'large');
                    if (!$thumbnail) {
                        $thumbnail = get_template_directory_uri() . '/images/placeholder.png';
                    }

                    // Category 
                    $post_cats = get_the_category();
                    $cat_name = !empty($post_cats) ? $post_cats[0]->name : '';
                    $cat_slug = !empty($post_cats) ? $post_cats[0]->slug : '';

                    // Excerpt
                    $excerpt = get_the_excerpt();
                    if (empty($excerpt)) {
                        $excerpt = wp_trim_words(get_the_content(), 25, '...');
                    }

                    // Date
                    $post_date = get_the_date('j \t\h\á\n\g n, Y');

                    // Read time
                    $read_time = vp_read_time($post_id);

                    // Author
                    $author_name = get_the_author();
                    ?>

            <article class="blog-card" data-category="<?php echo esc_attr($cat_slug); ?>">
                <a href="<?php echo esc_url($post_url); ?>" class="blog-card__link">

                    <!-- Image -->
                    <div class="blog-card__image-wrap">
                        <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($post_title); ?>"
                            class="blog-card__image" loading="lazy">

                        <!-- Category tag overlay -->
                        <?php if (!empty($cat_name)): ?>
                        <span class="blog-card__cat-tag"><?php echo esc_html($cat_name); ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Content -->
                    <div class="blog-card__body">

                        <!-- Meta: date + read time -->
                        <div class="blog-card__meta">
                            <span class="blog-card__date">
                                <i class="fa-regular fa-calendar"></i>
                                <?php echo esc_html($post_date); ?>
                            </span>
                            <span class="blog-card__read-time">
                                <i class="fa-regular fa-clock"></i>
                                <?php echo esc_html($read_time); ?>
                            </span>
                        </div>

                        <!-- Title -->
                        <h3 class="blog-card__title"><?php echo esc_html($post_title); ?></h3>

                        <!-- Excerpt -->
                        <p class="blog-card__excerpt"><?php echo esc_html(wp_strip_all_tags($excerpt)); ?></p>

                        <!-- Footer -->
                        <div class="blog-card__footer">
                            <span class="blog-card__author">
                                <i class="fa-regular fa-user"></i>
                                <?php echo esc_html($author_name); ?>
                            </span>
                            <span class="blog-card__read-more">
                                Đọc tiếp <i class="fa-solid fa-arrow-right"></i>
                            </span>
                        </div>

                    </div>

                </a>
            </article>

            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        </div>

        <!-- ── Pagination ── -->
        <?php if ($posts_query->max_num_pages > 1): ?>
        <div class="blog-pagination">

            <!-- Prev -->
            <?php if ($paged > 1): ?>
            <?php
                        $prev_args = array('paged' => $paged - 1);
                        if (!empty($selected_category) && $selected_category !== 'all')
                            $prev_args['category'] = $selected_category;
                        if (!empty($search_term))
                            $prev_args['blog_s'] = $search_term;
                        ?>
            <a href="<?php echo esc_url(add_query_arg($prev_args)); ?>"
                class="blog-pagination__btn blog-pagination__prev">
                <i class="fa-solid fa-chevron-left"></i>
                <span>Trước</span>
            </a>
            <?php else: ?>
            <button class="blog-pagination__btn blog-pagination__prev" disabled>
                <i class="fa-solid fa-chevron-left"></i>
                <span>Trước</span>
            </button>
            <?php endif; ?>

            <!-- Page Numbers -->
            <div class="blog-pagination__numbers">
                <?php
                        $big = 999999999;
                        $pagination_args = array(
                            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                            'format' => '?paged=%#%',
                            'current' => max(1, $paged),
                            'total' => $posts_query->max_num_pages,
                            'type' => 'array',
                            'end_size' => 1,
                            'mid_size' => 2,
                            'prev_next' => false,
                        );

                        if (!empty($selected_category) && $selected_category !== 'all') {
                            $pagination_args['add_args']['category'] = $selected_category;
                        }
                        if (!empty($search_term)) {
                            $pagination_args['add_args']['blog_s'] = $search_term;
                        }

                        $pagination_links = paginate_links($pagination_args);
                        if ($pagination_links):
                            foreach ($pagination_links as $link):
                                $is_current = strpos($link, 'current') !== false;
                                $is_dots = strpos($link, 'dots') !== false;

                                if ($is_dots): ?>
                <span class="blog-pagination__ellipsis">...</span>
                <?php else:
                                    $class = $is_current ? 'blog-pagination__number active' : 'blog-pagination__number';
                                    $custom = preg_replace('/class=["\']page-numbers[^"\']*["\']/', 'class="' . $class . '"', $link);
                                    echo $custom;
                                endif;
                            endforeach;
                        endif;
                        ?>
            </div>

            <!-- Next -->
            <?php if ($paged < $posts_query->max_num_pages): ?>
            <?php
                        $next_args = array('paged' => $paged + 1);
                        if (!empty($selected_category) && $selected_category !== 'all')
                            $next_args['category'] = $selected_category;
                        if (!empty($search_term))
                            $next_args['blog_s'] = $search_term;
                        ?>
            <a href="<?php echo esc_url(add_query_arg($next_args)); ?>"
                class="blog-pagination__btn blog-pagination__next">
                <span>Tiếp</span>
                <i class="fa-solid fa-chevron-right"></i>
            </a>
            <?php else: ?>
            <button class="blog-pagination__btn blog-pagination__next" disabled>
                <span>Tiếp</span>
                <i class="fa-solid fa-chevron-right"></i>
            </button>
            <?php endif; ?>

        </div>
        <?php endif; ?>

        <?php else: ?>
        <div class="blog-no-results">
            <i class="fa-regular fa-face-sad-tear"></i>
            <p>Không tìm thấy bài viết phù hợp.</p>
            <a href="<?php echo esc_url(remove_query_arg(array('category', 'blog_s', 'paged'))); ?>"
                class="blog-no-results__reset">
                Xem tất cả bài viết
            </a>
        </div>
        <?php endif; ?>

    </div>
</section>

<?php get_footer(); ?>