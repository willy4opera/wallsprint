<?php

// Add wcdp menu admin settings
function wcdp_settings_menu_admin(){
	add_menu_page( __( 'Settings WC Designer Pro', 'wcdp' ),
				       'WC Designer Pro',
				       'manage_options',
				       'wcdp-content-dashboard',
				       'wcdp_content_page_settings',
				       WCDP_URL . '/assets/images/wcdp-admin-ico.png'
	);
    add_submenu_page(  'wcdp-content-dashboard',
                       __( 'Settings', 'wcdp' ),
					   __( 'Settings', 'wcdp' ),
					   'manage_options',
					   'wcdp-content-settings',
					   'wcdp_content_page_settings'
	);
    add_submenu_page(  'wcdp-content-dashboard',
                       __( 'Fonts', 'wcdp' ),
					   __( 'Fonts', 'wcdp' ),
					   'manage_options',
					   'wcdp-content-fonts',
					   'wcdp_content_page_fonts'
	);
    add_submenu_page(  'wcdp-content-dashboard',
                       __( 'Shapes', 'wcdp' ),
					   __( 'Shapes', 'wcdp' ),
					   'manage_options',
					   'wcdp-content-shapes',
					   'wcdp_content_page_shapes'
	);
    add_submenu_page(  'wcdp-content-dashboard',
                       __( 'Cliparts', 'wcdp' ),
					   __( 'Cliparts', 'wcdp' ),
					   'manage_options',
					   'edit.php?post_type=wcdp-cliparts',
					   false
	);
    add_submenu_page(  'wcdp-content-dashboard',
                       __( 'Calendars', 'wcdp' ),
					   __( 'Calendars', 'wcdp' ),
					   'manage_options',
					   'edit.php?post_type=wcdp-calendars',
					   false
	);
    add_submenu_page(  'wcdp-content-dashboard',
                       __( 'Image filters', 'wcdp' ),
					   __( 'Image filters', 'wcdp' ),
					   'manage_options',
					   'wcdp-content-filters',
					   'wcdp_content_page_filters'
	);
    add_submenu_page(  'wcdp-content-dashboard',
                       __( 'Parameters', 'wcdp' ),
					   __( 'Parameters', 'wcdp' ),
					   'manage_options',
					   'edit.php?post_type=wcdp-parameters',
					   false
	);
    add_submenu_page(  'wcdp-content-dashboard',
                       __( 'Categories', 'wcdp' ),
					   __( 'Categories', 'wcdp' ),
					   'manage_options',
					   'edit-tags.php?taxonomy=wcdp-design-cat&post_type=wcdp-designs',
					   false
	);
    add_submenu_page(  'wcdp-content-dashboard',
                       __( 'Designs', 'wcdp' ),
					   __( 'Designs', 'wcdp' ),
					   'manage_options',
					   'edit.php?post_type=wcdp-designs',
					   false
	);
    add_submenu_page(  'wcdp-content-dashboard',
                       __( 'Documentation', 'wcdp' ),
					   __( 'Documentation', 'wcdp' ),
					   'manage_options',
					   'wcdp-content-docs',
					   'wcdp_content_page_docs'
	);		
	remove_submenu_page( 'wcdp-content-dashboard','wcdp-content-dashboard' );
}
add_action('admin_menu','wcdp_settings_menu_admin');

// Add tabs for general settings
function wcdp_content_page_settings(){
   ?>
    <div class="wrap">
        <div id="icon-themes" class="icon32"></div>  
        <h2><?php _e( 'WooCommerce Designer Pro Settings', 'wcdp' ); ?></h2>
	    <?php 
		    settings_errors();  
            $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
        ?>         
        <h2 class="nav-tab-wrapper">  
            <a href="?page=wcdp-content-settings&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><?php _e( 'General', 'wcdp' ); ?></a>  
            <a href="?page=wcdp-content-settings&tab=shortcut_keys" class="nav-tab <?php echo $active_tab == 'shortcut_keys' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Shortcut Keys', 'wcdp' ); ?></a>
            <a href="?page=wcdp-content-settings&tab=style" class="nav-tab <?php echo $active_tab == 'style' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Style', 'wcdp' ); ?></a>
            <a href="?page=wcdp-content-settings&tab=license" class="nav-tab <?php echo $active_tab == 'license' ? 'nav-tab-active' : ''; ?>"><?php _e( 'License', 'wcdp' ); ?></a>  		   
        </h2>		
	    <form method="POST" action="options.php">
           <?php
		   	$wcdp_settings = array(
	            'general'    => get_option('wcdp-settings-general'),
		        'style'      => get_option('wcdp-settings-style'),
		        'parameters' => null
	        );
		    wcdp_add_global_variables_javascript_settings($wcdp_settings);

            if($active_tab == 'general'){
                settings_fields('wcdp-settings-general-group');
                do_settings_sections('wcdp-menu-general');  
            }
			else if($active_tab == 'shortcut_keys'){
                settings_fields( 'wcdp-settings-shortcutkeys-group' );
                do_settings_sections( 'wcdp-menu-shortcut-keys' );
            } 
			else if($active_tab == 'style'){
				wcdp_register_spectrum_mod_cmyk();
                settings_fields( 'wcdp-settings-style-group' );
                do_settings_sections( 'wcdp-menu-style' );
            } 
			else if($active_tab == 'license'){
				settings_fields( 'wcdp-settings-license-group' );
                do_settings_sections( 'wcdp-menu-license' );
            }
            submit_button();
			?>
	    </form>
    </div>
    <?php
}

