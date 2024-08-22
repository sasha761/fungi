function readMore() {
	if (document.querySelector('.js-read-more')) {
	  const readMore    = document.querySelector('.js-read-more');
	  const readMoreBtn = document.querySelector('.js-read-more-btn');
	  const readMoreTxt = document.querySelector('.js-read-more-text');
	  const readMoreHeight = readMoreTxt.offsetHeight;

	  readMoreTxt.classList.add('is-close')
	  readMoreBtn.addEventListener('click', () => {
	  	if (readMoreBtn.classList.contains('is-open')) {
	  		readMoreBtn.classList.remove('is-open');
	  		readMoreTxt.classList.remove('is-open');
	  		readMoreTxt.classList.add('is-close');
	  	} else {
	  		readMoreBtn.classList.add('is-open');
	  		readMoreTxt.classList.add('is-open');
	  		readMoreTxt.classList.remove('is-close');

	  		readMoreTxt.style.maxHeight = readMoreHeight + 'px';
	  	}
	  });
	}
}

export default readMore;