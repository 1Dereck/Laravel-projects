<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full preload bg-white dark:bg-slate-950">
<head>
    <!-- Instant Theme & Sidebar Detection Script & Anti-FOUC Transition Lock -->
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

            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                document.documentElement.classList.add('sidebar-collapsed');
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

        /* Previne o efeito flash da sidebar colapsada no desktop antes do Alpine inicializar */
        @media (min-width: 768px) {
            html.sidebar-collapsed aside {
                width: 5rem !important;
            }
            html.sidebar-collapsed .sidebar-text {
                display: none !important;
            }
        }
    </style>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'TI Hospitalar' }} — Hospitais Asclépio</title>
    <link rel="icon" type="image/png" href="{{ asset('img/icon-hospitais-asclepio.png') }}?v=2">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- HTML5 QRCode, Tesseract.js OCR & Cropper.js CDN -->
    <script src="https://unpkg.com/html5-qrcode" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js" defer></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxAppearance
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="h-full bg-white text-slate-900 dark:bg-slate-950 dark:text-slate-100 antialiased selection:bg-emerald-500 selection:text-white"
      x-data="{
          sidebarOpen: false,
          sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
          theme: localStorage.getItem('theme') || 'system',
          toggleSidebar() {
              this.sidebarCollapsed = !this.sidebarCollapsed;
              localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
              if (this.sidebarCollapsed) {
                  document.documentElement.classList.add('sidebar-collapsed');
              } else {
                  document.documentElement.classList.remove('sidebar-collapsed');
              }
          },
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
              if (this.sidebarCollapsed) {
                  document.documentElement.classList.add('sidebar-collapsed');
              } else {
                  document.documentElement.classList.remove('sidebar-collapsed');
              }
              window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                  if (this.theme === 'system') {
                      this.applyTheme();
                  }
              });
          }
      }">
    <div class="min-h-screen flex flex-col md:flex-row">

        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-sm md:hidden"
             style="display: none;"></div>

        <!-- Sidebar (Fixo e estático na tela durante a rolagem) -->
        <aside wire:ignore.self
               :class="{
                   'translate-x-0': sidebarOpen,
                   'md:w-20': sidebarCollapsed,
                   'md:w-72': !sidebarCollapsed
               }"
               class="-translate-x-full fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col justify-between transition-[width,transform] duration-300 ease-in-out md:sticky md:top-0 md:h-screen md:shrink-0 md:translate-x-0 overflow-y-auto">
            <div>
                <!-- Brand Header -->
                <div class="h-20 px-4 flex items-center justify-between border-b border-slate-200/80 dark:border-slate-800/80">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden">
                        <img src="{{ asset('img/icon-hospitais-asclepio.png') }}" class="w-10 h-10 shrink-0 object-contain rounded-xl" alt="Logo Hospitais Asclépio">
                        <div :class="sidebarCollapsed ? 'md:hidden' : ''" class="transition-opacity duration-200 whitespace-nowrap sidebar-text">
                            <span class="block font-black text-slate-900 dark:text-slate-100 text-base leading-tight tracking-tight">HOSPITAIS ASCLÉPIO</span>
                            <span class="block text-[10px] font-bold text-emerald-600 dark:text-emerald-400 tracking-wider uppercase">TI & Parque Tecnológico</span>
                        </div>
                    </a>

                    <!-- Mobile Close Button -->
                    <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="p-3 space-y-1.5">
                    <!-- Desktop Sidebar Toggle Button (Exclusivo para PC e posicionado acima do Dashboard) -->
                    <div class="hidden md:block pb-1">
                        <button @click="toggleSidebar()"
                                :class="sidebarCollapsed ? 'md:justify-center md:px-0' : 'px-4 justify-between'"
                                class="w-full flex items-center gap-3 py-2.5 rounded-xl text-xs font-bold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100 bg-slate-100/80 hover:bg-slate-200/80 dark:bg-slate-800/50 dark:hover:bg-slate-800 border border-slate-200/60 dark:border-slate-800 transition-all duration-200 cursor-pointer shadow-xs group"
                                :title="sidebarCollapsed ? 'Expandir Menu' : 'Recolher Menu'">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 shrink-0 text-slate-500 dark:text-slate-400 group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                <span :class="sidebarCollapsed ? 'md:hidden' : ''" class="whitespace-nowrap uppercase tracking-wider text-[11px] sidebar-text">Recolher Menu</span>
                            </div>
                            <svg :class="sidebarCollapsed ? 'md:hidden' : ''" class="w-4 h-4 shrink-0 text-slate-400 group-hover:-translate-x-0.5 transition-transform sidebar-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Dashboard Link -->
                    <a href="{{ route('dashboard') }}"
                       :class="sidebarCollapsed ? 'md:justify-center md:px-0 px-4' : 'px-4'"
                       class="flex items-center gap-3 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('dashboard', 'home') ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}"
                       :title="sidebarCollapsed ? 'Dashboard' : ''">
                        <flux:icon name="squares-2x2" class="w-5 h-5 shrink-0" />
                        <span :class="sidebarCollapsed ? 'md:hidden' : ''" class="whitespace-nowrap sidebar-text">Dashboard</span>
                    </a>

                    @if(auth()->user()?->isDiretor() || auth()->user()?->isAdmin())
                    <a href="{{ route('setores.index') }}"
                       :class="sidebarCollapsed ? 'md:justify-center md:px-0 px-4' : 'px-4'"
                       class="flex items-center gap-3 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('setores.*') ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}"
                       :title="sidebarCollapsed ? 'Setores & Secretarias' : ''">
                        <flux:icon name="building-office-2" class="w-5 h-5 shrink-0" />
                        <span :class="sidebarCollapsed ? 'md:hidden' : ''" class="whitespace-nowrap sidebar-text">Setores & Secretarias</span>
                    </a>
                    @endif

                    <a href="{{ route('equipamentos.index') }}"
                       :class="sidebarCollapsed ? 'md:justify-center md:px-0 px-4' : 'px-4'"
                       class="flex items-center gap-3 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('equipamentos.*') ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}"
                       :title="sidebarCollapsed ? 'Equipamentos' : ''">
                        <flux:icon name="computer-desktop" class="w-5 h-5 shrink-0" />
                        <span :class="sidebarCollapsed ? 'md:hidden' : ''" class="whitespace-nowrap sidebar-text">Equipamentos</span>
                    </a>

                    <a href="{{ route('perifericos.index') }}"
                       :class="sidebarCollapsed ? 'md:justify-center md:px-0 px-4' : 'px-4'"
                       class="flex items-center gap-3 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('perifericos.*') ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}"
                       :title="sidebarCollapsed ? 'Periféricos Avulsos' : ''">
                        <flux:icon name="printer" class="w-5 h-5 shrink-0" />
                        <span :class="sidebarCollapsed ? 'md:hidden' : ''" class="whitespace-nowrap sidebar-text">Periféricos Avulsos</span>
                    </a>

                    <a href="{{ route('relatorios.index') }}"
                       :class="sidebarCollapsed ? 'md:justify-center md:px-0 px-4' : 'px-4'"
                       class="flex items-center gap-3 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('relatorios.*') ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}"
                       :title="sidebarCollapsed ? 'Busca & Relatórios PDF' : ''">
                        <flux:icon name="document-text" class="w-5 h-5 shrink-0" />
                        <span :class="sidebarCollapsed ? 'md:hidden' : ''" class="whitespace-nowrap sidebar-text">Busca & Relatórios PDF</span>
                    </a>

                    <a href="{{ route('quantidades.index') }}"
                       :class="sidebarCollapsed ? 'md:justify-center md:px-0 px-4' : 'px-4'"
                       class="flex items-center gap-3 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('quantidades.*') ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}"
                       :title="sidebarCollapsed ? 'Levantamento de Quantidades' : ''">
                        <flux:icon name="chart-bar" class="w-5 h-5 shrink-0" />
                        <span :class="sidebarCollapsed ? 'md:hidden' : ''" class="whitespace-nowrap sidebar-text">Levantamento de Quantidades</span>
                    </a>

                    @if(auth()->check() && (auth()->user()->isDiretor() || auth()->user()->isAdmin() || auth()->user()->isCoordenador()))
                        <div :class="sidebarCollapsed ? 'md:hidden' : ''" class="pt-4 pb-1 sidebar-text">
                            <span class="px-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Administrativo</span>
                        </div>
                        <div x-show="sidebarCollapsed" class="hidden md:block pt-3 pb-1 border-t border-slate-200 dark:border-slate-800 my-1"></div>

                        <!-- Gestão de Usuários -->
                        <a href="{{ route('usuarios.index') }}"
                           :class="sidebarCollapsed ? 'md:justify-center md:px-0 px-4' : 'px-4'"
                           class="flex items-center gap-3 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('usuarios.*') ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}"
                           :title="sidebarCollapsed ? 'Gestão de Usuários' : ''">
                            <flux:icon name="users" class="w-5 h-5 shrink-0" />
                            <span :class="sidebarCollapsed ? 'md:hidden' : ''" class="whitespace-nowrap sidebar-text">Gestão de Usuários</span>
                        </a>

                        @if(auth()->user()->isDiretor())
                        <!-- Lixeira Segura (EXCLUSIVO DIRETORES) -->
                        <a href="{{ route('lixeira.index') }}"
                           :class="sidebarCollapsed ? 'md:justify-center md:px-0 px-4' : 'px-4'"
                           class="flex items-center gap-3 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('lixeira.*') ? 'bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}"
                           :title="sidebarCollapsed ? 'Lixeira Segura' : ''">
                            <flux:icon name="trash" class="w-5 h-5 shrink-0" />
                            <span :class="sidebarCollapsed ? 'md:hidden' : ''" class="whitespace-nowrap sidebar-text">Lixeira Segura</span>
                        </a>
                        @endif
                    @endif

                    <div :class="sidebarCollapsed ? 'md:hidden' : ''" class="pt-4 pb-1 sidebar-text">
                        <span class="px-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Sistema</span>
                    </div>
                    <div x-show="sidebarCollapsed" class="hidden md:block pt-3 pb-1 border-t border-slate-200 dark:border-slate-800 my-1"></div>

                    <!-- Link para Página de Configurações na Sidebar -->
                    <a href="{{ route('configuracoes.index') }}"
                       :class="sidebarCollapsed ? 'md:justify-center md:px-0 px-4' : 'px-4'"
                       class="flex items-center gap-3 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('configuracoes.*') ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}"
                       :title="sidebarCollapsed ? 'Configurações' : ''">
                        <flux:icon name="cog-6-tooth" class="w-5 h-5 shrink-0" />
                        <span :class="sidebarCollapsed ? 'md:hidden' : ''" class="whitespace-nowrap sidebar-text">Configurações</span>
                    </a>
                </nav>
            </div>

            <!-- Footer Sidebar: Botão de Sair -->
            @auth
            <div class="p-3 border-t border-slate-200/80 dark:border-slate-800/80">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            :class="sidebarCollapsed ? 'md:justify-center md:px-0 px-4' : 'px-4'"
                            class="w-full flex items-center justify-center gap-2.5 py-3 rounded-xl text-sm font-semibold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 dark:text-red-400 dark:hover:text-red-300 dark:bg-red-500/10 dark:hover:bg-red-500/20 dark:border-red-500/20 transition-all duration-200 shadow-sm group cursor-pointer"
                            :title="sidebarCollapsed ? 'Encerrar Sessão' : ''">
                        <svg class="w-5 h-5 shrink-0 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span :class="sidebarCollapsed ? 'md:hidden' : ''" class="whitespace-nowrap sidebar-text">Encerrar Sessão</span>
                    </button>
                </form>
            </div>
            @endauth
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Navbar -->
            <header class="h-20 bg-white/80 dark:bg-slate-900/60 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 sticky top-0 z-30 px-4 sm:px-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="md:hidden text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 p-2 rounded-lg bg-slate-100 dark:bg-slate-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100 hidden sm:block">
                        {{ $header ?? 'Painel de Controle' }}
                    </h1>
                </div>

                <!-- User Menu Dropdown (Avatar com Primeira Letra, Nome, Configurações e Sair) -->
                @auth
                <div class="relative" x-data="{ userMenuOpen: false }">
                    <button @click="userMenuOpen = !userMenuOpen"
                            class="flex items-center gap-3 p-1.5 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-emerald-500/40 group"
                            :aria-expanded="userMenuOpen"
                            title="Menu do Usuário">
                        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-400 to-teal-600 shadow-md shadow-emerald-500/20 flex items-center justify-center text-white font-extrabold select-none group-hover:scale-105 transition-transform duration-200">
                            {{ auth()->user()->initials() }}
                        </div>
                        <div class="text-left hidden md:block">
                            <span class="block text-sm font-semibold text-slate-800 dark:text-slate-200 leading-tight">{{ auth()->user()->name }}</span>
                            <span class="block text-xs text-slate-500 dark:text-slate-400 font-medium">
                                @if(auth()->user()->isDiretor())
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">Diretor</span>
                                @elseif(auth()->user()->isCoordenador())
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20">Coordenador</span>
                                @elseif(auth()->user()->isUsuario())
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">Usuário</span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">Administrador</span>
                                @endif
                            </span>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-200 transition-transform duration-200" :class="{ 'rotate-180': userMenuOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="userMenuOpen"
                         @click.outside="userMenuOpen = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200/80 dark:border-slate-800 py-2 z-50 overflow-hidden"
                         style="display: none;">

                        <!-- Cabeçalho do Dropdown -->
                        <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-800">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Conectado como</p>
                            <p class="text-sm font-bold text-slate-900 dark:text-slate-100 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ auth()->user()->email }}</p>
                        </div>

                        <!-- Opções -->
                        <div class="p-1 space-y-0.5">
                            <!-- Configurações -->
                            <a href="{{ route('configuracoes.index') }}"
                               @click="userMenuOpen = false"
                               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/80 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                                <svg class="w-4 h-4 shrink-0 text-slate-400 group-hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Configurações
                            </a>

                            <!-- Sair / Encerrar Sessão -->
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors cursor-pointer text-left">
                                    <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Encerrar Sessão
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endauth
            </header>

            <!-- Main Page Content -->
            <main class="flex-1 p-4 sm:p-8 overflow-y-auto">
                <div class="max-w-7xl mx-auto w-full">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <x-barcode-scanner-modal />
    <x-ocr-scanner-modal />

    @livewireScripts
    @fluxScripts
</body>
</html>
