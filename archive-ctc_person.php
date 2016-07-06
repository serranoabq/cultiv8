<?php
/**
 * The template for displaying person archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Cultiv8
 */
wp_enqueue_style( 'cultiv8-glyphs', get_stylesheet_directory_uri() . '/assets/css/glyphs.css', array(), null, 'screen' );
 
$titles = cultiv8_option( 'ctc-people', __( 'People/Person', 'cultiv8' ) );
$titles = explode( '/', $titles );
$title = array_shift( $titles );

$query_term = $wp_query->query;
$args = array(
	'order' => 'ASC',
	'orderby' => 'menu_order',
	'post_per_page' => -1,
);
$query_term = array_merge( $args, $query_term ); 
$wp_query = new WP_Query( $query_term );	

get_header(); ?>


	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title"><?php echo $title; ?></h1>
				<style>
					.pill.button{
						padding: 5px;
						border-radius: 3px;
						max-width: 100px;
						margin: 5px;
						flex: auto;
						flex-basis: 100px;
						display: flex;
						justify-content: center;
						cursor: pointer;
						font-size: 12px;
					}
					.pill.button span{
						flex: auto;
						align-self: center;
					}
					#ctc-people-groups{
						display: flex;
						flex-flow: row wrap;
						justify-content: center;
						align-items: stretch;
						align-content: space-around;						
					}
					
				</style>
				<div id="ctc-people-groups"></div>
			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>
			<div class="flex-container">
			<?php while ( have_posts() ) : the_post(); ?>
			
				<!-- Person details -->
				<?php get_template_part( 'components/content', 'single-person-grid' ); ?>
				<!-- End person details -->

			<?php endwhile; ?>
			</div>
			<?php the_posts_navigation(); ?>
			<script>
				jQuery( document ).ready( function( $ ){
					var items = $( '.ctcex-person-container' );
					var groups = [];
					items.each( function( i, el ){
						if( $(el).data('groups') ) {
							var mgroups = $(el).data('groups').split( '; ' );
							$.merge( groups, mgroups );
						}
					} );
					
					var uniqueGroups = [];
					$.each(groups, function(i, el){
						if($.inArray(el, uniqueGroups) === -1) uniqueGroups.push(el);
					});
					uniqueGroups.sort();
					
					$.each( uniqueGroups, function( i, el ){
						var grp = $('<div class="pill button"><span>' + el + '</span></div>');
						grp.click( function() {
							if( $(this).hasClass('active') ) {
								$(this).removeClass('active');
								$( '.ctcex-person-container' ).fadeIn(500);
							} else {
								$(this).siblings().removeClass('active');
								$(this).addClass('active');
								$( '.ctcex-person-container:not([data-groups *= "' + el + '"])' ).fadeOut(250);
								$( '.ctcex-person-container[data-groups *= "' + el + '"]' ).fadeIn(500);
							}
						})
						$( '#ctc-people-groups' ).append( grp );
					} );
					
				});
			</script>	
		<?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->


<?php get_footer(); ?>
