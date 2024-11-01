<?php

/**
 * @class       Woocommerce_Price_Per_Word_Setting
 * @version    1.0
 * @package    woocommerce-price-per-word
 * @category    Class
 * @author      Angell EYE <service@angelleye.com>
 */
class Woocommerce_Price_Per_Word_Setting {

    /**
     * Hook in methods
     * @since    1.0.0
     * @access   static
     */
    public static function init() {

        add_action('woocommerce_price_per_word_general_setting', array(__CLASS__, 'woocommerce_price_per_word_general_setting'));
        add_action('woocommerce_price_per_word_general_setting_save_field', array(__CLASS__, 'woocommerce_price_per_word_general_setting_save_field'));

        add_action('woocommerce_price_per_word_tools_setting', array(__CLASS__, 'woocommerce_price_per_word_tools_setting'));
    }

    public static function woocommerce_price_per_word_general_setting_fields() {

        $fields[] = array('title' => __('WooCommerce Price Per Word Settings', 'woocommerce-price-per-word'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');

        $fields[] = array(
            'title' => __('QTY Accessibility', 'woocommerce-price-per-word'),
            'desc' => __('Allow buyers to enter a QTY instead of forcing a document upload.', 'woocommerce-price-per-word'),
            'id' => 'aewcppw_allow_users_to_enter_qty',
            'default' => 'no',
            'type' => 'checkbox',
        );

        $fields[] = array(
            'title' => __('Minimum Price', 'woocommerce-price-per-word'),
            'desc' => __('Set a global minimum price so that if a document does not have enough words / characters, the minimum will still be charged.  This can also be set at the product level, which would override this global setting.', 'woocommerce-price-per-word'),
            'id' => '_minimum_product_price',
            'type' => 'text',
        );

        $fields[] = array(
            'title' => __('Product Page Message', 'woocommerce-price-per-word'),
            'desc' => __('', 'woocommerce-price-per-word'),
            'id' => 'aewcppw_product_page_message',
            'type' => 'textarea',
            'css' => 'min-width:300px;',
            'default' => __('Please upload your .doc, .docx, .pdf , .txt .po , .pot to get a price.','woocommerce-price-per-word')
        );
        
        $fields[] = array(
            'title' => __('Hide Word/Charcter count', 'woocommerce-price-per-word'),
            'desc' => __('Hide Word/Charcter count.', 'woocommerce-price-per-word'),
            'id' => 'aewcppw_hide_qty_field',
            'type' => 'checkbox'            
        );
        
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');

        return $fields;
    }

    public static function woocommerce_price_per_word_general_setting() {
        $wppw_setting_fields = self::woocommerce_price_per_word_general_setting_fields();
        $Html_output = new Woocommerce_Price_Per_Word_Html_output();
        ?>
        <form id="woocommerce_price_per_word_form" enctype="multipart/form-data" action="" method="post">
            <?php $Html_output->init($wppw_setting_fields); ?>
            <p class="submit">
                <input type="submit" name="woocommerce_price_per_word_integration" class="button-primary"
                       value="<?php esc_attr_e('Save changes', 'Option'); ?>"/>
            </p>
        </form>
        <?php
    }


    public static function woocommerce_price_per_word_general_setting_save_field() {
        $wppw_setting_fields = self::woocommerce_price_per_word_general_setting_fields();
        $Html_output = new Woocommerce_Price_Per_Word_Html_output();
        $Html_output->save_fields($wppw_setting_fields);
    }

    public static function woocommerce_price_per_word_tools_setting() {
        // WooCommerce product categories
        $taxonomy = 'product_cat';
        $orderby = 'name';
        $show_count = 0;      // 1 for yes, 0 for no
        $pad_counts = 0;      // 1 for yes, 0 for no
        $hierarchical = 1;      // 1 for yes, 0 for no
        $title = '';
        $empty = 0;

        $args = array(
            'taxonomy' => $taxonomy,
            'orderby' => $orderby,
            'show_count' => $show_count,
            'pad_counts' => $pad_counts,
            'hierarchical' => $hierarchical,
            'title_li' => $title,
            'hide_empty' => $empty
        );
        $product_cats = get_categories($args);

        // Tools - Bulk enable/disable offers
        $processed = (isset($_GET['processed'])) ? wc_clean($_GET['processed']) : FALSE;
        if ($processed) {
            if ($processed == 'zero') {
                echo '<div class="updated">';
                echo '<p>' . sprintf(__('Action completed; %s records processed.', 'woocommerce-price-per-word'), '0');
                echo '</div>';
            } else {
                echo '<div class="updated">';
                echo '<p>' . sprintf(__('Action completed; %s records processed. ', 'woocommerce-price-per-word'), $processed);
                echo '</div>';
            }
        }
        ?>

        <div class="ppw_wrap_tools">
            <form id="ppw_tool_enable_price_per_words_characters" autocomplete="off"
                  action="<?php echo admin_url('admin.php?page=woocommerce-price-per-word-option&tab=tools'); ?>"
                  method="post">
                <div class="ppw-enable-price-per-words-characters">
                    <h3><?php echo __('Bulk Edit Tool for Enable or Disable the Price per Words/Characters', 'woocommerce-price-per-word'); ?></h3>
                    <div><?php echo __('Select from the options below to enable or disable Price per Words/Characters.', 'woocommerce-price-per-word'); ?></div>

                    <div class="ppw-tool-bulk-action-section ppw-bulk-tool-action-type">
                        <label
                            for="ppw-bulk-tool-action-type"><?php echo __('Action', 'woocommerce-price-per-word'); ?></label>
                        <div>
                            <select name="ppw_bulk_tool_action_type" id="ppw-bulk-tool-action-type" required="required">
                                <option
                                    value=""><?php echo __('- Select option', 'woocommerce-price-per-word'); ?></option>
                                <option
                                    value="enable_price_per_words"><?php echo __('Enable Price per Word', 'woocommerce-price-per-word'); ?></option>
                                <option
                                    value="enable_price_per_characters"><?php echo __('Enable Price per Character', 'woocommerce-price-per-word'); ?></option>
                                <option
                                    value="disable_price_per_words_chacacters"><?php echo __('Disable Price per Word / Character', 'woocommerce-price-per-word'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="ppw-tool-bulk-action-section ppw-bulk-tool-action-target-type">
                        <label
                            for="ppw-bulk-tool-action-target-type"><?php echo __('Target', 'woocommerce-price-per-word'); ?></label>
                        <div>
                            <select name="ppw_bulk_tool_action_target-type" id="ppw-bulk-tool-action-target-type"
                                    required="required">
                                <option
                                    value=""><?php echo __('- Select option', 'woocommerce-price-per-word'); ?></option>
                                <option
                                    value="all"><?php echo __('All products', 'woocommerce-price-per-word'); ?></option>
                                <option
                                    value="featured"><?php echo __('Featured products', 'woocommerce-price-per-word'); ?></option>
                                <option
                                    value="where"><?php echo __('Where...', 'woocommerce-price-per-word'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div
                        class="ppw-tool-bulk-action-section ppw-bulk-tool-action-target-where-type angelleye-hidden">
                        <label
                            for="ppw-bulk-tool-action-target-where-type"><?php echo __('Where', 'woocommerce-price-per-word'); ?></label>
                        <div>
                            <select name="ppw_bulk_tool_action_target_where_type"
                                    id="ppw-bulk-tool-action-target-where-type">
                                <option
                                    value=""><?php echo __('- Select option', 'woocommerce-price-per-word'); ?></option>
                                <option
                                    value="category"><?php echo __('Category...', 'woocommerce-price-per-word'); ?></option>
                                <option
                                    value="product_type"><?php echo __('Product type...', 'woocommerce-price-per-word'); ?></option>
                                <option
                                    value="price_greater"><?php echo __('Price greater than...', 'woocommerce-price-per-word'); ?></option>
                                <option
                                    value="price_less"><?php echo __('Price less than...', 'woocommerce-price-per-word'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div
                        class="ppw-tool-bulk-action-section ppw-bulk-tool-target-where-category angelleye-hidden">
                        <label
                            for="ppw-bulk-tool-target-where-category"><?php echo __('Category', 'woocommerce-price-per-word'); ?></label>
                        <div>
                            <select name="ppw_bulk_tool_target_where_category" id="ppw-bulk-tool-target-where-category">
                                <option
                                    value=""><?php echo __('- Select option', 'woocommerce-price-per-word'); ?></option>
                                <?php
                                if ($product_cats) {
                                    foreach ($product_cats as $cat) {
                                        echo '<option value="' . $cat->slug . '">' . $cat->cat_name . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div
                        class="ppw-tool-bulk-action-section ppw-bulk-tool-target-where-product-type angelleye-hidden">
                        <label for="ppw-bulk-tool-target-where-product-type">Product type</label>
                        <div>
                            <select name="ppw_bulk_tool_target_where_product_type"
                                    id="ppw-bulk-tool-target-where-product-type">
                                <option
                                    value=""><?php echo __('- Select option', 'woocommerce-price-per-word'); ?></option>
                                <option
                                    value="simple"><?php echo __('Simple', 'woocommerce-price-per-word'); ?></option>
                                <option
                                    value="variable"><?php echo __('Variable', 'woocommerce-price-per-word'); ?></option>
                                <option
                                    value="grouped"><?php echo __('Grouped', 'woocommerce-price-per-word'); ?></option>
                                <option
                                    value="external"><?php echo __('External', 'woocommerce-price-per-word'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div
                        class="ppw-tool-bulk-action-section ppw-bulk-tool-action-target-where-price-value angelleye-hidden">
                        <label for="ppw-bulk-tool-action-target-where-price-value"></label>
                        <div>
                            <input type="text" name="ppw_bulk_tool_action_target_where_price_value"
                                   id="ppw-bulk-tool-action-target-where-price-value">
                        </div>
                    </div>

                    <div class="ppw-tool-bulk-action-section">
                        <label for="bulk_enable_disable_price_per_word_character_tool_submit"></label>
                        <div>
                            <button class="button button-primary" id="ppw-tool-bulk-submit"
                                    name="ppw-tool-bulk_submit"><?php echo __('Process', 'woocommerce-price-per-word'); ?></button>
                        </div>
                    </div>
                    <div class="angelleye-offers-clearfix"></div>

                </div>
            </form>
        </div>
        <?php
    }

}

Woocommerce_Price_Per_Word_Setting::init();
