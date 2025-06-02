/* global wc_add_to_cart_variation_params */

( function( $, window, document ) {
	'use strict';
	/**
	 * Handles variation forms and attributes.
	 *
	 * @param {Object} $form                     The product form
	 * @param {Object} element                   The product element
	 * @param {Object} currentCart               The current cart form
	 * @param {Object} variableProductContainers The container for the variable product
	 * @param {Object} epoObject                 The EPO Object
	 */
	var VariationForm = function( $form, element, currentCart, variableProductContainers, epoObject ) {
		var self = this;

		self.field = element;
		self.currentCart = currentCart;
		self.variableProductContainers = variableProductContainers;
		self.epoObject = epoObject;
		self.$form = $form;
		self.$wrap = $form.closest( '.tc-epo-element-product-container-wrap' );
		self.$attributeFields = $form.find( '.tc-epo-variable-product-selector' );
		self.$singleVariation = $form.find( '.tc-epo-element-single-variation' );
		self.$resetVariations = $form.find( '.tc-epo-element-variable-reset-variations' );
		self.variationData = $form.data( 'product_variations' );

		self.useAjax = false === self.variationData;
		self.xhr = false;
		self.loading = true;
		self.variationId = $form.closest( '.tc-epo-element-product-li-container' ).find( '.tc-epo-element-product-container-variation-id input.product-variation-id' );

		// Methods.
		self.getChosenAttributes = self.getChosenAttributes.bind( self );
		self.findMatchingVariations = self.findMatchingVariations.bind( self );
		self.isMatch = self.isMatch.bind( self );
		self.toggleResetLink = self.toggleResetLink.bind( self );

		// Events.
		$form.on( 'click.tc-variation-form', '.tc-epo-element-variable-reset-variations', { variationForm: self }, self.onReset );
		$form.on( 'tc_hide_variation', { variationForm: self }, self.onHide );
		$form.on( 'tc_show_variation', { variationForm: self }, self.onShow );
		$form.on( 'tc_reset_data', { variationForm: self }, self.onResetDisplayedVariation );
		$form.on( 'tc_reset_image', { variationForm: self }, self.onResetImage );
		$form.on( 'change.tc-variation-form', '.tc-epo-variable-product-selector', { variationForm: self }, self.onChange );
		$form.on( 'tc_found_variation.tc-variation-form', { variationForm: self }, self.onFoundVariation );
		$form.on( 'tc_check_variations.tc-variation-form', { variationForm: self }, self.onFindVariation );
		$form.on( 'tc_update_variation_values.tc-variation-form', { variationForm: self }, self.onUpdateAttributes );
		$form.on( 'refresh.tc-variation-form', { variationForm: self }, self.onRefreshContainer );
		$form.on( 'update_field.tc-variation-form', { variationForm: self }, self.onUpdateField );

		self.variationId.val( '' );

		// Init after gallery.
		setTimeout( function() {
			$form.trigger( 'refresh' );
			$form.trigger( 'tc_variation_form' );
			self.loading = false;
		}, 100 );
	};

	/**
	 * Refresh variations container
	 *
	 * @param {Event} event The event Object.
	 */
	VariationForm.prototype.onRefreshContainer = function( event ) {
		var form = event.data.variationForm;

		form.variableProductContainers.find( '.tc-epo-element-product-container' ).removeClass( 'variations_form' );

		form.$form.addClass( 'variations_form' );

		form.$form.trigger( 'tc_check_variations.tc-variation-form' );
	};

	/**
	 * Reset all fields.
	 *
	 * @param {Event}  event     The event Object.
	 * @param {Object} variation The variation Object.
	 */
	VariationForm.prototype.onUpdateField = function( event, variation ) {
		var form = event.data.variationForm;
		var field = form.field;
		var associatedSetter;
		var associatedPrice;
		var associatedRawPrice;
		var associatedOriginalPrice;
		var associatedRawOriginalPrice;

		event.preventDefault();

		if ( ! variation ) {
			associatedSetter = field;
			if ( field.is( 'select' ) ) {
				associatedSetter = field.find( 'option:selected' );
			}

			associatedPrice = 0;
			associatedRawPrice = 0;
			associatedOriginalPrice = 0;
			associatedRawOriginalPrice = 0;

			field.data( 'price_set', 1 );
			associatedSetter.data( 'associated_price_set', 1 );
			associatedSetter.data( 'price_set', 1 );
			associatedSetter.data( 'raw_price', associatedRawPrice );
			associatedSetter.data( 'raw_original_price', associatedRawOriginalPrice );
			associatedSetter.data( 'price', associatedPrice );
			associatedSetter.data( 'original_price', associatedOriginalPrice );

			field.data( 'price-changed', 1 );

			form.$form.trigger( {
				type: 'tm-epo-update',
				norules: 2
			} );
			form.currentCart.trigger( {
				type: 'tm-epo-update',
				norules: 2
			} );
		}
	};

	/**
	 * Reset all fields.
	 *
	 * @param {Event} event The event Object.
	 */
	VariationForm.prototype.onReset = function( event ) {
		event.preventDefault();
		event.data.variationForm.$attributeFields.val( '' ).trigger( 'change' );
		event.data.variationForm.$form.trigger( 'tc_reset_data' );
	};

	/**
	 * When a variation is hidden.
	 *
	 * @param {Event} event The event Object.
	 */
	VariationForm.prototype.onHide = function( event ) {
		event.preventDefault();
	};
	/**
	 * When a variation is shown.
	 *
	 * @param {Event} event The event Object.
	 */
	VariationForm.prototype.onShow = function( event ) {
		event.preventDefault();
	};
	/**
	 * When displayed variation data is reset.
	 *
	 * @param {Event} event The event Object.
	 */
	VariationForm.prototype.onResetDisplayedVariation = function( event ) {
		var form = event.data.variationForm;
		form.$form.find( '.product-meta' ).find( '.tc-product-sku' ).tc_reset_content();
		form.$form.trigger( 'tc_reset_image' );
		form.$singleVariation.slideUp( 200 ).trigger( 'tc_hide_variation' );
	};

	/**
	 * When the product image is reset.
	 *
	 * @param {Event} event The event Object.
	 */
	VariationForm.prototype.onResetImage = function( event ) {
		event.data.variationForm.$form.tc_variations_image_update( false );
	};

	/**
	 * Looks for matching variations for current selected attributes.
	 *
	 * @param {Event} event The event Object.
	 */
	VariationForm.prototype.onFindVariation = function( event ) {
		var form = event.data.variationForm;
		var attributes = form.getChosenAttributes();
		var currentAttributes = attributes.data;
		var cpfElement;
		var matching_variations;
		var variation;

		if ( attributes.count === attributes.chosenCount ) {
			if ( form.useAjax ) {
				if ( typeof wc_add_to_cart_variation_params === 'undefined' ) {
					return;
				}
				if ( form.xhr ) {
					form.xhr.abort();
				}
				cpfElement = form.$form.closest( '.cpf-type-product' );
				form.$form.block( { message: null, overlayCSS: { background: '#fff', opacity: 0.6 } } );
				currentAttributes.product_id = parseInt( form.$form.data( 'product_id' ), 10 );
				currentAttributes.discount = cpfElement.attr( 'data-discount' );
				currentAttributes.discount_type = cpfElement.attr( 'data-discount-type' );
				currentAttributes.discount_exclude_addons = cpfElement.attr( 'data-discount-exclude-addons' );

				form.xhr = $.ajax( {
					url: wc_add_to_cart_variation_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'get_variation' ),
					type: 'POST',
					data: currentAttributes,
					success: function( svariation ) {
						if ( svariation ) {
							form.$form.trigger( 'tc_found_variation', [ svariation ] );
						} else {
							form.$form.trigger( 'tc_reset_data' );
							attributes.chosenCount = 0;

							if ( ! form.loading ) {
								form.$form.find( '.tc-epo-element-single-variation' ).after( '<p class="wc-no-matching-variations woocommerce-info">' + wc_add_to_cart_variation_params.i18n_no_matching_variations_text + '</p>' );
								form.$form.find( '.wc-no-matching-variations' ).slideDown( 200 );
								form.$form.trigger( 'update_field', [] );
							}
						}
					},
					complete: function() {
						form.$form.unblock();
					}
				} );
			} else {
				form.$form.trigger( 'tc_update_variation_values' );

				matching_variations = form.findMatchingVariations( form.variationData, currentAttributes );
				variation = matching_variations.shift();

				if ( variation ) {
					form.$form.trigger( 'tc_found_variation', [ variation ] );
				} else {
					form.$form.trigger( 'tc_reset_data' );
					attributes.chosenCount = 0;

					if ( ! form.loading ) {
						form.$form.find( '.tc-epo-element-single-variation' ).after( '<p class="wc-no-matching-variations woocommerce-info">' + wc_add_to_cart_variation_params.i18n_no_matching_variations_text + '</p>' );
						form.$form.find( '.wc-no-matching-variations' ).slideDown( 200 );
					}
				}
			}
		} else {
			form.variationId.val( '' ).trigger( 'change' );
			form.$form.trigger( 'tc_update_variation_values' );
			form.$form.trigger( 'tc_reset_data' );
			form.$form.trigger( 'update_field', [] );
		}

		// Show reset link.
		form.toggleResetLink( attributes.chosenCount > 0 );
	};

	/**
	 * Triggered when a variation has been found which matches all attributes.
	 *
	 * @param {Event}  event     The event Object.
	 * @param {Object} variation The variation Object.
	 */
	VariationForm.prototype.onFoundVariation = function( event, variation ) {
		var form = event.data.variationForm,
			$sku = form.$form.find( '.product-meta' ).find( '.tc-product-sku' ),
			$qtyWrap = form.$wrap.find( '.tm-quantity-alt' ),
			$qty = $qtyWrap.find( 'input.tm-qty-alt' ),
			qtyMin = $.epoAPI.math.toInt( $qty.attr( 'data-min' ) ),
			qtyMax = $.epoAPI.math.toInt( $qty.attr( 'data-max' ) ),
			dataQtyMax = $qty.attr( 'data-max' ),
			purchasable = true,
			template = false,
			$template_html = '';

		if ( variation.sku ) {
			$sku.tc_set_content( variation.sku );
		} else {
			$sku.tc_reset_content();
		}

		form.$form.tc_variations_image_update( variation );
		if ( ! variation.variation_is_visible ) {
			template = wpTemplate( 'unavailable-variation-template' );
		} else {
			template = wpTemplate( 'variation-template' );
		}

		if ( form.field.attr( 'data-no-price' ) ) {
			variation.display_price = '';
			variation.display_regular_price = '';
			variation.price_html = '';
		}

		form.$form.find( '.tc-epo-element-product-container-cart' ).find( '.stock' ).remove();

		$template_html = template( {
			variation: variation
		} );
		$template_html = $template_html.replace( '/*<![CDATA[*/', '' );
		$template_html = $template_html.replace( '/*]]>*/', '' );
		form.$singleVariation.html( $template_html );
		form.variationId.val( variation.variation_id ).trigger( 'change' );

		// Hide or show qty input
		if ( variation.is_sold_individually === 'yes' ) {
			$qty.val( '1' ).attr( 'min', '1' ).attr( 'max', '1' );
			$qtyWrap.hide();
		} else {
			if ( variation.min_qty !== '' && variation.min_qty > qtyMin && qtyMin > 0 ) {
				qtyMin = variation.min_qty;
			}

			if ( variation.max_qty !== '' && variation.max_qty < qtyMax ) {
				qtyMax = variation.max_qty;
			}

			if ( qtyMin !== '' ) {
				$qty.attr( 'min', qtyMin );
			} else {
				$qty.removeAttr( 'min' );
			}
			if ( dataQtyMax !== '' ) {
				$qty.attr( 'max', qtyMax );
			} else {
				$qty.removeAttr( 'max' );
			}

			if ( qtyMax && $.epoAPI.math.toInt( $qty.val() ) > qtyMax ) {
				$qty.val( qtyMax );
			}
			if ( qtyMin > $.epoAPI.math.toInt( $qty.val() ) ) {
				$qty.val( qtyMin );
			}
			$qtyWrap.show();
		}

		if ( ! variation.is_purchasable || ! variation.is_in_stock || ! variation.variation_is_visible ) {
			purchasable = false;
			$qty.val( '0' ).attr( 'min', '0' ).attr( 'max', '0' );
			$qtyWrap.hide();
		}

		$qty.trigger( 'change' );

		// Reveal
		if ( $.epoAPI.util.trim( form.$singleVariation.text() ) ) {
			form.$singleVariation.slideDown( 200 ).trigger( 'tc_show_variation', [ variation, purchasable ] );
		} else {
			form.$singleVariation.show().trigger( 'tc_show_variation', [ variation, purchasable ] );
		}
		form.$form.trigger( 'update_field', [ variation ] );
	};

	/**
	 * Triggered when an attribute field changes.
	 *
	 * @param {Event} event The event Object.
	 */
	VariationForm.prototype.onChange = function( event ) {
		var form = event.data.variationForm;
		var field = form.field;

		form.variationId.val( '' ).trigger( 'change' );
		form.$form.find( '.wc-no-matching-variations' ).remove();

		if ( form.useAjax ) {
			form.$form.trigger( 'tc_check_variations' );
		} else {
			form.$form.trigger( 'woocommerce_variation_select_change' );
			form.$form.trigger( 'tc_check_variations' );
		}

		// Custom event for when variation selection has been changed
		form.$form.trigger( 'woocommerce_variation_has_changed' );

		setTimeout( function() {
			field.closest( '.tcowl-carousel' ).trigger( 'refresh.owl.carousel' );
		}, 50 );
	};

	/**
	 * Updates attributes in the DOM to show valid values.
	 *
	 * @param {Event} event The event Object.
	 */
	VariationForm.prototype.onUpdateAttributes = function( event ) {
		var form = event.data.variationForm,
			attributes = form.getChosenAttributes(),
			currentAttributes = attributes.data;

		if ( form.useAjax ) {
			return;
		}

		// Loop through selects and disable/enable options based on selections.
		form.$attributeFields.each( function( index, el ) {
			var current_attr_select = $( el );
			var current_attr_name = current_attr_select.data( 'attribute_name' ) || current_attr_select.attr( 'name' );
			var show_option_none = $( el ).data( 'show_option_none' );
			var option_gt_filter = ':gt(0)';
			var attached_options_count = 0;
			var new_attr_select = $( '<select/>' );
			var selected_attr_val = current_attr_select.val() || '';
			var selected_attr_val_valid = true;
			var refSelect;
			var checkAttributes;
			var variations;
			var num;
			var i;
			var len;
			var variationAttributes;
			var attr_name;
			var attr_val;
			var variation_active;
			var $option_elements;
			var $option_element;
			var option_value;

			// Reference options set at first.
			if ( ! current_attr_select.data( 'attribute_html' ) ) {
				refSelect = current_attr_select.clone();

				refSelect.find( 'option' ).prop( 'disabled', false ).prop( 'selected', false ).removeAttr( 'attached' );

				current_attr_select.data( 'attribute_options', refSelect.find( 'option' + option_gt_filter ).get() ); // Legacy data attribute.
				current_attr_select.data( 'attribute_html', refSelect.html() );
			}

			new_attr_select.html( current_attr_select.data( 'attribute_html' ) );

			// The attribute of this select field should not be taken into account when calculating its matching variations:
			// The constraints of this attribute are shaped by the values of the other attributes.
			checkAttributes = $.extend( true, {}, currentAttributes );

			checkAttributes[ current_attr_name ] = '';

			variations = form.findMatchingVariations( form.variationData, checkAttributes );

			// Loop through variations.
			for ( num in variations ) {
				if ( typeof variations[ num ] !== 'undefined' ) {
					variationAttributes = variations[ num ].attributes;

					for ( attr_name in variationAttributes ) {
						if ( Object.prototype.hasOwnProperty.call( variationAttributes, attr_name ) ) {
							attr_val = variationAttributes[ attr_name ];
							variation_active = '';

							if ( attr_name === current_attr_name ) {
								if ( variations[ num ].variation_is_active ) {
									variation_active = 'enabled';
								}

								if ( attr_val ) {
									// Decode entities.
									attr_val = $( '<div/>' ).html( attr_val ).text();

									// Attach to matching options by value. This is done to compare
									// TEXT values rather than any HTML entities.
									$option_elements = new_attr_select.find( 'option' );
									if ( $option_elements.length ) {
										for ( i = 0, len = $option_elements.length; i < len; i++ ) {
											$option_element = $( $option_elements[ i ] );
											option_value = $option_element.val();

											if ( attr_val === option_value ) {
												$option_element.addClass( 'attached ' + variation_active );
												break;
											}
										}
									}
								} else {
									// Attach all apart from placeholder.
									new_attr_select.find( 'option:gt(0)' ).addClass( 'attached ' + variation_active );
								}
							}
						}
					}
				}
			}

			// Count available options.
			attached_options_count = new_attr_select.find( 'option.attached' ).length;

			// Check if current selection is in attached options.
			if ( selected_attr_val ) {
				selected_attr_val_valid = false;

				if ( 0 !== attached_options_count ) {
					new_attr_select.find( 'option.attached.enabled' ).each( function() {
						var thisVal = $( this ).val();

						if ( selected_attr_val === thisVal ) {
							selected_attr_val_valid = true;
							return false; // break.
						}
					} );
				}
			}

			/**
			 * Detach the placeholder if:
			 * - Valid options exist.
			 * - The current selection is non-empty.
			 * - The current selection is valid.
			 * - Placeholders are not set to be permanently visible.
			 */
			if ( attached_options_count > 0 && selected_attr_val && selected_attr_val_valid && 'no' === show_option_none ) {
				new_attr_select.find( 'option:first' ).remove();
				option_gt_filter = '';
			}

			// Detach unattached.
			new_attr_select.find( 'option' + option_gt_filter + ':not(.attached)' ).remove();

			// Finally, copy to DOM and set value.
			current_attr_select.html( new_attr_select.html() );
			current_attr_select.find( 'option' + option_gt_filter + ':not(.enabled)' ).prop( 'disabled', true );

			// Choose selected value.
			if ( selected_attr_val ) {
				// If the previously selected value is no longer available, fall back to the placeholder (it's going to be there).
				if ( selected_attr_val_valid ) {
					current_attr_select.val( selected_attr_val );
				} else {
					current_attr_select.val( '' ).trigger( 'change' );
				}
			} else {
				current_attr_select.val( '' ); // No change event to prevent infinite loop.
			}
		} );

		// Custom event for when variations have been updated.
		form.$form.trigger( 'woocommerce_update_variation_values' );
	};

	/**
	 * Get chosen attributes from form.
	 */
	VariationForm.prototype.getChosenAttributes = function() {
		var data = {};
		var count = 0;
		var chosen = 0;

		this.$attributeFields.each( function() {
			var attribute_name = $( this ).data( 'attribute_name' ) || $( this ).attr( 'name' );
			var value = $( this ).val() || '';

			if ( value.length > 0 ) {
				chosen++;
			}

			count++;
			data[ attribute_name ] = value;
		} );

		return {
			count: count,
			chosenCount: chosen,
			data: data
		};
	};

	/**
	 * Find matching variations for attributes.
	 *
	 * @param {Array} variations Variations.
	 * @param {Array} attributes attributes.
	 */
	VariationForm.prototype.findMatchingVariations = function( variations, attributes ) {
		var matching = [];
		var i;
		var variation;
		for ( i = 0; i < variations.length; i++ ) {
			variation = variations[ i ];

			if ( this.isMatch( variation.attributes, attributes ) ) {
				matching.push( variation );
			}
		}
		return matching;
	};

	/**
	 * See if attributes match.
	 *
	 * @param {Array} variation_attributes Variation attributes.
	 * @param {Array} attributes           attributes.
	 */
	VariationForm.prototype.isMatch = function( variation_attributes, attributes ) {
		var match = true;
		var val1;
		var val2;
		var attr_name;
		for ( attr_name in variation_attributes ) {
			if ( Object.prototype.hasOwnProperty.call( variation_attributes, attr_name ) ) {
				val1 = variation_attributes[ attr_name ];
				val2 = attributes[ attr_name ];
				if ( val1 !== undefined && val2 !== undefined && val1.length !== 0 && val2.length !== 0 && val1 !== val2 ) {
					match = false;
				}
			}
		}
		return match;
	};

	/**
	 * Show or hide the reset link.
	 *
	 * @param {bool} on true or false.
	 */
	VariationForm.prototype.toggleResetLink = function( on ) {
		if ( on ) {
			if ( this.$resetVariations.css( 'visibility' ) === 'hidden' ) {
				this.$resetVariations.css( 'visibility', 'visible' ).hide().fadeIn();
			}
		} else {
			this.$resetVariations.css( 'visibility', 'hidden' );
		}
	};

	/**
	 * Function to call tc_variation_form on jquery selector.
	 *
	 * @param {Object} element                   The product element
	 * @param {Object} currentCart               The current cart form
	 * @param {Object} variableProductContainers The container for the variable product
	 * @param {Object} thisEpoObject             The EPO Object
	 */
	$.fn.tc_product_variation_form = function( element, currentCart, variableProductContainers, thisEpoObject ) {
		new VariationForm( this, element, currentCart, variableProductContainers, thisEpoObject );
		this.trigger( 'tc_product_variation_form' );
		return this;
	};

	/**
	 * Stores the default text for an element so it can be reset later
	 *
	 * @param {string} content Content to set.
	 */
	$.fn.tc_set_content = function( content ) {
		if ( undefined === this.attr( 'data-o_content' ) ) {
			this.attr( 'data-o_content', this.text() );
		}
		this.text( content );
	};
	/**
	 * Stores the default text for an element so it can be reset later
	 */
	$.fn.tc_reset_content = function() {
		if ( undefined !== this.attr( 'data-o_content' ) ) {
			this.text( this.attr( 'data-o_content' ) );
		}
	};

	/**
	 * Stores a default attribute for an element so it can be reset later
	 *
	 * @param {string} attr  Attribute name.
	 * @param {string} value Value name.
	 */
	$.fn.tc_set_variation_attr = function( attr, value ) {
		if ( undefined === this.attr( 'data-o_' + attr ) ) {
			this.attr( 'data-o_' + attr, ! this.attr( attr ) ? '' : this.attr( attr ) );
		}
		if ( false === value ) {
			this.removeAttr( attr );
		} else {
			this.attr( attr, value );
		}
	};

	/**
	 * Reset a default attribute for an element so it can be reset later
	 *
	 * @param {string} attr Attribute name.
	 */
	$.fn.tc_reset_variation_attr = function( attr ) {
		if ( undefined !== this.attr( 'data-o_' + attr ) ) {
			this.attr( attr, this.attr( 'data-o_' + attr ) );
		}
	};
	/**
	 * Sets product images for the chosen variation
	 *
	 * @param {Object} variation variation object.
	 */
	$.fn.tc_variations_image_update = function( variation ) {
		var $form = this,
			$product_img_wrap = $form.find( '.tc-product-image, .woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder' ).eq( 0 ),
			$product_img = $product_img_wrap.find( '.wp-post-image' ),
			$product_link = $product_img_wrap.find( 'a' ).eq( 0 );

		if ( variation && variation.image && variation.image.src && variation.image.src.length > 1 ) {
			$product_img.tc_set_variation_attr( 'src', variation.image.src );
			$product_img.tc_set_variation_attr( 'height', variation.image.src_h );
			$product_img.tc_set_variation_attr( 'width', variation.image.src_w );
			$product_img.tc_set_variation_attr( 'srcset', variation.image.srcset );
			$product_img.tc_set_variation_attr( 'sizes', variation.image.sizes );
			$product_img.tc_set_variation_attr( 'title', variation.image.title );
			$product_img.tc_set_variation_attr( 'data-caption', variation.image.caption );
			$product_img.tc_set_variation_attr( 'alt', variation.image.alt );
			$product_img.tc_set_variation_attr( 'data-src', variation.image.full_src );
			$product_img.tc_set_variation_attr( 'data-large_image', variation.image.full_src );
			$product_img.tc_set_variation_attr( 'data-large_image_width', variation.image.full_src_w );
			$product_img.tc_set_variation_attr( 'data-large_image_height', variation.image.full_src_h );
			$product_img_wrap.tc_set_variation_attr( 'data-thumb', variation.image.src );
			$product_link.tc_set_variation_attr( 'href', variation.image.full_src );
		} else {
			$form.tc_variations_image_reset();
		}
	};

	/**
	 * Reset main image to defaults.
	 */
	$.fn.tc_variations_image_reset = function() {
		var $form = this,
			$product_img_wrap = $form.find( '.tc-product-image, .woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder' ).eq( 0 ),
			$product_img = $product_img_wrap.find( '.wp-post-image' ),
			$product_link = $product_img_wrap.find( 'a' ).eq( 0 );

		$product_img.tc_reset_variation_attr( 'src' );
		$product_img.tc_reset_variation_attr( 'width' );
		$product_img.tc_reset_variation_attr( 'height' );
		$product_img.tc_reset_variation_attr( 'srcset' );
		$product_img.tc_reset_variation_attr( 'sizes' );
		$product_img.tc_reset_variation_attr( 'title' );
		$product_img.tc_reset_variation_attr( 'data-caption' );
		$product_img.tc_reset_variation_attr( 'alt' );
		$product_img.tc_reset_variation_attr( 'data-src' );
		$product_img.tc_reset_variation_attr( 'data-large_image' );
		$product_img.tc_reset_variation_attr( 'data-large_image_width' );
		$product_img.tc_reset_variation_attr( 'data-large_image_height' );
		$product_img_wrap.tc_reset_variation_attr( 'data-thumb' );
		$product_link.tc_reset_variation_attr( 'href' );
	};

	/**
	 * Avoids using wp.template where possible in order to be CSP compliant.
	 * wp.template uses internally eval().
	 *
	 * @param {string} templateId The id of the template.
	 */
	function wpTemplate( templateId ) {
		var html = document.getElementById( 'tmpl-' + templateId ).textContent;
		var hard = false;
		// any <# #> interpolate (evaluate).
		hard = hard || /<#\s?data\./.test( html );
		// any data that is NOT data.variation.
		hard = hard || /{{{?\s?data\.(?!variation\.).+}}}?/.test( html );
		// any data access deeper than 1 level e.g.
		// data.variation.object.item
		// data.variation.object['item']
		// data.variation.array[0]
		hard = hard || /{{{?\s?data\.variation\.[\w-]*[^\s}]/.test( html );
		if ( hard ) {
			return window.wp.template( templateId );
		}
		return function template( data ) {
			var variation = data.variation || {};
			var replacement;
			return html.replace( /({{{?)\s?data\.variation\.([\w-]*)\s?(}}}?)/g, function( _, open, key, close ) {
				// Error in the format, ignore.
				if ( open.length !== close.length ) {
					return '';
				}
				replacement = variation[ key ] || '';
				// {{{ }}} => interpolate (unescaped).
				// {{  }}  => interpolate (escaped).
				// https://codex.wordpress.org/Javascript_Reference/wp.template
				if ( open.length === 2 ) {
					return window.escape( replacement );
				}
				return replacement;
			} );
		};
	}
}( window.jQuery, window, document ) );
