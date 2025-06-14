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

/**
 * Enqueue crypto polyfill script
 */
function bw_printxtore_enqueue_crypto_polyfill() {
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
    wp_enqueue_script(
        'crypto-polyfill',
        get_template_directory_uri() . '/js/crypto-polyfill' . $suffix . '.js',
        array(),
        '1.0.0',
        false  // Load in header to ensure it's available before other scripts
    );
}
add_action('wp_enqueue_scripts', 'bw_printxtore_enqueue_crypto_polyfill', 0);  // Priority 0 to load early

/**
 * Enqueue custom styles for post 12195 (header)
 * Added: June 3, 2025
 */
function bw_printxtore_enqueue_post_12195_styles() {
    // Check if we're on the correct post/page or anywhere the header might be displayed
    wp_enqueue_style(
        'bw-printxtore-header-12195-style',
        get_template_directory_uri() . '/assets/global/css/custom-style-12195.css',
        array(),
        '1.0.1',
        'all'
    );
}
add_action('wp_enqueue_scripts', 'bw_printxtore_enqueue_post_12195_styles', 20);
