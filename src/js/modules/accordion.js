import {slideUp, slideDown} from './slideToggle.js';

export default class Accordion {
	constructor(block, container) {
		this.mainContainer = document.querySelector(container);
		this.blocks        = document.querySelectorAll(block);
		// this.init();
	}

	init() {
		this.blocks.forEach(item => {
			const head = item.querySelector('.js-accordion__heading');
			const list = item.querySelector('.js-accordion__list');

			if (item.classList.contains('is-active')) {
				slideDown(list, 0);
			} else {
				slideUp(list);
			}

			head.addEventListener('click', (event) => {
				event.preventDefault();

	  		if (item.classList.contains('is-active')) {
		        item.classList.remove('is-active');
		        slideUp(list);
		    } else {
		    	this.close();
		    	this.open(item);
		    }
			});
		});
	}

	close(blocks = this.blocks) {
		blocks.forEach(item => {
      if (item.classList.contains('is-active')) {
        item.classList.remove('is-active');
        slideUp(item.querySelector('.js-accordion__list'));
      }
    });
	}

	open(block) {
    block.classList.add('is-active');
    slideDown(block.querySelector('.js-accordion__list'));
	}
}