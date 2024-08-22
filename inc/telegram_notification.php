<?php
function telegram_notification( $order_id ) {
  $chatID_sasha = TG_ID_CHAT;
  $chatID = TG_ID_CHAT_BOT;
  $token = TG_TOKEN;

  $order = wc_get_order( $order_id );
    
  $text  = "Новый заказ: № " . $order_id . "\n";
  $text .= "ФИО: " . $order->get_billing_first_name() . " " . $order->get_billing_last_name() . "\n";
  $text .= "Телефон: " . $order->get_billing_phone() . "\n";
  $text .= "Город: " . $order->get_billing_city() . "\n";
  $text .= "Новая почта №: " . $order->get_billing_address_1() . "\n";
  $text .= "Дата: " . date('Y-m-d H:i:s', strtotime(get_post($order->get_id())->post_date)) . "\n";
  $text .= "Сумма: " . wc_format_decimal($order->get_total(), 2) . "\n";
  $text .= "Метод оплаты: " . $order->get_payment_method_title() . "\n";
  $text .= "<b>Товар: </b> \n \n";
    
  foreach ( $order->get_items() as $item_id => $item_data ) {
    $product_id    = $item_data->get_product_id();
    $product       = $item_data->get_product();
    $product_name  = $product->get_name();
    $item_quantity = $item_data->get_quantity();
    $item_total    = $item_data->get_total();
    $item_link     = get_the_permalink($product_id);
    $text         .= $product_name . ' × ' . $item_quantity . ' = ' . $item_total . "\n" . $item_link . "\n";
  }

  file_get_contents("https://api.telegram.org/bot". $token ."/sendMessage?chat_id=". $chatID_sasha ."&parse_mode=html&text=" . urlencode($text));
  // file_get_contents("https://api.telegram.org/bot". $token ."/sendMessage?chat_id=". $chatID ."&parse_mode=html&text=" . urlencode($text));
}

add_action( 'woocommerce_checkout_update_order_meta', 'telegram_notification',  10, 1  );
