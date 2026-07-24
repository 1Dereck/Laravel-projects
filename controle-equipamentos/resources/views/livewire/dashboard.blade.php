<div class="space-y-8">
    <x-slot name="header">Dashboard Gerencial</x-slot>

    <!-- Director Trash Alert Banner -->
    @if(auth()->user()->isDiretor() && $trashedCount > 0)
        <div class="bg-linear-to-r from-red-500/10 via-red-500/5 to-white dark:from-red-950/40 dark:via-red-900/20 dark:to-slate-900 border border-red-200 dark:border-red-500/30 rounded-2xl p-5 shadow-lg flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-500/15 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0 border border-red-500/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-red-900 dark:text-red-200">Atenção, Diretor: Itens na Lixeira</h3>
                    <p class="text-xs text-red-700/80 dark:text-red-300/80 mt-0.5">Existem <span class="font-bold text-red-600 dark:text-red-400">{{ $trashedCount }}</span> registro(s) desativado(s) aguardando restauração ou expurgo definitivo.</p>
                </div>
            </div>
            <a href="{{ route('lixeira.index') }}" class="px-4 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-slate-950 font-bold text-xs transition-colors shadow-md shrink-0">
                Acessar Lixeira
            </a>
        </div>
    @endif

    <!-- Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Metric Card 1: Computadores -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden group hover:border-slate-300 dark:hover:border-slate-700 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Computadores</span>
                    <h4 class="text-3xl font-black text-slate-900 dark:text-slate-100 mt-1">{{ $totalEquipamentos }}</h4>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                <span>🖥️ Desktops: <strong class="text-slate-800 dark:text-slate-200 font-bold">{{ $totalDesktops }}</strong></span>
                <span>💻 Notebooks: <strong class="text-slate-800 dark:text-slate-200 font-bold">{{ $totalNotebooks }}</strong></span>
            </div>
        </div>

        <!-- Metric Card 2: Monitores -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden group hover:border-slate-300 dark:hover:border-slate-700 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Monitores Mapeados</span>
                    <h4 class="text-3xl font-black text-slate-900 dark:text-slate-100 mt-1">{{ $totalMonitores }}</h4>
                </div>
                <div class="w-12 h-12 rounded-xl bg-teal-500/10 border border-teal-500/20 text-teal-600 dark:text-teal-400 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80 text-xs text-slate-500 dark:text-slate-400">
                <span>Média por PC: <strong class="text-slate-800 dark:text-slate-200 font-bold">{{ $totalEquipamentos > 0 ? number_format($totalMonitores / $totalEquipamentos, 1) : 0 }}</strong></span>
            </div>
        </div>

        <!-- Metric Card 3: Periféricos -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden group hover:border-slate-300 dark:hover:border-slate-700 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Periféricos Avulsos</span>
                    <h4 class="text-3xl font-black text-slate-900 dark:text-slate-100 mt-1">{{ $totalPerifericos }}</h4>
                </div>
                <div class="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-600 dark:text-cyan-400 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80 text-xs text-slate-500 dark:text-slate-400">
                <span>Impressoras, No-breaks, Leitores</span>
            </div>
        </div>

        <!-- Metric Card 4: Setores -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden group hover:border-slate-300 dark:hover:border-slate-700 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Setores Ativos</span>
                    <h4 class="text-3xl font-black text-slate-900 dark:text-slate-100 mt-1">{{ $totalSetores }}</h4>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h6m-6 0V11m0 0h6m-6 0H7" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80 text-xs text-slate-500 dark:text-slate-400">
                <span>Secretarias e Minisetores</span>
            </div>
        </div>
    </div>

    <!-- Charts & Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Notebook vs Desktop Donut Visual -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
                Distribuição de Equipamentos
            </h3>

            <div class="py-6 flex flex-col items-center justify-center">
                @php
                    $percDesktop = $totalEquipamentos > 0 ? round(($totalDesktops / $totalEquipamentos) * 100) : 0;
                    $percNotebook = $totalEquipamentos > 0 ? round(($totalNotebooks / $totalEquipamentos) * 100) : 0;
                @endphp
                <!-- Circular Visual Bar -->
                <div class="relative w-40 h-40 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                        <path class="text-slate-200 dark:text-slate-800" stroke-width="3.8" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <path class="text-emerald-500" stroke-linecap="round" stroke-dasharray="{{ $percDesktop }}, 100" stroke-width="3.8" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    </svg>
                    <div class="absolute text-center">
                        <span class="block text-2xl font-black text-slate-900 dark:text-slate-100">{{ $totalEquipamentos }}</span>
                        <span class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Equipamentos</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-md bg-emerald-500"></span>
                    <span class="text-slate-700 dark:text-slate-300">Desktops ({{ $percDesktop }}%)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-md bg-slate-400 dark:bg-slate-700"></span>
                    <span class="text-slate-600 dark:text-slate-400">Notebooks ({{ $percNotebook }}%)</span>
                </div>
            </div>
        </div>

        <!-- Top 10 Setores Bar Chart Visual -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-teal-500 dark:bg-teal-400"></span>
                Top Setores por Volume de Equipamentos
            </h3>

            <div class="space-y-3 my-auto">
                @php
                    $maxEquip = $topSetores->max('equipamentos_count') ?: 1;
                @endphp
                @forelse($topSetores as $setor)
                    @php
                        $widthPercent = round(($setor->equipamentos_count / $maxEquip) * 100);
                    @endphp
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-slate-700 dark:text-slate-300 truncate max-w-50">{{ $setor->nome }}</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $setor->equipamentos_count }} pc(s)</span>
                        </div>
                        <div class="w-full h-2.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-linear-to-r from-emerald-500 to-teal-400 rounded-full" style="width: {{ max($widthPercent, 5) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 py-6 text-center">Nenhum setor cadastrado com equipamentos.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Feed de Atividades Recentes (Audit Trail) -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyan-500 dark:bg-cyan-400"></span>
                    Feed de Atividades Recentes
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Últimas 10 edições e ações registradas no sistema</p>
            </div>
        </div>

        <div class="divide-y divide-slate-100 dark:divide-slate-800/80">
            @forelse($recentActivities as $act)
                <div class="py-3.5 flex items-center justify-between text-xs hover:bg-slate-50 dark:hover:bg-slate-800/30 px-2 rounded-xl transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 font-bold flex items-center justify-center">
                            {{ $act->causer ? $act->causer->initials() : 'SYS' }}
                        </div>
                        <div>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $act->causer ? $act->causer->name : 'Sistema' }}</span>
                            <span class="text-slate-500 dark:text-slate-400"> realizou </span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400 uppercase text-[10px] px-1.5 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20">
                                {{ $act->description }}
                            </span>
                            <span class="text-slate-500 dark:text-slate-400"> em </span>
                            <span class="font-medium text-slate-700 dark:text-slate-300">
                                {{ class_basename($act->subject_type) }} #{{ $act->subject_id }}
                            </span>
                        </div>
                    </div>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500 font-mono">{{ $act->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <div class="py-8 text-center text-slate-500 text-xs">
                    Nenhuma atividade registrada no histórico de auditoria ainda.
                </div>
            @endforelse
        </div>
    </div>
</div>
