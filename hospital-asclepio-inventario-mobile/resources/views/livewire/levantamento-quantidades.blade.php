<div class="space-y-6">
    <x-slot name="header">Levantamento de Quantidades</x-slot>

    <!-- Header Controls & Mode Switcher -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl shadow-xl space-y-5">
        @if(!auth()->user()->isUsuario())
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
                <div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Levantamento de Hardware</span>
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-slate-100 mt-0.5">Quantidades de Desktops, Notebooks e Total de PCs</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Consultas por Secretaria ou filtro isolado por Local.
                    </p>
                </div>

                <!-- Mode Selector (Por Secretaria vs Por Local) -->
                <div class="flex bg-slate-100 dark:bg-slate-800 p-1.5 rounded-xl border border-slate-200 dark:border-slate-700 shrink-0">
                    <button wire:click="$set('modoQuantidades', 'secretaria')"
                            class="px-4 py-2 rounded-lg text-xs font-extrabold transition-all cursor-pointer flex items-center gap-2 {{ $modoQuantidades === 'secretaria' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0h4m-4 0v10" />
                        </svg>
                        <span>1. Por Secretaria</span>
                    </button>
                    <button wire:click="$set('modoQuantidades', 'local')"
                            class="px-4 py-2 rounded-lg text-xs font-extrabold transition-all cursor-pointer flex items-center gap-2 {{ $modoQuantidades === 'local' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>2. Por Local</span>
                    </button>
                </div>
            </div>

            <!-- Dropdowns de Seleção -->
            @if($modoQuantidades === 'secretaria')
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                        Selecionar Secretaria:
                    </label>
                    @if(auth()->user()->isCoordenador())
                        <div class="px-4 py-3 bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">
                                    🏢
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $quantidadesSecretaria?->secretaria ?? 'Sua Secretaria' }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $quantidadesSecretaria?->nome_extenso }}</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 uppercase tracking-wider">
                                Coordenador
                            </span>
                        </div>
                    @else
                        <flux:select wire:model.live="quantidadesSecretariaId" variant="combobox" placeholder="Selecione uma Secretaria...">
                            <flux:select.option value="">🌐 Todas as Secretarias (Geral)</flux:select.option>
                            @foreach($quantidadesSecretarias as $qs)
                                <flux:select.option value="{{ $qs->id_secretarias }}">
                                    {{ $qs->secretaria }} — {{ $qs->nome_extenso }} ({{ $qs->locais->count() }} locais)
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    @endif
                </div>
            @else
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                        Selecionar Local:
                    </label>
                    <flux:select wire:model.live="quantidadesLocalId" variant="combobox" placeholder="Selecione um Local...">
                        <flux:select.option value="">📍 Todos os Locais (Geral)</flux:select.option>
                        @foreach($quantidadesLocais as $ql)
                            <flux:select.option value="{{ $ql->id_local }}">
                                {{ $ql->local }} @if($ql->secretaria) ({{ $ql->secretaria->secretaria }}) @endif
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            @endif
        @else
            <!-- Visão do Usuário (Local Atribuído) -->
            <div class="flex items-center gap-3 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-700 dark:text-emerald-300">
                <svg class="w-6 h-6 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-sm font-bold">Levantamento do seu Local de Trabalho</p>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400">Exibindo contagem de equipamentos atribuídos a: <strong>{{ $quantidadesLocal?->local ?? 'Seu Local' }}</strong></p>
                </div>
            </div>
        @endif
    </div>

    <!-- MODO 1: SECRETARIA -->
    @if($modoQuantidades === 'secretaria' && !auth()->user()->isUsuario())
        <!-- Header da Secretaria com Botão de PDF -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xl space-y-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-slate-100 dark:border-slate-800 pb-5 gap-4">
                <div>
                    @if($quantidadesSecretaria)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Secretaria Selecionada
                        </span>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 mt-2">{{ $quantidadesSecretaria->secretaria }}</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">{{ $quantidadesSecretaria->nome_extenso }}</p>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h1.5a2.5 2.5 0 002.5-2.5V8.065" />
                            </svg>
                            Visão Geral
                        </span>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 mt-2">Todas as Secretarias (Geral)</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">Somatório consolidado de todas as secretarias e setores hospitalares</p>
                    @endif
                    <p class="text-xs text-slate-400 mt-1">Total de Locais Vinculados: <strong class="text-slate-700 dark:text-slate-300 font-bold">{{ $secStats['locais_count'] }}</strong></p>
                </div>

                <a href="{{ route('quantidades.secretaria.pdf', $quantidadesSecretariaId ?? 0) }}" target="_blank"
                   class="px-5 py-3 rounded-xl bg-linear-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 font-bold text-slate-950 text-sm transition shadow-lg shadow-emerald-500/20 flex items-center gap-2 cursor-pointer shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Gerar PDF (Levantamento)</span>
                </a>
            </div>

            <!-- Metric Cards (Total de PCs, Desktops, Notebooks) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Total de PCs (Desktops + Notebooks) -->
                <div class="bg-linear-to-br from-emerald-500/10 to-teal-500/10 dark:from-emerald-500/20 dark:to-teal-500/20 border border-emerald-500/30 p-5 rounded-2xl shadow-md relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-700 dark:text-emerald-300">Total de PCs</span>
                        <span class="p-2.5 rounded-xl bg-emerald-500 text-slate-950 text-xl shadow-md">📊</span>
                    </div>
                    <div class="mt-3">
                        <span class="text-3xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">{{ $secStats['total_pcs'] }}</span>
                        <span class="block text-[11px] font-extrabold text-emerald-700 dark:text-emerald-300 mt-1 uppercase tracking-wider">Desktops + Notebooks</span>
                    </div>
                </div>

                <!-- Desktops -->
                <div class="bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 p-5 rounded-2xl shadow-sm relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Desktops</span>
                        <span class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xl">🖥️</span>
                    </div>
                    <div class="mt-3">
                        <span class="text-3xl font-black text-slate-900 dark:text-slate-100 tracking-tight">{{ $secStats['desktops'] }}</span>
                        <span class="block text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 mt-1">Computadores de Mesa</span>
                    </div>
                </div>

                <!-- Notebooks -->
                <div class="bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 p-5 rounded-2xl shadow-sm relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Notebooks</span>
                        <span class="p-2.5 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 text-xl">💻</span>
                    </div>
                    <div class="mt-3">
                        <span class="text-3xl font-black text-slate-900 dark:text-slate-100 tracking-tight">{{ $secStats['notebooks'] }}</span>
                        <span class="block text-[11px] font-semibold text-teal-600 dark:text-teal-400 mt-1">Notebooks Portáteis</span>
                    </div>
                </div>
            </div>
        </div>

    <!-- MODO 2: LOCAL -->
    @else
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xl space-y-6">
            <!-- Header do Local com Botão de PDF -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-slate-100 dark:border-slate-800 pb-5 gap-4">
                <div>
                    @if($quantidadesLocal)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                            Local Selecionado
                        </span>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 mt-2">{{ $quantidadesLocal->local }}</h2>
                        <div class="flex flex-wrap gap-3 text-xs text-slate-500 dark:text-slate-400 mt-1">
                            @if($quantidadesLocal->secretaria)
                                <span>Secretaria: <strong class="text-indigo-600 dark:text-indigo-400 font-bold">{{ $quantidadesLocal->secretaria->secretaria }}</strong></span>
                            @endif
                            @if($quantidadesLocal->bairro)
                                <span>Bairro: <strong class="text-slate-700 dark:text-slate-300">{{ $quantidadesLocal->bairro }}</strong></span>
                            @endif
                        </div>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                            Visão Geral
                        </span>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 mt-2">Todos os Locais / Setores (Geral)</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">Métricas e computadores consolidados de todos os locais cadastrados</p>
                    @endif
                </div>

                <a href="{{ route('quantidades.local.pdf', $quantidadesLocalId ?? 0) }}" target="_blank"
                   class="px-5 py-3 rounded-xl bg-linear-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 font-bold text-slate-950 text-sm transition shadow-lg shadow-emerald-500/20 flex items-center gap-2 cursor-pointer shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Gerar PDF (Levantamento)</span>
                </a>
            </div>

            <!-- Metric Cards do Local -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Total de PCs -->
                <div class="bg-linear-to-br from-emerald-500/10 to-teal-500/10 dark:from-emerald-500/20 dark:to-teal-500/20 border border-emerald-500/30 p-5 rounded-2xl shadow-md relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-700 dark:text-emerald-300">Total de PCs</span>
                        <span class="p-2.5 rounded-xl bg-emerald-500 text-slate-950 text-xl shadow-md">📊</span>
                    </div>
                    <div class="mt-3">
                        <span class="text-3xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">{{ $locStats['total_pcs'] }}</span>
                        <span class="block text-[11px] font-extrabold text-emerald-700 dark:text-emerald-300 mt-1 uppercase tracking-wider">Desktops + Notebooks</span>
                    </div>
                </div>

                <!-- Desktops -->
                <div class="bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 p-5 rounded-2xl shadow-sm relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Desktops</span>
                        <span class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xl">🖥️</span>
                    </div>
                    <div class="mt-3">
                        <span class="text-3xl font-black text-slate-900 dark:text-slate-100 tracking-tight">{{ $locStats['desktops'] }}</span>
                        <span class="block text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 mt-1">Computadores de Mesa</span>
                    </div>
                </div>

                <!-- Notebooks -->
                <div class="bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 p-5 rounded-2xl shadow-sm relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Notebooks</span>
                        <span class="p-2.5 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 text-xl">💻</span>
                    </div>
                    <div class="mt-3">
                        <span class="text-3xl font-black text-slate-900 dark:text-slate-100 tracking-tight">{{ $locStats['notebooks'] }}</span>
                        <span class="block text-[11px] font-semibold text-teal-600 dark:text-teal-400 mt-1">Notebooks Portáteis</span>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
