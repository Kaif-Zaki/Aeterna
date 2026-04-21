(function () {
  const root = document.documentElement;
  const nav = document.getElementById('main-nav');
  const navLinks = document.getElementById('nav-links');
  const navMenuToggle = document.getElementById('nav-menu-toggle');
  const navBackdrop = document.getElementById('nav-backdrop');
  const themeToggle = document.getElementById('theme-toggle');
  const scrollProgress = document.getElementById('scroll-progress');
  // Keep JS breakpoint in sync with CSS mobile nav media queries.
  const mobileBreakpoint = window.matchMedia('(max-width: 768px)');

  function applyTheme(theme) {
    root.classList.remove('light', 'dark');
    root.classList.add(theme);
    localStorage.setItem('theme', theme);

    if (themeToggle) {
      themeToggle.textContent = theme === 'dark' ? 'Light Mode' : 'Dark Mode';
    }
  }

  const storedTheme = localStorage.getItem('theme') || 'light';
  applyTheme(storedTheme);

  function initMediaReveal() {
    const media = Array.from(document.querySelectorAll('img'));
    media.forEach((img) => {
      img.classList.add('media-reveal');

      if (img.complete) {
        img.classList.add('media-ready');
        return;
      }

      img.addEventListener('load', function () {
        img.classList.add('media-ready');
      }, { once: true });

      img.addEventListener('error', function () {
        // Avoid hidden broken-image icon.
        img.classList.add('media-ready');
      }, { once: true });
    });
  }

  if (themeToggle) {
    themeToggle.addEventListener('click', function () {
      const nextTheme = root.classList.contains('dark') ? 'light' : 'dark';
      applyTheme(nextTheme);
    });
  }

  function isMobileNav() {
    return mobileBreakpoint.matches;
  }

  function syncNavStateForViewport() {
    if (isMobileNav()) {
      // Always start closed on mobile to avoid stale open state.
      closeNavMenu();
      return;
    }

    // Desktop: ensure navigation is visible and overlay state is cleared.
    if (nav) {
      nav.classList.remove('nav-open');
    }
    document.body.classList.remove('nav-open-lock');
    if (navBackdrop) {
      navBackdrop.classList.add('hidden');
    }
    if (navMenuToggle) {
      navMenuToggle.setAttribute('aria-expanded', 'false');
    }
  }

  function closeNavMenu() {
    if (!nav) return;
    nav.classList.remove('nav-open');
    document.body.classList.remove('nav-open-lock');
    if (navBackdrop) {
      navBackdrop.classList.add('hidden');
    }
    if (navMenuToggle) {
      navMenuToggle.setAttribute('aria-expanded', 'false');
    }
  }

  function openNavMenu() {
    if (!nav) return;
    nav.classList.add('nav-open');
    document.body.classList.add('nav-open-lock');
    if (navBackdrop) {
      navBackdrop.classList.remove('hidden');
    }
    if (navMenuToggle) {
      navMenuToggle.setAttribute('aria-expanded', 'true');
    }
  }

  function toggleNavMenu() {
    if (!nav || !isMobileNav()) return;
    if (nav.classList.contains('nav-open')) {
      closeNavMenu();
    } else {
      openNavMenu();
    }
  }

  if (navMenuToggle) {
    navMenuToggle.addEventListener('click', function () {
      toggleNavMenu();
    });
  }

  if (navBackdrop) {
    navBackdrop.addEventListener('click', function () {
      closeNavMenu();
    });
  }

  if (navLinks) {
    navLinks.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', function () {
        closeNavMenu();
      });
    });
  }

  document.addEventListener('click', function (e) {
    if (!isMobileNav() || !nav || !nav.classList.contains('nav-open')) return;
    if (!(e.target instanceof Node)) return;
    if (!nav.contains(e.target)) {
      closeNavMenu();
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      closeNavMenu();
    }
  });

  if (mobileBreakpoint.addEventListener) {
    mobileBreakpoint.addEventListener('change', function () {
      syncNavStateForViewport();
    });
  } else if (mobileBreakpoint.addListener) {
    mobileBreakpoint.addListener(function () {
      syncNavStateForViewport();
    });
  }

  // Handle browser back-forward cache restore.
  window.addEventListener('pageshow', function () {
    syncNavStateForViewport();
  });

  function updateScrollUi() {
    const y = window.scrollY || 0;

    if (nav) {
      if (y > 50) {
        nav.classList.add('nav-scrolled');
      } else {
        nav.classList.remove('nav-scrolled');
      }
    }

    if (scrollProgress) {
      const h = document.documentElement.scrollHeight - window.innerHeight;
      const p = h > 0 ? (y / h) * 100 : 0;
      scrollProgress.style.width = p + '%';
    }
  }

  window.addEventListener('scroll', updateScrollUi, { passive: true });
  updateScrollUi();
  syncNavStateForViewport();
  initMediaReveal();

  const page = document.body.getAttribute('data-page');

  if (page === 'faq') {
    document.querySelectorAll('.faq-trigger').forEach((btn) => {
      btn.addEventListener('click', function () {
        const item = this.closest('.faq-item');
        if (!item) return;
        const isOpen = item.classList.contains('open');
        document.querySelectorAll('.faq-item.open').forEach((el) => el.classList.remove('open'));
        if (!isOpen) item.classList.add('open');
      });
    });
  }

  if (page === 'collections') {
    const filterButtons = Array.from(document.querySelectorAll('[data-filter]'));
    const cards = Array.from(document.querySelectorAll('[data-card-category]'));
    const modal = document.getElementById('collection-modal');
    const modalImg = document.getElementById('modal-image');
    const modalCategory = document.getElementById('modal-category');
    const modalTitle = document.getElementById('modal-title');
    const modalDesc = document.getElementById('modal-desc');

    filterButtons.forEach((btn) => {
      btn.addEventListener('click', function () {
        const filter = this.getAttribute('data-filter') || 'All';
        filterButtons.forEach((b) => b.classList.remove('border-accent', 'text-foreground'));
        this.classList.add('border-accent', 'text-foreground');

        cards.forEach((card) => {
          const category = card.getAttribute('data-card-category');
          const show = filter === 'All' || category === filter;
          card.classList.toggle('hidden', !show);
        });
      });
    });

    document.querySelectorAll('[data-open-modal]').forEach((btn) => {
      btn.addEventListener('click', function () {
        if (!modal || !modalImg || !modalCategory || !modalTitle || !modalDesc) return;
        modalImg.setAttribute('src', this.getAttribute('data-img') || '');
        modalImg.setAttribute('alt', this.getAttribute('data-title') || 'Collection item');
        modalCategory.textContent = this.getAttribute('data-category') || '';
        modalTitle.textContent = this.getAttribute('data-title') || '';
        modalDesc.textContent = this.getAttribute('data-desc') || '';
        modal.classList.remove('hidden');
      });
    });

    document.querySelectorAll('[data-close-modal]').forEach((btn) => {
      btn.addEventListener('click', function () {
        modal && modal.classList.add('hidden');
      });
    });

    if (modal) {
      modal.addEventListener('click', function (e) {
        if (e.target === modal) modal.classList.add('hidden');
      });
    }
  }

  if (page === 'gallery') {
    const images = Array.from(document.querySelectorAll('[data-gallery-image]'));
    const lightbox = document.getElementById('gallery-lightbox');
    const lightboxImg = document.getElementById('lightbox-image');
    const lightboxAlt = document.getElementById('lightbox-alt');
    const prevBtn = document.getElementById('lightbox-prev');
    const nextBtn = document.getElementById('lightbox-next');
    const closeBtns = document.querySelectorAll('[data-close-lightbox]');
    let index = -1;

    function showAt(i) {
      if (!lightbox || !lightboxImg || !lightboxAlt || images.length === 0) return;
      index = (i + images.length) % images.length;
      const item = images[index];
      lightboxImg.setAttribute('src', item.getAttribute('data-src') || '');
      lightboxImg.setAttribute('alt', item.getAttribute('data-alt') || 'Gallery image');
      lightboxAlt.textContent = item.getAttribute('data-alt') || '';
      lightbox.classList.remove('hidden');
    }

    images.forEach((img, i) => {
      img.addEventListener('click', function () {
        showAt(i);
      });
    });

    if (prevBtn) prevBtn.addEventListener('click', function (e) { e.stopPropagation(); showAt(index - 1); });
    if (nextBtn) nextBtn.addEventListener('click', function (e) { e.stopPropagation(); showAt(index + 1); });
    closeBtns.forEach((btn) => btn.addEventListener('click', function () { lightbox && lightbox.classList.add('hidden'); }));

    if (lightbox) {
      lightbox.addEventListener('click', function (e) {
        if (e.target === lightbox) lightbox.classList.add('hidden');
      });
    }
  }
})();
