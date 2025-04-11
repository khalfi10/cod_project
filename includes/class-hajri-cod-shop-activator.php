<?php
/**
 * Fired during plugin activation.
 *
 * @link       https://example.com
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/includes
 * @author     Your Name <email@example.com>
 */
class Hajri_Cod_Shop_Activator {

    /**
     * Creates necessary database tables and initializes plugin settings during activation.
     *
     * @since    1.0.0
     */
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        // Table for shipping costs by city
        $table_shipping = $wpdb->prefix . 'hajri_shipping_costs';
        $sql_shipping = "CREATE TABLE $table_shipping (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            city_name varchar(100) NOT NULL,
            shipping_cost decimal(10,2) NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY city_name (city_name)
        ) $charset_collate;";
        
        // Table for discounts
        $table_discounts = $wpdb->prefix . 'hajri_discounts';
        $sql_discounts = "CREATE TABLE $table_discounts (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            discount_name varchar(100) NOT NULL,
            discount_type varchar(50) NOT NULL,
            discount_value decimal(10,2) NOT NULL,
            product_id bigint(20) NULL,
            city_name varchar(100) NULL,
            start_date datetime NULL,
            end_date datetime NULL,
            is_active tinyint(1) NOT NULL DEFAULT 1,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        // Table for orders
        $table_orders = $wpdb->prefix . 'hajri_orders';
        $sql_orders = "CREATE TABLE $table_orders (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            customer_name varchar(100) NOT NULL,
            phone_number varchar(20) NOT NULL,
            city varchar(100) NOT NULL,
            address text NOT NULL,
            products text NOT NULL,
            total_amount decimal(10,2) NOT NULL,
            shipping_cost decimal(10,2) NOT NULL,
            discount_applied decimal(10,2) DEFAULT 0,
            status varchar(50) NOT NULL DEFAULT 'pending',
            ip_address varchar(50) NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NULL,
            notes text NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        // Table for abandoned carts
        $table_abandoned = $wpdb->prefix . 'hajri_abandoned_carts';
        $sql_abandoned = "CREATE TABLE $table_abandoned_carts (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            customer_name varchar(100) NULL,
            phone_number varchar(20) NULL,
            city varchar(100) NULL,
            address text NULL,
            products text NOT NULL,
            total_amount decimal(10,2) NULL,
            ip_address varchar(50) NOT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NULL,
            is_converted tinyint(1) NOT NULL DEFAULT 0,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        // Table for blocked IP addresses
        $table_blocked_ips = $wpdb->prefix . 'hajri_blocked_ips';
        $sql_blocked_ips = "CREATE TABLE $table_blocked_ips (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            ip_address varchar(50) NOT NULL,
            reason text NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY ip_address (ip_address)
        ) $charset_collate;";
        
        // Table for Algerian delivery companies
        $table_delivery_companies = $wpdb->prefix . 'hajri_delivery_companies';
        $sql_delivery_companies = "CREATE TABLE $table_delivery_companies (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            company_name varchar(100) NOT NULL,
            company_code varchar(50) NOT NULL,
            api_base_url varchar(255) NULL,
            api_id varchar(100) NULL,
            api_key varchar(255) NULL,
            api_secret varchar(255) NULL,
            token varchar(255) NULL,
            username varchar(100) NULL,
            password varchar(255) NULL,
            auth_type varchar(50) NULL,
            endpoints text NULL,
            is_active tinyint(1) NOT NULL DEFAULT 1,
            description text NULL,
            config_data text NULL,
            webhook_url varchar(255) NULL,
            api_version varchar(50) NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY company_name (company_name),
            KEY company_code (company_code)
        ) $charset_collate;";
        
        // Table for Algerian wilayas (states/provinces)
        $table_wilayas = $wpdb->prefix . 'hajri_wilayas';
        $sql_wilayas = "CREATE TABLE $table_wilayas (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            wilaya_code varchar(5) NOT NULL,
            wilaya_name varchar(100) NOT NULL,
            wilaya_name_ar varchar(100) NOT NULL,
            shipping_cost decimal(10,2) NULL DEFAULT 500.00,
            delivery_days smallint(5) NULL DEFAULT 3,
            is_active tinyint(1) NOT NULL DEFAULT 1,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY wilaya_code (wilaya_code),
            UNIQUE KEY wilaya_name (wilaya_name)
        ) $charset_collate;";
        
        // Table for Algerian municipalities (communes/cities)
        $table_municipalities = $wpdb->prefix . 'hajri_municipalities';
        $sql_municipalities = "CREATE TABLE $table_municipalities (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            municipality_code varchar(10) NOT NULL,
            municipality_name varchar(100) NOT NULL,
            municipality_name_ar varchar(100) NOT NULL,
            wilaya_id mediumint(9) NOT NULL,
            postal_code varchar(10) NULL,
            extra_fee decimal(10,2) NULL DEFAULT 0.00,
            is_active tinyint(1) NOT NULL DEFAULT 1,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY municipality_code (municipality_code),
            KEY wilaya_id (wilaya_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_shipping);
        dbDelta($sql_discounts);
        dbDelta($sql_orders);
        dbDelta($sql_abandoned);
        dbDelta($sql_blocked_ips);
        dbDelta($sql_delivery_companies);
        dbDelta($sql_wilayas);
        dbDelta($sql_municipalities);
        
        // Add Algerian cities with default shipping costs
        $algerian_cities = array(
            'Adrar', 'Aïn Defla', 'Aïn Témouchent', 'Alger', 'Annaba', 'Batna', 'Béchar', 
            'Béjaïa', 'Biskra', 'Blida', 'Bordj Bou Arréridj', 'Bouira', 'Boumerdès', 
            'Chlef', 'Constantine', 'Djelfa', 'El Bayadh', 'El Oued', 'El Tarf', 'Ghardaïa', 
            'Guelma', 'Illizi', 'Jijel', 'Khenchela', 'Laghouat', 'Mascara', 'Médéa', 
            'Mila', 'Mostaganem', 'M\'Sila', 'Naâma', 'Oran', 'Ouargla', 'Oum El Bouaghi', 
            'Relizane', 'Saïda', 'Sétif', 'Sidi Bel Abbès', 'Skikda', 'Souk Ahras', 
            'Tamanrasset', 'Tébessa', 'Tiaret', 'Tindouf', 'Tipaza', 'Tissemsilt', 
            'Tizi Ouzou', 'Tlemcen'
        );
        
        // Default shipping cost is 500 DZD
        foreach ($algerian_cities as $city) {
            $wpdb->replace(
                $table_shipping,
                array(
                    'city_name' => $city,
                    'shipping_cost' => 500.00
                ),
                array('%s', '%f')
            );
        }
        
        // Create default plugin settings
        $default_settings = array(
            'google_sheets_enabled' => 0,
            'google_sheets_id' => '',
            'recaptcha_enabled' => 0,
            'recaptcha_site_key' => '',
            'recaptcha_secret_key' => '',
            'facebook_pixel_enabled' => 0,
            'facebook_pixel_id' => '',
            'tiktok_pixel_enabled' => 0,
            'tiktok_pixel_id' => '',
            'snapchat_pixel_enabled' => 0,
            'snapchat_pixel_id' => '',
            'google_analytics_enabled' => 0,
            'google_analytics_id' => '',
            'order_block_days' => 7,
            
            // Form fields default settings
            'form_fields_enabled' => json_encode(array(
                'name' => true,
                'phone' => true,
                'city' => true,
                'municipality' => true,
                'address' => true,
                'notes' => true
            )),
            'form_field_labels' => json_encode(array(
                'name' => 'الاسم الكامل',
                'phone' => 'رقم الهاتف',
                'city' => 'الولاية',
                'municipality' => 'البلدية',
                'address' => 'العنوان الكامل',
                'notes' => 'ملاحظات إضافية (اختياري)'
            )),
            'form_field_order' => json_encode(array(
                'name', 'phone', 'city', 'municipality', 'address', 'notes'
            )),
            
            // Form appearance default settings
            'form_background_color' => '#ffffff',
            'form_text_color' => '#333333',
            'form_accent_color' => '#3498db',
            'form_button_color' => '#3498db',
            'form_button_text_color' => '#ffffff',
            'form_border_color' => '#dddddd',
            
            // Product variations default settings
            'product_sizes' => json_encode(array('S', 'M', 'L', 'XL', 'XXL')),
            'product_colors' => json_encode(array(
                'أسود' => '#000000',
                'أبيض' => '#ffffff',
                'أحمر' => '#ff0000',
                'أزرق' => '#0000ff'
            )),
            
            // Form text default settings
            'form_button_text' => 'تأكيد الطلب',
            'form_success_message' => 'تم استلام طلبك بنجاح! سنتواصل معك قريبًا لتأكيد طلبك.'
        );
        
        update_option('hajri_cod_shop_settings', $default_settings);
        
        // Add default Algerian delivery companies with API integration details
        $delivery_companies = array(
            array(
                'company_name' => 'Yalidine',
                'company_code' => 'yalidine',
                'api_base_url' => 'https://api.yalidine.app/v1/',
                'auth_type' => 'api_key',
                'endpoints' => json_encode(array(
                    'create_shipment' => 'parcels/create',
                    'get_shipment' => 'parcels/details',
                    'track_shipment' => 'tracking',
                    'get_centers' => 'wilaya/centers',
                    'get_wilaya' => 'wilaya',
                    'get_fees' => 'wilaya/fees'
                )),
                'description' => 'شركة توصيل منتشرة في معظم ولايات الجزائر مع دعم API كامل لمتابعة الشحنات وإنشائها إلكترونياً'
            ),
            array(
                'company_name' => 'Maystro Delivery',
                'company_code' => 'maystro',
                'api_base_url' => 'https://api.maystro.com/',
                'auth_type' => 'token',
                'endpoints' => json_encode(array(
                    'create_shipment' => 'api/shipping/create',
                    'track_shipment' => 'api/shipping/track',
                    'get_cities' => 'api/cities',
                    'get_prices' => 'api/prices'
                )),
                'description' => 'خدمة توصيل سريعة وفعالة في المدن الكبرى مع واجهة برمجية متكاملة'
            ),
            array(
                'company_name' => '3M EXPRESS',
                'company_code' => '3mexp',
                'api_base_url' => 'https://3m-express.com/api/',
                'auth_type' => 'username_password',
                'endpoints' => json_encode(array(
                    'create_order' => 'orders/create',
                    'track_order' => 'orders/track',
                    'get_status' => 'orders/status'
                )),
                'description' => 'تغطية واسعة في الشمال والجنوب مع نظام تتبع الكتروني'
            ),
            array(
                'company_name' => 'ZR EXPRESS',
                'company_code' => 'zrexp',
                'api_base_url' => 'https://api.zrexpress.dz/',
                'auth_type' => 'api_key',
                'endpoints' => json_encode(array(
                    'create_delivery' => 'delivery/create',
                    'get_delivery' => 'delivery/get',
                    'track_delivery' => 'delivery/track'
                )),
                'description' => 'توصيل سريع للمناطق الحضرية مع نظام API مرن'
            ),
            array(
                'company_name' => 'Nord et Ouest',
                'company_code' => 'nordouest',
                'api_base_url' => 'https://api.nordouest.dz/api/v1/',
                'auth_type' => 'api_key',
                'endpoints' => json_encode(array(
                    'create_shipment' => 'shipments/create',
                    'track' => 'shipments/track'
                )),
                'description' => 'متخصصة في مناطق الشمال والغرب الجزائري مع واجهة برمجية شاملة'
            ),
            array(
                'company_name' => 'Kazi Tour',
                'company_code' => 'kazitour',
                'api_base_url' => 'https://kazitour.dz/api/',
                'auth_type' => 'bearer_token',
                'endpoints' => json_encode(array(
                    'auth' => 'auth/login',
                    'create' => 'deliveries/create',
                    'track' => 'deliveries/track'
                )),
                'description' => 'حلول توصيل فعالة للشركات الصغيرة والمتوسطة مع نظام تتبع إلكتروني متقدم'
            ),
            array(
                'company_name' => 'EMS',
                'company_code' => 'ems',
                'api_base_url' => 'https://api.ems.dz/',
                'auth_type' => 'api_key',
                'endpoints' => json_encode(array(
                    'create_parcel' => 'parcels/create',
                    'track_parcel' => 'tracking/status'
                )),
                'description' => 'خدمة بريد سريعة موثوقة على المستوى الوطني مع دعم API'
            ),
            array(
                'company_name' => 'GS Trans Express',
                'company_code' => 'gstrans',
                'api_base_url' => 'https://gstrans.dz/api/',
                'auth_type' => 'api_key',
                'endpoints' => json_encode(array(
                    'create_shipping' => 'shipping/create',
                    'get_status' => 'shipping/status',
                    'get_cities' => 'cities/list'
                )),
                'description' => 'تغطية واسعة في المناطق الشرقية والوسطى من الجزائر مع نظام إلكتروني متكامل'
            )
        );
        
        $table_delivery_companies = $wpdb->prefix . 'hajri_delivery_companies';
        foreach ($delivery_companies as $company) {
            $data = array(
                'company_name' => $company['company_name'],
                'company_code' => $company['company_code'],
                'api_base_url' => $company['api_base_url'],
                'auth_type' => $company['auth_type'],
                'endpoints' => $company['endpoints'],
                'description' => $company['description'],
                'is_active' => 1,
                'created_at' => current_time('mysql')
            );
            
            $wpdb->replace(
                $table_delivery_companies,
                $data,
                array('%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s')
            );
        }
        
        // Add default Algerian wilayas (states/provinces)
        $algerian_wilayas = array(
            array('code' => '01', 'name' => 'Adrar', 'name_ar' => 'أدرار'),
            array('code' => '02', 'name' => 'Chlef', 'name_ar' => 'الشلف'),
            array('code' => '03', 'name' => 'Laghouat', 'name_ar' => 'الأغواط'),
            array('code' => '04', 'name' => 'Oum El Bouaghi', 'name_ar' => 'أم البواقي'),
            array('code' => '05', 'name' => 'Batna', 'name_ar' => 'باتنة'),
            array('code' => '06', 'name' => 'Béjaïa', 'name_ar' => 'بجاية'),
            array('code' => '07', 'name' => 'Biskra', 'name_ar' => 'بسكرة'),
            array('code' => '08', 'name' => 'Béchar', 'name_ar' => 'بشار'),
            array('code' => '09', 'name' => 'Blida', 'name_ar' => 'البليدة'),
            array('code' => '10', 'name' => 'Bouira', 'name_ar' => 'البويرة'),
            array('code' => '11', 'name' => 'Tamanrasset', 'name_ar' => 'تمنراست'),
            array('code' => '12', 'name' => 'Tébessa', 'name_ar' => 'تبسة'),
            array('code' => '13', 'name' => 'Tlemcen', 'name_ar' => 'تلمسان'),
            array('code' => '14', 'name' => 'Tiaret', 'name_ar' => 'تيارت'),
            array('code' => '15', 'name' => 'Tizi Ouzou', 'name_ar' => 'تيزي وزو'),
            array('code' => '16', 'name' => 'Alger', 'name_ar' => 'الجزائر'),
            array('code' => '17', 'name' => 'Djelfa', 'name_ar' => 'الجلفة'),
            array('code' => '18', 'name' => 'Jijel', 'name_ar' => 'جيجل'),
            array('code' => '19', 'name' => 'Sétif', 'name_ar' => 'سطيف'),
            array('code' => '20', 'name' => 'Saïda', 'name_ar' => 'سعيدة'),
            array('code' => '21', 'name' => 'Skikda', 'name_ar' => 'سكيكدة'),
            array('code' => '22', 'name' => 'Sidi Bel Abbès', 'name_ar' => 'سيدي بلعباس'),
            array('code' => '23', 'name' => 'Annaba', 'name_ar' => 'عنابة'),
            array('code' => '24', 'name' => 'Guelma', 'name_ar' => 'قالمة'),
            array('code' => '25', 'name' => 'Constantine', 'name_ar' => 'قسنطينة'),
            array('code' => '26', 'name' => 'Médéa', 'name_ar' => 'المدية'),
            array('code' => '27', 'name' => 'Mostaganem', 'name_ar' => 'مستغانم'),
            array('code' => '28', 'name' => 'M\'Sila', 'name_ar' => 'المسيلة'),
            array('code' => '29', 'name' => 'Mascara', 'name_ar' => 'معسكر'),
            array('code' => '30', 'name' => 'Ouargla', 'name_ar' => 'ورقلة'),
            array('code' => '31', 'name' => 'Oran', 'name_ar' => 'وهران'),
            array('code' => '32', 'name' => 'El Bayadh', 'name_ar' => 'البيض'),
            array('code' => '33', 'name' => 'Illizi', 'name_ar' => 'إليزي'),
            array('code' => '34', 'name' => 'Bordj Bou Arréridj', 'name_ar' => 'برج بوعريريج'),
            array('code' => '35', 'name' => 'Boumerdès', 'name_ar' => 'بومرداس'),
            array('code' => '36', 'name' => 'El Tarf', 'name_ar' => 'الطارف'),
            array('code' => '37', 'name' => 'Tindouf', 'name_ar' => 'تندوف'),
            array('code' => '38', 'name' => 'Tissemsilt', 'name_ar' => 'تيسمسيلت'),
            array('code' => '39', 'name' => 'El Oued', 'name_ar' => 'الوادي'),
            array('code' => '40', 'name' => 'Khenchela', 'name_ar' => 'خنشلة'),
            array('code' => '41', 'name' => 'Souk Ahras', 'name_ar' => 'سوق أهراس'),
            array('code' => '42', 'name' => 'Tipaza', 'name_ar' => 'تيبازة'),
            array('code' => '43', 'name' => 'Mila', 'name_ar' => 'ميلة'),
            array('code' => '44', 'name' => 'Aïn Defla', 'name_ar' => 'عين الدفلى'),
            array('code' => '45', 'name' => 'Naâma', 'name_ar' => 'النعامة'),
            array('code' => '46', 'name' => 'Aïn Témouchent', 'name_ar' => 'عين تموشنت'),
            array('code' => '47', 'name' => 'Ghardaïa', 'name_ar' => 'غرداية'),
            array('code' => '48', 'name' => 'Relizane', 'name_ar' => 'غليزان'),
            array('code' => '49', 'name' => 'Timimoun', 'name_ar' => 'تيميمون'),
            array('code' => '50', 'name' => 'Bordj Badji Mokhtar', 'name_ar' => 'برج باجي مختار'),
            array('code' => '51', 'name' => 'Ouled Djellal', 'name_ar' => 'أولاد جلال'),
            array('code' => '52', 'name' => 'Béni Abbès', 'name_ar' => 'بني عباس'),
            array('code' => '53', 'name' => 'In Salah', 'name_ar' => 'عين صالح'),
            array('code' => '54', 'name' => 'In Guezzam', 'name_ar' => 'عين قزام'),
            array('code' => '55', 'name' => 'Touggourt', 'name_ar' => 'تقرت'),
            array('code' => '56', 'name' => 'Djanet', 'name_ar' => 'جانت'),
            array('code' => '57', 'name' => 'El M\'Ghair', 'name_ar' => 'المغير'),
            array('code' => '58', 'name' => 'El Meniaa', 'name_ar' => 'المنيعة')
        );
        
        $table_wilayas = $wpdb->prefix . 'hajri_wilayas';
        foreach ($algerian_wilayas as $wilaya) {
            // Calculate shipping cost: default 500 + (wilaya_code * 10) to make costs vary by region
            $shipping_cost = 500 + (intval($wilaya['code']) * 10);
            
            $data = array(
                'wilaya_code' => $wilaya['code'],
                'wilaya_name' => $wilaya['name'],
                'wilaya_name_ar' => $wilaya['name_ar'],
                'shipping_cost' => $shipping_cost,
                'delivery_days' => 3, // Default delivery time
                'is_active' => 1,
                'created_at' => current_time('mysql')
            );
            
            $wpdb->replace(
                $table_wilayas,
                $data,
                array('%s', '%s', '%s', '%f', '%d', '%d', '%s')
            );
        }
        
        // Add a few sample municipalities for major wilayas
        $algerian_municipalities = array(
            // Algiers (16)
            array('code' => '1601', 'name' => 'Alger Centre', 'name_ar' => 'الجزائر وسط', 'wilaya_code' => '16'),
            array('code' => '1602', 'name' => 'Bab El Oued', 'name_ar' => 'باب الوادي', 'wilaya_code' => '16'),
            array('code' => '1603', 'name' => 'El Biar', 'name_ar' => 'الأبيار', 'wilaya_code' => '16'),
            array('code' => '1604', 'name' => 'Bouzareah', 'name_ar' => 'بوزريعة', 'wilaya_code' => '16'),
            array('code' => '1605', 'name' => 'Bir Mourad Raïs', 'name_ar' => 'بئر مراد رايس', 'wilaya_code' => '16'),
            array('code' => '1606', 'name' => 'Hussein Dey', 'name_ar' => 'حسين داي', 'wilaya_code' => '16'),
            array('code' => '1607', 'name' => 'Kouba', 'name_ar' => 'القبة', 'wilaya_code' => '16'),
            array('code' => '1608', 'name' => 'Bachdjerrah', 'name_ar' => 'باش جراح', 'wilaya_code' => '16'),
            array('code' => '1609', 'name' => 'El Harrach', 'name_ar' => 'الحراش', 'wilaya_code' => '16'),
            array('code' => '1610', 'name' => 'Dar El Beïda', 'name_ar' => 'الدار البيضاء', 'wilaya_code' => '16'),
            
            // Oran (31)
            array('code' => '3101', 'name' => 'Oran', 'name_ar' => 'وهران', 'wilaya_code' => '31'),
            array('code' => '3102', 'name' => 'Gdyel', 'name_ar' => 'قديل', 'wilaya_code' => '31'),
            array('code' => '3103', 'name' => 'Bir El Djir', 'name_ar' => 'بئر الجير', 'wilaya_code' => '31'),
            array('code' => '3104', 'name' => 'Es Senia', 'name_ar' => 'السانية', 'wilaya_code' => '31'),
            array('code' => '3105', 'name' => 'Arzew', 'name_ar' => 'أرزيو', 'wilaya_code' => '31'),
            array('code' => '3106', 'name' => 'Bethioua', 'name_ar' => 'بطيوة', 'wilaya_code' => '31'),
            
            // Constantine (25)
            array('code' => '2501', 'name' => 'Constantine', 'name_ar' => 'قسنطينة', 'wilaya_code' => '25'),
            array('code' => '2502', 'name' => 'Hamma Bouziane', 'name_ar' => 'حامة بوزيان', 'wilaya_code' => '25'),
            array('code' => '2503', 'name' => 'El Khroub', 'name_ar' => 'الخروب', 'wilaya_code' => '25'),
            array('code' => '2504', 'name' => 'Zighoud Youcef', 'name_ar' => 'زيغود يوسف', 'wilaya_code' => '25'),
            array('code' => '2505', 'name' => 'Didouche Mourad', 'name_ar' => 'ديدوش مراد', 'wilaya_code' => '25'),
            
            // Annaba (23)
            array('code' => '2301', 'name' => 'Annaba', 'name_ar' => 'عنابة', 'wilaya_code' => '23'),
            array('code' => '2302', 'name' => 'Berrahal', 'name_ar' => 'برحال', 'wilaya_code' => '23'),
            array('code' => '2303', 'name' => 'El Hadjar', 'name_ar' => 'الحجار', 'wilaya_code' => '23'),
            array('code' => '2304', 'name' => 'Sidi Amar', 'name_ar' => 'سيدي عمار', 'wilaya_code' => '23')
        );
        
        $table_municipalities = $wpdb->prefix . 'hajri_municipalities';
        
        // Get wilaya IDs from the database
        $wilaya_ids = array();
        $results = $wpdb->get_results("SELECT id, wilaya_code FROM $table_wilayas", ARRAY_A);
        foreach ($results as $row) {
            $wilaya_ids[$row['wilaya_code']] = $row['id'];
        }
        
        foreach ($algerian_municipalities as $municipality) {
            if (isset($wilaya_ids[$municipality['wilaya_code']])) {
                $wilaya_id = $wilaya_ids[$municipality['wilaya_code']];
                
                $data = array(
                    'municipality_code' => $municipality['code'],
                    'municipality_name' => $municipality['name'],
                    'municipality_name_ar' => $municipality['name_ar'],
                    'wilaya_id' => $wilaya_id,
                    'postal_code' => substr($municipality['code'], 0, 2) . '000',
                    'extra_fee' => 0.00, // No extra fee by default
                    'is_active' => 1,
                    'created_at' => current_time('mysql')
                );
                
                $wpdb->replace(
                    $table_municipalities,
                    $data,
                    array('%s', '%s', '%s', '%d', '%s', '%f', '%d', '%s')
                );
            }
        }
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}
