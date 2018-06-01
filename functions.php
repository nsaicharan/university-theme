<?php

function university_files () {
    wp_enqueue_style('font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('roboto', 'https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i" rel="stylesheet');

    wp_enqueue_style('university_main_styles', get_stylesheet_uri());

    wp_enqueue_script('main-university-js', get_theme_file_uri('js/scripts-bundled.js'), null, microtime(), true);
}
add_action('wp_enqueue_scripts', 'university_files');


function university_features () {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_image_size( 'professorLandscape', 400, 260, true );
    add_image_size( 'professorPortrait', 480, 650, true );
}
add_action('after_setup_theme', 'university_features');


function university_post_types() {
	// Event
	register_post_type( 'event', array(
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
		'supports' => array( 'title', 'editor' ),
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
}
add_action('pre_get_posts', 'university_adjust_queries');
