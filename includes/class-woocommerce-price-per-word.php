<?php

/**
 * @since      1.0.0
 * @package    Woocommerce_Price_Per_Word
 * @subpackage Woocommerce_Price_Per_Word/includes
 * @author     Angell EYE <service@angelleye.com>
 */
class Woocommerce_Price_Per_Word {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Woocommerce_Price_Per_Word_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {

        $this->plugin_name = 'woocommerce-price-per-word';
        $this->version = '1.2.2';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Woocommerce_Price_Per_Word_Loader. Orchestrates the hooks of the plugin.
     * - Woocommerce_Price_Per_Word_i18n. Defines internationalization functionality.
     * - Woocommerce_Price_Per_Word_Admin. Defines all hooks for the admin area.
     * - Woocommerce_Price_Per_Word_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-woocommerce-price-per-word-loader.php';        
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/parser/class.doccounter.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-woocommerce-price-per-word-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-woocommerce-price-per-word-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-woocommerce-price-per-word-public.php';

        $this->loader = new Woocommerce_Price_Per_Word_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Woocommerce_Price_Per_Word_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Woocommerce_Price_Per_Word_i18n();
        $plugin_i18n->set_domain($this->get_plugin_name());

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Woocommerce_Price_Per_Word_Admin($this->get_plugin_name(), $this->get_version());

        if (function_exists('WC')) {
            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
            $this->loader->add_action('product_type_options', $plugin_admin, 'product_type_options_own');
            $this->loader->add_action('woocommerce_process_product_meta_simple', $plugin_admin, 'woocommerce_process_product_meta_save');
            $this->loader->add_action('woocommerce_process_product_meta_variable', $plugin_admin, 'woocommerce_process_product_meta_save');
            $this->loader->add_action('woocommerce_before_add_to_cart_button', $plugin_admin, 'woocommerce_before_add_to_cart_button_own');
            $this->loader->add_filter('woocommerce_get_price_html', $plugin_admin, 'woocommerce_get_price_html_own', 10, 1);
            $this->loader->add_action('wp_ajax_ppw_uploads', $plugin_admin, 'ppw_file_upload_action', 10);
            $this->loader->add_action('wp_ajax_nopriv_ppw_uploads', $plugin_admin, 'ppw_file_upload_action', 10);
            $this->loader->add_filter('upload_mimes', $plugin_admin, 'woocommerce_price_per_word_extended_mime_types', 10, 1);
            $this->loader->add_filter('woocommerce_add_to_cart_redirect', $plugin_admin, 'woocommerce_add_to_cart_redirect_own', 10, 1);
            $this->loader->add_action('init', $plugin_admin, 'ppw_session_start', 1);
            $this->loader->add_action('wp_ajax_ppw_remove', $plugin_admin, 'woocommerce_price_per_word_ppw_remove', 10);
            $this->loader->add_action('wp_ajax_nopriv_ppw_remove', $plugin_admin, 'woocommerce_price_per_word_ppw_remove', 10);
            $this->loader->add_filter('woocommerce_add_cart_item_data', $plugin_admin, 'woocommerce_add_cart_item_data_own', 1, 2);
            $this->loader->add_filter('woocommerce_get_cart_item_from_session', $plugin_admin, 'woocommerce_get_cart_item_from_session_own', 1, 3);
            $this->loader->add_filter('woocommerce_checkout_cart_item_quantity', $plugin_admin, 'woocommerce_checkout_cart_item_quantity_own', 1, 3);
            $this->loader->add_filter('woocommerce_cart_item_price', $plugin_admin, 'woocommerce_checkout_cart_item_quantity_own', 1, 3);
            $this->loader->add_action('woocommerce_add_order_item_meta', $plugin_admin, 'woocommerce_add_order_item_meta_own', 1, 2);
            $this->loader->add_action('woocommerce_single_product_summary', $plugin_admin, 'woocommerce_single_product_summary_own', 11);
            $this->loader->add_filter('woocommerce_paypal_args', $plugin_admin, 'wppw_paypal_standard_additional_parameters', 10, 1);
            $this->loader->add_filter('woocommerce_product_options_pricing', $plugin_admin, 'wppw_woocommerce_product_options_pricing', 10);
            $this->loader->add_filter('woocommerce_product_after_variable_attributes', $plugin_admin, 'wppw_variation_panel', 10, 3);
            $this->loader->add_filter('woocommerce_before_calculate_totals', $plugin_admin, 'wppw_add_minimum_product_price', 10, 1);
            $this->loader->add_filter('woocommerce_save_product_variation', $plugin_admin, 'wppw_woocommerce_save_product_variation', 10, 2);
            $this->loader->add_filter( 'woocommerce_display_item_meta', $plugin_admin,'filter_woocommerce_display_item_meta', 10, 3 );

            /*
             * Action - Ajax 'bulk enable/disable tool' from settings/tools
             * @since	1.2.0
             */
            $this->loader->add_filter('wp_ajax_adminToolBulkEnableDisablePricePerWordsCharacters', $plugin_admin, 'adminToolBulkEnableDisablePricePerWordsCharactersCallback');
        } else {
            $this->loader->add_action('admin_notices', $plugin_admin, 'wppw_woocommerce_missing_notice');
        }
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Woocommerce_Price_Per_Word_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');        
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Woocommerce_Price_Per_Word_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}