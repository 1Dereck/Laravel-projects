<div class="w-full max-w-md">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 shadow-2xl backdrop-blur-xl relative overflow-hidden">
        <!-- Glow accent -->
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="text-center mb-8">
            <img src="{{ asset('img/icon-inventario.png') }}" alt="Logo Inventário TI" class="h-20 w-20 mx-auto rounded-2xl object-contain shadow-xl shadow-emerald-500/20 mb-4">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-slate-100 tracking-tight">Inventário TI</h2>
            <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mt-1">Gestão de Equipamentos</p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Informe suas credenciais para acessar o painel</p>
        </div>

        <form wire:submit="login" class="space-y-5">
            <div>
                <label for="username" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Usuário de Acesso</label>
                <input wire:model="username" type="text" id="username" required autofocus
                       class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition duration-200 text-sm"
                       placeholder="Digite seu usuário (ex: dereck ou maciel)">
                @error('username')
                    <span class="text-xs text-red-500 dark:text-red-400 mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Senha de Segurança</label>
                <input wire:model="password" type="password" id="password" required
                       class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition duration-200 text-sm"
                       placeholder="••••••••">
                @error('password')
                    <span class="text-xs text-red-500 dark:text-red-400 mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model="remember" type="checkbox" class="w-4 h-4 rounded bg-slate-100 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-emerald-500 focus:ring-emerald-500">
                    <span class="text-xs font-medium text-slate-600 dark:text-slate-400">Lembrar-me neste dispositivo</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-emerald-500 hover:bg-emerald-600 font-bold text-slate-950 transition duration-200 shadow-lg shadow-emerald-500/25 flex items-center justify-center gap-2 group">
                <span>Entrar no Sistema</span>
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </form>
    </div>
</div>
