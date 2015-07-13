<?php
/**
* Display social icons
*/
class Skrollr_Social_Icons {

	private static $instance;

	function __construct(){
		self::$instance = $this;
		add_action( 'get_header', array( $this, 'render' ) );
		add_action( 'customize_register', array( $this, 'register_settings' ) );
	}

	/**
	* Get the static instance
	* This allows access to the instance of this class without creating a global var.
	* Read more at http://hardcorewp.com/2012/enabling-action-and-filter-hook-removal-from-class-based-wordpress-plugins
	*/
	static function get_instance() {
		return self::$instance;
	}

	function render(){
		$facebook = get_theme_mod('facebook_url', false);
		$twitter = get_theme_mod('twitter_url', false);
		if( $facebook || $twitter ) {
			?><aside class="partage">
				<?php if( $facebook ) : ?>
					<a class="ir icons icomoon-facebook" href="<?php echo esc_url($facebook) ?>" target="_blank">facebook</a>
				<?php endif; ?>
				<?php if( $twitter ) : ?>
					<a class="ir icons icomoon-twitter" href="<?php echo esc_url($twitter) ?>" target="_blank" >twitter</a>
				<?php endif; ?>
			</aside><?php
		}
	}

	function register_settings( $wp_customize ) {
		if ( ! isset( $wp_customize ) ) {
			return;
		}

		$wp_customize->add_setting( 'facebook_url', array(
			'default'   => '',
			'sanitize_callback' => 'esc_url',
		) );
		$wp_customize->add_setting( 'twitter_url', array(
			'default'   => '',
			'sanitize_callback' => 'esc_url',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'facebook_url', array(
			'label'   => __('Facebook URL', 'skrollr'),
			'section' => 'layout',
			'settings' => 'facebook_url'
		) ) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'twitter_url', array(
			'label'   => __('Twitter URL', 'skrollr'),
			'section' => 'layout',
			'settings' => 'twitter_url'
		) ) );
	}

}

new Skrollr_Social_Icons;

