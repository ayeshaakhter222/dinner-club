<?php

namespace WpCafe\Core\Shortcodes;

defined("ABSPATH") || exit;

use WpCafe\Traits\Wpc_Singleton;
use WpCafe\Utils\Wpc_Utilities;

/**
 * create post type class
 */
class Hook{

    use Wpc_Singleton;

    private $settings_obj = null;
    public $wpc_message   = '';
    public $wpc_cart_css  = '';
    /**
     * call hooks
     */
    public function init(){
        $settings = $this->settings_obj =  \WpCafe\Core\Base\Wpc_Settings_Field::instance()->get_settings_option();

        $shortcode_arr  = array(
            'wpc_food_menu_tab'     => 'wpc_food_menu_tab',
            'wpc_food_menu_list'    => 'wpc_food_menu_list',
            'wpc_reservation_form'  => 'reservation_shortcode',
            'wpc_food_location_menu'=> 'food_location_menu',
        );

        // add shortcode
        if( ! empty( $shortcode_arr ) ){
            foreach( $shortcode_arr as $key => $value ){
                add_shortcode($key, [$this, $value]);
            }
        }

        // add minicart to header
        add_action('wp_head', [$this, 'wpc_custom_inline_css']);
        add_action('wp_footer', [$this, 'wpc_custom_mini_cart']);

        // add new field in checkout page
        if ( isset($settings['wpcafe_food_location']) && $settings['wpcafe_food_location'] == 'on' && class_exists('WooCommerce')) {
            add_action('woocommerce_checkout_before_customer_details', [$this, 'wpc_location_checkout_form'], 10);
            add_action('woocommerce_checkout_process', [$this, 'wpc_validate_location'], 10);
            add_action('woocommerce_checkout_create_order', [$this, 'wpc_location_update_meta'], 10, 1);
        }

        // menu order action
        add_action('product_cat_add_form_fields', [$this, 'wpc_product_cat_taxonomy_add_new_meta_field'], 10, 1);
        add_action('product_cat_edit_form_fields', [$this, 'wpc_product_cat_taxonomy_edit_meta_field'], 10, 1);
        add_action('edited_product_cat', [$this, 'wpc_product_cat_taxonomy_save_meta_field'], 10, 1);
        add_action('create_product_cat', [$this, 'wpc_product_cat_taxonomy_save_meta_field'], 10, 1);

        //Displaying Additional Columns
        add_filter('manage_edit-product_cat_columns', [$this, 'wpc_custom_fields_list_title']);
        add_action('manage_product_cat_custom_column', [$this, 'wpc_custom_fields_list_diplay'], 10, 3);

        if (class_exists('woocommerce')) {
            // update cart counter 
            add_filter('woocommerce_add_to_cart_fragments', [$this, 'wpc_add_to_cart_count_fragment_refresh'], 30, 1);
            add_filter('woocommerce_add_to_cart_fragments', [$this, 'wpc_add_to_cart_content_fragment_refresh']);
        }

    }

    /**
     * Create a shortcode to render the reservation form.
     * Print the reservation form's HTML code.
     */
    public function reservation_shortcode($atts){
        ob_start();

        $settings = $this->settings_obj;
        // get pro feature values 
        $result_data = apply_filters('wpcafe/action/reservation_template', $atts );
        
        $from_field_label = "From"; $to_field_label = "To"; $show_form_field = "on"; $show_to_field = "on";
        $from_to_column = "wpc-col-md-6"; $required_from_field = 'on'; $required_to_field = 'on';$view = 'yes'; 
        $column_lg = 'wpc-col-lg-6';$column_md = 'wpc-col-md-12'; $booking_button_text = esc_html__("Confirm Booking","wpcafe"); $cancell_button_text = esc_html__("Request Cancellation" ,"wpcafe");
        
        if ( is_array($result_data) ) {
            if ( isset( $result_data['calender_view']) ) {
                $view      = $result_data['calender_view'];
                $column_lg = isset($result_data['column_lg']) ? $result_data['column_lg'] : 'wpc-col-lg-6';
                $column_md = isset($result_data['column_md']) ? $result_data['column_md'] : 'wpc-col-md-12';
            }
            if(isset( $result_data['from_field_label'] ) && isset( $result_data['to_field_label'] )  ) {
                $from_field_label   =  $result_data['from_field_label'];
                $to_field_label     =  $result_data['to_field_label'];
                $show_form_field    =  $result_data['show_form_field'];
                $show_to_field      =  $result_data['show_to_field'];
                $required_from_field=  $result_data['required_from_field'];
                $required_to_field  =  $result_data['required_to_field'];

                if(!( $show_form_field =='on' && $show_to_field =='on' ) ){
                    $from_to_column = "wpc-col-md-12";
                }

                $booking_button_text = $result_data['form_booking_button'];
                $cancell_button_text = $result_data['form_cancell_button'];
            }
        }

        $seat_capacity  = isset( $result_data['seat_capacity'] ) ? $result_data['seat_capacity'] : 20;
        $booking_status = isset( $result_data['booking_status'] ) ? $result_data['booking_status']: '';

        $reservation_form_template = \Wpcafe::plugin_dir() . "core/shortcodes/views/reservation/reservation-form-template.php";
        $cancellation_form_template = \Wpcafe::plugin_dir() . "core/shortcodes/views/reservation/cancellation-form-template.php";
        
        // All form settings for reservation
        if ( file_exists( \Wpcafe::plugin_dir() . "core/shortcodes/views/reservation/form-settings.php" ) ) {
            include_once \Wpcafe::plugin_dir() . "core/shortcodes/views/reservation/form-settings.php";
        }

        ?>
        <div class="reservation_section">
            <?php

            if( file_exists( $reservation_form_template ) ){
                include $reservation_form_template;
            }

            if ( !empty( $settings['wpc_allow_cancellation'] ) && $settings['wpc_allow_cancellation'] !=="off" && file_exists( $cancellation_form_template )) {
                include $cancellation_form_template;
            }

            ?>
        </div>
        <?php


        return ob_get_clean();
    }

