<?php
$page_id = apply_filters('bzotech_footer_page_id',bzotech_get_value_by_id('bzotech_footer_page'));
if(!empty($page_id)) {?>
	<div id="footer" class="footer-page <?php echo 'bzotech-'.str_replace ('.php','',get_page_template_slug($page_id));?> <?php echo'bzotech-footer-page-'.get_post_field( 'post_name', $page_id )?>">
            <?php echo Bzotech_Template::get_vc_pagecontent($page_id);?>
    </div>
<?php
}
else{
?>
	<div id="footer" class="footer-default">
		<div class="bzotech-container">
			<div class="bzotech-row">
				<div class="bzotech-col-md-12">
					<p class="copyright desc white"><?php esc_html_e("Copyright by BZOTech Theme. All Rights Reserved.",'bw-printxtore')?></p>
				</div>
			</div>
		</div>
	</div>
<?php
}