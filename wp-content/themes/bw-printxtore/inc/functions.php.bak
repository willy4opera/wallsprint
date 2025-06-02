<?php
/**
 * 
 * Get option redux plugin
 * 
 * @return void
 *
 * */
if(!function_exists('bzotech_get_redux_option')){
    function bzotech_get_redux_option($key){
        $option_name = bzotech_get_option_name();
        $bzotech_theme_option = get_option($option_name);
        if(isset($bzotech_theme_option[$key])){
            $values = $bzotech_theme_option[$key];
            if(isset($values['rgba']) && isset($values['color'])){
                if($values['alpha'] != '1') $values = $values['rgba'];
                else $values = $values['color'];
            }
            return $values;
        }
        else return null;
    }
}
if(!function_exists('bzotech_get_option_name')){
    function bzotech_get_option_name(){
        $bzotech_option_name = apply_filters('bzotech_option_name',"bzotech_theme_option");
        return $bzotech_option_name;
    }
}

/**
 * 
 * Get option theme option
 * 
 * @return void
 *
 * */
if(!function_exists('bzotech_get_option')){
    function bzotech_get_option($key,$default=NULL){
        if(class_exists('Redux')){
            $value = bzotech_get_redux_option($key);
            if(empty($value) && $default !== NULL && $value !== "0") $value = $default;
            return $value;
        }
        else{
            if(function_exists('ot_get_option')){
                $value = ot_get_option($key,$default);
                if(empty($value) && $default) $value = $default;
                return $value;
            }
        }
        
        return $default;
    }
}

/**
 * 
 * Get post by post_type
 * 
 * @return void
 *
 * */
