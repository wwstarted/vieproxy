<?php
/**
 * VieProxy - User Authentication API
 * JWT + Google Login + OTP + Wallet
 *
 * Fix so với TradeProxy cũ:
 *  1. Xóa endpoint /logout trùng lặp
 *  2. Bỏ debug_otp khỏi response production
 *  3. JWT_SECRET_KEY đọc từ wp-config.php (define ở đó)
 *  4. Login chuyển sang AJAX endpoint riêng
 *  5. reCAPTCHA v2 validate phía server
 */

date_default_timezone_set('Asia/Ho_Chi_Minh');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// ─── JWT Secret ────────────────────────────────────────────────────────────
// Định nghĩa trong wp-config.php:  define('VIEPROXY_JWT_SECRET', 'your-secret');
if (!defined('VIEPROXY_JWT_SECRET')) {
    define('VIEPROXY_JWT_SECRET', 'vieproxy-change-this-secret-2024');
}

// ─── reCAPTCHA Secret ──────────────────────────────────────────────────────
// Định nghĩa trong wp-config.php:  define('RECAPTCHA_SECRET_KEY', 'your-secret');
if (!defined('RECAPTCHA_SECRET_KEY')) {
    define('RECAPTCHA_SECRET_KEY', 'YOUR_RECAPTCHA_SECRET_KEY');
}

// ─── Google Client ID ─────────────────────────────────────────────────────
if (!defined('GOOGLE_CLIENT_ID')) {
    define('GOOGLE_CLIENT_ID', '1039910145576-cthvk2plbd1l3320nd3ieibvb5bb3o13.apps.googleusercontent.com');
}

// ══════════════════════════════════════════════════════════════════════════════
//  REGISTER REST ROUTES
// ══════════════════════════════════════════════════════════════════════════════
add_action('rest_api_init', function () {

    // ── Auth ────────────────────────────────────────────────────────────────
    register_rest_route('vieproxy/v1', '/auth/login', [
        'methods' => 'POST',
        'callback' => 'vp_handle_login',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('vieproxy/v1', '/auth/google', [
        'methods' => 'POST',
        'callback' => 'vp_handle_google_login',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('vieproxy/v1', '/auth/logout', [
        'methods' => 'POST',
        'callback' => 'vp_handle_logout',
        'permission_callback' => '__return_true',
    ]);

    // ── Profile ─────────────────────────────────────────────────────────────
    register_rest_route('vieproxy/v1', '/user/profile', [
        [
            'methods' => 'GET',
            'callback' => 'vp_get_profile',
            'permission_callback' => 'vp_check_jwt',
        ],
        [
            'methods' => 'POST',
            'callback' => 'vp_update_profile',
            'permission_callback' => 'vp_check_jwt',
        ],
    ]);

    // ── Change password ─────────────────────────────────────────────────────
    register_rest_route('vieproxy/v1', '/user/change-password', [
        'methods' => 'POST',
        'callback' => 'vp_change_password',
        'permission_callback' => 'vp_check_jwt',
    ]);

    // ── Wallet ───────────────────────────────────────────────────────────────
    register_rest_route('vieproxy/v1', '/wallet/balance', [
        'methods' => 'GET',
        'callback' => 'vp_get_wallet_balance',
        'permission_callback' => 'vp_check_jwt',
    ]);

    // ── OTP: email verify ────────────────────────────────────────────────────
    register_rest_route('vieproxy/v1', '/otp/send-email', [
        'methods' => 'POST',
        'callback' => 'vp_send_email_otp',
        'permission_callback' => 'vp_check_jwt',
    ]);
    register_rest_route('vieproxy/v1', '/otp/verify-email', [
        'methods' => 'POST',
        'callback' => 'vp_verify_email_otp',
        'permission_callback' => 'vp_check_jwt',
    ]);

    // ── OTP: change password ─────────────────────────────────────────────────
    register_rest_route('vieproxy/v1', '/otp/send-change-password', [
        'methods' => 'POST',
        'callback' => 'vp_send_change_password_otp',
        'permission_callback' => 'vp_check_jwt',
    ]);
    register_rest_route('vieproxy/v1', '/otp/verify-change-password', [
        'methods' => 'POST',
        'callback' => 'vp_verify_change_password_otp',
        'permission_callback' => 'vp_check_jwt',
    ]);

    // ── OTP: forgot password (public) ────────────────────────────────────────
    register_rest_route('vieproxy/v1', '/otp/send-forgot-password', [
        'methods' => 'POST',
        'callback' => 'vp_send_forgot_password_otp',
        'permission_callback' => '__return_true',
    ]);
    register_rest_route('vieproxy/v1', '/otp/verify-forgot-password', [
        'methods' => 'POST',
        'callback' => 'vp_verify_and_reset_password',
        'permission_callback' => '__return_true',
    ]);
});

// ══════════════════════════════════════════════════════════════════════════════
//  HELPERS
// ══════════════════════════════════════════════════════════════════════════════

/**
 * Tạo JWT token (7 ngày)
 */
function vp_generate_jwt(int $user_id): string
{
    $payload = [
        'iss' => get_bloginfo('url'),
        'iat' => time(),
        'exp' => time() + (7 * DAY_IN_SECONDS),
        'user_id' => $user_id,
    ];
    return JWT::encode($payload, VIEPROXY_JWT_SECRET, 'HS256');
}

/**
 * Giải mã JWT token
 */
function vp_decode_jwt(string $token)
{
    return JWT::decode($token, new Key(VIEPROXY_JWT_SECRET, 'HS256'));
}

/**
 * Permission callback: xác thực Bearer token
 */
function vp_check_jwt()
{
    $auth = '';
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $auth = $_SERVER['HTTP_AUTHORIZATION'];
    } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $auth = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    } elseif (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        $auth = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    }

    if (empty($auth)) {
        return new WP_Error('no_token', 'Không tìm thấy token xác thực', ['status' => 401]);
    }

    $parts = explode(' ', trim($auth), 2);
    if (count($parts) !== 2 || $parts[0] !== 'Bearer') {
        return new WP_Error('invalid_token', 'Token không đúng định dạng', ['status' => 401]);
    }

    try {
        $decoded = vp_decode_jwt($parts[1]);
        // Gắn vào global để các callback dùng
        $GLOBALS['vp_current_user_id'] = (int) $decoded->user_id;
        return true;
    } catch (Exception $e) {
        return new WP_Error('invalid_token', 'Token lỗi: ' . $e->getMessage(), ['status' => 401]);
    }
}

/**
 * Lấy user_id hiện tại (sau khi vp_check_jwt đã chạy)
 */
function vp_current_user_id(): int
{
    return (int) ($GLOBALS['vp_current_user_id'] ?? 0);
}

/**
 * Validate reCAPTCHA v2 token
 */
function vp_verify_recaptcha(string $token): bool
{
    if (empty($token))
        return false;

    $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
        'body' => [
            'secret' => RECAPTCHA_SECRET_KEY,
            'response' => $token,
        ],
    ]);

    if (is_wp_error($response))
        return false;

    $body = json_decode(wp_remote_retrieve_body($response), true);
    return !empty($body['success']);
}

