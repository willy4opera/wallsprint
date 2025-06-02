<?php
namespace Elementor;
extract($settings);
use Bzotech_Template;
$active = (int)$active-1;
echo '<div class="elbzotech-accordion elbzotech-accordion-global-css elbzotech-accordion-global-'.$style.'" data-active="'.$active.'" data-animate="'.$animate.'" data-heightstyle="'.$heightstyle.'">';
 	foreach (  $list_accor as $key => $item ) {
        $wdata->add_render_attribute( 'elbzotech-tab-title'.$key, 'class', 'item-title-e accordion-title elementor-repeater-item-'.$item['_id'] );
        $wdata->add_render_attribute( 'elbzotech-tab-content'.$key, 'class', 'item-content-e accordion-content elementor-repeater-item-'.$item['_id'] );
        $icon_html='';
        if(!empty($icon['value'])) $icon_html = '<i class="icon-accor '.$icon['value'].'"></i>';
        if(!empty($icon_active['value'])) $icon_html .= '<i class="icon-accor-active '.$icon_active['value'].'"></i>';
        if(!empty($icon['value']) || !empty($icon_active['value'])) $icon_html = '<span class="box-icon-accor item-icon-e">'. $icon_html.'</span>';
        $title = '<span>'.$item['title'].'</span>';

        echo '<h3 '.$wdata->get_render_attribute_string( 'elbzotech-tab-title'.$key ).'><span class="text">'. $item['title'].'</span>'.$icon_html.'</h3>';
        echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-tab-content'.$key ).'>';
           
            if(!empty($item['template'])) {
             
             echo Bzotech_Template::get_vc_pagecontent($item['template']);
            }
            else if(!empty($item['content'])){
                echo  bzotech_parse_text_editor( $item['content']);
            }  
                    
        echo '</div>';
    }

echo '</div>';