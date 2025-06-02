# Documentation: Disabling and Restoring the Shopping Cart in bw-printxtore Theme

## Overview
This document details the procedures for disabling the shopping cart display in the bw-printxtore WordPress theme and how to restore it when needed. We used a two-part approach to completely disable the cart display.

## Backup Files Created
- `header1_backup.txt` - Complete backup of the header content (post ID: 12195)
- `header1_modified_v3.txt` - Modified header with cart HTML commented out
- `wp-content/themes/bw-printxtore/bzotech-templates/elementor/global/mini-cart/mini-cart.php.bak` - Backup of the Elementor mini-cart template

## Part 1: Modify the Header Template

### Steps Taken to Disable
1. Created a backup of the header content:
   ```bash
   wp post get 12195 --field=post_content --allow-root > header1_backup.txt
   ```

2. Modified the header content to remove the cart HTML:
   ```bash
   perl -0777 -pe 's|<a href="http://192\.168\.0\.124:8090/cart/">.*?<bdi>&#36;0\.00</bdi>|<!-- Cart removed -->|s' header1_backup.txt > header1_modified_v3.txt
   ```

3. Updated the header post with the modified content:
   ```bash
   wp post update 12195 --post_content="$(cat header1_modified_v3.txt)" --allow-root
   ```

### Steps to Restore
To restore the original header with the cart HTML:
```bash
wp post update 12195 --post_content="$(cat header1_backup.txt)" --allow-root
wp cache flush --allow-root
```

## Part 2: Disable the Elementor Mini-Cart Template

### Steps Taken to Disable
1. Created a backup of the mini-cart template:
   ```bash
   cp wp-content/themes/bw-printxtore/bzotech-templates/elementor/global/mini-cart/mini-cart.php wp-content/themes/bw-printxtore/bzotech-templates/elementor/global/mini-cart/mini-cart.php.bak
   ```

2. Replaced the mini-cart template with an empty version:
   ```bash
   cat > wp-content/themes/bw-printxtore/bzotech-templates/elementor/global/mini-cart/mini-cart.php << 'EOF'
   <?php
   namespace Elementor;
   // Cart display disabled
   ?>
   EOF
   ```

3. Flushed the WordPress cache:
   ```bash
   wp cache flush --allow-root
   ```

### Steps to Restore
To restore the original Elementor mini-cart template:
```bash
cp wp-content/themes/bw-printxtore/bzotech-templates/elementor/global/mini-cart/mini-cart.php.bak wp-content/themes/bw-printxtore/bzotech-templates/elementor/global/mini-cart/mini-cart.php
wp cache flush --allow-root
```

## Complete Restoration
To fully restore all shopping cart functionality:

```bash
# Restore the header template
wp post update 12195 --post_content="$(cat header1_backup.txt)" --allow-root

# Restore the Elementor mini-cart template
cp wp-content/themes/bw-printxtore/bzotech-templates/elementor/global/mini-cart/mini-cart.php.bak wp-content/themes/bw-printxtore/bzotech-templates/elementor/global/mini-cart/mini-cart.php

# Clear all caches
wp cache flush --allow-root
```

## Additional Notes
- The shopping cart display is controlled by both the HTML in the header template and the Elementor mini-cart widget
- Both components needed to be modified to completely disable the cart display
- All original files are preserved as backups with their original extensions plus `.bak` or as text files
- Always flush the cache after making changes to ensure they take effect immediately
- These changes only affect the display of the cart, not the actual cart functionality in WooCommerce

## File Locations
- Header backup: `/root/header1_backup.txt`
- Modified header: `/root/header1_modified_v3.txt`
- Mini-cart template backup: `/var/www/html/wallsprint/wp-content/themes/bw-printxtore/bzotech-templates/elementor/global/mini-cart/mini-cart.php.bak`
