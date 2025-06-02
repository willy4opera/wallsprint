<?php

// Manage admin image filters
function wcdp_content_page_filters(){
	$filters = wcdp_content_html_filters();
	$optionFilter = get_option('wcdp-settings-filters');
	?>
    <script> 
	    var wcdp_translations = <?php echo json_encode(wcdp_editor_translations()); ?>,
            AJAX_URL = <?php echo json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
	</script>
	<div class="wrap">
        <h3><?php _e('Manage image filters', 'wcdp') ?></h3>
		<div id="wcdp-filters-contain">
			<?php
            $imgPosition = 0;			
	        foreach($filters as $f => $fv){
				$rng = $f == 'brightness' || $f == 'saturate' || $f == 'contrast' ? true : false;
				$attr = 'class="dp-filter-'. ($rng ? 'rng':'btn') .'"' . (!$rng ? 'style="background-position: 0px '. $imgPosition .'px;"' : '');
				?>
	            <div class="dp-filter">
				    <span <?php echo $attr ?>></span>
					<div class="dp-row">
					    <label class="dp-filter-name"><?php echo $fv ?></label>
					    <label><input data-filter="<?php echo $f ?>" type="checkbox" <?php if(isset($optionFilter[$f]) && $optionFilter[$f] == 'on'){ echo 'checked="checked"';} ?>><?php  _e( "Enable", "wcdp" ) ?></label>
		            </div>
				</div>
				<?php
				if(!$rng)
				    $imgPosition -= 62;
	        }
            ?>
        </div>
		<input type="button" id="wcdp-update-filters" class="button button-primary button-large" value="<?php  _e( "Update", "wcdp" ) ?>">
	</div>
	<?php
}

function wcdp_content_html_filters(){
	return array(
        'none'        => __( 'None', 'wcdp' ),	
        'grayscale'   => __( 'Grayscale', 'wcdp' ),
	    'sepia'       => __( 'Sepia', 'wcdp' ),	    
		'warm'        => __( 'Warm', 'wcdp' ),
    	'cold'        => __( 'Cold', 'wcdp' ),
		'yellow'      => __( 'Yellow', 'wcdp' ),		
		'kodachrome'  => __( 'Kodachrome', 'wcdp' ),		
	    'vintage'     => __( 'Vintage', 'wcdp' ),
	    'brownie'     => __( 'Brownie', 'wcdp' ),        
	    'polaroid'    => __( 'Polaroid', 'wcdp' ),
		'technicolor' => __( 'Technicolor', 'wcdp' ),				
	    'acid'        => __( 'Acid', 'wcdp' ),		
		'shiftToBGR'  => __( 'Sea', 'wcdp' ),
		'fantasy'     => __( 'Fantasy', 'wcdp' ),
		'purple'      => __( 'Purple', 'wcdp' ),		
		'ghost'       => __( 'Ghost', 'wcdp' ),
        'predator'    => __( 'Predator', 'wcdp' ),		
	    'night'       => __( 'Night', 'wcdp' ),
	    'invert'      => __( 'Invert', 'wcdp' ),		
	    'noise'       => __( 'Noise', 'wcdp' ),
	    'pixelate'    => __( 'Pixelate', 'wcdp' ),	
        'sharpen'     => __( 'Sharpen', 'wcdp' ),
        'blur'        => __( 'Blur', 'wcdp' ),
        'emboss'      => __( 'Emboss', 'wcdp' ),			
        'brightness'  => __( 'Brightness', 'wcdp' ),
	    'saturate'    => __( 'Saturation', 'wcdp' ),
	    'contrast'    => __( 'Contrast', 'wcdp' ),
	);
}

function wcdp_update_manage_filters_options(){
	$mf = 'wcdp-settings-filters';
	$rq = $_REQUEST['filters'];
    if(get_option($mf) == $rq || update_option($mf, $rq)){
		echo 'update_successful';
	} else{
		echo 'error';
	}
    exit;
}
add_action('wp_ajax_wcdp_update_manage_filters_options', 'wcdp_update_manage_filters_options');