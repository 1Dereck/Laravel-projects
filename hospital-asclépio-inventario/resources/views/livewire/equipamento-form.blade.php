<div class="space-y-6">
    <x-slot name="header">Equipamentos (Desktops & Notebooks)</x-slot>

    @livewire('historico-modal')

    <!-- Top Action & Filter Bar -->
    <div class="flex flex-col md:flex-row gap-4 items-center justify-between bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xl">
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto flex-1">
            <form action="#" @submit.prevent="$refs.searchEqInput.blur()" x-data class="flex items-center gap-2 flex-1">
                <div class="relative flex-1">
                    <input x-ref="searchEqInput"
                           wire:model.live.debounce.300ms="search"
                           type="search"
                           enterkeyhint="search"
                           @keydown.enter="$el.blur()"
                           placeholder="Buscar por serial, marca ou responsável..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-sm placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500">
                    <svg class="w-5 h-5 text-slate-400 dark:text-slate-500 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <button type="submit"
                        @click="$refs.searchEqInput.blur()"
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
                    <div class="min-w-52">
                        <flux:select wire:model.live="setorFilter" variant="combobox" placeholder="Todos os Locais ({{ $locais->count() }})...">
                            <flux:select.option value="">-- Todos os Locais --</flux:select.option>
                            @foreach($locais as $l)
                                <flux:select.option value="{{ $l->id_local }}">{{ $l->local }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>
            @else
                <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                    <div class="min-w-52">
                        <flux:select wire:model.live="secretariaFilter" variant="combobox" placeholder="Todas as Secretarias...">
                            <flux:select.option value="">-- Todas as Secretarias --</flux:select.option>
                            @foreach($secretarias as $s)
                                <flux:select.option value="{{ $s->id_secretarias }}">{{ $s->secretaria }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                    <div class="min-w-52">
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

        @can('create', App\Models\Equipamento::class)
            <button wire:click="novoEquipamento" class="w-full md:w-auto px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 font-bold text-slate-950 text-sm transition shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2 shrink-0 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Cadastrar Equipamento
            </button>
        @endcan
    </div>

    <!-- Notification Message -->
    @if(session()->has('message'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-sm font-semibold flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('message') }}
        </div>
    @endif

    <!-- Content Table (Desktop) & Cards (Mobile) -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl overflow-hidden">
        <!-- Desktop Table view -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-950/60 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Equipamento</th>
                        <th class="px-6 py-4">Setor Alocado</th>
                        <th class="px-6 py-4">Série & Marca/Modelo</th>
                        <th class="px-6 py-4">Monitores</th>
                        <th class="px-6 py-4 text-center">Kit Locado?</th>
                        <th class="px-6 py-4 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($equipamentos as $eq)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-emerald-600 dark:text-emerald-400 font-bold border border-slate-200 dark:border-slate-700 shrink-0">
                                        @if($eq->tipo === 'notebook') 💻 @else 🖥️ @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-bold text-slate-900 dark:text-slate-100 block uppercase tracking-wide text-xs">{{ $eq->tipo }}</span>
                                            @if(($eq->tipo_desempenho ?? 'administrativo') === 'avancado')
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20" title="Desempenho Avançado">
                                                    Avançado
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20" title="Desempenho Administrativo">
                                                    Admin
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-[11px] text-slate-400 dark:text-slate-500 font-mono">Resp: {{ $eq->responsavel_levantamento ?? 'N/I' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($eq->local)
                                    <div class="flex flex-col gap-1 items-start">
                                        @if($eq->local->secretaria)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                                {{ $eq->local->secretaria->secretaria }}
                                            </span>
                                        @endif
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 shadow-xs">
                                            <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            {{ $eq->local->local }}
                                        </span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                        N/A
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-emerald-600 dark:text-emerald-400 font-bold block">{{ $eq->serial }}</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $eq->marca_modelo ?? 'Marca não especificada' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($eq->monitores->count() > 0)
                                    <div class="flex flex-wrap gap-1 items-center">
                                        @foreach($eq->monitores as $m)
                                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-[11px] font-mono text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                                <span class="text-emerald-600 dark:text-emerald-400 font-bold">#{{ $m->numero }}:</span>
                                                @if($m->marca_modelo)
                                                    <span class="font-sans font-medium text-slate-900 dark:text-slate-100">{{ $m->marca_modelo }}</span>
                                                @endif
                                                <span class="text-slate-500 dark:text-slate-400">({{ $m->serial }})</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 dark:text-slate-500 italic">Sem monitores</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($eq->kit_teclado_mouse_locado)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        Sim (Locado)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                        Não
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="verHistorico({{ $eq->id }})" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold transition cursor-pointer">
                                        Histórico
                                    </button>
                                    @can('update', $eq)
                                        <button wire:click="editarEquipamento({{ $eq->id }})" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-semibold transition border border-emerald-500/20 cursor-pointer">
                                            Editar
                                        </button>
                                    @endcan
                                    @if(auth()->user()->isDiretor())
                                        <button wire:click="excluirEquipamento({{ $eq->id }})"
                                                wire:confirm="Mover este equipamento para a Lixeira?"
                                                class="px-3 py-1.5 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-600 dark:text-red-400 text-xs font-semibold transition border border-red-500/20 cursor-pointer">
                                            Excluir
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                Nenhum equipamento cadastrado ou encontrado na busca.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Stacked Cards view -->
        <div class="block lg:hidden divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($equipamentos as $eq)
                <div class="p-5 space-y-3.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase text-emerald-600 dark:text-emerald-400">
                                @if($eq->tipo === 'notebook') 💻 Notebook @else 🖥️ Desktop @endif
                            </span>
                            @if(($eq->tipo_desempenho ?? 'administrativo') === 'avancado')
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                    Avançado
                                </span>
                            @else
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20">
                                    Admin
                                </span>
                            @endif
                        </div>
                        <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-500/10 px-2.5 py-1 rounded-lg border border-indigo-500/20 inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            {{ $eq->local ? $eq->local->local : 'N/A' }}
                        </span>
                    </div>

                    <div>
                        <span class="font-mono text-sm text-emerald-600 dark:text-emerald-400 font-bold block">Série: {{ $eq->serial }}</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400 block">{{ $eq->marca_modelo ?? 'Modelo não informado' }}</span>
                        <span class="text-[11px] text-slate-400 dark:text-slate-500 block mt-0.5">Resp. Coleta: {{ $eq->responsavel_levantamento ?? 'N/I' }}</span>
                    </div>

                    @if($eq->monitores->count() > 0)
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800/60">
                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 block mb-1">Monitores Vinculados:</span>
                            <div class="flex flex-wrap gap-1">
                                @foreach($eq->monitores as $m)
                                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-[10px] font-mono text-slate-700 dark:text-slate-300 rounded border border-slate-200 dark:border-slate-700">
                                        #{{ $m->numero }}: {{ $m->marca_modelo ? $m->marca_modelo.' ' : '' }}({{ $m->serial }})
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="pt-3 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-xs text-slate-500 dark:text-slate-400">Kit Locado: <strong class="{{ $eq->kit_teclado_mouse_locado ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' }}">{{ $eq->kit_teclado_mouse_locado ? 'Sim' : 'Não' }}</strong></span>
                        <div class="flex items-center justify-center gap-2">
                            <button wire:click="verHistorico({{ $eq->id }})" class="flex-1 sm:flex-initial px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                                Histórico
                            </button>
                            @can('update', $eq)
                                <button wire:click="editarEquipamento({{ $eq->id }})" class="flex-1 sm:flex-initial px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-semibold border border-emerald-500/20">
                                    Editar
                                </button>
                            @endcan
                            @if(auth()->user()->isDiretor())
                                <button wire:click="excluirEquipamento({{ $eq->id }})"
                                        wire:confirm="Excluir equipamento?"
                                        class="flex-1 sm:flex-initial px-3 py-1.5 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-semibold border border-red-500/20">
                                    Excluir
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-500 text-xs">
                    Nenhum equipamento cadastrado ou encontrado.
                </div>
            @endforelse
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            <flux:pagination :paginator="$equipamentos" />
        </div>
    </div>

    <!-- Modal Form (Create / Edit) -->
    @if($showModal)
    <div wire:key="modal-equipamento-{{ $equipamentoId ?? 'novo' }}" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-2xl overflow-hidden shadow-2xl flex flex-col max-h-[90vh]">
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/80 dark:bg-slate-900/80 sticky top-0">
                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">
                    {{ $equipamentoId ? 'Editar Equipamento' : 'Cadastrar Novo Equipamento' }}
                </h3>
            </div>

            <!-- Modal Body -->
            <form wire:submit="salvar" class="p-6 overflow-y-auto space-y-5 flex-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Secretaria *</label>
                        <flux:select wire:model.live="secretaria_id" variant="combobox" placeholder="Selecione a Secretaria..." :disabled="auth()->user()->isCoordenador()">
                            <flux:select.option value="">Selecione a Secretaria...</flux:select.option>
                            @foreach($secretarias as $s)
                                <flux:select.option value="{{ $s->id_secretarias }}">{{ $s->secretaria }} — {{ $s->nome_extenso }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        @error('secretaria_id') <span class="text-xs text-red-500 dark:text-red-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Local de Alocação *</label>
                        <flux:select wire:model.live="setor_id" variant="combobox" placeholder="Selecione o Local...">
                            <flux:select.option value="">Selecione o Local...</flux:select.option>
                            @foreach($modalLocais as $l)
                                <flux:select.option value="{{ $l->id_local }}">{{ $l->local }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        @error('setor_id') <span class="text-xs text-red-500 dark:text-red-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Tipo de Equipamento *</label>
                        <flux:select wire:model="tipo" variant="select" placeholder="Selecione o Tipo...">
                            <flux:select.option value="desktop">🖥️ Desktop</flux:select.option>
                            <flux:select.option value="notebook">💻 Notebook</flux:select.option>
                        </flux:select>
                        @error('tipo') <span class="text-xs text-red-500 dark:text-red-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Tipo de Desempenho *</label>
                        <flux:select wire:model="tipo_desempenho" variant="select" placeholder="Selecione o Desempenho...">
                            <flux:select.option value="administrativo">Administrativo</flux:select.option>
                            <flux:select.option value="avancado">Avançado</flux:select.option>
                        </flux:select>
                        @error('tipo_desempenho') <span class="text-xs text-red-500 dark:text-red-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="eq-serial-input" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Número de Série (Serial) *</label>
                        <input id="eq-serial-input" wire:model="serial" type="text" placeholder="Ex: BRJ123456"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50 mb-2">

                        <div class="flex items-center gap-2">
                            <!-- Opção 1: Cód. de Barra -->
                            <button type="button"
                                    onclick="escanearCodigoBarras('eq-serial-input')"
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
                                    onclick="lerTextoOCR('eq-serial-input')"
                                    title="Ler Números por Foto"
                                    class="flex-1 px-3 py-2 rounded-xl bg-teal-500/10 hover:bg-teal-500/20 text-teal-600 dark:text-teal-400 border border-teal-500/20 font-bold text-xs flex items-center justify-center gap-1.5 transition cursor-pointer active:scale-95">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Foto OCR</span>
                            </button>
                        </div>
                        @error('serial') <span class="text-xs text-red-500 dark:text-red-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Marca & Modelo *</label>
                        <input wire:model="marca_modelo" type="text" placeholder="Ex: Dell OptiPlex 3080"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                        @error('marca_modelo') <span class="text-xs text-red-500 dark:text-red-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Técnico / Responsável Coleta *</label>
                        <input wire:model="responsavel_levantamento" type="text" placeholder="Nome do técnico da TI"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                        @error('responsavel_levantamento') <span class="text-xs text-red-500 dark:text-red-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4">
                        <flux:checkbox wire:model="kit_teclado_mouse_locado" accent="emerald" label="Kit Teclado & Mouse Locado" description="Possui periféricos de contrato de locação" />
                    </div>
                </div>

                <!-- Dynamic Monitores Section (1:N) -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Monitores Conectados</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Adicione um ou múltiplos monitores para este computador</p>
                        </div>
                        <button type="button" wire:click="adicionarMonitor" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-emerald-600 dark:text-emerald-400 text-xs font-bold flex items-center gap-1.5 transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Adicionar Monitor
                        </button>
                    </div>

                    @if(count($monitores) > 0)
                        <div class="space-y-3">
                            @foreach($monitores as $index => $mon)
                                <div class="bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 p-4 rounded-xl space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-emerald-600 dark:text-emerald-400 font-bold text-xs">
                                            🖥️ Monitor #{{ $mon['numero'] }}
                                        </span>
                                        <button type="button" wire:click="removerMonitor({{ $index }})" class="p-1.5 text-red-500 dark:text-red-400 hover:bg-red-500/10 rounded-lg transition cursor-pointer flex items-center gap-1 text-xs font-semibold">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            <span>Remover</span>
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Marca & Modelo *</label>
                                            <input wire:model="monitores.{{ $index }}.marca_modelo" type="text" placeholder="Ex: Dell P2419H / LG 24MK430H"
                                                   class="w-full px-3 py-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                                            @error("monitores.{$index}.marca_modelo")
                                                <span class="text-[11px] text-red-500 dark:text-red-400 block mt-0.5 font-medium">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Número de Série (Serial) *</label>
                                            <input id="mon-serial-input-{{ $index }}" wire:model="monitores.{{ $index }}.serial" type="text" placeholder="Número de Série do Monitor *"
                                                   class="w-full px-3 py-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 font-mono text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/50 mb-2">

                                            <div class="flex items-center gap-2">
                                                <!-- Opção 1: Cód. de Barra -->
                                                <button type="button"
                                                        onclick="escanearCodigoBarras('mon-serial-input-{{ $index }}')"
                                                        title="Escanear Cód. de Barra"
                                                        class="flex-1 px-2.5 py-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 font-bold text-xs flex items-center justify-center gap-1 transition cursor-pointer active:scale-95">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    <span>Cód. de Barra</span>
                                                </button>

                                                <!-- Opção 2: Foto OCR -->
                                                <button type="button"
                                                        onclick="lerTextoOCR('mon-serial-input-{{ $index }}')"
                                                        title="Ler Números por Foto"
                                                        class="flex-1 px-2.5 py-1.5 rounded-lg bg-teal-500/10 hover:bg-teal-500/20 text-teal-600 dark:text-teal-400 border border-teal-500/20 font-bold text-xs flex items-center justify-center gap-1 transition cursor-pointer active:scale-95">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    <span>Foto OCR</span>
                                                </button>
                                            </div>
                                            @error("monitores.{$index}.serial")
                                                <span class="text-[11px] text-red-500 dark:text-red-400 block mt-0.5 font-medium">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 rounded-xl border border-dashed border-slate-300 dark:border-slate-800 text-center text-xs text-slate-500">
                            Nenhum monitor adicionado ainda. Clique em "Adicionar Monitor" acima.
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-sm cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold text-sm shadow-lg shadow-emerald-500/20">
                        {{ $equipamentoId ? 'Atualizar Equipamento' : 'Salvar Equipamento' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
