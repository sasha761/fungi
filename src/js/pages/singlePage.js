import {initProductRowSlider} from '../modules/swiper.js';
import initValidation from '../utils/initValidation.js';

export default async function singlePage() {
  const formSelector = document.querySelector('.js-comment-form');
  // const swiperModule = await import('../modules/swiper.js');

  await initValidation({
    formSelector: formSelector,
    validateFields: {
      'name': '.c-comment-form [name="author"]',
      'email': '.c-comment-form [name="email"]',
      'text': '.c-comment-form [name="comment"]',
      'submit': '.c-comment-form button[name="comment_submit"]',
    }
  });

  initProductRowSlider();
  initializeSummarizeButtons();
  likes();
}
