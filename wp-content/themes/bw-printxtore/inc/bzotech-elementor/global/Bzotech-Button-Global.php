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
class Bzotech_Button_Global extends Widget_Base {

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
		return 'bzotech-button-global';
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
		return esc_html__('Button (Global)', 'bw-printxtore');
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
		return ['bzotech-el-button'];
	}
	public function get_widget_css_config( $widget_name ) { 
	    $file_content_css = get_template_directory() . '/assets/global/css/elementor/button.css';
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
		$settings = $this->get_settings();
		$this->add_render_attribute('button-wrap', 'class', 'button-wrap-global');
		$check_icon = $settings['button_icon'];
		if (!empty($check_icon['value'])) {
			$icon_show = 'icon-on';
		} else { $icon_show = 'icon-off';}

		$image_hover = '';
		$button_link = $settings['button_link'];
		if (!empty($settings['icon_image_hover']['url'])) {
			$image_hover = 'yes';
		}

		$this->add_render_attribute('button-inner', 'class', 'button-inner ' . bzotech_implode($settings['css_by_theme_button']) . ' ' . $icon_show . ' elbzotech-bt-global-' . $settings['style'] . '  elementor-animation-' . $settings['button_hover_animation']);
		if ($settings['button_action'] == 'click' || $settings['button_action'] == 'hover') {
			$this->add_render_attribute('button-inner', 'class', 'js-button-trigger-' . $settings['button_action']);
			if ($button_link['url']) {
				$this->add_render_attribute('button-inner', 'href', $button_link['url']);
			} else {
				$this->add_render_attribute('button-inner', 'role', 'button');
			}

			$this->add_render_attribute('button-inner', 'data-trigger', $settings['elementor_trigger']);
		} else {

			if ($button_link['is_external']) {
				$this->add_render_attribute('button-inner', 'target', "_blank");
			}

			if ($button_link['nofollow']) {
				$this->add_render_attribute('button-inner', 'rel', "nofollow");
			}

			if ($button_link['url']) {
				$this->add_render_attribute('button-inner', 'href', $button_link['url']);
			} else {
				$this->add_render_attribute('button-inner', 'role', 'button');
			}

		}

		$this->add_render_attribute('button-inner', 'class', 'btn-flex-e image_hover-' . $image_hover);

		$attr = array(
			'wdata' => $this,
			'settings' => $settings,
		);
		echo bzotech_get_template_elementor_global('button/button', $settings['style'], $attr);
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
			'section_button',
			[
				'label' => esc_html__('Button', 'bw-printxtore'),
			]
		);

		$this->add_control(
			'style',
			[
				'label' => esc_html__('Style', 'bw-printxtore'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__('Style 1', 'bw-printxtore'),
					'style2' => esc_html__('Style 2', 'bw-printxtore'),
					'style3' => esc_html__('Style 3', 'bw-printxtore'),
					'style4' => esc_html__('Style 4', 'bw-printxtore'),
					'custom' => esc_html__('Style Custom', 'bw-printxtore'),

				],
			]
		);

		$this->add_control(
			'button_text',
			[
				'label' => esc_html__('Button name', 'bw-printxtore'),
				'description' => esc_html__('Enter text of button', 'bw-printxtore'),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('Read more', 'bw-printxtore'),
				'placeholder' => esc_html__('Read more', 'bw-printxtore'),
			]
		);

		$this->add_control(
			'button_icon',
			[
				'label' => esc_html__('Icon', 'bw-printxtore'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'icon_image[url]' => '',
				],
			]
		);
		$this->add_control(
			'icon_image',
			[
				'label' => esc_html__('Icon image', 'bw-printxtore'),
				'description' => esc_html__('You can choose the icon image here (Replace for icon)', 'bw-printxtore'),
				'type' => Controls_Manager::MEDIA,
				'condition' => [
					'button_icon[value]' => '',
				],
			]
		);
		$this->add_control(
			'icon_image_hover',
			[
				'label' => esc_html__('Icon image hover', 'bw-printxtore'),
				'description' => esc_html__('You can choose the icon image here (Replace for icon)', 'bw-printxtore'),
				'type' => Controls_Manager::MEDIA,
				'condition' => [
					'icon_image[url]!' => '',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default' => 'full',
				'separator' => 'none',
				'condition' => [
					'icon_image[url]!' => '',
				],
			]
		);
		$this->add_control(
			'button_action',
			[
				'label' => esc_html__('Action', 'bw-printxtore'),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__('Link dafault', 'bw-printxtore'),
					'click' => esc_html__('Trigger to click over a certain element', 'bw-printxtore'),
					'hover' => esc_html__('Trigger to hover over a certain element', 'bw-printxtore'),
				],
			]
		);
		$this->add_control(
			'button_icon_pos',
			[
				'label' => esc_html__('Icon position', 'bw-printxtore'),
				'type' => Controls_Manager::SELECT,
				'default' => 'after-text',
				'options' => [
					'after-text' => esc_html__('After text', 'bw-printxtore'),
					'before-text' => esc_html__('Before text', 'bw-printxtore'),
				],
				'condition' => [
					'button_icon[value]!' => '',
				],
			]
		);
		$this->add_control(
			'button_display',
			[
				'label' => esc_html__('Display', 'bw-printxtore'),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__('Default', 'bw-printxtore'),
					'inline-block' => esc_html__('Inline block', 'bw-printxtore'),
					'block' => esc_html__('block', 'bw-printxtore'),
					'flex' => esc_html__('Flex', 'bw-printxtore'),
				],
				'selectors' => [
					'{{WRAPPER}} a' => 'display: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_link',
			[
				'label' => esc_html__('Link', 'bw-printxtore'),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__('https://your-link.com', 'bw-printxtore'),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => false,
					'nofollow' => true,
				],

				'condition' => [
					'button_action' => ['', 'hover'],
				],
			]
		);
		$this->add_control(
			'elementor_trigger',
			[
				'label' => esc_html__('Enter select to trigger', 'bw-printxtore'),
				'type' => Controls_Manager::TEXTAREA,
				'default' => '',
				'label_block' => true,
				'description' => esc_html__('EX: div.navi > .next', 'bw-printxtore'),
				'condition' => [
					'button_action' => ['click', 'hover'],
				],
			]
		);
		$this->add_responsive_control(
			'button_align',
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
				],
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'section_button_flex',
			[
				'label' => esc_html__('Flex Container Control', 'bw-printxtore'),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'button_display' => ['flex'],
				],
			]
		);
		$this->get_style_type_container_flex_base('btn_flex', 'btn-flex-e');
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label' => esc_html__('Button', 'bw-printxtore'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('button_effects');

		$this->start_controls_tab('button_normal',
			[
				'label' => esc_html__('Normal', 'bw-printxtore'),
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}} .icon-off .text-button,{{WRAPPER}} .icon-on.button-inner',
				'label' => esc_html__('Typography', 'bw-printxtore'),
			]
		);
		$this->add_control(
			'text_color',
			[
				'label' => esc_html__('Color', 'bw-printxtore'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .button-inner' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_background',
				'label' => esc_html__('Background', 'bw-printxtore'),
				'types' => ['classic', 'gradient', 'video'],
				'selector' => '{{WRAPPER}} .button-inner',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_shadow',
				'selector' => '{{WRAPPER}} .button-inner',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_border',
				'selector' => '{{WRAPPER}} .button-inner',
			]
		);
		$this->add_responsive_control(
			'button_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .button-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__('Padding', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .button-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
				],
			]
		);
		$this->add_responsive_control(
			'button_width_css',
			[
				'label' => esc_html__('Width', 'bw-printxtore'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%', 'px', 'vw'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .button-inner,{{WRAPPER}} .button-wrap-global' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'button_height_css',
			[
				'label' => esc_html__('Height', 'bw-printxtore'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%', 'px', 'vw'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .button-inner' => 'height: {{SIZE}}{{UNIT}}!important;',
				],
			]
		);
		$this->add_responsive_control(
			'button_line_height_css',
			[
				'label' => esc_html__('Line Height', 'bw-printxtore'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%', 'px', 'vw'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .button-inner' => 'Line-height: {{SIZE}}{{UNIT}}!important;',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('button_hover',
			[
				'label' => esc_html__('Hover', 'bw-printxtore'),
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography_hover',
				'label' => esc_html__('Typography hover', 'bw-printxtore'),
				'selector' => '{{WRAPPER}} .icon-off:hover .text-button,{{WRAPPER}} .icon-on.button-inner:hover',
			]
		);
		$this->add_control(
			'text_color_hover',
			[
				'label' => esc_html__('Color hover', 'bw-printxtore'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .button-inner:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .button-inner:hover .text-button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_background_hover',
				'label' => esc_html__('Background hover', 'bw-printxtore'),
				'types' => ['classic', 'gradient', 'video'],
				'selector' => '{{WRAPPER}} .button-inner:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_shadow_hover',
				'label' => esc_html__('button shadow hover', 'bw-printxtore'),
				'selector' => '{{WRAPPER}} .button-inner:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_border_hover',
				'label' => esc_html__('Border hover', 'bw-printxtore'),
				'selector' => '{{WRAPPER}} .button-inner:hover',
			]
		);

		$this->add_responsive_control(
			'button_border_radius_hover',
			[
				'label' => esc_html__('Border Radius hover', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .button-inner:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'button_padding_hover',
			[
				'label' => esc_html__('Padding hover', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .icon-off:hover .text-button,{{WRAPPER}} .icon-on.button-inner:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_hover_animation',
			[
				'label' => esc_html__('Animation Hover', 'bw-printxtore'),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'button_hover_transition',
			[
				'label' => esc_html__('Transition Duration', 'bw-printxtore'),
				'type' => Controls_Manager::SLIDER,
				'separator' => 'before',
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .icon-off .text-button,{{WRAPPER}} .icon-on.button-inner' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
		$this->add_control(
			'css_by_theme_button',
			[
				'label' => esc_html__('Add class style', 'bw-printxtore'),
				'type' => Controls_Manager::SELECT2,
				'default' => '',
				'options' => bzotech_list_class_style_by_theme(),
				'multiple' => true,
				'label_block' => true,
				'description' => esc_html__('Add class style by theme', 'bw-printxtore'),
			]
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => esc_html__('Icon', 'bw-printxtore'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('icon_effects');

		$this->start_controls_tab('icon_normal',
			[
				'label' => esc_html__('Normal', 'bw-printxtore'),
			]
		);
		$this->add_responsive_control(
			'size_icon',
			[
				'label' => esc_html__('Size icon', 'bw-printxtore'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .icon-button-el' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__('Color icon', 'bw-printxtore'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .icon-button-el' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'icon_background',
				'label' => esc_html__('Background', 'bw-printxtore'),
				'types' => ['classic', 'gradient', 'video'],
				'selector' => '{{WRAPPER}} .icon-button-el',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'icon_border',
				'label' => esc_html__('Border type', 'bw-printxtore'),
				'selector' => '{{WRAPPER}} .icon-button-el',
			]
		);

		$this->add_responsive_control(
			'icon_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .icon-button-el' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('icon_hover',
			[
				'label' => esc_html__('Hover', 'bw-printxtore'),
			]
		);
		$this->add_responsive_control(
			'size_icon_hover',
			[
				'label' => esc_html__('Size icon hover', 'bw-printxtore'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .icon-button-el:hover' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'icon_color_hover',
			[
				'label' => esc_html__('Color icon hover', 'bw-printxtore'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .icon-button-el:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'icon_background_hover',
				'label' => esc_html__('Background Hover', 'bw-printxtore'),
				'types' => ['classic', 'gradient', 'video'],
				'selector' => '{{WRAPPER}} .icon-button-el:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'icon_border_hover',
				'label' => esc_html__('Border hover', 'bw-printxtore'),
				'selector' => '{{WRAPPER}} .icon-button-el:hover',
			]
		);

		$this->add_responsive_control(
			'icon_border_radius_hover',
			[
				'label' => esc_html__('Border Radius hover', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .icon-button-el:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'padding_icon',
			[
				'label' => esc_html__('Padding icon', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .icon-button-el' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'margin_icon',
			[
				'label' => esc_html__('Margin icon', 'bw-printxtore'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .icon-button-el' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	public function get_style_type_container_flex_base($key = 'container_flex', $class = "container-flex-e") {

		$start = is_rtl() ? 'right' : 'left';
		$end = is_rtl() ? 'left' : 'right';
		$this->add_responsive_control(
			$key . '_flex_direction',
			[
				'label' => esc_html__('Direction', 'bw-printxtore'),
				'type' => Controls_Manager::CHOOSE,
				'responsive' => true,
				'label_block' => true,
				'options' => [
					'row' => [
						'title' => esc_html_x('Row - horizontal', 'Flex Container Control', 'bw-printxtore'),
						'icon' => 'eicon-arrow-' . $end,
					],
					'column' => [
						'title' => esc_html_x('Column - vertical', 'Flex Container Control', 'bw-printxtore'),
						'icon' => 'eicon-arrow-down',
					],
					'row-reverse' => [
						'title' => esc_html_x('Row - reversed', 'Flex Container Control', 'bw-printxtore'),
						'icon' => 'eicon-arrow-' . $start,
					],
					'column-reverse' => [
						'title' => esc_html_x('Column - reversed', 'Flex Container Control', 'bw-printxtore'),
						'icon' => 'eicon-arrow-up',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .' . $class => 'flex-direction: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$this->add_responsive_control(
			$key . '_alignment',
			[
				'label' => esc_html__('Justify Content', 'bw-printxtore'),
				'type' => Controls_Manager::CHOOSE,
				'responsive' => true,
				'label_block' => true,
				'options' => [
					'flex-start' => [
						'title' => esc_html_x('Start', 'Flex Container Control', 'bw-printxtore'),
						'icon' => 'eicon-flex eicon-justify-start-h',
					],
					'center' => [
						'title' => esc_html_x('Center', 'Flex Container Control', 'bw-printxtore'),
						'icon' => 'eicon-flex eicon-justify-center-h',
					],
					'flex-end' => [
						'title' => esc_html_x('End', 'Flex Container Control', 'bw-printxtore'),
						'icon' => 'eicon-flex eicon-justify-end-h',
					],
					'space-between' => [
						'title' => esc_html_x('Space Between', 'Flex Container Control', 'bw-printxtore'),
						'icon' => 'eicon-flex eicon-justify-space-between-h',
					],
					'space-around' => [
						'title' => esc_html_x('Space Around', 'Flex Container Control', 'bw-printxtore'),
						'icon' => 'eicon-flex eicon-justify-space-around-h',
					],
					'space-evenly' => [
						'title' => esc_html_x('Space Evenly', 'Flex Container Control', 'bw-printxtore'),
						'icon' => 'eicon-flex eicon-justify-space-evenly-h',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .' . $class => 'justify-content: {{VALUE}};',
				],
				'default' => '',
			]
		);

		$this->add_responsive_control(
			$key . 'align_items',
			[
				'label' => esc_html__('Align Items', 'bw-printxtore'),
				'type' => Controls_Manager::CHOOSE,
				'responsive' => true,
				'options' => [
					'flex-start' => [
						'title' => esc_html_x('Start', 'Flex Container Control', 'bw-printxtore'),
						'icon' => 'eicon-flex eicon-align-start-v',
					],
					'center' => [
						'title' => esc_html_x('Center', 'Flex Container Control', 'bw-printxtore'),
						'icon' => 'eicon-flex eicon-align-center-v',
					],
					'flex-end' => [
						'title' => esc_html_x('End', 'Flex Container Control', 'bw-printxtore'),
						'icon' => 'eicon-flex eicon-align-end-v',
					],
					'stretch' => [
						'title' => esc_html_x('Stretch', 'Flex Container Control', 'bw-printxtore'),
						'icon' => 'eicon-flex eicon-align-stretch-v',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .' . $class => 'align-items: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$this->add_responsive_control(
			$key . 'gap_item',
			[
				'label' => esc_html__('Gap', 'bw-printxtore'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .' . $class => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			$key . 'flex_wrap',
			[
				'label' => esc_html__('Wrap', 'bw-printxtore'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'nowrap' => [
						'title' => esc_html__('No Wrap', 'bw-printxtore'),
						'icon' => 'eicon-flex eicon-nowrap',
					],
					'wrap' => [
						'title' => esc_html__('Wrap', 'bw-printxtore'),
						'icon' => 'eicon-flex eicon-wrap',
					],
				],
				'description' => esc_html__(
					'Items within the container can stay in a single line (No wrap), or break into multiple lines (Wrap).', 'bw-printxtore'
				),
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .' . $class => 'flex-wrap: {{VALUE}};',
				],
				'responsive' => true,
			]
		);

	}
}