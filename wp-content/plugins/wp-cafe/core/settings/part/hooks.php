<?php 
 use WpCafe\Utils\Wpc_Utilities;
?>
<h3 class="wpc-tab-title"><?php esc_html_e('Shortcodes', 'wpcafe'  ); ?></h3>

<!-- reservation form  -->
<div class="shortcode-generator-wrap">
    <div class="shortcode-generator-main-wrap">
        <div class="shortcode-generator-inner">
            <div class="shortcode-popup-close">x</div>

            <div class="wpc-row">
                <div class="wpc-col-lg-6">
                    <div class="wpc-field-wrap">
                        <h3><?php echo esc_html__('Select Template', 'wpcafe'); ?></h3>
                        <select  class="get_template wpc-setting-input">
                            <option value="wpc_reservation_form"> <?php echo esc_html__(' Reservation Form', 'wpcafe'); ?> </option>
                        </select>
                    </div>
                </div>
                <div class="wpc-col-lg-6">
                    <div class="wpc-field-wrap reserv-form-style">
                        <h3><?php echo esc_html__('Select Style', 'wpcafe'); ?></h3>
                        <?php
                            echo Wpc_Utilities::get_option_style( 2, 'form_style','', 'Style ' );
                        ?>
                    </div>
                </div>
            </div>
            <div class="wpc-row">
                <div class="wpc-col-lg-12">
                     <div class="wpc-field-wrap image-url-field">
                        <h3><?php echo esc_html__('Image url for style 1', 'wpcafe'); ?></h3>
                        <input type="url" data-count="<?php echo esc_attr('wpc_image_url'); ?>" placeholder="<?php echo esc_attr('http://domain.com/img.jpg'); ?>" class="wpc-setting-input post_count">
                    </div>
                </div>
            </div>
        
            <button type="button"  class="wpc-btn shortcode-generate-btn"><?php echo esc_html__('Generate', 'wpcafe'); ?></button>
            
            
            <div class="attr-form-group wpc-label-item copy_shortcodes">
                <div class="wpc-meta">
                    <input type="text" readonly name="etn_event_label" id="wpc_reservation_form-shortcode" value="[wpc_reservation_form]" class="wpc-setting-input wpc_include_shortcode" placeholder="<?php esc_html_e('Label Text', 'wpcafe'); ?>">
                    <button type="button" title="copy" onclick="copyTextData('wpc_reservation_form-shortcode');" class="etn_copy_button wpc-btn"><span class="dashicons dashicons-category"></span></button>
                </div>
            </div>
        </div>
    </div>
   
    <div class="wpc-label-item">
        <div class="wpc-label">
            <label for="wpc_reservation_form"><?php esc_html_e('Reservation Form', 'wpcafe'  ); ?></label>
            <div class="wpc-desc"> <?php esc_html_e("You can generate shortcode",  'wpcafe'  );?></div>
        </div>
        <div class="wpc-meta">
            <button type="button" class="wpc-btn s-generate-btn"><?php echo esc_html__('Generate Shortcode', 'wpcafe'); ?></button>

        </div>
    </div>
</div>

