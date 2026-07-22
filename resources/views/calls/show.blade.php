@extends('layouts.app')

@section('title', 'Appel — USN')

@section('content')
  <div class="container mx-auto mt-6">
    <div class="rounded-3xl bg-white/95 border border-ardoise/20 p-6 shadow-sm dark:bg-slate-950/90 dark:border-slate-700">
      <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
        <div>
          <p class="text-sm text-gray-500 uppercase tracking-[.18em]">Appel {{ $session->type }}</p>
          <h1 class="text-2xl font-semibold text-ardoise">{{ $other->prenom }} {{ $other->nom }}</h1>
          <p class="mt-1 text-sm text-gray-500">{{ $other->handle ? '@' . $other->handle : 'ID ' . $other->id }}</p>
        </div>

        <div class="flex flex-wrap gap-3">
          @if($role === 'callee' && $session->status === 'pending')
            <button id="accept-call" class="rounded-full bg-moutarde px-4 py-2 text-sm font-semibold text-ardoise hover:bg-moutarde/90 transition-colors">Accepter</button>
            <button id="reject-call" class="rounded-full border border-ardoise/20 bg-white px-4 py-2 text-sm font-semibold text-ardoise hover:bg-ardoise/5 transition-colors">Refuser</button>
          @else
            <button id="hangup-call" class="rounded-full bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition-colors">Raccrocher</button>
          @endif
        </div>
      </div>

      <div class="mt-8 grid gap-6 lg:grid-cols-[1.4fr_1fr]">
        <div class="rounded-3xl border border-ardoise/10 bg-kraft-light p-5 dark:border-slate-700 dark:bg-slate-900/90">
          <p class="text-sm text-gray-500">Statut de l’appel</p>
          <p class="mt-2 text-lg font-medium text-ardoise" id="call-status">{{ ucfirst($session->status) }}</p>
          <p class="text-sm text-gray-500 mt-4">Utilisez ce tenant pour les échanges WebRTC et les mises à jour de statut.</p>
        </div>

        <div class="rounded-3xl border border-ardoise/10 bg-white p-5 dark:border-slate-700 dark:bg-slate-900/90">
          <p class="text-sm text-gray-500 uppercase tracking-[.18em]">Flux média</p>
          <div class="mt-4 space-y-4">
            <div>
              <p class="text-sm text-gray-500">Votre flux</p>
              <video id="local-video" autoplay muted playsinline class="mt-3 w-full rounded-3xl bg-black/5"></video>
              <audio id="local-audio" controls muted class="mt-3 w-full rounded-3xl bg-black/5 hidden"></audio>
            </div>
            <div>
              <p class="text-sm text-gray-500">Flux distant</p>
              <video id="remote-video" autoplay playsinline class="mt-3 w-full rounded-3xl bg-black/5"></video>
              <audio id="remote-audio" controls class="mt-3 w-full rounded-3xl bg-black/5 hidden"></audio>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
    <script>
      const callSessionId = {{ $session->id }};
      const callRole = '{{ $role }}';
      const callType = '{{ $session->type }}';
      const callStatusEl = document.getElementById('call-status');
      const localVideo = document.getElementById('local-video');
      const remoteVideo = document.getElementById('remote-video');
      const localAudio = document.getElementById('local-audio');
      const remoteAudio = document.getElementById('remote-audio');
      const acceptButton = document.getElementById('accept-call');
      const rejectButton = document.getElementById('reject-call');
      const hangupButton = document.getElementById('hangup-call');

      let peerConnection;
      let localStream;
      let remoteCandidates = [];
      let isCallActive = false;
      let pendingOffer = null;
      let answerApplied = false;

      async function csrfFetch(url, data) {
        return fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.USN.csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify(data),
        });
      }

      function updateMediaVisibility() {
        if (callType === 'video') {
          localVideo.classList.remove('hidden');
          remoteVideo.classList.remove('hidden');
          localAudio.classList.add('hidden');
          remoteAudio.classList.add('hidden');
        } else {
          localVideo.classList.add('hidden');
          remoteVideo.classList.add('hidden');
          localAudio.classList.remove('hidden');
          remoteAudio.classList.remove('hidden');
        }
      }

      async function createPeerConnection() {
        peerConnection = new RTCPeerConnection({
          iceServers: [{ urls: 'stun:stun.l.google.com:19302' }],
        });

        peerConnection.addEventListener('icecandidate', async event => {
          if (!event.candidate) return;
          await csrfFetch('{{ route('messages.call.candidate', ['session' => $session->id]) }}', {
            role: callRole,
            candidate: event.candidate.toJSON(),
          });
        });

        peerConnection.addEventListener('track', event => {
          if (event.streams && event.streams[0]) {
            const stream = event.streams[0];
            if (callType === 'video') {
              remoteVideo.srcObject = stream;
            } else {
              remoteAudio.srcObject = stream;
            }
          }
        });
      }

      async function getLocalStream() {
        const constraints = callType === 'video' ? { audio: true, video: true } : { audio: true };
        localStream = await navigator.mediaDevices.getUserMedia(constraints);

        if (callType === 'video') {
          localVideo.srcObject = localStream;
        } else {
          localAudio.srcObject = localStream;
        }

        localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));
      }

      async function applyRemoteCandidates(candidates) {
        for (const candidate of candidates) {
          const key = JSON.stringify(candidate);
          if (remoteCandidates.includes(key)) continue;
          remoteCandidates.push(key);
          try {
            await peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
          } catch (error) {
            console.warn('Erreur candidate distante', error);
          }
        }
      }

      async function pollCallSession() {
        const response = await fetch('{{ route('messages.call.status', ['session' => $session->id]) }}');
        if (!response.ok) return;
        const data = await response.json();

        callStatusEl.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);

        if (data.status === 'rejected' || data.status === 'ended') {
          isCallActive = false;
          return;
        }

        if (callRole === 'caller' && data.answer && !answerApplied) {
          answerApplied = true;
          const answerDesc = new RTCSessionDescription(data.answer);
          await peerConnection.setRemoteDescription(answerDesc);
        }

        if (data.caller_candidates && data.caller_candidates.length > 0 && callRole === 'callee') {
          await applyRemoteCandidates(data.caller_candidates);
        }

        if (data.callee_candidates && data.callee_candidates.length > 0 && callRole === 'caller') {
          await applyRemoteCandidates(data.callee_candidates);
        }

        if (callRole === 'callee' && !pendingOffer && data.offer) {
          pendingOffer = data.offer;
          if (acceptButton) {
            acceptButton.disabled = false;
          }
        }
      }

      async function startCaller() {
        await createPeerConnection();
        await getLocalStream();
        updateMediaVisibility();

        const offer = await peerConnection.createOffer();
        await peerConnection.setLocalDescription(offer);

        await csrfFetch('{{ route('messages.call.offer', ['session' => $session->id]) }}', {
          offer: offer.toJSON(),
        });

        isCallActive = true;
      }

      async function startCallee() {
        if (!pendingOffer) {
          alert('En attente de l’offre du correspondant...');
          return;
        }

        await createPeerConnection();
        await getLocalStream();
        updateMediaVisibility();

        const offerDesc = new RTCSessionDescription(pendingOffer);
        await peerConnection.setRemoteDescription(offerDesc);

        const answer = await peerConnection.createAnswer();
        await peerConnection.setLocalDescription(answer);

        await csrfFetch('{{ route('messages.call.answer', ['session' => $session->id]) }}', {
          answer: answer.toJSON(),
        });

        isCallActive = true;
      }

      async function initializeCall() {
        updateMediaVisibility();

        if (callRole === 'caller') {
          await startCaller();
        }

        if (callRole === 'callee') {
          if (acceptButton) {
            acceptButton.disabled = true;
            acceptButton.addEventListener('click', async function () {
              acceptButton.disabled = true;
              await startCallee();
            });
          }
        }
      }

      hangupButton?.addEventListener('click', async function () {
        await csrfFetch('{{ route('messages.call.hangup', ['session' => $session->id]) }}', {});
        callStatusEl.textContent = 'Terminé';
        isCallActive = false;
      });

      rejectButton?.addEventListener('click', async function () {
        await csrfFetch('{{ route('messages.call.reject', ['session' => $session->id]) }}', {});
        callStatusEl.textContent = 'Refusé';
        isCallActive = false;
      });

      document.addEventListener('DOMContentLoaded', async function () {
        try {
          await initializeCall();
        } catch (error) {
          console.error(error);
          alert('Erreur lors de l’initialisation de l’appel.');
        }

        setInterval(pollCallSession, 3000);
      });
    </script>
  @endpush
@endsection
