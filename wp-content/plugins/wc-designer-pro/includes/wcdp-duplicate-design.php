<?php

// Duplicate Design
function wcdp_duplicate_design(){
	global $wpdb;
	
	if(!(isset($_GET['post']) || isset($_POST['post']) || (isset($_REQUEST['action']) && 'wcdp_duplicate_design' == $_REQUEST['action']))){
		wp_die( __( 'No design to duplicate has been supplied!', 'wcdp') );
	}
	
 	// Nonce verification
	if(!isset( $_GET['duplicate_nonce']) || !wp_verify_nonce($_GET['duplicate_nonce'], basename( __FILE__ )))
		return; 

	// Get the original design id
	$post_id = (isset($_GET['post']) ? absint($_GET['post']) : absint($_POST['post']));
	
	$post              = get_post($post_id);
	$current_user      = wp_get_current_user();	
	$new_post_author   = $current_user->ID;
	$new_post_date     = current_time('mysql');
	$new_post_date_gmt = get_gmt_from_date($new_post_date);
	$new_post_title    = $post->post_title .' ' . __('(Copy)', 'wcdp');	
	$post_status       = 'draft';
 
    // Create & insert the new design
	if(isset($post) && $post != null){
		$args = array(
		    'post_author'           => $new_post_author,
			'post_date'             => $new_post_date,
			'post_date_gmt'         => $new_post_date_gmt,
			'post_content'          => $post->post_content,
			'post_content_filtered' => $post->post_content_filtered,
			'post_title'            => $new_post_title,
			'post_excerpt'          => $post->post_excerpt,
			'post_status'           => $post_status,
			'post_type'             => $post->post_type,
			'comment_status'        => $post->comment_status,
			'ping_status'           => $post->ping_status,
			'post_password'         => $post->post_password,
			'to_ping'               => $post->to_ping,
			'pinged'                => $post->pinged,
			'post_modified'         => $new_post_date,
			'post_modified_gmt'     => $new_post_date_gmt,			
			'post_parent'           => $post->post_parent,
			'menu_order'            => $post->menu_order,
			'post_mime_type'        => $post->post_mime_type					
		);		
		$new_post_id = wp_insert_post($args);	
		
	    // Copy the original design files to the new design
	    $url_path = WCDP_PATH_UPLOADS . '/save-admin/designID';
        wcdp_copy_files_directory($url_path . $post_id, $url_path . $new_post_id);	
 
		// Get all current design terms ad set them to the new design
		$taxonomies = get_object_taxonomies($post->post_type);
		foreach($taxonomies as $taxonomy){
			$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
			wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
		}
 
		// Duplicate all design meta in SQL
		$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
		if(count($post_meta_infos) !=0){
			$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
			foreach($post_meta_infos as $meta_info){
				$meta_key = $meta_info->meta_key;
				if($meta_key == '_wp_old_slug') continue;
				$meta_value = addslashes($meta_info->meta_value);
				$sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
			}
			$sql_query.= implode(" UNION ALL ", $sql_query_sel);
			$wpdb->query($sql_query);
		}

		// Redirect to the edit design screen for the new design
		wp_redirect(admin_url('post.php?action=edit&post=' . $new_post_id));
		exit;
	} else{
		wp_die( __( 'Design creation failed, could not find original design: ', 'wcdp') . $post_id );
	}
}
add_action( 'admin_action_wcdp_duplicate_design', 'wcdp_duplicate_design' );

// Add duplicate design link
function wcdp_duplicate_design_link($actions, $post){
	if(current_user_can('edit_posts') && $post->post_type == 'wcdp-designs'){
		$nonce_url = wp_nonce_url('admin.php?action=wcdp_duplicate_design&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce');
		$actions['duplicate'] = '<a href="' . $nonce_url . '" title="'. __( 'Duplicate this design', 'wcdp' ) .'" rel="permalink">'. __( 'Duplicate', 'wcdp' ) .'</a>';
	}
	return $actions;
}
add_filter('post_row_actions', 'wcdp_duplicate_design_link', 10, 2);
add_filter('page_row_actions', 'wcdp_duplicate_design_link', 10, 2);