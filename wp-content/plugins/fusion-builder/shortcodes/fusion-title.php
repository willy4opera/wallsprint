<?php
/**
 * Add an element to fusion-builder.
 *
 * @package fusion-builder
 * @since 1.0
 */

if ( fusion_is_element_enabled( 'fusion_title' ) ) {

	if ( ! class_exists( 'FusionSC_Title' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @since 1.0
		 */
		class FusionSC_Title extends Fusion_Element {

			/**
			 * Title counter.
			 *
			 * @access protected
			 * @since 1.9
			 * @var integer
			 */
			protected $title_counter = 1;

			/**
			 * Constructor.
			 *
			 * @access public
			 * @since 1.0
			 */
			public function __construct() {
				parent::__construct();
				add_filter( 'fusion_attr_title-shortcode', [ $this, 'attr' ] );
				add_filter( 'fusion_attr_title-shortcode-heading', [ $this, 'heading_attr' ] );
				add_filter( 'fusion_attr_marquee-text-wrapper', [ $this, 'marquee_text_attr' ] );
				add_filter( 'fusion_attr_animated-text-wrapper', [ $this, 'animated_text_wrapper' ] );
				add_filter( 'fusion_attr_rotated-text', [ $this, 'rotated_text_attr' ] );
				add_filter( 'fusion_attr_title-shortcode-sep', [ $this, 'sep_attr' ] );
				add_filter( 'fusion_attr_title-shortcode-href', [ $this, 'href_attr' ] );

				add_shortcode( 'fusion_title', [ $this, 'render' ] );
			}

			/**
			 * Gets the default values.
			 *
			 * @static
			 * @access public
			 * @since 2.0.0
			 * @return array
			 */
			public static function get_element_defaults() {
				$fusion_settings = awb_get_fusion_settings();

				return [
					'animation_direction'            => 'left',
					'animation_offset'               => $fusion_settings->get( 'animation_offset' ),
					'animation_speed'                => '',
					'animation_delay'                => '',
					'animation_type'                 => '',
					'animation_color'                => '',
					'hide_on_mobile'                 => fusion_builder_default_visibility( 'string' ),
					'sticky_display'                 => '',
					'class'                          => '',
					'id'                             => '',
					'title_type'                     => 'text',
					'rotation_effect'                => 'bounceIn',
					'display_time'                   => '1200',
					'highlight_effect'               => 'circle',
					'loop_animation'                 => 'once',
					'highlight_animation_duration'   => '1500',
					'highlight_smudge_effect'        => 'no',
					'highlight_width'                => '9',
					'highlight_top_margin'           => '0',
					'before_text'                    => '',
					'rotation_text'                  => '',
					'highlight_text'                 => '',
					'title_link'                     => 'off',
					'link_url'                       => '',
					'link_target'                    => '_self',
					'link_color'                     => $fusion_settings->get( 'link_color' ),
					'link_hover_color'               => $fusion_settings->get( 'link_hover_color' ),
					'fusion_font_family_title_font'  => '',
					'fusion_font_variant_title_font' => '',
					'after_text'                     => '',
					'content_align'                  => 'left',
					'content_align_medium'           => '',
					'content_align_small'            => '',
					'font_size'                      => '',
					'animated_font_size'             => '',
					'letter_spacing'                 => '',
					'line_height'                    => '',
					'link_attributes'                => '',
					'margin_bottom'                  => $fusion_settings->get( 'title_margin', 'bottom' ),
					'margin_bottom_medium'           => '',
					'margin_bottom_mobile'           => '',
					'margin_bottom_small'            => $fusion_settings->get( 'title_margin_mobile', 'bottom' ),
					'margin_top'                     => $fusion_settings->get( 'title_margin', 'top' ),
					'margin_top_medium'              => '',
					'margin_top_mobile'              => '',
					'margin_top_small'               => $fusion_settings->get( 'title_margin_mobile', 'top' ),
					'margin_right'                   => $fusion_settings->get( 'title_margin', 'right' ),
					'margin_right_medium'            => '',
					'margin_right_small'             => $fusion_settings->get( 'title_margin_mobile', 'right' ),
					'margin_left'                    => $fusion_settings->get( 'title_margin', 'left' ),
					'margin_left_medium'             => '',
					'margin_left_small'              => $fusion_settings->get( 'title_margin_mobile', 'left' ),
					'sep_color'                      => $fusion_settings->get( 'title_border_color' ),
					'marquee_direction'              => 'left',
					'marquee_mask_edges'             => 'no',
					'marquee_speed'                  => '15000',
					'scroll_reveal_above_fold'       => 'yes',
					'scroll_reveal_basis'            => 'chars',
					'scroll_reveal_behavior'         => 'always',
					'scroll_reveal_delay'            => '0',
					'scroll_reveal_duration'         => '500',
					'scroll_reveal_effect'           => 'color_change',
					'scroll_reveal_stagger'          => '150',
					'size'                           => 1,
					'style_tag'                      => '',
					'style_type'                     => $fusion_settings->get( 'title_style_type' ),
					'text_color'                     => '',
					'text_shadow'                    => '',
					'text_shadow_blur'               => '',
					'text_shadow_color'              => '',
					'text_shadow_horizontal'         => '',
					'text_shadow_vertical'           => '',
					'text_transform'                 => $fusion_settings->get( 'title_text_transform' ),
					'animated_text_color'            => '',
					'highlight_color'                => '',
					'responsive_typography'          => 0.0 < $fusion_settings->get( 'typography_sensitivity' ),
					'gradient_font'                  => 'no',
					'gradient_start_color'           => '',
					'gradient_end_color'             => '',
					'gradient_start_position'        => '0',
					'gradient_end_position'          => '100',
					'gradient_type'                  => 'linear',
					'radial_direction'               => 'center center',
					'linear_angle'                   => '180',
					'text_stroke'                    => '',
					'text_stroke_size'               => '1',
					'text_stroke_color'              => 'var(--primary_color)',
					'text_overflow'                  => 'none',
				];
			}

			/**
			 * Maps settings to param variables.
			 *
			 * @static
			 * @access public
			 * @since 2.0.0
			 * @return array
			 */
			public static function settings_to_params() {
				return [
					'title_margin[top]'           => 'margin_top',
					'title_margin[right]'         => 'margin_right',
					'title_margin[bottom]'        => 'margin_bottom',
					'title_margin[left]'          => 'margin_left',
					'title_margin_mobile[top]'    => 'margin_top_small',
					'title_margin_mobile[right]'  => 'margin_right_small',
					'title_margin_mobile[bottom]' => 'margin_bottom_small',
					'title_margin_mobile[left]'   => 'margin_left_small',
					'title_border_color'          => 'sep_color',
					'title_style_type'            => 'style_type',
				];
			}

			/**
			 * Used to set any other variables for use on front-end editor template.
			 *
			 * @static
			 * @access public
			 * @since 2.0.0
			 * @return array
			 */
			public static function get_element_extras() {
				$fusion_settings = awb_get_fusion_settings();
				return [
					'primary_color'       => $fusion_settings->get( 'primary_color' ),
					'content_break_point' => $fusion_settings->get( 'content_break_point' ),
					'visibility_large'    => $fusion_settings->get( 'visibility_large' ),
					'visibility_medium'   => $fusion_settings->get( 'visibility_medium' ),
					'visibility_small'    => $fusion_settings->get( 'visibility_small' ),
				];
			}

			/**
			 * Maps settings to extra variables.
			 *
			 * @static
			 * @access public
			 * @since 2.0.0
			 * @return array
			 */
			public static function settings_to_extras() {

				return [
					'content_break_point' => 'content_break_point',
				];
			}

			/**
			 * Renders content
			 *
			 * @access public
			 * @since 3.0
			 * @param string $content Content between shortcode.
			 * @return string
			 */
			public function render_content( $content ) {
				fusion_element_rendering_elements( true );
				$content = do_shortcode( $content );

				if ( 'off' !== $this->args['title_link'] ) {
					$content = '<a ' . FusionBuilder::attributes( 'title-shortcode-href' ) . '>' . $content . '</a>';
				}

				fusion_element_rendering_elements( false );
				return $content;
			}

			/**
			 * Validate args.
			 *
			 * @access public
			 * @since 1.0
			 * @return void
			 */
			public function validate_args() {
				$this->args['margin_top']    = FusionBuilder::validate_shortcode_attr_value( $this->args['margin_top'], 'px' );
				$this->args['margin_right']  = FusionBuilder::validate_shortcode_attr_value( $this->args['margin_right'], 'px' );
				$this->args['margin_bottom'] = FusionBuilder::validate_shortcode_attr_value( $this->args['margin_bottom'], 'px' );
				$this->args['margin_left']   = FusionBuilder::validate_shortcode_attr_value( $this->args['margin_left'], 'px' );

				$this->args['margin_top_medium']    = FusionBuilder::validate_shortcode_attr_value( $this->args['margin_top_medium'], 'px' );
				$this->args['margin_right_medium']  = FusionBuilder::validate_shortcode_attr_value( $this->args['margin_right_medium'], 'px' );
				$this->args['margin_bottom_medium'] = FusionBuilder::validate_shortcode_attr_value( $this->args['margin_bottom_medium'], 'px' );
				$this->args['margin_left_medium']   = FusionBuilder::validate_shortcode_attr_value( $this->args['margin_left_medium'], 'px' );

				$this->args['margin_top_small']    = FusionBuilder::validate_shortcode_attr_value( $this->args['margin_top_small'], 'px' );
				$this->args['margin_right_small']  = FusionBuilder::validate_shortcode_attr_value( $this->args['margin_right_small'], 'px' );
				$this->args['margin_bottom_small'] = FusionBuilder::validate_shortcode_attr_value( $this->args['margin_bottom_small'], 'px' );
				$this->args['margin_left_small']   = FusionBuilder::validate_shortcode_attr_value( $this->args['margin_left_small'], 'px' );

				// BC.
				$this->args['margin_top_mobile']    = FusionBuilder::validate_shortcode_attr_value( $this->args['margin_top_mobile'], 'px' );
				$this->args['margin_bottom_mobile'] = FusionBuilder::validate_shortcode_attr_value( $this->args['margin_bottom_mobile'], 'px' );

				if ( 'on' === $this->args['loop_animation'] ) {
					$this->args['loop_animation'] = 'loop';
				}
				if ( 'off' === $this->args['loop_animation'] ) {
					$this->args['loop_animation'] = 'once';
				}

				if (  'highlight' === $this->args['title_type']  && 'yes' === $this->args['highlight_smudge_effect'] ) {
					$color = $this->args['highlight_color'] ? $this->args['highlight_color'] : fusion_library()->get_option( 'primary_color' );
					$alpha = Fusion_Color::new_color( $color )->alpha;

					$this->args['highlight_color_trans'] = Fusion_Color::new_color( $color )->get_new( 'alpha', '0' )->to_css( 'rgba' );
					$this->args['highlight_color_min']   = Fusion_Color::new_color( $color )->get_new( 'alpha', $alpha * 0.15 )->to_css( 'rgba' );
					$this->args['highlight_color_inter'] = Fusion_Color::new_color( $color )->get_new( 'alpha', $alpha * 0.3 )->to_css( 'rgba' );
					$this->args['highlight_color_max']   = Fusion_Color::new_color( $color )->get_new( 'alpha', $alpha * 0.9 )->to_css( 'rgba' );
				}

				if ( 'marquee' === $this->args['title_type'] ) {
					$this->args['marquee_translate_x'] = 'left' === $this->args['marquee_direction'] ? '-100%' : '100%';
					$this->args['marquee_speed']      .= 'ms';
				}
			}

			/**
			 * Render the shortcode
			 *
			 * @access public
			 * @since 1.0
			 * @param  array  $args    Shortcode parameters.
			 * @param  string $content Content between shortcode.
			 * @return string          HTML output.
			 */
			public function render( $args, $content = '' ) {
				$fusion_settings = awb_get_fusion_settings();

				$this->defaults = self::get_element_defaults();
				$this->args     = FusionBuilder::set_shortcode_defaults( self::get_element_defaults(), $args, 'fusion_title' );
				$this->args     = apply_filters( 'fusion_builder_default_args', $this->args, 'fusion_title', $args );
				$content        = apply_filters( 'fusion_shortcode_content', $content, 'fusion_title', $args );

				$this->validate_args();

				$is_flex_container = fusion_element_rendering_is_flex();

				$this->set_text_shadow_style();

				if ( ! $this->args['style_type'] || 'default' === $this->args['style_type'] ) {
					$this->args['style_type'] = $fusion_settings->get( 'title_style_type' );
				}

				if ( 'text' !== $this->args['title_type'] ) {
					$this->args['style_type'] = 'none';
				}

				if ( 1 === count( explode( ' ', $this->args['style_type'] ) ) ) {
					$this->args['style_type'] .= ' solid';
				}

				// Make sure the title text is not wrapped with an unattributed p tag.
				$content        = preg_replace( '!^<p>(.*?)</p>$!i', '$1', trim( $content ) );
				$rotation_texts = [];

				if ( 'rotating' === $this->args['title_type'] && $this->args['rotation_text'] ) {
					$rotation_texts = explode( '|', trim( $this->args['rotation_text'] ) );
				}

				$title_tag = 'div' === $this->args['size'] || 'p' === $this->args['size'] ? $this->args['size'] : 'h' . $this->args['size'];

				if ( 'rotating' === $this->args['title_type'] ) {

					$html  = '<div ' . FusionBuilder::attributes( 'title-shortcode' ) . '>';
					$html .= '<' . $title_tag . ' ' . FusionBuilder::attributes( 'title-shortcode-heading' ) . '>';
					$html .= '<span class="fusion-animated-text-prefix">' . $this->args['before_text'] . '</span> ';

					if ( 0 < count( $rotation_texts ) ) {
						$html .= '<span ' . FusionBuilder::attributes( 'animated-text-wrapper' ) . '>';
						$html .= '<span class="fusion-animated-texts">';

						foreach ( $rotation_texts as $text ) {
							if ( '' !== $text ) {
								$html .= '<span ' . FusionBuilder::attributes( 'rotated-text' ) . '>' . $text . '</span>';
							}
						}

						$html .= '</span></span>';
					}

					$html .= ' <span class="fusion-animated-text-postfix">' . $this->args['after_text'] . '</span>';
					$html .= '</' . $title_tag . '>';
					$html .= '</div>';

				} elseif ( 'highlight' === $this->args['title_type'] ) {
					$html  = '<div ' . FusionBuilder::attributes( 'title-shortcode' ) . '>';
					$html .= '<' . $title_tag . ' ' . FusionBuilder::attributes( 'title-shortcode-heading' ) . '>';
					$html .= '<span class="fusion-highlighted-text-prefix">' . $this->args['before_text'] . '</span> ';

					if ( $this->args['highlight_text'] ) {
						$html .= '<span class="fusion-highlighted-text-wrapper">';
						$html .= '<span ' . FusionBuilder::attributes( 'animated-text-wrapper' ) . '>' . $this->args['highlight_text'] . '</span>';

						$highlight_effects = [
							'circle'               => [ 'M344.6,40.1c0,0-293-3.4-330.7,40.3c-5.2,6-3.5,15.3,3.3,19.4c65.8,39,315.8,42.3,451.2-3 c6.3-2.1,12-6.1,16-11.4C527.9,27,242,16.1,242,16.1' ],
							'underline_zigzag'     => [ 'M6.1,133.6c0,0,173.4-20.6,328.3-14.5c154.8,6.1,162.2,8.7,162.2,8.7s-262.6-4.9-339.2,13.9 c0,0,113.8-6.1,162.9,6.9' ],
							'x'                    => [ 'M25.8,37.1c0,0,321.2,56.7,435.5,82.3', 'M55.8,108.7c0,0,374-78.3,423.6-76.3' ],
							'strikethrough'        => [ 'M22.2,93.2c0,0,222.1-11.3,298.8-15.8c84.2-4.9,159.1-4.7,159.1-4.7' ],
							'curly'                => [ 'M9.4,146.9c0,0,54.4-60.2,102.1-11.6c42.3,43.1,84.3-65.7,147.3,0.9c37.6,39.7,79.8-52.6,123.8-14.4 c68.6,59.4,107.2-7,107.2-7' ],
							'diagonal_bottom_left' => [ 'M6.5,127.1C10.6,126.2,316.9,24.8,497,23.9' ],
							'diagonal_top_left'    => [ 'M7.2,28.5c0,0,376.7,64.4,485.2,93.4' ],
							'double'               => [ 'M21.7,145.7c0,0,192.2-33.7,456.3-14.6', 'M13.6,28.2c0,0,296.2-22.5,474.9-5.4' ],
							'double_underline'     => [ 'M10.3,130.6c0,0,193.9-24.3,475.2-11.2', 'M38.9,148.9c0,0,173.8-35.3,423.3-11.8' ],
							'underline'            => [ 'M8.1,146.2c0,0,240.6-55.6,479-13.8' ]
						];

						$paths = isset( $highlight_effects[ $this->args['highlight_effect'] ] ) ? $highlight_effects[ $this->args['highlight_effect'] ] : [];

						if ( ! empty( $paths ) ) {
							$html .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none">';
							$style = '';

							if ( 'yes' === $this->args['highlight_smudge_effect'] ) {
								$style = ' style="stroke: url(#stroke-gradient-' . $this->title_counter . ');"';

								$html .= '<defs>
									<linearGradient id="stroke-gradient-' . $this->title_counter . '" x1="0%" y1="0%" x2="100%" y2="0%">
										<stop offset="0%" style="stop-color: ' . esc_attr( $this->args['highlight_color_min'] ) . ';" />
										<stop offset="5%" style="stop-color: ' . esc_attr( $this->args['highlight_color_max'] ) . ';" />
										<stop offset="100%" style="stop-color: ' . esc_attr( $this->args['highlight_color_inter'] ) . ';" />
									</linearGradient>
								</defs>';
							}
						
							foreach ( $paths as $path ) {
								$html .= '<path' . $style . ' d="' . esc_attr( $path ) . '"></path>';
							}
							$html .= '</svg>';
						}

						$html .= '</span>';
					}

					$html .= ' <span class="fusion-highlighted-text-postfix">' . $this->args['after_text'] . '</span>';
					$html .= '</' . $title_tag . '>';
					$html .= '</div>';
				} elseif ( 'marquee' === $this->args['title_type'] ) {
					$html    = '<div ' . FusionBuilder::attributes( 'title-shortcode' ) . '>';
					$html   .= '<' . $title_tag . ' ' . FusionBuilder::attributes( 'title-shortcode-heading' ) . '>';
					$marquee = '<span ' . FusionBuilder::attributes( 'marquee-text-wrapper' ) . '>' . fusion_force_balance_tags( $this->render_content( $content ) ) . '</span>';
					$html   .= $marquee . $marquee;
					$html   .= '</' . $title_tag . '>';
					$html   .= '</div>';
				} elseif ( false !== strpos( $this->args['style_type'], 'underline' ) || false !== strpos( $this->args['style_type'], 'none' ) ) {
					$html  = '<div ' . FusionBuilder::attributes( 'title-shortcode' ) . '>';
					$html .= '<' . $title_tag . ' ' . FusionBuilder::attributes( 'title-shortcode-heading' ) . '>';
					$html .= $this->render_content( $content );
					$html .= '</' . $title_tag . '>';
					$html .= '</div>';
				} else {
					if ( 'right' === $this->args['content_align'] && ! $is_flex_container ) {
						$html  = '<div ' . FusionBuilder::attributes( 'title-shortcode' ) . '>';
						$html .= '<div ' . FusionBuilder::attributes( 'title-sep-container' ) . '>';
						$html .= '<div ' . FusionBuilder::attributes( 'title-shortcode-sep' ) . '></div>';
						$html .= '</div>';
						$html .= '<span ' . FusionBuilder::attributes( 'awb-title-spacer' ) . '></span>';
						$html .= '<' . $title_tag . ' ' . FusionBuilder::attributes( 'title-shortcode-heading' ) . '>';
						$html .= $this->render_content( $content );
						$html .= '</' . $title_tag . '>';
						$html .= '</div>';
					} elseif ( 'center' === $this->args['content_align'] || $is_flex_container ) {
						$left_classes             = 'title-sep-container title-sep-container-left';
						$right_classes            = 'title-sep-container title-sep-container-right';
						$additional_left_classes  = '';
						$additional_right_classes = '';

						if ( $is_flex_container ) {
							foreach ( [ 'large', 'medium', 'small' ] as $responsive_size ) {
								$key   = 'content_align' . ( 'large' === $responsive_size ? '' : '_' . $responsive_size );
								$value = isset( $this->args[ $key ] ) && '' !== $this->args[ $key ] ? $this->args[ $key ] : $this->args['content_align'];

								if ( 'left' === $value ) {
									$additional_left_classes .= ' fusion-no-' . $responsive_size . '-visibility';
								} elseif ( 'right' === $value ) {
									$additional_right_classes .= ' fusion-no-' . $responsive_size . '-visibility';
								}
							}

							$left_classes  .= $additional_left_classes;
							$right_classes .= $additional_right_classes;
						}

						$html  = '<div ' . FusionBuilder::attributes( 'title-shortcode' ) . '>';
						$html .= '<div ' . FusionBuilder::attributes( $left_classes ) . '>';
						$html .= '<div ' . FusionBuilder::attributes( 'title-shortcode-sep' ) . '></div>';
						$html .= '</div>';
						$html .= '<span ' . FusionBuilder::attributes( 'awb-title-spacer' . $additional_left_classes ) . '></span>';
						$html .= '<' . $title_tag . ' ' . FusionBuilder::attributes( 'title-shortcode-heading' ) . '>';
						$html .= $this->render_content( $content );
						$html .= '</' . $title_tag . '>';
						$html .= '<span ' . FusionBuilder::attributes( 'awb-title-spacer' . $additional_right_classes ) . '></span>';
						$html .= '<div ' . FusionBuilder::attributes( $right_classes ) . '>';
						$html .= '<div ' . FusionBuilder::attributes( 'title-shortcode-sep' ) . '>';
						$html .= '</div></div></div>';
					} else {
						$html  = '<div ' . FusionBuilder::attributes( 'title-shortcode' ) . '>';
						$html .= '<' . $title_tag . ' ' . FusionBuilder::attributes( 'title-shortcode-heading' ) . '>';
						$html .= $this->render_content( $content );
						$html .= '</' . $title_tag . '>';
						$html .= '<span ' . FusionBuilder::attributes( 'awb-title-spacer' ) . '></span>';
						$html .= '<div ' . FusionBuilder::attributes( 'title-sep-container' ) . '>';
						$html .= '<div ' . FusionBuilder::attributes( 'title-shortcode-sep' ) . '></div>';
						$html .= '</div>';
						$html .= '</div>';

						fusion_element_rendering_elements( false );
					}
				}

				$this->title_counter++;

				$this->on_render();

				return apply_filters( 'fusion_element_title_content', $html, $args );
			}

			/**
			 * Get CSS variables for options.
			 *
			 * @access public
			 * @since 3.9
			 * @return string
			 */
			public function get_css_vars() {
				$bottom_highlights = [ 'underline', 'double_underline', 'underline_zigzag', 'underline_zigzag', 'curly' ];
				$css_vars          = [
					'text_color' => [ 'callback' => [ 'Fusion_Sanitize', 'color' ] ],
				];

				if ( 'highlight' === $this->args['title_type'] ) {
					if ( $this->args['highlight_color'] ) {
						$css_vars['highlight_color'] = [ 'callback' => [ 'Fusion_Sanitize', 'color' ] ];
					}

					if ( $this->args['highlight_top_margin'] && in_array( $this->args['highlight_effect'], $bottom_highlights, true ) ) {
						$css_vars['highlight_top_margin'] = [ 'callback' => [ 'Fusion_Sanitize', 'get_value_with_unit' ] ];
					}

					$css_vars['highlight_animation_duration'] = [ 'callback' => [ 'Fusion_Sanitize', 'number' ] ];

					if ( $this->args['highlight_width'] ) {
						$css_vars['highlight_width'] = [ 'callback' => [ 'Fusion_Sanitize', 'number' ] ];
					}

					if ( 'marker' === $this->args['highlight_effect'] ) {
						if ( 'yes' === $this->args['highlight_smudge_effect'] ) {
							$css_vars['highlight_color_trans']   = [ 'callback' => [ 'Fusion_Sanitize', 'color' ] ];
							$css_vars['highlight_color_min']     = [ 'callback' => [ 'Fusion_Sanitize', 'color' ] ];
							$css_vars['highlight_color_inter']   = [ 'callback' => [ 'Fusion_Sanitize', 'color' ] ];
							$css_vars['highlight_color_max']     = [ 'callback' => [ 'Fusion_Sanitize', 'color' ] ];
						}
					}
				}

				if ( 'scroll_reveal' === $this->args['title_type'] ) {
					$css_vars['animated_text_color'] = [ 'callback' => [ 'Fusion_Sanitize', 'color' ] ];
				}

				if ( ! fusion_element_rendering_is_flex() && ! ( '' === $this->args['margin_top_mobile'] && '' === $this->args['margin_bottom_mobile'] ) ) {
					$this->args['margin_top_small']    = $this->args['margin_top_mobile'];
					$this->args['margin_bottom_small'] = $this->args['margin_bottom_mobile'];
				}

				$css_vars['margin_top']           = [ 'callback' => [ 'Fusion_Sanitize', 'get_value_with_unit' ] ];
				$css_vars['margin_right']         = [ 'callback' => [ 'Fusion_Sanitize', 'get_value_with_unit' ] ];
				$css_vars['margin_bottom']        = [ 'callback' => [ 'Fusion_Sanitize', 'get_value_with_unit' ] ];
				$css_vars['margin_left']          = [ 'callback' => [ 'Fusion_Sanitize', 'get_value_with_unit' ] ];
				$css_vars['margin_top_small']     = [ 'callback' => [ 'Fusion_Sanitize', 'get_value_with_unit' ] ];
				$css_vars['margin_right_small']   = [ 'callback' => [ 'Fusion_Sanitize', 'get_value_with_unit' ] ];
				$css_vars['margin_bottom_small']  = [ 'callback' => [ 'Fusion_Sanitize', 'get_value_with_unit' ] ];
				$css_vars['margin_left_small']    = [ 'callback' => [ 'Fusion_Sanitize', 'get_value_with_unit' ] ];
				$css_vars['margin_top_medium']    = [ 'callback' => [ 'Fusion_Sanitize', 'get_value_with_unit' ] ];
				$css_vars['margin_right_medium']  = [ 'callback' => [ 'Fusion_Sanitize', 'get_value_with_unit' ] ];
				$css_vars['margin_bottom_medium'] = [ 'callback' => [ 'Fusion_Sanitize', 'get_value_with_unit' ] ];
				$css_vars['margin_left_medium']   = [ 'callback' => [ 'Fusion_Sanitize', 'get_value_with_unit' ] ];
				$css_vars['text_stroke_size']     = [ 'callback' => [ 'Fusion_Sanitize', 'get_value_with_unit' ] ];
				$css_vars['text_stroke_color']    = [ 'callback' => [ 'Fusion_Sanitize', 'color' ] ];

				if ( ( 'text' === $this->args['title_type'] || 'marquee' === $this->args['title_type'] ) && 'on' === $this->args['title_link'] ) {
					$css_vars['link_color']       = [ 'callback' => [ 'Fusion_Sanitize', 'color' ] ];
					$css_vars['link_hover_color'] = [ 'callback' => [ 'Fusion_Sanitize', 'color' ] ];
				}

				if ( 'marquee' === $this->args['title_type'] ) {
					$css_vars[] = 'marquee_speed';
					$css_vars[] = 'marquee_translate_x';
				}

				$css_vars['sep_color'] = [ 'callback' => [ 'Fusion_Sanitize', 'color' ] ];
				$css_vars['font_size'] = [ 'callback' => [ 'Fusion_Sanitize', 'get_value_with_unit' ] ];

				if ( 'none' !== $this->args['text_overflow'] ) {
					$css_vars[] = 'text_overflow';
				}

				return $this->get_css_vars_for_options( $css_vars );
			}

			/**
			 * Sets style for text shadow if set.
			 *
			 * @access public
			 * @since 3.1
			 * @return void
			 */
			public function set_text_shadow_style() {
				$this->args['text_shadow_styles'] = '';

				if ( 'yes' === $this->args['text_shadow'] ) {
					$text_shadow_styles = Fusion_Builder_Text_Shadow_Helper::get_text_shadow_styles(
						[
							'text_shadow_horizontal' => $this->args['text_shadow_horizontal'],
							'text_shadow_vertical'   => $this->args['text_shadow_vertical'],
							'text_shadow_blur'       => $this->args['text_shadow_blur'],
							'text_shadow_color'      => $this->args['text_shadow_color'],
						]
					);

					if ( 'yes' === $this->args['gradient_font'] ) {
						$this->args['text_shadow_styles'] = 'filter:drop-shadow(' . esc_attr( trim( $text_shadow_styles ) ) . ');';
					} else {
						$this->args['text_shadow_styles'] = 'text-shadow:' . esc_attr( trim( $text_shadow_styles ) ) . ';';
					}
				}
			}

			/**
			 * Builds the attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function attr() {

				$attr = fusion_builder_visibility_atts(
					$this->args['hide_on_mobile'],
					[
						'class'          => 'fusion-title title fusion-title-' . $this->title_counter,
						'style'          => $this->get_css_vars(),
						'data-highlight' => '',
					]
				);

				$attr['class'] .= Fusion_Builder_Sticky_Visibility_Helper::get_sticky_class( $this->args['sticky_display'] );

				if ( false !== strpos( $this->args['style_type'], 'underline' ) ) {
					$styles = explode( ' ', $this->args['style_type'] );

					foreach ( $styles as $style ) {
						$attr['class'] .= ' sep-' . $style;
					}
				} elseif ( false !== strpos( $this->args['style_type'], 'none' ) ) {
					$attr['class'] .= ' fusion-sep-none';
				}

				if ( 'center' === $this->args['content_align'] ) {
					$attr['class'] .= ' fusion-title-center';
				}

				if ( $this->args['title_type'] ) {
					$attr['class'] .= ' fusion-title-' . $this->args['title_type'];
				}

				if ( 'text' !== $this->args['title_type'] && $this->args['loop_animation'] ) {
					$attr['class'] .= ' fusion-animate-' . $this->args['loop_animation'];
				}

				if ( 'rotating' === $this->args['title_type'] && $this->args['rotation_effect'] ) {
					$attr['class'] .= ' fusion-title-' . $this->args['rotation_effect'];
				}

				if ( 'highlight' === $this->args['title_type'] && $this->args['highlight_effect'] ) {
					$attr['data-highlight'] .= $this->args['highlight_effect'];
					$attr['class']          .= ' fusion-highlight-' . $this->args['highlight_effect'];
				}

				if ( 'scroll_reveal' === $this->args['title_type'] && $this->args['scroll_reveal_effect'] ) {
					$attr['class']                        .= ' awb-title__scroll-reveal--' . $this->args['scroll_reveal_effect'];
					$attr['data-scroll-reveal-effect']     = $this->args['scroll_reveal_effect'];
					$attr['data-scroll-reveal-above-fold'] = 'yes' === $this->args['scroll_reveal_above_fold'] ? true : false;
					$attr['data-scroll-reveal-basis']      = $this->args['scroll_reveal_basis'];
					$attr['data-scroll-reveal-behavior']   = $this->args['scroll_reveal_behavior'];
					$attr['data-scroll-reveal-duration']   = (float) $this->args['scroll_reveal_duration'] / 1000;
					$attr['data-scroll-reveal-stagger']    = (float) $this->args['scroll_reveal_stagger'] / 1000;
					$attr['data-scroll-reveal-delay']      = (float) $this->args['scroll_reveal_delay'] / 1000;
					$attr['data-scroll-reveal-counter']    = $this->title_counter;
					
				}

				$title_size = 'div';
				if ( '1' == $this->args['size'] ) { // phpcs:ignore Universal.Operators.StrictComparisons
					$title_size = 'one';
				} elseif ( '2' == $this->args['size'] ) { // phpcs:ignore Universal.Operators.StrictComparisons
					$title_size = 'two';
				} elseif ( '3' == $this->args['size'] ) { // phpcs:ignore Universal.Operators.StrictComparisons
					$title_size = 'three';
				} elseif ( '4' == $this->args['size'] ) { // phpcs:ignore Universal.Operators.StrictComparisons
					$title_size = 'four';
				} elseif ( '5' == $this->args['size'] ) { // phpcs:ignore Universal.Operators.StrictComparisons
					$title_size = 'five';
				} elseif ( '6' == $this->args['size'] ) { // phpcs:ignore Universal.Operators.StrictComparisons
					$title_size = 'six';
				} elseif ( 'p' == $this->args['size'] ) { // phpcs:ignore Universal.Operators.StrictComparisons
					$title_size = 'paragraph';
				}

				$attr['class'] .= ' fusion-title-size-' . $title_size;

				if ( $this->args['animation_type'] ) {
					$attr = Fusion_Builder_Animation_Helper::add_animation_attributes( $this->args, $attr );
				}

				if ( 'yes' === $this->args['text_stroke'] ) {
					$attr['class'] .= ' fusion-text-has-stroke';
				}

				if ( 'none' !== $this->args['text_overflow'] ) {
					$attr['class'] .= ' fusion-has-text-overflow';
				}

				if ( $this->args['class'] ) {
					$attr['class'] .= ' ' . $this->args['class'];
				}

				if ( $this->args['id'] ) {
					$attr['id'] = $this->args['id'];
				}

				return $attr;
			}

			/**
			 * Builds the heading attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function heading_attr() {
				$fusion_settings = awb_get_fusion_settings();

				$attr = [
					'class' => 'fusion-title-heading title-heading-' . $this->args['content_align'],
					'style' => '',
				];

				if ( 'div' === $this->args['size'] || 'p' === $this->args['size'] ) {
					$attr['class'] .= ' title-heading-tag';
				}

				if ( 'marquee' === $this->args['title_type'] ) {

					if ( 'right' === $this->args['marquee_direction'] && ! is_rtl() ) {
						$attr['dir'] = 'rtl';
					} elseif ( 'left' === $this->args['marquee_direction'] && is_rtl() ) {
						$attr['dir'] = 'ltr';
					}

					$attr['class'] .= ' awb-marquee-' . $this->args['marquee_direction'];

					if ( 'yes' === $this->args['marquee_mask_edges'] ) {
						$attr['class'] .= ' awb-marquee-masked';
					}
				}

				if ( fusion_element_rendering_is_flex() ) {
					if ( ! empty( $this->args['content_align_medium'] ) && $this->args['content_align'] !== $this->args['content_align_medium'] ) {
						$attr['class'] .= ' md-text-align-' . $this->args['content_align_medium'];
					}

					if ( ! empty( $this->args['content_align_small'] ) && $this->args['content_align'] !== $this->args['content_align_small'] ) {
						$attr['class'] .= ' sm-text-align-' . $this->args['content_align_small'];
					}
				}

				$attr['style'] .= Fusion_Builder_Element_Helper::get_font_styling( $this->args, 'title_font' );

				if ( '' !== $this->args['margin_top'] || '' !== $this->args['margin_bottom'] ) {
					$attr['style'] .= 'margin:0;';
				}

				if ( $this->args['font_size'] ) {
					$attr['style'] .= 'font-size:1em;';
				}

				if ( $this->args['letter_spacing'] ) {
					$attr['style'] .= 'letter-spacing:' . fusion_library()->sanitize->get_value_with_unit( $this->args['letter_spacing'] ) . ';';
				}

				if ( ! empty( $this->args['text_transform'] ) ) {
					$attr['style'] .= 'text-transform:' . $this->args['text_transform'] . ';';
				}

				if ( ( 'text' === $this->args['title_type'] || 'marquee' === $this->args['title_type'] ) && 'yes' === $this->args['gradient_font'] ) {
					$attr['style'] .= Fusion_Builder_Gradient_Helper::get_gradient_font_string( $this->args );
					$attr['class'] .= ' awb-gradient-text';
				}

				if ( $this->args['style_tag'] ) {
					$attr['style'] .= $this->args['style_tag'];
				}

				if ( $this->args['responsive_typography'] && false === strpos( $this->args['font_size'], 'clamp(' ) ) {
					$data           = awb_get_responsive_type_data( $this->args['size'], $this->args['font_size'], $this->args['line_height'] );
					$attr['class'] .= ' ' . $data['class'];
					$attr['style'] .= $data['font_size'];
					$attr['style'] .= $data['min_font_size'];
					$attr['style'] .= $data['line_height'];
				} elseif ( $this->args['line_height'] ) {
					$attr['style'] .= 'line-height:' . fusion_library()->sanitize->size( $this->args['line_height'] ) . ';';
				}

				// Text shadow.
				if ( '' !== $this->args['text_shadow_styles'] ) {
					$attr['style'] .= $this->args['text_shadow_styles'];
				}

				return $attr;
			}

			/**
			 * Builds the marquee content attributes array.
			 *
			 * @access public
			 * @since 3.11.3
			 * @return array
			 */
			public function marquee_text_attr() {
				if ( 'right' === $this->args['marquee_direction'] && ! is_rtl() ) {
					$attr['dir'] = 'ltr';
				} elseif ( 'left' === $this->args['marquee_direction'] && is_rtl() ) {
					$attr['dir'] = 'rtl';
				}

				$attr['class'] = 'awb-marquee-content';

				return $attr;
			}

			/**
			 * Builds the rotated text attributes array.
			 *
			 * @access public
			 * @since 2.1
			 * @return array
			 */
			public function rotated_text_attr() {

				$attr = [
					'data-in-effect'   => $this->args['rotation_effect'],
					'class'            => 'fusion-animated-text',
					'data-in-sequence' => 'true',
					'data-out-reverse' => 'true',
				];

				$attr['data-out-effect'] = str_replace( [ 'In', 'Down' ], [ 'Out', 'Up' ], $this->args['rotation_effect'] );

				return $attr;
			}

			/**
			 * Builds the animated text wrapper attributes array.
			 *
			 * @access public
			 * @since 2.1
			 * @return array
			 */
			public function animated_text_wrapper() {
				$attr = [
					'class' => 'fusion-animated-texts-wrapper',
					'style' => '',
				];

				if ( $this->args['animated_text_color'] ) {
					$attr['style'] .= 'color:' . fusion_library()->sanitize->color( $this->args['animated_text_color'] ) . ';';
				}

				if ( $this->args['animated_font_size'] ) {
					if ( $this->args['responsive_typography'] ) {
						$data           = awb_get_responsive_type_data( $this->args['size'], $this->args['animated_font_size'], $this->args['line_height'] );
						$attr['class'] .= ' ' . $data['class'];
						$attr['style'] .= $data['font_size'];
						$attr['style'] .= $data['min_font_size'];
					}

					$attr['style'] .= 'font-size:' . fusion_library()->sanitize->get_value_with_unit( $this->args['animated_font_size'] ) . ';';
				}

				if ( 'highlight' === $this->args['title_type'] ) {
					$attr['class'] = 'fusion-highlighted-text';
				}

				if ( 'rotating' === $this->args['title_type'] ) {
					$attr['data-length'] = $this->animation_length();

					if ( $this->args['display_time'] ) {
						$attr['data-minDisplayTime'] = fusion_library()->sanitize->number( $this->args['display_time'] );
					}

					if ( $this->args['after_text'] || ( ! $this->args['before_text'] && ! $this->args['after_text'] ) ) {
						$attr['style'] .= 'text-align: center;';
					}
				}

				return $attr;
			}

			/**
			 * Get animation length based on effect.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function animation_length() {
				$animation_length = '';

				switch ( $this->args['rotation_effect'] ) {

					case 'flipInX':
					case 'bounceIn':
					case 'zoomIn':
					case 'slideInDown':
					case 'clipIn':
						$animation_length = 'line';
						break;

					case 'lightSpeedIn':
						$animation_length = 'word';
						break;

					case 'rollIn':
					case 'typeIn':
					case 'fadeIn':
						$animation_length = 'char';
						break;
				}

				return $animation_length;
			}

			/**
			 * Builds the separator attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function sep_attr() {

				$attr = [
					'class' => 'title-sep',
				];

				$styles = explode( ' ', $this->args['style_type'] );

				foreach ( $styles as $style ) {
					$attr['class'] .= ' sep-' . $style;
				}

				if ( $this->args['sep_color'] ) {
					$attr['style'] = 'border-color:' . $this->args['sep_color'] . ';';
				}

				return $attr;
			}

			/**
			 * Builds the link attributes array.
			 *
			 * @access public
			 * @since 3.3
			 * @return array
			 */
			public function href_attr() {

				$attr = [
					'href'  => $this->args['link_url'],
					'class' => '',
				];

				if ( FusionBuilder()->post_card_data['is_rendering'] && empty( $attr['href'] ) ) {
					$attr['href'] = get_permalink( get_the_ID() );
				}

				$attr['target'] = $this->args['link_target'];

				if ( ( 'text' === $this->args['title_type'] || 'marquee' === $this->args['title_type'] ) && 'on' === $this->args['title_link'] ) {
					if ( $this->args['link_color'] ) {
						$attr['class'] .= 'awb-custom-text-color';
					}

					if ( $this->args['link_hover_color'] ) {
						$attr['class'] .= ' awb-custom-text-hover-color';
					}
				}

				// Add additional, custom link attributes correctly formatted to the anchor.
				$attr = fusion_get_link_attributes( $this->args, $attr );

				return $attr;
			}

			/**
			 * Adds settings to element options panel.
			 *
			 * @access public
			 * @since 1.1
			 * @return array $sections Title settings.
			 */
			public function add_options() {

				return [
					'title_shortcode_section' => [
						'label'       => esc_html__( 'Title', 'fusion-builder' ),
						'description' => '',
						'id'          => 'title_shortcode_section',
						'type'        => 'accordion',
						'icon'        => 'fusiona-H',
						'fields'      => [
							'title_text_transform' => [
								'label'       => esc_attr__( 'Text Transform', 'fusion-builder' ),
								'description' => esc_attr__( 'Choose how the text is displayed.', 'fusion-builder' ),
								'id'          => 'title_text_transform',
								'default'     => '',
								'type'        => 'select',
								'choices'     => [
									''           => esc_attr__( 'Default', 'fusion-builder' ),
									'none'       => esc_attr__( 'None', 'fusion-builder' ),
									'uppercase'  => esc_attr__( 'Uppercase', 'fusion-builder' ),
									'lowercase'  => esc_attr__( 'Lowercase', 'fusion-builder' ),
									'capitalize' => esc_attr__( 'Capitalize', 'fusion-builder' ),
								],
								'css_vars'    => [
									[
										'name' => '--title_text_transform',
									],
								],
							],
							'title_style_type'     => [
								'label'       => esc_html__( 'Title Separator', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the type of title separator that will display.', 'fusion-builder' ),
								'id'          => 'title_style_type',
								'default'     => 'none',
								'type'        => 'select',
								'transport'   => 'postMessage',
								'choices'     => [
									'single solid'     => esc_html__( 'Single Solid', 'fusion-builder' ),
									'single dashed'    => esc_html__( 'Single Dashed', 'fusion-builder' ),
									'single dotted'    => esc_html__( 'Single Dotted', 'fusion-builder' ),
									'double solid'     => esc_html__( 'Double Solid', 'fusion-builder' ),
									'double dashed'    => esc_html__( 'Double Dashed', 'fusion-builder' ),
									'double dotted'    => esc_html__( 'Double Dotted', 'fusion-builder' ),
									'underline solid'  => esc_html__( 'Underline Solid', 'fusion-builder' ),
									'underline dashed' => esc_html__( 'Underline Dashed', 'fusion-builder' ),
									'underline dotted' => esc_html__( 'Underline Dotted', 'fusion-builder' ),
									'none'             => esc_html__( 'None', 'fusion-builder' ),
								],
							],
							'title_border_color'   => [
								'label'       => esc_html__( 'Title Separator Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of the title separators.', 'fusion-builder' ),
								'id'          => 'title_border_color',
								'default'     => 'var(--awb-color3)',
								'type'        => 'color-alpha',
								'transport'   => 'postMessage',
								'css_vars'    => [
									[
										'name'     => '--title_border_color',
										'callback' => [ 'sanitize_color' ],
									],
								],
							],
							'title_margin'         => [
								'label'       => esc_html__( 'Title Margins', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the margin of the titles. Leave empty to use corresponding heading margins.', 'fusion-builder' ),
								'id'          => 'title_margin',
								'default'     => [
									'top'    => '10px',
									'right'  => '0px',
									'bottom' => '15px',
									'left'   => '0px',
								],
								'transport'   => 'postMessage',
								'type'        => 'spacing',
								'choices'     => [
									'top'    => true,
									'right'  => true,
									'bottom' => true,
									'left'   => true,
								],
								'css_vars'    => [
									[
										'name'   => '--title_margin-top',
										'choice' => 'top',
									],
									[
										'name'   => '--title_margin-right',
										'choice' => 'right',
									],
									[
										'name'   => '--title_margin-bottom',
										'choice' => 'bottom',
									],
									[
										'name'   => '--title_margin-left',
										'choice' => 'left',
									],
								],
							],
							'title_margin_mobile'  => [
								'label'       => esc_html__( 'Title Mobile Margins', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the margin of the titles on mobiles. Leave empty together with desktop margins to use corresponding heading margins.', 'fusion-builder' ),
								'id'          => 'title_margin_mobile',
								'transport'   => 'postMessage',
								'default'     => [
									'top'    => '10px',
									'right'  => '0px',
									'bottom' => '10px',
									'left'   => '0px',
								],
								'type'        => 'spacing',
								'choices'     => [
									'top'    => true,
									'right'  => true,
									'bottom' => true,
									'left'   => true,
								],
								'css_vars'    => [
									[
										'name'   => '--title_margin_mobile-top',
										'choice' => 'top',
									],
									[
										'name'   => '--title_margin_mobile-right',
										'choice' => 'right',
									],
									[
										'name'   => '--title_margin_mobile-bottom',
										'choice' => 'bottom',
									],
									[
										'name'   => '--title_margin_mobile-left',
										'choice' => 'left',
									],
								],
							],
						],
					],
				];
			}

			/**
			 * Sets the necessary scripts.
			 *
			 * @access public
			 * @since 3.2
			 * @return void
			 */
			public function on_first_render() {

				Fusion_Dynamic_JS::enqueue_script(
					'jquery-title-textillate',
					FusionBuilder::$js_folder_url . '/library/jquery.textillate.js',
					FusionBuilder::$js_folder_path . '/library/jquery.textillate.js',
					[ 'jquery' ],
					FUSION_BUILDER_VERSION,
					true
				);

				Fusion_Dynamic_JS::enqueue_script( 'fusion-title' );
			}

			/**
			 * Load base CSS.
			 *
			 * @access public
			 * @since 3.0
			 * @return void
			 */
			public function add_css_files() {
				FusionBuilder()->add_element_css( FUSION_BUILDER_PLUGIN_DIR . 'assets/css/shortcodes/title.min.css' );

				Fusion_Media_Query_Scripts::$media_query_assets[] = [
					'awb-title-md',
					FUSION_BUILDER_PLUGIN_DIR . 'assets/css/media/title-md.min.css',
					[],
					FUSION_BUILDER_VERSION,
					Fusion_Media_Query_Scripts::get_media_query_from_key( 'fusion-max-medium' ),
				];
				Fusion_Media_Query_Scripts::$media_query_assets[] = [
					'awb-title-sm',
					FUSION_BUILDER_PLUGIN_DIR . 'assets/css/media/title-sm.min.css',
					[],
					FUSION_BUILDER_VERSION,
					Fusion_Media_Query_Scripts::get_media_query_from_key( 'fusion-max-small' ),
				];
			}
		}
	}

	new FusionSC_Title();

}

/**
 * Map shortcode to Avada Builder.
 *
 * @since 1.0
 */
function fusion_element_title() {
	$fusion_settings = awb_get_fusion_settings();

	$is_builder = ( function_exists( 'fusion_is_preview_frame' ) && fusion_is_preview_frame() ) || ( function_exists( 'fusion_is_builder_frame' ) && fusion_is_builder_frame() );
	$to_link    = '';

	if ( $is_builder ) {
		$to_link = '<span class="fusion-panel-shortcut" data-fusion-option="headers_typography_important_note_info">' . esc_html__( 'Global Options Heading Settings', 'fusion-builder' ) . '</span>';
	} else {
		$to_link = '<a href="' . esc_url( $fusion_settings->get_setting_link( 'headers_typography_important_note_info' ) ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Global Options Heading Settings', 'fusion-builder' ) . '</a>';
	}

	fusion_builder_map(
		fusion_builder_frontend_data(
			'FusionSC_Title',
			[
				'name'            => esc_attr__( 'Title', 'fusion-builder' ),
				'shortcode'       => 'fusion_title',
				'icon'            => 'fusiona-H',
				'preview'         => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-title-preview.php',
				'preview_id'      => 'fusion-builder-block-module-title-preview-template',
				'allow_generator' => true,
				'inline_editor'   => true,
				'help_url'        => 'https://avada.com/documentation/title-element/',
				'subparam_map'    => [
					'fusion_font_family_title_font'  => 'main_typography',
					'fusion_font_variant_title_font' => 'main_typography',
					'font_size'                      => 'main_typography',
					'line_height'                    => 'main_typography',
					'letter_spacing'                 => 'main_typography',
					'text_transform'                 => 'main_typography',
				],
				'params'          => [
					[
						'type'        => 'radio_button_set',
						'heading'     => esc_html__( 'Title Type', 'fusion-builder' ),
						'description' => esc_html__( 'Choose the title type.', 'fusion-builder' ),
						'param_name'  => 'title_type',
						'value'       => [
							'text'          => esc_html__( 'Text', 'fusion-builder' ),
							'marquee'       => esc_html__( 'Marquee', 'fusion-builder' ),
							'rotating'      => esc_html__( 'Rotating', 'fusion-builder' ),
							'highlight'     => esc_html__( 'Highlight', 'fusion-builder' ),
							'scroll_reveal' => esc_html__( 'Scroll Reveal', 'fusion-builder' ),
						],
						'default'     => 'text',
					],
					[
						'type'        => 'select',
						'heading'     => esc_attr__( 'Scroll Reveal Effect', 'fusion-builder' ),
						'description' => esc_attr__( 'Select the style for the scroll reveal animation.', 'fusion-builder' ),
						'param_name'  => 'scroll_reveal_effect',
						'default'     => 'color_change',
						'value'       => [
							'color_change'    => esc_attr__( 'Color Change', 'fusion-builder' ),
							'unblur'          => esc_attr__( 'Unblur', 'fusion-builder' ),
							'random_assemble' => esc_attr__( 'Random Assemble', 'fusion-builder' ),
							'slide_up'        => esc_attr__( 'Slide Up', 'fusion-builder' ),
							'slide_down'      => esc_attr__( 'Slide Down', 'fusion-builder' ),
							'turn'            => esc_attr__( 'Turn', 'fusion-builder' ),
							'rotate'          => esc_attr__( 'Rotate', 'fusion-builder' ),
							'scale_up'        => esc_attr__( 'Scale Up', 'fusion-builder' ),
						],
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'scroll_reveal',
								'operator' => '==',
							],
						],
					],
					[
						'type'        => 'radio_button_set',
						'heading'     => esc_html__( 'Scroll Reveal Basis', 'fusion-builder' ),
						'description' => esc_html__( 'Select the basis for the reveal effect.', 'fusion-builder' ),
						'param_name'  => 'scroll_reveal_basis',
						'default'     => 'chars',
						'value'       => [
							'lines'  => esc_html__( 'Lines', 'fusion-builder' ),
							'words'  => esc_html__( 'Words', 'fusion-builder' ),
							'chars'  => esc_html__( 'Chars', 'fusion-builder' ),
						],
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'scroll_reveal',
								'operator' => '==',
							],
							[
								'element'  => 'scroll_reveal_effect',
								'value'    => 'random_assemble',
								'operator' => '!=',
							],
							[
								'element'  => 'scroll_reveal_effect',
								'value'    => 'rotate',
								'operator' => '!=',
							],
							[
								'element'  => 'scroll_reveal_effect',
								'value'    => 'scale_up',
								'operator' => '!=',
							],
						],
					],
					[
						'type'        => 'select',
						'heading'     => esc_attr__( 'Scroll Reveal Behavior', 'fusion-builder' ),
						'description' => esc_attr__( 'Select how the scroll reveal effects behave on scroll..', 'fusion-builder' ),
						'param_name'  => 'scroll_reveal_behavior',
						'default'     => 'always',
						'value'       => [
							'always' => esc_attr__( 'Animate on down & up', 'fusion-builder' ),
							'replay' => esc_attr__( 'Animate on down', 'fusion-builder' ),
							'play'   => esc_attr__( 'Animate once', 'fusion-builder' ),

						],
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'scroll_reveal',
								'operator' => '==',
							],
						],
					],
					[
						'type'        => 'range',
						'heading'     => esc_html__( 'Scroll Reveal Duration', 'fusion-builder' ),
						'description' => esc_html__( 'Controls the duration of the scroll animation effect. In milliseconds.', 'fusion-builder' ),
						'param_name'  => 'scroll_reveal_duration',
						'value'       => '500',
						'min'         => '100',
						'max'         => '5000',
						'step'        => '100',
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'scroll_reveal',
								'operator' => '==',
							],
						],
					],
					[
						'type'        => 'range',
						'heading'     => esc_html__( 'Scroll Reveal Stagger', 'fusion-builder' ),
						'description' => esc_html__( 'Controls how the start times of the individual animations within a scroll reveal effect are staggered. In milliseconds.', 'fusion-builder' ),
						'param_name'  => 'scroll_reveal_stagger',
						'value'       => '200',
						'min'         => '10',
						'max'         => '1000',
						'step'        => '10',
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'scroll_reveal',
								'operator' => '==',
							],
							[
								'element'  => 'scroll_reveal_effect',
								'value'    => 'rotate',
								'operator' => '!=',
							],
						],
					],
					[
						'type'        => 'range',
						'heading'     => esc_html__( 'Scroll Reveal Delay', 'fusion-builder' ),
						'description' => esc_html__( 'Controls the delay for the start of the scroll effect. In milliseconds.', 'fusion-builder' ),
						'param_name'  => 'scroll_reveal_delay',
						'value'       => '0',
						'min'         => '100',
						'max'         => '2500',
						'step'        => '100',
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'scroll_reveal',
								'operator' => '==',
							],
						],
					],
					[
						'type'        => 'radio_button_set',
						'heading'     => esc_html__( 'Enable Scroll Reveal Above The Fold', 'fusion-builder' ),
						'description' => esc_html__( 'Set to "yes" to enable the scroll reveal effect even if the title is above the fold. This can lead to a flash of unstyled content.', 'fusion-builder' ),
						'param_name'  => 'scroll_reveal_above_fold',
						'default'     => 'yes',
						'value'       => [
							'yes' => esc_html__( 'Yes', 'fusion-builder' ),
							'no'  => esc_html__( 'No', 'fusion-builder' ),
						],
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'scroll_reveal',
								'operator' => '==',
							],
						],
					],
					[
						'type'        => 'radio_button_set',
						'heading'     => esc_html__( 'Marquee Direction', 'fusion-builder' ),
						'description' => esc_html__( 'Select the marquee direction.', 'fusion-builder' ),
						'param_name'  => 'marquee_direction',
						'default'     => 'left',
						'value'       => [
							'left'  => esc_html__( 'Left', 'fusion-builder' ),
							'right' => esc_html__( 'Right', 'fusion-builder' ),
						],
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'marquee',
								'operator' => '==',
							],
						],
					],
					[
						'type'        => 'radio_button_set',
						'heading'     => esc_html__( 'Mask Edges', 'fusion-builder' ),
						'description' => esc_html__( 'Choose if the edges of the marquee should be masked with a fade out effect.', 'fusion-builder' ),
						'param_name'  => 'marquee_mask_edges',
						'default'     => 'no',
						'value'       => [
							'yes' => esc_html__( 'Yes', 'fusion-builder' ),
							'no'  => esc_html__( 'No', 'fusion-builder' ),
						],
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'marquee',
								'operator' => '==',
							],
						],
					],					
					[
						'type'        => 'range',
						'heading'     => esc_html__( 'Marquee Speed', 'fusion-builder' ),
						'description' => esc_html__( 'Controls the speed of the marquee effect. In milliseconds, 1000 = 1 second.', 'fusion-builder' ),
						'param_name'  => 'marquee_speed',
						'value'       => '15000',
						'min'         => '0',
						'max'         => '50000',
						'step'        => '250',
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'marquee',
								'operator' => '==',
							],
						],
					],
					[
						'type'        => 'select',
						'heading'     => esc_attr__( 'Rotation Effect', 'fusion-builder' ),
						'description' => esc_attr__( 'Select the style for rotation text.', 'fusion-builder' ),
						'param_name'  => 'rotation_effect',
						'default'     => 'bounceIn',
						'value'       => [
							'bounceIn'     => esc_attr__( 'Bounce', 'fusion-builder' ),
							'clipIn'       => esc_attr__( 'Clip', 'fusion-builder' ),
							'fadeIn'       => esc_attr__( 'Fade', 'fusion-builder' ),
							'flipInX'      => esc_attr__( 'Flip', 'fusion-builder' ),
							'lightSpeedIn' => esc_attr__( 'Light Speed', 'fusion-builder' ),
							'rollIn'       => esc_attr__( 'Roll', 'fusion-builder' ),
							'typeIn'       => esc_attr__( 'Typing', 'fusion-builder' ),
							'slideInDown'  => esc_attr__( 'Slide Down', 'fusion-builder' ),
							'zoomIn'       => esc_attr__( 'Zoom', 'fusion-builder' ),
						],
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'rotating',
								'operator' => '==',
							],
						],
					],
					[
						'type'        => 'range',
						'heading'     => esc_attr__( 'Display Time', 'fusion-builder' ),
						'description' => esc_attr__( 'Controls the animation delay between each text in a set. In milliseconds.', 'fusion-builder' ),
						'param_name'  => 'display_time',
						'value'       => '1200',
						'min'         => '0',
						'max'         => '10000',
						'step'        => '100',
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'rotating',
								'operator' => '==',
							],
						],
					],
					[
						'type'        => 'select',
						'heading'     => esc_attr__( 'Highlight Effect', 'fusion-builder' ),
						'description' => esc_attr__( 'Select the highlight effect.', 'fusion-builder' ),
						'param_name'  => 'highlight_effect',
						'default'     => 'circle',
						'value'       => [
							'circle'               => esc_attr__( 'Circle', 'fusion-builder' ),
							'curly'                => esc_attr__( 'Curly', 'fusion-builder' ),
							'marker'               => esc_attr__( 'Marker', 'fusion-builder' ),
							'underline'            => esc_attr__( 'Underline', 'fusion-builder' ),
							'double'               => esc_attr__( 'Double', 'fusion-builder' ),
							'double_underline'     => esc_attr__( 'Double Underline', 'fusion-builder' ),
							'underline_zigzag'     => esc_attr__( 'Underline Zigzag', 'fusion-builder' ),
							'diagonal_bottom_left' => esc_attr__( 'Diagonal Bottom Left', 'fusion-builder' ),
							'diagonal_top_left'    => esc_attr__( 'Diagonal Top Left', 'fusion-builder' ),
							'strikethrough'        => esc_attr__( 'Strikethrough', 'fusion-builder' ),
							'x'                    => esc_attr__( 'X', 'fusion-builder' ),
						],
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'highlight',
								'operator' => '==',
							],
						],
					],
					[
						'type'        => 'radio_button_set',
						'heading'     => esc_attr__( 'Animation Mode', 'fusion-builder' ),
						'description' => esc_attr__( 'Controls if and how the text should be animated. Rotating text will either run once or loop.', 'fusion-builder' ),
						'param_name'  => 'loop_animation',
						'default'     => 'once',
						'value'       => [
							'loop' => esc_html__( 'Loop', 'fusion-builder' ),
							'once' => esc_html__( 'Once', 'fusion-builder' ),
							'none' => esc_html__( 'None', 'fusion-builder' ),
						],
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'text',
								'operator' => '!=',
							],
							[
								'element'  => 'title_type',
								'value'    => 'marquee',
								'operator' => '!=',
							],
							[
								'element'  => 'title_type',
								'value'    => 'scroll_reveal',
								'operator' => '!=',
							],
						],
					],
					[
						'type'        => 'range',
						'heading'     => esc_attr__( 'Highlight Animation Duration', 'fusion-builder' ),
						'description' => esc_attr__( 'Controls the duration of the highlight animation. In milliseconds.', 'fusion-builder' ),
						'param_name'  => 'highlight_animation_duration',
						'value'       => '1500',
						'min'         => '250',
						'max'         => '5000',
						'step'        => '50',
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'highlight',
								'operator' => '==',
							],
							[
								'element'  => 'loop_animation',
								'value'    => 'none',
								'operator' => '!=',
							],
						],
					],
					[
						'type'        => 'range',
						'heading'     => esc_attr__( 'Highlight Shape Thickness', 'fusion-builder' ),
						'description' => esc_attr__( 'Controls the thickness of highlight shape.', 'fusion-builder' ),
						'param_name'  => 'highlight_width',
						'value'       => '9',
						'min'         => '1',
						'max'         => '100',
						'step'        => '1',
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'highlight',
								'operator' => '==',
							],
						],
					],
					[
						'type'        => 'radio_button_set',
						'heading'     => esc_attr__( 'Highlight Smudge Effect', 'fusion-builder' ),
						'description' => esc_attr__( 'Choose if the highlight shape should have a slightly smudged gradient effect.', 'fusion-builder' ),
						'param_name'  => 'highlight_smudge_effect',
						'value'       => [
							'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
							'no'  => esc_attr__( 'No', 'fusion-builder' ),
						],
						'default'     => 'no',
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'highlight',
								'operator' => '==',
							],
						],
					],
					[
						'type'        => 'range',
						'heading'     => esc_attr__( 'Highlight Top Margin', 'fusion-builder' ),
						'description' => esc_attr__( 'Controls the top margin of highlight shape.', 'fusion-builder' ),
						'param_name'  => 'highlight_top_margin',
						'value'       => '0',
						'min'         => '-50',
						'max'         => '50',
						'step'        => '1',
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'highlight',
								'operator' => '==',
							],
							[
								'element'  => 'highlight_effect',
								'value'    => 'circle',
								'operator' => '!=',
							],
							[
								'element'  => 'highlight_effect',
								'value'    => 'marker',
								'operator' => '!=',
							],
							[
								'element'  => 'highlight_effect',
								'value'    => 'double',
								'operator' => '!=',
							],
							[
								'element'  => 'highlight_effect',
								'value'    => 'diagonal_bottom_left',
								'operator' => '!=',
							],
							[
								'element'  => 'highlight_effect',
								'value'    => 'diagonal_top_left',
								'operator' => '!=',
							],
							[
								'element'  => 'highlight_effect',
								'value'    => 'strikethrough',
								'operator' => '!=',
							],
							[
								'element'  => 'highlight_effect',
								'value'    => 'x',
								'operator' => '!=',
							],
						],
					],
					[
						'type'         => 'textfield',
						'heading'      => esc_attr__( 'Before Text', 'fusion-builder' ),
						'description'  => esc_html__( 'Enter before text.', 'fusion-builder' ),
						'param_name'   => 'before_text',
						'value'        => '',
						'group'        => esc_attr__( 'General', 'fusion-builder' ),
						'dynamic_data' => true,
						'dependency'   => [
							[
								'element'  => 'title_type',
								'value'    => 'text',
								'operator' => '!=',
							],
							[
								'element'  => 'title_type',
								'value'    => 'marquee',
								'operator' => '!=',
							],
							[
								'element'  => 'title_type',
								'value'    => 'scroll_reveal',
								'operator' => '!=',
							],
						],
					],
					[
						'type'        => 'sortable_text',
						'heading'     => esc_attr__( 'Rotation Text', 'fusion-builder' ),
						'description' => esc_attr__( 'Enter text for rotation.', 'fusion-builder' ),
						'param_name'  => 'rotation_text',
						'placeholder' => 'Text',
						'add_label'   => 'Add Rotation Text',
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'rotating',
								'operator' => '==',
							],
						],
					],
					[
						'type'         => 'textfield',
						'heading'      => esc_attr__( 'Highlighted Text', 'fusion-builder' ),
						'description'  => esc_html__( 'Enter text which should be highlighted.', 'fusion-builder' ),
						'param_name'   => 'highlight_text',
						'value'        => '',
						'dynamic_data' => true,
						'dependency'   => [
							[
								'element'  => 'title_type',
								'value'    => 'highlight',
								'operator' => '==',
							],
						],
					],
					[
						'type'         => 'textfield',
						'heading'      => esc_attr__( 'After Text', 'fusion-builder' ),
						'description'  => esc_html__( 'Enter after text.', 'fusion-builder' ),
						'param_name'   => 'after_text',
						'value'        => '',
						'dynamic_data' => true,
						'dependency'   => [
							[
								'element'  => 'title_type',
								'value'    => 'text',
								'operator' => '!=',
							],
							[
								'element'  => 'title_type',
								'value'    => 'marquee',
								'operator' => '!=',
							],
							[
								'element'  => 'title_type',
								'value'    => 'scroll_reveal',
								'operator' => '!=',
							],
						],
					],
					[
						'type'         => 'tinymce',
						'heading'      => esc_attr__( 'Title', 'fusion-builder' ),
						'description'  => esc_attr__( 'Insert the title text.', 'fusion-builder' ),
						'param_name'   => 'element_content',
						'value'        => esc_attr__( 'Your Content Goes Here', 'fusion-builder' ),
						'placeholder'  => true,
						'dynamic_data' => true,
						'dependency'   => [
							[
								'element'  => 'title_type',
								'value'    => 'rotating',
								'operator' => '!=',
							],
							[
								'element'  => 'title_type',
								'value'    => 'highlight',
								'operator' => '!=',
							],
						],
					],
					[
						'type'        => 'radio_button_set',
						'heading'     => esc_attr__( 'Title Link', 'fusion-builder' ),
						'description' => esc_attr__( 'Choose if title text should have a link.', 'fusion-builder' ),
						'param_name'  => 'title_link',
						'value'       => [
							'on'  => esc_attr__( 'On', 'fusion-builder' ),
							'off' => esc_attr__( 'Off', 'fusion-builder' ),
						],
						'default'     => 'off',
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'rotating',
								'operator' => '!=',
							],
							[
								'element'  => 'title_type',
								'value'    => 'highlight',
								'operator' => '!=',
							],
						],
					],
					[
						'type'         => 'link_selector',
						'heading'      => esc_attr__( 'Link URL', 'fusion-builder' ),
						'description'  => esc_attr__( 'Add an URL for the link. E.g: https://example.com.', 'fusion-builder' ),
						'param_name'   => 'link_url',
						'value'        => '',
						'dynamic_data' => true,
						'dependency'   => [
							[
								'element'  => 'title_link',
								'value'    => 'off',
								'operator' => '!=',
							],
							[
								'element'  => 'title_type',
								'value'    => 'rotating',
								'operator' => '!=',
							],
							[
								'element'  => 'title_type',
								'value'    => 'highlight',
								'operator' => '!=',
							],
						],
					],
					[
						'type'        => 'radio_button_set',
						'heading'     => esc_attr__( 'Link Target', 'fusion-builder' ),
						'description' => esc_html__( 'Controls how the link will open.', 'fusion-builder' ),
						'param_name'  => 'link_target',
						'value'       => [
							'_self'  => esc_html__( 'Same Window/Tab', 'fusion-builder' ),
							'_blank' => esc_html__( 'New Window/Tab', 'fusion-builder' ),
						],
						'default'     => '_self',
						'dependency'  => [
							[
								'element'  => 'title_link',
								'value'    => 'off',
								'operator' => '!=',
							],
							[
								'element'  => 'link_url',
								'value'    => '',
								'operator' => '!=',
							],
							[
								'element'  => 'title_type',
								'value'    => 'rotating',
								'operator' => '!=',
							],
							[
								'element'  => 'title_type',
								'value'    => 'highlight',
								'operator' => '!=',
							],
						],
					],
					[
						'type'        => 'radio_button_set',
						'heading'     => esc_attr__( 'Alignment', 'fusion-builder' ),
						'description' => esc_attr__( 'Choose to align the heading left, right or center.', 'fusion-builder' ),
						'param_name'  => 'content_align',
						'responsive'  => [
							'state'         => 'large',
							'default_value' => true,
						],
						'value'       => [
							'left'   => esc_attr__( 'Left', 'fusion-builder' ),
							'center' => esc_attr__( 'Center', 'fusion-builder' ),
							'right'  => esc_attr__( 'Right', 'fusion-builder' ),
						],
						'default'     => 'left',
						'group'       => esc_attr__( 'Design', 'fusion-builder' ),
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'marquee',
								'operator' => '!=',
							],
						],
					],
					[
						'type'        => 'radio_button_set',
						'heading'     => esc_attr__( 'HTML Heading Tag', 'fusion-builder' ),
						'description' => esc_attr__( 'Choose HTML tag of the heading, either div, p or the heading tag, h1-h6.', 'fusion-builder' ),
						'param_name'  => 'size',
						'value'       => [
							'1'   => 'H1',
							'2'   => 'H2',
							'3'   => 'H3',
							'4'   => 'H4',
							'5'   => 'H5',
							'6'   => 'H6',
							'div' => 'DIV',
							'p'   => 'P',
						],
						'default'     => '1',
						'group'       => esc_attr__( 'Design', 'fusion-builder' ),
					],
					[
						'type'        => 'textfield',
						'heading'     => esc_attr__( 'Animated Text Font Size', 'fusion-builder' ),
						/* translators: URL for the link. */
						'description' => sprintf( esc_html__( 'Controls the font size of the animated text. Enter value including any valid CSS unit, ex: 20px. Leave empty if the global font size for the corresponding heading size (h1-h6) should be used: %s.', 'fusion-builder' ), $to_link ),
						'param_name'  => 'animated_font_size',
						'value'       => '',
						'group'       => esc_attr__( 'Design', 'fusion-builder' ),
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'text',
								'operator' => '!=',
							],
							[
								'element'  => 'title_type',
								'value'    => 'marquee',
								'operator' => '!=',
							],
							[
								'element'  => 'title_type',
								'value'    => 'scroll_reveal',
								'operator' => '!=',
							],
						],
					],
					[
						'type'             => 'typography',
						'remove_from_atts' => true,
						'global'           => true,
						'heading'          => esc_attr__( 'Typography', 'fusion-builder' ),
						/* translators: URL for the link. */
						'description'      => sprintf( esc_html__( 'Controls the title text typography.  Leave empty if the global typography for the corresponding heading size (h1-h6) should be used: %s.', 'fusion-builder' ), $to_link ),
						'param_name'       => 'main_typography',
						'group'            => esc_attr__( 'Design', 'fusion-builder' ),
						'choices'          => [
							'font-family'    => 'title_font',
							'font-size'      => 'font_size',
							'line-height'    => 'line_height',
							'letter-spacing' => 'letter_spacing',
							'text-transform' => 'text_transform',
						],
						'default'          => [
							'font-family'    => '',
							'variant'        => '',
							'font-size'      => '',
							'line-height'    => '',
							'letter-spacing' => '',
							'text-transform' => '',
						],
					],
					[
						'type'        => 'colorpickeralpha',
						'heading'     => esc_attr__( 'Font Color', 'fusion-builder' ),
						/* translators: URL for the link. */
						'description' => sprintf( esc_html__( 'Controls the color of the title, ex: #000. Leave empty if the global color for the corresponding heading size (h1-h6) should be used: %s.', 'fusion-builder' ), $to_link ),
						'param_name'  => 'text_color',
						'value'       => '',
						'group'       => esc_attr__( 'Design', 'fusion-builder' ),
					],
					[
						'type'        => 'colorpickeralpha',
						'heading'     => esc_attr__( 'Animated Text Font Color', 'fusion-builder' ),
						/* translators: URL for the link. */
						'description' => sprintf( esc_html__( 'Controls the color of the animated title, ex: #000. Leave empty if the global color for the corresponding heading size (h1-h6) should be used: %s.', 'fusion-builder' ), $to_link ),
						'param_name'  => 'animated_text_color',
						'value'       => '',
						'group'       => esc_attr__( 'Design', 'fusion-builder' ),
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'text',
								'operator' => '!=',
							],
							[
								'element'  => 'title_type',
								'value'    => 'marquee',
								'operator' => '!=',
							],
						],
					],
					[
						'type'        => 'colorpickeralpha',
						'heading'     => esc_attr__( 'Highlight Shape Color', 'fusion-builder' ),
						'description' => esc_html__( 'Controls the color of the highlight shape, ex: #000.', 'fusion-builder' ),
						'param_name'  => 'highlight_color',
						'value'       => '',
						'default'     => 'var(--primary_color)',
						'group'       => esc_attr__( 'Design', 'fusion-builder' ),
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'highlight',
								'operator' => '==',
							],
						],
					],
					'fusion_text_shadow_placeholder'       => [],
					[
						'type'        => 'radio_button_set',
						'heading'     => esc_attr__( 'Text Stroke', 'fusion-builder' ),
						'description' => esc_attr__( 'Set to "Yes" to enable text stroke.', 'fusion-builder' ),
						'param_name'  => 'text_stroke',
						'default'     => 'no',
						'group'       => esc_html__( 'Design', 'fusion-builder' ),
						'value'       => [
							'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
							'no'  => esc_attr__( 'No', 'fusion-builder' ),
						],
					],
					[
						'type'        => 'range',
						'heading'     => esc_attr__( 'Text Stroke Size', 'fusion-builder' ),
						'description' => esc_attr__( 'Set text stroke size. In pixels.', 'fusion-builder' ),
						'param_name'  => 'text_stroke_size',
						'value'       => '1',
						'min'         => '0',
						'max'         => '10',
						'step'        => '1',
						'group'       => esc_html__( 'Design', 'fusion-builder' ),
						'dependency'  => [
							[
								'element'  => 'text_stroke',
								'value'    => 'yes',
								'operator' => '==',
							],
						],
					],
					[
						'type'        => 'colorpickeralpha',
						'heading'     => esc_attr__( 'Text Stroke Color', 'fusion-builder' ),
						'description' => esc_attr__( 'Controls the color of the text stroke.', 'fusion-builder' ),
						'param_name'  => 'text_stroke_color',
						'value'       => '',
						'default'     => 'var(--primary_color)',
						'group'       => esc_html__( 'Design', 'fusion-builder' ),
						'dependency'  => [
							[
								'element'  => 'text_stroke',
								'value'    => 'yes',
								'operator' => '==',
							],
						],
					],
					[
						'type'        => 'radio_button_set',
						'heading'     => esc_attr__( 'Text Overflow', 'fusion-builder' ),
						'description' => esc_attr__( 'Controls the text overflow for longer texts.', 'fusion-builder' ),
						'param_name'  => 'text_overflow',
						'default'     => 'none',
						'group'       => esc_html__( 'Design', 'fusion-builder' ),
						'value'       => [
							'none'     => esc_attr__( 'Default', 'fusion-builder' ),
							'ellipsis' => esc_attr__( 'Ellipsis', 'fusion-builder' ),
							'clip'     => esc_attr__( 'Clip', 'fusion-builder' ),
						],
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'text',
								'operator' => '==',
							],
						],
					],
					'fusion_margin_placeholder'            => [
						'param_name' => 'dimensions',
						'value'      => [
							'margin_top'    => '',
							'margin_right'  => '',
							'margin_bottom' => '',
							'margin_left'   => '',
						],
						'responsive' => [
							'state' => 'large',
						],
					],
					'fusion_margin_mobile_placeholder'     => [
						'param_name'  => 'margin_mobile',
						'heading'     => esc_attr__( 'Mobile Margin', 'fusion-builder' ),
						'description' => esc_attr__( 'Spacing above and below the title on mobiles. In px, em or %, e.g. 10px.', 'fusion-builder' ),
						'value'       => [
							'margin_top_mobile'    => '',
							'margin_bottom_mobile' => '',
						],
						'dependency'  => [
							[
								'element'  => 'fusion_builder_container',
								'param'    => 'type',
								'value'    => 'flex',
								'operator' => '!=',
							],
						],
					],
					'fusion_gradient_text_placeholder'     => [
						'selector'   => '.fusion-title',
						'dependency' => [
							[
								'element'  => 'title_type',
								'value'    => 'rotating',
								'operator' => '!=',
							],
							[
								'element'  => 'title_type',
								'value'    => 'highlight',
								'operator' => '!=',
							],
							[
								'element'  => 'title_type',
								'value'    => 'scroll_reveal',
								'operator' => '!=',
							],							
						],
					],
					[
						'type'        => 'select',
						'heading'     => esc_attr__( 'Separator', 'fusion-builder' ),
						'description' => esc_attr__( 'Choose the kind of the title separator you want to use.', 'fusion-builder' ),
						'param_name'  => 'style_type',
						'value'       => [
							'default'          => esc_attr__( 'Default', 'fusion-builder' ),
							'single solid'     => esc_attr__( 'Single Solid', 'fusion-builder' ),
							'single dashed'    => esc_attr__( 'Single Dashed', 'fusion-builder' ),
							'single dotted'    => esc_attr__( 'Single Dotted', 'fusion-builder' ),
							'double solid'     => esc_attr__( 'Double Solid', 'fusion-builder' ),
							'double dashed'    => esc_attr__( 'Double Dashed', 'fusion-builder' ),
							'double dotted'    => esc_attr__( 'Double Dotted', 'fusion-builder' ),
							'underline solid'  => esc_attr__( 'Underline Solid', 'fusion-builder' ),
							'underline dashed' => esc_attr__( 'Underline Dashed', 'fusion-builder' ),
							'underline dotted' => esc_attr__( 'Underline Dotted', 'fusion-builder' ),
							'none'             => esc_attr__( 'None', 'fusion-builder' ),
						],
						'default'     => 'default',
						'group'       => esc_attr__( 'Design', 'fusion-builder' ),
						'dependency'  => [
							[
								'element'  => 'title_type',
								'value'    => 'text',
								'operator' => '==',
							],
						],
					],
					[
						'type'        => 'colorpickeralpha',
						'heading'     => esc_attr__( 'Separator Color', 'fusion-builder' ),
						'param_name'  => 'sep_color',
						'value'       => '',
						'description' => esc_attr__( 'Controls the separator color. ', 'fusion-builder' ),
						'group'       => esc_attr__( 'Design', 'fusion-builder' ),
						'dependency'  => [
							[
								'element'  => 'style_type',
								'value'    => 'none',
								'operator' => '!=',
							],
							[
								'element'  => 'title_type',
								'value'    => 'text',
								'operator' => '==',
							],
						],
						'default'     => $fusion_settings->get( 'title_border_color' ),
					],
					[
						'type'        => 'colorpickeralpha',
						'heading'     => esc_attr__( 'Link Color', 'fusion-builder' ),
						'param_name'  => 'link_color',
						'value'       => '',
						'description' => esc_attr__( 'Controls the link color.', 'fusion-builder' ),
						'group'       => esc_attr__( 'Design', 'fusion-builder' ),
						'dependency'  => [
							[
								'element'  => 'title_link',
								'value'    => 'off',
								'operator' => '!=',
							],
							[
								'element'  => 'title_type',
								'value'    => 'rotating',
								'operator' => '!=',
							],
							[
								'element'  => 'title_type',
								'value'    => 'highlight',
								'operator' => '!=',
							],
						],
						'default'     => $fusion_settings->get( 'link_color' ),
					],
					[
						'type'        => 'colorpickeralpha',
						'heading'     => esc_attr__( 'Link Hover Color', 'fusion-builder' ),
						'param_name'  => 'link_hover_color',
						'value'       => '',
						'description' => esc_attr__( 'Controls the link hover color.', 'fusion-builder' ),
						'group'       => esc_attr__( 'Design', 'fusion-builder' ),
						'dependency'  => [
							[
								'element'  => 'title_link',
								'value'    => 'off',
								'operator' => '!=',
							],
							[
								'element'  => 'title_type',
								'value'    => 'rotating',
								'operator' => '!=',
							],
							[
								'element'  => 'title_type',
								'value'    => 'highlight',
								'operator' => '!=',
							],
						],
						'default'     => $fusion_settings->get( 'link_hover_color' ),
					],
					'fusion_animation_placeholder'         => [
						'preview_selector' => '.fusion-title',
					],
					[
						'type'        => 'checkbox_button_set',
						'heading'     => esc_attr__( 'Element Visibility', 'fusion-builder' ),
						'param_name'  => 'hide_on_mobile',
						'value'       => fusion_builder_visibility_options( 'full' ),
						'default'     => fusion_builder_default_visibility( 'array' ),
						'description' => esc_attr__( 'Choose to show or hide the element on small, medium or large screens. You can choose more than one at a time.', 'fusion-builder' ),
					],
					'fusion_sticky_visibility_placeholder' => [],
					[
						'type'        => 'textfield',
						'heading'     => esc_attr__( 'CSS Class', 'fusion-builder' ),
						'param_name'  => 'class',
						'value'       => '',
						'description' => esc_attr__( 'Add a class to the wrapping HTML element.', 'fusion-builder' ),
					],
					[
						'type'        => 'textfield',
						'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
						'param_name'  => 'id',
						'value'       => '',
						'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
					],
				],
			]
		)
	);
}
add_action( 'fusion_builder_before_init', 'fusion_element_title' );
