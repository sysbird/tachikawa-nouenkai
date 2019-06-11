<?php get_header(); ?>

<div id="content">
	<?php birdfield_content_header(); ?>

	<div class="container">

	<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="entry-header">
				<?php echo fureainouen_get_catchcopy(); ?>
				<h1 class="entry-title"><?php the_title(); ?></h1>
			</header>
			<div class="entry-content">
				<?php the_content(); ?>
			</div>

			<div class="vegetable-meta">
				<?php $type = get_field( 'type' );
				if( $type ) {
					echo fureainouen_get_type_label( $type );
				}

				$season = get_field( 'season' );
				if( $season ){
					echo fureainouen_get_season_label( $season );
				} ?>
			</div>

			<?php $selected = get_field( 'calendar' );  // 収穫カレンダー ?>
				<?php if(is_array($selected)): ?>

				<table class="vegetable-calendar"><tbody><tr><th class="title"><em>&nbsp;</em></th><th class="data"><span>4月</span><span>5月</span><span>6月</span><span>7月</span><span>8月</span><span>9月</span><span>10月</span><span>11月</span><span>12月</span><span>1月</span><span>2月</span><span>3月</span></th></tr>

				<tr>
					<td class="title">収穫時期</td>
					<td class="data">
					<?php for( $i = 1; $i <= 12; $i++ ){
						$month = $i +3;
						if( 12 < $month ){
							$month -= 12;
						}
	
						if( in_array( $month, $selected) ) { ?>
							<span class="best"><?php echo $month; ?></span>
					<?php	}
						else{ ?>
							<span><?php echo $month; ?> </span>
						<?php	}
					} ?>

				</td></tr></tbody></table>
			<?php endif; ?>

			<?php //related recipe
				$recipe_count = 0;
				$vegetable_title = get_the_title();
				$args = array(
						'tag'				=> $vegetable_title,
						'posts_per_page'	=> 6,
						'orderby' 			=> 'rand',
						'post_type'			=> 'post',
						'post_status'		=> 'publish',
					);
			
				$the_query = new WP_Query($args);
				if ( $the_query->have_posts() ) :
					while ( $the_query->have_posts() ) : $the_query->the_post();
						if( !$recipe_count ){
							echo '<h2>' .$vegetable_title .'を使ったレシピ</h2>';
							echo '<div class="tile">';
						}

						get_template_part( 'content', 'vegetable' );
						$recipe_count++;
					endwhile;
			
					wp_reset_postdata();
				endif;

				if($recipe_count ){
					echo '</div>';
				}
			?>
	</article>

	<?php endwhile; ?>

		<div class="more"><a href="<?php echo esc_html( get_post_type_archive_link( 'vegetable' )); ?>"><?php echo esc_html(get_post_type_object( 'vegetable' )->labels->singular_name ); ?>をもっと見る</a></div>
	</div>

	<?php birdfield_content_footer(); ?>
</div>

<?php get_footer(); ?>
