<?php
	// HELPER: Feed
	
	// iTunes enhancements

function cultiv8_podcast_description(){
	global $wp_query;
	$query = $wp_query->query;
	$site_desc = bloginfo( 'description' );
	$pod_desc = get_theme_mod( 'cultiv8_podcast_desc', '' );
	$term_desc = '';
	if( is_user_logged_in() ){
	if( isset( $query[ 'ctc_sermon_topic' ] )){
		$term = get_term_by( 'slug', $query[ 'ctc_sermon_topic' ], 'ctc_sermon_topic' );
		$term_desc = $term->description;
	}
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
if( cultiv8_option( 'podcast_author' ) ) 
			echo '
	<itunes:author>'. cultiv8_option( 'podcast_author' ) . '</itunes:author>';
	if( $desc ) 
			echo '
	<itunes:summary>'. $desc . '</itunes:summary>';
	if( cultiv8_option('podcast_image') )
			echo '
	<itunes:image href="'. cultiv8_option('podcast_image' ) . '"/>';
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
		$min_loc_slug = $locs[0]->slug;
		
		if( ! empty( $min_loc_slug ) )
			$qv[ 'ctc_sermon_topic' ] = $min_loc_slug;
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

