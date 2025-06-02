<?php
/**
 * Created by Sublime Text 3.
 * Date: 13/08/15
 * Time: 10:20 AM
 */
/*Set style default*/
$main_color = '#e32131';
$main_color2 = '#ffcebd';
$gray_color = '#454545';
$border_color = '#d5d5d5';
$body_bg = '#ffffff';
$preload_bg = $main_color;
$container_width = '1560px';
$gutter = '15px';
$title_typo = array('font-family'=>'Outfit','color'=>'#2D3150');
$body_typo = array('font-family'=>'Outfit','color'=>'#666666','font-size'=>'16px','line-height'=>'1.5em');
$letter_spacing_body = '0.04px'; 

/*Get style default*/
$get_main_color = bzotech_get_value_by_id('main_color');
$get_main_color2 = bzotech_get_value_by_id('main_color2');
$get_body_bg = bzotech_get_value_by_id('body_bg');
$get_container_width = bzotech_get_value_by_id('container_width');

$get_preload_bg = bzotech_get_option('preload_bg');

/*Structure var() : var(--bzo-$key-$name_attribute)*/
$body_typography = bzotech_get_css_option_array_type('body_typo',$body_typo);
$title_typography = bzotech_get_css_option_array_type('title_typo',$title_typo);


if(!empty($get_main_color)){
    $preload_bg=$get_main_color;
    $main_color = $get_main_color;
} 
if(!empty($get_main_color2)) $main_color2 = $get_main_color2;

$main_color_mix_black = bzotech_mix_color($main_color,'#000','1',0.75);
$main_color_mix_white = bzotech_mix_color($main_color,'#fff','1',0.35);
$main_color_mix_white_90 = bzotech_mix_color($main_color,'#fff','1',0.10);
$main_color_mix_d7ffcd_90 = bzotech_mix_color($main_color,'#d7ffcd','1',0.10);
if(!empty($get_body_bg)) $body_bg = $get_body_bg;
if(!empty($get_container_width)) $container_width = $get_container_width;
if(!empty($get_preload_bg)) $preload_bg = $get_preload_bg;

$style = ':root {
            --bzo-main-color: ' . $main_color . ';
            --bzo-main-color-mix-black: ' . $main_color_mix_black . ';
            --bzo-main-color-mix-white: ' . $main_color_mix_white . ';
            --bzo-main-color-mix-white_90: ' . $main_color_mix_white_90 . ';
            --bzo-main-color-mix-d7ffcd_90: ' . $main_color_mix_d7ffcd_90 . ';

            --bzo-main-color2: ' . $main_color2 . ';
            --bzo-gray-color: ' . $gray_color . ';
            --bzo-border-color: ' . $border_color . ';
            --bzo-body-background: ' . $body_bg . ';
            --bzo-container-width: ' . $container_width . ';
            --bzo-preload-background: ' . $preload_bg . '; 
            --bzo-gutter: ' . $gutter . ';
            --bzo-gutter-minus: -' . $gutter . ';
            --bzo-letter-spacing-body: ' . $letter_spacing_body . ';
           '.$body_typography.'
           '.$title_typography.'
        }';
if(!empty($style)) echo apply_filters('bzotech_output_root_css',$style);