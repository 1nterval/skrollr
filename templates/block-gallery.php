<?php
	global $multipage;
	if( !function_exists( 'skrollr_blockgal_get_instance' ) ) {
		function skrollr_blockgal_get_instance(){
			static $instance = 0;
			$instance++;
			return $instance;
		}
	}

	// get the custom layout
	$class = Skrollr_Customize_Layout::get_instance()->get_layout_classes();

	// split down the content into shortcode / content / shortcode parts
	$content = get_the_content();
	$pattern = get_shortcode_regex();
	$content_parts = preg_split( "/$pattern/s", $content, -1, PREG_SPLIT_OFFSET_CAPTURE );

	// show the title only if there is content (appart from the gallery)
	$show_title = (trim( strip_shortcodes( $content ) ) != '' || $multipage );

?><div <?php post_class(); ?> id="<?php echo esc_attr($post->post_name) ?>">
	<?php // afficher le contenu sans la galerie
	foreach ( $content_parts as $i => $part ) :
		if( $show_title ) :  ?>
		<div class="one-column row">
			<div class="title-column <?php echo esc_attr($class['title']) ?>">
				<?php if( $i == 0) { ?>
					<h2><?php the_title() ?></h2>
				<?php } ?>
			</div>
			<div class="text-column <?php echo esc_attr($class['content']) ?>"><?php 
				echo apply_filters( 'the_content', $part[0] );
				if( $i == sizeof($content_parts) - 1 ) {
					wp_link_pages();
					the_tags();
				}
			?></div>
		</div><?php 
		endif;
		if( isset( $content_parts[$i] ) && isset( $content_parts[$i+1] ) ){ 
			$content_parts[$i][2] = $content_parts[$i][1] + sizeof($content_parts[$i][0]);
			$instance = skrollr_blockgal_get_instance();
			?>
			<div class="the_gallery gal-<?php echo $instance; ?>" data-instance="<?php echo $instance; ?>"></div>
		<?php }
	endforeach; ?>

	<?php edit_post_link( sprintf( '<span class="icons icomoon-edit" title="%s"></span>', __( 'Edit', 'skrollr' ) ) ); ?>
</div>
