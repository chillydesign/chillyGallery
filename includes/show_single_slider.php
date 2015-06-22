<?php



$chill_gall = ''; 

if ($gallery->post_title != ''  && get_option('wpt_show_gallery_title') )   : 
	$chill_gall .= '<h2>' .  $gallery->post_title . '</h2>';
 endif; 


$chill_gall .= '<div class="chilly_banner"><ul >';

	 foreach ($images as $image) : 
		 $thumb = get_post_meta( $image->ID, '_thumbnail_id', true );  
		 $url =  wp_get_attachment_image_src( $thumb, 'full');
		
		$chill_gall .= '<li class="banner_image" style="background-image:url(\'' . $url[0] . '\');"></li>';

	 endforeach; 


	$chill_gall .= '</ul>';
	$chill_gall .= '<a href="#" class="unslider-arrow prev">&laquo;</a>';
	$chill_gall .= '<a href="#" class="unslider-arrow next">&raquo;</a>';
$chill_gall .= '</div>';


$chill_gall .= '<script>

	var Slideroptions = {
		speed: 500,               //  The speed to animate each slide (in milliseconds)
		delay:  ' .  $this->slider_delay() . ' ,              //  The delay between slide animations (in milliseconds)
		complete: function() {},  //  A function that gets called after every slide animation
		keys: true,               //  Enable keyboard (left, right) arrow shortcuts
		dots: true,               //  Display dot navigation
		fluid: true              //  Support responsive design. May break non-responsive designs
	}

</script>';


?>