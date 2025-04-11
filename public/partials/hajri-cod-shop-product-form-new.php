<?php
/**
 * Enhanced Product order form template
 *
 * This template provides a comprehensive form for ordering products with COD payment
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

// Get product data
$variations = array();
if (isset($product['variations']) && is_array($product['variations'])) {
    $variations = $product['variations'];
}

// Get Algerian cities for the dropdown
$cities = Hajri_Cod_Shop_Shipping::get_algerian_cities();

// Determine if we have color or size variations
$has_colors = false;
$has_sizes = false;
$colors = array();
$sizes = array();

foreach ($variations as $variation) {
    if (!empty($variation['color']) && !in_array($variation['color'], $colors)) {
        $colors[] = $variation['color'];
        $has_colors = true;
    }
    
    if (!empty($variation['size']) && !in_array($variation['size'], $sizes)) {
        $sizes[] = $variation['size'];
        $has_sizes = true;
    }
}
?>

<div class="hajri-cod-form-container">
    <div class="hajri-cod-form-header">
        <h2><?php echo esc_html__('طلب المنتج عبر الدفع عند الاستلام', 'hajri-cod-shop'); ?></h2>
        <p class="hajri-form-tagline"><?php echo esc_html__('أكمل النموذج أدناه لطلب المنتج. سيتم التحصيل عند التسليم.', 'hajri-cod-shop'); ?></p>
    </div>
    
    <div class="hajri-cod-form-wrapper">
        <!-- Product selection and information section -->
        <div class="hajri-cod-form-product-section">
            <?php if ($atts['show_image'] === 'yes' && !empty($product['image'])) : ?>
                <div class="hajri-product-image">
                    <img src="<?php echo esc_url($product['image']); ?>" alt="<?php echo esc_attr($product['title']); ?>">
                </div>
            <?php endif; ?>
            
            <div class="hajri-product-details">
                <h3 class="hajri-product-title"><?php echo esc_html($product['title']); ?></h3>
                
                <?php if ($atts['show_description'] === 'yes' && !empty($product['excerpt'])) : ?>
                    <div class="hajri-product-description">
                        <?php echo wp_kses_post($product['excerpt']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($atts['show_price'] === 'yes') : ?>
                    <div class="hajri-product-price-display">
                        <?php if (!empty($product['sale_price'])) : ?>
                            <span class="hajri-regular-price"><?php echo esc_html(number_format($product['price'], 2)); ?> <?php echo esc_html__('دج', 'hajri-cod-shop'); ?></span>
                            <span class="hajri-sale-price"><?php echo esc_html(number_format($product['sale_price'], 2)); ?> <?php echo esc_html__('دج', 'hajri-cod-shop'); ?></span>
                        <?php else : ?>
                            <span class="hajri-price"><?php echo esc_html(number_format($product['price'], 2)); ?> <?php echo esc_html__('دج', 'hajri-cod-shop'); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!$product['in_stock']) : ?>
                    <div class="hajri-product-not-available">
                        <p class="hajri-out-of-stock-message"><?php echo esc_html__('هذا المنتج غير متوفر حاليًا', 'hajri-cod-shop'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($product['in_stock']) : ?>
            <form class="hajri-cod-order-form" id="hajri-product-order-form">
                <?php wp_nonce_field('hajri_order_nonce', 'hajri_order_security'); ?>
                <input type="hidden" name="product_id" value="<?php echo esc_attr($product['id']); ?>">
                
                <div class="hajri-form-section hajri-product-options-section">
                    <h3><?php echo esc_html__('خيارات المنتج', 'hajri-cod-shop'); ?></h3>
                    
                    <?php if ($has_colors) : ?>
                        <div class="hajri-form-row">
                            <div class="hajri-form-group">
                                <label for="hajri-color"><?php echo esc_html__('اللون', 'hajri-cod-shop'); ?></label>
                                <select id="hajri-color" name="color" class="hajri-select">
                                    <option value=""><?php echo esc_html__('-- اختر اللون --', 'hajri-cod-shop'); ?></option>
                                    <?php foreach ($colors as $color) : ?>
                                        <option value="<?php echo esc_attr($color); ?>"><?php echo esc_html($color); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($has_sizes) : ?>
                        <div class="hajri-form-row">
                            <div class="hajri-form-group">
                                <label for="hajri-size"><?php echo esc_html__('الحجم', 'hajri-cod-shop'); ?></label>
                                <select id="hajri-size" name="size" class="hajri-select">
                                    <option value=""><?php echo esc_html__('-- اختر الحجم --', 'hajri-cod-shop'); ?></option>
                                    <?php foreach ($sizes as $size) : ?>
                                        <option value="<?php echo esc_attr($size); ?>"><?php echo esc_html($size); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="hajri-form-row">
                        <div class="hajri-form-group">
                            <label for="hajri-quantity"><?php echo esc_html__('الكمية', 'hajri-cod-shop'); ?></label>
                            <div class="hajri-quantity-control">
                                <button type="button" class="hajri-qty-btn hajri-qty-minus">-</button>
                                <input type="number" id="hajri-quantity" name="quantity" min="1" value="1" class="hajri-quantity-input" data-product-id="<?php echo esc_attr($product['id']); ?>">
                                <button type="button" class="hajri-qty-btn hajri-qty-plus">+</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="hajri-form-section hajri-customer-info-section">
                    <h3><?php echo esc_html__('معلومات العميل', 'hajri-cod-shop'); ?></h3>
                    
                    <div class="hajri-form-row hajri-two-columns">
                        <div class="hajri-form-group">
                            <label for="hajri-name"><?php echo esc_html__('الاسم الكامل', 'hajri-cod-shop'); ?> <span class="required">*</span></label>
                            <input type="text" id="hajri-name" name="name" required class="hajri-input" placeholder="<?php echo esc_attr__('أدخل اسمك الكامل', 'hajri-cod-shop'); ?>">
                        </div>
                        
                        <div class="hajri-form-group">
                            <label for="hajri-phone"><?php echo esc_html__('رقم الهاتف', 'hajri-cod-shop'); ?> <span class="required">*</span></label>
                            <input type="tel" id="hajri-phone" name="phone" required class="hajri-phone-input" placeholder="05XXXXXXXX" pattern="(05|06|07)[0-9]{8}">
                            <div class="hajri-form-hint"><?php echo esc_html__('أرقام هواتف جزائرية فقط (تبدأ بـ 05، 06، أو 07)', 'hajri-cod-shop'); ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="hajri-form-section hajri-shipping-info-section">
                    <h3><?php echo esc_html__('معلومات التوصيل', 'hajri-cod-shop'); ?></h3>
                    
                    <div class="hajri-form-row hajri-two-columns">
                        <div class="hajri-form-group">
                            <label for="hajri-city"><?php echo esc_html__('الولاية', 'hajri-cod-shop'); ?> <span class="required">*</span></label>
                            <select id="hajri-city" name="city" required class="hajri-city-select hajri-select" data-product-id="<?php echo esc_attr($product['id']); ?>">
                                <option value=""><?php echo esc_html__('-- اختر الولاية --', 'hajri-cod-shop'); ?></option>
                                <?php foreach ($cities as $city) : ?>
                                    <option value="<?php echo esc_attr($city); ?>"><?php echo esc_html($city); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="hajri-form-group">
                            <label for="hajri-municipality"><?php echo esc_html__('البلدية', 'hajri-cod-shop'); ?> <span class="required">*</span></label>
                            <input type="text" id="hajri-municipality" name="municipality" required class="hajri-input" placeholder="<?php echo esc_attr__('أدخل اسم البلدية', 'hajri-cod-shop'); ?>">
                        </div>
                    </div>
                    
                    <div class="hajri-form-row">
                        <div class="hajri-form-group">
                            <label for="hajri-address"><?php echo esc_html__('العنوان الكامل', 'hajri-cod-shop'); ?> <span class="required">*</span></label>
                            <textarea id="hajri-address" name="address" required class="hajri-textarea" placeholder="<?php echo esc_attr__('أدخل عنوانك بالتفصيل (الشارع، الحي، العمارة، إلخ...)', 'hajri-cod-shop'); ?>"></textarea>
                        </div>
                    </div>
                    
                    <div class="hajri-form-row">
                        <div class="hajri-form-group">
                            <label for="hajri-notes"><?php echo esc_html__('ملاحظات إضافية (اختياري)', 'hajri-cod-shop'); ?></label>
                            <textarea id="hajri-notes" name="notes" class="hajri-textarea" placeholder="<?php echo esc_attr__('أية معلومات إضافية تريد إعلامنا بها', 'hajri-cod-shop'); ?>"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="hajri-order-summary-section">
                    <h3><?php echo esc_html__('ملخص الطلب', 'hajri-cod-shop'); ?></h3>
                    
                    <div class="hajri-order-summary">
                        <div class="hajri-summary-product">
                            <div class="hajri-summary-product-name">
                                <span class="hajri-selected-product-name"><?php echo esc_html($product['title']); ?></span>
                                <span class="hajri-selected-product-quantity">x <span class="quantity-value">1</span></span>
                            </div>
                        </div>
                        
                        <div class="hajri-summary-row">
                            <span class="hajri-summary-label"><?php echo esc_html__('سعر المنتج', 'hajri-cod-shop'); ?></span>
                            <span class="hajri-summary-value hajri-subtotal">
                                <?php echo esc_html(number_format($product['current_price'], 2)); ?> <?php echo esc_html__('دج', 'hajri-cod-shop'); ?>
                            </span>
                        </div>
                        
                        <div class="hajri-summary-row">
                            <span class="hajri-summary-label"><?php echo esc_html__('تكلفة التوصيل', 'hajri-cod-shop'); ?></span>
                            <span class="hajri-summary-value hajri-shipping">-- <?php echo esc_html__('دج', 'hajri-cod-shop'); ?></span>
                        </div>
                        
                        <div class="hajri-summary-row hajri-discount-row" style="display: none;">
                            <span class="hajri-summary-label"><?php echo esc_html__('الخصم', 'hajri-cod-shop'); ?></span>
                            <span class="hajri-summary-value hajri-discount">0.00 <?php echo esc_html__('دج', 'hajri-cod-shop'); ?></span>
                        </div>
                        
                        <div class="hajri-summary-row hajri-total-row">
                            <span class="hajri-summary-label"><?php echo esc_html__('المجموع الكلي', 'hajri-cod-shop'); ?></span>
                            <span class="hajri-summary-value hajri-total">
                                <?php echo esc_html(number_format($product['current_price'], 2)); ?> <?php echo esc_html__('دج', 'hajri-cod-shop'); ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="hajri-form-row hajri-terms-row">
                    <div class="hajri-form-checkbox">
                        <input type="checkbox" id="hajri-terms" name="terms" required>
                        <label for="hajri-terms"><?php echo esc_html__('أوافق على شروط الدفع عند الاستلام (COD)', 'hajri-cod-shop'); ?> <span class="required">*</span></label>
                    </div>
                </div>
                
                <?php if ($recaptcha_enabled) : ?>
                    <input type="hidden" name="recaptcha_token" class="hajri-recaptcha-token">
                <?php endif; ?>
                
                <div class="hajri-form-row">
                    <button type="submit" class="hajri-submit-btn">
                        <span class="hajri-btn-text"><?php echo esc_html($atts['button_text']); ?></span>
                        <span class="hajri-btn-loading" style="display: none;"><?php echo esc_html__('جاري المعالجة...', 'hajri-cod-shop'); ?></span>
                    </button>
                </div>
                
                <div class="hajri-response-messages"></div>
                
                <div class="hajri-form-footer">
                    <p class="hajri-cod-notice"><?php echo esc_html__('* سيتم الدفع عند الاستلام (الدفع نقدًا عند التسليم)', 'hajri-cod-shop'); ?></p>
                </div>
            </form>
            
            <!-- Success message template (hidden by default) -->
            <div class="hajri-success-message-template" style="display: none;">
                <div class="hajri-success-container">
                    <div class="hajri-success-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M8 12l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h3><?php echo esc_html__('تم استلام طلبك بنجاح!', 'hajri-cod-shop'); ?></h3>
                    <p><?php echo esc_html__('سنتواصل معك قريبًا لتأكيد طلبك. رقم الطلب الخاص بك هو:', 'hajri-cod-shop'); ?> <span class="hajri-order-number"></span></p>
                    <div class="hajri-success-actions">
                        <a href="#" class="hajri-new-order-btn"><?php echo esc_html__('طلب جديد', 'hajri-cod-shop'); ?></a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Form Styling */
