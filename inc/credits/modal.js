jQuery(function($){
	$('.credits').click(function(){
		$('.modal').show();
		$('.modal-display').css( 'top', ($(window).height() - $('.modal-display').height()) / 2 );
		if( $('body').hasClass('customize_preview') ){
			return false;
		}
	});
	$('.modal-background, .modal-close').click(function(e){
		e.preventDefault();
		$('.modal').hide();
		// remove # from the URL
		history.replaceState("", document.title, window.location.pathname);
		return false;
	});
	var id = $('.modal').parent().attr('id');
	if( window.location && window.location.hash == "#" + id ){
		$('.modal').show();
	}
});
