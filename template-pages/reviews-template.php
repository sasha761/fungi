<?php
/* Template Name: reviews page
 * Template Post Type: page
 */

 global $sitepress;
 $context = Timber::context();
 $context['post'] = Timber::get_post();
 
 // Отключаем языковой фильтр WPML
 remove_filter('comments_clauses', [ $sitepress, 'comments_clauses' ], 10);
 
 $args = [
     'post_type'    => 'product',
     'status'       => 'approve',
     'comment_type' => 'review',
     'number'       => 20,
 ];
 
 $reviews = get_comments($args);
 $total_rating = 0;
 $reviews_count = 0;
 $all_reviews = [];
 
 // Определяем текущий язык сайта
 $current_lang = apply_filters('wpml_current_language', null);
 
 foreach ($reviews as $review) {
     $rating = get_comment_meta($review->comment_ID, 'rating', true);
     $product_id = $review->comment_post_ID;
     $product_title = get_the_title($product_id);
     $product_link = get_permalink($product_id);
 
     // Получаем переводы товара в WPML
     $translated_product_id = apply_filters('wpml_object_id', $product_id, 'product', false, $current_lang);
     if ($translated_product_id) {
         $product_title = get_the_title($translated_product_id);
         $product_link = get_permalink($translated_product_id);
     }
 
     if (!$rating) {
         continue;
     }
 
     $total_rating += $rating;
     $reviews_count++;
 
     // Добавляем данные в Timber
     $review->rating = $rating;
     $review->product_title = $product_title;
     $review->product_link = $product_link;
 
     // Добавляем в JSON-LD микроразметку
     $all_reviews[] = [
         "@type" => "Review",
         "author" => [
             "@type" => "Person",
             "name" => $review->comment_author
         ],
         "datePublished" => $review->comment_date,
         "reviewBody" => $review->comment_content,
         "reviewRating" => [
             "@type" => "Rating",
             "ratingValue" => $rating,
             "bestRating" => "5"
         ],
         "itemReviewed" => [
             "@type" => "Product",
             "name" => $product_title,
             "url" => $product_link
         ]
     ];
 }
 
 // Добавляем микроразметку JSON-LD, если есть отзывы
 if ($reviews_count > 0) {
     $context['json_ld'] = json_encode([
         "@context" => "https://schema.org",
         "@type" => "Product",
         "name" => "Отзывы о товарах",
         "aggregateRating" => [
             "@type" => "AggregateRating",
             "ratingValue" => round($total_rating / $reviews_count, 1),
             "reviewCount" => $reviews_count,
             "bestRating" => "5"
         ],
         "review" => $all_reviews
     ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
 }
 
 $context['reviews'] = $reviews;
 
 // Восстанавливаем фильтр WPML
 add_filter('comments_clauses', [ $sitepress, 'comments_clauses' ], 10, 2);
 
 // Рендерим шаблон
 Timber::render('templates/reviews-template.twig', $context);