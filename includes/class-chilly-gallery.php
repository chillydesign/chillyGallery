<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Chilly_Gallery {

	/**
	 * The single instance of Chilly_Gallery.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token = 'chilly_gallery';

		// Load plugin environment variables
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );




		add_action( 'admin_footer',  array($this, 'my_action2_javascript' ) ); // Write our JS below here
		add_action( 'wp_ajax_my_action2',  array($this, 'my_action2_callback' ) ); // Write our JS below here



		// Add submenu page to menu
		add_action( 'admin_menu' , array( $this, 'add_cgallery_to_menu' ) );



		add_action( 'admin_notices', array( $this, 'add_gallery_name' )  );


		// Load frontend JS & CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		// Load admin JS & CSS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );


		add_action( 'after_setup_theme', array($this, 'add_thumbnail_support_to_theme') );


		#add_action( 'admin_bar_menu', array($this, 'add_chilly_gallery_to_toolbar'), 999 );



		add_shortcode( 'chilly-gallery', array($this, 'cgallery_shortcode') );




		// Load API for generic admin functions
		if ( is_admin() ) {
			$this->admin = new Chilly_Gallery_Admin_API();
		} 

		// Handle localisation
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
	} // End __construct ()

	/**
	 * Wrapper function to register a new post type
	 * @param  string $post_type   Post type name
	 * @param  string $plural      Post type item plural name
	 * @param  string $single      Post type item single name
	 * @param  string $description Description of post type
	 * @return object              Post type class object
	 */
	public function register_post_type ( $post_type = '', $plural = '', $single = '', $description = '' ) {

		if ( ! $post_type || ! $plural || ! $single ) return;

		$post_type = new Chilly_Gallery_Post_Type( $post_type, $plural, $single, $description );

		return $post_type;
	}

	/**
	 * Wrapper function to register a new taxonomy
	 * @param  string $taxonomy   Taxonomy name
	 * @param  string $plural     Taxonomy single name
	 * @param  string $single     Taxonomy plural name
	 * @param  array  $post_types Post types to which this taxonomy applies
	 * @return object             Taxonomy class object
	 */
	public function register_taxonomy ( $taxonomy = '', $plural = '', $single = '', $post_types = array() ) {

		if ( ! $taxonomy || ! $plural || ! $single ) return;

		$taxonomy = new Chilly_Gallery_Taxonomy( $taxonomy, $plural, $single, $post_types );

		return $taxonomy;
	}

	/**
	 * Load frontend CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function enqueue_styles () {

		wp_register_style( $this->_token . '-fancybox', esc_url( $this->assets_url ) . 'css/jquery.fancybox.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-fancybox' );


		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );

		wp_enqueue_style( $this->_token . '-frontend' );



	} // End enqueue_styles ()




	/**
	 * Load frontend Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function enqueue_scripts () {

	
    	wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version, true );
		wp_enqueue_script( $this->_token . '-frontend' );


		wp_register_script( $this->_token . '-masonry', esc_url( $this->assets_url ) . 'js/jquery.masonry' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-masonry' );
		wp_register_script( $this->_token . '-bjqs', esc_url( $this->assets_url ) . 'js/jquery.fancybox' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-bjqs' );
	wp_register_script( $this->_token . '-unslider', esc_url( $this->assets_url ) . 'js/unslider' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
	wp_enqueue_script( $this->_token . '-unslider' );		


		
	} // End enqueue_scripts ()

	/**
	 * Load admin CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_styles ( $hook = '' ) {
		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-admin' );
	} // End admin_enqueue_styles ()

	/**
	 * Load admin Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_scripts ( $hook = '' ) {
		wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'js/admin' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-admin' );
	} // End admin_enqueue_scripts ()

	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'chilly-gallery', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'chilly-gallery';

	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain ()




	public function add_thumbnail_support_to_theme() {
		add_theme_support( 'post-thumbnails' , array('cgallery, cimage') );
	}


	public function default_gallery_type(){
		$slider_or_gallery = get_option('chilly_slider_or_lightbox');
		if (isset($slider_or_gallery)) {
				return $slider_or_gallery;
		} else {
			return 'slider';
		}
	

	}



	public function default_show_gallery_title(){
		$chilly_show_gallery_title = get_option('chilly_show_gallery_title');
		if (isset($chilly_show_gallery_title)) {
				return $chilly_show_gallery_title;
		} else {
			return 'on';
		}
	

	}
	public function default_thumbnail_width(){
		$thumbnail_width = get_option('chilly_thumbnail_width');
		if (isset($thumbnail_width)) {
			return $thumbnail_width;
		} else {
			return 100;
		}
	

	}
	public function default_gallery_delay(){
		$chilly_slider_delay = get_option('chilly_slider_delay');
		if (isset($chilly_slider_delay)) {
				return $chilly_slider_delay;
		} else {
			return 2500;
		}
	

	}







	public function gallery_type($gallery_id) {
		$gallery_type  =  get_post_meta( $gallery_id,  '_gallery_type', true);
		return $gallery_type;
	}

	public function gallery_title($gallery_id) {
		$gallery_title  =  get_post_meta( $gallery_id,  '_gallery_title', true);
		return $gallery_title;
	}

	public function gallery_thumbnail($gallery_id) {
		$gallery_thumbnail  =  get_post_meta( $gallery_id,  '_gallery_thumbnail', true);
		return $gallery_thumbnail;
	}
	public function gallery_delay($gallery_id) {
		$gallery_delay  =  get_post_meta( $gallery_id,  '_gallery_delay', true);
		return $gallery_delay;
	}
	public function gallery_height($gallery_id) {
		$gallery_height  =  get_post_meta( $gallery_id,  '_gallery_height', true);
		return $gallery_height;
	}



	// public function slider_delay(){
	// 	$slider_delay = get_option('chilly_slider_delay');
	// 	if ($slider_delay) {
	// 		return $slider_delay;
	// 	} else {
	// 		return 2500;
	// 	}
	// }




		// [bartag foo="foo-value"]
		public function cgallery_shortcode($atts  ) {


		    $a = shortcode_atts( array(
		        'id' => 0,
		    ), $atts );

		    $id = intval($a['id']);
			$gallery = $this->single_gallery($id);
			$images = $this->all_images($id);
			$slider_height = $gallery->post_content;
			$gallery_type  =  $this->gallery_type($id);
			$gallery_title  =  $this->gallery_title($id);
			$gallery_thumbnail  =  $this->gallery_thumbnail($id);
			$gallery_delay  =  $this->gallery_delay($id);


			if ($gallery_type == '' ) $gallery_type = $this->default_gallery_type();
			global $chill_gall;
			$chill_gall =  '';


			if($id == 0 ){
	            $chill_gall =  '<div class="chilly_alert"><p>No gallery selected. Please add an id to shortcode</p></div>';
	           	
	        } elseif(  $this->count_pictures($id )  == 0  ) {
	        	$chill_gall = '<div class="chilly_alert"><p>No images</p></div>';

	        } else {

	        	  
	        		
	        	if (  $gallery_type == 'masonry') {
						// SHOW THE MASONRY GALLERY
						// load scripts and style for this

						include('show_single_masonry.php');
					

	        	} elseif(  $gallery_type == 'gallery') {
						// SHOW THE GALLERY
						// load scripts and style for this

						include('show_single_gallery.php');

	        	} else { 


					// SHOW THE SLIDER
					// load scripts and style for this

					include('show_single_slider.php');
			
	        		
	        	}






		        
	        }
	      
	        return 	 $chill_gall;



		}




	public function add_gallery_name () {
		global $post;

		if( isset($post) &&  $post->post_type == 'cimage'  ) {


			$gallery = $this->single_gallery($post->post_parent);
			echo '<br/><div class="update-nag">Chilly Gallery: <a href="admin.php?page=' .  $this->_token . '_galleries&amp;id=' .  $gallery->ID . '">Back to ' .  $gallery->post_title  .  '</a></div>';
		}





	}

	

		public function all_galleries(){
			$args = array(
				'post_type'        => 'cgallery',
				'post_status'      => 'publish',
				'posts_per_page' => -1
			);
			$galleries = get_posts( $args );
			return $galleries;
		}


		public function single_gallery($gallery_id){
			$gallery = get_post( $gallery_id ); 
			return $gallery;
		}


		public function count_pictures($gallery_id) {
			return count($this->all_images($gallery_id));
		}



		public function all_images($gallery_id){
			$args = array(
				'post_type'        => 'cimage',
				'post_parent'      => $gallery_id,
				'post_status'      => 'publish',
				'posts_per_page'   => -1,
				'orderby'          => 'menu_order'
			);
			$images = get_posts( $args );
			return $images;
		}


		public function get_thumbnail_size(){

			if (get_option( 'chilly_thumbnail_width' ) != ''  ) {
				$size = get_option( 'chilly_thumbnail_width' );
				$thumbnail_size = array($size, $size);
			} else {
				$thumbnail_size = array(100,100);
			}
			return $thumbnail_size;

		}

	


	public function my_action2_javascript() { ?>
		<script type="text/javascript" >


		jQuery(document).ready(function($) {

				 jQuery(".sortable" ).sortable({
					items: "tr.sortrow",
					handle: ".dragger",
					axis: "y",
					placeholder: "ui-state-highlight",
					connectWith: ".sortable",
					update: function(event, ui) {
						var newOrder = jQuery(this).sortable('toArray' , {attribute: 'data-row' }).toString();


						var data = {
							'chillyOrder': newOrder
						};
			
						// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
						$.post(ajaxurl, data, function(response) {
							console.log( data);
							console.log( response);
							console.log( ajaxurl);
						});

					}

			   });
		});
		</script> <?php
	}





	public function my_action2_callback() {

		echo 'hello';

		global $wpdb; // this is how you get access to the database


			$order =  explode(',', $_POST['chillyOrder']);
			$i = 3;
			foreach ($order as $id) {
				// wp_update_post( array('ID'  => $id, 'menu_order' =>  $i   ) );
				 $i++;
			}

			echo $i;
			



		wp_die(); // this is required to terminate immediately and return a proper response

	}


		// public function show_single_gallery($gallery_id){
		// 	global $chill_gall;	
		// }
		// public function show_single_slider($gallery_id ){
		// 	global $chill_gall;
		// }
		// public function show_single_masonry($gallery_id ){
		// 	global $chill_gall;
		// }






		// public function add_chilly_gallery_to_toolbar( $wp_admin_bar ) {
		// 	$args = array(
		// 		'id'    => 'edit_chilly_gallery',
		// 		'title' => 'Chilly Galleries',
		// 		'href'  =>  admin_url() . 'admin.php?page=' . $this->_token . "_galleries" 
		// 	);
		// 	$wp_admin_bar->add_node( $args );
		// }



		public function redirect($location){
			 if (!headers_sent()){
	          wp_redirect( $location);
	        } else{
	            echo '<meta http-equiv="refresh" content="0;url='.$location.'" />';
	        }	
		}


	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	 public function add_cgallery_to_menu() {


		$page = add_menu_page( 
				__( 'Chilly Gallery', 'chilly-gallery' ) ,
				 __( 'Chilly Gallery', 'chilly-gallery' ) ,
				  'manage_options' ,
				   $this->_token . '_galleries' ,
				   array( $this, 'galleries_page' )

			);



		add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );

	}

		/**
	 * Load galleries page content
	 * LOAD THE ACTUAL GALLERIES PAGE
	 * @return void
	 */
	public function galleries_page () {


		if(isset( $_GET['id'] )) :

			include('admin_single_gallery.php');

		else : # if no id set

			include('admin_list_of_galleries.php');

		endif; # end of if no id set


	 }	// END OF GALLERIES PAGE



	 public function generate_shortcode($gallery_id){
	 	return '[chilly-gallery id=' . $gallery_id  . ']';
	 }



	public function settings_assets () {

		// We're including the farbtastic script & styles here because they're needed for the colour picker
		// If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below
		wp_enqueue_style( 'farbtastic' );
    	wp_enqueue_script( 'farbtastic' );

    	// We're including the WP media scripts here because they're needed for the image upload field
    	// If you're not including an image upload then you can leave this function call out
    	wp_enqueue_media();

    	wp_register_script( $this->_token . '-settings-js', $this->assets_url . 'js/settings' . $this->script_suffix . '.js', array( 'farbtastic', 'jquery' ), '1.0.0' );
    	wp_enqueue_script( $this->_token . '-settings-js' );
	}





	/**
	 * Main Chilly_Gallery Instance
	 *
	 * Ensures only one instance of Chilly_Gallery is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Chilly_Gallery()
	 * @return Main Chilly_Gallery instance
	 */
	public static function instance ( $file = '', $version = '0.2.5' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {
		$this->_log_version_number();
	} // End install ()

	/**
	 * Log the plugin version number.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

}