/**
 * Generate OTP số
 */
function vp_generate_otp(int $length = 6): string
{
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= mt_rand(0, 9);
    }
    return $otp;
}

/**
 * Gắn JWT vào httpOnly cookie + trả về token để JS lưu localStorage
 */
function vp_set_auth_cookie(string $token): void
{
    setcookie('vp_jwt', $token, time() + (7 * DAY_IN_SECONDS), '/', '', is_ssl(), true);
}

/**
 * Xóa JWT cookie
 */
function vp_clear_auth_cookie(): void
{
    setcookie('vp_jwt', '', time() - 3600, '/', '', is_ssl(), true);
}

// ══════════════════════════════════════════════════════════════════════════════
//  AUTH HANDLERS
// ══════════════════════════════════════════════════════════════════════════════

/**
 * POST /vieproxy/v1/auth/login
 * Body: { email, password, recaptcha_token }
 */
function vp_handle_login(WP_REST_Request $request)
{
    $params = $request->get_json_params();

    $email = sanitize_email($params['email'] ?? '');
    $password = $params['password'] ?? '';
    $captcha = sanitize_text_field($params['recaptcha_token'] ?? '');

    // ── Validation ──────────────────────────────────────────────────────────
    if (empty($email) || empty($password)) {
        return new WP_Error('missing_fields', 'Vui lòng nhập đầy đủ email và mật khẩu', ['status' => 400]);
    }

    if (!is_email($email)) {
        return new WP_Error('invalid_email', 'Email không hợp lệ', ['status' => 400]);
    }

    // ── reCAPTCHA ───────────────────────────────────────────────────────────
    if (!vp_verify_recaptcha($captcha)) {
        return new WP_Error('captcha_failed', 'Xác minh reCAPTCHA thất bại', ['status' => 400]);
    }

    // ── Authenticate ────────────────────────────────────────────────────────
    $user_obj = get_user_by('email', $email);
    if (!$user_obj) {
        return new WP_Error('invalid_credentials', 'Email hoặc mật khẩu không đúng', ['status' => 401]);
    }

    $user = wp_authenticate($user_obj->user_login, $password);
    if (is_wp_error($user)) {
        return new WP_Error('invalid_credentials', 'Email hoặc mật khẩu không đúng', ['status' => 401]);
    }

    // ── Issue token ─────────────────────────────────────────────────────────
    $token = vp_generate_jwt($user->ID);
    vp_set_auth_cookie($token);

    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID);
    do_action('wp_login', $user->user_login, $user);

    return new WP_REST_Response([
        'success' => true,
        'message' => 'Đăng nhập thành công',
        'token' => $token,
        'user' => [
            'id' => $user->ID,
            'display_name' => $user->display_name,
            'email' => $user->user_email,
        ],
    ], 200);
}

