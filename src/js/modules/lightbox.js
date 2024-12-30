import Swiper from 'swiper';
import { Navigation, Pagination, Zoom} from 'swiper/modules';

class lightbox {
	swiper;
	switcher = true;
	constructor(selector, modalContainer) {
		this.el = selector;
		this.modal = modalContainer;
		this.init();
	}

	init() {
			let images = document.querySelectorAll(this.el);
			let hrefArray = [];

			images.forEach((image, index) => {
				let href = image.getAttribute("href");
				hrefArray.push(href);
				
				image.addEventListener("click", (event) => { 
					event.preventDefault();
					if (this.switcher == true) {
						this.createModal(hrefArray);
					}
					this.switcher = false;

					this.openModal(index);
			 	}, false);

			});
		}

		initSlider() {
			const swiper = new Swiper('.js-lightbox-image', {
				modules: [Zoom, Pagination, Navigation],
				watchOverflow: true,
				zoom: true,
			 	navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
        },
        pagination: {
          el: ".swiper-pagination",
          clickable: true,
        },
        breakpoints: {
			    320: {
			    	navigation: false,
			    	
			    },
			    576: {
			    	navigation: {
		          nextEl: ".swiper-button-next",
		          prevEl: ".swiper-button-prev",
		        },
			    }
			  }
			});

			this.swiper = swiper;
		}

		openModal(index) {
			document.querySelector('[data-modal="#lightbox"]').click();
			this.swiper.slideTo(index, 0);
			document.querySelector('.js-lightbox-modal .js-load-more-icon').style.display = 'none';
		}

		closeModal() {
			document.querySelector('.js-lightbox-modal [data-modal="close"]').click();
		}

		createModal(hrefArray) {
			let lightboxModal = document.querySelectorAll(this.modal);
			let htmlSlide = '';

			hrefArray.forEach(href => {
				let hrefType = href.split('.').pop();
				let file;
				if (hrefType == 'mp4' || hrefType == 'mov') {
					file = `<video src="${href}" muted playsinline autoplay loop poster="${hrefArray[1]}"></video>`;
				} else {
					file = `<img src="${href}">`;
				}
				htmlSlide = htmlSlide + `<div class="swiper-slide">
																	<div class="swiper-zoom-container">
																		${file}
																	</div>
																</div>`;
			});

			let html = ` 
				<div class="swiper js-lightbox-image">
          <div class="swiper-wrapper">
          	${htmlSlide}
          </div>
          <div class="d-none d-sm-block swiper-button-next"></div>
		      <div class="d-none d-sm-block swiper-button-prev"></div>
		      <div class="swiper-pagination"></div>
        </div>
			`;

			lightboxModal[0].insertAdjacentHTML('afterBegin', html);

			this.initSlider();
		}
}

export default lightbox;