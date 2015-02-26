<?php
/**
* Metadata management (taxonomies, description, pagination ...)
*/
class Skrollr_Metadata {

	private static $instance;

	function __construct(){
		self::$instance = $this;

		// taxonomies
		if( !is_admin() ) {
			add_filter( 'the_category', array( $this, 'hide_uncategorized' ) );
			add_filter( 'the_category', array( $this, 'maybe_wrap_in_p' ) );
			add_filter( 'the_tags', array( $this, 'maybe_wrap_in_p' ) );
		}

		// pagination
		add_filter( 'next_posts_link_attributes', array( $this, 'add_ir_class' ) );
		add_filter( 'previous_posts_link_attributes', array( $this, 'add_ir_class' ) );
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
	* Do not display the default "Uncategorized" category if it's the only one
	*/
	function hide_uncategorized( $cats ){
		if( !is_admin() 
		  && strpos( $cats, __( 'Uncategorized', 'skrollr' ) ) !== false 
		  && sizeof( get_the_category() ) === 1) {
			return '';
		} elseif( !empty( $cats ) ) {
			return __('Categories: ', 'skrollr') . $cats;
		}
		return $cats;
	}

	/**
	* Add 'ir' (image replcement) class to pagination links
	*/
	function add_ir_class( $attr ){
		$attr .= ' class="ir"';
		return $attr;
	}

	/**
	* Display pagination
	*/
	function pagination(){
		?><div class="col-xs-12 col-md-2 col-md-offset-5 pagination">
			<div class="col-xs-6 prev"><?php previous_posts_link(); ?></div>
			<div class="col-xs-6 next"><?php next_posts_link(); ?></div>
		</div><?php
	}

	/**
	* Wrap tags and categories list in <p> tag if they are not empty
	*/
	function maybe_wrap_in_p( $content ){
		if( !empty( $content ) ){
			$content = "<p>$content</p>";
		}
		return $content;
	}

}

new Skrollr_Metadata;

