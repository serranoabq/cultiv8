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

function cultiv8_get_option( $option, $default = '' ){
	if( class_exists( 'CTC_Extender' ) )
		return ctcex_get_option( $option, $default );
	else {
		$out = get_option( $option, $default );
		return $out;
	}
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
		$data[ 'map_used' ] ? '</a>' : ''
	) : '' ;
	
	$names = cultiv8_get_option( 'ctc-events', __( 'Events/Event', 'cultiv8' ) );
	$plural_name = explode( '/', strtolower( $names ) );
	$single_name = array_pop( $plural_name );
	
	// Prepare output
	$item_output = sprintf(
		'<div class="%s">
			<div class="%s">%s</div>
			<div class="%s">
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
		$categories_src
	);
	
	echo '<div id="ctcex-events" class="ctcex-events-list">' . $item_output . '</div>';
}

function cultiv8_the_sermon_details( $post_id, $glyph = 'fa' ){
	$classes = array(
		'container'  => 'ctcex-sermon-container',
		'media'      => 'ctcex-sermon-media',
		'details'    => 'ctcex-sermon-details',
		'date'       => 'ctcex-sermon-date',
		'speaker'    => 'ctcex-sermon-speaker',
		'series'     => 'ctcex-sermon-series',
		'topic'      => 'ctcex-sermon-topic',
		'audio-link' => 'ctcex-sermon-audio-link',
		'audio'      => 'ctcex-sermon-audio',
		'video'      => 'ctcex-sermon-video',
		'img'        => 'ctcex-sermon-img'
	);
	$title 		= get_the_title( $post_id ) ;
	$data 		= cultiv8_get_sermon_data( $post_id );

	// Sermon date
	$date_src = sprintf( '<div class="%s"><b>%s:</b> %s</div>', $classes[ 'date' ], __( 'Date', 'ctcex' ), get_the_date() );
	
	// Get speaker
	$speaker_src = $data[ 'speakers' ] ? sprintf( '<div class="%s"><b>%s:</b> %s</div>', $classes[ 'speaker' ], __( 'Speaker', 'ctcex' ), $data[ 'speakers' ] ) : '';
	
	// Get series
	$series_src = $data[ 'series' ] ?	sprintf( '<div class="%s"><b>%s:</b> <a href="%s">%s</a></div>', $classes[ 'series' ],  __( 'Series', 'ctcex' ), $data[ 'series_link' ], $data[ 'series' ] ) : '';
	
	// Get topics
	// Topic name
	$topic_name = explode( '/', cultiv8_get_option( 'ctc-sermon-topic' , __( 'Topic', 'ctcex') ) );
	$topic_name = ucfirst( array_pop(  $topic_name ) );
	$topic_src = $data[ 'topic' ] ? sprintf( '<div class="%s"><b>%s:</b> <a href="%s">%s</a></div>', $classes[ 'topic' ], $topic_name, $data[ 'topic_link' ], $data[ 'topic' ] ) : '';

	// Get audio link
	$audio_link_src = $data[ 'audio' ] ? sprintf( '<div class="%s"><b>%s:</b> <a href="%s">%s</a></div>', $classes[ 'audio-link' ], __( 'Audio', 'ctcex' ), $data[ 'audio' ], __( 'Download audio', 'ctcex' ) ) : '';
	
	// Get audio display
	$audio_src = $data[ 'audio' ] ? sprintf( '<div class="%s">%s</div>', $classes[ 'audio' ], wp_audio_shortcode( array( 'src' => $data[ 'audio' ] ) ) ) : '';
	
	// Get video display
	$video_iframe_class = strripos( $data[ 'video' ], 'iframe' ) ? 'iframe-container' : '';
	$video_src = $data[ 'video' ] ? sprintf( '<div class="%s %s">%s</div>', $classes[ 'video' ], $video_iframe_class, $video_iframe_class ? $data[ 'video' ] : wp_video_shortcode( array( 'src' => $data[ 'video' ] ) ) ) : '';
	
	// Use the image as a placeholder for the video
	$img_overlay_class = $data[ 'video' ] && $data[ 'img' ] ? 'ctcex-overlay' : '';
	$img_overlay_js = $img_overlay_class ? sprintf(
		'<div class="ctcex-overlay">
			<i class="' . ( $glyph === 'gi' ? 'genericon genericon-play' : 'fa fa-play' ) . '"></i>
		</div>
		<script>
			jQuery(document).ready( function($) {
				$( ".%s" ).css( "position", "relative" );
				$( ".ctcex-overlay" ).css( "cursor", "pointer" );
				var vid_src = \'%s\';
				vid_src = vid_src.replace( "autoPlay=false", "autoPlay=true" );
				$( ".ctcex-overlay" ).click( function(){
					$( this ).hide();
					$( ".ctcex-sermon-img" ).fadeOut( 200, function() {
						$( this ).replaceWith( vid_src );
					});
				} );
			})
		</script>', 
		$classes[ 'media' ],
		$video_src ) : '' ;
		
	// Get image
	$img_src = $data[ 'img' ] ? sprintf( '%s<img class="%s" src="%s" alt="%s"/>', $img_overlay_js, $classes[ 'img' ], $data[ 'img' ], get_the_title() ) : '';
	$video_src = $img_overlay_class ? $img_src : $video_src;
	
	$img_video_output = $video_src ? $video_src : $img_src . $audio_src;
	
	$names = cultiv8_get_option( 'ctc-sermons', __( 'sermons/sermon', 'cultiv8' ) );
	$plural_name = explode( '/', strtolower( $names ) );
	$single_name = array_pop( $plural_name );
	
	// Prepare output
	$item_output =sprintf(
		'<div class="%s">
			<div class="%s">%s</div>
			<div class="%s">
				%s
				%s
				%s
				%s
				%s
			</div>
		', 
		$classes[ 'container' ],
		$classes[ 'media' ],
		$img_video_output,
		$classes[ 'details' ],
		$date_src,
		$speaker_src,
		$series_src,
		$topic_src,
		$audio_link_src
	);
	
	echo $item_output;
}

