import validator from 'validator';

export default class validation {
  disallow_nums = /[0-9\/]+/;

  /**
   *
   * @param type
   * @param selector
   */
  validate(type, selector) {
    switch (type) {
      case 'firstName':
      case 'lastName':
        this.firstName(selector)
        break
      case 'text':
        this.text(selector)
        break
      case 'email':
        this.email(selector)
        break
      case 'phone':
        this.phone(selector)
        break  
      case 'submit':
        this.submit(selector)

    }
  }

  strError(errorText) {
    const lang = document.documentElement.getAttribute('lang');
    const str = {
      'ua-UA': {
        'This field is required': 'Це поле обов’язкове',
        'Invalid input format': 'Неправильний формат введення',
        'Invalid number': 'Невірний номер',
        'Invalid country code': 'Невірний код країни',
        'Too short': 'Занадто коротке',
        'Too long': 'Занадто довге',
        'Password must be at least 6 characters long': 'Пароль повинен містити щонайменше 6 символів',
      },
      'ru-RU': {
        'This field is required': 'Это поле обязательно',
        'Invalid input format': 'Неверный формат ввода',
        'Invalid number': 'Неверный номер',
        'Invalid country code': 'Неверный код страны',
        'Too short': 'Слишком короткое',
        'Too long': 'Слишком длинное',
        'Password must be at least 6 characters long': 'Пароль должен содержать не менее 6 символов',
      },
      'en-GB': {
        'This field is required': 'This field is required',
        'Invalid input format': 'Invalid input format',
        'Invalid number': 'Invalid number',
        'Invalid country code': 'Invalid country code',
        'Too short': 'Too short', 
        'Too long': 'Too long',
        'Password must be at least 6 characters long': 'Password must be at least 6 characters long',
      },
      'en-US': {
        'This field is required': 'This field is required',
        'Invalid input format': 'Invalid input format',
        'Invalid number': 'Invalid number',
        'Invalid country code': 'Invalid country code',
        'Too short': 'Too short', 
        'Too long': 'Too long',
        'Password must be at least 6 characters long': 'Password must be at least 6 characters long',
      }
    }
    return str[lang][errorText];
  }

  /**
   * Validate the empty fields before submit
   */
  submit(selector) {
    let checkout_form_submit = document.querySelector(selector)

    this.boundSubmitValidator = e => this.submitValidator(e, checkout_form_submit)
    checkout_form_submit.addEventListener('click', this.boundSubmitValidator);
  }

  /**
   * Action submit handler
   * @param e
   * @param field
   */
  submitValidator(e, field) {
    let from = field.closest('form')
    for (let i = 0; i < from.length; i++) {
      if (from[i].hasAttribute('required') && from[i].value === "") {
        this.isValid(false, from[i], this.strError('This field is required'));
      } else {
        this.isValid(true, from[i], '');
      }
    }

    if (from.querySelectorAll('.invalid').length) {
      e.preventDefault()
    }
  }

  /**
   * Field interaction handler: keydown, keyup
   * @param selector
   */
  firstName(selector) {
    let field = document.querySelector(selector)
    field.addEventListener("keydown", event => {
      if (this.disallow_nums.test(event.key)) {
        event.preventDefault()
      }
    })
    if (field.name === 'name') {
      field.setAttribute("maxlength", "40");
    } else {
      field.setAttribute("maxlength", "20");
    }
    this.boundFirstNameValidator = e => this.firstNameStringValidator(e, field)
    field.addEventListener('keyup', this.boundFirstNameValidator);
  }

  firstNameStringValidator(e, field) {
    // Проверка на валидность введённых данных
    let value = e.target.value;
    let isValid = /^[a-zA-Z\u0400-\u04FF\s-]+$/.test(value); // Регулярное выражение для проверки всех букв кириллицы и латиницы, а также пробелов и дефисов

    // Формирование сообщения об ошибке
    let error_msg = value === '' ? this.strError('This field is required') : this.strError('Invalid input format');
    this.isValid(isValid, field, error_msg);
  }

  text(selector) {
    let field = document.querySelector(selector)
    this.boundTextValidator = e => this.textStringValidator(e, field)
    field.addEventListener('keyup', this.boundTextValidator);
  }

  textStringValidator(e, field) {
    const value = e.target.value;
    const forbiddenChars = /[<>{}%&)(]/; 
    const specialCharsOnly = /^[^a-zA-Z\u0400-\u04FF0-9]{5,}$/;

    let _isValid = !forbiddenChars.test(value);
    if (_isValid) {
      _isValid = !specialCharsOnly.test(value);
    }

    console.log(_isValid);

    let error_msg = value === '' ? this.strError('This field is required') : this.strError('Invalid input format');
    this.isValid(_isValid, field, error_msg);
  }


  /**
   * Email validation
   */
  email(selector) {
    let field = document.querySelector(selector)    
    this.boundEmailStringValidation = e => this.emailStringValidation(e, field)
    field.addEventListener('keyup', this.boundEmailStringValidation);
  }

  emailStringValidation(e, field) {
    let _isValid = validator.isEmail(e.target.value)
    let error_msg = e.target.value === '' ? this.strError('This field is required') : this.strError('Invalid input format')
    this.isValid(_isValid, field, error_msg)
  }

  /**
   * Phone number masking and validation
   * @param selector
   */
  phone(telInstance) {
    let field = telInstance.telInput
    this.boundPhoneStringValidator = e => this.phoneStringValidator(e, field, telInstance)
    field.addEventListener('keyup', this.boundPhoneStringValidator);
  }

  phoneStringValidator(e, field, telInstance) {
    const errorMap = ["Invalid number", "Invalid country code", "Too short", "Too long", "Invalid number"];
    let _isValid = telInstance.isValidNumber()
    const errorCode = telInstance.getValidationError();
    const errorText = errorMap[errorCode] || "Invalid number";
    let error_msg = field.value === '' ? this.strError(errorText) : this.strError(errorText);
    this.isValid(_isValid, field, error_msg)
  }


  /**
   * manage status classes and form submit
   * @param isValid
   * @param field
   * @param error_type
   */
  isValid(isValid, field, error_type) {
    // Получаем родительский элемент с классом 'js-input-block'
    const parentBlock = field.closest('.js-input-block');
  
    if (isValid) {
      if (field.classList.contains('invalid')) {
        field.classList.replace('invalid', 'valid');
        if (parentBlock) parentBlock.classList.replace('invalid', 'valid');
      } else {
        field.classList.add('valid');
        if (parentBlock) parentBlock.classList.add('valid');
      }
      this.clearErrorMsg(field);
    } else {
      if (field.classList.contains('valid')) {
        field.classList.replace('valid', 'invalid');
        if (parentBlock) parentBlock.classList.replace('valid', 'invalid');
      } else {
        field.classList.add('invalid');
        if (parentBlock) parentBlock.classList.add('invalid');
      }
      this.errorMsg(field, error_type);
    }
  }
  

  errorMsg(field, error_type) {
    if (field.nextElementSibling !== null && field.nextElementSibling.classList.contains('error-msg')) {
      field.nextElementSibling.remove()
    }
    let error_node = document.createElement("span")
    error_node.innerHTML = error_type
    error_node.classList.add('error-msg')

    field.insertAdjacentElement('afterend', error_node)
  }

  clearErrorMsg(field) {
    if (field.nextElementSibling !== null && field.nextElementSibling.classList.contains('error-msg')) {
      field.nextElementSibling.remove()
    }
  }
}