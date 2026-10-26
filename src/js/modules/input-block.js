export default () => {
  const inputBlocks = document.querySelectorAll('.js-input-block');

  inputBlocks.forEach(block => {
    const input = block.querySelector('input:not([type="hidden"]), textarea');
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
        input.focus();
      });
    }
  });

  document.addEventListener('click', (event) => {
    inputBlocks.forEach(block => {
      const input = block.querySelector('input:not([type="hidden"]), textarea');
      if (input && !block.contains(event.target) && !input.value.trim()) {
        block.classList.remove('is-active');
      }
    });
  });  
}
