<?php
/**
 * Underscore.js template
 *
 * @package fusion-builder
 * @since 2.0
 */

?>
<script type="text/template" id="fusion-builder-child-sortables">
	<h3>{{ fusionBuilderText.add_edit_items }}</h3>
	<p class="fusion-multi-child-desc">{{ fusionBuilderText.sortable_items_info }}</p>
	<a href="#" class="fusion-multi-child-button fusion-builder-add-multi-child"><span class="fusiona-plus"></span><span class="add-sortable-child-text"><?php esc_attr_e( 'Add', 'fusion-builder' ); ?> {{ fusionAllElements[fusionAllElements[attributes.element_type].element_child].name }}</span></a>

	<# if ( 'fusion_checklist' === attributes.element_type ) { #>
	<a href="#" class="fusion-multi-child-button fusion-builder-add-predefined-multi-child"><span class="fusiona-plus"></span><span class="add-sortable-child-text">{{ fusionBuilderText.bulk_add }}</span></a>
	<# } #>
	<# if ( 'fusion_gallery' === attributes.element_type ) { #>
	<a href="#" class="fusion-multi-child-button fusion-builder-add-multi-gallery-images"><span class="fusiona-plus"></span><span class="add-sortable-child-text">{{ fusionBuilderText.bulk_add }}</span></a>
	<# } #>
	<# if ( 'fusion_images' === attributes.element_type ) { #>
	<a href="#" class="fusion-multi-child-button fusion-builder-add-multi-gallery-images"><span class="fusiona-plus"></span><span class="add-sortable-child-text">{{ fusionBuilderText.bulk_add }}</span></a>
	<# } #>
	<ul class="fusion-builder-sortable-children">
		<#
			_.each( children.models, function( child ) { #>
			<#
				params = jQuery.extend( true, {}, child.attributes.params );
				elementName = "<?php esc_attr_e( 'Item', 'fusion-builder' ); ?>";
				if ( 'undefined' !== typeof params.title && params.title.length ) {
					elementName = params.title;
				} else if ( 'fusion_image_hotspot_point' === child.attributes.element_type && 'undefined' !== typeof params.icon && params.icon && ( 'undefined' === typeof params.title || '' === params.title ) ) {
					elementName = '<i class="awb-preview-icon ' + params.icon + '"></i>';
				} else if ( 'undefined' !== typeof params.title_front && params.title_front.length ) {
					elementName = params.title_front;
				} else if ( 'undefined' !== typeof params.name && params.name.length ) {
					elementName = params.name;
				} else if ( 'undefined' !== typeof params.image && params.image.length ) {
					elementName = params.image;
					$image      = params.image;

					// If contains backslash, retrieve only last part.
					if ( -1 !== elementName.indexOf( '/' ) && -1 === elementName.indexOf( '[' ) ) {
						elementName = elementName.split( '/' );
						elementName = elementName.slice( -1 )[0];
					}

					$extension = $image.substr( $image.lastIndexOf( '.' ) );
					if ( 0 === $extension.indexOf( '.' ) ) {
						$image = $image.replace( /-\d+x\d+\./, '.' );
						$image = $image.replace( $extension, '-66x66' + $extension );
					}

					elementName = '<img class="awb-preview-image" src="' + $image + '" /> ' + elementName;
				} else if ( 'image' == child.attributes.element_name && 'undefined' !== typeof params.element_content && params.element_content.length ) {
					elementName = params.element_content;

					// If contains backslash, retrieve only last part.
					if ( -1 !== elementName.indexOf( '/' ) && -1 === elementName.indexOf( '[' ) ) {
						elementName = elementName.split( '/' );
						elementName = elementName.slice( -1 )[0];
					}
				} else if ( 'undefined' !== typeof params.video && params.video.length ) {
					elementName = params.video;
				} else if ( 'undefined' !== typeof params.element_content && params.element_content.length ) {
					elementName = params.element_content;
				}

				// Remove HTML tags but keep quotation marks etc.
				if ( -1 === elementName.indexOf( 'awb-preview-icon' ) && -1 === elementName.indexOf( 'awb-preview-image' )) {
					elementName = elementName.replace( /(<([^>]+)>)/ig, '' );
				}

				let isDynamic = '';
				if ( child.attributes.params.dynamic_parent ) {
					isDynamic = 'fusion-dynamic-child';
					elementName = '<?php esc_html_e( 'Item', 'fusion-builder' ); ?>'
				}
			#>
			<li data-cid="{{child.attributes.cid}}" class="fusion-builder-data-cid {{isDynamic}}">
				<span class="multi-element-child-name">{{{ ( ( elementName ) ? elementName : fusionAllElements[child.attributes.element_type].name ) }}}</span>
				<div class="fusion-builder-controls">
					<a href="#" class="fusion-builder-multi-setting-options" title="{{ fusionBuilderText.edit_item }}"><span class="fusiona-pen"></span></a>
					<a href="#" class="fusion-builder-multi-setting-clone" title="{{ fusionBuilderText.clone_item }}"><span class="fusiona-file-add"></span></a>
					<a href="#" class="fusion-builder-multi-setting-remove" title="{{ fusionBuilderText.delete_item }}"><span class="fusiona-trash-o"></span></a>
				</div>
			</li>
		<# }); #>
	</ul>
</script>
