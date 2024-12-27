<?php
/* Template Name: faq page
 * Template Post Type: page
 */


$context = Timber::context();
$context['post'] = Timber::get_post();
$context['questions'] = get_field('questions');

$template = array( 'template-faq.twig' );
Timber::render($template, $context);