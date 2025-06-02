<?php

// Init shortcode [wcdp_editor] 
function wcdp_editor_user_init(){
    $getModeDesign = wcdp_check_mode_design_page();
	require_once(ABSPATH .'/wp-admin/includes/plugin.php');
	if($getModeDesign && is_plugin_active('woocommerce/woocommerce.php')){
        ob_start();
		$mode = $getModeDesign['mode'];
        $productID = $getModeDesign['id'];
		$templateID = $getModeDesign['template_id'];
		$attributes = $getModeDesign['attr_data'];
		$designID = $templateID ? $templateID : get_post_meta($productID,'_wcdp_product_design_id', true);
		if($designID && get_post_status($designID) == 'publish'){
            if($mode === 'designer_editor'){
	            $url_path = WCDP_PATH_UPLOADS .'/save-admin/designID'. $designID;
            }
            else if($mode === 'designer_save_editor'){
		        $url_path = WCDP_PATH_UPLOADS .'/save-user/'. $getModeDesign['design_id'];
            }
            $args = array(
		        'editor'      => 'frontend',
			    'productID'   => $productID, 
			    'designID'    => $designID,
				'attr_data'   => $attributes,
				'designURL'   => $url_path,
			    'pageID'      => wcdp_check_publish_design_page($productID, $designID),
				'mode'        => $mode
		    );
            $params = wcdp_get_params_product_designer($args);
            wcdp_designer_pro_content_store($params);
		}
        return ob_get_clean();
    }
}
add_shortcode( 'wcdp_editor', 'wcdp_editor_user_init' );