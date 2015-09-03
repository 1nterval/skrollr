<?php
/**
* Shortcode customisation management
*/
class Skrollr_Shortcodes {

	private static $instance;
	public $gallery_class = 'Skrollr_Gallery';

	function __construct(){
		self::$instance = $this;
		add_filter( 'wp_video_shortcode_override', array( $this, 'video_shortcode'), 10, 4);
		add_filter( 'post_gallery', array( $this, 'gallery_shortcode'), 10, 2 );
		add_action( 'admin_footer', array( $this, 'gallery_settings_template' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'gallery_settings_template' ) );
	}

	/**
	* Get the static instance
	* This allows access to the instance of this class without creating a global var.
	* Read more at http://hardcorewp.com/2012/enabling-action-and-filter-hook-removal-from-class-based-wordpress-plugins
	*/
	static function get_instance() {
		return self::$instance;
	}

	/**
	* Remove any shortcodes from the provided content
	*/
	function remove_shortcodes($content){
		return strip_shortcodes( $content );
	}

	/**
	* Extract any shortcodes by removing all other text from the provided content
	*/
	function extract_shortcodes($content){
		$c = '';
		preg_match_all( '/' . get_shortcode_regex() . '/s', $content, $matches, PREG_SET_ORDER );
		foreach ( $matches as $shortcode ) {
			$c .= $shortcode[0];
		}
		return $c;
	}

