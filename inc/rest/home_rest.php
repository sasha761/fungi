<?php

add_action('rest_api_init', function () {
  register_rest_route('custom/v1', '/home', [
    'methods'             => WP_REST_Server::READABLE,
    'callback'            => 'news_home_rest_get_home',
    'permission_callback' => '__return_true',
    'args'                => [
      'lang' => [
        'type'              => 'string',
        'required'          => false,
        'default'           => 'en',
        'sanitize_callback' => 'sanitize_key',
      ],
    ],
  ]);
});

function news_home_rest_get_home(WP_REST_Request $request) {
  $lang = sanitize_key((string) $request->get_param('lang'));

  if ($lang && has_action('wpml_switch_language')) {
    do_action('wpml_switch_language', $lang);
  }

  $home_page_id = news_home_rest_get_home_page_id($lang);
  $blog         = function_exists('get_posts_info') ? get_posts_info([
    'posts_per_page' => 5,
    'order'          => 'DESC',
    'orderby'        => 'date',
    'post_type'      => 'post',
  ]) : [];
  $products     = function_exists('get_products') ? get_products([
    'posts_per_page' => 16,
  ]) : [];
  $categories   = function_exists('get_taxonomy_data') ? get_taxonomy_data([
    'taxonomy' => 'category',
  ]) : [];
  $popular      = function_exists('get_posts_info') ? get_posts_info([
    'meta_key' => 'count_post_viewed',
    'orderby'  => 'meta_value_num',
  ], false) : [];

  return rest_ensure_response([
    'pageId'         => $home_page_id,
    'shopLink'       => news_home_rest_get_shop_link(),
    'labels'         => news_home_rest_get_labels(),
    'heroSection'    => news_home_rest_normalize_hero(news_home_rest_get_field('hero_section', $home_page_id)),
    'aboutUsSection' => news_home_rest_normalize_about(news_home_rest_get_field('about_us_section', $home_page_id)),
    'ourProducts'    => news_home_rest_normalize_products_section(news_home_rest_get_field('our_products', $home_page_id)),
    'ourVision'      => news_home_rest_normalize_vision(news_home_rest_get_field('our_vision', $home_page_id)),
    'blogSection'    => news_home_rest_normalize_section_title(news_home_rest_get_field('blog_section', $home_page_id)),
    'products'       => array_values(array_filter(array_map('news_home_rest_normalize_product', is_array($products) ? $products : []))),
    'categories'     => array_values(array_filter(array_map('news_home_rest_normalize_category', is_array($categories) ? $categories : []))),
    'blog'           => array_values(array_filter(array_map('news_home_rest_normalize_post', is_array($blog) ? $blog : []))),
    'popularPosts'   => array_values(array_filter(array_map('news_home_rest_normalize_post', is_array($popular) ? $popular : []))),
  ]);
}

function news_home_rest_get_home_page_id($lang = 'en') {
  $front_page_id = absint(get_option('page_on_front'));

  if ($front_page_id && function_exists('icl_object_id')) {
    $translated_id = apply_filters('wpml_object_id', $front_page_id, 'page', true, $lang);
    $front_page_id = $translated_id ? absint($translated_id) : $front_page_id;
  }

  if ($front_page_id) {
    return $front_page_id;
  }

  $pages = get_posts([
    'post_type'        => 'page',
    'posts_per_page'   => 1,
    'post_status'      => 'publish',
    'fields'           => 'ids',
    'suppress_filters' => false,
    'meta_key'         => '_wp_page_template',
    'meta_value'       => 'template-pages/home-template.php',
  ]);

  return ! empty($pages) ? absint($pages[0]) : 0;
}

function news_home_rest_get_field($field_name, $post_id) {
  if (! function_exists('get_field') || ! $post_id) {
    return null;
  }

  return get_field($field_name, $post_id);
}

function news_home_rest_get_shop_link() {
  if (function_exists('wc_get_page_id')) {
    $shop_id = wc_get_page_id('shop');

    if ($shop_id && $shop_id > 0) {
      return esc_url_raw((string) get_permalink($shop_id));
    }
  }

  return esc_url_raw(home_url('/shop/'));
}

