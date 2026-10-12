<?php

add_action('rest_api_init', function () {
  register_rest_route('custom/v1', '/layout', [
    'methods'             => WP_REST_Server::READABLE,
    'callback'            => 'news_layout_rest_get_layout',
    'permission_callback' => '__return_true',
    'args'                => [
      'lang' => [
        'type'              => 'string',
        'required'          => false,
        'default'           => 'en',
        'sanitize_callback' => 'sanitize_key',
      ],
      'url'  => [
        'type'              => 'string',
        'required'          => false,
        'default'           => '/',
        'sanitize_callback' => 'news_layout_rest_sanitize_url_path',
      ],
    ],
  ]);
});

function news_layout_rest_get_layout(WP_REST_Request $request) {
  $lang = sanitize_key((string) $request->get_param('lang'));
  $url  = news_layout_rest_normalize_request_url((string) $request->get_param('url'));

  if ($lang && has_action('wpml_switch_language')) {
    do_action('wpml_switch_language', $lang);
  }

  $current_lang = news_layout_rest_get_current_language($lang);

  return rest_ensure_response([
    'site'        => [
      'name'        => get_bloginfo('name'),
      'description' => get_bloginfo('description'),
      'url'         => home_url('/'),
      'htmlLang'    => $current_lang,
    ],
    'assets'      => [
      'logo'             => '/theme/img/main_logo_blue.webp',
      'footerBackground' => '/theme/img/Footer.webp',
    ],
    'currentLang' => $current_lang,
    'languages'   => news_layout_rest_get_languages($current_lang, $url),
    'menus'       => [
      'primary' => news_layout_rest_get_menu('menu-1'),
      'footer'  => news_layout_rest_get_menu('menu-2'),
      'mobile'  => news_layout_rest_get_menu('menu-3'),
    ],
    'options'     => news_layout_rest_get_options(),
    'labels'      => [
      'home'          => news_layout_rest_translate('Home', 'Menu - Home'),
      'privacyPolicy' => news_layout_rest_translate('Privacy Policy', 'Menu - Privacy Policy'),
      'share'         => news_layout_rest_translate('Share', 'Social - Share'),
      'email'         => news_layout_rest_translate('Email', 'Input - Email'),
      'items'         => news_layout_rest_translate('items', 'Cart - Items'),
      'quantity'      => news_layout_rest_translate('Quantity', 'Cart - Quantity'),
      'placeOrder'    => news_layout_rest_translate('Place an order', 'Button - Place an order'),
      'proceedForm'   => news_layout_rest_translate('Proceed the form', 'button - Proceed the form'),
      'postComment'   => news_layout_rest_translate('Post comment', 'Comments - Post comment'),
      'noComments'    => news_layout_rest_translate('No comments yet.', 'Comments - No comments yet'),
    ],
    'header'      => [
      'shippingText' => news_layout_rest_translate('FREE SHIPPING ON ORDERS OVER $125', 'Header - free shipping'),
      'regionLabel'  => news_layout_rest_translate('In Europe?', 'Header - in Europe'),
      'callUsLabel'  => news_layout_rest_translate('Call us', 'Mobile menu - Call us'),
      'searchLabel'  => news_layout_rest_translate('Search', 'input - Search'),
    ],
    'footer'      => [
      'intro'             => news_layout_rest_translate('We believe in the power of nature and its ability to improve our lives. Dive into the world of harmony with us.', 'Footer - text'),
      'contactTitle'      => news_layout_rest_translate('Contact Us', 'Footer - Contact Us'),
      'learnMoreTitle'    => news_layout_rest_translate('Learn more', 'Footer - Learn more'),
      'newsletterTitle'   => news_layout_rest_translate('Join Our Newsletter', 'Footer - Join Our Newsletter'),
      'emailPlaceholder'  => news_layout_rest_translate('Enter Your Email', 'Footer - email placeholder'),
      'subscribeLabel'    => news_layout_rest_translate('Subscribe now', 'Button - Subscribe now'),
      'consentNotice'     => news_layout_rest_translate('You may withdraw your consent at any time!', 'Footer - noties under subscribe'),
      'legalNoticeTitle'  => news_layout_rest_translate('Attention!', 'Footer - Attention'),
      'legalNotice'       => news_layout_rest_translate('We do not sell or promote drugs or illegal substances. All products presented on the website do not contain narcotic substances, do not cause addiction, and are not medicinal products.', 'Footer - legal notice'),
      'copyrightBrand'    => 'Fungi Harmony.',
    ],
  ]);
}

