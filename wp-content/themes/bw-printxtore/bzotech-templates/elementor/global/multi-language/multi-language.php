<?php
namespace Elementor;
extract($settings);
$html ='<div class ="elbzotech-lang-global elbzotech-lang-global-'.$style.'">';
switch ($style) {
    case 'poly-style':
        if(function_exists('pll_the_languages')){
            ob_start();
            $html .=    '<div class="polylang-selector">';
            pll_the_languages(array('dropdown'=>1,'show_flags'=>1));
            $html .=    ob_get_clean();
            $html .=    '</div>';
        }
        break;

    case 'wpml-style':
        ob_start();
        do_action('wpml_add_language_selector');
        $html .=    ob_get_clean();
        break;
    case 'demo-style':
       if(!empty($data_demo)){
            $lang_sub= $lang_active= '';
            foreach($data_demo as $key => $value){
                 if($key == 0){
                    $l_class = 'active';
                    $lang_active = '<a class="dropdown-link label-e " href="#'.$value['_id'].'">';
                    if($flag == 'yes' && !empty($value['image']['id'])) $lang_active .= wp_get_attachment_image($value['image']['id'],'full');
                    if($show_label == 'yes') $lang_active .= '<span class="label-language">'.$value['title'].'</span>';

                    $lang_active .= '<i class="las la-angle-down"></i>';
                    $lang_active .= '</a>';
                }else{
                     $l_class = '';
                }
                $lang_sub .= '<li class="'.$l_class.'"><a class="label-item-e" href="#'.$value['_id'].'">';
                if($flag == 'yes' && !empty($value['image']['id'])) $lang_sub .= wp_get_attachment_image($value['image']['id'],'full');
                if($show_label == 'yes') $lang_sub .= ' <span class="label-language">'.$value['title'].'</span>';
                $lang_sub .= '</a></li>';

                
            }
            $html .=    '<div class="elbzotech-dropdown-box language-box-global">';
            $html .=        $lang_active;
            $html .=        '<ul class="list-none elbzotech-dropdown-list">';
            $html .=            $lang_sub;
            $html .=        '</ul>';
            $html .=    '</div>';
        }
        break;
    
    default:
        if(defined('ICL_SITEPRESS_VERSION') && defined('ICL_LANGUAGE_CODE') ){
            $wpml_lang = icl_get_languages('skip_missing=0&orderby=custom');            
            foreach ($wpml_lang as $lang) {
                $url = $lang['url'];
                $flag_url = $lang['country_flag_url'];
                $name = $lang['native_name'];
                if($lang['active']){
                    $l_class = 'active';
                    $lang_active .=     '<a class="dropdown-link label-e" href="'.esc_url($url).'">';
                    if($flag == 'yes') $lang_active .=  '<img alt="'.esc_attr__("flag",'bw-printxtore').'" src="'.esc_url($flag_url).'">';
                    if($show_label == 'yes') $lang_active .= '<span class="label-language">'.$name.'</span>';
                    $lang_active .=     '</a>';
                }
                else $l_class = '';
                $lang_sub .=                '<li class="'.$l_class.'">
                                                <a class="label-item-e" href="'.esc_url($url).'">';
                if($flag == 'yes') $lang_sub .=     '<img alt="'.esc_attr__("flag",'bw-printxtore').'" src="'.esc_url($flag_url).'">';
                if($show_label == 'yes') $lang_sub .= ' <span class="label-language">'.$name.'</span>';
                $lang_sub .=                    '</a>
                                            </li>';
            }
            $html .=    '<div class="elbzotech-dropdown-box language-box-global">';
            $html .=        $lang_active;
            $html .=        '<ul class="list-none elbzotech-dropdown-list">';
            $html .=            $lang_sub;
            $html .=        '</ul>';
            $html .=    '</div>';
        }
        elseif(class_exists('Polylang')){
                global $polylang;
                $languages = $polylang->model->get_languages_list();
                $current_lang = pll_current_language();
                foreach ($languages as $lang) {
                    $url = PLL()->links->get_translation_url($lang);
                    $flag_url = $lang->flag_url;
                    $name = $lang->name;
                    if($lang->slug == $current_lang){
                        $l_class = 'active';
                        $lang_active .=     '<a class="dropdown-link label-e" href="'.esc_url($url).'">';
                        if($flag == 'yes') $lang_active .=     '<img alt="'.esc_attr__("flag",'bw-printxtore').'" src="'.esc_url($flag_url).'">';
                        if($show_label == 'yes') $lang_active .=         '<span class="label-language">'.$name.'</span>';
                        $lang_active .=     '</a>';
                    }
                    else $l_class = '';
                    $lang_sub .=                '<li class="'.$l_class.'">
                                                    <a class="label-item-e" href="'.esc_url($url).'">';
                    if($flag == 'yes') $lang_sub .=     '<img alt="'.esc_attr__("flag",'bw-printxtore').'" src="'.esc_url($flag_url).'">';
                    if($show_label == 'yes') $lang_sub .= ' <span class="label-language">'.$name.'</span>';
                    $lang_sub .=                    '</a>
                                                </li>';
                }
            $html .=    '<div class="elbzotech-dropdown-box language-box-global">';
            $html .=        $lang_active;
            $html .=        '<ul class="list-none elbzotech-dropdown-list">';
            $html .=            $lang_sub;
            $html .=        '</ul>';
            $html .=    '</div>';
        }
        else{
            if(defined('QTX_VERSION')){
                global $q_config;
                $languages = qtranxf_getSortedLanguages();
                $current_lang = bzotech_get_current_language();
                if(is_404()) $url = home_url('/'); else $url = '';
                $flag_location=qtranxf_flag_location();
                foreach ($languages as $lang) {
                    $url = qtranxf_convertURL($url, $lang, false, true);
                    $flag_url = $flag_location.$q_config['flag'][$lang];
                    $name = $q_config['language_name'][$lang];
                    if($lang == $current_lang){
                        $l_class = 'active';
                        $lang_active .=     '<a class="dropdown-link label-e" href="'.esc_url($url).'">';
                        if($flag == 'yes') $lang_active .=     '<img alt="'.esc_attr__("flag",'bw-printxtore').'" src="'.esc_url($flag_url).'">';
                        if($show_label == 'yes') $lang_active .= '<span class="label-language">'.$name.'</span>';
                        $lang_active .=     '</a>';
                    }
                    else $l_class = '';
                    $lang_sub .=                '<li class="'.$l_class.'">
                                                    <a class="label-item-e" href="'.esc_url($url).'">';
                    if($flag == 'yes') $lang_sub .=     '<img alt="'.esc_attr__("flag",'bw-printxtore').'" src="'.esc_url($flag_url).'">';
                    if($show_label == 'yes') $lang_sub .= ' <span class="label-language">'.$name.'</span>';
                    $lang_sub .=                    '</a>
                                                </li>';
                }
                $html .=    '<div class="elbzotech-dropdown-box language-box-global">';
                $html .=        $lang_active;
                $html .=        '<ul class="list-none elbzotech-dropdown-list">';
                $html .=            $lang_sub;
                $html .=        '</ul>';
                $html .=    '</div>';
            }
        }
        break;
}
$html .='</div>';        
echo apply_filters('bzotech_output_content',$html);