<?php
function get_products($args = []) {  
  $defaults = [
    'numberposts'      => 20,
    'category'         => 0,
    'include'          => array(),
    'exclude'          => array(),
    'post_type'        => 'product',
    'posts_per_page'   => 20,
    'orderby'          => 'date',
    'suppress_filters' => false,
    'post_status'      => 'publish',
    'paged'            => 1,
    'orderby'          => 'date',
    'order'            => 'DESC',
    'meta_key'         => '',
    'fields'           => 'ids'
  ];

  $parsed_args = wp_parse_args($args, $defaults );

  $get_product_ids = get_posts($parsed_args);
  $products = [];
  
  foreach ($get_product_ids as $product_id) {
    $product = wc_get_product($product_id);

    if (!$product) continue; 
    
    $price_sale = $product->is_type('variable') ? $product->get_variation_sale_price() : $product->get_sale_price();
    $price_regular = $product->is_type('variable') ? $product->get_variation_regular_price() : $product->get_regular_price();

    if ($price_regular && $price_sale) {
      $percent = ($price_regular - $price_sale) / $price_regular * 100;
    } else {
      $percent = 0;
    }
    
    $thumbnail_id = get_post_thumbnail_id($product_id);
    
    $products[] = (object) [
      'ID'            => $product_id,
      'price'         => $product->get_price_html(),
      'regular_price' => $price_regular,
      'sale_price'    => $price_sale,
      'is_sale'       => $product->is_on_sale(),
      'percent'       => $percent,
      'outStock'      => $product->get_stock_status(),
      'link'          => get_the_permalink($product_id),
      'thumb_md'      => get_image_data($thumbnail_id, 'archive_md'),
      'thumb_xl'      => get_image_data($thumbnail_id, 'archive_xl'),
      'archive'       => get_image_data($thumbnail_id, 'archive'),
      'thumb_sm'      => get_image_data($thumbnail_id, [100, 100]),
      'title'         => get_the_title($product_id),
      'rating'        => $product->get_average_rating()
    ];
  }

  return $products;
}
