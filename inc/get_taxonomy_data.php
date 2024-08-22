<?php
function get_taxonomy_data($args = []) {
  $defaults = [
    'taxonomy'   => 'category',
    'hide_empty' => true 
  ];

  $parsed_args = wp_parse_args( $args, $defaults );
  $categories = get_terms($parsed_args);


  $localized_categories = [];
  if (!empty($categories) && !is_wp_error($categories)) {
    foreach ($categories as $category) {
      $translated_category_id = apply_filters('wpml_object_id', $category->term_id, $parsed_args['taxonomy'], true);
      $translated_category = get_term($translated_category_id, $parsed_args['taxonomy']);

      if ($translated_category) {
        $taxonomy_icon = get_field('tag_icon', $parsed_args['taxonomy'] . '_' . $translated_category->term_id);
        $localized_categories[] = [
          'name'  => $translated_category->name,
          'slug' => $translated_category->slug,
          'link'  => get_term_link($translated_category),
          'img' => $taxonomy_icon,
          'count' => $translated_category->count
        ];
      }
    }
  }

  return $localized_categories;
}
