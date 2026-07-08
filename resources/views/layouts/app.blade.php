{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'USN')</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
  <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Caveat:wght@500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
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
            hand:    ['"Caveat"', 'cursive'],
          },
        }
      }
    }
  </script>
  <style>
    * { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body { background-color: #E8E0CE; color: #221E18; }
    .cover-zone {
      height: 220px;
      background-color: #1F2E26;
      position: relative;
      overflow: hidden;
    }
    .cover-zone img.cover-img {
      width: 100%; height: 100%; object-fit: cover; opacity: .85;
    }
    .cover-pattern {
      position: absolute; inset: 0;
      background-image: radial-gradient(rgba(239,230,211,.07) 1px, transparent 1.4px);
      background-size: 16px 16px;
    }
    .avatar-ring {
      width: 108px; height: 108px;
      border-radius: 50%;
      border: 4px solid #F8F2E6;
      overflow: hidden;
      flex-shrink: 0;
      background: #2B3F33;
      display: flex; align-items: center; justify-content: center;
      font-size: 38px; font-weight: 700;
      color: #E2A33B;
      box-shadow: 0 3px 10px rgba(0,0,0,.25);
      margin-top: -54px;
      position: relative; z-index: 10;
    }
    .avatar-ring img { width: 100%; height: 100%; object-fit: cover; }
    .stat-band {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      border: 1px solid #D4CABC;
      border-radius: 10px;
      overflow: hidden;
      background: #F8F2E6;
    }
    .stat-item {
      padding: 10px 6px;
      text-align: center;
      border-right: 1px solid #D4CABC;
    }
    .stat-item:last-child { border-right: none; }
    .bar-fill {
      height: 6px;
      background: #E2A33B;
      border-radius: 99px;
      transition: width .6s ease;
    }
    .tab-btn {
      padding: 10px 16px;
      font-size: .8125rem;
      font-weight: 500;
      color: #6B7280;
      border-bottom: 2px solid transparent;
      cursor: pointer;
      white-space: nowrap;
      transition: color .15s, border-color .15s;
      background: none; border: none;
    }
    .tab-btn:hover { color: #1F2E26; }
    .tab-btn.active { color: #1F2E26; border-bottom-color: #E2A33B; }
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }
    .skill-bar-bg { flex: 1; height: 5px; background: #D4CABC; border-radius: 99px; overflow: hidden; }
    .skill-bar-fill { height: 100%; background: #7A8C6B; border-radius: 99px; }
    .post-card { background: #F8F2E6; border: 1px solid #D4CABC; border-radius: 12px; margin-bottom: 14px; overflow: hidden; }
    .post-action-btn {
      flex: 1; padding: 9px;
      display: flex; align-items: center; justify-content: center; gap: 6px;
      font-size: .8125rem; color: #6B7280;
      cursor: pointer; transition: background .15s;
      background: none; border: none;
      font-family: 'Space Grotesk', sans-serif;
    }
    .post-action-btn:hover { background: rgba(31,46,38,.06); color: #1F2E26; }
    .post-action-btn.liked { color: #B8442E; }
    .pin-card {
      background: #2B3F33;
      border-radius: 8px;
      padding: 12px 14px;
      margin-bottom: 8px;
      position: relative;
    }
    .pin-dot {
      width: 10px; height: 10px; border-radius: 50%;
      background: #B8442E;
      position: absolute; top: -5px; left: 50%; transform: translateX(-50%);
      box-shadow: 0 1px 3px rgba(0,0,0,.4);
    }
    .tl-dot { width: 8px; height: 8px; border-radius: 50%; background: #E2A33B; margin-top: 5px; flex-shrink: 0; }
    .friend-avatar {
      width: 100%; aspect-ratio: 1;
      border-radius: 8px;
      background: #2B3F33;
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; font-weight: 600;
      color: #E2A33B;
      overflow: hidden;
    }
    .friend-avatar img { width: 100%; height: 100%; object-fit: cover; }
    @media (prefers-reduced-motion: reduce) { *, html { animation: none !important; transition: none !important; scroll-behavior: auto !important; } }
    .fade-in { animation: fadeIn .4s ease both; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    @media (max-width: 768px) {
      .stat-band { grid-template-columns: repeat(3, 1fr); }
      .stat-item:nth-child(3) { border-right: none; }
      .stat-item:nth-child(4), .stat-item:nth-child(5) { border-top: 1px solid #D4CABC; }
      .desktop-only { display: none !important; }
    }
    @media (max-width: 640px) {
      .avatar-ring { width: 84px; height: 84px; font-size: 28px; margin-top: -42px; }
      .cover-zone { height: 160px; }
    }
  </style>
  @stack('head')
</head>
<body class="font-display antialiased">
  @include('partials.navbar')
  <main class="max-w-5xl mx-auto px-4 sm:px-6 pb-10">
    @yield('content')
  </main>
  @stack('scripts')
</body>
</html>
