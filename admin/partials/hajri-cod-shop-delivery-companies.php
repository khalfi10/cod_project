<?php
/**
 * The admin delivery companies partial for the plugin
 *
 * @link       https://example.com
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/admin/partials
 */

// Direct access security
if (!defined('WPINC')) {
    die;
}
?>

<div class="wrap hajri-admin-page hajri-delivery-companies-page">
    <h1><?php echo esc_html__('شركات التوصيل الجزائرية', 'hajri-cod-shop'); ?></h1>
    
    <div id="hajri-delivery-companies-notification" class="notice is-dismissible" style="display: none;">
        <p></p>
    </div>

    <div class="hajri-admin-card">
        <div class="hajri-admin-card-header">
            <h2><?php echo esc_html__('إضافة شركة توصيل جديدة', 'hajri-cod-shop'); ?></h2>
        </div>
        <div class="hajri-admin-card-body">
            <form id="hajri-add-delivery-company-form" class="hajri-form">
                <div class="hajri-form-row">
                    <div class="hajri-form-group">
                        <label for="company_name"><?php echo esc_html__('اسم الشركة', 'hajri-cod-shop'); ?> <span class="required">*</span></label>
                        <input type="text" id="company_name" name="company_name" class="regular-text" required>
                    </div>
                    <div class="hajri-form-group">
                        <label for="company_code"><?php echo esc_html__('رمز الشركة', 'hajri-cod-shop'); ?></label>
                        <input type="text" id="company_code" name="company_code" class="regular-text" placeholder="<?php echo esc_attr__('سيتم إنشاؤه تلقائيًا إذا تم تركه فارغًا', 'hajri-cod-shop'); ?>">
                        <small class="help-text"><?php echo esc_html__('رمز فريد للشركة يستخدم في الاتصالات البرمجية (بالأحرف اللاتينية)', 'hajri-cod-shop'); ?></small>
                    </div>
                </div>
                
                <div class="hajri-tabs hajri-api-tabs">
                    <div class="hajri-tab-header">
                        <button type="button" class="hajri-tab-btn active" data-tab="basic-info"><?php echo esc_html__('معلومات أساسية', 'hajri-cod-shop'); ?></button>
                        <button type="button" class="hajri-tab-btn" data-tab="api-settings"><?php echo esc_html__('إعدادات API', 'hajri-cod-shop'); ?></button>
                        <button type="button" class="hajri-tab-btn" data-tab="endpoints"><?php echo esc_html__('نقاط النهاية', 'hajri-cod-shop'); ?></button>
                    </div>
                    
                    <div class="hajri-tab-content active" data-tab="basic-info">
                        <div class="hajri-form-row">
                            <div class="hajri-form-group">
                                <label for="api_base_url"><?php echo esc_html__('رابط API الأساسي', 'hajri-cod-shop'); ?></label>
                                <input type="text" id="api_base_url" name="api_base_url" class="regular-text" placeholder="https://api.example.com/v1/">
                            </div>
                            <div class="hajri-form-group">
                                <label for="auth_type"><?php echo esc_html__('نوع المصادقة', 'hajri-cod-shop'); ?></label>
                                <select id="auth_type" name="auth_type" class="regular-text">
                                    <option value=""><?php echo esc_html__('- اختر نوع المصادقة -', 'hajri-cod-shop'); ?></option>
                                    <option value="api_key"><?php echo esc_html__('مفتاح API', 'hajri-cod-shop'); ?></option>
                                    <option value="token"><?php echo esc_html__('رمز مميز (Token)', 'hajri-cod-shop'); ?></option>
                                    <option value="bearer_token"><?php echo esc_html__('رمز Bearer', 'hajri-cod-shop'); ?></option>
                                    <option value="username_password"><?php echo esc_html__('اسم المستخدم وكلمة المرور', 'hajri-cod-shop'); ?></option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="hajri-form-row">
                            <div class="hajri-form-group">
                                <label for="is_active"><?php echo esc_html__('حالة الشركة', 'hajri-cod-shop'); ?></label>
                                <select id="is_active" name="is_active" class="regular-text">
                                    <option value="1"><?php echo esc_html__('مفعّلة', 'hajri-cod-shop'); ?></option>
                                    <option value="0"><?php echo esc_html__('معطّلة', 'hajri-cod-shop'); ?></option>
                                </select>
                            </div>
                            <div class="hajri-form-group">
                                <label for="api_version"><?php echo esc_html__('إصدار API', 'hajri-cod-shop'); ?></label>
                                <input type="text" id="api_version" name="api_version" class="regular-text" placeholder="v1">
                            </div>
                        </div>
                        
                        <div class="hajri-form-row">
                            <div class="hajri-form-group">
                                <label for="description"><?php echo esc_html__('وصف مختصر', 'hajri-cod-shop'); ?></label>
                                <textarea id="description" name="description" rows="3" class="regular-text"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="hajri-tab-content" data-tab="api-settings">
                        <div class="auth-fields api_key-fields">
                            <div class="hajri-form-row">
                                <div class="hajri-form-group">
                                    <label for="api_id"><?php echo esc_html__('معرّف API', 'hajri-cod-shop'); ?></label>
                                    <input type="text" id="api_id" name="api_id" class="regular-text">
                                </div>
                                <div class="hajri-form-group">
                                    <label for="api_key"><?php echo esc_html__('مفتاح API', 'hajri-cod-shop'); ?></label>
                                    <input type="text" id="api_key" name="api_key" class="regular-text">
                                </div>
                            </div>
                            <div class="hajri-form-row">
                                <div class="hajri-form-group">
                                    <label for="api_secret"><?php echo esc_html__('سر API', 'hajri-cod-shop'); ?></label>
                                    <input type="text" id="api_secret" name="api_secret" class="regular-text">
                                </div>
                            </div>
                        </div>
                        
                        <div class="auth-fields token-fields bearer_token-fields">
                            <div class="hajri-form-row">
                                <div class="hajri-form-group">
                                    <label for="token"><?php echo esc_html__('الرمز المميز', 'hajri-cod-shop'); ?></label>
                                    <input type="text" id="token" name="token" class="regular-text">
                                </div>
                            </div>
                        </div>
                        
                        <div class="auth-fields username_password-fields">
                            <div class="hajri-form-row">
                                <div class="hajri-form-group">
                                    <label for="username"><?php echo esc_html__('اسم المستخدم', 'hajri-cod-shop'); ?></label>
                                    <input type="text" id="username" name="username" class="regular-text">
                                </div>
                                <div class="hajri-form-group">
                                    <label for="password"><?php echo esc_html__('كلمة المرور', 'hajri-cod-shop'); ?></label>
                                    <input type="password" id="password" name="password" class="regular-text">
                                </div>
                            </div>
                        </div>
                        
                        <div class="hajri-form-row">
                            <div class="hajri-form-group">
                                <label for="webhook_url"><?php echo esc_html__('رابط Webhook', 'hajri-cod-shop'); ?></label>
                                <input type="text" id="webhook_url" name="webhook_url" class="regular-text">
                                <small class="help-text"><?php echo esc_html__('للإشعارات من شركة التوصيل (اختياري)', 'hajri-cod-shop'); ?></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="hajri-tab-content" data-tab="endpoints">
                        <p class="description"><?php echo esc_html__('أدخل معلومات نقاط نهاية API. هذه هي المسارات المحددة لعمليات مختلفة مثل إنشاء شحنة أو تتبع طلب.', 'hajri-cod-shop'); ?></p>
                        
                        <div id="endpoints-container">
                            <div class="hajri-form-row endpoint-row">
                                <div class="hajri-form-group">
                                    <label for="endpoint_key_0"><?php echo esc_html__('المفتاح', 'hajri-cod-shop'); ?></label>
                                    <input type="text" id="endpoint_key_0" name="endpoint_keys[]" class="regular-text" placeholder="create_shipment">
                                </div>
                                <div class="hajri-form-group">
                                    <label for="endpoint_value_0"><?php echo esc_html__('المسار', 'hajri-cod-shop'); ?></label>
                                    <input type="text" id="endpoint_value_0" name="endpoint_values[]" class="regular-text" placeholder="shipments/create">
                                </div>
                                <div class="endpoint-actions">
                                    <button type="button" class="button remove-endpoint" disabled><?php echo esc_html__('حذف', 'hajri-cod-shop'); ?></button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="hajri-form-row">
                            <button type="button" id="add-endpoint" class="button"><?php echo esc_html__('إضافة نقطة نهاية', 'hajri-cod-shop'); ?></button>
                        </div>
                    </div>
                </div>
                
                <div class="hajri-form-row form-actions">
                    <button type="submit" class="button button-primary"><?php echo esc_html__('إضافة شركة', 'hajri-cod-shop'); ?></button>
                </div>
            </form>
        </div>
    </div>

    <div class="hajri-admin-card">
        <div class="hajri-admin-card-header">
            <h2><?php echo esc_html__('قائمة شركات التوصيل', 'hajri-cod-shop'); ?></h2>
        </div>
        <div class="hajri-admin-card-body">
            <div id="hajri-delivery-companies-loading">
                <?php echo esc_html__('جاري تحميل قائمة الشركات...', 'hajri-cod-shop'); ?>
            </div>

            <table id="hajri-delivery-companies-table" class="widefat hajri-table" style="display: none;">
                <thead>
                    <tr>
                        <th><?php echo esc_html__('اسم الشركة', 'hajri-cod-shop'); ?></th>
                        <th><?php echo esc_html__('الكود', 'hajri-cod-shop'); ?></th>
                        <th><?php echo esc_html__('نوع المصادقة', 'hajri-cod-shop'); ?></th>
                        <th><?php echo esc_html__('الحالة', 'hajri-cod-shop'); ?></th>
                        <th><?php echo esc_html__('رابط API', 'hajri-cod-shop'); ?></th>
                        <th><?php echo esc_html__('الإجراءات', 'hajri-cod-shop'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Companies will be loaded here via JavaScript -->
                </tbody>
            </table>
            
            <div id="hajri-no-delivery-companies" style="display: none;">
                <?php echo esc_html__('لا توجد شركات توصيل. أضف شركة باستخدام النموذج أعلاه.', 'hajri-cod-shop'); ?>
            </div>
        </div>
    </div>
</div>

<!-- Edit Company Modal -->
<div id="hajri-edit-company-modal" class="hajri-modal">
    <div class="hajri-modal-content">
        <span class="hajri-modal-close">&times;</span>
        <h3><?php echo esc_html__('تعديل شركة التوصيل', 'hajri-cod-shop'); ?></h3>
        
        <form id="hajri-edit-delivery-company-form" class="hajri-form">
            <input type="hidden" id="edit_company_id" name="id">
            
            <div class="hajri-tabs hajri-edit-tabs">
                <div class="hajri-tab-header">
                    <button type="button" class="hajri-tab-btn active" data-tab="edit-basic-info"><?php echo esc_html__('معلومات أساسية', 'hajri-cod-shop'); ?></button>
                    <button type="button" class="hajri-tab-btn" data-tab="edit-api-settings"><?php echo esc_html__('إعدادات API', 'hajri-cod-shop'); ?></button>
                    <button type="button" class="hajri-tab-btn" data-tab="edit-endpoints"><?php echo esc_html__('نقاط النهاية', 'hajri-cod-shop'); ?></button>
                </div>
                
                <div class="hajri-tab-content active" data-tab="edit-basic-info">
                    <div class="hajri-form-row">
                        <div class="hajri-form-group">
                            <label for="edit_company_name"><?php echo esc_html__('اسم الشركة', 'hajri-cod-shop'); ?> <span class="required">*</span></label>
                            <input type="text" id="edit_company_name" name="company_name" class="regular-text" required>
                        </div>
                        <div class="hajri-form-group">
                            <label for="edit_company_code"><?php echo esc_html__('رمز الشركة', 'hajri-cod-shop'); ?></label>
                            <input type="text" id="edit_company_code" name="company_code" class="regular-text">
                        </div>
                    </div>
                    
                    <div class="hajri-form-row">
                        <div class="hajri-form-group">
                            <label for="edit_api_base_url"><?php echo esc_html__('رابط API الأساسي', 'hajri-cod-shop'); ?></label>
                            <input type="text" id="edit_api_base_url" name="api_base_url" class="regular-text">
                        </div>
                        <div class="hajri-form-group">
                            <label for="edit_auth_type"><?php echo esc_html__('نوع المصادقة', 'hajri-cod-shop'); ?></label>
                            <select id="edit_auth_type" name="auth_type" class="regular-text">
                                <option value=""><?php echo esc_html__('- اختر نوع المصادقة -', 'hajri-cod-shop'); ?></option>
                                <option value="api_key"><?php echo esc_html__('مفتاح API', 'hajri-cod-shop'); ?></option>
                                <option value="token"><?php echo esc_html__('رمز مميز (Token)', 'hajri-cod-shop'); ?></option>
                                <option value="bearer_token"><?php echo esc_html__('رمز Bearer', 'hajri-cod-shop'); ?></option>
                                <option value="username_password"><?php echo esc_html__('اسم المستخدم وكلمة المرور', 'hajri-cod-shop'); ?></option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="hajri-form-row">
                        <div class="hajri-form-group">
                            <label for="edit_is_active"><?php echo esc_html__('حالة الشركة', 'hajri-cod-shop'); ?></label>
                            <select id="edit_is_active" name="is_active" class="regular-text">
                                <option value="1"><?php echo esc_html__('مفعّلة', 'hajri-cod-shop'); ?></option>
                                <option value="0"><?php echo esc_html__('معطّلة', 'hajri-cod-shop'); ?></option>
                            </select>
                        </div>
                        <div class="hajri-form-group">
                            <label for="edit_api_version"><?php echo esc_html__('إصدار API', 'hajri-cod-shop'); ?></label>
                            <input type="text" id="edit_api_version" name="api_version" class="regular-text">
                        </div>
                    </div>
                    
                    <div class="hajri-form-row">
                        <div class="hajri-form-group">
                            <label for="edit_description"><?php echo esc_html__('وصف مختصر', 'hajri-cod-shop'); ?></label>
                            <textarea id="edit_description" name="description" rows="3" class="regular-text"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="hajri-tab-content" data-tab="edit-api-settings">
                    <div class="edit-auth-fields edit-api_key-fields">
                        <div class="hajri-form-row">
                            <div class="hajri-form-group">
                                <label for="edit_api_id"><?php echo esc_html__('معرّف API', 'hajri-cod-shop'); ?></label>
                                <input type="text" id="edit_api_id" name="api_id" class="regular-text">
                            </div>
                            <div class="hajri-form-group">
                                <label for="edit_api_key"><?php echo esc_html__('مفتاح API', 'hajri-cod-shop'); ?></label>
                                <input type="text" id="edit_api_key" name="api_key" class="regular-text">
                            </div>
                        </div>
                        <div class="hajri-form-row">
                            <div class="hajri-form-group">
                                <label for="edit_api_secret"><?php echo esc_html__('سر API', 'hajri-cod-shop'); ?></label>
                                <input type="text" id="edit_api_secret" name="api_secret" class="regular-text">
                            </div>
                        </div>
                    </div>
                    
                    <div class="edit-auth-fields edit-token-fields edit-bearer_token-fields">
                        <div class="hajri-form-row">
                            <div class="hajri-form-group">
                                <label for="edit_token"><?php echo esc_html__('الرمز المميز', 'hajri-cod-shop'); ?></label>
                                <input type="text" id="edit_token" name="token" class="regular-text">
                            </div>
                        </div>
                    </div>
                    
                    <div class="edit-auth-fields edit-username_password-fields">
                        <div class="hajri-form-row">
                            <div class="hajri-form-group">
                                <label for="edit_username"><?php echo esc_html__('اسم المستخدم', 'hajri-cod-shop'); ?></label>
                                <input type="text" id="edit_username" name="username" class="regular-text">
                            </div>
                            <div class="hajri-form-group">
                                <label for="edit_password"><?php echo esc_html__('كلمة المرور', 'hajri-cod-shop'); ?></label>
                                <input type="password" id="edit_password" name="password" class="regular-text">
                            </div>
                        </div>
                    </div>
                    
                    <div class="hajri-form-row">
                        <div class="hajri-form-group">
                            <label for="edit_webhook_url"><?php echo esc_html__('رابط Webhook', 'hajri-cod-shop'); ?></label>
                            <input type="text" id="edit_webhook_url" name="webhook_url" class="regular-text">
                        </div>
                    </div>
                </div>
                
                <div class="hajri-tab-content" data-tab="edit-endpoints">
                    <p class="description"><?php echo esc_html__('أدخل معلومات نقاط نهاية API. هذه هي المسارات المحددة لعمليات مختلفة مثل إنشاء شحنة أو تتبع طلب.', 'hajri-cod-shop'); ?></p>
                    
                    <div id="edit-endpoints-container">
                        <!-- Endpoints will be loaded dynamically -->
                    </div>
                    
                    <div class="hajri-form-row">
                        <button type="button" id="edit-add-endpoint" class="button"><?php echo esc_html__('إضافة نقطة نهاية', 'hajri-cod-shop'); ?></button>
                    </div>
                </div>
            </div>
            
            <div class="hajri-form-row form-actions">
                <button type="submit" class="button button-primary"><?php echo esc_html__('حفظ التغييرات', 'hajri-cod-shop'); ?></button>
                <button type="button" class="button hajri-modal-cancel"><?php echo esc_html__('إلغاء', 'hajri-cod-shop'); ?></button>
            </div>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Load delivery companies
    function loadDeliveryCompanies() {
        $('#hajri-delivery-companies-loading').show();
        $('#hajri-delivery-companies-table').hide();
        $('#hajri-no-delivery-companies').hide();
        
        $.ajax({
            url: hajri_admin_object.ajax_url,
            type: 'POST',
            data: {
                action: 'get_delivery_companies',
                security: hajri_admin_object.nonce
            },
            success: function(response) {
                $('#hajri-delivery-companies-loading').hide();
                
                if (response.success && response.data.companies) {
                    const companies = response.data.companies;
                    
                    if (companies.length > 0) {
                        $('#hajri-delivery-companies-table tbody').empty();
                        
                        companies.forEach(function(company) {
                            const companyCode = company.company_code || '-';
                            
                            // Get auth type display text
                            let authType = '-';
                            switch(company.auth_type) {
                                case 'api_key':
                                    authType = 'مفتاح API';
                                    break;
                                case 'token':
                                    authType = 'رمز مميز';
                                    break;
                                case 'bearer_token':
                                    authType = 'رمز Bearer';
                                    break;
                                case 'username_password':
                                    authType = 'اسم مستخدم وكلمة مرور';
                                    break;
                            }
                            
                            // Format status
                            const status = company.is_active == 1 
                                ? '<span class="status-badge status-active">مفعّلة</span>' 
                                : '<span class="status-badge status-inactive">معطّلة</span>';
                            
                            // Format API URL (shortened)
                            const apiUrl = company.api_base_url 
                                ? `<span title="${company.api_base_url}">${company.api_base_url.substring(0, 30)}${company.api_base_url.length > 30 ? '...' : ''}</span>` 
                                : '-';
                            
                            const row = `
                                <tr data-id="${company.id}">
                                    <td>${company.company_name}</td>
                                    <td>${companyCode}</td>
                                    <td>${authType}</td>
                                    <td>${status}</td>
                                    <td>${apiUrl}</td>
                                    <td class="actions-column">
                                        <button class="button edit-company" data-id="${company.id}" title="تعديل">
                                            <span class="dashicons dashicons-edit"></span>
                                        </button>
                                        <button class="button test-api" data-id="${company.id}" title="اختبار الاتصال بالـ API">
                                            <span class="dashicons dashicons-rest-api"></span>
                                        </button>
                                        <button class="button toggle-status" data-id="${company.id}" data-active="${company.is_active}" title="${company.is_active == 1 ? 'تعطيل' : 'تفعيل'}">
                                            <span class="dashicons dashicons-${company.is_active == 1 ? 'toggle-on' : 'toggle-off'}"></span>
                                        </button>
                                        <button class="button delete-company" data-id="${company.id}" title="حذف">
                                            <span class="dashicons dashicons-trash"></span>
                                        </button>
                                    </td>
                                </tr>
                            `;
                            
                            $('#hajri-delivery-companies-table tbody').append(row);
                        });
                        
                        $('#hajri-delivery-companies-table').show();
                    } else {
                        $('#hajri-no-delivery-companies').show();
                    }
                } else {
                    $('#hajri-no-delivery-companies').show();
                }
            },
            error: function() {
                $('#hajri-delivery-companies-loading').hide();
                $('#hajri-no-delivery-companies').show();
                showNotification('error', hajri_admin_object.strings.error + ' ' + hajri_admin_object.strings.failed_load);
            }
        });
    }
    
    // Add new delivery company
    $('#hajri-add-delivery-company-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        
        $.ajax({
            url: hajri_admin_object.ajax_url,
            type: 'POST',
            data: {
                action: 'create_delivery_company',
                security: hajri_admin_object.nonce,
                ...getFormData($(this))
            },
            beforeSend: function() {
                $(e.target).find('button[type="submit"]').prop('disabled', true).text(hajri_admin_object.strings.processing);
            },
            success: function(response) {
                $(e.target).find('button[type="submit"]').prop('disabled', false).text('إضافة شركة');
                
                if (response.success) {
                    showNotification('success', response.data.message);
                    $('#hajri-add-delivery-company-form')[0].reset();
                    loadDeliveryCompanies();
                } else {
                    showNotification('error', response.data.message);
                }
            },
            error: function() {
                $(e.target).find('button[type="submit"]').prop('disabled', false).text('إضافة شركة');
                showNotification('error', hajri_admin_object.strings.error + ' ' + hajri_admin_object.strings.try_again);
            }
        });
    });
    
    // Edit company - open modal
    $(document).on('click', '.edit-company', function() {
        const companyId = $(this).data('id');
        
        // Reset form
        $('#hajri-edit-delivery-company-form')[0].reset();
        $('#edit-endpoints-container').empty();
        
        // Hide all auth fields in edit form
        $('.edit-auth-fields').hide();
        
        // Reset tabs to first tab
        $('.hajri-edit-tabs .hajri-tab-btn').removeClass('active');
        $('.hajri-edit-tabs .hajri-tab-btn:first-child').addClass('active');
        $('.hajri-edit-tabs .hajri-tab-content').removeClass('active');
        $('.hajri-edit-tabs .hajri-tab-content:first-child').addClass('active');
        
        // Show loading in form
        const $form = $('#hajri-edit-delivery-company-form');
        $form.find('button[type="submit"]').prop('disabled', true).text(hajri_admin_object.strings.loading || 'جاري التحميل...');
        
        // Load company data
        $.ajax({
            url: hajri_admin_object.ajax_url,
            type: 'POST',
            data: {
                action: 'get_delivery_company',
                security: hajri_admin_object.nonce,
                id: companyId
            },
            success: function(response) {
                $form.find('button[type="submit"]').prop('disabled', false).text(hajri_admin_object.strings.save || 'حفظ التغييرات');
                
                if (response.success && response.data.company) {
                    const company = response.data.company;
                    
                    // Set form values
                    $('#edit_company_id').val(company.id);
                    $('#edit_company_name').val(company.company_name);
                    $('#edit_company_code').val(company.company_code);
                    $('#edit_api_base_url').val(company.api_base_url);
                    $('#edit_auth_type').val(company.auth_type);
                    $('#edit_is_active').val(company.is_active);
                    $('#edit_api_version').val(company.api_version);
                    $('#edit_description').val(company.description);
                    
                    // API credentials
                    $('#edit_api_id').val(company.api_id);
                    $('#edit_api_key').val(company.api_key);
                    $('#edit_api_secret').val(company.api_secret);
                    $('#edit_token').val(company.token);
                    $('#edit_username').val(company.username);
                    $('#edit_password').val(''); // Leave password blank for security
                    $('#edit_webhook_url').val(company.webhook_url);
                    
                    // Show auth fields based on auth type
                    if (company.auth_type) {
                        $(`.edit-${company.auth_type}-fields`).show();
                    }
                    
                    // Load endpoints
                    if (company.endpoints) {
                        let endpointCounter = 0;
                        
                        // Try to parse endpoints as object if it's a string
                        let endpoints = company.endpoints;
                        if (typeof endpoints === 'string') {
                            try {
                                endpoints = JSON.parse(endpoints);
                            } catch (e) {
                                endpoints = {};
                            }
                        }
                        
                        // Add endpoints to form
                        if (typeof endpoints === 'object') {
                            for (const [key, value] of Object.entries(endpoints)) {
                                const endpointHtml = `
                                    <div class="hajri-form-row endpoint-row">
                                        <div class="hajri-form-group">
                                            <label for="edit_endpoint_key_${endpointCounter}">${hajri_admin_object.strings.endpoint_key || 'المفتاح'}</label>
                                            <input type="text" id="edit_endpoint_key_${endpointCounter}" name="edit_endpoint_keys[]" class="regular-text" value="${key}">
                                        </div>
                                        <div class="hajri-form-group">
                                            <label for="edit_endpoint_value_${endpointCounter}">${hajri_admin_object.strings.endpoint_path || 'المسار'}</label>
                                            <input type="text" id="edit_endpoint_value_${endpointCounter}" name="edit_endpoint_values[]" class="regular-text" value="${value}">
                                        </div>
                                        <div class="endpoint-actions">
                                            <button type="button" class="button remove-endpoint">${hajri_admin_object.strings.remove || 'حذف'}</button>
                                        </div>
                                    </div>
                                `;
                                
                                $('#edit-endpoints-container').append(endpointHtml);
                                endpointCounter++;
                            }
                        }
                        
                        // If no endpoints were added, add a default empty one
                        if (endpointCounter === 0) {
                            $('#edit-add-endpoint').trigger('click');
                        }
                        
                        // Disable remove button if only one endpoint
                        if ($('#edit-endpoints-container .endpoint-row').length === 1) {
                            $('#edit-endpoints-container .endpoint-row:first-child .remove-endpoint').prop('disabled', true);
                        }
                    }
                    
                    // Show modal
                    $('#hajri-edit-company-modal').show();
                } else {
                    showNotification('error', response.data.message || hajri_admin_object.strings.load_error || 'حدث خطأ أثناء تحميل بيانات الشركة');
                }
            },
            error: function() {
                $form.find('button[type="submit"]').prop('disabled', false).text(hajri_admin_object.strings.save || 'حفظ التغييرات');
                showNotification('error', hajri_admin_object.strings.error + ' ' + hajri_admin_object.strings.try_again);
            }
        });
    });
    
    // Close modal
    $('.hajri-modal-close').on('click', function() {
        $('.hajri-modal').hide();
    });
    
    // Close modal when clicking outside
    $(window).on('click', function(e) {
        if ($(e.target).hasClass('hajri-modal')) {
            $('.hajri-modal').hide();
        }
    });
    
    // Edit company - submit form
    $('#hajri-edit-delivery-company-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        
        $.ajax({
            url: hajri_admin_object.ajax_url,
            type: 'POST',
            data: {
                action: 'update_delivery_company',
                security: hajri_admin_object.nonce,
                ...getFormData($(this))
            },
            beforeSend: function() {
                $(e.target).find('button[type="submit"]').prop('disabled', true).text(hajri_admin_object.strings.processing);
            },
            success: function(response) {
                $(e.target).find('button[type="submit"]').prop('disabled', false).text('حفظ التغييرات');
                
                if (response.success) {
                    showNotification('success', response.data.message);
                    $('#hajri-edit-company-modal').hide();
                    loadDeliveryCompanies();
                } else {
                    showNotification('error', response.data.message);
                }
            },
            error: function() {
                $(e.target).find('button[type="submit"]').prop('disabled', false).text('حفظ التغييرات');
                showNotification('error', hajri_admin_object.strings.error + ' ' + hajri_admin_object.strings.try_again);
            }
        });
    });
    
    // Delete company
    $(document).on('click', '.delete-company', function() {
        if (!confirm(hajri_admin_object.strings.confirm_delete)) {
            return;
        }
        
        const companyId = $(this).data('id');
        
        $.ajax({
            url: hajri_admin_object.ajax_url,
            type: 'POST',
            data: {
                action: 'delete_delivery_company',
                security: hajri_admin_object.nonce,
                id: companyId
            },
            success: function(response) {
                if (response.success) {
                    showNotification('success', response.data.message);
                    loadDeliveryCompanies();
                } else {
                    showNotification('error', response.data.message);
                }
            },
            error: function() {
                showNotification('error', hajri_admin_object.strings.error + ' ' + hajri_admin_object.strings.try_again);
            }
        });
    });
    
    // Toggle company active status
    $(document).on('click', '.toggle-status', function() {
        const companyId = $(this).data('id');
        const isActive = $(this).data('active');
        const confirmMsg = isActive == 1 
            ? 'هل أنت متأكد من رغبتك في تعطيل هذه الشركة؟' 
            : 'هل أنت متأكد من رغبتك في تفعيل هذه الشركة؟';
            
        if (!confirm(confirmMsg)) {
            return;
        }
        
        const $button = $(this);
        $button.prop('disabled', true);
        
        $.ajax({
            url: hajri_admin_object.ajax_url,
            type: 'POST',
            data: {
                action: 'toggle_delivery_company_status',
                security: hajri_admin_object.nonce,
                id: companyId
            },
            success: function(response) {
                $button.prop('disabled', false);
                
                if (response.success) {
                    showNotification('success', response.data.message);
                    loadDeliveryCompanies();
                } else {
                    showNotification('error', response.data.message);
                }
            },
            error: function() {
                $button.prop('disabled', false);
                showNotification('error', hajri_admin_object.strings.error + ' ' + hajri_admin_object.strings.try_again);
            }
        });
    });
    
    // Test API connection
    $(document).on('click', '.test-api', function() {
        const companyId = $(this).data('id');
        const $button = $(this);
        
        $button.prop('disabled', true).find('.dashicons').removeClass('dashicons-rest-api').addClass('dashicons-update');
        
        $.ajax({
            url: hajri_admin_object.ajax_url,
            type: 'POST',
            data: {
                action: 'test_delivery_company_api',
                security: hajri_admin_object.nonce,
                id: companyId
            },
            success: function(response) {
                $button.prop('disabled', false).find('.dashicons').removeClass('dashicons-update').addClass('dashicons-rest-api');
                
                if (response.success) {
                    showNotification('success', response.data.message);
                } else {
                    showNotification('error', response.data.message);
                }
            },
            error: function() {
                $button.prop('disabled', false).find('.dashicons').removeClass('dashicons-update').addClass('dashicons-rest-api');
                showNotification('error', hajri_admin_object.strings.error + ' ' + hajri_admin_object.strings.try_again);
            }
        });
    });
    
    // Helper function to show notifications
    function showNotification(type, message) {
        const $notification = $('#hajri-delivery-companies-notification');
        
        $notification.removeClass('notice-success notice-error').addClass(type === 'success' ? 'notice-success' : 'notice-error');
        $notification.find('p').text(message);
        $notification.show();
        
        setTimeout(function() {
            $notification.fadeOut();
        }, 3000);
    }
    
    // Helper function to get form data as an object
    function getFormData($form) {
        const formData = {};
        $form.find('input, textarea, select').each(function() {
            formData[$(this).attr('name')] = $(this).val();
        });
        return formData;
    }
    
    // Tab switching
    $('.hajri-tab-btn').on('click', function() {
        const tabId = $(this).data('tab');
        
        // Update active button
        $('.hajri-tab-btn').removeClass('active');
        $(this).addClass('active');
        
        // Show active content
        $('.hajri-tab-content').removeClass('active');
        $(`.hajri-tab-content[data-tab="${tabId}"]`).addClass('active');
    });
    
    // Toggle auth fields based on selected auth type
    $('#auth_type').on('change', function() {
        const authType = $(this).val();
        
        // Hide all auth fields
        $('.auth-fields').hide();
        
        // Show fields for selected auth type
        if (authType) {
            $(`.${authType}-fields`).show();
        }
    });
    
    // Manage endpoints
    let endpointCounter = 1; // Start from 1 since we already have index 0
    
    $('#add-endpoint').on('click', function() {
        const endpointHtml = `
            <div class="hajri-form-row endpoint-row">
                <div class="hajri-form-group">
                    <label for="endpoint_key_${endpointCounter}">${hajri_admin_object.strings.endpoint_key || 'المفتاح'}</label>
                    <input type="text" id="endpoint_key_${endpointCounter}" name="endpoint_keys[]" class="regular-text" placeholder="track_shipment">
                </div>
                <div class="hajri-form-group">
                    <label for="endpoint_value_${endpointCounter}">${hajri_admin_object.strings.endpoint_path || 'المسار'}</label>
                    <input type="text" id="endpoint_value_${endpointCounter}" name="endpoint_values[]" class="regular-text" placeholder="tracking/status">
                </div>
                <div class="endpoint-actions">
                    <button type="button" class="button remove-endpoint">${hajri_admin_object.strings.remove || 'حذف'}</button>
                </div>
            </div>
        `;
        
        $('#endpoints-container').append(endpointHtml);
        endpointCounter++;
        
        // Enable first remove button if we have more than one row
        if ($('.endpoint-row').length > 1) {
            $('.endpoint-row:first-child .remove-endpoint').prop('disabled', false);
        }
    });
    
    // Remove endpoint (delegate event to container)
    $('#endpoints-container').on('click', '.remove-endpoint', function() {
        $(this).closest('.endpoint-row').remove();
        
        // Disable the last remove button if only one row remains
        if ($('.endpoint-row').length === 1) {
            $('.endpoint-row:first-child .remove-endpoint').prop('disabled', true);
        }
    });
    
    // Dynamically collect endpoint data before form submit
    $('#hajri-add-delivery-company-form').on('submit', function(e) {
        // Get endpoints data
        const endpoints = {};
        $('.endpoint-row').each(function() {
            const key = $(this).find('input[name="endpoint_keys[]"]').val();
            const value = $(this).find('input[name="endpoint_values[]"]').val();
            
            if (key && value) {
                endpoints[key] = value;
            }
        });
        
        // Add to hidden field
        if (!$('#endpoints_data').length) {
            $('<input>').attr({
                type: 'hidden',
                id: 'endpoints_data',
                name: 'endpoints',
                value: JSON.stringify(endpoints)
            }).appendTo(this);
        } else {
            $('#endpoints_data').val(JSON.stringify(endpoints));
        }
    });
    
    // Load companies on page load
    loadDeliveryCompanies();
});
</script>

