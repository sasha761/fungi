<?php
/* Template Name: cart page
 * Template Post Type: page
 */

$context = Timber::context();
$context['post'] = Timber::get_post();

$context['products'] = [];

foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
  $products_array = [];

  // General vars
  $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
  $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

  if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {

    // price, title, image, url
    $products_array['title']       = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
    $products_array['description'] = $_product->get_short_description() ? $_product->get_short_description() : $_product->get_description();
    $products_array['price']       = $_product->get_price_html();
    $products_array['url']         = apply_filters('woocommerce_cart_item_permalink', $_product->get_permalink($cart_item), $cart_item, $cart_item_key);
    $products_array['thumbnail']   = get_the_post_thumbnail_url($product_id);

    // Delete button
    $products_array['delete_permalink'] = wc_get_cart_remove_url( $cart_item_key );
    $products_array['delete_productid'] = esc_attr($product_id);
    $products_array['delete_sku']       = esc_attr($_product->get_sku());

    // Quantity
    $products_array['quantity'] = $cart_item['quantity'];

    // Total
    $products_array['total'] = apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);

    $context['products'][] = $products_array;
  }
}
foreach ( WC()->cart->get_coupons() as $code => $coupon ) {
	$arr_cupon[] = $coupon;
}

if (!empty($arr_cupon)) {
    $context['coupons'] = $arr_cupon;
}


$context['total']    = WC()->cart->get_total();
$context['cart_total']    = WC()->cart->get_cart_subtotal();
$context['cart_discount']    = WC()->cart->get_total_discount();

$context['shipping']    = WC()->cart->get_cart_shipping_total();
$context['shipping_packages']    = WC()->cart->get_shipping_packages();

$context['nonce'] = wp_nonce_field('woocommerce-cart');
$context['action'] = esc_url(wc_get_cart_url());



$template = ['woo/cart-page.twig'];
Timber::render($template, $context);