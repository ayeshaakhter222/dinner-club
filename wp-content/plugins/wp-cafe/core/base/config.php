<?php
namespace WpCafe\Core\Base;

use WpCafe_Pro\Core\Template\Food_Menu;

defined( 'ABSPATH' ) || exit;

/**
 * Check all active plugin and returns instane and functions
 */

class Config{
    use \WpCafe\Traits\Wpc_Singleton;

    public function init( $class_name ) {
        $reponse_type = false ; $obj_template = null;

        switch( $class_name ){
            
            case "WpCafe_Pro":
                if ( did_action( 'wpcafe_pro/after_load' ) ) {
                    $reponse_type   = true;
                    $obj_template   = Food_Menu::instance();
                }

            break;

            case "Wpcafe_Multivendor":
                if ( class_exists( 'Wpcafe_Multivendor' ) ) {
                    $reponse_type   = true;
                }

            break;

            default:
                return;
            break;
        }

        $result_data = [ 'success' => $reponse_type ,'obj_template' => $obj_template  ];

        return $result_data;
    }
}