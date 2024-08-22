<?php
$context = Timber::context();
$timber_post = Timber::query_post();
$context['post'] = $timber_post;

Timber::render( 'templates/woo/cart.twig', $context );