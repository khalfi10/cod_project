<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks for
 * enqueuing the public-facing stylesheet and JavaScript.
 *
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/public
 * @author     Your Name <email@example.com>
 */
class Hajri_Cod_Shop_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of the plugin.
     * @param    string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        // Enqueue main CSS
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/hajri-cod-shop-public.css', array(), $this->version, 'all');
        
        // Enqueue RTL stylesheets if needed
        if (is_rtl() || get_locale() === 'ar') {
            wp_enqueue_style($this->plugin_name . '-rtl', plugin_dir_url(__FILE__) . 'css/hajri-cod-shop-rtl.css', array($this->plugin_name), $this->version, 'all');
            
            // Add RTL body class via JavaScript
            wp_add_inline_script('jquery', 'jQuery(document).ready(function($) { $("body").addClass("rtl"); });');
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/hajri-cod-shop-public.js', array('jquery'), $this->version, false);
        
        // Add localized script data for AJAX and other functionality
        $script_data = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('hajri_public_nonce'),
            'order_nonce' => wp_create_nonce('hajri_order_nonce'),
            'shipping_nonce' => wp_create_nonce('hajri_shipping_nonce'),
            'currency' => __('DZD', 'hajri-cod-shop'),
            'strings' => array(
                'error' => __('Error:', 'hajri-cod-shop'),
                'success' => __('Success:', 'hajri-cod-shop'),
                'processing' => __('Processing...', 'hajri-cod-shop'),
                'add_to_cart' => __('Add to Cart', 'hajri-cod-shop'),
                'added_to_cart' => __('Added to Cart', 'hajri-cod-shop'),
                'out_of_stock' => __('Out of Stock', 'hajri-cod-shop'),
                'please_select' => __('Please select', 'hajri-cod-shop'),
                'phone_error' => __('Please enter a valid Algerian phone number', 'hajri-cod-shop'),
                'saving_cart' => __('Saving your cart...', 'hajri-cod-shop'),
            )
        );
        
        // Add recaptcha if enabled
        $settings = get_option('hajri_cod_shop_settings', array());
        if (isset($settings['recaptcha_enabled']) && $settings['recaptcha_enabled'] && !empty($settings['recaptcha_site_key'])) {
            $script_data['recaptcha'] = array(
                'enabled' => true,
                'site_key' => $settings['recaptcha_site_key']
            );
            
            // Add Google reCAPTCHA script
            wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js?render=' . $settings['recaptcha_site_key'], array(), null, true);
        } else {
            $script_data['recaptcha'] = array(
                'enabled' => false
            );
        }
        
        wp_localize_script($this->plugin_name, 'hajri_shop', $script_data);
    }

    /**
     * Register shortcodes for the plugin.
     *
     * @since    1.0.0
     */
    public function register_shortcodes() {
        add_shortcode('hajri_product_form', array($this, 'product_form_shortcode'));
        add_shortcode('hajri_order_form', array($this, 'order_form_shortcode'));
        add_shortcode('hajri_product_grid', array($this, 'product_grid_shortcode'));
        add_shortcode('hajri_cod_form', array($this, 'cod_form_shortcode'));
    }

    /**
     * Shortcode for displaying product form.
     *
     * @since    1.0.0
     * @param    array    $atts    Shortcode attributes.
     * @return   string   HTML output of the shortcode.
     */
    public function product_form_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
            'show_image' => 'yes',
            'show_description' => 'yes',
            'show_price' => 'yes',
            'button_text' => __('Order Now', 'hajri-cod-shop'),
        ), $atts, 'hajri_product_form');
        
        $product_id = intval($atts['id']);
        
        if ($product_id <= 0) {
            return '<p class="hajri-error">' . __('Please specify a valid product ID.', 'hajri-cod-shop') . '</p>';
        }
        
        $product = Hajri_Cod_Shop_Product::get_product($product_id);
        
        if (!$product) {
            return '<p class="hajri-error">' . __('Product not found.', 'hajri-cod-shop') . '</p>';
        }
        
        // Buffer the output
        ob_start();
        
        include plugin_dir_path(__FILE__) . 'partials/hajri-cod-shop-product-form.php';
        
        return ob_get_clean();
    }

    /**
     * Shortcode for displaying standalone order form.
     *
     * @since    1.0.0
     * @param    array    $atts    Shortcode attributes.
     * @return   string   HTML output of the shortcode.
     */
    public function order_form_shortcode($atts) {
        $atts = shortcode_atts(array(
            'products' => '',
            'default_product' => 0,
            'button_text' => __('Place Order', 'hajri-cod-shop'),
        ), $atts, 'hajri_order_form');
        
        $product_ids = array();
        
        if (!empty($atts['products'])) {
            $product_ids = array_map('intval', explode(',', $atts['products']));
        }
        
        if (empty($product_ids)) {
            // If no products specified, get all products
            $products_data = Hajri_Cod_Shop_Product::get_products(array(
                'posts_per_page' => -1,
                'orderby' => 'title',
                'order' => 'ASC',
            ));
            
            $product_ids = array_column($products_data, 'id');
        }
        
        if (empty($product_ids)) {
            return '<p class="hajri-error">' . __('No products available.', 'hajri-cod-shop') . '</p>';
        }
        
        $default_product_id = intval($atts['default_product']);
        if ($default_product_id <= 0 || !in_array($default_product_id, $product_ids)) {
            $default_product_id = $product_ids[0];
        }
        
        // Get products data
        $products = array();
        foreach ($product_ids as $id) {
            $product_data = Hajri_Cod_Shop_Product::get_product($id);
            if ($product_data) {
                $products[] = $product_data;
            }
        }
        
        if (empty($products)) {
            return '<p class="hajri-error">' . __('No valid products found.', 'hajri-cod-shop') . '</p>';
        }
        
        // Get Algerian cities for the dropdown
        $cities = Hajri_Cod_Shop_Shipping::get_algerian_cities();
        
        // Buffer the output
        ob_start();
        
        include plugin_dir_path(__FILE__) . 'partials/hajri-cod-shop-order-form.php';
        
        return ob_get_clean();
    }

    /**
     * Shortcode for displaying a grid of products.
     *
     * @since    1.0.0
     * @param    array    $atts    Shortcode attributes.
     * @return   string   HTML output of the shortcode.
     */
    public function product_grid_shortcode($atts) {
        $atts = shortcode_atts(array(
            'category' => '',
            'columns' => 3,
            'limit' => 9,
            'orderby' => 'date',
            'order' => 'DESC',
        ), $atts, 'hajri_product_grid');
        
        $args = array(
            'posts_per_page' => intval($atts['limit']),
            'orderby' => sanitize_text_field($atts['orderby']),
            'order' => sanitize_text_field($atts['order']),
        );
        
        // Filter by category if specified
        if (!empty($atts['category'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'hajri_product_cat',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($atts['category']),
                ),
            );
        }
        
        $products = Hajri_Cod_Shop_Product::get_products($args);
        
        if (empty($products)) {
            return '<p class="hajri-info">' . __('No products found.', 'hajri-cod-shop') . '</p>';
        }
        
        $columns = intval($atts['columns']);
        if ($columns < 1 || $columns > 6) {
            $columns = 3;
        }
        
        $column_class = 'hajri-column-' . $columns;
        
        $output = '<div class="hajri-product-grid">';
        
        foreach ($products as $product) {
            $output .= '<div class="hajri-product-item ' . esc_attr($column_class) . '">';
            
            if (!empty($product['image'])) {
                $output .= '<div class="hajri-product-image">';
                $output .= '<a href="' . esc_url($product['permalink']) . '">';
                $output .= '<img src="' . esc_url($product['image']) . '" alt="' . esc_attr($product['title']) . '">';
                $output .= '</a>';
                $output .= '</div>';
            }
            
            $output .= '<h3 class="hajri-product-title">';
            $output .= '<a href="' . esc_url($product['permalink']) . '">' . esc_html($product['title']) . '</a>';
            $output .= '</h3>';
            
            if (!empty($product['excerpt'])) {
                $output .= '<div class="hajri-product-excerpt">' . wp_kses_post($product['excerpt']) . '</div>';
            }
            
            $output .= '<div class="hajri-product-price">';
            if (!empty($product['sale_price'])) {
                $output .= '<span class="hajri-regular-price">' . esc_html(number_format($product['price'], 2)) . ' ' . __('DZD', 'hajri-cod-shop') . '</span>';
                $output .= '<span class="hajri-sale-price">' . esc_html(number_format($product['sale_price'], 2)) . ' ' . __('DZD', 'hajri-cod-shop') . '</span>';
            } else {
                $output .= '<span class="hajri-price">' . esc_html(number_format($product['price'], 2)) . ' ' . __('DZD', 'hajri-cod-shop') . '</span>';
            }
            $output .= '</div>';
            
            $output .= '<div class="hajri-product-actions">';
            $output .= '<a href="' . esc_url($product['permalink']) . '" class="hajri-button">' . __('View Product', 'hajri-cod-shop') . '</a>';
            $output .= '</div>';
            
            $output .= '</div>';
        }
        
        $output .= '</div>';
        
        return $output;
    }

    /**
     * AJAX handler for submitting an order.
     *
     * @since    1.0.0
     */
    public function ajax_submit_order() {
        // This is handled in the Hajri_Cod_Shop_Order class
    }

    /**
     * AJAX handler for saving abandoned cart data.
     *
     * @since    1.0.0
     */
    public function ajax_save_abandoned_cart() {
        check_ajax_referer('hajri_order_nonce', 'security');
        
        $response = array(
            'success' => false,
            'message' => '',
        );
        
        // Get cart data from AJAX request
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        $city = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
        $address = isset($_POST['address']) ? sanitize_textarea_field($_POST['address']) : '';
        
        // Validate product
        $product = Hajri_Cod_Shop_Product::get_product($product_id);
        if (!$product) {
            $response['message'] = __('Invalid product.', 'hajri-cod-shop');
            wp_send_json($response);
        }
        
        // Create product array for storing in the database
        $products_json = json_encode(array(
            array(
                'id' => $product_id,
                'name' => $product['title'],
                'price' => $product['current_price'],
                'quantity' => $quantity,
                'subtotal' => $product['current_price'] * $quantity,
            )
        ));
        
        // Calculate total amount
        $total_amount = $product['current_price'] * $quantity;
        if (!empty($city)) {
            $shipping_cost = Hajri_Cod_Shop_Shipping::get_shipping_cost($city);
            $total_amount += $shipping_cost;
        }
        
        // Get client IP
        $ip_address = Hajri_Cod_Shop_Security::get_client_ip();
        
        // Check if we already have an abandoned cart for this IP
        global $wpdb;
        $abandoned_cart_table = $wpdb->prefix . 'hajri_abandoned_carts';
        
        $existing_cart = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id FROM $abandoned_cart_table WHERE ip_address = %s AND is_converted = 0",
                $ip_address
            )
        );
        
        if ($existing_cart) {
            // Update existing cart
            $result = $wpdb->update(
                $abandoned_cart_table,
                array(
                    'customer_name' => $name,
                    'phone_number' => $phone,
                    'city' => $city,
                    'address' => $address,
                    'products' => $products_json,
                    'total_amount' => $total_amount,
                    'updated_at' => current_time('mysql'),
                ),
                array('id' => $existing_cart->id),
                array('%s', '%s', '%s', '%s', '%s', '%f', '%s'),
                array('%d')
            );
        } else {
            // Insert new cart
            $result = $wpdb->insert(
                $abandoned_cart_table,
                array(
                    'customer_name' => $name,
                    'phone_number' => $phone,
                    'city' => $city,
                    'address' => $address,
                    'products' => $products_json,
                    'total_amount' => $total_amount,
                    'ip_address' => $ip_address,
                    'created_at' => current_time('mysql'),
                ),
                array('%s', '%s', '%s', '%s', '%s', '%f', '%s', '%s')
            );
        }
        
        if ($result !== false) {
            $response['success'] = true;
            $response['message'] = __('Cart saved.', 'hajri-cod-shop');
        } else {
            $response['message'] = __('Failed to save cart data.', 'hajri-cod-shop');
        }
        
        wp_send_json($response);
    }

    /**
     * AJAX handler for getting shipping cost.
     *
     * @since    1.0.0
     */
    public function ajax_get_shipping_cost() {
        check_ajax_referer('hajri_shipping_nonce', 'security');
        
        $city = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
        
        if (empty($city)) {
            wp_send_json_error(array('message' => __('City is required.', 'hajri-cod-shop')));
        }
        
        $shipping_cost = Hajri_Cod_Shop_Shipping::get_shipping_cost($city);
        
        wp_send_json_success(array(
            'shipping_cost' => $shipping_cost,
            'formatted_cost' => number_format($shipping_cost, 2) . ' ' . __('DZD', 'hajri-cod-shop'),
        ));
    }

    /**
     * Shortcode for displaying enhanced COD order form.
     *
     * @since    1.0.0
     * @param    array    $atts    Shortcode attributes.
     * @return   string   HTML output of the shortcode.
     */
    public function cod_form_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
            'show_image' => 'yes',
            'show_description' => 'yes',
            'show_price' => 'yes',
            'button_text' => __('تأكيد الطلب', 'hajri-cod-shop'),
        ), $atts, 'hajri_cod_form');
        
        $product_id = intval($atts['id']);
        
        if ($product_id <= 0) {
            return '<p class="hajri-error">' . __('يرجى تحديد رقم معرف منتج صالح.', 'hajri-cod-shop') . '</p>';
        }
        
        $product = Hajri_Cod_Shop_Product::get_product($product_id);
        
        if (!$product) {
            return '<p class="hajri-error">' . __('المنتج غير موجود.', 'hajri-cod-shop') . '</p>';
        }
        
        // Buffer the output
        ob_start();
        
        include plugin_dir_path(__FILE__) . 'partials/hajri-cod-shop-product-form-new.php';
        
        return ob_get_clean();
    }

    /**
     * AJAX handler for applying discount.
     *
     * @since    1.0.0
     */
    public function ajax_apply_discount() {
        check_ajax_referer('hajri_order_nonce', 'security');
        
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        $city = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
        
        // Validate product
        $product = Hajri_Cod_Shop_Product::get_product($product_id);
        if (!$product) {
            wp_send_json_error(array('message' => __('Invalid product.', 'hajri-cod-shop')));
        }
        
        // Calculate subtotal
        $subtotal = $product['current_price'] * $quantity;
        
        // Get shipping cost
        $shipping_cost = 0;
        if (!empty($city)) {
            $shipping_cost = Hajri_Cod_Shop_Shipping::get_shipping_cost($city);
        }
        
        // Calculate total before discount
        $total_before_discount = $subtotal + $shipping_cost;
        
        // Apply discounts
        $discount_info = Hajri_Cod_Shop_Discounts::apply_discounts($product_id, $quantity, $city, $total_before_discount);
        $discount_amount = $discount_info['discount_amount'];
        $final_total = $total_before_discount - $discount_amount;
        
        wp_send_json_success(array(
            'subtotal' => $subtotal,
            'subtotal_formatted' => number_format($subtotal, 2) . ' ' . __('DZD', 'hajri-cod-shop'),
            'shipping_cost' => $shipping_cost,
            'shipping_cost_formatted' => number_format($shipping_cost, 2) . ' ' . __('DZD', 'hajri-cod-shop'),
            'discount_amount' => $discount_amount,
            'discount_amount_formatted' => number_format($discount_amount, 2) . ' ' . __('DZD', 'hajri-cod-shop'),
            'total' => $final_total,
            'total_formatted' => number_format($final_total, 2) . ' ' . __('DZD', 'hajri-cod-shop'),
            'discounts_applied' => $discount_info['discounts_applied']
        ));
    }

    /**
     * Display product content on single product pages.
     *
     * @since    1.0.0
     */
    public function single_product_content($content) {
        global $post;
        
        // Check if we're on a single product page
        if (is_singular('hajri_product') && $post->post_type === 'hajri_product') {
            // Buffer the output
            ob_start();
            
            // Get product data
            $product = Hajri_Cod_Shop_Product::get_product($post->ID);
            
            if ($product) {
                echo '<div class="hajri-single-product">';
                
                // Display product image
                if (!empty($product['image'])) {
                    echo '<div class="hajri-product-image">';
                    echo '<img src="' . esc_url($product['image']) . '" alt="' . esc_attr($product['title']) . '">';
                    echo '</div>';
                }
                
                // Display product details
                echo '<div class="hajri-product-details">';
                
                // Price
                echo '<div class="hajri-product-price">';
                if (!empty($product['sale_price'])) {
                    echo '<span class="hajri-regular-price">' . esc_html(number_format($product['price'], 2)) . ' ' . __('DZD', 'hajri-cod-shop') . '</span>';
                    echo '<span class="hajri-sale-price">' . esc_html(number_format($product['sale_price'], 2)) . ' ' . __('DZD', 'hajri-cod-shop') . '</span>';
                } else {
                    echo '<span class="hajri-price">' . esc_html(number_format($product['price'], 2)) . ' ' . __('DZD', 'hajri-cod-shop') . '</span>';
                }
                echo '</div>';
                
                // Stock status
                echo '<div class="hajri-product-stock">';
                if ($product['in_stock']) {
                    echo '<span class="hajri-in-stock">' . __('In Stock', 'hajri-cod-shop') . '</span>';
                } else {
                    echo '<span class="hajri-out-of-stock">' . __('Out of Stock', 'hajri-cod-shop') . '</span>';
                }
                echo '</div>';
                
                // Product content
                echo '<div class="hajri-product-content">';
                echo wp_kses_post($content);
                echo '</div>';
                
                // Display product form
                echo do_shortcode('[hajri_product_form id="' . $post->ID . '"]');
                
                echo '</div>'; // End .hajri-product-details
                
                // Related products for alternative medicine
                $related_products = Hajri_Cod_Shop_Product::get_related_products($post->ID);
                
                if (!empty($related_products)) {
                    echo '<div class="hajri-related-products">';
                    echo '<h3>' . __('Related Products for Alternative Medicine', 'hajri-cod-shop') . '</h3>';
                    
                    echo '<div class="hajri-product-grid">';
                    
                    foreach ($related_products as $related) {
                        echo '<div class="hajri-product-item hajri-column-3">';
                        
                        if (!empty($related['image'])) {
                            echo '<div class="hajri-product-image">';
                            echo '<a href="' . esc_url($related['permalink']) . '">';
                            echo '<img src="' . esc_url($related['image']) . '" alt="' . esc_attr($related['title']) . '">';
                            echo '</a>';
                            echo '</div>';
                        }
                        
                        echo '<h4 class="hajri-product-title">';
                        echo '<a href="' . esc_url($related['permalink']) . '">' . esc_html($related['title']) . '</a>';
                        echo '</h4>';
                        
                        echo '<div class="hajri-product-price">';
                        if (!empty($related['sale_price'])) {
                            echo '<span class="hajri-regular-price">' . esc_html(number_format($related['price'], 2)) . ' ' . __('DZD', 'hajri-cod-shop') . '</span>';
                            echo '<span class="hajri-sale-price">' . esc_html(number_format($related['sale_price'], 2)) . ' ' . __('DZD', 'hajri-cod-shop') . '</span>';
                        } else {
                            echo '<span class="hajri-price">' . esc_html(number_format($related['price'], 2)) . ' ' . __('DZD', 'hajri-cod-shop') . '</span>';
                        }
                        echo '</div>';
                        
                        echo '<div class="hajri-product-actions">';
                        echo '<a href="' . esc_url($related['permalink']) . '" class="hajri-button">' . __('View Product', 'hajri-cod-shop') . '</a>';
                        echo '</div>';
                        
                        echo '</div>';
                    }
                    
                    echo '</div>'; // End .hajri-product-grid
                    echo '</div>'; // End .hajri-related-products
                }
                
                echo '</div>'; // End .hajri-single-product
                
                return ob_get_clean();
            }
        }
        
        return $content;
    }

    /**
     * Add marketing pixels to site header.
     *
     * @since    1.0.0
     */
    public function add_marketing_pixels() {
        Hajri_Cod_Shop_Marketing::output_header_pixels();
    }

    /**
     * Add marketing event tracking to footer.
     *
     * @since    1.0.0
     */
    public function add_marketing_events() {
        Hajri_Cod_Shop_Marketing::output_footer_scripts();
    }
}
