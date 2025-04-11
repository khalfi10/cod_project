<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار فورم الطلب للدفع عند الاستلام</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            direction: rtl;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        header {
            text-align: center;
            margin-bottom: 30px;
        }
        h1 {
            color: #333;
            font-size: 28px;
        }
        .description {
            color: #666;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
    <!-- Load jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <header>
            <h1>اختبار فورم طلب المنتج للدفع عند الاستلام</h1>
            <div class="description">هذه صفحة اختبار لفورم طلب المنتج مع خيارات الدفع عند الاستلام</div>
        </header>

        <!-- Simulating WordPress shortcode -->
        <div id="cod-form-container">
            <?php
            // Include necessary files to simulate WordPress environment
            define('WPINC', true);
            
            // Create a sample product for testing
            $product = array(
                'id' => 1,
                'title' => 'منتج اختباري للدفع عند الاستلام',
                'excerpt' => 'هذا وصف مختصر للمنتج الاختباري المستخدم لتجربة فورم الطلب.',
                'price' => 2500,
                'sale_price' => 1999,
                'current_price' => 1999,
                'in_stock' => true,
                'image' => 'https://via.placeholder.com/300',
                'variations' => array(
                    array('color' => 'أحمر', 'size' => 'صغير'),
                    array('color' => 'أحمر', 'size' => 'متوسط'),
                    array('color' => 'أزرق', 'size' => 'صغير'),
                    array('color' => 'أزرق', 'size' => 'متوسط'),
                    array('color' => 'أسود', 'size' => 'صغير'),
                    array('color' => 'أسود', 'size' => 'متوسط'),
                    array('color' => 'أسود', 'size' => 'كبير'),
                ),
            );
            
            // Simulate WordPress shortcode attributes
            $atts = array(
                'id' => 1,
                'show_image' => 'yes',
                'show_description' => 'yes',
                'show_price' => 'yes',
                'button_text' => 'تأكيد الطلب',
            );
            
            // Simulate WordPress translation function
            if (!function_exists('__')) {
                function __($text, $domain = 'default') {
                    return $text;
                }
            }
            
            // Simulate WordPress escaping functions
            if (!function_exists('esc_attr')) {
                function esc_attr($text) {
                    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
                }
            }
            
            if (!function_exists('esc_html')) {
                function esc_html($text) {
                    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
                }
            }
            
            if (!function_exists('esc_url')) {
                function esc_url($url) {
                    return $url;
                }
            }
            
            if (!function_exists('esc_html__')) {
                function esc_html__($text, $domain = 'default') {
                    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
                }
            }
            
            if (!function_exists('esc_attr__')) {
                function esc_attr__($text, $domain = 'default') {
                    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
                }
            }
            
            if (!function_exists('wp_kses_post')) {
                function wp_kses_post($text) {
                    return $text;
                }
            }
            
            if (!function_exists('wp_nonce_field')) {
                function wp_nonce_field($action, $name) {
                    echo '<input type="hidden" name="' . esc_attr($name) . '" value="test_nonce" />';
                }
            }
            
            // Define get_option function
            if (!function_exists('get_option')) {
                function get_option($option, $default = false) {
                    $settings = array(
                        'hajri_cod_shop_settings' => array(
                            'recaptcha_enabled' => false,
                            'recaptcha_site_key' => '',
                        )
                    );
                    
                    return isset($settings[$option]) ? $settings[$option] : $default;
                }
            }
            
            // Simulate Hajri_Cod_Shop_Shipping class
            class Hajri_Cod_Shop_Shipping {
                public static function get_algerian_cities() {
                    return array(
                        'الجزائر',
                        'وهران',
                        'قسنطينة',
                        'عنابة',
                        'سطيف',
                        'بجاية',
                        'تلمسان',
                        'سيدي بلعباس',
                        'باتنة',
                        'مستغانم',
                        'سكيكدة',
                        'المدية',
                        'تيزي وزو',
                        'الشلف',
                        'بليدة',
                        'أم البواقي',
                        'الوادي',
                        'برج بوعريريج',
                        'بسكرة',
                        'غليزان',
                        'تيارت',
                        'بومرداس',
                        'الأغواط',
                        'سوق أهراس',
                        'تيبازة',
                        'الطارف',
                        'البويرة',
                        'قالمة',
                        'خنشلة',
                        'الجلفة',
                        'عين الدفلى',
                        'أدرار',
                        'بشار',
                        'ميلة',
                        'ورقلة',
                        'غرداية',
                        'البيض',
                        'المسيلة',
                        'تندوف',
                        'إليزي',
                        'تمنراست',
                        'النعامة',
                        'تيسمسيلت',
                        'معسكر',
                        'جيجل',
                        'سعيدة',
                        'عين تموشنت',
                        'إن صالح',
                        'إن قزام',
                        'برج باجي مختار',
                        'تيميمون',
                        'جانت',
                        'المغير',
                        'المنيعة',
                        'تقرت',
                        'بني عباس',
                        'أولاد جلال'
                    );
                }
                
                public static function get_shipping_cost($city) {
                    $shipping_costs = array(
                        'الجزائر' => 400,
                        'وهران' => 500,
                        'قسنطينة' => 550,
                    );
                    
                    return isset($shipping_costs[$city]) ? $shipping_costs[$city] : 600;
                }
            }
            
            // Include the product form template
            include 'public/partials/hajri-cod-shop-product-form-new.php';
            ?>
        </div>
    </div>

    <script>
        // Simulate Ajax responses
        const hajri_shop = {
            ajax_url: 'ajax.php',
            nonce: 'test_nonce',
            order_nonce: 'test_order_nonce',
            shipping_nonce: 'test_shipping_nonce',
            currency: 'دج',
            strings: {
                error: 'خطأ:',
                success: 'نجاح:',
                processing: 'جاري المعالجة...',
                add_to_cart: 'أضف إلى السلة',
                added_to_cart: 'تمت الإضافة إلى السلة',
                out_of_stock: 'غير متوفر',
                please_select: 'يرجى الإختيار',
                phone_error: 'يرجى إدخال رقم هاتف جزائري صالح',
                saving_cart: 'جاري حفظ السلة...'
            },
            recaptcha: {
                enabled: false
            }
        };

        // Mock Ajax requests
        $.ajax = function(options) {
            console.log('Ajax request made:', options);
            
            setTimeout(function() {
                if (options.data.action === 'get_shipping_cost') {
                    const cityValue = options.data.city;
                    let shippingCost = 600; // Default
                    
                    // Simulate different costs for different cities
                    if (cityValue === 'الجزائر') shippingCost = 400;
                    if (cityValue === 'وهران') shippingCost = 500;
                    if (cityValue === 'قسنطينة') shippingCost = 550;
                    
                    options.success({
                        success: true,
                        data: {
                            shipping_cost: shippingCost,
                            formatted_cost: shippingCost + ' دج'
                        }
                    });
                } 
                else if (options.data.action === 'submit_order') {
                    options.success({
                        success: true,
                        data: {
                            order_id: 'TEST-' + Math.floor(Math.random() * 10000),
                            message: 'تم استلام طلبك بنجاح!'
                        }
                    });
                }
                else if (options.data.action === 'apply_discount') {
                    const quantity = parseInt(options.data.quantity) || 1;
                    const productId = parseInt(options.data.product_id) || 0;
                    
                    // Use the product price from our test product
                    const price = 1999;
                    const subtotal = price * quantity;
                    const shippingCost = 600;
                    
                    options.success({
                        success: true,
                        data: {
                            subtotal: subtotal,
                            subtotal_formatted: subtotal + ' دج',
                            shipping_cost: shippingCost,
                            shipping_cost_formatted: shippingCost + ' دج',
                            discount_amount: 0,
                            discount_amount_formatted: '0 دج',
                            total: subtotal + shippingCost,
                            total_formatted: (subtotal + shippingCost) + ' دج',
                            discounts_applied: []
                        }
                    });
                }
                else if (options.data.action === 'track_conversion') {
                    options.success({
                        success: true
                    });
                }
            }, 500); // Simulate network delay
            
            return { done: function() {} };
        };
    </script>
</body>
</html>