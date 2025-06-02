<?php
/**
 * Shows an order item header
 *
 * @package Extra Product Options/Admin/Views
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;
if ( isset( $item, $item_id, $order, $header_title, $_product ) ) :
	$item_id      = intval( $item_id );
	$header_title = (string) $header_title;
	$tax_data     = wc_tax_enabled() ? themecomplete_maybe_unserialize( isset( $item['line_tax_data'] ) ? $item['line_tax_data'] : '' ) : false;
	$row_class    = apply_filters( 'woocommerce_admin_html_order_item_class', isset( $class ) && ! empty( $class ) ? $class : '', $item, $order );
	if ( ! empty( $row_class ) ) {
		$row_class = ' ' . $row_class;
	}
	?>
<tr class="tm-order-line<?php echo ' ' . esc_attr( (string) $row_class ); ?>" data-order_item_id="<?php echo esc_attr( (string) $item_id ); ?>">
	<?php echo ( version_compare( WC()->version, '2.6', '>=' ) ) ? '' : '<td class="check-column">&nbsp;</td>'; ?>
	<td class="thumb">&nbsp;</td>
	<td class="tm-c name" data-sort-value="<?php echo esc_attr( $item['name'] ); ?>">
		<div class="tm-view tm-order-header">
			<?php echo esc_html( $header_title ); ?>
		</div>
		<div class="tm-view tm-header">
			<div class="tm-50"><?php esc_html_e( 'Option', 'woocommerce-tm-extra-product-options' ); ?></div>
			<div class="tm-50"><?php esc_html_e( 'Value', 'woocommerce-tm-extra-product-options' ); ?></div>
		</div>
	</td>
	<?php

	do_action( 'wc_epo_admin_order_item_header', $order, $_product, $item, $item_id );

	?>
	<td class="item_cost" width="1%" data-sort-value="<?php echo esc_attr( $order->get_item_subtotal( $item, false, true ) ); ?>">
		<?php esc_html_e( 'Cost', 'woocommerce' ); ?>
	</td>
	<td class="quantity" width="1%">
		<?php esc_html_e( 'Qty', 'woocommerce' ); ?>
	</td>
	<td class="line_cost" width="1%" data-sort-value="<?php echo esc_attr( isset( $item['line_total'] ) ? $item['line_total'] : '' ); ?>">
		<?php esc_html_e( 'Total', 'woocommerce' ); ?>
	</td>
	<?php
	if ( ! empty( $tax_data ) && isset( $order_taxes ) && is_array( $order_taxes ) ) {
		foreach ( $order_taxes as $tax_item ) {
			$column_label = esc_html__( 'Tax', 'woocommerce' );
			if ( $tax_item instanceof WC_Order_Item_Tax && is_callable( [ $tax_item, 'get_label' ] ) ) {
				$tax_label = $tax_item->get_label();
				if ( ! empty( $tax_label ) ) {
					$column_label = $tax_label;
				}
			}
			?>
			<td class="line_tax" width="1%">
				<?php echo esc_html( $column_label ); ?>
			</td>
			<?php
		}
	}
	?>
	<td class="wc-order-edit-line-item" width="1%">
		&nbsp;
	</td>
</tr>
	<?php
endif;
