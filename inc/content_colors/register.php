<?php

class Skrollr_Customize_Content_Colors {

	private static $instance;

	function __construct(){
		self::$instance = $this;
		add_action( 'customize_register', array( $this, 'register_settings' ) );
		add_action( 'wp_head', array( $this, 'render' ) );
		add_action( 'customize_preview_init', array( $this, 'live_preview' ) );
		add_action( 'wp_ajax_content-colors', array( $this, 'ajax_render' ) );
	}

	/**
	* Get the static instance
	* This allows access to the instance of this class without creating a global var.
	* Read more at http://hardcorewp.com/2012/enabling-action-and-filter-hook-removal-from-class-based-wordpress-plugins
	*/
	static function get_instance() {
		return self::$instance;
	}

	function register_settings( $wp_customize ) {
		if ( ! isset( $wp_customize ) ) {
			return;
		}

		$wp_customize->add_setting( 'bg_color', array(
			'default'   => '#505050',
			'transport' => 'postMessage',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_setting( 'bg_active_color', array(
			'default'   => '#b61713',
			'transport' => 'postMessage',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_setting( 'txt_color', array(
			'default'   => '#ffffff',
			'transport' => 'postMessage',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_setting( 'content_bg_color', array(
			'default'   => '#eaeaea',
			'transport' => 'postMessage',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_setting( 'content_txt_color', array(
			'default'   => '#505050',
			'transport' => 'postMessage',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_setting( 'desc_bg_color', array(
			'default'   => '#dddddd',
			'transport' => 'postMessage',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_setting( 'desc_txt_color', array(
			'default'   => '#505050',
			'transport' => 'postMessage',
			'sanitize_callback' => 'sanitize_hex_color',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bg_color', array(
			'label'   => __('Background color', 'skrollr'),
			'section' => 'colors',
			'settings' => 'bg_color'
		) ) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bg_active_color', array(
			'label'   => __('Background active color', 'skrollr'),
			'section' => 'colors',
			'settings' => 'bg_active_color'
		) ) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'txt_color', array(
			'label'   => __('Text color', 'skrollr'),
			'section' => 'colors',
			'settings' => 'txt_color'
		) ) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'content_bg_color', array(
			'label'   => __('Content background color', 'skrollr'),
			'section' => 'colors',
			'settings' => 'content_bg_color'
		) ) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'content_txt_color', array(
			'label'   => __('Content text color', 'skrollr'),
			'section' => 'colors',
			'settings' => 'content_txt_color'
		) ) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'desc_bg_color', array(
			'label'   => __('Description background color', 'skrollr'),
			'section' => 'colors',
			'settings' => 'desc_bg_color'
		) ) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'desc_txt_color', array(
			'label'   => __('Description text color', 'skrollr'),
			'section' => 'colors',
			'settings' => 'desc_txt_color'
		) ) );
	}

	function render(){
		echo '<style id="customized-content-colors">';
		$this->get_css(
			get_theme_mod('bg_color', '#505050'),
			get_theme_mod('bg_active_color', '#b61713'),
			get_theme_mod('txt_color', '#ffffff'),
			get_theme_mod('content_bg_color', '#eaeaea'),
			get_theme_mod('content_txt_color', '#505050'),
			get_theme_mod('desc_bg_color', '#dddddd'),
			get_theme_mod('desc_txt_color', '#505050')
		);
		echo '</style>';
	}

	function ajax_render(){
		$this->get_css(
			isset($_REQUEST['bg']) ? urldecode($_REQUEST['bg']) : get_theme_mod('bg_color', '#505050'),
			isset($_REQUEST['active']) ? urldecode($_REQUEST['active']) : get_theme_mod('bg_active_color', '#b61713'),
			isset($_REQUEST['txt']) ? urldecode($_REQUEST['txt']) : get_theme_mod('txt_color', '#ffffff'),
			isset($_REQUEST['content_bg']) ? urldecode($_REQUEST['content_bg']) : get_theme_mod('content_bg_color', '#eaeaea'),
			isset($_REQUEST['content_txt']) ? urldecode($_REQUEST['content_txt']) : get_theme_mod('content_txt_color', '#505050'),
			isset($_REQUEST['desc_bg']) ? urldecode($_REQUEST['desc_bg']) : get_theme_mod('desc_bg_color', '#dddddd'),
			isset($_REQUEST['desc_txt']) ? urldecode($_REQUEST['desc_txt']) : get_theme_mod('desc_txt_color', '#505050')
		);
		die();
	}

	function get_css( $bg_color, $bg_active_color, $txt_color, $content_bg_color, $content_txt_color, $desc_bg_color, $desc_txt_color) {
		$bg_color = esc_attr( $bg_color );
		$bg_active_color = esc_attr( $bg_active_color );
		$txt_color = esc_attr( $txt_color );
		$content_bg_color = esc_attr( $content_bg_color );
		$content_txt_color = esc_attr( $content_txt_color );
		$desc_bg_color = esc_attr( $desc_bg_color );
		$desc_txt_color = esc_attr( $desc_txt_color );

		$bg_active_color_dec = Skrollr_Color_Tools::rgbhex2dec($bg_active_color);
		$content_menu_border_color =  Skrollr_Color_Tools::darken_color($content_bg_color, 'low');
		$content_link_hover_color =  Skrollr_Color_Tools::darken_color($content_txt_color, 'medium');
		$content_txt_strong_color =  Skrollr_Color_Tools::darken_color($content_txt_color, 'strong');
		?>
			a {
				color: <?php echo $content_txt_color ?>;
			}

			a:hover {
				color: <?php echo $content_link_hover_color ?>;
			}

			body, .block, .one-column, footer {
				background-color: <?php echo $content_bg_color ?>;
				color: <?php echo $content_txt_color ?>;
			}

			blockquote {
				color: <?php echo $bg_color ?>;
			}

			blockquote > p:before, blockquote > p:after {
				color: <?php echo $content_txt_color ?>;
			}

			#chapo {
				background: <?php echo $desc_bg_color ?>;
				color: <?php echo $desc_txt_color ?>;
			}

			.text-column img.size-medium {
				border-color: white;
			}

			.text-column > p strong {
				color: <?php echo $content_txt_strong_color ?>;
			}

			#main-menu {
				background: <?php echo $bg_color ?>;
				color: <?php echo $txt_color ?>;
			}

			#main-menu:hover {
				background-color: <?php echo $bg_color ?>!important;
			}

			#main-menu dd, #main-menu dt {
				background: <?php echo $bg_active_color ?>;
			}

			#main-menu .menu a.current {
				border-color: <?php echo $bg_active_color ?>;
			}

