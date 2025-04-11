<?php
/**
 * Algerian delivery companies management functionality.
 *
 * @link       https://example.com
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/includes
 */

/**
 * Algerian delivery companies management functionality.
 *
 * This class handles all delivery company-related operations.
 *
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/includes
 * @author     Your Name <email@example.com>
 */
class Hajri_Cod_Shop_Delivery_Companies {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        add_action('wp_ajax_create_delivery_company', array($this, 'ajax_create_delivery_company'));
        add_action('wp_ajax_update_delivery_company', array($this, 'ajax_update_delivery_company'));
        add_action('wp_ajax_delete_delivery_company', array($this, 'ajax_delete_delivery_company'));
        add_action('wp_ajax_get_delivery_companies', array($this, 'ajax_get_delivery_companies'));
        add_action('wp_ajax_get_delivery_company', array($this, 'ajax_get_delivery_company'));
        add_action('wp_ajax_toggle_delivery_company_status', array($this, 'ajax_toggle_delivery_company_status'));
        add_action('wp_ajax_test_delivery_company_api', array($this, 'ajax_test_delivery_company_api'));
    }

    /**
     * Create a new delivery company via AJAX.
     *
     * @since    1.0.0
     */
    public function ajax_create_delivery_company() {
        check_ajax_referer('hajri_admin_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'hajri-cod-shop')));
        }
        
        // Collect and sanitize all possible fields
        $data = array(
            'company_name' => isset($_POST['company_name']) ? sanitize_text_field($_POST['company_name']) : '',
            'company_code' => isset($_POST['company_code']) ? sanitize_text_field($_POST['company_code']) : '',
            'api_base_url' => isset($_POST['api_base_url']) ? esc_url_raw($_POST['api_base_url']) : '',
            'api_id' => isset($_POST['api_id']) ? sanitize_text_field($_POST['api_id']) : '',
            'api_key' => isset($_POST['api_key']) ? sanitize_text_field($_POST['api_key']) : '',
            'api_secret' => isset($_POST['api_secret']) ? sanitize_text_field($_POST['api_secret']) : '',
            'token' => isset($_POST['token']) ? sanitize_text_field($_POST['token']) : '',
            'username' => isset($_POST['username']) ? sanitize_text_field($_POST['username']) : '',
            'password' => isset($_POST['password']) ? sanitize_text_field($_POST['password']) : '',
            'auth_type' => isset($_POST['auth_type']) ? sanitize_text_field($_POST['auth_type']) : '',
            'description' => isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '',
            'webhook_url' => isset($_POST['webhook_url']) ? esc_url_raw($_POST['webhook_url']) : '',
            'api_version' => isset($_POST['api_version']) ? sanitize_text_field($_POST['api_version']) : '',
            'is_active' => isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1
        );
        
        // Process endpoints if provided
        if (isset($_POST['endpoints']) && is_array($_POST['endpoints'])) {
            $endpoints = array();
            foreach ($_POST['endpoints'] as $key => $value) {
                $endpoints[sanitize_text_field($key)] = sanitize_text_field($value);
            }
            $data['endpoints'] = $endpoints;
        }
        
        // Process config data if provided
        if (isset($_POST['config_data']) && is_array($_POST['config_data'])) {
            $config_data = array();
            foreach ($_POST['config_data'] as $key => $value) {
                $config_data[sanitize_text_field($key)] = sanitize_text_field($value);
            }
            $data['config_data'] = $config_data;
        }
        
        // Validate required inputs
        if (empty($data['company_name'])) {
            wp_send_json_error(array('message' => __('Company name is required.', 'hajri-cod-shop')));
        }
        
        if (empty($data['company_code'])) {
            // Generate company code from name if not provided
            $data['company_code'] = sanitize_title($data['company_name']);
        }
        
        $result = self::create_delivery_company($data);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('Delivery company created successfully.', 'hajri-cod-shop'),
                'company_id' => $result
            ));
        } else {
            wp_send_json_error(array('message' => __('Failed to create delivery company.', 'hajri-cod-shop')));
        }
    }

    /**
     * Update an existing delivery company via AJAX.
     *
     * @since    1.0.0
     */
    public function ajax_update_delivery_company() {
        check_ajax_referer('hajri_admin_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'hajri-cod-shop')));
        }
        
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        // Validate ID
        if ($id <= 0) {
            wp_send_json_error(array('message' => __('Invalid company ID.', 'hajri-cod-shop')));
        }
        
        // Collect and sanitize all possible fields
        $data = array(
            'company_name' => isset($_POST['company_name']) ? sanitize_text_field($_POST['company_name']) : '',
            'company_code' => isset($_POST['company_code']) ? sanitize_text_field($_POST['company_code']) : '',
            'api_base_url' => isset($_POST['api_base_url']) ? esc_url_raw($_POST['api_base_url']) : '',
            'api_id' => isset($_POST['api_id']) ? sanitize_text_field($_POST['api_id']) : '',
            'api_key' => isset($_POST['api_key']) ? sanitize_text_field($_POST['api_key']) : '',
            'api_secret' => isset($_POST['api_secret']) ? sanitize_text_field($_POST['api_secret']) : '',
            'token' => isset($_POST['token']) ? sanitize_text_field($_POST['token']) : '',
            'username' => isset($_POST['username']) ? sanitize_text_field($_POST['username']) : '',
            'password' => isset($_POST['password']) ? sanitize_text_field($_POST['password']) : '',
            'auth_type' => isset($_POST['auth_type']) ? sanitize_text_field($_POST['auth_type']) : '',
            'description' => isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '',
            'webhook_url' => isset($_POST['webhook_url']) ? esc_url_raw($_POST['webhook_url']) : '',
            'api_version' => isset($_POST['api_version']) ? sanitize_text_field($_POST['api_version']) : '',
            'is_active' => isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1
        );
        
        // Process endpoints if provided
        if (isset($_POST['endpoints']) && is_array($_POST['endpoints'])) {
            $endpoints = array();
            foreach ($_POST['endpoints'] as $key => $value) {
                $endpoints[sanitize_text_field($key)] = sanitize_text_field($value);
            }
            $data['endpoints'] = $endpoints;
        }
        
        // Process config data if provided
        if (isset($_POST['config_data']) && is_array($_POST['config_data'])) {
            $config_data = array();
            foreach ($_POST['config_data'] as $key => $value) {
                $config_data[sanitize_text_field($key)] = sanitize_text_field($value);
            }
            $data['config_data'] = $config_data;
        }
        
        // Validate required inputs
        if (empty($data['company_name'])) {
            wp_send_json_error(array('message' => __('Company name is required.', 'hajri-cod-shop')));
        }
        
        // Only keep non-empty fields to avoid overwriting existing values with empty ones
        $data = array_filter($data, function($value) {
            return $value !== '' && $value !== null;
        });
        
        $result = self::update_delivery_company($id, $data);
        
        if ($result) {
            wp_send_json_success(array('message' => __('Delivery company updated successfully.', 'hajri-cod-shop')));
        } else {
            wp_send_json_error(array('message' => __('Failed to update delivery company.', 'hajri-cod-shop')));
        }
    }

    /**
     * Delete a delivery company via AJAX.
     *
     * @since    1.0.0
     */
    public function ajax_delete_delivery_company() {
        check_ajax_referer('hajri_admin_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'hajri-cod-shop')));
        }
        
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if ($id <= 0) {
            wp_send_json_error(array('message' => __('Invalid company ID.', 'hajri-cod-shop')));
        }
        
        $result = self::delete_delivery_company($id);
        
        if ($result) {
            wp_send_json_success(array('message' => __('Delivery company deleted successfully.', 'hajri-cod-shop')));
        } else {
            wp_send_json_error(array('message' => __('Failed to delete delivery company.', 'hajri-cod-shop')));
        }
    }

    /**
     * Get all delivery companies via AJAX.
     *
     * @since    1.0.0
     */
    public function ajax_get_delivery_companies() {
        check_ajax_referer('hajri_admin_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'hajri-cod-shop')));
        }
        
        $companies = self::get_delivery_companies();
        
        wp_send_json_success(array('companies' => $companies));
    }

    /**
     * Create a new delivery company.
     *
     * @since    1.0.0
     * @param    array     $data            Company data associative array.
     * @return   int|bool  The company ID on success, false on failure.
     */
    public static function create_delivery_company($data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'hajri_delivery_companies';
        
        // Required fields check
        if (empty($data['company_name']) || empty($data['company_code'])) {
            return false;
        }
        
        // Set default values for optional fields
        $defaults = array(
            'api_base_url' => null,
            'api_id' => null,
            'api_key' => null,
            'api_secret' => null,
            'token' => null,
            'username' => null,
            'password' => null,
            'auth_type' => null,
            'endpoints' => null,
            'is_active' => 1,
            'description' => null,
            'config_data' => null,
            'webhook_url' => null,
            'api_version' => null
        );
        
        $data = wp_parse_args($data, $defaults);
        
        // Format endpoints if it's an array
        if (isset($data['endpoints']) && is_array($data['endpoints'])) {
            $data['endpoints'] = json_encode($data['endpoints']);
        }
        
        // Format config_data if it's an array
        if (isset($data['config_data']) && is_array($data['config_data'])) {
            $data['config_data'] = json_encode($data['config_data']);
        }
        
        // Add timestamps
        $data['created_at'] = current_time('mysql');
        $data['updated_at'] = current_time('mysql');
        
        // Set format specifiers based on data
        $formats = array();
        foreach ($data as $key => $value) {
            if (is_null($value)) {
                $formats[] = '%s';
            } elseif (is_int($value) || is_bool($value)) {
                $formats[] = '%d';
            } elseif (is_float($value)) {
                $formats[] = '%f';
            } else {
                $formats[] = '%s';
            }
        }
        
        $result = $wpdb->insert($table_name, $data, $formats);
        
        if ($result) {
            return $wpdb->insert_id;
        }
        
        return false;
    }

    /**
     * Update an existing delivery company.
     *
     * @since    1.0.0
     * @param    int       $id              Company ID.
     * @param    array     $data            Company data associative array.
     * @return   bool      Whether the update was successful.
     */
    public static function update_delivery_company($id, $data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'hajri_delivery_companies';
        
        // Check if company exists
        $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_name WHERE id = %d", $id));
        if (!$exists) {
            return false;
        }
        
        // Format endpoints if it's an array
        if (isset($data['endpoints']) && is_array($data['endpoints'])) {
            $data['endpoints'] = json_encode($data['endpoints']);
        }
        
        // Format config_data if it's an array
        if (isset($data['config_data']) && is_array($data['config_data'])) {
            $data['config_data'] = json_encode($data['config_data']);
        }
        
        // Update timestamp
        $data['updated_at'] = current_time('mysql');
        
        // Set format specifiers based on data
        $formats = array();
        foreach ($data as $key => $value) {
            if (is_null($value)) {
                $formats[] = '%s';
            } elseif (is_int($value) || is_bool($value)) {
                $formats[] = '%d';
            } elseif (is_float($value)) {
                $formats[] = '%f';
            } else {
                $formats[] = '%s';
            }
        }
        
        $result = $wpdb->update($table_name, $data, array('id' => $id), $formats, array('%d'));
        
        return ($result !== false);
    }

    /**
     * Delete a delivery company.
     *
     * @since    1.0.0
     * @param    int       $id    Company ID.
     * @return   bool      Whether the deletion was successful.
     */
    public static function delete_delivery_company($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'hajri_delivery_companies';
        
        $result = $wpdb->delete(
            $table_name,
            array('id' => $id),
            array('%d')
        );
        
        return ($result !== false);
    }

    /**
     * Get all delivery companies.
     *
     * @since    1.0.0
     * @return   array     List of delivery companies.
     */
    public static function get_delivery_companies() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'hajri_delivery_companies';
        
        $results = $wpdb->get_results(
            "SELECT * FROM $table_name ORDER BY id ASC",
            ARRAY_A
        );
        
        return $results ? $results : array();
    }

    /**
     * Get a specific delivery company.
     *
     * @since    1.0.0
     * @param    int       $id    Company ID.
     * @return   array     Company data.
     */
    public static function get_delivery_company($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'hajri_delivery_companies';
        
        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d",
                $id
            ),
            ARRAY_A
        );
        
        if ($result) {
            // Parse JSON data
            if (!empty($result['endpoints'])) {
                $result['endpoints'] = json_decode($result['endpoints'], true);
            }
            
            if (!empty($result['config_data'])) {
                $result['config_data'] = json_decode($result['config_data'], true);
            }
        }
        
        return $result ? $result : array();
    }
    
    /**
     * Get a delivery company by company code.
     * 
     * @since    1.0.0
     * @param    string    $code    Company code.
     * @return   array     Company data.
     */
    public static function get_delivery_company_by_code($code) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'hajri_delivery_companies';
        
        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE company_code = %s",
                $code
            ),
            ARRAY_A
        );
        
        if ($result) {
            // Parse JSON data
            if (!empty($result['endpoints'])) {
                $result['endpoints'] = json_decode($result['endpoints'], true);
            }
            
            if (!empty($result['config_data'])) {
                $result['config_data'] = json_decode($result['config_data'], true);
            }
        }
        
        return $result ? $result : array();
    }
    
    /**
     * Get a specific delivery company via AJAX.
     *
     * @since    1.0.0
     */
    public function ajax_get_delivery_company() {
        check_ajax_referer('hajri_admin_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'hajri-cod-shop')));
        }
        
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if ($id <= 0) {
            wp_send_json_error(array('message' => __('Invalid company ID.', 'hajri-cod-shop')));
        }
        
        $company = self::get_delivery_company($id);
        
        if (empty($company)) {
            wp_send_json_error(array('message' => __('Delivery company not found.', 'hajri-cod-shop')));
        }
        
        wp_send_json_success(array('company' => $company));
    }
    
    /**
     * Toggle the active status of a delivery company via AJAX.
     * 
     * @since    1.0.0
     */
    public function ajax_toggle_delivery_company_status() {
        check_ajax_referer('hajri_admin_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'hajri-cod-shop')));
        }
        
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if ($id <= 0) {
            wp_send_json_error(array('message' => __('Invalid company ID.', 'hajri-cod-shop')));
        }
        
        // Get current status
        $company = self::get_delivery_company($id);
        if (empty($company)) {
            wp_send_json_error(array('message' => __('Delivery company not found.', 'hajri-cod-shop')));
        }
        
        // Toggle status
        $new_status = $company['is_active'] ? 0 : 1;
        
        $result = self::update_delivery_company($id, array('is_active' => $new_status));
        
        if ($result) {
            $status_text = $new_status ? __('activated', 'hajri-cod-shop') : __('deactivated', 'hajri-cod-shop');
            wp_send_json_success(array(
                'message' => sprintf(__('Delivery company %s successfully.', 'hajri-cod-shop'), $status_text),
                'is_active' => $new_status
            ));
        } else {
            wp_send_json_error(array('message' => __('Failed to update delivery company status.', 'hajri-cod-shop')));
        }
    }
    
    /**
     * Test delivery company API connection via AJAX.
     * 
     * @since    1.0.0
     */
    public function ajax_test_delivery_company_api() {
        check_ajax_referer('hajri_admin_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'hajri-cod-shop')));
        }
        
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if ($id <= 0) {
            wp_send_json_error(array('message' => __('Invalid company ID.', 'hajri-cod-shop')));
        }
        
        // Get company data
        $company = self::get_delivery_company($id);
        if (empty($company)) {
            wp_send_json_error(array('message' => __('Delivery company not found.', 'hajri-cod-shop')));
        }
        
        // Validate required API details depending on auth type
        if (empty($company['auth_type'])) {
            wp_send_json_error(array('message' => __('Authentication type not specified.', 'hajri-cod-shop')));
        }
        
        if (empty($company['api_base_url'])) {
            wp_send_json_error(array('message' => __('API Base URL is required.', 'hajri-cod-shop')));
        }
        
        // Test the API connection based on auth type
        $result = self::test_api_connection($company);
        
        if (isset($result['success']) && $result['success']) {
            wp_send_json_success(array(
                'message' => __('API connection successful!', 'hajri-cod-shop'),
                'details' => $result['details'] ?? null
            ));
        } else {
            wp_send_json_error(array(
                'message' => sprintf(__('API connection failed: %s', 'hajri-cod-shop'), $result['message'] ?? __('Unknown error', 'hajri-cod-shop')),
                'details' => $result['details'] ?? null
            ));
        }
    }
    
    /**
     * Test API connection for a delivery company.
     * 
     * @since    1.0.0
     * @param    array     $company    Company data.
     * @return   array     Result of the API test.
     */
    public static function test_api_connection($company) {
        // Define API request params based on auth type
        $args = array(
            'timeout' => 15,
            'headers' => array(
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ),
        );
        
        // Add authentication headers based on auth type
        switch ($company['auth_type']) {
            case 'api_key':
                if (empty($company['api_key'])) {
                    return array(
                        'success' => false,
                        'message' => __('API Key is required for this authentication type.', 'hajri-cod-shop')
                    );
                }
                
                // Some APIs expect the key in headers, others as URL param
                $args['headers']['X-API-Key'] = $company['api_key'];
                $test_url = $company['api_base_url'];
                
                // Add endpoint for testing if available
                if (!empty($company['endpoints']) && is_array($company['endpoints'])) {
                    $test_endpoints = array('wilaya', 'get_wilaya', 'get_cities', 'test', 'ping', 'status');
                    foreach ($test_endpoints as $endpoint) {
                        if (isset($company['endpoints'][$endpoint])) {
                            $test_url = rtrim($company['api_base_url'], '/') . '/' . $company['endpoints'][$endpoint];
                            break;
                        }
                    }
                }
                break;
                
            case 'token':
                if (empty($company['token'])) {
                    return array(
                        'success' => false,
                        'message' => __('Token is required for this authentication type.', 'hajri-cod-shop')
                    );
                }
                
                $args['headers']['Authorization'] = 'Token ' . $company['token'];
                $test_url = $company['api_base_url'];
                break;
                
            case 'bearer_token':
                if (empty($company['token'])) {
                    return array(
                        'success' => false,
                        'message' => __('Token is required for this authentication type.', 'hajri-cod-shop')
                    );
                }
                
                $args['headers']['Authorization'] = 'Bearer ' . $company['token'];
                $test_url = $company['api_base_url'];
                break;
                
            case 'username_password':
                if (empty($company['username']) || empty($company['password'])) {
                    return array(
                        'success' => false,
                        'message' => __('Username and password are required for this authentication type.', 'hajri-cod-shop')
                    );
                }
                
                // Most APIs expect Basic Auth
                $args['headers']['Authorization'] = 'Basic ' . base64_encode($company['username'] . ':' . $company['password']);
                $test_url = $company['api_base_url'];
                break;
                
            default:
                return array(
                    'success' => false,
                    'message' => __('Unsupported authentication type.', 'hajri-cod-shop')
                );
        }
        
        // Make the API request
        $response = wp_remote_get($test_url, $args);
        
        // Process the response
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => $response->get_error_message(),
                'details' => array(
                    'url' => $test_url,
                    'error' => $response->get_error_code()
                )
            );
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        
        // Successful status codes are usually 2xx
        $success = ($status_code >= 200 && $status_code < 300);
        
        return array(
            'success' => $success,
            'message' => $success ? __('API connection successful!', 'hajri-cod-shop') : sprintf(__('API returned status code: %d', 'hajri-cod-shop'), $status_code),
            'details' => array(
                'url' => $test_url,
                'status_code' => $status_code,
                'response' => $response_body
            )
        );
    }
}

new Hajri_Cod_Shop_Delivery_Companies();