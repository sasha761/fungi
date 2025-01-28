<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package News
 */

$context = Timber::context();
$timber_post = Timber::get_post();
$context['post'] = $timber_post;

$thumbnail_id = get_post_thumbnail_id($timber_post->id);

$context['thumbnail'] = get_image_data($thumbnail_id, 'full');
  
$context['author_name'] = get_the_author_meta('display_name', $timber_post->post_author);
$context['author_link'] = get_author_posts_url($timber_post->post_author);


$comments_args       = array('post_id' => $timber_post->id, 'status' => 'approve'); 
$context['comments'] = get_comments($comments_args);

$context['popular_posts'] = get_posts_info([
  'meta_key' => 'count_post_viewed',
  'orderby'  => 'meta_value_num',
], $is_emoji = false);

$context['product_categories'] = get_taxonomy_data([
	'taxonomy' => 'category'
]);;

$context['related'] = get_posts_info([
  'numberposts' => 6,
  'ignore_sticky_posts' => true,
  'orderby' => 'rand',
  'post_status' => 'publish',
  'post_type' => 'post', 
  'post__not_in' => array($timber_post->id) 
]);

$context['if_attention'] = get_field('attention');

global $wpdb;

// Получение всех связанных постов
$translations = $wpdb->get_col($wpdb->prepare(
  "SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE trid = (SELECT trid FROM {$wpdb->prefix}icl_translations WHERE element_id = %d)",
  $timber_post->id
));

if (empty($translations)) {
  $translations = [$timber_post->id];
}

// Формирование плейсхолдеров для IN()
$placeholders = implode(',', array_fill(0, count($translations), '%d'));

// Подготовка и выполнение запроса
$query = $wpdb->prepare(
  "SELECT like_type, COUNT(*) as count FROM {$wpdb->prefix}post_likes WHERE post_id IN ($placeholders) GROUP BY like_type",
  ...$translations
);

$likes = $wpdb->get_results($query);

$likes_data = [
  'like' => 0,
  'fire' => 0,
  'heart' => 0,
  'swearing' => 0,
  'clown' => 0,
];

foreach ($likes as $like) {
  $likes_data[$like->like_type] = $like->count;
}

$context['post_likes'] = $likes_data;

Timber::render( array( 'single-' . $timber_post->ID . '.twig', 'single-' . $timber_post->post_type . '.twig', 'single.twig' ), $context );

// if ( post_password_required( $timber_post->ID ) ) {
	// Timber::render( 'single-password.twig', $context );
// } 