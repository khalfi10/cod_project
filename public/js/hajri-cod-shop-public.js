/**
 * Public JavaScript for Hajri COD Shop
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/public/js
 */

(function($) {
    'use strict';

    /**
     * Initialize the public-facing functionality when the document is ready
     */
    $(document).ready(function() {
        
        // Phone number validation for Algerian numbers
        $('.hajri-phone-input').on('input', function() {
            var phoneInput = $(this);
            var phoneValue = phoneInput.val().replace(/\s+/g, ''); // Remove spaces
            
            // Check if it's a valid Algerian phone number (starts with 05, 06, or 07 and has 10 digits)
            var isValid = /^(05|06|07)[0-9]{8}$/.test(phoneValue);
            
            if (phoneValue.length > 0 && !isValid) {
                phoneInput.addClass('invalid');
                
                // Show error message if it doesn't exist
                if (phoneInput.next('.hajri-error-message').length === 0) {
                    phoneInput.after('<div class="hajri-error-message">' + hajri_shop.strings.phone_error + '</div>');
                }
            } else {
                phoneInput.removeClass('invalid');
                phoneInput.next('.hajri-error-message').remove();
            }
        });
        
        // Update summary when quantity changes
        $('.hajri-quantity-input').on('change', function() {
            var quantity = parseInt($(this).val());
            var productId = $(this).data('product-id');
            var form = $(this).closest('form');
            
            // Update quantity display in summary if it exists
            form.find('.quantity-value').text(quantity);
            
            updateOrderSummary(form);
        });
        
        // Update summary when city changes
        $('.hajri-city-select').on('change', function() {
            var city = $(this).val();
            var form = $(this).closest('form');
            
            if (city) {
                // Show loading state
                form.find('.hajri-shipping').text(hajri_shop.strings.processing);
                
                // Get shipping cost for selected city
                $.ajax({
                    url: hajri_shop.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'get_shipping_cost',
                        city: city,
                        security: hajri_shop.shipping_nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            form.find('.hajri-shipping').text(response.data.formatted_cost);
                            updateOrderSummary(form);
                        } else {
                            form.find('.hajri-shipping').text('-- ' + hajri_shop.currency);
                        }
                    },
                    error: function() {
                        form.find('.hajri-shipping').text('-- ' + hajri_shop.currency);
                    }
                });
            } else {
                form.find('.hajri-shipping').text('-- ' + hajri_shop.currency);
                updateOrderSummary(form);
            }
        });
        
        // Update order summary calculations
        function updateOrderSummary(form) {
            var productId = getProductIdFromForm(form);
            var quantity = parseInt(form.find('[name="quantity"]').val()) || 1;
            var city = form.find('[name="city"]').val();
            
            if (productId && city) {
                $.ajax({
                    url: hajri_shop.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'apply_discount',
                        product_id: productId,
                        quantity: quantity,
                        city: city,
                        security: hajri_shop.order_nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            form.find('.hajri-subtotal').text(response.data.subtotal_formatted);
                            form.find('.hajri-shipping').text(response.data.shipping_cost_formatted);
                            
                            // Show discount if available
                            if (response.data.discount_amount > 0) {
                                form.find('.hajri-discount').text('-' + response.data.discount_amount_formatted);
                                form.find('.hajri-discount-row').show();
                            } else {
                                form.find('.hajri-discount-row').hide();
                            }
                            
                            form.find('.hajri-total').text(response.data.total_formatted);
                        }
                    }
                });
            }
        }
        
        // Get the product ID from a form
        function getProductIdFromForm(form) {
            // Try to get it from the hidden input first
            var productId = form.find('[name="product_id"]').val();
            
            // If not found, try to get it from the form wrapper
            if (!productId) {
                productId = form.closest('[data-product-id]').data('product-id');
            }
            
            return productId;
        }
        
        // Product selection in standalone form
        $('.hajri-product-select-item').on('click', function() {
            var item = $(this);
            var productId = item.data('product-id');
            var form = $('.hajri-standalone-form');
            
            // Skip if product is out of stock or already selected
            if (item.find('.hajri-out-of-stock').length > 0 || item.hasClass('selected')) {
                return;
            }
            
            // Update selection state
            $('.hajri-product-select-item').removeClass('selected');
            item.addClass('selected');
            
            // Update radio button
            $('#select-product-' + productId).prop('checked', true);
            
            // Update hidden input
            $('#hajri-selected-product').val(productId);
            
            // Update product name in summary
            $('.hajri-selected-product-name').text(item.find('h4').text());
            
            // Update quantity input data attribute
            form.find('.hajri-quantity-input').data('product-id', productId);
            
            // Update summary
            updateOrderSummary(form);
        });
        
        // Save abandoned cart data as user fills the form
        let abandonedCartTimeout;
        
        $('.hajri-order-form input, .hajri-order-form textarea, .hajri-order-form select').on('change', function() {
            clearTimeout(abandonedCartTimeout);
            
            // Set a timeout to avoid too many requests
            abandonedCartTimeout = setTimeout(function() {
                saveAbandonedCart();
            }, 2000);
        });
        
        // Save abandoned cart data
        function saveAbandonedCart() {
            var form = $('form.hajri-order-form:first');
            var formData = form.serializeArray();
            var formObject = {};
            
            // Check if we have at least some data
            var hasData = false;
            
            $.each(formData, function(i, field) {
                formObject[field.name] = field.value;
                
                if (field.name !== 'security' && field.name !== 'product_id' && field.value) {
                    hasData = true;
                }
            });
            
            if (!hasData) {
                return; // Don't save if the form is empty
            }
            
            // Add the security nonce
            formObject.security = hajri_shop.order_nonce;
            formObject.action = 'save_abandoned_cart';
            
            // Save the data
            $.ajax({
                url: hajri_shop.ajax_url,
                type: 'POST',
                data: formObject,
                success: function(response) {
                    // No need to show feedback to the user
                }
            });
        }
        
        // Form submission handling
        $('.hajri-order-form').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var submitBtn = form.find('.hajri-submit-btn');
            var responseDiv = form.find('.hajri-response-messages');
            
            // Validate form
            if (!validateForm(form)) {
                return;
            }
            
            // Disable submit button and show loading state
            submitBtn.prop('disabled', true).text(hajri_shop.strings.processing);
            responseDiv.html('');
            
            // Handle reCAPTCHA if enabled
            if (hajri_shop.recaptcha && hajri_shop.recaptcha.enabled) {
                grecaptcha.ready(function() {
                    grecaptcha.execute(hajri_shop.recaptcha.site_key, {action: 'submit_order'}).then(function(token) {
                        form.find('.hajri-recaptcha-token').val(token);
                        submitOrder(form, submitBtn, responseDiv);
                    });
                });
            } else {
                submitOrder(form, submitBtn, responseDiv);
            }
        });
        
        // Validate form before submission
        function validateForm(form) {
            var isValid = true;
            
            // Check required fields
            form.find('[required]').each(function() {
                var field = $(this);
                
                if (!field.val()) {
                    field.addClass('invalid');
                    isValid = false;
                } else {
                    field.removeClass('invalid');
                }
            });
            
            // Check phone number format
            var phoneInput = form.find('.hajri-phone-input');
            var phoneValue = phoneInput.val().replace(/\s+/g, '');
            
            if (!phoneValue || !/^(05|06|07)[0-9]{8}$/.test(phoneValue)) {
                phoneInput.addClass('invalid');
                
                // Show error message if it doesn't exist
                if (phoneInput.next('.hajri-error-message').length === 0) {
                    phoneInput.after('<div class="hajri-error-message">' + hajri_shop.strings.phone_error + '</div>');
                }
                
                isValid = false;
            }
            
            if (!isValid) {
                // Scroll to the first invalid field
                var firstInvalid = form.find('.invalid:first');
                
                if (firstInvalid.length) {
                    $('html, body').animate({
                        scrollTop: firstInvalid.offset().top - 100
                    }, 500);
                }
            }
            
            return isValid;
        }
        
        // Submit order to server
        function submitOrder(form, submitBtn, responseDiv) {
            var formData = form.serialize();
            
            $.ajax({
                url: hajri_shop.ajax_url,
                type: 'POST',
                data: formData + '&action=hajri_submit_order',
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        responseDiv.html('<div class="hajri-success-message">' + response.message + '</div>');
                        
                        // Reset form
                        form[0].reset();
                        
                        // Scroll to the response message
                        $('html, body').animate({
                            scrollTop: responseDiv.offset().top - 100
                        }, 500);
                        
                        // Track conversion
                        trackOrderConversion(response.order_id, form);
                        
                        // Keep the success message visible, but re-enable the button for another order
                        submitBtn.prop('disabled', false).text(submitBtn.data('original-text') || hajri_shop.strings.add_to_cart);
                    } else {
                        // Show error message
                        responseDiv.html('<div class="hajri-error-message">' + response.message + '</div>');
                        
                        // Re-enable submit button
                        submitBtn.prop('disabled', false).text(submitBtn.data('original-text') || hajri_shop.strings.add_to_cart);
                        
                        // Scroll to the error message
                        $('html, body').animate({
                            scrollTop: responseDiv.offset().top - 100
                        }, 500);
                    }
                },
                error: function() {
                    // Show generic error message
                    responseDiv.html('<div class="hajri-error-message">' + hajri_shop.strings.error + ' ' + hajri_shop.strings.connection_error + '</div>');
                    
                    // Re-enable submit button
                    submitBtn.prop('disabled', false).text(submitBtn.data('original-text') || hajri_shop.strings.add_to_cart);
                }
            });
        }
        
        // Track order conversion for marketing pixels
        function trackOrderConversion(orderId, form) {
            // This is just a placeholder - actual tracking is handled server-side in Hajri_Cod_Shop_Marketing class
            // We can trigger any client-side tracking events here if needed
        }
        
        // Save the original button text for later
        $('.hajri-submit-btn').each(function() {
            $(this).data('original-text', $(this).text());
        });
        
        // Initialize forms by triggering city change to update shipping costs
        $('.hajri-city-select').each(function() {
            if ($(this).val()) {
                $(this).trigger('change');
            }
        });
        
    });

})(jQuery);
