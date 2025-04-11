<?php
/**
 * إدارة الولايات والبلديات الجزائرية
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/admin/partials
 */

// Direct access protection
defined('WPINC') || die;
?>

<div class="wrap hajri-locations-admin">
    <h1><?php echo esc_html__('Algerian Locations (Wilayas & Municipalities)', 'hajri-cod-shop'); ?></h1>
    
    <!-- Tabs for locations management -->
    <nav class="nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e('Secondary menu', 'hajri-cod-shop'); ?>">
        <a href="#wilayas-tab" class="nav-tab nav-tab-active" id="wilayas-tab-link"><?php esc_html_e('Wilayas (Provinces)', 'hajri-cod-shop'); ?></a>
        <a href="#municipalities-tab" class="nav-tab" id="municipalities-tab-link"><?php esc_html_e('Municipalities', 'hajri-cod-shop'); ?></a>
        <a href="#import-tab" class="nav-tab" id="import-tab-link"><?php esc_html_e('Import/Export', 'hajri-cod-shop'); ?></a>
    </nav>
    
    <div class="tab-content">
        <!-- Wilayas Tab -->
        <div id="wilayas-tab" class="tab-pane active">
            <div class="wilayas-controls">
                <h2><?php esc_html_e('Manage Wilayas', 'hajri-cod-shop'); ?></h2>
                <div class="actions-row">
                    <button type="button" id="add-wilaya-btn" class="button button-primary">
                        <span class="dashicons dashicons-plus"></span> <?php esc_html_e('Add New Wilaya', 'hajri-cod-shop'); ?>
                    </button>
                    <div class="search-box">
                        <input type="search" id="wilaya-search" placeholder="<?php esc_attr_e('Search wilayas...', 'hajri-cod-shop'); ?>">
                        <button type="button" class="button"><?php esc_html_e('Search', 'hajri-cod-shop'); ?></button>
                    </div>
                </div>
            </div>
            
            <div class="table-container wilayas-table-container">
                <table class="wp-list-table widefat fixed striped wilayas-table">
                    <thead>
                        <tr>
                            <th class="column-code"><?php esc_html_e('Code', 'hajri-cod-shop'); ?></th>
                            <th class="column-name"><?php esc_html_e('Name (FR)', 'hajri-cod-shop'); ?></th>
                            <th class="column-name-ar"><?php esc_html_e('Name (AR)', 'hajri-cod-shop'); ?></th>
                            <th class="column-shipping"><?php esc_html_e('Shipping Cost', 'hajri-cod-shop'); ?></th>
                            <th class="column-delivery"><?php esc_html_e('Delivery Days', 'hajri-cod-shop'); ?></th>
                            <th class="column-status"><?php esc_html_e('Status', 'hajri-cod-shop'); ?></th>
                            <th class="column-actions"><?php esc_html_e('Actions', 'hajri-cod-shop'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="wilayas-list">
                        <!-- Wilayas will be loaded here via AJAX -->
                        <tr class="no-items">
                            <td class="colspanchange" colspan="7"><?php esc_html_e('Loading wilayas...', 'hajri-cod-shop'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Municipalities Tab -->
        <div id="municipalities-tab" class="tab-pane">
            <div class="municipalities-controls">
                <h2><?php esc_html_e('Manage Municipalities', 'hajri-cod-shop'); ?></h2>
                <div class="actions-row">
                    <button type="button" id="add-municipality-btn" class="button button-primary">
                        <span class="dashicons dashicons-plus"></span> <?php esc_html_e('Add New Municipality', 'hajri-cod-shop'); ?>
                    </button>
                    <div class="wilaya-filter">
                        <label for="wilaya-filter-select"><?php esc_html_e('Filter by Wilaya:', 'hajri-cod-shop'); ?></label>
                        <select id="wilaya-filter-select">
                            <option value=""><?php esc_html_e('All Wilayas', 'hajri-cod-shop'); ?></option>
                            <!-- Wilayas will be loaded here via AJAX -->
                        </select>
                    </div>
                    <div class="search-box">
                        <input type="search" id="municipality-search" placeholder="<?php esc_attr_e('Search municipalities...', 'hajri-cod-shop'); ?>">
                        <button type="button" class="button"><?php esc_html_e('Search', 'hajri-cod-shop'); ?></button>
                    </div>
                </div>
            </div>
            
            <div class="table-container municipalities-table-container">
                <table class="wp-list-table widefat fixed striped municipalities-table">
                    <thead>
                        <tr>
                            <th class="column-code"><?php esc_html_e('Code', 'hajri-cod-shop'); ?></th>
                            <th class="column-name"><?php esc_html_e('Name (FR)', 'hajri-cod-shop'); ?></th>
                            <th class="column-name-ar"><?php esc_html_e('Name (AR)', 'hajri-cod-shop'); ?></th>
                            <th class="column-wilaya"><?php esc_html_e('Wilaya', 'hajri-cod-shop'); ?></th>
                            <th class="column-postal"><?php esc_html_e('Postal Code', 'hajri-cod-shop'); ?></th>
                            <th class="column-fee"><?php esc_html_e('Extra Fee', 'hajri-cod-shop'); ?></th>
                            <th class="column-status"><?php esc_html_e('Status', 'hajri-cod-shop'); ?></th>
                            <th class="column-actions"><?php esc_html_e('Actions', 'hajri-cod-shop'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="municipalities-list">
                        <!-- Municipalities will be loaded here via AJAX -->
                        <tr class="no-items">
                            <td class="colspanchange" colspan="8"><?php esc_html_e('Select a wilaya to view municipalities', 'hajri-cod-shop'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Import/Export Tab -->
        <div id="import-tab" class="tab-pane">
            <h2><?php esc_html_e('Import/Export Locations', 'hajri-cod-shop'); ?></h2>
            
            <div class="card import-export-card">
                <h3><?php esc_html_e('Import Locations from JSON', 'hajri-cod-shop'); ?></h3>
                <p><?php esc_html_e('You can import wilayas and municipalities from a JSON file. The JSON file should follow the required format.', 'hajri-cod-shop'); ?></p>
                
                <form id="import-locations-form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="import_locations_json">
                    <input type="hidden" name="security" value="<?php echo wp_create_nonce('hajri_cod_shop_nonce'); ?>">
                    
                    <div class="form-field">
                        <label for="locations-file"><?php esc_html_e('JSON File:', 'hajri-cod-shop'); ?></label>
                        <input type="file" name="locations_file" id="locations-file" accept=".json" required>
                    </div>
                    
                    <div class="form-submit">
                        <button type="submit" class="button button-primary">
                            <span class="dashicons dashicons-upload"></span> <?php esc_html_e('Import Locations', 'hajri-cod-shop'); ?>
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="card import-export-card">
                <h3><?php esc_html_e('JSON Format Example', 'hajri-cod-shop'); ?></h3>
                <p><?php esc_html_e('The JSON file should follow this format:', 'hajri-cod-shop'); ?></p>
                
                <pre>
{
    "wilayas": [
        {
            "code": "16",
            "name": "Alger",
            "name_ar": "الجزائر",
            "shipping_cost": 600,
            "delivery_days": 3,
            "municipalities": [
                {
                    "code": "1601",
                    "name": "Alger Centre",
                    "name_ar": "الجزائر وسط",
                    "postal_code": "16000",
                    "extra_fee": 0
                },
                {
                    "code": "1602",
                    "name": "Bab El Oued",
                    "name_ar": "باب الوادي",
                    "postal_code": "16006",
                    "extra_fee": 0
                }
            ]
        }
    ]
}
                </pre>
            </div>
        </div>
    </div>
</div>

<!-- Templates for modal forms -->
<!-- Wilaya Modal Form -->
<div id="wilaya-modal" class="hajri-modal" style="display: none;">
    <div class="hajri-modal-content">
        <div class="hajri-modal-header">
            <span class="hajri-modal-close">&times;</span>
            <h2 id="wilaya-modal-title"><?php esc_html_e('Add New Wilaya', 'hajri-cod-shop'); ?></h2>
        </div>
        <div class="hajri-modal-body">
            <form id="wilaya-form">
                <input type="hidden" id="wilaya-id" name="id" value="">
                <input type="hidden" name="action" value="create_wilaya">
                <input type="hidden" name="security" value="<?php echo wp_create_nonce('hajri_cod_shop_nonce'); ?>">
                
                <div class="form-field">
                    <label for="wilaya-code"><?php esc_html_e('Wilaya Code:', 'hajri-cod-shop'); ?></label>
                    <input type="text" id="wilaya-code" name="wilaya_code" required pattern="[0-9]{1,2}" title="<?php esc_attr_e('Enter a 1 or 2-digit code (e.g. 16)', 'hajri-cod-shop'); ?>">
                    <p class="description"><?php esc_html_e('Enter a 1 or 2-digit numeric code (e.g. 16 for Algiers)', 'hajri-cod-shop'); ?></p>
                </div>
                
                <div class="form-field">
                    <label for="wilaya-name"><?php esc_html_e('Wilaya Name (French):', 'hajri-cod-shop'); ?></label>
                    <input type="text" id="wilaya-name" name="wilaya_name" required>
                </div>
                
                <div class="form-field">
                    <label for="wilaya-name-ar"><?php esc_html_e('Wilaya Name (Arabic):', 'hajri-cod-shop'); ?></label>
                    <input type="text" id="wilaya-name-ar" name="wilaya_name_ar" required dir="rtl">
                </div>
                
                <div class="form-field">
                    <label for="shipping-cost"><?php esc_html_e('Shipping Cost (DZD):', 'hajri-cod-shop'); ?></label>
                    <input type="number" id="shipping-cost" name="shipping_cost" min="0" step="50" value="500">
                </div>
                
                <div class="form-field">
                    <label for="delivery-days"><?php esc_html_e('Delivery Days:', 'hajri-cod-shop'); ?></label>
                    <input type="number" id="delivery-days" name="delivery_days" min="1" max="30" value="3">
                </div>
                
                <div class="form-field">
                    <label for="is-active"><?php esc_html_e('Status:', 'hajri-cod-shop'); ?></label>
                    <select id="is-active" name="is_active">
                        <option value="1"><?php esc_html_e('Active', 'hajri-cod-shop'); ?></option>
                        <option value="0"><?php esc_html_e('Inactive', 'hajri-cod-shop'); ?></option>
                    </select>
                </div>
                
                <div class="form-submit">
                    <button type="submit" class="button button-primary" id="save-wilaya-btn">
                        <?php esc_html_e('Save Wilaya', 'hajri-cod-shop'); ?>
                    </button>
                    <button type="button" class="button hajri-modal-cancel">
                        <?php esc_html_e('Cancel', 'hajri-cod-shop'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Municipality Modal Form -->
<div id="municipality-modal" class="hajri-modal" style="display: none;">
    <div class="hajri-modal-content">
        <div class="hajri-modal-header">
            <span class="hajri-modal-close">&times;</span>
            <h2 id="municipality-modal-title"><?php esc_html_e('Add New Municipality', 'hajri-cod-shop'); ?></h2>
        </div>
        <div class="hajri-modal-body">
            <form id="municipality-form">
                <input type="hidden" id="municipality-id" name="id" value="">
                <input type="hidden" name="action" value="create_municipality">
                <input type="hidden" name="security" value="<?php echo wp_create_nonce('hajri_cod_shop_nonce'); ?>">
                
                <div class="form-field">
                    <label for="wilaya-id-select"><?php esc_html_e('Wilaya:', 'hajri-cod-shop'); ?></label>
                    <select id="wilaya-id-select" name="wilaya_id" required>
                        <option value=""><?php esc_html_e('Select a Wilaya', 'hajri-cod-shop'); ?></option>
                        <!-- Options will be populated via AJAX -->
                    </select>
                </div>
                
                <div class="form-field">
                    <label for="municipality-code"><?php esc_html_e('Municipality Code:', 'hajri-cod-shop'); ?></label>
                    <input type="text" id="municipality-code" name="municipality_code" required pattern="[0-9]{4}" title="<?php esc_attr_e('Enter a 4-digit code (e.g. 1601)', 'hajri-cod-shop'); ?>">
                    <p class="description"><?php esc_html_e('Enter a 4-digit code (e.g. 1601 for Algiers Centre)', 'hajri-cod-shop'); ?></p>
                </div>
                
                <div class="form-field">
                    <label for="municipality-name"><?php esc_html_e('Municipality Name (French):', 'hajri-cod-shop'); ?></label>
                    <input type="text" id="municipality-name" name="municipality_name" required>
                </div>
                
                <div class="form-field">
                    <label for="municipality-name-ar"><?php esc_html_e('Municipality Name (Arabic):', 'hajri-cod-shop'); ?></label>
                    <input type="text" id="municipality-name-ar" name="municipality_name_ar" required dir="rtl">
                </div>
                
                <div class="form-field">
                    <label for="postal-code"><?php esc_html_e('Postal Code:', 'hajri-cod-shop'); ?></label>
                    <input type="text" id="postal-code" name="postal_code" pattern="[0-9]{5}" title="<?php esc_attr_e('Enter a 5-digit postal code (e.g. 16000)', 'hajri-cod-shop'); ?>">
                </div>
                
                <div class="form-field">
                    <label for="extra-fee"><?php esc_html_e('Extra Fee (DZD):', 'hajri-cod-shop'); ?></label>
                    <input type="number" id="extra-fee" name="extra_fee" min="0" step="50" value="0">
                    <p class="description"><?php esc_html_e('Additional fee to be added to the wilaya shipping cost', 'hajri-cod-shop'); ?></p>
                </div>
                
                <div class="form-field">
                    <label for="municipality-is-active"><?php esc_html_e('Status:', 'hajri-cod-shop'); ?></label>
                    <select id="municipality-is-active" name="is_active">
                        <option value="1"><?php esc_html_e('Active', 'hajri-cod-shop'); ?></option>
                        <option value="0"><?php esc_html_e('Inactive', 'hajri-cod-shop'); ?></option>
                    </select>
                </div>
                
                <div class="form-submit">
                    <button type="submit" class="button button-primary" id="save-municipality-btn">
                        <?php esc_html_e('Save Municipality', 'hajri-cod-shop'); ?>
                    </button>
                    <button type="button" class="button hajri-modal-cancel">
                        <?php esc_html_e('Cancel', 'hajri-cod-shop'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-confirmation-modal" class="hajri-modal" style="display: none;">
    <div class="hajri-modal-content">
        <div class="hajri-modal-header">
            <span class="hajri-modal-close">&times;</span>
            <h2 id="delete-modal-title"><?php esc_html_e('Confirm Deletion', 'hajri-cod-shop'); ?></h2>
        </div>
        <div class="hajri-modal-body">
            <p id="delete-confirmation-message"><?php esc_html_e('Are you sure you want to delete this item? This action cannot be undone.', 'hajri-cod-shop'); ?></p>
            
            <div class="form-submit">
                <button type="button" class="button button-primary" id="confirm-delete-btn">
                    <?php esc_html_e('Yes, Delete It', 'hajri-cod-shop'); ?>
                </button>
                <button type="button" class="button hajri-modal-cancel">
                    <?php esc_html_e('Cancel', 'hajri-cod-shop'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Tab switching
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        
        // Update active tab
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        // Show corresponding tab content
        var targetTab = $(this).attr('href');
        $('.tab-pane').removeClass('active');
        $(targetTab).addClass('active');
        
        // Load data for specific tabs if needed
        if (targetTab === '#wilayas-tab') {
            loadWilayas();
        } else if (targetTab === '#municipalities-tab') {
            loadWilayaDropdowns();
            if ($('#wilaya-filter-select').val()) {
                loadMunicipalities();
            }
        }
    });
    
    // Load wilayas on page load
    loadWilayas();
    loadWilayaDropdowns();
    
    // Load municipalities when wilaya filter changes
    $('#wilaya-filter-select').on('change', function() {
        loadMunicipalities();
    });
    
    // Add new wilaya button
    $('#add-wilaya-btn').on('click', function() {
        resetWilayaForm();
        $('#wilaya-modal-title').text('<?php echo esc_js(__('Add New Wilaya', 'hajri-cod-shop')); ?>');
        $('#wilaya-form [name="action"]').val('create_wilaya');
        $('#wilaya-modal').show();
    });
    
    // Add new municipality button
    $('#add-municipality-btn').on('click', function() {
        resetMunicipalityForm();
        $('#municipality-modal-title').text('<?php echo esc_js(__('Add New Municipality', 'hajri-cod-shop')); ?>');
        $('#municipality-form [name="action"]').val('create_municipality');
        $('#municipality-modal').show();
    });
    
    // Modal close buttons
    $('.hajri-modal-close, .hajri-modal-cancel').on('click', function() {
        $(this).closest('.hajri-modal').hide();
    });
    
    // Close modal if clicked outside
    $(window).on('click', function(e) {
        if ($(e.target).hasClass('hajri-modal')) {
            $('.hajri-modal').hide();
        }
    });
    
    // Submit wilaya form
    $('#wilaya-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var actionType = $('#wilaya-form [name="action"]').val();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    alert(response.data.message);
                    
                    // Close modal and reload wilayas
                    $('#wilaya-modal').hide();
                    loadWilayas();
                    loadWilayaDropdowns();
                } else {
                    // Show error message
                    alert(response.data.message);
                }
            },
            error: function() {
                alert('<?php echo esc_js(__('An error occurred. Please try again.', 'hajri-cod-shop')); ?>');
            }
        });
    });
    
    // Submit municipality form
    $('#municipality-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var actionType = $('#municipality-form [name="action"]').val();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    alert(response.data.message);
                    
                    // Close modal and reload municipalities
                    $('#municipality-modal').hide();
                    loadMunicipalities();
                } else {
                    // Show error message
                    alert(response.data.message);
                }
            },
            error: function() {
                alert('<?php echo esc_js(__('An error occurred. Please try again.', 'hajri-cod-shop')); ?>');
            }
        });
    });
    
    // Import locations form
    $('#import-locations-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    alert(response.data.message);
                    
                    // Reload wilayas and municipalities
                    loadWilayas();
                    loadWilayaDropdowns();
                } else {
                    // Show error message
                    alert(response.data.message);
                }
            },
            error: function() {
                alert('<?php echo esc_js(__('An error occurred. Please try again.', 'hajri-cod-shop')); ?>');
            }
        });
    });
    
    // Function to load wilayas
    function loadWilayas() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'get_wilayas',
                security: '<?php echo wp_create_nonce('hajri_cod_shop_nonce'); ?>'
            },
            success: function(response) {
                if (response.success && response.data.wilayas) {
                    var wilayas = response.data.wilayas;
                    var html = '';
                    
                    if (wilayas.length === 0) {
                        html = '<tr class="no-items"><td class="colspanchange" colspan="7"><?php echo esc_js(__('No wilayas found.', 'hajri-cod-shop')); ?></td></tr>';
                    } else {
                        for (var i = 0; i < wilayas.length; i++) {
                            var wilaya = wilayas[i];
                            var statusClass = wilaya.is_active == 1 ? 'status-active' : 'status-inactive';
                            var statusText = wilaya.is_active == 1 ? '<?php echo esc_js(__('Active', 'hajri-cod-shop')); ?>' : '<?php echo esc_js(__('Inactive', 'hajri-cod-shop')); ?>';
                            
                            html += '<tr data-id="' + wilaya.id + '">';
                            html += '<td>' + wilaya.wilaya_code + '</td>';
                            html += '<td>' + wilaya.wilaya_name + '</td>';
                            html += '<td dir="rtl">' + wilaya.wilaya_name_ar + '</td>';
                            html += '<td>' + wilaya.shipping_cost + ' <?php echo esc_js(__('DZD', 'hajri-cod-shop')); ?></td>';
                            html += '<td>' + wilaya.delivery_days + ' <?php echo esc_js(__('days', 'hajri-cod-shop')); ?></td>';
                            html += '<td><span class="status-badge ' + statusClass + '">' + statusText + '</span></td>';
                            html += '<td class="actions">';
                            html += '<button type="button" class="button edit-wilaya" data-id="' + wilaya.id + '"><?php echo esc_js(__('Edit', 'hajri-cod-shop')); ?></button>';
                            html += '<button type="button" class="button toggle-wilaya-status" data-id="' + wilaya.id + '" data-status="' + wilaya.is_active + '">';
                            html += wilaya.is_active == 1 ? '<?php echo esc_js(__('Deactivate', 'hajri-cod-shop')); ?>' : '<?php echo esc_js(__('Activate', 'hajri-cod-shop')); ?>';
                            html += '</button>';
                            html += '<button type="button" class="button button-link-delete delete-wilaya" data-id="' + wilaya.id + '"><?php echo esc_js(__('Delete', 'hajri-cod-shop')); ?></button>';
                            html += '</td>';
                            html += '</tr>';
                        }
                    }
                    
                    $('#wilayas-list').html(html);
                    setupWilayaEventListeners();
                } else {
                    $('#wilayas-list').html('<tr class="no-items"><td class="colspanchange" colspan="7"><?php echo esc_js(__('Error loading wilayas.', 'hajri-cod-shop')); ?></td></tr>');
                }
            },
            error: function() {
                $('#wilayas-list').html('<tr class="no-items"><td class="colspanchange" colspan="7"><?php echo esc_js(__('Error loading wilayas.', 'hajri-cod-shop')); ?></td></tr>');
            }
        });
    }
    
    // Function to load municipalities
    function loadMunicipalities() {
        var wilayaId = $('#wilaya-filter-select').val();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'get_municipalities',
                wilaya_id: wilayaId,
                security: '<?php echo wp_create_nonce('hajri_cod_shop_nonce'); ?>'
            },
            success: function(response) {
                if (response.success && response.data.municipalities) {
                    var municipalities = response.data.municipalities;
                    var html = '';
                    
                    if (municipalities.length === 0) {
                        html = '<tr class="no-items"><td class="colspanchange" colspan="8"><?php echo esc_js(__('No municipalities found.', 'hajri-cod-shop')); ?></td></tr>';
                    } else {
                        for (var i = 0; i < municipalities.length; i++) {
                            var municipality = municipalities[i];
                            var statusClass = municipality.is_active == 1 ? 'status-active' : 'status-inactive';
                            var statusText = municipality.is_active == 1 ? '<?php echo esc_js(__('Active', 'hajri-cod-shop')); ?>' : '<?php echo esc_js(__('Inactive', 'hajri-cod-shop')); ?>';
                            
                            html += '<tr data-id="' + municipality.id + '">';
                            html += '<td>' + municipality.municipality_code + '</td>';
                            html += '<td>' + municipality.municipality_name + '</td>';
                            html += '<td dir="rtl">' + municipality.municipality_name_ar + '</td>';
                            html += '<td>' + municipality.wilaya_name + ' (' + municipality.wilaya_name_ar + ')</td>';
                            html += '<td>' + (municipality.postal_code || '—') + '</td>';
                            html += '<td>' + municipality.extra_fee + ' <?php echo esc_js(__('DZD', 'hajri-cod-shop')); ?></td>';
                            html += '<td><span class="status-badge ' + statusClass + '">' + statusText + '</span></td>';
                            html += '<td class="actions">';
                            html += '<button type="button" class="button edit-municipality" data-id="' + municipality.id + '"><?php echo esc_js(__('Edit', 'hajri-cod-shop')); ?></button>';
                            html += '<button type="button" class="button toggle-municipality-status" data-id="' + municipality.id + '" data-status="' + municipality.is_active + '">';
                            html += municipality.is_active == 1 ? '<?php echo esc_js(__('Deactivate', 'hajri-cod-shop')); ?>' : '<?php echo esc_js(__('Activate', 'hajri-cod-shop')); ?>';
                            html += '</button>';
                            html += '<button type="button" class="button button-link-delete delete-municipality" data-id="' + municipality.id + '"><?php echo esc_js(__('Delete', 'hajri-cod-shop')); ?></button>';
                            html += '</td>';
                            html += '</tr>';
                        }
                    }
                    
                    $('#municipalities-list').html(html);
                    setupMunicipalityEventListeners();
                } else {
                    $('#municipalities-list').html('<tr class="no-items"><td class="colspanchange" colspan="8"><?php echo esc_js(__('Error loading municipalities.', 'hajri-cod-shop')); ?></td></tr>');
                }
            },
            error: function() {
                $('#municipalities-list').html('<tr class="no-items"><td class="colspanchange" colspan="8"><?php echo esc_js(__('Error loading municipalities.', 'hajri-cod-shop')); ?></td></tr>');
            }
        });
    }
    
    // Function to load wilaya dropdowns
    function loadWilayaDropdowns() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'get_wilayas',
                security: '<?php echo wp_create_nonce('hajri_cod_shop_nonce'); ?>'
            },
            success: function(response) {
                if (response.success && response.data.wilayas) {
                    var wilayas = response.data.wilayas;
                    var filterHtml = '<option value=""><?php echo esc_js(__('All Wilayas', 'hajri-cod-shop')); ?></option>';
                    var selectHtml = '<option value=""><?php echo esc_js(__('Select a Wilaya', 'hajri-cod-shop')); ?></option>';
                    
                    for (var i = 0; i < wilayas.length; i++) {
                        var wilaya = wilayas[i];
                        filterHtml += '<option value="' + wilaya.id + '">' + wilaya.wilaya_name + ' (' + wilaya.wilaya_code + ')</option>';
                        selectHtml += '<option value="' + wilaya.id + '">' + wilaya.wilaya_name + ' (' + wilaya.wilaya_code + ')</option>';
                    }
                    
                    $('#wilaya-filter-select').html(filterHtml);
                    $('#wilaya-id-select').html(selectHtml);
                }
            }
        });
    }
    
    // Function to setup event listeners for wilaya actions
    function setupWilayaEventListeners() {
        // Edit wilaya
        $('.edit-wilaya').on('click', function() {
            var wilayaId = $(this).data('id');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_wilaya',
                    id: wilayaId,
                    security: '<?php echo wp_create_nonce('hajri_cod_shop_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success && response.data.wilaya) {
                        var wilaya = response.data.wilaya;
                        
                        // Populate form
                        $('#wilaya-id').val(wilaya.id);
                        $('#wilaya-code').val(wilaya.wilaya_code);
                        $('#wilaya-name').val(wilaya.wilaya_name);
                        $('#wilaya-name-ar').val(wilaya.wilaya_name_ar);
                        $('#shipping-cost').val(wilaya.shipping_cost);
                        $('#delivery-days').val(wilaya.delivery_days);
                        $('#is-active').val(wilaya.is_active);
                        
                        // Update form action and title
                        $('#wilaya-form [name="action"]').val('update_wilaya');
                        $('#wilaya-modal-title').text('<?php echo esc_js(__('Edit Wilaya', 'hajri-cod-shop')); ?>');
                        
                        // Show modal
                        $('#wilaya-modal').show();
                    } else {
                        alert('<?php echo esc_js(__('Error loading wilaya details.', 'hajri-cod-shop')); ?>');
                    }
                },
                error: function() {
                    alert('<?php echo esc_js(__('Error loading wilaya details.', 'hajri-cod-shop')); ?>');
                }
            });
        });
        
        // Toggle wilaya status
        $('.toggle-wilaya-status').on('click', function() {
            var wilayaId = $(this).data('id');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'toggle_wilaya_status',
                    id: wilayaId,
                    security: '<?php echo wp_create_nonce('hajri_cod_shop_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        alert(response.data.message);
                        
                        // Reload wilayas
                        loadWilayas();
                    } else {
                        // Show error message
                        alert(response.data.message);
                    }
                },
                error: function() {
                    alert('<?php echo esc_js(__('An error occurred. Please try again.', 'hajri-cod-shop')); ?>');
                }
            });
        });
        
        // Delete wilaya
        $('.delete-wilaya').on('click', function() {
            var wilayaId = $(this).data('id');
            
            // Set the confirm delete button data
            $('#confirm-delete-btn').data('type', 'wilaya');
            $('#confirm-delete-btn').data('id', wilayaId);
            
            // Update modal text
            $('#delete-modal-title').text('<?php echo esc_js(__('Delete Wilaya', 'hajri-cod-shop')); ?>');
            $('#delete-confirmation-message').text('<?php echo esc_js(__('Are you sure you want to delete this wilaya? This action cannot be undone. Note: Wilayas with municipalities cannot be deleted.', 'hajri-cod-shop')); ?>');
            
            // Show modal
            $('#delete-confirmation-modal').show();
        });
    }
    
    // Function to setup event listeners for municipality actions
    function setupMunicipalityEventListeners() {
        // Edit municipality
        $('.edit-municipality').on('click', function() {
            var municipalityId = $(this).data('id');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_municipality',
                    id: municipalityId,
                    security: '<?php echo wp_create_nonce('hajri_cod_shop_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success && response.data.municipality) {
                        var municipality = response.data.municipality;
                        
                        // Populate form
                        $('#municipality-id').val(municipality.id);
                        $('#wilaya-id-select').val(municipality.wilaya_id);
                        $('#municipality-code').val(municipality.municipality_code);
                        $('#municipality-name').val(municipality.municipality_name);
                        $('#municipality-name-ar').val(municipality.municipality_name_ar);
                        $('#postal-code').val(municipality.postal_code);
                        $('#extra-fee').val(municipality.extra_fee);
                        $('#municipality-is-active').val(municipality.is_active);
                        
                        // Update form action and title
                        $('#municipality-form [name="action"]').val('update_municipality');
                        $('#municipality-modal-title').text('<?php echo esc_js(__('Edit Municipality', 'hajri-cod-shop')); ?>');
                        
                        // Show modal
                        $('#municipality-modal').show();
                    } else {
                        alert('<?php echo esc_js(__('Error loading municipality details.', 'hajri-cod-shop')); ?>');
                    }
                },
                error: function() {
                    alert('<?php echo esc_js(__('Error loading municipality details.', 'hajri-cod-shop')); ?>');
                }
            });
        });
        
        // Toggle municipality status
        $('.toggle-municipality-status').on('click', function() {
            var municipalityId = $(this).data('id');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'toggle_municipality_status',
                    id: municipalityId,
                    security: '<?php echo wp_create_nonce('hajri_cod_shop_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        alert(response.data.message);
                        
                        // Reload municipalities
                        loadMunicipalities();
                    } else {
                        // Show error message
                        alert(response.data.message);
                    }
                },
                error: function() {
                    alert('<?php echo esc_js(__('An error occurred. Please try again.', 'hajri-cod-shop')); ?>');
                }
            });
        });
        
        // Delete municipality
        $('.delete-municipality').on('click', function() {
            var municipalityId = $(this).data('id');
            
            // Set the confirm delete button data
            $('#confirm-delete-btn').data('type', 'municipality');
            $('#confirm-delete-btn').data('id', municipalityId);
            
            // Update modal text
            $('#delete-modal-title').text('<?php echo esc_js(__('Delete Municipality', 'hajri-cod-shop')); ?>');
            $('#delete-confirmation-message').text('<?php echo esc_js(__('Are you sure you want to delete this municipality? This action cannot be undone.', 'hajri-cod-shop')); ?>');
            
            // Show modal
            $('#delete-confirmation-modal').show();
        });
    }
    
    // Confirm delete button
    $('#confirm-delete-btn').on('click', function() {
        var type = $(this).data('type');
        var id = $(this).data('id');
        var action = type === 'wilaya' ? 'delete_wilaya' : 'delete_municipality';
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: action,
                id: id,
                security: '<?php echo wp_create_nonce('hajri_cod_shop_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    alert(response.data.message);
                    
                    // Close modal
                    $('#delete-confirmation-modal').hide();
                    
                    // Reload data
                    if (type === 'wilaya') {
                        loadWilayas();
                        loadWilayaDropdowns();
                    } else {
                        loadMunicipalities();
                    }
                } else {
                    // Show error message
                    alert(response.data.message);
                }
            },
            error: function() {
                alert('<?php echo esc_js(__('An error occurred. Please try again.', 'hajri-cod-shop')); ?>');
            }
        });
    });
    
    // Function to reset wilaya form
    function resetWilayaForm() {
        $('#wilaya-id').val('');
        $('#wilaya-code').val('');
        $('#wilaya-name').val('');
        $('#wilaya-name-ar').val('');
        $('#shipping-cost').val('500');
        $('#delivery-days').val('3');
        $('#is-active').val('1');
    }
    
    // Function to reset municipality form
    function resetMunicipalityForm() {
        $('#municipality-id').val('');
        $('#wilaya-id-select').val('');
        $('#municipality-code').val('');
        $('#municipality-name').val('');
        $('#municipality-name-ar').val('');
        $('#postal-code').val('');
        $('#extra-fee').val('0');
        $('#municipality-is-active').val('1');
    }
});
</script>

