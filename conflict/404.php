<?php
/**
 * ERROR 404
 */

get_header(); 

 // add the class "panel" below here to wrap the content-padder in Bootstrap style ;) 
?>
	<section class="content-padder error-404 not-found">

		<header>
			<h2 class="page-title"><?php _e( 'Oops! Something went wrong here.', '_tk' ); ?></h2>
		</header><!-- .page-header -->

		<div class="page-content">

			<p><?php _e( 'We\'re sorry. The page you requested could not be found. It\'s a simple mistake, probably on our part. Please <a href="/">click here</a> to return our homepage or use the menu above to visit another section of our site. Have a great day!', '_cultiv8' ); ?></p>

		</div><!-- .page-content -->

	</section><!-- .content-padder -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>