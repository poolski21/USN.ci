<header class="sticky top-0 z-40 border-b border-black/10 bg-ardoise/95 text-kraft shadow-[0_12px_40px_rgba(31,46,38,0.16)] backdrop-blur">
  <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-5 sm:px-8">
    <a href="{{ url('/') }}" class="flex items-center gap-2.5 font-display text-lg font-semibold transition hover:opacity-90">
      <span class="grid h-9 w-9 place-items-center rounded-full bg-moutarde text-base font-bold text-ardoise ring-2 ring-kraft/30">USN</span>
      USN
    </a>
    <div class="flex items-center gap-3 sm:gap-4">
      <form action="{{ route('search') }}" method="GET" class="mr-2 hidden items-center md:flex" role="search" aria-label="Recherche d'amis">
        <label for="search-friends" class="sr-only">Chercher un ami</label>
        <input id="search-friends" name="q" type="search" placeholder="Chercher un ami..." value="{{ request('q') }}" aria-label="Chercher un ami" class="w-56 rounded-full border border-white/20 bg-white/90 px-4 py-2 text-sm text-ardoise shadow-sm transition focus:border-moutarde focus:outline-none focus:ring-2 focus:ring-moutarde/30" />
        <button type="submit" class="ml-2 inline-flex items-center justify-center rounded-full bg-moutarde px-3 py-2 text-sm text-ardoise transition hover:bg-moutarde/90" aria-label="Lancer la recherche">
          <i class="ti ti-search" aria-hidden="true"></i>
        </button>
      </form>

      <nav class="hidden items-center gap-2 text-sm font-medium md:flex" aria-label="Navigation principale">
        @guest
          <a href="{{ route('connexion') }}" class="nav-pill">Connexion</a>
          <a href="{{ route('inscription') }}" class="inline-flex items-center justify-center rounded-full bg-moutarde px-4 py-2 text-sm font-semibold text-ardoise shadow-sm transition hover:bg-moutarde/90">Inscription</a>
        @else
          <a href="{{ route('profil.show') }}" class="nav-pill {{ request()->routeIs('profil.*') ? 'active' : '' }}" aria-current="{{ request()->routeIs('profil.*') ? 'page' : 'false' }}">
            <i class="ti ti-user" aria-hidden="true"></i>
            <span>Profil</span>
          </a>
          <a href="{{ route('messages') }}" class="nav-pill relative {{ request()->routeIs('messages') ? 'active' : '' }}" aria-current="{{ request()->routeIs('messages') ? 'page' : 'false' }}" aria-label="Messages">
            <i class="ti ti-message" aria-hidden="true"></i>
            <span>Messages</span>
            @if(!empty($unreadMessages) && $unreadMessages > 0)
              <span class="absolute -right-2 -top-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-moutarde px-1.5 text-[10px] font-semibold text-ardoise unread-messages-badge">{{ $unreadMessages }}</span>
            @endif
          </a>
          <a href="{{ route('notifications') }}" class="nav-pill relative {{ request()->routeIs('notifications') ? 'active' : '' }}" aria-current="{{ request()->routeIs('notifications') ? 'page' : 'false' }}" aria-label="Notifications">
            <i class="ti ti-bell" aria-hidden="true"></i>
            <span>Notifications</span>
            @if(!empty($unreadNotifications) && $unreadNotifications > 0)
              <span class="absolute -right-2 -top-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-moutarde px-1.5 text-[10px] font-semibold text-ardoise unread-notifications-badge">{{ $unreadNotifications }}</span>
            @else
              <span class="absolute -right-2 -top-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-moutarde px-1.5 text-[10px] font-semibold text-ardoise unread-notifications-badge hidden">0</span>
            @endif
          </a>
          <a href="{{ route('profil.edit') }}" class="nav-pill {{ request()->routeIs('profil.edit') ? 'active' : '' }}" aria-current="{{ request()->routeIs('profil.edit') ? 'page' : 'false' }}">
            <i class="ti ti-settings" aria-hidden="true"></i>
            <span>Paramètres</span>
          </a>
          @if(auth()->check() && auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="nav-pill {{ request()->routeIs('admin.*') ? 'active' : '' }}" aria-current="{{ request()->routeIs('admin.*') ? 'page' : 'false' }}">
              <i class="ti ti-shield-check" aria-hidden="true"></i>
              <span>Admin</span>
            </a>
          @endif
          <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="nav-pill">
              <i class="ti ti-logout"></i>
              <span>Déconnexion</span>
            </button>
          </form>
        @endguest
      </nav>

      <button id="mobile-menu-open" type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-ardoise/10 bg-white text-ardoise shadow-sm transition hover:border-ardoise/40 hover:bg-ardoise/5 md:hidden" aria-label="Ouvrir le menu mobile" aria-controls="mobile-menu-drawer" aria-expanded="false">
        <i class="ti ti-menu-2" aria-hidden="true"></i>
      </button>

      <div id="mobile-menu-drawer" class="fixed inset-0 z-50 hidden md:hidden" aria-hidden="true">
        <div id="mobile-menu-backdrop" class="pointer-events-none fixed inset-0 bg-slate-950/75 transition-opacity duration-300 opacity-0 z-40"></div>
        <aside id="mobile-menu-panel" class="fixed left-0 top-16 bottom-0 h-auto w-full max-w-xs border-r border-ardoise/10 bg-white p-4 shadow-2xl transition-transform duration-300 ease-in-out transform -translate-x-full dark:border-slate-700 dark:bg-slate-950 sm:max-w-sm z-50" aria-hidden="true" style="padding-top: env(safe-area-inset-top, 0.75rem); -webkit-overflow-scrolling: touch; overflow-y: auto;">
          <div class="mb-4 flex items-center justify-between">
            <div>
              <p class="text-sm font-semibold text-ardoise">Menu</p>
              <p class="text-xs text-ardoise/70">Accédez rapidement à votre espace</p>
            </div>
            <button id="mobile-menu-close" type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-ardoise/10 bg-white text-ardoise transition hover:border-ardoise/40 hover:bg-ardoise/5" aria-label="Fermer le menu mobile">
              <i class="ti ti-x" aria-hidden="true"></i>
            </button>
          </div>
          <form action="{{ route('search') }}" method="GET" class="mb-4 flex items-center gap-2">
            <label for="mobile-search" class="sr-only">Chercher un ami</label>
            <input id="mobile-search" name="q" type="search" placeholder="Chercher un ami..." value="{{ request('q') }}" class="input-base w-full bg-kraft-light text-ardoise dark:bg-slate-800 dark:text-gray-100" />
            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-moutarde px-3 py-2 text-ardoise transition hover:bg-moutarde/90" aria-label="Recherche">
              <i class="ti ti-search"></i>
            </button>
          </form>
          <nav class="flex flex-col gap-2">
            @guest
              <a href="{{ route('connexion') }}" class="rounded-2xl px-4 py-3 text-sm text-ardoise transition hover:bg-kraft-light hover:text-moutarde">Connexion</a>
              <a href="{{ route('inscription') }}" class="rounded-2xl bg-moutarde px-4 py-3 text-sm font-semibold text-ardoise transition hover:bg-moutarde/90">Inscription</a>
            @else
              <a href="{{ route('profil.show') }}" class="rounded-2xl px-4 py-3 text-sm text-ardoise transition hover:bg-kraft-light hover:text-moutarde {{ request()->routeIs('profil.*') ? 'bg-kraft-light text-moutarde' : '' }}">Profil</a>
              <a href="{{ route('messages') }}" class="relative rounded-2xl px-4 py-3 text-sm text-ardoise transition hover:bg-kraft-light hover:text-moutarde {{ request()->routeIs('messages') ? 'bg-kraft-light text-moutarde' : '' }}">
                Messages
                @if(!empty($unreadMessages) && $unreadMessages > 0)
                  <span class="absolute right-4 top-3 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-moutarde px-1.5 text-[10px] font-semibold text-ardoise unread-messages-badge">{{ $unreadMessages }}</span>
                @endif
              </a>
              <a href="{{ route('friend.requests.show') }}" class="rounded-2xl px-4 py-3 text-sm text-ardoise transition hover:bg-kraft-light hover:text-moutarde">Demandes d'ami</a>
              <a href="{{ route('notifications') }}" class="relative rounded-2xl px-4 py-3 text-sm text-ardoise transition hover:bg-kraft-light hover:text-moutarde {{ request()->routeIs('notifications') ? 'bg-kraft-light text-moutarde' : '' }}">
                Notifications
                @if(!empty($unreadNotifications) && $unreadNotifications > 0)
                  <span class="absolute right-4 top-3 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-moutarde px-1.5 text-[10px] font-semibold text-ardoise unread-notifications-badge">{{ $unreadNotifications }}</span>
                @else
                  <span class="absolute right-4 top-3 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-moutarde px-1.5 text-[10px] font-semibold text-ardoise unread-notifications-badge hidden">0</span>
                @endif
              </a>
              <a href="{{ route('profil.edit') }}" class="rounded-2xl px-4 py-3 text-sm text-ardoise transition hover:bg-kraft-light hover:text-moutarde {{ request()->routeIs('profil.edit') ? 'bg-kraft-light text-moutarde' : '' }}">Paramètres</a>
              @if(auth()->check() && auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="rounded-2xl px-4 py-3 text-sm text-ardoise transition hover:bg-kraft-light hover:text-moutarde {{ request()->routeIs('admin.*') ? 'bg-kraft-light text-moutarde' : '' }}">Admin</a>
              @endif
              <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="w-full rounded-2xl px-4 py-3 text-left text-sm text-ardoise transition hover:bg-kraft-light hover:text-moutarde">Déconnexion</button>
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
