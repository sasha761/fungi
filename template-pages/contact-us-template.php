<?php
/* Template Name: contact us
 * Template Post Type: page
 */


$context = Timber::context();
$context['post'] = Timber::get_post();


$template = array( 'template-contact-us.twig' );
Timber::render($template, $context);