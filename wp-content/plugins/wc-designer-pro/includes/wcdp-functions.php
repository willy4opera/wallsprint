<?php

// Get settings field defaults
function wcdp_get_defaults_settings_field(){
	
   $picker_table = file_get_contents(WCDP_PATH .'assets/js/picker-table.json');
   $args = array(
        'general' => array(
            'CMYK'                   => '',
			'cmyk_convert'           => '',
			'cmyk_picker'            => '',
			'chunk_colors'           => '32',
			'imagick_cmd'            => '',
			'buttom_myaccount'       => 'on',
            'rtl_mode'               => '',
			'view_finder'            => '',
			'hide_addtocart'         => 'on',
			'number_save_user'       => '',
			'profile_cmyk'           => '',
			'profile_rgb'            => '',
			'zip_name'               => '',
			'output_svg'             => 'on',
			'add_svg_user'           => 'on',
			'output_pdf'             => 'on',
			'add_pdf_user'           => 'on',
			'output_pdf_svg'         => '',
			'output_png'             => '',
			'add_png_user'           => '',
			'output_cmyk'            => '',
			'output_cmyk_user'       => '',
			'output_json'            => '',
			'upload_svg'             => '',
			'maps_api'               => '',
			'pixabay_api'            => '',
			'unsplash_api'           => '',
			'pexels_api'             => '',
			'flaticon_api'           => '',
			'flaticon_svg'           => '',
			'sources_cmyk'           => '',
            'preload_fonts'          => 'none',
			'loader_gif'             => '',
			'loader_w'               => '64',
			'loader_h'               => '64',
            'mail_order_complete'    => 'on',
			'mail_status_complete'   => 'on',
			'download_design_cart'   => 'on',
			'download_design_order'  => 'on',
            'download_design_editor' => 'on',
			'download_design_logged' => '',
			'confirm_box'            => 'on',
			'text_confirm_box'       => '<p><b>Please review your design carefully:</b></p><p>Check that the design does not contain spelling errors, etc.</p>',
			'label_confirm_box'      => 'I have reviewed the design and I give my confirmation.',
	    ),
        'shortcutkeys' => array(
		    'moveup'               => '38',
			'moveup_ck'            => 'on',
		    'movedown'             => '40', 
			'movedown_ck'          => 'on',
		    'moveleft'             => '37',
			'moveleft_ck'          => 'on',
		    'moveright'            => '39',
			'moveright_ck'         => 'on',			  
			'select_all'           => '83',
			'select_all_ck'        => 'on',
			'erase_all'            => 'ctrl+46',
			'erase_all_ck'         => 'on',
			'grid'                 => '49',
			'grid_ck'              => 'on',
			'center_vertical'      => 'ctrl+86',
			'center_vertical_ck'   => 'on',
			'center_horizontal'    => 'ctrl+72',
			'center_horizontal_ck' => 'on',
			'flip_vertical'        => 'shift+86',
			'flip_vertical_ck'     => 'on',
			'flip_horizontal'      => 'shift+72',
			'flip_horizontal_ck'   => 'on',			  
		    'bring_front'          => '81',
			'bring_front_ck'       => 'on',
            'send_back'            => '65',
			'send_back_ck'         => 'on',
		    'lock'                 => '76',
			'lock_ck'              => 'on',
		    'duplicate'            => 'ctrl+67',
			'duplicate_ck'         => 'on',
			'clone_sides'          => 'ctrl+83',
			'clone_sides_ck'       => 'on',
		    'magic_more'           => '77',
			'magic_more_ck'        => 'on',
		    'magic_less'           => '78',
			'magic_less_ck'        => 'on',
		    'align_left'           => '79',
			'align_left_ck'        => 'on',
		    'align_right'          => '80',
			'align_right_ck'       => 'on',
            'rotate'               => '82',
			'rotate_ck'            => 'on',
		    'return_state'         => '86',
			'return_state_ck'      => 'on',
		    'align_vertical'       => '88',
			'align_vertical_ck'    => 'on',
		    'align_horizontal'     => '90',
			'align_horizontal_ck'  => 'on',
		    'group'                => '71',
			'group_ck'             => 'on',
		    'delete'               => '46',
			'delete_ck'            => 'on',			  
		    'undo'                 => 'shift+37',
			'undo_ck'              => 'on',
		    'redo'                 => 'shift+39',
			'redo_ck'              => 'on',		  
		    'line_text_small'      => 'shift+38',
			'line_text_small_ck'   => 'on',
		    'line_text_large'      => 'shift+40',
			'line_text_large_ck'   => 'on'
	    ),
        'style' => array(
	        'color_icons'                     => ['RGB' => '#36495e', 'CMYK' => '76,53,33,41'],
			'color_icons_hover'               => ['RGB' => '#ffffff', 'CMYK' => '0,0,0,0'],
			'bg_icons'                        => ['RGB' => '#ffffff', 'CMYK' => '0,0,0,0'],
			'bg_icons_hover'                  => ['RGB' => '#36495e', 'CMYK' => '76,53,33,41'],
			'text_color'                      => ['RGB' => '#36495e', 'CMYK' => '76,53,33,41'],
			'tabs_bg'                         => ['RGB' => '#fbfbfb', 'CMYK' => '0,0,0,2'],
			'tabs_content'                    => ['RGB' => '#ffffff', 'CMYK' => '0,0,0,0'],
			'buttons_color'                   => ['RGB' => '#36495e', 'CMYK' => '76,53,33,41'],		
			'buttons_color_hover'             => ['RGB' => '#ffffff', 'CMYK' => '0,0,0,0'],	
            'buttons_bg'                      => ['RGB' => '#ffffff', 'CMYK' => '0,0,0,0'],	
			'buttons_bg_hover'                => ['RGB' => '#36495e', 'CMYK' => '76,53,33,41'],			
			'buttons_color_jbox'              => ['RGB' => '#ffffff', 'CMYK' => '0,0,0,0'],		
			'buttons_color_hover_jbox'        => ['RGB' => '#ffffff', 'CMYK' => '0,0,0,0'],	
            'buttons_bg_jbox'                 => ['RGB' => '#36495e', 'CMYK' => '76,53,33,41'],
			'buttons_bg_hover_jbox'           => ['RGB' => '#bcd800', 'CMYK' => '40,0,100,0'],
			'buttons_color_folders'           => ['RGB' => '#36495e', 'CMYK' => '76,53,33,41'],
			'buttons_color_folders_select'    => ['RGB' => '#36495e', 'CMYK' => '76,53,33,41'],	
			'buttons_color_folders_bg'        => ['RGB' => '#ffffff', 'CMYK' => '0,0,0,0'],	
			'buttons_color_folders_bg_select' => ['RGB' => '#ececec', 'CMYK' => '0,0,0,10'],
            'tooltip_color'                   => ['RGB' => '#ffffff', 'CMYK' => '0,0,0,0'],
			'tooltip_bg'                      => ['RGB' => '#36495e', 'CMYK' => '76,53,33,41'],
            'scrollbar_bg'                    => ['RGB' => '#ffffff', 'CMYK' => '0,0,0,0'],
			'border_color'                    => ['RGB' => '#b6babd', 'CMYK' => '30,20,20,5'],
			'border_bleed_color'              => ['RGB' => '#dd2b1c', 'CMYK' => '0,100,100,0'],
			'picker_color_bg'                 => ['RGB' => '#f2f2f2', 'CMYK' => '0,0,0,10'],	
			'picker_color_border'             => ['RGB' => '#b6babd', 'CMYK' => '30,20,20,5'],
			'picker_color_text'               => ['RGB' => '#292929', 'CMYK' => '0,0,0,90'],			
			'corner_color'                    => ['RGB' => '#36495e', 'CMYK' => '76,53,33,41'],
			'corner_border_color'             => ['RGB' => '#ffffff', 'CMYK' => '0,0,0,0'],
			'corner_icons_color'              => ['RGB' => '#ffffff', 'CMYK' => '0,0,0,0'],
			'text_color_editor'               => ['RGB' => '#000000', 'CMYK' => '0,0,0,100'],
			'text_color_editor_outline'       => ['RGB' => '#000000', 'CMYK' => '0,0,0,100'],
			'color_shapes_editor'             => ['RGB' => '#afb0b0', 'CMYK' => '0,0,0,40'],
			'color_shapes_editor_outline'     => ['RGB' => '#000000', 'CMYK' => '0,0,0,100'],
			'color_qr_editor'                 => ['RGB' => '#000000', 'CMYK' => '0,0,0,100'], 
			'bg_color_qr_editor'              => ['RGB' => '#ffffff', 'CMYK' => '0,0,0,0'],
			'map_color_ico'                   => ['RGB' => '#dd2b1c', 'CMYK' => '0,100,100,0'],
			'snap_color_border'               => ['RGB' => '#e10886', 'CMYK' => '0,100,0,0'],
			'tooltip_offset_x'                => '0',
			'tooltip_offset_y'                => '-5',
			'auto_bleed_color'                => 'on',
			'auto_snap'                       => 'on',
			'snap_tolerance'                  => '5',
			'centered_scaling'                => 'on',
			'obj_center'                      => 'on',
			'corners_outside'                 => 'on',
			'hide_middle_corners'             => '',
			'corner_size'                     => '20',
			'corner_style'                    => 'rect',
            'border_corners'                  => 'on',
			'popup_show'                      => 'on',
			'hide_notes'                      => '',
			'show_picker_palette'             => 'on',
            'column_picker_palette'           => '3',       
			'picker_palette' => array(
			    'RGB' => array(
			        '#000000','#686867','#c4c5c5','#193b8d','#8873c8','#00a3d4','#afdcf1','#00a759','#bcd800',
					'#fdc900','#fbe116','#dd2b1c','#d1768b','#e10886','#f3c7db','#f27e00','#feae47','#ffffff'
				  ),
				'CMYK' => array(
			        '0,0,0,100','0,0,0,70','0,0,0,30','100,80,0,20','60,60,0,0','100,0,0,0','40,0,0,0','100,0,100,0','40,0,100,0',
					'0,20,100,0','0,0,100,0','0,100,100,0','0,60,20,20','0,100,0,0','5,30,5,0','0,60,100,0','0,40,80,0','0,0,0,0'
				  )
			  ),
            'background_palette' => array(
			    'RGB' => array(
                    '#000000','#d4d2d3','#93878e','#b40e97','#6c2c79','#776891','#146194','#614942','#d7caa2','#892a2e','#c11826','#ffbbbe',
					'#ffb475','#e6ad2a','#edd925','#b8f1bd','#e1e955','#a49d45','#68ab7a','#51aca4','#82d8df','#0b6270','#36495e','#ffffff'
				  ),
				'CMYK' => array(
                    '0,0,0,100','20,15,15,0','40,40,30,15','30,100,0,10','60,90,10,30','60,60,20,10','90,50,10,20','50,60,60,40',
					'15,15,40,5','20,90,70,40','0,100,80,20','0,40,20,0','0,40,60,0','0,30,90,10','0,0,90,10','40,0,40,0','25,0,80,0',
					'30,20,80,20','60,0,60,20','70,10,40,10','60,0,20,0','85,25,30,45','76,53,33,41','0,0,0,0'
				  )
              )
        ),
        'license' => array(
		    'envato_username'      => '',
			'envato_api_key'       => '',
			'envato_purchase_code' => ''
		),
		'table'   => json_decode($picker_table, true)
   );
   return $args;
}