if(!function_exists('bzotech_list_post_type')){
    function bzotech_list_post_type($post_type = 'page',$add_empty = false,$default=array(),$admin=true){
        global $post;
        $post_temp = $post;
        $page_list = array();
        if($add_empty) $page_list[''] = esc_html__('-- Choose One --','bw-printxtore');
        if(is_array($default) and count($default)>0){
            foreach ($default as $key => $value) {
               $page_list[$key] =  $value;
            }
            
        }
        if($admin){
            if(is_admin()){

                $pages = get_posts( array( 'post_type' => $post_type, 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
                
                if(is_array($pages)){
                    foreach ($pages as $page) {
                        $page_list[$page->ID] = $page->post_title;
                    }
                }
            }
        }else {

            $pages = get_posts( array( 'post_type' => $post_type, 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
            
            if(is_array($pages)){
                foreach ($pages as $page) {
                    $page_list[$page->ID] = $page->post_title;
                }
            }
        }
        $post = $post_temp;
        return $page_list;
    }
}

/**
 * 
 * Get style inline
 * 
 * @return string
 *
 * */
if(!function_exists('bzotech_add_html_attr')){   
    function bzotech_add_html_attr($value,$echo = false,$attr='style'){
        $output = '';
        if(!empty($attr)){
            $output = $attr.'="'.$value.'"';
        }
        if($echo) echo apply_filters('bzotech_output_content',$output);
        else return $output;
    }
}

/**
 * 
 * Get google font
 * 
 * @return string
 *
 * */
if ( !function_exists( 'bzotech_get_google_link' ) ) {
    function bzotech_get_google_link($fonts=null) {
        $fonts_list2 = $font1 ='';
        $fonts_list  = 'Outfit:400,500,600,700';
        if(!empty($fonts) && is_array($fonts)){
            $step ='|';
            $dem =0;
            foreach ($fonts as $key => $value) {
                $fonts_add = explode(',', $value);
                if('Outfit' !== $fonts_add[0]){
                    if($key == 0){
                        $font1 = $fonts_add[0];
                        $fonts_list2 .= $fonts_add[0].':100,300,400,500,600,700';
                    }else if($font1 !==$fonts_add[0]){
                        $fonts_list2 .= $step.$fonts_add[0].':100,300,400,500,600,700';
                    }
                    $dem = $dem+1;
                    
                }
            }
            if($dem==2)$fonts_list='';
            if($dem==1)$fonts_list=$fonts_list.'|';
            if(!empty($fonts_list2)) $fonts_list = $fonts_list.$fonts_list2;
        }
        $fonts_list  = array($fonts_list);
        if ( 'off' !== _x( 'on', 'Google font: on or off', 'bw-printxtore' ) ) {


            $fonts_url = add_query_arg( array(
                'family' => urlencode( implode( '|', $fonts_list ) ),
            ), "//fonts.googleapis.com/css" );
        }

        return $fonts_url;
    }
}

/**
 * 
 * Get current ID
 * 
 * @return int
 *
 * */
if(!function_exists('bzotech_get_current_id')){   
    function bzotech_get_current_id(){
        $id = get_the_ID();
        if(is_front_page() && is_home()) $id = (int)get_option( 'page_on_front' );
        if(!is_front_page() && is_home()) $id = (int)get_option( 'page_for_posts' );
        if(is_archive() || is_search()) $id = 0;
        if (class_exists('woocommerce')) {
            if(is_shop()) $id = (int)get_option('woocommerce_shop_page_id');
            if(is_cart()) $id = (int)get_option('woocommerce_cart_page_id');
            if(is_checkout()) $id = (int)get_option('woocommerce_checkout_page_id');
            if(is_account_page()) $id = (int)get_option('woocommerce_myaccount_page_id');
        }
        return $id;
    }
}

/**
 * 
 * Get option value by ID default get theme option
 * 
 * @return void
 *
 * */
if(!function_exists('bzotech_get_value_by_id')){   
    function bzotech_get_value_by_id($key,$meta_empty = false,$by_theme_option="by-theme-option"){
        if(!empty($key)){
            $id = bzotech_get_current_id();
            $value = get_post_meta($id,$key,true);
            if(isset($value['rgba']) && isset($value['color'])){
                if($value['alpha'] != '1') $value = $value['rgba'];
                else $value = $value['color'];
            }
             if($key == 'body_typo'|| $key == 'title_typo'){
               if(empty($value['font-family']) && !$meta_empty)  $value = bzotech_get_option($key);
            }else if((empty($value) || $value == $by_theme_option) && !$meta_empty) $value = bzotech_get_option($key);
            $session_page = bzotech_get_option('session_page');

            if($session_page == '1'){
                if($key == 'bzotech_header_page' || $key == 'bzotech_footer_page' || $key == 'main_color' || $key == 'main_color2' || $key == 'body_bg'){
                    session_start();
                    $val_meta = get_post_meta($id,$key,true);
                    if(isset($val_meta['rgba']) && isset($val_meta['color'])){
                        if($val_meta['alpha'] != '1') $val_meta = $val_meta['rgba'];
                        else $val_meta = $val_meta['color'];
                    }
                    if(!empty($val_meta)) $_SESSION[$key] = $val_meta; 
                    if(isset($_SESSION[$key])) $session_val = $_SESSION[$key];
                    else $session_val = '';
                    if(!empty($session_val)) $value = $session_val;
                }
                if($key == 'body_typo'|| $key == 'title_typo'){
                    session_start();
                    $val_meta = get_post_meta($id,$key,true);
                   
                    if(!empty($val_meta['font-family'])) $_SESSION[$key] = $val_meta; 
                    if(isset($_SESSION[$key])) $session_val = $_SESSION[$key];
                    else $session_val = '';
                    if(!empty($session_val)) $value = $session_val;

                }
            }
            return $value;
        }
        else return esc_html__('Missing a variable of this funtion','bw-printxtore');
    }
}


/**
 * 
 * Check woocommerce page
 * 
 * @return bool
 *
 * */
if (!function_exists('bzotech_is_woocommerce_page')){
    function bzotech_is_woocommerce_page() {
        if(  function_exists ( "is_woocommerce" ) && is_woocommerce()){
                return true;
        }
        $woocommerce_keys   =   array ( "woocommerce_shop_page_id" ,
                                        "woocommerce_terms_page_id" ,
                                        "woocommerce_cart_page_id" ,
                                        "woocommerce_checkout_page_id" ,
                                        "woocommerce_pay_page_id" ,
                                        "woocommerce_thanks_page_id" ,
                                        "woocommerce_myaccount_page_id" ,
                                        "woocommerce_edit_address_page_id" ,
                                        "woocommerce_view_order_page_id" ,
                                        "woocommerce_change_password_page_id" ,
                                        "woocommerce_logout_page_id" ,
                                        "woocommerce_lost_password_page_id" ) ;
        foreach ( $woocommerce_keys as $wc_page_id ) {
                if ( get_the_ID () == get_option ( $wc_page_id , 0 ) ) {
                        return true ;
                }
        }
        return false;
    }
}

/**
 * 
 * Check woocommerce inner page
 * 
 * @return bool
 *
 * */
if (!function_exists('bzotech_is_woocommerce_page_inner')){
    function bzotech_is_woocommerce_page_inner() {
        if(  function_exists ( "is_woocommerce" ) && is_woocommerce()){
                return true;
        }
        $woocommerce_keys   =   array ( 
                                        "woocommerce_cart_page_id" ,
                                        "woocommerce_checkout_page_id" ,
                                        "woocommerce_pay_page_id" ,
                                        "woocommerce_thanks_page_id" ,
                                        "woocommerce_myaccount_page_id" ,
                                        "woocommerce_edit_address_page_id" ,
                                        "woocommerce_view_order_page_id" ,
                                        "woocommerce_change_password_page_id" ,
                                        "woocommerce_logout_page_id" ,
                                        "woocommerce_lost_password_page_id" ) ;
        foreach ( $woocommerce_keys as $wc_page_id ) {
                if ( get_the_ID () == get_option ( $wc_page_id , 0 ) ) {
                        return true ;
                }
        }
        return false;
    }
}

/**
 * 
 * Get preload
 * 
 * @return html
 *
 * */
if(!function_exists('bzotech_preload')){
    function bzotech_preload(){
        $preload = bzotech_get_option('show_preload');
        if($preload == '1'):
            $preload_style = bzotech_get_option('preload_style');
            $preload_bg = bzotech_get_option('preload_bg');
            $preload_img = bzotech_get_option('preload_image');
            if(isset($preload_img['url'])) $preload_img = $preload_img['url'];
        ?>
        <div id="loading" class="preload-loading preload-style-<?php echo esc_attr($preload_style)?>">
            <div id="loading-center">
                <?php
                switch ($preload_style) {
                    case 'style2':
                        ?>
                        <div id="loading-center-absolute">
                            <div id="object<?php echo esc_attr($preload_style)?>"></div>
                        </div>
                        <?php
                        break;

                    case 'style3':
                        ?>
                        <div id="loading-center-absolute<?php echo esc_attr($preload_style)?>">
                            <div class="object<?php echo esc_attr($preload_style)?>" id="object_one<?php echo esc_attr($preload_style)?>"></div>
                            <div class="object<?php echo esc_attr($preload_style)?>" id="object_two<?php echo esc_attr($preload_style)?>"></div>
                            <div class="object<?php echo esc_attr($preload_style)?>" id="object_three<?php echo esc_attr($preload_style)?>"></div>
                            <div class="object<?php echo esc_attr($preload_style)?>" id="object_four<?php echo esc_attr($preload_style)?>"></div>
                        </div>
                        <?php
                        break;

                    case 'style4':
                        ?>
                        <div id="loading-center-absolute<?php echo esc_attr($preload_style)?>">
                            <div class="object<?php echo esc_attr($preload_style)?>" id="object_one<?php echo esc_attr($preload_style)?>"></div>
                            <div class="object<?php echo esc_attr($preload_style)?>" id="object_two<?php echo esc_attr($preload_style)?>"></div>
                            <div class="object<?php echo esc_attr($preload_style)?>" id="object_three<?php echo esc_attr($preload_style)?>"></div>
                        </div>
                        <?php
                        break;

                    case 'style5':
                        ?>
                        <div id="loading-center-absolute<?php echo esc_attr($preload_style)?>">
                            <div class="object<?php echo esc_attr($preload_style)?>" id="first_object<?php echo esc_attr($preload_style)?>"></div>
                            <div class="object<?php echo esc_attr($preload_style)?>" id="second_object<?php echo esc_attr($preload_style)?>"></div>
                        </div>
                        <?php
                        break;

                    case 'style6':
                        ?>
                        <div id="loading-center-absolute<?php echo esc_attr($preload_style)?>">
                            <div class="object<?php echo esc_attr($preload_style)?>" id="object_one<?php echo esc_attr($preload_style)?>"></div>
                            <div class="object<?php echo esc_attr($preload_style)?>" id="object_two<?php echo esc_attr($preload_style)?>"></div>
                            <div class="object<?php echo esc_attr($preload_style)?>" id="object_three<?php echo esc_attr($preload_style)?>"></div>
                            <div class="object<?php echo esc_attr($preload_style)?>" id="object_four<?php echo esc_attr($preload_style)?>"></div>
                            <div class="object<?php echo esc_attr($preload_style)?>" id="object_five<?php echo esc_attr($preload_style)?>"></div>
                        </div>
                        <?php
                        break;

                    case 'style7':
                        ?>
                        <div id="loading-center-absolute<?php echo esc_attr($preload_style)?>">
                            <div class="object<?php echo esc_attr($preload_style)?>" id="object_one<?php echo esc_attr($preload_style)?>"></div>
                        </div>
                        <?php
                        break;

                    case 'custom-image':
                        ?>
                        <div id="loading-center-absolute-image">
                            <img src="<?php echo esc_url($preload_img)?>" alt="<?php esc_attr_e("preload-image",'bw-printxtore');?>"/>
                        </div>
                        <?php
                        break;
                    
                    default:
                        ?>
                        <div id="loading-center-absolute">
                            <div class="object" id="object_four"></div>
                            <div class="object" id="object_three"></div>
                            <div class="object" id="object_two"></div>
                            <div class="object" id="object_one"></div>
                        </div>
                        <?php
                        break;
                }
                ?> 
            </div>
        </div>
        <?php endif;
    }
}


/**
 * 
 * Get template file to forder bzotech-templates
 * 
 * @return void
 *
 * */
if(!function_exists('bzotech_get_template')){
    function bzotech_get_template( $view_name,$slug=false,$data=array(),$echo=FALSE ){
        $html = Bzotech_Template::load_view($view_name,$slug,$data,$echo);
        if(!$echo) return $html;
    }
}

/**
 * 
 * Get template file to forder bzotech-templates/posts
 * 
 * @return void
 *
 * */
if(!function_exists('bzotech_get_template_post')){
    function bzotech_get_template_post( $view_name,$slug=false,$data=array(),$echo=FALSE ){
        $view_name = 'posts/'.$view_name;
        $html = Bzotech_Template::load_view($view_name,$slug,$data,$echo);
        if(!$echo) return $html;
    }
}

/**
 * 
 * Get template file to forder bzotech-templates/elements
 * 
 * @return void
 *
 * */
if(!function_exists('bzotech_get_template_element')){
    function bzotech_get_template_element( $view_name,$slug=false,$data=array(),$echo=FALSE ){
        $view_name = 'elements/'.$view_name;
        $html = Bzotech_Template::load_view($view_name,$slug,$data,$echo);
        if(!$echo) return $html;
    }
}

/**
 * 
 * Get template file to forder bzotech-templates/products
 * 
 * @return void
 *
 * */
if(!function_exists('bzotech_get_template_product')){
    function bzotech_get_template_product( $view_name,$slug=false,$data=array(),$echo=FALSE ){
        $view_name = 'products/'.$view_name;
        $html = Bzotech_Template::load_view($view_name,$slug,$data,$echo);
        if(!$echo) return $html;
    }
}

/**
 * 
 * Get template file to forder bzotech-templates/woocommerce
 * 
 * @return void
 *
 * */
if(!function_exists('bzotech_get_template_woocommerce')){
    function bzotech_get_template_woocommerce( $view_name,$slug=false,$data=array(),$echo=FALSE ){
        $view_name = 'woocommerce/'.$view_name;
        $html = Bzotech_Template::load_view($view_name,$slug,$data,$echo);
        if(!$echo) return $html;
    }
}

/**
 * 
 * Get template file to forder bzotech-templates/widgets
 * 
 * @return void
 *
 * */
if(!function_exists('bzotech_get_template_widget')){
    function bzotech_get_template_widget( $view_name,$slug=false,$data=array(),$echo=FALSE ){
        $view_name = 'widgets/'.$view_name;
        $html = Bzotech_Template::load_view($view_name,$slug,$data,$echo);
        if(!$echo) return $html;
    }
}

/**
 * 
 * Get template file to forder bzotech-templates/elementor
 * 
 * @return void
 *
 * */

if(!function_exists('bzotech_get_template_elementor_global')){
    function bzotech_get_template_elementor_global( $view_name,$slug=false,$data=array(),$echo=FALSE){
        
        $view_name = 'elementor/global/'.$view_name;
        $html = Bzotech_Template::load_view($view_name,$slug,$data,$echo);
        if(!$echo) return $html;
    }
}

/**
 * 
 * Echo sidebar by position
 * 
 * @return void
 *
 * */
if(!function_exists('bzotech_output_sidebar')){
    function bzotech_output_sidebar($position){
        $sidebar = bzotech_get_sidebar();
        $sidebar_pos = $sidebar['position'];
        if($sidebar_pos == $position) get_sidebar();
    }
}

/**
 * 
 * Get list sidebar
 * 
 * @return array
 *
 * */
if(!function_exists('bzotech_get_sidebar_list')){
    function bzotech_get_sidebar_list(){
        global $wp_registered_sidebars;
        $sidebars = array(
            esc_html__('--Select--','bw-printxtore') => ''
            );
        foreach( $wp_registered_sidebars as $id=>$sidebar ) {
          $sidebars[ $sidebar[ 'name' ] ] = $id;
        }
        return $sidebars;
    }
}

/**
 * 
 * Get sidebar
 * 
 * @return array
 *
 * */
if(!function_exists('bzotech_get_sidebar')){
    function bzotech_get_sidebar(){
        $default=array(
            'position'=>'right',
            'style'=>'default',
            'id'      =>'blog-sidebar'
        );
        if(class_exists("woocommerce") && bzotech_is_woocommerce_page()) $default['id'] = 'woocommerce-sidebar';
        return apply_filters('bzotech_get_sidebar',$default);
    }
}

/**
 * 
 * Echo class by sidebar
 * 
 * @return string
 *
 * */
if(!function_exists('bzotech_get_main_class')){
    function bzotech_get_main_class(){
        $sidebar=bzotech_get_sidebar();
        $sidebar_pos=$sidebar['position'];
        $main_class = 'content-wrap content-no-sidebar bzotech-col-md-12';
        if($sidebar_pos != 'no' && is_active_sidebar( $sidebar['id'])) $main_class = 'content-wrap content-sidebar-type-'.$sidebar['style'].' content-sidebar-'.$sidebar_pos.' bzotech-col-lg-9 bzotech-col-md-8 bzotech-col-sm-8 bzotech-col-xs-12';
        return apply_filters('bzotech_main_class',$main_class);
    }
}

/**
 * 
 * Get size string to array
 * 
 * @return array
 *
 * */
if(!function_exists('bzotech_get_size_crop')){
    function bzotech_get_size_crop($size='',$default=''){
        if(!empty($size) && strpos($size, 'x')){
            $size = str_replace('|', 'x', $size);
            $size = str_replace(',', 'x', $size);
            $size = explode('x', $size);
        }
        if(empty($size) && !empty($default)) $size = $default;
        return $size;
    }
}

/**
 * 
 * Get metabox
 * 
 * @return html
 *
 * */
if(!function_exists('bzotech_display_metabox')){
    function bzotech_display_metabox($type ='', $data = array(), $split = '|',$class_css=''){
       
        if(empty($data)) $data = ['author','comments'];
        switch ($type) {
            case 'detail-post':
                
                if(!empty($data)){ ?>
                    <ul class="list-inline-block post-meta-data <?php echo esc_attr($class_css); ?>">
                    <?php
                        foreach ($data as $key => $value) {
                            switch ($value) {
                                case 'date':
                                    ?>
                                    <li class="post-meta-data__item meta-date">
                                        <div class="post-meta-data__content">
                                            <i class="lar la-calendar-minus"></i>
                                            <span><?php echo get_the_date() ?></span>
                                        </div>
                                        <?php if($key < (count($data)-1)) echo '<span class="split">'.$split.'</span>';?>
                                    </li>
                                    <?php
                                    break;

                                case 'cats':
                                    $cats = get_the_category_list('<span>, </span>');
                                    if($cats): ?>
                                        <li class="post-meta-data__item meta-cats">  
                                            <div class="post-meta-data__content">
                                                                    
                                                <?php echo apply_filters('bzotech_output_content',$cats);?>
                                            </div>
                                            <?php if($key < (count($data)-1)) echo '<span class="split">'.$split.'</span>';?>
                                        </li>
                                    
                                    <?php endif;
                                    break;

                                case 'tags':
                                    $tags = get_the_tag_list('',', ','');
                                    if($tags):?>
                                        <li class="post-meta-data__item meta-tags">
                                            <div class="post-meta-data__content">
                                                <i class="las la-hashtag "></i>
                                                <?php if($tags) echo apply_filters('bzotech_output_content',$tags); else esc_html_e("No Tag",'bw-printxtore');?>
                                            </div>
                                            <?php if($key < (count($data)-1)) echo '<span class="split">'.$split.'</span>';?>
                                        </li>
                                    <?php endif;
                                    break;

                                case 'comments':
                                    ?>

                                    <li class="post-meta-data__item comments-item">
                                        <div class="post-meta-data__content">
                                            <i class="las la-comments"></i>
                                            <a href="<?php echo esc_url( get_comments_link() ); ?>"><?php echo get_comments_number(); ?> 
                                                <span>
                                                <?php 
                                                    if(get_comments_number() != 1) esc_html_e('Comments', 'bw-printxtore') ;
                                                    else esc_html_e('Comment', 'bw-printxtore') ;
                                                ?>
                                                </span>
                                            </a>
                                        </div>
                                        <?php if($key < (count($data)-1)) echo '<span class="split">'.$split.'</span>';?>
                                    </li>
                                    <?php
                                    break;

                                case 'views':
                                    ?>
                                    <li class="post-meta-data__item meta-views">
                                        <div class="post-meta-data__content">
                                            <i class="las la-eye"></i>
                                            <span>
                                                <?php echo bzotech_get_post_view();
                                                ?>
                                            </span>
                                        </div>
                                        <?php if($key < (count($data)-1)) echo '<span class="split">'.$split.'</span>';?>
                                    </li>
                                  
                                    <?php
                                    break;
                                case 'like':
                                    if(function_exists('bzotech_getPostLikeLink')){
                                    ?>
                                        <li class="post-meta-data__item meta-like">
                                            <div class="post-meta-data__content">
                                                <?php echo bzotech_getPostLikeLink(get_the_ID()); ?>
                                            </div>
                                            <?php if($key < (count($data)-1)) echo '<span class="split">'.$split.'</span>';?>
                                        </li>
                                  
                                    <?php }
                                    break;

                                default:
                                    ?>
                                    <li class="post-meta-data__item avata">
                                        <div class="post-meta-data__content">
                                            <i class="las la-user"></i><?php echo esc_html__('By: ','bw-printxtore');?>
                                            <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ); ?>">                                                                                        
                                                <span class="name"><?php echo get_the_author(); ?></span>
                                                    
                                            </a>
                                        </div>
                                        <?php if($key < (count($data)-1)) echo '<span class="split">'.$split.'</span>';?>
                                    </li>
                                    <?php
                                    break;
                            }                            
                        }  
                         ?></ul>
                <?php                      
                }
                break;
            case 'tags-share':
                $check_share = bzotech_get_option('post_single_share',array());
                $post_type = get_post_type();
                $tags = get_the_tag_list('','','');
                $check_navigation   =  bzotech_get_option('post_single_navigation','0');
                if((isset($check_share[$post_type]) && $check_share[$post_type] == '1') || $tags || $check_navigation == '1') { ?>

                    <div class="tags-and-share <?php echo esc_attr($class_css); ?>">
                       

                        <div class="flex-wrapper align-content-center justify_content-space-between">
                          
                            <?php
                            if ($tags):
                                $tag_count = count(get_the_tags()); ?>
                                <div class="tags-post-wrap flex-wrapper">
                                    <span class="tags-and-share-title"><?php esc_html_e('Tag', 'bw-printxtore') ; ?></span>
                                    <div class="tags-post">
                                        <?php echo apply_filters(' bzotech_output_content', $tags); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ((isset($check_share[$post_type]) && $check_share[$post_type] == '1')) { ?>
                                <div class="single-post-share-wrap flex-wrapper">
                                    <span class="tags-and-share-title"><?php esc_html_e('Share', 'bw-printxtore') ; ?></span>
                                    <?php
                                     bzotech_get_template('share', '', array('el_class' => 'single-post-share text-right'), true);
                                    ?>
                                </div>
                            <?php } ?>
                        </div>
                        
                    </div>
                    <?php
                }
                break;
            case 'grid-post':
                if(!empty($data)){ ?>
                    <ul class="list-inline-block post-meta-data <?php echo esc_attr($class_css); ?>">
                    <?php
                        foreach ($data as $key => $value) {
                            switch ($value) {
                                case 'date':
                                    ?>
                                    <li class="post-meta-data__item meta-date">
                                        <div class="post-meta-data__content">
                                            <i class="fa fa-calendar"></i>
                                            <span><?php echo get_the_date() ?></span>
                                        </div>
                                        <?php if($key < (count($data)-1)) echo '<span class="split">'.$split.'</span>';?>
                                    </li>
                                    <?php
                                    break;

                                case 'cats':
                                    $cats = get_the_category_list('<span>,</span>');
                                    if($cats): ?>
                                        <li class="post-meta-data__item meta-cats">  
                                            <div class="post-meta-data__content">
                                                <i class="fa fa-folder"></i>                          
                                                <?php echo '<div class="flex-wrapper flex_wrap-wrap align_items-center">'.apply_filters('bzotech_output_content',$cats),'</div>';?>
                                            </div>
                                            <?php if($key < (count($data)-1)) echo '<span class="split">'.$split.'</span>';?>
                                        </li>
                                    
                                    <?php endif;
                                    break;

                                case 'tags':
                                    $tags = get_the_tag_list('','<span>,</span>','');
                                    if($tags):?>
                                        <li class="post-meta-data__item meta-tags">
                                            <div class="post-meta-data__content">
                                                <i class="fa fa-tags"></i>
                                                <?php if($tags) echo '<div class="flex-wrapper flex_wrap-wrap align_items-center">'.apply_filters('bzotech_output_content',$tags); else esc_html_e("No Tag",'bw-printxtore').'</div>';?>
                                            </div>
                                            <?php if($key < (count($data)-1)) echo '<span class="split">'.$split.'</span>';?>
                                        </li>
                                    <?php endif;
                                    break;

                                case 'comments':
                                    ?>

                                    <li class="post-meta-data__item comments-item">
                                        <div class="post-meta-data__content">
                                            <i class="fa fa-comments"></i>
                                            <a href="<?php echo esc_url( get_comments_link() ); ?>"><?php echo get_comments_number(); ?> 
                                                <span>
                                                <?php 
                                                    if(get_comments_number() != 1) esc_html_e('Comments', 'bw-printxtore') ;
                                                    else esc_html_e('Comment', 'bw-printxtore') ;
                                                ?>
                                                </span>
                                            </a>
                                        </div>
                                        <?php if($key < (count($data)-1)) echo '<span class="split">'.$split.'</span>';?>
                                    </li>
                                    <?php
                                    break;

                                case 'views':
                                    ?>
                                    <li class="post-meta-data__item meta-views">
                                        <div class="post-meta-data__content">
                                            <i class="fa fa-eye"></i>
                                            
                                            <span>
                                                <?php echo bzotech_get_post_view(). ' ';
                                                if(bzotech_get_post_view() != 1) echo esc_html__("Views",'bw-printxtore');
                                                else echo esc_html__("View",'bw-printxtore');
                                                ?>
                                            </span>
                                        </div>
                                        <?php if($key < (count($data)-1)) echo '<span class="split">'.$split.'</span>';?>
                                    </li>
                                  
                                    <?php
                                    break;
                                case 'like':
                                    if(function_exists('bzotech_getPostLikeLink')){
                                    ?>
                                        <li class="post-meta-data__item meta-like">
                                            <div class="post-meta-data__content">
                                                <?php echo bzotech_getPostLikeLink(get_the_ID()); ?>
                                            </div>
                                            <?php if($key < (count($data)-1)) echo '<span class="split">'.$split.'</span>';?>
                                        </li>
                                  
                                    <?php }
                                    break;

                                default:
                                    ?>
                                    <li class="post-meta-data__item avata">
                                        <div class="post-meta-data__content">
                                            <i class="fa fa-user"></i><?php echo esc_html__('By: ','bw-printxtore');?>
                                            <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ); ?>">                                                                                        
                                                <span class="name"><?php echo get_the_author(); ?></span>
                                                    
                                            </a>
                                        </div>
                                        <?php if($key < (count($data)-1)) echo '<span class="split">'.$split.'</span>';?>
                                    </li>
                                    <?php
                                    break;
                            }                            
                        }  
                         ?></ul>
                <?php                      
                }
                break;

            case 'grid-post2':
                if(!empty($data)){ ?>
                    <ul class="list-inline-block post-meta-data <?php echo esc_attr($class_css); ?>">
                    <?php
                        foreach ($data as $key => $value) {
                            switch ($value) {
                                case 'date':
                                    ?>
                                    <li class="post-meta-data__item meta-date">
                                        <div class="post-meta-data__content">
                                            <i class="fa fa-calendar"></i>
                                            <span><?php echo get_the_date() ?></span>
                                        </div>
                                        <?php if($key < (count($data)-1)) echo '<span class="split">'.$split.'</span>';?>
                                    </li>
                                    <?php
                                    break;

                                case 'cats':
                                    $cats = get_the_category_list('<span>,</span>');
                                    if($cats): ?>
                                        <li class="post-meta-data__item meta-cats">  
                                            <div class="post-meta-data__content">
                                                <i class="fa fa-folder"></i>                          
                                                <?php echo '<div class="flex-wrapper flex_wrap-wrap align_items-center">'.apply_filters('bzotech_output_content',$cats),'</div>';?>
                                            </div>
                                            <?php if($key < (count($data)-1)) echo '<span class="split">'.$split.'</span>';?>
                                        </li>
                                    
                                    <?php endif;
                                    break;

                                case 'tags':
                                    $tags = get_the_tag_list('','<span>,</span>','');
                                    if($tags):?>
                                        <li class="post-meta-data__item meta-tags">
                                            <div class="post-meta-data__content">
                                                <i class="fa fa-tags"></i>
                                                <?php if($tags) echo '<div class="flex-wrapper flex_wrap-wrap align_items-center">'.apply_filters('bzotech_output_content',$tags); else esc_html_e("No Tag",'bw-printxtore').'</div>';?>
                                            </div>
                                            <?php if($key < (count($data)-1)) echo '<span class="split">'.$split.'</span>';?>
                                        </li>
                                    <?php endif;
                                    break;

                                case 'comments':
                                    ?>

                                    <li class="post-meta-data__item comments-item">
                                        <div class="post-meta-data__content">
                                            <i class="fa fa-comments"></i>
                                            <a href="<?php echo esc_url( get_comments_link() ); ?>"><?php echo get_comments_number(); ?> 
                                                <span>
                                                <?php 
                                                    if(get_comments_number() != 1) esc_html_e('Comments', 'bw-printxtore') ;
                                                    else esc_html_e('Comment', 'bw-printxtore') ;
                                                ?>
                                                </span>
                                            </a>
                                        </div>
                                        <?php if($key < (count($data)-1)) echo '<span class="split">'.$split.'</span>';?>
                                    </li>
                                    <?php
                                    break;

                                case 'views':
                                    ?>
                                    <li class="post-meta-data__item meta-views">
                                        <div class="post-meta-data__content">
                                            <i class="fa fa-eye"></i>
                                            
                                            <span>
                                                <?php echo bzotech_get_post_view(). ' ';
                                                if(bzotech_get_post_view() != 1) echo esc_html__("Views",'bw-printxtore');
                                                else echo esc_html__("View",'bw-printxtore');
                                                ?>
                                            </span>
                                        </div>
                                        <?php if($key < (count($data)-1)) echo '<span class="split">'.$split.'</span>';?>
                                    </li>
                                  
                                    <?php
                                    break;
                                case 'like':
                                    if(function_exists('bzotech_getPostLikeLink')){
                                    ?>
                                        <li class="post-meta-data__item meta-like">
                                            <div class="post-meta-data__content">
                                                <?php echo bzotech_getPostLikeLink(get_the_ID()); ?>
                                            </div>
                                            <?php if($key < (count($data)-1)) echo '<span class="split">'.$split.'</span>';?>
                                        </li>
                                  
                                    <?php }
                                    break;

                                default:
                                    ?>
                                     
                                    <li class="post-meta-data__item avata">
                                        <a class="post-meta-data__content" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ); ?>">       
                                            <?php echo get_avatar( get_the_author_meta( 'ID' ), 40 ); ?>                                                                           
                                            <span class="name"><?php echo get_the_author(); ?></span>
                                                
                                        </a>
                                        <?php if($key < (count($data)-1)) echo '<span class="split">'.$split.'</span>';?>
                                    </li>
                                    <?php
                                    break;
                            }                            
                        }  
                         ?></ul>
                <?php                      
                }
                break;

            
            default:
                
                ?>
                <ul class="list-inline-block post-meta-data <?php echo esc_attr($class_css); ?>">
                    <?php
                    if(!empty($data)){
                        foreach ($data as $key => $value) {
                            switch ($value) {
                                case 'date':
                                    ?>
                                    <li class="meta-item"><i class="fa fa-calendar"></i><span class="silver"><?php echo get_the_date()?></span></li>
                                    <?php if($key < (count($data)-1) && $split) echo '<li class="split">'.$split.'</li>';?>
                                    <?php
                                    break;

                                case 'cats':
                                    $cats = get_the_category_list(' ');
                                    if($cats):?>
                                        <li class="meta-item"><i class="fa fa-folder-open" aria-hidden="true"></i>                            
                                            <?php echo apply_filters('bzotech_output_content',$cats);?>
                                        </li>
                                        <?php if($key < (count($data)-1) && $split) echo '<li class="split">'.$split.'</li>';?>
                                    <?php endif;
                                    break;

                                case 'tags':
                                    $tags = get_the_tag_list(' ',' ',' ');
                                    if($tags):?>
                                        <li class="meta-item"><i class="fa fa-tags" aria-hidden="true"></i>
                                            <?php $tags = get_the_tag_list(' ',' ',' ');?>
                                            <?php if($tags) echo apply_filters('bzotech_output_content',$tags); else esc_html_e("No Tag",'bw-printxtore');?>
                                        </li>
                                        <?php if($key < (count($data)-1) && $split) echo '<li class="split">'.$split.'</li>';?>
                                    <?php endif;
                                    break;

                                case 'comments':
                                    ?>
                                    <li class="meta-item"><i aria-hidden="true" class="fa fa-comment"></i>
                                        <a href="<?php echo esc_url( get_comments_link() ); ?>"><?php echo get_comments_number(); ?> 
                                        <?php 
                                            if(get_comments_number() != 1) esc_html_e('Comments', 'bw-printxtore') ;
                                            else esc_html_e('Comment', 'bw-printxtore') ;
                                        ?>
                                        </a>
                                    </li>
                                    <?php if($key < (count($data)-1) && $split) echo '<li class="split">'.$split.'</li>';?>
                                    <?php
                                    break;

                                case 'views':
                                    ?>
                                    <li class="meta-item"><i class="fa fa-eye"></i>
                                        <span class="silver"><?php echo bzotech_get_post_view(). ' ';
                                        if(bzotech_get_post_view() != 1) echo esc_html__("Views",'bw-printxtore');
                                        else echo esc_html__("View",'bw-printxtore');
                                        ?>
                                        </span>
                                    </li>
                                    <?php if($key < (count($data)-1) && $split) echo '<li class="split">'.$split.'</li>';?>
                                    <?php
                                    break;

                                default:
                                    ?>
                                    <li class="meta-item">
                                        <i class="fa fa-user" aria-hidden="true"></i>
                                        <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"><?php echo get_the_author(); ?></a>
                                    </li>
                                    <?php if($key < (count($data)-1) && $split) echo '<li class="split">'.$split.'</li>';?>
                                    <?php
                                    break;
                            }                            
                        }                        
                    }
                    ?>
                </ul>               
                <?php
                break;
        }
    }
}

/**
 * 
 * Get paging navigation
 * 
 * @return html
 *
 * */
if(!function_exists('bzotech_paging_nav')){
    function bzotech_paging_nav($query = false,$style = '',$echo = true){
        if($query){
            $big = 999999999;
            $paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
            $links = array(
                    'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                    'format'       => '&page=%#%',
                    'current'      => max( 1, $paged ),
                    'total'        => $query->max_num_pages,
                    'end_size'     => 2,
                    'mid_size'     => 1
                );
        }
        else{
            if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
                return;
            }

            $paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
            $pagenum_link = html_entity_decode( get_pagenum_link() );
            $query_args   = array();
            $url_parts    = explode( '?', $pagenum_link );

            if ( isset( $url_parts[1] ) ) {
                wp_parse_str( $url_parts[1], $query_args );
            }

            $pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
            $pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

            $format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
            $format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';

            $links = array(
                'base'          => $pagenum_link,
                'format'        => $format,
                'total'         => $GLOBALS['wp_query']->max_num_pages,
                'current'       => $paged,
                'end_size'      => 2,
                'mid_size'      => 1,
                'add_args'      => array_map( 'urlencode', $query_args ),
            );
        }
        $data = array(
            'links' => $links,
            'style' => $style,
        );
        $html = bzotech_get_template( 'paging-nav', false, $data, $echo );
        if(!$echo) return $html;
    }
}

