<?php if ($links) :
    $links['prev_text'] = '<i class="las la-long-arrow-alt-left"></i>';
    $links['next_text'] = '<i class="las la-long-arrow-alt-right"></i>';
    if(is_rtl()){
    	$links['prev_text'] = '<i class="las la-long-arrow-alt-right"></i>';
    	$links['next_text'] = '<i class="las la-long-arrow-alt-left"></i>';
    }
    ?>
    <div class="pagi-nav-block text-center">
	    <div class="pagi-nav">
	        <?php echo apply_filters('bzotech_output_content',paginate_links($links)); ?>
	    </div>
    </div>
<?php endif;