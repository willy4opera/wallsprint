<?php

// Register Designs post type
function wcdp_designs_init(){	
	$labels = array(
		'name'               => __( 'Designs', 'wcdp' ),
		'singular_name'      => __( 'Design', 'wcdp' ),
		'menu_name'          => __( 'Designs', 'wcdp' ),
		'add_new'            => __( 'Add new design', 'wcdp' ),
		'add_new_item'       => __( 'Add new design', 'wcdp' ),
		'new_item'           => __( 'New Design', 'wcdp' ),
		'edit_item'          => __( 'Edit design', 'wcdp' ),
		'view_item'          => __( 'View design', 'wcdp' ),
		'search_items'       => __( 'Search designs', 'wcdp' ),
		'not_found'          => __( 'No designs found.', 'wcdp' ),
		'not_found_in_trash' => __( 'No designs found in Trash.', 'wcdp' )
	);
	$args = array(
		'labels'              => $labels,
        'description'         => __( 'Settings designs.', 'wcdp' ),
		'supports'            => array( 'title' ),
		'public'              => false,
		'publicly_queryable'  => false,
		'show_ui'             => true,
		'can_export'          => true,
		'show_in_menu'        => false,
		'query_var'           => false,
		'has_archive'         => false,
		'show_in_nav_menus'   => false,
		'exclude_from_search' => true,
		'hierarchical'        => true
	);
	register_post_type( 'wcdp-designs', $args );
}
add_action( 'init', 'wcdp_designs_init' );

// Add to designs section new columns
function wcdp_designs_section_columns($columns){	
    $columns = array(
		'cb'    => '<input type="checkbox" />',
		'thumb' => '<span class="wcdp-image-design">'. __( 'Image', 'wcdp' ) .'</span>',
		'title' => __( 'Design Name', 'wcdp' ),
		'param' => __( 'Parameter', 'wcdp' ),
		'cat'   => __( 'Categories', 'wcdp' ),
		'date'  => __( 'Date', 'wcdp' )
	);	
	return $columns;
}
function wcdp_designs_section_manage_columns($column, $post_id){
    switch($column){
        case 'thumb' :
		    $thumb = '/save-admin/designID'. $post_id .'/cover.jpg';
		    $src = is_file(WCDP_PATH_UPLOADS . $thumb) ?  WCDP_URL_UPLOADS . $thumb : WCDP_URL .'/assets/images/placeholder.png';
		     ?>
		        <a href="<?php echo get_edit_post_link($post_id); ?>">
		            <img src="<?php echo $src; ?>" class="attachment-thumbnail size-thumbnail wp-post-image" alt=""/>
		        </a>
		    <?php			
		    break;
        case 'param' :
		    $getParamID = $get_parameter_design = get_post_meta($post_id, '_wcdp_parameter_design', true);
		    echo $getParamID ? get_the_title($getParamID) : '';
            break;
        case 'cat' :
		    $terms = get_the_term_list($post_id, 'wcdp-design-cat', '', ', ', '');
            $terms = strip_tags($terms);
	        echo $terms;
            break;
    }
}
add_filter('manage_edit-wcdp-designs_columns', 'wcdp_designs_section_columns');
add_action('manage_wcdp-designs_posts_custom_column', 'wcdp_designs_section_manage_columns', 10, 2);

// Sortable designs by parameters
add_filter('manage_edit-wcdp-designs_sortable_columns',  function($columns){
	$columns['param'] = 'param';
	$columns['cat'] = 'cat';
	return $columns;
});
add_action('pre_get_posts', function($query){
	if(is_admin() && $query->get('post_type') === 'wcdp-designs' &&  $query->get('orderby') == 'param'){
        $query->set('meta_key', '_wcdp_parameter_design');
        $query->set('orderby', 'meta_value_num');
        return $query;
	}
});

