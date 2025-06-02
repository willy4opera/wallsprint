<?php

// Init shortcode [wcdp_my_designs] 
function wcdp_my_designs_init(){        
	ob_start();
    if(is_user_logged_in()){
		$user_ID = get_current_user_id();		
	    $get_designs_list = get_user_meta($user_ID, '_wcdp_designs_save_user_list', true);
        if($get_designs_list){
			wcdp_add_dependencies_shortcode_my_designs(); ?>
			<div class="woocommerce">
			    <table class="shop_table" id="wcdp-save-user-table">
			        <tr>
		                <td><b><?php _e( "Date", "wcdp" ) ?></b></td>
		                <td><b><?php _e( "Preview Design", "wcdp" ) ?></b></td>
		                <td><b><?php _e( "Design Name", "wcdp" ) ?></b></td>
		                <td><b><?php _e( "Product ID", "wcdp" ) ?></b></td>	
		                <td><b><?php _e( "Options", "wcdp" ) ?></b></td>
			        </tr>	
			        <?php					
			        foreach($get_designs_list as $designKey => $designVal){
						$templateID = 0;
						$designName = 0;
						$productID = $designVal;
						if(is_array($designVal)){
	                        $templateID = $designVal['designID'];
							$productID = $designVal['productID'];
							$designName = isset($designVal['designName']) && !empty($designVal['designName']) ? $designVal['designName'] : 0;
						}
						$pageID = wcdp_check_publish_design_page($productID, $templateID);
					    $getTitle = empty($designName) ? get_the_title($productID) : $designName;
						$designTitle = $getTitle ? $getTitle : '---';
     			        ?>
				        <tr class="dp-my-design-row">
		                    <td><?php echo date("Y-m-d H:i", $designKey / 1000); ?></td>
		                    <td><img src="<?php echo WCDP_URL_UPLOADS .'/save-user/'. $designKey .'/cover.jpg'; ?>"/></td>
		                    <td class="dp-name-my-design"><?php echo $designTitle; ?></td>
		                    <td><?php echo $productID; ?></td> 
		                    <td>
								<div class="dp-my-design-contain" page-id="<?php echo $pageID; ?>" pid="<?php echo $productID; ?>" sid="<?php echo $designKey; ?>">	
							        <a class="button dp-load-my-design" href="javascript:void(0)"><?php _e( "LOAD", "wcdp" ) ?></a>
									<a class="button dp-rename-my-design" href="javascript:void(0)"><?php _e( "RENAME", "wcdp" ) ?></a>
								    <a class="button dp-remove-my-design" href="javascript:void(0)"><?php _e( "DELETE", "wcdp" ) ?></a>
								</div>
							</td>
				        </tr>
                        <?php
			        } ?>
		        </table>
			</div>
			<?php 
        } else{
			?> <p><?php _e( "No saved designs found.", "wcdp" ) ?></p> <?php
	    }
	} else{
		?> <p><?php _e('You must','wcdp'); ?><a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>"> <?php _e('login','wcdp'); ?></a> <?php _e('to access your designs.','wcdp'); ?></p> <?php
	}
	return ob_get_clean();
}
add_shortcode( 'wcdp_my_designs', 'wcdp_my_designs_init' );

