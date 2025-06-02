<?php
$data = '';
global $post;
$gallery = get_post_meta(get_the_ID(), 'format_gallery', true);
if (!empty($gallery)){
    $array = explode(',', $gallery);
    if(is_array($array) && !empty($array)){?>
        <div class="single-post-media-format">
            <div class="format-gallery">
                <div class="elbzotech-wrapper-slider display-swiper-navi-style1">
                    <div class="elbzotech-swiper-slider swiper-container slider-wrap" data-items="2" data-space="10" data-speed="6000" data-navigation="style1" data-center="yes" data-loop="yes">
                        <div class="swiper-wrapper">
                            <?php
                            foreach ($array as $key => $item) {
                                                $thumbnail_url = wp_get_attachment_url($item);
                            ?>
                            <div class="swiper-slide">
                                <?php echo '<img src="' . esc_url($thumbnail_url) . '" alt="'.esc_attr__("Image slider",'bw-printxtore').'">'?>
                            </div>
                            <?php
                            
                            }
                            ?>
                        </div>
                    </div>
                    <div class="bzotech-swiper-navi">
                        <div class="swiper-button-nav swiper-button-next"><i class="las la-angle-right"></i></div>
                        <div class="swiper-button-nav swiper-button-prev"><i class="las la-angle-left"></i></div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    } 
}