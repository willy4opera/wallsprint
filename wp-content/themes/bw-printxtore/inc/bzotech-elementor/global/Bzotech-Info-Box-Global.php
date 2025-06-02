<?php
namespace Elementor;
if ( ! defined( 'ABSPATH' ) ) exit; 

class Bzotech_Info_Box_Global extends Widget_Base {
	public function get_name() {
		return 'bzotech_info_box_global';
	}
	public function get_title() {
		return esc_html__( 'Info Box (Global)', 'bw-printxtore' );
	}
	public function get_icon() {
		return 'eicon-info-box';
	}
	public function get_categories() {
		return [ 'aqb-htelement-category' ];
	}

	public function get_style_depends() {
		return [ 'bzotech-el-info-box' ];
	}
	public function get_widget_css_config( $widget_name ) { 
	    $file_content_css = get_template_directory() . '/assets/global/css/elementor/info-box.css';
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
		echo bzotech_get_template_elementor_global('info-box/info-box',$settings['style'],$attr);
	}
	
	protected function content_template() {
		
	}
	
	protected function register_controls() {

		/*------------CONTENT--------- */
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
				'description'	=> esc_html__( 'You can change the display style here', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,				
				'default'   => '',
				'label_block' => true,
				'options'   => [
					''		=> esc_html__( 'Info custom', 'bw-printxtore' ),
					'style2'		=> esc_html__( 'Team', 'bw-printxtore' ),
					'style3'		=> esc_html__( 'Team style 2', 'bw-printxtore' ),
					'menu-vertical'		=> esc_html__( 'Menu vertical', 'bw-printxtore' ),
					'countdown'		=> esc_html__( 'Countdown', 'bw-printxtore' ),
				],
			]
		);
		
		$this->get_style_countdown('',['countdown']);
		$this->add_control(
			'icon',
			[
				'label' => esc_html__( 'Icon', 'bw-printxtore' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => '',
					'library' => 'solid',
				],
				'condition' => [
					'style' =>  ['','menu-vertical'],
					'icon_image[url]' => '',
				]
			]
		);
		$this->add_control(
			'icon_image',
			[
				'label' => esc_html__( 'Image', 'bw-printxtore' ),
				'description'	=> esc_html__( 'You can choose the icon image here (Replace for icon)', 'bw-printxtore' ),
				'type' => Controls_Manager::MEDIA,
				'condition' => [
					'icon[value]' =>  '',
					'style' =>  ['','style2','style3'],
				]
			]
		);
		$this->add_control(
			'icon_image_hover',
			[
				'label' => esc_html__( 'Image hover', 'bw-printxtore' ),
				'description'	=> esc_html__( 'You can choose the icon image here (Replace for icon)', 'bw-printxtore' ),
				'type' => Controls_Manager::MEDIA,
				'condition' => [
					'icon[value]' =>  '',
					'style' =>  [''],
					'icon_image[url]!' => '',
				]
			]
		);
		$this->get_setting_menu_vertical('menu_vertical',['menu-vertical']);
		$repeater_icon = new Repeater();
		$repeater_icon->add_control(
			'icon',
			[
				'label' => esc_html__( 'Icon', 'bw-printxtore' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => '',
					'library' => 'solid',
				],
			]
		);
		
		$repeater_icon->add_control(
			'link',
			[
				'label' => esc_html__( 'Link', 'bw-printxtore' ),
				'description'	=> esc_html__( 'You can add links for the element here', 'bw-printxtore' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'bw-printxtore' ),
				'show_label' => false,
				
			]
		);
		$this->add_control(
			'list_icon',
			[
				'label' => esc_html__( 'Add Icon', 'bw-printxtore' ),
				'type' => Controls_Manager::REPEATER,
				'prevent_empty'=>false,
				'fields' => $repeater_icon->get_controls(),
				'condition' => [
					'style' => ['style2','style3'],
				]
			]
		);