<style>
.hajri-admin-page {
    margin: 20px;
}

.hajri-admin-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
    margin-bottom: 20px;
}

.hajri-admin-card-header {
    border-bottom: 1px solid #ccd0d4;
    padding: 12px 15px;
    background: #f8f9fa;
}

.hajri-admin-card-header h2 {
    margin: 0;
    font-size: 16px;
}

.hajri-admin-card-body {
    padding: 15px;
}

.hajri-form-row {
    margin-bottom: 15px;
    display: flex;
    flex-wrap: wrap;
}

.hajri-form-group {
    flex: 1;
    min-width: 200px;
    margin-right: 15px;
}

.hajri-form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
}

.hajri-form-group input,
.hajri-form-group textarea {
    width: 100%;
}

.hajri-table {
    border-collapse: collapse;
    width: 100%;
}

.hajri-table th {
    text-align: right;
}

.hajri-table th,
.hajri-table td {
    padding: 10px;
    border: 1px solid #ccd0d4;
}

.required {
    color: red;
}

/* Modal styles */
.hajri-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
}

.hajri-modal-content {
    background-color: #fefefe;
    margin: 10% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    border-radius: 4px;
    position: relative;
}

.hajri-modal-close {
    color: #aaa;
    float: left;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    position: absolute;
    top: 10px;
    left: 15px;
}

