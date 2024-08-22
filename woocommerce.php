<?php

if ( is_singular( 'product' ) ) {
  include_once('woocommerce/product.php');  
} 
if (is_singular('cart')) {
  include_once('woocommerce/cart.php');
  
} elseif (is_search()) {
  include_once('woocommerce/search.php');
  
} else {
  include_once('woocommerce/archive.php');
}
