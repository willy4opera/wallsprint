<?php
namespace Elementor;
$name ='';
$account_id = get_option('woocommerce_myaccount_page_id');
if(empty($login_url)){
    if($account_id) $login_url = get_permalink( $account_id );
    else $login_url = wp_login_url();
}
if(is_user_logged_in()){
	$redirect_logout =  apply_filters( 'bzotech_logout_redirect',home_url('/'));
	$current_user = wp_get_current_user();
    if(!empty($current_user)){
        $name = $current_user->data->display_name;
    }
	$title_account = sprintf(wp_kses_post(__( 'Hello %s','bw-printxtore' )), $name);
	$html_a = '<a title="'.esc_attr($title_account).'" class="title-account-e" href="'.esc_url($login_url).'">';
	$html_a_icon = '<a title="'.esc_attr($title_account).'" class="item-icon-e flex-wrapper" href="'.esc_url($login_url).'">';
	$logout = '<a class="title14 text-login-logout title-account-e" href="'.wp_logout_url( $redirect_logout ) .'">'.esc_html__( 'Log out','bw-printxtore' ).'</a>';

}else{
	$title_account = $settings['account_bttext'];
	$html_a = '<a title="'.esc_attr($settings['account_bttext']).'" class="title-account-e" href="#">';
	$html_a_icon = '<a title="'.esc_attr($settings['account_bttext']).'" class="item-icon-e flex-wrapper" href="#">';
	$logout ='<a class="title14 text-login-logout title-account-e" href="#">'.esc_html__( 'Login','bw-printxtore' ).'</a>';
}
echo '<div class="button-account-e button-account-manager '.$settings['account_bt_class_css'].' ">';
	echo  apply_filters('bzotech_output_content',$html_a); ?>
		<?php if($title_account && $settings['account_bttext_pos'] == 'before-icon' && $settings['account_bttext']) echo '<span class="title-account">'.$title_account.'</span>'?>
		<?php 
		$icon_stt = '';
		if(is_user_logged_in()) $icon_stt = '_logged';
		if(!empty($settings['icon'.$icon_stt]['value'])){
			Icons_Manager::render_icon( $settings['icon'.$icon_stt], [ 'aria-hidden' => 'true' ] ); 
		}
		
		?>
		<?php if($title_account && $settings['account_bttext_pos'] == 'after-icon' && $settings['account_bttext']) echo '<span class="title-account">'.$title_account.'</span>'?>
	</a>
</div>