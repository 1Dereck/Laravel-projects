<div class="space-y-6">
    <x-slot name="header">Exportação de Relatórios PDF</x-slot>

    <!-- Navigation & Type Selection -->
    @if(!auth()->user()->isUsuario())
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl shadow-xl space-y-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">Filtro de Inventário para Impressão</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Selecione se deseja emitir o relatório de um Local específico ou de toda uma Secretaria com seus locais vinculados.</p>
            </div>

            <div class="flex bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700 shrink-0">
                <button wire:click="$set('tipoRelatorio', 'local')" class="px-4 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer {{ $tipoRelatorio === 'local' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-xs' : 'text-slate-600 dark:text-slate-400' }}">
                    Por Local
                </button>
                <button wire:click="$set('tipoRelatorio', 'secretaria')" class="px-4 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer {{ $tipoRelatorio === 'secretaria' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-xs' : 'text-slate-600 dark:text-slate-400' }}">
                    Por Secretaria
                </button>
            </div>
        </div>

        @if($tipoRelatorio === 'local')
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                    Selecionar Local:
                </label>
                <flux:select wire:model.live="selectedLocalId" variant="combobox" placeholder="Selecione ou digite um Local ({{ $locais->count() }} disponíveis)...">
                    <flux:select.option value="">-- Limpar Seleção --</flux:select.option>
                    @foreach($locais as $l)
                        <flux:select.option value="{{ $l->id_local }}">
                            {{ $l->local }} ({{ $l->equipamentos_count }} PCs, {{ $l->perifericos_count }} Periféricos)
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        @else
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                    Selecionar Secretaria:
                </label>
                <flux:select wire:model.live="selectedSecretariaId" variant="combobox" placeholder="Selecione uma Secretaria ({{ $secretarias->count() }} disponíveis)..." :disabled="auth()->user()->isCoordenador()">
                    <flux:select.option value="">-- Limpar Seleção --</flux:select.option>
                    @foreach($secretarias as $s)
                        <flux:select.option value="{{ $s->id_secretarias }}">
                            {{ $s->secretaria }} — {{ $s->nome_extenso }} ({{ $s->locais_count }} locais)
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        @endif
    </div>
    @endif

    <!-- Selected Local Details Card -->
    @if($tipoRelatorio === 'local' && $selectedLocal)
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xl space-y-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-slate-100 dark:border-slate-800 pb-5 gap-4">
                <div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Local Selecionado</span>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 mt-1">{{ $selectedLocal->local }}</h2>
                    <div class="flex flex-wrap gap-3 text-xs text-slate-500 dark:text-slate-400 mt-1">
                        @if($selectedLocal->secretaria)
                            <span>Secretaria: <strong class="text-indigo-600 dark:text-indigo-400 font-bold">{{ $selectedLocal->secretaria->secretaria }}</strong></span>
                        @endif
                        @if($selectedLocal->telefone)
                            <span>Tel: <strong class="text-slate-700 dark:text-slate-300">{{ $selectedLocal->telefone }}</strong></span>
                        @endif
                        @if($selectedLocal->bairro)
                            <span>Bairro: <strong class="text-slate-700 dark:text-slate-300">{{ $selectedLocal->bairro }}</strong></span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('relatorios.pdf', $selectedLocal->id_local) }}" target="_blank" class="px-5 py-3 rounded-xl bg-linear-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 font-bold text-slate-950 text-sm transition shadow-lg shadow-emerald-500/20 flex items-center gap-2 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Gerar Relatório PDF (Local)
                </a>
            </div>

            <!-- Computadores do Local -->
            <div class="space-y-4">
                <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
                    Computadores Instalados ({{ $selectedLocal->equipamentos->count() }})
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($selectedLocal->equipamentos as $eq)
                        <div class="bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl p-4 space-y-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400 uppercase text-xs">
                                        @if($eq->tipo === 'notebook') 💻 Notebook @else 🖥️ Desktop @endif
                                    </span>
                                    <h4 class="font-mono text-sm text-slate-900 dark:text-slate-100 font-bold mt-0.5">Série: {{ $eq->serial }}</h4>
                                </div>
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded {{ $eq->kit_teclado_mouse_locado ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                                    {{ $eq->kit_teclado_mouse_locado ? 'Kit Locado' : 'Kit Próprio' }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-500 dark:text-slate-400">Modelo: <strong class="text-slate-700 dark:text-slate-300 font-semibold">{{ $eq->marca_modelo ?? 'N/I' }}</strong></p>
                        </div>
                    @empty
                        <div class="col-span-2 p-6 text-center text-slate-500 text-xs bg-slate-50 dark:bg-slate-950/40 rounded-xl border border-dashed border-slate-200 dark:border-slate-800">
                            Nenhum computador cadastrado neste local.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    <!-- Selected Secretaria Details Card -->
    @elseif($tipoRelatorio === 'secretaria' && $selectedSecretaria)
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xl space-y-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-slate-100 dark:border-slate-800 pb-5 gap-4">
                <div>
                    <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest">Secretaria Selecionada</span>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 mt-1">{{ $selectedSecretaria->secretaria }}</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">{{ $selectedSecretaria->nome_extenso }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">Total de Locais Atribuídos a esta Secretaria: <strong class="text-slate-700 dark:text-slate-300 font-bold">{{ $selectedSecretaria->locais_atribuidos->count() }}</strong></p>
                </div>
                <a href="{{ route('relatorios.secretaria.pdf', $selectedSecretaria->id_secretarias) }}" target="_blank" class="px-5 py-3 rounded-xl bg-linear-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 font-bold text-slate-950 text-sm transition shadow-lg shadow-emerald-500/20 flex items-center gap-2 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Gerar Relatório PDF (Secretaria Completa)</span>
                </a>
            </div>

            <!-- Resumo dos Locais da Secretaria -->
            <div class="space-y-4">
                <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
                    Locais Atribuídos a esta Secretaria ({{ $selectedSecretaria->locais_atribuidos->count() }})
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($selectedSecretaria->locais_atribuidos as $locSec)
                        <div class="bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl p-4 space-y-3">
                            <div>
                                <h4 class="font-bold text-sm text-slate-900 dark:text-slate-100">{{ $locSec->local }}</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $locSec->rua }} {{ $locSec->numero ? ', '.$locSec->numero : '' }} {{ $locSec->bairro ? ' — Bairro '.$locSec->bairro : '' }}</p>
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-slate-200 dark:border-slate-800/80">
                                <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ $locSec->equipamentos->count() }} PC(s) | {{ $locSec->perifericos->count() }} Periférico(s)
                                </span>
                                <a href="{{ route('relatorios.pdf', $locSec->id_local) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>Gerar PDF (Setor)</span>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 p-6 text-center text-slate-500 text-xs bg-slate-50 dark:bg-slate-950/40 rounded-xl border border-dashed border-slate-200 dark:border-slate-800">
                            Nenhum local vinculado a esta secretaria ainda.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-12 text-center text-slate-500 space-y-3">
            <svg class="w-12 h-12 mx-auto text-slate-400 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16l2.879-2.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm font-semibold">Selecione uma opção acima para visualizar os detalhes e emitir o relatório PDF.</p>
        </div>
    @endif
</div>
