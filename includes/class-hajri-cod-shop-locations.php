<?php
/**
 * الفئة المسؤولة عن إدارة الولايات والبلديات
 *
 * @link       https://example.com
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/includes
 */

/**
 * الفئة المسؤولة عن إدارة الولايات والبلديات
 *
 * تتحكم هذه الفئة في الوظائف المرتبطة بالولايات والبلديات الجزائرية،
 * مثل إضافة / تعديل / حذف الولايات والبلديات، وكذلك استيراد البيانات من ملف JSON
 *
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/includes
 * @author     Your Name <email@example.com>
 */
class Hajri_Cod_Shop_Locations {

    /**
     * سجل الإجراءات لربط الدوال مع WordPress
     *
     * @since    1.0.0
     */
    public function __construct() {
        // AJAX actions for admin
        add_action('wp_ajax_get_wilayas', array($this, 'ajax_get_wilayas'));
        add_action('wp_ajax_get_wilaya', array($this, 'ajax_get_wilaya'));
        add_action('wp_ajax_create_wilaya', array($this, 'ajax_create_wilaya'));
        add_action('wp_ajax_update_wilaya', array($this, 'ajax_update_wilaya'));
        add_action('wp_ajax_delete_wilaya', array($this, 'ajax_delete_wilaya'));
        add_action('wp_ajax_toggle_wilaya_status', array($this, 'ajax_toggle_wilaya_status'));
        
        add_action('wp_ajax_get_municipalities', array($this, 'ajax_get_municipalities'));
        add_action('wp_ajax_get_municipality', array($this, 'ajax_get_municipality'));
        add_action('wp_ajax_create_municipality', array($this, 'ajax_create_municipality'));
        add_action('wp_ajax_update_municipality', array($this, 'ajax_update_municipality'));
        add_action('wp_ajax_delete_municipality', array($this, 'ajax_delete_municipality'));
        add_action('wp_ajax_toggle_municipality_status', array($this, 'ajax_toggle_municipality_status'));
        
        add_action('wp_ajax_import_locations_json', array($this, 'ajax_import_locations_json'));
        
        // AJAX for public (no authentication required)
        add_action('wp_ajax_nopriv_get_municipalities_by_wilaya', array($this, 'ajax_get_municipalities_by_wilaya'));
        add_action('wp_ajax_get_municipalities_by_wilaya', array($this, 'ajax_get_municipalities_by_wilaya'));
    }

