<?php

// Add cart item design data to the order
function wcdp_add_order_design_params($item, $cart_item_key, $values, $order){
    if(isset($values['wcdp_product_data'])){
        $item->update_meta_data('wcdp_product_data', $values['wcdp_product_data']);
    }
}
add_action( 'woocommerce_checkout_create_order_line_item', 'wcdp_add_order_design_params', 20, 4 );

// Save the design of the new order
function wcdp_new_order_item_save_designs($item_id, $item, $order_id){
	$order = wc_get_order($order_id);
	$userID = is_user_logged_in() ? get_current_user_id() : false; 
    foreach($order->get_items() as $item_id => $item){
    	if(isset($item['wcdp_product_data']) && !empty($item['wcdp_product_data'])){
		    $product_data = $item['wcdp_product_data'];
            $uniq = $product_data['uniq'];
	    	$path = WCDP_PATH_UPLOADS .'/';
		    $target = $path .'temp/'. $uniq;
		    if(file_exists($target)){
	            if(rename($target, $path .'save-user/'. $uniq)){
                    wcdp_copy_files_directory($path .'save-user/'. $uniq, $path .'orders/orderID'. $order_id .'/'. $uniq);
			        if($userID){
						$designName = isset($product_data['design_name']) ? $product_data['design_name'] : 0;
						$attributes = isset($product_data['attr_data']) ? $product_data['attr_data'] : 0;
			            wcdp_save_user_designs_list($userID, $uniq, $product_data['product_id'], $product_data['design_id'], $designName, $attributes, false);
			        }
				}
			}
		}
    }	
}
add_action( 'woocommerce_new_order_item', 'wcdp_new_order_item_save_designs', 10, 3 );

// Check temporary files exist or remove product
function wcdp_check_temp_files_or_remove_item(){
	global $woocommerce;
	if($woocommerce->cart->get_cart_contents_count() > 0){
        foreach($woocommerce->cart->get_cart() as $item => $values){
            if(array_key_exists('wcdp_product_data', $values)){
				$path = WCDP_PATH_UPLOADS .'/';
				$uniq = $values['wcdp_product_data']['uniq'];
			    $urlSave = $path .'save-user/'. $uniq;
				$urlTemp = $path .'temp/'. $uniq;
				if(file_exists($urlSave)){
				    rename($urlSave, $urlTemp);
				} else if(!file_exists($urlTemp)){
  			        $woocommerce->cart->remove_cart_item($values['key']);
				}
			}
        } 
	}
}
add_action( 'woocommerce_before_cart', 'wcdp_check_temp_files_or_remove_item', 10, 1 );

// Replace cart item thumbnail for design
function wcdp_replace_thumb_by_design($product_get_image, $cart_item, $cart_item_key){
	if(array_key_exists('wcdp_product_data', $cart_item)){
		$product_data = $cart_item['wcdp_product_data'];
		$url = WCDP_URL_UPLOADS . '/temp/'. $product_data['uniq'] .'/';
	    $product_get_image = '<img style="max-width: 200px; min-width: 100px;" src="'. $url .'cover.jpg' .'">';
		$general = get_option('wcdp-settings-general');
		$downloadOnlyUserLogged = isset($general['download_design_logged']) && $general['download_design_logged'] === 'on' ? true : false;
		if(isset($general['download_design_cart']) && $general['download_design_cart'] === 'on' && (!$downloadOnlyUserLogged || $downloadOnlyUserLogged && is_user_logged_in())){
		    $product_get_image .= '<br><br><a class="button" href="'. $url . $product_data['zip_name'] .'_'. $product_data['user'] .'.zip' .'">'. __( 'Download', 'wcdp' ) .'</a>';
		}
	}
	return $product_get_image;
}
add_filter( 'woocommerce_cart_item_thumbnail', 'wcdp_replace_thumb_by_design', 10, 3 );

