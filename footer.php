	<footer id="footer">
		<section id="widget-area">
			<div class="container">
				<div class="left">
					<?php dynamic_sidebar( 'widget-area-footer' ); ?>
				</div>
				<div class="right">
                    <div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/ja_JP/sdk.js#xfbml=1&version=v4.0"></script>
					<div class="fb-page" data-href="https://www.facebook.com/pg/tachikawashi.noukenkai" data-tabs="timeline" data-width="460" data-height="500" data-small-header="true" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="false"><blockquote cite="https://www.facebook.com/pg/tachikawashi.noukenkai" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/pg/tachikawashi.noukenkai">立川市農研会</a></blockquote></div>
				</div>
			</div>
		</section>

		<div class="container">
			<div class="site-title">
				<a href="<?php echo esc_url( home_url( '/' ) ) ; ?>">TACHIKAWASHI-NOUKENKAI</a>
				<?php printf( 'Copyright &copy; %s All Rights Reserved.', date("Y") ); ?>
			</div>
		</div>
		<p id="back-top"><a href="#top"><span><?php _e( 'Go Top', 'birdfield' ); ?></span></a></p>
	</footer>

</div><!-- wrapper -->

<?php wp_footer(); ?>

</body>
</html>