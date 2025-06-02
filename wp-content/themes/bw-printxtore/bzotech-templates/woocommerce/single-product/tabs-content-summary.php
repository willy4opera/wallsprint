<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$tabs = apply_filters( 'woocommerce_product_tabs', array() );
$tab_style = bzotech_get_value_by_id('product_tab_detail');
if ( ! empty( $tabs ) ) : 
        
    if($tab_style == 'tab-product-accordion'){ ?>
        <div class="set_offset_top">
            <div class="tab-content-mega <?php echo esc_attr($tab_style)?>">

                <div class="tab-product-accordion-js" data-active="false">
                    <?php
                    $i = 1;
                    foreach ( $tabs as $key => $tab ) : ?>
                            <?php if($key !== 'reviews'){ ?>
                            <h3 class="<?php echo esc_attr( $key ); ?>_tab" id="tab-title-<?php echo esc_attr( $key ); ?>">
                                <a class="color-title font-semibold" href="#tab-<?php echo esc_attr( $key ); ?>" data-target="#tab-<?php echo esc_attr( $key ); ?>" data-toggle="tab" aria-expanded="false">
                                    <?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); ?>
                                </a>
                            </h3>
                            <div id="tab-<?php echo esc_attr( $key ); ?>" class="item-accordion">
                                <div class="detail-tab-desc detail-content-wrap">
                                    <?php if ( isset( $tab['callback'] ) ) { call_user_func( $tab['callback'], $key, $tab ); } ?>
                                </div>
                            </div>
                        <?php } ?>
                        <?php endforeach; ?>

                </div>
            </div>
        </div>
        <?php
    } ?>
    
<?php endif;
