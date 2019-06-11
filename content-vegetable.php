<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<a href="<?php the_permalink(); ?>">
		<?php if( has_post_thumbnail() ): ?>
			<div class="entry-eyecatch"><?php the_post_thumbnail(  get_the_ID(), 'large' ); ?></div>
		<?php endif; ?>
		<header class="entry-header"><?php echo fureainouen_get_catchcopy(); ?><h3 class="entry-title"><?php the_title(); ?></h3></header>
	</a>
</div>
