<?php
$check_share = bzotech_get_option('post_single_share',array());
$check_share_page = bzotech_get_value_by_id('post_single_page_share');
$post_type = get_post_type();
if((isset($check_share[$post_type]) && $check_share[$post_type] == '1') || $check_share_page == '1'):
	$list_default = array(
		array(
			'title'  => esc_html__('Total','bw-printxtore'),
		    'social' => 'total',
		    'number' => '1',
			),
		array(
			'title'  => esc_html__('Facebook','bw-printxtore'),
		    'social' => 'facebook',
		    'number' => '1',
			),
		array(
			'title'  => esc_html__('Twitter','bw-printxtore'),
		    'social' => 'twitter',
		    'number' => '1',
			),
		array(
			'title'  => esc_html__('Pinterest','bw-printxtore'),
		    'social' => 'pinterest"',
		    'number' => '1',
			),
		array(
			'title'  => esc_html__('Linkedin','bw-printxtore'),
		    'social' => 'Linkedin',
		    'number' => '1',
			),
		array(
			'title'  => esc_html__('Tumblr','bw-printxtore'),
		    'social' => 'tumblr',
		    'number' => '1',
			),
		array(
			'title'  => esc_html__('Email','bw-printxtore'),
		    'social' => 'envelope',
		    'number' => '1',
			),
		);
	$list = bzotech_get_option('post_single_share_list',$list_default);
	$html_list='';
	$html_total='';

?>
<?php if(!empty($style) && $style == 'popup') echo '<a href="#" class="popup-share"><i class="icon-bzo icon-bzo-share"><i class="path1"></i></i><span>'.esc_html__('Share','bw-printxtore').'</span></a>';?>
<div class="single-list-social <?php if(!empty($el_class)) echo esc_attr($el_class); ?>" data-id="<?php echo esc_attr(get_the_ID())?>">
	<?php 
		foreach ($list as $value) {

			switch ($value['social']) { 
				case 'facebook': 
					$number = get_post_meta(get_the_ID(),'total_share_'.$value['social'],true);
					if(empty($number)) $number = 0;
					if($value['number'] == '1') $number_html = '<span class="number">'.esc_html($number).'</span>';
					else $number_html = '';
					$html_list .='<li><a target="_blank" data-social="'.esc_attr($value['social']).'" title="'.esc_attr($value['title']).'" href="'.esc_url('http://www.facebook.com/sharer.php?u='.urlencode(get_the_permalink())).'">

								<span class="share-icon '.esc_attr($value['social']).'-social"><i class="fab fa-facebook-square"></i></span>
							</a></li>';
					break;

				case 'twitter':
					$number = get_post_meta(get_the_ID(),'total_share_'.$value['social'],true);
					if(empty($number)) $number = 0;
					if($value['number'] == '1') $number_html = '<span class="number">'.esc_html($number).'</span>';
					else $number_html = '';
					$html_list .='<li><a target="_blank" data-social="'.esc_attr($value['social']).'" title="'.esc_attr($value['title']).'" href="'.esc_url('http://www.twitter.com/share?url='.get_the_permalink()).'">
								<span class="share-icon '.esc_attr($value['social']).'-social"><i class="fab fa-twitter-square"></i></span>
							</a></li>';
					break;

				case 'pinterest':
					$number = get_post_meta(get_the_ID(),'total_share_'.$value['social'],true);
					if(empty($number)) $number = 0;
					if($value['number'] == '1') $number_html = '<span class="number">'.esc_html($number).'</span>';
					else $number_html = '';
					$html_list .='<li><a target="_blank" data-social="'.esc_attr($value['social']).'" title="'.esc_attr($value['title']).'" href="'.esc_url('http://pinterest.com/pin/create/button/?url='.get_the_permalink().'&amp;media='.wp_get_attachment_url(get_post_thumbnail_id())).'">
								<span class="share-icon '.esc_attr($value['social']).'-social"><i class="fab fa-pinterest"></i></span>
							</a></li>';
					break;

				case 'envelope':
					$number = get_post_meta(get_the_ID(),'total_share_'.$value['social'],true);
					if(empty($number)) $number = 0;
					if($value['number'] == '1') $number_html = '<span class="number">'.esc_html($number).'</span>';
					else $number_html = '';
					$html_list .='<li><a target="_blank" data-social="'.esc_attr($value['social']).'" title="'.esc_attr($value['title']).'" href="mailto:?subject='.esc_attr__("I wanted you to see this site&amp;body=Check out this site",'bw-printxtore').' '.get_the_permalink().'">
								<span class="share-icon '.esc_attr($value['social']).'-social"><i class="fas fa-envelope"></i></span>
							</a></li>';
					break;

				case 'linkedin':
					$number = get_post_meta(get_the_ID(),'total_share_'.$value['social'],true);
					if(empty($number)) $number = 0;
					if($value['number'] == '1') $number_html = '<span class="number">'.esc_html($number).'</span>';
					else $number_html = '';
					$html_list .='<li><a target="_blank" data-social="'.esc_attr($value['social']).'" title="'.esc_attr($value['title']).'" href="'.esc_url('https://www.linkedin.com/cws/share?url='.get_the_permalink()).'">
								<span class="share-icon '.esc_attr($value['social']).'-social"><i class="lab la-'.esc_attr($value['social']).'-in" aria-hidden="true"></i>'.$number_html.'</span>
							</a></li>';
					break;

				case 'tumblr':
					$number = get_post_meta(get_the_ID(),'total_share_'.$value['social'],true);
					if(empty($number)) $number = 0;
					if($value['number'] == '1') $number_html = '<span class="number">'.esc_html($number).'</span>';
					else $number_html = '';
					$html_list .='<li><a target="_blank" data-social="'.esc_attr($value['social']).'" title="'.esc_attr($value['title']).'" href="'.esc_url('https://www.tumblr.com/widgets/share/tool?canonicalUrl='.get_the_permalink().'&amp;title='.get_the_title()).'">
								<span class="share-icon '.esc_attr($value['social']).'-social"><i class="lab la-'.esc_attr($value['social']).'" aria-hidden="true"></i>'.$number_html.'</span>
							</a></li>';
					break;
				
				case 'total':
					$number = get_post_meta(get_the_ID(),'total_share',true);
					if(empty($number)) $number = 0;
					if($value['number'] == '1') $number_html = '<span class="number">'.esc_html($number).'</span>';
					else $number_html = '';
					$html_total .= '<span class="share-icon total-share "><span class="title18 font-light label-title">'.$value['title'].'</span><i class="las la-share-alt" aria-hidden="true"></i>'.$number_html.'</span>';
					break;
			}			
		}
	?>
	<?php echo apply_filters('bzotech_output_content', $html_total.'<ul class="list-inline-block">'.$html_list.'</ul>'); ?>
</div>
<?php endif;