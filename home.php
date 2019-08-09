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
				<h2><a href="#"><?php echo $category_blog->cat_name; ?></a></h2>

				<ul class="article">
				<?php while ( have_posts()) : the_post(); ?>
					<?php get_template_part( 'content', 'home' ); ?>
				<?php endwhile; ?>
				</ul>

				<?php if( ! is_paged() ): ?>
					<div class="more"><a href="<?php echo get_category_link( $category_blog->cat_ID ); ?>" >「<?php echo $category_blog->cat_name; ?>」をもっと見る</a></div>
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
				$information_count = 0;
				while ( $the_query->have_posts() ) : $the_query->the_post();
				$has_post_thumbnail = has_post_thumbnail();
				if( $has_post_thumbnail ){
					$information_count++;

					$vertical  = false;
					$post_thumbnail_id = get_post_thumbnail_id( get_the_ID() );
					$post_thumbnail_attr = wp_get_attachment_metadata( $post_thumbnail_id );
					if( $post_thumbnail_attr['width'] < $post_thumbnail_attr['height'] ){
						$vertical  = true;
					}
				}
		?>

		<section class="information <?php  echo get_post_field( 'post_name', get_the_ID() ); ?>">
			<div class="container <?php if( $has_post_thumbnail ){ echo ' two-columns'; } if( !($information_count %2) ){ echo ' reverse';} ?>">
				<?php
					$more_text = '「<span>' .get_the_title() .'</span>」を詳しく見る';
					$more_url = get_the_permalink();
				?>

				<?php if( $has_post_thumbnail ): ?>
					<div class="entry-eyecatch<?php if( $vertical ){ echo ' vertical'; } ?>"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(  get_the_ID(), 'middle' ); ?></a></div>
				<?php endif; ?>

				<div class="entry-content">
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_content(''); ?>
					<div class="more"><a href="<?php echo $more_url; ?>"><?php echo $more_text; ?></a></div>
				</div>

			</div>
		</section>

		<?php endwhile;
			wp_reset_postdata();
			endif;
		?>

		<?php
			$args = array(
				'post_type' => 'vegetable',
				'meta_key' => '_thumbnail_id',
				'posts_per_page' => 6,
				'orderby' => 'rand',
				'post_status' => 'publish'
				);
			$the_query = new WP_Query($args);
			if ( $the_query->have_posts() ) :
		?>

		<?php $more_url = get_post_type_archive_link( 'vegetable' );
			$more_text = get_post_type_object( 'vegetable' )->labels->singular_name;
		?>
		<section class="information">
			<div class="container">
				<h2><a href="<?php echo $more_url; ?>"><?php echo $more_text; ?></a></h2>
				<div class="tile">

				<?php while ( $the_query->have_posts() ) : $the_query->the_post();
					get_template_part( 'content', 'vegetable' );
				?>

		<?php endwhile;
			wp_reset_postdata();
		?>
				</div>
				<div class="more"><a href="<?php echo esc_html( $more_url ); ?>">「<span><?php echo esc_html( $more_text ); ?></span>」をもっと見る</a></div>
			</div>
		</section>

		<?php endif; ?>

		<section class="information" id="gmap">
			<iframe src="https://www.google.com/maps/d/embed?mid=1XGLmoeh2a-ChT6lL6KnLq4ImpJeLLqiw" width="100%" height="480"></iframe>
		</section>

	<?php endif; ?>

	<?php birdfield_content_footer(); ?>
</div>

<?php get_footer(); ?>
