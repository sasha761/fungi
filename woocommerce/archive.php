<?php
$context = Timber::context();
$url = $_SERVER['REQUEST_URI'];
$url_parts = parse_url($url, PHP_URL_QUERY);

if (!empty($url_parts)) {
  parse_str($url_parts, $query_params);
}

if (!empty($query_params['orderby'])) {
  switch ($query_params['orderby']) {
    case 'default':
      $meta_key = '';
      $order = 'desc';
      $orderby = 'date';
      break;
    case 'popularity':
      $meta_key = 'total_sales';
      $order = 'desc';
      $orderby = 'meta_value_num date';
      break;
    case 'price':
      $meta_key = '_price';
      $order = 'asc';
      $orderby = 'meta_value_num date';
      break;
    case 'price-desc':
      $meta_key = '_price';
      $order = 'desc';
      $orderby = 'meta_value_num date';
      break;
    case 'date':
      $meta_key = '';
      $order = 'desc';
      $orderby = 'date';
      break;
    case 'rating':
      $meta_key = '_wc_average_rating';
      $order = 'desc';
      $orderby = 'meta_value_num date';
      break;
    default:
      $meta_key = '';
      $order = 'desc';
      $orderby = 'date';
  }
} else {
  $meta_key = '';
  $order = 'desc';
  $orderby = 'date';
}

$paged = get_query_var('paged') ? get_query_var('paged') : 1;

$product_args = [
  'paged' => $paged, 
  'orderby' => $orderby, 
  'order' => $order, 
  'meta_key' => $meta_key, 
  'posts_per_page' => 21
];

if (is_product_category()) {
  $term_id = get_queried_object_id();
  $product_args['tax_query'] = [
    [
      'taxonomy' => 'product_cat',
      'field' => 'term_id',
      'terms' => [$term_id],
    ],
  ];
  $description = wp_kses_post(term_description($term_id, 'product_cat'));

  // The first paragraph of the category text is the intro above the products; the rest stays under the grid.
  if (preg_match('~^\s*(<p\b[^>]*>.*?</p>)~s', $description, $intro)) {
    $context['intro'] = $intro[1];
    $description      = substr($description, strlen($intro[0]));
  }

  $context['description']    = trim($description) !== '' ? $description : '';
  $context['category_guide'] = fh_shop_category_guide($term_id);
}

$context['products'] = get_products($product_args);
$context['product_tags'] = get_taxonomy_data([
	'taxonomy'   => 'product_tag',
  'hide_empty' => false, 
]);

$context['popular_posts'] = get_posts_info([
  'meta_key' => 'count_post_viewed',
  'orderby'  => 'meta_value_num',
], $is_emoji = false);

$context['query_object'] = get_queried_object();
// $context['banner'] = get_field('banner', $context['query_object']);
// $context['acf_title'] = get_field('title', $context['query_object']);
$context['current_page'] = get_query_var('paged') ? get_query_var('paged') : 1;

// if ($context['acf_title']) {
//   $title = $context['acf_title'];
// } else {
$title = woocommerce_page_title($echo = false);
// }


// The H1 is the archive title only: any query string (utm, fbclid, orderby) used to add ". " to it.
$context['title'] = $title;


Timber::render( 'templates/woo/archive.twig', $context );
