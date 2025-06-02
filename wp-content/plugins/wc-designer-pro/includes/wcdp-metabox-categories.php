<?php

// Register Categories taxonomy
function wcdp_categories_init(){	
	$labels = array(
        'name'              => __( 'Design categories', 'taxonomy general name', 'wcdp' ),
        'singular_name'     => __( 'Category', 'taxonomy singular name', 'wcdp' ),
		'menu_name'         => __( 'Design categories', 'wcdp' ),
		'add_new_item'      => __( 'Add new category', 'wcdp' ),
		'edit_item'         => __( 'Edit category', 'wcdp' ), 
        'search_items'      => __( 'Search categories', 'wcdp' ),
        'all_items'         => __( 'All categories', 'wcdp' ),
        'parent_item'       => __( 'Parent category', 'wcdp' ),
        'parent_item_colon' => __( 'Parent category:', 'wcdp' ),        
        'update_item'       => __( 'Update category', 'wcdp' ),        
        'new_item_name'     => __( 'New category name', 'wcdp' )        		
	);
	$args = array(
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => false,
        'show_tagcloud'     => true,
        'query_var'         => true,
	);
	register_taxonomy( 'wcdp-design-cat', 'wcdp-designs', $args );
}
add_action( 'init', 'wcdp_categories_init' );

//  Sort designs categories hierarchicaly
function wcdp_sort_designs_cat_hierarchicaly($cats, $designs_cat, $parent_id){
	$output = '';
    foreach($cats as $i => $cat){
        if($cat->parent == $parent_id){
			$children = wcdp_sort_designs_cat_hierarchicaly($cats, $designs_cat, $cat->term_id);			
            $checked = is_array($designs_cat) && in_array($cat->term_id, $designs_cat) ? ' checked="checked"':'';
			$output .= '<li id="wcdp-design-cat-'. $cat->term_id .'">';
			$output .= '<label for="in-wcdp-design-cat-'. $cat->term_id .'">';
			$output .= '<input value="'. $cat->term_id .'" type="checkbox" name="wcdp_input_design_cat[]" id="in-wcdp-design-cat-'. $cat->term_id .'"'. $checked .'> ';
            $output .=  $cat->name .'('. $cat->count .')</label></li>';
            if($children)
				$output .= '<ul class="children">'. $children .'</ul>';
        }
    }
    return $output;
}

// Fix design categories checked on top
function wcdp_design_cat_fix_checked_ontop($args){
    if($args['taxonomy'] === 'wcdp-design-cat'){
        $args['checked_ontop'] = false;
    }
    return $args;
}
add_filter('wp_terms_checklist_args','wcdp_design_cat_fix_checked_ontop');