<div class="sk-video" style="display:block;opacity:1;"
	data-1p-bottom-top="display:none;top:0" 
	data-bottom-top="display:block;" 
	data-center-bottom="display:none;" 
	data-anchor-target="#<?php echo $post->post_name ?>">
	<?php 
		// display only video here
		add_filter( 'the_content', array( Skrollr_Shortcodes::get_instance(), 'extract_shortcodes' ) );
		the_content();
		remove_filter( 'the_content', array( Skrollr_Shortcodes::get_instance(), 'extract_shortcodes' ) );
	?>
</div>

