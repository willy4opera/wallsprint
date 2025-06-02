<?php
$page_id = apply_filters('bzotech_header_page_id',bzotech_get_value_by_id('bzotech_header_page'));
if(!empty($page_id)){ 
 ?>
    <div id="header" class="header-page <?php echo 'bzotech-'.str_replace ('.php','',get_page_template_slug($page_id));?> <?php echo'bzotech-header-page-'.get_post_field( 'post_name', $page_id )?>">
       
            <div class="bzotech-container-width">
                <?php echo Bzotech_Template::get_vc_pagecontent($page_id); ?>
            </div>
    </div>
<?php
}
else{?>
    <div id="header" class="header header-default">
         <div class="bzotech-container">
                <div class="flex-wrapper justify_content-space-between align_items-center">
                    <div class="logo-default">
                        <a href="<?php echo esc_url(home_url('/'));?>" title="<?php echo esc_attr__("logo",'bw-printxtore');?>">
                            <?php $bzotech_logo=bzotech_get_option('logo');?>
                            <?php if($bzotech_logo!=''){
                                echo '<h1 class="hidden">'.get_bloginfo('name', 'display').'</h1><img src="' . esc_url($bzotech_logo) . '" alt="'.esc_attr__("logo",'bw-printxtore').'">';
                            }   else { echo '<h1 class="title24 main-color">'.get_bloginfo('name', 'display').'</h1>'; }
                            ?>
                        </a>
                    </div>
                    <?php if ( has_nav_menu( 'primary' ) ) {?>
                    <div class="header-nav-default bzotech-menu-container bzotech_plus_icon menu-global-style- effect-line-bottom">
                            <a href="#" class="toggle-mobile-menu"><i class="white la la-reorder"></i></a>
                                <div class="bzotech-menu-inner">
                                <?php wp_nav_menu( array(
                                        'theme_location'    => 'primary',
                                        'container'         =>false,
                                        'menu_class'      => 'bzotech-navbar-nav',
                                        'walker'            =>new Bzotech_Walker_Nav_Menu(),
                                     )
                                );?>
                                
                            </div>
                    </div>
                    <?php } ?> 
                </div>      
                                   
            </div>
    </div>
<?php
}
