<?php
function remove_from_cart_handler() {
  if (!isset($_POST['cart_item_key'])) {
    wp_send_json_error('Invalid cart item key');
    return;
  }

  $cart_item_key = sanitize_text_field($_POST['cart_item_key']);

  if (WC()->cart->get_cart_item($cart_item_key)) {
    WC()->cart->remove_cart_item($cart_item_key);

    $cart_info = get_cart_info();

    wp_send_json_success([
      'cart_products' => $cart_info['cart_products'],
      'count' => $cart_info['count'],
      'total' => $cart_info['total'], 
    ]);
  } else {
    wp_send_json_error('Failed to remove product from cart');
  }
}

add_action('wp_ajax_remove_from_cart', 'remove_from_cart_handler');
add_action('wp_ajax_nopriv_remove_from_cart', 'remove_from_cart_handler');