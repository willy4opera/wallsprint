<?php
/**
 * Textfield Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.0
 * phpcs:disable PEAR.NamingConventions.ValidClassName
 */

defined( 'ABSPATH' ) || exit;

/**
 * Textfield Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.0
 */
class THEMECOMPLETE_EPO_FIELDS_textfield extends THEMECOMPLETE_EPO_FIELDS {

	/**
	 * Display field array
	 *
	 * @param array<mixed> $element The element array.
	 * @param array<mixed> $args Array of arguments.
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function display_field( $element = [], $args = [] ) {

		$validation1      = $this->get_value( $element, 'validation1', '' );
		$price_rules_type = $this->get_value( $element, 'price_rules_type', [] );

		$input_type = isset( $price_rules_type[0] ) && isset( $price_rules_type[0][0] )
		? (
			in_array( $price_rules_type[0][0], [ 'step', 'currentstep' ], true ) || ( 'number' === $validation1 || 'digits' === $validation1 )
			? ( 'digits' === $validation1 ? 'number' : 'decimal' )
			: 'text'
			)
		: 'text';

		$min_chars = $this->get_value( $element, 'min_chars', '' );
		$max_chars = $this->get_value( $element, 'max_chars', '' );

		$min_chars = '' !== $min_chars ? absint( $element['min_chars'] ) : '';
		$max_chars = '' !== $max_chars ? absint( $element['max_chars'] ) : '';

		$class_label = '';
		if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_select_fullwidth' ) ) {
			$class_label = ' fullwidth';
		}

		$step = $this->get_value( $element, 'step', '' );
		if ( empty( $step ) ) {
			$step = 'any';
		} else {
			THEMECOMPLETE_EPO_HELPER()->convert_to_number( floatval( themecomplete_convert_local_numbers( $step ) ), true );
		}

		$display = [
			'default_value'     => $this->get_value( $element, 'default_value', '' ),
			'get_default_value' => $this->get_default_value( $element, $args ),
			'textbeforeprice'   => $this->get_value( $element, 'text_before_price', '' ),
			'textafterprice'    => $this->get_value( $element, 'text_after_price', '' ),
			'hide_amount'       => $this->get_value( $element, 'hide_amount', '' ),
			'placeholder'       => $this->get_value( $element, 'placeholder', '' ),
			'min'               => $this->get_value( $element, 'min', '' ),
			'max'               => $this->get_value( $element, 'max', '' ),
			'step'              => $step,
			'quantity'          => $this->get_value( $element, 'quantity', '' ),
			'freechars'         => $this->get_value( $element, 'freechars', '' ),
			'min_chars'         => $min_chars,
			'max_chars'         => $max_chars,
			'input_type'        => $input_type,
			'class_label'       => $class_label,
		];

		return apply_filters( 'wc_epo_display_field_textfield', $display, $this, $element, $args );
	}

	/**
	 * Field validation
	 *
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function validate() {

		$passed  = true;
		$message = [];

		$quantity_once = false;
		$min_quantity  = isset( $this->element['quantity_min'] ) ? (int) $this->element['quantity_min'] : 0;
		if ( apply_filters( 'wc_epo_field_min_quantity_greater_than_zero', true ) && $min_quantity < 0 ) {
			$min_quantity = 0;
		}
		foreach ( $this->field_names as $attribute ) {
			$attribute_quantity = $attribute . '_quantity';
			if ( ! $quantity_once && isset( $this->epo_post_fields[ $attribute ] ) && '' !== $this->epo_post_fields[ $attribute ] && isset( $this->epo_post_fields[ $attribute_quantity ] ) && ! ( (int) array_sum( (array) $this->epo_post_fields[ $attribute_quantity ] ) >= $min_quantity ) ) {
				$passed        = false;
				$quantity_once = true;
				/* translators: %1 element label %2 quantity value. */
				$message[] = sprintf( esc_html__( 'The quantity for "%1$s" must be greater than %2$s', 'woocommerce-tm-extra-product-options' ), $this->element['label'], $min_quantity );
			}
			if ( $this->element['required'] ) {
				if ( ! isset( $this->epo_post_fields[ $attribute ] ) || '' === $this->epo_post_fields[ $attribute ] ) {
					$passed    = false;
					$message[] = 'required';
					break;
				}
			}
			if ( $this->element['min_chars'] ) {
				$val = false;
				if ( isset( $this->epo_post_fields[ $attribute ] ) ) {
					$val = $this->epo_post_fields[ $attribute ];
					$val = preg_replace( "/\r\n/u", "\n", $val );
				}
				if ( ! is_array( $val ) ) {
					$val = [ $val ];
				}

				foreach ( $val as $val_value ) {
					if ( '' !== $val_value && ( false !== $val_value && tc_strlen( $val_value ) < (int) $this->element['min_chars'] ) ) {
						$passed = false;
						/* translators: %1 number of characters %2 element label. */
						$message[] = sprintf( esc_html__( 'You must enter at least %1$s characters for "%2$s".', 'woocommerce-tm-extra-product-options' ), (int) $this->element['min_chars'], $this->element['label'] );
						break;
					}
				}
			}
			if ( $this->element['max_chars'] ) {
				$val = false;
				if ( isset( $this->epo_post_fields[ $attribute ] ) ) {
					$val = $this->epo_post_fields[ $attribute ];
					$val = preg_replace( "/\r\n/u", "\n", $val );
				}
				if ( ! is_array( $val ) ) {
					$val = [ $val ];
				}

				foreach ( $val as $val_value ) {
					if ( '' !== $val_value && ( false !== $val_value && tc_strlen( $val_value ) > (int) $this->element['max_chars'] ) ) {
						$passed = false;
						/* translators: %1 number of characters %2 element label. */
						$message[] = sprintf( esc_html__( 'You cannot enter more than %1$s characters for "%2$s".', 'woocommerce-tm-extra-product-options' ), (int) $this->element['max_chars'], $this->element['label'] );
						break;
					}
				}
			}
		}

		return [
			'passed'  => $passed,
			'message' => $message,
		];
	}
}
