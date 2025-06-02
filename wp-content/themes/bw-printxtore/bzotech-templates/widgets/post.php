<?php
/**
 * Created by PhpStorm.
 * User: mai100it
 * Date: 26/02/2018
 * Time: 10:40 SA
 */
extract($instance);

$image_size = bzotech_get_size_image('150x120',$image_size); 
$image_mobile_size = ['500','330'];
?>
<div class="wg-post-list">
    <?php
    if($post_query->have_posts()) { ?>
        <div class="elbzotech-wrapper-slider display-swiper-navi-style1 ">
            <div class="elbzotech-swiper-slider swiper-container slider-wrap" data-items-custom="" data-items="1" data-space="30" 
         data-column="<?php echo esc_attr($number_row)?>" data-auto="" data-center="" data-loop="" data-speed="" data-navigation="" data-pagination=""> 
                <div class="swiper-wrapper"> 
                <?php
                while($post_query->have_posts()) {
                    $post_query->the_post(); ?>
                        <div class="swiper-slide">
                            <div class="item-post-wg flex-wrapper">
                                <div class="post-thumb banner-advs zoom-image">
                                    <a href="<?php echo esc_url(get_the_permalink()); ?>" class="adv-thumb-link">
                                        <?php
                                        $id_thumb = get_post_thumbnail_id(get_the_ID());
                                        if(has_post_thumbnail()) echo bzotech_get_picture_html(['image'=>$id_thumb,'media'=>1200,'image_size'=>$image_size,'image_mobile_size'=>$image_mobile_size]); 
                                        ?>
                                    </a>
                                </div>

                                <div class="post-info">                                    
                                    <div class="the-date title14">
                                        <?php echo get_the_date() ?>
                                    </div>
                                    <?php the_title('<h3 class="title14 w-p font-medium"><a class="line-height24" href="'.esc_url(get_the_permalink()).'">','</a></h3>')?>
                                </div>
                            </div>
                        </div>
                <?php }
                wp_reset_postdata(); ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>