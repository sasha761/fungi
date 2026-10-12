import {submitFormData} from '../modules/sendFormData.js';

import FileDropZone from '../modules/fileDropZone.js';
// import initPhone from '../utils/initPhone.js';
// import initValidation from '../utils/initValidation.js';

// const FileDropZone = await import('../modules/fileDropZone.js');
// const {default: initPhone} = await import('../utils/initPhone.js');
// const {default: initValidation} = await import('../utils/initValidation.js');

export default async function contactUsPage() {
  const contactUsFormSelector = document.querySelector('.js-contact-us-form');
  const callbackFormSelector = document.querySelector('.js-callback-form');
  // const FileDropZone = await import('../modules/fileDropZone.js');
  const {default: initPhone} = await import('../utils/initPhone.js');
  const {default: initValidation} = await import('../utils/initValidation.js');
  

  const itiPhone = await initPhone('.js-contact-us-form input[name="phone"]');
  const itiPhoneCallback = await initPhone('.js-callback-form input[name="phone"]');
  
  // contact us form
  const contactUsFormValidator = await initValidation({
    formSelector: contactUsFormSelector,
    itiPhone: itiPhone,
    validateFields: {
      'name': '.js-contact-us-form input[name="name"]',
      'email': '.js-contact-us-form input[name="email"]',
      'phone': '.js-contact-us-form input[name="phone"]',
      'file': '.js-contact-us-form input.js-drag-and-drop-input',
      'submit': '.js-contact-us-form .js-submit-btn',
    }
  });
  
  // console.log('itiPhone: ', itiPhone);
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
    formElement: contactUsFormSelector,
    formValidation: contactUsFormValidator,
    onSuccess: () => { 
      document.querySelector('[data-modal="#successful"]').click();
      contactUsFormSelector.reset();
      formFiles.removeAllFiles();
    },
    onError: () => {
      document.querySelector('[data-modal="#error"]').click();
    }
  });

  // callback form
  const callbackFormValidator = await initValidation({
    formSelector: callbackFormSelector,
    itiPhone: itiPhoneCallback,
    validateFields: {
      'phone': '.js-callback-form input[name="phone"]',
      'submit': '.js-callback-form .js-submit-btn',
    }
  });

  submitFormData({
    formElement: callbackFormSelector,
    formValidation: callbackFormValidator,
    onSuccess: () => { 
      document.querySelector('[data-modal="#successful"]').click();
      callbackFormSelector.reset();
    },
    onError: () => {
      document.querySelector('[data-modal="#error"]').click();
    }
  });
}