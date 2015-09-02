jQuery(function($){

	function init(){
		mejsHack();
		$('.block-0 > h1').slabText();
		setupBlocks();
		setupVideos();
		var s = skrollr.init({
			forceHeight: false,
			render: function(data) {

				// arrêter les vidéos qui ne sont pas visibles
				$('.sk-video.skrollable-before, .sk-video.skrollable-after').each(function(i, e){
					var player1 = $(e).data('player');
					if( player1 && player1.media && !player1.media.paused ) {
						player1.saveTime = player1.media.currentTime;
						player1.pause();
						// forcer l'arrêt du chargement de la vidéo
						player1.setSrc('');
						player1.load();
					}
				});

				// démarrer la vidéo visible
				$currentVideo = $('.sk-video.skrollable-between');
				var player2 = $currentVideo.data('player');
				if( player2 && player2.media && $currentVideo.css('opacity') >= 0.5 ) {
					if( player2.media.paused && !player2.pausedByUser ) {
						// restaurer la source
						player2.setSrc( player2.saveSrc );
						player2.load();
						player2.play();
						player2.startControlsTimer();

						// et le temps
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
		});
		setTimeout(s.refresh, 500);

		$('.block.video').click(function(){
			var player;
			if(player = $(this).data('player')) {
				if( player.pausedByUser ) {
					player.pausedByUser = false;
					player.play();
					$(this).find('figcaption').fadeOut();
				} else {
					player.pausedByUser = true;
					player.pause();
					$(this).find('figcaption').fadeIn();
				}
			}
		}).mousemove(function(){
			var player;
			if(player = $(this).data('player')) {
				player.showControls();
				player.startControlsTimer();
			}
		});

		$('.block').not('.block-0').mousemove(throttle(displaySharing, 1000));
		$('.partage').fadeOut();

		$(window).resize(function(){
			setupBlocks();
			resizeVideos();
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

				// cacher le titre en même temps que les contrôles
				player.container.on( 'controlshidden', function(){
					$block.find( 'figcaption' ).fadeOut();
				})
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
