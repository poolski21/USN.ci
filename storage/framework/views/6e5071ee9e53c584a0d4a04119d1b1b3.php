<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion — USN</title>
<meta name="description" content="Connecte-toi à USN, le réseau social de ta vie étudiante.">

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
    background: rgba(255,255,255,0.55); padding:.75rem 1rem; font-family:'Source Serif 4',serif;
    font-size:.95rem; outline:none; transition: border-color .15s ease, box-shadow .15s ease;
  }
  .field::placeholder{ color: rgba(34,30,24,0.4); }
  .field:focus{ border-color:#E2A33B; box-shadow: 0 0 0 3px rgba(226,163,59,0.25); background: rgba(255,255,255,0.85); }
</style>
</head>

<body class="font-serif text-tinta antialiased">

<a href="#contenu" class="skip-link absolute -left-[999px] top-0 bg-moutarde text-ardoise font-display px-4 py-2 z-50">Aller au contenu principal</a>

<header class="sticky top-0 z-40 bg-ardoise text-kraft border-b border-black/20">
  <div class="max-w-6xl mx-auto px-5 sm:px-8 h-16 flex items-center justify-between">
  <a href="<?php echo e(url('/')); ?>" class="flex items-center gap-2.5 font-display font-semibold text-lg shrink-0">
      <span class="grid place-items-center w-9 h-9 rounded-full bg-moutarde text-ardoise font-display font-bold text-base ring-2 ring-kraft/30">USN</span>
      USN
    </a>
    <a href="<?php echo e(route('inscription')); ?>" class="font-display text-sm">
      <span class="hidden sm:inline text-kraft/70">Nouveau ici ?</span>
      <span class="ml-1 underline decoration-moutarde decoration-2 underline-offset-2 hover:text-moutarde transition-colors">Créer un compte</span>
    </a>
  </div>
</header>

<main id="contenu" class="min-h-[calc(100vh-4rem)] grid lg:grid-cols-2">

  <!-- PANNEAU GAUCHE : tableau d'affichage -->
  <section class="hidden lg:flex relative bg-ardoise text-kraft cork-texture flex-col justify-between p-10 sm:p-14 overflow-hidden">
    <div>
      <p class="font-display text-xs uppercase tracking-[0.2em] text-moutarde mb-4">Bon retour parmi nous</p>
      <h1 class="font-display font-semibold text-3xl leading-tight max-w-sm">
        Ton fil t'attend : cours, groupes et événements du campus.
      </h1>
    </div>

    <div class="relative w-72 self-center my-10">
      <div class="note-card relative bg-kraft-light text-tinta rounded-sm rotate-[-2deg] p-5">
        <span class="pin"></span>
        <p class="font-hand text-xl leading-snug">« J'ai trouvé mon binôme de TP en 10 minutes sur USN, plus jamais sans. »</p>
        <p class="mt-3 text-xs font-display text-tinta/55">Aïcha K. · L3 Économie</p>
      </div>
    </div>

    <p class="text-sm text-kraft/55">🎓 96&nbsp;000 étudiants actifs sur 42 campus francophones</p>
  </section>

  <!-- PANNEAU DROIT : formulaire -->
  <section class="bg-kraft-light paper-grid flex items-center justify-center px-5 sm:px-8 py-14">
    <div class="w-full max-w-md">

      <p class="font-display text-xs uppercase tracking-[0.2em] text-encre mb-3">Connexion</p>
      <h2 class="font-display font-semibold text-3xl mb-2">Content de te revoir</h2>
      <p class="text-tinta/65 mb-8">Connecte-toi avec ton matricule étudiant pour retrouver ton fil.</p>

      <form method="POST" action="<?php echo e(route('connexion.store')); ?>" class="space-y-5" novalidate>
        <?php echo csrf_field(); ?>
        <div>
          <label for="email" class="block font-display text-sm font-medium mb-1.5">Adresse e-mail</label>
          <input id="email" name="email" type="email" autocomplete="email" required
                 placeholder="prenom.nom@univ.fr" class="field">
        </div>

        <div>
          <label for="password" class="block font-display text-sm font-medium mb-1.5">Mot de passe</label>
          <div class="relative">
            <input id="password" name="password" type="password" autocomplete="current-password" required
                   placeholder="••••••••" class="field pr-11">
            <button type="button" id="toggle-password" aria-label="Afficher le mot de passe" aria-pressed="false"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-tinta/45 hover:text-tinta/80">
              <svg id="eye-open" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12S5.5 5 12 5s9.5 7 9.5 7-3 7-9.5 7-9.5-7-9.5-7Z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
              <svg id="eye-closed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M10.6 10.7a3 3 0 0 0 4.2 4.2M6.6 6.7C4.3 8.2 2.5 12 2.5 12s3 7 9.5 7c1.7 0 3.2-.4 4.5-1.1M17.5 17.4C19.7 15.9 21.5 12 21.5 12s-1-2.3-3-4.1"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="flex items-center justify-between text-sm">
          <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="remember" class="w-4 h-4 rounded accent-[#E2A33B]">
            <span>Se souvenir de moi</span>
          </label>
          <a href="#" class="font-display hover:text-encre transition-colors underline decoration-tinta/20 underline-offset-2">Mot de passe oublié ?</a>
        </div>

        <button type="submit" class="stamp-btn w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-md bg-moutarde text-ardoise font-display font-semibold shadow-lg">
          Se connecter
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </button>
      </form>

      <div class="flex items-center gap-3 my-7">
        <span class="h-px flex-1 bg-tinta/15"></span>
        <span class="text-xs font-display uppercase tracking-wide text-tinta/40">ou</span>
        <span class="h-px flex-1 bg-tinta/15"></span>
      </div>

      <p class="text-center text-sm">
        Pas encore de compte ?
        <a href="<?php echo e(route('inscription')); ?>" class="font-display font-semibold text-encre hover:text-encre-dark transition-colors">Rejoindre mon campus</a>
      </p>
    </div>
  </section>
</main>

<footer class="bg-ardoise text-kraft/45 text-xs text-center py-5">
  © 2026 USN. Conçu pour les étudiants, pas pour les annonceurs.
</footer>

<script>
  const toggleBtn = document.getElementById('toggle-password');
  const pwd = document.getElementById('password');
  const eyeOpen = document.getElementById('eye-open');
  const eyeClosed = document.getElementById('eye-closed');

  toggleBtn.addEventListener('click', () => {
    const showing = pwd.type === 'text';
    pwd.type = showing ? 'password' : 'text';
    eyeOpen.classList.toggle('hidden', !showing);
    eyeClosed.classList.toggle('hidden', showing);
    toggleBtn.setAttribute('aria-label', showing ? 'Afficher le mot de passe' : 'Masquer le mot de passe');
    toggleBtn.setAttribute('aria-pressed', String(!showing));
  });
</script>

</body>
</html><?php /**PATH C:\Users\Poolski\Desktop\DEV Project\USN.ci\resources\views/connexion.blade.php ENDPATH**/ ?>