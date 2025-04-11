<?php
/**
 * Standalone order form template
 *
 * This file provides the markup for the standalone order form that can be embedded on landing pages
 *
 * @link       https://example.com
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/public/partials
 */

// Direct access protection
defined('WPINC') or die;

// Get settings for recaptcha
$settings = get_option('hajri_cod_shop_settings', array());
$recaptcha_enabled = isset($settings['recaptcha_enabled']) && $settings['recaptcha_enabled'] && !empty($settings['recaptcha_site_key']);
?>

<div class="hajri-standalone-form-wrapper">
    <div class="hajri-form-header">
        <h2><?php echo esc_html__('Place Your Order', 'hajri-cod-shop'); ?></h2>
        <p class="hajri-form-tagline"><?php echo esc_html__('Fill out the form below to order your products. Payment will be collected upon delivery.', 'hajri-cod-shop'); ?></p>
    </div>
    
    <div class="hajri-form-content">
        <div class="hajri-product-selector">
            <h3><?php echo esc_html__('Select Product', 'hajri-cod-shop'); ?></h3>
            <div class="hajri-products-grid">
                <?php foreach ($products as $index => $product) : ?>
                    <div class="hajri-product-select-item<?php echo ($product['id'] == $default_product_id) ? ' selected' : ''; ?>" data-product-id="<?php echo esc_attr($product['id']); ?>">
                        <?php if (!empty($product['image'])) : ?>
                            <div class="hajri-select-item-image">
                                <img src="<?php echo esc_url($product['image']); ?>" alt="<?php echo esc_attr($product['title']); ?>">
                            </div>
                        <?php endif; ?>
                        
                        <div class="hajri-select-item-details">
                            <h4><?php echo esc_html($product['title']); ?></h4>
                            
                            <div class="hajri-select-item-price">
                                <?php if (!empty($product['sale_price'])) : ?>
                                    <span class="hajri-regular-price"><?php echo esc_html(number_format($product['price'], 2)); ?> <?php echo esc_html__('DZD', 'hajri-cod-shop'); ?></span>
                                    <span class="hajri-sale-price"><?php echo esc_html(number_format($product['sale_price'], 2)); ?> <?php echo esc_html__('DZD', 'hajri-cod-shop'); ?></span>
                                <?php else : ?>
                                    <span class="hajri-price"><?php echo esc_html(number_format($product['price'], 2)); ?> <?php echo esc_html__('DZD', 'hajri-cod-shop'); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="hajri-select-item-stock">
                                <?php if ($product['in_stock']) : ?>
                                    <span class="hajri-in-stock"><?php echo esc_html__('In Stock', 'hajri-cod-shop'); ?></span>
                                <?php else : ?>
                                    <span class="hajri-out-of-stock"><?php echo esc_html__('Out of Stock', 'hajri-cod-shop'); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="hajri-select-radio">
                            <input type="radio" name="select_product" id="select-product-<?php echo esc_attr($product['id']); ?>" value="<?php echo esc_attr($product['id']); ?>" <?php checked($product['id'], $default_product_id); ?> <?php disabled(!$product['in_stock'], true); ?>>
                            <label for="select-product-<?php echo esc_attr($product['id']); ?>"><?php echo esc_html__('Select', 'hajri-cod-shop'); ?></label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <form class="hajri-order-form hajri-standalone-form" id="hajri-standalone-form">
            <?php wp_nonce_field('hajri_order_nonce', 'hajri_order_security'); ?>
            <input type="hidden" name="product_id" id="hajri-selected-product" value="<?php echo esc_attr($default_product_id); ?>">
            
            <div class="hajri-form-section">
                <h3><?php echo esc_html__('Order Details', 'hajri-cod-shop'); ?></h3>
                
                <div class="hajri-form-row">
                    <div class="hajri-form-group">
                        <label for="hajri-quantity"><?php echo esc_html__('Quantity', 'hajri-cod-shop'); ?></label>
                        <input type="number" id="hajri-quantity" name="quantity" min="1" value="1" class="hajri-quantity-input" data-product-id="0">
                    </div>
                </div>
                
                <div class="hajri-form-row hajri-two-columns">
                    <div class="hajri-form-group">
                        <label for="hajri-name"><?php echo esc_html__('Full Name', 'hajri-cod-shop'); ?> <span class="required">*</span></label>
                        <input type="text" id="hajri-name" name="name" required class="hajri-input">
                    </div>
                    
                    <div class="hajri-form-group">
                        <label for="hajri-phone"><?php echo esc_html__('Phone Number', 'hajri-cod-shop'); ?> <span class="required">*</span></label>
                        <input type="tel" id="hajri-phone" name="phone" required class="hajri-phone-input" placeholder="05XXXXXXXX" pattern="(05|06|07)[0-9]{8}">
                        <span class="hajri-form-help"><?php echo esc_html__('Algerian numbers only (starting with 05, 06, or 07)', 'hajri-cod-shop'); ?></span>
                    </div>
                </div>
                
                <div class="hajri-form-row">
                    <div class="hajri-form-group">
                        <label for="hajri-city"><?php echo esc_html__('City', 'hajri-cod-shop'); ?> <span class="required">*</span></label>
                        <select id="hajri-city" name="city" required class="hajri-city-select" data-product-id="0">
                            <option value=""><?php echo esc_html__('-- Select City --', 'hajri-cod-shop'); ?></option>
                            <?php foreach ($cities as $city) : ?>
                                <option value="<?php echo esc_attr($city); ?>"><?php echo esc_html($city); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="hajri-form-row">
                    <div class="hajri-form-group">
                        <label for="hajri-address"><?php echo esc_html__('Address', 'hajri-cod-shop'); ?> <span class="required">*</span></label>
                        <textarea id="hajri-address" name="address" required class="hajri-textarea"></textarea>
                    </div>
                </div>
                
                <div class="hajri-form-row">
                    <div class="hajri-form-group">
                        <label for="hajri-notes"><?php echo esc_html__('Notes (Optional)', 'hajri-cod-shop'); ?></label>
                        <textarea id="hajri-notes" name="notes" class="hajri-textarea"></textarea>
                    </div>
                </div>
            </div>
            
            <div class="hajri-order-summary-section">
                <h3><?php echo esc_html__('Order Summary', 'hajri-cod-shop'); ?></h3>
                
                <div class="hajri-order-summary">
                    <div class="hajri-summary-product">
                        <div class="hajri-summary-product-name">
                            <span class="hajri-selected-product-name">
                                <?php 
                                foreach ($products as $product) {
                                    if ($product['id'] == $default_product_id) {
                                        echo esc_html($product['title']);
                                        break;
                                    }
                                }
                                ?>
                            </span>
                            <span class="hajri-selected-product-quantity">x <span class="quantity-value">1</span></span>
                        </div>
                    </div>
                    
                    <div class="hajri-summary-row">
                        <span class="hajri-summary-label"><?php echo esc_html__('Subtotal', 'hajri-cod-shop'); ?></span>
                        <span class="hajri-summary-value hajri-subtotal">
                            <?php 
                            foreach ($products as $product) {
                                if ($product['id'] == $default_product_id) {
                                    echo esc_html(number_format($product['current_price'], 2));
                                    break;
                                }
                            }
                            ?> <?php echo esc_html__('DZD', 'hajri-cod-shop'); ?>
                        </span>
                    </div>
                    
                    <div class="hajri-summary-row">
                        <span class="hajri-summary-label"><?php echo esc_html__('Shipping', 'hajri-cod-shop'); ?></span>
                        <span class="hajri-summary-value hajri-shipping">-- <?php echo esc_html__('DZD', 'hajri-cod-shop'); ?></span>
                    </div>
                    
                    <div class="hajri-summary-row hajri-discount-row" style="display: none;">
                        <span class="hajri-summary-label"><?php echo esc_html__('Discount', 'hajri-cod-shop'); ?></span>
                        <span class="hajri-summary-value hajri-discount">0.00 <?php echo esc_html__('DZD', 'hajri-cod-shop'); ?></span>
                    </div>
                    
                    <div class="hajri-summary-row hajri-total-row">
                        <span class="hajri-summary-label"><?php echo esc_html__('Total', 'hajri-cod-shop'); ?></span>
                        <span class="hajri-summary-value hajri-total">
                            <?php 
                            foreach ($products as $product) {
                                if ($product['id'] == $default_product_id) {
                                    echo esc_html(number_format($product['current_price'], 2));
                                    break;
                                }
                            }
                            ?> <?php echo esc_html__('DZD', 'hajri-cod-shop'); ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="hajri-form-row hajri-terms-row">
                <div class="hajri-form-checkbox">
                    <input type="checkbox" id="hajri-terms" name="terms" required>
                    <label for="hajri-terms"><?php echo esc_html__('I agree to the cash on delivery terms and conditions', 'hajri-cod-shop'); ?> <span class="required">*</span></label>
                </div>
            </div>
            
            <?php if ($recaptcha_enabled) : ?>
                <input type="hidden" name="recaptcha_token" class="hajri-recaptcha-token">
            <?php endif; ?>
            
            <div class="hajri-form-row">
                <button type="submit" class="hajri-submit-btn">
                    <?php echo esc_html($atts['button_text']); ?>
                </button>
            </div>
            
            <div class="hajri-response-messages"></div>
            
            <div class="hajri-form-footer">
                <p class="hajri-cod-notice"><?php echo esc_html__('* Payment will be collected upon delivery (Cash on Delivery)', 'hajri-cod-shop'); ?></p>
            </div>
        </form>
    </div>
</div>
