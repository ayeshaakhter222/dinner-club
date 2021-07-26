<?php
namespace WpCafe\Core\Enqueue;

defined( 'ABSPATH' ) || exit;

use WpCafe\Traits\Wpc_Singleton;
use WpCafe\Utils\Wpc_Utilities;

/**
 * Enqueue all css and js file class
 */
class Wpc_Enqueue {

    use Wpc_Singleton;

    /**
     * Main calling function
     */
    public function init() {
        // backend asset
        add_action( 'admin_enqueue_scripts', [$this, 'admin_enqueue_assets'] );
        // frontend asset
        add_action( 'wp_enqueue_scripts', [$this, 'frontend_enqueue_assets'] );
        // enqueue editor css.
        add_action( 'elementor/editor/before_enqueue_styles', [$this, 'elementor_editor_css'] );
        // enqueue editor js.
        add_action( 'elementor/frontend/before_enqueue_scripts', [$this, 'elementor_js'] );
    }
  

    /**
     * all js files function
     */
    public function admin_get_scripts() {
        $data = $this->wpc_settings_obj();
        $reserv_form_local = isset($data['reserv_form_local']) && $data['reserv_form_local'] !=="en" ? $data['reserv_form_local'] : "";
        $script_arr =  array(
            'moment'     => array(
                'src'     => \Wpcafe::assets_url() . 'js/moment.min.js',
                'version' => \Wpcafe::version(),
                'deps'    => ['jquery'],
            ),
            'flatpicker' => array(
                'src'     => \Wpcafe::assets_url() . 'js/flatpickr.min.js',
                'version' => \Wpcafe::version(),
                'deps'    => ['jquery'],
            ),
            'wpc-jquery-timepicker' => array(
                'src'     => \Wpcafe::assets_url() . 'js/jquery.timepicker.min.js',
                'version' => \Wpcafe::version(),
                'deps'    => ['jquery'],
            ),
            'wpc-ui'      => array(
                'src'     => \Wpcafe::assets_url() . 'js/wpc-ui.min.js',
                'version' => \Wpcafe::version(),
                'deps'    => ['jquery'],
            ),
            'wpc-admin'   => array(
                'src'     => \Wpcafe::assets_url() . 'js/wpc-admin.js',
                'version' => \Wpcafe::version(),
                'deps'    => ['jquery'],
            ),
            'wpc-common'      => array(
                'src'     => \Wpcafe::assets_url() . 'js/common.js',
                'version' => \Wpcafe::version(),
                'deps'    => ['jquery'],
            ),
        );
        
        if( $reserv_form_local !=="" ){
            $script_arr['wpc-translate'] = array(
                'src'     => \Wpcafe::assets_url() . 'js/local/'.$reserv_form_local.'.js',
                'version' => \Wpcafe::version(),
                'deps'    => ['jquery'],
            );
        }
        
        return $script_arr;
    }

    /**
     * all css files function
     *
     * @param Type $var
     */
    public function admin_get_styles() {
        return array(
            'flatpicker' => array(
                'src'     => \Wpcafe::assets_url() . 'css/flatpickr.min.css',
                'version' => \Wpcafe::version(),
            ),
            'jquery-timepicker' => array(
                'src'     => \Wpcafe::assets_url() . 'css/jquery.timepicker.min.css',
                'version' => \Wpcafe::version(),
            ),
        
            'wpc-ui'      => array(
                'src'     => \Wpcafe::assets_url() . 'css/wpc-ui.css',
                'version' => \Wpcafe::version(),
            ),
            'wpc-common' => array(
                'src'     => \Wpcafe::assets_url() . 'css/wpc-common.css',
                'version' => \Wpcafe::version(),
            )
        );
    }

