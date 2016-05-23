<?php
/**
 * Template part for displaying latest sermon
 *
 * @package Cutiv8
 */

$query = array(
	'post_type' 			=> 'ctc_sermon', 
	'posts_per_page'	=> 1,
	'order' 				  => 'DESC', 
	'orderby' 				=> 'date',
);

/*
if( 'all' != $contents['topic'] ){
	$args[ 'tax_query' ] = array( 
			array(
				'taxonomy' 	=> 'ctc_sermon_topic',
				'field'			=> 'slug',
				'terms'			=> $contents['topic'],
			),
		);
}
/* */



$ctc_sermons = new WP_Query( $query );

?>

<?php if ( $ctc_sermons->have_posts() ) : ?>

<div class="pique-grid-three">

	<?php while ( $ctc_sermons->have_posts() ) : $ctc_people->the_post(); ?>
	<?php 
		$post_id = get_the_ID();
		$data = cultiv8_get_person_data( $post_id );
		$urls = explode( "\r\n", $data[ 'url' ] );
	?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div> 
			<?php
			if ( has_post_thumbnail() ) :
				the_post_thumbnail( 'pique-square' );
			else:
				echo '<div class="size-pique-square" style="position: relative; wi<img class="size-pique-square" src="'. $data[ 'img' ] . '" />';
			endif;
			?>

			<h2><?php echo $data[ 'name' ]; ?></h2>
			<h3><?php echo $data[ 'position' ]; ?></h2>
			
			<?php //the_title( '<h3>' , '</h3>' ); ?>

			<?php // the_content(); ?>
			<?php 
				echo sprintf( '<h2>%s</h2><h3>%s</h3>', $data[ 'name' ], $data[ 'position' ] );
				$i = 1;
				$urls = explode( "\r\n", $data[ 'url' ] );
				foreach( $urls as $url ) {
					if( 1 == $i ) { echo '<div class="secondary-links"><ul>'; $i++; }
					echo sprintf( '<li><a href="%s"></a></li>', $url );
				}
				if( $i > 1 ) echo '</ul></div>'; 
			?>
		</article>

	<?php endwhile; ?>

</div><!-- .child-pages .grid -->

<?php
endif;
wp_reset_postdata();
?>
