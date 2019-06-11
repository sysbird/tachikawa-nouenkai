<?php get_header(); ?>

<div id="content">
	<?php birdfield_content_header(); ?>

	<div class="container">

		<article class="hentry">
			<header class="content-header">
				<h1 class="content-title">
				<?php echo esc_html( get_post_type_object( 'vegetable')->label ); ?>
				</h1>
			</header>

            <?php  echo do_shortcode( '[fureainouen_vegetable_calendar]' ); ?>

			<?php if ( have_posts() ) : ?>
				<div class="tile">

					<?php while ( have_posts() ) : the_post(); ?>
						<?php get_template_part( 'content', 'vegetable' ); ?>
					<?php endwhile; ?>

				</div>
			<?php endif; ?>
		</article>
	</div>

	<?php birdfield_content_footer(); ?>
</div>

<?php get_footer(); ?>
