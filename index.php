<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
	<head>
		<?php wp_head(); ?>
	</head>
	<body <?php body_class( is_customize_preview() ? 'customize_preview' : null ); ?>>
		<?php get_header() ?>

		<article id="skrollr-body" role="main" class="container-fluid">
			<?php get_template_part( 'templates/chapo' ) ?>

			<?php if ( have_posts() ) {
					while ( have_posts() ) {
						the_post();
						$done = false;
						foreach( array( 'video', 'gallery' ) as $format ){
							$the_format = get_post_format();
							if( $the_format === $format && has_shortcode( $post->post_content, $format ) ){
								get_template_part( 'templates/block', $format );
								$done = true;
							}
						}
						if( !$done ) {
							get_template_part( 'templates/block' );
						}
					}
			} ?>

			<?php get_template_part( 'templates/bottes' ) ?>
		</article>

		<?php // print videos and galleries here
			rewind_posts();
			while ( have_posts() ) {
				the_post();
				foreach( array( 'video', 'gallery' ) as $format ){
					$the_format = get_post_format();
					if( $the_format === $format && has_shortcode( $post->post_content, $format ) ){
						get_template_part( 'templates/media', $format );
					}
				}
			}
		?>

		<?php get_footer() ?>
		<?php wp_footer(); ?>
	</body>
</html>
