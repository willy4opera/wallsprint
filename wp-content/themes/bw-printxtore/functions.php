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
