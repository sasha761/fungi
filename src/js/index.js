import '../scss/main.scss';

import Accordion    from './modules/accordion.js';
import modal        from './modules/modal-container.js';
import tabs         from './modules/tabs.js';
import likes        from './modules/likes.js';
import burgerMenu   from './modules/burger.js';
import Cart         from './modules/cart.js';
import inputBlock   from './modules/input-block';
import headerSticky from './modules/header.js';

import {submitFormData, sendAjax} from './modules/sendFormData.js';

import LazyLoad     from 'vanilla-lazyload';
import { initializeSummarizeButtons } from './modules/summarizeButtons.js';

let modulesLoaded = false;

document.addEventListener('cartUpdated', (e) => {
  const { count } = e.detail;
  if (count > 0 && !modulesLoaded) {
    initCartScripts();
  }
});

document.addEventListener("DOMContentLoaded", () => {
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
    initCartScripts();
  }

  new modal('.c-modal', '.l-modal-container');
  new tabs('.js-tab-product-additional-info');
  
  burgerMenu();
  inputBlock();

  const accordion = new Accordion('.js-accordion__item', '.js-accordion');


  const cart = new Cart({
    addToCartBtn: '.js-add-to-cart',
    removeFromCartBtn: '.js-product-remove',
    cartContainer: '.js-cart-container',
    miniCartCounter: '.js-mini-cart-counter'
  });

  cart.addToCartHandler();
  cart.removeFromCartHandler();
  cart.quantityCartHandler();

  const pageClass = document.querySelector('main').classList.value;
  switch (pageClass) {
    case 'p-main': {
      homePage();
      break;
    }
    case 'p-shop': {
     
      break;
    }  
    case 'p-product': {
      productPage();
      break;
    }
    case 'p-checkout':
      break;   
    case 'p-shop p-search':
      break;  
    case 'p-faq':
      accordion.init(); 
      break;   
    case 'p-contact-us': {
      contactUsPage();
      break;
    }
    case 'p-page is-page':
      break;   
    case 'p-single': {
      singlePage();
      break;  
    } 
    case 'p-reviews':
      break; 
    default:
      break;
  }
});

async function singlePage() {
  const formSelector = document.querySelector('.c-comment-form');
  const swiperModule = await import('./modules/swiper.js');

  const formValidator = await initValidation({
    formSelector: ".c-comment-form",
    validateFields: {
      'name': '.c-comment-form [name="author"]',
      'email': '.c-comment-form [name="email"]',
      'submit': '.c-comment-form button[name="comment_submit"]',
    }
  });

  submitFormData({
    formElement: formSelector,
    formValidation: formValidator,
    onSuccess: () => { 
      document.querySelector('[data-modal="#successful"]').click();
      formSelector.reset();
    },
    onError: () => {
      document.querySelector('[data-modal="#error"]').click();
    }
  });

  swiperModule.initProductRowSlider();
  initializeSummarizeButtons();
  likes();
}

async function contactUsPage() {
  const formSelector = document.querySelector('.js-contact-us-form');
  const FileDropZone = await import('./modules/fileDropZone.js');
  const itiPhone = await initPhone('.js-contact-us-form input[name="phone"]');
  
  const formValidator = await initValidation({
    formSelector: ".js-contact-us-form",
    itiPhone: itiPhone,
    validateFields: {
      'name': '.js-contact-us-form input[name="name"]',
      'email': '.js-contact-us-form input[name="email"]',
      'phone': '.js-contact-us-form input[name="phone"]',
      'file': '.js-contact-us-form input.js-drag-and-drop-input',
      'submit': '.js-contact-us-form .js-submit-btn',
    }
  });
  
  const formFiles = new FileDropZone.default({
    dropArea: ".js-drag-and-drop",
    fileInput: ".js-drag-and-drop-input",
    previewContainer: "#file-preview-container",
    activeClass: "is-active",
    maxFileSizeMB: 10,
    allowedFileTypes: [
      'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // doc, docx
      'text/xml', // xml
      'image/jpeg', 'image/png', 'image/webp', 'image/heic', // jpg, jpeg, png, webp, heic
      'video/quicktime', 'video/mp4' // mov, mp4
    ]
  });

  submitFormData({
    formElement: formSelector,
    formValidation: formValidator,
    onSuccess: () => { 
      document.querySelector('[data-modal="#successful"]').click();
      formSelector.reset();
      formFiles.removeAllFiles();
    },
    onError: () => {
      document.querySelector('[data-modal="#error"]').click();
    }
  });
}

