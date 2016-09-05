<?php
	// HELPER: Feed
	
	// RSS feed enhancements

function cultiv8_podcast_description(){
	global $wp_query;
	$query = $wp_query->query;
	$site_desc = bloginfo( 'description' );
	$pod_desc = get_theme_mod( 'cultiv8_podcast_desc', '' );
	$term_desc = '';
	if( isset( $query[ 'ctc_sermon_topic' ] )){
		$term = get_term_by( 'slug', $query[ 'ctc_sermon_topic' ], 'ctc_sermon_topic' );
		$term_desc = $term->description;
	}
	
	if( !empty($term_desc) ) return $term_desc;
	if( !empty($pod_desc) ) return $pod_desc;
	if( !empty($site_desc) ) return $site_desc;
	return '';
}
	
// Add namespace
add_filter( 'rss2_ns', 'cultiv8_itunes_namespace' );
function cultiv8_itunes_namespace() {
	echo 'xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"';
}

add_filter('rss2_head', 'cultiv8_itunes_head');
function cultiv8_itunes_head() {
		$desc = cultiv8_podcast_description();
	if( cultiv8_option( 'podcast_author' ) ) {
			echo '
	<itunes:author>'. cultiv8_option( 'podcast_author' ) . '</itunes:author>';
	}
	if( $desc ) {
			echo '
	<itunes:summary>'. $desc . '</itunes:summary>';
	}
	if( cultiv8_option('podcast_image') ) {
			echo '
	<itunes:image href="'. cultiv8_option('podcast_image' ) . '"/>';
	}
}


/*************************************************************
/ RSS Feeds
/************************************************************/
// Change the feed to just the sermons
add_filter( 'request', 'cultiv8_feed_request' );
function cultiv8_feed_request( $qv ) {
	if( ! isset( $qv['feed'] ) ) return $qv;
	
	if( ! isset($qv[ 'post_type' ] ) )
		$qv[ 'post_type' ] = 'ctc_sermon';
	
	$topic_option = ctcex_get_option( 'ctc-sermon-topic' );
	if( ! empty( $topic_option ) && ! isset( $qv['ctc_sermon_topic' ] ) ) {
		// Set the first location as the default
		$locs = get_terms('ctc_sermon_topic', array( 'order_by' => 'id', 'order' => 'DESC') );
		$def_loc = array_shift( $locs );
		$def_loc = $def_loc->slug;
		
		if( ! empty( $def_loc ) )
			$qv[ 'ctc_sermon_topic' ] = $def_loc;
	}
	
	return $qv;
}


// Add an image to go with the item on the feed
add_filter( 'the_excerpt_rss', 'cultiv8_rss_post_thumbnail' );
add_filter( 'the_content_feed', 'cultiv8_rss_post_thumbnail' );
function cultiv8_rss_post_thumbnail( $content ) {
	global $post;
	$content = '<p><img src="' . cultiv8_getImage( $post->ID ) . '"/></p>' . $content;
	
	return $content;
}

// Add logos and icons to feeds
add_action( 'atom_head', 'cultiv8_atom_feed_add_icon' );
add_action( 'comments_atom_head', 'cultiv8_atom_feed_add_icon' );
function cultiv8_atom_feed_add_icon() { 
?>
	<feed>
		<icon><?php echo get_site_icon_url(); ?></icon>
		<logo><?php echo get_theme_mod( 'cultiv8_feed_logo', get_theme_mod( 'cultiv8_site_logo', '' ) ); ?></logo>
	</feed>
<?php }

add_action( 'rss_head', 'cultiv8_rss_feed_add_icon' );
add_action( 'rss2_head', 'cultiv8_rss_feed_add_icon' );
add_action( 'commentsrss2_head', 'cultiv8_rss_feed_add_icon' );
function cultiv8_rss_feed_add_icon($text) { 
?>
	<image>
		<url><?php echo get_theme_mod( 'cultiv8_feed_logo', get_theme_mod( 'cultiv8_site_logo', '' ) ); ?></url>
		<title><?php wp_title( '|', true, 'right' ); ?></title>
		<link><?php bloginfo_rss( 'url' ); ?></link>
		<description><?php echo cultiv8_podcast_description(); ?></description>
	</image>
<?php 
} 

