<?php
/**
 * Created by Sublime Text 3.
 * User: MBach90
 * Date: 24/12/15
 * Time: 10:20 AM
 */
if(!class_exists('Bzotech_Group_Begin'))
{
    class Bzotech_Group_Begin extends WP_Widget {


        protected $default=array();

        static function _init()
        {
            add_action( 'widgets_init', array(__CLASS__,'_add_widget') );
        }

        static function _add_widget()
        {
            if(function_exists('bzotech_reg_widget')) bzotech_reg_widget( 'Bzotech_Group_Begin' );
        }

        function __construct() {
            parent::__construct( false, esc_html__('BZOTECH Group begin','bw-printxtore'),
                array( 'description' => esc_html__( 'Begin a Group', 'bw-printxtore' ), ));

            $this->default=array(
                'el_class'         => '',
            );
        }



        function widget( $args, $instance ) {
            $instance=wp_parse_args($instance,$this->default);
            extract($instance);
            echo '<div class="widget-group '.esc_attr($el_class).'">';
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
                <label for="<?php echo esc_attr($this->get_field_id( 'el_class' )); ?>"><?php esc_html_e( 'Wrap class:' ,'bw-printxtore'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'el_class' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'el_class' )); ?>" type="text" value="<?php echo esc_attr( $el_class ); ?>">
            </p>
        <?php
        }
    }

    Bzotech_Group_Begin::_init();

}
