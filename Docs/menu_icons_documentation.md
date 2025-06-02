# Menu Icons Documentation

## Overview
This document explains how icons were added to the main navigation menu items in the Wallsprint WordPress theme using a custom plugin approach.

## Menu Items and Icons
The following menu items have been enhanced with Font Awesome icons:

| Menu Item     | Icon                  | Font Awesome Class |
|---------------|:---------------------:|-------------------|
| HOME          | <i class="fa fa-home"></i> | `\f015` (home)    |
| BLOG          | <i class="fa fa-blog"></i> | `\f781` (blog)    |
| CONTACT       | <i class="fa fa-envelope"></i> | `\f0e0` (envelope) |
| LOGIN         | <i class="fa fa-sign-out-alt"></i> | `\f2f6` (sign-in) |
| PROMOTIONALS  | <i class="fa fa-bullhorn"></i> | `\f0a1` (bullhorn) |
| RESOURCES     | <i class="fa fa-book"></i> | `\f02d` (book)    |

## Implementation Details

### Custom Plugin Approach
A custom plugin named "Menu Item Icons" was created to add icons to the menu items. This approach offers several advantages:
- Changes persist through theme updates
- Easy enabling/disabling of icons
- Clean separation of custom code from theme files
- No modification of core theme files

### How It Works
The plugin uses CSS pseudo-elements (`:before`) to insert icons before the menu item text. Each menu item is targeted by its specific ID in the DOM.

### Plugin Code
```php
<?php
/*
Plugin Name: Menu Item Icons
Description: Adds Font Awesome icons to menu items
Version: 1.0
Author: Admin
*/

// Add CSS for menu icons
function menu_item_icons_css() {
    echo '<style>
        /* Add icons to menu items using Font Awesome */
        
        /* HOME icon */
        #nav-menu-item-24856 > a:before {
            content: "\f015"; /* home icon */
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            margin-right: 8px;
            display: inline-block;
            vertical-align: middle;
            line-height: inherit;
            font-size: 0.9em; /* Slightly smaller than text */
        }
        
        /* BLOG icon */
        #nav-menu-item-24854 > a:before {
            content: "\f781"; /* blog icon */
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            margin-right: 8px;
            display: inline-block;
            vertical-align: middle;
            line-height: inherit;
            font-size: 0.9em;
        }
        
        /* CONTACT icon */
        #nav-menu-item-25138 > a:before {
            content: "\f0e0"; /* envelope icon */
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            margin-right: 8px;
            display: inline-block;
            vertical-align: middle;
            line-height: inherit;
            font-size: 0.9em;
        }
        
        /* LOGIN icon */
        #nav-menu-item-25142 > a:before {
            content: "\f2f6"; /* sign-in icon */
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            margin-right: 8px;
            display: inline-block;
            vertical-align: middle;
            line-height: inherit;
            font-size: 0.9em;
        }
        
        /* PROMOTIONALS icon */
        #nav-menu-item-25143 > a:before {
            content: "\f0a1"; /* bullhorn icon */
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            margin-right: 8px;
            display: inline-block;
            vertical-align: middle;
            line-height: inherit;
            font-size: 0.9em;
        }
        
        /* RESOURCES icon */
        #nav-menu-item-25144 > a:before {
            content: "\f02d"; /* book icon */
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            margin-right: 8px;
            display: inline-block;
            vertical-align: middle;
            line-height: inherit;
            font-size: 0.9em;
        }
    </style>';
}
add_action('wp_head', 'menu_item_icons_css', 999);
```

## Proper Inline Alignment
To ensure the icons are perfectly aligned inline with the menu text, the following CSS properties are crucial:

- `display: inline-block` - Makes the icon behave like an inline element while allowing height/width settings
- `vertical-align: middle` - Aligns the icon vertically with the middle of the text
- `line-height: inherit` - Ensures the icon maintains the same line height as the menu text
- `font-size: 0.9em` - Makes the icon slightly smaller than the text for better visual balance

These properties ensure the icons align perfectly with the text and maintain consistent spacing across different screen sizes.

## Installation and Activation

1. The plugin files were placed in:
   ```
   wp-content/plugins/menu-item-icons/
   ```

2. The plugin was activated via WP-CLI:
   ```bash
   wp plugin activate menu-item-icons --allow-root
   ```

3. Cache was flushed to ensure immediate visibility of changes:
   ```bash
   wp cache flush --allow-root
   ```

## Customization Options

### Changing Icons
To change an icon, simply update the `content` property with the Unicode value of the desired Font Awesome icon. For example:
```css
content: "\f015"; /* This is the home icon */
```

### Styling Options
The following CSS properties can be adjusted to customize the appearance of the icons:
- `margin-right`: Controls spacing between icon and text
- `font-size`: Changes icon size
- `color`: Changes icon color
- `font-weight`: Adjusts icon weight (900 for solid icons)

## Reverting Changes
To remove the icons, simply deactivate the plugin:
```bash
wp plugin deactivate menu-item-icons --allow-root
```

## File Locations
- Plugin main file: `/var/www/html/wallsprint/wp-content/plugins/menu-item-icons/menu-item-icons.php`
- Documentation: `/var/www/html/wallsprint/menu_icons_documentation.md`
