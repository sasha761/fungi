export default () => {
  document.querySelectorAll('.c-likes__item').forEach(function(item) {
    item.addEventListener('click', function() {
      
    // Получаем текущее значение лайков
    let currentCount = parseInt(this.querySelector('span').textContent);
    let selectedItem = this;
    
    // Проверка предыдущего выбора
    let previouslySelected = document.querySelector('.c-likes__item.selected');
    
    if (previouslySelected) {
      let previousCount = parseInt(previouslySelected.querySelector('span').textContent);
      if (previouslySelected === selectedItem) {
        // Убираем лайк, если кликнули повторно на ту же иконку
        selectedItem.querySelector('span').textContent = currentCount - 1;
        selectedItem.classList.remove('selected');
      } else {
        // Убираем лайк с предыдущей иконки
        previouslySelected.querySelector('span').textContent = previousCount - 1;
        previouslySelected.classList.remove('selected');
        // Добавляем лайк на текущую иконку
        selectedItem.querySelector('span').textContent = currentCount + 1;
        selectedItem.classList.add('selected');
      }
    } else {
      // Добавляем лайк, если ранее не было выбора
      selectedItem.querySelector('span').textContent = currentCount + 1;
      selectedItem.classList.add('selected');
    }

      const postId = document.querySelector('.c-likes').getAttribute('data-post-id');
      const likeType = this.getAttribute('data-like-type');
      // const nonce = ajax.nonce;
      // const data = new URLSearchParams();
      // data.append('post_id', postId);
      // data.append('like_type', likeType);
      // data.append('nonce', nonce);

      fetch('/wp-json/custom/v1/handle_like', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          'post_id': postId,
          'like_type': likeType,
          // 'nonce': nonce,
        }),
      })
      .then(response => response.json())
      .then(data => {
        console.log(data);
          if (data.success) {
            // const likeCount = item.innerText + 1;
            // item.innerText = parseInt(item.innerText) + 1;
              // item.innerHTML = item.innerHTML.replace(/\d+$/, data.data.new_likes);
          } else {
            console.log(data.data);

            if (previouslySelected) {
              let previousCount = parseInt(previouslySelected.querySelector('span').textContent);
              previouslySelected.querySelector('span').textContent = previousCount + 1;
              previouslySelected.classList.add('selected');
            }
            selectedItem.querySelector('span').textContent = currentCount;
            selectedItem.classList.remove('selected');
          }
      })
      .catch(error => {
        console.error('Error:', error)
        // Восстановление предыдущего состояния в случае ошибки
        if (previouslySelected) {
          let previousCount = parseInt(previouslySelected.querySelector('span').textContent);
          previouslySelected.querySelector('span').textContent = previousCount + 1;
          previouslySelected.classList.add('selected');
        }
        selectedItem.querySelector('span').textContent = currentCount;
        selectedItem.classList.remove('selected');
      });
    });
  });
}