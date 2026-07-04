<header class="sticky top-0 z-40 bg-ardoise text-kraft border-b border-black/10">
  <div class="max-w-6xl mx-auto px-5 sm:px-8 h-16 flex items-center justify-between">
    <a href="<?php echo e(url('/')); ?>" class="flex items-center gap-2.5 font-display font-semibold text-lg">
      <span class="grid place-items-center w-9 h-9 rounded-full bg-moutarde text-ardoise font-display font-bold text-base ring-2 ring-kraft/30">USN</span>
      USN
    </a>
    <div class="flex items-center gap-4">
      <form action="<?php echo e(route('search')); ?>" method="GET" class="hidden md:flex items-center mr-4">
        <label for="search-friends" class="sr-only">Chercher un ami</label>
        <input id="search-friends" name="q" type="search" placeholder="Chercher un ami..." value="<?php echo e(request('q')); ?>" class="rounded-full border border-ardoise/20 bg-white/90 text-sm text-ardoise px-4 py-2 shadow-sm focus:border-moutarde focus:outline-none">
        <button type="submit" class="ml-2 inline-flex items-center justify-center rounded-full bg-moutarde px-3 py-2 text-sm text-ardoise hover:bg-moutarde/90 transition-colors">
          <i class="ti ti-search"></i>
        </button>
      </form>

      <nav class="hidden md:flex items-center gap-4 text-sm font-medium">
        <?php if(auth()->guard()->guest()): ?>
          <a href="<?php echo e(route('connexion')); ?>" class="hover:text-moutarde transition-colors">Connexion</a>
          <a href="<?php echo e(route('inscription')); ?>" class="px-4 py-2 rounded-md bg-moutarde text-ardoise hover:bg-moutarde/90 transition-colors">Inscription</a>
        <?php else: ?>
          <a href="<?php echo e(route('profil.show')); ?>" class="flex items-center gap-2 rounded-full border border-ardoise/10 bg-white px-4 py-2 text-ardoise transition-colors duration-150 hover:border-ardoise/40 hover:text-ardoise-dark">
            <i class="ti ti-user"></i>
            <span>Profil</span>
          </a>
          <a href="<?php echo e(route('messages')); ?>" class="relative flex items-center gap-2 rounded-full border border-ardoise/10 bg-white px-4 py-2 text-ardoise transition-colors duration-150 hover:border-ardoise/40 hover:text-ardoise-dark">
            <i class="ti ti-message"></i>
            <span>Messages</span>
            <?php if(!empty($unreadMessages) && $unreadMessages > 0): ?>
              <span class="absolute -right-2 -top-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-moutarde px-1.5 text-[10px] font-semibold text-ardoise unread-messages-badge"><?php echo e($unreadMessages); ?></span>
            <?php endif; ?>
          </a>
          <a href="<?php echo e(route('notifications')); ?>" class="relative flex items-center gap-2 rounded-full border border-ardoise/10 bg-white px-4 py-2 text-ardoise transition-colors duration-150 hover:border-ardoise/40 hover:text-ardoise-dark">
            <i class="ti ti-bell"></i>
            <span>Notifications</span>
            <?php if(!empty($unreadNotifications) && $unreadNotifications > 0): ?>
              <span class="absolute -right-2 -top-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-moutarde px-1.5 text-[10px] font-semibold text-ardoise unread-notifications-badge"><?php echo e($unreadNotifications); ?></span>
            <?php else: ?>
              <span class="absolute -right-2 -top-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-moutarde px-1.5 text-[10px] font-semibold text-ardoise unread-notifications-badge hidden">0</span>
            <?php endif; ?>
          </a>
          <a href="<?php echo e(route('stage')); ?>" class="flex items-center gap-2 rounded-full border border-ardoise/10 bg-white px-4 py-2 text-ardoise transition-colors duration-150 hover:border-ardoise/40 hover:text-ardoise-dark">
            <i class="ti ti-briefcase"></i>
            <span>Stage</span>
          </a>
          <a href="<?php echo e(route('profil.edit')); ?>" class="flex items-center gap-2 rounded-full border border-ardoise/10 bg-white px-4 py-2 text-ardoise transition-colors duration-150 hover:border-ardoise/40 hover:text-ardoise-dark">
            <i class="ti ti-settings"></i>
            <span>Paramètres</span>
          </a>
          <form method="POST" action="<?php echo e(route('logout')); ?>" class="inline">
            <?php echo csrf_field(); ?>
            <button type="submit" class="flex items-center gap-2 rounded-full border border-ardoise/10 bg-white px-4 py-2 text-ardoise transition-colors duration-150 hover:border-ardoise/40 hover:text-ardoise-dark">
              <i class="ti ti-logout"></i>
              <span>Déconnexion</span>
            </button>
          </form>
        <?php endif; ?>
      </nav>

      <details class="relative md:hidden">
        <summary class="list-none inline-flex h-11 w-11 cursor-pointer items-center justify-center rounded-full border border-ardoise/10 bg-white text-ardoise shadow-sm transition-colors hover:border-ardoise/40 hover:bg-ardoise/5">
          <i class="ti ti-menu-2"></i>
        </summary>
        <div class="mt-2 w-72 rounded-3xl border border-ardoise/10 bg-white p-4 shadow-lg animate-fade-in">
          <form action="<?php echo e(route('search')); ?>" method="GET" class="mb-4 flex items-center gap-2">
            <label for="mobile-search" class="sr-only">Chercher un ami</label>
            <input id="mobile-search" name="q" type="search" placeholder="Chercher un ami..." value="<?php echo e(request('q')); ?>" class="w-full rounded-full border border-ardoise/20 bg-kraft-light px-4 py-2 text-sm text-ardoise focus:border-moutarde focus:outline-none">
            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-moutarde px-3 py-2 text-sm text-ardoise hover:bg-moutarde/90 transition-colors">
              <i class="ti ti-search"></i>
            </button>
          </form>
          <nav class="flex flex-col gap-2">
            <?php if(auth()->guard()->guest()): ?>
              <a href="<?php echo e(route('connexion')); ?>" class="rounded-2xl px-4 py-3 text-sm text-ardoise transition-colors hover:bg-kraft-light hover:text-moutarde">Connexion</a>
              <a href="<?php echo e(route('inscription')); ?>" class="rounded-2xl px-4 py-3 text-sm text-ardoise bg-moutarde transition-colors hover:bg-moutarde/90">Inscription</a>
            <?php else: ?>
              <a href="<?php echo e(route('profil.show')); ?>" class="rounded-2xl px-4 py-3 text-sm text-ardoise transition-colors hover:bg-kraft-light hover:text-moutarde">Profil</a>
              <a href="<?php echo e(route('messages')); ?>" class="relative rounded-2xl px-4 py-3 text-sm text-ardoise transition-colors hover:bg-kraft-light hover:text-moutarde">
                Messages
                <?php if(!empty($unreadMessages) && $unreadMessages > 0): ?>
                  <span class="absolute right-4 top-3 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-moutarde px-1.5 text-[10px] font-semibold text-ardoise unread-messages-badge"><?php echo e($unreadMessages); ?></span>
                <?php endif; ?>
              </a>
              <a href="<?php echo e(route('notifications')); ?>" class="relative rounded-2xl px-4 py-3 text-sm text-ardoise transition-colors hover:bg-kraft-light hover:text-moutarde">
                Notifications
                <?php if(!empty($unreadNotifications) && $unreadNotifications > 0): ?>
                  <span class="absolute right-4 top-3 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-moutarde px-1.5 text-[10px] font-semibold text-ardoise unread-notifications-badge"><?php echo e($unreadNotifications); ?></span>
                <?php else: ?>
                  <span class="absolute right-4 top-3 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-moutarde px-1.5 text-[10px] font-semibold text-ardoise unread-notifications-badge hidden">0</span>
                <?php endif; ?>
              </a>
              <a href="<?php echo e(route('stage')); ?>" class="rounded-2xl px-4 py-3 text-sm text-ardoise transition-colors hover:bg-kraft-light hover:text-moutarde">Stage</a>
              <a href="<?php echo e(route('profil.edit')); ?>" class="rounded-2xl px-4 py-3 text-sm text-ardoise transition-colors hover:bg-kraft-light hover:text-moutarde">Paramètres</a>
              <form method="POST" action="<?php echo e(route('logout')); ?>" class="inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-full rounded-2xl px-4 py-3 text-left text-sm text-ardoise transition-colors hover:bg-kraft-light hover:text-moutarde">Déconnexion</button>
              </form>
            <?php endif; ?>
          </nav>
        </div>
      </details>
    </div>
  </div>
</header>
<?php /**PATH C:\Users\Poolski\Desktop\DEV Project\USN.ci\resources\views/partials/navbar.blade.php ENDPATH**/ ?>