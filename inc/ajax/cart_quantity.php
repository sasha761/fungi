<?php
function update_cart_quantity() {

  if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $quantity   = intval($_POST['quantity']);
    $lang = sanitize_text_field($_POST['lang']);

    if ($quantity > 0) {
      $cart = WC()->cart;
      foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        if ($cart_item['product_id'] == $product_id) {
          $cart->set_quantity($cart_item_key, $quantity);

          $cart_info = get_cart_info($lang);

          wp_send_json_success([
            'cart_products' => $cart_info['cart_products'],
            'count' => $cart_info['count'],
            'total' => $cart_info['total'], 
          ]);

          return;
        }
      }
    }
  }
  wp_send_json_error();
}

add_action('wp_ajax_update_cart_quantity', 'update_cart_quantity');
add_action('wp_ajax_nopriv_update_cart_quantity', 'update_cart_quantity');