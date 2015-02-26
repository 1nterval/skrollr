( function( $ ) {

	// Update the site title
	wp.customize( 'header_textcolor', function( value ) {
		value.bind( function( newval ) {
			if( newval == 'blank' ) {
				$( '#accueil > h1' )
					.css( 'display', 'none' )
					.attr( 'data-top', 'opacity:1;transform:scale(1);display:none' );
			} else {
				$( '#accueil > h1' ).css( {
					display: 'block',
					color: newval
				}).attr( 'data-top', 'opacity:1;transform:scale(1);display:block' );;
			}
			skrollr.get().refresh($( '#accueil > h1' ));
		} );
	} );

	wp.customize( 'header_image', function( value ) {
		value.bind( function( newval ) {
			$( '#accueil' ).css( 'background-image', 'url(' + newval + ')' );
		} );
	} );

} )( jQuery );
