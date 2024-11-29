export default class Cart {
  constructor(options) {
    this.addToCartBtn = options.addToCartBtn;
    this.removeFromCartBtn = options.removeFromCartBtn;
    this.cartContainer = options.cartContainer;
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
    const cartContainer = document.querySelectorAll(`${this.cartContainer}`);
    cartContainer.forEach(cart => {
      let cartList = cart.querySelector('.js-mini-cart-list');
      const cartTotal = cart.querySelector('.js-cart-total');
      const cartEmpty = cart.querySelector('.js-cart-empty');
      const cartInfo = cart.querySelector('.js-cart-info');

      cartList.innerHTML = '';  

      if (data.data?.cart_products && data.data?.cart_products.length > 0) {
        cartEmpty?.classList.add('is-hide');
        cartInfo?.classList.remove('is-hide');

        data.data?.cart_products.forEach(product => {
          const listItem = document.createElement('li');
          listItem.classList.add('c-mini-cart-product');

          listItem.setAttribute('data-id', product.id); 
          listItem.setAttribute('data-quantity', product.quantity); 
          listItem.setAttribute('data-name', product.title); 
          listItem.setAttribute('data-link', product.url);

          let quantityHtml = '';
          if (product.quantity >= 2) quantityHtml = `<p class="is-quantity">Quantity: <b>${product.quantity}</b></p>`;
          
          listItem.innerHTML = `
            <div class="c-mini-cart-product__img">
              <a href="${product.url}">
                ${product.thumbnail}
              </a>
            </div>
            <div class="c-mini-cart-product__info">
              <span class="is-name">${product.title}</span>
              ${quantityHtml}
              <p class="c-price">${product.price}</p>  
            </div>
            <a href="${product.delete_permalink}" 
              class="c-remove js-product-remove" 
              data-product-id="${product.delete_productid}" 
              data-key="${product.cart_item_key}"
              data-product-sku="${product.delete_sku}">
              <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="0.5" width="24" height="24" rx="12"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.02323 7.81637C7.82797 7.62111 7.51139 7.62111 7.31612 7.81637C7.12086 8.01163 7.12086 8.32821 7.31612 8.52348L11.2928 12.5001L7.31639 16.4765C7.12113 16.6718 7.12113 16.9884 7.31639 17.1836C7.51166 17.3789 7.82824 17.3789 8.0235 17.1836L11.9999 13.2072L15.9763 17.1836C16.1715 17.3789 16.4881 17.3789 16.6834 17.1836C16.8786 16.9884 16.8786 16.6718 16.6834 16.4765L12.707 12.5001L16.6836 8.52348C16.8789 8.32821 16.8789 8.01163 16.6836 7.81637C16.4884 7.62111 16.1718 7.62111 15.9765 7.81637L11.9999 11.793L8.02323 7.81637Z" fill="white"/>
              </svg>
            </a>                
          `;

          cartList.appendChild(listItem);
        });
      } else {
        cartInfo?.classList.add('is-hide');
        cartEmpty?.classList.remove('is-hide');
      }

      if (cartTotal) cartTotal.innerHTML = data.data?.total;
    });

    const cartCounter = document.querySelectorAll('.js-mini-cart-counter');
    if (cartCounter) cartCounter.forEach(item => item.textContent = data.data?.count)

    this.removeFromCartHandler();
  }

  #cartVisability(status) {
    const headerMiniCart = document.querySelector('.c-service.cart');
    headerMiniCart.classList.toggle('hovered', status === 'show');
  }

  #cartChangeStatus() {
    const cartContainer = document.querySelectorAll(this.cartContainer);
    cartContainer.forEach(container => container.classList.toggle('is-loading'));
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