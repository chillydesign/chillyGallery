<?php



if ($gallery->post_title != ''   && $gallery_title != 'no' )   : 
	$chill_gall .= '<h2>' .  $gallery->post_title . '</h2>';
 endif; 

	$chill_gall .= '<div id="chilly_masonry">';

 foreach ($images as $image) : 
	 $thumb = get_post_meta( $image->ID, '_thumbnail_id', true );  
	 $url =  wp_get_attachment_image_src( $thumb, 'full')[0];  ;  
	 $chill_gall .= '<img  src="' .  $url . '" class="chilly_item" alt="" />';			
 endforeach; 


$chill_gall .= '</div>';



?>