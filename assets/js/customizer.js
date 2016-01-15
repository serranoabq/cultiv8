/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {

	// Site logo
	wp.customize( 'cultiv8_site_logo', function( value ) {
		value.bind( function( to ) {
			if( to ) {
				$( 'a.site-logo-link').css( 'display', '');
				$( 'img.site-logo' ).attr( 'src', to );
			} else {
				$( 'a.site-logo-link').css( 'display', 'none');
			}
		} );
	} );
	

} )( jQuery );
