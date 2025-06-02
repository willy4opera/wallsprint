<?php

// Register Calendars post type
function wcdp_calendars_init(){	
	$labels = array(
		'name'               => __( 'Calendars', 'wcdp' ),
		'singular_name'      => __( 'Calendar', 'wcdp' ),
		'menu_name'          => __( 'Calendars', 'wcdp' ),
		'add_new'            => __( 'Add new calendar category', 'wcdp' ),
		'add_new_item'       => __( 'Add new calendar category', 'wcdp' ),
		'new_item'           => __( 'New Calendar', 'wcdp' ),
		'edit_item'          => __( 'Edit calendar category', 'wcdp' ),
		'view_item'          => __( 'View calendar category', 'wcdp' ),
		'search_items'       => __( 'Search calendar categories', 'wcdp' ),
		'not_found'          => __( 'No calendar categories found.', 'wcdp' ),
		'not_found_in_trash' => __( 'No calendar categories found in Trash.', 'wcdp' )
	);
	$args = array(
		'labels'              => $labels,
        'description'         => __( 'Settings categories calendars.', 'wcdp' ),
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
	register_post_type('wcdp-calendars', $args);
}
add_action('init', 'wcdp_calendars_init');

// Upload admin calendars metabox
function wcdp_calendars_metabox_html($post){
    $calendars = get_post_meta($post->ID, '_wcdp_uploads_calendars', true); 
	$loader = WCDP_URL .'/assets/images/ajax-loader.gif'; ?>
    <script>
	    var wcdp_translations = <?php echo json_encode(wcdp_editor_translations()); ?>,
            wcdp_lazy_loader = <?php echo json_encode($loader); ?>;
	</script>	
    <div class="dp-wrap">
	    <div id="wcdp-calendars-contain">
            <?php 
			$html = '';
			if(!empty($calendars)){				
			    foreach(array_unique($calendars) as $caz){
					$thumb = wp_get_attachment_image_src($caz, 'thumbnail');
					$imageURL = $thumb ? $thumb[0] : wp_get_attachment_url($caz);					
                    $html .='<div class="dp-caz">';			
					$html .='<span class="dp-img-contain"><img class="lazyload dp-loading-lazy" data-src="'. $imageURL .'" src="'. $loader .'"/></span>';
					$html .="<div title='". __( 'Remove', 'wcdp' ) ."' class='wcdp-remove-caz'></div>";
                    $html .='<input type="hidden" name="wcdp-uploads-calendars[]" value="'. $caz . '">';
                    $html .="</div>";					
		        } 
			} else{				
			    $html .='<div id="wcdp-contain-search-empty">';
			    $html .='<div class="wcdp-upload-cloud"></div>';
			    $html .='<label>'. __( 'No uploaded calendars found', 'wcdp' ) .'</label>';							
			    $html .='</div>';
		    }	
            echo $html;	
            wp_enqueue_media(array('post' => $post->ID));		
			?>
	    </div>
        <div id="wcdp-add-calendar-contain" multiple="multiple" format="image" support="jpg,jpeg,png,gif,svg">
            <input type="button" class="wcdp-select-file button" value="<?php  _e( "Add new calendar", "wcdp" ) ?> " />
        </div> 
    </div>
<?php    
}
add_action('add_meta_boxes', function(){
	add_meta_box('wcdp-calendars-box', __('Calendars', 'wcdp'), 'wcdp_calendars_metabox_html', 'wcdp-calendars', 'normal', 'high');
});

function wcdp_calendars_save_post($post_id){
    if(get_post_type($post_id) == 'wcdp-calendars'){
		if(isset($_POST['wcdp-uploads-calendars'])){
            update_post_meta($post_id, '_wcdp_uploads_calendars', $_POST['wcdp-uploads-calendars']);
		}
    }
}
add_action('save_post', 'wcdp_calendars_save_post');
