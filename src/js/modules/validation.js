import IMask from 'imask';
import validator from 'validator';


export default class validation {

    disallow_nums = /[0-9\/]+/;

    /**
     *
     * @param type
     * @param selector
     */
    validate(type, selector){
        switch (type) {
            case 'firstName':
            case 'lastName':
            case 'billingCity':
                this.firstName(selector)
                break
            case 'post':
                this.post(selector)
                break
            case 'phone':
                this.phone(selector)
                break
            case 'email':
                this.email(selector)
                break
            case 'submit':
                this.submit(selector)

        }
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
            if(from[i].hasAttribute('required') && from[i].value === "") {
                this.isValid(false, from[i], 'Поле не должно быть пустым')
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
        let ignore = {
            ignore: '\-'
        }
        if (field.name === 'name') {
            ignore = {
                ignore: '[\\s-]+'
            }
        }
        let _isValid = validator.isAlpha(e.target.value, 'uk-UA', {ignore: '-'}) || validator.isAlpha(e.target.value, 'ru-RU', ignore)
        let error_msg = e.target.value === '' ? 'Поле не должно быть пустым' : 'Неверный формат ввода'
        this.isValid(_isValid, field, error_msg)
    }

    /**
     * Phone number masking and validation
     * @param selector
     */
    phone(selector) {
        let field = document.querySelector(selector)
        let maskOptions = {
            mask: '+{38}(000)000-00-00'
        }

        IMask(field, maskOptions);
        field.setAttribute("placeholder", "+38(000)000-00-00")
        this.boundPhoneStringValidator = e => this.phoneStringValidator(e, field)
        field.addEventListener('keyup', this.boundPhoneStringValidator);
    }

    phoneStringValidator(e, field) {
        let _value = e.target.value.replace(/[()-]/g,"")
        let _isValid = validator.isMobilePhone(_value, ['uk-UA'], { strict: true})
        let error_msg = e.target.value === '' ? 'Поле не должно быть пустым' : 'Неверный формат ввода'
        this.isValid(_isValid, field, error_msg)
    }

    /**
     * Email validation
     */
    email(selector){
        let field = document.querySelector(selector)
        this.boundEmailStringValidation = e => this.emailStringValidation(e, field)
        field.addEventListener('keyup', this.boundEmailStringValidation);
    }

    emailStringValidation(e, field) {
        let _isValid = validator.isEmail(e.target.value)
        let error_msg = e.target.value === '' ? 'Поле не должно быть пустым' : 'Неверный формат ввода'
        this.isValid(_isValid, field, error_msg)
    }

    /**
     * The delivery filial ID
     * @param selector
     */
    post(selector) {
        let field = document.querySelector(selector)

        field.setAttribute("maxlength", "4")
        field.setAttribute("required", "")
        field.oninput = function (e){
            e.target.value = e.target.value.replace(/[^0-9]/gi, "")
        }
        this.boundPostStringValidator = e => this.postStringValidator(e, field)
        field.addEventListener('keyup', this.boundPostStringValidator);
    }

    postStringValidator(e, field) {
        let _isValid = validator.isNumeric(e.target.value)
        let error_msg = e.target.value === '' ? 'Поле не должно быть пустым' : 'Неверный формат ввода'
        this.isValid(_isValid, field, error_msg)
    }


    /**
     * manage status classes and form submit
     * @param isValid
     * @param field
     * @param error_type
     */
    isValid(isValid, field, error_type) {
        if (isValid) {
            if (field.classList.contains('invalid')) {
                field.classList.replace('invalid','valid')
            } else {
                field.classList.add('valid')
            }
            this.clearErrorMsg(field)
        } else {
            if (field.classList.contains('valid')) {
                field.classList.replace('valid','invalid')
            } else {
                field.classList.add('invalid')
            }
            this.errorMsg(field, error_type)
        }
    }

    errorMsg(field, error_type){
        if (field.nextElementSibling !== null && field.nextElementSibling.classList.contains('error-msg')){
            field.nextElementSibling.remove()
        }
        let error_node = document.createElement("span")
        error_node.innerHTML = error_type
        error_node.classList.add('error-msg')

        field.insertAdjacentElement('afterend', error_node)
    }

    clearErrorMsg(field){
        if (field.nextElementSibling !== null && field.nextElementSibling.classList.contains('error-msg')){
            field.nextElementSibling.remove()
        }
    }

}