		$repeater_text = new Repeater();
		$repeater_text->add_control(
			'text', 
			[
				'label' => esc_html__( 'Text', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Enter text' , 'bw-printxtore' ),
				'label_block' => true,
			]
		);
		$repeater_text->add_control(
			'image',
			[
				'label' => esc_html__( 'Image', 'bw-printxtore' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$repeater_text->start_controls_tabs( 'item_text_effects' );
		$repeater_text->start_controls_tab( 'normal_item_text',
			[
				'label' => esc_html__( 'Normal', 'bw-printxtore' ),
			]
		);
		$repeater_text->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Text Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}} sup' => 'color: {{VALUE}};',
				],
			]
		);
		$repeater_text->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'bg_title',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
			]
		);
		$repeater_text->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
			]
		);
		$repeater_text->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
			]
		);
		$repeater_text->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_text',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
				'separator' => 'before',
			]
		);

		$repeater_text->add_responsive_control(
			'border_radius_text',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$repeater_text->add_control(
			'transform-rotate', 
			[
				'label' => esc_html__( 'Transform rotate', 'bw-printxtore' ),
				'type' => Controls_Manager::NUMBER,
				'min' => -180,
				'max' => 180,
				'step' => 1,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'transform: rotate({{SIZE}}deg);',
				],
			]
		);
		$repeater_text->end_controls_tab();
		$repeater_text->start_controls_tab( 'hover_item_text',
			[
				'label' => esc_html__( 'Hover', 'bw-printxtore' ),
			]
		);
		$repeater_text->add_control(
			'title_color_hover',
			[
				'label' => esc_html__( 'Text Color hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$repeater_text->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => esc_html__( 'Typography hover', 'bw-printxtore' ),
				'name' => 'text_typography_hover',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}:hover',
			]
		);
		$repeater_text->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow_hover',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}:hover',
			]
		);
		$repeater_text->add_control(
			'transform-rotate-hover', 
			[
				'label' => esc_html__( 'Transform rotate hover', 'bw-printxtore' ),
				'type' => Controls_Manager::NUMBER,
				'min' => -180,
				'max' => 180,
				'step' => 1,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}:hover' => 'transform: rotate({{SIZE}}deg);',
				],
			]
		);
		$repeater_text->end_controls_tab();
		$repeater_text->end_controls_tabs();
		
		$repeater_text->add_control(
			'align_info_item',
			[
				'label' => esc_html__( 'Alignment Info', 'bw-printxtore' ),
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
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'text-align: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		
		$repeater_text->add_control(
			'text_tag',
			[
				'label' => esc_html__( 'Tag wrap text', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => [
					'h2' 		=> esc_html__( 'H2', 'bw-printxtore' ),
					'h3' 		=> esc_html__( 'H3', 'bw-printxtore' ),
					'h4' 		=> esc_html__( 'H4', 'bw-printxtore' ),
					'h5' 		=> esc_html__( 'H5', 'bw-printxtore' ),
					'h6' 		=> esc_html__( 'H6', 'bw-printxtore' ),
					'p' 		=> esc_html__( 'p', 'bw-printxtore' ),
					'span' 		=> esc_html__( 'Span', 'bw-printxtore' ),
					'strong' 	=> esc_html__( 'Strong', 'bw-printxtore' ),
					'div' 		=> esc_html__( 'Div', 'bw-printxtore' ),
					'label' 	=> esc_html__( 'Label', 'bw-printxtore' ),
					'a' 	=> esc_html__( 'a', 'bw-printxtore' ),
				],
			]
		);
		$repeater_text->add_control(
			'link_text_tag',
			[
				'label' => esc_html__( 'Link (url)', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'default' => '#',
				'condition' => [
					'text_tag' => ['a'],
				],
				
			]
		);
		$repeater_text->add_responsive_control(
			'text_padding',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}'=> 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$repeater_text->add_responsive_control(
			'text_margin',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$repeater_text->add_control(
			'add_class_css', 
			[
				'label' => esc_html__( 'Add class CSS', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter name class' , 'bw-printxtore' ),
			]
		);
		
		$this->add_control(
			'list_text_info',
			[
				'label' => esc_html__( 'Add text', 'bw-printxtore' ),
				'type' => Controls_Manager::REPEATER,
				'prevent_empty'=>false,
				'fields' => $repeater_text->get_controls(),
				'title_field' => '{{{ text }}}',
				'condition' => [
					'style' => ['','style2','style3'],
				]
			]
		);
		$this->add_control(
			'link_info',
			[
				'label' => esc_html__( 'Link', 'bw-printxtore' ),
				'description'	=> esc_html__( 'You can add links for the element here', 'bw-printxtore' ),
				'type' => Controls_Manager::URL,
				'separator' => 'before',
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'bw-printxtore' ),
				'show_label' => false,
				'condition' => [
					'style' =>  ['','style2','style3'],
				]
			]
		);
		
		
		$this->end_controls_section();

		

		/*------------STYLE--------- */
		$this->start_controls_section(
			'section_style_container_flex',
			[
				'label' => esc_html__( 'Flex Container Control (Box)', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style' =>  ['','style2','style3','countdown']
				]
			]
		);
			$this->start_controls_tabs( 'container_info_tabs_style' );
				$this->start_controls_tab(
					'container_info_tabs_style_tab_normal',
					[
						'label' => esc_html__( 'Normal Style', 'bw-printxtore' ),
					]
				);

					$this->get_style_type_container_flex();
				$this->end_controls_tab(); 
				$this->start_controls_tab(
					'container_info_tabs_style_tab_hover',
					[
						'label' => esc_html__( 'Hover Style', 'bw-printxtore' ),
					]
				);
					$this->add_group_control(
						Group_Control_Background::get_type(),
						[
							'name' => 'container_info_tabs_bg_hover',
							'label' => esc_html__( 'Background Hover', 'bw-printxtore' ),
							'types' => [ 'classic', 'gradient'],
							'selector' => '{{WRAPPER}} .container-flex-e:hover',
						]
					);
					$this->add_group_control(
						Group_Control_Border::get_type(),
						[
							'name' => 'container_info_tabs__border_css_hover',
							'selector' => '{{WRAPPER}} .container-flex-e:hover',
							'separator' => 'before',
						]
					);
				$this->end_controls_tab(); 
			$this->end_controls_tabs();
		$this->end_controls_section(); /*End Icon style*/

		$this->start_controls_section(
			'section_style_container_flex-info',
			[
				'label' => esc_html__( 'Flex Container Control (Info)', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style' =>  ['','style2','style3','countdown']
				]
			]
		);
		$this->get_style_type_container_flex('info_container_flex','info-container-flex-e');
		$this->end_controls_section(); /*End Icon style*/

		$this->start_controls_section(
			'section_style_image_icon',
			[
				'label' => esc_html__( 'Image style', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'icon_image[url]!' =>  '',
				]
			]
		);
		$this->get_style_type_image('image_icon','item-image-icon-e');
		$this->end_controls_section(); /*End Icon style*/
		
		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => esc_html__( 'Icon style', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'icon[value]!' =>  '',
				]
			]
		);
		$this->get_style_type_icon('style_icon','item-icon-e');
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

		$this->end_controls_section(); /*End Icon style*/

		$this->start_controls_section(
			'section_style_title',
			[
				'label' => esc_html__( 'Title style', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
				'style' =>  ['countdown']
				]
			]
		);
		$this->get_style_type_text('title','item-title-e');
		
		$this->end_controls_section(); 
		$this->start_controls_section(
			'section_style_number',
			[
				'label' => esc_html__( 'Number style', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
				'style' =>  ['countdown']
				]
			]
		);
		$this->get_style_type_text('number','item-number-e');
		
		$this->end_controls_section(); 
		
		
	}
	protected function get_list_category($taxonomy='category',$by='slug'){
		$listcate = get_terms($taxonomy);
		
		$newarr = [];
		
		foreach($listcate as $value){
			if(!empty($value->$by))
			$newarr[$value->$by] = $value->name; 
		}
	
		return $newarr;
	
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
		$this->add_control(
			$key.'display_css',
			[
				'label' => esc_html__( 'Display', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'Normal', 'bw-printxtore' ),
					'inline' => esc_html__('Inline','bw-printxtore' ),
					'block' => esc_html__('Block','bw-printxtore' ),
					'inline-block' => esc_html__('Inline block	','bw-printxtore' ),
					'flex' => esc_html__('Flex','bw-printxtore' ),
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'display: {{VALUE}}',
				],
				'separator' => '',
			]
		);
		$start = is_rtl() ? 'right' : 'left';
		$end = is_rtl() ? 'left' : 'right';
		$this->add_responsive_control(
			$key.'_flex_direction',
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
					'{{WRAPPER}} .'.$class => 'flex-direction: {{VALUE}};',
				],
				'default' => '',
				'condition' => [
					$key.'display_css' =>  ['flex'],
				]
			]
		);
		$this->add_responsive_control(
			$key.'_alignment',
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
					'{{WRAPPER}} .'.$class => 'justify-content: {{VALUE}};',
				],
				'default' => '',
				'condition' => [
					$key.'display_css' =>  ['flex'],
				]
			]
		);

		$this->add_responsive_control(
			$key.'align_items',
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
					'{{WRAPPER}} .'.$class => 'align-items: {{VALUE}};',
				],
				'default' => '',
				'condition' => [
					$key.'display_css' =>  ['flex'],
				]
			]
		);
		$this->add_responsive_control(
			$key.'gap_item',
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
					'{{WRAPPER}} .'.$class => 'gap: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					$key.'display_css' =>  ['flex'],
				]
			]
		);
		$this->add_responsive_control(
			$key.'flex_wrap',
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
					'{{WRAPPER}} .'.$class => 'flex-wrap: {{VALUE}};',
				],
				'responsive' => true,
				'condition' => [
					$key.'display_css' =>  ['flex'],
				]
			]
		);
		
		$this->add_responsive_control(
			$key.'_width_css',
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
					'{{WRAPPER}} .'.$class => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			$key.'_height_css',
			[
				'label' => esc_html__( 'Hight', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%','px','vh' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'height: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'after',
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
         $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => $key.'_border_css',
				'selector' => '{{WRAPPER}} .'.$class,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			 $key.'_border_radius_css',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => $key.'_border_css',
				'selector' => '{{WRAPPER}} .'.$class,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			 $key.'_border_radius_css',
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
	public function get_style_type_image($key='image',$class="item-image-e") {

		$this->start_controls_tabs( $key.'_tabs_style' );
		$this->start_controls_tab(
			$key.'_tab_normal',
			[
				'label' => esc_html__( 'Normal Style', 'bw-printxtore' ),
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => $key, // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
				'include' => [],
				'default' => 'full',
			]
		);
		$this->add_responsive_control(
			$key.'_width_css',
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
					'{{WRAPPER}} .'.$class.' img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			$key.'_width_max_css',
			[
				'label' => esc_html__( 'Max Width', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%','px','vw' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class.' img' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			$key.'_height_css',
			[
				'label' => esc_html__( 'Height', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%','px','vh' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class.' img' => 'height: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'after',
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
					'{{WRAPPER}} .'.$class.' img' => 'opacity: {{SIZE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => $key.'_filters_css',
				'selector' => '{{WRAPPER}} .'.$class.' img',
			]
		);
		$this->add_responsive_control(
			$key.'_padding_css',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', ],
				'selectors' => [
					'{{WRAPPER}} .'.$class.' a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
        );

        $this->add_responsive_control(
			$key.'_margin_css',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', ],
				'selectors' => [
					'{{WRAPPER}} .'.$class.' a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => $key.'_border_css',
				'selector' => '{{WRAPPER}} .'.$class.' a',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			 $key.'_border_radius_css',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .'.$class.' a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' =>  $key.'_box_shadow_css',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .'.$class.' a',
			]
		);
		$this->end_controls_tab(); /*End Normal Style*/

		$this->start_controls_tab(
			$key.'_tab_hover',
			[
				'label' => esc_html__( 'Style On Hover', 'bw-printxtore' ),
			]
		);
		$this->add_control(
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
					'{{WRAPPER}} .'.$class.':hover img' => 'opacity: {{SIZE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'label' => esc_html__( 'Filters On Hover', 'bw-printxtore' ),
				'name' => $key.'_filters_hover_css',
				'selector' => '{{WRAPPER}} .'.$class.':hover img',
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
	public function get_style_type_container_flex($key='container_flex',$class="container-flex-e") {

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => $key.'_bg',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .'.$class,
			]
		);
		$start = is_rtl() ? 'right' : 'left';
		$end = is_rtl() ? 'left' : 'right';
		$this->add_responsive_control(
			$key.'_flex_direction',
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
					'{{WRAPPER}} .'.$class => 'flex-direction: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$this->add_responsive_control(
			$key.'_alignment',
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
					'{{WRAPPER}} .'.$class => 'justify-content: {{VALUE}};',
				],
				'default' => '',
			]
		);

		$this->add_responsive_control(
			$key.'align_items',
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
					'{{WRAPPER}} .'.$class => 'align-items: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$this->add_responsive_control(
			$key.'gap_item',
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
					'{{WRAPPER}} .'.$class => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			$key.'flex_wrap',
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
					'{{WRAPPER}} .'.$class => 'flex-wrap: {{VALUE}};',
				],
				'responsive' => true,
			]
		);
		
		$this->add_responsive_control(
			$key.'_width_css',
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
					'{{WRAPPER}} .'.$class => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			$key.'_height_css',
			[
				'label' => esc_html__( 'Hight', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%','px','vh' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .'.$class => 'height: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		
		$this->add_responsive_control(
			$key.'_padding_css',
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
			$key.'_margin_css',
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
				'name' => $key.'_border_css',
				'selector' => '{{WRAPPER}} .'.$class,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			 $key.'_border_radius_css',
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
				'name' =>  $key.'_box_shadow_css',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .'.$class,
			]
		);
	}
	public function get_setting_menu_vertical($key='menu_vertical',$condition=[],$class="menu-vertical-e") {

		$this->add_control(
			$key.'_title', [
				'label' => esc_html__( 'Title', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter text', 'bw-printxtore' ),
				'label_block' => true,
				'separator' => 'before',
				'default' => esc_html__( 'Title text', 'bw-printxtore' ),
				'condition' => [
					'style' =>$condition,
				]
			]
		);
		$this->add_control(
			'style_show_dropdow',
			[
				'label' 	=> esc_html__( 'The style show dropdow menu', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,				
				'default'   => 'show',
				'label_block' => true,
				'options'   => [
					'show'		=> esc_html__( 'Show dropdow menu', 'bw-printxtore' ),
					'hide'		=> esc_html__( 'Hide dropdow menu', 'bw-printxtore' ),
					'is-show-home'	=> esc_html__( 'Only show dropdown menu in homepage', 'bw-printxtore' ),
					
				],
				'condition' => [
					'style' =>$condition,
				]
			]
		);
			
		$menu_vertical = new Repeater();
		$menu_vertical->add_control(
			'icon',
			[
				'label' => esc_html__( 'Icon', 'bw-printxtore' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => '',
					'library' => 'solid',
				],
				'condition' => [
					'icon_image_menu[url]' => '',
				]
			]
		);
		$menu_vertical->add_control(
			'icon_image_menu',
			[
				'label' => esc_html__( 'Icon Image', 'bw-printxtore' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'icon[value]' =>  '',
				]
			]
		);
		$menu_vertical->add_control(
			'title', [
				'label' => esc_html__( 'Title', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter text', 'bw-printxtore' ),
				'label_block' => true,
				'separator' => 'before',
				'default' => esc_html__( 'Item menu', 'bw-printxtore' ),
				
			]
		);
		$menu_vertical->add_control(
			'link',
			[
				'label' => esc_html__( 'Link', 'bw-printxtore' ),
				'description'	=> esc_html__( 'You can add links for the element here', 'bw-printxtore' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'bw-printxtore' ),
				'show_label' => false,
				
			]
		);
		$menu_vertical->add_control(
			'template',
			[
				'label' 	=> esc_html__( 'Template content (Menu sub of item)', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'label_block' => true,
				'options'   => bzotech_list_post_type('bzotech_mega_item',true),
			]
		);

		$menu_vertical->start_controls_tabs( 'item_text_effects' );
		$menu_vertical->start_controls_tab( 'normal_item_text',
			[
				'label' => esc_html__( 'Normal', 'bw-printxtore' ),
			]
		);
		$menu_vertical->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Text Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}} sup' => 'color: {{VALUE}};',
				],
			]
		);
		$menu_vertical->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'bg_title',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
			]
		);
		$menu_vertical->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
			]
		);
		$menu_vertical->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
			]
		);
		$menu_vertical->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_text',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
				'separator' => 'before',
			]
		);

		$menu_vertical->add_responsive_control(
			'border_radius_text',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$menu_vertical->add_control(
			'transform-rotate', 
			[
				'label' => esc_html__( 'Transform rotate', 'bw-printxtore' ),
				'type' => Controls_Manager::NUMBER,
				'min' => -180,
				'max' => 180,
				'step' => 1,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'transform: rotate({{SIZE}}deg);',
				],
			]
		);
		$menu_vertical->end_controls_tab();
		$menu_vertical->start_controls_tab( 'hover_item_text',
			[
				'label' => esc_html__( 'Hover', 'bw-printxtore' ),
			]
		);
		$menu_vertical->add_control(
			'title_color_hover',
			[
				'label' => esc_html__( 'Text Color hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$menu_vertical->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => esc_html__( 'Typography hover', 'bw-printxtore' ),
				'name' => 'text_typography_hover',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}:hover',
			]
		);
		$menu_vertical->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow_hover',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}:hover',
			]
		);
		$menu_vertical->add_control(
			'transform-rotate-hover', 
			[
				'label' => esc_html__( 'Transform rotate hover', 'bw-printxtore' ),
				'type' => Controls_Manager::NUMBER,
				'min' => -180,
				'max' => 180,
				'step' => 1,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}:hover' => 'transform: rotate({{SIZE}}deg);',
				],
			]
		);
		$menu_vertical->end_controls_tab();
		$menu_vertical->end_controls_tabs();
		
		$menu_vertical->add_responsive_control(
			'align_info_item',
			[
				'label' => esc_html__( 'Alignment Info', 'bw-printxtore' ),
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
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'text-align: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			$key.'vertical_width_submenu',
			[
				'label' => esc_html__( 'Width sub menu', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%','px'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .list-menu-vertical__item-sub' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			$key.'_list',
			[
				'label' => esc_html__( 'Add item menu', 'bw-printxtore' ),
				'type' => Controls_Manager::REPEATER,
				'prevent_empty'=>false,
				'fields' => $menu_vertical->get_controls(),
				'condition' => [
					'style' => $condition,
				]
			]
		);
	}
	public function get_style_countdown($key='',$condition=[],$class="countdown-e") {

		$this->add_control(
			$key.'date', [
				'label' => esc_html__( 'Date', 'bw-printxtore' ),
				'type' => Controls_Manager::DATE_TIME,
				'placeholder' => esc_html__( 'Set date', 'bw-printxtore' ),
				'label_block' => true,
				'condition' => [
					'style' => $condition,
				]
			]
		);
		$this->add_control(
			$key.'day', [
				'label' => esc_html__( 'Title day', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'description' => esc_html__( 'Enter text (Leave it blank will hide it)', 'bw-printxtore' ),
				'label_block' => true,
				'default' => '',
				'condition' => [
					'style' => $condition,
				]
			]
		);
		$this->add_control(
			$key.'hour', [
				'label' => esc_html__( 'Title hour', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'description' => esc_html__( 'Enter text (Leave it blank will hide it)', 'bw-printxtore' ),
				'label_block' => true,
				'default' =>'',
				'condition' => [
					'style' => $condition,
				]
			]
		);
		$this->add_control(
			$key.'min', [
				'label' => esc_html__( 'Title min', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'description' => esc_html__( 'Enter text (Leave it blank will hide it)', 'bw-printxtore' ),
				'label_block' => true,
				'default' => '',
				'condition' => [
					'style' => $condition,
				]
			]
		);
		$this->add_control(
			$key.'sec', [
				'label' => esc_html__( 'Title sec', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'description' => esc_html__( 'Enter text (Leave it blank will hide it)', 'bw-printxtore' ),
				'label_block' => true,
				'default' => '',
				'condition' => [
					'style' => $condition,
				]
			]
		);
	}
} ?>