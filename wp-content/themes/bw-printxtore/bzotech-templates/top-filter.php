<?php
global $post;
if($show_number == 'on' || $show_number == '1') $show_number = 'yes';
if($show_type == 'on' || $show_type == '1') $show_type = 'yes';
if(!isset($show_order)) $show_order = false;
if(isset($show_order) && $show_order == 'on' || $show_order == '1') $show_order = true;
?>
<?php if($show_number == 'yes' || $show_type == 'yes' || $show_order):?>
<div class="main-top-filter">
    <?php echo '<input type="hidden" name="load-shop-ajax-nonce" class="load-shop-ajax-nonce" value="' . wp_create_nonce( 'load-shop-ajax-nonce' ) . '" />'; ?>
    <div class="content-top-filter top-filter flex-wrapper justify_content-space-between align_items-center">
        <div class="main-top-filter__left flex-wrapper align_items-center">
            <div class="open-hide-filters open-hide-filters-desktop"><a href="#" data-textshow = "<?php echo esc_html__('Show Sidebar','bw-printxtore'); ?>"  data-texthide = "<?php echo esc_html__('Hide Sidebar','bw-printxtore'); ?>" class="dropdown-link"><span class="text"><?php echo esc_html__('Hide Sidebar','bw-printxtore'); ?></span><?php echo bzotech_get_icon_svg('hide-filters'); ?></a></div>
            <?php 
            if(function_exists('is_shop')) if(is_shop()) $show_order = true;
            if($show_order == true) $add_class = 'load-shop-ajax';
            else $add_class = '';
            if($show_number == 'yes'):
                    $source = 'blog';
                    if(bzotech_is_woocommerce_page() || strpos($post->post_content, '[bzotech_shop')) $source = 'shop';
                    $list   = bzotech_get_option($source.'_number_filter_list');
                    if(isset($list[0]['number'])) $check_list = trim($list[0]['number']);
                    if(empty($list) || !$check_list){
                        $list = array(12,16,20,24);
                    }
                    else{
                        $temp = array();
                        foreach ($list as $value) {
                            $temp[] = (int)$value['number'];
                        }
                        $list = $temp;
                    }
                    $number_df = get_option( 'posts_per_page' );
                    if(!isset($count_query)){
                        $count_query ='';
                    } else{
                         $count_query = '<span class="total-count">'.esc_html__(' of ','bw-printxtore').$count_query.'</span>';
                    }
                    if(!in_array((int)$number_df, $list)) $list = array_merge(array((int)$number_df),$list);
                    if(!in_array((int)$number, $list) && $number) $list = array_merge(array((int)$number),$list);
                    if(isset($wp_query->query_vars['posts_per_page'])) $number = $wp_query->query_vars['posts_per_page'];
                    if(isset($_GET['number'])) $number = sanitize_text_field($_GET['number']); ?>
                    <div class="show-by elbzotech-dropdown-box">
                        <a href="#" class="dropdown-link"><span class="gray"><?php esc_html_e("Showing: ",'bw-printxtore')?></span><span class="silver number"> <?php echo esc_html((int)$number)?></span><?php echo apply_filters('bzotech_output_content',$count_query); ?></a>
                        <ul class=" list-none elbzotech-dropdown-list elbzotech-dropdown-number-blog">
                            <?php
                            if(is_array($list)){
                                foreach ($list as $value) {
                                    if($value == $number) $active = ' active';
                                    else $active = '';
                                    echo '<li><a data-number="'.esc_attr($value).'" class="'.esc_attr($add_class.$active).'" href="'.esc_url(bzotech_get_key_url('number',$value)).'">'.$value.'</a></li>';
                                }
                            }
                            ?>
                        </ul>
                    </div>
            <?php endif;?>
            <?php
                global $wp_query;
                
                $orderby = apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
                if(isset($_GET['orderby'])) $orderby = sanitize_text_field($_GET['orderby']);
                if($show_order):?>
                    <div class="sort-by">
                        
                        <div class="inline-block">
                            <?php bzotech_catalog_ordering($wp_query,$orderby,true,$add_class);?>
                        </div>
                    </div>
                <?php endif;
            ?>
        </div>
        <div class="main-top-filter__right ">
            <ul class="sort-pagi-bar list-inline-block">
               
                <?php if($show_type == 'yes'):?>
                <li>
                    <div class="view-type">
                       
                        <a data-type="list" href="<?php echo esc_url(bzotech_get_key_url('type','list'))?>" class="list-view <?php echo esc_attr($add_class)?> <?php if($style == 'list') echo 'active'?>"><i class="icon-bzo icon-bzo-filter-list title32"></i></a>
                        <?php
                        if(!empty($column_style_type)){
                            if($column_style_type>4)$column_style_type = 4;
                            for($item = 2; $item <= $column_style_type; $item++){
                                $key = $item;
                                if($item  >= 4) $key = 'n';
                                ?>
                                <a data-type="grid" href="<?php echo esc_url(bzotech_get_key_url('type','grid-'.$key.'col'))?>" class="grid-<?php echo esc_attr($key)?> grid-view <?php echo esc_attr($add_class)?> <?php if($style == 'grid-'.$key.'col') echo 'active'?>"><i class="title32 icon-bzo icon-bzo-filter-grid<?php echo esc_attr($key)?>"></i></a>
                                <?php
                            }
                        }?>
                    </div>
                </li>
                <?php endif;?>
            </ul>
            
        </div>
    </div>
    <?php 
    $filter_url_price = '';
    if(isset($_GET['min_price'])) $filter_url_price = 'min_price='.sanitize_text_field($_GET['min_price']);
    if(isset($_GET['max_price'])) $filter_url_price = 'max_price'.sanitize_text_field($_GET['max_price']);
    if(isset($_GET['max_price'])&&(isset($_GET['min_price']))) $filter_url_price = 'min_price='.sanitize_text_field($_GET['min_price']).'&max_price='.sanitize_text_field($_GET['max_price']);
    
    $filter_url = $clear_filters = $current_url = '';
    if(function_exists('bzotech_get_query_string_url')) $filter_url =bzotech_get_query_string_url();
    if(function_exists('bzotech_get_current_url')) $current_url =  bzotech_get_current_url();
    if(!empty($filter_url_price) && !empty($current_url)){
        $filter_url_price = ['&'.$filter_url_price,$filter_url_price.'&','&'.$filter_url_price.'&',$filter_url_price];
        $filter_url = str_replace($filter_url_price,'',$filter_url);
        $filter_url_price = str_replace($filter_url_price,'',$current_url);
    }
    if($filter_url_price || $filter_url && function_exists('bzotech_get_url_path'))
        $clear_filters = bzotech_get_url_path();
    ?>
    <div class="main-filter__hitory  flex-wrapper justify_content-space-between align_items-center">
        <div class="main-filter__hitory flex-wrapper justify_content-space-between align_items-center"><div class="js-filter-hitory flex-wrapper " data-filterurl = "<?php echo esc_attr($filter_url); ?>" data-clearfilters = "<?php echo esc_url($clear_filters); ?>" data-filterurlprice = "<?php echo esc_url($filter_url_price); ?>"></div></div>
        <div class="main-filter__number"><?php echo woocommerce_result_count(); ?></div>
    </div>
</div>
<?php endif; 