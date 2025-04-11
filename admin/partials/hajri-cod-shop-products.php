<?php
/**
 * Provide an admin area view for managing products
 *
 * @link       https://example.com
 * @since      1.0.0
 * @package    Hajri_Cod_Shop
 * @subpackage Hajri_Cod_Shop/admin/partials
 */

// Direct access protection
defined('WPINC') or die;

// This file is not directly used as we're using WordPress' built-in admin UI for products
// It redirects to the WordPress edit.php for the custom post type

// Redirect to the WordPress products page
wp_redirect(admin_url('edit.php?post_type=hajri_product'));
exit;
