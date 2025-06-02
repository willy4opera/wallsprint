<?php
if(!class_exists('Bzotech_Woocommerce_Attributes')){
    class Bzotech_Woocommerce_Attributes{
        static function _init(){
            if (!class_exists('WC_Product')) return;

            add_action( 'woocommerce_product_option_terms', array( __CLASS__, 'bzotech_product_option_terms_attribute' ), 10, 2 );
            if(is_admin()){
                add_filter( 'product_attributes_type_selector', array( __CLASS__, 'bzotech_add_attribute_types' ) );
                add_action('admin_enqueue_scripts', array(__CLASS__, 'bzotech_attributes_admin_scripts'));
                add_action('admin_init', array(__CLASS__, 'bzotech_init_attribute_hooks'));
                add_action( 'bzotech_product_attribute_field', array( __CLASS__, 'bzotech_attribute_fields' ), 10, 3 );
            }
            add_filter( 'woocommerce_dropdown_variation_attribute_options_html', array( __CLASS__, 'bzotech_get_swatch_html_attribute' ), 100, 2 );
            add_filter( 'bzotech_filters_swatch_html_attribute', array( __CLASS__, 'bzotech_swatch_html_attribute' ), 5, 4 );

        }

        //-------- Begin Add Type Attributes------------//

        static function bzotech_get_tax_attribute( $taxonomy ) {
            global $wpdb;

            $attr = substr( $taxonomy, 3 );
            $attr = $wpdb->get_row( $wpdb->prepare("
                SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = '%s'",$attr ));
            
            return $attr;
        }
        static function bzotech_product_option_terms_attribute( $taxonomy, $index ) {
            $types = array(
                'color' => esc_html__( 'Color', 'bw-printxtore' ),
                'image' => esc_html__( 'Image', 'bw-printxtore' ),
                'label' => esc_html__( 'Label', 'bw-printxtore' ),
            );
            if ( ! array_key_exists( $taxonomy->attribute_type,$types) ) {
                return;
            }

            $taxonomy_name = wc_attribute_taxonomy_name( $taxonomy->attribute_name );
            global $thepostid;
            $product_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : $thepostid;
            ?>

            <select multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select terms', 'bw-printxtore' ); ?>" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php echo esc_attr($index); ?>][]">
                <?php

                $all_terms = get_terms( $taxonomy_name, apply_filters( 'woocommerce_product_attribute_terms', array( 'orderby' => 'name', 'hide_empty' => false ) ) );
                if ( $all_terms ) {
                    foreach ( $all_terms as $term ) {
                        echo '<option value="' . esc_attr( $term->term_id ) . '" ' . selected( has_term( absint( $term->term_id ), $taxonomy_name, $product_id ), true, false ) . '>' . esc_attr( apply_filters( 'woocommerce_product_attribute_term_name', $term->name, $term ) ) . '</option>';
                    }
                }
                ?>
            </select>
            <button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', 'bw-printxtore' ); ?></button>
            <button class="button minus select_no_attributes"><?php esc_html_e( 'Select none', 'bw-printxtore' ); ?></button>
            <button class="button fr plus tawcvs_add_new_attribute" data-type="<?php echo esc_attr($taxonomy->attribute_type); ?>"><?php esc_html_e( 'Add new', 'bw-printxtore' ); ?></button>

            <?php
        }
        static function bzotech_init_attribute_hooks() {
            $attribute_taxonomies = wc_get_attribute_taxonomies();
            if ( empty( $attribute_taxonomies ) ) {
                return;
            }

            foreach ( $attribute_taxonomies as $tax ) {
                add_action( 'pa_' . $tax->attribute_name . '_add_form_fields', array( __CLASS__, 'bzotech_add_attribute_fields' ) );
                add_action( 'pa_' . $tax->attribute_name . '_edit_form_fields', array( __CLASS__, 'bzotech_edit_attribute_fields' ), 10, 2 );
            }
            add_action( 'created_term', array( __CLASS__, 'bzotech_save_term_meta_attribute' ), 10, 2 );
            add_action( 'edit_term', array( __CLASS__, 'bzotech_save_term_meta_attribute' ), 10, 2 );
        }
        static function bzotech_add_attribute_fields( $taxonomy ) {
            $attr = Bzotech_Woocommerce_Attributes::bzotech_get_tax_attribute( $taxonomy );
            do_action( 'bzotech_product_attribute_field', $attr->attribute_type, '', 'add' );
        }
        static function bzotech_edit_attribute_fields( $term, $taxonomy ) {
            $attr = Bzotech_Woocommerce_Attributes::bzotech_get_tax_attribute( $taxonomy );
            $value = get_term_meta( $term->term_id, $attr->attribute_type, true );

            do_action( 'bzotech_product_attribute_field', $attr->attribute_type, $value, 'edit' );
        }
        static function bzotech_attribute_fields( $type, $value, $form ) {
            if ( in_array( $type, array( 'select', 'text' ) ) ) {
                return;
            }
            $types = array(
                'color' => esc_html__( 'Color', 'bw-printxtore' ),
                'image' => esc_html__( 'Image', 'bw-printxtore' ),
                'label' => esc_html__( 'Label', 'bw-printxtore' ),
            );
            printf(
                '<%s class="form-field">%s<label for="term-%s">%s</label>%s',
                'edit' == $form ? 'tr' : 'div',
                'edit' == $form ? '<th>' : '',
                esc_attr( $type ),
                $types[$type],
                'edit' == $form ? '</th><td>' : ''
            );

            switch ( $type ) {
                case 'image':
                    $image_default = WC()->plugin_url() . '/assets/images/placeholder.png';
                    $image = $value ? wp_get_attachment_image_url( $value, 'thumbnail' ) : $image_default;
                    ?>
                    <div class="wrap-metabox">
                        <div class="live-previews" data-image="<?php echo esc_attr($image_default)?>">
                            <img  alt= "<?php echo esc_attr__('image','bw-printxtore'); ?>" src="<?php echo esc_url($image)?>"/>
                        </div>
                        <a class="button button-primary sv-button-remove "> <?php esc_html_e("Remove",'bw-printxtore')?></a>
                        <a class="button button-primary sv-button-upload-id"><?php esc_html_e("Upload",'bw-printxtore')?></a>
                        <input name="image" type="hidden" class="sv-image-value" value="<?php echo esc_attr( $value ) ?>"> </input>
                    </div>

                    <?php
                    break;

                default:
                    ?>
                    <input type="text" id="term-<?php echo esc_attr( $type ) ?>" name="<?php echo esc_attr( $type ) ?>" value="<?php echo esc_attr( $value ) ?>" />
                    <?php
                    break;
            }

            echo 'edit' == $form ? '</td></tr>' : '</div>';
        }
        static function bzotech_save_term_meta_attribute( $term_id, $tt_id ) {
            $types = array(
                'color' => esc_html__( 'Color', 'bw-printxtore' ),
                'image' => esc_html__( 'Image', 'bw-printxtore' ),
                'label' => esc_html__( 'Label', 'bw-printxtore' ),
            );
            foreach ( $types as $type => $label ) {
                if ( isset( $_POST[$type] ) ) {
                    $type_val = sanitize_text_field($_POST[$type]);
                    update_term_meta( $term_id, $type, $type_val );
                }
            }
        }
        static function bzotech_add_attribute_types($types) {
            $add_type = array(
                'color' => esc_html__( 'Color', 'bw-printxtore' ),
                'image' => esc_html__( 'Image', 'bw-printxtore' ),
                'label' => esc_html__( 'Label', 'bw-printxtore' ),
            );
            $types = array_merge( $types, $add_type);
            return $types;
        }
        static function bzotech_attributes_admin_scripts(){
            $screen = get_current_screen();
            if (strpos($screen->id, 'pa_') !== false) :
                wp_enqueue_media();
                wp_enqueue_style( 'wp-color-picker' );
                wp_enqueue_script( 'wp-color-picker');
            endif;
        }

        static function bzotech_get_swatch_html_attribute( $html, $args ) {
            $swatch_types = array(
                'color' => esc_html__( 'Color', 'bw-printxtore' ),
                'image' => esc_html__( 'Image', 'bw-printxtore' ),
                'label' => esc_html__( 'Label', 'bw-printxtore' ),
            );
            $attr         = Bzotech_Woocommerce_Attributes::bzotech_get_tax_attribute( $args['attribute'] );
            if ( empty( $attr ) ) {
                return $html;
            }

            if ( ! array_key_exists( $attr->attribute_type, $swatch_types ) ) {
                return $html;
            }
            $options   = $args['options'];
            $product   = $args['product'];
            $attribute = $args['attribute'];
            $class     = "variation-selector variation-select-{$attr->attribute_type}";
            $swatches  = '';

            if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
                $attributes = $product->get_variation_attributes();
                $options    = $attributes[$attribute];
            }

            if ( array_key_exists( $attr->attribute_type, $swatch_types ) ) {
                if ( ! empty( $options ) && $product && taxonomy_exists( $attribute ) ) {
                    $terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );

                    foreach ( $terms as $term ) {
                        if ( in_array( $term->slug, $options ) ) {
                            $swatches .= apply_filters( 'bzotech_filters_swatch_html_attribute', '', $term, $attr, $args );
                        }
                    }
                }

                if ( ! empty( $swatches ) ) {
                    $class .= ' hidden';

                    $swatches = '<div class="tawcvs-swatches" data-attribute_name="attribute_' . esc_attr( $attribute ) . '">' . $swatches . '</div>';
                    $html     = '<div class="' . esc_attr( $class ) . '">' . $html . '</div>' . $swatches;
                }
            }

            return $html;
        }
        static function bzotech_swatch_html_attribute( $html, $term, $attr, $args ) {
            $selected = sanitize_title( $args['selected'] ) == $term->slug ? 'selected' : '';
            $name     = esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) );

            switch ( $attr->attribute_type ) {
                case 'color':
                    $class_white = '';
                    $color = get_term_meta( $term->term_id, 'color', true );
                    if($color == '#fff' || $color == '#ffffff') $class_white = 'white-color';
                    $html = sprintf(
                        '<span class="swatch swatch-color '.$class_white.' swatch-%s %s" '.bzotech_add_html_attr('background-color:'.$color).' title="%s" data-value="%s"><span class="hide">%s</span></span>',
                        esc_attr( $term->slug ),
                        $selected,
                        esc_attr( $name ),
                        esc_attr( $term->slug ),
                        $name
                    );
                    break;

                case 'image':
                    $value = get_term_meta( $term->term_id, 'image', true );
                    $image = $value ? wp_get_attachment_image_url( $value, 'thumbnail' ) : '';
                    $image = $image ?  $image : WC()->plugin_url() . '/assets/images/placeholder.png';
                    $html  = sprintf(
                        '<span class="swatch swatch-image swatch-%s %s" title="%s" data-value="%s"><img src="%s" alt="%s"><span class="hide">%s</span></span>',
                        esc_attr( $term->slug ),
                        $selected,
                        esc_attr( $name ),
                        esc_attr( $term->slug ),
                        esc_url( $image ),
                        esc_attr( $name ),
                        esc_attr( $name )
                    );
                    break;

                case 'label':
                    $label = get_term_meta( $term->term_id, 'label', true );
                    $label = $label ? $label : $name;
                    $html  = sprintf(
                        '<span class="swatch-label-box"><span class="swatch swatch-label swatch-%s %s" title="%s" data-value="%s">%s</span><span class="desc">%s</span></span>',
                        esc_attr( $term->slug ),
                        $selected,
                        esc_attr( $name ),
                        esc_attr( $term->slug ),
                        esc_html( $label),
                        esc_html( $term->description )
                    );
                    break;
            }

            return $html;
        }        
    }
    Bzotech_Woocommerce_Attributes::_init();
}