<?php
	global $multipage;
	// recupérer le contenu sans la galerie
	$content = Skrollr_Shortcodes::get_instance()->remove_shortcodes( get_the_content() );
	$show_title = (trim($content) != '' || $multipage);

	// get the custom layout
	$class = Scrollr_Customize_Layout::get_instance()->get_layout_classes();

?><div <?php post_class(); ?> id="<?php echo $post->post_name ?>">
	<?php if( $show_title ) :
		// afficher le contenu sans la galerie
		add_filter( 'the_content', array( Skrollr_Shortcodes::get_instance(), 'remove_shortcodes' ) );
		?><div class="one-column row">
			<div class="title-column <?php echo $class['title'] ?>"><h2><?php the_title() ?></h2></div>
			<div class="text-column <?php echo $class['content'] ?>"><?php 
				the_content();
				wp_link_pages();
				the_tags();
			?></div>
		</div><?php
		remove_filter( 'the_content', array( Skrollr_Shortcodes::get_instance(), 'remove_shortcodes' ) );
	endif;

		// gallery placeholder 
	?><div class="the_gallery"></div>

	<?php edit_post_link( sprintf( '<span class="icons icomoon-edit" title="%s"></span>', __( 'Edit', 'skrollr' ) ) ); ?>
</div>
