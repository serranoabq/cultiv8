<?php
/**
 * The template for displaying event category archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Cultiv8
 */

$term = get_queried_object(); 
$title = ucwords( sprintf( _x( '%s events', 'Event category', 'cultiv8'), $term->name ) );

$query_term = $wp_query->query;
$args = array(
	'order' => 'ASC',
	'orderby' => 'meta_value',
	'meta_key' => '_ctc_event_start_date_start_time',
	'meta_type' => 'DATETIME',
	'posts_per_page' => 9,
	'meta_query' => array(
		array(
			'key' => '_ctc_event_end_date_end_time',
			'value' => date_i18n( 'Y-m-d H:i:s' ), // today localized
			'compare' => '>=', // later than today
			'type' => 'DATE',
		),
	)
);
$query_term = array_merge( $args, $query_term ); 
$wp_query = new WP_Query( $query_term );	
	
get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title"><?php echo $title; ?></h1>
				<div class="taxonomy-description"><?php echo do_shortcode( $term->description ); ?></div>
			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
			
				<?php
					get_template_part( 'components/content', 'single-event' );
				?>

			<?php endwhile; ?>

			<?php the_posts_navigation(); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->


<?php get_footer(); ?>
