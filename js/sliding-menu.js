jQuery(function($){
	// private variables
	var win = {
		width: $(window).width(),
		height: $(window).height()
	};
	var current = 0;
	var $m = $('#main-menu-container');
	var h = $m.find('.nav-item').height();// + 1; // take border into account ?
	var nbItems = $m.find('.nav-item').length;
	var timer;
	var availableSpace = Math.ceil(win.height*0.8 - h*2);

	// recalculate when window size change
	$(window).resize(function(){
		win = {
			width: $(window).width(),
			height: $(window).height()
		};
		availableSpace = win.height*0.8 - h*2;

		// reinit masking zone
		maskingZone($m.position().top);
		placeDownArrow();
	});

	// slide menu when mouse is hovered over "home" or "bottom" items
	$('#main-menu .menu > a').first().hover(function(){
		clearInterval(timer);
		slide(-1);
		timer = setInterval(function(){slide(-1);}, 500);
	}, function(){
		clearInterval(timer);
	});

	$('#main-menu .menu > a').last().hover(function(){
		clearInterval(timer);
		slide(1);
		timer = setInterval(function(){slide(1);}, 500);
	}, function(){
		clearInterval(timer);
	});

	/**
	* auto sliding
	*   # dir = 1 to see next chapters
	*   # dir = -1 to see previous chapters
	*/
	function slide(dir){
		if(dir == undefined) dir = 1;

		// if an animation is ongoing, let it finish
		if($m.queue().length) return;

		// do not go beyond borders
		if( (dir == 1 && isVisible(nbItems-1)) || (dir == -1 && isVisible(0)) ) return;

		// compute menu position
		var pos = $m.position().top;
		var newpos = pos - h*dir;

		$m.animate({top: newpos});
		maskingZone(newpos);
		current = Math.abs(Math.floor(newpos / h));
	}

	function maskingZone(newpos){
		// compute masking zone
		var clip = 'rect('+ (-newpos)+'px '+win.width+'px '+( availableSpace - (availableSpace % h) - newpos + 2 )+'px 0px)';
		$m.css('clip', clip);
	}

	function placeDownArrow(){
		$('#main-menu .nav-item').last().css('bottom', Math.floor(Math.max(win.height, 400)*0.8) % h);
	}

	// is the menu at this index visible ?
	function isVisible(index){
		var pos = $m.position().top;
		return ( (pos + h*index >= 1) && (pos + h*(index+1) <= availableSpace + 1) );
	}

	// make the menu at this index visible (with the minimum sliding)
	function slideFor(index){
		var loop;
		if(index > nbItems-1) index = nbItems-1;
		if(!isVisible(index)) {
			slide( index < current ? -1 : 1 );
			loop = setTimeout(function(){
				slideFor(index);
			}, 500);
		} else {
			clearTimeout(loop);
		}
	}

	// init masking zone
	maskingZone($m.position().top);
	placeDownArrow();

	// export public functions
	window.slidingMenu = {
		slideUp: function(){ slide(1) },
		slideDown: function(){ slide(-1) },
		isVisible: isVisible,
		slideFor: slideFor
	};
});
