<!DOCTYPE html>
<html lang="pt-BR" class="{{ session('theme', 'dark') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Acolhimento</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/icon-acolhimento.png') }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 dark:bg-slate-950 min-h-screen flex transition-colors duration-300 font-sans">

    <div class="w-full min-h-screen flex overflow-hidden">

        <!-- Left Side: Brand/Marketing (Hidden on Mobile) -->
        <div class="hidden lg:flex lg:w-3/5 bg-linear-to-tr from-[#0b1329] via-[#0f2042] to-[#1e3a8a] text-white p-16 flex-col justify-between relative overflow-hidden">
            <!-- Decorative abstract background glow elements -->
            <div class="absolute -top-40 -left-40 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -right-40 w-120 h-120 bg-teal-500/10 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/3 -translate-y-1/2 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl"></div>

            <!-- Middle Headline Content -->
            <div class="my-auto max-w-lg z-10 space-y-6">
                <h1 class="text-5xl font-black tracking-tight leading-[1.1] bg-linear-to-r from-white via-white to-slate-300 bg-clip-text text-transparent">
                    Acolhimento
                </h1>
                <p class="text-lg text-slate-300 leading-relaxed font-light">
                    Gestão de acolhimentos, cadastros, atendimentos e assistência social para pessoas em situação de rua, promovendo dignidade e reintegração.
                </p>
            </div>

            <!-- Footer information -->
            <div class="z-10 flex items-center justify-center text-xs text-slate-450 border-t border-white/10 pt-6">
                <span>© {{ date('Y') }} &bull; Acolhimento</span>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full lg:w-2/5 flex items-center justify-center p-6 bg-slate-50 dark:bg-slate-950 relative">
            <div class="w-full max-w-md space-y-8 animate-fade-in-up">
                <!-- Header -->
                <div class="space-y-3">
                    <img src="{{ asset('images/icon-acolhimento.png') }}" alt="Logo Acolhimento" class="mx-auto h-32 w-auto object-contain mb-4">
                    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                        Bem-vindo(a) ao Sistema de Acolhimento!
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-450">
                        Insira suas credenciais abaixo para acessar o sistema.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="p-4 bg-red-500/10 dark:bg-red-500/5 border border-red-500/20 rounded-2xl text-red-700 dark:text-red-400 text-sm">
                        <ul class="list-disc pl-5 space-y-1 font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Login Input -->
                    <div class="space-y-2">
                        <label for="login" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Usuário / Login</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </span>
                            <input type="text" name="login" id="login" required value="{{ old('login') }}"
                                class="w-full pl-11 pr-4 py-3.5 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 border border-slate-250 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-transparent transition-all duration-200 shadow-sm"
                                placeholder="Digite seu usuário">
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="space-y-2">
                        <label for="senha" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Senha</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </span>
                            <input type="password" name="senha" id="senha" required
                                class="w-full pl-11 pr-4 py-3.5 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 border border-slate-250 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-transparent transition-all duration-200 shadow-sm"
                                placeholder="Digite sua senha">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 active:scale-[0.99] text-white font-bold rounded-xl shadow-sm transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 text-sm">
                        Acessar Sistema
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>