/**
 * POST /vieproxy/v1/auth/google
 * Body: { credential (Google ID token) }
 */
function vp_handle_google_login(WP_REST_Request $request)
{
    $params = $request->get_json_params();
    $id_token = sanitize_text_field($params['credential'] ?? '');

    if (empty($id_token)) {
        return new WP_Error('missing_credential', 'Thiếu Google credential', ['status' => 400]);
    }

    // Verify token với Google
    $response = wp_remote_get("https://oauth2.googleapis.com/tokeninfo?id_token={$id_token}");
    if (is_wp_error($response)) {
        return new WP_Error('google_error', 'Không thể kết nối Google', ['status' => 500]);
    }

    $user_data = json_decode(wp_remote_retrieve_body($response), true);

    // Kiểm tra aud (client ID) để tránh token của app khác
    if (empty($user_data['email']) || ($user_data['aud'] ?? '') !== GOOGLE_CLIENT_ID) {
        return new WP_Error('invalid_google_token', 'Google token không hợp lệ', ['status' => 401]);
    }

    $email = sanitize_email($user_data['email']);

    // Tìm hoặc tạo user
    $user = get_user_by('email', $email);
    if (!$user) {
        $user_id = wp_insert_user([
            'user_login' => $email,
            'user_email' => $email,
            'first_name' => sanitize_text_field($user_data['given_name'] ?? ''),
            'last_name' => sanitize_text_field($user_data['family_name'] ?? ''),
            'display_name' => sanitize_text_field($user_data['name'] ?? $email),
            'role' => 'subscriber',
            'user_pass' => wp_generate_password(32),
        ]);

        if (is_wp_error($user_id)) {
            return new WP_Error('register_failed', 'Không thể tạo tài khoản', ['status' => 500]);
        }

        // Đánh dấu tài khoản Google
        update_user_meta($user_id, 'google_login', true);
        $user = get_user_by('id', $user_id);
    }

    $token = vp_generate_jwt($user->ID);
    vp_set_auth_cookie($token);

    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID);
    do_action('wp_login', $user->user_login, $user);

    return new WP_REST_Response([
        'success' => true,
        'message' => 'Đăng nhập Google thành công',
        'token' => $token,
        'user' => [
            'id' => $user->ID,
            'display_name' => $user->display_name,
            'email' => $user->user_email,
        ],
    ], 200);
}

/**
 * POST /vieproxy/v1/auth/logout
 */
function vp_handle_logout(WP_REST_Request $request)
{
    vp_clear_auth_cookie();
    wp_logout();
    wp_clear_auth_cookie();

    return new WP_REST_Response([
        'success' => true,
        'message' => 'Đăng xuất thành công',
    ], 200);
}

// ══════════════════════════════════════════════════════════════════════════════
//  PROFILE
// ══════════════════════════════════════════════════════════════════════════════

function vp_get_profile()
{
    $user_id = vp_current_user_id();
    $user = get_userdata($user_id);

    if (!$user) {
        return new WP_Error('not_found', 'Không tìm thấy người dùng', ['status' => 404]);
    }

    return [
        'display_name' => $user->display_name,
        'email' => $user->user_email,
        'phone' => get_user_meta($user_id, 'phone', true) ?: '',
        'google_login' => (bool) get_user_meta($user_id, 'google_login', true),
    ];
}

