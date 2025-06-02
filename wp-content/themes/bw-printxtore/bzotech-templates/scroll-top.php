<?php
$scroll_top = bzotech_get_value_by_id('show_scroll_top');
if($scroll_top == '1'){?>
	<a href="#" class="scroll-top"><i aria-hidden="true" class=" las la-location-arrow"></i></a>
<?php }else if($scroll_top == '2'){ ?>
	<div class="scroll-progress-wrap scroll-top-style2">
		<i class="las la-arrow-up"></i>
		<svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
			<path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"/>
		</svg>
	</div>
	<?php
}