<?php

add_action( 'wp_ajax_comment_reaction', 'my_comment_reaction_ajax' );
add_action( 'wp_ajax_nopriv_comment_reaction', 'my_comment_reaction_ajax' );

function my_comment_reaction_ajax() {
  // Если хотите использовать nonce для дополнительной защиты, раскомментируйте и настройте проверку:
  // if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'comment_reaction_nonce' ) ) {
  //     wp_send_json_error( 'Недопустимый запрос' );
  // }

  $comment_id  = isset( $_POST['comment_id'] ) ? intval( $_POST['comment_id'] ) : 0;
  $action_type = isset( $_POST['action_type'] ) ? sanitize_text_field( $_POST['action_type'] ) : '';

  if ( ! $comment_id || ! in_array( $action_type, array( 'like', 'dislike' ) ) ) {
      wp_send_json_error( 'Некорректные данные' );
  }
  
  /* 
    ограничить повторное голосование для неавторизованных пользователей. устанавливая cookie с уникальным именем для каждого комментария.
  */
  $cookie_name = 'comment_voted_' . $comment_id;
  if ( isset( $_COOKIE[ $cookie_name ] ) ) {
       wp_send_json_error( 'Вы уже проголосовали за этот отзыв' );
  }

  // Определяем ключ мета-данных для реакции
  $meta_key = ( 'like' === $action_type ) ? 'like_count' : 'dislike_count';

  $current_count = intval( get_comment_meta( $comment_id, $meta_key, true ) );
  $new_count     = $current_count + 1;
  
  update_comment_meta( $comment_id, $meta_key, $new_count );

  // Устанавливаем cookie, чтобы предотвратить повторное голосование
  setcookie( $cookie_name, $action_type, time() + 3600 * 24 * 30, COOKIEPATH, COOKIE_DOMAIN );

  wp_send_json_success( array( 'count' => $new_count ) );
}
