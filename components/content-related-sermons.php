<?php
/**
 * Template part for displaying related sermons
 *
 * @package Cultiv8
 */
?>

<?php
	$data = cultiv8_get_sermon_data( $post->ID );

	if( $data[ 'series' ] ):
		$tax_query[] = array(
				'taxonomy'	=> 'ctc_sermon_series',
				'field'			=> 'slug',
				'terms'			=> $data[ 'series_slug' ],
		);
		if( $data[ 'topic' ] ) {
			$tax_query[] = array( 
				'taxonomy'	=> 'ctc_sermon_topic',
				'field'			=> 'slug',
				'terms'			=> $data[ 'topic_slug' ],	
			);
			$tax_query['relation'] = 'AND';
		}
		$args = array( 
			'post_type' 			=> 'ctc_sermon',  
			'tax_query'				=> $tax_query,
			'order' 					=> 'DESC', 
			'posts_per_page'   => 3,
			'no_found_rows'   => true,
			'post__not_in'		=> array( $post->ID ),
		);
		$related_pages = new WP_Query ( $args );

		$topic_name = cultiv8_get_option( 'ctc-sermon-topic', __( 'Topic', 'cultiv8' ) );
		$topic = explode( '/', $topic_name );
		$topic = strtolower( array_pop( $topic ) );
		$series_name = cultiv8_get_option( 'ctc-sermon-series', __( 'Series', 'cultiv8' ) );
		$series = explode( '/', $series_name );
		$series = strtolower( array_pop( $series ) );
		
?>

<?php if ( $related_pages->have_posts() ) : ?>

<h2><?php echo  __( 'Other messages from this ', 'cultiv8') . strtolower( $series ) . ( $data['topic'] ? _x( ' and ', 'Space before and after', 'cultiv8' ) . strtolower( $topic ) : '' ); ?></h2>

<div class="pique-grid-three">

	<?php while ( $related_pages->have_posts() ) : $related_pages->the_post(); 
		$mdata = cultiv8_get_sermon_data( get_the_ID() );
		$img = $mdata[ 'img' ];
		$permalink = $mdata[ 'permalink' ];

	?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<?php if ( $img ) : ?>
				<img class="ctc-sermon-img" src="<?php echo $img; ?>" width="320" height="180" />
			<?php endif; ?>

			<a href="<?php echo $permalink; ?>"><?php the_title( '<h3>' , '</h3>' ); ?></a>

		</article>

	<?php endwhile; ?>

</div><!-- .child-pages .grid -->

<?php
endif;
wp_reset_postdata();
endif;
