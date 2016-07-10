<?php

add_filter('widget_text', 'do_shortcode');

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

function cultiv8_link_pages_by_meta( $args, $meta_args ){
	$post_id = get_the_id();
	$post_type = get_post_type();
	
	// Templates
	$nav_template = '
		<nav class="navigation post-navigation" role="navigation">
			<h2 class="screen-reader-text">%1$s</h2>
			<div class="nav-links">%2$s</div>
		</nav>';
	$navlink_template = '<div class="nav-%direction">%link</div>';
	$link_template = '<a href="%url" title="%title">%link_text</a>';
	
	// Parse display arguments
	$defaults = array(
		'prev_text'        => '%title',
		'next_text'        => '%title',
		'screen_reader_text' => __( 'Post navigation' ),
		'echo'             => 1
	);
	$r = wp_parse_args( $args, $defaults );
	
	// Parse meta arguments
	$default_meta_args = array(
		'post_type' 	=> $post_type,
		'posts_per_page' => -1,
		'order' 			=> 'DESC',
		'orderby' 		=> 'meta_value', // Use 'meta_value', 'meta_value_num', 'meta_type'
		'meta_key' 		=> '',
		'meta_type'		=> '',
		'meta_query'	=> '',
	);
	$m = wp_parse_args( $meta_args, $default_meta_args );
	
	// Get the next/previous posts
	$pages = array();
	$nav_posts = get_posts( $m );
	foreach($nav_posts as $nav_post) {
		$pages[] += $nav_post->ID;
	}
	$current = array_search($post_id, $pages);
	$prev = $current - 1;
	$next = $current + 1;
	
	// Prepare the navigation
	$prevlink = '';
	$nextlink = '';
	if( $prev > 0 ) {
		$prevID = $pages[ $prev ];
		$prevlink = str_replace( '%url', get_permalink( $prevID ), $link_template );
		$prevlink = str_replace( '%link_text', $r[ 'prev_text' ], $prevlink );
		$prevlink = str_replace( '%title', get_the_title( $prevID ), $prevlink );
		$prevlink = str_replace( '%link', $prevlink, $navlink_template );
		$prevlink = str_replace( '%direction', 'previous', $prevlink );
	}
	if( $next < count( $pages ) ) {
		$nextID = $pages[ $next ];
		$nextlink = str_replace( '%url', get_permalink( $nextID ), $link_template );
		$nextlink = str_replace( '%link_text', $r[ 'next_text' ], $nextlink );
		$nextlink = str_replace( '%title', get_the_title( $nextID ), $nextlink );
		$nextlink = str_replace( '%link', $nextlink, $navlink_template );
		$nextlink = str_replace( '%direction', 'next', $nextlink );
	}
	$nav_link = sprintf( $nav_template, $r[ 'screen_reader_text' ], $prevlink . $nextlink );

	if( $r[ 'echo' ] ){
		echo $nav_link;
	}
	return $nav_link;
	
}
