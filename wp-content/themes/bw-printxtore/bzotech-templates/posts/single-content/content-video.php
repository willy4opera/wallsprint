<?php
$data = ''; global $post;
$blog_single_title_check = bzotech_get_option('post_single_title','1');
if (get_post_meta(get_the_ID(), 'format_media', true)) {
    $media_url = get_post_meta(get_the_ID(), 'format_media', true); ?>
    <div class="single-post-media-format">
    <?php     
   
    $ext_url = pathinfo($media_url, PATHINFO_EXTENSION);
    echo '<div class="format-video">';
    if($ext_url == 'mp4'){
        echo '<video class="video-click" controls ><source src="'.esc_url($media_url).'" type="video/mp4"></video>';
    }else
        echo bzotech_remove_w3c(wp_oembed_get($media_url, array( 'autoplay' => 1 )));
    echo '</div>';
    ?>
    </div>
    <?php
}
