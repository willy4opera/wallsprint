<?php
/**
 * Created by Sublime Text 3.
 * User: MBach90
 * Date: 13/08/15
 * Time: 10:20 AM
 */
use Elementor\App\Modules\ImportExport\Runners\Import\Site_Settings;
if(class_exists('Bzotech_PluginsImporter') and !class_exists('Bzotech_Importer'))
{
    class Bzotech_Importer extends  Bzotech_PluginsImporter
    {
        static function init()
        {
            add_action('admin_init',array(__CLASS__,'_do_import'));
        }

        static function _do_import()
        {
            if(isset($_REQUEST['bzotech_do_import']) and $_REQUEST['bzotech_do_import'] || isset($_REQUEST['media']))
            {

                //Check Permission
                if(!current_user_can('manage_options')){
                    echo json_encode(array('status'=>0,'message'=>esc_html__('You do not have permission to do this','bw-printxtore')));die;
                }
                self::load_lib();

                //Check Importer Plugins was installed
                if ( !class_exists( 'WP_Importer' ) or ! class_exists( 'WP_Import' ) ){
                    echo json_encode(array('status'=>0,'message'=>esc_html__('Importer Class Was Not Installed','bw-printxtore')));die;
                }
                global $bzotech_config;
                $demo=self::$_demo;
                $st_import_config = $bzotech_config['import_config'];
                extract($st_import_config[$demo]);
                $step =isset($_REQUEST['step'])? $_REQUEST['step']:1;
                $import_media = isset($_REQUEST['media'])? $_REQUEST['media']:1;
                if($import_media == '2') $step = 3;
                $data_dir_root=self::$_import_path;
                $data_url=self::$_import_url;
                $package=self::$_package;
                
                $data_dir = $data_dir_root.'/demo';
                if($step==1) {
                    //Update theme_options
                    $data_option= $bzotech_config['import_theme_option'][$demo];
                    if(!empty($data_option)){
                        $options=json_decode($data_option,true); // unserialize
                        if(!function_exists('bzotech_get_option_name'))
                        {
                            echo json_encode( array(
                                    'status'   =>0,
                                    'messenger'=>"<span class='red'>".esc_html__("Plugin: Bzotech core must be installed first. Stop working!",'bw-printxtore')."</span>",
                                    'next_url' => ''
                                )
                            );
                            die;
                        }
                        update_option( bzotech_get_option_name(), $options ); // and overwrite the current theme-options
                        echo json_encode( array(
                                'status'   =>"ok",
                                'messenger'=>esc_html__("Importing the demo theme options...",'bw-printxtore')."<span>".esc_html__("DONE!",'bw-printxtore')."</span><br>",
                                'next_url' =>  admin_url(self::$_import_page."&bzotech_do_import={$demo}&media={$import_media}&package={$package}&step=" . ($step + 1))
                            )
                        );
                    }
                    else{
                        echo json_encode( array(
                                'status'   =>0,
                                'messenger'=>"<span class='red'>".esc_html__("Theme option contain NULL content. Stop working!",'bw-printxtore')."</span>",
                                'next_url' => ''
                            )
                        );
                    }

                }

                //Update Widgets
                if($step==2){
                    // Add data to widgets
                    $widget_data = $bzotech_config['import_widget'][$demo];
                    if(empty($widget_data)) $widget_data=$bzotech_config['import_widget']['global'];
                    $data_object = json_decode($widget_data);
                    if(!empty($data_object)){
                        $import_widgets = self::wie_import_data( $data_object );
                        echo json_encode( array(
                                'status'   =>1,
                                'messenger'=>esc_html__("Importing the demo widgets...",'bw-printxtore')." <span>".esc_html__("DONE!",'bw-printxtore')."</span>.<br>",
                                'next_url' => admin_url(self::$_import_page."&bzotech_do_import={$demo}&media={$import_media}&package={$package}&step=" . ($step + 1)),
                            )
                        );
                    }
                    else{
                        echo json_encode( array(
                                'status'   =>0,
                                'messenger'=>"<span class='red'>".esc_html__("Widget option contain NULL content. Stop working!",'bw-printxtore')."</span>",
                                'next_url' => ''
                            )
                        );
                    }
                }

                //Import XML

                if($step==3){
                    if($import_media != '2') wp_delete_nav_menu($menu_replace);
                    $stt_file=isset($_REQUEST['file_number'])?$_REQUEST['file_number']:0;
                    $ds_file=array_filter(glob($data_dir.'/data/*'),'is_file');
                    $file_name=isset($ds_file[$stt_file])?$ds_file[$stt_file]:false;
                    if(!$file_name){
                        echo json_encode( array(
                                'status'   =>0,
                                'messenger'=>"<span class='red'>".esc_html__("File Not Found. Stop working!",'bw-printxtore')."</span>",
                                'next_url' => ''
                            )
                        );
                        die;
                    }

                    $nexturl=admin_url(self::$_import_page."&bzotech_do_import={$demo}&media={$import_media}&package={$package}&step=" . ($step).'&file_number='.($stt_file+1));
                    if($stt_file>=count($ds_file)-1){
                        $nexturl=admin_url(self::$_import_page."&bzotech_do_import={$demo}&media={$import_media}&package={$package}&step=" . ($step+1));
                    }
                    ob_start();
                    $importer = new WP_Import();
                    $theme_xml = $file_name;
                    $importer->fetch_attachments = true;
                    if(basename($file_name) != 'media.xml'){
                        if($import_media != '2'){
                            $importer->import($theme_xml);
                            @ob_clean();
                            echo json_encode( array(
                                    'status'   =>1,
                                    'messenger'=>sprintf("Importing data: %s %d of %d ... <span>DONE!</span><br>",basename($file_name),$stt_file+1, count($ds_file)-0),
                                    'next_url' => $nexturl,
                                    'file'     => $ds_file,
                                )
                            );
                        }
                        else{
                            echo json_encode( array(
                                    'status'   =>1,
                                    'messenger'=>'',
                                    'next_url' => $nexturl,
                                    'file'     => $ds_file,
                                )
                            );
                        }
                    }
                    else{
                        @ob_clean();
                        if($import_media != '0'){
                            $importer->import($theme_xml);
                            if($import_media == '2'){
                                @ob_clean();
                                echo json_encode( array(
                                        'status'   =>'ok',
                                        'messenger'=>esc_html__('Setting WooCommerce options...','bw-printxtore').' <span>'.esc_html__('DONE!','bw-printxtore').'</span><br/><span>'.esc_html__('All Done! Have Fun','bw-printxtore').'</span><br/><a class="close-import bt-done" href="#">Close</a><a class="bt-done bt-next" href="https://bzotech.com/printxtore-wordpress-theme-documentation/#after-import"  target="_blank">Next Step<span class=" dashicons-before dashicons-arrow-right-alt2"></span></a><a class="bt-done" href="'.get_admin_url().'admin.php?page=_options&tab=1">Theme Options</a><a target="_blank" class="bt-done" href="'.home_url().'">Visit Site</a>',
                                        'next_url' => '',
                                        'file'     => $ds_file,
                                        
                                    )
                                );
                            }else{
                                @ob_clean();
                                echo json_encode( array(
                                        'status'   =>1,
                                        'messenger'=>sprintf("Importing data: %s %d of %d ... <span>DONE!</span><br>",basename($file_name),$stt_file+1, count($ds_file)-0),
                                        'next_url' => $nexturl,
                                        'file'     => $ds_file,
                                        
                                    )
                                );
                            } 
                            
                        }
                        else{
                            echo json_encode( array(
                                        'status'   =>1,
                                        'messenger'=>sprintf("Importing data: %s %d of %d ... <span>Pass!</span><br>",basename($file_name),$stt_file+1, count($ds_file)-0),
                                        'next_url' => $nexturl,
                                        'file'     => $ds_file,
                                    )
                                );
                        }
                    }                    
                }

                // Set Up Menu Theme Location

                if($step==4) {
                    bzotech_fix_import_category('product_cat',$demo);
                    //  Set imported menus to registered theme locations
                    $locations = get_theme_mod('nav_menu_locations'); // registered menu locations in theme
                    $menus = wp_get_nav_menus(); // registered menus
                    if ($menus) {
                        foreach ($menus as $menu) { // assign menus to theme locations
                            if (!empty($menu_locations))
                                foreach ($menu_locations as $key => $st_over_menu) {
                                    if ($menu->name == $key) {
                                        $locations[$st_over_menu] = $menu->term_id;
                                    }
                                }
                        }
                    }
                    set_theme_mod('nav_menu_locations', $locations); // set menus to locations

                    $nexturl = admin_url(self::$_import_page . "&bzotech_do_import={$demo}&package={$package}&step=" . ($step+1) );
                    echo json_encode(array(
                            'status' => 1,
                            'messenger' => esc_html__("Importing menu settings ...",'bw-printxtore')." <span>".esc_html__("DONE!",'bw-printxtore')."</span><br>",
                            'next_url' => $nexturl,
                        )
                    );
                }
                if($step==5) {
                                    
                    $imported_data = array();
                    $site_settings = json_decode($bzotech_config['site_settings'],true);

                    $data['site_settings'] = $site_settings;
                    
                    $runner = new Site_Settings();
                    $result = $runner->import($data, $imported_data);   

                    $nexturl = admin_url(self::$_import_page . "&bzotech_do_import={$demo}&package={$package}&step=" . ($step+1) );
                    echo json_encode(array(
                            'status' => 1,
                            'messenger' => esc_html__("Importing elementor settings ...",'bw-printxtore')." <span>".esc_html__("DONE!",'bw-printxtore')."</span><br>",
                            'next_url' => $nexturl,
                        )
                    );
                }
                // Set reading options
                if($step == 6){
                    $stt_file=isset($_REQUEST['rev_number'])?$_REQUEST['rev_number']:0;
                    $ds_file=array_filter(glob($data_dir.'/data-slider/*'),'is_file');
                    $file_name=isset($ds_file[$stt_file])?$ds_file[$stt_file]:false;
                    $nexturl=admin_url(self::$_import_page."&bzotech_do_import={$demo}&media={$import_media}&package={$package}&step=" . ($step+1));
                    if($stt_file<count($ds_file) && class_exists('RevSlider')){   
                        $nexturl=admin_url(self::$_import_page."&bzotech_do_import={$demo}&media={$import_media}&package={$package}&step=" . ($step).'&rev_number='.($stt_file+1));                     
                        if($file_name){
                            $slider = new RevSlider();
                            $response = $slider->importSliderFromPost(true, true, $data_dir . '/data-slider'.'/'.basename($file_name));
                            echo json_encode( array(
                                    'status'   => 1,
                                    'messenger'=> sprintf("Importing data: %s %d of %d ... <span>DONE!</span><br>",basename($file_name),$stt_file+1, count($ds_file)-0),
                                    'next_url' => $nexturl,
                                    'file'     => $ds_file,
                                )
                            );
                        }
                    }
                    else{                    
                        // Set reading options
                        if(!$set_woocommerce_page) {
                            $next_url = '';
                            $messenger = '<span>'.esc_html__("All Done! Have Fun",'bw-printxtore').'</span>';
                        }
                        else {
                            $next_url = admin_url(self::$_import_page . "&bzotech_do_import={$demo}&package={$package}&step=" . ($step+1) );
                            $messenger = '';
                        }
                        if($homepage_default != ""){
                            $homepage = get_page_by_title( $homepage_default );
                            if(is_object($homepage)) {
                                update_option('show_on_front', 'page');
                                update_option('page_on_front', $homepage->ID); // Front Page
                            }
                        }
                        if($blogpage_default != ""){
                            $homepage = get_page_by_title( $blogpage_default );
                            if(is_object($homepage)) {
                                update_option('show_on_front', 'page');
                                update_option('page_for_posts', $homepage->ID); // Blog Page
                            }
                        }

                        echo json_encode( array(
                                'status'   =>"ok",
                                'messenger'=>esc_html__("Setting reading options...",'bw-printxtore')." <span>".esc_html__("DONE!",'bw-printxtore')."</span><br/>".$messenger,
                                'next_url' => $next_url,
                            )
                        );
                    }
                }

                if($step==7)
                {

                    //Setup WooCommerce page
                    if(class_exists('Woocommerce') and class_exists('WC_Install'))
                    {
                        WC_Install::create_pages();
                        WC_Admin_Notices::remove_notice( 'install' );
                    }
                    if(function_exists('vc_editor_set_post_types')){
                        $list = array(
                            'post',
                            'page',
                            'product',
                            'bzotech_header',
                            'bzotech_footer',
                            'bzotech_mega_item',
                        );
                        vc_editor_set_post_types($list);
                    }     
                    $cpt_support = [ 'page', 'post', 'product', 'bzotech_header', 'bzotech_footer', 'bzotech_mega_item' ]; 
                    update_option( 'elementor_cpt_support', $cpt_support );
                    update_option( 'elementor_disable_color_schemes', 'yes' );       
                    update_option( 'elementor_disable_typography_schemes', 'yes' );
                    update_option( 'elementor_experiment-container', 'active' );          
                    update_option( 'elementor_experiment-e_swiper_latest', 'inactive' );          
                    update_option( 'elementor_lazy_load_background_images', 0);          
                    update_option( 'woocs_show_flags', 0);          
                    update_option( 'woocs_show_money_signs', 0);          
                    update_option( 'elementor_experiment-e_font_icon_svg', 'inactive');          
                    update_option( 'yith_wcwl_show_on_loop', 'no');
                    update_option( 'permalink_structure', '/%postname%/'); 
                    update_option( 'posts_per_page', 10);          
                    echo json_encode( array(
                            'status'   =>"ok",
                            'messenger'=>esc_html__('Setting WooCommerce options...','bw-printxtore').' <span>'.esc_html__('DONE!','bw-printxtore').'</span><br/><span>'.esc_html__('All Done! Have Fun','bw-printxtore').'</span><br/><a class="close-import bt-done" href="#">Close</a><a class="bt-done bt-next" href="https://bzotech.com/printxtore-wordpress-theme-documentation/#after-import"  target="_blank">Next Step<span class=" dashicons-before dashicons-arrow-right-alt2"></span></a><a class="bt-done" href="'.get_admin_url().'admin.php?page=_options&tab=1">Theme Options</a><a target="_blank" class="bt-done" href="'.home_url().'">Visit Site</a>',
                            'next_url' => '',
                        )
                    );
                }
                die;
            }
        }
    }

    Bzotech_Importer::init();
}