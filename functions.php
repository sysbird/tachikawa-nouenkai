<?php
add_filter( 'comments_open', '__return_false' );

//////////////////////////////////////////////////////
// Setup Theme
function tachikawashi_noukenkai_setup() {

	register_default_headers( array(
		'birdfield_child'		=> array(
		'url'			=> '%2$s/images/header.jpg',
		'thumbnail_url'		=> '%2$s/images/header-thumbnail.jpg',
		'description_child'	=> 'birdfield'
		)
	) );
}
add_action( 'after_setup_theme', 'tachikawashi_noukenkai_setup' );

//////////////////////////////////////////////////////
// Child Theme Initialize
function tachikawashi_noukenkai_init() {

	// add tags at page
	register_taxonomy_for_object_type('post_tag', 'page');

}
add_action( 'init', 'tachikawashi_noukenkai_init', 0 );

//////////////////////////////////////////////////////
// Filter at main query
function tachikawashi_noukenkai_query( $query ) {

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
add_action( 'pre_get_posts', 'tachikawashi_noukenkai_query' );

//////////////////////////////////////////////////////
// Set offset for pagination
function tachikawashi_noukenkai_found_posts($found_posts, $query) {

	if ( $query->is_home() && $query->is_main_query() && is_paged() ) {
		$offset = 6;
        return $found_posts + $offset;
    }
    return $found_posts;
}
add_filter('found_posts', 'tachikawashi_noukenkai_found_posts', 1, 2 );

//////////////////////////////////////////////////////
// Enqueue Scripts
function tachikawashi_noukenkai_scripts() {

	// css
	wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );

	// Google Fonts
	wp_enqueue_style( 'tachikawashi-noukenkai-google-font', '//fonts.googleapis.com/css?family=Open+Sans', false, null, 'all' );

	// tachikawashi_noukenkai js
	wp_enqueue_script( 'tachikawashi_noukenkai', get_stylesheet_directory_uri() .'/js/tachikawashi-noukenkai.js', array( 'jquery', 'jquerytile' ), '1.10' );
}
add_action( 'wp_enqueue_scripts', 'tachikawashi_noukenkai_scripts' );

//////////////////////////////////////////////////////
// rmove parent scripts
function tachikawashi_noukenkai_deregister_scripts() {
	wp_dequeue_script( 'birdfield' );
	wp_dequeue_script( 'jquery-masonry' );
 }
add_action( 'wp_print_scripts', 'tachikawashi_noukenkai_deregister_scripts', 10 );

//////////////////////////////////////////////////////
// rmove parent styles
function tachikawashi_noukenkai_deregister_styles() {
	wp_dequeue_style( 'birdfield-google-font' );
 }
add_action( 'wp_print_styles', 'tachikawashi_noukenkai_deregister_styles', 10 );