function news_home_rest_get_labels() {
  return [
    'learnMore'    => news_home_rest_translate('Learn more', 'buttons - Learn more'),
    'learnMoreAlt' => news_home_rest_translate('Learn More', 'buttons - Learn More'),
    'readMore'     => news_home_rest_translate('Read More', 'buttons - Read More'),
    'shopNow'      => news_home_rest_translate('Shop now', 'buttons - Shop now'),
    'addToCart'    => news_home_rest_translate('Add to cart', 'buttons - Add to cart'),
    'adding'       => news_home_rest_translate('Adding...', 'buttons - Adding'),
    'outOfStock'   => news_home_rest_translate('Out of stock', 'Product - Out of stock'),
    'blogCategory' => news_home_rest_translate('Blog Category', 'Sidebar - category title'),
    'popularPost'  => news_home_rest_translate('Popular Post', 'Sidebar - posts title'),
  ];
}

function news_home_rest_translate($string, $name) {
  if (function_exists('news_layout_rest_translate')) {
    return news_layout_rest_translate($string, $name);
  }

  return news_translate_string($string, $name);
}

function news_home_rest_normalize_hero($data) {
  if (! is_array($data) || empty($data)) {
    return null;
  }

  return [
    'title' => news_home_rest_clean_html($data['title'] ?? ''),
    'link'  => news_home_rest_normalize_link($data['link'] ?? null, news_home_rest_translate('Learn more', 'buttons - Learn more')),
  ];
}

function news_home_rest_normalize_about($data) {
  if (! is_array($data) || empty($data)) {
    return null;
  }

  $posts = isset($data['posts']) && is_array($data['posts']) ? $data['posts'] : [];

  return [
    'title'    => news_home_rest_clean_html($data['title'] ?? ''),
    'subtitle' => news_home_rest_clean_html($data['subtitle'] ?? ''),
    'button'   => news_home_rest_normalize_link($data['button'] ?? null),
    'image'    => news_home_rest_normalize_image($data['image'] ?? null, [394, 466]),
    'posts'    => array_values(array_filter(array_map('news_home_rest_normalize_icon_text', $posts))),
  ];
}

function news_home_rest_normalize_products_section($data) {
  if (! is_array($data) || empty($data)) {
    return null;
  }

  $benefits = isset($data['benefits']) && is_array($data['benefits']) ? $data['benefits'] : [];

  return [
    'title'    => news_home_rest_clean_html($data['title'] ?? ''),
    'subtitle' => news_home_rest_clean_html($data['subtitle'] ?? ''),
    'benefits' => array_values(array_filter(array_map(static function ($benefit) {
      if (! is_array($benefit)) {
        return null;
      }

      return [
        'text' => news_home_rest_clean_html($benefit['text'] ?? ''),
      ];
    }, $benefits))),
  ];
}

function news_home_rest_normalize_vision($data) {
  if (! is_array($data) || empty($data)) {
    return null;
  }

  $info = isset($data['info']) && is_array($data['info']) ? $data['info'] : [];

  return [
    'title'    => news_home_rest_clean_html($data['title'] ?? ''),
    'subtitle' => news_home_rest_clean_html($data['subtitle'] ?? ''),
    'info'     => array_values(array_filter(array_map(static function ($item) {
      if (! is_array($item)) {
        return null;
      }

      return [
        'title'    => news_home_rest_clean_html($item['title'] ?? ''),
        'subtitle' => news_home_rest_clean_html($item['subtitle'] ?? ''),
        'text'     => news_home_rest_clean_html($item['text'] ?? ''),
      ];
    }, $info))),
    'quote'    => news_home_rest_normalize_quote($data['quote'] ?? null),
  ];
}

function news_home_rest_normalize_section_title($data) {
  if (! is_array($data) || empty($data)) {
    return null;
  }

  return [
    'title'    => news_home_rest_clean_html($data['title'] ?? ''),
    'subtitle' => news_home_rest_clean_html($data['subtitle'] ?? ''),
  ];
}

function news_home_rest_normalize_icon_text($item) {
  if (! is_array($item)) {
    return null;
  }

  return [
    'icon'  => news_home_rest_normalize_image($item['icon'] ?? null),
    'title' => news_home_rest_clean_html($item['title'] ?? ''),
    'text'  => news_home_rest_clean_html($item['text'] ?? ''),
    'link'  => news_home_rest_normalize_link($item['link'] ?? null, news_home_rest_translate('Learn More', 'buttons - Learn More')),
  ];
}

function news_home_rest_normalize_quote($quote) {
  if (! is_array($quote) || empty($quote)) {
    return null;
  }

  return [
    'text'   => news_home_rest_clean_html($quote['text'] ?? ''),
    'author' => news_home_rest_text($quote['author'] ?? ''),
    'image'  => news_home_rest_normalize_image($quote['image'] ?? null),
  ];
}

