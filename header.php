	<!--[if lt IE 7]>
		<p class="browsehappy"><?php 
			printf( 
				wp_kses( __( 'You are using an <strong>outdated</strong> browser. Please <a href="%s">upgrade your browser</a> to improve your experience', 'skrollr' ),
					array( 'a' => array( 'href' => array() ), 'strong' => array() )
				),
				esc_url( 'http://browsehappy.com/' )
			);
		?></p>
	<![endif]-->

