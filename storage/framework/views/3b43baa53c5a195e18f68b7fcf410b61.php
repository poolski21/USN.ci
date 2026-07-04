<?php $__env->startSection('title', 'Notifications — USN'); ?>

<?php $__env->startSection('content'); ?>
  <div class="rounded-3xl bg-white/90 border border-ardoise/20 p-6 shadow-sm mt-6">
    <h1 class="text-2xl font-semibold text-ardoise mb-3">Notifications</h1>
    <p class="text-sm text-gray-600">Vos notifications d’activité apparaîtront ici dès qu’il y aura des événements importants.</p>

    <?php if($notifications->isEmpty()): ?>
      <div class="mt-6 rounded-3xl bg-kraft-light border border-kraft-dark/40 p-6 text-sm text-gray-600">
        Vous n’avez aucune notification pour le moment.
      </div>
    <?php else: ?>
      <div class="mt-6 space-y-4">
        <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="rounded-3xl border p-5 shadow-sm <?php echo e($notification->read_at ? 'bg-kraft-light border-kraft-dark/30' : 'bg-white border-ardoise/20'); ?>">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
              <div class="min-w-0">
                <?php if($notification->type === 'friend_request'): ?>
                  <p class="text-sm text-ardoise">
                    Nouvelle demande d’ami de
                    <a href="<?php echo e(route('profil.show', $notification->data['sender_handle'] ?? $notification->data['sender_id'])); ?>" class="font-semibold text-moutarde hover:text-moutarde-dark transition-colors">
                      <?php echo e($notification->data['sender_name'] ?? 'Utilisateur'); ?>

                    </a>
                    .
                  </p>
                <?php elseif($notification->type === 'friend_request_accepted'): ?>
                  <p class="text-sm text-ardoise">
                    <?php echo e($notification->data['receiver_name'] ?? 'Quelqu’un'); ?> a accepté votre demande d’ami.
                  </p>
                <?php else: ?>
                  <p class="text-sm text-ardoise">Notification reçue : <?php echo e(str_replace('_', ' ', ucfirst($notification->type))); ?>.</p>
                <?php endif; ?>
                <p class="text-xs text-gray-500 mt-2"><?php echo e($notification->created_at->diffForHumans()); ?>

                  <?php if($notification->read_at): ?>
                    · Lu
                  <?php else: ?>
                    · Non lu
                  <?php endif; ?>
                </p>
              </div>
              <div class="flex flex-wrap items-center gap-2">
                <?php $friendRequestId = $notification->friendRequestId(); ?>
                <?php if($notification->type === 'friend_request' && $friendRequestId): ?>
                  <form action="<?php echo e(route('friend.requests.accept', $friendRequestId)); ?>" method="POST" class="inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="rounded-full bg-moutarde px-4 py-2 text-sm font-semibold text-ardoise hover:bg-moutarde/90 transition-colors">Accepter</button>
                  </form>
                  <form action="<?php echo e(route('friend.requests.decline', $friendRequestId)); ?>" method="POST" class="inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="rounded-full border border-ardoise/20 px-4 py-2 text-sm text-ardoise hover:bg-ardoise/10 transition-colors">Refuser</button>
                  </form>
                <?php elseif($notification->type === 'friend_request'): ?>
                  <span class="text-sm text-gray-500">Demande déjà obsolète.</span>
                <?php endif; ?>
                <?php if(! $notification->read_at): ?>
                  <form action="<?php echo e(route('notifications.read', $notification->id)); ?>" method="POST" class="inline ajax-mark-read">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="rounded-full border border-gray-200 bg-white px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 transition-colors">Marquer comme lu</button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    <?php endif; ?>
  </div>
  <?php $__env->startPush('scripts'); ?>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form.ajax-mark-read').forEach(function (form) {
          form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const action = form.getAttribute('action');
            const token = form.querySelector('input[name="_token"]').value;

            try {
              const res = await fetch(action, {
                method: 'POST',
                headers: {
                  'X-Requested-With': 'XMLHttpRequest',
                  'X-CSRF-TOKEN': token,
                  'Accept': 'application/json',
                },
              });

              if (!res.ok) throw new Error('Erreur réseau');

              const data = await res.json();

              // Update all notification badges
              document.querySelectorAll('.unread-notifications-badge').forEach(function (el) {
                if (data.unreadNotifications && data.unreadNotifications > 0) {
                  el.textContent = data.unreadNotifications;
                } else {
                  el.remove();
                }
              });

              // Update UI for this notification: remove the button and mark as read
              form.remove();
              const card = form.closest('.rounded-3xl');
              if (card) {
                card.classList.remove('bg-white', 'border-ardoise/20');
                card.classList.add('bg-kraft-light', 'border-kraft-dark/30');
                const status = card.querySelector('.text-xs');
                if (status) status.innerHTML = status.innerHTML.replace('· Non lu', '· Lu');
              }
            } catch (err) {
              console.error(err);
              alert('Impossible de marquer la notification comme lue.');
            }
          });
        });
      });
    </script>
  <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Poolski\Desktop\DEV Project\USN.ci\resources\views/notifications.blade.php ENDPATH**/ ?>