<?php
/**
 * Order-related functionality.
 *
 * @link       https://example.com
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/includes
 */

/**
 * Order-related functionality.
 *
 * Defines the functionality related to orders in the shop.
 *
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/includes
 * @author     Your Name <email@example.com>
 */
class Hajri_Cod_Shop_Order {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        add_action('wp_ajax_hajri_submit_order', array($this, 'ajax_submit_order'));
        add_action('wp_ajax_nopriv_hajri_submit_order', array($this, 'ajax_submit_order'));
    }

    /**
     * Submit an order via AJAX.
     *
     * @since    1.0.0
     */
    public function ajax_submit_order() {
        check_ajax_referer('hajri_order_nonce', 'security');

        $response = array(
            'success' => false,
            'message' => '',
        );

        // Validate inputs
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        $city = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
        $address = isset($_POST['address']) ? sanitize_textarea_field($_POST['address']) : '';
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

        // Validate name
        if (empty($name)) {
            $response['message'] = __('Please enter your name.', 'hajri-cod-shop');
            wp_send_json($response);
        }

        // Validate phone (Algerian phone numbers)
        if (empty($phone) || !$this->is_valid_algerian_phone($phone)) {
            $response['message'] = __('Please enter a valid Algerian phone number (starting with 05, 06, or 07).', 'hajri-cod-shop');
            wp_send_json($response);
        }

        // Check if the phone number has been used in the past 7 days
        if ($this->is_phone_blocked($phone)) {
            $response['message'] = __('This phone number has been used for an order in the past 7 days. Please try again later.', 'hajri-cod-shop');
            wp_send_json($response);
        }

        // Validate city
        if (empty($city)) {
            $response['message'] = __('Please select your city.', 'hajri-cod-shop');
            wp_send_json($response);
        }

        // Validate address
        if (empty($address)) {
            $response['message'] = __('Please enter your delivery address.', 'hajri-cod-shop');
            wp_send_json($response);
        }

        // Validate product
        $product = Hajri_Cod_Shop_Product::get_product($product_id);
        if (!$product) {
            $response['message'] = __('Invalid product selected.', 'hajri-cod-shop');
            wp_send_json($response);
        }

        // Check if product is in stock
        if (!$product['in_stock']) {
            $response['message'] = __('Sorry, this product is out of stock.', 'hajri-cod-shop');
            wp_send_json($response);
        }

        // Get shipping cost for the city
        $shipping_cost = Hajri_Cod_Shop_Shipping::get_shipping_cost($city);

        // Calculate order total
        $product_total = $product['current_price'] * $quantity;
        $order_total = $product_total + $shipping_cost;

        // Apply any discounts
        $discount_info = Hajri_Cod_Shop_Discounts::apply_discounts($product_id, $quantity, $city, $order_total);
        $discount_amount = $discount_info['discount_amount'];
        $final_total = $order_total - $discount_amount;

        // Create the order in the database
        global $wpdb;
        $order_table = $wpdb->prefix . 'hajri_orders';
        
        $products_json = json_encode(array(
            array(
                'id' => $product_id,
                'name' => $product['title'],
                'price' => $product['current_price'],
                'quantity' => $quantity,
                'subtotal' => $product_total,
            )
        ));

        $result = $wpdb->insert(
            $order_table,
            array(
                'customer_name' => $name,
                'phone_number' => $phone,
                'city' => $city,
                'address' => $address,
                'products' => $products_json,
                'total_amount' => $final_total,
                'shipping_cost' => $shipping_cost,
                'discount_applied' => $discount_amount,
                'status' => 'pending',
                'ip_address' => $this->get_client_ip(),
                'created_at' => current_time('mysql'),
                'notes' => isset($_POST['notes']) ? sanitize_textarea_field($_POST['notes']) : '',
            ),
            array(
                '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%f', '%s', '%s', '%s', '%s'
            )
        );

        if ($result) {
            $order_id = $wpdb->insert_id;
            
            // Update product stock
            $new_stock = $product['stock'] - $quantity;
            update_post_meta($product_id, '_hajri_product_stock', max(0, $new_stock));
            
            // Send data to Google Sheets if enabled
            $settings = get_option('hajri_cod_shop_settings', array());
            if (isset($settings['google_sheets_enabled']) && $settings['google_sheets_enabled']) {
                Hajri_Cod_Shop_Google_Sheets::add_order_to_sheet($order_id, $name, $phone, $city, $address, $product['title'], $quantity, $product_total, $shipping_cost, $discount_amount, $final_total);
            }
            
            // Clear abandoned cart if it exists
            $this->clear_abandoned_cart();
            
            $response['success'] = true;
            $response['message'] = __('Your order has been successfully submitted! We will contact you soon.', 'hajri-cod-shop');
            $response['order_id'] = $order_id;
        } else {
            $response['message'] = __('There was an error processing your order. Please try again.', 'hajri-cod-shop');
        }

        wp_send_json($response);
    }

    /**
     * Check if a phone number is a valid Algerian phone number.
     *
     * @since    1.0.0
     * @param    string $phone The phone number to validate.
     * @return   bool Whether the phone number is valid.
     */
    public function is_valid_algerian_phone($phone) {
        // Remove any spaces or special characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Check if it starts with 05, 06, or 07 and has 10 digits
        if (preg_match('/^(05|06|07)[0-9]{8}$/', $phone)) {
            return true;
        }
        
        // Check if it starts with +213 and then 5, 6, or 7
        if (preg_match('/^00213[567][0-9]{8}$/', $phone)) {
            return true;
        }
        
        // Check if it starts with 213 and then 5, 6, or 7
        if (preg_match('/^213[567][0-9]{8}$/', $phone)) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if a phone number has been used for an order in the past N days.
     *
     * @since    1.0.0
     * @param    string $phone The phone number to check.
     * @return   bool Whether the phone number is blocked.
     */
    public function is_phone_blocked($phone) {
        global $wpdb;
        $settings = get_option('hajri_cod_shop_settings', array());
        $block_days = isset($settings['order_block_days']) ? intval($settings['order_block_days']) : 7;
        
        $order_table = $wpdb->prefix . 'hajri_orders';
        
        // Format phone number to handle different formats
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Get orders with this phone in the past N days
        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM $order_table 
            WHERE phone_number LIKE %s 
            AND created_at >= DATE_SUB(NOW(), INTERVAL %d DAY)",
            '%' . $wpdb->esc_like($phone) . '%',
            $block_days
        );
        
        $count = $wpdb->get_var($query);
        
        return ($count > 0);
    }

    /**
     * Get the client's IP address.
     *
     * @since    1.0.0
     * @return   string The client's IP address.
     */
    public function get_client_ip() {
        $ip_keys = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        );
        
        foreach ($ip_keys as $key) {
            if (isset($_SERVER[$key]) && filter_var($_SERVER[$key], FILTER_VALIDATE_IP)) {
                return $_SERVER[$key];
            }
        }
        
        return '127.0.0.1'; // Default to localhost if no valid IP is found
    }

    /**
     * Clear abandoned cart for the current session/user.
     *
     * @since    1.0.0
     */
    public function clear_abandoned_cart() {
        global $wpdb;
        $ip_address = $this->get_client_ip();
        $abandoned_cart_table = $wpdb->prefix . 'hajri_abandoned_carts';
        
        $wpdb->delete(
            $abandoned_cart_table,
            array('ip_address' => $ip_address, 'is_converted' => 0),
            array('%s', '%d')
        );
    }

    /**
     * Get an order by ID.
     *
     * @since    1.0.0
     * @param    int $order_id The order ID.
     * @return   array|bool The order data or false if not found.
     */
    public static function get_order($order_id) {
        global $wpdb;
        $order_table = $wpdb->prefix . 'hajri_orders';
        
        $order = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $order_table WHERE id = %d", $order_id),
            ARRAY_A
        );
        
        if (!$order) {
            return false;
        }
        
        // Decode the products JSON
        $order['products'] = json_decode($order['products'], true);
        
        return $order;
    }

    /**
     * Get orders with optional filtering.
     *
     * @since    1.0.0
     * @param    array $args Query arguments.
     * @return   array The orders.
     */
    public static function get_orders($args = array()) {
        global $wpdb;
        $order_table = $wpdb->prefix . 'hajri_orders';
        
        $defaults = array(
            'limit' => 20,
            'offset' => 0,
            'orderby' => 'created_at',
            'order' => 'DESC',
            'status' => '',
            'search' => '',
            'date_from' => '',
            'date_to' => '',
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = "1=1";
        $values = array();
        
        if (!empty($args['status'])) {
            $where .= " AND status = %s";
            $values[] = $args['status'];
        }
        
        if (!empty($args['search'])) {
            $where .= " AND (customer_name LIKE %s OR phone_number LIKE %s OR address LIKE %s)";
            $search_term = '%' . $wpdb->esc_like($args['search']) . '%';
            $values[] = $search_term;
            $values[] = $search_term;
            $values[] = $search_term;
        }
        
        if (!empty($args['date_from'])) {
            $where .= " AND created_at >= %s";
            $values[] = $args['date_from'] . ' 00:00:00';
        }
        
        if (!empty($args['date_to'])) {
            $where .= " AND created_at <= %s";
            $values[] = $args['date_to'] . ' 23:59:59';
        }
        
        $orderby = sanitize_sql_orderby($args['orderby'] . ' ' . $args['order']);
        
        $limit = intval($args['limit']);
        $offset = intval($args['offset']);
        
        $query = "SELECT * FROM $order_table WHERE $where ORDER BY $orderby LIMIT $offset, $limit";
        
        if (!empty($values)) {
            $query = $wpdb->prepare($query, $values);
        }
        
        $orders = $wpdb->get_results($query, ARRAY_A);
        
        // Decode the products JSON for each order
        foreach ($orders as &$order) {
            $order['products'] = json_decode($order['products'], true);
        }
        
        return $orders;
    }

    /**
     * Get the total count of orders with optional filtering.
     *
     * @since    1.0.0
     * @param    array $args Query arguments.
     * @return   int The total count.
     */
    public static function get_orders_count($args = array()) {
        global $wpdb;
        $order_table = $wpdb->prefix . 'hajri_orders';
        
        $defaults = array(
            'status' => '',
            'search' => '',
            'date_from' => '',
            'date_to' => '',
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = "1=1";
        $values = array();
        
        if (!empty($args['status'])) {
            $where .= " AND status = %s";
            $values[] = $args['status'];
        }
        
        if (!empty($args['search'])) {
            $where .= " AND (customer_name LIKE %s OR phone_number LIKE %s OR address LIKE %s)";
            $search_term = '%' . $wpdb->esc_like($args['search']) . '%';
            $values[] = $search_term;
            $values[] = $search_term;
            $values[] = $search_term;
        }
        
        if (!empty($args['date_from'])) {
            $where .= " AND created_at >= %s";
            $values[] = $args['date_from'] . ' 00:00:00';
        }
        
        if (!empty($args['date_to'])) {
            $where .= " AND created_at <= %s";
            $values[] = $args['date_to'] . ' 23:59:59';
        }
        
        $query = "SELECT COUNT(*) FROM $order_table WHERE $where";
        
        if (!empty($values)) {
            $query = $wpdb->prepare($query, $values);
        }
        
        return $wpdb->get_var($query);
    }

    /**
     * Update order status.
     *
     * @since    1.0.0
     * @param    int $order_id The order ID.
     * @param    string $status The new status.
     * @return   bool Whether the update was successful.
     */
    public static function update_order_status($order_id, $status) {
        global $wpdb;
        $order_table = $wpdb->prefix . 'hajri_orders';
        
        $result = $wpdb->update(
            $order_table,
            array(
                'status' => $status,
                'updated_at' => current_time('mysql'),
            ),
            array('id' => $order_id),
            array('%s', '%s'),
            array('%d')
        );
        
        return ($result !== false);
    }

    /**
     * Get order statistics for analytics.
     *
     * @since    1.0.0
     * @param    string $period The period ('day', 'week', 'month', 'year').
     * @return   array The statistics.
     */
    public static function get_order_stats($period = 'month') {
        global $wpdb;
        $order_table = $wpdb->prefix . 'hajri_orders';
        
        switch ($period) {
            case 'day':
                $date_format = '%Y-%m-%d';
                $date_interval = '30 DAY';
                break;
            case 'week':
                $date_format = '%Y-%u';
                $date_interval = '52 WEEK';
                break;
            case 'year':
                $date_format = '%Y-%m';
                $date_interval = '12 MONTH';
                break;
            case 'month':
            default:
                $date_format = '%Y-%m';
                $date_interval = '12 MONTH';
                break;
        }
        
        // Orders by date
        $orders_by_date = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT 
                    DATE_FORMAT(created_at, %s) as date_group,
                    COUNT(*) as count,
                    SUM(total_amount) as total
                FROM 
                    $order_table
                WHERE 
                    created_at >= DATE_SUB(NOW(), INTERVAL $date_interval)
                GROUP BY 
                    date_group
                ORDER BY 
                    date_group ASC",
                $date_format
            ),
            ARRAY_A
        );
        
        // Top selling products
        $top_products_query = "
            SELECT 
                p.product_id,
                p.product_name,
                SUM(p.quantity) as total_quantity,
                SUM(p.price * p.quantity) as total_sales
            FROM (
                SELECT 
                    o.id,
                    JSON_EXTRACT(o.products, '$[*].id') as product_ids,
                    JSON_EXTRACT(o.products, '$[*].name') as product_names,
                    JSON_EXTRACT(o.products, '$[*].price') as product_prices,
                    JSON_EXTRACT(o.products, '$[*].quantity') as product_quantities
                FROM 
                    $order_table o
                WHERE 
                    o.created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            ) AS orders
            CROSS JOIN JSON_TABLE(
                orders.product_ids,
                '$[*]' COLUMNS (
                    idx FOR ORDINALITY,
                    product_id INT PATH '$'
                )
            ) AS product_ids
            CROSS JOIN JSON_TABLE(
                orders.product_names,
                '$[*]' COLUMNS (
                    idx FOR ORDINALITY,
                    product_name VARCHAR(255) PATH '$'
                )
            ) AS product_names
            CROSS JOIN JSON_TABLE(
                orders.product_prices,
                '$[*]' COLUMNS (
                    idx FOR ORDINALITY,
                    price DECIMAL(10,2) PATH '$'
                )
            ) AS product_prices
            CROSS JOIN JSON_TABLE(
                orders.product_quantities,
                '$[*]' COLUMNS (
                    idx FOR ORDINALITY,
                    quantity INT PATH '$'
                )
            ) AS p
            WHERE 
                product_ids.idx = product_names.idx
                AND product_ids.idx = product_prices.idx
                AND product_ids.idx = p.idx
            GROUP BY 
                p.product_id, p.product_name
            ORDER BY 
                total_quantity DESC
            LIMIT 10
        ";
        
        // This is a complex query that might not work on all MySQL versions
        // Fallback to a simpler approach if JSON functions aren't available
        $top_products = array();
        try {
            $top_products = $wpdb->get_results($top_products_query, ARRAY_A);
        } catch (Exception $e) {
            // Fallback - process in PHP
            $recent_orders = $wpdb->get_results(
                "SELECT products FROM $order_table 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)",
                ARRAY_A
            );
            
            $product_counts = array();
            foreach ($recent_orders as $order) {
                $products = json_decode($order['products'], true);
                if (is_array($products)) {
                    foreach ($products as $product) {
                        $id = $product['id'];
                        if (!isset($product_counts[$id])) {
                            $product_counts[$id] = array(
                                'product_id' => $id,
                                'product_name' => $product['name'],
                                'total_quantity' => 0,
                                'total_sales' => 0
                            );
                        }
                        $product_counts[$id]['total_quantity'] += $product['quantity'];
                        $product_counts[$id]['total_sales'] += ($product['price'] * $product['quantity']);
                    }
                }
            }
            
            usort($product_counts, function($a, $b) {
                return $b['total_quantity'] - $a['total_quantity'];
            });
            
            $top_products = array_slice($product_counts, 0, 10);
        }
        
        // Orders by city
        $orders_by_city = $wpdb->get_results(
            "SELECT 
                city,
                COUNT(*) as count,
                SUM(total_amount) as total
            FROM 
                $order_table
            WHERE 
                created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY 
                city
            ORDER BY 
                count DESC
            LIMIT 10",
            ARRAY_A
        );
        
        // Order status counts
        $status_counts = $wpdb->get_results(
            "SELECT 
                status,
                COUNT(*) as count
            FROM 
                $order_table
            GROUP BY 
                status",
            ARRAY_A
        );
        
        // Calculate conversion rate from abandoned carts
        $abandoned_table = $wpdb->prefix . 'hajri_abandoned_carts';
        $abandoned_count = $wpdb->get_var(
            "SELECT COUNT(*) FROM $abandoned_table 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
        );
        
        $orders_count = $wpdb->get_var(
            "SELECT COUNT(*) FROM $order_table 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
        );
        
        $conversion_rate = 0;
        if ($abandoned_count > 0) {
            $conversion_rate = ($orders_count / ($abandoned_count + $orders_count)) * 100;
        }
        
        return array(
            'orders_by_date' => $orders_by_date,
            'top_products' => $top_products,
            'orders_by_city' => $orders_by_city,
            'status_counts' => $status_counts,
            'conversion_rate' => $conversion_rate,
            'total_orders' => $orders_count,
            'total_abandoned' => $abandoned_count,
        );
    }
}

new Hajri_Cod_Shop_Order();
