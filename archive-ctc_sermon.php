<?php
/**
 * The template for displaying sermon archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Cultiv8
 */
 
$titles = cultiv8_option( 'ctc-sermons', __( 'Sermons/Sermon', 'cultiv8' ) );
$titles = explode( '/', $titles );
$title = array_shift( $titles );

$query_term = $wp_query->query;
$topic_option = cultiv8_get_option( 'ctc-sermon-topic' );
if( !empty( $topic_option ) && ! isset( $qv['ctc_sermon_topic' ] ) ) {
	// Set the first location as the default
	$locs = get_terms( 'ctc_sermon_topic', array( 'order_by' => 'id', 'order' => 'DESC') );
	$min_loc_slug = $locs[0]->slug;
	if( !empty( $min_loc_slug ) ){
		$tax_query = array(
			array(
				'taxonomy' => 'ctc_sermon_topic',
				'field'    => 'slug',
				'terms'    => $min_loc_slug
			)
		);
		$args[ 'tax_query' ] = $tax_query;
	}
	$query_term = array_merge( $args, $query_term ); 
	$wp_query = new WP_Query( $query_term );	
}
get_header(); ?>


	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title"><?php echo $title; ?></h1>
				<?php the_archive_description( '<div class="taxonomy-description">', '</div>' ); ?>
			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
			
				<?php
					get_template_part( 'components/content', 'single-sermon' );
				?>

			<?php endwhile; ?>

			<?php the_posts_navigation(); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->


<?php get_footer(); ?>
