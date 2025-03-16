import validator from 'validator';

export default class validation {
  disallow_nums = /[0-9\/]+/;
  maxFileSize = 10 * 1024 * 1024; // 10MB
  allowedFileTypes = [
    'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // doc, docx
    'text/xml', // xml
    'image/jpeg', 'image/png', 'image/webp', 'image/heic', // jpg, jpeg, png, webp, heic
    'video/quicktime', 'video/mp4' // mov, mp4
  ];
  validationResults = {};

  /**
   * @param {string} type - Тип валидации (email, phone, text и т. д.)
   * @param {string | object} selector - Селектор поля или объект (для телефона)
   * @returns {boolean} - Результат валидации
   */
  validate(type, selector, itiPhone) {
    this.validationResults[type] = null;
    switch (type) {
      case 'file':
        this.file(selector, type);
        break;
      case 'name':
      case 'firstName':
      case 'lastName':
        this.name(selector, type)
        break
      case 'text':
        this.text(selector, type)
        break
      case 'email':
        this.email(selector, type)
        break
      case 'phone':
        this.phone(selector, type, itiPhone)
        break  
      case 'submit':
        this.submit(selector, type)
    }
  }

  updateValidationResult = (type, isValid) => {
    this.validationResults[type] = isValid;
    // console.log(selector, isValid);
  };

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
        'Invalid file type': 'Недопустимий тип файлу',
        'File is too large': 'Файл занадто великий (макс. 10MB)',
      },
      'ru-RU': {
        'This field is required': 'Это поле обязательно',
        'Invalid input format': 'Неверный формат ввода',
        'Invalid number': 'Неверный номер',
        'Invalid country code': 'Неверный код страны',
        'Too short': 'Слишком короткое',
        'Too long': 'Слишком длинное',
        'Password must be at least 6 characters long': 'Пароль должен содержать не менее 6 символов',
        'Invalid file type': 'Недопустимый тип файла',
        'File is too large': 'Файл слишком большой (макс. 10MB)',
      },
      'en-GB': {
        'This field is required': 'This field is required',
        'Invalid input format': 'Invalid input format',
        'Invalid number': 'Invalid number',
        'Invalid country code': 'Invalid country code',
        'Too short': 'Too short', 
        'Too long': 'Too long',
        'Password must be at least 6 characters long': 'Password must be at least 6 characters long',
        'Invalid file type': 'Invalid file type',
        'File is too large': 'File is too large (max 10MB)',
      },
      'en-US': {
        'This field is required': 'This field is required',
        'Invalid input format': 'Invalid input format',
        'Invalid number': 'Invalid number',
        'Invalid country code': 'Invalid country code',
        'Too short': 'Too short', 
        'Too long': 'Too long',
        'Password must be at least 6 characters long': 'Password must be at least 6 characters long',
        'Invalid file type': 'Invalid file type',
        'File is too large': 'File is too large (max 10MB)',
      }
    }
    return str[lang][errorText];
  }

  /**
   * Validate the empty fields before submit
   */
  submit(selector, type) {
    let checkout_form_submit = document.querySelector(selector)
    checkout_form_submit.addEventListener('click', (e) => this.submitValidator(e, checkout_form_submit, type));
  }

  /**
   * Action submit handler
   * @param e
   * @param field
   */
  submitValidator(e, field) {
    let from = field.closest('form')
    for (let i = 0; i < from.length; i++) {
      let _isValid = false;
      if (from[i].hasAttribute('required') && from[i].value === "") {
        _isValid = false;
        this.isValid(_isValid, from[i], this.strError('This field is required'));
      } else if (from[i].classList.contains('invalid') && from[i].value !== "") {
        _isValid = false;
        this.isValid(_isValid, from[i], this.strError('Invalid input format'));
      } else {
        _isValid = true;
        this.isValid(_isValid, from[i], '');
      }

      // this.validationResults[type] = _isValid;
    }
    
    // if (from.querySelectorAll('.invalid').length) {
    //   e.preventDefault()
    // }
  }

  /**
   * Валидация файлов
   * @param selector - input[type=file]
   */
  file(selector, type) {
    let fileInput = document.querySelector(selector);
    fileInput.addEventListener('change', (e) => this.fileValidator(e, fileInput, type));
  }

  fileValidator(e, field, type) {
    const files = e.target.files;
    let _isValid = true;
    let errorMsg = '';

    for (let file of files) {
      if (!this.allowedFileTypes.includes(file.type)) {
        _isValid = false;
        errorMsg = this.strError('Invalid file type');
        break;
      }

      if (file.size > this.maxFileSize) {
        _isValid = false;
        errorMsg = this.strError('File is too large');
        break;
      }
    }

    this.isValid(_isValid, field, errorMsg);
    this.updateValidationResult(type, _isValid);
  }
  

  /**
   * Field interaction handler: keydown, keyup
   * @param selector
   */
  name(selector, type) {
    let field = document.querySelector(selector)
    field.addEventListener('keyup', (e) => this.nameStringValidator(e, field, type));
  }

  nameStringValidator(e, field, type) {
    const value = e.target.value;
    const specialCharsOnly = /^[a-zA-Z\u0400-\u04FF\s-]+$/;

    let _isValid = !this.disallow_nums.test(value);
    if (_isValid) {
      _isValid = specialCharsOnly.test(value);
    }

    let error_msg = value === '' 
      ? this.strError('This field is required') 
      : this.strError('Invalid input format');

    this.isValid(_isValid, field, error_msg);
    this.updateValidationResult(type, _isValid);
  }

  text(selector, type) {
    let field = document.querySelector(selector)
    field.addEventListener('keyup', (e) => this.textStringValidator(e, field, type));
  }

  textStringValidator(e, field, type) {
    const value = e.target.value;
    const forbiddenChars = /[<>{}%&)(]/; 
    const specialCharsOnly = /^[^a-zA-Z\u0400-\u04FF0-9]{5,}$/;

    let _isValid = !forbiddenChars.test(value);
    if (_isValid) {
      _isValid = !specialCharsOnly.test(value);
    }

    let error_msg = value === '' 
      ? this.strError('This field is required') 
      : this.strError('Invalid input format');

    this.isValid(_isValid, field, error_msg);
    this.updateValidationResult(type, _isValid);
  }


  /**
   * Email validation
   */

  email(selector, type) {
    let field = document.querySelector(selector)    
    field.addEventListener('keyup', (e) => this.emailStringValidation(e, field, type));
  }

  emailStringValidation(e, field, type) {
    if (!field) return false; // Предохранитель
  
    let _isValid = validator.isEmail(e.target.value);
    let error_msg = e.target.value === '' 
      ? this.strError('This field is required') 
      : this.strError('Invalid input format');
  
    this.isValid(_isValid, field, error_msg);
    this.updateValidationResult(type, _isValid);
  }

  /**
   * Phone number masking and validation
   * @param selector
   */
  phone(selector, type, itiPhone = null) {
    let field = document.querySelector(selector);
    field.addEventListener('keyup', (e) => this.phoneStringValidator(e, field, type, itiPhone));
  }
  
  phoneStringValidator(e, field, type, telInstance = null) {
    const errorMap = ["Invalid number", "Invalid country code", "Too short", "Too long", "Invalid number"];
    let _isValid = false;
    let errorCode = '';
    let error_msg = '';
  
    if (telInstance) {
      // Если передан telInstance, используем его методы для проверки
      _isValid = telInstance.isValidNumber();
      errorCode = telInstance.getValidationError();
      error_msg = field.value === '' ? this.strError('This field is required') : this.strError(errorMap[errorCode] || 'Invalid number');
    } else {
      // Если telInstance не передан, используем старую проверку
      _isValid = validator.isMobilePhone(field.value, ['sq-AL', 'ca-AD', 'de-AT', 'be-BY', 'fr-BE', 'bg-BG', 'cs-CZ', 'da-DK', 'et-EE', 'fi-FI', 
      'fr-FR', 'de-DE', 'el-GR', 'hu-HU', 'en-IE', 'it-IT', 'lt-LT', 'de-LU', 'en-MT', 'ro-Md', 
      'nl-NL', 'nb-NO', 'pl-PL', 'pt-PT', 'ro-RO', 'it-SM', 'sr-RS', 'sk-SK', 'sl-SI', 'es-ES', 
      'sv-SE', 'de-CH', 'uk-UA', 'en-GB', 'ka-GE', 'az-AZ', 'kk-KZ', 'tr-TR', 'en-US', 'en-CA', 
      'pt-BR', 'en-ZA', 'ar-EG', 'en-NG', 'en-KE', 'en-GH', 'ar-DZ', 'ar-TN'], { strict: true });
      error_msg = field.value === '' ? this.strError('This field is required') : this.strError('Invalid input format');
    }
  
    // Обновление результата валидации
    this.isValid(_isValid, field, error_msg);
    this.updateValidationResult(type, _isValid);
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
    // if (field.nextElementSibling !== null && field.nextElementSibling.classList.contains('error-msg')) {
    //   field.nextElementSibling.remove()
    // }
    this.clearErrorMsg(field);
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

  /**
   * Получить текущий статус валидации
   * @returns {Object} - Объект с результатами всех проверок
   */
  getResults() {
    return this.validationResults;
  }
}