    /**
     * Food menu shortcode
     */
    public function wpc_food_menu_tab($atts){
        if (!class_exists('Woocommerce')) { return; }
        $settings = array();
        $atts     = Wpc_Utilities::replace_qoute( $atts );

        $atts = extract(shortcode_atts([
            'style'                 => 'style-1',
            'wpc_food_categories'   => '',
            'no_of_product'         => 5,
            'wpc_desc_limit'        => 20,
            'wpc_menu_order'        => 'DESC',
            'wpc_show_desc'         => 'yes',
            'title_link_show'       => 'yes',
            'show_item_status'      => 'yes',
            'product_thumbnail'     => 'yes',
            'wpc_cart_button'       => 'yes',
        ], $atts));

        ob_start();
        $wpc_cat_arr  = explode(',', $wpc_food_categories);
        if (!empty($wpc_cat_arr)) {
            $food_menu_tabs = Wpc_Utilities::get_tab_array_from_category($wpc_cat_arr);
            
            // sort category list
            if ( !empty($food_menu_tabs) ) {
                ksort($food_menu_tabs);
            }
            
            $unique_id = md5(md5(microtime()));
            $settings["food_menu_tabs"]         = $food_menu_tabs;
            $settings["food_tab_menu_style"]    = $style;
            $settings["show_thumbnail"]         = $product_thumbnail;
            $settings["wpc_menu_order"]         = $wpc_menu_order;
            $settings["show_item_status"]       = $show_item_status;
            $settings["wpc_menu_count"]         = $no_of_product;
            $settings["wpc_show_desc"]          = $wpc_show_desc;
            $settings["wpc_desc_limit"]         = $wpc_desc_limit;
            $settings["title_link_show"]        = $title_link_show;
            $settings["wpc_cart_button"]        = $wpc_cart_button;
            // render template
            $template = \Wpcafe::core_dir() ."shortcodes/views/food-menu/food-tab.php";
            if( file_exists( $template ) ){
                include $template;
            }
        }
        
        return ob_get_clean();
    }

    /**
     * Food menu list block
     */
    public function wpc_food_menu_list($atts){

        if (!class_exists('Woocommerce')) { return; }

        $atts    = Wpc_Utilities::replace_qoute( $atts );
        $atts    = extract(shortcode_atts(
            [
                'style'                 => 'style-1',
                'wpc_food_categories'   => '',
                'no_of_product'         => 5,
                'wpc_cart_button'       => 'yes',
                'product_thumbnail'     => 'yes',
                'show_item_status'      => 'yes',
                'wpc_show_desc'         => 'yes',
                'title_link_show'       => 'yes',
                'wpc_desc_limit'        => 20,
                'wpc_menu_order'        => 'DESC',
                'wpc_menu_col'          => '4',
                'wpc_menu_col_tablet'   => '3',
                'wpc_menu_col_mobile'   => '2'
            ],
            $atts
        ));
        ob_start();
        // category sorting from backend
        $wpc_cat_arr      = explode(',', $wpc_food_categories);

        $wpc_menu_col           = 4;
        $wpc_menu_col_tablet    = 3;
        $wpc_menu_col_mobile    = 2;

        if (is_array($wpc_cat_arr) && count($wpc_cat_arr) > 0) {
            $unique_id = md5(md5(microtime()));
            $settings = array();
            $settings["food_menu_style"]        = $style;
            $settings["show_thumbnail"]         = $product_thumbnail;
            $settings["wpc_cart_button_show"]   = $wpc_cart_button;
            $settings["show_item_status"]       = $show_item_status;
            $settings["title_link_show"]        = $title_link_show;
            $settings["wpc_show_desc"]          = $wpc_show_desc;
            $settings["wpc_desc_limit"]         = 20;
            $settings["wpc_menu_cat"]           = $wpc_cat_arr;
            $settings["wpc_menu_count"]         = $no_of_product;
            $settings["wpc_menu_order"]         = $wpc_menu_order;

            $settings['wpc_menu_col']           = $wpc_menu_col;
            $settings['wpc_menu_col_tablet']    = $wpc_menu_col_tablet;
            $settings['wpc_menu_col_mobile']    = $wpc_menu_col_mobile;

            // render template
            $template = \Wpcafe::core_dir() ."shortcodes/views/food-menu/food-list.php";
            if( file_exists( $template ) ){
                include $template;
            }
        }
        return ob_get_clean();
    }

