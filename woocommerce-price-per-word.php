<?php

/**
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Price Per Word
 * Plugin URI:        https://www.angelleye.com/
 * Description:       Allow users to upload a document to calculate a price based on the 'price-per-word' set for the product/service.
 * Version:           1.2.2
 * Author:            Angell EYE
 * Author URI:        http://www.angelleye.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-price-per-word
 * Domain Path:       /languages
 * Requires at least: 4.4
 * Tested up to: 4.9.6
 * WC requires at least: 3.0.0
 * WC tested up to: 3.4.3
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
if (!defined('PPW_PLUGIN_URL')) {
    define('PPW_PLUGIN_URL', plugin_dir_url(__FILE__));
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woocommerce-price-per-word-activator.php
 */
function activate_woocommerce_price_per_word() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-woocommerce-price-per-word-activator.php';
    Woocommerce_Price_Per_Word_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woocommerce-price-per-word-deactivator.php
 */
function deactivate_woocommerce_price_per_word() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-woocommerce-price-per-word-deactivator.php';
    Woocommerce_Price_Per_Word_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_woocommerce_price_per_word');
register_deactivation_hook(__FILE__, 'deactivate_woocommerce_price_per_word');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-woocommerce-price-per-word.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

add_action('plugins_loaded', 'wppw_plugins_init', 0);

function run_woocommerce_price_per_word() {

    $plugin = new Woocommerce_Price_Per_Word();
    $plugin->run();
}

function wppw_plugins_init() {
    run_woocommerce_price_per_word();
}
