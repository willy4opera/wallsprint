<?php
/**
 * The template for displaying the styled variations
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-variations.php
 *
 * NOTE that we may need to update template files and you
 * (the plugin or theme developer) will need to copy the new files
 * to your theme or plugin to maintain compatibility.
 *
 * @author  ThemeComplete
 * @package Extra Product Options/Templates
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $tm_product_id ) ) {
	$tm_product_id = 0;
}
$tm_product = false;
// Ensure product is valid and that variations are customized.
if ( ! empty( $tm_product_id ) ) {
	$tm_product = wc_get_product( $tm_product_id );
}

if (
	! empty( $tm_product ) &&
	is_object( $tm_product ) &&
	is_callable( [ $tm_product, 'get_available_variations' ] )
	&& ! empty( $builder )
	&& isset( $builder['variations_options'], $form_prefix, $args, $field_counter, $epo_template_path, $epo_default_path, $variations_builder_element_end_args )
) {
	$variations_options = $builder['variations_options'];
	$parse              = false;
	if ( isset( $variations_builder_element_start_args ) && isset( $variations_builder_element_start_args['tm_element_settings'] ) && isset( $variations_builder_element_start_args['tm_element_settings']['builder'] ) && isset( $variations_builder_element_start_args['tm_element_settings']['builder']['variations_disabled'] ) && $variations_builder_element_start_args['tm_element_settings']['builder']['variations_disabled'] ) {
		$get_variations       = count( $tm_product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $tm_product );
		$available_variations = $get_variations ? $tm_product->get_available_variations() : false;
		$parse                = true;
	} else {
		$available_variations = $tm_product->get_available_variations();
		$parse                = ! empty( $available_variations );
	}

	$attributes     = $tm_product->get_variation_attributes(); // @phpstan-ignore-line
	$all_attributes = $tm_product->get_attributes();
	if ( $attributes ) {
		foreach ( $attributes as $key => $value ) {
			if ( ! $value ) {
				$attributes[ $key ] = array_map( 'trim', explode( '|', $all_attributes[ $key ]['value'] ) );
			}
		}
	}
	$selected_attributes = $tm_product->get_default_attributes();

	$variations_builder_element_start_args_class = '';
	if ( isset( $variations_builder_element_start_args['class'] ) ) {
		$variations_builder_element_start_args_class = $variations_builder_element_start_args['class'];
	}
	if ( $parse ) {

		$loop = 0;

		foreach ( $attributes as $name => $options ) {

			++$loop;
			// name - wc_attribute_label( $name )
			// id - sanitize_title($name)
			// select box name 'attribute_'.sanitize_title( $name )
			// select box id sanitize_title( $name ).

			$att_id = sanitize_title( $name );
			if ( is_array( $options ) ) {

				$variations_display_as = 'select';
				if ( isset( $variations_options[ $att_id ] ) && ! empty( $variations_options[ $att_id ]['variations_display_as'] ) ) {
					$variations_display_as = $variations_options[ $att_id ]['variations_display_as'];
				}

				$options_array          = [];
				$original_options_array = [];
				$default_value          = '';
				$imagesp                = [];
				$images                 = [];
				$color                  = [];
				$changes_product_image  = '';
				if ( isset( $variations_options[ $att_id ] ) && ! empty( $variations_options[ $att_id ]['variations_changes_product_image'] ) ) {
					$changes_product_image = $variations_options[ $att_id ]['variations_changes_product_image'];
				}

				$variations_label = '';
				if ( isset( $variations_options[ $att_id ] ) && ! empty( $variations_options[ $att_id ]['variations_label'] ) ) {
					$variations_label = $variations_options[ $att_id ]['variations_label'];
				}
				$variations_class = '';
				if ( isset( $variations_options[ $att_id ] ) && ! empty( $variations_options[ $att_id ]['variations_class'] ) ) {
					$variations_class = $variations_options[ $att_id ]['variations_class'];
				}
				$variations_items_per_row = '';
				if ( isset( $variations_options[ $att_id ] ) && ! empty( $variations_options[ $att_id ]['variations_items_per_row'] ) ) {
					$variations_items_per_row = $variations_options[ $att_id ]['variations_items_per_row'];
				}

				$variations_items_per_row_tablets = '';
				if ( isset( $variations_options[ $att_id ] ) && ! empty( $variations_options[ $att_id ]['variations_items_per_row_tablets'] ) ) {
					$variations_items_per_row_tablets = $variations_options[ $att_id ]['variations_items_per_row_tablets'];
				}
				$variations_items_per_row_tablets_small = '';
				if ( isset( $variations_options[ $att_id ] ) && ! empty( $variations_options[ $att_id ]['variations_items_per_row_tablets_small'] ) ) {
					$variations_items_per_row_tablets_small = $variations_options[ $att_id ]['variations_items_per_row_tablets_small'];
				}
				$variations_items_per_row_smartphones = '';
				if ( isset( $variations_options[ $att_id ] ) && ! empty( $variations_options[ $att_id ]['variations_items_per_row_smartphones'] ) ) {
					$variations_items_per_row_smartphones = $variations_options[ $att_id ]['variations_items_per_row_smartphones'];
				}
				$variations_items_per_row_iphone5 = '';
				if ( isset( $variations_options[ $att_id ] ) && ! empty( $variations_options[ $att_id ]['variations_items_per_row_iphone5'] ) ) {
					$variations_items_per_row_iphone5 = $variations_options[ $att_id ]['variations_items_per_row_iphone5'];
				}
				$variations_items_per_row_iphone6 = '';
				if ( isset( $variations_options[ $att_id ] ) && ! empty( $variations_options[ $att_id ]['variations_items_per_row_iphone6'] ) ) {
					$variations_items_per_row_iphone6 = $variations_options[ $att_id ]['variations_items_per_row_iphone6'];
				}
				$variations_items_per_row_iphone6_plus = '';
				if ( isset( $variations_options[ $att_id ] ) && ! empty( $variations_options[ $att_id ]['variations_items_per_row_iphone6_plus'] ) ) {
					$variations_items_per_row_iphone6_plus = $variations_options[ $att_id ]['variations_items_per_row_iphone6_plus'];
				}
				$variations_items_per_row_samsung_galaxy = '';
				if ( isset( $variations_options[ $att_id ] ) && ! empty( $variations_options[ $att_id ]['variations_items_per_row_samsung_galaxy'] ) ) {
					$variations_items_per_row_samsung_galaxy = $variations_options[ $att_id ]['variations_items_per_row_samsung_galaxy'];
				}
				$variations_items_per_row_tablets_galaxy = '';
				if ( isset( $variations_options[ $att_id ] ) && ! empty( $variations_options[ $att_id ]['variations_items_per_row_tablets_galaxy'] ) ) {
					$variations_items_per_row_tablets_galaxy = $variations_options[ $att_id ]['variations_items_per_row_tablets_galaxy'];
				}

				$variations_item_width = '';
				if ( ( 'color' === $variations_display_as || 'image' === $variations_display_as ) && isset( $variations_options[ $att_id ] ) && ! empty( $variations_options[ $att_id ]['variations_item_width'] ) ) {
					$variations_item_width = $variations_options[ $att_id ]['variations_item_width'];
				}
				$variations_item_height = '';
				if ( ( 'color' === $variations_display_as || 'image' === $variations_display_as ) && isset( $variations_options[ $att_id ] ) && ! empty( $variations_options[ $att_id ]['variations_item_height'] ) ) {
					$variations_item_height = $variations_options[ $att_id ]['variations_item_height'];
				}
				$variations_show_name = '';
				if ( isset( $variations_options[ $att_id ] ) && ! empty( $variations_options[ $att_id ]['variations_show_name'] ) ) {
					$variations_show_name = $variations_options[ $att_id ]['variations_show_name'];
				}
				$variations_show_reset_button = '';
				if ( isset( $variations_options[ $att_id ] ) && ! empty( $variations_options[ $att_id ]['variations_show_reset_button'] ) ) {
					$variations_show_reset_button = $variations_options[ $att_id ]['variations_show_reset_button'];
				}

				if ( isset( $_REQUEST[ 'attribute_' . $att_id ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$selected_value = wp_unslash( $_REQUEST[ 'attribute_' . $att_id ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				} elseif ( isset( $selected_attributes[ $att_id ] ) ) {
					$selected_value = $selected_attributes[ $att_id ];
				} else {
					$selected_value = '';
				}

				if ( isset( $_REQUEST['asscociated_name'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$asscociated_name = wp_unslash( $_REQUEST['asscociated_name'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					if ( isset( $_REQUEST[ 'asscociated_cart_data_' . $asscociated_name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$asscociated_cart_data = wp_unslash( $_REQUEST[ 'asscociated_cart_data_' . $asscociated_name ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						$selected_value        =
								is_array( $asscociated_cart_data ) && isset( $asscociated_cart_data[ $asscociated_name . '_attribute_' . sanitize_title( $name ) ] ) // @phpstan-ignore-line
									? $asscociated_cart_data[ $asscociated_name . '_attribute_' . sanitize_title( $name ) ]
									: $selected_value;
					}
					if ( '' === $selected_value && isset( $_REQUEST[ 'tm_attribute_' . $att_id . $form_prefix ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$selected_value = wp_unslash( $_REQUEST[ 'tm_attribute_' . $att_id . $form_prefix ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					}
				}

				$taxonomy_name = rawurldecode( sanitize_title( $name ) );
				// Get terms if this is a taxonomy - ordered.
				if ( taxonomy_exists( $taxonomy_name ) ) {

					if ( function_exists( 'wc_get_product_terms' ) ) {
						$terms = wc_get_product_terms( $tm_product_id, $name, [ 'fields' => 'all' ] );
					} else {
						$taxorderby = wc_attribute_orderby( $taxonomy_name );
						$term_args  = [
							'taxonomy'   => $name,
							'hide_empty' => false,
						];
						switch ( $taxorderby ) {
							case 'name':
								$term_args['orderby']    = 'name';
								$term_args['menu_order'] = false;
								break;
							case 'id':
								$term_args['orderby']    = 'id';
								$term_args['order']      = 'ASC';
								$term_args['menu_order'] = false;
								break;
							case 'menu_order':
								$term_args['menu_order'] = 'ASC';
								break;
						}

						$terms = get_terms( $term_args );
					}

					$flipped_haystack = array_flip( $options );
					$_index           = 0;
					if ( is_array( $terms ) ) {
						foreach ( $terms as $_term ) {
							$option = THEMECOMPLETE_EPO_HELPER()->sanitize_key( $_term->slug );
							if ( ! isset( $flipped_haystack[ $option ] ) ) {
								continue;
							}

							$original_options_array[ esc_attr( $option ) ] = $option;
							$options_array[ esc_attr( $option ) ]          = apply_filters( 'woocommerce_variation_option_name', $_term->name );
							if ( sanitize_title( $selected_value ) === sanitize_title( $option ) ) {
								$default_value = $_index;
							}
							if ( isset( $variations_options[ $att_id ] ) && isset( $variations_options[ $att_id ]['variations_imagep'] ) ) {
								if ( ! empty( $variations_options[ $att_id ]['variations_imagep'][ $option ] ) ) {
									$imagesp[ $_index ] = $variations_options[ $att_id ]['variations_imagep'][ $option ];
								} else {
									$ov = '';
									if ( is_array( $variations_options[ $att_id ]['variations_imagep'] ) ) {
										$ak = array_keys( $variations_options[ $att_id ]['variations_imagep'] );
										if ( isset( $ak[ $_index ] ) && ! empty( $variations_options[ $att_id ]['variations_imagep'][ $ak[ $_index ] ] ) ) {
											$ov = $variations_options[ $att_id ]['variations_imagep'][ $ak[ $_index ] ];
										}
									}
									$imagesp[ $_index ] = apply_filters( 'woocommerce_tm_epo_variation_product_image', $ov, $option, $options, $available_variations );
								}
							}
							if ( isset( $variations_options[ $att_id ] ) && isset( $variations_options[ $att_id ]['variations_image'] ) ) {
								if ( ! empty( $variations_options[ $att_id ]['variations_image'][ $option ] ) ) {
									$images[ $_index ] = $variations_options[ $att_id ]['variations_image'][ $option ];
								} else {
									$ov = '';
									if ( is_array( $variations_options[ $att_id ]['variations_image'] ) ) {
										$ak = array_keys( $variations_options[ $att_id ]['variations_image'] );
										if ( isset( $ak[ $_index ] ) && ! empty( $variations_options[ $att_id ]['variations_image'][ $ak[ $_index ] ] ) ) {
											$ov = $variations_options[ $att_id ]['variations_image'][ $ak[ $_index ] ];
										}
									}
									$images[ $_index ] = apply_filters( 'woocommerce_tm_epo_variation_image', $ov, $option, $options, $available_variations );
								}
							}
							if ( isset( $variations_options[ $att_id ] ) && isset( $variations_options[ $att_id ]['variations_color'] ) ) {
								if ( ! empty( $variations_options[ $att_id ]['variations_color'][ $option ] ) ) {
									$color[ $_index ] = $variations_options[ $att_id ]['variations_color'][ $option ];
								} else {
									$ov = '';
									if ( is_array( $variations_options[ $att_id ]['variations_color'] ) ) {
										$ak = array_keys( $variations_options[ $att_id ]['variations_color'] );
										if ( isset( $ak[ $_index ] ) && ! empty( $variations_options[ $att_id ]['variations_color'][ $ak[ $_index ] ] ) ) {
											$ov = $variations_options[ $att_id ]['variations_color'][ $ak[ $_index ] ];
										}
									}
									$color[ $_index ] = apply_filters( 'woocommerce_tm_epo_variation_color', $ov, $option, $options, $available_variations );
								}
							}

							++$_index;
						}
					}
				} else {

					$_index = 0;
					foreach ( $options as $option ) {
						$original_option = $option;
						$option          = THEMECOMPLETE_EPO_HELPER()->entity_decode( THEMECOMPLETE_EPO_HELPER()->sanitize_key( $option ) );
						if ( version_compare( WC()->version, '2.4', '<' ) ) {
							$options_array[ sanitize_title( $option ) ]          = apply_filters( 'woocommerce_variation_option_name', $original_option );
							$original_options_array[ sanitize_title( $option ) ] = $original_option;
						} else {
							$options_array[ $option ]          = apply_filters( 'woocommerce_variation_option_name', $original_option );
							$original_options_array[ $option ] = $original_option;
						}

						if ( sanitize_title( $selected_value ) === sanitize_title( $option ) ) {
							$default_value = $_index;
						}
						if ( isset( $variations_options[ $att_id ] ) && isset( $variations_options[ $att_id ]['variations_imagep'] ) ) {
							if ( ! empty( $variations_options[ $att_id ]['variations_imagep'][ $option ] ) ) {
								$imagesp[ $_index ] = $variations_options[ $att_id ]['variations_imagep'][ $option ];
							} else {
								$ov = '';
								if ( is_array( $variations_options[ $att_id ]['variations_imagep'] ) ) {
									$ak = array_keys( $variations_options[ $att_id ]['variations_imagep'] );
									if ( isset( $ak[ $_index ] ) && ! empty( $variations_options[ $att_id ]['variations_imagep'][ $ak[ $_index ] ] ) ) {
										$ov = $variations_options[ $att_id ]['variations_imagep'][ $ak[ $_index ] ];
									}
								}
								$imagesp[ $_index ] = apply_filters( 'woocommerce_tm_epo_variation_product_image', $ov, $option, $options, $available_variations );
							}
						}
						if ( isset( $variations_options[ $att_id ] ) && isset( $variations_options[ $att_id ]['variations_image'] ) ) {
							if ( ! empty( $variations_options[ $att_id ]['variations_image'][ $option ] ) ) {
								$images[ $_index ] = $variations_options[ $att_id ]['variations_image'][ $option ];
							} else {
								$ov = '';
								if ( is_array( $variations_options[ $att_id ]['variations_image'] ) ) {
									$ak = array_keys( $variations_options[ $att_id ]['variations_image'] );
									if ( isset( $ak[ $_index ] ) && ! empty( $variations_options[ $att_id ]['variations_image'][ $ak[ $_index ] ] ) ) {
										$ov = $variations_options[ $att_id ]['variations_image'][ $ak[ $_index ] ];
									}
								}
								$images[ $_index ] = apply_filters( 'woocommerce_tm_epo_variation_image', $ov, $option, $options, $available_variations );
							}
						}
						if ( isset( $variations_options[ $att_id ] ) && isset( $variations_options[ $att_id ]['variations_color'] ) ) {
							if ( ! empty( $variations_options[ $att_id ]['variations_color'][ $option ] ) ) {
								$color[ $_index ] = $variations_options[ $att_id ]['variations_color'][ $option ];
							} else {
								$ov = '';
								if ( is_array( $variations_options[ $att_id ]['variations_color'] ) ) {
									$ak = array_keys( $variations_options[ $att_id ]['variations_color'] );
									if ( isset( $ak[ $_index ] ) && ! empty( $variations_options[ $att_id ]['variations_color'][ $ak[ $_index ] ] ) ) {
										$ov = $variations_options[ $att_id ]['variations_color'][ $ak[ $_index ] ];
									}
								}
								$color[ $_index ] = apply_filters( 'woocommerce_tm_epo_variation_color', $ov, $option, $options, $available_variations );
							}
						}

						++$_index;
					}
				}

				switch ( $variations_display_as ) {
					case 'select':
						if ( class_exists( 'THEMECOMPLETE_EPO_FIELDS_select' ) ) {

							$element_display = new THEMECOMPLETE_EPO_FIELDS_select();

							$selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $name ) ] ) ? urldecode( sanitize_text_field( wp_unslash( $_REQUEST[ 'attribute_' . sanitize_title( $name ) ] ) ) ) : $selected_value; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

							$fake_element = [
								'use_url'                => '',
								'textbeforeprice'        => '',
								'textafterprice'         => '',
								'hide_amount'            => 'hidden',
								'changes_product_image'  => $changes_product_image,
								'placeholder'            => esc_attr(
									apply_filters(
										'wc_epo_dropdown_variation_attribute_placeholder',
										esc_html__( 'Choose an option', 'woocommerce' ),
										[
											'options'   => $options,
											'attribute' => $name,
											'product'   => $tm_product,
											'selected'  => $selected,
										]
									)
								),
								'default_value'          => $default_value,
								'default_value_override' => true,
								'imagesp'                => $imagesp,
								'container_css_id'       => 'variation-element-',
								'options'                => $options_array,
								'original_options'       => $original_options_array,
							];

							$display = $element_display->display_field(
								$fake_element,
								[
									'name_inc'          => 'tm_attribute_' . $att_id . $form_prefix,
									'element_counter'   => '',
									'tabindex'          => '',
									'form_prefix'       => $form_prefix,
									'field_counter'     => $field_counter,
									'element_data_attr' => [ 'data-tm-for-variation' => $att_id ],
									'fieldtype'         => 'tmcp-field ' . $variations_class . ' tm-epo-variation-element',
								]
							);

							if ( is_array( $display ) ) {

								$field_args = [
									'name'       => 'tm_attribute_' . $att_id . $form_prefix,
									'element_id' => 'tm_attribute_id_' . $att_id . $form_prefix,
									'class'      => $variations_class . ' tm-epo-variation-element',
									'tabindex'   => '',
									'amount'     => '',
									'fieldtype'  => 'tmcp-field ' . $variations_class . ' tm-epo-variation-element',
								];

								$field_args = array_merge( $field_args, $display );

								if ( ! empty( $variations_class ) ) {
									$variations_builder_element_start_args['class'] = $variations_builder_element_start_args_class . ' ' . $variations_class;
								}
								$variations_builder_element_start_args['required']                 = 1;
								$variations_builder_element_start_args['label']                    = ! empty( $variations_label ) ? $variations_label : wc_attribute_label( $name );
								$variations_builder_element_start_args['class_id']                 = 'tm-variation-ul-' . $variations_display_as . ' variation-element-' . $loop . $form_prefix;
								$variations_builder_element_start_args['tm_variation_undo_button'] = '';
								$variations_builder_element_start_args['tm_product_id']            = $tm_product_id;
								wc_get_template(
									'tm-builder-element-start.php',
									$variations_builder_element_start_args,
									$epo_template_path,
									$epo_default_path
								);

								wc_get_template(
									'tm-select.php',
									$field_args,
									$epo_template_path,
									$epo_default_path
								);

								wc_get_template(
									'tm-builder-element-end.php',
									$variations_builder_element_end_args,
									$epo_template_path,
									$epo_default_path
								);
							}
						}
						break;

					case 'radio':
					case 'radiostart':
					case 'radioend':
					case 'image':
					case 'color':
					case 'text':
						if ( 'color' === $variations_display_as ) {
							$images = [];
						}
						if ( class_exists( 'THEMECOMPLETE_EPO_FIELDS_radio' ) && ! empty( $options_array ) ) {

							$element_display = new THEMECOMPLETE_EPO_FIELDS_radio();

							if ( ! empty( $variations_class ) ) {
								$variations_builder_element_start_args['class'] = $variations_builder_element_start_args_class . ' ' . $variations_class;
							}
							$variations_builder_element_start_args['required'] = 1;
							$variations_builder_element_start_args['label']    = ! empty( $variations_label ) ? $variations_label : wc_attribute_label( $name );

							$variations_builder_element_start_args['class_id'] = 'tm-variation-ul-' . $variations_display_as . ' variation-element-' . $loop . $form_prefix;
							if ( ! empty( $variations_show_reset_button ) ) {
								$variations_builder_element_start_args['tm_variation_undo_button'] = $att_id;
							} else {
								$variations_builder_element_start_args['tm_variation_undo_button'] = '';
							}
							$variations_builder_element_start_args['tm_product_id'] = $tm_product_id;

							$v_field_counter  = 0;
							$replacement_mode = 'none';
							$swatch_position  = 'center';
							switch ( $variations_display_as ) {
								case 'image':
									$replacement_mode = 'image';
									break;
								case 'color':
									$replacement_mode = 'color';
									break;
								case 'text':
									$replacement_mode = 'text';
									break;
								case 'radiostart':
									$replacement_mode = 'image';
									$swatch_position  = 'start';
									break;
								case 'radioend':
									$replacement_mode = 'image';
									$swatch_position  = 'end';
									break;
								default:
									// code...
									break;
							}
							$fake_element = [
								'default_value'          => $default_value,
								'default_value_override' => true,
								'class'                  => $variations_class,
								'textbeforeprice'        => '',
								'textafterprice'         => '',
								'hide_amount'            => 'hidden',
								'replacement_mode'       => $replacement_mode,
								'swatch_position'        => $swatch_position,
								'use_url'                => '',
								'images'                 => $images,
								'imagesp'                => $imagesp,
								'url'                    => [],
								'items_per_row'          => $variations_items_per_row,
								'items_per_row_r'        => [
									'desktop'        => $variations_items_per_row,
									'tablets_galaxy' => $variations_items_per_row_tablets_galaxy,
									'tablets'        => $variations_items_per_row_tablets,
									'tablets_small'  => $variations_items_per_row_tablets_small,
									'iphone6_plus'   => $variations_items_per_row_iphone6_plus,
									'iphone6'        => $variations_items_per_row_iphone6,
									'galaxy'         => $variations_items_per_row_samsung_galaxy,
									'iphone5'        => $variations_items_per_row_iphone5,
									'smartphones'    => $variations_items_per_row_smartphones,
								],
								'item_width'             => $variations_item_width,
								'item_height'            => $variations_item_height,
								'show_label'             => $variations_show_name,
								'limit'                  => '',
								'exactlimit'             => '',
								'minimumlimit'           => '',
								'show_tooltip'           => '',
								'changes_product_image'  => $changes_product_image,
								'container_css_id'       => 'variation-element-',
							];

							$element_display->display_field_pre(
								$fake_element,
								[
									'element_counter' => $loop,
									'tabindex'        => $v_field_counter,
									'form_prefix'     => $form_prefix,
									'field_counter'   => $v_field_counter,
									'product_id'      => $tm_product_id,
								]
							);

							$variations_builder_element_start_args['field_obj'] = $element_display;

							wc_get_template(
								'tm-builder-element-start.php',
								$variations_builder_element_start_args,
								$epo_template_path,
								$epo_default_path
							);

							foreach ( $options_array as $value => $label ) {

								if ( isset( $color[ $v_field_counter ] ) ) {
									$fake_element['color'] = $color[ $v_field_counter ];
								} else {
									unset( $fake_element['color'] );
								}

								$display = $element_display->display_field(
									$fake_element,
									[
										'element_id'      => 'tm_attribute_id_' . $att_id . '_' . $loop . '_' . $v_field_counter . '_' . intval( $v_field_counter + $loop ) . $form_prefix, // doesn't actually gets that value.
										'name'            => 'tm_attribute_' . $att_id . '_' . $loop . $form_prefix,
										'name_inc'        => 'tm_attribute_' . $att_id . '_' . $loop . $form_prefix,
										'value'           => isset( $original_options_array[ $value ] ) ? $original_options_array[ $value ] : $value,
										'label'           => $label,
										'element_counter' => $loop,
										'tabindex'        => $v_field_counter,
										'form_prefix'     => $form_prefix,
										'element_data_attr' => [ 'data-tm-for-variation' => $att_id ],
										'fieldtype'       => 'tmcp-field ' . $variations_class . ' tm-epo-variation-element',
										'field_counter'   => $v_field_counter,
										'border_type'     => THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_css_selected_border' ),
									]
								);

								if ( is_array( $display ) ) {

									$field_args = [
										'tm_element_settings' => $fake_element,
										'element_id'     => 'tm_attribute_id_' . $att_id . '_' . $loop . '_' . $v_field_counter . '_' . intval( $v_field_counter + $loop ) . $form_prefix, // doesn't actually gets that value.
										'name'           => 'tm_attribute_' . $att_id . '_' . $loop . $form_prefix,
										'class'          => $variations_class . ' tm-epo-variation-element',
										'tabindex'       => $v_field_counter,
										'rules'          => '',
										'original_rules' => '',
										'rules_type'     => '',
										'price_type'     => '',
										'amount'         => '',
										'fieldtype'      => 'tmcp-field ' . $variations_class . ' tm-epo-variation-element',
										'border_type'    => THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_css_selected_border' ),
									];

									$field_args = array_merge( $field_args, $display );

									wc_get_template(
										'tm-radio.php',
										$field_args,
										$epo_template_path,
										$epo_default_path
									);
								}

								++$v_field_counter;
							}

							wc_get_template(
								'tm-builder-element-end.php',
								$variations_builder_element_end_args,
								$epo_template_path,
								$epo_default_path
							);
						}
						break;
				}
			}

			if ( count( $attributes ) === intval( $loop ) ) {
				echo '<a class="reset_variations tc-cell tcwidth tcwidth-100" href="#reset">' . ( ( ! empty( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_reset_variation_text' ) ) ) ? esc_html( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_reset_variation_text' ) ) : esc_html__( 'Reset options', 'woocommerce-tm-extra-product-options' ) ) . '</a>';
			}
		}
	}
}

do_action( 'tm_after_styled_variations', $tm_product );
