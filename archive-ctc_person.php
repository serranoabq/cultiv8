<?php
/**
 * The template for displaying person archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Cultiv8
 */
wp_enqueue_style( 'cultiv8-glyphs', get_stylesheet_directory_uri() . '/assets/css/glyphs.css' );

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
					
					.ctcex-person-container, #ctc-person-grid{
						transition: all linear 250ms;
					}
					.ctcex-person-container.zoomout{
						transform: scale(0.05);
						opacity: 0;
					}
					.ctcex-person-container.zoomin{
						transform: scale(1.0);
						opacity: 1;
					}
				</style>
				<div id="ctc-people-groups"></div>
			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>
			<div>
			<div id="ctc-people-grid" class="flex-container">
			<?php while ( have_posts() ) : the_post(); ?>
			
				<!-- Person details -->
				<?php get_template_part( 'components/content', 'single-person-grid' ); ?>
				<!-- End person details -->

			<?php endwhile; ?>
			</div>
			</div>
			<?php the_posts_navigation(); ?>
			<script>
				jQuery( document ).ready( function( $ ){
					var items = $( '.ctcex-person-container' );
					var group_slugs = [];
					var group_names = [];
					items.each( function( i, el ){
						if( $(el).data('groups') ) {
							var mgroup_names = $(el).data('group_names').split( '; ' );
							var mgroup_slugs = $(el).data('groups').split( '; ' );
							$.merge( group_names, mgroup_names );
							$.merge( group_slugs, mgroup_slugs );
						}
					} );
					
					var uniqueGroups = [];
					var uniqueGroupSlugs = [];
					$.each(group_names, function(i, el){
						if($.inArray(el, uniqueGroups) === -1) uniqueGroups.push(el);
						if($.inArray(group_slugs[i], uniqueGroupSlugs) === -1) uniqueGroupSlugs.push(group_slugs[i]);
					});
					// uniqueGroups.sort();
					// uniqueGroupSlugs.sort();
					
					$.each( uniqueGroups, function( i, el ){
						var grp = $('<div class="pill button"><span>' + el + '</span></div>');
						grp.click( function() {
							if( $(this).hasClass('active') ) {
								$(this).removeClass('active');
								$( '.ctcex-person-container' ).addClass('zoomin' ).delay(250).queue( function(){$(this).fadeIn( 250 ).dequeue();});
							//.delay(250).queue(function(){$(this).addClass('shown').dequeue();});
							} else {
								$(this).siblings().removeClass('active');
								$(this).addClass('active');
								$( '.ctcex-person-container:not([data-groups *= "' + uniqueGroupSlugs[i] + '"])' ).addClass('zoomout' ).delay(250).queue( function(){$(this).fadeOut( 250 ).dequeue();});
								$( '.ctcex-person-container[data-groups *= "' + uniqueGroupSlugs[i] + '"]' ).addClass('zoomin' ).delay(250).queue( function(){$(this).fadeIn( 250 ).dequeue();});
								//.removeClass('hidden');
							}
						});
						$( '#ctc-people-groups' ).append( grp );
					} );
					
				});
			</script>	
		<?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->


<?php get_footer(); ?>