/**
 * 
 * Get post list style
 * 
 * @return array
 *
 * */
if(!function_exists('bzotech_get_post_list_style')){
    function bzotech_get_post_list_style(){
        $list = apply_filters('bzotech_post_list_item_style',array(
            '' => esc_html__('Default','bw-printxtore'),
            'style2' => esc_html__('Post list 2','bw-printxtore'),
            ));        
        return $list;
    }
}

/**
 * 
 * Get post item style
 * 
 * @return array
 *
 * */
if(!function_exists('bzotech_get_post_style')){
    function bzotech_get_post_style(){
        $list = apply_filters('bzotech_post_item_style',array(
             '' => esc_html__('Default','bw-printxtore'),
             'style2' => esc_html__('Style 2','bw-printxtore'),
             'style3' => esc_html__('Style 3','bw-printxtore'),
             'style4' => esc_html__('Style 4','bw-printxtore'),
             'style5' => esc_html__('Style 5','bw-printxtore'),
             'style6' => esc_html__('Style 6','bw-printxtore'),
             'style7' => esc_html__('Style 7','bw-printxtore'),

            ));
        
        return $list;
    }
}

/**
 * 
 * Get product thumb animation
 * 
 * @return array
 *
 * */
if(!function_exists('bzotech_get_product_thumb_animation')){
    function bzotech_get_product_thumb_animation(){
        $list = apply_filters('bzotech_product_item_style',array(
            ''                  => esc_html__('None','bw-printxtore'),
            'zoom-thumb'        => esc_html__('Zoom','bw-printxtore'),
            'rotate-thumb'      => esc_html__('Rotate','bw-printxtore'),
            'zoomout-thumb'     => esc_html__('Zoom Out','bw-printxtore'),
            'translate-thumb'   => esc_html__('Translate','bw-printxtore'),
            ));
        return $list;
    }
}