function news_layout_rest_get_current_language($fallback = 'en') {
  if (has_filter('wpml_current_language')) {
    $wpml_lang = apply_filters('wpml_current_language', null);

    if ($wpml_lang) {
      return sanitize_key((string) $wpml_lang);
    }
  }

  if (defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE) {
    return sanitize_key((string) ICL_LANGUAGE_CODE);
  }

  return sanitize_key((string) $fallback) ?: 'en';
}

function news_layout_rest_translate($string, $name) {
  return news_translate_string($string, $name);
}

function news_layout_rest_sanitize_url_path($url) {
  return news_layout_rest_normalize_request_url((string) $url);
}

function news_layout_rest_get_options() {
  $options = function_exists('get_fields') ? get_fields('option') : [];
  $options = is_array($options) ? $options : [];
  $social  = isset($options['social']) && is_array($options['social']) ? $options['social'] : [];

  return [
    'social' => [
      'phone'     => sanitize_text_field((string) ($social['phone'] ?? '')),
      'mail'      => sanitize_email((string) ($social['mail'] ?? '')),
      'location'  => sanitize_text_field((string) ($social['location'] ?? '')),
      'facebook'  => esc_url_raw((string) ($social['facebook'] ?? '')),
      'instagram' => esc_url_raw((string) ($social['instagram'] ?? '')),
      'youtube'   => esc_url_raw((string) ($social['youtube'] ?? '')),
      'linkedin'  => esc_url_raw((string) ($social['linkedin'] ?? '')),
      'telegram'  => esc_url_raw((string) ($social['telegram'] ?? '')),
    ],
  ];
}

function news_layout_rest_get_languages($current_lang, $current_url = '/') {
  $supported_languages = ['en', 'ru', 'uk'];
  $current_url         = news_layout_rest_normalize_request_url((string) $current_url);

  if (function_exists('icl_get_languages')) {
    $languages = icl_get_languages('skip_missing=0');

    if (is_array($languages) && ! empty($languages)) {
      return array_values(array_filter(array_map(static function ($language) use ($current_lang, $current_url, $supported_languages) {
        $code = sanitize_key((string) ($language['code'] ?? ''));

        if (! in_array($code, $supported_languages, true)) {
          return null;
        }

        return [
          'code'       => $code,
          'name'       => sanitize_text_field((string) ($language['translated_name'] ?? $language['native_name'] ?? strtoupper($code))),
          'nativeName' => sanitize_text_field((string) ($language['native_name'] ?? strtoupper($code))),
          'url'        => news_layout_rest_get_language_url($code, $current_url, (string) ($language['url'] ?? '')),
          'active'     => $code === $current_lang,
        ];
      }, $languages)));
    }
  }

  return array_map(static function ($code) use ($current_lang, $current_url) {
    return [
      'code'       => $code,
      'name'       => strtoupper($code),
      'nativeName' => strtoupper($code),
      'url'        => news_layout_rest_get_language_url($code, $current_url),
      'active'     => $code === $current_lang,
    ];
  }, $supported_languages);
}

function news_layout_rest_get_language_url($code, $current_url, $fallback_url = '') {
  $code        = sanitize_key((string) $code);
  $current_url = news_layout_rest_normalize_request_url((string) $current_url);
  $absolute    = home_url($current_url);

  if (has_filter('wpml_permalink')) {
    $translated_url = apply_filters('wpml_permalink', $absolute, $code, true);

    if (is_string($translated_url) && $translated_url) {
      return esc_url_raw($translated_url);
    }
  }

  if ($fallback_url) {
    return esc_url_raw($fallback_url);
  }

  return esc_url_raw(home_url(news_layout_rest_prefix_url_for_language($current_url, $code)));
}

function news_layout_rest_normalize_request_url($url) {
  $url   = trim((string) $url);
  $parts = $url ? wp_parse_url($url) : false;

  if (! is_array($parts)) {
    return '/';
  }

  $path = isset($parts['path']) ? (string) $parts['path'] : '/';
  $path = '/' . ltrim($path, '/');
  $path = preg_replace('#/+#', '/', $path);
  $path = news_layout_rest_strip_language_prefix($path ?: '/');

  $query = isset($parts['query']) && '' !== $parts['query'] ? '?' . $parts['query'] : '';

  return $path . $query;
}

