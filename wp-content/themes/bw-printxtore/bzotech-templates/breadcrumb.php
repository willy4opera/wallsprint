<?php
if(!isset($breadcrumb)) $breadcrumb = bzotech_get_value_by_id('bzotech_show_breadrumb');
if(!isset($breadcrumb_page)) $breadcrumb_page = bzotech_get_option('breadcrumb_page');
if(!isset($el_class)) $el_class = 'bread-crumb-';
if($breadcrumb==NULL) $breadcrumb='1';
if($breadcrumb == '1'): 
	if(!empty($breadcrumb_page)){
		echo '<div class="bread-crumb-page-builder">'.Bzotech_Template::get_vc_pagecontent($breadcrumb_page).'</div>';
	}else{
		$bg_html = '';
		if(!empty($bg = bzotech_get_option('bzotech_bg_breadcrumb'))){
		$bg_html = bzotech_add_html_attr('background-image:url('.wp_get_attachment_url($bg['id'],'full').')');
		}
		$step = '<span class="step-bread-crumb"><i class="title14 las la-angle-right"></i></span>';
		?>
		<?php echo '<div class="wrap-bread-crumb '.$el_class.'" '.$bg_html.'>' ; ?>
				<div class="bread-crumb bzotech-container">
					<div class="bread-crumb-row">
						<?php echo bzotech_get_template('entry-title');?>
						<div class="bread-crumb-content">
							<?php
								if(!bzotech_is_woocommerce_page()){
					                if(function_exists('bcn_display')) bcn_display();
					                else bzotech_breadcrumb($step);
					            }
					            else {
					            	if(function_exists('woocommerce_breadcrumb')){
						            	woocommerce_breadcrumb(array(
						            	'delimiter'		=> $step,
						            	'wrap_before'	=> '<div class="woo-breadcrumb">',
						            	'wrap_after'	=> '</div>',
						            	'before'      	=> '<span>',
										'after'       	=> '</span>',
						            	));
						            }
					            }
				            ?>
						</div>
					</div>
				</div>
		</div>
		<?php
	}
	
endif;