/**
 * 
 * Get thumb animation
 * 
 * @return array
 *
 * */
if(!function_exists('bzotech_get_thumb_animation')){
    function bzotech_get_thumb_animation(){
        $list = apply_filters('bzotech_thumb_animation',array(
            ''                          => esc_html__("Default",'bw-printxtore'),
            'zoom-image'                => esc_html__("Zoom",'bw-printxtore'),
            'fade-out-in'               => esc_html__("Fade out-in",'bw-printxtore'),
            'zoom-image fade-out-in'    => esc_html__("Zoom Fade out-in",'bw-printxtore'),
            'fade-in-out'               => esc_html__("Fade in-out",'bw-printxtore'),
            'zoom-rotate'               => esc_html__("Zoom rotate",'bw-printxtore'),
            'zoom-rotate fade-out-in'   => esc_html__("Zoom rotate Fade out-in",'bw-printxtore'),
            'overlay-image'             => esc_html__("Overlay",'bw-printxtore'),
            'overlay-image zoom-image'  => esc_html__("Overlay Zoom",'bw-printxtore'),
            'zoom-image line-scale'     => esc_html__("Zoom image line",'bw-printxtore'),
            'gray-image'                => esc_html__("Gray image",'bw-printxtore'),
            'gray-image line-scale'     => esc_html__("Gray image line",'bw-printxtore'),
            'pull-curtain'              => esc_html__("Pull curtain",'bw-printxtore'),
            'pull-curtain gray-image'   => esc_html__("Pull curtain gray image",'bw-printxtore'),
            'pull-curtain zoom-image'   => esc_html__("Pull curtain zoom image",'bw-printxtore'),
        ));
        return $list;
    }
}

