<?php

// Documentation
function wcdp_content_page_docs(){
	$srcImgs = WCDP_URL .'/assets/images/';
	$manual  = WCDP_URL .'/user-manual';
	$filelog = WCDP_PATH .'changelog.txt';
	$sparams = array(
	    __("Current PHP version", "wcdp")   => phpversion(),
	    __("Memory limit", "wcdp")          => ini_get('memory_limit'),
	    __("PHP post max size", "wcdp")     => ini_get('post_max_size'),
	    __("PHP time limit", "wcdp")        => ini_get('max_execution_time'),
	    __("PHP max input vars", "wcdp")    => ini_get('max_input_vars'),
	    __("Maximum upload size", "wcdp")   => ini_get('upload_max_filesize'),
		__("PHP allow_url_fopen", "wcdp")   => ini_get('allow_url_fopen') ? 'on':'off',
		__("PHP file_put_contents", "wcdp") => function_exists('file_put_contents') ? 'on':'off',
		__("PHP file_get_contents", "wcdp") => function_exists('file_get_contents') ? 'on':'off',
	    __("PHP Imagick extension", "wcdp") => extension_loaded('imagick') ? 'on':'off'
    );
	$wp_pages = array();
	foreach(get_pages() as $page){
		if($page->post_status == 'publish'){
			$wp_pages[] = array(
		        'id' => $page->ID,
				'title' => $page->post_title
		    );
		}
	}
	$data_demos = array(
	    'demos' => array(
	        'businesscard' => __("Business card", "wcdp"),
		    'flyer'        => __("Flyer", "wcdp"),
		    'poster'       => __("Poster", "wcdp"),
		    'diptych'      => __("Diptych brochure", "wcdp"),
		    'triptych'     => __("Triptych brochure", "wcdp"),
		    'tshirt'       => __("Men's t-shirt", "wcdp"),
		    'cap'          => __("Cap", "wcdp"),
		    'sweatshirt'   => __("Sweatshirt", "wcdp"),
    	    'mug'          => __("Mug", "wcdp"),
		    'mousepad'     => __("Mouse Pad", "wcdp"),
		    'phonecase'    => __("Phone case", "wcdp"),
	        'badge'        => __("Badge", "wcdp"),
		    'sticker'      => __("Sticker", "wcdp")
		),
	    'pages' => $wp_pages
	);
	?>
    <script>
	    var wcdp_translations = <?php echo json_encode(wcdp_editor_translations()); ?>,
		    wcdp_data_demos = <?php echo json_encode($data_demos); ?>,
            AJAX_URL = <?php echo json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
	</script>
	<div id="wcdp-contain-docs">	
		<div id="dp-doc-header">
		    <div><img src="<?php echo $srcImgs .'wcdp-logo.png'; ?>"/></div>
			<div><h2>WooCommerce Designer Pro <?php echo WCDP_VERSION; ?></h2></div><hr>
			<h4><?php _e( "Thank you for purchasing our WooCommerce Designer Pro plugin. Customize your products and create your awesome online printing!!", "wcdp" ) ?></h4>
		</div>		
	    <div id="wcdp-doc-sidebar">
	        <ul id="wcdp-doc-nav">
			    <li class="dp-selected"><?php _e( "Demos", "wcdp" ) ?></li>
	            <li><?php _e( "Tutorials", "wcdp" ) ?></li>
				<li><?php _e( "Status", "wcdp" ) ?></li>
		        <li><?php _e( "Changelog", "wcdp" ) ?></li>
		    </ul>
	    </div>
	    <div id="wcdp-doc-content">
		    <div class="dp-doc-section">
			    <h2><?php _e( "Install demos", "wcdp" ) ?></h2><hr>
                <div id="wcdp-demos-contain">
				    <?php
					$imgPosition = 0;
					foreach($data_demos['demos'] as $slug => $title){
				        echo '<div class="dp-demo"><span class="dp-demo-btn" data-value="'. $slug .'" title="'. __("Install Demo", "wcdp") .'" style="background-position: 0px '. $imgPosition .'px;"></span><p>'. $title .'</p></div>';
					    $imgPosition -= 100;
					}
				    ?>
				</div>
			    <input type="button" data-value="alldemos" id="wcdp-install-all-demos" class="button button-primary button-large" value="<?php _e( "Install all demos", "wcdp" ) ?>">
			</div>
		    <div class="dp-doc-section">
			    <h2><?php _e( "Tutorials", "wcdp" ) ?></h2><hr>
				<a href="<?php echo $manual; ?>" target="_blank">
				    <div class="dp-img-contain user-manual">
					    <img src="<?php echo $srcImgs .'user-manual.jpg'; ?>">
					</div>
				</a>			
			    <a href="https://youtu.be/hDvIEAVCqQA" target="_blank">
				    <div class="dp-img-contain">
					    <img src="<?php echo $srcImgs .'video-basic.jpg'; ?>">
					</div>
				</a>
			    <a href="https://youtu.be/XfEB8RIfb2Y" target="_blank">
				    <div class="dp-img-contain">
					    <img src="<?php echo $srcImgs .'video-designs.jpg'; ?>">
					</div>
				</a>				
			</div>
		    <div class="dp-doc-section">
			    <h2><?php _e( "Status", "wcdp" ) ?></h2><hr>
	            <table>
				    <?php
	                foreach($sparams as $key => $val){
						$vl = $val == 'on' ? __("Enabled", "wcdp") : ($val == 'off' ? __("Disabled", "wcdp") : $val);
						$cl = $val == 'on' ? ' class="dp-st-enabled"' : ($val == 'off' ? ' class="dp-st-disabled"' : '');
					    echo '<tr><td>'. $key .':</td><td'. $cl .'>'. $vl .'</td></tr>';
					}					
					?>
		        </table>
			</div>
		    <div class="dp-doc-section">
			    <h2><?php _e( "Changelog", "wcdp" ) ?></h2><hr>
	            <?php 
				$logString = '<div class="dp-log">';    
	            $changelog = fopen($filelog, 'r') or die( __("Can't open file", "wcdp") );
                while(!feof($changelog)){
                    $line = fgets($changelog);
                    if(strpos($line, 'Version') !== false)
                        $logString .='<b>'. $line .'</b><br>';
                    else
                        $logString .= $line .'<br>';
                }
                fclose($changelog);				
				print_r($logString .'</div>');				
				?>
			</div>
	    </div>		
	</div>
	<?php
}