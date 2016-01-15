<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Cultiv8
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo" style="background-image: url('<?php header_image(); ?>')">

		<?php get_sidebar( 'footer' ); ?>

		<?php
		if ( has_nav_menu( 'secondary' ) ) :
			wp_nav_menu( array( 'theme_location' => 'secondary', 'depth' => 1, 'container_class' => 'secondary-links', ) );
		endif;
		?>

		<div class="site-info">
			<?php echo sprintf( __( '&copy; Copyright %d %s', 'harvest' ), date('Y'), get_bloginfo( 'name' ) ); ?>
    </div><!-- .site-info -->

	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
