<?php

/**
 * Home Page Settings
 * Tab: Section Visibility + Hero Section + Why Choose (dynamic cards)
 */

if (!defined('ABSPATH')) exit;

function vieproxy_render_home_settings()
{
    // Danh sách tabs của Home page
    $tabs = array(
        'visibility' => array(
            'label' => 'Section Visibility',
            'icon'  => 'dashicons-visibility',
        ),
        'hero' => array(
            'label' => 'Hero Section',
            'icon'  => 'dashicons-slides',
        ),
        'proxy_list' => array(
            'label' => 'Proxy List Section',
            'icon'  => 'dashicons-list-view',
        ),
        'why_choose' => array(
            'label' => 'Why Choose Section',
            'icon'  => 'dashicons-star-filled',
        ),
        'commitment' => array(
            'label' => 'Commitment Section',
            'icon'  => 'dashicons-awards',
        ),
        'pricing' => array(
            'label' => 'Pricing Section',
            'icon'  => 'dashicons-money-alt',
        ),
        'coverage' => array(
            'label' => 'Coverage Section',
            'icon'  => 'dashicons-location-alt',
        ),
        'rating' => array(
            'label' => 'Rating Section',
            'icon'  => 'dashicons-star-half',
        ),
        'partners' => array(
            'label' => 'Partners Section',
            'icon'  => 'dashicons-groups',
        ),
        'faq' => array(
            'label' => 'FAQ Section',
            'icon'  => 'dashicons-editor-help',
        ),
    );

    $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'visibility';
    if (!array_key_exists($active_tab, $tabs)) {
        $active_tab = 'visibility';
    }
?>
    <div class="vp-admin-wrap">

        <!-- Header -->
        <div class="vp-admin-header">
            <div class="vp-admin-header__icon">
                <span class="dashicons dashicons-admin-home"></span>
            </div>
            <h1 class="vp-admin-header__title">Home Page Settings</h1>
        </div>

        <!-- Tab Nav -->
        <nav class="vp-tab-nav">
            <?php foreach ($tabs as $slug => $tab): ?>
                <a
                    href="<?php echo admin_url('admin.php?page=vieproxy-settings-home&tab=' . $slug); ?>"
                    class="vp-tab-nav__item <?php echo $active_tab === $slug ? 'is-active' : ''; ?>"
                    data-tab="<?php echo esc_attr($slug); ?>">
                    <span class="dashicons <?php echo esc_attr($tab['icon']); ?>"></span>
                    <?php echo esc_html($tab['label']); ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- Form -->
        <form id="vp-settings-form" method="post">
            <?php wp_nonce_field('vieproxy_save_options', '_wpnonce'); ?>
            <input type="hidden" name="action" value="vieproxy_save_options" />

            <!-- TAB: Section Visibility -->
            <div class="vp-tab-content <?php echo $active_tab === 'visibility' ? 'is-active' : ''; ?>" data-tab="visibility">
                <div class="vp-panel">
                    <div class="vp-panel__head">
                        <h2 class="vp-panel__title">Quản lý hiển thị Sections</h2>
                        <p class="vp-panel__subtitle">Bật/tắt hiển thị các sections trên trang Home</p>
                    </div>
                    <div class="vp-panel__body">
                        <div class="vp-visibility-grid">

                            <?php
                            $sections = array(
                                'hero'    => array(
                                    'label' => 'Hero Section',
                                    'desc'  => 'Banner chính đầu trang',
                                ),
                                'proxy_list' => array(
                                    'label' => 'Proxy List Section',
                                    'desc'  => 'Danh sách các loại proxy tốt nhất',
                                ),
                                'why_choose' => array(
                                    'label' => 'Why Choose Section',
                                    'desc'  => 'Vì sao nên lựa chọn VieProxy',
                                ),
                                'commitment' => array(
                                    'label' => 'Commitment Section',
                                    'desc'  => 'Cam kết giá tốt nhất thị trường',
                                ),
                                'pricing' => array(
                                    'label' => 'Pricing Section',
                                    'desc'  => 'Bảng giá các gói proxy ưu đãi nhất',
                                ),
                                'coverage' => array(
                                    'label' => 'Coverage Section',
                                    'desc'  => 'Bản đồ phủ sóng IP proxy toàn cầu',
                                ),
                                'rating' => array(
                                    'label' => 'Rating Section',
                                    'desc'  => 'Đánh giá từ khách hàng',
                                ),
                                'partners' => array(
                                    'label' => 'Partners Section',
                                    'desc'  => 'Đối tác của VieProxy',
                                ),
                                'faq' => array(
                                    'label' => 'FAQ Section',
                                    'desc'  => 'Câu hỏi thường gặp',
                                ),
                            );

                            foreach ($sections as $key => $section):
                                vieproxy_field_toggle(array(
                                    'key'     => 'section_' . $key,
                                    'label'   => $section['label'],
                                    'desc'    => $section['desc'],
                                    'default' => 1,
                                ));
                            endforeach;
                            ?>

                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB: Hero Section -->
            <div class="vp-tab-content <?php echo $active_tab === 'hero' ? 'is-active' : ''; ?>" data-tab="hero">
                <div class="vp-panel">
                    <div class="vp-panel__head">
                        <h2 class="vp-panel__title">Hero Section</h2>
                        <p class="vp-panel__subtitle">Nội dung phần banner đầu trang Home</p>
                    </div>
                    <div class="vp-panel__body">

                        <?php
                        vieproxy_field_text(array(
                            'key'     => 'hero_title',
                            'label'   => 'Tiêu đề (Title)',
                            'default' => 'Mua proxy cao cấp – giá tốt nhất tại VieProxy',
                            'desc'    => 'Tiêu đề lớn hiển thị ở phần hero. Hỗ trợ xuống dòng tự động.',
                        ));

                        vieproxy_field_textarea(array(
                            'key'     => 'hero_description',
                            'label'   => 'Mô tả (Description)',
                            'default' => 'Proxy IPv4, IPv6 tốc độ cao, bảo mật, nhiều quốc gia. Cam kết uptime 99.9%.',
                            'desc'    => 'Đoạn mô tả ngắn hiển thị dưới tiêu đề.',
                            'rows'    => 3,
                        ));

                        vieproxy_field_image(array(
                            'key'   => 'hero_image',
                            'label' => 'Ảnh minh hoạ (bên phải)',
                            'desc'  => 'Ảnh lớn hiển thị bên phải hero section. Khuyến nghị: PNG nền trong, tối thiểu 600×500px.',
                        ));
                        ?>

                    </div>
                </div>
            </div>

            <!-- TAB: Proxy List Section -->
            <div class="vp-tab-content <?php echo $active_tab === 'proxy_list' ? 'is-active' : ''; ?>" data-tab="proxy_list">
                <div class="vp-panel">
                    <div class="vp-panel__head">
                        <h2 class="vp-panel__title">Proxy List Section</h2>
                        <p class="vp-panel__subtitle">Cài đặt tiêu đề cho section danh sách proxy</p>
                    </div>
                    <div class="vp-panel__body">

                        <?php
                        vieproxy_field_text(array(
                            'key'     => 'proxy_list_title_main',
                            'label'   => 'Tiêu đề chính (màu xám)',
                            'default' => 'DANH SÁCH CÁC LOẠI PROXY',
                            'desc'    => 'Phần tiêu đề màu xám (chữ hoa)',
                        ));

                        vieproxy_field_text(array(
                            'key'     => 'proxy_list_title_highlight',
                            'label'   => 'Tiêu đề highlight (màu xanh)',
                            'default' => 'TỐT NHẤT',
                            'desc'    => 'Phần tiêu đề màu xanh nổi bật (chữ hoa)',
                        ));
                        ?>

                    </div>
                </div>
            </div>

            <!-- TAB: Why Choose Section -->
            <div class="vp-tab-content <?php echo $active_tab === 'why_choose' ? 'is-active' : ''; ?>" data-tab="why_choose">
                <div class="vp-panel">
                    <div class="vp-panel__head">
                        <h2 class="vp-panel__title">Why Choose Section</h2>
                        <p class="vp-panel__subtitle">Cài đặt nội dung section "Vì sao nên lựa chọn VieProxy"</p>
                    </div>
                    <div class="vp-panel__body">

                        <h3 style="margin: 0 0 16px; font-size: 14px; font-weight: 600; color: #1f2937;">Tiêu đề chính</h3>

                        <?php
                        vieproxy_field_text(array(
                            'key'     => 'why_choose_title_main',
                            'label'   => 'Tiêu đề chính (màu xám)',
                            'default' => 'VÌ SAO NÊN LỰA CHỌN',
                            'desc'    => 'Phần tiêu đề màu xám (chữ hoa)',
                        ));

                        vieproxy_field_text(array(
                            'key'     => 'why_choose_title_highlight',
                            'label'   => 'Tiêu đề highlight (màu xanh)',
                            'default' => 'VIEPROXY ?',
                            'desc'    => 'Phần tiêu đề màu xanh nổi bật (chữ hoa)',
                        ));

                        vieproxy_field_textarea(array(
                            'key'     => 'why_choose_description',
                            'label'   => 'Mô tả (Description)',
                            'default' => 'Cung cấp cho bạn một mức giá hợp lý tại các đối tác proxy thông dụng với hình thức thanh toán dễ dàng và tiết kiệm nhất',
                            'desc'    => 'Đoạn mô tả ngắn hiển thị dưới tiêu đề',
                            'rows'    => 3,
                        ));
                        ?>

                        <hr style="margin: 24px 0; border: none; border-top: 1px solid #e5e7eb;">

                        <h3 style="margin: 0 0 16px; font-size: 14px; font-weight: 600; color: #1f2937;">Nội dung Cards (Slider)</h3>
                        <p style="margin: 0 0 20px; font-size: 13px; color: #6c7a8d;">Thêm, sửa, xóa các card hiển thị trong slider. Kéo thả để sắp xếp lại thứ tự.</p>

                        <?php
                        // Dynamic repeater for cards
                        vieproxy_field_repeater(array(
                            'key'   => 'why_choose_cards',
                            'label' => 'Danh sách Cards',
                            'desc'  => '',
                            'fields' => array(
                                'title' => array(
                                    'type'        => 'text',
                                    'label'       => 'Tiêu đề',
                                    'placeholder' => 'Ví dụ: Dễ dàng sử dụng',
                                ),
                                'description' => array(
                                    'type'        => 'textarea',
                                    'label'       => 'Mô tả',
                                    'placeholder' => 'Nhập mô tả chi tiết...',
                                    'rows'        => 3,
                                ),
                            ),
                        ));
                        ?>

                    </div>
                </div>
            </div>

            <!-- TAB: Commitment Section -->
            <div class="vp-tab-content <?php echo $active_tab === 'commitment' ? 'is-active' : ''; ?>" data-tab="commitment">
                <div class="vp-panel">
                    <div class="vp-panel__head">
                        <h2 class="vp-panel__title">Commitment Section</h2>
                        <p class="vp-panel__subtitle">Cài đặt nội dung section "Cam kết giá tốt nhất thị trường"</p>
                    </div>
                    <div class="vp-panel__body">

                        <h3 style="margin: 0 0 16px; font-size: 14px; font-weight: 600; color: #1f2937;">Tiêu đề</h3>

                        <?php
                        vieproxy_field_text(array(
                            'key'     => 'commitment_title_main',
                            'label'   => 'Dòng tiêu đề 1 (màu trắng đậm)',
                            'default' => 'CAM KẾT GIÁ',
                            'desc'    => 'Dòng đầu tiêu đề, hiển thị chữ trắng đậm',
                        ));

                        vieproxy_field_text(array(
                            'key'     => 'commitment_title_highlight',
                            'label'   => 'Dòng tiêu đề 2 (màu trắng lớn)',
                            'default' => 'TỐT NHẤT THỊ TRƯỜNG',
                            'desc'    => 'Dòng thứ hai, chữ lớn hơn màu trắng',
                        ));

                        vieproxy_field_textarea(array(
                            'key'     => 'commitment_description',
                            'label'   => 'Mô tả',
                            'default' => 'Cung cấp cho bạn một mức giá hợp lý tại các đối tác proxy thông dụng với hình thức thanh toán dễ dàng và tiết kiệm nhất',
                            'desc'    => 'Đoạn mô tả ngắn hiển thị dưới tiêu đề',
                            'rows'    => 3,
                        ));

                        vieproxy_field_image(array(
                            'key'   => 'commitment_image',
                            'label' => 'Ảnh nhân vật (bên phải)',
                            'desc'  => 'Ảnh PNG nền trong hiển thị bên phải section. Khuyến nghị: tối thiểu 400×500px.',
                        ));
                        ?>

                        <hr style="margin: 24px 0; border: none; border-top: 1px solid #e5e7eb;">

                        <h3 style="margin: 0 0 16px; font-size: 14px; font-weight: 600; color: #1f2937;">Danh sách tính năng</h3>
                        <p style="margin: 0 0 20px; font-size: 13px; color: #6c7a8d;">Thêm, sửa, xóa các dòng tính năng hiển thị bên trái. Kéo thả để sắp xếp lại.</p>

                        <?php
                        vieproxy_field_repeater(array(
                            'key'    => 'commitment_features',
                            'label'  => 'Danh sách tính năng',
                            'desc'   => '',
                            'fields' => array(
                                'title' => array(
                                    'type'        => 'text',
                                    'label'       => 'Tiêu đề tính năng',
                                    'placeholder' => 'Ví dụ: Băng thông tốc độ cao',
                                ),
                                'description' => array(
                                    'type'        => 'textarea',
                                    'label'       => 'Mô tả',
                                    'placeholder' => 'Nhập mô tả chi tiết...',
                                    'rows'        => 2,
                                ),
                                'icon' => array(
                                    'type'        => 'icon_html',
                                    'label'       => 'Icon (thẻ HTML)',
                                    'placeholder' => '<i class="fa-solid fa-gauge-high"></i>',
                                ),
                            ),
                        ));
                        ?>

                    </div>
                </div>
            </div>

            <!-- TAB: Pricing Section -->
            <div class="vp-tab-content <?php echo $active_tab === 'pricing' ? 'is-active' : ''; ?>" data-tab="pricing">
                <div class="vp-panel">
                    <div class="vp-panel__head">
                        <h2 class="vp-panel__title">Pricing Section</h2>
                        <p class="vp-panel__subtitle">Cài đặt nội dung section "Thuê proxy giá ưu đãi nhất"</p>
                    </div>
                    <div class="vp-panel__body">

                        <h3 style="margin: 0 0 16px; font-size: 14px; font-weight: 600; color: #1f2937;">Tiêu đề chính</h3>

                        <?php
                        vieproxy_field_text(array(
                            'key'     => 'pricing_title_main',
                            'label'   => 'Tiêu đề chính (màu xám)',
                            'default' => 'THUÊ PROXY GIÁ ƯU ĐÃI NHẤT TẠI',
                            'desc'    => 'Phần tiêu đề màu xám (chữ hoa)',
                        ));

                        vieproxy_field_text(array(
                            'key'     => 'pricing_title_highlight',
                            'label'   => 'Tiêu đề highlight (màu xanh)',
                            'default' => 'VIEPROXY',
                            'desc'    => 'Phần tiêu đề màu xanh nổi bật (chữ hoa)',
                        ));

                        vieproxy_field_textarea(array(
                            'key'     => 'pricing_description',
                            'label'   => 'Mô tả (Description)',
                            'default' => 'Cung cấp cho bạn một mức giá hợp lý tại các đối tác proxy thông dụng với hình thức thanh toán dễ dàng và tiết kiệm nhất',
                            'desc'    => 'Đoạn mô tả ngắn hiển thị dưới tiêu đề',
                            'rows'    => 3,
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <!-- TAB: Coverage Section -->
            <div class="vp-tab-content <?php echo $active_tab === 'coverage' ? 'is-active' : ''; ?>" data-tab="coverage">
                <div class="vp-panel">
                    <div class="vp-panel__head">
                        <h2 class="vp-panel__title">Coverage Section</h2>
                        <p class="vp-panel__subtitle">Cài đặt nội dung section bản đồ phủ sóng IP proxy toàn cầu</p>
                    </div>
                    <div class="vp-panel__body">

                        <h3 style="margin: 0 0 16px; font-size: 14px; font-weight: 600; color: #1f2937;">Tiêu đề</h3>

                        <?php
                        vieproxy_field_text(array(
                            'key'     => 'coverage_title_main',
                            'label'   => 'Dòng tiêu đề 1 (màu xám đậm)',
                            'default' => 'IP PROXY DÂN CƯ TỪ KHẮP NƠI',
                            'desc'    => 'Dòng đầu tiêu đề, hiển thị chữ xám đậm',
                        ));

                        vieproxy_field_text(array(
                            'key'     => 'coverage_title_highlight',
                            'label'   => 'Dòng tiêu đề 2 (màu xanh highlight)',
                            'default' => 'TRÊN THẾ GIỚI',
                            'desc'    => 'Dòng thứ hai, chữ lớn hơn màu xanh nổi bật',
                        ));

                        vieproxy_field_textarea(array(
                            'key'     => 'coverage_description',
                            'label'   => 'Mô tả (Description)',
                            'default' => 'VieProxy cung cấp đại IP proxy đa dạng, phủ sóng hơn 200 quốc gia và khu vực trên toàn cầu, nhằm hỗ trợ ẩn danh an toàn một cách tối ưu.',
                            'desc'    => 'Đoạn mô tả ngắn hiển thị dưới tiêu đề',
                            'rows'    => 3,
                        ));
                        ?>

                    </div>
                </div>
            </div>

            <!-- TAB: Rating Section -->
            <div class="vp-tab-content <?php echo $active_tab === 'rating' ? 'is-active' : ''; ?>" data-tab="rating">
                <div class="vp-panel">
                    <div class="vp-panel__head">
                        <h2 class="vp-panel__title">Rating Section</h2>
                        <p class="vp-panel__subtitle">Cài đặt nội dung section "Đánh giá từ khách hàng"</p>
                    </div>
                    <div class="vp-panel__body">

                        <h3 style="margin: 0 0 16px; font-size: 14px; font-weight: 600; color: #1f2937;">Tiêu đề</h3>

                        <?php
                        vieproxy_field_text(array(
                            'key'     => 'rating_title_main',
                            'label'   => 'Dòng tiêu đề 1 (màu trắng đậm)',
                            'default' => 'ĐÁNH GIÁ TỪ',
                            'desc'    => 'Dòng đầu tiêu đề, hiển thị chữ trắng đậm',
                        ));

                        vieproxy_field_text(array(
                            'key'     => 'rating_title_highlight',
                            'label'   => 'Dòng tiêu đề 2 (màu trắng lớn)',
                            'default' => 'KHÁCH HÀNG',
                            'desc'    => 'Dòng thứ hai, chữ lớn hơn màu trắng',
                        ));

                        vieproxy_field_textarea(array(
                            'key'     => 'rating_description',
                            'label'   => 'Mô tả',
                            'default' => 'Dưới đây các bạn tạm một mức giá hợp lý tại các đối tác proxy thông dụng với hình thức thanh toán dễ dàng và tiết kiệm nhất',
                            'desc'    => 'Đoạn mô tả ngắn hiển thị dưới tiêu đề',
                            'rows'    => 3,
                        ));
                        ?>

                        <hr style="margin: 24px 0; border: none; border-top: 1px solid #e5e7eb;">

                        <h3 style="margin: 0 0 16px; font-size: 14px; font-weight: 600; color: #1f2937;">Danh sách đánh giá</h3>
                        <p style="margin: 0 0 20px; font-size: 13px; color: #6c7a8d;">Thêm, sửa, xóa các card đánh giá từ khách hàng. Kéo thả để sắp xếp lại.</p>

                        <?php
                        vieproxy_field_repeater(array(
                            'key'    => 'rating_cards',
                            'label'  => 'Danh sách đánh giá',
                            'desc'   => '',
                            'fields' => array(
                                'avatar' => array(
                                    'type'  => 'image',
                                    'label' => 'Ảnh đại diện (Avatar)',
                                ),
                                'name' => array(
                                    'type'        => 'text',
                                    'label'       => 'Tên khách hàng',
                                    'placeholder' => 'Ví dụ: Sarah Johnson',
                                ),
                                'position' => array(
                                    'type'        => 'text',
                                    'label'       => 'Chức vụ/Vị trí',
                                    'placeholder' => 'Ví dụ: CEO at TechCorp',
                                ),
                                'comment' => array(
                                    'type'        => 'textarea',
                                    'label'       => 'Nội dung đánh giá',
                                    'placeholder' => 'Nhập nội dung đánh giá từ khách hàng...',
                                    'rows'        => 4,
                                ),
                            ),
                        ));
                        ?>

                    </div>
                </div>
            </div>

            <!-- TAB: Partners Section -->
            <div class="vp-tab-content <?php echo $active_tab === 'partners' ? 'is-active' : ''; ?>" data-tab="partners">
                <div class="vp-panel">
                    <div class="vp-panel__head">
                        <h2 class="vp-panel__title">Partners Section</h2>
                        <p class="vp-panel__subtitle">Cài đặt nội dung section "Đối tác của VieProxy"</p>
                    </div>
                    <div class="vp-panel__body">

                        <h3 style="margin: 0 0 16px; font-size: 14px; font-weight: 600; color: #1f2937;">Tiêu đề</h3>

                        <?php
                        vieproxy_field_text(array(
                            'key'     => 'partners_title_main',
                            'label'   => 'Tiêu đề chính (màu xám)',
                            'default' => 'ĐỐI TÁC CỦA',
                            'desc'    => 'Phần tiêu đề màu xám (chữ hoa)',
                        ));

                        vieproxy_field_text(array(
                            'key'     => 'partners_title_highlight',
                            'label'   => 'Tiêu đề highlight (màu xanh)',
                            'default' => 'VIEPROXY',
                            'desc'    => 'Phần tiêu đề màu xanh nổi bật (chữ hoa)',
                        ));

                        vieproxy_field_textarea(array(
                            'key'     => 'partners_description',
                            'label'   => 'Mô tả (Description)',
                            'default' => 'TradeProxy cung cấp đại IP proxy đa dạng, phủ sóng hơn 200 quốc gia và khu vực trên toàn cầu, nhằm hỗ trợ ẩn danh an toàn một cách tối ưu.',
                            'desc'    => 'Đoạn mô tả ngắn hiển thị dưới tiêu đề',
                            'rows'    => 3,
                        ));
                        ?>

                        <hr style="margin: 24px 0; border: none; border-top: 1px solid #e5e7eb;">

                        <h3 style="margin: 0 0 8px; font-size: 14px; font-weight: 600; color: #1f2937;">Logo đối tác</h3>
                        <p style="margin: 0 0 20px; font-size: 13px; color: #6c7a8d;">Thêm logo các đối tác. Các logo sẽ được hiển thị thành 2 hàng tự động chạy (hàng trên: trái → phải, hàng dưới: phải → trái). Mỗi logo nên có kích thước tối thiểu 120×60px, nền trong suốt (PNG).</p>

                        <?php
                        // Partners logos - repeater field với logo, tên, và URL
                        vieproxy_field_repeater(array(
                            'key'    => 'partners_logos',
                            'label'  => 'Danh sách logo đối tác',
                            'desc'   => '',
                            'fields' => array(
                                'logo' => array(
                                    'type'  => 'image',
                                    'label' => 'Logo đối tác',
                                ),
                                'name' => array(
                                    'type'        => 'text',
                                    'label'       => 'Tên đối tác',
                                    'placeholder' => 'Ví dụ: MeetLogin, OwnPlus, GenZData Agency...',
                                ),
                                'url' => array(
                                    'type'        => 'text',
                                    'label'       => 'URL/Link (tùy chọn)',
                                    'placeholder' => 'https://example.com hoặc để trống nếu không cần link',
                                ),
                            ),
                        ));
                        ?>

                    </div>
                </div>
            </div>

            <!-- TAB: FAQ Section -->
            <div class="vp-tab-content <?php echo $active_tab === 'faq' ? 'is-active' : ''; ?>" data-tab="faq">
                <div class="vp-panel">
                    <div class="vp-panel__head">
                        <h2 class="vp-panel__title">FAQ Section</h2>
                        <p class="vp-panel__subtitle">Cài đặt nội dung section "Câu hỏi thường gặp"</p>
                    </div>
                    <div class="vp-panel__body">

                        <h3 style="margin: 0 0 16px; font-size: 14px; font-weight: 600; color: #1f2937;">Tiêu đề</h3>

                        <?php
                        vieproxy_field_text(array(
                            'key'     => 'faq_title_main',
                            'label'   => 'Tiêu đề chính (màu xám)',
                            'default' => 'CÂU HỎI',
                            'desc'    => 'Phần tiêu đề màu xám (chữ hoa)',
                        ));

                        vieproxy_field_text(array(
                            'key'     => 'faq_title_highlight',
                            'label'   => 'Tiêu đề highlight (màu xanh lớn)',
                            'default' => 'THƯỜNG GẶP',
                            'desc'    => 'Phần tiêu đề lớn màu xanh nổi bật (chữ hoa)',
                        ));

                        vieproxy_field_textarea(array(
                            'key'     => 'faq_description',
                            'label'   => 'Mô tả (Description)',
                            'default' => 'Bạn đang có thắc mắc về các dịch vụ? Trade Proxy sẽ giúp bạn giải đáp nhanh chóng các câu hỏi được quan tâm nhiều nhất.',
                            'desc'    => 'Đoạn mô tả ngắn hiển thị dưới tiêu đề',
                            'rows'    => 3,
                        ));
                        ?>

                        <hr style="margin: 24px 0; border: none; border-top: 1px solid #e5e7eb;">

                        <h3 style="margin: 0 0 8px; font-size: 14px; font-weight: 600; color: #1f2937;">Danh sách câu hỏi</h3>
                        <p style="margin: 0 0 20px; font-size: 13px; color: #6c7a8d;">Thêm, sửa, xóa các câu hỏi thường gặp. Câu hỏi đầu tiên sẽ tự động mở khi tải trang, các câu hỏi khác sẽ đóng.</p>

                        <?php
                        vieproxy_field_repeater(array(
                            'key'    => 'faq_items',
                            'label'  => 'Danh sách FAQ',
                            'desc'   => '',
                            'fields' => array(
                                'question' => array(
                                    'type'        => 'text',
                                    'label'       => 'Câu hỏi',
                                    'placeholder' => 'Ví dụ: Proxy là gì?',
                                ),
                                'answer' => array(
                                    'type'        => 'textarea',
                                    'label'       => 'Câu trả lời',
                                    'placeholder' => 'Nhập câu trả lời chi tiết...',
                                    'rows'        => 4,
                                ),
                            ),
                        ));
                        ?>

                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="vp-save-bar">
                <button type="submit" class="vp-btn-save" id="vp-save-btn">
                    <span class="dashicons dashicons-yes-alt"></span>
                    Lưu cài đặt
                </button>
                <span class="vp-save-notice" id="vp-save-notice"></span>
            </div>

        </form>
    </div><!-- /.vp-admin-wrap -->
<?php
}
