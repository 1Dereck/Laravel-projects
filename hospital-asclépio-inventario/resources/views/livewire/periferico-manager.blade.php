<div class="space-y-6">
    <x-slot name="header">Gestão de Periféricos Avulsos</x-slot>

    <!-- Top Action & Search Bar -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-6 shadow-xl flex flex-col md:flex-row gap-4 items-stretch md:items-center justify-between">
        <div class="flex flex-col lg:flex-row gap-3 flex-1 items-stretch lg:items-center">
            <form action="#" @submit.prevent="$refs.searchPerifInput.blur()" x-data class="flex items-center gap-2 flex-1 min-w-48">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input x-ref="searchPerifInput"
                           wire:model.live.debounce.300ms="search"
                           type="search"
                           enterkeyhint="search"
                           @keydown.enter="$el.blur()"
                           placeholder="Buscar por tipo, patrimonio, serial ou observações..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition">
                </div>
                <button type="submit"
                        @click="$refs.searchPerifInput.blur()"
                        class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 font-bold text-slate-950 text-xs sm:text-sm transition shadow-md shadow-emerald-500/20 shrink-0 flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Buscar</span>
                </button>
            </form>

            @if(auth()->user()->isUsuario())
                <div class="px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-sm font-semibold flex items-center gap-2">
                    <span class="text-xs uppercase tracking-wider text-slate-500">Setor:</span>
                    <strong class="text-emerald-600 dark:text-emerald-400">{{ auth()->user()->setor?->local ?? 'Sem Setor' }}</strong>
                </div>
            @elseif(auth()->user()->isCoordenador())
                <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto items-center">
                    <div class="px-4 py-2.5 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-600 dark:text-purple-400 text-sm font-bold flex items-center gap-2">
                        <span class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">Secretaria:</span>
                        <span>{{ auth()->user()->setor?->secretaria?->secretaria ?? auth()->user()->setor?->local ?? 'Minha Secretaria' }}</span>
                    </div>
                    <div class="min-w-48">
                        <flux:select wire:model.live="setorFilter" variant="combobox" placeholder="Todos os Locais ({{ $locais->count() }})...">
                            <flux:select.option value="">-- Todos os Locais --</flux:select.option>
                            @foreach($locais as $l)
                                <flux:select.option value="{{ $l->id_local }}">{{ $l->local }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>
            @else
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="min-w-48">
                        <flux:select wire:model.live="secretariaFilter" variant="combobox" placeholder="Todas as Secretarias...">
                            <flux:select.option value="">-- Todas as Secretarias --</flux:select.option>
                            @foreach($secretarias as $s)
                                <flux:select.option value="{{ $s->id_secretarias }}">{{ $s->secretaria }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                    <div class="min-w-48">
                        <flux:select wire:model.live="setorFilter" variant="combobox" placeholder="Todos os Locais ({{ $locais->count() }})...">
                            <flux:select.option value="">-- Todos os Locais --</flux:select.option>
                            @foreach($locais as $l)
                                <flux:select.option value="{{ $l->id_local }}">{{ $l->local }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>
            @endif
        </div>

        @can('create', App\Models\Periferico::class)
            <button wire:click="novoPeriferico" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 font-bold text-slate-950 text-sm transition shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2 cursor-pointer shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Novo Periférico
            </button>
        @endcan
    </div>

    @if(session()->has('message'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-sm font-semibold flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('message') }}
        </div>
    @endif

    <!-- Main List Container -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl overflow-hidden">
        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-950/60 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Tipo / Descrição</th>
                        <th class="px-6 py-4">Setor Destino</th>
                        <th class="px-6 py-4">Patrimônio / Serial</th>
                        <th class="px-6 py-4">PC Vinculado</th>
                        <th class="px-6 py-4 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($perifericos as $per)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-900 dark:text-slate-100 block">{{ $per->tipo }}</span>
                                @if($per->observacoes)
                                    <span class="text-xs text-slate-500 dark:text-slate-400 block truncate max-w-xs">{{ $per->observacoes }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($per->local)
                                    <div class="flex flex-col gap-1 items-start">
                                        @if($per->local->secretaria)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                                {{ $per->local->secretaria->secretaria }}
                                            </span>
                                        @endif
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 shadow-xs">
                                            <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            {{ $per->local->local }}
                                        </span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                        N/A
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                                {{ $per->serial_patrimonio ?? 'Sem Patrimônio/Serial' }}
                            </td>
                            <td class="px-6 py-4 text-xs">
                                @if($per->equipamento)
                                    <span class="px-2.5 py-1 rounded bg-slate-100 dark:bg-slate-800 text-teal-600 dark:text-teal-400 font-mono font-bold border border-slate-200 dark:border-slate-700">
                                        PC: {{ $per->equipamento->serial }} ({{ $per->equipamento->tipo }})
                                    </span>
                                @else
                                    <span class="text-slate-400 dark:text-slate-500 italic">Avulso no Setor</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="verHistorico({{ $per->id }})" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold transition cursor-pointer">
                                        Histórico
                                    </button>
                                    @can('update', $per)
                                        <button wire:click="editarPeriferico({{ $per->id }})" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-semibold transition border border-emerald-500/20 cursor-pointer">
                                            Editar
                                        </button>
                                    @endcan
                                    @if(auth()->user()->isDiretor())
                                        <button wire:click="excluirPeriferico({{ $per->id }})"
                                                wire:confirm="Mover periférico para a Lixeira?"
                                                class="px-3 py-1.5 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-600 dark:text-red-400 text-xs font-semibold transition border border-red-500/20 cursor-pointer">
                                            Excluir
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                Nenhum periférico cadastrado ou encontrado na busca.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Stacked Cards view -->
        <div class="block lg:hidden divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($perifericos as $per)
                <div class="p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-base font-bold text-slate-900 dark:text-slate-100">{{ $per->tipo }}</h4>
                        <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-500/10 px-2.5 py-1 rounded-lg border border-indigo-500/20 inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            {{ $per->local ? $per->local->local : 'N/A' }}
                        </span>
                    </div>

                    <div>
                        <span class="font-mono text-xs text-emerald-600 dark:text-emerald-400 font-bold block">Patrimônio/Serial: {{ $per->serial_patrimonio ?? 'S/N' }}</span>
                        @if($per->observacoes)
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $per->observacoes }}</p>
                        @endif
                    </div>

                    <div class="pt-2 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-xs text-slate-500 dark:text-slate-400">
                            PC Vinculado: <strong class="text-slate-700 dark:text-slate-300 font-mono">{{ $per->equipamento ? $per->equipamento->serial : 'Nenhum' }}</strong>
                        </span>
                        <div class="flex items-center gap-2.5 shrink-0">
                            <button wire:click="verHistorico({{ $per->id }})" class="px-2.5 py-1 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                                Histórico
                            </button>
                            @can('update', $per)
                                <button wire:click="editarPeriferico({{ $per->id }})" class="px-2.5 py-1 rounded bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-semibold border border-emerald-500/20">
                                    Editar
                                </button>
                            @endcan
                            @if(auth()->user()->isDiretor())
                                <button wire:click="excluirPeriferico({{ $per->id }})"
                                        wire:confirm="Excluir periférico?"
                                        class="px-2.5 py-1 rounded bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-semibold border border-red-500/20">
                                    Excluir
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-500 text-xs">
                    Nenhum periférico cadastrado.
                </div>
            @endforelse
        </div>

        <div class="p-4 sm:p-5 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
            <flux:pagination :paginator="$perifericos" />
        </div>
    </div>

    <!-- Modal Form (Create / Edit Periferico) -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-md p-6 shadow-2xl space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">
                    {{ $perifericoId ? 'Editar Periférico' : 'Cadastrar Periférico' }}
                </h3>
                <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form wire:submit="salvar" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Secretaria *</label>
                    <flux:select wire:model.live="secretaria_id" variant="combobox" placeholder="Selecione a Secretaria..." :disabled="auth()->user()->isCoordenador()">
                        <flux:select.option value="">Selecione a Secretaria...</flux:select.option>
                        @foreach($secretarias as $s)
                            <flux:select.option value="{{ $s->id_secretarias }}">{{ $s->secretaria }} — {{ $s->nome_extenso }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    @error('secretaria_id') <span class="text-xs text-red-500 dark:text-red-400 block mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Local Destino (Setor) *</label>
                    <flux:select wire:model.live="setor_id" variant="combobox" placeholder="Selecione o local...">
                        <flux:select.option value="">Selecione o local...</flux:select.option>
                        @foreach($modalLocais as $l)
                            <flux:select.option value="{{ $l->id_local }}">{{ $l->local }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    @error('setor_id') <span class="text-xs text-red-500 dark:text-red-400 block mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Tipo de Periférico *</label>
                    <input wire:model="tipo" type="text" placeholder="ex: Impressora HP, Estabilizador, Switch 8p, Leitor Código de Barras"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                    @error('tipo') <span class="text-xs text-red-500 dark:text-red-400 block mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="per-serial-input" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Patrimônio ou Serial (Opcional)</label>
                    <input id="per-serial-input" wire:model="serial_patrimonio" type="text" placeholder="ex: PAT-2026-99 ou SN12345"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50 mb-2">

                    <div class="flex items-center gap-2">
                        <!-- Opção 1: Cód. de Barra -->
                        <button type="button"
                                onclick="escanearCodigoBarras('per-serial-input')"
                                title="Escanear Cód. de Barra"
                                class="flex-1 px-3 py-2 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 font-bold text-xs flex items-center justify-center gap-1.5 transition cursor-pointer active:scale-95">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Cód. de Barra</span>
                        </button>

                        <!-- Opção 2: Foto OCR -->
                        <button type="button"
                                onclick="lerTextoOCR('per-serial-input')"
                                title="Ler Números por Foto"
                                class="flex-1 px-3 py-2 rounded-xl bg-teal-500/10 hover:bg-teal-500/20 text-teal-600 dark:text-teal-400 border border-teal-500/20 font-bold text-xs flex items-center justify-center gap-1.5 transition cursor-pointer active:scale-95">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Foto OCR</span>
                        </button>
                    </div>
                    @error('serial_patrimonio') <span class="text-xs text-red-500 dark:text-red-400 block mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Vincular a Computador Específico (Opcional)</label>
                    <flux:select wire:model="equipamento_id" variant="combobox" placeholder="Nenhum (Periférico Avulso do Setor)">
                        <flux:select.option value="">Nenhum (Periférico Avulso do Setor)</flux:select.option>
                        @foreach($equipamentos as $eq)
                            <flux:select.option value="{{ $eq->id }}">{{ $eq->tipo }} — Serial: {{ $eq->serial }} (Setor: {{ $eq->setor->nome ?? 'N/A' }})</flux:select.option>
                        @endforeach
                    </flux:select>
                    @error('equipamento_id') <span class="text-xs text-red-500 dark:text-red-400 block mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Observações Adicionais</label>
                    <textarea wire:model="observacoes" rows="2" placeholder="Informações de garantia, modelo específico ou localização no setor..."
                              class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-sm">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold text-sm shadow-lg shadow-emerald-500/20">
                        {{ $perifericoId ? 'Salvar Alterações' : 'Cadastrar Periférico' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Livewire Modal Histórico -->
    <livewire:historico-modal />
</div>
