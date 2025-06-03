<?php
/*
Plugin Name: Logo Left Position
Description: Moves the logo to the extreme left side of the header (mobile only)
Version: 1.3
Author: Admin
*/

// Add CSS to position the logo to the extreme left on mobile devices only
function logo_left_position_css() {
    // Only output CSS for mobile devices - we use PHP to detect
    if (wp_is_mobile()) {
        echo '<style>
            /* Logo positioning - move to the extreme left with reduced height */
            .logo-default {
                float: left !important;
                margin-right: auto !important;
                margin-left: -80px !important; /* More extreme negative margin */
                position: relative !important;
                left: -30px !important; /* Additional left positioning */
            }

            /* Elementor-based logo positioning */
            .elementor-widget-bzotech-logo-global {
                float: left !important;
                margin-right: auto !important;
                margin-left: -80px !important; /* More extreme negative margin */
                position: relative !important;
                left: -30px !important; /* Additional left positioning */
            }

            /* Specific targeting for post 12195 logo */
            .bzotech-header-page-12195 .logo-default,
            .post-12195 .logo-default,
            .bzotech-header-page-12195 .elementor-widget-bzotech-logo-global,
            .post-12195 .elementor-widget-bzotech-logo-global {
                float: left !important;
                margin-right: auto !important;
                margin-left: -80px !important; /* More extreme negative margin */
                position: relative !important;
                left: -30px !important; /* Additional left positioning */
            }

            /* For logos within Elementor sections */
            .elementor-section .elementor-widget-image:has(img[alt*="logo"]),
            .elementor-section .elementor-widget-image:has(img[src*="logo"]) {
                float: left !important;
                margin-right: auto !important;
                margin-left: -80px !important; /* More extreme negative margin */
                position: relative !important;
                left: -30px !important; /* Additional left positioning */
            }

            /* Ensure parent containers don\'t clip the logo */
            .logo-default, 
            .elementor-widget-bzotech-logo-global,
            .elementor-section .elementor-widget-image:has(img[alt*="logo"]),
            .elementor-section .elementor-widget-image:has(img[src*="logo"]) {
                overflow: visible !important;
            }

            /* Make sure container allows overflow */
            .bzotech-container,
            .elementor-container,
            .flex-wrapper,
            .header-page,
            .header {
                overflow: visible !important;
            }

            /* Make sure logo images remain responsive with reduced height */
            .logo-default img,
            .elementor-widget-bzotech-logo-global img,
            img[alt*="logo"],
            img[src*="logo"] {
                max-width: 100%;
                height: auto;
                max-height: 40px !important; /* Reduced height */
                width: auto !important; /* Keep aspect ratio */
            }

            /* Direct targeting for the logo in header content */
            a img[src*="wellsprintlogo"] {
                margin-left: -80px !important;
                max-height: 40px !important; /* Reduced height */
                width: auto !important; /* Keep aspect ratio */
            }

            /* Media query for responsive adjustments */
            @media (max-width: 480px) {
                .logo-default, 
                .elementor-widget-bzotech-logo-global {
                    margin-left: -40px !important; /* Less extreme on very small mobile */
                    left: -15px !important;
                }
                
                .logo-default img,
                .elementor-widget-bzotech-logo-global img,
                img[alt*="logo"],
                img[src*="logo"] {
                    max-height: 35px !important; /* Even smaller on mobile */
                }
            }
        </style>';
    }
}
add_action('wp_head', 'logo_left_position_css', 999);
