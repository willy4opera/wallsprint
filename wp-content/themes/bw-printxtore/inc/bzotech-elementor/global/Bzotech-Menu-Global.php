<?php
namespace Elementor;
use Bzotech_Walker_Nav_Menu;
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Elementor Hello World
 *
 * Elementor widget for hello world.
 *
 * @since 1.0.0
 */
class Bzotech_Menu_Global extends Widget_Base {

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
		return 'bzotech-menu-global';
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
		return esc_html__( 'Menu (Global)', 'bw-printxtore' );
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

	public function get_menus(){
        $list = [];
        $menus = wp_get_nav_menus();
        foreach($menus as $menu){
            $list[$menu->slug] = $menu->name;
        }

        return $list;
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
		return [ 'bzotech-el-menu' ];
	}
	public function get_widget_css_config( $widget_name ) { 
	    $file_content_css = get_template_directory() . '/assets/global/css/elementor/menu.css';
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
            'content_tab',
            [
                'label' => esc_html__('Menu settings', 'bw-printxtore'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
		);

        $this->add_control(
            'nav_menu',
            [
                'label'     =>esc_html__( 'Select menu', 'bw-printxtore' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => $this->get_menus(),
            ]
		);

		$this->add_control(
			'main_menu_style',
			[
				'label' => esc_html__( 'Menu style', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''  => esc_html__( 'Default', 'bw-printxtore' ),
					'icon' => esc_html__( 'Menu icon', 'bw-printxtore' ),
				],
			]
		);
		$this->add_control(
			'menu_sticky',
			[
				'label' => esc_html__( 'Menu sticky', 'bw-printxtore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'bw-printxtore' ),
				'label_off' => esc_html__( 'Off', 'bw-printxtore' ),
				'return_value' => 'on',
				'default' => '',
			]
		);
		$this->add_control(
			'megamenu_max_width',
			[
				'label' => esc_html__( 'Mega menu max width', 'bw-printxtore' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 2000,
				'step' => 1,
				'default' => '',
				'description' => esc_html__( 'This index is used to determine the maximum width of the mega menu. Default is equal to container: 1440px', 'bw-printxtore' ),
			]
		);

        $this->add_responsive_control(
			'alignment_menu_box',
			[
				'label' => esc_html__( 'Justify Content Text', 'bw-printxtore' ),
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
					'{{WRAPPER}} .menu-global-style- .bzotech-navbar-nav' => 'justify-content: {{VALUE}};',
				],
				'default' => '',
				'condition' => [
					'main_menu_style' => '',
				]
			]
		);
		$this->add_responsive_control(
			'align_items_menu_box',
			[
				'label' => esc_html__( 'Align Items', 'bw-printxtore' ),
				'type' => Controls_Manager::CHOOSE,
				'responsive' => true,
				'label_block' => true,
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
					'{{WRAPPER}} .menu-global-style- .bzotech-navbar-nav' => 'align-items: {{VALUE}};',
				],
				'default' => '',
				'condition' => [
					'main_menu_style' => '',
				]
			]
		);
        $this->add_responsive_control(
			'menu_icon_position_content',
			[
				'label' => esc_html__( 'Menu position content', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'  => esc_html__( 'Left', 'bw-printxtore' ),
                    'right' => esc_html__( 'Right', 'bw-printxtore' ),
				],

				'condition' => [
					'main_menu_style' => 'icon',
				]
			]
		);
		$this->add_control(
			'menu_mobile_style',
			[
				'label' => esc_html__( 'Display menu style on mobile', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''  => esc_html__( 'Default dropdown type', 'bw-printxtore' ),
					'left'  => esc_html__( 'Side Left', 'bw-printxtore' ),
                    'right' => esc_html__( 'Side Right', 'bw-printxtore' ),
				],
				'label_block' => true,
				'condition' => [
					'main_menu_style' => '',
				]
			]
		);
		$this->add_control(
			'menu_icon_title_text',
			[
				'label' => esc_html__( 'Menu icon title ', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter text' , 'bw-printxtore' ),
				'condition' => [
					'main_menu_style' => 'icon',
				]
			]
		);

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'bzotech_menubar_background',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
                'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .menu-global-style- .bzotech-navbar-nav',
				'condition' => [
					'main_menu_style' => '',
				]
			]
        );
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_menu_box',
				'selector' => '{{WRAPPER}} .menu-global-style- .bzotech-navbar-nav',
				'separator' => 'before',
				'condition' => [
					'main_menu_style' => '',
				]
			]
		);
        $this->add_responsive_control(
			'border_radius_menu_box',
			[
				'label' => esc_html__( 'Border radius', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .menu-global-style- .bzotech-navbar-nav' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'main_menu_style' => '',
				]
			]
        ); 


