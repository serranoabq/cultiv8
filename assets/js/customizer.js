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
	
	// Additional Panel options
	for( var i = 1; i <= 8; i++ ){
		var ist = i.toString();
		var panelctrl = 'cultiv8_panel' + ist;
		var panel = '.pique-panel' + ist;
		
		( function( ctrl, panel ) {
			wp.customize( ctrl + '_hidetitle', function( value ) {
				value.bind( function( to ) {
					if( to ) {
						$( panel + ' .entry-header h2').css( 'display', 'none');
					} else {
						$( panel + ' .entry-header h2').css( 'display', '');
					}
				} );
			} );
			
			wp.customize( ctrl + '_height', function( value ) {
				value.bind( function( to ) {
					if( to ) {
						$( panel ).css( 'min-height', 'auto');
					} else {
						$( panel ).css( 'min-height', '60vh');
					}
				} );
			} );
		} ) ( panelctrl, panel );
	}
	
} )( jQuery );