function vp_update_profile(WP_REST_Request $request)
{
    $user_id = vp_current_user_id();
    $params = $request->get_json_params();

    if (isset($params['display_name'])) {
        wp_update_user([
            'ID' => $user_id,
            'display_name' => sanitize_text_field($params['display_name']),
        ]);
    }

    if (isset($params['email'])) {
        $new_email = sanitize_email($params['email']);
        $curr_user = get_userdata($user_id);

        if ($new_email !== $curr_user->user_email) {
            if (!is_email($new_email)) {
                return new WP_Error('invalid_email', 'Email không hợp lệ', ['status' => 400]);
            }
            if (email_exists($new_email)) {
                return new WP_Error('email_exists', 'Email đã được sử dụng', ['status' => 400]);
            }
            wp_update_user(['ID' => $user_id, 'user_email' => $new_email]);
        }
    }

    if (isset($params['phone'])) {
        $phone = sanitize_text_field($params['phone']);
        if (!empty($phone) && !preg_match('/^(\+\d{1,3})?\d{7,15}$/', $phone)) {
            return new WP_Error('invalid_phone', 'Số điện thoại không hợp lệ', ['status' => 400]);
        }
        update_user_meta($user_id, 'phone', $phone);
    }

    $user = get_userdata($user_id);
    return [
        'success' => true,
        'message' => 'Cập nhật thành công',
        'data' => [
            'display_name' => $user->display_name,
            'email' => $user->user_email,
            'phone' => get_user_meta($user_id, 'phone', true),
        ],
    ];
}

// ══════════════════════════════════════════════════════════════════════════════
//  CHANGE PASSWORD
// ══════════════════════════════════════════════════════════════════════════════

function vp_change_password(WP_REST_Request $request)
{
    $user_id = vp_current_user_id();
    if (!$user_id) {
        return new WP_Error('unauthorized', 'Phiên đăng nhập hết hạn', ['status' => 401]);
    }

    $user = get_userdata($user_id);
    $params = $request->get_json_params();

    $current_password = $params['current_password'] ?? '';
    $new_password = $params['new_password'] ?? '';

    if (!wp_check_password($current_password, $user->user_pass, $user_id)) {
        return new WP_Error('invalid_password', 'Mật khẩu cũ không đúng', ['status' => 401]);
    }

    if ($current_password === $new_password) {
        return new WP_Error('same_password', 'Mật khẩu mới không được trùng mật khẩu cũ', ['status' => 400]);
    }

    if (strlen($new_password) < 8) {
        return new WP_Error('weak_password', 'Mật khẩu phải có ít nhất 8 ký tự', ['status' => 400]);
    }

    if (
        !preg_match('/[a-z]/', $new_password) ||
        !preg_match('/[A-Z]/', $new_password) ||
        !preg_match('/[0-9]/', $new_password)
    ) {
        return new WP_Error('weak_password', 'Mật khẩu phải chứa chữ hoa, chữ thường và số', ['status' => 400]);
    }

    wp_set_password($new_password, $user_id);

    error_log(sprintf(
        '[VP_CHANGE_PASSWORD] User ID: %d | Email: %s | Time: %s',
        $user_id,
        $user->user_email,
        current_time('mysql')
    ));

    return new WP_REST_Response([
        'success' => true,
        'message' => 'Đổi mật khẩu thành công',
        'changed_at' => current_time('mysql'),
    ], 200);
}

// ══════════════════════════════════════════════════════════════════════════════
//  WALLET
// ══════════════════════════════════════════════════════════════════════════════

function vp_get_wallet_balance()
{
    $user_id = vp_current_user_id();
    $balance = (float) get_user_meta($user_id, 'wallet_balance', true);
    return [
        'balance' => $balance,
        'formatted' => number_format($balance, 0, ',', '.') . ' VND',
    ];
}

function vp_wallet_adjust(int $user_id, float $amount): float
{
    $current = (float) get_user_meta($user_id, 'wallet_balance', true);
    $new = max(0, $current + $amount);
    update_user_meta($user_id, 'wallet_balance', $new);
    return $new;
}

// Khởi tạo wallet khi đăng ký
add_action('user_register', function ($user_id) {
    update_user_meta($user_id, 'wallet_balance', 0);
});

// ══════════════════════════════════════════════════════════════════════════════
//  OTP: EMAIL VERIFY
// ══════════════════════════════════════════════════════════════════════════════

