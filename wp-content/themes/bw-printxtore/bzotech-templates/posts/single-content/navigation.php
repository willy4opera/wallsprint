<?php
$check_navigation   = bzotech_get_option('post_single_navigation');
if($check_navigation == '1'):
	$previous_post = get_previous_post();
	$next_post = get_next_post();
	?>
	<div class="post-control">
		<div class="flex-wrapper justify_content-space-between">
			<?php if(!empty( $previous_post )):?>
				<div class="prev-post">
					
	                <h3 class="title14 text-left">
	                	<a href="<?php echo esc_url(get_permalink( $previous_post->ID )); ?>" class=" title18 text-capitalize">
	                		<span class="title font-bold color-title title24"><i class="las la-angle-left"></i><?php echo esc_html__('Previous post','bw-printxtore');?></span>
	                		<span class="text-title"><?php echo esc_html($previous_post->post_title)?></span>
	                	</a>
	                </h3>
		            
				</div>
			<?php endif; ?>
			<?php if(!empty( $next_post )):?>
				<div class="next-post">
					
	            	<h3 class="title14 text-right">
	            		<a href="<?php echo esc_url(get_permalink( $next_post->ID )); ?>" class="  title18   text-capitalize">
	            			<span class="title  font-bold color-title title24"><?php echo esc_html__('Next post','bw-printxtore');?><i class="las la-angle-right"></i></span>
	            			<span class="text-title"><?php echo esc_html($next_post->post_title)?></span>
	            		</a>
	            	</h3>
		            
				</div>
			<?php endif;?>
		</div>
	</div>
<?php endif;?>