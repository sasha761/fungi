<?php

add_action('rest_api_init', function () {
  register_rest_route('custom/v1', '/handle_like', [
    'methods'             => WP_REST_Server::CREATABLE,
    'callback'            => 'handle_like',
    'permission_callback' => 'can_handle_like',
    'args'                => [
      'post_id'   => [
        'type'              => 'integer',
        'required'          => true,
        'sanitize_callback' => 'absint',
        'validate_callback' => static function ($value) {
          $post_id = absint($value);

          return $post_id > 0
            && get_post_type($post_id) === 'post'
            && get_post_status($post_id) === 'publish';
        },
      ],
      'like_type' => [
        'type'              => 'string',
        'required'          => true,
        'sanitize_callback' => 'sanitize_key',
        'validate_callback' => static function ($value) {
          return array_key_exists((string) $value, get_like_types());
        },
      ],
    ],
  ]);
});

function get_like_types() {
  return [
    'like'     => true,
    'fire'     => true,
    'heart'    => true,
    'swearing' => true,
    'clown'    => true,
  ];
}

function get_default_like_counts() {
  return array_fill_keys(array_keys(get_like_types()), 0);
}

function get_likes_table_name() {
  global $wpdb;

  return $wpdb->prefix . 'post_likes';
}

function can_handle_like(WP_REST_Request $request) {
  $nonce = $request->get_header('X-WP-Nonce');

  if (! is_string($nonce) || ! wp_verify_nonce($nonce, 'wp_rest')) {
    return new WP_Error(
      'invalid_nonce',
      __('Reaction request could not be verified.', 'news'),
      ['status' => 403]
    );
  }

  return true;
}

function get_client_ip_address() {
  $server_keys = [
    'HTTP_CLIENT_IP',
    'HTTP_X_FORWARDED_FOR',
    'REMOTE_ADDR',
  ];

  foreach ($server_keys as $server_key) {
    if (empty($_SERVER[$server_key])) {
      continue;
    }

    $value      = sanitize_text_field(wp_unslash((string) $_SERVER[$server_key]));
    $candidates = explode(',', $value);

    foreach ($candidates as $candidate) {
      $candidate = trim($candidate);

      if (filter_var($candidate, FILTER_VALIDATE_IP)) {
        return $candidate;
      }
    }
  }

  return '';
}

function get_current_like_user_key() {
  $user_id = get_current_user_id();

  if ($user_id > 0) {
    return 'user:' . $user_id;
  }

  $ip_address = get_client_ip_address();

  if ($ip_address === '') {
    return '';
  }

  $user_agent = isset($_SERVER['HTTP_USER_AGENT'])
    ? sanitize_text_field(wp_unslash((string) $_SERVER['HTTP_USER_AGENT']))
    : '';

  return 'anon:' . hash('sha256', $ip_address . '|' . $user_agent . '|' . wp_salt('auth'));
}

function get_legacy_like_user_id() {
  $ip_address = get_client_ip_address();

  if (! filter_var($ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    return 0;
  }

  return (int) sprintf('%u', ip2long($ip_address));
}

function get_like_storage_user_id($user_key) {
  return (int) sprintf('%u', crc32($user_key));
}

function get_posts_translations($post_id) {
  global $wpdb;

  $table_name = $wpdb->prefix . 'icl_translations';
  $table_exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table_name));

  if ($table_exists !== $table_name) {
    return [$post_id];
  }

  $translations = $wpdb->get_col($wpdb->prepare(
    "SELECT element_id FROM {$table_name} WHERE trid = (SELECT trid FROM {$table_name} WHERE element_id = %d LIMIT 1)",
    $post_id
  ));

  if (empty($translations)) {
    return [$post_id];
  }

  return array_map('absint', $translations);
}

