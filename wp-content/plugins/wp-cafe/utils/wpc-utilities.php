<?php

namespace WpCafe\Utils;

defined( 'ABSPATH' ) || exit;

class Wpc_Utilities {

    /**
     * Html markup validation
     */
    public static function wpc_kses( $raw ) {
        $allowed_tags = [
            'a'                             => [
                'class'  => [],
                'href'   => [],
                'rel'    => [],
                'title'  => [],
                'target' => [],
            ],
            'input'                         => [
                'value'       => [],
                'type'        => [],
                'size'        => [],
                'name'        => [],
                'checked'     => [],
                'placeholder' => [],
                'id'          => [],
                'class'       => [],
            ],

            'select'                        => [
                'value'       => [],
                'type'        => [],
                'size'        => [],
                'name'        => [],
                'placeholder' => [],
                'id'          => [],
                'class'       => [],
                'option'      => [
                    'value'   => [],
                    'checked' => [],
                ],
            ],

            'textarea'                      => [
                'value'       => [],
                'type'        => [],
                'size'        => [],
                'name'        => [],
                'rows'        => [],
                'cols'        => [],
                'placeholder' => [],
                'id'          => [],
                'class'       => [],
            ],
            'abbr'                          => [
                'title' => [],
            ],
            'b'                             => [],
            'blockquote'                    => [
                'cite' => [],
            ],
            'cite'                          => [
                'title' => [],
            ],
            'code'                          => [],
            'del'                           => [
                'datetime' => [],
                'title'    => [],
            ],
            'dd'                            => [],
            'div'                           => [
                'class' => [],
                'title' => [],
                'style' => [],
            ],
            'dl'                            => [],
            'dt'                            => [],
            'em'                            => [],
            'h1'                            => [
                'class' => [],
            ],
            'h2'                            => [
                'class' => [],
            ],
            'h3'                            => [
                'class' => [],
            ],
            'h4'                            => [
                'class' => [],
            ],
            'h5'                            => [
                'class' => [],
            ],
            'h6'                            => [
                'class' => [],
            ],
            'i'                             => [
                'class' => [],
            ],
            'img'                           => [
                'alt'    => [],
                'class'  => [],
                'height' => [],
                'src'    => [],
                'width'  => [],
            ],
            'li'                            => [
                'class' => [],
            ],
            'ol'                            => [
                'class' => [],
            ],
            'p'                             => [
                'class' => [],
            ],
            'q'                             => [
                'cite'  => [],
                'title' => [],
            ],
            'span'                          => [
                'class' => [],
                'title' => [],
                'style' => [],
            ],
            'small'                          => [
                'class' => [],
                'title' => [],
                'style' => [],
            ],
            'iframe'                        => [
                'width'       => [],
                'height'      => [],
                'scrolling'   => [],
                'frameborder' => [],
                'allow'       => [],
                'src'         => [],
            ],
            'strike'                        => [],
            'br'                            => [],
            'strong'                        => [],
            'data-wow-duration'             => [],
            'data-wow-delay'                => [],
            'data-wallpaper-options'        => [],
            'data-stellar-background-ratio' => [],
            'ul'                            => [
                'class' => [],
            ],
            'label'                         => [
                'class' => [],
                'for' => [],
            ],
        ];

        if ( function_exists( 'wp_kses' ) ) { // WP is here
            return wp_kses( $raw, $allowed_tags );
        } else {
            return $raw;
        }

    }

    /**
     * Auto generate class name from path.
     */
    public static function make_classname( $dirname ) {
        $dirname    = pathinfo( $dirname, PATHINFO_FILENAME );
        $class_name = explode( '-', $dirname );
        $class_name = array_map( 'ucfirst', $class_name );
        $class_name = implode( '_', $class_name );

        return $class_name;
    }

    public static function kspan( $text ) {
        return str_replace( ['{', '}'], ['<span>', '</span>'], self::wpc_kses( $text ) );
    }