			#main-menu .menu a:hover, #main-menu .menu a:hover > dt {
				background: <?php echo $bg_active_color ?>;
			}

			#main-menu a {
				color: <?php echo $txt_color ?>;
			}

			.mejs-video .mejs-controls .mejs-time-rail .mejs-time-current {
				background: <?php echo $bg_active_color ?>;
			}

			.mejs-video .mejs-controls .mejs-time-rail .mejs-time-loaded {
				background: <?php echo $txt_color ?>;
			}

			.sk-gallery  figcaption,
			.sk-video figcaption {
				background: rgba(<?php echo implode(",", $bg_active_color_dec) ?>, 0.5);
				color: <?php echo $txt_color ?>;
			}

			.partage > a {
				color: <?php echo $txt_color ?>;
			}

			.scrolldown,
			.scrolldown:hover {
				color: <?php echo $txt_color ?>;
			}

			.scrolldown:after {
				border-color: <?php echo $txt_color ?>;
			}

			.post-edit-link {
				border-color: <?php echo $bg_color ?>;
				color: <?php echo $bg_color ?>;
				background-color: <?php echo $content_bg_color ?>;
			}

			.post-edit-link:hover {
				color: <?php echo $txt_color ?>;
				background-color: <?php echo $bg_color ?>;
			}

			.page .menu .menu-item > a {
				border-color: <?php echo $content_menu_border_color ?>;
			}

			.wp-caption-text {
				color: <?php echo $content_link_hover_color ?>;
			}

		<?php
	}

	function live_preview(){
		$ver = defined('THEME_ASSETS_VERSION') ? THEME_ASSETS_VERSION : false;
		wp_enqueue_script( 'content-colors-customizer', get_template_directory_uri().'/inc/content_colors/theme_customizer.js', array( 'jquery','customize-preview' ), $ver, true);
		wp_localize_script( 'content-colors-customizer', 'ajax_url', admin_url( 'admin-ajax.php' ) );
	}
}

new Skrollr_Customize_Content_Colors;
