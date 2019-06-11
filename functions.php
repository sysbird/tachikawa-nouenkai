<?php
add_filter( 'comments_open', '__return_false' );

//////////////////////////////////////////////////////
// Setup Theme
function fureainouen_setup() {

	register_default_headers( array(
		'birdfield_child'		=> array(
		'url'			=> '%2$s/images/header.jpg',
		'thumbnail_url'		=> '%2$s/images/header-thumbnail.jpg',
		'description_child'	=> 'birdfield'
		)
	) );
}
add_action( 'after_setup_theme', 'fureainouen_setup' );

//////////////////////////////////////////////////////
// Child Theme Initialize
function fureainouen_init() {

	// add tags at page
	register_taxonomy_for_object_type('post_tag', 'page');

	// add post type vegetable
	$labels = array(
		'name'		=> '板橋区でとれる野菜・果物・花卉',
		'all_items'	=> '板橋区でとれる野菜・果物・花卉の一覧',
		);

	$args = array(
		'labels'			=> $labels,
		'supports'			=> array( 'title','editor', 'thumbnail', 'custom-fields' ),
		'public'			=> true,	// 公開するかどうが
		'show_ui'			=> true,	// メニューに表示するかどうか
		'menu_position'		=> 5,		// メニューの表示位置
		'has_archive'		=> true,	// アーカイブページの作成
		);

	register_post_type( 'vegetable', $args );

}
add_action( 'init', 'fureainouen_init', 0 );

//////////////////////////////////////////////////////
// Filter at main query
function fureainouen_query( $query ) {

 	if ( $query->is_home() && $query->is_main_query() ) {
		$offset = 3;

		if( !is_paged() ){
			// toppage news
			$query->set( 'posts_per_page', $offset );
		}
		else{
			// blog pagination
			$ppp = get_option('posts_per_page');
			$page_numper = get_query_var('paged');
			$query->set( 'offset', (( $page_numper -2 ) *$ppp ) +$offset );
		}
	}

	if (!is_admin() && $query->is_main_query() && is_post_type_archive('vegetable')) {
		// vegetable
		$query->set( 'posts_per_page', -1 );
		$query->set( 'orderby', 'rand' );
	}
}
add_action( 'pre_get_posts', 'fureainouen_query' );

//////////////////////////////////////////////////////
// Set offset for pagination
function fureainouen_found_posts($found_posts, $query) {

	if ( $query->is_home() && $query->is_main_query() && is_paged() ) {
		$offset = 6;
        return $found_posts + $offset;
    }
    return $found_posts;
}
add_filter('found_posts', 'fureainouen_found_posts', 1, 2 );

//////////////////////////////////////////////////////
// Enqueue Scripts
function fureainouen_scripts() {

	// css
	wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );

	if ( is_page() || is_home() ) {
		wp_enqueue_script( 'googlemaps', '//maps.googleapis.com/maps/api/js?key=AIzaSyAlofBWp1v_u6vh3zSDXz-kpHLBwypX8RQ' );
	}

	// Google Fonts
	wp_enqueue_style( 'setos-google-font', '//fonts.googleapis.com/css?family=Open+Sans', false, null, 'all' );

	// fureainouen js
	wp_enqueue_script( 'fureainouen', get_stylesheet_directory_uri() .'/js/fureainouen.js', array( 'jquery', 'jquerytile' ), '1.10' );
}
add_action( 'wp_enqueue_scripts', 'fureainouen_scripts' );

//////////////////////////////////////////////////////
// rmove parent scripts
function fureainouen_deregister_scripts() {
	wp_dequeue_script( 'birdfield' );
	wp_dequeue_script( 'jquery-masonry' );
 }
add_action( 'wp_print_scripts', 'fureainouen_deregister_scripts', 10 );

//////////////////////////////////////////////////////
// rmove parent styles
function fureainouen_deregister_styles() {
	wp_dequeue_style( 'birdfield-google-font' );
 }
add_action( 'wp_print_styles', 'fureainouen_deregister_styles', 10 );

