<?php
$show = bzotech_get_option('show_too_panel');
$tool_id = bzotech_get_option('tool_panel_page');
if($show == '1' && !empty($tool_id)){
	echo '<div class="tool-panel-page">';
	echo Bzotech_Template::get_vc_pagecontent($tool_id,true);
	echo '</div>';
}