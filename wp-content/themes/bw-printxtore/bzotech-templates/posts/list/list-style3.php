<?php
global $post;

if(empty($size_list)) $size_list = array(550,340); //set value default of image size
if(empty($item_thumbnail_list)){
    $item_thumbnail_list = 'yes';
}
if(empty($item_title_list)){
    $item_title_list = 'yes';
}
if(empty($item_excerpt_list)){
    $item_excerpt_list = 'yes';
}
if(empty($item_button_list)){
    $item_button_list = 'yes';
}
if(empty($item_meta_list)) {
    $item_meta_list = 'yes';
}
if(empty($item_meta_select_list)) {
    $item_meta_select_list = ['author','date'];
} 
?>
<div class="bzotech-col-md-12">
    <div class="item-post item-list-post-style3 flex-wrapper align_items-center">
        <?php if($item_thumbnail_list == 'yes' && has_post_thumbnail()):?>
            <div class="post-thumb zoom-image">
                <a class="adv-thumb-link elementor-animation-<?php echo esc_attr($thumbnail_hover_animation)?>" href="<?php echo esc_url(get_the_permalink()) ?>">
                    <?php echo get_the_post_thumbnail(get_the_ID(),$size_list);?>
                </a>
            </div>
        <?php endif;?>
            <div class="post-info">
                <div class="post-info2">
                   <?php if($item_meta_list == 'yes') bzotech_display_metabox('detail-post',$item_meta_select_list,'','meta-post-style1');?>
                    <?php if($item_title_list == 'yes'):?><h3 class="title16 post-title"><a class=" color-title" href="<?php echo esc_url(get_the_permalink()) ?>"><?php the_title()?> <?php echo (is_sticky()) ? '<i class="sticky-icon las la-star"></i>':''?></a></h3><?php endif?>
                    
                    <?php if($item_excerpt_list == 'yes') echo '<p class="desc">'.bzotech_substr(get_the_excerpt(),0,$excerpt_list).'</p>';?>

                    <?php if($item_button_list == 'yes'):?>
                        <div class="readmore-wrap">
                            <a href="<?php echo esc_url(get_the_permalink()) ?>" class="elbzotech-bt-style4">
                                <?php if($button_icon_pos == 'before-text' && $button_icon) echo '<i class="'.$button_icon['value'].'"></i>';?>
                                <?php echo apply_filters('bzotech_output_content',$button_text); ?>
                                <?php if($button_icon_pos == 'after-text' && $button_icon) echo '<i class="'.$button_icon['value'].'"></i>';?>                    
                            </a>
                        </div>
                    <?php endif?>
                </div>
            </div>
    </div>
</div>