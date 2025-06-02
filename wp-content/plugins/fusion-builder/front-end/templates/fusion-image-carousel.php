<?php
/**
 * Underscore.js template
 *
 * @package fusion-builder
 * @since 2.0
 */

?>
<script type="text/html" id="tmpl-fusion_images-shortcode">
<# if ( 'undefined' !== typeof usingDynamic && usingDynamic ) { #>
	<div class="fusion-builder-placeholder"><?php esc_html_e( 'This image carousel element is set to use dynamic data.  For a preview please check the front-end.', 'fusion-builder' ); ?></div>
<# } else { #>
	<div {{{ _.fusionGetAttributes( attr ) }}}>
		<div  {{{ _.fusionGetAttributes( attrCarousel ) }}}>
			<div {{{ _.fusionGetAttributes( attrCarouselWrapper ) }}}></div>

			<# if ( _.contains( [ 'dots', 'arrows_dots'], values.show_nav ) && 'marquee' !== values.layout ) { #>
					<div class="swiper-pagination"></div>
				<# } #>

			<# if ( _.contains( [ 'yes', 'arrows_dots' ], values.show_nav ) && ( -1 === jQuery.inArray( values.layout, [ 'carousel', 'coverflow', 'marquee' ] ) || 'no' === values.mask_edges ) ) { #>
				<div class="awb-swiper-button awb-swiper-button-prev"><i {{{ _.fusionGetAttributes( prevAttr ) }}}></i></div>
				<div class="awb-swiper-button awb-swiper-button-next"><i {{{ _.fusionGetAttributes( nextAttr ) }}}></i></div>
				<# } #>
			</div>

			<# if ( !usingDynamicParent ) { #>
				<div class="fusion-element-placeholder">
					<div class="fusion-carousel-item-wrapper" style="visibility: inherit">
						<div class="fusion-image-wrapper hover-type-none" style="width:100%">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 560"><path fill="#EAECEF" d="M0 0h1024v560H0z"/><g fill-rule="evenodd" clip-rule="evenodd"><path fill="#BBC0C4" d="M378.9 432L630.2 97.4c9.4-12.5 28.3-12.6 37.7 0l221.8 294.2c12.5 16.6.7 40.4-20.1 40.4H378.9z"/><path fill="#CED3D6" d="M135 430.8l153.7-185.9c10-12.1 28.6-12.1 38.7 0L515.8 472H154.3c-21.2 0-32.9-24.8-19.3-41.2z"/><circle fill="#FFF" cx="429" cy="165.4" r="55.5"/></g></svg>
						</div>
					</div>
				</div>
			<# } #>
		</div>
		{{{captionStyles}}}
<# } #>
</script>
<script type="text/html" id="tmpl-fusion_image-shortcode">
<#
var image_html = '';

image_html += '<div ' + _.fusionGetAttributes( attrItemWrapper ) + '>';

if ( 'above' === parentValues.caption_style && 'undefined' !== typeof captionHtml ) {
	image_html += captionHtml;
}

image_html += '<div ' + _.fusionGetAttributes( attrImageWrapper ) + '>';

if ( 'no' === mouseScroll && ( ( null !== link && '' !== link ) || 'yes' === lightbox ) ) {
	image_html += '<a ' + _.fusionGetAttributes( attrCarouselLink ) + '>' + imageElement + '</a>';
} else {
	image_html += imageElement;
}

image_html += '</div>';

if ( 'below' === parentValues.caption_style && 'undefined' !== typeof captionHtml ) {
	image_html += captionHtml;
}

image_html += '</div>';
#>
	<# if ( 'undefined' !== typeof usingDynamicParent && usingDynamicParent ) {	#>
		<div class="fusion-builder-placeholder">
			<?php esc_html_e( 'This image carousel element is set to use dynamic data.  For a preview please check the front-end.', 'fusion-builder' ); ?>
		</div>
	<# } else { #>
		{{{ image_html }}}
	<# } #>
</script>
