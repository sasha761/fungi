<?php
/* Template Name: checkout page
 * Template Post Type: page
 */

$context = Timber::context();

if( is_wc_endpoint_url( 'order-received' ) ) {
	
	$order_id = absint( get_query_var( 'order-received' ) );
	$order = wc_get_order($order_id);
	$context['order'] = $order_id;
  $context['order_data'] = array(
    'order_items' => $order->get_items(),
    'order_id' => $order->get_id(),
    'order_number' => $order->get_order_number(),
    'order_date' => date('Y-m-d H:i:s', strtotime(get_post($order->get_id())->post_date)),
    'status' => $order->get_status(),
    'shipping_total' => $order->get_total_shipping(),
    'shipping_tax_total' => wc_format_decimal($order->get_shipping_tax(), 2),
    // 'fee_total' => wc_format_decimal($fee_total, 2),
    // 'fee_tax_total' => wc_format_decimal($fee_tax_total, 2),
    'tax_total' => wc_format_decimal($order->get_total_tax(), 2),
    'cart_discount' => (defined('WC_VERSION') && (WC_VERSION >= 2.3)) ? wc_format_decimal($order->get_total_discount(), 2) : wc_format_decimal($order->get_cart_discount(), 2),
    'order_discount' => (defined('WC_VERSION') && (WC_VERSION >= 2.3)) ? wc_format_decimal($order->get_total_discount(), 2) : wc_format_decimal($order->get_order_discount(), 2),
    'discount_total' => wc_format_decimal($order->get_total_discount(), 2),
    'order_total' => wc_format_decimal($order->get_total(), 2),
    'order_currency' => $order->get_currency(),
    'payment_method' => $order->get_payment_method(),
    'payment_method_title' => $order->get_payment_method_title(),
    'shipping_method' => $order->get_shipping_method(),
    'customer_id' => $order->get_user_id(),
    'customer_user' => $order->get_user_id(),
    'customer_email' => ($a = get_userdata($order->get_user_id() )) ? $a->user_email : '',
    'billing_first_name' => $order->get_billing_first_name(),
    'billing_last_name' => $order->get_billing_last_name(),
    'billing_company' => $order->get_billing_company(),
    'billing_email' => $order->get_billing_email(),
    'billing_phone' => $order->get_billing_phone(),
    'billing_address_1' => $order->get_billing_address_1(),
    'billing_address_2' => $order->get_billing_address_2(),
    'billing_postcode' => $order->get_billing_postcode(),
    'billing_city' => $order->get_billing_city(),
    'billing_state' => $order->get_billing_state(),
    'billing_country' => $order->get_billing_country(),
    'shipping_first_name' => $order->get_shipping_first_name(),
    'shipping_last_name' => $order->get_shipping_last_name(),
    'shipping_company' => $order->get_shipping_company(),
    'shipping_address_1' => $order->get_shipping_address_1(),
    'shipping_address_2' => $order->get_shipping_address_2(),
    'shipping_postcode' => $order->get_shipping_postcode(),
    'shipping_city' => $order->get_shipping_city(),
    'shipping_state' => $order->get_shipping_state(),
    'shipping_country' => $order->get_shipping_country(),
    'customer_note' => $order->get_customer_note(),
    'download_permissions' => $order->is_download_permitted() ? $order->is_download_permitted() : 0,
  );

  $context['products'] = [];
  foreach ( $order->get_items() as $item_id => $item ) {
    $products_array = [];
    $categories           = get_the_terms( $item->get_product_id(), 'product_cat' );
    foreach ($categories as $key => $category) {
      if ($category->parent) {
        if ($key == 0) $key = $key + 1;
        $products_array['cats'][$key] = $category->name;
      } else {
        $products_array['cats'][0] = $category->name;
      }
    }
    ksort($products_array['cats']);

    $products_array['id'] = $item->get_product_id();
    $products_array['variation_id'] = $item->get_variation_id();
    $products_array['name'] = $item->get_name();
    $products_array['brand'] = wc_get_product_terms( $item->get_product_id(), 'pa_brand', array()); 
    $products_array['qty'] = $item->get_quantity();
    $products_array['subtotal'] = $item->get_subtotal();
    $products_array['total'] = $item->get_total();

    $context['products'][] = $products_array;
  }
} else {
  $context['products'] = [];

	$context['checkout_url'] = wc_get_checkout_url();

	$context['checkout_billing'] = WC()->checkout()->get_checkout_fields( 'billing' );
	$context['checkout_shipping'] = WC()->checkout()->get_checkout_fields( 'shipping' );
	$context['checkout_account'] = WC()->checkout()->get_checkout_fields( 'account' );
	$context['checkout_order'] = WC()->checkout()->get_checkout_fields( 'order' );

	$context['fields'] = WC()->checkout()->get_checkout_fields( 'billing' );

	foreach ( $context['fields'] as $key => $field ) {
		$arr[] = WC()->checkout()->get_value( $key );
	}
	$context['checkout_key'] = $arr;



	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$products_array = [];

		// General var
		$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
		$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

		if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {

			// URL
	    $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
	    $products_array['url'] = get_permalink($product_id);

	    // Thumbnail
	    $thumbnail = get_the_post_thumbnail_url($product_id);

	    if (! $product_permalink) {
	        $products_array['thumbnail'] = $thumbnail;
	    } else {
	        $products_array['thumbnail'] = $thumbnail;
	    }

	    // Title
	    if (! $product_permalink) {
	      $products_array['title'] = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
	    } else {
	      $products_array['title'] = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
	    }

	    // Attributes
	    $brand = wc_get_product_terms( $product_id, 'pa_brand', array() ); 
	    $size = $_product->get_attribute('size'); 
	    $color = $_product->get_attribute('color'); 

	    $products_array['attr'] = [
	      'brand' => $brand,
	      'size' => $size,
	      'color' => $color,
	    ];

	    // Price
	    $products_array['price'] = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);

	    // Total
	    $products_array['total'] = apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
			$products_array['quantity'] = apply_filters( 'woocommerce_checkout_cart_item_quantity', $cart_item['quantity'], $cart_item, $cart_item_key ); 

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
}


$template = ['woo/checkout.twig'];
Timber::render($template, $context);