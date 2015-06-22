<?php
/*
 * Plugin Name: ChillyGallery
 * Version: 0.2.5
 * Plugin URI: http://www.chillydesign.co/
 * Description: A simple Chilly gallery
 * Author: Chilly
 * Author URI: http://www.chillydesign.co/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: chilly-gallery-plugin
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Charles Harvey
 * @since 1.0.0
 */



if ( ! defined( 'ABSPATH' ) ) exit;



// Load plugin class files
require_once( 'includes/class-chilly-gallery.php' );
require_once( 'includes/class-chilly-gallery-settings.php' );

// Load plugin libraries
require_once( 'includes/lib/class-chilly-gallery-admin-api.php' );
require_once( 'includes/lib/class-chilly-gallery-post-type.php' );
require_once( 'includes/lib/class-chilly-gallery-taxonomy.php' );

/**
 * Returns the main instance of Chilly_Gallery to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Chilly_Gallery
 */
function Chilly_Gallery () {
	$instance = Chilly_Gallery::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Chilly_Gallery_Settings::instance( $instance );
	}

	return $instance;
}

Chilly_Gallery();

Chilly_Gallery()->register_post_type(
	'cgallery',
	__( 'Galleries', 'chilly-gallery' ),
	__( 'Gallery', 'chilly-gallery' )
);

Chilly_Gallery()->register_post_type( 
	'cimage', 
	__( 'Images', 'chilly-gallery' ), 
	__( 'Image', 'chilly-gallery' )
);






?>