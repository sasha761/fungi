class storage {

	constructor(dataLang) {
		this.dataLang = document.querySelectorAll(dataLang);
		this.init();
	}

	init() {
		// если язык сохранен в LocalStorage делаем редирект на страницу языка, если новый гость то открываем модалку
		if (typeof(Storage) !== "undefined") {
			this.storageController();

			if (this.getLocalStorage('lang') == null && document.querySelector('.p-main')) {				
				this.modalSwitchLang();
			} else {
				if (!document.querySelector('.p-error')) {
					let projectUrl = document.querySelector(".js-lang a[data-lang='" + this.getLocalStorage('lang') + "']");
					// this.redirectLang(projectUrl, this.getLocalStorage('lang'));
				}
			}
		}
	}

	setLocalStorage(key, value) {
    localStorage.setItem(key, value);
  }

  getLocalStorage(key) {
    let getStorage = localStorage.getItem(key);
    return getStorage;
  }

	storageController() {
		// при клике на data-lang созраняем значение в LocalStorage и переключаем язык если он существует
    this.dataLang.forEach(lang => {
      lang.addEventListener('click', (event) => {
      	// event.preventDefault();
        let langCode = event.target.getAttribute('data-lang');
        this.setLocalStorage('lang', langCode);

        let projectUrl = document.querySelector(".js-lang a[data-lang='" + this.getLocalStorage('lang') + "']");
        this.redirectLang(projectUrl, langCode);

      });
    });
	}

	projectLangExist(langCode) {
    if (document.querySelector(".js-lang a[data-lang='" + langCode + "']")){
      return true;
    } else {
      return false;
    }
  }

  redirectLang(projectUrl, langCode) {
  	// зыкрываем модальные окна и переключаем на язык если он существует, если нет то на главную страницу указанного языка
    document.querySelector('.js-modal-lang [data-modal="close"]').click();
    document.querySelector('.l-modal-container').classList.remove('is-half-opacity');

    if (projectUrl == location.href){
      return false;

    } else if (location.href.includes('?') || location.href.includes('page')) {
    	return false;

    } else if (this.projectLangExist(langCode)) {
      projectUrl = projectUrl;

    } else {
      projectUrl = window.location.origin + "/" + langCode;
    }

    location.assign(projectUrl);
  }

  modalSwitchLang() {
  	// модальное окно
    document.querySelector('[data-modal="#modal-lang"]').click();
    document.querySelector('.l-modal-container').classList.add('is-half-opacity');
		if (document.querySelector('.js-modal-change-lang')) {
			const modalLangChange = document.querySelector('.js-modal-change-lang');
		  const modalLangBox = document.querySelector('.c-modal__lang-box.is-first');

		  modalLangChange.addEventListener('click', (event) => {
		    modalLangBox.classList.add('d-none');
		    document.querySelector('.c-modal__lang-box.is-second').classList.add('d-flex');
		  });
		}
	}
}

export default storage;