.hajri-cod-form-container {
    max-width: 800px;
    margin: 0 auto;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    direction: rtl;
}

.hajri-cod-form-header {
    text-align: center;
    margin-bottom: 20px;
}

.hajri-cod-form-header h2 {
    font-size: 24px;
    color: #333;
    margin-bottom: 10px;
}

.hajri-form-tagline {
    color: #666;
    font-size: 16px;
}

.hajri-cod-form-wrapper {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.hajri-cod-form-product-section {
    display: flex;
    padding: 20px;
    background-color: #f9f9f9;
    border-bottom: 1px solid #eee;
}

.hajri-product-image {
    flex: 0 0 120px;
    margin-left: 20px;
}

.hajri-product-image img {
    max-width: 100%;
    height: auto;
    border-radius: 4px;
}

.hajri-product-details {
    flex: 1;
}

.hajri-product-title {
    font-size: 20px;
    margin-top: 0;
    margin-bottom: 10px;
    color: #333;
}

.hajri-product-description {
    margin-bottom: 15px;
    color: #666;
    font-size: 14px;
}

.hajri-product-price-display {
    font-weight: bold;
    font-size: 18px;
    margin-bottom: 10px;
}

.hajri-regular-price {
    text-decoration: line-through;
    color: #999;
    margin-left: 10px;
    font-size: 16px;
}

.hajri-sale-price {
    color: #e74c3c;
}

.hajri-price {
    color: #2c3e50;
}

.hajri-form-section {
    padding: 20px;
    border-bottom: 1px solid #eee;
}

.hajri-form-section h3 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #2c3e50;
    font-size: 18px;
    position: relative;
    padding-bottom: 10px;
}

