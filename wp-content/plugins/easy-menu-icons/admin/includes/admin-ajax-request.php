<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.
if ( !class_exists('EMICONS_Admin_Ajax')) {
    class EMICONS_Admin_Ajax {

        function __construct(){

            add_action( "wp_ajax_emicons_update_menu_options", array ( $this, 'emicons_update_menu_options' ) );
            add_action( "wp_ajax_nopriv_emicons_update_menu_options", array ( $this, 'emicons_update_menu_options' ) );

            add_action( "wp_ajax_emicons_get_menu_options", array ( $this, 'emicons_get_menu_options' ) );
            add_action( "wp_ajax_nopriv_emicons_get_menu_options", array ( $this, 'emicons_get_menu_options' ) );

            add_action( "wp_ajax_emicons_delete_menu_options", array ( $this, 'emicons_delete_menu_options' ) );
            add_action( "wp_ajax_nopriv_emicons_delete_menu_options", array ( $this, 'emicons_delete_menu_options' ) );

            add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'emicons_item_icon' ), 10, 2 );
            

        }

        function emicons_item_icon( $item_id, $item ) {

            $EMICONS = new EMICONS();
            $emicons_item_settings = get_post_meta( $item_id, 'emicons_settings', true );
            $emicons_item_icon_source = isset($emicons_item_settings['content']['icon_source']) ? $emicons_item_settings['content']['icon_source'] : '';
            $emicons_item_icon = isset($emicons_item_settings['content']['menu_icon']) ? $emicons_item_settings['content']['menu_icon'] : '';
            $icon_html = '';

            if(!empty($emicons_item_icon_source) && !empty($emicons_item_icon)){

                if($emicons_item_icon_source == 'dashicon'){
                    $icon_html = '<span class="'.$emicons_item_icon.'"></span>';
                }else if($emicons_item_icon_source == 'fontawesome'){
                    $icon_html = '<i class="'.$emicons_item_icon.'"></i>';
                }
            }

            
                ?>
                    <div class="emicons_saved_icon_wrapper <?php echo !empty($emicons_item_icon) ? esc_attr( 'has-icon') : '' ?>" style="clear: both;">
                        <?php 
                            if($emicons_item_icon_source == 'dashicon' || $emicons_item_icon_source == 'fontawesome' || $emicons_item_icon_source == ''){ ?>
                                 <div class="emicons_saved_icon"><?php echo wp_kses_post( $icon_html )?></div> 
                            <?php }
                        ?>
                       
                        <div class="emicons_saved_icon_actions">
                            <button type="button" class="emicons_set_icon_toggle_in_nav_item" data-icon_source ="<?php echo esc_attr( $emicons_item_icon_source )?>" data-menu_item_id="<?php echo esc_attr($item_id); ?>"><?php echo !empty($emicons_item_icon) ? esc_html__('Change', 'easy-menu-icons') : esc_html__('Add Icon', 'easy-menu-icons'); ?></button>
                            
                            <button type="button" class="emicons_remove_icon_toggle_in_nav_item" data-icon_source ="<?php echo esc_attr( $emicons_item_icon_source )?>" data-menu_item_id="<?php echo esc_attr($item_id); ?>"><?php echo esc_html__('Remove', 'easy-menu-icons'); ?></button>
                        </div>
                    </div>
                <?php
            

        }
        

        public function emicons_update_menu_options($menu_id) {

            check_ajax_referer('emicons_nonce', 'nonce');
            
            if(isset($_POST['settings']) && isset($_POST['menu_id'])){


                $menu_id = !empty($_POST['menu_id']) ? sanitize_text_field(wp_unslash($_POST['menu_id'])) : '';
                $menu_item_id = !empty($_POST['menu_item_id']) ? sanitize_text_field(wp_unslash($_POST['menu_item_id'])) : '';
                $menu_id = absint( $menu_id );


                if( isset( $_POST['settings'] ) && !empty( $_POST['settings'] ) ) {
                    $settings = array_map('sanitize_text_field', wp_unslash($_POST['settings']));
                } 


                if( isset($_POST['css']) && !empty( $_POST['css'] ) ) {
                    $css = array_map('sanitize_text_field', (wp_unslash($_POST['css'])));
                    update_post_meta( $menu_item_id, 'emicons_settings', ['switch' => 'on', 'content' => $settings, 'css' => $css] );
                } else {
                    return;
                }

                    
                
                
                wp_send_json_success([
                   'message' => esc_html__( 'Successfully data saved','easy-menu-icons' )
                ]);
                
                wp_die();

            }

        }
        

        public function emicons_delete_menu_options() {
            check_ajax_referer('emicons_nonce', 'nonce');
            if(isset($_POST['menu_item_id'])){

                $menu_item_id = sanitize_text_field(wp_unslash($_POST['menu_item_id']));
                $emicons_item_settings = get_post_meta( $menu_item_id, 'emicons_settings', true );

                if(isset($emicons_item_settings)){
                    delete_post_meta( $menu_item_id, 'emicons_settings' );
                    wp_send_json_success( $emicons_item_settings, 200 );
                }else{
                    wp_send_json_success( $emicons_item_settings, 404 );
                }

            }
            wp_die();
        }

        public function emicons_get_menu_options() {

            check_ajax_referer('emicons_nonce', 'nonce');

            if(isset($_POST['menu_item_id'])){

                $EMICONS = new EMICONS();
                

                $menu_item_id = sanitize_text_field(wp_unslash($_POST['menu_item_id']));

                $emicons_item_css = '';

                $emicons_item_settings = get_post_meta( $menu_item_id, 'emicons_settings', true );


                
                if(isset($_POST['icon_source'])){

                    $emicons_item_icon_source =  sanitize_text_field( wp_unslash($_POST['icon_source']) );

                }else if(isset($emicons_item_settings['icon_source'])){

                    $emicons_item_icon_source = $emicons_item_settings['icon_source'];
                    
                }else {
                    $emicons_item_icon_source = 'dashicon'; 
                }

                $emicons_item_icon = isset($emicons_item_settings['content']['menu_icon']) ? $emicons_item_settings['content']['menu_icon'] : '';

               

                

                $emicons_item_icon_source_err = '';
                if($emicons_item_icon_source !='dashicon' && $emicons_item_icon_source !='fontawesome'){
                    $emicons_item_icon_source_err = 'emicons-pro-source-error';
                }

                ?>
                    <div id="tabs-content">
                        <div id="tab1" class="tab-content">
                            <form action="" onsubmit="return false" id='emicons_items_settings' class="<?php echo esc_attr($emicons_item_icon_source_err);?>">
                                <div class="icon-source-wrapper">
                                    <select name="icon_source" id="emicons_source_select" data-menu_item_id="<?php echo esc_attr( $menu_item_id )?>">
                                        <option selected <?php echo $emicons_item_icon_source == 'dashicon' ? 'selected' : '' ?> value="dashicon">Dashicons</option>
                                        <option <?php echo $emicons_item_icon_source == 'fontawesome' ? 'selected' : '' ?> value="fontawesome">Fontawesome</option>
                                        <option <?php echo $emicons_item_icon_source == 'elegant' ? 'selected' : '' ?> value="elegant">Elegant</option>
                                        <option <?php echo $emicons_item_icon_source == 'foundation' ? 'selected' : '' ?> value="foundation">Foundation</option>
                                        <option <?php echo $emicons_item_icon_source == 'elusive' ? 'selected' : '' ?> value="elusive">Elusive</option>
                                        <option <?php echo $emicons_item_icon_source == 'themify' ? 'selected' : '' ?> value="themify">Themify</option>
                                        <option <?php echo $emicons_item_icon_source == 'fontello' ? 'selected' : '' ?> value="fontello">Fontello</option>
                                        <option <?php echo $emicons_item_icon_source == 'generic' ? 'selected' : '' ?> value="generic">Generic</option>
                                        <option <?php echo $emicons_item_icon_source == 'custom' ? 'selected' : '' ?> value="custom">Custom</option>
                                    </select>
                                    <input type="search" name="search_rt_icon" class="search_rt_icon" id="search_rt_icon" placeholder="search here">
                                </div>
                                <div id="emicons_set_icon_dialog">
                                    <input type="hidden" name="menu_icon" value="<?php echo esc_attr($emicons_item_icon); ?>">
                                    <div class="tabs">
                                        <?php 
                                            if($emicons_item_icon_source !== 'custom'){
                                                include 'load-icons.php'; 
                                            }
                                        ?>
                                    </div> <!-- END tabs -->
                                </div>
                            </form>
                            
                        </div>



                        <?php

                            if(isset($emicons_item_settings['css'])){

                                $emicons_item_css = $emicons_item_settings['css'];
                                

                                if( isset( $emicons_item_css['icon_color'] ) ){
                                    $icon_color = $emicons_item_css['icon_color'];
                                }

                                if( isset( $emicons_item_css['icon_font_size'] ) ){
                                    $icon_font_size = $emicons_item_css['icon_font_size'];
                                }

                                if( isset( $emicons_item_css['icon_margin_left'] ) ){
                                    $icon_margin_left = $emicons_item_css['icon_margin_left'];
                                }
                                if( isset( $emicons_item_css['icon_margin_right'] ) ){
                                    $icon_margin_right = $emicons_item_css['icon_margin_right'];
                                }
                                if( isset( $emicons_item_css['icon_margin_top'] ) ){
                                    $icon_margin_top = $emicons_item_css['icon_margin_top'];
                                }
                                if( isset( $emicons_item_css['icon_margin_bottom'] ) ){
                                    $icon_margin_bottom = $emicons_item_css['icon_margin_bottom'];
                                }

                                if( isset( $emicons_item_css['icon_position'] ) ){
                                    $icon_position = $emicons_item_css['icon_position'];
                                }

                            }

                           

                        ?>                        
                    

                        <div id="tab2" class="tab-content" style="display: none;">
                            <form action="" onsubmit="return false" id='emicons_items_css'>       
                                
                                


                                <div class="emicons-menu-option-inputs">
                                    <ul class="emicons-menu-option-input-list">
                                        <li>
                                            <div class="option-label">Color: </div>
                                            <div class="option-inputs">
                                                <label>
                                                    <div class="color-selector">
                                                        <input type="wpcolor" name="icon_color" value="<?php echo esc_attr($icon_color); ?>">
                                                    </div>
                                                </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="option-label">Size: </div>
                                            <div class="option-inputs">
                                                <label>
                                                    <strong>Font-size (ex: 10px)</strong>
                                                    <input type="text" name="icon_font_size" value="<?php echo esc_attr($icon_font_size); ?>">
                                                </label>
                                            </div>
                                        </li>


                                        <?php 
                                        
                                    
                                            
                                        ?>
                                        <li>
                                            <div class="option-label">Spacing: </div>
                                            <div class="option-inputs">
                                                <div class="multi-inputs">
                                                    <label>
                                                        <strong>Left (ex: 2px)</strong>
                                                        <input type="text" name="icon_margin_left" value="<?php echo esc_attr($icon_margin_left) ?>">
                                                    </label>
                                                    <label>
                                                        <strong>Right (ex: 2px)</strong>
                                                        <input type="text" name="icon_margin_right" value="<?php echo esc_attr($icon_margin_right); ?>">
                                                    </label>
                                                    <label>
                                                        <strong>Top (ex: 2px)</strong>
                                                        <input type="text" name="icon_margin_top" value="<?php echo esc_attr($icon_margin_top); ?>">
                                                    </label>
                                                    <label>
                                                        <strong>Bottom (ex: 2px)</strong>
                                                        <input type="text" name="icon_margin_bottom" value="<?php echo esc_attr($icon_margin_bottom); ?>">
                                                    </label>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="option-label">Position: </div>
                                            <div class="option-inputs">
                                                <label>
                                                    <strong>Where you want to show icon?</strong>
                                                    <select name="icon_position" id="icon_position_select">
                                                        <option value="">Default</option>
                                                        <option <?php echo $icon_position == 'left' ? 'selected' : ''?> value="left">Left</option>
                                                        <option <?php echo $icon_position == 'right' ? 'selected' : ''?> value="right">Right</option>
                                                    </select>
                                                </label>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </form>
                        </div>
                    </div> <!-- END tabs-content -->

                <?php

            }
            
            wp_die();
        }

    }
    
    $EMICONS_Admin_Ajax = new EMICONS_Admin_Ajax();
}

