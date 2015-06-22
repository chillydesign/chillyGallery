<?php

$chill_gall = ''; 

if ($gallery->post_title != ''  && get_option('chilly_show_gallery_title') )   : 
	$chill_gall .= '<h2>' .  $gallery->post_title . '</h2>';
 endif; 

	$chill_gall .= '<style type="text/css">
	body .images_container img { ' . get_option('chilly_image_css') . '}
</style>
<div id="chilly_masonry">';

 foreach ($images as $image) : 
	 $thumb = get_post_meta( $image->ID, '_thumbnail_id', true );  
	 $url =  wp_get_attachment_image_src( $thumb, 'full')[0];  ;  

		$chill_gall .= '<img height="300"  src="' .  $url . '" class="chilly_item" alt="" />';			
 endforeach; 


$chill_gall .= '</div>';



?>