function get_likes($user_key, $translations) {
  global $wpdb;

  $translations = array_filter(array_map('absint', (array) $translations));

  if (empty($translations) || $user_key === '') {
    return null;
  }

  $table_name   = get_likes_table_name();
  $placeholders = implode(',', array_fill(0, count($translations), '%d'));
  $params       = array_merge($translations, [$user_key]);
  $where_user   = 'user_key = %s';

  $legacy_user_id = get_legacy_like_user_id();

  if ($legacy_user_id > 0) {
    $where_user .= ' OR user_key = %s OR (user_key = "" AND user_id = %d)';
    $params[] = 'legacy:' . $legacy_user_id;
    $params[] = $legacy_user_id;
  }

  return $wpdb->get_row($wpdb->prepare(
    "SELECT id, like_type
    FROM {$table_name}
    WHERE post_id IN ({$placeholders}) AND ({$where_user})
    LIMIT 1",
    ...$params
  ));
}

function delete_like($existing_like) {
  global $wpdb;

  return $wpdb->delete(get_likes_table_name(), ['id' => (int) $existing_like->id], ['%d']);
}

function update_like($like_type, $existing_like, $user_key) {
  global $wpdb;

  return $wpdb->update(
    get_likes_table_name(),
    [
      'user_id'    => get_like_storage_user_id($user_key),
      'user_key'   => $user_key,
      'like_type'  => $like_type,
      'updated_at' => current_time('mysql'),
    ],
    ['id' => (int) $existing_like->id],
    ['%d', '%s', '%s', '%s'],
    ['%d']
  );
}

function add_like($post_id, $user_key, $like_type) {
  global $wpdb;

  $now = current_time('mysql');

  return $wpdb->insert(
    get_likes_table_name(),
    [
      'post_id'    => $post_id,
      'user_id'    => get_like_storage_user_id($user_key),
      'user_key'   => $user_key,
      'like_type'  => $like_type,
      'created_at' => $now,
      'updated_at' => $now,
    ],
    ['%d', '%d', '%s', '%s', '%s', '%s']
  );
}

function get_likes_counts_for_post($post_id) {
  global $wpdb;

  $translations = get_posts_translations($post_id);
  $translations = array_filter(array_map('absint', (array) $translations));
  $likes_data   = get_default_like_counts();

  if (empty($translations)) {
    return $likes_data;
  }

  $placeholders = implode(',', array_fill(0, count($translations), '%d'));
  $query = $wpdb->prepare(
    "SELECT like_type, COUNT(*) as count
    FROM " . get_likes_table_name() . "
    WHERE post_id IN ({$placeholders})
    GROUP BY like_type",
    ...$translations
  );

  $likes = $wpdb->get_results($query);

  foreach ($likes as $like) {
    $like_type = (string) $like->like_type;

    if (array_key_exists($like_type, $likes_data)) {
      $likes_data[$like_type] = (int) $like->count;
    }
  }

  return $likes_data;
}

function get_current_user_like_for_post($post_id) {
  $user_key = get_current_like_user_key();

  if ($user_key === '') {
    return null;
  }

  $existing_like = get_likes($user_key, get_posts_translations($post_id));

  if (! $existing_like || ! array_key_exists((string) $existing_like->like_type, get_like_types())) {
    return null;
  }

  return (string) $existing_like->like_type;
}

function handle_like(WP_REST_Request $request) {
  $post_id   = absint($request->get_param('post_id'));
  $like_type = sanitize_key((string) $request->get_param('like_type'));
  $user_key  = get_current_like_user_key();

  if ($user_key === '') {
    return new WP_Error(
      'missing_user_key',
      __('Could not identify the reaction author.', 'news'),
      ['status' => 400]
    );
  }

  if (! array_key_exists($like_type, get_like_types())) {
    return new WP_Error(
      'invalid_like_type',
      __('Invalid reaction type.', 'news'),
      ['status' => 400]
    );
  }

  $translations  = get_posts_translations($post_id);
  $existing_like = get_likes($user_key, $translations);
  $action        = 'inserted';

  if ($existing_like) {
    if ($existing_like->like_type === $like_type) {
      $response = delete_like($existing_like);

      if ($response === false) {
        return new WP_Error('db_delete_failed', __('Reaction could not be saved.', 'news'), ['status' => 500]);
      }

      $action = 'removed';
    } else {
      $response = update_like($like_type, $existing_like, $user_key);

      if ($response === false) {
        return new WP_Error('db_update_failed', __('Reaction could not be saved.', 'news'), ['status' => 500]);
      }

      $action = 'updated';
    }
  } else {
    $response = add_like($post_id, $user_key, $like_type);

    if ($response === false) {
      return new WP_Error('db_insert_failed', __('Reaction could not be saved.', 'news'), ['status' => 500]);
    }
  }

  return rest_ensure_response([
    'success'           => true,
    'action'            => $action,
    'new_likes'         => $action,
    'selected_reaction' => get_current_user_like_for_post($post_id),
    'counts'            => get_likes_counts_for_post($post_id),
  ]);
}