    /**
     * الحصول على قائمة الولايات
     *
     * @since    1.0.0
     */
    public function ajax_get_wilayas() {
        check_ajax_referer('hajri_cod_shop_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'غير مصرح لك بالوصول إلى هذه البيانات'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'hajri_wilayas';
        
        $wilayas = $wpdb->get_results("SELECT * FROM $table_name ORDER BY wilaya_code ASC", ARRAY_A);
        
        if ($wilayas) {
            wp_send_json_success(array('wilayas' => $wilayas));
        } else {
            wp_send_json_error(array('message' => 'لم يتم العثور على ولايات'));
        }
    }

    /**
     * الحصول على ولاية محددة بناءً على المعرف
     *
     * @since    1.0.0
     */
    public function ajax_get_wilaya() {
        check_ajax_referer('hajri_cod_shop_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'غير مصرح لك بالوصول إلى هذه البيانات'));
        }
        
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            wp_send_json_error(array('message' => 'معرف الولاية مطلوب'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'hajri_wilayas';
        
        $wilaya_id = intval($_POST['id']);
        $wilaya = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $wilaya_id), ARRAY_A);
        
        if ($wilaya) {
            wp_send_json_success(array('wilaya' => $wilaya));
        } else {
            wp_send_json_error(array('message' => 'لم يتم العثور على الولاية'));
        }
    }

    /**
     * إنشاء ولاية جديدة
     *
     * @since    1.0.0
     */
    public function ajax_create_wilaya() {
        check_ajax_referer('hajri_cod_shop_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'غير مصرح لك بإنشاء ولاية جديدة'));
        }
        
        // التحقق من الحقول المطلوبة
        if (!isset($_POST['wilaya_code']) || empty($_POST['wilaya_code']) || 
            !isset($_POST['wilaya_name']) || empty($_POST['wilaya_name']) ||
            !isset($_POST['wilaya_name_ar']) || empty($_POST['wilaya_name_ar'])) {
            wp_send_json_error(array('message' => 'جميع الحقول مطلوبة'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'hajri_wilayas';
        
        // التحقق من عدم وجود رمز مكرر
        $existing_wilaya = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_name WHERE wilaya_code = %s", $_POST['wilaya_code']));
        if ($existing_wilaya) {
            wp_send_json_error(array('message' => 'رمز الولاية موجود بالفعل'));
        }
        
        // إعداد البيانات
        $data = array(
            'wilaya_code' => sanitize_text_field($_POST['wilaya_code']),
            'wilaya_name' => sanitize_text_field($_POST['wilaya_name']),
            'wilaya_name_ar' => sanitize_text_field($_POST['wilaya_name_ar']),
            'shipping_cost' => isset($_POST['shipping_cost']) ? floatval($_POST['shipping_cost']) : 500.00,
            'delivery_days' => isset($_POST['delivery_days']) ? intval($_POST['delivery_days']) : 3,
            'is_active' => isset($_POST['is_active']) ? intval($_POST['is_active']) : 1,
            'created_at' => current_time('mysql')
        );
        
        // إدراج البيانات
        $result = $wpdb->insert($table_name, $data, array('%s', '%s', '%s', '%f', '%d', '%d', '%s'));
        
        if ($result) {
            wp_send_json_success(array(
                'message' => 'تم إنشاء الولاية بنجاح',
                'wilaya_id' => $wpdb->insert_id
            ));
        } else {
            wp_send_json_error(array('message' => 'فشل إنشاء الولاية. يرجى المحاولة مرة أخرى.'));
        }
    }

    /**
     * تحديث ولاية موجودة
     *
     * @since    1.0.0
     */
    public function ajax_update_wilaya() {
        check_ajax_referer('hajri_cod_shop_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'غير مصرح لك بتعديل الولاية'));
        }
        
        // التحقق من الحقول المطلوبة
        if (!isset($_POST['id']) || empty($_POST['id']) ||
            !isset($_POST['wilaya_code']) || empty($_POST['wilaya_code']) || 
            !isset($_POST['wilaya_name']) || empty($_POST['wilaya_name']) ||
            !isset($_POST['wilaya_name_ar']) || empty($_POST['wilaya_name_ar'])) {
            wp_send_json_error(array('message' => 'جميع الحقول مطلوبة'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'hajri_wilayas';
        
        $wilaya_id = intval($_POST['id']);
        
        // التحقق من وجود الولاية
        $existing_wilaya = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_name WHERE id = %d", $wilaya_id));
        if (!$existing_wilaya) {
            wp_send_json_error(array('message' => 'الولاية غير موجودة'));
        }
        
        // التحقق من عدم وجود رمز مكرر (للولايات الأخرى)
        $duplicate_code = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE wilaya_code = %s AND id != %d",
            $_POST['wilaya_code'],
            $wilaya_id
        ));
        
        if ($duplicate_code) {
            wp_send_json_error(array('message' => 'رمز الولاية موجود بالفعل'));
        }
        
        // إعداد البيانات
        $data = array(
            'wilaya_code' => sanitize_text_field($_POST['wilaya_code']),
            'wilaya_name' => sanitize_text_field($_POST['wilaya_name']),
            'wilaya_name_ar' => sanitize_text_field($_POST['wilaya_name_ar']),
            'shipping_cost' => isset($_POST['shipping_cost']) ? floatval($_POST['shipping_cost']) : 500.00,
            'delivery_days' => isset($_POST['delivery_days']) ? intval($_POST['delivery_days']) : 3,
            'is_active' => isset($_POST['is_active']) ? intval($_POST['is_active']) : 1,
            'updated_at' => current_time('mysql')
        );
        
        // تحديث البيانات
        $result = $wpdb->update(
            $table_name,
            $data,
            array('id' => $wilaya_id),
            array('%s', '%s', '%s', '%f', '%d', '%d', '%s'),
            array('%d')
        );
        
        if ($result !== false) {
            wp_send_json_success(array('message' => 'تم تحديث الولاية بنجاح'));
        } else {
            wp_send_json_error(array('message' => 'لم يتم إجراء أي تغييرات أو حدث خطأ أثناء التحديث'));
        }
    }

    /**
     * حذف ولاية
     *
     * @since    1.0.0
     */
    public function ajax_delete_wilaya() {
        check_ajax_referer('hajri_cod_shop_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'غير مصرح لك بحذف الولاية'));
        }
        
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            wp_send_json_error(array('message' => 'معرف الولاية مطلوب'));
        }
        
        global $wpdb;
        $table_wilayas = $wpdb->prefix . 'hajri_wilayas';
        $table_municipalities = $wpdb->prefix . 'hajri_municipalities';
        
        $wilaya_id = intval($_POST['id']);
        
        // التحقق من عدم وجود بلديات مرتبطة بالولاية
        $municipalities_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_municipalities WHERE wilaya_id = %d",
            $wilaya_id
        ));
        
        if ($municipalities_count > 0) {
            wp_send_json_error(array('message' => 'لا يمكن حذف الولاية لأنها تحتوي على بلديات. قم بحذف البلديات أولاً.'));
        }
        
        // حذف الولاية
        $result = $wpdb->delete(
            $table_wilayas,
            array('id' => $wilaya_id),
            array('%d')
        );
        
        if ($result) {
            wp_send_json_success(array('message' => 'تم حذف الولاية بنجاح'));
        } else {
            wp_send_json_error(array('message' => 'فشل حذف الولاية. يرجى المحاولة مرة أخرى.'));
        }
    }

    /**
     * تبديل حالة الولاية (تفعيل/تعطيل)
     *
     * @since    1.0.0
     */
    public function ajax_toggle_wilaya_status() {
        check_ajax_referer('hajri_cod_shop_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'غير مصرح لك بتغيير حالة الولاية'));
        }
        
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            wp_send_json_error(array('message' => 'معرف الولاية مطلوب'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'hajri_wilayas';
        
        $wilaya_id = intval($_POST['id']);
        
        // الحصول على الحالة الحالية
        $current_status = $wpdb->get_var($wpdb->prepare(
            "SELECT is_active FROM $table_name WHERE id = %d",
            $wilaya_id
        ));
        
        if ($current_status === null) {
            wp_send_json_error(array('message' => 'الولاية غير موجودة'));
        }
        
        // تبديل الحالة
        $new_status = $current_status == 1 ? 0 : 1;
        
        $result = $wpdb->update(
            $table_name,
            array(
                'is_active' => $new_status,
                'updated_at' => current_time('mysql')
            ),
            array('id' => $wilaya_id),
            array('%d', '%s'),
            array('%d')
        );
        
        if ($result !== false) {
            $status_message = $new_status == 1 ? 'تم تفعيل الولاية بنجاح' : 'تم تعطيل الولاية بنجاح';
            wp_send_json_success(array(
                'message' => $status_message,
                'new_status' => $new_status
            ));
        } else {
            wp_send_json_error(array('message' => 'فشل تغيير حالة الولاية. يرجى المحاولة مرة أخرى.'));
        }
    }

    /**
     * الحصول على قائمة البلديات
     *
     * @since    1.0.0
     */
    public function ajax_get_municipalities() {
        check_ajax_referer('hajri_cod_shop_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'غير مصرح لك بالوصول إلى هذه البيانات'));
        }
        
        global $wpdb;
        $table_municipalities = $wpdb->prefix . 'hajri_municipalities';
        $table_wilayas = $wpdb->prefix . 'hajri_wilayas';
        
        // الاستعلام مع ربط جدول الولايات
        $query = "SELECT m.*, w.wilaya_name, w.wilaya_name_ar as wilaya_name_ar 
                 FROM $table_municipalities AS m
                 LEFT JOIN $table_wilayas AS w ON m.wilaya_id = w.id";
        
        // إضافة فلترة حسب الولاية إذا تم تحديدها
        if (isset($_POST['wilaya_id']) && !empty($_POST['wilaya_id'])) {
            $wilaya_id = intval($_POST['wilaya_id']);
            $query .= $wpdb->prepare(" WHERE m.wilaya_id = %d", $wilaya_id);
        }
        
        $query .= " ORDER BY m.municipality_code ASC";
        
        $municipalities = $wpdb->get_results($query, ARRAY_A);
        
        if ($municipalities) {
            wp_send_json_success(array('municipalities' => $municipalities));
        } else {
            wp_send_json_error(array('message' => 'لم يتم العثور على بلديات'));
        }
    }

    /**
     * الحصول على بلدية محددة بناءً على المعرف
     *
     * @since    1.0.0
     */
    public function ajax_get_municipality() {
        check_ajax_referer('hajri_cod_shop_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'غير مصرح لك بالوصول إلى هذه البيانات'));
        }
        
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            wp_send_json_error(array('message' => 'معرف البلدية مطلوب'));
        }
        
        global $wpdb;
        $table_municipalities = $wpdb->prefix . 'hajri_municipalities';
        $table_wilayas = $wpdb->prefix . 'hajri_wilayas';
        
        $municipality_id = intval($_POST['id']);
        
        $query = $wpdb->prepare(
            "SELECT m.*, w.wilaya_name, w.wilaya_name_ar as wilaya_name_ar 
             FROM $table_municipalities AS m
             LEFT JOIN $table_wilayas AS w ON m.wilaya_id = w.id
             WHERE m.id = %d",
            $municipality_id
        );
        
        $municipality = $wpdb->get_row($query, ARRAY_A);
        
        if ($municipality) {
            wp_send_json_success(array('municipality' => $municipality));
        } else {
            wp_send_json_error(array('message' => 'لم يتم العثور على البلدية'));
        }
    }

    /**
     * إنشاء بلدية جديدة
     *
     * @since    1.0.0
     */
    public function ajax_create_municipality() {
        check_ajax_referer('hajri_cod_shop_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'غير مصرح لك بإنشاء بلدية جديدة'));
        }
        
        // التحقق من الحقول المطلوبة
        if (!isset($_POST['municipality_code']) || empty($_POST['municipality_code']) || 
            !isset($_POST['municipality_name']) || empty($_POST['municipality_name']) ||
            !isset($_POST['municipality_name_ar']) || empty($_POST['municipality_name_ar']) ||
            !isset($_POST['wilaya_id']) || empty($_POST['wilaya_id'])) {
            wp_send_json_error(array('message' => 'جميع الحقول مطلوبة'));
        }
        
        global $wpdb;
        $table_municipalities = $wpdb->prefix . 'hajri_municipalities';
        $table_wilayas = $wpdb->prefix . 'hajri_wilayas';
        
        $wilaya_id = intval($_POST['wilaya_id']);
        
        // التحقق من وجود الولاية
        $wilaya_exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_wilayas WHERE id = %d",
            $wilaya_id
        ));
        
        if (!$wilaya_exists) {
            wp_send_json_error(array('message' => 'الولاية المحددة غير موجودة'));
        }
        
        // التحقق من عدم وجود رمز مكرر
        $existing_municipality = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_municipalities WHERE municipality_code = %s",
            $_POST['municipality_code']
        ));
        
