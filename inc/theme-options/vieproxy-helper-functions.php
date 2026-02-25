<?php

/**
 * VieProxy Helper Functions
 * Dùng chung cho toàn bộ theme options
 */

if (!defined('ABSPATH')) exit;

// ─────────────────────────────────────────────────────────────
// GET / SET OPTIONS
// ─────────────────────────────────────────────────────────────

/**
 * Lấy một option theo key, trả về $default nếu không có
 */
function vieproxy_get_option($key, $default = '')
{
    $options = get_option('vieproxy_theme_options', array());
    return isset($options[$key]) ? $options[$key] : $default;
}

/**
 * Lấy toàn bộ options array
 */
function vieproxy_get_all_options()
{
    return get_option('vieproxy_theme_options', array());
}

/**
 * Lấy section visibility (on/off)
 */
function vieproxy_section_visible($section_key)
{
    return (bool) vieproxy_get_option('section_' . $section_key, true);
}

// ─────────────────────────────────────────────────────────────
// RENDER ADMIN FIELD HELPERS
// ─────────────────────────────────────────────────────────────

/**
 * Render text input
 */
function vieproxy_field_text($args)
{
    $key     = $args['key'];
    $label   = $args['label'];
    $default = isset($args['default']) ? $args['default'] : '';
    $desc    = isset($args['desc'])    ? $args['desc']    : '';
    $value   = vieproxy_get_option($key, $default);
?>
    <div class="vp-field vp-field--text">
        <label class="vp-field__label"><?php echo esc_html($label); ?></label>
        <?php if ($desc): ?>
            <p class="vp-field__desc"><?php echo esc_html($desc); ?></p>
        <?php endif; ?>
        <input
            type="text"
            name="vieproxy_theme_options[<?php echo esc_attr($key); ?>]"
            value="<?php echo esc_attr($value); ?>"
            class="vp-field__input"
            placeholder="<?php echo esc_attr($default); ?>" />
    </div>
<?php
}

/**
 * Render textarea
 */
function vieproxy_field_textarea($args)
{
    $key     = $args['key'];
    $label   = $args['label'];
    $default = isset($args['default']) ? $args['default'] : '';
    $desc    = isset($args['desc'])    ? $args['desc']    : '';
    $rows    = isset($args['rows'])    ? $args['rows']    : 4;
    $value   = vieproxy_get_option($key, $default);
?>
    <div class="vp-field vp-field--textarea">
        <label class="vp-field__label"><?php echo esc_html($label); ?></label>
        <?php if ($desc): ?>
            <p class="vp-field__desc"><?php echo esc_html($desc); ?></p>
        <?php endif; ?>
        <textarea
            name="vieproxy_theme_options[<?php echo esc_attr($key); ?>]"
            class="vp-field__textarea"
            rows="<?php echo esc_attr($rows); ?>"
            placeholder="<?php echo esc_attr($default); ?>"><?php echo esc_textarea($value); ?></textarea>
    </div>
<?php
}

/**
 * Render image uploader
 */
function vieproxy_field_image($args)
{
    $key     = $args['key'];
    $label   = $args['label'];
    $desc    = isset($args['desc']) ? $args['desc'] : '';
    $value   = vieproxy_get_option($key, '');
?>
    <div class="vp-field vp-field--image">
        <label class="vp-field__label"><?php echo esc_html($label); ?></label>
        <?php if ($desc): ?>
            <p class="vp-field__desc"><?php echo esc_html($desc); ?></p>
        <?php endif; ?>

        <div class="vp-image-uploader" data-key="<?php echo esc_attr($key); ?>">
            <div class="vp-image-preview <?php echo $value ? 'has-image' : ''; ?>">
                <?php if ($value): ?>
                    <img src="<?php echo esc_url($value); ?>" alt="" />
                <?php else: ?>
                    <div class="vp-image-placeholder">
                        <i class="dashicons dashicons-format-image"></i>
                        <span>Chưa có ảnh</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="vp-image-actions">
                <button type="button" class="button vp-btn-upload-image">
                    <i class="dashicons dashicons-upload"></i>
                    <?php echo $value ? 'Thay đổi ảnh' : 'Tải lên ảnh'; ?>
                </button>
                <?php if ($value): ?>
                    <button type="button" class="button vp-btn-remove-image">
                        <i class="dashicons dashicons-trash"></i> Xoá ảnh
                    </button>
                <?php endif; ?>
            </div>

            <input
                type="hidden"
                name="vieproxy_theme_options[<?php echo esc_attr($key); ?>]"
                value="<?php echo esc_url($value); ?>"
                class="vp-image-url-input" />
        </div>
    </div>
<?php
}

