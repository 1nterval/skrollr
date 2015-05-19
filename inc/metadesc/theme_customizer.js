( function( $ ) {

	// link elements to their customizer control
	$( '#chapo .text-column, #chapo .post-edit-link' ).click(function(){
		frames.top.wp.customize.control( 'metadesc' ).focus()
	});
	$( '#chapo .title-column' ).click(function(){
		frames.top.wp.customize.control( 'blogdescription' ).focus()
	});

	// Update the site meta description
	wp.customize( 'metadesc', function( value ) {
		value.bind( function( newval ) {
			$( '#chapo .text-column' ).html( '<p>' + newval + '</p>' );
		} );
	} );

	// Update the site slogan
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( newval ) {
			$( '#chapo .title-column' ).html( '<h2>' + newval + '</h2>' );
		} );
	} );

} )( jQuery );
