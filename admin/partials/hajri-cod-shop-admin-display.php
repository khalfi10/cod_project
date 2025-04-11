<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/admin/partials
 */

// Direct access protection
defined('WPINC') or die;
?>

<div class="wrap hajri-cod-shop-admin">
    <h1><?php echo esc_html__('Hajri COD Shop Dashboard', 'hajri-cod-shop'); ?></h1>
    
    <div class="hajri-dashboard-wrapper">
        <?php
        // Get some stats for the dashboard
        global $wpdb;
        $orders_table = $wpdb->prefix . 'hajri_orders';
        $abandoned_table = $wpdb->prefix . 'hajri_abandoned_carts';
        
        // Count orders by status
        $pending_count = $wpdb->get_var("SELECT COUNT(*) FROM $orders_table WHERE status = 'pending'");
        $processing_count = $wpdb->get_var("SELECT COUNT(*) FROM $orders_table WHERE status = 'processing'");
        $completed_count = $wpdb->get_var("SELECT COUNT(*) FROM $orders_table WHERE status = 'completed'");
        $cancelled_count = $wpdb->get_var("SELECT COUNT(*) FROM $orders_table WHERE status = 'cancelled'");
        
        // Recent orders
        $recent_orders = $wpdb->get_results(
            "SELECT * FROM $orders_table ORDER BY created_at DESC LIMIT 5",
            ARRAY_A
        );
        
        // Abandoned carts count
        $abandoned_count = $wpdb->get_var("SELECT COUNT(*) FROM $abandoned_table WHERE is_converted = 0");
        
        // Total revenue
        $total_revenue = $wpdb->get_var("SELECT SUM(total_amount) FROM $orders_table WHERE status != 'cancelled'");
        $total_revenue = $total_revenue ? $total_revenue : 0;
        
        // Top cities
        $top_cities = $wpdb->get_results(
            "SELECT city, COUNT(*) as count FROM $orders_table GROUP BY city ORDER BY count DESC LIMIT 5",
            ARRAY_A
        );
        ?>
        
        <div class="hajri-dashboard-cards">
            <div class="hajri-card">
                <div class="hajri-card-header"><?php echo esc_html__('Pending Orders', 'hajri-cod-shop'); ?></div>
                <div class="hajri-card-content">
                    <span class="hajri-card-number"><?php echo esc_html($pending_count); ?></span>
                </div>
                <div class="hajri-card-footer">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=hajri-orders&status=pending')); ?>"><?php echo esc_html__('View All', 'hajri-cod-shop'); ?></a>
                </div>
            </div>
            
            <div class="hajri-card">
                <div class="hajri-card-header"><?php echo esc_html__('Processing Orders', 'hajri-cod-shop'); ?></div>
                <div class="hajri-card-content">
                    <span class="hajri-card-number"><?php echo esc_html($processing_count); ?></span>
                </div>
                <div class="hajri-card-footer">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=hajri-orders&status=processing')); ?>"><?php echo esc_html__('View All', 'hajri-cod-shop'); ?></a>
                </div>
            </div>
            
            <div class="hajri-card">
                <div class="hajri-card-header"><?php echo esc_html__('Completed Orders', 'hajri-cod-shop'); ?></div>
                <div class="hajri-card-content">
                    <span class="hajri-card-number"><?php echo esc_html($completed_count); ?></span>
                </div>
                <div class="hajri-card-footer">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=hajri-orders&status=completed')); ?>"><?php echo esc_html__('View All', 'hajri-cod-shop'); ?></a>
                </div>
            </div>
            
            <div class="hajri-card">
                <div class="hajri-card-header"><?php echo esc_html__('Total Revenue', 'hajri-cod-shop'); ?></div>
                <div class="hajri-card-content">
                    <span class="hajri-card-number"><?php echo esc_html(number_format($total_revenue, 2)); ?> <?php echo esc_html__('DZD', 'hajri-cod-shop'); ?></span>
                </div>
                <div class="hajri-card-footer">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=hajri-analytics')); ?>"><?php echo esc_html__('View Analytics', 'hajri-cod-shop'); ?></a>
                </div>
            </div>
            
            <div class="hajri-card">
                <div class="hajri-card-header"><?php echo esc_html__('Abandoned Carts', 'hajri-cod-shop'); ?></div>
                <div class="hajri-card-content">
                    <span class="hajri-card-number"><?php echo esc_html($abandoned_count); ?></span>
                </div>
                <div class="hajri-card-footer">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=hajri-abandoned')); ?>"><?php echo esc_html__('View All', 'hajri-cod-shop'); ?></a>
                </div>
            </div>
        </div>
        
        <div class="hajri-dashboard-sections">
            <div class="hajri-section">
                <h2><?php echo esc_html__('Recent Orders', 'hajri-cod-shop'); ?></h2>
                <div class="hajri-table-container">
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php echo esc_html__('ID', 'hajri-cod-shop'); ?></th>
                                <th><?php echo esc_html__('Customer', 'hajri-cod-shop'); ?></th>
                                <th><?php echo esc_html__('Phone', 'hajri-cod-shop'); ?></th>
                                <th><?php echo esc_html__('City', 'hajri-cod-shop'); ?></th>
                                <th><?php echo esc_html__('Total', 'hajri-cod-shop'); ?></th>
                                <th><?php echo esc_html__('Status', 'hajri-cod-shop'); ?></th>
                                <th><?php echo esc_html__('Date', 'hajri-cod-shop'); ?></th>
                                <th><?php echo esc_html__('Actions', 'hajri-cod-shop'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_orders)) : ?>
                                <tr>
                                    <td colspan="8"><?php echo esc_html__('No orders found.', 'hajri-cod-shop'); ?></td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($recent_orders as $order) : ?>
                                    <tr>
                                        <td><?php echo esc_html($order['id']); ?></td>
                                        <td><?php echo esc_html($order['customer_name']); ?></td>
                                        <td><?php echo esc_html($order['phone_number']); ?></td>
                                        <td><?php echo esc_html($order['city']); ?></td>
                                        <td><?php echo esc_html(number_format($order['total_amount'], 2) . ' ' . __('DZD', 'hajri-cod-shop')); ?></td>
                                        <td>
                                            <span class="hajri-status hajri-status-<?php echo esc_attr($order['status']); ?>">
                                                <?php echo esc_html(ucfirst($order['status'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($order['created_at']))); ?>
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
                <p>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=hajri-orders')); ?>" class="button">
                        <?php echo esc_html__('View All Orders', 'hajri-cod-shop'); ?>
                    </a>
                </p>
            </div>
            
            <div class="hajri-section">
                <h2><?php echo esc_html__('Top Cities', 'hajri-cod-shop'); ?></h2>
                <?php if (empty($top_cities)) : ?>
                    <p><?php echo esc_html__('No data available.', 'hajri-cod-shop'); ?></p>
                <?php else : ?>
                    <ul class="hajri-cities-list">
                        <?php foreach ($top_cities as $city) : ?>
                            <li>
                                <span class="hajri-city-name"><?php echo esc_html($city['city']); ?></span>
                                <span class="hajri-city-count"><?php echo esc_html($city['count']); ?> <?php echo esc_html__('orders', 'hajri-cod-shop'); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <p>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=hajri-analytics')); ?>" class="button">
                        <?php echo esc_html__('View Analytics', 'hajri-cod-shop'); ?>
                    </a>
                </p>
            </div>
        </div>
        
        <div class="hajri-dashboard-footer">
            <div class="hajri-quick-links">
                <h3><?php echo esc_html__('Quick Links', 'hajri-cod-shop'); ?></h3>
                <div class="hajri-links-grid">
                    <a href="<?php echo esc_url(admin_url('post-new.php?post_type=hajri_product')); ?>" class="hajri-quick-link">
                        <span class="dashicons dashicons-plus"></span>
                        <?php echo esc_html__('Add Product', 'hajri-cod-shop'); ?>
                    </a>
                    <a href="<?php echo esc_url(admin_url('edit.php?post_type=hajri_product')); ?>" class="hajri-quick-link">
                        <span class="dashicons dashicons-list-view"></span>
                        <?php echo esc_html__('Manage Products', 'hajri-cod-shop'); ?>
                    </a>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=hajri-settings')); ?>" class="hajri-quick-link">
                        <span class="dashicons dashicons-admin-settings"></span>
                        <?php echo esc_html__('Settings', 'hajri-cod-shop'); ?>
                    </a>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=hajri-analytics')); ?>" class="hajri-quick-link">
                        <span class="dashicons dashicons-chart-bar"></span>
                        <?php echo esc_html__('Analytics', 'hajri-cod-shop'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