// Restore options by defaults
function wcdp_restore_defaults_all_settings(){
    $fields = wcdp_get_defaults_settings_field();
	foreach($fields as $fieldkey => $fieldVal){
		update_option('wcdp-settings-'. $fieldkey, $fieldVal);
    }
    echo 'restore_successful';
    exit;
}
add_action( 'wp_ajax_wcdp_restore_defaults_all_settings', 'wcdp_restore_defaults_all_settings' );

// Demos Installer
function wcdp_install_product_demos(){
	$response = array('success' => 0);
	require_once(ABSPATH .'/wp-admin/includes/plugin.php');
	if(is_plugin_active('woocommerce/woocommerce.php')){
	    $demoURL = WCDP_PATH .'product-demos/'. $_POST['demo'] .'.json';
        $json = file_get_contents($demoURL); 
        $data = json_decode($json, true);
        $paramID = wcdp_insert_post_type('wcdp-parameters', $data['title'], 'closed', 0);
        if($paramID){
            $metaKeys = array_keys(wcdp_meta_keys_params());
		    $data['param'][0] = $_POST['pageID'];
            foreach($metaKeys as $key => $meta_key){
		        update_post_meta($paramID, $meta_key, $data['param'][$key]);
            }
		    $designID = wcdp_insert_post_type('wcdp-designs', $data['title'], 'closed', 0);
		    if($designID){
			    update_post_meta($designID, '_wcdp_parameter_design', $paramID);
			    $designFiles = array(
			        'cover.jpg' => base64_decode($data['cover']),
			        'front.json' => json_encode($data['design_front'])
			    );
			    if(isset($data['design_back'])){
				    $designFiles['back.json'] = json_encode($data['design_back']);
			    }
			    $url_path = WCDP_PATH_UPLOADS .'/save-admin/designID'. $designID;
                if(!file_exists($url_path)){
	                mkdir($url_path, 0755, true);
                }
			    foreach($designFiles as $filename => $dataFile){
			        file_put_contents($url_path .'/'. $filename, $dataFile);
			    }
                $productID = wcdp_insert_post_type('product', $data['title'], 'open', 0);
			    if($productID){
			        $imageID = wcdp_add_file_to_media_library(base64_decode($data['product_image']), sanitize_title($data['title']) .'.png', $productID);
				    if($imageID){
					    set_post_thumbnail($productID, $imageID);
				    }
				    update_post_meta($productID, '_wcdp_personalize_product', 'on');
				    update_post_meta($productID, '_wcdp_product_design_id', $designID);
				    wp_set_object_terms($productID, 'variable', 'product_type');

				    $attributes = array();
				    $defaultAttr = array();
				    foreach($data['attributes'] as $attribute){
					    $attrSlug = sanitize_title($attribute['name']);
					    $attributes[$attrSlug] = array(
                            'name' => $attribute['name'],
                            'value' => $attribute['values'],
                            'is_visible' => '1', 
                            'is_variation' => '1',
                            'is_taxonomy' => '0'
				        );
					    $defaultAttr[$attrSlug] = $attribute['default'];
				    }
                    update_post_meta($productID, '_product_attributes', $attributes);
				    update_post_meta($productID, '_wcdp_product_attribute_actions', $data['attr_actions']);
				    update_post_meta($productID, '_default_attributes', $defaultAttr);

				    foreach($data['variations'] as $variation){
					    $variationID = wcdp_insert_post_type('product_variation', $data['title'], 'closed', $productID);
					    if($variationID){
                            $newVariation = new WC_Product_Variation($variationID);
	                        $newVariation->set_price($variation['price']);
	                        $newVariation->set_regular_price($variation['price']);
	                        $newVariation->set_manage_stock(false);
							foreach($variation['data'] as $value){
                                update_post_meta($variationID, 'attribute_'. $value['slug'], $value['select']);
							}
                            $newVariation->save();
					    } else{
						    $err = __( 'Error add product variations.', 'wcdp' );
						    break;
					    }
				    }
				    if(!isset($err))
				        $response['success'] = __( 'Installation successful.', 'wcdp' );
			    }
		    } else{
			    $err = __( 'Error add design.', 'wcdp' );
		    }
        } else{
		    $err = __( 'Error add parameter.', 'wcdp' );
	    }
	    $response['err'] = isset($err) ? __( 'Failed to complete the demo install', 'wcdp' ) .': '. $data['title'] .'. '. $err : 0;
	} else{
		$response['err'] = __( 'The installer cannot continue because WooCommerce is not installed or activated.', 'wcdp' );
	}	
    echo json_encode($response);
	exit;
}
add_action('wp_ajax_wcdp_install_product_demos', 'wcdp_install_product_demos');

