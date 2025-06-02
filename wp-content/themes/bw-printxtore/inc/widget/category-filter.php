<?php
/**
 * Created by Sublime Text 3.
 * User: MBach90
 * Date: 24/12/15
 * Time: 10:20 AM
 */
if(!class_exists('Bzotech_Category_Fillter') && class_exists("woocommerce"))
{
    class Bzotech_Category_Fillter extends WP_Widget {


        protected $default=array();

        static function _init()
        {
            add_action( 'widgets_init', array(__CLASS__,'_add_widget') );
        }

        static function _add_widget()
        {
            if(function_exists('bzotech_reg_widget')) bzotech_reg_widget( 'Bzotech_Category_Fillter' );
        }

        function __construct() {
            parent::__construct( false, esc_html__('BZOTECH Categories Filter','bw-printxtore'),
                array( 'description' => esc_html__( 'Filter product shop page', 'bw-printxtore' ), ));

            $this->default=array(
                'title' => '',
                'category' => array(),
            );
        }



        function widget( $args, $instance ) {
            global $post;
            $check_shop = true;
            if(isset($post->post_content)){
                if(strpos($post->post_content, '[bzotech_shop')){
                    $check_shop = true;
                }
            }
            if ( ! is_shop() && ! is_product_taxonomy() ) {
                if(!$check_shop) return;
            }
            if(!is_single()){
                echo apply_filters('bzotech_output_content',$args['before_widget']);
                if ( ! empty( $instance['title'] ) ) {
                   echo apply_filters('bzotech_output_content',$args['before_title']) . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
                }

                $instance=wp_parse_args($instance,$this->default);
                extract($instance);
                if(is_object($category)) $category = json_decode(json_encode($category), true);
                if(is_array($category) && !empty($category)){
                    echo '<input type="hidden" name="load-shop-ajax-nonce" class="load-shop-ajax-nonce" value="' . wp_create_nonce( 'load-shop-ajax-nonce' ) . '" />';
                    echo        '<ul class="product_cat">';                
                        $cat_current = '';
                        if(isset($_GET['product_cat'])) $cat_current = sanitize_text_field($_GET['product_cat']);
                        if($cat_current != '') $cat_current = explode(',', $cat_current);
                        else $cat_current = array();
                        foreach ($category as $cat_slug) {
                            $cat = get_term_by('slug',$cat_slug,'product_cat');
                            if(is_object($cat)){
                                if(in_array($cat->slug, $cat_current)) $active = 'active';
                                else $active = '';
                                echo        '<li><a data-product_cat='.esc_attr($cat->slug).' href="'.esc_url(bzotech_get_filter_url('product_cat',$cat->slug)).'" class="load-shop-ajax '.$active.'"> '.$cat->name.'</a><span class="smoke">('.$cat->count.')</span></li>';
                            }
                        }
                    echo      '</ul>';
                }      
                echo apply_filters('bzotech_output_content',$args['after_widget']);
            }
        }

        function update( $new_instance, $old_instance ) {

            $instance=array();
            $instance=wp_parse_args($instance,$this->default);
            $new_instance=wp_parse_args($new_instance,$instance);

            return $new_instance;
        }

        function form( $instance ) {

            $instance=wp_parse_args($instance,$this->default);
            extract($instance);
            if(is_object($category)) $category = json_decode(json_encode($category), true);
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:' ,'bw-printxtore'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
            </p>
            <p>
                <label><?php esc_html_e( 'Categories:' ,'bw-printxtore'); ?></label></br>
                <?php 
                $cats = get_terms('product_cat');
                if(is_array($cats) && !empty($cats)){
                    foreach ($cats as $cat) {
                        if(in_array($cat->slug, $category)) $checked = 'checked="checked"';
                        else $checked = '';
                        echo '<input '.$checked.' id="'.esc_attr($this->get_field_id( 'category' )).'" type="checkbox" name="'.esc_attr($this->get_field_name( 'category' )).'[]" value="'.esc_attr($cat->slug).'"><span>'.$cat->name.'</span>';
                    }
                }
                else echo esc_html__("No any category.",'bw-printxtore');
                ?>
            </p>            
        <?php
        }
    }

    Bzotech_Category_Fillter::_init();

}
