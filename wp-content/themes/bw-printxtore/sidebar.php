<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package BzoTech-Framework
 */
?>
<?php
$sidebar = bzotech_get_sidebar();
if ( is_active_sidebar( $sidebar['id']) && $sidebar['position'] != 'no' ):?>

	<div class="bzotech-col-lg-3 bzotech-col-md-4 bzotech-col-sm-4 bzotech-col-xs-12 sidebar-type-<?php echo esc_attr($sidebar['style'])?> sidebar-position-<?php echo esc_attr($sidebar['position'])?>">
		
		<div class="sidebar sidebar-<?php echo esc_attr($sidebar['position'])?>">
			<?php if($sidebar['style'] == 'style2') echo '<div class="widget-group group-filters-shop">';?>
		    <?php dynamic_sidebar($sidebar['id']); ?>
		    <?php if($sidebar['style'] == 'style2') echo '</div>'?>
		</div>
	</div>
<?php endif;?>