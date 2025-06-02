<?php

// Uploads images since editor frontend
function wcdp_upload_img_file_ajax(){
	$general = get_option('wcdp-settings-general');
    $filename = $_FILES['file']['name'];
    $type = pathinfo($filename, PATHINFO_EXTENSION);
	$ext = strtolower($type);
    $fileUniq = '/temp/img_file_'. uniqid().rand(0,10) .'.'. $ext;
	$fileURL = WCDP_URL_UPLOADS . $fileUniq;
    $filePATH = WCDP_PATH_UPLOADS . $fileUniq;    
    $fileTEMP = $_FILES['file']['tmp_name'];
	$imgTypes = array('jpg','jpeg','png','gif');

	if(isset($general['upload_svg']) && $general['upload_svg'] === 'on')
		$imgTypes[] = 'svg';

	$result = 0;
    if(array_search($ext, $imgTypes) !== false && !empty($fileTEMP)){
        if(move_uploaded_file($fileTEMP, $filePATH)){	
		    wcdp_convert_rgb_to_cmyk($fileURL, $filePATH, false);
			$response['filename'] = pathinfo($filename, PATHINFO_FILENAME);
			$response['url'] = $fileURL;
		    $result = true;
        }
    }
	$response['success'] = $result;
	echo json_encode($response);
    exit;
}
add_action('wp_ajax_wcdp_upload_img_file_ajax','wcdp_upload_img_file_ajax');
add_action('wp_ajax_nopriv_wcdp_upload_img_file_ajax','wcdp_upload_img_file_ajax');

// Uploads images since backend media
function wcdp_upload_img_file_wp_media($attachment_id){
	$post_type = get_post_type(wp_get_post_parent_id($attachment_id));
	if($post_type === 'wcdp-cliparts' || $post_type === 'wcdp-calendars' || $post_type === 'wcdp-designs'){	
        $fileURL = wp_get_attachment_url($attachment_id);
		$filePATH  = get_attached_file($attachment_id);		
	    wcdp_convert_rgb_to_cmyk($fileURL, $filePATH, false);
	}
}
add_action('add_attachment', 'wcdp_upload_img_file_wp_media', 10, 1);

// Convert CMYK Resources
function wcdp_convert_resource_cmyk(){
    $result = 0;
	$url = $_REQUEST['url'];
	$ext = pathinfo($url, PATHINFO_EXTENSION);
	$ext = ($ext == 'jpg' || $ext == 'png' || $ext == 'gif') ? $ext : 'jpg'; 
    $fileUniq = '/temp/img_source_file_'. uniqid().rand(0,10) .'.'. $ext;
	$fileURL = WCDP_URL_UPLOADS . $fileUniq;
	$filePATH = WCDP_PATH_UPLOADS . $fileUniq;	
	if(copy($url, $filePATH)){
	    $base64 = wcdp_convert_rgb_to_cmyk($fileURL, $filePATH, true);			
		if($base64){
		    $response['string'] = $base64;		
		    $result = true;		
		}
	}
    $response['success'] = $result;
	echo json_encode($response);
    exit;
}
add_action('wp_ajax_wcdp_convert_resource_cmyk','wcdp_convert_resource_cmyk');
add_action('wp_ajax_nopriv_wcdp_convert_resource_cmyk','wcdp_convert_resource_cmyk');

// Convert RGB to CMYK image with imagick
function wcdp_convert_rgb_to_cmyk($fileURL, $filePATH, $resource){
	$general = get_option('wcdp-settings-general');
	if($resource || (isset($general['CMYK']) && $general['CMYK'] == 'on' && isset($general['cmyk_convert']) && $general['cmyk_convert'] == 'on')){	
		$prfl = wcpd_get_imagick_profiles();
	    $ext = pathinfo($fileURL, PATHINFO_EXTENSION);
        if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif'){
            if(isset($general['imagick_cmd']) && $general['imagick_cmd'] === 'on'){	
                exec("convert ". $filePATH. " -profile ". $prfl['RGB'] ." -profile ". $prfl['CMYK'] ." -profile ". $prfl['RGB'] ." ". $filePATH ." 2>&1");
			}
            else if(extension_loaded('imagick')){
				$prflRGB = file_get_contents($prfl['RGB']);
				$prflCMYK = file_get_contents($prfl['CMYK']);
			    $im = new Imagick($filePATH);
                $im->profileImage('icc', $prflRGB);		  
                $im->profileImage('icc', $prflCMYK);
                $im->profileImage('icc', $prflRGB);
                $im->writeImage($filePATH);
                $im->clear();
                $im->destroy();	
			}
			if($resource){
				$tempIMG = file_get_contents($fileURL);
				$base64 = 'data:image/'. $ext .';base64,'. base64_encode($tempIMG);
				unlink($filePATH);
				return $base64;
			}			
        }
		else if($ext =='svg'){
            $tempSVG = file_get_contents($fileURL);  
            preg_match_all('/WCDP_DATA_CMYK/i', $tempSVG, $svgCMYK);
            if(!$svgCMYK[0]){
			    $colors = array();
			    $patchToCMYK = array();
	            $regx = "/#([a-f0-9]{3}){1,2}\b|rgb\((?:\s*\d+\s*,){2}\s*[\d]+\)|rgba\((\s*\d+\s*,){3}[\d\.]+\)/i";
			    preg_match_all($regx, $tempSVG, $matches);
                foreach($matches[0] as $color){
			        if(!in_array($color, $colors)){
				        if(strpos($color,'#') !== false && strlen($color) == 4){
					        $fixHex = '#'.$color[1].$color[1].$color[2].$color[2].$color[3].$color[3];
					        $tempSVG = preg_replace('/'.$color.'\b/', $fixHex, $tempSVG);
					        $color = $fixHex;
			            }
                        $colors[] = $color;
			        }				  
                }
			    if(count($colors) > 0){                
                    $colorsHEX = array();
                    $colorsCMYK = array();
				    $colors_chunks = array_chunk($colors, $general['chunk_colors']);
                    foreach($colors_chunks as $colors_chunk){
                        $convert = wcdp_convert_colors_to_cmyk($colors_chunk, false);
                        $colorsHEX = array_merge($colorsHEX, $convert['HEX']);
                        $colorsCMYK = array_merge($colorsCMYK, $convert['CMYK']);
                    }				
                    foreach($colorsCMYK as $key => $value){
                        $find = $colors[$key];
                        $replace = '#'. $colorsHEX[$key];
                        $tempSVG = preg_replace_callback($regx, function($match) use ($find, $replace){
                            return str_replace($find, $replace, $match[0]);
                        },$tempSVG);
			            $patchToCMYK[$replace] = $value;
			        }
				    $tempSVG .= '<!-- WCDP_DATA_CMYK_START'. json_encode($patchToCMYK). 'WCDP_DATA_CMYK_END -->'; 
		            file_put_contents($filePATH, $tempSVG);
			    }
		    }
		}
    }	
}