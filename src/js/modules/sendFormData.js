const submitFormData = ({
    formElement, 
    formValidation, 
    onSuccess = () => {}, 
    onError = () => {},
    onBeforeSubmit = null
  }) => {
  if (!formElement) return;

  formElement.addEventListener("submit", async (event) => {
    event.preventDefault();

    // Проверяем, что ВСЕ поля валидны
    const isValid = Object.values(formValidation).every(value => value !== false);
    if (!isValid) return;
    
    const formData = new FormData(formElement);

    if (typeof onBeforeSubmit === 'function') {
      onBeforeSubmit(formData);
    }
    await sendAjax({ formData, onSuccess, onError });
  });
};

const sendAjax = async ({ formData, onSuccess, onError }) => {
  const response = await fetch(window.ajax.url, {
    method: 'POST',
    body: formData,
  });

  const result = await response.json();
  if (result.success) {
    onSuccess();
  } else {
    onError();
  }

  return result;
};

export {submitFormData, sendAjax}