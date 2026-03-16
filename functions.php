<?php
// ── Theme Options ──────────────────────────────────────────────────────────────
require_once get_template_directory() . '/inc/theme-options/vieproxy-theme-options-loader.php';

// Enqueue assets
function vieproxy_theme_enqueue_assets()
{
    wp_enqueue_style('main-style', get_stylesheet_directory_uri() . '/style.css', array(), filemtime(get_stylesheet_directory() . '/style.css'));
    wp_enqueue_style('font-icon', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css', array(), '1.0', 'all');

    // CSS
    $css_files = [
        'header',
        'footer',
        'home',
        'pricing-card',
        'single-product',
    ];
    foreach ($css_files as $file) {
        wp_enqueue_style("vieproxy-{$file}", get_template_directory_uri() . "/css/{$file}.css", array(), filemtime(get_stylesheet_directory() . "/css/{$file}.css"));
    }

    // JS
    $js_files = [
        'header',
        'home-hero-order-widget',
        'home-proxy-list',
        'home-why-choose-slider',
        'home-pricing-slider',
        'home-rating-slider',
        'home-partners-slider',
        'home-faq-accordion',
        'single-product',
        'single-product-related-slider',
        'single-product-toc-align',
    ];
    foreach ($js_files as $file) {
        wp_enqueue_script("vieproxy-{$file}", get_template_directory_uri() . "/js/{$file}.js", array('jquery'), filemtime(get_template_directory() . "/js/{$file}.js"), true);
    }

    if (is_page_template('page-privacy-policy.php')) {
        wp_enqueue_style('info-pages-style', get_template_directory_uri() . '/css/info-pages.css', array(), '1.0.0');
        wp_enqueue_script(
            'info-pages-script',
            get_template_directory_uri() . '/js/info-pages.js',
            array(),
            '1.0.1',
            true
        );
    }
    if (is_page_template('page-terms-of-service.php')) {
        wp_enqueue_style('info-pages-style', get_template_directory_uri() . '/css/info-pages.css', array(), '1.0.0');
        wp_enqueue_script(
            'info-pages-script',
            get_template_directory_uri() . '/js/info-pages.js',
            array(),
            '1.0.1',
            true
        );
    }

    if (is_page_template('archive-proxies.php')) {
        wp_enqueue_style(
            'archive-proxies',
            get_template_directory_uri() . '/css/archive-proxies.css',
            [],
            '1.0.0'
        );

        wp_enqueue_script(
            'archive-proxies',
            get_template_directory_uri() . '/js/archive-proxies.js',
            [],
            '1.0.0',
            true
        );
    }

    if (is_page_template('page-blog.php')) {
        wp_enqueue_style(
            'archive-blogs',
            get_template_directory_uri() . '/css/archive-blogs.css',
            [],
            '1.0.0'
        );

        wp_enqueue_script(
            'archive-blogs',
            get_template_directory_uri() . '/js/archive-blogs.js',
            [],
            '1.0.0',
            true
        );
    }

    if (is_single()) {
        wp_enqueue_style(
            'single-blog',
            get_template_directory_uri() . '/css/single-blog.css',
            [],
            '1.0.0'
        );

        wp_enqueue_script(
            'single-blog',
            get_template_directory_uri() . '/js/single-blog.js',
            [],
            '1.0.0',
            true
        );
    }

    wp_enqueue_style(
        'speed-dial',
        get_template_directory_uri() . '/css/speed-dial.css',
        array(),
        filemtime(get_stylesheet_directory() . '/css/speed-dial.css')
    );

    wp_enqueue_script(
        'speed-dial',
        get_template_directory_uri() . '/js/speed-dial.js',
        array(),
        filemtime(get_template_directory() . '/js/speed-dial.js'),
        true
    );

    wp_enqueue_style(
        'back-to-top',
        get_template_directory_uri() . '/css/back-to-top.css',
        array(),
        filemtime(get_stylesheet_directory() . '/css/back-to-top.css')
    );

    wp_enqueue_script(
        'back-to-top',
        get_template_directory_uri() . '/js/back-to-top.js',
        array(),
        filemtime(get_template_directory() . '/js/back-to-top.js'),
        true
    );

    $show_progress = (
        is_page_template('page-privacy-policy.php') ||
        is_page_template('page-terms-of-service.php') ||
        is_page_template('page-contact.php') ||
        is_single()
    );

    if ($show_progress) {
        wp_enqueue_style(
            'reading-progress',
            get_template_directory_uri() . '/css/reading-progress.css',
            array(),
            filemtime(get_stylesheet_directory() . '/css/reading-progress.css')
        );
        wp_enqueue_script(
            'reading-progress',
            get_template_directory_uri() . '/js/reading-progress.js',
            array(),
            filemtime(get_template_directory() . '/js/reading-progress.js'),
            true
        );
    }

    if (is_page_template('page-archive-partners.php')) {
        wp_enqueue_style(
            'archive-partners',
            get_template_directory_uri() . '/css/archive-partners.css',
            [],
            filemtime(get_stylesheet_directory() . '/css/archive-partners.css')
        );
        wp_enqueue_script(
            'archive-partners',
            get_template_directory_uri() . '/js/archive-partners.js',
            [],
            filemtime(get_template_directory() . '/js/archive-partners.js'),
            true
        );
        wp_localize_script('archive-partners', 'VieProxyPartners', [
            'homeUrl' => home_url('partners'),
        ]);
    }


    $is_single_partner = isset($_SERVER['REQUEST_URI']) &&
        preg_match('#/partners/[^/]+/?(\?.*)?$#', $_SERVER['REQUEST_URI']);

    if ($is_single_partner) {
        wp_enqueue_style(
            'single-partner',
            get_template_directory_uri() . '/css/single-partner.css',
            [],
            filemtime(get_stylesheet_directory() . '/css/single-partner.css')
        );
        wp_enqueue_script(
            'single-partner',
            get_template_directory_uri() . '/js/single-partner.js',
            [],
            filemtime(get_template_directory() . '/js/single-partner.js'),
            true
        );
    }

    if (is_page_template('page-about.php')) {

        wp_enqueue_style(
            'info-pages-style',
            get_template_directory_uri() . '/css/info-pages.css',
            [],
            filemtime(get_stylesheet_directory() . '/css/info-pages.css')
        );

        wp_enqueue_style(
            'vieproxy-about',
            get_template_directory_uri() . '/css/about.css',
            ['info-pages-style'],
            filemtime(get_stylesheet_directory() . '/css/about.css')
        );

        wp_enqueue_script(
            'info-pages-script',
            get_template_directory_uri() . '/js/info-pages.js',
            [],
            filemtime(get_template_directory() . '/js/info-pages.js'),
            true
        );

        wp_enqueue_script(
            'vieproxy-about',
            get_template_directory_uri() . '/js/about.js',
            ['info-pages-script'],
            filemtime(get_template_directory() . '/js/about.js'),
            true
        );
    }

    if (is_page_template('page-sign-in.php')) {
        wp_enqueue_style(
            'vieproxy-sign-in',
            get_template_directory_uri() . '/css/sign-in.css',
            [],
            filemtime(get_stylesheet_directory() . '/css/sign-in.css')
        );
        wp_enqueue_script(
            'vieproxy-sign-in',
            get_template_directory_uri() . '/js/sign-in.js',
            [],
            filemtime(get_template_directory() . '/js/sign-in.js'),
            true // footer
        );
    }

    if (is_page_template('page-register.php')) {
        wp_enqueue_style('vieproxy-register', get_template_directory_uri() . '/css/register.css', [], filemtime(get_stylesheet_directory() . '/css/register.css'));
        wp_enqueue_script('vieproxy-register', get_template_directory_uri() . '/js/register.js', [], filemtime(get_template_directory() . '/js/register.js'), true);
    }

    if (is_page_template('page-forgot-password.php')) {
        wp_enqueue_style('vieproxy-fp', get_template_directory_uri() . '/css/forgot-password.css', [], filemtime(get_stylesheet_directory() . '/css/forgot-password.css'));
        wp_enqueue_script('vieproxy-fp', get_template_directory_uri() . '/js/forgot-password.js', [], filemtime(get_template_directory() . '/js/forgot-password.js'), true);
    }

    // // add_filter('template_include', function ($template) {
    // //     $spa = ['account', 'profile', 'change-password', 'purchase-history'];
    // //     if (is_page() && in_array(get_post_field('post_name', get_queried_object_id()), $spa)) {
    // //         $t = locate_template('page-account.php');
    // //         if ($t)
    // //             return $t;
    // //     }
    // //     return $template;
    // // });

    $account_pages = ['account', 'profile', 'change-password', 'purchase-history'];

    if (is_page($account_pages)) {

        wp_enqueue_style(
            'vieproxy-account',
            get_template_directory_uri() . '/css/account.css',
            [],
            filemtime(get_stylesheet_directory() . '/css/account.css')
        );

        wp_enqueue_script(
            'vieproxy-account',
            get_template_directory_uri() . '/js/account.js',
            [],
            filemtime(get_template_directory() . '/js/account.js'),
            true
        );
    }

    add_filter('template_include', function ($template) {

        if (!is_page())
            return $template;

        $slug = get_post_field('post_name', get_queried_object_id());

        $spa_pages = ['account', 'profile', 'change-password', 'purchase-history'];

        if (in_array($slug, $spa_pages)) {
            $t = locate_template('page-account.php');
            if ($t)
                return $t;
        }

        return $template;

    });


}
add_action('wp_enqueue_scripts', 'vieproxy_theme_enqueue_assets');

require_once get_template_directory() . '/vendor/autoload.php'; // Firebase JWT
require_once get_template_directory() . '/inc/theme-options/api-user.php';
require_once get_template_directory() . '/inc/theme-options/functions-account.php';


add_theme_support('post-thumbnails');


function vieproxy_register_menus()
{
    register_nav_menus(array(
        'header_menu' => __('Header Menu', 'vieproxy'),
    ));
}
add_action('after_setup_theme', 'vieproxy_register_menus');


require_once get_template_directory() . '/inc/theme-options/page/page-contact.php';


add_action('template_redirect', function () {

    $request_uri = $_SERVER['REQUEST_URI'];
    $home_path = trim(parse_url(home_url(), PHP_URL_PATH), '/');
    $request_path = trim($request_uri, '/');

    if (strpos($request_path, '?') !== false) {
        $request_path = substr($request_path, 0, strpos($request_path, '?'));
        $request_path = trim($request_path, '/');
    }

    if ($home_path && strpos($request_path, $home_path) === 0) {
        $request_path = trim(substr($request_path, strlen($home_path)), '/');
    }

    if (preg_match('#^partners/([^/]+)/?$#', $request_path, $matches)) {
        $partner_slug = sanitize_text_field($matches[1]);
        set_query_var('partner_slug', $partner_slug);

        $template = locate_template('page-single-partner.php');
        if ($template) {
            status_header(200);
            include $template;
            exit;
        }
    }

}, 1);

if (!function_exists('get_providers_from_api')) {
    function get_providers_from_api()
    {
        $cache_key = 'vieproxy_providers_api';
        $cached = get_transient($cache_key);

        if ($cached !== false) {
            return $cached;
        }

        $api_url = 'https://tradeproxy.proxyflowpxp.com/wp-json/wp/v2/provider?per_page=100';

        $response = wp_remote_get($api_url, [
            'timeout' => 10,
            'sslverify' => false,
        ]);

        if (is_wp_error($response)) {
            return [];
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!is_array($data) || empty($data)) {
            return [];
        }

        // Cache 1 giờ
        set_transient($cache_key, $data, HOUR_IN_SECONDS);

        return $data;
    }
}


add_filter('redirect_canonical', function ($redirect_url, $requested_url) {
    if (preg_match('#/partners/[^/]+/?#', $requested_url)) {
        return false;
    }
    return $redirect_url;
}, 10, 2);

// ── CORS cho VieProxy REST API ────────────────────────────────────────────
add_action('rest_api_init', function () {
    remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
    add_filter('rest_pre_serve_request', function ($value) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
        return $value;
    });
}, 15);