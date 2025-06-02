<?php

// Activate & deactivate plugin
function wcdp_plugin_activate(){
    foreach(['save-admin','save-user','orders','temp'] as $folder){
        $url_path = WCDP_PATH_UPLOADS .'/'. $folder; 
        if(!file_exists($url_path)){
	        mkdir($url_path, 0755, true);
        }
	}	
	$shapes = array(); 
	for($i = 0; $i < count(wcdp_content_html_shapes()); $i++){ 
		$shapes[$i] = 'on';
	}
	$filters = wcdp_content_html_filters();
    $fields = wcdp_get_defaults_settings_field();
	
	$fields['fonts'] = '';
	$fields['shapes'] = $shapes;
	$fields['filters'] = array_map(function(){ return 'on'; }, $filters);
	foreach($fields as $fieldkey => $fieldVal){
		if(wcdp_optionExists('wcdp-settings-'. $fieldkey) === false){
	        add_option('wcdp-settings-'. $fieldkey, $fieldVal);
		}
    }
}
function wcdp_plugin_deactivate(){}

// Register stylesheets & javascript
function wcdp_plugin_assets_enqueue(){	
	wp_enqueue_style('wcdp-design-css', plugins_url( '/assets/css/wcdp-design.min.css', __FILE__ ), array(), WCDP_VERSION, 'all');
	wp_enqueue_style('spectrum-css', plugins_url( '/assets/css/spectrum.min.css', __FILE__ ), array(), WCDP_VERSION, 'all');
	wp_enqueue_style('mCustomScrollbar-css', plugins_url( '/assets/css/jquery.mCustomScrollbar.min.css', __FILE__ ), array(), WCDP_VERSION, 'all');
	wp_enqueue_style('jbox-css', plugins_url( '/assets/css/jBox.min.css', __FILE__ ), array(), WCDP_VERSION, 'all');
	wp_enqueue_style('cropper-css', plugins_url( '/assets/css/cropper.min.css', __FILE__ ), array(), WCDP_VERSION, 'all');
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-dialog');
	wp_enqueue_script('jquery-touch-punch');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('webfontloader-js', plugins_url( '/assets/js/webfontloader.js', __FILE__ ), array('jquery'), WCDP_VERSION, false);
	wp_enqueue_script('lazyload-js', plugins_url( '/assets/js/lazyload.min.js', __FILE__ ), array('jquery'), WCDP_VERSION, false);
	wp_enqueue_script('jbox-js', plugins_url( '/assets/js/jBox.min.js', __FILE__ ), array('jquery'), WCDP_VERSION, false);
}
function wcdp_plugin_assets_enqueue_admin(){
	wp_enqueue_style('wcdp-admin-css', plugins_url( '/assets/css/wcdp-admin.min.css', __FILE__ ), array(), WCDP_VERSION, 'all');
	wp_enqueue_style('select2-css', plugins_url( '/assets/css/select2.min.css', __FILE__ ), array(), WCDP_VERSION, 'all');
    wp_enqueue_script('wcdp-admin-js', plugins_url( '/assets/js/wcdp-admin.min.js', __FILE__ ), array('jquery'), WCDP_VERSION, false);    
	wp_enqueue_script('select2-js', plugins_url( '/assets/js/select2.min.js', __FILE__ ), array('jquery'), WCDP_VERSION, false);	
}
function wcdp_register_java_files_for_editor(){
	wcdp_register_spectrum_mod_cmyk();
	wp_enqueue_script('fabric-js', plugins_url( '/assets/js/fabric.min.js', __FILE__ ), array('jquery'), WCDP_VERSION, false);
	wp_enqueue_script('fabric-canvasex-js', plugins_url( '/assets/js/fabric.canvasex.min.js', __FILE__ ), array('jquery'), WCDP_VERSION, false);
	wp_enqueue_script('fabric-curvedText-js', plugins_url( '/assets/js/fabric.curvedText.min.js', __FILE__ ), array('jquery'), WCDP_VERSION, false);
	wp_enqueue_script('wcdp-content-editor-js', plugins_url( '/assets/js/wcdp-content-editor.min.js', __FILE__ ), array('jquery'), WCDP_VERSION, false);
    wp_enqueue_script('mCustomScrollbar-js', plugins_url( '/assets/js/jquery.mCustomScrollbar.concat.min.js', __FILE__ ), array('jquery'), WCDP_VERSION, false);
	wp_enqueue_script('masonry-pkgd-js', plugins_url( '/assets/js/masonry.pkgd.min.js', __FILE__ ), array('jquery'), WCDP_VERSION, false);
	wp_enqueue_script('qrcodegen-js', plugins_url( '/assets/js/qrcodegen.min.js', __FILE__ ), array('jquery'), WCDP_VERSION, false);
	wp_enqueue_script('cropper-js', plugins_url( '/assets/js/cropper.min.js', __FILE__ ), array('jquery'), WCDP_VERSION, false);
    wp_enqueue_script('pdfkit-js', plugins_url( '/assets/js/pdfkit.min.js', __FILE__ ), array('jquery'), WCDP_VERSION, false);
	wp_enqueue_script('svg-to-pdfkit-js', plugins_url( '/assets/js/svg-to-pdfkit.min.js', __FILE__ ), array('jquery'), WCDP_VERSION, false);
	wp_enqueue_script('blobstream-js', plugins_url( '/assets/js/blobstream.min.js', __FILE__ ), array('jquery'), WCDP_VERSION, false);
	wp_enqueue_script('jszip-js', plugins_url( '/assets/js/jszip.min.js', __FILE__ ), array('jquery'), WCDP_VERSION, false);
    wp_enqueue_script('file-saver-js', plugins_url( '/assets/js/fileSaver.min.js', __FILE__ ), array('jquery'), WCDP_VERSION, false);
}
function wcdp_register_spectrum_mod_cmyk(){
	wp_enqueue_script('spectrum-js', plugins_url( '/assets/js/spectrum.min.js', __FILE__ ), array('jquery'), WCDP_VERSION, false);
}

// Add notices error for admin
function wcdp_note_error_html($type, $content){
	echo '<div class="'. $type .'"><p><b>WooCommerce Designer Pro</b></p>'. $content. '</div>';
}

// Load textdomain
add_action( 'plugins_loaded', function(){
    load_plugin_textdomain( 'wcdp', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
});

// Start session if it does not exist
add_action( 'init', function(){
    if(!is_user_logged_in() && !session_id()){
        session_start();
	}
});