/**
 * 
 * Get product list style
 * 
 * @return array
 *
 * */
if(!function_exists('bzotech_get_product_list_style')){
    function bzotech_get_product_list_style(){
        $list = apply_filters('bzotech_product_list_item_style',array(
            ''          => esc_html__('Default','bw-printxtore'),
            'style2'          => esc_html__('Style 2','bw-printxtore'),
            ));
        return $list;
    }
}

/**
 * 
 * Get product item style
 * 
 * @return array
 *
 * */
if(!function_exists('bzotech_get_product_style')){
    function bzotech_get_product_style($data = ''){
        $list = apply_filters('bzotech_product_item_style',array(
            ''          => esc_html__('Default','bw-printxtore'),
            'style14'          => esc_html__('Style 2','bw-printxtore'),
            'style15'          => esc_html__('Style 3','bw-printxtore'),
            'style16'          => esc_html__('Style 4','bw-printxtore'),

            ));
        if(!empty($data) && is_array($data)){
           $list = array_merge($list,$data);
        }
        return $list;
    }
}

/**
 * 
 * Get filter url current
 * 
 * @return array
 *
 * */
if(!function_exists('bzotech_get_filter_url')){
    function bzotech_get_filter_url($key,$value){
        if(function_exists('bzotech_get_current_url')) $current_url = bzotech_get_current_url();
        else{
            if(function_exists('wc_get_page_id')) $current_url = get_permalink( wc_get_page_id( 'shop' ) );
            else $current_url = get_permalink();
        }
        $current_url = get_pagenum_link();
        if(isset($_GET[$key])){
            $current_val_string = sanitize_text_field($_GET[$key]);
            if($current_val_string == $value){
                $current_url = str_replace('&'.$key.'='.$_GET[$key], '', $current_url);
                if(strpos($current_url,'&') > -1 )$current_url = str_replace('?'.$key.'='.$_GET[$key], '?', $current_url);
                else $current_url = str_replace('?'.$key.'='.$_GET[$key], '', $current_url);
            }
            if(strpos($current_val_string,',') > -1 ) $current_val_key = explode(',', $current_val_string);
            else $current_val_key = explode('%2C', $current_val_string);
            $val_encode = str_replace(',', '%2C', $current_val_string);
            if(!empty($current_val_string)){
                if(!in_array($value, $current_val_key)) $current_val_key[] = $value;
                else{
                    $pos = array_search($value, $current_val_key);
                    unset($current_val_key[$pos]);
                }            
                $new_val_string = implode('%2C', $current_val_key);
                $current_url = str_replace($key.'='.$val_encode, $key.'='.$new_val_string, $current_url);
                if (strpos($current_url, '?') == false) $current_url = str_replace('&','?',$current_url);
            }
            else $current_url = str_replace($key.'=', $key.'='.$value, $current_url);     
        }
        else{
            if(strpos($current_url,'?') > -1 ){
                $current_url .= '&amp;'.$key.'='.$value;
            }
            else {
                $current_url .= '?'.$key.'='.$value;
            }
        }
        return $current_url;
    }
}

