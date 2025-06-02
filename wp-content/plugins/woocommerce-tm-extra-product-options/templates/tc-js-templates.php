<?php
/**
 * The javascript-based template for displayed javascript generated html code
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tc-js-templates.php
 *
 * NOTE that we may need to update template files and you
 * (the plugin or theme developer) will need to copy the new files
 * to your theme or plugin to maintain compatibility.
 *
 * @see     https://codex.wordpress.org/Javascript_Reference/wp.template
 * @author  ThemeComplete
 * @package Extra Product Options/Templates
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;
if ( ! isset( $formatted_price ) ) {
	$formatted_price = '';
}
if ( ! isset( $formatted_price_m ) ) {
	$formatted_price_m = '';
}
if ( ! isset( $formatted_sale_price ) ) {
	$formatted_sale_price = '';
}
if ( ! isset( $formatted_sale_price_m10 ) ) {
	$formatted_sale_price_m10 = '';
}
if ( ! isset( $formatted_sale_price_m01 ) ) {
	$formatted_sale_price_m01 = '';
}
if ( ! isset( $formatted_sale_price_m11 ) ) {
	$formatted_sale_price_m11 = '';
}
?>
<script class="tm-hidden" type="text/template" id="tmpl-tc-cart-options-popup">
	<div class='header'>
		<h3>{{{ data.title }}}</h3>
	</div>
	<div id='{{{ data.id }}}' class='float-editbox'>{{{ data.html }}}</div>
	<div class='footer'>
		<div class='inner'>
			<span class='tm-button button button-secondary button-large floatbox-cancel'>{{{ data.close }}}</span>
		</div>
	</div>
</script>
<script class="tm-hidden" type="text/template" id="tmpl-tc-lightbox">
	<div class="tc-lightbox-wrap">
		<span class="tc-lightbox-button tcfa tcfa-search tc-transition tcinit"></span>
	</div>
</script>
<script class="tm-hidden" type="text/template" id="tmpl-tc-lightbox-zoom">
	<span class="tc-lightbox-button-close tcfa tcfa-times"></span>
	{{{ data.img }}}
</script>
<script class="tm-hidden" type="text/template" id="tmpl-tc-final-totals">
	<dl class="tm-extra-product-options-totals tm-custom-price-totals">
		<# if (data.show_unit_price==true){ #><?php do_action( 'wc_epo_template_before_unit_price' ); ?>
		<dt class="tm-unit-price">{{{ data.unit_price }}}</dt>
		<dd class="tm-unit-price">
		{{{ data.totals_box_before_unit_price }}}<span class="price amount options">{{{ data.formatted_unit_price }}}</span>{{{ data.totals_box_after_unit_price }}}
		</dd><?php do_action( 'wc_epo_template_after_unit_price' ); ?>
		<# } #>
		<# if (data.show_options_vat==true){ #><?php do_action( 'wc_epo_template_before_vat_options_total' ); ?>
		<dt class="tm-vat-options-totals">{{{ data.options_vat_total }}}</dt>
		<dd class="tm-vat-options-totals">
		{{{ data.totals_box_before_vat_options_totals_price }}}<span class="price amount options">{{{ data.formatted_vat_options_total }}}</span>{{{ data.totals_box_after_vat_options_totals_price }}}
		</dd><?php do_action( 'wc_epo_template_after_vat_options_total' ); ?>
		<# } #>
		<# if (data.show_options_total==true){ #><?php do_action( 'wc_epo_template_before_option_total' ); ?>
		<dt class="tm-options-totals">{{{ data.options_total }}}</dt>
		<dd class="tm-options-totals">
		{{{ data.totals_box_before_options_totals_price }}}<span class="price amount options">{{{ data.formatted_options_total }}}</span>{{{ data.totals_box_after_options_totals_price }}}
		</dd><?php do_action( 'wc_epo_template_after_option_total' ); ?>
		<# } #>
		<# if (data.show_fees_total==true){ #><?php do_action( 'wc_epo_template_before_fee_total' ); ?>
		<dt class="tm-fee-totals">{{{ data.fees_total }}}</dt>
		<dd class="tm-fee-totals">
		{{{ data.totals_box_before_fee_totals_price }}}<span class="price amount fees">{{{ data.formatted_fees_total }}}</span>{{{ data.totals_box_after_fee_totals_price }}}
		</dd><?php do_action( 'wc_epo_template_after_fee_total' ); ?>
		<# } #>
		<# if (data.show_extra_fee==true){ #><?php do_action( 'wc_epo_template_before_extra_fee' ); ?>
		<dt class="tm-extra-fee">{{{ data.extra_fee }}}</dt>
		<dd class="tm-extra-fee">
		{{{ data.totals_box_before_extra_fee_price }}}<span class="price amount options extra-fee">{{{ data.formatted_extra_fee }}}</span>{{{ data.totals_box_after_extra_fee_price }}}
		</dd><?php do_action( 'wc_epo_template_after_extra_fee' ); ?>
		<# } #>
		<# if (data.show_final_total==true){ #><?php do_action( 'wc_epo_template_before_final_total' ); ?>
		<dt class="tm-final-totals">{{{ data.final_total }}}</dt>
		<dd class="tm-final-totals">
		{{{ data.totals_box_before_final_totals_price }}}<span class="price amount final">{{{ data.formatted_final_total }}}</span>{{{ data.totals_box_after_final_totals_price }}}
		</dd><?php do_action( 'wc_epo_template_after_final_total' ); ?>
		<# } #>
		<?php do_action( 'wc_epo_after_js_final_totals' ); ?>
	</dl>
</script>
<script class="tm-hidden" type="text/template" id="tmpl-tc-plain-price">
	{{{ data.price }}}
</script>
<script class="tm-hidden" type="text/template" id="tmpl-tc-price">
	<?php echo esc_html( $formatted_price ); ?>
</script>
<script class="tm-hidden" type="text/template" id="tmpl-tc-price-m">
	<?php echo esc_html( $formatted_price_m ); ?>
</script>
<script class="tm-hidden" type="text/template" id="tmpl-tc-sale-price">
	<?php echo esc_html( $formatted_sale_price ); ?>
</script>
<script class="tm-hidden" type="text/template" id="tmpl-tc-sale-price-m10">
	<?php echo esc_html( $formatted_sale_price_m10 ); ?>
</script>
<script class="tm-hidden" type="text/template" id="tmpl-tc-sale-price-m01">
	<?php echo esc_html( $formatted_sale_price_m01 ); ?>
</script>
<script class="tm-hidden" type="text/template" id="tmpl-tc-sale-price-m11">
	<?php echo esc_html( $formatted_sale_price_m11 ); ?>
</script>
<script class="tm-hidden" type="text/template" id="tmpl-tc-section-pop-link">
	<div id="tm-section-pop-up" class="tm-extra-product-options flasho tc-wrapper tm-section-pop-up single tm-animated appear">
		<div class='header'><h3>{{{ data.title }}}</h3></div>
		<div class="float-editbox" id="tc-floatbox-content"></div>
		<div class='footer'>
			<div class='inner'>
				<span class='tm-button button button-secondary button-large floatbox-cancel'>{{{ data.close }}}</span>
			</div>
		</div>
	</div>
</script>
<script class="tm-hidden" type="text/template" id="tmpl-tc-floating-box-nks"><# if (data.values.length) {#>
	{{{ data.html_before }}}
	<div class="tc-row tm-fb-labels">
		<span class="tc-cell tcwidth tcwidth-25 tm-fb-title">{{{ data.option_label }}}</span>
		<span class="tc-cell tcwidth tcwidth-25 tm-fb-value">{{{ data.option_value }}}</span>
		<span class="tc-cell tcwidth tcwidth-25 tm-fb-quantity">{{{ data.option_qty }}}</span>
		<span class="tc-cell tcwidth tcwidth-25 tm-fb-price">{{{ data.option_price }}}</span>
	</div>
	<# for (var i = 0; i < data.values.length; i++) { #>
		<# if (data.values[i].label_show=='' || data.values[i].value_show=='') {#>
	<div class="tc-row tm-fb-data">
			<# if (data.values[i].label_show=='') {#>
		<span class="tc-cell tcwidth tcwidth-25 tm-fb-title">{{{ data.values[i].title }}}</span>
			<# } #>
			<# if (data.values[i].value_show=='') {#>
		<span class="tc-cell tcwidth tcwidth-25 tm-fb-value">{{{ data.values[i].value }}}</span>
			<# } #>
		<span class="tc-cell tcwidth tcwidth-25 tm-fb-quantity">{{{ data.values[i].quantity }}}</span>
		<span class="tc-cell tcwidth tcwidth-25 tm-fb-price">{{{ data.values[i].price }}}</span>
	</div>
		<# } #>
	<# } #>
	{{{ data.html_after }}}
	<# }#>
	{{{ data.totals }}}</script>
<script class="tm-hidden" type="text/template" id="tmpl-tc-floating-box"><# if (data.values.length) {#>
	{{{ data.html_before }}}
	<dl class="tm-fb">
		<# for (var i = 0; i < data.values.length; i++) { #>
			<# if (data.values[i].label_show=='') {#>
		<dt class="tm-fb-title">{{{ data.values[i].title }}}</dt>
			<# } #>
			<# if (data.values[i].value_show=='') {#>
		<dd class="tm-fb-value">{{{ data.values[i].value }}}<# if (data.values[i].quantity > 1) {#><span class="tm-fb-quantity"> &times; {{{ data.values[i].quantity }}}</span><#}#></dd>
			<# } #>
		<# } #>
	</dl>
	{{{ data.html_after }}}
	<# }#>{{{ data.totals }}}</script>
<script class="tm-hidden" type="text/template" id="tmpl-tc-chars-remanining">
	<span class="tc-chars">
		<span class="tc-chars-remanining">{{{ data.maxlength }}}</span>
		<span class="tc-remaining"> {{{ data.characters_remaining }}}</span>
	</span>
</script>
<script class="tm-hidden" type="text/template" id="tmpl-tc-formatted-price">{{{ data.before_price_text }}}<# if (data.customer_price_format_wrap_start) {#>
	{{{ data.customer_price_format_wrap_start }}}
	<# } #><?php echo esc_html( $formatted_price ); ?><# if (data.customer_price_format_wrap_end) {#>
	{{{ data.customer_price_format_wrap_end }}}
	<# } #>{{{ data.after_price_text }}}</script>
<script class="tm-hidden" type="text/template" id="tmpl-tc-formatted-sale-price">{{{ data.before_price_text }}}<# if (data.customer_price_format_wrap_start) {#>
	{{{ data.customer_price_format_wrap_start }}}
	<# } #><?php echo esc_html( $formatted_sale_price ); ?><# if (data.customer_price_format_wrap_end) {#>
	{{{ data.customer_price_format_wrap_end }}}
	<# } #>{{{ data.after_price_text }}}</script>
<script class="tm-hidden" type="text/template" id="tmpl-tc-upload-messages">
	<div class="header">
		<h3>{{{ data.title }}}</h3>
	</div>
	<div class="float-editbox" id="tc-floatbox-content">
		<div class="tc-upload-messages">
			<div class="tc-upload-message">{{{ data.message }}}</div>
			<# for (var id in data.files) {
				if (data.files.hasOwnProperty(id)) {#>
					<# for (var i in id) {
						if (data.files[id].hasOwnProperty(i)) {#>
						<div class="tc-upload-files">{{{ data.files[id][i] }}}</div>
						<# }
					}#>
				<# }
			}#>
		</div>
	</div>
	<div class="footer">
		<div class="inner">&nbsp;</div>
	</div>
</script>