// Enable full screen to design on the backend
add_filter('screen_layout_columns', function($columns){
    $columns['wcdp-designs'] = 1;
    return $columns;
});
add_filter('get_user_option_screen_layout_wcdp-designs', function(){
    return 1;
});

// Add admin design metabox parameter
function wcdp_designs_metabox_param_html($post){
	$get_parameter_design = get_post_meta($post->ID,'_wcdp_parameter_design', true );
	$paramsID = 'dp_select_parameter_design_dropdown';
	$paramsDefault = __( 'Default parameter', 'wcdp' );
	$type = 'wcdp-parameters';
	$posts = get_posts( array (
	    'post_status' => 'publish',
	    'numberposts' => -1,
	    'post_type' => $type		
	));
	if(empty($posts)){
	    echo '<select name="' . $paramsID . '" id="' . $paramsID .'"><option value="">' . $paramsDefault . '</option></select>';
	} else{
 	    wp_dropdown_pages( array(
	        'depth' => -1,
	        'post_status' => 'publish',
	        'show_option_none' => $paramsDefault,
	        'selected' => $get_parameter_design,
	        'name'=> $paramsID,
            'post_type' => $type
        ));
    }    
}

// Add admin design metabox editor
function wcdp_designs_metabox_editor_html($post){
	$args = array(
	    'editor'      => 'backend',		 
		'designID'    => $post->ID,
		'designURL'   => WCDP_PATH_UPLOADS .'/save-admin/designID'. $post->ID,
		'productID'   => false,
		'attr_data'   => false,
		'pageID'      => false,
		'mode'        => false
	);
    $params = wcdp_get_params_product_designer($args);
    wcdp_designer_pro_content_store($params);
}

// Add metabox custom product
function wcdp_personalize_product_meta_box($post){
    $designID = get_post_meta($post->ID,'_wcdp_product_design_id', true);
	$get_multiple_designs = get_post_meta($post->ID,'_wcdp_product_multiple_designs', true);
    $personalize_product = get_post_meta($post->ID,'_wcdp_personalize_product', true);
	$load_designs_by_objs = get_post_meta($post->ID,'_wcdp_load_designs_by_objs', true);
	$load_designs_by_ajax = get_post_meta($post->ID,'_wcdp_load_designs_by_ajax', true);
	$get_multiple_designs_cat = get_post_meta($post->ID,'_wcdp_product_multiple_designs_cat', true);	
	$design_cats = get_terms('wcdp-design-cat', array('hide_empty' => false));	
	$cats_html = wcdp_sort_designs_cat_hierarchicaly($design_cats, $get_multiple_designs_cat, 0);
    ?>
	<script> var AJAX_URL = <?php echo json_encode( admin_url( 'admin-ajax.php' ) ); ?>; </script>
    <div>	                                         
	    <label for="dp-personalize-product">
		    <input type="checkbox" id="dp-personalize-product" name="personalize-product-checkbox" <?php checked('on', $personalize_product); ?> />
			<?php _e( 'Enable Custom Product.', 'wcdp' ); ?>
		</label>        
	    <?php	
            $cover = '';			
	        $html = '<p>'. __( "Select main design:", "wcdp" ) .'</p><p><select name="wcdp_select_design_id" post-id="designs" id="wcdp_select_design_id">';
	        
			if($designID && get_post_status($designID) == 'publish'){					
			    $title = wcdp_get_post_title($designID);
		        $html .= '<option value="'. $designID .'" selected="selected">'. $title .'</option>';
			    $cover = '<p><img class="dp-main-design" src="'. WCDP_URL_UPLOADS .'/save-admin/designID'. $designID .'/cover.jpg"/></p>';
	        } 
		    $html .= '</select></p>';
		    $html .= $cover;
			$html .= '<p>'. __( "Add multiple designs to the templates section:", "wcdp" ) .'</p>';
			$html .= '<p><select name="wcdp_select_multiple_designs[]" post-id="designs" id="wcdp_select_multiple_designs" multiple="multiple">';		    
			
            if($get_multiple_designs){
		        foreach($get_multiple_designs as $designID){
					if(get_post_status($designID) == 'publish'){
				        $title = wcdp_get_post_title($designID);
			            $html .= '<option value="' . $designID . '" selected="selected">'. $title .'</option>';
					}
		        }
	        }
			$html .= '</select></p>';
			if($cats_html){
				$html .= '<p>'. __( "Add designs to the templates section by categories:", "wcdp" ) .'</p>';
                $html .= '<div id="wcdp-design-cat-contain"><ul class="dp-design-cat-checklist">'. $cats_html. '</ul></div>';
			}
			echo $html;
		?>
	    <p>
		    <label for="dp-load-designs-objs">
		        <input type="checkbox" class="wcdp-settings-tpl" id="dp-load-designs-objs" name="load-designs-objs-checkbox" <?php checked('on', $load_designs_by_objs); ?> />
			    <?php _e( 'Load only template objects.', 'wcdp' ); ?>
		    </label>
		    <?php echo wc_help_tip( __( "This option will add only the front template objects to the active side of the editor without loading the parameter, improving speed. Recommended to add simple templates as ideas.", "wcdp" )); ?>
	    </p>
		<p>
		    <label for="dp-load-designs-ajax">
		        <input type="checkbox" class="wcdp-settings-tpl" id="dp-load-designs-ajax" name="load-designs-ajax-checkbox" <?php checked('on', $load_designs_by_ajax); ?> />
			    <?php _e( 'Load parameters by AJAX query.', 'wcdp' ); ?>
		    </label>  
		     <?php echo wc_help_tip( __( "This option will load only the canvas sizes. If you want to load the complete parameter of the designs added in multiples, leave it disabled.", "wcdp" )); ?>
        </p>
	</div>
    <?php
}

