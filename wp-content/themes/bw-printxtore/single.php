<?php
/**
 * The template for displaying all single posts.
 *
 * @package BzoTech-Framework
 */

get_header();
while ( have_posts() ) : the_post();
	$style = bzotech_get_value_by_id('bzotech_style_post_detail');
	do_action('bzotech_before_main_content');
	bzotech_set_post_view();
	bzotech_get_template_post('single',$style,null,true );
	do_action('bzotech_after_main_content');
endwhile; 
get_footer();