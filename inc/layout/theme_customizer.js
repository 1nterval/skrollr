( function( $ ) {

	// remove bootstrap grid classes
	$.fn.removeColClass = function(){
		$(this).removeClass(function( index, className ){
			return className.split(' ')
				.filter( function(className){ return className.match(/col-md(-offset)?-\d{1,12}/) } )
				.join(' ');
		});
		return $(this);
	};

	// Update footer menu layout
	wp.customize( 'footer_menu_layout', function( value ) {
		value.bind( function( newval ) {
			if( newval == 'left' ){
				$('.two-columns .text-column').addClass('col-md-push-5');
				$('.two-columns .menu-column').addClass('col-md-pull-5');
			} else {
				$('.two-columns .text-column').removeClass('col-md-push-5');
				$('.two-columns .menu-column').removeClass('col-md-pull-5');
			}
		} );
	} );

	// Update content witdh
	wp.customize( 'content_width', function( value ) {
		value.bind( function( newval ) {
			$( '.one-column .text-column' ).removeColClass().addClass("col-md-" + newval);
			if( newval > 8 && newval < 12 ) {
				$( '.one-column .text-column' ).addClass( "col-md-offset-" + Math.ceil(6 - newval/2) );
			}
			$( '.one-column .title-column' ).removeColClass().addClass("col-md-" + (newval > 8 ? '10' : '4') );
		} );
	} );

	// Update logo
	wp.customize( 'logo_header', function( value ) {
		value.bind( function( newval ) {
			var $logo = $('#logo-header');
			if( $logo.length ) {
				if( newval == '' ) {
					$logo.remove();
				} else {
					$logo.html('<img src="' + newval + '"/>');
				}
			} else {
				if( newval ) {
					$('#accueil').append('<a id="logo-header" href="#"><img src="'  + newval +  '"/></a>');
				}
			}
		} );
	} );


} )( jQuery );
