function updateCart() {
  const quantityButtons = document.querySelectorAll('.js-quantity-plus, .js-quantity-minus');
    quantityButtons.forEach(function(button) {
      button.addEventListener('click', function() {
        let input = this.parentElement.querySelector('.c-quantity__input');
        let currentValue = parseInt(input.value);
        if (this.classList.contains('js-quantity-plus')) {
          input.value = currentValue + 1;
        } else if (this.classList.contains('js-quantity-minus') && currentValue > 1) {
          input.value = currentValue - 1;
        }
        var event = new Event('change');
        input.dispatchEvent(event);
      });
    });

    const quantityInputs = document.querySelectorAll('.c-quantity__input');
    quantityInputs.forEach(function(input) {
      input.addEventListener('change', function() {
        const productID = this.closest('.c-product-cart').querySelector('.c-remove').dataset.product_id;
        const quantity = this.value;

        const currentLang = ajax.lang || 'en';
        
        const body = new URLSearchParams({
          action: 'update_cart_quantity',
          product_id: productID,
          quantity: quantity,
          lang: currentLang
        });
      
        fetch(ajax.url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
          },
          credentials: 'include',
          body: body.toString()
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              updateCartData(data.data);
            } else {
              console.error('Ошибка при обновлении корзины');
            }
          })
          .catch(error => {
            console.error('Ошибка при обновлении корзины:', error);
          });
      });
      // input.addEventListener('change', function() {
      //   const productID = this.closest('.c-product-cart').querySelector('.c-remove').dataset.product_id;
      //   const quantity = this.value;
      //   var xhr = new XMLHttpRequest();
      //   xhr.open('POST', ajax.url, true);
      //   xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
      //   xhr.onload = function() {
      //     if (xhr.status === 200) {
      //       const response = JSON.parse(xhr.responseText);
      //       if (response.success) {
      //         updateCartData(response.data);
      //       } else {
      //         alert('Ошибка при обновлении корзины');
      //       }
      //     }
      //   };
      //   xhr.send('action=update_cart_quantity&product_id=' + productID + '&quantity=' + quantity);
      // });
    });

    function updateCartData(data) {
      document.querySelector('.l-sidebar-cart .js-cart-amount').innerHTML = data.data.subtotal;
      document.querySelector('.l-sidebar-cart .js-cart-total').innerHTML = data.data.cart_total;
      document.querySelector('.l-sidebar-cart .js-cart-shiping').innerHTML = data.data.shipping_total;
    }
};

export default updateCart;