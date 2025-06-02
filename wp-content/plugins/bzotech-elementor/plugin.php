<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Main Plugin Class
 *
 * Register new elementor widget.
 *
 * @since 1.0.0
 */

class Plugin {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
		$this->add_actions();
	}

	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function add_actions() {
		add_action( 'wp_enqueue_scripts',[ $this , 'nth_add_scripts' ] );
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'on_widgets_registered' ] );

		add_action( 'elementor/frontend/after_register_scripts', function() {
			wp_register_script( 'bzotech-elementor', plugins_url( '/assets/js/hello-world.js', ELEMENTOR_HELLO_WORLD__FILE__ ), [ 'jquery' ], false, true );
		} );
	}

	public function nth_add_scripts() {
		wp_register_style( 'owl2-carousel', plugins_url( '/assets/css/owl.carousel.min.css', ELEMENTOR_HELLO_WORLD__FILE__ ));
		wp_register_style( 'owl2-carousel-theme', plugins_url( '/assets/css/owl.theme.default.css', ELEMENTOR_HELLO_WORLD__FILE__ ));
		wp_register_script( 'owl2-carousel-script', plugins_url( '/assets/js/owl.carousel.min.js', ELEMENTOR_HELLO_WORLD__FILE__ ), [ 'jquery' ], false, true );
		
		//wp_enqueue_style( 'owl2-carousel' );
		// wp_enqueue_style( 'owl2-carousel-theme' );
		//wp_enqueue_script( 'owl2-carousel-script' );
	}

	/**
	 * On Widgets Registered
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function on_widgets_registered() {
		$this->includes();
	}

	/**
	 * Includes
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function includes() {	
		global $bzotech_demo;
		$files=glob(__DIR__."/widgets/*.php");
		$template_path = get_template_directory();
        $stylesheet_path = get_stylesheet_directory();
		$files_global=glob($template_path."/inc/bzotech-elementor/global/*.php");
		$files_demo=glob($template_path."/inc/bzotech-elementor/demo".$bzotech_demo."/*.php");
		
		$files = array_merge($files,$files_global);
		$names = [];
        // Auto load all file
        if(!empty($files)){
            foreach ($files as $filename)
            {
            	$dirname = pathinfo($filename);
            	$name =  $dirname['filename'];
            	if(!in_array($name, $names));{
            		$names[] = $name;
	            	$child_path = $stylesheet_path.'/inc/bzotech-elementor/global/'.$name.'.php';
	            	$theme_path = $template_path.'/inc/bzotech-elementor/global/'.$name.'.php';
	            	if( $template_path != $stylesheet_path && is_file($child_path) ) require $child_path;
	            	elseif(is_file($theme_path)) require $theme_path;
	                else require $filename;
			        $class_name	 = str_replace('-', '_', $name);
			        $class_name = 'Elementor\\'.$class_name;
	                if(class_exists($class_name)) \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new $class_name() );
            		}
            }
        }
        if(!empty($files_demo)){
            foreach ($files_demo as $filename)
            {
            	$dirname = pathinfo($filename);
            	$name =  $dirname['filename'];
            	if(!in_array($name, $names));{
            		$names[] = $name;
	            	$child_path = $stylesheet_path.'/inc/bzotech-elementor/demo'.$bzotech_demo.'/'.$name.'.php';
	            	$theme_path = $template_path.'/inc/bzotech-elementor/demo'.$bzotech_demo.'/'.$name.'.php';
	            	if( $template_path != $stylesheet_path && is_file($child_path) ) require $child_path;
	            	elseif(is_file($theme_path)) require $theme_path;
	                else require $filename;
			        $class_name	 = str_replace('-', '_', $name);
			        $class_name = 'Elementor\\'.$class_name;
	                if(class_exists($class_name)) \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new $class_name() );
            		}
            }
        }
	}
}

new Plugin();
