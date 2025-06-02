<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if(!isset($wp_query)) global $wp_query;

if ( $wp_query->max_num_pages <= 1 ) {
	return;
}
if(!isset($paged)) $paged = get_query_var( 'paged' );
$prev_text = '<i class="las la-long-arrow-alt-left" aria-hidden="true"></i>';
$next_text = '<i class="las la-long-arrow-alt-right" aria-hidden="true"></i>';
if(is_rtl()){
	$prev_text = '<i class="las la-long-arrow-alt-right" aria-hidden="true"></i>';
	$next_text = '<i class="las la-long-arrow-alt-left" aria-hidden="true"></i>';
}
?>
<div class="woocommerce-pagination text-center">
<nav class="pagi-nav ">
	<?php
		echo paginate_links( apply_filters( 'woocommerce_pagination_args', array(
			'base'         => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
			'format'       => '',
			'add_args'     => false,
			'current'      => max( 1, $paged ),
			'total'        => $wp_query->max_num_pages,
			'prev_text'    => $prev_text,
			'next_text'    => $next_text,
			'end_size'     => 2,
            'mid_size'     => 1
		) ) );
	?>
</nav>
</div>
