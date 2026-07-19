<header class="sticky top-0 z-40 bg-ardoise text-kraft border-b border-black/10">
  <div class="max-w-6xl mx-auto px-5 sm:px-8 h-16 flex items-center justify-between">
    <a href="{{ url('/') }}" class="flex items-center gap-2.5 font-display font-semibold text-lg">
      <span class="grid place-items-center w-9 h-9 rounded-full bg-moutarde text-ardoise font-display font-bold text-base ring-2 ring-kraft/30">USN</span>
      USN
    </a>
    <div class="flex items-center gap-4">
      <form action="{{ route('search') }}" method="GET" class="hidden md:flex items-center mr-4" role="search" aria-label="Recherche d'amis">
        <label for="search-friends" class="sr-only">Chercher un ami</label>
        <input id="search-friends" name="q" type="search" placeholder="Chercher un ami..." value="{{ request('q') }}" aria-label="Chercher un ami" class="rounded-full border border-ardoise/20 bg-white/90 text-sm text-ardoise px-4 py-2 shadow-sm focus:border-moutarde focus:outline-none" />
        <button type="submit" class="ml-2 inline-flex items-center justify-center rounded-full bg-moutarde px-3 py-2 text-sm text-ardoise hover:bg-moutarde/90 transition-colors" aria-label="Lancer la recherche">
          <i class="ti ti-search" aria-hidden="true"></i>
        </button>
      </form>

      <nav class="hidden md:flex items-center gap-4 text-sm font-medium" aria-label="Navigation principale">
        @guest
          <a href="{{ route('connexion') }}" class="text-ardoise hover:text-moutarde transition-colors">Connexion</a>
          <a href="{{ route('inscription') }}" class="inline-flex items-center justify-center rounded-full bg-moutarde px-4 py-2 text-sm text-ardoise hover:bg-moutarde/90 transition-colors">Inscription</a>
        @else
          <a href="{{ route('profil.show') }}" class="flex items-center gap-2 rounded-full border border-ardoise/10 bg-white px-4 py-2 text-ardoise transition-colors duration-150 hover:border-ardoise/40 hover:text-moutarde">
            <i class="ti ti-user" aria-hidden="true"></i>
            <span>Profil</span>
          </a>
          <a href="{{ route('messages') }}" class="relative flex items-center gap-2 rounded-full border border-ardoise/10 bg-white px-4 py-2 text-ardoise transition-colors duration-150 hover:border-ardoise/40 hover:text-moutarde" aria-label="Messages">
            <i class="ti ti-message" aria-hidden="true"></i>
            <span>Messages</span>
            @if(!empty($unreadMessages) && $unreadMessages > 0)
              <span class="absolute -right-2 -top-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-moutarde px-1.5 text-[10px] font-semibold text-ardoise unread-messages-badge">{{ $unreadMessages }}</span>
            @endif
          </a>
          <a href="{{ route('notifications') }}" class="relative flex items-center gap-2 rounded-full border border-ardoise/10 bg-white px-4 py-2 text-ardoise transition-colors duration-150 hover:border-ardoise/40 hover:text-moutarde" aria-label="Notifications">
            <i class="ti ti-bell" aria-hidden="true"></i>
            <span>Notifications</span>
            @if(!empty($unreadNotifications) && $unreadNotifications > 0)
              <span class="absolute -right-2 -top-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-moutarde px-1.5 text-[10px] font-semibold text-ardoise unread-notifications-badge">{{ $unreadNotifications }}</span>
            @else
              <span class="absolute -right-2 -top-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-moutarde px-1.5 text-[10px] font-semibold text-ardoise unread-notifications-badge hidden">0</span>
            @endif
          </a>
          <a href="{{ route('profil.edit') }}" class="flex items-center gap-2 rounded-full border border-ardoise/10 bg-white px-4 py-2 text-ardoise transition-colors duration-150 hover:border-ardoise/40 hover:text-moutarde">
            <i class="ti ti-settings" aria-hidden="true"></i>
            <span>Paramètres</span>
          </a>
          @if(auth()->check() && auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 rounded-full border border-ardoise/10 bg-white px-4 py-2 text-ardoise transition-colors duration-150 hover:border-ardoise/40 hover:text-moutarde">
              <i class="ti ti-shield-check" aria-hidden="true"></i>
              <span>Admin</span>
            </a>
          @endif
          <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="flex items-center gap-2 rounded-full border border-ardoise/10 bg-white px-4 py-2 text-ardoise transition-colors duration-150 hover:border-ardoise/40 hover:text-moutarde">
              <i class="ti ti-logout"></i>
              <span>Déconnexion</span>
            </button>
          </form>
        @endguest
      </nav>

      <button id="mobile-menu-open" type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-ardoise/10 bg-white text-ardoise shadow-sm transition-colors hover:border-ardoise/40 hover:bg-ardoise/5 md:hidden" aria-label="Ouvrir le menu mobile" aria-controls="mobile-menu-drawer" aria-expanded="false">
        <i class="ti ti-menu-2" aria-hidden="true"></i>
      </button>

      <div id="mobile-menu-drawer" class="fixed inset-0 z-50 hidden md:hidden" aria-hidden="true">
        <div id="mobile-menu-backdrop" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity duration-300 opacity-0 z-50"></div>
        <aside id="mobile-menu-panel" class="fixed left-0 top-16 bottom-0 h-auto w-full max-w-xs bg-white shadow-2xl border-r border-ardoise/10 p-4 transition-transform duration-300 ease-in-out transform -translate-x-full dark:bg-slate-950 dark:border-slate-700 sm:max-w-sm z-60" aria-hidden="true" style="padding-top: env(safe-area-inset-top, 0.75rem); -webkit-overflow-scrolling: touch; overflow-y: auto;">
          <div class="flex items-center justify-between mb-4">
            <span class="text-sm font-semibold text-ardoise">Menu</span>
            <button id="mobile-menu-close" type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-ardoise/10 bg-white text-ardoise transition-colors hover:border-ardoise/40 hover:bg-ardoise/5" aria-label="Fermer le menu mobile">
              <i class="ti ti-x" aria-hidden="true"></i>
            </button>
          </div>
          <form action="{{ route('search') }}" method="GET" class="mb-4 flex items-center gap-2">
            <label for="mobile-search" class="sr-only">Chercher un ami</label>
            <input id="mobile-search" name="q" type="search" placeholder="Chercher un ami..." value="{{ request('q') }}" class="input-base bg-kraft-light text-ardoise dark:bg-slate-800 dark:text-gray-100 w-full" />
            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-moutarde px-3 py-2 text-ardoise hover:bg-moutarde/90 transition-colors" aria-label="Recherche">
              <i class="ti ti-search"></i>
            </button>
          </form>
          <nav class="flex flex-col gap-2">
            @guest
              <a href="{{ route('connexion') }}" class="rounded-2xl px-4 py-3 text-sm text-ardoise transition-colors hover:bg-kraft-light hover:text-moutarde">Connexion</a>
              <a href="{{ route('inscription') }}" class="rounded-2xl px-4 py-3 text-sm text-ardoise bg-moutarde transition-colors hover:bg-moutarde/90">Inscription</a>
            @else
              <a href="{{ route('profil.show') }}" class="rounded-2xl px-4 py-3 text-sm text-ardoise transition-colors hover:bg-kraft-light hover:text-moutarde">Profil</a>
              <a href="{{ route('messages') }}" class="relative rounded-2xl px-4 py-3 text-sm text-ardoise transition-colors hover:bg-kraft-light hover:text-moutarde">
                Messages
                @if(!empty($unreadMessages) && $unreadMessages > 0)
                  <span class="absolute right-4 top-3 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-moutarde px-1.5 text-[10px] font-semibold text-ardoise unread-messages-badge">{{ $unreadMessages }}</span>
                @endif
              </a>
              <a href="{{ route('notifications') }}" class="relative rounded-2xl px-4 py-3 text-sm text-ardoise transition-colors hover:bg-kraft-light hover:text-moutarde">
                Notifications
                @if(!empty($unreadNotifications) && $unreadNotifications > 0)
                  <span class="absolute right-4 top-3 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-moutarde px-1.5 text-[10px] font-semibold text-ardoise unread-notifications-badge">{{ $unreadNotifications }}</span>
                @else
                  <span class="absolute right-4 top-3 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-moutarde px-1.5 text-[10px] font-semibold text-ardoise unread-notifications-badge hidden">0</span>
                @endif
              </a>
              <a href="{{ route('profil.edit') }}" class="rounded-2xl px-4 py-3 text-sm text-ardoise transition-colors hover:bg-kraft-light hover:text-moutarde">Paramètres</a>
              @if(auth()->check() && auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="rounded-2xl px-4 py-3 text-sm text-ardoise transition-colors hover:bg-kraft-light hover:text-moutarde">Admin</a>
              @endif
              <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="w-full rounded-2xl px-4 py-3 text-left text-sm text-ardoise transition-colors hover:bg-kraft-light hover:text-moutarde">Déconnexion</button>
              </form>
            @endguest
          </nav>
        </aside>
      </div>
      <!-- left edge sensor to detect pull-to-open gestures -->
      <div id="mobile-menu-edge" class="fixed left-0 top-0 bottom-0 w-4 z-30 md:hidden" aria-hidden="true"></div>
    </div>
  </div>
</header>
