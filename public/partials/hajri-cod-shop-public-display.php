<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/public/partials
 */

// Direct access protection
defined('WPINC') or die;
?>

<!-- This file is used as a template for the plugin's public-facing aspects -->
<div class="hajri-cod-shop-container">
    <h2><?php echo esc_html__('Our Products', 'hajri-cod-shop'); ?></h2>
    
    <div class="hajri-products-wrapper">
        <?php
        // Get all products
        $products = Hajri_Cod_Shop_Product::get_products(array(
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        ));
        
        if (empty($products)) {
            echo '<p class="hajri-info">' . esc_html__('No products found.', 'hajri-cod-shop') . '</p>';
        } else {
            echo '<div class="hajri-product-grid">';
            
            foreach ($products as $product) {
                ?>
                <div class="hajri-product-item hajri-column-3">
                    <?php if (!empty($product['image'])) : ?>
                        <div class="hajri-product-image">
                            <a href="<?php echo esc_url($product['permalink']); ?>">
                                <img src="<?php echo esc_url($product['image']); ?>" alt="<?php echo esc_attr($product['title']); ?>">
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <h3 class="hajri-product-title">
                        <a href="<?php echo esc_url($product['permalink']); ?>"><?php echo esc_html($product['title']); ?></a>
                    </h3>
                    
                    <?php if (!empty($product['excerpt'])) : ?>
                        <div class="hajri-product-excerpt"><?php echo wp_kses_post($product['excerpt']); ?></div>
                    <?php endif; ?>
                    
                    <div class="hajri-product-price">
                        <?php if (!empty($product['sale_price'])) : ?>
                            <span class="hajri-regular-price"><?php echo esc_html(number_format($product['price'], 2)); ?> <?php echo esc_html__('DZD', 'hajri-cod-shop'); ?></span>
                            <span class="hajri-sale-price"><?php echo esc_html(number_format($product['sale_price'], 2)); ?> <?php echo esc_html__('DZD', 'hajri-cod-shop'); ?></span>
                        <?php else : ?>
                            <span class="hajri-price"><?php echo esc_html(number_format($product['price'], 2)); ?> <?php echo esc_html__('DZD', 'hajri-cod-shop'); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="hajri-product-actions">
                        <a href="<?php echo esc_url($product['permalink']); ?>" class="hajri-button"><?php echo esc_html__('View Product', 'hajri-cod-shop'); ?></a>
                    </div>
                </div>
                <?php
            }
            
            echo '</div>';
        }
        ?>
    </div>
</div>
