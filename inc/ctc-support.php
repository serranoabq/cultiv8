<?php 
// Add Church Theme Content support

function cultiv8_ctc_notice(){
	echo '<div class="error"><p><a href="http://localhost/wp-admin/plugin-install.php?tab=plugin-information&plugin=church-theme-content&TB_iframe=true&width=600&height=550">'. __( 'Church Theme Content Plugin is required!', 'cultiv8' ).'</a></p></div>';
}
function cultiv8_ctcex_notice(){
	echo '<div class="error"><p>'. __( 'CTC_Extender Plugin is required!', 'cultiv8' ).'</p></div>';
}
	
function cultiv8_add_ctc(){
	 
	if( ! class_exists( 'Church_Theme_Content' ) ) {
		add_action( 'admin_notices', 'cultiv8_ctc_notice' );
		return;
	}
	if( ! class_exists( 'CTC_Extender' ) ) {
		add_action( 'admin_notices', 'cultiv8_ctcex_notice' );
	}
	
	add_theme_support( 'church-theme-content' );
	
	// Events
	add_theme_support( 'ctc-events', array(
			'taxonomies' => array(
				'ctc_event_category',
			),
			'fields' => array(
				'_ctc_event_start_date',
				'_ctc_event_end_date',
				'_ctc_event_start_time',
				'_ctc_event_end_time',
				'_ctc_event_recurrence',
				'_ctc_event_recurrence_end_date',
				'_ctc_event_recurrence_period',       // Not default in CTC
				'_ctc_event_recurrence_monthly_type', // Not default in CTC
				'_ctc_event_recurrence_monthly_week', // Not default in CTC 
				'_ctc_event_venue',
				'_ctc_event_address',
			),
			'field_overrides' => array()
	) );
	
	// Sermons
	add_theme_support( 'ctc-sermons', array(
			'taxonomies' => array(
					'ctc_sermon_topic',
					'ctc_sermon_series',
					'ctc_sermon_speaker',
			),
			'fields' => array(
					'_ctc_sermon_video',
					'_ctc_sermon_audio',
			),
			'field_overrides' => array()
	) );
	 
	// People
	add_theme_support( 'ctc-people', array(
			'taxonomies' => array(
					'ctc_person_group',
			),
			'fields' => array(
					'_ctc_person_position',
					'_ctc_person_phone',
					'_ctc_person_email',
					'_ctc_person_urls',
					'_ctc_person_gender',	// Not default in CTC
			),
			'field_overrides' => array()
	) );

	// Locations
	add_theme_support( 'ctc-locations', array(
			'taxonomies' => array(),
			'fields' => array(
					'_ctc_location_address',
					'_ctc_location_phone',
					'_ctc_location_times',
					'_ctc_location_slider',
					'_ctc_location_pastor',
			),
			'field_overrides' => array()
	) );
	
}

// Add default image into the sermon
add_filter( 'ctc_sermon_image', 'cultiv8_sermon_image' );
add_filter( 'ctc_event_image', 'cultiv8_sermon_image' );
function cultiv8_sermon_image( $img ){
	if( empty( $img ) )
		$img = cultiv8_option( 'feed_logo', '' );
	
	// Fall back to the site logo
	if( empty( $img ) )
		$img = cultiv8_option( 'logo', '' );
	
	return $img; 
	
}

// Add a default gender-specific person image defined by the theme
add_filter( 'ctc_person_image', 'cultiv8_person_image', 10, 2 );
function cultiv8_person_image( $img, $gender ){
	// Check if the gender meta is available (added by CTC Extender)
	if( $gender ){
		// Allow a gender-specific default image
		if( file_exists( get_stylesheet_directory() . '/assets/images/user_' . strtolower( $gender ) . '.png' ) )
			$img = get_stylesheet_directory_uri() . '/assets/images/user_' . strtolower( $gender ) . '.png';
	} elseif( file_exists( get_stylesheet_directory() . '/assets/images/user_male.png' ) ) {
		// Get the default user image
		$img = get_stylesheet_directory_uri() . '/assets/images/user_male.png';
	}
	
	return $img; 
}

// This helper is used to get an expression for recurrence
function cultiv8_get_recurrence_note( $post_obj ) {
	if( class_exists( 'CTC_Extender' ) )
		return ctcex_get_recurrence_note ( $post_obj );
	else
		return '';
}

function cultiv8_get_default_data( $post_id ) {
	$data = array(
		'permalink'   => get_permalink( $post_id ),
		'name'        => get_the_title( $post_id ),
	);
	return $data;
}

// Get sermon data for use in templates
function cultiv8_get_sermon_data( $post_id ){
	$default_img = cultiv8_option( 'feed_logo', '');
	if( empty( $default_img ) ) $default_img = cultiv8_option( 'logo', '' );
	if( class_exists( 'CTC_Extender' ) )		
		return ctcex_get_sermon_data( $post_id, $default_img );
	else
		return cultiv8_get_default_data( $post_id ); 
}

// Get event data for use in templates
function cultiv8_get_event_data( $post_id ){
	if( class_exists( 'CTC_Extender' ) )
		return ctcex_get_event_data( $post_id );
	else
		return cultiv8_get_default_data( $post_id ); 
}

// Get location data for use in templates
function cultiv8_get_location_data( $post_id ){
	if( class_exists( 'CTC_Extender' ) )
		return ctcex_get_location_data( $post_id );
	else
		return cultiv8_get_default_data( $post_id ); 
}