// If the post title is too long, truncate it and add "..." at the end
function wcdp_get_post_title($designID){
    $title = get_the_title($designID);
	$truncate_title = (mb_strlen($title) > 35) ? mb_substr($title, 0, 34) .'...' : $title;
	return $truncate_title;
}

add_action('add_meta_boxes', function(){
	add_meta_box('wcdp-designs-box-param', __('Select parameter', 'wcdp'), 'wcdp_designs_metabox_param_html', 'wcdp-designs', 'normal', 'high');
	add_meta_box('wcdp-designs-box-editor', __('Design', 'wcdp'), 'wcdp_designs_metabox_editor_html', 'wcdp-designs', 'normal', 'high');
	add_meta_box("wcdp-personalize-product", __('WCDP - Custom Product', 'wcdp' ), "wcdp_personalize_product_meta_box", "product", "side", "default");
});

// Add new tab wcdp attribute actions
function wcdp_attribute_actions_tab($tabs){
    $tabs['wcdp'] = array(
        'label'  =>  __( 'WCDP - Actions', 'wcdp' ),
        'target' =>  'wcdp_tab_product_data',
		'class'  => 'show_if_variable'
    );
    return $tabs;
}
add_filter( 'woocommerce_product_data_tabs', 'wcdp_attribute_actions_tab', 10, 1 );

