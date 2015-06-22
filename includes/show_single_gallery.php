<?php

$chill_gall = ''; 

if ($gallery->post_title != ''  && get_option('chilly_show_gallery_title') )   : 
	$chill_gall .= '<h2>' .  $gallery->post_title . '</h2>';
 endif; 



	$chill_gall .= '<style type="text/css">body .images_container img {  ' .  get_option('chilly_image_css') .  '}</style>';
	$chill_gall .= '<ul class="images_container ">';

foreach ($images as $image) : 
	 $thumb = get_post_meta( $image->ID, '_thumbnail_id', true );  
	 $url =  wp_get_attachment_image_src( $thumb, 'full')[0];  ;  
	$chill_gall .= '<li class="image_container"><a title="' .  $image->post_excerpt  . '"  class="fancybox" rel="group_' .  $gallery->ID  . '" href="' .  $url . '">' . wp_get_attachment_image(  $thumb , $thumbnail_size ) .  '</a></li>';

 endforeach; 


	$chill_gall .= '</ul>';

?>