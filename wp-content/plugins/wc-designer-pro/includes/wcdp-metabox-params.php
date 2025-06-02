<?php

// Register Parameters post type
function wcdp_parameters_init(){	
	$labels = array(
		'name'               => __( 'Parameters', 'wcdp' ),
		'singular_name'      => __( 'Parameter', 'wcdp' ),
		'menu_name'          => __( 'Parameters', 'wcdp' ),
		'add_new'            => __( 'Add New parameter', 'wcdp' ),
		'add_new_item'       => __( 'Add New parameter', 'wcdp' ),
		'new_item'           => __( 'New Parameter', 'wcdp' ),
		'edit_item'          => __( 'Edit Parameter', 'wcdp' ),
		'view_item'          => __( 'View Parameter', 'wcdp' ),
		'search_items'       => __( 'Search Parameters', 'wcdp' ),
		'not_found'          => __( 'No Parameters found.', 'wcdp' ),
		'not_found_in_trash' => __( 'No Parameters found in Trash.', 'wcdp' )
	);
	$args = array(
		'labels'              => $labels,
        'description'         => __( 'Settings product parameters.', 'wcdp' ),
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
	register_post_type('wcdp-parameters', $args);
}
add_action('init', 'wcdp_parameters_init');

// Get custom parameters
function wcdp_get_params_product_designer($args){
	$paramID = get_post_meta($args['designID'], '_wcdp_parameter_design', true);
    $params = array(	    
		'text_e'       => 'on',
        'images_e'     => 'on',
		'settings_e'   => 'on',
		'my-designs_e' => 'on',
		'code_e'       => 'on',
		'templates_e'  => 'off',
		'fontsDesign'  => array('titillium'),
	    'editor'       => $args['editor'],
		'pageID'       => $args['pageID'],
	    'designID'     => $args['designID'],
		'productID'    => $args['productID'],
		'attr_data'    => $args['attr_data'],
		'mode'         => $args['mode'],
		'paramID'      => $paramID
	);
	$paramStatus = get_post_status($paramID);
	foreach(wcdp_meta_keys_params() as $meta_key => $meta_value){
		if(empty($paramID) || !$paramStatus){
		    $params[$meta_value[0]] = $meta_value[1];
		}
		else if(metadata_exists('post', $paramID, $meta_key)){
		    $get_post = get_post_meta($paramID, $meta_key, true);
            $params[$meta_value[0]] = !empty($get_post) && $paramStatus === 'publish' ? $get_post : $meta_value[1];	
		}		
	}
	
	// Add attribute actions & product name
	if($args['productID']){
		$params['attr_actions'] = get_post_meta($args['productID'], '_wcdp_product_attribute_actions', true);
		$params['productName'] = get_the_title($args['productID']);
	}
	
	// Add all cliparts categories 
	if(isset($params['all_cliparts']) && $params['all_cliparts'] === 'on'){
		$params['category_cliparts'] = array();
        $clipCats = get_posts(
		    array(
                'post_type'   => 'wcdp-cliparts',
                'post_status' => 'publish',
                'numberposts' => -1,
				'orderby'     => 'title',
                'order'       => 'ASC'
            )
		);
		if($clipCats){
		    foreach($clipCats as $clipCat){
				$params['category_cliparts'][] = $clipCat->ID;
		    }
		}
	}
	
	// Add all calendars categories
	if(isset($params['all_calendars']) && $params['all_calendars'] === 'on'){
		$params['category_calendars'] = array();
        $cazCats = get_posts(
		    array(
                'post_type'   => 'wcdp-calendars',
                'post_status' => 'publish',
                'numberposts' => -1,
				'orderby'     => 'title',
                'order'       => 'ASC'
            )
		);
		if($cazCats){
		    foreach($cazCats as $cazCat){
				$params['category_calendars'][] = $cazCat->ID;
		    }
		}
	}	
	
	// Add json canvas sides
	$canvasSides = array('front');
	if($params['type'] !== 'typeF')
	    array_push($canvasSides, 'back');
	
    foreach($canvasSides as $side){
        $fileURL = $args['designURL'] .'/'. $side .'.json';
		$isFile = is_file($fileURL);
	    if($isFile) $result = file_get_contents($fileURL);
        $canvasSide = $isFile && $result ? $result : '';
		if($canvasSide){
    	    foreach(json_decode($canvasSide, true)['objects'] as $obj){
                if($obj['clas'] == 'i-text' && !in_array($obj['fontFamily'], $params['fontsDesign']))
                    $params['fontsDesign'][] = $obj['fontFamily'];	        
    	    }
		}
	    $params['jsonSides'][$side] = array($canvasSide);
		$params['jsonStates'][$side] = 0;
	}

	// Add multiple designs
	if($args['productID']){
	    $get_multiple_designs = get_post_meta($args['productID'], '_wcdp_product_multiple_designs', true);
		$get_multiple_designs_cat = get_post_meta($args['productID'], '_wcdp_product_multiple_designs_cat', true);
		$params['loadObjs'] = get_post_meta($args['productID'], '_wcdp_load_designs_by_objs', true);
		$params['loadAjax'] = get_post_meta($args['productID'], '_wcdp_load_designs_by_ajax', true);
	    if($get_multiple_designs){			
		    foreach($get_multiple_designs as $multidesignID){
			    if(get_post_status($multidesignID) == 'publish')
		            $params['multipleDesigns'][] = $multidesignID;
		    }
	    }
	    if($get_multiple_designs_cat)
			$params['multipleDesignsCat'] = $get_multiple_designs_cat;
	}
	return $params;
}

// Defaults parameters
function wcdp_meta_keys_params(){
	return array( 
		'_wcdp_page_design'             => ['page_edit', ''],		
	    '_wcdp_design_type'             => ['type', 'typeFB'],
    	'_wcdp_design_label_front'      => ['label_f', __( 'Front', 'wcdp' )],
		'_wcdp_design_label_back'       => ['label_b', __( 'Back', 'wcdp' )],			
		'_wcdp_design_width'            => ['canvas_w', '680'],			
		'_wcdp_design_height'           => ['canvas_h', '450'],			
		'_wcdp_design_output_width'     => ['output_w', '1360'],
		'_wcdp_design_output_bleed'     => ['output_inside_bleed', ''],
		'_wcdp_design_watermark_img'    => ['watermark_img', ''],
		'_wcdp_design_watermark_rep'    => ['watermark_rep', ''],
		'_wcdp_design_preview'          => ['preview_e', 'on'],			
		'_wcdp_design_preview_width'    => ['preview_w', '300'],
        '_wcdp_design_pdf_width'        => ['pdf_w', '360'],
		'_wcdp_design_pdf_height'       => ['pdf_h', '238'],
		'_wcdp_design_pdf_margin'       => ['pdf_margin', '-1'],
		'_wcdp_design_pdf_scale'        => ['pdf_scale', '0.8'],
		'_wcdp_design_pdf_strech'       => ['pdf_strech', 'on'],
		'_wcdp_design_bleed'            => ['border_bleed_e', 'on'],
		'_wcdp_design_auto_hide_bleed'  => ['auto_hide_bleed', 'on'],
		'_wcdp_design_bleed_clip'       => ['bleed_clip', 'on'],
		'_wcdp_design_bleed_width'      => ['border_bleed_w', '1'],
		'_wcdp_design_margin_bleed_lr'  => ['margin_bleed_lr', '20'],			
		'_wcdp_design_margin_bleed_tb'  => ['margin_bleed_tb', '20'],
		'_wcdp_design_bleed_top'        => ['bleed_top', '0'],
		'_wcdp_design_bleed_left'       => ['bleed_left', '0'],
        '_wcdp_design_bleed_radius'     => ['bleed_radius', '0'],
		'_wcdp_design_border_radius'    => ['radius', '0'],
		'_wcdp_design_border_solid'     => ['border_solid', ''],
		'_wcdp_design_box_shadow'       => ['box_shadow', 'on'],			
		'_wcdp_design_grid_size'        => ['grid_size', '25'],			
		'_wcdp_design_font_size'        => ['font_size', '24'],
		'_wcdp_design_image_size'       => ['image_size', '150'],
		'_wcdp_design_shape_size'       => ['shape_size', '80'],
		'_wcdp_design_qr_size'          => ['qr_size', '80'],
		'_wcdp_design_ungroup_svg'      => ['ungroup_svg', 'on'],
		'_wcdp_design_user_mask'        => ['user_mask', 'on'],
		'_wcdp_design_user_bg'          => ['user_bg', ''],
		'_wcdp_design_hide_bc'          => ['hide_bc', ''],
		'_wcdp_design_hide_bg'          => ['hide_bg', ''],
		'_wcdp_design_hide_ov'          => ['hide_ov', ''],
		'_wcdp_design_shapes'           => ['shapes_e', 'on'],			
		'_wcdp_design_cliparts'         => ['cliparts_e', 'on'],
        '_wcdp_design_all_cliparts'     => ['all_cliparts', 'on'],
		'_wcdp_design_cat_cliparts'     => ['category_cliparts', []],		
		'_wcdp_design_qr'               => ['qr_e', 'on'],			
		'_wcdp_design_calendars'        => ['calendars_e', 'on'],
		'_wcdp_design_all_calendars'    => ['all_calendars', 'on'],
		'_wcdp_design_cat_calendars'    => ['category_calendars', []],		
		'_wcdp_design_bg_colors'        => ['bgcolors_e', ''],
        '_wcdp_design_layers'           => ['layers_e', 'on'],		
		'_wcdp_design_maps'             => ['maps_e', 'on'],
		'_wcdp_design_maps_width'       => ['maps_w', '640'],
		'_wcdp_design_maps_height'      => ['maps_h', '480'],
	);	
}

// Add admin metabox parameters
function wcdp_parameters_metabox_html($post){
	$parameters = array(
	    array(
		    'type' => 'dropdown',
		    'title' => __( 'Select page for editor', 'wcdp' ),
		    'description' => __( 'Select a page where the designs will be personalized.', 'wcdp' )
		),
	    array(
		    'type' => 'select',
		    'title' => __( 'Select type of design', 'wcdp' ),
		    'description' => __( 'Select type of design for editor.', 'wcdp' ),
            'options' => array(
				'typeFB'   => __( 'Side front and back', 'wcdp' ),
				'typeF'    => __( 'Only side front', 'wcdp' ),
				'typeDIP'  => __( 'Diptych Brochure', 'wcdp' ),
				'typeTRIP' => __( 'Triptych Brochure', 'wcdp' )
			)
		),
	    array(
		    'type' => 'text',
		    'label' => '',
		    'title' => __( 'Name front side', 'wcdp' ),
		    'description' => __( 'Default name "Front"', 'wcdp' )
		),
	    array(
		    'type' => 'text',
		    'label' => '',
		    'title' => __( 'Name back side', 'wcdp' ),
		    'description' => __( 'Default name "Back"', 'wcdp' )
		),
	    array(
		    'type' => 'number',
		    'label' => 'px',
		    'title' => __( 'Canvas width', 'wcdp' ),
		    'description' => __( 'Set canvas width for editor.', 'wcdp' )
		),
	    array(
		    'type' => 'number',
		    'label' => 'px',
		    'title' => __( 'Canvas height', 'wcdp' ),
		    'description' => __( 'Set canvas height for editor.', 'wcdp' )
		),
	    array(
		    'type' => 'number',
		    'label' => 'px',
		    'title' => __( 'Canvas output width', 'wcdp' ),
		    'description' => __( 'Set output files width.', 'wcdp' )
		),
	    array(
		    'type' => 'checkbox',
		    'label' => 'Enable',
		    'title' => __( 'Canvas output bleed area', 'wcdp' ),
		    'description' => __( 'Capture only the inside of the bleed area in the output files. Requires option Border bleed area enabled.', 'wcdp' )
		),
	    array(
		   'type' => 'media',
		   'library' => array('image','png'),
		   'label' => __( 'image', 'wcdp' ),
		   'title' => __( 'Add watermark', 'wcdp' ),
		   'description' => __( 'Add watermark when downloading designs.', 'wcdp' )
		),
	    array(
		    'type' => 'checkbox',
		    'label' => 'Enable',
		    'title' => __( 'Repeat watermark image', 'wcdp' ),
		    'description' => __( 'Repeat the watermark in the background.', 'wcdp' )
		),		
	    array(
		    'type' => 'checkbox',
		    'label' => __( 'Enable', 'wcdp' ),
		    'title' => __( 'Preview design', 'wcdp' ),
		    'description' => __( 'Enable/Disable design preview button in the toolbar options.', 'wcdp' )
		),
	    array(
		    'type' => 'number',
		    'label' => 'px',
		    'title' => __( 'Preview design width', 'wcdp' ),
		    'description' => __( 'Set width of preview images.', 'wcdp' )
		),		
	    array(
		    'type' => 'number',
		    'label' => 'mm',
		    'title' => __( 'PDF output width', 'wcdp' ),
		    'description' => __( 'Set PDF output files width.', 'wcdp' )
		),		
	    array(
		    'type' => 'number',
		    'label' => 'mm',
		    'title' => __( 'PDF output height', 'wcdp' ),
		    'description' => __( 'Set PDF output files height.', 'wcdp' )
		),
	    array(
		    'type' => 'number',
		    'label' => 'mm',
		    'title' => __( 'PDF margin top', 'wcdp' ),
		    'description' => __( 'Set PDF margin top. To remove margin, set value -1', 'wcdp' )
		),
	    array(
		    'type' => 'number',
		    'label' => '',
		    'title' => __( 'PDF scale output image', 'wcdp' ),
		    'description' => __( 'Output image is scaled proportionally by the provided scale factor. Default 0.8', 'wcdp' )
		),
	    array(
		    'type' => 'checkbox',
		    'label' => __( 'Enable', 'wcdp' ),
		    'title' => __( 'PDF stretch output image', 'wcdp' ),
		    'description' => __( 'Stretch the output image to the PDF size automatically.', 'wcdp' )
		),
	    array(
		    'type' => 'checkbox',
		    'label' => __( 'Enable', 'wcdp' ),
		    'title' => __( 'Border bleed area', 'wcdp' ),
		    'description' => __( 'Show/Hide border bleed area in editor.', 'wcdp' )
		),
	    array(
		    'type' => 'checkbox',
		    'label' => __( 'Enable', 'wcdp' ),
		    'title' => __( 'Auto hide bleed area', 'wcdp' ),
		    'description' => __( 'Auto hide bleed area when there is no object selected in the editor.', 'wcdp' )
		),		
	    array(
		    'type' => 'checkbox',
		    'label' => __( 'Enable', 'wcdp' ),
		    'title' => __( 'Clipping objects to bleed area', 'wcdp' ),
		    'description' => __( 'Clipping and hide the objects outside the bleed area. Requires option Border bleed area enabled.', 'wcdp' )
		),
	    array(
		    'type' => 'number',
		    'label' => 'px',
		    'title' => __( 'Border width bleed area', 'wcdp' ),
		    'description' => __( 'Set width of border bleed area.', 'wcdp' )
		),
	    array(
		    'type' => 'number',
		    'label' => 'px',
		    'title' => __( 'Margin bleed area horizontally', 'wcdp' ),
		    'description' => __( 'Set the margin bleed area from outside to inside. Left/Right', 'wcdp' )
		),		
	    array(
		    'type' => 'number',
		    'label' => 'px',
		    'title' => __( 'Margin bleed area vertically', 'wcdp' ),
		    'description' => __( 'Set the margin bleed area from outside to inside. Top/Bottom', 'wcdp' )
		),
	    array(
		    'type' => 'number',
		    'label' => 'px',
		    'title' => __( 'Top bleed area', 'wcdp' ),
		    'description' => __( 'Set top position of border bleed area.', 'wcdp' )
		),
	    array(
		    'type' => 'number',
		    'label' => 'px',
		    'title' => __( 'Left bleed area', 'wcdp' ),
		    'description' => __( 'Set left position of border bleed area.', 'wcdp' )
		),
	    array(
  		    'type' => 'number',
		    'label' => 'px',
		    'title' => __( 'Radius bleed area', 'wcdp' ),
		    'description' => __( 'Set the radius bleed area.', 'wcdp' )
		),		
	    array(
  		    'type' => 'number',
		    'label' => 'px',
		    'title' => __( 'Editor corners rounded', 'wcdp' ),
		    'description' => __( 'Round corners in the canvas editor.', 'wcdp' )
		),
	    array(
  		    'type' => 'checkbox',
		    'label' => __( 'Enable', 'wcdp' ),
		    'title' => __( 'Editor border solid', 'wcdp' ),
		    'description' => __( 'Border solid in the canvas editor.', 'wcdp' )
		),		
	    array(
  		    'type' => 'checkbox',
		    'label' => __( 'Enable', 'wcdp' ),
		    'title' => __( 'Editor box shadow', 'wcdp' ),
		    'description' => __( 'Box shadow in the canvas editor.', 'wcdp' )
		),			
	    array(
		    'type' => 'number',
		    'label' => 'px',
		    'title' => __( 'Grid size', 'wcdp' ),
		    'description' => __( 'Size of grid cells.', 'wcdp' )
		),
	    array(
		    'type' => 'select',
		    'title' => __( 'Default font size', 'wcdp' ),
		    'description' => __( 'Default font size in the text tab.', 'wcdp' ),
		    'options' => array(8,9,10,11,12,13,14,15,16,17,18,20,22,24,26,28,30,32,36,42,48,72,96)
		),
	    array(
		    'type' => 'number',
		    'label' => 'px',
		    'title' => __( 'Default image size', 'wcdp' ),
		    'description' => __( 'Image size when add to the canvas. Default size 150px', 'wcdp' )
		),
	    array(
		    'type' => 'number',
		    'label' => 'px',
		    'title' => __( 'Default shape size', 'wcdp' ),
		    'description' => __( 'Shape size when add to the canvas. Default size 80px', 'wcdp' )
		),
	    array(
		    'type' => 'number',
		    'label' => 'px',
		    'title' => __( 'Default QR size', 'wcdp' ),
		    'description' => __( 'QR Code size when add to the canvas. Default size 80px', 'wcdp' )
		),
	    array(
		    'type' => 'checkbox',
		    'label' => __( 'Enable', 'wcdp' ),
		    'title' => __( 'Group and ungroup SVG', 'wcdp' ),
		    'description' => __( 'Enable/Disable for the user the option to group and ungroup SVG.', 'wcdp' )
		),
	    array(
		    'type' => 'checkbox',
		    'label' => __( 'Enable', 'wcdp' ),
		    'title' => __( 'Mask layers', 'wcdp' ),
		    'description' => __( 'Enable/Disable for user the option to mask layers.', 'wcdp' )
		),
	    array(
		    'type' => 'checkbox',
		    'label' => __( 'Enable', 'wcdp' ),
		    'title' => __( 'Add or extract background images', 'wcdp' ),
		    'description' => __( 'Enable/Disable for user the option to add or extract background images.', 'wcdp' )
		),
	    array(
		    'type' => 'checkbox',
		    'label' => __( 'Enable', 'wcdp' ),
		    'title' => __( 'Hide background color in the output files', 'wcdp' ),
		    'description' => __( 'Hide the background color of the design when generating the output files.', 'wcdp' )
		),
	    array(
		    'type' => 'checkbox',
		    'label' => __( 'Enable', 'wcdp' ),
		    'title' => __( 'Hide background image in the output files', 'wcdp' ),
		    'description' => __( 'Hide the background image of the design when generating the output files.', 'wcdp' )
		),
	    array(
		    'type' => 'checkbox',
		    'label' => __( 'Enable', 'wcdp' ),
		    'title' => __( 'Hide overlay image in the output files', 'wcdp' ),
		    'description' => __( 'Hide the overlay image of the design when generating the output files.', 'wcdp' )
		),
	    array(
		    'type' => 'checkbox',
		    'label' => __( 'Enable', 'wcdp' ),
		    'title' => __( 'Shapes tab', 'wcdp' ),
		    'description' => __( 'Enable/Disable the section of geometric shapes.', 'wcdp' )
		),
	    array(
		    'type' => 'checkbox',
		    'label' => __( 'Enable', 'wcdp' ),
		    'title' => __( 'Cliparts tab', 'wcdp' ),
		    'description' => __( 'Enable/Disable the section of cliparts.', 'wcdp' )
		),
	    array(
		    'type' => 'checkbox',
		    'label' => __( 'Enable', 'wcdp' ),
		    'title' => __( 'Add all cliparts categories', 'wcdp' ),
		    'description' => sprintf( __( 'Add all %1$scategories%2$s to the cliparts section.', 'wcdp' ), '<a href="'. admin_url() .'edit.php?post_type=wcdp-cliparts" title="'. __( 'Add new category of cliparts', 'wcdp' ) .'" target="_self">', '</a>'),
		),
	    array(
		    'type' => 'select2',
			'post-id' => 'cliparts',
		    'title' => __( 'Add cliparts categories', 'wcdp' ),
		    'description' => sprintf( __( 'Add %1$scategories%2$s to the cliparts section.', 'wcdp' ), '<a href="'. admin_url() .'edit.php?post_type=wcdp-cliparts" title="'. __( 'Add new category of cliparts', 'wcdp' ) .'" target="_self">', '</a>'),
		),		
	    array(
		    'type' => 'checkbox',
		    'label' => __( 'Enable', 'wcdp' ),
		    'title' => __( 'QR tab', 'wcdp' ),
		    'description' => __( 'Enable/Disable the section of qr.', 'wcdp' )
		),
	    array(
		    'type' => 'checkbox',
		    'label' => __( 'Enable', 'wcdp' ),
		    'title' => __( 'Calendars tab', 'wcdp' ),
		    'description' => __( 'Enable/Disable the section of calendars.', 'wcdp' )
		),
	    array(
		    'type' => 'checkbox',
		    'label' => __( 'Enable', 'wcdp' ),
		    'title' => __( 'Add all calendars categories', 'wcdp' ),
		    'description' => sprintf( __( 'Add all %1$scategories%2$s to the calendars section.', 'wcdp' ), '<a href="'. admin_url() .'edit.php?post_type=wcdp-calendars" title="'. __( 'Add new category of calendars', 'wcdp' ) .'" target="_self">', '</a>'),
		),
	    array(
		    'type' => 'select2',
			'post-id' => 'calendars',
		    'title' => __( 'Add calendars categories', 'wcdp' ),
		    'description' => sprintf( __( 'Add %1$scategories%2$s to the calendars section.', 'wcdp' ), '<a href="'. admin_url() .'edit.php?post_type=wcdp-calendars" title="'. __( 'Add new category of calendars', 'wcdp' ) .'" target="_self">', '</a>'),
		),			
	    array(
		    'type' => 'checkbox',
		    'label' => __( 'Enable', 'wcdp' ),
		    'title' => __( 'Background colors tab', 'wcdp' ),
		    'description' => __( 'Enable/Disable the section of background colors.', 'wcdp' )
		),
	    array(
		    'type' => 'checkbox',
		    'label' => __( 'Enable', 'wcdp' ),
		    'title' => __( 'Layers tab', 'wcdp' ),
		    'description' => __( 'Enable/Disable the section of manage layers.', 'wcdp' )
		),
	    array(
		    'type' => 'checkbox',
		    'label' => __( 'Enable', 'wcdp' ),
		    'title' => __( 'Static maps tab', 'wcdp' ),
		    'description' => __( 'Enable/Disable the section of static maps.', 'wcdp' )
		),
	    array(
		    'type' => 'number',
		    'label' => 'px',
		    'title' => __( 'Static maps width', 'wcdp' ),
		    'description' => __( 'Set width of static maps.', 'wcdp' )
		),
	    array(
		    'type' => 'number',
		    'label' => 'px',
		    'title' => __( 'Static maps height', 'wcdp' ),
		    'description' => __( 'Set height of static maps.', 'wcdp' )
		)
	);
	?>
	<script>
	    var AJAX_URL = <?php echo json_encode( admin_url( 'admin-ajax.php' ) ); ?>,
	        wcdp_translations = <?php echo json_encode(wcdp_editor_translations()); ?>;
	</script>
    <div class="wrap">
	    <table id="wcdp-parameters" class="form-table">
            <?php
		    $metaKeys = array_keys(wcdp_meta_keys_params()); 
		    foreach($parameters as $paramKey => $param){
		        $param['id'] = $metaKeys[$paramKey];
				$param['value'] = get_post_meta($post->ID, $metaKeys[$paramKey], true);
		        ?>
	            <tr>
	                <th><label><?php echo $param['title']; ?>:</label></th>
	                <td> 
				    <?php
					    if($param['type'] == 'dropdown'){
                            wp_dropdown_pages( array(
				                'depth'    => -1,						        
						        'name'     => $param['id'],
								'selected' => $param['value']
                            ));					 
					    } else if($param['type'] == 'select'){ ?>
				            <select name="<?php echo $param['id']; ?>" id="<?php echo $param['id']; ?>">
				                <?php 
								$label = '';
                                foreach($param['options'] as $key => $value){
							        if($param['id'] == '_wcdp_design_font_size'){
										$key = $value;
										$label = ' px';
									} ?>
                                    <option value="<?php echo $key;?>" <?php echo ($key == $param['value']) ? ' selected' : '';?>><?php echo $value . $label; ?></option>
                                    <?php
								} ?>
                            </select>	
                        <?php
						} else if($param['type'] == 'select2'){ ?>
						    <select name="<?php echo $param['id']; ?>[]" id="<?php echo $param['id']; ?>" post-id="<?php echo $param['post-id']; ?>" multiple="multiple">
							<?php
			                    if($param['value']){
		                            foreach($param['value'] as $category){ 
									    if(get_post_status($category) == 'publish'){
				                            $title = wcdp_get_post_title($category); ?>
			                                <option value="<?php echo $category; ?>" selected="selected"><?php echo $title; ?></option>
									        <?php
										}
									} 
								} ?>
							</select>
							<?php			
						} else if($param['type'] == 'media'){
	                        wp_enqueue_media();
		                    $filename = !empty($param['value']) ? basename($param['value']) : ''; ?>
	                        <div id="contain<?php echo $param['id']; ?>" format="<?php echo $param['library'][0]; ?>" support="<?php echo $param['library'][1]; ?>">
                                <input <?php if($param['value'] != ''){ ?> style="display:none" <?php } ?> type="button" class="wcdp-select-file button" value="<?php printf( __( 'Upload %s', 'wcdp' ), $param['label']); ?> " />
	                            <input <?php if($param['value'] == ''){ ?> style="display:none" <?php } ?> type="button" class="wcdp-remove-file button" value="<?php printf( __( 'Remove %s', 'wcdp' ), $param['label']); ?> " />
                                <input class="value-file" type="hidden" name="<?php echo $param['id']; ?>" id="<?php echo $param['id']; ?>" value="<?php echo $param['value']; ?>">
	                            <div class="media-filename"><?php echo $filename ?></div>
	                        </div>
		                    <?php	
	                    } else if($param['type'] == 'text' || $param['type'] == 'number'){
							$step = $param['id'] == '_wcdp_design_pdf_scale' ? ' step="0.01"':''; ?>
						    <label><input type="<?php echo $param['type']; ?>"<?php echo $step; ?> name="<?php echo $param['id']; ?>" value="<?php echo $param['value']; ?>"> <?php echo $param['label'];?></label>
					    <?php
						} else if($param['type'] == 'checkbox'){ ?>
						    <label><input type="checkbox" name="<?php echo $param['id']; ?>" <?php if($param['value'] == 'on') echo ' checked="checked"'; ?>> <?php echo $param['label'];?></label>
						<?php
						} ?>
					    <p class="description"><?php echo $param['description']; ?></p>
                    </td>
		        </tr>				 
		        <?php
		    } ?>
	    </table>
    </div>
    <?php
}
add_action('add_meta_boxes', function(){
	add_meta_box('wcdp-parameters-box', __('Settings Parameters', 'wcdp'), 'wcdp_parameters_metabox_html', 'wcdp-parameters', 'normal', 'high');
});

function wcdp_parameter_save_post($post_id){
    if(get_post_type($post_id) == 'wcdp-parameters'){
		$paramStatus = get_post_status($post_id); 
        foreach(wcdp_meta_keys_params() as $meta_key => $meta_value){
			if($paramStatus === 'publish'){
				if(isset($_POST['action']) && $_POST['action'] != 'inline-save'){
                    if(isset($_POST[$meta_key])){
					    $value = $_POST[$meta_key];
					} else if($meta_value[1] == 'on'){
		                $value = 'off';
					} else if(is_array($meta_value[1])){
					    $value = array();
					} else{
		                $value = '';
					}
                    if(metadata_exists('post', $post_id, $meta_key)){
				        update_post_meta($post_id, $meta_key, $value);
					} else{
                        add_post_meta($post_id, $meta_key, $value);
					}
				}
			} else{
				add_post_meta($post_id, $meta_key, $meta_value[1]);
			}
	    }
	}
}	
add_action('save_post', 'wcdp_parameter_save_post');