<?php
function custom_dequeue()
{
  wp_dequeue_style('news-style');
  wp_deregister_style('news-style');
  wp_dequeue_style('woocommerce-general');
  wp_deregister_style('woocommerce-general');
  wp_dequeue_style('woocommerce-smallscreen');
  wp_deregister_style('woocommerce-smallscreen');
  wp_dequeue_style('woocommerce-layout');
  wp_deregister_style('woocommerce-layout');
  wp_dequeue_style('woocommerce_prettyPhoto_css');
  wp_deregister_style('woocommerce_prettyPhoto_css');
  wp_dequeue_style('wp-block-library');
  wp_deregister_style('wp-block-library');
  wp_dequeue_style('woo-variation-swatches-tooltip');
  wp_deregister_style('woo-variation-swatches-tooltip');
  wp_dequeue_style('woo-variation-swatches');
  wp_deregister_style('woo-variation-swatches');
  wp_dequeue_style('aws-style');
  wp_deregister_style('aws-style');
  wp_dequeue_style('wc-blocks-style');
  wp_deregister_style('wc-blocks-style');
  wp_dequeue_style('wc-blocks-vendors-style');
  wp_deregister_style('wc-blocks-vendors-style');
  wp_dequeue_style('select2');
  wp_deregister_style('select2');
  wp_dequeue_style('classic-theme-styles');
  wp_deregister_style('classic-theme-styles');
  wp_dequeue_style('wpml-blocks');
  wp_deregister_style('wpml-blocks');
  wp_dequeue_style('wpml-legacy-horizontal-list-0');
  wp_deregister_style('wpml-legacy-horizontal-list-0');
  wp_dequeue_style('wcml-dropdown-0');
  wp_deregister_style('wcml-dropdown-0');
  wp_dequeue_style('brands-styles');
  wp_deregister_style('brands-styles');
  



  // wp_deregister_script( 'wc-single-product' );
  wp_deregister_script('wc-cart-fragments');
  wp_deregister_script('aws-script');
  wp_deregister_script('comment-reply');

  wp_deregister_script('jquery-migrate');
  wp_deregister_script('jquery-core');
  wp_deregister_script('jquery-blockui');

  wp_deregister_script('wc-add-to-cart');
  wp_deregister_script('wc-single-product');
  wp_deregister_script('woocommerce');

  wp_deregister_script('cart-widget');
  wp_deregister_script('wcml-mc-scripts');
  wp_deregister_script('sourcebuster-js');
  wp_deregister_script('wc-order-attribution');
  // wp_deregister_script( 'selectWoo' );
}

add_action('wp_enqueue_scripts', 'custom_dequeue', 9999);
add_action('wp_head', 'custom_dequeue', 9999);

if (function_exists('acf_add_options_page')) {
  acf_add_options_page(array(
    'page_title' => 'Theme General Settings',
    'menu_title' => 'Theme Settings',
    'redirect' => false
  ));
}

add_filter('comments_number', 'comments_number_count', 10, 2);
function comments_number_count($output, $number)
{
  if ($number == 0)
    $output = '0';
  elseif ($number == 1)
    $output = '1';
  else
    $output = $number;
  return $output;
}

function allow_svg_uploads($mimes)
{
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'allow_svg_uploads');


// most popular
function count_post_visits()
{
  if (is_single()) {
    global $post;
    $views = get_post_meta($post->ID, 'count_post_viewed', true);
    if ($views == '') {
      update_post_meta($post->ID, 'count_post_viewed', '1');
    } else {
      $views_number = intval($views);
      update_post_meta($post->ID, 'count_post_viewed', ++$views_number);
    }
  }
}
add_action('wp_head', 'count_post_visits');


remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
remove_action('wp_head', 'index_rel_link'); // index link
remove_action('wp_head', 'parent_post_rel_link', 10, 0); // prev link
remove_action('wp_head', 'start_post_rel_link', 10, 0); // start link
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // Display relational links for the posts adjacent to the current post.
remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version

/**
 * Redirect to the homepage all users trying to access feeds.
 */
function disable_feeds()
{
  wp_redirect(home_url());
  die;
}

// Disable global RSS, RDF & Atom feeds.
add_action('do_feed', 'disable_feeds', -1);
add_action('do_feed_rdf', 'disable_feeds', -1);
add_action('do_feed_rss', 'disable_feeds', -1);
add_action('do_feed_rss2', 'disable_feeds', -1);
add_action('do_feed_atom', 'disable_feeds', -1);

// Disable comment feeds.
add_action('do_feed_rss2_comments', 'disable_feeds', -1);
add_action('do_feed_atom_comments', 'disable_feeds', -1);

// Prevent feed links from being inserted in the <head> of the page.
add_action('feed_links_show_posts_feed', '__return_false', -1);
add_action('feed_links_show_comments_feed', '__return_false', -1);
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);


add_action('woocommerce_admin_order_data_after_order_details', 'show_custom_fields_in_order_admin');
function show_custom_fields_in_order_admin($order)
{
  $messenger = $order->get_meta('_messenger');
  $messengerValue = $order->get_meta('_messenger_value');
  if ($messenger || $messengerValue) {
    echo '<div class="order_data_column">';
    echo '<h3>Мессенджер</h3>';
    if ($messenger) {
      echo '<p><strong>Тип:</strong> ' . esc_html($messenger) . '</p>';
    }
    if ($messengerValue) {
      echo '<p><strong>Контакт:</strong> ' . esc_html($messengerValue) . '</p>';
    }
    echo '</div>';
  }
}