// Gets updater instance
function wcdp_updater(){
    do_action('wcdp_before_init_updater');
	require_once(WCDP_PATH. 'includes/updaters/wcdp-updater.php');
    $updater = new WCDP_Updater();
    $updater->init();
	require_once(WCDP_PATH. 'includes/updaters/wcdp-updating-manager.php');
    $updater->setUpdateManager(new WCDP_Updating_Manager(WCDP_VERSION, $updater->versionUrl(), WCDP_MAIN_FILE));
    do_action('wcdp_after_init_updater');
    return $updater;
}
add_action('init', 'wcdp_updater');

// Update colors picker table
function wcdp_update_picker_table(){
	$mode = $_POST['mode'];
	$colors = explode(',',$_POST['colors']);
	if($mode == 'update'){
		if(count($colors) == 4096 && count(array_unique($colors)) !== 1){
		    update_option('wcdp-settings-table', $colors);
		    echo 'update_successful';
		} else{
			echo 'error/profile';
		}
    } else if($mode == 'convert'){		
        $convert = wcdp_convert_colors_to_cmyk($colors, true);
		$result = $convert['HEX'];
		if($result)
            echo json_encode($result);
	    else
			echo 'error/chunk';
	}
	exit;
}
add_action('wp_ajax_wcdp_update_picker_table', 'wcdp_update_picker_table');

