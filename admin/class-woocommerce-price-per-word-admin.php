<?php

/**
 * @package    Woocommerce_Price_Per_Word
 * @subpackage Woocommerce_Price_Per_Word/admin
 * @author     Angell EYE <service@angelleye.com>
 */
class Woocommerce_Price_Per_Word_Admin {

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
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->load_dependencies();
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/woocommerce-price-per-word-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woocommerce-price-per-word-admin.js', array('jquery'), $this->version, false);
        if (wp_script_is($this->plugin_name)) {
            wp_localize_script($this->plugin_name, 'woocommerce_price_per_word_params', apply_filters('woocommerce_price_per_word_params', array(
                'woocommerce_currency_symbol_js' => '(' . get_woocommerce_currency_symbol() . ')'
            )));
        }
    }

    private function load_dependencies() {
        /**
         * The class responsible for defining all actions that occur in the Dashboard
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/woocommerce-price-per-word-admin-display.php';

        /**
         * The class responsible for defining function for display Html element
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/woocommerce-price-per-word-html-output.php';

        /**
         * The class responsible for defining function for display general setting tab
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/woocommerce-price-per-word-admin-setting.php';

        /**
         * The class responsible for defining function for display custom tab shown in product page
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/woocommerce-price-per-word-html-custom-tab.php';
    }

    /**
     * add enable/disable option for Price Per Word
     *
     * @since    1.0.0
     */
    public function product_type_options_own($product_type_options) {
        $wppw_get_product_type = $this->wppw_get_product_type();
        if ($wppw_get_product_type == 'word') {
            $type = "Word";
        } else {
            $type = "Character";
        }
        $product_type_options['price_per_word_character_enable'] = array(
            'id' => '_price_per_word_character_enable',
            'wrapper_class' => '',
            'label' => __("Enable Price Per Word/Character", 'woocommerce'),
            'description' => __("Enable Price Per Word/Character", 'woocommerce'),
            'default' => 'no'
        );
        return $product_type_options;
    }

    public function woocommerce_process_product_meta_save($post_id) {
        if (isset($_POST['_price_per_word_character_enable'])) {
            if ($_POST['_price_per_word_character_enable'] == "on") {
                update_post_meta($post_id, '_price_per_word_character_enable', "yes");
                if (isset($_POST['_price_per_word_character'])) {
                    if ($_POST['_price_per_word_character'] == "word") {
                        update_post_meta($post_id, '_price_per_word_character', "word");
                    } else {
                        update_post_meta($post_id, '_price_per_word_character', "character");
                    }
                } else {
                    update_post_meta($post_id, '_price_per_word_character', "word");
                }

                /*
                 * word count cap meta value
                 * */
                if (isset($_POST['_word_count_cap_status'])) {
                    update_post_meta($post_id, '_word_count_cap_status', wc_clean($_POST['_word_count_cap_status']));
                    update_post_meta($post_id, '_word_count_cap_word_limit', wc_clean($_POST['_word_count_cap_word_limit']));
                } else {
                    update_post_meta($post_id, '_word_count_cap_status', "close");
                }

                /*
                * Enable Price Breaks meta value
                * */
                if (isset($_POST['_is_enable_price_breaks'])) {
                    update_post_meta($post_id, '_is_enable_price_breaks', "yes");
                    $price_breaks_array = array();
                    for ($row = 0; $row < count($_POST['price-breaks-min']); $row++) {
                        if ((isset($_POST['price-breaks-min'][$row]) && strlen($_POST['price-breaks-min'][$row]))
                            && (isset($_POST['price-breaks-max'][$row]) && strlen($_POST['price-breaks-max'][$row]))
                            && (isset($_POST['price-breaks-price'][$row]) && strlen($_POST['price-breaks-price'][$row]))
                        ) {
                            $price_breaks_array[] = array(
                                "min" => wc_clean($_POST['price-breaks-min'][$row]),
                                "max" => wc_clean($_POST['price-breaks-max'][$row]),
                                "price" => wc_clean($_POST['price-breaks-price'][$row])
                            );
                        }
                    }
                    update_post_meta($post_id, '_price_breaks_array', maybe_serialize($price_breaks_array));
                } else {
                    update_post_meta($post_id, '_is_enable_price_breaks', "no");
                }

            } else {
                update_post_meta($post_id, '_price_per_word_character_enable', "no");
            }
        } else {
            update_post_meta($post_id, '_price_per_word_character_enable', "no");
        }


        if (isset($_POST['_minimum_product_price']) && !empty($_POST['_minimum_product_price']) && !is_array($_POST['_minimum_product_price'])) {
            update_post_meta($post_id, '_minimum_product_price', wc_clean($_POST['_minimum_product_price']));
        } else {
            delete_post_meta_by_key('_minimum_product_price');
        }
    }

    public function woocommerce_before_add_to_cart_button_own() {
        global $product;
        if(version_compare(WC_VERSION, '3.0', '<')){
            $product_id  = $product->id;
        } 
        else{                
            $product_id  = $product->get_id();
        }
        if ($this->is_enable_price_per_word()) {
            $display_or_hide_ppw_file_container = (isset($_SESSION['attach_id']) && !empty($_SESSION['attach_id']) && isset($_SESSION['product_id']) && isset($product_id) && $_SESSION['product_id'] == $product_id) ? '' : 'style="display: none"';
            $display_or_hide_ppw_file_upload_div = (isset($_SESSION['attach_id']) && !empty($_SESSION['attach_id']) && isset($_SESSION['product_id']) && isset($product_id) && $_SESSION['product_id'] == $product_id) ? 'style="display: none"' : 'a';
            $aewcppw_product_page_message = get_option('aewcppw_product_page_message');
            if (empty($aewcppw_product_page_message)) {
                $aewcppw_product_page_message = __('Please upload your .doc, .docx, .pdf , .txt .po , .pot to get a price.','woocommerce-price-per-word');
            }
            ?>
            <span
                id="aewcppw_product_page_message" <?php echo $display_or_hide_ppw_file_upload_div; ?>><?php echo $aewcppw_product_page_message; ?></span>
            <div class="ppw_file_upload_div" <?php echo $display_or_hide_ppw_file_upload_div; ?>>
                <label for="file_upload">Select your file(s)</label><input type="file" name="ppw_file_upload"
                                                                           value="Add File" id="ppw_file_upload_id">
            </div>
            <div id="ppw_loader" style="display: none;">
                <div class="ppw-spinner-loader"><?php esc_html__('Loading...','woocommerce-price-per-word'); ?></div>
            </div>
            <div id="ppw_file_container" class="woocommerce-message" <?php echo $display_or_hide_ppw_file_container; ?>>
                <?php
                if (session_id()) {
                    if (isset($_SESSION['attach_id']) && !empty($_SESSION['attach_id']) && isset($_SESSION['product_id']) && isset($product_id) && $_SESSION['product_id'] == $product_id) {
                        echo '<input type="hidden" name="file_uploaded" value="url">';
                        echo '<a id="ppw_remove_file" data_file="' . $_SESSION['attach_id'] . '" class="button wc-forward" title="' . esc_attr__('Remove file', 'woocommerce') . '" href="#">' . esc_html__('Delete','woocommerce-price-per-word') . '</a>'.esc_html__('File successfully uploaded','woocommerce-price-per-word');
                    }
                }
                ?>
            </div>
            <?php
        }
    }
            
    public function woocommerce_get_price_html_own($price) {
        global $product;
        if(version_compare(WC_VERSION, '3.0', '<')){
            $product_price  = $product->price;
        } 
        else{                
            $product_price  = $product->get_price();
        }        
        $wppw_get_product_type = $this->wppw_get_product_type();
        if ($wppw_get_product_type == 'word') {
            $type = "Word";
        } else {
            $type = "Character";
        }
        if ($this->is_enable_price_per_word()) {
                if ( $product->is_type( 'variable' ) ) {
                    $children   = $product->get_children( $args = '', $output = OBJECT ); 
                    $price_array = array();
                    foreach ($children as $key=>$value) {
                        $product_variatons = new WC_Product_Variation($value);
                        $price_array[] = $product_variatons->get_price();
                    }
                    $min = min($price_array);
                    $max = max($price_array);                   
                    $price = $min.'-'.$max;                    
                }
            if (is_numeric($product_price) && !is_string($price)) {
                $decimals = strlen(substr($product_price, strpos($product_price, ".") + 1));
                $decimals = $decimals < wc_get_price_decimals() ? wc_get_price_decimals() : $decimals;
                $price = wc_price($product_price, array("decimals" => $decimals));
            }
            $label = apply_filters("ppw_change_html_label_of_price", "Price Per $type:", $type);
            return __($label, "woocommerce-price-per-word") . " " . $price;
        } else {
            return $price;
        }
    }

    public function ppw_file_upload_action() {
        add_filter('upload_dir', array($this, 'woocommerce_price_per_word_upload_dir'), 10, 1);
        $product_id = sanitize_text_field($_POST['product_id']);   
        $pid = $this->wppw_get_product_id();
        $_product = wc_get_product($pid);        
        $pprice = $_product->get_price();
        $return_messge = array('total_word' => '', 'total_character' => '', 'aewcppw_word_character' => $this->wppw_get_product_type_by_product_id($product_id), 'message' => __('File successfully uploaded','woocommerce-price-per-word'), 'url' => '', 'message_content' => '', 'product_price' => '', 'product_id' => $pid,'pprice' => $pprice);
        if (isset($_POST['security']) && !empty($_POST['security'])) {
            if (wp_verify_nonce($_POST['security'], 'woocommerce_price_per_word_params_nonce')) {
                if (!function_exists('wp_handle_upload')) {
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                }
                $uploadedfile = $_FILES['file'];
                $upload_overrides = array('test_form' => false);
                $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
                if ($movefile && !isset($movefile['error'])) {
                    $fileArray = pathinfo($movefile['file']);
                    $file_ext = $fileArray['extension'];
                    $return_messge['url'] = $movefile['url'];
                    if ($file_ext == "doc" || $file_ext == "docx" || $file_ext == "pdf" || $file_ext == 'txt') {                        
                        $doc = new DocCounter();
                        $doc->setFile($movefile['file']);
                        $info = $doc->getInfo();
                        if(isset($info->wordCount) && isset($info->charCount)){
                            $total_words = $info->wordCount;
                            $total_characters = $info->charCount;
                            $enable_word_count_cap = $this->is_word_count_cap_enable($product_id);
                            if ($enable_word_count_cap) {
                                $word_count_cap_word_limit = get_post_meta($product_id, '_word_count_cap_word_limit', TRUE);
                                $product_type = $this->wppw_get_product_type_by_product_id($product_id);
                                if ($product_type == 'word') {
                                    $limit_of_word_or_character = $total_words;
                                } else {
                                    $limit_of_word_or_character = $total_characters;
                                }
                                if ($limit_of_word_or_character > $word_count_cap_word_limit) {
                                    $return_messge = array('total_word' => '', 'message' => __('Your file contains more ','woocommerce-price-per-word').$product_type . __('s than word cap limit. Maximum ','woocommerce-price-per-word') . $product_type . __('s limit is ','woocommerce-price-per-word') . $word_count_cap_word_limit, 'url' => '');
                                    $return_messge['total_word'] = $total_words;
                                    $return_messge['total_character'] = $total_characters;
                                    $return_messge['message_content'] = __('The file upload failed, Please choose a file having less ','woocommerce-price-per-word') . $product_type . __('s than limit one.','woocommerce-price-per-word');
                                    @unlink($movefile['file']);
                                    echo json_encode($return_messge, true);
                                    exit();
                                }
                            }                           
                            $attach_id = $this->ppw_upload_file_to_media($movefile['file'], $total_words, $total_characters);
                            $attachment_page = wp_get_attachment_url($attach_id);
                            $return_messge['total_word'] = $total_words;
                            $return_messge['total_character'] = $total_characters;
                            $return_messge = $this->wppw_get_product_price($return_messge);
                            $_SESSION['attach_id'] = $attach_id;
                            $_SESSION['total_words'] = $total_words;
                            $_SESSION['total_characters'] = $total_characters;
                            $_SESSION['product_price'] = $return_messge['product_price'];
                            $_SESSION['product_id'] = $product_id;
                            $_SESSION['file_name'] = '<a href="' . esc_url($attachment_page) . '" target="_blank">' . esc_html($fileArray['basename']) . '</a>';
                            $return_messge['message_content'] = '<a id="ppw_remove_file" data_file="' . $attach_id . '" class="button wc-forward" title="' . esc_attr__('Remove file', 'woocommerce') . '" href="#">' . __("Delete",'woocommerce-price-per-word') . '</a>'.__('File successfully uploaded','woocommerce-price-per-word');

                            echo json_encode($return_messge, true);
                        }
                        else{
                            $return_messge = array('total_word' => '', 'message' => __('Your file is secured or empty.','woocommerce-price-per-word'), 'url' => '');
                            $return_messge['message_content'] = __('The file upload failed, Please choose a valid file extension and try again.','woocommerce-price-per-word');
                            echo json_encode($return_messge, true);
                        }                                                
                    }
                    else if ($file_ext == 'po' || $file_ext == 'pot') {
                        $handle = fopen($movefile['file'], "r");
                        if ($handle) {
                            $total_words = 0;
                            $total_characters = 0;
                            while (($line = fgets($handle)) !== false) {
                                if(substr( $line, 0, 5 ) === "msgid"){
                                    if($line != 'msgid ""'){
                                        $translation_words = $this->GetBetween('"','"',$line);                                    
                                        $total_words += count(str_word_count($translation_words, 1));
                                        $total_characters += strlen(utf8_decode($translation_words));
                                    }
                                }
                            }
                            fclose($handle);
                        } else {
                            // error opening the file.
                        }                                                
                        
                        $enable_word_count_cap = $this->is_word_count_cap_enable($product_id);
                        if ($enable_word_count_cap) {
                            $word_count_cap_word_limit = get_post_meta($product_id, '_word_count_cap_word_limit', TRUE);
                            $product_type = $this->wppw_get_product_type_by_product_id($product_id);
                            if ($product_type == 'word') {
                                $limit_of_word_or_character = $total_words;
                            } else {
                                $limit_of_word_or_character = $total_characters;
                            }
                            if ($limit_of_word_or_character > $word_count_cap_word_limit) {
                                $return_messge = array('total_word' => '', 'message' => 'Your file contains more ' . $product_type . 's than word cap limit. Maximum ' . $product_type . 's limit is ' . $word_count_cap_word_limit, 'url' => '');
                                $return_messge['total_word'] = $total_words;
                                $return_messge['total_character'] = $total_characters;
                                $return_messge['message_content'] = 'The file upload failed, Please choose a file having less ' . $product_type . 's than limit one.';
                                @unlink($movefile['file']);
                                echo json_encode($return_messge, true);
                                exit();
                            }
                        }

                        $attach_id = $this->ppw_upload_file_to_media($movefile['file'], $total_words, $total_characters);
                        $attachment_page = wp_get_attachment_url($attach_id);
                        $return_messge['total_word'] = $total_words;
                        $return_messge['total_character'] = $total_characters;
                        $return_messge = $this->wppw_get_product_price($return_messge);
                        $_SESSION['attach_id'] = $attach_id;
                        $_SESSION['total_words'] = $total_words;
                        $_SESSION['total_characters'] = $total_characters;
                        $_SESSION['product_price'] = $return_messge['product_price'];
                        $_SESSION['product_id'] = $product_id;
                        //$_SESSION['product_price'] = $_product->get_price();
                        $_SESSION['file_name'] = '<a href="' . esc_url($attachment_page) . '" target="_blank">' . esc_html($fileArray['basename']) . '</a>';
                        $return_messge['message_content'] = '<a id="ppw_remove_file" data_file="' . $attach_id . '" class="button wc-forward" title="' . esc_attr__('Remove file', 'woocommerce') . '" href="#">' . "Delete" . '</a>File successfully uploaded';
                        echo json_encode($return_messge, true);
                    }
                    else {
                        $return_messge = array('total_word' => '', 'message' => __('The file upload failed, Please choose a valid file extension and try again.','woocommerce-price-per-word'), 'url' => '');
                        echo json_encode($return_messge, true);
                    }
                    exit();
                } else {                    
                    $return_messge = array('total_word' => '', 'message' => $movefile['error'], 'url' => '');
                    echo json_encode($return_messge, true);
                    exit();
                }
            } else {
                $return_messge = array('total_word' => '', 'message' => __('security problem, wordpress nonce is not verified','woocommerce-price-per-word'), 'url' => '');
                echo json_encode($return_messge, true);
                exit();
            }
        } else {
            $return_messge = array('total_word' => '', 'message' => __('security problem, wordpress nonce is not verified','woocommerce-price-per-word'), 'url' => '');
            echo json_encode($return_messge, true);
            exit();
        }
    }

    public function GetBetween($var1="",$var2="",$pool){
        $temp1 = strpos($pool,$var1)+strlen($var1);
        $result = substr($pool,$temp1,strlen($pool));
        $dd=strpos($result,$var2);
        if($dd == 0){
            $dd = strlen($result);
        }        
        return substr($result,0,$dd);
    }
    
    public function woocommerce_price_per_word_upload_dir($param) {
        $custom_dir = '/woocommerce-price-per-word';
        $param['path'] = $param['basedir'] . $custom_dir;
        $param['url'] = $param['basedir'] . $custom_dir;
        return $param;
    }

    public function woocommerce_price_per_word_extended_mime_types($mime_types) {
        $mime_types['doc-dot'] = 'doc|dot|word|w6w application/msword';
        $mime_types['po'] = 'po text/x-gettext-translation';
        return $mime_types;
    }

    public function woocommerce_add_to_cart_redirect_own($cart_url) {
        if (isset($cart_url) && !empty($cart_url)) {
            return $cart_url;
        }
    }

    public function ppw_upload_file_to_media($filename, $count_word, $total_characters) {
        $parent_post_id = '';
        $filetype = wp_check_filetype(basename($filename), null);
        $wp_upload_dir = wp_upload_dir();
        $attachment = array(
            'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
            'post_mime_type' => $filetype['type'],
            'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        $attach_id = wp_insert_attachment($attachment, $filename, $parent_post_id);
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
        wp_update_attachment_metadata($attach_id, $attach_data);
        update_post_meta($attach_id, 'total_word', $count_word);
        update_post_meta($attach_id, 'total_character', $total_characters);
        return $attach_id;
    }

    public function ppw_session_start() {
        if (!session_id()) {
            session_start();
        }
    }

    public function woocommerce_price_per_word_ppw_remove() {
        $return_messge = array('message' => __('File successfully deleted','woocommerce-price-per-word'));
        if (isset($_POST['security']) && !empty($_POST['security'])) {
            if (wp_verify_nonce($_POST['security'], 'woocommerce_price_per_word_params_nonce')) {
                if (isset($_POST['value']) && !empty($_POST['value'])) {
                    wp_delete_attachment($_POST['value'], true);
                    unset($_SESSION['attachment_anchor_tag_url']);
                    unset($_SESSION['attach_id']);
                    unset($_SESSION['total_words']);
                    unset($_SESSION['total_characters']);
                    unset($_SESSION['file_name']);
                    echo json_encode($return_messge, true);
                    exit();
                }
            }
        }
    }

    public function woocommerce_add_cart_item_data_own($cart_item_data, $product_id) {
        global $woocommerce;
        $custom_cart_data = array();
        if (isset($_SESSION['attach_id']) && !empty($_SESSION['attach_id'])) {
            $custom_cart_data['attach_id'] = $_SESSION['attach_id'];
            unset($_SESSION['attach_id']);
        }
        if (isset($_SESSION['total_words']) && !empty($_SESSION['total_words'])) {
            $custom_cart_data['total_words'] = $_SESSION['total_words'];
            unset($_SESSION['total_words']);
        }
        if (isset($_SESSION['total_characters']) && !empty($_SESSION['total_characters'])) {
            $custom_cart_data['total_characters'] = $_SESSION['total_characters'];
            unset($_SESSION['total_characters']);
        }
        if (isset($_SESSION['file_name']) && !empty($_SESSION['file_name'])) {
            $custom_cart_data['file_name'] = $_SESSION['file_name'];
            unset($_SESSION['file_name']);
        }
        if (isset($_SESSION['attachment_anchor_tag_url']) && !empty($_SESSION['attachment_anchor_tag_url'])) {
            $custom_cart_data['attachment_anchor_tag_url'] = $_SESSION['attachment_anchor_tag_url'];
            unset($_SESSION['attachment_anchor_tag_url']);
        }
        $ppw_custom_cart_data_value = array('ppw_custom_cart_data' => $custom_cart_data);
        if (empty($custom_cart_data)) {
            return $cart_item_data;
        } else {
            if (empty($cart_item_data)) {
                return $ppw_custom_cart_data_value;
            } else {
                return array_merge($cart_item_data, $ppw_custom_cart_data_value);
            }
        }
    }

    public function woocommerce_get_cart_item_from_session_own($item, $values, $key) {
        if (array_key_exists('ppw_custom_cart_data', $values)) {
            $item['ppw_custom_cart_data'] = $values['ppw_custom_cart_data'];
        }
        return $item;
    }

    public function woocommerce_checkout_cart_item_quantity_own($product_name, $values, $cart_item_key) {
        
        if(version_compare(WC_VERSION, '3.0', '<')){
            $product_price  = $values["data"]->price;
        } 
        else{                
            $product_price  = $values["data"]->get_price();
        }
        if (is_numeric($product_price)) {
            $decimals = strlen(substr($product_price, strpos($product_price, ".") + 1));
            $decimals = $decimals < wc_get_price_decimals() ? wc_get_price_decimals() : $decimals;
        }

        if (isset($values['ppw_custom_cart_data']) && count($values['ppw_custom_cart_data']) > 0) {
            $return_string = wc_price($product_price, array("decimals" => $decimals));
            if (isset($values['ppw_custom_cart_data']['file_name']) && !empty($values['ppw_custom_cart_data']['file_name'])) {

                $return_string .= '<div><span><b>'.esc_html__('File Name:','woocommerce-price-per-word').'</b></span>';
                $return_string .= "<span>" . $values['ppw_custom_cart_data']['file_name'] . "</span></div>";
            }
            $wppw_get_product_type = $this->wppw_get_product_type_by_product_id($values['product_id']);
            $hide_qty = get_option('aewcppw_hide_qty_field');                                  
            if ($wppw_get_product_type == 'word') {
                if (isset($values['ppw_custom_cart_data']['total_words']) && !empty($values['ppw_custom_cart_data']['total_words'])) {
                    if($hide_qty !='yes'){
                        $return_string .= '<div><span><b>'.esc_html__('Total Words: ','woocommerce-price-per-word').'</b></span>';
                        $return_string .= "<span>" . $values['ppw_custom_cart_data']['total_words'] . "</span></div>";
                    }                    
                }
            } else {
                if (isset($values['ppw_custom_cart_data']['total_characters']) && !empty($values['ppw_custom_cart_data']['total_characters'])) {
                    if($hide_qty !='yes'){
                        $return_string .= '<div><span><b>'.esc_html__('Total Characters: ','woocommerce-price-per-word').'</b></span>';
                        $return_string .= "<span>" . $values['ppw_custom_cart_data']['total_characters'] . "</span></div>";
                    }                    
                }
            }
            return $return_string;
        } else {
            return $product_name;
        }
    }

    public function woocommerce_add_order_item_meta_own($item_id, $values) {
        global $woocommerce, $wpdb;        
        $user_custom_values = (isset($values['ppw_custom_cart_data']) && !empty($values['ppw_custom_cart_data'])) ? $values['ppw_custom_cart_data'] : '';        
        if (!empty($user_custom_values)) {
            foreach ($user_custom_values as $key => $value) {
                if ($key != "attach_id") {
                    $key = ucwords(str_replace("_", " ", $key));
                    wc_add_order_item_meta($item_id, $key, $value);
                }
            }
            wc_add_order_item_meta($item_id, 'ppw_custom_cart_data', $user_custom_values);
        }        
    }

    public function is_enable_price_per_word() {
        global $product;        
        if(version_compare(WC_VERSION, '3.0', '<')){
            $product_id  = $product->id;
        } 
        else{                
            $product_id  = $product->get_id();
        }
        if (isset($product_id) && !empty($product_id)) {
            $enable = get_post_meta($product_id, '_price_per_word_character_enable', true);
            if (!empty($enable) && $enable == "yes") {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function woocommerce_single_product_summary_own() {
        if ($this->is_enable_price_per_word()) {
            global $product;            
            if(version_compare(WC_VERSION, '3.0', '<')){                
                $product_id  = $product->id;
            } 
            else{                
                $product_id  = $product->get_id();
            }
            $total_price = 0;
            if (isset($_SESSION['product_id']) && isset($product_id) && $_SESSION['product_id'] == $product_id) {
                $total_price = $_SESSION['product_price'];
            }
            $product_type = $this->wppw_get_product_type();
            if ($this->is_price_breaks_enable($product_id)) {                
                $price_break_array = get_post_meta($product_id, '_price_breaks_array',true);
                $price_break_array = unserialize($price_break_array);                
                echo 'Price Per '.ucfirst($product_type) . ' Range';
                foreach ($price_break_array as $value) {
                    ?>
                    <div class='cprice-range'><label style='background-color: #f2f2f2;padding: 5px;'>
                    <?php
                    if($value['max'] != '>'){                        
                        echo __('For ','').$value['min']. " - ".$value['max']. "".__(" ".ucfirst($product_type)."s, ",'').__('Price is ','')."<span>".wc_price($value['price'])."</span>";
                    }
                    else{
                        echo $value['min'].__(' or more ','').__(ucfirst($product_type)."s, ",'').__('Price is ','')."<span>".wc_price($value['price'])."</span>";
                    }
                 ?>
                  </label></div>
                  <?php
                }
            }
            $style = "style='display:none;'";
            echo "<div><p class='ppw_total_price price' $style>".esc_html__('Total Price:','woocommerce-price-per-word')."<span class='ppw_total_amount'>" . $total_price . "</span></p></div>";
        }
    }

    public function wppw_paypal_standard_additional_parameters($paypal_args) {
        $paypal_args['bn'] = 'AngellEYE_SP_WooCommerce';
        return $paypal_args;
    }

    public function wppw_woocommerce_missing_notice() {
        echo '<div class="error"><p>' . sprintf(__('WooCommerce Price Per Word Plugin requires the %s to work!', 'woocommerce-price-per-word'), '<a href="https://wordpress.org/plugins/woocommerce/" target="_blank">' . __('WooCommerce', 'woocommerce-price-per-word') . '</a>') . '</p></div>';
    }

    public function wppw_get_product_type() {
        global $post, $product;
        $product = wc_get_product($post);
        if(version_compare(WC_VERSION, '3.0', '<')){
            $product_id  = $product->id;
        } 
        else{                
            $product_id  = $product->get_id();
        }
        $aewcppw_word_character = get_post_meta($product_id, '_price_per_word_character', TRUE);
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

    public function wppw_get_product_type_by_product_id($product_id) {
        $aewcppw_word_character = get_post_meta($product_id, '_price_per_word_character', TRUE);
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

    public function wppw_woocommerce_product_options_pricing() {
        woocommerce_wp_text_input(array('id' => '_minimum_product_price', 'label' => __('Minimum product price', 'woocommerce') . ' (' . get_woocommerce_currency_symbol() . ')', 'data_type' => 'price'));
    }

    public function wppw_variation_panel($loop, $variation_data, $variation) {
        $_minimum_product_price = get_post_meta($variation->ID, '_minimum_product_price', true);
        ?>
        <div>
            <p class="form-row form-row-first">
                <label><?php _e('Minimum Price', 'min-max-quantities-for-woocommerce'); ?>
                    <input type="number" size="5" name="_minimum_product_price[<?php echo $loop; ?>]"
                           value="<?php if ($_minimum_product_price) echo esc_attr($_minimum_product_price); ?>"/></label>
            </p>
        </div>
        <?php
    }

    public function wppw_add_minimum_product_price($cart_object) {
        foreach ($cart_object->cart_contents as $cart_key => $value) {
            if ($this->wppw_is_enable_price_per_word($value['product_id'])) {
                if (isset($value['variation_id']) && !empty($value['variation_id'])) {
                    $_minimum_product_price = $this->wppw_get_minimum_price($value['variation_id']);
                    $sale_price = get_post_meta($value['variation_id'], '_sale_price', true);
                    if ($this->is_price_breaks_enable($value['product_id'])) {
                        $product_price = $this->get_price_from_price_break_range($value['product_id'], array("total_word" => $value['quantity'],
                            "total_character" => $value['quantity']));
                        if (isset($product_price) && !empty($product_price)) {
                            $product_price = $product_price;
                        } else if (isset($sale_price) && !empty($sale_price)) {
                            $product_price = $sale_price;
                        } else {
                            $product_price = get_post_meta($value['product_id'], '_regular_price', true);
                        }
                    } else {
                        if (isset($sale_price) && !empty($sale_price)) {
                            $product_price = $sale_price;
                        } else {
                            $product_price = get_post_meta($value['variation_id'], '_regular_price', true);
                        }
                    }
                } elseif (isset($value['product_id']) && !empty($value['product_id'])) {
                    $_minimum_product_price = $this->wppw_get_minimum_price($value['product_id']);
                    $sale_price = get_post_meta($value['product_id'], '_sale_price', true);
                    if ($this->is_price_breaks_enable($value['product_id'])) {
                        $product_price = $this->get_price_from_price_break_range($value['product_id'], array("total_word" => $value['quantity'],
                            "total_character" => $value['quantity']));
                        if (isset($product_price) && !empty($product_price)) {
                            $product_price = $product_price;
                        } else if (isset($sale_price) && !empty($sale_price)) {
                            $product_price = $sale_price;
                        } else {
                            $product_price = get_post_meta($value['product_id'], '_regular_price', true);
                        }
                    } else {
                        if (isset($sale_price) && !empty($sale_price)) {
                            $product_price = $sale_price;
                        } else {
                            $product_price = get_post_meta($value['product_id'], '_regular_price', true);
                        }
                    }

                }
                $_minimum_product_price = $this->wppw_get_minimum_price($value['product_id']);
                if ($this->wppw_get_product_type_by_product_id($value['product_id']) == 'word') {
                    if (isset($value['ppw_custom_cart_data']['total_words']) && !empty($value['ppw_custom_cart_data']['total_words'])) {
                        $total_words = $value['ppw_custom_cart_data']['total_words'];
                    } else {
                        $total_words = 1;
                    }
                    $final_product_price = $product_price * $total_words;
                } else {
                    if (isset($value['ppw_custom_cart_data']['total_characters']) && !empty($value['ppw_custom_cart_data']['total_characters'])) {
                        $total_characters = $value['ppw_custom_cart_data']['total_characters'];
                    } else {
                        $total_characters = 1;
                    }
                    $final_product_price = $total_characters;
                }                                
                if ($_minimum_product_price != false && (floatval($_minimum_product_price) > floatval($final_product_price))) {                   
                    $product_final_price = $_minimum_product_price;
                    $value['data']->price = $product_final_price;
                    WC()->cart->set_quantity($cart_key, $_minimum_product_price, false);
                } else if ($this->is_price_breaks_enable($value['product_id']) && (floatval($_minimum_product_price) < floatval($final_product_price))) {

                    if(version_compare(WC_VERSION, '3.0', '<')){
                        $product_final_price = $product_price;
                        $value['data']->price = $product_final_price;
                    } 
                    else{                
                        $value['data']->set_price($product_price);
                    }                    
                    if ($this->wppw_get_product_type_by_product_id($value['product_id']) == 'word') {
                        WC()->cart->set_quantity($cart_key, $value['quantity'], false);
                    } else {
                        WC()->cart->set_quantity($cart_key, $value['quantity'], false);
                    }
                }
            }
        }
    }

    public function wppw_woocommerce_save_product_variation($variation_id, $i) {
        $minimum_allowed_quantity = isset($_POST['_minimum_product_price']) ? $_POST['_minimum_product_price'] : '';
        update_post_meta($variation_id, '_minimum_product_price', wc_clean($minimum_allowed_quantity[$i]));
    }

    public function wppw_get_minimum_price($product_id = null) {
        $minimum_product_price = get_post_meta($product_id, '_minimum_product_price', true);
        if (isset($minimum_product_price) && !empty($minimum_product_price)) {
            return $minimum_product_price;
        } else {
            $minimum_product_price = get_option('_minimum_product_price');
            if (isset($minimum_product_price) && !empty($minimum_product_price)) {
                return $minimum_product_price;
            } else {
                return false;
            }
        }
    }

    public function wppw_get_product_price($return_messge) {
        $minimum_product_price = $this->wppw_get_minimum_price($return_messge['product_id']);

        // Check to Price breaks enable or not
        $enable_price_break = $this->is_price_breaks_enable($return_messge['product_id']);
        if ($enable_price_break) {
            $product = wc_get_product($return_messge['product_id']);
            if ($product->is_type('simple')) {

                if ($return_messge['aewcppw_word_character'] == 'word') {
                    $total_word_or_character = $return_messge['total_word'];
                } else {
                    $total_word_or_character = $return_messge['total_character'];
                }

                $product_price = $this->get_price_from_price_break_range($return_messge['product_id'], $return_messge);
                $sale_price = get_post_meta($return_messge['product_id'], '_sale_price', true);
                if (isset($product_price) && !empty($product_price)) {
                    $product_price = $product_price;
                } else if (isset($sale_price) && !empty($sale_price)) {
                    $product_price = $sale_price;
                } else {
                    $product_price = get_post_meta($return_messge['product_id'], '_regular_price', true);
                }
                if ($return_messge['aewcppw_word_character'] == 'word') {
                    $price_amount = $product_price * $return_messge['total_word'];
                } else {
                    $price_amount = $product_price * $return_messge['total_character'];
                }

                if ($minimum_product_price == true && (floatval($minimum_product_price) > floatval($price_amount))) {
                    $product_final_price = $minimum_product_price;
                } else {
                    $product_final_price = $price_amount;
                }
                $_SESSION['breaks_price_of_product'] = $product_price;

                if (is_numeric($product_final_price)) {
                    $decimals = strlen(substr($product_final_price, strpos($product_final_price, ".") + 1));
                    $decimals = $decimals < wc_get_price_decimals() ? wc_get_price_decimals() : $decimals;
                }

                $price_clean = array('<span class="amount">', '</span>');
                $return_messge['product_price'] = str_replace($price_clean, '', wc_price($product_final_price, array("decimals" => $decimals)));
                return $return_messge;
            }
        }


        $sale_price = get_post_meta($return_messge['product_id'], '_sale_price', true);
        if (isset($sale_price) && !empty($sale_price)) {
            $product_price = $sale_price;
        } else {
            $product_price = get_post_meta($return_messge['product_id'], '_regular_price', true);
        }
        if ($return_messge['aewcppw_word_character'] == 'word') {
            $price_amount = $product_price * $return_messge['total_word'];
        } else {
            $price_amount = $product_price * $return_messge['total_character'];
        }
        if ($minimum_product_price == true && (floatval($minimum_product_price) > floatval($price_amount))) {
            $product_final_price = $minimum_product_price;
        } else {
            $product_final_price = $price_amount;
        }

        if (is_numeric($product_final_price)) {
            $decimals = strlen(substr($product_final_price, strpos($product_final_price, ".") + 1));
            $decimals = $decimals < wc_get_price_decimals() ? wc_get_price_decimals() : $decimals;
        }

        $price_clean = array('<span class="amount">', '</span>');
        $return_messge['product_price'] = str_replace($price_clean, '', wc_price($product_final_price, array("decimals" => $decimals)));
        return $return_messge;
    }

    public function get_price_from_price_break_range($product_id, $return_data) {
        $price_breaks = $this->get_product_price_breaks($product_id);
        $wppw_get_product_type = $this->wppw_get_product_type_by_product_id($product_id);
        if ($wppw_get_product_type == 'word') {
            $total_word_or_character = $return_data['total_word'];
        } else {
            $total_word_or_character = $return_data['total_character'];
        }
        for ($price_break = 0; $price_break < count($price_breaks); $price_break++) {
            if ($total_word_or_character >= $price_breaks[$price_break]['min']) {
                if ($price_breaks[$price_break]['max'] == '>') {
                    return $product_price = $price_breaks[$price_break]['price'];
                } else if ($total_word_or_character <= $price_breaks[$price_break]['max']) {
                    return $product_price = $price_breaks[$price_break]['price'];
                }
            }
        }
    }

    public function wppw_get_product_id() {
        $product_id = '';
        if (isset($_POST['variation_id']) && !empty($_POST['variation_id']) && $_POST['variation_id'] != "undefined") {
            $product_id = sanitize_text_field($_POST['variation_id']);
        } else {
            $product_id = sanitize_text_field($_POST['product_id']);
        }
        return $product_id;
    }

    public function wppw_is_enable_price_per_word($product_id) {
        if (isset($product_id) && !empty($product_id)) {
            $enable = get_post_meta($product_id, '_price_per_word_character_enable', true);
            if (!empty($enable) && $enable == "yes") {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Check words count cap enable or not for specific proudct.
     * @var      string $product_id product id.
     * @return  true or false.
     * @since    1.2.0
     */
    public function is_word_count_cap_enable($product_id) {
        if (isset($product_id) && !empty($product_id)) {
            $enable = get_post_meta($product_id, '_word_count_cap_status', true);
            if (!empty($enable) && $enable == "open") {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Check price breask enable or not for specific proudct.
     * @var      string $product_id product id.
     * @return  true or false.
     * @since    1.2.0
     */
    public function is_price_breaks_enable($product_id) {
        if (isset($product_id) && !empty($product_id)) {
            $enable = get_post_meta($product_id, '_is_enable_price_breaks', true);
            if (!empty($enable) && $enable == "yes") {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Get the price breaks data of product.
     * @var      string $product_id product id.
     * @return unserialize data of price breaks.
     * @since    1.2.0
     */
    public function get_product_price_breaks($product_id) {
        $price_breaks_serialize = get_post_meta($product_id, '_price_breaks_array', true);
        $price_breaks = maybe_unserialize($price_breaks_serialize);
        return $price_breaks;
    }

    /*
     * Action - Ajax 'bulk enable/disable tool' from Price per Words settings/tools
     * @since	1.2.0
     */
    public function adminToolBulkEnableDisablePricePerWordsCharactersCallback() {
        if (is_admin() && (defined('DOING_AJAX') || DOING_AJAX)) {
            global $wpdb;
            $processed_product_id = array();
            $errors = FALSE;
            $products = FALSE;
            $product_ids = FALSE;
            $update_count = 0;
            $where_args = array(
                'post_type' => array('product', 'product_variation'),
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'fields' => 'id=>parent',
            );
            $where_args['meta_query'] = array();

            $ppw_bulk_action_type = (isset($_POST["actionType"])) ? wc_clean($_POST['actionType']) : FALSE;
            $ppw_bulk_action_target_type = (isset($_POST["actionTargetType"])) ? wc_clean($_POST['actionTargetType']) : FALSE;
            $ppw_bulk_action_target_where_type = (isset($_POST["actionTargetWhereType"])) ? wc_clean($_POST['actionTargetWhereType']) : FALSE;
            $ppw_bulk_action_target_where_category = (isset($_POST["actionTargetWhereCategory"])) ? wc_clean($_POST['actionTargetWhereCategory']) : FALSE;
            $ppw_bulk_action_target_where_product_type = (isset($_POST["actionTargetWhereProductType"])) ? wc_clean($_POST['actionTargetWhereProductType']) : FALSE;
            $ppw_bulk_action_target_where_price_value = (isset($_POST["actionTargetWherePriceValue"])) ? wc_clean($_POST['actionTargetWherePriceValue']) : FALSE;
            $ppw_bulk_action_target_where_stock_value = (isset($_POST["actionTargetWhereStockValue"])) ? wc_clean($_POST['actionTargetWhereStockValue']) : FALSE;
            $ppw_meta_key_value = (isset($_POST['ppw_meta_key_value']) && !empty($_POST["ppw_meta_key_value"])) ? wc_clean($_POST['ppw_meta_key_value']) : FALSE;


            if (!$ppw_bulk_action_type || !$ppw_bulk_action_target_type) {
                $errors = TRUE;
            }
            if (!$ppw_meta_key_value) {
                $errors = TRUE;
            }

            $ppw_bulk_action_type_status = ($ppw_bulk_action_type == 'enable_price_per_words' || $ppw_bulk_action_type == 'enable_price_per_characters') ? 'yes' : 'no';

            // All Products
            if ($ppw_bulk_action_target_type == 'all') {
                $products = new WP_Query($where_args);
            } // Featured products
            elseif ($ppw_bulk_action_target_type == 'featured') {
                array_push($where_args['meta_query'],
                    array(
                        'key' => '_featured',
                        'value' => 'yes'
                    )
                );
                $products = new WP_Query($where_args);
            } // Where
            elseif ($ppw_bulk_action_target_type == 'where' && $ppw_bulk_action_target_where_type) {
                // Where - By Category
                if ($ppw_bulk_action_target_where_type == 'category' && $ppw_bulk_action_target_where_category) {
                    $where_args['product_cat'] = $ppw_bulk_action_target_where_category;
                    $products = new WP_Query($where_args);

                } // Where - By Product type
                elseif ($ppw_bulk_action_target_where_type == 'product_type' && $ppw_bulk_action_target_where_product_type) {
                    $where_args['product_type'] = $ppw_bulk_action_target_where_product_type;
                    $products = new WP_Query($where_args);

                } // Where - By Price - greater than
                elseif ($ppw_bulk_action_target_where_type == 'price_greater') {
                    array_push($where_args['meta_query'],
                        array(
                            'key' => '_price',
                            'value' => str_replace(",", "", number_format($ppw_bulk_action_target_where_price_value, 2, '.', '')),
                            'compare' => '>',
                            'type' => 'DECIMAL(10,2)'
                        )
                    );
                    $products = new WP_Query($where_args);

                } // Where - By Price - less than
                elseif ($ppw_bulk_action_target_where_type == 'price_less') {
                    array_push($where_args['meta_query'],
                        array(
                            'key' => '_price',
                            'value' => str_replace(",", "", number_format($ppw_bulk_action_target_where_price_value, 2, '.', '')),
                            'compare' => '<',
                            'type' => 'DECIMAL(10,2)'
                        )
                    );
                    $products = new WP_Query($where_args);
                }

            } else {
                $errors = TRUE;
            }

            // Update posts
            if (!$errors && $products) {
                if (count($products->posts) < 1) {
                    $errors = TRUE;
                    $update_count = 'zero';
                    $redirect_url = admin_url('admin.php?page=woocommerce-price-per-word-option&tab=tools&processed=' . $update_count);
                    echo $redirect_url;
                } else {
                    foreach ($products->posts as $target) {
                        $target_product_id = ($target->post_parent != '0') ? $target->post_parent : $target->ID;
                        if (get_post_type($target_product_id) == 'product' && !in_array($target_product_id, $processed_product_id)) {
                            if (!update_post_meta($target_product_id, $ppw_meta_key_value, $ppw_bulk_action_type_status)) {
                                switch ($ppw_bulk_action_type) {
                                    case 'enable_price_per_words':
                                        update_post_meta($target_product_id, '_price_per_word_character', 'word');
                                        break;
                                    case 'enable_price_per_characters':
                                        update_post_meta($target_product_id, '_price_per_word_character', 'character');
                                        break;
                                    default:
                                        update_post_meta($target_product_id, '_price_per_word_character', 'word');
                                        break;
                                }

                            } else {
                                $processed_product_id[$target_product_id] = $target_product_id;
                            }
                        }
                    }
                    $update_count = count($processed_product_id);
                }
            }

            // return
            if (!$errors) {
                if ($update_count == 0) {
                    $update_count = 'zero';
                }
                $redirect_url = admin_url('admin.php?page=woocommerce-price-per-word-option&tab=tools&processed=' . $update_count);
                echo $redirect_url;
            }
            die(); // this is required to return a proper result
        }
    }
    
    function filter_woocommerce_display_item_meta( $html, $item, $args ) {  
        $product_id = $item->get_product_id();
        $enable = get_post_meta($product_id, '_price_per_word_character_enable', true);
        if (!empty($enable) && $enable == "yes") {
            $hide_qty = get_option('aewcppw_hide_qty_field');                      
            if($hide_qty =='yes'){
                return '';
            }
            else{
                return $html;
            }
        } else {
            return $html;
        }
        return $html;
    }

}