		$this->end_controls_section();

		$this->start_controls_section(
            'content_side_tab',
            [
                'label' => esc_html__('Menu side/mobile', 'bw-printxtore'),
                'tab' => Controls_Manager::TAB_CONTENT,
                'condition' => [
					'main_menu_style' => 'icon',
				]
            ]

		);

		$this->add_control(
			'bzotech_nav_menu_logo',
			[
				'label' => esc_html__( 'Choose Mobile Menu Logo', 'bw-printxtore' ),
				'type' => Controls_Manager::MEDIA,
			]
		);

		$this->add_responsive_control(
            'mobile_menu_panel_background',
            [
                'label' => esc_html__( 'Item text color', 'bw-printxtore' ),
                'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .menu-style-icon .bzotech-menu-inner' => 'background-image: linear-gradient(180deg, {{VALUE}} 0%, {{VALUE}} 100%);',
				],
            ]
        );

		$this->add_responsive_control(
			'mobile_menu_panel_spacing',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .menu-style-icon .bzotech-menu-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'mobile_menu_panel__head_spacing',
			[
				'label' => esc_html__( 'Head Padding', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bzotech-nav-identity-panel.panel-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'mobile_menu_panel_width',
			[
				'label' => esc_html__( 'Width', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 260,
						'max' => 900,
						'step' => 1,
                    ],
                    '%' => [
						'min' => 0,
						'max' => 100,
					],
                ],
				'selectors' => [
					'{{WRAPPER}} .menu-style-icon .bzotech-menu-inner' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();
        // Custom menu item lv0
        $this->start_controls_section(
            'style_tab_menuitem',
            [
                'label' => esc_html__('Menu item style', 'bw-printxtore'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'label' => esc_html__( 'Typography', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .bzotech-navbar-nav > li > a',
			]
		);

        $this->add_responsive_control(
			'menu_item_spacing',
			[
				'label' => esc_html__( 'Spacing', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .bzotech-navbar-nav > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'menu_item_margin',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .bzotech-navbar-nav > li > a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs(
            'nav_menu_tabs'
		);
			// Normal
			$this->start_controls_tab(
				'nav_menu_normal_tab',
				[
					'label' => esc_html__( 'Normal', 'bw-printxtore' ),
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'item_background',
					'label' => esc_html__( 'Item background', 'bw-printxtore' ),
					'types' => ['classic', 'gradient'],
					'selector' => '{{WRAPPER}} .bzotech-navbar-nav > li > a',
				]
			);

			$this->add_responsive_control(
				'menu_text_color',
				[
					'label' => esc_html__( 'Item text color', 'bw-printxtore' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bzotech-navbar-nav > li > a' => 'color: {{VALUE}}',
						'{{WRAPPER}} .bzotech-navbar-nav > li > a .indicator-icon' => 'color: {{VALUE}}',
					],
				]
			);
	
			$this->end_controls_tab();

			// Hover
			$this->start_controls_tab(
				'nav_menu_hover_tab',
				[
					'label' => esc_html__( 'Hover', 'bw-printxtore' ),
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'item_background_hover',
					'label' => esc_html__( 'Item background', 'bw-printxtore' ),
					'types' => ['classic', 'gradient'],
					'selector' => '{{WRAPPER}} .bzotech-navbar-nav > li > a:hover, {{WRAPPER}} .bzotech-navbar-nav > li > a:focus, {{WRAPPER}} .bzotech-navbar-nav > li > a:active, {{WRAPPER}} .bzotech-navbar-nav > li:hover > a',
				]
			);
	
			$this->add_responsive_control(
				'item_color_hover',
				[
					'label' => esc_html__( 'Item text color', 'bw-printxtore' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bzotech-navbar-nav > li > a:hover' => 'color: {{VALUE}}',
						'{{WRAPPER}} .bzotech-navbar-nav > li > a:focus' => 'color: {{VALUE}}',
						'{{WRAPPER}} .bzotech-navbar-nav > li > a:active' => 'color: {{VALUE}}',
						'{{WRAPPER}} .bzotech-navbar-nav > li:hover > a' => 'color: {{VALUE}}',
						'{{WRAPPER}} .bzotech-navbar-nav > li:hover > a .bzotech-submenu-indicator' => 'color: {{VALUE}}',
						'{{WRAPPER}} .bzotech-navbar-nav > li > a:hover .bzotech-submenu-indicator' => 'color: {{VALUE}}',
						'{{WRAPPER}} .bzotech-navbar-nav > li > a:focus .bzotech-submenu-indicator' => 'color: {{VALUE}}',
						'{{WRAPPER}} .bzotech-navbar-nav > li > a:active .bzotech-submenu-indicator' => 'color: {{VALUE}}',
					],
				]
			);

			$this->end_controls_tab();

			// active
			$this->start_controls_tab(
				'nav_menu_active_tab',
				[
					'label' => esc_html__( 'Active', 'bw-printxtore' ),
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'		=> 'nav_menu_active_bg_color',
					'label' 	=> esc_html__( 'Item background', 'bw-printxtore' ),
					'types'		=> ['classic', 'gradient'],
					'selector'	=> '{{WRAPPER}} .bzotech-navbar-nav > li.current-menu-item > a,{{WRAPPER}} .bzotech-navbar-nav .sub-menu > li.current_page_item > a'
				]
			);
	
			$this->add_responsive_control(
				'nav_menu_active_text_color',
				[
					'label' => esc_html__( 'Item text color (Active)', 'bw-printxtore' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bzotech-navbar-nav > li.current-menu-item > a' => 'color: {{VALUE}}',
						'{{WRAPPER}} .bzotech-navbar-nav > li.current-menu-parent > a' => 'color: {{VALUE}}',
						'{{WRAPPER}} .bzotech-navbar-nav > li.current-menu-ancestor > a' => 'color: {{VALUE}}',
						'{{WRAPPER}} .bzotech-navbar-nav > li.current-menu-ancestor > a .bzotech-submenu-indicator' => 'color: {{VALUE}}',
					],
				]
			);	

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'menu_item0_border_heading',
			[
				'label' => esc_html__( 'Items Border', 'bw-printxtore' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'menu_item0_border',
				'label' => esc_html__( 'Border', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .bzotech-navbar-nav > li > a',
			]
		);

		$this->add_control(
			'menu_item0_border_last_child_heading',
			[
				'label' => esc_html__( 'Border Last Child', 'bw-printxtore' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'menu_item0_border_last_child',
				'label' => esc_html__( 'Border last Child', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .bzotech-navbar-nav > li:last-child > a',
			]
		);

		$this->add_control(
			'menu_item0_border_first_child_heading',
			[
				'label' => esc_html__( 'Border First Child', 'bw-printxtore' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'menu_item0_border_first_child',
				'label' => esc_html__( 'Border First Child', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .bzotech-navbar-nav > li:first-child > a',
			]
		);
		$this->add_control(
			'style_effect_hover',
			[
				'label' => esc_html__( 'Effect hover style', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''  => esc_html__( 'None', 'bw-printxtore' ),
					'effect-line-bottom'  => esc_html__( 'Line bottom', 'bw-printxtore' ),
					'effect-line-top' => esc_html__( 'Line top', 'bw-printxtore' ),
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'style_effect_hover_color',
			[
				'label' => esc_html__( 'Background Line', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .effect-line-bottom .bzotech-navbar-nav>li>a:after,{{WRAPPER}} .effect-line-top .bzotech-navbar-nav>li>a:after' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'style_effect_hover!' => ''
				]
			]
		);
		$this->add_control(
			'style_effect_line_height',
			[
				'label' => esc_html__( 'Height Line', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
						'step' => 1,
					],
                ],
				'selectors' => [
					'{{WRAPPER}} .effect-line-bottom .bzotech-navbar-nav>li>a:after,{{WRAPPER}} .effect-line-top .bzotech-navbar-nav>li>a:after' => 'height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'style_effect_hover!' => ''
				]
			]
		);
        $this->end_controls_section();
        // Custom sub menu item
        $this->start_controls_section(
            'style_tab_submenu_item',
            [
                'label' => esc_html__('Submenu item style', 'bw-printxtore'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
			'style_tab_submenu_item_arrow',
			[
				'label' => esc_html__( 'Submenu Indicator', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'bzotech_line_arrow',
				'options' => [
					'bzotech_line_arrow'  => esc_html__( 'Line Arrow', 'bw-printxtore' ),
					'bzotech_plus_icon' => esc_html__( 'Plus', 'bw-printxtore' ),
					'bzotech_fill_arrow' => esc_html__( 'Fill Arrow', 'bw-printxtore' ),
					'bzotech_none' => esc_html__( 'None', 'bw-printxtore' ),
                ],
			]
		);
		
		$this->add_responsive_control(
			'style_tab_submenu_indicator_color',
			[
				'label' => esc_html__( 'Indicator color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bzotech-navbar-nav .sub-menu a .indicator-icon' => 'color: {{VALUE}}',
				],
				'condition' => [
					'style_tab_submenu_item_arrow!' => 'bzotech_none'
				]
			]
		);
		$this->add_responsive_control(
			'submenu_indicator_spacing',
			[
				'label' => esc_html__( 'Indicator Margin', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bzotech-navbar-nav-default a .indicator-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'style_tab_submenu_item_arrow!' => 'bzotech_none'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'menu_item_typography',
				'label' => esc_html__( 'Typography', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .bzotech-navbar-nav .sub-menu > li > a',
			]
        );

		$this->add_responsive_control(
			'submenu_item_spacing',
			[
				'label' => esc_html__( 'Spacing', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .bzotech-navbar-nav .sub-menu > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs(
			'submenu_active_hover_tabs'
		);
			$this->start_controls_tab(
				'submenu_normal_tab',
				[
					'label'	=> esc_html__('Normal', 'bw-printxtore')
				]
			);

			$this->add_responsive_control(
				'submenu_item_color',
				[
					'label' => esc_html__( 'Item text color', 'bw-printxtore' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bzotech-navbar-nav .sub-menu > li > a' => 'color: {{VALUE}}',
					],
					
				]
			);
	
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'menu_item_background',
					'label' => esc_html__( 'Item background', 'bw-printxtore' ),
					'types' => [ 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} .bzotech-navbar-nav .sub-menu > li > a',
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'submenu_hover_tab',
				[
					'label'	=> esc_html__('Hover', 'bw-printxtore')
				]
			);
	
			$this->add_responsive_control(
				'item_text_color_hover',
				[
					'label' => esc_html__( 'Item text color (hover)', 'bw-printxtore' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bzotech-navbar-nav .sub-menu > li > a:hover' => 'color: {{VALUE}}',
						'{{WRAPPER}} .bzotech-navbar-nav .sub-menu > li > a:focus' => 'color: {{VALUE}}',
						'{{WRAPPER}} .bzotech-navbar-nav .sub-menu > li > a:active' => 'color: {{VALUE}}',
						'{{WRAPPER}} .bzotech-navbar-nav .sub-menu > li:hover > a' => 'color: {{VALUE}}',
					],
				]
			);
	
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'menu_item_background_hover',
					'label' => esc_html__( 'Item background (hover)', 'bw-printxtore' ),
					'types' => [ 'classic', 'gradient' ],
					'selector' => '
					{{WRAPPER}} .bzotech-navbar-nav .sub-menu > li > a:hover,
					{{WRAPPER}} .bzotech-navbar-nav .sub-menu > li > a:focus,
					{{WRAPPER}} .bzotech-navbar-nav .sub-menu > li > a:active,
					{{WRAPPER}} .bzotech-navbar-nav .sub-menu > li:hover > a',
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'submenu_active_tab',
				[
					'label'	=> esc_html__('Active', 'bw-printxtore')
				]
			);

			$this->add_responsive_control(
				'nav_sub_menu_active_text_color',
				[
					'label' => esc_html__( 'Item text color (Active)', 'bw-printxtore' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bzotech-navbar-nav .sub-menu > li.current-menu-item > a,{{WRAPPER}} .bzotech-navbar-nav .sub-menu > li.current_page_item > a' => 'color: {{VALUE}} !important'
					],
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'		=> 'nav_sub_menu_active_bg_color',
					'label' 	=> esc_html__( 'Item background (Active)', 'bw-printxtore' ),
					'types'		=> ['classic', 'gradient'],
					'selector'	=> '{{WRAPPER}} .bzotech-navbar-nav .sub-menu > li.current-menu-item > a,{{WRAPPER}} .bzotech-navbar-nav .sub-menu > li.current_page_item > a',
				]
			);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'menu_item_border_heading',
			[
				'label' => esc_html__( 'Sub Menu Items Border', 'bw-printxtore' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'menu_item_border',
				'label' => esc_html__( 'Border', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .bzotech-navbar-nav .sub-menu > li > a',
			]
		);

		$this->add_control(
			'menu_item_border_last_child_heading',
			[
				'label' => esc_html__( 'Border Last Child', 'bw-printxtore' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'menu_item_border_last_child',
				'label' => esc_html__( 'Border last Child', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .bzotech-navbar-nav .sub-menu > li:last-child > a',
			]
		);

		$this->add_control(
			'menu_item_border_first_child_heading',
			[
				'label' => esc_html__( 'Border First Child', 'bw-printxtore' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'menu_item_border_first_child',
				'label' => esc_html__( 'Border First Child', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .bzotech-navbar-nav .sub-menu > li:first-child > a',
			]
		);
		
        $this->end_controls_section();
		
        $this->start_controls_section(
            'style_tab_submenu_panel',
            [
                'label' => esc_html__('Submenu panel style', 'bw-printxtore'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'panel_submenu_border',
				'label' => esc_html__( 'Panel Menu Border', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .bzotech-navbar-nav .sub-menu',
			]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'submenu_container_background',
                'label' => esc_html__( 'Container background', 'bw-printxtore' ),
                'types' => [ 'classic','gradient' ],
                'selector' => '{{WRAPPER}} .bzotech-navbar-nav .sub-menu',
            ]
        );

        $this->add_responsive_control(
			'submenu_panel_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .bzotech-navbar-nav .sub-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

        $this->add_responsive_control(
			'submenu_panel_padding',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .bzotech-navbar-nav .sub-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

        $this->add_responsive_control(
			'submenu_container_width',
			[
				'label' => esc_html__( 'Container width', 'bw-printxtore' ),
                'type' => Controls_Manager::TEXT,
                'selectors' => [
                    '{{WRAPPER}} .bzotech-navbar-nav .sub-menu' => 'min-width: {{VALUE}};',
                ]
			]
		);
		

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'panel_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .bzotech-navbar-nav .sub-menu',
			]
		);

        $this->end_controls_section();

       	$this->start_controls_section(
			'menu_toggle_style_tab',
			[
				'label' => esc_html__( 'Icon menu Style', 'bw-printxtore' ),
                'tab' => Controls_Manager::TAB_STYLE,
			]
		);
        $this->get_style_type_container_flex('menu_toggle_style_flex','e-toggle-style-flex');
        $this->add_control(
			'heading_menu_toggle_style_icon',
			[
				'label' => esc_html__( 'Style icon', 'bw-printxtore' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
        $this->get_style_type_text('menu_toggle_style_icon','e-toggle-style-icon');
        $this->add_control(
			'heading_menu_toggle_style_title',
			[
				'label' => esc_html__( 'Style title icon', 'bw-printxtore' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
        $this->get_style_type_text('menu_toggle_style_icon_title','e-toggle-style-icon-title');



		$this->end_controls_section();

		$this->start_controls_section(
			'mobile_menu_logo_style_tab',
			[
				'label' => esc_html__( 'Mobile Menu Logo', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'mobile_menu_logo_width',
			[
				'label' => esc_html__( 'Width', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .mobile-logo > img' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'mobile_menu_logo_height',
			[
				'label' => esc_html__( 'Height', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .mobile-logo > img' => 'max-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'mobile_menu_logo_margin',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .mobile-logo' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'mobile_menu_logo_padding',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .mobile-logo' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
		$settings = $this->get_settings_for_display();
		$attr = array(
			'wdata'		=> $this,
			'settings'	=> $settings,
		);
		echo bzotech_get_template_elementor_global('menu/menu',$settings['main_menu_style'],$attr);
		
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