import {slideUp, slideDown} from './slideToggle.js';

function burgerMenu() {
  const burger = document.querySelector('.js-burger');
  const header = document.querySelector('.l-header');
  const headerMenu = document.querySelector('.l-header__mobile');
  const body = document.body;
  const html = document.documentElement;

  burger.addEventListener('click', () => {
    if (burger.classList.contains('is-open')) {
      burger.classList.remove('is-open');
      header.classList.remove('is-open');
      slideUp(headerMenu);
      body.classList.remove('is-overflow-hidden');
      html.classList.remove('is-overflow-hidden');
    } else {
      burger.classList.add('is-open');
      header.classList.add('is-open');
      slideDown(headerMenu);
      body.classList.add('is-overflow-hidden');
      html.classList.add('is-overflow-hidden');
    }
  });
}

export default burgerMenu;