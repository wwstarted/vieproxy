<?php

if (!function_exists('vp_header_parse_name')) {
    function vp_header_parse_name($display_name)
    {
        if (empty($display_name)) {
            return ['name' => '', 'avatar' => ''];
        }
        $words = preg_split('/\s+/', trim($display_name));
        $last_two = implode(' ', array_slice($words, -2));
        $last = end($words);
        $avatar = mb_strtoupper(mb_substr($last, 0, 1, 'UTF-8'), 'UTF-8');
        return ['name' => $last_two, 'avatar' => $avatar];
    }
}
$vp_logged_in = false;
$vp_user = ['name' => '', 'email' => '', 'avatar' => '', 'is_logged_in' => false];

if (!empty($_COOKIE['vp_jwt'])) {
    $wp_user = wp_get_current_user();
    if ($wp_user && $wp_user->ID > 0) {
        $parsed = vp_header_parse_name($wp_user->display_name);
        $vp_logged_in = true;
        $vp_user = [
            'name' => $parsed['name'],
            'email' => $wp_user->user_email,
            'avatar' => $parsed['avatar'],
            'is_logged_in' => true,
        ];
    }
}

$s_flex = 'display:flex';
$s_block = 'display:block';
$s_none = 'display:none';

$st_dropdown = $vp_logged_in ? $s_flex : $s_none;
$st_auth_btn = $vp_logged_in ? $s_none : $s_flex;
$st_mobile_user = $vp_logged_in ? $s_flex : $s_none;
$st_mobile_menu = $vp_logged_in ? $s_block : $s_none;
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php wp_head(); ?>
    <script>
    window.vpHeaderData = {
        apiBase: '<?php echo esc_js(home_url('/wp-json/vieproxy/v1')); ?>',
        loginUrl: '<?php echo esc_js(home_url('/dang-nhap')); ?>',
        registerUrl: '<?php echo esc_js(home_url('/register')); ?>',
        accountUrl: '<?php echo esc_js(home_url('/account')); ?>',
        initialState: {
            isLoggedIn: <?php echo $vp_logged_in ? 'true' : 'false'; ?>,
            userName: '<?php echo esc_js($vp_user['name']); ?>',
            userEmail: '<?php echo esc_js($vp_user['email']); ?>',
            userAvatar: '<?php echo esc_js($vp_user['avatar']); ?>'
        }
    };
    </script>
</head>

