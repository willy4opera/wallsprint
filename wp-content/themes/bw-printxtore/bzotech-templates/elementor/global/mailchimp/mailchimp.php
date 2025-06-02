<?php
namespace Elementor;
extract($settings);

?>
<div class="sv-mailchimp-form elbzotech-mailchimp-wrap elbzotech-mailchimp-global-<?php echo esc_attr($settings['style'].' '.$settings['align_form'])?>" data-placeholder="<?php echo esc_attr($settings['placeholder']);?>" data-submit="<?php echo esc_attr($settings['mailchimp_bttext']);?>" data-icon="<?php echo esc_attr($icon_mailchimp);?>" data-textpos="<?php echo esc_attr($settings['mailchimp_bttext_pos'])?>">
	<?php echo apply_filters('bzotech_mailchimp_form',do_shortcode('[mc4wp_form id="'.$settings['form_id'].'"]'));?>
	<div class="hidden"><?php Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] );?></div>
</div>