<style>
.hajri-locations-admin .nav-tab-wrapper {
    margin-bottom: 20px;
}

.hajri-locations-admin .tab-pane {
    display: none;
}

.hajri-locations-admin .tab-pane.active {
    display: block;
}

.hajri-locations-admin .actions-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.hajri-locations-admin .search-box {
    display: flex;
    align-items: center;
}

.hajri-locations-admin .search-box input[type="search"] {
    margin-right: 5px;
    width: 200px;
}

.hajri-locations-admin .wilaya-filter {
    display: flex;
    align-items: center;
    margin-left: 15px;
}

.hajri-locations-admin .wilaya-filter label {
    margin-right: 5px;
}

.hajri-locations-admin .table-container {
    margin-top: 15px;
    margin-bottom: 20px;
}

.hajri-locations-admin table {
    width: 100%;
    border-collapse: collapse;
}

.hajri-locations-admin table th.column-code {
    width: 80px;
}

.hajri-locations-admin table th.column-name,
.hajri-locations-admin table th.column-name-ar {
    width: 180px;
}

.hajri-locations-admin table th.column-shipping,
.hajri-locations-admin table th.column-delivery,
.hajri-locations-admin table th.column-postal,
.hajri-locations-admin table th.column-fee {
    width: 120px;
}

