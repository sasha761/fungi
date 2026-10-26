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
 * Return a post title as plain text for Twig, which escapes it with |e.
 *
 * get_the_title() is texturized HTML ("Lion&#8217;s"); printing it with |e would
 * show the entity itself, and wp_specialchars_decode() leaves numeric entities.
 *
 * @param int|WP_Post $post Post ID or object.
 * @return string
 */
function fh_plain_title( $post ) {
	return html_entity_decode( get_the_title( $post ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
}

/**
 * Describe a published page for links and breadcrumbs.
 *
 * 'title' is plain text for Twig; 'crumb' stays HTML because Yoast prints breadcrumb text as is.
 *
 * @param int $page_id Page ID.
 * @return array|null
 */
function fh_guide_page_link( $page_id ) {
	if ( ! $page_id || 'page' !== get_post_type( $page_id ) || 'publish' !== get_post_status( $page_id ) ) {
		return null;
	}

	$crumb_title = trim( (string) get_post_meta( $page_id, '_yoast_wpseo_bctitle', true ) );

	return array(
		'id'    => $page_id,
		'title' => fh_plain_title( $page_id ),
		'crumb' => '' !== $crumb_title ? esc_html( $crumb_title ) : get_the_title( $page_id ),
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

/**
 * Register fh_guide_order: position of a post in its guide, starting from 1.
 *
 * @return void
 */
function fh_guide_register_meta() {
	register_post_meta(
		'post',
		'fh_guide_order',
		array(
			'type'              => 'integer',
			'description'       => 'Position of the post in its guide, starting from 1.',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
			'auth_callback'     => static function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'fh_guide_register_meta' );

/**
 * Add the "Guide order" box to the post editor sidebar.
 *
 * @return void
 */
function fh_guide_order_add_meta_box() {
	add_meta_box( 'fh-guide-order', __( 'Guide order', 'news' ), 'fh_guide_order_meta_box', 'post', 'side' );
}
add_action( 'add_meta_boxes', 'fh_guide_order_add_meta_box' );

/**
 * Render the "Guide order" box.
 *
 * @param WP_Post $post Current post.
 * @return void
 */
function fh_guide_order_meta_box( $post ) {
	$order = (int) get_post_meta( $post->ID, 'fh_guide_order', true );

	wp_nonce_field( 'fh_guide_order_save', 'fh_guide_order_nonce' );
	?>
	<label for="fh-guide-order-field" class="screen-reader-text"><?php esc_html_e( 'Guide order', 'news' ); ?></label>
	<input type="number" min="1" step="1" id="fh-guide-order-field" name="fh_guide_order" value="<?php echo $order ? esc_attr( $order ) : ''; ?>" style="width:100%">
	<p class="description"><?php esc_html_e( 'Position of the post in its guide, starting from 1. Posts without a position go to the end.', 'news' ); ?></p>
	<?php
}

/**
 * Save the "Guide order" box.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function fh_guide_order_save( $post_id ) {
	if ( ! isset( $_POST['fh_guide_order_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fh_guide_order_nonce'] ) ), 'fh_guide_order_save' ) ) {
		return;
	}

	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$order = isset( $_POST['fh_guide_order'] ) ? absint( wp_unslash( $_POST['fh_guide_order'] ) ) : 0;

	if ( $order ) {
		update_post_meta( $post_id, 'fh_guide_order', $order );
	} else {
		delete_post_meta( $post_id, 'fh_guide_order' );
	}
}
add_action( 'save_post_post', 'fh_guide_order_save' );

/**
 * Return the published posts of a guide in the current language, ordered by fh_guide_order.
 *
 * Posts without a position follow the ordered ones, oldest first.
 *
 * @param string $key Guide key.
 * @return int[]
 */
function fh_guide_posts( $key ) {
	static $cache = array();

	$map = fh_guide_map();

	if ( ! isset( $map[ $key ] ) ) {
		return array();
	}

	$category_id = absint( apply_filters( 'wpml_object_id', $map[ $key ]['categories'][0], 'category', false ) );

	if ( ! $category_id ) {
		return array();
	}

	if ( isset( $cache[ $category_id ] ) ) {
		return $cache[ $category_id ];
	}

	$post_ids = get_posts(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => -1,
			'fields'              => 'ids',
			'category__in'        => array( $category_id ),
			'orderby'             => 'date',
			'order'               => 'ASC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'suppress_filters'    => false,
		)
	);

	update_meta_cache( 'post', $post_ids );

	$post_ids = array_values(
		array_filter(
			array_map( 'intval', $post_ids ),
			static function ( $post_id ) use ( $key ) {
				$guide = fh_guide_for_post( $post_id );

				return $guide && $key === $guide['key'];
			}
		)
	);

	usort(
		$post_ids,
		static function ( $a, $b ) {
			$order_a = (int) get_post_meta( $a, 'fh_guide_order', true );
			$order_b = (int) get_post_meta( $b, 'fh_guide_order', true );

			return ( $order_a ?: PHP_INT_MAX ) <=> ( $order_b ?: PHP_INT_MAX );
		}
	);

	$cache[ $category_id ] = $post_ids;

	return $post_ids;
}

/**
 * Return a one-line description of a post or page for cards.
 *
 * The Yoast meta description goes first: excerpts of many posts still start with
 * the manual "Author: … Updated: …" line.
 *
 * @param int $post_id Post or page ID.
 * @return string
 */
function fh_guide_card_description( $post_id ) {
	$description = '';

	if ( function_exists( 'YoastSEO' ) ) {
		try {
			$meta        = YoastSEO()->meta->for_post( $post_id );
			$description = $meta ? (string) $meta->description : '';
		} catch ( Exception $e ) {
			$description = '';
		}
	}

	if ( '' === trim( $description ) ) {
		$description = has_excerpt( $post_id ) ? get_post_field( 'post_excerpt', $post_id ) : wp_trim_words( wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $post_id ) ) ), 30 );
	}

	return trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( html_entity_decode( $description, ENT_QUOTES, 'UTF-8' ) ) ) );
}

