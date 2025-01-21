import { fetchSummary } from '../api/summarize.js';

export function initializeSummarizeButtons() {
  const buttonContainer  = document.querySelector('.js-buttons-container');
  const contentContainer = document.querySelector('.js-article-content');
  const contentBox = document.getElementById('article-content');

  if (!buttonContainer || !contentContainer || !contentBox) return;

  const originalContent = contentBox.innerHTML; // Сохраняем оригинальный текст
  const lang = window.ajax.lang;

  buttonContainer.addEventListener('click', async (event) => {
    const button = event.target.closest('button[data-summary]');
    if (!button) return; // Если клик не по кнопке с data-summary, игнорируем

    const length = button.dataset.summary;

    if (length === 'original') {
      restoreOriginalContent(buttonContainer, button, contentBox, originalContent);
      return;
    }

    await handleSummaryButtonClick(button, contentBox, contentContainer, originalContent, length, lang, buttonContainer);
  });
}

/**
 * Обработка клика по кнопке суммирования
 *
 * @param {HTMLElement} button - Нажатая кнопка
 * @param {HTMLElement} contentBox - Блок с содержимым статьи
 * @param {HTMLElement} contentContainer - Контейнер для индикатора загрузки
 * @param {string} originalContent - Оригинальное содержимое
 * @param {string} length - Длина суммирования
 * @param {string} lang - Язык
 * @param {HTMLElement} buttonContainer - Родительский контейнер кнопок
 */
async function handleSummaryButtonClick(button, contentBox, contentContainer, originalContent, length, lang, buttonContainer) {
  try {
    contentContainer.classList.add('is-loading');
    button.disabled = true;

    const summary = await fetchSummary(originalContent, length, lang);
    contentBox.innerHTML = summary;

    // Обновляем видимость кнопок
    updateButtonVisibility(buttonContainer, button);
  } catch (error) {
    console.error(error);
    alert('Ошибка при получении сводки');
  } finally {
    button.disabled = false;
    contentContainer.classList.remove('is-loading');
  }
}

/**
 * Восстановление оригинального содержимого и обновление видимости кнопок
 *
 * @param {HTMLElement} buttonContainer - Родительский контейнер кнопок
 * @param {HTMLElement} button - Кнопка "original"
 * @param {HTMLElement} contentBox - Блок с содержимым статьи
 * @param {string} originalContent - Оригинальное содержимое
 */
function restoreOriginalContent(buttonContainer, button, contentBox, originalContent) {
  contentBox.innerHTML = originalContent; // Возвращаем оригинальный текст
  updateButtonVisibility(buttonContainer, button);
}

/**
 * Обновление видимости кнопок: показываем все кнопки и скрываем текущую
 *
 * @param {HTMLElement} buttonContainer - Родительский контейнер кнопок
 * @param {HTMLElement} currentButton - Текущая кнопка для скрытия
 */
function updateButtonVisibility(buttonContainer, currentButton) {
  const buttons = buttonContainer.querySelectorAll('button[data-summary]');
  buttons.forEach(btn => btn.classList.remove('d-none')); // Показываем все кнопки
  currentButton.classList.add('d-none'); // Скрываем текущую кнопку
}
