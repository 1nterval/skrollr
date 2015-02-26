<?php

add_action('after_setup_theme', 'skrollr_translate');
function skrollr_translate(){
	load_theme_textdomain('skrollr', get_template_directory() . '/languages');
}

add_action('after_setup_theme', 'skrollr_theme_features');
function skrollr_theme_features(){
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'post-formats', array('gallery', 'video', 'audio') );
	add_theme_support( 'title-tag' );
	add_theme_support( 'menus' );
	add_theme_support( 'automatic-feed-links' );
}

add_action('wp_head', 'skrollr_head', 0);
function skrollr_head(){
?>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width">
<?php
}

add_action('wp_enqueue_scripts', 'skrollr_print_assets');
function skrollr_print_assets(){
	// To use this, define the THEME_ASSETS_VERSION constant in the wp-config.php (or wherever you want)
	$ver = defined('THEME_ASSETS_VERSION') ? THEME_ASSETS_VERSION : false;

	if( (defined('CSS_DEBUG') && !CSS_DEBUG && file_exists(dirname(__FILE__).'/build/cssbuild.php')) ) {
		// stylesheets (minified version)
		require(dirname(__FILE__).'/build/cssbuild.php');
		wp_enqueue_style('cssbuild', get_stylesheet_directory_uri().'/css/'.CSSBUILD.'.min.css', array('mediaelement'), null);
	} else {
		wp_enqueue_style('bootstrap-grid', get_stylesheet_directory_uri().'/css/libs/bootstrap-grid.css', array(), '3.3.1');
		// stylesheets (follow SMACSS guidelines)
		wp_enqueue_style('base', get_stylesheet_directory_uri().'/css/base.css', array('bootstrap-grid', 'mediaelement'), $ver);
		wp_enqueue_style('layout', get_stylesheet_directory_uri().'/css/layout.css', array('base'), $ver);
		wp_enqueue_style('module', get_stylesheet_directory_uri().'/css/module.css', array('layout'), $ver);
		wp_enqueue_style('state', get_stylesheet_directory_uri().'/css/state.css', array('module'), $ver);
		wp_enqueue_style('theme', get_stylesheet_directory_uri().'/css/theme.css', array('state'), $ver);
	}

	wp_enqueue_script('modernizr', get_template_directory_uri().'/js/libs/modernizr-2.8.3.min.js', array(), '2.8.3', false);

	if(defined('SCRIPT_DEBUG') && !SCRIPT_DEBUG && file_exists(dirname(__FILE__).'/build/jsbuild.php')){
		// scripts (minified version)
		require(dirname(__FILE__).'/build/jsbuild.php');
		wp_enqueue_script('main', get_stylesheet_directory_uri().'/js/'.JSBUILD.'.min.js', array('jquery', 'mediaelement'), null, true);
	} else {
		wp_enqueue_script('skrollr', get_template_directory_uri().'/js/libs/skrollr.min.js', array(), '0.6.29', true);
		wp_enqueue_script('plugins', get_template_directory_uri().'/js/plugins.js', array(), $ver, true);
		wp_enqueue_script('slabtext', get_template_directory_uri().'/js/libs/jquery.slabtext.js', array('jquery'), '2.2.0', true);
		wp_enqueue_script('main', get_template_directory_uri().'/js/main.js', array( 'plugins', 'jquery', 'skrollr', 'mediaelement', 'slabtext'), $ver, true);
	}

	if ( is_singular() ) wp_enqueue_script( "comment-reply" );
}

// include features
foreach( array( 'footermenu', 'shortcodes', 'header_background', 'color_tools', 'content_colors', 'positionIcon', 'metadesc', 'layout', 'metadata' ) as $feature ) {
	$file = sprintf( '%s/inc/%s.php', get_template_directory(), $feature );
	$rep = sprintf( '%s/inc/%s/register.php', get_template_directory(), $feature );
	if( file_exists( $file ) ) {
		require_once( $file );
	} else if( file_exists( $rep ) ) {
		require_once( $rep );
	}
}

?>