/**
 * 
 * Get url current by key
 * 
 * @return string
 *
 * */
if(!function_exists('bzotech_get_key_url')){
    function bzotech_get_key_url($key,$value){
        if(function_exists('bzotech_get_current_url')) $current_url = bzotech_get_current_url();
        else{
            if(function_exists('wc_get_page_id')) $current_url = get_permalink( wc_get_page_id( 'shop' ) );
            else $current_url = get_permalink();
        }
        $current_url = get_pagenum_link();
        if(isset($_GET[$key])){
            $current_url = str_replace('&'.$key.'='.$_GET[$key], '', $current_url);
            if(strpos($current_url,'&') > -1 )$current_url = str_replace('?'.$key.'='.$_GET[$key], '?', $current_url);
            else $current_url = str_replace('?'.$key.'='.$_GET[$key], '', $current_url);
        }
        if(strpos($current_url,'?') > -1 ){
            $current_url .= '&amp;'.$key.'='.$value;
        }
        else {
            $current_url .= '?'.$key.'='.$value;
        }
        return $current_url;
    }
}

/**
 * 
 * Get size random
 * 
 * @return array
 *
 * */
if(!function_exists('bzotech_size_random')){
    function bzotech_size_random($size){
        if(count($size) > 2){
            $sizes = array();
            if(is_array($size)){
                foreach ($size as $key => $value) {
                    $i = $key + 1;
                    if($i % 2 == 1 && isset($size[$i])) $sizes[] = array($value,$size[$i]);
                }
            }
            $k = array_rand($sizes);
            $size = $sizes[$k];
        }
        return $size;
    }
}

/**
 * 
 * Set post view
 * 
 * @return void
 *
 * */
if(!function_exists('bzotech_set_post_view')){
    function bzotech_set_post_view($post_id=false){
        if(!$post_id) $post_id=get_the_ID();
        $view=(int)get_post_meta($post_id,'post_views',true);
        $view++;
        update_post_meta($post_id,'post_views',$view);
    }
}

/**
 * 
 * Get post view
 * 
 * @return void
 *
 * */
if(!function_exists('bzotech_get_post_view')){
    function bzotech_get_post_view($post_id=false){
        if(!$post_id) $post_id=get_the_ID();
        return (int)get_post_meta($post_id,'post_views',true);
    }
}

/**
 * 
 * Cut string
 * 
 * @return string
 *
 * */
if(!function_exists('bzotech_substr')){
    function bzotech_substr($string='',$start=0,$end=1){
        $output = '';
        if(!empty($string)){
            $string = strip_tags($string);
            if($end ==''){
                $output = $string;
            }else if((int)$end !== 0 and (int)$end < strlen($string)){
                
                if($string[$end] != ''){
                    for ($i=$end; $i < strlen($string) ; $i++) { 
                        if($string[$i] == ' ' || $string[$i] == '.' || $i == strlen($string)-1){
                            $end = $i;
                            break;
                        }
                    }
                }
                $output = substr($string,$start,$end);
            }else if((int)$end !== 0 and (int)$end > strlen($string)){
                 $output =$string;
            }else{
                 $output ='';
            }
            
        }
        return $output;
    }
}


/**
 * 
 * Get order list
 * 
 * @return array
 *
 * */
if(!function_exists('bzotech_get_order_list')){
    function bzotech_get_order_list($current=false,$extra=array(),$return='array'){
        $default = array(
            esc_html__('None','bw-printxtore')               => 'none',
            esc_html__('ID','bw-printxtore')                 => 'ID',
            esc_html__('Author','bw-printxtore')             => 'author',
            esc_html__('Title','bw-printxtore')              => 'title',
            esc_html__('Name','bw-printxtore')               => 'name',
            esc_html__('Date','bw-printxtore')               => 'date',
            esc_html__('Last Modified Date','bw-printxtore') => 'modified',
            esc_html__('Post Parent','bw-printxtore')        => 'parent',
        );

        if(!empty($extra) and is_array($extra))
        {
            $default=array_merge($default,$extra);
        }

        if($return=="array")
        {
            return $default;
        }elseif($return=='option')
        {
            $html='';
            if(!empty($default)){
                foreach($default as $key=>$value){
                    $selected=selected($key,$current,false);
                    $html.="<option {$selected} value='{$value}'>{$key}</option>";
                }
            }
            return $html;
        }
    }
}

/**
 * 
 * Get size image
 * 
 * @return array
 *
 * */
if(!function_exists('bzotech_get_size_image')){
    function bzotech_get_size_image($default, $value = ''){
        $return = $default;
        if(strpos($value,'x')){
            $size_arr = explode('x',$value);
            if(is_array($size_arr) and count($size_arr) == 2){
                $return = $size_arr;
            }
        }else{
            if($value != '' and !empty($value)){
                $return = $value;
            }else if(strpos($default,'x')){
                $size_arr = explode('x',$default);
                if(is_array($size_arr) and count($size_arr) == 2){
                    $return = $size_arr;
                }
            }
        }
        return $return;
    }
}

/**
 * 
 * Get BreadCrumb
 * 
 * @return html
 *
 * */
if(!function_exists('bzotech_breadcrumb')){
    function bzotech_breadcrumb($step = '',$class_item='') {
        global $post;
       
        if(is_home() && !is_front_page()) echo '<a class="'.$class_item.'" href="'.esc_url(home_url('/')).'">'.esc_html__('Home','bw-printxtore').'</a>'.$step.'<span  class="'.$class_item.'" >'.esc_html__('Blog','bw-printxtore').'</span>';
        else echo '<a class="'.$class_item.'" href="'.esc_url(home_url('/')).'">'.esc_html__('Home','bw-printxtore').'</a>';
        if (is_single()){
            echo apply_filters('bzotech_output_content',$step);
            echo get_the_category_list($step);
            if(!empty(get_the_title()))
            echo apply_filters('bzotech_output_content',$step).'<span class="'.$class_item.'" >'.get_the_title().'</span>';
        } elseif (is_page()) {
            if($post->post_parent){
                $anc = get_post_ancestors( get_the_ID() );
                $title = get_the_title();
                foreach ( $anc as $ancestor ) { 
                    $output = $step.'<a class="'.$class_item.'" href="'.esc_url(get_permalink($ancestor)).'" title="'.get_the_title($ancestor).'">'.get_the_title($ancestor).'</a>';
                }
                echo apply_filters('bzotech_output_content',$output);
                echo apply_filters('bzotech_output_content',$step).'<span class="'.$class_item.'" >'.$title.'</span>';

            } else {
                echo apply_filters('bzotech_output_content',$step).'<span class="'.$class_item.'" >'.get_the_title().'</span>';
            }
        }
        elseif(is_archive()) echo apply_filters('bzotech_output_content',$step).'<span  class="'.$class_item.'" >'.get_the_archive_title().'</span>';
        elseif(is_search()) echo apply_filters('bzotech_output_content',$step).'<span class="'.$class_item.'">'.esc_html__('Search Results for: ','bw-printxtore').get_search_query().'</span>';
        elseif(is_404()) echo apply_filters('bzotech_output_content',$step).'<span class="'.$class_item.'">'.esc_html__('404','bw-printxtore').'</span>';
    }
}


