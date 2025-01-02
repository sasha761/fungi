<?php
/**
 * Получает данные изображения (URL, ширина, высота) по ID миниатюры и размеру.
 *
 * @param int $thumbnail_id ID миниатюры изображения.
 * @param string|array $size Размер изображения (строка или массив).
 * @return array|null Ассоциативный массив с 'url', 'width' и 'height' или null, если изображение не найдено.
 */
function get_image_data($thumbnail_id, $size = 'full') {
  if (!$thumbnail_id) return null;
  
  $image_data = wp_get_attachment_image_src($thumbnail_id, $size);
  if ($image_data) {
    return [
      'url'    => $image_data[0],
      'width'  => $image_data[1],
      'height' => $image_data[2],
    ];
  }

  return null;
}