    /**
     * Enqueue admin js and css function
     *
     * @param  $var
     */
    public function admin_enqueue_assets() {
        $screen = get_current_screen();
        
        $admin_page_arr = Wpc_Utilities::admin_page_array();

        // load js only wpcafe page
        if ( is_admin() && ( in_array( $screen->id , $admin_page_arr ) ) ) {
                // js
                wp_enqueue_script( 'wp-color-picker' );

                $scripts = $this->admin_get_scripts();

                foreach ( $scripts as $key => $value ) {
                    $deps       = !empty( $value['deps'] ) ? $value['deps'] : false;
                    $version    = !empty( $value['version'] ) ? $value['version'] : false;
                    wp_enqueue_script( $key, $value['src'], $deps, $version, true );
                }

                // css
                wp_enqueue_style( 'wp-color-picker' );
                $styles = $this->admin_get_styles();

                foreach ( $styles as $key => $value ) {
                    $deps       = isset( $value['deps'] ) ? $value['deps'] : false;
                    $version    = !empty( $value['version'] ) ? $value['version'] : false;
                    wp_enqueue_style( $key, $value['src'], $deps, $version, 'all' );
                }

                // locallize for admin
                $form_data                          = $this->wpc_settings_obj();
                $form_data['wpc_ajax_url']          = admin_url( 'admin-ajax.php' );
                $form_data['wpc_settings_nonce']    = wp_create_nonce( 'wpc_settings_nonce' ); 
                wp_localize_script( 'wpc-admin', 'wpc_form_data', [ $form_data ] );
        }

        wp_enqueue_style('wpc-icon', \Wpcafe::assets_url() . 'css/wpc-icon.css' , false , \Wpcafe::version() , 'all' );
        
        if ( is_admin() && ( $screen->id !== "themes"  ) ) {
            wp_enqueue_style('wpc-admin', \Wpcafe::assets_url() . 'css/wpc-admin.css' , false , \Wpcafe::version() , 'all' );
        }
    }

