( function( $ ) {

	wp.customize( 'credits_activate', function( value ) {
		value.bind( function( newval ) {
			if( newval ) $('.credits').show();
			else $('.credits').hide();
		} );
	} );

	wp.customize( 'credits_link_title', function( value ) {
		value.bind( function( newval ) {
			$('.credits').text( newval );
		} );
	} );

	wp.customize( 'credits_content', function( value ) {
		value.bind( function( newval ) {
			console.log(newval);
		} );
	} );

} )( jQuery );
