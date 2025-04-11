<?php
/**
 * Provide an admin area view for managing orders
 *
 * @link       https://example.com
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/admin/partials
 */

// Direct access protection
defined('WPINC') or die;

// Handle actions
$action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get filters
$status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
$search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
$date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : '';

// Pagination
$page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Prepare query args
$args = array(
    'limit' => $per_page,
    'offset' => $offset,
);

if (!empty($status_filter)) {
    $args['status'] = $status_filter;
}

if (!empty($search)) {
    $args['search'] = $search;
}

if (!empty($date_from)) {
    $args['date_from'] = $date_from;
}

if (!empty($date_to)) {
    $args['date_to'] = $date_to;
}

// Get orders and total count
$orders = Hajri_Cod_Shop_Order::get_orders($args);
$total_orders = Hajri_Cod_Shop_Order::get_orders_count($args);

// Calculate pagination
$total_pages = ceil($total_orders / $per_page);

// Handle single order view
if ($action === 'view' && $order_id > 0) {
    $order = Hajri_Cod_Shop_Order::get_order($order_id);
    
    if ($order) {
        ?>
        <div class="wrap hajri-cod-shop-admin">
            <h1>
                <?php echo esc_html__('Order', 'hajri-cod-shop'); ?> #<?php echo esc_html($order['id']); ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=hajri-orders')); ?>" class="page-title-action"><?php echo esc_html__('Back to Orders', 'hajri-cod-shop'); ?></a>
            </h1>
            
            <div class="hajri-order-details">
                <div class="hajri-order-meta">
                    <div class="hajri-meta-box">
                        <h3><?php echo esc_html__('Order Information', 'hajri-cod-shop'); ?></h3>
                        <div class="hajri-meta-data">
                            <p>
                                <strong><?php echo esc_html__('Order Status:', 'hajri-cod-shop'); ?></strong>
                                <span class="hajri-status hajri-status-<?php echo esc_attr($order['status']); ?>">
                                    <?php echo esc_html(ucfirst($order['status'])); ?>
                                </span>
                            </p>
                            <p>
                                <strong><?php echo esc_html__('Order Date:', 'hajri-cod-shop'); ?></strong>
                                <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($order['created_at']))); ?>
                            </p>
                            <p>
                                <strong><?php echo esc_html__('Total Amount:', 'hajri-cod-shop'); ?></strong>
                                <?php echo esc_html(number_format($order['total_amount'], 2) . ' ' . __('DZD', 'hajri-cod-shop')); ?>
                            </p>
                            <p>
                                <strong><?php echo esc_html__('Shipping Cost:', 'hajri-cod-shop'); ?></strong>
                                <?php echo esc_html(number_format($order['shipping_cost'], 2) . ' ' . __('DZD', 'hajri-cod-shop')); ?>
                            </p>
                            <p>
                                <strong><?php echo esc_html__('Discount Applied:', 'hajri-cod-shop'); ?></strong>
                                <?php echo esc_html(number_format($order['discount_applied'], 2) . ' ' . __('DZD', 'hajri-cod-shop')); ?>
                            </p>
                            <p>
                                <strong><?php echo esc_html__('IP Address:', 'hajri-cod-shop'); ?></strong>
                                <?php echo esc_html($order['ip_address']); ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="hajri-meta-box">
                        <h3><?php echo esc_html__('Customer Information', 'hajri-cod-shop'); ?></h3>
                        <div class="hajri-meta-data">
                            <p>
                                <strong><?php echo esc_html__('Name:', 'hajri-cod-shop'); ?></strong>
                                <?php echo esc_html($order['customer_name']); ?>
                            </p>
                            <p>
                                <strong><?php echo esc_html__('Phone:', 'hajri-cod-shop'); ?></strong>
                                <?php echo esc_html($order['phone_number']); ?>
                            </p>
                            <p>
                                <strong><?php echo esc_html__('City:', 'hajri-cod-shop'); ?></strong>
                                <?php echo esc_html($order['city']); ?>
                            </p>
                            <p>
                                <strong><?php echo esc_html__('Address:', 'hajri-cod-shop'); ?></strong>
                                <?php echo esc_html($order['address']); ?>
                            </p>
                            <?php if (!empty($order['notes'])) : ?>
                                <p>
                                    <strong><?php echo esc_html__('Notes:', 'hajri-cod-shop'); ?></strong>
                                    <?php echo esc_html($order['notes']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="hajri-order-products">
                    <h3><?php echo esc_html__('Products', 'hajri-cod-shop'); ?></h3>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php echo esc_html__('Product', 'hajri-cod-shop'); ?></th>
                                <th><?php echo esc_html__('Price', 'hajri-cod-shop'); ?></th>
                                <th><?php echo esc_html__('Quantity', 'hajri-cod-shop'); ?></th>
                                <th><?php echo esc_html__('Subtotal', 'hajri-cod-shop'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['products'] as $product) : ?>
                                <tr>
                                    <td><?php echo esc_html($product['name']); ?></td>
                                    <td><?php echo esc_html(number_format($product['price'], 2) . ' ' . __('DZD', 'hajri-cod-shop')); ?></td>
                                    <td><?php echo esc_html($product['quantity']); ?></td>
                                    <td><?php echo esc_html(number_format($product['subtotal'], 2) . ' ' . __('DZD', 'hajri-cod-shop')); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3"><?php echo esc_html__('Subtotal', 'hajri-cod-shop'); ?></th>
                                <td>
                                    <?php 
                                    $subtotal = array_sum(array_column($order['products'], 'subtotal'));
                                    echo esc_html(number_format($subtotal, 2) . ' ' . __('DZD', 'hajri-cod-shop')); 
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th colspan="3"><?php echo esc_html__('Shipping', 'hajri-cod-shop'); ?></th>
                                <td><?php echo esc_html(number_format($order['shipping_cost'], 2) . ' ' . __('DZD', 'hajri-cod-shop')); ?></td>
                            </tr>
                            <?php if ($order['discount_applied'] > 0) : ?>
                                <tr>
                                    <th colspan="3"><?php echo esc_html__('Discount', 'hajri-cod-shop'); ?></th>
                                    <td>-<?php echo esc_html(number_format($order['discount_applied'], 2) . ' ' . __('DZD', 'hajri-cod-shop')); ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <th colspan="3"><?php echo esc_html__('Total', 'hajri-cod-shop'); ?></th>
                                <td><strong><?php echo esc_html(number_format($order['total_amount'], 2) . ' ' . __('DZD', 'hajri-cod-shop')); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="hajri-order-actions">
                    <h3><?php echo esc_html__('Order Actions', 'hajri-cod-shop'); ?></h3>
                    <div class="hajri-action-box">
                        <form id="update-order-status-form">
                            <input type="hidden" name="order_id" value="<?php echo esc_attr($order['id']); ?>">
                            <input type="hidden" name="security" value="<?php echo esc_attr(wp_create_nonce('hajri_admin_nonce')); ?>">
                            
                            <select name="status" id="order-status">
                                <option value="pending" <?php selected($order['status'], 'pending'); ?>><?php echo esc_html__('Pending', 'hajri-cod-shop'); ?></option>
                                <option value="processing" <?php selected($order['status'], 'processing'); ?>><?php echo esc_html__('Processing', 'hajri-cod-shop'); ?></option>
                                <option value="completed" <?php selected($order['status'], 'completed'); ?>><?php echo esc_html__('Completed', 'hajri-cod-shop'); ?></option>
                                <option value="cancelled" <?php selected($order['status'], 'cancelled'); ?>><?php echo esc_html__('Cancelled', 'hajri-cod-shop'); ?></option>
                            </select>
                            
                            <button type="button" id="update-status-btn" class="button button-primary"><?php echo esc_html__('Update Status', 'hajri-cod-shop'); ?></button>
                            <span id="status-update-message"></span>
                        </form>
                    </div>
                    
                    <div class="hajri-action-box">
                        <a href="tel:<?php echo esc_attr($order['phone_number']); ?>" class="button">
                            <span class="dashicons dashicons-phone"></span> <?php echo esc_html__('Call Customer', 'hajri-cod-shop'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <script type="text/javascript">
            jQuery(document).ready(function($) {
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
                            order_id: <?php echo esc_js($order['id']); ?>,
                            status: statusValue,
                            security: $('input[name="security"]').val()
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
                            $('#status-update-message').html('<span class="hajri-error">' + hajri_admin_object.strings.error + ' ' + hajri_admin_object.strings.ajax_error + '</span>');
                        }
                    });
                });
            });
        </script>
        <?php
    } else {
        ?>
        <div class="wrap hajri-cod-shop-admin">
            <h1><?php echo esc_html__('Order Not Found', 'hajri-cod-shop'); ?></h1>
            <p><?php echo esc_html__('The requested order could not be found.', 'hajri-cod-shop'); ?></p>
            <p><a href="<?php echo esc_url(admin_url('admin.php?page=hajri-orders')); ?>" class="button"><?php echo esc_html__('Back to Orders', 'hajri-cod-shop'); ?></a></p>
        </div>
        <?php
    }
} else {
    // Display orders list
    ?>
    <div class="wrap hajri-cod-shop-admin">
        <h1><?php echo esc_html__('Orders', 'hajri-cod-shop'); ?></h1>
        
        <div class="hajri-filters">
            <form method="get" action="<?php echo esc_url(admin_url('admin.php')); ?>">
                <input type="hidden" name="page" value="hajri-orders">
                
                <div class="hajri-filter-row">
                    <div class="hajri-filter-item">
                        <label for="status"><?php echo esc_html__('Status:', 'hajri-cod-shop'); ?></label>
                        <select name="status" id="status">
                            <option value=""><?php echo esc_html__('All Statuses', 'hajri-cod-shop'); ?></option>
                            <option value="pending" <?php selected($status_filter, 'pending'); ?>><?php echo esc_html__('Pending', 'hajri-cod-shop'); ?></option>
                            <option value="processing" <?php selected($status_filter, 'processing'); ?>><?php echo esc_html__('Processing', 'hajri-cod-shop'); ?></option>
                            <option value="completed" <?php selected($status_filter, 'completed'); ?>><?php echo esc_html__('Completed', 'hajri-cod-shop'); ?></option>
                            <option value="cancelled" <?php selected($status_filter, 'cancelled'); ?>><?php echo esc_html__('Cancelled', 'hajri-cod-shop'); ?></option>
                        </select>
                    </div>
                    
                    <div class="hajri-filter-item">
                        <label for="date_from"><?php echo esc_html__('From:', 'hajri-cod-shop'); ?></label>
                        <input type="date" name="date_from" id="date_from" value="<?php echo esc_attr($date_from); ?>">
                    </div>
                    
                    <div class="hajri-filter-item">
                        <label for="date_to"><?php echo esc_html__('To:', 'hajri-cod-shop'); ?></label>
                        <input type="date" name="date_to" id="date_to" value="<?php echo esc_attr($date_to); ?>">
                    </div>
                    
                    <div class="hajri-filter-item">
                        <label for="search"><?php echo esc_html__('Search:', 'hajri-cod-shop'); ?></label>
                        <input type="text" name="search" id="search" value="<?php echo esc_attr($search); ?>" placeholder="<?php echo esc_attr__('Name, Phone or Address', 'hajri-cod-shop'); ?>">
                    </div>
                    
                    <div class="hajri-filter-actions">
                        <button type="submit" class="button"><?php echo esc_html__('Filter', 'hajri-cod-shop'); ?></button>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=hajri-orders')); ?>" class="button"><?php echo esc_html__('Reset', 'hajri-cod-shop'); ?></a>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="hajri-orders-stats">
            <p>
                <?php
                printf(
                    /* translators: %1$d: number of orders shown, %2$d: total orders */
                    esc_html__('Showing %1$d of %2$d orders', 'hajri-cod-shop'),
                    count($orders),
                    $total_orders
                );
                ?>
            </p>
        </div>
        
        <div class="hajri-table-container">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th width="5%"><?php echo esc_html__('ID', 'hajri-cod-shop'); ?></th>
                        <th width="15%"><?php echo esc_html__('Customer', 'hajri-cod-shop'); ?></th>
                        <th width="15%"><?php echo esc_html__('Phone', 'hajri-cod-shop'); ?></th>
                        <th width="10%"><?php echo esc_html__('City', 'hajri-cod-shop'); ?></th>
                        <th width="15%"><?php echo esc_html__('Products', 'hajri-cod-shop'); ?></th>
                        <th width="10%"><?php echo esc_html__('Total', 'hajri-cod-shop'); ?></th>
                        <th width="10%"><?php echo esc_html__('Status', 'hajri-cod-shop'); ?></th>
                        <th width="10%"><?php echo esc_html__('Date', 'hajri-cod-shop'); ?></th>
                        <th width="10%"><?php echo esc_html__('Actions', 'hajri-cod-shop'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)) : ?>
                        <tr>
                            <td colspan="9"><?php echo esc_html__('No orders found.', 'hajri-cod-shop'); ?></td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($orders as $order) : ?>
                            <tr>
                                <td><?php echo esc_html($order['id']); ?></td>
                                <td><?php echo esc_html($order['customer_name']); ?></td>
                                <td><?php echo esc_html($order['phone_number']); ?></td>
                                <td><?php echo esc_html($order['city']); ?></td>
                                <td>
                                    <?php 
                                    $product_names = array();
                                    foreach ($order['products'] as $product) {
                                        $product_names[] = $product['name'] . ' (x' . $product['quantity'] . ')';
                                    }
                                    echo esc_html(implode(', ', $product_names));
                                    ?>
                                </td>
                                <td><?php echo esc_html(number_format($order['total_amount'], 2) . ' ' . __('DZD', 'hajri-cod-shop')); ?></td>
                                <td>
                                    <span class="hajri-status hajri-status-<?php echo esc_attr($order['status']); ?>">
                                        <?php echo esc_html(ucfirst($order['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($order['created_at']))); ?>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=hajri-orders&action=view&id=' . $order['id'])); ?>" class="button button-small">
                                        <?php echo esc_html__('View', 'hajri-cod-shop'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($total_pages > 1) : ?>
            <div class="hajri-pagination">
                <div class="tablenav-pages">
                    <span class="displaying-num">
                        <?php
                        printf(
                            /* translators: %s: number of orders */
                            esc_html(_n('%s order', '%s orders', $total_orders, 'hajri-cod-shop')),
                            number_format_i18n($total_orders)
                        );
                        ?>
                    </span>
                    
                    <span class="pagination-links">
                        <?php
                        // First page link
                        if ($page > 1) {
                            printf(
                                '<a class="first-page button" href="%s"><span class="screen-reader-text">%s</span><span aria-hidden="true">«</span></a>',
                                esc_url(add_query_arg('paged', 1)),
                                esc_html__('First page', 'hajri-cod-shop')
                            );
                        } else {
                            printf(
                                '<span class="first-page button disabled"><span class="screen-reader-text">%s</span><span aria-hidden="true">«</span></span>',
                                esc_html__('First page', 'hajri-cod-shop')
                            );
                        }
                        
                        // Previous page link
                        if ($page > 1) {
                            printf(
                                '<a class="prev-page button" href="%s"><span class="screen-reader-text">%s</span><span aria-hidden="true">‹</span></a>',
                                esc_url(add_query_arg('paged', $page - 1)),
                                esc_html__('Previous page', 'hajri-cod-shop')
                            );
                        } else {
                            printf(
                                '<span class="prev-page button disabled"><span class="screen-reader-text">%s</span><span aria-hidden="true">‹</span></span>',
                                esc_html__('Previous page', 'hajri-cod-shop')
                            );
                        }
                        
                        // Current page info
                        printf(
                            '<span class="paging-input">%s / <span class="total-pages">%s</span></span>',
                            $page,
                            $total_pages
                        );
                        
                        // Next page link
                        if ($page < $total_pages) {
                            printf(
                                '<a class="next-page button" href="%s"><span class="screen-reader-text">%s</span><span aria-hidden="true">›</span></a>',
                                esc_url(add_query_arg('paged', $page + 1)),
                                esc_html__('Next page', 'hajri-cod-shop')
                            );
                        } else {
                            printf(
                                '<span class="next-page button disabled"><span class="screen-reader-text">%s</span><span aria-hidden="true">›</span></span>',
                                esc_html__('Next page', 'hajri-cod-shop')
                            );
                        }
                        
                        // Last page link
                        if ($page < $total_pages) {
                            printf(
                                '<a class="last-page button" href="%s"><span class="screen-reader-text">%s</span><span aria-hidden="true">»</span></a>',
                                esc_url(add_query_arg('paged', $total_pages)),
                                esc_html__('Last page', 'hajri-cod-shop')
                            );
                        } else {
                            printf(
                                '<span class="last-page button disabled"><span class="screen-reader-text">%s</span><span aria-hidden="true">»</span></span>',
                                esc_html__('Last page', 'hajri-cod-shop')
                            );
                        }
                        ?>
                    </span>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="hajri-export-section">
            <h3><?php echo esc_html__('Export Orders', 'hajri-cod-shop'); ?></h3>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="hajri_export_orders">
                <?php wp_nonce_field('hajri_export_orders_nonce', 'hajri_export_nonce'); ?>
                
                <div class="hajri-filter-row">
                    <div class="hajri-filter-item">
                        <label for="export_status"><?php echo esc_html__('Status:', 'hajri-cod-shop'); ?></label>
                        <select name="export_status" id="export_status">
                            <option value=""><?php echo esc_html__('All Statuses', 'hajri-cod-shop'); ?></option>
                            <option value="pending"><?php echo esc_html__('Pending', 'hajri-cod-shop'); ?></option>
                            <option value="processing"><?php echo esc_html__('Processing', 'hajri-cod-shop'); ?></option>
                            <option value="completed"><?php echo esc_html__('Completed', 'hajri-cod-shop'); ?></option>
                            <option value="cancelled"><?php echo esc_html__('Cancelled', 'hajri-cod-shop'); ?></option>
                        </select>
                    </div>
                    
                    <div class="hajri-filter-item">
                        <label for="export_date_from"><?php echo esc_html__('From:', 'hajri-cod-shop'); ?></label>
                        <input type="date" name="export_date_from" id="export_date_from">
                    </div>
                    
                    <div class="hajri-filter-item">
                        <label for="export_date_to"><?php echo esc_html__('To:', 'hajri-cod-shop'); ?></label>
                        <input type="date" name="export_date_to" id="export_date_to">
                    </div>
                    
                    <div class="hajri-filter-actions">
                        <button type="submit" class="button"><?php echo esc_html__('Export to CSV', 'hajri-cod-shop'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
}
?>
