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


function add_img_alt_if_empty($content)
{
  global $post;
  if (!$post) return $content;

  $title = get_the_title($post->ID);

  // Регулярное выражение ищет все теги <img ...>
  $content = preg_replace_callback(
    '/<img[^>]*>/i',
    function ($matches) use ($title) {
      $img_tag = $matches[0];

      if (!preg_match('/alt\s*=/', $img_tag)) {
        $img_tag = str_replace('<img', '<img alt="' . esc_attr($title) . '"', $img_tag);
      } else {
        // Если alt есть, но, например, пустой: alt=""
        // — заменим пустой на заголовок
        $img_tag = preg_replace(
          '/alt\s*=\s*[\'"]\s*[\'"]/',
          'alt="' . esc_attr($title) . '"',
          $img_tag
        );
      }

      return $img_tag;
    },
    $content
  );

  return $content;
}
add_filter('the_content', 'add_img_alt_if_empty');