// Add to shortcode my designs custom styling & javascript
function wcdp_add_dependencies_shortcode_my_designs(){	
	$style = get_option('wcdp-settings-style');	?>	
	<style type="text/css">
	    #wcdp-save-user-table .dp-my-design-row img{
			width: 100px;
            height: auto;
		}
	    #wcdp-save-user-table .dp-my-design-row td.dp-name-my-design{
            text-overflow: ellipsis;
            overflow: hidden;
	        white-space: nowrap;
			max-width: 250px;
		}
        .jBox-Confirm .jBox-Confirm-footer{
	        font-size: 14px;
        }
        .jBox-Confirm-button-cancel{
	        border-right: 1px solid #b6babd;
        }
        .jBox-Confirm .jBox-content{
	        border-bottom: 1px solid #b6babd;
        }
        .jBox-Confirm-button-cancel, .jBox-Confirm-button-submit{
	        color: <?php echo $style['buttons_color_jbox']['RGB'];?> !important;
	        background: <?php echo $style['buttons_bg_jbox']['RGB'];?> !important;
	    }
        .jBox-Confirm-button-cancel:hover, .jBox-Confirm-button-cancel:active,
	    .jBox-Confirm-button-submit:hover, .jBox-Confirm-button-submit:active{
	        color: <?php echo $style['buttons_color_hover_jbox']['RGB'];?> !important;
	        background: <?php echo $style['buttons_bg_hover_jbox']['RGB'];?> !important;
	    }
	</style>	
    <script type="text/javascript">	
        var dp_trl = <?php echo json_encode(wcdp_editor_translations()); ?>;	
	    (function($){
	        'use strict';
            $(document).ready(function(){				
                $('.dp-load-my-design').click(function(e){
	                e.preventDefault();
					var el = $(this).parent();
			        if(el.attr('page-id')){
						wcdp_jbox_confirm(dp_trl.load, function(){
                            window.open('<?php echo get_site_url(); ?>/?page_id='+ el.attr('page-id') +'&dp_mode=save&design_id='+ el.attr('sid'), '_self');
                        });						 
					} else{
				        new jBox('Modal',{
						    closeButton: 'box',
						    content: dp_trl.product_id +' <b>"'+ el.attr('pid') +'"</b> '+ dp_trl.product_unavailable
				        }).open();						
					}
				});
                $('.dp-rename-my-design').click(function(e){
	                e.preventDefault();
					var el = $(this).parents('.dp-my-design-row'),
					    sid = el.find('.dp-my-design-contain').attr('sid'),
					    dsn = el.find('.dp-name-my-design'),
						old = dsn.html();
			        new jBox('Confirm',{
				        title: dp_trl.design_new_title,
			            content: '<input type="text" class="dp-design-name" value="'+ old +'"/>',
				        closeOnEsc: true,
				        closeButton: 'title',
	                    cancelButton: dp_trl.cancel,
	                    confirmButton: dp_trl.save_design,
                        confirm: function(){
						    var getDesgName = $('#' + this.id).find('.dp-design-name').val(),
                                szDesignName = getDesgName.replace(/([\/\\\"\*\?\|\<\>\:])/g, ''),
							    designName = szDesignName.trim().length ? szDesignName : dp_trl.untitled;
						    if(old != designName){
						        var jbox_modal = new jBox('Modal',{
                                    ajax: {
                                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
					                    data: {
                                            'action': 'wcdp_rename_my_design_ajax',
    	                                    'designID': sid,
									        'designName': designName
        		                        },			
                                        reload: 'strict',
					                    success: function(response){
						                    var data = JSON.parse(response);
                                            if(data.success){
								                dsn.html(designName);
									            jbox_modal.destroy();
					                        } else{
								                this.setContent(dp_trl.error_process);										
								                setTimeout(function(){
							                        jbox_modal.destroy();
								                }, 2000);
			                                }
					                    }
				                    },
					                closeOnClick: false,
                                }).open();
                            }
				        },
                        onCloseComplete: function(){
                            this.destroy();
                        }
			        }).open();
				});
                $('.dp-remove-my-design').click(function(e){
	                e.preventDefault();
					var el = $(this).parent(),
					sid = el.attr('sid');					
				    wcdp_jbox_confirm(dp_trl.delete, function(){
                        var jbox_modal = new jBox('Modal',{
                            ajax: {
                                url: '<?php echo admin_url('admin-ajax.php'); ?>',
					            data: {
                                    'action': 'wcdp_remove_canvas_design_ajax',
    	                            'designID': sid	
        		                },			
                                reload: 'strict',
					            success: function(response){
                                    if(response == 'delete_successful'){
					    	            this.setContent(dp_trl.delete_success);
				    	                location.reload(true);
					                } else{
								        this.setContent(dp_trl.error_process);										
								        setTimeout(function(){
							                jbox_modal.destroy();
								        }, 2000);
			                        }				
					            }
				            },
					        closeOnClick: false,
                        }).open();	
                    });
				});				
			    function wcdp_jbox_confirm(content, confirm){
			        new jBox('Confirm',{
			            content: content,
						closeOnClick: 'body',
	                    cancelButton: dp_trl.cancel,
	                    confirmButton: dp_trl.confirm,
                        confirm: confirm,
                        onCloseComplete: function(){
                            this.destroy();
                        }
			        }).open();				
			    }				
			});
		})(jQuery);
    </script>
	<?php
}