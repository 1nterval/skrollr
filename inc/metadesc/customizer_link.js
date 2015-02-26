jQuery(function($){

	$( 'body' ).addClass( 'can_edit_theme_options' );

	// link elements to their customizer control
	$( '#chapo .text-column' ).click(function(){
		location.href=customizer_url + "?autofocus[control]=metadesc";
	});
});
