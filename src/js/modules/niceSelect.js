export default class select {
	niceSelectHTML = '';

	constructor(selector) {
		this.select = document.querySelectorAll(selector);

		this.init();
	}

	init() {
		this.createNiceSelect();
		this.open();
		this.closeOutside();
		this.submit();
	}

	createNiceSelect() {
		this.select.forEach(item => {
			item.style.display = 'none';
			let options = item.querySelectorAll('option');
    	let selected = item.querySelector('option:checked');

    	let selects = item.querySelectorAll('option:checked');

    	let selectTitle = '';
    	
    	selects.forEach(item => {
    		selectTitle += item.textContent;
    	});

			let newNode = document.createElement('div');
					newNode.classList.add('nice-select');		
					newNode.innerHTML = '<span class="current"></span><ul class="list"></ul>';

			item.after(newNode);

		 	newNode.querySelector('.current').innerHTML = selectTitle; 

		 	let optionToList = options.forEach(item => {
    		const li = document.createElement('li');

    		li.classList.add('option');
    		if (item.selected) li.classList.add('selected');
    		li.dataset.value = item.value;
    		li.innerText = item.textContent;
    		
    		newNode.querySelector('ul.list').append(li);
    	});

		});

		this.niceSelectHTML = document.querySelectorAll('.nice-select');
		// console.log(this.niceSelectHTML);
	}

	close() {
		this.niceSelectHTML.forEach(item => {
			item.classList.remove('open');
		});
	}

	closeOutside() {
		document.addEventListener('click', (event) => {
			if (event.target.closest('.nice-select') === null) {
				this.close();
			}
		});
	}

	open() {
		this.niceSelectHTML.forEach(item => {
			item.addEventListener('click', (event) => {
				if (event.currentTarget.classList.contains('open')) {
					event.currentTarget.classList.remove('open');
				} else {
					this.close();
					event.currentTarget.classList.add('open');
				}
			});
		});
	}

 	triggerChange(element) {
  	let changeEvent = new Event('change');
  	element.dispatchEvent(changeEvent);
	}

	submit() {
		this.niceSelectHTML.forEach((item, index) => {

			item.addEventListener('click', (event) => {
				if (event.target.classList.contains('option')) {
			 		let optionVal = event.target.getAttribute("data-value");
			 		// let thisSelect = this.select[index];
			 		let thisSelect = event.currentTarget.closest('form').querySelector('select');
					
			 		thisSelect.value = optionVal;


			 		this.triggerChange(thisSelect);
		
			 		thisSelect.querySelector('option:checked').removeAttribute("selected");
			 		thisSelect.querySelector('option[value="' + optionVal + '"]').setAttribute("selected", "selected");

				 	item.querySelector('.selected').classList.remove('selected');
				 	item.querySelector('.option[data-value="' + optionVal + '"]').classList.add('selected');
    			let text = thisSelect.querySelector('option[value="' + optionVal + '"]').innerText;

		      item.querySelector('.current').innerText = text;

		      thisSelect.closest('form').submit();
				}
			});
		});
	}
}	