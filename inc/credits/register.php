<?php
/**
* Display credits in the footer
*/
class Skrollr_Credits {

	private static $instance;
	private $default_title, $default_content;

	function __construct(){
		self::$instance = $this;

		add_action( 'get_footer', array( $this, 'display' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'customize_register', array( $this, 'register_settings' ) );
		add_action( 'customize_preview_init', array( $this, 'live_preview' ) );
		add_action( 'after_setup_theme', array( $this, 'set_default' ) );
	}

	/**
	* Get the static instance
	* This allows access to the instance of this class without creating a global var.
	* Read more at http://hardcorewp.com/2012/enabling-action-and-filter-hook-removal-from-class-based-wordpress-plugins
	*/
	static function get_instance() {
		return self::$instance;
	}

	function set_default(){
		$this->default_title = __( 'Credits', 'skrollr' );
		$this->default_content = __( '<h2>Credits</h2>
WordPress theme by <a href="http://www.1nterval.com" target="_blank">1nterval</a>
Development : <a href="http://fab1en.github.io/" target="_blank">Fabien Quatravaux</a>
Graphic Design: <a href="http://www.ostrogo.fr" target="_blank">Gauthier Mesnil-Blanc</a>', 'skrollr' );
	}

	function register_settings( $wp_customize ) {
		if ( ! isset( $wp_customize ) ) {
			return;
		}

		$wp_customize->add_section( 'credits', array(
			'title'      => __('Credits', 'skrollr'),
		) );

		// Checkbox to enable or disable credits popup
		$wp_customize->add_setting( 'credits_activate', array(
			'default'   => false,
			'transport' => 'postMessage',
			'sanitize_callback' => array( $this, 'sanitize_boolean' ),
		) );
		$wp_customize->add_control( 'credits_activate', array(
			'settings' => 'credits_activate',
			'label'    => __( 'Display credits popup', 'skrollr' ),
			'section'  => 'credits',
			'type'     => 'checkbox',
		) );

		// Title of the link.
		// This is also used to generate the slug to display credits
		$wp_customize->add_setting( 'credits_link_title', array(
			'default'   => $this->default_title,
			'transport' => 'postMessage',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'credits_link_title', array(
			'settings' => 'credits_link_title',
			'label'    => __( 'Credits link title', 'skrollr' ),
			'section'  => 'credits',
		) );

		// HTML content of the popup
		$wp_customize->add_setting( 'credits_content', array(
			'default'   => $this->default_content,
			'sanitize_callback' => array( $this, 'sanitize_html' ),
		) );
		$wp_customize->add_control( 'credits_content', array(
			'settings' => 'credits_content',
			'label'    => __( 'Credits popup content', 'skrollr' ),
			'section'  => 'credits',
			'type'     => 'textarea',
		) );
	}

	function live_preview(){
		$ver = defined('THEME_ASSETS_VERSION') ? THEME_ASSETS_VERSION : false;
		wp_enqueue_script( 'credits-customizer', get_template_directory_uri().'/inc/credits/theme_customizer.js', array( 'jquery','customize-preview' ), $ver, true);
	}

	function sanitize_html( $content ){
		// strip evil scripts
		return wp_kses( $content, wp_kses_allowed_html( 'post' ) );;
	}

	function sanitize_boolean( $value ){
		return $value == true;
	}

	function is_set(){
		return get_theme_mod( 'credits_activate', false );
	}

	function get_content(){
		$content = get_theme_mod( 'credits_content', $this->default_content );

		// sanitization
		$content = $this->sanitize_html( $content );
		// add paragraphs
		$content = wpautop( $content );
		return $content;
	}

	function get_slug(){
		return sanitize_title( $this->get_title() );
	}

	function get_title(){
		return get_theme_mod( 'credits_link_title', $this->default_title );
	}

	function assets(){
		// To use this, define the THEME_ASSETS_VERSION constant in the wp-config.php (or wherever you want)
		$ver = defined('THEME_ASSETS_VERSION') ? THEME_ASSETS_VERSION : false;
		wp_enqueue_script( 'modal', get_template_directory_uri() . '/inc/credits/modal.js', array( 'jquery' ), $ver, true);
		wp_enqueue_style( 'modal', get_template_directory_uri() . '/inc/credits/modal.css', array(), $ver);
	}

	function display(){
	if( $this->is_set() || is_customize_preview() ) : ?>
		<div id="<?php echo $this->get_slug(); ?>">
			<div class="modal">
				<div class="modal-background"></div>
				<div class="modal-display">
					<?php echo $this->get_content(); ?>
					<a href="#" class="modal-close ir" title="<?php _e( 'Close', 'skrollr' ); ?>"><?php _e( 'Close', 'skrollr' ); ?></a>
				</div>
			</div>
			<a class="credits" data-menu-ignore href="#<?php echo $this->get_slug(); ?>"><?php echo $this->get_title(); ?></a>
		</div>
		<?php if( !$this->is_set() ) : ?><style>
			.credits{ display: none; }
		</style><?php endif;
	endif;
	}
}

new Skrollr_Credits;

