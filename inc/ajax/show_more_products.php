<?php
function ajax_load_more_product(){  
  $url = $_SERVER['REQUEST_URI'];
  $url_parts = parse_url($url, PHP_URL_QUERY);

  $url_pages = explode("/", $url)[3];
  $url_pages_value = explode("/", $url)[4];

  if (!empty($url_parts)) {
    parse_str($url_parts, $query_params);
  }

  $tax_query = [];
  $i = 0;

  foreach ($query_params as $key => $value) {
    $filter = '';
    if (explode("_", $key)[0] == 'filter' ) {
      $filter = explode("_", $key)[1];
      $i++;

      $value = explode(',', $value);

      if ($value[0] !== '') {
        $tax_query[$i] = array(
          'taxonomy'        => 'pa_' .  $filter,
          'field'           => 'slug',
          'terms'           => $value,
          'operator'        => 'IN'
        );
      }
    } 
  }

  if ($url_pages === 'brand' || $url_pages === 'size' || $url_pages === 'color') {
    $tax_query[$i++] = array(
      'taxonomy'        => 'pa_' . $url_pages,
      'field'           => 'slug',
      'terms'           => $url_pages_value,
      'operator'        => 'IN'
    );
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

  $number_posts = 16;

  $args = [
    'post_type' => 'product',
    'posts_per_page'   => $number_posts,
    'post_status' => 'publish',
    'paged' => $_POST['page'],
    'orderby' => $orderby,
    'order' => $order,
    'meta_key' => $meta_key,
    'offset' => $_POST['offset'],
  ];

  if ($_POST['taxonomy'] == 'product_cat') {
    $args['product_cat'] = $_POST['slug'];
  } 

  if (count($tax_query) > 0) {
    $args['tax_query'] = array('relation' => 'AND', $tax_query);
  }

  $response = get_products($args);

  if (empty($response)) {
    $response = "nomore";
  } 

  $output_JSON['args'] = $args;
  $output_JSON['products'] = $response;
  
  header('Content-Type: application/json');
  echo json_encode($output_JSON);
  wp_die();
}

add_action( 'wp_ajax_showMoreProducts', 'ajax_load_more_product' );
add_action( 'wp_ajax_nopriv_showMoreProducts', 'ajax_load_more_product' );