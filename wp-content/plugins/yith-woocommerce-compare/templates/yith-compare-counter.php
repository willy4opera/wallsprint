<?php
/**
 * Woocommerce Compare counter shortcode template
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Compare
 * @version 2.3.2
 */

defined( 'YITH_WOOCOMPARE' ) || exit; // Exit if accessed directly.

global $yith_woocompare;
?>

<div class="yith-woocompare-counter" data-type="<?php echo esc_attr( $type ); ?>" data-text_o="<?php echo esc_attr( $text_o ); ?>">
	<a class="yith-woocompare-open" href="<?php echo esc_url( YITH_WooCompare_Frontend::instance()->get_table_url() ); ?>">
		<span class="yith-woocompare-counter">
			<?php if ( 'yes' === $show_icon ) : ?>
				<span class="yith-woocompare-icon">
					<img src="<?php echo esc_url( $icon ); ?>" />
				</span>
			<?php endif; ?>
			<span class="yith-woocompare-count">
				<?php
				switch ( $type ) :
					case 'text':
						echo esc_html( $text );
						break;
					case 'number':
					default:
						echo esc_html( $items_count );
						break;
					endswitch;
				?>
			</span>
		</span>
	</a>
</div>

<?php
wp_enqueue_script( 'yith-woocompare-main' );