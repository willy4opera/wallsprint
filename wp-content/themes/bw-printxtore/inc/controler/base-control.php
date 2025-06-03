<?php
/**
 * Created by Sublime Text 3.
 * User: MBach90
 * Date: 12/08/15
 * Time: 10:20 AM
 */
if(!defined('ABSPATH')) return;

if(!class_exists('Bzotech_BaseController')){
    class Bzotech_BaseController{
         /**
         * 
         * Default Framwork Hooked
         *
         * @return void
         * 
         */
        static function _init(){
            add_filter( 'wp_title', array(__CLASS__,'_wp_title'), 10, 2 );
            add_action( 'wp', array(__CLASS__,'_setup_author') );
            add_action( 'after_setup_theme', array(__CLASS__,'_after_setup_theme') );
            add_action( 'widgets_init',array(__CLASS__,'_add_sidebars'));
            add_filter( 'bzotech_get_sidebar',array(__CLASS__,'_set_filter_sidebar'));
            add_action( 'wp_enqueue_scripts',array(__CLASS__,'_add_scripts'));
            add_action( 'admin_enqueue_scripts',array(__CLASS__,'_add_admin_scripts'));
            add_filter( 'bzotech_header_page_id',array(__CLASS__,'_header_id'));
            add_filter( 'bzotech_footer_page_id',array(__CLASS__,'_footer_id'));
            add_filter('body_class', array(__CLASS__,'bzotech_body_classes'));
            $image_down_size = bzotech_get_option('image_down_size');
            if($image_down_size == '1'){
                add_filter( 'image_downsize', 'gambit_otf_regen_thumbs_media_downsize', 10, 3 );
            }
            
            if(class_exists("woocommerce") && !is_admin()){
                add_action('woocommerce_product_query', array(__CLASS__, '_woocommerce_product_query'), 20);
            }
            add_action('after_switch_theme', array(__CLASS__,'bzotech_setup_options'));
            add_action( 'pre_get_posts', array(__CLASS__,'bzotech_custom_posts_per_page'));
            add_action( 'bzotech_before_main_content', array(__CLASS__,'bzotech_display_breadcrumb'),15);
            
            $terms = array('product_cat','product_tag','category','post_tag');
            foreach ($terms as $term_name) {
                add_action($term_name.'_add_form_fields', array(__CLASS__,'bzotech_metabox_register'), 10, 1);
                add_action($term_name.'_edit_form_fields', array(__CLASS__,'bzotech_metabox_edit'), 10, 1);    
                add_action('created_'.$term_name, array(__CLASS__,'bzotech_metadata_save'), 10, 1);    
                add_action('edited_'.$term_name, array(__CLASS__,'bzotech_metadata_save'), 10, 1);
            }
            add_action('bzotech_before_main_content', array(__CLASS__,'bzotech_append_content_before'), 10);
            
            add_action('bzotech_after_main_content', array(__CLASS__,'bzotech_append_content_after'), 10);
            
            if ( version_compare($GLOBALS['wp_version'], '5.0-beta', '>') ) {
                
                add_filter( 'use_block_editor_for_post_type', '__return_false', 100 );
            } else {
                
                add_filter( 'gutenberg_can_edit_post_type', '__return_false' );
            }
            add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
            add_filter( 'use_widgets_block_editor', '__return_false' );

            add_filter( 'get_the_archive_title', array(__CLASS__,'bzotech_remove_archive_title'));
        }
        /**
         * 
         * Hook to remove archive title 
         * 
         * @return void
         *
         * */
         static function bzotech_remove_archive_title($title) {
            if (is_category()) {
                $title = single_cat_title('', false);
            } elseif (is_tag()) {
                $title = single_tag_title('', false);
            } elseif (is_author()) {
                $title = '<span class="vcard">' . get_the_author() . '</span>';
            } elseif (is_tax()) { 
                $title = sprintf(esc_html__('%1$s','bw-printxtore'), single_term_title('', false));
            } elseif (is_post_type_archive()) {
                $title = post_type_archive_title('', false);
            }
            return $title;
        }
         /**
         * 
         * Hook to wp_title
         * 
         * @return void
         *
         * */
        static function _wp_title($title,$sep){
            return $title;
        }

        /**
         * Set up author data
         * 
         * @return void
         *
         * */
        static function _setup_author(){
            global $wp_query;

            if ( $wp_query->is_author() && isset( $wp_query->post ) ) {
                $GLOBALS['authordata'] = get_userdata( $wp_query->post->post_author );
            }
        }

        /**
         * Set up theme support, hook after_setup_theme
         * 
         * @return void
         *
         * */
        static function _after_setup_theme(){
            global $bzotech_config;
            $menus= $bzotech_config['nav_menu'];
            if(is_array($menus) and !empty($menus) ){
                register_nav_menus($menus);
            }
            add_theme_support( "title-tag" );
            add_theme_support('automatic-feed-links');
            add_theme_support('post-thumbnails');
            add_theme_support('html5',array(
                'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
            ));
            add_theme_support('post-formats',array(
                'image', 'video', 'gallery','audio','quote'
            ));
            add_theme_support('custom-header');
            add_theme_support('custom-background');
            add_theme_support( 'wc-product-gallery-slider' );
            add_theme_support( 'woocommerce', array(
                'gallery_thumbnail_image_width' => 150,
                ));
        }

        /**
         * 
         * Add default sidebar to website
         * 
         * @return void
         * 
         * */
        static function _add_sidebars(){
            global $bzotech_config;
            $sidebars = $bzotech_config['sidebars'];
            if(is_array($sidebars) and !empty($sidebars) ){
                foreach($sidebars as $value){
                    register_sidebar($value);
                }
            }
            $add_sidebars = bzotech_get_option('bzotech_add_sidebar');
            if(is_array($add_sidebars) and !empty($add_sidebars) ){
                foreach($add_sidebars as $sidebar){
                    $sidebar['title'] = trim($sidebar['title']);
                   
                    if(!empty($sidebar['title'])){
                        $id = strtolower(str_replace(' ', '_', trim($sidebar['title'])));
                        $custom_add_sidebar = array(
                                'name' => $sidebar['title'],
                                'id' => $id,
                                'description' => esc_html__( 'SideBar created by add sidebar in theme options.', 'bw-printxtore'),
                                'before_title' => '<'.$sidebar['widget_title_heading'].' class="widget-title">',
                                'after_title' => '</'.$sidebar['widget_title_heading'].'>',
                                'before_widget' => '<div id="%1$s" class="sidebar-widget widget %2$s">',
                                'after_widget'  => '</div>',
                            );
                        register_sidebar($custom_add_sidebar);
                        unset($custom_add_sidebar);
                    }
                }
            }
        }

        /**
         * 
         * Sidebar control 
         * 
         * @return array
         * 
         * */
        static function _set_filter_sidebar($sidebar){
            if((!is_front_page() && is_home()) || (is_front_page() && is_home())){
                $pos=bzotech_get_option('bzotech_sidebar_position_blog');
                $sidebar_id=bzotech_get_option('bzotech_sidebar_blog');
                $sidebar_style=bzotech_get_option('bzotech_sidebar_style_blog');
            }
            else{
                if(is_single()){
                    $pos = bzotech_get_option('bzotech_sidebar_position_post');
                    $sidebar_id = bzotech_get_option('bzotech_sidebar_post');
                    $sidebar_style=bzotech_get_option('bzotech_sidebar_style_post');
                }
                else{
                    $pos = bzotech_get_option('bzotech_sidebar_position_page');
                    $sidebar_id = bzotech_get_option('bzotech_sidebar_page');
                    $sidebar_style=bzotech_get_option('bzotech_sidebar_style_page');
                }    
            }
            if(class_exists( 'WooCommerce' )){
                if(bzotech_is_woocommerce_page()){
                    $pos = bzotech_get_option('bzotech_sidebar_position_woo');
                    $sidebar_id = bzotech_get_option('bzotech_sidebar_woo');    
                    $sidebar_style=bzotech_get_option('bzotech_sidebar_style_woo');   
                    if(is_single()){
                        $pos = bzotech_get_option('sv_sidebar_position_woo_single');
                        $sidebar_id = bzotech_get_option('sv_sidebar_woo_single');
                        $sidebar_style=bzotech_get_option('bzotech_sidebar_style_woo_single'); 
                    }
                }
            }
         
            if(is_archive() && !bzotech_is_woocommerce_page()){
                $pos = bzotech_get_option('bzotech_sidebar_position_page_archive');
                $sidebar_id = bzotech_get_option('bzotech_sidebar_page_archive');
                $sidebar_style=bzotech_get_option('bzotech_sidebar_style_archive'); 
            }
            else{
                if(!is_home()){
                    $id = bzotech_get_current_id();
                    $sidebar_pos = get_post_meta($id,'bzotech_sidebar_position',true);
                    $id_side_post = get_post_meta($id,'bzotech_select_sidebar',true);
                    $id_sidebar_style=get_post_meta( $id,'bzotech_sidebar_style',true); 
                    if(!empty($sidebar_pos)){
                        $pos = $sidebar_pos;
                        if(!empty($id_side_post)) $sidebar_id = $id_side_post;
                        if(!empty($id_sidebar_style)) $sidebar_style = $id_sidebar_style;
                    }
                }
            }
            if(is_search()) {
                $post_type = '';
                if(isset($_GET['post_type'])) $post_type = sanitize_text_field($_GET['post_type']);
                if($post_type != 'product'){
                    $pos = bzotech_get_option('bzotech_sidebar_position_page_search','right');
                    $sidebar_id = bzotech_get_option('bzotech_sidebar_page_search','blog-sidebar');  
                    $sidebar_style = bzotech_get_option('bzotech_sidebar_style_search','default');  
                }   
                         
            }
            if(isset($_GET['sidebar_pos'])) $pos = sanitize_text_field($_GET['sidebar_pos']);
            if(isset($_GET['sidebar_id'])) $sidebar_id = sanitize_text_field($_GET['sidebar_id']);
            if($sidebar_id) $sidebar['id'] = $sidebar_id;
            if($pos) $sidebar['position'] = $pos;
            if($sidebar_style) $sidebar['style'] = $sidebar_style;
            return $sidebar;
        }

        /**
         * 
         * Enqueue css and js
         * Hook to wp_enqueue_scripts
         * 
         * @return void
         *
         * */
        
        static function _add_scripts(){
            global $bzotech_config;
            $css_url_global = get_template_directory_uri() . '/assets/global/css/';
            $js_url_global = get_template_directory_uri() . '/assets/global/js/';
            // Javascript
            if ( is_singular() && comments_open()){
            wp_enqueue_script( 'comment-reply' );
            }
            if(class_exists("woocommerce")){
                wp_enqueue_script( 'wc-add-to-cart-variation' );
            }

            // Load script form wp lib
            wp_enqueue_script('jquery-masonry');
            wp_enqueue_script( 'jquery-ui-tabs');
            wp_enqueue_script( 'jquery-ui-slider');       
            wp_enqueue_script( 'jquery-ui-accordion');       
            
            //Begin enqueue script global
            wp_enqueue_script( 'slick',$js_url_global.'lib/slick.js',array('jquery'),null,true);
            wp_enqueue_script( 'swiper',$js_url_global.'lib/swiper.min.js',array('jquery'),null,true );
            wp_enqueue_script( 'jquery-elevatezoom',$js_url_global.'lib/jquery.elevatezoom.min.js',array('jquery'),null,true);
            wp_enqueue_script( 'packery-pkgd',$js_url_global.'lib/packery.pkgd.min.js',array('jquery'),null,true);
            wp_enqueue_script( 'jquery-smoothscroll',$js_url_global.'lib/SmoothScroll.js',array('jquery'),null,true);
            wp_enqueue_script( 'jquery-countdown',$js_url_global.'lib/jquery.countdown.min.js',array('jquery'),null,true);
            wp_enqueue_script( 'jquery-fancybox',$js_url_global.'lib/jquery.fancybox.min.js',array('jquery'),null,true);
            wp_enqueue_script( 'jquery-magnific-popup',$js_url_global.'lib/jquery.magnific-popup.js',array('jquery'),null,true);
            wp_enqueue_script( 'jquery-accordionslider',$js_url_global.'lib/jquery.accordionSlider.min.js',array('jquery'),null,true);
            wp_enqueue_script( 'waypoints.min',$js_url_global.'lib/waypoints.min.js',array('jquery'),null,true);
            wp_enqueue_script( 'jquery-counterup-min',$js_url_global.'lib/jquery.counterup.min.js',array('jquery'),null,true);
            wp_enqueue_script( 'modernizr',$js_url_global.'lib/modernizr.custom.min.js',array('jquery'),null,true);
            wp_enqueue_script( 'hoverdir',$js_url_global.'lib/jquery.hoverdir.min.js',array('jquery'),null,true);
            wp_enqueue_script( 'bzotech-script',$js_url_global.'script.js',array('jquery'),null,true);

            //AJAX
            wp_enqueue_script( 'bzotech-ajax', $js_url_global.'ajax.js', array( 'jquery' ),null,true);
            wp_localize_script( 'bzotech-ajax', 'ajax_process', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
            // End enqueue script global

            
            //Begin enqueue style
            $title_typo = bzotech_get_value_by_id('title_typo');
            $body_typo = bzotech_get_value_by_id('body_typo');
            $enqueue_font=[];
            if(!empty($body_typo['font-family'])) {
                $enqueue_font[] =$body_typo['font-family'];
            }
            if(!empty($title_typo['font-family'])) 
                $enqueue_font[] =$title_typo['font-family'];
            wp_enqueue_style('bzotech-google-fonts',bzotech_get_google_link($enqueue_font) );
            wp_enqueue_style('bzotech-reset',$css_url_global.'base/reset.css',array(),$bzotech_config['theme_version']);
            wp_enqueue_style('bzotech-typography',$css_url_global.'base/typography.css',array(),$bzotech_config['theme_version']);            
            wp_enqueue_style('bzotech-layout',$css_url_global.'base/layout.css',array(),$bzotech_config['theme_version']);
            wp_enqueue_style('bzotech-theme-unit-test', $css_url_global . 'base/theme-unit-test.css',array(),$bzotech_config['theme_version']);
            wp_enqueue_style('bzotech-class-style',$css_url_global.'base/class-style.css',array(),$bzotech_config['theme_version']);
            

            wp_enqueue_style('jquery-ui',$css_url_global.'lib/jquery-ui.min.css');
            wp_dequeue_style('yith-wcwl-font-awesome' );
            wp_enqueue_style('font-awesome-all',$css_url_global.'lib/font-awesome/css/all.min.css');
            wp_enqueue_style('elementor-icons-shared-0' );
            wp_enqueue_style( 'elementskit-css-icon-control',$css_url_global.'ekiticons.css' );
            wp_enqueue_style( 'bzoicon-css-icon-control',$css_url_global.'bzoicon.css' );
       
            //wp_enqueue_style('lineawesome',$css_url_global.'lib/line-awesome.min.css');

            wp_enqueue_style('magnific-popup',$css_url_global.'lib/magnific-popup.css');
            wp_enqueue_style('jquery-fancybox',$css_url_global.'lib/jquery.fancybox.min.css');
            wp_enqueue_style('accordion-slider',$css_url_global.'lib/accordion-slider.min.css');
            wp_enqueue_style('slick',$css_url_global.'lib/slick.css');            
            wp_enqueue_style('swiper',$css_url_global.'lib/swiper.css');
           
            $css_side_wide_global=glob(get_template_directory().'/assets/global/css/side-wide/*.css');
            if(!empty($css_side_wide_global) && is_array($css_side_wide_global))
                foreach($css_side_wide_global as $filename){
                    $dirname = pathinfo($filename);
                    $name =  $dirname['filename'];
                    wp_enqueue_style('bzotech-side-wide-global-'.$name,$css_url_global.'side-wide/'.$name.'.css',array(),$bzotech_config['theme_version']);
                }
            if(is_single()){
                wp_enqueue_style('bzotech-single-post',$css_url_global.'single-post.css',array(),$bzotech_config['theme_version']);
            }
            wp_enqueue_style('bzotech-single-product',$css_url_global.'single-product.css',array(),$bzotech_config['theme_version']);
            
            if(bzotech_is_woocommerce_page_inner()){
                wp_enqueue_style('bzotech-shop-inner-page',$css_url_global.'shop-inner-page.css',array(),$bzotech_config['theme_version']);
            }
            
           
            
            $css_element_global=glob(get_template_directory()."/assets/global/css/elementor/*.css");
            $plugin_dir = WP_PLUGIN_DIR . '/elementor/elementor.php';
            if ( file_exists( $plugin_dir ) && function_exists( 'get_plugin_data' )) {
                $plugin_data = get_plugin_data($plugin_dir);
                if ( version_compare($plugin_data['Version'], '3.24.0', '<') ) {
                    $css_loading = get_option('elementor_experiment-e_optimized_css_loading');
                }else{
                    $css_loading = 'inactive';
                }
                if(!empty($css_element_global) && is_array($css_element_global) && $css_loading == 'inactive' ){

                    foreach($css_element_global as $filename){
                        $dirname = pathinfo($filename);
                        if(!empty($dirname['filename'])){
                            $name =  $dirname['filename'];
                            wp_register_style('bzotech-el-'.$name,$css_url_global.'elementor/'.$name.'.css');
                        }                        
                    }
                }    
            }
            
                
            wp_enqueue_style('bzotech-theme-style',$css_url_global.'style.css',array(),$bzotech_config['theme_version']);
            if ( !is_plugin_inactive( 'woocommerce-tm-extra-product-options/tm-woo-extra-product-options.php' ))
                wp_enqueue_style('bzotech-extra-product-options',$css_url_global.'extra-product-options.css',array(),$bzotech_config['theme_version']);
            if ( !is_plugin_inactive( 'fancy-product-designer-2/fancy-product-designer.php' ) )
                wp_enqueue_style('bzotech-fancy-product-designer',$css_url_global.'fancy-product-designer.css',array(),$bzotech_config['theme_version']);
            wp_enqueue_style('bzotech-theme-custom-style',$css_url_global.'custom-style.css',array(),$bzotech_config['theme_version']);
            /*if(is_rtl())
                wp_enqueue_style('bzotech-theme-rtl',get_template_directory_uri().'/rtl.css',array(),$bzotech_config['theme_version']);*/
            // Inline css
            $custom_style = Bzotech_Template::load_view('custom_css');
           
            if(!empty($custom_style)) {
                wp_add_inline_style('bzotech-theme-style',$custom_style);
            }
            
            // Default style
            wp_enqueue_style('bzotech-theme-default',get_stylesheet_uri());
        }
        
         /**
         * 
         * Enqueue css and js in ADMIN
         * Hook to admin_enqueue_scripts
         * 
         * @return void
         *
         * */
        static function _add_admin_scripts(){
            $admin_url = get_template_directory_uri().'/assets/admin/';
            wp_enqueue_media();
            add_editor_style();   
            wp_enqueue_script('redux-js');         
            wp_enqueue_script( 'bzotech-admin-js', $admin_url . '/js/admin.js', array( 'jquery' ),null,true );
            wp_enqueue_style( 'bzotech-custom-admin',$admin_url.'css/custom.css');
        }

        
        /**
         * 
         * Get id page
         * Hook to bzotech_header_page_id
         * 
         * @return int
         *
         * */
        static function _header_id($page_id){
            if(bzotech_is_woocommerce_page()){
                $id = bzotech_get_current_id();
                $meta_value = get_post_meta($id,'bzotech_header_page',true);
                $id_woo = bzotech_get_option('bzotech_header_page_woo');
                if(empty($meta_value) && !empty($id_woo)) $page_id = $id_woo;                    
            }
            return $page_id;
        }
        
        /**
         * 
         * Get id page
         * Hook to bzotech_footer_page_id
         * 
         * @return int
         *
         * */
        static function _footer_id($page_id){
            if(bzotech_is_woocommerce_page()){
                $id = bzotech_get_current_id();
                $meta_value = get_post_meta($id,'bzotech_footer_page',true);
                $id_woo = bzotech_get_option('bzotech_footer_page_woo');
                if(empty($meta_value) && !empty($id_woo)) $page_id = $id_woo;                  
            }
            return $page_id;
        }

        /**
         * 
         * Set class name of body tag
         * Hook to body_class
         * 
         * @return array
         *
         * */
        static function bzotech_body_classes($classes){
            $page_style     = bzotech_get_value_by_id('bzotech_page_style');
            $menu_fixed     = bzotech_get_value_by_id('bzotech_menu_fixed');
            $shop_ajax      = bzotech_get_option('shop_ajax');
            $show_preload   = bzotech_get_option('show_preload');
            $style_post_single = bzotech_get_value_by_id('bzotech_style_post_detail');
            $theme_info     = wp_get_theme();
            $id             = bzotech_get_current_id();
            $session_page = bzotech_get_option('session_page');
            $header_session = get_post_meta($id,'bzotech_header_page',true);
            $add_class = get_post_meta($id,'add_class_body_page',true);
            $classes[] =  $add_class;
            $classes[] =  'bzotech-elementor-layout-shifts';
            $style_woo_single = bzotech_get_value_by_id('sv_style_woo_single');
            if(is_singular('product'))
            $classes[] =  'body-product-'.$style_woo_single;
            $sidebar = bzotech_get_sidebar();
            if(!empty($sidebar['style']))
            $classes[] = 'body-sidebar-type-'.esc_attr($sidebar['style']);
            if(is_singular('post')){
                $classes[] = 'post_detail_'.$style_post_single;
            }
            if(empty($header_session) && $session_page == '1'){ 
                $classes[] = 'header-session';
            }
            if(!empty($page_style)) $classes[] = $page_style;
            if(is_rtl()) $classes[] = 'rtl-enable';
            if($show_preload == '1') $classes[] = 'preload';
            if($shop_ajax == '1' && bzotech_is_woocommerce_page()) $classes[] = 'shop-ajax-enable';
            if(!empty($theme_info['Template'])) $theme_info = wp_get_theme($theme_info['Template']);
            $classes[]  = 'theme-ver-'.$theme_info['Version'];

            global $post;
            if(isset($post->post_content)){
                if(strpos($post->post_content, '[bzotech_shop')){
                    $classes[] = 'woocommerce';
                    if(strpos($post->post_content, 'shop_ajax="on"')) $classes[] = 'shop-ajax-enable';
                }
            }
            return $classes;
        }

        /**
         * 
         * 
         * Hook to woocommerce_product_query
         * 
         * @return $query
         *
         * */
        static function _woocommerce_product_query($query){
            if($query->get( 'post_type' ) == 'product'){
                $query->set('post__not_in', '');
            } 
        }

        /**
         * 
         * Update option after switch theme
         * Hook to after_switch_theme
         * 
         * @return void
         *
         * */
        static function bzotech_setup_options(){
            update_option( 'bzotech_woo_widgets', 'false' );
        }

        /**
         * 
         * Set number post
         * Hook to pre_get_posts
         * 
         * @return void
         *
         * */
        static function bzotech_custom_posts_per_page($query){
            if( $query->is_main_query() && ! is_admin() && $query->get( 'post_type' ) != 'product') {
                $number         = get_option('posts_per_page');
                if(isset($_GET['number'])) $number = sanitize_text_field($_GET['number']);
                $query->set( 'posts_per_page', $number );
            }
        }
       
        /**
         * 
         * Display breadcrumb default
         * Hook to bzotech_before_main_content
         * 
         * @return html
         *
         * */
        static function bzotech_display_breadcrumb(){
            if(!is_page_template('elementor-template.php'))
            echo bzotech_get_template('breadcrumb');
        }
        
        /**
         * 
         * Register metabox
         * 
         * @return void
         *
         * */
        static function bzotech_metabox_register($tag) { 
            ?>
            <div class="form-field">
                <label><?php esc_html_e('Show banner','bw-printxtore'); ?></label>
                <div class="wrap-metabox">
                    <select name="show_banner_list_post" id="show_banner_list_post">
                        <option value=""><?php esc_html_e("Choose by theme option",'bw-printxtore')?></option>
                        <option value="1"><?php esc_html_e("Show",'bw-printxtore')?></option>
                        <option value="0"><?php esc_html_e("Hidden",'bw-printxtore')?></option>
                    </select>
                </div>
            </div>
            <div class="form-field">
                <label><?php esc_html_e('Background banner','bw-printxtore'); ?></label>
                <div class="wrap-metabox">
                    <div class="live-previews"></div>
                    <a class="button button-primary sv-button-remove"> <?php esc_html_e("Remove",'bw-printxtore')?></a>
                    <a class="button button-primary sv-button-upload-id"><?php esc_html_e("Upload",'bw-printxtore')?></a>
                    <input name="bg_banner_list_post" type="hidden" class="sv-image-value" value=""></input>
                </div>
            </div>
            <div class="form-field">
                <label><?php esc_html_e('Link banner','bw-printxtore'); ?></label>
                <input name="link_banner_list_post" type="text" value="" size="40">
            </div>
            <div class="form-field">
                <label><?php esc_html_e('Content banner','bw-printxtore'); ?></label>
                <textarea name="content_banner_list_post" cols="50" rows="5"></textarea>
            </div>
            <div class="form-field">
                <label><?php esc_html_e('Append Content Before','bw-printxtore'); ?></label>
                <div class="wrap-metabox">
                    <select name="before_append" id="before_append">
                        <option value=""><?php esc_html_e("Choose page",'bw-printxtore')?></option>
                        <?php
                        $mega_pages = bzotech_list_post_type('bzotech_mega_item',false);
                        foreach ($mega_pages as $key => $value) {
                            echo '<option value="'.esc_attr($key).'">'.esc_html($value).'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-field">
                <label><?php esc_html_e('Append Content After','bw-printxtore'); ?></label>
                <div class="wrap-metabox">
                    <select name="after_append" id="after_append">
                        <option value=""><?php esc_html_e("Choose page",'bw-printxtore')?></option>
                        <?php
                        foreach ($mega_pages as $key => $value) {
                            echo '<option value="'.esc_attr($key).'">'.esc_html($value).'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
        <?php }

        /**
         * 
         * Edit metabox
         * 
         * @return void
         *
         * */
        static function bzotech_metabox_edit($tag) { ?>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label><?php esc_html_e('Show banner','bw-printxtore'); ?></label>
                </th>
                <td>            
                    <div class="wrap-metabox">
                        <select name="show_banner_list_post" id="show_banner_list_post">
                            <?php $show_banner_list_post = get_term_meta($tag->term_id, 'show_banner_list_post', true);?>
                            <option value=""><?php esc_html_e("Choose by theme option",'bw-printxtore')?></option>
                            <option <?php echo selected('1',$show_banner_list_post,false); ?> value="1"><?php esc_html_e("Show",'bw-printxtore')?></option>
                            <option <?php echo selected('0',$show_banner_list_post,false); ?> value="0"><?php esc_html_e("Hidden",'bw-printxtore')?></option>
                        </select>
                    </div>            
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label><?php esc_html_e('Background banner','bw-printxtore'); ?></label>
                </th>
                <td>            
                    <div class="wrap-metabox">
                        <div class="live-previews">
                            <?php 
                                $image = get_term_meta($tag->term_id, 'bg_banner_list_post', true);
                                echo '<img alt="'.esc_attr__('image','bw-printxtore').'" src="'.wp_get_attachment_url($image).'" />';
                            ?> 
                        </div>
                        <a class="button sv-button-remove"> <?php esc_html_e("Remove",'bw-printxtore')?></a>
                        <a class="button button-primary sv-button-upload-id"><?php esc_html_e("Upload",'bw-printxtore')?></a>
                        <input name="bg_banner_list_post" type="hidden" class="sv-image-value" value="<?php echo esc_attr($image)?>"></input>
                    </div>            
                </td>
            </tr>            
            <tr class="form-field">
                <th scope="row"><label><?php esc_html_e('Link banner','bw-printxtore'); ?></label></th>
                <td>
                    <input name="link_banner_list_post" type="text" value="<?php echo get_term_meta($tag->term_id, 'link_banner_list_post', true)?>" size="40">
                </td>
            </tr>        
            <tr class="form-field">
                <th scope="row"><label><?php esc_html_e('Content banner','bw-printxtore'); ?></label></th>
                <td>
                    <textarea name="content_banner_list_post" type="text" cols="50" rows="5"><?php echo get_term_meta($tag->term_id, 'content_banner_list_post', true)?></textarea>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label><?php esc_html_e('Append Content Before','bw-printxtore'); ?></label>
                </th>
                <td>            
                    <div class="wrap-metabox">
                        <select name="before_append" id="before_append">
                            <option value=""><?php esc_html_e("Choose page",'bw-printxtore')?></option>
                            <?php
                            $page = get_term_meta($tag->term_id, 'before_append', true);
                            $mega_pages = bzotech_list_post_type('bzotech_mega_item',false);
                            foreach ($mega_pages as $key => $value) {
                                $selected = selected($key,$page,false);
                                echo '<option '.$selected.' value="'.esc_attr($key).'">'.esc_html($value).'</option>';
                            }
                            ?>
                        </select>
                    </div>            
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label><?php esc_html_e('Append Content After','bw-printxtore'); ?></label>
                </th>
                <td>            
                    <div class="wrap-metabox">
                        <select name="after_append" id="after_append">
                            <option value=""><?php esc_html_e("Choose page",'bw-printxtore')?></option>
                            <?php
                            $page = get_term_meta($tag->term_id, 'after_append', true);
                            foreach ($mega_pages as $key => $value) {
                                $selected = selected($key,$page,false);
                                echo '<option '.$selected.' value="'.esc_attr($key).'">'.esc_html($value).'</option>';
                            }
                            ?>
                        </select>
                    </div>            
                </td>
            </tr>
        <?php }

        /**
         * 
         * Save metabox
         * 
         * @return void
         *
         * */
        static function bzotech_metadata_save($term_id){
            if (isset($_POST['show_banner_list_post'])){
                $show_banner_list_post = sanitize_text_field($_POST['show_banner_list_post']);
                update_term_meta( $term_id, 'show_banner_list_post', $show_banner_list_post);
            }
            if (isset($_POST['bg_banner_list_post'])){
                $bg_banner_list_post = sanitize_text_field($_POST['bg_banner_list_post']);
                update_term_meta( $term_id, 'bg_banner_list_post', $bg_banner_list_post);
            }
            if (isset($_POST['link_banner_list_post'])){
                $link_banner_list_post = sanitize_text_field($_POST['link_banner_list_post']);
                update_term_meta( $term_id, 'link_banner_list_post', $link_banner_list_post);
            }
            if (isset($_POST['content_banner_list_post'])){
                $content_banner_list_post = sanitize_text_field($_POST['content_banner_list_post']);
                update_term_meta( $term_id, 'content_banner_list_post', $content_banner_list_post);
            }
            if (isset($_POST['before_append'])){
                $before_append = sanitize_text_field($_POST['before_append']);
                update_term_meta( $term_id, 'before_append', $before_append);
            }
            if (isset($_POST['after_append'])){
                $after_append = sanitize_text_field($_POST['after_append']);
                update_term_meta( $term_id, 'after_append', $after_append);
            }
        }

        /**
         * 
         * Hook to bzotech_before_main_content
         * @return void
         *
         * */
        static function bzotech_append_content_before(){
            $post_id = bzotech_get_option('before_append_post');
            $post_detail_id = bzotech_get_option('before_append_post_detail');
            if(bzotech_is_woocommerce_page()){
                $page_id = bzotech_get_option('before_append_woo');
                if(is_single()) $page_id = bzotech_get_option('before_append_woo_single');
            }
            elseif(is_home() || is_archive() || is_search()) $page_id = $post_id;
            elseif(is_singular('post')) $page_id = $post_detail_id;
            else $page_id = bzotech_get_option('before_append_page');
            $id = bzotech_get_current_id();
            $meta_id = get_post_meta($id,'before_append',true);

            if(!empty($meta_id)) $page_id = $meta_id;
            if(function_exists('is_shop')) $is_shop = is_shop();
            else $is_shop = false;
                 
            if(is_archive() && !$is_shop){
                global $wp_query;
                $term = $wp_query->get_queried_object();
                if(isset($term->term_id)) $cat_id = get_term_meta($term->term_id, 'before_append', true);
                else $cat_id = '';
                if(!empty($cat_id)) $page_id = $cat_id;
            }
            $class_template= 'bzotech-'.str_replace ('.php','',get_page_template_slug($page_id));
            if(!empty($page_id)) echo '<div class="content-append-before '.$class_template.'"><div class="">'.Bzotech_Template::get_vc_pagecontent($page_id).'</div></div>';
        }

        /**
         * 
         * Hook to bzotech_after_main_content
         * @return void
         *
         * */
        static function bzotech_append_content_after(){
            $post_id = bzotech_get_option('after_append_post');
            $post_detail_id = bzotech_get_option('after_append_post_detail');
            if(bzotech_is_woocommerce_page()){
                $page_id = bzotech_get_option('after_append_woo');
                if(is_single()) $page_id = bzotech_get_option('after_append_woo_single');
            }
            elseif(is_home() || is_archive() || is_search()) $page_id = $post_id;
            elseif(is_singular('post')) $page_id = $post_detail_id;
            else $page_id = bzotech_get_option('after_append_page');
            $id = bzotech_get_current_id();
            $meta_id = get_post_meta($id,'after_append',true);
            if(!empty($meta_id)) $page_id = $meta_id;
            if(function_exists('is_shop')) $is_shop = is_shop();
            else $is_shop = false;         
            if(is_archive() && !$is_shop){
                global $wp_query;
                $term = $wp_query->get_queried_object();
                if(isset($term->term_id)) $cat_id = get_term_meta($term->term_id, 'after_append', true);
                else $cat_id = '';
                if(!empty($cat_id)) $page_id = $cat_id;
            }
            $class_template= 'bzotech-'.str_replace ('.php','',get_page_template_slug($page_id));
            if(!empty($page_id)) echo '<div class="content-append-after '.$class_template.'"><div class="">'.Bzotech_Template::get_vc_pagecontent($page_id).'</div></div>';
        }

    }

    Bzotech_BaseController::_init();
}

/**
* Load more blog
* Hook to wp_ajax_ , wp_ajax_nopriv_
* @return void
*
* */
add_action( 'wp_ajax_load_more_post', 'bzotech_load_more_post' );
add_action( 'wp_ajax_nopriv_load_more_post', 'bzotech_load_more_post' );
if(!function_exists('bzotech_load_more_post')){
    function bzotech_load_more_post() {
        $ajax_security = bzotech_get_option('ajax_security');
        if($ajax_security == true){
            check_ajax_referer( 'load-more-post-ajax-nonce', 'security');
        }
        
        $paged = sanitize_text_field($_POST['paged']);
        $load_data = sanitize_text_field($_POST['load_data']);
        $load_data = str_replace('\\"', '"', $load_data);
        $load_data = str_replace('\"', '"', $load_data);
        $load_data = str_replace('\/', '/', $load_data);
        $load_data = json_decode($load_data,true);
        extract($load_data);
        extract($attr);
        $args['posts_per_page'] = $number;
        $args['paged'] = $paged + 1;
        $query = new WP_Query($args);
        $count = 1;
        $count_query = $query->post_count;
        $slug = $item_style;
        if($view == 'grid' && $type_active == 'list'){
            $view = $type_active;
            $slug = $item_list_style;
        }
        if($query->have_posts()) {
            while($query->have_posts()) {
                $query->the_post();
                bzotech_get_template_post($view.'/'.$view,$slug,$attr,true);
                $count++;
            }
        }
        wp_reset_postdata();
        die();
    }
}


