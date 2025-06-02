<?php
global $post;

if(empty($size_list)) $size_list = 'full'; //set value default of image size
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
    $item_meta_select_list = ['author','date','comments'];
}
?>
<div class="bzotech-col-md-12">
    <div class="item-post item-list-post-default">
        <?php if($item_thumbnail_list == 'yes' && has_post_thumbnail()):?>
            <div class="post-thumb <?php echo esc_attr($thumbnail_hover_animation)?>">
                <a href="<?php echo esc_url(get_the_permalink()) ?>">
                    <?php echo get_the_post_thumbnail(get_the_ID(),$size_list);?>
                </a>
            </div>
        <?php endif;?>
            <div class="post-info">
                <?php if($item_meta_list == 'yes') bzotech_display_metabox('detail-post',['cats'],'','style-item-meta__cats');?>
                
                <?php if($item_title_list == 'yes'):?><h3 class="title34 post-title font-regular color-title font-title"><a href="<?php echo esc_url(get_the_permalink()) ?>"><?php the_title()?> <?php echo (is_sticky()) ? '<i class="sticky-icon las la-star"></i>':''?></a></h3><?php endif?>
                <?php if($item_meta_list == 'yes') bzotech_display_metabox('detail-post',$item_meta_select_list,'','meta-post-style1');?>
                <?php if($item_excerpt_list == 'yes') echo '<p class="desc color-title">'.get_the_excerpt().'</p>';?>
                
                <?php if($item_button_list == 'yes'):?>
                    <div class="readmore-wrap">
                        <a href="<?php echo esc_url(get_the_permalink()) ?>" class="elbzotech-bt-default">
                            <?php if($button_icon_pos == 'before-text' && $button_icon) echo '<i class="'.$button_icon['value'].'"></i>';?>
                            <?php echo apply_filters('bzotech_output_content',$button_text); ?>
                            <?php if($button_icon_pos == 'after-text' && $button_icon) echo '<i class="'.$button_icon['value'].'"></i>';?>                    
                        </a>
                    </div>
                <?php endif?>
            </div>
    </div>
</div>