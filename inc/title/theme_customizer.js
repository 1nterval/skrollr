( function( $ ) {

	// remove bootstrap grid classes
	$.fn.removeColClass = function(){
		$(this).removeClass(function( index, className ){
			return className.split(' ')
				.filter( function(className){ return className.match(/col-sm(-offset)?-\d{1,12}/) } )
				.join(' ');
		});
		return $(this);
	};

	// Update title witdh
	wp.customize( 'title_width', function( value ) {
		value.bind( function( newval ) {
			$( '.block-0 h1' ).removeColClass()
				.addClass( "col-sm-" + (newval * 2) )
				.addClass( "col-sm-offset-" + (6 - newval) )
				.slabText();
		} );
	} );


} )( jQuery );
