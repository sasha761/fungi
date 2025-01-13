// rating-stars.js

/**
 * Инициализирует логику скрытия #rating,
 * добавления "звёзд" и навешивания всех событий.
 *
 * @param {Object} [options] - объект настроек
 * @param {boolean} [options.reviewRatingRequired] - аналог wc_single_product_params.review_rating_required
 * @param {string} [options.requiredRatingText] - аналог wc_single_product_params.i18n_required_rating_text
 */
export function initRatingModule(options = {}) {
  // Распакуем нужные параметры:
  const {
    reviewRatingRequired = false,
    requiredRatingText = 'Выберите рейтинг', // на случай, если ничего не передадим
  } = options;

  // --- 1. Обработчик "init" для #rating ---
  function onInitRating() {
    const rating = document.getElementById('rating');
    if (!rating) return;

    // Скрываем исходный <select>
    rating.style.display = 'none';

    // Создаём элемент <p class="stars"> и <span> для ссылок
    const pStars = document.createElement('p');
    pStars.classList.add('stars');

    const span = document.createElement('span');

    // Генерируем ссылки-звёзды от 1 до 5
    for (let i = 1; i <= 5; i++) {
      const a = document.createElement('a');
      a.classList.add('star-' + i);
      a.href = '#';
      a.textContent = i; // текст ссылки — «1», «2» и т.п.
      span.appendChild(a);
    }

    pStars.appendChild(span);

    // Вставляем <p class="stars"> перед #rating
    rating.parentNode.insertBefore(pStars, rating);
  }

  // --- 2. Навешиваем прослушку на кастомное событие "init" для #rating ---
  const ratingElem = document.getElementById('rating');
  if (ratingElem) {
    ratingElem.addEventListener('init', onInitRating);
  }

  // --- 3. Делегирование клика по звёздам ---
  document.addEventListener('click', function (e) {
    // Проверяем, что кликнули именно по <a>, находящемуся в #respond p.stars
    if (e.target.matches('#respond p.stars a')) {
      e.preventDefault(); // аналог return false в jQuery

      const star = e.target;
      const respond = star.closest('#respond');
      if (!respond) return;

      // Находим скрытый <select id="rating">
      const rating = respond.querySelector('#rating');
      if (!rating) return;

      // Ставим выбранную оценку (число от 1 до 5)
      rating.value = star.textContent;

      // Убираем класс active у всех звёзд и ставим у текущей
      const container = star.closest('.stars');
      const allStars = container.querySelectorAll('a');
      allStars.forEach(a => a.classList.remove('active'));
      star.classList.add('active');

      // Добавляем класс .selected к блоку звёзд
      container.classList.add('selected');
    }
  });

  // --- 4. Проверка рейтинга при клике на "Отправить" ---
  document.addEventListener('click', function (e) {
    if (e.target.matches('#respond #submit')) {
      const respond = e.target.closest('#respond');
      if (!respond) return;

      const rating = respond.querySelector('#rating');
      if (!rating) return;

      // Если «рейтинг обязателен» и он не выбран — выводим alert
      const ratingValue = rating.value;
      if (reviewRatingRequired && !ratingValue) {
        e.preventDefault();
        window.alert(requiredRatingText);
      }
    }
  });

  // --- 5. Имитируем jQuery-триггер .trigger('init') для заданных элементов ---
  const initElements = document.querySelectorAll('.wc-tabs-wrapper, .woocommerce-tabs, #rating');
  initElements.forEach(el => {
    const event = new Event('init');
    el.dispatchEvent(event);
  });
}