// Fix feed title
add_filter('get_wp_title_rss', 'cultiv8_rss_title', 10, 2);
function cultiv8_rss_title( $title, $dep ){
	global $wp_query;
	$query = $wp_query->query;
	$title = get_bloginfo( 'name' );
	
	// If a topic (aka Location) is set fix the title appropriately
	if( isset( $query[ 'ctc_sermon_topic' ] ) ){
		// Since we've filtered the feed such that the first location is the 'default', we
		// only add the location to the title if it's not the default one
		$locs = get_terms('ctc_sermon_topic', array( 'order_by' => 'id', 'order' => 'DESC') );
		$def_loc = array_shift( $locs );
		$def_loc = $def_loc->slug;
		$term = get_term_by( 'slug', $query[ 'ctc_sermon_topic' ], 'ctc_sermon_topic' );
		if( $term && $term->slug != $def_loc ){
			$title .= ' ' . $term->name;
			// This corrects duplication of a name if the campus names have the name of the church 
			// ( e.g., "Crossroads" is the church and the campus is "Crossroads Springfield", which would result in "Crossroads Crossroads Springfield")
			$title = implode( ' ', array_unique( explode( ' ', $title ) ) );
		}
	}
	
	return $title;
}

// Use the sermon speaker as the post author in RSS feed
add_filter( 'the_author', 'cultiv8_rss_author', 10 );
function cultiv8_rss_author( $name ){
	if( is_feed() ){
		global $post;
		$data = cultiv8_get_sermon_data( $post->ID );
		if( $data[ 'speakers' ] ) 
			$name = $data[ 'speakers' ];
	}
	return $name;
}

/* 
	CTC uses the WP function do_enclose to make proper enclosures for the audio in a post. That method does an http fetch to
	get the audio length. If the fetch fails the enclosure isn't added. While the fetch is necessary if the file is remote, 
	it isn't needed for files in the uploads folder, which WP can process without the HTTP fetch. This method now does the 
	handling of the enclosure data using WP methods for local files and then falls back to the normal do_enclose method if 
	it's a remote file. If you don't need it, comment the next function out. 
	
*/
remove_action( 'save_post', 'ctc_sermon_save_audio_enclosure', 11 ); // Replace the built-in CTC enclosure function which is failing on my server
add_action( 'save_post', 'cultiv8_sermon_save_audio_enclosure', 11, 2 ); // after 'save_post' saves meta fields on 10
function cultiv8_sermon_save_audio_enclosure( $post_id, $post ) {

	// Stop if no post, auto-save (meta not submitted) or user lacks permission
	if ( 'ctc_sermon' != $post->post_type ) {
		return;
  }
	$post_type = get_post_type_object( $post->post_type );
	if ( empty( $_POST ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
		return false;
	}

	// Stop if PowerPress plugin is active
	// Solves conflict regarding enclosure field: http://wordpress.org/support/topic/breaks-blubrry-powerpress-plugin?replies=6
	if ( defined( 'POWERPRESS_VERSION' ) ) {
		return false;
	}

	// Get audio URL
	$audio = get_post_meta( $post_id , '_ctc_sermon_audio' , true );

	// The built-in do_enclose method goes a roundabout way of getting the file 
	// length, which involves an http fetch to get the right length. On some server 
	// configurations if the fetch fails the enclosure isn't added. While the fetch 
	// is necessary if the file is remote, it's frustrating if the file is on the 
	// same server, where WP can get all the information without the http fetch. 
	// This method now does the handling of the enclosure data using WP methods if the 
	// file is local and then falls back to the normal do_enclose method if it's 
	// a remote file
	
	// Populate enclosure field with URL, length and format, if valid URL found
	$uploads = wp_upload_dir();
	// A local file is assume to be one living in the uploads directory
	$is_local = stripos( $audio, $uploads[ 'baseurl' ] ); 
	if( ! ( false === $is_local)  ) {
		// Get the path to the file
		$audio_src = str_replace( $uploads['baseurl'], $uploads['basedir'], $audio );
		// Get meta data
		$metadata =  wp_read_audio_metadata( $audio_src );
		if( $metadata ){
			// Make sure we got metadata and read the mime_type 
			// and filesize values which are needed for the enclosure
			$mime = $metadata[ 'mime_type' ];
			$length = $metadata[ 'filesize' ];
			if( $mime ) {
				// We've got data, add enclosure meta
				update_post_meta( $post_id, 'enclosure', "$audio\n$length\n$mime\n" );
			}
		}
	} else {
		// Leave do_enclose for remote files
		do_enclose( $audio, $post_id ); 
	}
}
/* */

