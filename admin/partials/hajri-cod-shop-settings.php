<?php
/**
 * Provide an admin area view for plugin settings
 *
 * @link       https://example.com
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/admin/partials
 */

// Direct access protection
defined('WPINC') or die;

// Get current settings
$settings = get_option('hajri_cod_shop_settings', array());

// Set defaults if not set
$settings = wp_parse_args($settings, array(
    // General settings
    'google_sheets_enabled' => 0,
    'google_sheets_id' => '',
    'recaptcha_enabled' => 0,
    'recaptcha_site_key' => '',
    'recaptcha_secret_key' => '',
    'facebook_pixel_enabled' => 0,
    'facebook_pixel_id' => '',
    'tiktok_pixel_enabled' => 0,
    'tiktok_pixel_id' => '',
    'snapchat_pixel_enabled' => 0,
    'snapchat_pixel_id' => '',
    'google_analytics_enabled' => 0,
    'google_analytics_id' => '',
    'order_block_days' => 7,
    
    // Form settings
    'form_fields_enabled' => json_encode(array(
        'name' => true,
        'phone' => true,
        'city' => true,
        'municipality' => true,
        'address' => true,
        'notes' => true,
    )),
    'form_field_labels' => json_encode(array(
        'name' => 'الاسم الكامل',
        'phone' => 'رقم الهاتف',
        'city' => 'الولاية',
        'municipality' => 'البلدية',
        'address' => 'العنوان الكامل',
        'notes' => 'ملاحظات إضافية (اختياري)',
    )),
    'form_field_order' => json_encode(array('name', 'phone', 'city', 'municipality', 'address', 'notes')),
    'form_button_text' => 'تأكيد الطلب',
    'form_success_message' => 'تم استلام طلبك بنجاح! سنتواصل معك قريبًا لتأكيد طلبك.',
    
    // Form style settings
    'form_background_color' => '#ffffff',
    'form_text_color' => '#333333',
    'form_accent_color' => '#3498db',
    'form_button_color' => '#3498db',
    'form_button_text_color' => '#ffffff',
    'form_border_color' => '#dddddd',
    
    // Product variation settings
    'product_sizes' => json_encode(array('S', 'M', 'L', 'XL', 'XXL')),
    'product_colors' => json_encode(array(
        'أحمر' => '#ff0000',
        'أزرق' => '#0000ff',
        'أخضر' => '#00ff00',
        'أسود' => '#000000',
        'أبيض' => '#ffffff',
    )),
));

