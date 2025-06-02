<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.
if ( !class_exists('EMICONS_admin_settings')) {
    class EMICONS_admin_settings {

        private $emicons_options;
    
        function __construct(){   
            
            add_action( 'admin_menu', [$this, 'emicons_register'] );
            //add_action('rest_api_init', [$this, 'emicons_settings']);
            add_action( 'admin_init', [$this, 'emicons_settings'] );   
    
        }   
    
        public function emicons_register (){
            add_menu_page( 
                __('Easy Menu Icons', 'easy-menu-icons'), 
                __('Easy Menu Icons', 'easy-menu-icons'), 
                'manage_options', 
                'emicons-menu', 
                array($this, 'emicons_plugin_page'), 
                'dashicons-editor-kitchensink',
                5
             );
        }
    
    
    
        public function emicons_settings() {
    
            register_setting(    
                'emicons_options_group', // option_group    
                'emicons_options', // option_name
            );
    
    
    
            add_settings_section(    
                'emicons_setting_section', // id    
                '', // title    
                array( $this, 'emicons_settings_section' ), // callback    
                'emicons-menu-settings' // page    
            );  
    
    
            add_settings_field(
    
                'emicons_width', // id
    
                'Width', // title
    
                array( $this, 'emicons_render_menu_opts' ), // callback
    
                'emicons-menu-settings', // page
    
                'emicons_setting_section' // section
    
            );
    
        }
    
        public function emicons_get_settings_fields() {
    
            $emicons_settings_fields = array();
    
            $style_fields = array(
                array(
                    'id'    => 'icon_color',
                    'name'  => 'icon_color',
                    'type'  => 'wpcolor',
                    'label' => 'Icon Color',
                ),
                array(
                    'id'    => 'icon_font_size',
                    'name'  => 'icon_font_size',
                    'type'  => 'text',
                    'label' => 'Icon Size',
                    'placeholder' => '14px',
                    'ex_text' => 'for ex: 14px',
                ),
                array(
                    'id'    => 'icon_margin',
                    'name'  => 'icon_margin',
                    'type'  => 'dimension',
                    'label' => 'Icon Margin',
                    'placeholder' => '5px',
                    'ex_text' => 'for ex: 5px',
                    'fields' => array(
                        'margin_left' => array('type'=> 'text', 'placeholder' => 'Left', 'ex_text' => 'Margin Left'),
                        'margin_right' => array('type'=> 'text', 'placeholder' => 'Right', 'ex_text' => 'Margin Right'),
                        'margin_top' => array('type'=> 'text', 'placeholder' => 'Top', 'ex_text' => 'Margin Top'),
                        'margin_bottom' => array('type'=> 'text', 'placeholder' => 'Bottom', 'ex_text' => 'Margin Bottom'),
                    )
                ),
            
            );
    
    
            $emicons_settings_fields['style_fields'] = $style_fields;
    
            return $emicons_settings_fields;
    
    
        }
    
        public function emicons_plugin_page (){
            
            $this->emicons_options = get_option( 'emicons_options' ); ?>
            <h1>Easy Menu Icon Settings</h1>
            <?php settings_errors(); ?>
            <div class="emicons-menu-settings-tabs-container">
                <form method="POST" action="options.php">
                    <div class="tabs emicons-menu-settings-tabs">
                        <ul id="tabs-nav">
                            <li><a href="#tab_menu_styles">Icon Styles</a></li>
                            <li><a href="#tab_menu_features">Pro Features</a></li>
                        </ul> <!-- END tabs-nav -->
                        <div class="tab-contents-wrapper">

                            <div id="tab_menu_styles" class="tab-content" style="display: none;">
                                <?php
    
                                    settings_fields( 'emicons_options_group' );	
                                    do_settings_sections( 'emicons-menu-settings' ); 
                                    submit_button();
                                ?>
                            </div>

                            <div id="tab_menu_features" class="tab-content" style="display: none;">
                                <h1>Easy Menu Icons Free Vs RT Easy Menu Icons Pro Features</h1>
                                <div class="emicons-features-list-wrapper">
                                    <div class="emicons-features-list emicons-features-list-free">
                                        <h3>RT Menu Free</h3>
                                        <ul>
                                            <li><span class="dashicons dashicons-yes"></span>DashIcon</li>
                                            <li><span class="dashicons dashicons-yes"></span>Fontawesome Icon</li>
                                            <li><span class="dashicons dashicons-no"></span>Elusive Icon</li>
                                            <li><span class="dashicons dashicons-no"></span>Elegant Icon</li>
                                            <li><span class="dashicons dashicons-no"></span>Foundation Icon</li>
                                            <li><span class="dashicons dashicons-no"></span>Themify Icon</li>
                                            <li><span class="dashicons dashicons-no"></span>Fontello Icon</li>
                                            <li><span class="dashicons dashicons-no"></span>Generic Icon</li>
                                            <li><span class="dashicons dashicons-no"></span>Custom Icon</li>
                                        </ul>
                                    </div>
                                    <div class="emicons-features-list emicons-features-list-free">
                                        <h3>RT Menu Pro</h3>
                                        <ul>
                                            <li><span class="dashicons dashicons-yes"></span>DashIcon</li>
                                            <li><span class="dashicons dashicons-yes"></span>Fontawesome Icon</li>
                                            <li><span class="dashicons dashicons-yes"></span>Elusive Icon</li>
                                            <li><span class="dashicons dashicons-yes"></span>Elegant Icon</li>
                                            <li><span class="dashicons dashicons-yes"></span>Foundation Icon</li>
                                            <li><span class="dashicons dashicons-yes"></span>Themify Icon</li>
                                            <li><span class="dashicons dashicons-yes"></span>Fontello Icon</li>
                                            <li><span class="dashicons dashicons-yes"></span>Generic Icon</li>
                                            <li><span class="dashicons dashicons-yes"></span>Custom Icon</li>
                                        </ul>
                                        <a href="<?php echo esc_url('https://themewant.com/downloads/easy-menu-icons-pro/');?>" target="_blank" class="button button-primary">Updrade to Pro</a>
                                    </div>
                                </div>
                            </div>

                           

                        </div>
                    </div> <!-- END tabs -->
                    
                </form>
            </div>
            
    
            <?php
        }
    
    
        public function emicons_settings_section (){ 

        }
    
        public function emicons_render_menu_opts() {
    
            ?>
        
            <?php
    
            $emicons_settings_fields = $this->emicons_get_settings_fields();    

           
            $Emicons_item_icon_position = '';
            if( isset( $this->emicons_options['icon_position']) ){
                $Emicons_item_icon_position = $this->emicons_options['icon_position'];
            }
            
        
            foreach ($emicons_settings_fields['style_fields'] as $field) {
                if($field['type'] == 'section_start'){
                    ?>
                    <h3 class="settings-section"><?php echo esc_html($field['label']) ?></h3>
                    <?php
                }elseif($field['type'] == 'dimension'){


                    $dimension_fields = isset($field['fields']) && count($field['fields']) > 0 ? $field['fields'] : '';
                    $field_name = $field['name'];

                    $val = [];
                    if( isset( $this->emicons_options[$field['name']]) ){
                        $val = $this->emicons_options[$field['name']];
                    }else if(isset($field['default'])){
                        $val = $field['default'];
                    }

                   
                    if(!empty($dimension_fields)){ ?>
                        <div class="settings-item">
                            <label><?php echo esc_html($field['label']) ?></label>
                            <div class="settings-item-group">
                                <?php 
                                    foreach ($dimension_fields as $key => $field) {
                                        $value = !empty($val[$key]) ? $val[$key] : '';
                                        printf(
                                            '<div class="settings-item">
                                            <input 
                                            type="'. esc_html($field['type']) .'" 
                                            name="emicons_options['.esc_html($field_name).']['. esc_html($key) .']" 
                                            id="emicons_render_menu_opts" value="%s" 
                                            placeholder="'. esc_html($field['placeholder']).'">
                                            </div>',esc_html($value)
                                        );
                                    }
                                ?>
                            </div>
                        </div>
                        
                    <?php } 
                    

                }else{
    
                    $val = '';
                    if( isset( $this->emicons_options[$field['name']]) ){
                        $val = $this->emicons_options[$field['name']];
                    }else if(isset($field['default'])){
                        $val = $field['default'];
                    }
    
                    $placeholder = isset($field['placeholder']) && !empty($field['placeholder']) ? esc_html($field['placeholder']) : '';
                    $ex_text = isset($field['ex_text']) && !empty($field['ex_text']) ? esc_html($field['ex_text']) : '';

                    printf(
                        '<div class="settings-item"><label>'. esc_html($field['label']) .'</label>
                        <input 
                        type="'. esc_html($field['type']).'" 
                        name="emicons_options['. esc_html($field['name']) .']" 
                        id="emicons_render_menu_opts" value="%s" 
                        placeholder="'. esc_html($placeholder).'">
                        <span>'. esc_html($ex_text) .'</span>
                        </div>',esc_html($val)
                    );
                }
                
            }  
            ?>
            <div class="settings-item">
                <label>Position</label>
                <select name="emicons_options[icon_position]" id="icon_position_select">
                    <option value="">Default</option>
                    <option <?php echo $Emicons_item_icon_position == 'left' ? 'selected' : ''?> value="left">Left</option>
                    <option <?php echo $Emicons_item_icon_position == 'right' ? 'selected' : ''?> value="right">Right</option>
                </select>
            </div>
            <?php
            
        }   
    
    }
    new EMICONS_admin_settings();
}
