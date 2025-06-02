<?php
namespace Elementor;
use WP_Query;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Hello World
 *
 * Elementor widget for hello world.
 *
 * @since 1.0.0
 */
class Bzotech_Products_Global extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'bzotech-products-global';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Products list (Global)', 'bw-printxtore' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-ticker';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'aqb-htelement-category' ];
	}

	/**
	 * Retrieve the list of scripts the widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [ 'hello-world' ];
	}


	public function get_style_depends() {
		return [ 'bzotech-el-products' ];
	}
	public function get_widget_css_config( $widget_name ) { 
	    $file_content_css = get_template_directory() . '/assets/global/css/elementor/products.css';
	    if ( is_file( $file_content_css ) ) {
	        $file_content_css_content = file_get_contents( $file_content_css );
	        echo bzotech_add_inline_style_widget( $file_content_css_content, true );
	    }
	    $direction = is_rtl() ? '-rtl' : '';
	    $has_custom_breakpoints = $this->is_custom_breakpoints_widget();
	    $file_name = 'widget-' . $widget_name . $direction . '.min.css';
	    $file_url = Plugin::$instance->frontend->get_frontend_file_url( $file_name, $has_custom_breakpoints );
	    $file_path = Plugin::$instance->frontend->get_frontend_file_path( $file_name, $has_custom_breakpoints );
	    return [
	        'key' => $widget_name,
	        'version' => ELEMENTOR_VERSION,
	        'file_path' => $file_path,
	        'data' => [
	            'file_url' => $file_url,
	        ],
	    ];
	}
	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		if(!class_exists('woocommerce')) return;
		$slider_items_widescreen =$slider_items_laptop = $slider_items_tablet_extra =$slider_items_mobile_extra =$slider_items_mobile =$slider_space_widescreen =$slider_space_laptop =$slider_space_tablet_extra =$slider_space_tablet =$slider_space_mobile_extra= $slider_space_mobile ='';
		$column_widescreen = $column_laptop =$slider_items_tablet =$column_tablet_extra =$column_tablet =$column_mobile_extra =$column_mobile ='';
		$settings = $this->get_settings();
		extract($settings);
		$get_type =$view = str_replace('elbzotech-product-', '', $display);
		if(isset($column['size']) && $get_type == 'grid') {
			if($column['size']<4)
				$get_type = 'grid-'.$column['size'].'col';
			else
				$get_type = 'grid-ncol';
		}
		if(isset($_GET['type']) && $_GET['type']) $get_type = sanitize_text_field($_GET['type']);
		if($get_type == 'grid-2col' ||$get_type == 'grid-3col' || $get_type == 'grid-ncol') $view = 'grid'; else if($get_type == 'list')$view = 'list';
        if(isset($_GET['number']) && $_GET['number']) $number = sanitize_text_field($_GET['number']);
		
        if(!empty($css_class)) $el_class .= ' '.$css_class;
        $filter_show = '';
        $el_class = 'product-'.$view.'-view '.$grid_type.' filter-'.$filter_show;

		if(isset($column['size'])) $column =$column_style_type = $column['size'];
		if(isset($column_widescreen['size'])) $column_widescreen = $column_widescreen['size'];
		if(isset($column_laptop['size'])) $column_laptop = $column_laptop['size'];
		if(isset($column_tablet_extra['size'])) $column_tablet_extra = $column_tablet_extra['size'];
		if(isset($column_tablet['size'])) $column_tablet = $column_tablet['size'];
		if(isset($column_mobile_extra['size'])) $column_mobile_extra = $column_mobile_extra['size'];
		if(isset($column_mobile['size'])) $column_mobile = $column_mobile['size'];
		if(!empty($column_custom)){
        	$column = $column_tablet = $column_mobile = $column_widescreen= $column_laptop= $column_tablet_extra= $column_mobile_extra='';
        }
        if($get_type == 'grid-2col'){
		    $column = 2;
		}else if($get_type == 'grid-3col'){
		    $column = 3;
		}
		if ( $view == 'grid' ) {
			$this->add_render_attribute( 'elbzotech-item-grid', 'class', 'list-col-item item-grid-product-'.$item_style.' list-'.esc_attr($column).'-item list-'.esc_attr($column_widescreen).'-item-widescreen list-'.esc_attr($column_laptop).'-item-laptop  list-'.esc_attr($column_tablet_extra).'-item-tablet-extra list-'.esc_attr($column_tablet).'-item-tablet list-'.esc_attr($column_mobile_extra).'-item-mobile-extra list-'.esc_attr($column_mobile).'-item-mobile');
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-grid',$column_custom);
		}
		$this->add_render_attribute( 'elbzotech-item', 'class', 'item-product');
		if ( $view == 'slider') { 
			$this->add_render_attribute( 'elbzotech-wrapper', 'class', 'elbzotech-swiper-slider swiper-container' );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-items-custom', $slider_items_custom );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-items', $slider_items );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-items-widescreen', $slider_items_widescreen );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-items-laptop', $slider_items_laptop );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-items-tablet-extra', $slider_items_tablet_extra);
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-items-tablet', $slider_items_tablet);
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-items-mobile-extra', $slider_items_mobile_extra);
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-items-mobile', $slider_items_mobile );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-space', $slider_space );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-space-widescreen', $slider_space_widescreen );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-space-laptop', $slider_space_laptop );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-space-tablet-extra', $slider_space_tablet_extra );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-space-tablet', $slider_space_tablet );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-space-mobile-extra', $slider_space_mobile_extra );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-space-mobile', $slider_space_mobile );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column', $slider_column );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-auto', $slider_auto );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-center', $slider_center );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-loop', $slider_loop );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-speed', $slider_speed );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-navigation', $slider_navigation );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-pagination', $slider_pagination );
			$this->add_render_attribute( 'elbzotech-inner', 'class', 'swiper-wrapper' );
			$this->add_render_attribute( 'elbzotech-item-grid', 'class', 'swiper-slide item-grid-product-'.$item_style);
		}else if ( $view == 'slider-masory') {
			$this->add_render_attribute( 'elbzotech-wrapper', 'class', 'elbzotech-swiper-slider swiper-container' );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-items-custom', $slider_items_custom );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-items', $slider_items );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-items-widescreen', $slider_items_widescreen );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-items-laptop', $slider_items_laptop );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-items-tablet-extra', $slider_items_tablet_extra);
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-items-tablet', $slider_items_tablet);
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-items-mobile-extra', $slider_items_mobile_extra);
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-items-mobile', $slider_items_mobile );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-space', $slider_space );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-space-widescreen', $slider_space_widescreen );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-space-laptop', $slider_space_laptop );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-space-tablet-extra', $slider_space_tablet_extra );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-space-tablet', $slider_space_tablet );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-space-mobile-extra', $slider_space_mobile_extra );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-space-mobile', $slider_space_mobile );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column', $slider_column );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-auto', $slider_auto );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-center', $slider_center );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-loop', $slider_loop );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-speed', $slider_speed );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-navigation', $slider_navigation );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-pagination', $slider_pagination );
			$this->add_render_attribute( 'elbzotech-inner', 'class', 'swiper-wrapper' );
			if($slider_items_group > 1) 
				$this->add_render_attribute( 'elbzotech-item-grid', 'class', 'width_masory item-grid-product-'.$item_style);
			else
				$this->add_render_attribute( 'elbzotech-item-grid', 'class', 'swiper-slide item-grid-product-'.$item_style);
		}else if ( $view == 'grid-masory') {
			$this->add_render_attribute( 'elbzotech-wrapper', 'class', 'elbzotech-products-wrap js-content-wrap '.$el_class );
			$this->add_render_attribute( 'elbzotech-inner', 'class', 'js-content-main list-product-wrap ');
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column', $column );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-widescreen', $column_widescreen );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-laptop', $column_laptop );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-tablet-extra', $column_tablet_extra );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-tablet', $column_tablet );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-mobile-extra', $column_mobile_extra );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-mobile', $column_mobile );
		}
		else{

			$this->add_render_attribute( 'elbzotech-wrapper', 'class', 'elbzotech-products-wrap js-content-wrap '.$el_class );
			$this->add_render_attribute( 'elbzotech-inner', 'class', 'js-content-main list-product-wrap bzotech-row');
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column', $column );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-widescreen', $column_widescreen );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-laptop', $column_laptop );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-tablet-extra', $column_tablet_extra );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-tablet', $column_tablet );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-mobile-extra', $column_mobile_extra );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-mobile', $column_mobile );
		}
        
        $paged = (get_query_var('paged') && $view != 'slider'&& $view != 'slider-masory') ? get_query_var('paged') : 1;
        $args = array(
            'post_type'         => 'product',
            'posts_per_page'    => $number,
            'orderby'           => $orderby,
            'order'             => $order,
            'paged'             => $paged,
            );
        if($product_type == 'trending'){
            $args['meta_query'][] = array(
                    'key'     => 'trending_product',
                    'value'   => '1',
                    'compare' => '=',
                );
        }
        if($product_type == 'toprate'){
            $args['meta_key'] = '_wc_average_rating';
            $args['orderby'] = 'meta_value_num';
            $args['meta_query'] = WC()->query->get_meta_query();
            $args['tax_query'][] = WC()->query->get_tax_query();
        }
        if($product_type == 'mostview'){
            $args['meta_key'] = 'post_views';
            $args['orderby'] = 'meta_value_num';
        }
        if($product_type == 'menu_order'){
            $args['meta_key'] = 'menu_order';
            $args['orderby'] = 'meta_value_num';
        }
        if($product_type == 'bestsell'){
            $args['meta_key'] = 'total_sales';
            $args['orderby'] = 'meta_value_num';
        }
        if($product_type=='onsale'){
            $args['meta_query']['relation']= 'OR';
            $args['meta_query'][]=array(
                'key'   => '_sale_price',
                'value' => 0,
                'compare' => '>',                
                'type'          => 'numeric'
            );
            $args['meta_query'][]=array(
                'key'   => '_min_variation_sale_price',
                'value' => 0,
                'compare' => '>',                
                'type'          => 'numeric'
            );
        }
        if($product_type == 'featured'){
            $args['tax_query'][] = array(
                'taxonomy' => 'product_visibility',
                'field'    => 'name',
                'terms'    => 'featured',
                'operator' => 'IN',
            );
        }
        if($product_type == 'flash_sale'){
            $args['meta_query']['relation']= 'AND';
            $args['meta_query'][]=array(
                'key'   => '_sale_price',
                'value' => 0,
                'compare' => '>',                
                'type'          => 'numeric'
            );
            $args['meta_query'][1]['relation']='OR';
            $args['meta_query'][1][]=array(
                'key'   => '_sale_price_dates_from',
                'value' => strtotime(date('Y-m-d')),
                'compare' => '<=',                
                'type'          => 'numeric'
            );
            $args['meta_query'][1][]=array(
                'key'   => '_sale_price_dates_from',
                'compare' => 'NOT EXISTS',              
            );
            $args['meta_query'][]=array(
                'key'   => '_sale_price_dates_to',
                'value' => strtotime(date('Y-m-d')),
                'compare' => '>',                
                'type'          => 'numeric'
            );
            $args['meta_query'][]=array(
                'key'   => '_stock',
                'value' => 0,
                'compare' => '>',                
                'type'          => 'numeric'
            );
        }
        if(isset( $_GET['product_cat'])) $cats = sanitize_text_field($_GET['product_cat']);
        if(!empty($cats)) {
            $custom_list = explode(",",$cats);
            $args['tax_query'][]=array(
                'taxonomy'=>'product_cat',
                'field'=>'slug',
                'terms'=> $custom_list
            );
        }
        if(!empty($custom_ids)){
            $args['post__in'] = explode(',', $custom_ids);
        }
        $args['tax_query'][] = array(
            'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => 'exclude-from-catalog',
            'operator' => 'NOT IN',
        );
        if( isset( $_GET['min_price']) && isset( $_GET['max_price']) ){
            $min = sanitize_text_field($_GET['min_price']);
            $max = sanitize_text_field($_GET['max_price']);
            $args['post__in'] = bzotech_filter_price($min,$max);
        }
        // Filter by rating.
		if ( isset( $_GET['rating_filter'] ) ) {
			$product_visibility_terms  = wc_get_product_visibility_term_ids();
			$product_visibility_not_in = array( is_search() && $main_query ? $product_visibility_terms['exclude-from-search'] : $product_visibility_terms['exclude-from-catalog'] );
			$rating_filter = array_filter( array_map( 'absint', explode( ',', wp_unslash( $_GET['rating_filter'] ) ) ) );
			$rating_terms  = array();
			for ( $i = 1; $i <= 5; $i ++ ) {
				if ( in_array( $i, $rating_filter, true ) && isset( $product_visibility_terms[ 'rated-' . $i ] ) ) {
					$rating_terms[] = $product_visibility_terms[ 'rated-' . $i ];
				}
			}
			if ( ! empty( $rating_terms ) ) {
				 $args['tax_query'][] = array(
					'taxonomy'      => 'product_visibility',
					'field'         => 'term_taxonomy_id',
					'terms'         => $rating_terms,
					'operator'      => 'IN',
					'rating_filter' => true,
				);
			}
		}

	    $attributes = wc_get_attribute_taxonomies();
	   	if(!empty($attributes) && is_array($attributes)){
	   		foreach($attributes as $attribute){
	   			
	   			if ( isset( $_GET['filter_'.$attribute->attribute_name] ) ) {
					$filter_brand = array_filter(explode( ',', wp_unslash( $_GET['filter_'.$attribute->attribute_name] ) ) );
					if ( ! empty( $filter_brand ) ) {
						 $args['tax_query'][] =array(
					        'taxonomy'        => 'pa_'.$attribute->attribute_name,
					        'field'           => 'slug',
					        'terms'           =>  $filter_brand,
					        'operator'        => 'IN',
					    );
					}
				}
	   		}
	   	}
		
        $product_query = new WP_Query($args);
        $count = 1;
        $count_query = $product_query->post_count;
        $max_page = $product_query->max_num_pages;
        $size = $thumbnail_size;
        if($size == 'custom' && !empty($thumbnail_custom_dimension['width']) && !empty($thumbnail_custom_dimension['height']))
        	$size = array($thumbnail_custom_dimension['width'],$thumbnail_custom_dimension['height']);
        
        if($grid_type == 'grid-masonry' and !empty($size_masonry)){
			$size = bzotech_get_size_crop($size_masonry);
        }

        $size_list = $thumbnail_list_size;
        if($size_list == 'custom' && !empty($thumbnail_list_custom_dimension['width']) && !empty($thumbnail_list_custom_dimension['height'])) $size_list = array($thumbnail_list_custom_dimension['width'],$thumbnail_list_custom_dimension['height']);
       


        $item_wrap = $this->get_render_attribute_string( 'elbzotech-item-grid' );
        $item_inner = $this->get_render_attribute_string( 'elbzotech-item' );
        $attr = array(
            'el_class'      => $el_class,
            'product_query' => $product_query,
            'count'         => $count,
            'count_query'   => $count_query,
            'max_page'      => $max_page,
            'args'          => $args,
            'column'        => $column,
            'column_style_type'        => $column_style_type,
            'view'       	=> $view,
            'settings'      => $settings,
            'size'      	=> $size,
            'item_wrap'		=> $item_wrap,
            'item_inner'	=> $item_inner,
            'get_type'	=> $get_type,
            'wdata'			=> $this,
        );
        if($display_tab !== '' && is_array($tabs) && count($tabs) >=1){
        	$tab_title_html = $tab_content_html = '';
        	foreach ($tabs as $key => $tab) {
        		extract($tab);
        		if($key == 0) $active = 'active';
        		else $active = '';
        		$args = array(
		            'post_type'         => 'product',
		            'posts_per_page'    => $number,
		            'orderby'           => $orderby,
		            'order'             => $order,
		            'paged'             => $paged,
		            );
		        if($product_type == 'trending'){
		            $args['meta_query'][] = array(
		                    'key'     => 'trending_product',
		                    'value'   => '1',
		                    'compare' => '=',
		                );
		        }
		        if($product_type == 'toprate'){
		            $args['meta_key'] = '_wc_average_rating';
		            $args['orderby'] = 'meta_value_num';
		            $args['meta_query'] = WC()->query->get_meta_query();
		            $args['tax_query'][] = WC()->query->get_tax_query();
		        }
		        if($product_type == 'mostview'){
		            $args['meta_key'] = 'post_views';
		            $args['orderby'] = 'meta_value_num';
		        }
		        if($product_type == 'menu_order'){
		            $args['meta_key'] = 'menu_order';
		            $args['orderby'] = 'meta_value_num';
		        }
		        if($product_type == 'bestsell'){
		            $args['meta_key'] = 'total_sales';
		            $args['orderby'] = 'meta_value_num';
		        }
		        if($product_type=='onsale'){
		            $args['meta_query']['relation']= 'OR';
		            $args['meta_query'][]=array(
		                'key'   => '_sale_price',
		                'value' => 0,
		                'compare' => '>',                
		                'type'          => 'numeric'
		            );
		            $args['meta_query'][]=array(
		                'key'   => '_min_variation_sale_price',
		                'value' => 0,
		                'compare' => '>',                
		                'type'          => 'numeric'
		            );
		        }
		        if($product_type == 'featured'){
		            $args['tax_query'][] = array(
		                'taxonomy' => 'product_visibility',
		                'field'    => 'name',
		                'terms'    => 'featured',
		                'operator' => 'IN',
		            );
		        }
		        if($product_type == 'flash_sale'){
		            $args['meta_query']['relation']= 'AND';
		            $args['meta_query'][]=array(
		                'key'   => '_sale_price',
		                'value' => 0,
		                'compare' => '>',                
		                'type'          => 'numeric'
		            );
		            $args['meta_query'][1]['relation']='OR';
		            $args['meta_query'][1][]=array(
		                'key'   => '_sale_price_dates_from',
		                'value' => strtotime(date('Y-m-d')),
		                'compare' => '<=',                
		                'type'          => 'numeric'
		            );
		            $args['meta_query'][1][]=array(
		                'key'   => '_sale_price_dates_from',
		                'compare' => 'NOT EXISTS',              
		            );
		            $args['meta_query'][]=array(
		                'key'   => '_sale_price_dates_to',
		                'value' => strtotime(date('Y-m-d')),
		                'compare' => '>',                
		                'type'          => 'numeric'
		            );
		            $args['meta_query'][]=array(
		                'key'   => '_stock',
		                'value' => 0,
		                'compare' => '>',                
		                'type'          => 'numeric'
		            );
		        }
		        if(!empty($cats)) {
		            $custom_list = explode(",",$cats);
		            $args['tax_query'][]=array(
		                'taxonomy'=>'product_cat',
		                'field'=>'slug',
		                'terms'=> $custom_list
		            );
		        }
		        if(!empty($custom_ids)){
		            $args['post__in'] = explode(',', $custom_ids);
		        }
		        $args['tax_query'][] = array(
		            'taxonomy' => 'product_visibility',
		            'field'    => 'name',
		            'terms'    => 'exclude-from-catalog',
		            'operator' => 'NOT IN',
		        );
		        $attr['args'] = $args;
		        $product_query = new WP_Query($args);
		        $count = 1;
		        $count_query = $product_query->post_count;
		        $max_page = $product_query->max_num_pages;
		        $attr['product_query'] = $product_query;
		        $attr['count'] = $count;
		        $attr['count_query'] = $count_query;
		        $attr['max_page'] = $max_page;
		        $id_tab_rand = $_id.rand(10,1000);
		        $tab_icon_html= '';
		        if(!empty($icon_image['url'])){
					$class_icon_image_hover ='';
					if(!empty($icon_image_hover['url'])) $class_icon_image_hover = 'icon-image-hover__active';
					$tab_icon_html .= '<span class="'.$class_icon_image_hover.'">';
					$tab_icon_html .= Group_Control_Image_Size::get_attachment_image_html( $tabs[$key],'','icon_image');
					
					if(!empty($icon_image_hover['url'])){
						$tab_icon_html .= '<span class="img-hover">'.Group_Control_Image_Size::get_attachment_image_html( $tabs[$key],'','icon_image_hover').'</span>';
					}
					$tab_icon_html .= '</span>';
				}else if(!empty( $icon['value'])){
					$tab_icon_html .= '<span class="">';		
					if( $icon['library'] == 'svg')
						$tab_icon_html .= '<img alt="'.esc_attr__('svg','bw-printxtore').'" src="'.esc_url($icon['value']['url']).'">';
					else
					$tab_icon_html .= '<i class="style-header-tab-icon-item-e  '.$icon['value'].'"></i>';
					$tab_icon_html .= '</span>';
				} 

        		$tab_title_html .= 	'<li class="tab-item-wrap '.$active.'">
        								<a href="#'.$id_tab_rand.'" data-target="#'.$id_tab_rand.'" data-toggle="tab" aria-expanded="false"  >';
        		if($icon_pos != 'after-text' && $tab_icon_html) $tab_title_html .= $tab_icon_html;
        		$tab_title_html .=		$title;
        		if($icon_pos == 'after-text' && $tab_icon_html) $tab_title_html .= $tab_icon_html;
        		$tab_title_html .=		'</a>
        							</li>';
        		$tab_content_html .= '<div id="'.$id_tab_rand.'" class="tab-pane '.$active.'">';
        		
        		$tab_content_html .= bzotech_get_template_elementor_global('products/shop',$view,$attr,false);
        		if ( !empty( $link_view_tab['url']) ) { 
					$this->add_link_attributes( 'link_view_tab'.$key, $link_view_tab);
					$tab_content_html .='<a class = "link-view-tab font-semibold elbzotech-bt-global-style3  " '.$this->get_render_attribute_string( 'link_view_tab'.$key ).' ><span class="text-button">'.esc_html__('View All', 'bw-printxtore').'</span><i class="las la-long-arrow-alt-right title18"></i></a>';
				}
        		$tab_content_html .= '</div>';
        		
        		
        	}
        	if(!empty($title_tab)) $title_tab = '<h3 class="title-tab title26">'.$title_tab.'</h3>';
        	echo 	'<div class="product-tab-wrap tab-wrap product-tab-'.$display_tab.'">

        				<div class="product-tab-title">'. $title_tab.'
        					<div class="inner tab-mobile-dropdown">
        						<h4 class="title-tab-mobile hidden"><span class="text-title-tab"></span><i class="las la-angle-down"></i></h4>
								<ul class="list-none nav nav-tabs" role="tablist">
									'.$tab_title_html.'
								</ul>
							</div>
						</div>
						<div class="product-tab-content">
							<div class="tab-content">
								'.$tab_content_html.'
							</div>
						</div>
					</div>';
        }
        else bzotech_get_template_elementor_global('products/shop',$view,$attr,true);
        wp_reset_postdata();
	}

	/**
	 * Render the widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function content_template() {
		
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function register_controls() {
		// BEGIN TAB_CONTENT
		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Layout', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'display',
			[
				'label' 	=> esc_html__( 'Display type (Layout)', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'elbzotech-product-grid',
				'options'   => [
					'elbzotech-product-grid'		=> esc_html__( 'Grid', 'bw-printxtore' ),
					'elbzotech-product-list'		=> esc_html__( 'List', 'bw-printxtore' ),
					'elbzotech-product-slider'		=> esc_html__( 'Slider', 'bw-printxtore' ),
					'elbzotech-product-grid-masory'		=> esc_html__( 'Grid Masory', 'bw-printxtore' ),
				],
			]
		);

		$this->add_control(
			'item_style',
			[
				'label' 	=> esc_html__( 'Item Grid Style', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => bzotech_get_product_style(),
			]
		);

		

		$this->add_control( 
			'display_tab',
			[
				'label' => esc_html__( 'Display tab', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default' => '',
				'options'   => [
					''		=> esc_html__( 'Off', 'bw-printxtore' ),
					'style1'		=> esc_html__( 'Style 1 (Default)', 'bw-printxtore' ),
					'style2'		=> esc_html__( 'Style 2', 'bw-printxtore' ),
					'style3'		=> esc_html__( 'Style 3', 'bw-printxtore' ),
					'style4'		=> esc_html__( 'Style 4', 'bw-printxtore' ),
				],
			]
		);

		$this->add_control(
			'item_thumbnail',
			[
				'label' => esc_html__( 'Thumbnail', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options'   => [
					''		=> esc_html__( 'Default', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Show', 'bw-printxtore' ),
					'no'		=> esc_html__( 'Hide', 'bw-printxtore' ),
				],
			]
		);

		$this->add_control(
			'item_title',
			[
				'label' => esc_html__( 'Title', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options'   => [
					''		=> esc_html__( 'Default', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Show', 'bw-printxtore' ),
					'no'		=> esc_html__( 'Hide', 'bw-printxtore' ),
				],
			]
		);

		$this->add_control(
			'item_quickview',
			[
				'label' => esc_html__( 'Quick View', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options'   => [
					''		=> esc_html__( 'Default', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Show', 'bw-printxtore' ),
					'no'		=> esc_html__( 'Hide', 'bw-printxtore' ),
				],
			]
		);

		$this->add_control(
			'item_label',
			[
				'label' => esc_html__( 'Label', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options'   => [
					''		=> esc_html__( 'Default', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Show', 'bw-printxtore' ),
					'no'		=> esc_html__( 'Hide', 'bw-printxtore' ),
				],
			]
		);

		$this->add_control(
			'item_price',
			[
				'label' => esc_html__( 'Price', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options'   => [
					''		=> esc_html__( 'Default', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Show', 'bw-printxtore' ),
					'no'		=> esc_html__( 'Hide', 'bw-printxtore' ),
				],
			]
		);

		$this->add_control(
			'item_rate',
			[
				'label' => esc_html__( 'Rate', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options'   => [
					''		=> esc_html__( 'Default', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Show', 'bw-printxtore' ),
					'no'		=> esc_html__( 'Hide', 'bw-printxtore' ),
				],
			]
		);

		$this->add_control(
			'item_button',
			[
				'label' => esc_html__( 'Button', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options'   => [
					''		=> esc_html__( 'Default', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Show', 'bw-printxtore' ),
					'no'		=> esc_html__( 'Hide', 'bw-printxtore' ),
				],
			]
		);
		$this->add_control(
			'item_excerpt',
			[
				'label' => esc_html__( 'Excerpt', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options'   => [
					''		=> esc_html__( 'Default', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Show', 'bw-printxtore' ),
					'no'		=> esc_html__( 'Hide', 'bw-printxtore' ),
				],
			]
		);
		$this->add_control(
			'excerpt',
			[
				'label' => esc_html__( 'Number of text for excerpt', 'bw-printxtore' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 999,
				'step' => 1,
				'default' => 80,
				'condition' => [
					'item_excerpt' => 'yes',
				]
			]
		);

		$this->add_control(
			'item_countdown',
			[
				'label' => esc_html__( 'Show countdown price', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options'   => [
					''		=> esc_html__( 'Default', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Show', 'bw-printxtore' ),
					'no'		=> esc_html__( 'Hide', 'bw-printxtore' ),
				],
			]
		);
		$this->add_control(
			'item_brand',
			[
				'label' => esc_html__( 'Show Brand', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options'   => [
					''		=> esc_html__( 'Default', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Show', 'bw-printxtore' ),
					'no'		=> esc_html__( 'Hide', 'bw-printxtore' ),
				],
			]
		);
		$this->add_control(
			'item_flash_sale',
			[
				'label' => esc_html__( 'Flash Sale', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options'   => [
					''		=> esc_html__( 'Default', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Show', 'bw-printxtore' ),
					'no'		=> esc_html__( 'Hide', 'bw-printxtore' ),
				],
			]
		);
		$this->add_control(
			'item_attribute',
			[
				'label' => esc_html__( 'Attribute (For products of type Data variable)', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options'   => [
					''		=> esc_html__( 'Default', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Show', 'bw-printxtore' ),
					'no'		=> esc_html__( 'Hide', 'bw-printxtore' ),
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_posts',
			[
				'label' => esc_html__( 'Query', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'number',
			[
				'label' => esc_html__( 'Number', 'bw-printxtore' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 1000,
				'step' => 1,
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' 	=> esc_html__( 'Order by', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => [
					''		=> esc_html__( 'None', 'bw-printxtore' ),
					'ID'		=> esc_html__( 'ID', 'bw-printxtore' ),
					'author'	=> esc_html__( 'Author', 'bw-printxtore' ),
					'title'		=> esc_html__( 'Title', 'bw-printxtore' ),
					'name'		=> esc_html__( 'Name', 'bw-printxtore' ),
					'date'		=> esc_html__( 'Date', 'bw-printxtore' ),
					'modified'		=> esc_html__( 'Last Modified Date', 'bw-printxtore' ),
					'parent'		=> esc_html__( 'Parent', 'bw-printxtore' ),
					'post_views'		=> esc_html__( 'Post views', 'bw-printxtore' ),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label' 	=> esc_html__( 'Order', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'DESC',
				'options'   => [
					'DESC'		=> esc_html__( 'DESC', 'bw-printxtore' ),
					'ASC'		=> esc_html__( 'ASC', 'bw-printxtore' ),
				],
			]
		);

		$this->add_control(
			'product_type',
			[
				'label' 	=> esc_html__( 'Product type', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => [
					'' 				=> esc_html__('Default','bw-printxtore'),
                    'trending' 		=> esc_html__('Trending','bw-printxtore'),
                    'featured' 		=> esc_html__('Featured Products','bw-printxtore'),
                    'bestsell' 		=> esc_html__('Best Sellers','bw-printxtore'),
                    'onsale' 		=> esc_html__('On Sale','bw-printxtore'),
                    'toprate' 		=> esc_html__('Top rate','bw-printxtore'),
                    'mostview' 		=> esc_html__('Most view','bw-printxtore'),
                    'menu_order' 	=> esc_html__('Menu order','bw-printxtore'),
                    'flash_sale' 	=> esc_html__('Flash Sale','bw-printxtore'),
				],
			]
		);

		$this->add_control(
			'custom_ids', 
			[
				'label' => esc_html__( 'Show by IDs', 'bw-printxtore' ),
				'description' => esc_html__( 'Enter IDs list. The values separated by ",". Example 11,12', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( '11,12', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			'cats', 
			[
				'label' => esc_html__( 'Categories', 'bw-printxtore' ),
				'description' => esc_html__( 'Enter slug categories. The values separated by ",". Example cat-1,cat-2. Default will show all categories', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'cat-1,cat-2', 'bw-printxtore' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_list_setting',
			[
				'label' => esc_html__( 'List setting', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'display' => ['elbzotech-product-grid','elbzotech-product-list'],
				]
			]
		);
		$this->add_control(
			'item_list_style',
			[
				'label' 	=> esc_html__( 'Item List Style', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => [
					''		=> esc_html__( 'Style 1 - default', 'bw-printxtore' ),
				],
			]
		);
		$this->add_control(
			'excerpt_list',
			[
				'label' => esc_html__( 'Excerpt list', 'bw-printxtore' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 1000,
				'step' => 1,
			]
		);

		$this->add_responsive_control(
			'item_list_thumb_width',
			[
				'label' => esc_html__( 'Thumbnail Width', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' , 'px' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 0.01,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .list-thumb-wrap' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .list-info-wrap' => 'width: calc(100% - {{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail_list', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default' => 'custom',
				'separator' => 'none',
			]
		);
		
		$this->end_controls_section();

		$this->start_controls_section(
			'section_grid',
			[
				'label' => esc_html__( 'Grid Setting', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'display' => ['elbzotech-product-grid','elbzotech-product-list'],
				]
			]
		);

		$this->add_responsive_control(
			'column',
			[
				'label' => esc_html__( 'Column', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 8,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 3,
				],
				'condition' => [
					'column_custom' => '',
				]
			]
		); 
		$this->add_control(
			'column_custom',
			[
				'label' => esc_html__( 'Column custom by display', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'description'	=> esc_html__( 'Enter item for screen width(px) format is width:value and separate values by ",". Example is 0:1,375:2,991:3,1170:4', 'bw-printxtore' ),
				'default' => '',
				
			]
		);
		$this->add_control(
			'grid_type',
			[
				'label' 	=> esc_html__( 'Grid type', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => [
					''				=> esc_html__( 'Default', 'bw-printxtore' ),
					'grid-masonry'	=> esc_html__( 'Masonry', 'bw-printxtore' ),
				],
			]
		);

		$this->add_control(
			'pagination',
			[
				'label' 	=> esc_html__( 'Grid pagination', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => [
					''				=> esc_html__( 'None', 'bw-printxtore' ),
					'pagination'	=> esc_html__( 'Pagination', 'bw-printxtore' ),
					'load-more'		=> esc_html__( 'Load more', 'bw-printxtore' ),
				],
			]
		);

		$this->end_controls_section();

		$this->get_slider_settings();
		$this->get_slider_masory_settings();
		$this->start_controls_section(
			'section_top_filter',
			[
				'label' => esc_html__( 'Top filter', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'display' => ['elbzotech-product-list', 'elbzotech-product-grid'],
				]
			]
		);

		$this->add_control(
			'show_top_filter',
			[
				'label' => esc_html__( 'Status', 'bw-printxtore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'bw-printxtore' ),
				'label_off' => esc_html__( 'Off', 'bw-printxtore' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_control(
			'show_type',
			[
				'label' => esc_html__( 'Type', 'bw-printxtore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'bw-printxtore' ),
				'label_off' => esc_html__( 'Off', 'bw-printxtore' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'show_top_filter' => 'yes',
				]
			]
		);
		$this->add_control(
			'show_number',
			[
				'label' => esc_html__( 'Number', 'bw-printxtore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'bw-printxtore' ),
				'label_off' => esc_html__( 'Off', 'bw-printxtore' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'show_top_filter' => 'yes',
				]
			]
		);

		$this->add_control(
			'show_order',
			[
				'label' => esc_html__( 'Order', 'bw-printxtore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'bw-printxtore' ),
				'label_off' => esc_html__( 'Off', 'bw-printxtore' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'show_top_filter' => 'yes',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_filter',
			[
				'label' => esc_html__( 'Filter Button', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'filter_show',
			[
				'label' => esc_html__( 'Status', 'bw-printxtore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'bw-printxtore' ),
				'label_off' => esc_html__( 'Off', 'bw-printxtore' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'filter_style',
			[
				'label' 	=> esc_html__( 'Style', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => [
					''					=> esc_html__( 'Style 1', 'bw-printxtore' ),
					'filter-col'		=> esc_html__( 'Style 2', 'bw-printxtore' ),
					'filter-col filter-col-list'	=> esc_html__( 'Style 3', 'bw-printxtore' ),
				],
				'condition' => [
					'filter_show' => 'yes',
				]
			]
		);

		$this->add_control(
			'filter_column',
			[
				'label' 	=> esc_html__( 'Column', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'filter-4-col',
				'options'   => [
					'filter-2-col'				=> esc_html__( '2 Column', 'bw-printxtore' ),
					'filter-3-col'				=> esc_html__( '3 Column', 'bw-printxtore' ),
					'filter-4-col'				=> esc_html__( '4 Column', 'bw-printxtore' ),
				],
				'condition' => [
					'filter_show' => 'yes',
					'filter_style' => ['filter-col','filter-col filter-col-list'],
				]
			]
		);

		$this->add_control(
			'filter_cats', 
			[
				'label' => esc_html__( 'Categories', 'bw-printxtore' ),
				'description' => esc_html__( 'Enter slug categories. The values separated by ",". Example cat-1,cat-2', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'cat-1,cat-2', 'bw-printxtore' ),
				'condition' => [
					'filter_show' => 'yes',
				]
			]
		);

		$this->add_control(
			'filter_price',
			[
				'label' => esc_html__( 'Price', 'bw-printxtore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'bw-printxtore' ),
				'label_off' => esc_html__( 'Off', 'bw-printxtore' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'filter_show' => 'yes',
				]
			]
		);

		$this->add_control(
			'filter_attr', 
			[
				'label' => esc_html__( 'Attributes', 'bw-printxtore' ),
				'description' => esc_html__( 'Enter slug attributes. The values separated by ",". Example attribute-1,attribute-2', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'cat-1,cat-2', 'bw-printxtore' ),
				'condition' => [
					'filter_show' => 'yes',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tab',
			[
				'label' => esc_html__( 'Tab', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'display_tab!' => '',
				]
			]
		);
		$this->add_control(
			'title_tab',
			[
				'label' => esc_html__( 'Title', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => '',
				'condition' => [
					'display_tab' => ['style3','style4']
				]
			]
		);
		$repeater = new Repeater();

		$repeater->add_control(
			'title', [
				'label' => esc_html__( 'Title', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Tab Title' , 'bw-printxtore' ),
				'label_block' => true,
			]
		);

		$repeater->add_control( 
			'icon',
			[
				'label' => esc_html__( 'Icon', 'bw-printxtore' ),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'icon_image[url]' =>  '',
				]
			]
		);
		$repeater->add_control(
			'icon_image',
			[
				'label' => esc_html__( 'Icon image', 'bw-printxtore' ),
				'description'	=> esc_html__( 'You can choose the icon image here (Replace for icon)', 'bw-printxtore' ),
				'type' => Controls_Manager::MEDIA,
				'condition' => [
					'icon[value]' =>  '',
				]
			]
		);
		$repeater->add_control(
			'icon_image_hover',
			[
				'label' => esc_html__( 'Icon image hover', 'bw-printxtore' ),
				'description'	=> esc_html__( 'You can choose the icon image here (Replace for icon)', 'bw-printxtore' ),
				'type' => Controls_Manager::MEDIA,
				'condition' => [
					'icon_image[url]!' =>  '',
				]
			]
		);

		$repeater->add_control(
			'icon_pos',
			[
				'label' => esc_html__( 'Icon position', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'after-icon',
				'options' => [
					'after-text'   => esc_html__( 'After text', 'bw-printxtore' ),
					'before-text'  => esc_html__( 'Before text', 'bw-printxtore' ),
				],
			]
		);

		$repeater->add_control(
			'number',
			[
				'label' => esc_html__( 'Number', 'bw-printxtore' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 1000,
				'step' => 1,
			]
		);

		$repeater->add_control(
			'orderby',
			[
				'label' 	=> esc_html__( 'Order by', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => [
					''		=> esc_html__( 'None', 'bw-printxtore' ),
					'ID'		=> esc_html__( 'ID', 'bw-printxtore' ),
					'author'	=> esc_html__( 'Author', 'bw-printxtore' ),
					'title'		=> esc_html__( 'Title', 'bw-printxtore' ),
					'name'		=> esc_html__( 'Name', 'bw-printxtore' ),
					'date'		=> esc_html__( 'Date', 'bw-printxtore' ),
					'modified'		=> esc_html__( 'Last Modified Date', 'bw-printxtore' ),
					'parent'		=> esc_html__( 'Parent', 'bw-printxtore' ),
					'post_views'		=> esc_html__( 'Post views', 'bw-printxtore' ),
				],
			]
		);

		$repeater->add_control(
			'order',
			[
				'label' 	=> esc_html__( 'Order', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'DESC',
				'options'   => [
					'DESC'		=> esc_html__( 'DESC', 'bw-printxtore' ),
					'ASC'		=> esc_html__( 'ASC', 'bw-printxtore' ),
				],
			]
		);

		$repeater->add_control(
			'product_type',
			[
				'label' 	=> esc_html__( 'Product type', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'DESC',
				'options'   => [
					'' 				=> esc_html__('Default','bw-printxtore'),
                    'trending' 		=> esc_html__('Trending','bw-printxtore'),
                    'featured' 		=> esc_html__('Featured Products','bw-printxtore'),
                    'bestsell' 		=> esc_html__('Best Sellers','bw-printxtore'),
                    'onsale' 		=> esc_html__('On Sale','bw-printxtore'),
                    'toprate' 		=> esc_html__('Top rate','bw-printxtore'),
                    'mostview' 		=> esc_html__('Most view','bw-printxtore'),
                    'menu_order' 	=> esc_html__('Menu order','bw-printxtore'),
				],
			]
		);

		$repeater->add_control(
			'custom_ids', 
			[
				'label' => esc_html__( 'Show by IDs', 'bw-printxtore' ),
				'description' => esc_html__( 'Enter IDs list. The values separated by ",". Example 11,12', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( '11,12', 'bw-printxtore' ),
			]
		);

		$repeater->add_control(
			'cats', 
			[
				'label' => esc_html__( 'Categories', 'bw-printxtore' ),
				'description' => esc_html__( 'Enter slug categories. The values separated by ",". Example cat-1,cat-2. Default will show all categories', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'cat-1,cat-2', 'bw-printxtore' ),
			]
		);
		$repeater->add_control(
			'link_view_tab',
			[
				'label' => esc_html__( 'Link view', 'bw-printxtore' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'bw-printxtore' ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => false,
					'nofollow' => true,
				],
			]
		);
		$this->add_control(
			'tabs',
			[
				'label' => esc_html__( 'Add tab', 'bw-printxtore' ),
				'type' => Controls_Manager::REPEATER,
				'prevent_empty'=>false,
				'fields' => $repeater->get_controls(),
				'default' => [],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();
		// END TAB_CONTENT

		// BEGIN TAB_STYLE

		$this->start_controls_section(
			'section_style_item',
			[
				'label' => esc_html__( 'Item', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'item_width',
			[
				'label' => esc_html__( 'Width', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' , 'px' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 0.01,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .product-slider-view .item-product' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .product-grid-view .list-col-item' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->get_box_settings('item','item-product');

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_thumbnail',
			[
				'label' => esc_html__( 'Thumbnail', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default' => 'custom',
				'separator' => 'none',
				'condition' => [
					'item_thumbnail!' => 'no',
					'grid_type!' => 'grid-masonry',
				]
			]
		);
		$this->add_control(
			'size_masonry',
			[
				'label' => esc_html__( 'Random image size', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'description' => esc_html__( 'Random image size mansory type (EX: 300x350,300x300,300x250)', 'bw-printxtore' ),
				'condition' => [
					'grid_type' => 'grid-masonry',
					'item_thumbnail!' => 'yes',
				]
			]
		);
		$this->add_control(
			'size_random_img',
			[
				'label' => esc_html__( 'Random image size', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'description' => esc_html__( 'Random image size mansory type (EX: 300x350,300x300,300x250)', 'bw-printxtore' ),
				
			]
		);
		$this->get_thumb_styles('thumbnail','product-thumb');

		$this->get_box_image('thumbnail','product-thumb');

		$this->end_controls_section();

		$this->get_slider_styles();

		$this->start_controls_section(
			'section_style_info',
			[
				'label' => esc_html__( 'Info', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'info_align',
			[
				'label' => esc_html__( 'Alignment', 'bw-printxtore' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'bw-printxtore' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'bw-printxtore' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'bw-printxtore' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justified', 'bw-printxtore' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .product-info' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->get_box_settings_info('info','product-info');

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label' => esc_html__( 'Title', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->get_text_styles('title','product-info .product-title a');

		$this->add_responsive_control(
			'title_space',
			[
				'label' => esc_html__( 'Space', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -300,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .product-info .product-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_price',
			[
				'label' => esc_html__( 'Price', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'price_regular',
			[
				'label' => esc_html__( 'Regular', 'bw-printxtore' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'none',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'regular_typography',
				'selectors' => [
					'{{WRAPPER}} .product-price > span',
					'{{WRAPPER}} .product-price ins',
				]
			]
		);

		$this->add_control(
			'regular_color',
			[
				'label' => esc_html__( 'Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-price > span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .product-price ins' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'price_sale',
			[
				'label' => esc_html__( 'Sale', 'bw-printxtore' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sale_typography',
				'selectors' => [
					'{{WRAPPER}} .product-price > del',
				]
			]
		);

		$this->add_control(
			'sale_color',
			[
				'label' => esc_html__( 'Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-price > del' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'separator_price',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->add_responsive_control(
			'price_space',
			[
				'label' => esc_html__( 'Space', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -300,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .product-info .product-price' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
		//.product-label span

		$this->start_controls_section(
			'section_style_label',
			[
				'label' => esc_html__( 'Label', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs( 'section_style_labels' );
		$this->start_controls_tab( 'section_label_new',
			[
				'label' => esc_html__( 'Label New', 'bw-printxtore' ),
			]
		);
		$this->add_control(
			'color_text_label_new',
			[
				'label' => esc_html__( 'Text Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-label span.new' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography_label_new',
				'label' => esc_html__( 'Typography Text', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .product-label span.new',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_label_new',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .product-label span.new',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_label_new',
				'selector' => '{{WRAPPER}} .product-label span.new',
			]
		);
		$this->add_responsive_control(
			'border_radius_label_new',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .product-label span.new' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'padding_label_new',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .product-label span.new' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'section_label_sale',
			[
				'label' => esc_html__( 'Label Sale', 'bw-printxtore' ),
			]
		);
		
		$this->add_control(
			'color_text_label_sale',
			[
				'label' => esc_html__( 'Text Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-label span.sale' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography_label_sale',
				'label' => esc_html__( 'Typography Text', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .product-label span.sale',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_label_sale',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .product-label span.sale',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_label_sale',
				'selector' => '{{WRAPPER}} .product-label span.sale',
			]
		);
		$this->add_responsive_control(
			'border_radius_label_sale',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .product-label span.sale' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'padding_label_sale',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .product-label span.sale' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label' => esc_html__( 'Button', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'item_button' => 'yes',
				]
			]
		);

		$this->get_button_styles('button','addcart-link');

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_tab',
			[
				'label' => esc_html__( 'Tab', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'display_tab' => 'style1',
				]
			]
		); 
		$start = is_rtl() ? 'right' : 'left';
		$end = is_rtl() ? 'left' : 'right';
		$this->add_responsive_control(
			'flex_direction_tab',
			[
				'label' => esc_html__( 'Direction', 'bw-printxtore' ),
				'type' => Controls_Manager::CHOOSE,
				'responsive' => true,
				'label_block' => true,
				'options' => [
					'row' => [
						'title' => esc_html_x( 'Row - horizontal', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-arrow-' . $end,
					],
					'column' => [
						'title' => esc_html_x( 'Column - vertical', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-arrow-down',
					],
					'row-reverse' => [
						'title' => esc_html_x( 'Row - reversed', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-arrow-' . $start,
					],
					'column-reverse' => [
						'title' => esc_html_x( 'Column - reversed', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-arrow-up',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .nav-tabs' => 'flex-direction: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$this->add_responsive_control(
			'alignment_tab',
			[
				'label' => esc_html__( 'Justify Content', 'bw-printxtore' ),
				'type' => Controls_Manager::CHOOSE,
				'responsive' => true,
				'label_block' => true,
				'options' => [
					'flex-start' => [
						'title' => esc_html_x( 'Start', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-justify-start-h',
					],
					'center' => [
						'title' => esc_html_x( 'Center', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-justify-center-h',
					],
					'flex-end' => [
						'title' => esc_html_x( 'End', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-justify-end-h',
					],
					'space-between' => [
						'title' => esc_html_x( 'Space Between', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-justify-space-between-h',
					],
					'space-around' => [
						'title' => esc_html_x( 'Space Around', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-justify-space-around-h',
					],
					'space-evenly' => [
						'title' => esc_html_x( 'Space Evenly', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-justify-space-evenly-h',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .nav-tabs' => 'justify-content: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$this->add_responsive_control(
			'align_items_tab',
			[
				'label' => esc_html__( 'Align Items', 'bw-printxtore' ),
				'type' => Controls_Manager::CHOOSE,
				'responsive' => true,
				'options' => [
					'flex-start' => [
						'title' => esc_html_x( 'Start', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-align-start-v',
					],
					'center' => [
						'title' => esc_html_x( 'Center', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-align-center-v',
					],
					'flex-end' => [
						'title' => esc_html_x( 'End', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-align-end-v',
					],
					'stretch' => [
						'title' => esc_html_x( 'Stretch', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-align-stretch-v',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .nav-tabs' => 'align-items: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$this->add_responsive_control(
			'gap_item_tab',
			[
				'label' => esc_html__( 'Gap', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .nav-tabs' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'flex_wrap_tab',
			[
				'label' => esc_html__( 'Wrap', 'bw-printxtore' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'nowrap' => [
						'title' => esc_html__( 'No Wrap', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-nowrap',
					],
					'wrap' => [
						'title' => esc_html__( 'Wrap', 'bw-printxtore' ),
						'icon' => 'eicon-flex eicon-wrap',
					],
				],
				'description' => esc_html__(
					'Items within the container can stay in a single line (No wrap), or break into multiple lines (Wrap).','bw-printxtore'
				),
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .nav-tabs' => 'flex-wrap: {{VALUE}};',
				],
				'responsive' => true,
			]
		);

		$this->add_control(
			'bg_image_tab',
			[
				'label' => esc_html__( 'Background image', 'bw-printxtore' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'display_tab' => 'style2',
				]
			]
		);
		$this->add_responsive_control(
			'tab_align',
			[
				'label' => esc_html__( 'Alignment', 'bw-printxtore' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'bw-printxtore' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'bw-printxtore' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'bw-printxtore' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .nav-tabs' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'tab_item_width',
			[
				'label' => esc_html__( 'Item width', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .nav-tabs > li' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tab_item_height',
			[
				'label' => esc_html__( 'Item height', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .nav-tabs > li > a' => 'height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tab_typography',
				'selector' => '{{WRAPPER}} .nav-tabs > li > a',
			]
		);

		$this->add_responsive_control(
			'tab_size_icon',
			[
				'label' => esc_html__( 'Size icon', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .nav-tabs > li > a i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tab_spacing',
			[
				'label' => esc_html__( 'Space', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .product-tab-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'tab_icon_spacing_left',
			[
				'label' => esc_html__( 'Icon Space left', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .nav-tabs > li > a i' => 'margin-left: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'tab_icon_spacing_right',
			[
				'label' => esc_html__( 'Icon Space right', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .nav-tabs > li > a i' => 'margin-right: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->start_controls_tabs( 'tab_effects' );

		$this->start_controls_tab( 'tab_normal',
			[
				'label' => esc_html__( 'Normal', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			'tab_color',
			[
				'label' => esc_html__( 'Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .nav-tabs > li > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'tab_background',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .nav-tabs > li > a',
			]
		);

		$this->add_responsive_control(
			'tab_padding',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .nav-tabs > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);		

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tab_border',
                'label' => esc_html__( 'Border', 'bw-printxtore' ),
                'separator' => 'before',
				'selector' => '{{WRAPPER}} .nav-tabs > li > a',
			]
        );

        $this->add_responsive_control(
			'tab_radius',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .nav-tabs > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tab_shadow',
				'selector' => '{{WRAPPER}} .nav-tabs > li > a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'tab_hover',
			[
				'label' => esc_html__( 'Hover', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			'tab_color_hover',
			[
				'label' => esc_html__( 'Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .nav-tabs > li > a:hover' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'tab_background_hover',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .nav-tabs > li > a:hover',
			]
		);

		$this->add_responsive_control(
			'tab_padding_hover',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
                'selectors' => [
					'{{WRAPPER}} .nav-tabs > li > a:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tab_border_hover',
                'label' => esc_html__( 'Border', 'bw-printxtore' ),
                'separator' => 'before',
				'selector' => '{{WRAPPER}} .nav-tabs > li > a:hover',
			]
        );

        $this->add_responsive_control(
			'tab_radius_hover',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .nav-tabs > li > a:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tab_shadow_hover',
				'selector' => '{{WRAPPER}} .nav-tabs > li > a:hover',
			]
		);

		$this->add_control(
			'tab_hover_transition',
			[
				'label' => esc_html__( 'Transition Duration', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .nav-tabs > li > a' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'tab_active',
			[
				'label' => esc_html__( 'Active', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			'tab_color_active',
			[
				'label' => esc_html__( 'Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .nav-tabs > li.active > a' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'tab_background_active',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .nav-tabs > li.active > a',
			]
		);

		$this->add_responsive_control(
			'tab_padding_active',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
                'selectors' => [
					'{{WRAPPER}} .nav-tabs > li.active > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tab_border_active',
                'label' => esc_html__( 'Border', 'bw-printxtore' ),
                'separator' => 'before',
				'selector' => '{{WRAPPER}} .nav-tabs > li.active > a',
			]
        );

        $this->add_responsive_control(
			'tab_radius_active',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .nav-tabs > li.active > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tab_shadow_active',
				'selector' => '{{WRAPPER}} .nav-tabs > li.active > a',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
		
		$this->start_controls_section(
			'section_style_slider_scrollbar',
			[
				'label' => esc_html__( 'Slider Scrollbar', 'bw-printxtore' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'slider_scrollbar!' => '',
				]
			]
		);
		$this->add_control(
			'auto_show_scrollbar',
			[
				'label' => esc_html__( 'Auto show scrollbar', 'bw-printxtore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'bw-printxtore' ),
				'label_off' => esc_html__( 'Off', 'bw-printxtore' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);
		$this->add_responsive_control(
			'height_slider_scrollbar',
			[
				'label' => esc_html__( 'Height', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-scrollbar' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_slider_scrollbar',
				'label' => esc_html__( 'Background scrollbar', 'bw-printxtore' ),
				'types' => [ 'classic'],
				'selector' => '{{WRAPPER}} .swiper-scrollbar',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'color_slider_scrollbar',
				'label' => esc_html__( 'Color scrollbar', 'bw-printxtore' ),
				'types' => [ 'classic'],
				'selector' => '{{WRAPPER}} .swiper-scrollbar>div',
			]
		);

		$this->add_responsive_control(
			'border_slider_scrollbar',
			[
				'label' => esc_html__( 'Border radius scrollbar', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .swiper-scrollbar>div' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .swiper-scrollbar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'slider_scrollbar_margin',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', ],
				'selectors' => [
					'{{WRAPPER}} .swiper-scrollbar ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
		
		$this->end_controls_section();
		// END TAB_STYLE
	}

	public function get_button_styles($key='button', $class="btn-class") {

		$this->add_control(
			$key.'_text', 
			[
				'label' => esc_html__( 'Text', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'label_block' => true,
			]
		);

		$this->add_responsive_control(
			$key.'_align',
			[
				'label' => esc_html__( 'Alignment', 'bw-printxtore' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'bw-printxtore' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'bw-printxtore' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'bw-printxtore' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class.'-wrap' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => $key.'_typography',
				'selector' => '{{WRAPPER}} .'.$class,
			]
		);

		$this->add_control(
			$key.'_icon',
			[
				'label' => esc_html__( 'Icon', 'bw-printxtore' ),
				'type' => Controls_Manager::ICONS,
			]
		);

		$this->add_responsive_control(
			$key.'_size_icon',
			[
				'label' => esc_html__( 'Size icon', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .'.$class.' i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			$key.'_icon_pos',
			[
				'label' => esc_html__( 'Icon position', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'after-icon',
				'options' => [
					'after-text'   => esc_html__( 'After text', 'bw-printxtore' ),
					'before-text'  => esc_html__( 'Before text', 'bw-printxtore' ),
				],
				'condition' => [
					$key.'_text!' => '',
					$key.'_icon[value]!' => '',
				]
			]
		);

		$this->add_responsive_control(
			$key.'_spacing',
			[
				'label' => esc_html__( 'Space', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class.'-wrap' => 'margin-top: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			$key.'_icon_spacing_left',
			[
				'label' => esc_html__( 'Icon Space left', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class.' i' => 'margin-left: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			$key.'_icon_spacing_right',
			[
				'label' => esc_html__( 'Icon Space right', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class.' i' => 'margin-right: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->start_controls_tabs( $key.'_effects' );

		$this->start_controls_tab( $key.'_normal',
			[
				'label' => esc_html__( 'Normal', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			$key.'_color',
			[
				'label' => esc_html__( 'Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => $key.'_background',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .'.$class,
			]
		);

		$this->add_responsive_control(
			$key.'_padding',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => $key.'_shadow',
				'selector' => '{{WRAPPER}} .'.$class,
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( $key.'_hover',
			[
				'label' => esc_html__( 'Hover', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			$key.'_color_hover',
			[
				'label' => esc_html__( 'Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .'.$class.':hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => $key.'_background_hover',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .'.$class.':hover',
			]
		);

		$this->add_responsive_control(
			$key.'_padding_hover',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .'.$class.':hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => $key.'_shadow_hover',
				'selector' => '{{WRAPPER}} .'.$class.':hover',
			]
		);

		$this->add_control(
			$key.'_hover_transition',
			[
				'label' => esc_html__( 'Transition Duration', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
	}
	
	public function get_text_styles($key='text', $class="text-class") {
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => $key.'_typography',
				'selector' => '{{WRAPPER}} .'.$class,
			]
		);

		$this->start_controls_tabs( $key.'_effects' );

		$this->start_controls_tab( $key.'_normal',
			[
				'label' => esc_html__( 'Normal', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			$key.'_color',
			[
				'label' => esc_html__( 'Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => $key.'_shadow',
				'selector' => '{{WRAPPER}} .'.$class,
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( $key.'_hover',
			[
				'label' => esc_html__( 'Hover', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			$key.'_color_hover',
			[
				'label' => esc_html__( 'Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .'.$class.':hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => $key.'_shadow_hover',
				'selector' => '{{WRAPPER}} .'.$class.':hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
	}

	public function get_thumb_styles($key='thumb', $class="thumb-image") {
		$this->start_controls_tabs( $key.'_effects' );

		$this->start_controls_tab( 'normal',
			[
				'label' => esc_html__( 'Normal', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			$key.'_opacity',
			[
				'label' => esc_html__( 'Opacity', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class.' img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => $key.'_css_filters',
				'selector' => '{{WRAPPER}} .'.$class.' img',
			]
		);

		$this->add_control(
			$key.'_overlay',
			[
				'label' => esc_html__( 'Overlay', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .'.$class.' .product-thumb-link:before' => 'background-color: {{VALUE}}; opacity: 1; visibility: visible;',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'hover',
			[
				'label' => esc_html__( 'Hover', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			$key.'_opacity_hover',
			[
				'label' => esc_html__( 'Opacity', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class.':hover img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => $key.'_css_filters_hover',
				'selector' => '{{WRAPPER}} .'.$class.':hover img',
			]
		);

		$this->add_control(
			$key.'_overlay_hover',
			[
				'label' => esc_html__( 'Overlay', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .'.$class.':hover .product-thumb-link:before' => 'background-color: {{VALUE}}; opacity: 1; visibility: visible;',
				],
			]
		);

		$this->add_control(
			$key.'_background_hover_transition',
			[
				'label' => esc_html__( 'Transition Duration', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class.' img' => 'transition-duration: {{SIZE}}s',
					'{{WRAPPER}} .'.$class.' .product-thumb-link::after' => 'transition-duration: {{SIZE}}s',
					'{{WRAPPER}} .'.$class.' .product-thumb-link' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->add_control(
			$key.'_hover_animation',
			[
				'label' 	=> esc_html__( 'Hover Animation', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'elbzotech-post-grid',
				'options'   => [
					''					=> esc_html__( 'None', 'bw-printxtore' ),
					'zoom-thumb'		=> esc_html__( 'Zoom', 'bw-printxtore' ),
					'rotate-thumb'		=> esc_html__( 'Rotate', 'bw-printxtore' ),
					'zoomout-thumb'		=> esc_html__( 'Zoom Out', 'bw-printxtore'),
					'translate-thumb'	=> esc_html__( 'Translate', 'bw-printxtore'),
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
	}
	public function get_slider_masory_settings() {
		$this->start_controls_section(
			'section_slider_masory',
			[
				'label' => esc_html__( 'Masory settings', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				
			]
		);

		$this->add_control(
			'slider_items_group',
			[
				'label' => esc_html__( 'Group item products', 'bw-printxtore' ),
				'type' => Controls_Manager::NUMBER,
				'description'	=> esc_html__( 'Group the number of products into 1  item of slider', 'bw-printxtore' ),
				'min' => 1,
				'max' => 20,
				'step' => 1,
				'default' => 1,
				'condition' => [
					'display' => ['elbzotech-product-slider-masory'],
				]
			]
		);
		$this->add_control(
			'column_custom_masory',
			[
				'label' => esc_html__( 'Column custom by display', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'description'	=> esc_html__( 'Enter item for screen width(px) format is width:value and separate values by ",". Example is 0:1,375:2,991:3,1170:4', 'bw-printxtore' ),
				'default' => '',
				'condition' => [
					'display' => ['elbzotech-product-grid-masory'],
				]
			]
		);
		$this->add_responsive_control(
			'space_item',
			[
				'label' => esc_html__( 'Space item (px)', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .width_masory' => 'padding: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .list-product-wrap' => 'margin: -{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'display' => ['elbzotech-product-slider-masory','elbzotech-product-grid-masory'],
				]
			]
		);
		$repeater_masory = new Repeater();

		$repeater_masory->add_responsive_control(
			'width',
			[
				'label' => esc_html__( 'Width', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%','px','vw' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$repeater_masory->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default' => 'custom',
				'separator' => 'none',
			]
		);
		$default_template = [
					'style1'		=> esc_html__( 'Style 1 (Replace)', 'bw-printxtore' ),
					'style2'		=> esc_html__( 'Style 2 (Replace)', 'bw-printxtore' ),
					'style3'		=> esc_html__( 'Style 3 (Replace)', 'bw-printxtore' ),
					'style5'		=> esc_html__( 'Style 5 (Replace)', 'bw-printxtore' ),
				];
		$repeater_masory->add_control(
			'template',
			[
				'label' 	=> esc_html__( 'Replace style or Insert template', 'bw-printxtore' ),
				'description'	=> esc_html__( 'Replace the display style or insert content in the template', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => bzotech_list_post_type('elementor_library',true,$default_template),
			]
		);
		$repeater_masory->add_control(
			'add_class_css', 
			[
				'label' => esc_html__( 'Add class CSS', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter name class' , 'bw-printxtore' ),
			]
		);
		$this->add_control(
			'list_grid_custom',
			[
				'label' => esc_html__( 'Add layout masory', 'bw-printxtore' ),
				'type' => Controls_Manager::REPEATER,
				'prevent_empty'=>false,
				'fields' => $repeater_masory->get_controls(),
				'condition' => [
					'display' => ['elbzotech-product-slider-masory','elbzotech-product-grid-masory'],
				]
			]
		);

		$this->end_controls_section();
	}
	public function get_slider_settings() {
		$this->start_controls_section(
			'section_slider',
			[
				'label' => esc_html__( 'Slider', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'display' => ['elbzotech-product-slider','elbzotech-product-slider-masory'],
				]
			]
		);

		$this->add_responsive_control(
			'slider_items',
			[
				'label' => esc_html__( 'Items', 'bw-printxtore' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 10,
				'step' => 1,
				'default' => 3,
				'condition' => [
					'slider_auto' => '',
					'slider_items_custom' => '',
				]
			]
		);
		$this->add_control(
			'slider_items_custom',
			[
				'label' => esc_html__( 'Items custom by display', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'description'	=> esc_html__( 'Enter item for screen width(px) format is width:value and separate values by ",". Example is 0:1,375:2,991:3,1170:4', 'bw-printxtore' ),
				'default' => '',
				'condition' => [
					'slider_auto' => '',
				]
			]
		);

		$this->add_responsive_control(
			'slider_space',
			[
				'label' => esc_html__( 'Space(px)', 'bw-printxtore' ),
				'description'	=> esc_html__( 'For example: 20', 'bw-printxtore' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 200,
				'step' => 1,
				'default' => 0
			]
		);

		$this->add_control(
			'slider_column',
			[
				'label' => esc_html__( 'Row', 'bw-printxtore' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 10,
				'step' => 1,
				'default' => 1,
			]
		);

		$this->add_control(
			'slider_speed',
			[
				'label' => esc_html__( 'Speed(ms)', 'bw-printxtore' ),
				'description'	=> esc_html__( 'For example: 3000 or 5000', 'bw-printxtore' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1000,
				'max' => 10000,
				'step' => 100,
			]
		);		

		$this->add_control(
			'slider_auto',
			[
				'label' => esc_html__( 'Auto width', 'bw-printxtore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'bw-printxtore' ),
				'label_off' => esc_html__( 'Off', 'bw-printxtore' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'slider_center',
			[
				'label' => esc_html__( 'Center', 'bw-printxtore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'bw-printxtore' ),
				'label_off' => esc_html__( 'Off', 'bw-printxtore' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'slider_loop',
			[
				'label' => esc_html__( 'Loop', 'bw-printxtore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'bw-printxtore' ),
				'label_off' => esc_html__( 'Off', 'bw-printxtore' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'slider_navigation',
			[
				'label' 	=> esc_html__( 'Navigation', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => [
					''		=> esc_html__( 'None', 'bw-printxtore' ),
					'style1'		=> esc_html__( 'Style 1', 'bw-printxtore' ),
					'group'		=> esc_html__( 'Style 2 (Group right)', 'bw-printxtore' ),
					'group2'		=> esc_html__( 'Style 3 (Group center)', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Default custom', 'bw-printxtore' ),
				],
			]
		);
		$this->add_control(
			'slider_pagination',
			[
				'label' 	=> esc_html__( 'Pagination', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => [
					''		=> esc_html__( 'None', 'bw-printxtore' ),
					'style1'		=> esc_html__( 'Style 1 (Square)', 'bw-printxtore' ),
					'style2'		=> esc_html__( 'style 2 (Round)', 'bw-printxtore' ),
					'style3'		=> esc_html__( 'style 3 (Line)', 'bw-printxtore' ),
					'number'		=> esc_html__( 'style 4 (Number)', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Default custom', 'bw-printxtore' ),
				],
			]
		);
		$this->add_control(
			'slider_scrollbar',
			[
				'label' 	=> esc_html__( 'Scrollbar', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => [
					''		=> esc_html__( 'None', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Default custom', 'bw-printxtore' ),
				],
			]
		);
		$this->add_responsive_control(
			'slider_padding',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', ],
				'selectors' => [
					'{{WRAPPER}} .swiper-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
        );

        $this->add_responsive_control(
			'slider_margin',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', ],
				'selectors' => [
					'{{WRAPPER}} .swiper-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
		$this->end_controls_section();
	}

	public function get_box_image($key='box-key',$class="box-class") {
		$this->add_responsive_control(
			$key.'_padding',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', ],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
        );

        $this->add_responsive_control(
			$key.'_margin',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', ],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => $key.'_border',
				'selectors' => [
					'{{WRAPPER}} .'.$class.' .product-thumb-link',
					'{{WRAPPER}} .'.$class.' .product-thumb-link::before',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			$key.'_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .'.$class.' .product-thumb-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => $key.'_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .'.$class.' .product-thumb-link',
			]
		);
	}

	public function get_box_settings($key='box-key',$class="box-class") {

		$this->add_responsive_control(
			$key.'_padding_wrap',
			[
				'label' => esc_html__( 'Padding Column', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', ],
				'selectors' => [
					'{{WRAPPER}} .list-col-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};margin-bottom:0px;',
					'{{WRAPPER}} .list-product-wrap' => 'margin: -{{TOP}}{{UNIT}} -{{RIGHT}}{{UNIT}} -{{BOTTOM}}{{UNIT}} -{{LEFT}}{{UNIT}};clear: both;',
				],
			]
        );

		$this->add_responsive_control(
			$key.'_padding',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', ],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

        $this->add_responsive_control(
			$key.'_margin',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', ],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => $key.'_background',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic' ],
				'selector' => '{{WRAPPER}} .'.$class,
			]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => $key.'_border',
                'label' => esc_html__( 'Border', 'bw-printxtore' ),
                'separator' => 'before',
				'selector' => '{{WRAPPER}} .'.$class,
			]
        );

        $this->add_responsive_control(
			$key.'_radius',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => $key.'_shadow',
				'selector' => '{{WRAPPER}} .'.$class,
			]
		);
	}
	public function get_box_settings_info($key='box-key',$class="box-class") {

			$this->add_responsive_control(
				$key.'_padding',
				[
					'label' => esc_html__( 'Padding', 'bw-printxtore' ),
					'type' => Controls_Manager::DIMENSIONS,
	                'size_units' => [ 'px', ],
					'selectors' => [
						'{{WRAPPER}} .'.$class => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
	        );

	        $this->add_responsive_control(
				$key.'_margin',
				[
					'label' => esc_html__( 'Margin', 'bw-printxtore' ),
					'type' => Controls_Manager::DIMENSIONS,
	                'size_units' => [ 'px', ],
					'selectors' => [
						'{{WRAPPER}} .'.$class => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
	        );

	        $this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => $key.'_background',
					'label' => esc_html__( 'Background', 'bw-printxtore' ),
					'types' => [ 'classic' ],
					'selector' => '{{WRAPPER}} .'.$class,
				]
	        );

	        $this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => $key.'_border',
	                'label' => esc_html__( 'Border', 'bw-printxtore' ),
	                'separator' => 'before',
					'selector' => '{{WRAPPER}} .'.$class,
				]
	        );

	        $this->add_responsive_control(
				$key.'_radius',
				[
					'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .'.$class => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => $key.'_shadow',
					'selector' => '{{WRAPPER}} .'.$class,
				]
			);
		}

	public function get_slider_styles() {
		$this->start_controls_section(
			'section_style_slider_nav',
			[
				'label' => esc_html__( 'Slider Navigation', 'bw-printxtore' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'display' => ['elbzotech-product-slider','elbzotech-product-slider-masory'],
					'slider_navigation!' => '',
				]
			]
		);

		$this->add_responsive_control(
			'width_slider_nav',
			[
				'label' => esc_html__( 'Width', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-button-nav' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'height_slider_nav',
			[
				'label' => esc_html__( 'Height', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-button-nav' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .swiper-button-nav i' => 'line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'padding_slider_nav',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .swiper-button-nav' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'margin_slider_nav',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .swiper-button-nav' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'slider_nav_effects' );

		$this->start_controls_tab( 'slider_nav_normal',
			[
				'label' => esc_html__( 'Normal', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			'color_slider_nav',
			[
				'label' => esc_html__( 'Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-button-nav' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_slider_nav',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .swiper-button-nav',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'shadow_slider_nav',
				'selector' => '{{WRAPPER}} .swiper-button-nav',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_slider_nav',
				'selector' => '{{WRAPPER}} .swiper-button-nav',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'border_radius_slider_nav',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .swiper-button-nav' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'slider_nav_hover',
			[
				'label' => esc_html__( 'Hover', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			'color_slider_nav_hover',
			[
				'label' => esc_html__( 'Color hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-button-nav:hover' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_slider_nav_hover',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .swiper-button-nav:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'shadow_slider_nav_hover',
				'selector' => '{{WRAPPER}} .swiper-button-nav:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_slider_nav_hover',
				'selector' => '{{WRAPPER}} .swiper-button-nav:hover',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'border_radius_slider_nav_hover',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .swiper-button-nav:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();	

		$this->add_control(
			'separator_slider_nav',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->add_control(
			'slider_icon_next',
			[
				'label' => esc_html__( 'Icon next', 'bw-printxtore' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'las la-angle-right',
					'library' => 'solid',
				],
			]
		);
		$this->add_control(
			'slider_text_next',
			[
				'label' => esc_html__( 'Text next', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Next', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			'slider_icon_prev',
			[
				'label' => esc_html__( 'Icon prev', 'bw-printxtore' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'las la-angle-left',
					'library' => 'solid',
				],
			]
		);

		$this->add_control(
			'slider_text_prev',
			[
				'label' => esc_html__( 'Text prev', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Prev', 'bw-printxtore' ),
			]
		);
		$this->add_responsive_control(
			'slider_icon_size',
			[
				'label' => esc_html__( 'Size icon', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .swiper-button-nav i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .swiper-button-nav' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'slider_nav_space',
			[
				'label' => esc_html__( 'Space', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-button-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_slider_pag',
			[
				'label' => esc_html__( 'Slider Pagination', 'bw-printxtore' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'display' =>  ['elbzotech-product-slider','elbzotech-product-slider-masory'],
					'slider_pagination!' => '',
				]
			]
		);

		$this->add_responsive_control(
			'width_slider_pag',
			[
				'label' => esc_html__( 'Width', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination span' => 'width: {{SIZE}}{{UNIT}};',
				], 
			]
		);

		$this->add_responsive_control(
			'height_slider_pag',
			[
				'label' => esc_html__( 'Height', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination span' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'separator_bg_normal',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->add_control(
			'background_pag_heading',
			[
				'label' => esc_html__( 'Normal', 'bw-printxtore' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'none',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_slider_pag',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .swiper-pagination span',
			]
		);

		$this->add_control(
			'opacity_pag',
			[
				'label' => esc_html__( 'Opacity', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination span' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'separator_bg_active',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->add_control(
			'background_pag_heading_active',
			[
				'label' => esc_html__( 'Active', 'bw-printxtore' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'none',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_slider_pag_active',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'description'	=> esc_html__( 'Active status', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .swiper-pagination span.swiper-pagination-bullet-active',
			]
		);

		$this->add_control(
			'opacity_pag_active',
			[
				'label' => esc_html__( 'Opacity', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination span.swiper-pagination-bullet-active' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'separator_shadow',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'shadow_slider_pag',
				'selector' => '{{WRAPPER}} .swiper-pagination span',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_slider_pag',
				'selector' => '{{WRAPPER}} .swiper-pagination span',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'border_radius_slider_pag',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'slider_pag_space',
			[
				'label' => esc_html__( 'Space', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination' => 'bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

}