function news_home_rest_normalize_product($product) {
  if (! is_object($product) && ! is_array($product)) {
    return null;
  }

  $product = (object) $product;
  $id      = absint($product->ID ?? 0);

  if (! $id) {
    return null;
  }

  $wc_product = function_exists('wc_get_product') ? wc_get_product($id) : null;
  $title      = news_home_rest_text($product->title ?? get_the_title($id));
  $price      = $wc_product ? (string) $wc_product->get_price() : (string) ($product->sale_price ?? $product->regular_price ?? '0');
  $stock      = $wc_product ? (string) $wc_product->get_stock_status() : (string) ($product->outStock ?? 'instock');

  return [
    'id'           => $id,
    'title'        => $title,
    'link'         => esc_url_raw((string) ($product->link ?? get_permalink($id))),
    'priceHtml'    => wp_kses_post((string) ($product->price ?? ($wc_product ? $wc_product->get_price_html() : ''))),
    'price'        => sanitize_text_field($price),
    'regularPrice' => sanitize_text_field((string) ($product->regular_price ?? ($wc_product ? $wc_product->get_regular_price() : ''))),
    'salePrice'    => sanitize_text_field((string) ($product->sale_price ?? ($wc_product ? $wc_product->get_sale_price() : ''))),
    'isSale'       => (bool) ($product->is_sale ?? ($wc_product ? $wc_product->is_on_sale() : false)),
    'percent'      => (float) ($product->percent ?? 0),
    'stockStatus'  => sanitize_key($stock),
    'inStock'      => in_array($stock, ['instock', 'onbackorder'], true),
    'image'        => news_home_rest_normalize_image_data($product->thumb_md ?? null, $title),
    'rating'       => (float) ($product->rating ?? ($wc_product ? $wc_product->get_average_rating() : 0)),
  ];
}

function news_home_rest_normalize_post($post) {
  if (! is_object($post) && ! is_array($post)) {
    return null;
  }

  $post = (object) $post;
  $id   = absint($post->ID ?? 0);

  if (! $id) {
    return null;
  }

  $categories = [];

  foreach ((array) ($post->category ?? []) as $category) {
    if (! is_object($category) || empty($category->term_id)) {
      continue;
    }

    $categories[] = [
      'id'   => absint($category->term_id),
      'name' => news_home_rest_text($category->name ?? ''),
      'slug' => sanitize_title((string) ($category->slug ?? '')),
      'link' => esc_url_raw((string) get_category_link($category->term_id)),
    ];
  }

  return [
    'id'         => $id,
    'title'      => news_home_rest_text($post->title ?? get_the_title($id)),
    'permalink'  => esc_url_raw((string) ($post->permalink ?? get_permalink($id))),
    'date'       => news_home_rest_text($post->date ?? get_the_date('M j, Y', $id)),
    'authorName' => news_home_rest_text($post->author_name ?? ''),
    'authorLink' => esc_url_raw((string) ($post->author_link ?? '')),
    'excerpt'    => news_home_rest_excerpt($post->post_excerpt ?? ''),
    'content'    => news_home_rest_excerpt($post->post_content ?? ''),
    'categories' => $categories,
    'likes'      => news_home_rest_normalize_likes($post->post_likes ?? []),
    'image'      => news_home_rest_normalize_image_data($post->thumb_md ?? null, news_home_rest_text($post->title ?? '')),
    'smallImage' => news_home_rest_normalize_image_data($post->thumb_sm ?? null, news_home_rest_text($post->title ?? '')),
  ];
}

function news_home_rest_normalize_category($category) {
  if (! is_array($category)) {
    return null;
  }

  return [
    'name'  => news_home_rest_text($category['name'] ?? ''),
    'slug'  => sanitize_title((string) ($category['slug'] ?? '')),
    'link'  => esc_url_raw((string) ($category['link'] ?? '')),
    'image' => news_home_rest_normalize_image($category['img'] ?? null),
    'count' => absint($category['count'] ?? 0),
  ];
}

function news_home_rest_normalize_likes($likes) {
  $likes = is_array($likes) ? $likes : [];

  return [
    'like'     => absint($likes['like'] ?? 0),
    'fire'     => absint($likes['fire'] ?? 0),
    'heart'    => absint($likes['heart'] ?? 0),
    'swearing' => absint($likes['swearing'] ?? 0),
  ];
}

