<?php  do_action('wpc_before_minicart'); ?>

    <ul class="wpc_cart_block">

    <a href="#" class="wpc_cart_icon">
    <div class="wpc-cart-message"><?php echo esc_html__('Product has been added', 'wpcafe'); ?></div>

            <i class="<?php echo esc_attr($wpc_cart_icon); ?>"></i>
            <sup class="basket-item-count" style="display: inline-block;">
                <span class="cart-items-count count" id="wpc-mini-cart-count">
                </span>
            </sup>
        </a>
        <li class="wpc-menu-mini-cart wpc_background_color">
            <div class="widget_shopping_cart_content">
                <?php
                is_object(WC()->cart) ? woocommerce_mini_cart() : '';
                ?>
            </div>
        </li>
    </ul>

<?php do_action('wpc_after_minicart');?>