function vp_send_email_otp(WP_REST_Request $request)
{
    $user_id = vp_current_user_id();
    $email = sanitize_email($request->get_param('email'));

    if (!is_email($email)) {
        return new WP_Error('invalid_email', 'Email không hợp lệ', ['status' => 400]);
    }

    $otp = vp_generate_otp();
    $now = time();
    $exp = $now + 300;
    $key = 'vp_otp_email_' . $user_id;

    set_transient($key, [
        'otp' => $otp,
        'email' => $email,
        'expires_at' => $exp,
        'attempts' => 0,
        'created_at' => $now,
    ], 300);

    $sent = wp_mail(
        $email,
        '[VieProxy] Mã xác thực OTP',
        vp_otp_email_template($otp, 'Xác thực Email'),
        ['Content-Type: text/html; charset=UTF-8']
    );

    if (!$sent) {
        return new WP_Error('email_failed', 'Không thể gửi email', ['status' => 500]);
    }

    return ['success' => true, 'message' => "Mã OTP đã được gửi đến {$email}", 'expires_in' => 300];
}

function vp_verify_email_otp(WP_REST_Request $request)
{
    $user_id = vp_current_user_id();
    $otp_input = sanitize_text_field($request->get_param('otp'));
    $email = sanitize_email($request->get_param('email'));
    $key = 'vp_otp_email_' . $user_id;

    return vp_validate_otp($key, $otp_input, $email);
}

// ══════════════════════════════════════════════════════════════════════════════
//  OTP: CHANGE PASSWORD
// ══════════════════════════════════════════════════════════════════════════════

function vp_send_change_password_otp(WP_REST_Request $request)
{
    $user_id = vp_current_user_id();
    $user = get_userdata($user_id);
    $email = $user->user_email;

    $otp = vp_generate_otp();
    $now = time();
    $key = 'vp_otp_chpass_' . $user_id;

    set_transient($key, [
        'otp' => $otp,
        'email' => $email,
        'expires_at' => $now + 300,
        'attempts' => 0,
        'created_at' => $now,
    ], 300);

    $sent = wp_mail(
        $email,
        '[VieProxy] Mã xác thực đổi mật khẩu',
        vp_otp_email_template($otp, 'Xác thực đổi mật khẩu'),
        ['Content-Type: text/html; charset=UTF-8']
    );

    if (!$sent) {
        return new WP_Error('email_failed', 'Không thể gửi email', ['status' => 500]);
    }

    return ['success' => true, 'message' => "Mã OTP đã gửi đến {$email}", 'expires_in' => 300];
}

function vp_verify_change_password_otp(WP_REST_Request $request)
{
    $user_id = vp_current_user_id();
    $otp_input = sanitize_text_field($request->get_param('otp'));
    $key = 'vp_otp_chpass_' . $user_id;

    return vp_validate_otp($key, $otp_input);
}

// ══════════════════════════════════════════════════════════════════════════════
//  OTP: FORGOT PASSWORD (public)
// ══════════════════════════════════════════════════════════════════════════════

function vp_send_forgot_password_otp(WP_REST_Request $request)
{
    $email = sanitize_email($request->get_param('email'));

    if (!is_email($email)) {
        return new WP_Error('invalid_email', 'Email không hợp lệ', ['status' => 400]);
    }

    $user = get_user_by('email', $email);
    if (!$user) {
        // Trả về success để tránh email enumeration
        return ['success' => true, 'message' => 'Nếu email tồn tại, mã OTP đã được gửi', 'expires_in' => 300];
    }

    $otp = vp_generate_otp();
    $now = time();
    $key = 'vp_otp_forgot_' . md5($email);

    set_transient($key, [
        'otp' => $otp,
        'email' => $email,
        'user_id' => $user->ID,
        'expires_at' => $now + 300,
        'attempts' => 0,
        'created_at' => $now,
    ], 300);

    $sent = wp_mail(
        $email,
        '[VieProxy] Mã khôi phục mật khẩu',
        vp_otp_email_template($otp, 'Khôi phục mật khẩu', $user->display_name),
        ['Content-Type: text/html; charset=UTF-8']
    );

    if (!$sent) {
        return new WP_Error('email_failed', 'Không thể gửi email', ['status' => 500]);
    }

    return ['success' => true, 'message' => 'Nếu email tồn tại, mã OTP đã được gửi', 'expires_in' => 300];
}

