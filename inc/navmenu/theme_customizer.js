( function( $ ) {

	// Hide/display main menu
	wp.customize( 'display_menu', function( value ) {
		value.bind( function( newval ) {
			if( newval ) {
				$('#main-menu').show();
			} else {
				$('#main-menu').hide()
			}
		} );
	} );

} )( jQuery );
