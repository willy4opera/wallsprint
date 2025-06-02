<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Hello World
 *
 * Elementor widget for hello world.
 *
 * @since 1.0.0
 */
class Hello_World extends Widget_Base {

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
		return 'nth-slider';
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
		return __( 'NTH Slider', 'nth-domain' );
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
		return [ 'theme-elements' ];
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
		return [ 'bzotech-elementor' ];
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
	protected function _register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'bzotech-elementor' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'bzotech-elementor' ),
				'type' => Controls_Manager::TEXT,
				'frontend_available' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'item',
			[
				'label' => __( 'Item', 'bzotech-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 6,
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Style', 'bzotech-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'list',
			[
				'label' => __( 'Repeater List', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => [
					[
						'name' => 'list_title',
						'label' => __( 'Title', 'plugin-domain' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => __( 'List Title' , 'plugin-domain' ),
						'label_block' => true,
					],
					[
						'name' => 'list_content',
						'label' => __( 'Content', 'plugin-domain' ),
						'type' => \Elementor\Controls_Manager::WYSIWYG,
						'default' => __( 'List Content' , 'plugin-domain' ),
						'show_label' => false,
					],
					[
						'name' => 'list_color',
						'label' => __( 'Color', 'plugin-domain' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}}'
						],
					],
				],
				'default' => [
					[
						'list_title' => __( 'Title #1', 'plugin-domain' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'plugin-domain' ),
					],
					[
						'list_title' => __( 'Title #2', 'plugin-domain' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'plugin-domain' ),
					],
				],
				'title_field' => '{{{ list_title }}}',
			]
		);
		$this->add_control(
			'text_color',
			[
				'label' => __( 'Text Color', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .title' => 'color: {{VALUE}};',
				],
			]
		);
		// $this->add_control(
		// 	'title_color',
		// 	[
		// 		'label' => __( 'Text Color', 'elementor' ),
		// 		'type' => \Elementor\Controls_Manager::COLOR,
		// 		'scheme' => [
		// 			'type' => \Elementor\Scheme_Color::get_type(),
		// 			'value' => \Elementor\Scheme_Color::COLOR_1,
		// 		],
		// 		'selectors' => [
		// 			// Stronger selector to avoid section style from overwriting
		// 			'{{WRAPPER}} .title' => 'color: {{VALUE}};',
		// 		],
		// 	]
		// );
		$this->add_responsive_control(
			'space',
			[
				'label' => __( 'Space', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 50,
				],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 600,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'content_align',
			[
				'label' => __( 'Alignment', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'plugin-name' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'plugin-name' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'plugin-name' ),
						'icon' => 'fa fa-align-right',
					],
					'none' => [
						'title' => __( 'None', 'plugin-name' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'devices' => [ 'desktop', 'tablet' ],
				'prefix_class' => 'content-align-%s',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'text_color2',
			[
				'label' => __( 'Text Color', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'condition' => [
					'content_align' => ['center','right'],
					'space' => [
						'unit' => 'px',
						'size' => 60,
					],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .title',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} .title',
			]
		);
		$this->add_responsive_control(
			'title_padding',
			[
				'label' => __( 'Padding', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'text_transform',
			[
				'label' => __( 'Text Transform', 'bzotech-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'None', 'bzotech-elementor' ),
					'uppercase' => __( 'UPPERCASE', 'bzotech-elementor' ),
					'lowercase' => __( 'lowercase', 'bzotech-elementor' ),
					'capitalize' => __( 'Capitalize', 'bzotech-elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}} .title' => 'text-transform: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			[
				'label' => __( 'Style Section', 'plugin-name' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'popover-toggle',
			[
				'label' => __( 'Box', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'your-plugin' ),
				'label_on' => __( 'Custom', 'your-plugin' ),
				'return_value' => 'yes',
			]
		);

		$this->start_popover();


		$this->add_control(
			'po_title1',
			[
				'label' => __( 'po title1', 'bzotech-elementor' ),
				'type' => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'po_title2',
			[
				'label' => __( 'po title2', 'bzotech-elementor' ),
				'type' => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'po_title3',
			[
				'label' => __( 'po title3', 'bzotech-elementor' ),
				'type' => Controls_Manager::TEXT,
			]
		);
		$this->end_popover();
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
		//$content = $this->parse_text_editor( $settings['title'] );
		// $settings = $this->get_settings_for_display();
		//echo $content;
		$this->add_inline_editing_attributes( 'title' );

		$this->add_render_attribute( 'title', 'class', 'title' );
		echo '<div '.$this->get_render_attribute_string( 'title' ).'>';
		echo $settings['title'];
		echo '</div>';
		?>
		<div class="images-slider">		   
		    <div class="wrap-item smart-slider" 
		        data-item="<?php echo $settings['item']['size']?>" data-speed="" 
		        data-itemres="" 
		        data-prev="" data-next="" 
		        data-pagination="" data-navigation="true">
		    	<div>Slider 1</div>
		    	<div>Slider 2</div>
		    	<div>Slider 3</div>
		    	<div>Slider 4</div>

		    </div>
		</div>
		<?php
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
	protected function _content_template() {
		?>
		<#
		console.log(settings);
		#>
		<div class="elementor-inline-editing" data-elementor-setting-key="title" data-elementor-inline-editing-toolbar="basic">
			{{{ settings.title }}}
		</div>
		<div class="images-slider">		   
		    <div class="wrap-item smart-slider" 
		        data-item="{{{ settings.item.size }}}" data-speed="" 
		        data-itemres="" 
		        data-prev="" data-next="" 
		        data-pagination="" data-navigation="true">
		    	<div>Slider 1</div>
		    	<div>Slider 2</div>
		    	<div>Slider 3</div>
		    	<div>Slider 4</div>

		    </div>
		</div>
		<?php
	}
}