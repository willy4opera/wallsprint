<?php
$id = get_the_ID();
if(is_front_page() && is_home()) $id = (int)get_option( 'page_on_front' );
if(!is_front_page() && is_home()) $id = (int)get_option( 'page_for_posts' );
if($id) $title  = get_the_title($id);
else $title = esc_html__("Blog",'bw-printxtore');
if(!empty($title_filter)){
    $title =$title_filter;
}
if(is_archive()){
    if(function_exists('is_shop')&&!is_shop())
    $title = get_the_archive_title();
} 
if(is_single()) $title = esc_html__("Blog",'bw-printxtore');
if(is_singular('product')) $title = esc_html__("Product",'bw-printxtore');
if(is_search()) $title = esc_html__("Search Result",'bw-printxtore');
if(function_exists('woocommerce_page_title') && is_shop()) $title = woocommerce_page_title(false);

if(get_post_meta(get_the_ID(),'show_title_page',true) == '0'){
    $title = false;
}
if(!empty($title)) echo '<h1 class="entry-title title60 color-title font-semibold font-title">'.$title.'</h1>';