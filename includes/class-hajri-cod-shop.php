<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/includes
 */

/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/includes
 * @author     Your Name <email@example.com>
 */
class Hajri_Cod_Shop {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Hajri_Cod_Shop_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if (defined('HAJRI_COD_SHOP_VERSION')) {
            $this->version = HAJRI_COD_SHOP_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'hajri-cod-shop';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-hajri-cod-shop-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-hajri-cod-shop-i18n.php';

        /**
         * The classes that handle the plugin functionality
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-hajri-cod-shop-product.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-hajri-cod-shop-order.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-hajri-cod-shop-shipping.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-hajri-cod-shop-discounts.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-hajri-cod-shop-google-sheets.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-hajri-cod-shop-security.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-hajri-cod-shop-marketing.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-hajri-cod-shop-delivery-companies.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-hajri-cod-shop-locations.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-hajri-cod-shop-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-hajri-cod-shop-public.php';

        $this->loader = new Hajri_Cod_Shop_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {
        $plugin_i18n = new Hajri_Cod_Shop_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new Hajri_Cod_Shop_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        
        // Add admin menu
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_admin_menu');
        
        // Register custom post types
        $this->loader->add_action('init', $plugin_admin, 'register_custom_post_types');
        
        // Register meta boxes
        $this->loader->add_action('add_meta_boxes', $plugin_admin, 'register_meta_boxes');
        
        // Save meta box data
        $this->loader->add_action('save_post', $plugin_admin, 'save_meta_box_data');
        
        // AJAX handlers for admin
        $this->loader->add_action('wp_ajax_get_city_shipping_costs', $plugin_admin, 'ajax_get_city_shipping_costs');
        $this->loader->add_action('wp_ajax_update_city_shipping_costs', $plugin_admin, 'ajax_update_city_shipping_costs');
        $this->loader->add_action('wp_ajax_update_order_status', $plugin_admin, 'ajax_update_order_status');
        $this->loader->add_action('wp_ajax_get_analytics_data', $plugin_admin, 'ajax_get_analytics_data');
        $this->loader->add_action('wp_ajax_get_abandoned_carts', $plugin_admin, 'ajax_get_abandoned_carts');
        $this->loader->add_action('wp_ajax_delete_abandoned_cart', $plugin_admin, 'ajax_delete_abandoned_cart');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new Hajri_Cod_Shop_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        
        // Register shortcodes
        $this->loader->add_action('init', $plugin_public, 'register_shortcodes');
        
        // Ajax handlers for public
        $this->loader->add_action('wp_ajax_submit_order', $plugin_public, 'ajax_submit_order');
        $this->loader->add_action('wp_ajax_nopriv_submit_order', $plugin_public, 'ajax_submit_order');
        
        $this->loader->add_action('wp_ajax_save_abandoned_cart', $plugin_public, 'ajax_save_abandoned_cart');
        $this->loader->add_action('wp_ajax_nopriv_save_abandoned_cart', $plugin_public, 'ajax_save_abandoned_cart');
        
        $this->loader->add_action('wp_ajax_get_shipping_cost', $plugin_public, 'ajax_get_shipping_cost');
        $this->loader->add_action('wp_ajax_nopriv_get_shipping_cost', $plugin_public, 'ajax_get_shipping_cost');
        
        $this->loader->add_action('wp_ajax_apply_discount', $plugin_public, 'ajax_apply_discount');
        $this->loader->add_action('wp_ajax_nopriv_apply_discount', $plugin_public, 'ajax_apply_discount');
        
        // أضف فورم الطلب تلقائياً لصفحة المنتج
        $this->loader->add_filter('the_content', $plugin_public, 'single_product_content');
    }

    /**
     * Register custom Location management hooks
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_locations_hooks() {
        $locations = new Hajri_Cod_Shop_Locations();
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        // Initialize other components
        $this->define_locations_hooks();
        
        // Run all hooks
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Hajri_Cod_Shop_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }
}
