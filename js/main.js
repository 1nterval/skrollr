jQuery(function($){

	function init(){
		mejsHack();
		$('.block-0 > h1').slabText();
		setupBlocks();
		setupVideos();
		var s = skrollr.init({
			forceHeight: false,
			render: function(data) {

				// stop non-visible videos
				$('.sk-video.skrollable-before, .sk-video.skrollable-after').each(function(i, e){
					var player1 = $(e).data('player');
					if( player1 && player1.media ){

						if( !player1.media.paused ) {
							player1.saveTime = player1.media.currentTime;
							player1.pause();
						}

						// force video loading to stop
						if( player1.media.src == player1.saveSrc && player1.media.pluginType == 'native' && !mejs.MediaFeatures.hasTouch ) {
							player1.setSrc('');
							player1.load();
						}
					}
				});

				var $nav = $('.skrollable-between').parent('.nav-item');
				if($nav.length) {
					$('.nav-item').not($nav).removeClass('current');
					$nav.addClass('current');
					if(!$nav.parent().hasClass('menu')) slidingMenu.slideFor($nav.index());
				}

				// start visible video
				$currentVideo = $('.sk-video.skrollable-between');
				var player2 = $currentVideo.data('player');
				if( player2 && player2.media ) {
					if( player2.media.paused && !player2.pausedByUser  ) {
						// restore the video source
						if( player2.media.pluginType == 'native' && !mejs.MediaFeatures.hasTouch ) {
							player2.setSrc( player2.saveSrc );
							player2.load();
						}
						player2.play();
						player2.startControlsTimer();

						// for flash, we have to wait for canplay event
						var readyToPlay = function(){
							player2.play();
							player2.media.removeEventListener( 'canplay', readyToPlay );
						}
						player2.media.addEventListener( 'canplay', readyToPlay);

						if( player2.media.pluginType == 'native' && !mejs.MediaFeatures.hasTouch ) {
							// restore the video time
							var setSavedTime = function(){
								player2.setCurrentTime( player2.saveTime );
								player2.media.removeEventListener( 'playing', setSavedTime );
							}
							if( player2.saveTime ) {
								player2.media.addEventListener( 'playing', setSavedTime);
							}
						}
					}
				}
			}
		});
		setTimeout(s.refresh, 500);

		skrollr.menu.init(s);

		// forward click event the the video timeline
		$('.block.video').on( 'mousedown', function(e){
			var player, forwardEvent;
			if(player = $(this).data('player')) {
				if( $(window).height() - e.clientY <= 50 ) {
					try {
						forwardEvent = new MouseEvent(e.type, e);
					} catch (error) {
						forwardEvent = document.createEvent('MouseEvent');
						forwardEvent.initEvent(e.type, true, true);
					}
					player.controls.find('.mejs-time-total')[0].dispatchEvent(forwardEvent);
				}
			}
		});
		$('.block.video').click(function(e){
			var player;
			if(player = $(this).data('player')) {
				if( $(window).height() - e.clientY <= 50 ) {
					// reserved area for timeline
				} else if( player.media.paused ) {
					player.pausedByUser = false;
					player.play();
				} else {
					player.pausedByUser = true;
					player.pause();
				}
			}
		}).mousemove(function(){
			var player;
			if(player = $(this).data('player')) {
				player.showControls();
				player.startControlsTimer();
			}
		});

		// avoid moving video blocks on iOS
		$('.sk-video').on( 'touchmove', function(e) {
			e.preventDefault();
			return false;
		} );

		$('.block').not('.block-0').mousemove(throttle(displaySharing, 1000));
		$('.partage').fadeOut();

		$(window).resize(function(){
			setupBlocks();
			resizeVideos();
			s.refresh();
		});
	}

	var sharingTimer;
	// gère l'affichage des liens de partage
	function displaySharing(){
		if(sharingTimer) clearTimeout(sharingTimer);
		$('.partage').fadeIn();
		sharingTimer = setTimeout(function(){
			if( $('.partage a:hover').length == 0 ){
				$('.partage').fadeOut();
			}
		}, 1200);
	}

	// positionne les blocs statiques pour laisser de l'espace aux blocs fixes
	function setupBlocks(){
		$('.block-0').height($(window).height());
		$('.sk-gallery > figure, .wp-video').height($(window).height() + 50);
		$('.gallery .the_gallery').each(function(index, gal){
			var sel = '#' + $(gal).parent().attr('id') + ' .gal-' + $(gal).attr('data-instance');
			var $skgal = $('.sk-gallery[data-anchor-target="' + sel + '"]');
			var nbimages = $skgal.find('[name="nbimages"]').attr('content');
			$(gal).height( $(window).height() * 2 * nbimages );
		});
		$('.video').height($(window).height()*1.5);
	}

	function setupVideos(){
		$('video').mediaelementplayer({
			videoWidth: $(window).width(),
			videoHeight: $(window).height(),
			features: ['progress'],
			success: function(media, node, player) {
				player.saveSrc = media.src;
				var $vid = player.container.closest('.sk-video');
				$vid.data('player', player);
				var $block = $($vid.attr('data-anchor-target'));
				$block.data('player', player);

				player.pausedByUser = false;

				// for iPhone, article cannot be scrolled when video is playing
				// and video is paused without click event on the video element
				if( mejs.MediaFeatures.isiPhone ) {
					media.addEventListener( 'pause', function(e){
						player.pausedByUser = true;
					} );
				}
			}
		});
	}

	function resizeVideos(){
		$('.sk-video').each(function(i, video){
			var player;
			if(player = $(video).data('player')) {
				player.setPlayerSize($(window).width(), $(window).height());
				player.setControlsSize();
			}
		});
	}

	function mejsHack(){
		mejs.MediaElementPlayer.prototype.setControlsSize = function() {
			var t = this,
				usedWidth = 0,
				railWidth = 0,
				rail = t.controls.find('.mejs-time-rail'),
				total = t.controls.find('.mejs-time-total'),
				current = t.controls.find('.mejs-time-current'),
				loaded = t.controls.find('.mejs-time-loaded'),
				others = rail.siblings(),
				lastControl = others.last(),
				lastControlPosition = null;

			// skip calculation if hidden
			if (!t.container.is(':visible') || !rail.length || !rail.is(':visible')) {
				return;
			}


			// allow the size to come from custom CSS
			if (t.options && !t.options.autosizeProgress) {
				// Also, frontends devs can be more flexible
				// due the opportunity of absolute positioning.
				railWidth = parseInt(rail.css('width'), 10);
			}

			// attempt to autosize
			if (railWidth === 0 || !railWidth) {

				// find the size of all the other controls besides the rail
				others.each(function() {
					var $this = $(this);
					if ($this.css('position') != 'absolute' && $this.is(':visible')) {
						usedWidth += $(this).outerWidth(true);
					}
				});

				// fit the rail into the remaining space
				railWidth = t.controls.width() - usedWidth - (rail.outerWidth(true) - rail.width());
			}

			// resize the rail,
			// but then check if the last control (say, the fullscreen button) got pushed down
			// this often happens when zoomed
			do {
				// outer area
				rail.width(railWidth);
				// dark space
				total.width(railWidth - (total.outerWidth(true) - total.width()));

				if (lastControl.css('position') != 'absolute') {
					// HACK fabien : check that lastControl is not void
					lastControlPosition = lastControl.length ? lastControl.position() : null;
					railWidth--;
				}
			} while (lastControlPosition !== null && lastControlPosition.top > 0 && railWidth > 0);

			if (t.setProgressRail)
				t.setProgressRail();
			if (t.setCurrentRail)
				t.setCurrentRail();
		};
	}

	init();
});
