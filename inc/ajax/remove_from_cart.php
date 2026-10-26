<?php
function remove_from_cart_handler() {
  if (!isset($_POST['cart_item_key'])) {
    wp_send_json_error(news_translate_string('Invalid cart item.', 'AJAX - Invalid cart item'));
    return;
  }

  $cart_item_key = sanitize_text_field(wp_unslash($_POST['cart_item_key']));

  if (WC()->cart->get_cart_item($cart_item_key)) {
    WC()->cart->remove_cart_item($cart_item_key);

    $cart_info = get_cart_info();

    wp_send_json_success([
      'cart_products' => $cart_info['cart_products'],
      'count' => $cart_info['count'],
      'total' => $cart_info['total'], 
    ]);
  } else {
    wp_send_json_error(news_translate_string('Could not remove the product from the cart.', 'AJAX - Remove from cart failed'));
  }
}

add_action('wp_ajax_remove_from_cart', 'remove_from_cart_handler');
add_action('wp_ajax_nopriv_remove_from_cart', 'remove_from_cart_handler');
