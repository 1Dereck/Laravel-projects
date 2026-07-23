@extends('layouts.app')

@section('title', 'Configurações')
@section('subtitle', 'Configurações do Sistema')

@section('content')
<div class="max-w-4xl w-full mx-auto flex-1 flex flex-col justify-between animate-fade-in-up">
    <div class="space-y-8 flex-1 flex flex-col justify-start">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-slate-200/80 dark:border-slate-800 pb-4">
            <div>
                <h2 class="text-3xl font-extrabold tracking-tight text-slate-850 dark:text-slate-100">Configurações</h2>
                <p class="text-sm text-slate-500 dark:text-slate-455 mt-1">Gerencie sua senha e preferências de tema do painel.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Card Tema -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800/80 flex flex-col justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2 flex items-center space-x-2">
                    <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m2.828-9.9a5 5 0 117.072 7.072l-7.072-7.072z" />
                    </svg>
                    <span>Preferência de Tema</span>
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-450 mb-6 font-medium">Escolha a sua aparência preferida para trabalhar no sistema.</p>
            </div>
            
            <button type="button" onclick="toggleAppTheme()" id="btn-toggle-theme"
                class="w-full px-4 py-3 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 active:scale-[0.98] text-white rounded-xl font-bold text-sm transition-all duration-200 cursor-pointer flex items-center justify-center space-x-2.5 shadow-md shadow-emerald-500/20">
                <span id="btn-theme-svg-wrapper" class="transition-transform duration-300">
                    @if(session('theme', 'dark') === 'dark')
                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    @else
                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m2.828-9.9a5 5 0 117.072 7.072l-7.072-7.072z" />
                        </svg>
                    @endif
                </span>
                <span id="btn-theme-text">{{ session('theme', 'dark') === 'dark' ? 'Tema Escuro' : 'Tema Claro' }}</span>
            </button>
        </div>

        <!-- Card Alterar Senha -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800/80">
            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-4 flex items-center space-x-2">
                <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span>Alterar Senha</span>
            </h3>
            
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 dark:bg-red-950/20 border-l-4 border-red-500 rounded text-red-700 dark:text-red-400 text-xs">
                    <ul class="list-disc pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('configuracoes.senha') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="senha_atual" class="block text-xs font-bold uppercase tracking-wider text-slate-555 dark:text-slate-400 mb-1.5">Senha Atual</label>
                    <input type="password" name="senha_atual" id="senha_atual" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 text-slate-850 dark:text-slate-100 border border-slate-250 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-450 focus:border-transparent text-sm transition-all duration-200">
                </div>
                <div>
                    <label for="nova_senha" class="block text-xs font-bold uppercase tracking-wider text-slate-555 dark:text-slate-400 mb-1.5">Nova Senha</label>
                    <input type="password" name="nova_senha" id="nova_senha" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 text-slate-850 dark:text-slate-100 border border-slate-250 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-450 focus:border-transparent text-sm transition-all duration-200">
                </div>
                <div>
                    <label for="nova_senha_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-555 dark:text-slate-400 mb-1.5">Confirmar Nova Senha</label>
                    <input type="password" name="nova_senha_confirmation" id="nova_senha_confirmation" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 text-slate-855 dark:text-slate-100 border border-slate-250 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-450 focus:border-transparent text-sm transition-all duration-200">
                </div>

                <button type="submit"
                    class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 active:scale-[0.98] text-white font-bold rounded-xl text-sm shadow-md shadow-emerald-500/20 transition-all duration-200 cursor-pointer">
                    Salvar Nova Senha
                </button>
            </form>
        </div>
    </div>
    </div>

    <!-- Rodapé de Versão -->
    <div class="pt-8 mt-12 border-t border-slate-200/80 dark:border-slate-800 flex items-center justify-center shrink-0">
        <span class="text-[11px] font-bold tracking-widest text-slate-400/80 dark:text-slate-500/80 uppercase select-none">
            {{ config('app.version', 'v1.0.0') }}
        </span>
    </div>
</div>

<script>
    function toggleAppTheme() {
        const html = document.documentElement;
        const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        fetch("{{ route('configuracoes.tema') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ theme: newTheme })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                if (newTheme === 'dark') {
                    html.classList.add('dark');
                    document.getElementById('btn-theme-text').innerText = 'Tema Escuro';
                    document.getElementById('btn-theme-svg-wrapper').innerHTML = `
                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    `;
                } else {
                    html.classList.remove('dark');
                    document.getElementById('btn-theme-text').innerText = 'Tema Claro';
                    document.getElementById('btn-theme-svg-wrapper').innerHTML = `
                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m2.828-9.9a5 5 0 117.072 7.072l-7.072-7.072z" />
                        </svg>
                    `;
                }
            }
        });
    }
</script>
@endsection
