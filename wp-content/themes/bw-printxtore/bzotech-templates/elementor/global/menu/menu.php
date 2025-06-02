<?php
if($settings['nav_menu'] != '' && wp_get_nav_menu_items($settings['nav_menu']) !== false){
			
	$logo_mobile = $close_bt ='';
	if(isset($settings['bzotech_nav_menu_logo']['url']) && !empty($settings['bzotech_nav_menu_logo']['url'])){
		$logo_mobile = 	'<a class="mobile-logo" href="'.esc_url(home_url('/')).'">
							<img src="'.esc_url($settings['bzotech_nav_menu_logo']['url']).'" alt="'.esc_attr__("logo mobile",'bw-printxtore').'" >
						</a>';
	}

	if($settings['main_menu_style'] =="icon"){
		$menu_class='bzotech-scrollbar';
		$menu_icon_title_text = $menu_mobile_style='';

		if(!empty($settings['menu_icon_title_text'])) $menu_icon_title_text = '<span class="e-toggle-style-icon-title">'.$settings['menu_icon_title_text'].'</span>';
		$position_content = 'position_content-'.$settings['menu_icon_position_content'];
		$icon_html = '<div class="bzotech-nav-identity-panel toggler-icon e-toggle-style-flex flex-wrapper">					
							<span class="bzotech-menu-toggler title24 e-toggle-style-icon">
							<i class="la la-reorder"></i>
							</span>
							'.$menu_icon_title_text.'
						</div>';
		$close_bt = '<div class="bzotech-nav-identity-panel panel-inner">
							'.$logo_mobile.'
							<div class="close-menu">
								<i class="las la-times"></i>
							</div>
						</div>';
	}else{
		$menu_mobile_style = 'menu_mobile_style-'.$settings['menu_mobile_style'];
		$position_content = '';
		$icon_html='<a href="#" class="toggle-mobile-menu"><i class="white la la-reorder"></i></a>';
		if($settings['menu_mobile_style'] !== '')
		$close_bt = '<div class="close-menu-not-style-icon"> <i class="las la-times"></i> </div>';
	}
	

	echo '<div class="bzotech-menu-global-container '.$menu_mobile_style.' '.$settings['style_tab_submenu_item_arrow'].' menu-global-style-'.$settings['main_menu_style'].' '.$settings['style_effect_hover'].' '.$position_content.'" data-megamenu-maxwidth="'.$settings['megamenu_max_width'].'">
				'.$icon_html.'
				<div class="bzotech-menu-inner">';
					wp_nav_menu([
						'items_wrap'      => $close_bt.'<ul id="%1$s" class="%2$s">%3$s</ul>',
						'container'       => false,
						'menu_id'         => '',
						'menu'         	  => $settings['nav_menu'],
						'menu_class'      => 'bzotech-navbar-nav menu-sticky-'.$settings['menu_sticky'],
						'depth'           => 4,
						'echo'            => true,
						'fallback_cb'     => 'wp_page_menu',
						'walker'          => new Bzotech_Walker_Nav_Menu(),
						'theme_location'    => 'primary',
					]);
	echo 		'</div>
			</div>';
}
