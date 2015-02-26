( function( $, _ ) {

	var media = wp.media;

	media.view.Settings.Gallery = media.view.Settings.extend({
		className: 'collection-settings gallery-settings skrollr-gallery-settings',
		template:  media.template('skrollr-gallery-settings')
	});

}(jQuery, _));
