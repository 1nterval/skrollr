<div <?php post_class(); ?> id="<?php echo $post->post_name ?>">
	<?php edit_post_link( sprintf( '<span class="icons icomoon-edit" title="%s"></span>', __( 'Edit', 'skrollr' ) ) ); ?>

	<?php
		// ne pas afficher la vidéo ici
		add_filter( 'the_content', array( Skrollr_Shortcodes::get_instance(), 'remove_shortcodes' ) );
		$content = get_the_content();
		$content = apply_filters( 'the_content', $content );
		$content = str_replace( ']]>', ']]&gt;', $content );
		if( trim($content) != '' ) : ?>
			<figcaption><?php echo $content; ?></figcaption>
		<?php endif;
		remove_filter( 'the_content', array( Skrollr_Shortcodes::get_instance(), 'remove_shortcodes' ) );
	?>
</div>
