<?php
/**
 * Shipping management functionality.
 *
 * @link       https://example.com
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/includes
 */

/**
 * Shipping management functionality.
 *
 * This class handles all shipping-related operations.
 *
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/includes
 * @author     Your Name <email@example.com>
 */
class Hajri_Cod_Shop_Shipping {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        add_action('wp_ajax_get_shipping_cost', array($this, 'ajax_get_shipping_cost'));
        add_action('wp_ajax_nopriv_get_shipping_cost', array($this, 'ajax_get_shipping_cost'));
        
        add_action('wp_ajax_update_shipping_costs', array($this, 'ajax_update_shipping_costs'));
        add_action('wp_ajax_get_all_shipping_costs', array($this, 'ajax_get_all_shipping_costs'));
    }

    /**
     * Get shipping cost for a city via AJAX.
     *
     * @since    1.0.0
     */
    public function ajax_get_shipping_cost() {
        check_ajax_referer('hajri_shipping_nonce', 'security');
        
        $city = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
        
        if (empty($city)) {
            wp_send_json_error(array('message' => __('City is required.', 'hajri-cod-shop')));
        }
        
        $shipping_cost = self::get_shipping_cost($city);
        
        wp_send_json_success(array(
            'shipping_cost' => $shipping_cost,
            'formatted_cost' => number_format($shipping_cost, 2) . ' ' . __('DZD', 'hajri-cod-shop'),
        ));
    }

    /**
     * Update shipping costs for cities via AJAX.
     *
     * @since    1.0.0
     */
    public function ajax_update_shipping_costs() {
        check_ajax_referer('hajri_admin_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'hajri-cod-shop')));
        }
        
        $city = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
        $cost = isset($_POST['cost']) ? floatval($_POST['cost']) : 0;
        
        if (empty($city)) {
            wp_send_json_error(array('message' => __('City name is required.', 'hajri-cod-shop')));
        }
        
        if ($cost < 0) {
            wp_send_json_error(array('message' => __('Shipping cost cannot be negative.', 'hajri-cod-shop')));
        }
        
        $result = self::update_shipping_cost($city, $cost);
        
        if ($result) {
            wp_send_json_success(array('message' => __('Shipping cost updated successfully.', 'hajri-cod-shop')));
        } else {
            wp_send_json_error(array('message' => __('Failed to update shipping cost.', 'hajri-cod-shop')));
        }
    }

    /**
     * Get all shipping costs via AJAX.
     *
     * @since    1.0.0
     */
    public function ajax_get_all_shipping_costs() {
        check_ajax_referer('hajri_admin_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'hajri-cod-shop')));
        }
        
        $shipping_costs = self::get_all_shipping_costs();
        
        wp_send_json_success(array('shipping_costs' => $shipping_costs));
    }

    /**
     * Get shipping cost for a city.
     *
     * @since    1.0.0
     * @param    string $city The city name.
     * @return   float The shipping cost.
     */
    public static function get_shipping_cost($city) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'hajri_shipping_costs';
        
        $cost = $wpdb->get_var($wpdb->prepare(
            "SELECT shipping_cost FROM $table_name WHERE city_name = %s",
            $city
        ));
        
        // Default cost if city not found
        if (null === $cost) {
            return 500.00; // Default shipping cost in DZD
        }
        
        return floatval($cost);
    }

    /**
     * Update shipping cost for a city.
     *
     * @since    1.0.0
     * @param    string $city The city name.
     * @param    float $cost The shipping cost.
     * @return   bool Whether the update was successful.
     */
    public static function update_shipping_cost($city, $cost) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'hajri_shipping_costs';
        
        // Check if city exists
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE city_name = %s",
            $city
        ));
        
        if ($exists) {
            // Update existing city
            $result = $wpdb->update(
                $table_name,
                array('shipping_cost' => $cost),
                array('city_name' => $city),
                array('%f'),
                array('%s')
            );
        } else {
            // Insert new city
            $result = $wpdb->insert(
                $table_name,
                array(
                    'city_name' => $city,
                    'shipping_cost' => $cost
                ),
                array('%s', '%f')
            );
        }
        
        return ($result !== false);
    }

    /**
     * Get all shipping costs.
     *
     * @since    1.0.0
     * @return   array List of cities and their shipping costs.
     */
    public static function get_all_shipping_costs() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'hajri_shipping_costs';
        
        $results = $wpdb->get_results(
            "SELECT city_name, shipping_cost FROM $table_name ORDER BY city_name ASC",
            ARRAY_A
        );
        
        return $results ? $results : array();
    }

    /**
     * Get a list of Algerian cities.
     *
     * @since    1.0.0
     * @return   array List of city names.
     */
    public static function get_algerian_cities() {
        return array(
            'Adrar', 'Aïn Defla', 'Aïn Témouchent', 'Alger', 'Annaba', 'Batna', 'Béchar', 
            'Béjaïa', 'Biskra', 'Blida', 'Bordj Bou Arréridj', 'Bouira', 'Boumerdès', 
            'Chlef', 'Constantine', 'Djelfa', 'El Bayadh', 'El Oued', 'El Tarf', 'Ghardaïa', 
            'Guelma', 'Illizi', 'Jijel', 'Khenchela', 'Laghouat', 'Mascara', 'Médéa', 
            'Mila', 'Mostaganem', 'M\'Sila', 'Naâma', 'Oran', 'Ouargla', 'Oum El Bouaghi', 
            'Relizane', 'Saïda', 'Sétif', 'Sidi Bel Abbès', 'Skikda', 'Souk Ahras', 
            'Tamanrasset', 'Tébessa', 'Tiaret', 'Tindouf', 'Tipaza', 'Tissemsilt', 
            'Tizi Ouzou', 'Tlemcen'
        );
    }
}

new Hajri_Cod_Shop_Shipping();
