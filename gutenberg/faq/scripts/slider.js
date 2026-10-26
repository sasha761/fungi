import Swiper from 'swiper/core';
// import { Autoplay } from 'swiper/modules';

const Sliders = {
  init(root = document) {
    const sliders = root.querySelectorAll('.js-accordion-content-slider');
    if (!sliders.length) return;

    sliders.forEach((el) => {
      if (el.swiper) return;

      new Swiper(el, {
        // modules: [Autoplay],
        slidesPerView: 'auto',
        speed: 800,
        loop: true,
      });
    });
  },
};

export default Sliders;
