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
$listed          = array();
$start_label     = __( 'Start here:', 'news' );

// The five guides in their fixed order, each with the first posts of the guide.
foreach ( fh_guide_map() as $guide_key => $guide ) {
	$pillar_id = fh_translated_id( $guide['page'], 'page' );

	if ( 'publish' !== get_post_status( $pillar_id ) ) {
		continue;
	}

	$card                = fh_guide_card( $pillar_id );
	$card['links_label'] = $start_label;
	$card['links']       = array_map(
		static function ( $post_id ) {
			return array(
				'url'   => get_permalink( $post_id ),
				'title' => fh_plain_title( $post_id ),
			);
		},
		array_slice( fh_guide_posts( $guide_key ), 0, 3 )
	);

	$pillar_pages[]       = $card;
	$listed[ $pillar_id ] = true;
}

// Other pages on the pillar template follow, as before.
$pillars_query = new WP_Query(
	array(
		'post_type'              => 'page',
		'post_status'            => 'publish',
		'posts_per_page'         => -1,
		'fields'                 => 'ids',
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

foreach ( $pillars_query->posts as $pillar_id ) {
	if ( ! isset( $listed[ $pillar_id ] ) ) {
		$pillar_pages[] = fh_guide_card( $pillar_id );
	}
}

$context['guides_index'] = array(
	'title'   => fh_plain_title( $guides_index_id ),
	'content' => wp_kses_post( apply_filters( 'the_content', get_post_field( 'post_content', $guides_index_id ) ) ),
);
$context['pillar_pages'] = $pillar_pages;

Timber::render( array( 'template-guides-index.twig', 'page.twig' ), $context );
