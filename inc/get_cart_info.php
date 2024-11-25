<?php
function get_cart_info(): array {
  if (!WC()->cart) {
    return [
      'cart_products' => [],
      'count' => 0,
      'total' => wc_price(0),
    ];
  }

  $data = [
    'cart_products' => [],
    'count' => WC()->cart->cart_contents_count,
    'total' => WC()->cart->get_total(),
  ];

  foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
    $_product = $cart_item['data'] ?? null;
    if (!$_product || !$cart_item['quantity'] || !$_product->exists()) continue;

    $data['cart_products'][] = [
        'id' => $cart_item['product_id'],
        'title' => $_product->get_name(),
        'price' => $_product->get_price_html(),
        'url' => $_product->get_permalink(),
        'thumbnail' => $_product->get_image(),
        'delete_permalink' => wc_get_cart_remove_url($cart_item_key),
        'delete_productid' => $cart_item['product_id'],
        'delete_sku' => $_product->get_sku(),
        'cart_item_key' => $cart_item_key,
        'quantity' => $cart_item['quantity'],
    ];
  }

  return $data;
}
