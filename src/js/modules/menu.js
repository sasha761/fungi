function menu() {
	if (window.innerWidth <= 576) {
		const menuItem = document.querySelector('.c-mobile-menu__item');
		const menuParent = document.querySelector('.c-mobile-menu__item-parent');
		const menuBack = document.querySelector('.js-mobile-menu-back');

		if (menuParent) {
			menuParent.addEventListener('click', function(event) {
				showList(event.currentTarget);
			});
		}
		
		if (menuBack) {
			menuBack.addEventListener('click', function(event) {
				menuBack(event.currentTarget);
			});
		}

		function showList(el) {
			const submenu = el.nextElementSibling;

			submenu.classlist.add('is-active');
			submenu.closest('.c-mobile-menu__item').classlist.add('is-show');

			if (submenu) {
				menuBack.classlist.add('is-active');
			}
		}

		function menuBack(el) {
			if (menuItem.classlist.contains('is-show')) {
				document.querySelector('.c-mobile-menu__item-sub').classlist.remove('is-active');
				menuItem.classlist.remove('is-show');
				el.classlist.remove('is-active');
			}
		}
	}
};

export default menu;