<?php
/**
 * Created by PhpStorm.
 * User: mai100it
 * Date: 27/09/2017
 * Time: 11:08 SA
 */
if(!class_exists('Bzotech_BlogListPostsWidget'))
{
    class Bzotech_BlogListPostsWidget extends WP_Widget {


        protected $default=array();

        static function _init()
        {
            add_action( 'widgets_init', array(__CLASS__,'_add_widget') );
        }

        static function _add_widget()
        {
            if(function_exists('bzotech_reg_widget')) bzotech_reg_widget( 'Bzotech_BlogListPostsWidget' );
        }

        function __construct() {
            parent::__construct( false, esc_html__('BZOTECH Posts List','bw-printxtore'),
                array( 'description' => esc_html__( 'Posts list', 'bw-printxtore' ), ));

            $this->default=array(
                'title'=>esc_html__('List Posts','bw-printxtore'),
                'posts_per_page'=>8,
                'style'=>'default',
                'category'=>'',
                'order'=>'desc',
                'order_by'=>'ID',
                'image_size'=>'',
                'number_row'=>'',
            );
        }



        function widget( $args, $instance ) {
            echo do_shortcode($args['before_widget']);
            if ( ! empty( $instance['title'] ) ) {
                echo do_shortcode($args['before_title']) . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
            }

            $instance=wp_parse_args($instance,$this->default);
            extract($instance);
            $args_post = array(
                'post_type'         => 'post',
                'posts_per_page'    => $posts_per_page,
                'orderby'           => $order_by,
                'order'             => $order,
            );
            if(!empty($category)){
                $args_post['tax_query'][]=array(
                    'taxonomy'=>'category',
                    'field'=>'id',
                    'terms'=> $category
                );
            }
            $post_query = new WP_Query($args_post);

            echo bzotech_get_template_widget('post',false,array(
                'instance'=>$instance,
                'post_query'=>$post_query,
                'number_row'=>$number_row,
            ));

            echo do_shortcode($args['after_widget']);
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

            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title' ,'bw-printxtore'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
            </p>

            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'posts_per_page' )); ?>"><?php esc_html_e( 'Post Number' ,'bw-printxtore'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'posts_per_page' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'posts_per_page' )); ?>" type="text" value="<?php echo esc_attr( $posts_per_page ); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'order_by' )); ?>"><?php esc_html_e( 'Order By' ,'bw-printxtore'); ?></label>

                <select class="widefat" id="<?php echo esc_attr($this->get_field_id( 'order_by' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'order_by' )); ?>">
                    <?php echo bzotech_get_order_list($order_by,false,'option');?>
                </select>
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'order' )); ?>"><?php esc_html_e( 'Order' ,'bw-printxtore'); ?></label>

                <select class="widefat" id="<?php echo esc_attr($this->get_field_id( 'order' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'order' )); ?>">
                    <option <?php selected('asc',$order) ?> value="asc">ASC</option>
                    <option <?php selected('desc',$order) ?> value="desc">DESC</option>

                </select>
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'category' )); ?>"><?php esc_html_e( 'Category' ,'bw-printxtore'); ?></label>

                <?php wp_dropdown_categories(array(
                    'selected'=>$category,
                    'show_option_all'=>esc_html__('--- Select ---','bw-printxtore'),
                    'name'  =>$this->get_field_name( 'category' )
                )); ?>
            </p>

            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'number_row' )); ?>"><?php esc_html_e( 'Number product in item silder (Default: 4)' ,'bw-printxtore'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'number_row' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'number_row' )); ?>" type="text" value="<?php echo esc_attr( $number_row ); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'image_size' )); ?>"><?php esc_html_e( 'Custom image size (Example: enter size in pixels : 200x100 (Width x Height))' ,'bw-printxtore'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'image_size' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'image_size' )); ?>" type="text" value="<?php echo esc_attr( $image_size ); ?>">
            </p>

            <?php
        }
    }

    Bzotech_BlogListPostsWidget::_init();

}