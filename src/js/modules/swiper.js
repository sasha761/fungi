import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, Zoom, FreeMode} from 'swiper/modules';

export const initblogCategorySlider = () => {
	const blogCategorySlider = document.querySelectorAll('.js-blog-category');

	if (blogCategorySlider.length) {
		blogCategorySlider.forEach(slider => {
			new Swiper(slider, {
				modules: [Autoplay],
				slidesPerView: 'auto',
				watchOverflow: true,
				watchOverflow: true,
				spaceBetween: 10,
				autoHeight: false,
				autoplay: {
					delay: 2500,
					stopOnLastSlide: false,
					disableOnInteraction: true,
				},
			});
		});
	}
}

export const initProductGallerySlider = () => {
	const productImageSlider = document.querySelectorAll('.js-product-image');

	if (productImageSlider.length) {
		productImageSlider.forEach(slider => {
			new Swiper(slider, {
				modules: [Autoplay, Zoom, Pagination],
				watchOverflow: true,
				spaceBetween: 7,
				autoHeight: false,
				slidesPerView: 1,
				zoom: {
					maxRatio: 1.6,
				},
				autoplay: {
					delay: 2500,
					stopOnLastSlide: false,
					disableOnInteraction: true,
				},
				pagination: {
	        el: ".swiper-pagination",
	        clickable: true,
	      },
				breakpoints: {
			    320: {
			      slidesPerView: 1,
			    },
			    576: {
			    	slidesPerView: 1,
			    }
			  }
			});
		});
	}
}

export const initProductRowSlider = () => {
	const productSlider = document.querySelectorAll('.js-product-row');

	if (productSlider.length) {
		productSlider.forEach(slider => {
			let sliderContainer = slider.closest('.js-slider-container');
			let sliderArrows = sliderContainer.querySelector('.c-arrow');
			let navArrows = false;
			let countArrows = false;
			if (sliderArrows) {
				let next = sliderArrows.querySelector('.js-next');
				let prev = sliderArrows.querySelector('.js-prev');
				let countEl = sliderArrows.querySelector('.c-arrow__count');
				navArrows = {
					nextEl: next,
					prevEl: prev
				}
				countArrows = {
					el: countEl,
	        type: "fraction",
				}
			}

			new Swiper(slider, {
				modules: [Autoplay, Navigation, Pagination],
				slidesPerView: 'auto',
				watchOverflow: true,
				autoplay: {
					delay: 2500,
					stopOnLastSlide: false,
					disableOnInteraction: true,
				},
				navigation: navArrows,
				pagination: countArrows,
				breakpoints: {
			    320: {
			      slidesPerView: 1.2,
			    },
					430: {
			      slidesPerView: 1.5,
			    },
			    576: {
			    	slidesPerView: 2,
			    },
					768: {
			    	slidesPerView: 3,
			    },
					1024: {
			    	slidesPerView: 4,
			    }
			  }
			})
		})
	}
}