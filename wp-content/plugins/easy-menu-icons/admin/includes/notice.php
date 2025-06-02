<?php
//define('EMICONS_NOTICE__SOURCE_SITE_URL', 'http://localhost:10035');
define('EMICONS_NOTICE__SOURCE_SITE_URL', 'https://themewant.com/menuicon');
class EMICONS_NOTICE{ 

    // Get Instance
    private static $_instance = null;
    public static function instance(){
        if( is_null( self::$_instance ) ){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    function __construct(){

        add_action('admin_notices', array($this, 'emicons_pro_plugin_available_notice'));
        add_action('admin_notices', array($this, 'add_notice_to_notice_bar'));
        add_action('wp_dashboard_setup', array($this, 'add_notice_to_dashboard_widget'), 0);
        add_action('wp_ajax_emicons_ignore_plugin_notice', array($this, 'emicons_ignore_plugin_notice'));

    }

    public function emicons_ignore_plugin_notice() {
	
        $user_id = get_current_user_id();
        check_ajax_referer('emicons_nonce', 'nonce');
        if (isset($_POST['notice_id']) && !empty($_POST['notice_id'])) {
            $notice_id = sanitize_text_field(wp_unslash($_POST['notice_id']));

            if ($user_id && !get_user_meta($user_id, 'emicons_notice_ignore_'.$notice_id, true)) {
                add_user_meta($user_id, 'emicons_notice_ignore_'.$notice_id, 'true', true);
            }else{
                update_user_meta( $user_id, 'emicons_notice_ignore_'.$notice_id, 'true' );
            }

            wp_send_json_success();
        }

        wp_die();
        
    }

    public function get_notice_status($notice_id){
        $user_id = get_current_user_id();
        $status = get_user_meta( $user_id, 'emicons_notice_ignore_'.$notice_id, true );
        return $status;
    }

    public static function get_emicons_notice($args=[]) {

         $notice_source_url = EMICONS_NOTICE__SOURCE_SITE_URL . '/wp-json/reacthemes/v1/get_emicons_notice';
         
     
        // Prepare the body with the page parameter
        $body = wp_json_encode($args); // Encode the array to JSON

        $response = wp_remote_post( $notice_source_url, array(
            'headers'     => [
                'Content-Type' => 'application/json',
            ],
            'timeout'     => 60,
            'redirection' => 5,
            'blocking'    => true,
            'httpversion' => '1.0',
            'sslverify'   => false,
            'data_format' => 'body',
            'body'        => $body
        ) );
        
        return wp_remote_retrieve_body($response); 
    }

    public function expire_notice_by_date($notice_id, $expire_timestamp){

        $today_date = gmdate('Y-m-d');
        $today_timestamp = strtotime($today_date);

        if($today_timestamp >= $expire_timestamp){

            $user_id = get_current_user_id();
            delete_user_meta( $user_id, 'emicons_notice_ignore_'.$notice_id );
            
        }
    }

    public function add_notice_to_notice_bar(){

       

        $args = [
            'screen' => 'notice-bar',
        ];

        $all_notice = $this->get_emicons_notice($args); 

        if(!empty($all_notice)){

            $today_date = gmdate('Y-m-d');
            $today_timestamp = strtotime($today_date);
            $all_notice = json_decode($all_notice, true);

            foreach ($all_notice as $key => $notice) {
                $notice_id = isset($notice['notice_id']) ? $notice['notice_id'] : '';
                $thumbnail_url = isset($notice['thumbnail_url']) ? $notice['thumbnail_url'] : '';
                $title = isset($notice['title']) ? $notice['title'] : '';
                $sub_title = isset($notice['sub_title']) ? $notice['sub_title'] : '';
                $content = isset($notice['content']) ? $notice['content'] : '';
                $action_buttons = isset($notice['action_buttons']) ? $notice['action_buttons'] : '';
                $expire_timestamp = isset($notice['expire_date']) ? strtotime($notice['expire_date']): '';
                
           
               $this->expire_notice_by_date($notice_id, $expire_timestamp);
    
               $notice_ignore_status = $this->get_notice_status($notice_id);
                
            
    
                if($notice_ignore_status != 'true' && $today_timestamp <= $expire_timestamp){

                    
                    ?>
                    <div data-notice_id="<?php echo esc_attr( $notice_id )?>" id="emicons-notice-<?php echo esc_attr( $notice_id )?>" class="emicons-notice notice is-dismissible" expired_time="<?php echo esc_attr( $notice_id )?>" dismissible="global">
    
                        <?php 
                            if(!empty($thumbnail_url)){ ?>
                                <img class="notice-logo" style="" src="<?php echo esc_url($thumbnail_url) ?>">
                            <?php }
                        ?>
                        
    
                        <div class="notice-right-container ">
                            <div class="notice-contents">
                               
                                <?php 
                                    if(!empty($content)){
                                        echo wp_kses_post( $content );
                                    }
                                ?>              
                            </div>
    
                            <?php 
                                if(!empty($action_buttons) && count($action_buttons) > 0){
                                    
                                   echo '<div class="emicons-notice-action-buttons">'; 
                                        foreach ($action_buttons as $key => $button) {
                                            $action_url = isset($button['url']) && !empty($button['url']) ? $button['url'] : '';
                                            $action_title = isset($button['title']) && !empty($button['title']) ? $button['title'] : '';
                                            if(!empty($action_url)){
                                               echo '<a href="'. esc_url($action_url) .'" class="emicons-notice-button button-small" target="_blank">'. esc_html($action_title) .'</a>';
                                            }
                                            
                                        }
                                   echo '</div>';
                                    
                                }
                            ?>
                            
                        </div>
                        <div style="clear:both"></div>
    
                    </div>
                <?php
                }
            }

        }    

    }


    public function add_notice_to_dashboard_widget() {

        // Add the widget
        wp_add_dashboard_widget(
            'emicons_notice_widget',          // Widget ID
            'Easy Menu Icons Stories',      // Widget title
            array($this, 'emicons_notice_widget_callback'), // Display callback
        );

        global $wp_meta_boxes;

        // Move the widget to the top
        $my_widget = array('emicons_notice_widget' => $wp_meta_boxes['dashboard']['normal']['core']['emicons_notice_widget']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['emicons_notice_widget']);
        
        // Prepend it to the beginning of the array
        $wp_meta_boxes['dashboard']['normal']['core'] = $my_widget + $wp_meta_boxes['dashboard']['normal']['core'];
        


    }
    
    public function emicons_notice_widget_callback() {
        $args = [
            'screen' => 'in-widget',
        ];
        
        $all_notice = $this->get_emicons_notice($args); 
        

        if(!empty($all_notice)){

            $today_date = gmdate('Y-m-d');
            $today_timestamp = strtotime($today_date);
            $all_notice = json_decode($all_notice, true);

            foreach ($all_notice as $key => $notice) {
                $notice_id = isset($notice['notice_id']) ? $notice['notice_id'] : '';
                $thumbnail_url = isset($notice['thumbnail_url']) ? $notice['thumbnail_url'] : '';
                $title = isset($notice['title']) ? $notice['title'] : '';
                $sub_title = isset($notice['sub_title']) ? $notice['sub_title'] : '';
                $content = isset($notice['content']) ? $notice['content'] : '';
                $action_buttons = isset($notice['action_buttons']) ? $notice['action_buttons'] : '';
                $expire_timestamp = isset($notice['expire_date']) ? strtotime($notice['expire_date']): '';
                
           
                $this->expire_notice_by_date($notice_id, $expire_timestamp);
    
                $notice_ignore_status = $this->get_notice_status($notice_id);
                
            
    
                if($notice_ignore_status != 'true' && $today_timestamp <= $expire_timestamp){
                    ?>
                    <div data-notice_id="<?php echo esc_attr( $notice_id )?>" id="emicons-notice-<?php echo esc_attr( $notice_id )?>" class="emicons-notice-widget-inner" expired_time="<?php echo esc_attr( $notice_id )?>">
    
                        <?php 
                            if(!empty($thumbnail_url)){ ?>
                                <img class="feature-image" style="" src="<?php echo esc_url($thumbnail_url) ?>">
                            <?php }
                        ?>
                        
    
                        <div class="notice-contents-wrapper">
                            <div class="notice-contents">
                               
                                <?php 
                                    if(!empty($content)){
                                        echo wp_kses_post( $content );
                                    }
                                ?>              
                            </div>
    
                            <?php 
                                if(!empty($action_buttons) && count($action_buttons) > 0){
                                    
                                   echo '<div class="emicons-notice-action-buttons">'; 
                                        foreach ($action_buttons as $key => $button) {
                                            $action_url = isset($button['url']) && !empty($button['url']) ? $button['url'] : '';
                                            $action_title = isset($button['title']) && !empty($button['title']) ? $button['title'] : '';
                                            if(!empty($action_url)){
                                               echo '<a href="'. esc_url($action_url) .'" class="button-secondary button-small" target="_blank">'. esc_html($action_title) .'</a>';
                                            }
                                            
                                        }
                                   echo '</div>';
                                    
                                }
                            ?>
                            
                        </div>
    
                    </div>
                <?php
                }
            }
        }     
        
    }

    public function emicons_pro_plugin_available_notice(){
        $notice_id = 'emicon_pro_plugin_notice';
        $notice_ignore_status = $this->get_notice_status($notice_id);

        if($notice_ignore_status != 'true'){
        
            $content = '"Upgrade to Easy Menu Icons Pro and unlock premium features to enhance your menu icons with advanced styling options, more icon packs, and priority support.';
            $action_buttons = array(
                array(
                    'title' => 'Easy Menu Icon Pro',
                    'url'   => 'https://themewant.com/downloads/easy-menu-icons-pro',
                ),
            );
            
            ?>
            <div data-notice_id="<?php echo esc_attr( $notice_id )?>" id="emicons-notice-<?php echo esc_attr( $notice_id )?>" class="emicons-notice notice is-dismissible" expired_time="<?php echo esc_attr( $notice_id )?>" dismissible="global">

            
                <div class="notice-right-container ">
                    <div class="notice-contents">
                        
                        <?php 
                            if(!empty($content)){
                                echo wp_kses_post( $content );
                            }
                        ?>              
                    </div>

                    <?php 
                        if(!empty($action_buttons) && count($action_buttons) > 0){
                            
                            echo '<div class="emicons-notice-action-buttons">'; 
                                foreach ($action_buttons as $key => $button) {
                                    $action_url = isset($button['url']) && !empty($button['url']) ? $button['url'] : '';
                                    $action_title = isset($button['title']) && !empty($button['title']) ? $button['title'] : '';
                                    if(!empty($action_url)){
                                        echo '<a href="'. esc_url($action_url) .'" class="emicons-notice-button button-small" target="_blank">'. esc_html($action_title) .'</a>';
                                    }
                                    
                                }
                            echo '</div>';
                            
                        }
                    ?>
                    
                </div>
                <div style="clear:both"></div>

            </div>
        <?php
        }
    }

    public static function emicons_after_plugin_activation() {
        $user_id = get_current_user_id();
        
        if ($user_id && !get_user_meta($user_id, 'emicons_notice_ignore_emicon_pro_plugin_notice', true)) {
            add_user_meta($user_id, 'emicons_notice_ignore_emicon_pro_plugin_notice', 'false', true);
        }else{
            update_user_meta( $user_id, 'emicons_notice_ignore_emicon_pro_plugin_notice', 'false' );
        }
    }


}

// Instantiate the class to ensure the menu is registered
if ( class_exists( 'EMICONS_NOTICE' ) ) {
    $EMICONS_NOTICE = new EMICONS_NOTICE();
}



