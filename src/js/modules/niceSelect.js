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
		this.select.forEach(originalSelect => {
			originalSelect.style.display = 'none';
			
			let options = originalSelect.querySelectorAll('option');
			let selectedOptions = originalSelect.querySelectorAll('option:checked');
			let selectTitle = '';

			
			selectedOptions.forEach(opt => {
				let iconUrl = opt.dataset.icon ? opt.dataset.icon : '';
				if (iconUrl) {
					selectTitle += `<img src="${iconUrl}" alt="${opt.textContent}" width="20" height="20"> `;
				}
				selectTitle += opt.textContent;
			});
			
			// let newNode = document.createElement('div');
			// newNode.classList.add('nice-select');
			// newNode.innerHTML = `
			// 	<span class="current">${selectTitle}</span>
			// 	<ul class="list"></ul>
			// `;
			
			// originalSelect.after(newNode);
			


			let newNode = document.createElement('div');
			newNode.classList.add('nice-select');
			newNode.innerHTML = `<span class="current">${selectTitle}</span><ul class="list"></ul>`;
			originalSelect.after(newNode);
			const ulList = newNode.querySelector('ul.list');
			// newNode.querySelector('.current').innerHTML = selectTitle;
			
			options.forEach(opt => {
				// let li = document.createElement('li');
				// li.classList.add('option');

				// const iconUrl = option.dataset.icon;

				// const optionText = option.textContent;  
				// let liContent = '';
				
				// if (iconUrl) liContent += `<img src="${iconUrl}" alt="">`;
				// liContent += optionText;
				// li.innerHTML = liContent;

				// if (option.selected) li.classList.add('selected');
			
				// li.dataset.value = option.value;
				
				// li.innerHTML = option.innerHTML;
				const li = document.createElement('li');
				li.classList.add('option');
				if (opt.selected) li.classList.add('selected');
				li.dataset.value = opt.value;
				let iconUrl = opt.dataset.icon ? opt.dataset.icon : '';
				if (iconUrl) {
					li.innerHTML = `<img src="${iconUrl}" alt="${opt.textContent}" width="20" height="20"> ` + opt.textContent;
				} else {
					li.innerText = opt.textContent;
				}

				ulList.append(li);

				// newNode.querySelector('ul.list').append(li);
			});
		});
	
		this.niceSelectHTML = document.querySelectorAll('.nice-select');
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
					const rect = event.currentTarget.getBoundingClientRect();
					const distanceToBottom = window.innerHeight - rect.bottom;
					const list = event.currentTarget.querySelector('.list');
					const listHeight = list.offsetHeight;
	
					if (distanceToBottom < listHeight) {
						event.currentTarget.classList.add('is-top');
					} else {
						event.currentTarget.classList.remove('is-top');
					}
	
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
		this.niceSelectHTML.forEach((item) => {
			item.addEventListener('click', (event) => {
				const clickedOption = event.target.closest('.option');
				if (clickedOption) {
					let optionVal = clickedOption.getAttribute('data-value');
					console.log('Выбранное значение:', optionVal);
					let thisSelect = event.currentTarget.closest('form')?.querySelector('select');
					if (!thisSelect) return;

					thisSelect.value = optionVal;
					this.triggerChange(thisSelect);

					let checkedOption = thisSelect.querySelector('option:checked');
					if (checkedOption) {
						checkedOption.removeAttribute('selected');
					}

					let newCheckedOption = thisSelect.querySelector('option[value="' + optionVal + '"]');
					if (newCheckedOption) {
						newCheckedOption.setAttribute('selected', 'selected');
					}

					let selectedLi = item.querySelector('.selected');
					if (selectedLi) {
						selectedLi.classList.remove('selected');
					}
					clickedOption.classList.add('selected');

					let iconUrl = newCheckedOption?.dataset.icon || '';
					let text = newCheckedOption?.textContent || '';
					if (iconUrl) {
						item.querySelector('.current').innerHTML = `<img src="${iconUrl}" alt="${text}" width="20" height="20"> ` + text;
					} else {
						item.querySelector('.current').innerText = text;
					}

					// if (!thisSelect.hasAttribute('multiple')) {
					// 	thisSelect.closest('form').submit();
					// }
				}
			});
		});
	}
}	