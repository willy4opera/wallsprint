<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
$view          = bzotech_get_option('shop_default_style','grid');
$grid_type      = bzotech_get_option('shop_grid_type');
$item_style     = bzotech_get_option('shop_grid_item_style');
$item_style_list= bzotech_get_option('shop_list_item_style');
$column         = bzotech_get_option('shop_grid_column',3);
$size           = bzotech_get_option('shop_grid_size');
$size_list      = bzotech_get_option('shop_list_size');
$thumbnail_hover_animation      = bzotech_get_option('shop_thumb_animation');
$get_type = $type_active = $view;
if(isset($_GET['type']) && $_GET['type']) 
    $get_type = sanitize_text_field($_GET['type']);
if($get_type !== 'list'){
    $type_active = 'grid';
} else $type_active = 'list';
if($get_type == 'grid-2col'){
    $column = 2;
}else if($get_type == 'grid-3col'){
    $column = 3;
}
$size = bzotech_get_size_crop($size);
$size_list = bzotech_get_size_crop($size_list);
$slug = $item_style;
if($view == 'grid' && $type_active == 'list'){
    $view = $type_active;
    $slug = $item_style_list;
}

$item_wrap = 'class="list-col-item item-grid-product-'.$item_style.' list-'.$column.'-item list-2-item-tablet-extra list-2-item-tablet list-2-item-mobile"';
$item_inner = 'class="item-product"';
$button_icon_pos = $button_icon = $button_text = $column = $item_thumbnail = $item_quickview = $item_title = $item_rate = $item_price = $item_button = $item_label=$item_flash_sale= $item_brand=$item_countdown='';
$item_thumbnail = bzotech_get_value_by_id('item_thumbnail');
$item_quickview = bzotech_get_value_by_id('item_quickview');
$item_title = bzotech_get_value_by_id('item_title');
$item_rate = bzotech_get_value_by_id('item_rate');
$item_price = bzotech_get_value_by_id('item_price');
$item_button = bzotech_get_value_by_id('item_button');
$item_label = bzotech_get_value_by_id('item_label');
$item_countdown = bzotech_get_value_by_id('item_countdown');
$item_brand = bzotech_get_value_by_id('item_brand');
$item_flash_sale = bzotech_get_value_by_id('item_flash_sale');
$attr = array(
	'item_wrap'         => $item_wrap,
    'item_inner'        => $item_inner,
    'button_icon_pos'   => $button_icon_pos,
    'button_icon'       => $button_icon,
    'button_text'       => $button_text,
    'size'              => $size,
    'size_list'         => $size_list,
    'type_active'       => $type_active,
    'view'              => $view,
    'column'            => $column,
    'item_style'        => $item_style,
    'item_style_list'   => $item_style_list,
    'animation'         => $thumbnail_hover_animation,
    'item_thumbnail'    => $item_thumbnail,
    'item_quickview'    => $item_quickview,
    'item_title'        => $item_title,
    'item_rate'         => $item_rate,
    'item_price'        => $item_price,
    'item_button'       => $item_button,
    'item_label'        => $item_label,
    'get_type'          => $get_type,
    'item_countdown'          => $item_countdown,
    'item_brand'          => $item_brand,
    'item_flash_sale'          => $item_flash_sale,
	);
?>
<?php bzotech_get_template_woocommerce('loop/'.$view.'/'.$view,$slug,$attr,true);?>
