<?php
/**
 * Product order form template
 *
 * This file provides the markup for the product order form embedded on product pages
 *
 * @link       https://example.com
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/public/partials
 */

// Direct access protection
defined('WPINC') or die;

// Get Algerian cities for the dropdown
$cities = Hajri_Cod_Shop_Shipping::get_algerian_cities();

// Get settings for recaptcha
$settings = get_option('hajri_cod_shop_settings', array());
$recaptcha_enabled = isset($settings['recaptcha_enabled']) && $settings['recaptcha_enabled'] && !empty($settings['recaptcha_site_key']);
?>

<div class="hajri-product-form-wrapper" id="hajri-product-form-<?php echo esc_attr($product['id']); ?>" data-product-id="<?php echo esc_attr($product['id']); ?>">
    <h3><?php echo esc_html__('Order Now', 'hajri-cod-shop'); ?></h3>
    
    <?php if ($atts['show_image'] === 'yes' && !empty($product['image'])) : ?>
        <div class="hajri-form-product-image">
            <img src="<?php echo esc_url($product['image']); ?>" alt="<?php echo esc_attr($product['title']); ?>">
        </div>
    <?php endif; ?>
    
    <div class="hajri-form-product-info">
        <h4><?php echo esc_html($product['title']); ?></h4>
        
        <?php if ($atts['show_description'] === 'yes' && !empty($product['excerpt'])) : ?>
            <div class="hajri-form-product-excerpt"><?php echo wp_kses_post($product['excerpt']); ?></div>
        <?php endif; ?>
        
        <?php if ($atts['show_price'] === 'yes') : ?>
            <div class="hajri-form-product-price">
                <?php if (!empty($product['sale_price'])) : ?>
                    <span class="hajri-regular-price"><?php echo esc_html(number_format($product['price'], 2)); ?> <?php echo esc_html__('DZD', 'hajri-cod-shop'); ?></span>
                    <span class="hajri-sale-price"><?php echo esc_html(number_format($product['sale_price'], 2)); ?> <?php echo esc_html__('DZD', 'hajri-cod-shop'); ?></span>
                <?php else : ?>
                    <span class="hajri-price"><?php echo esc_html(number_format($product['price'], 2)); ?> <?php echo esc_html__('DZD', 'hajri-cod-shop'); ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <form class="hajri-order-form" id="hajri-order-form-<?php echo esc_attr($product['id']); ?>">
        <?php wp_nonce_field('hajri_order_nonce', 'hajri_order_security'); ?>
        <input type="hidden" name="product_id" value="<?php echo esc_attr($product['id']); ?>">
        
        <div class="hajri-form-row">
            <div class="hajri-form-group">
                <label for="hajri-quantity-<?php echo esc_attr($product['id']); ?>"><?php echo esc_html__('Quantity', 'hajri-cod-shop'); ?></label>
                <input type="number" id="hajri-quantity-<?php echo esc_attr($product['id']); ?>" name="quantity" min="1" value="1" class="hajri-quantity-input" data-product-id="<?php echo esc_attr($product['id']); ?>">
            </div>
        </div>
        
        <div class="hajri-form-row">
            <div class="hajri-form-group">
                <label for="hajri-name-<?php echo esc_attr($product['id']); ?>"><?php echo esc_html__('Full Name', 'hajri-cod-shop'); ?> <span class="required">*</span></label>
                <input type="text" id="hajri-name-<?php echo esc_attr($product['id']); ?>" name="name" required class="hajri-input">
            </div>
        </div>
        
        <div class="hajri-form-row">
            <div class="hajri-form-group">
                <label for="hajri-phone-<?php echo esc_attr($product['id']); ?>"><?php echo esc_html__('Phone Number', 'hajri-cod-shop'); ?> <span class="required">*</span></label>
                <input type="tel" id="hajri-phone-<?php echo esc_attr($product['id']); ?>" name="phone" required class="hajri-phone-input" placeholder="05XXXXXXXX" pattern="(05|06|07)[0-9]{8}">
                <span class="hajri-form-help"><?php echo esc_html__('Algerian numbers only (starting with 05, 06, or 07)', 'hajri-cod-shop'); ?></span>
            </div>
        </div>
        
        <div class="hajri-form-row">
            <div class="hajri-form-group">
                <label for="hajri-city-<?php echo esc_attr($product['id']); ?>"><?php echo esc_html__('City', 'hajri-cod-shop'); ?> <span class="required">*</span></label>
                <select id="hajri-city-<?php echo esc_attr($product['id']); ?>" name="city" required class="hajri-city-select" data-product-id="<?php echo esc_attr($product['id']); ?>">
                    <option value=""><?php echo esc_html__('-- Select City --', 'hajri-cod-shop'); ?></option>
                    <?php foreach ($cities as $city) : ?>
                        <option value="<?php echo esc_attr($city); ?>"><?php echo esc_html($city); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="hajri-form-row">
            <div class="hajri-form-group">
                <label for="hajri-address-<?php echo esc_attr($product['id']); ?>"><?php echo esc_html__('Address', 'hajri-cod-shop'); ?> <span class="required">*</span></label>
                <textarea id="hajri-address-<?php echo esc_attr($product['id']); ?>" name="address" required class="hajri-textarea"></textarea>
            </div>
        </div>
        
        <div class="hajri-form-row">
            <div class="hajri-form-group">
                <label for="hajri-notes-<?php echo esc_attr($product['id']); ?>"><?php echo esc_html__('Notes (Optional)', 'hajri-cod-shop'); ?></label>
                <textarea id="hajri-notes-<?php echo esc_attr($product['id']); ?>" name="notes" class="hajri-textarea"></textarea>
            </div>
        </div>
        
        <div class="hajri-order-summary">
            <h4><?php echo esc_html__('Order Summary', 'hajri-cod-shop'); ?></h4>
            
            <div class="hajri-summary-row">
                <span class="hajri-summary-label"><?php echo esc_html__('Subtotal', 'hajri-cod-shop'); ?></span>
                <span class="hajri-summary-value hajri-subtotal"><?php echo esc_html(number_format($product['current_price'], 2)); ?> <?php echo esc_html__('DZD', 'hajri-cod-shop'); ?></span>
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
                <span class="hajri-summary-value hajri-total"><?php echo esc_html(number_format($product['current_price'], 2)); ?> <?php echo esc_html__('DZD', 'hajri-cod-shop'); ?></span>
            </div>
        </div>
        
        <div class="hajri-form-row hajri-terms-row">
            <div class="hajri-form-checkbox">
                <input type="checkbox" id="hajri-terms-<?php echo esc_attr($product['id']); ?>" name="terms" required>
                <label for="hajri-terms-<?php echo esc_attr($product['id']); ?>"><?php echo esc_html__('I agree to the cash on delivery terms and conditions', 'hajri-cod-shop'); ?> <span class="required">*</span></label>
            </div>
        </div>
        
        <?php if ($recaptcha_enabled) : ?>
            <input type="hidden" name="recaptcha_token" class="hajri-recaptcha-token">
        <?php endif; ?>
        
        <div class="hajri-form-row">
            <button type="submit" class="hajri-submit-btn" <?php echo !$product['in_stock'] ? 'disabled' : ''; ?>>
                <?php echo esc_html($product['in_stock'] ? $atts['button_text'] : __('Out of Stock', 'hajri-cod-shop')); ?>
            </button>
        </div>
        
        <div class="hajri-response-messages"></div>
        
        <div class="hajri-form-footer">
            <p class="hajri-cod-notice"><?php echo esc_html__('* Payment will be collected upon delivery (Cash on Delivery)', 'hajri-cod-shop'); ?></p>
        </div>
    </form>
</div>
