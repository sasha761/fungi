import lightbox     from './modules/lightbox.js';
import modal        from './modules/modal-container.js';
import tabs         from './modules/tabs.js';
// import niceSelect   from './modules/niceSelect.js';
import validation   from './modules/validation.js';
import likes        from './modules/likes.js';
// import readMore     from './modules/read-more.js';
import swiperFn     from './modules/swiper.js';
// import menu         from './modules/menu.js';
// import loadMore     from './modules/load-more.js';
import quickBuy     from './modules/quick-buy.js';
import burgerMenu   from './modules/burger.js';
import updateCart   from './modules/cart.js';
import Cart         from './modules/quick-add-to-cart';
import LazyLoad     from 'vanilla-lazyload';
// import NiceSelect   from 'nice-select2';

// import {Accordion, CheckedAccordion}    from './modules/accordion.js';


document.addEventListener("DOMContentLoaded", function(event) {
  window.lazyLoadInstance = new LazyLoad({
    cancel_on_exit: false,
    threshold: 150,
    unobserve_entered: true
  });
  
  new modal('.c-modal', '.l-modal-container');
  // new tabs('.js-tab-mobile-menu');
  new tabs('.js-tab-product-additional-info');
  
  // new CheckedAccordion('.js-accordion__item', '.js-accordion');
  burgerMenu();
  // menu();

  
  const from_validator = new validation();

  const cart = new Cart({
    addToCartBtn: '.js-add-to-cart',
    removeFromCartBtn: '.js-product-remove',
    cartContainer: '.js-cart-container',
    miniCartCounter: '.js-mini-cart-counter'
  });

  cart.addToCartHandler();
  cart.removeFromCartHandler();

  if (document.querySelector("form[name='quick-buy']")) {
    from_validator.validate('firstName', '[name="name"]')
    from_validator.validate('email', '[name="email"]')
    from_validator.validate('text', '[name="contactInfo"]')
    from_validator.validate('submit', 'button[name="quick-buy-submit"]')
  }

  quickBuy();

  // new QuickBuyForm('.js-quick-buy-form');


  const pageClass = document.querySelector('main').classList.value;
  

  switch (pageClass) {
    case 'p-main':
      swiperFn();
      break;
    case 'p-shop':
      // new NiceSelect(document.querySelector(".js-filter-sort select"), {searchable: false, placeholder: 'Sorting'});
      // readMore();
      // loadMore();      
      break;  
    case 'p-product':
      swiperFn();
      new lightbox('.js-lightbox', '.js-lightbox-modal');
      
      break;
    case 'p-checkout':
      // if (document.querySelector(".js-select")) {
      //   new NiceSelect(document.querySelector(".js-select"), {searchable: true, placeholder: 'Country/Region'});
      // }
      break;   
    case 'p-shop p-search':
      new niceSelect('.js-filter-sort select');
      break;
    case 'p-cart':
      updateCart();
      break;
    case 'p-thank':
      break;       
    case 'p-page is-page':
      break;   
    case 'p-single':
      swiperFn();
      likes();
      break;  
    case 'p-reviews':
      break; 
    default:
      break;
  }
});