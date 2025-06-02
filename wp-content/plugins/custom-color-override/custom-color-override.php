<?php
/*
Plugin Name: Custom Color Override
Description: Replaces #FF7831 color with #e32131
Version: 1.0
Author: Admin
*/

// Add CSS to override the hover color
function custom_color_override_css() {
    echo '<style>
        /* Override all instances of #FF7831 with #e32131 */
        [style*="#FF7831"], 
        [style*="#ff7831"] {
            color: #e32131 !important;
        }
        
        /* Target specific elements that use this color for hover */
        .elementor-element a:hover,
        .button-account-e:hover i,
        i:hover,
        a:hover i,
        .button-account-e .title-account-e:hover {
            color: #e32131 !important;
        }
        
        /* Target borders and backgrounds with this color */
        [style*="background-color: #FF7831"],
        [style*="background-color: #ff7831"],
        [style*="background: #FF7831"],
        [style*="background: #ff7831"] {
            background-color: #e32131 !important;
        }
        
        [style*="border-color: #FF7831"],
        [style*="border-color: #ff7831"] {
            border-color: #e32131 !important;
        }
    </style>';
}
add_action("wp_head", "custom_color_override_css", 9999); // High priority to override other styles
