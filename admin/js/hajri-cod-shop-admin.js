/**
 * Admin JavaScript for Hajri COD Shop
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/admin/js
 */

(function($) {
    'use strict';

    /**
     * Initialize the admin functionality when the document is ready
     */
    $(document).ready(function() {
        
        // Initialize datepickers
        if ($.fn.datepicker) {
            $('.hajri-datepicker').datepicker({
                dateFormat: 'yy-mm-dd'
            });
        }

        // Tab navigation for settings page
        $('.hajri-tab').on('click', function(e) {
            e.preventDefault();
            
            // Update active tab
            $('.hajri-tab').removeClass('active');
            $(this).addClass('active');
            
            // Show the corresponding content
            var target = $(this).attr('href');
            $('.hajri-tab-content').removeClass('active');
            $(target).addClass('active');
        });

        // Load shipping costs data
        function loadShippingCosts() {
            $('.hajri-shipping-costs-manager .hajri-loader-container').show();
            
            $.ajax({
                url: hajri_admin_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_city_shipping_costs',
                    security: hajri_admin_object.nonce
                },
                success: function(response) {
                    $('.hajri-shipping-costs-manager .hajri-loader-container').hide();
                    
                    if (response.success && response.data.shipping_costs) {
                        var costs = response.data.shipping_costs;
                        var html = '';
                        
                        if (costs.length === 0) {
                            html = '<tr><td colspan="3">No shipping costs defined.</td></tr>';
                        } else {
                            $.each(costs, function(index, cost) {
                                html += '<tr>';
                                html += '<td>' + cost.city_name + '</td>';
                                html += '<td>' + parseFloat(cost.shipping_cost).toFixed(2) + '</td>';
                                html += '<td>';
                                html += '<button type="button" class="button edit-city" data-city="' + cost.city_name + '" data-cost="' + cost.shipping_cost + '">';
                                html += 'Edit';
                                html += '</button>';
                                html += '</td>';
                                html += '</tr>';
                            });
                        }
                        
                        $('#shipping-costs-table tbody').html(html);
                        
                        // Bind edit buttons
                        $('.edit-city').on('click', function() {
                            var city = $(this).data('city');
                            var cost = $(this).data('cost');
                            
                            $('#new-city').val(city);
                            $('#new-cost').val(cost);
                        });
                    } else {
                        $('#shipping-costs-table tbody').html('<tr><td colspan="3">Error loading shipping costs.</td></tr>');
                    }
                },
                error: function() {
                    $('.hajri-shipping-costs-manager .hajri-loader-container').hide();
                    $('#shipping-costs-table tbody').html('<tr><td colspan="3">Error loading shipping costs.</td></tr>');
                }
            });
        }

        // Add/update city shipping cost
        $('#add-city-btn').on('click', function() {
            var city = $('#new-city').val();
            var cost = $('#new-cost').val();
            
            if (!city || !cost) {
                $('#city-update-message').html('<div class="notice notice-error"><p>Please enter both city name and shipping cost.</p></div>');
                return;
            }
            
            $(this).prop('disabled', true);
            $('#city-update-message').html('<div class="notice notice-info"><p>' + hajri_admin_object.strings.processing + '</p></div>');
            
            $.ajax({
                url: hajri_admin_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'update_city_shipping_costs',
                    security: hajri_admin_object.nonce,
                    city: city,
                    cost: cost
                },
                success: function(response) {
                    $('#add-city-btn').prop('disabled', false);
                    
                    if (response.success) {
                        $('#city-update-message').html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>');
                        $('#new-city').val('');
                        $('#new-cost').val('');
                        loadShippingCosts();
                    } else {
                        $('#city-update-message').html('<div class="notice notice-error"><p>' + hajri_admin_object.strings.error + ' ' + response.data.message + '</p></div>');
                    }
                },
                error: function() {
                    $('#add-city-btn').prop('disabled', false);
                    $('#city-update-message').html('<div class="notice notice-error"><p>An error occurred while updating the shipping cost.</p></div>');
                }
            });
        });

        // Load shipping costs if shipping tab is active
        if ($('.hajri-tab[href="#shipping"]').hasClass('active')) {
            loadShippingCosts();
        }

        // Load shipping costs when tab is clicked
        $('.hajri-tab[href="#shipping"]').on('click', function() {
            loadShippingCosts();
        });

        // Handle order status updates
        $('#update-status-btn').on('click', function() {
            var formData = $('#update-order-status-form').serialize();
            var statusText = $('#order-status option:selected').text();
            var statusValue = $('#order-status').val();
            
            $('#status-update-message').html(hajri_admin_object.strings.processing);
            
            $.ajax({
                url: hajri_admin_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'update_order_status',
                    security: $('input[name="security"]').val(),
                    order_id: $('input[name="order_id"]').val(),
                    status: statusValue
                },
                success: function(response) {
                    if (response.success) {
                        $('#status-update-message').html('<span class="hajri-success">' + response.data.message + '</span>');
                        
                        // Update the status display
                        $('.hajri-status').removeClass('hajri-status-pending hajri-status-processing hajri-status-completed hajri-status-cancelled');
                        $('.hajri-status').addClass('hajri-status-' + statusValue);
                        $('.hajri-status').text(statusText);
                    } else {
                        $('#status-update-message').html('<span class="hajri-error">' + hajri_admin_object.strings.error + ' ' + response.data.message + '</span>');
                    }
                },
                error: function() {
                    $('#status-update-message').html('<span class="hajri-error">' + hajri_admin_object.strings.error + ' An unknown error occurred.</span>');
                }
            });
        });

        // Handle abandoned cart deletion
        $('.delete-cart').on('click', function() {
            if (!confirm(hajri_admin_object.strings.confirm_delete)) {
                return;
            }
            
            var button = $(this);
            var cartId = button.data('id');
            
            button.prop('disabled', true);
            button.text(hajri_admin_object.strings.processing);
            
            $.ajax({
                url: hajri_admin_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'delete_abandoned_cart',
                    cart_id: cartId,
                    security: hajri_admin_object.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('#delete-message').html('<div class="notice notice-success is-dismissible"><p>' + response.data.message + '</p></div>');
                        button.closest('tr').fadeOut(300, function() {
                            $(this).remove();
                            
                            // Check if there are no rows left
                            if ($('.hajri-table-container tbody tr').length === 0) {
                                $('.hajri-table-container tbody').html('<tr><td colspan="6">No abandoned carts found.</td></tr>');
                            }
                        });
                    } else {
                        $('#delete-message').html('<div class="notice notice-error is-dismissible"><p>' + hajri_admin_object.strings.error + ' ' + response.data.message + '</p></div>');
                        button.prop('disabled', false);
                        button.text('Delete');
                    }
                },
                error: function() {
                    $('#delete-message').html('<div class="notice notice-error is-dismissible"><p>An error occurred while deleting the cart.</p></div>');
                    button.prop('disabled', false);
                    button.text('Delete');
                }
            });
        });

        // IP blocking functionality
        $('#block-ip-btn').on('click', function() {
            var ip = $('#block-ip').val();
            var reason = $('#block-reason').val();
            
            if (!ip) {
                $('#ip-block-message').html('<div class="notice notice-error"><p>Please enter an IP address.</p></div>');
                return;
            }
            
            $(this).prop('disabled', true);
            $('#ip-block-message').html('<div class="notice notice-info"><p>' + hajri_admin_object.strings.processing + '</p></div>');
            
            $.ajax({
                url: hajri_admin_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'block_ip',
                    security: hajri_admin_object.nonce,
                    ip: ip,
                    reason: reason
                },
                success: function(response) {
                    $('#block-ip-btn').prop('disabled', false);
                    
                    if (response.success) {
                        $('#ip-block-message').html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>');
                        $('#block-ip').val('');
                        $('#block-reason').val('');
                        loadBlockedIPs();
                    } else {
                        $('#ip-block-message').html('<div class="notice notice-error"><p>' + hajri_admin_object.strings.error + ' ' + response.data.message + '</p></div>');
                    }
                },
                error: function() {
                    $('#block-ip-btn').prop('disabled', false);
                    $('#ip-block-message').html('<div class="notice notice-error"><p>An error occurred while blocking the IP.</p></div>');
                }
            });
        });

        // Load blocked IPs
        function loadBlockedIPs() {
            $.ajax({
                url: hajri_admin_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_blocked_ips',
                    security: hajri_admin_object.nonce
                },
                success: function(response) {
                    if (response.success && response.data.blocked_ips) {
                        var ips = response.data.blocked_ips;
                        var html = '';
                        
                        if (ips.length === 0) {
                            html = '<tr><td colspan="3">No IPs are currently blocked.</td></tr>';
                        } else {
                            $.each(ips, function(index, ip) {
                                html += '<tr>';
                                html += '<td>' + ip.ip_address + '</td>';
                                html += '<td>' + (ip.reason || 'No reason provided') + '</td>';
                                html += '<td>';
                                html += '<button type="button" class="button unblock-ip" data-ip="' + ip.id + '">';
                                html += 'Unblock';
                                html += '</button>';
                                html += '</td>';
                                html += '</tr>';
                            });
                        }
                        
                        $('#blocked-ips-table tbody').html(html);
                        
                        // Bind unblock buttons
                        $('.unblock-ip').on('click', function() {
                            var ipId = $(this).data('ip');
                            unblockIP(ipId);
                        });
                    } else {
                        $('#blocked-ips-table tbody').html('<tr><td colspan="3">Error loading blocked IPs.</td></tr>');
                    }
                },
                error: function() {
                    $('#blocked-ips-table tbody').html('<tr><td colspan="3">Error loading blocked IPs.</td></tr>');
                }
            });
        }

        // Unblock IP
        function unblockIP(ipId) {
            if (!confirm(hajri_admin_object.strings.confirm_delete)) {
                return;
            }
            
            $('#ip-block-message').html('<div class="notice notice-info"><p>' + hajri_admin_object.strings.processing + '</p></div>');
            
            $.ajax({
                url: hajri_admin_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'unblock_ip',
                    security: hajri_admin_object.nonce,
                    ip_id: ipId
                },
                success: function(response) {
                    if (response.success) {
                        $('#ip-block-message').html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>');
                        loadBlockedIPs();
                    } else {
                        $('#ip-block-message').html('<div class="notice notice-error"><p>' + hajri_admin_object.strings.error + ' ' + response.data.message + '</p></div>');
                    }
                },
                error: function() {
                    $('#ip-block-message').html('<div class="notice notice-error"><p>An error occurred while unblocking the IP.</p></div>');
                }
            });
        }

        // Load blocked IPs if security tab is active
        if ($('.hajri-tab[href="#security"]').hasClass('active')) {
            loadBlockedIPs();
        }

        // Load blocked IPs when security tab is clicked
        $('.hajri-tab[href="#security"]').on('click', function() {
            loadBlockedIPs();
        });

        // Handle discount creation/update
        $('.hajri-discount-form').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            var formData = form.serialize();
            var action = form.data('action');
            
            submitBtn.prop('disabled', true);
            $('#discount-message').html('<div class="notice notice-info"><p>' + hajri_admin_object.strings.processing + '</p></div>');
            
            $.ajax({
                url: hajri_admin_object.ajax_url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    submitBtn.prop('disabled', false);
                    
                    if (response.success) {
                        $('#discount-message').html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>');
                        
                        if (action === 'create') {
                            // Clear form
                            form[0].reset();
                        }
                        
                        // Reload discounts list
                        loadDiscounts();
                    } else {
                        $('#discount-message').html('<div class="notice notice-error"><p>' + hajri_admin_object.strings.error + ' ' + response.data.message + '</p></div>');
                    }
                },
                error: function() {
                    submitBtn.prop('disabled', false);
                    $('#discount-message').html('<div class="notice notice-error"><p>An error occurred while processing the discount.</p></div>');
                }
            });
        });

        // Load analytics data when period selector changes
        $('#period-selector').on('change', function() {
            $(this).closest('form').submit();
        });

    });

})(jQuery);
