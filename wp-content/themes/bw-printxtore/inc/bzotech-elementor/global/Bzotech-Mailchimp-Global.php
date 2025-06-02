<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; 

/**
 * Elementor Hello World
 *
 * Elementor widget for hello world.
 *
 * @since 1.0.0
 */
class Bzotech_Mailchimp_Global extends Widget_Base {

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
		return 'bzotech-mailchimp-global';
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
		return esc_html__( 'Mailchimp (Global)', 'bw-printxtore' );
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
		return 'eicon-mailchimp';
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
		return [ 'bzotech-el-mailchimp' ];
	}
	public function get_widget_css_config( $widget_name ) { 
	    $file_content_css = get_template_directory() . '/assets/global/css/elementor/mailchimp.css';
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
	protected function register_controls() {
		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'style',
			[
				'label' 	=> esc_html__( 'Style', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'elbzotech-mailchimp-default',
				'options'   => [
					'default'		=> esc_html__( 'Default', 'bw-printxtore' ),
					'style2'		=> esc_html__( 'Style 2 (Newletter popup)', 'bw-printxtore' ),
					'style3'		=> esc_html__( 'Style 3 (Footer 2)', 'bw-printxtore' ),
					'style4'		=> esc_html__( 'Style 4 (Line)', 'bw-printxtore' ),
				],
			]
		);
		$this->add_control(
			'title', [
				'label' => esc_html__( 'Title', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Title text', 'bw-printxtore' ),
				'label_block' => true,
				'condition' => [
					'style' =>  ['style2'],
				]
			]
		);
		$this->add_control(
			'desc', [
				'label' => esc_html__( 'Description', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter description', 'bw-printxtore' ),
				'label_block' => true,
				'condition' => [
					'style' =>  ['style2'],
				]
			]
		);
		$this->add_control(
			'image',
			[
				'label' => esc_html__( 'Image', 'bw-printxtore' ),
				'description'	=> esc_html__( 'You can choose the icon image here (Replace for icon)', 'bw-printxtore' ),
				'type' => Controls_Manager::MEDIA,
				'condition' => [
					'style' =>  ['style2'],
				]
			]
		);
		$repeater_style2 = new Repeater();
		$repeater_style2->add_control(
			'icon', [
				'label' => esc_html__( 'Icon social', 'bw-printxtore' ),
				'type' => Controls_Manager::ICONS,
				'placeholder' => esc_html__( 'Add Your Content Here', 'bw-printxtore' ),
				'label_block' => true,
			]
		);
		$repeater_style2->add_control(
			'link', [
				'label' => esc_html__( 'Link', 'bw-printxtore' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'You can add links for the element here', 'bw-printxtore' ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => false,
					'nofollow' => true,
				],
			]
		);
		$this->add_control(
			'list_social',
			[
				'label' => esc_html__( 'Add item social', 'bw-printxtore' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater_style2->get_controls(),
				'prevent_empty'=>false,
				'condition' => [
					'style' => ['style2'],
				]
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_form',
			[
				'label' => esc_html__( 'Form', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'form_id',
			[
				'label' => esc_html__( 'Form ID', 'bw-printxtore' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 100000,
				'step' => 1,
			]
		);

		$this->add_control(
			'placeholder',
			[
				'label' => esc_html__( 'Placeholder', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Your email address', 'bw-printxtore' ),
				'placeholder' => esc_html__( 'Type your placeholder here', 'bw-printxtore' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_button',
			[
				'label' => esc_html__( 'Button', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => esc_html__( 'Icon', 'bw-printxtore' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-search',
					'library' => 'solid',
				],
			]
		);

		$this->add_control(
			'mailchimp_bttext',
			[
				'label' => esc_html__( 'Add text', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Type your text to add search button', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			'mailchimp_bttext_pos',
			[
				'label' => esc_html__( 'Text position', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'after-icon',
				'options' => [
					'after-icon'   => esc_html__( 'After icon', 'bw-printxtore' ),
					'before-icon'  => esc_html__( 'Before icon', 'bw-printxtore' ),
				],
				'condition' => [
					'mailchimp_bttext!' => '',
					'icon[value]!' => '',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_popup',
			[
				'label' => esc_html__( 'Popup', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'popup_width',
			[
				'label' => esc_html__( 'Width', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' , '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .content-popup-mailchimp' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'popup_max-width',
			[
				'label' => esc_html__( 'Max Width', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' , '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .content-popup-mailchimp' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'popup_height',
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
					'{{WRAPPER}} .content-popup-mailchimp' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'popup_padding',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .content-popup-mailchimp' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'popup_margin',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .content-popup-mailchimp' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'popup_background',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .content-popup-mailchimp',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'popup_border',
				'selector' => '{{WRAPPER}} .content-popup-mailchimp',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'popup_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .content-popup-mailchimp' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'popup_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .content-popup-mailchimp',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_mailchimp_button',
			[
				'label' => esc_html__( 'Button', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'width_icon',
			[
				'label' => esc_html__( 'Width', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mailchimp-wrap *[type="submit"]' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);	

		$this->add_responsive_control(
			'height_icon',
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
					'{{WRAPPER}} .elbzotech-mailchimp-wrap *[type="submit"]' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elbzotech-mailchimp-wrap .elbzotech-text-bt-mailchimp > *' => 'line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);	

		$this->add_responsive_control(
			'size_icon',
			[
				'label' => esc_html__( 'Size icon', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mailchimp-wrap  *[type="submit"] i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'separator_begin_tabs',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->start_controls_tabs( 'mailchimp_button_effects' );

		$this->start_controls_tab( 'normal',
			[
				'label' => esc_html__( 'Normal', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => esc_html__( 'Button Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mailchimp-wrap  *[type="submit"]' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Icon Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mailchimp-wrap  *[type="submit"] i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'mailchimp_text_button_typography',
				'label' => esc_html__( 'Typography button text', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .elbzotech-mailchimp-wrap  *[type="submit"]',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_icon',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .elbzotech-mailchimp-wrap *[type="submit"]',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'hover',
			[
				'label' => esc_html__( 'Hover', 'bw-printxtore' ),
			]
		);
		$this->add_control(
			'button_color_hover',
			[
				'label' => esc_html__( 'Button Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mailchimp-wrap  *[type="submit"]:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'title_color_hover',
			[
				'label' => esc_html__( 'Icon Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mailchimp-wrap .elbzotech-submit-form:hover . *[type="submit"] i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'mailchimp_text_button_typography_hover',
				'label' => esc_html__( 'Typography button text', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .elbzotech-mailchimp-wrap .elbzotech-submit-form:hover . *[type="submit"]',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_icon_hover',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .elbzotech-mailchimp-wrap  *[type="submit"]:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();		

		$this->add_control(
			'separator_end_tabs',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->add_responsive_control(
			'padding_icon',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mailchimp-wrap  *[type="submit"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'margin_icon',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mailchimp-wrap *[type="submit"]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' 		=> 'icon_border',
				'selector'  => '{{WRAPPER}} .elbzotech-mailchimp-wrap  *[type="submit"]',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'icon_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mailchimp-wrap  *[type="submit"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_form',
			[
				'label' => esc_html__( 'Form', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'width_form',
			[
				'label' => esc_html__( 'Width', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' , '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mailchimp-wrap form' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'max-width_form',
			[
				'label' => esc_html__( 'Max Width', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' , '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mailchimp-wrap form' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'height_form',
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
					'{{WRAPPER}} .elbzotech-mailchimp-wrap form' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'align_form',
			[
				'label' => esc_html__( 'Alignment', 'bw-printxtore' ),
				'type' => Controls_Manager::CHOOSE,
				'default'	=> '',
				'options' => [
					'form-left' => [
						'title' => esc_html__( 'Left', 'bw-printxtore' ),
						'icon' => 'eicon-text-align-left',
					],
					'form-center' => [
						'title' => esc_html__( 'Center', 'bw-printxtore' ),
						'icon' => 'eicon-text-align-center',
					],
					'form-right' => [
						'title' => esc_html__( 'Right', 'bw-printxtore' ),
						'icon' => 'eicon-text-align-right',
					],
				],
			]
		);

		$this->add_responsive_control(
			'padding_form',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mailchimp-wrap form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'margin_form',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mailchimp-wrap form' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_form',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .elbzotech-mailchimp-wrap form',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_form',
				'selector' => '{{WRAPPER}} .elbzotech-mailchimp-wrap form',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'border_form_radius',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mailchimp-wrap form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'form_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .elbzotech-mailchimp-wrap form',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_input',
			[
				'label' => esc_html__( 'Input', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'color_input',
			[
				'label' => esc_html__( 'Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mailchimp-wrap .mc4wp-form-fields input[type="email"]' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'color_input_placeholder',
			[
				'label' => esc_html__( 'Color placeholder', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mailchimp-wrap .mc4wp-form-fields input[type="email"]::placeholder' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elbzotech-mailchimp-wrap .mc4wp-form-fields input[type="email"]::-ms-input-placeholder' => 'color: {{VALUE}}',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_input',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .elbzotech-mailchimp-wrap .mc4wp-form-fields input[type="email"]',
			]
		);
		$this->add_responsive_control(
			'width_input',
			[
				'label' => esc_html__( 'Width', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' , '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mailchimp-wrap .mc4wp-form-fields input[type="email"]' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'height_input',
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
					'{{WRAPPER}} .elbzotech-mailchimp-wrap .mc4wp-form-fields input[type="email"]' => 'line-height: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'padding_input',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mailchimp-wrap .mc4wp-form-fields input[type="email"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'margin_input',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mailchimp-wrap .mc4wp-form-fields input[type="email"]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);		

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_input',
				'selector' => '{{WRAPPER}} .elbzotech-mailchimp-wrap .mc4wp-form-fields input[type="email"]',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'border_input_radius',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mailchimp-wrap .mc4wp-form-fields input[type="email"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'input_box_shadow',
				'selector' => '{{WRAPPER}} .elbzotech-mailchimp-wrap .mc4wp-form-fields input[type="email"]',
			]
		);

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
		$settings = $this->get_settings();
		if(isset($settings['icon']['value'])) $icon_mailchimp = $settings['icon']['value'];
		else $icon = '';
		$attr = array(
			'wdata'		=> $this,
			'icon_mailchimp'		=> $icon_mailchimp,
			'settings'	=> $settings,
		);
		echo bzotech_get_template_elementor_global('mailchimp/mailchimp',$settings['style'],$attr);
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
