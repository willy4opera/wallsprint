<?php
namespace Elementor;
use WC;
if ( ! defined( 'ABSPATH' ) ) exit; 

/**
 * Elementor Mini Cart
 *
 * Elementor widget for Mini Cart
 *
 * @since 1.0.0
 */
class Bzotech_MiniCart_Global extends Widget_Base {

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
		return 'bzotech-mini-cart-global';
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
		return esc_html__( 'Mini Cart (Global)', 'bw-printxtore' );
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
		return 'eicon-cart';
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
		return [ 'bzotech-el-mini-cart' ];
	}
	public function get_widget_css_config( $widget_name ) { 
	    $file_content_css = get_template_directory() . '/assets/global/css/elementor/mini-cart.css';
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
	public function mini_cart($image_size='') {
		if(empty($image_size) || $image_size == 'custom') $image_size =array(100,100);
		if(!\WC()->cart) return;
		if ( ! \WC()->cart->is_empty() ) : do_action( 'woocommerce_before_mini_cart' ); ?>
		    <div class="product-mini-cart list-mini-cart-item bzotech-scrollbar">
		        <?php
		            do_action( 'woocommerce_before_mini_cart_contents' );
		            $count_item = 0;
		            foreach ( \WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		                
		                $_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
		                $product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
		                $product_price     = apply_filters( 'woocommerce_cart_item_price', \WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
		                
		                if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
		                    $product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
		                    $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
		                    echo '<input type="hidden" name="product-remove-ajax-nonce" class="product-remove-ajax-nonce" value="' . wp_create_nonce( 'product-remove-ajax-nonce' ) . '" />';
		                    ?>
		                    <div class="item-info-cart product-mini-cart table-custom <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>" data-key="<?php echo esc_attr($cart_item_key)?>">
		                        <div class="product-thumb">
		                            <a href="<?php echo esc_url($product_permalink)?>" class="product-thumb-link">
		                                <?php echo apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image($image_size), $cart_item, $cart_item_key )?>
		                            </a>
		                        </div>
		                        <div class="product-info">
		                            <h3 class="title14 product-title"><a href="<?php echo esc_url($product_permalink)?>"><?php echo apply_filters( 'bzotech-product-title-item-minicart',$product_name); ?></a></h3>
		                            <div class="mini-cart-qty">
		                                <span><?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="qty-num">' . sprintf( '%s', $cart_item['quantity'] ) . '</span>', $cart_item, $cart_item_key ); ?> x <span class=""><?php echo apply_filters( 'woocommerce_cart_item_price', \WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );?></span></span>
		                            </div>
		                        </div>
		                        <div class="product-delete text-right">
		                            <a href="#" class="remove-product title18"><i class="la la-trash"></i></a>
		                        </div>
		                    </div>
		                    <?php
		                    $count_item = $count_item + $cart_item['quantity'] ;
		                }

		            }

		            do_action( 'woocommerce_mini_cart_contents' );
		        ?>
		    </div>

		    <input class="get-cart-number" type="hidden" value="<?php echo esc_attr($count_item)?>">

		    <div class="mini-cart-footer">
			    <div class="mini-cart-total clearfix">
			        <span class="pull-left"><?php esc_html_e( 'Total', 'bw-printxtore' ); ?></span>
			        <strong class="pull-right  mini-cart-total-price get-cart-price"><?php echo \WC()->cart->get_cart_subtotal(); ?></strong>
			    </div>

			    <?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

			    <div class="mini-cart-button">
			        <?php do_action( 'woocommerce_widget_shopping_cart_buttons' ); ?>
			    </div>
			</div>

		<?php do_action( 'woocommerce_after_mini_cart' ); else : ?>

		    <div class="mini-cart-empty"><?php esc_html_e( 'No products in the cart.', 'bw-printxtore' ); ?></div>

		<?php endif;
		
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
					'style1'		=> esc_html__( 'Style default', 'bw-printxtore' ),
					'style2'		=> esc_html__( 'Style 2', 'bw-printxtore' ),
				],
			]
		);

		$this->add_control(
			'style_content',
			[
				'label' 	=> esc_html__( 'Content Style', 'bw-printxtore' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'dropdown',
				'options'   => [
					'dropdown'		=> esc_html__( 'Dropdown', 'bw-printxtore' ),
					'side-right'		=> esc_html__( 'Side right', 'bw-printxtore' ),
					'side-left'		=> esc_html__( 'Side left', 'bw-printxtore' ),
				],
			]
		);
		$this->add_control(
			'icon',
			[
				'label' => esc_html__( 'Icon', 'bw-printxtore' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-shopping-cart',
					'library' => 'solid',
				],
			]
		);
		$this->add_control(
			'text', 
			[
				'label' => esc_html__( 'Label', 'bw-printxtore' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'label_block' => true,
			]
		);
		$this->add_control(
			'show_price',
			[
				'label' => esc_html__( 'Show price', 'bw-printxtore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'bw-printxtore' ),
				'label_off' => esc_html__( 'Hide', 'bw-printxtore' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
	
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => esc_html__( 'Style box cart', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'width_mini_cart',
			[
				'label' => esc_html__( 'Width', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .mini-cart-link' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'height_mini_cart',
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
					'{{WRAPPER}} .mini-cart-link' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$start = is_rtl() ? 'right' : 'left';
		$end = is_rtl() ? 'left' : 'right';
		$this->add_responsive_control(
			'flex_direction_mini_cart',
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
					'{{WRAPPER}} .mini-cart-link' => 'flex-direction: {{VALUE}};',
				],
				'default' => '',
			]
		);

		$this->add_responsive_control(
			'alignment_mini_cart',
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
					'{{WRAPPER}} .mini-cart-link' => 'justify-content: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$this->add_responsive_control(
			'align_items_mini_cart',
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
					'{{WRAPPER}} .mini-cart-link' => 'align-items: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$this->add_responsive_control(
			'gap_item_mini_cart',
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
					'{{WRAPPER}} .mini-cart-link' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'flex_wrap_mini_cart',
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
					'{{WRAPPER}} .mini-cart-link' => 'flex-wrap: {{VALUE}};',
				],
				'responsive' => true,
			]
		);

		$this->add_responsive_control(
			'padding_button',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .mini-cart-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'margin_button',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .mini-cart-link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'section_style_label_price',
			[
				'label' => esc_html__( 'Style box label and price cart', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_price_typography',
				'label' => esc_html__( 'Typography button text', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .mini-cart-text',
			]
		);
		$this->add_responsive_control(
			'direction_mini_cart_text',
			[
				'label' => esc_html__( 'Direction Text', 'bw-printxtore' ),
				'type' => Controls_Manager::CHOOSE,
				'responsive' => true,
				'options' => [
					'column' => [
						'title' => esc_html_x( 'Column', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex  eicon-align-stretch-h',
					],
					'row' => [
						'title' => esc_html_x( 'Row', 'Flex Container Control', 'bw-printxtore' ),
						'icon' => 'eicon-flex  eicon-align-stretch-v',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .mini-cart-text' => 'flex-direction: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$this->add_responsive_control(
			'alignment_mini_cart_text',
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
					'{{WRAPPER}} .mini-cart-text' => 'justify-content: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$this->add_responsive_control(
			'align_items_mini_cart_text',
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
					'{{WRAPPER}} .mini-cart-text' => 'align-items: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$this->add_responsive_control(
			'padding_mini_cart_text',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .mini-cart-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'margin_mini_cart_text',
			[
				'label' => esc_html__( 'Margin', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .mini-cart-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_elementor_cart',
			[
				'label' => esc_html__( 'Style elementor cart', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'button_cart_effects' );

		$this->start_controls_tab( 'button_cart_normal',
			[
				'label' => esc_html__( 'Normal', 'bw-printxtore' ),
			]
		);
		$this->add_responsive_control(
			'size_icon',
			[
				'label' => esc_html__( 'Size icon', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .mini-cart-link i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'color_icon',
			[
				'label' => esc_html__( 'Icon Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mini-cart-link i' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'color_label',
			[
				'label' => esc_html__( 'Label Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mini-cart-link .mini-cart-text-bt' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'color_cart_number',
			[
				'label' => esc_html__( 'Cart number color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mini-cart-link .mini-cart-number-e' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'color_price',
			[
				'label' => esc_html__( 'Price Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mini-cart-link .mini-cart-total-price .woocommerce-Price-amount' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cart_text_button_typography',
				'label' => esc_html__( 'Typography Label', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .mini-cart-link .mini-cart-text-bt',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cart_price_button_typography',
				'label' => esc_html__( 'Typography Price', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .mini-cart-link .mini-cart-total-price .woocommerce-Price-amount',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_bt',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic' ],
				'selector' => '{{WRAPPER}} .mini-cart-link',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'shadow_bt',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .mini-cart-link',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_bt',
				'label' => esc_html__( 'Border', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .mini-cart-link',
			]
		);

		$this->add_responsive_control(
			'border_bt',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .mini-cart-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'button_cart_hover',
			[
				'label' => esc_html__( 'Hover', 'bw-printxtore' ),
			]
		);

		$this->add_control(
			'color_icon_hover',
			[
				'label' => esc_html__( 'Icon Color Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mini-cart-global:hover .mini-cart-icon i' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'color_label_hover',
			[
				'label' => esc_html__( 'Label Color Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mini-cart-global:hover .mini-cart-link .mini-cart-text-bt' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'color_price_hover',
			[
				'label' => esc_html__( 'Price Color Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mini-cart-global:hover .mini-cart-link .mini-cart-total-price .woocommerce-Price-amount' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'color_cart_number_hover',
			[
				'label' => esc_html__( 'Cart number color hover', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mini-cart-global:hover .mini-cart-link .mini-cart-number-e' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cart_text_button_typography_hover',
				'label' => esc_html__( 'Typography Label Hover', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .elbzotech-mini-cart-global:hover .mini-cart-text-bt',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cart_price_button_typography_hover',
				'label' => esc_html__( 'Typography Price Hover', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .elbzotech-mini-cart-global:hover  .mini-cart-total-price .woocommerce-Price-amount',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_bt_hover',
				'label' => esc_html__( 'Background Hover', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .elbzotech-mini-cart-global:hover .mini-cart-link',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'shadow_bt_hover',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .elbzotech-mini-cart-global:hover .mini-cart-link',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_bt_hover',
				'label' => esc_html__( 'Border Hover', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .elbzotech-mini-cart-global:hover .mini-cart-link',
			]
		);

		$this->add_responsive_control(
			'border_bt_hover',
			[
				'label' => esc_html__( 'Border Radius Hover', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mini-cart-global:hover .mini-cart-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();	

		
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_number',
			[
				'label' => esc_html__( 'Number style', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'width_number',
			[
				'label' => esc_html__( 'Width', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mini-cart-global .mini-cart-number' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'height_number',
			[
				'label' => esc_html__( 'Height', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mini-cart-global .mini-cart-number' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'color_number',
			[
				'label' => esc_html__( 'Color number', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mini-cart-global .mini-cart-number' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'product_number_typography',
				'label' => esc_html__( 'Typography', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .elbzotech-mini-cart-global .mini-cart-number',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_number',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .elbzotech-mini-cart-global .mini-cart-number',
			]
		);

		$this->add_responsive_control(
			'pos_left_number',
			[
				'label' => esc_html__( 'Position right', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' , '%'],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mini-cart-global .mini-cart-number' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'pos_top_number',
			[
				'label' => esc_html__( 'Position top', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' , '%'],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mini-cart-global .mini-cart-number' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'alignment_number',
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
					'{{WRAPPER}} .mini-cart-number' => 'justify-content: {{VALUE}};',
				],
				'default' => '',
			]
		);
		$this->add_responsive_control(
			'align_items_number',
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
					'{{WRAPPER}} .mini-cart-number' => 'align-items: {{VALUE}};',
				],
				'default' => '',
			]
		);

		
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'shadow_number',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .elbzotech-mini-cart-global .mini-cart-number',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_number_product',
				'selector' => '{{WRAPPER}} .elbzotech-mini-cart-global .mini-cart-number',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'border_radius_number_product',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mini-cart-global .mini-cart-number' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'padding_number_product',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mini-cart-global .mini-cart-number' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_price',
			[
				'label' => esc_html__( 'Price', 'bw-printxtore' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_price' => 'yes',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cart_price_typography',
				'label' => esc_html__( 'Typography', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .elbzotech-mini-cart-global .mini-cart-total-price .woocommerce-Price-amount amount',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_dropdown_style',
			[
				'label' => esc_html__( 'Dropdown/side', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'label' => esc_html__( 'Custom image size product', 'bw-printxtore' ),
				'name' => 'image_size_product', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default' => 'full',
				'separator' => 'none',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_dropdown_typography',
				'label' => esc_html__( 'Title Typography', 'bw-printxtore' ),
				'selector' => '{{WRAPPER}} .mini-cart-content > h2',
			]
		);

		$this->add_responsive_control(
			'dropdown_pos',
			[
				'label' => esc_html__( 'Dropdown position', 'bw-printxtore' ),
				'type' => Controls_Manager::CHOOSE,
				'default'	=> '',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'bw-printxtore' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Inherit', 'bw-printxtore' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'bw-printxtore' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				
				'condition' => [
					'style_content' => 'dropdown',
				]
			]
		);

		$this->add_responsive_control(
			'padding_content',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mini-cart-global .mini-cart-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_content',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .elbzotech-mini-cart-global .mini-cart-content',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'shadow_content',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .elbzotech-mini-cart-global .mini-cart-content',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_content',
				'selector' => '{{WRAPPER}} .elbzotech-mini-cart-global .mini-cart-content',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'border_radius_content',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mini-cart-global .mini-cart-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'separator_bg_overlay',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->add_control(
			'background_search_popup_heading',
			[
				'label' => esc_html__( 'Background overlay', 'bw-printxtore' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'none',
				'condition' => [
					'style_content' => ['side-left','side-right'],
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_overlay',
				'label' => esc_html__( 'Background overlay', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .elbzotech-mini-cart-side .mini-cart-side-overlay',
				'condition' => [
					'style_content' => ['side-left','side-right'],
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_footer_side_style',
			[
				'label' => esc_html__( 'Footer side', 'bw-printxtore' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style_content' => ['side-left','side-right'],
				]
			]
		);

		$this->add_responsive_control(
			'padding_footer_side',
			[
				'label' => esc_html__( 'Padding', 'bw-printxtore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mini-cart-side .mini-cart-footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_footer_side',
				'label' => esc_html__( 'Background', 'bw-printxtore' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .elbzotech-mini-cart-side .mini-cart-footer',
			]
		);

		$this->add_control(
			'footer_text_color',
			[
				'label' => esc_html__( 'Text Color', 'bw-printxtore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mini-cart-total > *' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'footer_text_space',
			[
				'label' => esc_html__( 'Space text', 'bw-printxtore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .mini-cart-total' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'shadow_footer_side',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .elbzotech-mini-cart-side .mini-cart-footer',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_footer_side',
				'selector' => '{{WRAPPER}} .elbzotech-mini-cart-side .mini-cart-footer',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'border_radius_footer_side',
			[
				'label' => esc_html__( 'Border Radius', 'bw-printxtore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elbzotech-mini-cart-side .mini-cart-footer' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		$settings = $this->get_settings();
		if(!class_exists("woocommerce")) return;
		$image_size_product = $settings['image_size_product_size'];
		if($image_size_product == 'custom' && !empty($settings['image_size_product_custom_dimension']['width']) && !empty($settings['image_size_product_custom_dimension']['height']))
        	$image_size_product = array($settings['image_size_product_custom_dimension']['width'],$settings['image_size_product_custom_dimension']['height']);
		?>
		<div class="elbzotech-mini-cart-global <?php if($settings['style_content'] !== 'dropdown') echo 'elbzotech-mini-cart-side-global '; echo esc_attr('elbzotech-mini-cart-global-'.$settings['style'].' elbzotech-mini-cart-'.$settings['style_content'].'-global')?>">			
			
			<?php if($settings['style_content'] == 'elbzotech-mini-cart-side-left' || $settings['style_content'] == 'elbzotech-mini-cart-side-right') echo '<div class="mini-cart-side-overlay"></div>';?>
			
			<?php
			$attr = array(
				'wdata'		=> $this,
				'settings'	=> $settings,
			);
			echo bzotech_get_template_elementor_global('mini-cart/mini-cart',$settings['style'],$attr);
			?>
            <div class="mini-cart-content mini-cart-dropdown-<?php echo esc_attr($settings['dropdown_pos']); ?>">
            	<?php 
            	global $woocommerce;
            	?>
		       
		        <div class="mini-cart-main-content"><?php $this->mini_cart($image_size_product)?></div>
		        <div class="total-default hidden"><?php echo wc_price(0)?></div>
		        <i class="las la-times elbzotech-close-mini-cart"></i>
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
	protected function content_template() {
		
	}
}
