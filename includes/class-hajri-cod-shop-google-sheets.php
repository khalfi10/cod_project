<?php
/**
 * Google Sheets integration functionality.
 *
 * @link       https://example.com
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/includes
 */

/**
 * Google Sheets integration functionality.
 *
 * This class handles integration with Google Sheets.
 *
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/includes
 * @author     Your Name <email@example.com>
 */
class Hajri_Cod_Shop_Google_Sheets {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        // Add settings page fields
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Register settings fields for Google Sheets integration.
     *
     * @since    1.0.0
     */
    public function register_settings() {
        register_setting('hajri_cod_shop_settings', 'hajri_cod_shop_settings');
        
        add_settings_section(
            'hajri_google_sheets_section',
            __('Google Sheets Integration', 'hajri-cod-shop'),
            array($this, 'google_sheets_section_callback'),
            'hajri_cod_shop_settings'
        );
        
        add_settings_field(
            'google_sheets_enabled',
            __('Enable Google Sheets Integration', 'hajri-cod-shop'),
            array($this, 'google_sheets_enabled_callback'),
            'hajri_cod_shop_settings',
            'hajri_google_sheets_section'
        );
        
        add_settings_field(
            'google_sheets_id',
            __('Google Sheet ID', 'hajri-cod-shop'),
            array($this, 'google_sheets_id_callback'),
            'hajri_cod_shop_settings',
            'hajri_google_sheets_section'
        );
    }

    /**
     * Google Sheets section description.
     *
     * @since    1.0.0
     */
    public function google_sheets_section_callback() {
        echo '<p>' . __('Configure integration with Google Sheets to track orders.', 'hajri-cod-shop') . '</p>';
    }

    /**
     * Google Sheets enabled field callback.
     *
     * @since    1.0.0
     */
    public function google_sheets_enabled_callback() {
        $options = get_option('hajri_cod_shop_settings');
        $enabled = isset($options['google_sheets_enabled']) ? $options['google_sheets_enabled'] : 0;
        
        echo '<input type="checkbox" id="google_sheets_enabled" name="hajri_cod_shop_settings[google_sheets_enabled]" value="1" ' . checked(1, $enabled, false) . '/>';
        echo '<label for="google_sheets_enabled">' . __('Enable syncing orders to Google Sheets', 'hajri-cod-shop') . '</label>';
    }

    /**
     * Google Sheet ID field callback.
     *
     * @since    1.0.0
     */
    public function google_sheets_id_callback() {
        $options = get_option('hajri_cod_shop_settings');
        $sheet_id = isset($options['google_sheets_id']) ? $options['google_sheets_id'] : '';
        
        echo '<input type="text" id="google_sheets_id" name="hajri_cod_shop_settings[google_sheets_id]" value="' . esc_attr($sheet_id) . '" class="regular-text" />';
        echo '<p class="description">' . __('Enter the ID of your Google Sheet. This is the long string of characters in the sheet URL.', 'hajri-cod-shop') . '</p>';
    }

    /**
     * Add an order to Google Sheets.
     *
     * @since    1.0.0
     * @param    int $order_id Order ID.
     * @param    string $name Customer name.
     * @param    string $phone Customer phone.
     * @param    string $city Customer city.
     * @param    string $address Customer address.
     * @param    string $product_name Product name.
     * @param    int $quantity Product quantity.
     * @param    float $product_total Product total.
     * @param    float $shipping_cost Shipping cost.
     * @param    float $discount_amount Discount amount.
     * @param    float $final_total Final total.
     * @return   bool Whether the operation was successful.
     */
    public static function add_order_to_sheet($order_id, $name, $phone, $city, $address, $product_name, $quantity, $product_total, $shipping_cost, $discount_amount, $final_total) {
        $options = get_option('hajri_cod_shop_settings');
        
        if (!isset($options['google_sheets_enabled']) || !$options['google_sheets_enabled']) {
            return false;
        }
        
        if (empty($options['google_sheets_id'])) {
            return false;
        }
        
        $sheet_id = $options['google_sheets_id'];
        
        // Format date for Google Sheets
        $date = current_time('Y-m-d H:i:s');
        
        // Prepare the data row
        $data = array(
            $order_id,
            $date,
            $name,
            $phone,
            $city,
            $address,
            $product_name,
            $quantity,
            $product_total,
            $shipping_cost,
            $discount_amount,
            $final_total,
            'pending'
        );
        
        // We'll use a simple webhook approach to Google Apps Script
        // Set up a Google Apps Script that handles this incoming data as a web app
        
        // The URL of the Google Apps Script web app
        $webhook_url = 'https://script.google.com/macros/s/YOUR_SCRIPT_ID/exec';
        
        // Prepare the POST data
        $post_data = array(
            'sheet_id' => $sheet_id,
            'action' => 'add_order',
            'data' => $data
        );
        
        // Send the data to Google Apps Script
        $response = wp_remote_post($webhook_url, array(
            'body' => json_encode($post_data),
            'headers' => array('Content-Type' => 'application/json'),
            'timeout' => 60
        ));
        
        if (is_wp_error($response)) {
            // Log error
            error_log('Google Sheets API Error: ' . $response->get_error_message());
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        $result = json_decode($body, true);
        
        if (isset($result['success']) && $result['success']) {
            return true;
        }
        
        return false;
    }

    /**
     * Create the Google Apps Script for integrating with Google Sheets.
     *
     * This function returns the code that needs to be pasted into Google Apps Script
     * to enable the integration.
     *
     * @since    1.0.0
     * @return   string The Google Apps Script code.
     */
    public static function get_google_apps_script_code() {
        $script = <<<'EOT'
        function doPost(e) {
          var data = JSON.parse(e.postData.contents);
          var sheetId = data.sheet_id;
          var action = data.action;
          
          if (action === 'add_order') {
            return addOrder(sheetId, data.data);
          }
          
          return ContentService.createTextOutput(JSON.stringify({
            success: false,
            message: 'Unknown action'
          })).setMimeType(ContentService.MimeType.JSON);
        }
        
        function addOrder(sheetId, rowData) {
          try {
            var ss = SpreadsheetApp.openById(sheetId);
            var sheet = ss.getSheetByName('Orders');
            
            // Create the sheet if it doesn't exist
            if (!sheet) {
              sheet = ss.insertSheet('Orders');
              
              // Add headers
              var headers = [
                'Order ID', 'Date', 'Customer Name', 'Phone', 'City', 'Address', 
                'Product', 'Quantity', 'Product Total', 'Shipping Cost', 
                'Discount', 'Final Total', 'Status'
              ];
              sheet.appendRow(headers);
              
              // Format the header row
              sheet.getRange(1, 1, 1, headers.length).setFontWeight('bold');
              sheet.setFrozenRows(1);
            }
            
            // Append the new row
            sheet.appendRow(rowData);
            
            return ContentService.createTextOutput(JSON.stringify({
              success: true,
              message: 'Order added successfully'
            })).setMimeType(ContentService.MimeType.JSON);
            
          } catch (error) {
            return ContentService.createTextOutput(JSON.stringify({
              success: false,
              message: error.toString()
            })).setMimeType(ContentService.MimeType.JSON);
          }
        }
        
        function doGet() {
          return HtmlService.createHtmlOutput(
            '<h1>This is a webhook for Hajri COD Shop</h1>' +
            '<p>This web app is designed to work with the Hajri COD Shop WordPress plugin.</p>'
          );
        }
        EOT;
        
        return $script;
    }
}

new Hajri_Cod_Shop_Google_Sheets();
