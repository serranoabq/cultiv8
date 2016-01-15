<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package _tk
 */
?>
			</div><!-- close .*-inner (main-content or sidebar, depending if sidebar is used) -->
		</div><!-- close .row -->
	</div><!-- close .container -->
</div><!-- close .main-content -->

<footer id="colophon" class="site-footer" role="contentinfo">
<?php // substitute the class "container-fluid" below if you want a wider content area ?>
	<div class="container">
		<div class="row">
			<div class="site-footer-inner col-sm-12">

<?php 
			get_sidebar( 'footer' ); 
?>
			
<?php
		if ( has_nav_menu( 'secondary' ) ) :
			wp_nav_menu( array( 'theme_location' => 'secondary', 'depth' => 1, 'container_class' => 'secondary-links', ) );
		endif;
?>

				<div class="site-info">
					<?php do_action( '_cultiv8_credits' ); ?>
					<?php echo sprintf( __( '&copy; Copyright %d %s', 'harvest' ), date('Y'), get_bloginfo( 'name' ) ); ?>
			  </div><!-- close .site-info -->

			</div>
		</div>
	</div><!-- close .container -->
</footer><!-- close #colophon -->

<?php wp_footer(); ?>

</body>
</html>