// Add content wcdp attribute actions panel
function wcdp_attribute_actions_data(){
	global $post;
	$product_id = $post->ID;
	$ph_img = WCDP_URL .'/assets/images/placeholder.png'; ?>
    <script>
	    var wcdp_translations = <?php echo json_encode(wcdp_editor_translations()); ?>,
            wcdp_placeholder_img = <?php echo json_encode($ph_img); ?>;
	</script>	
    <div id="wcdp_tab_product_data" class="panel wc-metaboxes-wrapper hidden">
        <p class="wcdp-toolbar">
	        <strong><?php _e( 'WCDP - Set attribute actions', 'wcdp' ); ?></strong>
			<a href="#" class="wcdp-close-all"><?php _e( 'Close', 'wcdp' ); ?></a>
			<a>/</a>
			<a href="#" class="wcdp-expand-all"><?php _e( 'Expand', 'wcdp' ); ?></a>
    	</p>
		<div id="wcdp-contain-attributes">
		    <?php echo wcdp_draw_product_attribute_actions($product_id); ?>
		</div>
	    <p class="wcdp-toolbar tb__btm">
	        <button type="button" id="wcdp_save_actions" class="button button-primary"><?php _e( 'Save actions', 'wcdp' ); ?></button>
		    <button type="button" id="wcdp_empty_actions" class="button"><?php _e( 'Empty actions', 'wcdp' ); ?></button>
		    <button type="button" id="wcdp_refresh_attributes" class="button"><?php _e( 'Refresh attributes', 'wcdp' ); ?></button>
			<input type="hidden" id="wcdp_product_id" value="<?php echo $product_id; ?>">
			<a href="#" class="wcdp-close-all"><?php _e( 'Close', 'wcdp' ); ?></a>
			<a>/</a>
			<a href="#" class="wcdp-expand-all"><?php _e( 'Expand', 'wcdp' ); ?></a>
    	</p>
		<div class="wcdp-loader-actions"></div>
	</div>
	<?php
}
add_action('woocommerce_product_data_panels', 'wcdp_attribute_actions_data', 10, 1);

