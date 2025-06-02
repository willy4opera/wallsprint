<?php
defined( 'ABSPATH' ) || exit;

class Bzotech_Icons{
	public static $_instance = null;
	public function ekit_icons_pack(){
		add_filter( 'elementor/icons_manager/additional_tabs', [ $this, '__add_font']);
	}

	public function __add_font( $font){
        $font_new['ekiticons'] = [
			'name' => 'ekiticons',
			'label' => esc_html__( 'Element skit - Icons', 'bw-printxtore' ),
			'url' => Bzotech_Elementor::get_url_css() . 'ekiticons.css',
			'enqueue' => [Bzotech_Elementor::get_url_css() . 'ekiticons.css'],
			'prefix' => 'icon-',
			'displayPrefix' => 'icon',
			'labelIcon' => 'icon icon-home',
			'ver' => '5.9.0',
			'fetchJson' => Bzotech_Elementor::get_url_js() . '/ekiticons.js',
			'native' => true,
		];
		$font_new['lineicons'] = [
			'name' => 'lineicons',
			'label' => esc_html__( 'La - Icons', 'bw-printxtore' ),
			'url' => Bzotech_Elementor::get_url_css() . 'line-awesome.css',
			'enqueue' => [Bzotech_Elementor::get_url_css() . 'line-awesome.css'],
			'prefix' => '',
			'displayPrefix' => '',
			'labelIcon' => 'la la-home',
			'fetchJson' => Bzotech_Elementor::get_url_js() . '/lineicons.js',
			'native' => true,
			'ver' => '',
		];
		$font_new['bzotechicon'] = [
			'name' => 'bzoicon',
			'label' => esc_html__( 'BZOTECH', 'bw-printxtore' ),
			'url' => Bzotech_Elementor::get_url_css() . 'bzoicon.css',
			'enqueue' => [Bzotech_Elementor::get_url_css() . 'bzoicon.css'],
			'prefix' => '',
			'displayPrefix' => 'icon-bzo',
			'labelIcon' => 'la la-home',
			'fetchJson' => Bzotech_Elementor::get_url_js() . '/bzoicon.js',
			'native' => true,
			'ver' => '',
		];
        return  array_merge($font, $font_new);
    }
	
	

	public static function _get_instance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

}