// Handle settings form submission
if (isset($_POST['hajri_save_settings']) && isset($_POST['hajri_settings_nonce'])) {
    if (wp_verify_nonce($_POST['hajri_settings_nonce'], 'hajri_save_settings')) {
        
        // General settings
        $settings['order_block_days'] = isset($_POST['order_block_days']) ? intval($_POST['order_block_days']) : 7;
        
        // Google Sheets integration
        $settings['google_sheets_enabled'] = isset($_POST['google_sheets_enabled']) ? 1 : 0;
        $settings['google_sheets_id'] = isset($_POST['google_sheets_id']) ? sanitize_text_field($_POST['google_sheets_id']) : '';
        
        // reCAPTCHA settings
        $settings['recaptcha_enabled'] = isset($_POST['recaptcha_enabled']) ? 1 : 0;
        $settings['recaptcha_site_key'] = isset($_POST['recaptcha_site_key']) ? sanitize_text_field($_POST['recaptcha_site_key']) : '';
        $settings['recaptcha_secret_key'] = isset($_POST['recaptcha_secret_key']) ? sanitize_text_field($_POST['recaptcha_secret_key']) : '';
        
        // Marketing pixels
        $settings['facebook_pixel_enabled'] = isset($_POST['facebook_pixel_enabled']) ? 1 : 0;
        $settings['facebook_pixel_id'] = isset($_POST['facebook_pixel_id']) ? sanitize_text_field($_POST['facebook_pixel_id']) : '';
        
        $settings['tiktok_pixel_enabled'] = isset($_POST['tiktok_pixel_enabled']) ? 1 : 0;
        $settings['tiktok_pixel_id'] = isset($_POST['tiktok_pixel_id']) ? sanitize_text_field($_POST['tiktok_pixel_id']) : '';
        
        $settings['snapchat_pixel_enabled'] = isset($_POST['snapchat_pixel_enabled']) ? 1 : 0;
        $settings['snapchat_pixel_id'] = isset($_POST['snapchat_pixel_id']) ? sanitize_text_field($_POST['snapchat_pixel_id']) : '';
        
        $settings['google_analytics_enabled'] = isset($_POST['google_analytics_enabled']) ? 1 : 0;
        $settings['google_analytics_id'] = isset($_POST['google_analytics_id']) ? sanitize_text_field($_POST['google_analytics_id']) : '';
        
        // Form field settings
        $form_fields_enabled = array();
        if (isset($_POST['form_fields_enabled']) && is_array($_POST['form_fields_enabled'])) {
            foreach ($_POST['form_fields_enabled'] as $field_key => $value) {
                $form_fields_enabled[$field_key] = true;
            }
        }
        $settings['form_fields_enabled'] = json_encode($form_fields_enabled);
        
        // Form field labels
        $form_field_labels = array();
        if (isset($_POST['form_field_labels']) && is_array($_POST['form_field_labels'])) {
            foreach ($_POST['form_field_labels'] as $field_key => $label) {
                $form_field_labels[$field_key] = sanitize_text_field($label);
            }
        }
        $settings['form_field_labels'] = json_encode($form_field_labels);
        
        // Form field order
        $form_field_order = array();
        if (isset($_POST['form_field_order']) && is_array($_POST['form_field_order'])) {
            foreach ($_POST['form_field_order'] as $field_key) {
                $form_field_order[] = sanitize_text_field($field_key);
            }
        }
        $settings['form_field_order'] = json_encode($form_field_order);
        
        // Form appearance settings
        $settings['form_background_color'] = isset($_POST['form_background_color']) ? sanitize_text_field($_POST['form_background_color']) : '#ffffff';
        $settings['form_text_color'] = isset($_POST['form_text_color']) ? sanitize_text_field($_POST['form_text_color']) : '#333333';
        $settings['form_accent_color'] = isset($_POST['form_accent_color']) ? sanitize_text_field($_POST['form_accent_color']) : '#3498db';
        $settings['form_button_color'] = isset($_POST['form_button_color']) ? sanitize_text_field($_POST['form_button_color']) : '#3498db';
        $settings['form_button_text_color'] = isset($_POST['form_button_text_color']) ? sanitize_text_field($_POST['form_button_text_color']) : '#ffffff';
        $settings['form_border_color'] = isset($_POST['form_border_color']) ? sanitize_text_field($_POST['form_border_color']) : '#dddddd';
        
        // Product variations
        $product_sizes = array();
        if (isset($_POST['product_sizes']) && is_array($_POST['product_sizes'])) {
            foreach ($_POST['product_sizes'] as $size) {
                if (!empty($size)) {
                    $product_sizes[] = sanitize_text_field($size);
                }
            }
        }
        $settings['product_sizes'] = json_encode($product_sizes);
        
        // Product colors
        $product_colors = array();
        if (isset($_POST['product_color_names']) && is_array($_POST['product_color_names']) &&
            isset($_POST['product_color_codes']) && is_array($_POST['product_color_codes'])) {
            
            $color_count = min(count($_POST['product_color_names']), count($_POST['product_color_codes']));
            
            for ($i = 0; $i < $color_count; $i++) {
                $color_name = sanitize_text_field($_POST['product_color_names'][$i]);
                $color_code = sanitize_text_field($_POST['product_color_codes'][$i]);
                
                if (!empty($color_name) && !empty($color_code)) {
                    $product_colors[$color_name] = $color_code;
                }
            }
        }
        $settings['product_colors'] = json_encode($product_colors);
        
        // Form text settings
        $settings['form_button_text'] = isset($_POST['form_button_text']) ? sanitize_text_field($_POST['form_button_text']) : 'تأكيد الطلب';
        $settings['form_success_message'] = isset($_POST['form_success_message']) ? sanitize_textarea_field($_POST['form_success_message']) : 'تم استلام طلبك بنجاح! سنتواصل معك قريبًا لتأكيد طلبك.';
        
        // Save settings
        update_option('hajri_cod_shop_settings', $settings);
        
        // Show success message
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Settings saved successfully.', 'hajri-cod-shop') . '</p></div>';
    }
}
?>

