<?php
namespace Elementor;
extract($settings);

$wdata->add_render_attribute( 'wrapper', 'class', 'bzoteche-info-box-'.$settings['style'].' item-info-box-global');

?>

<div <?php echo apply_filters('bzotech_output_content', $wdata->get_render_attribute_string('wrapper'));?>>
	
	<?php
	if(!empty($date)){
		echo '<div class="bzotech-countdown flex-wrapper container-flex-e" data-date="'.$date.'">';
		 echo '<div class="clock day flex-wrapper info-container-flex-e"><strong class="number title48 text-semibold item-number-e">%D</strong><sup class="text title20 font-medium item-title-e">'.$day.'</sup></div>';
         echo '<div class="clock hour flex-wrapper info-container-flex-e"><strong class="number title48 text-semibold item-number-e">%H</strong><sup class="text title20 font-medium item-title-e">'.$hour.'</sup></div>';
         echo '<div class="clock min flex-wrapper info-container-flex-e"><strong class="number title48 text-semibold item-number-e">%M</strong><sup class="text title20 font-medium item-title-e">'.$min.'</sup></div>';
       	 echo '<div class="clock sec flex-wrapper info-container-flex-e"><strong class="number title48 text-semibold item-number-e">%S</strong><sup class="text title20 font-medium item-title-e">'.$sec.'</sup></div>';
		echo '</div>';
	}
	?>

</div>
