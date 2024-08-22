<?php
function update_cart_quantity() {
  if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity > 0) {
      $cart = WC()->cart;
      foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        if ($cart_item['product_id'] == $product_id) {
          $cart->set_quantity($cart_item_key, $quantity);
          
          // Получаем обновленные данные корзины
          $cart_totals = WC()->cart->get_totals();
          $data = array(
            'cart_total' => wc_price($cart_totals['total']),
            'subtotal' => wc_price($cart_totals['subtotal']),
            'shipping_total' => wc_price($cart_totals['shipping_total'])
          );

          wp_send_json_success(array('data' => $data));
          return;
        }
      }
    }
  }
  wp_send_json_error();
}

add_action('wp_ajax_update_cart_quantity', 'update_cart_quantity');
add_action('wp_ajax_nopriv_update_cart_quantity', 'update_cart_quantity');