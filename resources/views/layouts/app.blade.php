{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script>
    window.USN = window.USN || {};
    window.USN.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    window.USN.getMobileMenuLogs = function () { return []; };
    window.USN.downloadMobileMenuLogs = function () { console.warn('Mobile menu logs not initialized yet'); };
  </script>
  <title>@yield('title', 'USN')</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
  <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Caveat:wght@500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            ardoise:  { DEFAULT: '#1F2E26', light: '#2B3F33', dark: '#15201A' },
            kraft:    { DEFAULT: '#EFE6D3', light: '#F8F2E6', dark: '#E2D6BA' },
            sauge:    { DEFAULT: '#7A8C6B', dark: '#5E6E52' },
            moutarde: { DEFAULT: '#E2A33B', dark: '#C98826' },
            encre:    { DEFAULT: '#B8442E', dark: '#963823' },
            tinta:    '#221E18',
          },
          fontFamily: {
            display: ['"Space Grotesk"', 'sans-serif'],
            hand:    ['"Caveat"', 'cursive'],
          },
        }
      }
    }

    if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.classList.add('dark');
    }
  </script>
  <style>
    * { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body { min-height: 100vh; background-color: #E8E0CE; color: #221E18; transition: background-color .2s ease, color .2s ease; }
    html.dark body { background-color: #0F172A; color: #E2E8F0; }

    .skip-link {
      position: absolute;
      left: -999px;
      top: auto;
      width: 1px;
      height: 1px;
      overflow: hidden;
    }

    .skip-link:focus {
      left: 1rem;
      top: 1rem;
      width: auto;
      height: auto;
      padding: .75rem 1rem;
      z-index: 50;
      background: #E2A33B;
      color: #1F2E26;
      border-radius: .75rem;
      box-shadow: 0 10px 30px rgba(31,46,38,.18);
    }

    button:focus-visible,
    a:focus-visible,
    input:focus-visible,
    textarea:focus-visible {
      outline: 3px solid #E2A33B;
      outline-offset: 3px;
    }

    img, svg { max-width: 100%; display: block; }
    .container { max-width: 1200px; margin: 0 auto; padding: 0 1rem; }
    .page-card { background: rgba(255,255,255,.96); border: 1px solid #D4CABC; border-radius: 1.75rem; box-shadow: 0 20px 60px rgba(31,46,38,.08); }
    .page-card-dark { background: rgba(15,23,42,.9); border-color: rgba(148,163,184,.22); }
    .section-heading { margin-bottom: 1.25rem; font-size: .875rem; font-weight: 700; color: #1F2E26; letter-spacing: .12em; text-transform: uppercase; }
    .btn-primary { display: inline-flex; align-items: center; justify-content: center; gap: .5rem; padding: .75rem 1.25rem; border-radius: 9999px; background: #E2A33B; color: #1F2E26; font-weight: 700; transition: background .15s ease, transform .15s ease; }
    .btn-primary:hover { background: #C98826; }
    .btn-secondary { display: inline-flex; align-items: center; justify-content: center; gap: .5rem; padding: .75rem 1.25rem; border-radius: 9999px; border: 1px solid rgba(31,46,38,.16); background: #FFF; color: #1F2E26; transition: background .15s ease, color .15s ease; }
    .btn-secondary:hover { background: #F8F2E6; }
    .input-base { width: 100%; border-radius: 9999px; border: 1px solid #D4CABC; background: #F8F2E6; color: #1F2E26; padding: .75rem 1rem; transition: border-color .15s ease, box-shadow .15s ease; }
    .input-base:focus { outline: none; border-color: #E2A33B; box-shadow: 0 0 0 3px rgba(226,163,59,.16); }
    .text-muted { color: #6B7280; }
    .badge-pill { display: inline-flex; align-items: center; justify-content: center; border-radius: 9999px; padding: .25rem .75rem; font-size: .6875rem; font-weight: 700; background: #E2A33B; color: #1F2E26; }

    html.dark .bg-white { background-color: #111827 !important; }
    html.dark .bg-white\/90 { background-color: rgba(17, 24, 39, .9) !important; }
    html.dark .bg-white\/70 { background-color: rgba(17, 24, 39, .7) !important; }
    html.dark .bg-kraft-light { background-color: #111827 !important; }
    html.dark .bg-kraft-light\/80 { background-color: rgba(17, 24, 39, .8) !important; }
    html.dark .bg-ardoise\/10 { background-color: rgba(17, 24, 39, .14) !important; }
    html.dark .text-ardoise { color: #E2E8F0 !important; }
    html.dark .text-tinta { color: #CBD5E1 !important; }
    html.dark .text-gray-700 { color: #CBD5E1 !important; }
    html.dark .text-gray-600 { color: #CBD5E1 !important; }
    html.dark .text-gray-500 { color: #94A3B8 !important; }
    html.dark .text-gray-400 { color: #94A3B8 !important; }
    html.dark .text-gray-300 { color: #E2E8F0 !important; }
    html.dark .text-muted { color: #94A3B8 !important; }
    html.dark .border-ardoise\/20 { border-color: rgba(148,163,184,.2) !important; }
    html.dark .bg-kraft { background-color: #1E293B !important; }
    html.dark .border-kraft-dark\/30 { border-color: rgba(148,163,184,.3) !important; }
    html.dark .border-kraft-dark\/40 { border-color: rgba(148,163,184,.4) !important; }
    html.dark .bg-moutarde { background-color: #E2A33B !important; color: #1F2937 !important; }
    html.dark .btn-secondary { background: #111827; border-color: #374151; color: #E2E8F0; }
    html.dark .btn-secondary:hover { background: #111827; }
    html.dark .input-base { background: #111827; border-color: #374151; color: #E2E8F0; }
    html.dark .input-base:focus { border-color: #E2A33B; box-shadow: 0 0 0 3px rgba(226,163,59,.16); }
    html.dark .hover\:bg-kraft-light:hover { background-color: #111827 !important; }
    html.dark .hover\:text-moutarde:hover { color: #E2A33B !important; }
    .cover-zone {
      height: 220px;
      background-color: #1F2E26;
      position: relative;
      overflow: hidden;
    }
    .cover-zone img.cover-img {
      width: 100%; height: 100%; object-fit: cover; opacity: .85;
    }
    .cover-pattern {
      position: absolute; inset: 0;
      background-image: radial-gradient(rgba(239,230,211,.07) 1px, transparent 1.4px);
      background-size: 16px 16px;
    }
    .avatar-ring {
      width: 108px; height: 108px;
      border-radius: 50%;
      border: 4px solid #F8F2E6;
      overflow: hidden;
      flex-shrink: 0;
      background: #2B3F33;
      display: flex; align-items: center; justify-content: center;
      font-size: 38px; font-weight: 700;
      color: #E2A33B;
      box-shadow: 0 3px 10px rgba(0,0,0,.25);
      margin-top: -54px;
      position: relative; z-index: 10;
    }
    .avatar-ring img { width: 100%; height: 100%; object-fit: cover; }
    .stat-band {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      border: 1px solid #D4CABC;
      border-radius: 10px;
      overflow: hidden;
      background: #F8F2E6;
    }
    .stat-item {
      padding: 10px 6px;
      text-align: center;
      border-right: 1px solid #D4CABC;
    }
    .stat-item:last-child { border-right: none; }
    .bar-fill {
      height: 6px;
      background: #E2A33B;
      border-radius: 99px;
      transition: width .6s ease;
    }
    .tab-btn {
      padding: 10px 16px;
      font-size: .8125rem;
      font-weight: 500;
      color: #6B7280;
      border-bottom: 2px solid transparent;
      cursor: pointer;
      white-space: nowrap;
      transition: color .15s, border-color .15s;
      background: none; border: none;
    }
    .tab-btn:hover { color: #1F2E26; }
    .tab-btn.active { color: #1F2E26; border-bottom-color: #E2A33B; }
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }
    .skill-bar-bg { flex: 1; height: 5px; background: #D4CABC; border-radius: 99px; overflow: hidden; }
    .skill-bar-fill { height: 100%; background: #7A8C6B; border-radius: 99px; }
    .post-card { background: #F8F2E6; border: 1px solid #D4CABC; border-radius: 12px; margin-bottom: 14px; overflow: hidden; }
    .post-action-btn {
      flex: 1; padding: 9px;
      display: flex; align-items: center; justify-content: center; gap: 6px;
      font-size: .8125rem; color: #6B7280;
      cursor: pointer; transition: background .15s;
      background: none; border: none;
      font-family: 'Space Grotesk', sans-serif;
    }
    .post-action-btn:hover { background: rgba(31,46,38,.06); color: #1F2E26; }
    .post-action-btn.liked { color: #B8442E; }
    .pin-card {
      background: #2B3F33;
      border-radius: 8px;
      padding: 12px 14px;
      margin-bottom: 8px;
      position: relative;
    }
    .pin-dot {
      width: 10px; height: 10px; border-radius: 50%;
      background: #B8442E;
      position: absolute; top: -5px; left: 50%; transform: translateX(-50%);
      box-shadow: 0 1px 3px rgba(0,0,0,.4);
    }
    .tl-dot { width: 8px; height: 8px; border-radius: 50%; background: #E2A33B; margin-top: 5px; flex-shrink: 0; }
    .friend-avatar {
      width: 100%; aspect-ratio: 1;
      border-radius: 8px;
      background: #2B3F33;
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; font-weight: 600;
      color: #E2A33B;
      overflow: hidden;
    }
    .friend-avatar img { width: 100%; height: 100%; object-fit: cover; }
    @media (prefers-reduced-motion: reduce) { *, html { animation: none !important; transition: none !important; scroll-behavior: auto !important; } }
    .fade-in { animation: fadeIn .4s ease both; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    @media (max-width: 768px) {
      .stat-band { grid-template-columns: repeat(3, 1fr); }
      .stat-item:nth-child(3) { border-right: none; }
      .stat-item:nth-child(4), .stat-item:nth-child(5) { border-top: 1px solid #D4CABC; }
      .desktop-only { display: none !important; }
    }
    @media (max-width: 640px) {
      .avatar-ring { width: 84px; height: 84px; font-size: 28px; margin-top: -42px; }
      .cover-zone { height: 160px; }
    }
  </style>
  @stack('head')
</head>
<body class="font-display antialiased" @auth data-authenticated="true" @endauth>
  <a href="#main-content" class="skip-link">Aller au contenu</a>
  @include('partials.navbar')
  <button id="theme-toggle-floating" type="button" aria-label="Basculer le thème" aria-pressed="false" class="fixed z-50 grid place-items-center rounded-full border border-ardoise/10 bg-white p-3 text-ardoise shadow-lg transition-colors duration-150 hover:border-ardoise/40 hover:text-ardoise-dark focus:outline-none focus:ring-2 focus:ring-moutarde/40" style="top:1rem; right:1rem; width:44px; height:44px; touch-action:none; cursor:grab;">
    <i id="theme-toggle-icon" class="ti ti-moon"></i>
  </button>
  <main id="main-content" class="max-w-5xl mx-auto px-4 sm:px-6 pb-10">
    @yield('content')
  </main>
  @stack('scripts')
  @auth
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const body = document.body;
        const isAuthenticated = body.getAttribute('data-authenticated') === 'true';

        if (!isAuthenticated) {
          return;
        }

        let previousState = null;

        const updateBadges = async function () {
          try {
            const response = await fetch('{{ route('live.updates') }}', {
              headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
              },
            });

            if (!response.ok) {
              return;
            }

            const data = await response.json();
            const currentState = {
              unreadMessages: Number(data.unreadMessages || 0),
              unreadNotifications: Number(data.unreadNotifications || 0),
              pendingFriendRequests: Number(data.pendingFriendRequests || 0),
            };

            document.querySelectorAll('.unread-messages-badge').forEach(function (badge) {
              if (currentState.unreadMessages > 0) {
                badge.textContent = currentState.unreadMessages;
                badge.classList.remove('hidden');
              } else {
                badge.textContent = '0';
                badge.classList.add('hidden');
              }
            });

            document.querySelectorAll('.unread-notifications-badge').forEach(function (badge) {
              if (currentState.unreadNotifications > 0) {
                badge.textContent = currentState.unreadNotifications;
                badge.classList.remove('hidden');
              } else {
                badge.textContent = '0';
                badge.classList.add('hidden');
              }
            });

            const shouldRefresh = previousState && (
              currentState.unreadMessages !== previousState.unreadMessages ||
              currentState.unreadNotifications !== previousState.unreadNotifications ||
              currentState.pendingFriendRequests !== previousState.pendingFriendRequests
            );

            previousState = currentState;

            if (shouldRefresh && ['\/messages', '\/notifications', '\/profil'].some((segment) => window.location.pathname.includes(segment))) {
              window.location.reload();
            }
          } catch (error) {
            console.error('Live updates error', error);
          }
        };

        updateBadges();
        updateThemeControls();
        window.setInterval(updateBadges, 10000);
      });
    </script>
  @endauth
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const themeToggleButton = document.getElementById('theme-toggle-floating');
      const themeIcon = document.getElementById('theme-toggle-icon');
      const THEME_KEY = 'theme';
      const POSITION_KEY = 'themeToggleFloatingPosition';
      let dragging = false;
      let dragMoved = false;
      let startX = 0;
      let startY = 0;
      let startLeft = 0;
      let startTop = 0;

      if (!themeToggleButton || !themeIcon) {
        return;
      }

      const updateIcon = function (dark) {
        themeIcon.className = dark ? 'ti ti-sun' : 'ti ti-moon';
      };

      const applyTheme = function (theme) {
        const dark = theme === 'dark';
        document.documentElement.classList.toggle('dark', dark);
        updateIcon(dark);
      };

      const clampPosition = function (left, top) {
        const maxLeft = window.innerWidth - themeToggleButton.offsetWidth - 8;
        const maxTop = window.innerHeight - themeToggleButton.offsetHeight - 8;
        return {
          left: Math.min(Math.max(left, 8), maxLeft),
          top: Math.min(Math.max(top, 8), maxTop),
        };
      };

      const savePosition = function () {
        const left = parseInt(themeToggleButton.style.left, 10);
        const top = parseInt(themeToggleButton.style.top, 10);
        if (!Number.isNaN(left) && !Number.isNaN(top)) {
          localStorage.setItem(POSITION_KEY, JSON.stringify({ left, top }));
        }
      };

      const restorePosition = function () {
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

      const storedTheme = localStorage.getItem(THEME_KEY);
      if (storedTheme) {
        applyTheme(storedTheme);
      } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        applyTheme('dark');
      } else {
        applyTheme('light');
      }

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
        // animate backdrop
        mobileMenuBackdrop?.classList.remove('opacity-0');
        mobileMenuBackdrop?.classList.add('opacity-100');
        mobileMenuPanel.classList.remove('translate-x-full');
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
        mobileMenuPanel.classList.remove('translate-x-0');
        mobileMenuPanel.classList.add('translate-x-full');
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

      mobileMenuOpen?.addEventListener('click', openMobileMenu);
      mobileMenuClose?.addEventListener('click', closeMobileMenu);
      mobileMenuBackdrop?.addEventListener('click', closeMobileMenu);

      document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
          closeMobileMenu();
        }
      });
    });
  </script>
</body>
</html>
