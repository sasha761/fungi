import lightbox     from './modules/lightbox.js';
import modal        from './modules/modal-container.js';
import tabs         from './modules/tabs.js';
import validation   from './modules/validation.js';
import likes        from './modules/likes.js';
import swiperFn     from './modules/swiper.js';
import quickBuy     from './modules/quick-buy.js';
import burgerMenu   from './modules/burger.js';
import updateCart   from './modules/cart.js';
import Cart         from './modules/quick-add-to-cart';
import inputBlock   from './modules/input-block';

import LazyLoad     from 'vanilla-lazyload';
import intlTelInput from 'intl-tel-input';

import niceSelect   from './modules/niceSelect.js';
// import {Accordion, CheckedAccordion}    from './modules/accordion.js';


document.addEventListener("DOMContentLoaded", () => {
  window.lazyLoadInstance = new LazyLoad({
    cancel_on_exit: false,
    threshold: 150,
    unobserve_entered: true
  });

  new niceSelect('select[name="contacts_client"]');
  
  new modal('.c-modal', '.l-modal-container');
  new tabs('.js-tab-product-additional-info');
  
  burgerMenu();

  inputBlock();

  const input = document.querySelector(".js-input-block #phone");
  const iti = intlTelInput(input, {
    initialCountry: "auto",
    onlyCountries: [
      "al", "ad", "at", "by", "be", "bg", "hr", "cz", "dk", "ee", "fi", "fr",
      "de", "gi", "gr", "va", "hu", "is", "ie", "it", "lv", "li", "lt", "lu", "mk",
      "mt", "md", "mc", "me", "nl", "no", "pl", "pt", "ro", "sm", "rs", "sk", "si",
      "es", "se", "ch", "ua", "gb", "ge", "az", "kz", "tr", "us", "ca", "br", 
      "za", "eg", "ng", "ke", "gh", "ma", "dz", "tn"
    ],
    preferredCountries: ['ua', 'us', 'ca', 'gb', 'pl', 'de', 'no'],
    geoIpLookup: callback => {
      fetch("https://ipapi.co/json")
        .then(res => res.json())
        .then(data => callback(data.country_code))
        .catch(() => callback("us"));
    },
    // loadUtils: () => import("intl-tel-input/build/js/utils"),
    utilsScript: import("intl-tel-input/build/js/utils")
  });

  const from_validator = new validation();

  if (document.querySelector("form[name='quick-buy']")) {
    from_validator.validate('firstName', '.js-input-block [name="name"]')
    from_validator.validate('email', '.js-input-block [name="email"]')
    from_validator.validate('phone', iti)
    from_validator.validate('text', '.js-input-block [name="contactInfo"]')
    from_validator.validate('submit', 'button[name="quick-buy-submit"]')
  }

  quickBuy();


  const cart = new Cart({
    addToCartBtn: '.js-add-to-cart',
    removeFromCartBtn: '.js-product-remove',
    cartContainer: '.js-cart-container',
    miniCartCounter: '.js-mini-cart-counter'
  });

  cart.addToCartHandler();
  cart.removeFromCartHandler();

  



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
      // new niceSelect('.js-filter-sort select');
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