<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package News
 */

$context = Timber::context();

$timber_post = Timber::get_post();
$context['post'] = $timber_post;

$context['thumbnail'] = get_the_post_thumbnail_url($timber_post->id);
$context['author_name'] = get_the_author_meta('display_name', $timber_post->post_author);
$context['author_link'] = get_author_posts_url($timber_post->post_author);


// Получаем ID главной страницы
$homepage_id = get_option('page_on_front');

// Получаем ID страниц WooCommerce
$woocommerce_pages = [
	wc_get_page_id('shop'),
	wc_get_page_id('cart'),
	wc_get_page_id('checkout'),
	wc_get_page_id('myaccount'),
];

// Получаем все опубликованные страницы, кроме главной и страниц WooCommerce
$page_ids = get_posts(array(
	'post_type' => 'page',
	'post_status' => 'publish',
	'post__not_in' => array_merge([$homepage_id], $woocommerce_pages),
	'fields'       => 'ids',
	'suppress_filters' => false,
	'posts_per_page' => -1, // Получить все страницы
));


$pages = [];
foreach ($page_ids as $page_id) {
	$author_id = get_post_field('post_author', $page_id);
	$thumbnail_id = get_post_thumbnail_id($page_id);


	$pages[] = (object) [
		'ID'        => $page_id,
		'permalink' => get_the_permalink($page_id),
		'thumb_md'  => get_image_data($thumbnail_id, 'archive_md'),
		'thumb_xl'  => get_image_data($thumbnail_id, 'archive_xl'),
		'thumb_sm'  => get_image_data($thumbnail_id, [100, 100]),
		'title'     => get_the_title($page_id),
		'date'      => get_the_date('M j, Y', $page_id), // Формат: Jul 21, 2022
		'excerpt'   => get_the_excerpt($page_id),
	];
}

$context['pages']  = $pages;


$context['product_categories'] = get_taxonomy_data([
	'taxonomy' => 'category'
]);;


// $context['related'] = get_posts( $related_args );
Timber::render(array('page.twig'), $context );
