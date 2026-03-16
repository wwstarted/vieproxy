<?php
if (empty($_COOKIE['vp_jwt'])) {
    wp_redirect(home_url('/dang-nhap'));
    exit;
}

add_action('wp_head', function () {
    $api_base = home_url('/wp-json/vieproxy/v1');
    ?>
<script>
window.wpAccountData = window.wpAccountData || {};
window.wpAccountData.apiBase = '<?php echo esc_js($api_base); ?>';
window.wpAccountData.baseUrl = '<?php echo esc_js(home_url()); ?>';
window.wpAccountData.ajaxUrl = '<?php echo esc_js(admin_url('admin-ajax.php')); ?>';
window.wpAccountData.nonce = '<?php echo esc_js(wp_create_nonce('account_nonce')); ?>';
window.wpAccountData.loginUrl = '<?php echo esc_js(home_url('/dang-nhap')); ?>';
window.wpAccountData.accountUrl = '<?php echo esc_js(home_url('/account')); ?>';
</script>
<?php
}, 1);

get_header();

// ── Determine active page from URL ──────────────────────────────────────────
$slug = get_post_field('post_name', get_queried_object_id());
$spa_pages = ['profile', 'change-password', 'purchase-history'];
$initial_page = in_array($slug, $spa_pages) ? $slug : 'profile';
?>

<div class="account-wrapper wrapper">

    <!-- ── Sidebar ─────────────────────────────────────────────────────── -->
    <aside class="sidebar-ac">

        <!-- User snapshot -->
        <div class="sidebar-user">
            <div class="sidebar-avatar" id="sidebarAvatar">
                <?php
                $wp_user = wp_get_current_user();
                $words = preg_split('/\s+/', trim($wp_user->display_name));
                $letter = mb_strtoupper(mb_substr(end($words), 0, 1, 'UTF-8'), 'UTF-8');
                echo esc_html($letter);
                ?>
            </div>
            <div class="sidebar-user-info">
                <strong class="sidebar-user-name">
                    <?php
                    $last_two = implode(' ', array_slice($words, -2));
                    echo esc_html($last_two);
                    ?>
                </strong>
                <span class="sidebar-user-email"><?php echo esc_html($wp_user->user_email); ?></span>
            </div>
        </div>

        <!-- Menu -->
        <nav class="sidebar-nav">

            <div class="sidebar-section">
                <p class="sidebar-section-label">Tài khoản</p>
                <a href="<?php echo esc_url(home_url('/profile')); ?>"
                    class="sidebar-item <?php echo $initial_page === 'profile' ? 'is-active' : ''; ?>"
                    data-page="profile">
                    <i class="fa-regular fa-circle-user"></i>
                    <span>Hồ sơ</span>
                </a>
                <a href="<?php echo esc_url(home_url('/change-password')); ?>"
                    class="sidebar-item <?php echo $initial_page === 'change-password' ? 'is-active' : ''; ?>"
                    data-page="change-password">
                    <i class="fa-solid fa-key"></i>
                    <span>Đổi mật khẩu</span>
                </a>
            </div>

            <div class="sidebar-section">
                <p class="sidebar-section-label">Giao dịch</p>
                <a href="<?php echo esc_url(home_url('/purchase-history')); ?>"
                    class="sidebar-item <?php echo $initial_page === 'purchase-history' ? 'is-active' : ''; ?>"
                    data-page="purchase-history">
                    <i class="fa-solid fa-receipt"></i>
                    <span>Lịch sử mua hàng</span>
                </a>
            </div>

            <div class="sidebar-section sidebar-section--logout">
                <button class="sidebar-item sidebar-item--danger" id="sidebarLogoutBtn">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Đăng xuất</span>
                </button>
            </div>

        </nav>
    </aside>

    <!-- ── Main content ─────────────────────────────────────────────────── -->
    <main class="account-content">
        <div id="page-content" data-initial-page="<?php echo esc_attr($initial_page); ?>">
            <div class="ac-loading">
                <i class="fa-solid fa-spinner fa-spin"></i>
                <p>Đang tải...</p>
            </div>
        </div>
    </main>

</div><!-- /.account-wrapper -->

<?php get_footer(); ?>