// Add news extension
function wcdp_add_custom_upload_mimes($existing_mimes){
    $existing_mimes['icc']   = 'application/vnd.iccprofile';
	$existing_mimes['svg']   = 'image/svg+xml';
  	$existing_mimes['ttf']   = 'application/x-font-ttf';
  	$existing_mimes['eot']   = 'application/vnd.ms-fontobject';
  	$existing_mimes['woff']  = 'application/x-font-woff';
  	$existing_mimes['woff2'] = 'application/x-font-woff2';
    return $existing_mimes;
}
add_filter('upload_mimes', 'wcdp_add_custom_upload_mimes');

// Add files to media library
function wcdp_add_file_to_media_library($file, $name, $post_id){
	$result = 0;
    $upload_file = wp_upload_bits($name, null, $file);
    if(!$upload_file['error']){
	    $wp_filetype = wp_check_filetype($name, null);
	    $attachment = array(
		    'post_mime_type' => $wp_filetype['type'],
		    'post_title' => sanitize_file_name($name),
		    'post_content' => '',
		    'post_status' => 'inherit'
	    );
	    $attachment_id = wp_insert_attachment($attachment, $upload_file['file'], $post_id);
	    if(!is_wp_error($attachment_id)){
		    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		    $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_file['file']);
		    wp_update_attachment_metadata($attachment_id,  $attachment_data);
			$result = $attachment_id;
	    }
    }
	return $result;
}

