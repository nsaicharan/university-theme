<?php 

require get_theme_file_path('/inc/search-route.php');

// Page Banner
function pageBanner($args = NULL) {

	if ( !$args['title'] ) {
		$args['title'] = get_the_title();
	}

	if ( !$args['subtitle'] ) {
		$args['subtitle'] = get_field('page_banner_subtitle');
	}

	if ( !$args['photo'] ) {
		if ( get_field('page_banner_subtitle') ) {
			$args['photo'] = get_field('page_banner_image')['sizes']['pageBanner'];
		} else {
			$args['photo'] = get_theme_file_uri('images/ocean.jpg');
		}
	}

?>
	<div class="page-banner">
		<div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo']; ?>)"></div>
		
		<div class="page-banner__content container container--narrow">
			<h1 class="page-banner__title"><?php echo $args['title']; ?></h1>
			<div class="page-banner__intro">
				<p><?php echo $args['subtitle']; ?></p>
			</div>
		</div>
	</div>
<?php } ?>


<?php

function university_files() {
    wp_enqueue_style('font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('roboto', 'https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i" rel="stylesheet');

    wp_enqueue_style('university_main_styles', get_stylesheet_uri());

    wp_enqueue_script( 'google-map', '//maps.googleapis.com/maps/api/js?key=AIzaSyC4xccdP73-MvfI4yK8CjBL3ET60QI8wwc', null, '1', true );

	wp_enqueue_script('main-university-js', get_theme_file_uri('js/scripts-bundled.js'), null, microtime(), true);

	wp_localize_script( 'main-university-js', 'universityData', array(
		'root_url' => get_site_url(),
		'nonce' => wp_create_nonce( 'wp_rest' )
	) );
}
add_action('wp_enqueue_scripts', 'university_files');


function university_features () {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_image_size( 'professorLandscape', 400, 260, true );
    add_image_size( 'professorPortrait', 480, 650, true );
    add_image_size( 'pageBanner', 1500, 350, true);
}
add_action('after_setup_theme', 'university_features');


function university_post_types() {
	// Event
	register_post_type( 'event', array(
		'capability_type' => 'event',
		'map_meta_cap' => true,
		'supports' => array( 'title', 'editor', 'excerpt' ),
		'rewrite' => array( 'slug' => 'events' ),
		'has_archive' => true,
		'public' => true,
		'labels' => array(
			'name' => 'Events',
			'add_new_item' => 'Add New Event',
			'edit_item' => 'Edit Event',
			'all_items' => 'All Events',
			'singular_name' => 'Event'
		),
		'menu_icon' => 'dashicons-calendar'
	) );

	// Program
	register_post_type( 'program', array(
		'supports' => array( 'title' ),
		'rewrite' => array( 'slug' => 'programs' ),
		'has_archive' => true,
		'public' => true,
		'labels' => array(
			'name' => 'Programs',
			'add_new_item' => 'Add New Program',
			'edit_item' => 'Edit Program',
			'all_items' => 'All Programs',
			'singular_name' => 'Program'
		),
		'menu_icon' => 'dashicons-awards'
	) );

	// Professor
	register_post_type( 'professor', array(
		'public' => true,
		'supports' => array ( 'title', 'editor', 'thumbnail' ),
		'labels' => array(
			'name' => 'Professors',
			'all_items' => 'All Professors',
			'edit_item' => 'Edit Professor',
			'add_new_item' => 'Add New Professor',
			'singular_name' => 'Professor'
		),
		'menu_icon' => 'dashicons-welcome-learn-more'
	) );

	// Campus
	register_post_type( 'campus', array (
		'capability_type' => 'campus',
		'map_meta_cap' => true,
		'public' => true,
		'has_archive' => true,
		'supports' => array ( 'title', 'editor', 'excerpt' ),
		'rewrite' => array ( 'slug' => 'campuses' ),
		'labels' => array (
			'name' => 'Campuses',
			'all_items' => 'All Campuses',
			'add_new_item' => 'Add Campus',
			'edit_item' => 'Edit Campus',
			'singular_name' => 'Campus'
		),
		'menu_icon' => 'dashicons-location-alt'
	) );

	// Note 
	register_post_type( 'note', array(
		'capability_type' => 'note',
		'map_meta_cap' => true,
		'show_in_rest' => true,
		'supports' => array( 'title', 'editor' ),
		'rewrite' => array( 'slug' => 'programs' ),
		'public' => false,
		'show_ui' => true, // Show in admin dashboard
		'labels' => array(
			'name' => 'Notes',
			'add_new_item' => 'Add New Note',
			'edit_item' => 'Edit Note',
			'all_items' => 'All Notes',
			'singular_name' => 'Note'
		),
		'menu_icon' => 'dashicons-welcome-write-blog'
	) );
}
add_action('init', 'university_post_types');

function university_adjust_queries($query) {
	$today = date('Ymd');

	// Events Archive Page
	if ( !is_admin() && is_post_type_archive('event') && $query->is_main_query() ) {
		$query->set('orderby', 'meta_value_num');
		$query->set('order', 'ASC');
		$query->set('meta_key', 'event_date');
		$query->set('meta_query', array(
			'key' => 'event_date',
			'compare' => '>=',
			'value' => $today,
			'type' => 'numeric'
		));
	}

	// Programs Archive Page
	if ( !is_admin() && is_post_type_archive('program') && $query->is_main_query() )  {
		$query->set('posts_per_page', -1);
		$query->set('orderby', 'title');
		$query->set('order', 'ASC');
	}

	// Campuses Archive Page
	if ( !is_admin() && is_post_type_archive('campus') && $query->is_main_query() ) {
		$query->set('posts_per_page', -1);
	}
}
add_action('pre_get_posts', 'university_adjust_queries');


function universityMapKey($api) {
	$api['key'] = 'AIzaSyC4xccdP73-MvfI4yK8CjBL3ET60QI8wwc';
	return $api;
}
add_filter( 'acf/fields/google_map/api', 'universityMapKey' );


function university_custom_rest() {
	register_rest_field( 'post', 'authorName', array(
		'get_callback' => function() {
			return get_the_author();
		}
	) );
} 
add_action( 'rest_api_init', 'university_custom_rest' );

// Redirect subscribers to homepage when they login
function redirectSubscribers() {
	$user = wp_get_current_user();

	if ( count($user->roles) == 1 && $user->roles[0] == 'subscriber' ) {
		wp_redirect( site_url('/') );
		exit;
	}
}
add_action( 'admin_init', 'redirectSubscribers' );

// Remove WP Admin bar for subscribers
function noSubsAdminBar() {
	$user = wp_get_current_user();

	if ( count($user->roles) == 1 && $user->roles[0] == 'subscriber' ) {
		show_admin_bar( false );
	}
}
add_action( 'wp_loaded', 'noSubsAdminBar' );

// Change the URL of login screen
function ourHeaderUrl() {
	return esc_url( site_url( '/' ) );
}
add_filter( 'login_headerurl', 'ourHeaderUrl' ); 

function ourHeaderTitle() {
	return get_bloginfo('name');
}
add_filter( 'login_headertitle', 'ourHeaderTitle' );

function ourLoginCSS() {
	wp_enqueue_style('roboto', 'https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i" rel="stylesheet');
	wp_enqueue_style('university_main_styles', get_stylesheet_uri());
}
add_action( 'login_enqueue_scripts', 'ourLoginCSS' );

// Force notes to be private
function makeNotePrivate($data) {
	if ( $data['post_type'] == 'note' ) {
		$data['post_title'] = sanitize_text_field( $data['post_title'] );
		$data['post_content'] = sanitize_textarea_field( $data['post_content'] );
	}

	if ( $data['post_type'] == 'note' && $data['post_status'] != 'trash' ) {
		$data['post_status'] = 'private';
	}

	return $data;
}
add_filter( 'wp_insert_post_data', 'makeNotePrivate' );
