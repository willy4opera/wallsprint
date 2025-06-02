<?php
$show_wishlist = bzotech_get_option('show_wishlist_notification');
if(class_exists('YITH_WCWL_Init') && $show_wishlist == '1'){            
$url = YITH_WCWL()->get_wishlist_url();
?>
<div class="wishlist-mask">
    <div class="wishlist-popup">
        <span class="popup-icon"><i class="fa fa-bullhorn" aria-hidden="true"></i></span>
        <p class="wishlist-alert">"<span class="wishlist-title"></span> <?php esc_html_e("was added to wishlist",'bw-printxtore')?></p>
        <div class="wishlist-button">
            <a href="#" class="wishlist-close"><?php esc_html_e("Close",'bw-printxtore')?> (<span class="wishlist-countdown">3</span>)</a>
            <a href="<?php echo esc_url($url)?>"><?php esc_html_e("View page",'bw-printxtore')?></a>
        </div>
    </div>
</div>
<?php
}