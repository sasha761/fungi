/**
 * Инициализирует скрытие/показ шапки при скролле за счёт "накопления" скролла.
 *
 * @param {Object} [options]                 - настройки
 * @param {number} [options.threshold=50]    - порог (в пикселях), после которого добавляем/убираем класс
 * @param {string} [options.hiddenClass='scrolled-down'] - класс, который добавляем при "прокрутке вниз"
 */
export default function initStickyHeader(options = {}) {
  const { 
    threshold = 50,
    hiddenClass = 'scrolled-down'
  } = options;

  const header = document.getElementById('mainHeader');
  if (!header) return;

  // Точка последнего скролла
  let lastScrollTop = document.documentElement.scrollTop;
  // "Аккумулятор" проскролленных пикселей в одном направлении
  let scrollDistance = 0;

  window.addEventListener('scroll', () => {
    const currentScrollTop = document.documentElement.scrollTop;

    // Считаем разницу
    if (currentScrollTop < lastScrollTop) {
      // Идём вверх
      if (scrollDistance > 0) {
        // Если до этого шли вниз, сбрасываем
        scrollDistance = 0;
      }
      // Прибавляем в минус (прокрутку вверх)
      scrollDistance -= (lastScrollTop - currentScrollTop);
    } else {
      // Идём вниз
      if (scrollDistance < 0) {
        // Если до этого шли вверх, сбрасываем
        scrollDistance = 0;
      }
      // Прибавляем положительно (прокрутку вниз)
      scrollDistance += (currentScrollTop - lastScrollTop);
    }

    // Если scrollDistance > threshold -> "скрываем" шапку
    if (scrollDistance > threshold) {
      header.classList.add(hiddenClass);
    }
    // Если scrollDistance < -threshold -> "показываем" шапку
    else if (scrollDistance < -threshold) {
      header.classList.remove(hiddenClass);
    }

    // Запоминаем позицию
    lastScrollTop = Math.max(currentScrollTop, 0);
  });
}
