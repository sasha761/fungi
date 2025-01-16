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
// require_once get_template_directory() . '/Classes/RegisterSystem.class.php';
// require_once get_template_directory() . '/Classes/NotificationSystem.class.php';


require_once get_template_directory() . '/inc/config.php';
require_once get_template_directory() . '/inc/language.php';
require_once get_template_directory() . '/inc/telegram_notification.php';
require_once get_template_directory() . '/inc/template-functions.php';
require_once get_template_directory() . '/inc/get_image_data.php'; 
require_once get_template_directory() . '/inc/get_taxonomy_data.php'; // get_taxonomy_data
require_once get_template_directory() . '/inc/get_products.php'; // get_products
require_once get_template_directory() . '/inc/get_cart_info.php'; // get_cart_info
require_once get_template_directory() . '/inc/get_posts_info.php'; // get_posts_info
require_once get_template_directory() . '/inc/likes.php';


require_once get_template_directory() . '/inc/ajax/add_to_cart.php';
require_once get_template_directory() . '/inc/ajax/remove_from_cart.php';
require_once get_template_directory() . '/inc/ajax/cart_quantity.php';
// require_once get_template_directory() . '/inc/ajax/mini-cart.php';
require_once get_template_directory() . '/inc/ajax/quick_buy.php';
// require_once get_template_directory() . '/inc/ajax/show_more_products.php';

add_action('rest_api_init', function () {
  register_rest_route('custom/v1', '/summarize', [
      'methods' => 'POST',
      'callback' => 'generate_summary',
      'permission_callback' => '__return_true', // Добавьте проверку безопасности при необходимости
  ]);
});


function generate_summary(WP_REST_Request $request) {
  $api_key = GPT_TOKEN; // Замените на ваш ключ OpenAI
  $text = $request->get_param('text'); // Получаем полный текст статьи
  $length = $request->get_param('length') ?: 'short'; // short, medium, original
  $language = $request->get_param('lang') ?: 'en'; // Устанавливаем язык, по умолчанию 'en'

  // Если пользователь запросил оригинал, возвращаем его, обернув в теги <p>
  if ($length === 'original') {
      return ['summary' => wrap_content_in_paragraphs($text)];
  }

  // Установка лимита слов
  $word_limit = $length === 'short' ? 120 : ($length === 'medium' ? 300 : 600);
  $word_to_token_ratio = 1.5; // приблизительно 1.5 токена равно 1 слово
  $max_tokens = ceil($word_limit * $word_to_token_ratio);

  // Формируем промпт в зависимости от языка
  $prompt = generate_prompt($text, $word_limit, $language);

  // Запрос к OpenAI API
  $response = wp_remote_post('https://api.openai.com/v1/chat/completions', [
    'headers' => [
      'Authorization' => 'Bearer ' . $api_key,
      'Content-Type' => 'application/json',
    ],
    'body' => json_encode([
      'model' => 'gpt-4o-mini', // Модель GPT-4
      'messages' => [
        [
          'role' => 'system',
          'content' => 'You are an assistant that summarizes content based on given word limits.'
        ],
        [
          'role' => 'user',
          'content' => $prompt,
        ],
      ],
      'max_tokens' => $max_tokens,
      'temperature' => 0.7,
    ]),
    'timeout' => 30,
  ]);

  // Обработка ошибок API
  if (is_wp_error($response)) {
      return new WP_Error('api_error', 'Ошибка подключения к OpenAI', ['status' => 500]);
  }

  // Декодирование ответа
  $body = json_decode(wp_remote_retrieve_body($response), true);

  // Проверка наличия данных
  if (empty($body['choices']) || !isset($body['choices'][0]['message']['content'])) {
      return new WP_Error('response_error', 'Некорректный ответ от OpenAI', ['status' => 500]);
  }

  // Извлечение сгенерированной сводки
  $summary = trim($body['choices'][0]['message']['content']);

  // Проверка причины завершения генерации
  // $finish_reason = $body['choices'][0]['finish_reason'] ?? '';
  // if ($finish_reason !== 'stop') {
  //     return new WP_Error('response_incomplete', 'Ответ от OpenAI был неполным', ['status' => 500]);
  // }

  // Возвращаем итоговую сводку, обернув в теги <p>
  return ['summary' => wrap_content_in_paragraphs($summary)];
}

/**
* Функция для оборачивания контента в теги <p>.
*/
function wrap_content_in_paragraphs($content) {
  // Разделяем текст на строки, предполагая, что строки разделены символом новой строки (\n)
  $lines = explode("\n", trim($content));

  // Очищаем пустые строки и оборачиваем каждую строку в <p>
  $wrapped_lines = array_map(function ($line) {
      return '<p>' . esc_html(trim($line)) . '</p>';
  }, array_filter($lines));

  // Возвращаем объединенные строки
  return implode("\n", $wrapped_lines);
}

/**
* Функция для формирования промпта в зависимости от языка.
*/
function generate_prompt($text, $word_limit, $language) {
  // Промпты для разных языков
  $prompts = [
      'en' => "Summarize the following article in no more than {$word_limit} words. Focus on the main ideas and keep it concise:\n\n{$text}",
      'uk' => "Складіть короткий виклад наступної статті обсягом не більше {$word_limit} слів. Зосередьтеся на основних ідеях і зробіть текст лаконічним:\n\n{$text}",
      'ru' => "Сделайте краткое изложение следующей статьи объемом не более {$word_limit} слов. Сосредоточьтесь на основных идеях и сделайте текст лаконичным:\n\n{$text}",
      'es' => "Resume el siguiente artículo en no más de {$word_limit} palabras. Concéntrate en las ideas principales y sé conciso:\n\n{$text}",
  ];

  // Возвращаем промпт для заданного языка или английский по умолчанию
  return $prompts[$language] ?? $prompts['en'];
}