    /**
     * Mini cart for frontend
     *
     */
    public function wpc_custom_mini_cart(){
        if (!class_exists('WooCommerce')) {  return; }
        // show location
        if (is_front_page()) {
            Wpc_Utilities::get_location_details();
        }
        $settings       = $this->settings_obj;
        if ( !isset($settings['wpcafe_allow_cart']) || ( isset($settings['wpcafe_allow_cart'])
            && $settings['wpcafe_allow_cart'] == 'on' ) ) {
            $wpc_cart_icon  = !empty( $settings['wpc_mini_cart_icon'] ) ? $settings['wpc_mini_cart_icon'] : 'wpcafe-cart_icon';
            
            $custom_mini_cart = \Wpcafe::core_dir() ."shortcodes/views/mini-cart/custom-mini-cart.php";
            
            if( file_exists($custom_mini_cart) ){
                include_once $custom_mini_cart;
            }
        }

        ?>
        <!-- After add to cart  message  -->
        <script type="text/javascript">
            jQuery( function($){
                var get_reserv_detials = localStorage.getItem('wpc_reservation_details');
                $('body').on('added_to_cart', function(event, fragments, cart_hash, button){
                    $('.wpc-cart-message').fadeIn().delay(3000).fadeOut();
                    // pass data to show in reservation details
                    if ( typeof food_details_reservation !=="undefiend" &&
                    typeof get_reserv_detials !=="undefined" && get_reserv_detials !== null && typeof button !=="undefined"
                    ) {

                        var product_id    = button.data('product_id'),   // Get the product id
                            product_name  = button.data('product_name'), // Get the product name
                            product_price = button.data('product_price'); // Get the product price

                        food_details_reservation({product_id:product_id,product_name:product_name,product_price:product_price ,
                         } , $  )
                    }
                });

            });
        </script>

        <?php
    }

    /**
     * Cart count  function
     */
    public function wpc_add_to_cart_count_fragment_refresh($fragments){
        ob_start();
        ?>
        <div id="wpc-mini-cart-count">
            <?php echo WC()->cart->get_cart_contents_count(); ?>
        </div>
        <?php
        $fragments['#wpc-mini-cart-count'] = ob_get_clean();
        return $fragments;
    }

    /**
     * Cart count  function
     */
    public function wpc_add_to_cart_content_fragment_refresh($fragments){
        ob_start();
        ?>
        <div class="widget_shopping_cart_content">
            <?php
            is_object(WC()->cart) ? woocommerce_mini_cart() : '';
            ?>
        </div>
        <?php
        $fragments['div.widget_shopping_cart_content'] = ob_get_clean();
        return $fragments;
    }

    /**
     * Location field in checkout form
     */
    public function wpc_location_checkout_form(){
        $checkout = WC()->checkout;
        ?>
        <div id="wpc_location_field">
            <?php
            // get location
            $wpc_location_arr = Wpc_Utilities::get_location_data();
            woocommerce_form_field('wpc_location_name', [
                'type'        => 'select',
                'class'       => ['wpc-location form-row-wide'],
                'label'       => esc_html__('Order location', 'wpcafe'),
                'placeholder' => esc_html__('Enter location', 'wpcafe'),
                'required'    => true,
                'options'     => $wpc_location_arr,
            ], $checkout->get_value('wpc_location_name'));
            ?>
        </div>
        <?php
    }

    /**
     * Valid location select option
     *
     */
    public function wpc_validate_location(){
        if(sanitize_text_field(isset($_POST['wpc_location_name'])) && empty(sanitize_text_field($_POST['wpc_location_name']))) {
            wc_add_notice(esc_html__('Please select delivery location', 'wpcafe'), 'error');
        }
    }