/**
 * Render toggle switch
 */
function vieproxy_field_toggle($args)
{
    $key     = $args['key'];
    $label   = $args['label'];
    $desc    = isset($args['desc'])    ? $args['desc']    : '';
    $default = isset($args['default']) ? $args['default'] : 1;
    $value   = vieproxy_get_option($key, $default);
?>
    <div class="vp-field vp-field--toggle">
        <div class="vp-toggle-wrap">
            <label class="vp-toggle">
                <input
                    type="hidden"
                    name="vieproxy_theme_options[<?php echo esc_attr($key); ?>]"
                    value="0" />
                <input
                    type="checkbox"
                    name="vieproxy_theme_options[<?php echo esc_attr($key); ?>]"
                    value="1"
                    <?php checked(1, (int) $value); ?> />
                <span class="vp-toggle__slider"></span>
            </label>
            <div class="vp-toggle__info">
                <span class="vp-toggle__label"><?php echo esc_html($label); ?></span>
                <?php if ($desc): ?>
                    <span class="vp-toggle__desc"><?php echo esc_html($desc); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php
}

/**
 * Render icon HTML input (for Font Awesome icons)
 */
function vieproxy_field_icon_html($args)
{
    $key     = $args['key'];
    $label   = $args['label'];
    $default = isset($args['default']) ? $args['default'] : '';
    $desc    = isset($args['desc'])    ? $args['desc']    : '';
    $value   = vieproxy_get_option($key, $default);
?>
    <div class="vp-field vp-field--icon-html">
        <label class="vp-field__label"><?php echo esc_html($label); ?></label>
        <?php if ($desc): ?>
            <p class="vp-field__desc"><?php echo esc_html($desc); ?></p>
        <?php endif; ?>

        <div class="vp-icon-html-group">
            <input
                type="text"
                name="vieproxy_theme_options[<?php echo esc_attr($key); ?>]"
                value="<?php echo esc_attr($value); ?>"
                class="vp-field__input vp-icon-html-input"
                placeholder='<?php echo esc_attr($default ?: '<i class="fa-solid fa-gauge-high"></i>'); ?>' />

            <div class="vp-icon-html-preview">
                <?php if ($value): ?>
                    <?php echo wp_kses($value, array('i' => array('class' => array()))); ?>
                <?php else: ?>
                    <i class="fa-solid fa-image" style="color: #cbd5e0;"></i>
                <?php endif; ?>
            </div>
        </div>

        <p class="vp-field__hint" style="margin: 6px 0 0; font-size: 11.5px; color: #9ca3af;">
            Ví dụ: <code style="background: #f3f4f6; padding: 2px 6px; border-radius: 3px; font-size: 11px;">&lt;i class="fa-solid fa-gauge-high"&gt;&lt;/i&gt;</code>
        </p>
    </div>
<?php
}

/**
 * Sanitize icon HTML - allow <i> tags with class attribute
 */
function vieproxy_sanitize_icon_html($value)
{
    // Allow only <i> tags with class attribute
    $allowed_tags = array(
        'i' => array(
            'class' => array(),
            'style' => array(),
        ),
    );
    return wp_kses($value, $allowed_tags);
}