// Add buttom my designs to account page 
function wcdp_add_btn_my_designs(){	
    if(is_user_logged_in()){
        $general = get_option('wcdp-settings-general');
		if(isset($general['buttom_myaccount']) && isset($general['page_save_user_default']) && get_user_meta(get_current_user_id(), '_wcdp_designs_save_user_list', true)){
            echo '<div style="width:100%;margin:2em 0;"><a class="button" href="'. get_page_link($general['page_save_user_default']) .'">'. __( 'My designs', 'wcdp' ) .'</a></div>';
		}
	}
}
add_action( 'woocommerce_before_my_account', 'wcdp_add_btn_my_designs', 10, 1 ); 

// Init pages editor user 
function wcdp_add_content_editor_page_parameter($content){
	$general = get_option('wcdp-settings-general');
    $getModeDesign = wcdp_check_mode_design_page();
	if($getModeDesign){
        $productID = $getModeDesign['id'];
	    $templateID = $getModeDesign['template_id'];
	}
    if($getModeDesign && !empty($productID)){
        $dp_publish_page = wcdp_check_publish_design_page($productID, $templateID);
        if(!empty($dp_publish_page) && get_the_ID() == $dp_publish_page){	   
	        if(!isset($general['shortcode_editor']) && !has_shortcode($content, 'wcdp_editor')){
				$content .= do_shortcode('[wcdp_editor]');
			}	        
        } else{
		    $content  = '<div style="text-align:center;margin:5em"><img src="'. WCDP_URL . '/assets/images/forbidden.png"/>'; 
	        $content .= '<p>' . __( 'Design temporarily unavailable.', 'wcdp' ) . '</p></div>';
	    }
    } else if(!isset($general['shortcode_save_user'])){
		if(isset($general['page_save_user_default']) && get_the_ID() == $general['page_save_user_default']){
			if(!has_shortcode($content, 'wcdp_my_designs')){
		        $content .= do_shortcode('[wcdp_my_designs]');
			}
		} else{
			remove_shortcode('wcdp_my_designs');
		}
	}
	return $content;
}
add_filter( 'the_content', 'wcdp_add_content_editor_page_parameter');

// Check mode editor 
function wcdp_check_mode_design_page(){
    if(isset($_GET['dp_mode'])){
        $mode = $_GET['dp_mode'];
		$designID = isset($_GET['design_id']) ? $_GET['design_id'] : 0;
        if($mode === 'designer' && isset($_GET['product_id'])){
	        return array(
			    'id' => $_GET['product_id'],
				'template_id' => $designID,
				'attr_data' => false,
				'mode' => 'designer_editor'
			); 
        }
        else if($mode === 'save' && $designID){
		    if(is_user_logged_in()){  
		        $user_ID = get_current_user_id();
		        $get_designs_list = get_user_meta($user_ID, '_wcdp_designs_save_user_list', true);
			    if($get_designs_list && array_key_exists($designID, $get_designs_list)){
			        $productID = $get_designs_list[$designID];
					$templateID = 0;
					$attributes = 0;
					if(is_array($productID)){
						$templateID = $productID['designID'];
						$attributes = isset($productID['attrData']) ? $productID['attrData'] : false;
						$productID  = $productID['productID'];
					}
				    return array(
					    'id' => $productID,
						'design_id' => $designID,
						'template_id' => $templateID,
						'attr_data' => $attributes,
						'mode' => 'designer_save_editor'
					); 
			    }	     
		    }
        }
    }
}

