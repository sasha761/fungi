import { fetchSummary } from '../api/summarize.js';

export function initializeSummarizeButtons() {
  const buttons = document.querySelectorAll('.btn-summarize');
  const contentContainer = document.getElementById('article-content');
  const originalContent = contentContainer.innerHTML; // Сохраняем оригинальный текст
  const lang = window.ajax.lang;
  buttons.forEach(button => {
    button.addEventListener('click', async () => {
      const length = button.getAttribute('data-summary');

      if (length === 'original') {
        contentContainer.innerHTML = originalContent; // Возвращаем оригинальный текст
        return;
      }

      try {
        button.disabled = true; // Блокируем кнопку
        button.textContent = 'Loading...';

        const summary = await fetchSummary(originalContent, length, lang);
        console.log(summary);
        contentContainer.innerHTML = summary; // Обновляем контент
      } catch (error) {
        console.error(error);
        alert('Ошибка при получении сводки');
      } finally {
        button.disabled = false; // Разблокируем кнопку
        button.textContent = length.charAt(0).toUpperCase() + length.slice(1); // Восстанавливаем текст кнопки
      }
    });
  });
}
