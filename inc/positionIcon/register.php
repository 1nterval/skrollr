<?php

/**
* Insert an icon into a WordPress theme and position it in the customizer
*/
class Skrollr_Position_Icon {

	private static $instance;

	function __construct(){
		self::$instance = $this;
		add_action( 'customize_register', array( $this, 'register_control' ) );
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

	function register_control( $wp_customize ){
		// Register the class so that it's JS template is available in the Customizer.
		$wp_customize->register_control_type( 'Icon_Position_Custom_Control' );
	}

	function live_preview(){
		$ver = defined('THEME_ASSETS_VERSION') ? THEME_ASSETS_VERSION : false;
		wp_enqueue_script( 'position-icon-customizer', get_template_directory_uri().'/inc/positionIcon/theme_customizer.js', array( 'jquery','customize-preview', 'jquery-ui-draggable' ), $ver, true);
	}

	function display( $image, $url, $position ){
		$image_mod = get_theme_mod( $image, null );
		$url_mod = get_theme_mod( $url, home_url() );
		$position_mod = get_theme_mod( $position, array("top" => 0, "left" => 0) );
		$style = 'position:absolute;z-index:9999;';
		foreach( array( 'top', 'bottom', 'left', 'right' ) as $pos ) {
			if( isset( $position_mod[$pos] )) $style .= "$pos:{$position_mod[$pos]}%;";
		}
		if( $image_mod ) : ?>
			<a id="logo-header" href="<?php echo esc_url( $url_mod ) ?>" style="<?php echo esc_attr( $style ) ?>"><img src="<?php echo esc_url( $image_mod ) ?>"/></a>
		<?php endif;
	}

	function sanitize_position( $setting ){
		if( is_array( $setting ) 
			&& ( isset($setting['top']) || isset($setting['bottom']) ) 
			&& ( isset($setting['left']) || isset($setting['right']) ) ) {
			return array_intersect_key($setting, array( 
				'top' => '',
				'bottom' => '',
				'left' => '',
				'right' => '',
			));
		} else {
			return null;
		}
	}

}

new Skrollr_Position_Icon;

if ( ! class_exists( 'WP_Customize_Control' ) ) return;

/*
* Custom Control to manage the icon position
*/
class Icon_Position_Custom_Control extends WP_Customize_Control {
	public $type = 'positionIcon';

	public function to_json() {
		parent::to_json();
		$this->json['iconId'] = $this->input_attrs['data-icon-id'];
		$this->json['parentSelector'] = $this->input_attrs['data-parent-selector'];
	}

	public function enqueue() {
		// version definie dans wp-config.php
		$ver = defined('THEME_ASSETS_VERSION') ? THEME_ASSETS_VERSION : false;
		wp_enqueue_script( 'positionIcon-custom-control', get_template_directory_uri() . '/inc/positionIcon/customize-controls.js', array('customize-controls'), $ver );
	}

	/**
	* Render a JS template for the content of the control.
	*/
	public function content_template() {
		?><label>
			<# if ( data.label ) { #>
				<span class="customize-control-title">{{{ data.label }}}</span>
			<# } #>
			<# if ( data.description ) { #>
				<span class="description customize-control-description">{{{ data.description }}}</span>
			<# } #>
		</label><?php
	}

	/**
	* Renders the control wrapper : nothing is rendered in PHP
	* @see content_template
	*/
	public function render_content(){}

}

