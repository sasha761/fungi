<?php
/* Template Name: home page
 * Template Post Type: page
 */

$context = Timber::context();

$context['blog']  = get_posts_info([
  'posts_per_page' => 5,
  'order' => 'DESC',
  'orderby' => 'date',
  'post_type' => 'post'
]);

$context['products'] = get_products([
  'posts_per_page' => 16
]);

$context['product_categories'] = get_taxonomy_data([
	'taxonomy' => 'category'
]);

$context['popular_posts'] = get_posts_info([
  'meta_key' => 'count_post_viewed',
  'orderby'  => 'meta_value_num',
], $is_emoji = false);

$context['shop_link'] = get_the_permalink(wc_get_page_id('shop'));

$context['blog_section'] = get_field('blog_section');
$context['about_us_section'] = get_field('about_us_section');
$context['our_products'] = get_field('our_products');
$context['our_vision'] = get_field('our_vision');
$context['hero_section'] = get_field('hero_section');


Timber::render( array( 'template-home.twig', 'page.twig' ), $context );