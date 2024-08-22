function dropdown() {
	const dropdown = document.querySelector('.js-dropdown');

	function dropdownList(element) {
		let dropdownBox = element.querySelector('.c-dropdown__list');

		if (dropdownBox !== null) {
			if( element.classList.contains('is-active')) {
				element.classList.remove('is-active');
			} else {
				element.classList.add('is-active');
			}
		}
	}

	dropdown.addEventListener('click', function(event) {
		dropdownList(this);
	});

	document.addEventListener('click', function(event) {
		let target = event.target.className;

	 	var isClickInsideElement = dropdown.contains(event.target);
	  if (!isClickInsideElement) {
	  	dropdown.classList.remove('is-active');
	  }
	});
};

export default dropdown;