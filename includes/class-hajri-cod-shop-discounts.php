<?php
/**
 * Discount management functionality.
 *
 * @link       https://example.com
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/includes
 */

/**
 * Discount management functionality.
 *
 * This class handles all discount-related operations.
 *
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/includes
 * @author     Your Name <email@example.com>
 */
class Hajri_Cod_Shop_Discounts {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        add_action('wp_ajax_create_discount', array($this, 'ajax_create_discount'));
        add_action('wp_ajax_update_discount', array($this, 'ajax_update_discount'));
        add_action('wp_ajax_delete_discount', array($this, 'ajax_delete_discount'));
        add_action('wp_ajax_get_discounts', array($this, 'ajax_get_discounts'));
    }

    /**
     * Create a new discount via AJAX.
     *
     * @since    1.0.0
     */
    public function ajax_create_discount() {
        check_ajax_referer('hajri_admin_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'hajri-cod-shop')));
        }
        
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
        $value = isset($_POST['value']) ? floatval($_POST['value']) : 0;
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : null;
        $city = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : null;
        $start_date = isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : null;
        $end_date = isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : null;
        $is_active = isset($_POST['is_active']) ? (bool)$_POST['is_active'] : true;
        
        // Validate inputs
        if (empty($name)) {
            wp_send_json_error(array('message' => __('Discount name is required.', 'hajri-cod-shop')));
        }
        
        if (!in_array($type, array('percentage', 'fixed', 'buy_one_get_one', 'city_based'))) {
            wp_send_json_error(array('message' => __('Invalid discount type.', 'hajri-cod-shop')));
        }
        
        if ($value < 0) {
            wp_send_json_error(array('message' => __('Discount value cannot be negative.', 'hajri-cod-shop')));
        }
        
        $result = self::create_discount($name, $type, $value, $product_id, $city, $start_date, $end_date, $is_active);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('Discount created successfully.', 'hajri-cod-shop'),
                'discount_id' => $result
            ));
        } else {
            wp_send_json_error(array('message' => __('Failed to create discount.', 'hajri-cod-shop')));
        }
    }

    /**
     * Update an existing discount via AJAX.
     *
     * @since    1.0.0
     */
    public function ajax_update_discount() {
        check_ajax_referer('hajri_admin_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'hajri-cod-shop')));
        }
        
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
        $value = isset($_POST['value']) ? floatval($_POST['value']) : 0;
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : null;
        $city = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : null;
        $start_date = isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : null;
        $end_date = isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : null;
        $is_active = isset($_POST['is_active']) ? (bool)$_POST['is_active'] : true;
        
        // Validate inputs
        if ($id <= 0) {
            wp_send_json_error(array('message' => __('Invalid discount ID.', 'hajri-cod-shop')));
        }
        
        if (empty($name)) {
            wp_send_json_error(array('message' => __('Discount name is required.', 'hajri-cod-shop')));
        }
        
        if (!in_array($type, array('percentage', 'fixed', 'buy_one_get_one', 'city_based'))) {
            wp_send_json_error(array('message' => __('Invalid discount type.', 'hajri-cod-shop')));
        }
        
        if ($value < 0) {
            wp_send_json_error(array('message' => __('Discount value cannot be negative.', 'hajri-cod-shop')));
        }
        
        $result = self::update_discount($id, $name, $type, $value, $product_id, $city, $start_date, $end_date, $is_active);
        
        if ($result) {
            wp_send_json_success(array('message' => __('Discount updated successfully.', 'hajri-cod-shop')));
        } else {
            wp_send_json_error(array('message' => __('Failed to update discount.', 'hajri-cod-shop')));
        }
    }

    /**
     * Delete a discount via AJAX.
     *
     * @since    1.0.0
     */
    public function ajax_delete_discount() {
        check_ajax_referer('hajri_admin_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'hajri-cod-shop')));
        }
        
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if ($id <= 0) {
            wp_send_json_error(array('message' => __('Invalid discount ID.', 'hajri-cod-shop')));
        }
        
        $result = self::delete_discount($id);
        
        if ($result) {
            wp_send_json_success(array('message' => __('Discount deleted successfully.', 'hajri-cod-shop')));
        } else {
            wp_send_json_error(array('message' => __('Failed to delete discount.', 'hajri-cod-shop')));
        }
    }

    /**
     * Get all discounts via AJAX.
     *
     * @since    1.0.0
     */
    public function ajax_get_discounts() {
        check_ajax_referer('hajri_admin_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'hajri-cod-shop')));
        }
        
        $discounts = self::get_discounts();
        
        wp_send_json_success(array('discounts' => $discounts));
    }

    /**
     * Create a new discount.
     *
     * @since    1.0.0
     * @param    string $name Discount name.
     * @param    string $type Discount type.
     * @param    float $value Discount value.
     * @param    int|null $product_id Product ID.
     * @param    string|null $city City name.
     * @param    string|null $start_date Start date.
     * @param    string|null $end_date End date.
     * @param    bool $is_active Whether the discount is active.
     * @return   int|bool The discount ID on success, false on failure.
     */
    public static function create_discount($name, $type, $value, $product_id = null, $city = null, $start_date = null, $end_date = null, $is_active = true) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'hajri_discounts';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'discount_name' => $name,
                'discount_type' => $type,
                'discount_value' => $value,
                'product_id' => $product_id,
                'city_name' => $city,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'is_active' => $is_active ? 1 : 0,
            ),
            array('%s', '%s', '%f', '%d', '%s', '%s', '%s', '%d')
        );
        
        if ($result) {
            return $wpdb->insert_id;
        }
        
        return false;
    }

    /**
     * Update an existing discount.
     *
     * @since    1.0.0
     * @param    int $id Discount ID.
     * @param    string $name Discount name.
     * @param    string $type Discount type.
     * @param    float $value Discount value.
     * @param    int|null $product_id Product ID.
     * @param    string|null $city City name.
     * @param    string|null $start_date Start date.
     * @param    string|null $end_date End date.
     * @param    bool $is_active Whether the discount is active.
     * @return   bool Whether the update was successful.
     */
    public static function update_discount($id, $name, $type, $value, $product_id = null, $city = null, $start_date = null, $end_date = null, $is_active = true) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'hajri_discounts';
        
        $result = $wpdb->update(
            $table_name,
            array(
                'discount_name' => $name,
                'discount_type' => $type,
                'discount_value' => $value,
                'product_id' => $product_id,
                'city_name' => $city,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'is_active' => $is_active ? 1 : 0,
            ),
            array('id' => $id),
            array('%s', '%s', '%f', '%d', '%s', '%s', '%s', '%d'),
            array('%d')
        );
        
        return ($result !== false);
    }

    /**
     * Delete a discount.
     *
     * @since    1.0.0
     * @param    int $id Discount ID.
     * @return   bool Whether the deletion was successful.
     */
    public static function delete_discount($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'hajri_discounts';
        
        $result = $wpdb->delete(
            $table_name,
            array('id' => $id),
            array('%d')
        );
        
        return ($result !== false);
    }

    /**
     * Get all discounts.
     *
     * @since    1.0.0
     * @return   array List of discounts.
     */
    public static function get_discounts() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'hajri_discounts';
        
        $results = $wpdb->get_results(
            "SELECT * FROM $table_name ORDER BY id DESC",
            ARRAY_A
        );
        
        return $results ? $results : array();
    }

    /**
     * Apply discounts to an order.
     *
     * @since    1.0.0
     * @param    int $product_id Product ID.
     * @param    int $quantity Product quantity.
     * @param    string $city City name.
     * @param    float $order_total Order total.
     * @return   array Discount information.
     */
    public static function apply_discounts($product_id, $quantity, $city, $order_total) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'hajri_discounts';
        
        // Get current date in MySQL format
        $current_date = current_time('mysql');
        
        // Get all active discounts for the product and city
        $discounts = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table_name 
                WHERE is_active = 1 
                AND (product_id IS NULL OR product_id = %d)
                AND (city_name IS NULL OR city_name = %s)
                AND (start_date IS NULL OR start_date <= %s)
                AND (end_date IS NULL OR end_date >= %s)",
                $product_id, $city, $current_date, $current_date
            ),
            ARRAY_A
        );
        
        if (empty($discounts)) {
            return array(
                'discount_amount' => 0,
                'discounts_applied' => array()
            );
        }
        
        $total_discount = 0;
        $applied_discounts = array();
        
        foreach ($discounts as $discount) {
            $discount_type = $discount['discount_type'];
            $discount_value = (float)$discount['discount_value'];
            $discount_amount = 0;
            
            switch ($discount_type) {
                case 'percentage':
                    $discount_amount = $order_total * ($discount_value / 100);
                    break;
                
                case 'fixed':
                    $discount_amount = min($discount_value, $order_total);
                    break;
                
                case 'buy_one_get_one':
                    // If quantity >= 2, give discount for each pair
                    if ($quantity >= 2) {
                        $pairs = floor($quantity / 2);
                        $product_price = $order_total / $quantity;
                        $discount_amount = $pairs * $product_price * ($discount_value / 100);
                    }
                    break;
                
                case 'city_based':
                    if ($discount['city_name'] === $city) {
                        $discount_amount = $order_total * ($discount_value / 100);
                    }
                    break;
            }
            
            if ($discount_amount > 0) {
                $total_discount += $discount_amount;
                $applied_discounts[] = array(
                    'id' => $discount['id'],
                    'name' => $discount['discount_name'],
                    'type' => $discount_type,
                    'value' => $discount_value,
                    'amount' => $discount_amount,
                );
            }
        }
        
        // Make sure discount doesn't exceed order total
        $total_discount = min($total_discount, $order_total);
        
        return array(
            'discount_amount' => $total_discount,
            'discounts_applied' => $applied_discounts
        );
    }
}

new Hajri_Cod_Shop_Discounts();