// Add button personalize product
function wcdp_add_product_link_customize($link, $product){	
	$productCustom = wcdp_get_button_personalize($product->get_id());
	if($productCustom){
	    $link = $productCustom;
	}	
    return $link;
}
function wcdp_add_product_single_link_customize(){
	$general = get_option('wcdp-settings-general');
	$productID = get_the_ID();
	$productCustom = wcdp_get_button_personalize($productID);
	if($productCustom){
		if(isset($general['hide_addtocart']) && $general['hide_addtocart'] === 'on'){
			?>
		    <style type="text/css">
		        form.cart .quantity, form.cart .single_add_to_cart_button{ display:none !important }
		    </style>
		    <?php	
		}
		$product = wc_get_product($productID);
        if($product->is_type('variable') && $product->get_available_variations()){
			$html_values = '<div id="wcdp-contain-attributes" style="display:none !important">';
			foreach($product->get_variation_attributes() as $attribute_name => $options){
				$attr_slug = sanitize_title($attribute_name);
				if(!empty($options)){
				    $html_values .= '<div class="wcdp-values-attribute_'. $attr_slug .'">';
				    foreach($options as $option){
					    $value_name = esc_attr($option);
					    $value_slug = sanitize_title($option);
					    $html_values .= '<input type="hidden" name="'. $value_name .'" value="'. $value_slug .'"/>';
				    }
					$html_values .= '</div>';
				}
			}
			echo $html_values .'</div>';
			?>
            <script type="text/javascript">
                jQuery(document).ready(function(){
    		        var el  = jQuery('.product_type_customizable'),
			            url = el.attr('href');
                    jQuery('.single_variation_wrap').on('show_variation', function(event, variation){
                        var optionsAttr = '';
				        Object.keys(variation.attributes).forEach(function(key){
						    var attr_value = jQuery('select[name='+ key +'] option:selected').val(),
						        attr_slug = jQuery('#wcdp-contain-attributes .wcdp-values-'+ key +' input[name="'+ attr_value +'"]').val();
							if(typeof attr_slug !== 'undefined'){
							    optionsAttr += '&'+ key +'='+ attr_slug;
							}
				        });
                        el.attr('href', url + optionsAttr);
                    });
		        });
		    </script>
            <?php
		}		
	    echo $productCustom;
	}
}
function wcdp_get_button_personalize($productID){
    $dp_publish_page = wcdp_check_publish_design_page($productID, false);
    if(!empty($dp_publish_page)){
		$page_link = add_query_arg(array('dp_mode' => 'designer', 'product_id' => $productID), get_page_link($dp_publish_page));
        $link = do_shortcode('<a href="'. $page_link .'" rel="nofollow" data-product_id="'. $productID .'" class="button product_type_customizable">'. __( "Personalize", "wcdp" ) .'</a>');
        return $link;
	}
}
add_filter( 'woocommerce_loop_add_to_cart_link', 'wcdp_add_product_link_customize' , 10, 2 ); 
add_action( 'woocommerce_after_add_to_cart_button', 'wcdp_add_product_single_link_customize' );

// Check design publish & select page
function wcdp_check_publish_design_page($product_id, $template_id){
    if(get_post_meta($product_id, '_wcdp_personalize_product', true ) === 'on'){
		$designID = $template_id ? $template_id : get_post_meta($product_id, '_wcdp_product_design_id', true );
	    if($designID){
	        $paramID = get_post_meta($designID, '_wcdp_parameter_design', true );
			if(get_post_status($paramID) === 'publish'){
                $pageID = get_post_meta($paramID, '_wcdp_page_design', true );	   
			}
	        if(isset($pageID) && !empty($pageID)){
                return $pageID;
	        } else{		  	
                $options = get_option('wcdp-settings-general');
	            if(isset($options['page_editor_default'])){
   	                return $options['page_editor_default'];
		        }
	        }	
		}		
    }	   
}