// Draw html product attribute actions
function wcdp_draw_product_attribute_actions($product_id){
    $product = wc_get_product($product_id);
    $attributes = $product->get_attributes();
	ob_start();
	if(!empty($attributes)){
        $params_actions = array(
	        array(
	            'param'  => 'bg_color',
                'label'  => __( 'Product color', 'wcdp' ),
		        'type'   => 'text',
		        'format' => 'hex'
	        ),
            array(
	            'param'  => 'pr_img_front',
                'label'  => __( 'Product image: Front', 'wcdp' ),
		        'type'   => 'hidden',
		        'format' => 'img'
	        ),
            array(
	            'param'  => 'pr_img_back',
                'label'  => __( 'Product image: Back', 'wcdp' ),
		        'type'   => 'hidden',
		        'format' => 'img'
	        ),
            array(
	            'param'  => 'pr_sides',
                'label'  => __( 'Show product sides', 'wcdp' ),
		        'type'   => 'hidden',
		        'format' => 'select'
	        ),
	        array(
	            'param'  => 'canvas_w',
                'label'  => __( 'Canvas width', 'wcdp' ),
		        'type'   => 'number',
		        'format' => 'px'
	        ),
	        array(
	            'param'  => 'canvas_h',
                'label'  => __( 'Canvas height', 'wcdp' ),
		        'type'   => 'number',
		        'format' => 'px'
	        ),
	        array(
	            'param'  => 'output_w',
                'label'  => __( 'Canvas output width', 'wcdp' ),
		        'type'   => 'number',
		        'format' => 'px'
	        ),
	        array(
	            'param'  => 'pdf_w',
                'label'  => __( 'PDF output width', 'wcdp' ),
		        'type'   => 'number',
		        'format' => 'mm'
	        ),
	        array(
	            'param'  => 'pdf_h',
                'label'  => __( 'PDF output height', 'wcdp' ),
		        'type'   => 'number',
		        'format' => 'mm'
	        ),
	        array(
	            'param'  => 'margin_bleed_lr',
                'label'  => __( 'Margin bleed area horizontally', 'wcdp' ),
		        'type'   => 'number',
		        'format' => 'px'
	        ),
	        array(
	            'param'  => 'margin_bleed_tb',
                'label'  => __( 'Margin bleed area vertically', 'wcdp' ),
		        'type'   => 'number',
		        'format' => 'px'
	        ),
	        array(
	            'param'  => 'bleed_top',
                'label'  => __( 'Top bleed area', 'wcdp' ),
		        'type'   => 'number',
		        'format' => 'px'
	        ),
	        array(
	            'param'  => 'bleed_left',
                'label'  => __( 'Left bleed area', 'wcdp' ),
		        'type'   => 'number',
		        'format' => 'px'
	        ),
	        array(
	            'param'  => 'bleed_radius',
                'label'  => __( 'Radius bleed area', 'wcdp' ),
		        'type'   => 'number',
		        'format' => 'px'
	        ),
	        array(
	            'param'  => 'radius',
                'label'  => __( 'Editor corners rounded', 'wcdp' ),
		        'type'   => 'number',
		        'format' => 'px'
	        )
        );
        $options_layout = array(
            'drop_down'      => __( 'Drop down', 'wcdp' ),
	        'product_colors' => __( 'Product colors', 'wcdp' ),
	        'radio_checkbox' => __( 'Radio checkbox', 'wcdp' )
        );
		$options_set_img = array(
            'bg_img' => __( 'Background image', 'wcdp' ),
	        'ov_img' => __( 'Overlay image', 'wcdp' )
        );
		$options_set_sides = array(
		    'none'   => __( 'Select option', 'wcdp' ),
		    'sideFB' => __( 'Side front and back', 'wcdp' ),
            'sideF'  => __( 'Only side front', 'wcdp' )
        );
		$attribute_actions = get_post_meta($product_id, '_wcdp_product_attribute_actions', true);
        foreach($attributes as $attribute){
	        $name = $attribute['name'];
		    $slug = sanitize_title($attribute['name']);
		    $values_attr = $attribute['options'];
		    $action_slug = isset($attribute_actions[$slug]) ? $attribute_actions[$slug] : '';
		    $action_layout = isset($action_slug['layout']) ? $action_slug['layout'] : '';
			$action_set_img = isset($action_slug['set_img']) ? $action_slug['set_img'] : '';
			$tax_exists = false;
			if(taxonomy_exists($name)){
				$terms = wc_get_product_terms($product_id, $name, array('fields' => 'all'));
				$values_attr = array();
				foreach($terms as $term){
					$values_attr[] = array(
					    'name' => $term->name,
						'slug' => $term->slug
					);
				}
				$tax_exists = true;
			}				
			?>
	        <div class="wcdp-attr-item closed">
	            <h3>
		            <div class="wcdp-item-down" title="<?php _e( 'Expand / Close', 'wcdp' ); ?>"></div>
		            <strong><?php echo wc_attribute_label($name); ?></strong>
				    <input type="hidden" class="wcdp-attr-name" name="wcdp_attribute_actions[<?php echo $slug; ?>][name]" data-slug="<?php echo $slug; ?>" value="<?php echo $name; ?>">
	            </h3>
	            <div class="wcdp-item-contain">
				    <div class="dp-col">
				        <label><?php _e( 'Layout type', 'wcdp' ); ?>:</label>
				        <select class="wcdp-layout-type" name="wcdp_attribute_actions[<?php echo $slug; ?>][layout]">
					        <?php
							foreach($options_layout as $layout_key => $layout_value){ ?>
					            <option value="<?php echo $layout_key; ?>" <?php echo ($action_layout == $layout_key ? 'selected':''); ?>><?php echo $layout_value; ?></option>
						        <?php
							} ?>
		                </select>
				    </div>
				    <div class="dp-col">
				        <label><?php _e( 'Set product image as', 'wcdp' ); ?>:</label>
				        <select class="wcdp-set-img-pr" name="wcdp_attribute_actions[<?php echo $slug; ?>][set_img]">
					        <?php
							foreach($options_set_img as $set_img_key => $set_img_value){ ?>
					            <option value="<?php echo $set_img_key; ?>" <?php echo ($action_set_img == $set_img_key ? 'selected':''); ?>><?php echo $set_img_value; ?></option>
						        <?php
							} ?>
		                </select>
				    </div>
				    <div class="dp-col last">
			            <label><?php _e( 'Attribute value', 'wcdp' ); ?>:</label>
	                    <select class="wcdp-attr-value">
			                <?php
							foreach($values_attr as $value_attr){ ?>
			                    <option value="<?php echo ($tax_exists ? $value_attr['slug'] : sanitize_title($value_attr)); ?>"><?php echo ($tax_exists ? $value_attr['name'] : $value_attr); ?></option>
			                    <?php
							} ?>					
		                </select>
				    </div>
				    <div class="wcdp-attr-content">
                        <?php
					    foreach($values_attr as $value_attr){
                            $attr_slug    = $tax_exists ? $value_attr['slug'] : sanitize_title($value_attr);
							$attr_name    = 'wcdp_attribute_actions['. $slug .'][actions]['. $attr_slug .']';
                            $get_actions  = isset($attribute_actions[$slug]) ? $attribute_actions[$slug] : '';
                            $attr_actions = isset($get_actions['actions']) ? $get_actions['actions'] : '';
                            $attr_action  = isset($attr_actions[$attr_slug]) ? $attr_actions[$attr_slug] : '';
                            ?>
                            <table data-attr="<?php echo $attr_slug; ?>">
                                <tr>
                                    <th><?php _e( 'Enable', 'wcdp' ); ?></th>
                                    <th><?php _e( 'Action', 'wcdp' ); ?></th>
                                    <th><?php _e( 'Value', 'wcdp' ); ?></th>
	                                <th><?php _e( 'Format', 'wcdp' ); ?></th>
                                </tr>
								<?php
								foreach($params_actions as $action){
									$param  = $action['param'];
									$label  = $action['label'];
									$type   = $action['type'];
									$format = $action['format'];
                                    $name   = $attr_name .'['. $param .']';
									$check  = isset($attr_action[$param]['active']) ? $attr_action[$param]['active'] : '';
									$value  = isset($attr_action[$param]['value']) ? $attr_action[$param]['value'] : '';
								    ?>
                                    <tr class="dp-row" data-action="<?php echo $param; ?>">
                                        <td class="dp-col1"><input type="checkbox" name="<?php echo $name; ?>[active]" <?php checked('on', $check); ?>/></td>
                                        <td class="dp-col2"><?php echo $label; ?></td>
                                        <td class="dp-col3">
										    <?php if($format == 'img'){ ?>
											    <img src="<?php echo ($value ? $value : WCDP_URL .'/assets/images/placeholder.png'); ?>"/>
											    <a href="#" class="wcdp-remove-img-action<?php echo (!$value ? ' dp-disabled':''); ?>"><?php _e( 'Remove image', 'wcdp' ); ?></a>
										    <?php } else if($format == 'select'){
												      if($param == 'pr_sides'){ ?>
						                                <select class="wcdp-set-sides-pr">
					                                       <?php
							                               foreach($options_set_sides as $set_side_key => $set_side_value){ ?>
					                                          <option value="<?php echo $set_side_key; ?>" <?php echo ($value == $set_side_key ? 'selected':''); ?>><?php echo $set_side_value; ?></option>
						                                      <?php
							                               } ?>
		                                                </select>
														<?php	
													  }
											       }
												?>
										    <input type="<?php echo $type; ?>"<?php echo ($format == 'hex' ? ' class="dp-bg-color" title="'. __( 'Empty transparent', 'wcdp' ) .'"' : ''); ?> name="<?php echo $name; ?>[value]" value="<?php echo $value ?>">
										</td>
                                        <td class="dp-col4">
										    <?php if($format == 'hex'){ ?>
										        <input type="color" title="<?php _e( 'Color picker', 'wcdp' ); ?>" value="<?php echo ($value ? $value : '#000001'); ?>">
                                            <?php } else if($format == 'img'){ ?>
										        <div class="wcdp-set-img-attr" format="image" support="jpg,jpeg,png,gif">
												    <input type="button" class="button" value="<?php _e( 'Set image', 'wcdp' ); ?>">
												</div>
											<?php } else if($format == 'px'){
											        _e( 'px', 'wcdp' );
										          }
                                                  else if($format == 'mm'){
											        _e( 'mm', 'wcdp' );
										          }
										    ?>
										</td>
									</tr>
								    <?php
								} ?>
                            </table>
                            <?php
						} ?>
                    </div>
	            </div>
	        </div>
		    <?php 
		}
    } else{
		echo '<h2>'. __( 'No attributes found', 'wcdp' ) .'</h2>';
	}
	return ob_get_clean();
}

