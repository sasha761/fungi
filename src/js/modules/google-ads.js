function GA_add_to_cart() {
  const productId    = document.querySelector('[data-id]').getAttribute('data-id');
  const productPrice = document.querySelector('[data-price]').getAttribute('data-price');
  const catTag       = document.querySelector('[data-categories]').getAttribute('data-categories');
  const button       = document.querySelector(".js-product-form .single_add_to_cart_button");
  
  if (button) {
    let productsGA4 = [];

    button.addEventListener('click', (event) => {
      if (!button.classList.contains('disabled')) {
        
        let categories = {};
        let cattArray = catTag.trim().split(', ');
        cattArray.forEach((item, index) => { 
          if(index == 0){
            categories['item_category'] = item.trim();
          } else {
            categories['item_category' + (index + 1)] = item.trim();
          }
        });

        productsGA4[0] = {
          'item_name': document.querySelector('.l-product__name').textContent,
          'item_id': document.querySelector('.js-product-form').getAttribute('data-product_id'),
          'price': productPrice,
          'item_brand': document.querySelector('.l-product__category').textContent,
          'quantity': 1
        }
        Object.assign(productsGA4[0], categories);

        window.dataLayer = window.dataLayer || []; 
        dataLayer.push({ 'ecommerce': null });  // Clear the previous ecommerce object.
        dataLayer.push({
          'event': 'add_to_cart', 
          'ecommerce': {
            'items': productsGA4
          }
        });

        // console.log(dataLayer);
      }
    });
  }
}

function GA_remove_from_cart() {
  // dataLayer.push({
  //   'event': 'remove_from_cart', 
  //   'ecommerce': {
  //     'items': [{
  //     'item_name': '{Название товара}',
  //     'item_id': '{ID товара}',
  //     'price': '{Цена товара}',
  //     'item_brand': '{Производитель}',
  //     'item_category': '{Категория товара верхнего уровня}', 
  //     'item_list_name': '{Название списка}',
  //     'item_list_id': '{ID списка}',
  //     'index': '{Позиция товара в списке}', 
  //     'quantity': '{Количество}'
  //     }]
  //   }
  // }); 
}

function GA_view_item() {
  const productId    = document.querySelector('[data-id]').getAttribute('data-id');
  const productPrice = document.querySelector('[data-price]').getAttribute('data-price');
  const catTag       = document.querySelector('[data-categories]').getAttribute('data-categories');
  
  let productsGA4 = [];
  let categories = {};
  let cattArray = catTag.trim().split(', ');
  cattArray.forEach((item, index) => { 
    if(index == 0){
      categories['item_category'] = item.trim();
    } else {
      categories['item_category' + (index + 1)] = item.trim();
    }
  });

  productsGA4[0] = {
    'item_name': document.querySelector('.l-product__name').textContent,
    'item_id': document.querySelector('.js-product-form').getAttribute('data-product_id'),
    'price': productPrice,
    'item_brand': document.querySelector('.l-product__category').textContent,
    'quantity': 1
  }
  Object.assign(productsGA4[0], categories);

  window.dataLayer = window.dataLayer || [];       
  dataLayer.push({ 'ecommerce': null });  // Clear the previous ecommerce object.
  dataLayer.push({
    'event': 'view_item', 
    'ecommerce': {
      'items': productsGA4
    }
  });

  // console.log(dataLayer);
}

