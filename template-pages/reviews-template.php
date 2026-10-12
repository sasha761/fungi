<?php
/* Template Name: reviews page
 * Template Post Type: page
 */

 $context = Timber::context();
 $context['post'] = Timber::get_post();

 $product_ids = get_posts([
     'post_type'        => 'product',
     'post_status'      => 'publish',
     'posts_per_page'   => -1,
     'fields'           => 'ids',
     'suppress_filters' => false,
 ]);

 $args = [
     'status'       => 'approve',
     'comment_type' => 'review',
     'number'       => 20,
     'post__in'     => $product_ids,
 ];

 $reviews = $product_ids ? get_comments($args) : [];
 $total_rating = 0;
 $reviews_count = 0;
 $all_reviews = [];
 
 foreach ($reviews as $review) {
     $rating = get_comment_meta($review->comment_ID, 'rating', true);
     $product_id = $review->comment_post_ID;
     $product_title = get_the_title($product_id);
     $product_link = get_permalink($product_id);
 
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
         "name" => news_translate_string('Product reviews', 'Reviews - schema name'),
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
 
 // Рендерим шаблон
 Timber::render('templates/reviews-template.twig', $context);
