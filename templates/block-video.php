<div <?php post_class(); ?> id="<?php echo $post->post_name ?>">
	<div class="the_video" 
		style="display: block;background: #eaeaea;position:static; height:100%" 
		data-bottom-top="opacity:1;"
		data-center-top="opacity:0;"
		data-bottom="opacity:0;"
		data-center-bottom="opacity:1;">
	<?php edit_post_link( sprintf( '<span class="icons icomoon-edit" title="%s"></span>', __( 'Edit', 'skrollr' ) ) ); ?>
	</div>
</div>
