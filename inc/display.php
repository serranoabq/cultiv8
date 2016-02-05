<?php

// Section header
function cultiv8_the_panel_header( $panel ) {
	global $wp_customize;
	
	if( get_theme_mod( 'cultiv8_panel'.$panel.'_hidetitle' ) ) {
		if( is_a( $wp_customize, 'WP_Customize_Manager' ) && $wp_customize->is_preview() ) {
			the_title( '<h2 class="entry-title" style="display:none">', '</h2>' ); 
		}
	} else {
			the_title( '<h2 class="entry-title">', '</h2>' ); 
	}
	return ;
}
