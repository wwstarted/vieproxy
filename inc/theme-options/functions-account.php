<?php
add_filter('template_include', function ($template) {
    $spa_pages = ['account', 'profile', 'change-password', 'purchase-history'];
    if (is_page()) {
        $slug = get_post_field('post_name', get_queried_object_id());
        if (in_array($slug, $spa_pages, true)) {
            $t = locate_template('page-account.php');
            if ($t)
                return $t;
        }
    }
    return $template;
});

add_action('wp_enqueue_scripts', function () {
    $account_slugs = ['account', 'profile', 'change-password', 'purchase-history'];
    if (!is_page($account_slugs))
        return;

    $uri = get_template_directory_uri();
    $ver = wp_get_theme()->get('Version');

    wp_enqueue_style(
        'vp-user-account',
        $uri . '/css/user-account.css',
        [],
        $ver
    );

    wp_enqueue_style('vp-profile-css', $uri . '/css/pages/profile.css', ['vp-user-account'], $ver);
    wp_enqueue_style('vp-changepass-css', $uri . '/css/pages/changepass.css', ['vp-user-account'], $ver);
    wp_enqueue_style('vp-purhistory-css', $uri . '/css/pages/purchase-history.css', ['vp-user-account'], $ver);

    wp_enqueue_script(
        'vp-account',
        $uri . '/js/account.js',
        [],
        $ver,
        true
    );

    wp_enqueue_script('vp-profile-js', $uri . '/js/pages/profile.js', ['vp-account'], $ver, true);
    wp_enqueue_script('vp-changepass-js', $uri . '/js/pages/changepass.js', ['vp-account'], $ver, true);
    wp_enqueue_script('vp-purhistory-js', $uri . '/js/pages/purchase-history.js', ['vp-account'], $ver, true);

    wp_localize_script('vp-account', 'wpAccountData', [
        'apiBase' => home_url('/wp-json/vieproxy/v1'),
        'baseUrl' => home_url(),
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('account_nonce'),
        'loginUrl' => home_url('/dang-nhap'),
        'accountUrl' => home_url('/account'),
    ]);
});

function vp_ajax_load_account_page()
{
    if (!check_ajax_referer('account_nonce', 'nonce', false)) {
        wp_send_json_error('Invalid nonce', 403);
    }

    if (empty($_COOKIE['vp_jwt'])) {
        wp_send_json_error('Unauthenticated', 401);
    }

    $allowed_pages = [
        'profile',
        'change-password',
        'purchase-history',
    ];

    $page_slug = sanitize_key($_POST['page_slug'] ?? '');

    if (!in_array($page_slug, $allowed_pages, true)) {
        wp_send_json_error('Page not found', 404);
    }

    $template_map = [
        'profile' => 'page-profile.php',
        'change-password' => 'page-change-password.php',
        'purchase-history' => 'page-purchase-history.php',
    ];

    $template_file = locate_template($template_map[$page_slug]);

    if (!$template_file) {
        wp_send_json_error('Template file not found: ' . $template_map[$page_slug], 500);
    }

    // Buffer the template output
    ob_start();
    include $template_file;
    $html = ob_get_clean();

    wp_send_json_success($html);
}

// Hook for logged-in users
add_action('wp_ajax_load_account_page', 'vp_ajax_load_account_page');
// Also allow for users with valid JWT cookie but WP session not set
// (Edge case: JWT-only auth without WP cookie)
add_action('wp_ajax_nopriv_load_account_page', 'vp_ajax_load_account_page');

function vp_ajax_get_purchase_history()
{
    if (!check_ajax_referer('account_nonce', 'nonce', false)) {
        wp_send_json_error('Invalid nonce', 403);
    }

    if (empty($_COOKIE['vp_jwt'])) {
        wp_send_json_error('Unauthenticated', 401);
    }

    $user_id = 0;

    if (function_exists('vp_check_jwt')) {
        $jwt = sanitize_text_field($_COOKIE['vp_jwt']);
        $user_id = vp_decode_jwt_user_id($jwt);
    }

    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    if (!$user_id) {
        wp_send_json_error('Cannot identify user', 401);
    }

    $orders = [];

    if (function_exists('wc_get_orders')) {
        $wc_orders = wc_get_orders([
            'customer_id' => $user_id,
            'limit' => 50,
            'orderby' => 'date',
            'order' => 'DESC',
            'status' => ['wc-completed', 'wc-pending', 'wc-processing', 'wc-cancelled'],
        ]);

        foreach ($wc_orders as $wc_order) {
            $status_raw = $wc_order->get_status();
            $status = str_replace('wc-', '', $status_raw);

            $items = [];
            foreach ($wc_order->get_items() as $item) {
                $items[] = [
                    'name' => $item->get_name(),
                    'config' => '',
                    'cdk_info' => vp_get_order_cdk($wc_order, $item),
                    'price' => wc_price($item->get_total()),
                ];
            }

            $orders[] = [
                'order_number' => $wc_order->get_order_number(),
                'date' => $wc_order->get_date_created()
                    ? $wc_order->get_date_created()->date('d/m/Y H:i')
                    : '',
                'status' => $status,
                'total' => wc_price($wc_order->get_total()),
                'payment_url' => $status === 'pending' ? $wc_order->get_checkout_payment_url() : '',
                'items' => $items,
            ];
        }

    } else {

    }

    wp_send_json_success(['orders' => $orders]);
}

add_action('wp_ajax_get_purchase_history', 'vp_ajax_get_purchase_history');
add_action('wp_ajax_nopriv_get_purchase_history', 'vp_ajax_get_purchase_history');



if (!function_exists('vp_decode_jwt_user_id')) {
    function vp_decode_jwt_user_id($jwt)
    {
        if (empty($jwt))
            return 0;

        $parts = explode('.', $jwt);
        if (count($parts) !== 3)
            return 0;


        $payload_b64 = str_replace(['-', '_'], ['+', '/'], $parts[1]);
        $payload_b64 = str_pad($payload_b64, strlen($payload_b64) % 4, '=', STR_PAD_RIGHT);
        $payload = json_decode(base64_decode($payload_b64), true);


        return (int) (
            $payload['sub'] ??
            $payload['user_id'] ??
            $payload['id'] ??
            0
        );
    }
}

if (!function_exists('vp_get_order_cdk')) {
    function vp_get_order_cdk($order, $item)
    {
        $cdk = $item->get_meta('_cdk') ?: $item->get_meta('cdk') ?: '';
        if ($cdk)
            return 'CDK: ' . $cdk;

        $order_status = str_replace('wc-', '', $order->get_status());
        if ($order_status === 'pending')
            return 'CDK: Đang chờ thanh toán';
        if ($order_status === 'processing')
            return 'CDK: Đang xử lý';
        return 'CDK: ' . $cdk;
    }
}