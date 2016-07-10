<?php
/**
 * Template part for displaying single person posts.
 *
 * @package Cultiv8
 */

	$data = cultiv8_get_person_data( get_the_ID() );
	$title 		= get_the_title() ;
	
	$url_src  = '';
	$urls     = explode( "\r\n", $data[ 'url' ] );
	if( $urls ) {
		if( $data[ 'email' ] )
			$urls[] = 'mailto:' . $data[ 'email' ];

		$url_src = '<div class="gi ctcex-person-urls ctcex-socials" style="width:100%"><ul class="textcenter">';
		foreach( $urls as $url_item ){
			$url_src .= sprintf( '<li><a href="%s">%s</a></li>', $url_item, $url_item );
		}
		$url_src .= '</ul></div>';
	}
	
	$img_src = $data[ 'img' ] ? sprintf( '<div class="cultiv8-fixedratio cultiv8-square">
		<div class="cultiv8-fixedratio-content cultiv8-circle"><img class="ctcex-person-img" src="%s" alt="%s"/></div></div>', $data[ 'img' ], $title ) : '';
	
	$position_src = $data[ 'position' ] ? sprintf( '<h3 class="ctcex-person-position textcenter normfont flush-bottom">%s</h3>', $data[ 'position' ] ) : '';
	
	$item_output =sprintf(
		'<div class="ctcex-person-container" data-groups="%s" data-order="%s">
			%s
			<div class="ctcex-person-details">
				<h2 class="ctcex-person-title textcenter flush-bottom"><a href="%s">%s</a></h2>
				%s
				%s
			</div>
		</div>
		', 
		$data[ 'groups' ],
		$data[ 'order' ],
		$img_src,
		esc_url( get_permalink() ),
		$title,
		$position_src,
		$url_src
	);
	
	echo $item_output;
