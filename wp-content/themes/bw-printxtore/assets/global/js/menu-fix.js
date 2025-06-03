// Menu fix for disabling dropdown previews
document.addEventListener('DOMContentLoaded', function() {
    // Function to completely disable dropdown on specific menu items
    function disableDropdown(menuItemId) {
        var menuItem = document.getElementById(menuItemId);
        if (!menuItem) return;
        
        // Remove classes that might trigger dropdown
        menuItem.classList.remove('menu-item-has-children');
        menuItem.classList.remove('has-mega-menu');
        menuItem.classList.remove('has-image-preview');
        menuItem.classList.remove('dropdown');
        
        // Remove any dropdown elements
        var previewImage = menuItem.querySelector('.preview-image');
        if (previewImage) {
            previewImage.parentNode.removeChild(previewImage);
        }
        
        var megaMenu = menuItem.querySelector('.mega-menu');
        if (megaMenu) {
            megaMenu.parentNode.removeChild(megaMenu);
        }
        
        var indicator = menuItem.querySelector('.indicator-icon');
        if (indicator) {
            indicator.parentNode.removeChild(indicator);
        }
        
        // Make sure the link works
        var link = menuItem.querySelector('a');
        if (link) {
            // Store original href
            var originalHref = link.getAttribute('href');
            
            // Override any click events
            link.addEventListener('click', function(e) {
                if (originalHref) {
                    window.location.href = originalHref;
                }
            });
        }
        
        // Prevent default events that might show dropdowns
        menuItem.addEventListener('mouseenter', function(e) {
            e.stopPropagation();
        });
        
        menuItem.addEventListener('mouseover', function(e) {
            e.stopPropagation();
        });
    }
    
    // Apply to both menu items
    disableDropdown('nav-menu-item-24856'); // Home
    disableDropdown('nav-menu-item-24854'); // Blog
});
