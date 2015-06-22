<?php 


	if( isset($_POST['gallerySubmit'] )) {
		$post = array(
		  'post_title'     => $_POST['galleryName'],
		  'post_status'    => 'publish',
		  'post_type'      => 'cgallery'
		);  
		$new_gallery = wp_insert_post( $post, $wp_error );
		$new_galery_type =  add_post_meta($new_gallery, '_gallery_type',  $this->default_gallery_type()  , true);  

		$permalink = "?page=" . $this->_token. "_galleries&amp;id=$new_gallery";
		$this->redirect($permalink);
	} # END OF IF SUBMIT FORM


?>

<div class="wrap" id="<?php echo $this->_token . '_galleries' ?>">
	


	<h2>List of Galleries</h2>
	<table  class="wp-list-table widefat fixed items" cellspacing="0">
	<thead>
		<tr>
			<th>Title</th>
			<th>Shortcode</th>
			<th># Pictures</th>
			<th>Type</th>
			<th>Author</th>
			<th>Created</th>
		</tr>
	</thead>
	<tbody id="the-list">
	<?php 

		foreach ($this->all_galleries() as $gallery) : ?>
		<?php $gallery_id = $gallery->ID; ?>
			<tr>
				<td><a href="?page=<?php echo $this->_token;?>_galleries&amp;id=<?php echo $gallery_id;  ?>"><?php echo $gallery->post_title; ?></a></td>
				<td><?php echo $this->generate_shortcode($gallery_id); ?>    </td>
				<td><?php echo $this->count_pictures($gallery_id); ?>    </td>
				<td><?php echo $this->gallery_type($gallery_id); ?>    </td>
				<td><?php echo get_userdata( $gallery->post_author)->user_nicename; ?></td>
				<td><?php echo  get_the_date( get_option('date_format')   , $gallery_id ); ?></td>
				</tr>
		<?php endforeach; ?>
		</tbody>
		</table>



	<form id="createGallery" method="post" action="" class="feature-filter" style="padding:5px 10px 20px;margin:20px 0">
		<h2>Create a new gallery</h2>
		<label  for="galleryName">Gallery Name</label>
		<input type="text" name="galleryName" id="galleryName" size="30" value=""  autocomplete="off" />
		<input type="submit" name="gallerySubmit"  value="Submit" />
	</form>

	<br/>
	<br/>
	<br/>



</div>



