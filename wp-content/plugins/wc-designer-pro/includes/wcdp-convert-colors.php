<?php

// Convert colors to CMYK
function wcdp_convert_colors_to_cmyk($colors, $table){	
	$general = get_option('wcdp-settings-general');
	$count = count($colors);
	$colorsHEX = array();
    $colorsCMYK = array();
	$prfl = wcpd_get_imagick_profiles();
	if(!$table) 
		$picker_table = get_option('wcdp-settings-table');
    if(isset($general['imagick_cmd']) && $general['imagick_cmd'] === 'on'){	
	    $pixels = '';
	    $format = '';
	    foreach($colors as $key => $color){           
		    $pixels .= "-draw \"fill $color color $key,0 point\" ";
            $format .= $table ? "%[fx:int(255*p{".$key.",0}.r)],%[fx:int(255*p{".$key.",0}.g)],%[fx:int(255*p{".$key.",0}.b)] " : "%[pixel:p{".$key.",0}] ";
		}
		$convert = $table ? "-colorspace cmyk -profile ". $prfl['CMYK'] ." -profile ". $prfl['RGB'] : "-profile ". $prfl['RGB'] ." -profile ". $prfl['CMYK'];		
    	$cmd = 'convert -size '. $count .'x1 xc:"#fff" '. $pixels .' '. $convert .' -format "'. $format .'"';
		$strCMYK = shell_exec("$cmd info:- 2>&1");
		$paletteCMYK = explode(' ', $strCMYK);

	    for($i = 0; $i < $count; $i++){
			if($table){ 
 			    $arr = explode(',', $paletteCMYK[$i]);
	            $rgb = array('r' => $arr[0], 'g' => $arr[1], 'b' => $arr[2]);
	            $hex = wcdp_rgb_to_hex($rgb);
                $colorsHEX[] = $hex;
			} else{	  
                $arr = array();
				$colorRpc = preg_replace('/cmyk|\(|\%|\)/', '', $paletteCMYK[$i]);
				$colorFix = explode(',', $colorRpc);
				for($j = 0; $j < count($colorFix); $j++)
                    $arr[] = round($colorFix[$j]);                
                $colorNumber = hexdec(wcdp_cmyk_to_hex3($arr[0], $arr[1], $arr[2], $arr[3]));
                $colorsHEX[] = $picker_table[$colorNumber];
				$colorsCMYK[] = implode(',', $arr);	
			}	  
        }
	} else if(extension_loaded('imagick')){
        $im = new Imagick();    
        $im->newImage($count, 1, '#fff');

        foreach($colors as $key => $color){
            $draw  = new ImagickDraw();
            $color = new ImagickPixel($color);
            $draw->setFillColor($color);
            $draw->point($key, 0);
            $im->drawImage($draw);
        }     
		$prflRGB = file_get_contents($prfl['RGB']);
		$prflCMYK = file_get_contents($prfl['CMYK']);		
		if($table){
			$im->transformImageColorspace(Imagick::COLORSPACE_CMYK);
		    $im->profileImage('icc', $prflCMYK);
			$im->profileImage('icc', $prflRGB);	
		} else{			
            $im->profileImage('icc', $prflRGB);		  
            $im->profileImage('icc', $prflCMYK);
		}
	    for($i = 0; $i < $count; $i++){
			$pixel = $im->getImagePixelColor($i, 0);
			if($table){                
                $rgb = $pixel->getColor();
		        $hex = wcdp_rgb_to_hex($rgb);
                $colorsHEX[] = $hex;
			} else{
			    $cc = round($pixel->getColorValue(imagick::COLOR_CYAN) * 100);
                $mm = round($pixel->getColorValue(imagick::COLOR_MAGENTA) * 100);
                $yy = round($pixel->getColorValue(imagick::COLOR_YELLOW) * 100);
                $kk = round($pixel->getColorValue(imagick::COLOR_BLACK) * 100);		
		        $colorNumber = hexdec(wcdp_cmyk_to_hex3($cc, $mm, $yy, $kk));			
                $colorsHEX[] = $picker_table[$colorNumber];
		        $colorsCMYK[] = $cc .','. $mm .','. $yy .','. $kk;				
			}
        }
        $im->clear();
        $im->destroy(); 
	}	
	$result = array(
		'HEX'  => $colorsHEX,
	    'CMYK' => $colorsCMYK
	);
	return $result;	
}