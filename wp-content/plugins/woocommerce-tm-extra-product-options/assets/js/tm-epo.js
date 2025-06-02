( function( window, document, $ ) {
	'use strict';

	var tcAPI = {};
	var TMEPOJS = window.TMEPOJS;
	var wp = window.wp;
	var TMEPOQTRANSLATEXJS = window.TMEPOQTRANSLATEXJS;
	var noUiSlider = window.noUiSlider;
	var ClipboardEvent = window.ClipboardEvent;
	var DataTransfer = window.DataTransfer;
	var lateVariationEvent = [];
	var latecpflogicactions = [];
	var tmLazyloadContainer = false;
	var variationsFormIsLoaded = false;
	var jBody = $( 'body' );
	var jWindow = $( window );
	var jDocument = $( document );
	var errorObject;
	var FloatingTotalsBox;
	var currentAjaxButton;
	var errorContainer = $( window );
	var tcmexp = window.tcmexp;
	var _ = window._;
	var priceCache = false;

	var getLocalInputDecimalSeparator = function() {
		if ( TMEPOJS.tm_epo_global_input_decimal_separator === '' ) {
			return TMEPOJS.currency_format_decimal_sep;
		}
		return $.epoAPI.locale.getSystemDecimalSeparator();
	};

	var getLocalDecimalSeparator = function() {
		if ( TMEPOJS.tm_epo_global_displayed_decimal_separator === '' ) {
			return TMEPOJS.currency_format_decimal_sep;
		}
		return $.epoAPI.locale.getSystemDecimalSeparator();
	};

	var getLocalThousandSeparator = function() {
		if ( TMEPOJS.tm_epo_global_displayed_decimal_separator === '' ) {
			return TMEPOJS.currency_format_thousand_sep;
		}
		return $.epoAPI.locale.getSystemDecimalSeparator();
	};

	var getEpoDelay = function() {
		if ( TMEPOJS.tm_epo_start_animation_delay ) {
			return TMEPOJS.tm_epo_start_animation_delay;
		} else if ( window.tc_epo_delay ) {
			return window.tc_epo_delay;
		}
		return 500;
	};

	var getEpoAnimationDelay = function() {
		if ( TMEPOJS.tm_epo_animation_delay ) {
			return TMEPOJS.tm_epo_start_animation_delay;
		} else if ( window.tc_epo_animation_delay ) {
			return window.tc_epo_animation_delay;
		}
		return 500;
	};

	var originalVal = $.fn.val;

	if ( ! TMEPOJS || ! wp ) {
		return;
	}

	// Trigger plugin events when other plugins use val.
	$.fn.val = function() {
		var result = originalVal.apply( this, arguments );
		if ( arguments.length > 0 ) {
			if ( $( this ).is( '.input-text.qty' ) ) {
				$( this ).trigger( 'change.cpf' );
			}
		}
		return result;
	};

	// Set update event for Lazy Load XT
	if ( TMEPOJS.tm_epo_no_lazy_load === 'no' && $.lazyLoadXT ) {
		$.extend( $.lazyLoadXT, {
			autoInit: false,
			updateEvent: $.lazyLoadXT.updateEvent + ' tmlazy'
		} );
	}

	tcAPI.localInputDecimalSeparator = getLocalInputDecimalSeparator();
	tcAPI.localDecimalSeparator = getLocalDecimalSeparator();
	tcAPI.localThousandSeparator = getLocalThousandSeparator();
	tcAPI.epoDelay = getEpoDelay();
	tcAPI.epoAnimationDelay = getEpoAnimationDelay();
	tcAPI.getElementFromFieldCache = [];
	tcAPI.epoSelector = '.tc-extra-product-options';
	tcAPI.associatedEpoSelector = '.tc-extra-product-options-inline';
	tcAPI.associatedEpoCart = '.tc-epo-element-product-container-cart';
	tcAPI.addToCartSelector = "input[name='add-to-cart']";
	tcAPI.tcAddToCartSelector = 'input.tc-add-to-cart';
	tcAPI.qtySelector = "input.qty,input[name='quantity'],select.qty,.drop-down-button #qty,.plus-minus-button #qty,.slider-input #amount";
	tcAPI.associateQtySelector = 'input.tm-qty-alt';
	tcAPI.addToCartButtonSelector = '.add_to_cart_button, .single_add_to_cart_button';
	tcAPI.compositeSelector = '.bto_item,.component';
	tcAPI.nativeProductPriceSelector = '.woocommerce .product p.price, .wc-block-components-product-price';
	tcAPI.associatedNativeProductPriceSelector = '.product-price .associated-price';
	tcAPI.templateEngine = $.epoAPI.applyFilter( 'tc_adjust_templateEngine', {
		plain_price: wp.template( 'tc-plain-price' ),
		price: wp.template( 'tc-price' ),
		price_m: wp.template( 'tc-price-m' ),
		sale_price: wp.template( 'tc-sale-price' ),
		sale_price_m10: wp.template( 'tc-sale-price-m10' ),
		sale_price_m01: wp.template( 'tc-sale-price-m01' ),
		sale_price_m11: wp.template( 'tc-sale-price-m11' ),
		tc_chars_remanining: wp.template( 'tc-chars-remanining' ),
		tc_final_totals: wp.template( 'tc-final-totals' ),
		tc_floating_box: wp.template( 'tc-floating-box' ),
		tc_floating_box_nks: wp.template( 'tc-floating-box-nks' ),
		tc_formatted_price: wp.template( 'tc-formatted-price' ),
		tc_formatted_sale_price: wp.template( 'tc-formatted-sale-price' ),
		tc_lightbox: wp.template( 'tc-lightbox' ),
		tc_lightbox_zoom: wp.template( 'tc-lightbox-zoom' ),
		tc_section_pop_link: wp.template( 'tc-section-pop-link' ),
		tc_upload_messages: wp.template( 'tc-upload-messages' )
	} );

	// make API available to 3rd party plugins
	$.tcAPI = function() {
		return tcAPI;
	};
	// method for accessing internal api variables
	$.tcAPIGet = function( name ) {
		return tcAPI[ name ];
	};

	// method for setting  internal api variables
	$.tcAPISet = function( name, value ) {
		tcAPI[ name ] = value;
	};

	if ( $.tc_validator ) {
		$.extend( $.tc_validator.messages, {
			required: TMEPOJS.tm_epo_global_validator_messages.required,
			email: TMEPOJS.tm_epo_global_validator_messages.email,
			url: TMEPOJS.tm_epo_global_validator_messages.url,
			number: TMEPOJS.tm_epo_global_validator_messages.number,
			digits: TMEPOJS.tm_epo_global_validator_messages.digits,
			maxlengthsingle: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.maxlengthsingle ),
			maxlength: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.maxlength ),
			minlengthsingle: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.minlengthsingle ),
			minlength: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.minlength ),
			max: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.max ),
			min: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.min ),
			step: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.step ),
			lettersonly: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.lettersonly ),
			lettersspaceonly: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.lettersspaceonly ),
			alphanumeric: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.alphanumeric ),
			alphanumericunicode: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.alphanumericunicode ),
			alphanumericunicodespace: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.alphanumericunicodespace ),
			repeaterminrows: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.repeaterminrows ),
			repeatermaxrows: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.repeatermaxrows )
		} );

		/*
		ASCII Digits
		\u0030-\u0039

		Latin Alphabet
		\u0041-\u005A\u0061-\u007A

		Latin-1 Supplement
		\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u00FF

		Latin Extended-A
		\u0100-\u0148\u014A-\u017F

		Latin Extended-B
		\u0180-\u01BF\u01C4-\u024F

		Latin Extended Additional
		\u1E02-\u1EF3

		Greek and Coptic
		\u0370-\u03FF

		Cyrillic
		\u0400-\u04FF\u0500-\u052F

		Japanese Hiragana
		\u3040-\u309f
		Japanese Katakana
		\u30a0-\u30ff
		Japanese Kanji (common & uncommon)
		\u4e00-\u9faf
		Japanese Kanji (rare)
		\u3400-\u4dbf

		Arabic
		\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF\uFB50-\uFDFF\uFE70-\uFEFF\u10E60-\u10E7F\u1EC70-\u1ECBF\u1ED00-\u1ED4F\u1EE00-\u1EEFF

		\u0600-\u06ff
		\u0600-\u06FF

		Armenian
		\u0530-\u1058F

		\u0030-\u0039\u0041-\u005A\u0061-\u007A\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u00FF\u0100-\u0148\u014A-\u017F\u0180-\u01BF\u01C4-\u024F\u1E02-\u1EF3\u0370-\u03FF\u0400-\u04FF\u0500-\u052F\u3040-\u309f\u30a0-\u30ff\u4e00-\u9faf\u3400-\u4dbf\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF\uFB50-\uFDFF\uFE70-\uFEFF\u10E60-\u10E7F\u1EC70-\u1ECBF\u1ED00-\u1ED4F\u1EE00-\u1EEFF\u0530-\u1058F

		*/

		$.tc_validator.addMethod(
			'alphanumeric',
			function( value, element ) {
				return this.optional( element ) || /^[a-zA-Z0-9.-]+$/i.test( value );
			},
			$.tc_validator.messages.alphanumeric
		);

		$.tc_validator.addMethod(
			'lettersonly',
			function( value, element ) {
				return this.optional( element ) || /^[a-z]+$/i.test( value );
			},
			$.tc_validator.messages.lettersonly
		);

		$.tc_validator.addMethod(
			'lettersspaceonly',
			function( value, element ) {
				return this.optional( element ) || /^[a-z,\u0020]+$/i.test( value );
			},
			$.tc_validator.messages.lettersspaceonly
		);

		$.tc_validator.addMethod(
			'alphanumericunicode',
			function( value, element ) {
				return (
					this.optional( element ) ||
					/^[\u0030-\u0039\u0041-\u005A\u0061-\u007A\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u00FF\u0100-\u0148\u014A-\u017F\u0180-\u01BF\u01C4-\u024F\u1E02-\u1EF3\u0370-\u03FF\u0400-\u04FF\u0500-\u052F\u3040-\u309f\u30a0-\u30ff\u4e00-\u9faf\u3400-\u4dbf\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF\uFB50-\uFDFF\uFE70-\uFEFF\u10E60-\u10E7F\u1EC70-\u1ECBF\u1ED00-\u1ED4F\u1EE00-\u1EEFF\u0530-\u1058F]+$/i.test(
						value
					)
				);
			},
			$.tc_validator.messages.alphanumericunicode
		);

		$.tc_validator.addMethod(
			'alphanumericunicodespace',
			function( value, element ) {
				return (
					this.optional( element ) ||
					/^[\u0030-\u0039\u0041-\u005A\u0061-\u007A\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u00FF\u0100-\u0148\u014A-\u017F\u0180-\u01BF\u01C4-\u024F\u1E02-\u1EF3\u0370-\u03FF\u0400-\u04FF\u0500-\u052F\u3040-\u309f\u30a0-\u30ff\u4e00-\u9faf\u3400-\u4dbf\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF\uFB50-\uFDFF\uFE70-\uFEFF\u10E60-\u10E7F\u1EC70-\u1ECBF\u1ED00-\u1ED4F\u1EE00-\u1EEFF\u0530-\u1058F,\u0020]+$/i.test(
						value
					)
				);
			},
			$.tc_validator.messages.alphanumericunicodespace
		);
		$.tc_validator.addMethod(
			'repeaterminrows',
			function( value, element, param ) {
				return $.epoAPI.math.toInt( $( element ).closest( '.tc-element-inner-wrap' ).find( '.tc-repeater-element' ).length ) >= $.epoAPI.math.toInt( param );
			},
			$.tc_validator.messages.repeaterminrows
		);
		$.tc_validator.addMethod(
			'repeatermaxrows',
			function( value, element, param ) {
				return $.epoAPI.math.toInt( $( element ).closest( '.tc-element-inner-wrap' ).find( '.tc-repeater-element' ).length ) <= $.epoAPI.math.toInt( param );
			},
			$.tc_validator.messages.repeatermaxrows
		);
		$.tc_validator.addMethod(
			'product_element_radio_qty',
			function( value, element ) {
				var qty = $( element ).closest( '.tm-element-ul-product' ).find( 'input.tc-epo-field-product.tmcp-radio:checked' ).closest( '.tc-epo-element-product-holder' ).find( '.tm-qty' ).first().val();
				return qty > 0;
			},
			$.tc_validator.messages.required
		);
		$.tc_validator.addMethod(
			'product_element_select_qty',
			function( value, element ) {
				var qty = $( element ).closest( '.tm-element-ul-product' ).find( 'select.tc-epo-field-product.tmcp-select' ).closest( '.tc-epo-element-product-holder' ).find( '.tm-qty' ).first().val();
				return qty > 0;
			},
			$.tc_validator.messages.required
		);
	}

	$.epoAPI.util.escapeSelector = ( function() {
		/* original escape string
		 *  /([!"#$%&'()*+,./:;<=>?@[\]^`{|}~])/g;
		 */
		var selectorEscape = /([!"$%&'()*+,/:;<=>?@[\]^`{|}~])/g;
		return function( selector ) {
			return selector.replace( selectorEscape, '\\$1' );
		};
	}() );

	$.epoAPI.util.unformat = function( o ) {
		var a = $.epoAPI.math.unformat( o, tcAPI.localInputDecimalSeparator );
		var n = parseFloat( a );

		if ( ! Number.isFinite( n ) ) {
			return a;
		}
		return n;
	};

	$.epoAPI.util.parseParams = function( string, decode ) {
		if ( typeof string !== 'string' || string.split === undefined ) {
			return [];
		}
		return string
			.split( '&' )
			.map( function( value ) {
				var obj = {};

				if ( decode === true ) {
					value = decodeURIComponent( value.replace( /\+/g, '%20' ) );
				}

				value = value.split( '=' ).map( function( v ) {
					var a = v.split( '?' );

					if ( a.length > 1 ) {
						return a[ 1 ];
					}
					return v;
				} );

				if ( value.length > 1 ) {
					obj[ value[ 0 ] ] = value[ 1 ];
				}

				return obj;
			} )
			.filter( function( n ) {
				return n !== null;
			} )
			.reduce( function( current, next ) {
				return Object.assign( {}, current, next );
			}, {} );
	};

	if ( ! $.tmempty ) {
		$.tmempty = function( obj ) {
			var emptyValues = [ undefined, null, false, 0, '', '0' ];
			var isEmptyValue =
				emptyValues.filter( function( item ) {
					return obj === item;
				} ).length === 1;
			var isEmptyObject = false;

			if ( typeof obj === 'object' ) {
				isEmptyObject =
					Object.keys( obj ).filter( function( key ) {
						return Object.prototype.hasOwnProperty.call( obj, key );
					} ).length === 0;
				return isEmptyObject;
			}

			return isEmptyValue || isEmptyObject;
		};
	}

	if ( ! $.tmType ) {
		$.tmType = function( obj ) {
			return Object.prototype.toString
				.call( obj )
				.match( /\s([a-zA-Z]+)/ )[ 1 ]
				.toLowerCase();
		};
	}

	if ( ! $.is_on_screen ) {
		$.fn.is_on_screen = function() {
			// we don't use jWindow because we want the current window object
			var win = $( window );
			var scroll = $.epoAPI.dom.scroll();
			var bounds = this.offset();
			var viewport = {
				top: scroll.top,
				left: scroll.left
			};

			viewport.right = viewport.left + win.width();
			viewport.bottom = viewport.top + win.height();
			bounds.right = bounds.left + this.outerWidth();
			bounds.bottom = bounds.top + this.outerHeight();

			return ! ( viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom );
		};
	}

	if ( ! $().tmtoggle ) {
		$.fn.tmtoggle = function() {
			var elements = this;
			var is_one_open_for_accordion = false;
			var init_done = 0;

			if ( elements.length === 0 ) {
				return;
			}

			if ( window.tc_accordion_offset === undefined ) {
				window.tc_accordion_offset = -100;
			}

			elements.each( function() {
				var t = $( this );
				var headers;
				var wrap;
				var wraps;

				if ( ! t.data( 'tm-toggle-init' ) ) {
					t.data( 'tm-toggle-init', 1 );
					headers = t.find( '.tm-toggle' );
					wrap = t.find( '.tm-collapse-wrap' );
					wraps = $( '.tm-collapse.tmaccordion' ).find( '.tm-toggle' );
					if ( headers.length === 0 || wrap.length === 0 ) {
						return;
					}

					if ( wrap.is( '.closed' ) ) {
						$( wrap ).removeClass( 'closed open' ).addClass( 'closed' ).hide();
						$( headers ).find( '.tm-arrow' ).removeClass( 'tcfa-angle-down tcfa-angle-up' ).addClass( 'tcfa-angle-down' );
						$( headers ).removeClass( 'toggle-header-open toggle-header-closed' ).addClass( 'toggle-header-closed' );
						$( headers ).attr( 'aria-expanded', false );
					} else {
						$( wrap ).removeClass( 'closed open' ).addClass( 'open' ).show();
						$( headers ).find( '.tm-arrow' ).removeClass( 'tcfa-angle-down tcfa-angle-up' ).addClass( 'tcfa-angle-up' );
						$( headers ).removeClass( 'toggle-header-open toggle-header-closed' ).addClass( 'toggle-header-open' );
						$( headers ).attr( 'aria-expanded', true );
						is_one_open_for_accordion = true;
					}

					headers.each( function( i, header ) {
						$( header ).on( 'closewrap.tmtoggle', function() {
							if ( t.is( '.tmaccordion' ) && $( wrap ).is( '.closed' ) ) {
								return;
							}
							$( wrap ).removeClass( 'closed open' ).addClass( 'closed' );
							$( this ).find( '.tm-arrow' ).removeClass( 'tcfa-angle-down tcfa-angle-up' ).addClass( 'tcfa-angle-down' );
							$( this ).removeClass( 'toggle-header-open toggle-header-closed' ).addClass( 'toggle-header-closed' );
							$( this ).attr( 'aria-expanded', false );
							$( wrap ).removeClass( 'tm-animated fadein' );
							if ( t.is( '.tmaccordion' ) ) {
								$( wrap ).animate( { height: 'toggle' }, 100, function() {
									$( wrap ).hide();
								} );
							} else {
								$( wrap ).animate( { height: 'toggle' }, 100, function() {
									$( wrap ).hide();
								} );
							}
							jWindow.trigger( 'tmlazy' );
						} );

						$( header ).on( 'openwrap.tmtoggle', function( e, data ) {
							if ( t.is( '.tmaccordion' ) ) {
								$( wraps ).not( $( this ) ).trigger( 'closewrap.tmtoggle' );
							}
							$( wrap ).removeClass( 'closed open' ).addClass( 'open' );
							$( this ).find( '.tm-arrow' ).removeClass( 'tcfa-angle-down tcfa-angle-up' ).addClass( 'tcfa-angle-up' );
							$( this ).removeClass( 'toggle-header-open toggle-header-closed' ).addClass( 'toggle-header-open' );
							$( this ).attr( 'aria-expanded', true );
							$( wrap ).show().removeClass( 'tm-animated fadein' ).addClass( 'tm-animated fadein' );
							setTimeout( function() {
								jWindow.trigger( 'tmlazy' );
							}, 200 );
							setTimeout( function() {
								if ( data === undefined && init_done && t.is( '.tmaccordion' ) && ! t.is_on_screen() ) {
									jWindow.tcScrollTo( $( header ), 10, window.tc_accordion_offset );
								}
							}, 110 );
						} );

						$( header ).on( 'click.tmtoggle keydown.tmtoggle', function( e ) {
							// If it's a keydown event, only trigger if the Enter key was pressed
							if ( e.type === 'keydown' && e.key !== 'Enter' ) {
								return; // Ignore all other keypresses
							}
							e.preventDefault();
							if ( $( wrap ).is( '.closed' ) ) {
								$( this ).trigger( 'openwrap.tmtoggle' );
							} else {
								$( this ).trigger( 'closewrap.tmtoggle' );
							}
						} );

						$( header )
							.find( '.tm-qty' )
							.closest( '.cpf-element' )
							.find( '.tm-epo-field' )
							.on( 'change.cpf', function() {
								$( header ).trigger( 'openwrap.tmtoggle' );
							} );
					} );
				}
			} );
			if ( undefined === window.tc_accordion_closed_on_page_load && ! is_one_open_for_accordion && elements.filter( '.tmaccordion' ).length > 0 ) {
				elements.filter( '.tmaccordion' ).first().find( '.tm-toggle' ).trigger( 'openwrap.tmtoggle', { nomove: 1 } );
			}
			init_done = 1;
			return elements;
		};
	}

	if ( ! $().tmpoplink ) {
		$.fn.tmpoplink = function( options ) {
			var elements = this;
			var floatbox_template;
			var settings = {
				classname: 'flasho tc-wrapper cart-popup'
			};

			if ( elements.length === 0 ) {
				return;
			}
			if ( options ) {
				settings = $.extend( {}, settings, options );
			}

			floatbox_template = function( data ) {
				return $.epoAPI.template.html( wp.template( 'tc-cart-options-popup' ), {
					title: data.title,
					id: data.id,
					html: data.html,
					close: TMEPOJS.i18n_close
				} );
			};

			return elements.each( function() {
				var t = $( this );
				var id;
				var title;
				var html;
				var $_html;

				if ( t.is( '.tc-poplink' ) ) {
					return;
				}
				t.addClass( 'tc-poplink' );
				id = $( this ).attr( 'href' );
				title = $( this ).attr( 'data-title' );
				html = $( id ).html();
				if ( ! title ) {
					title = TMEPOJS.i18n_addition_options;
				}
				$_html = floatbox_template( {
					id: 'tc-floatbox-content',
					html: html,
					title: title
				} );

				t.on( 'click.tmpoplink', function( e ) {
					$.tcFloatBox( {
						fps: 1,
						ismodal: false,
						refresh: 100,
						width: '80%',
						height: '80%',
						classname: settings.classname,
						data: $_html
					} );

					e.preventDefault();
				} );
			} );
		};
	}

	function readableFileSize( a, b, c, d, e ) {
		b = Math;
		c = b.log;
		d = 1024;
		e = c( a ) / c( d ) | 0; //eslint-disable-line no-bitwise

		return ( ( a / b.pow( d, e ) ).toFixed( e ? 2 : 0 ) ) + ' ' + ( e ? 'KMGTPEZY'[ --e ] + 'B' : 'bytes' );
	}

	function toggleState( thisProductContainerWrap, disabled ) {
		thisProductContainerWrap.toArray().forEach( function( setter ) {
			setter = $( setter );
			setter.find( ':input' ).prop( 'disabled', function( i, v ) {
				var state = $( this ).data( 'tc-state' );
				if ( state === undefined ) {
					$( this ).data( 'tc-state', v );
					return disabled;
				}
				if ( state === false ) {
					return disabled;
				}
				return state;
			} );
		} );
	}
	// Function to calculate tax amount
	function calculateTaxAmount( priceWithTax, _cart ) {
		var taxable;
		var tax_rate;
		var pricesIncludeTax;
		var tax_display_mode;
		var current_variation;
		var priceWithoutTax;
		var taxAmount = 0;

		if ( _cart ) {
			taxable = _cart.attr( 'data-taxable' );
			tax_rate = _cart.attr( 'data-tax-rate' );
			pricesIncludeTax = _cart.attr( 'data-prices-include-tax' ) || TMEPOJS.prices_include_tax;
			tax_display_mode = _cart.attr( 'data-tax-display-mode' );
			if ( _cart.data( 'current_variation' ) !== undefined ) {
				current_variation = _cart.data( 'current_variation' );
			}

			if ( current_variation !== undefined ) {
				current_variation = _cart.data( 'current_variation' );
				taxable = current_variation.tc_is_taxable;
				tax_rate = current_variation.tc_tax_rate;
			}
		}

		if ( taxable ) {
			tax_rate = parseFloat( tax_rate / 100 );

			if ( pricesIncludeTax === '1' ) {
				if ( tax_display_mode === 'incl' ) {
					priceWithTax = parseFloat( priceWithTax );

					// Calculate price without tax
					priceWithoutTax = priceWithTax / ( 1 + tax_rate );

					// Calculate tax amount
					taxAmount = priceWithTax - priceWithoutTax;
				} else {
					// Calculate price without tax
					priceWithoutTax = priceWithTax;

					// Calculate tax amount
					taxAmount = priceWithTax * tax_rate;
				}
			} else if ( tax_display_mode === 'incl' ) {
				// Calculate price without tax
				priceWithoutTax = priceWithTax / ( 1 + tax_rate );

				// Calculate tax amount
				taxAmount = priceWithTax - priceWithoutTax;
			}

			taxAmount = $.epoAPI.math.toFloat( taxAmount );
		}

		return taxAmount;
	}
	// Taxes setup
	function get_price_including_tax( price, _cart, element, force, variation, pricetype, doubleforce ) {
		var taxable;
		var tax_rate;
		var prices_include_tax;
		var is_vat_exempt;
		var non_base_location_prices;
		var taxes_of_one;
		var base_taxes_of_one;
		var modded_taxes_of_one;
		var current_variation;

		if ( ! Number.isFinite( parseFloat( price ) ) ) {
			price = 0;
		}
		price = price * 10000;
		if ( _cart ) {
			taxable = _cart.attr( 'data-taxable' );
			tax_rate = _cart.attr( 'data-tax-rate' );
			prices_include_tax = _cart.attr( 'data-prices-include-tax' ) || TMEPOJS.prices_include_tax;
			is_vat_exempt = _cart.attr( 'data-is-vat-exempt' );
			non_base_location_prices = _cart.attr( 'data-non-base-location-prices' );
			taxes_of_one = _cart.attr( 'data-taxes-of-one' );
			base_taxes_of_one = _cart.attr( 'data-base-taxes-of-one' );
			modded_taxes_of_one = _cart.attr( 'data-modded-taxes-of-one' );

			if ( _cart.data( 'current_variation' ) !== undefined ) {
				current_variation = _cart.data( 'current_variation' );
			} else if ( variation !== undefined ) {
				current_variation = variation;
			}

			if ( current_variation !== undefined ) {
				current_variation = _cart.data( 'current_variation' );
				taxable = current_variation.tc_is_taxable;
				tax_rate = current_variation.tc_tax_rate;
				non_base_location_prices = current_variation.tc_non_base_location_prices;
				taxes_of_one = current_variation.tc_taxes_of_one;
				base_taxes_of_one = current_variation.tc_base_taxes_of_one;
				modded_taxes_of_one = current_variation.tc_modded_taxes_of_one;
			}

			if ( element ) {
				if ( element.data( 'tax-obj' ) ) {
					tax_rate = element.data( 'tax-obj' );
					if ( tax_rate.has_fee === 'no' ) {
						taxable = false;
					} else if ( tax_rate.has_fee === 'yes' ) {
						taxable = true;
					}
					tax_rate = tax_rate.tax_rate;
					taxes_of_one = tax_rate / 100;
					base_taxes_of_one = tax_rate / 100;
				}
			}
			if ( taxable ) {
				if ( prices_include_tax === '1' && ! force ) {
					if ( is_vat_exempt === '1' ) {
						if ( non_base_location_prices === '1' ) {
							price = parseFloat( price ) - ( taxes_of_one * price );
						} else {
							price = parseFloat( price ) - ( base_taxes_of_one * price );
						}
					} else if ( non_base_location_prices === '1' ) {
						price = parseFloat( price ) - ( base_taxes_of_one * price ) + ( modded_taxes_of_one * price );
					}
				} else if ( element || current_variation === undefined || doubleforce ) {
					price = parseFloat( price ) + ( parseFloat( price ) * taxes_of_one );
				}
			}
		}
		price = price / 10000;

		return price;
	}

	function get_price_excluding_tax( price, _cart, element, force, variation, pricetype, doubleforce ) {
		var taxable;
		var tax_rate;
		var taxes_of_one;
		var base_taxes_of_one;
		var prices_include_tax;
		var current_variation;
		var tax_display_mode;

		if ( ! Number.isFinite( parseFloat( price ) ) ) {
			price = 0;
		}
		price = price * 10000;

		if ( _cart ) {
			tax_display_mode = _cart.attr( 'data-tax-display-mode' );

			taxable = _cart.attr( 'data-taxable' );
			tax_rate = _cart.attr( 'data-tax-rate' );
			taxes_of_one = _cart.attr( 'data-taxes-of-one' );
			base_taxes_of_one = _cart.attr( 'data-base-taxes-of-one' );
			prices_include_tax = _cart.attr( 'data-prices-include-tax' );

			if ( _cart.data( 'current_variation' ) !== undefined ) {
				current_variation = _cart.data( 'current_variation' );
			} else if ( variation !== undefined ) {
				current_variation = variation;
			}
			if ( current_variation !== undefined ) {
				taxable = current_variation.tc_is_taxable;
				tax_rate = current_variation.tc_tax_rate;
				taxes_of_one = current_variation.tc_taxes_of_one;
				base_taxes_of_one = current_variation.tc_base_taxes_of_one;
			}

			if ( element ) {
				if ( element.data( 'tax-obj' ) ) {
					tax_rate = element.data( 'tax-obj' );
					if ( tax_rate.has_fee === 'no' ) {
						taxable = false;
					} else if ( tax_rate.has_fee === 'yes' ) {
						taxable = true;
					}
					tax_rate = tax_rate.tax_rate;
					base_taxes_of_one = tax_rate / 100;
				}
			}

			if ( taxable ) {
				if ( prices_include_tax === '1' || force ) {
					if ( tax_display_mode === 'incl' ) {
						// This should never run on this function
					} else if ( element || current_variation === undefined || force || doubleforce ) {
						if ( base_taxes_of_one === taxes_of_one ) {
							price = parseFloat( price ) - parseFloat( taxes_of_one * price );
						} else {
							price = price * ( 1 - parseFloat( base_taxes_of_one ) );
						}
					}
				}
			}
		}
		price = price / 10000;

		return price;
	}

	function tm_set_tax_price( value, _cart, element, pricetype, force, variation, doubleforce ) {
		var tax_display_mode;

		if ( ! Number.isFinite( parseFloat( value ) ) ) {
			value = 0;
		}
		if ( _cart ) {
			tax_display_mode = _cart.attr( 'data-tax-display-mode' ) || TMEPOJS.tax_display_mode;
			if ( tax_display_mode === 'incl' ) {
				value = get_price_including_tax( value, _cart, element, force, variation, pricetype, doubleforce );
			} else {
				value = get_price_excluding_tax( value, _cart, element, force, variation, pricetype, doubleforce );
			}
		}
		return value;
	}

	// Return a formatted currency value
	function formatPrice( value, args ) {
		var data;
		if ( ! args ) {
			args = {};
		}
		data = $.extend( {
			symbol: '',
			format: '',
			decimal: tcAPI.localDecimalSeparator,
			thousand: tcAPI.localThousandSeparator,
			precision: TMEPOJS.currency_format_num_decimals
		}, args );

		if ( TMEPOJS.tm_epo_trim_zeros === 'yes' ) {
			if ( ( ( value % 1 ).toString() !== '0' ) === false ) {
				data.precision = 0;
			}
		}

		return $.epoAPI.applyFilter( 'tc_formatPrice', $.epoAPI.math.format( value, data ), data, value );
	}

	// Return a formatted currency value
	function tm_set_price_( value, sign, inc_tax_string ) {
		return (
			sign +
			formatPrice( value, { symbol: TMEPOJS.currency_format_symbol, format: TMEPOJS.currency_format } ) +
			inc_tax_string
		);
	}

	// Return a formatted currency value
	function tm_set_price( value, _cart, notax, taxstring, element, pricetype ) {
		var inc_tax_string = '';
		var val;
		var sign = TMEPOJS.option_plus_sign + ' ';

		if ( ! notax ) {
			value = tm_set_tax_price( value, _cart, element, pricetype );
		}

		val = Math.abs( value );

		if ( TMEPOJS.tm_epo_global_options_price_sign === 'minus' ) {
			sign = '';
		}
		if ( value < 0 ) {
			sign = TMEPOJS.option_minus_sign + ' ';
		}

		if ( _cart && taxstring ) {
			inc_tax_string = _cart.attr( 'data-tax-string' );
		}
		if ( inc_tax_string === undefined ) {
			inc_tax_string = '';
		}

		return tm_set_price_( val, sign, inc_tax_string );
	}

	// Return a price currency value without any formatting
	function tm_get_price( value, _cart, notax, element, pricetype, force ) {
		if ( ! notax ) {
			value = tm_set_tax_price( value, _cart, element, pricetype, force );
		}

		return value;
	}

	// FloatingTotalsBox plugin
	FloatingTotalsBox = function( this_epo_totals_container, is_quickview, main_cart ) {
		this.this_epo_totals_container = this_epo_totals_container;
		this.is_quickview = is_quickview;
		this.main_cart = main_cart;

		if ( ! is_quickview && TMEPOJS.floating_totals_box && TMEPOJS.floating_totals_box !== 'disable' && main_cart && this_epo_totals_container.length ) {
			this.init();
			return this;
		}

		return false;
	};

	FloatingTotalsBox.prototype = {
		constructor: FloatingTotalsBox,

		onUpdate: function() {
			var tm_epo_totals_html = this.this_epo_totals_container.data( 'tm-html' );
			var tm_floating_box_data = this.this_epo_totals_container.data( 'tm-floating-box-data' );
			var values_obj = [];
			var floatingBoxHtml;
			var floatingBoxaddToCartButton;

			if ( tm_floating_box_data && tm_floating_box_data.length ) {
				$.each( tm_floating_box_data, function( i, row ) {
					if ( row.title === '' ) {
						row.title = '&nbsp;';
					}
					if ( row.value === '' ) {
						row.value = '&nbsp;';
					}
					if ( ! row.title ) {
						row.title = '&nbsp;';
					} else {
						row.title = $( '<div>' + row.title + '</div>' );
						row.title.find( 'span' ).remove();
						row.title = row.title.html();
					}

					if ( this.is_nks ) {
						if ( row.label_show !== '' ) {
							row.title = '';
						}
						if ( row.value_show !== '' ) {
							row.value = '';
						}
					}

					if ( TMEPOJS.tm_epo_auto_hide_price_if_zero === 'yes' && $.tmempty( row.price ) === true ) {
						row.price = '';
					} else {
						row.price = tm_set_price( row.price, this.this_epo_totals_container, true, false );
					}

					values_obj.push( {
						label_show: row.label_show,
						value_show: row.value_show,
						title: row.title,
						value: row.value,
						quantity: row.quantity,
						price: row.price
					} );
				} );
			}

			if ( ( ( tm_epo_totals_html && tm_epo_totals_html === '' ) && ( ! tm_floating_box_data || ! tm_floating_box_data.length ) ) || ! ( ( tm_epo_totals_html && tm_epo_totals_html !== '' ) || this.is_nks ) ) {
				tm_epo_totals_html = '';
				this.floatingBox.hide();
			}

			floatingBoxHtml = $.epoAPI.template.html( this.engineTemplate, {
				html_before: TMEPOJS.floating_totals_box_html_before,
				html_after: TMEPOJS.floating_totals_box_html_after,
				option_label: TMEPOJS.i18n_option_label,
				option_value: TMEPOJS.i18n_option_value,
				option_qty: TMEPOJS.i18n_option_qty,
				option_price: TMEPOJS.i18n_option_price,
				values: values_obj,
				totals: tm_epo_totals_html
			} );

			this.floatingBox.html( floatingBoxHtml );
			this.onUpdateScroll();

			if ( TMEPOJS.floating_totals_box_add_button === 'yes' ) {
				floatingBoxaddToCartButton = this.main_cart.find( tcAPI.addToCartButtonSelector ).first();
				floatingBoxaddToCartButton
					.tcClone()
					.addClass( 'tc-add-to-cart-button' )
					.on( 'click', function() {
						floatingBoxaddToCartButton.trigger( 'click' );
					} )
					.appendTo( this.floatingBox );
			}
		},

		onUpdateScroll: function() {
			if ( TMEPOJS.floating_totals_box_visibility === 'always' ) {
				if ( this.floatingBox.is( ':empty' ) && ! this.is_nks_alt ) {
					this.floatingBox.hide();
				} else {
					this.floatingBox.show();
				}
				return;
			}
			if ( TMEPOJS.floating_totals_box_visibility === 'hideafterscroll' ) {
				if ( jWindow.scrollTop() > $.epoAPI.math.toFloat( TMEPOJS.floating_totals_box_pixels ) && ! this.is_nks_alt ) {
					if ( ! this.floatingBox.is( ':hidden' ) ) {
						if ( this.is_nks === false ) {
							this.floatingBox.fadeOut();
						} else {
							this.floatingBox.hide();
						}
					}
				} else if ( this.floatingBox.is( ':hidden' ) || this.is_nks_alt ) {
					if ( ! this.floatingBox.is( ':empty' ) || this.is_nks_alt ) {
						if ( this.is_nks === false ) {
							this.floatingBox.fadeIn();
						} else {
							this.floatingBox.show();
						}
					}
				}
			}

			if ( TMEPOJS.floating_totals_box_visibility === 'afterscroll' ) {
				if ( jWindow.scrollTop() > $.epoAPI.math.toFloat( TMEPOJS.floating_totals_box_pixels ) || this.is_nks_alt ) {
					if ( ( this.floatingBox.is( ':hidden' ) && ! this.floatingBox.is( ':empty' ) ) || this.is_nks_alt ) {
						if ( this.is_nks === false ) {
							this.floatingBox.fadeIn();
						} else {
							this.floatingBox.show();
						}
					} else if ( ! this.floatingBox.is( ':hidden' ) && this.floatingBox.is( ':empty' ) ) {
						if ( this.is_nks === false ) {
							this.floatingBox.fadeOut();
						} else {
							this.floatingBox.hide();
						}
					}
				} else if ( ! this.floatingBox.is( ':hidden' ) ) {
					if ( this.is_nks === false ) {
						this.floatingBox.fadeOut();
					} else {
						this.floatingBox.hide();
					}
				}
			}
		},

		addEvents: function() {
			this.onUpdate();

			this.main_cart.on( 'tm-epo-after-update', this.onUpdate.bind( this ) );
			this.main_cart.on( 'tm-epo-short-update', this.onUpdate.bind( this ) );

			if ( this.is_nks === false ) {
				jWindow.on( 'scroll', this.onUpdateScroll.bind( this ) );
			}
		},

		init: function() {
			this.floatingBox = $( '<div class="tm-floating-box ' + TMEPOJS.floating_totals_box + '"></div>' );
			this.nks_selector = $( '.tm-floating-box-nks' ).first();
			this.alt_selector = $( '.tm-floating-box-alt' ).first();
			this.engineTemplate = tcAPI.templateEngine.tc_floating_box;
			this.is_nks = false;
			this.is_nks_alt = false;

			if ( this.nks_selector.length > 0 ) {
				this.is_nks = true;
				this.floatingBox.removeClass( 'top left right bottom' ).appendTo( this.nks_selector ).show();
			} else if ( this.alt_selector.length > 0 ) {
				this.floatingBox.removeClass( 'top left right bottom' ).appendTo( this.alt_selector ).hide();
			} else {
				this.floatingBox.appendTo( 'body' ).hide();
			}

			if ( this.nks_selector.length > 0 || this.alt_selector.length > 0 ) {
				this.is_nks_alt = true;
				this.engineTemplate = tcAPI.templateEngine.tc_floating_box_nks;
			}

			this.addEvents();
		}
	};

	$.tcFloatingTotalsBox = function( this_epo_totals_container, is_quickview, main_cart ) {
		var data = false;

		if ( this_epo_totals_container && this_epo_totals_container.length && this_epo_totals_container.data( 'tcfloatingtotalsbox' ) === undefined ) {
			data = new FloatingTotalsBox( this_epo_totals_container, is_quickview, main_cart );
			this_epo_totals_container.data( 'tcfloatingtotalsbox', data );
		}

		return data;
	};

	$.tc_product_image = {};
	$.tc_product_image_store = {};

	// replace obj1 values with obj2 values
	$.tc_replace_object_values = function( obj1, obj2 ) {
		Object.keys( obj1 ).forEach( function( x ) {
			Object.keys( obj1[ x ] ).forEach( function( attr ) {
				if ( undefined !== obj2[ x ] && undefined !== obj2[ x ][ attr ] && Object.prototype.hasOwnProperty.call( obj2[ x ], attr ) ) {
					obj1[ x ][ attr ] = obj2[ x ][ attr ];
				}
			} );
		} );
		return obj1;
	};
	// copy obj2 values to obj1
	$.tc_maybe_copy_object_values = function( obj1, obj2 ) {
		Object.keys( obj2 ).forEach( function( x ) {
			Object.keys( obj2[ x ] ).forEach( function( attr ) {
				if ( undefined !== obj2[ x ] && Object.prototype.hasOwnProperty.call( obj2[ x ], attr ) && undefined !== obj2[ x ][ attr ] && ( undefined === obj1[ x ] || undefined === obj1[ x ][ attr ] ) ) {
					if ( undefined === obj1[ x ] ) {
						obj1[ x ] = {};
					}
					obj1[ x ][ attr ] = obj2[ x ][ attr ];
				}
			} );
		} );
		return obj1;
	};

	$.tc_pre_populate_store = function() {
		var obj = {};

		obj[ 0 ] = {};
		obj[ 1 ] = {};
		obj[ 2 ] = {};
		obj[ 3 ] = {};

		obj[ 0 ].src = '';
		obj[ 0 ].srcset = '';
		obj[ 0 ].sizes = '';
		obj[ 0 ].title = '';
		obj[ 0 ].alt = '';
		obj[ 0 ][ 'data-src' ] = '';
		obj[ 0 ][ 'data-large_image' ] = '';
		obj[ 0 ][ 'data-large_image_width' ] = '';
		obj[ 0 ][ 'data-large_image_height' ] = '';
		obj[ 1 ][ 'data-thumb' ] = '';
		obj[ 2 ].src = '';
		obj[ 3 ].href = '';
		obj[ 3 ].title = '';

		return obj;
	};

	$.tc_populate_store = function( img, product_element ) {
		var $gallery_img = product_element.find( '.flex-control-nav li:eq(0) img' );
		var $gallery_wrapper = product_element.find( '.woocommerce-product-gallery__wrapper ' );
		var $product_img_wrap = $gallery_wrapper.find( '.woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder' ).eq( 0 );
		var $product_img = img;
		var $product_link = img.closest( 'a' );
		var obj = {};

		obj[ 0 ] = {};
		obj[ 1 ] = {};
		obj[ 2 ] = {};
		obj[ 3 ] = {};

		obj[ 0 ].src = $product_img.attr( 'src' );
		obj[ 0 ].srcset = $product_img.attr( 'srcset' );
		obj[ 0 ].sizes = $product_img.attr( 'sizes' );
		obj[ 0 ].title = $product_img.attr( 'title' );
		obj[ 0 ].alt = $product_img.attr( 'alt' );
		obj[ 0 ][ 'data-src' ] = $product_img.attr( 'data-src' );
		obj[ 0 ][ 'data-large_image' ] = $product_img.attr( 'data-large_image' );
		obj[ 0 ][ 'data-large_image_width' ] = $product_img.attr( 'data-large_image_width' );
		obj[ 0 ][ 'data-large_image_height' ] = $product_img.attr( 'data-large_image_height' );
		obj[ 1 ][ 'data-thumb' ] = $product_img_wrap.attr( 'data-thumb' );
		obj[ 2 ].src = $gallery_img.attr( 'src' );
		obj[ 3 ].href = $product_link.attr( 'href' );
		obj[ 3 ].title = $product_link.attr( 'title' );

		return obj;
	};

	$.tc_maybe_copy_object_values_from_img = function( obj1, img, product_element ) {
		var $gallery_img = product_element.find( '.flex-control-nav li:eq(0) img' );
		var $gallery_wrapper = product_element.find( '.woocommerce-product-gallery__wrapper ' );
		var $product_img_wrap = $gallery_wrapper.find( '.woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder' ).eq( 0 );
		var $product_img = img;
		var $product_link = img.closest( 'a' );
		var attrs;
		var attr;
		var attrs_product_img = [ 'src', 'srcset', 'sizes', 'title', 'alt', 'data-src', 'data-large_image', 'data-large_image_width', 'data-large_image_height', 'large-image' ];
		var attrs_product_img_wrap = [ 'data-thumb' ];
		var attrs_gallery_img = [ 'src' ];
		var attrs_product_link = [ 'href', 'title' ];
		var all = [ $product_img, $product_img_wrap, $gallery_img, $product_link ];
		var attrs_all = [ attrs_product_img, attrs_product_img_wrap, attrs_gallery_img, attrs_product_link ];
		all.forEach( function( item, index ) {
			if ( undefined !== item && undefined !== item[ 0 ] ) {
				attrs = item[ 0 ].attributes;

				$.each( attrs, function() {
					if ( this.specified ) {
						attr = this.name;

						if ( $.inArray( attr, attrs_all[ index ] ) !== -1 && ( undefined === obj1[ index ] || ( undefined !== obj1[ index ] && undefined === obj1[ index ][ attr ] ) ) ) {
							if ( undefined === obj1[ index ] ) {
								obj1[ index ] = {};
							}
							obj1[ index ][ attr ] = this.value;
						}
					}
				} );
			}
		} );

		return obj1;
	};

	// Stores a default attribute for an element so it can be reset later
	$.fn.tc_set_attr = function( attr, value, id ) {
		if ( undefined === id ) {
			id = 0;
		}
		if ( undefined === $.tc_product_image[ id ] || ( undefined !== $.tc_product_image[ id ] && undefined === $.tc_product_image[ id ][ attr ] ) ) {
			if ( undefined === $.tc_product_image[ id ] ) {
				$.tc_product_image[ id ] = {};
			}
			$.tc_product_image[ id ][ attr ] = '';
			if ( this.attr( attr ) ) {
				$.tc_product_image[ id ][ attr ] = this.attr( attr );
			}
		}
		if ( false === value ) {
			this.removeAttr( attr );
		} else {
			this.attr( attr, value );
		}
	};

	// Reset a default attribute for an element so it can be reset later
	$.fn.tc_reset_attr = function( attr, id ) {
		if ( undefined === id ) {
			id = 0;
		}
		if ( undefined === $.tc_product_image[ id ] ) {
			return;
		}
		if ( undefined !== $.tc_product_image[ id ][ attr ] ) {
			this.attr( attr, $.tc_product_image[ id ][ attr ] );
		}
		delete $.tc_product_image[ id ][ attr ];
	};

	$.fn.tc_update_attr = function( attr, id ) {
		if ( undefined === id ) {
			id = 0;
		}
		if ( undefined !== $.tc_product_image[ id ] ) {
			$.tc_product_image[ id ][ attr ] = this.attr( attr );
		}
	};

	$.fn.tc_image_update = function( dom, image ) {
		var element = $( dom );
		var $form = this;
		var $image = $( image );
		var epo_object = $form.data( 'epo_object' );
		var image_info;
		var $product_img;
		var product_element = epo_object.main_product.closest( '#product-' + epo_object.product_id );
		var $product_element = product_element;
		var $product_link;
		var use_image_info;

		if ( product_element.length <= 0 ) {
			$product_element = epo_object.main_product.closest( '.post-' + epo_object.product_id );
		}

		if ( element.is( 'select' ) ) {
			element = element.children( 'option:selected' );
		}
		image_info = element.data( 'image-variations' );

		if ( TMEPOJS.tm_epo_global_product_image_selector !== '' ) {
			$product_img = $( TMEPOJS.tm_epo_global_product_image_selector );
		} else {
			$product_img = $product_element.find( 'a.woocommerce-main-image img, img.woocommerce-main-image,a img' ).not( '.thumbnails img,.product_list_widget img,img.emoji,a.woocommerce-product-gallery__trigger img' ).first();
		}
		$product_link = $product_img.closest( 'a' );

		if ( $product_img.length > 1 ) {
			$product_img = $product_img.first();
		}

		if ( element && image_info && $image.length > 0 ) {
			$image.removeAttr( 'data-o_src' ).removeAttr( 'data-o_title' ).removeAttr( 'data-o_alt' ).removeAttr( 'data-o_srcset' ).removeAttr( 'data-o_sizes' ).removeAttr( 'srcset' ).removeAttr( 'sizes' );

			use_image_info = image_info.imagep;
			if ( ! image_info.imagep.image_link ) {
				use_image_info = image_info.image;
			}

			$image.attr( 'title', use_image_info.image_title );
			$image.attr( 'alt', use_image_info.image_alt );
			if ( use_image_info.image_srcset ) {
				$image.attr( 'srcset', use_image_info.image_srcset );
			}
			if ( use_image_info.image_sizes ) {
				$image.attr( 'sizes', use_image_info.image_sizes );
			}

			$product_img.tc_set_attr( 'title', use_image_info.image_title );
			$product_img.tc_set_attr( 'alt', use_image_info.image_alt );

			$product_img.tc_set_attr( 'data-large-image', use_image_info.image_link );
			if ( $product_img.data.wc27_zoom_target ) {
				$product_img.data.wc27_zoom_target.tc_set_attr( 'data-thumb', use_image_info.image_link );
				$product_element.find( '.flex-control-nav li:eq(0) img' ).tc_set_attr( 'src', use_image_info.image_link );
			}

			$product_link.tc_set_attr( 'href', use_image_info.image_link );
			$product_link.tc_set_attr( 'title', use_image_info.image_caption );
		} else {
			$product_img.tc_reset_attr( 'title' );
			$product_img.tc_reset_attr( 'alt' );

			$product_img.tc_reset_attr( 'data-large-image' );
			if ( $product_img.data.wc27_zoom_target ) {
				$product_img.data.wc27_zoom_target.tc_reset_attr( 'data-thumb' );
				$product_element.find( '.flex-control-nav li:eq(0) img' ).tc_reset_attr( 'src' );
			}

			$product_link.tc_reset_attr( 'href' );
			$product_link.tc_reset_attr( 'title' );
		}
	};

	// variations checker
	$.fn.tm_find_matching_variations = function( product_variations, settings ) {
		var matching = [];
		var i;
		var variation;

		if ( product_variations ) {
			for ( i = 0; i < product_variations.length; i += 1 ) {
				variation = product_variations[ i ];

				if ( $.fn.tm_variations_match( variation.attributes, settings ) ) {
					matching.push( variation );
				}
			}
		}

		return matching;
	};

	$.fn.tm_variations_match = function( attrs1, attrs2 ) {
		var match = true;
		var val1;
		var val2;

		Object.keys( attrs1 ).forEach( function( x ) {
			if ( Object.prototype.hasOwnProperty.call( attrs1, x ) ) {
				val1 = attrs1[ x ];
				val2 = attrs2[ x ];

				if ( val1 !== undefined && val2 !== undefined && val1.length !== 0 && val2.length !== 0 && val1 !== val2 ) {
					match = false;
				}
			}
		} );

		return match;
	};

	function get_element_from_field( element ) {
		var $element = $( element );
		var data_uniqid;
		var the_epo_id;
		var _class;
		var epoContainer;

		if ( $element.length === 0 ) {
			return;
		}

		if ( $element.is( '.cpf-section' ) ) {
			return element.find( '.tm-epo-field' );
		}
		data_uniqid = $element.attr( 'data-uniqid' );
		epoContainer = $element.closest( '.tc-extra-product-options' );
		the_epo_id = epoContainer.attr( 'data-epo-id' );

		if ( ! epoContainer.is( '.reactivate' ) && tcAPI.getElementFromFieldCache && tcAPI.getElementFromFieldCache[ the_epo_id ] && tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ] ) {
			return tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ];
		}
		_class = $element
			.attr( 'class' )
			.split( ' ' )
			.map( function( cls ) {
				if ( cls.indexOf( 'cpf-type-', 0 ) !== -1 ) {
					return cls;
				}
				return null;
			} )
			.filter( function( v ) {
				if ( v !== null && v !== undefined ) {
					return v;
				}
				return null;
			} );

		if ( _class.length > 0 ) {
			_class = _class[ 0 ];
			if ( _class === 'cpf-type-product' ) {
				if ( $element.is( '.cpf-type-product-mode-product' ) || $element.is( '.cpf-type-product-checkbox' ) || $element.is( '.cpf-type-product-thumbnailmultiple' ) ) {
					_class = 'cpf-type-checkbox';
				} else if ( $element.is( '.cpf-type-product-dropdown' ) ) {
					_class = 'cpf-type-select';
				} else {
					_class = 'cpf-type-radio';
				}
			}
			switch ( _class ) {
				case 'cpf-type-radio':
					tcAPI.getElementFromFieldCache[ the_epo_id ] = [];
					tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ] = $element.find( '.tm-epo-field.tmcp-radio' );
					return tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ];

				case 'cpf-type-checkbox':
					tcAPI.getElementFromFieldCache[ the_epo_id ] = [];
					tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ] = $element.find( '.tm-epo-field.tmcp-checkbox' );
					return tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ];

				case 'cpf-type-select':
					tcAPI.getElementFromFieldCache[ the_epo_id ] = [];
					tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ] = $element.find( '.tm-epo-field.tmcp-select' );
					return tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ];

				case 'cpf-type-selectmultiple':
					tcAPI.getElementFromFieldCache[ the_epo_id ] = [];
					tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ] = $element.find( '.tm-epo-field.tmcp-selectmultiple' );
					return tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ];

				case 'cpf-type-textarea':
					tcAPI.getElementFromFieldCache[ the_epo_id ] = [];
					tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ] = $element.find( '.tm-epo-field.tmcp-textarea' );
					return tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ];

				case 'cpf-type-textfield':
					tcAPI.getElementFromFieldCache[ the_epo_id ] = [];
					tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ] = $element.find( '.tm-epo-field.tmcp-textfield' );
					return tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ];

				case 'cpf-type-color':
					tcAPI.getElementFromFieldCache[ the_epo_id ] = [];
					tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ] = $element.find( '.tm-epo-field.tm-color-picker' );
					return tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ];

				case 'cpf-type-range':
					tcAPI.getElementFromFieldCache[ the_epo_id ] = [];
					tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ] = $element.find( '.tm-epo-field.tmcp-range' );
					return tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ];

				case 'cpf-type-date':
					tcAPI.getElementFromFieldCache[ the_epo_id ] = [];
					tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ] = $element.find( '.tm-epo-field.tmcp-date' );
					return tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ];

				case 'cpf-type-time':
					tcAPI.getElementFromFieldCache[ the_epo_id ] = [];
					tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ] = $element.find( '.tm-epo-field.tmcp-time' );
					return tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ];

				case 'cpf-type-variations':
					tcAPI.getElementFromFieldCache[ the_epo_id ] = [];
					tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ] = $element.closest( '.cpf-section' ).find( '.tm-epo-field.tm-epo-variation-element' );
					return tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ];
			}
		}
	}

	// tc-lightbox
	if ( ! $().tclightbox ) {
		$.fn.tclightbox = function() {
			var elements = this;

			if ( elements.length === 0 ) {
				return;
			}

			return elements.each( function() {
				var $this = $( this );
				var _imgsrc;
				var _label;
				var _input;
				var tclightboxwrap;
				var _img_button;
				var preload_img;
				var addButtonEvent;

				if ( $this.is( '.tcinit' ) ) {
					return;
				}
				_imgsrc = $this.attr( 'src' ) || $this.attr( 'data-original' );
				_label = $this.closest( 'label' );
				_input = _label.closest( '.tmcp-field-wrap' ).find( ".tm-epo-field[id='" + _label.attr( 'for' ) + "']" );
				_imgsrc = _input.attr( 'data-imagel' ) || _input.attr( 'data-imagep' ) || _input.attr( 'data-image' ) || _imgsrc;

				if ( ! _imgsrc ) {
					return;
				}

				$this.addClass( 'tcinit' ).before( $.epoAPI.template.html( tcAPI.templateEngine.tc_lightbox, {} ) );
				tclightboxwrap = $this.prev();

				$this.wrap( "<div class='tc-lightbox-image-wrap'/>" );
				$this.after( tclightboxwrap );

				_img_button = tclightboxwrap.find( '.tc-lightbox-button' );

				addButtonEvent = function() {
					_img_button.addClass( 'tcinit' ).on( 'click.tclightbox', function( buttonevent ) {
						var size;
						var _img;

						if ( $( '.tc-closing.tc-lightbox' ).length > 0 ) {
							return;
						}

						size = $.epoAPI.dom.size();
						_img = $( '<img>' )
							.addClass( 'tc-lightbox-img' )
							.attr( 'src', _imgsrc )
							.css( 'maxHeight', size.visibleHeight + 'px' )
							.css( 'maxWidth', size.visibleWidth + 'px' );

						$.tcFloatBox( {
							fps: 1,
							ismodal: false,
							refresh: 'fixed',
							width: 'auto',
							height: 'auto',
							top: '0%',
							left: '0%',
							classname: 'flasho tc-lightbox',
							animateIn: 'tc-lightbox-zoomin',
							animateOut: 'tc-lightbox-zoomout',
							data: $.epoAPI.template.html( tcAPI.templateEngine.tc_lightbox_zoom, { img: _img[ 0 ].outerHTML } ),
							zIndex: 102001,
							cancelClass: '.tc-lightbox-img, .tc-lightbox-button-close',
							unique: true
						} );

						buttonevent.preventDefault();
					} );
				};
				if ( TMEPOJS.tm_epo_preload_lightbox_image === 'yes' ) {
					preload_img = new Image();
					preload_img.src = _imgsrc;
					preload_img.onload = function() {
						addButtonEvent();
					};
				} else {
					addButtonEvent();
				}
			} );
		};
	}

	// Start Section popup
	if ( ! $().tmsectionpoplink ) {
		$.fn.tmsectionpoplink = function() {
			var elements = this;

			if ( elements.length === 0 ) {
				return;
			}

			return elements.each( function() {
				var $this = $( this );
				var id;
				var title;
				var section;
				var clicked;
				var _ovl;
				var cancelfunc;

				if ( $this.data( 'tmsectionpoplink' ) ) {
					return;
				}

				$this.data( 'tmsectionpoplink', true );
				id = $this.attr( 'data-sectionid' );
				title = TMEPOJS.i18n_addition_options;
				section = $this.closest( ".cpf-section[data-uniqid='" + id + "']" );
				clicked = false;
				_ovl = $( '<div class="fl-overlay"></div>' ).css( {
					zIndex: parseInt( $this.zIndex, 10 ) - 1,
					opacity: 0.8
				} );
				cancelfunc = function() {
					var pop = $( '#tm-section-pop-up' );
					pop.parents().removeClass( 'noanimated' );

					_ovl.unbind().remove();
					pop.after( section );
					pop.remove();

					section.find( '.tm-section-link' ).show();
					section.find( '.tm-section-pop' ).hide();
				};

				if ( $this.attr( 'data-title' ) ) {
					title = $this.attr( 'data-title' );
				}

				$this.on( 'click.tmsectionpoplink', function( e ) {
					var pop;

					e.preventDefault();
					clicked = false;
					_ovl.appendTo( 'body' ).on( 'click', cancelfunc );

					section.before(
						$.epoAPI.template.html( tcAPI.templateEngine.tc_section_pop_link, {
							title: title,
							close: TMEPOJS.i18n_close
						} )
					);

					pop = $( '#tm-section-pop-up' );

					pop.find( '.float-editbox' ).prepend( section );

					section.find( '.tm-section-link' ).hide();
					section.find( '.tm-section-pop' ).show();

					pop.parents().addClass( 'noanimated' );

					pop.find( '.floatbox-cancel' ).on( 'click', function() {
						if ( clicked ) {
							return;
						}
						clicked = true;
						cancelfunc();
					} );
					jWindow.trigger( 'tmlazy' );
					jWindow.trigger( 'tmsectionpoplink' );
				} );
			} );
		};
	} // End Section popup

	function getVariationIdSelector( currentCart ) {
		var variationIdSelector = "input[name^='variation_id']";

		if ( currentCart.find( 'input.variation_id' ).length > 0 ) {
			variationIdSelector = 'input.variation_id';
		} else {
			variationIdSelector = 'input.product-variation-id';
		}

		return variationIdSelector;
	}

	function getVariationIdElement( currentCart, not ) {
		var variationIdSelector = getVariationIdSelector( currentCart );
		var variationIdElement = currentCart.find( variationIdSelector );

		if ( currentCart.is( '.tc-epo-element-product-container-cart' ) ) {
			variationIdElement = currentCart.closest( '.tc-epo-element-product-li-container' ).find( variationIdSelector );
		} else {
			variationIdElement = currentCart.find( variationIdSelector ).not( '.tc-epo-element-product-container-variation-id ' + variationIdSelector );
		}
		if ( not ) {
			variationIdElement = variationIdElement.not( not );
		}

		return variationIdElement;
	}

	function getCurrentVariation( currentCart ) {
		var field_div;
		var newCart;

		if ( currentCart.is( '.tc-epo-element-product-container-cart' ) ) {
			field_div = currentCart.closest( '.cpf-element' );
			if ( field_div.is( '.cpf-type-product-thumbnailmultiple' ) || field_div.is( '.cpf-type-product-checkbox' ) ) {
				newCart = currentCart.closest( '.tc-epo-element-product-holder' ).find( '.tc-epo-element-product-container-variation-id' );
			} else if ( field_div.is( '.cpf-type-product-thumbnail' ) || field_div.is( '.cpf-type-product-radio' ) || field_div.is( '.cpf-type-product-dropdown' ) || field_div.is( '.cpf-type-product-single' ) ) {
				newCart = field_div.find( '.tc-epo-element-product-container-variation-id' );
			}
			if ( newCart && newCart.length ) {
				currentCart = newCart;
			}
		}
		return currentCart.find( getVariationIdSelector( currentCart ) ).val() || 0;
	}

	function getQtyElement( currentCart ) {
		var qty = currentCart.find( tcAPI.qtySelector ).last();
		if ( qty.length === 0 ) {
			qty = currentCart.find( tcAPI.associateQtySelector ).last();
		}
		qty = $.epoAPI.applyFilter( 'tc_getQtyElement', qty, currentCart );
		return qty;
	}

	function getCurrentQty( currentCart ) {
		return $.epoAPI.applyFilter( 'tc_getCurrentQty', $.epoAPI.math.toFloat( getQtyElement( currentCart ).val() ), currentCart );
	}

	function add_variation_event( name, selector, func ) {
		lateVariationEvent[ lateVariationEvent.length ] = {
			name: name,
			selector: selector,
			func: func
		};
	}

	function field_is_active( field, nochecks, single ) {
		var ret = true;
		var insideProductCheck = false;
		var productElement;
		field = $( field );
		productElement = field.filter( '.tc-epo-field-product' );
		if ( productElement.length && productElement.data( 'islogicinit' ) === undefined ) {
			insideProductCheck = true;
		}
		field.each( function( j, element ) {
			if ( ! ret && field.has( '.tc-epo-field-product' ).length ) {
				if ( insideProductCheck || productElement.is( '.tcdisabled' ) ) {
					field_check_active( element, nochecks, single );
				}
			} else {
				ret = field_check_active( element, nochecks, single ) && ret;
			}
		} );

		return ret;
	}

	function field_check_active( field, nochecks, single ) {
		var hideElement;
		var singleField;
		var ul;
		var isInsideProductElement;
		var productElementActive;
		var productElementSectionActive;
		var productElementQty;
		var section;
		var disableElements;
		var sectionLi;
		var allSectionsDisabled;

		field = $( field );
		if ( field.is( '.cpf-element' ) ) {
			hideElement = field;
			field = field.find( '.tmcp-field, .tmcp-fee-field' );
		} else {
			hideElement = field.closest( '.cpf-element' );
		}

		section = hideElement.closest( '.cpf-section' );
		sectionLi = section.closest( '.tm-extra-product-options-field' );

		if ( single ) {
			ul = field.closest( '.tmcp-ul-wrap' );
		} else {
			ul = hideElement;
		}

		if ( field.is( '.tc-epo-field-product' ) && field.data( 'islogicinit' ) === undefined ) {
			field.data( 'islogicinit', 1 );
		}

		isInsideProductElement = ! field.is( '.tc-epo-field-product' ) && hideElement.closest( '.cpf-type-product' );

		if ( isInsideProductElement.length ) {
			productElementQty = isInsideProductElement.find( tcAPI.associateQtySelector );
			if ( productElementQty.length ) {
				if ( $.epoAPI.math.toFloat( productElementQty.val() ) > 0 ) {
					field.removeClass( 'ignore' );
					productElementQty.removeClass( 'ignore' );
				} else {
					field.addClass( 'ignore' );
					productElementQty.addClass( 'ignore' );
				}
			}
			productElementActive = isInsideProductElement.data( 'isactive' );
			isInsideProductElement = isInsideProductElement.closest( '.cpf-section' );
			productElementSectionActive = isInsideProductElement.data( 'isactive' );
		}
		if ( productElementActive !== false && productElementSectionActive !== false && hideElement.data( 'isactive' ) !== false && section.data( 'isactive' ) !== false ) {
			singleField = field.first();
			field.prop( 'disabled', false );
			if ( field.data( 'tc-state' ) !== undefined ) {
				field.data( 'tc-state', false );
			}

			if ( TMEPOJS.tm_epo_show_only_active_quantities !== 'yes' ) {
				if ( singleField.is( ':radio, .cpf-type-radio' ) || singleField.is( ':checkbox, .cpf-type-checkbox' ) ) {
					field.filter( ':checked' ).closest( '.tmcp-field-wrap' ).find( '.tm-qty' ).prop( 'disabled', false );
					field.not( ':checked' ).closest( '.tmcp-field-wrap' ).find( '.tm-qty' ).prop( 'disabled', true );
				} else if ( singleField.is( 'select, .cpf-type-select, .cpf-type-selectmultiple' ) ) {
					if ( singleField.val() ) {
						ul.find( '.tm-qty' ).prop( 'disabled', false );
					} else {
						ul.find( '.tm-qty' ).prop( 'disabled', true );
					}
				} else if ( singleField.val() ) {
					ul.find( '.tm-qty' ).prop( 'disabled', false );
				} else {
					ul.find( '.tm-qty' ).prop( 'disabled', true );
				}
			} else if ( ! nochecks ) {
				hideElement.find( '.tm-quantity' ).trigger( 'showhide.cpfcustom' );
			}

			if ( ! singleField.is( '.cpf-element' ) ) {
				field.removeClass( 'tcdisabled' ).addClass( 'tcenabled' );

				if ( field.is( '.tmcp-upload' ) ) {
					if ( field.next( '.tmcp-upload-hidden' ).length ) {
						field.next( '.tmcp-upload-hidden' ).removeClass( 'tcdisabled' ).addClass( 'tcenabled' ).prop( 'disabled', false );
					}
				}
			}

			hideElement.removeClass( 'tc-container-disabled' ).addClass( 'tc-container-enabled' );
			hideElement.find( '.product-variation-id, .tc-epo-field-product-counter' ).prop( 'disabled', false );

			field.trigger( {
				type: 'tm-field-is-active',
				field: field,
				value: true
			} );
			section.removeClass( 'section-disabled' );
			sectionLi.removeClass( 'tc-hidden' );

			return true;
		}

		if ( ! field.is( '.cpf-element' ) ) {
			field.prop( 'disabled', true ).removeClass( 'tcenabled' ).addClass( 'tcdisabled' );
			if ( field.data( 'tc-state' ) !== undefined ) {
				field.data( 'tc-state', true );
			}
			hideElement.find( '.tm-qty' ).prop( 'disabled', true );
			if ( field.is( '.tmcp-upload' ) ) {
				if ( field.next( '.tmcp-upload-hidden' ).length ) {
					field.next( '.tmcp-upload-hidden' ).removeClass( 'tcenabled' ).addClass( 'tcdisabled' ).prop( 'disabled', true );
				}
			}
		}

		hideElement.removeClass( 'tc-container-enabled' ).addClass( 'tc-container-disabled' );
		hideElement.find( '.product-variation-id, .tc-epo-field-product-counter' ).prop( 'disabled', true );

		field.trigger( {
			type: 'tm-field-is-active',
			field: field,
			value: false
		} );

		disableElements = section.find( '.cpf-element.tc-container-enabled' ).length === 0;
		if ( disableElements ) {
			section.addClass( 'section-disabled' );
			allSectionsDisabled = sectionLi.children( '.cpf-section' ).filter( function() {
				return ! $( this ).hasClass( 'section-disabled' );
			} ).length === 0;
			if ( allSectionsDisabled ) {
				sectionLi.addClass( 'tc-hidden' );
			}
		}

		return false;
	}

	function tm_variation_check_match( element, val2, operator ) {
		var $element = $( element );
		var epoId = $element.attr( 'data-epo_id' );
		var productId = $element.attr( 'data-product_id' );

		var variationsForm = $( ".variations_form[data-epo_id='" + epoId + "'][data-product_id='" + productId + "']" );
		var val1;
		var variationIdSelector = "input[name^='variation_id']";
		var $variationId;

		variationsForm = $.epoAPI.applyFilter( 'tm_variation_check_match_variationsForm', variationsForm, epoId, productId, $element );

		if ( variationsForm.length === 0 ) {
			return false;
		}

		$variationId = variationsForm.find( variationIdSelector );

		if ( $variationId.length === 0 ) {
			variationIdSelector = 'input.variation_id';

			$variationId = variationsForm.find( variationIdSelector );

			if ( $variationId.length === 0 ) {
				$variationId = variationsForm.closest( '.tc-epo-element-product-li-container' ).find( '.product-variation-id' );
			}
		}

		if ( element !== null && val2 !== null && element !== undefined && val2 !== undefined && element !== false && val2 !== false ) {
			if ( val2 ) {
				val2 = parseInt( val2, 10 );
			} else {
				val2 = -1;
			}
		}

		val1 = parseInt( $variationId.val(), 10 );

		if ( ! Number.isFinite( val1 ) ) {
			val1 = 0;
		}
		if ( ! Number.isFinite( val2 ) ) {
			val2 = 0;
		}

		switch ( operator ) {
			case 'is':
				return val1 !== '' && val1 === val2;

			case 'isnot':
				return val1 !== '' && val1 !== val2;

			case 'isempty':
				return val1 === '' || val1 === 0;

			case 'isnotempty':
				return val1 !== '' && val1 !== 0;

			case 'startswith':
				return val1.toString().startsWith( val2 );

			case 'endswith':
				return val1.toString().endsWith( val2 );

			case 'greaterthan':
				return parseFloat( val1 ) > parseFloat( val2 );

			case 'lessthan':
				return parseFloat( val1 ) < parseFloat( val2 );

			case 'greaterthanequal':
				return parseFloat( val1 ) >= parseFloat( val2 );

			case 'lessthanequal':
				return parseFloat( val1 ) <= parseFloat( val2 );
		}
		return false;
	}

	function tm_check_match( val1, val2, operator ) {
		if ( val1 !== null && val2 !== null ) {
			val1 = encodeURIComponent( val1 );
			if ( $.qtranxj_split ) {
				//backwards compatible
				val2 = encodeURIComponent( $.qtranxj_split( decodeURIComponent( val2 ) )[ TMEPOQTRANSLATEXJS.language ] );
			} else {
				//backwards compatible
				val2 = encodeURIComponent( decodeURIComponent( val2 ) );
			}

			if ( val1 ) {
				val1 = val1.toLowerCase();
			} else {
				val1 = '';
			}
			if ( val2 ) {
				val2 = val2.toLowerCase();
			} else {
				val2 = '';
			}
		} else {
			return false;
		}

		val1 = val1.toString();
		val2 = val2.toString();

		switch ( operator ) {
			case 'is':
				return val1 !== null && val1 === val2;

			case 'isnot':
				return val1 !== null && val1 !== val2;

			case 'isempty':
				return ! ( val1 !== 'undefined' && val1 !== undefined && val1 !== '' );

			case 'isnotempty':
				return val1 !== 'undefined' && val1 !== undefined && val1 !== '';

			case 'startswith':
				return val1.startsWith( val2 );

			case 'endswith':
				return val1.endsWith( val2 );

			case 'greaterthan':
				return parseFloat( val1 ) > parseFloat( val2 );

			case 'lessthan':
				return parseFloat( val1 ) < parseFloat( val2 );

			case 'greaterthanequal':
				return parseFloat( val1 ) >= parseFloat( val2 );

			case 'lessthanequal':
				return parseFloat( val1 ) <= parseFloat( val2 );
		}

		return false;
	}

	function tm_check_section_match( elements, operator ) {
		var any_checked;
		var val;
		var all_elements = elements.find( '.cpf-element' );
		var radio_checked;
		var checkbox_checked;
		var noSplit = false;

		if ( elements.is( '.tc-hidden' ) ) {
			if ( operator === 'isnotempty' ) {
				return false;
			} else if ( operator === 'isempty' ) {
				return true;
			}
		}

		if ( operator === 'isnotempty' ) {
			any_checked = false;
		} else if ( operator === 'isempty' ) {
			any_checked = true;
		}

		$( all_elements ).each( function( j, element ) {
			var _class;
			var elementToCheck = '';

			element = $( element );
			if ( element.is( '.cpf-type-product' ) ) {
				elementToCheck = ':not(.tc-extra-product-options-inline .tm-epo-field)';
			}
			if ( field_is_active( element ) ) {
				_class = element
					.attr( 'class' )
					.split( ' ' )
					.map( function( cls ) {
						if ( cls.indexOf( 'cpf-type-', 0 ) !== -1 ) {
							return cls;
						}
						return null;
					} )
					.filter( function( v ) {
						if ( v !== null && v !== undefined ) {
							return v;
						}
						return null;
					} );

				if ( _class.length > 0 ) {
					_class = _class[ 0 ];
					if ( _class === 'cpf-type-product' ) {
						noSplit = true;
						if ( element.is( '.cpf-type-product-mode-product' ) || element.is( '.cpf-type-product-checkbox' ) || element.is( '.cpf-type-product-thumbnailmultiple' ) ) {
							_class = 'cpf-type-checkbox';
						} else if ( element.is( '.cpf-type-product-dropdown' ) ) {
							_class = 'cpf-type-select';
						} else {
							_class = 'cpf-type-radio';
						}
					}
					switch ( _class ) {
						case 'cpf-type-radio':
							radio_checked = element.find( 'input.tm-epo-field.tmcp-radio:checked' + elementToCheck );
							if ( operator === 'isnotempty' ) {
								any_checked = any_checked || radio_checked.length > 0;
								if ( radio_checked.length > 0 ) {
									val = radio_checked.length;
								}
							} else if ( operator === 'isempty' ) {
								any_checked = any_checked && radio_checked.length === 0;
							}
							break;

						case 'cpf-type-checkbox':
							checkbox_checked = element.find( 'input.tm-epo-field.tmcp-checkbox:checked' + elementToCheck );
							if ( operator === 'isnotempty' ) {
								any_checked = any_checked || checkbox_checked.length > 0;
								if ( checkbox_checked.length > 0 ) {
									val = checkbox_checked.length;
								}
							} else if ( operator === 'isempty' ) {
								any_checked = any_checked && checkbox_checked.length === 0;
							}
							break;

						case 'cpf-type-select':
							val = element.find( 'select.tm-epo-field.tmcp-select' + elementToCheck ).val();
							if ( val && ! noSplit ) {
								val = val.slice( 0, val.lastIndexOf( '_' ) );
							}
							break;

						case 'cpf-type-selectmultiple':
							val = element.find( 'select.tm-epo-field.tmcp-selectmultiple' + elementToCheck ).val();
							if ( val && ! noSplit ) {
								val.forEach( function( option, i ) {
									val[ i ] = option.slice( 0, option.lastIndexOf( '_' ) );
								} );
							}
							break;

						default:
							val = element.find( '.tm-epo-field' + elementToCheck ).val();
							break;
					}
					if ( Array.isArray( val ) ) {
						if ( operator === 'isnotempty' ) {
							any_checked = any_checked || val.some( function( item ) {
								return tm_check_match( item, '', operator );
							} );
						} else if ( operator === 'isempty' ) {
							any_checked = any_checked && val.every( function( item ) {
								return tm_check_match( item, '', operator );
							} );
						}
					} else if ( operator === 'isnotempty' ) {
						any_checked = any_checked || tm_check_match( val, '', operator );
					} else if ( operator === 'isempty' ) {
						any_checked = any_checked && tm_check_match( val, '', operator );
					}
				} else {
					any_checked = false;
				}
			}
		} );

		return any_checked;
	}

	function tm_check_field_match( f ) {
		var element = $( f.element );
		var operator = f.operator;
		var value = f.value;
		var val;
		var radio_checked;
		var checkbox_checked;
		var ret;
		var _class;
		var noSplit = false;
		var elementToCheck = '';

		if ( ! element.length ) {
			return false;
		}
		if ( element.is( '.cpf-section' ) ) {
			return tm_check_section_match( element, operator );
		}
		if ( element.is( '.cpf-type-product' ) ) {
			elementToCheck = ':not(.tc-extra-product-options-inline .tm-epo-field)';
		}
		_class = element
			.attr( 'class' )
			.split( ' ' )
			.map( function( cls ) {
				if ( cls.indexOf( 'cpf-type-', 0 ) !== -1 ) {
					return cls;
				}
				return null;
			} )
			.filter( function( v ) {
				if ( v !== null && v !== undefined ) {
					return v;
				}
				return null;
			} );

		if ( _class.length > 0 ) {
			_class = _class[ 0 ];
			if ( _class === 'cpf-type-product' ) {
				noSplit = true;
				if ( element.is( '.cpf-type-product-mode-product' ) || element.is( '.cpf-type-product-checkbox' ) || element.is( '.cpf-type-product-thumbnailmultiple' ) ) {
					_class = 'cpf-type-checkbox';
				} else if ( element.is( '.cpf-type-product-dropdown' ) ) {
					_class = 'cpf-type-select';
				} else {
					_class = 'cpf-type-radio';
				}
			}
			switch ( _class ) {
				case 'cpf-type-radio':
					radio_checked = element.find( 'input.tm-epo-field.tmcp-radio:checked' + elementToCheck );

					if ( operator === 'is' || operator === 'isnot' ) {
						if ( radio_checked.length === 0 && operator === 'is' ) {
							return false;
						}
						if ( radio_checked.length === 0 && operator === 'isnot' ) {
							return true;
						}
					}
					if ( operator === 'isnotempty' ) {
						return radio_checked.length > 0;
					}
					if ( operator === 'isempty' ) {
						return radio_checked.length === 0;
					}
					val = element.find( 'input.tm-epo-field.tmcp-radio:checked' + elementToCheck ).val();
					if ( val && ! noSplit ) {
						val = val.slice( 0, val.lastIndexOf( '_' ) );
					}
					break;
				case 'cpf-type-checkbox':
					checkbox_checked = element.find( 'input.tm-epo-field.tmcp-checkbox:checked' + elementToCheck );

					if ( operator === 'is' || operator === 'isnot' ) {
						if ( checkbox_checked.length === 0 && operator === 'is' ) {
							return false;
						}
						if ( checkbox_checked.length === 0 && operator === 'isnot' ) {
							return true;
						}
						ret = false;
						checkbox_checked.each( function( i, el ) {
							val = $( el ).val();
							if ( val && ! noSplit ) {
								val = val.slice( 0, val.lastIndexOf( '_' ) );
							}
							if ( tm_check_match( val, value, operator ) ) {
								ret = true;
							} else if ( operator === 'isnot' ) {
								ret = false;
								return false;
							}
						} );
						return ret;
					}
					if ( operator === 'isnotempty' ) {
						return checkbox_checked.length > 0;
					}
					if ( operator === 'isempty' ) {
						return checkbox_checked.length === 0;
					}
					break;

				case 'cpf-type-select':
					val = element.find( 'select.tm-epo-field.tmcp-select' + elementToCheck ).val();
					if ( val && ! noSplit ) {
						val = val.slice( 0, val.lastIndexOf( '_' ) );
					}
					break;

				case 'cpf-type-selectmultiple':
					val = element.find( 'select.tm-epo-field.tmcp-selectmultiple' + elementToCheck ).val();
					if ( val && ! noSplit ) {
						val.forEach( function( option, i ) {
							val[ i ] = option.slice( 0, option.lastIndexOf( '_' ) );
						} );
					}
					break;

				case 'cpf-type-variations':
					return tm_variation_check_match( element, value, operator );

				default:
					val = element.find( '.tm-epo-field' + elementToCheck ).val();
					break;
			}
			if ( Array.isArray( val ) ) {
				return val.some( function( item ) {
					return tm_check_match( item, value, operator );
				} );
			}
			return tm_check_match( val, value, operator );
		}
		return false;
	}

	function tm_check_rules( o, theevent ) {
		o.each( function() {
			var $this = $( this );
			var matches = $this.data( 'matches' );
			var toggle = $this.data( 'toggle' );
			var fields = $this.data( 'fields' );
			var checked = [];
			var show = false;

			switch ( toggle ) {
				case 'show':
					show = false;
					break;
				case 'hide':
					show = true;
					break;
			}

			$.each( fields, function( i, ifield ) {
				if ( Array.isArray( ifield ) ) {
					checked[ i ] = 0;
					$.each( ifield, function( ii, field ) {
						var fia = true;

						if ( theevent === 'cpflogic' ) {
							fia = field_is_active( $( field.element ) );
						}
						if ( fia && tm_check_field_match( field ) ) {
							checked[ i ] = parseInt( checked[ i ], 10 ) + 1;
						}
					} );
				}
			} );

			$.each( matches, function( im, match ) {
				var checkim = parseInt( checked[ im ], 10 );
				match = parseInt( match, 10 );

				if ( checkim > 0 && match === checkim ) {
					show = ! show;
					return false;
				}
			} );

			if ( show ) {
				if ( theevent === 'cpflogic' && ! $this.data( 'did_initial_activation' ) ) {
					latecpflogicactions.push( function() {
						var enabledEpos = $this.find( '.tm-epo-field.tcenabled' );
						enabledEpos.each( function( i, el ) {
							el = $( el );
							if ( ! el.data( 'initial_activation' ) && ! $this.closest( '.cpf-section' ).is( '.tc-hidden' ) && field_is_active( el ) ) {
								el.trigger( 'tc_element_epo_rules' );
								el.data( 'initial_activation', 1 );
							}
						} );
						if ( ( $this.is( '.cpf-element' ) && enabledEpos.length ) || $this.is( '.cpf-section' ) ) {
							$this.data( 'did_initial_activation', 1 );
						}
					} );
				}

				$this.removeClass( 'tc-hidden' );
			} else {
				$this.addClass( 'tc-hidden' );
			}
			$this.data( 'isactive', show );
			$this.trigger( 'tc-logic' );
		} );
	}

	function run_cpfdependson( obj ) {
		var iscpfdependson;
		var last_activate_field = [];
		var epo_object;

		if ( ! $( obj ).length ) {
			obj = 'body';
		}
		obj = $( obj );
		iscpfdependson = obj.find( '.iscpfdependson' );
		iscpfdependson.each( function( i, elements ) {
			$( elements ).each( function( j, el ) {
				tm_check_rules( $( el ) );
			} );
		} );
		iscpfdependson.each( function( i, elements ) {
			$( elements ).each( function( j, el ) {
				tm_check_rules( $( el ), 'cpflogic' );
			} );
		} );
		iscpfdependson.each( function( i, elements ) {
			$( elements ).each( function( j, o ) {
				o = $( o );
				if ( o.is( '.cpf-section' ) ) {
					o = o.find( '.cpf-element' );
				}
				o.each( function( theindex, theelement ) {
					field_is_active( $( theelement ).find( '.tm-epo-field' ) );
				} );
			} );
		} );
		latecpflogicactions.forEach( function( func ) {
			func();
		} );
		latecpflogicactions = [];
		if ( $().selectric ) {
			$( '.tm-extra-product-options select' ).selectric( 'refresh' );
		}
		setTimeout( function() {
			$( '.tm-owl-slider' ).each( function() {
				$( this ).trigger( 'refresh.owl.carousel' );
			} );
		}, 200 );

		obj.find( '.tm-product-image:checked,select.tm-product-image' ).each( function() {
			var t = $( this );
			if ( field_is_active( t ) && t.val() !== '' ) {
				last_activate_field.push( t );
			}
		} );
		if ( last_activate_field.length ) {
			last_activate_field[ last_activate_field.length - 1 ].trigger( 'tm_trigger_product_image' );
		} else {
			epo_object = obj.data( 'epo_object' );
			if ( epo_object ) {
				epo_object.main_product.trigger( 'tm_restore_product_image' );
			}
		}

		jWindow.trigger( 'cpflogicrun' );
		jWindow.trigger( 'tmlazy' );
		jWindow.trigger( 'cpflogicdone' );
	}

	// Start Conditional logic
	if ( ! $().cpfdependson ) {
		$.fn.cpfdependson = function( fields, toggle, refresh ) {
			var elements = this;
			var matches = [];

			if ( elements.length === 0 || typeof fields !== 'object' ) {
				return;
			}

			if ( ! toggle ) {
				toggle = 'show';
			}

			$.each( fields, function( i, ifield ) {
				var get_element;
				var $this_epo_container;

				matches[ i ] = 0;

				if ( Array.isArray( ifield ) ) {
					$.each( ifield, function( ii, field ) {
						if ( typeof field !== 'object' ) {
							return true;
						}
						get_element = get_element_from_field( field.element );

						if ( get_element && get_element.length > 0 ) {
							get_element.each( function( iii, element ) {
								var $element = $( element );
								var $pid1;
								var $epo_id1;
								var _events = 'change.cpflogic';

								// this essentially only works for the plugin so we use
								// cache and not recalcualte each time
								if ( ! $this_epo_container || $this_epo_container.closest( '.tc-extra-product-options-inline' ).length ) {
									$pid1 = '.tm-product-id-' + $element.closest( '.tc-extra-product-options' ).attr( 'data-product-id' );
									$epo_id1 = "[data-epo-id='" + $element.closest( '.tc-extra-product-options' ).attr( 'data-epo-id' ) + "']";
									$this_epo_container = $( '.tc-extra-product-options' + $pid1 + $epo_id1 );
								}

								if ( element && $element.length > 0 && ( ! $element.data( 'tmhaslogicevents' ) || refresh ) ) {
									if ( $element.is( '.tm-epo-variation-element' ) ) {
										// associated product event prefixes are added later
										add_variation_event( 'found_variation.tmlogic', false, function() {
											run_cpfdependson( $this_epo_container );
											jWindow.trigger( 'tm-do-epo-update' );
										} );
										add_variation_event( 'hide_variation.tmlogic', false, function() {
											run_cpfdependson( $this_epo_container );
											jWindow.trigger( 'tm-do-epo-update' );
										} );
									} else {
										if ( $element.is( ':text' ) || $element.is( 'textarea' ) ) {
											_events = 'change.cpflogic input.cpflogic';
										}
										if ( $element.is( ':input[type="number"]' ) ) {
											_events = 'change.cpflogic input.cpflogic keypress.cpflogic';
										}
										$element.off( _events ).on( _events, function() {
											run_cpfdependson( $this_epo_container );
										} );
									}
									$element.data( 'tmhaslogicevents', 1 );
								}
							} );
						}
						matches[ i ] = parseInt( matches[ i ], 10 ) + 1;
					} );
				}
			} );

			elements.each( function() {
				var $this = $( this );
				var show = false;

				$this.data( 'matches', matches ).data( 'toggle', toggle ).data( 'fields', fields );

				switch ( toggle ) {
					case 'show':
						show = false;
						break;
					case 'hide':
						show = true;
						break;
				}
				if ( show ) {
					$this.removeClass( 'tc-hidden' );
				} else {
					$this.addClass( 'tc-hidden' );
				}

				$this.data( 'isactive', show );
			} );

			elements.addClass( 'iscpfdependson is-epo-depend' ).data( 'iscpfdependson', 1 );
			return elements;
		};

		$.fn.run_cpfdependson = function() {
			run_cpfdependson();
		};
	}

	$.tcepo = {
		formSubmitEvents: {},

		oneOptionIsSelected: {},

		initialActivation: {},

		// Holds the active precentage of total current price type fields
		lateFieldsPrices: {},

		errorObject: {},

		showHideTotal: {}
	};

	function validate_logic( l ) {
		return typeof l === 'object' && 'toggle' in l && 'rules' in l && l.rules.length > 0;
	}

	function convert_rules( rules ) {
		if ( rules.what ) {
			if ( rules.what === 'all' ) {
				rules.rules = [ rules.rules ];
			} else if ( rules.what === 'any' ) {
				rules.rules = rules.rules.reduce( function( accumulator, elrule ) {
					accumulator.push( [ elrule ] );
					return accumulator;
				}, [] );
			}
			delete rules.what;
		}
		return rules;
	}

	// The following loops are required for the logic to work on composite products that have custom variations
	function cpf_section_logic( obj ) {
		var root_element = $( obj );
		var all_sections = root_element.find( '.cpf-section' );
		var search_obj;
		var cpf_section;
		var sect;
		var logic;
		var haslogic;
		var fields;
		var section;
		var element;
		var operator;
		var value;
		var obj_section;
		var obj_element;
		var closestProductElement;

		if ( root_element.is( '.cpf-section' ) ) {
			search_obj = false;
		} else {
			search_obj = all_sections;
		}

		root_element.each( function( j, obj_el ) {
			if ( $( obj_el ).is( '.cpf-section' ) ) {
				cpf_section = $( obj_el );
			} else {
				cpf_section = $( obj_el ).find( '.cpf-section' );
			}

			cpf_section.filter( '[data-haslogic="1"]' ).each( function( index, el ) {
				sect = $( el );
				logic = sect.data( 'logic' );
				// backwards compatibility conversion.
				logic = convert_rules( logic );
				haslogic = parseInt( sect.data( 'haslogic' ), 10 );
				fields = [];
				closestProductElement = sect.parent().closest( '.cpf-type-product' );

				if ( haslogic === 1 && validate_logic( logic ) ) {
					$.each( logic.rules, function( i, irule ) {
						if ( irule ) {
							if ( Array.isArray( irule ) ) {
								fields[ i ] = [];
								$.each( irule, function( ii, rule ) {
									section = rule.section;
									element = rule.element;
									operator = rule.operator;
									value = rule.value;

									if ( search_obj ) {
										if ( closestProductElement.length ) {
											obj_section = closestProductElement.find( '.cpf-section' ).filter( "[data-uniqid='" + section + "']" );
											if ( element !== section ) {
												obj_element = obj_section.find( '.cpf-element' ).eq( element );
											} else {
												obj_element = obj_section;
											}
										} else {
											obj_section = search_obj.filter( "[data-uniqid='" + section + "']" );
											if ( element !== section ) {
												obj_element = obj_section.find( '.cpf-element:not(.cpf-element .cpf-element)' ).eq( element );
											} else {
												obj_element = obj_section;
											}
										}
									} else if ( element !== section ) {
										obj_element = root_element.find( '.cpf-element' ).eq( element );
									} else {
										obj_element = obj_section;
									}

									fields[ i ].push( {
										element: obj_element,
										operator: operator,
										value: value
									} );
								} );
							}
						}
					} );
					if ( ! sect.data( 'iscpfdependson' ) ) {
						sect.data( 'cpfdependson-fields', fields );
						sect.cpfdependson( fields, logic.toggle );
					} else {
						sect.cpfdependson( sect.data( 'cpfdependson-fields' ), logic.toggle, true );
					}
				}
			} );
		} );
	}

	function cpf_element_logic( obj ) {
		var root_element = $( obj );
		var all_sections = root_element.find( '.cpf-section' ).not( '.cpf-type-product .cpf-section' );
		var search_obj;
		var current_element;
		var logic;
		var haslogic;
		var section;
		var element;
		var operator;
		var value;
		var obj_section;
		var obj_element;
		var closestProductElement;

		if ( root_element.is( '.cpf-section' ) ) {
			search_obj = false;
		} else {
			search_obj = all_sections;
		}

		root_element.find( '.cpf-element[data-haslogic="1"]' ).each( function( index, el ) {
			var fields = [];

			current_element = $( el );
			logic = current_element.data( 'logic' );
			// backwards compatibility conversion.
			logic = convert_rules( logic );

			haslogic = parseInt( current_element.data( 'haslogic' ), 10 );

			closestProductElement = current_element.parent().closest( '.cpf-type-product' );

			if ( haslogic === 1 && validate_logic( logic ) ) {
				$.each( logic.rules, function( i, irule ) {
					if ( irule ) {
						if ( Array.isArray( irule ) ) {
							fields[ i ] = [];
							$.each( irule, function( ii, rule ) {
								section = rule.section;
								element = rule.element;
								operator = rule.operator;
								value = rule.value;

								if ( search_obj ) {
									if ( closestProductElement.length ) {
										obj_section = closestProductElement.find( '.cpf-section' ).filter( "[data-uniqid='" + section + "']" );
										if ( element !== section ) {
											obj_element = obj_section.find( '.cpf-element' ).eq( element );
										} else {
											obj_element = obj_section;
										}
									} else {
										obj_section = search_obj.filter( "[data-uniqid='" + section + "']" );
										if ( element !== section ) {
											obj_element = obj_section.find( '.cpf-element:not(.cpf-element .cpf-element)' ).eq( element );
										} else {
											obj_element = obj_section;
										}
									}
								} else if ( element !== section ) {
									obj_element = root_element.find( '.cpf-element' ).eq( element );
								} else {
									obj_element = obj_section;
								}

								fields[ i ].push( {
									element: obj_element,
									operator: operator,
									value: value
								} );
							} );
						}
					}
				} );

				if ( ! current_element.data( 'iscpfdependson' ) ) {
					current_element.data( 'cpfdependson-fields', fields );
					current_element.cpfdependson( fields, logic.toggle );
				} else {
					current_element.cpfdependson( current_element.data( 'cpfdependson-fields' ), logic.toggle, true );
				}
			}
		} );
	} // End Conditional logic

	// Return price value without tax
	function tm_set_price_without_tax( value, _cart, force ) {
		var taxable;
		var tax_rate;
		var tax_display_mode;
		var prices_include_tax;

		if ( _cart ) {
			taxable = _cart.attr( 'data-taxable' );
			tax_rate = _cart.attr( 'data-tax-rate' );
			tax_display_mode = _cart.attr( 'data-tax-display-mode' );
			prices_include_tax = _cart.attr( 'data-prices-include-tax' );

			if ( force || ( taxable && tax_display_mode === 'incl' && prices_include_tax !== '1' ) ) {
				value = parseFloat( value ) / ( 1 + ( tax_rate / 100 ) );
			}
		}

		return value;
	}

	// Return price with tax
	function tm_set_price_with_tax( value, _cart, force ) {
		var taxable;
		var tax_rate;
		var tax_display_mode;
		var prices_include_tax;

		if ( _cart ) {
			taxable = _cart.attr( 'data-taxable' );
			tax_rate = _cart.attr( 'data-tax-rate' );
			tax_display_mode = _cart.attr( 'data-tax-display-mode' );
			prices_include_tax = _cart.attr( 'data-prices-include-tax' );
			if ( force || ( taxable && tax_display_mode !== 'incl' && prices_include_tax === '1' ) ) {
				value = parseFloat( value ) * ( 1 + ( tax_rate / 100 ) );
			}
		}

		return value;
	}

	// Return  price
	function tm_set_backend_price( value, _cart, variation ) {
		var taxable;
		var tax_display_mode;
		var prices_include_tax;

		if ( _cart ) {
			taxable = _cart.attr( 'data-taxable' );
			tax_display_mode = _cart.attr( 'data-tax-display-mode' );
			prices_include_tax = _cart.attr( 'data-prices-include-tax' );
			if ( taxable ) {
				if ( variation && variation.tc_tax_rate && String( variation.tc_tax_rate ) === '0' ) {
					return value;
				}
				if ( prices_include_tax === '1' ) {
					if ( tax_display_mode === 'incl' ) {
						// if we are not in the base tax
						if ( variation.tc_base_taxes_of_one !== variation.tc_modded_taxes_of_one ) {
							value = parseFloat( ( ( 1 - variation.tc_taxes_of_one ) * value ) * ( 1 / ( 1 - variation.tc_base_taxes_of_one ) ) );
						}
					} else {
						value = tm_set_price_with_tax( value, _cart );
					}
				} else if ( prices_include_tax !== '1' && tax_display_mode === 'incl' ) {
					value = tm_set_price_without_tax( value, _cart );
				}
			}
		}

		return value;
	}

	// Return a formatted currency value
	function tm_set_price_totals( value, _cart, notax, taxstring, element ) {
		var inc_tax_string = '';
		var sign = '';
		var val;

		if ( ! notax ) {
			value = tm_set_tax_price( value, _cart, element, undefined, true );
		}
		val = Math.abs( value );
		if ( _cart && taxstring ) {
			inc_tax_string = _cart.attr( 'data-tax-string' );
		}
		if ( inc_tax_string === undefined ) {
			inc_tax_string = '';
		}

		if ( value < 0 ) {
			sign = TMEPOJS.minus_sign + ' ';
		}

		return tm_set_price_( val, sign, inc_tax_string );
	}

	function replace_suffixes( value, rawValue, tc_totals_ob, totalsHolder ) {
		if ( totalsHolder.attr( 'data-tax-display-mode' ) === 'excl' ) {
			tc_totals_ob[ value ] = tc_totals_ob[ value ].replace( /{price_excluding_tax}/g, tm_set_price_totals( tc_totals_ob[ rawValue ], totalsHolder, true, false ) );
			tc_totals_ob[ value ] = tc_totals_ob[ value ].replace( /{price_including_tax}/g, tm_set_price_totals( tm_set_price_with_tax( tc_totals_ob[ rawValue ], totalsHolder, true ), totalsHolder, true, false ) );
		} else {
			tc_totals_ob[ value ] = tc_totals_ob[ value ].replace( /{price_including_tax}/g, tm_set_price_totals( tc_totals_ob[ rawValue ], totalsHolder, true, false ) );
			tc_totals_ob[ value ] = tc_totals_ob[ value ].replace( /{price_excluding_tax}/g, tm_set_price_totals( tm_set_price_without_tax( tc_totals_ob[ rawValue ], totalsHolder, true ), totalsHolder, true, false ) );
		}
		return tc_totals_ob;
	}

	function tm_force_update_price( obj, price, formatted_price, original_price, useFormattedPrice ) {
		tm_update_price( {
			obj: obj,
			price: price,
			formatted_price: formatted_price,
			original_price: original_price,
			force: true,
			useFormattedPrice: useFormattedPrice
		} );
	}

	function tm_update_price( priceobj ) {
		var $obj;
		var w;
		var $ba_amount;
		var f;
		var pw;
		var price;
		var formatted_price;
		var original_price;
		var force;
		var useFormattedPrice;
		var template;
		var templatePrice;

		$obj = $( priceobj.obj );

		if ( $obj.length === 0 ) {
			return;
		}

		w = $obj.closest( '.tmcp-field-wrap' );
		pw = $obj.closest( '.tc-price-wrap' );
		f = w.find( '.tm-epo-field' );

		price = priceobj.price || 0;
		formatted_price = priceobj.formatted_price || '';
		original_price = priceobj.original_price || '';
		force = priceobj.force || false;
		useFormattedPrice = priceobj.useFormattedPrice || false;

		if ( ! force && f.attr( 'data-no-price-change' ) === '1' && f.data( 'price-changed' ) ) {
			return;
		}

		price = $.epoAPI.applyFilter( 'tc_adjust_update_price_price', price ); //number
		formatted_price = $.epoAPI.applyFilter( 'tc_adjust_update_price_formatted_price', formatted_price, price ); //formatted
		original_price = $.epoAPI.applyFilter( 'tc_adjust_update_price_original_price', original_price ); //number

		if ( ! Number.isFinite( parseFloat( original_price ) ) ) {
			original_price = 0;
		}
		if ( ! Number.isFinite( parseFloat( price ) ) ) {
			price = 0;
		}

		$ba_amount = w.find( '.before-amount,.after-amount' );

		if ( ( TMEPOJS.tm_epo_auto_hide_price_if_zero === 'yes' && ( $.tmempty( price ) === false || ( TMEPOJS.tm_epo_no_hide_price_if_original_not_zero === 'yes' && $.tmempty( price ) === true && $.tmempty( original_price ) === false ) ) ) || TMEPOJS.tm_epo_auto_hide_price_if_zero !== 'yes' ) {
			if ( ( $.tmempty( price ) === true && f.attr( 'data-no-price' ) === '1' ) || ( ! force && f.length > 0 && ( f.attr( 'data-no-price' ) === '1' || ( f.attr( 'data-type' ) === 'variable' && ! f.data( 'price' ) ) ) ) ) {
				pw.addClass( 'tm-hidden' );
				$obj.addClass( 'tm-hidden' );
				$obj.empty();
				$ba_amount.addClass( 'tm-hidden' );
			} else {
				if ( original_price && original_price !== undefined && parseFloat( original_price ) !== parseFloat( price ) ) {
					if ( useFormattedPrice ) {
						template = tcAPI.templateEngine.plain_price;
						templatePrice = { price: formatted_price };
					} else {
						if ( price < 0 && ! original_price < 0 ) {
							template = tcAPI.templateEngine.sale_price_m10;
							price = Math.abs( price );
						} else if ( ! price < 0 && original_price < 0 ) {
							template = tcAPI.templateEngine.sale_price_m01;
							original_price = Math.abs( original_price );
						} else if ( price < 0 && original_price < 0 ) {
							template = tcAPI.templateEngine.sale_price_m11;
							price = Math.abs( price );
							original_price = Math.abs( original_price );
						} else {
							template = tcAPI.templateEngine.sale_price;
						}
						templatePrice = { price: formatPrice( original_price ), sale_price: formatPrice( price ) };
					}
				} else if ( useFormattedPrice ) {
					template = tcAPI.templateEngine.plain_price;
					templatePrice = { price: formatted_price };
				} else {
					template = tcAPI.templateEngine.price;
					if ( price < 0 ) {
						template = tcAPI.templateEngine.price_m;
						price = Math.abs( price );
					}
					templatePrice = { price: formatPrice( price ) };
				}
				$obj.html( $.epoAPI.util.decodeHTML( $.epoAPI.template.html( template, templatePrice ) ) );
				pw.removeClass( 'tm-hidden' );
				$obj.removeClass( 'tm-hidden' );
				$ba_amount.removeClass( 'tm-hidden' );
			}
		} else {
			pw.addClass( 'tm-hidden' );
			$obj.addClass( 'tm-hidden' );
			$obj.empty();
			$ba_amount.addClass( 'tm-hidden' );
		}
	}

	function get_variation_current_settings( form, epoObject ) {
		var current_settings = {};

		if ( epoObject.thisForm ) {
			form = epoObject.thisForm;
		}

		form.find( '.variations select, .tc-epo-variable-product-selector' ).each( function() {
			var attribute_name;
			var value;

			// Get attribute name from data-attribute_name, or from input name
			// if it doesn't exist
			if ( typeof $( this ).data( 'attribute_name' ) !== 'undefined' ) {
				attribute_name = $( this ).data( 'attribute_name' );
			} else {
				attribute_name = $( this ).attr( 'name' );
			}

			// Encode entities
			value = $( this ).val();

			// Add to settings array
			current_settings[ attribute_name ] = value;
		} );

		return current_settings;
	}

	function do_tm_custom_variations_update( form, all_variations, epoObject ) {
		var check_if_all_are_not_set = [];
		var formSettings = get_variation_current_settings( form, epoObject );
		var redo_check = true;
		var variationId = form.find( getVariationIdSelector( form ) ).val();
		variationId = ( variationId !== '0' && variationId !== '' ) || form.find( '.cpf-type-variations' ).find( '.tm-epo-variation-element:checked' ).length > 0 || form.find( '.cpf-type-variations' ).find( '.tm-epo-variation-element option[value!=""]:selected' );

		if ( ! variationId ) {
			return;
		}
		form.find( '.cpf-type-variations' ).each( function( i, el ) {
			var t = $( el ).find( '.tm-epo-variation-element' );
			var id;
			var v;
			var exists = false;

			check_if_all_are_not_set[ i ] = true;

			if ( t.is( 'select' ) ) {
				id = $.epoAPI.dom.id( t.attr( 'data-tm-for-variation' ) );
				v = t.val();
				if ( v ) {
					check_if_all_are_not_set[ i ] = false;
				}
				t.children( 'option' ).each( function( x, o ) {
					exists = false;
					form.find( "[data-attribute_name='attribute_" + id + "']" )
						.children( 'option' )
						.each( function() {
							if ( $( this ).attr( 'value' ) === $( o ).attr( 'value' ) ) {
								exists = true;
								return false;
							}
						} );
					if ( ! exists ) {
						$( o ).prop( 'disabled', true ).hide();
					} else {
						$( o ).prop( 'disabled', false ).show();
					}
				} );
			} else {
				t.each( function( x, oe ) {
					var o = $( oe );
					var li = o.closest( 'li' );
					var input = li.find( '.tm-epo-variation-element' );
					var this_settings = $.extend( true, {}, formSettings );
					var matching_variations;
					var variation;
					var is_in_stock;

					id = o.attr( 'data-tm-for-variation' );
					v = o.val();
					if ( o.is( ':checked' ) ) {
						check_if_all_are_not_set[ i ] = false;
					}

					this_settings[ 'attribute_' + id ] = v;

					matching_variations = $.fn.tm_find_matching_variations( all_variations, this_settings );
					variation = matching_variations.shift();

					is_in_stock = variation && 'is_in_stock' in variation && variation.is_in_stock;

					if ( ! is_in_stock ) {
						if ( ! input.is( ':checked' ) && ! is_in_stock ) {
							li.addClass( 'pointereventsoff' );
						} else {
							li.removeClass( 'pointereventsoff' );
						}

						o.attr( 'disabled', 'disabled' ).addClass( 'tm-disabled' );

						input.attr( 'disabled', 'disabled' );
						input.attr( 'data-tm-disabled', 'disabled' );

						li.addClass( 'tm-attribute-disabled' ).fadeTo( 'fast', 0.5 );
					} else {
						o.prop( 'disabled', false ).removeClass( 'tm-disabled' );
						li.removeClass( 'pointereventsoff tm-attribute-disabled' ).fadeTo( 'fast', 1, function() {
							$( this ).css( 'opacity', '' );
						} );
						input.prop( 'disabled', false );
						input.removeAttr( 'data-tm-disabled' );
					}
				} );
			}
		} );

		if ( check_if_all_are_not_set ) {
			check_if_all_are_not_set.shift();

			$.each( check_if_all_are_not_set, function( i, el ) {
				if ( el === false ) {
					redo_check = false;
					return false;
				}
			} );
			if ( redo_check ) {
				form.find( '.cpf-type-variations' )
					.first()
					.each( function( i, el ) {
						var t;
						var li;
						var input;

						t = $( el ).find( '.tm-epo-variation-element' );

						if ( ! t.is( 'select' ) ) {
							t.each( function( x, o ) {
								o = $( o );
								li = o.closest( 'li' );
								input = li.find( '.tm-epo-variation-element' );
								o.prop( 'disabled', false ).removeClass( 'tm-disabled' );
								li.removeClass( 'tm-attribute-disabled' ).stop().css( 'opacity', '' );
								input.prop( 'disabled', false );
								input.removeAttr( 'data-tm-disabled' );
							} );
						}
					} );
			}
		}
	}

	function tm_custom_variations_update( form, epoObject ) {
		var data;
		var all_variations = form.data( 'product_variations' );
		var product_id = parseInt( form.data( 'product_id' ), 10 );
		var globalVariationObject = form.data( 'globalVariationObject' ) || false;

		if ( ! product_id ) {
			product_id = form.data( 'tc_product_id' );
		}

		if ( ! product_id && form.is( tcAPI.compositeSelector ) ) {
			data = form.find( '.component_options' ).data( 'options_data' );
			product_id = data[ 0 ].option_id;
			if ( ! all_variations ) {
				all_variations = form.find( '.details.component_data' ).data( 'product_variations' );
			}
		}

		if ( ! epoObject.is_associated ) {
			// Fallback to window property if not set - backwards compat
			if ( ! all_variations && window.product_variations && window.product_variations.product_id ) {
				all_variations = window.product_variations.product_id;
			}
			if ( ! all_variations && window.product_variations ) {
				all_variations = window.product_variations;
			}
			if ( ! all_variations && window[ 'product_variations_' + product_id ] ) {
				all_variations = window[ 'product_variations_' + product_id ];
			}
		}

		if ( ! all_variations ) {
			if ( ! globalVariationObject ) {
				data = {
					action: 'woocommerce_tm_get_variations_array',
					post_id: product_id
				};
				$.post(
					TMEPOJS.ajax_url,
					data,
					function( response ) {
						globalVariationObject = response;
						form.data( 'globalVariationObject', response );
						do_tm_custom_variations_update( form, globalVariationObject.variations, epoObject );
					},
					'json'
				);
			} else {
				do_tm_custom_variations_update( form, globalVariationObject.variations, epoObject );
			}

			return;
		}
		// may need 2.4 check for woocommerce_ajax_variation_threshold
		do_tm_custom_variations_update( form, all_variations, epoObject );
	}

	function tm_fix_stock( cart, html ) {
		var custom_variations;
		var section;

		if ( html === undefined ) {
			return false;
		}
		cart = $( cart );
		custom_variations = cart.find( '.tm-epo-variation-element' ).first();
		section = custom_variations.closest( '.tm-epo-variation-section' );

		if ( custom_variations.length ) {
			section.find( '.tm-stock' ).remove();
			section.append( '<div class="tm-stock">' + html + '</div>' );
			return true;
		}
		cart.find( '.tm-stock' ).remove();
		cart.find( '.variations' ).after( '<div class="tm-stock">' + html + '</div>' );
		return true;
	}

	function fix_stock( $this, form ) {
		var stock;

		if ( TMEPOJS.tm_epo_global_move_out_of_stock === 'no' ) {
			return;
		}
		stock = $this.find( '.woocommerce-variation-availability' ).last();
		if ( ! stock.length ) {
			stock = $this.find( '.stock' ).last();
		}

		if ( stock.length ) {
			form.find( '.tm-stock' ).remove();
			if ( tm_fix_stock( form, stock.prop( 'outerHTML' ) ) ) {
				stock.remove();
			}
		} else {
			form.find( '.tm-stock' ).remove();
		}
	}

	function get_main_input_id( main_product, product, id ) {
		var selector = '';
		var inputid;

		if ( id ) {
			selector = selector + "[value='" + id + "']";
		}
		if ( ! product ) {
			product = main_product;
		}
		inputid = product.find( tcAPI.addToCartSelector + selector );
		if ( inputid.length === 0 ) {
			inputid = product.find( tcAPI.tcAddToCartSelector + selector );
		}
		return inputid.last();
	}

	function get_main_form( main_product, product, selector, id ) {
		if ( ! selector ) {
			selector = 'form';
		}
		return get_main_input_id( main_product, product, id ).closest( selector );
	}

	function get_main_cart( main_product, product, selector, id ) {
		return get_main_form( main_product, product, selector, id );
	}

	function tm_get_native_prices_block( obj ) {
		var selector = $.epoAPI.applyFilter( 'tcGetNativePricesBlockSelector', '.single_variation .price', obj );

		return $( obj ).find( selector ).not( '.tc-price' );
	}

	// URL replacement setup
	function tm_set_url_fields() {
		jDocument.on( 'click.cpfurl change.cpfurl tmredirect', '.tc-url-container .tmcp-radio, .tc-url-container .tmcp-radio+label', function( e ) {
			var data_url = $( this ).attr( 'data-url' );
			if ( data_url ) {
				if ( window.location !== data_url ) {
					e.preventDefault();
					window.location = data_url;
				}
			}
		} );
		jDocument.on( 'change.cpfurl tmredirect', '.tc-url-container .tmcp-select', function( e ) {
			var data_url = $( this ).children( 'option:selected' ).attr( 'data-url' );
			if ( data_url ) {
				if ( window.location !== data_url ) {
					e.preventDefault();
					window.location = data_url;
				}
			}
		} );
	}

	function tm_floating_totals( this_epo_totals_container, is_quickview, main_cart ) {
		$.tcFloatingTotalsBox( this_epo_totals_container, is_quickview, main_cart );
	}

	function tm_show_hide_add_to_cart_button( main_product, currentEpoObject ) {
		var button;
		var qty;
		var showHideCart = currentEpoObject.showHideCart;

		if ( ! currentEpoObject.is_associated && showHideCart !== undefined ) {
			if ( TMEPOJS.tm_epo_hide_add_cart_button === 'yes' || TMEPOJS.tm_epo_hide_all_add_cart_button === 'yes' || TMEPOJS.tm_epo_hide_required_add_cart_button === 'yes' ) {
				button = main_product.find( tcAPI.addToCartButtonSelector ).first();
				qty = main_product.find( tcAPI.qtySelector ).first();

				if ( showHideCart ) {
					button.removeClass( 'tc-hide-add-to-cart-button' );
					qty.removeClass( 'tc-hide-add-to-cart-button' );
				} else {
					button.addClass( 'tc-hide-add-to-cart-button' );
					qty.addClass( 'tc-hide-add-to-cart-button' );
				}

				jWindow.trigger( 'epoShowHideCart', {
					main_product: main_product,
					currentEpoObject: currentEpoObject,
					showHideCart: showHideCart,
					button: button,
					qty: qty
				} );
			}
		}
	}

	function addShowHidetoEpoObject( currentEpoObject, epoEventId, epoObject ) {
		var has_epo;
		var this_epo_container;
		var epos;
		var allElementsAreSelected;
		var showHideCart = true;
		var showHideTotal = true;
		var one_option_is_selected = $.tcepo.oneOptionIsSelected[ epoEventId ];

		if ( currentEpoObject.is_associated ) {
			currentEpoObject = $.epoAPI.applyFilter( 'tc_currentEpoObject_associated', currentEpoObject, epoEventId, epoObject );
			return currentEpoObject;
		}

		if ( typeof epoObject === 'object' ) {
			has_epo = epoObject.has_epo;
			this_epo_container = epoObject.this_epo_container;
			has_epo = has_epo && ( this_epo_container.find( '.tmcp-fee-field' ).length || this_epo_container.find( '.tmcp-field' ).not( '.cpf-type-variations .tmcp-field' ).length );
		} else {
			has_epo = epoObject;
		}

		currentEpoObject.showHideCart = undefined;
		currentEpoObject.showHideTotal = undefined;

		if ( has_epo && ( TMEPOJS.tm_epo_hide_add_cart_button === 'yes' || TMEPOJS.tm_epo_hide_all_add_cart_button === 'yes' || TMEPOJS.tm_epo_hide_required_add_cart_button === 'yes' || TMEPOJS.tm_epo_hide_totals_until_any === 'yes' || TMEPOJS.tm_epo_hide_totals_until_all_required === 'yes' || TMEPOJS.tm_epo_hide_totals_until_all === 'yes' ) ) {
			// Hide cart button until an element is chosen
			if ( TMEPOJS.tm_epo_hide_add_cart_button === 'yes' ) {
				showHideCart = one_option_is_selected;
			}

			// Hide Final total box until an element is chosen
			if ( TMEPOJS.tm_epo_hide_totals_until_any === 'yes' ) {
				showHideTotal = one_option_is_selected;
			}

			// Hide until all required elements are chosen
			if ( TMEPOJS.tm_epo_hide_required_add_cart_button === 'yes' || TMEPOJS.tm_epo_hide_totals_until_all_required === 'yes' ) {
				epos = currentEpoObject.this_epo_container.find( '.cpf-element' ).not( '.cpf-type-variations' ).filter( '.tc-container-enabled.tc-is-required' );
				allElementsAreSelected = epos.toArray().every( function( element ) {
					var elementToCheck = '';
					var _class;
					var noSplit = false;
					var radio_checked;
					var checkbox_checked;
					var val;

					element = $( element );
					if ( element.is( '.cpf-type-product' ) ) {
						elementToCheck = ':not(.tc-extra-product-options-inline .tm-epo-field)';
					}
					_class = element
						.attr( 'class' )
						.split( ' ' )
						.map( function( cls ) {
							if ( cls.indexOf( 'cpf-type-', 0 ) !== -1 ) {
								return cls;
							}
							return null;
						} )
						.filter( function( v ) {
							if ( v !== null && v !== undefined ) {
								return v;
							}
							return null;
						} );

					if ( _class.length > 0 ) {
						_class = _class[ 0 ];
						if ( _class === 'cpf-type-product' ) {
							noSplit = true;
							if ( element.is( '.cpf-type-product-mode-product' ) || element.is( '.cpf-type-product-checkbox' ) || element.is( '.cpf-type-product-thumbnailmultiple' ) ) {
								_class = 'cpf-type-checkbox';
							} else if ( element.is( '.cpf-type-product-dropdown' ) ) {
								_class = 'cpf-type-select';
							} else {
								_class = 'cpf-type-radio';
							}
						}
						switch ( _class ) {
							case 'cpf-type-radio':
								radio_checked = element.find( 'input.tm-epo-field.tmcp-radio:checked' + elementToCheck );
								return radio_checked.length > 0;

							case 'cpf-type-checkbox':
								checkbox_checked = element.find( 'input.tm-epo-field.tmcp-checkbox:checked' + elementToCheck );
								return checkbox_checked.length > 0;

							case 'cpf-type-select':
								val = element.find( 'select.tm-epo-field.tmcp-select' + elementToCheck ).val();
								if ( val && ! noSplit ) {
									val = val.slice( 0, val.lastIndexOf( '_' ) );
								}
								break;

							case 'cpf-type-selectmultiple':
								val = element.find( 'select.tm-epo-field.tmcp-selectmultiple' + elementToCheck ).val();
								if ( val && ! noSplit ) {
									val.forEach( function( option, i ) {
										val[ i ] = option.slice( 0, option.lastIndexOf( '_' ) );
									} );
								}
								break;

							default:
								val = element.find( '.tm-epo-field' + elementToCheck ).val();
								break;
						}
						if ( Array.isArray( val ) ) {
							return val.some( function( item ) {
								return item !== '';
							} );
						}
						return val !== '';
					}
					return false;
				} );
				if ( TMEPOJS.tm_epo_hide_required_add_cart_button === 'yes' ) {
					showHideCart = allElementsAreSelected;
				}
				if ( TMEPOJS.tm_epo_hide_totals_until_all_required === 'yes' ) {
					showHideTotal = allElementsAreSelected;
				}
			}

			// Hide until all elements are chosen
			if ( TMEPOJS.tm_epo_hide_all_add_cart_button === 'yes' || TMEPOJS.tm_epo_hide_totals_until_all === 'yes' ) {
				epos = currentEpoObject.this_epo_container.find( '.cpf-element' ).not( '.cpf-type-variations' ).filter( '.tc-container-enabled' );
				allElementsAreSelected = epos.toArray().every( function( element ) {
					var elementToCheck = '';
					var _class;
					var noSplit = false;
					var radio_checked;
					var checkbox_checked;
					var val;

					element = $( element );
					if ( element.is( '.cpf-type-product' ) ) {
						elementToCheck = ':not(.tc-extra-product-options-inline .tm-epo-field)';
					}
					_class = element
						.attr( 'class' )
						.split( ' ' )
						.map( function( cls ) {
							if ( cls.indexOf( 'cpf-type-', 0 ) !== -1 ) {
								return cls;
							}
							return null;
						} )
						.filter( function( v ) {
							if ( v !== null && v !== undefined ) {
								return v;
							}
							return null;
						} );

					if ( _class.length > 0 ) {
						_class = _class[ 0 ];
						if ( _class === 'cpf-type-product' ) {
							noSplit = true;
							if ( element.is( '.cpf-type-product-mode-product' ) || element.is( '.cpf-type-product-checkbox' ) || element.is( '.cpf-type-product-thumbnailmultiple' ) ) {
								_class = 'cpf-type-checkbox';
							} else if ( element.is( '.cpf-type-product-dropdown' ) ) {
								_class = 'cpf-type-select';
							} else {
								_class = 'cpf-type-radio';
							}
						}
						switch ( _class ) {
							case 'cpf-type-radio':
								radio_checked = element.find( 'input.tm-epo-field.tmcp-radio:checked' + elementToCheck );
								return radio_checked.length > 0;

							case 'cpf-type-checkbox':
								checkbox_checked = element.find( 'input.tm-epo-field.tmcp-checkbox:checked' + elementToCheck );
								return checkbox_checked.length > 0;

							case 'cpf-type-select':
								val = element.find( 'select.tm-epo-field.tmcp-select' + elementToCheck ).val();
								if ( val && ! noSplit ) {
									val = val.slice( 0, val.lastIndexOf( '_' ) );
								}
								break;

							case 'cpf-type-selectmultiple':
								val = element.find( 'select.tm-epo-field.tmcp-selectmultiple' + elementToCheck ).val();
								if ( val && ! noSplit ) {
									val.forEach( function( option, i ) {
										val[ i ] = option.slice( 0, option.lastIndexOf( '_' ) );
									} );
								}
								break;

							default:
								val = element.find( '.tm-epo-field' + elementToCheck ).val();
								break;
						}
						if ( Array.isArray( val ) ) {
							return val.some( function( item ) {
								return item !== '';
							} );
						}
						return val !== '';
					}
					return false;
				} );
				if ( TMEPOJS.tm_epo_hide_all_add_cart_button === 'yes' ) {
					showHideCart = allElementsAreSelected;
				}
				if ( TMEPOJS.tm_epo_hide_totals_until_all === 'yes' ) {
					showHideTotal = allElementsAreSelected;
				}
			}

			currentEpoObject.showHideCart = showHideCart;
			currentEpoObject.showHideTotal = showHideTotal;
			$.tcepo.showHideTotal[ epoEventId ] = showHideTotal;
		}

		currentEpoObject = $.epoAPI.applyFilter( 'tc_currentEpoObject', currentEpoObject, epoEventId, epoObject );

		return currentEpoObject;
	}

	function goto_error_item( item, epoEventId ) {
		var el = $.tcepo.errorObject[ epoEventId ] || item;
		var elsection;
		var elsectionlink;
		var cpfElement;
		var productSection;
		var pos;

		if ( el ) {
			if ( TMEPOJS.tm_epo_disable_error_scroll !== 'yes' ) {
				elsection = el.closest( '.cpf-section' );
				elsectionlink = elsection.find( '.tm-section-link' );
				cpfElement = el.closest( '.cpf-element' );
				productSection = el.closest( '.cpf-type-product' ).closest( '.cpf-section' );

				if ( productSection.length && productSection.find( '.tm-toggle' ).length ) {
					productSection.find( '.tm-toggle' ).trigger( 'openwrap.tmtoggle' );
				}

				if ( elsection.find( '.tm-toggle' ).length ) {
					elsection.find( '.tm-toggle' ).trigger( 'openwrap.tmtoggle' );
				}
				if ( window.tc_validation_offset === undefined ) {
					window.tc_validation_offset = -100;
				}
				if ( elsection.is( '.section_popup' ) ) {
					errorContainer.tcScrollTo( elsectionlink, 300, window.tc_validation_offset );
					elsectionlink.trigger( 'click.tmsectionpoplink' );
				} else if ( elsection.is( '.tm-owl-slider-section' ) ) {
					pos = el.closest( '.owl-item' ).index();
					elsection.find( '.tcowl-carousel' ).trigger( 'to.owl.carousel', [ pos, 100 ] );
					setTimeout( function() {
						elsection.find( '.tcowl-carousel' ).trigger( 'refresh.owl.carousel' );

						if ( cpfElement.length > 0 ) {
							errorContainer.tcScrollTo( cpfElement, 300, window.tc_validation_offset );
						}
					}, 200 );
				} else if ( elsection.is( '.tc-tabs-section' ) ) {
					pos = el.closest( '.tc-tab-slide' ).index();
					el.closest( '.tc-tabs' ).find( '.tc-tab-headers .tc-tab-header .tab-header[data-id="tc-tab-slide' + pos + '"]' ).trigger( 'click.tmtabs' );
					setTimeout( function() {
						if ( cpfElement.length > 0 ) {
							errorContainer.tcScrollTo( cpfElement, 300, window.tc_validation_offset );
						}
					}, 200 );
				} else if ( cpfElement.length > 0 ) {
					errorContainer.tcScrollTo( cpfElement, 300, window.tc_validation_offset );
				}
			}

			if ( ! item ) {
				$.tcepo.errorObject[ epoEventId ] = false;
			}
		}
	}

	function tm_limit_c_selection( field, prevent ) {
		var allowed = parseInt( field.attr( 'data-limit' ), 10 );
		var checked = false;
		var val;
		var t;
		var q;

		if ( allowed > 0 ) {
			checked = 0;
			field
				.closest( '.tm-extra-product-options-checkbox' )
				.find( "input.tm-epo-field[type='checkbox']:checked" )
				.each( function() {
					t = $( this );
					q = t.closest( 'li.tmcp-field-wrap' ).find( 'input.tm-qty' );
					if ( q.length > 0 ) {
						val = parseInt( q.val(), 10 );
						if ( val <= 0 ) {
							val = 1;
						}
						checked = parseInt( checked, 10 ) + val;
					} else {
						checked = parseInt( checked, 10 ) + 1;
					}
				} );
			if ( checked > allowed ) {
				if ( prevent ) {
					field.prop( 'checked', '' ).trigger( 'change' );
				}
				return false;
			}
		}
		return true;
	}

	function tm_exact_c_selection( field, prevent ) {
		var allowed = parseInt( field.attr( 'data-exactlimit' ), 10 );
		var checked = false;
		var val;
		var t;
		var q;

		if ( allowed > 0 ) {
			checked = 0;
			field
				.closest( '.tm-extra-product-options-checkbox' )
				.find( "input.tm-epo-field[type='checkbox']:checked" )
				.each( function() {
					t = $( this );
					q = t.closest( 'li.tmcp-field-wrap' ).find( 'input.tm-qty' );
					if ( q.length > 0 ) {
						val = parseInt( q.val(), 10 );
						if ( val <= 0 ) {
							val = 1;
						}
						checked = parseInt( checked, 10 ) + val;
					} else {
						checked = parseInt( checked, 10 ) + 1;
					}
				} );
			if ( checked > allowed ) {
				if ( prevent ) {
					field.prop( 'checked', '' ).trigger( 'change' );
				}
				return false;
			}
		}
		return true;
	}

	function tm_limit_cont( fields, main_product, epoEventId ) {
		var checkall = true;
		var first_error_obj = false;
		var limit;
		var eln;
		var checked;
		var t;
		var val;
		var q;
		var ew;
		var em;
		var message;
		var field;

		fields.each( function() {
			field = $( this );
			limit = field.find( "[type='checkbox'][data-limit]" );
			if ( limit.length && field_is_active( limit ) ) {
				eln = parseInt( limit.attr( 'data-limit' ), 10 );
				checked = 0;
				field.find( "input.tm-epo-field[type='checkbox']:checked" ).each( function() {
					t = $( this );
					q = t.closest( 'li.tmcp-field-wrap' ).find( 'input.tm-qty' );
					if ( q.length > 0 ) {
						val = parseInt( q.val(), 10 );
						if ( val <= 0 ) {
							val = 1;
						}
						checked = parseInt( checked, 10 ) + val;
					} else {
						checked = parseInt( checked, 10 ) + 1;
					}
				} );
				ew = field.closest( '.cpf-element' );
				em = ew.find( 'div.tm-error-min' );

				if ( eln < checked ) {
					checkall = false;
					first_error_obj = field;
					if ( eln === 1 ) {
						message = TMEPOJS.tm_epo_global_validator_messages.epolimitsingle.replace( '{0}', eln );
					} else {
						message = TMEPOJS.tm_epo_global_validator_messages.epolimit.replace( '{0}', eln );
					}
					if ( em.length ) {
						em.remove();
					}
					if ( TMEPOJS.tm_epo_global_error_label_placement === 'before' ) {
						field.closest( '.tc-element-container' ).before( '<div class="tm-error-min tm-error tc-cell tcwidth tcwidth-100">' + message + '</div>' );
					} else {
						field.closest( '.tc-element-container' ).after( '<div class="tm-error-min tm-error tc-cell tcwidth tcwidth-100">' + message + '</div>' );
					}
					main_product.find( tcAPI.addToCartButtonSelector ).first().removeClass( 'disabled loading fpd-disabled' ).prop( 'disabled', false );
				} else {
					em.remove();
				}
			}
		} );
		if ( first_error_obj ) {
			$.tcepo.errorObject[ epoEventId ] = first_error_obj;
		}
		return checkall;
	}

	function tm_check_limit_cont( limit_cont, main_product, epoEventId ) {
		$.tcepo.formSubmitEvents[ epoEventId ][ $.tcepo.formSubmitEvents[ epoEventId ].length ] = {
			trigger: function() {
				return tm_limit_cont( limit_cont, main_product, epoEventId );
			},
			on_true: function() {
				return true;
			},
			on_false: function() {
				goto_error_item( $( limit_cont ).find( '.tm-epo-field' ).first(), epoEventId );
				return true;
			}
		};
	}

	function tm_exactlimit_cont( fields, main_product, epoEventId ) {
		var checkall = true;
		var first_error_obj = false;
		var exactlimit;
		var eln;
		var checked;
		var t;
		var val;
		var q;
		var ew;
		var em;
		var message;
		var field;

		fields.each( function() {
			field = $( this );
			exactlimit = field.find( "[type='checkbox'][data-exactlimit]" );
			if ( exactlimit.length && field_is_active( exactlimit ) ) {
				eln = parseInt( exactlimit.attr( 'data-exactlimit' ), 10 );
				checked = 0;
				field.find( "input.tm-epo-field[type='checkbox']:checked" ).each( function() {
					t = $( this );
					q = t.closest( 'li.tmcp-field-wrap' ).find( 'input.tm-qty' );
					if ( q.length > 0 ) {
						val = parseInt( q.val(), 10 );
						if ( val <= 0 ) {
							val = 1;
						}
						checked = parseInt( checked, 10 ) + val;
					} else {
						checked = parseInt( checked, 10 ) + 1;
					}
				} );
				ew = field.closest( '.cpf-element' );
				em = ew.find( 'div.tm-error-min' );

				if ( eln !== checked ) {
					checkall = false;
					first_error_obj = field;
					if ( eln === 1 ) {
						message = TMEPOJS.tm_epo_global_validator_messages.epoexactsingle.replace( '{0}', eln );
					} else {
						message = TMEPOJS.tm_epo_global_validator_messages.epoexact.replace( '{0}', eln );
					}
					if ( em.length ) {
						em.remove();
					}
					if ( TMEPOJS.tm_epo_global_error_label_placement === 'before' ) {
						field.closest( '.tc-element-container' ).before( '<div class="tm-error-min tm-error tc-cell tcwidth tcwidth-100">' + message + '</div>' );
					} else {
						field.closest( '.tc-element-container' ).after( '<div class="tm-error-min tm-error tc-cell tcwidth tcwidth-100">' + message + '</div>' );
					}
					main_product.find( tcAPI.addToCartButtonSelector ).first().removeClass( 'disabled loading fpd-disabled' ).prop( 'disabled', false );
				} else {
					em.remove();
				}
			}
		} );
		if ( first_error_obj ) {
			$.tcepo.errorObject[ epoEventId ] = first_error_obj;
		}
		return checkall;
	}

	function tm_check_exactlimit_cont( exactlimit_cont, main_product, epoEventId ) {
		$.tcepo.formSubmitEvents[ epoEventId ][ $.tcepo.formSubmitEvents[ epoEventId ].length ] = {
			trigger: function() {
				return tm_exactlimit_cont( exactlimit_cont, main_product, epoEventId );
			},
			on_true: function() {
				return true;
			},
			on_false: function() {
				goto_error_item( $( exactlimit_cont ).find( '.tm-epo-field' ).first(), epoEventId );
				return true;
			}
		};
	}

	function tm_minimumlimit_cont( fields, epoEventId ) {
		var checkall = true;
		var first_error_obj = false;
		var eln;
		var checked;
		var t;
		var val;
		var q;
		var ew;
		var em;
		var message;
		var field;

		fields.each( function() {
			var minimumlimit;

			field = $( this );
			minimumlimit = field.find( "[type='checkbox'][data-minimumlimit]" );

			if ( minimumlimit.length && field_is_active( minimumlimit ) ) {
				eln = parseInt( minimumlimit.attr( 'data-minimumlimit' ), 10 );
				checked = 0;
				field.find( "input.tm-epo-field[type='checkbox']:checked" ).each( function() {
					t = $( this );
					q = t.closest( 'li.tmcp-field-wrap' ).find( 'input.tm-qty' );
					if ( q.length > 0 ) {
						val = parseInt( q.val(), 10 );
						if ( val <= 0 ) {
							val = 1;
						}
						checked = parseInt( checked, 10 ) + val;
					} else {
						checked = parseInt( checked, 10 ) + 1;
					}
				} );
				ew = field.closest( '.cpf-element' );
				em = ew.find( 'div.tm-error-min' );
				if ( eln > checked ) {
					checkall = false;
					first_error_obj = field;
					if ( eln === 1 ) {
						message = TMEPOJS.tm_epo_global_validator_messages.epominsingle.replace( '{0}', eln );
					} else {
						message = TMEPOJS.tm_epo_global_validator_messages.epomin.replace( '{0}', eln );
					}
					if ( em.length ) {
						em.remove();
					}
					if ( TMEPOJS.tm_epo_global_error_label_placement === 'before' ) {
						field.closest( '.tc-element-container' ).before( '<div class="tm-error-min tm-error tc-cell tcwidth tcwidth-100">' + message + '</div>' );
					} else {
						field.closest( '.tc-element-container' ).after( '<div class="tm-error-min tm-error tc-cell tcwidth tcwidth-100">' + message + '</div>' );
					}
				} else {
					em.remove();
				}
			}
		} );

		if ( first_error_obj ) {
			$.tcepo.errorObject[ epoEventId ] = first_error_obj;
		}

		return checkall;
	}

	function tm_check_minimumlimit_cont( minimumlimit_cont, epoEventId ) {
		$.tcepo.formSubmitEvents[ epoEventId ][ $.tcepo.formSubmitEvents[ epoEventId ].length ] = {
			trigger: function() {
				return tm_minimumlimit_cont( minimumlimit_cont, epoEventId );
			},
			on_true: function() {
				return true;
			},
			on_false: function() {
				goto_error_item( false, epoEventId );
				return true;
			}
		};
	}

	function cleanPrice( price ) {
		if ( price === null ) {
			return 0;
		}

		if ( typeof price === 'object' ) {
			price = price[ 0 ];
		}

		if ( ! Number.isFinite( parseFloat( price ) ) ) {
			price = 0;
		}

		return price;
	}

	function tm_apply_dpd( price, totals, apply, force ) {
		price = cleanPrice( price );

		if ( apply ) {
			price = $.epoAPI.applyFilter( 'tc_apply_dpd', price, totals, apply, force );
		}

		return price;
	}

	function tm_calculate_product_regular_price( totals, allowfalse ) {
		var price = 0;

		if ( totals.length > 0 ) {
			price = totals.data( 'regular-price' );
		}

		price = $.epoAPI.applyFilter( 'tc_calculate_product_regular_price', price, totals );

		if ( allowfalse && price === false ) {
			return false;
		}
		price = parseFloat( price );

		if ( ! Number.isFinite( price ) ) {
			price = 0;
		}

		return price;
	}

	function tm_calculate_product_price( totals, allowfalse ) {
		var price = 0;

		if ( totals.length > 0 ) {
			price = totals.data( 'price' );
		}

		price = $.epoAPI.applyFilter( 'tc_calculate_product_price', price, totals );

		if ( allowfalse && price === false ) {
			return false;
		}
		price = parseFloat( price );

		if ( ! Number.isFinite( price ) ) {
			price = 0;
		}

		return price;
	}

	function calculateMathPrice( price, thisElement, epoObject, noevents, useOriginalPrice, funcTotal, cumulativeTotal, mathskip ) {
		var formula = price.toString();
		var val = 0;
		var matches;
		var match;
		var elementWrap;
		var allElements;
		var element;
		var reg;
		var elementPrice = 0;
		var pos;
		var type;
		var id;
		var thisVal;
		var thisValForced;
		var thisElementId = thisElement.closest( '.cpf-element' ).attr( 'data-uniqid' );
		var thisElementWrap = thisElement.closest( '.tmcp-ul-wrap' );
		var totalsHolder_tc_totals_ob = epoObject.this_epo_totals_container.data( 'totalsHolder_tc_totals_ob' );
		var thisElementIndex = thisElementWrap.find( '.tmcp-field, .tmcp-fee-field, .tmcp-sub-fee-field' ).filter( ':checked' ).index( thisElement );
		var thisElementIndexForced = thisElementWrap.find( '.tmcp-field, .tmcp-fee-field, .tmcp-sub-fee-field' ).index( thisElement );
		var this_epo_container = epoObject.is_associated ? epoObject.this_epo_container : epoObject.this_epo_container.not( tcAPI.epoSelector + ' ' + tcAPI.associatedEpoSelector );
		var constants = $.epoAPI.util.parseJSON( TMEPOJS.tm_epo_math ) || {};
		var productPrice;
		var dynamicProductPrice;
		var __price;
		var __price_type;

		if ( thisElementIndex === -1 && ! thisElement.is( '.tmcp-checkbox, .tmcp-radio' ) ) {
			thisElementIndex = 0;
		}
		if ( thisElementIndexForced === -1 && ! thisElement.is( '.tmcp-checkbox, .tmcp-radio' ) ) {
			thisElementIndexForced = 0;
		}

		if ( ! thisElement.is( '.tcdisabled' ) && ! noevents && ! thisElement.data( 'addedtcEpoBeforeOptionPriceCalculation' ) ) {
			jWindow.on( 'tcEpoBeforeOptionPriceCalculation.math' + thisElementId, function() {
				tm_element_epo_rules( epoObject, thisElement, undefined, undefined, undefined, true );
			} );
			jWindow.on( 'tcEpoAfterNormalOptionPriceCalculation.math' + thisElementId, function() {
				thisElement.data( 'fetchOptionPrices', false );
				thisElement.data( 'fetchOptionPrices-forced', false );
				thisElement.data( 'fetchOptionPrices-fee', false );
				thisElement.data( 'fetchOptionPrices-fee-forced', false );
				thisElement.data( 'fetchOptionPrices-sub-fee', false );
				thisElement.data( 'fetchOptionPrices-sub-fee-forced', false );
			} );
			thisElement.data( 'addedtcEpoBeforeOptionPriceCalculation', 1 );
		}

		if ( thisElement.is( '.tmcp-field' ) ) {
			if ( thisElement.data( 'fetchOptionPrices' ) ) {
				thisVal = thisElement.data( 'fetchOptionPrices' );
				thisValForced = thisElement.data( 'fetchOptionPrices-forced' );
			} else {
				thisVal = fetchOptionPrices( epoObject, thisElementWrap, '.tmcp-field', 0, 0, [], true );
				thisValForced = fetchOptionPrices( epoObject, thisElementWrap, '.tmcp-field', 0, 0, [], true, true );
				thisElement.data( 'fetchOptionPrices', thisVal );
				thisElement.data( 'fetchOptionPrices-forced', thisValForced );
			}
		} else if ( thisElement.is( '.tmcp-fee-field' ) ) {
			if ( thisElement.data( 'fetchOptionPrices-fee' ) ) {
				thisVal = thisElement.data( 'fetchOptionPrices-fee' );
				thisValForced = thisElement.data( 'fetchOptionPrices-fee-forced' );
			} else {
				thisVal = fetchOptionPrices( epoObject, thisElementWrap, '.tmcp-fee-field', 0, 0, [], true );
				thisValForced = fetchOptionPrices( epoObject, thisElementWrap, '.tmcp-fee-field', 0, 0, [], true, true );
				thisElement.data( 'fetchOptionPrices-fee', thisVal );
				thisElement.data( 'fetchOptionPrices-fee-forced', thisValForced );
			}
		} else if ( thisElement.is( '.tmcp-sub-fee-field' ) ) {
			if ( thisElement.data( 'fetchOptionPrices-sub-fee' ) ) {
				thisVal = thisElement.data( 'fetchOptionPrices-sub-fee' );
				thisValForced = thisElement.data( 'fetchOptionPrices-sub-fee-forced' );
			} else {
				thisVal = fetchOptionPrices( epoObject, thisElementWrap, '.tmcp-sub-fee-field', 0, 0, [], true );
				thisValForced = fetchOptionPrices( epoObject, thisElementWrap, '.tmcp-sub-fee-field', 0, 0, [], true, true );
				thisElement.data( 'fetchOptionPrices-sub-fee', thisVal );
				thisElement.data( 'fetchOptionPrices-sub-fee-forced', thisValForced );
			}
		}

		Object.keys( constants ).forEach( function( key ) {
			var name;
			var value;

			if ( constants[ key ].name !== '' && constants[ key ].value !== '' ) {
				name = constants[ key ].name;
				value = constants[ key ].value;
				if ( name.startsWith( '{' ) ) {
					formula = formula.replace( name, value );
				} else if ( constants[ key ].name.isNumeric() ) {
					formula = formula.replace( '{' + name + '}', value );
				} else {
					name = new RegExp( '{' + name + '}', 'g' );
					if ( value.startsWith( '{' ) ) {
						formula = formula.replace( name, value );
					} else {
						formula = formula.replace( name, $.epoAPI.math.unformat( value.replace( ',', '.' ) ) );
					}
				}
			}
		} );

		if ( totalsHolder_tc_totals_ob !== undefined ) {
			// product quantity
			formula = formula.replace( /{quantity}/g, $.epoAPI.math.unformat( totalsHolder_tc_totals_ob.qty ) );
			// original product price
			if ( useOriginalPrice && totalsHolder_tc_totals_ob.original_product_price !== undefined ) {
				productPrice = $.epoAPI.math.unformat( totalsHolder_tc_totals_ob.original_product_price );
			} else {
				productPrice = $.epoAPI.math.unformat( totalsHolder_tc_totals_ob.product_price );
			}
			if ( totalsHolder_tc_totals_ob.dynamic_product_price !== undefined ) {
				dynamicProductPrice = $.epoAPI.math.unformat( totalsHolder_tc_totals_ob.dynamic_product_price );
			} else {
				dynamicProductPrice = 0;
			}
		} else {
			// product quantity
			formula = formula.replace( /{quantity}/g, 0 );
			// original product price
			productPrice = 0;
			dynamicProductPrice = 0;
		}

		productPrice = $.epoAPI.math.toFloat( productPrice );

		formula = formula.replace( /{product_price}/g, productPrice );
		formula = formula.replace( /{dynamic_product_price}/g, dynamicProductPrice );

		if ( ! funcTotal ) {
			funcTotal = 0;
		}
		funcTotal = $.epoAPI.math.toFloat( funcTotal );

		if ( ! cumulativeTotal ) {
			cumulativeTotal = 0;
		}
		cumulativeTotal = $.epoAPI.math.toFloat( cumulativeTotal );

		formula = formula.replace( /{options_total}/g, funcTotal );
		formula = formula.replace( /{product_price_plus_options_total}/g, productPrice + funcTotal );
		formula = formula.replace( /{cumulative_total}/g, cumulativeTotal );
		formula = formula.replace( /{product_price_plus_cumulative_total}/g, productPrice + cumulativeTotal );

		if ( thisValForced.floatingBoxData !== undefined && thisValForced.floatingBoxData[ thisElementIndexForced ] !== undefined ) {
			// the option/element value to float
			if ( thisValForced.floatingBoxData[ thisElementIndexForced ].input_type === 'number' || thisValForced.floatingBoxData[ thisElementIndexForced ].input_type === 'hidden' ) {
				formula = formula
					.replace( /{this.value}/g, $.epoAPI.math.toFloat( $.epoAPI.math.unformat( thisValForced.floatingBoxData[ thisElementIndexForced ].valueText ) ) );
			} else {
				formula = formula
					.replace( /{this.value}/g, $.epoAPI.math.toFloat( $.epoAPI.math.unformat( thisValForced.floatingBoxData[ thisElementIndexForced ].valueText, tcAPI.localDecimalSeparator ) ) );
			}
			// the option/element raw value
			formula = formula.replace( /{this.rawvalue}/g, thisValForced.floatingBoxData[ thisElementIndexForced ].valueText );
			// the option/element raw value
			formula = formula.replace( /{this.text}/g, thisValForced.floatingBoxData[ thisElementIndexForced ].valueText );
			// the option/element value length
			formula = formula
				.replace( /{this.value.length}/g, thisValForced.floatingBoxData[ thisElementIndexForced ].valueText.length );
		} else {
			formula = formula
				// the option/element value to float
				.replace( /{this.value}/g, 0 )
				// the option/element raw value
				.replace( /{this.rawvalue}/g, '' )
				// the option/element raw value
				.replace( /{this.text}/g, '' )
				// the option/element value length
				.replace( /{this.value.length}/g, 0 );
		}

		formula = formula
			// the number of options the user has selected
			.replace( /{this.count}/g, thisVal.floatingBoxData.length )
			// the total option quantity of this element
			.replace(
				/{this.count.quantity}/g,
				thisVal.floatingBoxData
					.map( function( x ) {
						return x.quantity;
					} )
					.reduce( function( acc, thisval ) {
						return $.epoAPI.math.toFloat( acc ) + $.epoAPI.math.toFloat( thisval );
					}, 0 )
			)
			// the option quantity of this element
			.replace( /{this.quantity}/g, thisElement.data( 'tm-quantity' ) );

		if ( formula.match( /\{(\s)*?field\.([^}]*)}/ ) ) {
			matches = formula.match( /\{(\s)*?field\.([^}]*)}/g );
			matches.forEach( function( field ) {
				match = field.match( /\{(\s)*?field\.([^}]*)}/ );
				if ( undefined !== match[ 2 ] && 'string' === typeof match[ 2 ] ) {
					pos = match[ 2 ].indexOf( '.', match[ 2 ].indexOf( '.' ) + 1 );
					if ( pos !== -1 ) {
						id = match[ 2 ].substring( 0, pos );
						type = match[ 2 ].substring( pos + 1 );

						if ( $.inArray( type, [ 'price', 'value', 'value.length', 'rawvalue', 'text', 'text.length', 'quantity', 'count', 'count.quantity' ] ) !== -1 ) {
							elementWrap = this_epo_container.find( "[data-uniqid='" + $.epoAPI.util.escapeSelector( id ) + "']" );
							if ( elementWrap.length ) {
								allElements = elementWrap.find( '.tmcp-field, .tmcp-fee-field, .tmcp-sub-fee-field' );
								element = allElements.first();

								if ( ! thisElement.is( '.tcdisabled' ) && ! noevents && ! thisElement.data( 'addedfieldtcEpoBeforeOptionPriceCalculation' ) ) {
									jWindow.on( 'tcEpoBeforeOptionPriceCalculation.math' + thisElementId, function() {
										tm_element_epo_rules( epoObject, thisElement, undefined, undefined, undefined, true );
										thisElement.trigger( 'tm-math-select-change-html-all' );
									} );
									thisElement.data( 'addedfieldtcEpoBeforeOptionPriceCalculation', 1 );
								}
								val = 0;
								switch ( type ) {
									case 'text':
									case 'rawvalue':
										val = '';
										break;
								}

								if ( elementWrap.is( '.tc-container-enabled' ) ) {
									if ( ! mathskip ) {
										allElements.toArray().forEach( function( singleElement ) {
											singleElement = $( singleElement );
											// For now we only recalculate dynamic elements
											if ( thisElement.attr( 'id' ) !== singleElement.attr( 'id' ) && ( thisElement.is( '.tmcp-dynamic' ) || singleElement.is( '.tmcp-dynamic' ) ) ) {
												tm_element_epo_rules( epoObject, singleElement, undefined, undefined, undefined, true, undefined, undefined, true );
											}
											if ( ! noevents && ! singleElement.data( 'addedmathevent' ) ) {
												singleElement.on( 'input.mathevent', function() {
													if ( ! thisElement.is( '.tcdisabled' ) ) {
														thisElement.trigger( 'change.cpf', { calculateMatcPrice: 1 } );
													}
												} );
												singleElement.data( 'addedmathevent', 1 );
											}
										} );
									}
									if ( element.is( '.tmcp-field' ) ) {
										val = fetchOptionPrices( epoObject, elementWrap, '.tmcp-field', 0, 0, [], true, undefined, undefined, true, undefined, undefined, true );
									} else if ( element.is( '.tmcp-fee-field' ) ) {
										val = fetchOptionPrices( epoObject, elementWrap, '.tmcp-fee-field', 0, 0, [], true, undefined, undefined, true, undefined, undefined, true );
									} else if ( element.is( '.tmcp-sub-fee-field' ) ) {
										val = fetchOptionPrices( epoObject, elementWrap, '.tmcp-sub-fee-field', 0, 0, [], true, undefined, undefined, true, undefined, undefined, true );
									}

									switch ( type ) {
										// element price
										case 'price':
											val = val.total;
											if ( thisElement.is( '.tmcp-dynamic' ) ) {
												__price = get_type( epoObject, element, 'price' );
												__price_type = get_type( epoObject, element, 'price_type' );
												if ( 'math' === __price_type && __price.includes( '{dynamic_product_price}' ) ) {
													val = 0;
												}
											}
											break;

										// element value
										case 'value':
										case 'text':
										case 'rawvalue':
											if ( val.floatingBoxData ) {
												val = val.floatingBoxData
													.map( function( x ) {
														if ( x.input_type === 'number' || x.input_type === 'hidden' ) {
															if ( type === 'text' || type === 'rawvalue' ) {
																return x.valueText;
															}
															return $.epoAPI.math.unformat( x.valueText );
														}
														if ( type === 'text' || type === 'rawvalue' ) {
															return x.valueText;
														}
														return $.epoAPI.math.unformat( x.valueText, tcAPI.localInputDecimalSeparator );
													} )
													.reduce( function( acc, thisval ) {
														if ( type === 'text' || type === 'rawvalue' ) {
															return acc + thisval;
														}
														return $.epoAPI.math.toFloat( acc ) + $.epoAPI.math.toFloat( thisval );
													}, ( type === 'text' || type === 'rawvalue' ) ? '' : 0 );

												if ( type === 'text' || type === 'rawvalue' ) {
													if ( val === '' ) {
														val = "''";
													} else if ( ! val.toString().isNumeric() ) {
														val = "'" + val.replace( "'", "\\'" ) + "'";
													}
												}
											}
											break;

										// element value length
										case 'value.length':
										case 'text.length':
											if ( val.floatingBoxData ) {
												val = val.floatingBoxData
													.map( function( x ) {
														return x.valueText;
													} )
													.reduce( function( acc, thisval ) {
														return $.epoAPI.math.toFloat( acc ) + thisval.length;
													}, 0 );
											}
											break;

										// element quantity
										// the total option quantity of this element
										case 'quantity':
										case 'count.quantity':
											if ( val.floatingBoxData ) {
												val = val.floatingBoxData
													.map( function( x ) {
														return x.quantity;
													} )
													.reduce( function( acc, thisval ) {
														return $.epoAPI.math.toFloat( acc ) + $.epoAPI.math.toFloat( thisval );
													}, 0 );
											}
											break;

										// number of element options the user has selected
										case 'count':
											if ( val.floatingBoxData ) {
												val = val.floatingBoxData.length;
											}
											break;
									}
									if ( type !== 'text' && type !== 'rawvalue' ) {
										val = $.epoAPI.math.toFloat( val );
										if ( ! Number.isFinite( val ) ) {
											val = 0;
										}
									}
								}
							} else {
								val = 0;
								switch ( type ) {
									case 'text':
									case 'rawvalue':
										val = '';
										break;
								}
							}
							reg = new RegExp( match[ 0 ] );
							if ( type === 'text' || type === 'rawvalue' ) {
								formula = val === '' ? formula.replace( reg, "''" ) : formula.replace( reg, val );
							} else {
								formula = ! Number.isFinite( val ) ? formula.replace( reg, "'" + val.replace( "'", "\\'" ) + "'" ) : formula.replace( reg, val );
							}
						}
					}
				}
			} );
		}

		try {
			elementPrice = tcmexp.evaluate( formula );
		} catch ( e ) {
			elementPrice = 0;
		}

		return elementPrice;
	}

	function get_type( epoObject, obj, type ) {
		var element = $( obj );
		var setter = element;
		var cart;
		var current_variation;
		var rules;
		var rulestype;
		var _rulestype;
		var pricetype;
		var variation_id_selector;
		var _tmcpulwrap;
		var price;
		var _rules;

		cart = epoObject.main_cart;
		variation_id_selector = "input[name^='variation_id']";
		if ( cart.find( 'input.variation_id' ).length > 0 ) {
			variation_id_selector = 'input.variation_id';
		}
		current_variation = cart.find( variation_id_selector ).val();
		// Get current woocommerce variation
		if ( ! current_variation ) {
			current_variation = 0;
		}

		if ( element.is( 'select' ) ) {
			setter = element.find( 'option:selected' );
		}

		rules = $.epoAPI.util.parseJSON( setter.attr( 'data-rules' ) );
		rulestype = $.epoAPI.util.parseJSON( setter.attr( 'data-rulestype' ) );

		pricetype = '';
		if ( typeof rules === 'object' ) {
			if ( current_variation in rules ) {
				price = rules[ current_variation ];
			} else {
				_rules = $.epoAPI.util.parseJSON( element.closest( '.tmcp-ul-wrap' ).attr( 'data-rules' ) );

				if ( typeof _rules === 'object' ) {
					if ( current_variation in _rules ) {
						price = _rules[ current_variation ];
					} else {
						price = rules[ 0 ];
					}
				} else {
					price = rules[ 0 ];
				}
			}
			if ( typeof rulestype === 'object' ) {
				if ( current_variation in rulestype ) {
					pricetype = rulestype[ current_variation ];
				} else {
					_rulestype = $.epoAPI.util.parseJSON( element.closest( '.tmcp-ul-wrap' ).attr( 'data-rulestype' ) );
					if ( typeof _rulestype === 'object' ) {
						if ( current_variation in _rulestype ) {
							pricetype = _rulestype[ current_variation ];
						} else {
							pricetype = rulestype[ 0 ];
						}
					} else {
						pricetype = rulestype[ 0 ];
					}
				}
			} else {
				rulestype = $.epoAPI.util.parseJSON( element.closest( '.tmcp-ul-wrap' ).attr( 'data-ulestype' ) );
				if ( typeof rulestype === 'object' ) {
					if ( current_variation in rulestype ) {
						pricetype = rulestype[ current_variation ];
					} else {
						pricetype = rulestype[ 0 ];
					}
				}
			}
		} else {
			_tmcpulwrap = element.closest( '.tmcp-ul-wrap' );
			rules = $.epoAPI.util.parseJSON( _tmcpulwrap.attr( 'data-rules' ) );

			if ( typeof rules === 'object' ) {
				if ( current_variation in rules ) {
					price = rules[ current_variation ];
				} else {
					price = rules[ 0 ];
				}
				if ( typeof rulestype === 'object' ) {
					if ( current_variation in rulestype ) {
						pricetype = rulestype[ current_variation ];
					} else {
						_rulestype = $.epoAPI.util.parseJSON( _tmcpulwrap.attr( 'data-rulestype' ) );
						if ( typeof _rulestype === 'object' ) {
							if ( current_variation in _rulestype ) {
								pricetype = _rulestype[ current_variation ];
							} else {
								pricetype = rulestype[ 0 ];
							}
						} else {
							pricetype = rulestype[ 0 ];
						}
					}
				} else {
					rulestype = $.epoAPI.util.parseJSON( _tmcpulwrap.attr( 'data-rulestype' ) );
					if ( typeof rulestype === 'object' ) {
						if ( current_variation in rulestype ) {
							pricetype = rulestype[ current_variation ];
						} else {
							pricetype = rulestype[ 0 ];
						}
					}
				}
			}
		}

		if ( typeof price === 'object' ) {
			price = price[ 0 ];
		}

		if ( typeof pricetype === 'object' ) {
			pricetype = pricetype[ 0 ];
		}
		if ( element.is( '.tmcp-fee-field' ) ) {
			if ( $.inArray( pricetype, [ 'fee', 'stepfee', 'currentstepfee' ] ) !== -1 ) {
				pricetype = '';
			}
		}

		if ( type === 'price' ) {
			return price;
		}

		return pricetype;
	}

	function wordLength( str ) {
		var regex = new RegExp( /[\p{L}\d!@#$%^&*()_+=\{[\}\]|\\"':;?/>.<,-]+/, 'gu' );
		var m;
		var len = 0;

		while ( ( m = regex.exec( str ) ) !== null ) {
			// This is necessary to avoid infinite loops with zero-width matches
			if ( m.index === regex.lastIndex ) {
				regex.lastIndex++;
			}
			len = len + m.length;
		}
		return len;
	}

	// Set field price rules
	function tm_element_epo_rules( epoObject, obj, args, setter_override, noremove, manthnoevent, funcTotal, cumulativeTotal, mathskip ) {
		var element = $( obj );
		var setterall = element;
		var cart;
		var current_variation;
		var bundleid;
		var epoTotalsContainer;
		var apply_dpd;
		var product_price;
		var product_original_price;
		var is_range_field = element.is( '.tmcp-range' );
		var rules;
		var rulestype;
		var original_rules;
		var _rules;
		var _rulestype;
		var _original_rules;
		var pricetype;
		var price;
		var original_price;
		var raw_price;
		var raw_original_price;
		var formatted_price = '';
		var textlength;
		var freechars;
		var min_value;
		var main_product = epoObject.main_product;
		var epoEventId = epoObject.epoEventId;
		var variation_id_selector;
		var _tmcpulwrap;
		var cart_total;
		var per_product_pricing = true;
		var addedPrice = 0;
		var originalAddedPrice = 0;
		var undiscounted_raw_price;
		var undiscounted_raw_original_price;
		var undiscounted_price;
		var undiscounted_original_price;
		var isFilled;
		var useFormattedPrice = false;

		if ( element.data( 'associated_price_set' ) ) {
			return;
		}

		if ( ! args ) {
			cart = epoObject.main_cart;
			if ( cart.data( 'per_product_pricing' ) !== undefined ) {
				per_product_pricing = cart.data( 'per_product_pricing' );
			}
			variation_id_selector = "input[name^='variation_id']";
			if ( cart.find( 'input.variation_id' ).length > 0 ) {
				variation_id_selector = 'input.variation_id';
			}
			current_variation = cart.find( variation_id_selector ).val();

			bundleid = $.epoAPI.applyFilter( 'tc_get_bundleid', cart.attr( 'data-product_id' ), cart, epoObject );

			// Get current woocommerce variation
			if ( ! current_variation ) {
				current_variation = 0;
			}

			epoTotalsContainer = $.epoAPI.applyFilter( 'tc_get_totals_container', epoObject.this_epo_totals_container, element, main_product, bundleid );
			product_price = tm_calculate_product_price( epoTotalsContainer );
			product_original_price = tm_calculate_product_regular_price( epoTotalsContainer );
			apply_dpd = epoTotalsContainer.data( 'fields-price-rules' );
		} else {
			cart = args.cart;
			current_variation = args.current_variation;

			bundleid = args.bundleid;
			epoTotalsContainer = args.epoTotalsContainer;
			product_price = args.product_price;
			product_original_price = args.product_original_price;
			apply_dpd = args.apply_dpd;
			per_product_pricing = args.per_product_pricing;
		}
		product_price = $.epoAPI.applyFilter( 'tc_alter_product_price', product_price, element, cart, epoTotalsContainer, bundleid );
		product_original_price = $.epoAPI.applyFilter( 'tc_alter_product_original_price', product_original_price, element, cart, epoTotalsContainer );

		if ( product_price === false || ! per_product_pricing ) {
			return;
		}

		if ( element.is( 'select' ) ) {
			setterall = element.find( 'option:selected' );
		}
		if ( setter_override ) {
			setterall = setter_override;
		}

		setterall.toArray().forEach( function( setter ) {
			setter = $( setter );

			rules = $.epoAPI.util.parseJSON( setter.attr( 'data-rules' ) );
			rulestype = $.epoAPI.util.parseJSON( setter.attr( 'data-rulestype' ) );
			original_rules = $.epoAPI.util.parseJSON( setter.attr( 'data-original-rules' ) );
			if ( element.is( '.tmcp-dynamic' ) ) {
				rules = $.epoAPI.util.parseJSON( setter.attr( 'data-formula' ) );
				original_rules = $.epoAPI.util.parseJSON( setter.attr( 'data-formula' ) );
				rulestype = $.epoAPI.util.parseJSON( setter.attr( 'data-calculation-type' ) );
			}
			if ( original_rules === undefined ) {
				original_rules = rules;
			}

			pricetype = '';
			if ( typeof rules === 'object' ) {
				if ( current_variation in rules ) {
					price = rules[ current_variation ];
					original_price = original_rules[ current_variation ];
				} else {
					_rules = $.epoAPI.util.parseJSON( element.closest( '.tmcp-ul-wrap' ).attr( 'data-rules' ) );
					_original_rules = element.closest( '.tmcp-ul-wrap' ).data( 'original-rules' );

					if ( typeof _rules === 'object' ) {
						if ( current_variation in _rules ) {
							price = _rules[ current_variation ];
						} else {
							price = rules[ 0 ];
						}
					} else {
						price = rules[ 0 ];
					}

					if ( typeof _original_rules === 'object' ) {
						if ( current_variation in _original_rules ) {
							original_price = _original_rules[ current_variation ];
						} else {
							original_price = original_rules[ 0 ];
						}
					} else {
						original_price = original_rules[ 0 ];
					}
				}

				if ( typeof rulestype === 'object' ) {
					if ( current_variation in rulestype ) {
						pricetype = rulestype[ current_variation ];
					} else {
						_rulestype = $.epoAPI.util.parseJSON( element.closest( '.tmcp-ul-wrap' ).attr( 'data-rulestype' ) );
						if ( typeof _rulestype === 'object' ) {
							if ( current_variation in _rulestype ) {
								pricetype = _rulestype[ current_variation ];
							} else {
								pricetype = rulestype[ 0 ];
							}
						} else {
							pricetype = rulestype[ 0 ];
						}
					}
				} else {
					rulestype = $.epoAPI.util.parseJSON( element.closest( '.tmcp-ul-wrap' ).attr( 'data-ulestype' ) );
					if ( typeof rulestype === 'object' ) {
						if ( current_variation in rulestype ) {
							pricetype = rulestype[ current_variation ];
						} else {
							pricetype = rulestype[ 0 ];
						}
					}
				}
			} else {
				_tmcpulwrap = element.closest( '.tmcp-ul-wrap' );
				rules = $.epoAPI.util.parseJSON( _tmcpulwrap.attr( 'data-rules' ) );
				original_rules = $.epoAPI.util.parseJSON( _tmcpulwrap.attr( 'data-original-rules' ) );

				if ( typeof rules === 'object' ) {
					if ( current_variation in rules ) {
						price = rules[ current_variation ];
					} else {
						price = rules[ 0 ];
					}
					if ( typeof original_rules === 'object' ) {
						if ( current_variation in original_rules ) {
							original_price = original_rules[ current_variation ];
						} else {
							original_price = original_rules[ 0 ];
						}
					} else {
						original_price = price;
					}

					if ( typeof rulestype === 'object' ) {
						if ( current_variation in rulestype ) {
							pricetype = rulestype[ current_variation ];
						} else {
							_rulestype = $.epoAPI.util.parseJSON( _tmcpulwrap.attr( 'data-rulestype' ) );
							if ( typeof _rulestype === 'object' ) {
								if ( current_variation in _rulestype ) {
									pricetype = _rulestype[ current_variation ];
								} else {
									pricetype = rulestype[ 0 ];
								}
							} else {
								pricetype = rulestype[ 0 ];
							}
						}
					} else {
						rulestype = $.epoAPI.util.parseJSON( _tmcpulwrap.attr( 'data-rulestype' ) );
						if ( typeof rulestype === 'object' ) {
							if ( current_variation in rulestype ) {
								pricetype = rulestype[ current_variation ];
							} else {
								pricetype = rulestype[ 0 ];
							}
						}
					}
				}
			}

			if ( typeof pricetype === 'object' ) {
				pricetype = pricetype[ 0 ];
			}
			if ( element.is( '.tmcp-fee-field' ) ) {
				if ( $.inArray( pricetype, [ 'fee', 'stepfee', 'currentstepfee' ] ) !== -1 ) {
					pricetype = '';
				}
				apply_dpd = 0;
			}

			if ( noremove === undefined ) {
				if ( element.is( 'select' ) ) {
					element.find( 'option' ).removeClass( 'tm-epo-late-field' ).removeData( 'tm-price-for-late islate' );
				} else {
					setter.removeClass( 'tm-epo-late-field' ).removeData( 'tm-price-for-late islate' );
				}
			}
			if ( pricetype !== 'math' ) {
				price = cleanPrice( price );
				original_price = cleanPrice( original_price );
			} else if ( typeof price === 'object' ) {
				price = price[ 0 ];
			}

			undiscounted_raw_price = price;
			undiscounted_raw_original_price = original_price;

			switch ( pricetype ) {
				case '':
					undiscounted_price = price;
					price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd );
					// original_price No change
					break;
				case 'math':
					price = calculateMathPrice( price, element, epoObject, manthnoevent, $.epoAPI.applyFilter( 'tc_use_undiscounted_price', undefined, element, cart, epoTotalsContainer ), funcTotal, cumulativeTotal, mathskip );
					undiscounted_price = price;
					price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd );
					original_price = calculateMathPrice( original_price, element, epoObject, true, true, funcTotal, cumulativeTotal, mathskip );
					// original_price No other change
					break;
				case 'percent_cart_total':
					cart_total = parseFloat( TMEPOJS.cart_total );

					if ( ! Number.isFinite( cart_total ) ) {
						cart_total = 0;
					}
					undiscounted_price = ( price / 100 ) * cart_total;
					price = ( tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) / 100 ) * cart_total;
					original_price = ( tm_apply_dpd( original_price, epoTotalsContainer, apply_dpd ) / 100 ) * cart_total;
					break;

				case 'percent':
					undiscounted_price = ( price / 100 ) * product_price;
					price = ( tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) / 100 ) * product_price;
					original_price = ( original_price / 100 ) * product_original_price;
					break;
				case 'percentcurrenttotal':
					$.tcepo.lateFieldsPrices[ epoEventId ].push( {
						setter: setter,
						price: price,
						original_price: original_price,
						bundleid: bundleid,
						pricetype: pricetype
					} );
					setter.data( 'tm-price-for-late', price ).data( 'tm-original-price-for-late', original_price ).data( 'islate', 1 ).addClass( 'tm-epo-late-field' );
					undiscounted_price = 0;
					price = 0;
					original_price = 0;
					break;
				case 'fixedcurrenttotal':
					price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd );
					$.tcepo.lateFieldsPrices[ epoEventId ].push( {
						setter: setter,
						price: price,
						original_price: original_price,
						bundleid: bundleid,
						pricetype: pricetype
					} );
					setter.data( 'tm-price-for-late', price ).data( 'tm-original-price-for-late', original_price ).data( 'islate', 1 ).addClass( 'tm-epo-late-field' );
					undiscounted_price = 0;
					price = 0;
					original_price = 0;
					break;
				case 'word':
					undiscounted_price = price * wordLength( setter.val() );
					price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) * wordLength( setter.val() );
					original_price = original_price * wordLength( setter.val() );
					break;
				case 'wordpercent':
					undiscounted_price = ( price / 100 ) * product_price * wordLength( setter.val() );
					price = ( tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) / 100 ) * product_price * wordLength( setter.val() );
					original_price = ( original_price / 100 ) * product_original_price * wordLength( setter.val() );
					break;
				case 'wordnon':
					freechars = parseInt( setter.attr( 'data-freechars' ), 10 );
					if ( ! Number.isFinite( freechars ) ) {
						freechars = 0;
					}
					textlength = wordLength( setter.val() ) - freechars;
					if ( textlength < 0 ) {
						textlength = 0;
					}
					undiscounted_price = price * textlength;
					price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) * textlength;
					original_price = original_price * textlength;
					break;
				case 'wordpercentnon':
					freechars = parseInt( setter.attr( 'data-freechars' ), 10 );
					if ( ! Number.isFinite( freechars ) ) {
						freechars = 0;
					}
					textlength = wordLength( setter.val() ) - freechars;
					if ( textlength < 0 ) {
						textlength = 0;
					}
					undiscounted_price = ( price / 100 ) * product_price * textlength;
					price = ( tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) / 100 ) * product_price * textlength;
					original_price = ( original_price / 100 ) * product_original_price * textlength;
					break;

				case 'char':
					undiscounted_price = price * setter.val().length;
					price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) * setter.val().length;
					original_price = original_price * setter.val().length;
					break;
				case 'charpercent':
					undiscounted_price = ( price / 100 ) * product_price * setter.val().length;
					price = ( tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) / 100 ) * product_price * setter.val().length;
					original_price = ( original_price / 100 ) * product_original_price * setter.val().length;
					break;
				case 'charnospaces':
					undiscounted_price = price * setter.val().replace( /\s/g, '' ).length;
					price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) * setter.val().replace( /\s/g, '' ).length;
					original_price = original_price * setter.val().replace( /\s/g, '' ).length;
					break;
				case 'charnofirst':
					textlength = setter.val().length - 1;
					if ( textlength < 0 ) {
						textlength = 0;
					}
					undiscounted_price = price * textlength;
					price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) * textlength;
					original_price = original_price * textlength;
					break;

				case 'charnon':
					freechars = parseInt( setter.attr( 'data-freechars' ), 10 );
					if ( ! Number.isFinite( freechars ) ) {
						freechars = 0;
					}
					textlength = setter.val().length - freechars;
					if ( textlength < 0 ) {
						textlength = 0;
					}
					undiscounted_price = price * textlength;
					price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) * textlength;
					original_price = original_price * textlength;
					break;
				case 'charpercentnon':
					freechars = parseInt( setter.attr( 'data-freechars' ), 10 );
					if ( ! Number.isFinite( freechars ) ) {
						freechars = 0;
					}
					textlength = setter.val().length - freechars;
					if ( textlength < 0 ) {
						textlength = 0;
					}
					undiscounted_price = ( price / 100 ) * product_price * textlength;
					price = ( tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) / 100 ) * product_price * textlength;
					original_price = ( original_price / 100 ) * product_original_price * textlength;
					break;
				case 'charnonnospaces':
					freechars = parseInt( setter.attr( 'data-freechars' ), 10 );
					if ( ! Number.isFinite( freechars ) ) {
						freechars = 0;
					}
					textlength = setter.val().replace( /\s/g, '' ).length - freechars;
					if ( textlength < 0 ) {
						textlength = 0;
					}
					undiscounted_price = price * textlength;
					price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) * textlength;
					original_price = original_price * textlength;
					break;
				case 'charpercentnonnospaces':
					freechars = parseInt( setter.attr( 'data-freechars' ), 10 );
					if ( ! Number.isFinite( freechars ) ) {
						freechars = 0;
					}
					textlength = setter.val().replace( /\s/g, '' ).length - freechars;
					if ( textlength < 0 ) {
						textlength = 0;
					}
					undiscounted_price = ( price / 100 ) * product_price * textlength;
					price = ( tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) / 100 ) * product_price * textlength;
					original_price = ( original_price / 100 ) * product_original_price * textlength;
					break;

				case 'charpercentnofirst':
					textlength = setter.val().length - 1;
					if ( textlength < 0 ) {
						textlength = 0;
					}
					undiscounted_price = ( price / 100 ) * product_price * textlength;
					price = ( tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) / 100 ) * product_price * textlength;
					original_price = ( original_price / 100 ) * product_original_price * textlength;
					break;
				case 'step':
					if ( is_range_field ) {
						undiscounted_price = price * setter.val();
						price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) * setter.val();
						original_price = original_price * $.epoAPI.math.toFloat( setter.val() );
					} else {
						undiscounted_price = price * $.epoAPI.math.toFloat( setter.val() );
						price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) * $.epoAPI.math.toFloat( setter.val() );
						original_price = original_price * $.epoAPI.math.toFloat( setter.val() );
					}
					break;
				case 'currentstep':
					if ( is_range_field ) {
						undiscounted_price = setter.val();
						price = tm_apply_dpd( setter.val(), epoTotalsContainer, apply_dpd );
						original_price = tm_apply_dpd( setter.val(), epoTotalsContainer, apply_dpd );
					} else {
						undiscounted_price = $.epoAPI.math.toFloat( setter.val() );
						price = tm_apply_dpd( $.epoAPI.math.toFloat( setter.val() ), epoTotalsContainer, apply_dpd );
						original_price = tm_apply_dpd( $.epoAPI.math.toFloat( setter.val() ), epoTotalsContainer, apply_dpd );
					}
					break;
				case 'intervalstep':
					if ( is_range_field ) {
						min_value = parseFloat( $( '.tm-range-picker[data-field-id="' + setter.attr( 'id' ) + '"]' ).attr( 'data-min' ) );
						undiscounted_price = price * ( setter.val() - min_value );
						price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) * ( setter.val() - min_value );
						original_price = original_price * ( setter.val() - min_value );
					}
					break;
				case 'row':
					undiscounted_price = price * ( ( setter.val().match( /(\r\n|\n|\r)/gm ) || [] ).length + 1 );
					price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) * ( ( setter.val().match( /(\r\n|\n|\r)/gm ) || [] ).length + 1 );
					original_price = original_price * ( ( setter.val().match( /(\r\n|\n|\r)/gm ) || [] ).length + 1 );
					break;
			}

			raw_price = price;
			raw_original_price = original_price;

			undiscounted_original_price = original_price;
			undiscounted_raw_price = undiscounted_price;
			undiscounted_raw_original_price = undiscounted_original_price;

			if ( element.data( 'tm-quantity' ) ) {
				undiscounted_price = undiscounted_price * parseFloat( element.data( 'tm-quantity' ) );
				undiscounted_original_price = undiscounted_original_price * parseFloat( element.data( 'tm-quantity' ) );
				undiscounted_raw_price = undiscounted_raw_price * parseFloat( element.data( 'tm-quantity' ) );
				undiscounted_raw_original_price = undiscounted_raw_original_price * parseFloat( element.data( 'tm-quantity' ) );

				price = price * parseFloat( element.data( 'tm-quantity' ) );
				original_price = original_price * parseFloat( element.data( 'tm-quantity' ) );
				raw_price = raw_price * parseFloat( element.data( 'tm-quantity' ) );
				raw_original_price = raw_original_price * parseFloat( element.data( 'tm-quantity' ) );
			}

			addedPrice = addedPrice + parseFloat( price );
			originalAddedPrice = originalAddedPrice + parseFloat( original_price );

			element.data( 'price_set', 1 );
			setter.data( 'price_set', 1 );

			setter.data( 'undiscounted_raw_price', undiscounted_raw_price );
			setter.data( 'undiscounted_raw_original_price', undiscounted_raw_original_price );
			setter.data( 'undiscounted_price', tm_set_tax_price( undiscounted_price, epoTotalsContainer, setter, pricetype ) );
			setter.data( 'undiscounted_original_price', tm_set_tax_price( undiscounted_original_price, epoTotalsContainer, setter, pricetype ) );

			setter.data( 'raw_price', raw_price );
			setter.data( 'raw_original_price', raw_original_price );
			setter.data( 'price', tm_set_tax_price( price, epoTotalsContainer, setter, pricetype ) );
			setter.data( 'original_price', tm_set_tax_price( original_price, epoTotalsContainer, setter, pricetype ) );

			if ( ! setter_override ) {
				if ( element.is( '.tc-epo-field-product' ) ) {
					isFilled = false;
					if ( element.is( 'select' ) ) {
						if ( element.val() !== '' ) {
							isFilled = true;
						}
					} else if ( ( element.is( ':checkbox' ) || element.is( ':radio' ) ) ) {
						if ( element.is( ':checked' ) ) {
							isFilled = true;
						}
					} else if ( element.val() !== '' ) {
						isFilled = true;
					}

					if ( ! isFilled ) {
						formatted_price = setter.data( 'price-html' );
						useFormattedPrice = true;
					}
				}
				tm_update_price( {
					obj: setter.closest( '.tmcp-field-wrap' ).find( '.tc-price' ),
					price: tm_get_price( addedPrice, epoTotalsContainer, false, setter, pricetype ),
					formatted_price: formatted_price,
					original_price: tm_get_price( originalAddedPrice, epoTotalsContainer, false, setter, pricetype ),
					force: false,
					useFormattedPrice: useFormattedPrice
				} );
				element.data( 'price-changed', 1 );
			}
		} );
	}

	function tm_epo_rules( epoObject, theCart ) {
		var all_carts;
		var variation_id_selector;
		var per_product_pricing = true;
		var current_variation;
		var bundleid;
		var epoContainer;
		var epoTotalsContainer;
		var apply_dpd;
		var rules;
		var original_rules;
		var price;
		var original_price;
		var product_price;
		var product_original_price;
		var all_fields;
		var active_fields;
		var args;
		var main_product = epoObject.main_product;
		var epoEventId = epoObject.epoEventId;
		var this_epo_container = epoObject.this_epo_container;
		var this_epo_totals_container = epoObject.this_epo_totals_container;

		if ( ! theCart ) {
			all_carts = main_product.find( '.cart' );
		} else {
			all_carts = theCart;
		}
		if ( all_carts.length <= 0 ) {
			return;
		}

		$.tcepo.lateFieldsPrices[ epoEventId ] = [];

		all_carts.toArray().forEach( function( cart ) {
			cart = $( cart );
			variation_id_selector = "input[name^='variation_id']";
			if ( cart.find( 'input.variation_id' ).length > 0 ) {
				variation_id_selector = 'input.variation_id';
			}

			if ( cart.data( 'per_product_pricing' ) !== undefined ) {
				per_product_pricing = cart.data( 'per_product_pricing' );
			}

			current_variation = cart.find( variation_id_selector ).val();
			bundleid = $.epoAPI.applyFilter( 'tc_get_bundleid', cart.attr( 'data-product_id' ), cart, epoObject );

			// get current woocommerce variation
			if ( ! current_variation ) {
				current_variation = 0;
			}

			epoContainer = $.epoAPI.applyFilter( 'tc_get_epo_container', this_epo_container, cart, main_product, bundleid );
			epoTotalsContainer = $.epoAPI.applyFilter( 'tc_get_totals_container', this_epo_totals_container, cart, main_product, bundleid );

			// WooCommerce Dynamic Pricing & Discounts
			apply_dpd = epoTotalsContainer.data( 'fields-price-rules' );

			// set initial prices for all fields
			if ( ! epoContainer.data( 'tm_rules_init_done' ) ) {
				if ( epoTotalsContainer.data( 'force-quantity' ) ) {
					cart.find( tcAPI.qtySelector ).val( epoTotalsContainer.data( 'force-quantity' ) );
				}
				epoContainer.toArray().forEach( function( el ) {
					$( el ).closest( '.tmcp-field-wrap' ).find( '.tm-epo-field' ).data( 'tm-quantity', $( el ).val() );
				} );

				epoContainer
					.find( '.tmcp-attributes, .tmcp-elements' )
					.toArray()
					.forEach( function( element ) {
						element = $( element );
						rules = $.epoAPI.util.parseJSON( element.attr( 'data-rules' ) );
						original_rules = $.epoAPI.util.parseJSON( element.attr( 'data-original-rules' ) );

						// if rule doesn't exit then init an empty rule
						if ( typeof rules !== 'object' ) {
							rules = {
								0: '0'
							};
						}
						if ( typeof original_rules !== 'object' ) {
							original_rules = {
								0: '0'
							};
						}
						if ( typeof rules === 'object' ) {
							// we skip price validation test so that every field has at least a price of 0
							price = tm_apply_dpd( rules[ $.epoAPI.math.toFloat( current_variation ) ], epoTotalsContainer, apply_dpd );
							original_price = tm_apply_dpd( original_rules[ $.epoAPI.math.toFloat( current_variation ) ], epoTotalsContainer, apply_dpd );

							element
								.find( '.tmcp-field, .tmcp-fee-field ' )
								.toArray()
								.forEach( function( el ) {
									el = $( el );
									if ( per_product_pricing ) {
										if ( el.attr( 'data-no-price' ) === '1' ) {
											price = 0;
											original_price = 0;
										}
										el.data( 'raw_price', price );
										el.data( 'raw_original_price', original_price );

										el.data( 'price', tm_set_tax_price( price, epoTotalsContainer, el ) );
										el.data( 'original_price', tm_set_tax_price( original_price, epoTotalsContainer, el ) );

										tm_update_price( {
											obj: el.closest( '.tmcp-field-wrap' ).find( '.tc-price' ),
											price: tm_get_price( price, epoTotalsContainer ),
											original_price: tm_get_price( original_price, epoTotalsContainer ),
											force: false,
											useFormattedPrice: false
										} );
									} else {
										el.data( 'price', 0 );
										el.data( 'original_price', 0 );
										el.closest( '.tmcp-field-wrap' ).find( '.amount' ).empty();
									}
								} );
						}
					} );
				epoContainer.data( 'tm_rules_init_done', 1 );
			}

			// skip specific field rules if per_product_pricing is false
			if ( ! per_product_pricing ) {
				return true;
			}

			product_price = tm_calculate_product_price( epoTotalsContainer );
			product_original_price = tm_calculate_product_regular_price( epoTotalsContainer );

			args = {
				cart: cart,
				current_variation: current_variation,
				bundleid: bundleid,
				epoTotalsContainer: epoTotalsContainer,
				product_price: product_price,
				product_original_price: product_original_price,
				apply_dpd: apply_dpd,
				per_product_pricing: per_product_pricing
			};

			all_fields = epoContainer.find( '.tmcp-field,.tmcp-sub-fee-field,.tmcp-fee-field' );
			if ( ! epoObject.is_associated ) {
				all_fields = all_fields.not( tcAPI.associatedEpoSelector + ' ' + '.tmcp-field,' + tcAPI.associatedEpoSelector + ' ' + '.tmcp-sub-fee-field,' + tcAPI.associatedEpoSelector + ' ' + '.tmcp-fee-field' );
			}
			active_fields = all_fields.filter( '.tcenabled' );

			// note: find a better way if any
			if ( ! $.tcepo.initialActivation[ epoEventId ] || ( active_fields.length === 0 && all_fields.length > 0 ) ) {
				all_fields.each( function() {
					field_is_active( $( this ) );
				} );

				$.tcepo.initialActivation[ epoEventId ] = true;
			}

			//  apply specific field rules
			all_fields.filter( '.tcenabled' ).each( function( index, element ) {
				tm_element_epo_rules( epoObject, element, args );
			} );

			all_fields.each( function( index, element ) {
				$( element ).on( 'tc_element_epo_rules', function() {
					tm_element_epo_rules( epoObject, element, args );
				} );
			} );
		} );
	}

	function add_late_fields_prices( epoObject, currentProductPrice, originalProductPrice, options_total, original_options_total, bid, _cart, applydpd ) {
		var total = 0;
		var originalTotal = 0;
		var totalFees = 0;
		var originalTotalFees = 0;
		var price;
		var originalPrice;
		var priceType;
		var setter;
		var id;
		var hidden;
		var bundleid;
		var realSetter;
		var productId;
		var epoId;
		var epoEventId = epoObject.epoEventId;
		var taxPrice;
		var taxOriginalPrice;
		var hiddenName;
		var productPrice;
		var oProductPrice;
		var apply_dpd;

		if ( applydpd !== undefined ) {
			apply_dpd = applydpd;
		} else {
			apply_dpd = epoObject.this_epo_totals_container.data( 'fields-price-rules' );
		}
		productPrice = currentProductPrice;
		oProductPrice = originalProductPrice;

		$.tcepo.lateFieldsPrices[ epoEventId ].forEach( function( field ) {
			price = field.price;
			originalPrice = field.original_price;
			priceType = field.pricetype;
			setter = field.setter;
			bundleid = field.bundleid;
			realSetter = setter;

			if ( priceType === 'percentcurrenttotal' ) {
				hiddenName = '_hidden';
			} else {
				hiddenName = '_hiddenfixed';
			}

			if ( setter.is( 'option' ) ) {
				realSetter = setter.closest( 'select' );
			}

			productPrice = parseFloat( $.epoAPI.applyFilter( 'tc_alter_product_price', productPrice, realSetter, _cart, epoObject.this_epo_totals_container, bid ) );
			oProductPrice = parseFloat( $.epoAPI.applyFilter( 'tc_alter_original_product_price', oProductPrice, realSetter, _cart, epoObject.this_epo_totals_container, bid ) );

			id = $.epoAPI.dom.id( realSetter.attr( 'name' ) );
			productId = $( '.tc-totals-form.tm-totals-form-' + _cart.attr( 'data-cart-id' ) ).attr( 'data-product-id' );
			epoId = $( '.tc-totals-form.tm-totals-form-' + _cart.attr( 'data-cart-id' ) ).attr( 'data-epo-id' );
			//workaround to support composite products
			hidden = $( '.tc-extra-product-options.tm-product-id-' + productId + "[data-epo-id='" + epoId + "']" ).find( '#' + id + hiddenName );

			if ( bundleid === bid ) {
				if ( priceType === 'percentcurrenttotal' ) {
					price = ( parseFloat( price ) / 100 ) * ( tm_apply_dpd( parseFloat( productPrice ), epoObject.this_epo_totals_container, apply_dpd ) + parseFloat( options_total ) );
					originalPrice = ( parseFloat( originalPrice ) / 100 ) * ( parseFloat( oProductPrice ) + parseFloat( original_options_total ) );
				} else if ( priceType === 'fixedcurrenttotal' ) {
					price = parseFloat( price ) + parseFloat( options_total );
					originalPrice = parseFloat( originalPrice ) + parseFloat( original_options_total );
				}
				if ( realSetter.data( 'tm-quantity' ) ) {
					price = price * parseFloat( realSetter.data( 'tm-quantity' ) );
					originalPrice = originalPrice * parseFloat( realSetter.data( 'tm-quantity' ) );
				}

				if ( setter.data( 'isset' ) === 1 && field_is_active( setter ) ) {
					if ( realSetter.is( '.tmcp-field' ) ) {
						total = total + price;
						originalTotal = originalTotal + originalPrice;
					} else if ( realSetter.is( '.tmcp-fee-field' ) ) {
						totalFees = totalFees + price;
						originalTotalFees = originalTotalFees + originalPrice;
					}
				}

				taxPrice = tm_set_tax_price( price, _cart, setter );
				taxOriginalPrice = tm_set_tax_price( originalPrice, _cart, setter );

				setter.data( 'price', taxPrice );
				setter.data( 'pricew', taxPrice );
				setter.data( 'original_price', taxOriginalPrice );
				setter.data( 'original_pricew', taxOriginalPrice );

				tm_update_price( {
					obj: setter.closest( '.tmcp-field-wrap' ).find( '.tc-price' ),
					price: tm_get_price( taxPrice, _cart, false, setter ),
					original_price: tm_get_price( taxOriginalPrice, _cart, false, setter ),
					force: false,
					useFormattedPrice: false
				} );

				if ( hidden.length === 0 ) {
					realSetter.before( '<input type="hidden" id="' + id + hiddenName + '" name="' + id + hiddenName + '" value="' + tm_set_price_without_tax( price, _cart ) + '">' );
				}
				if ( setter.is( '.tm-epo-field.tmcp-radio' ) || setter.is( '.tm-epo-field.tmcp-checkbox' ) ) {
					if ( setter.is( ':checked' ) ) {
						hidden.val( tm_set_price_without_tax( price, _cart ) );
					}
				} else {
					hidden.val( tm_set_price_without_tax( price, _cart ) );
				}
			} else if ( setter.data( 'pricew' ) !== undefined ) {
				// Prices are already taxed
				tm_update_price( {
					obj: setter.closest( '.tmcp-field-wrap' ).find( '.tc-price' ),
					price: setter.data( 'pricew' ),
					original_price: ( setter.data( 'original_pricew' ) !== undefined ? setter.data( 'original_pricew' ) : setter.data( 'pricew' ) ),
					force: false,
					useFormattedPrice: false
				} );
			}
		} );

		return {
			normal: [ total, originalTotal ],
			fees: [ totalFees, originalTotalFees ]
		};
	}

	function tm_lazyload() {
		var container;

		if ( TMEPOJS.tm_epo_no_lazy_load === 'yes' ) {
			return;
		}

		if ( tmLazyloadContainer ) {
			container = $( tmLazyloadContainer ).find( 'img.tmlazy' );
		} else {
			container = $( '.tc-extra-product-options img.tmlazy' );
		}

		container.lazyLoadXT();
		container.on( 'lazyshow', function() {
			jWindow.trigger( 'lazyLoadXToncomplete' );
		} );
	}

	function tm_css_styles( obj ) {
		var slider;
		var $cart;

		if ( ! obj ) {
			return;
		}

		$cart = $( '.cart' );

		obj.find( '.tm-owl-slider-section' ).each( function() {
			slider = $( this );

			slider.find( '.tc-slider-content' ).addClass( 'tm-owl-slider' );

			slider = slider.find( '.tm-owl-slider' );

			slider
				.addClass( 'tcowl-carousel-show' )
				.addClass( 'tcowl-carousel' )
				.on( 'changed.owl.carousel', function() {
					$cart.css( 'display', 'block' ).addClass( 'slider-setup' );
				} )
				.tmowlCarousel( Object.assign( {
					rtl: TMEPOJS.isRTL === '1',
					dots: false,
					nav: true,
					items: 1,
					autoHeight: true,
					mouseDrag: false,
					touchDrag: true,
					navText: [ TMEPOJS.i18n_prev_text, TMEPOJS.i18n_next_text ],
					navClass: [ 'owl-prev button', 'owl-next button' ],
					navElement: 'a',
					loop: false,
					navRewind: false
				}, window.tmowlCarouselSettings || {} ) );

			slider.removeClass( 'tcowl-carousel-show' );
		} );

		if ( $.fn.tcTabs ) {
			obj.find( '.tc-tabs' ).tcTabs( {
				headers: 'tc-tab-headers',
				header: 'tab-header',
				dataopenattribute: 'data-tab',
				sortabletabs: false
			} );
		}
	}

	function repeaterAdd( el, elementInnerWrap ) {
		var cpf = elementInnerWrap.closest( '.cpf-element' );
		var maxRows = cpf.data( 'repeater-max-rows' );
		var repeater = elementInnerWrap.find( '.tc-repeater-element' );
		var rows = repeater.length;
		var repeaterLast = repeater.last();
		var repeaterLastUL = repeater.last().find( '.tmcp-ul-wrap' );
		var clone;
		var cloneUl;
		var originalId = el.data( 'tc-repeater-id' );
		var index = el.data( 'tc-repeater-init' );

		if ( maxRows ) {
			if ( rows >= maxRows ) {
				return false;
			}
		}

		if ( rows + 1 >= maxRows && el.is( '.tc-repeater-add' ) ) {
			el.closest( '.tc-repeater-wrap' ).addClass( 'tc-hidden' );
		}

		if ( repeaterLastUL.is( '.tm-element-ul-date, .tm-element-ul-time' ) ) {
			repeaterLastUL.find( '.tm-epo-field' ).tm_datepicker( 'destroy' );
			repeaterLastUL.find( '.tmcp-date-select' ).off();
		}
		if ( repeaterLastUL.is( '.tm-element-ul-color' ) ) {
			repeaterLastUL.find( '.tm-epo-field' ).spectrum( 'destroy' );
		}

		clone = repeaterLast.tcClone( true );

		clone.find( '.tm-epo-field' ).attr( 'id', function( i, value ) {
			return value.replace( /\_\d+$/g, '' ) + '_' + index;
		} );
		clone.find( '[data-for]' ).attr( 'data-for', function( i, value ) {
			return value.replace( /\_\d+$/g, '' ) + '_' + index;
		} );
		clone.find( '[for]' ).attr( 'for', function( i, value ) {
			return value.replace( /\_\d+$/g, '' ) + '_' + index;
		} );
		clone.find( '[data-field-id]' ).attr( 'data-field-id', function( i, value ) {
			return value.replace( /\_\d+$/g, '' ) + '_' + index;
		} );
		clone.find( '[data-tm-date]' ).attr( 'data-tm-date', function() {
			return originalId + '_' + index;
		} );
		clone.find( '.tm-epo-field' ).attr( 'name', function( i, value ) {
			return value.replace( /\[\d+\]/g, '[' + index + ']' );
		} );
		clone.find( '.tm-qty' ).attr( 'name', function( i, value ) {
			return value.replace( /\[\d+\]/g, '[' + index + ']' );
		} );
		clone.find( '.tmcp-date-select' ).attr( 'name', function( i, value ) {
			return value.replace( /\[\d+\]/g, '[' + index + ']' );
		} );
		clone.find( '.tmcp-date-select' ).attr( 'id', function( i, value ) {
			return originalId + '_' + index + '_' + value.substring( value.lastIndexOf( '_' ) + 1 );
		} );

		cloneUl = clone.find( '.tmcp-ul-wrap' );

		if ( elementInnerWrap.find( '.tc-repeater-element' ).length > 0 ) {
			elementInnerWrap.find( '.tc-repeater-delete' ).removeClass( 'tc-hidden' );
			clone.find( '.tc-repeater-delete' ).removeClass( 'tc-hidden' );
		}

		if ( cloneUl.is( '.tm-element-ul-checkbox' ) ) {
			clone.find( 'li.tmcp-field-wrap' ).removeClass( 'tc-active' );
			clone
				.find( '.tm-epo-field.tmcp-checkbox' )
				.prop( 'checked', false );
			clone
				.find( '.tm-epo-field.tmcp-checkbox' )
				.filter( function( i, x ) {
					return $.inArray( $( x ).val(), el.data( 'repeater-init-value' ) ) !== -1;
				} )
				.prop( 'checked', true )
				.closest( 'li.tmcp-field-wrap' ).addClass( 'tc-active' );
		} else if ( cloneUl.is( '.tm-element-ul-radio' ) ) {
			clone.find( 'li.tmcp-field-wrap' ).removeClass( 'tc-active' );
			clone
				.find( '.tm-epo-field.tmcp-radio' )
				.filter( function( i, x ) {
					return $( x ).val() === el.data( 'repeater-init-value' );
				} )
				.prop( 'checked', true )
				.closest( 'li.tmcp-field-wrap' ).addClass( 'tc-active' );
		} else {
			clone.find( '.tm-epo-field' ).val( el.data( 'repeater-init-value' ) );
		}

		if ( ! cloneUl.is( '.tm-element-ul-checkbox' ) && ! cloneUl.is( '.tm-element-ul-radio' ) ) {
			clone.find( '.tm-epo-field' ).val( el.data( 'repeater-init-value' ) );
		}

		if ( cloneUl.is( '.tm-element-ul-upload' ) ) {
			clone.find( '.tc-upload-preview' ).remove();
			clone.find( '.tm-filename' ).remove();
		}

		clone.find( '.tc-element-qty' ).each( function( y, elc ) {
			var qelc = $( elc );
			var defaultValue = qelc.attr( 'data-default-value' );
			qelc.val( defaultValue ).trigger( 'change' );
		} );

		el.data( 'tc-repeater-init', el.data( 'tc-repeater-init' ) + 1 );

		return [ clone, repeaterLast, repeaterLastUL ];
	}

	function repeaterAddAfter( epoObject, clone, repeaterLast, repeaterLastUL, doevents ) {
		var cloneUl = clone.find( '.tmcp-ul-wrap' );

		if ( cloneUl.is( '.tm-element-ul-range' ) ) {
			clone.find( '.tm-range-picker' ).removeData( 'tc-picker-init' ).attr( 'class', 'tm-range-picker' ).html( '' );
			setRangePickers( clone );
		}
		if ( cloneUl.is( '.tm-element-ul-date, .tm-element-ul-time' ) ) {
			tm_set_datepicker( repeaterLastUL );
			tm_set_datepicker( cloneUl );
		}
		if ( cloneUl.is( '.tm-element-ul-color' ) ) {
			tm_set_color_pickers( repeaterLastUL );
			tm_set_color_pickers( cloneUl );
		}
		clone.find( '.tm-tooltip' ).removeData( 'tctooltip' ).removeData( 'tm-has-tm-tip' );
		$.tcToolTip( clone.find( '.tm-tooltip' ) );

		//  apply specific field rules
		clone.find( '.tm-epo-field' )
			.removeData( 'addedtcEpoBeforeOptionPriceCalculation' )
			.each( function( y, elc ) {
				var element = $( elc );
				field_is_active( element, false, true );
				if ( element.is( '.tcenabled' ) ) {
					tm_element_epo_rules( epoObject, element );
				}
				element.on( 'tc_element_epo_rules', function() {
					tm_element_epo_rules( epoObject, element );
				} );
			} );
		if ( ! doevents ) {
			return;
		}
		if ( cloneUl.is( '.tm-element-ul-checkbox' ) ) {
			clone
				.find( '.tm-epo-field.tmcp-checkbox' )
				.filter( ':checked' )
				.trigger( 'change.cpf' );
		} else if ( cloneUl.is( '.tm-element-ul-radio' ) ) {
			clone
				.find( '.tm-epo-field.tmcp-radio' )
				.filter( ':checked' )
				.trigger( 'change.cpf' );
		} else {
			clone.find( '.tm-epo-field' ).trigger( 'change.cpf' );
		}
	}

	function repeaterDelete( elementInnerWrap, repeaterElement, repeaterObj, $this ) {
		var cpf = elementInnerWrap.closest( '.cpf-element' );
		var minRows = cpf.data( 'repeater-min-rows' );
		var rows = elementInnerWrap.find( '.tc-repeater-element' ).length;
		var repeaterElementIndex = repeaterElement.index();
		var message;

		if ( minRows ) {
			if ( rows <= minRows ) {
				if ( $this ) {
					message = '<div class="tm-error-repeater tm-error tc-cell tcwidth tcwidth-100">' + $.tc_validator.messages.repeaterminrows( minRows ) + '</div>';
					$this.tcToolTip( { tip: message, onetime: true, trigger: true, tipclass: 'tc-error' } );
				}
				return false;
			}
		}
		if ( rows === 1 ) {
			return;
		}
		if ( rows === 2 ) {
			elementInnerWrap.find( '.tc-repeater-delete' ).addClass( 'tc-hidden' );
		}
		if ( repeaterObj.is( '.tc-repeater-add' ) ) {
			repeaterObj.closest( '.tc-repeater-wrap' ).removeClass( 'tc-hidden' );
		}
		repeaterElement.remove();
		repeaterObj.data( 'tc-repeater-init', repeaterObj.data( 'tc-repeater-init' ) - 1 );
		if ( repeaterElementIndex + 1 === rows ) {
			return;
		}
		elementInnerWrap.find( '.tc-repeater-element' )
			.toArray()
			.forEach( function( clone, index ) {
				clone = $( clone );
				clone.find( '.tm-epo-field' ).attr( 'id', function( i, value ) {
					return value.replace( /\_\d+$/g, '' ) + ( ( index !== 0 ) ? '_' + index : '' );
				} );
				clone.find( '[data-for]' ).attr( 'data-for', function( i, value ) {
					return value.replace( /\_\d+$/g, '' ) + ( ( index !== 0 ) ? '_' + index : '' );
				} );
				clone.find( '[for]' ).attr( 'for', function( i, value ) {
					return value.replace( /\_\d+$/g, '' ) + ( ( index !== 0 ) ? '_' + index : '' );
				} );
				clone.find( '[data-field-id]' ).attr( 'data-field-id', function( i, value ) {
					return value.replace( /\_\d+$/g, '' ) + ( ( index !== 0 ) ? '_' + index : '' );
				} );
				clone.find( '[data-tm-date]' ).attr( 'data-tm-date', function( i, value ) {
					return value.replace( /\_\d+$/g, '' ) + ( ( index !== 0 ) ? '_' + index : '' );
				} );
				clone.find( '.tm-epo-field' ).attr( 'name', function( i, value ) {
					return value.replace( /\[\d+\]/g, '[' + index + ']' );
				} );
				clone.find( '.tm-qty' ).attr( 'name', function( i, value ) {
					return value.replace( /\[\d+\]/g, '[' + index + ']' );
				} );
				clone.find( '.tmcp-date-select' ).attr( 'name', function( i, value ) {
					return value.replace( /\[\d+\]/g, '[' + index + ']' );
				} );
			} );
	}

	function tm_set_repeaters( obj, epoObject ) {
		var currentCart;
		var qtyElement;
		var quantityRepeaters;
		if ( ! obj ) {
			return;
		}
		obj = $( obj );

		if ( obj.length ) {
			currentCart = epoObject.main_cart;
			qtyElement = getQtyElement( currentCart );
			qtyElement = $.epoAPI.applyFilter( 'qtyElementForRepeaterQuantity', qtyElement, {
				epo: epoObject,
				currentCart: currentCart,
				obj: obj,
				qtyElement: qtyElement
			} );
			quantityRepeaters = obj.find( '.tc-repeater-quantity' );
			quantityRepeaters
				.toArray()
				.forEach( function( repeater, qx ) {
					var el = $( repeater );
					var tmEpoField;
					var repeaterElement;
					var length;

					if ( el.data( 'tc-repeater-init' ) ) {
						return;
					}
					repeaterElement = el.find( '.tc-repeater-element' );
					length = repeaterElement.length;
					tmEpoField = repeaterElement.first().find( '.tm-epo-field' );
					if ( tmEpoField.is( ':checkbox' ) ) {
						el.data( 'repeater-init-value',
							tmEpoField.filter( ':checked' ).toArray().map( function( x ) {
								return $( x ).val();
							} ) );
					} else if ( tmEpoField.is( ':radio' ) ) {
						el.data( 'repeater-init-value', tmEpoField.filter( ':checked' ).val() );
					} else {
						el.data( 'repeater-init-value', tmEpoField.val() );
					}
					el.data( 'tc-repeater-init', length );
					el.data( 'tc-repeater-id', tmEpoField.attr( 'id' ) );
					el.data( 'tc-repeater-name', tmEpoField.attr( 'name' ) );

					if ( qtyElement.length ) {
						qtyElement
							.off( 'change.r' + qx + 'cpf input.r' + qx + 'cpf' )
							.on( 'change.r' + qx + 'cpf input.r' + qx + 'cpf', function( event ) {
								var field = $( this );
								var prevValue = field.data( 'tm-prev-value' );
								var value = $.epoAPI.applyFilter( 'qtyElementForRepeaterQuantityValue', field.val(), {
									epo: epoObject,
									currentCart: currentCart,
									obj: obj,
									qtyElement: qtyElement,
									field: field
								} );
								var difference = value - prevValue;
								var elementInnerWrap;
								var thisRepeaterElement;
								var i;
								var ele;
								var cloned;
								var repeaterLast;
								var repeaterLastUL;
								var fieldprevval;
								if ( prevValue === undefined ) {
									prevValue = value - 1;
									difference = value - prevValue;
								}
								if ( event.isTrigger !== undefined ) {
									prevValue = el.find( '.tc-element-inner-wrap' ).find( '.tc-repeater-element' ).length;
									difference = value - prevValue;
								}
								if ( difference === 0 ) {
									return;
								}
								elementInnerWrap = el.find( '.tc-element-inner-wrap' );
								fieldprevval = $.epoAPI.applyFilter( 'qtyElementForRepeaterQuantity_tm-prev-value', field.val(), {
									epo: epoObject,
									currentCart: currentCart,
									obj: obj,
									qtyElement: qtyElement,
									field: field
								} );
								if ( fieldprevval <= 0 ) {
									fieldprevval = 1;
								}
								if ( qx === quantityRepeaters.length ) {
									field.data( 'tm-prev-value', fieldprevval );
								}
								if ( difference > 0 ) {
									thisRepeaterElement = elementInnerWrap.find( '.tc-repeater-element' ).eq( value - 1 );
									if ( thisRepeaterElement.length ) {
										return;
									}
									for ( i = 0; i < difference; i++ ) {
										cloned = repeaterAdd( el, elementInnerWrap );
										if ( cloned ) {
											if ( ele ) {
												ele = ele.add( cloned[ 0 ] );
											} else {
												ele = cloned[ 0 ];
											}
											if ( i === 0 ) {
												repeaterLast = cloned[ 1 ];
												repeaterLastUL = cloned[ 2 ];
											}
										}
									}
									if ( ele ) {
										ele.appendTo( elementInnerWrap.find( '.tc-element-container' ) );
										repeaterAddAfter( epoObject, ele, repeaterLast, repeaterLastUL );
									}
								} else {
									for ( i = value - difference; i > value; i -= 1 ) {
										thisRepeaterElement = elementInnerWrap.find( '.tc-repeater-element' ).eq( i - 1 );
										repeaterDelete( elementInnerWrap, thisRepeaterElement, el );
									}
								}
								currentCart.trigger( {
									type: 'tm-epo-update',
									norules: 2
								} );
							} );
						qtyElement.data( 'tm-prev-value', $.epoAPI.applyFilter( 'qtyElementForRepeaterQuantityValue', qtyElement.val(), {
							epo: epoObject,
							currentCart: currentCart,
							obj: obj,
							qtyElement: qtyElement,
							field: qtyElement
						} ) ).trigger( 'change' );
					}
				} );

			obj.find( '.tc-repeater-add' )
				.toArray()
				.forEach( function( repeater ) {
					var el = $( repeater );
					var tmEpoField;
					var elementInnerWrap;
					var repeaterElement;
					var length;
					if ( el.data( 'tc-repeater-init' ) ) {
						return;
					}
					elementInnerWrap = el.closest( '.tc-element-inner-wrap' );
					repeaterElement = elementInnerWrap.find( '.tc-repeater-element' );
					length = repeaterElement.length;
					tmEpoField = repeaterElement.first().find( '.tm-epo-field' );
					if ( elementInnerWrap.find( '.tc-repeater-element' ).length > 1 ) {
						elementInnerWrap.find( '.tc-repeater-delete' ).removeClass( 'tc-hidden' );
					}
					if ( tmEpoField.is( ':checkbox' ) ) {
						el.data( 'repeater-init-value',
							tmEpoField.filter( ':checked' ).toArray().map( function( x ) {
								return $( x ).val();
							} ) );
					} else if ( tmEpoField.is( ':radio' ) ) {
						el.data( 'repeater-init-value', tmEpoField.filter( ':checked' ).val() );
					} else {
						el.data( 'repeater-init-value', tmEpoField.val() );
					}
					el.data( 'tc-repeater-init', length );
					el.data( 'tc-repeater-id', tmEpoField.attr( 'id' ) );
					el.data( 'tc-repeater-name', tmEpoField.attr( 'name' ) );

					el.on( 'click.repeater', function() {
						var ele = repeaterAdd( el, elementInnerWrap );
						if ( ele ) {
							ele[ 0 ].appendTo( elementInnerWrap.find( '.tc-element-container' ) );
							repeaterAddAfter( epoObject, ele[ 0 ], ele[ 1 ], ele[ 2 ], true );
						}
					} );
				} );

			obj.find( '.tc-repeater-delete .delete' ).on( 'click', function() {
				var $this = $( this );
				var elementInnerWrap = $this.closest( '.tc-element-inner-wrap' );
				var repeaterElement = $this.closest( '.tc-repeater-element' );
				repeaterDelete( elementInnerWrap, repeaterElement, elementInnerWrap.find( '.tc-repeater-add' ), $this );
			} );
		}
	}

	function tm_set_color_pickers( obj ) {
		if ( ! obj ) {
			return;
		}
		obj = $( obj ).find( '.tm-color-picker' );
		if ( obj.length ) {
			obj.spectrum( {
				type: 'color',
				theme: 'epo',
				showButtons: true,
				allowEmpty: true,
				showInitial: true,
				showInput: true,
				clickoutFiresChange: false,
				chooseText: TMEPOJS.closeText,
				cancelText: TMEPOJS.i18n_cancel
			} );
			obj.spectrum( 'enable' );
		}
	}

	function tm_set_lightbox( obj ) {
		if ( ! obj ) {
			return;
		}
		if ( $( obj ).length ) {
			// document ready
			$( function() {
				$( obj ).tclightbox();
			} );
		}
	}

	function has_active_changes_product_image( field ) {
		var uic = field.closest( '.tmcp-field-wrap' ).find( 'label img' );
		var src = $( uic ).first().attr( 'data-original' );

		if ( field.is( 'select.tm-product-image' ) ) {
			field = field.children( 'option:selected' );
		}

		if ( ! src ) {
			src = $( uic ).first().attr( 'src' );
		}
		if ( ! src ) {
			src = field.attr( 'data-image' );
		}
		if ( field.attr( 'data-imagep' ) ) {
			src = field.attr( 'data-imagep' );
		}
		if ( src ) {
			return true;
		}

		return false;
	}

	function tm_set_upload_fields( epoObject ) {
		var field;
		var dT;
		var name;
		var file;
		var selector = epoObject.is_associated
			? epoObject.this_epo_container.find( '.tm-epo-field.tmcp-upload' )
			: epoObject.this_epo_container.find( '.tm-epo-field.tmcp-upload' ).not( tcAPI.epoSelector + ' ' + tcAPI.associatedEpoSelector + ' .tm-epo-field.tmcp-upload' );

		try {
			selector
				.not( tcAPI.epoSelector + ' ' + tcAPI.associatedEpoSelector + ' .tm-epo-field.tmcp-upload' )
				.toArray()
				.forEach( function( el ) {
					var dataFiles;
					var dataFilename;
					file = [];
					field = $( el );
					if ( field.attr( 'data-file' ) === '' ) {
						return false;
					}
					dataFiles = field.attr( 'data-file' ).split( '|' );
					if ( dataFiles.length === 0 ) {
						return false;
					}
					if ( ClipboardEvent || DataTransfer ) {
						dT = new ClipboardEvent( '' ).clipboardData || new DataTransfer();
					}
					dataFiles.forEach( function( dataFile ) {
						dataFilename = $.epoAPI.util.basename( dataFile );
						if ( ClipboardEvent || DataTransfer ) {
							dT.items.add( new File( [ dataFile ], dataFilename ) );
						}
						file.push( dataFile );
					} );
					if ( dT ) {
						el.files = dT.files;
					}
					file = file.join( '|' );
					name = field.attr( 'name' );
					field.trigger( 'tcupload' );
					field.after( '<input type="hidden" class="tmcp-upload-hidden" name="' + name + '" value="' + file + '">' );
					field.removeAttr( 'data-file' );
				} );
		} catch ( err ) {
			window.console.log( err );
			$( '.tm-epo-field.tmcp-upload' ).not( '.tm-multiple-file-upload' ).addClass( 'tc-nodt' );
			errorObject = err;
		}
	}

	function tm_set_upload_rules( epoObject ) {
		var epoEventId = epoObject.epoEventId;
		var this_epo_container = epoObject.this_epo_container;

		if ( TMEPOJS.tm_epo_upload_popup === 'yes' ) {
			$.tcepo.formSubmitEvents[ epoEventId ][ $.tcepo.formSubmitEvents[ epoEventId ].length ] = {
				trigger: function() {
					return true;
				},
				on_true: function() {
					var upload_fields = this_epo_container.data( 'num_uploads' );
					var thisPopup;
					var ajaxSuccessFunc;
					if ( upload_fields && Object.keys( upload_fields ).length ) {
						thisPopup = $.tcFloatBox( {
							fps: 1,
							ismodal: true,
							refresh: 'fixed',
							width: '50%',
							height: '300px',
							classname: 'flasho tc-wrapper',
							data: $.epoAPI.template.html( tcAPI.templateEngine.tc_upload_messages, {
								files: upload_fields,
								title: TMEPOJS.i18n_uploading_files,
								message: TMEPOJS.i18n_uploading_message
							} )
						} );
						ajaxSuccessFunc = function() {
							thisPopup.destroy();
							jDocument.off( 'ajaxSuccess', ajaxSuccessFunc );
						};
						jDocument.on( 'ajaxSuccess', ajaxSuccessFunc );
					}

					return true;
				},
				on_false: function() {
					return true;
				}
			};
		}
	}

	function tm_set_checkboxes_rules( epoObject ) {
		var this_epo_container = epoObject.this_epo_container;
		var main_product = epoObject.main_product;
		var epoEventId = epoObject.epoEventId;
		var limit_cont = this_epo_container.find( '.tm-limit' );
		var exactlimit_cont = this_epo_container.find( '.tm-exactlimit' );
		var minimumlimit_cont = this_epo_container.find( '.tm-minimumlimit' );

		// Limit checkbox selection
		this_epo_container.on( 'change.cpflimit', 'input.tm-epo-field.tmcp-checkbox', function() {
			var $this = $( this );
			tm_limit_c_selection( $this, true );
			tm_exact_c_selection( $this, true );
		} );
		if ( limit_cont.length ) {
			tm_check_limit_cont( limit_cont, main_product, epoEventId );
		}

		// Exact value checkbox check (Todo:check for isvisible)
		if ( exactlimit_cont.length ) {
			tm_check_exactlimit_cont( exactlimit_cont, main_product, epoEventId );
		}

		// Minimum number checkbox check (Todo:check for isvisible)
		if ( minimumlimit_cont.length ) {
			tm_check_minimumlimit_cont( minimumlimit_cont, epoEventId );
		}
	}

	function tm_theme_specific_actions( epoObject ) {
		var this_epo_container = epoObject.this_epo_container;
		var theme_name = TMEPOJS.theme_name;
		var all_epo_selects;
		var blaszok_selects;

		if ( theme_name ) {
			theme_name = theme_name.toLowerCase();
			all_epo_selects = this_epo_container.find( 'select' );

			switch ( theme_name ) {
				case 'flatsome':
				case 'flatsome-child':
				case 'flatsome child':
					all_epo_selects.wrap( '<div class="custom select-wrapper"/>' );
					break;

				case 'avada':
				case 'avada-child':
				case 'avada child':
					all_epo_selects.wrap( '<div class="avada-select-parent tm-select-parent"></div>' );
					$( '<div class="select-arrow">&#xe61f;</div>' ).appendTo( this_epo_container.find( '.tm-select-parent' ) );
					if ( window.calc_select_arrow_dimensions ) {
						window.calc_select_arrow_dimensions();
						jWindow.on( 'tmsectionpoplink cpflogicdone', function() {
							window.calc_select_arrow_dimensions();
						} );
					} else if ( window.calcSelectArrowDimensions ) {
						window.calcSelectArrowDimensions();
						jWindow.on( 'tmsectionpoplink cpflogicdone', function() {
							window.calcSelectArrowDimensions();
						} );
					}
					break;

				case 'bazar':
				case 'bazar-child':
				case 'bazar child':
					all_epo_selects.wrap( '<div class="tm-select-wrapper select-wrapper"/>' );
					break;

				case 'blaszok':
				case 'blaszok-child':
				case 'blaszok child':
					blaszok_selects = function() {
						setTimeout( function() {
							$( '.tm-extra-product-options select' )
								.not( '.hasCustomSelect' )
								.filter( ':visible' )
								.each( function() {
									if ( ! $( this ).is( '.mpcthSelect' ) ) {
										$( this ).width( $( this ).outerWidth() );
										$( this ).customSelect( { customClass: 'mpcthSelect' } );
									}
								} );
						}, 100 );
					};
					jWindow.on( 'cpflogicrun', function() {
						blaszok_selects();
					} );
					jWindow.on( 'epo_options_visible', function() {
						blaszok_selects();
					} );

					break;

				case 'handmade':
				case 'handmade child theme':
					$( '.tm-owl-slider.tcowl-carousel' ).addClass( 'manual' );
					break;
			}

			jWindow.trigger( 'tm-theme-specific-actions', {
				epo: {
					theme_name: theme_name,
					all_epo_selects: all_epo_selects
				}
			} );
		}

		// Fix added +/- quantity button on most themes.
		jDocument.off( 'click.cpf', '.quantity:not(.buttons_added) .minus, .quantity:not(.buttons_added) .plus' ).on( 'click.cpf', '.quantity:not(.buttons_added) .minus, .quantity:not(.buttons_added) .plus', function() {
			$( this ).closest( '.quantity' ).find( tcAPI.qtySelector ).trigger( 'change.cpf' );
		} );
	}

	function tm_custom_variations( epoObject, form, item_id, $main_product, $epo_holder ) {
		var epoEventId = epoObject.epoEventId;
		var variation_id_selector = "input[name^='variation_id']";
		var tm_epo_variation_section;
		var li_variations;
		var composite_load_test;
		var form_event;
		var type;
		var name;
		var selector;
		var func;
		var i;
		var eventName = epoObject.is_associated ? 'tc_variation_form.tmlogic' : 'wc_variation_form.tmlogic';
		var eventNamePrefix = epoObject.is_associated ? 'tc_' : '';
		var variationsForm = epoObject.variations_form;
		var variationsTable = epoObject.is_associated ? variationsForm.find( '.tc-epo-element-variations' ) : variationsForm.find( '.variations' );
		var resetSelector = epoObject.is_associated ? '.tc-epo-element-variable-reset-variations' : '.reset_variations';

		variationsForm.attr( 'data-epo_id', epoObject.epo_id );

		if ( form.find( 'input.variation_id' ).length > 0 ) {
			variation_id_selector = 'input.variation_id';
		}
		if ( $epo_holder.find( '.tm-epo-variation-element' ).length || $epo_holder.data( 'tm-epo-variation-element' ) ) {
			tm_epo_variation_section = $epo_holder.find( '.tm-epo-variation-section' ).first();
			tm_epo_variation_section.find( '.cpf-type-variations' ).attr( 'data-epo_id', epoObject.epo_id ).attr( 'data-product_id', variationsForm.attr( 'data-product_id' ) );

			$epo_holder.data( 'tm-epo-variation-element', tm_epo_variation_section.find( '.tm-epo-variation-element' ) );

			if ( item_id && item_id !== 'main' && ! epoObject.is_associated ) {
				// on composite

				variationsForm = epoObject.form;
				variationsTable = variationsForm.find( '.composite_component[data-item_id="' + item_id + '"]' ).find( '.variations' );
				variationsForm.attr( 'data-epo_id', epoObject.epo_id );

				if ( variationsTable.length === 0 ) {
					return;
				}

				li_variations = tm_epo_variation_section.closest( 'li.tm-extra-product-options-field' );
				if ( ! tm_epo_variation_section.is( '.tm-hidden' ) ) {
					variationsTable.hide();
				}

				variationsTable.after( tm_epo_variation_section.addClass( 'tm-extra-product-options nopadding' ) );
				if ( li_variations.is( ':empty' ) ) {
					li_variations.hide();
				}

				if ( ! tm_epo_variation_section.is( '.section_popup' ) ) {
					tm_epo_variation_section.removeClass( 'tc-cell' );
					tm_epo_variation_section.wrap( "<div class='tc-styled-variations'></div>" );
				} else {
					tm_epo_variation_section.wrap( "<div class='tc-styled-variations tc-row'></div>" );
				}

				composite_load_test = false;
				form.off( eventName ).on( eventName, function() {
					composite_load_test = true;
					variationsForm.on( 'click.tmlogic', '.reset_variations', function() {
						tm_epo_variation_section.find( 'select.tm-epo-variation-element' ).val( '' ).children( 'option' ).prop( 'disabled', false ).show();
						tm_epo_variation_section.find( '.tm-epo-variation-element' ).prop( 'disabled', false ).removeClass( 'tm-disabled' ).prop( 'checked', false ).closest( 'li' ).show();
						jWindow.trigger( 'tmlazy' );
						tm_epo_variation_section.find( '.tm-epo-variation-element' ).trigger( 'tm_trigger_product_image' );
						tm_epo_variation_section.find( 'li' ).removeClass( 'tc-active tm-attribute-disabled' ).css( 'opacity', '' );
					} );

					// Disable option fields that are unavaiable for current set of attributes
					form.off( 'woocommerce_update_variation_values_tmlogic' ).on( 'woocommerce_update_variation_values_tmlogic', function() {
						tm_custom_variations_update( form, epoObject );
					} );
					for ( i = 0; i < lateVariationEvent.length; i += 1 ) {
						form_event = lateVariationEvent[ i ];
						type = typeof form_event;
						if ( type === 'object' ) {
							name = typeof form_event.name === 'string' || false;
							selector = typeof form_event.selector === 'string' || false;
							func = typeof form_event.func === 'function' || false;
							if ( name && func ) {
								if ( selector === "input[name='variation_id']" ) {
									selector = variation_id_selector;
								}
								if ( form_event.selector ) {
									form.data( 'tm-styled-variations', 1 )
										.off( eventNamePrefix + form_event.name + i, selector )
										.on( eventNamePrefix + form_event.name + i, selector, form_event.func );
								} else {
									form.data( 'tm-styled-variations', 1 )
										.off( eventNamePrefix + form_event.name + i )
										.on( eventNamePrefix + form_event.name + i, form_event.func );
								}
							}
						}
					}
					lateVariationEvent = [];
					tm_epo_variation_section.find( '.tm-epo-variation-element:not(.tm-hidden .tm-epo-variation-element)' ).last().trigger( 'tm_epo_variation_element_change' );
				} );
				// document ready
				$( function() {
					if ( composite_load_test === false ) {
						form.trigger( eventName );
					}
				} );
			} else {
				if ( tm_epo_variation_section.length ) {
					if ( ! tm_epo_variation_section.is( '.tm-hidden' ) ) {
						variationsTable.hide();
					}

					li_variations = tm_epo_variation_section.closest( 'li.tm-extra-product-options-field' );

					variationsTable.after( tm_epo_variation_section.addClass( 'tm-extra-product-options nopadding' ) );
					if ( li_variations.is( ':empty' ) ) {
						li_variations.hide();
					}

					if ( ! tm_epo_variation_section.is( '.section_popup' ) ) {
						tm_epo_variation_section.removeClass( 'tc-cell' );
						tm_epo_variation_section.wrap( "<div class='tc-styled-variations'></div>" );
					} else {
						tm_epo_variation_section.wrap( "<div class='tc-styled-variations tc-row'></div>" );
					}

					variationsForm.off( 'click.tmlogic', resetSelector ).on( 'click.tmlogic', resetSelector, function() {
						tm_epo_variation_section.find( 'select.tm-epo-variation-element' ).val( '' ).children( 'option' ).prop( 'disabled', false ).show();
						tm_epo_variation_section.find( '.tm-epo-variation-element' ).prop( 'disabled', false ).removeClass( 'tm-disabled' ).prop( 'checked', false ).closest( 'li' ).show();
						jWindow.trigger( 'tmlazy' );
						tm_epo_variation_section.find( '.tm-epo-variation-element' ).trigger( 'tm_trigger_product_image' );
						tm_epo_variation_section.find( 'li' ).removeClass( 'tc-active tm-attribute-disabled' ).css( 'opacity', '' );
					} );
				}

				// Disable option fields that are unavaiable for current set of attributes
				variationsForm.off( 'woocommerce_update_variation_values_tmlogic' ).on( 'woocommerce_update_variation_values_tmlogic', function() {
					tm_custom_variations_update( variationsForm, epoObject );
				} );

				for ( i = 0; i < lateVariationEvent.length; i += 1 ) {
					form_event = lateVariationEvent[ i ];
					type = typeof form_event;
					if ( type === 'object' ) {
						name = typeof form_event.name === 'string' || false;
						selector = typeof form_event.selector === 'string' || false;
						func = typeof form_event.func === 'function' || false;
						if ( name && func ) {
							if ( selector === "input[name='variation_id']" ) {
								selector = variation_id_selector;
							}
							if ( form_event.selector ) {
								variationsForm
									.data( 'tm-styled-variations', 1 )
									.off( eventNamePrefix + form_event.name + i, selector )
									.on( eventNamePrefix + form_event.name + i, selector, form_event.func );
							} else {
								variationsForm
									.data( 'tm-styled-variations', 1 )
									.off( eventNamePrefix + form_event.name + i )
									.on( eventNamePrefix + form_event.name + i, form_event.func );
							}
						}
					}
				}
				lateVariationEvent = [];
				tm_epo_variation_section.find( '.tm-epo-variation-element:not(.tm-hidden .tm-epo-variation-element)' ).last().trigger( 'tm_epo_variation_element_change' );
			}

			// global event for custom variations
			$.tcepo.formSubmitEvents[ epoEventId ][ $.tcepo.formSubmitEvents[ epoEventId ].length ] = {
				trigger: function() {
					return true;
				},
				on_true: function() {
					tm_epo_variation_section.find( '.tm-epo-variation-element' ).attr( 'disabled', 'disabled' );
					return true;
				},
				on_false: function() {
					tm_epo_variation_section.find( '.tm-epo-variation-element' ).prop( 'disabled', false );
				}
			};

			$( document.body ).on( 'added_to_cart', function() {
				tm_epo_variation_section.find( '.tm-epo-variation-element' ).prop( 'disabled', false );
			} );
		}
	}

	function repopulate_backup_image_atts( img, product_element ) {
		var $gallery_img = product_element.find( '.flex-control-nav li:eq(0) img' );
		var $gallery_wrapper = product_element.find( '.woocommerce-product-gallery__wrapper ' );
		var $product_img_wrap = $gallery_wrapper.find( '.woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder' ).eq( 0 );
		var $product_img = img;
		var $product_link = img.closest( 'a' );

		$product_img.attr( 'data-o_' + 'src', $.tc_product_image_store[ 0 ].src );
		$product_img.attr( 'data-o_' + 'srcset', $.tc_product_image_store[ 0 ].srcset );
		$product_img.attr( 'data-o_' + 'sizes', $.tc_product_image_store[ 0 ].sizes );
		$product_img.attr( 'data-o_' + 'title', $.tc_product_image_store[ 0 ].title );
		$product_img.attr( 'data-o_' + 'alt', $.tc_product_image_store[ 0 ].alt );
		$product_img.attr( 'data-o_' + 'data-src', $.tc_product_image_store[ 0 ][ 'data-src' ] );
		$product_img.attr( 'data-o_' + 'data-large_image', $.tc_product_image_store[ 0 ][ 'data-large_image' ] );
		$product_img.attr( 'data-o_' + 'data-large_image_width', $.tc_product_image_store[ 0 ][ 'data-large_image_width' ] );
		$product_img.attr( 'data-o_' + 'data-large_image_height', $.tc_product_image_store[ 0 ][ 'data-large_image_height' ] );
		$product_img_wrap.attr( 'data-o_' + 'data-thumb', $.tc_product_image_store[ 1 ][ 'data-thumb' ] );
		if ( $.tc_product_image_store[ 2 ] ) {
			$gallery_img.attr( 'data-o_' + 'src', $.tc_product_image_store[ 2 ].src );
		}

		$product_link.attr( 'data-o_' + 'href', $.tc_product_image_store[ 3 ].href );
		$product_link.attr( 'data-o_' + 'title', $.tc_product_image_store[ 3 ].title );
	}

	function reset_saved_image( img, product_element ) {
		var $gallery_img = product_element.find( '.flex-control-nav li:eq(0) img' );
		var $gallery_wrapper = product_element.find( '.woocommerce-product-gallery__wrapper ' );
		var $product_img_wrap = $gallery_wrapper.find( '.woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder' ).eq( 0 );
		var $product_img = img;
		var $product_link = img.closest( 'a' );

		// backup current product image attributes
		if ( ! $.isEmptyObject( $.tc_product_image ) ) {
			$.tc_product_image_store = $.tc_pre_populate_store();
			$.tc_product_image_store = $.tc_maybe_copy_object_values( $.tc_product_image_store, $.tc_product_image );
		} else {
			$.tc_product_image_store = $.tc_populate_store( img, product_element );
		}

		$product_img.tc_update_attr( 'src', 0 );
		$product_img.tc_update_attr( 'srcset', 0 );
		$product_img.tc_update_attr( 'sizes', 0 );
		$product_img.tc_update_attr( 'title', 0 );
		$product_img.tc_update_attr( 'alt', 0 );
		$product_img.tc_update_attr( 'data-src', 0 );
		$product_img.tc_update_attr( 'data-large_image', 0 );
		$product_img.tc_update_attr( 'data-large_image_width', 0 );
		$product_img.tc_update_attr( 'data-large_image_height', 0 );
		$product_img_wrap.tc_update_attr( 'data-thumb', 1 );
		$gallery_img.tc_update_attr( 'src', 2 );

		$product_link.tc_update_attr( 'href', 3 );
		$product_link.tc_update_attr( 'title', 3 );
	}

	function image_update( data, img, product_element ) {
		var $gallery_img = product_element.find( '.flex-control-nav li:eq(0) img' );
		var $gallery_wrapper = product_element.find( '.woocommerce-product-gallery__wrapper ' );
		var $product_img_wrap = $gallery_wrapper.find( '.woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder' ).eq( 0 );
		var $product_img = img;
		var $product_link = img.closest( 'a' );
		var $img_zoom = $product_link.next( '.zoomImg' );

		if ( data && data.image_link && data.image_link && data.image_link.length > 1 ) {
			if ( data.full_src === null || data.full_src === '' ) {
				data.full_src = data.image_link;
			}
			if ( data.full_src_w === null || data.full_src_w === '' ) {
				data.full_src_w = $product_img.attr( 'data-large_image_width' );
			}
			if ( data.full_src_h === null || data.full_src_h === '' ) {
				data.full_src_h = $product_img.attr( 'data-large_image_height' );
			}
			if ( $product_img.length ) {
				if ( ! data.image_srcset ) {
					data.image_sizes = false;
				}
				if ( ! data.image_sizes ) {
					data.image_srcset = false;
				}
				$product_img.tc_set_attr( 'src', data.image_link, 0 );
				$product_img.tc_set_attr( 'srcset', data.image_srcset, 0 );
				$product_img.tc_set_attr( 'sizes', data.image_sizes, 0 );
				$product_img.tc_set_attr( 'title', data.image_title, 0 );
				$product_img.tc_set_attr( 'alt', data.image_alt, 0 );
				$product_img.tc_set_attr( 'data-src', data.full_src, 0 );
				$product_img.tc_set_attr( 'data-large_image', data.full_src, 0 );
				$product_img.tc_set_attr( 'data-large_image_width', data.full_src_w, 0 );
				$product_img.tc_set_attr( 'data-large_image_height', data.full_src_h, 0 );
				$product_img_wrap.tc_set_attr( 'data-thumb', data.image_link, 1 );
			}
			if ( $gallery_img.length ) {
				$gallery_img.tc_set_attr( 'src', data.image_link, 2 );
			}
			if ( $product_link.length ) {
				$product_link.tc_set_attr( 'href', data.full_src, 3 );
				$product_link.tc_set_attr( 'title', data.image_caption, 3 );
			}
			if ( $img_zoom.length ) {
				$img_zoom.tc_set_attr( 'src', data.full_src, 4 );
			}
		} else {
			if ( $product_img.length ) {
				$product_img.tc_reset_attr( 'src', 0 );
				$product_img.tc_reset_attr( 'srcset', 0 );
				$product_img.tc_reset_attr( 'sizes', 0 );
				$product_img.tc_reset_attr( 'title', 0 );
				$product_img.tc_reset_attr( 'alt', 0 );
				$product_img.tc_reset_attr( 'data-src', 0 );
				$product_img.tc_reset_attr( 'data-large_image', 0 );
				$product_img.tc_reset_attr( 'data-large_image_width', 0 );
				$product_img.tc_reset_attr( 'data-large_image_height', 0 );
				$product_img_wrap.tc_reset_attr( 'data-thumb', 1 );
			}
			if ( $gallery_img.length ) {
				$gallery_img.tc_reset_attr( 'src', 2 );
			}
			if ( $product_link.length ) {
				$product_link.tc_reset_attr( 'href', 3 );
				$product_link.tc_reset_attr( 'title', 3 );
			}
			if ( $img_zoom.length ) {
				$img_zoom.tc_reset_attr( 'src', 4 );
			}
		}
	}

	function get_main_product_image( epoObject, product_element ) {
		var img;

		if ( epoObject.is_associated ) {
			img = product_element.find( '.tc-product-image .wp-post-image' ).first();
		} else if ( TMEPOJS.tm_epo_global_product_image_selector !== '' ) {
			img = $( TMEPOJS.tm_epo_global_product_image_selector );
		} else {
			img = product_element.find( '.woocommerce-product-gallery__image:not(.clone), .woocommerce-product-gallery__image--placeholder:not(.clone)' ).eq( 0 ).find( '.wp-post-image' ).first();
			if ( img.length === 0 ) {
				img = product_element.find( 'a.woocommerce-main-image img, img.woocommerce-main-image,a img' ).not( '.thumbnails img,.product_list_widget img' ).first();
			}
			if ( img.length === 0 ) {
				img = product_element.find( 'img.wp-post-image' ).not( '.thumbnails img,.product_list_widget img' ).first();
			}
			if ( img.length === 0 ) {
				img = product_element.find( '.elementor-widget-wd_single_product_gallery img.wp-post-image' ).first();
			}
			if ( img.length === 0 ) {
				img = product_element.find( '.elementor-widget-ae-post-image .wp-post-image' ).first();
			}
			if ( img.length === 0 ) {
				img = $( '.woocommerce div.product div.images' ).not( '.thumbnails img,.product_list_widget img' ).first();
			}
		}

		if ( $( img ).length > 1 ) {
			img = $( img ).first();
		}

		return img;
	}

	function get_product_element( epoObject ) {
		var main_product;
		var product_id;
		var product_element;

		if ( epoObject.is_associated ) {
			return epoObject.main_product.closest( '.tc-epo-element-product-container' );
		}

		main_product = epoObject.main_product;
		product_id = epoObject.product_id;
		product_element = main_product.closest( '#product-' + product_id );

		if ( product_element.length <= 0 ) {
			product_element = main_product.closest( '.post-' + product_id );
		}

		return product_element;
	}

	function gallery_compatibility_actions( gallery_type, clone_image, preload_img, visible, event_data, $form, product_element ) {
		var gallery;
		var _elements;
		var ge;
		var galleryWidth;
		var zoomEnabled;
		var image;
		var zoom_options;

		for ( gallery in gallery_type ) {
			if ( Object.prototype.hasOwnProperty.call( gallery_type, gallery ) ) {
				gallery = gallery_type[ gallery ];

				if ( gallery.enabled ) {
					switch ( gallery.type ) {
						case 'yith':
							if ( ! clone_image ) {
								if ( ! visible ) {
									gallery.yith_wcmg_zoom.attr( 'href', gallery._yith_wcmg_default_zoom );
								} else {
									gallery.yith_wcmg_zoom.attr( 'href', gallery.yith_wcmg_default_zoom );
								}
								if ( gallery.element.data( 'yith_magnifier' ) ) {
									gallery.element.yith_magnifier( 'destroy' );
								}

								gallery.element.yith_magnifier( window.yith_magnifier_options );
							} else {
								clone_image.attr( 'srcset', preload_img ).attr( 'src-orig', preload_img );

								if ( gallery.element.data( 'yith_magnifier' ) ) {
									gallery.element.yith_magnifier( 'destroy' );
								}
								_elements = {
									elements: {
										zoom: $( '.yith_magnifier_zoom' ),
										zoomImage: clone_image,
										gallery: $( '.yith_magnifier_gallery li a' )
									}
								};

								gallery.element.yith_magnifier( $.extend( true, {}, window.yith_magnifier_options, _elements ) );
							}
							break;
						case 'iosslider':
							setTimeout(
								function( g ) {
									g.element.iosSlider( 'update' );
								}.bind( null, gallery ),
								150
							);
							break;
						case 'flexslider':
							jWindow.trigger( 'resize' );
							break;
						case 'elevatezoom':
							gallery.element.each(
								function( g, j ) {
									var elevateZoom = j( this ).data( 'elevateZoom' );
									if ( typeof elevateZoom !== 'undefined' ) {
										elevateZoom.swaptheimage( g, g );
									}
								}.bind( null, preload_img, $ )
							);
							break;
						case 'easyzoom':
							gallery.element.swap( null, preload_img );
							break;
						case 'easyzoom-flatsome':
							gallery.element.swap( preload_img, preload_img );
							break;
						case 'woocommerce':
							if ( clone_image ) {
								gallery.element.flexslider( 0 );
								gallery.element.trigger( 'woocommerce_gallery_reset_slide_position' );
								ge = gallery.element;
								window.setTimeout(
									function( g, w ) {
										g.trigger( 'woocommerce_gallery_init_zoom' );
										w.trigger( 'resize' );
									}.bind( null, ge, jWindow ),
									10
								);
							}
							break;
						case 'zoom':
							if ( product_element ) {
								galleryWidth = product_element.find( '.woocommerce-product-gallery--with-images' ).width();
								zoomEnabled = false;

								image = gallery.element.find( 'img.wp-post-image' );
								if ( image.attr( 'data-large_image_width' ) > galleryWidth ) {
									zoomEnabled = true;
								}

								if ( zoomEnabled ) {
									zoom_options = {
										touch: false
									};
									if ( 'ontouchstart' in window ) {
										zoom_options.on = 'click';
									}

									gallery.element.trigger( 'zoom.destroy' );
									gallery.element.zoom( zoom_options );
								} else {
									gallery.element.trigger( 'zoom.destroy' );
								}
							}
							break;
					}
				}
			}
		}

		jWindow.trigger( 'tm_gallery_compatibility_actions', {
			event_data: event_data,
			product_element: product_element,
			form: $form
		} );
	}

	function get_gallery_type( epoObject, img, product_element ) {
		// YITH WooCommerce Zoom Magnifier
		var is_yith_wcmg;
		var yith_wcmg;
		var yith_wcmg_zoom;
		var yith_wcmg_default_zoom;
		var _yith_wcmg_default_zoom;
		var yith_wcmg_default_image;

		// iosslider - Touch Enabled, Responsive jQuery Horizontal Content
		// Slider/Carousel/Image Gallery Plugin
		var is_iosSlider;
		var is_iosSlider_element;

		// ThemeFusion flexslider
		var is_flexslider;
		var is_flexslider_element;

		// elevateZoom A Jquery Image Zoom Plugin
		var is_elevateZoom;
		var is_elevateZoom_obj;

		// EasyZoom jQuery image zoom plugin
		var is_easyzoom;
		var is_easyzoom_element;

		// new flatsome easyzoom
		var is_easyzoom_flatsome;
		var is_easyzoom_flatsome_element;

		// WooCommerce 2.7x gallery
		var is_wc27_gallery;
		var is_wc27_gallery_element;
		var wc27_zoom_target;
		var wc_single_product_params;
		var zoom_target_temp;

		// fn.zoom
		var is_zoom_enabled;
		var zoom_images;
		var gallery;

		if ( epoObject.is_associated ) {
			return {};
		}

		// YITH WooCommerce Zoom Magnifier
		is_yith_wcmg = false;
		yith_wcmg = $( '.images' );
		yith_wcmg_zoom = $( '.yith_magnifier_zoom' );
		yith_wcmg_default_zoom = yith_wcmg.find( '.yith_magnifier_zoom' ).first().attr( 'href' );
		_yith_wcmg_default_zoom = yith_wcmg_default_zoom;
		yith_wcmg_default_image = yith_wcmg.find( '.yith_magnifier_zoom img' ).first().attr( 'src' );

		// iosslider - Touch Enabled, Responsive jQuery Horizontal Content
		// Slider/Carousel/Image Gallery Plugin
		is_iosSlider = false;
		is_iosSlider_element = $( '.iosSlider.product-gallery-slider,.iosSlider.product-slider' );

		// ThemeFusion flexslider
		is_flexslider = false;
		is_flexslider_element = product_element.find( '.images .fusion-flexslider' );

		// elevateZoom A Jquery Image Zoom Plugin
		is_elevateZoom = img.data( 'elevateZoom' ) || false;
		is_elevateZoom_obj = product_element.find( 'div.product-images .woocommerce-main-image' );

		// EasyZoom jQuery image zoom plugin
		is_easyzoom = false;
		is_easyzoom_element = product_element.find( '.images .easyzoom' );

		// new flatsome easyzoom
		is_easyzoom_flatsome = false;
		is_easyzoom_flatsome_element = product_element.find( '.images .easyzoom' );

		// WooCommerce 2.7x gallery
		is_wc27_gallery = false;
		is_wc27_gallery_element = product_element.find( '.woocommerce-product-gallery' );
		wc27_zoom_target = false;
		wc_single_product_params = window.wc_single_product_params;

		// fn.zoom
		is_zoom_enabled = typeof $.fn.zoom === 'function' && wc_single_product_params && wc_single_product_params.zoom_enabled;
		zoom_images = false;

		if ( window.yith_magnifier_options && yith_wcmg.data( 'yith_magnifier' ) ) {
			is_yith_wcmg = true;
		}

		if ( is_iosSlider_element.length && is_iosSlider_element.iosSlider ) {
			is_iosSlider = true;
		}

		if ( is_flexslider_element.length && is_flexslider_element.flexslider ) {
			is_flexslider = true;
		}

		if ( is_easyzoom_element.length && is_easyzoom_element.filter( '.images .easyzoom.first' ).data( 'easyZoom' ) ) {
			is_easyzoom_element = is_easyzoom_element.filter( '.images .easyzoom.first' ).data( 'easyZoom' );
			is_easyzoom = true;
		}

		if ( ! is_easyzoom ) {
			is_easyzoom_flatsome_element = product_element.find( '.images .has-image-zoom .slide' );
			if ( is_easyzoom_flatsome_element.length && is_easyzoom_flatsome_element.filter( '.images .has-image-zoom .slide.first' ).data( 'easyZoom' ) ) {
				is_easyzoom_flatsome_element = is_easyzoom_flatsome_element.filter( '.images .has-image-zoom .slide.first' ).data( 'easyZoom' );
				is_easyzoom_flatsome = true;
			}
		}

		if ( document.readyState === 'complete' ) {
			setTimeout( function() {
				if ( is_easyzoom_element.length && is_easyzoom_element.data( 'easyZoom' ) ) {
					is_easyzoom_element = is_easyzoom_element.data( 'easyZoom' );
					is_easyzoom = true;
				}
				if ( is_easyzoom_flatsome_element.length && is_easyzoom_flatsome_element.data( 'easyZoom' ) ) {
					is_easyzoom_flatsome_element = is_easyzoom_flatsome_element.data( 'easyZoom' );
					is_easyzoom_flatsome = true;
				}
			}, 150 );
		} else {
			jWindow.on( 'load', function() {
				setTimeout( function() {
					if ( is_easyzoom_element.length && is_easyzoom_element.data( 'easyZoom' ) ) {
						is_easyzoom_element = is_easyzoom_element.data( 'easyZoom' );
						is_easyzoom = true;
					}
					if ( is_easyzoom_flatsome_element.length && is_easyzoom_flatsome_element.data( 'easyZoom' ) ) {
						is_easyzoom_flatsome_element = is_easyzoom_flatsome_element.data( 'easyZoom' );
						is_easyzoom_flatsome = true;
					}
				}, 150 );
			} );
		}

		if ( is_wc27_gallery_element.length && is_wc27_gallery_element.data( 'flexslider' ) ) {
			is_wc27_gallery = true;

			if ( typeof $.fn.zoom === 'function' && wc_single_product_params && wc_single_product_params.zoom_enabled ) {
				zoom_target_temp = img.closest( '.woocommerce-product-gallery__image' );

				if ( zoom_target_temp.length > 0 && img.width() > $( '.woocommerce-product-gallery' ).width() ) {
					wc27_zoom_target = zoom_target_temp;
					img.data.wc27_zoom_target = wc27_zoom_target;
				}
			}
		}

		if ( ! is_wc27_gallery && is_zoom_enabled ) {
			zoom_images = product_element.find( '.woocommerce-product-gallery__image' );
		}

		gallery = {
			is_yith_wcmg: {
				type: 'yith',
				enabled: is_yith_wcmg,
				element: yith_wcmg,
				yith_wcmg_zoom: yith_wcmg_zoom,
				_yith_wcmg_default_zoom: _yith_wcmg_default_zoom,
				yith_wcmg_default_image: yith_wcmg_default_image
			},
			is_iosSlider: {
				type: 'iosslider',
				enabled: is_iosSlider,
				element: is_iosSlider_element
			},
			is_flexslider: {
				type: 'flexslider',
				enabled: is_flexslider,
				element: is_flexslider_element
			},
			is_elevateZoom: {
				type: 'elevatezoom',
				enabled: is_elevateZoom,
				element: is_elevateZoom_obj
			},
			is_easyzoom: {
				type: 'easyzoom',
				enabled: is_easyzoom,
				element: is_easyzoom_element
			},
			is_easyzoom_flatsome: {
				type: 'easyzoom-flatsome',
				enabled: is_easyzoom_flatsome,
				element: is_easyzoom_flatsome_element
			},
			is_wc27_gallery: {
				type: 'woocommerce',
				enabled: is_wc27_gallery,
				element: is_wc27_gallery_element
			},
			is_zoom_enabled: {
				type: 'zoom',
				enabled: ! is_wc27_gallery && is_zoom_enabled,
				element: zoom_images
			}
		};

		return gallery;
	}

	function tm_product_image_self( epoObject ) {
		var this_epo_container = epoObject.is_associated ? epoObject.this_epo_container : epoObject.this_epo_container.not( tcAPI.epoSelector + ' ' + tcAPI.associatedEpoSelector );
		var variationsForm = epoObject.variations_form;
		var main_product = epoObject.main_product;
		var $form = epoObject.form;
		var img;
		var gallery_type;
		var product_element = get_product_element( epoObject );
		var last_active_field = [];
		var t;
		var el;
		var el_current;
		var imp;
		var selector;
		var eventNamePrefix = epoObject.is_associated ? 'tc_' : '';
		// This currently has a limitation that it doesn't work if there is variation active onload.
		var main_tc_product_image_store;

		img = get_main_product_image( epoObject, product_element );
		gallery_type = get_gallery_type( epoObject, img, product_element );

		if ( $( img ).length > 0 ) {
			$form.on( eventNamePrefix + 'reset_image.tcpi', function() {
				setTimeout( function() {
					if ( TMEPOJS.tm_epo_global_image_recalculate === 'yes' ) {
						img = get_main_product_image( epoObject, product_element );
						$.tc_product_image_store = main_tc_product_image_store;
					}
					// restore product image atts from backup
					$.tc_product_image = $.tc_replace_object_values( $.tc_product_image, $.tc_product_image_store );

					last_active_field = [];

					$( this_epo_container.find( '.tm-product-image:checked,select.tm-product-image' ) )
						.add( variationsForm.find( '.tm-epo-variation-section' ).first().find( '.tm-product-image:checked,select.tm-product-image' ) )
						.each( function() {
							t = $( this );
							if ( field_is_active( t ) && t.val() !== '' ) {
								last_active_field.push( t );
							}
						} );
					if ( last_active_field.length ) {
						last_active_field[ last_active_field.length - 1 ].trigger( 'tm_trigger_product_image' );
					} else {
						$.tc_product_image_store = $.tc_populate_store( img, product_element );
					}
				}, window.tc_epo_product_image_change_delay || 0 );
			} );

			$form.on( eventNamePrefix + 'found_variation.tcpi', function() {
				setTimeout( function() {
					if ( TMEPOJS.tm_epo_global_image_recalculate === 'yes' ) {
						img = get_main_product_image( epoObject, product_element );
					}
					reset_saved_image( img, product_element );

					last_active_field = [];
					$( this_epo_container.find( '.tm-product-image:checked,select.tm-product-image' ) )
						.add( variationsForm.find( '.tm-epo-variation-section' ).first().find( '.tm-product-image:checked,select.tm-product-image' ) )
						.each( function() {
							t = $( this );
							if ( field_is_active( t ) && t.val() !== '' ) {
								last_active_field.push( t );
							}
						} );
					if ( last_active_field.length ) {
						repopulate_backup_image_atts( img, product_element );
						last_active_field[ last_active_field.length - 1 ].trigger( 'tm_trigger_product_image' );
					}
				}, window.tc_epo_product_image_change_delay || 0 );
			} );

			$.tc_product_image_store = $.tc_maybe_copy_object_values_from_img( $.tc_product_image_store, img, product_element );
			main_tc_product_image_store = $.epoAPI.util.deepCopyArray( $.tc_product_image_store );

			main_product.off( 'tm_change_product_image' ).on( 'tm_change_product_image', function( evt, event_data ) {
				var data;
				evt.stopImmediatePropagation();

				el = event_data.element;
				el_current = event_data.element_current;
				if ( el && el_current ) {
					imp = el.data( 'imagep' );
					selector = '';
					if ( imp !== '' ) {
						selector = 'imagep';
					} else if ( el.data( 'changes-product-image' ) === 'images' ) {
						selector = 'image';
					}
					data = event_data.element_current.data( 'image-variations' );

					if ( data ) {
						data = data[ selector ];
					}

					if ( data === undefined ) {
						// Enter the following if you want to restore the original product image
						// main_product.trigger( 'tm_restore_product_image', event_data );
						return;
					}

					last_active_field = [];
					$( this_epo_container.find( '.tm-product-image:checked,select.tm-product-image' ) )
						.add( variationsForm.find( '.tm-epo-variation-section' ).first().find( '.tm-product-image:checked,select.tm-product-image' ) )
						.each( function() {
							t = $( this );
							if ( t.is( 'option' ) ) {
								t = t.closest( 'select' );
							}
							if ( field_is_active( t ) && t.val() !== '' ) {
								last_active_field.push( t );
							}
						} );

					if ( last_active_field.length ) {
						if ( ! last_active_field[ last_active_field.length - 1 ].is( el ) ) {
							return;
						}
					}

					setTimeout( function() {
						if ( TMEPOJS.tm_epo_global_image_recalculate === 'yes' ) {
							img = get_main_product_image( epoObject, product_element );
						}
						image_update( data, img, product_element );
						gallery_compatibility_actions( gallery_type, img, data.image_link, false, event_data, $form, product_element );
					}, window.tc_epo_product_image_change_delay || 0 );
				}
			} );

			main_product.off( 'tm_restore_product_image' ).on( 'tm_restore_product_image', function( evt, event_data ) {
				evt.stopImmediatePropagation();

				el = event_data ? event_data.element : false;
				last_active_field = [];

				if ( el ) {
					$( this_epo_container.find( '.tm-product-image:checked,select.tm-product-image' ) )
						.add( variationsForm.find( '.tm-epo-variation-section' ).first().find( '.tm-product-image:checked,select.tm-product-image' ) )
						.each( function() {
							t = $( this );
							if ( field_is_active( t ) && t.val() !== '' ) {
								last_active_field.push( t );
							}
						} );
					if ( last_active_field.length ) {
						if ( ! last_active_field[ last_active_field.length - 1 ].is( el ) ) {
							last_active_field[ last_active_field.length - 1 ].trigger( 'tm_trigger_product_image' );
							return;
						}
					}
				}

				image_update( false, img, product_element );
				gallery_compatibility_actions( gallery_type, false, img.attr( 'src' ), false, event_data, $form, product_element );
			} );

			last_active_field = [];
			$( this_epo_container.find( '.tm-product-image:checked,select.tm-product-image' ) )
				.add( variationsForm.find( '.tm-epo-variation-section' ).first().find( '.tm-product-image:checked,select.tm-product-image' ) )
				.each( function() {
					t = $( this );
					if ( field_is_active( t ) && t.val() !== '' ) {
						last_active_field.push( t );
					}
				} );
			if ( last_active_field.length ) {
				last_active_field[ last_active_field.length - 1 ].trigger( 'tm_trigger_product_image' );
			}
		}

		jWindow.trigger( 'tm_product_image_loaded' );
	}

	function tm_product_image_inline( epoObject ) {
		var this_epo_container = epoObject.is_associated ? epoObject.this_epo_container : epoObject.this_epo_container.not( tcAPI.epoSelector + ' ' + tcAPI.associatedEpoSelector );
		var main_product = epoObject.main_product;
		var product_id = epoObject.product_id;
		var $form = epoObject.form;
		var img;
		var product_element = get_product_element( epoObject );
		var gallery_type;
		var a;
		var img_src_original;
		var img_width;
		var img_height;
		var last_active_field = [];
		var t;

		img = get_main_product_image( epoObject, product_element );

		gallery_type = get_gallery_type( epoObject, img, product_element );

		if ( $( img ).length > 0 ) {
			img.data( 'tm-current-image', false );
			a = img.closest( 'a' );
			img_src_original = img.attr( 'src' );
			img_width = img.width();
			img_height = img.height();

			main_product.off( 'tm_change_product_image' ).on( 'tm_change_product_image', function( evt, e ) {
				var variation_element_section;
				var is_variation_element;
				var $this_epo_container;
				var tm_last_visible_image_element;
				var last_activate_field = [];
				var tm_current_image_element_id;
				var can_show_image;
				var $main_product;
				var $current_product_element;
				var preload_width;
				var preload_height;
				var current_cloned_image;
				var preloader;
				var clone_image;
				var preload_img;
				var preload_img_onerror;

				variation_element_section = e.element.closest( '.cpf-section' );
				is_variation_element = variation_element_section.is( '.tm-epo-variation-section' );
				$this_epo_container = e.epo_holder;
				if ( is_variation_element ) {
					$this_epo_container = variation_element_section;
				}
				tm_last_visible_image_element = $this_epo_container.find( '.tm-product-image:checked,select.tm-product-image' );
				last_activate_field = [];
				tm_current_image_element_id = e.element.attr( 'id' );
				can_show_image = true;
				$main_product = e.main_product;
				$current_product_element = $main_product.closest( '#product-' + product_id );
				preload_width = img_width;
				preload_height = img_height;
				preloader = $( "<div class='blockUI blockOverlay tm-preloader-img'></div>" );

				if ( $current_product_element.length <= 0 ) {
					$current_product_element = $main_product.closest( '.post-' + product_id );
				}

				current_cloned_image = $current_product_element.find( '#' + tm_current_image_element_id + '_tmimage' );
				if ( current_cloned_image.length === 0 ) {
					current_cloned_image = img;
				}

				preload_img_onerror = function() {
					preloader.remove();
					$form.tc_image_update( false );
					img.data( 'tm-current-image', false );
					$current_product_element.find( '.tm-clone-product-image' ).hide();
					img.show();
				};

				if ( e.src === current_cloned_image.attr( 'src' ) && current_cloned_image.is( ':visible' ) ) {
					return;
				}

				if ( e.src === false ) {
					preload_img_onerror();
					return;
				}

				preloader.css( {
					width: preload_width,
					height: preload_height
				} );

				// Get last active field
				tm_last_visible_image_element.each( function() {
					t = $( this );
					if (
						field_is_active( t ) &&
						has_active_changes_product_image( t ) &&
						tm_check_field_match( {
							element: t.closest( '.cpf-element' ),
							operator: 'isnotempty',
							value: ''
						} )
					) {
						last_activate_field.push( t );
					}
				} );
				// Get last active image
				if ( last_activate_field.length ) {
					tm_last_visible_image_element = last_activate_field[ last_activate_field.length - 1 ];
				}

				if ( tm_last_visible_image_element.attr( 'id' ) !== e.element.attr( 'id' ) ) {
					can_show_image = false;
				}

				clone_image = img.tcClone();
				preload_img = new Image();
				clone_image.removeAttr( 'data-o_src' ).removeAttr( 'data-o_title' ).removeAttr( 'data-o_alt' ).removeAttr( 'data-o_srcset' ).removeAttr( 'data-o_sizes' ).removeAttr( 'srcset' ).removeAttr( 'sizes' );

				if ( can_show_image ) {
					img.before( preloader );
				}

				gallery_type.is_yith_wcmg.yith_wcmg_default_zoom = gallery_type.is_yith_wcmg.element.find( '.yith_magnifier_zoom' ).first().attr( 'href' );
				gallery_type.is_yith_wcmg.yith_wcmg_default_image = gallery_type.is_yith_wcmg.element.find( '.yith_magnifier_zoom img' ).first().attr( 'src' );

				preload_img.onerror = function() {
					preload_img_onerror();
				};

				preload_img.onload = function() {
					if ( 'naturalHeight' in this ) {
						if ( this.naturalHeight + this.naturalWidth === 0 ) {
							this.onerror();
							return;
						}
					} else if ( this.width + this.height === 0 ) {
						this.onerror();
						return;
					}
					$current_product_element.find( '#' + tm_current_image_element_id + '_tmimage' ).remove();
					$current_product_element.find( '.tm-clone-product-image' ).hide();
					clone_image.prop( 'src', preload_img.src ).hide();

					img.hide().after( clone_image );

					clone_image.css( 'opacity', 0 ).show();

					gallery_compatibility_actions( gallery_type, clone_image, preload_img.src );

					preloader.animate(
						{
							opacity: 0
						},
						750,
						'easeOutExpo',
						function() {
							preloader.remove();
						}
					);
					clone_image.animate(
						{
							opacity: 1
						},
						window.tc_epo_image_animation_delay || 1500,
						'easeOutExpo',
						function() {}
					);

					jWindow.trigger( 'tm_change_product_image_loaded', {
						src: e.src,
						element: e.element,
						main_product: e.main_product,
						epo_holder: e.epo_holder
					} );
				};

				clone_image
					.attr( 'id', tm_current_image_element_id + '_tmimage' )
					.addClass( 'tm-clone-product-image' )
					.hide();

				if ( clone_image.attr( 'src-orig' ) ) {
					clone_image.attr( 'src-orig', e.src );
				}

				if ( can_show_image ) {
					preload_img.src = e.src;

					$form.tc_image_update( e.element, clone_image );

					img.data( 'tm-current-image', tm_current_image_element_id );

					jWindow.trigger( 'tm_change_product_image_show', {
						src: e.src,
						element: e.element,
						main_product: e.main_product,
						epo_holder: e.epo_holder
					} );
				} else {
					clone_image.prop( 'src', e.src ).hide();
					img.after( clone_image );
				}

				jWindow.trigger( 'tm_change_product_image_end', {
					src: e.src,
					element: e.element,
					main_product: e.main_product,
					epo_holder: e.epo_holder
				} );
			} );

			main_product.off( 'tm_restore_product_image' ).on( 'tm_restore_product_image', function( evt, e ) {
				var tm_current_image_element_id;
				var $main_product;
				var $current_product_element;
				var variation_element_section;
				var is_variation_element;
				var current_element;
				var current_image_replacement;
				var found;
				var is_it_visible;
				var len;
				var el_to_check;
				var imgSrc;
				var $this_epo_container;
				var i;

				if ( ! e || ! e.element ) {
					return false;
				}

				jWindow.trigger( 'tm_restore_product_image_pre', {
					element: e.element,
					main_product: e.main_product,
					epo_holder: e.epo_holder
				} );
				tm_current_image_element_id = e.element.attr( 'id' );
				$main_product = e.main_product;
				$current_product_element = $main_product.closest( '#product-' + product_id );
				variation_element_section = e.element.closest( '.cpf-section' );
				is_variation_element = variation_element_section.is( '.tm-epo-variation-section' );
				found = false;
				imgSrc = img_src_original;
				$this_epo_container = e.epo_holder;
				if ( is_variation_element ) {
					$this_epo_container = variation_element_section;
				}

				if ( $current_product_element.length <= 0 ) {
					$current_product_element = $main_product.closest( '.post-' + product_id );
				}

				is_it_visible = $current_product_element.find( '#' + tm_current_image_element_id + '_tmimage' ).is( ':visible' );

				$current_product_element.find( '#' + tm_current_image_element_id + '_tmimage' ).remove();

				if ( $current_product_element.find( '.tm-clone-product-image' ).length === 0 ) {
					img.show();
					img.data( 'tm-current-image', false );
					$form.tc_image_update( false );
				} else {
					if ( ! is_it_visible ) {
						jWindow.trigger( 'tm_restore_product_image_loaded_exit', {
							element: e.element,
							main_product: e.main_product,
							epo_holder: e.epo_holder
						} );
						return;
					}

					len = $current_product_element.find( '.tm-clone-product-image' ).length;
					tm_current_image_element_id = img.data( 'tm-current-image' );

					for ( i = len - 1; i >= 0; i -= 1 ) {
						current_image_replacement = $current_product_element.find( '.tm-clone-product-image' ).eq( i );
						current_element = current_image_replacement.attr( 'id' ).replace( '_tmimage', '' );
						el_to_check = $this_epo_container.find( "[id='" + current_element + "']" );

						if ( el_to_check.is( ':checked' ) && el_to_check.closest( '.cpf-element' ).is( ':visible' ) ) {
							$current_product_element.find( '.tm-clone-product-image' ).eq( i ).show();
							a.attr( 'href', $current_product_element.find( '.tm-clone-product-image' ).eq( i ).prop( 'src' ) );
							img.data( 'tm-current-image', current_element );
							found = true;
							break;
						} else {
							$current_product_element.find( '.tm-clone-product-image' ).eq( i ).hide();
						}
					}
					if ( ! found ) {
						img.show();
						img.data( 'tm-current-image', false );
						$form.tc_image_update( false );
					} else {
						$current_product_element.find( '#' + tm_current_image_element_id + '_tmimage' ).remove();
					}
				}

				if ( found ) {
					imgSrc = current_image_replacement.attr( 'src' );
				}

				gallery_compatibility_actions( gallery_type, false, imgSrc, $current_product_element.find( '.tm-clone-product-image' ).filter( ':visible' ).length );

				jWindow.trigger( 'tm_restore_product_image_loaded', {
					element: e.element,
					main_product: e.main_product,
					epo_holder: e.epo_holder
				} );
			} );

			main_product.off( 'tm_attempt_product_image' ).on( 'tm_attempt_product_image', function( evt, e ) {
				var $main_product;
				var $current_product_element;
				var variation_element_section;
				var is_variation_element;
				var $this_epo_container;
				var tm_last_visible_image_element;
				var last_activate_field;
				var tm_last_visible_image_element_id;
				var current_image_replacement;
				var current_element;
				var found;
				var tm_current_image_element_id;
				var len;
				var imgSrc;
				var el_to_check;
				var tmcie_id;
				var i;

				$main_product = e.main_product;
				$current_product_element = $main_product.closest( '#product-' + product_id );
				if ( e.element ) {
					variation_element_section = e.element.closest( '.cpf-section' );
				} else {
					variation_element_section = $( $main_product.find( '.tm-epo-variation-section' ).first(), e.epo_holder );
				}
				is_variation_element = variation_element_section.is( '.tm-epo-variation-section' );
				$this_epo_container = e.epo_holder;
				if ( is_variation_element ) {
					$this_epo_container = variation_element_section;
				}
				tm_last_visible_image_element = $this_epo_container.find( '.tm-product-image:checked,select.tm-product-image' );
				last_activate_field = [];
				tm_last_visible_image_element_id = '';
				found = false;
				tm_current_image_element_id = img.data( 'tm-current-image' );
				imgSrc = img_src_original;

				if ( $current_product_element.length <= 0 ) {
					$current_product_element = $main_product.closest( '.post-' + product_id );
				}

				$this_epo_container = $main_product.find( '.tm-epo-variation-section' ).first().add( e.epo_holder );
				tm_last_visible_image_element = $this_epo_container.find( '.tm-product-image:checked,select.tm-product-image' );

				tm_last_visible_image_element.each( function() {
					t = $( this );
					if (
						field_is_active( t ) &&
						has_active_changes_product_image( t ) &&
						tm_check_field_match( {
							element: t.closest( '.cpf-element' ),
							operator: 'isnotempty',
							value: ''
						} )
					) {
						last_activate_field.push( t );
					}
				} );

				if ( last_activate_field.length ) {
					tm_last_visible_image_element = last_activate_field[ last_activate_field.length - 1 ];
					tm_last_visible_image_element_id = tm_last_visible_image_element.attr( 'id' );
				}

				if ( last_activate_field.length && tm_last_visible_image_element.length && ( ! tm_current_image_element_id || tm_last_visible_image_element_id !== tm_current_image_element_id ) ) {
					tm_last_visible_image_element.last().trigger( 'tm_trigger_product_image' );
					return;
				}

				tmcie_id = $this_epo_container.find( "[id='" + tm_current_image_element_id + "']" ).closest( '.cpf-element' );
				if ( ! tm_current_image_element_id || ( tmcie_id.data( 'isactive' ) !== false && tmcie_id.closest( '.cpf-section' ).data( 'isactive' ) !== false ) ) {
					return;
				}

				$current_product_element.find( '#' + tm_current_image_element_id + '_tmimage' ).remove();
				len = $current_product_element.find( '.tm-clone-product-image' ).length;

				if ( len === 0 ) {
					img.show();
					img.data( 'tm-current-image', false );
					$form.tc_image_update( false );
				} else {
					for ( i = len - 1; i >= 0; i -= 1 ) {
						current_image_replacement = $current_product_element.find( '.tm-clone-product-image' ).eq( i );
						current_element = current_image_replacement.attr( 'id' ).replace( '_tmimage', '' );
						el_to_check = $this_epo_container.find( "[id='" + current_element + "']" );

						if ( el_to_check.is( ':checked' ) && el_to_check.closest( '.cpf-element' ).is( ':visible' ) ) {
							$current_product_element.find( '.tm-clone-product-image' ).eq( i ).show();
							a.attr( 'href', $current_product_element.find( '.tm-clone-product-image' ).eq( i ).prop( 'src' ) );
							img.data( 'tm-current-image', current_element );
							found = true;
							break;
						} else {
							$current_product_element.find( '.tm-clone-product-image' ).eq( i ).hide();
						}
					}

					if ( ! found ) {
						img.show();
						img.data( 'tm-current-image', false );
						$form.tc_image_update( false );
					}
				}

				if ( found ) {
					imgSrc = current_image_replacement.attr( 'src' );
				}

				gallery_compatibility_actions( gallery_type, false, imgSrc, $current_product_element.find( '.tm-clone-product-image' ).filter( ':visible' ).length );
			} );

			$( this_epo_container.find( '.tm-product-image:checked,select.tm-product-image' ) )
				.add( main_product.find( '.tm-epo-variation-section' ).first().find( '.tm-product-image:checked,select.tm-product-image' ) )
				.each( function() {
					t = $( this );
					if ( field_is_active( t ) && t.val() !== '' ) {
						last_active_field.push( t );
					}
				} );
			if ( last_active_field.length ) {
				last_active_field[ last_active_field.length - 1 ].trigger( 'tm_trigger_product_image' );
			}
		}

		jWindow.trigger( 'tm_product_image_loaded' );
	}

	function tm_product_image( epoObject ) {
		setTimeout( function() {
			if ( TMEPOJS.tm_epo_global_product_image_mode === 'inline' ) {
				tm_product_image_inline( epoObject );
			} else {
				tm_product_image_self( epoObject );
			}
		}, window.tc_epo_product_image_setup_delay || 0 );
	}

	function tc_compatibility( epoObject ) {
		jWindow.trigger( 'tm-epo-compatibility', {
			epo: epoObject
		} );
	}

	// Range picker setup
	function setRangePickers( obj ) {
		if ( ! noUiSlider ) {
			return;
		}
		obj.find( '.tm-range-picker' )
			.toArray()
			.forEach( function( picker ) {
				var el = $( picker );
				var $decimals = el.attr( 'data-step' ).split( '.' );
				var $tmfid = obj.find( '#' + $.epoAPI.dom.id( el.attr( 'data-field-id' ) ) );
				var $min = parseFloat( el.attr( 'data-min' ) );
				var $max = parseFloat( el.attr( 'data-max' ) );
				var $start = parseFloat( el.attr( 'data-start' ) );
				var $step = parseFloat( el.attr( 'data-step' ) );
				var $show_picker_value = el.attr( 'data-show-picker-value' );
				var $show_label = el.closest( 'li' ).find( '.tm-show-picker-value' );
				var $noofpips = parseFloat( el.attr( 'data-noofpips' ) );
				var $pips = null;
				var $tmh;

				if ( el.data( 'tc-picker-init' ) ) {
					return;
				}
				el.data( 'tc-picker-init', 1 );

				if ( $decimals.length === 1 ) {
					$decimals = 0;
				} else {
					$decimals = $decimals[ 1 ].length;
				}
				if ( ! Number.isFinite( $min ) ) {
					$min = 0;
				}
				if ( ! Number.isFinite( $max ) ) {
					$max = 0;
				}
				if ( $max <= $min ) {
					$max = parseFloat( $max ) + 1;
				}
				$start = $.epoAPI.math.unformat( $start, tcAPI.localDecimalSeparator );
				if ( ! Number.isFinite( $start ) ) {
					$start = 0;
				}
				$start = formatPrice( $start, { precision: $decimals } );
				if ( ! Number.isFinite( $step ) ) {
					$step = 0;
				}
				if ( ! Number.isFinite( $noofpips ) ) {
					$noofpips = 10;
				}
				if ( $noofpips < 2 ) {
					$noofpips = 2;
				}

				if ( el.attr( 'data-pips' ) === 'yes' ) {
					$pips = {
						mode: 'count',
						values: $noofpips,
						filter: function( value, type ) {
							value = parseFloat( $.epoAPI.math.toFixed( value, $decimals ) );

							if ( $step <= 0 ) {
								return 0;
							}

							if ( type === 1 ) {
								if ( ! Number.isInteger( value ) ) {
									return 2;
								}
							}

							return type;
						},
						format: {
							from: function( value ) {
								return $.epoAPI.math.unformat( value, tcAPI.localInputDecimalSeparator );
							},
							to: function( value ) {
								return formatPrice( value, { precision: $decimals } );
							}
						},
						density: 2
					};
				}

				noUiSlider.create( el.get( 0 ), {
					direction: TMEPOJS.text_direction,
					start: $start,
					step: $step,
					connect: 'lower',
					// Configure tapping, or make the selected range dragable.
					behaviour: 'tap',
					// Full number format support.
					format: {
						from: function( value ) {
							return $.epoAPI.math.unformat( value, tcAPI.localDecimalSeparator );
						},
						to: function( value ) {
							return formatPrice( value, { precision: $decimals } );
						}
					},
					// Support for non-linear ranges by adding intervals.
					range: {
						min: [ $min ],
						max: [ $max ]
					},
					pips: $pips,
					tooltips: {
						from: function( value ) {
							return $.epoAPI.math.unformat( value, tcAPI.localInputDecimalSeparator );
						},
						to: function( value ) {
							return formatPrice( value, { precision: $decimals } );
						}
					}
				} );

				$tmh = el.find( '.noui-handle-lower' );
				el.get( 0 ).noUiSlider.on( 'slide', function() {
					$tmfid.trigger( 'change.cpf' );
				} );
				el.get( 0 ).noUiSlider.on( 'update', function( values, handle ) {
					values[ handle ] = $.epoAPI.math.unformat( values[ handle ], tcAPI.localDecimalSeparator );
					handle = 0; //fixes rtl issue.
					if ( $show_picker_value !== 'left' && $show_picker_value !== 'right' ) {
						$tmh.attr(
							'title',
							formatPrice( values[ handle ], { precision: $decimals } )
						);
					}
					$tmfid.val( values[ handle ] ).trigger( 'change' );
					if ( $show_picker_value !== '' ) {
						$show_label.html(
							formatPrice( values[ handle ], { precision: $decimals } )
						);
					}
				} );

				if ( $show_picker_value !== '' ) {
					$show_label.html( $start );
				}

				if ( $show_picker_value !== 'left' && $show_picker_value !== 'right' ) {
					$tmh.attr( 'title', $start );
					el.addClass( 'noui-show-tooltip' );
				}
			} );
	}

	// Range picker event setup
	function setRangePickersEvents() {
		if ( ! noUiSlider || jDocument.data( 'setRangePickersEvents' ) ) {
			return;
		}
		jDocument.on( 'click', '.tm-show-picker-value', function() {
			var $this = $( this );
			var li = $this.closest( '.tmcp-field-wrap' );
			var value = li.find( '.tmcp-range' ).val();
			var edit;
			var html5Slider = $this.closest( '.tmcp-field-wrap' ).find( '.tm-range-picker' );

			$this.addClass( 'tc-hidden' ).after( $( '<input type="number" class="tm-show-picker-value-edit">' ) );
			edit = li.find( '.tm-show-picker-value-edit' );
			edit.focus().val( value ).wrap( '<div class="tm-show-picker-value-edit-wrap"></div>' );
			edit.attr( 'step', html5Slider.attr( 'data-step' ) );
			edit.attr( 'min', html5Slider.attr( 'data-min' ) );
			edit.attr( 'max', html5Slider.attr( 'data-max' ) );
			jDocument.data( 'range-picker-edit', li.find( '.tm-show-picker-value-edit' ) );
		} );
		jDocument.on( 'change input', '.tm-show-picker-value-edit', function() {
			var $this = $( this );
			var html5Slider = $this.closest( '.tmcp-field-wrap' ).find( '.tm-range-picker' );
			var $decimals = html5Slider.attr( 'data-step' ).split( '.' );
			var value = $this.val();
			if ( $decimals.length === 1 ) {
				$decimals = 0;
			} else {
				$decimals = $decimals[ 1 ].length;
			}

			html5Slider.get( 0 ).noUiSlider.set( [ formatPrice( value, { precision: $decimals } ), null ] );
		} );
		jDocument.on( 'keydown', '.tm-show-picker-value-edit', function( event ) {
			var key;

			key = event.which;
			if ( key === 13 ) {
				event.preventDefault();
			}
		} );
		jDocument.on( 'click', function( event ) {
			var $this;

			if ( ! jDocument.data( 'range-picker-edit' ) || $( event.target ).is( '.tm-show-picker-value' ) ) {
				return;
			}

			if ( $( event.target ).closest( '.tm-show-picker-value-edit-wrap' ).length === 0 ) {
				$this = jDocument.data( 'range-picker-edit' );
				$this.closest( '.tmcp-field-wrap' ).find( '.tm-show-picker-value' ).data( 'has-edit', 0 ).removeClass( 'tc-hidden' );
				$this.closest( '.tm-show-picker-value-edit-wrap' ).remove();
				jDocument.removeData( 'range-picker-edit' );
			}
		} );
		jDocument.data( 'setRangePickersEvents', 1 );
	}

	function validate_date_with_options( date, inputElement ) {
		var input = $( inputElement );
		var inst = $.tm_datepicker._getInst( input[ 0 ] );
		var enabled_only_dates = input.data( 'tc-enabled_only_dates' );
		var disabled_weekdays = input.data( 'tc-disabled_weekdays' );
		var disabled_months = input.data( 'tc-disabled_months' );
		var disabled_dates = input.data( 'tc-disabled_dates' );
		var format = input.data( 'tc-format' );
		var day = date.getDay();
		var month = date.getDay() + 1;
		var string;

		if ( ! $.tm_datepicker._isInRange( inst, date ) ) {
			return false;
		}
		if ( enabled_only_dates !== '' ) {
			string = $.tm_datepicker.formatDate( format, date );
			return enabled_only_dates.indexOf( string ) !== -1;
		}
		if ( disabled_weekdays.indexOf( day.toString() ) !== -1 ) {
			return false;
		}
		if ( disabled_months.indexOf( month.toString() ) !== -1 ) {
			return false;
		}
		if ( disabled_dates !== '' ) {
			string = $.tm_datepicker.formatDate( format, date );
			return disabled_dates.indexOf( string ) === -1;
		}
		return true;
	}

	function correctDate( days ) {
		var sign, testDate, count, added, noOfDaysToAdd;
		if ( days.toString().isNumeric() ) {
			sign = days === 0 ? days : ( days > 0 ? 1 : -1 );
			if ( sign !== 0 ) {
				testDate = new Date();
				count = 1;
				added = false;
				noOfDaysToAdd = Math.abs( days );
				while ( count <= noOfDaysToAdd ) {
					if ( added === false ) {
						added = 0;
					}
					testDate.setDate( testDate.getDate() + ( 1 * sign ) );
					added++;
					if ( testDate.getDay() !== 0 && testDate.getDay() !== 6 ) {
						count++;
					}
				}
				if ( added !== false ) {
					days = added * sign;
				}
			}
		}
		return days;
	}
	// Date and time picker setup
	function tm_set_datepicker( obj ) {
		var inputIds;
		var elem;
		var timepickerSelector = '.tm-epo-timepicker';

		if ( ! $.tm_datepicker ) {
			return;
		}

		inputIds = $( 'input' )
			.map( function() {
				return this.id;
			} )
			.get()
			.join( ' ' );

		elem = document.createElement( 'input' );
		elem.setAttribute( 'type', 'date' );

		if ( elem.type === 'text' ) {
			timepickerSelector = '.tm-epo-system-timepicker';
		}

		obj.find( timepickerSelector )
			.toArray()
			.forEach( function( el ) {
				var field = $( el );
				var _mintime = null;
				var _maxtime = null;
				var format = field.attr( 'data-time-format' ).trim();
				var date_theme = field.attr( 'data-time-theme' ).trim();
				var date_theme_size = field.attr( 'data-time-theme-size' ).trim();
				var date_theme_position = field.attr( 'data-time-theme-position' ).trim();
				var data_translation_hour = field.attr( 'data-tranlation-hour' ).trim();
				var data_translation_minute = field.attr( 'data-tranlation-minute' ).trim();
				var data_translation_second = field.attr( 'data-tranlation-second' ).trim();

				field.attr( 'type', 'text' );

				if ( field.attr( 'data-min-time' ).trim() !== '' ) {
					_mintime = field.attr( 'data-min-time' ).trim();
				}
				if ( field.attr( 'data-max-time' ).trim() !== '' ) {
					_maxtime = field.attr( 'data-max-time' ).trim();
				}

				if ( field.attr( 'data-custom-time-format' ).trim() !== '' ) {
					format = field.attr( 'data-custom-time-format' ).trim();
				}
				if ( ! data_translation_hour ) {
					data_translation_hour = TMEPOJS.hourText;
				}
				if ( ! data_translation_minute ) {
					data_translation_minute = TMEPOJS.minuteText;
				}
				if ( ! data_translation_second ) {
					data_translation_second = TMEPOJS.secondText;
				}

				field.tm_timepicker( Object.assign( {
					isRTL: TMEPOJS.isRTL,
					hourText: data_translation_hour,
					minuteText: data_translation_minute,
					secondText: data_translation_second,
					timeFormat: format,
					minTime: _mintime,
					maxTime: _maxtime,
					closeText: TMEPOJS.closeText,
					showOn: 'both',
					buttonText: '',

					beforeShow: function( input, inst ) {
						$( inst.dpDiv )
							.removeClass( inputIds )
							.removeClass( 'tm-ui-skin-epo tm-ui-skin-epo-black tm-datepicker-medium tm-datepicker-small tm-datepicker-large tm-datepicker-normal tm-datepicker-top tm-datepicker-bottom' )
							.addClass( this.id + ' tm-bsbb-all tm-ui-skin-' + date_theme + ' tm-timepicker tm-datepicker tm-datepicker-' + date_theme_position + ' tm-datepicker-' + date_theme_size )
							.appendTo( 'body' );

						jDocument.off( 'click', '.tm-ui-dp-overlay' ).on( 'click', '.tm-ui-dp-overlay', function() {
							field.tm_timepicker( 'hide' );
						} );
						jBody.addClass( 'tm-static' );
						field.prop( 'readonly', true );

						jWindow.trigger( {
							type: 'tm-timepicker-beforeShow',
							input: input,
							inst: inst
						} );
					},
					onClose: function() {
						jBody.removeClass( 'tm-static' );
						field.prop( 'readonly', false );
						field.trigger( 'change' );
					}
				}, window.tmTimepickerSettings || window.timepicker_settings || {} ) );
				$( '#ui-tm-datepicker-div' ).hide();
			} );

		obj.find( '.tm-epo-datepicker' )
			.toArray()
			.forEach( function( el ) {
				var field = $( el );
				var startDate = parseInt( field.attr( 'data-start-year' ).trim(), 10 );
				var endDate = parseInt( field.attr( 'data-end-year' ).trim(), 10 );
				var minDate = field.attr( 'data-min-date' ).trim();
				var maxDate = field.attr( 'data-max-date' ).trim();
				var disabled_dates = field.attr( 'data-disabled-dates' ).trim();
				var enabled_only_dates = field.attr( 'data-enabled-only-dates' ).trim();
				var exlude_disabled = field.attr( 'data-exlude-disabled' ).trim();
				var disabled_weekdays = field.attr( 'data-disabled-weekdays' ).trim().split( ',' );
				var disabled_months = field.attr( 'data-disabled-months' ).trim().split( ',' );
				var format = field.attr( 'data-date-format' ).trim();
				var show = field.attr( 'data-date-showon' ).trim();
				var default_date = field.attr( 'data-date-defaultdate' ).trim();
				var date_theme = field.attr( 'data-date-theme' ).trim();
				var date_theme_size = field.attr( 'data-date-theme-size' ).trim();
				var date_theme_position = field.attr( 'data-date-theme-position' ).trim();
				var $split;
				var $index;
				var $split2;
				var $index2;

				if ( disabled_dates !== '' ) {
					$split = disabled_dates.split( ',' );
					$index = disabled_dates.indexOf( ',' );

					if ( $index !== -1 && $split.length > 0 ) {
						disabled_dates = $split;
					}
				}
				if ( enabled_only_dates !== '' ) {
					$split2 = enabled_only_dates.split( ',' );
					$index2 = enabled_only_dates.indexOf( ',' );

					if ( $index2 !== -1 && $split2.length > 0 ) {
						enabled_only_dates = $split2;
					}
				}

				if ( minDate === '' ) {
					if ( startDate === '' ) {
						minDate = null;
					} else {
						minDate = new Date( startDate, 1 - 1, 1 );
					}
				} else if ( exlude_disabled ) {
					minDate = correctDate( minDate );
				}
				if ( maxDate === '' ) {
					if ( endDate === '' ) {
						maxDate = null;
					} else {
						maxDate = new Date( endDate, 12 - 1, 31 );
					}
				} else if ( exlude_disabled ) {
					maxDate = correctDate( maxDate );
				}

				field.data( 'tc-enabled_only_dates', enabled_only_dates );
				field.data( 'tc-disabled_weekdays', disabled_weekdays );
				field.data( 'tc-disabled_months', disabled_months );
				field.data( 'tc-disabled_dates', disabled_dates );
				field.data( 'tc-format', format );

				field.tm_datepicker( Object.assign( {
					monthNames: TMEPOJS.monthNames,
					monthNamesShort: TMEPOJS.monthNamesShort,
					dayNames: TMEPOJS.dayNames,
					dayNamesShort: TMEPOJS.dayNamesShort,
					dayNamesMin: TMEPOJS.dayNamesMin,
					isRTL: TMEPOJS.isRTL,
					showOtherMonths: true,
					selectOtherMonths: true,
					showOn: show,
					defaultDate: default_date,
					buttonText: '',
					showButtonPanel: true,
					firstDay: TMEPOJS.first_day,
					closeText: TMEPOJS.closeText,
					currentText: TMEPOJS.currentText,
					dateFormat: format,
					minDate: minDate,
					maxDate: maxDate,
					onSelect: function() {
						var input = $( this );
						var id = '#' + $.epoAPI.dom.id( input.attr( 'id' ) );
						var date = input.tm_datepicker( 'getDate' );
						var day = '';
						var month = '';
						var year = '';
						var day_field = obj.find( id + '_day' );
						var month_field = obj.find( id + '_month' );
						var year_field = obj.find( id + '_year' );
						var string;
						var ld;

						if ( date ) {
							day = date.getDate();
							month = date.getMonth() + 1;
							year = date.getFullYear();
							string = $.tm_datepicker.formatDate( format, date );
							if (
								disabled_months.indexOf( month.toString() ) !== -1 ||
								disabled_weekdays.indexOf( date.getDay().toString() ) !== -1 ||
								disabled_dates.indexOf( string ) !== -1 ||
								( enabled_only_dates !== '' && enabled_only_dates.indexOf( string ) === -1 )
							) {
								ld = input.data( 'tm-last-date' );
								if ( input.data( 'tm-last-date' ) ) {
									ld = input.data( 'tm-last-date' );
								} else {
									ld = '';
								}
								input.val( ld );
								input.tm_datepicker( 'setDate', ld );
								if ( ld ) {
									date = input.tm_datepicker( 'getDate' );
									day = date.getDate();
									month = date.getMonth() + 1;
									year = date.getFullYear();
								} else {
									day = '';
									month = '';
									year = '';
								}
							}
						}

						day_field.val( day );
						month_field.val( month );
						year_field.val( year );

						input.data( 'tm-last-date', input.val() );
						input.tm_datepicker( 'hide' );
					},
					beforeShow: function( input, inst ) {
						$( inst.dpDiv )
							.removeClass( inputIds )
							.removeClass( 'tm-datepicker-normal tm-datepicker-top tm-datepicker-bottom' )
							.addClass( this.id + ' tm-bsbb-all tm-ui-skin-' + date_theme + ' tm-datepicker tm-datepicker-' + date_theme_position + ' tm-datepicker-' + date_theme_size )
							.appendTo( 'body' );

						jDocument.off( 'click', '.tm-ui-dp-overlay' ).on( 'click', '.tm-ui-dp-overlay', function() {
							field.tm_datepicker( 'hide' );
						} );
						jDocument.off( 'click', '.ui-tm-datepicker-current' ).on( 'click', '.ui-tm-datepicker-current', function() {
							var tempDate = new Date(),
								today = $.tm_datepicker._daylightSavingAdjust( new Date( tempDate.getFullYear(), tempDate.getMonth(), tempDate.getDate() ) );
							var day = today.getDay();
							var month = today.getMonth() + 1;
							var id = '#' + inst.id.replace( /\\\\/g, '\\' );
							var check = false;
							var string;
							var date = field.tm_datepicker( 'getDate' );

							if ( enabled_only_dates !== '' ) {
								string = $.tm_datepicker.formatDate( format, date );
								check = enabled_only_dates.indexOf( string ) !== -1;
							} else if ( disabled_months.indexOf( month.toString() ) !== -1 || disabled_weekdays.indexOf( day.toString() ) !== -1 ) {
								check = false;
							} else {
								if ( disabled_dates !== '' ) {
									string = $.tm_datepicker.formatDate( format, date );
									return [ disabled_dates.indexOf( string ) === -1, '' ];
								}
								check = true;
							}
							if ( check ) {
								$.tm_datepicker._setDate( inst, today );
								$.tm_datepicker._gotoToday( id );
							}
						} );
						jBody.addClass( 'tm-static' );

						jWindow.trigger( {
							type: 'tm-datepicker-beforeShow',
							input: input,
							inst: inst
						} );
					},
					onClose: function() {
						jBody.removeClass( 'tm-static' );
						field.trigger( 'change' );
					},
					beforeShowDay: function( date ) {
						var day = date.getDay();
						var month = date.getMonth() + 1;
						var string;

						if ( enabled_only_dates !== '' ) {
							string = $.tm_datepicker.formatDate( format, date );
							return [ enabled_only_dates.indexOf( string ) !== -1, '' ];
						}
						if ( disabled_months.indexOf( month.toString() ) !== -1 || disabled_weekdays.indexOf( day.toString() ) !== -1 ) {
							return [ false, '' ];
						}
						if ( disabled_dates !== '' ) {
							string = $.tm_datepicker.formatDate( format, date );
							return [ disabled_dates.indexOf( string ) === -1, '' ];
						}
						return [ true, '' ];
					}
				}, window.tmDatepickerSettings || {} ) );

				$( '#ui-tm-datepicker-div' ).hide();
			} );

		obj.find( '.tmcp-date-select' )
			.on( 'change.cpf', function() {
				var id = '#' + $.epoAPI.dom.id( $( this ).attr( 'data-tm-date' ) );
				var input = obj.find( id );
				var format = input.attr( 'data-date-format' );
				var day = obj.find( id + '_day' ).val();
				var month = obj.find( id + '_month' ).val();
				var year = obj.find( id + '_year' ).val();
				var dateFormat = $.tm_datepicker.formatDate( format, new Date( year, parseInt( month, 10 ) - 1, day ) );

				if ( day > 0 && month > 0 && year > 0 ) {
					input.tm_datepicker( 'setDate', dateFormat );
					input.trigger( 'change' );
				} else {
					input.val( '' );
					input.trigger( 'change.cpf' );
				}
			} )
			.on( 'focus.cpf', function() {
				var id = '#' + $.epoAPI.dom.id( $( this ).attr( 'data-tm-date' ) );
				var input = obj.find( id );
				var day_select = obj.find( id + '_day' );
				var month_select = obj.find( id + '_month' );
				var year_select = obj.find( id + '_year' );
				var day = day_select.val();
				var month = month_select.val();
				var year = year_select.val();
				var _select = $( this );

				if ( ( year !== '' && month !== '' && day !== '' ) || ( year !== '' && month !== '' && day === '' ) || ( day !== '' && year !== '' && month === '' ) || ( day !== '' && month !== '' && year === '' ) ) {
					_select
						.find( 'option' )
						.toArray()
						.forEach( function( element ) {
							var option = $( element );
							var val = option.val();
							var date_string = year + '-' + month + '-' + day;
							var d;

							if ( _select.is( '.tmcp-date-day' ) ) {
								if ( year === '' || month === '' ) {
									return;
								}
								date_string = year + '-' + month + '-' + val;
							} else if ( _select.is( '.tmcp-date-month' ) ) {
								if ( year === '' || day === '' ) {
									return;
								}
								date_string = year + '-' + val + '-' + day;
							} else if ( _select.is( '.tmcp-date-year' ) ) {
								if ( day === '' || month === '' ) {
									return;
								}
								date_string = val + '-' + month + '-' + day;
							}

							if ( val !== '' ) {
								try {
									d = $.tm_datepicker.parseDate( 'yy-mm-dd', date_string );
									if ( d ) {
										if ( validate_date_with_options( d, input ) ) {
											option.prop( 'disabled', false );
										} else {
											option.prop( 'disabled', true );
										}
									}
								} catch ( err ) {
									window.console.log( err );

									option.prop( 'disabled', true );
									errorObject = err;
								}
							}
						} );
				} else {
					day_select.find( 'option' ).prop( 'disabled', false );
					month_select.find( 'option' ).prop( 'disabled', false );
					year_select.find( 'option' ).prop( 'disabled', false );
				}
			} );

		jWindow.on( 'resizestart', function() {
			var activeElement = $( document.activeElement );

			if ( activeElement.is( '.hasDatepicker' ) ) {
				activeElement.data( 'resizestarted', true );

				// we don't use jWindow here because we want the current window width
				if ( $( window ).width() < 768 ) {
					activeElement.data( 'resizewidth', true );
					return;
				}
				activeElement.tm_datepicker( 'hide' );
			}
		} );
		jWindow.on( 'resizestop', function() {
			var activeElement = $( document.activeElement );

			if ( activeElement.is( '.hasDatepicker' ) && activeElement.data( 'resizestarted' ) ) {
				if ( activeElement.data( 'resizewidth' ) ) {
					activeElement.tm_datepicker( 'hide' );
				}
				activeElement.tm_datepicker( 'show' );
			}
			activeElement.data( 'resizestarted', false );
			activeElement.data( 'resizewidth', false );
		} );
	}

	function apply_submit_events( epoObject ) {
		var epoEventId = epoObject.epoEventId;
		var main_product = epoObject.main_product;
		var type;
		var form_is_submit = ! $.tcepo.formSubmitEvents[ epoEventId ].some( function( form_event ) {
			return typeof form_event && ( typeof form_event.trigger === 'function' || false ) && ! form_event.trigger();
		} );

		$.tcepo.formSubmitEvents[ epoEventId ].forEach( function( form_event ) {
			type = typeof form_event;
			if ( type === 'object' ) {
				if ( form_is_submit ) {
					form_event.on_true();
				} else {
					form_event.on_false();
				}
			}
		} );

		if ( ! form_is_submit ) {
			setTimeout( function() {
				main_product.find( tcAPI.addToCartButtonSelector ).first().removeClass( 'disabled' ).removeClass( 'loading' ).prop( 'disabled', false ).removeClass( 'fpd-disabled' );
			}, 100 );
		}

		jWindow.trigger( 'tm-apply-submit-events', {
			epo: {
				form_is_submit: form_is_submit
			}
		} );

		return form_is_submit;
	}

	function tm_apply_validation( epoObject ) {
		var form = epoObject.form;
		var this_epo_container = epoObject.this_epo_container;
		var main_product = epoObject.main_product;
		var epoEventId = epoObject.epoEventId;
		var validation_rules;
		var has_rules;

		if ( TMEPOJS.tm_epo_global_enable_validation === 'yes' ) {
			validation_rules = {};

			this_epo_container
				.find( '.tmcp-ul-wrap' )
				.toArray()
				.forEach( function( tmcpulwrap ) {
					var field;
					var field_name;
					var subFieldName;
					var subRule;
					var productField;

					tmcpulwrap = $( tmcpulwrap );
					has_rules = tmcpulwrap.data( 'tm-validation' );
					if ( has_rules && $.tmType( has_rules ) === 'object' ) {
						field = tmcpulwrap.find( '.tm-epo-field' );
						field_name = field.first().attr( 'name' );
						if ( tmcpulwrap.is( '.tm-extra-product-options-radio.tm-element-ul-radio' ) ) {
							field_name = field.last().attr( 'name' );
							validation_rules[ field_name ] = has_rules;
						} else if ( tmcpulwrap.is( '.tm-extra-product-options-checkbox.tm-element-ul-checkbox' ) ) {
							field.each( function( f, fname ) {
								if ( 'required' in has_rules ) {
									has_rules.required = function( elem ) {
										var len = tmcpulwrap.find( 'input.tm-epo-field.tmcp-checkbox:checked' ).length;
										if ( len === 0 ) {
											if ( field.last().attr( 'name' ) === $( elem ).attr( 'name' ) ) {
												return true;
											}
											return false;
										}
										return len <= 0;
									};
								}
								validation_rules[ $( fname ).attr( 'name' ) ] = has_rules;
							} );
						} else if ( tmcpulwrap.is( '.tm-extra-product-options-product.tm-element-ul-product' ) ) {
							if ( field.is( ':checkbox' ) ) {
								field.each( function( f, fname ) {
									if ( 'required' in has_rules ) {
										productField = $( fname );
										if ( productField.is( ':checkbox' ) ) {
											has_rules.required = function( elem ) {
												var checkedFields = tmcpulwrap.find( 'input.tm-epo-field.tmcp-checkbox:checked' );
												var len = checkedFields.length;
												var qtyCheck;
												if ( len === 0 ) {
													if ( field.last().attr( 'name' ) === $( elem ).attr( 'name' ) ) {
														return true;
													}
													return false;
												}
												qtyCheck = checkedFields.map( function() {
													return $( this );
												} ).get().some( function( element ) {
													return element.closest( '.tc-epo-element-product-holder' ).find( '.tm-qty' ).first().val() <= 0;
												} );
												if ( len > 0 ) {
													return qtyCheck;
												}
												return len <= 0;
											};
											if ( productField.attr( 'data-type' ) === 'variable' ) {
												subFieldName = productField.closest( '.tc-epo-element-product-holder' ).find( '.product-variation-id' ).first().attr( 'name' );
												subRule = {
													required: function( elem ) {
														var element = $( elem );
														var holder = element.closest( '.tc-epo-element-product-holder' );
														var checkbox = holder.find( '.tc-epo-field-product' );

														if ( field_is_active( checkbox, true ) && checkbox.is( ':checked' ) ) {
															return true;
														}
														return false;
													}
												};
												validation_rules[ subFieldName ] = subRule;
											}
										}
									}
									validation_rules[ productField.attr( 'name' ) ] = has_rules;
								} );
							} else if ( field.is( ':radio' ) ) {
								has_rules.product_element_radio_qty = true;
								validation_rules[ field.attr( 'name' ) ] = has_rules;
								subFieldName = field.closest( '.tc-element-container' ).find( '.product-variation-id' ).first().attr( 'name' );
								subRule = {
									required: function( elem ) {
										var element = $( elem );
										var holder = element.closest( '.tc-element-container' );
										var radiobutton = holder.find( '.tc-epo-field-product:checked' );

										if ( radiobutton.length && radiobutton.attr( 'data-type' ) === 'variable' && field_is_active( radiobutton, true ) && radiobutton.is( ':checked' ) ) {
											return true;
										}
										return false;
									}
								};
								validation_rules[ subFieldName ] = subRule;
							} else if ( field.is( 'select' ) ) {
								has_rules.product_element_select_qty = true;
								validation_rules[ field.attr( 'name' ) ] = has_rules;
								subFieldName = field.closest( '.tc-element-container' ).find( '.product-variation-id' ).first().attr( 'name' );
								subRule = {
									required: function( elem ) {
										var element = $( elem );
										var holder = element.closest( '.tc-element-container' );
										var select = holder.find( '.tc-epo-field-product' );

										if ( select.length && select.children( ':selected' ).attr( 'data-type' ) === 'variable' && field_is_active( select, true ) ) {
											return true;
										}
										return false;
									}
								};
								validation_rules[ subFieldName ] = subRule;
							}
						} else {
							validation_rules[ field_name ] = has_rules;
						}
					}
				} );

			form.removeData( 'tc_validator' );
			form.tc_validate( {
				focusInvalid: false,
				ignore:
					tcAPI.qtySelector +
					",.tcdisabled,.tmcp-upload-hidden,#wc_bookings_field_duration,input.tm-qty:hidden[type='number'],input.input-text.qty,.ignore,.variations select,.tc-epo-variable-product-selector,.tm-extra-product-options-variations input,.tm-extra-product-options-variations select,input:not(.tc-extra-product-options input),select:not(.tc-extra-product-options select)",
				rules: validation_rules,
				errorClass: 'tm-error',
				validClass: 'tm-valid',
				errorElement: 'label',
				errorPlacement: function( error, element ) {
					error.addClass( 'tc-cell tcwidth tcwidth-100' );
					if ( TMEPOJS.tm_epo_global_error_label_placement === 'before' ) {
						element.closest( '.tc-element-container' ).before( error );
					} else {
						element.closest( '.tc-element-container' ).after( error );
					}
					return false;
				},
				invalidHandler: function( event, validator ) {
					jWindow.trigger( 'tm-invalidHandler', {
						epo: {
							validator: validator
						}
					} );
					setTimeout( function() {
						if ( ! main_product.find( tcAPI.addToCartButtonSelector ).first().is( '.disabled' ) ) {
							main_product.find( tcAPI.addToCartButtonSelector ).first().removeClass( 'loading' ).prop( 'disabled', false ).removeClass( 'fpd-disabled' );
							main_product.find( tcAPI.addToCartButtonSelector ).first().removeClass( 'disabled' ).removeClass( 'loading' ).prop( 'disabled', false ).removeClass( 'fpd-disabled' );
						}
					}, 100 );
					if ( validator.errorList && validator.errorList[ 0 ] && validator.errorList[ 0 ].element ) {
						goto_error_item( $( validator.errorList[ 0 ].element ), epoEventId );
					}
				},
				submitHandler: function() {
					var ajaxSuccessFunc;
					if ( ! epoObject.is_quickview ) {
						main_product.find( tcAPI.addToCartButtonSelector ).first().addClass( 'disabled' );
						ajaxSuccessFunc = function() {
							main_product.find( tcAPI.addToCartButtonSelector ).first().removeClass( 'disabled' );
							jDocument.off( 'ajaxSuccess', ajaxSuccessFunc );
						};
						jDocument.on( 'ajaxSuccess', ajaxSuccessFunc );
					}
					return apply_submit_events( epoObject );
				}
			} );

			// This should handle most ajax based add to cart solutions
			form.find( tcAPI.addToCartButtonSelector ).on( 'click', function( e ) {
				if ( ! form.tc_validate().form() ) {
					e.preventDefault();
					e.stopImmediatePropagation();
				}
			} );

			return true;
		}
		return false;
	}

	function tm_form_submit_event( epoObject ) {
		var form = epoObject.form;
		var epoEventId = epoObject.epoEventId;

		jWindow.trigger( 'tm-from-submit', {
			epo: epoObject,
			functions: {
				tm_apply_validation: tm_apply_validation,
				apply_submit_events: apply_submit_events
			}
		} );
		if ( ! tm_apply_validation( epoObject ) && $.tcepo.formSubmitEvents[ epoEventId ].length ) {
			form.on( 'submit', function() {
				apply_submit_events( epoObject );
			} );
		}

		jWindow.on( 'tc_apply_validation', function() {
			return tm_apply_validation( epoObject );
		} );
	}

	function found_variation_tmepo( dataObject ) {
		var epoHolder = dataObject.epoHolder;
		var totalsHolder = dataObject.totalsHolder;
		var totalsHolderContainer = dataObject.totalsHolderContainer;
		var currentCart = dataObject.currentCart;
		var variationForm = dataObject.variationForm;
		var variation = dataObject.variation;
		var variations = totalsHolder.data( 'variations' );
		var product_price;

		totalsHolder.data( 'current_variation', variation );

		/**
		 * Currency converters that don't allow multi currency checkout will fail the following if statement
		 *
		 * if (variation.display_price!=undefined) {
		 *     product_price = variation.display_price;
		 *     totalsHolder.data('price', product_price);
		 * } else ...
		 *
		 */
		// This needs to be the first check as the next one
		// fails for taxed products as it displays the backend price only.
		if ( variation && 'display_price' in variation && ! totalsHolder.data( 'tm-epo-is-woocs' ) ) {
			product_price = variation.display_price;

			totalsHolder.data( 'priceIsWithDiscount', '1' );
			// Fancy product Designer
			totalsHolder.removeData( 'tcprice' );
		} else if ( variations && variation && variation.variation_id && variations[ variation.variation_id ] !== undefined ) {
			product_price = variations[ variation.variation_id ];

			// Fancy product Designer
			totalsHolder.removeData( 'tcprice' );
		} else if ( variation && $( variation.price_html ).find( '.amount:last' ).length ) {
			product_price = $( variation.price_html ).find( '.amount:last' ).text();
			product_price = product_price.replace( TMEPOJS.currency_format_thousand_sep, '' );
			product_price = product_price.replace( TMEPOJS.currency_format_decimal_sep, '.' );
			product_price = product_price.replace( /[^0-9.]/g, '' );
			product_price = parseFloat( product_price );

			// Fancy product Designer
			totalsHolder.removeData( 'tcprice' );
		}

		product_price = tm_set_backend_price( product_price, totalsHolder, variation );
		totalsHolder.data( 'price', product_price );

		totalsHolderContainer.find( '.cpf-product-price' ).val( product_price );

		setTimeout( function() {
			epoHolder.find( 'select.tm-epo-field' ).trigger( 'tm-select-change-html-all-math' );
		}, 100 );

		// This must be run every time to get correct results for percent price types
		// if set norules then discount will not auto work upon chosing a variation
		if ( ! variationForm.data( 'tm-styled-variations' ) ) {
			currentCart.trigger( {
				type: 'tm-epo-update'
			} );
		}
	}

	function fetchOptionPrices( epoObject, epoHolder, selector, total, original_total, floatingBoxData, showTotal, forced, setPriceTax, useUndiscountedPrice, mathTotal, cumulative, nopriceCacheSelector ) {
		var obj;
		var noDpd;
		var priceArray;
		var priceCacheSelector = 'none';
		var total_taxed;
		var original_total_taxed;
		var vat_total = 0;
		var currentTotalsContainer;
		var epoinline;
		var funcTotal;
		var cumulativeTotal;

		if ( ! nopriceCacheSelector ) {
			priceCacheSelector = epoHolder.attr( 'class' ) + epoHolder.attr( 'data-uniqid' ) + selector + epoObject.is_associated + setPriceTax + forced + setPriceTax + useUndiscountedPrice || 'none';
		}

		if ( priceCacheSelector !== 'none' && priceCache !== true && priceCache !== false && priceCache[ priceCacheSelector ] !== undefined ) {
			return priceCache[ priceCacheSelector ];
		}

		noDpd = useUndiscountedPrice ? 'undiscounted_' : '';
		obj = epoHolder.find( selector );
		if ( epoObject.is_associated === false ) {
			obj = obj.not( tcAPI.associatedEpoSelector + ' ' + selector + ',.cpf-type-variations ' + selector );
		}
		if ( ! forced ) {
			obj = obj.filter( '.tcenabled' );
		}

		if ( ! total ) {
			total = 0;
		}
		if ( ! original_total ) {
			original_total = 0;
		}

		funcTotal = total;
		cumulativeTotal = total;

		total_taxed = total;
		original_total_taxed = original_total;

		if ( ! floatingBoxData ) {
			floatingBoxData = [];
		}

		currentTotalsContainer = epoObject.this_epo_totals_container;

		if ( obj.length ) {
			obj.toArray().forEach( function( tmcpfield ) {
				var field = $( tmcpfield );
				var _value = '';
				var fieldval;
				var field_div = field.closest( '.cpf-element' );
				var field_wrap = field.closest( '.tmcp-field-wrap' );
				var field_label_show = field_div.attr( 'data-fblabelshow' );
				var field_value_show = field_div.attr( 'data-fbvalueshow' );
				var field_title = '';
				var option_quantity = field_wrap.find( '.tm-qty' ).val();
				var option_price;
				var option_price_taxed;
				var option_original_price;
				var option_original_price_taxed;
				var liw;
				var cri;
				var tl;
				var options;
				var forrangepicker;
				var $decimals;
				var _valueText = '';
				var setter;
				var dofloatingBoxData = false;

				if ( selector === '.tc-epo-field-product' ) {
					if ( field_div.is( '.cpf-type-product-thumbnailmultiple' ) || field_div.is( '.cpf-type-product-checkbox' ) ) {
						epoinline = field.closest( '.tc-epo-element-product-holder' ).find( '.tc-extra-product-options-inline' );
					} else if ( field_div.is( '.cpf-type-product-thumbnail' ) || field_div.is( '.cpf-type-product-radio' ) || field_div.is( '.cpf-type-product-dropdown' ) ) {
						epoinline = field_div.find( '.tc-epo-element-product-container[data-product_id="' + field.val() + '"]' ).find( '.tc-extra-product-options-inline' );
					} else if ( field_div.is( '.cpf-type-product-single' ) ) {
						epoinline = field_div.find( '.tc-extra-product-options-inline' );
					}
					currentTotalsContainer = $( '.tc-epo-totals' + '.tm-product-id-' + epoinline.attr( 'data-product-id' ) + '[data-epo-id="' + epoinline.attr( 'data-epo-id' ) + '"]' );
				}

				if ( currentTotalsContainer.length ) {
					if ( field_label_show === '' ) {
						field_title = field_div.find( '.tc-epo-element-label-text' ).html();
					}

					if ( option_quantity === undefined ) {
						option_quantity = '';
					}
					if ( mathTotal ) {
						tm_element_epo_rules( epoObject, field, undefined, undefined, undefined, undefined, funcTotal, cumulativeTotal );
					}
					if ( field.is( ':checkbox, :radio, :input' ) ) {
						option_price = 0;
						option_price_taxed = 0;
						option_original_price = 0;
						option_original_price_taxed = 0;
						if ( field.is( '.tmcp-checkbox, .tmcp-radio' ) ) {
							if ( forced || field.is( ':checked' ) ) {
								option_price = field.data( noDpd + 'raw_price' );
								option_price_taxed = field.data( noDpd + 'price' );
								option_original_price = field.data( noDpd + 'raw_original_price' );
								option_original_price_taxed = field.data( noDpd + 'original_price' );
								showTotal = true;
								field.data( 'isset', 1 );
								liw = field.closest( 'li.tmcp-field-wrap' );
								cri = liw.find( '.tc-image' );
								_value = '';
								_valueText = '';

								tl = field.closest( 'li.tmcp-field-wrap' ).find( '.tc-label-text' );
								if ( tl.length ) {
									_value = tl.html();
									_valueText = _value;
								}

								if ( cri.length ) {
									cri = cri.closest( '.tc-label-wrap' ).clone().addClass( 'tc-img-floating' );
									cri.find( '.tc-label, .tc-epo-style-wrapper, .tc-input-wrap' ).remove();
									_value = cri[ 0 ].outerHTML + '<span class="tc-label-text">' + _value + '</span>';
								}
								dofloatingBoxData = true;
							} else {
								field.data( 'isset', 0 );
							}
						} else if ( field.is( '.tmcp-select' ) ) {
							setter = field.find( 'option:selected' );
							option_price = setter.data( noDpd + 'raw_price' );
							option_price_taxed = setter.data( noDpd + 'price' );
							option_original_price = setter.data( noDpd + 'raw_original_price' );
							option_original_price_taxed = setter.data( noDpd + 'original_price' );

							options = field.children( 'option:selected' );
							if ( ! ( options.val() === '' && options.attr( 'data-rulestype' ) === '' ) ) {
								showTotal = true;
							}

							field.find( 'option' ).data( 'isset', 0 );
							setter.data( 'isset', 1 );

							if ( ! ( setter.val() === '' && setter.attr( 'data-rulestype' ) === '' ) ) {
								_value = setter.attr( 'data-text' );
								dofloatingBoxData = true;
								_valueText = _value;
							}
						} else if ( field.is( '.tmcp-selectmultiple' ) ) {
							setter = field.find( 'option:selected' );
							setter.toArray().forEach( function( setterel ) {
								setterel = $( setterel );
								option_price = option_price + $.epoAPI.math.toFloat( setterel.data( noDpd + 'raw_price' ) );
								option_price_taxed = option_price_taxed + $.epoAPI.math.toFloat( setterel.data( noDpd + 'price' ) );
								option_original_price = option_original_price + $.epoAPI.math.toFloat( setterel.data( noDpd + 'raw_original_price' ) );
								option_original_price_taxed = option_original_price_taxed + $.epoAPI.math.toFloat( setterel.data( noDpd + 'original_price' ) );

								options = field.children( 'option:selected' );
								if ( ! ( options.val() === '' && options.attr( 'data-rulestype' ) === '' ) ) {
									showTotal = true;
								}

								field.find( 'option' ).data( 'isset', 0 );
								setterel.data( 'isset', 1 );

								if ( ! ( setterel.val() === '' && setterel.attr( 'data-rulestype' ) === '' ) ) {
									_value = setterel.attr( 'data-text' );
									dofloatingBoxData = true;
									_valueText = _value;
								}
							} );
						} else {
							fieldval = field.val();
							if ( field.is( "[type='file']" ) ) {
								fieldval = fieldval.replace( 'C:\\fakepath\\', '' );
							}
							if ( fieldval ) {
								if ( field.is( '.tmcp-range' ) && fieldval === '0' ) {
									field.data( 'isset', 0 );
								} else {
									option_price = field.data( noDpd + 'raw_price' );
									option_price_taxed = field.data( noDpd + 'price' );
									option_original_price = field.data( noDpd + 'raw_original_price' );
									option_original_price_taxed = field.data( noDpd + 'original_price' );
									showTotal = true;
									field.data( 'isset', 1 );

									_value = fieldval;
									if ( field.is( '.tmcp-range' ) ) {
										forrangepicker = field.closest( '.tmcp-field-wrap' ).find( '.tm-range-picker' );
										$decimals = forrangepicker.attr( 'data-step' ).split( '.' );
										if ( $decimals.length === 1 ) {
											$decimals = 0;
										} else {
											$decimals = $decimals[ 1 ].length;
										}
										_value = formatPrice( _value, { precision: $decimals } );
									}
									dofloatingBoxData = true;
									if ( field.is( '.tmcp-dynamic' ) ) {
										field_label_show = 'hidden';
										field_value_show = 'hidden';
									}
									_valueText = fieldval;
								}
							} else {
								field.data( 'isset', 0 );
							}
						}
						if ( ! option_price ) {
							option_price = 0;
						}
						if ( ! option_original_price ) {
							option_original_price = 0;
						}
						if ( ! option_price_taxed ) {
							option_price_taxed = 0;
						}
						if ( ! option_original_price_taxed ) {
							option_original_price_taxed = 0;
						}
						if ( dofloatingBoxData ) {
							floatingBoxData.push( {
								title: field_title,
								value: _value,
								valueText: _valueText,
								price: option_price_taxed,
								original_price: option_original_price_taxed,
								quantity: option_quantity,
								label_show: field_label_show,
								value_show: field_value_show,
								input_type: field.attr( 'type' )
							} );
						}

						option_price_taxed = tm_set_tax_price( option_price, currentTotalsContainer, field, undefined, undefined, undefined, true );
						option_original_price_taxed = tm_set_tax_price( option_original_price, currentTotalsContainer, field, undefined, undefined, undefined, true );

						if ( setPriceTax ) {
							option_price = option_price_taxed;
							option_original_price = option_original_price_taxed;
						}

						total = $.epoAPI.math.toFloat( total ) + $.epoAPI.math.toFloat( option_price );
						total = $.epoAPI.math.toFloat( $.epoAPI.math.round( total, 10 ) );
						original_total = $.epoAPI.math.toFloat( original_total ) + $.epoAPI.math.toFloat( option_original_price );
						original_total = $.epoAPI.math.toFloat( $.epoAPI.math.round( original_total, 10 ) );

						total_taxed = $.epoAPI.math.toFloat( total_taxed ) + $.epoAPI.math.toFloat( option_price_taxed );
						total_taxed = $.epoAPI.math.toFloat( $.epoAPI.math.round( total_taxed, 10 ) );
						vat_total = vat_total + $.epoAPI.math.toFloat( calculateTaxAmount( option_price_taxed, currentTotalsContainer ) );
						original_total_taxed = $.epoAPI.math.toFloat( original_total_taxed ) + $.epoAPI.math.toFloat( option_original_price_taxed );
						original_total_taxed = $.epoAPI.math.toFloat( $.epoAPI.math.round( original_total_taxed, 10 ) );

						if ( cumulative ) {
							cumulativeTotal = total;
						}
					}
				}
			} );
		}
		priceArray = {
			total: total,
			original_total: original_total,
			total_taxed: total_taxed,
			original_total_taxed: original_total_taxed,
			vat_total: vat_total,
			floatingBoxData: floatingBoxData,
			showTotal: showTotal,
			elementsLength: obj.length
		};

		if ( priceCache === true ) {
			priceCache = {};
			priceCache[ priceCacheSelector ] = priceArray;
		} else if ( priceCache !== true && priceCache !== false ) {
			priceCache[ priceCacheSelector ] = priceArray;
		}

		return priceArray;
	}

	function show_product_html( thisEpoObject, thisMainProduct, thisVariableProductContainer, type, $this, currentCart, variableProductContainers, isTrigger, qtyalt ) {
		var epoObjectCopy = $.extend( true, {}, thisEpoObject );
		var item_tm_extra_product_options = thisVariableProductContainer.find( tcAPI.associatedEpoSelector );
		var item = thisVariableProductContainer;
		var newEpoObject;
		var showOnly = true;

		$this.removeData( 'triggeredforced' );

		if ( item.closest( '.cpf-element' ).is( '.tc-hidden' ) ) {
			return;
		}

		variableProductContainers.addClass( 'tm-hidden' );
		if ( variableProductContainers.length ) {
			toggleState( variableProductContainers, true );
		}

		if ( type === 'variable' ) {
			if ( ! thisVariableProductContainer.is( '.tc-init-variations' ) ) {
				thisVariableProductContainer.addClass( 'variations_form' );
				setTimeout( function() {
					newEpoObject = tm_init_epo( item, false, item_tm_extra_product_options.attr( 'data-product-id' ), item_tm_extra_product_options.attr( 'data-epo-id' ), $this, epoObjectCopy );
					thisVariableProductContainer.addClass( 'tc-init-variations' );
					thisVariableProductContainer.tc_product_variation_form( $this, currentCart, variableProductContainers, newEpoObject );
				}, 40 );
				showOnly = false;
			} else {
				thisVariableProductContainer.trigger( 'refresh.tc-variation-form' );
			}
		} else if ( ! thisVariableProductContainer.is( '.tc-init-product' ) ) {
			thisVariableProductContainer.addClass( 'tc-init-product' );
			variableProductContainers.find( '.tc-epo-element-variable-product' ).removeClass( 'variations_form' );
			variableProductContainers.find( '.tc-epo-element-variations' ).removeClass( 'variations' );

			setTimeout( function() {
				tm_init_epo( item, false, item_tm_extra_product_options.attr( 'data-product-id' ), item_tm_extra_product_options.attr( 'data-epo-id' ), $this, epoObjectCopy );
			}, 20 );
			showOnly = false;
		}

		setTimeout( function() {
			if ( variableProductContainers.length ) {
				toggleState( thisVariableProductContainer, false );
			}
		}, 200 );
		setTimeout( function() {
			if ( qtyalt.length ) {
				qtyalt.trigger( 'change' );
			}
			if ( thisVariableProductContainer.find( tcAPI.epoSelector ).length === 0 ) {
				thisVariableProductContainer.addClass( 'no-epo' );
			}
			thisVariableProductContainer.removeClass( 'tm-hidden' );
			if ( showOnly ) {
				jWindow.trigger( 'cpflogicdone' );
				thisVariableProductContainer.find( '.tm-quantity' ).trigger( 'showhide.cpfcustom' );
			}
			if ( isTrigger === undefined && TMEPOJS.tm_epo_global_product_element_scroll === 'yes' ) {
				jWindow.tcScrollTo( thisVariableProductContainer, 200, $.epoAPI.math.toFloat( TMEPOJS.tm_epo_global_product_element_scroll_offset ) );
			}
		}, 210 );
	}

	function epoEventHandlers( epoObject, cartContainer, alternativeCart ) {
		// if cartContainer & alternativeCart is defined we are on a non default product (eg. composite product)
		var product_id = epoObject.product_id;
		var main_product = epoObject.main_product;
		var main_cart = epoObject.main_cart;
		var this_epo_container = epoObject.this_epo_container;
		var this_totals_container = epoObject.this_totals_container;
		var this_epo_totals_container = epoObject.this_epo_totals_container;
		var epoEventId = epoObject.epoEventId;
		var main_epo_inside_form = epoObject.main_epo_inside_form;
		var epo_id_selector = epoObject.epo_id_selector;
		var epo_id = epoObject.epo_id;
		var product_id_selector = epoObject.product_id_selector;
		var itemId = 'main';
		var epoHolder;
		var totalsHolderContainer;
		var totalsHolder;
		var currentCart;
		var variation_id_selector;
		var this_product_type;
		var variationForm;
		var qtyElement;
		var finalTotalBoxMode;
		var finalTotalBoxShowFinal;
		var finalTotalBoxShowOptions;
		var eventName = epoObject.is_associated ? 'tc-variation-form' : 'wc-variation-form';
		var eventNamePrefix = epoObject.is_associated ? 'tc_' : '';
		var epoVariationSection;
		var thismaxlength;
		var epoFieldAll;
		var selectSelector;
		var epoFieldText;
		var epoResetRadio;
		var epoFieldUpload;
		var epoFieldHasClearButton;
		var tmQty;
		var tmQuantity;

		// Non default product (eg. composite product)
		if ( alternativeCart && cartContainer ) {
			itemId = $.epoAPI.applyFilter( 'tc_get_item_id', cartContainer.attr( 'data-item_id' ), cartContainer );
			epoHolder = main_product.find( '.tm-extra-product-options.tm-cart-' + itemId );
			totalsHolderContainer = main_product.find( '.tm-totals-form-' + itemId );
			totalsHolder = main_product.find( '.tm-epo-totals.tm-cart-' + itemId );
			variationForm = cartContainer.find( '.variations_form' ).first();
		// Default product
		} else {
			if ( ! main_cart || main_cart.length === 0 ) {
				if ( this_epo_container.is( '.tc-shortcode' ) ) {
					main_cart = main_product;
				} else {
					main_cart = get_main_cart( main_product, main_product, 'form', product_id );
				}
			}
			cartContainer = main_cart.parent();
			epoHolder = this_epo_container;
			totalsHolderContainer = this_totals_container;
			totalsHolder = this_epo_totals_container;
			variationForm = epoObject.variations_form;
		}

		if ( epoObject.is_associated ) {
			itemId = epoHolder.attr( 'data-cart-id' );
		}

		currentCart = alternativeCart || main_cart;
		totalsHolder.data( 'tm_for_cart', currentCart );

		variation_id_selector = getVariationIdSelector( currentCart );
		qtyElement = getQtyElement( currentCart );

		totalsHolder.data( 'variationIdElement', getVariationIdElement( currentCart, '.wceb_picker_wrap ' + variation_id_selector ) );
		totalsHolder.data( 'qty_element', qtyElement );

		this_product_type = totalsHolder.data( 'type' );

		variationForm.data( 'tc_product_id', product_id );

		finalTotalBoxMode = totalsHolder.attr( 'data-tm-epo-final-total-box' );
		finalTotalBoxShowFinal = totalsHolder.attr( 'data-tm-epo-show-final-total' );
		finalTotalBoxShowOptions = totalsHolder.attr( 'data-tm-epo-show-options-total' );

		jWindow.on( 'epoCalculateRules', function( event, dataObject ) {
			if ( event && dataObject && dataObject.currentCart ) {
				tm_epo_rules( epoObject, dataObject.currentCart );
			}
		} );

		if ( currentCart.is( 'form' ) ) {
			currentCart.on( 'reset', function() {
				var form = $( this );
				setTimeout( function() {
					$( form.data( 'epo_id_selector' ) ).find( '.tm-epo-field' ).trigger( 'change' );
				}, 1 );
			} );
		}

		tm_epo_rules( epoObject, currentCart );

		epoFieldAll = epoHolder.find( '.tm-epo-field' );
		if ( ! epoObject.is_associated ) {
			epoFieldAll = epoFieldAll.not( tcAPI.epoSelector + ' ' + tcAPI.associatedEpoSelector + ' .tm-epo-field' );
		}

		epoFieldText = epoHolder.find( '.tm-epo-field.tmcp-textarea,.tm-epo-field.tmcp-textfield' );
		if ( ! epoObject.is_associated ) {
			epoFieldText = epoFieldText.not( tcAPI.epoSelector + ' ' + tcAPI.associatedEpoSelector + ' .tm-epo-field' );
		}

		epoResetRadio = epoHolder.find( '.tm-epo-reset-radio' );
		if ( ! epoObject.is_associated ) {
			epoResetRadio = epoResetRadio.not( tcAPI.epoSelector + ' ' + tcAPI.associatedEpoSelector + ' .tm-epo-reset-radio' );
		}

		epoFieldUpload = epoHolder.find( '.tm-epo-field.tmcp-upload' );
		if ( ! epoObject.is_associated ) {
			epoFieldUpload = epoFieldUpload.not( tcAPI.epoSelector + ' ' + tcAPI.associatedEpoSelector + ' .tm-epo-field.tmcp-upload' );
		}

		epoFieldHasClearButton = epoHolder.find( '.tm-has-clearbutton .tm-epo-field' );
		if ( ! epoObject.is_associated ) {
			epoFieldHasClearButton = epoFieldHasClearButton.not( tcAPI.epoSelector + ' ' + tcAPI.associatedEpoSelector + ' .tm-has-clearbutton .tm-epo-field' );
		}

		tmQty = epoHolder.find( '.tm-quantity .tm-qty' );
		if ( ! epoObject.is_associated ) {
			tmQty = tmQty.not( tcAPI.epoSelector + ' ' + tcAPI.associatedEpoSelector + ' .tm-quantity .tm-qty' );
		}

		tmQuantity = epoHolder.find( '.tm-quantity' );
		if ( ! epoObject.is_associated ) {
			tmQuantity = tmQuantity.not( tcAPI.epoSelector + ' ' + tcAPI.associatedEpoSelector + ' .tm-quantity' );
		}

		selectSelector = epoHolder.find( 'select.tm-epo-field' );
		if ( ! epoObject.is_associated ) {
			selectSelector = selectSelector.not( tcAPI.epoSelector + ' ' + tcAPI.associatedEpoSelector + ' select.tm-epo-field' );
		}
		// update price amount for select elements
		selectSelector
			.off( 'tm-select-change-html' )
			.on( 'tm-select-change-html', function() {
				var field = $( this );
				var e_tip;
				var e_description;
				var sign;
				var formatted_price;

				if ( field.is( '.tc-epo-field-product' ) || field.is( '.tmcp-selectmultiple' ) ) {
					return;
				}

				if ( main_cart && main_cart.data( 'per_product_pricing' ) !== undefined && ! main_cart.data( 'per_product_pricing' ) ) {
					return;
				}

				formatted_price = tm_set_price( field.find( 'option:selected' ).data( 'price' ), totalsHolder, true, false, field );
				e_tip = field.closest( '.tmcp-field-wrap' ).find( '.tc-tooltip' );
				e_description = field.closest( '.tmcp-field-wrap' ).find( '.tc-inline-description' );

				// Prices are already taxed
				tm_update_price( {
					obj: field.closest( '.tmcp-field-wrap' ).find( '.tc-price' ),
					price: field.find( 'option:selected' ).data( 'price' ),
					original_price: field.find( 'option:selected' ).data( 'original_price' ),
					force: false,
					useFormattedPrice: false
				} );

				if ( e_tip.length > 0 ) {
					e_tip.attr( 'data-tm-tooltip-html', field.find( 'option:selected' ).attr( 'data-tm-tooltip-html' ) ).trigger( 'tc-tooltip-html-changed' );
				}

				if ( e_description.length > 0 ) {
					if ( field.find( 'option:selected' ).attr( 'data-tm-tooltip-html' ) ) {
						e_description.html( field.find( 'option:selected' ).attr( 'data-tm-tooltip-html' ) );
					} else {
						e_description.html( '' );
					}
				}

				if (
					( field.find( 'option:selected' ).attr( 'data-hide-amount' ) === '0' || TMEPOJS.tm_epo_show_price_inside_option_hidden_even === 'yes' ) &&
					TMEPOJS.tm_epo_show_price_inside_option === 'yes' &&
					field.find( 'option:selected' ).attr( 'data-text' ) &&
					( field.find( 'option:selected' ).data( 'price' ) || ( TMEPOJS.tm_epo_no_hide_price_if_original_not_zero === 'yes' && field.find( 'option:selected' ).data( 'original_price' ) ) )
				) {
					if (
						( TMEPOJS.tm_epo_auto_hide_price_if_zero === 'yes' && $.tmempty( field.find( 'option:selected' ).data( 'price' ) ) === false ) ||
						( TMEPOJS.tm_epo_auto_hide_price_if_zero !== 'yes' && field.find( 'option:selected' ).attr( 'data-price' ) !== '' )
					) {
						sign = '';
						field.find( 'option:selected' ).html( field.find( 'option:selected' ).attr( 'data-text' ) + ' (' + sign + formatted_price + ')' );
					} else {
						field.find( 'option:selected' ).html( field.find( 'option:selected' ).attr( 'data-text' ) );
					}
				}

				if ( field.val() === '' ) {
					e_tip.addClass( 'tm-hidden' );
				} else if ( field.find( 'option:selected' ).attr( 'data-tm-tooltip-html' ) ) {
					e_tip.removeClass( 'tm-hidden' );
				} else {
					e_tip.addClass( 'tm-hidden' );
				}
			} )
			.off( 'tm-math-select-change-html-all tm-select-change-html-all tm-select-change-html-all-math' )
			.on( 'tm-math-select-change-html-all tm-select-change-html-all tm-select-change-html-all-math', function( event ) {
				var field = $( this );
				var e_tip;
				var e_description;
				var thisoption;
				var divider;
				var thisformatted_price;

				if ( field.is( '.tc-epo-field-product' ) ) {
					return;
				}

				e_tip = field.closest( '.tmcp-field-wrap' ).find( '.tc-tooltip' );
				e_description = field.closest( '.tmcp-field-wrap' ).find( '.tc-inline-description' );

				if ( e_tip.length > 0 ) {
					e_tip.attr( 'data-tm-tooltip-html', field.find( 'option:selected' ).attr( 'data-tm-tooltip-html' ) ).trigger( 'tc-tooltip-html-changed' );
				}

				if ( field.val() === '' ) {
					e_tip.addClass( 'tm-hidden' );
				} else if ( field.find( 'option:selected' ).attr( 'data-tm-tooltip-html' ) ) {
					e_tip.removeClass( 'tm-hidden' );
				} else {
					e_tip.addClass( 'tm-hidden' );
				}

				if ( e_description.length > 0 ) {
					if ( field.find( 'option:selected' ).attr( 'data-tm-tooltip-html' ) ) {
						e_description.html( field.find( 'option:selected' ).attr( 'data-tm-tooltip-html' ) );
					} else {
						e_description.html( '' );
					}
				}

				if ( main_cart && main_cart.data( 'per_product_pricing' ) !== undefined && ! main_cart.data( 'per_product_pricing' ) ) {
					return;
				}

				if ( TMEPOJS.tm_epo_show_price_inside_option === 'yes' ) {
					field.find( 'option' ).each( function() {
						thisoption = $( this );
						if ( ! thisoption.val() ) {
							return true;
						}

						if ( event.type === 'tm-select-change-html-all' ) {
							thisoption.removeClass( 'tm-epo-late-field' ).removeData( 'tm-price-for-late islate' );
							tm_element_epo_rules( epoObject, field, undefined, thisoption, 1 );
						} else if ( event.type === 'tm-math-select-change-html-all' ) {
							tm_element_epo_rules( epoObject, field, undefined, thisoption, 1, true );
						} else if ( event.type === 'tm-select-change-html-all-math' ) {
							thisoption.removeClass( 'tm-epo-late-field' ).removeData( 'tm-price-for-late islate' );
							tm_element_epo_rules( epoObject, field, undefined, thisoption, 1, true );
						}
						divider = 1;

						if ( TMEPOJS.tm_epo_multiply_price_inside_option !== 'yes' ) {
							divider = field.data( 'tm-quantity' );
						}

						if ( ! divider ) {
							divider = 1;
						}

						if ( ( TMEPOJS.tm_epo_auto_hide_price_if_zero === 'yes' && $.tmempty( thisoption.data( 'price' ) ) === false ) || ( TMEPOJS.tm_epo_auto_hide_price_if_zero !== 'yes' && thisoption.attr( 'data-price' ) !== '' ) ) {
							thisformatted_price = tm_set_price( thisoption.data( 'price' ) / divider, totalsHolder, true, false, field );

							if ( ( thisoption.attr( 'data-hide-amount' ) === '0' || TMEPOJS.tm_epo_show_price_inside_option_hidden_even === 'yes' ) && thisoption.attr( 'data-text' ) ) {
								thisoption.html( thisoption.attr( 'data-text' ) + ' (' + thisformatted_price + ')' );
							}
						} else {
							thisoption.html( thisoption.attr( 'data-text' ) );
						}
					} );
				}
			} )
			.off( 'tm-select-price-update-html-all' )
			.on( 'tm-select-price-update-html-all', function() {
				var field = $( this );
				var thisoption;
				var divider;
				var thisformatted_price;

				if ( field.is( '.tc-epo-field-product' ) ) {
					return;
				}

				if ( main_cart && main_cart.data( 'per_product_pricing' ) !== undefined && ! main_cart.data( 'per_product_pricing' ) ) {
					return;
				}

				if ( TMEPOJS.tm_epo_show_price_inside_option === 'yes' ) {
					field.find( 'option' ).each( function() {
						thisoption = $( this );
						if ( ! thisoption.val() ) {
							return true;
						}

						divider = 1;

						if ( TMEPOJS.tm_epo_multiply_price_inside_option !== 'yes' ) {
							divider = field.data( 'tm-quantity' );
						}

						if ( ! divider ) {
							divider = 1;
						}

						if ( ( TMEPOJS.tm_epo_auto_hide_price_if_zero === 'yes' && $.tmempty( thisoption.data( 'price' ) ) === false ) || ( TMEPOJS.tm_epo_auto_hide_price_if_zero !== 'yes' && thisoption.attr( 'data-price' ) !== '' ) ) {
							thisformatted_price = tm_set_price( thisoption.data( 'price' ) / divider, totalsHolder, true, false, field );

							if ( ( thisoption.attr( 'data-hide-amount' ) === '0' || TMEPOJS.tm_epo_show_price_inside_option_hidden_even === 'yes' ) && thisoption.attr( 'data-text' ) ) {
								thisoption.html( thisoption.attr( 'data-text' ) + ' (' + thisformatted_price + ')' );
							}
						} else {
							thisoption.html( thisoption.attr( 'data-text' ) );
						}
					} );
				}
			} )
			.off( 'tm-select-change' )
			.on( 'tm-select-change', function() {
				var field = $( this );
				var thisElementId = field.closest( '.cpf-element' ).attr( 'data-uniqid' );

				if ( field.is( '.tc-epo-field-product' ) ) {
					return;
				}

				if ( main_cart && main_cart.data( 'per_product_pricing' ) !== undefined && ! main_cart.data( 'per_product_pricing' ) ) {
					return;
				}

				field.removeData( 'addedtcEpoBeforeOptionPriceCalculation' );
				field.removeData( 'addedfieldtcEpoBeforeOptionPriceCalculation' );
				jWindow.off( 'tcEpoBeforeOptionPriceCalculation.math' + thisElementId );
				jWindow.off( 'tcEpoAfterNormalOptionPriceCalculation.math' + thisElementId );

				field.trigger( 'tm-select-change-html' );
				field.trigger( 'tm-select-change-html-all' );

				currentCart.trigger( {
					type: 'tm-epo-update',
					norules: 1,
					element: field
				} );
			} ).trigger( 'tm-select-change-html-all' );

		// Element quantity selector
		tmQty
			.off( 'focus.cpf' )
			.on( 'focus.cpf', function() {
				var qtyField = $( this );
				var field = qtyField.closest( '.tmcp-field-wrap' ).find( '.tm-epo-field' );
				var currentVal = parseFloat( qtyField.val() );
				var max = parseFloat( qtyField.attr( 'max' ) );
				var min = parseFloat( qtyField.attr( 'min' ) );
				var step = qtyField.attr( 'step' );
				var check1 = tm_limit_c_selection( field, false );
				var check2 = tm_exact_c_selection( field, false );
				var check3 = true;

				// Format values
				if ( ! currentVal || currentVal === '' || currentVal === 'NaN' ) {
					currentVal = 0;
				}
				if ( max === '' || max === 'NaN' ) {
					max = '';
				}
				if ( min === '' || min === 'NaN' ) {
					min = 0;
				}
				if ( step === 'any' || step === '' || step === undefined || parseFloat( step ) === 'NaN' ) {
					step = 1;
				}

				if ( currentVal < min || currentVal > max ) {
					check3 = false;
				}

				if ( check1 && check2 && check3 ) {
					qtyField.data( 'tm-prev-value', currentVal );
				} else {
					qtyField.data( 'tm-prev-value', min );
				}
			} )
			.off( 'change.cpf' )
			.on( 'change.cpf', function( event, data ) {
				var qtyField = $( this );
				var field = qtyField.closest( '.tmcp-field-wrap' ).find( '.tm-epo-field' );
				var currentVal = parseFloat( qtyField.val() );
				var max = parseFloat( qtyField.attr( 'max' ) );
				var min = parseFloat( qtyField.attr( 'min' ) );
				var step = qtyField.attr( 'step' );
				var check1 = tm_limit_c_selection( field, false );
				var check2 = tm_exact_c_selection( field, false );
				var check3 = true;

				// Format values
				if ( ! currentVal || currentVal === '' || currentVal === 'NaN' ) {
					currentVal = 0;
				}
				if ( max === '' || max === 'NaN' ) {
					max = '';
				}
				if ( min === '' || min === 'NaN' ) {
					min = 0;
				}
				if ( step === 'any' || step === '' || step === undefined || parseFloat( step ) === 'NaN' ) {
					step = 1;
				}

				if ( currentVal < min || currentVal > max ) {
					check3 = false;
				}

				if ( check1 && check2 && check3 ) {
					if ( ! epoObject.noEpoUpdate ) {
						field.data( 'tm-quantity', qtyField.val() ).trigger( 'change', data );
					} else {
						field.data( 'tm-quantity', qtyField.val() ).trigger( 'change.cpf', data ).trigger( 'change.cpfproduct', data );
					}
					field.trigger( 'tm-select-change-html-all' );
				} else if ( qtyField.data( 'tm-prev-value' ) ) {
					qtyField.val( qtyField.data( 'tm-prev-value' ) );
				} else {
					qtyField.val( min );
				}

				qtyField.trigger( 'cpf-changed' );
			} )
			.off( 'tmaddquantity' )
			.on( 'tmaddquantity', function() {
				var qtyField = $( this );
				var field = qtyField.closest( '.tmcp-field-wrap' ).find( '.tm-epo-field' );

				field.data( 'tm-quantity', qtyField.val() );
			} );

		// Insert characters remaining for text-areas and text-fields
		thismaxlength = epoHolder.find( '.tmcp-textfield.tm-epo-field[maxlength],textarea.tm-epo-field[maxlength]' );
		if ( ! epoObject.is_associated ) {
			thismaxlength = thismaxlength.not( tcAPI.epoSelector + ' ' + tcAPI.associatedEpoSelector + ' .tm-epo-field' );
		}

		thismaxlength.each( function() {
			var field = $( this );
			var innerWrap;
			var html;
			if ( field.data( 'tcmaxlength' ) ) {
				return;
			}
			field.data( 'tcmaxlength', 1 );
			innerWrap = field.closest( '.tmcp-field-wrap-inner' );
			html = $.epoAPI.template.html( tcAPI.templateEngine.tc_chars_remanining, {
				maxlength: field.attr( 'maxlength' ),
				characters_remaining: TMEPOJS.i18n_characters_remaining
			} );
			innerWrap.append( $( html ) );
		} );
		thismaxlength
			.off( 'change.tc_maxlen input.tc_maxlen' )
			.on( 'change.tc_maxlen input.tc_maxlen', function() {
				var field = $( this );

				field
					.closest( '.tmcp-field-wrap' )
					.find( '.tc-chars-remanining' )
					.html( parseInt( field.attr( 'maxlength' ), 10 ) - parseInt( field.val().length, 10 ) );
			} );

		// Change product image event
		epoFieldAll
			.off( 'tm_trigger_product_image' )
			.on( 'tm_trigger_product_image', function() {
				var field = $( this );
				var currentElement;
				var uic;
				var variation_element_section;
				var is_variation_element;
				var src;

				if ( field.is( '.tm-product-image:checkbox, .tm-product-image:radio, select.tm-product-image' ) ) {
					uic = field.closest( '.tmcp-field-wrap' ).find( 'label img' );
					variation_element_section = field.closest( '.cpf-section' );
					is_variation_element = variation_element_section.is( '.tm-epo-variation-section' );

					currentElement = field;
					if ( field.is( 'select.tm-product-image' ) ) {
						currentElement = field.children( 'option:selected' );
					}
					if (
						$( uic ).length > 0 ||
						( is_variation_element && currentElement.attr( 'data-image' ) !== undefined ) ||
						( currentElement.attr( 'data-image' ) !== undefined && currentElement.attr( 'data-image' ) !== '' ) ||
						( currentElement.attr( 'data-imagep' ) !== undefined && currentElement.attr( 'data-imagep' ) !== '' )
					) {
						if ( field.is( ':checked' ) || ( field.is( 'select.tm-product-image' ) && field.val() !== '' && ( field.find( 'option:selected' ).attr( 'data-rules' ) !== '' || field.is( '.tm-epo-variation-element' ) ) ) ) {
							src = $( uic ).first().attr( 'data-original' );

							if ( ! src && ! is_variation_element ) {
								src = $( uic ).first().attr( 'src' );
							}
							if ( ! src ) {
								src = currentElement.attr( 'data-image' );
							}
							if ( currentElement.attr( 'data-imagep' ) ) {
								src = currentElement.attr( 'data-imagep' );
							}
							if ( src ) {
								main_product.trigger( 'tm_change_product_image', {
									src: src,
									element: field,
									element_current: currentElement,
									main_product: main_product,
									epo_holder: epoHolder
								} );
							} else {
								main_product.trigger( 'tm_change_product_image', {
									src: false,
									element: field,
									element_current: currentElement,
									main_product: main_product,
									epo_holder: epoHolder
								} );
							}
						} else {
							main_product.trigger( 'tm_restore_product_image', {
								element: field,
								element_current: currentElement,
								main_product: main_product,
								epo_holder: epoHolder
							} );
						}
					} else {
						main_product.trigger( 'tm_restore_product_image', {
							element: field,
							element_current: currentElement,
							main_product: main_product,
							epo_holder: epoHolder
						} );
					}
				} else {
					main_product.trigger( 'tm_attempt_product_image', {
						element: field,
						element_current: currentElement,
						main_product: main_product,
						epo_holder: epoHolder
					} );
				}
			} );

		tmQuantity
			.off( 'showhide.cpfcustom' )
			.on( 'showhide.cpfcustom', function() {
				var quantity_selector = $( this );
				var field = quantity_selector.closest( '.tmcp-field-wrap' ).find( '.tm-epo-field' );
				var show = false;
				var tmqty;
				var tmqtyval;
				var tmqtymin;
				var radios;

				if ( ! field.is( '.tm-epo-variation-element' ) ) {
					if ( field.is( 'select' ) ) {
						if ( field.val() !== '' ) {
							show = true;
						}
					} else if ( field.is( ':checkbox' ) ) {
						if ( field.is( ':checked' ) ) {
							show = true;
						}
					} else if ( field.is( ':radio' ) ) {
						if ( field.is( ':checked' ) ) {
							show = true;
							if ( TMEPOJS.tm_epo_show_only_active_quantities === 'yes' ) {
								radios = field.closest( '.tc-element-inner-wrap' ).find( '.tm-epo-field.tmcp-radio' );
								radios.each( function() {
									$( this ).closest( '.tmcp-field-wrap' ).find( '.tm-quantity' ).hide();
								} );
							}
						}
					} else if ( field.val() ) {
						show = true;
					}

					tmqty = quantity_selector.find( '.tm-qty' );
					tmqtyval = tmqty.val();
					tmqtymin = tmqty.attr( 'min' ) || '';

					if ( show ) {
						if ( TMEPOJS.tm_epo_show_only_active_quantities === 'yes' ) {
							quantity_selector.show();
						}

						tmqty.removeClass( 'ignore' ).prop( 'disabled', false );
					} else {
						if ( TMEPOJS.tm_epo_show_only_active_quantities === 'yes' ) {
							quantity_selector.hide();
							if ( ! tmqtyval ) {
								tmqty.val( tmqtymin );
							}
						}

						tmqty.addClass( 'ignore' ).prop( 'disabled', true );
					}

					if ( epoHolder.is( '.tc-show' ) ) {
						setTimeout( function() {
							quantity_selector.closest( '.tcowl-carousel' ).trigger( 'refresh.owl.carousel' );
						}, 200 );
					}
				}
			} );

		epoFieldAll
			.off( 'change.cpfcustom' )
			.on( 'change.cpfcustom', function() {
				$( this ).closest( '.tmcp-field-wrap' ).find( '.tm-quantity' ).trigger( 'showhide.cpfcustom' );
			} );

		epoFieldAll
			.off( 'change.cpf' )
			.on( 'change.cpf', function( event, data ) {
				var field = $( this );
				var is_li = field.closest( '.tmcp-field-wrap' );
				var is_ul = field.closest( '.tmcp-ul-wrap' );
				var is_replace;
				var connector = is_ul.attr( 'data-tm-connector' );

				if ( field.is( ':checkbox, :radio' ) ) {
					if ( field.is( ':radio' ) ) {
						if ( ! data ) {
							if ( connector !== undefined ) {
								$( '[data-tm-connector="' + connector + '"]' ).find( '.tmcp-field-wrap' ).removeClass( 'tc-active' ).find( '.tm-epo-reset-radio' ).addClass( 'tm-hidden' );
							} else {
								is_ul.find( '.tmcp-field-wrap' ).removeClass( 'tc-active' );
							}
						}
						if ( connector !== undefined ) {
							$( '[data-tm-connector="' + connector + '"]' ).find( '.tmcp-field-wrap' ).removeClass( 'tc-active' ).find( '.tm-epo-reset-radio' ).addClass( 'tm-hidden' );
						}
					}
					if ( field.is( ':checked' ) ) {
						is_li.addClass( 'tc-active' );
					} else {
						is_li.removeClass( 'tc-active' );
					}
				}

				if ( ! field.is( '.tm-epo-variation-element' ) ) {
					if ( field.is( '.use_images:checkbox, .use_images:radio' ) && field.attr( 'data-imagec' ) ) {
						is_replace = is_li.find( '.radio-image,.checkbox-image' ).first();
						if ( is_replace.length > 0 ) {
							if ( field.is( ':checked' ) ) {
								is_replace.prop( 'src', field.attr( 'data-imagec' ) );
							} else {
								is_replace.prop( 'src', field.attr( 'data-image' ) );
							}
						}
					}

					if ( field.is( '.use_images:radio' ) && ! data ) {
						field
							.closest( '.cpf-type-radio' )
							.find( '.use_images:radio' )
							.not( field )
							.each( function() {
								var r = $( this );
								r.closest( '.tmcp-field-wrap' ).find( '.radio-image' ).first().prop( 'src', r.attr( 'data-image' ) );
							} );
					}

					if ( field.is( '.tmcp-range' ) ) {
						field.trigger( 'change.cpflogic' );
					}
					if ( field.is( 'select' ) ) {
						field.trigger( 'tm-select-change' );
					} else {
						if ( field.is( '.tmcp-radio' ) ) {
							field
								.closest( '.cpf-element' )
								.find( '.tm-quantity .tm-qty' )
								.each( function() {
									if ( ! $( this ).closest( 'li.tmcp-field-wrap' ).find( '.tmcp-radio' ).is( ':checked' ) ) {
										$( this ).attr( 'disabled', 'disabled' );
									} else {
										$( this ).prop( 'disabled', false );
									}
								} );
						}
						priceCache = true;
						currentCart.trigger( {
							type: 'tm-epo-update',
							norules: 1,
							element: field
						} );
						priceCache = false;
					}
				}

				field.trigger( 'tm_trigger_product_image' );

				if ( epoHolder.is( '.tc-show' ) ) {
					setTimeout( function() {
						$( '.tm-owl-slider' ).each( function() {
							$( this ).trigger( 'refresh.owl.carousel' );
						} );
					}, 200 );
				}
				main_product.trigger( 'tm_attempt_product_image', {
					element: field,
					main_product: main_product,
					epo_holder: epoHolder
				} );
			} );

		epoFieldAll.filter( '.tm-epo-field:text,input.tm-epo-field[type="number"],textarea.tm-epo-field' )
			.off( 'input.cpf' )
			.on( 'input.cpf', function() {
				$( this ).trigger( 'change.cpf', { autoload: 1 } );
			} );

		epoFieldAll
			.filter( ':checkbox:checked, :radio:checked' )
			.each( function() {
				$( this ).closest( '.tmcp-field-wrap' ).addClass( 'tc-active' );
				$( this ).trigger( 'change.cpf', { autoload: 1 } );
			} );

		epoFieldHasClearButton
			.off( 'change.cpfclearbutton' )
			.on( 'change.cpfclearbutton cpfclearbutton', function() {
				var field = $( this );
				var radioResetElement;
				var fieldWrap = field.closest( '.tmcp-field-wrap' );
				var fieldSearch;

				if ( field.is( ':checked' ) ) {
					fieldSearch = field.closest( '.tc-element-inner-wrap' );

					if ( fieldSearch.find( '.tc-epo-element-product-li-container' ).length ) {
						fieldSearch = fieldSearch.find( '.tmcp-ul-wrap' ).first().children( '.tmcp-field-wrap' ).not( '.tc-epo-element-product-li-container' );
					}

					radioResetElement = fieldSearch.find( '.tm-epo-reset-radio' );
					if ( ! radioResetElement.length ) {
						radioResetElement = field.closest( '.tc-element-inner-wrap' ).find( '.tm-epo-reset-radio' ).first();
					}
					radioResetElement.removeClass( 'tm-hidden' );

					fieldWrap.append( radioResetElement );
				}
			} );

		epoResetRadio
			.off( 'click.cpf' )
			.on( 'click.cpf', function() {
				var radioResetElement = $( this );
				var fieldContainer = radioResetElement.closest( '.tc-element-inner-wrap' );
				var checkedRadios = fieldContainer.find( '.tm-epo-field.tmcp-radio:checked' );

				if ( checkedRadios.length ) {
					checkedRadios.prop( 'checked', false );
					checkedRadios.trigger( 'change', { forced: 1 } );
				}

				radioResetElement.addClass( 'tm-hidden' );
			} );

		if ( _ && _.debounce ) {
			epoFieldText.on( 'keyup',
				_.debounce( function() {
					var $this = $( this );

					if ( TMEPOJS.tm_epo_global_enable_validation === 'yes' && $.tc_validator && $this.closest( '.tmcp-ul-wrap' ).data( 'tm-validation' ) && $this.tc_rules() ) {
						currentCart.tc_validate().element( $this );
					}
					$this.trigger( 'change.cpf' );
					$this.closest( '.tmcp-field-wrap' ).find( '.tm-quantity' ).trigger( 'showhide.cpfcustom' );
				}, 10 )
			);
		}

		epoFieldUpload
			.off( 'change.cpfv tcupload' )
			.on( 'change.cpfv tcupload', function() {
				var field = $( this );
				var label = field.closest( 'label' );
				var li = field.closest( '.tmcp-field-wrap' );
				var cpfUploadContainer = li.find( '.cpf-upload-container' );
				var name = li.find( '.tm-filename' );
				var val = field.val().replace( 'C:\\fakepath\\', '' );
				var valHidden = [];
				var num_uploads;
				var windowURL = window.URL || window.webkitURL;
				var files;
				var image;
				var uploadPreview = li.find( '.tc-upload-preview' );
				var addImage;
				var uniqid;

				if ( cpfUploadContainer.length && name.length <= 0 ) {
					name = $( '<span class="tm-filename"></span>' );
					label.after( name );
				}
				if ( val === undefined || val === 'undefined' ) {
					val = '';
				}

				field.next( '.tmcp-upload-hidden' ).remove();

				valHidden = field.attr( 'data-file' );
				valHidden = valHidden ? valHidden.split( '|' ) : [];
				if ( this.files ) {
					files = Array.from( this.files );
				} else if ( valHidden.length > 0 ) {
					files = valHidden;
				}

				if ( files.length > 1 ) {
					name.html( '' + files.length + ' ' + ( files.length === 1 ? TMEPOJS.i18n_file : TMEPOJS.i18n_files ) );
				} else if ( files.length === 1 ) {
					name.html( val );
				} else {
					name.empty();
				}
				if ( ! uploadPreview.length ) {
					uploadPreview = $( '<div class="tc-upload-preview tc-hidden"></div>' );
					li.append( uploadPreview );
				}

				uploadPreview.empty();

				num_uploads = epoHolder.data( 'num_uploads' );
				if ( ! num_uploads ) {
					num_uploads = [];
				}
				uniqid = field.closest( '.cpf-element' ).attr( 'data-uniqid' );
				if ( ! num_uploads[ uniqid ] ) {
					num_uploads[ uniqid ] = [];
				}

				if ( ( val || files.length > 0 ) && windowURL && windowURL.createObjectURL ) {
					if ( TMEPOJS.tm_epo_upload_inline_image_preview === 'yes' ) {
						if ( files.length > 1 ) {
							uploadPreview.addClass( 'multiple' );
						} else {
							uploadPreview.removeClass( 'multiple' );
						}
						uploadPreview.removeClass( 'tc-hidden' );
					}
					addImage = function( dataFile, src, i, fileHolder, ext ) {
						var size = field[ 0 ].files && field[ 0 ].files[ i ] && field[ 0 ].files[ i ].size ? field[ 0 ].files[ i ].size : 0;
						var imageHTML = '<div class="tc-upload-image">';
						if ( src ) {
							imageHTML = imageHTML + '<img src="' + src + '">';
						} else {
							imageHTML = imageHTML + '<div class="tc-file-ext-overlay"></div>';
						}
						imageHTML = imageHTML + '<div class="tc-file-name">' + dataFile.name + '</div>';
						if ( ClipboardEvent || DataTransfer ) {
							imageHTML = imageHTML + '<button type="button" class="tc-upload-remove"><svg width="26" height="26" viewBox="0 0 26 26" xmlns="http://www.w3.org/2000/svg"><path d="M11.586 13l-2.293 2.293a1 1 0 0 0 1.414 1.414L13 14.414l2.293 2.293a1 1 0 0 0 1.414-1.414L14.414 13l2.293-2.293a1 1 0 0 0-1.414-1.414L13 11.586l-2.293-2.293a1 1 0 0 0-1.414 1.414L11.586 13z" fill="currentColor" fill-rule="nonzero"/></svg></button>';
						}
						if ( ext ) {
							imageHTML = imageHTML + '<div class="tc-file-ext">' + ext + '</div>';
						}
						imageHTML = imageHTML + '<div class="tc-file-size">' + readableFileSize( size ) + '</div>';
						imageHTML = imageHTML + '</div>';
						fileHolder.append( imageHTML );
					};

					files.forEach( function( dataFile, i ) {
						var fileHolder;
						if ( TMEPOJS.tm_epo_upload_inline_image_preview === 'yes' ) {
							fileHolder = $( '<div class="tc-upload-file"></div>' );
							uploadPreview.append( fileHolder );
							image = new Image();
							image.onload = function() {
								addImage( dataFile, this.src, i, fileHolder );
							};
							image.onerror = function() {
								addImage( dataFile, false, i, fileHolder, dataFile.name.split( '.' ).pop() );
							};
							if ( valHidden.length > 0 ) {
								image.src = valHidden[ i ];
							} else {
								image.src = windowURL.createObjectURL( dataFile );
								windowURL.revokeObjectURL( dataFile );
							}
						}
						num_uploads[ uniqid ].push( dataFile.name );
					} );
					epoHolder.data( 'num_uploads', num_uploads );
				} else {
					delete num_uploads[ uniqid ];
				}
				if ( num_uploads[ uniqid ] && num_uploads[ uniqid ].length === 0 ) {
					delete num_uploads[ uniqid ];
				}
			} );

		jDocument.on( 'click', '.tc-upload-remove', function() {
			var $this = $( this );
			var val;
			var cpfElement;
			var input;
			var fileWrap;
			var li;
			var uploadPreview;
			var name;
			var index;
			var files;
			var dT;
			var num_uploads;
			var uniqid;
			var uploadHidden;
			var dataFiles;

			if ( ClipboardEvent || DataTransfer ) {
				cpfElement = $this.closest( '.cpf-element' );
				input = cpfElement.find( '.tm-epo-field' );
				fileWrap = $this.closest( '.tc-upload-file' );
				li = $this.closest( '.tmcp-field-wrap' );
				uploadPreview = li.find( '.tc-upload-preview' );
				name = li.find( '.tm-filename' );
				index = fileWrap.index();
				val = input.val().replace( 'C:\\fakepath\\', '' );
				if ( val === undefined || val === 'undefined' ) {
					val = '';
				}

				files = Array.from( input[ 0 ].files );
				files.splice( index, 1 );
				dT = new ClipboardEvent( '' ).clipboardData || new DataTransfer();

				files.forEach( function( dataFile ) {
					dT.items.add( dataFile );
				} );

				if ( files.length > 1 ) {
					uploadPreview.addClass( 'multiple' );
				} else {
					uploadPreview.removeClass( 'multiple' );
				}

				input[ 0 ].files = dT.files;
				fileWrap.remove();
				if ( dT.files.length > 1 ) {
					name.html( '' + dT.files.length + ' ' + ( dT.files.length === 1 ? TMEPOJS.i18n_file : TMEPOJS.i18n_files ) );
				} else if ( dT.files.length === 1 ) {
					name.html( val );
				} else {
					name.empty();
				}

				num_uploads = epoHolder.data( 'num_uploads' );
				if ( ! num_uploads ) {
					num_uploads = [];
				}
				uniqid = cpfElement.closest( '.cpf-element' ).attr( 'data-uniqid' );
				if ( ! num_uploads[ uniqid ] ) {
					num_uploads[ uniqid ] = [];
				}

				num_uploads[ uniqid ].splice( index, 1 );
				if ( num_uploads[ uniqid ].length === 0 ) {
					delete num_uploads[ uniqid ];
					input.val( '' );
				}

				uploadHidden = input.next( '.tmcp-upload-hidden' );
				if ( uploadHidden.length ) {
					dataFiles = uploadHidden.val().split( '|' );
					if ( dataFiles.length > 0 ) {
						dataFiles.splice( index, 1 );
					}
					if ( dataFiles.length === 0 ) {
						uploadHidden.remove();
					} else {
						uploadHidden.val( dataFiles.join( '|' ) );
					}
				}

				epoHolder.data( 'num_uploads', num_uploads );
			}
		} );

		qtyElement
			.off( 'change.cpf input.cpf' )
			.on( 'change.cpf input.cpf', function() {
				var field = $( this );

				currentCart.trigger( 'tm-epo-check-dpd' );
				field.data( 'tm-prev-value', field.val() );
				// It is required than when you update the product quantity
				// to recalculate the option price to accommodate price types
				// that depend on quantity
				currentCart.trigger( {
					type: 'tm-epo-update',
					norules: 2
				} );
			} )
			.data( 'tm-prev-value', qtyElement.val() );

		// Support for math formula on associated products when they are initially hidden
		epoHolder
			.find( '.cpf-type-product' )
			.off( 'tc-logic' )
			.on( 'tc-logic', function() {
				var $this = $( this );
				if ( $this.find( '.tc-init' ).length ) {
					return;
				}
				if ( $this.data( 'isactive' ) && ! $this.data( 'donetclogic' ) ) {
					$this.data( 'donetclogic', true );
					$this.find( '.tc-epo-field-product' ).trigger( 'change.cpfproduct', { forced: 3 } );
				}
			} );

		// Product element checkbox required status fix
		epoHolder
			.find( '.cpf-type-product .tc-epo-field-product.tc-epo-field-product-checkbox' )
			.off( 'change.cpfrequired' )
			.on( 'change.cpfrequired', function() {
				var $this = $( this );
				var cpfElement = $this.closest( '.cpf-element' );
				var checkboxes;
				var checkboxesChecked;
				var checkboxesNotChecked;
				if ( cpfElement.is( '.tc-is-required' ) ) {
					checkboxes = cpfElement.find( '.tmcp-field.tc-epo-field-product-checkbox' );
					checkboxesNotChecked = checkboxes.not( ':checked' );
					checkboxesChecked = cpfElement.find( '.tmcp-field.tc-epo-field-product-checkbox:checked' );

					if ( checkboxesChecked.length > 0 ) {
						checkboxesNotChecked.prop( 'required', false );
						checkboxesChecked.prop( 'required', true );
					} else {
						checkboxes.attr( 'required', true );
						checkboxes.prop( 'required', true );
					}
				}
			} );

		// Product element variable product support
		epoHolder
			.find( '.cpf-type-product .tc-epo-field-product' )
			.off( 'change.cpfproduct' )
			.on( 'change.cpfproduct', function( e, data ) {
				var $this = $( this );
				var value;
				var type;
				var selected;
				var variableProductContainers;
				var thisVariableProductContainer;
				var elementContainer = $this.closest( '.cpf-element' );
				var productContainerWraps = elementContainer.find( '.tc-epo-element-product-container-wrap' );
				var thisProductContainerWrap = $this.closest( '.tmcp-field-wrap' ).find( '.tc-epo-element-product-container-wrap' );
				var hasProductContainerWrap = thisProductContainerWrap.length > 0;
				var postData;
				var skip = false;
				var isTrigger = 1000;
				var qtyalt;
				var associatedSetter = $this;
				var associatedElement;
				var isFilled;
				var productPrice;
				var originalProductPrice;
				var epoField = elementContainer.find( '.tm-epo-field' ).not( '.tc-epo-element-product-li-container .tm-epo-field' );
				var checked = epoField.filter( ':checked' );
				var qty;
				var counter = $this.attr( 'data-counter' );

				if ( false === elementContainer.data( 'isactive' ) ) {
					return;
				}
				if ( data && data.forced === 2 ) {
					return;
				}

				if ( e.isTrigger !== undefined && $this.data( 'triggeredonce' ) && ! ( data && data.forced ) ) {
					return;
				}

				if ( $this.data( 'triggeredforced' ) === 3 ) {
					return;
				}

				$this.data( 'triggeredonce', 1 );

				if ( ! $this.is( ':checkbox' ) ) {
					isTrigger = e.isTrigger;
				}

				if ( epoField.is( ':radio' ) ) {
					if ( checked.length > 0 ) {
						qty = checked.closest( '.tmcp-field-wrap' ).find( 'input.tm-qty' );
					}
				} else if ( epoField.is( ':checkbox' ) ) {
					if ( checked.length > 0 ) {
						qty = $this.closest( '.tmcp-field-wrap' ).find( 'input.tm-qty' );
					}
				} else {
					qty = epoField.closest( '.tmcp-field-wrap' ).find( 'input.tm-qty' );
				}

				if ( $this.is( ':checkbox' ) ) {
					if ( $this.is( '.tc-epo-field-product-hidden' ) ) {
						if ( $this.is( ':checked' ) ) {
							productContainerWraps.addClass( 'tc-active-product' );
						} else {
							productContainerWraps.removeClass( 'tc-active-product' );
						}

						value = $this.val();
						type = $this.attr( 'data-type' );
						elementContainer.find( '.tc-epo-element-product-li-container' ).removeClass( 'tm-hidden' );
					} else {
						if ( ! $this.is( ':checked' ) ) {
							if ( hasProductContainerWrap ) {
								thisProductContainerWrap.addClass( 'tm-hidden' );
							}
							if ( ! ( data && data.forced ) ) {
								return;
							}
							skip = true;
						} else if ( hasProductContainerWrap ) {
							thisProductContainerWrap.removeClass( 'tm-hidden' );
						}
						if ( ! skip ) {
							value = $this.val();
							type = $this.attr( 'data-type' );
							elementContainer.find( '.tc-epo-element-product-li-container' ).removeClass( 'tm-hidden' );
						}
					}
				} else if ( $this.is( ':radio' ) ) {
					if ( ! $this.is( ':checked' ) ) {
						if ( hasProductContainerWrap ) {
							thisProductContainerWrap.addClass( 'tm-hidden' );
						}
						if ( ! ( data && data.forced ) ) {
							return;
						}
						skip = true;
					} else if ( hasProductContainerWrap ) {
						productContainerWraps.addClass( 'tm-hidden' );
						thisProductContainerWrap.removeClass( 'tm-hidden' );
					}
					if ( ! skip ) {
						value = $this.val();
						type = $this.attr( 'data-type' );
						elementContainer.find( '.tc-epo-element-product-li-container' ).removeClass( 'tm-hidden' );
					}
				} else if ( $this.is( 'select' ) ) {
					selected = $this.children( ':selected' );
					associatedSetter = selected;
					counter = selected.index();
					if ( $this.children( ':first' ).val() === '' ) {
						counter--;
					}
					value = $this.val();
					type = selected.attr( 'data-type' );
					elementContainer.find( '.tc-epo-element-product-li-container' ).removeClass( 'tm-hidden' );
				}

				if ( $this.is( '.tc-epo-field-product-checkbox' ) ) {
					variableProductContainers = thisProductContainerWrap.find( '.tc-epo-element-product-container' );
				} else {
					variableProductContainers = elementContainer.find( '.tc-epo-element-product-container' );
				}

				thisVariableProductContainer = variableProductContainers.filter( '[data-product_id="' + value + '"]' );

				qtyalt = thisVariableProductContainer.find( tcAPI.associateQtySelector );

				if ( productContainerWraps.html() !== '' && qty && qty.length > 0 && qtyalt.length === 0 ) {
					qty.val( 0 );
				}
				if ( $this.is( ':checkbox' ) ) {
					if ( $this.is( '.tc-epo-field-product-hidden' ) ) {
						if ( $this.is( ':checked' ) ) {
							if ( qtyalt.val() === '0' ) {
								if ( qtyalt.attr( 'min' ) !== '0' ) {
									qtyalt.val( qtyalt.attr( 'min' ) ).trigger( 'change' );
								}
							}
						} else {
							qtyalt.val( 0 );
							qtyalt.closest( '.tm-quantity-alt' ).removeClass( 'tm-hidden' );
							if ( data.forced !== 1 ) {
								qtyalt.closest( '.tm-quantity-alt' ).find( '.single_add_to_cart_product' ).trigger( 'cpfqtybutton' );
							}
							productContainerWraps.find( tcAPI.associatedEpoCart ).trigger( 'tm-epo-update' );
						}
					}
				} else if ( $this.is( ':radio' ) ) {
					if ( $this.val() && ! $this.data( 'set_initial' ) ) {
						if ( qtyalt.val() === '0' ) {
							if ( qtyalt.attr( 'min' ) !== '0' ) {
								qtyalt.val( qtyalt.attr( 'min' ) ).trigger( 'change' );
							}
							$this.data( 'set_initial', 1 );
						}
					}
				} else if ( $this.is( 'select' ) ) {
					if ( $this.val() && ! $this.data( 'set_initial' ) ) {
						if ( qtyalt.val() === '0' ) {
							if ( qtyalt.attr( 'min' ) !== '0' ) {
								qtyalt.val( qtyalt.attr( 'min' ) ).trigger( 'change' );
							}
							$this.data( 'set_initial', 1 );
						}
					}
				} else {
					return;
				}

				if ( ! value ) {
					variableProductContainers.addClass( 'tm-hidden' );
					productPrice = $.epoAPI.util.parseJSON( associatedSetter.attr( 'data-rules' ) );
					productPrice = productPrice[ 0 ] || 0;
					originalProductPrice = $.epoAPI.util.parseJSON( associatedSetter.attr( 'data-original-rules' ) );
					originalProductPrice = originalProductPrice || 0;
					if ( associatedSetter.is( '.tcenabled' ) ) {
						associatedSetter.data( 'associated_price_set', 1 );
					}
					associatedSetter.data( 'price_set', 1 );
					associatedSetter.data( 'raw_price', productPrice );
					associatedSetter.data( 'raw_original_price', originalProductPrice );
					associatedSetter.data( 'price', productPrice );
					associatedSetter.data( 'original_price', originalProductPrice );

					associatedElement = associatedSetter;
					if ( associatedElement.is( 'option' ) ) {
						associatedElement = associatedSetter.closest( 'select' );
					}
					isFilled = false;
					if ( associatedElement.is( '.tc-epo-field-product' ) ) {
						if ( associatedElement.is( 'select' ) ) {
							if ( associatedElement.val() !== '' ) {
								isFilled = true;
							}
						} else if ( ( associatedElement.is( ':checkbox' ) || associatedElement.is( ':radio' ) ) ) {
							if ( associatedElement.is( ':checked' ) ) {
								isFilled = true;
							}
						} else if ( associatedElement.val() !== '' ) {
							isFilled = true;
						}
					}
					tm_force_update_price(
						associatedSetter
							.closest( '.tmcp-field-wrap' )
							.find( '.tc-price' )
							.not( tcAPI.associatedEpoSelector + ' .tc-price' ),
						productPrice,
						isFilled ? tm_set_price_totals( productPrice ) : associatedSetter.data( 'price-html' ),
						originalProductPrice,
						isFilled
					);

					qtyElement.trigger( 'change.cpf' );

					return;
				}

				if ( thisVariableProductContainer.length === 0 ) {
					if ( data && data.forced === 3 ) {
						$this.data( 'triggeredforced', 3 );
					}
					// parent_id is used for customization purposes.
					postData = {
						action: 'wc_epo_get_associated_product_html',
						product_id: value,
						parent_id: epoHolder.attr( 'data-product-id' ),
						mode: elementContainer.attr( 'data-mode' ),
						layout_mode: elementContainer.attr( 'data-product-layout-mode' ),
						uniqid: elementContainer.attr( 'data-uniqid' ),
						name: $this.attr( 'name' ),
						counter: counter,
						quantity_min: elementContainer.attr( 'data-quantity-min' ),
						quantity_max: elementContainer.attr( 'data-quantity-max' ),
						priced_individually: elementContainer.attr( 'data-priced-individually' ),
						discount: elementContainer.attr( 'data-discount' ),
						discount_type: elementContainer.attr( 'data-discount-type' ),
						discount_exclude_addons: elementContainer.attr( 'data-discount-exclude-addons' ),
						show_image: elementContainer.attr( 'data-show-image' ),
						show_title: elementContainer.attr( 'data-show-title' ),
						show_title_link: elementContainer.attr( 'data-show-title-link' ),
						show_price: elementContainer.attr( 'data-show-price' ),
						show_description: elementContainer.attr( 'data-show-description' ),
						show_meta: elementContainer.attr( 'data-show-meta' ),
						disable_epo: elementContainer.attr( 'data-disable-epo' )
					};

					elementContainer.block( { message: null, overlayCSS: { background: '#fff', opacity: 0.6 } } );
					$.ajax( {
						url: TMEPOJS.ajax_url,
						type: 'POST',
						data: postData,
						dataType: 'json',
						success: function( response ) {
							if ( response.result === 200 ) {
								thisVariableProductContainer = $( response.html );
								if ( hasProductContainerWrap ) {
									$this.closest( '.tmcp-field-wrap' ).find( '.tc-epo-element-product-container-wrap' ).empty().append( thisVariableProductContainer );
								} else {
									elementContainer.find( '.tc-epo-element-product-container-wrap' ).append( thisVariableProductContainer );
								}
								show_product_html( epoObject, main_product, thisVariableProductContainer, type, $this, currentCart, variableProductContainers, isTrigger, qtyalt );
								thisVariableProductContainer.find( tcAPI.associateQtySelector ).trigger( 'change' );
								jWindow.trigger( 'tc_apply_validation' );
							}
						},
						complete: function() {
							elementContainer.unblock();
						}
					} );
				} else {
					show_product_html( epoObject, main_product, thisVariableProductContainer, type, $this, currentCart, variableProductContainers, isTrigger, qtyalt );
					thisVariableProductContainer.find( tcAPI.associateQtySelector ).trigger( 'change' );
				}
			} );

		jDocument
			.off( 'click.cpfqtybutton cpfqtybutton', '.cpf-type-product .single_add_to_cart_product' )
			.on( 'click.cpfqtybutton cpfqtybutton', '.cpf-type-product .single_add_to_cart_product', function() {
				var $this = $( this );
				var qtyalt = $this.closest( '.tm-quantity-alt' ).find( tcAPI.associateQtySelector );
				var isAdd = $this.is( '.alt' );
				var productContainerWraps = $this.closest( '.cpf-element' ).find( '.tc-epo-element-product-container-wrap' );
				var qtyMin;
				var qtyMax;

				if ( $this.data( 'inittriggeredonce' ) ) {
					qtyMin = $.epoAPI.math.toInt( qtyalt.attr( 'data-min' ) );
					qtyMax = $.epoAPI.math.toInt( qtyalt.attr( 'data-max' ) );
					if ( isAdd ) {
						productContainerWraps.addClass( 'tc-active-product' );
						qtyalt.attr( 'min', qtyMin );
						if ( qtyMax ) {
							qtyalt.attr( 'max', qtyMax );
						}
						if ( qtyalt.val() === '0' ) {
							if ( qtyalt.attr( 'min' ) === '0' ) {
								qtyalt.val( 1 ).trigger( 'change' );
							} else {
								qtyalt.val( qtyalt.attr( 'min' ) ).trigger( 'change' );
							}
						}
						$this.removeClass( 'alt' ).text( $this.attr( 'data-remove' ) );
					} else {
						productContainerWraps.removeClass( 'tc-active-product' );
						qtyalt.val( 0 ).trigger( 'change' );
						qtyalt.closest( '.tm-quantity-alt' ).removeClass( 'tm-hidden' );
						$this.addClass( 'alt' ).text( $this.attr( 'data-add' ) );
					}
				}

				$this.data( 'inittriggeredonce', 1 );

				productContainerWraps.find( tcAPI.associatedEpoCart ).trigger( 'tm-epo-update' );
			} );

		jDocument
			.off( 'change.cpfqtyalt input.cpfqtyalt', '.cpf-type-product ' + tcAPI.associateQtySelector )
			.on( 'change.cpfqtyalt input.cpfqtyalt', '.cpf-type-product ' + tcAPI.associateQtySelector, function() {
				var $this = $( this );
				var epoField = $this.closest( '.cpf-element' ).find( '.tm-epo-field' ).not( '.tc-epo-element-product-li-container .tm-epo-field' );
				var addButton = $this.closest( '.tm-quantity-alt' ).find( '.single_add_to_cart_product' );
				var checked = epoField.filter( ':checked' );
				var qty;
				var qtyMin;
				var qtyMax;
				var associatedEpoCart;

				qtyMin = $.epoAPI.math.toInt( $this.attr( 'min' ) );
				qtyMax = $.epoAPI.math.toInt( $this.attr( 'max' ) );

				if ( epoField.is( '.tc-epo-field-product-hidden' ) && epoField.is( ':checkbox' ) ) {
					if ( checked.length === 0 && $this.val() !== '0' ) {
						epoField.prop( 'checked', true ).trigger( 'change' );
						checked = epoField.filter( ':checked' );
					} else if ( $this.val() === '0' && checked.length ) {
						epoField.prop( 'checked', false ).trigger( 'change' );
					}
				}

				if ( epoField.is( ':radio' ) ) {
					if ( checked.length === 0 ) {
						return;
					}
					qty = checked.closest( '.tmcp-field-wrap' ).find( 'input.tm-qty' ).not( '.tc-element-qty' );
				} else if ( epoField.is( ':checkbox' ) ) {
					if ( checked.length === 0 ) {
						return;
					}
					qty = $this.closest( '.tmcp-field-wrap' ).find( 'input.tm-qty' ).not( '.tc-element-qty' );
				} else {
					qty = epoField.closest( '.tmcp-field-wrap' ).find( 'input.tm-qty' ).not( '.tc-element-qty' );
				}

				if ( qty.length === 0 ) {
					return;
				}

				qty.attr( 'min', qtyMin );
				if ( qtyMax ) {
					qty.attr( 'max', qtyMax );
				}
				associatedEpoCart = $this.closest( '.tc-epo-element-product-container' ).find( tcAPI.associatedEpoCart );
				associatedEpoCart.trigger( 'tm-epo-update' );

				qty.val( $this.val() );

				if ( $.epoAPI.math.toFloat( $this.val() ) > 0 ) {
					associatedEpoCart.find( '.tm-epo-field.tcenabled' ).removeClass( 'ignore' );
					$this.removeClass( 'ignore' );
				} else {
					associatedEpoCart.find( '.tm-epo-field.tcenabled' ).addClass( 'ignore' );
					$this.addClass( 'ignore' );
				}
				epoField.trigger( 'change.cpfproduct', { forced: 2 } );
				if ( addButton.data( 'inittriggeredonce' ) ) {
					if ( $this.val() === '0' ) {
						addButton.removeClass( 'alt' );
					} else {
						addButton.addClass( 'alt' );
					}
				}
				addButton.trigger( 'cpfqtybutton' );
			} );

		// Global update event
		currentCart.off( 'tm-epo-update' ).on( 'tm-epo-update', function( event ) {
			var cart = $( this );
			var bundleid;
			var productPrice = false;
			var rawProductPrice = 0;
			var productRegularPrice = false;
			var rawProductRegularPrice = 0;
			var total = 0;
			var original_total = 0;
			var showTotal = false;
			var cartQty;
			var elementQty = 1;
			var priceOverrideMode;
			var perProductPricing = true;
			var floatingBoxData = [];
			var currentVariation;
			var cart_fee_options_total = 0;
			var cart_fee_options_original_total = 0;
			var _total;
			var _original_total;
			var late_total_price;
			var tc_totals_ob = {};
			var formatted_options_total;
			var formatted_options_original_total;
			var formatted_fees_total;
			var formatted_fees_original_total;
			var formatted_final_total;
			var formatted_final_original_total;
			var extraFee = 0;
			var raw_extraFee = 0;
			var product_total_price;
			var product_total_original_price;
			var calculateFinalProductPrice;
			var total_plus_fee;
			var original_total_plus_fee;
			var product_total_price_without_options;
			var unit_price;
			var unit_original_price;
			var formatted_unit_price;
			var formatted_unit_original_price;
			var html;
			var show_options_total = false;
			var show_vat_options_total = false;
			var show_fees_total = false;
			var formatted_extra_fee = '';
			var show_extra_fee = false;
			var show_final_total = false;
			var hide_native_price;
			var update_native_html;
			var _fprice;
			var _f_regular_price;
			var customerPriceFormat;
			var currentEpoObject;
			var raw_total;
			var raw__total;
			var raw_original_total;
			var raw__original_total;
			var raw_cart_fee_options_total;
			var raw_cart_fee_options_original_total;
			var raw_total_plus_fee;
			var raw_original_total_plus_fee;
			var raw_product_total_price_without_options;
			var raw_product_total_price;
			var raw_product_total_original_price;
			var fetch;
			var customer_price_format_wrap_start = '';
			var customer_price_format_wrap_end = '';
			var associatedSetter;
			var associatedPrice;
			var associatedRawPrice;
			var associatedOriginalPrice;
			var associatedRawOriginalPrice;
			var associatedFormattedPrice;
			var nativeProductPriceSelector;
			var elementsLength;
			var productFieldSyncVatTotal = 0;
			var productFieldSyncTotal = 0;
			var productFieldSyncOriginalTotal = 0;
			var productFieldSyncTotalTaxed = 0;
			var productFieldSyncOriginalTotalTaxed = 0;
			var totalProductField = 0;
			var originalTotalProductField = 0;
			var totalProductFieldTaxed = 0;
			var originalTotalProductFieldTaxed = 0;
			var _ftotal;
			var _foriginal_total;
			var totalsHolder_tc_totals_ob;
			var temp;
			var vat_options_total;
			var vat_options_total_plus_fee;
			var formatted_vat_options_total;
			var formatted_vat_options_total_plus_fee;
			var vat_total;
			var formatted_vat_total;
			var vat_product_base;
			var formatted_vat_product_base;
			var pricesIncludeTax;
			var dynamicCheck;
			var dynamicProductPrice = 0;

			if ( event.epoObject ) {
				currentEpoObject = $.extend( true, {}, event.epoObject );
			} else {
				currentEpoObject = $.extend( true, {}, epoObject );
			}

			if ( ! currentEpoObject ) {
				return;
			}

			if ( currentEpoObject.noEpoUpdate ) {
				return;
			}

			jWindow.trigger( 'tcEpoUpdateStart', {
				epo: currentEpoObject,
				alternativeCart: currentEpoObject,
				this_product_type: this_product_type,
				cart: cart,
				totalsHolder: totalsHolder,
				fetchOptionPrices: fetchOptionPrices,
				epoHolder: epoHolder
			} );

			bundleid = $.epoAPI.applyFilter( 'tc_get_bundleid', cart.attr( 'data-product_id' ), cart, currentEpoObject );
			priceOverrideMode = totalsHolder.attr( 'data-price-override' );
			cartQty = getCurrentQty( cart );
			currentVariation = getCurrentVariation( cart );

			if ( currentEpoObject.associated_connect && currentEpoObject.associated_connect.length === 1 ) {
				cartQty = parseFloat(
					currentEpoObject.main_product
						.find( tcAPI.associateQtySelector )
						.not( tcAPI.associatedEpoSelector + ' ' + tcAPI.qtySelector )
						.last()
						.val()
				);
			}

			event.stopImmediatePropagation();

			productPrice = $.epoAPI.applyFilter( 'tcGetCurrentProductPrice', tm_calculate_product_price( totalsHolder, true ), currentCart, totalsHolder );

			rawProductPrice = productPrice;

			productRegularPrice = $.epoAPI.applyFilter( 'tcGetCurrentProductRegularPrice', tm_calculate_product_regular_price( totalsHolder, true ), currentCart, totalsHolder );

			rawProductRegularPrice = productRegularPrice;

			productPrice = $.epoAPI.applyFilter( 'tcCalculateCurrentProductPrice', productPrice, {
				epo: currentEpoObject,
				alternativeCart: alternativeCart,
				cart: cart,
				main_product: main_product
			} );

			if ( ! Number.isFinite( cartQty ) ) {
				if ( totalsHolder.attr( 'data-is-sold-individually' ) || getQtyElement( cart ).length === 0 ) {
					cartQty = 1;
				}
			}

			// needed for inital math calculation
			totalsHolder_tc_totals_ob = {
				qty: cartQty,
				product_price: rawProductPrice,
				original_product_price: rawProductRegularPrice
			};
			totalsHolder.data( 'totalsHolder_tc_totals_ob', totalsHolder_tc_totals_ob );

			if ( ! event.norules ) {
				tm_epo_rules( currentEpoObject, cart );
			} else if ( event.norules ) {
				if ( event.norules === 1 ) {
					tm_element_epo_rules( currentEpoObject, event.element );
				}
				$.tcepo.lateFieldsPrices[ epoEventId ] = [];

				epoHolder
					.find( '.tm-epo-late-field' )
					.toArray()
					.forEach( function( setter ) {
						setter = $( setter );
						setter.data( 'price', 0 );
						$.tcepo.lateFieldsPrices[ epoEventId ].push( {
							setter: setter,
							price: setter.data( 'tm-price-for-late' ),
							original_price: setter.data( 'tm-original-price-for-late' ),
							bundleid: bundleid,
							pricetype: get_type( currentEpoObject, setter, 'price_type' )
						} );
					} );
			}

			// Check for dynamic elements.
			dynamicCheck = epoHolder.find( '.tmcp-dynamic.dynamic-product-price:not(.tc-is-math-special, .tc-is-math-cumulative)' );
			if ( currentEpoObject.is_associated === false ) {
				dynamicCheck = dynamicCheck.not( tcAPI.associatedEpoSelector + ' ' + '.tmcp-dynamic.dynamic-product-price:not(.tc-is-math-special, .tc-is-math-cumulative)' );
			}
			dynamicCheck = dynamicCheck.filter( '.tcenabled' );
			if ( dynamicCheck.length > 0 ) {
				fetch = fetchOptionPrices( currentEpoObject, epoHolder, '.tmcp-dynamic.dynamic-product-price:not(.tc-is-math-special, .tc-is-math-cumulative)', 0, 0, floatingBoxData, showTotal, undefined, undefined, undefined, true );
				productPrice = fetch.total;
				dynamicProductPrice = productPrice;
				rawProductRegularPrice = fetch.original_total;
				totalsHolderContainer.find( '.cpf-dynamic-product-price' ).val( dynamicProductPrice );
				totalsHolder_tc_totals_ob.dynamic_product_price = dynamicProductPrice;
				totalsHolder.data( 'totalsHolder_tc_totals_ob', totalsHolder_tc_totals_ob );
			}

			if ( currentEpoObject.associated_connect && currentEpoObject.associated_connect.length === 1 ) {
				associatedSetter = currentEpoObject.associated_connect;
				if ( currentEpoObject.associated_connect.is( 'select' ) ) {
					associatedSetter = currentEpoObject.associated_connect.find( 'option:selected' );
				}
			}

			// No reason to continue if the product price is invalid
			if ( productPrice === false ) {
				totalsHolder.data( 'tm-floating-box-data', [] );
				totalsHolder.empty();
				if ( currentEpoObject.associated_connect && currentEpoObject.associated_connect.length === 1 ) {
					tm_force_update_price(
						associatedSetter
							.closest( '.tmcp-field-wrap' )
							.find( '.tc-price' )
							.not( tcAPI.associatedEpoSelector + ' .tc-price' ),
						0,
						'',
						0
					);
				}
				main_cart.trigger( 'tm-epo-short-update', {
					container: cartContainer
				} );
				return;
			}

			elementQty = $.epoAPI.applyFilter( 'tcAlterElementQty', elementQty, {
				epo: currentEpoObject,
				alternativeCart: alternativeCart,
				currentCart: currentCart,
				main_product: main_product
			} );

			if ( currentCart.data( 'per_product_pricing' ) !== undefined ) {
				perProductPricing = currentCart.data( 'per_product_pricing' );
			}

			perProductPricing = $.epoAPI.applyFilter( 'tcCalculatePerProductPricing', perProductPricing, {
				epo: currentEpoObject,
				alternativeCart: alternativeCart,
				cart: cart,
				main_product: main_product
			} );

			if ( main_epo_inside_form && TMEPOJS.tm_epo_totals_box_placement === 'woocommerce_before_add_to_cart_button' ) {
				if ( ( this_product_type === 'variable' || this_product_type === 'variable-subscription' ) && ! totalsHolder.data( 'moved_inside' ) ) {
					totalsHolder.data( 'moved_inside', 1 );
				}
			}

			jWindow.trigger( 'tcEpoBeforeOptionPriceCalculation', {
				epo: currentEpoObject,
				alternativeCart: currentEpoObject,
				this_product_type: this_product_type,
				cart: cart,
				totalsHolder: totalsHolder,
				fetchOptionPrices: fetchOptionPrices,
				epoHolder: epoHolder,
				total: total,
				original_total: original_total,
				floatingBoxData: floatingBoxData,
				showTotal: showTotal,
				cart_fee_options_total: cart_fee_options_total,
				cart_fee_options_original_total: cart_fee_options_original_total
			} );

			if ( ! currentEpoObject.associated_connect && ! event.noassociated ) {
				if ( TMEPOJS.tm_epo_global_product_element_quantity_sync === 'yes' && ! cart.is( $.tcAPI().associatedEpoCart ) ) {
					cart.find( $.tcAPI().associatedEpoCart ).filter( ':visible' ).toArray().forEach( function( acart ) {
						$( acart ).trigger( {
							type: 'tm-epo-update',
							noassociated: 1
						} );
					} );
				}
			}

			fetch = fetchOptionPrices( currentEpoObject, epoHolder, '.tmcp-field:not(.tmcp-dynamic,.tc-epo-field-product, .tc-is-math-special, .tc-is-math-cumulative)', total, original_total, floatingBoxData, showTotal );
			total = fetch.total;
			original_total = fetch.original_total;
			floatingBoxData = fetch.floatingBoxData;
			showTotal = fetch.showTotal;
			elementsLength = fetch.elementsLength;

			fetch = fetchOptionPrices( currentEpoObject, epoHolder, '.tc-epo-field-product', productFieldSyncTotal, productFieldSyncOriginalTotal, floatingBoxData, showTotal );
			productFieldSyncTotal = fetch.total;
			productFieldSyncOriginalTotal = fetch.original_total;
			productFieldSyncTotalTaxed = fetch.total_taxed;
			productFieldSyncOriginalTotalTaxed = fetch.original_total_taxed;
			productFieldSyncVatTotal = fetch.vat_total;
			floatingBoxData = fetch.floatingBoxData;
			showTotal = fetch.showTotal;
			elementsLength = elementsLength + fetch.elementsLength;

			totalsHolder.data( 'tm-floating-box-data', floatingBoxData );

			fetch = fetchOptionPrices( currentEpoObject, epoHolder, '.tmcp-fee-field', cart_fee_options_total, cart_fee_options_original_total, floatingBoxData, showTotal, false, true );
			cart_fee_options_total = fetch.total;
			cart_fee_options_original_total = fetch.original_total;
			floatingBoxData = fetch.floatingBoxData;
			showTotal = fetch.showTotal;
			elementsLength = elementsLength + fetch.elementsLength;

			jWindow.trigger( 'tcEpoAfterNormalOptionPriceCalculation', {
				epo: currentEpoObject,
				alternativeCart: currentEpoObject,
				this_product_type: this_product_type,
				cart: cart,
				totalsHolder: totalsHolder,
				fetchOptionPrices: fetchOptionPrices,
				epoHolder: epoHolder,
				total: total,
				original_total: original_total,
				floatingBoxData: floatingBoxData,
				showTotal: showTotal,
				cart_fee_options_total: cart_fee_options_total,
				cart_fee_options_original_total: cart_fee_options_original_total
			} );

			// Original price + options price type requires this here.
			_total = total;
			_original_total = original_total;

			late_total_price = add_late_fields_prices( currentEpoObject, parseFloat( productPrice ), parseFloat( rawProductRegularPrice ), parseFloat( _total + productFieldSyncTotal ), parseFloat( _original_total + productFieldSyncOriginalTotal ), bundleid, totalsHolder );

			fetch = fetchOptionPrices( currentEpoObject, epoHolder, '.tmcp-field.tc-is-math-special:not(.tmcp-dynamic)', total, original_total, floatingBoxData, showTotal, undefined, undefined, undefined, true );
			total = fetch.total;
			original_total = fetch.original_total;
			floatingBoxData = fetch.floatingBoxData;
			showTotal = fetch.showTotal;
			elementsLength = elementsLength + fetch.elementsLength;

			_total = total;
			_original_total = original_total;

			_total = _total + late_total_price.normal[ 0 ];
			_original_total = _original_total + late_total_price.normal[ 1 ];

			cart_fee_options_total = cart_fee_options_total + late_total_price.fees[ 0 ];
			cart_fee_options_original_total = cart_fee_options_original_total + late_total_price.fees[ 1 ];

			total = _total;
			original_total = _original_total;
			fetch = fetchOptionPrices( currentEpoObject, epoHolder, '.tmcp-field.tc-is-math-cumulative:not(.tmcp-dynamic)', total, original_total, floatingBoxData, showTotal, undefined, undefined, undefined, true, true );
			total = fetch.total;
			original_total = fetch.original_total;
			floatingBoxData = fetch.floatingBoxData;
			showTotal = fetch.showTotal;
			elementsLength = elementsLength + fetch.elementsLength;

			_total = total;
			_original_total = original_total;

			// Check for dynamic elements.
			dynamicCheck = epoHolder.find( '.tmcp-dynamic.dynamic-product-price.tc-is-math-special' );
			if ( currentEpoObject.is_associated === false ) {
				dynamicCheck = dynamicCheck.not( tcAPI.associatedEpoSelector + ' ' + '.tmcp-dynamic.dynamic-product-price.tc-is-math-special' );
			}
			dynamicCheck = dynamicCheck.filter( '.tcenabled' );
			if ( dynamicCheck.length > 0 ) {
				fetch = fetchOptionPrices( currentEpoObject, epoHolder, '.tmcp-field.tmcp-dynamic.dynamic-product-price.tc-is-math-special', dynamicProductPrice + total, dynamicProductPrice + original_total, floatingBoxData, showTotal, undefined, undefined, undefined, true );
				dynamicProductPrice = $.epoAPI.math.toFloat( fetch.total ) - $.epoAPI.math.toFloat( total );
				productPrice = dynamicProductPrice;
				rawProductRegularPrice = $.epoAPI.math.toFloat( fetch.original_total ) - $.epoAPI.math.toFloat( original_total );
				totalsHolderContainer.find( '.cpf-dynamic-product-price' ).val( dynamicProductPrice );
				totalsHolder_tc_totals_ob.dynamic_product_price = dynamicProductPrice;
				totalsHolder.data( 'totalsHolder_tc_totals_ob', totalsHolder_tc_totals_ob );
			}

			dynamicCheck = epoHolder.find( '.tmcp-dynamic.dynamic-product-price.tc-is-math-cumulative' );
			if ( currentEpoObject.is_associated === false ) {
				dynamicCheck = dynamicCheck.not( tcAPI.associatedEpoSelector + ' ' + '.tmcp-dynamic.dynamic-product-price.tc-is-math-cumulative' );
			}
			dynamicCheck = dynamicCheck.filter( '.tcenabled' );
			if ( dynamicCheck.length > 0 ) {
				fetch = fetchOptionPrices( currentEpoObject, epoHolder, '.tmcp-field.tmcp-dynamic.dynamic-product-price.tc-is-math-cumulative', dynamicProductPrice + total, dynamicProductPrice + original_total, floatingBoxData, showTotal, undefined, undefined, undefined, true );
				dynamicProductPrice = $.epoAPI.math.toFloat( fetch.total ) - $.epoAPI.math.toFloat( total );
				productPrice = dynamicProductPrice;
				rawProductRegularPrice = $.epoAPI.math.toFloat( fetch.original_total ) - $.epoAPI.math.toFloat( original_total );
				totalsHolderContainer.find( '.cpf-dynamic-product-price' ).val( dynamicProductPrice );
				totalsHolder_tc_totals_ob.dynamic_product_price = dynamicProductPrice;
				totalsHolder.data( 'totalsHolder_tc_totals_ob', totalsHolder_tc_totals_ob );
			}

			// Check if there are any dynamic product price elements
			dynamicCheck = epoHolder.find( '.tmcp-dynamic.dynamic-product-price' );
			if ( currentEpoObject.is_associated === false ) {
				dynamicCheck = dynamicCheck.not( tcAPI.associatedEpoSelector + ' ' + '.tmcp-dynamic.dynamic-product-price' );
			}
			dynamicCheck = dynamicCheck.filter( '.tcenabled' );
			if ( ! dynamicCheck.length > 0 ) {
				totalsHolderContainer.find( '.cpf-dynamic-product-price' ).val( '' );
			}

			// Dynamic Calculation
			dynamicProductPrice = 0;
			dynamicCheck = epoHolder.find( '.tmcp-dynamic.calculation:not(.tc-is-math-special, .tc-is-math-cumulative)' );
			if ( currentEpoObject.is_associated === false ) {
				dynamicCheck = dynamicCheck.not( tcAPI.associatedEpoSelector + ' ' + '.tmcp-dynamic.calculation:not(.tc-is-math-special, .tc-is-math-cumulative)' );
			}
			dynamicCheck = dynamicCheck.filter( '.tcenabled' );
			if ( dynamicCheck.length > 0 ) {
				dynamicCheck.toArray().forEach( function( setter ) {
					var f;
					var ulWrap;
					var setterVal;
					setter = $( setter );
					ulWrap = setter.closest( '.tmcp-ul-wrap' );
					f = fetchOptionPrices( epoObject, ulWrap, '.tmcp-dynamic.calculation:not(.tc-is-math-special, .tc-is-math-cumulative)', dynamicProductPrice, dynamicProductPrice, [], true, true );
					setterVal = f.total;
					if ( setter.is( '.result-as-price' ) ) {
						setterVal = tm_set_price_totals( setterVal, totalsHolder, true, false );
					}
					ulWrap.find( '.tc-result' ).html( setterVal );
				} );
			}

			dynamicCheck = epoHolder.find( '.tmcp-dynamic.calculation.tc-is-math-special' );
			if ( currentEpoObject.is_associated === false ) {
				dynamicCheck = dynamicCheck.not( tcAPI.associatedEpoSelector + ' ' + '.tmcp-dynamic.calculation.tc-is-math-special' );
			}
			dynamicCheck = dynamicCheck.filter( '.tcenabled' );
			if ( dynamicCheck.length > 0 ) {
				dynamicCheck.toArray().forEach( function( setter ) {
					var f;
					var ulWrap;
					var setterVal;
					setter = $( setter );
					ulWrap = setter.closest( '.tmcp-ul-wrap' );
					f = fetchOptionPrices( epoObject, ulWrap, '.tmcp-field.tmcp-dynamic.calculation.tc-is-math-special', dynamicProductPrice + total, dynamicProductPrice + original_total, [], true, true, undefined, undefined, true );
					setterVal = $.epoAPI.math.toFloat( f.total ) - $.epoAPI.math.toFloat( total );
					if ( setter.is( '.result-as-price' ) ) {
						setterVal = tm_set_price_totals( setterVal, totalsHolder, true, false );
					}
					ulWrap.find( '.tc-result' ).html( setterVal );
				} );
			}

			dynamicCheck = epoHolder.find( '.tmcp-dynamic.calculation.tc-is-math-cumulative' );
			if ( currentEpoObject.is_associated === false ) {
				dynamicCheck = dynamicCheck.not( tcAPI.associatedEpoSelector + ' ' + '.tmcp-dynamic.calculation.tc-is-math-cumulative' );
			}
			dynamicCheck = dynamicCheck.filter( '.tcenabled' );
			if ( dynamicCheck.length > 0 ) {
				dynamicCheck.toArray().forEach( function( setter ) {
					var f;
					var ulWrap;
					var setterVal;
					setter = $( setter );
					ulWrap = setter.closest( '.tmcp-ul-wrap' );
					f = fetchOptionPrices( epoObject, ulWrap, '.tmcp-field.tmcp-dynamic.calculation.tc-is-math-cumulative', dynamicProductPrice + total, dynamicProductPrice + original_total, [], true, true, undefined, undefined, true );
					setterVal = $.epoAPI.math.toFloat( f.total ) - $.epoAPI.math.toFloat( total );
					if ( setter.is( '.result-as-price' ) ) {
						setterVal = tm_set_price_totals( setterVal, totalsHolder, true, false );
					}
					ulWrap.find( '.tc-result' ).html( setterVal );
				} );
			}

			// Dynamic Weight
			dynamicProductPrice = 0;
			dynamicCheck = epoHolder.find( '.tmcp-dynamic.change-product-weight:not(.tc-is-math-special, .tc-is-math-cumulative)' );
			if ( currentEpoObject.is_associated === false ) {
				dynamicCheck = dynamicCheck.not( tcAPI.associatedEpoSelector + ' ' + '.tmcp-dynamic.change-product-weight:not(.tc-is-math-special, .tc-is-math-cumulative)' );
			}
			dynamicCheck = dynamicCheck.filter( '.tcenabled' );
			if ( dynamicCheck.length > 0 ) {
				dynamicCheck.toArray().forEach( function( setter ) {
					var f;
					var ulWrap;
					setter = $( setter );
					ulWrap = setter.closest( '.tmcp-ul-wrap' );
					f = fetchOptionPrices( epoObject, ulWrap, '.tmcp-dynamic.change-product-weight:not(.tc-is-math-special, .tc-is-math-cumulative)', dynamicProductPrice, dynamicProductPrice, [], true, true );
					ulWrap.find( '.tc-result' ).html( f.total );
				} );
			}

			dynamicCheck = epoHolder.find( '.tmcp-dynamic.change-product-weight.tc-is-math-special' );
			if ( currentEpoObject.is_associated === false ) {
				dynamicCheck = dynamicCheck.not( tcAPI.associatedEpoSelector + ' ' + '.tmcp-dynamic.change-product-weight.tc-is-math-special' );
			}
			dynamicCheck = dynamicCheck.filter( '.tcenabled' );
			if ( dynamicCheck.length > 0 ) {
				dynamicCheck.toArray().forEach( function( setter ) {
					var f;
					var ulWrap;
					setter = $( setter );
					ulWrap = setter.closest( '.tmcp-ul-wrap' );
					f = fetchOptionPrices( epoObject, ulWrap, '.tmcp-field.tmcp-dynamic.change-product-weight.tc-is-math-special', dynamicProductPrice + total, dynamicProductPrice + original_total, [], true, true, undefined, undefined, true );
					ulWrap.find( '.tc-result' ).html( $.epoAPI.math.toFloat( f.total ) - $.epoAPI.math.toFloat( total ) );
				} );
			}

			dynamicCheck = epoHolder.find( '.tmcp-dynamic.change-product-weight.tc-is-math-cumulative' );
			if ( currentEpoObject.is_associated === false ) {
				dynamicCheck = dynamicCheck.not( tcAPI.associatedEpoSelector + ' ' + '.tmcp-dynamic.change-product-weight.tc-is-math-cumulative' );
			}
			dynamicCheck = dynamicCheck.filter( '.tcenabled' );
			if ( dynamicCheck.length > 0 ) {
				dynamicCheck.toArray().forEach( function( setter ) {
					var f;
					var ulWrap;
					setter = $( setter );
					ulWrap = setter.closest( '.tmcp-ul-wrap' );
					f = fetchOptionPrices( epoObject, ulWrap, '.tmcp-field.tmcp-dynamic.change-product-weight.tc-is-math-cumulative', dynamicProductPrice + total, dynamicProductPrice + original_total, [], true, true, undefined, undefined, true );
					ulWrap.find( '.tc-result' ).html( $.epoAPI.math.toFloat( f.total ) - $.epoAPI.math.toFloat( total ) );
				} );
			}

			// Check for dynamic override elements
			dynamicProductPrice = 0;
			dynamicCheck = epoHolder.find( '.tmcp-dynamic.override-product-price:not(.tc-is-math-special, .tc-is-math-cumulative)' );
			if ( currentEpoObject.is_associated === false ) {
				dynamicCheck = dynamicCheck.not( tcAPI.associatedEpoSelector + ' ' + '.tmcp-dynamic.override-product-price:not(.tc-is-math-special, .tc-is-math-cumulative)' );
			}
			dynamicCheck = dynamicCheck.filter( '.tcenabled' );
			if ( dynamicCheck.length > 0 ) {
				fetch = fetchOptionPrices( currentEpoObject, epoHolder, '.tmcp-dynamic.override-product-price:not(.tc-is-math-special, .tc-is-math-cumulative)', dynamicProductPrice, dynamicProductPrice, floatingBoxData, showTotal );
				dynamicProductPrice = fetch.total;
				productPrice = dynamicProductPrice;
				rawProductRegularPrice = fetch.original_total;
				_total = dynamicProductPrice;
				_original_total = dynamicProductPrice;
				totalsHolderContainer.find( '.cpf-override-product-price' ).val( dynamicProductPrice );
				totalsHolder_tc_totals_ob.override_product_price = dynamicProductPrice;
				totalsHolder.data( 'totalsHolder_tc_totals_ob', totalsHolder_tc_totals_ob );
			}

			dynamicCheck = epoHolder.find( '.tmcp-dynamic.override-product-price.tc-is-math-special' );
			if ( currentEpoObject.is_associated === false ) {
				dynamicCheck = dynamicCheck.not( tcAPI.associatedEpoSelector + ' ' + '.tmcp-dynamic.override-product-price.tc-is-math-special' );
			}
			dynamicCheck = dynamicCheck.filter( '.tcenabled' );
			if ( dynamicCheck.length > 0 ) {
				fetch = fetchOptionPrices( currentEpoObject, epoHolder, '.tmcp-field.tmcp-dynamic.override-product-price.tc-is-math-special', dynamicProductPrice + total, dynamicProductPrice + original_total, floatingBoxData, showTotal, undefined, undefined, undefined, true );
				dynamicProductPrice = $.epoAPI.math.toFloat( fetch.total ) - $.epoAPI.math.toFloat( total );
				productPrice = dynamicProductPrice;
				rawProductRegularPrice = $.epoAPI.math.toFloat( fetch.original_total ) - $.epoAPI.math.toFloat( original_total );
				_total = dynamicProductPrice;
				_original_total = dynamicProductPrice;
				totalsHolderContainer.find( '.cpf-override-product-price' ).val( dynamicProductPrice );
				totalsHolder_tc_totals_ob.override_product_price = dynamicProductPrice;
				totalsHolder.data( 'totalsHolder_tc_totals_ob', totalsHolder_tc_totals_ob );
			}

			dynamicCheck = epoHolder.find( '.tmcp-dynamic.override-product-price.tc-is-math-cumulative' );
			if ( currentEpoObject.is_associated === false ) {
				dynamicCheck = dynamicCheck.not( tcAPI.associatedEpoSelector + ' ' + '.tmcp-dynamic.override-product-price.tc-is-math-cumulative' );
			}
			dynamicCheck = dynamicCheck.filter( '.tcenabled' );
			if ( dynamicCheck.length > 0 ) {
				fetch = fetchOptionPrices( currentEpoObject, epoHolder, '.tmcp-field.tmcp-dynamic.override-product-price.tc-is-math-cumulative', dynamicProductPrice + total, dynamicProductPrice + original_total, floatingBoxData, showTotal, undefined, undefined, undefined, true );
				dynamicProductPrice = $.epoAPI.math.toFloat( fetch.total ) - $.epoAPI.math.toFloat( total );
				productPrice = dynamicProductPrice;
				rawProductRegularPrice = $.epoAPI.math.toFloat( fetch.original_total ) - $.epoAPI.math.toFloat( original_total );
				_total = dynamicProductPrice;
				_original_total = dynamicProductPrice;
				totalsHolderContainer.find( '.cpf-override-product-price' ).val( dynamicProductPrice );
				totalsHolder_tc_totals_ob.override_product_price = dynamicProductPrice;
				totalsHolder.data( 'totalsHolder_tc_totals_ob', totalsHolder_tc_totals_ob );
			}

			// Check if there are any dynamic override elements
			dynamicCheck = epoHolder.find( '.tmcp-dynamic.override-product-price' );
			if ( currentEpoObject.is_associated === false ) {
				dynamicCheck = dynamicCheck.not( tcAPI.associatedEpoSelector + ' ' + '.tmcp-dynamic.override-product-price' );
			}
			dynamicCheck = dynamicCheck.filter( '.tcenabled' );
			if ( ! dynamicCheck.length > 0 ) {
				totalsHolderContainer.find( '.cpf-override-product-price' ).val( '' );
			} else {
				_total = 0;
				_original_total = 0;
			}

			jWindow.trigger( 'tcEpoAfterOptionPriceCalculation', {
				epo: currentEpoObject,
				alternativeCart: currentEpoObject,
				this_product_type: this_product_type,
				cart: cart,
				totalsHolder: totalsHolder,
				fetchOptionPrices: fetchOptionPrices,
				epoHolder: epoHolder,
				total: total,
				original_total: original_total,
				floatingBoxData: floatingBoxData,
				showTotal: showTotal,
				cart_fee_options_total: cart_fee_options_total,
				cart_fee_options_original_total: cart_fee_options_original_total
			} );

			setTimeout( function() {
				epoHolder.find( 'select.tm-epo-field' ).trigger( 'tm-select-price-update-html-all' );
			}, 1 );

			$.tcepo.oneOptionIsSelected[ epoEventId ] = showTotal;
			currentEpoObject = addShowHidetoEpoObject( currentEpoObject, epoEventId, elementsLength );
			tm_show_hide_add_to_cart_button( main_product, currentEpoObject );

			if ( cart_fee_options_total > 0 ) {
				showTotal = true;
			}

			if ( alternativeCart && ! perProductPricing ) {
				showTotal = false;
			}

			if (
				finalTotalBoxMode === 'enable' ||
				finalTotalBoxMode === 'hideifoptionsiszero' ||
				finalTotalBoxMode === 'hideiftotaliszero'
			) {
				showTotal = true;
			}

			if ( cartQty > 1 ) {
				showTotal = true;
			}
			if ( ( this_product_type === 'variable' || this_product_type === 'variable-subscription' ) && ! $.epoAPI.math.toFloat( currentVariation ) ) {
				showTotal = false;
			}

			if ( finalTotalBoxMode === 'disable' ) {
				showTotal = false;
			}
			if ( TMEPOJS.tm_epo_change_variation_price === 'yes' || TMEPOJS.tm_epo_change_original_price === 'yes' ) {
				showTotal = true;
			}

			if ( currentEpoObject.is_associated && ! perProductPricing ) {
				showTotal = false;
			}

			if ( TMEPOJS.tm_epo_total_price_as_unit_price === 'yes' ) {
				cartQty = 1;
			}

			product_total_price = parseFloat( productPrice * cartQty );

			if ( TMEPOJS.extraFee ) {
				extraFee = parseFloat( TMEPOJS.extraFee );
				if ( ! Number.isFinite( extraFee ) ) {
					extraFee = 0;
				}
			}

			calculateFinalProductPrice = $.epoAPI.applyFilter( 'tcCalculateFinalProductPrice', false, {
				alternativeCart: alternativeCart,
				product_price: productPrice,
				product_total_price: product_total_price,
				v_product_price: rawProductPrice,
				tm_set_tax_price: tm_set_tax_price,
				main_product: main_product,
				totalsHolder: totalsHolder,
				cartQty: cartQty
			} );

			if (
				calculateFinalProductPrice !== false &&
				typeof calculateFinalProductPrice === 'object' &&
				Object.prototype.hasOwnProperty.call( calculateFinalProductPrice, 'productPrice' ) &&
				Object.prototype.hasOwnProperty.call( calculateFinalProductPrice, 'productTotalPrice' )
			) {
				productPrice = calculateFinalProductPrice.productPrice;
				product_total_price = calculateFinalProductPrice.productTotalPrice;
			}

			_total = $.epoAPI.applyFilter( 'tc_adjust_options_price_per_unit', _total, product_total_price );
			total = parseFloat( _total * cartQty * elementQty );
			total = $.epoAPI.applyFilter( 'tc_adjust_options_total_price', total, cartQty, elementQty, _total, totalsHolder );
			if ( TMEPOJS.tm_epo_global_product_element_quantity_sync === 'no' ) {
				totalProductField = parseFloat( productFieldSyncTotal );
				totalProductFieldTaxed = parseFloat( productFieldSyncTotalTaxed );
			} else {
				totalProductField = parseFloat( productFieldSyncTotal * cartQty * elementQty );
				totalProductFieldTaxed = parseFloat( productFieldSyncTotalTaxed * cartQty * elementQty );
			}
			totalProductField = $.epoAPI.applyFilter( 'tc_adjust_options_productfield_total_price', totalProductField, cartQty, elementQty, _total, totalsHolder );
			totalProductFieldTaxed = $.epoAPI.applyFilter( 'tc_adjust_options_productfield_total_price_taxed', totalProductFieldTaxed, cartQty, elementQty, _total, totalsHolder );

			_original_total = $.epoAPI.applyFilter( 'tc_adjust_options_price_per_unit', _original_total, product_total_price );
			original_total = parseFloat( _original_total * cartQty * elementQty );
			original_total = $.epoAPI.applyFilter( 'tc_adjust_options_original_total_price', original_total, cartQty, elementQty, _original_total, totalsHolder );
			if ( TMEPOJS.tm_epo_global_product_element_quantity_sync === 'no' ) {
				originalTotalProductField = parseFloat( productFieldSyncOriginalTotal );
				originalTotalProductFieldTaxed = parseFloat( productFieldSyncOriginalTotalTaxed );
			} else {
				originalTotalProductField = parseFloat( productFieldSyncOriginalTotal * cartQty * elementQty );
				originalTotalProductFieldTaxed = parseFloat( productFieldSyncOriginalTotalTaxed * cartQty * elementQty );
			}
			originalTotalProductField = $.epoAPI.applyFilter( 'tc_adjust_options_productfield_original_total_price', originalTotalProductField, cartQty, elementQty, _original_total, totalsHolder );
			originalTotalProductFieldTaxed = $.epoAPI.applyFilter( 'tc_adjust_options_productfield_original_total_price_taxed', originalTotalProductFieldTaxed, cartQty, elementQty, _original_total, totalsHolder );

			if ( priceOverrideMode === '1' && parseFloat( total ) > 0 ) {
				productPrice = 0;
				rawProductPrice = 0;
				product_total_price = 0;
			}

			product_total_price = $.epoAPI.applyFilter( 'tc_adjust_product_total_price_without_options', product_total_price );

			total = $.epoAPI.applyFilter( 'tcAdjustTotal', total, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				cart: cart,
				main_product: main_product
			} );
			totalProductField = $.epoAPI.applyFilter( 'tcAdjustProductFieldTotal', totalProductField, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				cart: cart,
				main_product: main_product
			} );
			totalProductFieldTaxed = $.epoAPI.applyFilter( 'tcAdjustProductFieldTotalTaxed', totalProductFieldTaxed, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				cart: cart,
				main_product: main_product
			} );
			original_total = $.epoAPI.applyFilter( 'tcAdjustOriginalTotal', original_total, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				cart: cart,
				main_product: main_product
			} );
			originalTotalProductField = $.epoAPI.applyFilter( 'tcAdjustProductFieldOriginalTotal', originalTotalProductField, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				cart: cart,
				main_product: main_product
			} );
			originalTotalProductFieldTaxed = $.epoAPI.applyFilter( 'tcAdjustProductFieldOriginalTotalTaxed', originalTotalProductFieldTaxed, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				cart: cart,
				main_product: main_product
			} );

			total = parseFloat( $.epoAPI.applyFilter( 'tc_adjust_total', total, totalsHolder ) );
			cart_fee_options_total = parseFloat( $.epoAPI.applyFilter( 'tc_adjust_totals_fee', cart_fee_options_total, totalsHolder ) );
			original_total = parseFloat( $.epoAPI.applyFilter( 'tc_adjust_original_total', original_total, totalsHolder ) );
			cart_fee_options_original_total = parseFloat( $.epoAPI.applyFilter( 'tc_adjust_original_total_fee', cart_fee_options_original_total, totalsHolder ) );
			raw_extraFee = extraFee;

			total_plus_fee = total + cart_fee_options_total;
			original_total_plus_fee = original_total + cart_fee_options_original_total;

			raw_total = total;
			raw__total = _total;

			raw_cart_fee_options_total = cart_fee_options_total;
			raw_total_plus_fee = total_plus_fee;

			raw_original_total = original_total;
			raw__original_total = _original_total;

			raw_cart_fee_options_original_total = cart_fee_options_original_total;
			raw_original_total_plus_fee = original_total_plus_fee;

			raw_product_total_price_without_options = product_total_price;
			raw_product_total_price = parseFloat( product_total_price + total_plus_fee + raw_extraFee );
			raw_product_total_original_price = parseFloat( rawProductRegularPrice + original_total_plus_fee + raw_extraFee );

			productPrice = tm_set_tax_price( productPrice, totalsHolder, undefined, undefined, undefined, undefined, true );
			productRegularPrice = tm_set_tax_price( productRegularPrice, totalsHolder, undefined, undefined, undefined, undefined, true );
			product_total_price = tm_set_tax_price( product_total_price, totalsHolder, undefined, undefined, undefined, undefined, true );

			total = tm_set_tax_price( total, totalsHolder, undefined, undefined, undefined, undefined, true );
			_total = tm_set_tax_price( _total, totalsHolder, undefined, undefined, undefined, undefined, true );

			total_plus_fee = parseFloat( cart_fee_options_total ) + parseFloat( total );

			original_total = tm_set_tax_price( original_total, totalsHolder, undefined, undefined, undefined, undefined, true );
			_original_total = tm_set_tax_price( _original_total, totalsHolder, undefined, undefined, undefined, undefined, true );

			original_total_plus_fee = parseFloat( cart_fee_options_original_total ) + parseFloat( original_total );

			extraFee = tm_set_tax_price( extraFee, totalsHolder, undefined, undefined, undefined, undefined, true );

			// Calculate product price
			product_total_price_without_options = product_total_price;
			product_total_price = $.epoAPI.applyFilter( 'tc_adjust_product_total_price', parseFloat( product_total_price + total_plus_fee + extraFee ), product_total_price_without_options, total_plus_fee, extraFee, total, cart_fee_options_total, totalsHolder );
			product_total_original_price = $.epoAPI.applyFilter( 'tc_adjust_product_total_original_price', parseFloat( product_total_price_without_options + original_total_plus_fee + extraFee ), product_total_price_without_options, original_total_plus_fee, extraFee, original_total, cart_fee_options_original_total, totalsHolder );

			// Calculate vat options total
			pricesIncludeTax = totalsHolder.attr( 'data-prices-include-tax' ) || TMEPOJS.prices_include_tax;
			if ( pricesIncludeTax === '1' ) {
				vat_options_total = productFieldSyncVatTotal + parseFloat( calculateTaxAmount( total, totalsHolder ) );
				vat_options_total_plus_fee = productFieldSyncVatTotal + parseFloat( calculateTaxAmount( total_plus_fee, totalsHolder ) );
				vat_total = productFieldSyncVatTotal + parseFloat( calculateTaxAmount( product_total_price, totalsHolder ) );
				vat_product_base = parseFloat( calculateTaxAmount( product_total_price_without_options, totalsHolder ) );
			} else {
				vat_options_total = productFieldSyncVatTotal + parseFloat( total - raw_total );
				vat_options_total_plus_fee = productFieldSyncVatTotal + parseFloat( total_plus_fee - raw_total_plus_fee );
				vat_total = productFieldSyncVatTotal + parseFloat( product_total_price - raw_product_total_price );
				vat_product_base = parseFloat( product_total_price_without_options - raw_product_total_price_without_options );
			}

			raw_total = raw_total + totalProductField;
			raw__total = raw__total + totalProductField;
			raw_total_plus_fee = raw_total_plus_fee + totalProductField;
			raw_original_total = raw_original_total + originalTotalProductField;
			raw__original_total = raw__original_total + originalTotalProductField;
			raw_original_total_plus_fee = raw_original_total_plus_fee + originalTotalProductField;
			raw_product_total_price = raw_product_total_price + totalProductField;
			raw_product_total_original_price = raw_product_total_original_price + originalTotalProductField;
			total = total + totalProductFieldTaxed;
			_total = _total + totalProductFieldTaxed;
			total_plus_fee = total_plus_fee + totalProductFieldTaxed;
			original_total = original_total + originalTotalProductFieldTaxed;
			_original_total = _original_total + originalTotalProductFieldTaxed;
			original_total_plus_fee = original_total_plus_fee + originalTotalProductFieldTaxed;
			product_total_price = product_total_price + totalProductFieldTaxed;
			product_total_original_price = product_total_original_price + originalTotalProductFieldTaxed;

			// Calculate unit price
			unit_price = parseFloat( productPrice + parseFloat( _total ) );
			unit_original_price = parseFloat( productPrice + parseFloat( _original_total ) );
			if ( TMEPOJS.tm_epo_fees_on_unit_price === 'yes' ) {
				unit_price = parseFloat( productPrice + parseFloat( _total ) + parseFloat( parseFloat( cart_fee_options_total ) / cartQty ) );
				unit_original_price = parseFloat( productPrice + parseFloat( _original_total ) + parseFloat( parseFloat( cart_fee_options_original_total ) / cartQty ) );
			}

			// Format unit price
			formatted_unit_price = tm_set_price_totals( unit_price, totalsHolder, true, true );
			formatted_unit_original_price = tm_set_price_totals( unit_original_price, totalsHolder, true, true );

			// Format extra fee
			if ( extraFee ) {
				show_extra_fee = true;
				formatted_extra_fee = tm_set_price_totals( extraFee, totalsHolder, true, true );
			}

			// Format final total
			formatted_final_total = tm_set_price_totals( product_total_price, totalsHolder, true, true );
			formatted_final_original_total = tm_set_price_totals( product_total_original_price, totalsHolder, true, true );

			// Format options total
			formatted_options_total = tm_set_price_totals( total, totalsHolder, true, true );

			// Format fees total
			formatted_fees_total = tm_set_price_totals( cart_fee_options_total, totalsHolder, true, true );

			// Format options original total
			formatted_options_original_total = tm_set_price_totals( original_total, totalsHolder, true, true );

			// Format fees original total
			formatted_fees_original_total = tm_set_price_totals( cart_fee_options_original_total, totalsHolder, true, true );

			// Format vat options total
			formatted_vat_options_total = tm_set_price_totals( vat_options_total );
			formatted_vat_options_total_plus_fee = tm_set_price_totals( vat_options_total_plus_fee );
			formatted_vat_total = tm_set_price_totals( vat_total );
			formatted_vat_product_base = tm_set_price_totals( vat_product_base );

			// Backwards compatibility
			formatted_unit_price = $.epoAPI.applyFilter( 'tc_adjust_formatted_unit_price', formatted_unit_price, productPrice, _total, cart_fee_options_total, cartQty );
			formatted_options_total = $.epoAPI.applyFilter( 'tc_adjust_formatted_options_total', formatted_options_total, total, _total, cartQty );
			formatted_fees_total = $.epoAPI.applyFilter( 'tc_adjust_formatted_fees_total', formatted_fees_total, cart_fee_options_total );
			formatted_final_total = $.epoAPI.applyFilter( 'tc_adjust_formatted_final_total', formatted_final_total, product_total_price, product_total_price_without_options, total_plus_fee, extraFee, cartQty );

			formatted_unit_price = $.epoAPI.applyFilter( 'tcAdjustFormattedUnitPrice', formatted_unit_price, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				event: event,
				unit_price: unit_price,
				productPrice: productPrice,
				_total: _total,
				total_plcart_fee_options_totals_fee: cart_fee_options_total,
				cartQty: cartQty
			} );
			formatted_unit_original_price = $.epoAPI.applyFilter( 'tcAdjustFormattedUnitOriginalPrice', formatted_unit_original_price, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				event: event,
				unit_original_price: unit_original_price,
				productPrice: productPrice,
				_original_total: _original_total,
				total_plcart_fee_options_totals_fee: cart_fee_options_total,
				cartQty: cartQty
			} );
			formatted_options_total = $.epoAPI.applyFilter( 'tcAdjustFormattedOptionsTotal', formatted_options_total, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				event: event,
				total: total,
				_total: _total,
				total_plus_fee: total_plus_fee,
				extraFee: extraFee,
				cartQty: cartQty
			} );
			formatted_options_original_total = $.epoAPI.applyFilter( 'tcAdjustFormattedOptionsOriginalTotal', formatted_options_original_total, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				event: event,
				original_total: original_total,
				_original_total: _original_total,
				original_total_plus_fee: original_total_plus_fee,
				extraFee: extraFee,
				cartQty: cartQty
			} );
			formatted_fees_total = $.epoAPI.applyFilter( 'tcAdjustFormattedFeesTotal', formatted_fees_total, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				event: event,
				cart_fee_options_total: cart_fee_options_total,
				extraFee: extraFee,
				cartQty: cartQty
			} );
			formatted_fees_original_total = $.epoAPI.applyFilter( 'tcAdjustFormattedFeesOriginalTotal', formatted_fees_original_total, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				event: event,
				cart_fee_options_original_total: cart_fee_options_original_total,
				extraFee: extraFee,
				cartQty: cartQty
			} );
			formatted_final_total = $.epoAPI.applyFilter( 'tcAdjustFormattedFinalTotal', formatted_final_total, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				event: event,
				product_total_price: product_total_price,
				product_total_price_without_options: product_total_price_without_options,
				total_plus_fee: total_plus_fee,
				extraFee: extraFee,
				cartQty: cartQty
			} );
			formatted_final_original_total = $.epoAPI.applyFilter( 'tcAdjustFormattedFinalOriginalTotal', formatted_final_original_total, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				event: event,
				product_total_original_price: product_total_original_price,
				product_total_price_without_options: product_total_price_without_options,
				original_total_plus_fee: original_total_plus_fee,
				extraFee: extraFee,
				cartQty: cartQty
			} );

			if ( ( total > 0 && finalTotalBoxShowOptions === 'showtgz' ) || finalTotalBoxShowOptions === 'show' ) {
				show_options_total = true;
				if ( TMEPOJS.tm_epo_enable_vat_options_total === 'yes' ) {
					show_vat_options_total = true;
				}
			}
			if ( cart_fee_options_total !== 0 ) {
				show_fees_total = true;
			}

			if ( formatted_final_total && finalTotalBoxShowFinal === 'show' ) {
				show_final_total = true;
			}

			tc_totals_ob = {
				qty: cartQty,
				product_price: productPrice,
				raw_product_price: rawProductPrice,
				product_regular_price: productRegularPrice,
				raw_product_regular_price: rawProductRegularPrice,
				formatted_product_price: tm_set_price_totals( parseFloat( productPrice ) * cartQty ),

				late_total_prices: late_total_price,
				late_total_price: tm_set_tax_price( late_total_price[ 0 ], totalsHolder ),
				late_total_original_price: tm_set_tax_price( late_total_price[ 1 ], totalsHolder ),

				raw_options_price_per_unit: raw__total,
				raw_options_total_price: raw_total,
				raw_cart_fee_options_total_price: raw_cart_fee_options_total,
				raw_total_plus_fee: raw_total_plus_fee,

				raw_options_original_price_per_unit: raw__original_total,
				raw_options_original_total_price: raw_original_total,
				raw_cart_fee_options_original_total_price: raw_cart_fee_options_original_total,
				raw_original_total_plus_fee: raw_original_total_plus_fee,

				raw_product_total_price: raw_product_total_price,
				raw_product_total_original_price: raw_product_total_original_price,
				raw_product_total_price_without_options: raw_product_total_price_without_options,

				options_price_per_unit: _total,
				options_total_price: total,
				cart_fee_options_total_price: cart_fee_options_total,
				total_plus_fee: total_plus_fee,

				options_original_price_per_unit: _original_total,
				options_original_total_price: original_total,
				cart_fee_options_total_original_price: cart_fee_options_original_total,
				original_total_plus_fee: original_total_plus_fee,

				vat_options_total: vat_options_total,
				vat_options_total_plus_fee: vat_options_total_plus_fee,
				formatted_vat_options_total: formatted_vat_options_total,
				formatted_vat_options_total_plus_fee: formatted_vat_options_total_plus_fee,
				vat_total: vat_total,
				formatted_vat_total: formatted_vat_total,
				vat_product_base: vat_product_base,
				formatted_vat_product_base: formatted_vat_product_base,

				product_total_price: product_total_price,
				product_total_original_price: product_total_original_price,
				product_total_price_without_options: product_total_price_without_options,

				product_unit_price: unit_price,
				product_unit_original_price: unit_original_price,
				formatted_unit_price: formatted_unit_price,
				formatted_options_total: formatted_options_total,
				formatted_fees_total: formatted_fees_total,
				formatted_final_total: formatted_final_total,

				formatted_unit_original_price: formatted_unit_original_price,
				formatted_options_original_total: formatted_options_original_total,
				formatted_fees_original_total: formatted_fees_original_total,
				formatted_final_original_total: formatted_final_original_total,

				formatted_extra_fee: formatted_extra_fee,

				show_options_total: show_options_total,
				show_fees_total: show_fees_total,
				show_extra_fee: show_extra_fee,
				show_final_total: show_final_total,
				show_options_vat: show_vat_options_total,

				unit_price: TMEPOJS.i18n_unit_price,
				show_unit_price: TMEPOJS.tm_epo_show_unit_price === 'yes',
				options_total: TMEPOJS.i18n_options_total,
				fees_total: TMEPOJS.i18n_fees_total,
				extra_fee: TMEPOJS.i18n_extra_fee,
				final_total: TMEPOJS.i18n_final_total,
				options_vat_total: TMEPOJS.i18n_vat_options_total,

				totals_box_before_unit_price: TMEPOJS.totals_box_before_unit_price,
				totals_box_after_unit_price: TMEPOJS.totals_box_after_unit_price,
				totals_box_before_vat_options_totals_price: TMEPOJS.totals_box_before_vat_options_totals_price,
				totals_box_after_vat_options_totals_price: TMEPOJS.totals_box_after_vat_options_totals_price,
				totals_box_before_options_totals_price: TMEPOJS.totals_box_before_options_totals_price,
				totals_box_after_options_totals_price: TMEPOJS.totals_box_after_options_totals_price,
				totals_box_before_fee_totals_price: TMEPOJS.totals_box_before_fee_totals_price,
				totals_box_after_fee_totals_price: TMEPOJS.totals_box_after_fee_totals_price,
				totals_box_before_extra_fee_price: TMEPOJS.totals_box_before_extra_fee_price,
				totals_box_after_extra_fee_price: TMEPOJS.totals_box_after_extra_fee_price,
				totals_box_before_final_totals_price: TMEPOJS.totals_box_before_final_totals_price,
				totals_box_after_final_totals_price: TMEPOJS.totals_box_after_final_totals_price
			};

			if ( TMEPOJS.customer_price_format ) {
				customer_price_format_wrap_start = TMEPOJS.customer_price_format_wrap_start;
				customer_price_format_wrap_end = TMEPOJS.customer_price_format_wrap_end;
				customerPriceFormat = TMEPOJS.customer_price_format;
			}

			if ( formatted_options_total && total > 0 ) {
				_ftotal = formatPrice( total );
				_foriginal_total = formatPrice( original_total );
				if ( customerPriceFormat ) {
					_ftotal = customerPriceFormat.replace( '__PRICE__', _ftotal ).replace( '__CODE__', TMEPOJS.current_currency );
					if ( ! totalsHolder.data( 'is-on-sale' ) ) {
						_foriginal_total = customerPriceFormat.replace( '__PRICE__', _foriginal_total ).replace( '__CODE__', TMEPOJS.current_currency );
					}
				}
				_ftotal = $.epoAPI.applyFilter( 'tcFilterFormattedFTotal', _ftotal, {
					epo: currentEpoObject,
					totalsHolder: totalsHolder,
					event: event,
					total: total,
					_total: _total,
					total_plus_fee: total_plus_fee,
					extraFee: extraFee,
					cartQty: cartQty
				} );
				_foriginal_total = $.epoAPI.applyFilter( 'tcFilterFormattedFOriginalTotal', _foriginal_total, {
					epo: currentEpoObject,
					totalsHolder: totalsHolder,
					event: event,
					original_total: original_total,
					total: original_total,
					_total: _total,
					total_plus_fee: total_plus_fee,
					extraFee: extraFee,
					cartQty: cartQty
				} );
				if ( TMEPOJS.tm_epo_enable_original_final_total === 'yes' && total !== original_total ) {
					tc_totals_ob.formatted_options_total = $.epoAPI.util.decodeHTML(
						$.epoAPI.template.html( tcAPI.templateEngine.tc_formatted_sale_price, {
							price: _foriginal_total,
							sale_price: _ftotal,
							customer_price_format_wrap_start: customer_price_format_wrap_start,
							customer_price_format_wrap_end: customer_price_format_wrap_end
						} )
					);
					tc_totals_ob._foriginal_total = _foriginal_total;
					tc_totals_ob._ftotal = _ftotal;
				} else {
					tc_totals_ob.formatted_options_total = $.epoAPI.util.decodeHTML(
						$.epoAPI.util.decodeHTML(
							$.epoAPI.template.html( tcAPI.templateEngine.tc_formatted_price, {
								price: _ftotal,
								customer_price_format_wrap_start: customer_price_format_wrap_start,
								customer_price_format_wrap_end: customer_price_format_wrap_end
							} )
						)
					);
				}
			}

			if ( formatted_final_total && product_total_price >= 0 ) {
				_fprice = formatPrice( product_total_price );
				if ( priceOverrideMode === '1' && parseFloat( total ) > 0 ) {
					_f_regular_price = parseFloat( raw_original_total + extraFee );
				} else {
					_f_regular_price = parseFloat( parseFloat( rawProductRegularPrice * cartQty ) + raw_original_total );
				}
				if ( customerPriceFormat ) {
					_fprice = customerPriceFormat.replace( '__PRICE__', _fprice ).replace( '__CODE__', TMEPOJS.current_currency );
					if ( ! totalsHolder.data( 'is-on-sale' ) ) {
						_f_regular_price = customerPriceFormat.replace( '__PRICE__', _f_regular_price ).replace( '__CODE__', TMEPOJS.current_currency );
					}
				}

				_fprice = $.epoAPI.applyFilter( 'tc_adjust_native_price', _fprice, product_total_price );
				_f_regular_price = tm_set_tax_price( _f_regular_price, totalsHolder, undefined, undefined, undefined, undefined, true ) + parseFloat( cart_fee_options_original_total ) + parseFloat( extraFee );
				_f_regular_price = formatPrice( _f_regular_price );
				_f_regular_price = $.epoAPI.applyFilter( 'tc_adjust_native_regular_price', _f_regular_price, product_total_price );

				if ( TMEPOJS.tm_epo_enable_original_final_total === 'yes' && ( ( totalsHolder.data( 'is-on-sale' ) && priceOverrideMode !== '1' ) || _f_regular_price !== _fprice ) ) {
					tc_totals_ob.formatted_final_total = $.epoAPI.util.decodeHTML(
						$.epoAPI.template.html( tcAPI.templateEngine.tc_formatted_sale_price, {
							price: _f_regular_price,
							sale_price: _fprice,
							customer_price_format_wrap_start: customer_price_format_wrap_start,
							customer_price_format_wrap_end: customer_price_format_wrap_end
						} )
					);
				} else {
					tc_totals_ob.formatted_final_total = $.epoAPI.util.decodeHTML(
						$.epoAPI.util.decodeHTML(
							$.epoAPI.template.html( tcAPI.templateEngine.tc_formatted_price, {
								price: _fprice,
								customer_price_format_wrap_start: customer_price_format_wrap_start,
								customer_price_format_wrap_end: customer_price_format_wrap_end
							} )
						)
					);
				}
				tc_totals_ob._f_regular_price = _f_regular_price;
				tc_totals_ob._fprice = _fprice;
			}

			tc_totals_ob = replace_suffixes( 'formatted_fees_original_total', 'cart_fee_options_total_original_price', tc_totals_ob, totalsHolder );
			tc_totals_ob = replace_suffixes( 'formatted_fees_total', 'cart_fee_options_total_price', tc_totals_ob, totalsHolder );
			tc_totals_ob = replace_suffixes( 'formatted_final_original_total', 'product_total_original_price', tc_totals_ob, totalsHolder );
			tc_totals_ob = replace_suffixes( 'formatted_final_total', 'product_total_price', tc_totals_ob, totalsHolder );
			tc_totals_ob = replace_suffixes( 'formatted_options_original_total', 'options_original_total_price', tc_totals_ob, totalsHolder );
			tc_totals_ob = replace_suffixes( 'formatted_options_total', 'options_total_price', tc_totals_ob, totalsHolder );
			tc_totals_ob = replace_suffixes( 'formatted_unit_original_price', 'product_unit_original_price', tc_totals_ob, totalsHolder );
			tc_totals_ob = replace_suffixes( 'formatted_unit_price', 'product_unit_price', tc_totals_ob, totalsHolder );

			if ( $.tcepo.showHideTotal !== undefined && $.tcepo.showHideTotal[ epoEventId ] !== undefined ) {
				showTotal = $.tcepo.showHideTotal[ epoEventId ];
			}

			tc_totals_ob = $.epoAPI.applyFilter( 'tc_adjust_tc_totals_ob', tc_totals_ob, {
				epo_object: currentEpoObject,
				showTotal: showTotal,
				epoHolder: epoHolder,
				totalsHolder: totalsHolder,
				tm_set_price: tm_set_price,
				tm_set_price_totals: tm_set_price_totals,
				product_total_price: product_total_price,
				product_price: productPrice,
				qty: cartQty
			} );

			if ( tc_totals_ob.showTotal !== undefined ) {
				showTotal = tc_totals_ob.showTotal;
			}

			currentEpoObject.tc_totals_ob = tc_totals_ob;

			showTotal = $.epoAPI.applyFilter( 'tcFinalTotalsBoxVisibility', showTotal, {
				epo: currentEpoObject,
				alternativeCart: alternativeCart,
				cart: cart,
				main_product: main_product,
				totalsHolder: totalsHolder,
				this_epo_totals_container: this_epo_totals_container
			} );

			html = $.epoAPI.template.html( tcAPI.templateEngine.tc_final_totals, tc_totals_ob );

			totalsHolder.data( 'tm-html', html );
			totalsHolder.data( 'tc_totals_ob', tc_totals_ob );

			if ( currentEpoObject.associated_connect && currentEpoObject.associated_connect.length === 1 ) {
				if ( currentEpoObject.associated_connect.attr( 'data-no-price' ) === '1' ) {
					associatedPrice = 0;
					associatedRawPrice = 0;
					associatedOriginalPrice = 0;
					associatedRawOriginalPrice = 0;
					associatedFormattedPrice = '';
					tm_force_update_price(
						associatedSetter
							.closest( '.tmcp-field-wrap' )
							.find( '.tc-price' )
							.not( tcAPI.associatedEpoSelector + ' .tc-price' ),
						associatedPrice,
						associatedFormattedPrice,
						associatedOriginalPrice
					);
				} else {
					associatedPrice = tc_totals_ob.product_price + tc_totals_ob.options_price_per_unit + ( tc_totals_ob.cart_fee_options_total_price / tc_totals_ob.qty );
					associatedRawPrice = tc_totals_ob.raw_product_price + tc_totals_ob.raw_options_price_per_unit + ( tc_totals_ob.raw_cart_fee_options_total_price / tc_totals_ob.qty );
					associatedOriginalPrice = tc_totals_ob.product_regular_price + tc_totals_ob.options_original_price_per_unit + ( tc_totals_ob.cart_fee_options_total_original_price / tc_totals_ob.qty );
					associatedRawOriginalPrice = tc_totals_ob.raw_product_regular_price + tc_totals_ob.raw_options_original_price_per_unit + ( tc_totals_ob.raw_cart_fee_options_original_total_price / tc_totals_ob.qty );

					associatedFormattedPrice = tm_set_price( associatedPrice, currentEpoObject.this_epo_totals_containe, false, false, associatedSetter );

					// Mainly used to update the price of the associated element when the associated product is variable.
					tm_force_update_price(
						associatedSetter
							.closest( '.tmcp-field-wrap' )
							.find( '.tc-price' )
							.not( tcAPI.associatedEpoSelector + ' .tc-price' ),
						associatedPrice,
						associatedFormattedPrice,
						associatedOriginalPrice
					);

					currentEpoObject.associated_connect.data( 'tm-quantity', tc_totals_ob.qty );

					if ( currentEpoObject.associated_connect.data( 'tm-quantity' ) ) {
						associatedPrice = associatedPrice * parseFloat( currentEpoObject.associated_connect.data( 'tm-quantity' ) );
						associatedRawPrice = associatedRawPrice * parseFloat( currentEpoObject.associated_connect.data( 'tm-quantity' ) );
						associatedOriginalPrice = associatedOriginalPrice * parseFloat( currentEpoObject.associated_connect.data( 'tm-quantity' ) );
						associatedRawOriginalPrice = associatedRawOriginalPrice * parseFloat( currentEpoObject.associated_connect.data( 'tm-quantity' ) );
					}

					associatedFormattedPrice = tm_set_price( associatedPrice, currentEpoObject.this_epo_totals_containe, false, false, associatedSetter );
				}

				currentEpoObject.associated_connect.data( 'price_set', 1 );
				if ( associatedSetter.is( '.tcenabled' ) ) {
					associatedSetter.data( 'associated_price_set', 1 );
				}
				associatedSetter.data( 'price_set', 1 );
				associatedSetter.data( 'raw_price', associatedRawPrice );
				associatedSetter.data( 'raw_original_price', associatedRawOriginalPrice );
				associatedSetter.data( 'price', tm_set_tax_price( associatedPrice, currentEpoObject.this_epo_totals_containe, associatedSetter ) );
				associatedSetter.data( 'original_price', tm_set_tax_price( associatedOriginalPrice, currentEpoObject.this_epo_totals_containe, associatedSetter ) );

				currentEpoObject.associated_connect.data( 'price-changed', 1 );

				setTimeout( function() {
					if ( ! ( currentEpoObject.associated_connect && event.noassociated ) ) {
						currentEpoObject.mainEpoObject.main_cart.trigger( {
							type: 'tm-epo-update',
							norules: 2
						} );
					}
				}, 20 );
			}

			jWindow.trigger( 'tcEpoAfterCalculateTotals', {
				epo: currentEpoObject,
				alternativeCart: alternativeCart,
				this_product_type: this_product_type,
				cart: cart,
				bundleid: bundleid,
				totalsObject: tc_totals_ob,
				main_product: main_product,
				per_product_pricing: perProductPricing,
				event: event
			} );

			hide_native_price = $.epoAPI.applyFilter( 'hide_native_price', true );

			if ( cartQty > 0 ) {
				// hide native prices
				if ( TMEPOJS.tm_epo_change_variation_price === 'yes' ) {
					if ( hide_native_price === true && finalTotalBoxMode !== 'disable' ) {
						tm_get_native_prices_block( cart ).hide();
					} else {
						tm_get_native_prices_block( cart ).show();
					}
				}

				if ( ! showTotal ||
					finalTotalBoxMode === 'disable' ||
					( finalTotalBoxMode === 'hideiftotaliszero' && product_total_price === 0 ) ||
					( finalTotalBoxMode === 'hideifoptionsiszero' && total_plus_fee === 0 )
				) {
					html = '';
					totalsHolder.html( html );
					totalsHolder.data( 'tm-floating-box-data', [] );
				} else {
					totalsHolder.html( html );

					jWindow.trigger( 'tc-totals-container', {
						epo: tc_totals_ob,
						totals_holder: totalsHolder,
						data: {
							epo_object: currentEpoObject,
							tm_set_price: tm_set_price,
							tm_set_price_totals: tm_set_price_totals,
							product_total_price: product_total_price,
							product_price: productPrice,
							qty: cartQty
						},
						tm_epo_js: TMEPOJS
					} );
				}

				if ( formatted_final_total && product_total_price >= 0 ) {
					update_native_html = tm_get_native_prices_block( cart );

					if ( TMEPOJS.tm_epo_change_variation_price === 'yes' ) {
						if ( totalsHolder.data( 'is-on-sale' ) ) {
							temp = $.epoAPI.util.decodeHTML(
								$.epoAPI.template.html( tcAPI.templateEngine.tc_formatted_sale_price, {
									price: _f_regular_price,
									sale_price: _fprice,
									customer_price_format_wrap_start: customer_price_format_wrap_start,
									customer_price_format_wrap_end: customer_price_format_wrap_end,
									before_price_text: TMEPOJS.variation_price_before_price_text,
									after_price_text: TMEPOJS.variation_price_after_price_text
								} )
							);
							temp = {
								formatted: temp,
								raw: tc_totals_ob.product_total_price
							};
							temp = replace_suffixes( 'formatted', 'raw', temp, totalsHolder );
							temp = temp.formatted;
							update_native_html
								.html( temp )
								.show();
						} else {
							temp = $.epoAPI.util.decodeHTML(
								$.epoAPI.template.html( tcAPI.templateEngine.tc_formatted_price, {
									price: _fprice,
									customer_price_format_wrap_start: customer_price_format_wrap_start,
									customer_price_format_wrap_end: customer_price_format_wrap_end,
									before_price_text: TMEPOJS.variation_price_before_price_text,
									after_price_text: TMEPOJS.variation_price_after_price_text
								} )
							);
							temp = {
								formatted: temp,
								raw: tc_totals_ob.product_total_price
							};
							temp = replace_suffixes( 'formatted', 'raw', temp, totalsHolder );
							temp = temp.formatted;
							update_native_html
								.html( temp )
								.show();
						}
					}

					if ( TMEPOJS.tm_epo_change_original_price === 'yes' ) {
						if ( ! alternativeCart || main_product.find( '.cpf-bto-price' ).length === 0 ) {
							if ( currentEpoObject.associated_connect ) {
								nativeProductPriceSelector = currentEpoObject.main_product.find( tcAPI.associatedNativeProductPriceSelector );
							} else {
								nativeProductPriceSelector = $( tcAPI.nativeProductPriceSelector );
							}

							if ( nativeProductPriceSelector.length === 0 && main_product.is( '.tc-after-shop-loop' ) ) {
								nativeProductPriceSelector = main_product.closest( '.product' ).find( '.price' );
							}
							if ( nativeProductPriceSelector.data( 'tc-original-html' ) === undefined ) {
								nativeProductPriceSelector.data( 'tc-original-html', nativeProductPriceSelector.html() );
							}
							if ( product_total_price > 0 ) {
								if ( totalsHolder.data( 'is-on-sale' ) ) {
									temp = $.epoAPI.util.decodeHTML(
										$.epoAPI.template.html( tcAPI.templateEngine.tc_formatted_sale_price, {
											price: _f_regular_price,
											sale_price: _fprice,
											customer_price_format_wrap_start: customer_price_format_wrap_start,
											customer_price_format_wrap_end: customer_price_format_wrap_end,
											before_price_text: TMEPOJS.product_price_before_price_text,
											after_price_text: TMEPOJS.product_price_after_price_text
										} )
									);
									temp = {
										formatted: temp,
										raw: tc_totals_ob.product_total_price
									};
									temp = replace_suffixes( 'formatted', 'raw', temp, totalsHolder );
									temp = temp.formatted;
									nativeProductPriceSelector
										.html( temp )
										.show();
								} else {
									temp = $.epoAPI.util.decodeHTML(
										$.epoAPI.template.html( tcAPI.templateEngine.tc_formatted_price, {
											price: _fprice,
											customer_price_format_wrap_start: customer_price_format_wrap_start,
											customer_price_format_wrap_end: customer_price_format_wrap_end,
											before_price_text: TMEPOJS.product_price_before_price_text,
											after_price_text: TMEPOJS.product_price_after_price_text
										} )
									);
									temp = {
										formatted: temp,
										raw: tc_totals_ob.product_total_price
									};
									temp = replace_suffixes( 'formatted', 'raw', temp, totalsHolder );
									temp = temp.formatted;
									nativeProductPriceSelector
										.html( temp )
										.show();
								}
							} else if ( this_product_type && this_product_type !== 'composite' ) {
								if ( currentEpoObject.associated_connect ) {
									if ( currentEpoObject.variations_form.length ) {
										temp = currentEpoObject.this_epo_totals_container.data( 'variationIdElement' );
										if ( temp.length > 0 ) {
											temp = parseFloat( temp.val() );
											if ( ! ( ! temp || temp === 0 ) ) {
												nativeProductPriceSelector.html( $.epoAPI.util.decodeHTML( TMEPOJS.assoc_current_free_text ) );
											}
										}
									} else {
										nativeProductPriceSelector.html( $.epoAPI.util.decodeHTML( TMEPOJS.assoc_current_free_text ) );
									}
								} else {
									nativeProductPriceSelector.html( $.epoAPI.util.decodeHTML( TMEPOJS.current_free_text ) );
								}
							}
						}

						jWindow.trigger( 'tcEpoMaybeChangePriceHtml', {
							epo: currentEpoObject,
							alternativeCart: alternativeCart,
							this_product_type: this_product_type,
							cart: cart,
							bundleid: bundleid,
							totalsObject: tc_totals_ob,
							main_product: main_product,
							nativePrice: _fprice
						} );
					}
				}

				if ( alternativeCart ) {
					main_cart.trigger( {
						type: 'tm-epo-update',
						norules: 1
					} );
				} else {
					this_epo_totals_container.data( 'is_active', true );
				}
			} else {
				if ( currentEpoObject.associated_connect ) {
					nativeProductPriceSelector = currentEpoObject.main_product.find( tcAPI.associatedNativeProductPriceSelector );
					nativeProductPriceSelector.html( nativeProductPriceSelector.data( 'tc-original-html' ) );
				}

				tm_get_native_prices_block( cart ).each( function() {
					var $npb = $( this );
					if ( ! $npb.data( 'tm-original-html' ) ) {
						$npb.data( 'tm-original-html', $npb.html() );
					} else {
						$npb.html( $npb.data( 'tm-original-html' ) );
					}
				} );

				if ( rawProductPrice === 0 && TMEPOJS.tm_epo_remove_free_price_label === 'yes' ) {
					tm_get_native_prices_block( cart ).hide();
				} else if ( TMEPOJS.tm_epo_change_variation_price === 'yes' && ! ( hide_native_price === true && finalTotalBoxMode !== 'disable' ) ) {
					tm_get_native_prices_block( cart ).show();
				}

				totalsHolder.empty();

				if ( alternativeCart ) {
					main_cart.trigger( {
						type: 'tm-epo-update',
						norules: 1
					} );
				}
			}

			main_cart.trigger( 'tm-epo-after-update', {
				container: cartContainer
			} );

			jWindow.trigger( 'tc-epo-after-update', {
				epo: tc_totals_ob,
				totals_holder: totalsHolder,
				data: {
					epo_object: currentEpoObject,
					add_late_fields_prices: add_late_fields_prices,
					tm_set_price: tm_set_price,
					tm_set_price_totals: tm_set_price_totals,
					product_total_price: product_total_price,
					product_price: productPrice,
					qty: cartQty,
					bundleid: bundleid,
					currentCart: currentCart
				},
				tm_epo_js: TMEPOJS
			} );
		} );

		if ( this_product_type === 'variable' || this_product_type === 'variable-subscription' ) {
			epoVariationSection = epoHolder.find( '.tm-epo-variation-section' ).first();

			// Custom variation events
			epoVariationSection
				.find( '.tm-epo-reset-variation' )
				.off( 'click.cpfv' )
				.on( 'click.cpfv', function() {
					var field = $( this );
					var id = $.epoAPI.dom.id( field.attr( 'data-tm-for-variation' ) );
					var section = field.closest( '.cpf-type-variations' );
					var inputs = field.closest( '.cpf-element' ).find( '.tm-epo-variation-element' );
					var lis = field.closest( '.cpf-element' ).find( '.tmcp-field-wrap' );

					inputs.prop( 'checked', false );
					lis.removeClass( 'tc-active' );
					variationForm
						.find( "[data-attribute_name='attribute_" + id + "']" )
						.val( '' )
						.trigger( 'change' );
					variationForm.find( "[data-attribute_name='attribute_" + id + "']" ).trigger( 'focusin' );

					main_product
						.find( '.cpf-type-variations' )
						.not( section )
						.each( function( i, el ) {
							variationForm.find( "[data-attribute_name='attribute_" + $.epoAPI.dom.id( $( el ).find( '.tm-epo-variation-element' ).first().attr( 'data-tm-for-variation' ) ) + "']" ).trigger( 'focusin' );
						} );
					field.trigger( 'blur' );
					variationForm.trigger( 'woocommerce_update_variation_values_tmlogic' );
				} );

			epoVariationSection
				.find( 'input.tm-epo-variation-element,input.tm-epo-variation-element + span' )
				.off( 'mouseup.cpfv' )
				.on( 'mouseup.cpfv', function() {
					var field = $( this );
					var id;

					if ( field.is( 'span' ) ) {
						field = field.prev( 'input' );
					}
					if ( field.attr( 'disabled' ) ) {
						variationForm.find( '.reset_variations' ).trigger( 'click' );
					}
					id = $.epoAPI.dom.id( field.attr( 'data-tm-for-variation' ) );
					variationForm.find( "[data-attribute_name='attribute_" + id + "']" ).trigger( 'focusin' );
				} );

			epoVariationSection
				.off( 'click.' + eventName + '.tmepo', '.reset_variations, .tc-epo-element-variable-reset-variations' )
				.on( 'click.' + eventName + '.tmepo', '.reset_variations, .tc-epo-element-variable-reset-variations', { _epoObject: epoObject }, function( event ) {
					var _nativeProductPriceSelector;
					if ( TMEPOJS.tm_epo_change_original_price === 'yes' ) {
						if ( ! alternativeCart || main_product.find( '.cpf-bto-price' ).length === 0 ) {
							if ( event.data._epoObject.associated_connect ) {
								_nativeProductPriceSelector = event.data._epoObject.main_product.find( tcAPI.associatedNativeProductPriceSelector );
							} else {
								_nativeProductPriceSelector = $( tcAPI.nativeProductPriceSelector );
							}
							if ( _nativeProductPriceSelector.data( 'tc-original-html' ) ) {
								_nativeProductPriceSelector.html( _nativeProductPriceSelector.data( 'tc-original-html' ) );
							}
						}
					}
					variationForm.find( '.variations .reset_variations, .tc-epo-element-variable-reset-variations' ).first().trigger( 'click' );
				} );

			epoVariationSection
				.find( '.tm-epo-variation-element' )
				.off( 'change.cpfv tm_epo_variation_element_change' )
				.on( 'change.cpfv tm_epo_variation_element_change', function( e ) {
					var field = $( this );
					var id = $.epoAPI.dom.id( field.attr( 'data-tm-for-variation' ) );
					var value = field.val();
					var section = field.closest( '.cpf-type-variations' );
					var nativeSelect = variationForm.find( "[data-attribute_name='attribute_" + id + "']" );
					var exists;

					if ( field.closest( '.tm-epo-variation-section' ).is( '.tm-hidden' ) ) {
						return;
					}

					if ( ! ( e && e.type && e.type === 'tm_epo_variation_element_change' ) ) {
						exists = false;
						nativeSelect.each( function() {
							if ( this.value === value ) {
								exists = true;
								return false;
							}
						} );
						if ( ! exists ) {
							nativeSelect.trigger( 'focusin' );
						}
						nativeSelect.val( value ).trigger( 'change' );
					}

					if ( ! value ) {
						nativeSelect.trigger( 'focusin' );
					}

					main_product
						.find( '.cpf-type-variations' )
						.not( section )
						.each( function() {
							variationForm.find( '#' + $.epoAPI.dom.id( $( this ).find( '.tm-epo-variation-element' ).first().attr( 'data-tm-for-variation' ) ) ).trigger( 'focusin' );
						} );

					field.trigger( 'blur' );
					variationForm.trigger( 'woocommerce_update_variation_values_tmlogic' );
				} )
				.off( 'focusin.cpfv' )
				.on( 'focusin.cpfv', function() {
					var field = $( this );
					var id;

					if ( ! field.is( 'select' ) ) {
						return;
					}

					id = $.epoAPI.dom.id( field.attr( 'data-tm-for-variation' ) );
					variationForm.find( "[data-attribute_name='attribute_" + id + "']" ).trigger( 'focusin' );
					variationForm.trigger( 'woocommerce_update_variation_values_tmlogic' );
				} );

			variationForm.off( eventNamePrefix + 'found_variation.tmepo tm_fix_stock', '.single_variation_wrap' ).on( eventNamePrefix + 'found_variation.tmepo tm_fix_stock', '.single_variation_wrap', function() {
				fix_stock( $( this ), cartContainer );
			} );

			// update prices when a variation is found
			variationForm
				.off( eventNamePrefix + 'found_variation.tmepo' )
				.on( eventNamePrefix + 'found_variation.tmepo', function( event, variation ) {
					var form = $( this );

					totalsHolder.data( 'is-on-sale', variation.tc_is_on_sale );
					totalsHolder.data( 'regular-price', tm_set_backend_price( variation.display_regular_price, totalsHolder, variation ) );

					jWindow.trigger( 'tm-epo-found-variation', {
						epo: epoObject,
						totalsHolder: totalsHolder,
						totalsHolderContainer: totalsHolderContainer,
						currentCart: currentCart,
						variationForm: form,
						variation: variation
					} );

					found_variation_tmepo( {
						epoHolder: epoHolder,
						totalsHolder: totalsHolder,
						totalsHolderContainer: totalsHolderContainer,
						currentCart: currentCart,
						variationForm: form,
						variation: variation
					} );

					fix_stock( form, cartContainer );
				} )
				.off( eventNamePrefix + 'hide_variation.tmepo' )
				.on( eventNamePrefix + 'hide_variation.tmepo', { _epoObject: epoObject }, function( event ) {
					var _nativeProductPriceSelector;
					if ( TMEPOJS.tm_epo_change_original_price === 'yes' ) {
						if ( ! alternativeCart || main_product.find( '.cpf-bto-price' ).length === 0 ) {
							if ( event.data._epoObject.associated_connect ) {
								_nativeProductPriceSelector = event.data._epoObject.main_product.find( tcAPI.associatedNativeProductPriceSelector );
							} else {
								_nativeProductPriceSelector = $( tcAPI.nativeProductPriceSelector );
							}
							if ( _nativeProductPriceSelector.data( 'tc-original-html' ) ) {
								_nativeProductPriceSelector.html( _nativeProductPriceSelector.data( 'tc-original-html' ) );
							}
						}
					}
					totalsHolder.data( 'price', false );
					// Fancy product Designer
					totalsHolder.removeData( 'tcprice' );
					currentCart.trigger( {
						type: 'tm-epo-update',
						norules: 2
					} );
				} )
				.off( eventNamePrefix + 'check_variations.tmepo' )
				.on( eventNamePrefix + 'check_variations.tmepo', function() {
					var data = {};
					var chosen = 0;
					var reset = epoVariationSection.find( '.reset_variations' );

					variationForm.find( '.variations select, .tc-epo-variable-product-selector' ).each( function() {
						var attribute_name = $( this ).data( 'attribute_name' ) || $( this ).attr( 'name' );
						var value = $( this ).val() || '';

						if ( value.length > 0 ) {
							chosen++;
						}

						data[ attribute_name ] = value;
					} );

					if ( chosen > 0 ) {
						if ( reset.css( 'visibility' ) === 'hidden' ) {
							reset.css( 'visibility', 'visible' ).hide().fadeIn();
						}
					} else {
						reset.css( 'visibility', 'hidden' );
					}
				} )
				.trigger( eventNamePrefix + 'check_variations' );

			tm_custom_variations( epoObject, cartContainer, itemId, main_product, epoHolder );
		}

		selectSelector.trigger( 'tm-select-change-html' );
		tmQty.trigger( 'change.cpf', { init: 1 } );
		tmQuantity.trigger( 'showhide.cpfcustom' );
		epoFieldHasClearButton.filter( ':checked' ).trigger( 'cpfclearbutton' );

		jWindow.on( 'tm-do-epo-update', function() {
			// This must be run every time to get correct results for percent price types
			// if set norules then discount will not auto work upon chosing a variation
			currentCart.trigger( {
				type: 'tm-epo-update'
				//"norules": 2
			} );
		} );

		jWindow.trigger( 'tm-epo-init-events', {
			epo: {
				epo_id: epo_id,
				form: epoObject.form,
				currentCart: currentCart,
				cart_container: cartContainer,
				epo_holder: epoHolder,
				totals_holder_container: totalsHolderContainer,
				totals_holder: totalsHolder,
				main_cart: main_cart,
				main_epo_inside_form: main_epo_inside_form,
				product_id_selector: product_id_selector,
				epo_id_selector: epo_id_selector,
				product_id: product_id,
				this_epo_container: this_epo_container,
				this_totals_container: this_totals_container,
				this_epo_totals_container: this_epo_totals_container
			}
		} );

		jWindow.trigger( 'epoEventHandlers', {
			epo: epoObject,
			currentCart: currentCart,
			cartContainer: cartContainer,
			qtyElement: qtyElement,
			epoHolder: epoHolder,
			totalsHolderContainer: totalsHolderContainer,
			totalsHolder: totalsHolder,
			variationForm: variationForm,
			variation_id_selector: variation_id_selector,
			main_epo_inside_form: main_epo_inside_form,
			this_product_type: this_product_type,
			get_price_excluding_tax: get_price_excluding_tax,
			get_price_including_tax: get_price_including_tax
		} );

		// show final totals
		if ( finalTotalBoxMode !== 'disable' ) {
			totalsHolderContainer.addClass( 'tc-show' );
		}

		// show extra options
		jWindow.trigger( 'epo_options_before_visible' );

		if ( TMEPOJS.tm_epo_progressive_display === 'yes' ) {
			setTimeout( function() {
				epoHolder
					.css( 'opacity', 0 )
					.addClass( 'tc-show' )
					.animate(
						{
							opacity: 1
						},
						tcAPI.epoAnimationDelay,
						'easeOutExpo',
						function() {
							jWindow.trigger( 'epo_options_visible' );
							jWindow.trigger( 'tmlazy' );
						}
					);
			}, tcAPI.epoDelay );
		} else {
			epoHolder.addClass( 'tc-show' );
			jWindow.trigger( 'epo_options_visible' );
			jWindow.trigger( 'tmlazy' );
		}

		main_product.addClass( 'tc-init' );
	}

	function run_wc_variation_form_cpf( epoObject ) {
		var form = epoObject.variations_form;
		var cart = epoObject.main_cart;
		var this_epo_container = epoObject.this_epo_container;
		var eventName = epoObject.is_associated ? 'tc_variation_form.cpf' : 'wc_variation_form.cpf';

		form.off( eventName ).on( eventName, function() {
			if ( form.data( 'epo_loaded' ) ) {
				return;
			}

			// Start Condition Logic
			cpf_section_logic( this_epo_container );
			cpf_element_logic( this_epo_container );

			// Init field price rules
			$.tcepo.lateFieldsPrices[ epoObject.epoEventId ] = [];

			epoEventHandlers( epoObject );
			tm_set_upload_fields( epoObject );
			tm_product_image( epoObject );

			epoObject.noEpoUpdate = false;

			setTimeout( function() {
				run_cpfdependson( this_epo_container );
				tm_lazyload();
				jWindow.trigger( 'epo-after-init-in-timeout', { epo: epoObject } );
				cart.trigger( {
					type: 'tm-epo-update',
					rules: 'init'
				} );
			}, 10 );

			form.data( 'epo_loaded', true );
		} );

		if ( variationsFormIsLoaded ) {
			form.trigger( eventName );
		}

		jWindow.trigger( 'epo-after-init', { epo: epoObject } );
	}

	function detect_variation_swatches_interval( epoObject ) {
		var $id = requestAnimationFrame( function() {
			detect_variation_swatches_interval( epoObject );
		} );
		var obj = epoObject.variations_form;
		var bound = obj.data( 'bound' );
		var eventName = epoObject.is_associated ? 'tc_variation_form.cpf' : 'wc_variation_form.cpf';

		if ( bound ) {
			cancelAnimationFrame( $id );
			run_wc_variation_form_cpf( epoObject );
			obj.trigger( eventName );
		}
	}

	function manualInitEPO( epoObject, item, itemCart, itemEpoContainer, main_product ) {
		var epoObjectOriginal = $.extend( true, {}, epoObject );
		var product_id = itemEpoContainer.attr( 'data-product-id' );
		var epo_id = itemEpoContainer.attr( 'data-epo-id' );
		var product_id_selector = '.tm-product-id-' + product_id;
		var epo_id_selector = "[data-epo-id='" + epo_id + "']";
		var epoEventId = 'p' + product_id + 'e' + epo_id;
		var this_epo_container = $( '.tc-extra-product-options' + product_id_selector + epo_id_selector );
		var this_totals_container = $( '.tc-totals-form' + product_id_selector + epo_id_selector );
		var this_epo_totals_container = $( '.tc-epo-totals' + product_id_selector + epo_id_selector );

		epoObject.isManual = true;

		$.tcepo.formSubmitEvents[ epoEventId ] = [];
		$.tcepo.errorObject[ epoEventId ] = false;
		$.tcepo.initialActivation[ epoEventId ] = false;

		epoObject.product_id = product_id;
		epoObject.product_id_selector = product_id_selector;
		epoObject.epo_id = epo_id;
		epoObject.epo_id_selector = epo_id_selector;
		epoObject.epoEventId = epoEventId;
		epoObject.noEpoUpdate = true;
		epoObject.thisForm = item;
		epoObject.this_epo_container = this_epo_container;
		epoObject.this_totals_container = this_totals_container;
		epoObject.this_epo_totals_container = this_epo_totals_container;

		tm_lazyload();
		main_product.find( '.tm-collapse' ).tmtoggle();
		main_product.find( '.tm-section-link' ).tmsectionpoplink();

		tm_set_datepicker( item );
		setRangePickers( item );
		setRangePickersEvents();
		tm_set_repeaters( item, epoObject );
		tm_css_styles( item );
		tm_set_color_pickers( itemEpoContainer );
		tm_set_lightbox( itemEpoContainer.find( '.tc-lightbox-image' ).not( '.tm-extra-product-options-variations .radio-image' ) );

		// Start Condition Logic
		cpf_section_logic( itemEpoContainer );
		cpf_element_logic( itemEpoContainer );
		run_cpfdependson( itemEpoContainer );

		$.tcToolTip( item.find( '.tm-tooltip' ) );
		epoEventHandlers( epoObject, item, itemCart );

		epoObject.noEpoUpdate = false;

		itemCart.trigger( {
			type: 'tm-epo-update',
			norules: 2
		} );
		setTimeout( function() {
			epoObject.main_cart.trigger( {
				type: 'tm-epo-update',
				epoObject: epoObjectOriginal,
				norules: 1
			} );
		}, 200 );
		fix_stock( itemCart, item );
	}

	function tm_init_epo( main_product, is_quickview, product_id, epo_id, associated_connect, mainEpoObject, reactivate ) {
		// Holds the main cart when using Composite Products
		var main_cart = false;
		var main_epo_inside_form = false;
		var main_totals_inside_form = false;
		var epoEventId;
		var has_epo = typeof product_id !== 'undefined';
		var not_has_epo = false;
		var add_to_cart_field;
		var product_id_selector;
		var epo_id_selector;
		var this_epo_container;
		var this_totals_container;
		var this_epo_totals_container;
		var epo_object;
		var variations_form;
		var detect_variation_swatches = $( '.variation_form_section .variations-table' ).length > 0;
		var is_associated = false;

		main_product = $( main_product );

		if ( main_product.is( '.tc-init' ) && ! reactivate ) {
			return true;
		}

		if ( is_quickview ) {
			errorContainer = main_product;
		} else {
			errorContainer = $( window );
		}

		jWindow.trigger( 'tm-epo-init-start' );

		if ( ! has_epo ) {
			if ( main_product.is( '.product' ) ) {
				not_has_epo = true;
				has_epo = jBody.find( tcAPI.epoSelector ).length;
			}
		}

		// return if product has no extra options and the totals box is not enabled for all products
		if ( ! has_epo && TMEPOJS.tm_epo_enable_final_total_box_all === 'no' && ! main_product.is( '.tm-no-options-composite' ) ) {
			jWindow.trigger( 'tm-epo-init-end-no-options' );
			return;
		}

		// set the main_product variable again for products that have no extra options
		if ( not_has_epo ) {
			jWindow.trigger( 'tm-epo-init-no-options' );
			if ( main_product.is( '.product' ) && ! ( main_product.is( '.tm-no-options-pxq' ) || main_product.is( '.tm-no-options-composite' ) ) ) {
				main_product = jBody;
			}
		}

		if ( ! product_id ) {
			add_to_cart_field = main_product.find( tcAPI.addToCartSelector ).last();
			if ( add_to_cart_field.length > 0 ) {
				product_id = add_to_cart_field.val();
			} else {
				add_to_cart_field = $( '.tc-totals-form.tm-totals-form-main' );
				product_id = add_to_cart_field.attr( 'data-product-id' );
			}
			if ( ! product_id ) {
				product_id = '';
			}
		}

		if ( ! epo_id ) {
			epo_id = parseInt( main_product.find( 'input.tm-epo-counter' ).last().val(), 10 );

			if ( ! Number.isFinite( epo_id ) ) {
				epo_id = '';
			}
		}

		product_id_selector = '.tm-product-id-' + product_id;
		epo_id_selector = "[data-epo-id='" + epo_id + "']";
		this_epo_container = $( '.tc-extra-product-options' + product_id_selector + epo_id_selector );
		this_totals_container = $( '.tc-totals-form' + product_id_selector + epo_id_selector );
		this_epo_totals_container = $( '.tc-epo-totals' + product_id_selector + epo_id_selector );
		variations_form = main_product.find( '.variations_form' ).not( '.composite_component .variations_form' ).first();
		epoEventId = 'p' + product_id + 'e' + epo_id;

		if ( variations_form && variations_form.attr( 'data-product_id' ) ) {
			if ( variations_form.attr( 'data-product_id' ) !== product_id ) {
				variations_form = main_product.find( ".variations_form[data-product_id='" + product_id + "']" );
			}
		}

		main_cart = get_main_cart( main_product, main_product, 'form', product_id );
		if ( main_cart.length === 0 ) {
			if ( main_product.is( '.tc-shortcode-wrap' ) ) {
				main_cart = get_main_cart( this_totals_container, this_totals_container, '.tc-totals-form', product_id );
			} else if ( main_product.is( '.tc-epo-element-product-container' ) ) {
				main_cart = main_product.find( tcAPI.associatedEpoCart );
				// should never be 0
				if ( main_cart.length === 0 ) {
					main_cart = this_epo_container.parent( tcAPI.associatedEpoSelector );
					if ( main_cart.length === 0 ) {
						main_cart = main_product.find( '.tc-epo-element-product-container-right' );
					}
				}
				is_associated = true;
			}
		}

		if ( is_associated && variations_form.length === 0 && main_product.is( '.variations_form' ) ) {
			variations_form = main_product;
		}

		$.tcepo.formSubmitEvents[ epoEventId ] = [];
		$.tcepo.errorObject[ epoEventId ] = false;
		$.tcepo.initialActivation[ epoEventId ] = false;

		if ( main_cart.find( tcAPI.epoSelector ).length > 0 ) {
			main_epo_inside_form = true;
		}
		if ( main_cart.find( '.tc-totals-form' ).length > 0 ) {
			main_totals_inside_form = true;
		}

		if ( ! main_totals_inside_form ) {
			$.tcepo.formSubmitEvents[ epoEventId ][ $.tcepo.formSubmitEvents[ epoEventId ].length ] = {
				trigger: function() {
					return true;
				},
				on_true: function() {
					// hidden fields see totals.php
					var epos_hidden = $( '.tc-totals-form.tm-product-id-' + product_id + "[data-epo-id='" + epo_id + "']" ).tcClone();
					var formepo = $( '<div class="tm-hidden tm-formepo-normal"></div>' );

					main_cart.find( '.tm-formepo-normal' ).remove();
					formepo.append( epos_hidden );
					main_cart.append( formepo );
					return true;
				},
				on_false: function() {
					setTimeout( function() {
						$( '.tm-formepo-normal' ).remove();
					}, 100 );
				}
			};
		}
		if ( ! main_epo_inside_form ) {
			$.tcepo.formSubmitEvents[ epoEventId ][ $.tcepo.formSubmitEvents[ epoEventId ].length ] = {
				trigger: function() {
					return true;
				},
				on_true: function() {
					// visible fields
					var epos = $( tcAPI.epoSelector + '.tm-product-id-' + product_id + "[data-epo-id='" + epo_id + "']" )
						.tcClone()
						.addClass( 'formepo' );
					var formepo = $( '<div class="tm-hidden tm-formepo"></div>' );

					main_cart.find( '.tm-formepo' ).remove();
					formepo.append( epos );

					main_cart.append( formepo );
					return true;
				},
				on_false: function() {
					setTimeout( function() {
						$( '.tm-formepo' ).remove();
					}, 100 );
				}
			};
		}

		epo_object = {
			main_product: main_product,
			main_cart: main_cart,
			epo_id: epo_id,
			form: get_main_form( main_product ),
			main_epo_inside_form: main_epo_inside_form,
			product_id_selector: product_id_selector,
			epo_id_selector: epo_id_selector,
			product_id: product_id,
			this_epo_container: this_epo_container,
			this_totals_container: this_totals_container,
			this_epo_totals_container: this_epo_totals_container,
			qtySelector: tcAPI.qtySelector,
			manualInitEPO: manualInitEPO,
			epoEventId: epoEventId,
			variations_form: variations_form,
			has_epo: has_epo,
			is_quickview: is_quickview,
			is_associated: is_associated,
			is_epo_shortcode: this_epo_container.is( '.tc-shortcode' ),
			mainEpoObject: mainEpoObject,
			associated_connect: associated_connect,
			noEpoUpdate: true
		};

		$( epo_object.form ).data( 'epo_object', epo_object );
		this_epo_container.data( 'epo_object', epo_object );

		main_cart.data( 'product_id', product_id ).data( 'epo_id', epo_id ).data( 'product_id_selector', product_id_selector ).data( 'epo_id_selector', epo_id_selector );

		tm_set_checkboxes_rules( epo_object );
		tm_set_upload_rules( epo_object );
		tm_set_datepicker( this_epo_container );
		setRangePickers( this_epo_container );
		setRangePickersEvents();
		tm_set_repeaters( this_epo_container, epo_object );
		tm_set_url_fields();

		$.tcToolTip( this_epo_container.find( '.tm-tooltip' ) );

		this_epo_container.find( '.tm-collapse' ).tmtoggle();
		this_epo_container.find( '.tm-section-link' ).tmsectionpoplink();

		if ( reactivate ) {
			this_epo_container.addClass( 'reactivate' );
		}

		if ( variations_form.length > 0 ) {
			if ( reactivate ) {
				variations_form.data( 'epo_loaded', false );
			}
			this_epo_totals_container.data( 'price', false );
			if ( detect_variation_swatches ) {
				detect_variation_swatches_interval( epo_object );
			} else {
				run_wc_variation_form_cpf( epo_object );
			}
		} else {
			setTimeout( function() {
				// Start Condition Logic
				cpf_section_logic( this_epo_container );
				cpf_element_logic( this_epo_container );
				run_cpfdependson( this_epo_container );

				// Init field price rules
				$.tcepo.lateFieldsPrices[ epoEventId ] = [];
				epoEventHandlers( epo_object );
				tm_set_upload_fields( epo_object );
				tm_product_image( epo_object );

				tm_lazyload();

				jWindow.trigger( 'epo-after-init-in-timeout', { epo: epo_object } );

				main_cart.trigger( 'tm-epo-check-dpd' );
				epo_object.noEpoUpdate = false;
				main_cart.trigger( {
					type: 'tm-epo-update',
					rules: 'init'
				} );
			}, 20 );
			jWindow.trigger( 'epo-after-init', { epo: epo_object } );
		}

		tm_css_styles( this_epo_container );
		tm_set_color_pickers( this_epo_container );
		tm_set_lightbox( this_epo_container.find( '.tc-lightbox-image' ).not( '.tm-extra-product-options-variations .radio-image' ) );
		tm_theme_specific_actions( epo_object );
		tc_compatibility( epo_object );

		if ( ! is_associated ) {
			tm_floating_totals( this_epo_totals_container, is_quickview, main_cart );
			tm_form_submit_event( epo_object );
			epo_object = addShowHidetoEpoObject( epo_object, epoEventId, epo_object );
			tm_show_hide_add_to_cart_button( main_product, epo_object );
		}

		jWindow.trigger( 'tm-epo-init-end', { epo: epo_object } );

		return epo_object;
	}

	function manual_init( container, reactivate ) {
		var $this = $( container );
		var product_id = $this.attr( 'data-product-id' );
		var epo_id = $this.attr( 'data-epo-id' );
		var quickview_floating = false;
		var testForm = $this.parent();
		if ( ! testForm.is( 'form' ) ) {
			testForm = $this.closest( 'form' );
			if ( ! testForm.is( 'form' ) ) {
				testForm = $this.parent();
			}
		}
		testForm = testForm.parent();

		tm_init_epo( testForm, quickview_floating, product_id, epo_id, undefined, undefined, reactivate );
	}

	function init_epo_plugin() {
		var epo_container;
		var epo_options_container;

		if ( TMEPOJS.tm_epo_no_lazy_load === 'no' ) {
			$.extend( $.lazyLoadXT, {
				autoInit: false,
				selector: 'img.tmlazy',
				srcAttr: 'data-original',
				visibleOnly: false,
				updateEvent: $.lazyLoadXT.updateEvent + ' tmlazy'
			} );
		}
		/*
		 * tm-no-options-pxq = product has not options but the "Enable Final total box for all products" is on
		 * tm-no-options-composite = product is a composite product with no options but at least one of its bundles have options
		 */
		epo_container = $( '.tm-no-options-pxq, .tm-no-options-composite' );

		if ( epo_container.length > 0 ) {
			// Special cases
			// -------------
			// Price x Quantity display (.tm-no-options-pxq) & composite
			// without option but a component has extra options
			// (.tm-no-options-composite)

			epo_container.addClass( 'initializing' );

			epo_container.each( function( loop_index, product_wrap ) {
				var inputepoid;
				product_wrap = $( product_wrap );
				inputepoid = product_wrap.find( 'input.tm-epo-counter' );
				if ( inputepoid.length > 1 ) {
					inputepoid.each( function() {
						var currentepoid = $( this );
						var currentcart = currentepoid.closest( '.cart' );
						var currentcartparent = currentcart.parent();
						if ( currentcartparent.is( 'form' ) ) {
							currentcartparent = currentcartparent.parent();
						}

						if ( ! currentcartparent.is( '.initializing' ) ) {
							tm_init_epo( currentcartparent, false, undefined, parseInt( currentepoid.val(), 10 ) );
						}
					} );
				} else {
					tm_init_epo( product_wrap, false );
				}
				epo_container.removeClass( 'initializing' );
			} );
		}

		// The setTimeout is used for compatibility with
		// skeleton screen mode that some themes have

		setTimeout( function() {
			try {
				// new main way of calling tm_init_epo
				// -----------------------------------
				// Normal product pages

				epo_options_container = $( tcAPI.epoSelector ).not( tcAPI.associatedEpoSelector + ' ' + tcAPI.epoSelector + ', .tm-no-options-pxq, .tm-no-options-composite, .wc-product-table ' + tcAPI.epoSelector );

				if ( epo_options_container.length > 0 ) {
					epo_container.addClass( 'initializing' );
					epo_options_container.each( function() {
						var $this = $( this );
						var product_id = $this.attr( 'data-product-id' );
						var epo_id = $this.attr( 'data-epo-id' );
						var quickview_floating = false;
						var jProductWrap;
						var addInputs = false;
						var inputepoid;

						// First check if we are in a loop.
						jProductWrap = $this.closest( '.tc-after-shop-loop.tm-has-options' );

						if ( jProductWrap.length === 0 ) {
							// Check based on plugin add to cart selector.
							jProductWrap = $( tcAPI.tcAddToCartSelector + "[data-epo-id='" + epo_id + "'][value='" + product_id + "']" )
								.closest( 'form,.cart' )
								.first()
								.parent();
							// Check based on native add to cart selector.
							if ( jProductWrap.length === 0 ) {
								jProductWrap = $( tcAPI.addToCartSelector + "[value='" + product_id + "']" )
									.closest( 'form,.cart' )
									.first()
									.parent();
								if ( jProductWrap.length === 0 ) {
									// Check if we are in a shortcode
									jProductWrap = $this.closest( 'form,.cart' ).first().parent( '.tm-has-options' );
									if ( jProductWrap.length === 0 ) {
										if ( $this.is( '.tc-shortcode' ) ) {
											jProductWrap = $this.wrap( '<div class="tc-shortcode-wrap tc-wrap-' + epo_id + '"></div>' );
											jProductWrap = $this.parent();
										}
										if ( jProductWrap.length > 0 ) {
											addInputs = true;
										}
									}
								}
							}
						} else {
							addInputs = true;
						}

						if ( jProductWrap.length > 0 ) {
							if ( addInputs ) {
								// in shop (variation logic will not work here)
								quickview_floating = true;
								$this
									.closest( 'form,.cart' )
									.first()
									.append( $( '<input name="add-to-cart" value="' + product_id + '" type="hidden">' ) );
								$this.closest( 'form,.cart' ).first().append( $( '<input type="hidden" value="" class="variation_id" name="variation_id">' ) );
							}

							if ( jProductWrap.is( 'form' ) ) {
								jProductWrap = jProductWrap.parent();
							}

							inputepoid = jProductWrap.find( 'input.tm-epo-counter' );
							if ( inputepoid.length > 1 ) {
								inputepoid.each( function() {
									var currentepoid = $( this );
									var currentcart = currentepoid.closest( '.cart' );
									var currentcartparent = currentcart.parent();
									if ( currentcartparent.is( 'form' ) ) {
										currentcartparent = currentcartparent.parent();
									}

									if ( ! currentcartparent.is( '.initializing' ) ) {
										tm_init_epo( currentcartparent, quickview_floating, product_id, parseInt( currentepoid.val(), 10 ) );
									}
								} );
							} else {
								tm_init_epo( jProductWrap, quickview_floating, product_id, epo_id );
							}
						}
					} );
					epo_container.removeClass( 'initializing' );
				}
			} catch ( err ) {
				window.console.log( err );
				errorObject = err;
			}
		}, 1 );
	}

	$.tcepo.tm_init_epo = function( main_product, is_quickview, product_id, epo_id ) {
		tm_init_epo( main_product, is_quickview, product_id, epo_id );
	};

	$.tcepo.tmLazyloadContainer = function( container ) {
		tmLazyloadContainer = container;
	};

	jWindow.on( 'tc_manual_init', function( evt, container ) {
		var reactivate;
		if ( 'container' in container && 'reactivate' in container ) {
			reactivate = container.reactivate;
			container = container.container;
		}
		manual_init( container, reactivate );
	} );

	function tcAjaxAddToCart( $this ) {
		var _pid;
		var epos;
		var _cpf_product_price;
		var _dynamic_product_price;
		var _override_product_price;
		var form_prefix;
		var epoid;
		var obj;
		var data;
		var tcTotalsForm;

		currentAjaxButton = $this;
		_pid = currentAjaxButton.attr( 'data-product_id' );
		if ( undefined === _pid ) {
			_pid = currentAjaxButton.val();
		}

		if ( undefined !== _pid ) {
			epoid = currentAjaxButton.closest( '.cart' ).find( '.tm-epo-counter' ).val();
			if ( ! epoid ) {
				epoid = currentAjaxButton.attr( 'data-epo-id' );
				if ( ! epoid ) {
					return;
				}
			}
			if ( currentAjaxButton.closest( '.tm-has-options' ).length !== 1 && currentAjaxButton.attr( 'data-epo-id' ) === undefined ) {
				return;
			}

			obj = {};

			epos = $( '.tc-extra-product-options.tm-product-id-' + _pid + '[data-epo-id="' + epoid + '"]' );

			if ( epos.length > 1 ) {
				if ( epos.filter( '.formepo' ) ) {
					epos = epos.filter( '.formepo' );
				} else {
					epos = epos.first();
				}
			}

			if ( epos.length === 1 ) {
				tcTotalsForm = $( '.tc-totals-form.tm-product-id-' + _pid + '[data-epo-id="' + epoid + '"]' );
				_cpf_product_price = tcTotalsForm.find( '.cpf-product-price' ).val();
				_dynamic_product_price = tcTotalsForm.find( '.cpf-dynamic-product-price' ).val();
				_override_product_price = tcTotalsForm.find( '.cpf-override-product-price' ).val();
				form_prefix = tcTotalsForm.find( '.tc_form_prefix' ).val();

				obj = $.extend( obj, {
					tcajax: 1,
					tcaddtocart: _pid,
					cpf_product_price: _cpf_product_price,
					dynamic_product_price: _dynamic_product_price,
					override_product_price: _override_product_price
				} );
				if ( form_prefix ) {
					obj.tc_form_prefix = form_prefix;
				}
			}

			data = currentAjaxButton.data();
			TMEPOJS.ajaxbuttondata = $.epoAPI.util.deepCopyArray( data );
			TMEPOJS.ajaxbutton = currentAjaxButton;

			currentAjaxButton.removeData();
			currentAjaxButton.data( $.extend( data, epos.tcSerializeObject(), obj ) );

			TMEPOJS.ajaxdata = {
				epos: epos,
				_pid: _pid,
				data: data
			};
		}
	}

	function ajaxAddToCartFunctions() {
		// Betheme ajax add to cart.
		$( document ).on( 'click.tcajax', 'body.mfn-ajax-add-to-cart .single_add_to_cart_button:not(.disabled)', function() {
			var formepo;
			var $this = $( this );
			tcAjaxAddToCart( $this );
			TMEPOJS.ajaxdata.cart = $this.closest( '.cart' );
			TMEPOJS.ajaxdata.cart.find( '.tm-formepo' ).remove();
			formepo = $( "<div class='tm-hidden tm-formepo'></div>" );
			formepo.append( TMEPOJS.ajaxdata.epos.tcClone().addClass( 'formepo' ) );
			$this.after( formepo );
		} );

		jBody.on( 'click.tcajax', '.ajax_add_to_cart', function() {
			tcAjaxAddToCart( $( this ) );
			if ( undefined !== TMEPOJS.ajaxdata ) {
				delete TMEPOJS.ajaxdata;
			}
		} );

		$( document.body ).on( 'added_to_cart', function() {
			if ( TMEPOJS.ajaxbuttondata && TMEPOJS.ajaxbutton instanceof $ ) {
				TMEPOJS.ajaxbutton.removeData();
				TMEPOJS.ajaxbutton.data( TMEPOJS.ajaxbuttondata );
			}
		} );
	}

	// This needs to run here to register our event handlers before WooCommerce.
	ajaxAddToCartFunctions();

	// document ready
	$( function() {
		tcAPI = $.epoAPI.applyFilter( 'tc_api', tcAPI );

		jWindow.on( 'lazyLoadXToncomplete', function() {
			$( '.tm-owl-slider' ).each( function() {
				$( this ).trigger( 'refresh.owl.carousel' );
			} );
		} );

		jWindow.on( 'tc_init_epo_plugin', function( evt ) {
			init_epo_plugin( evt );
		} );

		jWindow.on( 'tcShowLastError', function() {
			window.console.log( errorObject );
		} );

		$.ajaxPrefilter( function( options, originalOptions ) {
			var found = false;
			var hashes;
			var hash;
			var i;
			var params;
			var $thisbutton;
			var _data;
			var _urldata;
			var _pid;
			var epos;
			var _cpf_product_price;
			var _dynamic_product_price;
			var _override_product_price;
			var form_prefix;
			var obj;
			var oldData;
			var formData;
			var can_be_added = false;
			var associativeArray = {};
			var tcTotalsForm;

			if ( TMEPOJS.tm_epo_enable_in_shop === 'yes' ) {
				hashes = options.url.split( '?' );

				if ( hashes && hashes.length >= 1 ) {
					hashes = hashes[ 1 ];
					if ( hashes ) {
						hash = hashes.split( '&' );
						for ( i = 0; i < hash.length; i += 1 ) {
							params = hash[ i ].split( '=' );
							if ( params.length >= 1 ) {
								if ( params[ 0 ] && params[ 1 ] && params[ 0 ] === 'wc-ajax' && params[ 1 ] === 'add_to_cart' ) {
									found = true;
								}
							}
						}
						if ( found ) {
							options.originalsuccess = options.success;
							options.success = function( response ) {
								if ( response && response.error && response.product_url ) {
									if ( currentAjaxButton && currentAjaxButton.length === 1 ) {
										$thisbutton = currentAjaxButton;
									}
									$thisbutton = $( ".ajax_add_to_cart[data-product_id='" + originalOptions.data.product_id + "']" );
									$thisbutton.removeClass( 'added' );
									$thisbutton.removeClass( 'loading' );
								} else {
									options.originalsuccess.call( null, response );
								}
							};
						}
					}
				}
			}

			if ( FormData && originalOptions.data ) {
				_data = originalOptions.data;
				if ( typeof originalOptions.data === 'string' ) {
					_data = $.epoAPI.util.parseParams( originalOptions.data );
				}
				if ( typeof _data[ 0 ] === 'object' ) {
					_data.forEach( function( item ) {
						associativeArray[ item.name ] = item.value;
					} );
					_data = associativeArray;
				}

				_urldata = [];
				if ( originalOptions.url && originalOptions.url.indexOf ) {
					_urldata = $.epoAPI.util.parseParams( originalOptions.url.slice( originalOptions.url.indexOf( '?' ) + 1 ) );
				}

				if ( 'quantity' in _data && _data.tcaddtocart && ( _data.product_id || _data[ 'add-to-cart' ] || _urldata.product_id || _urldata[ 'add-to-cart' ] ) ) {
					can_be_added = true;
					_pid = _data.tcaddtocart;
				} else if ( Array.isArray( _data ) ) {
					can_be_added = _data.some( function( item ) {
						return item.name === 'quantity';
					} ) && _data.some( function( item ) {
						return item.name === 'add-to-cart';
					} ) && _data.some( function( item ) {
						return item.name === 'tcaddtocart';
					} );
					_pid = _data.find( function( item ) {
						return item.name === 'tcaddtocart';
					} );
					if ( _pid ) {
						_pid = _pid.value;
					} else {
						_pid = false;
					}
				}

				if ( can_be_added ) {
					if ( currentAjaxButton && currentAjaxButton.length === 1 && currentAjaxButton.closest( '.tm-has-options' ).length === 1 ) {
						epos = currentAjaxButton.closest( '.tm-has-options' ).find( '.tc-extra-product-options.tm-product-id-' + _pid );
					} else {
						epos = $( '.tc-extra-product-options.tm-product-id-' + _pid );
					}

					if ( epos.length > 1 ) {
						if ( epos.filter( '.formepo' ) ) {
							epos = epos.filter( '.formepo' );
						} else {
							epos = epos.first();
						}
					}
					if ( epos.length === 1 ) {
						tcTotalsForm = $( '.tc-totals-form.tm-product-id-' + _pid );
						_cpf_product_price = tcTotalsForm.find( '.cpf-product-price' ).val();
						_dynamic_product_price = tcTotalsForm.find( '.cpf-dynamic-product-price' ).val();
						_override_product_price = tcTotalsForm.find( '.cpf-override-product-price' ).val();
						form_prefix = tcTotalsForm.find( '.tc_form_prefix' ).val();
						obj = {
							tcajax: 1,
							tcaddtocart: _pid,
							cpf_product_price: _cpf_product_price,
							dynamic_product_price: _dynamic_product_price,
							override_product_price: _override_product_price
						};
						if ( form_prefix ) {
							obj.tc_form_prefix = form_prefix;
						}

						oldData = $.epoAPI.util.parseParams( options.data, true );
						oldData = $.extend( oldData, epos.tcSerializeObject(), obj );
						formData = new FormData();

						Object.keys( oldData ).forEach( function( key ) {
							if ( key ) {
								formData.append( key, oldData[ key ] );
							}
						} );

						epos.find( ':file' )
							.toArray()
							.forEach( function( el ) {
								for ( i = 0; i < $( el )[ 0 ].files.length; i++ ) {
									if ( ! el.multiple ) {
										formData.delete( $( el ).attr( 'name' ) );
									}
									formData.append( $( el ).attr( 'name' ), $( el )[ 0 ].files[ i ] );
								}
							} );

						options.data = formData;
						options.contentType = false;
						options.cache = false;
						options.processData = false;
					}
				}
			}
		} );

		jDocument.ajaxSuccess( function( event, request, settings ) {
			// quickview plugins
			var qv_container = TMEPOJS.quickview_array || 'null';
			var fromaddons = TMEPOJS.quickview_container || 'null';
			var added = {};
			var selectors;
			var container;
			var product_id;
			var epo_id;
			var noProductCheck;
			var testContainer;
			var parsedUrl;
			var time = 1;
			var detectContainer;
			var requestContainer;

			if ( undefined !== TMEPOJS.ajaxdata ) {
				TMEPOJS.ajaxdata.cart.find( '.tm-formepo' ).remove();
				delete TMEPOJS.ajaxdata;
			}

			parsedUrl = $.epoAPI.util.parseParams( settings.data );
			if ( parsedUrl.action === 'wc_epo_get_associated_product_html' ) {
				return;
			}

			$( '.tm-formepo-normal' ).remove();
			$( '.tm-formepo' ).remove();

			//fix for menu cart pop up
			$( '.tm-cart-link' ).tmpoplink();

			qv_container = $.epoAPI.util.parseJSON( qv_container );

			fromaddons = $.epoAPI.util.parseJSON( fromaddons );

			for ( selectors in fromaddons ) {
				if ( Object.prototype.hasOwnProperty.call( fromaddons, selectors ) ) {
					added[ fromaddons[ selectors ][ 0 ] ] = $( fromaddons[ selectors ][ 1 ] );
				}
			}

			$.extend( qv_container, added );

			detectContainer = function() {
				var keyfound = false;
				Object.keys( qv_container ).forEach( function( key ) {
					if ( $( qv_container[ key ] ).length ) {
						keyfound = key;
					}
				} );
				return keyfound;
			};
			requestContainer = function( times ) {
				var id = requestAnimationFrame( function() {
					setTimeout( function() {
						requestContainer( times - 1 );
					}, 200 );
				} );
				var key = detectContainer();
				var epoContainer;

				if ( key || 0 === times ) {
					cancelAnimationFrame( id );
					if ( key ) {
						noProductCheck = false;
						container = $( qv_container[ key ] );

						if ( key === 'woothemes_quick_view' && container.is( '.fusion-woocommerce-quick-view-container' ) ) {
							return true;
						}

						if ( key === 'fusion_quick_view_load' && container.find( tcAPI.epoSelector ).length === 0 ) {
							return true;
						}

						if ( key === 'woodmart_quick_shop' ) {
							parsedUrl = $.epoAPI.util.parseParams( settings.url );
							if ( parsedUrl.action === 'woodmart_quick_shop' ) {
								testContainer = $( $.epoAPI.util.escapeSelector( qv_container[ key ] + '.post-' + parsedUrl.id ) );
								if ( testContainer.length ) {
									container = testContainer;
									noProductCheck = true;
								}
							}
						}
						if ( key === 'woodmart_quick_view' ) {
							parsedUrl = $.epoAPI.util.parseParams( settings.url );
							if ( parsedUrl.action === 'woodmart_quick_view' ) {
								testContainer = $( $.epoAPI.util.escapeSelector( qv_container[ key ] + '.post-' + parsedUrl.id ) );
								if ( testContainer.length ) {
									container = testContainer;
									noProductCheck = true;
								}
							}
						}

						if ( key === 'quickview_pro' ) {
							parsedUrl = settings.url.split( '/' );
							if ( parsedUrl.length ) {
								testContainer = $( $.epoAPI.util.escapeSelector( qv_container[ key ] + ' .post-' + parsedUrl[ parsedUrl.length - 1 ] ) );
								if ( testContainer.length ) {
									container = testContainer;
									noProductCheck = true;
								}
							}
						}

						if ( key === 'exwoofood_booking_info' || key === 'wqv_popup_content' || key === 'wp_food' || key === 'jet_popup_get_content' ) {
							noProductCheck = true;
						}

						if ( key === 'woofood' && settings.data ) {
							parsedUrl = $.epoAPI.util.parseParams( settings.data );
							if ( parsedUrl.action === 'woofood_quickview_ajax' ) {
								testContainer = testContainer = container.find( 'form' ).parent();
								if ( testContainer.length ) {
									container = testContainer;
									noProductCheck = true;
								}
							}
						}

						if ( container.find( '.product' ).length === 0 && container.is( '.product' ) ) {
							noProductCheck = true;
						}

						if ( ( container.find( '.product' ).length > 0 || noProductCheck ) ) {
							container.removeClass( 'tc-init' );

							if (
								key === 'fusion_quick_view_load' ||
								key === 'jet_popup_get_content' ||
								key === 'wp_food' ||
								key === 'woodmart_quick_shop' ||
								key === 'woodmart_quick_view' ||
								key === 'lightboxpro' ||
								key === 'jckqv_quick_view' ||
								key === 'yith_quick_view' ||
								key === 'theme_flatsome'
							) {
								variationsFormIsLoaded = true;
							}
							tmLazyloadContainer = container;
							if ( tmLazyloadContainer.length > 1 ) {
								while ( tmLazyloadContainer.length > 1 ) {
									tmLazyloadContainer = tmLazyloadContainer.filter( function() {
										return $( this ).find( tcAPI.epoSelector ).length > 0;
									} );
									if ( tmLazyloadContainer.length === 0 ) {
										break;
									}
								}
								if ( tmLazyloadContainer.length === 0 ) {
									setTimeout( function() {
										requestContainer( 10 );
									}, 1000 );

									return true;
								}
							}

							epoContainer = tmLazyloadContainer.find( tcAPI.epoSelector );
							if ( epoContainer.length === 0 ) {
								delete qv_container[ key ];
								requestContainer( 10 );
								return true;
							}

							if ( key === 'fusion_quick_view_load' ) {
								time = 1400;
							}

							setTimeout( function() {
								product_id = epoContainer.attr( 'data-product-id' );
								epo_id = epoContainer.attr( 'data-epo-id' );
								if ( key === 'woodmart_quick_shop' ) {
									container.addClass( 'has-options' );
								}

								// Reset element cache
								tcAPI.getElementFromFieldCache = [];
								tm_init_epo( tmLazyloadContainer, true, product_id, epo_id );
								jWindow.trigger( 'tmlazy' );
								jWindow.trigger( 'tm_epo_loaded_quickview' );
								if ( $.jMaskGlobals ) {
									tmLazyloadContainer.find( $.jMaskGlobals.maskElements ).each( function() {
										var t = $( this );

										if ( t.attr( 'data-mask' ) ) {
											t.mask( t.attr( 'data-mask' ) );
										}
									} );
								}
							}, time );
						}
					}
				}
			};
			requestContainer( 10 );
		} );

		init_epo_plugin();

		$( '.tm-cart-link' ).tmpoplink();
		jBody.on( 'updated_checkout wc_fragments_loaded wc_fragments_refreshed wc_fragment_refresh', function() {
			$( '.tm-cart-link' ).tmpoplink();
		} );

		jWindow.trigger( 'tmlazy' );

		jWindow.trigger( 'tm_epo_loaded' );
	} );

	function associated_table_item() {
		$( '.tc-associated-table-product td.product-name' ).each( function() {
			var el = $( this );
			if ( el.find( '.product-name' ).length === 0 ) {
				el.wrapInner( $( '<div class="tc-associated-table-product-indent"></div>' ) );
			}
		} );
	}

	// Associated product cart indent
	jBody.on( 'updated_checkout updated_cart_totals', function() {
		associated_table_item();
	} );

	associated_table_item();

	// document ready
	$( function() {
		// Remove accept attribute from upload buttons when in Facebook or Instagram internral browser
		var ua = navigator.userAgent || navigator.vendor || window.opera;
		ua = ( ua.indexOf( 'FBAN' ) > -1 ) || ( ua.indexOf( 'FBAV' ) > -1 ) || ( ua.indexOf( 'Instagram' ) > -1 );
		if ( ua ) {
			$( '.tmcp-upload' ).removeAttr( 'accept' );
		}

		// Fix several custom quantity buttons on themes
		jDocument.on( 'click', '.quantity .jckqv-qty-spinner, .quantity .ui-spinner-button', function() {
			$( this ).closest( '.quantity' ).find( tcAPI.qtySelector ).trigger( 'change' );
		} );

		// bulk variations forms plugin
		$( '#wholesale_form' ).on( 'submit', function() {
			var _product_id = $( 'form.cart' ).find( tcAPI.addToCartSelector ).val();
			// visible fields
			var epos = $( tcAPI.epoSelector + ".tm-cart-main[data-product-id='" + _product_id + "']" ).tcClone();
			// hidden fields see totals.php
			var epos_hidden = $( ".tm-totals-form-main[data-product-id='" + _product_id + "']" ).tcClone();
			var formepo = $( "<div class='tm-hidden tm-formepo'></div>" );

			formepo.append( epos );
			formepo.append( epos_hidden );
			$( this ).append( formepo );
			return true;
		} );

		// Disable quote button if option validation fails
		jDocument.on( 'click', '#add_to_quote', function( e ) {
			var form;
			var epo_id;
			var epos;

			if ( TMEPOJS && TMEPOJS.tm_epo_global_enable_validation === 'yes' ) {
				form = $( this ).parents( 'form' );
				epo_id = parseInt( form.find( '.tm-epo-counter' ).val(), 10 );
				epos = $( tcAPI.epoSelector + "[data-epo-id='" + epo_id + "']" );

				// not validated
				if ( TMEPOJS.tm_epo_global_enable_validation === 'yes' && $.tc_validator && form.length > 0 && epos.length > 0 && ! form.tc_validate().form() ) {
					e.stopImmediatePropagation();
				}
			}
		} );

		// PayPal for WooCommerce (PayPal Express Checkout button fix)
		$( '.single_add_to_cart_button.paypal_checkout_button' ).on( 'click', function( event ) {
			// this is the selector used by the paypal checkout plugin
			var form = $( '.cart' );
			var validator;

			if ( form.data( 'tc_validator' ) ) {
				validator = form.data( 'tc_validator' );
				if ( validator.errorList ) {
					event.stopImmediatePropagation();
				}
			}
		} );

		$( '.wc-product-table' ).on( 'init.wcpt', function( event, table ) {
			table.$table.find( tcAPI.epoSelector ).addClass( 'hidden' );
			table.$table.find( 'thead tr' ).append( '<th>&nbsp;</th>' );
			setTimeout( function() {
				table.$table.find( '.cart:not(.cart_group)' ).each( function() {
					var epo = $( this ).find( tcAPI.epoSelector );
					var tr = epo.closest( 'tr' );

					$( "<td class='wc-product-table-epo'></td>" ).appendTo( tr ).append( epo );
					$( window ).trigger( 'tc_manual_init', epo );
					epo.removeClass( 'hidden' );
				} );
			}, 500 );
		} );
	} );
}( window, document, window.jQuery ) );