// Get json design id by ajax
function wcdp_get_json_design_ajax(){	
	$designID = $_REQUEST['designID'];
    $args = array(
	    'editor'      => 'frontend',
		'designID'    => $designID,
		'designURL'   => WCDP_PATH_UPLOADS .'/save-admin/designID'. $designID,		
		'productID'   => false,
		'pageID'      => false,
	);
    $params = wcdp_get_params_product_designer($args);
	echo json_encode($params);
	exit;
}
add_action('wp_ajax_nopriv_wcdp_get_json_design_ajax', 'wcdp_get_json_design_ajax');
add_action('wp_ajax_wcdp_get_json_design_ajax', 'wcdp_get_json_design_ajax');

// Remove design by admin
function wcdp_remove_canvas_design_admin($post_id){
	if(get_post_type($post_id) == 'wcdp-designs' && get_post_status($post_id) == 'trash'){
		$url_path = WCDP_PATH_UPLOADS .'/save-admin/designID'. $post_id;
		if(file_exists($url_path)){
            wcdp_delete_directory_content($url_path);
		}
	}
}
add_action('before_delete_post', 'wcdp_remove_canvas_design_admin', 10, 1);

// Remove design by ajax
function wcdp_remove_canvas_design_ajax(){
	if(is_user_logged_in()){	
	    $userID = get_current_user_id();
        $designID = $_REQUEST['designID'];		
		$get_designs_list = get_user_meta($userID, '_wcdp_designs_save_user_list', true);
		unset($get_designs_list[$designID]);
		if(update_user_meta($userID, '_wcdp_designs_save_user_list', $get_designs_list)){
			$url_path = WCDP_PATH_UPLOADS .'/save-user/'. $designID;
		    if(file_exists($url_path)){
                wcdp_delete_directory_content($url_path);
				wp_send_json('delete_successful');
		    }
		}		
	}	
	exit;
}
add_action('wp_ajax_nopriv_wcdp_remove_canvas_design_ajax','wcdp_remove_canvas_design_ajax');
add_action('wp_ajax_wcdp_remove_canvas_design_ajax', 'wcdp_remove_canvas_design_ajax');

