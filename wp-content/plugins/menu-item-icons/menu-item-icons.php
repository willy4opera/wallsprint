<?php
/*
Plugin Name: Menu Item Icons
Description: Adds Font Awesome icons to menu items
Version: 1.0
Author: Admin
*/

// Add JavaScript to insert icons directly into the menu items
function menu_item_icons_js() {
    // Get WordPress home URL dynamically
    $home_url = esc_js(home_url('/'));
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Using the theme's main color
        var mainColor = '#e32131';
        var iconStyle = 'margin-right: 8px; color: ' + mainColor + ';';
        var homeUrl = '<?php echo $home_url; ?>';
        
        // Add CSS to completely disable home dropdown and hover effects
        var style = document.createElement('style');
        style.textContent = `
            #nav-menu-item-24856 .sub-menu,
            #nav-menu-item-24856.menu-item-has-children .sub-menu,
            #nav-menu-item-24856 > .sub-menu,
            #nav-menu-item-24856 .preview-area,
            #nav-menu-item-24856:hover .sub-menu,
            #nav-menu-item-24856:hover .preview-area {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
                pointer-events: none !important;
                height: 0 !important;
                width: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
                overflow: hidden !important;
                position: absolute !important;
                clip: rect(0,0,0,0) !important;
            }
            #nav-menu-item-24856 > a::after,
            #nav-menu-item-24856:hover > a::after {
                display: none !important;
            }
            #nav-menu-item-24856 {
                pointer-events: auto !important;
            }
            #nav-menu-item-24856 > a {
                pointer-events: auto !important;
                cursor: pointer !important;
            }
        `;
        document.head.appendChild(style);
        
        // Add icon to HOME menu item and completely disable dropdown
        var homeMenuItem = document.querySelector('#nav-menu-item-24856 > a');
        var homeMenuLi = document.querySelector('#nav-menu-item-24856');
        if (homeMenuItem && homeMenuLi) {
            // Remove all dropdown and preview related classes
            homeMenuLi.className = homeMenuLi.className.replace(/menu-item-has-children|dropdown|menu-item-object-custom|menu-item-has-preview/g, '').trim();
            
            // Remove dropdown elements
            ['arrow', 'sub-menu', 'preview-area', 'dropdown-content'].forEach(function(className) {
                var element = homeMenuLi.querySelector('.' + className);
                if (element) element.remove();
            });

            homeMenuItem.innerHTML = '<i class="fas fa-home" style="' + iconStyle + '"></i> Home';
            homeMenuItem.href = homeUrl;
            
            // Ensure direct navigation without preview
            homeMenuItem.onclick = function(e) {
                e.preventDefault();
                window.location.href = homeUrl;
                return false;
            };

            // Remove all hover events
            homeMenuLi.onmouseenter = null;
            homeMenuLi.onmouseleave = null;
            homeMenuItem.onmouseenter = null;
            homeMenuItem.onmouseleave = null;
        }
        
        // [Rest of the menu items remain unchanged]
        // Add icon to BLOG menu item
        var blogMenuItem = document.querySelector('#nav-menu-item-24854 > a');
        if (blogMenuItem) {
            blogMenuItem.innerHTML = '<i class="fas fa-blog" style="' + iconStyle + '"></i> ' + blogMenuItem.innerHTML;
        }

        // Add icon to ABOUT menu item
        var aboutMenuItem = document.querySelector('#nav-menu-item-25142 > a');
        if (aboutMenuItem) {
            aboutMenuItem.innerHTML = '<i class="fas fa-info-circle" style="' + iconStyle + '"></i> About';
            aboutMenuItem.href = homeUrl + "about-us/";
        }
        
        // Add icon to CONTACT menu item
        var contactMenuItem = document.querySelector('#nav-menu-item-25138 > a');
        if (contactMenuItem) {
            contactMenuItem.innerHTML = '<i class="fas fa-envelope" style="' + iconStyle + '"></i> ' + contactMenuItem.innerHTML;
        }
        
        // Add icon to PROMOTIONALS menu item
        var promoMenuItem = document.querySelector('#nav-menu-item-25143 > a');
        if (promoMenuItem) {
            promoMenuItem.innerHTML = '<i class="fas fa-bullhorn" style="' + iconStyle + '"></i> ' + promoMenuItem.innerHTML;
        }
        
        // Add icon to RESOURCES menu item
        var resourcesMenuItem = document.querySelector('#nav-menu-item-25144 > a');
        if (resourcesMenuItem) {
            resourcesMenuItem.innerHTML = '<i class="fas fa-book" style="' + iconStyle + '"></i> ' + resourcesMenuItem.innerHTML;
        }

        // Add icon to SUPPORT menu item
        var supportMenuItem = document.querySelector('#nav-menu-item-support > a');
        if (supportMenuItem) {
            supportMenuItem.innerHTML = '<i class="fas fa-headset" style="' + iconStyle + '"></i> Support: support@wallsprint.com';
            supportMenuItem.href = 'mailto:support@wallsprint.com';
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'menu_item_icons_js', 999);
