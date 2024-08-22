function quickBuy() {
	const quickBuyForm = document.querySelector('.js-quick-buy-form');
	const targetNode = document.querySelector('.woocommerce-variation-add-to-cart .variation_id');
  const quickBuyBtn = document.querySelector('.js-quick-add-to-card');
  const notification = document.querySelector('.js-product-form-notification');

  // const string = (document.body.getAttribute('data-language') == 'ru') ? 'Выберите размер' : 'Виберіть розмір';

  // const buttonFn = () => {
  //   if (quickBuyBtn.classList.contains('is-disabled')) {
  //     quickBuyBtn.setAttribute('data-modal', '');
  //     quickBuyBtn.addEventListener('click', (event) => {
  //       if (quickBuyBtn.classList.contains('is-disabled')) {
  //         notification.textContent = string;
  //       }  
  //     });
  //   } else {
  //     quickBuyBtn.setAttribute('data-modal', '#quick-buy');
  //     notification.textContent = '';
  //   }
  // } 

  // buttonFn();
  

  if (targetNode) {
    const config = { attributes: true };
    const callback = (mutationList, observer) => {
      for (const mutation of mutationList) {
        if (mutation.attributeName === 'value' && document.querySelector('.single_variation_wrap .woocommerce-variation-availability .out-of-stock') === null) {
          document.querySelector('.js-quick-buy-variation').value = targetNode.value;
          quickBuyBtn.classList.remove('is-disabled');
          // buttonFn();
        } else {
          quickBuyBtn.classList.add('is-disabled');
          // buttonFn();
        }
      }
    };

    const observer = new MutationObserver(callback);
    observer.observe(targetNode, config);
  }

	
	quickBuyForm.addEventListener("submit", (event) => {
    event.preventDefault();

    const email = quickBuyForm.querySelector('[name="email"]').value;
    const name = quickBuyForm.querySelector('[name="name"]').value;
    const surname = quickBuyForm.querySelector('[name="surname"]').value;
    const phone = quickBuyForm.querySelector('[name="phone"]').value;
    const city = quickBuyForm.querySelector('[name="city"]').value;
    const address = quickBuyForm.querySelector('[name="address"]').value;
    const variation_id = quickBuyForm.querySelector('[name="variation_id"]').value;
    const product_id = quickBuyForm.querySelector('[name="product_id"]').value;

    ajax({email, name, surname, phone, city, address, variation_id, product_id});
  });

  const ajax = async(data) => {
  	quickBuyForm.querySelector('.js-load-more-icon').classList.add('is-show');
  	const params = new URLSearchParams();

  	for (const [key, value] of Object.entries(data)) {
  		params.set(key, value);
		}

    const response = await fetch(`${window.ajax.url}?action=quickBuy`, {
      method: 'POST',
      body: params,
    });
    const result = await response.json();

    // console.log(result);

    quickBuyForm.querySelector('.js-load-more-icon').classList.remove('is-show');

    if (result) {
    	quickBuyForm.reset();
    	quickBuyForm.querySelector('.js-form-answer').classList.remove('is-error');
    	quickBuyForm.querySelector('.js-form-answer').classList.add('is-success');
	    quickBuyForm.querySelector('.js-form-answer').textContent = 'Заказ успешно оформлен';
      window.location.href = result;
    } else {
    	quickBuyForm.querySelector('.js-form-answer').classList.remove('is-success');
    	quickBuyForm.querySelector('.js-form-answer').classList.add('is-error');
	    quickBuyForm.querySelector('.js-form-answer').textContent = 'Что-то пошло не так. Позвоните нашему менеджеру +38 (066) 315 65 36';
    }
  }
}

export default quickBuy;