//////////////////////////////////////////////////////
// Shortcode Goole Maps
function fureainouen_map ( $atts ) {

	$output ='<iframe src="https://www.google.com/maps/d/embed?mid=1MDTh5UGRFR7LeC6E4kc2hFGc0S1Wwa0r" width="100%" height="480"></iframe>';

	return $output;
}
add_shortcode( 'fureainouen_map', 'fureainouen_map' );

//////////////////////////////////////////////////////
// Shortcode vegetable Calendar Link
function fureainouen_vegetable_calendar_link ( $atts ) {

	$html = '';
	if ( wp_is_mobile() ){
		$page = get_page_by_path( 'calendar' );
		$html = '<p><a href="' .get_the_permalink( $page->ID) .'">&raquo;' .$page->post_title .'</a></p>';
	}
	else{
		$html = do_shortcode( '[fureainouen_vegetable_calendar]' );
	}

	return $html;
}
add_shortcode( 'fureainouen_vegetable_calendar_link', 'fureainouen_vegetable_calendar_link' );

//////////////////////////////////////////////////////
// Shortcode vegetable Calendar
function fureainouen_vegetable_calendar ( $atts ) {

	extract( shortcode_atts( array(
		'title' => 'no'
		), $atts ) );

	$html_table_header = '<table class="vegetable-calendar"><tbody><tr><th class="title">&nbsp;</th><th class="data"><span>4月</span><span>5月</span><span>6月</span><span>7月</span><span>8月</span><span>9月</span><span>10月</span><span>11月</span><span>12月</span><span>1月</span><span>2月</span><span>3月</span></th></tr>';
	$html_table_footer = '</tbody></table>';
	$html = '';

	$args = array(
		'posts_per_page' => -1,
		'post_type'	=> 'vegetable',
		'post_status'	=> 'publish',
		'meta_key'		=> 'type',
//		'orderby'		=> 'meta_value',
	);

	$the_query = new WP_Query($args);
	$type_current = '';
	if ( $the_query->have_posts() ) :
		while ( $the_query->have_posts() ) : $the_query->the_post();

		$type = get_field( 'type' );
		if( $type && ( $type != $type_current ) ){
			if( !empty( $html )){
				$html .= $html_table_footer;
			}

			$html .= '<div class="vegetable-meta">' .fureainouen_get_type_label( $type ) .'</div>';
			$type_current = $type;
			$html .= $html_table_header;
		}

		// 収穫カレンダー
		$selected = get_field( 'calendar' );
		$html .= '<tr>';
		$html .= '<td class="title"><a href="' .get_permalink() .'">' .get_the_title() .'</a></td>';
		$html .= '<td class="data">';
		for( $i = 1; $i <= 12; $i++ ){

			$month = $i +3;
			if( 12 < $month ){
				$month -= 12;
			}

			if( $selected && in_array( $month, $selected ) ) {
				$html .= '<span class="best">' .$month .'</span>';
			}
			else{
				$html .= '<span>' .$month .'</span>';
			}
		}

		$html .= '</td>';
		$html .= '</tr>';

		endwhile;

		wp_reset_postdata();
	endif;

	if( !empty( $html )){
		$html .= $html_table_footer;
	}

	if( 'yes' === $title ){
		$html = '<h2>カレンダー</h2>' .$html;
	}

	return $html;
}
add_shortcode( 'fureainouen_vegetable_calendar', 'fureainouen_vegetable_calendar' );

//////////////////////////////////////////////////////
// Shortcode vegetable List
function fureainouen_vegetable_list ( $atts ) {

	ob_start();

	$args = array(
		'post_type' => 'vegetable',
		'post_status' => 'publish',
		'orderby'	=> 'rand',
	);

	if( is_home()){
		$args[ 'posts_per_page' ] = 6;
		$args[ 'meta_key' ] = '_thumbnail_id';
	}
	else{
		$args[ 'posts_per_page' ] = -1;
	}

	$the_query = new WP_Query($args);
	if ( $the_query->have_posts() ) :
		?> <div class="tile"><?php

		while ( $the_query->have_posts() ) : $the_query->the_post();
			get_template_part( 'content', 'vegetable' );
		endwhile;

		?></div><?php

		wp_reset_postdata();
	endif;

	return ob_get_clean();
}
add_shortcode( 'fureainouen_vegetable_list', 'fureainouen_vegetable_list' );