<div class="wrap hajri-cod-shop-admin">
    <h1><?php echo esc_html__('Settings', 'hajri-cod-shop'); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('hajri_save_settings', 'hajri_settings_nonce'); ?>
        
        <div class="hajri-settings-tabs">
            <div class="hajri-tabs-nav">
                <a href="#general" class="hajri-tab active"><?php echo esc_html__('General', 'hajri-cod-shop'); ?></a>
                <a href="#form_settings" class="hajri-tab"><?php echo esc_html__('Form Settings', 'hajri-cod-shop'); ?></a>
                <a href="#shipping" class="hajri-tab"><?php echo esc_html__('Shipping', 'hajri-cod-shop'); ?></a>
                <a href="#integrations" class="hajri-tab"><?php echo esc_html__('Integrations', 'hajri-cod-shop'); ?></a>
                <a href="#marketing" class="hajri-tab"><?php echo esc_html__('Marketing', 'hajri-cod-shop'); ?></a>
                <a href="#security" class="hajri-tab"><?php echo esc_html__('Security', 'hajri-cod-shop'); ?></a>
            </div>
            
            <div class="hajri-tabs-content">
                <!-- General Settings -->
                <div id="general" class="hajri-tab-content active">
                    <h2><?php echo esc_html__('General Settings', 'hajri-cod-shop'); ?></h2>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="order_block_days"><?php echo esc_html__('Order Block Period', 'hajri-cod-shop'); ?></label>
                            </th>
                            <td>
                                <input type="number" id="order_block_days" name="order_block_days" min="1" max="30" value="<?php echo esc_attr($settings['order_block_days']); ?>">
                                <p class="description"><?php echo esc_html__('Number of days to block repeat orders from the same phone number.', 'hajri-cod-shop'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Form Settings -->
                <div id="form_settings" class="hajri-tab-content">
                    <h2><?php echo esc_html__('Order Form Settings', 'hajri-cod-shop'); ?></h2>
                    
                    <!-- Form Fields Management -->
                    <div class="hajri-settings-section">
                        <h3><?php echo esc_html__('Form Fields', 'hajri-cod-shop'); ?></h3>
                        <p class="description"><?php echo esc_html__('Enable, disable, or customize the fields that appear in your order form.', 'hajri-cod-shop'); ?></p>
                        
                        <table class="form-table hajri-form-fields-table">
                            <thead>
                                <tr>
                                    <th><?php echo esc_html__('Enabled', 'hajri-cod-shop'); ?></th>
                                    <th><?php echo esc_html__('Field Name', 'hajri-cod-shop'); ?></th>
                                    <th><?php echo esc_html__('Label', 'hajri-cod-shop'); ?></th>
                                    <th><?php echo esc_html__('Order', 'hajri-cod-shop'); ?></th>
                                </tr>
                            </thead>
                            <tbody id="form-fields-tbody">
                                <?php
                                // Get form field settings
                                $form_fields_enabled = json_decode($settings['form_fields_enabled'], true);
                                $form_field_labels = json_decode($settings['form_field_labels'], true);
                                $form_field_order = json_decode($settings['form_field_order'], true);
                                
                                // Define the fields and their default values
                                $fields = array(
                                    'name' => array('label' => 'الاسم الكامل', 'enabled' => true),
                                    'phone' => array('label' => 'رقم الهاتف', 'enabled' => true),
                                    'city' => array('label' => 'الولاية', 'enabled' => true),
                                    'municipality' => array('label' => 'البلدية', 'enabled' => true),
                                    'address' => array('label' => 'العنوان الكامل', 'enabled' => true),
                                    'notes' => array('label' => 'ملاحظات إضافية (اختياري)', 'enabled' => true),
                                );
                                
                                $field_index = 0;
                                foreach ($form_field_order as $field_key) {
                                    if (isset($fields[$field_key])) {
                                        $is_enabled = isset($form_fields_enabled[$field_key]) ? $form_fields_enabled[$field_key] : $fields[$field_key]['enabled'];
                                        $label = isset($form_field_labels[$field_key]) ? $form_field_labels[$field_key] : $fields[$field_key]['label'];
                                        
                                        echo '<tr data-field="' . esc_attr($field_key) . '" class="hajri-form-field-row">';
                                        // Enabled checkbox
                                        echo '<td><input type="checkbox" name="form_fields_enabled[' . esc_attr($field_key) . ']" ' . checked($is_enabled, true, false) . ' value="1"></td>';
                                        // Field name
                                        echo '<td><strong>' . esc_html($field_key) . '</strong></td>';
                                        // Label
                                        echo '<td><input type="text" name="form_field_labels[' . esc_attr($field_key) . ']" value="' . esc_attr($label) . '" class="regular-text"></td>';
                                        // Order (drag handle)
                                        echo '<td><input type="hidden" name="form_field_order[]" value="' . esc_attr($field_key) . '"><span class="dashicons dashicons-menu hajri-drag-handle"></span></td>';
                                        echo '</tr>';
                                        
                                        $field_index++;
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Form Appearance -->
                    <div class="hajri-settings-section">
                        <h3><?php echo esc_html__('Form Appearance', 'hajri-cod-shop'); ?></h3>
                        <p class="description"><?php echo esc_html__('Customize the appearance of your order form.', 'hajri-cod-shop'); ?></p>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="form_background_color"><?php echo esc_html__('Form Background Color', 'hajri-cod-shop'); ?></label>
                                </th>
                                <td>
                                    <input type="color" id="form_background_color" name="form_background_color" value="<?php echo esc_attr($settings['form_background_color']); ?>">
                                    <p class="description"><?php echo esc_html__('The background color of the form.', 'hajri-cod-shop'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="form_text_color"><?php echo esc_html__('Form Text Color', 'hajri-cod-shop'); ?></label>
                                </th>
                                <td>
                                    <input type="color" id="form_text_color" name="form_text_color" value="<?php echo esc_attr($settings['form_text_color']); ?>">
                                    <p class="description"><?php echo esc_html__('The color of text in the form.', 'hajri-cod-shop'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="form_accent_color"><?php echo esc_html__('Accent Color', 'hajri-cod-shop'); ?></label>
                                </th>
                                <td>
                                    <input type="color" id="form_accent_color" name="form_accent_color" value="<?php echo esc_attr($settings['form_accent_color']); ?>">
                                    <p class="description"><?php echo esc_html__('The accent color for elements like borders, highlights, etc.', 'hajri-cod-shop'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="form_button_color"><?php echo esc_html__('Button Color', 'hajri-cod-shop'); ?></label>
                                </th>
                                <td>
                                    <input type="color" id="form_button_color" name="form_button_color" value="<?php echo esc_attr($settings['form_button_color']); ?>">
                                    <p class="description"><?php echo esc_html__('The color of the submit button.', 'hajri-cod-shop'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="form_button_text_color"><?php echo esc_html__('Button Text Color', 'hajri-cod-shop'); ?></label>
                                </th>
                                <td>
                                    <input type="color" id="form_button_text_color" name="form_button_text_color" value="<?php echo esc_attr($settings['form_button_text_color']); ?>">
                                    <p class="description"><?php echo esc_html__('The color of the text on the submit button.', 'hajri-cod-shop'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="form_border_color"><?php echo esc_html__('Border Color', 'hajri-cod-shop'); ?></label>
                                </th>
                                <td>
                                    <input type="color" id="form_border_color" name="form_border_color" value="<?php echo esc_attr($settings['form_border_color']); ?>">
                                    <p class="description"><?php echo esc_html__('The color of borders in the form.', 'hajri-cod-shop'); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Product Variations -->
                    <div class="hajri-settings-section">
                        <h3><?php echo esc_html__('Product Variations', 'hajri-cod-shop'); ?></h3>
                        <p class="description"><?php echo esc_html__('Manage the product variations available in your order form.', 'hajri-cod-shop'); ?></p>
                        
                        <!-- Product Sizes -->
                        <div class="hajri-variation-section">
                            <h4><?php echo esc_html__('Product Sizes', 'hajri-cod-shop'); ?></h4>
                            <div id="product-sizes-container">
                                <?php
                                $product_sizes = json_decode($settings['product_sizes'], true);
                                foreach ($product_sizes as $index => $size) {
                                    echo '<div class="hajri-variation-item">';
                                    echo '<input type="text" name="product_sizes[]" value="' . esc_attr($size) . '" class="regular-text">';
                                    echo '<button type="button" class="button button-secondary remove-variation">' . esc_html__('Remove', 'hajri-cod-shop') . '</button>';
                                    echo '</div>';
                                }
                                ?>
                                <div class="hajri-variation-controls">
                                    <button type="button" id="add-size-btn" class="button button-secondary"><?php echo esc_html__('Add Size', 'hajri-cod-shop'); ?></button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Product Colors -->
                        <div class="hajri-variation-section">
                            <h4><?php echo esc_html__('Product Colors', 'hajri-cod-shop'); ?></h4>
                            <div id="product-colors-container">
                                <?php
                                $product_colors = json_decode($settings['product_colors'], true);
                                foreach ($product_colors as $color_name => $color_code) {
                                    echo '<div class="hajri-variation-item hajri-color-item">';
                                    echo '<input type="text" name="product_color_names[]" value="' . esc_attr($color_name) . '" class="regular-text" placeholder="' . esc_attr__('Color Name', 'hajri-cod-shop') . '">';
                                    echo '<input type="color" name="product_color_codes[]" value="' . esc_attr($color_code) . '">';
                                    echo '<button type="button" class="button button-secondary remove-variation">' . esc_html__('Remove', 'hajri-cod-shop') . '</button>';
                                    echo '</div>';
                                }
                                ?>
                                <div class="hajri-variation-controls">
                                    <button type="button" id="add-color-btn" class="button button-secondary"><?php echo esc_html__('Add Color', 'hajri-cod-shop'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Text Settings -->
                    <div class="hajri-settings-section">
                        <h3><?php echo esc_html__('Form Text Settings', 'hajri-cod-shop'); ?></h3>
                        <p class="description"><?php echo esc_html__('Customize the text displayed in your order form.', 'hajri-cod-shop'); ?></p>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="form_button_text"><?php echo esc_html__('Submit Button Text', 'hajri-cod-shop'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="form_button_text" name="form_button_text" class="regular-text" value="<?php echo esc_attr($settings['form_button_text']); ?>">
                                    <p class="description"><?php echo esc_html__('The text displayed on the submit button.', 'hajri-cod-shop'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="form_success_message"><?php echo esc_html__('Success Message', 'hajri-cod-shop'); ?></label>
                                </th>
                                <td>
                                    <textarea id="form_success_message" name="form_success_message" class="large-text" rows="3"><?php echo esc_textarea($settings['form_success_message']); ?></textarea>
                                    <p class="description"><?php echo esc_html__('The message displayed after a successful order submission.', 'hajri-cod-shop'); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Shipping Settings -->
                <div id="shipping" class="hajri-tab-content">
                    <h2><?php echo esc_html__('Shipping Costs by City', 'hajri-cod-shop'); ?></h2>
                    
                    <div class="hajri-shipping-costs-manager">
                        <div class="hajri-loader-container">
                            <div class="hajri-loader"></div>
                        </div>
                        
                        <table class="widefat" id="shipping-costs-table">
                            <thead>
                                <tr>
                                    <th width="60%"><?php echo esc_html__('City', 'hajri-cod-shop'); ?></th>
                                    <th width="30%"><?php echo esc_html__('Shipping Cost (DZD)', 'hajri-cod-shop'); ?></th>
                                    <th width="10%"><?php echo esc_html__('Actions', 'hajri-cod-shop'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="hajri-placeholder-row">
                                    <td colspan="3"><?php echo esc_html__('Loading cities...', 'hajri-cod-shop'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="hajri-add-city-form">
                            <h3><?php echo esc_html__('Add/Update City', 'hajri-cod-shop'); ?></h3>
                            <div class="hajri-form-row">
                                <div class="hajri-form-field">
                                    <label for="new-city"><?php echo esc_html__('City Name', 'hajri-cod-shop'); ?></label>
                                    <input type="text" id="new-city" name="new_city">
                                </div>
                                
                                <div class="hajri-form-field">
                                    <label for="new-cost"><?php echo esc_html__('Shipping Cost', 'hajri-cod-shop'); ?></label>
                                    <input type="number" id="new-cost" name="new_cost" min="0" step="0.01">
                                </div>
                                
                                <div class="hajri-form-actions">
                                    <button type="button" id="add-city-btn" class="button button-secondary"><?php echo esc_html__('Add/Update City', 'hajri-cod-shop'); ?></button>
                                </div>
                            </div>
                        </div>
                        
                        <div id="city-update-message"></div>
                    </div>
                </div>
                
                <!-- Integrations -->
                <div id="integrations" class="hajri-tab-content">
                    <h2><?php echo esc_html__('Google Sheets Integration', 'hajri-cod-shop'); ?></h2>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php echo esc_html__('Enable Google Sheets', 'hajri-cod-shop'); ?></th>
                            <td>
                                <label for="google_sheets_enabled">
                                    <input type="checkbox" id="google_sheets_enabled" name="google_sheets_enabled" value="1" <?php checked(1, $settings['google_sheets_enabled']); ?>>
                                    <?php echo esc_html__('Sync orders to Google Sheets', 'hajri-cod-shop'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="google_sheets_id"><?php echo esc_html__('Google Sheet ID', 'hajri-cod-shop'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="google_sheets_id" name="google_sheets_id" class="regular-text" value="<?php echo esc_attr($settings['google_sheets_id']); ?>">
                                <p class="description"><?php echo esc_html__('The ID of your Google Sheet from the URL.', 'hajri-cod-shop'); ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <div class="hajri-setup-instructions">
                        <h3><?php echo esc_html__('Setup Instructions', 'hajri-cod-shop'); ?></h3>
                        <ol>
                            <li><?php echo esc_html__('Create a new Google Sheet.', 'hajri-cod-shop'); ?></li>
                            <li><?php echo esc_html__('Copy the Sheet ID from the URL (the long string between /d/ and /edit).', 'hajri-cod-shop'); ?></li>
                            <li><?php echo esc_html__('Paste the ID in the field above.', 'hajri-cod-shop'); ?></li>
                            <li><?php echo esc_html__('Set up a Google Apps Script to handle incoming data.', 'hajri-cod-shop'); ?></li>
                            <li><?php echo esc_html__('Enable the integration by checking the box above.', 'hajri-cod-shop'); ?></li>
                        </ol>
                        
                        <details>
                            <summary><?php echo esc_html__('Google Apps Script Code (Click to expand)', 'hajri-cod-shop'); ?></summary>
                            <pre><?php echo esc_html(Hajri_Cod_Shop_Google_Sheets::get_google_apps_script_code()); ?></pre>
                        </details>
                    </div>
                </div>
                
                <!-- Marketing Settings -->
                <div id="marketing" class="hajri-tab-content">
                    <h2><?php echo esc_html__('Marketing Pixels', 'hajri-cod-shop'); ?></h2>
                    
                    <table class="form-table">
                        <!-- Facebook Pixel -->
                        <tr>
                            <th scope="row"><?php echo esc_html__('Facebook Pixel', 'hajri-cod-shop'); ?></th>
                            <td>
                                <label for="facebook_pixel_enabled">
                                    <input type="checkbox" id="facebook_pixel_enabled" name="facebook_pixel_enabled" value="1" <?php checked(1, $settings['facebook_pixel_enabled']); ?>>
                                    <?php echo esc_html__('Enable Facebook Pixel', 'hajri-cod-shop'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="facebook_pixel_id"><?php echo esc_html__('Facebook Pixel ID', 'hajri-cod-shop'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="facebook_pixel_id" name="facebook_pixel_id" class="regular-text" value="<?php echo esc_attr($settings['facebook_pixel_id']); ?>">
                            </td>
                        </tr>
                        
                        <!-- TikTok Pixel -->
                        <tr>
                            <th scope="row"><?php echo esc_html__('TikTok Pixel', 'hajri-cod-shop'); ?></th>
                            <td>
                                <label for="tiktok_pixel_enabled">
                                    <input type="checkbox" id="tiktok_pixel_enabled" name="tiktok_pixel_enabled" value="1" <?php checked(1, $settings['tiktok_pixel_enabled']); ?>>
                                    <?php echo esc_html__('Enable TikTok Pixel', 'hajri-cod-shop'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="tiktok_pixel_id"><?php echo esc_html__('TikTok Pixel ID', 'hajri-cod-shop'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="tiktok_pixel_id" name="tiktok_pixel_id" class="regular-text" value="<?php echo esc_attr($settings['tiktok_pixel_id']); ?>">
                            </td>
                        </tr>
                        
                        <!-- Snapchat Pixel -->
                        <tr>
                            <th scope="row"><?php echo esc_html__('Snapchat Pixel', 'hajri-cod-shop'); ?></th>
                            <td>
                                <label for="snapchat_pixel_enabled">
                                    <input type="checkbox" id="snapchat_pixel_enabled" name="snapchat_pixel_enabled" value="1" <?php checked(1, $settings['snapchat_pixel_enabled']); ?>>
                                    <?php echo esc_html__('Enable Snapchat Pixel', 'hajri-cod-shop'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="snapchat_pixel_id"><?php echo esc_html__('Snapchat Pixel ID', 'hajri-cod-shop'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="snapchat_pixel_id" name="snapchat_pixel_id" class="regular-text" value="<?php echo esc_attr($settings['snapchat_pixel_id']); ?>">
                            </td>
                        </tr>
                        
                        <!-- Google Analytics -->
                        <tr>
                            <th scope="row"><?php echo esc_html__('Google Analytics', 'hajri-cod-shop'); ?></th>
                            <td>
                                <label for="google_analytics_enabled">
                                    <input type="checkbox" id="google_analytics_enabled" name="google_analytics_enabled" value="1" <?php checked(1, $settings['google_analytics_enabled']); ?>>
                                    <?php echo esc_html__('Enable Google Analytics', 'hajri-cod-shop'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="google_analytics_id"><?php echo esc_html__('Google Analytics ID', 'hajri-cod-shop'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="google_analytics_id" name="google_analytics_id" class="regular-text" value="<?php echo esc_attr($settings['google_analytics_id']); ?>">
                                <p class="description"><?php echo esc_html__('Your Google Analytics tracking ID (e.g., UA-XXXXXXXX-X or G-XXXXXXXXXX)', 'hajri-cod-shop'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Security Settings -->
                <div id="security" class="hajri-tab-content">
                    <h2><?php echo esc_html__('Security Settings', 'hajri-cod-shop'); ?></h2>
                    
                    <table class="form-table">
                        <!-- reCAPTCHA -->
                        <tr>
                            <th scope="row"><?php echo esc_html__('Google reCAPTCHA', 'hajri-cod-shop'); ?></th>
                            <td>
                                <label for="recaptcha_enabled">
                                    <input type="checkbox" id="recaptcha_enabled" name="recaptcha_enabled" value="1" <?php checked(1, $settings['recaptcha_enabled']); ?>>
                                    <?php echo esc_html__('Enable Google reCAPTCHA v3', 'hajri-cod-shop'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="recaptcha_site_key"><?php echo esc_html__('Site Key', 'hajri-cod-shop'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="recaptcha_site_key" name="recaptcha_site_key" class="regular-text" value="<?php echo esc_attr($settings['recaptcha_site_key']); ?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="recaptcha_secret_key"><?php echo esc_html__('Secret Key', 'hajri-cod-shop'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="recaptcha_secret_key" name="recaptcha_secret_key" class="regular-text" value="<?php echo esc_attr($settings['recaptcha_secret_key']); ?>">
                            </td>
                        </tr>
                    </table>
                    
                    <h2><?php echo esc_html__('IP Blocking', 'hajri-cod-shop'); ?></h2>
                    
                    <div class="hajri-ip-blocking">
                        <div class="hajri-add-ip-form">
                            <div class="hajri-form-row">
                                <div class="hajri-form-field">
                                    <label for="block-ip"><?php echo esc_html__('IP Address', 'hajri-cod-shop'); ?></label>
                                    <input type="text" id="block-ip" name="block_ip" placeholder="e.g., 192.168.1.1">
                                </div>
                                
                                <div class="hajri-form-field">
                                    <label for="block-reason"><?php echo esc_html__('Reason (optional)', 'hajri-cod-shop'); ?></label>
                                    <input type="text" id="block-reason" name="block_reason">
                                </div>
                                
                                <div class="hajri-form-actions">
                                    <button type="button" id="block-ip-btn" class="button button-secondary"><?php echo esc_html__('Block IP', 'hajri-cod-shop'); ?></button>
                                </div>
                            </div>
                        </div>
                        
                        <div id="ip-block-message"></div>
                        
                        <h3><?php echo esc_html__('Currently Blocked IPs', 'hajri-cod-shop'); ?></h3>
                        <div class="hajri-blocked-ips-container">
                            <table class="widefat" id="blocked-ips-table">
                                <thead>
                                    <tr>
                                        <th width="40%"><?php echo esc_html__('IP Address', 'hajri-cod-shop'); ?></th>
                                        <th width="45%"><?php echo esc_html__('Reason', 'hajri-cod-shop'); ?></th>
                                        <th width="15%"><?php echo esc_html__('Actions', 'hajri-cod-shop'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="hajri-placeholder-row">
                                        <td colspan="3"><?php echo esc_html__('Loading blocked IPs...', 'hajri-cod-shop'); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <p class="submit">
            <input type="submit" name="hajri_save_settings" class="button button-primary" value="<?php echo esc_attr__('Save Settings', 'hajri-cod-shop'); ?>">
        </p>
    </form>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Tab navigation
        $('.hajri-tab').on('click', function(e) {
            e.preventDefault();
            
            // Update active tab
            $('.hajri-tab').removeClass('active');
            $(this).addClass('active');
            
            // Show the corresponding content
            var target = $(this).attr('href');
            $('.hajri-tab-content').removeClass('active');
            $(target).addClass('active');
        });
        
        // Load shipping costs
        function loadShippingCosts() {
            $('.hajri-shipping-costs-manager .hajri-loader-container').show();
            
            $.ajax({
                url: hajri_admin_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_city_shipping_costs',
                    security: hajri_admin_object.nonce
                },
                success: function(response) {
                    $('.hajri-shipping-costs-manager .hajri-loader-container').hide();
                    
                    if (response.success && response.data.shipping_costs) {
                        var costs = response.data.shipping_costs;
                        var html = '';
                        
                        if (costs.length === 0) {
                            html = '<tr><td colspan="3"><?php echo esc_js(__('No shipping costs defined.', 'hajri-cod-shop')); ?></td></tr>';
                        } else {
                            $.each(costs, function(index, cost) {
                                html += '<tr>';
                                html += '<td>' + cost.city_name + '</td>';
                                html += '<td>' + parseFloat(cost.shipping_cost).toFixed(2) + '</td>';
                                html += '<td>';
                                html += '<button type="button" class="button edit-city" data-city="' + cost.city_name + '" data-cost="' + cost.shipping_cost + '">';
                                html += '<?php echo esc_js(__('Edit', 'hajri-cod-shop')); ?>';
                                html += '</button>';
                                html += '</td>';
                                html += '</tr>';
                            });
                        }
                        
                        $('#shipping-costs-table tbody').html(html);
                        
                        // Bind edit buttons
                        $('.edit-city').on('click', function() {
                            var city = $(this).data('city');
                            var cost = $(this).data('cost');
                            
                            $('#new-city').val(city);
                            $('#new-cost').val(cost);
                        });
                    } else {
                        $('#shipping-costs-table tbody').html('<tr><td colspan="3"><?php echo esc_js(__('Error loading shipping costs.', 'hajri-cod-shop')); ?></td></tr>');
                    }
                },
                error: function() {
                    $('.hajri-shipping-costs-manager .hajri-loader-container').hide();
                    $('#shipping-costs-table tbody').html('<tr><td colspan="3"><?php echo esc_js(__('Error loading shipping costs.', 'hajri-cod-shop')); ?></td></tr>');
                }
            });
        }
        
        // Add/update city shipping cost
        $('#add-city-btn').on('click', function() {
            var city = $('#new-city').val();
            var cost = $('#new-cost').val();
            
            if (!city || !cost) {
                $('#city-update-message').html('<div class="notice notice-error"><p><?php echo esc_js(__('Please enter both city name and shipping cost.', 'hajri-cod-shop')); ?></p></div>');
                return;
            }
            
            $(this).prop('disabled', true);
            $('#city-update-message').html('<div class="notice notice-info"><p>' + hajri_admin_object.strings.processing + '</p></div>');
            
            $.ajax({
                url: hajri_admin_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'update_city_shipping_costs',
                    security: hajri_admin_object.nonce,
                    city: city,
                    cost: cost
                },
                success: function(response) {
                    $('#add-city-btn').prop('disabled', false);
                    
                    if (response.success) {
                        $('#city-update-message').html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>');
                        $('#new-city').val('');
                        $('#new-cost').val('');
                        loadShippingCosts();
                    } else {
                        $('#city-update-message').html('<div class="notice notice-error"><p>' + hajri_admin_object.strings.error + ' ' + response.data.message + '</p></div>');
                    }
                },
                error: function() {
                    $('#add-city-btn').prop('disabled', false);
                    $('#city-update-message').html('<div class="notice notice-error"><p><?php echo esc_js(__('An error occurred while updating the shipping cost.', 'hajri-cod-shop')); ?></p></div>');
                }
            });
        });
        
        // Load blocked IPs
        function loadBlockedIPs() {
            $.ajax({
                url: hajri_admin_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_blocked_ips',
                    security: hajri_admin_object.nonce
                },
                success: function(response) {
                    if (response.success && response.data.blocked_ips) {
                        var ips = response.data.blocked_ips;
                        var html = '';
                        
                        if (ips.length === 0) {
                            html = '<tr><td colspan="3"><?php echo esc_js(__('No IPs are currently blocked.', 'hajri-cod-shop')); ?></td></tr>';
                        } else {
                            $.each(ips, function(index, ip) {
                                html += '<tr>';
                                html += '<td>' + ip.ip_address + '</td>';
                                html += '<td>' + (ip.reason || '<?php echo esc_js(__('No reason provided', 'hajri-cod-shop')); ?>') + '</td>';
                                html += '<td>';
                                html += '<button type="button" class="button unblock-ip" data-ip="' + ip.id + '">';
                                html += '<?php echo esc_js(__('Unblock', 'hajri-cod-shop')); ?>';
                                html += '</button>';
                                html += '</td>';
                                html += '</tr>';
                            });
                        }
                        
                        $('#blocked-ips-table tbody').html(html);
                        
                        // Bind unblock buttons
                        $('.unblock-ip').on('click', function() {
                            var ipId = $(this).data('ip');
                            unblockIP(ipId);
                        });
                    } else {
                        $('#blocked-ips-table tbody').html('<tr><td colspan="3"><?php echo esc_js(__('Error loading blocked IPs.', 'hajri-cod-shop')); ?></td></tr>');
                    }
                },
                error: function() {
                    $('#blocked-ips-table tbody').html('<tr><td colspan="3"><?php echo esc_js(__('Error loading blocked IPs.', 'hajri-cod-shop')); ?></td></tr>');
                }
            });
        }
        
        // Block IP
        $('#block-ip-btn').on('click', function() {
            var ip = $('#block-ip').val();
            var reason = $('#block-reason').val();
            
            if (!ip) {
                $('#ip-block-message').html('<div class="notice notice-error"><p><?php echo esc_js(__('Please enter an IP address.', 'hajri-cod-shop')); ?></p></div>');
                return;
            }
            
            $(this).prop('disabled', true);
            $('#ip-block-message').html('<div class="notice notice-info"><p>' + hajri_admin_object.strings.processing + '</p></div>');
            
            $.ajax({
                url: hajri_admin_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'block_ip',
                    security: hajri_admin_object.nonce,
                    ip: ip,
                    reason: reason
                },
                success: function(response) {
                    $('#block-ip-btn').prop('disabled', false);
                    
                    if (response.success) {
                        $('#ip-block-message').html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>');
                        $('#block-ip').val('');
                        $('#block-reason').val('');
                        loadBlockedIPs();
                    } else {
                        $('#ip-block-message').html('<div class="notice notice-error"><p>' + hajri_admin_object.strings.error + ' ' + response.data.message + '</p></div>');
                    }
                },
                error: function() {
                    $('#block-ip-btn').prop('disabled', false);
                    $('#ip-block-message').html('<div class="notice notice-error"><p><?php echo esc_js(__('An error occurred while blocking the IP.', 'hajri-cod-shop')); ?></p></div>');
                }
            });
        });
        
        // Unblock IP
        function unblockIP(ipId) {
            if (!confirm(hajri_admin_object.strings.confirm_delete)) {
                return;
            }
            
            $('#ip-block-message').html('<div class="notice notice-info"><p>' + hajri_admin_object.strings.processing + '</p></div>');
            
            $.ajax({
                url: hajri_admin_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'unblock_ip',
                    security: hajri_admin_object.nonce,
                    ip_id: ipId
                },
                success: function(response) {
                    if (response.success) {
                        $('#ip-block-message').html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>');
                        loadBlockedIPs();
                    } else {
                        $('#ip-block-message').html('<div class="notice notice-error"><p>' + hajri_admin_object.strings.error + ' ' + response.data.message + '</p></div>');
                    }
                },
                error: function() {
                    $('#ip-block-message').html('<div class="notice notice-error"><p><?php echo esc_js(__('An error occurred while unblocking the IP.', 'hajri-cod-shop')); ?></p></div>');
                }
            });
        }
        
        // Load data when tabs are clicked
        $('.hajri-tab[href="#shipping"]').on('click', function() {
            loadShippingCosts();
        });
        
        $('.hajri-tab[href="#security"]').on('click', function() {
            loadBlockedIPs();
        });
        
        // Load shipping costs if shipping tab is active
        if ($('.hajri-tab[href="#shipping"]').hasClass('active')) {
            loadShippingCosts();
        }
        
        // Load blocked IPs if security tab is active
        if ($('.hajri-tab[href="#security"]').hasClass('active')) {
            loadBlockedIPs();
        }
    });
</script>
