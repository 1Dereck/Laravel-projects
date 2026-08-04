<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full preload bg-white dark:bg-slate-950">
<head>
    <!-- Instant Theme Detection Script & Anti-FOUC Transition Lock -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'system';
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDark = savedTheme === 'dark' || (savedTheme === 'system' && systemPrefersDark);
            if (isDark) {
                document.documentElement.classList.add('dark');
                document.documentElement.style.backgroundColor = '#020617';
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.style.backgroundColor = '#ffffff';
            }
        })();
        
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.documentElement.classList.remove('preload');
            }, 100);
        });
    </script>
    <style>
        html {
            background-color: #ffffff;
        }
        html.dark {
            background-color: #020617;
        }
        html.preload *, html.preload *::before, html.preload *::after {
            -webkit-transition: none !important;
            -moz-transition: none !important;
            -ms-transition: none !important;
            -o-transition: none !important;
            transition: none !important;
        }
    </style>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'TI Hospitalar' }} — Hospitais Asclépio</title>
    <link rel="icon" type="image/png" href="{{ asset('img/icon-hospitais-asclepio.png') }}?v=2">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxAppearance
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full bg-white text-slate-900 dark:bg-slate-950 dark:text-slate-100 antialiased selection:bg-emerald-500 selection:text-white flex items-center justify-center p-4"
      x-data="{ 
          theme: localStorage.getItem('theme') || 'system',
          setTheme(mode) {
              this.theme = mode;
              localStorage.setItem('theme', mode);
              localStorage.setItem('flux.appearance', mode);
              this.applyTheme();
          },
          applyTheme() {
              const isDark = this.theme === 'dark' || (this.theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
              if (isDark) {
                  document.documentElement.classList.add('dark');
                  document.documentElement.style.backgroundColor = '#020617';
              } else {
                  document.documentElement.classList.remove('dark');
                  document.documentElement.style.backgroundColor = '#ffffff';
              }
              if (window.Flux && typeof window.Flux.applyAppearance === 'function') {
                  window.Flux.applyAppearance(this.theme);
              }
          },
          init() {
              this.applyTheme();
              window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                  if (this.theme === 'system') {
                      this.applyTheme();
                  }
              });
          }
      }">
    {{ $slot }}
    @livewireScripts
    @fluxScripts
</body>
</html>
