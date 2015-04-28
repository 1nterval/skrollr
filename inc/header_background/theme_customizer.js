( function( $ ) {

	// Update the site title
	wp.customize( 'header_textcolor', function( value ) {
		value.bind( function( newval ) {
			if( newval == 'blank' ) {
				$( '.block-0 > h1' ).attr( 'data-top', 'display:none' );
			} else {
				$( '.block-0 > h1' ).css( {
					color: newval
				}).attr( 'data-top', 'opacity:1;transform:scale(1);display:block' );;
			}
			skrollr.get().refresh($( '.block-0 > h1' ));
		} );
	} );

	wp.customize( 'header_image', function( value ) {
		value.bind( function( newval ) {
			$( '.block-0' ).css( 'background-image', 'url(' + newval + ')' );
		} );
	} );

} )( jQuery );
