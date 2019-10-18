<?php get_header(); ?>

<div id="content">
	<?php birdfield_content_header(); ?>

	<?php if( ! is_paged() ): ?>
		<?php if( !( birdfield_headerslider())): ?>
			<section id="wall" class="no-image"></section>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( have_posts()) : ?>
		<section id="blog">
			<div class="container">
                <?php $category_blog = get_category_by_slug( 'blog' ); ?>
				<h2><a href="<?php echo get_category_link( $category_blog->cat_ID ); ?>"><?php echo $category_blog->cat_name; ?></a></h2>


				<ul class="tile">
				<?php while ( have_posts()) : the_post(); ?>
					<?php get_template_part( 'content', 'home' ); ?>
				<?php endwhile; ?>
				</ul>

				<?php if( ! is_paged() ): ?>
					<div class="more"><a href="<?php echo get_category_link( $category_blog->cat_ID ); ?>" >「<span><?php echo $category_blog->cat_name; ?></span>」をもっと見る</a></div>

				<?php else:
					$tachikawashi_noukenkai_pagination = get_the_posts_pagination( array(
							'mid_size'	=> 3,
							'screen_reader_text'	=> 'pagination',
						) );

					$tachikawashi_noukenkai_pagination = str_replace( '<h2 class="screen-reader-text">pagination</h2>', '', $tachikawashi_noukenkai_pagination );
					echo $tachikawashi_noukenkai_pagination;
				endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if( ! is_paged()): ?>
		<?php
			$args = array(
				'post_type' => 'page',
				'tag' => 'information',
				'post_status' => 'publish'
			);

			$the_query = new WP_Query($args);

			if ( $the_query->have_posts() ) :
				while ( $the_query->have_posts() ) : $the_query->the_post();
		?>

		<section class="information <?php  echo get_post_field( 'post_name', get_the_ID() ); ?>">
			<div class="container ">
				<?php
					$more_text = '「<span>' .get_the_title() .'</span>」を詳しく見る';
					$more_url = get_the_permalink();
				?>

				<div class="entry-content">
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <?php the_content(''); ?>
                    
                     <?php  if( !strcmp( 'harvests' ,get_post_field( 'post_name' ))):
                         echo do_shortcode( '[tachikawashi_noukenkai_harvests]' );
                     endif;  ?>
					<div class="more"><a href="<?php echo $more_url; ?>"><?php echo $more_text; ?></a></div>
				</div>

			</div>
		</section>

		<?php endwhile;
			wp_reset_postdata();
			endif;
		?>

        <section class="information">
            <div class="container ">
                <?php echo do_shortcode( '[instagram-feed accesstoken=="21767575539.M2E4MWE5Zg==.YzZhNmU4M2U4M2Z.lNDYwNGFiY2ZjZDA4MDEwNjhjNTY="]' ); ?>
            </div>
        </section>
        

		<section class="information" id="gmap">
			<iframe src="https://www.google.com/maps/d/embed?mid=1XGLmoeh2a-ChT6lL6KnLq4ImpJeLLqiw" width="100%" height="480"></iframe>
		</section>

	<?php endif; ?>

	<?php birdfield_content_footer(); ?>
</div>

<?php get_footer(); ?>
