<?php
$main_language_code = 'en';
$context = Timber::context();

$post = get_post();
$product = wc_get_product($post->ID);
$product_id = $product->get_id();
$main_product_id       = get_main_post_id( $product_id, $main_language_code );


$thumbnail_id = get_post_thumbnail_id($product_id);
$images_ids = $product->get_gallery_image_ids();
$images = [];



$comments = get_all_language_comments( $main_product_id );
$nested_comments = nest_comments( $comments );

foreach ($images_ids as $image_id) {
  $images[] = [
    'full'  => get_image_data($image_id, 'full'),
		'single_xl'  => get_image_data($image_id, 'single_xl'),
		'archive_xl'  => get_image_data($image_id, 'archive_xl'),
  ];
}

$data = [
  'id' => $product_id,
  'main_post_id'   => $main_product_id,
  'product' => $product,
  'stock_status' => $product->get_stock_status(),
  'tags' => get_the_terms($product_id, 'product_tag'),
  'categories' => get_the_terms($product_id, 'product_cat'),
  'rating' => $product->get_average_rating(),
  'count' => $product->get_rating_count(),
  'comments'       => $nested_comments,
  'total_comments' => count($comments),
  'how_to_use' => get_field('how_to_use', $product_id),
  'composition' => get_field('composition', $product_id),
  'sertificates' => get_field('sertificates', $product_id),
  'related_products' => get_products(['posts_per_page' => 8]),
  'price_html' => $product->get_price_html(),
  'is_sale' => $product->is_on_sale(),
  'images' => $images,
  'thumbnail' => get_image_data($thumbnail_id, 'full'),
  'thumbnail_xl' => get_image_data($thumbnail_id, 'single_xl'),
  'thumbnail_md' => get_image_data($thumbnail_id, 'archive_xl'),
  'permalink' => get_the_permalink($product_id),
  'price_sale' => $product->is_type('variable') ? $product->get_variation_sale_price() : $product->get_sale_price(),
  'price_regular' => $product->is_type('variable') ? $product->get_variation_regular_price() : $product->get_regular_price(),
];

if ($product->is_type( 'variable' )) {
  wp_enqueue_script( 'wc-add-to-cart-variation' );
  $variations = $product->get_available_variations();

  $variations_json = wp_json_encode( $variations );
  $variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

  $data['variation_attr'] = $variations_attr;
  $variations_attributes = $product->get_variation_attributes();
  $data['variations_attributes'] = $variations_attributes;
} 

$context['data'] = $data;

$context['product_tags'] = get_taxonomy_data([
	'taxonomy'   => 'product_tag',
  'hide_empty' => false, 
]);

$context['popular_posts'] = get_posts_info([
  'meta_key' => 'count_post_viewed',
  'orderby'  => 'meta_value_num',
], $is_emoji = false);

Timber::render( 'templates/woo/single-product.twig', $context );