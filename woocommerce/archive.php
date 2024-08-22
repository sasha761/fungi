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

$context['products'] = get_products([
  'paged' => $paged, 
  'orderby' => $orderby, 
  'order' => $order, 
  'meta_key' => $meta_key, 
  'posts_per_page' => 21
]);
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


if ( is_product_category() || is_shop() || is_tax() ) {
  $query_url = $_SERVER['QUERY_STRING'];
  // $term_id = get_queried_object()->term_id;


  if ($query_url == '') {
    // $context['description'] = term_description( $term_id );
    $context['title'] = $title;
  } else {
    $url = $_SERVER['HTTP_REFERER'];
    parse_str($query_url, $output);

    $title_variable = '';

    switch(!empty($output['orderby'])){
      case 'default':
          break;
      case 'popularity':  
          $title_variable .= '| Популярные';
          break;
      case 'price':
          $title_variable .= '| Цены по возрастанию';
          break;
      case 'price-desc':
          $title_variable .= '| Цены по убыванию';
          break;
      case 'date':
          $title_variable .= '| Новые';
          break;
      case 'rating':
          $title_variable .= '| Рейтинговые';
          break;
      default:   
    }

    $context['title'] = $title . '. ' . $title_variable;
  }
}


Timber::render( 'templates/woo/archive.twig', $context );