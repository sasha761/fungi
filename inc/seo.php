<?php
/**
 * SEO additions on top of Yoast SEO: Product schema.
 *
 * @package News
 */

/**
 * Return the plain-text description of a product for schema.
 *
 * The short description of the shop's products holds shipping and payment notes,
 * so the Yoast meta description (written per product) goes first.
 *
 * @param WC_Product $product        Product.
 * @param string     $yoast_desc     Yoast description of the current page.
 * @return string
 */
function fh_product_schema_description( $product, $yoast_desc ) {
	foreach ( array( $yoast_desc, $product->get_description(), $product->get_short_description() ) as $text ) {
		$text = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( strip_shortcodes( (string) $text ) ) ) );

		if ( '' !== $text ) {
			return $text;
		}
	}

	return '';
}

/**
 * Return a schema.org Offer for a product or a variation.
 *
 * @param WC_Product $product Product or variation.
 * @param string     $url     Offer URL.
 * @return array
 */
function fh_product_schema_offer( $product, $url ) {
	$availability = array(
		'instock'     => 'InStock',
		'outofstock'  => 'OutOfStock',
		'onbackorder' => 'BackOrder',
	);
	$status       = $product->get_stock_status();
	$sale_until   = $product->is_on_sale() ? $product->get_date_on_sale_to() : null;

	return array(
		'@type'           => 'Offer',
		'url'             => $url,
		'price'           => wc_format_decimal( $product->get_price(), wc_get_price_decimals() ),
		'priceCurrency'   => get_woocommerce_currency(),
		'availability'    => 'https://schema.org/' . ( isset( $availability[ $status ] ) ? $availability[ $status ] : 'InStock' ),
		'priceValidUntil' => $sale_until ? $sale_until->date( 'Y-m-d' ) : gmdate( 'Y-12-31', time() + YEAR_IN_SECONDS ),
	);
}

/**
 * Add a Product piece to the Yoast schema graph of a single product.
 *
 * The theme does not run woocommerce_single_product_summary, so WooCommerce never
 * prints its own Product markup; this piece is linked to the Yoast WebPage instead.
 *
 * @param array  $graph   Schema pieces.
 * @param object $context Yoast Meta_Tags_Context.
 * @return array
 */
function fh_product_schema_graph( $graph, $context ) {
	if ( ! is_singular( 'product' ) || ! function_exists( 'wc_get_product' ) ) {
		return $graph;
	}

	$product = wc_get_product( get_queried_object_id() );

	if ( ! $product ) {
		return $graph;
	}

	$url    = get_permalink( $product->get_id() );
	$images = array();

	foreach ( array_merge( array( $product->get_image_id() ), $product->get_gallery_image_ids() ) as $image_id ) {
		$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';

		if ( $image_url ) {
			$images[] = $image_url;
		}
	}

	$offers = array();

	if ( $product->is_type( 'variable' ) ) {
		foreach ( $product->get_children() as $variation_id ) {
			$variation = wc_get_product( $variation_id );

			if ( $variation && $variation->is_purchasable() && '' !== (string) $variation->get_price() ) {
				$offers[] = fh_product_schema_offer( $variation, $variation->get_permalink() );
			}
		}
	} elseif ( '' !== (string) $product->get_price() ) {
		$offers[] = fh_product_schema_offer( $product, $url );
	}

	if ( ! $offers ) {
		return $graph;
	}

	$piece = array(
		'@type'            => 'Product',
		'@id'              => $url . '#product',
		'name'             => wp_strip_all_tags( $product->get_name() ),
		'url'              => $url,
		'description'      => fh_product_schema_description( $product, isset( $context->description ) ? $context->description : '' ),
		'image'            => $images,
		'brand'            => array(
			'@type' => 'Brand',
			'name'  => 'Fungi Harmony',
		),
		'offers'           => 1 === count( $offers ) ? $offers[0] : $offers,
		'mainEntityOfPage' => array( '@id' => $context->main_schema_id ),
	);

	if ( $product->get_sku() ) {
		$piece['sku'] = $product->get_sku();
	}

	if ( $product->get_review_count() > 0 && (float) $product->get_average_rating() > 0 ) {
		$piece['aggregateRating'] = array(
			'@type'       => 'AggregateRating',
			'ratingValue' => wc_format_decimal( $product->get_average_rating(), 2 ),
			'reviewCount' => $product->get_review_count(),
			'bestRating'  => 5,
			'worstRating' => 1,
		);
	}

	$graph[] = array_filter( $piece );

	return $graph;
}
add_filter( 'wpseo_schema_graph', 'fh_product_schema_graph', 10, 2 );

// The Product piece above is the only product markup; never print a second one from WooCommerce.
add_filter( 'woocommerce_structured_data_product', '__return_empty_array' );
