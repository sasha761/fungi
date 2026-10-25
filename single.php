<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package News
 */
function get_likes_for_post_translations( $post_id ) {
  if ( function_exists( 'get_likes_counts_for_post' ) ) {
    return get_likes_counts_for_post( $post_id );
  }

  global $wpdb;

  // Получаем все переводные ID через WPML (без прямых запросов)
  $trid = $wpdb->get_var( $wpdb->prepare("
      SELECT trid FROM {$wpdb->prefix}icl_translations
       WHERE element_id = %d
       LIMIT 1
  ", $post_id ) );

  if ( ! $trid ) {
      // Если вдруг записи нет в icl_translations, просто вернём данные для одного поста
      return [
          'like' => 0,
          'fire' => 0,
          'heart' => 0,
          'swearing' => 0,
          'clown' => 0,
      ];
  }

  // Все element_id в группе перевода
  $translations = $wpdb->get_col( $wpdb->prepare("
      SELECT element_id 
      FROM {$wpdb->prefix}icl_translations 
      WHERE trid = %d
  ", $trid ) );

  // Подготовка плейсхолдеров и выполнение запроса
  if ( empty($translations) ) {
      $translations = [ $post_id ];
  }
  $placeholders = implode(',', array_fill(0, count($translations), '%d'));

  $query = $wpdb->prepare("
      SELECT like_type, COUNT(*) as count
        FROM {$wpdb->prefix}post_likes
       WHERE post_id IN ($placeholders)
       GROUP BY like_type
  ", ...$translations );

  $results = $wpdb->get_results( $query );

  // Базовые значения
  $likes_data = [
      'like' => 0,
      'fire' => 0,
      'heart' => 0,
      'swearing' => 0,
      'clown' => 0,
  ];
  // Заполняем
  foreach ( $results as $row ) {
      if ( isset($likes_data[$row->like_type]) ) {
          $likes_data[$row->like_type] = $row->count;
      }
  }

  return $likes_data;
}


$main_language_code = 'en';

$context = Timber::context();

$post_id        = get_the_ID();
$post_type      = get_post_type( $post_id );
$thumbnail_id   = get_post_thumbnail_id( $post_id );
$author_id      = absint( get_post_field( 'post_author', $post_id ) );

$likes_data = get_likes_for_post_translations( $post_id );
$post_views = absint( get_post_meta( $post_id, 'count_post_viewed', true ) );
$comments = get_comments(
  [
    'post_id' => $post_id,
    'status'  => 'approve',
  ]
);

$nested_comments = nest_comments( $comments );

$guide              = fh_guide_for_post( $post_id );
$parent_pillar_link = $guide ? $guide['pillar'] : null;

$data = [
    'ID'             => $post_id,
    'main_post_id'   => $post_id,
    'title'          => get_the_title( $post_id ),
    'date'           => get_the_date( 'M j, Y', $post_id ),
    'thumbnail_xl'   => get_image_data($thumbnail_id, 'single_post_desktop'),
    'thumbnail_md'   => get_image_data($thumbnail_id, 'single_post_phone'),
    'author_name'    => get_the_author_meta( 'display_name', $author_id ),
    'author_link'    => $author_id ? esc_url( get_author_posts_url( $author_id ) ) : '',
    'views'          => max( 1, $post_views + 1 ),
    'comments'       => $nested_comments,
    'total_comments' => count($comments),
    'if_attention'   => get_field('attention', $post_id),
    'post_likes'     => $likes_data,
    'selected_reaction' => function_exists( 'get_current_user_like_for_post' ) ? get_current_user_like_for_post( $post_id ) : null,
    'parent_pillar'     => $parent_pillar_link,
    'trust'             => fh_article_trust( $post_id ),
    'guide_nav'         => fh_guide_neighbours( $post_id ),
    'part_of_guide_label' => esc_html__( 'Part of guide:', 'news' ),
    'content'        => apply_filters( 'the_content', get_the_content( null, false, $post_id ) ),
];

$context['data'] = $data;
$context['reaction_endpoint'] = rest_url( 'custom/v1/handle_like' );
$context['reaction_nonce'] = wp_create_nonce( 'wp_rest' );


$context['related'] = get_posts_info([
  'numberposts' => 6,
  'ignore_sticky_posts' => true,
  'orderby' => 'rand',
  'post_status' => 'publish',
  'post_type' => 'post', 
  'post__not_in' => [$post_id] 
]);


Timber::render( array( 'single-' . $post_id . '.twig', 'single-' . $post_type . '.twig', 'single.twig' ), $context );
