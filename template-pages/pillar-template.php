<?php
/**
 * Template Name: Pillar Guide
 * Template Post Type: page
 *
 * @package News
 */

$context       = Timber::context();
$pillar_id     = get_queried_object_id();
$cluster_posts = function_exists( 'get_field' ) ? get_field( 'cluster_posts', $pillar_id ) : array();
$cluster_data  = array();
$seen_posts    = array();

foreach ( (array) $cluster_posts as $cluster_post ) {
	$cluster_id = $cluster_post instanceof WP_Post ? $cluster_post->ID : absint( $cluster_post );
	$cluster_id = fh_translated_id( $cluster_id, 'post' );

	if ( ! $cluster_id || isset( $seen_posts[ $cluster_id ] ) || 'post' !== get_post_type( $cluster_id ) ) {
		continue;
	}

	$seen_posts[ $cluster_id ] = true;
	$thumbnail_id              = get_post_thumbnail_id( $cluster_id );

	$cluster_data[] = array(
		'ID'        => $cluster_id,
		'permalink' => get_permalink( $cluster_id ),
		'title'     => get_the_title( $cluster_id ),
		'excerpt'   => get_the_excerpt( $cluster_id ),
		'thumbnail' => get_image_data( $thumbnail_id, 'archive_post' ),
	);
}

$pillar_content = wp_kses_post( apply_filters( 'the_content', get_post_field( 'post_content', $pillar_id ) ) );

$context['pillar']          = array(
	'title'   => get_the_title( $pillar_id ),
	'content' => $pillar_content,
	// Collected by the_content (inc/content.php): sidebar table of contents, sticky on desktop.
	'toc'     => fh_content_toc_html( fh_content_toc( $pillar_id ), true ),
);
$context['cluster_posts']   = $cluster_data;
$context['cluster_heading'] = __( 'Articles in this guide', 'news' );

Timber::render( array( 'template-pillar.twig', 'page.twig' ), $context );
