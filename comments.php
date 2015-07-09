<?php if ( comments_open() || pings_open() ) : ?>
	<div class="clearfix visible-xs"></div>
	<div class="container-fluid"><div class="col-xs-12 col-md-6 col-md-push-3">
	<?php if ( have_comments() && ! (is_singular() && get_option('page_comments')) ) : ?>
		<a href="<?php the_permalink() ?>"><?php _e( 'See the comments', 'skrollr' ); ?></a>
	<?php elseif ( have_comments() ) : ?>
		<h3 id="comments"><?php	printf( _n( 'One Response to %2$s', '%1$s Responses to %2$s', get_comments_number(), 'skrollr' ),
										number_format_i18n( get_comments_number() ), '&#8220;' . get_the_title() . '&#8221;' ); ?></h3>

		<div class="navigation">
			<div class="prev"><?php previous_comments_link() ?></div>
			<div class="next"><?php next_comments_link() ?></div>
		</div>

		<ol class="commentlist">
		<?php wp_list_comments();?>
		</ol>

		<div class="navigation">
			<div class="alignleft"><?php previous_comments_link() ?></div>
			<div class="alignright"><?php next_comments_link() ?></div>
		</div>
	<?php endif; ?>

	<?php comment_form(); ?>

	</div></div><!-- /bootstrap cols and container -->
<?php endif;
