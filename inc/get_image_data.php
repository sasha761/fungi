<?php
use Timber\Timber;
use Timber\Image;
use Timber\ImageHelper;

/**
 * Получает данные изображения (WebP и стандартное) по ID миниатюры и размеру.
 *
 * @param int $thumbnail_id ID миниатюры.
 * @param string|array $size Размер изображения.
 * @param bool $generate_webp Создавать WebP-версию (по умолчанию true).
 * @return array|null Ассоциативный массив с 'url', 'webp', 'width', 'height' или null, если изображения нет.
 */
function get_image_data($thumbnail_id, $size = 'full', $generate_webp = true) {
  if (!$thumbnail_id) return null;
  
  $image_data = wp_get_attachment_image_src($thumbnail_id, $size);
  if ($image_data) {

    $timber_image = Timber::get_image($thumbnail_id);

    // Создаём WebP только если $generate_webp === true
    $webp_url = null;
    if ($generate_webp && $timber_image) {
        $webp_url = ImageHelper::img_to_webp($image_data[0], 95);
    }

    return [
      'url'    => $image_data[0],
      'webp'   => $webp_url, 
      'width'  => $image_data[1],
      'height' => $image_data[2],
    ];
  }

  return null;
}
