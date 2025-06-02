
<?php
// $column set column in grid style
// $item_wrap set attribute in wrap div
// $item_inner set attribute in wrap inner div
// $item_thumbnail on/off thumbnail yes or empty
// $item_meta on/off meta yes or empty
// $item_title on/off title yes or empty
// $item_excerpt on/off excerpt yes or empty
// $item_button on/off button yes or empty
if(empty($size)|| $size == 'custom') $size = array(500,700);
if(is_array($size)) $size = bzotech_size_random($size);
if(empty($item_thumbnail)){
    $item_thumbnail = 'yes';
}
if(empty($item_title)){
    $item_title = 'yes';
}
if(empty($item_excerpt)){
    $item_excerpt = 'no';
}
if(empty($item_button)){
    $item_button = 'no';
}
if(empty($item_meta)) {
    $item_meta = 'yes';
} 
if(empty($item_meta_select)) {
    $item_meta_select = ['date','comments'];
}
$thumbnail_animation_default = '';
if(empty($thumbnail_hover_animation)) {
    $thumbnail_animation_default = 'zoom-image overlay-image';
}
$thumbnail_default = bzotech_get_option('bzotech_thumbnail_default');
?>

<?php echo '<div '.$item_wrap.'>';?>
    <?php echo '<div '.$item_inner.'>';?>
        <?php  if($item_thumbnail == 'yes' && (has_post_thumbnail() || !empty($thumbnail_default["id"]))): ?>
            <div class="post-thumb <?php echo esc_attr($thumbnail_animation_default)?>">
                <a href="<?php echo esc_url(get_the_permalink()) ?>" class="adv-thumb-link elementor-animation-<?php echo esc_attr($thumbnail_hover_animation)?>">
                    <?php if(has_post_thumbnail()) echo get_the_post_thumbnail(get_the_ID(),$size);
                    else echo wp_get_attachment_image($thumbnail_default["id"],$size); ?>
                </a>
                <div class="label-cat">                         
                    <?php echo get_the_category_list();?>
                </div>                
            </div>            
             <div class="post-info">
                <div class="post-info2 bg-white">
                    <?php if($item_meta == 'yes') bzotech_display_metabox('detail-post',$item_meta_select,'','meta-post-style4');?>
                    <?php if($item_title == 'yes'):?><h3 class="title20 post-title font-title font-semibold"><a class="color-title2 text-capitalize" href="<?php echo esc_url(get_the_permalink()) ?>"><?php the_title()?></a></h3><?php endif ?>
                </div>
            </div>
        <?php endif; ?>
        
    </div>
</div>