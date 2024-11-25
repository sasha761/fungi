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
	define( '_S_VERSION', '2.0.0' );
}

// add_filter('timber_compile_result', function($output) {
//   return str_replace('http://', 'https://', $output);
// });


require_once get_template_directory() . '/Classes/SiteInit.class.php';
require_once get_template_directory() . '/Classes/ThemeImageSizes.class.php';
// require_once get_template_directory() . '/Classes/RegisterSystem.class.php';
// require_once get_template_directory() . '/Classes/NotificationSystem.class.php';
require_once get_template_directory() . '/inc/config.php';
require_once get_template_directory() . '/inc/language.php';
require_once get_template_directory() . '/inc/telegram_notification.php';
require_once get_template_directory() . '/inc/template-functions.php';
require_once get_template_directory() . '/inc/get_taxonomy_data.php'; // get_taxonomy_data
require_once get_template_directory() . '/inc/get_products.php'; // get_products
require_once get_template_directory() . '/inc/get_cart_info.php'; // get_cart_info
require_once get_template_directory() . '/inc/get_posts_info.php'; // get_posts_info
require_once get_template_directory() . '/inc/likes.php';

// require_once get_template_directory() . '/test.php';


require_once get_template_directory() . '/inc/ajax/add_to_cart.php';
require_once get_template_directory() . '/inc/ajax/remove_from_cart.php';
require_once get_template_directory() . '/inc/ajax/cart_quantity.php';
require_once get_template_directory() . '/inc/ajax/mini-cart.php';
require_once get_template_directory() . '/inc/ajax/quick_buy.php';
require_once get_template_directory() . '/inc/ajax/show_more_products.php';
