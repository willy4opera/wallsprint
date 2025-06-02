
<div class="share-popup-content-open"><i class="icon-bzo icon-bzo-close"></i><div class="share-popup-content-js"></div></div>
        <?php if(function_exists('wc_get_cart_url')){?>
            <div class="bzo-ajaxcart-report hide">
                <div class="">
                    <div class="bzo-ajaxcart-messages msg-success"><?php echo esc_html__('Product was successfully added to your cart!','bw-printxtore')?></div>
                    <div class="bzo-ajaxcart-actions"><a class="close btn-popup-cart"  href="#"><?php echo esc_html__('Continue ','bw-printxtore')?>(<span class="js-time-cout-dow"></span>)</a> <a class="btn-popup-cart btn-popup-view" href="<?php echo wc_get_cart_url()?>" title="<?php echo esc_attr__('View Cart','bw-printxtore')?>"><?php echo esc_html__('View Cart','bw-printxtore')?></a> </div>
                </div>
            </div>
        <?php }?>
<?php
$after_append_footer = bzotech_get_option('after_append_footer');
if(!empty($after_append_footer)){
    echo '<div class="after-append-footer">';
    echo Bzotech_Template::get_vc_pagecontent($after_append_footer,true);
    echo '</div>';
}