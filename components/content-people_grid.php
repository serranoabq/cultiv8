<?php
/**
 * Template part for displaying content of ctc_people in grid form.
 *
 * @package Cutiv8
 */
$query = array(
	'post_type' 			=> 'ctc_person', 
	'orderby' 				=> 'menu_order',
	'order' 					=> 'ASC',
	'posts_per_page'  => -1,
);
$ctc_people = new WP_Query( $query );
//error_log( json_encode( $ctc_people ) );
?>

<?php if ( $ctc_people->have_posts() ) : ?>

<div class="pique-grid-three">

	<?php while ( $ctc_people->have_posts() ) : $ctc_people->the_post(); ?>
	<?php 
		$post_id = get_the_ID();
		$data = cultiv8_get_person_data( $post_id );
		$urls = explode( "\r\n", $data[ 'url' ] );
		echo json_encode( $data ) ;
	?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php if( $data[ 'img' ] ): ?>
			<div class="cultiv8-fixedratio cultiv8-square">
				<div class="cultiv8-fixedratio-content cultiv8-circle">
				<?php
					if( has_post_thumbnail() && get_the_post_thumbnail_url() == $data[ 'img' ] ):
						the_post_thumbnail( 'pique-square', array( 'class' => 'ctc-person-img' ) );
					else:
				?>
					<img class="ctc-person-img" src="<?php echo $data[ 'img' ]; ?>" width="100" height="100" />
				<?php endif; ?>
				
				</div>
			</div>
			<?php endif; ?>
			
			<h2><?php echo the_title(); ?></h2>
			<h3 class="cultiv8-ctc-position"><?php echo $data[ 'position' ]; ?></h3>
			<?php 
				$i = 1;
				foreach( $urls as $url ) {
					if( 1 == $i ) { echo '<div class="secondary-links" style="margin: 20px 0;"><ul>'; $i++; }
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
