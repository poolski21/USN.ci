<script>
  document.addEventListener('DOMContentLoaded', function () {
    const themeToggleButton = document.getElementById('theme-toggle-floating');
    const themeIcon = document.getElementById('theme-toggle-icon');
    const themeButtonAvailable = themeToggleButton && themeIcon;
    const THEME_KEY = 'theme';
    const POSITION_KEY = 'themeToggleFloatingPosition';
    let dragging = false;
    let dragMoved = false;
    let startX = 0;
    let startY = 0;
    let startLeft = 0;
    let startTop = 0;

    const updateIcon = function (dark) {
      if (!themeIcon) return;
      themeIcon.className = dark ? 'ti ti-sun' : 'ti ti-moon';
    };

    const applyTheme = function (theme) {
      const dark = theme === 'dark';
      document.documentElement.classList.toggle('dark', dark);
      updateIcon(dark);
    };

    const clampPosition = function (left, top) {
      if (!themeToggleButton) {
        return { left: 8, top: 8 };
      }
      const maxLeft = window.innerWidth - themeToggleButton.offsetWidth - 8;
      const maxTop = window.innerHeight - themeToggleButton.offsetHeight - 8;
      return {
        left: Math.min(Math.max(left, 8), maxLeft),
        top: Math.min(Math.max(top, 8), maxTop),
      };
    };

    const savePosition = function () {
      if (!themeToggleButton) {
        return;
      }
      const left = parseInt(themeToggleButton.style.left, 10);
      const top = parseInt(themeToggleButton.style.top, 10);
      if (!Number.isNaN(left) && !Number.isNaN(top)) {
        localStorage.setItem(POSITION_KEY, JSON.stringify({ left, top }));
      }
    };

    const restorePosition = function () {
      if (!themeToggleButton) {
        return;
      }
      const stored = localStorage.getItem(POSITION_KEY);
      if (!stored) {
        return;
      }
      try {
        const position = JSON.parse(stored);
        if (typeof position.left === 'number' && typeof position.top === 'number') {
          const clamped = clampPosition(position.left, position.top);
          themeToggleButton.style.left = clamped.left + 'px';
          themeToggleButton.style.top = clamped.top + 'px';
          themeToggleButton.style.right = 'auto';
        }
      } catch (err) {
        console.error('theme button position parse error', err);
      }
    };

    const updateThemeControls = function () {
      const storedTheme = localStorage.getItem(THEME_KEY);
      if (storedTheme) {
        applyTheme(storedTheme);
      } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        applyTheme('dark');
      } else {
        applyTheme('light');
      }
    };

    updateThemeControls();
    if (themeButtonAvailable) {
      restorePosition();
      themeToggleButton.style.cursor = 'grab';
      themeToggleButton.addEventListener('pointerdown', function (event) {
        dragging = true;
        dragMoved = false;
        startX = event.clientX;
        startY = event.clientY;
        const rect = themeToggleButton.getBoundingClientRect();
        startLeft = rect.left;
        startTop = rect.top;
        themeToggleButton.setPointerCapture(event.pointerId);
        themeToggleButton.style.cursor = 'grabbing';
        event.preventDefault();
      });

      themeToggleButton.addEventListener('pointermove', function (event) {
        if (!dragging) {
          return;
        }
        const dx = event.clientX - startX;
        const dy = event.clientY - startY;
        if (Math.abs(dx) > 5 || Math.abs(dy) > 5) {
          dragMoved = true;
        }
        const newPos = clampPosition(startLeft + dx, startTop + dy);
        themeToggleButton.style.left = newPos.left + 'px';
        themeToggleButton.style.top = newPos.top + 'px';
        themeToggleButton.style.right = 'auto';
      });

      themeToggleButton.addEventListener('pointerup', function (event) {
        if (!dragging) {
          return;
        }
        dragging = false;
        themeToggleButton.releasePointerCapture(event.pointerId);
        themeToggleButton.style.cursor = 'grab';
        if (dragMoved) {
          savePosition();
        }
      });

      themeToggleButton.addEventListener('pointercancel', function (event) {
        if (!dragging) {
          return;
        }
        dragging = false;
        themeToggleButton.releasePointerCapture(event.pointerId);
        themeToggleButton.style.cursor = 'grab';
        savePosition();
      });

      themeToggleButton.addEventListener('click', function () {
        if (dragMoved) {
          dragMoved = false;
          return;
        }
        const nextTheme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
        applyTheme(nextTheme);
        localStorage.setItem(THEME_KEY, nextTheme);
      });
    }

    const mobileMenuOpen = document.getElementById('mobile-menu-open');
    const mobileMenuClose = document.getElementById('mobile-menu-close');
    const mobileMenuDrawer = document.getElementById('mobile-menu-drawer');
    const mobileMenuBackdrop = document.getElementById('mobile-menu-backdrop');
    const mobileMenuPanel = document.getElementById('mobile-menu-panel');

    // Mobile menu logging helpers: store recent events in localStorage for debugging
    const MOBILE_MENU_LOGS_KEY = 'USN_mobile_menu_logs';

    const saveMobileMenuLog = (entry) => {
      try {
        const maxEntries = 200;
        const raw = localStorage.getItem(MOBILE_MENU_LOGS_KEY);
        const arr = raw ? JSON.parse(raw) : [];
        arr.push(Object.assign({ ts: new Date().toISOString() }, entry));
        if (arr.length > maxEntries) arr.splice(0, arr.length - maxEntries);
        localStorage.setItem(MOBILE_MENU_LOGS_KEY, JSON.stringify(arr));
        console.info('[mobile-menu] saved log (total):', arr.length);
      } catch (e) {
        console.warn('[mobile-menu] could not save log', e);
      }
    };

    window.USN = window.USN || {};
    window.USN.getMobileMenuLogs = () => {
      try { return JSON.parse(localStorage.getItem(MOBILE_MENU_LOGS_KEY) || '[]'); } catch (e) { return []; }
    };
    window.USN.downloadMobileMenuLogs = () => {
      try {
        const logs = window.USN.getMobileMenuLogs();
        const blob = new Blob([JSON.stringify(logs, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'mobile-menu-logs.json';
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
      } catch (e) {
        console.warn('downloadMobileMenuLogs error', e);
      }
    };

    const openMobileMenu = () => {
      if (!mobileMenuDrawer || !mobileMenuPanel) return;
      // compute header height to position panel below navbar
      const header = document.querySelector('header.sticky');
      const headerRect = header ? header.getBoundingClientRect() : { height: 64, bottom: 64 };
      const headerHeight = Math.round(headerRect.height || 64);
      mobileMenuPanel.style.top = headerHeight + 'px';
      mobileMenuPanel.style.height = `calc(100vh - ${headerHeight}px)`;
      // debug logs for mobile menu positioning
      try {
        console.log('[mobile-menu] open - headerRect:', headerRect);
        console.log('[mobile-menu] open - headerHeight:', headerHeight);
        console.log('[mobile-menu] open - panel inline top/height before open:', mobileMenuPanel.style.top, mobileMenuPanel.style.height);
      } catch (e) {
        // ignore logging errors
      }

      // persist initial state to localStorage for later inspection
      try {
        saveMobileMenuLog({
          event: 'open.start',
          header: { height: headerRect.height, top: headerRect.top, bottom: headerRect.bottom },
          panelInline: { top: mobileMenuPanel.style.top, height: mobileMenuPanel.style.height }
        });
      } catch (e) {}
      mobileMenuDrawer.classList.remove('hidden');
      // make backdrop interactive and animate it
      mobileMenuBackdrop?.classList.remove('pointer-events-none');
      mobileMenuBackdrop?.classList.remove('opacity-0');
      mobileMenuBackdrop?.classList.add('opacity-100');
      // ensure left-drawer classes: remove closed (-translate-x-full) and add open (translate-x-0)
      mobileMenuPanel.classList.remove('-translate-x-full');
      mobileMenuPanel.classList.add('translate-x-0');
      mobileMenuOpen?.setAttribute('aria-expanded', 'true');
      document.body.style.overflow = 'hidden';
      setTimeout(() => {
        try {
          const panelTop = getComputedStyle(mobileMenuPanel).top;
          const panelHeight = getComputedStyle(mobileMenuPanel).height;
          const backdropOpacity = mobileMenuBackdrop ? getComputedStyle(mobileMenuBackdrop).opacity : null;
          console.log('[mobile-menu] open - panel computed top:', panelTop);
          console.log('[mobile-menu] open - panel computed height:', panelHeight);
          console.log('[mobile-menu] open - backdrop opacity:', backdropOpacity);
          saveMobileMenuLog({
            event: 'open.complete',
            panel: { top: panelTop, height: panelHeight },
            backdropOpacity: backdropOpacity
          });
        } catch (e) {}
      }, 50);
    };

    const closeMobileMenu = () => {
      if (!mobileMenuDrawer || !mobileMenuPanel) return;
      mobileMenuBackdrop?.classList.remove('opacity-100');
      mobileMenuBackdrop?.classList.add('opacity-0');
      mobileMenuBackdrop?.classList.add('pointer-events-none');
      // close left-drawer: remove open and add closed
      mobileMenuPanel.classList.remove('translate-x-0');
      mobileMenuPanel.classList.add('-translate-x-full');
      mobileMenuOpen?.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
      setTimeout(() => {
        mobileMenuDrawer?.classList.add('hidden');
        // reset inline styles
        if (mobileMenuPanel) {
          mobileMenuPanel.style.top = '';
          mobileMenuPanel.style.height = '';
        }
      }, 300);
    };

    mobileMenuOpen?.addEventListener('click', function () {
      const expanded = mobileMenuOpen.getAttribute('aria-expanded') === 'true';
      if (expanded) {
        closeMobileMenu();
      } else {
        openMobileMenu();
      }
    });
    mobileMenuClose?.addEventListener('click', closeMobileMenu);
    mobileMenuBackdrop?.addEventListener('click', closeMobileMenu);

    // Close the panel when any link inside is clicked (behaviour like welcome page)
    try {
      mobileMenuPanel?.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
          closeMobileMenu();
        });
      });
    } catch (e) {
      // ignore if panel not present or no links
    }

    // Drag to close/open logic for left drawer
    const mobileMenuEdge = document.getElementById('mobile-menu-edge');
    let draggingPanel = false;
    let dragStartX = 0;
    let currentTranslate = 0;
    const panelWidth = () => mobileMenuPanel ? mobileMenuPanel.getBoundingClientRect().width : 0;

    const startDrag = (clientX) => {
      draggingPanel = true;
      dragStartX = clientX;
      // disable transition during drag
      if (mobileMenuPanel) mobileMenuPanel.style.transition = 'none';
    };

    const dragMove = (clientX) => {
      if (!draggingPanel || !mobileMenuPanel) return;
      const dx = clientX - dragStartX;
      // when open, dx negative => move left to close. when opening from edge, dx positive => move right to reveal.
      // clamp so panel doesn't move beyond fully open (0) or fully closed (-panelWidth)
      const w = panelWidth();
      let translate = Math.min(0, Math.max(-w, dx));
      // if we started from closed edge sensor, dx will be negative; handle separately in edge handlers.
      mobileMenuPanel.style.transform = `translateX(${translate}px)`;
      currentTranslate = translate;
    };

    const endDrag = () => {
      if (!draggingPanel || !mobileMenuPanel) return;
      draggingPanel = false;
      // restore transition
      mobileMenuPanel.style.transition = '';
      const threshold = panelWidth() * 0.3; // 30% to trigger close/open
      // if dragged left beyond threshold -> close
      if (currentTranslate < -threshold) {
        closeMobileMenu();
      } else {
        // revert to open
        mobileMenuPanel.style.transform = '';
        mobileMenuPanel.classList.remove('-translate-x-full');
        mobileMenuPanel.classList.add('translate-x-0');
        mobileMenuOpen?.setAttribute('aria-expanded', 'true');
      }
      currentTranslate = 0;
    };

    // panel pointer handlers (for closing drag)
    mobileMenuPanel?.addEventListener('pointerdown', function (e) {
      // only start drag if pointer is near left edge of panel (to avoid interfering with inner interactions)
      const rect = mobileMenuPanel.getBoundingClientRect();
      if (e.clientX - rect.left < 30) {
        startDrag(e.clientX);
        mobileMenuPanel.setPointerCapture(e.pointerId);
      }
    });
    mobileMenuPanel?.addEventListener('pointermove', function (e) { dragMove(e.clientX); });
    mobileMenuPanel?.addEventListener('pointerup', function (e) { endDrag(); mobileMenuPanel.releasePointerCapture(e.pointerId); });
    mobileMenuPanel?.addEventListener('pointercancel', endDrag);

    // edge sensor to start opening by dragging from far left
    mobileMenuEdge?.addEventListener('pointerdown', function (e) {
      // reveal drawer immediately
      mobileMenuDrawer.classList.remove('hidden');
      mobileMenuBackdrop?.classList.remove('opacity-0');
      mobileMenuBackdrop?.classList.add('opacity-100');
      // ensure panel is positioned offscreen left
      mobileMenuPanel.classList.remove('translate-x-0');
      mobileMenuPanel.classList.add('-translate-x-full');
      startDrag(e.clientX);
    });
    document.addEventListener('pointermove', function (e) { if (draggingPanel) dragMove(e.clientX); });
    document.addEventListener('pointerup', function (e) { if (draggingPanel) endDrag(); });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeMobileMenu();
      }
    });
  });
</script>
