<?php
/**
 * An underscore.js template.
 *
 * @package fusion-builder
 */

?>
<script type="text/template" id="fusion-builder-block-module-settings-template">
	<div class="fusion-builder-modal-top-container">
		<# elementData = fusionAllElements[atts.element_type]; #>
		<# if ( 'undefined' !== typeof elementData ) { #>
				<h2>
					{{ elementData.name }}
					<# if ( 'undefined' !== typeof elementData.has_responsive ) { #>
						<ul class="fusion-viewport-indicator">
							<li class="fusion-viewport-text">
								<?php esc_html_e( 'responsive', 'fusion-builder' ); ?>
							</li>
							<li data-viewport="fusion-small">
								<a  href="JavaScript:void(0);">
									<i class="fusiona-mobile"></i>
								</a>
							</li>
							<li data-viewport="fusion-medium">
								<a href="JavaScript:void(0);">
									<i class="fusiona-tablet"></i>
								</a>
							</li>
							<li data-viewport="fusion-large" class="active">
								<a href="JavaScript:void(0);">
									<i class="fusiona-desktop"></i>
								</a>
							</li>
						</ul>
					<# }; #>
				</h2>
		<# }; #>
		<div class="fusion-builder-modal-close fusiona-plus2"></div>
		<#  group_options = {};
			var	menuLabel = '',
				generalGroupTag  = '<?php esc_attr_e( 'General', 'fusion-builder' ); ?>'.toLowerCase().replace(/ /g, '-');
			
			group_options[ generalGroupTag ] = {};
		#>

		<!-- Move options to groups -->
		<# _.each( fusionAllElements[atts.element_type].params, function( param ) {
			if ( 'undefined' !== typeof param.group ) {
				var group_tag = param.group.toLowerCase().replace(/ /g, '-');
				if ( 'undefined' == typeof group_options[ group_tag ] ) {
					group_options[ group_tag ] = {};
				}
				if ( 'undefined' !== typeof param.subgroup ) {
					if ( 'undefined' == typeof group_options[ group_tag ][param.subgroup.name]['subgroups'] ) {
						group_options[ group_tag ][param.subgroup.name]['subgroups'] = {};
					}
					if ( 'undefined' == typeof group_options[ group_tag ][param.subgroup.name]['subgroups'][param.subgroup.tab] ) {
						group_options[ group_tag ][param.subgroup.name]['subgroups'][param.subgroup.tab] = {};
					}
					group_options[ group_tag ][param.subgroup.name]['subgroups'][param.subgroup.tab][ param.param_name ] = param;
				} else {
					group_options[ group_tag ][ param.param_name ] = param;
				}
			} else {
				group_options[ generalGroupTag ][ param.param_name ] = param;
			}

		} ); #>

		<!-- If there is more than one group found show tabs -->
		<# if ( Object.keys( group_options ).length > 1 ) { #>
			<ul class="fusion-tabs-menu">
				<# 
					_.each( group_options, function( options, group) {
						if ( 'children' !== group ) {
				#>
							<# menuLabel = group.replace(/-/g, ' '); #>
							<li class=""><a href="#{{ group }}">{{ menuLabel }}</a></li>
				<# 
						}
					});
				#>
			</ul>
		<# }; #>
	</div>

	<div class="fusion-builder-modal-bottom-container">
		<a href="#" class="fusion-builder-modal-save">
			<span>
				<# if ( true === FusionPageBuilderApp.shortcodeGenerator && true !== FusionPageBuilderApp.shortcodeGeneratorMultiElementChild ) { #>
					{{ fusionBuilderText.insert }}
				<# } else { #>
					{{ fusionBuilderText.save }}
				<# } #>
			</span>
		</a>

		<a href="#" class="fusion-builder-modal-close">
			<span>
				{{ fusionBuilderText.cancel }}
			</span>
		</a>
	</div>

	<# if ( 'undefined' !== typeof atts.multi && 'multi_element_parent' === atts.multi ) {
		advanced_module_class = ' fusion-builder-main-settings-advanced';
	} else {
		advanced_module_class = '';
	} #>

	<div class="fusion-builder-main-settings fusion-large fusion-builder-main-settings-full <# if ( Object.keys(group_options).length > 1 ) { #>has-group-options<# } #>{{ advanced_module_class }}">
		<# if ( 'undefined' !== typeof elementData ) { #>
			<# if ( _.isObject ( elementData.params ) ) { #>

				<!-- If there is more than one group found show tabs -->
				<# if ( Object.keys(group_options).length > 1 ) { #>

					<!-- Show group options -->
					<div class="fusion-tabs">
						<# _.each( group_options, function( options, group) { 
								if ( 'children' !== group ) {
							#>
							<div id="{{ group }}" class="fusion-tab-content">
								<?php fusion_element_options_loop( 'options' ); ?>
							</div>
						<# 
							}
							} ); #>
					</div>

				<# } else { #>

					<?php fusion_element_options_loop( 'fusionAllElements[atts.element_type].params' ); ?>

				<# }; #>

			<# }; #>

		<# } else { #>

			{{ atts.element_type }} - Undefined Module

		<# }; #>

		<!-- Show create new subelement button -->
		<# if ( elementData.multi !== 'undefined' && elementData.multi == 'multi_element_parent' ) {  #>

			<# element_child = elementData.element_child #>
			<#
				let dynamicClass = '';
				if ( atts.params.dynamic_params ) {
					let dynamicData = FusionPageBuilderApp.base64Decode( atts.params.dynamic_params );
						dynamicData = _.unescape( dynamicData );
						dynamicData = JSON.parse( dynamicData );

					if ( dynamicData.parent_dynamic_content ) {
						dynamicClass = 'has-dynamic-data';

						if ( 'undefined' !== dynamicData.parent_dynamic_content.data && 'filebird_folder_parent' === dynamicData.parent_dynamic_content.data ) {
							dynamicClass += ' has-dynamic-data-no-children';
						}
					}
				}
			#>

			<div class="fusion-builder-option-advanced-module-settings {{ dynamicClass }}" data-element_type="{{ element_child }}">
				<div class="fusion-builder-option-advanced-module-settings-content">

					<# if ( Object.keys( group_options ).length > 1 ) { #>
							<# 
								_.each( group_options, function( options, group) {
									if ( 'children' === group ) {
							#>
								<?php fusion_element_options_loop( 'options', 'fusion-dynamic-parent-option' ); ?>
							<# 
									}
								});
							#>
					<# }; #>

					<#
					addEditItems      = 'undefined' !== typeof elementData.add_edit_items ? elementData.add_edit_items : fusionBuilderText.add_edit_items;
					sortableItemsInfo = 'undefined' !== typeof elementData.sortable_items_info ? elementData.sortable_items_info : fusionBuilderText.sortable_items_info;
					#>
					<h3 class="fusion-multi-child-title">{{ addEditItems }}</h3>
					<p class="fusion-multi-child-desc">{{ sortableItemsInfo }}</p>

					<ul class="fusion-builder-sortable-options"></ul>
					<a href="#" class="fusion-multi-child-button fusion-builder-add-multi-child"><span class="fusiona-plus"></span><span class="add-sortable-child-text">{{ fusionAllElements[element_child].name }}</span></a>
					<# if ( 'fusion_checklist' === elementData.shortcode ) { #>
					<a href="#" class="fusion-multi-child-button fusion-builder-add-predefined-multi-child"><span class="fusiona-plus"></span><span class="add-sortable-child-text">{{ fusionBuilderText.bulk_add }}</span></a>
					<# } #>
					<# if ( 'fusion_gallery' === elementData.shortcode ) { #>
					<a href="#" class="fusion-multi-child-button fusion-builder-add-multi-gallery-images"><span class="fusiona-plus"></span><span class="add-sortable-child-text">{{ fusionBuilderText.bulk_add }}</span></a>
					<# } #>
					<# if ( 'fusion_images' === elementData.shortcode ) { #>
					<a href="#" class="fusion-multi-child-button fusion-builder-add-multi-gallery-images"><span class="fusiona-plus"></span><span class="add-sortable-child-text">{{ fusionBuilderText.bulk_add }}</span></a>
					<# } #>
				</div>
			</div>

		<# }; #>
	</div>
</script>
