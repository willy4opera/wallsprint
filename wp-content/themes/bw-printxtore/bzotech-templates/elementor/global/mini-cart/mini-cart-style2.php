<?php
namespace Elementor;
?>
<a class="mini-cart-link" href="<?php echo esc_url(wc_get_cart_url())?>">
    <span class="mini-cart-icon">
    	<?php if(!empty($settings['icon']['value'])) Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] ); ?>
    	
    </span>
    <span class="mini-cart-text">                    
        <?php if($settings['text']) echo '<span class="mini-cart-text-bt">'.$settings['text'].'</span>';?>
        <span class="mini-cart-number-e title14"><span class="mini-cart-number set-cart-number">0</span><span><?php echo esc_html__(' items','bw-printxtore')?></span></span>
        <?php if($settings['show_price'] == 'yes'): ?>
            <span class="mini-cart-total-price set-cart-price">
            	<?php 
            	if(\WC()->cart) echo \WC()->cart->get_cart_total();
            	else echo wc_price(0);
            	?>                    		
            </span>
        <?php endif?>
    </span>
</a>