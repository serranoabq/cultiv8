<?php
/**
 * The template for displaying all single sermon posts.
 *
 * @package Cultiv8
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'components/content', 'single-person' ); ?>

			<?php 
				$meta_args = array(
					'order' 		=> 'ASC',
					'orderby' 	=> 'menu_order',
				);
				cultiv8_link_pages_by_meta( array(
					'prev_text' => '<span>' . esc_html__( 'Previous', 'pique' ) . '</span> %title',
					'next_text' => '<span>' . esc_html__( 'Next', 'pique' ) . '</span> %title',
				), $meta_args );
 
			?>

			<?php
			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;
			?>

		<?php endwhile; // End of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->


<?php get_sidebar(); ?>
<?php get_footer(); ?>
