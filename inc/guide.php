<?php
/**
 * Guide structure: which pillar guide a post belongs to, and the guide breadcrumbs.
 *
 * Every blog post belongs to exactly one pillar category. The category decides the
 * pillar page; IDs are the same on production and staging and are translated to the
 * language of the current page through WPML.
 *
 * @package News
 */

/**
 * Return the guides: pillar page and pillar categories (EN, RU, UK).
 *
 * Page IDs are English originals and are translated with fh_translated_id().
 *
 * @return array
 */
function fh_guide_map() {
	static $map = null;

	if ( null === $map ) {
		$map = apply_filters(
			'fh_guide_map',
			array(
				'G1' => array(
					'page'       => 1979,
					'categories' => array( 208, 213, 218 ),
				),
				'G2' => array(
					'page'       => 1981,
					'categories' => array( 209, 214, 219 ),
				),
				'G3' => array(
					'page'       => 1977,
					'categories' => array( 210, 215, 220 ),
				),
				'G4' => array(
					'page'       => 1964,
					'categories' => array( 211, 216, 221 ),
				),
				'G5' => array(
					'page'       => 1975,
					'categories' => array( 212, 217, 222 ),
				),
			)
		);
	}

	return $map;
}

/**
 * Return the guides index page (/guide/) in the current language.
 *
 * @return int
 */
function fh_guide_index_id() {
	return fh_translated_id( (int) apply_filters( 'fh_guide_index_page', 1972 ), 'page' );
}

/**
 * Find the guide of a pillar category in any language.
 *
 * @param int $term_id Category ID.
 * @return string|null Guide key.
 */
function fh_guide_key_by_category( $term_id ) {
	$term_id = absint( $term_id );

	if ( ! $term_id ) {
		return null;
	}

	$original_id = absint( apply_filters( 'wpml_object_id', $term_id, 'category', false, 'en' ) );

	foreach ( fh_guide_map() as $key => $guide ) {
		if ( in_array( $term_id, $guide['categories'], true ) || ( $original_id && $original_id === $guide['categories'][0] ) ) {
			return $key;
		}
	}

	return null;
}

/**
 * Find the guide of a pillar page in any language.
 *
 * @param int $page_id Page ID.
 * @return string|null Guide key.
 */
function fh_guide_key_by_page( $page_id ) {
	$page_id     = absint( $page_id );
	$original_id = absint( apply_filters( 'wpml_object_id', $page_id, 'page', true, 'en' ) );

	foreach ( fh_guide_map() as $key => $guide ) {
		if ( $page_id === $guide['page'] || $original_id === $guide['page'] ) {
			return $key;
		}
	}

	return null;
}

/**
 * Find the guide of a post by its categories: Yoast primary category first,
 * then the first pillar category (the Blog category is not a guide).
 *
 * @param int $post_id Post ID.
 * @return string|null Guide key.
 */
function fh_guide_key_by_post_categories( $post_id ) {
	if ( function_exists( 'yoast_get_primary_term_id' ) ) {
		$key = fh_guide_key_by_category( yoast_get_primary_term_id( 'category', $post_id ) );

		if ( $key ) {
			return $key;
		}
	}

	foreach ( wp_get_post_categories( $post_id ) as $term_id ) {
		$key = fh_guide_key_by_category( $term_id );

		if ( $key ) {
			return $key;
		}
	}

	return null;
}

/**
 * Describe a published page for links and breadcrumbs.
 *
 * @param int $page_id Page ID.
 * @return array|null
 */
function fh_guide_page_link( $page_id ) {
	if ( ! $page_id || 'page' !== get_post_type( $page_id ) || 'publish' !== get_post_status( $page_id ) ) {
		return null;
	}

	$title       = get_the_title( $page_id );
	$crumb_title = trim( (string) get_post_meta( $page_id, '_yoast_wpseo_bctitle', true ) );

	return array(
		'id'    => $page_id,
		'title' => $title,
		'crumb' => '' !== $crumb_title ? $crumb_title : $title,
		'url'   => get_permalink( $page_id ),
	);
}

/**
 * Return the guide of a post: the pillar page and the guides index in the current language.
 *
 * A pillar chosen manually in the "Parent pillar" field wins over the categories.
 *
 * @param int $post_id Post ID.
 * @return array|null Array with 'key', 'pillar' and 'index', or null when the post is not in a guide.
 */
function fh_guide_for_post( $post_id ) {
	static $cache = array();

	$post_id = absint( $post_id );

	if ( ! $post_id || 'post' !== get_post_type( $post_id ) ) {
		return null;
	}

	if ( array_key_exists( $post_id, $cache ) ) {
		return $cache[ $post_id ];
	}

	$key       = null;
	$pillar_id = 0;
	$manual    = function_exists( 'get_field' ) ? get_field( 'parent_pillar', $post_id ) : get_post_meta( $post_id, 'parent_pillar', true );
	$manual_id = $manual instanceof WP_Post ? $manual->ID : absint( $manual );

	if ( $manual_id && 'page' === get_post_type( $manual_id ) ) {
		$key       = fh_guide_key_by_page( $manual_id );
		$pillar_id = fh_translated_id( $manual_id, 'page' );
	}

	if ( ! $pillar_id ) {
		$key = fh_guide_key_by_post_categories( $post_id );
		$map = fh_guide_map();

		if ( $key ) {
			$pillar_id = fh_translated_id( $map[ $key ]['page'], 'page' );
		}
	}

	$pillar = fh_guide_page_link( $pillar_id );

	$cache[ $post_id ] = $pillar
		? array(
			'key'    => $key,
			'pillar' => $pillar,
			'index'  => fh_guide_page_link( fh_guide_index_id() ),
		)
		: null;

	return $cache[ $post_id ];
}

/**
 * Build post breadcrumbs as Home › Guides › Pillar › Post.
 *
 * The same links feed the visible breadcrumbs and the Yoast BreadcrumbList schema.
 *
 * @param array $links Yoast breadcrumb links.
 * @return array
 */
function fh_guide_breadcrumb_links( $links ) {
	if ( ! is_array( $links ) || count( $links ) < 2 || ! is_singular( 'post' ) ) {
		return $links;
	}

	$guide = fh_guide_for_post( get_queried_object_id() );

	if ( ! $guide ) {
		return $links;
	}

	$crumbs = array( reset( $links ) );

	if ( $guide['index'] ) {
		$crumbs[] = array(
			'url'  => $guide['index']['url'],
			'text' => $guide['index']['crumb'],
		);
	}

	$crumbs[] = array(
		'url'  => $guide['pillar']['url'],
		'text' => $guide['pillar']['crumb'],
	);
	$crumbs[] = end( $links );

	return $crumbs;
}
add_filter( 'wpseo_breadcrumb_links', 'fh_guide_breadcrumb_links', 20 );
