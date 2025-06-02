<?php 
	$url_bg = wp_get_attachment_url($bg_banner,'full');
?>
<div class="banner-list-post" <?php echo bzotech_add_html_attr('background-image:url('.$url_bg.')'); ?>>
	<div class="bzotech-container">
		<div class="banner-list-post__info">
			<?php if(!empty($content_banner)) echo '<div class="banner-list-post__desc">'.do_shortcode($content_banner).'</div>'; ?>
			<?php if(!empty($link_banner)) echo '<a class= "elbzotech-bt-style4" href="'.esc_url($link_banner).'">'.esc_html__('View More','bw-printxtore').'</a>'; ?>
		</div>
	</div>
</div>