// Register general settings
function wcdp_general_settings_section(){
	
    add_settings_section('wcdp_general_settings', __( 'General settings', 'wcdp'), null, 'wcdp-menu-general');
	add_settings_section('wcdp_shortcut_keys_settings', __( 'Define Shortcut Keys', 'wcdp'), null, 'wcdp-menu-shortcut-keys');
	add_settings_section('wcdp_style_settings', __( 'Style options', 'wcdp'), null, 'wcdp-menu-style');
	add_settings_section('wcdp_license_section', __( 'License', 'wcdp'), null, 'wcdp-menu-license');
	
	register_setting('wcdp-settings-general-group', 'wcdp-settings-general');
	register_setting('wcdp-settings-shortcutkeys-group', 'wcdp-settings-shortcutkeys');
	register_setting('wcdp-settings-style-group', 'wcdp-settings-style');
	register_setting('wcdp-settings-license-group', 'wcdp-settings-license');

	// Section general settings
	$general_settings = array(
	    array(
		   'type' => 'dropdown',
		   'field' => 'page_editor_default',
		   'title' => __( 'Default page for editor:', 'wcdp' ),
		   'description' => __( 'Select a page where the designs will be personalized.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'shortcode_editor',
		   'title' => __( 'Add shortcode manually for editor page:', 'wcdp' ),
		   'description' => __( 'Copy shortcode: [wcdp_editor], on the default page for editor.', 'wcdp' )
		),
	    array(
		   'type' => 'dropdown',
		   'field' => 'page_save_user_default',
		   'title' => __( 'Default page for my designs:', 'wcdp' ),
		   'description' => __( 'Select a page where the saved designs of the user will be displayed.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'shortcode_save_user',
		   'title' => __( 'Add shortcode manually for my designs page:', 'wcdp' ),
		   'description' => __( 'Copy shortcode: [wcdp_my_designs], on the default page for my designs.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'buttom_myaccount',
		   'title' => __( 'Add buttom my designs for account page:', 'wcdp' ),
		   'description' => __( 'Requires to have selected a my designs page for link.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'rtl_mode',
		   'title' => __( 'Enable RTL mode:', 'wcdp' ),
		   'description' => __( 'Enable right to left reading mode.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'CMYK',
		   'title' => __( 'Enable CMYK editor (Requires ImageMagick):', 'wcdp' ),
		   'description' => null
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'imagick_cmd',
		   'title' => __( 'Conversion ImageMagick by shell commands:', 'wcdp' ),
		   'description' => __( 'Change all conversions to CMYK by shell commands.<br><b>Important!!</b> The use of commands will have a higher overhead and the processes tend to be slower depending on your server.', 'wcdp' )
		),	
	    array(
		   'type' => 'checkbox',
		   'field' => 'cmyk_picker',
		   'title' => __( 'Enable CMYK in color picker:', 'wcdp' ),
		   'description' => __( 'Show and convert the color picker in CMYK automatically. Requires option CMYK enabled.', 'wcdp' )
		),
	    array(
		   'type' => 'button',
		   'label' => __( 'Update table', 'wcdp' ),
		   'field' => 'picker-table-update',
		   'title' => __( 'Update colors table in color picker:', 'wcdp' ),
		   'description' => __( 'If you change the default profiles, you can make a new CMYK color table with the new selected profiles and update the color picker. Requires option CMYK enabled.', 'wcdp' )
		),		
	    array(
		   'type' => 'select',
		   'field' => 'chunk_colors',
		   'title' => __( 'Max colors chunck by conversion to CMYK:', 'wcdp' ),
		   'description' => __( 'Select the maximum number of colors per conversion at a time to make the color picker table and to convert the SVG images uploads into CMYK. You can increase the value depending on the speed and memory of your server.', 'wcdp' ),
		   'items' => array(
                '1'   => __( '1 color', 'wcdp' ),
				'4'   => __( '4 colors', 'wcdp' ),
				'8'   => __( '8 colors', 'wcdp' ),
              	'16'  => __( '16 colors', 'wcdp' ),
				'32'  => __( '32 colors', 'wcdp' ),
				'64'  => __( '64 colors', 'wcdp' ),
				'128' => __( '128 colors', 'wcdp' ),
				'256' => __( '256 colors', 'wcdp' ),
				'512' => __( '512 colors', 'wcdp' )
		    )								
		),		
	    array(
		   'type' => 'checkbox',
		   'field' => 'cmyk_convert',
		   'title' => __( 'Convert images uploads to CMYK automatically:', 'wcdp' ),
		   'description' => __( 'Uploads in editor, cliparts, calendars. Requires option CMYK enabled.', 'wcdp' )
		),
	    array(
		   'type' => 'media',
		   'library' => array('application/vnd.iccprofile','icc'),
		   'field' => 'profile_rgb',
		   'label' => __( 'profile', 'wcdp' ),
		   'title' => __( 'Select RGB profile:', 'wcdp' ),
		   'description' => __( 'Default profile', 'wcdp' ) . ' "sRGB-IEC61966-2.1"'
		),		
	    array(
		   'type' => 'media',
		   'library' => array('application/vnd.iccprofile','icc'),
		   'field' => 'profile_cmyk',
		   'label' => __( 'profile', 'wcdp' ),
		   'title' => __( 'Select CMYK profile:', 'wcdp' ),
		   'description' => __( 'Default profile', 'wcdp' ) .' "ISOcoated_v2_eci"'
		),
	    array(
		   'type' => 'text',
		   'field' => 'zip_name',
		   'title' => __( 'Zip output name:', 'wcdp' ),
		   'description' => __( 'Zip output name.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'output_svg',
		   'title' => __( 'Output SVG:', 'wcdp' ),
		   'description' => __( 'Include the SVG design in the output.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'add_svg_user',
		   'title' => __( 'Add SVG to the user downloads:', 'wcdp' ),
		   'description' => __( 'Include the SVG design for user downloads. Requires option Output SVG enabled.', 'wcdp' )
		),		
	    array(
		   'type' => 'checkbox',
		   'field' => 'output_pdf',
		   'title' => __( 'Output PDF:', 'wcdp' ),
		   'description' => __( 'Include the PDF design in the output. You can configure the size in the parameters.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'add_pdf_user',
		   'title' => __( 'Add PDF to the user downloads:', 'wcdp' ),
		   'description' => __( 'Include the PDF design for user downloads. Requires option Output PDF enabled.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'output_pdf_svg',
		   'title' => __( 'Output PDF with SVG:', 'wcdp' ),
		   'description' => __( 'Include the SVG design in the output PDF. Requires option Output PDF enabled.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'output_png',
		   'title' => __( 'Output PNG:', 'wcdp' ),
		   'description' => __( 'Include the PNG design in the output.', 'wcdp' )
		),		
	    array(
		   'type' => 'checkbox',
		   'field' => 'add_png_user',
		   'title' => __( 'Add PNG to the user downloads:', 'wcdp' ),
		   'description' => __( 'Include the PNG design for user downloads. Requires option Output PNG enabled.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'output_cmyk',
		   'title' => __( 'Output CMYK:', 'wcdp' ),
		   'description' => __( 'Includes the CMYK design in the output for the admin. Requires option CMYK enabled.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'output_cmyk_user',
		   'title' => __( 'Output CMYK to the user:', 'wcdp' ),
		   'description' => __( 'Includes the CMYK design in the output for the user. Requires option Output CMYK enabled.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'output_json',
		   'title' => __( 'Output JSON:', 'wcdp' ),
		   'description' => __( 'Includes the JSON design in the output for the admin.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'upload_svg',
		   'title' => __( 'Enable SVG image uploads to the user:', 'wcdp' ),
		   'description' => __( 'It allows users to upload SVG images in the editor. This option is disabled by default, for security reasons.', 'wcdp' )
		),
	    array(
		    'type' => 'text',
		    'field' => 'maps_api',			
		    'label' => '<a href="https://developers.google.com/maps/documentation/static-maps/" target="_blank">'.__( 'Get a key', 'wcdp' ).'</a>',
		    'title' => __( 'Static maps API:', 'wcdp' ),
		    'description' => __( 'Add required API key for the static maps section.', 'wcdp' )
		),
	    array(
		    'type' => 'text',
		    'field' => 'pixabay_api',			
		    'label' => '<a href="https://pixabay.com/api/docs/" target="_blank">'.__( 'Get a key', 'wcdp' ).'</a>',
		    'title' => __( 'Pixabay API:', 'wcdp' ),
		    'description' => __( 'Add the API key required for the image search in Pixabay.', 'wcdp' )
		),
	    array(
		    'type' => 'text',
		    'field' => 'unsplash_api',			
		    'label' => '<a href="https://unsplash.com/developers/" target="_blank">'.__( 'Get a key', 'wcdp' ).'</a>',
		    'title' => __( 'Unsplash API:', 'wcdp' ),
		    'description' => __( 'Add the API key required for the image search in Unsplash.', 'wcdp' )
		),
	    array(
		    'type' => 'text',
		    'field' => 'pexels_api',			
		    'label' => '<a href="https://www.pexels.com/api/" target="_blank">'.__( 'Get a key', 'wcdp' ).'</a>',
		    'title' => __( 'Pexels API:', 'wcdp' ),
		    'description' => __( 'Add the API key required for the image search in Pexels.', 'wcdp' )
		),
	    array(
		    'type' => 'text',
		    'field' => 'flaticon_api',			
		    'label' => '<a href="https://developer.flaticon.com/getstarted/" target="_blank">'.__( 'Get a key', 'wcdp' ).'</a>',
		    'title' => __( 'Flaticon API:', 'wcdp' ),
		    'description' => __( 'Add the API key required for the image search in Flaticon.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'flaticon_svg',
		   'title' => __( 'Add Flaticon icons in SVG:', 'wcdp' ),
		   'description' => __( 'Add Flaticon icons to the canvas in SVG format.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'sources_cmyk',
		   'title' => __( 'Convert resources in CMYK:', 'wcdp' ),
		   'description' => __( 'Convert images of Pixabay, Unsplash and Pexels in CMYK before adding it to the canvas. Requires option CMYK enabled.', 'wcdp' )		
		),
	    array(
		   'type' => 'select',
		   'field' => 'preload_fonts',
		   'title' => __( 'Preload fonts unicode:', 'wcdp' ),
		   'description' => __( 'Select an alphabet to preload it in fonts. This option is only necessary when working with fonts in the selected language.', 'wcdp' ),
		   'items' => array(
		        'none'   => __( 'None', 'wcdp' ),
                'arabic' => __( 'Arabic alphabet', 'wcdp' ),
				'latin'  => __( 'Latin extended alphabet', 'wcdp' )
		    )								
		),
	    array(
		   'type' => 'media',
		   'library' => array('image','gif'),
		   'field' => 'loader_gif',
		   'label' => __( 'image', 'wcdp' ),
		   'title' => __( 'Add loader image:', 'wcdp' ),
		   'description' => __( 'Image that reports when the editor is loading or an AJAX query.', 'wcdp' )
		),
	    array(
		   'type' => 'number',
		   'field' => 'loader_w',
		   'title' => __( 'Loader image width:', 'wcdp' ),
		   'label' => 'px',
		   'description' => __( 'Set loader image width.', 'wcdp' )
		),
	    array(
		   'type' => 'number',
		   'field' => 'loader_h',
		   'title' => __( 'Loader image height:', 'wcdp' ),
		   'label' => 'px',
		   'description' => __( 'Set loader image height.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'view_finder',
		   'title' => __( 'Hide add to cart button in the editor:', 'wcdp' ),
		   'description' => __( 'Hide the add to cart button and the product options tab in the editor.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'hide_addtocart',
		   'title' => __( 'Hide add to cart button for customized products:', 'wcdp' ),
		   'description' => __( 'Hide the add to cart button for customizable products in the product page.', 'wcdp' )
		),
	    array(
		   'type' => 'number',
		   'field' => 'number_save_user',
		   'title' => __( 'Number of designs the user can save:', 'wcdp' ),
		   'label' => 'Set number',
		   'description' => __( 'Number of designs the user can save in the editor. Default empty can save unlimited designs.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'download_design_editor',
		   'title' => __( 'Download design from the editor:', 'wcdp' ),
		   'description' => __( 'Allows users to download the design from the editor.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'download_design_cart',
		   'title' => __( 'Download design from shopping cart:', 'wcdp' ),
		   'description' => __( 'Allows users to download the design from the shopping cart.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'download_design_logged',
		   'title' => __( 'Download design only to user logged:', 'wcdp' ),
		   'description' => __( 'Allow the download of design from the editor and the shopping cart only to user logged.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'download_design_order',
		   'title' => __( 'Download design in the order', 'wcdp' ),
		   'description' => __( 'Allows users to download the design in the order.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'mail_order_complete',
		   'title' => __( 'Add link to email for download the design after finishing the order:', 'wcdp' ),
		   'description' => __( 'Allows the user to download the design from the order email.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'mail_status_complete',
		   'title' => __( 'Add link to email for download the design after change in the order status to completed:', 'wcdp' ),
		   'description' => __( 'Allows the user to download the design from the order email when changing the status to completed.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'confirm_box',
		   'title' => __( 'Add confirmation box to review design:', 'wcdp' ),
		   'description' => __( 'Confirmation required to review the design before adding to cart.', 'wcdp' )
		),
	    array(
		   'type' => 'textarea',
		   'field' => 'text_confirm_box',
		   'title' => __( 'Text for confirmation box:', 'wcdp' ),
		   'description' => __( 'Example: Please review your design carefully. Check that the design does not contain spelling errors, etc.', 'wcdp' )
		),
	    array(
		   'type' => 'text',
		   'field' => 'label_confirm_box',
		   'title' => __( 'Label for confirmation box:', 'wcdp' ),
		   'description' => __( 'Example: I have reviewed the design and I give my confirmation.', 'wcdp' )
		),
	    array(
		   'type' => 'button',
		   'label' => __( 'Restore defaults', 'wcdp' ),
		   'field' => 'restore-all-defaults',
		   'title' => __( 'Restore all defaults settings:', 'wcdp' ),
		   'description' => __( 'Careful this option will erase all your saved settings.', 'wcdp' )
		)		
	);
	foreach($general_settings as $setting){
		$label = '';
		$items = '';
		$library = '';
		if(isset($setting['label'])) $label = $setting['label'];
		if(isset($setting['items'])) $items = $setting['items'];
		if(isset($setting['library'])) $library = $setting['library'];
	    add_settings_field('wcdp_option_'.$setting['field'],
	                        $setting['title'],
		                    'wcdp_admin_options_html_settings',
		                    'wcdp-menu-general',
		                    'wcdp_general_settings',
		                    array( 
							    'settings'    => 'wcdp-settings-general',
							    'type'        => $setting['type'],
		                        'field'       => $setting['field'],
								'label'       => $label,
								'items'       => $items,
								'library'     => $library,
		                        'description' => $setting['description']
		                    )
        );		
	}

    // Section shortcut keys
	$sc_transl = wcdp_shortcuts_translations();
	$shortcutkeys = array(
	    array(
		   'field' => 'moveup',
		   'title' => $sc_transl['moveup'].':',
		   'description' => __( 'Move objects up.', 'wcdp' )
		),
		array(
		   'field' => 'movedown',
		   'title' => $sc_transl['movedown'].':',
		   'description' => __( 'Move objects down.', 'wcdp' )
		),
		array(
		   'field' => 'moveleft',
		   'title' => $sc_transl['moveleft'].':',
		   'description' => __( 'Move objects left.', 'wcdp' )
		),
		array(
		   'field' => 'moveright',
		   'title' => $sc_transl['moveright'].':',
		   'description' => __( 'Move objects right.', 'wcdp' )
		),		
		array(
		   'field' => 'select_all',
		   'title' =>  $sc_transl['select_all'].':',
		   'description' => __( 'Select all objects.', 'wcdp' )
		),
		array(
		   'field' => 'erase_all',
		   'title' =>  $sc_transl['erase_all'].':',
		   'description' => __( 'Erase all objects.', 'wcdp' )
		),
		array(
		   'field' => 'grid',
		   'title' =>  $sc_transl['grid'].':',
		   'description' => __( 'Show/Hide grid.', 'wcdp' )
		),
		array(
		   'field' => 'center_vertical',
		   'title' =>  $sc_transl['center_vertical'].':',
		   'description' => __( 'Center objects vertically.', 'wcdp' )
		),
		array(
		   'field' => 'center_horizontal',
		   'title' =>  $sc_transl['center_horizontal'].':',
		   'description' => __( 'Center objects horizontally.', 'wcdp' )
		),
		array(
		   'field' => 'flip_vertical',
		   'title' =>  $sc_transl['flip_vertical'].':',
		   'description' => __( 'Flip objects vertically.', 'wcdp' )
		),
		array(
		   'field' => 'flip_horizontal',
		   'title' =>  $sc_transl['flip_horizontal'].':',
		   'description' => __( 'Flip objects horizontally.', 'wcdp' )
		),		
		array(
		   'field' => 'bring_front',
		   'title' =>  $sc_transl['bring_front'].':',
		   'description' => __( 'Bring object to front.', 'wcdp' )
		),
		array(
		   'field' => 'send_back',
		   'title' =>  $sc_transl['send_back'].':',
		   'description' => __( 'Bring object to back.', 'wcdp' )
		),
		array(
		   'field' => 'lock',
		   'title' =>  $sc_transl['lock'].':',
		   'description' => __( 'Lock and unlock object.', 'wcdp' )
		),
		array(
		   'field' => 'duplicate',
		   'title' =>  $sc_transl['duplicate'].':',
		   'description' => __( 'Duplicate objects.', 'wcdp' )
		),
		array(
		   'field' => 'clone_sides',
		   'title' =>  $sc_transl['clone_sides'].':',
		   'description' => __( 'Duplicate objects from the front side to the back side. If there is only one side it will be deactivated.', 'wcdp' )
		),
		array(
		   'field' => 'magic_more',
		   'title' =>  $sc_transl['magic_more'].':',
		   'description' => __( 'Align selected objects vertically with more space between them.', 'wcdp' )
		),
		array(
		   'field' => 'magic_less',
		   'title' =>  $sc_transl['magic_less'].':',
		   'description' => __( 'Align selected objects vertically with less space between them.', 'wcdp' )
		),
		array(
		   'field' => 'align_left',
		   'title' =>  $sc_transl['align_left'].':',
		   'description' => __( 'Align objects to left.', 'wcdp' )
		),
		array(
		   'field' => 'align_right',
		   'title' =>  $sc_transl['align_right'].':',
		   'description' => __( 'Align objects to right.', 'wcdp' )
		),
		array(
		   'field' => 'rotate',
		   'title' =>  $sc_transl['rotate'].':',
		   'description' => __( 'Rotate objects.', 'wcdp' )
		),
		array(
		   'field' => 'return_state',
		   'title' =>  $sc_transl['return_state'].':',
		   'description' => __( 'Returns the objects to their original state.', 'wcdp' )
		),
		array(
		   'field' => 'align_vertical',
		   'title' =>  $sc_transl['align_vertical'].':',
		   'description' => __( 'Align selected objects vertically between them.', 'wcdp' )
		),
		array(
		   'field' => 'align_horizontal',
		   'title' =>  $sc_transl['align_horizontal'].':',
		   'description' => __( 'Align selected objects horizontally between them.', 'wcdp' )
		),
		array(
		   'field' => 'group',
		   'title' =>  $sc_transl['group'].':',
		   'description' => __( 'Group and ungroup objects.', 'wcdp' )
		),
		array(
		   'field' => 'delete',
		   'title' =>  $sc_transl['delete'].':',
		   'description' => __( 'Delete objects.', 'wcdp' )
		),		
		array(
		   'field' => 'undo',
		   'title' =>  $sc_transl['undo'].':',
		   'description' => __( 'Undo changes.', 'wcdp' )
		),
		array(
		   'field' => 'redo',
		   'title' =>  $sc_transl['redo'].':',
		   'description' => __( 'Redo changes.', 'wcdp' )
		),		
		array(
		   'field' => 'line_text_small',
		   'title' =>  $sc_transl['line_text_small'].':',
		   'description' => __( 'Height of the smallest text line.', 'wcdp' )
		),
		array(
		   'field' => 'line_text_large',
		   'title' =>  $sc_transl['line_text_large'].':',
		   'description' => __( 'Height of the larger text line.', 'wcdp' )
		)		
	);
	foreach($shortcutkeys as $shortcutkey){
		$type = "shortcutkeys";
		if(isset($shortcutkey['type'])) $type = $shortcutkey['type'];
	    add_settings_field('wcdp_option_'.$shortcutkey['field'],
	                        $shortcutkey['title'],
		                    'wcdp_admin_options_html_settings',
		                    'wcdp-menu-shortcut-keys',
		                    'wcdp_shortcut_keys_settings',
		                    array( 
							    'settings'    => 'wcdp-settings-shortcutkeys',
							    'type'        => $type,
		                        'field'       => $shortcutkey['field'],
		                        'description' => $shortcutkey['description']
		                    )
        );		
	}
	
    // Section style
    $style_settings = array(
	    array(
		   'type' => 'select',
		   'field' => 'skin_style',
		   'title' => __( 'Skin predesigned colors:', 'wcdp' ),
		   'description' => __( "Select a option and save the changes, to apply the predesigned colors.", "wcdp" ),
		   'items' => array(
                'default'      => __( 'Default', 'wcdp' ),
				'gray-blue'    => __( 'Gray & Blue', 'wcdp' ),
				'green-coral'  => __( 'Green & Coral', 'wcdp' ),
				'blue-orange'  => __( 'Blue & Orange', 'wcdp' ),
				'violet-blue'  => __( 'Violet & Dark blue', 'wcdp' ),
				'black-red'    => __( 'Black & Red', 'wcdp' )
				
		    )	
		),
	    array(
		   'type' => 'colors',
		   'field' => 'color_icons_section',
		   'title' => __( 'Icons color:', 'wcdp' ),
		   'data' => array( 
		       array( 'field' => 'color_icons', 'label' => __( 'Icons', 'wcdp' ) ),
			   array( 'field' => 'color_icons_hover', 'label' => __( 'Icons hover', 'wcdp' ) ),
		       array( 'field' => 'bg_icons', 'label' => __( 'Icons BG', 'wcdp' ) ),
			   array( 'field' => 'bg_icons_hover', 'label' => __( 'Icons BG hover', 'wcdp' ) )
           )		   
		),
	    array(
		   'type' => 'colors',
		   'field' => 'buttons_color_section',
		   'title' => __( 'Buttons color:', 'wcdp' ),
		   'data' => array( 
			   array( 'field' => 'buttons_color', 'label' => __( 'Buttons', 'wcdp' ) ),
			   array( 'field' => 'buttons_color_hover', 'label' => __( 'Buttons hover', 'wcdp' ) ),
			   array( 'field' => 'buttons_bg', 'label' => __( 'Buttons BG', 'wcdp' ) ),
			   array( 'field' => 'buttons_bg_hover', 'label' => __( 'Buttons BG hover', 'wcdp' ) )
           )		   
		),
	    array(
		   'type' => 'colors',
		   'field' => 'buttons_jbox_color_section',
		   'title' => __( 'Buttons color box messages:', 'wcdp' ),
		   'data' => array( 
			   array( 'field' => 'buttons_color_jbox', 'label' => __( 'Buttons', 'wcdp' ) ),
			   array( 'field' => 'buttons_color_hover_jbox', 'label' => __( 'Buttons hover', 'wcdp' ) ),
			   array( 'field' => 'buttons_bg_jbox', 'label' => __( 'Buttons BG', 'wcdp' ) ),
			   array( 'field' => 'buttons_bg_hover_jbox', 'label' => __( 'Buttons BG hover', 'wcdp' ) )
           )		   
		),
	    array(
		   'type' => 'colors',
		   'field' => 'buttons_folder_section',
		   'title' => __( 'Buttons color folders:', 'wcdp' ),
		   'data' => array( 
			   array( 'field' => 'buttons_color_folders', 'label' => __( 'Folders', 'wcdp' ) ),
			   array( 'field' => 'buttons_color_folders_select', 'label' => __( 'Folders select', 'wcdp' ) ),
			   array( 'field' => 'buttons_color_folders_bg', 'label' => __( 'Buttons BG', 'wcdp' ) ),			   
			   array( 'field' => 'buttons_color_folders_bg_select', 'label' => __( 'Buttons BG select', 'wcdp' ) )
           )		   
		),
	    array(
		   'type' => 'colors',
		   'field' => 'tabs_color_section',
		   'title' => __( 'Tabs color:', 'wcdp' ),
		   'data' => array( 
		       array( 'field' => 'text_color', 'label' => __( 'Tabs Text', 'wcdp' ) ),
		       array( 'field' => 'tabs_bg', 'label' => __( 'Tabs BG', 'wcdp' ) ),
			   array( 'field' => 'tabs_content', 'label' => __( 'Tabs Content', 'wcdp' ) ),
           )		   
		),
	    array(
		   'type' => 'colors',
		   'field' => 'tooltip_color_section',
		   'title' => __( 'Tooltip color:', 'wcdp' ),
		   'data' => array( 
		       array( 'field' => 'tooltip_color', 'label' => __( 'Tooltip', 'wcdp' ) ),
		       array( 'field' => 'tooltip_bg', 'label' => __( 'Tooltip BG', 'wcdp' ) )
           )		   
		),
	    array(
		   'type' => 'colors',
		   'field' => 'scrollbar_color_section',
		   'title' => __( 'Scroll bar color:', 'wcdp' ),
		   'data' => array( 
		       array( 'field' => 'scrollbar_bg', 'label' => __( 'Background', 'wcdp' ) ) 
           )		   
		),
	    array(
		   'type' => 'number',
		   'field' => 'tooltip_offset_x',
		   'title' => __( 'Tooltip offset x:', 'wcdp' ),
		   'description' => __( 'Tooltip position horizontally. Default 0.', 'wcdp' )
		),
	    array(
		   'type' => 'number',
		   'field' => 'tooltip_offset_y',
		   'title' => __( 'Tooltip offset y:', 'wcdp' ),
		   'description' => __( 'Tooltip position vertically. Default -5.', 'wcdp' )
		),		
	    array(
		   'type' => 'colors',
		   'field' => 'colors_borders_section',
		   'title' => __( 'Border colors:', 'wcdp' ),
		   'data' => array( 
		       array( 'field' => 'border_color', 'label' => __( 'Skin', 'wcdp' ) ),
			   array( 'field' => 'border_bleed_color', 'label' => __( 'Bleed area', 'wcdp' ) )
           )		   
		),		
	    array(
		   'type' => 'colors',
		   'field' => 'colors_picker_section',
		   'title' => __( 'Color picker colors:', 'wcdp' ),
		   'data' => array( 
		       array( 'field' => 'picker_color_bg', 'label' => __( 'Background', 'wcdp' ) ),
			   array( 'field' => 'picker_color_border', 'label' => __( 'Border', 'wcdp' ) ),
			   array( 'field' => 'picker_color_text', 'label' => __( 'Text', 'wcdp' ) ),
           )		   
		),		
	    array(
		   'type' => 'colors',
		   'field' => 'colors_editor_section',
		   'title' => __( 'Colors default in editor:', 'wcdp' ),
		   'data' => array( 
		       array( 'field' => 'text_color_editor', 'label' => __( 'Text', 'wcdp' ) ),
			   array( 'field' => 'text_color_editor_outline', 'label' => __( 'Text outline', 'wcdp' ) ),
		       array( 'field' => 'color_shapes_editor', 'label' => __( 'Shapes', 'wcdp' ) ),
			   array( 'field' => 'color_shapes_editor_outline', 'label' => __( 'Shapes outline', 'wcdp' ) ),
			   array( 'field' => 'color_qr_editor', 'label' => __( 'QR', 'wcdp' ) ),
		       array( 'field' => 'bg_color_qr_editor', 'label' => __( 'QR BG', 'wcdp' ) ),
			   array( 'field' => 'map_color_ico', 'label' => __( 'Map icon', 'wcdp' ) )
           )		   
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'auto_bleed_color',
		   'title' => __( 'Automatic bleed area color:', 'wcdp' ),
		   'description' => __( 'Enable / Disable display color of the light bleed area on dark backgrounds.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'auto_snap',
		   'title' => __( 'Auto snap:', 'wcdp' ),
		   'description' => __( 'Enable / Disable auto snap mode.', 'wcdp' )
		),			
	    array(
		   'type' => 'number',
		   'field' => 'snap_tolerance',
		   'title' => __( 'Auto snap tolerance:', 'wcdp' ),
		   'description' => __( "Tolerance when lock the objects with the auto snap. Default 5.", "wcdp" )
		),			
	    array(
		   'type' => 'colors',
		   'field' => 'snap_color_section',
		   'title' => __( 'Auto snap color:', 'wcdp' ),
		   'data' => array( 
		       array( 'field' => 'snap_color_border', 'label' => __( 'Guides', 'wcdp' ) )
           )		   
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'centered_scaling',
		   'title' => __( 'Centered scaling:', 'wcdp' ),
		   'description' => __( 'Enable / Disable centered scaling for objects.', 'wcdp' )
		),	
	    array(
		   'type' => 'checkbox',
		   'field' => 'obj_center',
		   'title' => __( 'Add centered objects:', 'wcdp' ),
		   'description' => __( 'Enable / Disable to add the objects centered on canvas.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'corners_outside',
		   'title' => __( 'Corners outside box:', 'wcdp' ),
		   'description' => __( "Enable / Disable corners outside the object's controlling box.", "wcdp" )
		),		
	    array(
		   'type' => 'checkbox',
		   'field' => 'hide_middle_corners',
		   'title' => __( 'Hide middle corners:', 'wcdp' ),
		   'description' => __( "Hide the middle scaling corners of object's controlling box.", "wcdp" )
		),
	    array(
		   'type' => 'number',
		   'field' => 'corner_size',
		   'title' => __( 'Corners size:', 'wcdp' ),
		   'description' => __( "Size of object's controlling corners. Default 20.", "wcdp" )
		),	
	    array(
		   'type' => 'select',
		   'field' => 'corner_style',
		   'title' => __( 'Corners style:', 'wcdp' ),
		   'description' => __( "Style of object's controlling corners.", "wcdp" ),
		   'items' => array(
                'rect'   => __( 'Rect', 'wcdp' ),
				'circle' => __( 'Circle', 'wcdp' )
		    )	
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'border_corners',
		   'title' => __( 'Corners border:', 'wcdp' ),
		   'description' => __( "Enable / Disable to add borders the object's control corners.", "wcdp" )
		),			
	    array(
		   'type' => 'colors',
		   'field' => 'corner_color_section',
		   'title' => __( 'Corners color:', 'wcdp' ),
		   'data' => array( 
		       array( 'field' => 'corner_color', 'label' => __( 'Corners', 'wcdp' ) ),
			   array( 'field' => 'corner_border_color', 'label' => __( 'Corners border', 'wcdp' ) ),
			   array( 'field' => 'corner_icons_color', 'label' => __( 'Icons color', 'wcdp' ) )
           )		   
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'popup_show',
		   'title' => __( 'Show pop-up thumbnails:', 'wcdp' ),
		   'description' => __( 'Enable / Disable pop-up for preview the thumbnails.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'hide_notes',
		   'title' => __( 'Hide note boxes:', 'wcdp' ),
		   'description' => __( 'Hide the note boxes of the QR Code & Map tabs.', 'wcdp' )
		),
	    array(
		   'type' => 'checkbox',
		   'field' => 'show_picker_palette',
		   'title' => __( 'Show picker palette:', 'wcdp' ),
		   'description' => __( 'Enable / Disable colors picker palette.', 'wcdp' )
		),
	    array(
		   'type' => 'number',
		   'field' => 'column_picker_palette',
		   'title' => __( 'Columns for colors picker palette:', 'wcdp' ),
		   'description' => __( 'Number of columns that will display the colors of the picker palette. Default 3 colums.', 'wcdp' )
		),
	    array(
		   'type' => 'colors',
		   'field' => 'picker_palette',
		   'label' => __( 'Add color picker', 'wcdp' ),
		   'title' => __( 'Customize the color picker:', 'wcdp' )
		),
	    array(
		   'type' => 'colors',
		   'field' => 'background_palette',
		   'label' => __( 'Add color background', 'wcdp' ),
		   'title' => __( 'Customize the background color section:', 'wcdp' )
		)
    );
    foreach($style_settings as $style){
		$data  = '';
		$label = ''; 
		$items = '';
		$desc  = '';
		if(isset($style['data'])) $data = $style['data'];
		if(isset($style['label'])) $label = $style['label'];
		if(isset($style['items'])) $items = $style['items'];
		if(isset($style['description'])) $desc = $style['description'];		 
	    add_settings_field('wcdp_option_'.$style['field'],
	                        $style['title'],
		                    'wcdp_admin_options_html_settings',
		                    'wcdp-menu-style',
		                    'wcdp_style_settings',
		                    array(
							    'settings'    => 'wcdp-settings-style',
							    'type'        => $style['type'],									
								'field'       => $style['field'],
								'data'        => $data,
								'label'       => $label,
							    'items'       => $items,
								'description' => $desc
		                    )
        );
	}
	
	// Section license
	$license_settings = array(
	    array(
		   'field' => 'envato_username',
		   'title' => __( 'Envato Username:', 'wcdp' ),
		   'description' => __( 'Add envato username.', 'wcdp' )
		),			
	    array(
		   'field' => 'envato_api_key',
		   'title' => __( 'Token Key:', 'wcdp' ),
		   'description' => __( 'How to get an Token Key? follow the instructions in the', 'wcdp' ) .' '. '<a href="'. WCDP_URL .'/user-manual/#!/license" target="_blank">'. __( 'User Manual', 'wcdp' ) .'</a> '. __( 'on the license tab.', 'wcdp' )
		),
	    array(
		   'field' => 'envato_purchase_code',
		   'title' => __( 'Purchase Code:', 'wcdp' ),
		   'description' => __( 'Where is my Purchase Code? follow the instructions of', 'wcdp' ) .' '. '<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code" target="_blank">Envato</a>.'
		)
	);
	foreach($license_settings as $license){
	    add_settings_field('wcdp_option_'.$license['field'],
	                        $license['title'],
		                    'wcdp_admin_options_html_settings',
		                    'wcdp-menu-license',
		                    'wcdp_license_section',
		                    array( 
							    'settings'    => 'wcdp-settings-license',
							    'type'        => 'text',
		                        'field'       => $license['field'],
		                        'description' => $license['description'],
								'label'       => ''
		                    )
        );		
	}	
}
add_action('admin_init', 'wcdp_general_settings_section');

// Add content types html options
function wcdp_admin_options_html_settings($args){	
	$html     = '';
	$style    = '';
	$desc     = '';
	$valField = '';
	$checked  = '';
	$filename = '';
	$disabled = '';
	$type     = $args['type'];
	$field    = $args['field'];
	$settings = $args['settings'];
	$options  = get_option($settings);	
	
	if(isset($args['data'])) $data = $args['data'];
	if(isset($args['items'])) $items = $args['items'];	
	if(isset($args['label'])) $label = $args['label'];
    if(isset($args['library'])) $library = $args['library'];	
	if(isset($args['description'])) $desc = $args['description'];
    if(isset($options[$field])) $valField = $options[$field];
	
	if($type == 'dropdown'){	   
        wp_dropdown_pages( array(
		    'depth' => -1,
		    'selected' => $valField,
		    'name'=> $settings . '['.$field.']'
        ));   
    } 
	else if($type == 'checkbox'){	   
	    if($field == 'CMYK'){
		    $style = 'style="color:green"';
		    $desc = __( 'ImageMagick is installed.', 'wcdp' );
            if(!extension_loaded('imagick')){
			    $style = 'style="color:red"';
			    $desc = __( 'ImageMagick is not installed.', 'wcdp' );
            }
	    }	   
	    if(true == $valField) $checked = ' checked="checked" ';
        $html  ="<label for='wcdp_option_".$field."'>";
		$html .="<input ".$checked." id='wcdp_option_" . $field . "' name='" . $settings . "[" . $field . "]' type='checkbox' />" . __( 'Enable', 'wcdp' );
		$html .="</label>";
    } 
	else if($type == 'select'){
	    $html = "<select id='wcdp_option_".$field."' name='" . $settings . "[" . $field . "]'>";
	    foreach($items as $key => $value){
		    $selected = ($valField == $key) ? 'selected="selected"' : '';
		    $html .= "<option value='$key' $selected>$value</option>";
	    }
		$html .= "</select>";
    } 
	else if($type == 'media'){
	    wp_enqueue_media();
        if(!empty($valField)) $filename = basename( $valField ); 
		?>
	    <div id="contain_wcdp_option_<?php echo $field; ?>" format="<?php echo $library[0]; ?>" support="<?php echo $library[1]; ?>">
            <input <?php if($valField != ''){ ?> style="display:none" <?php } ?> type="button" class="wcdp-select-file button" value="<?php printf( __( 'Upload %s', 'wcdp' ), $label ); ?> " />
	        <input <?php if($valField == ''){ ?> style="display:none" <?php } ?> type="button" class="wcdp-remove-file button<?php if($library[1] == 'icc') echo ' dp-icc'; ?>" value="<?php printf( __( 'Remove %s', 'wcdp' ), $label ); ?> " />
            <input class="value-file" type="hidden" name="<?php echo $settings.'['.$field.']'; ?>" id="wcdp_option_<?php echo $field; ?>" value="<?php echo $valField; ?>">
	        <div class="media-filename"><?php echo $filename ?></div>
	    </div>
		<?php	
	} 
	else if($type == 'shortcutkeys'){
        if(isset($options[$field.'_ck']) && (true == $options[$field.'_ck'])) $checked = ' checked="checked" ';
	    $html  ="<input type='text' spellcheck='false' class='dp-shortcutkeys'>";
	    $html .="<input type='hidden' name='" . $settings . "[". $field . "]' value='".$valField."'>";		    
        $html .="<label class='dp-ckbox' for='wcdp_option_".$field."'>";
		$html .="<input type='checkbox' ".$checked." name='" . $settings . "[". $field . "_ck]' id='wcdp_option_" . $field . "' />" . __( 'Enable', 'wcdp' );
		$html .="</label>";       		
	} 
	else if($type == 'colors'){
		$general = get_option('wcdp-settings-general');
		$CMYK = (isset($general['CMYK']) && $general['CMYK'] == 'on') ? 'on':'off';
	    if($data){
		    foreach($data as $item){
			    if(isset($options[$item['field']])){
					$valField = $options[$item['field']];
				} else if($item['field'] == 'snap_color_border'){
					$valField = array('RGB' => '#e10886', 'CMYK' => '0,100,0,0');
				} else{
					$valField = array('RGB' => '#ffffff', 'CMYK' => '0,0,0,0');
				}
			    $CMYK == 'on' ? $valueCMYK = $valField['CMYK'] : $valueCMYK = '0,0,0,0';
		        $html .="<div class='wcdp-colors-options'><p>". $item['label'] ."</p>";
		        $html .="<input type='text' class='spectrum-js' name='" . $settings . "[" . $item['field'] . "][RGB]' cmyk='" . $valueCMYK . "' value='".$valField['RGB']."'>";
			    $html .="<input type='hidden' name='" . $settings . "[". $item['field'] . "][CMYK]' value='".$valueCMYK."'>";
		        $html .="</div>";	
		    }
		} else{
		    $html .= "<div class='wcdp-colors-palette'>";
		    if(!empty($valField)){
                foreach($valField['RGB'] as $index => $value){
			        $CMYK == 'on' ? $valueCMYK = $valField['CMYK'][$index] : $valueCMYK = '0,0,0,0';
		            $html .="<div class='color'>";			 
		            $html .="<input type='text' class='spectrum-js' name='" . $settings . "[". $field . "][RGB][]' cmyk='" . $valueCMYK . "' value='" . $valField['RGB'][$index] . "'>";
			        $html .="<input type='hidden' name='" . $settings . "[". $field . "][CMYK][]' value='" . $valueCMYK . "'>";
		            $html .="<button class='button wcdp-remove-color'>". __( 'Remove', 'wcdp' ) ."</button>";
		            $html .="</div>";
		        }
            }
            $html .="</div>";		
            $html .="<button data='" . $field . "' class='button wcdp-add-color-palette'>" . $label. "</button>";	
		}	   
	} 
	else if($type == 'button'){
	    $html .="<button id='wcdp-" . $field . "' class='button'>" . $label. "</button>"; 
	} 
	else if($type == 'text' || $type == 'number'){
	    $html .="<label><input id='wcdp_option_" . $field . "' name='" . $settings . "[" . $field . "]' value='".$valField."' type='".$type."'> ".$label."</label>";
	} 
	else if($type == 'textarea'){
        wp_editor( $valField, $field,  array(
            'wpautop'       => false,
		    'textarea_rows' => 10,
            'media_buttons' => false,
            'quicktags'     => false,
            'textarea_name' => $settings.'['.$field.']',
		    'tinymce'       => array(
                'toolbar1' => 'formatselect,|,bold, italic,,strikethrough,underline,|,forecolor,|,bullist,numlist,|,alignleft,aligncenter,alignright,alignjustify,|,undo,redo',
                'toolbar2' => '',
            )
		));
	}
	if(!empty($desc))
	    $html .= '<p class="dp description" '. $style . '>'. $desc. '</p>';
	
	echo $html;
}