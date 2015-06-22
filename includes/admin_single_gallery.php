<?php  $gallery_id = $_GET['id']; ?>
<?php


	# IF GALLERY IS UPDATED
	if( isset($_POST['update_gallery'] )) {

		$update = array(
		      'ID'           => $gallery_id,
		      'post_title' => $_POST['gallery_title'],
		      'post_content' => $_POST['slider_height']
		  );

		// Update the post into the database
		wp_update_post( $update );
		update_post_meta($gallery_id, '_gallery_type',  $_POST['chilly_slider_or_lightbox']);  


	}; # END OF UPDATE GALLERY



	# IF ADDING IMAGE TO GALLERY
	if( isset($_POST['new_gallery_image_submit'] )) {

		foreach ($_POST as $field => $image_id) {


			# IF IT IS AN IMAGE TO BE UPLAODED
			if (strpos($field,'cgallery_img_id_row') !== false) {

				$post = array(
				  'post_title'     => 'Image ' . $image_id,
				  'post_status'    => 'publish',
				  'post_type'      => 'cimage',
				  'post_parent'	   => $gallery_id
				);  
				 $new_image = wp_insert_post( $post, $wp_error );
				 $new_image_meta =  add_post_meta($new_image, '_thumbnail_id', $image_id, true);  

			};

		}

		// REDIRECT TO IMAGE AFTER UPLOAD
		// $permalink = get_edit_post_link( $new_image);
		// $this->redirect($permalink);


	} # END OF IF SUBMIT FORM



?>
<?php 	$gallery = $this->single_gallery($gallery_id); ?>
<?php   $gallery_type = $this->gallery_type($gallery_id); ?>

<div class="wrap" id="<?php echo $this->parent->_token . '_galleries' ?>">

<?php if (count($gallery) == 0) : # if no gallery with this id ?>
	
	<h1>No Gallery here</h1>

<?php  else : ?>

<div id="poststuff">
<div id="post-body" class="metabox-holder columns-2">

	<div id="post-body-content">

	<h1>Gallery: <?php echo $gallery->post_title; ?></h1><br/>


	<div class="feature-filter" style="padding:5px 10px 20px;">
		<h2>Upload a new image</h2>
		<form method="post" action="" enctype="multipart/form-data">
	       <!--  <img id="new_gallery_image_preview" class="image_preview" src="" /><br/> -->
	        <div id="image_count_notice"></div>
			<input id="new_gallery_image_button" type="button" data-uploader_title="<?php echo  __( 'Upload an image' , 'chilly-gallery' ); ?> " data-uploader_button_text="' <?php echo  __( 'Use image' , 'chilly-gallery' ); ?> " class="image_upload_button button" value="<?php echo  __( 'Upload new image' , 'chilly-gallery' ); ?> " />
			<input id="new_gallery_image" class="image_data_field" type="hidden" name="new_gallery_image" value="' . $data . '"/>
			<input id="new_gallery_image_submit" type="submit" name="new_gallery_image_submit" class="button-primary" value="Add to gallery"/><br/>
		</form>
	</div>



	<h2>List of images</h2>
	<table  class="wp-list-table widefat fixed items" cellspacing="0">
	<thead>
		<tr>
			<th style="width:1px"></th>
			<th style="width:100px">Image</th>
			<th>Title</th>
			<th>Author</th>
			<th>Created</th>
		</tr>
	</thead>
	<tbody id="the-list" class="sortable" >
	<?php 

		foreach ($this->all_images($gallery_id) as $image) : ?>

			<?php $image_id  =  $image->ID;  ?>
			<?php $thumb = get_post_meta( $image_id, '_thumbnail_id', true );  ?>
			<?php $edit_link =  get_edit_post_link($image_id); ?>
			<tr class="sortrow" data-row="<?php echo $image_id; ?>" >
				<td class="dragger"></td>
				<td>
					<a href="<?php echo $edit_link  ?>"><?php echo wp_get_attachment_image(  $thumb, [100,100] ); ?></a>
				</td>
				<td>
					<a href="<?php  echo $edit_link  ?>">
						<?php echo $image->post_title  ?>
						</a><br/>
					<?php if ($image->post_excerpt != '') : ?>
 					<span class="description">
					<?php echo substr($image->post_excerpt, 0 , 200) . '....'; ?>
					</span>
					<?php endif; ?>
					<div class="row-actions">
						<span class="edit">
							<a href="<?php  echo $edit_link; ?>">Edit</a> |
						</span>
						<span class="trash">
							<a class="submitdelete" href="<?php echo get_delete_post_link($image_id); ?>">Trash</a>
						</span>
					</div>
				</td>
				<td><?php echo  get_userdata( $image->post_author)->user_nicename; ?></td>
				<td><?php echo  get_the_date( get_option('date_format')   , $image_id ); ?></td>
				</tr>
		<?php endforeach; ?>
		</tbody>
		</table>

		<div class="feature-filter" style="padding:15px;margin:20px 0">
	<a class="button-primary" style="background:#c00;border-color:#700;" href="<?php echo get_delete_post_link($gallery_id); ?>">Trash Gallery</a>
	</div>
	</div> <!-- END OF post-body-content -->

	<div id="postbox-container-1" class="postbox-container">
	
		<div class="postbox">
		<h3 class="hndle"><span>Settings</span></h3>
		<div class="inside">
		<!-- 	<h2>Gallery: <a href="?page=<?php echo $this->parent->_token;?>_galleries" class="add-new-h2">Add New</a></h2> -->
		<form method="post" action="" >
		<p><label  for="gallery_title">Gallery Name</label>
		<input type="text" name="gallery_title" size="30" value="<?php echo $gallery->post_title; ?>" id="gallery_title" autocomplete="off" /></p>

		<p>
		<label>Gallery Format</label><br/>


		<?php foreach (['slider', 'gallery', 'masonry'] as $type) {
			$checked = ( $type == $gallery_type  ) ?  ' checked="checked" ' : '';
			echo '<label for="slider_or_lightbox_' . $type . '">';
			echo '<input type="radio"  name="chilly_slider_or_lightbox" value="' . $type . '" id="slider_or_lightbox_' . $type . '" ' . $checked . ' />' . ucfirst($type) . '</label> ';
		}
		?>





		</p>


		<?php if( $this->default_gallery_type() == 'slider' ): ?>
		<p>
		<label  for="slider_height">Height of Slider (px)</label>
		<input type="text" name="slider_height" size="30" value="<?php echo $gallery->post_content; ?>" id="slider_height" autocomplete="off" /></p>
		<?php endif; ?>

	

		<input name="update_gallery" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="Update gallery" />
		</form>			
		</div>
		</div>



		<div class="postbox">
		<h3 class="hndle"><span>Shortcode</span></h3>
		<div class="inside">
		<label for="gallery_shortcode">Shortcode</label>
		<input id="gallery_shortcode" type="text"  value="<?php echo $this->generate_shortcode($gallery_id); ?>" />
		</div>
		</div>



	</div> <!-- END OF postbox-container -->
	</div> <!-- END OF post-body -->
	</div><!--  END OF poststuff -->

<?php endif #if there is a gallery with this id ?>

</div>
