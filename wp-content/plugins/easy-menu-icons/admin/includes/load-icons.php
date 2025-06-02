<?php
 if($emicons_item_icon_source == 'dashicon'){
    ?>
        <div class="icon-tab-contents-wrapper">
            <div class="icon-tab-content active">
                <div class="emicons_icons-selection-wrapper">
                    <?php 
                        
                
                        $EMICONS_dashicon_json = wp_remote_get(EMICONS_DIR_URL . '/admin/assets/json/dashicons.json', []);

                        try {
                            $icons = json_decode( $EMICONS_dashicon_json['body'] );
                          } catch ( Exception $ex ) {
                            $icons = null;
                          }

                        foreach ($icons as $key => $icon) {
                            ?>
                                <button class="emicons-icon-button" icon_class="dashicons dashicons-<?php echo esc_attr( $key )?>">
                                    <span class="dashicons dashicons-<?php echo esc_attr( $key )?>"></span>
                                    <span class="icon-name"><?php echo esc_attr( $key )?></span>
                                </button>
                            <?php
                        }

                    ?>
                </div>
            </div>
        </div>
    <?php
    
 }
 else if($emicons_item_icon_source == 'fontawesome'){

        $fontawesome_directory = EMICONS_PL_URL . 'admin/assets/json/font-awesome/';
        //var_dump($fontawesome_directory);
                     ?>
                        <ul id="icon-tabs-nav">
                            <li><a href="#solid_icons">Solid</a></li>
                            <li><a href="#regular_icons">Regular</a></li>
                            <li><a href="#brands_icons">Brands</a></li>
                        </ul> <!-- END tabs-nav -->
                        <div class="icon-tab-contents-wrapper">
                            <div id="solid_icons" class="icon-tab-content">
                                <div class="emicons_icons-selection-wrapper">
                                    <?php 

                                        // Solid Icons JSON file 
                                        $EMICONS_elementor_font_awesome_solid_json = wp_remote_get($fontawesome_directory . 'solid.json', []); 

                                       


                                        try {
                                            $icons = json_decode( $EMICONS_elementor_font_awesome_solid_json['body'] );
                                            $icons = json_decode(wp_json_encode($icons), true);
                                            $icons = $icons['icons'];
                                          } catch ( Exception $ex ) {
                                            $icons = null;
                                          }
                                    
                                        foreach ($icons as $key => $icon) {
                                            ?>
                                                <button class="emicons-icon-button" icon_class="fas fa-<?php echo esc_attr( $key )?>">
                                                    <i class="fas fa-<?php echo esc_attr( $key )?>"></i>
                                                    <span class="icon-name"><?php echo esc_attr( $key )?></span>
                                                </button>
                                            <?php
                                        }

                                    ?>
                                </div>
                            </div>
                            <div id="regular_icons" class="icon-tab-content" style="display:none">
                                <div class="emicons_icons-selection-wrapper">
                                    <?php 
                                        // Regular Icons JSON file 
                                        $EMICONS_elementor_font_awesome_solid_json = wp_remote_get($fontawesome_directory . 'regular.json', []); 
                                        try {
                                            $icons = json_decode( $EMICONS_elementor_font_awesome_solid_json['body'] );
                                            $icons = json_decode(wp_json_encode($icons), true);
                                            $icons = $icons['icons'];
                                          } catch ( Exception $ex ) {
                                            $icons = null;
                                          }
                                    
                                        foreach ($icons as $key => $icon) {
                                            ?>
                                                <button class="emicons-icon-button" icon_class="far fa-<?php echo esc_attr( $key )?>">
                                                    <i class="far fa-<?php echo esc_attr( $key )?>"></i>
                                                    <span class="icon-name"><?php echo esc_attr( $key )?></span>
                                                </button>
                                            <?php
                                        }
                                    ?>
                                </div>
                            </div>
                            <div id="brands_icons" class="icon-tab-content" style="display:none">
                                <div class="emicons_icons-selection-wrapper">
                                    <?php 
                                        // Brands Icons JSON file 
                                        $EMICONS_elementor_font_awesome_solid_json = wp_remote_get($fontawesome_directory . 'brands.json', []); 
                                        try {
                                            $icons = json_decode( $EMICONS_elementor_font_awesome_solid_json['body'] );
                                            $icons = json_decode(wp_json_encode($icons), true);
                                            $icons = $icons['icons'];
                                          } catch ( Exception $ex ) {
                                            $icons = null;
                                          }
                                    
                                        foreach ($icons as $key => $icon) {
                                            ?>
                                                <button class="emicons-icon-button" icon_class="fab fa-<?php echo esc_attr( $key )?>">
                                                    <i class="fab fa-<?php echo esc_attr( $key )?>"></i>
                                                    <span class="icon-name"><?php echo esc_attr( $key )?></span>
                                                </button>
                                            <?php
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
        <?php
}else{
    ?>
        <div class="icon-tab-contents-wrapper">
            <p classs="warning"><?php echo esc_html( 'Ops! You can\'t use icon from '.$emicons_item_icon_source. '. Please use our premium plugin and enjoy all icons.' );?></p>
        </div>
    <?php
}