.hajri-form-section h3:after {
    content: '';
    position: absolute;
    bottom: 0;
    right: 0;
    width: 50px;
    height: 3px;
    background-color: #3498db;
}

.hajri-form-row {
    margin-bottom: 15px;
}

.hajri-two-columns {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.hajri-two-columns .hajri-form-group {
    flex: 1 1 45%;
    min-width: 250px;
}

.hajri-form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #333;
}

.hajri-input,
.hajri-select,
.hajri-textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.hajri-input:focus,
.hajri-select:focus,
.hajri-textarea:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

.hajri-textarea {
    min-height: 80px;
    resize: vertical;
}

.hajri-form-hint {
    font-size: 12px;
    color: #777;
    margin-top: 5px;
}

.required {
    color: #e74c3c;
}

/* Quantity control */
.hajri-quantity-control {
    display: flex;
    align-items: center;
    max-width: 120px;
}

.hajri-qty-btn {
    width: 30px;
    height: 30px;
    background: #f5f5f5;
    border: 1px solid #ddd;
    color: #333;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    user-select: none;
}

.hajri-qty-minus {
    border-radius: 4px 0 0 4px;
}

.hajri-qty-plus {
    border-radius: 0 4px 4px 0;
}

.hajri-quantity-input {
    width: 60px;
    height: 30px;
    text-align: center;
    border: 1px solid #ddd;
    border-left: none;
    border-right: none;
    padding: 0;
    font-size: 14px;
}

