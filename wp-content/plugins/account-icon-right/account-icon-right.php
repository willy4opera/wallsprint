<?php
/*
Plugin Name: Account Icon Right Position
Description: Moves only the account icon to the right without affecting other components
Version: 1.0
Author: Admin
*/

// Add minimal CSS to move only the account icon to the right
function account_icon_right_css() {
    echo '<style>
        /* Target only the account icon widget and position it at the right */
        .elementor-element-883753a {
            float: right !important;
            margin-left: 120px !important; /* Extremely large margin */
            margin-right: -6px !important; /* Larger negative margin */
        }
    </style>';
}
add_action('wp_head', 'account_icon_right_css', 999);