function news_home_rest_normalize_link($link, $fallback_title = '') {
  if (empty($link)) {
    return null;
  }

  if (is_string($link)) {
    return [
      'url'    => esc_url_raw($link),
      'title'  => news_home_rest_text($fallback_title ?: $link),
      'target' => '',
    ];
  }

  if (! is_array($link)) {
    return null;
  }

  $url = esc_url_raw((string) ($link['url'] ?? ''));

  if (! $url) {
    return null;
  }

  return [
    'url'    => $url,
    'title'  => news_home_rest_text($link['title'] ?? $fallback_title),
    'target' => sanitize_text_field((string) ($link['target'] ?? '')),
  ];
}

function news_home_rest_normalize_image($image, $size = 'full') {
  if (empty($image)) {
    return null;
  }

  $image_id = 0;
  $image_url = '';
  $alt = '';
  $width = 0;
  $height = 0;

  if (is_string($image) && filter_var($image, FILTER_VALIDATE_URL)) {
    $image_url = esc_url_raw($image);
    $image_id  = absint(attachment_url_to_postid($image_url));
  }

  if (is_numeric($image)) {
    $image_id = absint($image);
  } elseif (is_array($image)) {
    $image_id = absint($image['ID'] ?? $image['id'] ?? 0);
    $image_url = esc_url_raw((string) ($image['url'] ?? ''));
    $alt = news_home_rest_text($image['alt'] ?? '');
    $width = absint($image['width'] ?? 0);
    $height = absint($image['height'] ?? 0);
  }

  if ($image_id) {
    $src = news_home_rest_get_resized_image_src($image_id, $size);

    if ($src) {
      return [
        'id'     => $image_id,
        'url'    => esc_url_raw((string) $src[0]),
        'alt'    => news_home_rest_text(get_post_meta($image_id, '_wp_attachment_image_alt', true)),
        'width'  => absint($src[1] ?? 0),
        'height' => absint($src[2] ?? 0),
      ];
    }
  }

  if ($image_url) {
    $src = news_home_rest_resize_image_url($image_url, $size, $width, $height);

    return [
      'id'     => $image_id,
      'url'    => esc_url_raw((string) ($src[0] ?? $image_url)),
      'alt'    => $alt,
      'width'  => absint($src[1] ?? $width),
      'height' => absint($src[2] ?? $height),
    ];
  }

  return null;
}

function news_home_rest_get_resized_image_src($image_id, $size = 'full') {
  $full = wp_get_attachment_image_src($image_id, 'full');

  if (! $full) {
    return null;
  }

  if (is_array($size) && isset($size[0], $size[1])) {
    return news_home_rest_resize_image_url((string) $full[0], $size, absint($full[1] ?? 0), absint($full[2] ?? 0));
  }

  return wp_get_attachment_image_src($image_id, $size);
}

function news_home_rest_resize_image_url($image_url, $size = 'full', $fallback_width = 0, $fallback_height = 0) {
  if (! is_array($size) || ! isset($size[0], $size[1])) {
    return [
      $image_url,
      $fallback_width,
      $fallback_height,
    ];
  }

  $width  = absint($size[0]);
  $height = absint($size[1]);

  if (! $width || ! $height || ! class_exists('Timber\\ImageHelper')) {
    return [
      $image_url,
      $fallback_width,
      $fallback_height,
    ];
  }

  try {
    $resized_url = \Timber\ImageHelper::resize($image_url, $width, $height);
  } catch (\Throwable $error) {
    $resized_url = '';
  }

  if (! $resized_url) {
    return [
      $image_url,
      $fallback_width,
      $fallback_height,
    ];
  }

  return [
    esc_url_raw((string) $resized_url),
    $width,
    $height,
  ];
}

function news_home_rest_normalize_image_data($image, $alt = '') {
  if (! is_array($image)) {
    return null;
  }

  return [
    'url'    => esc_url_raw((string) ($image['url'] ?? '')),
    'webp'   => esc_url_raw((string) ($image['webp'] ?? '')),
    'width'  => absint($image['width'] ?? 0),
    'height' => absint($image['height'] ?? 0),
    'alt'    => news_home_rest_text($alt),
  ];
}

function news_home_rest_clean_html($value) {
  return wp_kses_post((string) $value);
}

function news_home_rest_text($value) {
  return html_entity_decode(wp_strip_all_tags((string) $value), ENT_QUOTES, get_bloginfo('charset') ?: 'UTF-8');
}

function news_home_rest_excerpt($value) {
  return wp_trim_words(news_home_rest_text($value), 55, '...');
}
