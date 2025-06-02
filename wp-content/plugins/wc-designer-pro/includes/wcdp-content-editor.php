<?php

// Content editor skin
function wcdp_designer_pro_content_store($wcdp_parameters){

	wcdp_register_java_files_for_editor();
	$wcdp_general = get_option('wcdp-settings-general');
	$wcdp_style   = get_option('wcdp-settings-style');
	$storageFonts = wcdp_get_storage_fonts();
	
	$wcdp_settings = array(
	    'general'    => $wcdp_general,
		'style'      => $wcdp_style,
		'parameters' => $wcdp_parameters,
		'fonts'      => $storageFonts
	);
	
	wcdp_add_skin_custom_style($wcdp_settings);
    wcdp_add_global_variables_javascript_settings($wcdp_settings);

    $btnToolbar = array(
	    'info'          => __( 'Keyboard shortcuts', 'wcdp' ),
		'select-all'    => __( 'Select all', 'wcdp' ),
        'clear'         => __( 'Erase all', 'wcdp' ),
	    'grid'          => __( 'Grid', 'wcdp' ),
        'center-h'      => __( 'Center horizontally', 'wcdp' ),	 
	    'center-v'      => __( 'Center vertically', 'wcdp' ),
        'flipX'         => __( 'Flip horizontally', 'wcdp' ),	 
	    'flipY'         => __( 'Flip vertically', 'wcdp' ),
		'rotate'        => __( 'Rotate', 'wcdp' ),
        'bringForward'  => __( 'Bring to front', 'wcdp' ),	 
        'sendBackwards' => __( 'Send to back', 'wcdp' ),
	    'lock'          => __( 'Lock', 'wcdp' ),
        'group'         => __( 'Group', 'wcdp' ),	 
	    'duplicate'     => __( 'Duplicate', 'wcdp' ),	 
	    'delete'        => __( 'Delete', 'wcdp' ),		
        'undo'          => __( 'Undo', 'wcdp' ),	 
        'redo'          => __( 'Redo', 'wcdp' ),
        'preview'       => __( 'Preview', 'wcdp' )
    );
	$rtl_mode = isset($wcdp_general['rtl_mode']) && $wcdp_general['rtl_mode'] === 'on' ? true : false;
	if($rtl_mode){
        $redo = $btnToolbar['redo'];
		unset($btnToolbar['redo']);
		$arrTb = array_splice($btnToolbar, 0, 15); 
		$arrTb['redo'] = $redo;
        $btnToolbar = array_merge($arrTb, $btnToolbar);
	}
	
	$btnTabs = array(
	    'text'      => __( 'Text', 'wcdp' ),
		'images'    => __( 'Images', 'wcdp' ),
	    'shapes'    => __( 'Shapes', 'wcdp' ),
		'cliparts'  => __( 'Cliparts', 'wcdp' ),
	    'qr'        => __( 'QR Code', 'wcdp' ),
		'maps'      => __( 'Map', 'wcdp' ),
		'calendars' => __( 'Calendars', 'wcdp' ),
		'bgcolors'  => __( 'Background colors', 'wcdp' ),
        'layers'    => __( 'Manage layers', 'wcdp' )
	);
	
	$btnBoxOptions = array(
	    'save' => __( 'SAVE', 'wcdp' )
	);
	
	$frontend = $wcdp_parameters['editor'] === 'frontend';
	$viewFinder = isset($wcdp_general['view_finder']) && $wcdp_general['view_finder'] === 'on' ? true : false;
	if($frontend){
		if(isset($wcdp_parameters['multipleDesigns']) || isset($wcdp_parameters['multipleDesignsCat'])){
			$wcdp_parameters['templates_e'] = 'on';
		    $btnTabs = array_merge(array('templates' => __( 'Templates', 'wcdp' )), $btnTabs);
		}
		if(!$viewFinder){
			$btnBoxOptions['addtocart'] = __( 'ADD TO CART', 'wcdp' );
		    $btnTabs = array_merge(array('settings' => __( 'Product', 'wcdp' )), $btnTabs);
		}
		$btnTabs['my-designs'] = __( 'My designs', 'wcdp' );
	}
	else{
		$btnTabs['code'] = __( 'Execute code', 'wcdp' );
	}
	
    $btnToolbarText = array(
	    'bold'         => __( 'Bold', 'wcdp' ),
	    'italic'       => __( 'Italic', 'wcdp' ),
	    'underline'    => __( 'Underline', 'wcdp' ),
	    'line-through' => __( 'Line through', 'wcdp' ),
	    'overline'     => __( 'Overline', 'wcdp' ),
	    'left'         => __( 'Align left', 'wcdp' ),
	    'center'       => __( 'Align center', 'wcdp' ),
	    'right'        => __( 'Align right', 'wcdp' )		
    );
	if($rtl_mode){
		$arrTbt = array_splice($btnToolbarText, 0, 5);
		$arrTbt['right'] = $btnToolbarText['right'];
		$arrTbt['center'] = $btnToolbarText['center'];
		$arrTbt['left'] = $btnToolbarText['left'];
        $btnToolbarText = $arrTbt;
	}
	
	$tabsContentRowsText = array(						
		array('type' => 'box-content-tools', 'box' => 'open'),
	    array('type' => 'color', 'btn' => 'text-color', 'label' => __( 'Text color', 'wcdp' ),		   
		      'data' => array('text-color-font', 'change_text_color', $wcdp_style['text_color_editor']['CMYK'], $wcdp_style['text_color_editor']['RGB'])),
	    array('type' => 'select', 'btn' => 'text-outline', 'label' => __( 'Outline', 'wcdp' ),		   
		      'data' => array('text-stroke', '0', __( 'None', 'wcdp' ), range(0.1, 10, 0.1), ' px') ),
	    array('type' => 'color', 'btn' => 'text-outline-color', 'label' => __( 'Outline color', 'wcdp' ),		   
		      'data' => array('text-outline-color', 'change_text_color_outline',  $wcdp_style['text_color_editor_outline']['CMYK'], $wcdp_style['text_color_editor_outline']['RGB'])),
	    array('type' => 'select', 'btn' => 'text-curved', 'label' => __( 'Text effects', 'wcdp' ),		   
		      'data' => array('text-curved', 'none', __( 'None', 'wcdp' ), array( 
		       array('curved' => __( 'Curved', 'wcdp' ),
				     'reverse' => __( 'Reverse', 'wcdp' ),
					 'arc' => __( 'Arc', 'wcdp' ),
					 'smallToLarge' => __( 'Small to large', 'wcdp' ),
					 'largeToSmallBottom' => __( 'Large to small', 'wcdp' ),
					 'bulge' => __( 'Bulge', 'wcdp' ))
			    ))),
		array('type' => 'box-effects', 'box' => 'open'),
	    array('type' => 'range', 'label' => __( 'Radius', 'wcdp' ),		   
		      'data' => array('text-radius', '-300', '300', '300', '1')),
	    array('type' => 'range', 'label' => __( 'Spacing', 'wcdp' ),		   
		      'data' => array('text-spacing', '0', '40', '0', '1')),
		array('type' => 'box-effects', 'box' => 'close'),
	    array('type' => 'range', 'btn' => 'text-opacity', 'label' => __( 'Opacity', 'wcdp' ),		   
		      'data' => array('text-opacity', '0', '1', '1', '0.01')),
		array('type' => 'box-content-tools', 'box' => 'close')
	);
	
	$btnToolbarImg = array(
	    'fill'    => __( "Fill options", "wcdp" ),
	    'crop'    => __( "Crop", "wcdp" )
	);
	
	$setMask = !$frontend || isset($wcdp_parameters['user_mask']) && $wcdp_parameters['user_mask'] === 'on' ? true : false;
	if($setMask)
		$btnToolbarImg['mask'] = __( "Mask", "wcdp" );
	
	$optionFilter = get_option('wcdp-settings-filters');
    
	if(!empty($optionFilter) && in_array('on', $optionFilter))
		$btnToolbarImg['filters'] = __( "Filters", "wcdp" );

	if(!$frontend)
	    $btnToolbarImg['ov'] = __( "Set overlay", "wcdp" );
	
    $setBackground = !$frontend || isset($wcdp_parameters['user_bg']) && $wcdp_parameters['user_bg'] === 'on' ? true : false;
	if($setBackground){
		$btnToolbarImg['bg'] = __( "Set background", "wcdp" );
		$btnToolbarImg['angle'] = __( "Background angle", "wcdp" );
	}
	
	$tabsContentRowsImage = array(
		array('type' => 'box-content-tools', 'box' => 'open', 'ident' => 'box__tool', 'data_id' => 'fill', 'title' => __( "Fill options", "wcdp" )),
	    array('type' => 'color', 'btn' => 'svg-color', 'label' => __( 'Fill color SVG', 'wcdp' ),
		      'data' => array('svg-color', 'change_svg_fill_color', '0,0,0,100', '#000000')),
		array('type' => 'div', 'class' => 'box-svg-multicolor mc__sb'),
	    array('type' => 'range', 'btn' => 'svg-outline', 'label' => __( 'Outline SVG', 'wcdp' ),
		      'data' => array('svg-stroke', '0', '50', '0', '1') ),
	    array('type' => 'color', 'btn' => 'svg-outline-color', 'label' => __( 'Outline color SVG', 'wcdp' ),		   
		      'data' => array('svg-outline-color', 'change_svg_fill_color_outline', '0,0,0,100', '#000000')),
        array('type' => 'div', 'class' => 'box-svg-stroke-multicolor mc__sb'),
	    array('type' => 'range', 'btn' => 'image-opacity', 'label' => __( 'Opacity', 'wcdp' ),   
		      'data' => array('image-opacity', '0', '1', '1', '0.01') ),
		array('type' => 'box-content-tools', 'box' => 'close')
	);
	
	$tabsContentRowsShapes = array(						
		array('type' => 'box-content-tools', 'box' => 'open'),
	    array('type' => 'color', 'btn' => 'shap-color', 'label' => __( 'Fill color', 'wcdp' ),		   
		      'data' => array('shap-color', 'change_shap_fill_color', $wcdp_style['color_shapes_editor']['CMYK'], $wcdp_style['color_shapes_editor']['RGB'])),
	    array('type' => 'range', 'btn' => 'shap-outline', 'label' => __( 'Outline', 'wcdp' ),		   
		      'data' => array('shap-stroke', '0', '50', '0', '1') ),
	    array('type' => 'color', 'btn' => 'shap-outline-color', 'label' => __( 'Outline color', 'wcdp' ),		   
		      'data' => array('shap-outline-color', 'change_shap_fill_color_outline',  $wcdp_style['color_shapes_editor_outline']['CMYK'], $wcdp_style['color_shapes_editor_outline']['RGB'])),
	    array('type' => 'range', 'btn' => 'shap-opacity', 'label' => __( 'Opacity', 'wcdp' ),   
		      'data' => array('shap-opacity', '0', '1', '1', '0.01') ),
		array('type' => 'box-content-tools', 'box' => 'close')
	);
	
	$tabsContentRowsCliparts = array(						
		array('type' => 'box-content-tools', 'box' => 'open', 'ident' => 'box__tool', 'data_id' => 'fill', 'title' => __( "Fill options", "wcdp" )),
	    array('type' => 'color', 'btn' => 'clip-svg-color', 'label' => __( 'Fill color SVG', 'wcdp' ),		   
		      'data' => array('clip-svg-color', 'change_clip_fill_color', '0,0,0,100', '#000000')),
		array('type' => 'div', 'class' => 'box-svg-multicolor mc__sb'),		
	    array('type' => 'range', 'btn' => 'clip-svg-outline', 'label' => __( 'Outline SVG', 'wcdp' ),		   
		      'data' => array('clip-svg-stroke', '0', '50', '0', '1') ),		
	    array('type' => 'color', 'btn' => 'clip-svg-outline-color', 'label' => __( 'Outline color SVG', 'wcdp' ),		   
		      'data' => array('clip-svg-outline-color', 'change_clip_fill_color_outline', '0,0,0,100', '#000000')),		
		array('type' => 'div', 'class' => 'box-svg-stroke-multicolor mc__sb'),		
	    array('type' => 'range', 'btn' => 'clipart-opacity', 'label' => __( 'Opacity', 'wcdp' ),   
		      'data' => array('clipart-opacity', '0', '1', '1', '0.01') ),
		array('type' => 'box-content-tools', 'box' => 'close')
	);
	
	$tabsContentRowsQRcode = array(						
		array('type' => 'box-content-tools', 'box' => 'open'),
	    array('type' => 'color', 'btn' => 'fg-qr-color', 'label' => __( 'Foreground color', 'wcdp' ),		   
		      'data' => array('fg-qr-color', 'change_qrcode_fill_color', $wcdp_style['color_qr_editor']['CMYK'], $wcdp_style['color_qr_editor']['RGB'])),
	    array('type' => 'color', 'btn' => 'bg-qr-color', 'label' => __( 'Background color', 'wcdp' ),		   
		      'data' => array('bg-qr-color', 'change_qrcode_fill_color', $wcdp_style['bg_color_qr_editor']['CMYK'], $wcdp_style['bg_color_qr_editor']['RGB'])),
	    array('type' => 'select', 'btn' => 'qr-level', 'label' => __( 'Correction level', 'wcdp' ),
              'data' => array('qr-level', 'LOW', __( 'Low', 'wcdp' ), array( 
		       array('MEDIUM' => __( 'Medium', 'wcdp' ),
				     'QUARTILE' => __( 'Quartile', 'wcdp' ),
					 'HIGH' => __( 'High', 'wcdp' ))
			   ))),
	    array('type' => 'select', 'btn' => 'qr-border', 'label' => __( 'Border', 'wcdp' ),		   
		      'data' => array('qr-border', '0', __( 'None', 'wcdp' ), range(1, 10), ' px') ),
	    array('type' => 'range', 'btn' => 'qr-range', 'label' => __( 'Version range', 'wcdp' ),   
		      'data' => array('qr-range', '1', '10', '1', '1') ),		  
		array('type' => 'box-content-tools', 'box' => 'close')
	);
	
	$tabsContentRowsMaps = array(						
		array('type' => 'box-content-tools', 'box' => 'open'),
	    array('type' => 'select', 'btn' => 'map-type', 'label' => __( 'Map type', 'wcdp' ),
              'data' => array('map-type', 'roadmap', __( 'Roadmap', 'wcdp' ), array( 
		       array('satellite' => __( 'Satellite', 'wcdp' ),
				     'terrain' => __( 'Terrain', 'wcdp' ),
					 'hybrid' => __( 'Hybrid', 'wcdp' ))
			    ))),
	    array('type' => 'select', 'btn' => 'map-zoom', 'label' => __( 'Map zoom', 'wcdp' ),		   
		      'data' => array('map-zoom', '1', 1, range(2, 20), '') ),
	    array('type' => 'select', 'btn' => 'map-icon-label', 'label' => __( 'Icon Label', 'wcdp' ),		   
		      'data' => array('map-icon-label', '', __( 'None', 'wcdp' ), array_merge(range(0, 9), range('A', 'Z')), '') ),
	    array('type' => 'select', 'btn' => 'map-icon-size', 'label' => __( 'Icon Size', 'wcdp' ),
              'data' => array('map-icon-size', 'normal', __( 'Normal', 'wcdp' ), array( 
		       array('mid' => __( 'Mid', 'wcdp' ),
				     'small' => __( 'Small', 'wcdp' ))
			    ))),
	    array('type' => 'color', 'btn' => 'map-icon-color', 'label' => __( 'Icon color', 'wcdp' ),		   
		      'data' => array('map-icon-color', 'change_map_icon_color', $wcdp_style['map_color_ico']['CMYK'], $wcdp_style['map_color_ico']['RGB'])),
		array('type' => 'box-content-tools', 'box' => 'close')
	);
	
	$tabsContentRowsBgColors = array(
	    array('type' => 'box-content-tools', 'box' => 'open'),
	    array('type' => 'color', 'btn' => 'bg-color', 'label' => __( 'Background color', 'wcdp' ),		   
		      'data' => array('bg-color', 'change_bg_color', '0,0,0,100', '#000000')),
		array('type' => 'box-content-tools', 'box' => 'close')
	);
	$hideNotes = isset($wcdp_style['hide_notes']) && $wcdp_style['hide_notes'] === 'on' ? true : false;
    $lazyLoader = WCDP_URL .'/assets/images/ajax-loader.gif';
    ?>
	
    <!-- Drawing Editor Content -->
    <div id="wcdp-container" <?php echo $rtl_mode ? ' class="md__rtl"':''; ?>>
        <div id="wcdp-custom-tool-panel">
            <div id="wcdp-vertical-tab">
                <div id="wcdp-tabs-icons">   
	                <?php $calHeight = 1;
				    foreach($btnTabs as $tab => $tabVal){
				        if(isset($wcdp_parameters[$tab.'_e']) && $wcdp_parameters[$tab.'_e'] === 'on'){
	                        echo '<span class="dp-tooltip" id="wcdp-btn-'.$tab.'" title="'.$tabVal.'"></span>';
						    $calHeight +=45;
				        }
		            } ?>
          	    </div>
			    <div id="wcdp-tabs-content" style="min-height: <?php echo $calHeight; ?>px">
				    <?php if(!$viewFinder && $frontend){
						$product = wc_get_product($wcdp_parameters['productID']); ?>
                        <div class="wcdp-tab-section" id="wcdp-settings-panel">
                            <div class="dp-title-label"><?php echo $wcdp_parameters['productName']; ?></div>
					        <div class="dp-row">
					            <div class="dp-contain-product mc__sb dp-content-style dp-border-style">
					            <?php 
						        $product_available = 0;
							    $stockStatus = method_exists($product, 'get_stock_status') ? $product->get_stock_status() : $product->stock_status;
							    if($stockStatus == 'instock'){
                                    if($product->is_type('variable') && $product->get_available_variations()){
		                                wp_enqueue_script('wc-add-to-cart-variation'); ?>
	                                    <form class="variations_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint($product->get_id()); ?>" data-product_variations="<?php echo htmlspecialchars(json_encode($product->get_available_variations())) ?>">
		                                <?php
			                        	    $product_available = true;
										    $product_type = 'variable';
											$attr_data = $wcdp_parameters['attr_data']; ?>
			                                <div class="variations">
											    <?php
												foreach($product->get_variation_attributes() as $attribute_name => $options){
												    $layout = 'drop_down';
													$attr_slug = sanitize_title($attribute_name);
													if(isset($wcdp_parameters['attr_actions']) && isset($wcdp_parameters['attr_actions'][$attr_slug])){
														$attr_value = $wcdp_parameters['attr_actions'][$attr_slug];
														if(isset($attr_value['layout'])){
															$layout = $attr_value['layout'];
														}
														if(isset($attr_value['actions'])){
															$actions = $attr_value['actions'];
														}
													} ?>
													<div class="dp-row" data-layout="<?php echo $layout; ?>">
					                                    <label for="<?php echo $attr_slug; ?>"><?php echo wc_attribute_label($attribute_name); ?>:</label> 
														<?php
														if($attr_data && isset($attr_data[$attr_slug])){
															$selected = $attr_data[$attr_slug];
														} else{
														    $selected = isset($_REQUEST['attribute_'. $attr_slug]) ? wc_clean(urldecode($_REQUEST['attribute_'. $attr_slug])) : $product->get_variation_default_attribute($attribute_name);
									                    }
														wc_dropdown_variation_attribute_options(array('options' => $options, 'attribute' => $attribute_name, 'product' => $product, 'selected' => $selected));	
														if(!empty($options)){
															$hmtl_colors = '';
															$hmtl_radio  = '';
															$html_values = '';
			                                                $tax_exists = false;											
			                                                if(taxonomy_exists($attribute_name)){
				                                                $terms = wc_get_product_terms($product->get_id(), $attribute_name, array('fields' => 'all'));
				                                                $options = array();
				                                                foreach($terms as $term){
					                                                $options[] = array(
					                                                    'name' => $term->name,
						                                                'slug' => $term->slug
					                                                );
				                                                }
				                                                $tax_exists = true;
                                                            }
				                                            foreach($options as $option){
																$value_name = $tax_exists ? esc_attr($option['name']) : esc_attr($option);
																$value_slug = $tax_exists ? esc_attr($option['slug']) : sanitize_title($option);
																$select_opt = sanitize_title($selected);
					                                            if(isset($actions) && $layout == 'product_colors'){
						                                            if(isset($actions[$value_slug]) && isset($actions[$value_slug]['bg_color']) && isset($actions[$value_slug]['bg_color']['active']) && $actions[$value_slug]['bg_color']['active'] == 'on'){
					                                                    $colorHex = $actions[$value_slug]['bg_color']['value'];
							                                            if(empty($colorHex) || preg_match('/^#[a-f0-9]{6}$/i', $colorHex)){
								                                            $colorHex = $colorHex ? $colorHex : 'transparent';
								                                            $close = $colorHex == 'transparent' ? 'sp-thumb-close' : '';
																			$lightness = '';
																			if($colorHex != 'transparent'){
																				$tiny = wcdp_hex_to_hsl($colorHex);
																				$lightness = $tiny['l'] < 0.5 ? ' sp-thumb-dark' : ' sp-thumb-light';
																			}
								                                            $active = $select_opt === $value_slug ? ' sp-thumb-active' . $lightness : '';
						                                                    $hmtl_colors .= '<span class="'. $close . $active .'" name="'. ($tax_exists ? $value_slug : $value_name) .'" title="'. $value_name .'" style="background-color: '. $colorHex .'"></span>';
							                                            }
						                                            }
					                                            }
                                                                else if($layout == 'radio_checkbox'){
																    $hmtl_radio .= '<div class="dp-radio-item">';
                                                                    $hmtl_radio .= '<input type="radio" id="_pr_attr_value-'. $value_slug .'"'. ($select_opt === $value_slug ? ' checked="checked"': '') .' name="dp_radio_'. $attr_slug .'" value="'. ($tax_exists ? $value_slug : $value_name) .'">';
																	$hmtl_radio .= '<label for="_pr_attr_value-'. $value_slug .'">'. $value_name .'</label></div>';
																}																
																$html_values .= '<input type="hidden" name="'. ($tax_exists ? $value_slug : $value_name) .'" value="'. $value_slug .'"/>';
				                                            }
															if($layout == 'product_colors'){
																if(empty($hmtl_colors)){
																	$hmtl_colors = '<div class="dp-border-style"><p>'. __( 'No colors found, check attribute actions in the product', 'wcdp' ) .'</p></div>';
																}
																echo '<div class="dp-attr-colors dp-box-bgcolors">'. $hmtl_colors .'</div>';
															}
															if($layout == 'radio_checkbox'){
																echo '<div class="dp-attr-radio">'. $hmtl_radio .'</div>';
															}
															echo '<div class="dp-attr-values">'. $html_values .'</div>';
														} ?>
												    </div> 
					                                <?php
												} ?>
			                                </div>			    
			                                <div class="single_variation_wrap">
				                                <div class="woocommerce-variation single_variation"></div>
					                            <div class="woocommerce-variation-add-to-cart"></div>
					                            <input type="hidden" id="wcdp-variation-id" name="variation_id" class="variation_id">
			                                </div>				                        
	                                    </form> 
                                        <?php
                                    } else if($product->is_type('simple')){								
	                                    $price_html = $product->get_price_html();
                                        if($price_html && $product->get_price() >= 0){
									        $product_available = true;
									        $product_type = 'simple'; ?>
		                                    <div class="woocommerce-simple-price">
	                                            <div class="price dp-content-style dp-border-style"><?php echo $price_html; ?></div>
									        </div>	
                                        <?php                                    					
		                                }
                                    } 
							    }
							    if(!$product_available){ ?>
								    <div id="wcdp-product-unavailable" class="dp-box-note">
									    <p><?php _e( 'This product is currently out of stock and unavailable.', 'wcdp' ); ?></p>
		                            </div>
						        <?php }	?>								
					            </div>
								<input type="hidden" id="wcdp-product-type" value="<?php echo $product_type; ?>">
					        </div>
					        <?php if($product_available){ ?>
					        <div class="dp-row<?php if($product->get_sold_individually() === true) echo ' woo_qty_dsb'; ?>">
						        <div class="wcdp-content-tools dp-content-style dp-border-style">
						            <div id="wcdp-product-input-qty" class="dp-row">
							            <span id="wcdp-btn-product-qty"></span>
								        <label><?php _e( 'Quantity', 'wcdp' ); ?></label>
		                                <input type="number" class="input-text qty text" step="1" min="0" max="" name="quantity" value="1" size="4" pattern="[0-9]*" inputmode="numeric">
						            </div>
						        </div>	
					        </div>			
					        <?php } ?>					
                        </div>
					<?php }	if($wcdp_parameters['templates_e'] === 'on'){ ?>
                    <div class="wcdp-tab-section" id="wcdp-templates-panel">
                        <div class="dp-title-label"><?php echo $btnTabs['templates']; ?></div>
					    <div class="dp-row dp-input-btn">
                            <input type="text" id="wcdp-text-box-search-tpl" placeholder="<?php _e( "Search...", "wcdp" ) ?>"/>
                            <span class="dp-tooltip" id="wcdp-btn-search-tpl" title="<?php _e( "Search templates", "wcdp" ) ?>"></span>
					    </div>						
					    <div class="dp-row">
							<?php 
							$htmlTpl = '';
							if(isset($wcdp_parameters['multipleDesignsCat'])){							    
								?>
								<div class="dp-contain-box mc__sb dp-content-style dp-border-style"> 
                                <?php
								foreach($wcdp_parameters['multipleDesignsCat'] as $designCat){
									$cat = get_term_by('id', $designCat, 'wcdp-design-cat');
									if($cat){
								        $count = $cat->count;
									    ?>
								        <div class="dp-folder-contain dp-tpl" data-id="<?php echo $designCat; ?>">
  								            <span class="wcdp-icon-folder"></span>
								            <label><?php echo $cat->name .' ('. $count .')'; ?></label>
								        </div>
									    <?php
									    $htmlTpl .= '<div class="dp-tpl-content mc__sb" data-id="'. $designCat .'">';
									    if($count){
    	                                    $args = array(
                                                'post_type'      => 'wcdp-designs',
                                                'post_status'    => 'publish',
                                                'posts_per_page' => -1,
                                                'order'          => 'ASC',
                                                'orderby'        => 'title',
                                                'tax_query' => array(
                                                    array(
                                                        'taxonomy'         => 'wcdp-design-cat',
                                                        'field'            => 'term_id',
                                                        'terms'            => $designCat,
				                                        'include_children' => false
                                                    )
                                                )
                                            );
                                            $designs = get_posts($args);
									        foreach($designs as $design){
											    $pageID = wcdp_check_publish_design_page($wcdp_parameters['productID'], $design->ID);
										        $htmlTpl .= '<span class="dp-img-contain" data-name="'. $design->post_title .'" page-id="'. $pageID .'" design-id="'. $design->ID .'">';
										        $htmlTpl .= '<img class="lazyload dp-loading-lazy" src="'. $lazyLoader .'" data-src="'. WCDP_URL_UPLOADS .'/save-admin/designID'. $design->ID .'/cover.jpg"/></span>';
								            }	
								        } else{
										    $htmlTpl .= '<div class="dp-box-note">';
										    $htmlTpl .= '<p>'. __( "The category is empty", "wcdp" ) .'</p>';
										    $htmlTpl .= '</div>';
									    }
									    $htmlTpl .= '</div>';
									}
								} ?>
								</div>
								<?php
							}
							if(isset($wcdp_parameters['multipleDesigns'])){
								$htmlTpl .= '<div id="wcdp-tpl-general" class="dp-tpl-content mc__sb">';
								foreach($wcdp_parameters['multipleDesigns'] as $designID){
								    $pageID = wcdp_check_publish_design_page($wcdp_parameters['productID'], $designID);
									$htmlTpl .= '<span class="dp-img-contain" data-name="'. get_the_title($designID) .'" page-id="'. $pageID .'" design-id="'. $designID .'">';
									$htmlTpl .= '<img class="lazyload dp-loading-lazy" src="'. $lazyLoader .'" data-src="'. WCDP_URL_UPLOADS .'/save-admin/designID'. $designID .'/cover.jpg"/></span>';
								}
								$htmlTpl .= '</div>';
							} ?>
							<div class="dp-contain-tpl dp-box-img dp-content-style dp-border-style">
							    <div class="dp-tpl-content mc__sb dp-label-center" id="dp-box-search-tpl"></div>
							    <?php echo $htmlTpl; ?>
							</div>
					    </div>
                    </div>	
	                <?php }	?>				
                    <div class="wcdp-tab-section" id="wcdp-text-panel">
                        <div class="dp-title-label"><?php echo $btnTabs['text']; ?></div>
					    <div class="dp-row dp-col-8">  
					    <?php foreach($btnToolbarText as $btnText => $btnTextVal){
						    echo '<span class="dp-tooltip" id="wcdp-btn-'.$btnText.'" title="'.$btnTextVal.'"></span>';
                        } ?>
					    </div>
					    <div class="dp-row">
                            <select id="wcdp-text-fontFamily">
						        <option value="titillium"><?php _e( "Select Font", "wcdp" ) ?></option>
						        <?php 
							        $fontPreload = '';
							        if($storageFonts){
	                                    $preloadAlphabet = 'p';
	                                    if(isset($wcdp_general['preload_fonts'])){
		                                    if($wcdp_general['preload_fonts'] === 'arabic'){
			                                    $preloadAlphabet = 'ا ب ت ث ج ح خ د ذ ر ز س ش ص ض ط ظ ع غ ف ق ك ل م ن ه و ي · ﺍ ﺑ ﺗ ﺛ ﺟ ﺣ ﺧ ﺩ ﺫ ﺭ ﺯ ﺳ ﺷ ﺻ ﺿ ﻃ ﻇ ﻋ ﻏ ﻓ ﻗ ﻛ ﻟ ﻣ ﻧ ﻫ ﻭ ﻳ · ﺍ ﺒ ﺘ ﺜ ﺠ ﺤ ﺨ ﺪ ﺬ ﺮ ﺰ ﺴ ﺸ ﺼ ﻀ ﻄ ﻈ ﻌ ﻐ ﻔ ﻘ ﻜ ﻠ ﻤ ﻨ ﻬ ﻮ ﻴ · ﺎ ﺐ ﺖ ﺚ ﺞ ﺢ ﺦ ﺪ ﺬ ﺮ ﺰ ﺲ ﺶ ﺺ ﺾ ﻂ ﻆ ﻊ ﻎ ﻒ ﻖ ﻚ ﻞ ﻢ ﻦ ﻪ ﻮ ﻲ · ء- - - - - آ أ إ ة ؤ ئ ى · پ چ ژ گ ﭪ ۰ ۱ ۲ ۳ ٤ ٥ ٦ ٧ ۸ ۹';
		                                    }
		                                    else if($wcdp_general['preload_fonts'] === 'latin'){
			                                    $preloadAlphabet = 'ĀāĂăĄąĆćĈĉĊċČčĎďĐđĒēĔĕĖėĘęĚěĜĝĞğĠġĢģĤĥĦħĨĩĪīĬĭĮįİıĲĳĴĵĶķĸĹĺĻļĽľĿŀŁłŃńŅņŇňŉŊŋŌōŎŏŐőŒœŔŕŖŗŘřŚśŜŝŞşŠšŢţŤťŦŧŨũŪūŬŭŮůŰűŲųŴŵŶŷŸŹźŻżŽžſ';
		                                    }
	                                    }
							            foreach($storageFonts as $font){
											$font = $font['name'];
	                  		                echo '<option style="font-family:'. $font .'" value="'. $font .'">'. $font .'</option>';
                                            $fontPreload .= '<span style="font-family:'. $font .';">'. $preloadAlphabet .'</span>';							
		                                }
								    }
							    ?>
						    </select>
						    <div id="wcdp-fonts-preload">
						        <?php echo $fontPreload; ?>						
						    </div>
                            <select id="wcdp-text-fontSize">
					            <?php foreach(array(8,9,10,11,12,13,14,15,16,17,18,20,22,24,26,28,30,32,36,42,48,72,96) as $fontSize){ ?>
								    <option value="<?php echo $fontSize ?>" <?php if($wcdp_parameters['font_size'] == $fontSize) echo 'selected'; ?> ><?php echo $fontSize ?> px</option>
                                <?php } ?>
                            </select>
                        </div>
					    <div class="dp-row">					
					        <textarea id="wcdp-new-textbox" disabled="disabled"></textarea>
					    </div>
					    <div class="dp-row dp-btn-style">	
					        <span id="wcdp-add-new-text"><?php _e( "ADD NEW TEXT", "wcdp" ) ?></span>						
					    </div>
					    <?php foreach($tabsContentRowsText as $tabContentRowText){
                            echo wcdp_skin_html_content_tools($tabContentRowText);
	                    } ?>
                    </div>
                    <div class="wcdp-tab-section dp-mode-upload" id="wcdp-images-panel">
                        <div class="dp-title-label"><?php echo $btnTabs['images']; ?></div>
                        <?php 
						$arrRes = array();    
						$resources = array(
						    'upload'   => __( "Upload", "wcdp" ),
						    'pixabay'  => __( "Pixabay", "wcdp" ),
							'unsplash' => __( "Unsplash", "wcdp" ),
							'pexels'   => __( "Pexels", "wcdp" ),
							'flaticon' => __( "Flaticon", "wcdp" )
						);					
						foreach($resources as $resource => $value){  
							if($resource === 'upload' || isset($wcdp_general[$resource .'_api']) && $wcdp_general[$resource .'_api'])
								$arrRes[$resource] = $value;							
						}					
                        if(count($arrRes) > 1){	?>
						    <div class="dp-hori-tab">
							    <?php
						        foreach($arrRes as $arrRe => $name){
								    $selected = $arrRe == 'upload' ? ' htab-selected': '';
							        echo '<span class="wcdp-icon-'. $arrRe .' dp-tooltip'. $selected .'" data-id="'. $arrRe .'" title="'. $name .'"></span>';									
							    } ?>	
						    </div>					    
					        <div class="dp-row dp-input-btn md__res">
                                <input type="text" id="wcdp-text-box-search-res"/>
						        <span class="dp-tooltip" id="wcdp-btn-search-res" title="<?php _e( "Search images", "wcdp" ) ?>"></span>			
					        </div>
                            <div class="dp-row md__res">
							    <div class="dp-contain-res dp-content-style dp-border-style dp-label-center">
						            <?php 
								    unset($arrRes['upload']);
								    foreach($arrRes as $arrRe => $name){ 
    							        echo '<div id="wcdp-box-'. $arrRe .'" class="dp-res-content mc__sb" data-id="'. $arrRe .'"><div class="dp-res-items"></div></div>';
						            } ?>
						        </div>
							</div> 
						    <?php
						} ?>
					    <div class="dp-row md__upl">
					        <div class="dp-contain-box mc__sb dp-box-img dp-content-style dp-border-style dp-label-center">
						        <span class="dp-search-empty" id="wcdp-btn-upload-cloud"></span>
                                <label><?php _e( "No uploaded images found", "wcdp" ) ?></label>
						    </div>
					    </div>
					    <div class="dp-row md__upl">
					        <?php
					        $htmlUploads  = '<span id="wcdp-btn-upload-img"></span>';
					        $htmlUploads .= '<label>'. __( "UPLOAD IMAGE", "wcdp" ) .'</label>';
					        if($frontend){ ?>
					            <form id="wcdp-form-upload-files-user" enctype="multipart/form-data">
						            <input type="file" id="wcdp-ajax-upload-user">
								    <div id="wcdp-upload-images" class="dp-btn-style-ico dp-border-style">
							            <?php echo $htmlUploads; ?>		
                                    </div>								
						        </form>
					        <?php
						    } else{ 
					            wp_enqueue_media(array('post' => $wcdp_parameters['designID'])); ?>  
						        <div id="wcdp-media-editor-backend" format="image" support="jpg,jpeg,png,gif,svg">
							        <div id="wcdp-upload-images-backend" class="dp-btn-style-ico dp-border-style">
			                            <?php echo $htmlUploads; ?>
                                    </div>									
						        </div>				
					        <?php
						    } ?>
                        </div>
                        <?php
						$htmlOptionsImg = '<div class="dp-toolbar-img">';
						foreach($btnToolbarImg as $btnImg => $btnImgVal){
							$selected = $btnImg == 'fill' ? ' opt-selected': '';
						    $htmlOptionsImg .='<span class="wcdp-icon-'. $btnImg .' dp-tooltip'. $selected .'" data-id="'. $btnImg .'" title="'. $btnImgVal .'"></span>';									
						}
						$htmlOptionsImg .= '</div>';
						echo $htmlOptionsImg;
						foreach($tabsContentRowsImage as $tabContentRowImage){
                            echo wcdp_skin_html_content_tools($tabContentRowImage);
	                    }
						if($setMask){
						    $htmlMaskBox  = '<div class="dp-row box__tool box__dsb" data-id="mask">';
						    $htmlMaskBox .= '<label class="dp-title-box">'. __( "Mask layer", "wcdp" ) .'</label>';
						    $htmlMaskBox .= '<div class="dp-contain-mask mc__sb dp-content-style dp-border-style"></div>';
							$htmlMaskBox .= '<div class="dp-remove-mask dp-btn-style-ico dp-border-style">';
                            $htmlMaskBox .= '<span class="wcdp-icon-clear-mask"></span>';
							$htmlMaskBox .= '<label>'. __( "REMOVE MASK", "wcdp" ) .'</label></div></div>';
							echo $htmlMaskBox;
						}
                        if(array_key_exists('filters', $btnToolbarImg)){
							$filtersRng = '';
							$filtersBtn = '';
							$imgPosition = 0;
                            $htmlFilters  = '<div class="dp-row box__tool box__dsb" data-id="filters">';
                            $htmlFilters .= '<label class="dp-title-box">'. __( "Filters", "wcdp" ) .'</label>';							
							$htmlFilters .= '<div class="dp-content-style dp-border-style">';							
							$filters = wcdp_content_html_filters();
					        foreach($filters as $f => $fv){
								$rng = $f == 'brightness' || $f == 'saturate' || $f == 'contrast' ? true : false;
								if(isset($optionFilter[$f]) && $optionFilter[$f] == 'on'){									
									if($rng)
										$filtersRng .= '<div class="dp-row"><label>'. $fv .'</label><input type="range" data-filter="'. $f .'" min="-1" max="1" value="0" step="0.01"></div>';
									else
					           	        $filtersBtn .= '<span class="dp-tooltip" title="'. $fv .'" data-filter="'. $f .'" style="background-position: 0px '. $imgPosition .'px;"></span>';
    							}
								if(!$rng)
									$imgPosition -= 62;
							}
							if($filtersBtn)								
								$htmlFilters .= '<div class="wcdp-box-filters-btn mc__sb">'. $filtersBtn .'</div>';							
							if($filtersRng)							
								$htmlFilters .= '<div class="wcdp-box-filters-rng'. (!$filtersBtn ? ' br__dsb':'') .'">'. $filtersRng .'</div>';								
							
							$htmlFilters .= '</div></div>';
					        echo $htmlFilters;
					    } ?>
                    </div>		
				    <?php if(isset($wcdp_parameters['shapes_e']) && $wcdp_parameters['shapes_e'] === 'on'){ ?>
                    <div class="wcdp-tab-section" id="wcdp-shapes-panel">
                        <div class="dp-title-label"><?php echo $btnTabs['shapes']; ?></div>
					    <div class="dp-row">
					        <div class="dp-contain-box dp-box-shap mc__sb dp-content-style dp-border-style dp-label-center">
                                <?php $shapes = wcdp_content_html_shapes();
								      $optionShap = get_option('wcdp-settings-shapes');
								      if(!empty($optionShap) && in_array('on', $optionShap)){
										  $i=0;		
	                                      foreach($shapes as $shap){
                                              if(isset($optionShap[$i]) && $optionShap[$i] == 'on'){ ?>
	                                              <span class="dp-img-contain">
	                                                  <svg xmlns="http://www.w3.org/2000/svg" width="30px" height="30px" viewBox="0 0 300 300">
	                                                      <?php echo $shap; ?>
				                                      </svg>
		                                          </span>
											      <?php
											  } $i++;												
	                                      }	                                
							          } else{ ?>  
							               <span class="dp-search-empty" id="wcdp-btn-shapes-empty"></span>
							               <label><?php _e( "No shapes found", "wcdp" ) ?></label>
							    <?php } ?>
						    </div>
					    </div>
					    <?php foreach($tabsContentRowsShapes as $tabsContentRowShape){
                            echo wcdp_skin_html_content_tools($tabsContentRowShape);
	                    } ?>	
                    </div>
				    <?php } if(isset($wcdp_parameters['cliparts_e']) && $wcdp_parameters['cliparts_e'] === 'on'){ ?>				
                    <div class="wcdp-tab-section" id="wcdp-cliparts-panel">
                        <div class="dp-title-label"><?php echo $btnTabs['cliparts']; ?></div>
					    <div class="dp-row dp-input-btn">
                            <input type="text" id="wcdp-text-box-search-clip" placeholder="<?php _e( "Search...", "wcdp" ) ?>"/>
                            <span class="dp-tooltip" id="wcdp-btn-search-clip" title="<?php _e( "Search cliparts", "wcdp" ) ?>"></span>
					    </div>
					    <div class="dp-row">
					        <div class="dp-contain-box mc__sb dp-content-style dp-border-style">
                            <?php 
							    $arrClips = array();
								$cliparts = $wcdp_parameters['category_cliparts'];
								foreach($cliparts as $clipart){
									if(get_post_status($clipart) == 'publish')
										$arrClips[] = $clipart;									
								}
								if($arrClips){
                                    $htmlClipContent = '';									
						            foreach($arrClips as $clipID){
                                        $clipItems = get_post_meta($clipID, '_wcdp_uploads_cliparts', true); 
									    $count = !empty($clipItems) ? count($clipItems) : 0;
										?>
								        <div class="dp-folder-contain dp-clip" data-id="<?php echo $clipID; ?>">
  								            <span class="wcdp-icon-folder"></span>
								            <label><?php echo get_the_title($clipID); ?> (<?php echo $count; ?>)</label>
								        </div>
										<?php										 
										$htmlClipContent .= '<div class="dp-clip-content mc__sb" data-id="'. $clipID .'">';
										if($count){	
										    foreach($clipItems as $clipItem){
												$thumb = wp_get_attachment_image_src($clipItem, 'thumbnail');
												$imageURL = wp_get_attachment_url($clipItem);
											    $identFile = pathinfo($imageURL, PATHINFO_EXTENSION) == 'svg' ? 'dp-svg':'dp-img';																								
											    $htmlClipContent .= '<span class="dp-img-contain '. $identFile .'" data-source="'. $imageURL .'" data-name="'. get_the_title($clipItem) .'">';
											    $htmlClipContent .= '<img class="lazyload dp-loading-lazy" src="'. $lazyLoader .'" data-src="'. ($thumb ? $thumb[0] : $imageURL) .'"/></span>';
											}
                                        } else{
										    $htmlClipContent .= '<div class="dp-box-note">';
										    $htmlClipContent .= '<p>'. __( "The category is empty", "wcdp" ) .'</p>';
										    $htmlClipContent .= '</div>';
										}
										$htmlClipContent .= '</div>';
								    } 
								} else{ ?>
								    <div class="dp-label-center">
									    <span class="dp-search-empty" id="wcdp-btn-clip-empty"></span>
							            <label><?php _e( "No uploaded cliparts found", "wcdp" ) ?></label>
									</div>
							        <?php	
							    } ?>
							</div>
							<?php if(isset($htmlClipContent)){ ?>
							    <div class="dp-contain-clip dp-content-style dp-border-style">
							        <div class="dp-clip-content mc__sb dp-label-center" id="dp-box-search-clip"></div>
							        <?php echo $htmlClipContent; ?>
							    </div>
							<?php } ?>					
					    </div>
					    <?php
						echo $htmlOptionsImg;
						foreach($tabsContentRowsCliparts as $tabContentRowClipart){
                            echo wcdp_skin_html_content_tools($tabContentRowClipart);
	                    }
						if(isset($htmlMaskBox))
						    echo $htmlMaskBox;
						
						if(isset($htmlFilters))
						    echo $htmlFilters;
                        ?>
                    </div>
                    <?php } if(isset($wcdp_parameters['qr_e']) && $wcdp_parameters['qr_e'] === 'on'){ ?>	
                    <div class="wcdp-tab-section" id="wcdp-qrcode-panel">
                        <div class="dp-title-label"><?php echo $btnTabs['qr']; ?></div>
					    <div class="dp-row dp-input-btn">
                            <input type="text" id="wcdp-text-box-qr" placeholder="<?php _e( "http://www.your-web-url", "wcdp" ) ?>"/>
                            <span class="dp-tooltip" id="wcdp-btn-make-qr" title="<?php _e( "Generate QR code", "wcdp" ) ?>"></span>
					    </div>
					    <?php foreach($tabsContentRowsQRcode as $tabContentRowQRcode){
                            echo wcdp_skin_html_content_tools($tabContentRowQRcode);
	                    }
						if(!$hideNotes){ ?>
					        <div class="dp-row">
					            <div class="dp-box-note dp-border-style">
						            <p><?php _e( "Note!! QR codes are not always legible. Try it before continuing.", "wcdp" ) ?></p>
						        </div>
					        </div>
                        <?php } ?>
                    </div>					
                    <?php } if(isset($wcdp_parameters['maps_e']) && $wcdp_parameters['maps_e'] === 'on'){ ?>				
                    <div class="wcdp-tab-section" id="wcdp-maps-panel">
                        <div class="dp-title-label"><?php echo $btnTabs['maps']; ?></div>
					    <div class="dp-row dp-input-btn">
                            <input type="text" id="wcdp-text-box-map" placeholder="<?php _e( "Add your address", "wcdp" ) ?>"/>
                            <span class="dp-tooltip" id="wcdp-btn-make-map" title="<?php _e( "Make map", "wcdp" ) ?>"></span>
					    </div>
					    <?php
						foreach($tabsContentRowsMaps as $tabsContentRowMap){
                            echo wcdp_skin_html_content_tools($tabsContentRowMap);
	                    } ?>
						<div class="dp-toolbar-img">
						    <?php
						    foreach($btnToolbarImg as $btnImg => $btnImgVal){
							    if($btnImg != 'fill' && $btnImg != 'mask' && $btnImg != 'filters')
						            echo '<span class="wcdp-icon-'. $btnImg .' dp-tooltip'. $selected .'" data-id="'. $btnImg .'" title="'. $btnImgVal .'"></span>';									
						    } ?>
						</div>
						<?php
						if(!$hideNotes){ ?>
					        <div class="dp-row">
					            <div class="dp-box-note dp-border-style">
						            <p><?php _e( "Note!! Make sure the map match with you address.", "wcdp" ) ?></p>
						        </div>
					        </div>
						<?php } ?>
                    </div>	
                    <?php } if(isset($wcdp_parameters['calendars_e']) && $wcdp_parameters['calendars_e'] === 'on'){ ?>	
                    <div class="wcdp-tab-section" id="wcdp-calendars-panel">
                        <div class="dp-title-label"><?php echo $btnTabs['calendars']; ?></div>
					    <div class="dp-row dp-input-btn">
                            <input type="text" id="wcdp-text-box-search-caz" placeholder="<?php _e( "Search...", "wcdp" ) ?>"/>
                            <span class="dp-tooltip" id="wcdp-btn-search-caz" title="<?php _e( "Search calendars", "wcdp" ) ?>"></span>
					    </div>
					    <div class="dp-row">
					        <div class="dp-contain-box mc__sb dp-content-style dp-border-style">
                            <?php 							    
							    $arrCazs = array();
								$calendars = $wcdp_parameters['category_calendars'];
								foreach($calendars as $calendar){
									if(get_post_status($calendar) == 'publish')
										$arrCazs[] = $calendar;									
								}
								if($arrCazs){
                                    $htmlCazContent = '';									
						            foreach($arrCazs as $cazID){
                                        $cazItems = get_post_meta($cazID, '_wcdp_uploads_calendars', true); 
										$count = !empty($cazItems) ? count($cazItems) : 0;
										?>
								        <div class="dp-folder-contain dp-caz" data-id="<?php echo $cazID; ?>">
  								            <span class="wcdp-icon-folder"></span>
								            <label><?php echo get_the_title($cazID); ?> (<?php echo $count; ?>)</label>
								        </div> 										
										<?php
										$htmlCazContent .= '<div class="dp-caz-content mc__sb" data-id="'. $cazID .'">';
									    if($count){										    
										    foreach($cazItems as $cazItem){
                                                $imageURL = wp_get_attachment_url($cazItem);												
											    $htmlCazContent .= '<span class="dp-img-contain" data-name="'. get_the_title($cazItem) .'">';
												$htmlCazContent .= '<img class="lazyload dp-loading-lazy" src="'. $lazyLoader .'" data-src="'. $imageURL .'"/></span>';
    							            }
									    } else{
										    $htmlCazContent .= '<div class="dp-box-note">';
										    $htmlCazContent .= '<p>'. __( "The category is empty", "wcdp" ) .'</p>';
										    $htmlCazContent .= '</div>';
										}
									    $htmlCazContent .= '</div>';										
								    } 
								} else{ ?>
								    <div class="dp-label-center">
									    <span class="dp-search-empty" id="wcdp-btn-caz-empty"></span>
							            <label><?php _e( "No uploaded calendars found", "wcdp" ) ?></label>
									</div>
							        <?php	
							    } ?>
							</div>
							<?php if(isset($htmlCazContent)){ ?>
							    <div class="dp-contain-caz dp-content-style dp-border-style">
							        <div class="dp-caz-content mc__sb dp-label-center" id="dp-box-search-caz"></div>
							        <?php echo $htmlCazContent; ?>
							    </div>
							<?php } ?>					
					    </div>
                    </div>						
                    <?php } if(isset($wcdp_parameters['bgcolors_e']) && $wcdp_parameters['bgcolors_e'] === 'on'){ ?>		
                    <div class="wcdp-tab-section" id="wcdp-bgcolors-panel">
                        <div class="dp-title-label"><?php echo $btnTabs['bgcolors']; ?></div>					
					    <?php if(isset($wcdp_style['background_palette'])){ ?>
					    <div class="dp-row">
					        <div class="dp-contain-box dp-box-bgcolors mc__sb dp-content-style dp-border-style dp-label-center">
                                <?php $bgColors = $wcdp_style['background_palette'];
							        foreach($bgColors['RGB'] as $index => $value){
	                  		            echo '<span style="background-color:'.$value.'" data-cmyk="'.$bgColors['CMYK'][$index].'"></span>';					
								    } 
							    ?>
						    </div>
					    </div>
					    <?php } 
					    foreach($tabsContentRowsBgColors as $tabsContentRowBgColor){
                            echo wcdp_skin_html_content_tools($tabsContentRowBgColor);
	                    } ?>
                    </div>
                    <?php } if(isset($wcdp_parameters['layers_e']) && $wcdp_parameters['layers_e'] === 'on'){ ?>
                    <div class="wcdp-tab-section" id="wcdp-layers-panel">
                        <div class="dp-title-label"><?php echo $btnTabs['layers']; ?></div>
                        <?php if(!$frontend){ ?>
						    <div class="dp-hori-tab">
							    <span id="wcdp-btn-lock-user" class="dp-tooltip" title="<?php _e( "Lock layer to user", "wcdp" ) ?>"></span>
								<span id="wcdp-btn-out-hide"class="dp-tooltip" title="<?php _e( "Hide layer in the output files", "wcdp" ) ?>"></span>
						    </div>
						<?php } ?>
                        <div id="wcdp-box-layers" class="dp-row">
						    <div id="wcdp-contain-layers" class="mc__sb">
							    <div class="wcdp-layers-items"></div>
							</div>
						</div>
                    </div>
                    <?php } if($frontend){ ?>
                    <div class="wcdp-tab-section" id="wcdp-my-designs-panel">
                        <div class="dp-title-label"><?php echo $btnTabs['my-designs']; ?></div>
   				        <div class="dp-row">
					        <div class="dp-contain-box mc__sb dp-content-style dp-border-style">
						    <?php
                                if(is_user_logged_in()){	
		                            $user_ID = get_current_user_id();
								    $get_designs_list = get_user_meta($user_ID, '_wcdp_designs_save_user_list', true);
                                    if($get_designs_list){
				                        foreach($get_designs_list as $designKey => $designVal){
											$templateID = 0;
											$designName = 0;
											$productID = $designVal;
										    if(is_array($designVal)){											    
	                                            $templateID = $designVal['designID'];
												$productID = $designVal['productID'];
												$designName = isset($designVal['designName']) && !empty($designVal['designName']) ? $designVal['designName'] : 0;
											}
										    $pageID = wcdp_check_publish_design_page($productID, $templateID);
											$getTitle = empty($designName) ? get_the_title($productID) : $designName;
											$designTitle = $getTitle ? $getTitle : '---';
     									    ?>
		                                    <div class="dp-my-design-contain dp-border-style" page-id="<?php echo $pageID; ?>" pid="<?php echo $productID; ?>" sid="<?php echo $designKey; ?>">										
										        <span class="dp-my-design-cover" title="<?php echo $designTitle; ?>" data-name="<?php echo $designTitle; ?>">
							                        <img src="<?php echo WCDP_URL_UPLOADS .'/save-user/'. $designKey .'/cover.jpg'; ?>"/>
											    </span>
											    <div class="dp-loader-box"></div>
											    <div class="dp-remove-my-design"></div>
										   </div>
                                           <?php 										
				                        }
			                        } else{
				                        ?>
						                <div class="dp-label-center">
						                    <span class="dp-search-empty" id="wcdp-btn-mydesigns-empty"></span>
						                    <label><?php _e( "No saved designs found", "wcdp" ) ?></label>
						                </div>
				                        <?php
			                        }
		                        } else{
                                    ?>
			                        <div class="dp-box-note">
			                            <p><?php _e( "Note!! You must login to access your designs.", "wcdp" ) ?></p>
			                        </div>
			                        <?php
		                        }		
						    ?>
                            </div>										
			            </div>
					    <?php
						    $downloadOnlyUserLogged = isset($wcdp_general['download_design_logged']) && $wcdp_general['download_design_logged'] === 'on' ? true : false;
						    if(isset($wcdp_general['download_design_editor']) && $wcdp_general['download_design_editor'] === 'on' && (!$downloadOnlyUserLogged || $downloadOnlyUserLogged && is_user_logged_in())){ ?>
                            <div class="dp-row">
						        <div id="wcdp-download-my-design" class="dp-btn-style-ico dp-border-style">
						            <span id="wcdp-btn-download-design"></span>
						            <label><?php _e( "DOWNLOAD", "wcdp" ) ?></label>
						        </div>	
                            </div>
                        <?php }	?>				
                    </div>
                    <?php } else{ ?>
                    <div class="wcdp-tab-section" id="wcdp-code-panel">
                        <div class="dp-title-label"><?php echo $btnTabs['code']; ?></div>
					    <div class="dp-row">
					        <?php
						        $canvasConsole  = "// Add red rectangle \ncanvas.add(new fabric.Rect";
						        $canvasConsole .="({\n  width: 50,\n  height: 50,\n  left: 50,\n  top: 50,\n  fill: 'rgb(255,0,0)'\n}));";
					        ?>
	                        <textarea id="wcdp-canvas-console" spellcheck="false"><?php echo $canvasConsole;?></textarea>			
					    </div>
					    <div class="dp-row">
					        <div class="wcdp-box-code dp-content-style dp-btn-style dp-border-style">
					            <?php					
	                                $btnCodeIns = array(
                                        'run'   => __( 'Execute', 'wcdp' ),						
		                                'get'   => __( 'Get Json', 'wcdp' ),
		                                'add'   => __( 'Add Json', 'wcdp' ),
                                        'img'   => __( 'Get Image', 'wcdp' ),
		                                'clear' => __( 'Clear', 'wcdp' )                           
	                                );					
					                foreach($btnCodeIns as $btnCodeIn => $btnCodeInVal){
					                    echo '<span id="wcdp-code-'.$btnCodeIn.'">'.$btnCodeInVal.'</span>';
	                                }
					            ?>	
                            </div>						
                        </div>					
                    </div>
                    <?php } ?>				
			    </div>
			    <div id="wcdp-box-options-btn">
				    <?php foreach($btnBoxOptions as $btnBoxOption => $btnBoxOptionVal){
					    $html  = '<div class="dp-btn-style-ico dp-border-style">';
					    $html .= '<span id="wcdp-btn-'.$btnBoxOption.'"></span>';
					    $html .= '<label>'.$btnBoxOptionVal.'</label>';
					    $html .= '</div>';
					    echo $html;
                    } ?>
		        </div>
            </div>		
	    </div>
	    <div id="wcdp-editor-container">
	        <div id="wcdp-toolbar-options">
	        <?php foreach($btnToolbar as $btnTool => $btnToolVal){ 
				$dSb = ($btnTool == 'preview' && $wcdp_parameters['preview_e'] == 'off') ? ' btn-disabled' : '';
				$blg = $btnTool == 'lock' || $btnTool == 'group';
	            echo '<span class="dp-tooltip'. ($blg ? '-cg' : '') . $dSb .'" id="wcdp-btn-'. $btnTool .'" '. ($blg ? 'data-jbox-content' : 'title') .'="'. $btnToolVal .'"></span>';
		    } ?>
		    </div>		
		    <div id="wcdp-canvas-container">
                <div id="wcdp-thumbs-preview">
			        <span class="dp-title"></span>
					<picture></picture>
			        <span class="dp-tags"></span>
		        </div>
		        <div id="wcdp-images-preload">
                    <img src='<?php echo WCDP_URL;?>/assets/images/save-loader.gif'/>
			    </div>
		        <canvas id="wcdp-canvas-editor" width="<?php echo $wcdp_parameters['canvas_w']; ?>" height="<?php echo $wcdp_parameters['canvas_h']; ?>"></canvas>
		    </div>			
			<div id="wcdp-opts-below-canvas">			
		        <div id="wcdp-canvas-thumbs-container">
		        <?php
		            $sidesCanvas['front'] = $wcdp_parameters['label_f'];
					$base64 = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
		            if($wcdp_parameters['type'] != 'typeF')	$sidesCanvas['back'] = $wcdp_parameters['label_b'];
		            foreach($sidesCanvas as $sideCanvas => $sideVal){
			            $html  = '<div data-id="'. $sideCanvas .'">';
			            $html .= '<img src="'. $base64 .'" width="100" style="height:'. 100 * $wcdp_parameters['canvas_h'] / $wcdp_parameters['canvas_w'] .'px"/><label>'. $sideVal .'</label>';
			            $html .= '</div>';
			            echo $html;
		            } ?>
		        </div>
			    <div id="wcdp-zoom-canvas-container">
			        <span class="wcdp-icon-zoom dp-tooltip" title="<?php _e( 'Zoom', 'wcdp' ); ?>"></span>				
			        <input type="range" id="wcdp-zoom-canvas" min="100" max="500" value="100">
				    <span class="dp-zoom-value">100%</span>		
			    </div>			
			</div>
	    </div>
    </div>
    <?php
}

