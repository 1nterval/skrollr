<?php

/**
* Tweak main site title (position, size and font)
*/
class Skrollr_Title {

	private static $instance;
	private $ver;

	function __construct(){
		self::$instance = $this;

		// To use this, define the THEME_ASSETS_VERSION constant in the wp-config.php (or wherever you want)
		$this->ver = defined('THEME_ASSETS_VERSION') ? THEME_ASSETS_VERSION : false;

		add_action( 'customize_register', array( $this, 'register_settings' ) );
		add_action( 'customize_preview_init', array( $this, 'live_preview' ) );
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

		$wp_customize->add_setting( 'title_width', array(
			'default'   => 4,
			'transport' => 'postMessage',
			'sanitize_callback' => 'absint',
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'title_width', array(
			'label'      => __( 'Title width', 'skrollr' ),
			'section'    => 'title_tagline',
			'settings'   => 'title_width',
			'type' => 'range',
			'input_attrs' => array(
				'max'  => 6,
				'min'  => 1,
				'step' => 1,
			),
			'priority' => 200,
		) ) );

	}

	function live_preview(){
		wp_enqueue_script( 'title-customizer', get_template_directory_uri().'/inc/title/theme_customizer.js', array( 'jquery','customize-preview' ), $this->ver, true);
	}

	function get_layout_classes(){
		$title_width = get_theme_mod('title_width', 4);
		$title_class = array();

		// title width
		$title_class[] = "col-xs-12";
		$title_class[] = "col-sm-" . ($title_width * 2);

		// center title
		$title_class[] = "col-sm-offset-" . (6 - $title_width);

		return join( ' ', $title_class );
	}

}

new Skrollr_Title;
