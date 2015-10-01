( function( $ ) {

	var bg, active, txt, content_bg, content_txt, desc_bg, desc_txt;

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
		value.bind( update_color_css );
	} );

	wp.customize( 'txt_color', function( value ) {
		txt = value;
		value.bind( update_color_css );
	} );

	wp.customize( 'content_bg_color', function( value ) {
		content_bg = value;
		value.bind( update_color_css );
	} );

	wp.customize( 'content_txt_color', function( value ) {
		content_txt = value;
		value.bind( update_color_css );
	} );

	wp.customize( 'desc_bg_color', function( value ) {
		desc_bg = value;
		value.bind( update_color_css );
	} );

	wp.customize( 'desc_txt_color', function( value ) {
		desc_txt = value;
		value.bind( update_color_css );
	} );

	function update_color_css(){
		$.get( ajax_url, {
			'bg':    encodeURIComponent( bg.get() ),
			'active': encodeURIComponent( active.get() ),
			'txt':   encodeURIComponent( txt.get() ),
			'content_bg':   encodeURIComponent( content_bg.get() ),
			'content_txt':   encodeURIComponent( content_txt.get() ),
			'desc_bg':   encodeURIComponent( desc_bg.get() ),
			'desc_txt':   encodeURIComponent( desc_txt.get() ),
			'action': 'content-colors'
		}, function(data) {
			$( '#customized-content-colors' ).remove();
			$( 'head' ).append( '<style id="customized-content-colors">' + data + '</style>' );
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
