<?php
if ( is_singular( 'product' ) ) {
  include_once('woocommerce/product.php');  
} 
elseif (is_singular('cart')) {
  include_once('woocommerce/cart.php');
} 
elseif (is_search()) {
  include_once('woocommerce/search.php');
} 
elseif (is_archive() || is_category() || is_tag()) {
  include_once('woocommerce/archive.php');
}
