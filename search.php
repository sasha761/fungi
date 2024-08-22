<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package News
 */

	$templates = array( 'search.twig' );

	$context          = Timber::context();
	$context['title'] = get_search_query();
	// Получение постов
	$post_args = array(
		's' => get_search_query(),
		'post_type' => 'post',
		'numberposts' => -1, // Получить все посты, соответствующие запросу
		'fields' => 'ids'
	);
	$context['posts'] = get_posts_info($post_args, true);

	// Получение продуктов
	$product_args = array(
		's' => get_search_query(),
		'post_type' => 'product',
		'posts_per_page' => -1, // Получить все продукты, соответствующие запросу
		'fields' => 'ids'
	);
	$context['products'] = get_products($product_args);

	$context['product_tags'] = get_taxonomy_data([
		'taxonomy'   => 'product_tag',
		'hide_empty' => false, 
	]);
	
	$context['popular_posts'] = get_posts_info([
		'meta_key' => 'count_post_viewed',
		'orderby'  => 'meta_value_num',
	], $is_emoji = false);


Timber::render( $templates, $context );
