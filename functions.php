<?php
/**
 * Cultiv8 functions and definitions
 *
 * @package Cultiv8
 */
 

/// Setup the theme
add_action( 'after_setup_theme', 'cultiv8_setup' );
function cultiv8_setup(){
		
	// Translation
	load_theme_textdomain( 'cultiv8', get_template_directory() . '/languages' );
	
	// Add Church Theme Content support
	if( function_exists( 'cultiv8_add_ctc' ) ) 
		cultiv8_add_ctc();

	if( is_user_logged_in() ){
		if( function_exists( 'ctcex_update_recurring_events' ) ) 
			ctcex_update_recurring_events();
	} 
	
	// Setup images
	cultiv8_image_setup();
	
} // cultiv8_setup

/// Enqueue parent & child theme stylesheets
add_action( 'wp_enqueue_scripts', 'cultiv8_styles' );
function cultiv8_styles() {
	wp_enqueue_style( 'pique-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'cultiv8-style', get_stylesheet_uri(), array(), null, 'screen' );
	
	cultiv8_deregister_scripts();
	
} // cultiv8 styles


// Handle scripts that we don't want loaded all the time
function cultiv8_deregister_scripts() {
	global $wp_query, $post;
	
	if( ! ( strpos( json_encode( $wp_query ), '[contact-form-7' ) || strpos( json_encode( $post ), '[contact-form-7' ) ) )  {
			wp_deregister_script( 'contact-form-7' );
			wp_deregister_style( 'contact-form-7' );
	}

} // cultiv8 scripts

// Helper function for theme options
function cultiv8_option( $option, $default = false ) {
	if( class_exists( 'CTC_Extender' )  && ctcex_has_option( $option ) )
		return ctcex_get_option( $option, $default );
	
	return get_theme_mod( $option, $default );
}


/// Load image helpers
require  get_stylesheet_directory() . '/inc/images.php' ;

/// Load display helper
require  get_stylesheet_directory() . '/inc/display.php' ;

/// Load CTC support file.
require  get_stylesheet_directory() . '/inc/ctc-support.php' ;


/// Load Customizer support 
require  get_stylesheet_directory() . '/inc/customizer.php' ;

/// Load Widget support
// require  get_stylesheet_directory() . '/inc/widgets.php' ;

/// Load Feed support
require  get_stylesheet_directory() . '/inc/feeds.php' ;


