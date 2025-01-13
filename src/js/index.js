import updateCart   from './modules/cart.js';
import modal        from './modules/modal-container.js';
import tabs         from './modules/tabs.js';
import likes        from './modules/likes.js';
import quickBuy     from './modules/quick-buy.js';
import burgerMenu   from './modules/burger.js';
import Cart         from './modules/quick-add-to-cart';
import inputBlock   from './modules/input-block';
import niceSelect   from './modules/niceSelect.js';
import headerSticky from './modules/header.js';

import LazyLoad     from 'vanilla-lazyload';

// import {Accordion, CheckedAccordion}    from './modules/accordion.js';


let modulesLoaded = false;

document.addEventListener('cartUpdated', (e) => {
  const { count } = e.detail;
  if (count > 0 && !modulesLoaded) {
    modulesLoaded = true;
    initPhoneAndValidation(); 
  }
});

document.addEventListener("DOMContentLoaded", async () => {
  window.lazyLoadInstance = new LazyLoad({
    cancel_on_exit: false,
    threshold: 150,
    unobserve_entered: true
  });

  headerSticky({
    threshold: 35,
    hiddenClass: 'scrolled-down'
  })

  
  if (ajax.cartCount > 0) {
    modulesLoaded = true;
    initPhoneAndValidation();
  }

  new niceSelect('select[name="contacts_client_messenger"]');
  
  new modal('.c-modal', '.l-modal-container');
  new tabs('.js-tab-product-additional-info');
  
  burgerMenu();
  inputBlock();


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
    case 'p-main': {
      const swiperModule = await import('./modules/swiper.js');
      swiperModule.initblogCategorySlider();
      swiperModule.initProductRowSlider();
      break;
    }
    case 'p-shop': {
     
      break;
    }  
    case 'p-product': {
      const [
        swiperModule, 
        lightboxModule,
        ratingModule
      ] = await Promise.all([
        import('./modules/swiper.js'),
        import('./modules/lightbox.js'),
        import('./modules/rating-stars.js')
      ]);
      swiperModule.initProductRowSlider();
      swiperModule.initProductGallerySlider();
      ratingModule.initRatingModule({
        reviewRatingRequired: true,
        requiredRatingText: 'Не забудьте выбрать оценку!',
      });

      new lightboxModule.default('.js-lightbox', '.js-lightbox-modal');
      break;
    }
    case 'p-checkout':

      break;   
    case 'p-shop p-search':
      break;
    case 'p-cart':
      updateCart();
      break;
    case 'p-thank':
      break;       
    case 'p-page is-page':
      break;   
    case 'p-single':
      const swiperModule = await import('./modules/swiper.js');

      swiperModule.initProductRowSlider();
      likes();
      break;  
    case 'p-reviews':
      break; 
    default:
      break;
  }
});

async function initPhoneAndValidation() {
  await import('intl-tel-input/build/css/intlTelInput.css');
  const intlTelInput = await import('intl-tel-input');
  const { default: validation } = await import('./modules/validation.js');
  const utils = await import("intl-tel-input/build/js/utils");

  const input = document.querySelector(".js-input-block #phone");
  if (!input) return;

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
    utilsScript: utils
  });

  const formValidator = new validation();

  if (document.querySelector("form[name='quick-buy']")) {
    formValidator.validate('firstName', '.js-input-block [name="name"]');
    formValidator.validate('email', '.js-input-block [name="email"]');
    formValidator.validate('phone', iti);
    formValidator.validate('text', '.js-input-block [name="contactInfo"]');
    formValidator.validate('submit', 'button[name="quick-buy-submit"]');
  }
}