function vp_verify_and_reset_password(WP_REST_Request $request)
{
    $email = sanitize_email($request->get_param('email'));
    $otp_input = sanitize_text_field($request->get_param('otp'));
    $new_password = $request->get_param('new_password');
    $confirm_password = $request->get_param('confirm_password');

    if (!is_email($email)) {
        return new WP_Error('invalid_email', 'Email không hợp lệ', ['status' => 400]);
    }

    if (strlen($new_password) < 8) {
        return new WP_Error('weak_password', 'Mật khẩu phải có ít nhất 8 ký tự', ['status' => 400]);
    }

    if ($new_password !== $confirm_password) {
        return new WP_Error('password_mismatch', 'Mật khẩu xác nhận không khớp', ['status' => 400]);
    }

    $key = 'vp_otp_forgot_' . md5($email);
    $result = vp_validate_otp($key, $otp_input, $email);

    if (is_wp_error($result))
        return $result;

    // OTP ok → đổi mật khẩu
    $user = get_user_by('email', $email);
    if (!$user) {
        return new WP_Error('user_not_found', 'Không tìm thấy tài khoản', ['status' => 404]);
    }

    wp_set_password($new_password, $user->ID);

    // Gửi email xác nhận
    wp_mail(
        $email,
        '[VieProxy] Mật khẩu đã được thay đổi',
        vp_password_changed_email_template($user->display_name, $email),
        ['Content-Type: text/html; charset=UTF-8']
    );

    return ['success' => true, 'message' => 'Đặt lại mật khẩu thành công! Bạn có thể đăng nhập ngay.'];
}

// ══════════════════════════════════════════════════════════════════════════════
//  OTP SHARED VALIDATOR
// ══════════════════════════════════════════════════════════════════════════════

/**
 * Validate OTP từ transient
 * $email_check: nếu truyền vào sẽ kiểm tra khớp email
 */
function vp_validate_otp(string $key, string $otp_input, string $email_check = '')
{
    $data = get_transient($key);

    if (!$data || !is_array($data)) {
        return new WP_Error('no_otp', 'Không tìm thấy OTP. Vui lòng gửi lại.', ['status' => 400]);
    }

    $now = time();
    $expires_at = (int) $data['expires_at'];
    $attempts = (int) $data['attempts'];
    $time_left = $expires_at - $now;

    if ($attempts >= 3) {
        delete_transient($key);
        return new WP_Error('too_many_attempts', 'Sai quá 3 lần. Vui lòng yêu cầu mã mới.', ['status' => 429]);
    }

    if ($now > $expires_at) {
        delete_transient($key);
        return new WP_Error('otp_expired', 'Mã OTP đã hết hạn.', ['status' => 400]);
    }

    if (!empty($email_check) && $email_check !== $data['email']) {
        return new WP_Error('email_mismatch', 'Email không khớp', ['status' => 400]);
    }

    if ($otp_input !== $data['otp']) {
        $data['attempts'] = $attempts + 1;
        set_transient($key, $data, $time_left);
        $left = 3 - $data['attempts'];
        return new WP_Error('invalid_otp', "Mã OTP không đúng. Còn {$left} lần thử.", ['status' => 400]);
    }

    delete_transient($key);
    return ['success' => true, 'message' => 'Xác thực thành công'];
}

// ══════════════════════════════════════════════════════════════════════════════
//  EMAIL TEMPLATES
// ══════════════════════════════════════════════════════════════════════════════

function vp_otp_email_template(string $otp, string $title, string $name = ''): string
{
    $greeting = $name ? "<p>Xin chào <strong>{$name}</strong>,</p>" : '';
    return "
    <div style='font-family:Arial,sans-serif;max-width:560px;margin:0 auto;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;'>
        <div style='background:#0ea5e9;padding:24px 32px;'>
            <h2 style='color:#fff;margin:0;font-size:20px;'>{$title}</h2>
        </div>
        <div style='padding:32px;'>
            {$greeting}
            <p style='color:#4b5563;margin-bottom:24px;'>Mã OTP của bạn:</p>
            <div style='background:#f0f9ff;border:2px dashed #0ea5e9;border-radius:8px;text-align:center;padding:20px;margin-bottom:24px;'>
                <span style='font-size:36px;font-weight:700;letter-spacing:12px;color:#0ea5e9;'>{$otp}</span>
            </div>
            <p style='color:#6b7280;font-size:14px;'>Mã có hiệu lực trong <strong>5 phút</strong>.</p>
            <p style='color:#ef4444;font-size:13px;font-weight:600;'>⚠️ Không chia sẻ mã này với bất kỳ ai.</p>
        </div>
        <div style='background:#f9fafb;padding:16px 32px;border-top:1px solid #e5e7eb;'>
            <p style='color:#9ca3af;font-size:12px;margin:0;'>Email tự động từ VieProxy — Vui lòng không trả lời.</p>
        </div>
    </div>";
}