    /**
     * Seat count min , max limit
     */
    static function get_seat_count_limit() {
        $seat_count_limit = [];
        try {
            $settings_obj     = new \WpCafe\Core\Base\Wpc_Settings_Field;
            $settings         = $settings_obj->get_settings_option();
            
            $get_seat_capacity= apply_filters('wpcafe/reservation/seat_capacity', $settings );
            $seat_capacity    = isset( $get_seat_capacity ) ? $get_seat_capacity : 20;
            $wpc_min_guest_no = isset( $settings['wpc_min_guest_no'] ) && $settings['wpc_min_guest_no']!=="" ? $settings['wpc_min_guest_no'] : 1;
            $wpc_max_guest_no = isset( $settings['wpc_max_guest_no'] ) && $settings['wpc_max_guest_no']!=="" ? $settings['wpc_max_guest_no'] : $seat_capacity;

            for ( $i = $wpc_min_guest_no; $i <= $wpc_max_guest_no; $i++ ) {
                $seat_count_limit[$i] = $i;
            }

            return $seat_count_limit;
        } catch ( \Exception $es ) {
            return [];
        }

    }

    /**
     * Reservation status array
     */
    public static function get_reservation_states() {
        $reservation_states              = [];
        $reservation_states['pending']   = "Pending";
        $reservation_states['confirmed'] = "Confirmed";
        $reservation_states['cancelled'] = "Cancelled";
        $reservation_states['completed'] = "Completed";
        
        return $reservation_states;
    }

    /**
     * Generate invoice no.
     */
    public static function generate_invoice_number( $post_id ) {
        $fourdigitrandom = rand( 1000, 9999 );
        $invoice_no      = "WPC" . $fourdigitrandom . $post_id;

        return $invoice_no;
    }

    /**
     * Email sending function
     * @param array $args = [$to, $subject, $mail_body, $from, $from_name]
     */
    public static function wpc_send_email( $args ) {
        extract( $args );
        $body = html_entity_decode($mail_body);
        $headers   = ['Content-Type: text/html; charset=UTF-8', 'From: ' . $from_name . ' <' . $from . '>'];
        $result = wp_mail( $to, $subject, $body, $headers );

        return $result;
    }

    /**
     * Show Notices
     */
    public static function push( $notice ) {

        $defaults = [
            'id'               => '',
            'type'             => 'info',
            'show_if'          => true,
            'message'          => '',
            'class'            => 'wpc-active-notice',
            'dismissible'      => false,
            'btn'              => [],
            'dismissible-meta' => 'user',
            'dismissible-time' => WEEK_IN_SECONDS,
            'data'             => '',
        ];

        $notice = wp_parse_args( $notice, $defaults );

        $classes = ['wpc-notice', 'notice'];

        $classes[] = $notice['class'];

        if ( isset( $notice['type'] ) ) {
            $classes[] = 'notice-' . $notice['type'];
        }

        // Is notice dismissible?
        if ( true === $notice['dismissible'] ) {
            $classes[] = 'is-dismissible';

            // Dismissable time.
            $notice['data'] = ' dismissible-time=' . esc_attr( $notice['dismissible-time'] ) . ' ';
        }

        // Notice ID.
        $notice_id    = 'wpc-sites-notice-id-' . $notice['id'];
        $notice['id'] = $notice_id;

        if ( !isset( $notice['id'] ) ) {
            $notice_id    = 'wpc-sites-notice-id-' . $notice['id'];
            $notice['id'] = $notice_id;
        } else {
            $notice_id = $notice['id'];
        }

        $notice['classes'] = implode( ' ', $classes );

        // User meta.
        $notice['data'] .= ' dismissible-meta=' . esc_attr( $notice['dismissible-meta'] ) . ' ';

        if ( 'user' === $notice['dismissible-meta'] ) {
            $expired = get_user_meta( get_current_user_id(), $notice_id, true );
        } elseif ( 'transient' === $notice['dismissible-meta'] ) {
            $expired = get_transient( $notice_id );
        }
        

        if("wpc-sites-notice-id-wpcafe-pro-notice" == $notice["id"]){
            self::pro_banner_markup( $notice );
        } else{
            // Notice visible after transient expire.
            if ( isset( $notice['show_if'] ) ) {
                if ( true === $notice['show_if'] ) {

                    // Is transient expired?
                    if ( false === $expired || empty( $expired ) ) {
                        self::markup( $notice );
                    }

                }

            } else {
                self::markup( $notice );
            }
        }

    }


    /**
     * Markup Notice.
     */
    public static function pro_banner_markup( $notice = [] ) {
        ?>
		<div id="<?php echo esc_attr( $notice['id'] ); ?>" class="wpc notice wpc-notice wpc-notice-buy-pro-banner is-dismissible" <?php echo self::wpc_render( $notice['data'] ); ?>>
             <?php if ( !empty( $notice['btn'] ) ) { ?>
					<a target="_blank" href="<?php echo esc_url( $notice['btn']['url'] ); ?>" class="notice-banner-link"></a>
            <?php } ?>
		</div>
		<?php
	}