function cultiv8_the_person_details( $post_id, $glyph = 'fa' ){
	$classes = array(
		'container'  => 'ctcex-person-container',
		'details'    => 'ctcex-person-details',
		'title'      => 'ctcex-person-title',
		'position'   => 'ctcex-person-position',
		'email'      => 'ctcex-person-email',
		'urls'       => 'ctcex-person-urls',
		'img'        => 'ctcex-person-img'
	);
	wp_enqueue_style( 'cultiv8-glyphs', get_stylesheet_directory_uri() . '/assets/css/glyphs.css', array(), null, 'screen' );
	
	$title 		= get_the_title( $post_id ) ;
	$data 		= cultiv8_get_person_data( $post_id );
	$urls     = explode( "\r\n", $data[ 'url' ] );
	
	if( $data[ 'email' ] )
		$urls[] = 'mailto:' . $data[ 'email' ];
	
	// URLs
	$url_src = sprintf( '<div class="%s %s ctcex-socials"><ul>', $classes[ 'urls' ], $glyph === 'gi' ? 'gi' : 'fa' );
	foreach( $urls as $url_item ){
		$url_src .= sprintf( '<li><a href="%s">%s</a></li>', $url_item, $url_item );
	}
	$url_src .= '</ul></div>';
	
	// Get position
	$position_src = $data[ 'position' ] ? sprintf( '<h3 class="%s">%s</h3>', $classes[ 'position' ], $data[ 'position' ] ) : '';
				
	// Get image
	$img_src = $data[ 'img' ] ? sprintf( '<img class="%s" src="%s" alt="%s"/>', $classes[ 'img' ], $data[ 'img' ], $title ) : '';

	$names = cultiv8_get_option( 'ctc-people', __( 'people/person', 'cultiv8' ) );
	$plural_name = explode( '/', strtolower( $names ) );
	$single_name = array_pop( $plural_name );
		
	// Prepare output
	$item_output =sprintf(
		'<div class="%s">
			%s
			<div class="%s">
				%s
				%s
			</div>
		</div>
		', 
		$classes[ 'container' ],
		$img_src,
		$classes[ 'details' ],
		$position_src,
		$url_src
	);
	
	echo $item_output;
}

function cultiv8_the_location_details( $post_id, $glyph = 'fa' ){
	$classes = array(
		'container'  => 'ctcex-location-container',
		'details'    => 'ctcex-location-details',
		'media'      => 'ctcex-location-media',
		'title'      => 'ctcex-location-title',
		'address'    => 'ctcex-location-address',
		'times'      => 'ctcex-location-times',
		'phone'      => 'ctcex-location-phone',
		'img'        => 'ctcex-location-img'
	);
	wp_enqueue_style( 'cultiv8-glyphs', get_stylesheet_directory_uri() . '/assets/css/glyphs.css', array(), null, 'screen' );
	
	$title 		= get_the_title( $post_id ) ;
	$data 		= cultiv8_get_location_data( $post_id );
	
	// Address
	$addr_src = '';
	if( $data[ 'address' ] ){
		$addr_src = sprintf( 
			'<div class="%s"><i class="%s %s"></i> %s</div>', 
			$classes[ 'address' ], 
			$glyph === 'gi' ? 'genericon' : 'fa', 
			$glyph === 'gi' ? 'genericon-location' : 'fa-map-marker', 
			$data[ 'address' ] );
	}
	
	// Times
	$time_src = '';
	if( $data[ 'times' ] ) {
		$time_src = sprintf( 
			'<div class="%s"><i class="%s %s"></i> %s</div>', 
			$classes[ 'times' ], 
			$glyph === 'gi' ? 'genericon' : 'fa', 
			$glyph === 'gi' ? 'genericon-time' : 'fa-clock-o', 
			$data[ 'times' ] );
	}
	
	// Phone
	$phone_src = '';
	if( $data[ 'phone' ] ) {
		$phone_src = sprintf( 
			'<div class="%s"><i class="%s %s"></i> %s</div>', 
			$classes[ 'phone' ], 
			$glyph === 'gi' ? 'genericon' : 'fa', 
			$glyph === 'gi' ? 'genericon-phone' : 'fa-mobile', 
			$data[ 'phone' ] );
	}
	
	// Get image
	$img_src = $data[ 'slider' ] ? do_shortcode( $data[ 'slider' ] ) : ''; 
	$img_src = !$img_src ? sprintf( 
		'%s
			<img class="%s" src="%s" alt="%s"/>
		%s', 
		$data[ 'map_used' ] ? '<a href="' . $data[ 'map_url' ] . '" target="_blank">' : '',
		$classes[ 'img' ], 
		$data[ 'img' ], 
		get_the_title(),
		$data[ 'map_used' ] ? '</a>' : ''
	) : $img_src ;
	
	$names = cultiv8_get_option( 'ctc-people', __( 'locations/location', 'cultiv8' ) );
	$plural_name = explode( '/', strtolower( $names ) );
	$single_name = array_pop( $plural_name );
		
	// Prepare output
	$item_output =sprintf(
		'<div class="%s">
			<div class="%s">%s</div>
			<div class="%s">
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
		$addr_src,
		$time_src,
		$phone_src
	);
	
	echo $item_output;
}

