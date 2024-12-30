export default () => {
  const inputBlocks = document.querySelectorAll('.js-input-block');

  inputBlocks.forEach(block => {
    const input = block.querySelector('input'); 
    const label = block.querySelector('label');

    if (input) {
      input.addEventListener('focus', () => {
        block.classList.add('is-active');
      });

      input.addEventListener('blur', () => {
        if (!input.value.trim()) {
          block.classList.remove('is-active');
        }
      });
    }

    if (label && input) {
      label.addEventListener('click', () => {
        // Если у label правильно проставлен for/id — input получит фокус и сработает событие focus.
        // Но если по каким-то причинам это не работает, можно явно вызвать:
        input.focus();
        // block.classList.add('is-active');
      });
    }
  });

  document.addEventListener('click', (event) => {
    inputBlocks.forEach(block => {
      const input = block.querySelector('input');
      if (input && !block.contains(event.target) && !input.value.trim()) {
        block.classList.remove('is-active');
      }
    });
  });  
}