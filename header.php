<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php wp_head(); ?>
</head>

<body>

    <header class="site-header">
        <div class="wrapper header-inner">

            <!-- Logo -->
            <div class="header-logo">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/folder-logo/png/logo-blue.png"
                        alt="<?php bloginfo('name'); ?>" class="logo-img" />
                </a>
            </div>

            <!-- Right side -->
            <div class="header-right">

                <!-- Navigation Menu -->
                <nav class="header-nav">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'header_menu',
                        'menu_class' => 'nav-menu',
                        'container' => false,
                        'fallback_cb' => false,
                        'depth' => 1,
                    ));
                    ?>
                </nav>

                <!-- Cart Icon -->
                <div class="header-cart">
                    <a href="#" class="cart-btn" title="Giỏ hàng">
                        <i class="fa-solid fa-lock"></i>
                        <span class="cart-badge">2</span>
                    </a>
                </div>

                <!-- Language Switcher -->
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

                <!-- Auth Buttons -->
                <div class="header-auth">
                    <a href="#" class="btn btn-login">Đăng Nhập</a>
                    <a href="#" class="btn btn-register">Đăng Ký</a>
                </div>

            </div>

            <!-- Mobile Hamburger -->
            <button class="hamburger" id="mobileMenuToggle" aria-label="Mở menu">
                <span></span>
                <span></span>
                <span></span>
            </button>


            <div class=""></div>



        </div>
    </header>