//////////////////////////////////////////////////////
// Shortcode link button
function fureainouen_link ( $atts ) {

	$atts = shortcode_atts( array( 'title' => '', 'url' => '#' ), $atts );
	$title = $atts['title'];
	$url = $atts['url'];

	if( !strcmp( '#' ,$url )){
		return '';
	}

	if( '' === $title ){
		$title = $url;
	}

	$html = '<a href="' .esc_html( $url ) .'" class="fureainouen_link">' .$title .'</a>';

	return $html;
}
add_shortcode( 'fureainouen_link', 'fureainouen_link' );

//////////////////////////////////////////////////////
// Shortcode popup link
function fureainouen_popuplink ( $atts ) {

	$atts = shortcode_atts( array( 'title' => '', 'pagetitle' => '' ), $atts );
	$title = $atts['title'];
	$pagetitle = $atts['pagetitle'];

	if( !strcmp( '' ,$pagetitle )){
		return '';
	}

	if( '' === $title ){
		$title = $pagetitle;
	}

	$html = '<a href="#" class="fureainouen_link popup" pagetitle="' .$pagetitle .'">' .$title .'</a>';

	return $html;
}
add_shortcode( 'fureainouen_popuplink', 'fureainouen_popuplink' );

//////////////////////////////////////////////////////
// Display the Featured Image at vegetable page
function fureainouen_post_image_html( $html, $post_id, $post_image_id ) {

	if( !( false === strpos( $html, 'anchor' ) ) ){
		$html = '<a href="' .get_permalink() .'" class="thumbnail">' .$html .'</a>';
	}

	return $html;
}
add_filter( 'post_thumbnail_html', 'fureainouen_post_image_html', 10, 3 );

/////////////////////////////////////////////////////
// get type label in vegetable
function fureainouen_get_type_label( $value, $anchor = TRUE ) {
	$label ='';
	$fields = get_field_object( 'type' );

	if( array_key_exists( 'choices' , $fields ) ){
		$label .= '<span>';
	
		$label .= $fields[ 'choices' ][ $value ];
		$label .= '</span>';
	}

	return $label;
}

/////////////////////////////////////////////////////
// get season label in vegetable
function fureainouen_get_season_label( $value, $anchor = TRUE ) {
	$label ='';
	$fields = get_field_object( 'season' );

	if( is_array($value)){
		foreach ( $value as $key => $v ) {
			if( array_key_exists( 'choices', $fields) ) {
				$label .= '<span>';
				$label .= ( $fields[ 'choices' ][ $v ] );
				$label .= '</span>';
			}
		}
	}
	else{
		if( array_key_exists( 'choices', $fields) ) {
			$label .= '<span>'. $fields[ 'choices' ][ $value ] .'</span>';
		}
	}

	return $label;
}

/////////////////////////////////////////////////////
// add permalink parameters for vegetable
function fureainouen_query_vars( $vars ){
	$vars[] = "type";
	$vars[] = "season";
	return $vars;
}
add_filter( 'query_vars', 'fureainouen_query_vars' );

/////////////////////////////////////////////////////
// show catchcopy at vegetable
function fureainouen_get_catchcopy() {

	$catchcopy = get_field( 'catchcopy' );
	if( $catchcopy ){
		return '<p class="catchcopy">' .$catchcopy .'</p>';
	}

	return NULL;
}

//////////////////////////////////////////////////////
// bread crumb
function fureainouen_en_content_header( $arg ){

	$html = '';

	if( !is_home()){
		if ( class_exists( 'WP_SiteManager_bread_crumb' ) ) {
			$html .= '<div class="bread_crumb_wrapper">';
			$html .= WP_SiteManager_bread_crumb::bread_crumb( array( 'echo'=>'false', 'home_label' => 'ホーム', 'elm_class' => 'bread_crumb container' ));
			$html .= '</div>';
		}
	}

	return $html;
}
add_action( 'birdfield_content_header', 'fureainouen_en_content_header' );


