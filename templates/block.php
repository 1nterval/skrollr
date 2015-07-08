<?php
	// get the custom layout
	$class = Skrollr_Customize_Layout::get_instance()->get_layout_classes();
?><div <?php post_class(); ?> id="<?php echo $post->post_name ?>">
	<div class="title-column <?php echo $class['title'] ?>"><h2><?php the_title() ?></h2></div>
	<div class="text-column <?php echo $class['content'] ?>"><?php 
		if( has_excerpt() && !is_single() ) {
			the_excerpt();
		} else {
			the_content();
		}

		wp_link_pages();
		the_tags();
		the_category(', ');
	?></div>
	<?php edit_post_link( sprintf( '<span class="icons icomoon-edit" title="%s"></span>', __( 'Edit', 'skrollr' ) ) ); ?>
	<?php comments_template(); ?>
</div>
