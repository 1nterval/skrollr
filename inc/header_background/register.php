<?php

class Skrollr_Customize_Header_Background {

	private static $instance;

	function __construct(){
		self::$instance = $this;
		add_action( 'customize_register', array( $this, 'register_settings' ) );
		add_action( 'after_setup_theme', array( $this, 'add_theme_support' ) );
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

		$wp_customize->remove_control( 'show_on_front' );
		$wp_customize->remove_section( 'static_front_page' );

		$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
		$wp_customize->get_setting( 'header_image' )->transport = 'postMessage';
	}

	function add_theme_support() {
		add_theme_support( 'custom-header', array(
			'wp-head-callback'	=> array( $this, 'render' ),
			'default-text-color' => 'FFFFFF',
			'height'            => 1000,
			'width'             => 1500,
			'flex-height'       => true,
			'flex-width'        => true,
		) );
		register_default_headers( array(
			'magic_book' => array(
				'url' => '%s/img/magic_book_by_colgreyis.jpg',
				'thumbnail_url' => '%s/img/magic_book_by_colgreyis.jpg',
				'description' => __( 'Magic Book', 'skrollr' ),
			),
		) );

		set_post_thumbnail_size( 1500, 1000 );
	}

	/**
	* Retrieve header image for custom header but use post thumbnail if available
	*/
	function get_header_image() {
		if( is_single() && has_post_thumbnail() ) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id(), array(1500, 1000) );
			return $image[0];
		} else {
			return get_header_image();
		}
	}

	function render(){
		?><style>
			.block-0 {
				background-image:url(<?php echo $this->get_header_image() ?>);
			}

			.block-0 > h1 {<?php if( !display_header_text() ) : ?>
				display: none;
			<?php else: ?>
				color: #<?php echo get_header_textcolor() ?>;
			<?php endif; ?>}
		</style><?php
	}

	function live_preview(){
		$ver = defined('THEME_ASSETS_VERSION') ? THEME_ASSETS_VERSION : false;
		wp_enqueue_script( 'header-customizer', get_template_directory_uri().'/inc/header_background/theme_customizer.js', array( 'jquery','customize-preview' ), $ver, true);
	}
}

new Skrollr_Customize_Header_Background;