function news_layout_rest_strip_language_prefix($path) {
  $segments = array_values(array_filter(explode('/', trim((string) $path, '/')), 'strlen'));

  if (! empty($segments) && in_array($segments[0], ['en', 'ru', 'uk'], true)) {
    array_shift($segments);
  }

  return empty($segments) ? '/' : '/' . implode('/', $segments);
}

function news_layout_rest_prefix_url_for_language($url, $code) {
  $parts = wp_parse_url((string) $url);
  $path  = is_array($parts) && isset($parts['path']) ? news_layout_rest_strip_language_prefix((string) $parts['path']) : '/';
  $query = is_array($parts) && isset($parts['query']) && '' !== $parts['query'] ? '?' . $parts['query'] : '';

  if ('en' === $code) {
    return $path . $query;
  }

  return '/' . $code . ('/' === $path ? '' : $path) . $query;
}

function news_layout_rest_get_menu($location) {
  $locations = get_nav_menu_locations();

  if (empty($locations[$location])) {
    return [];
  }

  $items = wp_get_nav_menu_items((int) $locations[$location], [
    'update_post_term_cache' => false,
  ]);

  if (! is_array($items)) {
    return [];
  }

  $nodes    = [];
  $children = [];

  foreach ($items as $item) {
    $id        = (int) $item->ID;
    $parent_id = (int) $item->menu_item_parent;

    $nodes[$id] = [
      'id'       => $id,
      'parentId' => $parent_id,
      'title'    => html_entity_decode(wp_strip_all_tags(news_translate_menu_title((string) $item->title)), ENT_QUOTES, get_bloginfo('charset') ?: 'UTF-8'),
      'url'      => esc_url_raw((string) $item->url),
      'target'   => sanitize_text_field((string) $item->target),
      'classes'  => array_values(array_filter(array_map('sanitize_html_class', (array) $item->classes))),
      'image'    => news_layout_rest_get_menu_item_image($id),
      'children' => [],
    ];

    if (! isset($children[$parent_id])) {
      $children[$parent_id] = [];
    }

    $children[$parent_id][] = $id;
  }

  $build_tree = static function ($parent_id) use (&$build_tree, &$nodes, &$children) {
    $branch = [];

    foreach ($children[$parent_id] ?? [] as $child_id) {
      if (! isset($nodes[$child_id])) {
        continue;
      }

      $node = $nodes[$child_id];
      unset($node['parentId']);
      $node['children'] = $build_tree($child_id);
      $branch[] = $node;
    }

    return $branch;
  };

  return $build_tree(0);
}

function news_layout_rest_get_menu_item_image($item_id) {
  if (! function_exists('get_field')) {
    return null;
  }

  return news_layout_rest_normalize_image(get_field('image', $item_id));
}

function news_layout_rest_normalize_image($image) {
  if (empty($image)) {
    return null;
  }

  if (is_numeric($image)) {
    $image_id = absint($image);
    $src      = wp_get_attachment_image_src($image_id, 'full');

    if (! $src) {
      return null;
    }

    $thumb = wp_get_attachment_image_src($image_id, 'thumbnail');

    return [
      'id'      => $image_id,
      'url'     => esc_url_raw((string) $src[0]),
      'alt'     => sanitize_text_field((string) get_post_meta($image_id, '_wp_attachment_image_alt', true)),
      'title'   => sanitize_text_field((string) get_the_title($image_id)),
      'caption' => sanitize_text_field((string) wp_get_attachment_caption($image_id)),
      'sizes'   => [
        'thumbnail' => $thumb ? esc_url_raw((string) $thumb[0]) : esc_url_raw((string) $src[0]),
      ],
    ];
  }

  if (! is_array($image)) {
    return null;
  }

  $sizes = isset($image['sizes']) && is_array($image['sizes']) ? $image['sizes'] : [];

  return [
    'id'      => absint($image['ID'] ?? $image['id'] ?? 0),
    'url'     => esc_url_raw((string) ($image['url'] ?? '')),
    'alt'     => sanitize_text_field((string) ($image['alt'] ?? '')),
    'title'   => sanitize_text_field((string) ($image['title'] ?? '')),
    'caption' => sanitize_text_field((string) ($image['caption'] ?? '')),
    'sizes'   => [
      'thumbnail' => esc_url_raw((string) ($sizes['thumbnail'] ?? $image['url'] ?? '')),
    ],
  ];
}
