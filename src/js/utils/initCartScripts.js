import {submitFormData} from '../modules/sendFormData.js';

import niceSelect from '../modules/niceSelect.js';
// import initPhone from '../utils/initPhone.js';
import initValidation from '../utils/initValidation.js';

export default async function initCartScripts() {

  console.log('initCartScripts');
  new niceSelect('select[name="contacts_client_messenger"]');

  // const itiPhone = initPhone('.js-quick-buy-form input[name="phone"]');
  // const itiPhone = await initPhone('.js-quick-buy-form input[name="phone"]');

  const formSelector = document.querySelector('.js-quick-buy-form');
  const validationCart = await initValidation({
    formSelector: formSelector,
    itiPhone: itiPhone,
    validateFields: {
      'name': '.js-quick-buy-form [name="name"]',
      'email': '.js-quick-buy-form [name="email"]',
      'phone': '.js-quick-buy-form input[name="phone"]',
      'submit': '.js-quick-buy-form .js-submit-btn',
    }
  });

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
