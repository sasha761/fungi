<?php
add_action( 'wp_ajax_contactForm', 'contactForm' );
add_action( 'wp_ajax_nopriv_contactForm', 'contactForm' );

function contactForm() {
  $name = !empty($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
  $email = !empty($_POST['email']) ? sanitize_email($_POST['email']) : '';
  $contactInfo = !empty($_POST['contactInfo']) ? sanitize_text_field($_POST['contactInfo']) : '';
  $text = !empty($_POST['text']) ? sanitize_text_field($_POST['text']) : '';
  $products = !empty($_POST['products']) ? $_POST['products'] : [];

  $user_mail_body = '';

  if (empty($name) && empty($email) && !is_email($email)) {
    wp_send_json_error(['message' => 'Пожалуйста, заполните обязательные поля или неправильный формат email']);
    wp_die();
  }

  $user_mail_body = [
      'Имя: ' . esc_html($name),
      'Email: ' . esc_html($email),
  ];

  if (!empty($contactInfo)) {
      $user_mail_body[] = 'contact info: ' . esc_html($contactInfo);
  }

  if (!empty($text)) {
      $user_mail_body[] = 'Текст: ' . esc_html($text);
  }

  if (!empty($products)) {
    foreach ($products as $product) {
      $user_mail_body[] = 'Продукт: ' . esc_html($product['name']) . ' (ID: ' . esc_html($product['id']) . ', Количество: ' . esc_html($product['quantity']) . ')';
    }
  }

  $user_mail_body = implode("\n", $user_mail_body);

  $subject = 'Contact form | order';
  $to_admin = get_option('admin_email');
  $headers = [
    'Content-Type: text/html; charset=UTF-8;',
    'From: ' . get_bloginfo('name') . ' <' . $to_admin . '>',
  ];

  // Отправка письма
  $mailResult = wp_mail($to_admin, $subject, $user_mail_body, $headers);

  if ($mailResult) {
    $order_result = create_order($name, $email, $contactInfo, $products);

    if ($order_result['success']) {

      WC()->cart->empty_cart();
      
      wp_send_json_success(['message' => 'Сообщение успешно отправлено!']);
    } else {
      wp_send_json_error(['message' => $order_result['message']]);
    }
  } else {
    wp_send_json_error(['message' => 'Ошибка при отправке сообщения. Попробуйте позже.']);
  }

  wp_die();
}


function create_order($name, $email, $contactInfo, $products) {
  if (empty($email) || empty($contactInfo) || empty($name) || empty($products)) {
    return ['success' => false, 'message' => 'Некорректные данные для создания заказа.'];
  }

  try {
    $order = wc_create_order();

    foreach ($products as $product) {
      $product_id = intval($product['id']);
      $quantity = intval($product['quantity']);
      
      $order->add_product(get_product($product_id), $quantity);
    }

    $billing_address = [
      'first_name' => $name,
      'last_name'  => '',
      'email'      => $email,
      'phone'      => '',
    ];

    $order->set_address($billing_address, 'billing');
    $order->set_address($billing_address, 'shipping');
    $order->set_payment_method_title('Оплата при доставке | быстрый заказ');
    $order->calculate_totals();
    $order->update_status('processing');

    $order_id = $order->get_id();
    telegram_notification($order_id);

    return [
      'success' => true,
      'message' => 'Заказ успешно создан.',
      'order_id' => $order_id,
      'thank_you_url' => $order->get_checkout_order_received_url(),
    ];
  } catch (Exception $e) {
    return ['success' => false, 'message' => 'Ошибка при создании заказа: ' . $e->getMessage()];
  }
}