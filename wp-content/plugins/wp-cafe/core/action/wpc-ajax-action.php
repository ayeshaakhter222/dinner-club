<?php
namespace WpCafe\Core\Action;

defined( 'ABSPATH' ) || exit;

use WpCafe\Utils\Wpc_Utilities as Utils;
use WpCafe\Utils\Wpc_Utilities;

Class Wpc_Ajax_Action {

    use \WpCafe\Traits\Wpc_Singleton;
    /**
     * Ajax function call
     */
    public function init() {
        
        $callback = ['wpc_check_for_submission','filter_food_location'];

        if ( !empty( $callback ) ) {
            foreach ($callback as $key => $value) {
                add_action( 'wp_ajax_'.$value , [$this , $value ] );
                add_action( 'wp_ajax_nopriv_'.$value , [$this , $value ] );
            }
        }
    }

    /**
     * Reservation form submit check
     */
    public function wpc_check_for_submission() {
        // Process a booking request
        $settings = \WpCafe\Core\Base\Wpc_Settings_Field::instance()->get_settings_option();
        
        $wpc_tag_arr = [
            '{site_name}',
            '{site_link}',
            '{user_name}',
            '{user_email}',
            '{phone}',
            '{message}',
            '{party}',
            '{date}',
            '{current_time}',
            '{invoice_no}',
         ];
        
         if ( "wpc_reservation" == sanitize_text_field( $_POST['wpc_action'] ) ) {

            //check for valid nonce
            $post_arr = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );

            //store our post vars into variables for later use
            //now would be a good time to run some basic error checking/validation
            //to ensure that data for these values have been set
            
            $meta_array                          = [];
            $meta_array['wpc_name']              = $title                 = isset( $post_arr['wpc_name'] ) ? sanitize_text_field( $post_arr['wpc_name'] ) : "";
            $meta_array['wpc_message']           = $content               = isset( $post_arr['wpc_message'] ) ? sanitize_text_field( $post_arr['wpc_message'] ) : "";
            $meta_array['wpc_email']             = $wpc_email             = (isset( $post_arr['wpc_email'] ) && is_email( $post_arr['wpc_email'] ) ) ? sanitize_email( $post_arr['wpc_email'] ) : "";
            $meta_array['wpc_phone']             = $wpc_phone             = isset( $post_arr['wpc_phone'] ) ? preg_replace( '/[^0-9+-]/', '', sanitize_text_field( $post_arr['wpc_phone'] ) ) : "";
            $meta_array['wpc_total_guest']       = $wpc_total_guest       = isset( $post_arr['wpc_guest_count'] ) ? intval( sanitize_text_field( $post_arr['wpc_guest_count'] ) ) : "";
            $meta_array['wpc_from_time']         = $wpc_from_time         = isset( $post_arr['wpc_from_time'] ) ? sanitize_text_field( $post_arr['wpc_from_time'] ) : "";
            $meta_array['wpc_to_time']           = $wpc_to_time           = isset( $post_arr['wpc_to_time'] ) ? sanitize_text_field( $post_arr['wpc_to_time'] ) : "";
            $meta_array['wpc_booking_date']      = $wpc_date              = isset( $post_arr['wpc_booking_date'] ) ? $post_arr['wpc_booking_date'] : "";
            $meta_array['wpc_branch']            = $wpc_branch            = isset( $post_arr['wpc_branch'] ) ? $post_arr['wpc_branch'] : "";
            
            $post_type                           = 'wpc_reservation';

            $post_slug = sanitize_title_with_dashes( $title, '', 'save' );
            $postslug  = sanitize_title( $post_slug );

            if ( isset( $title ) && isset( $wpc_email ) && isset( $wpc_total_guest ) &&
                isset( $wpc_from_time ) && isset( $wpc_to_time ) && isset( $wpc_date ) ||
                ( isset( $settings['wpc_require_phone'] ) && isset( $wpc_phone ) ) ||
                ( isset( $settings['require_branch'] ) && isset( $wpc_branch ) )
                ) {

                //the array of arguments to be inserted with wp_insert_post
                $new_post = [
                    'post_title'     => $title,
                    'post_content'   => $content,
                    'post_status'    => 'publish',
                    'post_type'      => $post_type,
                    'comment_status' => 'closed',
                    'post_name'      => $postslug,
                ];

                //insert the the post into database by passing $new_post to wp_insert_post
                //store our post ID in a variable $pid
                $pid                                   = wp_insert_post( $new_post );
                $invoice                               = Utils::generate_invoice_number( $pid );
                $meta_array['wpc_reservation_invoice'] = $invoice;

                // if food with reservaion
                if ( !empty( $post_arr['order_id'] ) ) {
                    $meta_array['order_id']          = $post_arr['order_id'];
                    $response_data                   = get_post_meta( $meta_array['order_id'] , 'reservation_details', true);
                    if ( $response_data !=="" ) {
                        $response_data->reservation_id = $pid;
                        update_post_meta( $meta_array['order_id'] , 'reservation_details' , $response_data );
                    }

                    //Automatic confirmd booking guest no
                    $meta_array['wpc_reservation_state'] = !empty( $response_data->status ) ? $response_data->status : 'pending';

                }else{
                    $default_guest                       = isset( $settings['wpc_default_guest_no'] ) ? intval( $settings['wpc_default_guest_no'] ) : 0;
                    $meta_array['wpc_reservation_state'] = ( (int)$default_guest !== 0 || (int)$wpc_total_guest  <= (int)$default_guest ) ? 'confirmed' : 'pending';
                }

                //we now use $pid (post id) to help add out post meta data
                foreach ( $meta_array as $key => $value ) {
                    add_post_meta( $pid, $key, $value, true );
                }

                apply_filters( 'wpcafe_pro/action/extra_field', $pid , $post_arr );

                /** use action for success message **/
                if ( $pid != 0 ) {
                    $time_start = get_post_meta( $pid, 'wpc_from_time', true );
                    $time_end   = get_post_meta( $pid, 'wpc_to_time', true );

                    $schedule_1 = $time_start !=="" ? esc_html__(" Start time ","wpcafe").": " : "";
                    $schedule_2 = $time_end !=="" ? esc_html__(" End time ","wpcafe").": " : "";

                    $wpc_value_arr = [
                        get_bloginfo( 'name' ),
                        get_option( 'home' ),
                        get_post_meta( $pid, 'wpc_name', true ),
                        get_post_meta( $pid, 'wpc_email', true ),
                        get_post_meta( $pid, 'wpc_phone', true ),
                        get_post_meta( $pid, 'wpc_message', true ),
                        get_post_meta( $pid, 'wpc_total_guest', true ),
                        get_post_meta( $pid, 'wpc_booking_date', true ) . $schedule_1  .
                        get_post_meta( $pid, 'wpc_from_time', true ) . $schedule_2 .
                        get_post_meta( $pid, 'wpc_to_time', true ),
                        date( 'Y-m-d H:i:s' ),
                        $invoice,
                    ];

                    $message = ''; $form_type = "";
                    
                    if ( $meta_array['wpc_reservation_state'] == 'confirmed' ) {
                        $message  = $settings['wpc_booking_confirmed_message'];

                    } elseif ( $meta_array['wpc_reservation_state'] == 'pending' ) {
                        $message  = $settings['wpc_pending_message'];
                    }

                    $form_type          ="wpc_reservation";

                    $response = [ 'status_code' => 200 , 'message' => [ $message ] ,'data' => ['form_type' => $form_type , 'invoice'=> $invoice ] ];

                    /**
                     * email to admin & user for new booking request
                     */
                    $args = array(
                        'wpc_email'     => $wpc_email,
                        'invoice'       => $invoice,
                        'message'       => $message,
                        'wpc_tag_arr'   => $wpc_tag_arr,
                        'wpc_value_arr' => $wpc_value_arr,
                    );
                    
                    $send_notification = apply_filters('wpcafe/notification/send_email_notification', true, $invoice);
                    if( $send_notification ){
                        Utils::send_notification_admin_user( $settings , $args );
                    }

                    wp_send_json_success( $response );
                    
                } else {
                    $response = [ 'status_code' => 400 , 'message' => [ esc_html__('Booking placement was failed, please try again!' ,'wpcafe' ) ] , 'data' => ['form_type' => 'wpc_reservation_field_missing'] ];
                    wp_send_json_error( $response );
                }
            } else {
                $response = [ 'status_code' => 400 , 'message' => [ esc_html__('Please enter all required fields!' ,'wpcafe' )  ] , 'data' => ['form_type' => 'wpc_reservation_field_missing'] ];
                wp_send_json_error( $response );
            }
        }

        if ( isset( $_POST['wpc_action'] ) && "wpc_cancellation" == sanitize_text_field( $_POST['wpc_action'] ) ) {

            $post_arr   = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
            $invoice_no = isset( $post_arr['wpc_reservation_invoice'] ) ?
            sanitize_text_field( $post_arr['wpc_reservation_invoice'] ) : "";
            $wpc_email = ( isset( $post_arr['wpc_cancell_email'] ) && is_email( $post_arr['wpc_cancell_email'] ) ) ?
            sanitize_email( $post_arr['wpc_cancell_email'] ) : "";
            $wpc_phone = isset( $post_arr['wpc_cancell_phone'] ) ?
            preg_replace( '/[^0-9+-]/', '', sanitize_text_field( $post_arr['wpc_cancell_phone'] ) ) : "";
            $content = sanitize_text_field( $post_arr['wpc_message'] );
            $eligible_for_cancellation = apply_filters('wpcafe/cancellation_form/invoice_eligibility', true, $invoice_no);
            //check if all required fields are given
            //else show a message

            if( !$eligible_for_cancellation ){
                $response = [ 
                    'status_code' => 400 , 
                    'message' => [ esc_html__(  'Your reservation includes food menu order. So this reservation can not be cancelled through this form. Please contact restaurant admin for cancelling your reservation manually.', 'wpcafe' )  ] , 
                    'data' => ['form_type' => 'wpc_reservation_cancell'] 
                ];
                wp_send_json_error( $response );
            }else if ( $invoice_no && $wpc_email ) {
                $args = array(
                    'post_type'      => 'wpc_reservation',
                    'posts_per_page' => '1',
                    'meta_query'     => array(
                        array(
                            'key'   => 'wpc_reservation_invoice',
                            'value' => $invoice_no,
                        ),
                        array(
                            'key'   => 'wpc_email',
                            'value' => $wpc_email,
                        )
                    ),
                );

                $reservations = get_posts( $args );

                //check if reservation record found with the given details
                if ( !$reservations || is_wp_error( $reservations ) ) {
                    $response = [ 'status_code' => 401 , 'message' => [ esc_html__( 'No reservation found with the given details' , 'wpcafe' ) ] , 'data' => ['form_type' => 'wpc_reservation_cancell'] ];
                } else {
                    $reservation_id = $reservations[0]->ID;
                    update_post_meta( $reservation_id, 'wpc_reservation_state', 'cancelled' );
                    apply_filters( 'wpcafe/action/cancell_notification', $settings, $invoice_no , $wpc_tag_arr );
                    $response = [ 'status_code' => 200 , 'message' => [ esc_html__( 'Cancellation requested successfully!', 'wpcafe' ) ] , 'data' => ['form_type' => 'wpc_reservation_cancell'] ];
                }
                wp_send_json_success( $response );
            } else {
                $response = [ 'status_code' => 400 , 'message' => [ esc_html__(  'Please enter required fields correctly!', 'wpcafe' )  ] , 'data' => ['form_type' => 'wpc_reservation_cancell'] ];
                wp_send_json_error( $response );
            }
        }

        exit;
    }

    /**
     * Filter menu by food location
     */
    public function filter_food_location(){

        $post_arr     = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
        $location   = $post_arr['location'];
        if( $location !== ''){
            $product_data           = $post_arr['product_data'];
            $show_thumbnail         = $product_data['show_thumbnail'];
            $show_item_status       = $product_data['show_item_status'];
            $wpc_cart_button        = $product_data['wpc_cart_button'];
            $wpc_show_desc          = $product_data['wpc_show_desc'];
            $wpc_delivery_time_show = $product_data['wpc_delivery_time_show'];
            $wpc_desc_limit         = $product_data['wpc_desc_limit'];
            $unique_id              = $product_data['unique_id'];
            $col                    = 'wpc-col-md-'.$product_data['wpc_menu_col'];
            $title_link_show        = $product_data['title_link_show'];

            $args = array(
                'order'         => 'DESC',
                'wpc_cat'       => [$location],
                'taxonomy'      => 'wpcafe_location',
            );

            $products = Wpc_Utilities::product_query ( $args );
            
            ?>
            <div class='wpc-food-wrapper wpc-menu-list-style1'>
                <?php

                if ( !empty( $products ) ) { 
                    
                    include \Wpcafe::plugin_dir() . "widgets/wpc-menus-list/style/style-1.php";

                }else{
                    ?>
                        <div><?php esc_html_e( 'No menu found' , 'wpcafe-pro')?></div>
                    <?php
                }
                ?>
            </div>
            <?php
        }

        wp_die();
    }

}