//////////////////////////////////////////////////////
// Shortcode vegetable Calendar
function tachikawashi_noukenkai_vegetable_calendar ( $atts ) {

	extract( shortcode_atts( array(
		'title' => 'no',
		'id' => 0,
		), $atts ));

	$html_table_header = '<table class="vegetable-calendar"><tbody><tr><th class="title">&nbsp;</th><th class="data"><span>1月</span><span>2月</span><span>3月</span><span>4月</span><span>5月</span><span>6月</span><span>7月</span><span>8月</span><span>9月</span><span>10月</span><span>11月</span><span>12月</span></th></tr>';
	$html_table_footer = '</tbody></table>';
	$html = '';

	$args = array(
		'posts_per_page' => -1,
		'post_type'	=> 'vegetable',
		'post_status'	=> 'publish',
		'meta_key'		=> 'type',
	);

	if( 0 !== $id ){
		// single vegetable
		$args['p'] = $id;
	}

	$the_query = new WP_Query($args);
	$type_current = '';
	if ( $the_query->have_posts() ) :
		while ( $the_query->have_posts() ) : $the_query->the_post();

		$type = get_field( 'type' );
		if( $type && ( $type != $type_current ) ){
			if( !empty( $html )){
				$html .= $html_table_footer;
			}

			$html .= '<div class="vegetable-meta">' .tachikawashi_noukenkai_get_type_label( $type ) .'</div>';
			$type_current = $type;
			$html .= $html_table_header;
		}

		// 収穫カレンダー
		$selected = get_field( 'calendar' );
		$html .= '<tr>';

		if( 0 !== $id ){
			// single vegetable
			$html .= '<td class="title">収穫時期</td>';
		}
		else{
			$html .= '<td class="title"><a href="' .get_permalink() .'">' .get_the_title() .'</a></td>';
		}

		$html .= '<td class="data">';
		for( $i = 1; $i <= 12; $i++ ){

			if( $selected && in_array( $i, $selected ) ) {
				$html .= '<span class="best">' .$i .'</span>';
			}
			else{
				$html .= '<span>' .$i .'</span>';
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
add_shortcode( 'tachikawashi_noukenkai_vegetable_calendar', 'tachikawashi_noukenkai_vegetable_calendar' );

//////////////////////////////////////////////////////
// Display the Featured Image at vegetable page
function tachikawashi_noukenkai_post_image_html( $html, $post_id, $post_image_id ) {

	if( !( false === strpos( $html, 'anchor' ) ) ){
		$html = '<a href="' .get_permalink() .'" class="thumbnail">' .$html .'</a>';
	}

	return $html;
}
add_filter( 'post_thumbnail_html', 'tachikawashi_noukenkai_post_image_html', 10, 3 );

/////////////////////////////////////////////////////
// get type label in vegetable
function tachikawashi_noukenkai_get_type_label( $value, $anchor = TRUE ) {
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
function tachikawashi_noukenkai_get_season_label( $value, $anchor = TRUE ) {
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
function tachikawashi_noukenkai_query_vars( $vars ){
	$vars[] = "type";
	$vars[] = "season";
	return $vars;
}
add_filter( 'query_vars', 'tachikawashi_noukenkai_query_vars' );

/////////////////////////////////////////////////////
// show catchcopy at vegetable
function tachikawashi_noukenkai_get_catchcopy() {

	$catchcopy = get_field( 'catchcopy' );
	if( $catchcopy ){
		return '<p class="catchcopy">' .$catchcopy .'</p>';
	}

	return NULL;
}

//////////////////////////////////////////////////////
// bread crumb
function tachikawashi_noukenkai_en_content_header( $arg ){

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
add_action( 'birdfield_content_header', 'tachikawashi_noukenkai_en_content_header' );


//////////////////////////////////////////////////////
// show eyecarch on dashboard
function tachikawashi_noukenkai_manage_posts_columns( $columns ) {
	$columns[ 'thumbnail' ] = __( 'Thumbnail' );
	return $columns;
}
add_filter( 'manage_posts_columns', 'tachikawashi_noukenkai_manage_posts_columns' );
add_filter( 'manage_pages_columns', 'tachikawashi_noukenkai_manage_posts_columns' );

function tachikawashi_noukenkai_manage_posts_custom_column( $column_name, $post_id ) {
	if ( 'thumbnail' == $column_name ) {
		$thum = get_the_post_thumbnail( $post_id, 'small', array( 'style'=>'width:100px;height:auto;' ));
	} if ( isset( $thum ) && $thum ) {
		echo $thum;
	} else {
		echo __( 'None' );
	}
}
add_action( 'manage_posts_custom_column', 'tachikawashi_noukenkai_manage_posts_custom_column', 10, 2 );
add_action( 'manage_pages_custom_column', 'tachikawashi_noukenkai_manage_posts_custom_column', 10, 2 );

//////////////////////////////////////////////////////
// login logo
function tachikawashi_noukenkai_login_head() {

	$url = get_stylesheet_directory_uri() .'/images/login.png';
	echo '<style type="text/css">.login h1 a { background-image:url(' .$url .'); height: 40px; width: 320px; background-size: 100% 100%;}</style>';
}
//add_action('login_head', 'tachikawashi_noukenkai_login_head');

//////////////////////////////////////////////////////
// remove emoji
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles', 10 );

//////////////////////////////////////////////////////
// set favicon
function tachikawashi_noukenkai_favicon() {
	echo '<link rel="shortcut icon" type="image/x-icon" href="' .get_stylesheet_directory_uri() .'/images/favicon.ico" />'. "\n";
	echo '<link rel="apple-touch-icon" href="' .get_stylesheet_directory_uri() .'/images/webclip.png" />'. "\n";
}
add_action( 'wp_head', 'tachikawashi_noukenkai_favicon' );

//////////////////////////////////////////////////////
// GoogleGoogle Analytics
function tachikawashi_noukenkai_wp_head() {
	if ( !is_user_logged_in() ) {
		get_template_part( 'google-analytics' );
	}
}
add_action( 'wp_head', 'tachikawashi_noukenkai_wp_head' );

//////////////////////////////////////////////////////
// image optimize
function tachikawashi_noukenkai_handle_upload( $file )
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
add_action( 'wp_handle_upload', 'tachikawashi_noukenkai_handle_upload' );

//////////////////////////////////////////////////////
// activated theme
function tachikawashi_noukenkai_after_switch_theme () {
	// enable theme option for editor
	$role = get_role( 'editor' );
	$role->add_cap( 'edit_theme_options' ); 
}
add_action('after_switch_theme', 'tachikawashi_noukenkai_after_switch_theme');

//////////////////////////////////////////////////////
// deactivated theme
function tachikawashi_noukenkai_switch_theme () {
	// disable theme option for editor
	$role = get_role( 'editor' );
	$role->remove_cap( 'edit_theme_options' ); 
}
add_action('switch_theme', 'tachikawashi_noukenkai_switch_theme');

//////////////////////////////////////////////////////
// admin menu
function tachikawashi_noukenkai_admin_menu() {
	// show theme option menu for editor
	add_menu_page( 'テーマオプション', 'テーマオプション', 'editor', 'customize.php?return=%2Fwp-admin%2Findex.php');

	// remove menu for editor
	if( !current_user_can( 'administrator' )){
		remove_menu_page( 'edit-comments.php' );
		remove_menu_page( 'themes.php' );
		remove_menu_page( 'tools.php' );
		remove_submenu_page( 'index.php','update-core.php' );
	}
}
add_action( 'admin_menu', 'tachikawashi_noukenkai_admin_menu' );

//////////////////////////////////////////////////////
// remove default theme customize
function tachikawashi_noukenkai_customize_register_menu( $wp_customize ) {

	$wp_customize->remove_control( 'header_image' );
	$wp_customize->remove_section( 'static_front_page' );
	$wp_customize->remove_section( 'background_image' );
	$wp_customize->remove_section( 'custom_css' );
	$wp_customize->remove_section( 'colors' );
	$wp_customize->remove_section( 'title_tagline' );

	// remove customize menu for editor
	if( !current_user_can( 'administrator' )){
		$wp_customize->remove_panel( "widgets" );
		remove_action( 'customize_register', array( $wp_customize->nav_menus, 'customize_register' ), 11 );
	}
}
add_action( 'customize_register', 'tachikawashi_noukenkai_customize_register_menu' );

//////////////////////////////////////////////////////
// remove parent theme customize
function tachikawashi_noukenkai_customize_register( $wp_customize ) {

	// remove customize menu for editor
	if( !current_user_can( 'administrator' )){
		$wp_customize->remove_section( 'birdfield_customize' );
	}
}
add_action( 'customize_register', 'tachikawashi_noukenkai_customize_register', 88 );

//////////////////////////////////////////////////////
// admin_init
function tachikawashi_noukenkai_admin_init() {
	// hide the update message for not administrator
	if( !current_user_can( 'administrator' )){
		remove_action( 'admin_notices', 'update_nag', 3 );
		remove_action( 'admin_notices', 'maintenance_nag', 10 );
	}
}
add_filter( 'admin_init', 'tachikawashi_noukenkai_admin_init' );

/////////////////////////////////////////////////////
// Add WP REST API Endpoints
function tachikawashi_noukenkai_rest_api_init() {

	// get related posts API
	register_rest_route( 'get_related_posts', '/(?P<title>.*)', array(
		'methods' => 'GET',
		'callback' => 'tachikawashi_noukenkai_get_related_posts',
		) );

	// get related vegetabless API
	register_rest_route( 'get_related_vegetables', '/(?P<title>.*)', array(
		'methods' => 'GET',
		'callback' => 'tachikawashi_noukenkai_get_related_vegetables',
		) );

}
add_action( 'rest_api_init', 'tachikawashi_noukenkai_rest_api_init' );

/////////////////////////////////////////////////////
// get related vegetables
// この記事のタイトルをタグにもつ投稿を取得する（野菜ページ用）
function tachikawashi_noukenkai_get_related_posts( $params ) {

	$find = FALSE;
	$item = array();
	$i = 0;

	$args = array(
		'tag'				=> urldecode( $params[ 'title' ] ),
		'posts_per_page'	=> 3,
		'orderby'			 => 'rand',
		'post_status'		=> 'publish',
	);

	$the_query = new WP_Query($args);
	if ( $the_query->have_posts() ) :
		$find = TRUE;
		while ( $the_query->have_posts() ) : $the_query->the_post();
			$item[ $i ][ 'id' ] = get_the_ID();
			$item[ $i ][ 'title' ] = get_the_title();
			$item[ $i ][ 'link' ] = get_the_permalink();
			if ( has_post_thumbnail( $item[ $i ][ 'id' ] )) {
				$thumbnail = get_the_post_thumbnail( $item[ $i ][ 'id' ], 'middle' );
				if( !empty($thumbnail )){
					if( preg_match_all( '/<img.*?src=(["\'])(.+?)\1.*?>/i', $thumbnail, $match )){
						$item[ $i ][ 'thumbnail' ] = $match[2][0];
					}
				}
			}

			$i++;

		endwhile;
		wp_reset_postdata();
	endif;

	if($find) {
		return new WP_REST_Response( array(
			'item'		=> $item,
		) );
	}
	else{
		$response = new WP_Error('error_code', 'Sorry, no posts matched your criteria.' );
		return $response;
	}
}

/////////////////////////////////////////////////////
// get related vegetables on recipe
function tachikawashi_noukenkai_get_related_vegetables( $params ) {

	$find = FALSE;
	$item = array();
	$tags = explode(",", urldecode( $params[ 'title' ] ));
	for( $i = 0; $i < count( $tags ); $i++ ){

		$args = array(
			'title'				=> $tags[ $i ],
			'post_type'			=> 'vegetable',
			'posts_per_page'	=> 1,
			'post_status'		=> 'publish',
		);

		$the_query = new WP_Query($args);
		if ( $the_query->have_posts() ) :
			$find = TRUE;
			while ( $the_query->have_posts() ) : $the_query->the_post();
				$item[ $i ][ 'id' ] = get_the_ID();
				$item[ $i ][ 'title' ] = get_the_title( );
				$item[ $i ][ 'link' ] = get_the_permalink();
				if ( has_post_thumbnail( $item[ $i ][ 'id' ] )) {
					$thumbnail = get_the_post_thumbnail( $item[ $i ][ 'id' ], 'middle' );
					if( !empty($thumbnail )){
						if( preg_match_all( '/<img.*?src=(["\'])(.+?)\1.*?>/i', $thumbnail, $match )){
							$item[ $i ][ 'thumbnail' ] = $match[2][0];
						}
					}
				}
	
			endwhile;
			wp_reset_postdata();
		endif;
	}

	if($find) {	
		return new WP_REST_Response( array(
			'item'		=> $item,
		) );
	}
	else{
		$response = new WP_Error('error_code', 'Sorry, no posts matched your criteria.' );
		return $response;
	}
}