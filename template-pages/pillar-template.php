<?php
/**
 * Template Name: Pillar Guide
 * Template Post Type: page
 *
 * @package News
 */

$context      = Timber::context();
$pillar_id    = get_queried_object_id();
$guide_key    = fh_guide_key_by_page( $pillar_id );
$thumbnail_id = get_post_thumbnail_id( $pillar_id );

// Posts of the guide in fh_guide_order; pillars outside the guide map keep the manual "Cluster posts" field.
if ( $guide_key ) {
	$cluster_ids = fh_guide_posts( $guide_key );
} else {
	$cluster_ids = array();

	foreach ( (array) ( function_exists( 'get_field' ) ? get_field( 'cluster_posts', $pillar_id ) : array() ) as $cluster_post ) {
		$cluster_id = fh_translated_id( $cluster_post instanceof WP_Post ? $cluster_post->ID : absint( $cluster_post ), 'post' );

		if ( $cluster_id && 'post' === get_post_type( $cluster_id ) && ! in_array( $cluster_id, $cluster_ids, true ) ) {
			$cluster_ids[] = $cluster_id;
		}
	}
}

$pillar_content = wp_kses_post( apply_filters( 'the_content', get_post_field( 'post_content', $pillar_id ) ) );

$context['pillar']          = array(
	'title'        => get_the_title( $pillar_id ),
	'trust'        => fh_article_trust( $pillar_id ),
	'thumbnail_xl' => get_image_data( $thumbnail_id, 'single_post_desktop' ),
	'thumbnail_md' => get_image_data( $thumbnail_id, 'single_post_phone' ),
	'content'      => $pillar_content,
	// Collected by the_content (inc/content.php): sidebar table of contents, sticky on desktop.
	'toc'          => fh_content_toc_html( fh_content_toc( $pillar_id ), true ),
);
$context['cluster_posts']   = array_map( 'fh_guide_card', $cluster_ids );
$context['cluster_heading'] = __( 'Articles in this guide', 'news' );

Timber::render( array( 'template-pillar.twig', 'page.twig' ), $context );
