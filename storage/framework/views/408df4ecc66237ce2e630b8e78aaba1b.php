
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo e($user->prenom); ?> <?php echo e($user->nom); ?> — USN</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Caveat:wght@500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
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
</script>

<style>
  /* ─── BASE ─── */
  * { box-sizing: border-box; }
  html { scroll-behavior: smooth; }
  body { background-color: #E8E0CE; color: #221E18; }

  /* ─── COVER ─── */
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

  /* ─── AVATAR ─── */
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

  /* ─── STAT BAND ─── */
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

  /* ─── PROGRESS BAR ─── */
  .bar-fill {
    height: 6px;
    background: #E2A33B;
    border-radius: 99px;
    transition: width .6s ease;
  }

  /* ─── TABS ─── */
  .tab-btn {
    padding: 10px 16px;
    font-size: .8125rem;
    font-weight: 500;
    color: #6B7280;
    border-bottom: 2px solid transparent;
    cursor: pointer;
    white-space: nowrap;
    transition: color .15s, border-color .15s;
    background: none; border-top: none; border-left: none; border-right: none;
  }
  .tab-btn:hover { color: #1F2E26; }
  .tab-btn.active { color: #1F2E26; border-bottom-color: #E2A33B; }
  .tab-panel { display: none; }
  .tab-panel.active { display: block; }

  /* ─── SKILL BARS ─── */
  .skill-bar-bg { flex: 1; height: 5px; background: #D4CABC; border-radius: 99px; overflow: hidden; }
  .skill-bar-fill { height: 100%; background: #7A8C6B; border-radius: 99px; }

  /* ─── POST CARD ─── */
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

  /* ─── PIN CARD ─── */
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

  /* ─── TIMELINE ─── */
  .tl-dot { width: 8px; height: 8px; border-radius: 50%; background: #E2A33B; margin-top: 5px; flex-shrink: 0; }

  /* ─── FRIENDS GRID ─── */
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

  /* ─── ANIMATIONS ─── */
  @media (prefers-reduced-motion: reduce) { *, html { animation: none !important; transition: none !important; scroll-behavior: auto !important; } }
  .fade-in { animation: fadeIn .4s ease both; }
  @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }

  /* ─── RESPONSIVE ─── */
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
</head>

<body class="font-display antialiased">


<?php echo $__env->make('partials.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="max-w-5xl mx-auto px-4 sm:px-6 pb-10">

  <?php if(session('status')): ?>
  <div class="mx-auto mb-4 max-w-5xl rounded-2xl border border-ardoise/10 bg-white/90 px-4 py-3 text-sm text-ardoise shadow-sm">
    <?php echo e(session('status')); ?>

  </div>
  <?php endif; ?>
  <div id="friend-action-feedback" class="mx-auto mb-4 hidden max-w-5xl rounded-2xl border border-ardoise/10 bg-white/90 px-4 py-3 text-sm text-ardoise shadow-sm"></div>

  <?php if($errors->any()): ?>
  <div class="mx-auto mb-4 max-w-5xl rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 shadow-sm">
    <ul class="list-disc pl-5">
      <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <li><?php echo e($error); ?></li>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
  </div>
  <?php endif; ?>

  
  <div class="cover-zone rounded-b-xl">
    <div class="cover-pattern"></div>
    <?php if($user->cover_photo): ?>
      <img src="<?php echo e(asset('storage/'.$user->cover_photo)); ?>" alt="Photo de couverture" class="cover-img">
    <?php endif; ?>
    
    <div class="absolute top-3 right-3 flex items-center gap-1.5 bg-white/10 border border-white/20 text-kraft text-xs px-3 py-1 rounded-lg backdrop-blur-sm">
      <i class="ti ti-shield-check text-moutarde"></i>
      Compte vérifié · <?php echo e($user->universite ?? 'UIST'); ?>

    </div>
    
    <?php if(auth()->id() === $user->id): ?>
    <form action="<?php echo e(route('profil.cover.update')); ?>" method="POST" enctype="multipart/form-data" class="absolute bottom-3 right-3">
      <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
      <label class="flex items-center gap-1.5 cursor-pointer bg-ardoise/75 hover:bg-ardoise border border-white/20 text-kraft text-xs px-3 py-1.5 rounded-lg transition-colors">
        <i class="ti ti-camera"></i> Modifier la couverture
        <input type="file" name="cover_photo" accept="image/*" class="hidden" onchange="this.form.submit()">
      </label>
    </form>
    <?php endif; ?>
  </div>

  
  <div class="bg-kraft-light border border-kraft-dark/40 border-t-0 rounded-b-xl px-5 pb-4">

    <div class="flex flex-wrap items-end gap-4 pt-0">

      
      <div class="avatar-ring relative">
        <?php if($user->avatar || $user->cover_photo): ?>
          <img src="<?php echo e(asset('storage/'.($user->avatar ?? $user->cover_photo))); ?>" alt="Avatar de <?php echo e($user->prenom); ?>">
        <?php else: ?>
          <?php echo e(strtoupper(substr($user->prenom,0,1).substr($user->nom,0,1))); ?>

        <?php endif; ?>

        <span class="absolute bottom-0 right-0 inline-flex h-4 w-4 rounded-full ring-2 ring-white <?php echo e($isOnline ? 'bg-emerald-500' : 'bg-red-500'); ?>" title="<?php echo e($isOnline ? 'En ligne' : 'Hors ligne'); ?>"></span>

        <?php if(auth()->id() === $user->id): ?>
          <form action="<?php echo e(route('profil.avatar.update')); ?>" method="POST" enctype="multipart/form-data" class="absolute -bottom-2 right-0">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>
            <label class="flex items-center justify-center w-10 h-10 rounded-full bg-ardoise/90 border border-white/30 text-kraft shadow-lg cursor-pointer hover:bg-ardoise transition-colors">
              <i class="ti ti-camera text-sm"></i>
              <input type="file" name="avatar" accept="image/*" class="hidden" onchange="this.form.submit()">
            </label>
          </form>
        <?php endif; ?>
      </div>

      
      <div class="flex-1 pb-2 min-w-0">
        <h1 class="text-xl font-semibold text-ardoise leading-tight">
          <?php echo e($user->prenom); ?> <?php echo e($user->nom); ?>

        </h1>
        <p class="text-sm text-gray-500 mt-0.5">
          <?php if($user->ville): ?> · <i class="ti ti-map-pin text-xs"></i> <?php echo e($user->ville); ?> <?php endif; ?>
          <?php if(! $isOnline && $user->last_seen): ?>
            · Dernière connexion <?php echo e($user->last_seen->translatedFormat('d F Y H:i')); ?>

          <?php endif; ?>
        </p>
        <span class="inline-flex items-center gap-1.5 mt-2 bg-ardoise text-moutarde text-xs font-medium px-2.5 py-1 rounded-md">
          <i class="ti ti-code text-xs"></i>
          <?php echo e($user->filiere ?? 'Informatique'); ?> · <?php echo e($user->niveau ?? 'L2'); ?>

        </span>
      </div>

      
      <div class="flex gap-2 pb-2 desktop-only">
        <?php if(auth()->id() === $user->id): ?>
          <a href="<?php echo e(route('profil.edit')); ?>"
             class="flex items-center gap-1.5 px-4 py-2 bg-ardoise text-kraft text-sm font-medium rounded-lg hover:bg-ardoise-light transition-colors">
            <i class="ti ti-edit"></i> Modifier le profil
          </a>
          <button class="flex items-center gap-1.5 px-3 py-2 border border-ardoise/40 text-ardoise text-sm rounded-lg hover:bg-ardoise/5 transition-colors">
            <i class="ti ti-share"></i> Partager
          </button>
        <?php else: ?>
          <?php if($isFriend): ?>
            <a href="<?php echo e(route('messages.conversation', $user->handle ?? $user->id)); ?>" class="flex items-center gap-1.5 px-4 py-2 bg-ardoise text-kraft text-sm font-medium rounded-lg hover:bg-ardoise-light transition-colors">
              <i class="ti ti-mail"></i> Envoyer un message
            </a>
            <div class="flex items-center gap-1.5 rounded-lg border border-ardoise/20 bg-white/70 px-3 py-2 text-sm text-sauge">
              <i class="ti ti-user-check"></i> Ami
            </div>
          <?php elseif($requestStatus === 'pending' && $friendRequest && $friendRequest->sender_id === auth()->id()): ?>
            <div class="flex items-center gap-1.5 rounded-lg border border-ardoise/20 bg-white/70 px-3 py-2 text-sm text-gray-600">
              <i class="ti ti-clock"></i> Invitation envoyée
            </div>
          <?php elseif($requestStatus === 'pending' && $friendRequest && $friendRequest->receiver_id === auth()->id()): ?>
            <div class="flex gap-2">
              <form class="friend-action-form" action="<?php echo e(route('friend.requests.accept', $friendRequest->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" data-success-text="Acceptée" class="flex items-center gap-1.5 px-4 py-2 bg-ardoise text-kraft text-sm font-medium rounded-lg hover:bg-ardoise-light transition-colors">
                  <i class="ti ti-check"></i> Accepter
                </button>
              </form>
              <form class="friend-action-form" action="<?php echo e(route('friend.requests.decline', $friendRequest->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" data-success-text="Refusée" class="flex items-center gap-1.5 px-4 py-2 border border-ardoise/40 text-ardoise text-sm rounded-lg hover:bg-ardoise/5 transition-colors">
                  <i class="ti ti-x"></i> Refuser
                </button>
              </form>
            </div>
          <?php else: ?>
            <form class="friend-action-form" action="<?php echo e(route('friend.requests.send', $user->handle ?? $user->id)); ?>" method="POST">
              <?php echo csrf_field(); ?>
              <button type="submit" data-success-text="Invitation envoyée" class="flex items-center gap-1.5 px-4 py-2 bg-moutarde text-ardoise text-sm font-medium rounded-lg hover:bg-moutarde/90 transition-colors">
                <i class="ti ti-user-plus"></i> Ajouter comme ami
              </button>
            </form>
          <?php endif; ?>
        <?php endif; ?>
        <button class="flex items-center px-3 py-2 border border-ardoise/40 text-ardoise text-sm rounded-lg hover:bg-ardoise/5 transition-colors">
          <i class="ti ti-dots"></i>
        </button>
      </div>
    </div>

    
    <div class="stat-band mt-4">
      <div class="stat-item">
        <p class="font-bold text-lg text-ardoise"><?php echo e($stats['connexions'] ?? 0); ?></p>
        <p class="text-xs uppercase tracking-wider text-gray-500 mt-0.5">Connexions</p>
      </div>
      <div class="stat-item">
        <p class="font-bold text-lg text-ardoise"><?php echo e($stats['groupes'] ?? 0); ?></p>
        <p class="text-xs uppercase tracking-wider text-gray-500 mt-0.5">Groupes</p>
      </div>
      <div class="stat-item">
        <p class="font-bold text-lg text-ardoise"><?php echo e($stats['projets'] ?? 0); ?></p>
        <p class="text-xs uppercase tracking-wider text-gray-500 mt-0.5">Projets</p>
      </div>
      <div class="stat-item">
        <p class="font-bold text-lg text-ardoise"><?php echo e($stats['contributions'] ?? 0); ?></p>
        <p class="text-xs uppercase tracking-wider text-gray-500 mt-0.5">Contributions</p>
      </div>
      <div class="stat-item">
        <p class="font-bold text-lg text-ardoise"><?php echo e($stats['evenements'] ?? 0); ?></p>
        <p class="text-xs uppercase tracking-wider text-gray-500 mt-0.5">Événements</p>
      </div>
    </div>

    
    <?php if(auth()->id() === $user->id): ?>
    <?php
      $score = 0;
      if($user->avatar) $score += 20;
      if($user->bio) $score += 20;
      if($user->cv_url || $user->cv_path) $score += 20;
      if($user->github) $score += 20;
      if($user->competences && count($user->competences) > 0) $score += 20;
    ?>
    <div class="mt-4 bg-white/60 border border-kraft-dark/40 rounded-xl p-3">
      <div class="flex justify-between items-center text-xs mb-2">
        <span class="text-gray-500 flex items-center gap-1"><i class="ti ti-chart-pie text-sauge"></i> Complétude du profil</span>
        <span class="font-semibold text-ardoise"><?php echo e($score); ?>%</span>
      </div>
      <div class="h-1.5 bg-kraft-dark/30 rounded-full overflow-hidden">
        <div class="bar-fill" style="width: <?php echo e($score); ?>%;"></div>
      </div>
      <div class="flex flex-wrap gap-2 mt-2">
        <?php if(!$user->avatar): ?>
        <a href="<?php echo e(route('profil.edit')); ?>" class="text-xs bg-moutarde/10 text-moutarde-dark border border-moutarde/30 px-2.5 py-1 rounded-full flex items-center gap-1 hover:bg-moutarde/20 transition-colors">
          <i class="ti ti-plus text-xs"></i> Photo de profil
        </a>
        <?php endif; ?>
        <?php if(!$user->bio): ?>
        <a href="<?php echo e(route('profil.edit')); ?>" class="text-xs bg-moutarde/10 text-moutarde-dark border border-moutarde/30 px-2.5 py-1 rounded-full flex items-center gap-1 hover:bg-moutarde/20 transition-colors">
          <i class="ti ti-plus text-xs"></i> Ajouter votre  bio
        </a>
        <?php endif; ?>
        <?php if(!$user->cv_url && !$user->cv_path): ?>
        <a href="<?php echo e(route('profil.edit')); ?>" class="text-xs bg-moutarde/10 text-moutarde-dark border border-moutarde/30 px-2.5 py-1 rounded-full flex items-center gap-1 hover:bg-moutarde/20 transition-colors">
          <i class="ti ti-plus text-xs"></i> Ajouter votre  CV
        </a>
        <?php endif; ?>
        <?php if(!$user->github): ?>
        <a href="<?php echo e(route('profil.edit')); ?>" class="text-xs bg-moutarde/10 text-moutarde-dark border border-moutarde/30 px-2.5 py-1 rounded-full flex items-center gap-1 hover:bg-moutarde/20 transition-colors">
          <i class="ti ti-plus text-xs"></i>Mon Lien GitHub
        </a>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>

    
    <div class="flex gap-0 border-b border-kraft-dark/40 mt-4 overflow-x-auto -mx-5 px-5" role="tablist" id="profile-tabs">
      <button class="tab-btn active" data-tab="publications" role="tab">Publications</button>
      <button class="tab-btn" data-tab="apropos" role="tab">À propos</button>
      <button class="tab-btn" data-tab="groupes" role="tab">Groupes</button>
      <button class="tab-btn" data-tab="evenements" role="tab">Événements</button>
      <button class="tab-btn" data-tab="activite" role="tab">Activité</button>
      <button class="tab-btn" data-tab="amis" role="tab">Amis</button>
    </div>
  </div>

  
  <div class="grid grid-cols-1 lg:grid-cols-[300px_1fr] gap-4 mt-4 items-start">

    
    <aside class="space-y-4">
      <?php if(auth()->id() === $user->id): ?>

      
      <?php if($pendingFriendRequests->isNotEmpty()): ?>
      <div class="bg-kraft-light border border-kraft-dark/40 rounded-xl p-4">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-sm font-semibold text-ardoise flex items-center gap-1.5">
            <i class="ti ti-user-plus text-sauge"></i> Demandes d’amis
          </h2>
          <span class="text-xs font-medium text-sauge"><?php echo e($pendingFriendRequests->count()); ?></span>
        </div>
        <div class="space-y-2">
          <?php $__currentLoopData = $pendingFriendRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pending): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="flex items-center justify-between gap-2 rounded-2xl border border-ardoise/10 bg-white p-2.5">
            <a href="<?php echo e(route('profil.show', $pending->sender->handle ?? $pending->sender->id)); ?>" class="flex items-center gap-2 min-w-0">
              <div class="w-9 h-9 rounded-full bg-ardoise text-moutarde flex items-center justify-center text-xs font-semibold overflow-hidden shrink-0">
                <?php if($pending->sender->avatar): ?>
                  <img src="<?php echo e(asset('storage/'.$pending->sender->avatar)); ?>" alt="" class="w-full h-full object-cover">
                <?php else: ?>
                  <?php echo e(strtoupper(substr($pending->sender->prenom,0,1).substr($pending->sender->nom,0,1))); ?>

                <?php endif; ?>
              </div>
              <div class="min-w-0">
                <p class="text-sm font-semibold text-ardoise truncate"><?php echo e($pending->sender->prenom); ?> <?php echo e($pending->sender->nom); ?></p>
                <p class="text-xs text-gray-500">souhaite devenir ami</p>
              </div>
            </a>
            <div class="flex gap-1.5 shrink-0">
              <form class="friend-action-form" action="<?php echo e(route('friend.requests.accept', $pending->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" data-success-text="Acceptée" class="rounded-full bg-ardoise px-2.5 py-1.5 text-[11px] font-semibold text-kraft hover:bg-ardoise-light transition-colors">
                  <i class="ti ti-check"></i>
                </button>
              </form>
              <form class="friend-action-form" action="<?php echo e(route('friend.requests.decline', $pending->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" data-success-text="Refusée" class="rounded-full border border-ardoise/20 px-2.5 py-1.5 text-[11px] font-semibold text-ardoise hover:bg-ardoise/5 transition-colors">
                  <i class="ti ti-x"></i>
                </button>
              </form>
            </div>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
      <?php endif; ?>

      
      <div class="bg-kraft-light border border-kraft-dark/40 rounded-xl p-4">
        <h2 class="text-sm font-semibold text-ardoise flex items-center gap-1.5 mb-3">
          <i class="ti ti-id-badge text-sauge"></i> À propos
        </h2>
        <p class="text-sm text-gray-600 leading-relaxed mb-3"><?php echo e($user->bio ?? 'Aucune bio pour l\'instant.'); ?></p>
        <div class="space-y-1.5">
          <?php if($user->universite): ?>
          <div class="flex items-center gap-2 text-sm text-gray-600">
            <i class="ti ti-school text-sauge text-base"></i>
            <span><?php echo e($user->universite); ?> — <strong class="text-tinta"><?php echo e($user->filiere); ?></strong></span>
          </div>
          <?php endif; ?>
          <?php if($user->ville): ?>
          <div class="flex items-center gap-2 text-sm text-gray-600">
            <i class="ti ti-map-pin text-sauge text-base"></i>
            <span><?php echo e($user->ville); ?>, <?php echo e($user->pays ?? 'Côte d\'Ivoire'); ?></span>
          </div>
          <?php endif; ?>
          <?php if(auth()->id() === $user->id && $user->email_public): ?>
          <div class="flex items-center gap-2 text-sm text-gray-600">
            <i class="ti ti-mail text-sauge text-base"></i>
            <span><?php echo e($user->email); ?></span>
          </div>
          <?php endif; ?>
          <?php if($user->github): ?>
          <div class="flex items-center gap-2 text-sm">
            <i class="ti ti-brand-github text-sauge text-base"></i>
            <a href="https://github.com/<?php echo e($user->github); ?>" target="_blank" rel="noopener"
               class="text-sauge hover:text-sauge-dark transition-colors">
              github.com/<?php echo e($user->github); ?>

            </a>
          </div>
          <?php endif; ?>
          <?php if($user->portfolio_url): ?>
          <div class="flex items-center gap-2 text-sm">
            <i class="ti ti-globe text-sauge text-base"></i>
            <a href="<?php echo e($user->portfolio_url); ?>" target="_blank" rel="noopener"
               class="text-sauge hover:text-sauge-dark transition-colors truncate">
              <?php echo e($user->portfolio_url); ?>

            </a>
          </div>
          <?php endif; ?>
          <div class="flex items-center gap-2 text-sm text-gray-500">
            <i class="ti ti-calendar text-sauge text-base"></i>
            <span>Rejoint en <strong class="text-tinta"><?php echo e($user->created_at->translatedFormat('F Y')); ?></strong></span>
          </div>
        </div>
        
        <?php if($user->cv_path): ?>
        <a href="<?php echo e(asset('storage/'.$user->cv_path)); ?>" download
           class="mt-3 w-full flex items-center justify-center gap-2 text-sm font-medium text-ardoise border border-ardoise/40 rounded-lg py-2 hover:bg-ardoise/5 transition-colors">
          <i class="ti ti-download"></i> Télécharger mon CV
        </a>
        <?php endif; ?>
      </div>

      
      <?php if($user->competences && count($user->competences) > 0): ?>
      <div class="bg-kraft-light border border-kraft-dark/40 rounded-xl p-4">
        <h2 class="text-sm font-semibold text-ardoise flex items-center gap-1.5 mb-3">
          <i class="ti ti-tools text-sauge"></i> Compétences
        </h2>
        <div class="space-y-2">
          <?php $__currentLoopData = $user->competences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="flex items-center gap-2">
            <span class="text-xs text-gray-500 w-20 shrink-0"><?php echo e($comp['nom']); ?></span>
            <div class="skill-bar-bg">
              <div class="skill-bar-fill" style="width: <?php echo e($comp['niveau']); ?>%;"></div>
            </div>
            <span class="text-xs text-gray-400 w-7 text-right shrink-0"><?php echo e($comp['niveau']); ?>%</span>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
      <?php endif; ?>

      
      <?php if(auth()->id() === $user->id): ?>
      <div class="bg-kraft-light border border-kraft-dark/40 rounded-xl p-4">
        <h2 class="text-sm font-semibold text-ardoise flex items-center gap-1.5 mb-3">
          <i class="ti ti-users text-sauge"></i>
          Connexions
          <span class="font-normal text-gray-500">· <?php echo e($connexions->total() ?? 0); ?></span>
        </h2>
        <div class="grid grid-cols-3 gap-1.5">
          <?php $__currentLoopData = $connexions->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ami): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <a href="<?php echo e(route('profil.show', $ami->handle)); ?>" class="block text-center group">
            <div class="friend-avatar mb-1">
              <?php if($ami->avatar): ?>
                <img src="<?php echo e(asset('storage/'.$ami->avatar)); ?>" alt="<?php echo e($ami->prenom); ?>">
              <?php else: ?>
                <?php echo e(strtoupper(substr($ami->prenom,0,1).substr($ami->nom,0,1))); ?>

              <?php endif; ?>
            </div>
            <p class="text-xs text-gray-500 truncate group-hover:text-ardoise transition-colors"><?php echo e($ami->prenom); ?></p>
          </a>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php if($connexions->total() > 5): ?>
          <a href="<?php echo e(route('profil.connexions', $user->handle)); ?>" class="block text-center">
            <div class="friend-avatar mb-1 text-sm">+<?php echo e($connexions->total() - 5); ?></div>
            <p class="text-xs text-gray-500">Voir tout</p>
          </a>
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>

      <?php else: ?>
      <div class="bg-kraft-light border border-kraft-dark/40 rounded-xl p-6 text-sm text-gray-600">
        Les informations de contact et la liste d'amis de ce profil sont privées.
      </div>
      <?php endif; ?>
    </aside>

    
    <section>

      
      <div class="tab-panel active fade-in" id="tab-publications">

        
        <?php if(auth()->id() === $user->id): ?>
        <div class="bg-kraft-light border border-kraft-dark/40 rounded-xl p-4 mb-4">
          <form action="<?php echo e(route('posts.store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="flex items-center gap-3 mb-3">
              <div class="avatar-ring w-9! h-9! text-sm! mt-0! shrink-0">
                <?php if($user->avatar): ?>
                  <img src="<?php echo e(asset('storage/'.$user->avatar)); ?>" alt="">
                <?php else: ?>
                  <?php echo e(strtoupper(substr($user->prenom,0,1).substr($user->nom,0,1))); ?>

                <?php endif; ?>
              </div>
              <textarea name="contenu" rows="2"
                class="flex-1 border border-kraft-dark/40 rounded-xl px-4 py-2 text-sm bg-white/70 resize-none focus:outline-none focus:ring-2 focus:ring-moutarde/40 placeholder:text-gray-400"
                placeholder="Quoi de neuf sur le campus ?"></textarea>
            </div>
            <div class="flex items-center justify-between border-t border-kraft-dark/30 pt-3">
              <div class="flex gap-3">
                <label class="flex items-center gap-1.5 text-xs text-sauge cursor-pointer hover:text-sauge-dark transition-colors">
                  <i class="ti ti-photo"></i> Photo
                  <input type="file" name="media" accept="image/*,video/*" class="hidden">
                </label>
                <span class="flex items-center gap-1.5 text-xs text-sauge cursor-pointer hover:text-sauge-dark transition-colors">
                  <i class="ti ti-file-text"></i> Cours
                </span>
                <span class="flex items-center gap-1.5 text-xs text-sauge cursor-pointer hover:text-sauge-dark transition-colors">
                  <i class="ti ti-briefcase"></i> Stage
                </span>
              </div>
              <button type="submit"
                class="flex items-center gap-1.5 px-4 py-1.5 bg-ardoise text-kraft text-xs font-medium rounded-lg hover:bg-ardoise-light transition-colors">
                <i class="ti ti-send"></i> Publier
              </button>
            </div>
          </form>
        </div>
        <?php endif; ?>

        
        <?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <article class="post-card fade-in">
          <div class="flex items-center gap-3 p-4 pb-0">
            <a href="<?php echo e(route('profil.show', $post->user->handle)); ?>">
              <div class="w-10 h-10 rounded-full bg-ardoise flex items-center justify-center text-sm font-semibold text-moutarde shrink-0 overflow-hidden">
                <?php if($post->user->avatar): ?>
                  <img src="<?php echo e(asset('storage/'.$post->user->avatar)); ?>" alt="" class="w-full h-full object-cover">
                <?php else: ?>
                  <?php echo e(strtoupper(substr($post->user->prenom,0,1).substr($post->user->nom,0,1))); ?>

                <?php endif; ?>
              </div>
            </a>
            <div class="flex-1">
              <p class="text-sm font-medium text-tinta">
                <a href="<?php echo e(route('profil.show', $post->user->handle)); ?>" class="hover:text-ardoise transition-colors">
                  <?php echo e($post->user->prenom); ?> <?php echo e($post->user->nom); ?>

                </a>
              </p>
              <p class="text-xs text-gray-400"><?php echo e($post->created_at->diffForHumans()); ?>

                <?php if($post->visibilite === 'public'): ?> · <i class="ti ti-world"></i>
                <?php elseif($post->visibilite === 'prive'): ?> · <i class="ti ti-lock"></i>
                <?php else: ?> · <i class="ti ti-users"></i>
                <?php endif; ?>
              </p>
              <?php if($post->groupe): ?>
              <span class="text-xs bg-sauge/15 text-sauge px-2 py-0.5 rounded-full"><?php echo e($post->groupe->nom); ?></span>
              <?php endif; ?>
            </div>
            <?php if(auth()->id() === $post->user_id): ?>
            <div class="relative post-menu-wrapper">
              <button type="button" class="post-menu-toggle text-gray-400 hover:text-gray-600 p-1" aria-expanded="false" aria-haspopup="true">
                <i class="ti ti-dots text-lg"></i>
              </button>
              <div class="post-menu hidden absolute right-0 top-7 bg-white border border-gray-200 rounded-lg shadow-lg text-sm z-10 w-40">
                <button type="button" class="edit-post-trigger flex items-center gap-2 px-3 py-2 hover:bg-gray-50 w-full text-left" data-post-id="<?php echo e($post->id); ?>" data-content="<?php echo e(e($post->contenu)); ?>">
                  <i class="ti ti-edit text-sm"></i> Modifier
                </button>
                <form action="<?php echo e(route('posts.destroy', $post->id)); ?>" method="POST" class="post-delete-form">
                  <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                  <button type="submit" class="flex items-center gap-2 px-3 py-2 hover:bg-red-50 text-red-600 w-full text-left">
                    <i class="ti ti-trash text-sm"></i> Supprimer
                  </button>
                </form>
              </div>
            </div>
            <?php endif; ?>
          </div>

          <div class="post-content px-4 py-3 text-sm text-tinta leading-relaxed">
            <?php echo e($post->contenu); ?>

            <?php if($post->tags): ?>
            <p class="mt-1">
              <?php $__currentLoopData = explode(',', $post->tags); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <a href="<?php echo e(route('search', ['q' => trim($tag)])); ?>"
                 class="text-sauge hover:text-sauge-dark transition-colors">#<?php echo e(trim($tag)); ?> </a>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </p>
            <?php endif; ?>
          </div>

          <?php if($post->media_path): ?>
          <div class="px-4 pb-3">
            <?php if(str_starts_with($post->media_type ?? '', 'image')): ?>
              <img src="<?php echo e(asset('storage/'.$post->media_path)); ?>" alt="Media du post"
                   class="w-full rounded-lg object-cover max-h-80">
            <?php elseif(str_starts_with($post->media_type ?? '', 'video')): ?>
              <video src="<?php echo e(asset('storage/'.$post->media_path)); ?>" controls
                     class="w-full rounded-lg max-h-80"></video>
            <?php endif; ?>
          </div>
          <?php endif; ?>

          
          <?php if($post->likes_count > 0 || $post->comments_count > 0 || $post->shares_count > 0): ?>
          <div class="px-4 pb-2 flex justify-between text-xs text-gray-400">
            <?php if($post->likes_count > 0): ?>
            <span class="post-like-count"><i class="ti ti-heart-filled text-encre"></i> <?php echo e($post->likes_count); ?> j’aime</span>
            <?php else: ?>
            <span class="post-like-count hidden"><i class="ti ti-heart-filled text-encre"></i></span>
            <?php endif; ?>
            <?php if($post->comments_count > 0): ?>
            <span class="post-comment-count"><?php echo e($post->comments_count); ?> commentaire<?php echo e($post->comments_count > 1 ? 's' : ''); ?></span>
            <?php else: ?>
            <span class="post-comment-count hidden">0 commentaire</span>
            <?php endif; ?>
            <?php if($post->shares_count > 0): ?>
            <span class="post-share-count"><?php echo e($post->shares_count); ?> partage<?php echo e($post->shares_count > 1 ? 's' : ''); ?></span>
            <?php else: ?>
            <span class="post-share-count hidden">0 partage</span>
            <?php endif; ?>
          </div>
          <?php endif; ?>

          
          <div class="flex border-t border-kraft-dark/30">
            <form class="post-action-form" action="<?php echo e(route('posts.like', $post->id)); ?>" method="POST" style="flex:1;">
              <?php echo csrf_field(); ?>
              <button type="submit" class="post-action-btn w-full <?php echo e($post->likedByUser(auth()->id()) ? 'liked' : ''); ?>">
                <i class="ti ti-heart<?php echo e($post->likedByUser(auth()->id()) ? '-filled' : ''); ?>"></i>
                <span class="post-action-label">J'aime</span>
              </button>
            </form>
            <form class="post-action-form" action="<?php echo e(route('posts.comment', $post->id)); ?>" method="POST" style="flex:1;">
              <?php echo csrf_field(); ?>
              <input type="hidden" name="contenu" value="Commentaire rapide">
              <button type="submit" class="post-action-btn w-full">
                <i class="ti ti-message-circle"></i> Commenter
              </button>
            </form>
            <form class="post-action-form" action="<?php echo e(route('posts.share', $post->id)); ?>" method="POST" style="flex:1;">
              <?php echo csrf_field(); ?>
              <button type="submit" class="post-action-btn w-full">
                <i class="ti ti-share-3"></i> Partager
              </button>
            </form>
          </div>
        </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-center py-16 text-gray-400">
          <i class="ti ti-notebook text-4xl mb-3 block"></i>
          <p class="text-sm">Aucune publication pour l'instant.</p>
          <?php if(auth()->id() === $user->id): ?>
          <p class="text-xs mt-1">Commencez par partager quelque chose avec votre campus !</p>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        
        <?php if($posts->hasPages()): ?>
        <div class="mt-4"><?php echo e($posts->links()); ?></div>
        <?php endif; ?>
      </div>

      
      <div class="tab-panel" id="tab-apropos">
        <div class="bg-kraft-light border border-kraft-dark/40 rounded-xl p-5 space-y-4">
          <div>
            <h3 class="text-xs uppercase tracking-wider text-gray-400 mb-2">Informations générales</h3>
            <div class="space-y-2 text-sm">
              <div class="flex gap-3"><i class="ti ti-school text-sauge text-base mt-0.5"></i><div><strong class="text-tinta"><?php echo e($user->universite ?? '—'); ?></strong><p class="text-gray-500"><?php echo e($user->filiere); ?> · <?php echo e($user->niveau); ?></p></div></div>
              <div class="flex gap-3"><i class="ti ti-map-pin text-sauge text-base mt-0.5"></i><span class="text-tinta"><?php echo e($user->ville ?? '—'); ?>, <?php echo e($user->pays ?? 'Côte d\'Ivoire'); ?></span></div>
              <div class="flex gap-3"><i class="ti ti-calendar text-sauge text-base mt-0.5"></i><span class="text-tinta">Inscrit le <?php echo e($user->created_at->translatedFormat('d F Y')); ?></span></div>
            </div>
          </div>
          <?php if($user->bio): ?>
          <div>
            <h3 class="text-xs uppercase tracking-wider text-gray-400 mb-2">Bio</h3>
            <p class="text-sm text-gray-700 leading-relaxed"><?php echo e($user->bio); ?></p>
          </div>
          <?php endif; ?>
          <?php if($user->centres_interet): ?>
          <div>
            <h3 class="text-xs uppercase tracking-wider text-gray-400 mb-2">Centres d'intérêt</h3>
            <div class="flex flex-wrap gap-2">
              <?php $__currentLoopData = explode(',', $user->centres_interet); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $centre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <span class="text-xs bg-sauge/15 text-sauge-dark px-3 py-1 rounded-full"><?php echo e(trim($centre)); ?></span>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>

      
      <div class="tab-panel" id="tab-projets">
        <?php if(auth()->id() === $user->id || !($user->private_projects ?? false) || (isset($isFriend) && $isFriend)): ?>
        <div class="grid sm:grid-cols-2 gap-4">
          <?php $__empty_1 = true; $__currentLoopData = $projets ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $projet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <div class="bg-kraft-light border border-kraft-dark/40 rounded-xl p-4 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-2">
              <h3 class="font-semibold text-ardoise text-sm"><?php echo e($projet->nom); ?></h3>
              <span class="text-xs px-2 py-0.5 rounded-full
                <?php echo e($projet->statut === 'en_cours' ? 'bg-moutarde/20 text-moutarde-dark' : ($projet->statut === 'termine' ? 'bg-sauge/20 text-sauge-dark' : 'bg-gray-100 text-gray-500')); ?>">
                <?php echo e(ucfirst(str_replace('_', ' ', $projet->statut))); ?>

              </span>
            </div>
            <p class="text-xs text-gray-500 leading-relaxed mb-3"><?php echo e($projet->description); ?></p>
            <?php if($projet->technologies): ?>
            <div class="flex flex-wrap gap-1 mb-3">
              <?php $__currentLoopData = explode(',', $projet->technologies); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tech): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <span class="text-[11px] bg-ardoise/10 text-ardoise px-2 py-0.5 rounded"><?php echo e(trim($tech)); ?></span>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
            <?php if($projet->lien): ?>
            <a href="<?php echo e($projet->lien); ?>" target="_blank" rel="noopener"
               class="text-xs text-sauge hover:text-sauge-dark flex items-center gap-1 transition-colors">
              <i class="ti ti-external-link"></i> Voir le projet
            </a>
            <?php endif; ?>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <div class="col-span-2 text-center py-12 text-gray-400">
            <i class="ti ti-folder text-4xl mb-3 block"></i>
            <p class="text-sm">Aucun projet ajouté.</p>
          </div>
          <?php endif; ?>
        </div>
        <?php else: ?>
          <div class="p-6 text-sm text-gray-600">Les projets de cet utilisateur sont privés.</div>
        <?php endif; ?>
      </div>

      
      <div class="tab-panel" id="tab-groupes">
        <?php if(auth()->id() === $user->id): ?>
        <div class="mb-4 flex justify-end">
          <button type="button" onclick="document.getElementById('create-group-modal').classList.remove('hidden')" class="rounded-xl bg-ardoise px-4 py-2 text-sm font-semibold text-kraft hover:bg-ardoise-light transition-colors">
            <i class="ti ti-plus"></i> Créer un groupe
          </button>
        </div>
        <?php endif; ?>
        <div class="grid sm:grid-cols-2 gap-4">
          <?php $__empty_1 = true; $__currentLoopData = $groupes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <a href="<?php echo e(route('groupes.show', data_get($groupe, 'slug'))); ?>"
             class="bg-kraft-light border border-kraft-dark/40 rounded-xl p-4 flex gap-3 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-lg bg-ardoise flex items-center justify-content-center text-moutarde font-bold text-lg shrink-0 overflow-hidden">
              <?php if(data_get($groupe, 'avatar')): ?>
                <img src="<?php echo e(asset('storage/'.data_get($groupe, 'avatar'))); ?>" alt="" class="w-full h-full object-cover">
              <?php else: ?> <?php echo e(strtoupper(substr(data_get($groupe, 'nom', ''), 0, 2))); ?> <?php endif; ?>
            </div>
            <div>
              <p class="font-medium text-sm text-ardoise"><?php echo e(data_get($groupe, 'nom')); ?></p>
              <p class="text-xs text-gray-500"><?php echo e(data_get($groupe, 'membres_count', 0)); ?><?php echo e(data_get($groupe, 'max_members') ? ' / ' . data_get($groupe, 'max_members') : ''); ?> membres</p>
              <span class="text-xs text-sauge"><?php echo e(ucfirst(data_get($groupe, 'type', ''))); ?></span>
            </div>
          </a>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <div class="col-span-2 text-center py-12 text-gray-400">
            <i class="ti ti-users text-4xl mb-3 block"></i>
            <p class="text-sm">Aucun groupe rejoint.</p>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <?php if(auth()->id() === $user->id): ?>
      <div id="create-group-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
        <div class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-xl border border-kraft-dark/40">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-ardoise">Créer un groupe</h3>
            <button type="button" onclick="document.getElementById('create-group-modal').classList.add('hidden')" class="text-gray-500 hover:text-ardoise">
              <i class="ti ti-x"></i>
            </button>
          </div>
          <form action="<?php echo e(route('groupes.store')); ?>" method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
              <label class="block text-sm font-medium text-ardoise mb-2">Nom du groupe</label>
              <input type="text" name="nom" required class="w-full rounded-2xl border border-kraft-dark/40 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-moutarde/40" placeholder="Ex. Groupe Dev Campus">
            </div>
            <div>
              <label class="block text-sm font-medium text-ardoise mb-2">Nombre maximum de membres</label>
              <input type="number" name="max_members" min="2" max="500" value="50" class="w-full rounded-2xl border border-kraft-dark/40 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-moutarde/40">
            </div>
            <div>
              <label class="block text-sm font-medium text-ardoise mb-2">Visibilité</label>
              <select name="visibilite" class="w-full rounded-2xl border border-kraft-dark/40 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-moutarde/40">
                <option value="public">Public</option>
                <option value="prive">Privé</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-ardoise mb-2">Description</label>
              <textarea name="description" rows="3" class="w-full rounded-2xl border border-kraft-dark/40 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-moutarde/40" placeholder="Décrivez l’objectif du groupe"></textarea>
            </div>
            <div class="flex justify-end gap-3">
              <button type="button" onclick="document.getElementById('create-group-modal').classList.add('hidden')" class="rounded-xl border border-ardoise/20 px-4 py-2 text-sm text-ardoise">Annuler</button>
              <button type="submit" class="rounded-xl bg-ardoise px-4 py-2 text-sm font-semibold text-kraft hover:bg-ardoise-light transition-colors">Créer</button>
            </div>
          </form>
        </div>
      </div>
      <?php endif; ?>

      
      <div class="tab-panel" id="tab-evenements">
        <div class="space-y-3">
          <?php $__empty_1 = true; $__currentLoopData = $evenements ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $evt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <div class="bg-kraft-light border border-kraft-dark/40 rounded-xl p-4 flex gap-4">
            <div class="shrink-0 text-center bg-ardoise text-kraft rounded-lg px-3 py-2 min-w-[52px]">
              <p class="text-xs uppercase tracking-wider text-moutarde"><?php echo e(optional(data_get($evt, 'date_debut'))->translatedFormat('M')); ?></p>
              <p class="text-xl font-bold leading-none"><?php echo e(optional(data_get($evt, 'date_debut'))->format('d')); ?></p>
            </div>
            <div>
              <p class="font-medium text-sm text-ardoise"><?php echo e(data_get($evt, 'titre')); ?></p>
              <p class="text-xs text-gray-500 mt-0.5"><?php echo e(data_get($evt, 'lieu')); ?> · <?php echo e(optional(data_get($evt, 'date_debut'))->format('H:i')); ?></p>
              <p class="text-xs text-sauge mt-1"><?php echo e(data_get($evt, 'participants_count', 0)); ?> participants</p>
            </div>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <div class="text-center py-12 text-gray-400">
            <i class="ti ti-calendar text-4xl mb-3 block"></i>
            <p class="text-sm">Aucun événement à venir.</p>
          </div>
          <?php endif; ?>
        </div>
      </div>

      
      <div class="tab-panel" id="tab-documents">
        <?php if(!(auth()->id() === $user->id || !($user->private_documents ?? false) || (isset($isFriend) && $isFriend))): ?>
          <div class="bg-kraft-light border border-kraft-dark/40 rounded-xl p-4 text-sm text-gray-600">Les documents de cet utilisateur sont privés.</div>
        <?php else: ?>
          <?php if($documents->isEmpty()): ?>
          <div class="bg-kraft-light border border-kraft-dark/40 rounded-xl p-4 text-sm text-gray-600">
            <p>Vous n’avez aucun document partagé dans vos messages pour le moment.</p>
            <p class="mt-3 text-xs text-gray-400">Les fichiers envoyés ou reçus apparaîtront ici automatiquement.</p>
          </div>
          <?php else: ?>
          <div class="space-y-4">
            <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="rounded-3xl border border-ardoise/10 bg-white p-4 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                  <div class="space-y-1">
                    <p class="font-semibold text-ardoise"><?php echo e($doc->attachment_name ?? 'Fichier joint'); ?></p>
                    <p class="text-sm text-gray-500">Envoyé par <?php echo e($doc->sender->id === auth()->id() ? 'Vous' : $doc->sender->prenom . ' ' . $doc->sender->nom); ?> à <?php echo e($doc->receiver->id === auth()->id() ? 'Vous' : $doc->receiver->prenom . ' ' . $doc->receiver->nom); ?></p>
                    <p class="text-xs text-gray-400"><?php echo e($doc->created_at->format('d/m/Y H:i')); ?> · <?php echo e($doc->attachment_type); ?></p>
                  </div>
                  <div class="flex flex-wrap items-center gap-2">
                    <a href="<?php echo e($doc->attachment_url); ?>" download class="rounded-full bg-moutarde px-4 py-2 text-xs font-semibold text-ardoise hover:bg-moutarde/90 transition-colors">Télécharger</a>
                    <?php if(str_starts_with($doc->attachment_type ?? '', 'image')): ?>
                      <a href="<?php echo e($doc->attachment_url); ?>" target="_blank" class="rounded-full border border-ardoise/20 px-4 py-2 text-xs font-semibold text-ardoise hover:bg-ardoise/5 transition-colors">Voir</a>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>

      
      <div class="tab-panel" id="tab-activite">
        <div class="bg-kraft-light border border-kraft-dark/40 rounded-xl p-4 space-y-6">
          <div>
            <h3 class="text-sm font-semibold text-ardoise flex items-center gap-2 mb-4">
              <i class="ti ti-activity text-sauge"></i> Activité récente
            </h3>
            <div class="space-y-0">
              <?php $__empty_1 = true; $__currentLoopData = $activites ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $act): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <div class="flex gap-3 py-3 border-b border-kraft-dark/20 last:border-b-0">
                <div class="w-2 h-2 rounded-full mt-2 shrink-0"
                     style="background: <?php echo e($act['couleur'] ?? '#E2A33B'); ?>;"></div>
                <div>
                  <p class="text-sm text-gray-700 leading-snug"><?php echo $act['description']; ?></p>
                  <p class="text-xs text-gray-400 mt-0.5"><?php echo e($act['temps']); ?></p>
                </div>
              </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <p class="text-sm text-gray-400 text-center py-8">Aucune activité récente.</p>
              <?php endif; ?>
            </div>
          </div>

        </div>
      </div>

      
      <div class="tab-panel" id="tab-amis">
        <div class="bg-kraft-light border border-kraft-dark/40 rounded-xl p-4 space-y-6">
          <div>
            <h3 class="text-sm font-semibold text-ardoise flex items-center gap-2 mb-4">
              <i class="ti ti-users text-sauge"></i> Amis
            </h3>
            <?php if(!(auth()->id() === $user->id || !($user->private_friends ?? false) || (isset($isFriend) && $isFriend))): ?>
              <div class="rounded-3xl border border-ardoise/10 bg-white p-6 text-sm text-gray-600">
                La liste d'amis de cet utilisateur est privée.
              </div>
            <?php elseif($friends->isEmpty()): ?>
              <div class="rounded-3xl border border-ardoise/10 bg-white p-6 text-sm text-gray-600">
                Aucun ami à afficher pour le moment.
              </div>
            <?php else: ?>
              <div class="grid gap-3 sm:grid-cols-2">
                <?php $__currentLoopData = $friends; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $relation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <a href="<?php echo e(route('profil.show', $relation->user->handle ?? $relation->user->id)); ?>" class="flex items-center gap-3 rounded-3xl border border-ardoise/10 bg-white p-3 hover:bg-kraft-light transition-colors">
                    <div class="w-10 h-10 rounded-full bg-ardoise text-moutarde flex items-center justify-center text-sm font-semibold overflow-hidden">
                      <?php if($relation->user->avatar): ?>
                        <img src="<?php echo e(asset('storage/'.$relation->user->avatar)); ?>" alt="<?php echo e($relation->user->prenom); ?>" class="w-full h-full object-cover">
                      <?php else: ?>
                        <?php echo e(strtoupper(substr($relation->user->prenom,0,1).substr($relation->user->nom,0,1))); ?>

                      <?php endif; ?>
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-ardoise"><?php echo e($relation->user->prenom); ?> <?php echo e($relation->user->nom); ?></p>
                      <p class="text-xs text-gray-500"><?php echo e($relation->user->handle ? '@'.$relation->user->handle : 'ID '.$relation->user->id); ?></p>
                    </div>
                  </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </div>
            <?php endif; ?>
          </div>

          <?php if($isSelf && $friendSuggestions->isNotEmpty()): ?>
            <div class="rounded-3xl border border-moutarde/30 bg-gradient-to-br from-white to-kraft-light p-4 shadow-sm">
              <div class="flex items-start justify-between gap-3 mb-4">
                <div>
                  <h3 class="text-sm font-semibold text-ardoise flex items-center gap-2">
                    <i class="ti ti-user-plus text-sauge"></i> Suggestions d’amis
                  </h3>
                  <p class="mt-1 text-xs text-gray-500">Nouveaux amis à découvrir</p>
                </div>
                <span class="rounded-full bg-moutarde/20 px-2.5 py-1 text-[11px] font-semibold text-ardoise">
                  <?php echo e($friendSuggestions->count()); ?>

                </span>
              </div>
              <div class="space-y-2">
                <?php $__currentLoopData = $friendSuggestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $suggestion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <div class="flex items-center justify-between gap-2 rounded-3xl border border-ardoise/10 bg-white p-3">
                    <a href="<?php echo e(route('profil.show', $suggestion->handle ?? $suggestion->id)); ?>" class="flex items-center gap-2 min-w-0">
                      <div class="w-9 h-9 rounded-full bg-ardoise text-moutarde flex items-center justify-center text-xs font-semibold overflow-hidden shrink-0">
                        <?php if($suggestion->avatar): ?>
                          <img src="<?php echo e(asset('storage/'.$suggestion->avatar)); ?>" alt="" class="w-full h-full object-cover">
                        <?php else: ?>
                          <?php echo e(strtoupper(substr($suggestion->prenom,0,1).substr($suggestion->nom,0,1))); ?>

                        <?php endif; ?>
                      </div>
                      <div class="min-w-0">
                        <p class="text-sm font-semibold text-ardoise truncate"><?php echo e($suggestion->prenom); ?> <?php echo e($suggestion->nom); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e($suggestion->filiere ?? 'Étudiant'); ?></p>
                      </div>
                    </a>
                    <form class="friend-action-form" action="<?php echo e(route('friend.requests.send', $suggestion->handle ?? $suggestion->id)); ?>" method="POST">
                      <?php echo csrf_field(); ?>
                      <button type="submit" data-success-text="Envoyée" class="rounded-full bg-moutarde px-2.5 py-1.5 text-[11px] font-semibold text-ardoise hover:bg-moutarde/90 transition-colors">
                        <i class="ti ti-user-plus"></i>
                      </button>
                    </form>
                  </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>

    </section>
  </div>

</main>

<?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
  /* ── TABS ── */
  const tabBtns = document.querySelectorAll('.tab-btn');
  const tabPanels = document.querySelectorAll('.tab-panel');

  tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      tabBtns.forEach(b => b.classList.remove('active'));
      tabPanels.forEach(p => { p.classList.remove('active'); p.classList.add('hidden'); });
      btn.classList.add('active');
      const panel = document.getElementById('tab-' + btn.dataset.tab);
      if (panel) { panel.classList.remove('hidden'); panel.classList.add('active'); }
    });
  });

  document.querySelectorAll('.friend-action-form').forEach(form => {
    form.addEventListener('submit', async (event) => {
      event.preventDefault();
      const button = form.querySelector('button');
      const feedback = document.getElementById('friend-action-feedback');
      if (button) {
        button.disabled = true;
      }
      const formData = new FormData(form);
      const response = await fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      if (response.ok) {
        if (feedback) {
          feedback.classList.remove('hidden');
          feedback.textContent = button?.dataset.successText || 'Action réalisée.';
        }
        if (button) {
          button.innerHTML = '<i class="ti ti-check"></i> ' + (button.dataset.successText || 'Terminé');
          button.classList.add('opacity-80');
        }
      } else if (feedback) {
        feedback.classList.remove('hidden');
        feedback.textContent = 'Une erreur est survenue.';
      }
    });
  });

  document.querySelectorAll('.post-menu-toggle').forEach(button => {
    button.addEventListener('click', (event) => {
      event.stopPropagation();
      const wrapper = button.closest('.post-menu-wrapper');
      const menu = wrapper?.querySelector('.post-menu');
      document.querySelectorAll('.post-menu').forEach(other => {
        if (other !== menu) {
          other.classList.add('hidden');
        }
      });
      menu?.classList.toggle('hidden');
      button.setAttribute('aria-expanded', String(!menu?.classList.contains('hidden')));
    });
  });

  document.addEventListener('click', () => {
    document.querySelectorAll('.post-menu').forEach(menu => menu.classList.add('hidden'));
    document.querySelectorAll('.post-menu-toggle').forEach(button => button.setAttribute('aria-expanded', 'false'));
  });

  document.querySelectorAll('.edit-post-trigger').forEach(btn => {
    btn.addEventListener('click', () => {
      const form = document.createElement('form');
      form.method = 'POST';
      form.className = 'edit-post-form';
      form.action = '/posts/' + btn.dataset.postId + '/edit';
      form.innerHTML = `<?php echo csrf_field(); ?><div class="mt-3 rounded-xl border border-ardoise/10 bg-white p-3"><textarea name="contenu" class="w-full rounded-lg border border-ardoise/20 p-2 text-sm" rows="3">${btn.dataset.content || ''}</textarea><div class="mt-2 flex justify-end"><button type="submit" class="rounded-lg bg-ardoise px-3 py-2 text-sm text-kraft">Enregistrer</button></div></div>`;
      btn.closest('.post-menu-wrapper').appendChild(form);
    });
  });

  document.addEventListener('submit', async (event) => {
    if (event.target.classList.contains('edit-post-form')) {
      event.preventDefault();
      const form = event.target;
      const response = await fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      if (response.ok) {
        const article = form.closest('article.post-card');
        const content = form.querySelector('textarea')?.value || '';
        const contentBlock = article?.querySelector('.post-content');
        if (contentBlock) {
          contentBlock.textContent = content;
        }
        form.remove();
      }
    }
  });

  document.querySelectorAll('.post-delete-form').forEach(form => {
    form.addEventListener('submit', async (event) => {
      event.preventDefault();
      if (!confirm('Supprimer cette publication ?')) {
        return;
      }
      const response = await fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      if (response.ok) {
        form.closest('article.post-card')?.remove();
      }
    });
  });

  const updatePostCounters = (article, data) => {
    if (!article) return;

    const likeCountEl = article.querySelector('.post-like-count');
    if (likeCountEl) {
      if (data.likesCount > 0) {
        likeCountEl.classList.remove('hidden');
        likeCountEl.innerHTML = '<i class="ti ti-heart-filled text-encre"></i> ' + data.likesCount + ' j’aime';
      } else {
        likeCountEl.classList.add('hidden');
        likeCountEl.innerHTML = '';
      }
    }

    const commentCountEl = article.querySelector('.post-comment-count');
    if (commentCountEl) {
      if (data.commentsCount > 0) {
        commentCountEl.classList.remove('hidden');
        commentCountEl.textContent = data.commentsCount + ' commentaire' + (data.commentsCount > 1 ? 's' : '');
      } else {
        commentCountEl.classList.add('hidden');
        commentCountEl.textContent = '';
      }
    }

    const shareCountEl = article.querySelector('.post-share-count');
    if (shareCountEl) {
      if (data.sharesCount > 0) {
        shareCountEl.classList.remove('hidden');
        shareCountEl.textContent = data.sharesCount + ' partage' + (data.sharesCount > 1 ? 's' : '');
      } else {
        shareCountEl.classList.add('hidden');
        shareCountEl.textContent = '';
      }
    }
  };

  const showPostFeedback = (article, message) => {
    if (!article) return;
    let feedback = article.querySelector('.post-action-feedback');
    if (!feedback) {
      feedback = document.createElement('div');
      feedback.className = 'post-action-feedback mt-2 text-xs text-sauge';
      article.querySelector('.post-action-btn')?.closest('.flex')?.appendChild(feedback);
    }
    feedback.textContent = message;
    window.clearTimeout(feedback._timer);
    feedback._timer = window.setTimeout(() => {
      feedback.textContent = '';
    }, 1800);
  };

  document.querySelectorAll('.post-action-form').forEach(form => {
    form.addEventListener('submit', async (event) => {
      event.preventDefault();
      const article = form.closest('article.post-card');
      const button = form.querySelector('button');
      const originalHTML = button?.innerHTML || '';
      if (button) {
        button.disabled = true;
      }

      const formData = new FormData(form);
      const response = await fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });

      if (button) {
        button.disabled = false;
        button.innerHTML = originalHTML;
      }

      if (response.ok) {
        const data = await response.json().catch(() => null);
        if (data?.success) {
          if (form.action.includes('/like')) {
            const icon = button?.querySelector('i');
            const label = button?.querySelector('.post-action-label');
            if (data.liked) {
              button?.classList.add('liked');
              if (icon) icon.className = 'ti ti-heart-filled';
              if (label) label.textContent = "J'aime";
            } else {
              button?.classList.remove('liked');
              if (icon) icon.className = 'ti ti-heart';
              if (label) label.textContent = "J'aime";
            }
            updatePostCounters(article, data);
          }

          if (form.action.includes('/comment')) {
            updatePostCounters(article, data);
          }

          if (form.action.includes('/share')) {
            updatePostCounters(article, data);
          }

          showPostFeedback(article, data.message || 'Action réalisée.');
        }
      }
    });
  });

  /* ── Animer les barres de compétences au chargement ── */
  document.querySelectorAll('.skill-bar-fill, .bar-fill').forEach(bar => {
    const targetW = bar.style.width;
    bar.style.width = '0';
    setTimeout(() => { bar.style.width = targetW; }, 300);
  });

  /* ── Fade-in des articles ── */
  if ('IntersectionObserver' in window) {
    const obs = new IntersectionObserver((entries) => {
      entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('fade-in'); obs.unobserve(e.target); } });
    }, { threshold: .1 });
    document.querySelectorAll('article.post-card').forEach(el => obs.observe(el));
    
  }
</script>

</body>
</html><?php /**PATH C:\Users\Poolski\Desktop\DEV Project\USN.ci\resources\views/profil.blade.php ENDPATH**/ ?>