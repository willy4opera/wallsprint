<?php
/**
 * frametheme functions and definitions
 *
 * @package WordPress
 * @subpackage frametheme
 * @since frametheme 1.0
 */
add_action('init', function(){
	load_theme_textdomain( 'bw-printxtore', get_template_directory() . '/languages' );
});
/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}
get_template_part( 'inc/class-inc' );