<?php
/**
 * News functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package News
 */
require_once(__DIR__ . '/vendor/autoload.php');
Timber\Timber::init();


if ( ! class_exists( 'Timber' ) ) {
  add_action( 'admin_notices', function () {
    echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
  } );

  return;
}
/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = 'templates';


if ( ! defined( '_S_VERSION' ) ) {
	define( '_S_VERSION', '2.0.1' );
}


require_once get_template_directory() . '/Classes/SiteInit.class.php';
require_once get_template_directory() . '/Classes/ThemeImageSizes.class.php';

require_once get_template_directory() . '/inc/config.php';
require_once get_template_directory() . '/inc/language.php';
require_once get_template_directory() . '/inc/telegram_notification.php';
require_once get_template_directory() . '/inc/template-functions.php';
require_once get_template_directory() . '/inc/get_image_data.php'; 
require_once get_template_directory() . '/inc/get_taxonomy_data.php'; // get_taxonomy_data
require_once get_template_directory() . '/inc/get_products.php'; // get_products
require_once get_template_directory() . '/inc/get_cart_info.php'; // get_cart_info
require_once get_template_directory() . '/inc/get_posts_info.php'; // get_posts_info


require_once get_template_directory() . '/inc/rest/likes_rest.php';
require_once get_template_directory() . '/inc/rest/summarize_rest.php';

require_once get_template_directory() . '/inc/ajax/quick_buy.php';
require_once get_template_directory() . '/inc/ajax/add_to_cart.php';
require_once get_template_directory() . '/inc/ajax/remove_from_cart.php';
require_once get_template_directory() . '/inc/ajax/cart_quantity.php';
// require_once get_template_directory() . '/inc/ajax/mini-cart.php';
// require_once get_template_directory() . '/inc/ajax/show_more_products.php';

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
     Опционально: можно ограничить повторное голосование для неавторизованных пользователей.
     Например, устанавливая cookie с уникальным именем для каждого комментария.
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
add_action( 'wp_ajax_comment_reaction', 'my_comment_reaction_ajax' );
add_action( 'wp_ajax_nopriv_comment_reaction', 'my_comment_reaction_ajax' );