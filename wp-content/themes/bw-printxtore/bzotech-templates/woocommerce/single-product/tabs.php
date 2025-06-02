<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$tabs = apply_filters( 'woocommerce_product_tabs', array() );
$tab_style = bzotech_get_value_by_id('product_tab_detail');
if(empty($tab_style))$tab_style='tab-product-accordion';
$get_style_woo_single = bzotech_get_value_by_id('sv_style_woo_single');

$key_check_style = array();
if($get_style_woo_single == 'sticky-style3'){
	$key_check_style = array('description'); //check remover tab by style product
}
if ( ! empty( $tabs ) ) :
    switch ($tab_style){
        case 'tab-product-accordion': ?>
            <div class="comments-<?php echo esc_attr($tab_style)?>">
                    <?php
                    $i = 1;
                    foreach ( $tabs as $key => $tab ) :
                    	if(in_array($key,$key_check_style)) continue;
                        if($i == 1) $active = 'active';
                        else $active = '';
                        $i++;
                        if($key == 'reviews'){ 
                            global $product;

                            $rating_count = $product->get_rating_count();
                            $review_count = $product->get_review_count();
                            $average      = $product->get_average_rating();?>
                            <div class="comments-tab-product-accordion__header align_items-center flex-wrapper flex_wrap-wrap justify_content-space-between align_items-flex-start">
                                <div class="woocommerce-product-rating flex-wrapper flex_wrap-wrap align_items-center total-star">
                                    <?php echo wc_get_rating_html( $average, $rating_count ); // WPCS: XSS ok. ?>
                                    <?php if ( comments_open() ) : ?>
                                        <?php //phpcs:disable ?>
                                        <a href="#reviews" class="woocommerce-review-link" rel="nofollow">(<?php printf( _n( '%s customer review', '%s customer reviews', $review_count, 'bw-printxtore' ), '<span class="count">' . esc_html( $review_count ) . '</span>' ); ?>)</a>
                                        <?php // phpcs:enable ?>
                                    <?php endif ?>
                                </div>
                                <div><a class="elbzotech-bt-default write-a-review"><i class="las la-pen-fancy"></i><?php echo esc_html__('Write A Review','bw-printxtore'); ?></a></div>
                            </div>

                            
                            <div id="tab-<?php echo esc_attr( $key ); ?>">
                                <?php if ( isset( $tab['callback'] ) ) { call_user_func( $tab['callback'], $key, $tab ); } ?>
                            </div>
                           <?php }
                        ?>
                        
                    <?php endforeach; ?>
            </div>
            <?php
            break;
        case 'tab-product-vertical': ?>
            <div class="detail-product-tabs tab-wrap <?php echo esc_attr($tab_style)?>">
                <div class="product-tab-title">
                    <ul class="list-none" role="tablist">
                        <?php
                        $i = 1;
                        foreach ( $tabs as $key => $tab ) :
                        	if(in_array($key,$key_check_style)) continue;
                            if($i == 1) $active = 'active';
                            else $active = '';
                            $i++;
                            if(!empty($tab['title'])){
                            ?>
                            <li class="<?php echo esc_attr($active)?> <?php echo esc_attr( $key ); ?>_tab" id="tab-title-<?php echo esc_attr( $key ); ?>">
                                <a class="title20 font-regular text-uppercase" href="#tab-<?php echo esc_attr( $key ); ?>" data-target="#tab-<?php echo esc_attr( $key ); ?>" data-toggle="tab" aria-expanded="false" role="button">
                                    <?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); ?><i class="las la-caret-right"></i>
                                </a>
                            </li>
                        <?php }
                        endforeach; ?>
                    </ul>
                </div>
                <div class="product-tab-content">
                    <div class="tab-content">
                        <?php
                        $i = 1;
                        foreach ( $tabs as $key => $tab ) :
                        	if(in_array($key,$key_check_style)) continue;
                            if($i == 1) $active = 'active';
                            else $active = '';
                            $i++;
                            
                            ?>
                            <div id="tab-<?php echo esc_attr( $key ); ?>" class="tab-pane <?php echo esc_attr($active)?>">
                                <div class="detail-tab-desc detail-content-wrap">
                                    <?php 
                                    if($key == 'reviews'){ 
                                        global $product;

                                        $rating_count = $product->get_rating_count();
                                        $review_count = $product->get_review_count();
                                        $average      = $product->get_average_rating();?>
                                        <div class="comments-tab-product-accordion__header align_items-center flex-wrapper flex_wrap-wrap justify_content-space-between align_items-flex-start">
                                            <div class="woocommerce-product-rating flex-wrapper flex_wrap-wrap align_items-center total-star">
                                                <?php echo wc_get_rating_html( $average, $rating_count ); // WPCS: XSS ok. ?>
                                                <?php if ( comments_open() ) : ?>
                                                    <?php //phpcs:disable ?>
                                                    <a href="#reviews" class="woocommerce-review-link" rel="nofollow">(<?php printf( _n( '%s customer review', '%s customer reviews', $review_count, 'bw-printxtore' ), '<span class="count">' . esc_html( $review_count ) . '</span>' ); ?>)</a>
                                                    <?php // phpcs:enable ?>
                                                <?php endif ?>
                                            </div>
                                            <div><a class="elbzotech-bt-default write-a-review"><i class="las la-pen-fancy"></i><?php echo esc_html__('Write A Review','bw-printxtore'); ?></a></div>
                                        </div>
                                    <?php }
                                    if ( isset( $tab['callback'] ) ) { call_user_func( $tab['callback'], $key, $tab ); } ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php
            break;
        default: ?>
            <div class="detail-product-tabs tab-wrap <?php echo esc_attr($tab_style)?>">
                <div class="product-tab-title">
                    <ul class="list-none" role="tablist">
                        <?php
                        $i = 1;
                        foreach ( $tabs as $key => $tab ) :
                        	if(in_array($key,$key_check_style)) continue;
                            if($i == 1) $active = 'active';
                            else $active = '';
                            $i++;
                            ?>
                            <li class="<?php echo esc_attr($active)?> <?php echo esc_attr( $key ); ?>_tab" id="tab-title-<?php echo esc_attr( $key ); ?>">
                                <a class="title20 font-regular text-uppercase" href="#tab-<?php echo esc_attr( $key ); ?>" data-target="#tab-<?php echo esc_attr( $key ); ?>" data-toggle="tab" aria-expanded="false" role="button">
                                    <?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="product-tab-content">
                    <div class="tab-content">
                        <?php
                        $i = 1;
                        foreach ( $tabs as $key => $tab ) :
                        	if(in_array($key,$key_check_style)) continue;
                            if($i == 1)
                                $active = 'active';
                            else $active ='';

                            ?>
                            <h3 class="hide title-tab-product-mobi <?php if($i == count($tabs)) echo 'last'; ?>"><?php echo esc_html( $tab['title'] ); ?></h3>
                            <div id="tab-<?php echo esc_attr( $key ); ?>" class="tab-pane <?php echo esc_attr($active)?>">

                                <div class="detail-tab-desc detail-content-wrap">
                                    <?php 
                                        if($key == 'reviews'){ 
                                        global $product;

                                        $rating_count = $product->get_rating_count();
                                        $review_count = $product->get_review_count();
                                        $average      = $product->get_average_rating();?>
                                        <div class="comments-tab-product-accordion__header align_items-center flex-wrapper flex_wrap-wrap justify_content-space-between align_items-flex-start">
                                            <div class="woocommerce-product-rating flex-wrapper flex_wrap-wrap align_items-center total-star">
                                                <?php echo wc_get_rating_html( $average, $rating_count ); // WPCS: XSS ok. ?>
                                                <?php if ( comments_open() ) : ?>
                                                    <?php //phpcs:disable ?>
                                                    <a href="#reviews" class="woocommerce-review-link" rel="nofollow">(<?php printf( _n( '%s customer review', '%s customer reviews', $review_count, 'bw-printxtore' ), '<span class="count">' . esc_html( $review_count ) . '</span>' ); ?>)</a>
                                                    <?php // phpcs:enable ?>
                                                <?php endif ?>
                                            </div>
                                            <div><a class="elbzotech-bt-default write-a-review"><i class="las la-pen-fancy"></i><?php echo esc_html__('Write A Review','bw-printxtore'); ?></a></div>
                                        </div>
                                    <?php }
                                    if ( isset( $tab['callback'] ) ) { call_user_func( $tab['callback'], $key, $tab ); } ?>
                                </div>
                            </div>
                        <?php $i=$i+1; endforeach; ?>
                    </div>
                </div>
            </div>
            <?php
            break;
    }

endif;