async function productPage() {
  const [
    swiperModule, 
    lightboxModule,
    ratingModule,
    validation
  ] = await Promise.all([
    import('./modules/swiper.js'),
    import('./modules/lightbox.js'),
    import('./modules/rating-stars.js'),
    import('./modules/validation.js')
  ]);
  swiperModule.initProductRowSlider();
  swiperModule.initProductGallerySlider();
  ratingModule.initRatingModule({
    reviewRatingRequired: true,
    requiredRatingText: 'Не забудьте выбрать оценку!',
  });

  const formValidator = new validation.default();

  if (document.querySelector(".c-comment-form")) {
    formValidator.validate('firstName', '.c-comment-form [name="author"]');
    formValidator.validate('email', '.c-comment-form [name="email"]');
    // formValidator.validate('text', '.c-comment-form [name="comment"]');
    formValidator.validate('submit', '.c-comment-form button[name="comment_submit"]');
  }

  new lightboxModule.default('.js-lightbox', '.js-lightbox-modal');
}

async function homePage() {
  const swiperModule = await import('./modules/swiper.js');
  swiperModule.initblogCategorySlider();
  swiperModule.initProductRowSlider();
}

async function initCartScripts() {
  modulesLoaded = true;

  const { default: niceSelect } = await import('./modules/niceSelect.js');
  new niceSelect('select[name="contacts_client_messenger"]');

  const itiPhone = await initPhone('.js-quick-buy-form input[name="phone"]');
  const validationCart = await initValidation({
    formSelector: ".js-quick-buy-form",
    itiPhone: itiPhone,
    validateFields: {
      'name': '.js-quick-buy-form [name="name"]',
      'email': '.js-quick-buy-form [name="email"]',
      'phone': '.js-quick-buy-form input[name="phone"]',
      'text': '.js-quick-buy-form [name="contactInfo"]',
      'submit': '.js-quick-buy-form .js-submit-btn',
    }
  });

  const formSelector = document.querySelector('.js-quick-buy-form');
  submitFormData({
    formElement: formSelector,
    formValidation: validationCart,
    onSuccess: () => { 
      document.querySelector('[data-modal="#successful"]').click();
      formSelector.reset();
    },
    onError: () => {
      document.querySelector('[data-modal="#error"]').click();
    },
    onBeforeSubmit: (formData) => {
      const productElements = document.querySelectorAll('.js-quick-buy-form .c-mini-cart-product');
      productElements.forEach((product, index) => {
        formData.append(`products[${index}][id]`, product.dataset.id);
        formData.append(`products[${index}][quantity]`, product.dataset.quantity);
        formData.append(`products[${index}][name]`, product.dataset.name);
        formData.append(`products[${index}][link]`, product.dataset.link);
      });
    }
  });
}

async function initPhone(phoneSelector) {
  await import('intl-tel-input/build/css/intlTelInput.css');
  const { default: intlTelInput } = await import('intl-tel-input');
  const utils = await import("intl-tel-input/build/js/utils");

  const input = document.querySelector(phoneSelector);
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

  return iti;
}

async function initValidation({ formSelector, itiPhone = null, validateFields }) {
  const form = document.querySelector(formSelector);
  if (!form) return;

  const { default: Validation } = await import('./modules/validation.js');
  const formValidator = new Validation();
  
  for (const [fieldType, selector] of Object.entries(validateFields)) {
    if (itiPhone && fieldType === 'phone') {
      formValidator.validate(fieldType, selector, itiPhone);
    }
    formValidator.validate(fieldType, selector);
  }

  return formValidator.getResults();
}