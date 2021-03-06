<?php
/**
 * Cultiv8  image-related functions and definitions
 *
 * @package Cultiv8
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

// Removes default thumbnail width/height attributes
add_filter( 'post_thumbnail_html', 'cultiv8_remove_thumbnail_dimensions', 10 );  
add_filter( 'image_send_to_editor', 'cultiv8_remove_thumbnail_dimensions', 10 ); 
function cultiv8_remove_thumbnail_dimensions( $html ) {     
	$html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );   
	return $html; 
} 

// Setup additional image dimensions
function cultiv8_image_setup(){
	add_image_size( 'ctc-wide', 960, 540 ); // 16x9
	add_image_size( 'ctc-tall', 540, 960 ); // 9x16
	add_image_size( 'ctc-person-tall', 685, 960 ); // 5x7
	add_image_size( 'ctc-person-tall', 960, 685 ); // 7x5
	
	// Recommend a thumbnail flush after installing theme to regenerate
}

// Get image on post
function cultiv8_getImage( $post_id = null, $size = 'large' ){
	if( null == $post_id ) {
		global $post;	
		$post_id = $post -> ID;
	}	
	
	// Check for a CTC image
	$img = get_post_meta( $post_id, '_ctc_image' , true );  
	
	// Fall back to the post thumbnail
	if( empty( $img ) ) {
		$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );
		if( $thumbnail ) $img = $thumbnail[0];
	}
	
	// Fall back to the site feed logo
	if( empty( $img ) )
		$img = get_theme_mod( 'cultiv8_feed_logo', '' );
	
	// Fall back to the site logo
	if( empty( $img ) )
		$img = cultiv8_get_site_logo();
			
	return $img;
	
}

// Site logo 
function cultiv8_get_site_logo() {
	if( function_exists( 'get_custom_logo' ) ){
		// Try to use WP4.5 built in site logo feature
		return wp_get_attachment_image_url( get_theme_mod( 'custom_logo'), 'full' );
		
	} elseif( function_exists( 'jetpack_get_site_logo' ) ){
		// If not available, try Jetpack's site logo feature 
		return jetpack_get_site_logo();
	
	} else {
		// Last resort, try our own feature
		if( get_theme_mod( 'cultiv8_site_logo' ) ) {
			
			return get_theme_mod( 'cultiv8_site_logo' );
			
		} else {
			
			return '';
			
		}
	}
}

// Site logo
function cultiv8_the_site_logo() {
	global $wp_customize;
	$url = cultiv8_get_site_logo();
		
	if( $url ) {
		// Output image HTML
		$html = sprintf( '<a href="%1$s" class="site-logo-link" rel="home" itemprop="url">' . 
			'<img src="%2$s" class="site-logo attachment-pique-logo" itemprop="logo" data-size="pique-logo" width="100" height="100"/></a>',
			esc_url( home_url( '/' ) ),
			$url 
		);
		echo $html;
	
	} else { 
	
		// Generate placeholder  for the customizer
		if( is_a( $wp_customize, 'WP_Customize_Manager' ) && $wp_customize->is_preview() ) {
			$html = sprintf( '<a href="%1$s" class="site-logo-link" style="display:none">' . '
				<img class="site-logo attachment-pique-logo" data-size="pique-logo"  width="100" height="100"/></a>',
				esc_url( home_url( '/' ) )
			);
			echo $html;
		} 
		
	}

	return ;
}