// Rename design by ajax
function wcdp_rename_my_design_ajax(){
	$result = array('success' => false);
	if(is_user_logged_in()){
	    $userID = get_current_user_id();
        $designID = $_REQUEST['designID'];
		$designName = $_REQUEST['designName'];
		$get_designs_list = get_user_meta($userID, '_wcdp_designs_save_user_list', true);
		if(isset($get_designs_list[$designID])){
			$item = $get_designs_list[$designID];
			if(!is_array($item)){
				$get_designs_list[$designID] = array('productID' => $item, 'designID' => 0);
			}
			$get_designs_list[$designID]['designName'] = $designName;
		    if(update_user_meta($userID, '_wcdp_designs_save_user_list', $get_designs_list)){
                $result['success'] = true;
			}
		}		
	}
	echo json_encode($result);
	exit;
}
add_action('wp_ajax_nopriv_wcdp_rename_my_design_ajax','wcdp_rename_my_design_ajax');
add_action('wp_ajax_wcdp_rename_my_design_ajax', 'wcdp_rename_my_design_ajax');

// Search designs on the product page
function wcdp_search_post_ajax(){
	$return = array();
	$results = new WP_Query(array( 
		's'=> $_GET['q'],
		'post_status' => 'publish',
		'ignore_sticky_posts' => 1,
		'posts_per_page' => 50,
		'post_type' => 'wcdp-'. $_GET['type']
	));
	if($results->have_posts()) :
		while($results->have_posts()) : $results->the_post();
			$postID = $results->post->ID;
			$title = wcdp_get_post_title($postID);
			$return[] = array($postID, $title);
		endwhile;
	endif;
	echo json_encode($return);
	exit;
}
add_action('wp_ajax_wcdp_search_post_ajax', 'wcdp_search_post_ajax');

