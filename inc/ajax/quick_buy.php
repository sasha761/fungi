<?php
add_action( 'wp_ajax_contactForm', 'contactForm' );
add_action( 'wp_ajax_nopriv_contactForm', 'contactForm' );

add_action( 'wp_ajax_callbackForm', 'callbackForm' );
add_action( 'wp_ajax_nopriv_callbackForm', 'callbackForm' );

function callbackForm() {
  $phone = !empty($_POST['full_phone']) ? esc_html($_POST['full_phone']) : esc_html($_POST['phone']);

  if (empty($phone)) {
    wp_send_json_error(['message' => 'Пожалуйста, заполните обязательные поля или неправильный формат email']);
    wp_die();
  }

  $lines = [];
  $lines[] = 'Форма обратного звонка.';
  $lines[] = 'Телефон: ' . $phone;

  $user_mail_body = implode("\n", $lines);
  // $user_mail_body = 'Телефон: ' . $phone;


  $subject = 'call back form';
  $to_admin = get_option('admin_email');
  $headers = [
    'Content-Type: text/html; charset=UTF-8;',
    'From: ' . get_bloginfo('name') . ' <' . $to_admin . '>',
  ];

  // Отправка письма
  $mailResult = wp_mail($to_admin, $subject, $user_mail_body, $headers);
  send_telegram_message( $user_mail_body, TG_ID_CHAT, TG_TOKEN );

  if ($mailResult) {
      wp_send_json_success(['message' => 'Сообщение успешно отправлено!']);
  } else {
      wp_send_json_error(['message' => 'Ошибка при отправке сообщения. Попробуйте позже.']);
  }

  wp_die();
}

function contactForm() {
  $name = !empty($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
  $email = !empty($_POST['email']) ? sanitize_email($_POST['email']) : '';
  $phone = !empty($_POST['full_phone']) ? esc_html($_POST['full_phone']) : esc_html($_POST['phone']);
  // $phone = !empty($_POST['phone']) ? esc_html($_POST['phone']) : '';
  $messenger = !empty($_POST['contacts_client_messenger']) ? sanitize_text_field($_POST['contacts_client_messenger']) : '';
  $messenger_value = !empty($_POST['contactInfo']) ? sanitize_text_field($_POST['contactInfo']) : '';
  $text = !empty($_POST['text']) ? sanitize_text_field($_POST['text']) : '';
  $products = !empty($_POST['products']) ? $_POST['products'] : [];

  $allowed_mime_types = [
    'application/msword', 
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // doc, docx
    'text/xml', // xml
    'image/jpeg', 'image/png', 'image/webp', 'image/heic', // jpg, jpeg, png, webp, heic
    'video/quicktime', 'video/mp4' // видео
  ];
  $max_file_size = 10 * 1024 * 1024;

  $user_mail_body = '';


  if (empty($name) && empty($email) && !is_email($email)) {
    wp_send_json_error(['message' => 'Пожалуйста, заполните обязательные поля или неправильный формат email']);
    wp_die();
  }

  $user_mail_body = [
      'Имя: ' . esc_html($name),
      'Email: ' . esc_html($email),
  ];

  if (!empty($messenger_value)) {
      $user_mail_body[] = esc_html($messenger) . ': ' . esc_html($messenger_value);
  }

  if (!empty($text)) {
      $user_mail_body[] = 'Текст: ' . esc_html($text);
  }

  if (!empty($phone)) {
      $user_mail_body[] = 'Телефон: ' . esc_html($phone);
  }

  if (!empty($products)) {
    foreach ($products as $product) {
      $user_mail_body[] = 'Продукт: ' . esc_html($product['name']) . ' (ID: ' . esc_html($product['id']) . ', Количество: ' . esc_html($product['quantity']) . ')';
    }
  }

  $user_mail_body = implode("\n", $user_mail_body);

  $subject = !empty($products) ? 'Contact form | order' : 'Contact form';
  $to_admin = get_option('admin_email');
  $headers = [
    'Content-Type: text/html; charset=UTF-8;',
    'From: ' . get_bloginfo('name') . ' <' . $to_admin . '>',
  ];

  $attachments = [];
  // var_dump($_FILES['files']);
  if (!empty($_FILES['files'])) {
    foreach ($_FILES['files']['name'] as $key => $filename) {
      if ($_FILES['files']['error'][$key] !== UPLOAD_ERR_OK) {
        continue; // Пропускаем файлы с ошибками загрузки
      }

      $file_tmp = $_FILES['files']['tmp_name'][$key];
      $file_size = $_FILES['files']['size'][$key];
      $file_type = $_FILES['files']['type'][$key];

      // Проверка размера файла
      if ($file_size > $max_file_size) {
          wp_send_json_error(['message' => 'Один или несколько файлов превышают 10MB.']);
          wp_die();
      }

      // Проверка типа файла
      if (!in_array($file_type, $allowed_mime_types)) {
          wp_send_json_error(['message' => 'Один или несколько файлов имеют недопустимый формат.']);
          wp_die();
      }

      // Сохранение файла во временную папку
      $upload_dir = wp_upload_dir();
      $upload_path = $upload_dir['path'] . '/' . basename($filename);

      if (move_uploaded_file($file_tmp, $upload_path)) {
          $attachments[] = $upload_path;
      }
    }
  }

  // Отправка письма
  // $mailResult = wp_mail($to_admin, $subject, $user_mail_body, $headers);
  $mailResult = wp_mail($to_admin, $subject, $user_mail_body, $headers, $attachments);

  // Очистка загруженных файлов
  foreach ($attachments as $file) {
    @unlink($file);
  }

  if ($mailResult && !empty($products)) {
    $order_result = create_order($name, $email, $phone, $messenger, $messenger_value, $products);

    if ($order_result['success']) {
        WC()->cart->empty_cart();
        wp_send_json_success(['message' => 'Сообщение успешно отправлено!']);
    } else {
        wp_send_json_error(['message' => $order_result['message']]);
    }
  } elseif ($mailResult) {
      wp_send_json_success(['message' => 'Сообщение успешно отправлено!']);
  } else {
      wp_send_json_error(['message' => 'Ошибка при отправке сообщения. Попробуйте позже.']);
  }

  wp_die();
}

function create_order($name, $email, $phone, $messenger, $messenger_value, $products) {
  if (empty($email) || empty($name) || empty($products)) {
    return ['success' => false, 'message' => 'Некорректные данные для создания заказа.'];
  }

  try {
    $order = wc_create_order();

    foreach ($products as $product) {
      $product_id = intval($product['id']);
      $quantity = intval($product['quantity']);
      
      $order->add_product(wc_get_product($product_id), $quantity);
    }

    $billing_address = [
      'first_name' => $name,
      'last_name'  => '',
      'email'      => $email,
      'phone'      => $phone,
    ];

    $order->set_address($billing_address, 'billing');
    $order->set_address($billing_address, 'shipping');
    $order->set_payment_method_title('Оплата при доставке | быстрый заказ');
    $order->calculate_totals();

    if (!empty($messenger)) {
      $order->update_meta_data('_messenger', $messenger);
    }

    if (!empty($messenger_value)) {
      $order->update_meta_data('_messenger_value', $messenger_value);
    } 

    $order->update_status('processing');
    $order->save();

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