    /**
     * Markup Notice.
     */
    public static function markup( $notice = [] ) {
        ?>
		<div id="<?php echo esc_attr( $notice['id'] ); ?>" class="<?php echo esc_attr( $notice['classes'] ); ?>" <?php echo Wpc_Utilities::wpc_render( $notice['data'] ); ?>>
			<p>
				<?php echo Wpc_Utilities::wpc_kses( $notice['message'] ); ?>
			</p>

			<?php if ( !empty( $notice['btn'] ) ): ?>
				<p>
					<a href="<?php echo esc_url( $notice['btn']['url'] ); ?>" class="button-primary"><?php echo esc_html( $notice['btn']['label'] ); ?></a>
				</p>
			<?php endif;?>
		</div>
        <?php
    }

    /**
     * Render html.
     */
    public static function wpc_render( $content ) {
        if ( $content == "" ) {
            return "";
        }

        return $content;
    }

    /**
     * Render numeric value.
     */
    public static function wpc_numeric( $content ) {
        if ( is_numeric( $content ) ) {
            return $content;
        }

        return $content;
    }

    /**
     *  Check nonce validation of submit data
     */
    public static function is_secured( $nonce_field, $action, $post ) {
        $nonce = isset( $post[$nonce_field] ) ? sanitize_text_field( $post[$nonce_field] ) : '';
        if ( $nonce == '' ) {
            return false;
        }

        if ( !wp_verify_nonce( $nonce, $action ) ) {
            return false;
        }

        return true;
    }

    /**
     * Menu category
     */
    public static function get_menu_category( $id = null ) {
        $menu_category = [];
        try {

            if ( is_null( $id ) ) {
                $terms = get_terms( [
                    'taxonomy'   => 'product_cat',
                    'hide_empty' => false,
                ] );

                foreach ( $terms as $cat ) {
                    $menu_category[$cat->term_id] = $cat->name;
                }

                return $menu_category;
            } else {
                // return single menu
                return get_post( $id );
            }

        } catch ( \Exception $es ) {
            return [];
        }

    }

    /**
     * content crop function
     */
    public static function wpcafe_trim_words( $content, $count = 150, $readmore = null ) {
        return wp_trim_words( $content, $count, $readmore );
    }

    public static function get_location_data( $default_options="Select delivery location", $no_options="No delivery location is set" , $value_type = "key" ) {
        // get location
        $wpc_location     = get_terms('wpcafe_location', ['taxonomy' => 'wpcafe_location', 'hide_empty' => 0, 'orderby' => 'DESC', 'parent' => 0]);
        $wpc_location_arr = ['' => esc_html__( $default_options , 'wpcafe')];

        if ( !empty($wpc_location) ) {
            foreach ($wpc_location as $value) {
                if ( $value_type =="key" ) {
                    $wpc_location_arr["$value->slug"] = esc_html__( $value->name , 'wpcafe' );
                }
                else if ( $value_type =="id" ) {
                    $wpc_location_arr["$value->term_id"] = esc_html__( $value->name , 'wpcafe' );
                }
                else{
                    $wpc_location_arr["$value->name"] = esc_html__( $value->name , 'wpcafe' ); 
                }
            }
        }

        return $wpc_location_arr;
    }
    
    /**
     * Location feature function
     */
    public static function get_location_details(){
        $settings =  \WpCafe\Core\Base\Wpc_Settings_Field::instance()->get_settings_option();
        // show location
        if (isset($settings['wpcafe_food_location']) && $settings['wpcafe_food_location'] == 'on') {
            $wpc_locations = Wpc_Utilities::get_location_data();
            
            \WpCafe\Core\Shortcodes\Template_Functions::modal_markup( $wpc_locations );
        }
    }

    /**
     * Show tag function
     */
    public static function wpc_tag($id , $stock_status ){
        $current_tags = get_the_terms($id, 'product_tag');
        //create a list to hold our tags
        ?>
        <ul class="wpc-menu-tag">
            <?php
            if ($stock_status == true || ( $current_tags && !is_wp_error($current_tags) ) ) {
                //for each tag we create a list item
                if ( is_array( $current_tags ) && count( $current_tags )>0 ) {
                    foreach ($current_tags as $tag) {
                        $tag_title = $tag->name;
                        ?>
                        <li>
                            <?php echo esc_html__($tag_title,'wpcafe'); ?>
                        </li>
                        <?php
                    }
                }
            }
            else{
                ?><li><?php echo esc_html__('Out of stock','wpcafe'); ?></li><?php 
            }
            ?>
        </ul>
        <?php 
    }

