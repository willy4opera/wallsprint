<?php
if(!class_exists('Bzotech_Rating_Filter') && class_exists("woocommerce"))
{
    class Bzotech_Rating_Filter extends WP_Widget {


        protected $default=array();

        static function _init()
        {
            add_action( 'widgets_init', array(__CLASS__,'_add_widget') );
        }

        static function _add_widget()
        {
            if(function_exists('bzotech_reg_widget')) bzotech_reg_widget( 'Bzotech_Rating_Filter' );
        }

        function __construct() {
            parent::__construct( false, esc_html__('BZOTECH Rating Filter','bw-printxtore'),
                array( 'description' => esc_html__( 'Display a list of star ratings to filter products in your store.', 'bw-printxtore' ), ));

            $this->default=array(
                'title'=>esc_html__('Rating','bw-printxtore'),
            );
        }

		protected function get_filtered_product_count( $rating ) {
			global $wpdb;

			$tax_query  = WC_Query::get_main_tax_query();
			$meta_query = WC_Query::get_main_meta_query();

			foreach ( $tax_query as $key => $query ) {
				if ( ! empty( $query['rating_filter'] ) ) {
					unset( $tax_query[ $key ] );
					break;
				}
			}

			$product_visibility_terms = wc_get_product_visibility_term_ids();
			$tax_query[]              = array(
				'taxonomy'      => 'product_visibility',
				'field'         => 'term_taxonomy_id',
				'terms'         => $product_visibility_terms[ 'rated-' . $rating ],
				'operator'      => 'IN',
				'rating_filter' => true,
			);

			$meta_query     = new WP_Meta_Query( $meta_query );
			$tax_query      = new WP_Tax_Query( $tax_query );
			$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
			$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

			$sql  = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) FROM {$wpdb->posts} ";
			$sql .= $tax_query_sql['join'] . $meta_query_sql['join'];
			$sql .= " WHERE {$wpdb->posts}.post_type = '%s' AND {$wpdb->posts}.post_status = '%s' ";
			$sql .= $tax_query_sql['where'] . $meta_query_sql['where'];

			$search = WC_Query::get_main_search_query_sql();
			if ( $search ) {
				$sql .= ' AND ' . $search;
			}

			return absint( $wpdb->get_var( $wpdb->prepare($sql,'product','publish') ) ); 
		}
        function widget( $args, $instance ) {
        	$check_shop = true;
        	if ( ! is_shop() && ! is_product_taxonomy() ) {
				 if(!$check_shop) return;
			}

			if ( ! WC()->query->get_main_query()->post_count ) {
				if(!$check_shop) return;
			}

        	echo do_shortcode($args['before_widget']);
            if ( ! empty( $instance['title'] ) ) {
                echo do_shortcode($args['before_title']) . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
            }
            $instance=wp_parse_args($instance,$this->default);
            extract($instance);
            ob_start();

            $rating_current = '';
            if(isset($_GET['rating_filter'])) $rating_current = $_GET['rating_filter'];
            if($rating_current != '') $rating_current = explode(',', $rating_current);
            else $rating_current = array();
			$found         = false;
			echo '<ul class="bzotech-rating-filter rating_filter">';

			for ( $rating = 1; $rating <= 5; $rating++ ) {
				$count = $this->get_filtered_product_count( $rating );
				if ( empty( $count ) ) {
					continue;
				}
				$found = true;
				if(in_array($rating, $rating_current)) $class = 'active'; else $class = '';
				$rating_html = bzotech_get_rating_html(false,false,'', $rating);
				echo '<li class="'.$class.'" ><a data-rating_filter = "'.$rating.'" class="bzotech-rating-filter__item" href="'.esc_url( bzotech_get_filter_url('rating_filter',$rating) ).'">'.$rating_html.'<span></span></a></li>';
			}

			echo '</ul>';
			if ( ! $found ) {
				ob_end_clean();
			} else {
				echo ob_get_clean(); 
			}
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
        <?php
        }
    }

    Bzotech_Rating_Filter::_init();

}