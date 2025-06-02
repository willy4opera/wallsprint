<?php
/*
 * Save design files by ajax for modes:
 * Publish admin | Save user & admin | Add to cart.
 * Output IMG & CMYK | SVG | PDF | JSON
 */

function wcdp_save_canvas_design_ajax(){
    $pr = json_decode(stripslashes($_POST['params']), true);
	$userID = is_user_logged_in() ? get_current_user_id() : false;
	$response = array('userID' => $userID, 'filesCMYK' => array());
	
	if($pr['mode'] == 'addtocart'){
		$folder = '/temp/'. $pr['uniq'];
	}
	else if($pr['mode'] == 'publish' || $pr['mode'] == 'save'){
		if($pr['editor'] == 'frontend')
			$folder = ($userID ? '/save-user/' : '/temp/') . $pr['uniq'];
		else
			$folder = '/save-admin/designID'. $pr['designID'];
	}
	$url_path = WCDP_PATH_UPLOADS . $folder;

    if(!file_exists($url_path))
	    mkdir($url_path, 0755, true);

	// Upload output files
	$result = 0;
	foreach($pr['files'] as $file){
	    $count = $file['count'];
		$name = $file['name'];
		$ext = $file['ext'];
        $fileTEMP = $_FILES[$count]['tmp_name'];
        if(!empty($fileTEMP))
		    $result = move_uploaded_file($fileTEMP, $url_path .'/'. $name . ($ext == 'CMYK' ? '_CMYK.jpg' : '.'. $ext));

	    if(!$result)
		    break;
			
		// Convert image to CMYK
		if($ext == 'CMYK'){
			if(!isset($general))
    	        $general = get_option('wcdp-settings-general');

		    $sideURL = $url_path .'/'. $name .'_CMYK.jpg';
            if(isset($general['CMYK']) && $general['CMYK'] == 'on' && isset($general['output_cmyk']) && $general['output_cmyk'] == 'on'){
			    if(!isset($prfl))
				    $prfl = wcpd_get_imagick_profiles();

                if(isset($general['imagick_cmd']) && $general['imagick_cmd'] == 'on'){
				    exec("convert -units PixelsPerInch ". $sideURL ." -density 300 -profile ". $prfl['RGB'] ." -profile ". $prfl['CMYK'] ." ". $sideURL ." 2>&1");
		        }	
                else if(extension_loaded('imagick')){
			        if(!isset($pflRGB))
					    $pflRGB = file_get_contents($prfl['RGB']);

					if(!isset($pflCMYK))
			            $pflCMYK = file_get_contents($prfl['CMYK']);

		            $im = new Imagick($sideURL);
			        $im->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
                    $im->setImageResolution(300,300); 
				    $im->resampleImage(300,300,imagick::FILTER_UNDEFINED,0);
			        $im->setImageFormat('jpeg');
                    $im->setImageCompression(Imagick::COMPRESSION_JPEG);
                    $im->setImageCompressionQuality(100);
			        $im->profileImage('icc', $pflRGB); 
                    $im->profileImage('icc', $pflCMYK);
		    	    $im->writeImage($sideURL);
			        $im->clear();
                    $im->destroy();			
			    }
			    $response['filesCMYK'][] = array(
			        'url' => WCDP_URL_UPLOADS . $folder,
                    'name' => $name .'_CMYK.jpg'
			    );
			}
		}
	}
	// Add product to cart
	if($result && $pr['productData'] && empty($response['filesCMYK'])){
	    $result = 0;
	    global $woocommerce;
	    $cart_item_data = array(
		    'wcdp_product_data' => array(
			    'uniq'        => $pr['uniq'],
				'product_id'  => $pr['productID'],
				'design_id'   => $pr['designID'],
				'user'        => $pr['uniqZipUser'],
		        'admin'       => $pr['uniqZipAdmin'],
                'zip_name'	  => $pr['zipName'],
				'design_name' => isset($pr['designName']) ? $pr['designName'] : 0,
				'attr_data'   => isset($pr['attrData']) ? $pr['attrData'] : 0
			)
	    );
		if($woocommerce->cart->add_to_cart($pr['productID'], $pr['productData']['qty'], $pr['productData']['variation_id'], $pr['productData']['attributes'], $cart_item_data)){
			$response['cart_url'] = get_permalink(get_option('woocommerce_cart_page_id'));
			$result = true;
		}
	    // Delete temp files
	    if($pr['addCMYK']){
            wcdp_delete_temp_files($url_path);
		}
	}
	// Save designs list
	if($result && $pr["saveList"]){
		$designName = isset($pr['designName']) ? $pr['designName'] : 0;
		$attributes = isset($pr['attrData']) ? $pr['attrData'] : 0;
        if($userID){
            wcdp_save_user_designs_list($userID, $pr['uniq'], $pr['productID'], $pr['designID'], $designName, $attributes, false);	
		} else{
			if(!isset($_SESSION['wcdp_save_design_temp'])){
		        $_SESSION['wcdp_save_design_temp'] = array('uniq' => $pr['uniq'], 'productID' => $pr['productID'], 'designID' => $pr['designID'], 'designName' => $designName, 'attrData' => $attributes);
			}
			$response['wc_myaccount'] = get_permalink(get_option('woocommerce_myaccount_page_id'));	
		}
	}
	$response['success'] = $result;
    echo json_encode($response);
    exit;
}
add_action( 'wp_ajax_nopriv_wcdp_save_canvas_design_ajax','wcdp_save_canvas_design_ajax' );
add_action( 'wp_ajax_wcdp_save_canvas_design_ajax', 'wcdp_save_canvas_design_ajax' );

