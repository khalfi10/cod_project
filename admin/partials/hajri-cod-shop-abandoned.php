<?php
/**
 * Provide an admin area view for abandoned carts
 *
 * @link       https://example.com
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/admin/partials
 */

// Direct access protection
defined('WPINC') or die;

// Get abandoned carts
global $wpdb;
$abandoned_table = $wpdb->prefix . 'hajri_abandoned_carts';

$abandoned_carts = $wpdb->get_results(
    "SELECT * FROM $abandoned_table 
    WHERE is_converted = 0 
    ORDER BY created_at DESC 
    LIMIT 50",
    ARRAY_A
);

foreach ($abandoned_carts as &$cart) {
    $cart['products'] = json_decode($cart['products'], true);
}

// Pagination (simple implementation)
$total_items = $wpdb->get_var("SELECT COUNT(*) FROM $abandoned_table WHERE is_converted = 0");
$items_per_page = 20;
$page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$offset = ($page - 1) * $items_per_page;
$total_pages = ceil($total_items / $items_per_page);

// Get paginated carts
$abandoned_carts = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM $abandoned_table 
        WHERE is_converted = 0 
        ORDER BY created_at DESC 
        LIMIT %d OFFSET %d",
        $items_per_page,
        $offset
    ),
    ARRAY_A
);

foreach ($abandoned_carts as &$cart) {
    if (!empty($cart['products'])) {
        $cart['products'] = json_decode($cart['products'], true);
    } else {
        $cart['products'] = array();
    }
}
?>

<div class="wrap hajri-cod-shop-admin">
    <h1><?php echo esc_html__('Abandoned Carts', 'hajri-cod-shop'); ?></h1>
    
    <div class="hajri-overview-stats">
        <p>
            <?php
            printf(
                /* translators: %d: number of abandoned carts */
                esc_html__('You have a total of %d abandoned carts.', 'hajri-cod-shop'),
                $total_items
            );
            ?>
        </p>
    </div>
    
    <div class="hajri-table-container">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th width="15%"><?php echo esc_html__('Date', 'hajri-cod-shop'); ?></th>
                    <th width="15%"><?php echo esc_html__('Customer', 'hajri-cod-shop'); ?></th>
                    <th width="15%"><?php echo esc_html__('Phone', 'hajri-cod-shop'); ?></th>
                    <th width="15%"><?php echo esc_html__('City', 'hajri-cod-shop'); ?></th>
                    <th width="25%"><?php echo esc_html__('Products', 'hajri-cod-shop'); ?></th>
                    <th width="15%"><?php echo esc_html__('Actions', 'hajri-cod-shop'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($abandoned_carts)) : ?>
                    <tr>
                        <td colspan="6"><?php echo esc_html__('No abandoned carts found.', 'hajri-cod-shop'); ?></td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($abandoned_carts as $cart) : ?>
                        <tr>
                            <td>
                                <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($cart['created_at']))); ?>
                            </td>
                            <td>
                                <?php echo !empty($cart['customer_name']) ? esc_html($cart['customer_name']) : '<em>' . esc_html__('Not provided', 'hajri-cod-shop') . '</em>'; ?>
                            </td>
                            <td>
                                <?php 
                                if (!empty($cart['phone_number'])) {
                                    echo esc_html($cart['phone_number']);
                                    if (!empty($cart['phone_number'])) {
                                        echo ' <a href="tel:' . esc_attr($cart['phone_number']) . '" class="button button-small">';
                                        echo '<span class="dashicons dashicons-phone"></span>';
                                        echo '</a>';
                                    }
                                } else {
                                    echo '<em>' . esc_html__('Not provided', 'hajri-cod-shop') . '</em>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php echo !empty($cart['city']) ? esc_html($cart['city']) : '<em>' . esc_html__('Not provided', 'hajri-cod-shop') . '</em>'; ?>
                            </td>
                            <td>
                                <?php
                                if (!empty($cart['products']) && is_array($cart['products'])) {
                                    $product_details = array();
                                    foreach ($cart['products'] as $product) {
                                        if (isset($product['id']) && isset($product['quantity'])) {
                                            $product_info = Hajri_Cod_Shop_Product::get_product($product['id']);
                                            if ($product_info) {
                                                $product_details[] = $product_info['title'] . ' (x' . $product['quantity'] . ')';
                                            }
                                        }
                                    }
                                    echo esc_html(implode(', ', $product_details));
                                } else {
                                    echo '<em>' . esc_html__('No products', 'hajri-cod-shop') . '</em>';
                                }
                                ?>
                            </td>
                            <td>
                                <button class="button button-small delete-cart" data-id="<?php echo esc_attr($cart['id']); ?>">
                                    <?php echo esc_html__('Delete', 'hajri-cod-shop'); ?>
                                </button>
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
                        /* translators: %s: number of items */
                        esc_html(_n('%s item', '%s items', $total_items, 'hajri-cod-shop')),
                        number_format_i18n($total_items)
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
    
    <div id="delete-message"></div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
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
                                $('.hajri-table-container tbody').html('<tr><td colspan="6"><?php echo esc_js(__('No abandoned carts found.', 'hajri-cod-shop')); ?></td></tr>');
                            }
                        });
                    } else {
                        $('#delete-message').html('<div class="notice notice-error is-dismissible"><p>' + hajri_admin_object.strings.error + ' ' + response.data.message + '</p></div>');
                        button.prop('disabled', false);
                        button.text('<?php echo esc_js(__('Delete', 'hajri-cod-shop')); ?>');
                    }
                },
                error: function() {
                    $('#delete-message').html('<div class="notice notice-error is-dismissible"><p><?php echo esc_js(__('An error occurred while deleting the cart.', 'hajri-cod-shop')); ?></p></div>');
                    button.prop('disabled', false);
                    button.text('<?php echo esc_js(__('Delete', 'hajri-cod-shop')); ?>');
                }
            });
        });
    });
</script>
