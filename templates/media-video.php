<div class="sk-video" 
	data-1p-bottom-top="display:none;top:0" 
	data-bottom-top="display:block;opacity:0.5;" 
	data-center-top="display:block;opacity:1;"
	data-center-bottom="display:block;opacity:1;" 
	data-top-bottom="display:block;opacity:0.5;" 
	data--1p-top-bottom="display:none" 
	data-anchor-target="#<?php echo $post->post_name ?>">
	<?php 
		// n'afficher que la vidéo ici
		add_filter( 'the_content', array( Skrollr_Shortcodes::get_instance(), 'extract_shortcodes' ) );
		the_content();
		remove_filter( 'the_content', array( Skrollr_Shortcodes::get_instance(), 'extract_shortcodes' ) );
	?>
</div>

