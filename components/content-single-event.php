<?php
/**
 * Template part for displaying single event posts.
 *
 * @package Cultiv8
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<!-- Event details -->
		<?php cultiv8_the_event_details( get_the_ID(), 'fa' ); ?>
		<!-- End event details -->
		
		<?php the_content(); ?>

	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php pique_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
