export default class MiniCart {
  constructor(options) {
    this.addToCartBtn = options.addToCartBtn;
    this.removeFromCartBtn = options.removeFromCartBtn;
    this.miniCart = options.miniCart;
  }

  #fetchData(action, args) {
    return new Promise((resolve, reject) => {
      this.#cartVisability('show');

      const formData = new FormData();

      formData.append('action', action);
      Object.entries(args).forEach(([key, value]) => {
        formData.append(key, value);
      });

      fetch(window.ajax.url, {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          resolve(data);
          setTimeout(() => {
            this.#cartVisability('hide');
          }, 5000);
        } else {
          reject('Update mini cart failed');
        }
      })
      .catch(error => reject(error));
    })
  }

  addToCartHandler() {
    const addToCartButtons = document.querySelectorAll(this.addToCartBtn);
    if(addToCartButtons) {
      addToCartButtons.forEach(button => {
        button.addEventListener('click', (event) => {
          event.preventDefault();
          this.#addToCart(button);
        });
      });
    }
  }

  removeFromCartHandler() {
    const removeFromCartBtn = document.querySelectorAll(this.removeFromCartBtn);
    if(removeFromCartBtn) {
      removeFromCartBtn.forEach(button => {
        button.addEventListener('click', (event) => {
          event.preventDefault();
          this.#removeFromCart(button);
        });
      });
    }
  }

  #addToCart(button) {
    this.#cartChangeStatus();
    this.#buttonChangeStatus(button);

    const productId = button.getAttribute('data-product-id');
    let quantity = 1;

    if (button.closest('.js-product-form')) {
      quantity = button.closest('.js-product-form').querySelector('.js-quantity-input').value
    }

    this.#fetchData('add_to_cart', {product_id: productId, quantity: quantity}).then((data) => {
      this.#buttonChangeStatus(button);
      this.#cartChangeStatus();
      this.#updateMiniCart(data);
      
    }).catch(error => {
      console.error('Failed to update mini cart:', error);
    });   
  }

  #removeFromCart(button) {
    this.#cartChangeStatus();

    const key = button.getAttribute('data-key');

    this.#fetchData('remove_from_cart', {cart_item_key: key}).then((data) => {
      this.#buttonChangeStatus(button);
      this.#cartChangeStatus();
      this.#updateMiniCart(data);
    }).catch(error => {
      console.error('Failed to update mini cart:', error);
    });
  }

  #updateMiniCart(data) {
    const miniCartCounter = document.querySelector('.js-mini-cart-counter');
    const headerCartCounter = document.querySelector('.c-service__count');
    const miniCartList = document.querySelector('.l-mini-cart__list');
    const miniCartTotal = document.querySelector('.l-mini-cart__total .is-bold');
    const miniCartEmpty = document.querySelector('.js-cart-empty');
    const miniCartInfo = document.querySelector('.js-cart-info');

    if (miniCartCounter) {
        miniCartCounter.textContent = data.data?.count;
        headerCartCounter.textContent = data.data?.count;
    }

    if (miniCartList) {
        miniCartList.innerHTML = '';  
    } 

    if (data.data?.html?.cart_products && data.data?.html?.cart_products.length > 0) {
      miniCartEmpty.classList.add('is-hide');
      miniCartInfo.classList.remove('is-hide');

      data.data?.html?.cart_products.forEach(product => {
        const listItem = document.createElement('li');
        listItem.classList.add('l-mini-cart__item');

        let quantityHtml = '';
        if (product.quantity >= 2) {
          quantityHtml = `
            <p class="is-quantity">Quantity: <b>${product.quantity}</b></p>
          `;
        }
        listItem.innerHTML = `
          <div class="l-mini-cart__item-img">
            <a href="${product.url}">
              ${product.thumbnail}
            </a>
          </div>
          <div class="l-mini-cart__item-info">
          <span class="is-name">${product.title}</span>
          ${quantityHtml}
          <p class="c-price">${product.price}</p>  
          </div>
          <a href="${product.delete_permalink}" 
              class="c-remove js-product-remove" 
              data-product-id="${product.delete_productid}" 
              data-key="${product.cart_item_key}"
              data-product-sku="${product.delete_sku}">x</a>                
        `;
        // document.querySelector('.l-mini-cart__list').appendChild(listItem);
        miniCartList.appendChild(listItem);
      });
    } else {
      miniCartInfo.classList.add('is-hide');
      miniCartEmpty.classList.remove('is-hide');
    }

    if (miniCartTotal) {
      miniCartTotal.innerHTML = data.data?.html?.total;
    }  
    
    this.removeFromCartHandler();
  }

  #cartVisability(status) {
    const headerMiniCart = document.querySelector('.c-service.cart');
    headerMiniCart.classList.toggle('hovered', status === 'show');
  }

  #cartChangeStatus() {
    const miniCart = document.querySelector(this.miniCart);
    miniCart?.classList.toggle('is-loading'); 
  }

  #buttonChangeStatus(button) {
    const btnLoader = button.querySelector('.js-loader');
    const btnText = button.querySelector('.js-button-text');

    if (button.classList.contains('is-proccess')) {
      button.classList.remove('is-proccess');
      button.disabled = false;
    } else {
      button.classList.add('is-proccess');
      button.disabled = true;
    }

    if (btnLoader && btnLoader.classList.contains('is-show')) {
      btnLoader.classList.remove('is-show');
      btnLoader.classList.add('is-hide');
    } else if (btnLoader && btnLoader.classList.contains('is-hide')) {
      btnLoader.classList.remove('is-hide');
      btnLoader.classList.add('is-show');
    }

    if (btnText && btnText.classList.contains('is-show')) {
      btnText.classList.remove('is-show');
      btnText.classList.add('is-hide');
    } else if (btnText && btnText.classList.contains('is-hide')) {
      btnText.classList.remove('is-hide');
      btnText.classList.add('is-show');
    }
  }
}