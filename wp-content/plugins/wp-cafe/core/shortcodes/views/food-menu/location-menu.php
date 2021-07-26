<?php
// food location list
use WpCafe\Utils\Wpc_Utilities;

$food_location  = Wpc_Utilities::get_location_data( "Select food location","No location is set","id" );

?>

    <!-- select location -->
    <select id="filter_location" name="filter_location" class="filter-location <?php echo esc_attr($location_alignment); ?>">
        <?php foreach ( $food_location as $key => $value ) { ?>
            <option value="<?php esc_attr_e($key) ?>"><?php esc_html_e( $value ) ?></option>
        <?php } ?>
    </select>
    <!-- render html -->
    <div class="food_location">
            <?php
            if ( !empty( $products ) ) { 
                include \Wpcafe::plugin_dir() . "widgets/wpc-menus-list/style/${style}.php";

            }else{
                ?>
                    <div><?php esc_html_e( 'No menu found' , 'wpcafe')?></div>
                <?php
            }
            ?>
    </div>
    
</div>



