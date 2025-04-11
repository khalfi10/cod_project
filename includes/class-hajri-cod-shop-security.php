<?php
/**
 * The security-related functionality of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/includes
 */

/**
 * The security-related functionality of the plugin.
 *
 * Handles security measures like IP blocking, captcha validation, and other security features.
 *
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/includes
 * @author     Your Name <email@example.com>
 */
class Hajri_Cod_Shop_Security {

    /**
     * Get the client IP address.
     *
     * @since    1.0.0
     * @return   string    The client IP address.
     */
    public static function get_client_ip() {
        // Check for shared internet/ISP IP
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        // Check for IP forwarded from proxy
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        // Use standard server remote address
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return sanitize_text_field($ip);
    }

    /**
     * Check if an IP is blocked.
     *
     * @since    1.0.0
     * @param    string    $ip    The IP address to check.
     * @return   boolean   True if blocked, false otherwise.
     */
    public static function is_ip_blocked($ip = '') {
        global $wpdb;
        
        if (empty($ip)) {
            $ip = self::get_client_ip();
        }
        
        $blocked_ips_table = $wpdb->prefix . 'hajri_blocked_ips';
        
        $result = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $blocked_ips_table WHERE ip_address = %s",
                $ip
            )
        );
        
        return $result > 0;
    }

    /**
     * Block an IP address.
     *
     * @since    1.0.0
     * @param    string    $ip       The IP address to block.
     * @param    string    $reason   The reason for blocking this IP.
     * @return   boolean   True if successfully blocked, false otherwise.
     */
    public static function block_ip($ip, $reason = '') {
        global $wpdb;
        
        $blocked_ips_table = $wpdb->prefix . 'hajri_blocked_ips';
        
        $result = $wpdb->insert(
            $blocked_ips_table,
            array(
                'ip_address' => $ip,
                'reason' => $reason,
                'created_at' => current_time('mysql')
            ),
            array('%s', '%s', '%s')
        );
        
        return $result !== false;
    }

    /**
     * Unblock an IP address.
     *
     * @since    1.0.0
     * @param    int       $id    The ID of the blocked IP record.
     * @return   boolean   True if successfully unblocked, false otherwise.
     */
    public static function unblock_ip($id) {
        global $wpdb;
        
        $blocked_ips_table = $wpdb->prefix . 'hajri_blocked_ips';
        
        $result = $wpdb->delete(
            $blocked_ips_table,
            array('id' => $id),
            array('%d')
        );
        
        return $result !== false;
    }

    /**
     * Get all blocked IPs.
     *
     * @since    1.0.0
     * @return   array    An array of blocked IP records.
     */
    public static function get_blocked_ips() {
        global $wpdb;
        
        $blocked_ips_table = $wpdb->prefix . 'hajri_blocked_ips';
        
        $results = $wpdb->get_results(
            "SELECT * FROM $blocked_ips_table ORDER BY created_at DESC",
            ARRAY_A
        );
        
        return $results;
    }

    /**
     * Verify reCAPTCHA token.
     *
     * @since    1.0.0
     * @param    string    $token    The reCAPTCHA token to verify.
     * @param    string    $action   The action to verify.
     * @return   boolean   True if verification is successful, false otherwise.
     */
    public static function verify_recaptcha($token, $action = 'submit_order') {
        $settings = get_option('hajri_cod_shop_settings', array());
        
        if (!isset($settings['recaptcha_enabled']) || !$settings['recaptcha_enabled'] || empty($settings['recaptcha_secret_key'])) {
            return true; // If reCAPTCHA is not enabled, return true
        }
        
        $secret_key = $settings['recaptcha_secret_key'];
        
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = array(
            'secret' => $secret_key,
            'response' => $token
        );
        
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $result = json_decode($response, true);
        
        if (isset($result['success']) && $result['success'] === true) {
            // If action is specified, verify it
            if (!empty($action) && isset($result['action']) && $result['action'] !== $action) {
                return false;
            }
            
            // Check score if it exists (reCAPTCHA v3)
            if (isset($result['score']) && $result['score'] < 0.5) {
                return false;
            }
            
            return true;
        }
        
        return false;
    }

    /**
     * Check if a phone number has placed an order in the last X days.
     *
     * @since    1.0.0
     * @param    string    $phone_number    The phone number to check.
     * @param    int       $days            The number of days to check.
     * @return   boolean   True if the phone number has placed an order, false otherwise.
     */
    public static function has_recent_order($phone_number, $days = 7) {
        global $wpdb;
        
        $orders_table = $wpdb->prefix . 'hajri_orders';
        
        $result = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $orders_table 
                WHERE phone_number = %s 
                AND created_at >= DATE_SUB(NOW(), INTERVAL %d DAY)",
                $phone_number,
                $days
            )
        );
        
        return $result > 0;
    }
}