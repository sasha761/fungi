import Validation from '../modules/validation.js';


export default async function initValidation({ formSelector, itiPhone = null, validateFields }) {
  const form = formSelector;
  if (!form) return;

  // const { default: Validation } = await import('../modules/validation.js');
  const formValidator = new Validation();

  for (const [fieldType, selector] of Object.entries(validateFields)) {
    if (itiPhone && fieldType === 'phone') {
      formValidator.validate(fieldType, selector, itiPhone);
    } else {
      formValidator.validate(fieldType, selector);
    }
  }

  return formValidator.getResults();
}