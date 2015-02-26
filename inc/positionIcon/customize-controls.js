(function( exports, $ ){
	var api = wp.customize;

	api.controlConstructor.positionIcon = api.Control.extend({
		ready: function() {
			var control = this;

			function bindChangeEvent( value ) {
				value.bind( function( newval ) {
					wp.customize.previewer.send( 'iconPosition-change', {
						'id': control.params.iconId,
						'image' : control.settings.image(),
						'url' : control.settings.url(),
						'parentSelector' : control.params.parentSelector,
					} );
				} );
			}

			// wait for the preview to be ready
			api.previewer.bind( 'documentTitle', function () {
				wp.customize.previewer.send( 'iconPosition-setup', {
					'id': control.params.iconId,
					'image' : control.settings.image(),
					'url' : control.settings.url(),
					'parentSelector' : control.params.parentSelector,
				} );

				// bind change event to image and url controls update
				wp.customize(control.settings.image.id, bindChangeEvent );
				wp.customize(control.settings.url.id, bindChangeEvent );
			} );

			// when icon has moved, update the position setting
			wp.customize.previewer.bind( 'iconPosition-update', function( position ){
				control.settings.position(position);
			} );

		}
	});

})( wp, jQuery );