<!-- Food Menu -->
<div class="shortcode-generator-wrap">
    <div class="shortcode-generator-main-wrap">
        <div class="shortcode-generator-inner">
            <div class="shortcode-popup-close">x</div>

            <div class="wpc-row">
                <div class="wpc-col-lg-6">
                    <div class="wpc-field-wrap">
                        <h3><?php echo esc_html__('Select Template', 'wpcafe'); ?></h3>
                        <?php
                            $wpc_food_menu = [
                                "wpc_food_menu_list" => esc_html__('Food Menu List', 'wpcafe'),
                                "wpc_food_menu_tab" => esc_html__('Food Menu Tab', 'wpcafe'),
                            ];
                        ?>
                        <?php echo Wpc_Utilities::get_option_range( $wpc_food_menu, 'free-options' );?>
                    </div>
                </div>
                <div class="wpc-col-lg-6">
                    <div class="wpc-field-wrap">
                        <h3><?php echo esc_html__('Select Style', 'wpcafe'); ?></h3>
                       <div class="list-style">
                            <?php  echo Wpc_Utilities::get_option_style( 3 ,'style','style-', 'Style ' ); ?>
                        </div> 
                        <div class="tab-style wpc-d-none">
                            <?php  echo Wpc_Utilities::get_option_style( 2 ,'style','style-', 'Style ' ); ?>
                       </div> 
                    </div>
                </div>
            </div>

            <div class="wpc-row">
                <div class="wpc-col-lg-6">
                    <div class="wpc-field-wrap">
                        <h3><?php echo esc_html__('Order', 'wpcafe'); ?></h3>
                            <?php Wpc_Utilities::get_order('wpc_menu_order');?>
                    </div>
                </div>
                <div class="wpc-col-lg-6">
                     <div class="wpc-field-wrap">
                        <h3><?php echo esc_html__('Product Count', 'wpcafe'); ?></h3>
                        <input type="number" data-count ="<?php echo esc_attr('no_of_product') ?>" class="post_count wpc-setting-input" value="20">
                    </div>
                </div>
            </div>
            <div class="wpc-row">
                <div class="wpc-col-lg-12">
                     <div class="wpc-field-wrap">
                        <h3><?php echo esc_html__('Select Category', 'eventin'); ?></h3>
                        <?php
                        echo Wpc_Utilities::get_wpc_taxonomy_ids('product_cat','wpc_food_categories');
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="wpc-row">
                <div class="wpc-col-lg-6">
                    <div class="wpc-field-wrap">
                        <h3><?php echo esc_html__('Show Description', 'wpcafe'); ?></h3>
                        <?php Wpc_Utilities::get_show_hide('wpc_show_desc');?>
                    </div>
                </div>
                <div class="wpc-col-lg-6">
                     <div class="wpc-field-wrap">
                        <h3><?php echo esc_html__('Description Limit', 'wpcafe'); ?></h3>
                        <input type="number" data-count ="<?php echo esc_attr('wpc_desc_limit') ?>" class="post_count wpc-setting-input" value="20">
                    </div>
                </div>
            </div>

            <div class="wpc-row">
                <div class="wpc-col-lg-6">
                    <div class="wpc-field-wrap">
                        <h3><?php echo esc_html__('Enable title link?', 'wpcafe'); ?></h3>
                            <select class="wpc-setting-input">
                                <option value="title_link_show='yes'"><?php echo esc_html__('Yes', 'wpcafe') ?></option>
                                <option value="title_link_show='no'"><?php echo esc_html__('no', 'wpcafe') ?></option>
                            </select>
                    </div>
                </div>
                <div class="wpc-col-lg-6">
                    <div class="wpc-field-wrap">
                        <h3><?php echo esc_html__('Show item status?', 'wpcafe'); ?></h3>
                        <?php Wpc_Utilities::get_show_hide('title_link_show');?> 

                    </div>
                </div>
            </div>
            <div class="wpc-row">
                <div class="wpc-col-lg-6">
                    <div class="wpc-field-wrap">
                        <h3><?php echo esc_html__('Show Product Thumbnail', 'wpcafe'); ?></h3>
                        <?php Wpc_Utilities::get_show_hide('product_thumbnail');?> 

                    </div>
                </div>
                <div class="wpc-col-lg-6">
                    <div class="wpc-field-wrap">
                        <h3><?php echo esc_html__('Show cart button', 'wpcafe'); ?></h3>
                        <?php Wpc_Utilities::get_show_hide('wpc_cart_button');?> 
                    </div>
                </div>
            </div>
        
            <button type="button"  class="wpc-btn shortcode-generate-btn"><?php echo esc_html__('Generate', 'wpcafe'); ?></button>
            
            
            <div class="attr-form-group wpc-label-item copy_shortcodes">
                <div class="wpc-meta">
                    <input type="text" readonly name="etn_event_label" id="wpc_food_menu_tab-shortcode" value="[wpc_food_menu_tab]" class="wpc-setting-input wpc_include_shortcode" placeholder="<?php esc_html_e('Label Text', 'wpcafe'); ?>">
                    <button type="button" title="copy" onclick="copyTextData('wpc_food_menu_tab-shortcode');" class="etn_copy_button wpc-btn"><span class="dashicons dashicons-category"></span></button>
                </div>
            </div>
        </div>
    </div>
   
    <div class="wpc-label-item">
        <div class="wpc-label">
            <label for="food_menu"><?php esc_html_e('Food Menu', 'wpcafe'  ); ?></label>
            <div class="wpc-desc"> <?php esc_html_e("You can generate shortcode",  'wpcafe'  );?></div>
        </div>
        <div class="wpc-meta">
            <button type="button" class="wpc-btn s-generate-btn"><?php echo esc_html__('Generate Shortcode', 'wpcafe'); ?></button>

        </div>
    </div>
</div>