// Get person data for use in templates
function cultiv8_get_person_data( $post_id ){
	if( class_exists( 'CTC_Extender' ) )
		return ctcex_get_person_data( $post_id );
	else
		return cultiv8_get_default_data( $post_id ); 
}

add_action( 'admin_init', 'cultiv8_metabox_location_slider' , 11);
add_action( 'admin_enqueue_scripts', 'cultiv8_metabox_location_slider', 11 );
function cultiv8_metabox_location_slider() {
	$meta_box = array(

		// Meta Box
		'id'        => 'ctc_location_slider', // unique ID
		'title'     => __( 'Slider ', 'cultiv8' ),
		'post_type' => 'ctc_location',
		'context'   => 'side', 
		'priority'  => 'low', 

		// Fields
		'fields' => array(
			'_ctc_location_slider' => array(
				'name'       => __( 'Location slider', 'cultiv8' ),
				'desc'       => __( 'Enter the shortcode for the slider to use instead of the image (e.g., <code>[metaslider id=1]</code>).', 'cultiv8' ), 
				'type'       => 'text', 
				'default'    => '', 
				'no_empty'   => false, 
				'class'      => 'ctmb-medium', // class(es) to add to input (try ctmb-medium, ctmb-small, ctmb-tiny)
				'field_class'   => '', // class(es) to add to field container
			),
		),
	);
	
	// Add Meta Box
	if( class_exists( 'CT_Meta_Box' ) )
		new CT_Meta_Box( $meta_box );
}

function cultiv8_the_event_details( $post_id, $glyph = 'fa' ){
	echo cultiv8_get_event_details( $post_id, $glyph );
}

function cultiv8_get_event_details( $post_id, $glyph = 'fa' ){
	$classes = array(
		'container'  => 'ctcex-events-container',
		'media'      => 'ctcex-event-media',
		'details'    => 'ctcex-event-details',
		'date'       => 'ctcex-event-date',
		'time'       => 'ctcex-event-time',
		'location'   => 'ctcex-event-location',
		'categories' => 'ctcex-event-categories',
		'img'        => 'ctcex-event-img'
	);
	$title 		= get_the_title( $post_id ) ;
	$url 			= get_permalink( $post_id );
	$data 		= cultiv8_get_event_data( $post_id );

	// Event date
	$date_str = sprintf( '%s%s',  date_i18n( 'l, F j', strtotime( $data[ 'start' ] ) ), $data[ 'start' ] != $data[ 'end' ] ? ' - '. date_i18n( 'l, F j', strtotime( $data[ 'end' ] ) ) : '' );
	$date_src = sprintf( 
		'<div class="%s"><i class="%s %s"></i> %s</div>', 
		$classes[ 'date' ], 
		$glyph === 'gi' ? 'genericon' : 'fa', 
		$glyph === 'gi' ? 'genericon-month' : 'fa-calendar', 
		$date_str );
	
	// Event time
	$time_str = sprintf( '%s%s',  $data[ 'time' ], $data[ 'endtime' ] ? ' - '. $data[ 'endtime' ] : '' );
	$time_src = '';
	if( $time_str ) {
		$time_src = sprintf( 
			'<div class="%s"><i class="%s %s"></i> %s</div>', 
			$classes[ 'time' ], 
			$glyph === 'gi' ? 'genericon' : 'fa', 
			$glyph === 'gi' ? 'genericon-time' : 'fa-clock-o', 
			$time_str );
	}
	
	// Event location
	$location_txt = $data[ 'venue' ] ? $data[ 'venue' ] : $data[ 'address' ];
	$location_src = '';
	if( $location_txt ) {
		$location_src = sprintf( 
			'<div class="%s"><i class="%s %s"></i> %s</div>', 
			$classes[ 'location' ], 
			$glyph === 'gi' ? 'genericon' : 'fa', 
			$glyph === 'gi' ? 'genericon-location' : 'fa-map-marker', 
			$location_txt );
	}
	
	// Event categories
	$categories_src = '';
	if( $data[ 'categories' ] ) {
		$categories_src = sprintf( 
			'<div class="%s"><i class="%s %s-tag"></i> %s</div>', 
			$classes[ 'location' ], 
			$glyph === 'gi' ? 'genericon' : 'fa', 
			$glyph === 'gi' ? 'genericon' : 'fa', 
			$data[ 'categories' ] );
	}
	
	// Get image
	$img_src = $data[ 'img' ] ? sprintf( 
		'%s
			<img class="%s" src="%s" alt="%s"/>
		%s', 
		$data[ 'map_used' ] ? '<a href="' . $data[ 'map_url' ] . '" target="_blank">' : '',
		$classes[ 'img' ], 
		$data[ 'img' ], 
		get_the_title(),
		'</a>' 
	) : '' ;
	
	$edit_link = get_edit_post_link( $post_id, 'link' );
	$edit_link = $edit_link ? sprintf( '<a href="%s" class="alignright">%s</a>',
			$edit_link, 
			__( 'Edit event', 'ctcex' )
			) : '';
	
	// Prepare output
	$item_output = sprintf(
		'<div class="%s">
			<div class="%s">%s</div>
			<div class="%s">
				%s
				%s
				%s
				%s
				%s
			</div>
		</div>
		', 
		$classes[ 'container' ],
		$classes[ 'media' ],
		$img_src,
		$classes[ 'details' ],
		$date_src,
		$time_src,
		$location_src,
		$categories_src,
		$edit_link
	);
	
	return '<div id="ctcex-events" class="ctcex-events-list">' . $item_output . '</div>';
}