function vp_password_changed_email_template(string $name, string $email): string
{
    $time = current_time('d/m/Y H:i:s');
    return "
    <div style='font-family:Arial,sans-serif;max-width:560px;margin:0 auto;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;'>
        <div style='background:#10b981;padding:24px 32px;'>
            <h2 style='color:#fff;margin:0;'>✓ Mật khẩu đã được thay đổi</h2>
        </div>
        <div style='padding:32px;'>
            <p>Xin chào <strong>{$name}</strong>,</p>
            <p>Mật khẩu tài khoản <strong>{$email}</strong> đã được thay đổi lúc <strong>{$time}</strong>.</p>
            <p style='color:#ef4444;font-weight:600;'>⚠️ Nếu bạn không thực hiện việc này, hãy liên hệ chúng tôi ngay.</p>
        </div>
        <div style='background:#f9fafb;padding:16px 32px;border-top:1px solid #e5e7eb;'>
            <p style='color:#9ca3af;font-size:12px;margin:0;'>Email tự động từ VieProxy — Vui lòng không trả lời.</p>
        </div>
    </div>";
}


// ══════════════════════════════════════════════════════════════════════════════
//  REGISTER: SEND OTP + VERIFY OTP → CREATE ACCOUNT
// ══════════════════════════════════════════════════════════════════════════════

add_action('rest_api_init', function () {

    // Gửi OTP xác thực email đăng ký (public)
    register_rest_route('vieproxy/v1', '/auth/register-send-otp', [
        'methods' => 'POST',
        'callback' => 'vp_register_send_otp',
        'permission_callback' => '__return_true',
    ]);

    // Verify OTP + tạo tài khoản (public)
    register_rest_route('vieproxy/v1', '/auth/register-verify-otp', [
        'methods' => 'POST',
        'callback' => 'vp_register_verify_and_create',
        'permission_callback' => '__return_true',
    ]);
});

/**
 * Bước 1: Validate email → Send OTP
 * Body: { email, recaptcha_token }
 */
function vp_register_send_otp(WP_REST_Request $request)
{
    $params = $request->get_json_params();
    $email = sanitize_email($params['email'] ?? '');
    $captcha = sanitize_text_field($params['recaptcha_token'] ?? '');

    // Validate email format
    if (!is_email($email)) {
        return new WP_Error('invalid_email', 'Email không hợp lệ', ['status' => 400]);
    }

    // Kiểm tra email đã tồn tại chưa
    if (email_exists($email)) {
        return new WP_Error('email_exists', 'Email này đã được sử dụng. Vui lòng đăng nhập.', ['status' => 409]);
    }

    // Validate reCAPTCHA (bỏ qua khi resend với token = 'resend')
    if ($captcha !== 'resend' && !vp_verify_recaptcha($captcha)) {
        return new WP_Error('captcha_failed', 'Xác minh reCAPTCHA thất bại', ['status' => 400]);
    }

    // Rate limit: 1 lần / phút
    $rate_key = 'vp_reg_rate_' . md5($email);
    if (get_transient($rate_key)) {
        return new WP_Error('rate_limit', 'Vui lòng đợi 1 phút trước khi gửi lại.', ['status' => 429]);
    }
    set_transient($rate_key, 1, 60); // 60 giây

    // Tạo và lưu OTP
    $otp = vp_generate_otp();
    $now = time();
    $key = 'vp_otp_reg_' . md5($email);

    set_transient($key, [
        'otp' => $otp,
        'email' => $email,
        'expires_at' => $now + 300,
        'attempts' => 0,
        'created_at' => $now,
    ], 300);

    // Gửi email
    $sent = wp_mail(
        $email,
        '[VieProxy] Mã xác thực đăng ký tài khoản',
        vp_otp_email_template($otp, 'Xác thực đăng ký'),
        ['Content-Type: text/html; charset=UTF-8']
    );

    if (!$sent) {
        return new WP_Error('email_failed', 'Không thể gửi email. Vui lòng thử lại.', ['status' => 500]);
    }

    error_log(sprintf('[VP_REGISTER] OTP sent to %s at %s', $email, current_time('mysql')));

    return new WP_REST_Response([
        'success' => true,
        'message' => "Mã OTP đã được gửi đến {$email}",
        'expires_in' => 300,
    ], 200);
}

