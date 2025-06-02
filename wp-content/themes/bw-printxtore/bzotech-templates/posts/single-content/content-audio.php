<?php
$data = '';
if (get_post_meta(get_the_ID(), 'format_media', true)) {
    $media_url = get_post_meta(get_the_ID(), 'format_media', true);
    $data .= '<div class="single-post-media-format"><div class="format-audio">' . bzotech_remove_w3c(wp_oembed_get($media_url, array('height' => '176'))) . '</div></div>';
}
if(!empty($data)) echo apply_filters('bzotech_output_content',$data);