<?php
/* Template Name: reviews page
 * Template Post Type: page,
 */
$context = Timber::context();

$context['post'] = Timber::get_post();
$args = array(
  'post_type' => 'product', 
  'status' => "approve", 
);
$context['reviews'] = get_comments($args);

Timber::render( array( 'templates/reviews-template.twig' ), $context );