<?php
function add_to_cart_handler() {
  if (!isset($_POST['product_id'])) {
    wp_send_json_error('Invalid product ID');
    return;
  }

  $product_id = absint($_POST['product_id']);
  $quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 1;

  $added = WC()->cart->add_to_cart($product_id, $quantity);

  if ($added) {
    $cart_info = get_cart_info();

    wp_send_json_success([
      'cart_products' => $cart_info['cart_products'],
      'count' => $cart_info['count'],
      'total' => $cart_info['total'], 
    ]);
  } else {
    wp_send_json_error('Failed to add product to cart');
  }
}

add_action('wp_ajax_add_to_cart', 'add_to_cart_handler');
add_action('wp_ajax_nopriv_add_to_cart', 'add_to_cart_handler');