	/**
	 * Overide the gallery settings template used in the media manager to add custom settings.
	 */
	function gallery_settings_template(){
		global $pagenow;
		if( $pagenow != 'post.php' ) return;

		$post = get_post();
		if( get_post_format( $post->ID ) != 'gallery' ) return;

		?><script type="text/html" id="tmpl-skrollr-gallery-settings">
			<h3><?php _e('Gallery Settings', 'skrollr'); ?></h3>

			<?php // Skrollr : new setting ?>
			<label class="setting">
				<span><?php _e( 'Gallery type', 'skrollr' ); ?></span>
				<br/><br/>
				<label><input type="radio" name="skrollr" data-setting="skrollr" value="meld" <#
							if ( data.model.skrollr == 'meld' ) { #>checked="checked"<# }
						#>><?php _e( 'Melded', 'skrollr' ); ?> <img src="<?php echo get_template_directory_uri().'/img/galerie-meld.gif' ?>"/></label>
				<label><input type="radio" name="skrollr" data-setting="skrollr" value="move-up"<#
							if ( data.model.skrollr == 'move-up' ) { #>checked="checked"<# }
						#>><?php _e( 'Rolling up', 'skrollr' ); ?> <img src="<?php echo get_template_directory_uri().'/img/galerie-move-up.gif' ?>"/></label>
				<label><input type="radio" name="skrollr" data-setting="skrollr" value="move-down"<#
							if ( data.model.skrollr == 'move-down' ) { #>checked="checked"<# }
						#>><?php _e( 'Rolling down', 'skrollr' ); ?> <img src="<?php echo get_template_directory_uri().'/img/galerie-move-down.gif' ?>"/></label>
				<label><input type="radio" name="skrollr" data-setting="skrollr" value="volet"<#
							if ( data.model.skrollr == 'volet' ) { #>checked="checked"<# }
						#>><?php _e( 'Shutter', 'skrollr' ); ?> <img src="<?php echo get_template_directory_uri().'/img/galerie-volet.gif' ?>"/></label>
			</label>
		</script>
		<?php
		
		// Script to set the gallery-settings template to the Skrollr custom one
		wp_enqueue_script('skrollr-gallery-settings', get_template_directory_uri().'/js/gallery-settings.js', array('media-views'));
	}

	/**
	* The Video shortcode.
	* Pretty much a copy-paste of the wp_video_shortcode function from wp-includes/media.php
	*/
	function video_shortcode( $html, $attr, $content, $instances ){
		global $content_width;
		$post_id = get_post() ? get_the_ID() : 0;

		// do not modify video display on the admin side
		if( is_admin() ) return '';

		static $instances = 0;
		$instances++;
		$video = null;

		$default_types = wp_get_video_extensions();
		$defaults_atts = array(
			'src'      => '',
			'poster'   => '',
			'loop'     => '',
			'autoplay' => '',
			'preload'  => 'metadata',
			'width'    => 640,
			'height'   => 360,
		);

		foreach ( $default_types as $type ) {
			$defaults_atts[$type] = '';
		}

		$atts = shortcode_atts( $defaults_atts, $attr, 'video' );

		if ( is_admin() ) {
			// shrink the video so it isn't huge in the admin
			if ( $atts['width'] > $defaults_atts['width'] ) {
				$atts['height'] = round( ( $atts['height'] * $defaults_atts['width'] ) / $atts['width'] );
				$atts['width'] = $defaults_atts['width'];
			}
		} else {
			// if the video is bigger than the theme
			if ( ! empty( $content_width ) && $atts['width'] > $content_width ) {
				$atts['height'] = round( ( $atts['height'] * $content_width ) / $atts['width'] );
				$atts['width'] = $content_width;
			}
		}

		$yt_pattern = '#^https?://(?:www\.)?(?:youtube\.com/watch|youtu\.be/)#';
		// Skrollr : add Dailymotion pattern
		$dm_pattern = '#^https?://(?:www\.)?(?:dailymotion\.com/video/|dai\.ly/)#';
		// End Skrollr

		$primary = false;
		if ( ! empty( $atts['src'] ) ) {
			if ( ! preg_match( $yt_pattern, $atts['src'] ) && ! preg_match( $dm_pattern, $atts['src'] ) ) { // Skrollr : add Dailymotion pattern
				$type = wp_check_filetype( $atts['src'], wp_get_mime_types() );
				if ( ! in_array( strtolower( $type['ext'] ), $default_types ) ) {
					return sprintf( '<a class="wp-embedded-video" href="%s">%s</a>', esc_url( $atts['src'] ), esc_html( $atts['src'] ) );
				}
			}
			$primary = true;
			array_unshift( $default_types, 'src' );
		} else {
			foreach ( $default_types as $ext ) {
				if ( ! empty( $atts[ $ext ] ) ) {
					$type = wp_check_filetype( $atts[ $ext ], wp_get_mime_types() );
					if ( strtolower( $type['ext'] ) === $ext ) {
						$primary = true;
					}
				}
			}
		}

		if ( ! $primary ) {
			$videos = get_attached_media( 'video', $post_id );
			if ( empty( $videos ) ) {
				return;
			}

			$video = reset( $videos );
			$atts['src'] = wp_get_attachment_url( $video->ID );
			if ( empty( $atts['src'] ) ) {
				return;
			}

			array_unshift( $default_types, 'src' );
		}

		/**
		 * Filter the class attribute for the video shortcode output container.
		 *
		 * @since 3.6.0
		 *
		 * @param string $class CSS class or list of space-separated classes.
		 */
		$html_atts = array(
			'class'    => apply_filters( 'wp_video_shortcode_class', 'wp-video-shortcode' ),
			'id'       => sprintf( 'video-%d-%d', $post_id, $instances ),
			// Skrollr : video size must be 100%
			'width'    => '100%',//absint( $atts['width'] ),
			'height'   => '100%',//absint( $atts['height'] ),
			'poster'   => esc_url( $atts['poster'] ),
			'loop'     => $atts['loop'],
			'autoplay' => $atts['autoplay'],
			'preload'  => 'none',
		);

		// These ones should just be omitted altogether if they are blank
		foreach ( array( 'poster', 'loop', 'autoplay', 'preload' ) as $a ) {
			if ( empty( $html_atts[$a] ) ) {
				unset( $html_atts[$a] );
			}
		}

		$attr_strings = array();
		foreach ( $html_atts as $k => $v ) {
			$attr_strings[] = $k . '="' . esc_attr( $v ) . '"';
		}

		$html = '';
		$html .= sprintf( '<video %s controls="controls">', join( ' ', $attr_strings ) );

		$fileurl = '';
		$source = '<source type="%s" src="%s" />';
		foreach ( $default_types as $fallback ) {
			if ( ! empty( $atts[ $fallback ] ) ) {
				if ( empty( $fileurl ) ) {
					$fileurl = $atts[ $fallback ];
				}
				if ( 'src' === $fallback && preg_match( $yt_pattern, $atts['src'] ) ) {
					$type = array( 'type' => 'video/youtube' );
				} else if ( 'src' === $fallback && preg_match( $dm_pattern, $atts['src'] ) ) { // Skrollr : add Dailymotion pattern
					$type = array( 'type' => 'video/dailymotion' );
				} else {
					$type = wp_check_filetype( $atts[ $fallback ], wp_get_mime_types() );
				}
				// Skrollr : remove '_' in URL query args
				$url = $atts[ $fallback ];
				$html .= sprintf( $source, $type['type'], esc_url( $url ) );
			}
		}

		if ( ! empty( $content ) ) {
			if ( false !== strpos( $content, "\n" ) ) {
				$content = str_replace( array( "\r\n", "\n", "\t" ), '', $content );
			}
			$html .= trim( $content );
		}

		$html .= wp_mediaelement_fallback( $fileurl );
		$html .= '</video>';

		// Skrollr : remove width and height rules
		return sprintf( '<div class="wp-video">%s</div>', $html );
	}

	/**
	* The Gallery shortcode.
	* Pretty much a copy-paste of the wp_video_shortcode function from wp-includes/media.php
	*/
	function gallery_shortcode( $output, $attr ){
		$post = get_post();
		if( get_post_format( $post->ID ) != 'gallery' ) return;

		static $instance = 0;
		$instance++;

		// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
			if ( ! $attr['orderby'] ) {
				unset( $attr['orderby'] );
			}
		}

		$atts = shortcode_atts( array(
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'id'         => $post ? $post->ID : 0,
			// Skrollr: remove unused backward compatibility code
			'size'       => 'thumbnail',
			'include'    => '',
			'exclude'    => '',
			'link'       => '',
			// Skrollr: add custom attribute
			'skrollr'    => 'meld'
		), $attr, 'gallery' );

		$id = intval( $atts['id'] );
		if ( 'RAND' == $atts['order'] ) {
			$atts['orderby'] = 'none';
		}

		if ( ! empty( $atts['include'] ) ) {
			$_attachments = get_posts( array( 'include' => $atts['include'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );

			$attachments = array();
			foreach ( $_attachments as $key => $val ) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		} elseif ( ! empty( $atts['exclude'] ) ) {
			$attachments = get_children( array( 'post_parent' => $id, 'exclude' => $atts['exclude'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
		} else {
			$attachments = get_children( array( 'post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
		}

		if ( empty( $attachments ) ) {
			return '';
		}

		// Skrollr: remove unused backward compatibility code

		$gallery = new $this->gallery_class($post, $attachments, $atts, $instance);
		$output .= $gallery->render();

		return $output;
	}

}

new Skrollr_Shortcodes;

class Skrollr_Gallery {
	private $post;
	private $attachments;
	private $atts;
	private $instance;

	function __construct($post, $attachments, $atts, $instance) {
		$this->post = $post;
		$this->attachments = $attachments;
		$this->atts = $atts;
		$this->instance = $instance;
	}

	function render() {
		$output = '<meta name="nbimages" content="' . sizeof($this->attachments) . '"/>';

		$i = 0;
		$style = '';
		$legend_style = '';
		foreach ( $this->attachments as $id => $attachment ) {
			list( $image_url ) = wp_get_attachment_image_src( $id, 'fullsize', false );
			$image_meta  = wp_get_attachment_metadata( $id );

			$output .= '<figure class="' . esc_attr($this->atts['skrollr']) . '" ';

			// version "fondu" :
			if( $this->atts['skrollr'] == 'meld' ) {
				if($i > 0) $output .= 'data--' . ( ($i*2-1)*100+80 ) . 'p-center-top="opacity:0" ';
				$output .= 'data--' . ($i*2*100) . 'p-center-top="opacity:1" ';
			}

			// version déroulé de bas en haut
			if( $this->atts['skrollr'] == 'move-up' ) {
				$output .= 'data--' . ( ($i*2)*100 ) . 'p-bottom-top="top:100%" ';
				$output .= 'data--' . ( ($i*2+1)*100 ) . 'p-bottom-top="top:0%" ';
			}

			// version déroulé de haut en bas
			if( $this->atts['skrollr'] == 'move-down' ) {
				$output .= 'data--' . ( ($i*2+2)*100 ) . 'p-bottom-top="top:0%" ';
				if($i != sizeof($this->attachments)-1) 
					$output .= 'data--' . ( ($i*2+3)*100 ) . 'p-bottom-top="top:100%" ';
				$style = 'z-index:'. ( 100 + sizeof($this->attachments) - $i ) . ';';
			}

			if( $this->atts['skrollr'] == 'volet' ) {
				$output .= 'data--' . ( ($i*2)*100 ) . 'p-bottom-top="top:100%" ';
				$output .= 'data--' . ( ($i*2+1)*100 ) . 'p-bottom-top="top:0%" ';
				$legend_style = 'position:absolute;top:0;';
			}

			$output .= 'style="' . esc_attr($style);
			if( $this->atts['skrollr'] != 'volet' ) {
				$output .= ' background-image:url(' . esc_url($image_url) . ');';
			}
			$output .= '" ';
			$output .= 'data-anchor-target="#' . esc_attr($this->post->post_name) . ' .gal-' . $this->instance . '">';

			if( $this->atts['skrollr'] == 'volet' ) {
				$output .= '<img style="width:100%;" src="' . esc_url($image_url) . '"/>';
			}

			if( trim($attachment->post_excerpt) || trim($attachment->post_title) ) {
				$output .= '<figcaption style="' . esc_attr($legend_style) . '" ';
				$output .= 'data--' . ( ($i*2)*100 ) . 'p-bottom-top="opacity:1" ';
				$output .= 'data--' . ( ($i*2+1)*100 ) . 'p-bottom-top="opacity:1" ';
				$output .= 'data--' . ( ($i*2+1)*100 + 20 ) . 'p-bottom-top="opacity:0" ';
				$output .= 'data-anchor-target="#' . esc_attr($this->post->post_name) . ' .gal-' . $this->instance . '"';
				$output .= '>';
				if( trim($attachment->post_title) ) {
					$output .= '<h2>' . $attachment->post_title . '</h2>';
				}
				if( trim($attachment->post_excerpt) ) {
					$output .= '<p>' . $attachment->post_excerpt . '</p>';
				}
				$output .= '</figcaption>';
			}
			$output .= '</figure>';

			$output .= "\n";
			$i++;

		}
		return $output;
	}
}