// Add global variables javascript settings
function wcdp_add_global_variables_javascript_settings($wcdp_settings){
	$picker_table = false;
	$wcdp_settings['fonts'][] = array(
	    'id' => 'df',
		'name' => 'titillium',
		'url' => WCDP_URL .'/assets/fonts/titillium-regular.ttf'
	);
	$wcdp_settings['general']['text_confirm_box'] = htmlentities($wcdp_settings['general']['text_confirm_box']);
	if(isset($wcdp_settings['general']['CMYK']) && $wcdp_settings['general']['CMYK'] == 'on') 
		$picker_table = get_option('wcdp-settings-table');
	?>
    <script>
        var wcdp_spectrum_functions = {},
	        wcdp_settings = <?php echo json_encode($wcdp_settings['general']); ?>,            
            wcdp_style = <?php echo json_encode($wcdp_settings['style']); ?>,
		    wcdp_parameters = <?php echo json_encode($wcdp_settings['parameters']); ?>,
			wcdp_data_fonts = <?php echo json_encode($wcdp_settings['fonts']); ?>,
			wcdp_picker_table = <?php echo json_encode($picker_table); ?>,
			wcdp_shortcutkeys = <?php echo json_encode(get_option('wcdp-settings-shortcutkeys')); ?>,	
		    wcdp_translations = <?php echo json_encode(wcdp_editor_translations()); ?>,
			WCDP_URL_UPLOADS = <?php echo json_encode(WCDP_URL_UPLOADS); ?>,			
		    WCDP_URL = <?php echo json_encode(WCDP_URL); ?>,
		    AJAX_URL = <?php echo json_encode(admin_url('admin-ajax.php')); ?>,
			SITE_URL = <?php echo json_encode(get_site_url()); ?>;
    </script>
	<?php	
}

// Insert post type
function wcdp_insert_post_type($type, $title, $comment, $post_parent){
    $post_id = wp_insert_post(array(
        'post_type'      => $type,
        'post_title'     => $title,
        'post_status'    => 'publish',
        'comment_status' => $comment,
		'post_parent'    => $post_parent,
        'ping_status'    => 'closed'
    ));
	return $post_id;
}

// Notice check settings pages selected
function wcdp_check_settings_pages_selected(){
	global $pagenow;
	$user_id = get_current_user_id();
	$options = get_option('wcdp-settings-general');
	if(!get_user_meta($user_id, 'wcdp_dismissed_pages_notice') && !isset($options['page_editor_default']) && ('plugins.php' == $pagenow)){
		$msg = '<p>'. __( 'Select a defaults pages for the editor, it is required for this plugin to work properly!', 'wcdp' ) .'<a style="margin-left:10px" href="'. admin_url('plugins.php?wcdp-pages-dismissed') .'">'. __( 'Dismiss', 'wcdp' ) .'</a></p>';
        wcdp_note_error_html('notice notice-warning is-dismissible', $msg);
	}
}
add_action('admin_notices', 'wcdp_check_settings_pages_selected');

// Discard notice
function wcdp_dismissed_pages_notice() {
    $user_id = get_current_user_id();
    if(isset($_GET['wcdp-pages-dismissed']))
        add_user_meta($user_id, 'wcdp_dismissed_pages_notice', 'true', true);
}
add_action('admin_init', 'wcdp_dismissed_pages_notice');

// Check option exists
function wcdp_optionExists($option_name){
    global $wpdb;
    $row = $wpdb->get_row($wpdb->prepare("SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", $option_name));
    if(is_object($row)){
        return true;
    }
    return false;
}

// Get Storage Fonts
function wcdp_get_storage_fonts(){
	$wcdp_fonts = get_option('wcdp-settings-fonts');
	$storageFonts = array();
	if($wcdp_fonts){
		foreach($wcdp_fonts as $font){
			$id = is_numeric($font);
			$storageFonts[] = array(
                'id'   => $id ? $font : 'wf',
    		    'name' => $id ? get_the_title($font) : urldecode(explode('=', $font)[1]),					
				'url'  => $id ? wp_get_attachment_url($font) : $font
			);
		}
		$keys = array_map(function($el){ return $el['name']; }, $storageFonts);
		array_multisort($keys, SORT_ASC, $storageFonts);
	}	
	return $storageFonts;
}

// Get imagick profiles
function wcpd_get_imagick_profiles(){
    $general = get_option('wcdp-settings-general');
    $uploads = wp_upload_dir();
	$baseurl = $uploads['baseurl'];
	$basedir = $uploads['basedir'];
    $prRGB   = isset($general['profile_rgb']) ? $general['profile_rgb'] : false;
    $prCMYK  = isset($general['profile_cmyk']) ? $general['profile_cmyk'] : false;
    return array(
	    'RGB'  => $prRGB ? str_replace($baseurl, $basedir, $prRGB) : WCDP_PATH .'profiles/sRGB-IEC61966-2.1.icc',
	    'CMYK' => $prCMYK ? str_replace($baseurl, $basedir, $prCMYK) : WCDP_PATH .'profiles/ISOcoated_v2_eci.icc'
    );
}	