    /**
     * Product query
     * @param array $args = [ $post_type, $no_of_product, $wpc_cat, $order, $page, $total_count, $search_value, $taxonomy ]
     */
    public static function product_query( $params ){
        $defaults = array(
            'post_type'     => 'product',
            'no_of_product' => 10,
            'wpc_cat'       => array(),
            'order'         => 'DESC',
            'page'          => null,
            'total_count'   => false,
            'search_value'  => false,
            'taxonomy'      => 'product_cat'
        );

        extract( wp_parse_args( $params , $defaults ) );

        $args = [];
        $args['post_type']      = $post_type;
        if ( $total_count ) {
            $args    = [ 'posts_per_page' =>  -1 ];
        }
        elseif( $search_value ){
            $args['posts_per_page']  = $no_of_product; 
            $args['post_title_like'] = $search_value; 
        }
        elseif( $page ){
            $args    = [
                'posts_per_page' =>  $no_of_product,
                'paged'          =>  $page,
            ];
        }
        else{
            $args    = [ 'posts_per_page' =>  $no_of_product ];
        }

        $args['tax_query'] = array(
            'relation' => 'AND',
        );

        
        if( is_array( $wpc_cat ) && count( $wpc_cat )>0 ){
            $tax_query = array(
            array(
                'taxonomy'          => $taxonomy,
                'terms'             =>  $wpc_cat,
                'field'             => 'id',
                'include_children'  => true,
                'operator'          => 'IN'
            ),
        );
            $args['tax_query'] = $tax_query;
        }

        $args['orderby']        = 'date';
        $args['order']          = $order;
        $args['post_status']    = 'publish';
        
        return wc_get_products($args);
    }

    /**
     * Add to cart button based on product type
     * @param array $args = [ $product, $cart_button, $wpc_btn_text='', $customize_btn= '', $widget_id='']
     */
    public static function product_add_to_cart( $args){

        extract( $args );

        switch ( $product->get_type() ) {
            case $product->get_type() == 'variable' || $product->get_type() == 'grouped' && $product->is_in_stock() == true :
                if( $cart_button=='on' || $cart_button =='yes' ){
                    apply_filters("wpcafe/shortcode/variation", $product, $customize_btn, $widget_id);
                }
                break;
            case ($product->get_type() == 'simple' ) && 
                ($cart_button == 'on' || $cart_button == 'yes' ) && 
                $product->is_in_stock() == true :
                $class = (isset($wpc_btn_text) && $wpc_btn_text  != '') ? 'cart-text-added' : 'cart-text-no-added';
                ?>
                <div class="wpc-add-to-cart">
                    <a href="?add-to-cart<?php echo esc_html($product->get_id()); ?>" 
                    data-product_name="<?php echo esc_html($product->get_name()); ?>"
                    data-product_price="<?php echo esc_html( wc_get_price_to_display( $product ) . get_woocommerce_currency_symbol() ); ?>"
                    data-product_id="<?php echo esc_html($product->get_id()); ?>" 
                    rel="nofollow" class="button  add_to_cart_button ajax_add_to_cart <?php echo esc_attr($class); ?>">
                        <span class="adding"> <?php echo esc_html__('Adding...', 'wpcafe'); ?></span>
                        <span class="added"> <?php echo esc_html__('Added', 'wpcafe'); ?></span>
                          <?php if (isset($wpc_btn_text) && $wpc_btn_text  != '') {
                            ?>
                            <span class="add-cart-text"> <?php echo esc_html($wpc_btn_text); ?> </span>
                            <?php
                        } ?>

                         <i class="wpcafe-cart_icon"></i>

               
                    </a>
                </div>
                <?php
                break;
                case $product->get_type() == 'external'  && 
                ($cart_button == 'on' || $cart_button == 'yes' ) && 
                $product->is_in_stock() == true :
                ?>
                <div class="wpc-external-product-link">
                    <a href="<?php echo esc_url( $product->get_product_url() ); ?>" class="wpc-btn">
                       <?php echo esc_html( $product->get_button_text() )?> 
                    </a>
                </div>
                <?php
                break;
            default:
                break;
        }
    }

