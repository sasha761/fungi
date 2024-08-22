<?php
function remove_from_cart_handler() {
  if (!isset($_POST['cart_item_key'])) {
    wp_send_json_error('Invalid cart item key');
    return;
  }

  $cart_item_key = sanitize_text_field($_POST['cart_item_key']);

  if (WC()->cart->get_cart_item($cart_item_key)) {
    WC()->cart->remove_cart_item($cart_item_key);

    $context['cart_products'] = [];

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
      // $products_array = [];
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

        $context['cart_products'][] = $products_array;
      }
    }

    $context['total'] = WC()->cart->get_total();

    $mini_cart_count = WC()->cart->get_cart_contents_count();

    wp_send_json_success([
      'html' => $context,
      'count' => $mini_cart_count,
    ]);
      // wp_send_json_success('Product removed from cart');
  } else {
    wp_send_json_error('Failed to remove product from cart');
  }
}

add_action('wp_ajax_remove_from_cart', 'remove_from_cart_handler');
add_action('wp_ajax_nopriv_remove_from_cart', 'remove_from_cart_handler');