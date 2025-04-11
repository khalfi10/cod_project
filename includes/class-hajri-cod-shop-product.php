<?php
/**
 * Product-related functionality.
 *
 * @link       https://example.com
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/includes
 */

/**
 * Product-related functionality.
 *
 * Defines the functionality related to products in the shop.
 *
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/includes
 * @author     Your Name <email@example.com>
 */
class Hajri_Cod_Shop_Product {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        add_action('init', array($this, 'register_product_post_type'));
        add_action('add_meta_boxes', array($this, 'add_product_meta_boxes'));
        add_action('save_post_hajri_product', array($this, 'save_product_meta'));
    }

    /**
     * Register the custom post type for products.
     *
     * @since    1.0.0
     */
    public function register_product_post_type() {
        $labels = array(
            'name'                  => _x('Products', 'Post type general name', 'hajri-cod-shop'),
            'singular_name'         => _x('Product', 'Post type singular name', 'hajri-cod-shop'),
            'menu_name'             => _x('Products', 'Admin Menu text', 'hajri-cod-shop'),
            'name_admin_bar'        => _x('Product', 'Add New on Toolbar', 'hajri-cod-shop'),
            'add_new'               => __('Add New', 'hajri-cod-shop'),
            'add_new_item'          => __('Add New Product', 'hajri-cod-shop'),
            'new_item'              => __('New Product', 'hajri-cod-shop'),
            'edit_item'             => __('Edit Product', 'hajri-cod-shop'),
            'view_item'             => __('View Product', 'hajri-cod-shop'),
            'all_items'             => __('All Products', 'hajri-cod-shop'),
            'search_items'          => __('Search Products', 'hajri-cod-shop'),
            'parent_item_colon'     => __('Parent Products:', 'hajri-cod-shop'),
            'not_found'             => __('No products found.', 'hajri-cod-shop'),
            'not_found_in_trash'    => __('No products found in Trash.', 'hajri-cod-shop'),
            'featured_image'        => _x('Product Cover Image', 'Overrides the "Featured Image" phrase', 'hajri-cod-shop'),
            'set_featured_image'    => _x('Set cover image', 'Overrides the "Set featured image" phrase', 'hajri-cod-shop'),
            'remove_featured_image' => _x('Remove cover image', 'Overrides the "Remove featured image" phrase', 'hajri-cod-shop'),
            'use_featured_image'    => _x('Use as cover image', 'Overrides the "Use as featured image" phrase', 'hajri-cod-shop'),
            'archives'              => _x('Product archives', 'The post type archive label used in nav menus', 'hajri-cod-shop'),
            'insert_into_item'      => _x('Insert into product', 'Overrides the "Insert into post" phrase', 'hajri-cod-shop'),
            'uploaded_to_this_item' => _x('Uploaded to this product', 'Overrides the "Uploaded to this post" phrase', 'hajri-cod-shop'),
            'filter_items_list'     => _x('Filter products list', 'Screen reader text for the filter links heading on the post type listing screen', 'hajri-cod-shop'),
            'items_list_navigation' => _x('Products list navigation', 'Screen reader text for the pagination heading on the post type listing screen', 'hajri-cod-shop'),
            'items_list'            => _x('Products list', 'Screen reader text for the items list heading on the post type listing screen', 'hajri-cod-shop'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'product'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
            'menu_icon'          => 'dashicons-cart',
        );

        register_post_type('hajri_product', $args);
        
        // Register product category taxonomy
        $category_labels = array(
            'name'              => _x('Categories', 'taxonomy general name', 'hajri-cod-shop'),
            'singular_name'     => _x('Category', 'taxonomy singular name', 'hajri-cod-shop'),
            'search_items'      => __('Search Categories', 'hajri-cod-shop'),
            'all_items'         => __('All Categories', 'hajri-cod-shop'),
            'parent_item'       => __('Parent Category', 'hajri-cod-shop'),
            'parent_item_colon' => __('Parent Category:', 'hajri-cod-shop'),
            'edit_item'         => __('Edit Category', 'hajri-cod-shop'),
            'update_item'       => __('Update Category', 'hajri-cod-shop'),
            'add_new_item'      => __('Add New Category', 'hajri-cod-shop'),
            'new_item_name'     => __('New Category Name', 'hajri-cod-shop'),
            'menu_name'         => __('Categories', 'hajri-cod-shop'),
        );

        $category_args = array(
            'hierarchical'      => true,
            'labels'            => $category_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'product-category'),
        );

        register_taxonomy('hajri_product_cat', array('hajri_product'), $category_args);
    }

    /**
     * Add meta boxes for product data.
     *
     * @since    1.0.0
     */
    public function add_product_meta_boxes() {
        add_meta_box(
            'hajri_product_details',
            __('Product Details', 'hajri-cod-shop'),
            array($this, 'render_product_details_meta_box'),
            'hajri_product',
            'normal',
            'high'
        );
        
        add_meta_box(
            'hajri_product_recommendations',
            __('Related Products for Alternative Medicine', 'hajri-cod-shop'),
            array($this, 'render_product_recommendations_meta_box'),
            'hajri_product',
            'normal',
            'default'
        );
    }

    /**
     * Render the product details meta box.
     *
     * @since    1.0.0
     * @param    WP_Post $post The post object.
     */
    public function render_product_details_meta_box($post) {
        // Add a nonce field for security
        wp_nonce_field('hajri_product_details', 'hajri_product_details_nonce');
        
        // Get saved values
        $price = get_post_meta($post->ID, '_hajri_product_price', true);
        $sale_price = get_post_meta($post->ID, '_hajri_product_sale_price', true);
        $sku = get_post_meta($post->ID, '_hajri_product_sku', true);
        $stock = get_post_meta($post->ID, '_hajri_product_stock', true);
        $weight = get_post_meta($post->ID, '_hajri_product_weight', true);
        
        // Output the fields
        ?>
        <div class="hajri-product-meta-panel">
            <div class="hajri-form-field">
                <label for="hajri_product_price"><?php esc_html_e('Regular Price', 'hajri-cod-shop'); ?></label>
                <input type="number" min="0" step="0.01" id="hajri_product_price" name="hajri_product_price" value="<?php echo esc_attr($price); ?>" class="regular-text">
            </div>
            
            <div class="hajri-form-field">
                <label for="hajri_product_sale_price"><?php esc_html_e('Sale Price', 'hajri-cod-shop'); ?></label>
                <input type="number" min="0" step="0.01" id="hajri_product_sale_price" name="hajri_product_sale_price" value="<?php echo esc_attr($sale_price); ?>" class="regular-text">
                <p class="description"><?php esc_html_e('Leave empty for no sale price.', 'hajri-cod-shop'); ?></p>
            </div>
            
            <div class="hajri-form-field">
                <label for="hajri_product_sku"><?php esc_html_e('SKU', 'hajri-cod-shop'); ?></label>
                <input type="text" id="hajri_product_sku" name="hajri_product_sku" value="<?php echo esc_attr($sku); ?>" class="regular-text">
            </div>
            
            <div class="hajri-form-field">
                <label for="hajri_product_stock"><?php esc_html_e('Stock Quantity', 'hajri-cod-shop'); ?></label>
                <input type="number" min="0" step="1" id="hajri_product_stock" name="hajri_product_stock" value="<?php echo esc_attr($stock); ?>" class="regular-text">
            </div>
            
            <div class="hajri-form-field">
                <label for="hajri_product_weight"><?php esc_html_e('Weight (g)', 'hajri-cod-shop'); ?></label>
                <input type="number" min="0" step="0.01" id="hajri_product_weight" name="hajri_product_weight" value="<?php echo esc_attr($weight); ?>" class="regular-text">
            </div>
        </div>
        <?php
    }

    /**
     * Render the product recommendations meta box.
     *
     * @since    1.0.0
     * @param    WP_Post $post The post object.
     */
    public function render_product_recommendations_meta_box($post) {
        // Add a nonce field for security
        wp_nonce_field('hajri_product_recommendations', 'hajri_product_recommendations_nonce');
        
        // Get saved related products
        $related_products = get_post_meta($post->ID, '_hajri_related_products', true);
        if (!is_array($related_products)) {
            $related_products = array();
        }
        
        // Get all products except the current one
        $args = array(
            'post_type' => 'hajri_product',
            'posts_per_page' => -1,
            'post__not_in' => array($post->ID),
            'orderby' => 'title',
            'order' => 'ASC',
        );
        $products = get_posts($args);
        
        ?>
        <p><?php esc_html_e('Select products to recommend with this product (for alternative medicine/healing).', 'hajri-cod-shop'); ?></p>
        
        <div class="hajri-product-related-wrapper">
            <select name="hajri_related_products[]" id="hajri_related_products" multiple="multiple" style="width: 100%; min-height: 150px;">
                <?php foreach ($products as $product) : ?>
                    <option value="<?php echo esc_attr($product->ID); ?>" <?php selected(in_array($product->ID, $related_products)); ?>>
                        <?php echo esc_html($product->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="description"><?php esc_html_e('Hold Ctrl/Cmd to select multiple products.', 'hajri-cod-shop'); ?></p>
        </div>
        <?php
    }

    /**
     * Save the product meta data.
     *
     * @since    1.0.0
     * @param    int $post_id The ID of the post being saved.
     */
    public function save_product_meta($post_id) {
        // Check if our nonces are set and verify them
        if (!isset($_POST['hajri_product_details_nonce']) || 
            !wp_verify_nonce($_POST['hajri_product_details_nonce'], 'hajri_product_details')) {
            return;
        }
        
        if (!isset($_POST['hajri_product_recommendations_nonce']) || 
            !wp_verify_nonce($_POST['hajri_product_recommendations_nonce'], 'hajri_product_recommendations')) {
            return;
        }
        
        // If this is an autosave, our form has not been submitted, so we don't want to do anything
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check the user's permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Sanitize and save product details
        if (isset($_POST['hajri_product_price'])) {
            update_post_meta($post_id, '_hajri_product_price', sanitize_text_field($_POST['hajri_product_price']));
        }
        
        if (isset($_POST['hajri_product_sale_price'])) {
            update_post_meta($post_id, '_hajri_product_sale_price', sanitize_text_field($_POST['hajri_product_sale_price']));
        }
        
        if (isset($_POST['hajri_product_sku'])) {
            update_post_meta($post_id, '_hajri_product_sku', sanitize_text_field($_POST['hajri_product_sku']));
        }
        
        if (isset($_POST['hajri_product_stock'])) {
            update_post_meta($post_id, '_hajri_product_stock', intval($_POST['hajri_product_stock']));
        }
        
        if (isset($_POST['hajri_product_weight'])) {
            update_post_meta($post_id, '_hajri_product_weight', sanitize_text_field($_POST['hajri_product_weight']));
        }
        
        // Save related products
        if (isset($_POST['hajri_related_products'])) {
            $related_products = array_map('intval', $_POST['hajri_related_products']);
            update_post_meta($post_id, '_hajri_related_products', $related_products);
        } else {
            update_post_meta($post_id, '_hajri_related_products', array());
        }
    }

    /**
     * Get product data by ID.
     *
     * @since    1.0.0
     * @param    int $product_id The product ID.
     * @return   array The product data.
     */
    public static function get_product($product_id) {
        $product = get_post($product_id);
        
        if (!$product || $product->post_type !== 'hajri_product') {
            return false;
        }
        
        $price = get_post_meta($product_id, '_hajri_product_price', true);
        $sale_price = get_post_meta($product_id, '_hajri_product_sale_price', true);
        $stock = get_post_meta($product_id, '_hajri_product_stock', true);
        
        $current_price = (!empty($sale_price)) ? $sale_price : $price;
        
        return array(
            'id' => $product_id,
            'title' => $product->post_title,
            'description' => $product->post_content,
            'excerpt' => $product->post_excerpt,
            'price' => $price,
            'sale_price' => $sale_price,
            'current_price' => $current_price,
            'sku' => get_post_meta($product_id, '_hajri_product_sku', true),
            'stock' => $stock,
            'in_stock' => (!empty($stock) && intval($stock) > 0),
            'weight' => get_post_meta($product_id, '_hajri_product_weight', true),
            'image' => get_the_post_thumbnail_url($product_id, 'large'),
            'permalink' => get_permalink($product_id),
            'categories' => wp_get_post_terms($product_id, 'hajri_product_cat', array('fields' => 'all')),
        );
    }

    /**
     * Get related products for a product.
     *
     * @since    1.0.0
     * @param    int $product_id The product ID.
     * @return   array The related products.
     */
    public static function get_related_products($product_id) {
        $related_ids = get_post_meta($product_id, '_hajri_related_products', true);
        
        if (!is_array($related_ids) || empty($related_ids)) {
            return array();
        }
        
        $related_products = array();
        
        foreach ($related_ids as $id) {
            $product_data = self::get_product($id);
            if ($product_data) {
                $related_products[] = $product_data;
            }
        }
        
        return $related_products;
    }

    /**
     * Get all products or filtered by criteria.
     *
     * @since    1.0.0
     * @param    array $args Query arguments.
     * @return   array The products.
     */
    public static function get_products($args = array()) {
        $default_args = array(
            'post_type' => 'hajri_product',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
        );
        
        $args = wp_parse_args($args, $default_args);
        $query = new WP_Query($args);
        
        $products = array();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $products[] = self::get_product(get_the_ID());
            }
            wp_reset_postdata();
        }
        
        return $products;
    }
}

new Hajri_Cod_Shop_Product();
