<?php

class Skrollr_Customize_Layout {

	private static $instance;

	function __construct(){
		self::$instance = $this;
		add_action( 'customize_register', array( $this, 'register_settings' ) );
		add_action( 'customize_preview_init', array( $this, 'live_preview' ) );
		add_filter( 'post_class', array( $this, 'blocks_classes' ), 10, 3 );
		add_action( 'admin_init', array( $this, 'editor_style' ) );
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

		$wp_customize->add_section( 'layout', array(
			'title'      => __('Page layout', 'skrollr'),
		) );

		$wp_customize->add_setting( 'footer_menu_layout', array(
			'default'   => 'right',
			'transport' => 'postMessage',
			'sanitize_callback' => array( $this, 'sanitize_footer_menu_layout' ),
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'footer_menu_layout', array(
			'label' => __('Footer menu layout', 'skrollr'),
			'section' => 'layout',
			'settings'   => 'footer_menu_layout',
			'type' => 'select',
			'choices' => array(
				'left' => __( 'Left column', 'skrollr' ),
				'right' => __( 'Right column', 'skrollr' ),
			),
		) ) );

		$wp_customize->add_setting( 'content_width', array(
			'default'   => 4,
			'transport' => 'postMessage',
			'sanitize_callback' => 'absint',
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'content_width', array(
			'label'      => __( 'Content width', 'skrollr' ),
			'section'    => 'layout',
			'settings'   => 'content_width',
			'type' => 'range',
			'input_attrs' => array(
				'max'  => 12,
				'min'  => 2,
				'step' => 1,
			),
		) ) );

		$wp_customize->add_setting( 'logo_header', array(
			'transport' => 'postMessage',
			'sanitize_callback' => 'esc_url',
		) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'logo_header', array(
			'label'      => __( 'Homescreen logo', 'skrollr' ),
			'section'    => 'layout',
			'settings'   => 'logo_header',
		) ) );
		$wp_customize->add_setting( 'logo_header_url', array(
			'default'   => home_url(),
			'transport' => 'postMessage',
			'sanitize_callback' => 'esc_url',
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'logo_header_url', array(
			'label'      => __( 'URL pointed by logo', 'skrollr' ),
			'section'    => 'layout',
			'settings'   => 'logo_header_url',
		) ) );

		$wp_customize->add_setting( 'logo_header_position', array(
			'transport' => 'postMessage',
			'sanitize_callback' => array( Skrollr_Position_Icon::get_instance(), 'sanitize_position' ),
		) );
		$wp_customize->add_control( new Icon_Position_Custom_Control( $wp_customize, 'logo_header_position', array(
			'label'      => __( 'Logo position', 'skrollr' ),
			'description' => __( 'Drag the logo around with your mouse to change its position', 'skrollr' ),
			'section'    => 'layout',
			'settings'   => array(
				'image'    => 'logo_header',
				'url'      => 'logo_header_url',
				'position' => 'logo_header_position',
			),
			'input_attrs' => array(
				'data-icon-id' => 'logo-header',
				'data-parent-selector'  => '#accueil',
			),
		) ) );

	}

	function sanitize_footer_menu_layout( $setting ){
		if( $setting == 'right' || $setting == 'left' ) {
			return $setting;
		} else {
			return null;
		}
	}

	function live_preview(){
		$ver = defined('THEME_ASSETS_VERSION') ? THEME_ASSETS_VERSION : false;
		wp_enqueue_script( 'layout-customizer', get_template_directory_uri().'/inc/layout/theme_customizer.js', array( 'jquery','customize-preview' ), $ver, true);
	}

	function get_layout_classes(){
		$content_width = get_theme_mod('content_width', 4);
		$content_class = $title_class = array();

		// main content width
		$content_class[] = "col-xs-12";
		$content_class[] = "col-md-$content_width";

		// center content if needed
		if( $content_width > 8 && $content_width < 12 ) {
			$content_class[] = "col-md-offset-" . ceil(6 - $content_width/2);
		}

		// title
		$title_class[] = "col-xs-12";
		$title_class[] = "col-md-" . ($content_width > 8 ? 10 : 4);

		return array(
			'content' => join( ' ', $content_class ),
			'title' => join( ' ', $title_class ),
		);
	}

	/**
	* Add custom classes for each block (post)
	*/
	function blocks_classes($classes, $list, $postID) {
		$post = get_post($postID);

		$classes[] = 'block';

		// custom post type classes
		$classes[] = $post->post_type == 'post' ? 'text' : $post->post_type;

		$format = get_post_format();
		if( $format == 'gallery' || $format == 'video' ) {
			// custom post format classes
			$classes[] = $format;
		} else {
			$classes[] = 'one-column';
			$classes[] = 'clearfix row';
		}

		return $classes;
	}

	/**
	* Enqueue editor style for TinyMce
	*/
	function editor_style(){
		add_editor_style( 'css/editor-style.css' );
	}
}

new Skrollr_Customize_Layout;
