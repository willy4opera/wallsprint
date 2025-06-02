<?php
namespace Elementor;
use WP_Query;
extract($settings);
$taxonomy = 'category';
$tax_key = 'category_name';
if($search_in == 'product'){
	$taxonomy = $tax_key = 'product_cat';
}
?>
<div class="elbzotech-search-wrap-global elbzotech-search-fixed-sidebar <?php echo 'elbzotech-search-global-'.esc_attr($settings['style'].' live-search-'.$live_search)?>">
	
	<div class="search-icon-popup">
		<?php if(!empty( $icon_popup['value'])) Icons_Manager::render_icon( $icon_popup, [ 'aria-hidden' => 'true' ] );
		if(!empty($search_bttext_popup)) echo '<span class="bttext_popup">'.$search_bttext_popup.'</span>';?>
	</div>
	<div class="elbzotech-search-form-wrap-global">
		<div class="flex-wrapper align_items-center justify_content-space-between title-form-fixed-sidebar"><?php echo '<h3 class="title16 font-bold font-title">'.$title_form_icon.'</h3>';?><i class="la la-close elbzotech-close-search-form"></i></div>
		<div class="content-form-popup">

			<form class="elbzotech-search-form-global" action="<?php echo esc_url( home_url( '/' ) ); ?>">

				<?php 
				echo '<input type="hidden" name="live-search-ajax-nonce" class="live-search-ajax-nonce" value="' . wp_create_nonce( 'live-search-ajax-nonce' ) . '" />';
				?>
				<?php if($show_cat == 'yes' && $search_in != 'all'):?>
		            <div class="elbzotech-dropdown-box dropdown-box-cate">
		                <span class="dropdown-link current-search-cat">
		                	<?php echo esc_html__('Search by Category ','bw-printxtore')?>		                		
		                </span>
		                <ul class="list-none elbzotech-dropdown-list">
		                    <li class="active"><a class="select-cat-search" href="#" data-filter=""><?php echo esc_html($title_cat)?></a></li>
		                    <?php
		                        if(!empty($cats)){
		                            $custom_list = explode(",",$cats);
		                            foreach ($custom_list as $key => $cat) {
		                                $term = get_term_by( 'slug',$cat, $taxonomy );
		                                if(!empty($term) && is_object($term)){
		                                    if(!empty($term) && is_object($term)){
		                                        echo '<li><a class="select-cat-search" href="#" data-filter="'.$term->slug.'">'.$term->name.'</a></li>';
		                                    }
		                                }
		                            }
		                        }
		                        else{
		                            $product_cat_list = get_terms($taxonomy);
		                            if(is_array($product_cat_list) && !empty($product_cat_list)){
		                                foreach ($product_cat_list as $cat) {
		                                    echo '<li><a class="select-cat-search" href="#" data-filter="'.$cat->slug.'">'.$cat->name.'</a></li>';
		                                }
		                            }
		                        }
		                    ?>
		                </ul>
		            </div>
		            <input class="cat-value" type="hidden" name="<?php echo esc_attr($tax_key)?>" value="" />
		        <?php endif;?>
		       	<div class="input-submit-form flex-wrapper align_items-stretch">
			        <input name="s" onblur="if (this.value=='') this.value = this.defaultValue" onfocus="if (this.value==this.defaultValue) this.value = ''" value="<?php echo esc_attr($settings['placeholder']);?>" type="text" autocomplete="off">

			        <?php if($search_in != 'all'):?>
			            <input type="hidden" name="post_type" value="<?php echo esc_attr($search_in)?>" />
			        <?php endif;?>
			        <div class="elbzotech-submit-form">
			            <button type="submit" value="" class="elbzotech-text-bt-search">
				            <?php if($settings['search_bttext'] && $settings['search_bttext_pos'] == 'before-icon') echo '<span>'.$settings['search_bttext'].'</span>'?>
			            	<?php 
		            		if(!empty( $icon_popup['value'])) Icons_Manager::render_icon( $icon_popup, [ 'aria-hidden' => 'true' ] );
			            	
							?>
				            	
				            	<?php if($settings['search_bttext'] && $settings['search_bttext_pos'] == 'after-icon') echo '<span>'.$settings['search_bttext'].'</span>'?>
			            </button>
			        </div>
			    </div>
		        
		        <?php
	        
            if($live_search == 'yes'){
            	$trending_html=$href_post_type='';
            	if(!empty($key_trending)){
            		if($search_in !== 'all')
            			$href_post_type = '&post_type='.$search_in;
            		$key_trending = explode("|",$key_trending);
            		$trending_html .= '<div class="key-trending">
				        			<h3 class="key-trending-title title16">'.$title_trending.'</h3><div class="key-trending-list">';
            		foreach ($key_trending as $key => $value) {
            			$trending_html .= '<a href="'.esc_url( home_url( '/' ) ).'?s='.$value.$href_post_type.'">'.$value.'</a>';
            		}
            		$trending_html .= '</div></div>';
            	}
            	$args = array(
		            'post_type'         => $search_in,
		            'posts_per_page'    => $number,
		            'orderby'           => $orderby,
		            'order'             => $order,
		            'paged'             => 1,
	            );
	            if(!empty($cats) && $show_cat == 'yes') {
		            $custom_list = explode(",",$cats);
		            $args['tax_query'][]=array(
		                'taxonomy'=>'product_cat',
		                'field'=>'slug',
		                'terms'=> $custom_list
		            );
		        }

		        $query_search = new WP_Query($args);
		        $count = 1;

				$attr = array(
					'item_wrap'         => $item_wrap,
				    'item_inner'        => $item_inner,
					);
				$data_load = array(
		            "args"        => $args,
		            "attr"        => $attr,
		            'display'   => $display,
		            'item_style'   => $item_style,
		            );
		        $data_loadjs = json_encode($data_load);

		        echo '<div class="js-list-live-search box-live-e display-'.$display.'" data-load="'.esc_attr($data_loadjs).'">';
            		if($search_in == 'product' && $display == 'grid'){
            		
			        
			       
				        echo '<div class="list-search-default">
				        		
				        		'.$trending_html.'
						        <div '.$wrapper.'><h3 class="search-popular-title title16 font-bold text-uppercase">'.$title_live_default.'</h3><div '.$inner.'>';
							        if($query_search->have_posts()) {
							            while($query_search->have_posts()) {
							                $query_search->the_post();
							                $attr['count'] = $count;
							    			bzotech_get_template_woocommerce('loop/grid/grid',$item_style,$attr,true);
							                $count++;
							    		}
							    	} wp_reset_postdata();
				        		echo '</div></div>';
				        echo '</div>';
				        echo '<div '.$wrapper.'><h3 class="search-results-title title16 font-bold text-uppercase">'.esc_html__('Product Results','bw-printxtore').'</h3>';
				        	echo '<div class="content-list-product-search js-content-main list-product-wrap bzotech-row"></div>';
            			echo '</div>';
		            }else{ 
		            	echo '<div class="list-search-default">
				        		
				        		'.$trending_html.'
						        <div '.$wrapper.'><h3 class="search-popular-title title16 font-bold text-uppercase">'.$title_live_default.'</h3><div '.$inner.'>';
							        if($query_search->have_posts()) {
							            while($query_search->have_posts()) {
							                $query_search->the_post();
							                bzotech_get_template_elementor_global('search/item-search-list',null,array('search_in'=>$search_in),true);
							    		}
							    	} wp_reset_postdata();
				        		echo '</div></div>';
				        echo '</div>';
				        echo '<div '.$wrapper.'><h3 class="search-results-title title16 font-bold text-uppercase">'.esc_html__('Product Results','bw-printxtore').'</h3>';
				        	echo '<div class="content-list-product-search js-content-main list-product-wrap bzotech-row"></div>';
            			echo '</div>';
		            

		            }
	            echo '</div>';
            }
	        ?>
		    </form>
		</div>
	</div>
</div>