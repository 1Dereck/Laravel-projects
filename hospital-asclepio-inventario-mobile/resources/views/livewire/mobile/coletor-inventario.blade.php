<div class="max-w-md mx-auto p-4 sm:p-5 space-y-4">
    {{-- Header com Ícone Oficial, Título e Indicador de Rede --}}
    <div class="flex items-center justify-between bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800">
        <div class="flex items-center gap-3">
            <img src="{{ asset('img/icon-hospitais-asclepio.png') }}" class="w-10 h-10 rounded-xl shadow-md border border-slate-100 dark:border-slate-800 object-contain bg-white" alt="Hospitais Asclépio">
            <div>
                <h1 class="font-bold text-base text-slate-800 dark:text-white leading-tight">Asclépio Mobile</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400">Coletor de Inventário</p>
            </div>
        </div>

        <div class="flex flex-col items-end gap-1">
            @if($estaOnline)
                <flux:badge color="green" icon="signal" size="sm">Online</flux:badge>
            @else
                <flux:badge color="amber" icon="signal-slash" size="sm">Modo Offline</flux:badge>
            @endif
            <button wire:click="atualizarStatus" class="text-[10px] text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 underline cursor-pointer">
                Checar rede
            </button>
        </div>
    </div>

    {{-- Banner de Pendências e Ação Rápida de Sincronização --}}
    @if($totalPendentes > 0)
        <div class="bg-linear-to-r from-blue-500/10 via-indigo-500/10 to-teal-500/10 dark:from-blue-950/40 dark:via-indigo-950/40 dark:to-teal-950/40 border border-blue-200 dark:border-blue-800/80 p-4 rounded-2xl flex items-center justify-between gap-3 shadow-xs">
            <div class="space-y-0.5">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-600 text-white text-xs font-bold">{{ $totalPendentes }}</span>
                    <span class="font-semibold text-sm text-slate-900 dark:text-blue-100">Coletas no Celular</span>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-400">Aguardando envio ao servidor central</p>
            </div>

            @if($estaOnline)
                <flux:button wire:click="executarSincronizacao" variant="primary" size="sm" icon="arrow-path">
                    Sincronizar
                </flux:button>
            @else
                <span class="text-[11px] font-medium text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-2 py-1 rounded-md">
                    Wi-Fi Off
                </span>
            @endif
        </div>
    @endif

    {{-- Mensagens de Feedback --}}
    @if($mensagemStatus)
        <div class="p-3.5 text-xs rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-800 flex items-start gap-2 animate-fadeIn">
            <flux:icon name="check-circle" class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
            <div class="flex-1">{{ $mensagemStatus }}</div>
        </div>
    @endif

    @if($mensagemErro)
        <div class="p-3.5 text-xs rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-800 dark:text-rose-200 border border-rose-200 dark:border-rose-800 flex items-start gap-2">
            <flux:icon name="exclamation-triangle" class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" />
            <div class="flex-1">{{ $mensagemErro }}</div>
        </div>
    @endif

    {{-- Formulário de Coleta em Campo --}}
    <form wire:submit="salvarColetaOffline" class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-4">
        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Nº de Patrimônio / Etiqueta *
            </label>
            <div class="relative">
                <input
                    type="text"
                    wire:model="patrimonio"
                    placeholder="Ex: EQ-09412"
                    autofocus
                    required
                    class="w-full text-base font-mono tracking-wide px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all"
                />
            </div>
            @error('patrimonio')
                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Tipo de Item</label>
                <select wire:model="tipo" class="w-full text-sm py-2 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                    <option value="equipamento">Equipamento</option>
                    <option value="monitor">Monitor</option>
                    <option value="periferico">Periférico</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Desempenho</label>
                <select wire:model="tipoDesempenho" class="w-full text-sm py-2 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                    <option value="Alto">Alto</option>
                    <option value="Médio">Médio</option>
                    <option value="Básico">Básico</option>
                </select>
            </div>
        </div>

        <div class="space-y-3">
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Localização (Ala / Prédio)</label>
                <select wire:model="localId" class="w-full text-sm py-2 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                    <option value="">Selecione o local...</option>
                    @foreach($locais as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->nome }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Setor Interno</label>
                <select wire:model="setorId" class="w-full text-sm py-2 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                    <option value="">Selecione o setor...</option>
                    @foreach($setores as $set)
                        <option value="{{ $set->id }}">{{ $set->nome }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Observações de Campo</label>
            <textarea
                wire:model="observacoes"
                rows="2"
                placeholder="Ex: Leito 03, avaria no gabinete, cabo faltando..."
                class="w-full text-sm py-2 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-teal-500"
            ></textarea>
        </div>

        <flux:button type="submit" variant="primary" icon="plus" class="w-full py-2.5 font-medium shadow-md">
            Salvar Coleta no Celular
        </flux:button>
    </form>

    {{-- Lista de Coletas Recentes no Aparelho --}}
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                Últimas Coletas no Aparelho
            </h2>
            <span class="text-[11px] text-slate-400">
                {{ $ultimasColetas->count() }} exibidas
            </span>
        </div>

        @if($ultimasColetas->isEmpty())
            <div class="py-6 text-center text-xs text-slate-400">
                Nenhuma coleta pendente no celular neste momento.
            </div>
        @else
            <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($ultimasColetas as $coleta)
                    <li class="py-2.5 flex items-center justify-between text-xs">
                        <div class="space-y-0.5">
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-slate-800 dark:text-white">
                                    {{ $coleta->payload['patrimonio'] ?? $coleta->uuid }}
                                </span>
                                <span class="text-[10px] uppercase font-semibold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                    {{ $coleta->entidade }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500">
                                {{ $coleta->coletado_em?->format('d/m H:i') ?? 'Agora' }}
                            </p>
                        </div>

                        <div>
                            @if($coleta->status === 'pendente')
                                <flux:badge color="amber" size="xs">Pendente</flux:badge>
                            @elseif($coleta->status === 'enviando')
                                <flux:badge color="blue" size="xs">Enviando</flux:badge>
                            @else
                                <flux:badge color="green" size="xs">OK</flux:badge>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Botão de Ação: Sincronizar e Purge (Limpeza de Armazenamento) --}}
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-3">
        <flux:button
            wire:click="executarSincronizacao"
            variant="filled"
            icon="arrow-path"
            class="w-full py-3 font-semibold shadow-xs"
            :disabled="!$estaOnline"
        >
            Sincronizar e Liberar Espaço no Celular
        </flux:button>

        <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 px-1">
            <span>Espaço do banco local: <strong class="text-slate-700 dark:text-slate-300">{{ $espacoLocalFormatado }}</strong></span>
            @if($estaOnline)
                <button wire:click="atualizarDadosMestres" class="text-teal-600 dark:text-teal-400 hover:underline cursor-pointer">
                    Atualizar Catálogo
                </button>
            @endif
        </div>
    </div>
</div>
