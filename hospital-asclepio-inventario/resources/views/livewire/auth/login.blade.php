<div class="w-full max-w-md">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 shadow-2xl backdrop-blur-xl relative overflow-hidden">
        <!-- Glow accent -->
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="text-center mb-8">
            <div class="mb-3 flex justify-center">
                <img src="{{ asset('img/icon-hospitais-asclepio.png') }}" class="rounded-xl" style="width: 150px; height: auto;" alt="Logo Hospitais Asclépio">
            </div>
            <h1 class="text-xl font-black tracking-wider uppercase text-emerald-600 dark:text-emerald-400">Hospitais Asclépio</h1>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight mt-1">Gestão de TI Hospitalar</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Inventário de Equipamentos & Parque Tecnológico</p>
        </div>

        <form wire:submit="login" class="space-y-5">
            @error('username')
                <div class="p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 text-xs font-medium flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>{{ $message }}</span>
                </div>
            @enderror

            <div>
                <label for="username" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Usuário de Acesso</label>
                <input wire:model="username" type="text" id="username" required autofocus
                       class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition duration-200 text-sm"
                       placeholder="Digite seu usuário (ex: Diretor ou Administrador)">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Senha</label>
                <input wire:model="password" type="password" id="password" required
                       class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition duration-200 text-sm"
                       placeholder="••••••••">
                @error('password')
                    <span class="text-xs text-red-500 dark:text-red-400 mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center justify-between px-1.5">
                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                    <input wire:model="remember" type="checkbox" class="w-4 h-4 rounded bg-slate-100 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-emerald-500 focus:ring-emerald-500 cursor-pointer">
                    <span class="text-xs font-medium text-slate-600 dark:text-slate-400">Manter conectado</span>
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
