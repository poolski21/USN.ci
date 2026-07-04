{{-- resources/views/messages.blade.php --}}
@extends('layouts.app')

@section('title', 'Messages — USN')

@section('content')
  <div class="grid gap-6 lg:grid-cols-[320px_1fr] mt-6">
    <aside class="rounded-3xl bg-white/90 border border-ardoise/20 shadow-sm p-6">
      <h1 class="text-xl font-semibold text-ardoise mb-4">Conversations</h1>
      <div class="space-y-3">
        @forelse($threads as $thread)
          <a href="{{ route('messages.conversation', $thread['friend']->handle ?? $thread['friend']->id) }}"
             class="block rounded-3xl border border-ardoise/10 p-4 hover:border-ardoise/30 hover:bg-kraft-light transition-colors {{ optional($selected)->id === $thread['friend']->id ? 'border-ardoise/30 bg-kraft-light' : '' }}">
            <p class="font-semibold text-ardoise">{{ $thread['friend']->prenom }} {{ $thread['friend']->nom }}</p>
            <p class="text-sm text-gray-500 mt-1 truncate">{{ Str::limit($thread['last']->body ?? 'Fichier joint', 60) }}</p>
            <p class="text-xs text-gray-400 mt-2">{{ $thread['last']->created_at->diffForHumans() }}</p>
          </a>
        @empty
          <div class="rounded-3xl border border-ardoise/10 bg-kraft-light p-4 text-sm text-gray-600">
            Vous n’avez pas encore de conversations. Ajoutez un ami pour commencer à discuter.
          </div>
        @endforelse
      </div>
    </aside>

    <section class="rounded-3xl bg-white/90 border border-ardoise/20 shadow-sm p-6">
      <div class="flex items-center justify-between gap-4 mb-6">
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
        <div class="space-y-4 mb-6">
          @forelse($messages as $message)
            <div class="max-w-[85%] rounded-3xl p-4 {{ $message->sender_id === auth()->id() ? 'ml-auto bg-kraft-light text-ardoise' : 'bg-white border border-ardoise/10 text-ardoise' }}">
              @if($message->body)
                <p class="text-sm leading-relaxed">{{ $message->body }}</p>
              @endif
              @if($message->attachment_path)
                <div class="mt-3 rounded-2xl border border-ardoise/20 bg-white p-3">
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
              <p class="text-[10px] text-gray-400 mt-3 text-right">{{ $message->created_at->format('d/m/Y H:i') }}</p>
            </div>
          @empty
            <div class="rounded-3xl border border-ardoise/10 bg-kraft-light p-6 text-sm text-gray-600">
              Aucun message dans cette conversation. Envoyez le premier message pour démarrer.
            </div>
          @endforelse
        </div>

        <form action="{{ route('messages.send', ['handle' => $selected->handle ?? $selected->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-4" id="message-form">
          @csrf
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
      @else
        <div class="rounded-3xl border border-ardoise/10 bg-kraft-light p-6 text-sm text-gray-600">
          Sélectionnez une conversation dans la colonne de gauche pour lire et répondre aux messages.
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
