<?php

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

	register_post_type( 'harvest',
		array(
			'labels'		=>  array(
				'name'		=> '立川市の農産物',
				'all_items'	=> '立川市の農産物の一覧',
			),
			'supports'		=> array( 'title','editor', 'thumbnail', 'custom-fields' ),
			'public'		=> true,
			'show_ui'		=> true,
			'menu_position'	=> 5,
			'has_archive'	=> true,
			'show_in_rest'	=> true,
			)
		);

}
add_action( 'init', 'tachikawashi_noukenkai_init', 0 );

//////////////////////////////////////////////////////
// Filter at main query
function tachikawashi_noukenkai_query( $query ) {
 	if ( $query->is_home() && $query->is_main_query() ) {
		$offset = 6;
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
	if (!is_admin() && $query->is_main_query() && is_post_type_archive('harvest')) {
		// harvest
        $query->set( 'posts_per_page', -1 );
        $query->set( 'orderby', 'rand' );        
        $query->set( 'meta_key', '_thumbnail_id' );
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
// Shortcode harvest Calendar
function tachikawashi_noukenkai_harvest_calendar ( $atts ) {

	extract( shortcode_atts( array(
		'title' => 'no',
		'id' => 0,
		), $atts ));

	$html_table_header = '<table class="harvest-calendar"><tbody><tr><th class="title">&nbsp;</th><th class="data"><span>1月</span><span>2月</span><span>3月</span><span>4月</span><span>5月</span><span>6月</span><span>7月</span><span>8月</span><span>9月</span><span>10月</span><span>11月</span><span>12月</span></th></tr>';
	$html_table_footer = '</tbody></table>';
    $html = '';
    $count = 0;

	$args = array(
		'posts_per_page'    => -1,
		'post_type'	        => 'harvest',
		'post_status'	    => 'publish',
        'meta_key'		    => 'type',
        'orderby' => array( 'meta_value' => 'DESC', 'menu_order' => 'ASC' ),
	);

	if( 0 !== $id ){
		// single harvest
        $args['p'] = $id;
        $html .= $html_table_header;
	}

	$the_query = new WP_Query($args);
	$type_current = '';
    if ( $the_query->have_posts() ) :
		while ( $the_query->have_posts() ) : $the_query->the_post();

            $count++;

            if( 0 == $id ){
                $type = get_field( 'type' );
                if( $type && ( $type != $type_current ) ){
                    if( !empty( $html )){
                        $html .= $html_table_footer;
                    }

                    if( 0 == $id ){
                        $html .= '<div class="harvest-meta"><span class="type">' .tachikawashi_noukenkai_get_type_label( $type ) .'</span></div>';
                    }

                    $type_current = $type;
                    $html .= $html_table_header;
                }
            }
                    
            // 収穫カレンダー
            $selected = get_field( 'calendar' );
            $html .= '<tr>';

            if( 0 !== $id ){
                // single harvest
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

	if( $count ){
		$html .= $html_table_footer;

        if( 'yes' === $title ){
            $html = '<h2>カレンダー</h2>' .$html;
        }
	}

	return $html;
}
add_shortcode( 'tachikawashi_noukenkai_harvest_calendar', 'tachikawashi_noukenkai_harvest_calendar' );

//////////////////////////////////////////////////////
// Shortcode harvest Photos
function tachikawashi_noukenkai_harvests ( $atts ) {

	extract( shortcode_atts( array(
		'type' => '',
		), $atts ));

	$args = array(
		'posts_per_page'    => 6,
		'post_type'	        => 'harvest',
        'post_status'	    => 'publish',
        'meta_key'          => '_thumbnail_id',
        'orderby'           => 'rand',
	);

    if( !empty( $type )){
        $args['posts_per_page'] = -1;
        $args['meta_query'] = array(
            array(
                'key' => 'type',
                'value' => $type,
            )
        );
    }

    $the_query = new WP_Query($args);
    ob_start();
    if ( $the_query->have_posts() ) :
        while ( $the_query->have_posts() ) : $the_query->the_post();
            //$html .= get_the_post_thumbnail(  get_the_ID(), 'middle' );
            get_template_part( 'content', 'harvest' );
        endwhile;

        wp_reset_postdata();
    endif;
    
    $html = ob_get_clean();

	if( !empty( $html )){
        $html = '<ul class="tile">' .$html .'</ul>';
	}

	return $html;
}
add_shortcode( 'tachikawashi_noukenkai_harvests', 'tachikawashi_noukenkai_harvests' );

//////////////////////////////////////////////////////
// Display the Featured Image at harvest page
function tachikawashi_noukenkai_post_image_html( $html, $post_id, $post_image_id ) {

	if( !( false === strpos( $html, 'anchor' ) ) ){
		$html = '<a href="' .get_permalink() .'" class="thumbnail">' .$html .'</a>';
	}

	return $html;
}
add_filter( 'post_thumbnail_html', 'tachikawashi_noukenkai_post_image_html', 10, 3 );

/////////////////////////////////////////////////////
// get type label in harvest
function tachikawashi_noukenkai_get_type_label( $value ) {
	$label ='';
	$fields = get_field_object( 'type' );

	if( array_key_exists( 'choices' , $fields ) ){	
		$label .= $fields[ 'choices' ][ $value ];
	}

	return $label;
}

//////////////////////////////////////////////////////
// bread crumb
function tachikawashi_noukenkai_en_content_header( $arg ){

	$html = '';

    // bread crumb
    if( is_post_type_archive( 'post' )){
        $url = esc_url( home_url( '/' ) );

        $html = '<ul class="breadcrumb"><li class="home"><a href="' .$url .'" class="home">ホーム</a></li><li>' .$blog_name = get_bloginfo( 'name' ) .'ブログ</li></ul>';

    }
    else if( !is_home()){
        if(function_exists('bcn_display_list')){
            $html .= bcn_display_list( true );
            $html = '<ul class="breadcrumb">' .$html .'</ul>';
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

	$url = get_stylesheet_directory_uri() .'/images/logo.svg';
	echo '<style type="text/css">.login h1 a { background-image:url(' .$url .'); height: 60px; width: 320px; background-size: 100% 100%;}</style>';
}
add_action('login_head', 'tachikawashi_noukenkai_login_head');

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

	// get related harvestss API
	register_rest_route( 'get_related_harvests', '/(?P<title>.*)', array(
		'methods' => 'GET',
		'callback' => 'tachikawashi_noukenkai_get_related_harvests',
		) );

}
add_action( 'rest_api_init', 'tachikawashi_noukenkai_rest_api_init' );

/////////////////////////////////////////////////////
// get related harvests
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
// get related harvests on recipe
function tachikawashi_noukenkai_get_related_harvests( $params ) {

	$find = FALSE;
	$item = array();
	$tags = explode(",", urldecode( $params[ 'title' ] ));
	for( $i = 0; $i < count( $tags ); $i++ ){

		$args = array(
			'title'				=> $tags[ $i ],
			'post_type'			=> 'harvest',
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