export default class Tabs {
  constructor(selector) {
    this.selector = document.querySelector(selector);
    if (this.selector) {
      this.init();
    }
  }

  closeTabs(tabs) {
    tabs.forEach(tab => {
      tab.classList.remove('is-active');
    });
  }

  activateTab(tabId) {
    let tabNav = this.selector.querySelector(`[data-tab="${tabId}"]`);
    let tabContent = this.selector.querySelector(tabId);

    if (tabNav && tabContent) {
      this.closeTabs(this.selector.querySelectorAll('[data-tab]'));
      this.closeTabs(this.selector.querySelectorAll('.c-tab__content'));

      tabNav.classList.add('is-active');
      tabContent.classList.add('is-active');
    }
  }

  handleAnchorClick(event) {
    const href = event.target.getAttribute('href');
    if (href && href.startsWith('#')) {
      const tabId = href;
      if (this.selector.querySelector(`[data-tab="${tabId}"]`) && this.selector.querySelector(tabId)) {
        // event.preventDefault();
        this.activateTab(tabId);
      }
    }
  }

  init() {
    let tabNav = this.selector.querySelectorAll('[data-tab]');
    let tabContent = this.selector.querySelectorAll('.c-tab__content');

    tabNav.forEach(tab => {
      tab.addEventListener('click', (event) => {
        let targetData = event.target.getAttribute('data-tab');

        this.closeTabs(tabNav);
        this.closeTabs(tabContent);

        event.target.classList.add('is-active');
        this.selector.querySelector(targetData).classList.add('is-active');
      });
    });

    // Handle click on all anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(anchor => {
      anchor.addEventListener('click', (event) => this.handleAnchorClick(event));
    });
  }
}