function GA_view_item_list() {
  const products = document.querySelectorAll('.js-product-item');
  const catTag = document.querySelector('[data-categories]');
  const catId = document.querySelector('[data-cat-id]').getAttribute('data-cat-id');

  if (products.length) {
    let productsGA4 = [];

    let categories = {};
    let cattArray = catTag.getAttribute('data-categories').trim().split(', ');
    cattArray.forEach((item, index) => { 
      if(index == 0){
        categories['item_category'] = item.trim();
      } else {
        categories['item_category' + (index + 1)] = item.trim();
      }
    });

    products.forEach((item, index) => {
      const id = item.getAttribute('data-id');
      const name = item.querySelector('.c-product__text-title').textContent;
      const brand = item.getAttribute('data-brand');
      let price = item.querySelector('.c-price bdi').textContent;        
      price = price.split('.')[0];

      productsGA4[index] = {
        'item_name': name,
        'item_id': id,
        'price': price,
        'item_brand': brand,
        'item_list_name': 'Product category',
        'item_list_id': catId,
        'index': index + 1, 
        'quantity': 1
      }
      Object.assign(productsGA4[index], categories);
    });

    window.dataLayer = window.dataLayer || []; 
    dataLayer.push({ 'ecommerce': null });  // Clear the previous ecommerce object.
    dataLayer.push({
      'event': 'view_item_list', 
      'ecommerce': {
        'items': productsGA4
      }
    });

    // console.log(dataLayer);
  }
}

function GA_select_item() {
  const products     = document.querySelectorAll('.js-product-item');
  const catTag = document.querySelector('[data-categories]');
  const catId = document.querySelector('[data-cat-id]').getAttribute('data-cat-id');

  if (products.length) {
    let categories = {};
    let cattArray = catTag.getAttribute('data-categories').trim().split(', ');
    cattArray.forEach((item, index) => { 
      if(index == 0){
        categories['item_category'] = item.trim();
      } else {
        categories['item_category' + (index + 1)] = item.trim();
      }
    });

    let productsGA4 = [];
    products.forEach((item, index) => {
      item.addEventListener('click', (event) => {
        productsGA4[0] = {
          'item_name': event.currentTarget.querySelector('.c-product__text-title').textContent,
          'item_id': event.currentTarget.getAttribute('data-id'),
          'price': event.currentTarget.querySelector('.c-price bdi').textContent.split('.')[0],
          'item_brand': event.currentTarget.getAttribute('data-brand'),
          'item_list_name': 'Product category',
          'item_list_id': catId,
          'index': index + 1, 
          'quantity': 1
        }
        Object.assign(productsGA4[0], categories);

        window.dataLayer = window.dataLayer || []; 
        dataLayer.push({ 'ecommerce': null });  // Clear the previous ecommerce object.
        dataLayer.push({
          'event': 'select_item', 
          'ecommerce': {
            'items': productsGA4
          }
        });

        // console.log(dataLayer);
      });
    });
  }
}

function GA_purchase() {
  const products = document.querySelectorAll('.js-order-item');
  const totalPrice = document.querySelector('[data-total]').getAttribute('data-total');
  const transactionId = document.querySelector('[transaction-id]').getAttribute('transaction-id');

  if (products.length) {
    let productsGA4 = [];

    products.forEach((item, index) => {
      const id = item.getAttribute('data-id');
      const name = item.getAttribute('data-name');
      const brand = item.getAttribute('data-brand');
      const qty = item.getAttribute('data-quantity');
      const price = item.getAttribute('data-price');     

      let categories = {};
      let cattArray = item.getAttribute('data-categories').trim().split(', ');
      cattArray.forEach((item, index) => { 
        if(index == 0){
          categories['item_category'] = item.trim();
        } else {
          categories['item_category' + (index + 1)] = item.trim();
        }
      });   

      productsGA4[index] = {
        'item_name': name,
        'item_id': id,
        'price': price,
        'item_brand': brand,
        'item_list_name': 'Purchase order list',
        'item_list_id': transactionId,
        'index': index + 1, 
        'quantity': qty
      }
      Object.assign(productsGA4[index], categories);
    });

    window.dataLayer = window.dataLayer || []; 
    dataLayer.push({ 'ecommerce': null });  // Clear the previous ecommerce object.
    dataLayer.push({
      'event': 'purchase',
      'ecommerce': {
        'transaction_id': transactionId,
        'value': totalPrice, 
        'currency': 'UAH', 
        'items': productsGA4
      }
    });

    // console.log(dataLayer);
  }
}
export {
  GA_add_to_cart, 
  GA_remove_from_cart, 
  GA_view_item, 
  GA_view_item_list, 
  GA_select_item,
  GA_purchase
};