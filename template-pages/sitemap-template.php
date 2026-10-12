<?php
/**
 * Template Name: Sitemap
 * Template Post Type: page
 *
 * @package News
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$context         = Timber::context();
$current_page_id = get_queried_object_id();
$sitemap_pages   = get_posts(
	array(
		'post_type'              => 'page',
		'post_status'            => 'publish',
		'posts_per_page'         => -1,
		'post__not_in'           => array( $current_page_id ),
		'orderby'                => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
		'suppress_filters'       => false,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	)
);
$sitemap_categories = get_categories(
	array(
		'hide_empty' => true,
		'orderby'    => 'name',
		'order'      => 'ASC',
	)
);
$sitemap_posts      = get_posts(
	array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => -1,
		'orderby'                => 'title',
		'order'                  => 'ASC',
		'suppress_filters'       => false,
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	)
);

$map_posts_to_items = static function ( array $posts ): array {
	return array_values(
		array_filter(
			array_map(
				static function ( WP_Post $post_item ): ?array {
					$title = wp_specialchars_decode( get_the_title( $post_item ), ENT_QUOTES );
					$url   = get_permalink( $post_item );

					if ( '' === $title || ! $url ) {
						return null;
					}

					return array(
						'label' => $title,
						'url'   => $url,
					);
				},
				$posts
			)
		)
	);
};

$category_items = array_values(
	array_filter(
		array_map(
			static function ( WP_Term $category ): ?array {
				$url = get_category_link( $category->term_id );

				if ( is_wp_error( $url ) ) {
					return null;
				}

				return array(
					'label' => wp_specialchars_decode( $category->name, ENT_QUOTES ),
					'url'   => $url,
				);
			},
			$sitemap_categories
		)
	)
);

$context['post']             = Timber::get_post();
$context['sitemap_sections'] = array(
	array(
		'id'    => 'sitemap-pages',
		'title' => news_translate_string( 'Pages', 'Sitemap - Pages' ),
		'items' => $map_posts_to_items( $sitemap_pages ),
	),
	array(
		'id'    => 'sitemap-categories',
		'title' => news_translate_string( 'Categories', 'Sitemap - Categories' ),
		'items' => $category_items,
	),
	array(
		'id'    => 'sitemap-posts',
		'title' => news_translate_string( 'Posts', 'Sitemap - Posts' ),
		'items' => $map_posts_to_items( $sitemap_posts ),
	),
);

Timber::render( array( 'template-sitemap.twig', 'page.twig' ), $context );
