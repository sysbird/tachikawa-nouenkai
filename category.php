<?php get_header(); ?>

<div id="content">
	<?php birdfield_content_header(); ?>

	<div class="container">

		<header class="content-header">
			<h1 class="content-title"><?php echo single_cat_title( '', false ); ?></h1>
		</header>

		<?php if ( have_posts() ) : ?>
			<ul class="article tile">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'content', 'home' ); ?>
				<?php endwhile; ?>
			</ul>

			<?php $igr_pagination = get_the_posts_pagination( array(
					'mid_size'				=> 3,
					'screen_reader_text'	=> 'pagination',
				) );

				$igr_pagination = str_replace( '<h2 class="screen-reader-text">pagination</h2>', '', $igr_pagination );
				echo $igr_pagination; ?>
		<?php endif; ?>
	</div>

	<?php birdfield_content_footer(); ?>
</div>

<?php get_footer(); ?>
