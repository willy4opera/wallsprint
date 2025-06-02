<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

if(! is_admin())  add_action('wp_enqueue_scripts', 'emicons_wp_enqueue_scripts');

function emicons_wp_enqueue_scripts (){

    // Enqueue Dashicons
    wp_enqueue_style('dashicons');

    wp_register_style( 'emicons-font-awesome', EMICONS_PL_URL . 'admin/assets/css/fontawesome.all.min.css', array(), EMICONS_VERSION );
    wp_register_style( 'emicons-accordion-style', EMICONS_PL_URL . 'public/assets/css/emicons-accordion.css', array(), EMICONS_VERSION  );
    wp_register_style( 'emicons-style', EMICONS_PL_URL . 'public/assets/css/emicons.css', array(), EMICONS_VERSION );
    wp_register_script( 'emicons-public', EMICONS_PL_URL . 'public/assets/js/emicons-menu-public.js', array('jquery'), EMICONS_VERSION, TRUE );
    wp_register_script( 'emicons-accordion-script', EMICONS_PL_URL . 'public/assets/js/emicons-accordion.js', array('jquery'), EMICONS_VERSION, TRUE );

    wp_enqueue_style( 'emicons-font-awesome' );
    wp_enqueue_style( 'emicons-accordion-style' );
    wp_enqueue_style( 'emicons-style' );
    wp_enqueue_script( 'emicons-public'  );
    wp_enqueue_script( 'emicons-accordion-script' );

}