/**
 * 
 * Echo style background
 * 
 * @return string
 *
 * */
if(!function_exists('bzotech_fill_css_background')){
    function bzotech_fill_css_background($data){
        $string = '';
        if(!empty($data['background-color'])) $string .= 'background-color:'.$data['background-color'].';';
        if(!empty($data['background-repeat'])) $string .= 'background-repeat:'.$data['background-repeat'].';';
        if(!empty($data['background-attachment'])) $string .= 'background-attachment:'.$data['background-attachment'].';';
        if(!empty($data['background-position'])) $string .= 'background-position:'.$data['background-position'].';';
        if(!empty($data['background-size'])) $string .= 'background-size:'.$data['background-size'].';';
        if(!empty($data['background-image'])) $string .= 'background-image:url("'.$data['background-image'].'");';
        if(!empty($string)) return Bzotech_Assets::build_css($string);
        else return false;
    }
}

/**
 * 
 * Get text editor
 * 
 * @return html
 *
 * */
if(!function_exists('bzotech_parse_text_editor')){
    function bzotech_parse_text_editor($content){

        $content = shortcode_unautop( $content );
        $content = do_shortcode( $content );
        $content = wptexturize( $content );

        if ( $GLOBALS['wp_embed'] instanceof \WP_Embed ) {
            $content = $GLOBALS['wp_embed']->autoembed( $content );
        }

        return $content;
    }
}

/**
 * 
 * Get button compare
 * 
 * @return html
 *
 * */
if(!function_exists('bzotech_compare_url')){
    function bzotech_compare_url($icon='',$id = false,$text = '',$class='',$tooltip = true){
        $html = $tooltip_html ='';
        if(empty($icon)) $icon = '<i class="las la-exchange-alt"></i>';
        if($tooltip) $tooltip_html = 'title="'.esc_attr($text).'" data-toggle="tooltip" data-placement="top"';
        if(class_exists('YITH_Woocompare')){
            if(!$id) $id = get_the_ID();
            $cp_link = str_replace('&', '&amp;',add_query_arg( array('action' => 'yith-woocompare-add-product','id' => $id )));
            $html = '<a '.$tooltip_html.' href="'.esc_url($cp_link).'" class="product-compare compare compare-link '.esc_attr($class).'" data-product_id="'.get_the_ID().'">'.$icon.'<span>'.$text.'</span></a>';
  
    }
        return $html;
    }
}

/**
 * 
 * Get button wishlist
 * 
 * @return html
 *
 * */
if(!function_exists('bzotech_wishlist_url')){
    function bzotech_wishlist_url($icon='',$text='',$class='',$tooltip = true){
        $html = $tooltip_html ='';
        if(empty($icon)) $icon = '<i class="la-heart lar"></i>';
        if($tooltip) $tooltip_html = 'title="'.esc_attr($text).'" data-toggle="tooltip" data-placement="top"';
        if(class_exists('YITH_WCWL')) 
            $html = '<a '.$tooltip_html.' href="'.esc_url(str_replace('&', '&amp;',add_query_arg( 'add_to_wishlist', get_the_ID() ))).'" class="add_to_wishlist wishlist-link '.esc_attr($class).'" rel="nofollow" data-product-id="'.get_the_ID().'" data-product-title="'.esc_attr(get_the_title()).'">'.$icon.'<span>'.$text.'</span></a>';
        return $html;
    }
}

/**
 * 
 * Get terms filter
 * 
 * @return array
 *
 * */
if(!function_exists('bzotech_get_terms_filter')){
    function bzotech_get_terms_filter($taxonomy){
        $get_terms_args = array( 'hide_empty' => '1' );

        $orderby = wc_attribute_orderby( $taxonomy );

        switch ( $orderby ) {
            case 'name' :
                $get_terms_args['orderby']    = 'name';
                $get_terms_args['menu_order'] = false;
            break;
            case 'id' :
                $get_terms_args['orderby']    = 'id';
                $get_terms_args['order']      = 'ASC';
                $get_terms_args['menu_order'] = false;
            break;
            case 'menu_order' :
                $get_terms_args['menu_order'] = 'ASC';
            break;
        }

        $terms = get_terms( $taxonomy, $get_terms_args );

        if (is_array($terms) && 0 === count( $terms ) ) {
            return;
        }

        switch ( $orderby ) {
            case 'name_num' :
                usort( $terms, '_wc_get_product_terms_name_num_usort_callback' );
            break;
            case 'parent' :
                usort( $terms, '_wc_get_product_terms_parent_usort_callback' );
            break;
        }
        return $terms;
    }
}

/**
 * 
 * remove w3c of ifarme
 * 
 * @return html
 *
 * */
if(!function_exists('bzotech_remove_w3c')){
    function bzotech_remove_w3c($embed_code){
        $embed_code=str_replace('webkitallowfullscreen','',$embed_code);
        $embed_code=str_replace('mozallowfullscreen','',$embed_code);
        $embed_code=str_replace('frameborder="0"','',$embed_code);
        $embed_code=str_replace('frameborder="no"','',$embed_code);
        $embed_code=str_replace('scrolling="no"','',$embed_code);
        $embed_code=str_replace('&','&amp;',$embed_code);
        return $embed_code;
    }
}

/**
 * 
 * fix import category
 * 
 * @return void
 *
 * */
if(!function_exists('bzotech_fix_import_category')){
    function bzotech_fix_import_category($taxonomy,$demo='1'){
        global $bzotech_config;
        $data = $bzotech_config['import_category'][$demo];
        if(!empty($data)){
            $data = json_decode($data,true);
            if(is_array($data)){
                foreach ($data as $cat => $value) {
                    $parent_id = 0;
                    $term = get_term_by( 'slug',$cat, $taxonomy );
                    if(isset($term->term_id)){
                        $term_parent = get_term_by( 'slug', $value['parent'], $taxonomy );
                        if(isset($term_parent->term_id)) $parent_id = $term_parent->term_id;
                        if($parent_id) wp_update_term( $term->term_id, $taxonomy, array('parent'=> $parent_id) );
                        if($value['thumbnail']){
                            if($taxonomy == 'product_cat')  update_woocommerce_term_meta( $term->term_id, 'thumbnail_id', $value['thumbnail']);
                            else{
                                update_term_meta( $term->term_id, 'thumbnail_id', $value['thumbnail']);
                            }
                        }
                    }
                }
            }
        }
    }
}

/**
 * 
 * Get val root css of option by array type
 * 
 * @return void
 *
 * */

if(!function_exists('bzotech_get_css_option_array_type')){
    function bzotech_get_css_option_array_type($key='',$defaults=array()){
        $css ='';
        if(!empty($key)){
            $array= bzotech_get_value_by_id($key);
            if(is_array($array)){
                foreach($array as $attr=>$value){
                    if(!empty($value)){
                        $css .='--bzo-'.$key.'-'.$attr.':'.$value.';';
                    }
                    else if(array_key_exists($attr,$defaults)){
                        $css .='--bzo-'.$key.'-'.$attr.':'.$defaults[$attr].';';
                    }
                }
            }else{
                if(is_array($defaults)){
                    foreach($defaults as $attr=>$value){
                       $css .='--bzo-'.$key.'-'.$attr.':'.$value.';';
                    }
                }
            }
            
        }
        return apply_filters('bzotech_get_css_option_array_type',$css);
    }
}
/**
 * 
 * Get image html
 * 
 * @return html
 * 
 * */
if(!function_exists('bzotech_get_picture_html')){
    function bzotech_get_picture_html($array=[]){
        $output = '';
        if(!empty($array['image'])){ 
            if(!isset($array['media'])) $array['media'] = '768';
            if(!isset($array['image_size'])) $array['image_size'] = 'full';
            if(!isset($array['image_mobile_size'])) $array['image_mobile_size'] = 'full';
            if(empty($array['image_mobile'])) $array['image_mobile'] = $array['image'];
            $output = '<picture>
                            <source media="(min-width:'.$array['media'].'px)" 
                            srcset="'.wp_get_attachment_image_url($array['image'],$array['image_size']).'">
                            '.wp_get_attachment_image($array['image_mobile'],$array['image_mobile_size']).'
                        </picture>';

        }
        return $output;
    }
}
/**
 * 
 * Get image html
 * 
 * @return html
 * 
 * */
