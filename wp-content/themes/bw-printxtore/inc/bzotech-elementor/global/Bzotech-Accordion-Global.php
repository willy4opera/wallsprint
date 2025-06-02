<?php
namespace Elementor;
if ( ! defined( 'ABSPATH' ) ) exit; 
/**
 * Elementor Accordion
 *
 * @since 1.0.0
 */
class Bzotech_Accordion_Global extends Widget_Base {

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
		return 'bzotech-accordion-global';
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
		return esc_html__( 'Accordion (Global)', 'bw-printxtore' );
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
		return 'eicon-accordion';
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
	
	public function get_style_depends() {
		return [ 'bzotech-el-accordion' ];
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
		return [ 'jquery-ui-accordion','hello-world' ];
	}
	public function get_widget_css_config( $widget_name ) { 
	    $file_content_css = get_template_directory() . '/assets/global/css/elementor/accordion.css';
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
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
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
		$settings = $this->get_settings();
		

		$attr = array(
			'wdata'		=> $this,
			'settings'	=> $settings,
		);
		echo bzotech_get_template_elementor_global('accordion/accordion',$settings['style'],$attr);
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
	protected function register_controls() {

		$this->start_controls_section(

			'section_content',
			[
				'label' => esc_html__( 'Content', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				
			]
		);
		$this->add_control(
			'style',
			[
				'label' => esc_html__( 'Style', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''  => esc_html__( 'Style 1 Default', 'bw-printxtore' ),
					'style2'  => esc_html__( 'Style 2', 'bw-printxtore' ),
				],
			]
		);
		
		
		
		$repeater_list = new Repeater();
		$repeater_list->add_control(
			'title', 
			[
				'label' => esc_html__( 'Title', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Enter text' , 'bw-printxtore' ),
				'label_block' => true,
			]
		);
		$repeater_list->add_control(
			'content', [
				'label' => esc_html__( 'Content', 'bw-printxtore' ),
				'type' => Controls_Manager::WYSIWYG,
				'placeholder' => esc_html__( 'Add Your Content Here', 'bw-printxtore' ),
				'label_block' => true,
				'condition' => [
					'template' => '',
				]
			]
		);
		$repeater_list->add_control(
			'template',
			[
				'label' 	=> esc_html__( 'Template content replace', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => bzotech_list_post_type('elementor_library',true),
			]
		);
		$repeater_list->add_responsive_control(
			'align',
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
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'list_accor',
			[
				'label' => esc_html__( 'Add text', 'bw-printxtore' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater_list->get_controls(),
				'prevent_empty'=>false,
				'default' => [
					[
						'title' => esc_html__( 'Accordion #1', 'bw-printxtore' ),
						'content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'bw-printxtore' ),
						'align'=>'left'
					],
					[
						'title' => esc_html__( 'Accordion #2', 'bw-printxtore' ),
						'content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'bw-printxtore' ),
						'align'=>'left'
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);
		$this->add_control(
			'icon',
			[
				'label' => esc_html__( 'Icon', 'bw-printxtore' ),
				'type' => Controls_Manager::ICONS,
				'separator' => 'before',
				'default' => [
					'value' => 'fas fa-plus',
					'library' => 'fa-solid',
				],
				'recommended' => [
					'fa-solid' => [
						'chevron-down',
						'angle-down',
						'angle-double-down',
						'caret-down',
						'caret-square-down',
						'angle-up'
					],
					
					'fa-regular' => [
						'caret-square-down',
					],
				],
				'skin' => 'inline',
				'label_block' => false,
			]
		);
		$this->add_control(
			'icon_active',
			[
				'label' => esc_html__( 'Active Icon', 'bw-printxtore' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-minus',
					'library' => 'fa-solid',
				],
				'recommended' => [
					'fa-solid' => [
						'chevron-up',
						'angle-up',
						'angle-double-up',
						'caret-up',
						'caret-square-up',
					],
					
					'fa-regular' => [
						'caret-square-up',
					],
				],
				'skin' => 'inline',
				'label_block' => false,
				'condition' => [
					'icon[value]!' => '',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(

			'section_accordion_setting',
			[
				'label' => esc_html__( 'Accordion setting', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				
			]
		);
		$this->add_control(
			'active',
			[
				'label' => esc_html__( 'Active', 'bw-printxtore' ),
				'description' => esc_html__( 'Which panel is currently open.', 'bw-printxtore' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 50,
				'step' => 1,
				'default' =>1,
			]
		);
		$this->add_control(
			'animate',
			[
				'label' => esc_html__( 'Animate ', 'bw-printxtore' ),
				'description' => esc_html__( 'The speed of the animation.', 'bw-printxtore' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 10000,
				'step' => 1,
				'default' =>300,
			]
		);
		$this->add_control(

			'heightstyle',
			[
				'label' => esc_html__( 'Height Style ', 'bw-printxtore' ),
				'description' => esc_html__( 'Controls the height of the accordion.', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'auto'  => esc_html__( 'Auto', 'bw-printxtore' ),
					'fill'  => esc_html__( 'Fill', 'bw-printxtore' ),
					'content'  => esc_html__( 'Content', 'bw-printxtore' ),
				],
				'default' =>'auto',
			]
		);
		$this->end_controls_section();

		/*------------STYLE--------- */

		$this->start_controls_section(
			'section_style_title',
			[
				'label' => esc_html__( 'Title style', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				
			]
		);
		$this->get_style_type_text('title','item-title-e');
		$this->end_controls_section(); /*End title style*/

		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => esc_html__( 'Icon style', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->get_style_type_icon('icon','item-icon-e');
		$this->end_controls_section(); /*End Icon style*/

		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__( 'Content style', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				
			]
		);
		$this->get_style_type_text('content','item-content-e');
		$this->end_controls_section(); /*End title style*/
	}


	public function get_style_type_text($key='text',$class="item-text-e") {
		$this->start_controls_tabs( $key.'_tabs_style' );
		$this->start_controls_tab(
			$key.'_tab_normal_css',
			[
				'label' => esc_html__( 'Normal Style', 'bw-printxtore' ),
			]
		);
		$this->add_control(
			$key.'_color_css',
			[
				'label' => esc_html__( 'Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'color: {{VALUE}}',
					'{{WRAPPER}} .'.$class.' .sub-color-e' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			$key.'bg_color_css',
			[
				'label' => esc_html__( 'Background Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => $key.'_typography_css',
				'label' => esc_html__( 'Text Typography', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .'.$class,
			]
		);
		$this->add_responsive_control(
			$key.'_opacity_css',
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
					'{{WRAPPER}} .'.$class => 'opacity: {{SIZE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => $key.'_shadow_css',
				'label' => esc_html__( 'Text Shadow', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .'.$class,
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => $key.'border',
				'selector' => '{{WRAPPER}} .'.$class,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			$key.'border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			$key.'_padding_css',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px'],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
        );

        $this->add_responsive_control(
			$key.'_margin_css',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px'],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
		$this->end_controls_tab(); /*End Normal Style*/

		$this->start_controls_tab(
			$key.'_tab_hover_css',
			[
				'label' => esc_html__( 'Style On Hover', 'bw-printxtore' ),
			]
		);
		$this->add_control(
			$key.'_color_hover_css',
			[
				'label' => esc_html__( 'Color On Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .'.$class.':hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .'.$class.':hover .sub-color-e' => 'color: {{VALUE}}',
				],
			]
		);
		
		$this->add_control(
			$key.'_bg_color_hover_css',
			[
				'label' => esc_html__( 'Background Color On Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .'.$class.':hover, {{WRAPPER}} .'.$class.':focus' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => $key.'_typography_hover_css',
				'label' => esc_html__( 'Typography On Hover', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .'.$class.':hover',
			]
		);
		$this->add_responsive_control(
			$key.'_opacity_hover_css',
			[
				'label' => esc_html__( 'Opacity On Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class.':hover' => 'opacity: {{SIZE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => $key.'_shadow_hover_css',
				'label' => esc_html__( 'Shadow On Hover', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .'.$class.':hover',
			]
		);
		$this->add_control(
			$key.'_hover_transition_css',
			[
				'label' => esc_html__( 'Transition Duration On Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 5,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}}  .'.$class => 'transition-duration: {{SIZE}}s',
				],
			]
		);
		$this->add_control(
			$key.'_animation_hover_css',
			[
				'label' => esc_html__( 'Animation On Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);
		$this->end_controls_tab();/*End Hover Style*/
		$this->start_controls_tab(
			$key.'_tab_active_css',
			[
				'label' => esc_html__( 'Style active', 'bw-printxtore' ),
			]
		);
		$this->add_control(
			$key.'_color_active_css',
			[
				'label' => esc_html__( 'Color On Active', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elbzotech-accordion-global-css .ui-accordion-header-active.'.$class => 'color: {{VALUE}}',
					'{{WRAPPER}} .elbzotech-accordion-global-css .ui-accordion-content-active.'.$class => 'color: {{VALUE}}',
				],
			]
		);
		
		$this->add_control(
			$key.'_bg_color_active_css',
			[
				'label' => esc_html__( 'Background Color On Active', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elbzotech-accordion-global-css .ui-accordion-header-active.'.$class => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .elbzotech-accordion-global-css .ui-accordion-content-active.'.$class => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => $key.'_typography_active_css',
				'label' => esc_html__( 'Typography On Active', 'bw-printxtore' ),
				'selectors' => [
					'{{WRAPPER}} .elbzotech-accordion-global-css .ui-accordion-header-active.'.$class,
					'{{WRAPPER}} .elbzotech-accordion-global-css .ui-accordion-content-active.'.$class,
				],
			]
		);
		$this->add_responsive_control(
			$key.'_opacity_active_css',
			[
				'label' => esc_html__( 'Opacity On Active', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-accordion-global-css .ui-accordion-header-active.'.$class => 'opacity: {{SIZE}};',
					'{{WRAPPER}} .elbzotech-accordion-global-css .ui-accordion-content-active.'.$class => 'opacity: {{SIZE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => $key.'border_active',
				'selectors' => [
					'{{WRAPPER}} .elbzotech-accordion-global-css .ui-accordion-header-active.'.$class,
					'{{WRAPPER}} .elbzotech-accordion-global-css .ui-accordion-content-active.'.$class,
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			$key.'border_radius_active',
			[
				'label' => esc_html__( 'Border Radius Active', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-accordion-global-css .ui-accordion-header-active.'.$class => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elbzotech-accordion-global-css .ui-accordion-content-active.'.$class => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => $key.'_shadow_active_css',
				'label' => esc_html__( 'Shadow On Active', 'bw-printxtore' ),
				'selectors' => [
					'{{WRAPPER}} .elbzotech-accordion-global-css .ui-accordion-header-active.'.$class,
					'{{WRAPPER}} .elbzotech-accordion-global-css .ui-accordion-content-active.'.$class,
				],
			]
		);
		$this->end_controls_tab();/*End Hover Style*/
		$this->end_controls_tabs();
	}
	public function get_style_type_icon($key='icon',$class="item-icon-e") {
		$this->start_controls_tabs( $key.'_tabs_style' );
		$this->start_controls_tab(
			$key.'_tab_normal',
			[
				'label' => esc_html__( 'Normal Style', 'bw-printxtore' ),
			]
		);
		$this->add_responsive_control(
			$key.'_size_css',
			[
				'label' => esc_html__( 'Font Size', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'max' => 200,
						'min' => 0,
						'step' => 1,
					],
					'em' => [
						'max' => 200,
						'min' => 0,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			$key.'_color_css',
			[
				'label' => esc_html__( 'Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'color: {{VALUE}}',
					'{{WRAPPER}} .'.$class.' .sub-color-e' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			$key.'bg_color_css',
			[
				'label' => esc_html__( 'Background Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			$key.'_opacity_css',
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
					'{{WRAPPER}} .'.$class => 'opacity: {{SIZE}};',
				],
			]
		);
		$this->add_responsive_control(
			$key.'_padding_css',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px'],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
        );

        $this->add_responsive_control(
			$key.'_margin_css',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px'],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
		$this->end_controls_tab(); /*End Normal Style*/

		$this->start_controls_tab(
			$key.'_tab_hover',
			[
				'label' => esc_html__( 'Hover Style', 'bw-printxtore' ),
			]
		);
		$this->add_responsive_control(
			$key.'_size_hover_css',
			[
				'label' => esc_html__( 'Size On Hover ', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'max' => 200,
						'min' => 0,
						'step' => 1,
					],
					'em' => [
						'max' => 200,
						'min' => 0,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class.':hover' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			$key.'_color_hover_css',
			[
				'label' => esc_html__( 'Color On Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .'.$class.':hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .'.$class.':hover .sub_color_e' => 'color: {{VALUE}}',
				],
			]
		);
		
		$this->add_control(
			$key.'_bg_hover_css',
			[
				'label' => esc_html__( 'Background Color On Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .'.$class.':hover, {{WRAPPER}} .'.$class.':focus' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			$key.'_opacity_hover_css',
			[
				'label' => esc_html__( 'Opacity On Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class.':hover' => 'opacity: {{SIZE}};',
				],
			]
		);
		$this->add_control(
			$key.'_hover_transition_css',
			[
				'label' => esc_html__( 'Transition Duration On Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 5,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}}  .'.$class => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->add_control(
			$key.'_animation_hover_css',
			[
				'label' => esc_html__( 'Animation On Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);
		$this->end_controls_tab();/*End Hover Style*/
		$this->end_controls_tabs();
	}
	
}