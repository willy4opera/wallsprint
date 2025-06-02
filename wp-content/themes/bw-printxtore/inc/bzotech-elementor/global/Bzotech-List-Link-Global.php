<?php
namespace Elementor;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
if ( ! defined( 'ABSPATH' ) ) exit; 
class Bzotech_List_Link_Global extends Widget_Base {
	public function get_name() {
		return 'bzotech_list_link_global';
	}
	public function get_title() {
		return esc_html__( 'List Link (Global)', 'bw-printxtore' );
	}
	public function get_icon() {
		return 'eicon-editor-link';
	}
	public function get_categories() {
		return [ 'aqb-htelement-category' ];
	}

	public function get_style_depends() {
		return [ 'bzotech-el-list-link' ];
	}
	public function get_widget_css_config( $widget_name ) { 
	    $file_content_css = get_template_directory() . '/assets/global/css/elementor/list-link.css';
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
	protected function render() {
		$settings = $this->get_settings();
		$attr = array(
			'wdata'		=> $this,
			'settings'	=> $settings,
		);
		echo bzotech_get_template_elementor_global('list-link/list-link',$settings['style'],$attr);
	}
	
	protected function content_template() {
		
	}
	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'style',
			[
				'label' 	=> esc_html__( 'Style', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'inline',
				'options'   => [
					'inline'		=> esc_html__( 'Style 1 - Inline', 'bw-printxtore' ),
					'block'	=> esc_html__( 'Style 2 - Block', 'bw-printxtore' ),
				],
			]
		);
		$this->add_control(
			'heading',
			[
				'label' 	=> esc_html__( 'Title Heading', 'bw-printxtore' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
			]
		);
		$repeater = new Repeater();
		$repeater->add_control(
			'title', [
				'label' => esc_html__( 'Title', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Title text', 'bw-printxtore' ),
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'icon',
			[
				'label' => esc_html__( 'Icon', 'bw-printxtore' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => '',
					'library' => 'solid',
				],
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
			'separator_list_link',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);
		$repeater->add_control(
			'link',
			[
				'label' => esc_html__( 'Link Simple', 'bw-printxtore' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'bw-printxtore' ),
				'show_label' => true,
				
			]
		);
		$repeater->add_control(
			'link_id',
			[
				'label' => esc_html__( 'Link ID', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'description'	=> esc_html__( 'Get Link by ID', 'bw-printxtore' ),
				'placeholder' => esc_html__( 'For example ID: 6', 'bw-printxtore' ),
				'show_label' => true,
			]
		);
		$repeater->add_control(
			'separator_list_link_tab',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);
		$repeater->start_controls_tabs( 'tabs_style_private' );
		$repeater->start_controls_tab(
			'tab_normal_private',
			[
				'label' => esc_html__( 'Style private', 'bw-printxtore' ),
			]
		);
		$repeater->add_control(
			'icon_color_private',
			[
				'label' => esc_html__( 'Icon Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.item-link  i' => 'color: {{VALUE}}',
				],
			]
		);
		$repeater->add_control(
			'title_color_private',
			[
				'label' => esc_html__( 'Title Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.item-link .title' => 'color: {{VALUE}}',
				],
			]
		);
		$repeater->add_control(
			'background_color_private',
			[
				'label' => esc_html__( 'Background Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.item-link' => 'background-color: {{VALUE}};',
				],
			]
		);
		$repeater->add_control(
			'border_color_icon_private',
			[
				'label' => esc_html__( 'Border Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.item-link i' => 'border-color: {{VALUE}};',
				],
			]
		);
		$repeater->add_control(
			'css_by_theme_text',
			[
				'label' 	=> esc_html__( 'Add class style', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT2,
				'default'   => '',
				'options'   => bzotech_list_class_style_by_theme(),
				'multiple'	=> true,
				'label_block' => true,
				'description'	=> esc_html__( 'Add class style by theme', 'bw-printxtore' ),
			]
		);
		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'tab_hover_private',
			[
				'label' => esc_html__( 'Style private hover', 'bw-printxtore' ),
			]
		);
		$repeater->add_control(
			'icon_color_hover_private',
			[
				'label' => esc_html__( 'Icon Color Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.item-link:hover i' => 'color: {{VALUE}}',
				],
			]
		);
		$repeater->add_control(
			'title_color_hover_private',
			[
				'label' => esc_html__( 'Title Color Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.item-link:hover .title,' => 'color: {{VALUE}}',
					'{{WRAPPER}} {{CURRENT_ITEM}}.item-link:hover,{{WRAPPER}} {{CURRENT_ITEM}}.item-link:focus' => 'color: {{VALUE}}',
				],
			]
		);
		
		$repeater->add_control(
			'background_color_hover_private',
			[
				'label' => esc_html__( 'Background Color hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.item-link:hover, {{WRAPPER}} {{CURRENT_ITEM}}.item-link:focus' => 'background-color: {{VALUE}};',
				],
			]
		);
		$repeater->add_control(
			'border_color_hover_private',
			[
				'label' => esc_html__( 'Border Color hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.item-link:hover, {{WRAPPER}} {{CURRENT_ITEM}}.item-link:focus' => 'border-color: {{VALUE}};',
				],
			]
		);
		$repeater->add_control(
			'css_by_theme_text_hover',
			[
				'label' 	=> esc_html__( 'Add class hover style', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT2,
				'default'   => '',
				'options'   => bzotech_list_class_style_hover_by_theme(),
				'multiple'	=> true,
				'label_block' => true,
				'description'	=> esc_html__( 'Add class hover style by theme', 'bw-printxtore' ),
			]
		);

		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();
		$this->add_control(
			'list_link',
			[
				'label' => esc_html__( 'Add list', 'bw-printxtore' ),
				'type' => Controls_Manager::REPEATER,
				'prevent_empty'=>false,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title' => esc_html__( 'Link 1', 'bw-printxtore' ),
						'link'  => '#',
					],
					[
						'title' => esc_html__( 'Link 2', 'bw-printxtore' ),
						'link'  => '#',
					],
				],
			]
		);
		
		$left = is_rtl() ? 'right' : 'left';
		$right = is_rtl() ? 'left' : 'right';
		$this->add_responsive_control(
			'alignment',
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
					'{{WRAPPER}} .elbzotech-list-link-global' => 'justify-content: {{VALUE}};',
				],
				'default' => '',
			]
		);

		$this->add_responsive_control(
			'align_items',
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
					'{{WRAPPER}} .elbzotech-list-link-global' => 'align-items: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$this->add_responsive_control(
			'gap_item_link',
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
					'{{WRAPPER}} .elbzotech-list-link-global' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'flex_wrap_item_link',
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
					'{{WRAPPER}} .elbzotech-list-link-global' => 'flex-wrap: {{VALUE}};',
				],
				'responsive' => true,
			]
		);
		$this->add_control(
			'icon_position',
			[
				'label' 	=> esc_html__( 'Icon position', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'left',
				'options'   => [
					'left'		=> esc_html__( 'Left', 'bw-printxtore' ),
					'right'		=> esc_html__( 'Right', 'bw-printxtore' ),
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs( 'tabs_style' );
		$this->start_controls_tab(
			'tab_normal',
			[
				'label' => esc_html__( 'Normal', 'bw-printxtore' ),
			]
		);
		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .item-link i' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Title Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .item-link .title' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'background_color',
			[
				'label' => esc_html__( 'Background Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .item-link' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'border_color_icon',
			[
				'label' => esc_html__( 'Border Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-button' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'label' => esc_html__( 'Typography', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .item-link',
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_hover',
			[
				'label' => esc_html__( 'Hover', 'bw-printxtore' ),
			]
		);
		$this->add_control(
			'icon_color_hover',
			[
				'label' => esc_html__( 'Icon Color Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .item-link:hover i' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'title_color_hover',
			[
				'label' => esc_html__( 'Title Color Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .item-link:hover .title' => 'color: {{VALUE}}',
					'{{WRAPPER}} .item-link:hover,{{WRAPPER}} .item-link:focus' => 'color: {{VALUE}}',
				],
			]
		);		
		$this->add_control(
			'background_color_hover',
			[
				'label' => esc_html__( 'Background Color hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .item-link:hover, {{WRAPPER}} .item-link:focus' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'border_color_hover',
			[
				'label' => esc_html__( 'Border Color hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .item-link:hover, {{WRAPPER}} .item-link:focus' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography_hover',
				'label' => esc_html__( 'Typography hover', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .item-link:hover',
			]
		);
		$this->add_responsive_control(
			'text_padding_hover',
			[
				'label' => esc_html__( 'Padding hover', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .item-link:hover, {{WRAPPER}} .item-link:focus' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'css_by_theme_text_hover',
			[
				'label' 	=> esc_html__( 'Add class hover style', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT2,
				'default'   => '',
				'options'   => bzotech_list_class_style_hover_by_theme(),
				'multiple'	=> true,
				'label_block' => true,
				'description'	=> esc_html__( 'Add class hover style by theme', 'bw-printxtore' ),
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		
		$this->add_responsive_control(
			'icon_size',
			[
				'label' => esc_html__( 'Icon size', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 150,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .item-link i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'space_icon',
			[
				'label' => esc_html__( 'Icon space with text', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 150,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .item-link.icon-position-left i' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .icon-position-left .icon-image-link' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .item-link.icon-position-right i' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .icon-position-right .icon-image-link' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'label' => esc_html__( 'Text Shadow', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .item-link .title',
			]
		);
		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters_title',
				'selector' => '{{WRAPPER}} .item-link .title',
			]
		);
		$this->add_responsive_control(
			'icon_padding',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .item-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'icon_margin',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .item-link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} .item-link',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .item-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'css_by_theme_text',
			[
				'label' 	=> esc_html__( 'Add class style', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT2,
				'default'   => '',
				'options'   => bzotech_list_class_style_by_theme(),
				'multiple'	=> true,
				'label_block' => true,
				'description'	=> esc_html__( 'Add class style by theme', 'bw-printxtore' ),
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_heading_style',
			[
				'label' => esc_html__( 'Heading style', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'heading_max_width_text',
			[
				'label' => esc_html__( 'Max Width', 'bw-printxtore' ) . ' (px)',
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .text-css-e' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'heading_color',
			[
				'label' => esc_html__( 'Text Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .text-css-e' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'heading_bg_color',
				'label' => esc_html__( 'Text Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .text-css-e',
				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'heading_typography',
				'selector' => '{{WRAPPER}} .text-css-e',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'heading_text_shadow',
				'selector' => '{{WRAPPER}} .text-css-e',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'heading_css_border_elementor',
				'selector' => '{{WRAPPER}} .text-css-e',
			]
		);
		$this->add_responsive_control(
			'heading_border_radius_box',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .text-css-e' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'heading_padding',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .text-css-e' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'heading_margin',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .text-css-e' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
	}
}?>