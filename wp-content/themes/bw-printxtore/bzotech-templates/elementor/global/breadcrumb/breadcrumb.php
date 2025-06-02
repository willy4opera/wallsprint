<?php
namespace Elementor;
extract($settings);
if(empty($step))$step = '<span class="step-bread-crumb"><i class="title14 las la-angle-right"></i></span>';
?>
<div class="elbzotech-bread-crumb-global bread-crumb-global-<?php echo esc_attr($style)?>">
	<div class="elbzotech-bread-crumb-global__content container-flex-e flex-wrapper">
		<?php
			if(!bzotech_is_woocommerce_page()){
                if(function_exists('bcn_display')) bcn_display();
                else bzotech_breadcrumb($step,'bread-crumb-e');
            }
            else {
            	if(function_exists('woocommerce_breadcrumb')){
	            	woocommerce_breadcrumb(array(
	            	'delimiter'		=> $step,
	            	'wrap_before'	=> '',
	            	'wrap_after'	=> '',
	            	'before'      	=> '',
					'after'       	=> '',
	            	));
	            }
            }
        ?>
	</div>
</div>