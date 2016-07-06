<?php
/**
 * Template part for displaying single event posts.
 *
 * @package Cultiv8
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="pique-panel-content">
		<header class="entry-header">
<?php if( is_archive() ): ?>
			<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
<?php else: ?>
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
<?php endif; ?>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<!-- Event details -->
			<?php cultiv8_the_event_details( get_the_ID(), 'fa' ); ?>
			<!-- End event details -->
			
			<?php the_content(); ?>

		</div><!-- .entry-content -->
	</div><!-- .pique-panel-content -->

	<footer class="entry-footer">
<?php if( is_archive() ): ?>
		<?php pique_edit_link( get_the_ID() ); ?>
<?php else: ?>
		<div class="entry-meta">
			<?php edit_post_link( esc_html__( 'Edit', 'pique' ), '<span class="edit-link">', '</span>' ); ?>
		</div>
<?php endif; ?>
		<?php pique_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
