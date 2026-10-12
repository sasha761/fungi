import {initProductRowSlider, initProductGallerySlider} from '../modules/swiper.js';
import lightboxModule from '../modules/lightbox.js';
import {initRatingModule} from '../modules/rating-stars.js';
import initValidation from '../utils/initValidation.js';


export default async function productPage() {
  initProductRowSlider();
  initProductGallerySlider();

  initRatingModule({
    reviewRatingRequired: true,
    requiredRatingText: window.ajax?.labels?.ratingRequired || 'Please select a rating!',
  });

  const formSelector = document.querySelector('.js-comment-form');

  await initValidation({
    formSelector: formSelector,
    validateFields: {
      'name': '.c-comment-form [name="author"]',
      'email': '.c-comment-form [name="email"]',
      'text': '.c-comment-form [name="comment"]',
      'submit': '.c-comment-form button[name="comment_submit"]',
    }
  });

  new lightboxModule('.js-lightbox', '.js-lightbox-modal');
}
