/**
 * jquery.tctooltip.js
 *
 * @param {Window} window - The window object representing the browser window.
 * @param {jQuery} $      - The jQuery object.
 * @version: v1.1
 * @author: themeComplete
 *
 * Created by themeComplete
 *
 * Copyright (c) 2021 themeComplete http://themecomplete.com
 */
( function( window, $ ) {
	'use strict';

	var ToolTip = function( dom, options ) {
		this.targets = $( dom );

		this.settings = $.extend( {}, $.fn.tcToolTip.defaults, options );

		if ( this.targets.length > 0 ) {
			this.init();
			return this;
		}

		return false;
	};

	var TMEPOJS;

	// document ready
	$( function() {
		TMEPOJS = window.TMEPOJS || { tm_epo_global_tooltip_max_width: '340px' };
	} );

	ToolTip.prototype = {
		constructor: ToolTip,

		removeTooltip: function( target, tooltip ) {
			var settings = this.settings;

			if ( target.data( 'is_moving' ) ) {
				return;
			}

			tooltip.removeClass( settings.fadin ).addClass( settings.fadeout );

			tooltip.animate(
				{
					opacity: 0
				},
				settings.speed,
				function() {
					$( this ).remove();
				}
			);

			if ( target.data( 'tmtip-title' ) && target.data( 'tm-tip-html' ) === undefined && ! target.attr( 'data-tm-tooltip-html' ) ) {
				target.attr( 'title', target.data( 'tmtip-title' ) );
			}

			$( window ).off( 'scroll.tcToolTip resize.tcToolTip' );

			if ( settings.onetime ) {
				this.destroy();
			}
		},

		initTooltip: function( target, tooltip, nofx ) {
			var settings = this.settings;
			var tip;
			var scroll;
			var pos_left;
			var pos_top;
			var pos_from_top;
			var original_pos_left;

			if ( target && tooltip && target.length === 1 && tooltip.length === 1 && target.data( 'tm-has-tm-tip' ) === 1 ) {
				if ( nofx === 1 ) {
					if ( target.data( 'tm-tip-html' ) !== undefined ) {
						tip = target.data( 'tm-tip-html' );
					} else if ( target.attr( 'data-tm-tooltip-html' ) ) {
						tip = target.attr( 'data-tm-tooltip-html' );
					} else {
						tip = target.attr( 'title' );
					}

					tooltip.html( tip );
					target.data( 'is_moving', true );
				}

				tooltip.find( 'aside' ).hide();

				if ( TMEPOJS.tm_epo_global_tooltip_max_width === '' ) {
					// 50: average scrollbar width. Needed to avoid flickering width issues on mobile.
					if ( $( window ).width() <= tooltip.outerWidth() * 1.2 ) {
						tooltip.css( 'max-width', ( $( window ).width() / 1.2 ) + 'px' );
					} else {
						tooltip.css( 'max-width', '340px' );
					}
				} else {
					if ( TMEPOJS.tm_epo_global_tooltip_max_width.isNumeric() ) {
						TMEPOJS.tm_epo_global_tooltip_max_width = TMEPOJS.tm_epo_global_tooltip_max_width + 'px';
					}
					tooltip.css( 'max-width', TMEPOJS.tm_epo_global_tooltip_max_width );
				}

				tooltip.find( 'aside' ).show();

				tooltip.css( {
					left: '',
					right: '',
					top: ''
				} );

				scroll = $.epoAPI.dom.scroll();
				pos_left = target.offset().left + ( target.outerWidth() / 2 ) - ( tooltip.outerWidth() / 2 );
				original_pos_left = pos_left;

				if ( pos_left < 0 ) {
					pos_left = target.offset().left + ( target.outerWidth() / 2 ) - 20;
					tooltip.addClass( 'left' );
				} else {
					tooltip.removeClass( 'left' );
				}
				if ( original_pos_left >= 0 && pos_left + tooltip.outerWidth() > $( window ).width() ) {
					pos_left = target.offset().left - tooltip.outerWidth() + ( target.outerWidth() / 2 ) + 20;
					if ( pos_left < 0 ) {
						pos_left = pos_left - 10;
						tooltip.css( 'max-width', 'calc(' + tooltip.css( 'max-width' ) + ' - ' + Math.abs( pos_left ) + 'px)' );
						pos_left = 10;
					}
					tooltip.addClass( 'right' );
				} else {
					tooltip.removeClass( 'right' );
				}

				tooltip.css( {
					left: pos_left,
					right: 'auto',
					top: pos_top
				} );
				pos_top = target.offset().top - tooltip.outerHeight() - 10;
				pos_from_top = target.offset().top - scroll.top - tooltip.outerHeight() - 10;

				if ( pos_top < 0 || pos_from_top < 0 ) {
					pos_top = target.offset().top + target.outerHeight() + 10;
					tooltip.addClass( 'top' );
					tooltip.removeClass( 'bottom' );
				} else {
					tooltip.removeClass( 'top' );
					tooltip.addClass( 'bottom' );
				}

				$( window ).trigger( 'tm_tooltip_show' );

				if ( nofx ) {
					tooltip.css( {
						left: pos_left,
						top: pos_top
					} );
					target.data( 'is_moving', false );
				} else {
					tooltip
						.css( {
							left: pos_left,
							top: pos_top
						} )
						.removeClass( settings.fadeout )
						.addClass( settings.fadin );
				}
			}
		},

		show: function( target ) {
			var tooltip;
			var tip;
			var img;
			var settings = this.settings;

			if ( target.data( 'is_moving' ) ) {
				return;
			}

			if ( target.data( 'tm-has-tm-tip' ) === 1 ) {
				if ( target.data( 'tm-tip-html' ) !== undefined ) {
					tip = target.data( 'tm-tip-html' );
					if ( target.attr( 'title' ) ) {
						target.data( 'tmtip-title', target.attr( 'title' ) );
					}
					target.removeAttr( 'title' );
				} else if ( target.attr( 'data-tm-tooltip-html' ) ) {
					tip = target.attr( 'data-tm-tooltip-html' );
					if ( target.attr( 'title' ) ) {
						target.data( 'tmtip-title', target.attr( 'title' ) );
					}
					target.removeAttr( 'title' );
				} else {
					tip = target.attr( 'title' );
				}

				if ( tip !== undefined ) {
					$( '#tm-tooltip' ).remove();
					if ( ! settings.tipclass ) {
						settings.tipclass = '';
					} else {
						settings.tipclass = ' ' + settings.tipclass;
					}
					tooltip = $( '<div id="tm-tooltip" class="tm-tip tm-animated' + settings.tipclass + '"></div>' );
					tooltip.css( 'opacity', 0 ).html( tip ).appendTo( 'body' );

					img = tooltip.find( 'img' );
					if ( img.length > 0 ) {
						img.on( 'load', this.initTooltip.bind( this, target, tooltip ) );
					}

					this.initTooltip( target, tooltip );

					$( window ).on( 'scroll.tcToolTip resize.tcToolTip', this.initTooltip.bind( this, target, tooltip ) );

					target.data( 'is_moving', false );

					target.on( 'tmmovetooltip', this.initTooltip.bind( this, target, tooltip, 1 ) );
					target.on( 'mouseleave.tc tmhidetooltip', this.removeTooltip.bind( this, target, tooltip ) );

					target.closest( 'label' ).on( 'mouseleave.tc tmhidetooltip', this.removeTooltip.bind( this, target, tooltip ) );

					tooltip.on( 'click', this.removeTooltip.bind( this, target, tooltip ) );
				}
			}

			return false;
		},

		destroy: function() {
			if ( this.targets.length > 0 ) {
				this.targets.toArray().forEach( function( element ) {
					var target = $( element );
					target.closest( 'off' ).on( 'mouseleave.tc tmhidetooltip' );
					target.off( 'tc-tooltip-html-changed tmmovetooltip tmhidetooltip mouseenter.tc mouseleave.tc tmshowtooltip.tc' );
					target.removeData( 'tmtip-title' );
					target.removeData( 'tm-tip-html' );
				} );
				this.targets.removeData( 'tctooltip tm-has-tm-tip is_moving' );
			}
		},

		init: function() {
			var that = this;
			var settings = this.settings;

			if ( this.targets.length > 0 ) {
				this.targets.toArray().forEach( function( element ) {
					var target;
					var is_swatch;
					var is_swatch_desc;
					var is_swatch_lbl_desc;
					var is_swatch_img;
					var is_swatch_img_lbl;
					var is_swatch_img_desc;
					var is_swatch_img_lbl_desc;
					var tip;
					var label;
					var desc;
					var descHTML;
					var get_img_src;
					var findlabel;
					var is_hide_label;
					var findlabelText;

					target = $( element );
					tip = settings.tip || undefined;

					if ( target.data( 'tm-has-tm-tip' ) === undefined ) {
						is_swatch = target.attr( 'data-tm-tooltip-swatch' );
						is_swatch_desc = target.attr( 'data-tm-tooltip-swatch-desc' );
						is_swatch_lbl_desc = target.attr( 'data-tm-tooltip-swatch-lbl-desc' );
						is_swatch_img = target.attr( 'data-tm-tooltip-swatch-img' );
						is_swatch_img_lbl = target.attr( 'data-tm-tooltip-swatch-img-lbl' );
						is_swatch_img_desc = target.attr( 'data-tm-tooltip-swatch-img-desc' );
						is_swatch_img_lbl_desc = target.attr( 'data-tm-tooltip-swatch-img-lbl-desc' );

						target.data( 'tm-has-tm-tip', 1 );

						if ( target.attr( 'data-original' ) !== undefined ) {
							get_img_src = target.attr( 'data-original' );
						} else if ( target.attr( 'src' ) !== undefined ) {
							if ( target.attr( 'data-src' ) !== undefined ) {
								get_img_src = target.attr( 'data-src' );
							} else {
								get_img_src = target.attr( 'src' );
							}
						} else {
							get_img_src = target[ 0 ].src;
						}

						label = target.closest( '.tmcp-field-wrap' );
						if ( label.is( '.tc-epo-element-product-holder' ) ) {
							label = target.closest( '.cpf-element' );
						}
						if ( label.length === 0 ) {
							label = target.closest( '.cpf-element' );
						}
						// If the target is inside a product element and there is a closest section
						if ( label.is( '.cpf-type-product' ) && target.closest( '.tm-epo-element-label' ).length === 0 && target.closest( '.cpf-section' ).length ) {
							label = target.closest( '.cpf-section' ).find( '.tc-section-inner-wrap .tm-section-description.tm-description' );
						}
						if ( label.length === 0 ) {
							label = target.closest( '.cpf-section' ).find( '.tc-section-inner-wrap .tm-section-description.tm-description' );
						}
						findlabel = label.find( '.tm-tip-html' );
						if ( findlabel.length === 0 ) {
							findlabel = label.find( '.checkbox-image-label,.checkbox-image-label-inline,.radio-image-label,.radio-image-label-inline' );
						}

						if ( findlabel.length === 0 ) {
							findlabel = label.next( '.checkbox-image-label,.checkbox-image-label-inline,.radio-image-label,.radio-image-label-inline,.tm-tip-html' );
						}
						if ( findlabel.length === 0 && label.is( '.tm-description' ) ) {
							findlabel = label;
						}
						label = findlabel;

						findlabel = $( label );
						findlabelText = findlabel.find( '.tc-label-text' );
						if ( ! findlabelText.length ) {
							findlabelText = findlabel;
						}

						is_hide_label = target.attr( 'data-tm-hide-label' ) === 'yes' || target.attr( 'data-tm-hide-label' ) === undefined || findlabel.is( '.tm-tip-html' );

						descHTML = '';
						desc = target.closest( '.tmcp-field-wrap' );
						desc = desc.find( '[data-tm-tooltip-html]' );
						if ( desc.length === 0 ) {
							desc = target.closest( '.tmcp-field-wrap' ).find( '.tc-inline-description' );
							if ( desc.length > 0 ) {
								descHTML = desc.html();
							}
						} else {
							descHTML = desc.attr( 'data-tm-tooltip-html' );
						}

						if ( tip === undefined ) {
							if ( is_swatch ) {
								tip = findlabelText.html();
							} else if ( is_swatch_desc && descHTML !== '' ) {
								tip = '<aside>' + descHTML + '</aside>';
							} else if ( is_swatch_lbl_desc && ( findlabelText.html() !== '' || descHTML !== '' ) ) {
								tip = '<aside>' + findlabelText.html() + '</aside><aside>' + descHTML + '</aside>';
							} else if ( is_swatch_img && get_img_src !== '' ) {
								tip = '<img src="' + get_img_src + '">';
							} else if ( is_swatch_img_lbl && ( findlabelText.html() !== '' || get_img_src !== '' ) ) {
								tip = '<img src="' + get_img_src + '"><aside>' + findlabelText.html() + '</aside>';
							} else if ( is_swatch_img_desc && ( get_img_src !== '' || descHTML !== '' ) ) {
								tip = '<img src="' + get_img_src + '"><aside>' + descHTML + '</aside>';
							} else if ( is_swatch_img_lbl_desc && ( findlabelText.html() !== '' || get_img_src !== '' || descHTML !== '' ) ) {
								tip = '<img src="' + get_img_src + '"><aside>' + findlabelText.html() + '</aside><aside>' + descHTML + '</aside>';
							}

							if ( tip !== undefined ) {
								target.data( 'tm-tip-html', tip );
								if ( is_hide_label ) {
									findlabel.find( '.tm-tooltip' ).remove();
									findlabelText.hide();
								}
							}
							if ( tip === undefined ) {
								// The following two methods are here for dynamic tooltip support
								if ( target.attr( 'data-tm-tooltip-html' ) ) {
									tip = target.attr( 'data-tm-tooltip-html' );
								} else {
									tip = target.attr( 'title' );
								}
							}
						} else {
							target.data( 'tm-tip-html', tip );
						}

						target.on( 'tc-tooltip-html-changed', function() {
							if ( target.attr( 'data-tm-tooltip-html' ) ) {
								target.show();
							} else {
								target.hide();
							}
						} );

						if ( target.is( 'img' ) ) {
							target.closest( 'label' ).on( 'mouseenter tmshowtooltip', that.show.bind( that, target ) );
						}
						target.on( 'mouseenter.tc tmshowtooltip.tc', that.show.bind( that, target ) );

						if ( settings.trigger ) {
							that.show( target );
						}
					}
				} );
			}
		}
	};

	$.fn.tcToolTip = function( option ) {
		var methodReturn;
		var targets = $( this );
		var data;
		var options;
		var ret;
		var hasAtLeastOneNonToolTip = targets
			.map( function() {
				return $( this ).data( 'tctooltip' ) || '';
			} )
			.get()
			.some( function( value ) {
				return value === '';
			} );

		if ( typeof option === 'object' ) {
			options = option;
		} else {
			options = {};
		}

		if ( hasAtLeastOneNonToolTip ) {
			data = new ToolTip( this, options );
			targets.data( 'tctooltip', data );
		}

		if ( typeof option === 'string' ) {
			data = targets.data( 'tctooltip' );
			methodReturn = data[ option ].apply( data, [] );
		}

		if ( methodReturn === undefined ) {
			ret = targets;
		} else {
			ret = methodReturn;
		}

		return ret;
	};

	$.fn.tcToolTip.defaults = {
		fadin: 'fadein',
		fadeout: 'fadeout',
		speed: 1500
	};

	$.fn.tcToolTip.instances = [];

	$.fn.tcToolTip.Constructor = ToolTip;

	$.tcToolTip = function( targets, options ) {
		var data = false;
		var hasAtLeastOneNonToolTip;

		targets = targets || $( '.tm-tooltip' );
		hasAtLeastOneNonToolTip = targets
			.map( function() {
				return $( this ).data( 'tctooltip' ) || '';
			} )
			.get()
			.some( function( value ) {
				return value === '';
			} );
		if ( hasAtLeastOneNonToolTip ) {
			data = new ToolTip( targets, options );
			targets.data( 'tctooltip', data );
		}

		return data;
	};
}( window, window.jQuery ) );
