<?php
/**
 * Provide an admin area view for analytics
 *
 * @link       https://example.com
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/admin/partials
 */

// Direct access protection
defined('WPINC') or die;

// Get the period from the query string or default to 'month'
$period = isset($_GET['period']) ? sanitize_text_field($_GET['period']) : 'month';

// Get stats for the selected period
$stats = Hajri_Cod_Shop_Order::get_order_stats($period);
?>

<div class="wrap hajri-cod-shop-admin">
    <h1><?php echo esc_html__('Analytics', 'hajri-cod-shop'); ?></h1>
    
    <div class="hajri-period-selector">
        <form method="get" action="<?php echo esc_url(admin_url('admin.php')); ?>">
            <input type="hidden" name="page" value="hajri-analytics">
            
            <select name="period" id="period-selector">
                <option value="day" <?php selected($period, 'day'); ?>><?php echo esc_html__('Daily', 'hajri-cod-shop'); ?></option>
                <option value="week" <?php selected($period, 'week'); ?>><?php echo esc_html__('Weekly', 'hajri-cod-shop'); ?></option>
                <option value="month" <?php selected($period, 'month'); ?>><?php echo esc_html__('Monthly', 'hajri-cod-shop'); ?></option>
                <option value="year" <?php selected($period, 'year'); ?>><?php echo esc_html__('Yearly', 'hajri-cod-shop'); ?></option>
            </select>
            
            <button type="submit" class="button"><?php echo esc_html__('Update', 'hajri-cod-shop'); ?></button>
        </form>
    </div>
    
    <div class="hajri-analytics-overview">
        <div class="hajri-dashboard-cards">
            <div class="hajri-card">
                <div class="hajri-card-header"><?php echo esc_html__('Total Orders', 'hajri-cod-shop'); ?></div>
                <div class="hajri-card-content">
                    <span class="hajri-card-number"><?php echo esc_html($stats['total_orders']); ?></span>
                </div>
            </div>
            
            <div class="hajri-card">
                <div class="hajri-card-header"><?php echo esc_html__('Abandoned Carts', 'hajri-cod-shop'); ?></div>
                <div class="hajri-card-content">
                    <span class="hajri-card-number"><?php echo esc_html($stats['total_abandoned']); ?></span>
                </div>
            </div>
            
            <div class="hajri-card">
                <div class="hajri-card-header"><?php echo esc_html__('Conversion Rate', 'hajri-cod-shop'); ?></div>
                <div class="hajri-card-content">
                    <span class="hajri-card-number"><?php echo esc_html(number_format($stats['conversion_rate'], 2)); ?>%</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="hajri-analytics-grid">
        <!-- Orders Over Time Chart -->
        <div class="hajri-analytics-widget hajri-widget-wide">
            <h2><?php echo esc_html__('Orders Over Time', 'hajri-cod-shop'); ?></h2>
            <div class="hajri-chart-container">
                <canvas id="orders-chart"></canvas>
            </div>
        </div>
        
        <!-- Order Status Chart -->
        <div class="hajri-analytics-widget">
            <h2><?php echo esc_html__('Order Status', 'hajri-cod-shop'); ?></h2>
            <div class="hajri-chart-container">
                <canvas id="status-chart"></canvas>
            </div>
        </div>
        
        <!-- Top Cities Chart -->
        <div class="hajri-analytics-widget">
            <h2><?php echo esc_html__('Top Cities', 'hajri-cod-shop'); ?></h2>
            <div class="hajri-chart-container">
                <canvas id="cities-chart"></canvas>
            </div>
        </div>
        
        <!-- Top Products Table -->
        <div class="hajri-analytics-widget hajri-widget-wide">
            <h2><?php echo esc_html__('Top Products', 'hajri-cod-shop'); ?></h2>
            <div class="hajri-table-container">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php echo esc_html__('Product', 'hajri-cod-shop'); ?></th>
                            <th><?php echo esc_html__('Quantity Sold', 'hajri-cod-shop'); ?></th>
                            <th><?php echo esc_html__('Revenue', 'hajri-cod-shop'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stats['top_products'])) : ?>
                            <tr>
                                <td colspan="3"><?php echo esc_html__('No product data available.', 'hajri-cod-shop'); ?></td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($stats['top_products'] as $product) : ?>
                                <tr>
                                    <td><?php echo esc_html($product['product_name']); ?></td>
                                    <td><?php echo esc_html($product['total_quantity']); ?></td>
                                    <td><?php echo esc_html(number_format($product['total_sales'], 2) . ' ' . __('DZD', 'hajri-cod-shop')); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Prepare data for Orders Over Time chart
        var ordersDates = [];
        var ordersData = [];
        var revenueData = [];
        
        <?php
        // Format orders by date for chart
        foreach ($stats['orders_by_date'] as $date_data) {
            echo "ordersDates.push('" . esc_js($date_data['date_group']) . "');\n";
            echo "ordersData.push(" . esc_js($date_data['count']) . ");\n";
            echo "revenueData.push(" . esc_js($date_data['total']) . ");\n";
        }
        ?>
        
        // Orders Over Time Chart
        var ordersChartCtx = document.getElementById('orders-chart').getContext('2d');
        var ordersChart = new Chart(ordersChartCtx, {
            type: 'line',
            data: {
                labels: ordersDates,
                datasets: [
                    {
                        label: '<?php echo esc_js(__('Orders', 'hajri-cod-shop')); ?>',
                        data: ordersData,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        fill: true,
                        yAxisID: 'y-axis-orders'
                    },
                    {
                        label: '<?php echo esc_js(__('Revenue (DZD)', 'hajri-cod-shop')); ?>',
                        data: revenueData,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        fill: true,
                        yAxisID: 'y-axis-revenue'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [
                        {
                            id: 'y-axis-orders',
                            type: 'linear',
                            position: 'left',
                            ticks: {
                                beginAtZero: true,
                                callback: function(value) {
                                    if (value % 1 === 0) {
                                        return value;
                                    }
                                }
                            },
                            scaleLabel: {
                                display: true,
                                labelString: '<?php echo esc_js(__('Orders', 'hajri-cod-shop')); ?>'
                            }
                        },
                        {
                            id: 'y-axis-revenue',
                            type: 'linear',
                            position: 'right',
                            ticks: {
                                beginAtZero: true,
                                callback: function(value) {
                                    return value.toLocaleString() + ' DZD';
                                }
                            },
                            scaleLabel: {
                                display: true,
                                labelString: '<?php echo esc_js(__('Revenue (DZD)', 'hajri-cod-shop')); ?>'
                            }
                        }
                    ]
                }
            }
        });
        
        // Prepare data for Order Status chart
        var statusLabels = [];
        var statusData = [];
        var statusColors = [
            'rgba(255, 99, 132, 0.7)',
            'rgba(54, 162, 235, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(153, 102, 255, 0.7)'
        ];
        
        <?php
        // Format status counts for chart
        foreach ($stats['status_counts'] as $status) {
            echo "statusLabels.push('" . esc_js(ucfirst($status['status'])) . "');\n";
            echo "statusData.push(" . esc_js($status['count']) . ");\n";
        }
        ?>
        
        // Order Status Chart
        var statusChartCtx = document.getElementById('status-chart').getContext('2d');
        var statusChart = new Chart(statusChartCtx, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: statusColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'bottom'
                }
            }
        });
        
        // Prepare data for Top Cities chart
        var cityLabels = [];
        var cityData = [];
        var cityColors = [
            'rgba(255, 99, 132, 0.7)',
            'rgba(54, 162, 235, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 159, 64, 0.7)',
            'rgba(201, 203, 207, 0.7)',
            'rgba(255, 99, 132, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(75, 192, 192, 0.5)'
        ];
        
        <?php
        // Format city data for chart
        foreach ($stats['orders_by_city'] as $city) {
            echo "cityLabels.push('" . esc_js($city['city']) . "');\n";
            echo "cityData.push(" . esc_js($city['count']) . ");\n";
        }
        ?>
        
        // Top Cities Chart
        var citiesChartCtx = document.getElementById('cities-chart').getContext('2d');
        var citiesChart = new Chart(citiesChartCtx, {
            type: 'pie',
            data: {
                labels: cityLabels,
                datasets: [{
                    data: cityData,
                    backgroundColor: cityColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'bottom'
                }
            }
        });
    });
</script>
