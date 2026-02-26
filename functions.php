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

    // Reading Progress Bar 
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

    if (is_page_template('page-about.php')) {

        wp_enqueue_style('info-pages-style', get_template_directory_uri() . '/css/info-pages.css', [], '1.0.0');

        wp_enqueue_style(
            'vieproxy-about',
            get_template_directory_uri() . '/css/about.css',
            ['info-pages-style'],
            filemtime(get_stylesheet_directory() . '/css/about.css')
        );
        wp_enqueue_script(
            'vieproxy-about',
            get_template_directory_uri() . '/js/about.js',
            [],
            filemtime(get_template_directory() . '/js/about.js'),
            true
        );
    }

}
add_action('wp_enqueue_scripts', 'vieproxy_theme_enqueue_assets');


add_theme_support('post-thumbnails');

// Đăng ký menus
function vieproxy_register_menus()
{
    register_nav_menus(array(
        'header_menu' => __('Header Menu', 'vieproxy'),
    ));
}
add_action('after_setup_theme', 'vieproxy_register_menus');


require_once get_template_directory() . '/inc/theme-options/page/page-contact.php';