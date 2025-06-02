<?php
/**
 * Product quantity inputs
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version 	7.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
if ( $max_value && $min_value === $max_value ) {
	?>
	<div class="quantity hidden">
		<input type="hidden" id="<?php echo esc_attr( $input_id ); ?>" class="qty" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $min_value ); ?>" />
	</div>
	<?php
} else {
?>
<div class="detail-qty info-qty font-title font-bold title18">
	<a href="#" class="qty-down"><i class="las la-minus"></i></a>
	<input type="text" step="<?php echo esc_attr( $step ); ?>" min="<?php echo esc_attr( $min_value ); ?>" max="<?php echo esc_attr( $max_value ); ?>" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $input_value ); ?>" title="<?php echo esc_attr( 'Qty','bw-printxtore' ) ?>" class="input-text text qty qty-val" size="4" />
	<a href="#" class="qty-up"><i class="las la-plus"></i></a>
</div>
<?php
}