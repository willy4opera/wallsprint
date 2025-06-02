<?php
if(empty($size)) $size = 'full';
global $post; 
if (has_post_thumbnail()) { ?>
    <div class="single-post-media-format">
        <div class="format-standard banner-advs">
            <?php echo get_the_post_thumbnail(get_the_ID(),$size); ?>
        </div>
    </div>
    <?php
}