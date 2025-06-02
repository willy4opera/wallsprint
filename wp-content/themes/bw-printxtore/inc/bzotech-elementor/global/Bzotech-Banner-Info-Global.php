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
class Bzotech_Banner_Info_Global extends Widget_Base {

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
		return 'bzotech-banner-info-global';
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
		return esc_html__( 'Banner info (Global)', 'bw-printxtore' );
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
		return [ 'bzotech-el-banner-info' ];
	}
	public function get_widget_css_config( $widget_name ) { 
	    $file_content_css = get_template_directory() . '/assets/global/css/elementor/banner-info.css';
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
		echo bzotech_get_template_elementor_global('banner-info/banner-info',$settings['banner_style'],$attr);
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

			'section_image',
			[
				'label' => esc_html__( 'Image', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				
			]
		);
		$this->add_control(
			'banner_style',
			[
				'label' => esc_html__( 'Banner Style', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'elbzotech-bndf',
				'options' => [
					'elbzotech-bndf'  => esc_html__( 'style 1 (Default)', 'bw-printxtore' ),
					'style2'  => esc_html__( 'style 2 (Video popup)', 'bw-printxtore' ),
					'style3'  => esc_html__( 'style 3 (Point)', 'bw-printxtore' ),					
					'style4'  => esc_html__( 'style 4 (Category)', 'bw-printxtore' ),
					'category'  => esc_html__( 'style 5 (Category)', 'bw-printxtore' ),
					'style5'  => esc_html__( 'style 6 (Count up)', 'bw-printxtore' ),
				],
			]
		);
		$this->add_control(
			'image',
			[
				'label' => esc_html__( 'Choose Image', 'bw-printxtore' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default' => 'full',
				'separator' => 'none',
			]
		);

		$this->add_responsive_control(
			'image-width',
			[
				'label' => esc_html__( 'Width image', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'100%' => esc_html__( 'Full width 100%', 'bw-printxtore' ),
					'auto' => esc_html__( 'Auto', 'bw-printxtore' ),
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .adv-thumb-link img' => 'width: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'align-image',
			[
				'label' => esc_html__( 'Alignment image', 'bw-printxtore' ),
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
					'{{WRAPPER}} .adv-thumb-link' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'image-width' => ['auto'],
				],
			]
		);

		$this->add_control(
			'link',
			[
				'label' => esc_html__( 'Link image', 'bw-printxtore' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'bw-printxtore' ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => false,
					'nofollow' => true,
				],
				'condition' => [
					'banner_style!' => ['style2'],
				],
			]
		);
		$this->add_control(
			'link_video',
			[
				'label' => esc_html__( 'Link Video', 'bw-printxtore' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'bw-printxtore' ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => false,
					'nofollow' => true,
				],
				'condition' => [
					'banner_style' => ['style2'],
				],
			]
		);
		$this->add_control(
			'icon_button_video',
			[
				'label' => esc_html__( 'Icon button video', 'bw-printxtore' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => '',
					'library' => 'solid',
				],
				'condition' => [
					'banner_style' =>  ['style2'],
				]
			]
		);			
		
		$this->add_control(
			'category',
			[
				'label' 	=> esc_html__( 'Get category', 'bw-printxtore' ),
				'description'	=> esc_html__( 'You can change the display category here', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'label_block' => true,
				'options'   => bzotech_get_list_category('product_cat'),
				'condition' => [
					'banner_style' =>  ['style4','category'],
				]
			]
		);	
		$this->add_control(
			'title_category',
			[
				'label' 	=> esc_html__( 'Title', 'bw-printxtore' ),
				'description'	=> esc_html__( 'You can change the display title here', 'bw-printxtore' ),
				'type'      => Controls_Manager::TEXT,
				'label_block' => true,
				'options'   => bzotech_get_list_category('product_cat'),
				'condition' => [
					'banner_style' =>  ['style4','category'],
				]
			]
		);	
		$this->add_control(
			'desc_category',
			[
				'label' 	=> esc_html__( 'Description', 'bw-printxtore' ),
				'description'	=> esc_html__( 'You can change the display description here', 'bw-printxtore' ),
				'type'      => Controls_Manager::TEXT,
				'label_block' => true,
				'options'   => bzotech_get_list_category('product_cat'),
				'condition' => [
					'banner_style' =>  ['style4','category'],
				]
			]
		);	
		$this->end_controls_section();
		
		/*info style style3*/
		$this->start_controls_section(
			'section_info_style3',
			[
				'label' => esc_html__( 'Text info', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'banner_style' => ['style3'],
				]
			]
		);
		$repeater_style3 = new Repeater();
		$repeater_style3->add_control(
			'id_product', 
			[
				'label' => esc_html__( 'Get info by id product', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter id product' , 'bw-printxtore' ),
				'label_block' => true,
				'condition' => [
					'info' =>'',
				]
			]
		);
		$repeater_style3->add_control(
			'info', 
			[
				'label' => esc_html__( 'Info', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Enter product information' , 'bw-printxtore' ),
				'label_block' => true,
				'condition' => [
					'id_product' =>'',
				]
			]
		);
		$repeater_style3->add_control(
			'link',
			[
				'label' => esc_html__( 'Link', 'bw-printxtore' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'bw-printxtore' ),
				'show_external' => true,
				'default' => [
					'url' => '#',
					'is_external' => false,
					'nofollow' => true,
				],
				'condition' => [
					'id_product' =>'',
				]
			]
		);
		$repeater_style3->add_control(
			'pos_left',
			[
				'label' => esc_html__( 'Position left', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'max' => 100,
						'min' => 0,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .list-point__item' => 'left: {{SIZE}}%;',
				],
			]
		);
		$repeater_style3->add_control(
			'pos_top',
			[
				'label' => esc_html__( 'Position Top', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'max' => 100,
						'min' => 0,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .list-point__item' => 'top: {{SIZE}}%;',
				],
			]
		);
		$repeater_style3->add_control(
			'active',
			[
				'label' => esc_html__( 'Active or Hover active', 'bw-printxtore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Active', 'bw-printxtore' ),
				'label_off' => esc_html__( 'Hover', 'bw-printxtore' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'list_point',
			[
				'label' => esc_html__( 'Add point', 'bw-printxtore' ),
				'type' => Controls_Manager::REPEATER,
				'prevent_empty'=>false,
				'fields' => $repeater_style3->get_controls(),
			]
		);
		$this->end_controls_section();
		/*info style style5*/
		$this->start_controls_section(
			'section_info_style5',
			[
				'label' => esc_html__( 'Text info', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'banner_style' => ['style5'],
				]
			]
		);
		$repeater_style5 = new Repeater();
		$repeater_style5->add_control(
			'title', 
			[
				'label' => esc_html__( 'Title', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter product information' , 'bw-printxtore' ),
				'label_block' => true,
			]
		);
		$repeater_style5->add_control(
			'number', 
			[
				'label' => esc_html__( 'Number', 'bw-printxtore' ),
				'type' => Controls_Manager::NUMBER,
				'placeholder' => esc_html__( '35' , 'bw-printxtore' ),
				'min' => 1,
				'step' => 1,
			]
		);
		$repeater_style5->add_control(
			'unit', 
			[
				'label' => esc_html__( 'Unit', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'K+' , 'bw-printxtore' ),
				'label_block' => true,
			]
		);
		$repeater_style5->add_responsive_control(
			'pos_left',
			[
				'label' => esc_html__( 'Position left', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'max' => 100,
						'min' => 0,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'left: {{SIZE}}%;',
				],
			]
		);
		$repeater_style5->add_responsive_control(
			'pos_top',
			[
				'label' => esc_html__( 'Position Top', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'max' => 100,
						'min' => 0,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'top: {{SIZE}}%;',
				],
			]
		);
		$repeater_style5->add_responsive_control(
			'swivel',
			[
				'label' => esc_html__( 'Swivel', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 360,
						'min' => 0,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'transform: rotate({{SIZE}}deg);',
				],
			]
		);

		$this->add_control(
			'list_count_up',
			[
				'label' => esc_html__( 'Add count up', 'bw-printxtore' ),
				'type' => Controls_Manager::REPEATER,
				'prevent_empty'=>false,
				'fields' => $repeater_style5->get_controls(),
			]
		);
		$this->end_controls_section();
		/*info style default*/
		$this->start_controls_section(
			'section_info_default',
			[
				'label' => esc_html__( 'Text info', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'banner_style' => ['elbzotech-bndf'],
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
		$repeater_text->add_control(
			'add_class_css', 
			[
				'label' => esc_html__( 'Add class CSS', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter name class' , 'bw-printxtore' ),
			]
		);
		
		$this->add_control(
			'list_text',
			[
				'label' => esc_html__( 'Add text', 'bw-printxtore' ),
				'type' => Controls_Manager::REPEATER,
				'prevent_empty'=>false,
				'fields' => $repeater_text->get_controls(),
				'title_field' => '{{{ text }}}',
			]
		);


		$repeater_bt = new Repeater();
		$repeater_bt->add_control(
			'text', [
				'label' => esc_html__( 'Label button', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Enter text' , 'bw-printxtore' ),
				'label_block' => true,
			]
		);
		$repeater_bt->add_control(
			'link',
			[
				'label' => esc_html__( 'Link', 'bw-printxtore' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'bw-printxtore' ),
				'show_external' => true,
				'default' => [
					'url' => '#',
					'is_external' => false,
					'nofollow' => true,
				],
			]
		);
		$repeater_bt->add_control(
			'button_icon',
			[
				'label' => esc_html__( 'Icon', 'bw-printxtore' ),
				'type' => Controls_Manager::ICONS,
			]
		);

		$repeater_bt->add_control(
			'button_icon_pos',
			[
				'label' => esc_html__( 'Icon position', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'after-text',
				'options' => [
					'after-text'   => esc_html__( 'After text', 'bw-printxtore' ),
					'before-text'  => esc_html__( 'Before text', 'bw-printxtore' ),
				],
				'condition' => [
					'button_icon[value]!' => '',
				]
			]
		);
		$repeater_bt->add_control(
			'style',
			[
				'label' => esc_html__( 'Style', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'		=> esc_html__( 'Style 1', 'bw-printxtore' ),
					'style2'		=> esc_html__( 'Style 2', 'bw-printxtore' ),
					'style3'		=> esc_html__( 'Style 3', 'bw-printxtore' ),
					'custom'		=> esc_html__( 'Custom', 'bw-printxtore' ),
				],
			]
		);
		$repeater_bt->add_control(
			'css_by_theme_button',
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

		$repeater_bt->add_control(
			'add_class_css', 
			[
				'label' => esc_html__( 'Add class CSS', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter name class' , 'bw-printxtore' ),
			]
		);
		$key = 'custom_botton';
		$class = 'item-custom-botton-e';

		$repeater_bt->start_controls_tabs( 
			$key.'_tabs_style',
			[
				'separator' => 'before',
				'condition' => [
					'style' => 'custom',
				]
			]
		);
		$repeater_bt->start_controls_tab(
			$key.'_tab_normal_css',
			[
				'label' => esc_html__( 'Normal Style', 'bw-printxtore' ),
			]
		);
		$repeater_bt->add_control(
			$key.'_color_css',
			[
				'label' => esc_html__( 'Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}}',
					'{{WRAPPER}} {{CURRENT_ITEM}} .sub-color-e' => 'color: {{VALUE}}',
				],
			]
		);
		$repeater_bt->add_control(
			$key.'bg_color_css',
			[
				'label' => esc_html__( 'Background Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'background-color: {{VALUE}};',
				],
			]
		);
		$repeater_bt->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => $key.'_typography_css',
				'label' => esc_html__( 'Text Typography', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
			]
		);
		$repeater_bt->add_responsive_control(
			$key.'_size_icon_css',
			[
				'label' => esc_html__( 'Font Size Icon', 'bw-printxtore' ),
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
					'{{WRAPPER}} {{CURRENT_ITEM}} .icon-button-el' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$repeater_bt->add_responsive_control(
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
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'opacity: {{SIZE}};',
				],
			]
		);
		$start = is_rtl() ? 'right' : 'left';
		$end = is_rtl() ? 'left' : 'right';
		$repeater_bt->add_responsive_control(
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
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'flex-direction: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$repeater_bt->add_responsive_control(
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
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'justify-content: {{VALUE}};',
				],
				'default' => '',
			]
		);

		$repeater_bt->add_responsive_control(
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
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'align-items: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$repeater_bt->add_responsive_control(
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
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$repeater_bt->add_responsive_control(
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
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'flex-wrap: {{VALUE}};',
				],
				'responsive' => true,
			]
		);
		$repeater_bt->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => $key.'_shadow_css',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
			]
		);
		$repeater_bt->add_responsive_control(
			$key.'_padding_css',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px'],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
        );

        $repeater_bt->add_responsive_control(
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
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
        $repeater_bt->add_responsive_control(
			$key.'_margin_css',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px'],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
        $repeater_bt->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => $key.'_border_css',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
				'separator' => 'before',
			]
		);

		$repeater_bt->add_responsive_control(
			 $key.'_border_radius_css',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$repeater_bt->end_controls_tab(); /*End Normal Style*/

		$repeater_bt->start_controls_tab(
			$key.'_tab_hover_css',
			[
				'label' => esc_html__( 'Style On Hover', 'bw-printxtore' ),
			]
		);
		$repeater_bt->add_control(
			$key.'_color_hover_css',
			[
				'label' => esc_html__( 'Color On Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} {{CURRENT_ITEM}}:hover .sub-color-e' => 'color: {{VALUE}}',
				],
			]
		);
		
		$repeater_bt->add_control(
			$key.'_bg_color_hover_css',
			[
				'label' => esc_html__( 'Background Color On Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}:hover, {{WRAPPER}} {{CURRENT_ITEM}}:focus' => 'background-color: {{VALUE}};',
				],
			]
		);
		$repeater_bt->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => $key.'_typography_hover_css',
				'label' => esc_html__( 'Typography On Hover', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}:hover',
			]
		);
		$repeater_bt->add_responsive_control(
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
					'{{WRAPPER}} {{CURRENT_ITEM}}:hover' => 'opacity: {{SIZE}};',
				],
			]
		);
		$repeater_bt->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => $key.'_shadow_hover_css',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}:hover',
			]
		);
		$repeater_bt->add_control(
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
					'{{WRAPPER}}  {{CURRENT_ITEM}}' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
		$repeater_bt->add_control(
			$key.'_animation_hover_css',
			[
				'label' => esc_html__( 'Animation On Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);
		$repeater_bt->end_controls_tab();/*End Hover Style*/
		$repeater_bt->end_controls_tabs();

		/*-----------------------------*/
		$this->add_control(
			'separator_list_button',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);
		$this->add_control(
			'list_button',
			[
				'label' => esc_html__( 'Add button', 'bw-printxtore' ),
				'type' => Controls_Manager::REPEATER,
				'prevent_empty'=>false,
				'fields' => $repeater_bt->get_controls(),
				'title_field' => '{{{ text }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_image',
			[
				'label' => esc_html__( 'Image', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'image2',
			[
				'label' => esc_html__( 'Choose Image 2 of Effect', 'bw-printxtore' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'image_effect_banner' => ['zoom-out','zoom-out overlay-image'],
				]
			]
		);
		

		$this->add_control(
			'box_overflow',
			[
				'label' => esc_html__( 'Overflow', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'elbzotech-hidden',
				'options' => [
					'elbzotech-hidden' 		=> esc_html__( 'Hidden', 'bw-printxtore' ),
					'elbzotech-inherit' 		=> esc_html__( 'Inherit', 'bw-printxtore' ),
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'separator' => 'before',
				'name' => 'background_overlay',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .background-overlay-color',
			]
		);
		$this->add_responsive_control(
			'align_image',
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
					'right ' => [
						'title' => esc_html__( 'Right', 'bw-printxtore' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-banner-info-global-thumb.elbzotech-inherit' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'box_overflow' => ['elbzotech-inherit'],
				]
			]
		);
		$this->add_control(
			'separator_image_style',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->start_controls_tabs( 'image_effects' );

		$this->start_controls_tab( 'normal',
			[
				'label' => esc_html__( 'Normal', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			'opacity',
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
					'{{WRAPPER}} .elbzotech-banner-info-global-thumb img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .elbzotech-banner-info-global-thumb img',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'hover',
			[
				'label' => esc_html__( 'Hover', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			'opacity_hover',
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
					'{{WRAPPER}} .elbzotech-banner-info-global-thumb img:hover' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .elbzotech-banner-info-global-wrap:hover img',
			]
		);

		$this->add_control(
			'background_hover_transition',
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
					'{{WRAPPER}} .elbzotech-banner-info-global-thumb img' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->add_control(
			'image_hover_animation',
			[
				'label' => esc_html__( 'Hover Animation', 'bw-printxtore' ),
				'type' => Controls_Manager::HOVER_ANIMATION,

				'condition' => [
					'image_effect_banner' => '',
				],
			]
		);
		$this->add_control(
			'image_effect_banner',
			[
				'label' => esc_html__( 'Effect Image', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'label_block'=>true,
				'condition' => [
					'image_hover_animation' => '',
				],
				'options' => [
					''  => esc_html__( 'None', 'bw-printxtore' ),
					'zoom-image'  => esc_html__( 'Zoom', 'bw-printxtore' ),
					'overlay-image zoom-image'  => esc_html__( 'Zoom Overlay', 'bw-printxtore' ),
					'zoom-out'  => esc_html__( 'Zoom out', 'bw-printxtore' ),
					'zoom-out overlay-image'  => esc_html__( 'Zoom out Overlay', 'bw-printxtore' ),
					'fade-out-in'  => esc_html__( 'Fade out-in', 'bw-printxtore' ),
					'zoom-image fade-out-in'  => esc_html__( 'Zoom Fade out-in', 'bw-printxtore' ),
					'fade-in-out'  => esc_html__( 'Fade in-out', 'bw-printxtore' ),
					'zoom-rotate'  => esc_html__( 'Zoom rotate', 'bw-printxtore' ),
					'zoom-rotate fade-out-in'  => esc_html__( 'Zoom rotate Fade out-in', 'bw-printxtore' ),
					'overlay-image'  => esc_html__( 'Overlay', 'bw-printxtore' ),
					'overlay-image-style2'  => esc_html__( 'Overlay Style 2', 'bw-printxtore' ),
					'zoom-image line-scale'  => esc_html__( 'Zoom image line', 'bw-printxtore' ),
					'gray-image'  => esc_html__( 'Gray image', 'bw-printxtore' ),
					'gray-image line-scale'  => esc_html__( 'Gray image line', 'bw-printxtore' ),
					'pull-curtain'  => esc_html__( 'Pull curtain', 'bw-printxtore' ),
					'pull-curtain gray-image'  => esc_html__( 'Pull curtain gray image', 'bw-printxtore' ),
					'pull-curtain zoom-image'  => esc_html__( 'Pull curtain zoom image', 'bw-printxtore' ),
					'folding-the-edge'  => esc_html__( 'Folding the edge', 'bw-printxtore' ),
					'hover-icon'  => esc_html__( 'Hover icon', 'bw-printxtore' )
				],
			]
		);
		$this->add_control(
			'effect_hover_icon',
			[
				'label' => esc_html__( 'Select icon hover', 'bw-printxtore' ),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'image_effect_banner' => 'hover-icon',
				],
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'image_border',
				'selector' => '{{WRAPPER}} .elbzotech-banner-info-global-thumb',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-banner-info-global-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elbzotech-banner-info-global-thumb img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .elbzotech-banner-info-global-thumb',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_info',
			[
				'label' => esc_html__( 'Wrap info banner', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'banner_style' => ['elbzotech-bndf'],
				]
			]
		);
		$this->add_responsive_control(
			'width_max_info',
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
					'{{WRAPPER}} .info-banner2,{{WRAPPER}} .position-info-custom' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'style_info',
			[
				'label' => esc_html__( 'Style Info', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'elbzotech-info-inner',
				'options' => [
					'elbzotech-info-inner' 		=> esc_html__( 'Inner', 'bw-printxtore' ),
					'elbzotech-info-outer' 		=> esc_html__( 'Outter', 'bw-printxtore' ),
				],
			]
		);
		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'bw-printxtore' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'text-left' => [
						'title' => esc_html__( 'Left', 'bw-printxtore' ),
						'icon' => 'eicon-text-align-left',
					],
					'text-center' => [
						'title' => esc_html__( 'Center', 'bw-printxtore' ),
						'icon' => 'eicon-text-align-center',
					],
					'text-right' => [
						'title' => esc_html__( 'Right', 'bw-printxtore' ),
						'icon' => 'eicon-text-align-right',
					],
				],
			]
		);

		$this->add_responsive_control(
			'justify_info_wrap',
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
					'{{WRAPPER}} .elbzotech-banner-info' => 'justify-content: {{VALUE}};',
				],
				'default' => '',
				
			]
		);

		$this->add_responsive_control(
			'align_items_info_wrap',
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
					'{{WRAPPER}} .elbzotech-banner-info' => 'align-items: {{VALUE}};',
				],
				'default' => '',
			]
		);

		$this->add_responsive_control(
			'position_info_top',
			[

				'label' => esc_html__( 'Top', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'description' => esc_html__( 'Enter parameter for the top position (Ex: 0px | 10px | 10% | auto)', 'bw-printxtore' ),
				'condition' => [
					'style_info' => ['elbzotech-info-inner'],
				],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-info-inner' => 'top: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'position_info_bottom',
			[
				'label' => esc_html__( 'Bottom', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'description' => esc_html__( 'Enter parameter for the bottom position (Ex: 0px | 10px | 10% | auto)', 'bw-printxtore' ),
				'condition' => [
					'style_info' => ['elbzotech-info-inner'],
				],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-info-inner' => 'bottom: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'position_info_left',
			[
				'label' => esc_html__( 'Left', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'description' => esc_html__( 'Enter parameter for the left position (Ex: 0px | 10px | 10% | auto)', 'bw-printxtore' ),
				'condition' => [
					'style_info' => ['elbzotech-info-inner'],
				],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-info-inner' => 'left: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'position_info_right',
			[
				'label' => esc_html__( 'Right', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'description' => esc_html__( 'Enter parameter for the Right position (Ex: 0px | 10px | 10% | auto)', 'bw-printxtore' ),
				'condition' => [
					'style_info' => ['elbzotech-info-inner'],
				],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-info-inner' => 'right: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'separator' => 'before',
				'name' => 'background',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .elbzotech-banner-info',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'info_shadow',
				'label' => esc_html__( 'Box Shadow', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .elbzotech-banner-info',
			]
		);
		$this->add_responsive_control(
			'padding_info',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-banner-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'margin_info',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-banner-info' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'display_css_info',
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
					'{{WRAPPER}} .info-banner2' => 'display: {{VALUE}}',
				],
				'separator' => '',
			]
		);
		$this->add_control(
			'info_wrap_css_by_theme',
			[
				'label' 	=> esc_html__( 'Add class style', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT2,
				'default'   => '',
				'options'   => bzotech_list_class_style_by_theme(),
				'multiple'	=> true,
				'label_block' => true,
				'description'	=> esc_html__( 'Add class style by theme root', 'bw-printxtore' ),
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_info_style_container_flex',
			[
				'label' => esc_html__( 'Info banner', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				
			]
		);
		$this->get_style_type_container_flex('info','info-container-flex-e');
		
		$this->end_controls_section(); 
		$this->start_controls_section(
			'btwrapinfo_style_container_flex',
			[
				'label' => esc_html__( 'Button wrap', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->get_style_type_container_flex('btwrapinfo','btwrapinfo-container-flex-e');
		
		$this->end_controls_section(); 
		$this->start_controls_section(
			'section_wrap_style_info_outer_container_flex',
			[
				'label' => esc_html__( 'Wrap style info outer', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style_info' => ['elbzotech-info-outer'],
				],
			]
		);
		$this->add_responsive_control(
			'info_outer_col_width_css',
			[
				'label' => esc_html__( 'Column Width', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%','px','vw' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .wrap-flex-e-elbzotech-info-outer .elbzotech-banner-info-global-thumb' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .wrap-flex-e-elbzotech-info-outer .elbzotech-info-outer' => 'width: calc(100% - {{SIZE}}{{UNIT}});',
				],
			]
		);
		$this->add_responsive_control(
			'info_outer_col_info_width_css',
			[
				'label' => esc_html__( 'Width info', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%','px','vw' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .wrap-flex-e-elbzotech-info-outer .elbzotech-info-outer' => 'width:  {{SIZE}}{{UNIT}}!important;',
				],
			]
		);
		$this->get_style_type_container_flex('wrap-info-inner','wrap-flex-e-elbzotech-info-outer');
		
		$this->end_controls_section(); 
		
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

		$this->add_control(
			$key.'_css_by_theme',
			[
				'label' 	=> esc_html__( 'Add class style', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT2,
				'default'   => '',
				'options'   => bzotech_list_class_style_by_theme(),
				'multiple'	=> true,
				'label_block' => true,
				'description'	=> esc_html__( 'Add class style by theme root', 'bw-printxtore' ),
			]
		);
	}
	
}