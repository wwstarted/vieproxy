<?php
/**
 * ============================================================
 * VIEPROXY — CONTACT FORM AJAX HANDLER
 */

/* ── Enqueue contact page assets ─────────────────────────── */
function vieproxy_enqueue_contact_assets()
{
    if (!is_page_template('page-contact.php'))
        return;

    // Depends on info-pages.css (breadcrumb, hero, back-to-top)
    wp_enqueue_style(
        'vieproxy-info-pages',
        get_template_directory_uri() . '/css/info-pages.css',
        [],
        '1.0.0'
    );

    wp_enqueue_style(
        'vieproxy-contact',
        get_template_directory_uri() . '/css/page-contact.css',
        ['vieproxy-info-pages'],
        '1.0.0'
    );

    wp_enqueue_script(
        'vieproxy-info-pages',
        get_template_directory_uri() . '/js/info-pages.js',
        [],
        '1.0.0',
        true
    );

    wp_enqueue_script(
        'vieproxy-contact',
        get_template_directory_uri() . '/js/page-contact.js',
        ['vieproxy-info-pages'],
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'vieproxy_enqueue_contact_assets');


add_action('wp_ajax_vieproxy_contact_submit', 'vieproxy_handle_contact_form');
add_action('wp_ajax_nopriv_vieproxy_contact_submit', 'vieproxy_handle_contact_form');

function vieproxy_handle_contact_form()
{

    // 1. Verify nonce
    if (
        !isset($_POST['contact_nonce']) ||
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['contact_nonce'])), 'vieproxy_contact_form')
    ) {
        wp_send_json_error(['message' => 'Yêu cầu không hợp lệ. Vui lòng tải lại trang.'], 403);
    }

    // 2. Rate limiting (simple: 3 submissions per IP per hour)
    $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : 'unknown';
    $rate_key = 'vieproxy_contact_rate_' . md5($ip);
    $rate_count = (int) get_transient($rate_key);

    if ($rate_count >= 3) {
        wp_send_json_error(['message' => 'Bạn đã gửi quá nhiều lần. Vui lòng thử lại sau 1 giờ.'], 429);
    }

    // 3. Sanitize & validate inputs
    $name = isset($_POST['contact_name']) ? sanitize_text_field(wp_unslash($_POST['contact_name'])) : '';
    $email = isset($_POST['contact_email']) ? sanitize_email(wp_unslash($_POST['contact_email'])) : '';
    $phone = isset($_POST['contact_phone']) ? sanitize_text_field(wp_unslash($_POST['contact_phone'])) : '';
    $subject = isset($_POST['contact_subject']) ? sanitize_text_field(wp_unslash($_POST['contact_subject'])) : '';
    $message = isset($_POST['contact_message']) ? sanitize_textarea_field(wp_unslash($_POST['contact_message'])) : '';
    $order = isset($_POST['contact_order']) ? sanitize_text_field(wp_unslash($_POST['contact_order'])) : '';
    $consent = isset($_POST['contact_consent']) ? (bool) $_POST['contact_consent'] : false;

    $errors = [];

    if (strlen($name) < 2)
        $errors[] = 'Họ tên quá ngắn.';
    if (!is_email($email))
        $errors[] = 'Email không hợp lệ.';
    if (empty($subject))
        $errors[] = 'Vui lòng chọn chủ đề.';
    if (strlen($message) < 20)
        $errors[] = 'Nội dung quá ngắn (tối thiểu 20 ký tự).';
    if (!$consent)
        $errors[] = 'Bạn cần đồng ý với chính sách bảo mật.';

    if (!empty($errors)) {
        wp_send_json_error(['message' => implode(' ', $errors)], 422);
    }

    // 4. Subject label map
    $subject_labels = [
        'mua-proxy' => 'Tư vấn mua proxy',
        'ky-thuat' => 'Hỗ trợ kỹ thuật',
        'thanh-toan' => 'Vấn đề thanh toán',
        'hoan-tien' => 'Yêu cầu hoàn tiền',
        'bao-cao-loi' => 'Báo cáo lỗi',
        'hop-tac' => 'Hợp tác kinh doanh',
        'khac' => 'Khác',
    ];

    $subject_label = isset($subject_labels[$subject]) ? $subject_labels[$subject] : $subject;

    // 5. Build email
    $admin_email = get_option('admin_email');
    $site_name = get_bloginfo('name');
    $date = wp_date('d/m/Y H:i', null, new DateTimeZone('Asia/Ho_Chi_Minh'));

    $email_subject = sprintf('[%s] Liên hệ mới: %s — %s', $site_name, $subject_label, $name);

    $email_body = "=== THÔNG TIN LIÊN HỆ MỚI ===\n\n";
    $email_body .= "Thời gian:   {$date}\n";
    $email_body .= "Họ tên:      {$name}\n";
    $email_body .= "Email:       {$email}\n";
    if ($phone)
        $email_body .= "Điện thoại:  {$phone}\n";
    $email_body .= "Chủ đề:      {$subject_label}\n";
    if ($order)
        $email_body .= "Mã đơn hàng: {$order}\n";
    $email_body .= "IP khách:    {$ip}\n";
    $email_body .= "\n=== NỘI DUNG ===\n\n{$message}\n\n";
    $email_body .= "---\nEmail này được gửi tự động từ form liên hệ tại " . home_url() . "\n";

    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
        'From: ' . $site_name . ' <' . $admin_email . '>',
    ];

    $sent = wp_mail($admin_email, $email_subject, $email_body, $headers);

    // 6. Auto-reply to user
    if ($sent) {
        $reply_subject = "Xác nhận nhận được yêu cầu của bạn — {$site_name}";
        $reply_body = "Xin chào {$name},\n\n";
        $reply_body .= "Cảm ơn bạn đã liên hệ với {$site_name}!\n\n";
        $reply_body .= "Chúng tôi đã nhận được tin nhắn của bạn với chủ đề: [{$subject_label}].\n";
        $reply_body .= "Đội ngũ hỗ trợ sẽ phản hồi trong vòng 24 giờ làm việc.\n\n";
        $reply_body .= "Để được hỗ trợ nhanh hơn, bạn có thể liên hệ trực tiếp:\n";
        $reply_body .= "• Telegram: @VieProxyVN\n";
        $reply_body .= "• Zalo: 034.770.0437\n\n";
        $reply_body .= "Trân trọng,\nĐội ngũ {$site_name}\n" . home_url() . "\n";

        $reply_headers = [
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . $site_name . ' <' . $admin_email . '>',
        ];

        wp_mail($email, $reply_subject, $reply_body, $reply_headers);
    }

    // 7. Update rate limit counter
    if ($rate_count === 0) {
        set_transient($rate_key, 1, HOUR_IN_SECONDS);
    } else {
        set_transient($rate_key, $rate_count + 1, HOUR_IN_SECONDS);
    }

    // 8. Optionally save to DB as a custom post (uncomment if needed)
    /*
    wp_insert_post([
        'post_type'   => 'contact_message',
        'post_title'  => "[{$subject_label}] {$name} — {$date}",
        'post_status' => 'private',
        'meta_input'  => [
            '_contact_name'    => $name,
            '_contact_email'   => $email,
            '_contact_phone'   => $phone,
            '_contact_subject' => $subject,
            '_contact_message' => $message,
            '_contact_order'   => $order,
            '_contact_ip'      => $ip,
        ],
    ]);
    */

    if ($sent) {
        wp_send_json_success(['message' => 'Tin nhắn đã được gửi thành công!']);
    } else {
        wp_send_json_error(['message' => 'Không thể gửi email. Vui lòng liên hệ qua Telegram.'], 500);
    }
}