function my_get_large_image_url_by_src( $src ) {
  // Получаем ID вложения по URL
  $attachment_id = attachment_url_to_postid( $src );
  
  // var_dump($src);
  if ( $attachment_id ) {
      // Получаем данные изображения для размера 'large'
      $large = wp_get_attachment_image_src( $attachment_id, 'large' );

      // var_dump($large);
      if ( $large && ! empty( $large[0] ) ) {
          return $large[0];
      }
  }
  return false;
}


function modify_img_tags_for_lazyload( $content ) {
  global $post;
  if ( ! $post ) {
    return $content;
  }

  // Получаем заголовок поста, который будем использовать как alt, если он отсутствует.
  $title = get_the_title( $post->ID );

  // Обрабатываем все теги <img> в контенте.
  $content = preg_replace_callback( '/<img[^>]*>/i', function ( $matches ) use ( $title ) {
      $img_tag = $matches[0];

      // 1. Извлекаем URL изображения из атрибута src
      $src = '';
      if ( preg_match( '/src\s*=\s*[\'"]([^\'"]+)[\'"]/', $img_tag, $src_match ) ) {
          $src = $src_match[1];
      }

      // 2. Если URL найден, пытаемся получить версию изображения размера "large"
      if ( $src ) {
          $large_url = my_get_large_image_url_by_src( $src );
          if ( $large_url ) {
              $src = $large_url;
          }
      }

      // 3. Удаляем атрибут src, чтобы затем подставить data-src
      $img_tag = preg_replace( '/\s*src\s*=\s*([\'"]).*?\1/i', '', $img_tag );

      // 4. Добавляем data-src с новым URL изображения
      $img_tag = preg_replace( '/<img/i', '<img data-src="' . esc_attr( $src ) . '"', $img_tag );

      // 5. Обрабатываем класс: добавляем класс lazy
      if ( preg_match( '/class\s*=\s*([\'"])(.*?)\1/i', $img_tag, $class_match ) ) {
          $classes = $class_match[2];
          if ( ! preg_match( '/\blazy\b/', $classes ) ) {
              $classes .= ' lazy';
          }
          $img_tag = preg_replace( '/class\s*=\s*([\'"]).*?\1/i', 'class="' . trim( $classes ) . '"', $img_tag );
      } else {
          // Если класс отсутствует – добавляем его
          $img_tag = preg_replace( '/<img/i', '<img class="lazy"', $img_tag );
      }

      // 6. Обрабатываем атрибут alt:
      // Если атрибут отсутствует, добавляем его с заголовком поста.
      if ( ! preg_match( '/alt\s*=\s*[\'"]([^\'"]*)[\'"]/i', $img_tag, $alt_match ) ) {
          $img_tag = preg_replace( '/<img/i', '<img alt="' . esc_attr( $title ) . '"', $img_tag );
      } else {
          // Если alt есть, но пустой (например, alt=""), заменяем его на заголовок
          if ( trim( $alt_match[1] ) === '' ) {
              $img_tag = preg_replace( '/alt\s*=\s*([\'"])\s*\1/i', 'alt="' . esc_attr( $title ) . '"', $img_tag );
          }
      }

      return $img_tag;
  }, $content );

  return $content;
}
add_filter( 'the_content', 'modify_img_tags_for_lazyload', 20 );


function get_all_language_comments($post_id, $args = [])
{
  global $sitepress;

  $args = [
    'post_id' => $post_id,
    'status' => 'approve',
  ];

  // Убираем фильтр WPML, чтобы не обрезало по языку
  remove_filter('comments_clauses', [$sitepress, 'comments_clauses'], 10);

  $comments = get_comments($args);

  // Возвращаем фильтр обратно
  add_filter('comments_clauses', [$sitepress, 'comments_clauses'], 10, 2);

  return $comments;
}

function get_main_post_id($current_post_id, $main_language_code = 'en')
{
  $main_id = (int) apply_filters('wpml_object_id', $current_post_id, 'post', true, $main_language_code);
  return $main_id ?: $current_post_id;
}

function nest_comments($comments) {
  $comments_by_id = [];

  // Инициализируем новое свойство custom_children для каждого комментария
  foreach ($comments as $comment) {
    $comment->meta = [
      'like_count' => get_comment_meta($comment->comment_ID, 'like_count', true) ?: 0,
      'dislike_count' => get_comment_meta($comment->comment_ID, 'dislike_count', true) ?: 0,
      'rating' => get_comment_meta($comment->comment_ID, 'rating', true) ?: 5
    ];
    // Используем имя, отличное от children, чтобы не было конфликта с защищённым свойством
    $comment->custom_children = [];
    $comments_by_id[$comment->comment_ID] = $comment;
  }

  $nested = [];
  // Распределяем комментарии по родительским комментариям
  foreach ($comments as $comment) {
    if ($comment->comment_parent && isset($comments_by_id[$comment->comment_parent])) {
      $comments_by_id[$comment->comment_parent]->custom_children[] = $comment;
    } else {
      // Если комментарий не является ответом (comment_parent == 0) или родитель не найден — это корневой комментарий
      $nested[] = $comment;
    }
  }

  return $nested;
}