.hajri-quantity-input::-webkit-inner-spin-button,
.hajri-quantity-input::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Order summary */
.hajri-order-summary-section {
    padding: 20px;
    background-color: #f9f9f9;
}

.hajri-order-summary {
    border: 1px solid #eee;
    border-radius: 4px;
    padding: 15px;
    background-color: #fff;
}

.hajri-summary-product {
    padding-bottom: 10px;
    margin-bottom: 10px;
    border-bottom: 1px dashed #eee;
}

.hajri-summary-product-name {
    display: flex;
    justify-content: space-between;
    font-weight: 600;
}

.hajri-summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 14px;
}

.hajri-total-row {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #eee;
    font-weight: bold;
    font-size: 16px;
}

/* Checkbox styling */
.hajri-form-checkbox {
    display: flex;
    align-items: flex-start;
}

.hajri-form-checkbox input[type="checkbox"] {
    margin-left: 10px;
    margin-top: 3px;
}

/* Submit button */
.hajri-submit-btn {
    width: 100%;
    padding: 12px 20px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
    position: relative;
    overflow: hidden;
}

.hajri-submit-btn:hover {
    background-color: #2980b9;
}

.hajri-form-footer {
    padding: 15px 20px;
    font-size: 12px;
    color: #666;
    text-align: center;
    background-color: #f9f9f9;
}

