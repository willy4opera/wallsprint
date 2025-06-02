<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.
if ( !class_exists('EMICONS_Nav')) {
    class EMICONS_Nav {

        function __construct(){

            
            add_action( 'admin_footer', array( $this, 'emicons_pop_up_content' ) );



        
        }



        public function emicons_pop_up_content(){
            ob_start();
            $contents = ob_get_clean();

            ?>
                <div id="emicons-menu-setting-modal" style="display: none;">
                    <div class="emicons-menu-overlay"></div>
                    <div class="emicons-modal-body">
                        <div class="ajax-loader">
                            <img src="<?php echo esc_url(EMICONS_PL_URL.'admin/assets/img/ajax-loader.gif'); ?>" alt="Ajax Loader">
                        </div>
                        <button type="button" class="emicons-menu-modal-closer"><span class="dashicons dashicons-no"></span></button>
                        <div class="emicons-modal-content">
                            
                                <div class="tabs">
                                    <ul id="tabs-nav">
                                        <li>
                                            <a href="#tab1">Icons</a>
                                        </li>
                                        <li><a href="#tab2">Style</a></li>
                                    </ul> <!-- END tabs-nav -->
                                    <div class="tab-contents-wrapper">

                                    </div>
                                    <p class="form-status"></p>
                                    <div class="tab-footer">
                                        <button type="button" class="button save-rt-menu-item-options" action="save">Apply</button>
                                        <button type="button" class="button save-rt-menu-item-options" action="save_close">Save & Close</button>
                                    </div>
                                </div> <!-- END tabs -->
                            
                        </div>
                    </div>
                </div>

            
       



            <?php

            
            echo esc_html($contents);

            
        }

       
    }
    $EMICONS_Nav = new EMICONS_Nav();
}








