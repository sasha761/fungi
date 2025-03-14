<?php
function get_cart_info($lang = 'en'): array {
  
  if (!WC()->cart) {
    return [
      'cart_products' => [],
      'count' => 0,
      'total' => wc_price(0),
    ];
  }

  $currency = ($lang === 'ru' || $lang === 'uk') ? 'UAH' : get_option('woocommerce_currency');
  $cart_total = floatval(WC()->cart->get_total('raw')); 
  $converted_total = apply_filters('wcml_raw_price_amount', $cart_total, $currency);

  $data = [
    'cart_products' => [],
    'count' => WC()->cart->cart_contents_count,
    'total' => wc_price($converted_total, ['currency' => $currency]),
  ];

  foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
    $_product = $cart_item['data'] ?? null;
    if (!$_product || !$cart_item['quantity'] || !$_product->exists()) continue;

    $price = apply_filters('wcml_raw_price_amount', $_product->get_price(), $currency);
    $price_html = wc_price($price, ['currency' => $currency]);

    $data['cart_products'][] = [
        'id' => $cart_item['product_id'],
        'title' => $_product->get_name(),
        'price' => $price_html,
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
