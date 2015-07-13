<?php 
	$title_width = get_theme_mod( 'title_width', 4 );
	$main_title_class = "col-sm-" . ($title_width*2) . " col-sm-offset-" . (6 - $title_width);

	// get the custom layout
	$class = Skrollr_Customize_Layout::get_instance()->get_layout_classes();

?><header id="accueil" class="block block-0 row">
	<h1 class="<?php echo esc_attr($main_title_class) ?>" data-top="opacity:1;transform:scale(1);display:<?php if( display_header_text() ) : ?>block<?php else: ?>none<?php endif ?>" data-top-bottom="opacity:0;transform:scale(2);display:none" data-anchor-target="#accueil">
		<span class="slabtext"><?php bloginfo( 'name' ) ?></span>
	</h1>
	<a class="scrolldown" title="<?php _e('Read next ...', 'skrollr') ?>" href="#chapo">
		<?php _e('go down to start', 'skrollr'); ?>
	</a>
	<?php Skrollr_Position_Icon::get_instance()->display( 'logo_header', 'logo_header_url', 'logo_header_position' ); ?>
</header>

<div id="chapo" class="one-column clearfix row">
	<div class="title-column <?php echo esc_attr($class['title']) ?>"><?php
		echo Skrollr_Header_Description::get_instance()->get_title();
	?></div>
	<div class="text-column <?php echo esc_attr($class['content']) ?>"><?php 
		echo Skrollr_Header_Description::get_instance()->get_desc();
	?></div>

	<?php if( current_user_can( 'edit_theme_options' ) ) : ?>
	<a class="post-edit-link" href="<?php echo admin_url( 'customize.php') . '?autofocus[control]=blogdescription'; ?>">
		<?php printf( '<span class="icons icomoon-edit" title="%s"></span>', __( 'Edit', 'skrollr' ) ); ?>
	</a>
	<?php endif; ?>
</div>
