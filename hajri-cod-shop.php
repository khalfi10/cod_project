<?php
/**
 * Hajri COD Shop
 *
 * @link              https://example.com
 * @since             1.0.0
 * @package           Hajri_Cod_Shop
 *
 * @wordpress-plugin
 * Plugin Name:       Hajri COD Shop
 * Plugin URI:        https://example.com/hajri-cod-shop-uri/
 * Description:       A custom e-commerce system for WordPress with cash-on-delivery, targeting Algerian market.
 * Version:           1.0.0
 * Author:            Your Name
 * Author URI:        https://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       hajri-cod-shop
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Current plugin version.
 */
define('HAJRI_COD_SHOP_VERSION', '1.0.0');

/**
 * Plugin base directory.
 */
define('HAJRI_COD_SHOP_DIR', plugin_dir_path(__FILE__));

/**
 * Plugin base URL.
 */
define('HAJRI_COD_SHOP_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_hajri_cod_shop() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-hajri-cod-shop-activator.php';
    Hajri_Cod_Shop_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_hajri_cod_shop() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-hajri-cod-shop-deactivator.php';
    Hajri_Cod_Shop_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_hajri_cod_shop');
register_deactivation_hook(__FILE__, 'deactivate_hajri_cod_shop');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-hajri-cod-shop.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_hajri_cod_shop() {
    $plugin = new Hajri_Cod_Shop();
    $plugin->run();
}

run_hajri_cod_shop();
