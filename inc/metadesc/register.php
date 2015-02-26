<?php

/**
* Provide a long description for the site (in addition to title and slogan)
*/
class Scrollr_Header_Description {

	private static $instance;

	function __construct(){
		self::$instance = $this;
		add_action( 'customize_register', array( $this, 'register_settings' ) );
		add_filter( 'pre_update_option_wpseo_titles', array( $this, 'sync_theme_mod' ), 10, 2 );
		add_action( 'customize_preview_init', array( $this, 'live_preview' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'link_to_customizer' ) );
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

		// meta description setting
		$wpseo_titles = get_option( 'wpseo_titles' );
		$wp_customize->add_setting( 'metadesc', array(
			'default'   => $wpseo_titles['metadesc-home-wpseo'], // default to WP SEO meta desc
			'transport' => 'postMessage',
			'sanitize_callback' => array( $this, 'sync_back_option')
		) );

		$wp_customize->add_control( 'metadesc', array(
			'label'    => __('Meta description', 'skrollr'),
			'section'  => 'title_tagline',
			'settings' => 'metadesc',
			'type'     => 'textarea',
			'priority' => 200
		) );

	}

	// When WPSEO option is updated, sync theme mod
	function sync_theme_mod( $value, $old_value ){
		if( $value['metadesc-home-wpseo'] != $old_value['metadesc-home-wpseo'] ){
			set_theme_mod( 'metadesc', sanitize_text_field( $value['metadesc-home-wpseo'] ) );
		}
		return $value;
	}

	// When customizer Save & Publish button is clicked and metadesc is updated,
	// sync it back to WPSEO option
	function sync_back_option( $value ){
		$value = sanitize_text_field( $value );
		$option = get_option( 'wpseo_titles' );
		if( isset( $option['metadesc-home-wpseo'] ) ) {
			$option['metadesc-home-wpseo'] = $value;
			update_option( 'wpseo_titles', $option );
		}
		return $value;
	}

	function live_preview(){
		$ver = defined('THEME_ASSETS_VERSION') ? THEME_ASSETS_VERSION : false;
		wp_enqueue_script( 'wpseo-customizer', get_template_directory_uri().'/inc/metadesc/theme_customizer.js', array( 'jquery','customize-preview' ), $ver, true);
	}

	function link_to_customizer(){
		$ver = defined('THEME_ASSETS_VERSION') ? THEME_ASSETS_VERSION : false;
		if( !is_customize_preview() && current_user_can( 'edit_theme_options' ) ) {
			wp_enqueue_script( 'wpseo-customizer-link', get_template_directory_uri().'/inc/metadesc/customizer_link.js', array( 'jquery' ), $ver, true);
			wp_localize_script( 'wpseo-customizer-link', 'customizer_url', admin_url( 'customize.php') );
		}
	}

	function get_desc(){
		if( is_404() ){
			$desc = __( "Sorry, but the page you were trying to view does not exist. \n\nIt looks like this was the result of either a mistyped address or an out-of-date link.", 'skrollr' );
		} else if( is_category() ){
			$desc = category_description();
		} else if( is_tag() ){
			$desc = tag_description();
		} else if( is_search() ){
			global $wp_query;
			if( $wp_query->found_posts == 0 ){
				$desc = __( 'Nothing found', 'skrollr' );
			} else {
				$desc = sprintf( _n( 'One post found.', '%d posts found', $wp_query->found_posts, 'skrollr' ), $wp_query->found_posts );
			}
		} else {
			$desc = get_theme_mod( 'metadesc' );
		}
		return wpautop( "$desc" );
	}

	function get_title(){
		if( is_404() ){
			$title = __( "404, Page not found", 'skrollr' );
		} else if( is_category() ){
			$title = single_cat_title( '', false );
		} else if( is_tag() ){
			$title = single_tag_title( '', false );
		} else if( is_search() ){
			$title = sprintf( __( 'Search results for "%s"', 'skrollr' ), get_search_query() );
		} else {
			$title = trim( get_bloginfo( 'description' ) );
		}
		return $title != '' ? "<h2>$title</h2>" : '';
	}
}

new Scrollr_Header_Description;
