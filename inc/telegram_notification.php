<?php
// function telegram_notification( $order_id ) {
//   $chatID_sasha = TG_ID_CHAT;
//   $chatID = TG_ID_CHAT_BOT;
//   $token = TG_TOKEN;

//   $order = wc_get_order( $order_id );

//   $messenger       = $order->get_meta('_messenger');
//   $messenger_value = $order->get_meta('_messenger_value');
    
//   $text  = "Новый заказ: № " . $order_id . "\n";
//   $text .= "ФИО: " . $order->get_billing_first_name() . " " . $order->get_billing_last_name() . "\n";
//   $text .= "Телефон: " . $order->get_billing_phone() . "\n";
//   $text .= "Город: " . $order->get_billing_city() . "\n";
//   $text .= "Новая почта №: " . $order->get_billing_address_1() . "\n";
//   $text .= "Дата: " . date('Y-m-d H:i:s', strtotime(get_post($order->get_id())->post_date)) . "\n";
//   $text .= "Сумма: " . wc_format_decimal($order->get_total(), 2) . "\n";
//   $text .= "Метод оплаты: " . $order->get_payment_method_title() . "\n";

//   if ($messenger) $text .= "Мессенджер: {$messenger}\n";
//   if ($messenger_value) $text .= "Контакт в мессенджере: {$messenger_value}\n";
  

//   $text .= "<b>Товар: </b> \n \n";
    
//   foreach ( $order->get_items() as $item_id => $item_data ) {
//     $product_id    = $item_data->get_product_id();
//     $product       = $item_data->get_product();
//     $product_name  = $product->get_name();
//     $item_quantity = $item_data->get_quantity();
//     $item_total    = $item_data->get_total();
//     $item_link     = get_the_permalink($product_id);
//     $text         .= $product_name . ' × ' . $item_quantity . ' = ' . $item_total . "\n" . $item_link . "\n";
//   }

//   file_get_contents("https://api.telegram.org/bot". $token ."/sendMessage?chat_id=". $chatID_sasha ."&parse_mode=html&text=" . urlencode($text));
//   // file_get_contents("https://api.telegram.org/bot". $token ."/sendMessage?chat_id=". $chatID ."&parse_mode=html&text=" . urlencode($text));
// }


function telegram_notification( $order_id ) {
  $order = wc_get_order( $order_id );
  if (!$order) return;
  
  $text = build_telegram_message( $order );
  send_telegram_message( $text, TG_ID_CHAT, TG_TOKEN );
}

function build_telegram_message( $order ) {
  $order_id = $order->get_id();
  $messenger = $order->get_meta('_messenger');
  $messenger_value = $order->get_meta('_messenger_value');
  $order_date = $order->get_date_created() 
    ? $order->get_date_created()->date_i18n('Y-m-d H:i:s') 
    : '';
  
  $lines = [];
  $lines[] = "Новый заказ № {$order_id}";
  $lines[] = "ФИО: {$order->get_billing_first_name()} {$order->get_billing_last_name()}";
  $lines[] = "Телефон: " . $order->get_billing_phone();
  $lines[] = "Дата: {$order_date}";
  $lines[] = "Сумма: " . wc_format_decimal($order->get_total(), 2);
  $lines[] = "Метод оплаты: " . $order->get_payment_method_title();

  if ($messenger) $lines[] = "Мессенджер: {$messenger}";
  if ($messenger_value) $lines[] = "Контакт в мессенджере: {$messenger_value}";
  
  $lines[] = "<b>Товар:</b>";
  foreach ($order->get_items() as $item_data) {
    $product = $item_data->get_product();
    if (!$product ) continue;
    
    $product_name = $product->get_name();
    $qty = $item_data->get_quantity();
    $total = $item_data->get_total();
    $link = get_the_permalink($product->get_id());
    $lines[] = "{$product_name} × {$qty} = {$total}";
    $lines[] = $link;
  }
  return implode("\n", $lines);
}

function send_telegram_message( $text, $chat_id, $token ) {
  $url = "https://api.telegram.org/bot{$token}/sendMessage";
  wp_remote_post($url, [
    'body' => [
      'chat_id'    => $chat_id,
      'parse_mode' => 'html',
      'text'       => $text
    ]
  ]);
}

add_action( 'woocommerce_checkout_update_order_meta', 'telegram_notification',  10, 1  );
