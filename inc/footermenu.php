<?php
/**
* Footer menu management
* The links listed in this menu are displayed at the bottom of each page
*/
class Skrollr_Footer_Menu {

	private $location = 'footer';
	private $description = '';
	private $menu_id;
	private $menu_object;
	private $menu_side = 'left';
	private static $instance;

	function __construct(){
		self::$instance = $this;
		$this->description = __( 'Footer menu', 'skrollr' );
		add_action( 'after_setup_theme', array( $this, 'register_nav_menu' ) );
		add_filter( 'mediahelper_image_link_size', function($size){ return array(250, 120, true); } );
	}

	/**
	* Get the static instance
	* This allows access to the instance of this class without creating a global var.
	* Read more at http://hardcorewp.com/2012/enabling-action-and-filter-hook-removal-from-class-based-wordpress-plugins
	*/
	static function get_instance() {
		return self::$instance;
	}

	/**
	* Declare main menu to Wordpress
	*/
	function register_nav_menu() {
		register_nav_menu( $this->location, $this->description );
		if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $this->location ] ) ) {
			$this->menu_id = $locations[ $this->location ];
			$this->menu_object = wp_get_nav_menu_object( $locations[ $this->location ] );

			// get the custom layout
			$this->menu_side = get_theme_mod('footer_menu_layout', 'left');
		}
	}

	/**
	* Display the menu if needed
	*/
	function display() {
		if ( has_nav_menu( 'footer' ) ) {
			?><div class="block page two-columns clearfix row" id="footer">
				<h2 class="col-md-10 col-md-offset-1"><?php echo $this->menu_object->name ?></h2>

				<div class="text-column col-md-5 col-md-offset-1 <?php if( $this->menu_side == 'left' ) echo 'col-md-push-5' ?>">
				</div>

				<div class="menu-column col-md-5 <?php if( $this->menu_side == 'left' ) echo 'col-md-pull-5' ?>">
					<?php wp_nav_menu( array( 
						'theme_location' => 'footer',
						'container'      => 'nav',
						'container_class'=> 'footer_menu',
						'items_wrap'     => '<ul id="%1$s" class="%2$s linesep">%3$s</ul>',
						'fallback_cb'		=> '__return_false',
					) ); ?>
				</div>

			</div><?php
		}
	}

}

new Skrollr_Footer_Menu;