    /**
     * Make obj to send localize script
     */
    public function wpc_settings_obj() {
        $wpc_one_day   = date( 'Y-m-d', strtotime( date( 'Y-m-d' ) . ' +1 day' ) );
        $wpc_one_week  = date( 'Y-m-d', strtotime( date( 'Y-m-d' ) . ' +7 day' ) );
        $wpc_one_month = date( 'Y-m-t', strtotime( date( 'Y-m-d' ) ) );
        $wpc_today     = date( 'Y-m-d' );

        $form_data = [];
        
        $settings                   = \WpCafe\Core\Base\Wpc_Settings_Field::instance()->get_settings_option();
        if ( $settings ) {
            $reserv_form_local              =  isset( $settings['reserv_form_local'] ) ? $settings['reserv_form_local'] : 'en' ;
            $wpc_weekly_schedule            =  isset( $settings['wpc_weekly_schedule'] ) ? $settings['wpc_weekly_schedule'] : '' ;
            $wpc_weekly_schedule_start_time =  isset( $settings['wpc_weekly_schedule_start_time'] ) ? $settings['wpc_weekly_schedule_start_time'] : '' ;
            $wpc_weekly_schedule_end_time   =  isset( $settings['wpc_weekly_schedule_end_time'] ) ? $settings['wpc_weekly_schedule_end_time'] : '' ;
            $wpc_all_day_start_time         =  isset( $settings['wpc_all_day_start_time'] ) ? $settings['wpc_all_day_start_time'] : '' ;
            $wpc_all_day_end_time           =  isset( $settings['wpc_all_day_end_time'] ) ? $settings['wpc_all_day_end_time'] : '' ;
            $wpc_exception_date             =  isset( $settings['wpc_all_day_end_time'] ) ? $settings['wpc_exception_date'] : '' ;
            $wpc_exception_start_time       =  isset( $settings['wpc_exception_start_time'] ) ? $settings['wpc_exception_start_time'] : '' ;
            $wpc_exception_end_time         =  isset( $settings['wpc_exception_end_time'] ) ? $settings['wpc_exception_end_time'] : '' ;
            $wpc_date_format                =  isset( $settings['wpc_date_format'] ) ? $settings['wpc_date_format'] : '' ;
            $wpc_time_format                =  isset( $settings['wpc_time_format'] ) ? $settings['wpc_time_format'] : '' ;
            $wpc_early_bookings             =  isset( $settings['wpc_early_bookings'] ) ? $settings['wpc_early_bookings'] : '';
            $wpc_late_bookings              =  isset( $settings['wpc_late_bookings'] ) ? $settings['wpc_late_bookings'] : '' ;
            $wpc_pending_message            =  isset( $settings['wpc_pending_message'] ) ? $settings['wpc_pending_message'] : '' ;
            $reserve_dynamic_message        =  isset( $settings['reserve_dynamic_message'] ) ? $settings['reserve_dynamic_message'] : '' ;
            $wpc_booking_confirmed_message  =  isset( $settings['wpc_booking_confirmed_message'] ) ? $settings['wpc_booking_confirmed_message'] : '';

            //multi slot data
            $reser_multi_schedule           =  isset( $settings['reser_multi_schedule'] ) ? $settings['reser_multi_schedule'] : '' ;
            $multi_start_time               =  isset( $settings['multi_start_time'] ) ? $settings['multi_start_time'] : [] ;
            $multi_end_time                 =  isset( $settings['multi_end_time'] ) ? $settings['multi_end_time'] : [] ;
            $weekly_multi_diff_times        =  !empty( $settings['weekly_multi_diff_times']) ? $settings['weekly_multi_diff_times'] : [];
            
            $form_data                      = [
                'wpc_weekly_schedule'            => $wpc_weekly_schedule,
                'wpc_weekly_schedule_start_time' => $wpc_weekly_schedule_start_time,
                'wpc_weekly_schedule_end_time'   => $wpc_weekly_schedule_end_time,
                'wpc_all_day_start_time'         => $wpc_all_day_start_time,
                'wpc_all_day_end_time'           => $wpc_all_day_end_time,
                'wpc_exception_date'             => $wpc_exception_date,
                'wpc_exception_start_time'       => $wpc_exception_start_time,
                'wpc_exception_end_time'         => $wpc_exception_end_time,
                'wpc_date_format'                => $wpc_date_format,
                'wpc_time_format'                => $wpc_time_format,
                'wpc_early_bookings'             => $wpc_early_bookings,
                'wpc_late_bookings'              => $wpc_late_bookings,
                'wpc_pending_message'            => $wpc_pending_message,
                'wpc_booking_confirmed_message'  => $wpc_booking_confirmed_message,
                'wpc_one_day'                    => $wpc_one_day,
                'wpc_one_week'                   => $wpc_one_week,
                'wpc_one_month'                  => $wpc_one_month,
                'wpc_today'                      => $wpc_today,
                'reserv_form_local'              => $reserv_form_local,
                'reserve_dynamic_message'        => $reserve_dynamic_message,
                'no_schedule_message'            => esc_html__('No schedule is set from admin','wpcafe'),

                //multi slot data
                'weekly_multi_diff_times'        => $weekly_multi_diff_times,
                'reser_multi_schedule'           => $reser_multi_schedule,
                'multi_start_time'               => $multi_start_time,
                'multi_end_time'                 => $multi_end_time,
                'multi_time_excludes'            => [],
                // Time validation message
                'time_valid_message'             => esc_html__("End time must be after start time","wpcafe")
            ];
        }

        return $form_data;
    }

    /**
     * all js files function
     */
    public function frontend_get_scripts() {
        $data = $this->wpc_settings_obj();

        $reserv_form_local = isset($data['reserv_form_local']) && $data['reserv_form_local'] !=="en" ? $data['reserv_form_local'] : "";
        
        $script_arr = array(
            'wpc-moment'     => array(
                'src'     => \Wpcafe::assets_url() . 'js/moment.min.js',
                'version' => \Wpcafe::version(),
                'deps'    => ['jquery'],
            ),
            'wpc-flatpicker' => array(
                'src'     => \Wpcafe::assets_url() . 'js/flatpickr.min.js',
                'version' => \Wpcafe::version(),
                'deps'    => ['jquery'],
            ),
            'wpc-jquery-timepicker' => array(
                'src'     => \Wpcafe::assets_url() . 'js/jquery.timepicker.min.js',
                'version' => \Wpcafe::version(),
                'deps'    => ['jquery'],
            ),
            'wpc-public'     => array(
                'src'     => \Wpcafe::assets_url() . 'js/wpc-public.js',
                'version' => \Wpcafe::version(),
                'deps'    => ['jquery'],
            ),
            'wpc-common'      => array(
                'src'     => \Wpcafe::assets_url() . 'js/common.js',
                'version' => \Wpcafe::version(),
                'deps'    => ['jquery'],
            ),
        );

        if( $reserv_form_local !=="" ){

            $script_arr['wpc-translate'] = array(
                'src'     => \Wpcafe::assets_url() . 'js/local/'.$reserv_form_local.'.js',
                'version' => \Wpcafe::version(),
                'deps'    => ['jquery'],
            );
        }

        return $script_arr;
    }