// Add settings skin html content tools
function wcdp_skin_html_content_tools($args){
	$html = '';
	$type = $args['type'];
    if($type == 'box-content-tools'){
		if($args['box'] == 'open'){
		    $ident = isset($args['ident']) ? ' '. $args['ident'] : '';
			$data_id = isset($args['data_id']) ? ' data-id="'. $args['data_id'] .'"' : '';
			$html .='<div class="dp-row'. $ident .'"'. $data_id .'>';
			if(isset($args['title'])){
			    $html .= '<label class="dp-title-box">'. $args['title'] .'</label>';
			}
	        $html .='<div class="wcdp-content-tools dp-content-style dp-border-style">';
		    return $html;
		} else{
			return '</div></div>';
		}
	} else if($type == 'box-effects'){
		if($args['box'] == 'open'){
	        $html .='<div class="dp-box-effects dp-border-btn-style">';
		    $html .='<div class="dp-border-style">';
			return $html;
		} else{
			return '</div></div>';
		}
	} else{
	    $html .='<div class="dp-row">';
        if(isset($args['btn'])) $html .='<span id="wcdp-btn-'.$args['btn'].'"></span>';
        if(isset($args['label'])) $html .='<label>'.$args['label'].'</label>';
        if($type == 'color'){			
		    $html .='<input type="text" id="wcdp-'.$args['data'][0].'" class="spectrum-js" callback="'.$args['data'][1].'" cmyk="'.$args['data'][2].'" value="'.$args['data'][3].'"/>';					
        } else if($type == 'select'){
            $html .='<select id="wcdp-'.$args['data'][0].'">';
            $html .='<option value="'.$args['data'][1].'">'.$args['data'][2].'</option>';			
            foreach($args['data'][3] as $optionValue){
		        if(is_array($optionValue)){
                    foreach($optionValue as $key => $value){
                        $html .= '<option value="'.$key.'">'.$value.'</option>';					
				    }
		        } else{
                    $html .= '<option value="'.$optionValue.'">'.$optionValue . $args['data'][4].'</option>';
			    }
            }
            $html .='</select>';                        			
        } else if($type == 'range'){
	        $html .='<input type="range" id="wcdp-'.$args['data'][0].'" min="'.$args['data'][1].'" max="'.$args['data'][2].'" value="'.$args['data'][3].'" step="'.$args['data'][4].'">';
	    } else if($type == 'div'){
	        $html .='<div class="wcdp-'.$args['class'].'"></div>';
	    }
	    $html .= '</div>';
	    return $html;
	}
}