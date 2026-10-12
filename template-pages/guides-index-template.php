<?php
/**
 * Template Name: Guides Index
 * Template Post Type: page
 *
 * @package News
 */

$context         = Timber::context();
$guides_index_id = get_queried_object_id();
$pillar_pages    = array();
$pillars_query   = new WP_Query(
	array(
		'post_type'              => 'page',
		'post_status'            => 'publish',
		'posts_per_page'         => -1,
		'orderby'                => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
		'meta_key'               => '_wp_page_template',
		'meta_value'             => 'template-pages/pillar-template.php',
		'suppress_filters'       => false,
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
	)
);

foreach ( $pillars_query->posts as $pillar_page ) {
	$pillar_id    = $pillar_page->ID;
	$thumbnail_id = get_post_thumbnail_id( $pillar_id );

	$pillar_pages[] = array(
		'ID'        => $pillar_id,
		'permalink' => get_permalink( $pillar_id ),
		'title'     => get_the_title( $pillar_id ),
		'excerpt'   => get_the_excerpt( $pillar_id ),
		'thumbnail' => get_image_data( $thumbnail_id, 'archive_post' ),
	);
}

$context['guides_index'] = array(
	'title'   => get_the_title( $guides_index_id ),
	'content' => wp_kses_post( apply_filters( 'the_content', get_post_field( 'post_content', $guides_index_id ) ) ),
);
$context['pillar_pages'] = $pillar_pages;

Timber::render( array( 'template-guides-index.twig', 'page.twig' ), $context );
