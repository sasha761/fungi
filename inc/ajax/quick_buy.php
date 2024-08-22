<?php
function ajax_quickBuy(){
  $context = Timber::context();

  $email      = $_POST['email'];
  $name       = $_POST['name'];
  $surname    = $_POST['surname'];
  $phone      = $_POST['phone'];
  $city       = $_POST['city'];
  $address    = $_POST['address'];
  $product_id = $_POST['variation_id'] != '0' ? $_POST['variation_id'] : $_POST['product_id'];

  $args = array(
    'status'        => null,
    'customer_id'   => null,
    'customer_note' => null,
    'parent'        => null,
    'created_via'   => null,
    'cart_hash'     => null,
    'order_id'      => 0,
  );

  $order = wc_create_order( $args );
  $order->add_product(get_product($product_id), 1);

  $billing_address = array(
    'first_name' => $name,
    'last_name'  => $surname,
    'email'      => $email,
    'phone'      => $phone,
    'city'       => $city,
    'address_1'  => $address,
  );
  $address = array(
    'first_name' => $name,
    'last_name'  => $surname,
    'email'      => $email,
    'phone'      => $phone,
    'city'       => $city,
    'address_1'  => $address,
  );

  $order->set_address($billing_address, 'billing');
  $order->set_address($address, 'shipping');
  $order->set_payment_method_title('Оплата при доставке | быстрый заказ');
  $order->calculate_totals();
  $order->update_status('on-hold');

  $order->save();

  $order_id = $order->get_id();
  $thank_you_url = $order->get_checkout_order_received_url();

  header('Content-Type: application/json');

  if (!empty($order_id)) {
    $response = $thank_you_url;
  } else {
    $response = false;
  }
  
  echo (json_encode($response));
  telegram_notification($order_id);
  wp_die();
}

add_action( 'wp_ajax_quickBuy', 'ajax_quickBuy' );
add_action( 'wp_ajax_nopriv_quickBuy', 'ajax_quickBuy' );