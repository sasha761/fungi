<?php
function update_cart_quantity() {
  // Если передаём язык (lang) - переключаем
  // if ( isset($_POST['lang']) && ! empty($_POST['lang']) ) {
  //   global $sitepress, $woocommerce_wpml;
  //   $lang = sanitize_text_field( $_POST['lang'] );
  //   // Принудительно переключаем язык
  //   $sitepress->switch_lang( sanitize_text_field($lang) );
  // }
  
  // var_dump($_POST['product_id']);
  if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $quantity   = intval($_POST['quantity']);

    if ($quantity > 0) {
      $cart = WC()->cart;
      foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        if ($cart_item['product_id'] == $product_id) {
          $cart->set_quantity($cart_item_key, $quantity);
          
          // $cart_totals = $cart->get_totals();
          // $data = [
          //   'cart_total'     => wc_price($cart_totals['total']),
          //   'subtotal'       => wc_price($cart_totals['subtotal']),
          //   'shipping_total' => wc_price($cart_totals['shipping_total']),
          // ];

          $cart_info = get_cart_info();

          // $template = ['templates/woo/mini-cart.twig'];
          // $mini_cart_html = Timber::compile($template, $cart_info);

          wp_send_json_success([
            'cart_products' => $cart_info['cart_products'],
            'count' => $cart_info['count'],
            'total' => $cart_info['total'], 
          ]);

          // wp_send_json_success(['data' => $data]);
          return;
        }
      }
    }
  }
  wp_send_json_error();
}

add_action('wp_ajax_update_cart_quantity', 'update_cart_quantity');
add_action('wp_ajax_nopriv_update_cart_quantity', 'update_cart_quantity');