// Copy files to directory
function wcdp_copy_files_directory($source, $target){
    if(!file_exists($target))
	    mkdir($target, 0755, true);
	
    foreach(glob($source .'/*') as $file){
        if(!is_dir($file) && is_readable($file)){
            copy($file, $target .'/'. basename($file));
        }
    }    
}

// Delete directory and content
function wcdp_delete_directory_content($folder){ 
    foreach(glob($folder) as $g){
        if(!is_dir($g)){
            unlink($g);
        } else{
            wcdp_delete_directory_content("$g/*");
            rmdir($g);
        }
    }
}

// Delete temp files
function wcdp_delete_temp_files($folder){
    foreach(glob($folder .'/*') as $file){
        if(file_exists($file)){
			$ext = pathinfo($file, PATHINFO_EXTENSION);
			if($ext !== 'zip' && $ext !== 'json' && basename($file) !== 'cover.jpg')
                unlink($file);				
		}
    }
}

// Convert color hex to rgba
function wcdp_hex_to_rgba($color, $opac){
    $color = substr($color, 1);
    $hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
    $rgb =  array_map('hexdec', $hex);
    return 'rgba('.implode(',', $rgb).','. $opac .')';
}

// Convert color rgb to hex
function wcdp_rgb_to_hex($rgb){
    $R = $rgb['r'];
    $G = $rgb['g'];
    $B = $rgb['b'];
    $R = dechex($R);
    if (strlen($R)<2)
    $R = '0'.$R;
    $G = dechex($G);
    if (strlen($G)<2)
    $G = '0'.$G;
    $B = dechex($B);
    if (strlen($B)<2)
    $B = '0'.$B;
    return $R . $G . $B;
}

// Convert color rgb to cmyk
function wcdp_rgb_to_cmyk($rgb){
    $r = $rgb['r'];
    $g = $rgb['g'];
    $b = $rgb['b'];
    $C = 255 - $r;
    $M = 255 - $g;
    $Y = 255 - $b;
    $K = min($C, $M, $Y);
    $C = round(@(($C-$K)/(255-$K))*100);
    $M = round(@(($M-$K)/(255-$K))*100);
    $Y = round(@(($Y-$K)/(255-$K))*100);
	$K = round($K/255*100);
	if($r == 0 && $g == 0 && $b == 0){
		$C = 0;
		$M = 0;
		$Y = 0;
		$K = 100;
	}
    return $C .','.$M .','.$Y .','.$K;
}

// Convert color cmyk to hex3
function wcdp_cmyk_to_hex3($c, $m, $y, $k){
    $c = ($c / 100);
    $m = ($m / 100);
    $y = ($y / 100);
    $k = ($k / 100);    
    $c = $c * (1 - $k) + $k;
    $m = $m * (1 - $k) + $k;
    $y = $y * (1 - $k) + $k;    
    $r = 1 - $c;
    $g = 1 - $m;
    $b = 1 - $y;
    $r = dechex(round(255 * $r / 17));
    $g = dechex(round(255 * $g / 17));
    $b = dechex(round(255 * $b / 17)); 
    return $r . $g . $b;
}

// Convert color hex to hsl
function wcdp_hex_to_hsl($hex){
	$hex = substr($hex, 1);
    $hex = array($hex[0] . $hex[1], $hex[2] . $hex[3], $hex[4] . $hex[5]);
    $rgb = array_map(function($part){
        return hexdec($part) / 255;
    }, $hex);
    $max = max($rgb);
    $min = min($rgb);
    $l = ($max + $min) / 2;
    if($max == $min){
        $h = $s = 0;
    } else{
        $diff = $max - $min;
        $s = $l > 0.5 ? $diff / (2 - $max - $min) : $diff / ($max + $min);
        switch($max){
            case $rgb[0]:
                $h = ($rgb[1] - $rgb[2]) / $diff + ($rgb[1] < $rgb[2] ? 6 : 0);
                break;
            case $rgb[1]:
                $h = ($rgb[2] - $rgb[0]) / $diff + 2;
                break;
            case $rgb[2]:
                $h = ($rgb[0] - $rgb[1]) / $diff + 4;
                break;
        }
        $h /= 6;
    }
    return array('h' => $h, 's' => $s, 'l' => $l);
}