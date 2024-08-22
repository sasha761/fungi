<?php
// Подключаем Timber
$templates = array( 'author.twig', 'archive.twig' );

$context = Timber::context();

// Получаем текущего автора
$author_id = get_query_var('author');
// $author = new User($author_id);
// $author = Timber::get_user($author_id);

// Получаем все посты автора
$args = array(
  'author' => $author_id,
  'post_type' => 'post',
  'posts_per_page' => -1, // Получить все посты автора
);
$posts = get_posts_info($args);

// Получаем кастомные поля автора
$user_photo = get_field('user_photo', 'user_' . $author_id);
$user_description = get_field('user_description', 'user_' . $author_id);

// Подготавливаем контекст для Timber
$context = Timber::context();
$context['author_name'] = get_the_author_meta('first_name', $author_id);
$context['author_surname'] = get_the_author_meta('last_name', $author_id);

$context['posts'] = $posts;
$context['user_photo'] = $user_photo;
$context['user_description'] = $user_description;

$context['product_categories'] = get_taxonomy_data([
	'taxonomy' => 'category'
]);;

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
	$pages[] = (object) [
		'ID'        => $page_id,
		'permalink' => get_the_permalink($page_id),
		'thumb_md'  => get_the_post_thumbnail_url($page_id, 'archive_md'),
		'thumb_xl'  => get_the_post_thumbnail_url($page_id, 'archive_xl'),
		'thumb_sm'  => get_the_post_thumbnail_url($page_id, array(100, 100)),
		'title'     => get_the_title($page_id),
		'date'      => get_the_date('M j, Y', $page_id), // Формат: Jul 21, 2022
		'excerpt'   => get_the_excerpt($page_id),
	];
}

$context['pages']  = $pages;


// Рендерим шаблон
Timber::render('author.twig', $context);