<body>

    <header class="site-header">
        <div class="wrapper header-inner">

            <div class="header-logo">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/folder-logo/png/logo-blue.png"
                        alt="<?php bloginfo('name'); ?>" class="logo-img" />
                </a>
            </div>
            <div class="header-right">

                <nav class="header-nav">
                    <?php wp_nav_menu([
                        'theme_location' => 'header_menu',
                        'menu_class' => 'nav-menu',
                        'container' => false,
                        'fallback_cb' => false,
                        'depth' => 1,
                    ]); ?>
                </nav>

                <div class="header-cart">
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('gio-hang')) ?: home_url('/gio-hang')); ?>"
                        class="cart-btn" title="Giỏ hàng">
                        <i class="fa-solid fa-lock"></i>
                        <span class="cart-badge">0</span>
                    </a>
                </div>

                <div class="header-lang">
                    <button class="lang-toggle" id="langToggle" aria-haspopup="true" aria-expanded="false">
                        <span class="lang-flag">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/vietnam.png" alt="Tiếng Việt"
                                class="flag-icon" />
                        </span>
                        <i class="fa-solid fa-chevron-down lang-arrow"></i>
                    </button>
                    <ul class="lang-dropdown" id="langDropdown" role="menu">
                        <li role="menuitem">
                            <a href="#" class="lang-option lang-active">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/vietnam.png" alt=""
                                    class="flag-icon" />
                                <span>Tiếng Việt</span>
                            </a>
                        </li>
                        <li role="menuitem">
                            <a href="#" class="lang-option">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/united-kingdom.png" alt=""
                                    class="flag-icon" />
                                <span>English</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="vp-user-dropdown" id="userDropdown" style="<?php echo $st_dropdown; ?>">
                    <button class="vp-user-trigger" id="userTrigger" aria-haspopup="true" aria-expanded="false">
                        <span class="vp-avatar">
                            <span class="vp-avatar-letter" id="desktopAvatarLetter">
                                <?php echo esc_html($vp_user['avatar']); ?>
                            </span>
                        </span>
                        <span class="vp-user-name" id="desktopUserName">
                            <?php echo esc_html($vp_user['name']); ?>
                        </span>
                        <i class="fa-solid fa-chevron-down vp-user-arrow"></i>
                    </button>

                    <div class="vp-user-menu" id="userMenu" role="menu">
                        <a href="<?php echo esc_url(home_url('/account')); ?>" class="vp-menu-item" role="menuitem">
                            <i class="fa-regular fa-circle-user"></i>
                            <span>Tài khoản của tôi</span>
                        </a>
                        <a href="<?php echo esc_url(home_url('/wallet')); ?>" class="vp-menu-item" role="menuitem">
                            <i class="fa-regular fa-credit-card"></i>
                            <span>Ví của tôi</span>
                        </a>
                        <a href="<?php echo esc_url(home_url('/purchase-history')); ?>" class="vp-menu-item"
                            role="menuitem">
                            <i class="fa-regular fa-clock"></i>
                            <span>Lịch sử mua hàng</span>
                        </a>
                        <div class="vp-menu-divider"></div>
                        <button class="vp-menu-item vp-menu-item--danger" id="logoutBtn" role="menuitem">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>Đăng xuất</span>
                        </button>
                    </div>
                </div>


                <div class="header-auth" id="authButtons" style="<?php echo $st_auth_btn; ?>">
                    <a href="<?php echo esc_url(home_url('/dang-nhap')); ?>" class="btn btn-login">Đăng Nhập</a>
                    <a href="<?php echo esc_url(home_url('/register')); ?>" class="btn btn-register">Đăng Ký</a>
                </div>


                <div class="mobile-user-row" id="mobileUserRow" style="<?php echo $st_mobile_user; ?>">
                    <span class="vp-avatar vp-avatar--lg">
                        <span class="vp-avatar-letter" id="mobileAvatarLetter">
                            <?php echo esc_html($vp_user['avatar']); ?>
                        </span>
                    </span>
                    <div class="mobile-user-details">
                        <strong id="mobileUserName"><?php echo esc_html($vp_user['name']); ?></strong>
                        <small id="mobileUserEmail"><?php echo esc_html($vp_user['email']); ?></small>
                    </div>
                </div>

                <div class="mobile-user-menu" id="mobileUserMenu" style="<?php echo $st_mobile_menu; ?>">
                    <a href="<?php echo esc_url(home_url('/account')); ?>" class="mobile-nav-link">
                        <i class="fa-regular fa-circle-user"></i>
                        <span>Tài khoản của tôi</span>
                    </a>
                    <a href="<?php echo esc_url(home_url('/wallet')); ?>" class="mobile-nav-link">
                        <i class="fa-regular fa-credit-card"></i>
                        <span>Ví của tôi</span>
                    </a>
                    <a href="<?php echo esc_url(home_url('/purchase-history')); ?>" class="mobile-nav-link">
                        <i class="fa-regular fa-clock"></i>
                        <span>Lịch sử mua hàng</span>
                    </a>
                    <button class="mobile-nav-link mobile-nav-link--danger" id="mobileLogoutBtn">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Đăng xuất</span>
                    </button>
                </div>

                <div class="mobile-cart-row">
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('gio-hang')) ?: home_url('/gio-hang')); ?>"
                        class="mobile-cart-link">
                        <i class="fa-solid fa-lock"></i>
                        <span>Giỏ hàng</span>
                        <span class="cart-badge cart-badge--inline">0</span>
                    </a>
                </div>

                <div class="mobile-lang-row">
                    <button class="mobile-lang-toggle" id="mobileLangToggle" aria-haspopup="true" aria-expanded="false">
                        <img src="<?php echo get_template_directory_uri(); ?>/images/vietnam.png" alt="Tiếng Việt"
                            class="flag-icon" />
                        <span class="mobile-lang-label">Tiếng Việt</span>
                        <i class="fa-solid fa-chevron-down mobile-lang-arrow"></i>
                    </button>
                    <ul class="mobile-lang-dropdown" id="mobileLangDropdown" role="menu">
                        <li role="menuitem">
                            <a href="#" class="lang-option lang-active">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/vietnam.png" alt=""
                                    class="flag-icon" />
                                <span>Tiếng Việt</span>
                            </a>
                        </li>
                        <li role="menuitem">
                            <a href="#" class="lang-option">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/united-kingdom.png" alt=""
                                    class="flag-icon" />
                                <span>English</span>
                            </a>
                        </li>
                    </ul>
                </div>

            </div>

            <button class="hamburger" id="mobileMenuToggle" aria-label="Mở menu">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <div class="mobile-menu-overlay" id="mobile-menu-overlay">
                <div class="mobile-menu-content">
                    <div class=""></div>
                </div>
            </div>
        </div>
    </header>