if(!function_exists('bzotech_get_icon_svg')){
    function bzotech_get_icon_svg($icon){
        $array =[
                'hide-filters' =>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M4 6H20" stroke="#898989" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M4 12H16" stroke="#898989" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M4 18H12" stroke="#898989" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>',
                'orderby-filters'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M4 18H20" stroke="#898989" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6 12H18" stroke="#898989" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8 6H16" stroke="#898989" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>',
                
        ];
        if(!empty($array[$icon]))
        return $array[$icon];
        else return;
    }
}
if(!function_exists('bzotech_filter_price')){
    function bzotech_filter_price($min,$max,$filtered_posts = array()){
        global $wpdb;
        $matched_products = array( 0 );
        $matched_products_query = apply_filters( 'woocommerce_price_filter_results', $wpdb->get_results( $wpdb->prepare("
            SELECT DISTINCT ID, post_parent, post_type FROM $wpdb->posts
            INNER JOIN $wpdb->postmeta ON ID = post_id
            WHERE post_type IN ( 'product', 'product_variation' ) AND post_status = 'publish' AND meta_key = '%s' AND meta_value BETWEEN %d AND %d
        ", '_price', $min, $max ), OBJECT_K ), $min, $max );

        if (!empty($matched_products_query ) && is_array($matched_products_query) ) {
            foreach ( $matched_products_query as $product ) {
                if ( $product->post_type == 'product' )
                    $matched_products[] = $product->ID;
                if ( $product->post_parent > 0 && ! in_array( $product->post_parent, $matched_products ) )
                    $matched_products[] = $product->post_parent;
            }
        }

        if ( sizeof( $filtered_posts ) == 0) {
            $filtered_posts = $matched_products;
        } else {
            $filtered_posts = array_intersect( $filtered_posts, $matched_products );
        }
        return $filtered_posts;
    }
}

if(!is_admin() && session_status() == PHP_SESSION_NONE){
    session_start();
}
if(!isset($_SESSION['dont_show_popup'])) $_SESSION['dont_show_popup'] = false;
add_action( 'wp_ajax_set_dont_show', 'bzotech_set_dont_show' );
add_action( 'wp_ajax_nopriv_set_dont_show', 'bzotech_set_dont_show' );
if(!function_exists('bzotech_set_dont_show')){
    function bzotech_set_dont_show() {
        $ajax_security = bzotech_get_option('ajax_security');
        if($ajax_security == true){
            check_ajax_referer( 'mailchimp-ajax-nonce', 'security' );
        }
        $checked = sanitize_text_field($_POST['checked']);
        
        if($checked){
            session_start();  
            $_SESSION['dont_show_popup'] = $checked;
        }
        else{
            unset($_SESSION['dont_show_popup']); 
            session_destroy();
        }
        
    }
}
function bzotech_hexToRgb($hex, $alpha = false) {
   $hex      = str_replace('#', '', $hex);
   $length   = strlen($hex);
   $rgb[] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
   $rgb[] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
   $rgb[] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
   if ( $alpha ) {
      $rgb[] = $alpha;
   }
   return $rgb;
}
if(!function_exists('bzotech_mix_color')){
    function bzotech_mix_color($color_1, $color_2, $opacity=1, $weight = 1) {
        $color_1 = bzotech_hexToRgb($color_1);
        $color_2 = bzotech_hexToRgb($color_2);
        $f = function ($x) use ($weight) {
            return $weight * $x;
        };

        $g = function ($x) use ($weight) {
            return (1 - $weight) * $x;
        };

        $h = function ($x, $y) {
            return round($x + $y);
        };
        $rgb= 'rgba(';
        $array_color = array_map($h, array_map($f, $color_1), array_map($g, $color_2));
        foreach($array_color as $key=>$value){
            if($key==0)
                $rgb .= $value;
            else $rgb .= ', '.$value;
        }
        $rgb .= ', '.$opacity.')';
        return $rgb;
    }
}
if(!function_exists('bzotech_text_edit_shortcode') && function_exists('bzotech_reg_shortcode')){
    function bzotech_text_edit_shortcode($args, $content) {
        if(empty($args['drive']))
            $args['drive'] = 'p';
        if(empty($args['style']))
            $args['style'] = '';
        if(empty($args['class']))
            $args['class'] = '';
        $html ='<'.$args['drive'].' class="'.$args['class'].'" style="'.$args['style'].'">'.$content.'</'.$args['drive'].'>';
        return $html;
    }
    bzotech_reg_shortcode( 'bzotech_text', 'bzotech_text_edit_shortcode' );
}
if(!function_exists('bzotech_list_class_style_by_theme')){
    function bzotech_list_class_style_by_theme() {
        $array = [
            'e-main-color'    => esc_html__( 'Main color', 'bw-printxtore' ),
            'e-main-color2'   => esc_html__( 'Main color 2', 'bw-printxtore' ),
            'e-main-color-darken'    => esc_html__( 'Main color darken', 'bw-printxtore' ),
            'e-bg-color'      => esc_html__( 'Background color', 'bw-printxtore' ),
            'e-bg-color2'     => esc_html__( 'Background color 2', 'bw-printxtore' ),
            'e-bg-color-darken'     => esc_html__( 'Background color darken', 'bw-printxtore' ),
            'e-bg-color-lighten'     => esc_html__( 'Background color lighten', 'bw-printxtore' ),
            'e-bg-color-title'     => esc_html__( 'Background color title', 'bw-printxtore' ),
            'e-bg-color-title2'     => esc_html__( 'Background color title2', 'bw-printxtore' ),
            'e-color-body'    => esc_html__( 'Color body', 'bw-printxtore' ),
            'e-color-title'   => esc_html__( 'Color title', 'bw-printxtore' ),
            'e-color-white'   => esc_html__( 'Color white', 'bw-printxtore' ),
            'e-color-gray'   => esc_html__( 'Color gray', 'bw-printxtore' ),
            'e-font-body'   => esc_html__( 'Font body', 'bw-printxtore' ),
            'e-font-title'   => esc_html__( 'Font title', 'bw-printxtore' ),
        ];

        return $array;
    }
}
if(!function_exists('bzotech_list_class_style_hover_by_theme')){
    function bzotech_list_class_style_hover_by_theme() {
        $array = [
            'hover-e-main-color'    => esc_html__( 'Hover main color', 'bw-printxtore' ),
            'hover-e-main-color-darken'    => esc_html__( 'Hover main color darken', 'bw-printxtore' ),
            'hover-e-main-color2'   => esc_html__( 'Hover main color 2', 'bw-printxtore' ),
            'hover-e-color-body'    => esc_html__( 'Hover color body', 'bw-printxtore' ),
            'hover-e-color-title'   => esc_html__( 'Hover color title', 'bw-printxtore' ),
            'hover-e-color-gray'   => esc_html__( 'Hover color gray', 'bw-printxtore' ),
        ];

        return $array;
    }
}
if(!function_exists('bzotech_implode')){
    function bzotech_implode($attr = '', $separator=' ') {

        if(!empty($attr) && is_array($attr)) $return = implode($separator, $attr);
        else $return = '';
        return $return;
    }
}
if(!function_exists('bzotech_get_list_category')){
    function bzotech_get_list_category($taxonomy='category',$by='slug'){
        $listcate = get_terms($taxonomy);
        $newarr = [];
        foreach($listcate as $value){
            if(!empty($value->$by))
            $newarr[$value->$by] = $value->name; 
        }

        return $newarr;

    }
}
if(!function_exists('bzotech_get_id_page_megamenu')){
    function bzotech_get_id_page_megamenu(){
        $attr = array();
        $nav_menus = wp_get_nav_menus();
        if(is_array($nav_menus)){
            foreach($nav_menus as $value){
               $menu_items = wp_get_nav_menu_items( $value->term_id);
               
                if(is_array($menu_items))
                foreach($menu_items as $menu_item){
                    $content = get_post_meta($menu_item->ID,'content1',true);
                    
                     if(!empty($menu_item->content2) && !empty($menu_item->enable_megamenu)) $attr[] = $menu_item->content2;
                }
            }
        }
        return $attr;
    }
}
function minimizeCSSsimple($css){
$css = preg_replace('/\/\*((?!\*\/).)*\*\//', '', $css); 
$css = preg_replace('/\s{2,}/', ' ', $css);
$css = preg_replace('/\s*([:;{}])\s*/', '$1', $css);
$css = preg_replace('/;}/', '}', $css);
return $css;
}

if(!function_exists('bzotech_get_loop_term_meta')){
        function bzotech_get_loop_term_meta($key_meta='',$taxonomy='category'){
            $attr = array();
            if(!empty($key_meta)){
                $terms = get_the_terms(get_the_ID(),$taxonomy);
                if(!empty($terms) && is_array($terms)){
                    foreach ($terms as $key => $term) { 
                        if(!empty($term->term_id)){
                            $attr[$key][$key_meta] = get_term_meta($term->term_id,$key_meta,true);
                            $attr[$key]['name'] = $term->name;
                            $attr[$key]['link'] = get_term_link($term->term_id,$taxonomy);
                        }
                        
                    }
                    
                }
            }
            return $attr;
            
        }
    }