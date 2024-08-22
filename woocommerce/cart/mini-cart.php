<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.3.0
 */
$context = Timber::context();
$context['cart_products'] = [];

	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$products_array = [];

		// General vars
		$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
		$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

		if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {

			// price, title, image, url
			$products_array['title']     = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
      $products_array['price']     = $_product->get_price_html();
      $products_array['url']       = apply_filters( 'woocommerce_cart_item_permalink', $_product->get_permalink( $cart_item ) , $cart_item, $cart_item_key );
			$products_array['thumbnail'] = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );


			// Delete button
      $products_array['delete_permalink'] = wc_get_cart_remove_url( $cart_item_key );
      $products_array['delete_productid'] = esc_attr($product_id);
      $products_array['delete_sku'] = esc_attr($_product->get_sku());
      $products_array['cart_item_key'] = $cart_item_key;

      $products_array['quantity'] = $cart_item['quantity'];

      // Total
      // $products_array['total'] = apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);

      // Merge with products
      $context['cart_products'][] = $products_array;

		}
	}

$context['total'] = WC()->cart->get_total();

$template = ['templates/woo/mini-cart.twig'];
Timber::render($template, $context);
