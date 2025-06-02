<?php

// Upload admin fonts
function wcdp_content_page_fonts(){
	?>
    <script> 
	    var wcdp_translations = <?php echo json_encode(wcdp_editor_translations()); ?>,
            AJAX_URL = <?php echo json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
	</script>
	<div class="wrap">
	    <h3><?php _e('Add fonts', 'wcdp') ?></h3>
		<select id="wcdp-select-web-fonts">
		<?php	
            $json = file_get_contents(WCDP_PATH .'assets/js/webfonts.json');
            $webfonts = json_decode($json, true);
            foreach($webfonts['items'] as $font){
                if(isset($font['family']) && isset($font['files']) && isset($font['files']['regular']))
                    echo '<option value="//fonts.googleapis.com/css?family='. urlencode($font['family']) .'">'. $font['family'] .'</option>';
            } 
		?>
        </select>
        <input type="button" id="wcdp-add-web-font" class="button" value="<?php  _e( "Add google font", "wcdp" ) ?>">		
        <h3><?php _e('Selected fonts', 'wcdp') ?></h3>
	    <div id='wcdp-fonts-contain'>
		<?php
			$storageFonts = wcdp_get_storage_fonts();			
		    if($storageFonts){
	            foreach($storageFonts as $font){		
                    if($font['id'] == 'wf'){
                        $fontName = sanitize_title($font['name']);
                        wp_register_style($fontName, $font['url'], array(), false, 'all');
                        wp_enqueue_style($fontName);
                    } else{
						?>
						<style>
                            @font-face{
				                font-family: <?php echo $font['name']; ?>; src: url('<?php echo $font['url']; ?>');
				            } 
				        </style>
					    <?php
					}
					?> 
                    <div class="dp-font">
	                    <p style="font-family:<?php echo $font['name']; ?>"><?php echo $font['name']; ?></p>
	                    <button class="button wcdp-remove-font"><?php _e( "Remove", "wcdp" ) ?></button>
				        <input type="hidden" value="<?php echo ($font['id'] == 'wf' ? $font['url'] : $font['id']); ?>">
		            </div>
					<?php
	            }
		    } else{
				?>
			    <div id="wcdp-contain-search-empty">
			        <div class="wcdp-upload-cloud"></div>
			        <label><?php _e( "No selected fonts found", "wcdp" ) ?></label>							
			    </div>
				<?php
		    } ?>
		</div>
		<?php 
        wp_enqueue_media();	
		?>
        <div id="wcdp-add-font-contain" multiple="multiple" format="application" support="ttf,eot,woff,woff2">
            <input type="button" class="wcdp-select-file button" value="<?php  _e( "Add custom font", "wcdp" ) ?>" />
        </div> 
		<input type="button" id="wcdp-update-fonts" class="button button-primary button-large" value="<?php  _e( "Update", "wcdp" ) ?>">
	</div>
	<?php
}

function wcdp_update_manage_fonts_options(){
	$mf = 'wcdp-settings-fonts';
	$rq = $_REQUEST['fonts'];
    if(get_option($mf) == $rq || update_option($mf, $rq)){
		echo 'update_successful';
	} else{
		echo 'error';
	}
    exit;
}
add_action('wp_ajax_wcdp_update_manage_fonts_options', 'wcdp_update_manage_fonts_options');