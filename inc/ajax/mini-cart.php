<?php
function update_mini_cart_handler() {
  // Собираем данные корзины
  $context = Timber::context();
  $cart_info = get_cart_info();

  $template = ['templates/woo/mini-cart.twig'];
  $mini_cart_html = Timber::compile($template, $cart_info);

  wp_send_json_success([
    'cart_products' => $mini_cart_html,
    'count' => $cart_info['count'],
    'total' => $cart_info['total'], 
  ]);
  // wp_send_json_success([
  //   'html' => $mini_cart_html,
  //   'count' => $mini_cart_count,
  //   'total' => $cart_info['total'], 
  // ]);
}

add_action('wp_ajax_update_mini_cart', 'update_mini_cart_handler');
add_action('wp_ajax_nopriv_update_mini_cart', 'update_mini_cart_handler');