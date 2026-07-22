{{-- resources/views/messages.blade.php --}}
@extends('layouts.app')

@section('title', 'Messages — USN')

@section('content')
  <div class="grid gap-6 lg:grid-cols-[320px_1fr] mt-6">
    <aside class="flex flex-col rounded-3xl bg-white/95 border border-ardoise/20 shadow-sm overflow-hidden dark:bg-slate-950/90 dark:border-slate-700">
      <div class="px-6 py-5 border-b border-ardoise/10 bg-white dark:bg-slate-950/90 dark:border-slate-700">
        <h1 class="text-xl font-semibold text-ardoise dark:text-gray-100">Messages</h1>
        <div class="mt-4 relative">
          <i class="ti ti-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" aria-hidden="true"></i>
          <input id="message-search" type="search" placeholder="Rechercher une conversation" aria-label="Rechercher une conversation" class="w-full rounded-full border border-ardoise/10 bg-[#F8F2E6] dark:bg-slate-800 dark:border-slate-700 pl-11 pr-4 py-3 text-sm text-ardoise dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-moutarde/40 dark:focus:ring-moutarde/40 dark:placeholder:text-gray-400" />
        </div>
      </div>
      <div class="overflow-y-auto p-4 space-y-3" role="list" style="max-height: calc(100vh - 220px);">
        @forelse($threads as $thread)
          <a href="{{ route('messages.conversation', $thread['friend']->handle ?? $thread['friend']->id) }}"
             role="listitem"
             data-search="{{ strtolower($thread['friend']->prenom . ' ' . $thread['friend']->nom . ' ' . ($thread['last']->body ?? '')) }}"
             class="thread-item flex gap-3 items-center rounded-3xl p-3 transition-colors {{ optional($selected)->id === $thread['friend']->id ? 'bg-kraft-light border border-ardoise/20 shadow-sm dark:bg-slate-900/80 dark:border-slate-700' : 'hover:bg-kraft-light hover:border hover:border-ardoise/10 dark:hover:bg-slate-900/80' }}" aria-label="Conversation avec {{ $thread['friend']->prenom }} {{ $thread['friend']->nom }}">
            <div class="flex-shrink-0 h-14 w-14 rounded-full bg-sauge/10 text-sauge grid place-items-center text-lg font-semibold text-ardoise">
              @if($thread['friend']->avatar)
                <img src="{{ asset('storage/'.$thread['friend']->avatar) }}" alt="{{ $thread['friend']->prenom }}" class="h-full w-full rounded-full object-cover" />
              @else
                {{ strtoupper(substr($thread['friend']->prenom,0,1).substr($thread['friend']->nom,0,1)) }}
              @endif
            </div>
            <div class="min-w-0 flex-1 overflow-hidden">
              <div class="flex items-center justify-between gap-2">
                <p class="truncate font-semibold text-ardoise">{{ $thread['friend']->prenom }} {{ $thread['friend']->nom }}</p>
                <div class="flex items-center gap-2">
                  @if(!empty($thread['unreadCount']) && $thread['unreadCount'] > 0)
                    <span class="unread-messages-badge inline-flex items-center justify-center rounded-full bg-moutarde px-2.5 py-1 text-[11px] font-semibold text-ardoise">{{ $thread['unreadCount'] }}</span>
                  @endif
                  <span class="text-[11px] text-gray-400">{{ $thread['last']->created_at->diffForHumans() }}</span>
                </div>
              </div>
              <p class="mt-1 text-sm text-gray-500 truncate">{{ Str::limit($thread['last']->body ?? 'Fichier joint', 60) }}</p>
            </div>
          </a>
        @empty
          <div class="rounded-3xl border border-ardoise/10 bg-kraft-light p-4 text-sm text-gray-600">
            Vous n’avez pas encore de conversations. Ajoutez un ami pour commencer à discuter.
          </div>
        @endforelse
      </div>
    </aside>

    <section class="flex flex-col rounded-3xl bg-white/95 border border-ardoise/20 shadow-sm overflow-hidden dark:bg-slate-950/90 dark:border-slate-700">
      <div class="px-6 py-5 border-b border-ardoise/10 bg-white dark:bg-slate-950/90 dark:border-slate-700">
        <div class="flex items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            @if($selected)
              <div class="flex-shrink-0 h-14 w-14 rounded-full bg-sauge/10 text-sauge grid place-items-center text-lg font-semibold text-ardoise overflow-hidden">
                @if($selected->avatar)
                  <img src="{{ asset('storage/'.$selected->avatar) }}" alt="{{ $selected->prenom }}" class="h-full w-full object-cover" />
                @else
                  {{ strtoupper(substr($selected->prenom,0,1).substr($selected->nom,0,1)) }}
                @endif
              </div>
            @endif
            <div>
              <h1 class="text-xl font-semibold text-ardoise">{{ $selected ? ($selected->prenom . ' ' . $selected->nom) : 'Sélectionnez une conversation' }}</h1>
              @if($selected)
                <p class="text-sm text-gray-500">{{ $selected->handle ? '@' . $selected->handle : 'ID ' . $selected->id }}</p>
              @else
                <p class="text-sm text-gray-500">Choisissez une discussion dans la colonne de gauche.</p>
              @endif
            </div>
          </div>
          @if($selected)
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
              <div class="flex items-center gap-3 text-sm text-gray-500">
                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-green-400"></span>
                Actif récemment
              </div>
              <div class="flex flex-wrap gap-3">
                <form action="{{ route('messages.call.start', ['handle' => $selected->handle ?? $selected->id]) }}" method="POST">
                  @csrf
                  <input type="hidden" name="type" value="audio">
                  <button type="submit" class="inline-flex h-12 w-12 items-center justify-center rounded-full border border-ardoise/20 bg-white text-ardoise hover:bg-ardoise/5 transition-colors" aria-label="Appel audio">
                    <i class="ti ti-phone"></i>
                  </button>
                </form>
                <form action="{{ route('messages.call.start', ['handle' => $selected->handle ?? $selected->id]) }}" method="POST">
                  @csrf
                  <input type="hidden" name="type" value="video">
                  <button type="submit" class="inline-flex h-12 w-12 items-center justify-center rounded-full border border-ardoise/20 bg-white text-ardoise hover:bg-ardoise/5 transition-colors" aria-label="Appel vidéo">
                    <i class="ti ti-device-tv"></i>
                  </button>
                </form>
                <a href="{{ route('messages.call.incoming') }}" class="rounded-full border border-ardoise/20 bg-white px-4 py-2 text-sm font-semibold text-ardoise hover:bg-ardoise/5 transition-colors">Appels entrants</a>
              </div>
            </div>
          @endif
        </div>
      </div>

      @if($selected)
        <div id="messages-container" class="flex-1 min-h-[40vh] overflow-y-auto px-6 py-5 space-y-4 bg-[#F3F1EB] dark:bg-slate-950/90">
          @forelse($messages as $message)
            <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
              <div data-own-message="{{ $message->sender_id === auth()->id() ? 'true' : 'false' }}" data-message-id="{{ $message->id }}" class="message-bubble max-w-[85%] rounded-[28px] p-4 shadow-sm relative {{ $message->sender_id === auth()->id() ? 'bg-[#F8F2E6] text-ardoise rounded-br-[6px] dark:bg-slate-800 dark:text-gray-100' : 'bg-white text-ardoise rounded-bl-[6px] border border-ardoise/10 dark:bg-slate-900/90 dark:text-gray-100 dark:border-slate-700' }}">
                @if($message->body)
                  <p class="text-sm leading-relaxed">{{ $message->body }}</p>
                @endif
                @if($message->attachment_path)
                  <div class="mt-3 rounded-3xl border border-ardoise/20 bg-white p-3 dark:border-slate-700 dark:bg-slate-900/90">
                    <p class="text-xs uppercase tracking-[.18em] text-gray-400 mb-2">Fichier joint</p>
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                      <div class="space-y-1">
                        <p class="text-sm font-medium text-ardoise">{{ $message->attachment_name }}</p>
                        <p class="text-xs text-gray-500">{{ $message->attachment_type }}</p>
                      </div>
                      <div class="flex gap-2">
                        <a href="{{ $message->attachment_url }}" target="_blank" class="rounded-full border border-ardoise/20 bg-white px-3 py-2 text-xs font-semibold text-ardoise hover:bg-ardoise/5 transition-colors">Ouvrir</a>
                        <a href="{{ $message->attachment_url }}" download class="rounded-full bg-moutarde px-3 py-2 text-xs font-semibold text-ardoise hover:bg-moutarde/90 transition-colors">Télécharger</a>
                      </div>
                    </div>
                  </div>
                @endif

                @if($message->sender_id === auth()->id())
                  <div class="message-actions-menu hidden absolute top-3 right-3 z-50 w-40 rounded-2xl border border-ardoise/10 bg-white p-2 shadow-lg dark:bg-slate-900 dark:border-slate-700">
                    <button type="button" class="message-action-edit inline-flex w-full items-center justify-center rounded-xl px-3 py-2 text-sm font-semibold text-ardoise hover:bg-ardoise/5">Modifier</button>
                    <button type="button" class="message-action-delete inline-flex w-full items-center justify-center rounded-xl px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">Supprimer</button>
                  </div>

                  <div class="message-edit-form hidden mt-3 rounded-3xl border border-ardoise/10 bg-[#F8F2E6] p-4 dark:bg-slate-900/90 dark:border-slate-700">
                    <form action="{{ route('messages.message.update', ['handle' => $selected->handle ?? $selected->id, 'message' => $message->id]) }}" method="POST" class="space-y-3">
                      @csrf
                      @method('PATCH')
                      <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Modifier le message</label>
                      <textarea name="body" rows="3" class="w-full resize-none rounded-2xl border border-ardoise/10 bg-white px-4 py-3 text-sm text-ardoise focus:outline-none focus:ring-2 focus:ring-moutarde/40 dark:bg-slate-950 dark:border-slate-700 dark:text-gray-100">{{ $message->body }}</textarea>
                      <div class="flex flex-wrap items-center justify-between gap-3">
                        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-full bg-ardoise px-4 text-sm font-semibold text-kraft hover:bg-ardoise-light transition-colors">Enregistrer</button>
                        <button type="button" class="message-edit-cancel-button inline-flex h-10 items-center justify-center rounded-full border border-ardoise/20 bg-white px-4 text-sm font-semibold text-ardoise hover:bg-ardoise/5 transition-colors">Annuler</button>
                      </div>
                    </form>
                  </div>

                  <form class="message-delete-form hidden" action="{{ route('messages.message.delete', ['handle' => $selected->handle ?? $selected->id, 'message' => $message->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                  </form>
                @endif

                <p class="text-[10px] text-gray-400 mt-3 text-right">{{ $message->created_at->format('d/m/Y H:i') }}</p>
              </div>
            </div>
          @empty
            <div class="rounded-3xl border border-ardoise/10 bg-kraft-light p-6 text-sm text-gray-600">
              Aucun message dans cette conversation. Envoyez le premier message pour démarrer.
            </div>
          @endforelse
        </div>

        <form action="{{ route('messages.send', ['handle' => $selected->handle ?? $selected->id]) }}" method="POST" enctype="multipart/form-data" class="border-t border-ardoise/10 bg-white p-6 dark:bg-slate-950/90 dark:border-slate-700" id="message-form">
          @csrf
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="flex items-center gap-3">
              <label for="message-attachment" class="flex h-12 w-12 items-center justify-center rounded-full border border-ardoise/10 bg-kraft-light text-ardoise cursor-pointer transition-colors hover:bg-kraft/90" aria-label="Ajouter une pièce jointe">
                <i class="ti ti-paperclip" aria-hidden="true"></i>
              </label>
              <button type="button" id="voice-record-button" class="inline-flex h-12 items-center justify-center rounded-full border border-ardoise/10 bg-kraft-light px-4 py-2 text-sm font-semibold text-ardoise hover:bg-kraft/90 transition-colors">🎤 Enregistrer</button>
            </div>
            <input type="file" name="attachment" id="message-attachment" class="hidden" accept="*/*">
            <textarea name="body" id="message-body" rows="1" class="min-h-[48px] flex-1 resize-none rounded-full border border-ardoise/10 bg-[#F8F2E6] dark:bg-slate-800 dark:border-slate-700 px-4 py-3 text-sm text-ardoise dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-moutarde/40 dark:focus:ring-moutarde/40 placeholder:text-gray-500 dark:placeholder:text-gray-400" placeholder="Écris un message..." aria-label="Écrire un message"></textarea>
            <button type="submit" id="message-send-button" class="inline-flex h-12 items-center justify-center rounded-full bg-ardoise px-6 text-sm font-semibold text-kraft hover:bg-ardoise-light transition-colors">Envoyer</button>
          </div>
          <div id="voice-preview" class="hidden mt-4 rounded-3xl border border-ardoise/10 bg-kraft-light p-4 text-sm text-gray-600 dark:bg-slate-900/90 dark:border-slate-700"></div>
          <div id="message-status" class="text-sm text-ardoise hidden mt-2" role="status" aria-live="polite"></div>
        </form>
      @else
        <div class="flex-1 p-6 border-t border-ardoise/10 bg-[#F3F1EB]">
          <div class="rounded-3xl border border-ardoise/10 bg-kraft-light p-6 text-sm text-gray-600">
            Sélectionnez une conversation dans la colonne de gauche pour lire et répondre aux messages.
          </div>
        </div>
      @endif
    </section>
  </div>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('message-form');
        const sendButton = document.getElementById('message-send-button');
        const statusElement = document.getElementById('message-status');
        const messagesContainer = document.getElementById('messages-container');
        const searchInput = document.getElementById('message-search');
        const threadItems = document.querySelectorAll('.thread-item');
        const textarea = document.getElementById('message-body');

        if (form && !messagesContainer) {
          return;
        }

        function scrollMessagesToBottom() {
          if (!messagesContainer) return;
          messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        if (form) {
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
                  'Accept': 'application/json',
                  'X-Requested-With': 'XMLHttpRequest',
                  'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                },
              });

              const contentType = response.headers.get('content-type') || '';
              const isJson = contentType.includes('application/json');
              const payload = isJson ? await response.json() : { message: await response.text() };

              if (!response.ok) {
                throw new Error(payload.message || 'Erreur lors de l’envoi du message.');
              }

              const message = payload;
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
              messagesContainer.scrollTop = messagesContainer.scrollHeight;
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
        }

        function scrollMessagesToBottom() {
          if (!messagesContainer) return;
          messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function escapeHtml(text) {
          const div = document.createElement('div');
          div.textContent = text;
          return div.innerHTML;
        }

        const voiceButton = document.getElementById('voice-record-button');
        const voicePreview = document.getElementById('voice-preview');
        let mediaRecorder;
        let audioChunks = [];

        if (voiceButton) {
          voiceButton.addEventListener('click', async function () {
            if (!navigator.mediaDevices || !window.MediaRecorder) {
              alert('Votre navigateur ne prend pas en charge l’enregistrement audio.');
              return;
            }

            if (!mediaRecorder || mediaRecorder.state === 'inactive') {
              try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];

                mediaRecorder.addEventListener('dataavailable', function (event) {
                  if (event.data && event.data.size > 0) {
                    audioChunks.push(event.data);
                  }
                });

                mediaRecorder.addEventListener('stop', function () {
                  const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                  const audioUrl = URL.createObjectURL(audioBlob);
                  voicePreview.innerHTML = `
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                      <div>
                        <p class="text-sm text-ardoise">Aperçu de l’enregistrement vocal</p>
                        <audio controls src="${audioUrl}" class="mt-2 w-full rounded-3xl border border-ardoise/10 bg-white p-2"></audio>
                      </div>
                      <button id="attach-voice-button" type="button" class="inline-flex h-12 items-center justify-center rounded-full bg-ardoise px-4 py-2 text-sm font-semibold text-kraft hover:bg-ardoise-light transition-colors">Joindre au message</button>
                    </div>
                  `;
                  voicePreview.classList.remove('hidden');

                  document.getElementById('attach-voice-button').addEventListener('click', function () {
                    const file = new File([audioBlob], 'message-voice.webm', { type: 'audio/webm' });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    document.getElementById('message-attachment').files = dataTransfer.files;
                    voicePreview.innerHTML = '<p class="text-sm text-ardoise">Fichier vocal prêt à être envoyé.</p>';
                  });
                });

                mediaRecorder.start();
                voiceButton.textContent = 'Arrêter';
                voiceButton.classList.add('bg-red-600', 'text-white');
              } catch (error) {
                alert('Impossible d’accéder au microphone.');
              }
            } else if (mediaRecorder.state === 'recording') {
              mediaRecorder.stop();
              voiceButton.textContent = '🎤 Enregistrer';
              voiceButton.classList.remove('bg-red-600', 'text-white');
            }
          });
        }

        function filterThreads() {
          if (!searchInput) return;
          const query = searchInput.value.trim().toLowerCase();
          threadItems.forEach(function (item) {
            const text = item.dataset.search || '';
            item.style.display = text.includes(query) ? 'flex' : 'none';
          });
        }

        function autoResizeTextarea() {
          if (!textarea) return;
          textarea.style.height = 'auto';
          textarea.style.height = `${textarea.scrollHeight}px`;
        }

        if (searchInput) {
          searchInput.addEventListener('input', filterThreads);
        }

        if (textarea) {
          textarea.addEventListener('input', autoResizeTextarea);
          autoResizeTextarea();
        }

        scrollMessagesToBottom();

        function hideAllActionMenus() {
          document.querySelectorAll('.message-actions-menu').forEach(function (menu) {
            menu.classList.add('hidden');
          });
        }

        function setupMessageActions() {
          const bubbles = document.querySelectorAll('.message-bubble[data-own-message="true"]');
          const longPressDelay = 500;

          bubbles.forEach(function (bubble) {
            let timer = null;

            function showMenu() {
              hideAllActionMenus();
              const menu = bubble.querySelector('.message-actions-menu');
              if (menu) {
                menu.classList.remove('hidden');
              }
            }

            function cancelPress() {
              if (timer) {
                clearTimeout(timer);
                timer = null;
              }
            }

            bubble.addEventListener('mousedown', function (event) {
              if (event.button !== 0) {
                return;
              }
              timer = setTimeout(showMenu, longPressDelay);
            });

            bubble.addEventListener('touchstart', function () {
              timer = setTimeout(showMenu, longPressDelay);
            });

            bubble.addEventListener('mouseup', cancelPress);
            bubble.addEventListener('mouseleave', cancelPress);
            bubble.addEventListener('touchend', cancelPress);
            bubble.addEventListener('touchcancel', cancelPress);
            bubble.addEventListener('contextmenu', function (event) {
              event.preventDefault();
              showMenu();
            });

            const editButton = bubble.querySelector('.message-action-edit');
            const deleteButton = bubble.querySelector('.message-action-delete');
            const editForm = bubble.querySelector('.message-edit-form');
            const deleteForm = bubble.querySelector('.message-delete-form');

            if (editButton && editForm) {
              editButton.addEventListener('click', function () {
                editForm.classList.toggle('hidden');
                hideAllActionMenus();
              });
            }

            if (deleteButton && deleteForm) {
              deleteButton.addEventListener('click', function () {
                hideAllActionMenus();
                if (confirm('Voulez-vous vraiment supprimer ce message ?')) {
                  deleteForm.submit();
                }
              });
            }
          });
        }

        setupMessageActions();

        document.addEventListener('click', function (event) {
          if (!event.target.closest('.message-actions-menu') && !event.target.closest('.message-bubble[data-own-message="true"]')) {
            hideAllActionMenus();
          }
        });

        document.addEventListener('touchstart', function (event) {
          if (!event.target.closest('.message-actions-menu') && !event.target.closest('.message-bubble[data-own-message="true"]')) {
            hideAllActionMenus();
          }
        });

        @if($selected)
          (async function () {
            try {
              const readUrl = "{{ route('messages.read', ['handle' => $selected->handle ?? $selected->id]) }}";
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
        @endif
      });
    </script>
  @endpush
@endsection
