<?php
function get_posts_info($args = [], $is_emoji = true) {
  if ($is_emoji) global $wpdb;
  
  $defaults = [
    'author'           => '',
    'numberposts'      => 5,
		'category'         => 0,
		'orderby'          => 'date',
		'order'            => 'DESC',
		'include'          => array(),
		'exclude'          => array(),
		'meta_key'         => '',
		'meta_value'       => '',
		'post_type'        => 'post',
		'suppress_filters' => false,
    'fields'           => 'ids',
  ];

  $parsed_args = wp_parse_args( $args, $defaults );

  $get_posts_ids = get_posts($parsed_args);

  $posts = [];

  foreach ($get_posts_ids as $post_id) {
    if ($is_emoji) {
      $translations = $wpdb->get_col($wpdb->prepare(
        "SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE trid = (SELECT trid FROM {$wpdb->prefix}icl_translations WHERE element_id = %d)",
        $post_id
      ));

      if (empty($translations)) {
        $translations = [$post_id];
      }

      $placeholders = implode(',', array_fill(0, count($translations), '%d'));

      $query = $wpdb->prepare(
        "SELECT like_type, COUNT(*) as count FROM {$wpdb->prefix}post_likes WHERE post_id IN ($placeholders) GROUP BY like_type",
        ...$translations
      );
      
      $likes = $wpdb->get_results($query);

      $likes_data = [
        'like' => 0,
        'fire' => 0,
        'heart' => 0,
      ];

      foreach ($likes as $like) {
        $likes_data[$like->like_type] = $like->count;
      }
    } else {
      $likes_data = NULL;
    }
    
    $author_id = get_post_field('post_author', $post_id);

    $thumbnail_id = get_post_thumbnail_id($post_id);

    $posts[] = (object) [
      'ID'        => $post_id,
      'permalink' => get_the_permalink($post_id),
      'title'     => get_the_title($post_id),
      'date'      => get_the_date('M j, Y', $post_id), // Формат: Jul 21, 2022
      'author_name'   => get_the_author_meta('display_name', $author_id),
      'author_link'   => get_author_posts_url($author_id),
      'post_excerpt' => get_the_excerpt($post_id),
      'post_content' => get_the_content($post_id),
      'category' => get_the_category($post_id),
      'post_likes' => $likes_data,
      'thumb_md'      => get_image_data($thumbnail_id, 'archive_md'),
      'thumb_xl'      => get_image_data($thumbnail_id, 'archive_xl'),
      'archive'       => get_image_data($thumbnail_id, 'archive'),
      'thumb_sm'      => get_image_data($thumbnail_id, [100, 100]),
    ];
  }

  return $posts;
}