    /**
     * Get variation price
     */
    public static function get_variation_price($product){
        if($product->get_type() == 'grouped'){
            $children = $product->get_children();
            $price = 0;
            // foreach ($children as $key => $value) {
            //   $_product = wc_get_product( $value );
            //   $price += $_product->get_price();
            // }
            $var_price = get_woocommerce_currency_symbol( '' ) . ' ' . $price;
        }else{
            $variation_price = $product->get_variation_prices(true); // true for getting tax price 
            $var_price = '';
            if (is_array($variation_price) && isset($variation_price['price'])) {
                $first = array_shift($variation_price['price']);
                $array_pop = array_pop($variation_price['price']);
                $last = ( !empty( $array_pop ) ) ?  "-" . get_woocommerce_currency_symbol() . $array_pop : '';
                $var_price = get_woocommerce_currency_symbol() . $first . $last ;
            }
        }

        return $var_price;
    }

    /**
     * email to admin & user for new booking request
     */
    public static function send_notification_admin_user( $settings , $args ){
        $result = false;
        
        if ( !isset( $settings['wpc_admin_notification_for_booking_req'] ) || ( isset( $settings['wpc_admin_notification_for_booking_req'] ) && $settings['wpc_admin_notification_for_booking_req'] == "on" ) && isset( $settings['wpc_admin_email_address'] )) {
           $mail_to         = $settings['wpc_admin_email_address'];
           $mail_subject    = isset($settings['wpc_admin_notification_subject']) ? $settings['wpc_admin_notification_subject'] : "";
           $default_message = $args['message'] . esc_html__( "Invoice No: " , "wpcafe") . $args['invoice'] . "";
           $mail_body       = isset($settings['wpc_admin_notification_email']) && $settings['wpc_admin_notification_email'] !=="" ? $settings['wpc_admin_notification_email'] : $default_message ;
           $wpc_main_email  = str_replace( $args['wpc_tag_arr'], $args['wpc_value_arr'], $mail_body );
           $mail_from       = isset($settings['sender_email_address']) && $settings['sender_email_address'] !==''  ? $settings['sender_email_address'] : $settings['wpc_admin_email_address'];
           $mail_from_name  = isset($settings['wpc_reply_to_name'])  ? $settings['wpc_reply_to_name']  : esc_html__("Admin","wpcafe");

            $email_args = array(
                'to'        => $mail_to,
                'subject'   => $mail_subject,
                'mail_body' => $wpc_main_email,
                'from'      => $mail_from,
                'from_name' => $mail_from_name
            );
           $result = Wpc_Utilities::wpc_send_email( $email_args );
       }
       if ( !isset( $settings['wpc_user_notification_for_booking_req'] ) || ( isset( $settings['wpc_user_notification_for_booking_req'] ) && $settings['wpc_user_notification_for_booking_req'] == "on" ) ) {
           $mail_to         = $args['wpc_email'];
           $mail_subject    = isset( $settings['wpc_new_req_email_subject'] ) ? $settings['wpc_new_req_email_subject'] : "";
           $default_message = $args['message'] . esc_html__( "Invoice No: " , "wpcafe") . $args['invoice'] . "";
           $mail_body       = isset( $settings['wpc_new_req_email'] ) && $settings['wpc_new_req_email'] !=="" ? $settings['wpc_new_req_email'] : $default_message;
           $mail_from       = isset($settings['wpc_admin_email_address']) && $settings['wpc_admin_email_address'] !==''  ? $settings['wpc_admin_email_address'] : $settings['sender_email_address'];
           $mail_from_name  = isset($settings['wpc_reply_to_name'])  ? $settings['wpc_reply_to_name']  : esc_html__("Admin","wpcafe");
           $wpc_main_email  = str_replace( $args['wpc_tag_arr'], $args['wpc_value_arr'] , $mail_body );

           $email_args = array(
               'to'         => $mail_to,
               'subject'    => $mail_subject,
               'mail_body'  => $wpc_main_email,
               'from'       => $mail_from,
               'from_name'  => $mail_from_name
           );
           $result = Wpc_Utilities::wpc_send_email( $email_args );
       }
       
       return $result;
    }

    /**
     * Menu price by tax for short code and widget
     */
    public static function menu_price_by_tax( $product ){
        $price = '';
        if (wc_get_price_excluding_tax($product)) {
            $price      = wc_get_price_excluding_tax($product);
        } else {
            $price      = wc_get_price_including_tax($product);
        }

        return $price;
    }

