<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php if( !is_home()): ?>
        <a href="<?php the_permalink(); ?>">
    <?php endif; ?>

    <?php if( has_post_thumbnail() ): ?>
		<div class="entry-eyecatch"><?php the_post_thumbnail(  get_the_ID(), 'middle' ); ?></div>
	<?php endif; ?>

    <?php if( !is_home()): ?>
        <header class="entry-header"><h3 class="entry-title"><?php the_title(); ?></h3></header>
    	</a>
    <?php endif; ?>
</div>
