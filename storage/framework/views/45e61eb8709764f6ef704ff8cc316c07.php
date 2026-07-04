<?php $__env->startSection('title', 'Messages — USN'); ?>

<?php $__env->startSection('content'); ?>
  <div class="grid gap-6 lg:grid-cols-[320px_1fr] mt-6">
    <aside class="rounded-3xl bg-white/90 border border-ardoise/20 shadow-sm p-6">
      <h1 class="text-xl font-semibold text-ardoise mb-4">Conversations</h1>
      <div class="space-y-3">
        <?php $__empty_1 = true; $__currentLoopData = $threads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $thread): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <a href="<?php echo e(route('messages.conversation', $thread['friend']->handle ?? $thread['friend']->id)); ?>"
             class="block rounded-3xl border border-ardoise/10 p-4 hover:border-ardoise/30 hover:bg-kraft-light transition-colors <?php echo e(optional($selected)->id === $thread['friend']->id ? 'border-ardoise/30 bg-kraft-light' : ''); ?>">
            <p class="font-semibold text-ardoise"><?php echo e($thread['friend']->prenom); ?> <?php echo e($thread['friend']->nom); ?></p>
            <p class="text-sm text-gray-500 mt-1 truncate"><?php echo e(Str::limit($thread['last']->body ?? 'Fichier joint', 60)); ?></p>
            <p class="text-xs text-gray-400 mt-2"><?php echo e($thread['last']->created_at->diffForHumans()); ?></p>
          </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <div class="rounded-3xl border border-ardoise/10 bg-kraft-light p-4 text-sm text-gray-600">
            Vous n’avez pas encore de conversations. Ajoutez un ami pour commencer à discuter.
          </div>
        <?php endif; ?>
      </div>
    </aside>

    <section class="rounded-3xl bg-white/90 border border-ardoise/20 shadow-sm p-6">
      <div class="flex items-center justify-between gap-4 mb-6">
        <div>
          <h1 class="text-xl font-semibold text-ardoise"><?php echo e($selected ? ($selected->prenom . ' ' . $selected->nom) : 'Sélectionnez une conversation'); ?></h1>
          <?php if($selected): ?>
            <p class="text-sm text-gray-500"><?php echo e($selected->handle ? '@' . $selected->handle : 'ID ' . $selected->id); ?></p>
          <?php else: ?>
            <p class="text-sm text-gray-500">Choisissez une discussion dans la colonne de gauche.</p>
          <?php endif; ?>
        </div>
      </div>

      <?php if($selected): ?>
        <div class="space-y-4 mb-6">
          <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="max-w-[85%] rounded-3xl p-4 <?php echo e($message->sender_id === auth()->id() ? 'ml-auto bg-kraft-light text-ardoise' : 'bg-white border border-ardoise/10 text-ardoise'); ?>">
              <?php if($message->body): ?>
                <p class="text-sm leading-relaxed"><?php echo e($message->body); ?></p>
              <?php endif; ?>
              <?php if($message->attachment_path): ?>
                <div class="mt-3 rounded-2xl border border-ardoise/20 bg-white p-3">
                  <p class="text-xs uppercase tracking-[.18em] text-gray-400 mb-2">Fichier joint</p>
                  <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div class="space-y-1">
                      <p class="text-sm font-medium text-ardoise"><?php echo e($message->attachment_name); ?></p>
                      <p class="text-xs text-gray-500"><?php echo e($message->attachment_type); ?></p>
                    </div>
                    <div class="flex gap-2">
                      <a href="<?php echo e($message->attachment_url); ?>" target="_blank" class="rounded-full border border-ardoise/20 bg-white px-3 py-2 text-xs font-semibold text-ardoise hover:bg-ardoise/5 transition-colors">Ouvrir</a>
                      <a href="<?php echo e($message->attachment_url); ?>" download class="rounded-full bg-moutarde px-3 py-2 text-xs font-semibold text-ardoise hover:bg-moutarde/90 transition-colors">Télécharger</a>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
              <p class="text-[10px] text-gray-400 mt-3 text-right"><?php echo e($message->created_at->format('d/m/Y H:i')); ?></p>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="rounded-3xl border border-ardoise/10 bg-kraft-light p-6 text-sm text-gray-600">
              Aucun message dans cette conversation. Envoyez le premier message pour démarrer.
            </div>
          <?php endif; ?>
        </div>

        <form action="<?php echo e(route('messages.send', ['handle' => $selected->handle ?? $selected->id])); ?>" method="POST" enctype="multipart/form-data" class="space-y-4" id="message-form">
          <?php echo csrf_field(); ?>
          <div>
            <label class="block text-sm font-medium text-ardoise mb-2">Message</label>
            <textarea name="body" id="message-body" rows="3" class="w-full rounded-3xl border border-ardoise/20 bg-white px-4 py-3 text-sm text-ardoise focus:outline-none focus:ring-2 focus:ring-moutarde/40" placeholder="Écris un message..."></textarea>
          </div>
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <label class="flex items-center gap-2 rounded-3xl border border-ardoise/20 bg-white px-4 py-3 text-sm text-ardoise cursor-pointer hover:bg-kraft-light transition-colors">
              <i class="ti ti-paperclip"></i>
              Joindre un fichier
              <input type="file" name="attachment" id="message-attachment" class="hidden" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip">
            </label>
            <button type="submit" id="message-send-button" class="rounded-3xl bg-ardoise px-5 py-3 text-sm font-semibold text-kraft hover:bg-ardoise-light transition-colors">Envoyer</button>
          </div>
          <div id="message-status" class="text-sm text-ardoise hidden"></div>
        </form>
      <?php else: ?>
        <div class="rounded-3xl border border-ardoise/10 bg-kraft-light p-6 text-sm text-gray-600">
          Sélectionnez une conversation dans la colonne de gauche pour lire et répondre aux messages.
        </div>
      <?php endif; ?>
    </section>
  </div>

  <?php $__env->startPush('scripts'); ?>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('message-form');
        const sendButton = document.getElementById('message-send-button');
        const statusElement = document.getElementById('message-status');
        const messagesContainer = document.querySelector('.space-y-4.mb-6');

        if (!form || !messagesContainer) {
          return;
        }

        form.addEventListener('submit', async function (event) {
          event.preventDefault();

          const formData = new FormData(form);
          const action = form.getAttribute('action');

          sendButton.disabled = true;
          sendButton.textContent = 'Envoi...';
          statusElement.classList.add('hidden');
          statusElement.textContent = '';

          try {
            const response = await fetch(action, {
              method: 'POST',
              body: formData,
              headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
              },
            });

            if (!response.ok) {
              const errorData = await response.json();
              throw new Error(errorData.message || 'Erreur lors de l’envoi du message.');
            }

            const message = await response.json();
            const newMessage = document.createElement('div');
            newMessage.className = 'max-w-[85%] rounded-3xl p-4 ml-auto bg-kraft-light text-ardoise';
            newMessage.innerHTML = `
              ${message.body ? `<p class="text-sm leading-relaxed">${escapeHtml(message.body)}</p>` : ''}
              ${message.attachment ? `
                <div class="mt-3 rounded-2xl border border-ardoise/20 bg-white p-3">
                  <p class="text-xs uppercase tracking-[.18em] text-gray-400 mb-2">Fichier joint</p>
                  <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div class="space-y-1">
                      <p class="text-sm font-medium text-ardoise">${escapeHtml(message.attachment.name)}</p>
                      <p class="text-xs text-gray-500">${escapeHtml(message.attachment.type)}</p>
                    </div>
                    <div class="flex gap-2">
                      <a href="${escapeHtml(message.attachment.url)}" target="_blank" class="rounded-full border border-ardoise/20 bg-white px-3 py-2 text-xs font-semibold text-ardoise hover:bg-ardoise/5 transition-colors">Ouvrir</a>
                      <a href="${escapeHtml(message.attachment.url)}" download class="rounded-full bg-moutarde px-3 py-2 text-xs font-semibold text-ardoise hover:bg-moutarde/90 transition-colors">Télécharger</a>
                    </div>
                  </div>
                </div>
              ` : ''}
              <p class="text-[10px] text-gray-400 mt-3 text-right">${escapeHtml(message.created_at)}</p>
            `;

            messagesContainer.appendChild(newMessage);
            form.reset();
            statusElement.textContent = 'Message envoyé.';
            statusElement.classList.remove('hidden');
          } catch (error) {
            statusElement.textContent = error.message;
            statusElement.classList.remove('hidden');
          } finally {
            sendButton.disabled = false;
            sendButton.textContent = 'Envoyer';
          }
        });

        function escapeHtml(text) {
          const div = document.createElement('div');
          div.textContent = text;
          return div.innerHTML;
        }

        // Mark messages as read via AJAX on load (if a conversation is selected)
        <?php if($selected): ?>
          (async function () {
            try {
              const readUrl = "<?php echo e(route('messages.read', ['handle' => $selected->handle ?? $selected->id])); ?>";
              const token = document.querySelector('input[name="_token"]').value;
              const res = await fetch(readUrl, {
                method: 'POST',
                headers: {
                  'X-Requested-With': 'XMLHttpRequest',
                  'X-CSRF-TOKEN': token,
                  'Accept': 'application/json',
                },
              });
              if (!res.ok) return;
              const data = await res.json();
              document.querySelectorAll('.unread-messages-badge').forEach(function (el) {
                if (data.unreadMessages && data.unreadMessages > 0) {
                  el.textContent = data.unreadMessages;
                } else {
                  el.remove();
                }
              });
            } catch (e) {
              // silently ignore
            }
          })();
        <?php endif; ?>
      });
    </script>
  <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Poolski\Desktop\DEV Project\USN.ci\resources\views/messages.blade.php ENDPATH**/ ?>