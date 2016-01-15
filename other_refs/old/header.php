<?php
/**
 * HEADER
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<title><?php wp_title( '|', true, 'right' ); ?></title>

	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php echo get_bloginfo_rss( 'rss2_url' ); ?>" />
	
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php do_action( 'before' ); ?>

	<header id="masthead" class="site-header" role="banner">
	<?php // substitute the class "container-fluid" below if you want a wider content area ?>
		<div class="container">
			<div class="row">
				<div class="site-header-inner col-sm-12">
<?php 	if ( ! cultiv8_is_frontpage() AND get_header_image() ) : ?>
					<a class="cultiv8-header" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
<?php 
					if ( is_singular() && has_post_thumbnail() ) :
						the_post_thumbnail( 'cultiv8-header', array( 'id' => 'cultiv8-header-image' ) );
					else : 
?>
						<img id="cultiv8-header-image" src="<?php header_image(); ?>" alt="">
<?php 		endif; ?>
				</a>
<?php 	endif; ?>

					<div class="site-branding">
						<?php cultiv8_the_site_logo(); ?>
						<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
						<p class="site-description"><?php bloginfo( 'description' ); ?></p>
					</div>

				</div>
			</div>
		</div><!-- .container -->
	</header><!-- #masthead -->

<?php if ( has_nav_menu( 'primary' ) ) : ?>
	<nav class="site-navigation">
	<?php // substitute the class "container-fluid" below if you want a wider content area ?>
		<div class="container">
			<div class="row">
				<div class="site-navigation-inner col-sm-12">
					<div class="navbar navbar-default">
						<div class="navbar-header">
							<!-- .navbar-toggle is used as the toggle for collapsed navbar content -->
							<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
								<span class="sr-only"><?php _e('Toggle navigation','_tk') ?> </span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
		
							<!-- Your site title as branding in the menu -->
							<a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
						</div>

						<!-- The WordPress Menu goes here -->
						<?php wp_nav_menu(
							array(
								'theme_location'  => 'primary',
								'depth'           => 2,
								'container'       => 'div',
								'container_class' => 'collapse navbar-collapse',
								'menu_class'      => 'nav navbar-nav',
								'fallback_cb'     => 'wp_bootstrap_navwalker::fallback',
								'menu_id'         => 'main-menu',
								'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
								'walker'          => new wp_bootstrap_navwalker()
							)
						); ?>

					</div><!-- .navbar -->
				</div>
			</div>
		</div><!-- .container -->
	</nav><!-- .site-navigation -->	
<?php endif; // has_nav_menu ?>

	<div class="main-content">


