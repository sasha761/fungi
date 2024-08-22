<?php
$context = Timber::context();

$context['title'] = get_search_query();
$context['posts'] = Timber::get_posts();

$context['query_object'] = get_queried_object();

Timber::render( 'templates/search.twig', $context );