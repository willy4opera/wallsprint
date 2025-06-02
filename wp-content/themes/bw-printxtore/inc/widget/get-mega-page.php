<?php
/**
 * Created by Sublime Text 3.
 * User: MBach90
 * Date: 24/12/15
 * Time: 10:20 AM
 */
if(!class_exists('Bzotech_WG_Get_Mega_Page'))
{
    class Bzotech_WG_Get_Mega_Page extends WP_Widget {


        protected $default=array();

        static function _init()
        {
            add_action( 'widgets_init', array(__CLASS__,'_add_widget') );
        }

        static function _add_widget()
        {
            if(function_exists('bzotech_reg_widget')) bzotech_reg_widget( 'Bzotech_WG_Get_Mega_Page' );
        }

        function __construct() {
            parent::__construct( false, esc_html__('BZOTECH Get Mega Page','bw-printxtore'),
                array( 'description' => esc_html__( 'Get content in mega page', 'bw-printxtore' ), ));

            $this->default=array(
                'title'=>'',
                'el_class'         => '',
                'page_id'         => '0',
            );
        }



        function widget( $args, $instance ) {
            echo do_shortcode($args['before_widget']);
            if ( ! empty( $instance['title'] ) ) {
                echo do_shortcode($args['before_title']) . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
            }
            $instance=wp_parse_args($instance,$this->default);
            extract($instance);

            if(!empty($page_id))
                echo '<div class="widget-mega_page'.esc_attr($el_class).'">'.Bzotech_Template::get_vc_pagecontent($page_id).'</div>';
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
            $mega_page_arr = bzotech_list_post_type('bzotech_mega_item',true,null,false);
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title' ,'bw-printxtore'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'page_id' )); ?>"><?php esc_html_e( 'Select mega page:' ,'bw-printxtore'); ?></label>

                <select class="widefat" id="<?php echo esc_attr($this->get_field_id( 'page_id' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'page_id' )); ?>">
                    <?php if(is_array($mega_page_arr)){
                        foreach ($mega_page_arr as $key=>$value){

                            echo '<option '.selected($key,$page_id).' value="'.$key.'">'.$value.'</option>';
                        }
                    }?>


                </select>
            </p>

            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'el_class' )); ?>"><?php esc_html_e( 'Wrap class:' ,'bw-printxtore'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'el_class' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'el_class' )); ?>" type="text" value="<?php echo esc_attr( $el_class ); ?>">
            </p>
            <?php
        }
    }

    Bzotech_WG_Get_Mega_Page::_init();
}
