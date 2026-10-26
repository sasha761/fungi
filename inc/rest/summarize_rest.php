<?php

add_action('rest_api_init', function () {
  register_rest_route('custom/v1', '/summarize', [
    'methods' => 'POST',
    'callback' => 'generate_summary',
    // Only the site's own pages may call it, with a per-IP limit: each call is paid (OpenAI).
    'permission_callback' => 'news_summarize_permission',
  ]);
});

/**
 * The request must come from this site (Origin, or Referer when the browser sends no Origin),
 * at most 10 summaries per hour per IP, and the text at most 100 000 characters.
 */
function news_summarize_permission(WP_REST_Request $request) {
  $source = $request->get_header('origin') ?: $request->get_header('referer');
  $host   = strtolower((string) wp_parse_url((string) $source, PHP_URL_HOST));
  if ($host === '' || $host !== strtolower((string) wp_parse_url(home_url(), PHP_URL_HOST))) {
    return new WP_Error('rest_forbidden', 'Forbidden', ['status' => 403]);
  }

  if (strlen((string) $request->get_param('text')) > 100000) {
    return new WP_Error('rest_invalid_param', 'Text is too long', ['status' => 413]);
  }

  $ip    = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
  $key   = 'news_summarize_' . md5($ip);
  $count = (int) get_transient($key);
  if ($count >= 10) {
    return new WP_Error('rest_rate_limited', 'Too many requests, please try again later.', ['status' => 429]);
  }
  set_transient($key, $count + 1, HOUR_IN_SECONDS);

  return true;
}

function generate_summary(WP_REST_Request $request) {
  $api_key = GPT_TOKEN; 
  $text = $request->get_param('text'); 
  $length = $request->get_param('length') ?: 'short'; // short, medium, original
  $language = $request->get_param('lang') ?: 'en'; 

  if ($length === 'original') {
      return ['summary' => wrap_content_in_paragraphs($text)];
  }

  // Установка лимита слов
  $word_limit = $length === 'short' ? 120 : ($length === 'medium' ? 300 : 600);
  $word_to_token_ratio = 1.5; // приблизительно 1.5 токена равно 1 слово
  $max_tokens = ceil($word_limit * $word_to_token_ratio);

  $prompt = generate_prompt($text, $word_limit, $language);

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

  if (is_wp_error($response)) {
      return new WP_Error('api_error', 'Ошибка подключения к OpenAI', ['status' => 500]);
  }

  $body = json_decode(wp_remote_retrieve_body($response), true);

  if (empty($body['choices']) || !isset($body['choices'][0]['message']['content'])) {
      return new WP_Error('response_error', 'Некорректный ответ от OpenAI', ['status' => 500]);
  }

  $summary = trim($body['choices'][0]['message']['content']);

  return ['summary' => wrap_content_in_paragraphs($summary)];
}


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


function generate_prompt($text, $word_limit, $language) {
  // Промпты для разных языков
  $prompts = [
      'en' => "Summarize the following article in no more than {$word_limit} words. Focus on the main ideas and keep it concise:\n\n{$text}",
      'uk' => "Складіть короткий виклад наступної статті обсягом не більше {$word_limit} слів. Зосередьтеся на основних ідеях і зробіть текст лаконічним:\n\n{$text}",
      'ru' => "Сделайте краткое изложение следующей статьи объемом не более {$word_limit} слов. Сосредоточьтесь на основных идеях и сделайте текст лаконичным:\n\n{$text}",
      'es' => "Resume el siguiente artículo en no más de {$word_limit} palabras. Concéntrate en las ideas principales y sé conciso:\n\n{$text}",
  ];

  return $prompts[$language] ?? $prompts['en'];
}
