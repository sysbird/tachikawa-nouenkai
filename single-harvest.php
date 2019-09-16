<?php get_header(); ?>

<div id="content">
	<?php birdfield_content_header(); ?>

	<div class="container">

	<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="entry-header">
				<h1 class="entry-title"><?php the_title(); ?></h1>
			</header>
			<div class="entry-content">
				<?php the_content(); ?>
			</div>

			<?php echo do_shortcode( '[tachikawashi_noukenkai_harvest_calendar id="' .get_the_ID() .'"]' );  // calendar ?>
	</article>

	<?php endwhile; ?>

        <?php 
            $page = get_page_by_path( 'harvests' );
            $url = get_permalink( $page->ID ); ?>
		<div class="more"><a href="<?php echo $url; ?>">「<span><?php echo esc_html(get_post_type_object( 'harvest' )->labels->singular_name ); ?></span>」をもっと見る</a></div>
	</div>

	<?php birdfield_content_footer(); ?>
</div>

<?php get_footer(); ?>
