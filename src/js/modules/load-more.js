// import imagesLoaded from 'imagesloaded';

const postTemplate = (post) => {
  const {
    id, link, title, price, video, thumb_md, thumb_xl, is_stock, percent, is_sale
  } = post;

  let imageHtml = '';
  let saleHtml  = '';
  let stockHtml  = '';

  if (is_sale) {
    saleHtml = `
      <span class="c-product__sale">-${percent}%</span>
    `;
  }

  if (is_stock === 'outofstock') {
    stockHtml = `
      <span class="c-product__sale">Нет в наличии</span>
    `;
  }

  if (video) {
    imageHtml = `
      <a href="${link}" class="c-product__video">
        <video src="${video}" muted playsinline autoplay loop poster="${thumb_md}"></video>
        ${saleHtml}
        ${stockHtml}
      </a>
    `;
  } else {
    imageHtml = ` 
      <a href="${link}" class="c-product__img">
        <picture>
          <source
            data-srcset="${thumb_xl}"
            media="(min-width: 426px)"
          >
          <source
            data-srcset="${thumb_md}"
            media="(max-width: 425px)"
          >
          <img
            src="data:image/png;base64,TEVITGhbV0IyeWs4cHlvSmFkUiouN2tDTWRuag=="
            data-src="${thumb_md}"
            alt="${title}"
            width="342"
            height="435"
            class="lazy"
          >
        </picture>
        ${saleHtml}
        ${stockHtml}
      </a>
    `;
  }

  return `
    <div class="col-lg-3 col-md-4 col-sm-6 col-6 u-col js-gallery-item">
      <div class="c-product" data-id="${id}">
        ${imageHtml}

        <div class="c-product__text">
          <p class="c-product__text-title">${title}</p>
          <p class="c-price">${price}</p>
        </div>
      </div>
    </div>
  `;
};

const createElementFromHTML = (htmlString) => {
  const div = document.createElement('div');
  div.innerHTML = htmlString.trim();

  return div.firstChild;
};

function loadMore() {
  const loadMoreBtn = document.querySelector('.js-show-more-product');
  
  if (loadMoreBtn) {
    const pagination = document.querySelector('.c-pagination ul');
    const paginationLi = pagination.querySelectorAll('li');
    const grid = document.querySelector('.js-load-more');
    loadMoreBtn.addEventListener('click', () => {
      document.querySelector('.l-shop__product .js-load-more-icon').classList.add('is-show');
      
      const postCount = parseInt(loadMoreBtn.getAttribute('data-count'), 10);
      const allPostCount = parseInt(loadMoreBtn.getAttribute('data-all-posts'), 10);
      const allPageCount = parseInt(loadMoreBtn.getAttribute('data-all-pages'), 10);
      const currentPage = parseInt(loadMoreBtn.getAttribute('data-current-page'), 10);
      const postType = loadMoreBtn.getAttribute('data-post-type');
      const postSlug = loadMoreBtn.getAttribute('data-slug');
      const category = loadMoreBtn.getAttribute('data-category');

      if (postCount >= allPostCount) {
        loadMoreBtn.style.display = 'none';
        document.querySelector('.l-shop__product .js-load-more-icon').classList.remove('is-show');
        return;
      }

      showMore({ postType, postCount, postSlug, category, currentPage });
    });

    const showMore = async ({ postType, postCount, postSlug, category, currentPage }) => {
      const params = new URLSearchParams();
      params.set('postType', postType);
      params.set('offset', postCount * currentPage);
      params.set('slug', postSlug);
      params.set('taxonomy', category);
      params.set('page', currentPage + 1);

      const response = await fetch(`${window.ajax.url}?action=showMoreProducts`, {
        method: 'POST',
        body: params,
      });

      const result = await response.json();
      // console.log(result);
      if (result.products === 'nomore') {
        document.querySelector('.l-shop__product .js-load-more-icon').classList.remove('is-show');
        loadMoreBtn.style.display = 'none';  
        return;
      }

      const fragment = document.createDocumentFragment();

      result.products.forEach((post) => {
        const elem = createElementFromHTML(postTemplate(post));

        fragment.appendChild(elem);
      });

      grid.appendChild(fragment);
      lazyLoadInstance.update();

      document.querySelector('.l-shop__product .js-load-more-icon').classList.remove('is-show');

      loadMoreBtn.setAttribute('data-current-page', currentPage + 1);
      const resultFilter = Array.from(paginationLi).find(element => element.innerText == currentPage + 1 );
      if (resultFilter) {
        resultFilter.classList.add('is-active');
      }
      
    }
  }
}

export default loadMore;