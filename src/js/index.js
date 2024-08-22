import lightbox     from './modules/lightbox.js';
import modal        from './modules/modal-container.js';
import tabs         from './modules/tabs.js';
import accordion    from './modules/accordion.js';
import niceSelect   from './modules/niceSelect.js';
import validation   from './modules/validation.js';
import likes        from './modules/likes.js';
import readMore     from './modules/read-more.js';
import swiperFn     from './modules/swiper.js';
import menu         from './modules/menu.js';
import loadMore     from './modules/load-more.js';
import quickBuy     from './modules/quick-buy.js';
import burgerMenu   from './modules/burger.js';
import updateCart   from './modules/cart.js';
import MiniCart     from './modules/quick-add-to-cart';
import LazyLoad     from 'vanilla-lazyload';
import NiceSelect   from 'nice-select2';

document.addEventListener("DOMContentLoaded", function(event) {
  window.lazyLoadInstance = new LazyLoad({
    cancel_on_exit: false,
    threshold: 150,
    unobserve_entered: true
  });

  new modal('.c-modal', '.l-modal-container');
  new tabs('.js-tab-mobile-menu');
  new tabs('.js-tab-product-additional-info');
  
  new accordion('.js-accordion__item', '.js-accordion');
  burgerMenu();
  menu();

  // NiceSelect.bind(document.getElementById("seachable-select"), options);
  new NiceSelect(document.querySelector(".js-select"), {searchable: true, placeholder: 'Country/Region'});

  const miniCart = new MiniCart({
    addToCartBtn: '.js-add-to-cart',
    removeFromCartBtn: '.js-product-remove',
    miniCart: '.l-mini-cart',
    miniCartCounter: '.js-mini-cart-counter'
  });

  miniCart.addToCartHandler();
  miniCart.removeFromCartHandler();


  const pageClass = document.querySelector('main').classList.value;
  const from_validator = new validation();

  switch (pageClass) {
    case 'p-main':
      swiperFn();
      break;
    case 'p-shop':
      new niceSelect('.js-filter-select, .js-filter-sort select');
      readMore();
      loadMore();      
      break;  
    case 'p-product':
      swiperFn();
      new lightbox('.js-lightbox', '.js-lightbox-modal');
      if (document.querySelector("form[name='quick-buy']")) {
        from_validator.validate('firstName', '[name="name"]')
        from_validator.validate('lastName', '[name="surname"]')
        from_validator.validate('post', '[name="address"]')
        from_validator.validate('billingCity', '[name="city"]')
        from_validator.validate('phone', '[name="phone"]')
        from_validator.validate('email', '[name="email"]')
        from_validator.validate('submit', 'button[name="quick-buy-submit"]')
      }
      quickBuy();

      break;
    case 'p-checkout':
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