/* Response messages */
.hajri-response-messages {
    margin-top: 15px;
    text-align: center;
}

.hajri-error-message {
    color: #e74c3c;
    margin-top: 5px;
    font-size: 12px;
}

.invalid {
    border-color: #e74c3c;
}

/* Success message */
.hajri-success-container {
    text-align: center;
    padding: 30px 20px;
}

.hajri-success-icon {
    color: #2ecc71;
    margin-bottom: 20px;
}

.hajri-success-container h3 {
    font-size: 22px;
    margin-bottom: 15px;
    color: #333;
}

.hajri-success-container p {
    font-size: 16px;
    color: #666;
    margin-bottom: 20px;
}

.hajri-order-number {
    font-weight: bold;
    color: #3498db;
}

.hajri-success-actions {
    margin-top: 20px;
}

.hajri-new-order-btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: #3498db;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-weight: 600;
    transition: background-color 0.3s ease;
}

.hajri-new-order-btn:hover {
    background-color: #2980b9;
}

/* Mobile responsive */
@media (max-width: 767px) {
    .hajri-cod-form-product-section {
        flex-direction: column;
    }
    
    .hajri-product-image {
        margin-left: 0;
        margin-bottom: 15px;
        text-align: center;
    }
    
    .hajri-product-image img {
        max-width: 150px;
    }
    
    .hajri-two-columns {
        flex-direction: column;
    }
    
    .hajri-two-columns .hajri-form-group {
        width: 100%;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Quantity control buttons
    $('.hajri-qty-minus').on('click', function() {
        var input = $(this).next('.hajri-quantity-input');
        var value = parseInt(input.val());
        if (value > 1) {
            input.val(value - 1).trigger('change');
        }
    });
    
    $('.hajri-qty-plus').on('click', function() {
        var input = $(this).prev('.hajri-quantity-input');
        var value = parseInt(input.val());
        input.val(value + 1).trigger('change');
    });
    
    // Handle form submission
    $('#hajri-product-order-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var submitBtn = form.find('.hajri-submit-btn');
        var responseDiv = form.find('.hajri-response-messages');
        
        // Validate the form
        if (!validateForm(form)) {
            return false;
        }
        
        // Show loading state
        submitBtn.find('.hajri-btn-text').hide();
        submitBtn.find('.hajri-btn-loading').show();
        submitBtn.prop('disabled', true);
        
        // Process reCAPTCHA if enabled
        if (typeof hajri_shop !== 'undefined' && hajri_shop.recaptcha && hajri_shop.recaptcha.enabled) {
            grecaptcha.ready(function() {
                grecaptcha.execute(hajri_shop.recaptcha.site_key, {action: 'submit_order'}).then(function(token) {
                    form.find('.hajri-recaptcha-token').val(token);
                    submitOrder(form, submitBtn, responseDiv);
                });
            });
        } else {
            submitOrder(form, submitBtn, responseDiv);
        }
    });
    
    // Form validation
    function validateForm(form) {
        var isValid = true;
        var errorHtml = '';
        
        // Clear previous error messages
        form.find('.hajri-error-message').remove();
        
        // Validate required fields
        form.find('[required]').each(function() {
            var field = $(this);
            
            if (!field.val()) {
                isValid = false;
                field.addClass('invalid');
                
                // Add error message
                var fieldName = field.prev('label').text().replace('*', '').trim();
                field.after('<div class="hajri-error-message">' + fieldName + ' مطلوب</div>');
            } else {
                field.removeClass('invalid');
            }
        });
        
        // Validate phone number
        var phoneInput = form.find('.hajri-phone-input');
        var phoneValue = phoneInput.val().replace(/\s+/g, '');
        
        if (phoneValue && !/^(05|06|07)[0-9]{8}$/.test(phoneValue)) {
            isValid = false;
            phoneInput.addClass('invalid');
            
            // Check if error message already exists
            if (phoneInput.next('.hajri-error-message').length === 0) {
                phoneInput.after('<div class="hajri-error-message">يرجى إدخال رقم هاتف جزائري صالح</div>');
            }
        }
        
        // Show validation summary if there are errors
        if (!isValid) {
            form.find('.hajri-response-messages').html('<div class="hajri-error-message">يرجى تصحيح الأخطاء في النموذج قبل الإرسال.</div>');
            
            // Scroll to first error
            $('html, body').animate({
                scrollTop: form.find('.invalid:first').offset().top - 100
            }, 500);
        }
        
        return isValid;
    }
    
    // Submit the order
    function submitOrder(form, submitBtn, responseDiv) {
        // Collect form data
        var formData = new FormData(form[0]);
        formData.append('action', 'submit_order');
        formData.append('security', hajri_shop.order_nonce);
        
        // Add municipality to address for compatibility
        var municipality = formData.get('municipality');
        var address = formData.get('address');
        formData.set('address', 'البلدية: ' + municipality + ' - ' + address);
        
        $.ajax({
            url: hajri_shop.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Reset button state
                submitBtn.find('.hajri-btn-loading').hide();
                submitBtn.find('.hajri-btn-text').show();
                submitBtn.prop('disabled', false);
                
                if (response.success) {
                    // Show success message
                    form.hide();
                    
                    // Clone the success template and show it
                    var successMessage = $('.hajri-success-message-template').clone();
                    successMessage.find('.hajri-order-number').text(response.data.order_id);
                    successMessage.css('display', 'block');
                    
                    form.after(successMessage);
                    
                    // Track conversion if available
                    if (typeof response.data.order_id !== 'undefined') {
                        trackOrderConversion(response.data.order_id, form);
                    }
                    
                    // Reset form
                    form[0].reset();
                    
                    // Scroll to success message
                    $('html, body').animate({
                        scrollTop: successMessage.offset().top - 50
                    }, 500);
                    
                    // Handle new order button
                    $('.hajri-new-order-btn').on('click', function(e) {
                        e.preventDefault();
                        successMessage.remove();
                        form.show();
                        form[0].reset();
                    });
                } else {
                    // Show error message
                    responseDiv.html('<div class="hajri-error-message">' + response.data.message + '</div>');
                    
                    // Scroll to error message
                    $('html, body').animate({
                        scrollTop: responseDiv.offset().top - 100
                    }, 500);
                }
            },
            error: function() {
                // Reset button state
                submitBtn.find('.hajri-btn-loading').hide();
                submitBtn.find('.hajri-btn-text').show();
                submitBtn.prop('disabled', false);
                
                // Show error message
                responseDiv.html('<div class="hajri-error-message">حدث خطأ أثناء معالجة طلبك. يرجى المحاولة مرة أخرى.</div>');
                
                // Scroll to error message
                $('html, body').animate({
                    scrollTop: responseDiv.offset().top - 100
                }, 500);
            }
        });
    }
    
    // Track order conversion for marketing pixels
    function trackOrderConversion(orderId, form) {
        if (typeof hajri_shop === 'undefined') {
            return;
        }
        
        var productId = form.find('[name="product_id"]').val();
        var quantity = form.find('[name="quantity"]').val();
        
        $.ajax({
            url: hajri_shop.ajax_url,
            type: 'POST',
            data: {
                action: 'track_conversion',
                order_id: orderId,
                product_id: productId,
                quantity: quantity,
                security: hajri_shop.nonce
            },
            success: function(response) {
                // Conversion tracking handled by the server
            }
        });
    }
});
</script>