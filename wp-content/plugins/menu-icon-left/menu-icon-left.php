<?php
/*
Plugin Name: Menu Icon Left Position
Description: Moves the menu icon slightly to the left
Version: 1.0
Author: Admin
*/

// Add CSS to move the menu icon to the left
function menu_icon_left_css() {
    echo '<style>
        /* Target the mobile menu toggle button and move it to the left */
        .toggle-mobile-menu {
            position: relative;
            left: -30px !important; /* Move 20px to the left */
            margin-right: auto !important;
        }

        /* Additional targeting for Elementor headers */
        .elementor-widget-bzotech-menu-global .toggle-mobile-menu,
        .header-nav-default .toggle-mobile-menu,
        .bzotech-menu-container .toggle-mobile-menu {
            position: relative;
            left: -00px !important;
            float: left !important;
        }

        /* Ensure proper positioning in flexbox containers */
        .flex-wrapper .toggle-mobile-menu {
            margin-right: auto !important;
            margin-left: 0 !important;
        }

        /* Specific targeting for post 12195 */
        .bzotech-header-page-12195 .toggle-mobile-menu,
        .post-12195 .toggle-mobile-menu {
            position: relative !important;
            left: -20px !important;
            transform: none !important;
        }

        /* Media query for responsive adjustments */
        @media (max-width: 768px) {
            .toggle-mobile-menu {
                left: -10px !important; /* Less movement on mobile */
            }
        }
    </style>';
}
add_action('wp_head', 'menu_icon_left_css', 999);
