<?php
namespace Elementor;
extract($settings);

if($style == 'image'){ 
	?>
	<div class="elbzotech-logo-global-style-image">
		<a href="<?php echo esc_url(home_url('/'));?>">
		<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings );?>
		</a>
	</div>
	<?php
}else{
	$wdata->add_render_attribute( 'title', 'class', 'logo-text' );
	if(empty($title)) $title = get_bloginfo('name', 'display');
	$title_html = sprintf( '<%1$s %2$s>%3$s</%1$s>', $header_size, $wdata->get_render_attribute_string( 'title' ), $title );
 	?>
	<div class="elbzotech-logo-global-style-text">
		<a class="font-title title20 font-regular " href="<?php echo esc_url(home_url('/'));?>">
			<?php echo ''.$title_html.'';?>
		</a>
	</div>
	<?php
}
?>
