<?php

// Register Cliparts post type
function wcdp_cliparts_init(){	
	$labels = array(
		'name'               => __( 'Cliparts', 'wcdp' ),
		'singular_name'      => __( 'Clipart', 'wcdp' ),
		'menu_name'          => __( 'Cliparts', 'wcdp' ),
		'add_new'            => __( 'Add new clipart category', 'wcdp' ),
		'add_new_item'       => __( 'Add new clipart category', 'wcdp' ),
		'new_item'           => __( 'New Clipart', 'wcdp' ),
		'edit_item'          => __( 'Edit clipart category', 'wcdp' ),
		'view_item'          => __( 'View clipart category', 'wcdp' ),
		'search_items'       => __( 'Search clipart categories', 'wcdp' ),
		'not_found'          => __( 'No clipart categories found.', 'wcdp' ),
		'not_found_in_trash' => __( 'No clipart categories found in Trash.', 'wcdp' )
	);
	$args = array(
		'labels'              => $labels,
        'description'         => __( 'Settings categories cliparts.', 'wcdp' ),
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
	register_post_type( 'wcdp-cliparts', $args );
}
add_action('init', 'wcdp_cliparts_init');

// Upload admin cliparts metabox
function wcdp_cliparts_metabox_html($post){
    $cliparts = get_post_meta($post->ID, '_wcdp_uploads_cliparts', true);
	$loader = WCDP_URL .'/assets/images/ajax-loader.gif'; ?>
    <script>
	    var wcdp_translations = <?php echo json_encode(wcdp_editor_translations()); ?>,
            wcdp_lazy_loader = <?php echo json_encode($loader); ?>;
	</script>		
    <div class="dp-wrap">
	    <div id="wcdp-cliparts-contain">
            <?php 
			$html = '';
			if(!empty($cliparts)){
			    foreach(array_unique($cliparts) as $clip){
					$thumb = wp_get_attachment_image_src($clip, 'thumbnail');
					$imageURL = $thumb ? $thumb[0] : wp_get_attachment_url($clip);
                    $html .='<div class="dp-clip">';			
					$html .='<span class="dp-img-contain"><img class="lazyload dp-loading-lazy" data-src="'. $imageURL .'" src="'. $loader .'"/></span>';
					$html .="<div title='". __( 'Remove', 'wcdp' ) ."' class='wcdp-remove-clip'></div>";
                    $html .='<input type="hidden" name="wcdp-uploads-cliparts[]" value="'. $clip . '">';
                    $html .="</div>";					
		        } 
			} else{				
			    $html .='<div id="wcdp-contain-search-empty">';
			    $html .='<div class="wcdp-upload-cloud"></div>';
			    $html .='<label>'. __( 'No uploaded cliparts found', 'wcdp' ) .'</label>';							
			    $html .='</div>';
		    }	
            echo $html;	
            wp_enqueue_media(array('post' => $post->ID));
			?>
	    </div>
        <div id="wcdp-add-clipart-contain" multiple="multiple" format="image" support="jpg,jpeg,png,gif,svg">
            <input type="button" class="wcdp-select-file button" value="<?php  _e( "Add new clipart", "wcdp" ) ?> "/>
        </div> 
    </div>
    <?php    
}
add_action('add_meta_boxes', function(){
	add_meta_box('wcdp-cliparts-box', __('Cliparts', 'wcdp'), 'wcdp_cliparts_metabox_html', 'wcdp-cliparts', 'normal', 'high');
});

function wcdp_cliparts_save_post($post_id){
    if(get_post_type($post_id) == 'wcdp-cliparts'){
		if(isset($_POST['wcdp-uploads-cliparts'])){
            update_post_meta($post_id, '_wcdp_uploads_cliparts', $_POST['wcdp-uploads-cliparts']);
		}
    }
}
add_action('save_post', 'wcdp_cliparts_save_post');