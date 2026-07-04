<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>USN — Le réseau social de ta vie étudiante</title>
<meta name="description" content="USN.ci connecte les étudiants d'un même campus : cours, groupes de travail, clubs, événements et petites annonces, au même endroit.">

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
  :root{
    --grain-dot: rgba(239,230,211,0.06);
  }
  html { scroll-behavior: smooth; }
  body{
    background-color: #EFE6D3;
    color:#221E18;
  }
  /* paper grid texture for kraft sections */
  .paper-grid{
    background-image:
      linear-gradient(rgba(34,30,24,0.05) 1px, transparent 1px),
      linear-gradient(90deg, rgba(34,30,24,0.05) 1px, transparent 1px);
    background-size: 28px 28px;
  }
  /* corkboard dotted texture for dark hero */
  .cork-texture{
    background-image: radial-gradient(rgba(239,230,211,0.07) 1px, transparent 1.4px);
    background-size: 16px 16px;
  }
  .pin{
    position:absolute;
    width:14px;height:14px;
    border-radius:50%;
    background: radial-gradient(circle at 35% 30%, #e0775e, #963823 70%);
    box-shadow: 0 2px 3px rgba(0,0,0,0.45);
    top:-7px; left:50%; transform:translateX(-50%);
  }
  .note-card{
    box-shadow: 0 10px 24px -8px rgba(0,0,0,0.35), 0 2px 4px rgba(0,0,0,0.15);
  }
  .skip-link:focus{
    clip:auto !important; width:auto !important; height:auto !important; overflow:visible !important;
  }
  *:focus-visible{
    outline: 3px solid #E2A33B;
    outline-offset: 2px;
    border-radius: 2px;
  }
  .stamp-btn{ transition: transform .15s ease, box-shadow .15s ease; }
  .stamp-btn:hover{ transform: translateY(-2px) rotate(-1deg); }
  .lift{ transition: transform .25s ease, box-shadow .25s ease; }
  .lift:hover{ transform: translateY(-6px) rotate(0deg) !important; box-shadow: 0 18px 30px -10px rgba(0,0,0,0.3); }

  @media (prefers-reduced-motion: reduce){
    *{ animation: none !important; transition: none !important; }
    html{ scroll-behavior: auto; }
  }

  .fade-up{
    opacity:0; transform: translateY(18px);
    transition: opacity .6s ease, transform .6s ease;
  }
  .fade-up.is-visible{ opacity:1; transform:none; }

  .underline-squiggle{
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='10' viewBox='0 0 120 10'%3E%3Cpath d='M0,6 Q15,0 30,6 T60,6 T90,6 T120,6' fill='none' stroke='%23E2A33B' stroke-width='3'/%3E%3C/svg%3E");
    background-repeat: repeat-x;
    background-position: bottom 1px left;
    background-size: 60px 8px;
  }
</style>
</head>

<body class="font-serif text-tinta antialiased">

<a href="#contenu" class="skip-link absolute -left-[999px] top-0 bg-moutarde text-ardoise font-display px-4 py-2 z-50">Aller au contenu principal</a>

<!-- NAV -->
<header class="sticky top-0 z-40 bg-ardoise text-kraft border-b border-black/20">
  <div class="max-w-6xl mx-auto px-5 sm:px-8 h-16 flex items-center justify-between">
    <a href="<?php echo e(url('/')); ?>" class="flex items-center gap-2.5 font-display font-semibold text-lg shrink-0">
      <span class="grid place-items-center w-9 h-9 rounded-full bg-moutarde text-ardoise font-display font-bold text-base ring-2 ring-kraft/30">USN</span>
      USN
    </a>

    <nav class="hidden md:flex items-center gap-8 font-display text-sm" aria-label="Navigation principale">
      <a href="#fonctionnalites" class="hover:text-moutarde transition-colors">Fonctionnalités</a>
      <a href="#etapes" class="hover:text-moutarde transition-colors">Comment ça marche</a>
      <a href="#avis" class="hover:text-moutarde transition-colors">Avis</a>
      <a href="#footer" class="hover:text-moutarde transition-colors">FAQ</a>
    </nav>

    <div class="hidden md:flex items-center gap-3 font-display text-sm">
      <a href="<?php echo e(route('connexion')); ?>" class="px-4 py-2 hover:text-moutarde transition-colors">Connexion</a>
      <a href="<?php echo e(route('inscription')); ?>" class="stamp-btn px-4 py-2 rounded-md bg-moutarde text-ardoise font-semibold shadow-md">Créer un compte</a>
    </div>

    <button id="menu-btn" class="md:hidden p-2 -mr-2" aria-expanded="false" aria-controls="mobile-menu" aria-label="Ouvrir le menu">
      <svg id="icon-open" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
      <svg id="icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
  </div>

  <div id="mobile-menu" class="md:hidden hidden border-t border-kraft/15 bg-ardoise-dark">
    <nav class="flex flex-col px-5 py-3 font-display text-sm" aria-label="Navigation mobile">
      <a href="#fonctionnalites" class="py-2.5 border-b border-kraft/10">Fonctionnalités</a>
      <a href="#etapes" class="py-2.5 border-b border-kraft/10">Comment ça marche</a>
      <a href="#avis" class="py-2.5 border-b border-kraft/10">Avis</a>
      <a href="#footer" class="py-2.5 border-b border-kraft/10">FAQ</a>
      <a href="<?php echo e(route('connexion')); ?>" class="py-2.5 border-b border-kraft/10">Connexion</a>
      <a href="<?php echo e(route('inscription')); ?>" class="mt-3 text-center px-4 py-2.5 rounded-md bg-moutarde text-ardoise font-semibold">Créer un compte</a>
    </nav>
  </div>
</header>

<main id="contenu">

  <!-- HERO : tableau d'affichage -->
  <section class="relative bg-ardoise text-kraft cork-texture overflow-hidden">
    <div class="max-w-6xl mx-auto px-5 sm:px-8 py-16 sm:py-24 grid lg:grid-cols-2 gap-14 items-center">

      <div>
        <p class="font-display text-xs uppercase tracking-[0.2em] text-moutarde mb-5">Réseau social — usage universitaire</p>
        <h1 class="font-display font-semibold text-4xl sm:text-5xl leading-[1.08] mb-6">
          Toute ta vie de campus,<br> épinglée au même endroit.
        </h1>
        <p class="text-kraft/80 text-lg leading-relaxed mb-8 max-w-md">
          USN rassemble tes cours, tes groupes de travail, tes clubs et les événements de ton campus — pour que tu arrêtes de chercher l'info à six endroits différents.
        </p>
        <div class="flex flex-wrap items-center gap-4">
          <a id="inscription" href="<?php echo e(route('inscription')); ?>" class="stamp-btn inline-flex items-center gap-2 px-6 py-3.5 rounded-md bg-moutarde text-ardoise font-display font-semibold shadow-lg">
            Rejoindre mon campus
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
          </a>
          <a href="#etapes" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-md border border-kraft/30 font-display font-medium hover:bg-kraft/10 transition-colors">
            Voir comment ça marche
          </a>
        </div>
        <p class="mt-7 text-sm text-kraft/55">Déjà actif sur 40+ campus francophones · gratuit pour les étudiants</p>
      </div>

      <!-- pinboard -->
      <div class="relative h-[460px] sm:h-[500px]" aria-hidden="false" role="img" aria-label="Aperçu du fil USN : annonce d'événement, recherche de binôme, club étudiant et objet perdu, présentés comme des notes épinglées sur un tableau d'affichage.">

        <div class="lift note-card absolute left-2 top-3 w-60 sm:w-64 bg-kraft-light text-tinta rounded-sm rotate-[-4deg] p-4">
          <span class="pin"></span>
          <p class="font-display text-[11px] uppercase tracking-wide text-encre font-semibold mb-1">📌 Événement</p>
          <p class="font-display font-semibold text-sm mb-1">Gala de rentrée — Fac de Lettres</p>
          <p class="text-xs text-tinta/70 leading-snug">Vendredi 20h, Salle Polyvalente. Billets en ligne, places limitées.</p>
          <p class="mt-2 text-[11px] text-tinta/45 font-display">BDE Lettres · il y a 2h</p>
        </div>

        <div class="lift note-card absolute right-1 top-0 w-56 sm:w-60 bg-sauge text-kraft-light rounded-sm rotate-[3deg] p-4">
          <span class="pin"></span>
          <p class="font-display text-[11px] uppercase tracking-wide text-kraft/70 font-semibold mb-1">👥 Groupe de travail</p>
          <p class="font-display font-semibold text-sm mb-1">Binôme pour le TP d'algo ?</p>
          <p class="text-xs text-kraft-light/85 leading-snug">L2 Info, rendu jeudi. On se cale 2h à la BU demain ?</p>
          <p class="mt-2 text-[11px] text-kraft-light/55 font-display">Léa M. · L2 Informatique</p>
        </div>

        <div class="lift note-card absolute left-6 bottom-6 w-60 sm:w-64 bg-kraft-light text-tinta rounded-sm rotate-[2deg] p-4">
          <span class="pin"></span>
          <p class="font-display text-[11px] uppercase tracking-wide text-moutarde-dark font-semibold mb-1">🎭 Club étudiant</p>
          <p class="font-display font-semibold text-sm mb-1">Le club théâtre recrute</p>
          <p class="text-xs text-tinta/70 leading-snug">Aucune expérience requise. Auditions ouvertes toute la semaine.</p>
          <p class="mt-2 text-[11px] text-tinta/45 font-display">Club Théâtre · 12 membres actifs</p>
        </div>

        <div class="lift note-card absolute right-3 bottom-2 w-52 sm:w-56 bg-moutarde text-ardoise rounded-sm rotate-[-2deg] p-4">
          <span class="pin"></span>
          <p class="font-display text-[11px] uppercase tracking-wide text-ardoise/70 font-semibold mb-1">🔑 Objet trouvé</p>
          <p class="font-hand text-lg leading-tight">Trousseau de clés trouvé devant l'amphi B !</p>
          <p class="mt-2 text-[11px] text-ardoise/60 font-display">Posté dans #campus-nord</p>
        </div>

      </div>
    </div>
  </section>

  <!-- STATS : relevé -->
  <section class="bg-kraft-light border-b border-tinta/10">
    <div class="max-w-6xl mx-auto px-5 sm:px-8 py-10 sm:py-12">
      <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-dashed divide-tinta/25 text-center">
        <div class="px-3 fade-up">
          <p class="font-display font-bold text-3xl sm:text-4xl text-ardoise">42</p>
          <p class="text-xs sm:text-sm uppercase tracking-wide text-tinta/60 mt-1">campus connectés</p>
        </div>
        <div class="px-3 fade-up">
          <p class="font-display font-bold text-3xl sm:text-4xl text-ardoise">96k</p>
          <p class="text-xs sm:text-sm uppercase tracking-wide text-tinta/60 mt-1">étudiants actifs</p>
        </div>
        <div class="px-3 fade-up">
          <p class="font-display font-bold text-3xl sm:text-4xl text-ardoise">3 800</p>
          <p class="text-xs sm:text-sm uppercase tracking-wide text-tinta/60 mt-1">groupes de travail</p>
        </div>
        <div class="px-3 fade-up">
          <p class="font-display font-bold text-3xl sm:text-4xl text-ardoise">610</p>
          <p class="text-xs sm:text-sm uppercase tracking-wide text-tinta/60 mt-1">événements / semaine</p>
        </div>
      </div>
    </div>
  </section>

        <ul class="space-y-2 text-sm">
          <li><a href="#" class="hover:text-moutarde transition-colors">Ajouter mon université</a></li>
          <li><a href="#" class="hover:text-moutarde transition-colors">Pour les associations</a></li>
          <li><a href="#" class="hover:text-moutarde transition-colors">Pour les administrations</a></li>
          <li><a href="#" class="hover:text-moutarde transition-colors">Centre d'aide</a></li>
        </ul>
      </div>

      <div>
        <p class="font-display text-xs uppercase tracking-wide text-kraft/45 mb-3">Légal</p>
        <ul class="space-y-2 text-sm">
          <li><a href="#" class="hover:text-moutarde transition-colors">Conditions d'utilisation</a></li>
          <li><a href="#" class="hover:text-moutarde transition-colors">Confidentialité</a></li>
          <li><a href="#" class="hover:text-moutarde transition-colors">Cookies</a></li>
          <li><a href="#" class="hover:text-moutarde transition-colors">Contact</a></li>
        </ul>
      </div>

    </div>

    <div class="pt-6 border-t border-kraft/10 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-kraft/45">
      <p>© 2026 USN. Conçu pour les étudiants, pas pour les annonceurs.</p>
      <p class="font-hand text-base text-kraft/55">fait avec 💛 entre deux révisions</p>
    </div>
  </div>
</footer>

<script>
  // mobile menu
  const menuBtn = document.getElementById('menu-btn');
  const mobileMenu = document.getElementById('mobile-menu');
  const iconOpen = document.getElementById('icon-open');
  const iconClose = document.getElementById('icon-close');

  menuBtn.addEventListener('click', () => {
    const isHidden = mobileMenu.classList.contains('hidden');
    mobileMenu.classList.toggle('hidden');
    iconOpen.classList.toggle('hidden');
    iconClose.classList.toggle('hidden');
    menuBtn.setAttribute('aria-expanded', String(isHidden));
  });

  mobileMenu.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      mobileMenu.classList.add('hidden');
      iconOpen.classList.remove('hidden');
      iconClose.classList.add('hidden');
      menuBtn.setAttribute('aria-expanded', 'false');
    });
  });

  // fade-up on scroll
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const fadeEls = document.querySelectorAll('.fade-up');

  if (prefersReduced) {
    fadeEls.forEach(el => el.classList.add('is-visible'));
  } else if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });
    fadeEls.forEach(el => observer.observe(el));
  } else {
    fadeEls.forEach(el => el.classList.add('is-visible'));
  }
</script>

</body>
</html><?php /**PATH C:\Users\Poolski\Desktop\DEV Project\USN.ci\resources\views/welcome.blade.php ENDPATH**/ ?>