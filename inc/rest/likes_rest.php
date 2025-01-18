<?php
add_action('rest_api_init', callback: function () {
  register_rest_route('custom/v1', '/handle_like', [
      'methods'             => 'POST',
      'callback'            => 'handle_like',
      'permission_callback' => '__return_true', // при необходимости замените на свою проверку
  ]);
});
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

function handle_like( WP_REST_Request $request ) {
  $post_id   = intval( $request->get_param('post_id') );
  $like_type = sanitize_text_field( $request->get_param('like_type') );

  $ip = get_IP();
  $user_id = ipToLong($ip);
  if ($user_id === false) {
      return new WP_Error(
          'invalid_ip',
          'Invalid IP',
          ['status' => 400]
      );
  }

  // Получаем связанные посты (переводы)
  $translations = get_posts_translations($post_id);
  if ( empty($translations) ) {
      return new WP_Error(
          'no_translations',
          'No translations found',
          ['status' => 404]
      );
  }

  // Проверка, лайкал ли уже (like / dislike и т.д.)
  $existing_like = get_likes($user_id, $translations);

  if ($existing_like) {
      // Лайк уже есть
      if ($existing_like->like_type === $like_type) {
          // Удаляем лайк
          $response = delete_like($existing_like);
          if ($response === false) {
              return new WP_Error(
                  'db_delete_failed',
                  'Database delete failed',
                  ['status' => 500]
              );
          }

          // Возвращаем успех
          return [
              'success'   => true,
              'new_likes' => 'removed',
          ];
      } else {
          // Обновляем тип лайка
          $response = update_like($like_type, $existing_like);
          if ($response === false) {
              return new WP_Error(
                  'db_update_failed',
                  'Database update failed',
                  ['status' => 500]
              );
          }

          return [
              'success'   => true,
              'new_likes' => 'updated',
          ];
      }
  } else {
      // Добавляем новый лайк
      $response = add_like($post_id, $user_id, $like_type);
      if ($response === false) {
          return new WP_Error(
              'db_insert_failed',
              'Database insert failed',
              ['status' => 500]
          );
      }

      return [
          'success'   => true,
          'new_likes' => 'inserted',
      ];
  }
}


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