function create_likes_table() {
  global $wpdb;

  $table_name      = get_likes_table_name();
  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE {$table_name} (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    post_id bigint(20) unsigned NOT NULL,
    user_id bigint(20) unsigned NOT NULL DEFAULT 0,
    user_key varchar(80) NOT NULL DEFAULT '',
    like_type varchar(20) NOT NULL,
    created_at datetime NULL DEFAULT NULL,
    updated_at datetime NULL DEFAULT NULL,
    PRIMARY KEY  (id),
    UNIQUE KEY user_post (post_id,user_key),
    KEY post_reaction (post_id,like_type)
  ) {$charset_collate};";

  require_once ABSPATH . 'wp-admin/includes/upgrade.php';
  dbDelta($sql);

  migrate_likes_table();
  update_option('likes_table_created', true);
  update_option('news_post_likes_db_version', '2.0.0');
}

function maybe_add_likes_column($column_name, $definition, $after_column) {
  global $wpdb;

  $table_name = get_likes_table_name();
  $column = $wpdb->get_var($wpdb->prepare("SHOW COLUMNS FROM {$table_name} LIKE %s", $column_name));

  if ($column === $column_name) {
    return;
  }

  $wpdb->query("ALTER TABLE {$table_name} ADD {$column_name} {$definition} AFTER {$after_column}");
}

function maybe_drop_likes_index($index_name) {
  global $wpdb;

  $table_name = get_likes_table_name();
  $index = $wpdb->get_var($wpdb->prepare("SHOW INDEX FROM {$table_name} WHERE Key_name = %s", $index_name));

  if ($index === $index_name) {
    $wpdb->query("ALTER TABLE {$table_name} DROP INDEX {$index_name}");
  }
}

function maybe_add_likes_index($index_name, $definition) {
  global $wpdb;

  $table_name = get_likes_table_name();
  $index = $wpdb->get_var($wpdb->prepare("SHOW INDEX FROM {$table_name} WHERE Key_name = %s", $index_name));

  if ($index !== $index_name) {
    $wpdb->query("ALTER TABLE {$table_name} ADD {$definition}");
  }
}

function migrate_likes_table() {
  global $wpdb;

  $table_name = get_likes_table_name();

  maybe_add_likes_column('user_key', "varchar(80) NOT NULL DEFAULT ''", 'user_id');
  maybe_add_likes_column('created_at', 'datetime NULL DEFAULT NULL', 'like_type');
  maybe_add_likes_column('updated_at', 'datetime NULL DEFAULT NULL', 'created_at');

  $wpdb->query("UPDATE {$table_name} SET user_key = CONCAT('legacy:', user_id) WHERE user_key = '' AND user_id > 0");
  $wpdb->query("UPDATE {$table_name} SET user_key = CONCAT('legacy-row:', id) WHERE user_key = ''");
  $wpdb->query("UPDATE {$table_name} SET created_at = UTC_TIMESTAMP() WHERE created_at IS NULL");
  $wpdb->query("UPDATE {$table_name} SET updated_at = created_at WHERE updated_at IS NULL");

  $wpdb->query(
    "DELETE older
    FROM {$table_name} older
    INNER JOIN {$table_name} newer
      ON older.post_id = newer.post_id
      AND older.user_key = newer.user_key
      AND older.id < newer.id"
  );

  maybe_drop_likes_index('user_post_like');
  maybe_add_likes_index('user_post', 'UNIQUE KEY user_post (post_id,user_key)');
  maybe_add_likes_index('post_reaction', 'KEY post_reaction (post_id,like_type)');
}

function check_and_create_likes_table() {
  if (get_option('news_post_likes_db_version') !== '2.0.0') {
    create_likes_table();
  }
}

add_action('init', 'check_and_create_likes_table');