/**
 * Render repeater field for dynamic cards
 */
function vieproxy_field_repeater($args)
{
    $key     = $args['key'];
    $label   = $args['label'];
    $desc    = isset($args['desc']) ? $args['desc'] : '';
    $fields  = $args['fields']; // Array of field definitions
    $value   = vieproxy_get_option($key, array());

    // Ensure we have at least one item for initial display
    if (empty($value)) {
        $value = array(array());
    }
?>
    <div class="vp-field vp-field--repeater" data-repeater-key="<?php echo esc_attr($key); ?>">
        <div class="vp-repeater-header">
            <label class="vp-field__label"><?php echo esc_html($label); ?></label>
            <?php if ($desc): ?>
                <p class="vp-field__desc"><?php echo esc_html($desc); ?></p>
            <?php endif; ?>
        </div>

        <div class="vp-repeater-items">
            <?php foreach ($value as $index => $item): ?>
                <div class="vp-repeater-item" data-index="<?php echo $index; ?>">
                    <div class="vp-repeater-item__header">
                        <div class="vp-repeater-item__drag">
                            <span class="dashicons dashicons-menu"></span>
                        </div>
                        <span class="vp-repeater-item__title">Card <?php echo $index + 1; ?></span>
                        <button type="button" class="vp-repeater-item__remove">
                            <span class="dashicons dashicons-no-alt"></span>
                        </button>
                    </div>
                    <div class="vp-repeater-item__body">
                        <?php foreach ($fields as $field_key => $field_config): ?>
                            <?php
                            $field_name = "vieproxy_theme_options[{$key}][{$index}][{$field_key}]";
                            $field_value = isset($item[$field_key]) ? $item[$field_key] : '';
                            $field_type = $field_config['type'];
                            ?>

                            <div class="vp-repeater-field">
                                <label class="vp-repeater-field__label">
                                    <?php echo esc_html($field_config['label']); ?>
                                </label>

                                <?php if ($field_type === 'text'): ?>
                                    <input
                                        type="text"
                                        name="<?php echo esc_attr($field_name); ?>"
                                        value="<?php echo esc_attr($field_value); ?>"
                                        class="vp-field__input"
                                        placeholder="<?php echo esc_attr($field_config['placeholder'] ?? ''); ?>" />

                                <?php elseif ($field_type === 'textarea'): ?>
                                    <textarea
                                        name="<?php echo esc_attr($field_name); ?>"
                                        class="vp-field__textarea"
                                        rows="<?php echo esc_attr($field_config['rows'] ?? 3); ?>"
                                        placeholder="<?php echo esc_attr($field_config['placeholder'] ?? ''); ?>"><?php echo esc_textarea($field_value); ?></textarea>

                                <?php elseif ($field_type === 'icon_html'): ?>
                                    <div class="vp-icon-html-group">
                                        <input
                                            type="text"
                                            name="<?php echo esc_attr($field_name); ?>"
                                            value="<?php echo esc_attr($field_value); ?>"
                                            class="vp-field__input vp-icon-html-input"
                                            placeholder='<?php echo esc_attr($field_config['placeholder'] ?? '<i class="fa-solid fa-icon"></i>'); ?>' />
                                        <div class="vp-icon-html-preview">
                                            <?php if ($field_value): ?>
                                                <?php echo wp_kses($field_value, array('i' => array('class' => array()))); ?>
                                            <?php else: ?>
                                                <i class="fa-solid fa-image" style="color: #cbd5e0;"></i>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                <?php elseif ($field_type === 'image'): ?>
                                    <div class="vp-image-uploader">
                                        <div class="vp-image-preview <?php echo $field_value ? 'has-image' : ''; ?>">
                                            <?php if ($field_value): ?>
                                                <img src="<?php echo esc_url($field_value); ?>" alt="" />
                                            <?php else: ?>
                                                <div class="vp-image-placeholder">
                                                    <span class="dashicons dashicons-format-image"></span>
                                                    <span>Chưa có ảnh</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <input
                                            type="hidden"
                                            name="<?php echo esc_attr($field_name); ?>"
                                            value="<?php echo esc_url($field_value); ?>"
                                            class="vp-image-url-input" />

                                        <button type="button" class="button vp-btn-upload-image">
                                            <span class="dashicons dashicons-upload"></span>
                                            <?php echo $field_value ? 'Thay đổi ảnh' : 'Tải lên ảnh'; ?>
                                        </button>

                                        <?php if ($field_value): ?>
                                            <button type="button" class="button vp-btn-remove-image">
                                                <span class="dashicons dashicons-trash"></span>
                                                Xoá ảnh
                                            </button>
                                        <?php endif; ?>
                                    </div>

                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" class="vp-repeater-add button button-secondary">
            <span class="dashicons dashicons-plus-alt"></span>
            Thêm Card mới
        </button>

        <!-- Template for new items (hidden) -->
        <script type="text/template" id="vp-repeater-template-<?php echo esc_attr($key); ?>">
            <div class="vp-repeater-item" data-index="__INDEX__">
                <div class="vp-repeater-item__header">
                    <div class="vp-repeater-item__drag">
                        <span class="dashicons dashicons-menu"></span>
                    </div>
                    <span class="vp-repeater-item__title">Card __NUMBER__</span>
                    <button type="button" class="vp-repeater-item__remove">
                        <span class="dashicons dashicons-no-alt"></span>
                    </button>
                </div>
                <div class="vp-repeater-item__body">
                    <?php foreach ($fields as $field_key => $field_config): ?>
                        <?php
                        $field_name = "vieproxy_theme_options[{$key}][__INDEX__][{$field_key}]";
                        $field_type = $field_config['type'];
                        ?>

                        <div class="vp-repeater-field">
                            <label class="vp-repeater-field__label">
                                <?php echo esc_html($field_config['label']); ?>
                            </label>

                            <?php if ($field_type === 'text'): ?>
                                <input
                                    type="text"
                                    name="<?php echo esc_attr($field_name); ?>"
                                    value=""
                                    class="vp-field__input"
                                    placeholder="<?php echo esc_attr($field_config['placeholder'] ?? ''); ?>" />

                            <?php elseif ($field_type === 'textarea'): ?>
                                <textarea
                                    name="<?php echo esc_attr($field_name); ?>"
                                    class="vp-field__textarea"
                                    rows="<?php echo esc_attr($field_config['rows'] ?? 3); ?>"
                                    placeholder="<?php echo esc_attr($field_config['placeholder'] ?? ''); ?>"></textarea>
                            
                            <?php elseif ($field_type === 'icon_html'): ?>
                                <div class="vp-icon-html-group">
                                    <input
                                        type="text"
                                        name="<?php echo esc_attr($field_name); ?>"
                                        value=""
                                        class="vp-field__input vp-icon-html-input"
                                        placeholder='<?php echo esc_attr($field_config['placeholder'] ?? '<i class="fa-solid fa-icon"></i>'); ?>' />
                                    <div class="vp-icon-html-preview">
                                        <i class="fa-solid fa-image" style="color: #cbd5e0;"></i>
                                    </div>
                                </div>

                            <?php elseif ($field_type === 'image'): ?>
                                <div class="vp-image-uploader">
                                    <div class="vp-image-preview">
                                        <div class="vp-image-placeholder">
                                            <span class="dashicons dashicons-format-image"></span>
                                            <span>Chưa có ảnh</span>
                                        </div>
                                    </div>

                                    <input
                                        type="hidden"
                                        name="<?php echo esc_attr($field_name); ?>"
                                        value=""
                                        class="vp-image-url-input" />

                                    <button type="button" class="button vp-btn-upload-image">
                                        <span class="dashicons dashicons-upload"></span>
                                        Tải lên ảnh
                                    </button>
                                </div>

                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </script>
    </div>
<?php
}
