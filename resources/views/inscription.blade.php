<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Créer un compte — USN</title>
<meta name="description" content="Rejoins ton campus sur USN : cours, groupes de travail, clubs et événements, au même endroit.">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Source+Serif+4:opsz,wght@8..60,400;8..60,500;8..60,600&family=Caveat:wght@500;600&display=swap" rel="stylesheet">

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
          serif:   ['"Source Serif 4"', 'serif'],
          hand:    ['"Caveat"', 'cursive'],
        },
      }
    }
  }
</script>

<style>
  html { scroll-behavior: smooth; }
  body{ background-color:#EFE6D3; color:#221E18; }
  .cork-texture{
    background-image: radial-gradient(rgba(239,230,211,0.07) 1px, transparent 1.4px);
    background-size: 16px 16px;
  }
  .paper-grid{
    background-image:
      linear-gradient(rgba(34,30,24,0.05) 1px, transparent 1px),
      linear-gradient(90deg, rgba(34,30,24,0.05) 1px, transparent 1px);
    background-size: 28px 28px;
  }
  .pin{
    position:absolute; width:14px;height:14px; border-radius:50%;
    background: radial-gradient(circle at 35% 30%, #e0775e, #963823 70%);
    box-shadow: 0 2px 3px rgba(0,0,0,0.45);
    top:-7px; left:50%; transform:translateX(-50%);
  }
  .note-card{ box-shadow: 0 10px 24px -8px rgba(0,0,0,0.35), 0 2px 4px rgba(0,0,0,0.15); }
  .stamp-btn{ transition: transform .15s ease, box-shadow .15s ease; }
  .stamp-btn:hover{ transform: translateY(-2px) rotate(-1deg); }
  .skip-link:focus{ clip:auto !important; width:auto !important; height:auto !important; overflow:visible !important; }
  *:focus-visible{ outline: 3px solid #E2A33B; outline-offset: 2px; border-radius: 2px; }
  @media (prefers-reduced-motion: reduce){ *{ animation:none !important; transition:none !important; } html{ scroll-behavior:auto; } }
  .field{
    width:100%; border-radius:.5rem; border:1.5px solid rgba(34,30,24,0.16);
    background: rgba(255,255,255,0.55); padding:.7rem 1rem; font-family:'Source Serif 4',serif;
    font-size:.95rem; outline:none; transition: border-color .15s ease, box-shadow .15s ease;
  }
  .field::placeholder{ color: rgba(34,30,24,0.4); }
  .field:focus{ border-color:#E2A33B; box-shadow: 0 0 0 3px rgba(226,163,59,0.25); background: rgba(255,255,255,0.85); }
  select.field{ appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='9' viewBox='0 0 14 9'%3E%3Cpath d='M1 1l6 6 6-6' fill='none' stroke='%23221E18' stroke-opacity='0.5' stroke-width='1.6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 1rem center; padding-right:2.4rem; }
</style>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body class="font-serif text-tinta antialiased">

<a href="#contenu" class="skip-link absolute -left-[999px] top-0 bg-moutarde text-ardoise font-display px-4 py-2 z-50">Aller au contenu principal</a>

<header class="sticky top-0 z-40 bg-ardoise text-kraft border-b border-black/20">
  <div class="max-w-6xl mx-auto px-5 sm:px-8 h-16 flex items-center justify-between">
  <a href="{{ url('/') }}" class="flex items-center gap-2.5 font-display font-semibold text-lg shrink-0">
      <span class="grid place-items-center w-9 h-9 rounded-full bg-moutarde text-ardoise font-display font-bold text-base ring-2 ring-kraft/30">USN</span>
      USN
    </a>
    <a href="{{ route('connexion') }}" class="font-display text-sm">
      <span class="hidden sm:inline text-kraft/70">Déjà un compte ?</span>
      <span class="ml-1 underline decoration-moutarde decoration-2 underline-offset-2 hover:text-moutarde transition-colors">Se connecter</span>
    </a>
  </div>
</header>

<main class="max-w-6xl mx-auto px-5 sm:px-8 flex flex-col lg:flex-row gap-8 py-14">

  <!-- PANNEAU GAUCHE : tableau d'affichage -->
  <section class="hidden lg:flex relative bg-ardoise text-kraft cork-texture flex-col justify-between p-10 sm:p-14 overflow-hidden">
    <div>
      <p class="font-display text-xs uppercase tracking-[0.2em] text-moutarde mb-4">Rejoindre mon campus</p>
      <h1 class="font-display font-semibold text-3xl leading-tight max-w-sm">
        Ton inscription, c'est ta première épingle sur le tableau.
      </h1>
    </div>

    <div class="relative w-72 self-center my-8 space-y-6">
      <div class="note-card relative bg-sauge text-kraft-light rounded-sm rotate-3 p-5">
        <span class="pin"></span>
        <p class="font-display text-[11px] uppercase tracking-wide text-kraft/70 font-semibold mb-1">🎭 Club étudiant</p>
        <p class="font-display font-semibold text-sm">Le club théâtre recrute</p>
        <p class="text-xs text-kraft-light/80 mt-1">Auditions ouvertes toute la semaine.</p>
      </div>
      <div class="note-card relative bg-kraft-light text-tinta rounded-sm -rotate-2 p-5 ml-6">
        <span class="pin"></span>
        <p class="font-hand text-lg leading-snug">« Inscrite en 2 minutes, j'avais déjà mon groupe de TD le soir même. »</p>
        <p class="mt-2 text-[11px] font-display text-tinta/50">Moussa T. · L1 Droit</p>
      </div>
    </div>

    <p class="text-sm text-kraft/55">✏️ Gratuit pour les étudiants · 42 campus francophones</p>
  </section>

  <!-- PANNEAU DROIT : formulaire -->
  <section class="bg-kraft-light paper-grid flex-1 flex items-center justify-center px-5 sm:px-8 py-14">
    <div class="w-full max-w-2xl rounded-[1.5rem] border border-black/10 bg-white/95 p-8 shadow-[0_22px_60px_-40px_rgba(34,30,24,0.55)]">

      <p class="font-display text-xs uppercase tracking-[0.2em] text-encre mb-3">Inscription</p>
      <h2 class="font-display font-semibold text-3xl mb-2">Crée ton compte</h2>
      <p class="text-tinta/65 mb-8">Une adresse universitaire suffit, on s'occupe du reste.</p>

      <form method="POST" action="{{ route('inscription.store') }}" class="space-y-6" novalidate>
        @csrf

        <div class="grid grid-cols-1 gap-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="prenom" class="block font-display text-sm font-medium mb-2">Prénom</label>
              <input id="prenom" name="prenom" type="text" autocomplete="given-name" required
                     value="{{ old('prenom') }}" placeholder="Aïcha" class="field">
              @error('prenom')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
              <label for="nom" class="block font-display text-sm font-medium mb-2">Nom</label>
              <input id="nom" name="nom" type="text" autocomplete="family-name" required
                     value="{{ old('nom') }}" placeholder="Koné" class="field">
              @error('nom')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="matricule" class="block font-display text-sm font-medium mb-2">Matricule étudiant</label>
              <input id="matricule" name="matricule" type="text" autocomplete="off" required
                     value="{{ old('matricule') }}" placeholder="ex. 21B0123" class="field">
              @error('matricule')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
              <p class="mt-2 text-xs text-tinta/50">Ce sera ton identifiant pour te connecter.</p>
            </div>
            <div>
              <label for="email" class="block font-display text-sm font-medium mb-2">Adresse e-mail universitaire</label>
              <input id="email" name="email" type="email" autocomplete="email" required
                     value="{{ old('email') }}" placeholder="prenom.nom@univ.ci" class="field">
              @error('email')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
          </div>

          <div>
            <label for="universite" class="block font-display text-sm font-medium mb-2">Ton campus</label>
            <select id="universite" name="universite" required class="field">
              <option value="" disabled {{ old('universite') ? '' : 'selected' }}>Choisis ton université</option>
              <option value="Université Félix Houphouët-Boigny" {{ old('universite') === 'Université Félix Houphouët-Boigny' ? 'selected' : '' }}>Université Félix Houphouët-Boigny</option>
              <option value="Université Alassane Ouattara" {{ old('universite') === 'Université Alassane Ouattara' ? 'selected' : '' }}>Université Alassane Ouattara</option>
              <option value="Université Jean Lorougnon Guédé" {{ old('universite') === 'Université Jean Lorougnon Guédé' ? 'selected' : '' }}>Université Jean Lorougnon Guédé</option>
              <option value="Institut National Polytechnique Félix Houphouët-Boigny" {{ old('universite') === 'Institut National Polytechnique Félix Houphouët-Boigny' ? 'selected' : '' }}>Institut National Polytechnique Félix Houphouët-Boigny</option>
              <option value="Université Nangui Abrogoua" {{ old('universite') === 'Université Nangui Abrogoua' ? 'selected' : '' }}>Université Nangui Abrogoua</option>
              <option value="Université de Bouaké" {{ old('universite') === 'Université de Bouaké' ? 'selected' : '' }}>Université de Bouaké</option>
              <option value="Université de Man" {{ old('universite') === 'Université de Man' ? 'selected' : '' }}>Université de Man</option>
              <option value="Université Internationale de Grand-Bassam" {{ old('universite') === 'Université Internationale de Grand-Bassam' ? 'selected' : '' }}>Université Internationale de Grand-Bassam</option>
              <option value="Université des Sciences et Technologies de Côte d'Ivoire" {{ old('universite') === "Université des Sciences et Technologies de Côte d'Ivoire" ? 'selected' : '' }}>Université des Sciences et Technologies de Côte d'Ivoire</option>
              <option value="Université Virtuelle de Côte d'Ivoire" {{ old('universite') === "Université Virtuelle de Côte d'Ivoire" ? 'selected' : '' }}>Université Virtuelle de Côte d'Ivoire</option>
              <option value="Institut Universitaire d'Abidjan" {{ old('universite') === "Institut Universitaire d'Abidjan" ? 'selected' : '' }}>Institut Universitaire d'Abidjan</option>
              <option value="ESATIC" {{ old('universite') === 'ESATIC' ? 'selected' : '' }}>ESATIC</option>
              <option value="PIGIER Côte d'Ivoire" @selected(old('universite') === 'PIGIER Côte d\'Ivoire')>PIGIER Côte d'Ivoire</option>
              <option value="IHEC Université Cocody" {{ old('universite') === 'IHEC Université Cocody' ? 'selected' : '' }}>IHEC Université Cocody</option>
              <option value="Université Catholique de l'Afrique de l'Ouest" {{ old('universite') === "Université Catholique de l'Afrique de l'Ouest" ? 'selected' : '' }}>Université Catholique de l'Afrique de l'Ouest</option>
              <option value="Mon université n'est pas dans la liste" @selected(old('universite') === 'Mon université n\'est pas dans la liste')>Mon université n'est pas dans la liste</option>
            </select>
            @error('universite')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="password" class="block font-display text-sm font-medium mb-2">Mot de passe</label>
              <div class="relative">
                <input id="password" name="password" type="password" autocomplete="new-password" required minlength="8"
                       placeholder="••••••••" class="field pr-11">
                <button type="button" data-target="password" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 text-tinta/45 hover:text-tinta/80" aria-label="Afficher le mot de passe" aria-pressed="false">
                  <svg class="eye-open w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12S5.5 5 12 5s9.5 7 9.5 7-3 7-9.5 7-9.5-7-9.5-7Z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                  <svg class="eye-closed w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M10.6 10.7a3 3 0 0 0 4.2 4.2M6.6 6.7C4.3 8.2 2.5 12 2.5 12s3 7 9.5 7c1.7 0 3.2-.4 4.5-1.1M17.5 17.4C19.7 15.9 21.5 12 21.5 12s-1-2.3-3-4.1"/>
                  </svg>
                </button>
              </div>
              @error('password')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
              <p class="mt-2 text-xs text-tinta/50">8 caractères minimum</p>
            </div>
            <div>
              <label for="password_confirmation" class="block font-display text-sm font-medium mb-2">Confirmer le mot de passe</label>
              <div class="relative">
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required minlength="8"
                       placeholder="••••••••" class="field pr-11">
                <button type="button" data-target="password2" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 text-tinta/45 hover:text-tinta/80" aria-label="Afficher le mot de passe" aria-pressed="false">
                  <svg class="eye-open w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12S5.5 5 12 5s9.5 7 9.5 7-3 7-9.5 7-9.5-7-9.5-7Z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                  <svg class="eye-closed w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M10.6 10.7a3 3 0 0 0 4.2 4.2M6.6 6.7C4.3 8.2 2.5 12 2.5 12s3 7 9.5 7c1.7 0 3.2-.4 4.5-1.1M17.5 17.4C19.7 15.9 21.5 12 21.5 12s-1-2.3-3-4.1"/>
                  </svg>
                </button>
              </div>
              @error('password_confirmation')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
          </div>

          <div class="flex items-start gap-3 text-sm">
            <input id="conditions" type="checkbox" name="conditions" required class="mt-1 h-4 w-4 rounded accent-[#E2A33B]">
            <label for="conditions" class="text-tinta/85">J'accepte les <a href="#" class="underline decoration-tinta/25 underline-offset-2 hover:text-encre">conditions d'utilisation</a> et la <a href="#" class="underline decoration-tinta/25 underline-offset-2 hover:text-encre">politique de confidentialité</a>.</label>
          </div>
          @error('conditions')<p class="text-xs text-red-600">{{ $message }}</p>@enderror

          <button type="submit" class="stamp-btn w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-md bg-moutarde text-ardoise font-display font-semibold shadow-lg">
            Créer mon compte
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
          </button>
        </div>
      </form>

      <p class="text-center text-sm mt-7">
        Déjà un compte ?
        <a href="{{ route('connexion') }}" class="font-display font-semibold text-encre hover:text-encre-dark transition-colors">Se connecter</a>
      </p>
    </div>
  </section>
</main>

<footer class="bg-ardoise text-kraft/45 text-xs text-center py-5">
  © 2026 USN. Conçu pour les étudiants, pas pour les annonceurs.
</footer>

<script>
  document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = document.getElementById(btn.dataset.target);
      const eyeOpen = btn.querySelector('.eye-open');
      const eyeClosed = btn.querySelector('.eye-closed');
      const showing = input.type === 'text';
      input.type = showing ? 'password' : 'text';
      eyeOpen.classList.toggle('hidden', !showing);
      eyeClosed.classList.toggle('hidden', showing);
      btn.setAttribute('aria-label', showing ? 'Afficher le mot de passe' : 'Masquer le mot de passe');
      btn.setAttribute('aria-pressed', String(!showing));
    });
  });
</script>

<!-- jQuery + Select2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $(function(){
    if (typeof $.fn.select2 !== 'undefined') {
      $('#universite').select2({
        placeholder: 'Choisis ton université',
        width: '100%'
      });
    }
  });
</script>

</body>
</html>