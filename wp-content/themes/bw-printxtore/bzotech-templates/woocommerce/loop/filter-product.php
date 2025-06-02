<?php
    $data_load = array(
        "args"        => $args,
        "attr"        => $attr,
        );
    $data_loadjs = json_encode($data_load);
echo '<input type="hidden" name="load-product-filter-ajax-nonce" class="load-product-filter-ajax-nonce" value="' . wp_create_nonce( 'load-product-filter-ajax-nonce' ) . '" />';
?>
<div class="filter-product <?php echo esc_attr($filter_style.' '.$filter_column.' '.$filter_pos)?>" data-load="<?php echo esc_attr($data_loadjs);?>" >
    <a href="#" class="btn-filter"><span class="color"><i class="fas fa-search"></i></span><?php esc_html_e("Filter",'bw-printxtore');?></a>
    <div class="box-attr-filter">
        <?php             
        if(!empty($filter_cats)):
            $filter_cats = str_replace(' ', '', $filter_cats);
            $filter_cats = explode(',', $filter_cats);
        ?>
            <div class="item-box-attr">
                <div class="item-attr-title">
                    <label><?php esc_html_e("Categories",'bw-printxtore');?></label>
                </div>
                <div class="item-attr-content">
                    <ul class="list-filter attr-category list-inline">
                        <?php

                        foreach ($filter_cats as $cat) {
                            $term = get_term_by( 'slug',$cat, 'product_cat' );
                            if(is_object($term) && !empty($term)) echo '<li><a href="#" class="element-filter" data-cat="'.esc_attr($cat).'">'.$term->name.'</a></li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        <?php endif;?>
        <?php
        if($filter_price == 'yes'){
            $price_arange = bzotech_get_price_arange();
            $min = $price_arange['min'];
            $max = $price_arange['max'];
            ?>
            <div class="item-box-attr">
                <div class="item-attr-title">
                    <label><?php esc_html_e("Price",'bw-printxtore');?></label>
                </div>
                <div class="item-attr-content range-filter">
                    <div class="slider-range" data-min="<?php echo esc_attr($min)?>" data-max="<?php echo esc_attr($max)?>"></div>
                    <div class="attr-price-filter">
                        <p>
                            <label><?php esc_html_e("Filter By Price:",'bw-printxtore');?></label>
                            <span class="amount"><?php echo get_woocommerce_currency_symbol()?><span class="element-get-min"><?php echo esc_html($min)?></span> - <?php echo get_woocommerce_currency_symbol()?><span class="element-get-max"><?php echo esc_html($max)?></span></span>
                        </p>
                    </div>
                </div>
            </div>
        <?php
        }
        if(!empty($filter_attr)):
            $attribute_filter = explode(',',$filter_attr);
            foreach($attribute_filter as $attr_filter):
                $terms = get_terms('pa_'.$attr_filter);
                $attr_tax = Bzotech_Woocommerce_Attributes::bzotech_get_tax_attribute( "pa_".$attr_filter);
                if(is_object($attr_tax) && !empty($attr_tax)):
    ?>
                    <div class="item-box-attr">
                        <div class="item-attr-title">
                            <label><?php echo esc_html($attr_tax->attribute_label)?></label>
                        </div>
                        <div class="item-attr-content">
                            <?php 
                            switch ($attr_tax->attribute_type){
                                case 'image':?>
                                    <div class="tawcvs-swatches attribute-type-<?php echo esc_attr($attr_tax->attribute_type); ?>">
                                        <?php foreach ($terms as $term){                                
                                            if(is_object($term)){$value = get_term_meta( $term->term_id, 'image', true );
                                                $image = $value ? wp_get_attachment_image_url( $value, 'thumbnail' ) : '';
                                                $image = $image ?  $image : WC()->plugin_url() . '/assets/images/placeholder.png';
                                                $name     = esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) );
                                                $selected = '';
                                                if(!empty($image)){
                                                    echo sprintf(
                                                        '<a data-attribute="%s" data-term="%s" class="element-filter swatch swatch-image swatch-%s %s" title="%s" data-value="%s" href="%s"><img src="%s" alt="%s"><span class="hide">%s</span></a>',
                                                        esc_attr($attr_filter),
                                                        esc_attr( $term->slug ),
                                                        esc_attr( $term->slug ),
                                                        $selected,
                                                        esc_attr( $name ),
                                                        esc_attr( $term->slug ),
                                                        esc_url( bzotech_get_filter_url('filter_'.$attr_tax->attribute_name,$term->slug) ),
                                                        esc_url( $image ),
                                                        esc_attr( $name ),
                                                        esc_attr( $name )
                                                    );
                                                }

                                            }
                                            ?>

                                        <?php } ?>
                                    </div>
                                    <?php
                                    break;
                                case 'color': ?>
                                    <div class="tawcvs-swatches attribute-type-<?php echo esc_attr($attr_tax->attribute_type); ?>">
                                        <?php 
                                        foreach ($terms as $term){
                                            if(is_object($term)){
                                                $color = get_term_meta( $term->term_id, 'color', true );
                                                $name     = esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) );
                                                $class_white_color = '';
                                                $selected = '';
                                                if(!empty($color)){
                                                    $white_color = array('#fff','#ffffff');
                                                    if(in_array(strtolower($color), $white_color)) $class_white_color = 'class_white_bg_color';
                                                    echo sprintf(
                                                        '<a data-attribute="%s" data-term="%s" class="element-filter swatch swatch-color '.$class_white_color.' swatch-%s %s" '.bzotech_add_html_attr('background-color:'.$color).' title="%s" href="%s"><span class="hide">%s</span></a>',
                                                        esc_attr($attr_filter),
                                                        esc_attr( $term->slug ),
                                                        esc_attr( $term->slug ),
                                                        $selected,
                                                        esc_attr( $name ),
                                                        esc_url( bzotech_get_filter_url('filter_'.$attr_tax->attribute_name,$term->slug) ),
                                                        $name
                                                    );
                                                }

                                            }
                                        }
                                        ?>
                                    </div>
                                    <?php
                                    break;
                                case 'label': ?>
                                    <div class="tawcvs-swatches attribute-type-<?php echo esc_attr($attr_tax->attribute_type); ?>">
                                        <?php 
                                        foreach ($terms as $term){
                                            if(is_object($term)){
                                                $label = get_term_meta( $term->term_id, 'label', true );
                                                $name     = esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) );
                                                $label = $label ? $label : $name;
                                                $selected = '';
                                                if(!empty($label)){
                                                    echo sprintf(
                                                        '<a data-attribute="%s" data-term="%s" class="element-filter swatch swatch-label swatch-%s %s" title="%s" data-value="%s" href="%s">%s</a>',
                                                        esc_attr($attr_filter),
                                                        esc_attr( $term->slug ),
                                                        esc_attr( $term->slug ),
                                                        $selected,
                                                        esc_attr( $name ),
                                                        esc_attr( $term->slug ),
                                                        esc_url( bzotech_get_filter_url('filter_'.$attr_tax->attribute_name,$term->slug) ),
                                                        esc_html( $label )
                                                    );
                                                }

                                            }
                                        }
                                        ?>
                                    </div>
                                    <?php
                                    break;
                                default :
                                    echo    '<ul class="list-filter list-inline">';                
                                    if(is_array($terms)){
                                        foreach ($terms as $term) {
                                            if(is_object($term)){
                                                $active = '';
                                                echo    '<li class="'.esc_attr($term->slug).'-inline">
                                                            <a data-attribute="'.esc_attr($attr_filter).'" data-term="'.esc_attr($term->slug).'" class="element-filter '.esc_attr($active).' bgcolor-'.esc_attr($term->slug).'" href="'.esc_url(bzotech_get_filter_url('filter_'.$attr_filter,$term->slug)).'">
                                                            <span></span>'.$term->name.'
                                                            </a>
                                                        </li>';
                                            }
                                        }
                                    }
                                    echo    '</ul>';
                                    break;
                            }
                            ?>
                        </div>
                    </div>
    <?php
                endif;
            endforeach;
        endif;
    ?>
    </div>
</div>