    /**
     * all css files function
     */
    public function frontend_get_styles() {
        $enequeue =  array(
            'flatpicker' => array(
                'src'     => \Wpcafe::assets_url() . 'css/flatpickr.min.css',
                'version' => \Wpcafe::version(),
            ),
       
            'jquery-timepicker' => array(
                'src'     => \Wpcafe::assets_url() . 'css/jquery.timepicker.min.css',
                'version' => \Wpcafe::version(),
            ),
            'wpc-icon'       => array(
                'src'     => \Wpcafe::assets_url() . 'css/wpc-icon.css',
                'version' => \Wpcafe::version(),
            ),
         
        );

        // divi builder support css load
        if(class_exists( 'ET_Builder_Plugin')){
            $enequeue['wpc-public'] =[
                'src'     => \Wpcafe::assets_url() . 'css/wpc-divi-builder-support.css',
                'version' => \Wpcafe::version(),
            ];
        }else{
            $enequeue['wpc-public'] =[
                'src'     => \Wpcafe::assets_url() . 'css/wpc-public.css',
                'version' => \Wpcafe::version(),
            ];
        }

        if(is_rtl()){
            $enequeue['wpc-rtl'] =[
                'src'     => \Wpcafe::assets_url() . 'css/rtl.css',
                'version' => \Wpcafe::version(),
            ];
        }

        return $enequeue;
    }

    /**
     * Enqueue admin js and css function
     */
    public function frontend_enqueue_assets() {
        // js
        $scripts = $this->frontend_get_scripts();

        foreach ( $scripts as $key => $value ) {
            $deps       = isset( $value['deps'] ) ? $value['deps'] : false;
            $version    = !empty( $value['version'] ) ? $value['version'] : false;
            wp_enqueue_script( $key, $value['src'], $deps, $version, true );
        }

        // css
        $styles = $this->frontend_get_styles();

        foreach ( $styles as $key => $value ) {
            $deps = isset( $value['deps'] ) ? $value['deps'] : false;
            $version    = !empty( $value['version'] ) ? $value['version'] : false;
            wp_enqueue_style( $key, $value['src'], $deps, $version, 'all' );
        }

        // localize for frontend
        $form_data                                      = [];
        $form_data['settings']                          = $this->wpc_settings_obj();
        $form_data['wpc_ajax_url']                      = admin_url( 'admin-ajax.php' );
        $form_data['wpc_validation_message'] = [
            'error_text'    => esc_html__('Please fill the field', 'wpcafe'),
            'email'         => esc_html__('Email is not valid', 'wpcafe'),
            'phone'         => [
                'phone_invalid'     => esc_html__('Invalid phone number', 'wpcafe'),
                'number_allowed'    => esc_html__('Only number allowed', 'wpcafe'),
             ],

        ];

        wp_localize_script( 'wpc-public', 'wpc_form_client_data', [ json_encode( $form_data ) ] );
    }

    /**
     *elementor editor css loaded
     */
    public function elementor_editor_css() {
        wp_enqueue_style( 'wpc-elementor-editor', \Wpcafe::assets_url() . 'css/elementor-editor.css', [], \Wpcafe::version(), true );
    }

    /**
     *elementor js loaded
     */
    public function elementor_js() {
        wp_enqueue_script( 'wpc-elementor-frontend', \Wpcafe::assets_url() . 'js/elementor.js', [ 'elementor-frontend' ], \Wpcafe::version(), true );
    }

}


