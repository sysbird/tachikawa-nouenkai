<?php $recipe = false; ?>
<header class="entry-header">
	<?php if( is_single() ) : ?>
		<time class="postdate" datetime="<?php echo get_the_time( 'Y-m-d' ) ?>"><?php echo get_post_time( get_option( 'date_format' ) ); ?></time>
		<?php $categories = get_the_category();
			$class = '';
			$category_class = '';
			$category_name = '';
			if ( $categories ) {
				foreach( $categories as $category ) {
					$category_class .= ' ' .$category->slug;
					if( 1 == $category->parent ){
						$category_class .= ' ' .$category->slug;
						$category_name .= ' ' .$category->name;
					}
					
					if( !strcmp( 'recipe', $category->slug )){
						$recipe = true;
					}
				}
			}
		?>
		<?php if( $category_name ): ?> 
			<span class="category <?php echo $category_class; ?>"><?php echo $category_name; ?></span>
		<?php endif; ?>
	<?php endif; ?>

	<h1 class="entry-title"><?php the_title(); ?></h1>

</header>

<div class="entry-content">
	<?php the_content(); ?>
	<?php wp_link_pages( array(
		'before'		=> '<div class="page-links">' . __( 'Pages:', 'birdfield' ),
		'after'			=> '</div>',
		'link_before'	=> '<span>',
		'link_after'	=> '</span>'
		) ); ?>
</div>

<?php if( $recipe ) : //related vegetables on this post ?>
	<?php $posttags = get_the_tags();
		if ( $posttags ) {
			$tag_count = 0;
			$tags = '';
			foreach ( $posttags as $tag ) {

				if( $tag_count ){
					$tags .=  ',';
				}
				$tags .=  urldecode( $tag->name );
				$tag_count++;
			}

			if($tag_count ): ?>
				<div class="related-vegetables" data-tags="<?php echo $tags; ?>">
				<h2>このレシピに使われている野菜</h2>
				</div>
			<?php endif;
		}
	?>

<?php endif; ?>
