<?php

/**
 * @package    Woocommerce_Price_Per_Word
 * @subpackage Woocommerce_Price_Per_Word/public
 * @author     Angell EYE <service@angelleye.com>
 */
class Woocommerce_Price_Per_Word_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of the plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/woocommerce-price-per-word-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        global $post, $product;
        if (is_object($post) && class_exists('WooCommerce')) {
            $is_product_type_variable = 'false';
            if (function_exists('wc_get_product')) {
                $product = wc_get_product($post);
                if ($product) {
                    if ($product->is_type('variable') && is_single()) {
                        $is_product_type_variable = 'true';
                    }
                }
            }

            $attach_id = (isset($_SESSION['attach_id']) && !empty($_SESSION['attach_id'])) ? $_SESSION['attach_id'] : '';
            if (!empty($attach_id)) {
                $total_word = get_post_meta($attach_id, 'total_word', true);
            } else {
                $total_word = '';
            }
            if (!empty($attach_id)) {
                $total_character = get_post_meta($attach_id, 'total_character', true);
            } else {
                $total_character = '';
            }
            $hide_qty = get_option('aewcppw_hide_qty_field');                      
            if($hide_qty =='yes'){
                $hide_qty_from_buyer = 'yes';
            }
            else{
                $hide_qty_from_buyer = '';
            }
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woocommerce-price-per-word-public.js', array('jquery'), $this->version, false);
            if (wp_script_is($this->plugin_name)) {
                wp_localize_script($this->plugin_name, 'woocommerce_price_per_word_params', apply_filters('woocommerce_price_per_word_params', array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'woocommerce_price_per_word_params_nonce' => wp_create_nonce("woocommerce_price_per_word_params_nonce"),
                    'total_word' => $total_word,
                    'total_character' => $total_character,
                    'is_product_type_variable' => $is_product_type_variable,
                    'is_enable_price_per_word_public' => $this->is_enable_price_per_word_public(),
                    'woocommerce_currency_symbol_js' => get_woocommerce_currency_symbol(),
                    'woocommerce_price_num_decimals' => wc_get_price_decimals(),
                    'aewcppw_word_character' => $this->wppw_get_product_type(),
                    'aewcppw_allow_users_to_enter_qty' => $this->aewcppw_allow_users_to_enter_qty(),
                    'aewcppw_hide_qty_from_buyer' => $hide_qty_from_buyer
                )));
            }
            wp_enqueue_script($this->plugin_name . '-bn', plugin_dir_url(__FILE__) . 'js/woocommerce-price-per-word-bn.js', array('jquery'), $this->version, false);
        }
    }

    public function is_enable_price_per_word_public() {
        global $post;
        if (isset($post->ID) && !empty($post->ID)) {
            $enable = get_post_meta($post->ID, '_price_per_word_character_enable', true);
            if (!empty($enable) && $enable == "yes") {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function wppw_get_product_type() {
        global $post;
        $aewcppw_word_character = get_post_meta($post->ID, '_price_per_word_character', TRUE);
        if (empty($aewcppw_word_character)) {
            $aewcppw_word_character = 'word';
        } elseif ($aewcppw_word_character == 'word') {
            $aewcppw_word_character = 'word';
        } elseif ($aewcppw_word_character == 'character') {
            $aewcppw_word_character = 'character';
        } else {
            $aewcppw_word_character = 'word';
        }
        return $aewcppw_word_character;
    }

    public function aewcppw_allow_users_to_enter_qty() {
        $aewcppw_allow_users_to_enter_qty = get_option('aewcppw_allow_users_to_enter_qty');
        if (empty($aewcppw_allow_users_to_enter_qty)) {
            $aewcppw_allow_users_to_enter_qty = 'no';
        } elseif ($aewcppw_allow_users_to_enter_qty == 'no') {
            $aewcppw_allow_users_to_enter_qty = 'no';
        } elseif ($aewcppw_allow_users_to_enter_qty == 'yes') {
            $aewcppw_allow_users_to_enter_qty = 'yes';
        } else {
            $aewcppw_allow_users_to_enter_qty = 'no';
        }
        return $aewcppw_allow_users_to_enter_qty;
    }
        
}
