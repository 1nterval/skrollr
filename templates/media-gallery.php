<?php
	if( !function_exists( 'skrollr_mediagal_get_instance' ) ) {
		function skrollr_mediagal_get_instance(){
			static $instance = 0;
			$instance++;
			return $instance;
		}
	}

	// split down the content into shortcode / content / shortcode parts
	$content = get_the_content();
	$pattern = get_shortcode_regex();
	$content_parts = preg_split( "/$pattern/s", $content, -1, PREG_SPLIT_OFFSET_CAPTURE );

	foreach ( $content_parts as $i => $part ) :
		if( isset( $content_parts[$i] ) && isset( $content_parts[$i+1] ) ) :
			$start = $content_parts[$i][1] + strlen($content_parts[$i][0]);
			$len = $content_parts[$i+1][1] - $start + 1;
			$instance = skrollr_mediagal_get_instance();
			?><div class="sk-gallery"
				data-1p-bottom-top="display:none;top:0"
				data-bottom-top="display:block;opacity:0;"
				data-center-top="display:block;opacity:1;"
				data-center-bottom="display:block;opacity:1;"
				data-top-bottom="display:block;opacity:0;"
				data--1p-top-bottom="display:none"
				data-anchor-target="#<?php echo $post->post_name ?> .gal-<?php echo $instance; ?>">
					<?php  echo apply_filters( 'the_content', substr( $content, $start, $len ) ); ?>
			</div><?php 
		endif;
	endforeach;
