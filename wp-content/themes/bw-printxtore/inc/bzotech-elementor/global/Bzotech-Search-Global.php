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
class Bzotech_Search_Global extends Widget_Base {

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
		return 'bzotech-search_global';
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
		return esc_html__( 'Search form (Global)', 'bw-printxtore' );
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
		return 'eicon-search';
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
		return [ 'bzotech-el-search' ];
	}
	public function get_widget_css_config( $widget_name ) { 
	    $file_content_css = get_template_directory() . '/assets/global/css/elementor/search.css';
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
				'default'   => 'style1',
				'options'   => [
					'style1'		=> esc_html__( 'Style 1 (Default)', 'bw-printxtore' ),
					'style2'  		=> esc_html__( 'Style 2', 'bw-printxtore' ),					
					'style3'  		=> esc_html__( 'Style 3 (Line home 7)', 'bw-printxtore' ),					
					'icon'  		=> esc_html__( 'Style icon popup', 'bw-printxtore' ),
					'icon-fixed-left'	=> esc_html__( 'Style icon fixed left', 'bw-printxtore' ),		
					'icon-fixed-right'	=> esc_html__( 'Style icon fixed right', 'bw-printxtore' ),
				],
			]
		);
		$this->add_control(
			'search_in',
			[
				'label'		=> esc_html__( 'Search in', 'bw-printxtore' ),
				'type' 		=> Controls_Manager::SELECT,
				'default' 	=> 'product',
				'options'   => [
					'product'  => esc_html__( 'Product', 'bw-printxtore' ),
					'post'     => esc_html__( 'Post', 'bw-printxtore' ),
					'all'      => esc_html__( 'All', 'bw-printxtore' ),
				],
			]
		);
		$this->add_control(
			'live_search',
			[
				'label' => esc_html__( 'Live search', 'bw-printxtore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'bw-printxtore' ),
				'label_off' => esc_html__( 'Off', 'bw-printxtore' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'display',
			[
				'label' 	=> esc_html__( 'Product display layout', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'list',
				'options'   => [
					'list'		=> esc_html__( 'List', 'bw-printxtore' ),
					'grid'		=> esc_html__( 'Grid', 'bw-printxtore' ),		
				],
				'condition' => [
					'live_search' => ['yes'],
					'search_in' => ['product'],

				]
			]
		);

		$this->add_control(
			'item_style',
			[
				'label' 	=> esc_html__( 'Item Grid Style', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => bzotech_get_product_style(),

				'condition' => [
					'display' => ['grid'],
					'live_search' => ['yes'],
					'search_in' => ['product']
				]
			]
		);
		$this->add_control(
			'title_live_default',
			[
				'label' => esc_html__( 'Title live default', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Popular Products' , 'bw-printxtore' ),
				'label_block' => true,
				'condition' => [
					'live_search' => ['yes']
				]
			]
		);
		$this->add_control(
			'title_trending',
			[
				'label' => esc_html__( 'Title wrapper trending', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Trending Now' , 'bw-printxtore' ),
				'label_block' => true,
				'condition' => [
					'live_search' => ['yes']
				]
			]
		);
		$this->add_control(
			'key_trending',
			[
				'label' => esc_html__( 'List of trending keywords', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Women | Men | New' , 'bw-printxtore' ),
				'label_block' => true,
				'placeholder' => esc_html__( 'List of keywords separated by the character "|" for example "Women | Men | New', 'bw-printxtore' ),
				'condition' => [
					'live_search' => ['yes']
				]
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_popup',
			[
				'label' => esc_html__( 'Popup button icon', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'style' => ['icon','icon-fixed-left','icon-fixed-right']
				]
			]
		);

		$this->add_control(
			'icon_popup',
			[
				'label' => esc_html__( 'Icon', 'bw-printxtore' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'las la-search',
					'library' => 'solid',
				],
			]
		);
		$this->add_control(
			'search_bttext_popup',
			[
				'label' => esc_html__( 'Add text', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
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
			'title_form_icon',
			[
				'label' => esc_html__( 'Title form', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Search Our Site' , 'bw-printxtore' ),
				'label_block' => true,
				'condition' => [
					'style' => ['icon','icon-fixed-left','icon-fixed-right']
				]
			]
		);
		$this->add_control(
			'placeholder',
			[
				'label' => esc_html__( 'Placeholder', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Enter key to search', 'bw-printxtore' ),
				'placeholder' => esc_html__( 'Type your placeholder here', 'bw-printxtore' ),
			]
		);
		$this->add_control(
			'icon',
			[
				'label' => esc_html__( 'Search button icon', 'bw-printxtore' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'las la-search',
					'library' => 'solid',
				],
			]
		);

		$this->add_control(
			'search_bttext',
			[
				'label' => esc_html__( 'Search button title', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Type your text to add search button', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			'search_bttext_pos',
			[
				'label' => esc_html__( 'Text position', 'bw-printxtore' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'after-icon',
				'options' => [
					'after-icon'   => esc_html__( 'After icon', 'bw-printxtore' ),
					'before-icon'  => esc_html__( 'Before icon', 'bw-printxtore' ),
				],
				'condition' => [
					'search_bttext!' => '',
					'icon[value]!' => '',
				]
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
			'show_cat',
			[
				'label' => esc_html__( 'Show choose category', 'bw-printxtore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'bw-printxtore' ),
				'label_off' => esc_html__( 'Hide', 'bw-printxtore' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'search_in!' => 'all'
				]
			]
		);
		$this->add_control(
			'cats', 
			[
				'label' => esc_html__( 'Categories', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter slug categories. The values separated by ",". Example cat-1,cat-2. Default will show all categories', 'bw-printxtore' ),
				'condition' => [
					'show_cat' => 'yes'
				]
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
				'condition' => [
					'search_in' => ['product']
				]
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
				'condition' => [
					'search_in' => ['product']
				]
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
				'condition' => [
					'search_in' => ['product']
				]
			],
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'section_grid',
			[
				'label' => esc_html__( 'Grid Setting', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'display' => ['grid'],
					'live_search' => ['yes'],
					'search_in' => ['product']
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
		$this->end_controls_section();
		

		$this->start_controls_section(
			'section_style_cat',
			[
				'label' => esc_html__( 'Categories dropdown', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_cat' => 'yes'
				]
			]
		);

		$this->add_control(
			'title_cat',
			[
				'label' => esc_html__( 'Title', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'All categories', 'bw-printxtore' ),
				'placeholder' => esc_html__( 'Type your title here', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			'title_cat_color',
			[
				'label' => esc_html__( 'Title Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elbzotech-dropdown-box .current-search-cat' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'width_cat',
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
					'{{WRAPPER}} .elbzotech-dropdown-box' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'padding_cat',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-dropdown-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'margin_cat',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-dropdown-box' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_cat',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .elbzotech-dropdown-box',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_cat',
				'selector' => '{{WRAPPER}} .elbzotech-dropdown-box',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'border_cat_radius',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-dropdown-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_icon_popup',
			[
				'label' => esc_html__( 'Style icon popup', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style' => ['icon','icon-fixed-left','icon-fixed-right']
				]
			]
		);

		$this->add_responsive_control(
			'width_icon_popup',
			[
				'label' => esc_html__( 'Width', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .search-icon-popup' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'height_icon_popup',
			[
				'label' => esc_html__( 'Height', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .search-icon-popup' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'line-height_icon_popup',
			[
				'label' => esc_html__( 'Line Height', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .search-icon-popup' => 'line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'size_icon_popup',
			[
				'label' => esc_html__( 'Size icon', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .search-icon-popup i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$start = is_rtl() ? 'right' : 'left';
		$end = is_rtl() ? 'left' : 'right';
		$this->add_responsive_control(
			'flex_direction',
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
					'{{WRAPPER}} .search-icon-popup' => 'flex-direction: {{VALUE}};',
				],
				'default' => '',
			]
		);
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
					'{{WRAPPER}} .search-icon-popup' => 'justify-content: {{VALUE}};',
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
					'{{WRAPPER}} .search-icon-popup' => 'align-items: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$this->add_responsive_control(
			'align_icon_popup',
			[
				'label' => esc_html__( 'Alignment', 'bw-printxtore' ),
				'type' => Controls_Manager::CHOOSE,
				'default'	=> '',
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
					'{{WRAPPER}} .search-icon-popup' => 'text-align: {{VALUE}}',
				],
			]
		);
		$this->start_controls_tabs( 'icon_popup_effects' );

		$this->start_controls_tab( 'icon_popup_normal',
			[
				'label' => esc_html__( 'Normal', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			'color_icon_popup',
			[
				'label' => esc_html__( 'Icon Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .search-icon-popup i' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'color_text_popup',
			[
				'label' => esc_html__( 'Text Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .search-icon-popup .bttext_popup' => 'color: {{VALUE}}',
				],
			]

		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_popup_typography_hover_css',
				'label' => esc_html__( 'Typography Text', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .search-icon-popup .bttext_popup',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_icon_popup',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .search-icon-popup',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'shadow_icon_popup',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .search-icon-popup',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_icon_popup',
				'selector' => '{{WRAPPER}} .search-icon-popup',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'border_icon_popup',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .search-icon-popup' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'icon_popup_hover',
			[
				'label' => esc_html__( 'Hover', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			'color_icon_popup_hover',
			[
				'label' => esc_html__( 'Icon Color Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .search-icon-popup:hover i' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'color_text_popup_hover',
			[
				'label' => esc_html__( 'Text Color Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .search-icon-popup:hover .bttext_popup' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_icon_popup_hover',
				'label' => esc_html__( 'Background Hover', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .search-icon-popup:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'shadow_icon_popup_hover',
				'exclude' => [
					'box_shadow_position',
				],
				'label' => esc_html__( 'Box Shadow Hover', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .search-icon-popup:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_icon_popup_hover',
				'label' => esc_html__( 'Border Hover', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .search-icon-popup:hover',
			]
		);

		$this->add_responsive_control(
			'border_icon_popup_hover',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'label' => esc_html__( 'Border Radius Hover', 'bw-printxtore' ),
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .search-icon-popup:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();	

		$this->add_control(
			'separator_icon_popup',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->add_responsive_control(
			'padding_icon_popup',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .search-icon-popup' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'margin_icon_popup',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .search-icon-popup' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_style_box_live',
			[
				'label' => esc_html__( 'Style box live', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'live_search' => ['yes']
				]
			]
		);
		$this->get_style_box_live();
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
		$column_widescreen = $column_laptop =$column_tablet_extra =$column_tablet =$column_mobile_extra =$column_mobile ='';
		extract($settings);
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
		$this->add_render_attribute( 'elbzotech-item', 'class', 'item-product');
		
		$this->add_render_attribute( 'elbzotech-item-grid', 'class', 'list-col-item item-grid-product-'.$item_style.' list-'.esc_attr($column).'-item list-'.esc_attr($column_widescreen).'-item-widescreen list-'.esc_attr($column_laptop).'-item-laptop  list-'.esc_attr($column_tablet_extra).'-item-tablet-extra list-'.esc_attr($column_tablet).'-item-tablet list-'.esc_attr($column_mobile_extra).'-item-mobile-extra list-'.esc_attr($column_mobile).'-item-mobile');
		$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-grid',$column_custom);
		$this->add_render_attribute( 'elbzotech-wrapper', 'class', 'elbzotech-products-wrap js-content-wrap');
		$this->add_render_attribute( 'elbzotech-inner', 'class', 'js-content-main list-product-wrap bzotech-row ');
		$this->add_render_attribute( 'elbzotech-wrapper', 'data-column', $column );
		$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-widescreen', $column_widescreen );
		$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-laptop', $column_laptop );
		$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-tablet-extra', $column_tablet_extra );
		$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-tablet', $column_tablet );
		$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-mobile-extra', $column_mobile_extra );
		$this->add_render_attribute( 'elbzotech-wrapper', 'data-column-mobile', $column_mobile );
		$this->add_render_attribute( 'elbzotech-wrapper', 'class', 'shop-grid-product-item-'.$item_style );
		
		$item_wrap = $this->get_render_attribute_string( 'elbzotech-item-grid' );
        $item_inner = $this->get_render_attribute_string( 'elbzotech-item' );
        $inner = $this->get_render_attribute_string( 'elbzotech-inner' );
        $wrapper = $this->get_render_attribute_string( 'elbzotech-wrapper' );
		$attr = array(
            'item_wrap'		=> $item_wrap,
            'item_inner'	=> $item_inner,
            'wrapper'	=> $wrapper,
            'wdata'			=> $this,
			'settings'			=> $settings,
			'inner'			=> $inner,
        );
		echo bzotech_get_template_elementor_global('search/search',$settings['style'],$attr,true);
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
	
	public function get_style_box_live() {
		$this->add_responsive_control(
			'width_box_live',
			[
				'label' => esc_html__( 'Width', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%','custom' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .box-live-e' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'height_box_live',
			[
				'label' => esc_html__( 'Height', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%','custom' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .box-live-e' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'left_box_live',
			[
				'label' => esc_html__( 'Left position', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%','custom' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .box-live-e' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'padding_cat_live',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .box-live-e' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
	}
	
}