// Save & refresh attribute actions by ajax query
function wcdp_manage_attribute_actions_ajax(){
	$result = array('success' => false);
	$option = $_REQUEST['option'];
	$product_id = $_REQUEST['product_id'];
	if($product_id){
	    if($option == 'save'){
	        $attr_actions = $_REQUEST['attr_actions'];
	        if($attr_actions){
                update_post_meta($product_id, '_wcdp_product_attribute_actions', $attr_actions);
		        $result['success'] = true;
	        }
	    }
	    else if($option == 'refresh'){
            $html = wcdp_draw_product_attribute_actions($product_id);
		    if($html){
				$result['success'] = true;
				$result['actions'] = $html;
		    }
	    }
	}
	echo json_encode($result);
	exit;
}
add_action('wp_ajax_wcdp_manage_attribute_actions_ajax', 'wcdp_manage_attribute_actions_ajax');

function wcdp_designs_save_post($post_id){
    if(get_post_type($post_id) == 'wcdp-designs'){
		if(isset($_POST['dp_select_parameter_design_dropdown'])){
            update_post_meta($post_id, '_wcdp_parameter_design', $_POST['dp_select_parameter_design_dropdown']);
		}
    } else if(get_post_type($post_id) == 'product' && isset($_POST['action']) && $_POST['action'] != 'inline-save'){
		$product = wc_get_product($post_id);
        if(isset($_POST['personalize-product-checkbox']) && ($product->is_type('variable') || $product->is_type('simple'))){
            update_post_meta($post_id,  '_wcdp_personalize_product', 'on' );
        } else{
            update_post_meta($post_id,  '_wcdp_personalize_product', 'off' );
        }
        if(isset($_POST['wcdp_select_design_id'])){
            update_post_meta($post_id, '_wcdp_product_design_id', $_POST['wcdp_select_design_id'] );
        } else{
		    delete_post_meta($post_id, '_wcdp_product_design_id' );
	    }
        if(isset($_POST['wcdp_select_multiple_designs'])){
            update_post_meta($post_id, '_wcdp_product_multiple_designs', $_POST['wcdp_select_multiple_designs'] );
        } else{
		    delete_post_meta($post_id, '_wcdp_product_multiple_designs' );
	    }
        if(isset($_POST['wcdp_input_design_cat'])){
            update_post_meta($post_id, '_wcdp_product_multiple_designs_cat', $_POST['wcdp_input_design_cat'] );
        } else{
			delete_post_meta($post_id, '_wcdp_product_multiple_designs_cat' );
        }
        if(isset($_POST['load-designs-objs-checkbox'])){
            update_post_meta($post_id,  '_wcdp_load_designs_by_objs', 'on' );
        } else{
            update_post_meta($post_id,  '_wcdp_load_designs_by_objs', 'off' );
        }
        if(isset($_POST['load-designs-ajax-checkbox'])){
            update_post_meta($post_id,  '_wcdp_load_designs_by_ajax', 'on' );
        } else{
            update_post_meta($post_id,  '_wcdp_load_designs_by_ajax', 'off' );
        }
        if(isset($_POST['wcdp_attribute_actions'])){
            update_post_meta($post_id, '_wcdp_product_attribute_actions', $_POST['wcdp_attribute_actions'] );
        } else{
		    delete_post_meta($post_id, '_wcdp_product_attribute_actions' );
	    }
    }
}
add_action('save_post', 'wcdp_designs_save_post');