//////////////////////////////////////////////////////
// show eyecarch on dashboard
function fureainouen_manage_posts_columns( $columns ) {
	$columns[ 'thumbnail' ] = __( 'Thumbnail' );
	return $columns;
}
add_filter( 'manage_posts_columns', 'fureainouen_manage_posts_columns' );
add_filter( 'manage_pages_columns', 'fureainouen_manage_posts_columns' );

function fureainouen_manage_posts_custom_column( $column_name, $post_id ) {
	if ( 'thumbnail' == $column_name ) {
		$thum = get_the_post_thumbnail( $post_id, 'small', array( 'style'=>'width:100px;height:auto;' ));
	} if ( isset( $thum ) && $thum ) {
		echo $thum;
	} else {
		echo __( 'None' );
	}
}
add_action( 'manage_posts_custom_column', 'fureainouen_manage_posts_custom_column', 10, 2 );
add_action( 'manage_pages_custom_column', 'fureainouen_manage_posts_custom_column', 10, 2 );


//////////////////////////////////////////////////////
// login logo
function fureainouen_login_head() {

	$url = get_stylesheet_directory_uri() .'/images/login.png';
	echo '<style type="text/css">.login h1 a { background-image:url(' .$url .'); height: 40px; width: 320px; background-size: 100% 100%;}</style>';
}
add_action('login_head', 'fureainouen_login_head');

//////////////////////////////////////////////////////
// remove emoji
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles', 10 );

//////////////////////////////////////////////////////
// set favicon
function fureainouen_favicon() {
	echo '<link rel="shortcut icon" type="image/x-icon" href="' .get_stylesheet_directory_uri() .'/images/favicon.ico" />'. "\n";
	echo '<link rel="apple-touch-icon" href="' .get_stylesheet_directory_uri() .'/images/webclip.png" />'. "\n";
}
//add_action( 'wp_head', 'fureainouen_favicon' );

//////////////////////////////////////////////////////
// remove theme customize
function fureainouen_customize_register( $wp_customize ) {
	$wp_customize->remove_control( 'header_image' );
	$wp_customize->remove_section( 'static_front_page' );
	$wp_customize->remove_section( 'background_image' );
	$wp_customize->remove_section( 'custom_css' );
	$wp_customize->remove_section( 'colors' );
	$wp_customize->remove_section( 'title_tagline' );
	$wp_customize->remove_section( 'birdfield_customize' );
}
add_action( 'customize_register', 'fureainouen_customize_register' );

//////////////////////////////////////////////////////
// GoogleGoogle Analytics
function fureainouen_wp_head() {
	if ( !is_user_logged_in() ) {
		get_template_part( 'google-analytics' );
	}
}
add_action( 'wp_head', 'fureainouen_wp_head' );


//////////////////////////////////////////////////////
// image optimize
function fureainouen_handle_upload( $file )
{
	if( $file['type'] == 'image/jpeg' ) {
		$image = wp_get_image_editor( $file[ 'file' ] );

		if (! is_wp_error($image)) {
			$exif = exif_read_data( $file[ 'file' ] );
			$orientation = $exif[ 'Orientation' ];
			$max_width = 1280;
			$max_height = 1280;
			$size = $image->get_size();
			$width = $size[ 'width' ];
			$height = $size[ 'height' ];

			if ( $width > $max_width || $height > $max_height ) {
				$image->resize( $max_width, $max_height, false );
			}

			if (! empty($orientation)) {
				switch ($orientation) {
					case 8:
						$image->rotate( 90 );
						break;

					case 3:
						$image->rotate( 180 );
						break;

					case 6:
						$image->rotate( -90 );
						break;
				}
			}
			$image->save( $file[ 'file' ]) ;
		}
	}

	return $file;
}
add_action( 'wp_handle_upload', 'fureainouen_handle_upload' );