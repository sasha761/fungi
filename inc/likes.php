<?php
function get_IP() {
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
    $ip = $_SERVER['REMOTE_ADDR'];
  }

  return $ip;
}

function ipToLong($ip) {
  if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    $packedIP = inet_pton($ip);
    $unpackedIP = unpack('N', $packedIP);
    return $unpackedIP[1];
  } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
    $packedIP = inet_pton($ip);
    $unpackedIP = unpack('J2', $packedIP);
    return bcadd(bcmul($unpackedIP[1], bcpow('2', '64')), $unpackedIP[2]);
  } else {
    return false;
  }
}

function get_posts_translations($post_id) {
  global $wpdb;
  return $wpdb->get_col($wpdb->prepare(
    "SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE trid = (SELECT trid FROM {$wpdb->prefix}icl_translations WHERE element_id = %d)",
    $post_id
  ));
}

function get_likes($user_id, $translations) {
  global $wpdb;
  return $wpdb->get_row($wpdb->prepare("SELECT id, like_type FROM {$wpdb->prefix}post_likes WHERE post_id IN (" . implode(',', array_map('intval', $translations)) . ") AND user_id = %s", $user_id));
}

function delete_like($existing_like) {
  global $wpdb;
  return $wpdb->delete("{$wpdb->prefix}post_likes", array('id' => $existing_like->id));
}

function update_like($like_type, $existing_like) {
  global $wpdb;
  return $wpdb->update("{$wpdb->prefix}post_likes", array('like_type' => $like_type), array('id' => $existing_like->id));
}

function add_like($post_id, $user_id, $like_type) {
  global $wpdb;
  return $wpdb->insert("{$wpdb->prefix}post_likes", array('post_id' => $post_id, 'user_id' => $user_id, 'like_type' => $like_type));
}

function handle_like() {
  if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'likes_nonce')) wp_send_json_error('Invalid nonce');

  $post_id = intval($_POST['post_id']);
  $like_type = sanitize_text_field($_POST['like_type']);

  $ip = get_IP();

  $user_id = ipToLong($ip);
  if ($user_id === false) wp_send_json_error('Invalid IP');

  // Получение всех связанных постов
  $translations = get_posts_translations($post_id);
  if (empty($translations)) wp_send_json_error('No translations found');

  // Проверка существующего лайка пользователя
  $existing_like = get_likes($user_id, $translations);
  // if (empty($existing_like)) wp_send_json_error('No likes found');

  if ($existing_like) {
    if ($existing_like->like_type === $like_type) {
      // delete like
      $response = delete_like($existing_like);
      if ($response === false) wp_send_json_error('Database insert failed');
      wp_send_json_success(['new_likes' => 'removed']);
    } else {
      // update like
      $response = update_like($like_type, $existing_like);
      if ($response === false) wp_send_json_error('Database insert failed');
      wp_send_json_success(['new_likes' => 'updated']);
    }
  } else {
    // add like
    $response = add_like($post_id, $user_id, $like_type);
    if ($response === false) wp_send_json_error('Database insert failed');
    wp_send_json_success(['new_likes' => 'inserted']);
  }

  wp_send_json_error('Unexpected error');
}
add_action('wp_ajax_handle_like', 'handle_like');
add_action('wp_ajax_nopriv_handle_like', 'handle_like');

function create_likes_table() {
  global $wpdb;

  $table_name = $wpdb->prefix . 'post_likes';
  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE $table_name (
    id int(11) NOT NULL AUTO_INCREMENT,
    post_id int(11) NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    like_type varchar(20) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY user_post_like (post_id, user_id, like_type)
  ) $charset_collate;";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);

  // Обновление опции для указания, что таблица создана
  add_option('likes_table_created', true);
}

function check_and_create_likes_table() {
  if (get_option('likes_table_created') === false) {
      create_likes_table();
  }
}

add_action('init', 'check_and_create_likes_table');