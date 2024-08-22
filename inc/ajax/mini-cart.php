<?php
function update_mini_cart_handler() {
  // Собираем данные корзины
  $context = Timber::context();
  $context['cart_products'] = [];

  foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
    $products_array = [];

    $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

    if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key)) {
      $products_array['title'] = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
      $products_array['price'] = $_product->get_price_html();
      $products_array['url'] = apply_filters('woocommerce_cart_item_permalink', $_product->get_permalink($cart_item), $cart_item, $cart_item_key);
      $products_array['thumbnail'] = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
      $products_array['delete_permalink'] = wc_get_cart_remove_url($cart_item_key);
      $products_array['delete_productid'] = esc_attr($product_id);
      $products_array['delete_sku'] = esc_attr($_product->get_sku());
      $products_array['cart_item_key'] = $cart_item_key;

      $products_array['quantity'] = $cart_item['quantity'];
      // $products_array['total'] = apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);

      $context['cart_products'][] = $products_array;
    }
  }

  $context['total'] = WC()->cart->get_total();

  $template = ['templates/woo/mini-cart.twig'];
  $mini_cart_html = Timber::compile($template, $context);
  $mini_cart_count = WC()->cart->get_cart_contents_count();

  wp_send_json_success([
    'html' => $mini_cart_html,
    'count' => $mini_cart_count,
  ]);
}

add_action('wp_ajax_update_mini_cart', 'update_mini_cart_handler');
add_action('wp_ajax_nopriv_update_mini_cart', 'update_mini_cart_handler');