<?php
namespace Elementor;
use WP_Query;
if ( ! defined( 'ABSPATH' ) ) exit; 

/**
 * Elementor Hello World
 *
 * Elementor widget for hello world.
 *
 * @since 1.0.0
 */

class Bzotech_Posts_Global extends Widget_Base {

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
		return 'bzotech-posts-global';
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
		return esc_html__( 'Posts (Global)', 'bw-printxtore' );
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
		return 'eicon-post';
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
		return [ 'bzotech-el-post' ];
	}
	public function get_widget_css_config( $widget_name ) { 
	    $file_content_css = get_template_directory() . '/assets/global/css/elementor/post.css';
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
	public function get_button_styles($key='button', $class="btn-class") {

		$this->add_control(
			$key.'_text', 
			[
				'label' => esc_html__( 'Text', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Read more' , 'bw-printxtore' ),
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
	public function get_text_meta_styles($key='text', $class="text-class") {
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => $key.'_typography',
				'selector' => '{{WRAPPER}} .meta-item > *'
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
					'{{WRAPPER}} .meta-item span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .meta-item' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => $key.'_shadow',
				'selector' => '{{WRAPPER}} .meta-item > *'
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
					'{{WRAPPER}} .meta-item:hover' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .'.$class.' .adv-thumb-link:after' => 'background-color: {{VALUE}}; opacity: 1; visibility: visible;',
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
					'{{WRAPPER}} .'.$class.' img:hover' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => $key.'_css_filters_hover',
				'selector' => '{{WRAPPER}} .'.$class.' img:hover',
			]
		);

		$this->add_control(
			$key.'_overlay_hover',
			[
				'label' => esc_html__( 'Overlay', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .'.$class.':hover .adv-thumb-link:after' => 'background-color: {{VALUE}}; opacity: 1; visibility: visible;',
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
					'{{WRAPPER}} .'.$class.' .adv-thumb-link::after' => 'transition-duration: {{SIZE}}s',
					'{{WRAPPER}} .'.$class.' .adv-thumb-link' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->add_control(
			$key.'_hover_animation',
			[
				'label' => esc_html__( 'Hover Animation', 'bw-printxtore' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
	}

	public function get_slider_settings() {
		$this->start_controls_section(
			'section_slider',
			[
				'label' => esc_html__( 'Slider', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'display' => ['elbzotech-post-slider','elbzotech-post-slider2'],
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
				'label' => esc_html__( 'Columns', 'bw-printxtore' ),
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
				'label' => esc_html__( 'Auto play', 'bw-printxtore' ),
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
		$this->add_control(
			'slider_overflow_css',
			[
				'label' 	=> esc_html__( 'Overflow', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => [
					''		=> esc_html__( 'default', 'bw-printxtore' ),
					'inherit'		=> esc_html__( 'Inherit', 'bw-printxtore' ),
					'hidden'		=> esc_html__( 'Hidden', 'bw-printxtore' ),
				],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-swiper-slider' => 'overflow: {{VALUE}};',
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
				'selector' => '{{WRAPPER}} .'.$class.' img',
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
					'{{WRAPPER}} .'.$class.' img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .'.$class.' img',
			]
		);
	}

	public function get_box_settings($key='box-key',$class="box-class") {
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
					'display' => ['elbzotech-post-slider','elbzotech-post-slider2'],
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
				'label' => esc_html__('Color', 'bw-printxtore'),
				'type' => Controls_Manager::COLOR,
				'selectors' => ['{{WRAPPER}} .swiper-button-nav' => 'color: {{VALUE}};'],
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

		$this->add_responsive_control(
			'slider_icon_size',
			[
				'label' => esc_html__( 'Size icon', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .swiper-button-nav i' => 'font-size: {{SIZE}}{{UNIT}};',
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
					'display' => ['elbzotech-post-slider','elbzotech-post-slider2'],
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

	/**
	 * Whether the reload preview is required or not.
	 *
	 * Used to determine whether the reload preview is required.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool Whether the reload preview is required.
	 */
	public function is_reload_preview_required() {
		return true;
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
				'default'   => 'elbzotech-post-grid',
				'options'   => [
					'elbzotech-post-grid'		=> esc_html__( 'Default (Grid - list)', 'bw-printxtore' ),
					'elbzotech-post-slider'		=> esc_html__( 'Slider', 'bw-printxtore' ),
					'elbzotech-post-slider2'		=> esc_html__( 'Slider Home 7', 'bw-printxtore' ),
				],
			]
		);

		$this->add_control(
			'type_active',
			[
				'label' 	=> esc_html__( 'Active type', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'grid',
				'options'   => [
					'grid'		=> esc_html__( 'Grid', 'bw-printxtore' ),
					'list'		=> esc_html__( 'List', 'bw-printxtore' ),
				],
				'condition' => [
					'display' => 'elbzotech-post-grid',
				]
				
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
				'condition' => [
					'display' => 'elbzotech-post-grid',
				]
			]
		);

		$this->end_controls_section();

		

		$this->start_controls_section(
			'section_grid',
			[
				'label' => esc_html__( 'Grid', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'display' => ['elbzotech-post-grid','elbzotech-post-slider'],
				]
			]
		);
		$this->add_control(
			'item_style',
			[
				'label' 	=> esc_html__( 'Item style', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   =>bzotech_get_post_style()
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
					'grid-custom'	=> esc_html__( 'Grid custom', 'bw-printxtore' ),
				],
				'condition' => [
					'display' => 'elbzotech-post-grid',
				]
			]
		);

		$repeater_grid_custom = new Repeater();
		$repeater_grid_custom->add_responsive_control(
			'col_grid', 
			[
				'label' => esc_html__( 'Column grid', 'bw-printxtore' ),
				'default' => esc_html__( 'Enter number column' , 'bw-printxtore' ),
				'label_block' => true,
				'type' => Controls_Manager::SELECT,
				'options' => [
					'1'  => esc_html__( '1', 'bw-printxtore' ),
					'2' => esc_html__( '2', 'bw-printxtore' ),
					'3' => esc_html__( '3', 'bw-printxtore' ),
					'4' => esc_html__( '4', 'bw-printxtore' ),
					'5' => esc_html__( '5', 'bw-printxtore' ),
					'6' => esc_html__( '6', 'bw-printxtore' ),
					'7' => esc_html__( '7', 'bw-printxtore' ),
					'8' => esc_html__( '8', 'bw-printxtore' ),
					'9' => esc_html__( '9', 'bw-printxtore' ),
					'auto' => esc_html__( 'auto', 'bw-printxtore' ),
				],
			]
		);
		$repeater_grid_custom->add_responsive_control(
			'row_grid', 
			[
				'label' => esc_html__( 'Row grid', 'bw-printxtore' ),
				'default' => esc_html__( 'Enter number row' , 'bw-printxtore' ),
				'label_block' => true,
				'type' => Controls_Manager::SELECT,
				'options' => [
					'1'  => esc_html__( '1', 'bw-printxtore' ),
					'2' => esc_html__( '2', 'bw-printxtore' ),
					'3' => esc_html__( '3', 'bw-printxtore' ),
					'4' => esc_html__( '4', 'bw-printxtore' ),
					'5' => esc_html__( '5', 'bw-printxtore' ),
					'6' => esc_html__( '6', 'bw-printxtore' ),
					'7' => esc_html__( '7', 'bw-printxtore' ),
					'8' => esc_html__( '8', 'bw-printxtore' ),
					'9' => esc_html__( '9', 'bw-printxtore' ),
					'auto' => esc_html__( 'auto', 'bw-printxtore' ),
				],
			]
		);
		$repeater_grid_custom->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default' => 'custom',
				'separator' => 'none',
				
			]
		);
		$repeater_grid_custom->add_control(
			'template',
			[
				'label' 	=> esc_html__( 'Template content replace', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => bzotech_list_post_type('elementor_library',true),
			]
		);
		$this->add_control(
			'list_grid_custom',
			[
				'label' => esc_html__( 'Add column row', 'bw-printxtore' ),
				'type' => Controls_Manager::REPEATER,
				'prevent_empty'=>false,
				'fields' => $repeater_grid_custom->get_controls(),
				'condition' => [
					'grid_type' => 'grid-custom',
					'display' => 'elbzotech-post-grid',
				]
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
				'separator' => 'before',
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
					'item_thumbnail!' => 'no',
				]
			]
		);
		$this->add_control(
			'item_title',
			[
				'label' => esc_html__( 'Title', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'options'   => [
					''		=> esc_html__( 'Default', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Show', 'bw-printxtore' ),
					'no'		=> esc_html__( 'Hide', 'bw-printxtore' ),
				],
				'default' => '',
			]
		);
		$this->add_control(
			'item_meta',
			[
				'label' => esc_html__( 'Meta data', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'options'   => [
					''		=> esc_html__( 'Default', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Show', 'bw-printxtore' ),
					'no'		=> esc_html__( 'Hide', 'bw-printxtore' ),
				],
				'default' => '',
			]
		);

		$this->add_control(
			'item_meta_select',
			[
				'label' 	=> esc_html__( 'Meta', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT2,
				'default'   => '',
				'options'   => [
					'author'	=> esc_html__( 'Author', 'bw-printxtore' ),
					'date'		=> esc_html__( 'Date', 'bw-printxtore' ),
					'cats'		=> esc_html__( 'Categories', 'bw-printxtore' ),
					'tags'		=> esc_html__( 'Tags', 'bw-printxtore' ),
					'comments'	=> esc_html__( 'Comments', 'bw-printxtore' ),
					'views'		=> esc_html__( 'Total views', 'bw-printxtore' ),
				],
				'multiple'	=> true,
				'condition' => [
					'item_meta!' => 'no',
				]
			]
		);

		$this->add_control(
			'item_excerpt',
			[
				'label' => esc_html__( 'Excerpt', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'options'   => [
					''		=> esc_html__( 'Default', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Show', 'bw-printxtore' ),
					'no'		=> esc_html__( 'Hide', 'bw-printxtore' ),
				],
				'default' => '',
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
					'item_excerpt!' => 'no',
				]
			]
		);
		
		$this->add_control(
			'item_button',
			[
				'label' => esc_html__( 'Button', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'options'   => [
					''		=> esc_html__( 'Default', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Show', 'bw-printxtore' ),
					'no'		=> esc_html__( 'Hide', 'bw-printxtore' ),
				],
				'default' => '',
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_list',
			[
				'label' => esc_html__( 'List', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'display' => 'elbzotech-post-grid',
				]
			]
		);

		$this->add_control(
			'item_list_style',
			[
				'label' 	=> esc_html__( 'Item list style', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'style1',
				'options'   => [
					'style1'		=> esc_html__( 'Style 1 - default', 'bw-printxtore' ),
					'style2'		=> esc_html__( 'Style 2', 'bw-printxtore' ),
					'style3'		=> esc_html__( 'Style 3 - menu', 'bw-printxtore' ),					
					
				],
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

		$this->add_control(
			'item_thumbnail_list',
			[
				'label' => esc_html__( 'Thumbnail', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'options'   => [
					''		=> esc_html__( 'Default', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Show', 'bw-printxtore' ),
					'no'		=> esc_html__( 'Hide', 'bw-printxtore' ),
				],
				'default' => '',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail_list', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default' => 'full',
				'separator' => 'none',
				'condition' => [
					'item_thumbnail_list!' => 'no',
				]
			]
		);
		$this->add_control(
			'item_title_list',
			[
				'label' => esc_html__( 'Title', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'options'   => [
					''		=> esc_html__( 'Default', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Show', 'bw-printxtore' ),
					'no'		=> esc_html__( 'Hide', 'bw-printxtore' ),
				],
				'default' => '',
			]
		);

		$this->add_control(
			'item_meta_list',
			[
				'label' => esc_html__( 'Meta data', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'options'   => [
					''		=> esc_html__( 'Default', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Show', 'bw-printxtore' ),
					'no'		=> esc_html__( 'Hide', 'bw-printxtore' ),
				],
				'default' => '',
			]
		);

		$this->add_control(
			'item_meta_select_list',
			[
				'label' 	=> esc_html__( 'Meta', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT2,
				'default'   => '',
				'options'   => [
					'author'	=> esc_html__( 'Author', 'bw-printxtore' ),
					'date'		=> esc_html__( 'Date', 'bw-printxtore' ),
					'cats'		=> esc_html__( 'Categories', 'bw-printxtore' ),
					'tags'		=> esc_html__( 'Tags', 'bw-printxtore' ),
					'comments'	=> esc_html__( 'Comments', 'bw-printxtore' ),
					'views'		=> esc_html__( 'Total views', 'bw-printxtore' ),
				],
				'multiple'	=> true,
				'condition' => [
					'item_meta_list!' => 'no',
				]
			]
		);

		$this->add_control(
			'item_excerpt_list',
			[
				'label' => esc_html__( 'Excerpt', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'options'   => [
					''		=> esc_html__( 'Default', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Show', 'bw-printxtore' ),
					'no'		=> esc_html__( 'Hide', 'bw-printxtore' ),
				],
				'default' => '',
			]
		);
		$this->add_control(
			'excerpt_list',
			[
				'label' => esc_html__( 'Number of text for excerpt', 'bw-printxtore' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 999,
				'step' => 1,
				'default' => 80,
				'condition' => [
					'item_excerpt_list!' => 'no',
				]
			]
		);
		
		$this->add_control(
			'item_button_list',
			[
				'label' => esc_html__( 'Button', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'options'   => [
					''		=> esc_html__( 'Default', 'bw-printxtore' ),
					'yes'		=> esc_html__( 'Show', 'bw-printxtore' ),
					'no'		=> esc_html__( 'Hide', 'bw-printxtore' ),
				],
				'default' => '',
			]
		);
		$this->end_controls_section();
		
		$this->get_slider_settings();
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
			'section_top_filter',
			[
				'label' => esc_html__( 'Top filter', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'display' => 'elbzotech-post-grid',
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
				'default' => '',
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

		$this->end_controls_section();

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
					'{{WRAPPER}} .blog-slider-view .item-post' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .blog-grid-view .list-col-item' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'space_item',
			[
				'label' => esc_html__( 'Space item', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', ],
				'selectors' => [
					'{{WRAPPER}} .list-col-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .list-post-wrap' => 'margin: -{{TOP}}{{UNIT}} -{{RIGHT}}{{UNIT}} -{{BOTTOM}}{{UNIT}} -{{LEFT}}{{UNIT}};',
				],
			]
        );
		$this->get_box_settings('item','item-post');

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_thumbnail',
			[
				'label' => esc_html__( 'Thumbnail', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		

		$this->get_thumb_styles('thumbnail','post-thumb');

		$this->get_box_image('thumbnail','post-thumb');

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
					'{{WRAPPER}} .post-info' => 'text-align: {{VALUE}};',
				],
			]
		);

		

		$this->get_box_settings('info','post-info');

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label' => esc_html__( 'Title', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->get_text_styles('title','post-info .post-title a');

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
					'{{WRAPPER}} .post-info .post-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_meta',
			[
				'label' => esc_html__( 'Meta', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'meta_space',
			[
				'label' => esc_html__( 'Space meta item', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -300,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .post-meta-data' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->get_text_meta_styles('meta','meta-item a');

		$this->add_responsive_control(
			'meta_icon_size',
			[
				'label' => esc_html__( 'Icon size', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .meta-item i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'meta_icon_color',
			[
				'label' => esc_html__( 'Icon color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .meta-item i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'meta_icon_space',
			[
				'label' => esc_html__( 'Space icon', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -300,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .meta-item i' => 'margin-right: {{SIZE}}{{UNIT}};',
					'.rtl {{WRAPPER}} .meta-item i' => 'margin-left: {{SIZE}}{{UNIT}};margin-right: 0 !important;',
				],
			]
		);

		$this->add_responsive_control(
			'meta_bottom_space',
			[
				'label' => esc_html__( 'Space bottom', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -300,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .post-meta-data' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

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

		$this->get_button_styles('button','readmore');

		$this->end_controls_section();
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
		$slider_items_widescreen =$slider_items_laptop = $slider_items_tablet = $slider_items_tablet_extra =$slider_items_mobile_extra =$slider_items_mobile =$slider_space_widescreen =$slider_space_laptop =$slider_space_tablet_extra =$slider_space_tablet =$slider_space_mobile_extra= $slider_space_mobile ='';
		$column_widescreen = $column_laptop =$slider_items_tablet =$column_tablet_extra =$column_tablet =$column_mobile_extra =$column_mobile ='';
		$settings = $this->get_settings();
		extract($settings);
        $view = str_replace('elbzotech-post-', '', $display);
        if(isset($_GET['type']) && $_GET['type']) $type_active = sanitize_text_field($_GET['type']);
        if(isset($_GET['number']) && $_GET['number']) $number = sanitize_text_field($_GET['number']);
		
		if($type_active == 'list' && $view == 'grid') $el_class = 'blog-list-view '.$grid_type;
		else $el_class = 'blog-'.$view.'-view '.$grid_type;


		if(isset($column['size'])) $column = $column['size'];
		if(isset($column_widescreen['size'])) $column_widescreen = $column_widescreen['size'];
		if(isset($column_laptop['size'])) $column_laptop = $column_laptop['size'];
		if(isset($column_tablet_extra['size'])) $column_tablet_extra = $column_tablet_extra['size'];
		if(isset($column_tablet['size'])) $column_tablet = $column_tablet['size'];
		if(isset($column_mobile_extra['size'])) $column_mobile_extra = $column_mobile_extra['size'];
		if(isset($column_mobile['size'])) $column_mobile = $column_mobile['size'];
        if(!empty($column_custom)){
        	$column = $column_tablet = $column_mobile ='';
        }
        if(isset($excerpt['size'])) $excerpt = $excerpt['size'];

		
		if ( $view == 'grid' ) {
			
			$this->add_render_attribute( 'elbzotech-item-grid', 'class', 'list-col-item list-'.esc_attr($column).'-item list-'.esc_attr($column_widescreen).'-item-widescreen list-'.esc_attr($column_laptop).'-item-laptop  list-'.esc_attr($column_tablet_extra).'-item-tablet-extra list-'.esc_attr($column_tablet).'-item-tablet list-'.esc_attr($column_mobile_extra).'-item-mobile-extra list-'.esc_attr($column_mobile).'-item-mobile item-grid-post-'.$item_style);
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-grid',$column_custom);
		}
		$this->add_render_attribute( 'elbzotech-item', 'class', 'item-post');
		if ( $view == 'slider'||$view == 'slider2' ) {
			$this->add_render_attribute( 'elbzotech-item-grid', 'class', 'swiper-slide item-grid-post-'.$item_style);
			$this->add_render_attribute( 'elbzotech-wrapper', 'class', 'elbzotech-swiper-slider swiper-container' );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-items', $slider_items );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-items-custom', $slider_items_custom );
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
			

		}
		else{
			$this->add_render_attribute( 'elbzotech-wrapper', 'class', 'elbzotech-posts-wrap js-content-wrap '.$el_class );
			$this->add_render_attribute( 'elbzotech-inner', 'class', 'js-content-main list-post-wrap bzotech-row');
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column', $column );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-widescreen', $column_widescreen );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-laptop', $column_laptop );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-tablet-extra', $column_tablet_extra );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-tablet', $column_tablet );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-mobile-extra', $column_mobile_extra );
			$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-mobile', $column_mobile );
		}


        $paged = (get_query_var('paged') && $view != 'slider' && $view != 'slider2') ? get_query_var('paged') : 1;
        $args = array(
            'post_type'         => 'post',
            'posts_per_page'    => $number,
            'orderby'           => $orderby,
            'order'             => $order,
            'paged'             => $paged,
            );            
        if(!empty($cats)) {
            $custom_list = explode(",",$cats);
            $args['tax_query'][]=array(
                'taxonomy'=>'category',
                'field'=>'slug',
                'terms'=> $custom_list
            );
        }
        if(!empty($custom_ids)){
            $args['post__in'] = explode(',', $custom_ids);
        }
        $post_query = new WP_Query($args);
        $count = 1;
        $count_query = $post_query->post_count;
        $max_page = $post_query->max_num_pages;
        $size = $thumbnail_size;
        if($size == 'custom' && !empty($thumbnail_custom_dimension['width']) && !empty($thumbnail_custom_dimension['height'])) $size = array($thumbnail_custom_dimension['width'],$thumbnail_custom_dimension['height']);
        
        if($grid_type == 'grid-masonry'){
			$size = bzotech_get_size_crop($size_masonry);
        }

        $size_list = $thumbnail_list_size;
        if($size_list == 'custom' && !empty($thumbnail_list_custom_dimension['width']) && !empty($thumbnail_list_custom_dimension['height'])) $size_list = array($thumbnail_list_custom_dimension['width'],$thumbnail_list_custom_dimension['height']);
        $item_wrap = $this->get_render_attribute_string( 'elbzotech-item-grid' );
        $item_inner = $this->get_render_attribute_string( 'elbzotech-item' );
        $attr = array(
            'el_class'      => $el_class,
            'post_query'    => $post_query,
            'count'         => $count,
            'count_query'   => $count_query,
            'max_page'      => $max_page,
            'args'          => $args,
            'column'        => $column,
            'excerpt'       => $excerpt,
            'view'       	=> $view,
            'type_active'   => $type_active,
            'settings'      => $settings,
            'size'      	=> $size,
            'size_list'     => $size_list,
            'item_wrap'		=> $item_wrap,
            'item_inner'	=> $item_inner,
            'wdata'			=> $this,
        );
        bzotech_get_template_elementor_global('posts/'.$view,'',$attr,true);
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
}
