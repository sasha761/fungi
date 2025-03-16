import '../scss/main.scss';

import Accordion    from './modules/accordion.js';
import modal        from './modules/modal-container.js';
import tabs         from './modules/tabs.js';
import likes        from './modules/likes.js';
// import quickBuy     from './modules/quick-buy.js';
// import sendContactForm     from './modules/sendContactForm.js';
import burgerMenu   from './modules/burger.js';
import Cart         from './modules/cart.js';
import inputBlock   from './modules/input-block';
import headerSticky from './modules/header.js';


import LazyLoad     from 'vanilla-lazyload';
import { initializeSummarizeButtons } from './modules/summarizeButtons.js';

let modulesLoaded = false;

document.addEventListener('cartUpdated', async (e) => {
  const { count } = e.detail;
  if (count > 0 && !modulesLoaded) {
    await initCartScripts();
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
    await initCartScripts();
  }

  new modal('.c-modal', '.l-modal-container');
  new tabs('.js-tab-product-additional-info');
  
  burgerMenu();
  inputBlock();
  // quickBuy();

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
      break;
    }
    case 'p-checkout':

      break;   
    case 'p-shop p-search':
      break;
    case 'p-cart':
      // updateCart();
      break;
    case 'p-thank':
      break;     
    case 'p-faq':
      accordion.init(); 
      break;   

    case 'p-contact-us':
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

      const contactUsForm = document.querySelector('.js-contact-us-form');

      if (!contactUsForm) return;

      contactUsForm.addEventListener("submit", (event) => {
        event.preventDefault();
        console.log('formValidator: ', formValidator);


        const isValid = Object.values(formValidator).every(value => value !== false);
        if (!isValid) return;
        
        const formData = new FormData(contactUsForm);
        ajax(formData);
      });

      const ajax = async (data) => {
        // const cartContainer = document.querySelector('.js-quick-buy-form .js-cart-container');
        // cartContainer.classList.add('is-loading');

        const response = await fetch(`${window.ajax.url}?action=contactForm`, {
          method: 'POST',
          body: data, // Отправка данных в формате FormData
        });

        // cartContainer.classList.remove('is-loading');

        const result = await response.json();
        if (result.success) {
          document.querySelector('[data-modal="#successful"]').click();
          contactUsForm.reset();
          formFiles.removeAllFiles();
        } else {
          document.querySelector('[data-modal="#error"]').click();
        }
      };

      // sendContactForm();
      break;
    case 'p-page is-page':
      break;   
    case 'p-single': {
      const swiperModule = await import('./modules/swiper.js');

      const formValidator = await initValidation({
        formSelector: ".c-comment-form",
        validateFields: {
          'name': '.c-comment-form [name="author"]',
          'email': '.c-comment-form [name="email"]',
          // 'submit': '.c-comment-form button[name="comment_submit"]',
        }
      });

      console.log(formValidator);

      swiperModule.initProductRowSlider();
      initializeSummarizeButtons();
      likes();
      break;  
    } 
    case 'p-reviews':
      break; 
    default:
      break;
  }
});

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


  const quickBuyForm = document.querySelector('.js-quick-buy-form');

  if (!quickBuyForm) return;

  quickBuyForm.addEventListener("submit", (event) => {
    event.preventDefault();
    console.log('validationCart: ', validationCart);


    const isValid = Object.values(validationCart).every(value => value !== false);
    if (!isValid) return;
    
    const formData = new FormData(quickBuyForm);

    // Добавляем список продуктов вручную в formData
    const productElements = quickBuyForm.querySelectorAll('.c-mini-cart-product');
    productElements.forEach((product, index) => {
      formData.append(`products[${index}][id]`, product.dataset.id);
      formData.append(`products[${index}][quantity]`, product.dataset.quantity);
      formData.append(`products[${index}][name]`, product.dataset.name);
      formData.append(`products[${index}][link]`, product.dataset.link);
    });

    ajax(formData);
  });

  const ajax = async (data) => {
    // const cartContainer = document.querySelector('.js-quick-buy-form .js-cart-container');
    // cartContainer.classList.add('is-loading');

    const response = await fetch(`${window.ajax.url}?action=contactForm`, {
      method: 'POST',
      body: data, // Отправка данных в формате FormData
    });

    // cartContainer.classList.remove('is-loading');

    const result = await response.json();
    if (result.success) {
      document.querySelector('[data-modal="#successful"]').click();
      quickBuyForm.reset();
    } else {
      document.querySelector('[data-modal="#error"]').click();
    }
  };
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