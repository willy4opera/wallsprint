<?php
namespace Elementor;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Elementor Hello World
 *
 * Elementor widget for hello world.
 *
 * @since 1.0.0
 */
class Bzotech_Slider_Global extends Widget_Base {

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
		return 'bzotech-slider_global';
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
		return esc_html__('Slider (Global)', 'bw-printxtore');
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
		return ['aqb-htelement-category'];
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
		return ['hello-world'];
	}

	public function get_style_depends() {
		return ['bzotech-el-slider'];
	}
	public function get_widget_css_config( $widget_name ) { 
	    $file_content_css = get_template_directory() . '/assets/global/css/elementor/slider.css';
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
		$slider_items_widescreen = $slider_items_laptop = $slider_items_tablet = $slider_items_tablet_extra = $slider_items_mobile_extra = $slider_items_mobile = $slider_space_widescreen = $slider_space_laptop = $slider_space_tablet_extra = $slider_space_tablet = $slider_space_mobile_extra = $slider_space_mobile = '';
		$settings = $this->get_settings();
		extract($settings);

		$this->add_render_attribute('elbzotech-wrapper', 'class', 'elbzotech-wrapper-slider-global elbzotech-wrapper-slider-global-' . $style . ' display-swiper-navi-' . $slider_navigation . ' display-swiper-pagination-' . $slider_pagination . ' display-swiper-scrollbar-' . $slider_scrollbar . ' auto-show-scrollbar-' . $auto_show_scrollbar . ' slider-type-' . $slider_type);
		if (!empty($slider_cursor_image['url'])) {
			$this->add_render_attribute('elbzotech-wrapper', 'class', 'cursor-active');
			$this->add_render_attribute('elbzotech-wrapper', 'style', '
cursor: url("' . $slider_cursor_image['url'] . '"), url("' . $slider_cursor_image['url'] . '"), move;');
		}

		$this->add_render_attribute('elbzotech-wrapper-slider', 'class', 'elbzotech-swiper-slider ' . $slider_bg_style . '  swiper-container slider-wrap popup-gallery');

		$this->add_render_attribute('elbzotech-wrapper-slider', 'data-items-custom', $slider_items_custom);
		$this->add_render_attribute('elbzotech-wrapper-slider', 'data-items', $slider_items);
		$this->add_render_attribute('elbzotech-wrapper-slider', 'data-items-widescreen', $slider_items_widescreen);
		$this->add_render_attribute('elbzotech-wrapper-slider', 'data-items-laptop', $slider_items_laptop);
		$this->add_render_attribute('elbzotech-wrapper-slider', 'data-items-tablet-extra', $slider_items_tablet_extra);
		$this->add_render_attribute('elbzotech-wrapper-slider', 'data-items-tablet', $slider_items_tablet);
		$this->add_render_attribute('elbzotech-wrapper-slider', 'data-items-mobile-extra', $slider_items_mobile_extra);
		$this->add_render_attribute('elbzotech-wrapper-slider', 'data-items-mobile', $slider_items_mobile);
		$this->add_render_attribute('elbzotech-wrapper-slider', 'data-space', $slider_space);
		$this->add_render_attribute('elbzotech-wrapper-slider', 'data-space-widescreen', $slider_space_widescreen);
		$this->add_render_attribute('elbzotech-wrapper-slider', 'data-space-laptop', $slider_space_laptop);
		$this->add_render_attribute('elbzotech-wrapper-slider', 'data-space-tablet-extra', $slider_space_tablet_extra);
		$this->add_render_attribute('elbzotech-wrapper-slider', 'data-space-tablet', $slider_space_tablet);
		$this->add_render_attribute('elbzotech-wrapper-slider', 'data-space-mobile-extra', $slider_space_mobile_extra);
		$this->add_render_attribute('elbzotech-wrapper-slider', 'data-space-mobile', $slider_space_mobile);

		$this->add_render_attribute('elbzotech-wrapper-slider', 'data-column', $slider_column);
		$this->add_render_attribute('elbzotech-wrapper-slider', 'data-auto', $slider_auto);
		$this->add_render_attribute('elbzotech-wrapper-slider', 'data-center', $slider_center);
		$this->add_render_attribute('elbzotech-wrapper-slider', 'data-loop', $slider_loop);
		$this->add_render_attribute('elbzotech-wrapper-slider', 'data-speed', $slider_speed);
		$this->add_render_attribute('elbzotech-wrapper-slider', 'data-navigation', $slider_navigation);
		$this->add_render_attribute('elbzotech-wrapper-slider', 'data-pagination', $slider_pagination);
		$this->add_render_attribute('elbzotech-wrapper-slider', 'data-slidertype', $slider_type);
		$this->add_render_attribute('elbzotech-inner', 'class', 'swiper-wrapper');
		$this->add_render_attribute('elbzotech-item', 'class', 'swiper-slide');

		$attr = array(
			'wdata' => $this,
			'settings' => $settings,
		);
		echo bzotech_get_template_elementor_global('slider/slider', $settings['style'], $attr);
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
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__('Content', 'bw-printxtore'),
			]
		);

		$this->add_control(
			'style',
			[
				'label' => esc_html__('Style', 'bw-printxtore'),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__('Default', 'bw-printxtore'),
					'category2' => esc_html__('Category ', 'bw-printxtore'),
					'category' => esc_html__('Category (Home 2)', 'bw-printxtore'),
					'category4' => esc_html__('Category (Home 5)', 'bw-printxtore'),
					'category5' => esc_html__('Category (Home 8)', 'bw-printxtore'),
					'testimonial' => esc_html__('Testimonial', 'bw-printxtore'),
					'testimonial2' => esc_html__('Testimonial 2', 'bw-printxtore'),
					'testimonial3' => esc_html__('Testimonial 3', 'bw-printxtore'),
					'testimonial4' => esc_html__('Testimonial 4 (Home 5)', 'bw-printxtore'),
					'testimonial5' => esc_html__('Testimonial 5 (Home 8)', 'bw-printxtore'),
					'banner-project'=> esc_html__('Banner project', 'bw-printxtore'),
					'accordion' => esc_html__('Slider Accordion (Home 2)', 'bw-printxtore'),
				],
			]
		);

		/* 1, $key : type string 2, $condition :
		$category=$image=$title=$desc=$content=$button=$link=$image_action=$star=$button2=$countdown_number = $countdown_after_number =$countdown_title =$countdown_number2 =$countdown_after_number2 =$countdown_title2 = false
		 */
		$this->get_list_item_slider('list_sliders', array('style' => ''), array('image' => true, 'content' => true, 'link' => true, 'image_action' => 'true'));

		$this->get_list_item_slider('list_categories2', array('style' => 'category'), array('title' => true, 'image' => true, 'link' => true, 'category' => 'product_cat'));

		$this->get_list_item_slider('list_categories3', array('style' => 'category2'), array('title' => true, 'image' => true, 'image_hover' => true, 'link' => true, 'category' => 'product_cat'));
		$this->get_list_item_slider('list_categories4', array('style' => 'category4'), array('title' => true, 'image' => true, 'image_hover' => true, 'link' => true, 'category' => 'product_cat'));
		$this->get_list_item_slider('list_categories5', array('style' => 'category5'), array('title' => true, 'image' => true, 'image_hover' => true, 'link' => true, 'category' => 'product_cat'));

		$this->get_list_item_slider('list_testimonial', array('style' => 'testimonial'), array('title' => true, 'desc' => true, 'content' => true, 'image' => true, 'link' => true, 'image_action' => true, 'star' => true));

		$this->get_list_item_slider('list_testimonial2', array('style' => 'testimonial2'), array('title' => true, 'desc' => true, 'content' => true, 'image' => true, 'link' => true, 'image_action' => true, 'star' => true));
		$this->get_list_item_slider('list_testimonial3', array('style' => 'testimonial3'), array('title' => true, 'desc' => true, 'content' => true, 'image' => true, 'link' => true, 'image_action' => true, 'star' => true));
		$this->get_list_item_slider('list_testimonial4', array('style' => 'testimonial4'), array('title' => true, 'desc' => true, 'content' => true, 'image' => true, 'link' => true, 'image_action' => true, 'star' => true));
		$this->get_list_item_slider('list_testimonial5', array('style' => 'testimonial5'), array('title' => true, 'desc' => true, 'content' => true, 'image' => true, 'image2' => true, 'link' => true, 'image_action' => true));

		$this->get_list_item_slider('brand_slider', array('style' => 'brands'), array('title' => true, 'image' => true, 'link' => true, 'category' => 'brand_woo'));

		$this->get_list_item_slider('slider_accordion', array('style' => 'accordion'), array('title' => true, 'desc' => true, 'image' => true, 'link' => true));
		$this->get_list_item_slider('banner_project', array('style' => 'banner-project'), array('title' => true, 'image' => true, 'image_action' => true, 'link' => true));

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__('Alignment', 'bw-printxtore'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'bw-printxtore'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bw-printxtore'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'bw-printxtore'),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__('Justified', 'bw-printxtore'),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-container' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->get_slider_settings(['style!' => 'accordion']);
		$this->get_accordion_slider_settings(['style' => 'accordion']);

		$this->start_controls_section(
			'section_style_image',
			[
				'label' => esc_html__('Image', 'bw-printxtore'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'slider_bg_style',
			[
				'label' => esc_html__('Image style', 'bw-printxtore'),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__('Default', 'bw-printxtore'),
					'bg-slider-swiper' => esc_html__('Background slider', 'bw-printxtore'),
					'bg-slider-swiper parallax-slider' => esc_html__('Background parallax', 'bw-printxtore'),
				],
			]
		);
		$this->add_responsive_control(
			'width_image_style_default',
			[
				'label' => esc_html__('Width image', 'bw-printxtore'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-swiper-slider- .swiper-thumb img' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'slider_bg_style' => '',
				],
			]
		);

		$this->get_thumb_styles('image', 'adv-thumb-link');

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label' => esc_html__('Title', 'bw-printxtore'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->get_text_styles('title', 'item-title a');

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_des',
			[
				'label' => esc_html__('Description', 'bw-printxtore'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->get_text_styles('des', 'item-des');

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__('Content text', 'bw-printxtore'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->get_text_styles('content', 'item-content');

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content_box',
			[
				'label' => esc_html__('Content box', 'bw-printxtore'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->get_box_settings('content_box', 'content-wrap');

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_box',
			[
				'label' => esc_html__('Box item', 'bw-printxtore'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->get_box_settings('box', 'wslider-item');

		$this->end_controls_section();

		$this->get_slider_styles();
	}
	public function get_slider_settings($condition = array()) {
		$this->start_controls_section(
			'section_slider',
			[
				'label' => esc_html__('Slider', 'bw-printxtore'),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => $condition, 
			]
		);
		$this->add_responsive_control(
			'slider_items',
			[
				'label' => esc_html__('Items', 'bw-printxtore'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 10,
				'step' => 1,
				'default' => 3,
				'condition' => [
					'slider_auto' => '',
					'slider_items_custom' => '',
				],
			]
		);
		$this->add_control(
			'slider_items_custom',
			[
				'label' => esc_html__('Items custom by display', 'bw-printxtore'),
				'type' => Controls_Manager::TEXT,
				'description' => esc_html__('Enter item for screen width(px) format is width:value and separate values by ",". Example is 0:1,375:2,991:3,1170:4', 'bw-printxtore'),
				'default' => '',
				'condition' => [
					'slider_auto' => '',
				],
			]
		);
		$this->add_responsive_control(
			'slider_space',
			[
				'label' => esc_html__('Space(px)', 'bw-printxtore'),
				'description' => esc_html__('For example: 20', 'bw-printxtore'),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 200,
				'step' => 1,
				'default' => 0,
			]
		);

		$this->add_control(
			'slider_column',
			[
				'label' => esc_html__('Columns', 'bw-printxtore'),
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
				'label' => esc_html__('Speed(ms)', 'bw-printxtore'),
				'description' => esc_html__('For example: 3000 or 5000', 'bw-printxtore'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1000,
				'max' => 50000,
				'step' => 100,
			]
		);

		$this->add_control(
			'slider_auto',
			[
				'label' => esc_html__('Auto width', 'bw-printxtore'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'bw-printxtore'),
				'label_off' => esc_html__('Off', 'bw-printxtore'),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'slider_center',
			[
				'label' => esc_html__('Center', 'bw-printxtore'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'bw-printxtore'),
				'label_off' => esc_html__('Off', 'bw-printxtore'),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'slider_loop',
			[
				'label' => esc_html__('Loop', 'bw-printxtore'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'bw-printxtore'),
				'label_off' => esc_html__('Off', 'bw-printxtore'),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'slider_navigation',
			[
				'label' => esc_html__('Navigation', 'bw-printxtore'),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__('None', 'bw-printxtore'),
					'style1' => esc_html__('Style 1', 'bw-printxtore'),
					'group' => esc_html__('Style 2 (Group right)', 'bw-printxtore'),
					'group2' => esc_html__('Style 3 (Group center)', 'bw-printxtore'),
					'yes' => esc_html__('Default custom', 'bw-printxtore'),
				],
			]
		);
		$this->add_control(
			'slider_pagination',
			[
				'label' => esc_html__('Pagination', 'bw-printxtore'),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__('None', 'bw-printxtore'),
					'style1' => esc_html__('Style 1 (Square)', 'bw-printxtore'),
					'style2' => esc_html__('style 2 (Round)', 'bw-printxtore'),
					'style3' => esc_html__('style 3 (Line)', 'bw-printxtore'),
					'style4' => esc_html__('style 4 (Line white)', 'bw-printxtore'),
					'number' => esc_html__('style 5 (Number)', 'bw-printxtore'),
					'yes' => esc_html__('Default custom', 'bw-printxtore'),
				],
			]
		);
		$this->add_control(
			'slider_scrollbar',
			[
				'label' => esc_html__('Scrollbar', 'bw-printxtore'),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__('None', 'bw-printxtore'),
					'yes' => esc_html__('Default custom', 'bw-printxtore'),
				],
			]
		);
		$this->add_control(
			'slider_type',
			[
				'label' => esc_html__('Slider type', 'bw-printxtore'),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'description' => esc_html__('Set up slider according to the available template', 'bw-printxtore'),
				'options' => [
					'' => esc_html__('None', 'bw-printxtore'),
					'marquee' => esc_html__('Marquee type', 'bw-printxtore'),
				],
			]
		);
		$this->add_control(
			'slider_cursor_image',
			[
				'label' => esc_html__('Cursor image', 'bw-printxtore'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
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
	public function get_accordion_slider_settings($condition = array()) {
		$this->start_controls_section(
			'section_accordion_slider',
			[
				'label' => esc_html__('Accordion Slider', 'bw-printxtore'),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => $condition,
			]
		);
		$this->add_control(
			'accordion_slider_width',
			[
				'label' => esc_html__('Width(px)', 'bw-printxtore'),
				'description' => esc_html__('For example: 1000', 'bw-printxtore'),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 1,
				'default' => 1000,
			]
		);
		$this->add_control(
			'accordion_slider_height',
			[
				'label' => esc_html__('Height(px)', 'bw-printxtore'),
				'description' => esc_html__('For example: 1000', 'bw-printxtore'),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 1,
				'default' => 620,
			]
		);
		$this->add_control(
			'accordion_slider_responsivemode',
			[
				'label' => esc_html__('Responsive Mode', 'bw-printxtore'),
				'type' => Controls_Manager::SELECT,
				'default' => 'auto',
				'options' => [
					'auto' => esc_html__('Auto', 'bw-printxtore'),
					'custom' => esc_html__('Custom', 'bw-printxtore'),
				],
			]
		);
		$this->add_control(
			'accordion_slider_visiblepanels',
			[
				'label' => esc_html__('Visible Panels', 'bw-printxtore'),
				'type' => Controls_Manager::SLIDER,
				'description' => esc_html__('Indicates the number of panels visible per page. If set to -1, all the panels will be displayed on one page.', 'bw-printxtore'),
				'range' => [
					'px' => [
						'max' => 10,
						'min' => -1,
						'step' => 1,
					],
				],
			]
		);

		$this->add_control(
			'accordion_slider_autoplay',
			[
				'label' => esc_html__('autoplay', 'bw-printxtore'),
				'type' => Controls_Manager::SELECT,
				'default' => 'false',
				'description' => esc_html__('Indicates if the autoplay will be enabled.', 'bw-printxtore'),
				'options' => [
					'false' => esc_html__('False', 'bw-printxtore'),
					'true' => esc_html__('True', 'bw-printxtore'),
				],
			]
		);
		$this->add_control(
			'accordion_slider_startpanel',
			[
				'label' => esc_html__('Start Panel', 'bw-printxtore'),
				'description' => esc_html__('Indicates which panel will be opened when the accordion loads (0 for the first panel, 1 for the second panel, etc.). If set to -1, no panel will be opened.', 'bw-printxtore'),
				'type' => Controls_Manager::NUMBER,
				'min' => -1,
				'step' => 1,
				'default' => 0,
			]
		);
		$this->end_controls_section();
	}

	public function get_thumb_styles($key = 'thumb', $class = "thumb-image") {
		$this->start_controls_tabs($key . '_effects');

		$this->start_controls_tab('normal',
			[
				'label' => esc_html__('Normal', 'bw-printxtore'),
			]
		);

		$this->add_control(
			$key . '_opacity',
			[
				'label' => esc_html__('Opacity', 'bw-printxtore'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .' . $class . ' img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => $key . '_css_filters',
				'selector' => '{{WRAPPER}} .' . $class . ' img',
			]
		);

		$this->add_control(
			$key . '_overlay',
			[
				'label' => esc_html__('Overlay', 'bw-printxtore'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .' . $class . ':after' => 'background-color: {{VALUE}}; opacity: 1; visibility: visible;',
				],
			]
		);
		// get_box_image
		$this->add_responsive_control(
			$key . '_padding',
			[
				'label' => esc_html__('Padding', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .' . $class . ' img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			$key . '_margin',
			[
				'label' => esc_html__('Margin', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .' . $class . ' img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => $key . '_border',
				'selector' => '{{WRAPPER}} .' . $class . ' img',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			$key . '_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .' . $class . ' img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => $key . '_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .' . $class . ' img',
			]
		);
		// end get_box_image
		$this->end_controls_tab();

		$this->start_controls_tab('hover',
			[
				'label' => esc_html__('Hover', 'bw-printxtore'),
			]
		);

		$this->add_control(
			$key . '_opacity_hover',
			[
				'label' => esc_html__('Opacity hover', 'bw-printxtore'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .' . $class . ':hover img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => $key . '_css_filters_hover',
				'label' => esc_html__('Filters hover', 'bw-printxtore'),
				'selector' => '{{WRAPPER}} .' . $class . ':hover img',
			]
		);

		$this->add_control(
			$key . '_overlay_hover',
			[
				'label' => esc_html__('Overlay hover', 'bw-printxtore'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .' . $class . ':hover img' => 'background-color: {{VALUE}}; opacity: 1; visibility: visible;',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => $key . '_border_hover',
				'label' => esc_html__('Border hover', 'bw-printxtore'),
				'selector' => '{{WRAPPER}} .' . $class . ':hover img',

			]
		);

		$this->add_responsive_control(
			$key . '_border_radius_hover',
			[
				'label' => esc_html__('Border Radius hover', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .' . $class . ':hover img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			$key . '_background_hover_transition',
			[
				'label' => esc_html__('Transition Duration hover', 'bw-printxtore'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .' . $class . ':hover img' => 'transition-duration: {{SIZE}}s',
					'{{WRAPPER}} .' . $class . ':hover .adv-thumb-link::after' => 'transition-duration: {{SIZE}}s',
					'{{WRAPPER}} .' . $class . ':hover .adv-thumb-link' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->add_control(
			$key . '_hover_animation',
			[
				'label' => esc_html__('Hover Animation', 'bw-printxtore'),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
	}

	public function get_box_image($key = 'box-key', $class = "box-class") {
		$this->add_responsive_control(
			$key . '_padding',
			[
				'label' => esc_html__('Padding', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .' . $class . ' img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			$key . '_margin',
			[
				'label' => esc_html__('Margin', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .' . $class . ' img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => $key . '_border',
				'selector' => '{{WRAPPER}} .' . $class . ' img',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			$key . '_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .' . $class . ' img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => $key . '_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .' . $class . ' img',
			]
		);
	}

	public function get_text_styles($key = 'text', $class = "text-class") {
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => $key . '_typography',
				'selector' => '{{WRAPPER}} .' . $class,
			]
		);

		$this->start_controls_tabs($key . '_effects');

		$this->start_controls_tab($key . '_normal',
			[
				'label' => esc_html__('Normal', 'bw-printxtore'),
			]
		);

		$this->add_control(
			$key . '_color',
			[
				'label' => esc_html__('Color', 'bw-printxtore'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .' . $class => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => $key . '_shadow',
				'selector' => '{{WRAPPER}} .' . $class,
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab($key . '_hover',
			[
				'label' => esc_html__('Hover', 'bw-printxtore'),
			]
		);

		$this->add_control(
			$key . '_color_hover',
			[
				'label' => esc_html__('Color', 'bw-printxtore'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .' . $class . ':hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => $key . '_shadow_hover',
				'selector' => '{{WRAPPER}} .' . $class . ':hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			$key . '_space',
			[
				'label' => esc_html__('Space', 'bw-printxtore'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -300,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .' . $class => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

	}

	public function get_box_settings($key = 'box-key', $class = "box-class") {
		$this->add_responsive_control(
			$key . '_padding',
			[
				'label' => esc_html__('Padding', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .' . $class => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			$key . '_margin',
			[
				'label' => esc_html__('Margin', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .' . $class => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => $key . '_background',
				'label' => esc_html__('Background', 'bw-printxtore'),
				'types' => ['classic'],
				'selector' => '{{WRAPPER}} .' . $class,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => $key . '_border',
				'label' => esc_html__('Border', 'bw-printxtore'),
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .' . $class,
			]
		);

		$this->add_responsive_control(
			$key . '_radius',
			[
				'label' => esc_html__('Border Radius', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .' . $class => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => $key . '_shadow',
				'selector' => '{{WRAPPER}} .' . $class,
			]
		);
	}
	public function get_list_item_slider($key = 'list_sliders', $condition = array(), $attr = []) {
		$category = $image =  $image2 = $image_hover = $title = $desc = $content = $button = $link = $image_action = $star = $button2 = $countdown_number = $countdown_after_number = $countdown_title = $countdown_number2 = $countdown_after_number2 = $countdown_title2 = false;

		extract($attr);
		$repeater_sliders = new Repeater();
		$repeater_sliders->add_control(
			'template',
			[
				'label' => esc_html__('Template content', 'bw-printxtore'),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => bzotech_list_post_type('bzotech_mega_item', true),
			]
		);
		if ($category == true) {
			$repeater_sliders->add_control(
				'category',
				[
					'label' => esc_html__('Get category', 'bw-printxtore'),
					'description' => esc_html__('You can change the display category here', 'bw-printxtore'),
					'type' => Controls_Manager::SELECT,
					'label_block' => true,
					'options' => bzotech_get_list_category($category),

				]
			);
		}

		if ($image == true) {
			$repeater_sliders->add_control(
				'image',
				[
					'label' => esc_html__('Choose Image', 'bw-printxtore'),
					'type' => Controls_Manager::MEDIA,
					'dynamic' => [
						'active' => true,
					],
					'default' => [
						'url' => '',
					],
					'condition' => [
						'template' => '',
					],
				]
			);
		}

		if ($image_hover == true) {
			$repeater_sliders->add_control(
				'image_hover',
				[
					'label' => esc_html__('Image hover', 'bw-printxtore'),
					'type' => Controls_Manager::MEDIA,
					'dynamic' => [
						'active' => true,
					],
					'default' => [
						'url' => '',
					],
					'condition' => [
						'template' => '',
						'image[url]!' => '',
					],
				]
			);
		}
		if ($image2 == true) {
			$repeater_sliders->add_control(
				'image2',
				[
					'label' => esc_html__('Image2', 'bw-printxtore'),
					'type' => Controls_Manager::MEDIA,
					'dynamic' => [
						'active' => true,
					],
					'default' => [
						'url' => '',
					],
					'condition' => [
						'template' => '',
					],
				]
			);
		}

		if ($image == true) {
			$repeater_sliders->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name' => 'thumbnail', 
					'include' => [],
					'default' => 'full',
					'condition' => [
						'template' => '',
					],
				]
			);
		}

		if ($title == true) {
			$repeater_sliders->add_control(
				'title',
				[
					'label' => esc_html__('Title', 'bw-printxtore'),
					'type' => Controls_Manager::TEXT,
					'default' => '',
					'label_block' => true,
					'condition' => [
						'template' => '',
					],
				]
			);
		}

		if ($desc == true) {
			$repeater_sliders->add_control(
				'description',
				[
					'label' => esc_html__('Description', 'bw-printxtore'),
					'type' => Controls_Manager::TEXT,
					'default' => '',
					'label_block' => true,
					'condition' => [
						'template' => '',
					],
				]
			);
		}

		if ($content == true) {
			$repeater_sliders->add_control(
				'content',
				[
					'label' => esc_html__('Content', 'bw-printxtore'),
					'type' => Controls_Manager::WYSIWYG,
					'default' => '',
					'condition' => [
						'template' => '',
					],
				]
			);
		}

		if ($button == true) {
			$repeater_sliders->add_control(
				'button_name',
				[
					'label' => esc_html__('Button name', 'bw-printxtore'),
					'type' => Controls_Manager::TEXT,
					'default' => '',
					'label_block' => true,
					'condition' => [
						'template' => '',
					],
				]
			);
		}

		if ($button2 == true) {
			$repeater_sliders->add_control(
				'button_name2',
				[
					'label' => esc_html__('Button name 2', 'bw-printxtore'),
					'type' => Controls_Manager::TEXT,
					'default' => '',
					'label_block' => true,
					'condition' => [
						'template' => '',
					],
				]
			);
		}

		if ($image_action == true) {
			$repeater_sliders->add_control(
				'image_action',
				[
					'label' => esc_html__('Action of the image', 'bw-printxtore'),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'' => esc_html__('Link', 'bw-printxtore'),
						'popup' => esc_html__('Popup', 'bw-printxtore'),

					],
					'default' => '',
				]
			);
		}

		if ($link == true) {
			$repeater_sliders->add_control(
				'link',
				[
					'label' => esc_html__('Link', 'bw-printxtore'),
					'type' => Controls_Manager::URL,
					'placeholder' => esc_html__('https://your-link.com', 'bw-printxtore'),
					'show_external' => true,
					'default' => [
						'url' => '',
						'is_external' => false,
						'nofollow' => false,
					],
				]
			);
		}

		if ($star == true) {
			$repeater_sliders->add_control(
				'number_star',
				[
					'label' => esc_html__('Number star', 'bw-printxtore'),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'' => esc_html__('None', 'bw-printxtore'),
						'1' => esc_html__('1 star', 'bw-printxtore'),
						'2' => esc_html__('2 star', 'bw-printxtore'),
						'3' => esc_html__('3 star', 'bw-printxtore'),
						'4' => esc_html__('4 star', 'bw-printxtore'),
						'5' => esc_html__('5 star', 'bw-printxtore'),

					],
					'default' => '5',
				]
			);
		}

		if ($countdown_number == true) {
			$repeater_sliders->add_control(
				'countdown_number',
				[
					'label' => esc_html__('Countdown number', 'bw-printxtore'),
					'type' => Controls_Manager::NUMBER,
					'default' => '12',
				]
			);
		}

		if ($countdown_after_number == true) {
			$repeater_sliders->add_control(
				'countdown_after_number',
				[
					'label' => esc_html__('After countdown number', 'bw-printxtore'),
					'type' => Controls_Manager::TEXT,
				]
			);
		}

		if ($countdown_title == true) {
			$repeater_sliders->add_control(
				'countdown_title',
				[
					'label' => esc_html__('Title countdown', 'bw-printxtore'),
					'type' => Controls_Manager::TEXT,
				]
			);
		}

		if ($countdown_number2 == true) {
			$repeater_sliders->add_control(
				'countdown_number2',
				[
					'label' => esc_html__('Countdown number 2', 'bw-printxtore'),
					'type' => Controls_Manager::NUMBER,
					'default' => '12',
				]
			);
		}

		if ($countdown_after_number2 == true) {
			$repeater_sliders->add_control(
				'countdown_after_number2',
				[
					'label' => esc_html__('After countdown number 2', 'bw-printxtore'),
					'type' => Controls_Manager::TEXT,
				]
			);
		}

		if ($countdown_title2 == true) {
			$repeater_sliders->add_control(
				'countdown_title2',
				[
					'label' => esc_html__('Title countdown 2', 'bw-printxtore'),
					'type' => Controls_Manager::TEXT,
				]
			);
		}

		$this->add_control(
			$key,
			[
				'label' => esc_html__('Add slide item', 'bw-printxtore'),
				'type' => Controls_Manager::REPEATER,
				'prevent_empty' => false,
				'fields' => $repeater_sliders->get_controls(),
				'title_field' => esc_html__('Item', 'bw-printxtore'),
				'condition' => $condition,
			]
		);

	}

	public function get_slider_styles() {
		$this->start_controls_section(
			'section_style_slider_nav',
			[
				'label' => esc_html__('Slider Navigation', 'bw-printxtore'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'slider_navigation!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'width_slider_nav',
			[
				'label' => esc_html__('Width', 'bw-printxtore'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
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
				'label' => esc_html__('Height', 'bw-printxtore'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
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
				'label' => esc_html__('Padding', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .swiper-button-nav' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'margin_slider_nav',
			[
				'label' => esc_html__('Margin', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .swiper-button-nav' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('slider_nav_effects');

		$this->start_controls_tab('slider_nav_normal',
			[
				'label' => esc_html__('Normal', 'bw-printxtore'),
			]
		);
		$this->add_control(
			'color_slider_nav',
			[
				'label' => esc_html__('Color', 'bw-printxtore'),
				'type' => Controls_Manager::COLOR,
				'selectors' => ['{{WRAPPER}} .swiper-button-nav' => 'color: {{VALUE}};'],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_slider_nav',
				'label' => esc_html__('Background', 'bw-printxtore'),
				'types' => ['classic', 'gradient', 'video'],
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
				'label' => esc_html__('Border Radius', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .swiper-button-nav' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('slider_nav_hover',
			[
				'label' => esc_html__('Hover', 'bw-printxtore'),
			]
		);
		$this->add_control(
			'color_slider_nav_hover',
			[
				'label' => esc_html__('Color hover', 'bw-printxtore'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-button-nav:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_slider_nav_hover',
				'label' => esc_html__('Background', 'bw-printxtore'),
				'types' => ['classic', 'gradient', 'video'],
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
				'label' => esc_html__('Border Radius', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
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
				'label' => esc_html__('Icon next', 'bw-printxtore'),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'las la-long-arrow-alt-right',
					'library' => 'solid',
				],
			]
		);

		$this->add_control(
			'slider_icon_prev',
			[
				'label' => esc_html__('Icon prev', 'bw-printxtore'),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'las la-long-arrow-alt-left',
					'library' => 'solid',
				],
			]
		);

		$this->add_responsive_control(
			'slider_icon_size',
			[
				'label' => esc_html__('Size icon', 'bw-printxtore'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .swiper-button-nav i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'slider_nav_space',
			[
				'label' => esc_html__('Space', 'bw-printxtore'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
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
				'label' => esc_html__('Slider Pagination', 'bw-printxtore'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'slider_pagination!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'width_slider_pag',
			[
				'label' => esc_html__('Width', 'bw-printxtore'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
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
				'label' => esc_html__('Height', 'bw-printxtore'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
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
				'label' => esc_html__('Normal', 'bw-printxtore'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'none',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_slider_pag',
				'label' => esc_html__('Background', 'bw-printxtore'),
				'types' => ['classic', 'gradient', 'video'],
				'selector' => '{{WRAPPER}} .swiper-pagination span',
			]
		);

		$this->add_control(
			'opacity_pag',
			[
				'label' => esc_html__('Opacity', 'bw-printxtore'),
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
				'label' => esc_html__('Active', 'bw-printxtore'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'none',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_slider_pag_active',
				'label' => esc_html__('Background', 'bw-printxtore'),
				'description' => esc_html__('Active status', 'bw-printxtore'),
				'types' => ['classic', 'gradient', 'video'],
				'selector' => '{{WRAPPER}} .swiper-pagination span.swiper-pagination-bullet-active',
			]
		);

		$this->add_control(
			'opacity_pag_active',
			[
				'label' => esc_html__('Opacity', 'bw-printxtore'),
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
				'label' => esc_html__('Border Radius', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'slider_pag_space',
			[
				'label' => esc_html__('Space top bottom', 'bw-printxtore'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
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
		$this->add_responsive_control(
			'slider_pag_space_item',
			[
				'label' => esc_html__('Space item', 'bw-printxtore'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -10,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'magin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .swiper-pagination-bullet:last-child' => 'magin-right: 0px;',
				],
			]
		);
		$this->add_control(
			'slider_pag_position',
			[
				'label' => esc_html__('Position', 'bw-printxtore'),
				'type' => Controls_Manager::SELECT,
				'default' => 'center',
				'options' => [
					'left' => esc_html__('Left', 'bw-printxtore'),
					'center' => esc_html__('Center', 'bw-printxtore'),
					'right' => esc_html__('Right', 'bw-printxtore'),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_slider_scrollbar',
			[
				'label' => esc_html__('Slider Scrollbar', 'bw-printxtore'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'slider_scrollbar!' => '',
				],
			]
		);
		$this->add_control(
			'auto_show_scrollbar',
			[
				'label' => esc_html__('Auto show scrollbar', 'bw-printxtore'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'bw-printxtore'),
				'label_off' => esc_html__('Off', 'bw-printxtore'),
				'return_value' => 'yes',
				'default' => '',
			]
		);
		$this->add_responsive_control(
			'height_slider_scrollbar',
			[
				'label' => esc_html__('Height', 'bw-printxtore'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
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
				'label' => esc_html__('Background scrollbar', 'bw-printxtore'),
				'types' => ['classic'],
				'selector' => '{{WRAPPER}} .swiper-scrollbar',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'color_slider_scrollbar',
				'label' => esc_html__('Color scrollbar', 'bw-printxtore'),
				'types' => ['classic'],
				'selector' => '{{WRAPPER}} .swiper-scrollbar>div',
			]
		);

		$this->add_responsive_control(
			'border_slider_scrollbar',
			[
				'label' => esc_html__('Border radius scrollbar', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .swiper-scrollbar>div' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .swiper-scrollbar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'slider_scrollbar_margin',
			[
				'label' => esc_html__('Margin', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .swiper-scrollbar ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}
}