# Color Override Documentation

## Overview
This document details the process of replacing the orange hover color (#FF7831) with the theme's main red color (#e32131) throughout the Wallsprint WordPress site.

## Implementation Details

### Custom Plugin Approach
A custom plugin named "Custom Color Override" was created to replace all instances of #FF7831 with #e32131. This approach was chosen because:
- It doesn't modify core theme files, making it upgrade-safe
- It can be easily enabled/disabled as needed
- It provides a centralized place for color overrides
- It uses high-specificity CSS to ensure the overrides take effect

### Plugin Code
```php
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
```

### CSS Selectors Explanation
The plugin uses several CSS selectors to target different instances of the orange color:

1. **Inline Style Overrides**: 
   ```css
   [style*="#FF7831"], [style*="#ff7831"] { color: #e32131 !important; }
   ```
   - Targets any element with inline styles containing the orange color
   - Case-insensitive matching for both uppercase and lowercase hex values

2. **Element-Specific Overrides**:
   ```css
   .elementor-element a:hover, .button-account-e:hover i, ... { color: #e32131 !important; }
   ```
   - Targets specific elements known to use the orange color on hover
   - Includes icons, links, and special theme elements

3. **Background and Border Overrides**:
   ```css
   [style*="background-color: #FF7831"], ... { background-color: #e32131 !important; }
   [style*="border-color: #FF7831"], ... { border-color: #e32131 !important; }
   ```
   - Targets elements using the orange color for backgrounds or borders
   - Handles different CSS property formats

### Menu Icons Integration
Additionally, the "Menu Item Icons" plugin was updated to use the theme's main color (#e32131) for its icons:

```javascript
// Using the theme's main color
var mainColor = '#e32131';
var iconStyle = 'margin-right: 8px; color: ' + mainColor + ';';
```

## Installation and Activation

1. The plugin files were placed in:
   ```
   wp-content/plugins/custom-color-override/
   ```

2. The plugin was activated via WP-CLI:
   ```bash
   wp plugin activate custom-color-override --allow-root
   ```

3. Cache was flushed to ensure immediate visibility of changes:
   ```bash
   wp cache flush --allow-root
   ```

## Color Information

### Original Color
- Hex: #FF7831
- RGB: rgb(255, 120, 49)
- Description: Orange (used for hover states)

### Replacement Color
- Hex: #e32131
- RGB: rgb(227, 33, 49)
- Description: Red (main theme color)

## Testing
After implementation, hover states on the following elements should now use the red color instead of orange:
- Navigation menu items
- Account icon
- Cart icon
- Buttons
- Links

## Reverting Changes
To revert these changes, simply deactivate the plugin:
```bash
wp plugin deactivate custom-color-override --allow-root
```

## File Locations
- Plugin main file: `/var/www/html/wallsprint/wp-content/plugins/custom-color-override/custom-color-override.php`
- Documentation: `/var/www/html/wallsprint/color_override_documentation.md`
