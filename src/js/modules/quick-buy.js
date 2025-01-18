function quickBuy() {
  const quickBuyForm = document.querySelector('.js-quick-buy-form');

  if (!quickBuyForm) return;

  quickBuyForm.addEventListener("submit", (event) => {
    event.preventDefault();

    const formData = new FormData(quickBuyForm);
    
    // Добавляем список продуктов вручную в formData
    const productElements = quickBuyForm.querySelectorAll('.c-mini-cart-product');
    productElements.forEach((product, index) => {
      formData.append(`products[${index}][id]`, product.dataset.id);
      formData.append(`products[${index}][quantity]`, product.dataset.quantity);
      formData.append(`products[${index}][name]`, product.dataset.name);
      formData.append(`products[${index}][link]`, product.dataset.link);
    });

    // Отправляем данные с использованием FormData
    ajax(formData);
  });

  const ajax = async (data) => {
    const cartContainer = document.querySelector('.js-quick-buy-form .js-cart-container');
    cartContainer.classList.add('is-loading');

    const response = await fetch(`${window.ajax.url}?action=contactForm`, {
      method: 'POST',
      body: data, // Отправка данных в формате FormData
    });

    cartContainer.classList.remove('is-loading');

    const result = await response.json();
    if (result.success) {
      document.querySelector('[data-modal="#successful"]').click();
      quickBuyForm.reset();
    } else {
      document.querySelector('[data-modal="#error"]').click();
    }
  };
}

export default quickBuy;