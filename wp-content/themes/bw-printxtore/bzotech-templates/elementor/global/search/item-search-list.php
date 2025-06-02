<?php
if(empty($search_in)) $search_in = '';
echo '<div class="item-search-pro">
    <div class="search-ajax-thumb product-thumb">
        <a href="'.esc_url(get_the_permalink()).'" class="product-thumb-link">
            '.get_the_post_thumbnail(get_the_ID(),array(70,70)).'
        </a>
    </div>
    <div class="search-ajax-title">
    	<h3 class="title14"><a href="'.esc_url(get_the_permalink()).'">'.get_the_title().'</a></h3>';
		if($search_in == 'product') echo '<div class="search-ajax-price">'.bzotech_get_price_html(false).'</div>';
	echo  '</div>';
echo  '</div>';
?>