    /**
     * Admin page array 
     */
    public static function admin_page_array(){
        $admin_page_arr = ['toplevel_page_cafe_menu','edit-wpc_reservation','wpc_reservation','wpcafe_page_wpcafe_get_help',
        'product','edit-post','post','page','edit-page','edit-wpcafe_location','wpcafe_page_wpc-license','shop_order'];

        return $admin_page_arr;
    }

    /**
     * Get Tab Array From Category List
     *
     * @param [type] $wpc_cat_arr
     * 
     * @since 1.3.3
     * 
     * @return array tab array
     */
    public static function get_tab_array_from_category( $wpc_cat_arr ){
        
        foreach ($wpc_cat_arr as $key => $value) {
            if ($wpc_cat = get_term_by('id', $value, 'product_cat')) {
                $wpc_get_menu_order = get_term_meta($wpc_cat->term_id, 'wpc_menu_order_priority', true);
                $wpc_cat    = get_term_by('id', $value, 'product_cat');
                $cat_name   = ($wpc_cat && $wpc_cat->name ) ? $wpc_cat->name : "";
                $tab_data   = array('post_cats'=>[$value, $wpc_cat->slug],'tab_title' => $cat_name);
                if ($wpc_get_menu_order == '') {
                    $food_menu_tabs[$key] = $tab_data;
                } else {
                    $food_menu_tabs[$wpc_get_menu_order] = $tab_data;
                }
            }
        }

        return $food_menu_tabs;
    }

    
    /**
     * get_category id
     *
     * @param [type] $order_id
     * @param [type] $order
     * 
     * @since 2.4.1
     * 
     * @return void
     */
    public static function get_wpc_taxonomy_ids( $taxonomy='product_cat', $shortcode_cat = "cat_id"){
        $taxonomy       = $taxonomy;
        $args_cat = array(
            'taxonomy'     => $taxonomy,
            'number'       => 50,
            'hide_empty'   => 0,
        );
        $cats = get_categories( $args_cat );
    
    ?>
        <select  data-cat = "<?php echo esc_attr($shortcode_cat); ?>" class="wpc-shortcode-select wpc-setting-input" multiple='multiple'>

                <?php foreach($cats as $item): ?>
                <?php echo '<option value="'. esc_attr($item->term_id) . '">' . ( esc_html($item->name) ) . '</option>'; ?>
            <?php endforeach; ?>
        </select>
       
        <?php
    }

    /**
     * Replace qoute of sting
     */
    public static function replace_qoute( $data ){
        if( count( $data )>0 ){
            array_walk( $data , function( &$value , $index ) 
            { $value = str_replace(['&#039;','&lsquo;','&quot;'],'', $value); });
        }

        return $data;
    }

    /**
     * shortcode builder option range
     */
    public static function get_option_range($arr=[],  $class=""  ) {
       ?>
        <select  class="wpc-setting-input <?php echo esc_attr($class); ?>">
            <?php
            foreach($arr as $key=> $value){
                ?>
                <option value="<?php echo esc_html( $key );?>"> <?php echo esc_html($value); ?> </option>
            <?php } ?>
        </select>
       <?php
       return;
   }
    /**
     * shortcode builder hide empty
     */
    public static function get_show_hide($key){
        $hide_empty = [
            "$key='yes'" => esc_html__('Yes', 'wpcafe-pro'),
            "$key='no'" => esc_html__('No', 'wpcafe-pro'),
        ];
         echo Wpc_Utilities::get_option_range( $hide_empty, '' );
    }
    /**
     * shortcode builder hide empty
     */
    public static function get_order($key){
    $order = [
        "$key='ASC'" => esc_html__('ASC', 'wpcafe-pro'),
        "$key='DESC'" => esc_html__('DESC', 'wpcafe-pro'),
    ];
        echo Wpc_Utilities::get_option_range( $order, '' );
    }
   /**
     * shortcode builder style
     */
   public static function get_option_style( $limit , $value_name, $option_name="", $display_name=""  ) {
        ?>
        <select  class="wpc-setting-input">
            <?php for ($i=1; $i <= $limit; $i++) { ?>
                <option value="<?php echo esc_html( $value_name );?> ='<?php echo esc_html($option_name.$i);?>'"> <?php echo esc_html($display_name .$i , 'wpcafe-pro'); ?> </option>
            <?php } ?>
        </select>
        <?php
        return;
   }

}
