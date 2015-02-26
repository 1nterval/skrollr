( function( $ ) {

	var bg, active, txt;

	wp.customize( 'bg_color', function( value ) {
		bg = value;
		value.bind( function( newval ) {
			update_color_css();
			$( '#main-menu' )
				.attr( 'data-10', HEX_to_RGBA( newval, 1 ) )
				.attr( 'data-200', HEX_to_RGBA( newval, 0 ) )
				.attr( 'data-200-end', HEX_to_RGBA( newval, 0 ) )
				.attr( 'data-40-end', HEX_to_RGBA( newval, 1 ) )
				.css( 'background-color', '' );
			skrollr.get().refresh($( '#main-menu' ));
		} );
	} );

	wp.customize( 'bg_active_color', function( value ) {
		active = value;
		value.bind( function( newval ) {
			update_color_css();
		} );
	} );

	wp.customize( 'txt_color', function( value ) {
		txt = value;
		value.bind( function( newval ) {
			update_color_css();
		} );
	} );

	function update_color_css(){
		$.get( ajax_url, {
			'bg':    encodeURIComponent( bg.get() ),
			'active': encodeURIComponent( active.get() ),
			'txt':   encodeURIComponent( txt.get() ),
			'action': 'content-colors'
		}, function(data) {
			$( '#customized-content-colors' ).remove();
			$( 'head' ).append( '<style>' + data + '</style>' );
		} );
	}

	function HEX_to_RGBA(hex, alpha) {
	var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
	return result ? 'rgba(' 
		+ parseInt(result[1], 16) + ','
		+ parseInt(result[2], 16) + ','
		+ parseInt(result[3], 16) + ','
		+ alpha + ')'
	: '';
}

} )( jQuery );