        if ($existing_municipality) {
            wp_send_json_error(array('message' => 'رمز البلدية موجود بالفعل'));
        }
        
        // إعداد البيانات
        $data = array(
            'municipality_code' => sanitize_text_field($_POST['municipality_code']),
            'municipality_name' => sanitize_text_field($_POST['municipality_name']),
            'municipality_name_ar' => sanitize_text_field($_POST['municipality_name_ar']),
            'wilaya_id' => $wilaya_id,
            'postal_code' => isset($_POST['postal_code']) ? sanitize_text_field($_POST['postal_code']) : '',
            'extra_fee' => isset($_POST['extra_fee']) ? floatval($_POST['extra_fee']) : 0.00,
            'is_active' => isset($_POST['is_active']) ? intval($_POST['is_active']) : 1,
            'created_at' => current_time('mysql')
        );
        
        // إدراج البيانات
        $result = $wpdb->insert(
            $table_municipalities,
            $data,
            array('%s', '%s', '%s', '%d', '%s', '%f', '%d', '%s')
        );
        
        if ($result) {
            wp_send_json_success(array(
                'message' => 'تم إنشاء البلدية بنجاح',
                'municipality_id' => $wpdb->insert_id
            ));
        } else {
            wp_send_json_error(array('message' => 'فشل إنشاء البلدية. يرجى المحاولة مرة أخرى.'));
        }
    }

    /**
     * تحديث بلدية موجودة
     *
     * @since    1.0.0
     */
    public function ajax_update_municipality() {
        check_ajax_referer('hajri_cod_shop_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'غير مصرح لك بتعديل البلدية'));
        }
        
        // التحقق من الحقول المطلوبة
        if (!isset($_POST['id']) || empty($_POST['id']) ||
            !isset($_POST['municipality_code']) || empty($_POST['municipality_code']) || 
            !isset($_POST['municipality_name']) || empty($_POST['municipality_name']) ||
            !isset($_POST['municipality_name_ar']) || empty($_POST['municipality_name_ar']) ||
            !isset($_POST['wilaya_id']) || empty($_POST['wilaya_id'])) {
            wp_send_json_error(array('message' => 'جميع الحقول مطلوبة'));
        }
        
        global $wpdb;
        $table_municipalities = $wpdb->prefix . 'hajri_municipalities';
        $table_wilayas = $wpdb->prefix . 'hajri_wilayas';
        
        $municipality_id = intval($_POST['id']);
        $wilaya_id = intval($_POST['wilaya_id']);
        
        // التحقق من وجود البلدية
        $existing_municipality = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_municipalities WHERE id = %d",
            $municipality_id
        ));
        
        if (!$existing_municipality) {
            wp_send_json_error(array('message' => 'البلدية غير موجودة'));
        }
        
        // التحقق من وجود الولاية
        $wilaya_exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_wilayas WHERE id = %d",
            $wilaya_id
        ));
        
        if (!$wilaya_exists) {
            wp_send_json_error(array('message' => 'الولاية المحددة غير موجودة'));
        }
        
        // التحقق من عدم وجود رمز مكرر (للبلديات الأخرى)
        $duplicate_code = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_municipalities WHERE municipality_code = %s AND id != %d",
            $_POST['municipality_code'],
            $municipality_id
        ));
        
        if ($duplicate_code) {
            wp_send_json_error(array('message' => 'رمز البلدية موجود بالفعل'));
        }
        
        // إعداد البيانات
        $data = array(
            'municipality_code' => sanitize_text_field($_POST['municipality_code']),
            'municipality_name' => sanitize_text_field($_POST['municipality_name']),
            'municipality_name_ar' => sanitize_text_field($_POST['municipality_name_ar']),
            'wilaya_id' => $wilaya_id,
            'postal_code' => isset($_POST['postal_code']) ? sanitize_text_field($_POST['postal_code']) : '',
            'extra_fee' => isset($_POST['extra_fee']) ? floatval($_POST['extra_fee']) : 0.00,
            'is_active' => isset($_POST['is_active']) ? intval($_POST['is_active']) : 1,
            'updated_at' => current_time('mysql')
        );
        
        // تحديث البيانات
        $result = $wpdb->update(
            $table_municipalities,
            $data,
            array('id' => $municipality_id),
            array('%s', '%s', '%s', '%d', '%s', '%f', '%d', '%s'),
            array('%d')
        );
        
        if ($result !== false) {
            wp_send_json_success(array('message' => 'تم تحديث البلدية بنجاح'));
        } else {
            wp_send_json_error(array('message' => 'لم يتم إجراء أي تغييرات أو حدث خطأ أثناء التحديث'));
        }
    }

    /**
     * حذف بلدية
     *
     * @since    1.0.0
     */
    public function ajax_delete_municipality() {
        check_ajax_referer('hajri_cod_shop_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'غير مصرح لك بحذف البلدية'));
        }
        
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            wp_send_json_error(array('message' => 'معرف البلدية مطلوب'));
        }
        
        global $wpdb;
        $table_municipalities = $wpdb->prefix . 'hajri_municipalities';
        
        $municipality_id = intval($_POST['id']);
        
        // حذف البلدية
        $result = $wpdb->delete(
            $table_municipalities,
            array('id' => $municipality_id),
            array('%d')
        );
        
        if ($result) {
            wp_send_json_success(array('message' => 'تم حذف البلدية بنجاح'));
        } else {
            wp_send_json_error(array('message' => 'فشل حذف البلدية. يرجى المحاولة مرة أخرى.'));
        }
    }

    /**
     * تبديل حالة البلدية (تفعيل/تعطيل)
     *
     * @since    1.0.0
     */
    public function ajax_toggle_municipality_status() {
        check_ajax_referer('hajri_cod_shop_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'غير مصرح لك بتغيير حالة البلدية'));
        }
        
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            wp_send_json_error(array('message' => 'معرف البلدية مطلوب'));
        }
        
        global $wpdb;
        $table_municipalities = $wpdb->prefix . 'hajri_municipalities';
        
        $municipality_id = intval($_POST['id']);
        
        // الحصول على الحالة الحالية
        $current_status = $wpdb->get_var($wpdb->prepare(
            "SELECT is_active FROM $table_municipalities WHERE id = %d",
            $municipality_id
        ));
        
        if ($current_status === null) {
            wp_send_json_error(array('message' => 'البلدية غير موجودة'));
        }
        
        // تبديل الحالة
        $new_status = $current_status == 1 ? 0 : 1;
        
        $result = $wpdb->update(
            $table_municipalities,
            array(
                'is_active' => $new_status,
                'updated_at' => current_time('mysql')
            ),
            array('id' => $municipality_id),
            array('%d', '%s'),
            array('%d')
        );
        
        if ($result !== false) {
            $status_message = $new_status == 1 ? 'تم تفعيل البلدية بنجاح' : 'تم تعطيل البلدية بنجاح';
            wp_send_json_success(array(
                'message' => $status_message,
                'new_status' => $new_status
            ));
        } else {
            wp_send_json_error(array('message' => 'فشل تغيير حالة البلدية. يرجى المحاولة مرة أخرى.'));
        }
    }
    
    /**
     * الحصول على البلديات حسب الولاية (للاستخدام العام)
     *
     * @since    1.0.0
     */
    public function ajax_get_municipalities_by_wilaya() {
        // نطلب nonce للتأكد من أن الطلب قادم من صفحة موثوقة، لكن بدون التحقق من الصلاحيات
        check_ajax_referer('hajri_cod_shop_public_nonce', 'security');
        
        if (!isset($_POST['wilaya_id']) || empty($_POST['wilaya_id'])) {
            wp_send_json_error(array('message' => 'معرف الولاية مطلوب'));
        }
        
        global $wpdb;
        $table_municipalities = $wpdb->prefix . 'hajri_municipalities';
        
        $wilaya_id = intval($_POST['wilaya_id']);
        
        // الحصول على البلديات النشطة فقط
        $municipalities = $wpdb->get_results($wpdb->prepare(
            "SELECT id, municipality_code, municipality_name, municipality_name_ar, postal_code, extra_fee
             FROM $table_municipalities
             WHERE wilaya_id = %d AND is_active = 1
             ORDER BY municipality_name ASC",
            $wilaya_id
        ), ARRAY_A);
        
        if ($municipalities) {
            wp_send_json_success(array('municipalities' => $municipalities));
        } else {
            wp_send_json_error(array('message' => 'لم يتم العثور على بلديات للولاية المحددة'));
        }
    }
    
    /**
     * استيراد الولايات والبلديات من ملف JSON
     *
     * @since    1.0.0
     */
    public function ajax_import_locations_json() {
        check_ajax_referer('hajri_cod_shop_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'غير مصرح لك باستيراد البيانات'));
        }
        
        // التحقق من وجود الملف
        if (!isset($_FILES['locations_file']) || !isset($_FILES['locations_file']['tmp_name']) || empty($_FILES['locations_file']['tmp_name'])) {
            wp_send_json_error(array('message' => 'يرجى تحميل ملف JSON صالح'));
        }
        
        // قراءة محتوى الملف
        $json_content = file_get_contents($_FILES['locations_file']['tmp_name']);
        if (empty($json_content)) {
            wp_send_json_error(array('message' => 'الملف فارغ'));
        }
        
        // تحويل JSON إلى مصفوفة
        $locations_data = json_decode($json_content, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($locations_data)) {
            wp_send_json_error(array('message' => 'تنسيق JSON غير صالح: ' . json_last_error_msg()));
        }
        
        // التحقق من هيكل البيانات
        if (!isset($locations_data['wilayas']) || !is_array($locations_data['wilayas'])) {
            wp_send_json_error(array('message' => 'الملف لا يحتوي على بيانات صحيحة للولايات'));
        }
        
        global $wpdb;
        $table_wilayas = $wpdb->prefix . 'hajri_wilayas';
        $table_municipalities = $wpdb->prefix . 'hajri_municipalities';
        
        // بدء المعاملة
        $wpdb->query('START TRANSACTION');
        
        $wilayas_count = 0;
        $municipalities_count = 0;
        $wilayas_map = array(); // لتخزين تناظر رموز الولايات مع معرفاتها
        
        try {
            // معالجة الولايات
            foreach ($locations_data['wilayas'] as $wilaya) {
                // التحقق من وجود الحقول المطلوبة
                if (!isset($wilaya['code']) || !isset($wilaya['name']) || !isset($wilaya['name_ar'])) {
                    continue;
                }
                
                // البحث عن الولاية الموجودة
                $existing_wilaya = $wpdb->get_row($wpdb->prepare(
                    "SELECT id FROM $table_wilayas WHERE wilaya_code = %s",
                    $wilaya['code']
                ));
                
                // تحديد قيمة shipping_cost و delivery_days إذا كانت متوفرة
                $shipping_cost = isset($wilaya['shipping_cost']) ? floatval($wilaya['shipping_cost']) : 500.00;
                $delivery_days = isset($wilaya['delivery_days']) ? intval($wilaya['delivery_days']) : 3;
                
                // إعداد البيانات
                $wilaya_data = array(
                    'wilaya_code' => sanitize_text_field($wilaya['code']),
                    'wilaya_name' => sanitize_text_field($wilaya['name']),
                    'wilaya_name_ar' => sanitize_text_field($wilaya['name_ar']),
                    'shipping_cost' => $shipping_cost,
                    'delivery_days' => $delivery_days,
                    'is_active' => 1
                );
                
                if ($existing_wilaya) {
                    // تحديث الولاية الموجودة
                    $wilaya_data['updated_at'] = current_time('mysql');
                    $wpdb->update(
                        $table_wilayas,
                        $wilaya_data,
                        array('id' => $existing_wilaya->id),
                        array('%s', '%s', '%s', '%f', '%d', '%d', '%s')
                    );
                    $wilayas_map[$wilaya['code']] = $existing_wilaya->id;
                } else {
                    // إنشاء ولاية جديدة
                    $wilaya_data['created_at'] = current_time('mysql');
                    $wpdb->insert(
                        $table_wilayas,
                        $wilaya_data,
                        array('%s', '%s', '%s', '%f', '%d', '%d', '%s')
                    );
                    $wilayas_map[$wilaya['code']] = $wpdb->insert_id;
                    $wilayas_count++;
                }
                
                // معالجة البلديات إذا كانت موجودة
                if (isset($wilaya['municipalities']) && is_array($wilaya['municipalities'])) {
                    foreach ($wilaya['municipalities'] as $municipality) {
                        // التحقق من وجود الحقول المطلوبة
                        if (!isset($municipality['code']) || !isset($municipality['name']) || !isset($municipality['name_ar'])) {
                            continue;
                        }
                        
                        $wilaya_id = $wilayas_map[$wilaya['code']];
                        
                        // البحث عن البلدية الموجودة
                        $existing_municipality = $wpdb->get_row($wpdb->prepare(
                            "SELECT id FROM $table_municipalities WHERE municipality_code = %s",
                            $municipality['code']
                        ));
                        
                        // تحديد قيمة postal_code و extra_fee إذا كانت متوفرة
                        $postal_code = isset($municipality['postal_code']) ? sanitize_text_field($municipality['postal_code']) : '';
                        $extra_fee = isset($municipality['extra_fee']) ? floatval($municipality['extra_fee']) : 0.00;
                        
                        // إعداد البيانات
                        $municipality_data = array(
                            'municipality_code' => sanitize_text_field($municipality['code']),
                            'municipality_name' => sanitize_text_field($municipality['name']),
                            'municipality_name_ar' => sanitize_text_field($municipality['name_ar']),
                            'wilaya_id' => $wilaya_id,
                            'postal_code' => $postal_code,
                            'extra_fee' => $extra_fee,
                            'is_active' => 1
                        );
                        
                        if ($existing_municipality) {
                            // تحديث البلدية الموجودة
                            $municipality_data['updated_at'] = current_time('mysql');
                            $wpdb->update(
                                $table_municipalities,
                                $municipality_data,
                                array('id' => $existing_municipality->id),
                                array('%s', '%s', '%s', '%d', '%s', '%f', '%d', '%s')
                            );
                        } else {
                            // إنشاء بلدية جديدة
                            $municipality_data['created_at'] = current_time('mysql');
                            $wpdb->insert(
                                $table_municipalities,
                                $municipality_data,
                                array('%s', '%s', '%s', '%d', '%s', '%f', '%d', '%s')
                            );
                            $municipalities_count++;
                        }
                    }
                }
            }
            
            // تأكيد المعاملة
            $wpdb->query('COMMIT');
            
            // إعداد رسالة النجاح
            $success_message = '';
            if ($wilayas_count > 0) {
                $success_message .= sprintf(_n('تم استيراد %d ولاية', 'تم استيراد %d ولايات', $wilayas_count, 'hajri-cod-shop'), $wilayas_count) . ' ';
            }
            if ($municipalities_count > 0) {
                $success_message .= sprintf(_n('و %d بلدية', 'و %d بلديات', $municipalities_count, 'hajri-cod-shop'), $municipalities_count);
            }
            if (empty($success_message)) {
                $success_message = 'تم تحديث البيانات الموجودة';
            }
            
            wp_send_json_success(array(
                'message' => $success_message,
                'wilayas_count' => $wilayas_count,
                'municipalities_count' => $municipalities_count
            ));
            
        } catch (Exception $e) {
            // التراجع عن المعاملة في حالة حدوث خطأ
            $wpdb->query('ROLLBACK');
            wp_send_json_error(array('message' => 'حدث خطأ أثناء استيراد البيانات: ' . $e->getMessage()));
        }
    }
}