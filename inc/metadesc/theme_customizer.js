( function( $ ) {

	// link elements to their customizer control
	$( '#chapo .text-column' ).click(function(){
		frames.top.wp.customize.control( 'metadesc' ).focus()
	});

	// Update the site meta description
	wp.customize( 'metadesc', function( value ) {
		value.bind( function( newval ) {
			$( '#chapo .text-column' ).html( '<p>' + newval + '</p>' );
		} );
	} );

} )( jQuery );
