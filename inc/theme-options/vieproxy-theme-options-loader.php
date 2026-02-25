<?php

/**
 * VieProxy Theme Options Loader
 * Đăng ký menu admin, xử lý save, load các page settings
 */

if (!defined('ABSPATH')) exit;

// Load helper functions
require_once get_template_directory() . '/inc/theme-options/vieproxy-helper-functions.php';

// ─────────────────────────────────────────────────────────────
// ENQUEUE ADMIN ASSETS
// ─────────────────────────────────────────────────────────────

add_action('admin_enqueue_scripts', function ($hook) {
    // Chỉ load trên trang theme options
    if (strpos($hook, 'vieproxy-settings') === false) return;

    wp_enqueue_media(); // WordPress Media Uploader

    wp_enqueue_style(
        'vieproxy-admin-css',
        get_template_directory_uri() . '/inc/theme-options/assets/admin-css.css',
        array(),
        filemtime(get_template_directory() . '/inc/theme-options/assets/admin-css.css')
    );

    wp_enqueue_script(
        'vieproxy-admin-js',
        get_template_directory_uri() . '/inc/theme-options/assets/admin-js.js',
        array('jquery'),
        filemtime(get_template_directory() . '/inc/theme-options/assets/admin-js.js'),
        true
    );

    // Pass ajaxurl và nonce vào JS
    wp_localize_script('vieproxy-admin-js', 'vieproxyAdmin', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('vieproxy_save_options'),
    ));
});

// ─────────────────────────────────────────────────────────────
// ĐĂNG KÝ MENU ADMIN
// ─────────────────────────────────────────────────────────────

add_action('admin_menu', function () {
    add_menu_page(
        'VieProxy Settings',      // Page title
        'VieProxy Settings',      // Menu title
        'manage_options',         // Capability
        'vieproxy-settings',      // Slug
        'vieproxy_render_settings_page', // Callback
        'dashicons-shield',       // Icon
        59                        // Position (sau Appearance = 60)
    );

    // Sub-pages
    $sub_pages = array(
        'home'    => 'Home Page',
        // Thêm page mới vào đây sau
        // 'contact' => 'Contact Page',
        // 'blog'    => 'Blog Page',
    );

    foreach ($sub_pages as $slug => $title) {
        add_submenu_page(
            'vieproxy-settings',
            $title . ' Settings',
            $title,
            'manage_options',
            'vieproxy-settings-' . $slug,
            'vieproxy_render_page_' . $slug
        );
    }
});

// ─────────────────────────────────────────────────────────────
// XỬ LÝ SAVE OPTIONS (AJAX)
// ─────────────────────────────────────────────────────────────

add_action('wp_ajax_vieproxy_save_options', function () {
    check_ajax_referer('vieproxy_save_options', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Không có quyền');
    }

    $posted = isset($_POST['vieproxy_theme_options']) ? $_POST['vieproxy_theme_options'] : array();

    /**
     * Sanitize options - với xử lý đặc biệt cho icon HTML
     */
    function vieproxy_sanitize_options($data, $is_nested = false)
    {
        $sanitized = array();

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    // Nested array: đệ quy tiếp (ví dụ: commitment_features[0][title])
                    $sanitized[$key] = vieproxy_sanitize_options($value, true);
                } else {
                    // Check if this is an icon field (contains <i> tag)
                    if (strpos($value, '<i') !== false && strpos($value, '</i>') !== false) {
                        // Allow <i> tags with class attribute for icons
                        $allowed_tags = array(
                            'i' => array(
                                'class' => array(),
                                'style' => array(),
                            ),
                        );
                        $sanitized[$key] = wp_kses($value, $allowed_tags);
                    } elseif (filter_var($value, FILTER_VALIDATE_URL)) {
                        $sanitized[$key] = esc_url_raw($value);
                    } else {
                        $sanitized[$key] = sanitize_textarea_field($value);
                    }
                }
            }
        }

        return $sanitized;
    }

    $sanitized = array();
    if (is_array($posted)) {
        foreach ($posted as $key => $value) {
            $key = sanitize_key($key);
            if (is_array($value)) {
                // Nested array (repeater): xử lý đệ quy
                $sanitized[$key] = vieproxy_sanitize_options($value, true);
            } else {
                // Check if this is an icon field
                if (strpos($value, '<i') !== false && strpos($value, '</i>') !== false) {
                    $allowed_tags = array(
                        'i' => array(
                            'class' => array(),
                            'style' => array(),
                        ),
                    );
                    $sanitized[$key] = wp_kses($value, $allowed_tags);
                } elseif (filter_var($value, FILTER_VALIDATE_URL)) {
                    $sanitized[$key] = esc_url_raw($value);
                } else {
                    $sanitized[$key] = sanitize_textarea_field($value);
                }
            }
        }
    }

    // Merge với options hiện tại (không xoá options của page khác)
    $existing = get_option('vieproxy_theme_options', array());
    $merged   = array_merge($existing, $sanitized);

    update_option('vieproxy_theme_options', $merged);

    wp_send_json_success(array('message' => 'Đã lưu cài đặt thành công!'));
});

// ─────────────────────────────────────────────────────────────
// RENDER: TRANG TỔNG (Main settings)
// ─────────────────────────────────────────────────────────────

function vieproxy_render_settings_page()
{
?>
    <div class="vp-admin-wrap">
        <div class="vp-admin-header">
            <div class="vp-admin-header__icon">
                <span class="dashicons dashicons-shield"></span>
            </div>
            <h1 class="vp-admin-header__title">VieProxy Settings</h1>
        </div>

        <div class="vp-admin-body vp-admin-body--welcome">
            <div class="vp-welcome-grid">
                <a href="<?php echo admin_url('admin.php?page=vieproxy-settings-home'); ?>" class="vp-welcome-card">
                    <span class="dashicons dashicons-admin-home"></span>
                    <strong>Home Page</strong>
                    <span>Hero, Features, Pricing…</span>
                </a>
                <!-- Thêm card cho page mới ở đây -->
            </div>
        </div>
    </div>
<?php
}

// ─────────────────────────────────────────────────────────────
// RENDER: HOME PAGE SETTINGS
// ─────────────────────────────────────────────────────────────

function vieproxy_render_page_home()
{
    require_once get_template_directory() . '/inc/theme-options/page/page-home.php';
    vieproxy_render_home_settings();
}
