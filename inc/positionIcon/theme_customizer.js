( function( $ ) {

	/**
	* Check that the icon exist and create it if not
	*/
	function checkAndCreate(id, image, url, parentSelector){
		var $icon = $('#'+id);
		if( image.length == 0 ) return false;
		if( $icon.length == 0 ) {
			$icon = $(document.createElement('img'))
				.attr( 'src', image )
				.wrap(document.createElement('a'))
				.parent().attr( 'id', params.id );
			if( url.length ) {
				$icon.attr( 'href', url );
			}
			$(parentSelector).append($icon);
		}
		return true;
	}

	/**
	* Bind the drag event to the icon if needed
	*/
	function bindDragEvent( id ) {
		var $icon = $('#'+id);
		if( $icon.draggable( "instance" ) == undefined ) {
			$icon.css({ 'cursor' : 'move', 'z-index' : 9999 }).draggable({
				stop: function(event, ui){
					wp.customize.preview.send( 'iconPosition-update', {
						top : ui.position.top / $(this).parent().height() * 100,
						left : ui.position.left / $(this).parent().width() * 100
					} );
				}
			});
		}
	}

	wp.customize.bind( 'preview-ready', function(){

		// wait for the control to be ready before setting-up the icon
		wp.customize.preview.bind( 'iconPosition-setup', function( params ) {
			checkAndCreate( params.id, params.image, params.url, params.parentSelector );
			bindDragEvent( params.id );
		});

		// change image and url of the icon upon user interaction with the controls
		wp.customize.preview.bind( 'iconPosition-change', function( params ) {
			var $icon = $('#'+params.id);
			checkAndCreate( params.id, params.image, params.url, params.parentSelector );
			bindDragEvent( params.id );
			$icon.attr( 'href', params.url );
			$icon.find( 'img' ).attr( 'src', params.image );
		});
	});

} )( jQuery );
