<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo e($group->nom); ?> — USN</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { background-color: #E8E0CE; color: #221E18; }
    .tab-btn { transition: all 0.3s ease; }
    .tab-btn.active { border-bottom: 3px solid #E2A33B; color: #1F2E26; font-weight: 600; }
    .tab-btn.inactive { color: #5E6E52; border-bottom: 2px solid transparent; }
  </style>
</head>
<body class="font-sans">
  <div class="min-h-screen px-4 py-8 md:py-10" style="background-color: #E8E0CE;">
    <div class="mx-auto max-w-4xl space-y-6">
      <a href="<?php echo e(route('profil.show')); ?>" class="inline-flex items-center gap-2 text-sm font-medium hover:opacity-80" style="color: #5E6E52;">← Retour au profil</a>

      <!-- En-tête du groupe -->
      <div class="overflow-hidden rounded-3xl shadow-lg" style="border: 1px solid #D4CABC; background: white;">
        <div class="px-6 py-8 text-white md:px-8 md:py-10" style="background: linear-gradient(to right, #1F2E26, #23392E, #2E4A3A);">
          <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
            <div class="flex-1">
              <div class="mb-3 inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-widest" style="border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.1); color: #E2A33B;">
                <?php echo e(ucfirst($group->visibilite)); ?>

              </div>
              <h1 class="text-4xl font-bold tracking-tight"><?php echo e($group->nom); ?></h1>
              <p class="mt-3 max-w-xl text-sm leading-6" style="color: #D4CABC;">
                <?php echo e($group->description ?? 'Pas encore de description.'); ?>

              </p>
            </div>
            <div class="rounded-2xl px-4 py-3" style="border: 1px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.1);">
              <p class="text-sm" style="color: #D4CABC;">Membres</p>
              <p class="mt-1 text-3xl font-bold"><?php echo e($group->membres->count()); ?></p>
            </div>
          </div>
        </div>

        <!-- Barre d'action -->
        <div class="flex items-center justify-between px-6 py-3 md:px-8" style="border-top: 1px solid #D4CABC; background-color: #F8F2E6;">
          <div class="text-sm" style="color: #5E6E52;">
            <?php if($isAdmin): ?>
              <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold" style="background: rgba(226, 163, 59, 0.15); color: #1F2E26;">⭐ Admin</span>
            <?php elseif($isMember): ?>
              <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold" style="background: rgba(31, 46, 38, 0.15); color: #1F2E26;">✓ Membre</span>
            <?php else: ?>
              <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold" style="background: rgba(94, 110, 82, 0.15); color: #1F2E26;">Public</span>
            <?php endif; ?>
          </div>
          <div>
            <?php if($isMember && !$isAdmin): ?>
              <form action="<?php echo e(route('groupes.leave', $group->slug)); ?>" method="POST" class="inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="text-xs font-semibold underline" style="color: #1F2E26;">Quitter</button>
              </form>
            <?php elseif(!$isMember): ?>
              <form action="<?php echo e(route('groupes.join', $group->slug)); ?>" method="POST" class="inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="rounded-full px-4 py-2 text-xs font-semibold text-white" style="background-color: #1F2E26;">Rejoindre</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Onglets -->
      <?php if($isMember): ?>
      <div class="flex gap-6 border-b" style="border-color: #D4CABC; background: white; padding: 0 20px;">
        <button onclick="switchTab('posts')" class="tab-btn active py-4 text-sm" id="tab-posts-btn">📝 Publications</button>
        <button onclick="switchTab('discussion')" class="tab-btn inactive py-4 text-sm" id="tab-discussion-btn">💬 Discussion</button>
        <button onclick="switchTab('files')" class="tab-btn inactive py-4 text-sm" id="tab-files-btn">📁 Fichiers</button>
      </div>
      <?php endif; ?>

      <div class="grid gap-6 lg:grid-cols-[1fr_320px]">
        <!-- Contenu principal -->
        <div class="space-y-4">
          <!-- TAB: Publications -->
          <div id="tab-posts" class="tab-content">
            <?php if($isMember): ?>
            <div class="overflow-hidden rounded-3xl p-5 shadow-sm md:p-6" style="border: 1px solid #D4CABC; background: white;">
              <form action="<?php echo e(route('posts.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="group_id" value="<?php echo e($group->id); ?>">
                <textarea name="contenu" rows="3" placeholder="Partager une pensée..." class="w-full rounded-2xl px-4 py-3 text-sm" style="border: 1px solid #D4CABC; background: white;"></textarea>
                <div class="flex items-center justify-between gap-3">
                  <label class="cursor-pointer rounded-full p-2 hover:opacity-75">
                    <input type="file" name="media" class="hidden" accept="image/*,video/*,.pdf,.doc,.docx">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color: #5E6E52;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                  </label>
                  <button type="submit" class="rounded-full px-6 py-2 text-xs font-semibold text-white" style="background-color: #1F2E26;">Publier</button>
                </div>
              </form>
            </div>
            <?php endif; ?>
            <div class="space-y-4">
              <?php $__empty_1 = true; $__currentLoopData = $group->posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <div class="overflow-hidden rounded-3xl shadow-sm" style="border: 1px solid #D4CABC; background: white;">
                <div class="p-5 md:p-6">
                  <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                      <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full font-bold uppercase text-white" style="background-color: #1F2E26;">
                        <?php if($post->user->avatar): ?>
                          <img src="<?php echo e(asset('storage/'.$post->user->avatar)); ?>" alt="<?php echo e($post->user->prenom); ?>" class="h-full w-full object-cover">
                        <?php else: ?>
                          <?php echo e(strtoupper(substr($post->user->prenom, 0, 1).substr($post->user->nom, 0, 1))); ?>

                        <?php endif; ?>
                      </div>
                      <div>
                        <p class="font-semibold text-sm" style="color: #1F2E26;"><?php echo e($post->user->prenom); ?> <?php echo e($post->user->nom); ?></p>
                        <p class="text-xs" style="color: #5E6E52;"><?php echo e($post->created_at->diffForHumans()); ?></p>
                      </div>
                    </div>
                  </div>
                  <p class="text-sm leading-relaxed mb-3" style="color: #2F3A30;"><?php echo e($post->contenu); ?></p>
                  <?php if($post->media_path): ?>
                    <div class="mt-3 overflow-hidden rounded-2xl" style="background-color: #F8F2E6;">
                      <?php if($post->isImage = in_array($post->media_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])): ?>
                        <img src="<?php echo e($post->media_url); ?>" alt="Publication" class="h-auto max-h-96 w-full object-cover">
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <div class="rounded-3xl border-2 p-8 text-center" style="border-style: dashed; border-color: #D4CABC; background-color: #F8F2E6;">
                <p class="text-sm" style="color: #5E6E52;">Aucune publication. Soyez le premier à partager ! 📢</p>
              </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- TAB: Discussion -->
          <div id="tab-discussion" class="tab-content hidden">
            <?php if($isMember): ?>
            <div class="overflow-hidden rounded-3xl p-5 shadow-sm md:p-6" style="border: 1px solid #D4CABC; background: white;">
              <form action="<?php echo e(route('groupes.messages.send', $group->slug)); ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
                <?php echo csrf_field(); ?>
                <textarea name="contenu" rows="3" placeholder="Écrire un message..." class="w-full rounded-2xl px-4 py-3 text-sm" style="border: 1px solid #D4CABC; background: white;"></textarea>
                <div class="flex items-center justify-between gap-3">
                  <label class="cursor-pointer rounded-full p-2 hover:opacity-75">
                    <input type="file" name="fichier" class="hidden" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt,.zip">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color: #5E6E52;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                  </label>
                  <button type="submit" class="rounded-full px-6 py-2 text-xs font-semibold text-white" style="background-color: #1F2E26;">Envoyer</button>
                </div>
              </form>
            </div>
            <?php endif; ?>
            <div class="space-y-3 max-h-96 overflow-y-auto">
              <?php $__empty_1 = true; $__currentLoopData = $group->messages->take(50); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <div class="rounded-2xl p-4" style="border: 1px solid #D4CABC; background: white;">
                <div class="flex items-center justify-between mb-2">
                  <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center overflow-hidden rounded-full font-bold uppercase text-white text-xs" style="background-color: #1F2E26;">
                      <?php echo e(strtoupper(substr($msg->user->prenom, 0, 1).substr($msg->user->nom, 0, 1))); ?>

                    </div>
                    <div>
                      <p class="font-semibold text-xs" style="color: #1F2E26;"><?php echo e($msg->user->prenom); ?> <?php echo e($msg->user->nom); ?></p>
                      <p class="text-xs" style="color: #5E6E52;"><?php echo e($msg->created_at->diffForHumans()); ?></p>
                    </div>
                  </div>
                  <?php if($msg->user_id === Auth::id() || $isAdmin): ?>
                  <form action="<?php echo e(route('groupes.messages.delete', [$group->slug, $msg->id])); ?>" method="POST" class="inline">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="text-xs hover:opacity-70" style="color: #5E6E52;">✕</button>
                  </form>
                  <?php endif; ?>
                </div>
                <p class="text-sm mb-2" style="color: #2F3A30;"><?php echo e($msg->contenu); ?></p>
                <?php if($msg->file_path): ?>
                  <?php if($msg->isImage()): ?>
                    <img src="<?php echo e($msg->file_url); ?>" alt="Image" class="rounded-lg max-h-48 object-cover">
                  <?php else: ?>
                    <a href="<?php echo e($msg->file_url); ?>" class="inline-flex items-center gap-2 text-xs font-semibold" style="color: #1F2E26; text-decoration: underline;">📎 <?php echo e($msg->file_name); ?></a>
                  <?php endif; ?>
                <?php endif; ?>
              </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <div class="rounded-3xl border-2 p-8 text-center" style="border-style: dashed; border-color: #D4CABC; background-color: #F8F2E6;">
                <p class="text-sm" style="color: #5E6E52;">Aucun message. Commencez la conversation ! 💬</p>
              </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- TAB: Fichiers -->
          <div id="tab-files" class="tab-content hidden">
            <div class="space-y-3">
              <?php $__empty_1 = true; $__currentLoopData = $group->messages->where('file_path', '!=', null); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <div class="rounded-2xl p-4" style="border: 1px solid #D4CABC; background: white;">
                <div class="flex items-center gap-3">
                  <div class="text-2xl">
                    <?php if($file->isImage()): ?>
                      📷
                    <?php elseif(str_ends_with($file->file_name, '.pdf')): ?>
                      📄
                    <?php elseif(str_ends_with($file->file_name, ['.doc', '.docx'])): ?>
                      📝
                    <?php else: ?>
                      📦
                    <?php endif; ?>
                  </div>
                  <div class="flex-1">
                    <p class="font-semibold text-sm" style="color: #1F2E26;"><?php echo e($file->file_name); ?></p>
                    <p class="text-xs" style="color: #5E6E52;">Par <?php echo e($file->user->prenom); ?> <?php echo e($file->user->nom); ?> • <?php echo e($file->created_at->diffForHumans()); ?></p>
                  </div>
                  <a href="<?php echo e($file->file_url); ?>" download class="rounded-full px-3 py-2 text-xs font-semibold text-white" style="background-color: #1F2E26;">Télécharger</a>
                </div>
              </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <div class="rounded-3xl border-2 p-8 text-center" style="border-style: dashed; border-color: #D4CABC; background-color: #F8F2E6;">
                <p class="text-sm" style="color: #5E6E52;">Aucun fichier partagé. Soyez le premier ! 📁</p>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Colonne latérale -->
        <aside class="space-y-4">
          <!-- Info -->
          <div class="overflow-hidden rounded-3xl" style="border: 1px solid #D4CABC; background-color: #F8F2E6;">
            <div class="border-b px-5 py-4" style="border-color: #D4CABC;">
              <h3 class="text-base font-semibold" style="color: #1F2E26;">À propos</h3>
            </div>
            <div class="space-y-3 p-5 text-sm">
              <div>
                <p class="font-semibold" style="color: #5E6E52;">Créé par</p>
                <p class="mt-1" style="color: #2F3A30;"><?php echo e($group->admin->prenom); ?> <?php echo e($group->admin->nom); ?></p>
              </div>
              <div class="border-t pt-3" style="border-color: #D4CABC;">
                <p class="font-semibold" style="color: #5E6E52;">Visibilité</p>
                <p class="mt-1" style="color: #2F3A30;"><?php echo e($group->visibilite === 'public' ? '🌐 Public' : '🔒 Privé'); ?></p>
              </div>
              <?php if($group->max_members): ?>
              <div class="border-t pt-3" style="border-color: #D4CABC;">
                <p class="font-semibold" style="color: #5E6E52;">Capacité</p>
                <p class="mt-1" style="color: #2F3A30;"><?php echo e($group->membres->count()); ?>/<?php echo e($group->max_members); ?></p>
              </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Admin -->
          <?php if($isAdmin): ?>
          <div class="overflow-hidden rounded-3xl" style="border: 1px solid #D4CABC; background: white;">
            <div class="border-b px-5 py-4" style="border-color: #D4CABC;">
              <h3 class="text-base font-semibold" style="color: #1F2E26;">Admin</h3>
            </div>
            <div class="p-5">
              <form action="<?php echo e(route('groupes.members.add', $group->slug)); ?>" method="POST" class="space-y-3 group-member-form">
                <?php echo csrf_field(); ?>
                <select name="user_ids[]" multiple size="6" required class="w-full rounded-2xl px-3 py-2 text-xs" style="border: 1px solid #D4CABC; background: white;">
                  <?php $__empty_1 = true; $__currentLoopData = Auth::user()->friends()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $friend): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <option value="<?php echo e($friend->id); ?>"><?php echo e($friend->prenom); ?> <?php echo e($friend->nom); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <option disabled>Aucun ami disponible</option>
                  <?php endif; ?>
                </select>
                <button type="submit" class="w-full rounded-2xl px-3 py-2 text-xs font-semibold text-white" style="background-color: #1F2E26;">Ajouter</button>
              </form>
            </div>
          </div>
          <?php endif; ?>

          <!-- Membres -->
          <div class="overflow-hidden rounded-3xl" style="border: 1px solid #D4CABC; background-color: #F8F2E6;">
            <div class="border-b px-5 py-4" style="border-color: #D4CABC;">
              <h3 class="text-base font-semibold" style="color: #1F2E26;">Membres (<?php echo e($group->membres->count()); ?>)</h3>
            </div>
            <div class="max-h-72 space-y-2 overflow-y-auto p-3">
              <?php $__empty_1 = true; $__currentLoopData = $group->membres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $membre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <div class="flex items-center gap-2 rounded-2xl p-2" style="background: rgba(255, 255, 255, 0.8);">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center overflow-hidden rounded-full font-bold uppercase text-white text-xs" style="background-color: #1F2E26;">
                  <?php echo e(strtoupper(substr($membre->prenom, 0, 1).substr($membre->nom, 0, 1))); ?>

                </div>
                <div class="min-w-0 flex-1">
                  <p class="text-xs font-semibold truncate" style="color: #1F2E26;"><?php echo e($membre->prenom); ?> <?php echo e($membre->nom); ?></p>
                  <p class="text-xs truncate" style="color: #5E6E52;"><?php echo e($membre->handle ? '@'.$membre->handle : 'ID '.$membre->id); ?></p>
                </div>
              </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <p class="p-2 text-center text-xs" style="color: #5E6E52;">Aucun membre</p>
              <?php endif; ?>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>

  <script>
    function switchTab(tabName) {
      // Hide all tabs
      document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
      // Show selected tab
      document.getElementById('tab-' + tabName).classList.remove('hidden');
      // Update button styles
      document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.classList.add('inactive');
      });
      document.getElementById('tab-' + tabName + '-btn').classList.remove('inactive');
      document.getElementById('tab-' + tabName + '-btn').classList.add('active');
    }
  </script>
</body>
</html>
<?php /**PATH C:\Users\Poolski\Desktop\DEV Project\USN.ci\resources\views/groupes/show.blade.php ENDPATH**/ ?>