.hajri-modal-close:hover,
.hajri-modal-close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

/* Buttons */
.button {
    margin-right: 5px;
}

.delete-company {
    color: #a00;
}

.delete-company:hover {
    color: #dc3232;
}

/* Status badges */
.status-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: bold;
}

.status-active {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status-inactive {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Actions column */
.actions-column {
    white-space: nowrap;
}

/* Tabs */
.hajri-tabs {
    margin-bottom: 20px;
    border: 1px solid #ccd0d4;
    border-radius: 3px;
    overflow: hidden;
}

.hajri-tab-header {
    background: #f8f9fa;
    border-bottom: 1px solid #ccd0d4;
    display: flex;
}

.hajri-tab-btn {
    background: transparent;
    border: none;
    padding: 10px 15px;
    cursor: pointer;
    font-weight: 600;
    border-right: 1px solid #ccd0d4;
    outline: none;
}

.hajri-tab-btn:hover {
    background: #f0f0f1;
}

.hajri-tab-btn.active {
    background: #fff;
    border-bottom: 2px solid #2271b1;
    position: relative;
    top: 1px;
}

.hajri-tab-content {
    display: none;
    padding: 15px;
}

.hajri-tab-content.active {
    display: block;
}

/* Auth field groups */
.auth-fields {
    display: none;
}

/* Help text */
.help-text {
    display: block;
    color: #666;
    font-style: italic;
    margin-top: 3px;
}

/* Endpoints */
.endpoint-row {
    position: relative;
    padding-right: 40px;
}

.endpoint-actions {
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
}

/* Form actions */
.form-actions {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #ccd0d4;
}
</style>