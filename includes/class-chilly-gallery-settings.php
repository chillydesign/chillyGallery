<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Chilly_Gallery_Settings {

	/**
	 * The single instance of Chilly_Gallery_Settings.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = array();

	public function __construct ( $parent ) {
		$this->parent = $parent;

		$this->base = 'chilly_gallery_';

		// Initialise settings
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register plugin settings
		add_action( 'admin_init' , array( $this, 'register_settings' ) );

		// Add settings page to menu
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_settings_link' ) );
	}

	/**
	 * Initialise settings
	 * @return void
	 */
	public function init_settings () {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function add_menu_item () {
		//$page = add_menu_page( __( 'Chilly Gallery', 'chilly-gallery' ) , __( 'Chilly Gallery', 'chilly-gallery' ) , 'manage_options' , $this->parent->_token . '_settings' ,  array( $this, 'settings_page' ) );


		$subpage = add_submenu_page(  
			$this->parent->_token . '_galleries', 
			'Settings', 
			'Settings' , 
			'manage_options' ,
			$this->parent->_token . '_settings',
			 array( $this, 'settings_page' )
		); 

		add_action( 'admin_print_styles-' . $subpage, array( $this, 'settings_assets' ) );

	}

	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function settings_assets () {

		// We're including the farbtastic script & styles here because they're needed for the colour picker
		// If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below
		wp_enqueue_style( 'farbtastic' );
    	wp_enqueue_script( 'farbtastic' );

    	// We're including the WP media scripts here because they're needed for the image upload field
    	// If you're not including an image upload then you can leave this function call out
    	wp_enqueue_media();

    	wp_register_script( $this->parent->_token . '-settings-js', $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js', array( 'farbtastic', 'jquery' ), '1.0.0' );
    	wp_enqueue_script( $this->parent->_token . '-settings-js' );
	}

	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link ( $links ) {
		$settings_link = '<a href="admin.php?page=' . $this->parent->_token . '_settings">' . __( 'Settings', 'chilly-gallery' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}

	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields () {

		$settings['standard'] = array(
			'title'					=> __( 'Default', 'chilly-gallery' ),
			'description'			=> __( 'Defaults for the galleries.', 'chilly-gallery' ),
			'fields'				=> array(
				array(
					'id' 			=> 'thumbnail_width',
					'label'			=> __( 'Thumbnail size' , 'chilly-gallery' ),
					'description'	=> __( 'The size in pixels of the thumbnails. Default is 100px', 'chilly-gallery' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'Thumbnail size', 'chilly-gallery' )
				),
	
				array(
					'id' 			=> 'show_gallery_title',
					'label'			=> __( 'Show gallery title', 'chilly-gallery' ),
					'description'	=> __( 'Show gallery title when gallery is shown', 'chilly-gallery' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),			
				array(
					'id' 			=> 'type',
					'label'			=> __( 'Default Gallery Format', 'chilly-gallery' ),
					'description'	=> __( 'Should it be a slider, lightbox or a masonry gallery.', 'chilly-gallery' ),
					'type'			=> 'radio',
					'options'		=> array( 'slider' => 'Slider', 'gallery' => 'Gallery' , 'masonry' => 'Masonry' ),
					'default'		=> 'slider'
				),
				array(
					'id' 			=> 'slider_delay',
					'label'			=> __( 'Slider delay (ms)' , 'chilly-gallery' ),
					'description'	=> __( 'Delay between transitions (ms)', 'chilly-gallery' ),
					'type'			=> 'text',
					'default'		=> '2500',
					'placeholder'	=> __( 'Slider Delay', 'chilly-gallery' )
				)



				// array(
				// 	'id' 			=> 'password_field',
				// 	'label'			=> __( 'A Password' , 'chilly-gallery' ),
				// 	'description'	=> __( 'This is a standard password field.', 'chilly-gallery' ),
				// 	'type'			=> 'password',
				// 	'default'		=> '',
				// 	'placeholder'	=> __( 'Placeholder text', 'chilly-gallery' )
				// ),
				// array(
				// 	'id' 			=> 'secret_text_field',
				// 	'label'			=> __( 'Some Secret Text' , 'chilly-gallery' ),
				// 	'description'	=> __( 'This is a secret text field - any data saved here will not be displayed after the page has reloaded, but it will be saved.', 'chilly-gallery' ),
				// 	'type'			=> 'text_secret',
				// 	'default'		=> '',
				// 	'placeholder'	=> __( 'Placeholder text', 'chilly-gallery' )
				// ),
				// array(
				// 	'id' 			=> 'text_block',
				// 	'label'			=> __( 'A Text Block' , 'chilly-gallery' ),
				// 	'description'	=> __( 'This is a standard text area.', 'chilly-gallery' ),
				// 	'type'			=> 'textarea',
				// 	'default'		=> '',
				// 	'placeholder'	=> __( 'Placeholder text for this textarea', 'chilly-gallery' )
				// ),

				// array(
				// 	'id' 			=> 'select_box',
				// 	'label'			=> __( 'A Select Box', 'chilly-gallery' ),
				// 	'description'	=> __( 'A standard select box.', 'chilly-gallery' ),
				// 	'type'			=> 'select',
				// 	'options'		=> array( 'drupal' => 'Drupal', 'joomla' => 'Joomla', 'wordpress' => 'WordPress' ),
				// 	'default'		=> 'wordpress'
				// ),
				// array(
				// 	'id' 			=> 'radio_buttons',
				// 	'label'			=> __( 'Some Options', 'chilly-gallery' ),
				// 	'description'	=> __( 'A standard set of radio buttons.', 'chilly-gallery' ),
				// 	'type'			=> 'radio',
				// 	'options'		=> array( 'superman' => 'Superman', 'batman' => 'Batman', 'ironman' => 'Iron Man' ),
				// 	'default'		=> 'batman'
				// ),
				// array(
				// 	'id' 			=> 'multiple_checkboxes',
				// 	'label'			=> __( 'Some Items', 'chilly-gallery' ),
				// 	'description'	=> __( 'You can select multiple items and they will be stored as an array.', 'chilly-gallery' ),
				// 	'type'			=> 'checkbox_multi',
				// 	'options'		=> array( 'square' => 'Square', 'circle' => 'Circle', 'rectangle' => 'Rectangle', 'triangle' => 'Triangle' ),
				// 	'default'		=> array( 'circle', 'triangle' )
				// )
			)
		);

		// $settings['extra'] = array(
		// 	'title'					=> __( 'Extra', 'chilly-gallery' ),
		// 	'description'			=> __( 'These are some extra input fields that maybe aren\'t as common as the others.', 'chilly-gallery' ),
		// 	'fields'				=> array(
		// 		array(
		// 			'id' 			=> 'number_field',
		// 			'label'			=> __( 'A Number' , 'chilly-gallery' ),
		// 			'description'	=> __( 'This is a standard number field - if this field contains anything other than numbers then the form will not be submitted.', 'chilly-gallery' ),
		// 			'type'			=> 'number',
		// 			'default'		=> '',
		// 			'placeholder'	=> __( '42', 'chilly-gallery' )
		// 		),
		// 		array(
		// 			'id' 			=> 'colour_picker',
		// 			'label'			=> __( 'Pick a colour', 'chilly-gallery' ),
		// 			'description'	=> __( 'This uses WordPress\' built-in colour picker - the option is stored as the colour\'s hex code.', 'chilly-gallery' ),
		// 			'type'			=> 'color',
		// 			'default'		=> '#21759B'
		// 		),
		// 		array(
		// 			'id' 			=> 'an_image',
		// 			'label'			=> __( 'An Image' , 'chilly-gallery' ),
		// 			'description'	=> __( 'This will upload an image to your media library and store the attachment ID in the option field. Once you have uploaded an imge the thumbnail will display above these buttons.', 'chilly-gallery' ),
		// 			'type'			=> 'image',
		// 			'default'		=> '',
		// 			'placeholder'	=> ''
		// 		),
		// 		array(
		// 			'id' 			=> 'multi_select_box',
		// 			'label'			=> __( 'A Multi-Select Box', 'chilly-gallery' ),
		// 			'description'	=> __( 'A standard multi-select box - the saved data is stored as an array.', 'chilly-gallery' ),
		// 			'type'			=> 'select_multi',
		// 			'options'		=> array( 'linux' => 'Linux', 'mac' => 'Mac', 'windows' => 'Windows' ),
		// 			'default'		=> array( 'linux' )
		// 		)
		// 	)
		// );

		// A THIRD PAGE IF NECCESSARY
		$settings['HTML'] = array(
			'title'					=> __( 'HTML customisation', 'chilly-gallery' ),
			'description'			=> __( 'Customise the output of the HTML', 'chilly-gallery' ),
			'fields'				=> array(
				array(
					'id' 			=> 'list_class',
					'label'			=> __( 'Class name of list' , 'chilly-gallery' ),
					'description'	=> __( 'What should the class of the list be', 'chilly-gallery' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'class name of list', 'chilly-gallery' )
				),
				array(
					'id' 			=> 'image_class',
					'label'			=> __( 'Class name of image' , 'chilly-gallery' ),
					'description'	=> __( 'What should the class of the image be', 'chilly-gallery' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'class name of image', 'chilly-gallery' )
				),
				array(
					'id' 			=> 'image_css',
					'label'			=> __( 'Additional image CSS' , 'chilly-gallery' ),
					'description'	=> __( 'Add any additonal CSS rules for the image here.', 'chilly-gallery' ),
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> __( 'CSS rules for image', 'chilly-gallery' )
				)


			)
		);



		$settings = apply_filters( $this->parent->_token . '_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings () {
		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab
			$current_section = '';
			if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
				$current_section = $_POST['tab'];
			} else {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = $_GET['tab'];
				}
			}

			foreach ( $this->settings as $section => $data ) {

				if ( $current_section && $current_section != $section ) continue;

				// Add section to page
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->parent->_token . '_settings' );

				foreach ( $data['fields'] as $field ) {

					// Validation callback for field
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field
					$option_name = $this->base . $field['id'];
					register_setting( $this->parent->_token . '_settings', $option_name, $validation );

					// Add field to page
					add_settings_field( $field['id'], $field['label'], array( $this->parent->admin, 'display_field' ), $this->parent->_token . '_settings', $section, array( 'field' => $field, 'prefix' => $this->base ) );
				}

				if ( ! $current_section ) break;
			}
		}
	}

	public function settings_section ( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	 * Load settings page content
	 * @return void
	 */
	public function settings_page () {

		// Build page HTML
		$html = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
			$html .= '<h2>' . __( 'Plugin Settings' , 'chilly-gallery' ) . '</h2>' . "\n";

			$tab = '';
			if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
				$tab .= $_GET['tab'];
			}

			// Show page tabs
			if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

				$html .= '<h2 class="nav-tab-wrapper">' . "\n";

				$c = 0;
				foreach ( $this->settings as $section => $data ) {

					// Set tab class
					$class = 'nav-tab';
					if ( ! isset( $_GET['tab'] ) ) {
						if ( 0 == $c ) {
							$class .= ' nav-tab-active';
						}
					} else {
						if ( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) {
							$class .= ' nav-tab-active';
						}
					}

					// Set tab link
					$tab_link = add_query_arg( array( 'tab' => $section ) );
					if ( isset( $_GET['settings-updated'] ) ) {
						$tab_link = remove_query_arg( 'settings-updated', $tab_link );
					}

					// Output tab
					$html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

					++$c;
				}

				$html .= '</h2>' . "\n";
			}

			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

				// Get settings fields
				ob_start();
				settings_fields( $this->parent->_token . '_settings' );
				do_settings_sections( $this->parent->_token . '_settings' );
				$html .= ob_get_clean();

				$html .= '<p class="submit">' . "\n";
					$html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'chilly-gallery' ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";
			$html .= '</form>' . "\n";
		$html .= '</div>' . "\n";

		echo $html;
	}

	/**
	 * Main Chilly_Gallery_Settings Instance
	 *
	 * Ensures only one instance of Chilly_Gallery_Settings is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Chilly_Gallery()
	 * @return Main Chilly_Gallery_Settings instance
	 */
	public static function instance ( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __wakeup()

}