    /**
     * Update location select option
     *
     * @param [type] $order
     */
    public function wpc_location_update_meta($order){

        if (sanitize_text_field(isset($_POST['wpc_location_name'])) && !empty(sanitize_text_field($_POST['wpc_location_name']))) {
            $order->update_meta_data('wpc_location_name', sanitize_text_field($_POST['wpc_location_name']));
        }
    }

    /**
     * Category new field for set priority
     */
    public function wpc_product_cat_taxonomy_add_new_meta_field(){
    ?>
        <div class="form-field">
            <label for="wpc_menu_order_priority"><?php esc_html_e('Order menu', 'wpcafe'); ?></label>
            <input type="text" name="wpc_menu_order_priority" id="wpc_menu_order_priority">
        </div>
    <?php
    }

    /**
     * Category edit field for set priority
     */
    public function wpc_product_cat_taxonomy_edit_meta_field($term){
        //getting term ID
        $term_id                 = $term->term_id;
        $wpc_menu_order_priority = get_term_meta($term_id, 'wpc_menu_order_priority', true);
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="wpc_menu_order_priority"><?php esc_html_e('Order menu', 'wpcafe'); ?></label></th>
            <td>
                <input type="text" name="wpc_menu_order_priority" id="wpc_menu_order_priority" value="<?php echo esc_attr($wpc_menu_order_priority) ? esc_attr($wpc_menu_order_priority) : ''; ?>">
            </td>
        </tr>
        <?php
    }

    /**
     * Category save field for set priority
     */
    public function wpc_product_cat_taxonomy_save_meta_field($term_id){
        $wpc_menu_order_priority = filter_input(INPUT_POST, 'wpc_menu_order_priority');
        update_term_meta($term_id, 'wpc_menu_order_priority', $wpc_menu_order_priority);
    }

    /**
     * Order menu column added to category admin screen.
     */
    public function wpc_custom_fields_list_title($columns){
        $columns['wpc_menu_order_priority'] = esc_html__('Order menu', 'wpcafe');
        $columns['cat_id']                  = esc_html__('ID', 'wpcafe');
        return $columns;
    }

    /**
     * Order menu column value added to product category admin screen.
     */
    public function wpc_custom_fields_list_diplay($columns, $column, $id){
        if ('wpc_menu_order_priority' == $column) {
            $columns = esc_html(get_term_meta($id, 'wpc_menu_order_priority', true));
        } elseif ('cat_id' == $column) {
            $columns = esc_html($id);
        }

        return $columns;
    }

    /**
     * Custom inline css
     */
    public function wpc_custom_inline_css(){
        if (!class_exists('WooCommerce')) {
            return;
        }
        $settings       = $this->settings_obj;
        $template       = \Wpcafe::core_dir() . "shortcodes/views/mini-cart/mini-cart.php";

        if( file_exists( $template ) ){
            include_once $template;
        }
    }

    /**
     * Food by location
     */
    public function food_location_menu( $atts ){
        if (!class_exists('Woocommerce')) {
            return;
        }

        ob_start();

        $unique_id = md5(md5(microtime()));
        $product_data               = $atts;
        $product_data['unique_id']  = $unique_id ;
        $product_data['wpc_menu_col']  = 'wpc-col-md-8' ;
        
        // shortcode option
        $atts = extract(shortcode_atts(
            [
                'wpc_food_categories'   => '',
                'style'                 => 'style-1',
                'no_of_product'         => 5,
                'show_thumbnail'        => "yes",
                'wpc_cart_button'       => 'yes',
                'title_link_show'       => 'yes',
                'wpc_menu_col'          => '6',
                'wpc_show_desc'         => 'yes',
                'wpc_desc_limit'        => '15',
                'live_search'           => 'yes',
                'wpc_delivery_time_show'=> 'yes',
                'show_item_status'      => 'yes',
                'wpc_menu_order'        => 'DESC',
                'wpc_nav_position'      => 'top',
                'location_alignment '   => 'center'
            ], $atts ));

        $location_alignment = "center";

        $products = wc_get_products([]);
        
        if ( file_exists( \WpCafe::plugin_dir() . "core/shortcodes/views/food-menu/location-menu.php" ) ) {
            ?>
            <div class="location_menu" data-product_data ="<?php esc_attr_e( json_encode( $product_data  ));?>">
                <?php include \WpCafe::plugin_dir() . "core/shortcodes/views/food-menu/location-menu.php"; ?>
            </div>
            <?php
        }

        return ob_get_clean();
    }
}