<!-- Food Menu Filter by location-->
<div class="shortcode-generator-wrap">
    <div class="shortcode-generator-main-wrap">
        <div class="shortcode-generator-inner">
            <div class="shortcode-popup-close">x</div>

            <div class="wpc-row">
                <div class="wpc-col-lg-6">
                    <div class="wpc-field-wrap">
                        <h3><?php echo esc_html__('Select Template', 'wpcafe'); ?></h3>
                        <select  class="get_template wpc-setting-input">
                            <option value="wpc_food_location_menu"> <?php echo esc_html__(' Food Menu List', 'wpcafe'); ?> </option>
                        </select>
                    </div>
                </div>
                <div class="wpc-col-lg-6">
                    <div class="wpc-field-wrap">
                        <h3><?php echo esc_html__('Select Style', 'wpcafe'); ?></h3>
                        <?php  echo Wpc_Utilities::get_option_style( 1 ,'style','style-', 'Style ' ); ?>
                    </div>
                </div>
            </div>

            <div class="wpc-row">
                <div class="wpc-col-lg-6">
                    <div class="wpc-field-wrap">
                        <h3><?php echo esc_html__('Order', 'wpcafe'); ?></h3>
                        <?php Wpc_Utilities::get_order('wpc_menu_order');?>
                    </div>
                </div>
                <div class="wpc-col-lg-6">
                     <div class="wpc-field-wrap">
                        <h3><?php echo esc_html__('Product Count', 'wpcafe'); ?></h3>
                        <input type="number" data-count ="<?php echo esc_attr('no_of_product') ?>" class="post_count wpc-setting-input" value="20">
                    </div>
                </div>
            </div>

            <div class="wpc-row">
                <div class="wpc-col-lg-6">
                    <div class="wpc-field-wrap">
                        <h3><?php echo esc_html__('Show Description', 'wpcafe'); ?></h3>
                        <?php Wpc_Utilities::get_show_hide('wpc_show_desc');?> 
                    </div>
                </div>
                <div class="wpc-col-lg-6">
                     <div class="wpc-field-wrap">
                        <h3><?php echo esc_html__('Description Limit', 'wpcafe'); ?></h3>
                        <input type="number" data-count ="<?php echo esc_attr('wpc_desc_limit') ?>" class="post_count wpc-setting-input" value="20">
                    </div>
                </div>
            </div>

            <div class="wpc-row">
                <div class="wpc-col-lg-6">
                    <div class="wpc-field-wrap">
                        <h3><?php echo esc_html__('Enable title link?', 'wpcafe'); ?></h3>
                        <?php Wpc_Utilities::get_show_hide('title_link_show');?> 
                    </div>
                </div>
                <div class="wpc-col-lg-6">
                    <div class="wpc-field-wrap">
                        <h3><?php echo esc_html__('Show item status?', 'wpcafe'); ?></h3>
                        <?php Wpc_Utilities::get_show_hide('show_item_status');?> 
                    </div>
                </div>
            </div>
            <div class="wpc-row">
                <div class="wpc-col-lg-6">
                    <div class="wpc-field-wrap">
                        <h3><?php echo esc_html__('Show Product Thumbnail', 'wpcafe'); ?></h3>
                        <?php Wpc_Utilities::get_show_hide('show_thumbnail');?> 
                    </div>
                </div>
                <div class="wpc-col-lg-6">
                    <div class="wpc-field-wrap">
                        <h3><?php echo esc_html__('Show cart button', 'wpcafe'); ?></h3>
                        <?php Wpc_Utilities::get_show_hide('wpc_cart_button');?> 
                    </div>
                </div>
            </div>

            <div class="wpc-row">
                <div class="wpc-col-lg-12">
                    <div class="wpc-field-wrap">
                        <h3><?php echo esc_html__('Show delivery time ', 'wpcafe'); ?></h3>
                        <?php Wpc_Utilities::get_show_hide('wpc_delivery_time_show');?> 
                    </div>
                </div>
            </div>
        
            <button type="button"  class="wpc-btn shortcode-generate-btn"><?php echo esc_html__('Generate', 'wpcafe'); ?></button>
            
            
            <div class="attr-form-group wpc-label-item copy_shortcodes">
                <div class="wpc-meta">
                    <input type="text" readonly name="etn_event_label" id="wpc_food_location_menu-shortcode" value="[wpc_food_location_menu]" class="wpc-setting-input wpc_include_shortcode" placeholder="<?php esc_html_e('Label Text', 'wpcafe'); ?>">
                    <button type="button" title="copy" onclick="copyTextData('wpc_food_location_menu-shortcode');" class="etn_copy_button wpc-btn"><span class="dashicons dashicons-category"></span></button>
                </div>
            </div>
        </div>
    </div>
   
    <div class="wpc-label-item">
        <div class="wpc-label">
            <label for="wpc_reservation_form"><?php esc_html_e('Filter Food Menu By Locaion', 'wpcafe'  ); ?></label>
            <div class="wpc-desc"> <?php esc_html_e("You can generate shortcode",  'wpcafe'  );?></div>
        </div>
        <div class="wpc-meta">
            <button type="button" class="wpc-btn s-generate-btn"><?php echo esc_html__('Generate Shortcode', 'wpcafe'); ?></button>

        </div>
    </div>
</div>
<?php
    apply_filters('wpcafe/key_options/hook_settings',false);
?>

<?php
return;