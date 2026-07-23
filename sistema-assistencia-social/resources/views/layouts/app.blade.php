<!DOCTYPE html>
<html lang="pt-BR" class="{{ session('theme', 'dark') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistema de Acolhimento</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/icon-acolhimento.png') }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Collapsible Sidebar Styles */
        .sidebar-transition {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-collapsed {
            width: 5rem !important; /* w-20 */
        }

        .sidebar-collapsed .brand-container {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
            justify-content: center !important;
        }

        .sidebar-collapsed .brand-text {
            display: none !important;
        }

        .sidebar-collapsed nav {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }

        .sidebar-collapsed nav a,
        .sidebar-collapsed #desktop-sidebar-toggle,
        .sidebar-collapsed .logout-btn {
            justify-content: center !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            width: 3.5rem !important;
            height: 3rem !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        .sidebar-collapsed nav a span,
        .sidebar-collapsed .logout-btn span {
            display: none !important;
        }

        .sidebar-collapsed nav a svg,
        .sidebar-collapsed #desktop-sidebar-toggle svg,
        .sidebar-collapsed .logout-btn svg {
            margin: 0 !important;
            width: 1.25rem !important;
            height: 1.25rem !important;
        }

        .sidebar-collapsed .logout-container {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 h-screen overflow-hidden transition-colors duration-300 flex flex-col font-sans">

    <!-- Header (Horizontal Bar covering full width of site) -->
    <header class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-850 h-16 flex items-center justify-between px-6 transition-all duration-300 z-10 shrink-0 shadow-sm shadow-slate-100/50 dark:shadow-none">
        <!-- Logo and Brand -->
        <div class="flex items-center space-x-3">
            <!-- Mobile Toggle -->
            <button id="mobile-menu-toggle" class="md:hidden text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all duration-200">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <a href="{{ route('acolhimentos.index') }}" class="flex items-center" title="Voltar para Acolhimentos">
                <img src="{{ asset('images/icon-acolhimento.png') }}" alt="Acolhimento" class="h-10 w-auto object-contain hover:scale-105 transition-transform duration-300">
            </a>
        </div>

        <div class="hidden md:block">
            <h1 class="text-sm font-semibold text-slate-500 dark:text-slate-400">
                @yield('subtitle', 'Painel Administrativo')
            </h1>
        </div>

        <!-- Actions / User Info -->
        <div class="flex items-center space-x-4">
            <!-- User Badge -->
            <div class="flex items-center space-x-3 pl-3 border-l border-slate-200 dark:border-slate-800">
                <div class="text-right hidden sm:block">
                    <div class="text-sm font-bold text-slate-800 dark:text-slate-100 leading-tight">
                        {{ Auth::user()->nome_usu ?? Auth::user()->login }}
                    </div>
                    <div class="text-[9px] uppercase font-extrabold tracking-widest mt-0.5 {{ Auth::user()->isDiretor() || Auth::user()->isAdmin() ? 'text-emerald-600 dark:text-emerald-450' : 'text-slate-450 dark:text-slate-400' }}">
                        @if (Auth::user()->isDiretor())
                            Diretor
                        @elseif (Auth::user()->isAdmin())
                            Administrador
                        @else
                            Usuário
                        @endif
                    </div>
                </div>
                <div class="h-9 w-9 rounded-full bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white shadow-sm border border-emerald-600/20 flex items-center justify-center font-bold text-xs transition-all duration-300">
                    {{ strtoupper(substr(Auth::user()->nome_usu ?? Auth::user()->login, 0, 2)) }}
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container under Header -->
    <div class="flex-1 flex min-h-0">
        <!-- Sidebar (Desktop) -->
        <aside id="desktop-sidebar" class="w-64 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 border-r border-slate-200/80 dark:border-slate-900 flex-col hidden md:flex sidebar-transition transition-colors duration-300">
            <script>
                if (localStorage.getItem('sidebar-collapsed') === 'true') {
                    document.getElementById('desktop-sidebar').classList.add('sidebar-collapsed');
                }
            </script>
            <!-- Toggle Sidebar Container -->
            <div class="p-4 border-b border-slate-200/60 dark:border-slate-900 brand-container shrink-0">
                <button id="desktop-sidebar-toggle" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-all duration-250 cursor-pointer text-left focus:outline-none" title="Alternar Barra Lateral">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <span class="text-xs font-bold uppercase tracking-wider brand-text text-slate-500 dark:text-slate-400">Recolher Menu</span>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto">
                <a href="{{ route('acolhimentos.index') }}" title="Acolhimentos"
                   class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-250 hover:translate-x-1 {{ Route::is('acolhimentos.index') || Route::is('dashboard') ? 'bg-emerald-600 text-white font-semibold shadow-sm hover:bg-emerald-700' : 'text-slate-600 dark:text-slate-355 hover:bg-slate-50 dark:hover:bg-slate-900/50 hover:text-slate-900 dark:hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span>Acolhimentos</span>
                </a>

                @can('edit-data')
                <a href="{{ route('acolhimentos.create') }}" title="Novo Cadastro"
                   class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-250 hover:translate-x-1 {{ Route::is('acolhimentos.create') ? 'bg-emerald-600 text-white font-semibold shadow-sm hover:bg-emerald-700' : 'text-slate-600 dark:text-slate-355 hover:bg-slate-50 dark:hover:bg-slate-900/50 hover:text-slate-900 dark:hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Novo Cadastro</span>
                </a>
                @endcan

                @can('manage-users')
                <a href="{{ route('usuarios.index') }}" title="Usuários"
                   class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-250 hover:translate-x-1 {{ Route::is('usuarios.index') ? 'bg-emerald-600 text-white font-semibold shadow-sm hover:bg-emerald-700' : 'text-slate-600 dark:text-slate-355 hover:bg-slate-55 dark:hover:bg-slate-900/50 hover:text-slate-900 dark:hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Usuários</span>
                </a>
                @endcan

                <a href="{{ route('configuracoes') }}" title="Configurações"
                   class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-250 hover:translate-x-1 {{ Route::is('configuracoes') ? 'bg-emerald-600 text-white font-semibold shadow-sm hover:bg-emerald-700' : 'text-slate-600 dark:text-slate-355 hover:bg-slate-50 dark:hover:bg-slate-900/50 hover:text-slate-900 dark:hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Configurações</span>
                </a>
            </nav>

            <!-- Sidebar Footer / Logout -->
            <div class="p-4 border-t border-slate-200/80 dark:border-slate-900 logout-container shrink-0">
                <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Tem certeza que deseja sair?');">
                    @csrf
                    <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-red-650 dark:text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-all duration-250 cursor-pointer logout-btn" title="Sair">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Sair</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 min-h-0">
            <!-- Main Content -->
            <main class="flex-1 p-6 overflow-y-auto flex flex-col">
                @if (session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/20 border-l-4 border-emerald-500 rounded text-emerald-700 dark:text-emerald-400 text-sm flex items-center justify-between shadow-sm">
                        <div class="flex items-center space-x-2">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 dark:bg-red-950/20 border-l-4 border-red-500 rounded text-red-700 dark:text-red-400 text-sm flex items-center justify-between shadow-sm">
                        <div class="flex items-center space-x-2">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Drawer Navigation -->
    <div id="mobile-sidebar" class="fixed inset-0 z-50 hidden">
        <!-- Backdrop -->
        <div id="mobile-sidebar-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300"></div>

        <!-- Sidebar Content -->
        <aside class="relative flex flex-col w-full max-w-xs bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 h-full p-6 shadow-2xl transition-transform duration-300 transform -translate-x-full">
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-200 dark:border-slate-900">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/icon-acolhimento.png') }}" alt="Acolhimento" class="h-10 w-auto">
                </div>
                <button id="mobile-sidebar-close" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 p-1 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-xl transition-all duration-200">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="flex-1 space-y-1.5">
                <a href="{{ route('acolhimentos.index') }}"
                   class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-colors duration-250 {{ Route::is('acolhimentos.index') || Route::is('dashboard') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-slate-600 dark:text-slate-350 hover:bg-slate-55 dark:hover:bg-slate-900/50' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span>Acolhimentos</span>
                </a>

                @can('edit-data')
                <a href="{{ route('acolhimentos.create') }}"
                   class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-colors duration-250 {{ Route::is('acolhimentos.create') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-slate-600 dark:text-slate-355 hover:bg-slate-55 dark:hover:bg-slate-900/50' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Novo Cadastro</span>
                </a>
                @endcan

                @can('manage-users')
                <a href="{{ route('usuarios.index') }}"
                   class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-colors duration-250 {{ Route::is('usuarios.index') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-slate-600 dark:text-slate-355 hover:bg-slate-55 dark:hover:bg-slate-900/50' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Usuários</span>
                </a>
                @endcan

                <a href="{{ route('configuracoes') }}"
                   class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-colors duration-250 {{ Route::is('configuracoes') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-slate-600 dark:text-slate-355 hover:bg-slate-55 dark:hover:bg-slate-900/50' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Configurações</span>
                </a>
            </nav>

            <div class="mt-auto pt-6 border-t border-slate-200 dark:border-slate-900">
                <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Tem certeza que deseja sair?');">
                    @csrf
                    <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-red-650 dark:text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors duration-250 cursor-pointer">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Sair</span>
                    </button>
                </form>
            </div>
        </aside>
    </div>

    <!-- Theme toggling & mobile menu JS -->
    <script>
        // Toggle Mobile Menu
        const mobileToggle = document.getElementById('mobile-menu-toggle');
        const mobileSidebar = document.getElementById('mobile-sidebar');
        const mobileSidebarBackdrop = document.getElementById('mobile-sidebar-backdrop');
        const mobileSidebarClose = document.getElementById('mobile-sidebar-close');
        const sidebarAside = mobileSidebar ? mobileSidebar.querySelector('aside') : null;

        if (mobileToggle && mobileSidebar) {
            mobileToggle.addEventListener('click', () => {
                mobileSidebar.classList.remove('hidden');
                setTimeout(() => {
                    sidebarAside.classList.remove('-translate-x-full');
                }, 50);
            });

            const closeSidebar = () => {
                sidebarAside.classList.add('-translate-x-full');
                setTimeout(() => {
                    mobileSidebar.classList.add('hidden');
                }, 300);
            };

            mobileSidebarBackdrop.addEventListener('click', closeSidebar);
            mobileSidebarClose.addEventListener('click', closeSidebar);
        }

        // Toggle Desktop Sidebar
        const desktopToggle = document.getElementById('desktop-sidebar-toggle');
        const desktopSidebar = document.getElementById('desktop-sidebar');

        if (desktopToggle && desktopSidebar) {
            desktopToggle.addEventListener('click', () => {
                const isCollapsed = desktopSidebar.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebar-collapsed', isCollapsed ? 'true' : 'false');
            });
        }

    </script>
</body>
</html>
