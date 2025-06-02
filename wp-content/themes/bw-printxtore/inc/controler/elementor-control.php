<?php
defined( 'ABSPATH' ) || exit;

class Bzotech_Elementor{

    public static function get_url_css(){
        return get_template_directory_uri() . '/assets/global/css/';
    }

    public static function get_url_js(){
        return get_template_directory_uri() . '/assets/global/js/';
    }

    public static function get_dir(){
        return get_template_directory() . '/inc/class/';
    }
  
    public function __construct() {

        $this->include_files();
        Bzotech_Icons::_get_instance()->ekit_icons_pack();
        add_action('elementor/controls/controls_registered', array( $this, 'icon' ), 11 );
        add_action('elementor/controls/controls_registered', array( $this, 'image_choose' ), 11 );
        add_action('elementor/controls/controls_registered', array( $this, 'ajax_select2' ), 11 );
        add_action( 'elementor/elements/categories_registered',array($this,'bzotech_add_elementor_categories'));
    }
    public function bzotech_add_elementor_categories( $elements_manager ) {
        $category_prefix = 'aqb-';
        $elements_manager->add_category(
           $category_prefix.'htelement-category',
            [
                'title' => esc_html__( 'Bzotech Elementor Global', 'bw-printxtore' ),
                'icon' => 'fa fa-plug',
            ]
        );
        $reorder_cats = function() use($category_prefix){
                uksort($this->categories, function($keyOne, $keyTwo) use($category_prefix){
                    if(substr($keyOne, 0, 4) == $category_prefix){
                        return -1;
                    }
                    if(substr($keyTwo, 0, 4) == $category_prefix){
                        return 1;
                    }
                    return 0;
                });

            };
            $reorder_cats->call($elements_manager);

    }
    
    private function include_files(){

        include_once self::get_dir() . 'image-choose.php';

        include_once self::get_dir() . 'icon.php';
        include_once self::get_dir() . 'icons.php';
        include_once self::get_dir() . 'ajax-select2.php';
        include_once self::get_dir() . 'ajax-select2-api.php';

    }

    public function icon( $controls_manager ) {
        $controls_manager->unregister_control( $controls_manager::ICON );
        $controls_manager->register_control( $controls_manager::ICON, new Bzotech_Icon());
    }

    public function image_choose( $controls_manager ) {
        $controls_manager->register_control('imagechoose', new Bzotech_Image_Choose());
    }

    public function ajax_select2( $controls_manager ) {
        $controls_manager->register_control('ajaxselect2', new Bzotech_Ajax_Select2());
    }
   
    
}
new Bzotech_Elementor();

