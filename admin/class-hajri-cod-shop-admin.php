<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks for
 * enqueuing the admin-specific stylesheet and JavaScript.
 *
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/admin
 * @author     Your Name <email@example.com>
 */
class Hajri_Cod_Shop_Admin {

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
     * @param    string    $plugin_name       The name of this plugin.
     * @param    string    $version           The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        // Enqueue main CSS
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/hajri-cod-shop-admin.css', array(), $this->version, 'all');
        
        // Enqueue RTL stylesheets if needed
        if (is_rtl() || get_locale() === 'ar') {
            wp_enqueue_style($this->plugin_name . '-rtl', plugin_dir_url(__FILE__) . 'css/hajri-cod-shop-admin-rtl.css', array($this->plugin_name), $this->version, 'all');
            
            // Add RTL body class via JavaScript
            wp_add_inline_script('jquery', 'jQuery(document).ready(function($) { $("body").addClass("rtl"); });');
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/hajri-cod-shop-admin.js', array('jquery', 'jquery-ui-datepicker'), $this->version, false);
        
        // Add jQuery UI sortable for form field reordering
        wp_enqueue_script('jquery-ui-sortable');
        
        // Add form settings JS
        if (isset($_GET['page']) && $_GET['page'] === 'hajri-settings') {
            wp_enqueue_script('hajri-form-settings', plugin_dir_url(__FILE__) . 'js/form-settings.js', array('jquery', 'jquery-ui-sortable'), $this->version, false);
        }
        
        // Localize the script with new data
        $admin_ajax_object = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('hajri_admin_nonce'),
            'strings' => array(
                'confirm_delete' => __('Are you sure you want to delete this item?', 'hajri-cod-shop'),
                'processing' => __('Processing...', 'hajri-cod-shop'),
                'success' => __('Success!', 'hajri-cod-shop'),
                'error' => __('Error:', 'hajri-cod-shop'),
                'remove_text' => __('Remove', 'hajri-cod-shop'),
            )
        );
        wp_localize_script($this->plugin_name, 'hajri_admin_object', $admin_ajax_object);
        
        // Use WordPress' built-in jQuery UI datepicker styles
        wp_enqueue_style('jquery-ui', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
        
        // Add Chart.js for analytics
        if (isset($_GET['page']) && $_GET['page'] === 'hajri-analytics') {
            wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.7.0', false);
        }
    }

    /**
     * Register the admin menu pages.
     *
     * @since    1.0.0
     */
    public function add_admin_menu() {
        // Main menu item
        add_menu_page(
            __('Hajri COD Shop', 'hajri-cod-shop'),
            __('Hajri COD Shop', 'hajri-cod-shop'),
            'manage_options',
            'hajri-cod-shop',
            array($this, 'display_admin_page'),
            'dashicons-cart',
            56
        );
        
        // Products submenu
        add_submenu_page(
            'hajri-cod-shop',
            __('Products', 'hajri-cod-shop'),
            __('Products', 'hajri-cod-shop'),
            'manage_options',
            'edit.php?post_type=hajri_product',
            null
        );
        
        // Orders submenu
        add_submenu_page(
            'hajri-cod-shop',
            __('Orders', 'hajri-cod-shop'),
            __('Orders', 'hajri-cod-shop'),
            'manage_options',
            'hajri-orders',
            array($this, 'display_orders_page')
        );
        
        // Abandoned carts submenu
        add_submenu_page(
            'hajri-cod-shop',
            __('Abandoned Carts', 'hajri-cod-shop'),
            __('Abandoned Carts', 'hajri-cod-shop'),
            'manage_options',
            'hajri-abandoned',
            array($this, 'display_abandoned_page')
        );
        
        // Analytics submenu
        add_submenu_page(
            'hajri-cod-shop',
            __('Analytics', 'hajri-cod-shop'),
            __('Analytics', 'hajri-cod-shop'),
            'manage_options',
            'hajri-analytics',
            array($this, 'display_analytics_page')
        );
        
        // Delivery Companies submenu
        add_submenu_page(
            'hajri-cod-shop',
            __('Algerian Delivery Companies', 'hajri-cod-shop'),
            __('Delivery Companies', 'hajri-cod-shop'),
            'manage_options',
            'hajri-delivery-companies',
            array($this, 'display_delivery_companies_page')
        );
        
        // Wilayas & Municipalities submenu
        add_submenu_page(
            'hajri-cod-shop',
            __('Algerian Wilayas & Municipalities', 'hajri-cod-shop'),
            __('Wilayas & Municipalities', 'hajri-cod-shop'),
            'manage_options',
            'hajri-locations',
            array($this, 'display_locations_page')
        );
        
        // Settings submenu
        add_submenu_page(
            'hajri-cod-shop',
            __('Settings', 'hajri-cod-shop'),
            __('Settings', 'hajri-cod-shop'),
            'manage_options',
            'hajri-settings',
            array($this, 'display_settings_page')
        );
    }

    /**
     * Display the main admin page.
     *
     * @since    1.0.0
     */
    public function display_admin_page() {
        include_once 'partials/hajri-cod-shop-admin-display.php';
    }

    /**
     * Display the orders admin page.
     *
     * @since    1.0.0
     */
    public function display_orders_page() {
        include_once 'partials/hajri-cod-shop-orders.php';
    }

    /**
     * Display the abandoned carts admin page.
     *
     * @since    1.0.0
     */
    public function display_abandoned_page() {
        include_once 'partials/hajri-cod-shop-abandoned.php';
    }

    /**
     * Display the analytics admin page.
     *
     * @since    1.0.0
     */
    public function display_analytics_page() {
        include_once 'partials/hajri-cod-shop-analytics.php';
    }

