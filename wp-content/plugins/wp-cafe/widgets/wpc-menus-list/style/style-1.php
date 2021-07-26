<?php

use \WpCafe\Core\Shortcodes\Template_Functions as Wpc_Widget_Template;

$col = ($show_thumbnail == 'yes') ? 'wpc-col-md-8' : 'wpc-col-md-12';
$class = ($title_link_show=='yes')? '' : 'wpc-no-link';

foreach ($products as $product) { 
    $permalink = ( $title_link_show == 'yes' ) ?  get_permalink($product->get_id()) : '#';
    ?>
     <?php
        $food_menu_list_args = array(
            'show_thumbnail'    => $show_thumbnail,
            'permalink'         => $permalink,
            'wpc_cart_button'   => $wpc_cart_button,
            'unique_id'         => $unique_id,
            'product'           => $product,
            'class'             => $class,
            'show_item_status'  => $show_item_status,
            'wpc_show_desc'     => $wpc_show_desc,
            'col'               => $col,
            'wpc_desc_limit'    => $wpc_desc_limit
        );

        Wpc_Widget_Template::wpc_food_menu_list_template( $food_menu_list_args );
     ?>
    <?php 
}