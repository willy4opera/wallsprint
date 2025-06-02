<?php
/**
 * Underscore.js template
 *
 * @package fusion-builder
 * @since 3.3
 */

?>
<script type="text/html" id="tmpl-fusion_post_cards-shortcode">
		<div {{{ _.fusionGetAttributes( attr ) }}}>
		<# if ( 'no' !== values.filters && ( 'grid' === values.layout || 'masonry' === values.layout ) && 'posts' === values.source ) { #>
			{{{filters}}}
		<# } #>
		<#
		// If Query Data is set, use it and continue. If not, echo HTML.
		if ( 'undefined' !== typeof query_data && 'undefined' !== typeof query_data.loop_product && query_data.loop_product ) {
		#>
			<# if ( _.contains( [ 'slider', 'carousel' , 'coverflow' ], values.layout ) ) { #>
				<div {{{ _.fusionGetAttributes( productsAttrs ) }}}>
					{{{ productsLoop }}}
				</div>

				<# if ( _.contains( [ 'dots', 'arrows_dots'], values.show_nav ) ) { #>
					<div class="swiper-pagination"></div>
				<# } #>

				<# if ( _.contains( [ 'yes', 'arrows_dots' ], values.show_nav ) && 'no' === values.mask_edges ) { #>
					<div class="awb-swiper-button awb-swiper-button-prev"><i {{{ _.fusionGetAttributes( prevAttr ) }}}></i></div>
					<div class="awb-swiper-button awb-swiper-button-next"><i {{{ _.fusionGetAttributes( nextAttr ) }}}></i></div>
				<# } #>

			<# } else { #>
				<ul {{{ _.fusionGetAttributes( productsAttrs ) }}}>
					{{{ productsLoop }}}
				</ul>
			<# } #>

			<# if ( 'no' !== values.scrolling && 'terms' !== values.source && ( 'grid' === values.layout || 'masonry' === values.layout || 'stacking' === values.layout  ) ) { #>
				{{{ pagination }}}
			<# } #>

			<# if ( 'load_more_button' === values.scrolling && -1 !== values.number_posts && 'terms' !== values.source && ( 'grid' === values.layout || 'stacking' === values.layout ) ) { #>
				<button class="fusion-load-more-button fusion-product-button fusion-clearfix">{{{ loadMoreText }}}</button>
			<# } #>

		<#
		} else if ( 'undefined' !== typeof query_data && 'undefined' !== typeof query_data.placeholder ) {
		#>
			{{{ query_data.placeholder }}}
		<# } #>
		</div>
</script>