    /**
     * Display the delivery companies admin page.
     *
     * @since    1.0.0
     */
    public function display_delivery_companies_page() {
        include_once 'partials/hajri-cod-shop-delivery-companies.php';
    }
    
    /**
     * Display the locations admin page.
     *
     * @since    1.0.0
     */
    public function display_locations_page() {
        include_once 'partials/hajri-cod-shop-locations.php';
    }

    /**
     * Display the settings admin page.
     *
     * @since    1.0.0
     */
    public function display_settings_page() {
        include_once 'partials/hajri-cod-shop-settings.php';
    }

    /**
     * Register custom post types.
     *
     * @since    1.0.0
     */
    public function register_custom_post_types() {
        // This is handled in the Hajri_Cod_Shop_Product class
    }

    /**
     * Register meta boxes for custom post types.
     *
     * @since    1.0.0
     */
    public function register_meta_boxes() {
        // This is handled in the Hajri_Cod_Shop_Product class
    }

    /**
     * Save meta box data for custom post types.
     *
     * @since    1.0.0
     * @param    int    $post_id    The ID of the post being saved.
     */
    public function save_meta_box_data($post_id) {
        // This is handled in the Hajri_Cod_Shop_Product class
    }

    /**
     * AJAX handler for getting city shipping costs.
     *
     * @since    1.0.0
     */
    public function ajax_get_city_shipping_costs() {
        check_ajax_referer('hajri_admin_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'hajri-cod-shop')));
        }
        
        $shipping_costs = Hajri_Cod_Shop_Shipping::get_all_shipping_costs();
        
        wp_send_json_success(array('shipping_costs' => $shipping_costs));
    }

    /**
     * AJAX handler for updating city shipping costs.
     *
     * @since    1.0.0
     */
    public function ajax_update_city_shipping_costs() {
        check_ajax_referer('hajri_admin_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'hajri-cod-shop')));
        }
        
        $city = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
        $cost = isset($_POST['cost']) ? floatval($_POST['cost']) : 0;
        
        if (empty($city)) {
            wp_send_json_error(array('message' => __('City name is required.', 'hajri-cod-shop')));
        }
        
        $result = Hajri_Cod_Shop_Shipping::update_shipping_cost($city, $cost);
        
        if ($result) {
            wp_send_json_success(array('message' => __('Shipping cost updated successfully.', 'hajri-cod-shop')));
        } else {
            wp_send_json_error(array('message' => __('Failed to update shipping cost.', 'hajri-cod-shop')));
        }
    }

    /**
     * AJAX handler for updating order status.
     *
     * @since    1.0.0
     */
    public function ajax_update_order_status() {
        check_ajax_referer('hajri_admin_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'hajri-cod-shop')));
        }
        
        $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
        
        if ($order_id <= 0) {
            wp_send_json_error(array('message' => __('Invalid order ID.', 'hajri-cod-shop')));
        }
        
        if (empty($status)) {
            wp_send_json_error(array('message' => __('Status is required.', 'hajri-cod-shop')));
        }
        
        $result = Hajri_Cod_Shop_Order::update_order_status($order_id, $status);
        
        if ($result) {
            wp_send_json_success(array('message' => __('Order status updated successfully.', 'hajri-cod-shop')));
        } else {
            wp_send_json_error(array('message' => __('Failed to update order status.', 'hajri-cod-shop')));
        }
    }

    /**
     * AJAX handler for getting analytics data.
     *
     * @since    1.0.0
     */
    public function ajax_get_analytics_data() {
        check_ajax_referer('hajri_admin_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'hajri-cod-shop')));
        }
        
        $period = isset($_POST['period']) ? sanitize_text_field($_POST['period']) : 'month';
        
        $stats = Hajri_Cod_Shop_Order::get_order_stats($period);
        
        wp_send_json_success(array('stats' => $stats));
    }

    /**
     * AJAX handler for getting abandoned carts.
     *
     * @since    1.0.0
     */
    public function ajax_get_abandoned_carts() {
        check_ajax_referer('hajri_admin_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'hajri-cod-shop')));
        }
        
        global $wpdb;
        $abandoned_table = $wpdb->prefix . 'hajri_abandoned_carts';
        
        $abandoned_carts = $wpdb->get_results(
            "SELECT * FROM $abandoned_table 
            WHERE is_converted = 0 
            ORDER BY created_at DESC 
            LIMIT 50",
            ARRAY_A
        );
        
        foreach ($abandoned_carts as &$cart) {
            $cart['products'] = json_decode($cart['products'], true);
        }
        
        wp_send_json_success(array('abandoned_carts' => $abandoned_carts));
    }

    /**
     * AJAX handler for deleting an abandoned cart.
     *
     * @since    1.0.0
     */
    public function ajax_delete_abandoned_cart() {
        check_ajax_referer('hajri_admin_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'hajri-cod-shop')));
        }
        
        $cart_id = isset($_POST['cart_id']) ? intval($_POST['cart_id']) : 0;
        
        if ($cart_id <= 0) {
            wp_send_json_error(array('message' => __('Invalid cart ID.', 'hajri-cod-shop')));
        }
        
        global $wpdb;
        $abandoned_table = $wpdb->prefix . 'hajri_abandoned_carts';
        
        $result = $wpdb->delete(
            $abandoned_table,
            array('id' => $cart_id),
            array('%d')
        );
        
        if ($result) {
            wp_send_json_success(array('message' => __('Abandoned cart deleted successfully.', 'hajri-cod-shop')));
        } else {
            wp_send_json_error(array('message' => __('Failed to delete abandoned cart.', 'hajri-cod-shop')));
        }
    }
}
