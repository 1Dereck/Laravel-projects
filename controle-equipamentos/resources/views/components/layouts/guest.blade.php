<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full preload">
<head>
    <!-- Instant Theme Detection Script & Anti-FOUC Transition Lock -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'system';
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDark = savedTheme === 'dark' || (savedTheme === 'system' && systemPrefersDark);
            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
        
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.documentElement.classList.remove('preload');
            }, 100);
        });
    </script>
    <style>
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
    <title>{{ $title ?? 'Inventário TI' }} — Gestão de Equipamentos</title>

    <!-- Ícone da Guia do Navegador -->
    <link rel="icon" type="image/png" href="{{ asset('img/icon-inventario.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full bg-slate-100 text-slate-900 dark:bg-slate-950 dark:text-slate-100 antialiased selection:bg-emerald-500 selection:text-white flex items-center justify-center p-4"
      x-data="{ 
          theme: localStorage.getItem('theme') || 'system',
          setTheme(mode) {
              this.theme = mode;
              localStorage.setItem('theme', mode);
              this.applyTheme();
          },
          applyTheme() {
              const isDark = this.theme === 'dark' || (this.theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
              if (isDark) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
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
</body>
</html>
