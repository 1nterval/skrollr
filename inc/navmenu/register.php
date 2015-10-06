<?php
/**
* Main navigation menu management
* The items displayed on the page are the one listed in the 'main' menu.
*/
class Skrollr_Nav_Menu {
	private static $instance;
	private $ver;

	function __construct(){
		self::$instance = $this;

		// To use this, define the THEME_ASSETS_VERSION constant in the wp-config.php (or wherever you want)
		$this->ver = defined('THEME_ASSETS_VERSION') ? THEME_ASSETS_VERSION : false;

		add_action( 'get_header', array( $this, 'display_nav_menu') );
		add_action( 'customize_register', array( $this, 'register_settings' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'print_assets' ) );
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

	function display_nav_menu(){
		if( $this->is_set() || is_customize_preview() ) :
			$bg_color = get_theme_mod('bg_color', '#505050');
			$bg_color_dec = Skrollr_Color_Tools::rgbhex2dec($bg_color);

			// translators: Anchor for top of the page, this string will appear in a URL
			$top_anchor = _x( 'top', 'anchor', 'skrollr' );
			// translators: Anchor for bottom of the page, this string will appear in a URL
			$bottom_anchor = _x( 'bottom', 'anchor', 'skrollr' );

			?><nav id="main-menu" class="icons icomoon-menu thin" 
					 data-10="background-color:rgba(<?php echo implode(',', $bg_color_dec) ?>,1)"
					data-200="background-color:rgba(<?php echo implode(',', $bg_color_dec) ?>,0)"
				data-200-end="background-color:rgba(<?php echo implode(',', $bg_color_dec) ?>,0)"
					data-40-end="background-color:rgba(<?php echo implode(',', $bg_color_dec) ?>,1)"
				>
				<dl class="menu">

					<a href="#<?php echo $top_anchor; ?>" class="nav-item current">
						<dt class="icons icomoon-home" data-bottom-top="display:none" data-center-top="display:block" data-top-top="display:none" data-anchor-target="#<?php echo $top_anchor; ?>"></dt>
						<dd><?php echo $this->fix_breakable_space( __( 'Top', 'skrollr' ) ); ?></dd>
					</a>

					<div id="main-menu-container" class="menu-menu-container">
					<?php 
						$i = 0;
						global $post;
						add_filter( 'the_title', array( $this, 'fix_breakable_space' ) );
						while ( have_posts() ) {
							$i++;
							the_post();
							?><a href="#<?php echo $post->post_name; ?>" class="nav-item">
								<dt data--10-bottom-top="display:none" data-center-top="display:block" data-top-top="display:none" data-anchor-target="#<?php echo $post->post_name; ?>"><?php echo $i ?></dt>
								<dd><?php the_title() ?></dd>
							</a><?php
						}
						rewind_posts();
						remove_filter( 'the_title', array( $this, 'fix_breakable_space' ) );
					?>

					</div>
					<a href="#<?php echo $bottom_anchor; ?>" class="nav-item">
						<dt class="icons icomoon-down-arrow" data-200-end="display:none" data-40-end="display:block"></dt>
					</a>
				</dl>
			</nav><?php
			if( !$this->is_set() ) : ?><style>
				#main-menu{ display: none; }
			</style><?php endif;
		endif;
	}

	function print_assets(){
		if( $this->is_set() || is_customize_preview() ) {
			if(! defined('SCRIPT_DEBUG') || SCRIPT_DEBUG || ! file_exists(dirname(__FILE__).'/../../build/jsbuild.php')){
				wp_enqueue_script('skrollr-sliding', get_template_directory_uri().'/inc/navmenu/sliding-menu.js', array('jquery'), $this->ver, true);
			}
		}
	}

	function register_settings( $wp_customize ) {
		if ( ! isset( $wp_customize ) ) {
			return;
		}

		// Checkbox to display or hide navigation menu
		$wp_customize->add_setting( 'display_menu', array(
			'default'   => true,
			'transport' => 'postMessage',
			'sanitize_callback' => array( $this, 'sanitize_boolean' ),
		) );
		$wp_customize->add_control( 'display_menu', array(
			'settings' => 'display_menu',
			'label'    => __( 'Display main navigation menu', 'skrollr' ),
			'section'  => 'menu_locations',
			'type'     => 'checkbox',
		) );
	}

	function live_preview(){
		wp_enqueue_script( 'skrollr-navmenu-customizer', get_template_directory_uri().'/inc/navmenu/theme_customizer.js', array( 'jquery','customize-preview' ), $this->ver, true);
	}

	function sanitize_boolean( $value ){
		return $value == true;
	}

	function is_set(){
		return get_theme_mod( 'display_menu', true );
	}

	/**
	* Replace dashes and spaces by non-breakable equivalents to avoid having titles on two lines
	*/
	function fix_breakable_space( $text ) {
		$text = str_replace(' ', '&nbsp;', $text);
		$text = str_replace('-', '&#8209;', $text);
		return $text;
	}

}

new Skrollr_Nav_Menu;
