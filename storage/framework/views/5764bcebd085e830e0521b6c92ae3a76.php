<?php $__env->startSection('title', 'Recherche d\'amis — USN'); ?>

<?php $__env->startSection('content'); ?>
  <div class="mb-6 rounded-3xl bg-white/90 border border-ardoise/20 p-5 shadow-sm mt-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
      <div>
        <h1 class="text-2xl font-semibold text-ardoise">Recherche d'amis</h1>
        <p class="text-sm text-gray-500 mt-1">Trouvez un ami par son nom, prénom ou sa filière.</p>
      </div>
      <form action="<?php echo e(route('search')); ?>" method="GET" class="flex items-center gap-2">
        <label for="search-query" class="sr-only">Chercher un ami</label>
        <input id="search-query" name="q" type="search" value="<?php echo e(old('q', $q)); ?>" placeholder="Entrez un nom, prénom ou filière" class="rounded-full border border-ardoise/20 bg-white/90 px-4 py-2 text-sm text-ardoise focus:border-moutarde focus:outline-none">
        <button type="submit" class="rounded-full bg-moutarde px-4 py-2 text-sm font-semibold text-ardoise hover:bg-moutarde/90 transition-colors">Rechercher</button>
      </form>
    </div>

    <?php if($q === ''): ?>
      <div class="rounded-2xl bg-kraft-light/80 border border-kraft-dark/20 p-5 text-sm text-ardoise">
        Entrez un terme de recherche pour trouver des amis.
      </div>
    <?php elseif($results->isEmpty()): ?>
      <div class="rounded-2xl bg-red-50 border border-red-200 p-5 text-sm text-red-700">
        Aucun résultat trouvé pour "<?php echo e($q); ?>".
      </div>
    <?php else: ?>
      <div class="grid gap-4">
        <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="rounded-3xl border border-ardoise/10 bg-white p-5 shadow-sm transition hover:border-moutarde/30 hover:shadow-md">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
              <div>
                <h2 class="text-lg font-semibold text-ardoise"><a href="<?php echo e(route('profil.show', $user->handle ?? $user->id)); ?>" class="hover:text-moutarde transition-colors"><?php echo e($user->name); ?></a></h2>
                <p class="text-sm text-gray-500"><?php echo e($user->prenom); ?> <?php echo e($user->nom); ?></p>
                <p class="text-sm text-ardoise mt-2">
                  <?php echo e($user->universite ?? 'Université non renseignée'); ?>

                  <?php if($user->filiere || $user->niveau): ?>
                    · <?php echo e($user->filiere ?? 'Filière non renseignée'); ?><?php echo e($user->niveau ? ' · ' . $user->niveau : ''); ?>

                  <?php endif; ?>
                </p>
              </div>
              <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('profil.show', $user->handle ?? $user->id)); ?>" class="rounded-full border border-ardoise/10 bg-white px-4 py-2 text-sm text-ardoise hover:bg-ardoise/5 transition-colors">Voir le profil</a>
                <?php if(Auth::id() && Auth::id() !== $user->id): ?>
                  <form action="<?php echo e(route('friend.requests.send', $user->handle ?? $user->id)); ?>" method="POST" class="inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="rounded-full bg-moutarde px-4 py-2 text-sm font-semibold text-ardoise hover:bg-moutarde/90 transition-colors">Ajouter</button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>

      <div class="mt-6">
        <?php echo e($results->links()); ?>

      </div>
    <?php endif; ?>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Poolski\Desktop\DEV Project\USN.ci\resources\views/search.blade.php ENDPATH**/ ?>