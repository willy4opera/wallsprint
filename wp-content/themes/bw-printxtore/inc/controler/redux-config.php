<?php
    /**
     * ReduxFramework Barebones Sample Config File
     * For full documentation, please visit: http://docs.reduxframework.com/
     */

    if ( ! class_exists( 'Redux' ) ) {
        return;
    }
    // Redux help function    
    if(!function_exists('bzotech_switch_redux_option')){
        function bzotech_switch_redux_option(){
            $bzotech_option_name = bzotech_get_option_name();
            // Basic Settings
            Redux::setSection( $bzotech_option_name, array(
                'title'            => esc_html__( 'Basic Settings', 'bw-printxtore' ),
                'id'               => 'basic',
                'icon'             => 'el el-home'
            ) );
            Redux::setSection( $bzotech_option_name, array(
                'title'            => esc_html__( 'General', 'bw-printxtore' ),
                'id'               => 'basic-general',
                'subsection'       => true,
                'fields'           => array(
                    
                    array(
                        'id'       => 'bzotech_header_page',
                        'type'     => 'select',
                        'title'    => esc_html__( 'Header Page', 'bw-printxtore' ),
                        'desc'     => esc_html__( 'Select the header style. To edit/create header content, go to "Header Page" in Admin Dashboard. Note: this setting is applied for all pages; for single page setting, please go to each page to config.', 'bw-printxtore' ),
                        'options'  => bzotech_list_post_type('bzotech_header'),
                        'default'  => ''
                    ),
                    array(
                        'id'       => 'bzotech_footer_page',
                        'type'     => 'select',
                        'title'    => esc_html__( 'Footer Page', 'bw-printxtore' ),
                        'desc'     => esc_html__( 'Select the footer style. To edit/create footer content, go to "Footer Page" in Admin Dashboard. Note: this setting is applied for all pages; for single page setting, please go to each page to config."', 'bw-printxtore' ),
                        'options'  => bzotech_list_post_type('bzotech_footer'),
                        'default'  => ''
                    ),
                    array(
                        'id'       => 'bzotech_show_breadrumb',
                        'type'     => 'switch',
                        'title'    => esc_html__( 'Breadcrumb', 'bw-printxtore' ),
                        'desc' => esc_html__( 'Show or hide the breadcrumb.', 'bw-printxtore' ),
                        'default'  => false,
                    ),
                    array(
                        'id'          => 'bzotech_bg_breadcrumb',
                        'type'        => 'media',
                        'title'       => esc_html__('Breadcrumb background image','bw-printxtore'),
                        'desc'        => 'Select image.',
                        'url'          => false,
                        'required'   =>  array(
                            array('bzotech_show_breadrumb','=','1'),
                            array('breadcrumb_page','=',''),
                        ), 
                    ),

                    array(
                        'id'       => 'breadcrumb_page',
                        'type'     => 'select',
                        'title'    => esc_html__( 'Custom breadcrumb page', 'bw-printxtore' ),
                        'desc'     => esc_html__( 'Select the custom breadcrumb page.', 'bw-printxtore' ),
                        'options'  => bzotech_list_post_type('bzotech_mega_item'),
                        'default'  => '',
                    ),

                    array(
                        'id'       => 'bzotech_404_page',
                        'type'     => 'select',
                        'title'    => esc_html__( '404 Page', 'bw-printxtore' ),
                        'desc'     => esc_html__( 'Select Mega Page inserts to the 404 page.', 'bw-printxtore' ),
                        'options'  => bzotech_list_post_type('bzotech_mega_item'),
                        'default'  => ''
                    ),
                    array( 
                        'id'       => 'bzotech_404_page_style',
                        'type'     => 'select',
                        'title'    => esc_html__( '404 Style', 'bw-printxtore' ),
                        'desc'     => esc_html__( 'Choose a style to display.', 'bw-printxtore' ),
                        'options'  => array(
                            ''           => esc_html__('Default','bw-printxtore'),
                            'full-width' => esc_html__('FullWidth','bw-printxtore'),
                        ),
                        'default'  => '',
                        'required' => array('bzotech_404_page','not','')
                    ),

                )
            ) );
            
            Redux::setSection( $bzotech_option_name, array(
                'title'            => esc_html__( 'Preload', 'bw-printxtore' ),
                'id'               => 'preload-general',
                'subsection'       => true,
                'fields'           => array(
                    array(
                        'id'       => 'show_preload',
                        'type'     => 'switch',
                        'title'    => esc_html__( 'Preload', 'bw-printxtore' ),
                        'desc'     => esc_html__( 'Turn on or off the preload option.', 'bw-printxtore' ),
                        'default'  => false,
                    ),
                    array(
                        'id'          => 'preload_bg',
                        'type'        => 'color_rgba',
                        'title'       => esc_html__('Background color','bw-printxtore'),
                        'desc'        => esc_html__( 'Select the default preload background color.', 'bw-printxtore' ),
                        'required'    => array('show_preload','=',true),
                    ),
                    array(
                        'id'          => 'preload_style',
                        'type'        => 'select',
                        'title'       => esc_html__('Preload style','bw-printxtore'),
                        'default'     => 'style2',
                        'options'     => array(
                            '' =>  esc_html__('Style 1','bw-printxtore'),
                            'style2' =>  esc_html__('Style 2','bw-printxtore'),
                            'style3' =>  esc_html__('Style 3','bw-printxtore'),
                            'style4' =>  esc_html__('Style 4','bw-printxtore'),
                            'style5' =>  esc_html__('Style 5','bw-printxtore'),
                            'style6' =>  esc_html__('Style 6','bw-printxtore'),
                            'style7' =>  esc_html__('Style 7','bw-printxtore'),
                            'custom-image' =>  esc_html__('Custom image','bw-printxtore'),
                        ),
                        'desc'        => esc_html__( 'Select the preload style.', 'bw-printxtore' ),
                        'required'    => array('show_preload','=',true),
                    ),
                    array(
                        'id'          => 'preload_image',
                        'type'        => 'media',
                        'title'       => esc_html__('Preload image','bw-printxtore'),
                        'desc'        => 'Select image.',
                        'url'          => false,
                        'required'   =>  array('preload_style','=','custom-image'),
                    ),
                )
            ) );
            Redux::setSection( $bzotech_option_name, array(
                'title'            => esc_html__( 'Other', 'bw-printxtore' ),
                'id'               => 'other-general',
                'subsection'       => true,
                'fields'           => array(
                    array(
                        'id'        => 'show_scroll_top',
                        'type'        => 'select',
                        'options'     => array(
                            '' =>  esc_html__('Off','bw-printxtore'),
                            '1' =>  esc_html__('Style 1','bw-printxtore'),
                            '2' =>  esc_html__('Style 2','bw-printxtore'),
                        ),
                        'title'     => esc_html__('Scroll to Top button', 'bw-printxtore'),
                        'desc'      => esc_html__('Show or hide scroll to top button.', 'bw-printxtore'),
                        'default'   => ''
                    ),
                    array(
                        'id'        => 'show_wishlist_notification',
                        'type'      => 'switch',
                        'title'     => esc_html__('Wislist notififcation', 'bw-printxtore'),
                        'desc'      => esc_html__('Show or hide notification after adding product to wishlist.', 'bw-printxtore'),
                        'default'   => false
                    ),
                    array(
                        'id'        => 'show_too_panel',
                        'type'      => 'switch',
                        'title'     => esc_html__('Tool panel', 'bw-printxtore'),
                        'desc'      => esc_html__('Show or hide sidebar tool panels.', 'bw-printxtore'),
                        'default'   => false
                    ),
                    array(
                        'id'          => 'tool_panel_page',
                        'type'        => 'select',
                        'title'       => esc_html__( 'Choose tool panel page', 'bw-printxtore' ),
                        'desc'        => esc_html__( 'Choose a mega page to display.', 'bw-printxtore' ),
                        'options'     => bzotech_list_post_type('bzotech_mega_item'),
                        'required'   =>  array('show_too_panel','=',true),
                    ),
                    array(
                        'id'          => 'after_append_footer',
                        'type'        => 'select',
                        'title'       => esc_html__( 'Append content after footer', 'bw-printxtore' ),
                        'desc'        => esc_html__( 'Choose a mega page content append to after main content of footer', 'bw-printxtore' ),
                        'options'     => bzotech_list_post_type('bzotech_mega_item'),
                    ),
                    array(
                        'id'          => 'body_bg',
                        'type'        => 'color_rgba',
                        'title'       => esc_html__('Body background color','bw-printxtore'),
                        'desc'        => esc_html__( 'Change the default body background color.', 'bw-printxtore' ),
                    ),
                    array(
                        'id'          => 'body_typo',
                        'type'        => 'typography',
                        'title'       => esc_html__('Body typography','bw-printxtore'),
                        'desc'        => esc_html__( 'Custom the body font.', 'bw-printxtore' ),
                    ),
                    array(
                        'id'          => 'title_typo',
                        'type'        => 'typography',
                        'title'       => esc_html__('Title typography','bw-printxtore'),
                        'desc'        => esc_html__( 'Custom font in Title.', 'bw-printxtore' ),
                        'font-weight'=>false,
                        'font-size'=>false,
                        'color'=>true,
                        'line-height'=>false,
                        'text-align'=>false,
                        'subsets'=>false,
                    ),
                    array(
                        'id'          => 'main_color',
                        'type'        => 'color_rgba',
                        'title'       => esc_html__('Main color','bw-printxtore'),
                        'desc'        => esc_html__( 'Change the website main color.', 'bw-printxtore' ),
                    ),
                    array(
                        'id'          => 'main_color2',
                        'type'        => 'color_rgba',
                        'title'       => esc_html__('Main color 2','bw-printxtore'),
                        'desc'        => esc_html__( 'Change main color 2 of your site.', 'bw-printxtore' ),
                    ),
                    array(
                        'id'          => 'bzotech_thumbnail_default',
                        'type'        => 'media',
                        'title'       => esc_html__('Thumbnail default','bw-printxtore'),
                        'desc'        => 'Select image default.',
                        'url'          => false,
                    ),
                    array(
                        'id'          => 'bzotech_page_style',
                        'type'        => 'select',
                        'title'       => esc_html__('Page Style','bw-printxtore'),
                        'default'     => '',
                        'options'     => array(
                            'page-content-df' =>  esc_html__('Default','bw-printxtore'),
                            'page-content-box' =>  esc_html__('Page boxed','bw-printxtore'),
                        ),
                        'desc'        => esc_html__( 'Select the default style for pages.', 'bw-printxtore' ),
                    ),
                    array(
                        'id'          => 'container_width',
                        'type'        => 'text',
                        'title'       => esc_html__('Custom container width (px)','bw-printxtore'),
                        'desc'        => esc_html__( 'Set width for the website container. Default is 1650px.', 'bw-printxtore' ),
                    ),
                     array(
                        'id'          => 'post_single_share',
                        'title'       => esc_html__('Show social share box','bw-printxtore'),
                        'type'        => 'checkbox',
                        'options'  => array(
                            'post' => esc_html__('Post','bw-printxtore'),
                            'page' => esc_html__('Page','bw-printxtore'),
                            'product' => esc_html__('Product','bw-printxtore'),
                        ),
                        'desc'        => esc_html__( 'Select to show social share box for post, page or product pages.', 'bw-printxtore' ),
                    ),
                    array(
                        'id'          => 'post_single_share_list',
                        'title'       => esc_html__('Custom social share box','bw-printxtore'),
                        'type'        => 'repeater',
                        'fields'    => array( 
                            array(
                                'id'       => 'title',
                                'type'     => 'text',
                                'title'    => esc_html__( 'Title', 'bw-printxtore' ),
                            ),
                            array(
                                'id'          => 'social',
                                'title'       => esc_html__('Social','bw-printxtore'),
                                'type'        => 'select',
                                'options'     => array(
                                    'total'    => esc_html__('Total share','bw-printxtore'),
                                    'facebook'  => esc_html__('Facebook','bw-printxtore'),
                                    'twitter' => esc_html__('Twitter','bw-printxtore'),
                                    'pinterest' => esc_html__('Pinterest','bw-printxtore'),
                                    'linkedin' => esc_html__('Linkedin','bw-printxtore'),
                                    'tumblr' => esc_html__('Tumblr','bw-printxtore'),
                                    'envelope' => esc_html__('Mail','bw-printxtore'),
                                ),
                                
                            ),
                            array(
                                'id'          => 'number',
                                'title'       => esc_html__('Show number','bw-printxtore'),
                                'type'        => 'switch',
                                'default'         => '0',
                            ),
                        ),
                    ),
                    
                    array(
                        'id'        => 'session_page',
                        'type'      => 'switch',
                        'title'     => esc_html__('Session option', 'bw-printxtore'),
                        'default'   => false
                    ),
                    array(
                        'id'        => 'ajax_security',
                        'type'      => 'switch',
                        'title'     => esc_html__('Ajax security', 'bw-printxtore'),
                        'default'   => true,
                        'desc'        => esc_html__( 'Check ajax referer for security. If you are using caching, enabling this function may cause ajax to not work', 'bw-printxtore' ),
                    ),
                    array(
                        'id'        => 'image_down_size',
                        'type'      => 'switch',
                        'title'     => esc_html__('Image down size', 'bw-printxtore'),
                        'default'   => true,
                        'desc'        => esc_html__( 'Enable this feature to crop images to the parameters you set everywhere. Note that after croppeing the images, you should turn it off to increase web speed', 'bw-printxtore' ),
                    ),
                )
            ) );
            // End Basic Settings
            // Layout Settings
            Redux::setSection( $bzotech_option_name, array(
                'title'            => esc_html__( 'Layout Settings', 'bw-printxtore' ),
                'id'               => 'layout',
                'icon'             => 'el el-indent-left',
                'fields'           => array(
                    array(
                        'id'          => 'bzotech_sidebar_position_page',
                        'type'        => 'select',
                        'title'       => esc_html__('Page sidebar position','bw-printxtore'),
                        'desc'        => esc_html__('Set the sidebar position for the website  pages.','bw-printxtore'),
                        'options'     => array(
                            'no'    => esc_html__('No Sidebar','bw-printxtore'),
                            'left'  => esc_html__('Left','bw-printxtore'),
                            'right' => esc_html__('Right','bw-printxtore'),
                        ),
                        'default'     => ''
                    ),
                    array(
                        'id'          => 'bzotech_sidebar_page',
                        'type'        => 'select',
                        'title'       => esc_html__('Select Sidebar','bw-printxtore'),
                        'data'        => 'sidebars',
                        'required'    => array(
                            array('bzotech_sidebar_position_page','not','no'),
                            array('bzotech_sidebar_position_page','not',''),
                        ),
                        'desc'        => esc_html__('Select the sidebar to display for the website page.','bw-printxtore'),
                        'default'     => ''
                    ),
                    array(
                        'id'          => 'bzotech_sidebar_style_page',
                        'type'        => 'select',
                        'title'       => esc_html__('Sidebar style','bw-printxtore'),
                        'desc'        => esc_html__('Select the sidebar style for the website page.','bw-printxtore'),
                        'options'     => array(
                            'default'    => esc_html__('Default','bw-printxtore'),
                            'style2'  => esc_html__('Style2','bw-printxtore'),
                        ),
                        'required'    => array(
                            array('bzotech_sidebar_position_page','not','no'),
                            array('bzotech_sidebar_position_page','not',''),
                        ), 
                        'default'     => 'default'
                    ),
                    array(
                        'id'          => 'bzotech_sidebar_position_page_archive',
                        'type'        => 'select',
                        'title'       => esc_html__('Select archives page sidebar','bw-printxtore'),
                        'desc'        => esc_html__('Select the sidebar to display for the archives page.','bw-printxtore'),
                        'options'     => array(
                            'no'    => esc_html__('No Sidebar','bw-printxtore'),
                            'left'  => esc_html__('Left','bw-printxtore'),
                            'right' => esc_html__('Right','bw-printxtore'),
                        ),
                        'default'     => 'right'
                    ),
                    array(
                        'id'          => 'bzotech_sidebar_page_archive',
                        'type'        => 'select',
                        'title'       => esc_html__('Select sidebar','bw-printxtore'),
                        'data'        => 'sidebars',
                        'required'    => array(
                            array('bzotech_sidebar_position_page_archive','not','no'),
                            array('bzotech_sidebar_position_page_archive','not',''),
                        ),
                        'desc'        => esc_html__('Select the sidebar to display for the archive page.','bw-printxtore'),
                        'default'     => 'blog-sidebar'
                    ),
                     array(
                        'id'          => 'bzotech_sidebar_style_archive',
                        'type'        => 'select',
                        'title'       => esc_html__('Sidebar style','bw-printxtore'),
                        'desc'        => esc_html__('Select the sidebar style for the archive page.','bw-printxtore'),
                        'options'     => array(
                            'default'    => esc_html__('Default','bw-printxtore'),
                            'style2'  => esc_html__('Style2','bw-printxtore'),
                        ),
                        'required'    => array(
                            array('bzotech_sidebar_position_page_archive','not','no'),
                            array('bzotech_sidebar_position_page_archive','not',''),
                        ), 
                        'default'     => 'default'
                    ),
                    array(
                        'id'          => 'bzotech_sidebar_position_page_search',
                        'type'        => 'select',
                        'title'       => esc_html__('Search page sidebar position','bw-printxtore'),
                        'desc'        => esc_html__('Set the sidebar position for the search page.','bw-printxtore'),
                        'options'     => array(
                            'no'    => esc_html__('No Sidebar','bw-printxtore'),
                            'left'  => esc_html__('Left','bw-printxtore'),
                            'right' => esc_html__('Right','bw-printxtore'),
                        )
                    ),
                    array(
                        'id'          => 'bzotech_sidebar_page_search',
                        'type'        => 'select',
                        'title'       => esc_html__('Select sidebar','bw-printxtore'),
                        'data'        => 'sidebars',
                        'required'    => array(
                            array('bzotech_sidebar_position_page_search','not','no'),
                            array('bzotech_sidebar_position_page_search','not',''),
                        ),
                        'desc'        => esc_html__('Select the sidebar to display for the search page.','bw-printxtore'),
                    ),    
                    array(
                        'id'          => 'bzotech_sidebar_style_search',
                        'type'        => 'select',
                        'title'       => esc_html__('Sidebar style','bw-printxtore'),
                        'desc'        => esc_html__('Select the sidebar style for the search page','bw-printxtore'),
                        'options'     => array(
                            'default'    => esc_html__('Default','bw-printxtore'),
                            'style2'  => esc_html__('Style2','bw-printxtore'),
                        ),
                        'required'    => array(
                            array('bzotech_sidebar_position_page_search','not','no'),
                            array('bzotech_sidebar_position_page_search','not',''),
                        ), 
                        'default'     => 'default'
                    ),          
                    array(
                        'id'          => 'bzotech_add_sidebar',
                        'title'       => esc_html__('Add SideBar','bw-printxtore'),
                        'type'        => 'repeater',
                        'default'     => '',
                        'fields'    => array( 
                            array(
                                'id'       => 'title',
                                'type'     => 'text',
                                'title'    => esc_html__( 'Title', 'bw-printxtore' ),
                            ),
                            array(
                                'id'          => 'widget_title_heading',
                                'type'        => 'select',
                                'title'       => esc_html__('Set widget heading style','bw-printxtore'),
                                'default'     => 'h3',
                                'options'     => array(
                                    'h1' => esc_html__('H1','bw-printxtore'),
                                    'h2' => esc_html__('H2','bw-printxtore'),
                                    'h3' => esc_html__('H3','bw-printxtore'),
                                    'h4' => esc_html__('H4','bw-printxtore'),
                                    'h5' => esc_html__('H5','bw-printxtore'),
                                    'h6' => esc_html__('H6','bw-printxtore'),
                                )
                            )
                            
                        ),
                    ),
                )
            ) );
            // End Layout Settings
 
         
            if(class_exists("woocommerce")){
                Redux::setSection( $bzotech_option_name, array(
                    'title'            => esc_html__( 'Shop', 'bw-printxtore' ),
                    'id'               => 'shop',
                    'icon'             => 'el el-shopping-cart'
                ) );
                Redux::setSection( $bzotech_option_name, array(
                    'title'            => esc_html__( 'General', 'bw-printxtore' ),
                    'id'               => 'general-shop',
                    'subsection'       => true,
                    'fields'           => array(
                       
                        array(
                            'id'          => 'bzotech_sidebar_position_woo',
                            'type'        => 'select',
                            'title'       => esc_html__('Sidebar position','bw-printxtore'),
                            'desc'        => esc_html__('Select the sidebar position for the WooCommerce pages (Shop, Checkout, Cart, My Account, Product category/tag/taxonomy page...). Left, Right, or No sidebar.','bw-printxtore'),
                            'options'     => array(
                                'no'    => esc_html__('No Sidebar','bw-printxtore'),
                                'left'  => esc_html__('Left','bw-printxtore'),
                                'right' => esc_html__('Right','bw-printxtore'),
                            ),
                            'default'  => 'right'
                        ),
                        array(
                            'id'          => 'bzotech_sidebar_woo',
                            'type'        => 'select',
                            'title'       => esc_html__('Select sidebar','bw-printxtore'),
                            'data'        => 'sidebars',
                            'required'    => array(
                                array('bzotech_sidebar_position_woo','not','no'),
                                array('bzotech_sidebar_position_woo','not',''),
                            ),
                            'desc'        => esc_html__('Select the sidebar to display for WooCommerce pages','bw-printxtore'),
                            'default'  => 'blog-sidebar'
                        ),
                        array(
                            'id'          => 'bzotech_sidebar_style_woo',
                            'type'        => 'select',
                            'title'       => esc_html__('Sidebar style','bw-printxtore'),
                            'desc'        => esc_html__('Select the sidebar style for the shop page','bw-printxtore'),
                            'options'     => array(
                                'default'    => esc_html__('Default','bw-printxtore'),
                                'style2'  => esc_html__('Style2','bw-printxtore'),
                            ),
                            'required'    => array(
                                array('bzotech_sidebar_position_woo','not','no'),
                                array('bzotech_sidebar_position_woo','not',''),
                            ), 
                            'default'     => 'default'
                        ),   
                        array(
                            'id'          => 'shop_default_style',
                            'type'        => 'select',
                            'title'       => esc_html__('Default style','bw-printxtore'),
                            'desc'=>esc_html__('Set the default style for the shop page: list view or grid view.','bw-printxtore'),
                            'options'     => array(                        
                                'grid'  => esc_html__('Grid','bw-printxtore'),
                                'list'  => esc_html__('List','bw-printxtore'),
                            ),
                            'default'  => 'grid'
                        ),
                        array(
                            'id'          => 'shop_gap_product',
                            'type'        => 'select',
                            'title'       => esc_html__('Gap products','bw-printxtore'),
                            'desc'=>esc_html__('Set the space between the items on the shop page.','bw-printxtore'),
                            'options'     => array(                        
                                ''          => esc_html__('Default','bw-printxtore'),
                                'gap-0'     => esc_html__('0','bw-printxtore'),
                                'gap-5'     => esc_html__('5px','bw-printxtore'),
                                'gap-10'    => esc_html__('10px','bw-printxtore'),
                                'gap-15'    => esc_html__('15px','bw-printxtore'),
                                'gap-20'    => esc_html__('20px','bw-printxtore'),
                                'gap-30'    => esc_html__('30px','bw-printxtore'),
                                'gap-40'    => esc_html__('40px','bw-printxtore'),
                                'gap-50'    => esc_html__('50px','bw-printxtore'),
                            ),
                        ),
                        array(
                            'id'          => 'woo_shop_number',
                            'type'        => 'slider',
                            'title'       => esc_html__('Product number','bw-printxtore'),
                            'min'         => 0,
                            'max'         => 999,
                            'step'        => 1,
                            'default'     => 12,
                            'desc'        => esc_html__('Set the number of product to display per page. Default is 12.','bw-printxtore')
                        ),
                        array(
                            'id'          => 'sv_set_time_woo',
                            'type'        => 'slider',
                            'title'       => esc_html__('New product','bw-printxtore'),
                            'min'         => 0,
                            'max'         => 999,
                            'step'        => 1,
                            'default'     => 0,
                            'desc'        => esc_html__('Set time for new products. Unit is day. Default is 0.','bw-printxtore')
                        ),
                        array(
                            'id'          => 'shop_style',
                            'type'        => 'select',
                            'title'       => esc_html__('Shop pagination','bw-printxtore'),
                            'desc'=>esc_html__('Select the pagination style for the shop page.','bw-printxtore'),
                            'options'     => array(
                                ''          => esc_html__('Default','bw-printxtore'),
                                'load-more' => esc_html__('Load more','bw-printxtore'),
                            )
                        ),
                        array(
                            'id'          => 'shop_ajax',
                            'type'        => 'switch',
                            'title'       => esc_html__('Shop ajax','bw-printxtore'),
                            'default'     => false,
                            'desc'        => esc_html__('Enable or disable ajax process for the shop page.','bw-printxtore'),
                            'default'     => false
                        ),
                        array(
                            'id'          => 'shop_thumb_animation',
                            'type'        => 'select',
                            'title'       => esc_html__('Thumbnail animation','bw-printxtore'),
                            'desc'        => esc_html__('Set the animation for product thumnail.','bw-printxtore'),
                            'options'     => bzotech_get_product_thumb_animation()
                        ),
                        array(
                            'id'          => 'shop_number_filter',
                            'type'        => 'switch',
                            'title'       => esc_html__('Show number filter','bw-printxtore'),
                            'desc'        => esc_html__('Show or hide number filter on shop page.','bw-printxtore'),
                            'default'     => false,
                        ),
                        array(
                            'id'          => 'shop_number_filter_list',
                            'type'        => 'repeater',
                            'title'       => esc_html__('Add number list for filter','bw-printxtore'),
                            'desc'        => esc_html__('Add the number list to filter on the shop page.','bw-printxtore'),
                            'fields'      => array(
                                array(
                                    'id'          => 'number',
                                    'type'        => 'text',
                                    'title'       => esc_html__('Number','bw-printxtore'),
                                ),
                            ),
                            'required'   => array('shop_number_filter','not',false),
                            'default'  => ''
                        ),
                        array(
                            'id'          => 'shop_type_filter',
                            'type'        => 'switch',
                            'title'       => esc_html__('Show type filter','bw-printxtore'),
                            'desc'        => esc_html__('Show or hide type filter (list/grid) on the shop page.','bw-printxtore'),
                            'default'     => false,
                        ),
                        array(
                            'id'          => 'shop_order_filter',
                            'type'        => 'switch',
                            'title'       => esc_html__('Show order filter','bw-printxtore'),
                            'desc'        => esc_html__('Show or hide order filter on the shop page.','bw-printxtore'),
                            'default'     => false,
                        ),

                        array(
                            'id'          => 'shop_attribute_color',
                            'title'       => esc_html__('Show color attribute','bw-printxtore'),
                            'desc'        => esc_html__('Show or hide color attribute on the product list.','bw-printxtore'),
                            'type'        => 'switch',
                            'section'     => 'option_woo',
                            'default'     => false,
                        ),
                        array(
                            'id'          => 'show_quick_view',
                            'title'       => esc_html__('Quick view button','bw-printxtore'),
                            'type'        => 'switch',
                            'section'     => 'option_woo',
                            'default'     => false,
                        ),
                        array(
                            'id'          => 'quick_view_style',
                            'title'       => esc_html__('Quick view style','bw-printxtore'),
                            'type'        => 'select',
                            'section'     => 'option_woo',
                            'desc'        => esc_html__('Select the style for quickview.','bw-printxtore'),
                            'default'         => '',
                            'condition'   => 'show_quick_view:is(1)',
                            'options'     => array(
                                ''          => esc_html__('Default','bw-printxtore'),
                                'load-more' => esc_html__('Style 1 (default)','bw-printxtore'),
                            )
                        ),
                    )
                ) );
                
                Redux::setSection( $bzotech_option_name, array(
                    'title'            => esc_html__( 'List View Settings', 'bw-printxtore' ),
                    'id'               => 'list-shop',
                    'subsection'       => true,
                    'fields'           => array(
                        array(
                            'id'          => 'shop_list_size',
                            'type'        => 'text',
                            'title'       => esc_html__('Custom thumbnail size','bw-printxtore'),
                            'desc'        => esc_html__('Set the thumbnail size to crop in px. Format: [width]x[height]. For example 300x300.','bw-printxtore')
                        ),
                        array(
                            'id'          => 'shop_list_item_style',
                            'type'        => 'select',
                            'title'       => esc_html__('Item style','bw-printxtore'),
                            'desc'        => esc_html__('Select the item style.','bw-printxtore'),
                            'options'     => bzotech_get_product_list_style()
                        ),
                    )
                ) );

                Redux::setSection( $bzotech_option_name, array(
                    'title'            => esc_html__( 'Grid View Settings', 'bw-printxtore' ),
                    'id'               => 'grid-shop',
                    'subsection'       => true,
                    'fields'           => array(
                        array(
                            'id'          => 'shop_grid_column',
                            'type'        => 'select',
                            'title'       => esc_html__('Grid column','bw-printxtore'),
                            'default'     => '3',
                            'desc'        => esc_html__('Select the number of column to show.','bw-printxtore'),
                            'options'     => array(
                                '2'     => esc_html__('2 column','bw-printxtore'),
                                '3'     => esc_html__('3 column','bw-printxtore'),
                                '4'     => esc_html__('4 column','bw-printxtore'),
                                '5'     => esc_html__('5 column','bw-printxtore'),
                                '6'     => esc_html__('6 column','bw-printxtore'),
                                '7'     => esc_html__('7 column','bw-printxtore'),
                                '8'     => esc_html__('8 column','bw-printxtore'),
                                '9'     => esc_html__('9 column','bw-printxtore'),
                                '10'    => esc_html__('10 column','bw-printxtore'),
                            )
                        ),
                        array(
                            'id'          => 'shop_grid_size',
                            'type'        => 'text',
                            'title'       => esc_html__('Custom thumbnail size','bw-printxtore'),
                            'desc'        => esc_html__('Set the thumbnail size to crop in px. Format: [width]x[height]. For example 300x300.','bw-printxtore')
                        ),
                        array(
                            'id'          => 'shop_grid_item_style',
                            'type'        => 'select',
                            'title'       => esc_html__('Item style','bw-printxtore'),
                            'desc'        => esc_html__('Select the item style.','bw-printxtore'),
                            'options'     => bzotech_get_product_style()
                        ),
                        array(
                            'id'       => 'item_thumbnail',
                            'type'     => 'select',
                            'title'    => esc_html__( 'Thumbnail', 'bw-printxtore' ),
                            'desc' => esc_html__( 'Show or hide the thumbnail.', 'bw-printxtore' ),
                            'default'  => '',
                            'options'     => array(
                                ''              => esc_html__('Default','bw-printxtore'),
                                'yes'  => esc_html__('Show','bw-printxtore'),
                                'no'  => esc_html__('Hide','bw-printxtore'),
                            )
                        ),
                        array(
                            'id'       => 'item_quickview',
                            'type'     => 'select',
                            'title'    => esc_html__( 'Quickview', 'bw-printxtore' ),
                            'desc' => esc_html__( 'Show or hide the quickview.', 'bw-printxtore' ),
                            'default'  => '',
                            'options'     => array(
                                ''              => esc_html__('Default','bw-printxtore'),
                                'yes'  => esc_html__('Show','bw-printxtore'),
                                'no'  => esc_html__('Hide','bw-printxtore'),
                            )
                        ),
                        array(
                            'id'       => 'item_title',
                            'type'     => 'select',
                            'title'    => esc_html__( 'Title', 'bw-printxtore' ),
                            'desc' => esc_html__( 'Show or hide the title.', 'bw-printxtore' ),
                            'default'  => '',
                            'options'     => array(
                                ''              => esc_html__('Default','bw-printxtore'),
                                'yes'  => esc_html__('Show','bw-printxtore'),
                                'no'  => esc_html__('Hide','bw-printxtore'),
                            )
                        ),
                        array(
                            'id'       => 'item_rate',
                            'type'     => 'select',
                            'title'    => esc_html__( 'Rate', 'bw-printxtore' ),
                            'desc' => esc_html__( 'Show or hide the rate.', 'bw-printxtore' ),
                            'default'  => '',
                            'options'     => array(
                                ''              => esc_html__('Default','bw-printxtore'),
                                'yes'  => esc_html__('Show','bw-printxtore'),
                                'no'  => esc_html__('Hide','bw-printxtore'),
                            )
                        ),
                        array(
                            'id'       => 'item_button',
                            'type'     => 'select',
                            'title'    => esc_html__( 'Button add to cart', 'bw-printxtore' ),
                            'desc' => esc_html__( 'Show or hide the button add to cart .', 'bw-printxtore' ),
                            'default'  => '',
                            'options'     => array(
                                ''              => esc_html__('Default','bw-printxtore'),
                                'yes'  => esc_html__('Show','bw-printxtore'),
                                'no'  => esc_html__('Hide','bw-printxtore'),
                            )
                        ),
                        array(
                            'id'       => 'item_label',
                            'type'     => 'select',
                            'title'    => esc_html__( 'Label', 'bw-printxtore' ),
                            'desc' => esc_html__( 'Show or hide the label (Label sale, label new...).', 'bw-printxtore' ),
                            'default'  => '',
                            'options'     => array(
                                ''              => esc_html__('Default','bw-printxtore'),
                                'yes'  => esc_html__('Show','bw-printxtore'),
                                'no'  => esc_html__('Hide','bw-printxtore'),
                            )
                        ),
                        array(
                            'id'       => 'item_countdown',
                            'type'     => 'select',
                            'title'    => esc_html__( 'Countdown price', 'bw-printxtore' ),
                            'desc' => esc_html__( 'Show or hide the countdown by reduced price.', 'bw-printxtore' ),
                            'default'  => '',
                            'options'     => array(
                                ''              => esc_html__('Default','bw-printxtore'),
                                'yes'  => esc_html__('Show','bw-printxtore'),
                                'no'  => esc_html__('Hide','bw-printxtore'),
                            )
                        ),
                        array(
                            'id'       => 'item_brand',
                            'type'     => 'select',
                            'title'    => esc_html__( 'Show Brand', 'bw-printxtore' ),
                            'desc' => esc_html__( 'Show or hide the Brand.', 'bw-printxtore' ),
                            'default'  => '',
                            'options'     => array(
                                ''              => esc_html__('Default','bw-printxtore'),
                                'yes'  => esc_html__('Show','bw-printxtore'),
                                'no'  => esc_html__('Hide','bw-printxtore'),
                            )
                        ),
                        array(
                            'id'       => 'item_flash_sale',
                            'type'     => 'select',
                            'title'    => esc_html__( 'Flash Sale', 'bw-printxtore' ),
                            'desc' => esc_html__( 'Show or hide the Flash Sale bar.', 'bw-printxtore' ),
                            'default'  => '',
                            'options'     => array(
                                ''              => esc_html__('Default','bw-printxtore'),
                                'yes'  => esc_html__('Show','bw-printxtore'),
                                'no'  => esc_html__('Hide','bw-printxtore'),
                            )
                        ),
                        array(
                            'id'       => 'item_attribute',
                            'type'     => 'select',
                            'title'    => esc_html__( 'Attribute (For products of type Data variable)', 'bw-printxtore' ),
                            'desc' => esc_html__( 'Show or hide the Attribute.', 'bw-printxtore' ),
                            'default'  => '',
                            'options'     => array(
                                ''              => esc_html__('Default','bw-printxtore'),
                                'yes'  => esc_html__('Show','bw-printxtore'),
                                'no'  => esc_html__('Hide','bw-printxtore'),
                            )
                        ),
                        array(
                            'id'          => 'shop_grid_type',
                            'type'        => 'select',
                            'title'       => esc_html__('Grid display','bw-printxtore'),
                            'desc'        => esc_html__('Select the grid style.','bw-printxtore'),
                            'options'     => array(
                                ''              => esc_html__('Default','bw-printxtore'),
                                'grid-masonry'  => esc_html__('Masonry','bw-printxtore'),
                            )
                        ),
                    )
                ) );

                Redux::setSection( $bzotech_option_name, array(
                    'title'            => esc_html__( 'Advanced', 'bw-printxtore' ),
                    'id'               => 'advanced-shop',
                    'subsection'       => true,
                    'fields'           => array(
                        array(
                            'id'          => 'cart_page_style',
                            'type'        => 'select',
                            'title'       => esc_html__('Cart display','bw-printxtore'),
                            'desc'        => esc_html__('Select the cart style.','bw-printxtore'),
                            'options'     => array(
                                ''          => esc_html__('Default','bw-printxtore'),
                                'style2'    => esc_html__('Style 2','bw-printxtore'),
                            )
                        ),
                        array(
                            'id'          => 'checkout_page_style',
                            'type'        => 'select',
                            'title'       => esc_html__('Checkout display','bw-printxtore'),
                            'desc'        => esc_html__('Select the checkout style.','bw-printxtore'),
                            'options'     => array(
                                ''          => esc_html__('Default','bw-printxtore'),
                                'style2'    => esc_html__('Style 2','bw-printxtore'),
                            )
                        ),
                        array(
                            'id'          => 'bzotech_header_page_woo',
                            'type'        => 'select',
                            'title'       => esc_html__( 'WooCommerce page header', 'bw-printxtore' ),
                            'desc'        => esc_html__( 'Select the header style. To edit/create header content, go to Header Page in Admin Dashboard.
Note: this setting is applied for all pages; for single page setting, please go to each page to config.', 'bw-printxtore' ),
                            'options'     => bzotech_list_post_type('bzotech_header')
                        ),
                        array(
                            'id'          => 'bzotech_footer_page_woo',
                            'type'        => 'select',
                            'title'       => esc_html__( 'WooCommerce page footer', 'bw-printxtore' ),
                            'desc'        => esc_html__( 'Select the footer style. To edit/create footer content, go to Footer Page in Admin Dashboard.
Note: this setting is applied for all pages; for single page setting, please go to each page to config.', 'bw-printxtore' ),
                            'options'     => bzotech_list_post_type('bzotech_footer')
                        ),
                        array(
                            'id'          => 'before_append_woo',
                            'type'        => 'select',
                            'title'       => esc_html__('Append content before WooCommerce page','bw-printxtore'),
                            'options'     => bzotech_list_post_type('bzotech_mega_item'),
                            'desc'        => esc_html__('Choose a mega page content append to before main content of page/post.','bw-printxtore'),
                        ),
                        array(
                            'id'          => 'after_append_woo',
                            'type'        => 'select',
                            'title'       => esc_html__('Append content after WooCommerce page','bw-printxtore'),
                            'options'     => bzotech_list_post_type('bzotech_mega_item'),
                            'desc'        => esc_html__('Choose a mega page content append to after main content of page/post.','bw-printxtore'),
                        ),
                        array(
                            'id'          => 'brand_woo',
                            'type'        => 'switch',
                            'title'       => esc_html__('Shop brand','bw-printxtore'),
                            'desc'        => esc_html__('Enable or disable brand function.','bw-printxtore'),
                            'default'     => 'false'
                        ),
                    )
                ) );
                // End Shop

                // Product
                Redux::setSection( $bzotech_option_name, array(
                    'title'            => esc_html__( 'Product', 'bw-printxtore' ),
                    'id'               => 'product',
                    'icon'             => 'el el-briefcase'
                ) );
                Redux::setSection( $bzotech_option_name, array(
                    'title'            => esc_html__( 'General', 'bw-printxtore' ),
                    'id'               => 'general-product',
                    'subsection'       => true,
                    'fields'           => array(
                        array(
                        'id'          => 'sv_style_woo_single',
                        'title'       => esc_html__('Product detail style','bw-printxtore'),
                        'type'        => 'select',
                        'section'     => 'option_product',
                        'default'         => 'style-gallery-horizontal',
                        'desc'        => esc_html__('Select style for the product detail.','bw-printxtore'),
                        'options'     => array(
                                'style-gallery-horizontal'=> esc_html__('Style 1 (Gallery horizontal )','bw-printxtore'),
                                'style-gallery-vertical' => esc_html__('Style 2 (Gallery vertical)','bw-printxtore'),
                                'sticky-style1' => esc_html__('Style 3 (Gallery sticky)','bw-printxtore'),
                                'sticky-style2' => esc_html__('Style 4 (Gallery sticky)','bw-printxtore'),
                                'sticky-style3' => esc_html__('Style 5 (Gallery sticky)','bw-printxtore'),
                                'style-gallery-horizontal2' => esc_html__('Style 6 (Gallery horizontal 2)','bw-printxtore'),
                                'style-gallery-vertical2' => esc_html__('Style 7 (Gallery vertical 2)','bw-printxtore'),
                                'style-default' => esc_html__('Default (WooCommerce default)','bw-printxtore'),
                            )
                        ),
                        array(
                        'id'          => 'show_hide_image_gallery_woo',
                        'title'       => esc_html__('Show/Hide product image and gallery product','bw-printxtore'),
                        'type'        => 'select',
                        'section'     => 'option_product',
                        'default'         => 'show',
                        'options'     => array(
                                'show'=> esc_html__('Show','bw-printxtore'),
                                'hide' => esc_html__('Hide','bw-printxtore'),
                            )
                        ),
                        array(
                            'id'          => 'sv_sidebar_position_woo_single',
                            'type'        => 'select',
                            'title'       => esc_html__('Sidebar position','bw-printxtore'),
                            'desc'        => esc_html__('Select the sidebar position for the single product page.','bw-printxtore'),
                            'default'         => 'no',
                            'options'     => array(
                                'no'    => esc_html__('No Sidebar','bw-printxtore'),
                                'left'  => esc_html__('Left','bw-printxtore'),
                                'right' => esc_html__('Right','bw-printxtore'),
                            ),
                        ),
                        array(
                            'id'          => 'sv_sidebar_woo_single',
                            'type'        => 'select',
                            'title'       => esc_html__('Select sidebar','bw-printxtore'),
                            'data'        => 'sidebars',
                            'required'    => array(
                                array('sv_sidebar_position_woo_single','not','no'),
                                array('sv_sidebar_position_woo_single','not',''),
                            ),
                            'desc'        => esc_html__('Select the sidebar for single product page.','bw-printxtore'),
                        ),
                        array(
                            'id'          => 'bzotech_sidebar_style_woo_single',
                            'type'        => 'select',
                            'title'       => esc_html__('Sidebar style','bw-printxtore'),
                            'desc'        => esc_html__('Select the sidebar style for the single product page.','bw-printxtore'),
                            'options'     => array(
                                'default'    => esc_html__('Default','bw-printxtore'),
                                'style2'  => esc_html__('Style2','bw-printxtore'),
                            ),
                            'required'    => array(
                                array('sv_sidebar_position_woo_single','not','no'),
                                array('sv_sidebar_position_woo_single','not',''),
                            ), 
                            'default'     => 'default'
                        ),  
                        array(
                            'id'          => 'product_image_zoom',
                            'type'        => 'select',
                            'title'       => esc_html__('Image zoom','bw-printxtore'),
                            'desc'        => esc_html__('Select the image zoom style.','bw-printxtore'),
                            'options'     => array(
                                ''              => esc_html__('None','bw-printxtore'),
                                'zoom-style1'   => esc_html__('Zoom 1','bw-printxtore'),
                                'zoom-style2'   => esc_html__('Zoom 2','bw-printxtore'),
                                'zoom-style3'   => esc_html__('Zoom 3','bw-printxtore'),
                                'zoom-style4'   => esc_html__('Zoom 4','bw-printxtore'),
                            )
                        ),
                        array(
                            'id'          => 'product_tab_detail',
                            'type'        => 'select',
                            'title'       => esc_html__('Product tab style','bw-printxtore'),
                            'desc'        => esc_html__('Select the product tab style.','bw-printxtore'),
                            'default'         => 'tab-product-accordion',
                            'options'     => array(
                                'tab-product-horizontal'=> esc_html__("Tab style horizontal", 'bw-printxtore'),
                                'tab-product-vertical'=> esc_html__("Tab style vertical", 'bw-printxtore'),
                                'tab-product-accordion'=> esc_html__("Tab style accordion", 'bw-printxtore'),
                            )
                        ),
                        array(
                            'id'          => 'show_excerpt',
                            'type'        => 'switch',
                            'title'       => esc_html__('Show excerpt','bw-printxtore'),
                            'default'     => true
                        ),
                    )
                ) );

                Redux::setSection( $bzotech_option_name, array(
                    'title'            => esc_html__( 'Extra Display', 'bw-printxtore' ),
                    'id'               => 'display-product',
                    'subsection'       => true,
                    'fields'           => array(
                       array(
                            'id'          => 'share_whatsapp',
                            'type'        => 'switch',
                            'title'       => esc_html__('Button share on WhatsApp','bw-printxtore'),
                            'default'     => false
                        ),
                       array(
                            'id'          => 'show_latest',
                            'type'        => 'switch',
                            'title'       => esc_html__('Show latest products','bw-printxtore'),
                            'default'     => false
                        ),
                        array(
                            'id'          => 'show_upsell',
                            'type'        => 'switch',
                            'title'       => esc_html__('Show upsell products','bw-printxtore'),
                            'default'     => false
                        ),
                        array(
                            'id'          => 'show_related',
                            'type'        => 'switch',
                            'title'       => esc_html__('Show related products','bw-printxtore'),
                            'section'     => 'option_product',
                            'default'     => false
                        ),
                        array(
                            'id'          => 'bzotech_product_sticky_addcart',
                            'type'        => 'switch',
                            'title'       => esc_html__('Sticky add to cart','bw-printxtore'),
                            'section'     => 'option_product',
                            'default'     => false
                        ),
                        array(
                            'id'          => 'show_single_number',
                            'type'        => 'slider',
                            'title'       => esc_html__('Show single number','bw-printxtore'),
                            'min'         => '1',
                            'max'         => '100',
                            'step'        => '1',
                            'default'     => '6'
                        ),
                        array(
                            'id'          => 'show_single_size',
                            'type'        => 'text',
                            'title'       => esc_html__('Show single size','bw-printxtore'),
                            'desc'        => esc_html__('Set the size for related, upsell products thumbnail. Format: [width]x[height]. For example 300x300.','bw-printxtore'),
                        ),
                        array(
                            'id'          => 'show_single_itemres',
                            'type'        => 'text',
                            'title'       => esc_html__('Custom item devices','bw-printxtore'),
                            'desc'        => esc_html__('Set the number of related, upsell products for different screen size in px. Format is width:value and separate by ",". Example is 0:2,600:3,1000:4. Default is auto.','bw-printxtore'),
                        ),
                        array(
                            'id'          => 'show_single_item_style',
                            'type'        => 'select',
                            'title'       => esc_html__('Single item style','bw-printxtore'),
                            'desc'        => esc_html__('Select the item style.','bw-printxtore'),
                            'options'     => bzotech_get_product_style()
                        ),
                    )
                ) );

                Redux::setSection( $bzotech_option_name, array(
                    'title'            => esc_html__( 'Advanced', 'bw-printxtore' ),
                    'id'               => 'advanced-product',
                    'subsection'       => true,
                    'fields'           => array(
                       array(
                            'id'          => 'before_append_woo_single',
                            'type'        => 'select',
                            'title'       => esc_html__('Append content before product page','bw-printxtore'),
                            'options'     => bzotech_list_post_type('bzotech_mega_item'),
                            'desc'        => esc_html__('Choose a mega page content append to before main content of page/post.','bw-printxtore'),
                        ),
                        array(
                            'id'          => 'before_append_tab',
                            'type'        => 'select',
                            'title'       => esc_html__('Append content before product tab','bw-printxtore'),
                            'options'     => bzotech_list_post_type('bzotech_mega_item'),
                            'desc'        => esc_html__('Choose a mega page content append to before product tab.','bw-printxtore'),
                        ),
                        array(
                            'id'          => 'after_append_tab',
                            'type'        => 'select',
                            'title'       => esc_html__('Append content after product tab','bw-printxtore'),
                            'options'     => bzotech_list_post_type('bzotech_mega_item'),
                            'desc'        => esc_html__('Choose a mega page content append to before product tab.','bw-printxtore'),
                        ),
                        array(
                            'id'          => 'after_append_woo_single',
                            'type'        => 'select',
                            'title'       => esc_html__('Append content after product page','bw-printxtore'),
                            'options'     => bzotech_list_post_type('bzotech_mega_item'),
                            'desc'        => esc_html__('Choose a mega page content append to after main content of page/post.','bw-printxtore'),
                        ),
                        array(
                            'id'          => 'append_content_summary',
                            'type'        => 'select',
                            'title'       => esc_html__('Append content in summary','bw-printxtore'),
                            'options'     => bzotech_list_post_type('bzotech_mega_item'),
                            'desc'        => esc_html__('Choose a "mega page" content append to after main content of product summary.','bw-printxtore'),
                        ),
                        array(
                            'id'          => 'append_content_after_gallery',
                            'type'        => 'select',
                            'title'       => esc_html__('Append content after gallery image','bw-printxtore'),
                            'options'     => bzotech_list_post_type('bzotech_mega_item'),
                            'desc'        => esc_html__('Choose a "mega page" content append to after main content of product gallery image.','bw-printxtore'),
                        ),

                        array(
                            'id'          => 'content_summary_pos',
                            'type'        => 'slider',
                            'desc'        => esc_html__('Set a position for the content to be added to the product summary','bw-printxtore'),
                            'title'       => esc_html__('Set a position for the content to be added','bw-printxtore'),
                            'min'         => '1',
                            'max'         => '100',
                            'step'        => '1',
                            'default'     => '60',
                            'required'    => array(
                                array('append_content_summary','not','')
                            ), 
                        ),
                    )
                ) );
                // End Product
            };
            // Blog & Post
            Redux::setSection( $bzotech_option_name, array(
                'title'            => esc_html__( 'Blog & Post', 'bw-printxtore' ),
                'id'               => 'blog-post',
                'icon'             => 'el el-website'
            ) );
            Redux::setSection( $bzotech_option_name, array(
                'title'            => esc_html__( 'General', 'bw-printxtore' ),
                'id'               => 'blog-general',
                'subsection'       => true,
                'fields'           => array(
                                 
                    array(
                        'id'          => 'before_append_post',
                        'type'        => 'select',
                        'title'       => esc_html__('Append content before post/blog/archive page','bw-printxtore'),
                        'options'     => bzotech_list_post_type('bzotech_mega_item'),
                        'desc'        => esc_html__('Choose a mega page content append to before main content of post/blog/archive page.','bw-printxtore'),
                    ),
                    array(
                        'id'          => 'after_append_post',
                        'type'        => 'select',
                        'title'       => esc_html__('Append content after post/blog/archive page','bw-printxtore'),
                        'options'     => bzotech_list_post_type('bzotech_mega_item'),
                        'desc'        => esc_html__('Choose a mega page content append to after main content of post/blog/archive page.','bw-printxtore'),
                    ),
                    array(
                        'id'          => 'bzotech_sidebar_position_blog',
                        'type'        => 'select',
                        'title'       => esc_html__('Sidebar position','bw-printxtore'),
                        'desc'        => esc_html__('Select the sidebar position for the blog page.','bw-printxtore'),
                        'options'     => array(
                            'no'    => esc_html__('No Sidebar','bw-printxtore'),
                            'left'  => esc_html__('Left','bw-printxtore'),
                            'right' => esc_html__('Right','bw-printxtore'),
                        ),
                        'default'     => 'right'
                    ),
                    array(
                        'id'          => 'bzotech_sidebar_blog',
                        'type'        => 'select',
                        'title'       => esc_html__('Select sidebar','bw-printxtore'),
                        'data'        => 'sidebars',
                        'required'    => array(
                            array('bzotech_sidebar_position_blog','not','no'),
                            array('bzotech_sidebar_position_blog','not',''),
                        ), 
                        'desc'        => esc_html__('Select the sidebar to display for the blog pages.','bw-printxtore'),
                    ),
                     array(
                        'id'          => 'bzotech_sidebar_style_blog',
                        'type'        => 'select',
                        'title'       => esc_html__('Sidebar style','bw-printxtore'),
                        'desc'        => esc_html__('Select the sidebar style for the blog page.','bw-printxtore'),
                        'options'     => array(
                            'default'    => esc_html__('Default','bw-printxtore'),
                            'style2'  => esc_html__('Style2','bw-printxtore'),
                        ),
                        'required'    => array(
                            array('bzotech_sidebar_position_blog','not','no'),
                            array('bzotech_sidebar_position_blog','not',''),
                        ), 
                        'default'     => 'default'
                    ),
                    array(
                        'id'          => 'blog_default_style',
                        'type'        => 'select',
                        'title'       => esc_html__('Default blog view style','bw-printxtore'),
                        'desc'        =>esc_html__('Select the default blog view style: list view or grid view.','bw-printxtore'),
                        'options'     => array(
                            'list'  => esc_html__('List','bw-printxtore'),
                            'grid'  => esc_html__('Grid','bw-printxtore'),
                        ),
                        'default'     => 'list',
                    ),
                    array(
                        'id'          => 'blog_style',
                        'type'        => 'select',
                        'title'       => esc_html__('Pagination','bw-printxtore'),
                        'desc'        => esc_html__('Select the blog pagination style.','bw-printxtore'),
                        'options'     => array(
                            ''          => esc_html__('Default','bw-printxtore'),
                            'load-more' =>esc_html__('Load more','bw-printxtore'),
                        )
                    ),
                    array(
                        'id'          => 'blog_number_filter',
                        'type'        => 'switch',
                        'title'       => esc_html__('Show number filter','bw-printxtore'),
                        'desc'        => esc_html__('Show/hide number filter on blog page.','bw-printxtore'),
                        'default'     => false,
                    ),
                    array(
                        'id'          => 'blog_number_filter_list',
                        'title'       => esc_html__('Add list number filter','bw-printxtore'),
                        'type'        => 'repeater',
                        'desc'        => esc_html__('Add custom list number to filter on the blog page.','bw-printxtore'),
                        'fields'    => array( 
                            array(
                                'id'       => 'title',
                                'type'     => 'text',
                                'title'    => esc_html__( 'Title', 'bw-printxtore' ),
                            ),
                            array(
                                'id'          => 'number',
                                'type'        => 'text',
                                'title'       => esc_html__('Number','bw-printxtore'),
                            ),
                        ),
                        'required'   => array('blog_number_filter','not', false),
                    ),
                    array(
                        'id'          => 'blog_type_filter',
                        'type'        => 'switch',
                        'title'       => esc_html__('Show type filter','bw-printxtore'),
                        'desc'        => esc_html__('Show or hide type filter (list/grid) on blog page.','bw-printxtore'),
                        'default'     => false,
                    ),
                    array(
                        'id'          => 'blog_order_filter',
                        'type'        => 'switch',
                        'title'       => esc_html__('Show order filter','bw-printxtore'),
                        'desc'        => esc_html__('Show or hide order filter on blog page.','bw-printxtore'),
                        'default'     => false,
                    ),
                    

                )
            ) );
            Redux::setSection( $bzotech_option_name, array(
                'title'            => esc_html__( 'List View Settings', 'bw-printxtore' ),
                'id'               => 'blog-list',
                'subsection'       => true,
                'fields'           => array(
                    
                   
                    array(
                        'id'          => 'post_list_item_style',
                        'type'        => 'select',
                        'title'       => esc_html__('Item style','bw-printxtore'),
                        'desc'        => esc_html__('Select the item style (Default: Style 1).','bw-printxtore'),
                        'options'     => bzotech_get_post_list_style()
                    ),



                    array(
                        'id'       => 'item_thumbnail_post_list',
                        'type'     => 'select',
                        'title'    => esc_html__( 'Thumbnail', 'bw-printxtore' ),
                        'desc' => esc_html__( 'Show or hide the thumbnail. (Default: by Item style)', 'bw-printxtore' ),
                        'default'  => '',
                        'options'     => array(
                            ''              => esc_html__('Default','bw-printxtore'),
                            'yes'  => esc_html__('Show','bw-printxtore'),
                            'no'  => esc_html__('Hide','bw-printxtore'),
                        )
                    ),
                    array(
                        'id'          => 'post_list_size',
                        'type'        => 'text',
                        'title'       => esc_html__('Custom list thumbnail size','bw-printxtore'),
                        'desc'        => esc_html__('Set the thumbnail size to crop in px. Format: [width]x[height]. For example 300x300.','bw-printxtore'),
                        'required'    => array('item_thumbnail_post_list','!=','no'),
                    ),
                    array(
                        'id'       => 'item_title_post_list',
                        'type'     => 'select',
                        'title'    => esc_html__( 'Title', 'bw-printxtore' ),
                        'desc' => esc_html__( 'Show or hide the title. (Default: by Item style)', 'bw-printxtore' ),
                        'default'  => '',
                        'options'     => array(
                            ''              => esc_html__('Default','bw-printxtore'),
                            'yes'  => esc_html__('Show','bw-printxtore'),
                            'no'  => esc_html__('Hide','bw-printxtore'),
                        )
                    ),
                    array(
                        'id'          => 'item_meta_post_list',
                        'type'        => 'select',
                        'options'     => array(
                           ''     => esc_html__( 'Default', 'bw-printxtore' ),
                            'yes'      => esc_html__( 'yes', 'bw-printxtore' ),
                            'no'      => esc_html__( 'No', 'bw-printxtore' ),
                        ),
                        'title'       => esc_html__('Show meta data','bw-printxtore'),
                        'desc'        => esc_html__('Add meta post. (Default: by Item style)','bw-printxtore'),
                        'default'     => '',
                    ),
                    array(
                        'id'          => 'item_meta_select_post_list',
                        'type'        => 'select',
                        'multi'=>  true,
                        'title'       => esc_html__('Meta list','bw-printxtore'),
                        'options'     => array(
                           'author'     => esc_html__( 'Author', 'bw-printxtore' ),
                            'date'      => esc_html__( 'Date', 'bw-printxtore' ),
                            'cats'      => esc_html__( 'Categories', 'bw-printxtore' ),
                            'tags'      => esc_html__( 'Tags', 'bw-printxtore' ),
                            'comments'  => esc_html__( 'Comments', 'bw-printxtore' ),
                            'views'     => esc_html__( 'Total views', 'bw-printxtore' ),
                        ),
                        'desc'        => esc_html__('Show or hide meta data (author, date, comments, categories, tags) on blog page.','bw-printxtore'),
                        'required'    => array('item_meta_post_list','!=','no'),
                    ),
                    
                    array(
                        'id'       => 'item_excerpt_post_list',
                        'type'     => 'select',
                        'title'    => esc_html__( 'Excerpt', 'bw-printxtore' ),
                        'desc' => esc_html__( 'Show or hide the excerpt. (Default: by Item style)', 'bw-printxtore' ),
                        'default'  => '',
                        'options'     => array(
                            ''              => esc_html__('Default','bw-printxtore'),
                            'yes'  => esc_html__('Show','bw-printxtore'),
                            'no'  => esc_html__('Hide','bw-printxtore'),
                        )
                    ),
                    array(
                        'id'          => 'post_list_excerpt',
                        'type'        => 'slider',
                        'title'       => esc_html__('Substring excerpt','bw-printxtore'),
                        'min'         => 0,
                        'max'         => 999,
                        'step'        => 1,
                        'default'     => 999,
                        'desc'        => esc_html__('Set the number of character to get from excerpt content. Default is 999. Note: This value only applies on items that supports to show excerpt.','bw-printxtore'),
                        'required'    => array('item_excerpt_post_list','!=','no'),
                    ),
                    array(
                        'id'       => 'item_button_post_list',
                        'type'     => 'select',
                        'title'    => esc_html__( 'Button', 'bw-printxtore' ),
                        'desc' => esc_html__( 'Show or hide the button. (Default: by Item style)', 'bw-printxtore' ),
                        'default'  => '',
                        'options'     => array(
                            ''              => esc_html__('Default','bw-printxtore'),
                            'yes'  => esc_html__('Show','bw-printxtore'),
                            'no'  => esc_html__('Hide','bw-printxtore'),
                        )
                    ),
                )
            ) );
            Redux::setSection( $bzotech_option_name, array(
                'title'            => esc_html__( 'Grid View Settings', 'bw-printxtore' ),
                'id'               => 'blog-grid',
                'subsection'       => true,
                'fields'           => array(
                    array(
                        'id'          => 'post_grid_column',
                        'type'        => 'select',
                        'title'       => esc_html__('Grid column','bw-printxtore'),
                        'default'     => '3',
                        'desc'=>esc_html__('Choose a style to active display','bw-printxtore'),
                        'options'     => array(
                            '2' => esc_html__('2 column','bw-printxtore'),
                            '3' =>esc_html__('3 column','bw-printxtore'),
                            '4' =>esc_html__('4 column','bw-printxtore'),
                            '5' =>esc_html__('5 column','bw-printxtore'),
                            '6' =>esc_html__('6 column','bw-printxtore'),
                        )
                    ),
                   
                    
                    array(
                        'id'          => 'post_grid_item_style',
                        'type'        => 'select',
                        'title'       => esc_html__('Item style','bw-printxtore'),
                        'desc'        =>esc_html__('Select the item style.','bw-printxtore'),
                        'options'     => bzotech_get_post_style()
                    ),
                    array(
                        'id'          => 'post_grid_type',
                        'type'        => 'select',
                        'title'       => esc_html__('Display style','bw-printxtore'),
                        'desc'        =>esc_html__('Select the grid display style.','bw-printxtore'),
                        'options'     => array(
                            ''  => esc_html__('Default','bw-printxtore'),
                            'grid-masonry'  => esc_html__('Masonry','bw-printxtore'),
                            )
                    ),
                    array(
                        'id'       => 'item_thumbnail_post',
                        'type'     => 'select',
                        'title'    => esc_html__( 'Thumbnail', 'bw-printxtore' ),
                        'desc' => esc_html__( 'Show or hide the thumbnail. (Default: by Item style)', 'bw-printxtore' ),
                        'default'  => '',
                        'options'     => array(
                            ''              => esc_html__('Default','bw-printxtore'),
                            'yes'  => esc_html__('Show','bw-printxtore'),
                            'no'  => esc_html__('Hide','bw-printxtore'),
                        )
                    ),
                    array(
                        'id'          => 'post_grid_size',
                        'type'        => 'text',
                        'title'       => esc_html__('Custom thumbnail size','bw-printxtore'),
                        'desc'        => esc_html__('Set the thumbnail size to crop in px. Format: [width]x[height]. For example 300x300.','bw-printxtore'),
                        'required'    => array('item_thumbnail_post','!=','no'),
                    ),
                    array(
                        'id'       => 'item_title_post',
                        'type'     => 'select',
                        'title'    => esc_html__( 'Title', 'bw-printxtore' ),
                        'desc' => esc_html__( 'Show or hide the title. (Default: by Item style)', 'bw-printxtore' ),
                        'default'  => '',
                        'options'     => array(
                            ''              => esc_html__('Default','bw-printxtore'),
                            'yes'  => esc_html__('Show','bw-printxtore'),
                            'no'  => esc_html__('Hide','bw-printxtore'),
                        )
                    ),
                    array(
                        'id'          => 'item_meta_post',
                        'type'        => 'select',
                        'options'     => array(
                           ''     => esc_html__( 'Default', 'bw-printxtore' ),
                            'yes'      => esc_html__( 'yes', 'bw-printxtore' ),
                            'no'      => esc_html__( 'No', 'bw-printxtore' ),
                        ),
                        'title'       => esc_html__('Show meta data','bw-printxtore'),
                        'desc'        => esc_html__('Add meta post. (Default: by Item style)','bw-printxtore'),
                        'default'     => '',
                    ),
                    array(
                        'id'          => 'item_meta_select_post',
                        'type'        => 'select',
                        'multi'=>  true,
                        'title'       => esc_html__('Meta list','bw-printxtore'),
                        'options'     => array(
                           'author'     => esc_html__( 'Author', 'bw-printxtore' ),
                            'date'      => esc_html__( 'Date', 'bw-printxtore' ),
                            'cats'      => esc_html__( 'Categories', 'bw-printxtore' ),
                            'tags'      => esc_html__( 'Tags', 'bw-printxtore' ),
                            'comments'  => esc_html__( 'Comments', 'bw-printxtore' ),
                            'views'     => esc_html__( 'Total views', 'bw-printxtore' ),
                        ),
                        'desc'        => esc_html__('Show or hide meta data (author, date, comments, categories, tags) on blog page.','bw-printxtore'),
                        'required'    => array('item_meta_post','!=','no'),
                    ),
                    
                    array(
                        'id'       => 'item_excerpt_post',
                        'type'     => 'select',
                        'title'    => esc_html__( 'Excerpt', 'bw-printxtore' ),
                        'desc' => esc_html__( 'Show or hide the excerpt. (Default: by Item style)', 'bw-printxtore' ),
                        'default'  => '',
                        'options'     => array(
                            ''              => esc_html__('Default','bw-printxtore'),
                            'yes'  => esc_html__('Show','bw-printxtore'),
                            'no'  => esc_html__('Hide','bw-printxtore'),
                        )
                    ),
                    array(
                        'id'          => 'post_grid_excerpt',
                        'type'        => 'slider',
                        'title'       => esc_html__('Substring excerpt','bw-printxtore'),
                        'min'         => 0,
                        'max'         => 999,
                        'step'        => 1,
                        'default'     => 999,
                        'desc'        => esc_html__('Set the number of character to get from excerpt content. Default is 999. Note: This value only applies on items that supports to show excerpt.','bw-printxtore')
                    ),
                    array(
                        'id'       => 'item_button_post',
                        'type'     => 'select',
                        'title'    => esc_html__( 'Button', 'bw-printxtore' ),
                        'desc' => esc_html__( 'Show or hide the button. (Default: by Item style)', 'bw-printxtore' ),
                        'default'  => '',
                        'options'     => array(
                            ''              => esc_html__('Default','bw-printxtore'),
                            'yes'  => esc_html__('Show','bw-printxtore'),
                            'no'  => esc_html__('Hide','bw-printxtore'),
                        )
                    ),
                )
            ) );
            Redux::setSection( $bzotech_option_name, array(
                'title'            => esc_html__( 'Post Detail Settings', 'bw-printxtore' ),
                'id'               => 'blog-post-detail',
                'subsection'       => true,
                'fields'           => array(                    
                    array(
                        'id'          => 'bzotech_style_post_detail',
                        'type'        => 'select',
                        'title'       => esc_html__('Single post style','bw-printxtore'),
                        'options'     => array(
                            'style1'    => esc_html__('Default','bw-printxtore'),
                            'style2'    => esc_html__('Style 2','bw-printxtore'),
                        ),
                        'default'  => 'style1'
                    ),
                    array(
                        'id'          => 'bzotech_sidebar_position_post',
                        'type'        => 'select',
                        'title'       => esc_html__('Sidebar position','bw-printxtore'),
                        'desc'        => esc_html__('Select the sidebar position for the single post page.','bw-printxtore'),
                        'options'     => array(
                            'no'    => esc_html__('No Sidebar','bw-printxtore'),
                            'left'  => esc_html__('Left','bw-printxtore'),
                            'right' => esc_html__('Right','bw-printxtore'),
                        ),
                        'default'  => 'right'
                    ),
                    array(
                        'id'          => 'bzotech_sidebar_post',
                        'type'        => 'select',
                        'title'       => esc_html__('Select sidebar','bw-printxtore'),
                        'data'        => 'sidebars',
                        'required'    => array(
                            array('bzotech_sidebar_position_post','not','no'),
                            array('bzotech_sidebar_position_post','not',''),
                        ),                   
                        'desc'        => esc_html__('Select the sidebar to display for the single post page.','bw-printxtore'),
                        'default'     => 'blog-sidebar'
                    ),

                    array(
                        'id'          => 'bzotech_sidebar_style_post',
                        'type'        => 'select',
                        'title'       => esc_html__('Sidebar style','bw-printxtore'),
                        'desc'        => esc_html__('Select the sidebar style for the post page.','bw-printxtore'),
                        'options'     => array(
                            'default'    => esc_html__('Default','bw-printxtore'),
                            'style2'  => esc_html__('Style2','bw-printxtore'),
                        ),
                        'required'    => array(
                            array('bzotech_sidebar_position_post','not','no'),
                            array('bzotech_sidebar_position_post','not',''),
                        ), 
                        'default'     => 'default'
                    ),
                    array(
                        'id'          => 'before_append_post_detail',
                        'title'       => esc_html__('Append content before post detail','bw-printxtore'),
                        'type'        => 'select',
                        'options'     => bzotech_list_post_type('bzotech_mega_item'),
                        'desc'        => esc_html__('Choose a mega page content append to before main content of post detail.','bw-printxtore'),
                    ),
                    array(
                        'id'          => 'after_append_post_detail',
                        'title'       => esc_html__('Append content after post detail','bw-printxtore'),
                        'type'        => 'select',
                        'options'     => bzotech_list_post_type('bzotech_mega_item'),
                        'desc'        => esc_html__('Choose a mega page content append to after main content of post detail.','bw-printxtore'),
                    ),
                    array(
                        'id'          => 'post_single_thumbnail',
                        'type'        => 'switch',
                        'title'       => esc_html__('Show thumbnail/media','bw-printxtore'),
                        'desc'        => 'Show/hide thumbnail image, gallery, media on post detail.',
                        'default'     => true,
                    ),                
                    array(
                        'id'          => 'post_single_size',
                        'title'       => esc_html__('Custom single image size','bw-printxtore'),
                        'type'        => 'text',
                        'desc'        => esc_html__('Enter size thumbnail to crop. [width]x[height]. Example is 300x300.','bw-printxtore'),
                        'required'    => array('post_single_thumbnail','=',true),
                    ),
                    array(
                        'id'          => 'post_single_meta',
                        'type'        => 'select',
                        'options'     => array(
                           ''     => esc_html__( 'Default', 'bw-printxtore' ),
                            'yes'      => esc_html__( 'yes', 'bw-printxtore' ),
                            'no'      => esc_html__( 'No', 'bw-printxtore' ),
                        ),
                        'title'       => esc_html__('Show meta data','bw-printxtore'),
                        'desc'        => esc_html__('Show or hide meta data (author, date, comments, categories, tags) on post detail.','bw-printxtore'),
                        'default'     => '',

                    ),
                     array(
                        'id'          => 'single_item_meta_select',
                        'type'        => 'select',
                        'multi'=>  true,
                        'title'       => esc_html__('Meta list','bw-printxtore'),
                        'options'     => array(
                           'author'     => esc_html__( 'Author', 'bw-printxtore' ),
                            'date'      => esc_html__( 'Date', 'bw-printxtore' ),
                            'cats'      => esc_html__( 'Categories', 'bw-printxtore' ),
                            'tags'      => esc_html__( 'Tags', 'bw-printxtore' ),
                            'comments'  => esc_html__( 'Comments', 'bw-printxtore' ),
                            'views'     => esc_html__( 'Total views', 'bw-printxtore' ),
                        ),
                        'required'    => array('post_single_meta','=','yes'),
                    ),
                    array(
                        'id'          => 'post_single_author',
                        'type'        => 'switch',
                        'title'       => esc_html__('Author','bw-printxtore'),
                        'desc'        => esc_html__('Show or hide author information in the post detail.','bw-printxtore' ),
                        'default'     => false,
                    ),
                    array(
                        'id'          => 'post_single_navigation',
                        'type'        => 'switch',
                        'title'       => esc_html__('Post navigation','bw-printxtore'),
                        'desc'        => esc_html__('Show or hide navigation to next or previous post in the post detail.','bw-printxtore' ),
                        'default'     => false,
                    ),
                    // Related section
                    array(
                        'id'          => 'post_single_related',
                        'type'        => 'switch',
                        'title'       => esc_html__('Related post','bw-printxtore'),
                        'desc'        => esc_html__('Show or hide related post in the post detail.','bw-printxtore' ),
                        'default'     => false,
                    ),
                    array(
                        'id'          => 'post_single_related_title',
                        'type'        => 'text',
                        'title'       => esc_html__('Related title','bw-printxtore'),
                        'desc'        => esc_html__('Enter title of related section.','bw-printxtore'),
                        'required'    => array('post_single_related','=',true),
                    ),
                    array(
                        'id'          => 'post_single_related_number',
                        'type'        => 'text',
                        'title'       => esc_html__('Related number post','bw-printxtore'),
                        'desc'        => esc_html__('Enter number of related post to display.','bw-printxtore'),
                        'required'    => array('post_single_related','=',true),
                    ),
                    array(
                        'id'          => 'post_single_related_item',
                        'type'        => 'text',
                        'title'       => esc_html__('Related custom number item responsive','bw-printxtore'),
                        'desc'        => esc_html__('Enter item for screen width(px) format is width:value and separate values by ",". Example is 0:2,600:3,1000:4. Default is auto.','bw-printxtore'),
                        'required'    => array('post_single_related','=',true),
                    ),
                    array(
                        'id'          => 'post_single_related_item_style',
                        'type'        => 'select',
                        'title'       => esc_html__('Related item style','bw-printxtore'),
                        'desc'        =>esc_html__('Choose a style to active display','bw-printxtore'),
                        'options'     => bzotech_get_post_style(),
                        'required'    => array('post_single_related','=',true),
                    ),
                )
            ) );
            // Blog & Post

            

           
        }
    }

    $bzotech_option_name = bzotech_get_option_name();

   

    $theme = wp_get_theme(); 

    $args = array(
        'opt_name'             => $bzotech_option_name,
        'display_name'         => $theme->get( 'Name' ),
        'display_version'      => $theme->get( 'Version' ),
        'menu_type'            => 'menu',
        'allow_sub_menu'       => true,
        'menu_title'           => esc_html__( 'Theme Options', 'bw-printxtore' ),
        'page_title'           => esc_html__( 'Theme Options', 'bw-printxtore' ),
        'google_api_key'       => 'AIzaSyBFxhycc63fWy_uk126zW8KPtkD3Bay0jI',
        'google_update_weekly' => false,
        'async_typography'     => true,
        'admin_bar'            => true,
        'admin_bar_icon'       => 'dashicons-portfolio',
        'admin_bar_priority'   => 50,
        'global_variable'      => '',
        'dev_mode'             => true,
        'update_notice'        => true,
        'customizer'           => true,
        
        'page_priority'        => 59,
        'page_parent'          => 'themes.php',
        'page_permissions'     => 'manage_options',
        'menu_icon'            => '',
        'last_tab'             => '',
        'page_icon'            => 'icon-themes',
        'menu_icon'            => get_template_directory_uri().'/assets/admin/image/logo.png',
        'page_slug'            => '_options',
        'save_defaults'        => true,
        'default_show'         => false,
        'default_mark'         => '',
        'show_options_object' => false,
        'show_import_export'   => true,

        'transient_time'       => 60 * MINUTE_IN_SECONDS,
        'output'               => true,
        'output_tag'           => true,
        'database'             => '',
        'use_cdn'              => true,
      
        'hints'                => array(
            'icon'          => 'el el-question-sign',
            'icon_position' => 'right',
            'icon_color'    => 'lightgray',
            'icon_size'     => 'normal',
            'tip_style'     => array(
                'color'   => 'light',
                'shadow'  => true,
                'rounded' => false,
                'style'   => '',
            ),
            'tip_position'  => array(
                'my' => 'top left',
                'at' => 'bottom right',
            ),
            'tip_effect'    => array(
                'show' => array(
                    'effect'   => 'slide',
                    'duration' => '500',
                    'event'    => 'mouseover',
                ),
                'hide' => array(
                    'effect'   => 'slide',
                    'duration' => '500',
                    'event'    => 'click mouseleave',
                ),
            ),
        )
    );

    $args['share_icons'][] = array(
        'url'   => 'https://www.facebook.com/BZOTech',
        'title' => 'Like us on Facebook',
        'icon'  => 'el el-facebook'
    );
    $args['share_icons'][] = array(
        'url'   => 'https://twitter.com/BzoTech',
        'title' => 'Follow us on Twitter',
        'icon'  => 'el el-twitter'
    );
    $args['share_icons'][] = array(
        'url'   => 'https://www.youtube.com/@bzotech9150',
        'title' => 'Find us on Youtube',
        'icon'  => 'el el-youtube'
    );
    $args['share_icons'][] = array(
        'url'   => 'https://www.linkedin.com/in/bzotech/',
        'title' => 'Follow us on Linkedin',
        'icon'  => 'el el-linkedin'
    );
    $args['share_icons'][] = array(
        'url'   => 'https://www.pinterest.com/Bzo_Tech',
        'title' => 'Follow us on Pinterest',
        'icon'  => 'el el-pinterest'
    );
    $args['share_icons'][] = array(
        'url'   => 'https://www.instagram.com/bzotech/',
        'title' => 'Follow us on Instagram',
        'icon'  => 'el el-instagram'
    );
    Redux::setArgs( $bzotech_option_name, $args );
    bzotech_switch_redux_option();