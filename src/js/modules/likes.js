const countFormatter = new Intl.NumberFormat(document.documentElement.lang || undefined);

const parseCount = (value) => {
  const parsed = Number.parseInt(String(value).replace(/[^\d]/g, ''), 10);

  return Number.isNaN(parsed) ? 0 : parsed;
};

const getCountElement = (button) =>
  button.querySelector('[data-reaction-count]') || button.querySelector('span');

const setButtonSelected = (button, isSelected) => {
  button.classList.toggle('selected', isSelected);
  button.classList.toggle('is-selected', isSelected);
  button.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
};

const setCount = (button, count) => {
  const countElement = getCountElement(button);

  if (!countElement) {
    return;
  }

  countElement.textContent = countFormatter.format(Math.max(0, count));
};

const getCounts = (buttons) =>
  buttons.reduce((counts, button) => {
    const likeType = button.getAttribute('data-like-type');
    const countElement = getCountElement(button);

    if (likeType && countElement) {
      counts[likeType] = parseCount(countElement.textContent);
    }

    return counts;
  }, {});

const applyState = (container, buttons, selectedReaction, counts) => {
  buttons.forEach((button) => {
    const likeType = button.getAttribute('data-like-type');

    if (!likeType) {
      return;
    }

    setButtonSelected(button, likeType === selectedReaction);

    if (Object.prototype.hasOwnProperty.call(counts, likeType)) {
      setCount(button, counts[likeType]);
    }
  });

  container.dataset.selectedReaction = selectedReaction || '';
};

const getOptimisticCounts = (buttons, previousReaction, nextReaction) => {
  const counts = getCounts(buttons);

  if (previousReaction && Object.prototype.hasOwnProperty.call(counts, previousReaction)) {
    counts[previousReaction] = Math.max(0, counts[previousReaction] - 1);
  }

  if (nextReaction && Object.prototype.hasOwnProperty.call(counts, nextReaction)) {
    counts[nextReaction] += 1;
  }

  return counts;
};

export default () => {
  document.querySelectorAll('.c-likes[data-post-id]').forEach((container) => {
    const buttons = Array.from(container.querySelectorAll('.c-likes__item[data-like-type]'));
    const endpoint = container.getAttribute('data-endpoint') || window.ajax?.restUrl;
    const nonce = container.getAttribute('data-nonce') || window.ajax?.restNonce;
    const postId = container.getAttribute('data-post-id');

    if (!buttons.length || !endpoint || !nonce || !postId) {
      return;
    }

    let isPending = false;

    buttons.forEach((button) => {
      button.addEventListener('click', async () => {
        if (isPending) {
          return;
        }

        const likeType = button.getAttribute('data-like-type');

        if (!likeType) {
          return;
        }

        const previousReaction = container.dataset.selectedReaction || '';
        const nextReaction = previousReaction === likeType ? '' : likeType;
        const previousCounts = getCounts(buttons);

        isPending = true;
        container.classList.add('is-loading');
        applyState(
          container,
          buttons,
          nextReaction,
          getOptimisticCounts(buttons, previousReaction, nextReaction),
        );

        try {
          const response = await fetch(endpoint, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
              'Content-Type': 'application/json',
              'X-WP-Nonce': nonce,
            },
            body: JSON.stringify({
              post_id: postId,
              like_type: likeType,
            }),
          });

          const data = await response.json();

          if (!response.ok || !data.success || !data.counts) {
            throw new Error(data.message || 'Reaction request failed.');
          }

          applyState(container, buttons, data.selected_reaction || '', data.counts);
        } catch (error) {
          applyState(container, buttons, previousReaction, previousCounts);
        } finally {
          isPending = false;
          container.classList.remove('is-loading');
        }
      });
    });
  });
};