.hajri-locations-admin table th.column-status {
    width: 100px;
}

.hajri-locations-admin table th.column-actions {
    width: 180px;
}

.hajri-locations-admin table td.actions {
    display: flex;
    gap: 5px;
}

.hajri-locations-admin .status-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: bold;
    text-align: center;
}

.hajri-locations-admin .status-active {
    background-color: #d4edda;
    color: #155724;
}

.hajri-locations-admin .status-inactive {
    background-color: #f8d7da;
    color: #721c24;
}

/* Modal Styles */
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
    margin: 5% auto;
    padding: 0;
    border: 1px solid #888;
    width: 50%;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
    border-radius: 4px;
}

.hajri-modal-header {
    padding: 15px 20px;
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.hajri-modal-header h2 {
    margin: 0;
    font-size: 1.25rem;
}

.hajri-modal-body {
    padding: 20px;
}

.hajri-modal-close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.hajri-modal-close:hover,
.hajri-modal-close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

/* Form Styles */
.form-field {
    margin-bottom: 15px;
}

.form-field label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-field input[type="text"],
.form-field input[type="number"],
.form-field select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.form-field .description {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
}

.form-submit {
    margin-top: 20px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* Card Styles */
.card {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 20px;
    padding: 20px;
}

.card h3 {
    margin-top: 0;
    margin-bottom: 15px;
}

pre {
    background-color: #f5f5f5;
    padding: 15px;
    border-radius: 4px;
    overflow-x: auto;
    white-space: pre-wrap;
    font-family: monospace;
}

/* Import/Export Styles */
.import-export-card {
    max-width: 800px;
}
</style>