// Save design after logging in or new account
function wcdp_save_user_design_after_login($userID){
	if(isset($_SESSION['wcdp_save_design_temp'])){
		$designName = isset($_SESSION['wcdp_save_design_temp']['designName']) ? $_SESSION['wcdp_save_design_temp']['designName'] : 0;
		$attributes = isset($_SESSION['wcdp_save_design_temp']['attrData']) ? $_SESSION['wcdp_save_design_temp']['attrData'] : 0;
	    wcdp_save_user_designs_list($userID, $_SESSION['wcdp_save_design_temp']['uniq'], $_SESSION['wcdp_save_design_temp']['productID'], $_SESSION['wcdp_save_design_temp']['designID'], $designName, $attributes, true);
		unset($_SESSION['wcdp_save_design_temp']);
    }
}
add_action('user_register', function($userID){ wcdp_save_user_design_after_login($userID); }, 10, 1);
add_action('wp_login', function($user_login, $user){ wcdp_save_user_design_after_login($user->ID); }, 10, 2);

// Save designs list of the user
function wcdp_save_user_designs_list($userID, $uniq, $product_id, $design_id, $design_name, $attributes, $after_login){
	$save_design = true;
	$get_designs_list = get_user_meta($userID, '_wcdp_designs_save_user_list', true);
	if(empty($get_designs_list)){
		$get_designs_list = array();
	}
	if($after_login){
		$general = get_option('wcdp-settings-general');
	    $save_max = isset($general['number_save_user']) && !empty($general['number_save_user']) ? $general['number_save_user'] : 'unlimited';
		if($save_max == 'unlimited' || count($get_designs_list) < $save_max){
			$result = 0;
		    $target = WCDP_PATH_UPLOADS .'/temp/'. $uniq;
		    if(file_exists($target)){
				$result = rename($target, WCDP_PATH_UPLOADS .'/save-user/'. $uniq);
		    }
			$save_design = $result ? true : false;
		} else{
			$save_design = false;
		}
	}
	if($save_design){
	    $get_designs_list[$uniq] = array('productID' => $product_id, 'designID' => $design_id, 'designName' => $design_name, 'attrData' => $attributes);
	    update_user_meta($userID, '_wcdp_designs_save_user_list', $get_designs_list);
	}
}