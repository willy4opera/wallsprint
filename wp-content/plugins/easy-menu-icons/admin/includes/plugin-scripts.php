<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.
add_action('admin_enqueue_scripts', 'emicons_admin_enqueue_scripts');
function emicons_admin_enqueue_scripts (){

    wp_enqueue_media();
    wp_enqueue_style( 'wp-color-picker');
    wp_enqueue_script( 'wp-color-picker');
    wp_enqueue_style( 'dashicons' );

    // Load fontawesome
    wp_register_style( 'emicons-font-awesome', EMICONS_PL_URL . 'admin/assets/css/fontawesome.all.min.css', array(), EMICONS_VERSION );
    wp_register_style( 'emicons-admin-style', EMICONS_PL_URL . 'admin/assets/css/emicons-admin.css', array(), EMICONS_VERSION );
    wp_register_script( 'emicons-jquery-qucik-search', EMICONS_PL_URL . 'admin/assets/js/jquery.quicksearch.js', array('jquery'), EMICONS_VERSION, TRUE );
    wp_register_script( 'emicons-admin', EMICONS_PL_URL . 'admin/assets/js/emicons-admin.js', array('jquery'), EMICONS_VERSION, TRUE );
    


    wp_enqueue_style( 'emicons-font-awesome' );
    wp_enqueue_style( 'emicons-admin-style' );
    wp_enqueue_script( 'emicons-jquery-qucik-search' );
    wp_enqueue_script( 'emicons-admin' );
    

    wp_localize_script(
            'emicons-admin', 
            'emicons_ajax',
                [
                    'ajaxurl'          => admin_url( 'admin-ajax.php' ),
                    'adminURL'         => admin_url(),
                    'elementorURL'     => admin_url( 'edit.php?post_type=elementor_library' ),
                    'nonce'            => wp_create_nonce('emicons_nonce'),
                    'version'          => EMICONS_VERSION,
                    'pluginURL'        => plugin_dir_url( __FILE__ ),
                ]
        );

}