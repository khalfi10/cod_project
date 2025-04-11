/**
 * Form Settings JavaScript for Hajri COD Shop
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/admin/js
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Make form fields sortable
        if ($('#form-fields-tbody').length) {
            $('#form-fields-tbody').sortable({
                handle: '.hajri-drag-handle',
                update: function(event, ui) {
                    // Update hidden input order when fields are reordered
                    updateFieldsOrder();
                }
            });
        }
        
        // Add product size
        $('#add-size-btn').on('click', function() {
            var sizeTemplate = '<div class="hajri-variation-item">' +
                '<input type="text" name="product_sizes[]" value="" class="regular-text" placeholder="مقاس جديد">' +
                '<button type="button" class="button button-secondary remove-variation">إزالة</button>' +
                '</div>';
            
            $('#product-sizes-container .hajri-variation-controls').before(sizeTemplate);
        });
        
        // Add product color
        $('#add-color-btn').on('click', function() {
            var colorTemplate = '<div class="hajri-variation-item hajri-color-item">' +
                '<input type="text" name="product_color_names[]" value="" class="regular-text" placeholder="اسم اللون">' +
                '<input type="color" name="product_color_codes[]" value="#ffffff">' +
                '<button type="button" class="button button-secondary remove-variation">إزالة</button>' +
                '</div>';
            
            $('#product-colors-container .hajri-variation-controls').before(colorTemplate);
        });
        
        // Remove variation (size or color)
        $(document).on('click', '.remove-variation', function() {
            $(this).closest('.hajri-variation-item').remove();
        });
        
        /**
         * Helper function to update field order inputs when fields are reordered
         */
        function updateFieldsOrder() {
            // Clear all hidden field order inputs first
            $('input[name="form_field_order[]"]').remove();
            
            // Add new hidden inputs with the updated order
            $('.hajri-form-field-row').each(function() {
                var fieldKey = $(this).data('field');
                $(this).find('td:last').append('<input type="hidden" name="form_field_order[]" value="' + fieldKey + '">');
            });
        }
    });

})(jQuery);