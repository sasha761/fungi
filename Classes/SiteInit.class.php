<?php
use Timber\ImageHelper;
use Timber\Menu;
use Timber\Site;
use Timber\URLHelper;
use Twig\Extension\StringLoaderExtension;
use Twig\TwigFilter;

/**
 * Start Timber Support
 */
class BrandedSite extends Site {
  /**
   * Add timber support.
   */
  public function __construct() {
    parent::__construct();
    add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    add_action( 'widgets_init', array( $this, 'widgets_init' ) );
    add_filter( 'timber/context', array( $this, 'add_to_context' ) );
    add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
    add_action( 'admin_head',  array( $this, 'admin_style') );

    add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
    add_filter( 'use_widgets_block_editor', '__return_false' );
    add_filter( 'auto_update_plugin', '__return_false' );
  }

  public function theme_supports() {
    load_theme_textdomain( 'news', get_template_directory() . '/languages' );
    load_theme_textdomain( 'wpml_theme', get_template_directory() . '/languages' );

    add_theme_support( 'woocommerce' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'menus' );

    register_nav_menus(
      array(
        'menu-1' => esc_html__( 'Primary', 'news' ),
        'menu-2' => esc_html__( 'footer', 'news' ),
        'menu-3' => esc_html__( 'mobile', 'news' ),
      )
    );
  }

  public function enqueue_scripts() {
    wp_enqueue_style( 'theme-style', get_stylesheet_uri(), array(), _S_VERSION );
    wp_enqueue_style( 'main-style', get_template_directory_uri() . '/dist/css/main.css', array(), _S_VERSION );

    wp_enqueue_script( 'pure-js', get_template_directory_uri() . '/dist/js/index.js', array(), _S_VERSION, true );
    wp_localize_script( 'pure-js', 'ajax', array(
      'url' => admin_url('admin-ajax.php'),
      'nonce' => wp_create_nonce('likes_nonce')
    ));
    // if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
    //   wp_enqueue_script( 'comment-reply' );
    // }
  }

  public function add_to_context( $context ) {
    ob_start();
    $context['menu']               = Timber::get_menu( 'menu-1' );
    $context['footer_menu']        = Timber::get_menu( 'menu-2' );
    $context['mobile_menu']        = Timber::get_menu( 'menu-3' );
    $context['currency']           = get_woocommerce_currency_symbol();
    $context['is_mobile']          = wp_is_mobile();
    $context['minicart']           = get_cart_info();
    $context['options']            = get_fields('option');
    
    if ( function_exists( 'icl_get_languages' ) ) {
      $context['current_lang']      = ICL_LANGUAGE_CODE;
      $context['language_code']     = icl_get_languages();
    }
    
    if ( function_exists( 'yoast_breadcrumb' ) ) {
      $context['breadcrumbs'] = yoast_breadcrumb('<nav class="c-breadcrumbs">','</nav>', false );
    }

    return $context;
  }

  public function admin_style(){
    wp_enqueue_style( 'style-admin', get_template_directory_uri() . '/admin-style.css', array(), _S_VERSION );
  }

  public function towebphq($src) {
    return ImageHelper::img_to_webp($src, 100, true);
  }

  public function add_to_twig( $twig ) {
    $twig->addExtension( new StringLoaderExtension() );

    $twig->addFilter( new TwigFilter( 'translateString', array( $this, 'translateString' ) ) );
    $twig->addFilter( new TwigFilter( 'is_current_url', function ($link) {
        return (URLHelper::get_current_url() == $link) ? true : false;
    }));
    $twig->addFilter( new TwigFilter('towebphq', [$this, 'towebphq']) );
    $twig->addFilter( new TwigFilter('tojpghq', [$this, 'tojpghq']) );

    return $twig;
  }
  
  public function translateString( $string, $name ) {
    return apply_filters( 'wpml_translate_single_string', $string, 'fungi', $name );
  }

  // public function mini_cart() {
  //   $data['cart_products'] = [];
  //   $data['count'] = WC()->cart->cart_contents_count;

  //   foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
  //     $products_array = [];

  //     // General vars
  //     $_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
  //     $product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

  //     if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {

  //       // price, title, image, url
  //       $products_array['title']     = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
  //       $products_array['price']     = $_product->get_price_html();
  //       $products_array['id']        = $product_id;
  //       $products_array['url']       = apply_filters( 'woocommerce_cart_item_permalink', $_product->get_permalink( $cart_item ) , $cart_item, $cart_item_key );
  //       $products_array['thumbnail'] = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );


  //       // Delete button
  //       $products_array['delete_permalink'] = wc_get_cart_remove_url( $cart_item_key );
  //       $products_array['delete_productid'] = esc_attr($product_id);
  //       $products_array['delete_sku'] = esc_attr($_product->get_sku());
  //       $products_array['cart_item_key'] = $cart_item_key;

  //       $products_array['quantity'] = $cart_item['quantity'];

  //       // Merge with products
  //       $data['cart_products'][] = $products_array;

  //     }
  //   }

  //   $data['total'] = WC()->cart->get_total();
  //   return $data;
  // }
}

new BrandedSite();