/**
 * Return card data of a post or page (the keys of partial/guide-card.twig).
 *
 * @param int $post_id Post or page ID.
 * @return array
 */
function fh_guide_card( $post_id ) {
	return array(
		'ID'        => $post_id,
		'permalink' => get_permalink( $post_id ),
		'title'     => fh_plain_title( $post_id ),
		'excerpt'   => fh_guide_card_description( $post_id ),
		'thumbnail' => get_image_data( get_post_thumbnail_id( $post_id ), 'archive_post' ),
	);
}

/**
 * Return the previous and next cards of a post inside its guide.
 *
 * The first post links back to the pillar instead of a previous post, the last
 * post links to the pillar instead of a next post.
 *
 * @param int $post_id Post ID.
 * @return array List of cards with 'direction' and 'label', empty when the post is not in a guide.
 */
function fh_guide_neighbours( $post_id ) {
	$guide = fh_guide_for_post( $post_id );

	if ( ! $guide || ! $guide['key'] ) {
		return array();
	}

	$post_ids = fh_guide_posts( $guide['key'] );
	$position = array_search( (int) $post_id, $post_ids, true );
	$pillar   = fh_guide_card( $guide['pillar']['id'] );
	$labels   = news_ui_labels();

	$previous = false !== $position && $position > 0 ? fh_guide_card( $post_ids[ $position - 1 ] ) : null;
	$next     = false !== $position && $position < count( $post_ids ) - 1 ? fh_guide_card( $post_ids[ $position + 1 ] ) : null;

	return array(
		$previous
			? $previous + array( 'direction' => 'prev', 'rel' => 'prev', 'label' => $labels['guide_previous'] )
			: $pillar + array( 'direction' => 'prev', 'rel' => '', 'label' => $labels['guide_back'] ),
		$next
			? $next + array( 'direction' => 'next', 'rel' => 'next', 'label' => $labels['guide_next'] )
			: $pillar + array( 'direction' => 'next', 'rel' => '', 'label' => $labels['guide_back'] ),
	);
}

/**
 * Return the next posts of the same guide, wrapping to the start of the guide.
 *
 * @param int $post_id Post ID.
 * @param int $limit   Number of posts.
 * @return array Cards of partial/guide-card.twig.
 */
