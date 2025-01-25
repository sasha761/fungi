<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package News
 */

$templates = array( 'archive.twig', 'index.twig' );

$context = Timber::context();


$context['title'] = 'Archive';
if ( is_day() ) {
	$context['title'] = 'Archive: ' . get_the_date( 'D M Y' );
} else if ( is_month() ) {
	$context['title'] = 'Archive: ' . get_the_date( 'M Y' );
} else if ( is_year() ) {
	$context['title'] = 'Archive: ' . get_the_date( 'Y' );
} else if ( is_tag() ) {
	$context['title'] = single_tag_title( '', false );
} else if ( is_category() ) {
	$context['title'] = single_cat_title( '', false );
	array_unshift( $templates, 'archive-' . get_query_var( 'cat' ) . '.twig' );
} else if ( is_post_type_archive() ) {
	$context['title'] = post_type_archive_title( '', false );
	array_unshift( $templates, 'archive-' . get_post_type() . '.twig' );
}

$post_type = get_post_type();
$category_id = get_queried_object_id();
$paged = get_query_var('paged') ? get_query_var('paged') : 1;


$context['blog_posts'] = get_posts_info([
	'post_type'   => $post_type,
	'post_status' => 'publish',
  'posts_per_page' => 10,
  'category'    => $category_id,
  'paged'       => $paged,
	'orderby'     => 'date',
  'order'       => 'ASC',
  'suppress_filters' => false
]);

$context['categories'] = get_taxonomy_data([
	'taxonomy' => 'category'
]);

$context['most_popular']  = get_posts_info(array(
  'post_type'   => 'post',
  'post_status' => 'publish',
  'posts_per_page' => 12, 
  // 'category'    => $category_id,
  'meta_key' => 'count_post_viewed', 
  'orderby' => 'meta_value_num', 
  'suppress_filters' => false,
  'order' => 'ASC'
), $is_emoji = false);

Timber::render( $templates, $context );