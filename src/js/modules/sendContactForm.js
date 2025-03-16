function sendContactForm() {
  const contactUsForm = document.querySelector('.js-contact-us-form');

  if (!contactUsForm) return;

  contactUsForm.addEventListener("submit", (event) => {
    event.preventDefault();

    const invalidInputs = contactUsForm.querySelectorAll("input.invalid");
    if (invalidInputs.length > 0) {
      console.warn("Форма содержит ошибки, отправка запрещена.");
      return;
    }

    const formData = new FormData(contactUsForm);
    
    // Отправляем данные с использованием FormData
    ajax(formData);
  });

  const ajax = async (data) => {
    // const cartContainer = document.querySelector('.js-quick-buy-form .js-cart-container');
    // cartContainer.classList.add('is-loading');

    const response = await fetch(`${window.ajax.url}?action=contactForm`, {
      method: 'POST',
      body: data, // Отправка данных в формате FormData
    });

    // cartContainer.classList.remove('is-loading');

    const result = await response.json();
    if (result.success) {
      document.querySelector('[data-modal="#successful"]').click();
      contactUsForm.reset();
    } else {
      document.querySelector('[data-modal="#error"]').click();
    }
  };
}

export default sendContactForm;