<?php
namespace Elementor; 
$check = true;
if(isset($_SESSION['dont_show_popup'])) $check = !$_SESSION['dont_show_popup'];
if($check):
	extract($settings);
	$wdata->add_render_attribute( 'wrapper', 'class', 'fadeInDown elbzotech-mailchimp-wrap sv-mailchimp-form  elbzotech-mailchimp-global-'.$style.' '.$align_form);
	$wdata->add_render_attribute( 'wrapper', 'data-placeholder', $placeholder);
	$wdata->add_render_attribute( 'wrapper', 'data-submit', $mailchimp_bttext);
	$wdata->add_render_attribute( 'wrapper', 'data-icon', $icon_mailchimp);
	$wdata->add_render_attribute( 'wrapper', 'data-textpos', $mailchimp_bttext_pos);
	?>
	<div <?php echo ''.$wdata->get_render_attribute_string('wrapper');?>>
		<div class="elbzotech-mailchimp-form content-popup-mailchimp flex-wrapper align_items-center">
			<i class="la la-close elbzotech-close-popup"></i>
			<div class="zoom-image image-mailchimp"><div class="adv-thumb-link"><?php echo Group_Control_Image_Size::get_attachment_image_html( $settings);?></div></div>
			<div class="info-mailchimp bzotech-scrollbar">
				<?php if(!empty($title)) echo '<h3 class="title title30 font-title color-title font-medium">'.$title.'</h3>'; ?>
				<?php if(!empty($desc)) echo '<div class="desc title16">'.$desc.'</div>'; ?>
				<?php echo apply_filters('bzotech_mailchimp_form',do_shortcode('[mc4wp_form id="'.$settings['form_id'].'"]'));?>
				 
				<?php 

				if(!empty($list_social)){
					echo '<div class="flex-wrapper align_items-center justify_content-center">';
					foreach (  $list_social as $key => $item ) {
						$wdata->add_render_attribute( 'social-link'.$key, 'class', 'item-social title36');
						if($item['link']['is_external']) $wdata->add_render_attribute( 'social-link'.$key, 'target', "_blank");
						if($item['link']['nofollow']) $wdata->add_render_attribute( 'social-link'.$key, 'rel', "nofollow");
						if($item['link']['url']) $wdata->add_render_attribute( 'social-link'.$key, 'href', $item['link']['url']);
						
						if(!empty($item['icon']['value'])) echo'<a '.apply_filters('bzotech_output_content', $wdata->get_render_attribute_string('social-link'.$key)).'><i class="'.$item['icon']['value'].'"></i></a>';
					}
					echo '</div>';
				}
				?>
				<input type="hidden" name="mailchimp-ajax-nonce" class="mailchimp-ajax-nonce" value="<?php echo wp_create_nonce( 'mailchimp-ajax-nonce' ); ?>" />
				<div class="text-center dont-show"><input type="checkbox" id="close-newsletter"> <label for="close-newsletter"><?php esc_html_e("Don’t show this pop-up again",'bw-printxtore')?></label></div>
			</div>
		</div>
	</div>
<?php endif;