/**
 * Bước 2: Verify OTP → Tạo tài khoản
 * Body: { email, otp, first_name, last_name, password }
 */
function vp_register_verify_and_create(WP_REST_Request $request)
{
    $params = $request->get_json_params();
    $email = sanitize_email($params['email'] ?? '');
    $otp_input = sanitize_text_field($params['otp'] ?? '');
    $first_name = sanitize_text_field($params['first_name'] ?? '');
    $last_name = sanitize_text_field($params['last_name'] ?? '');
    $password = $params['password'] ?? '';

    // Validate input
    if (!is_email($email)) {
        return new WP_Error('invalid_email', 'Email không hợp lệ', ['status' => 400]);
    }
    if (empty($first_name) || empty($last_name)) {
        return new WP_Error('missing_name', 'Vui lòng nhập họ và tên', ['status' => 400]);
    }
    if (strlen($password) < 8) {
        return new WP_Error('weak_password', 'Mật khẩu phải có ít nhất 8 ký tự', ['status' => 400]);
    }

    // Double-check email chưa bị đăng ký trong lúc chờ OTP
    if (email_exists($email)) {
        return new WP_Error('email_exists', 'Email này đã được sử dụng.', ['status' => 409]);
    }

    // Validate OTP
    $key = 'vp_otp_reg_' . md5($email);
    $result = vp_validate_otp($key, $otp_input, $email);
    if (is_wp_error($result))
        return $result;

    // ── Tạo tài khoản ─────────────────────────────────────────────────
    $display_name = trim($first_name . ' ' . $last_name);
    $user_login = sanitize_user(strtolower($first_name . '.' . $last_name));

    // Đảm bảo username unique
    if (username_exists($user_login)) {
        $user_login = $user_login . '_' . wp_rand(100, 999);
    }

    $user_id = wp_insert_user([
        'user_login' => $user_login,
        'user_email' => $email,
        'user_pass' => $password,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'display_name' => $display_name,
        'role' => 'subscriber',
    ]);

    if (is_wp_error($user_id)) {
        return new WP_Error(
            'register_failed',
            'Đăng ký thất bại: ' . $user_id->get_error_message(),
            ['status' => 500]
        );
    }

    // Khởi tạo wallet
    update_user_meta($user_id, 'wallet_balance', 0);
    // Đánh dấu email đã verify
    update_user_meta($user_id, 'email_verified', true);

    // Gửi email chào mừng
    wp_mail(
        $email,
        '[VieProxy] Chào mừng bạn đến với VieProxy!',
        vp_welcome_email_template($display_name, $email),
        ['Content-Type: text/html; charset=UTF-8']
    );

    error_log(sprintf(
        '[VP_REGISTER] Account created — ID: %d | Email: %s | Time: %s',
        $user_id,
        $email,
        current_time('mysql')
    ));

    return new WP_REST_Response([
        'success' => true,
        'message' => 'Đăng ký thành công! Vui lòng đăng nhập.',
        'user_id' => $user_id,
    ], 201);
}

/**
 * Email template chào mừng
 */
function vp_welcome_email_template(string $name, string $email): string
{
    $site_name = get_bloginfo('name');
    $login_url = home_url('/dang-nhap');
    return "
    <div style='font-family:Arial,sans-serif;max-width:560px;margin:0 auto;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;'>
        <div style='background:linear-gradient(135deg,#0369a1,#0ea5e9);padding:32px;text-align:center;'>
            <h1 style='color:#fff;margin:0;font-size:24px;'>Chào mừng đến với {$site_name}! 🎉</h1>
        </div>
        <div style='padding:32px;'>
            <p>Xin chào <strong>{$name}</strong>,</p>
            <p style='color:#4b5563;margin:16px 0;'>Tài khoản của bạn đã được tạo thành công với email <strong>{$email}</strong>.</p>
            <div style='text-align:center;margin:28px 0;'>
                <a href='{$login_url}' style='background:linear-gradient(135deg,#0ea5e9,#0284c7);color:#fff;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;display:inline-block;'>Đăng nhập ngay</a>
            </div>
            <p style='color:#6b7280;font-size:14px;'>Nếu bạn không tạo tài khoản này, vui lòng bỏ qua email này.</p>
        </div>
        <div style='background:#f9fafb;padding:16px 32px;border-top:1px solid #e5e7eb;'>
            <p style='color:#9ca3af;font-size:12px;margin:0;'>Email tự động từ {$site_name} — Vui lòng không trả lời.</p>
        </div>
    </div>";
}