// Add custom design download for item admin
function wcdp_add_order_item_custom_design($item_id, $item, $product){
    if(isset($item['wcdp_product_data']) && !empty($item['wcdp_product_data'])){
	    $product_data = $item['wcdp_product_data'];
	    $url = WCDP_URL_UPLOADS . '/orders/orderID'. $item['order_id'] .'/'. $product_data['uniq'] .'/'; ?> 
		<div class="wcdp-contain-custom-design">
	        <p><img style="max-width: 100px !important; height: auto" src="<?php echo $url .'cover.jpg'; ?>"></p>
			<p><a class="button" href="<?php echo $url . $product_data['zip_name'] .'_'. $product_data['admin'] .'.zip'; ?>"><?php  _e( "Download custom design", "wcdp" ) ?> </a></p>
		</div>
		<?php
    }
}
add_action( 'woocommerce_after_order_itemmeta', 'wcdp_add_order_item_custom_design', 10, 3 ); 

// Add custom design link to email after finishing the order
function wcdp_add_custom_design_link($fields, $sent_to_admin, $order){
	$general = get_option('wcdp-settings-general');
    if(isset($general['mail_order_complete']) && $general['mail_order_complete'] === 'on' && $order->get_status() !== 'completed' || isset($general['mail_status_complete']) && $general['mail_status_complete'] === 'on' && $order->get_status() === 'completed'){	
		foreach($order->get_items() as $item_id => $item){
			if(isset($item['wcdp_product_data']) && !empty($item['wcdp_product_data'])){
                $product_data = $item['wcdp_product_data'];
                $fields[] = array(
                    'label' => __( "Download custom design", "wcdp" ),
                    'value' => '<a href="'. WCDP_URL_UPLOADS .'/save-user/'. $product_data['uniq'] .'/'. $product_data['zip_name'] .'_'. $product_data['user'] .'.zip">'. $item['name'] .'</a>'
                );			
		    }
        }
	}
	return $fields;  
}; 
add_action( 'woocommerce_email_order_meta_fields', 'wcdp_add_custom_design_link', 10, 3 );

// Add custom design table in the order
function wcdp_add_custom_design_order($order){
	$general = get_option('wcdp-settings-general');
	if(isset($general['download_design_order']) && $general['download_design_order'] === 'on' && $order->get_status() !== 'pending'){
	    $designs = array();
	    foreach($order->get_items() as $item_id => $item){
		    if(isset($item['wcdp_product_data']) && !empty($item['wcdp_product_data'])){
				$data = $item['wcdp_product_data'];
			    $designs[] = array(
			        'data' => $data,
					'name' => isset($data['design_name']) && !empty($data['design_name']) ? $data['design_name'] : $item['name']
			    );
	        }
        }
	    if(!empty($designs)){
		    $userID = $order->get_user_id();
		    if($userID){ 
			    $get_designs_list = get_user_meta($userID, '_wcdp_designs_save_user_list', true);
				if(!empty($get_designs_list)){ ?>
	                <h2 class="woocommerce-custom-design__title"><?php _e( "Custom design", "wcdp" ) ?></h2>
	                <table class="woocommerce-table woocommerce-table--custom-design shop_table">
	                    <tr>
	                        <th><?php _e( "Preview Design", "wcdp" ) ?></th>
				            <th><?php _e( "Design Name", "wcdp" ) ?></th>
				            <th><?php _e( "Options", "wcdp" ) ?></th>
	                    </tr>
	                    <?php	                
			            foreach($designs as $design){
							$uniq = $design['data']['uniq'];
				            if(array_key_exists($uniq, $get_designs_list)){
							    if(isset($get_designs_list[$uniq]['designName'])){
							        $design['name'] = $get_designs_list[$uniq]['designName'];
								}
				                $url = WCDP_URL_UPLOADS .'/save-user/'. $uniq .'/';
					            $download = '<a class="button" href="'. $url . $design['data']['zip_name'] .'_'. $design['data']['user'] .'.zip">'. __( "Download", "wcdp" ) .'</a>';
					            $thumbnail = $url .'cover.jpg';
				            }
				            else{
					            $download = __( "You deleted design and download is not available.", "wcdp" );
					            $thumbnail = wc_placeholder_img_src();
				            }
				            ?>
                            <tr>
				                <td class="woocommerce-table__line-item">
					                <img style="max-width:100px;" src="<?php echo $thumbnail; ?>">
					            </td>
					            <td><?php echo $design['name']; ?></td>
					            <td><?php echo $download; ?></td>
				            </tr>
			                <?php
			            }
			            ?>
	                </table>
	                <?php
				}
		    }
	    }
	}
}; 
add_action( 'woocommerce_order_details_after_order_table', 'wcdp_add_custom_design_order', 10, 1 );