function fh_guide_more( $post_id, $limit = 3 ) {
	$guide = fh_guide_for_post( $post_id );

	if ( ! $guide || ! $guide['key'] ) {
		return array();
	}

	$post_ids = fh_guide_posts( $guide['key'] );
	$position = array_search( (int) $post_id, $post_ids, true );
	$count    = count( $post_ids );
	$cards    = array();

	if ( false === $position ) {
		$position = -1;
	}

	for ( $step = 1; $step < $count + 1 && count( $cards ) < $limit; $step++ ) {
		$next_id = $post_ids[ ( $position + $step ) % $count ];

		if ( (int) $post_id !== $next_id ) {
			$cards[] = fh_guide_card( $next_id );
		}
	}

	return $cards;
}

/**
 * Return species product categories (EN IDs) with the main guide post of each species (EN ID).
 *
 * @return array
 */
function fh_shop_species_map() {
	return apply_filters(
		'fh_shop_species_map',
		array(
			224 => array( 'article' => 1638, 'amanita' => false ), // Lion's mane.
			227 => array( 'article' => 1396, 'amanita' => false ), // Cordyceps militaris.
			230 => array( 'article' => 682, 'amanita' => true ),   // Red fly agaric.
			233 => array( 'article' => 1437, 'amanita' => true ),  // Panther amanita.
			236 => array( 'article' => 1306, 'amanita' => true ),  // Royal amanita.
		)
	);
}

/**
 * Return a link to a published post or page in the current language.
 *
 * @param int    $post_id Original post ID.
 * @param string $type    Post type.
 * @return array|null
 */
function fh_guide_translated_link( $post_id, $type ) {
	$post_id = fh_translated_id( $post_id, $type );

	if ( ! $post_id || $type !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
		return null;
	}

	return array(
		'id'    => $post_id,
		'url'   => get_permalink( $post_id ),
		'title' => fh_plain_title( $post_id ),
	);
}

/**
 * Return the guide blocks under the products of a species category: main article of the
 * species, the "How to choose a supplement" guide, other species and, for amanitas,
 * a safety note without doses.
 *
 * @param int $term_id Product category ID.
 * @return array|null
 */
function fh_shop_category_guide( $term_id ) {
	$map         = fh_shop_species_map();
	$original_id = absint( apply_filters( 'wpml_object_id', $term_id, 'product_cat', true, 'en' ) );

	if ( ! isset( $map[ $original_id ] ) ) {
		return null;
	}

	$cards   = array();
	$article = fh_guide_translated_link( $map[ $original_id ]['article'], 'post' );
	$guides  = fh_guide_map();
	$choose  = fh_guide_translated_link( $guides['G2']['page'], 'page' );

	if ( $article ) {
		$cards[] = fh_guide_card( $article['id'] ) + array( 'label' => __( 'Main article about this species', 'news' ) );
	}

	if ( $choose ) {
		$cards[] = fh_guide_card( $choose['id'] ) + array( 'label' => __( 'How to choose a supplement', 'news' ) );
	}

	$neighbours = array();

	foreach ( array_keys( $map ) as $species_id ) {
		$species_term = $species_id === $original_id ? null : get_term( fh_translated_id( $species_id, 'product_cat' ), 'product_cat' );

		if ( $species_term instanceof WP_Term && $species_term->count ) {
			$neighbours[] = array(
				'url'   => get_term_link( $species_term ),
				'title' => html_entity_decode( $species_term->name, ENT_QUOTES | ENT_HTML5, 'UTF-8' ),
			);
		}
	}

	$safety = null;

	if ( $map[ $original_id ]['amanita'] ) {
		// M4: microdosing dosages compared; N17: how Amanita muscaria is prepared.
		$safety = array(
			'heading'     => __( 'Amanita safety', 'news' ),
			'text'        => __( 'Amanita mushrooms contain ibotenic acid and muscimol. Their amount differs from one mushroom to another, so the effect is hard to predict. Do not combine them with alcohol, sedatives or other psychoactive substances, and talk to a doctor first if you take medicines or have heart, liver, kidney or mental health conditions. Avoid them during pregnancy and breastfeeding and keep them away from children and pets. Vomiting, confusion, agitation, seizures or trouble breathing need emergency medical help.', 'news' ),
			'links_label' => __( 'Read before use:', 'news' ),
			'links'       => array_values( array_filter( array( fh_guide_translated_link( 1815, 'post' ), fh_guide_translated_link( 2416, 'post' ) ) ) ),
		);
	}

	return array(
		'cards'            => $cards,
		'neighbours_label' => __( 'Other species:', 'news' ),
		'neighbours'       => $neighbours,
		'safety'           => $safety,
	);
}
