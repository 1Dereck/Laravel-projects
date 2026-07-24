<div class="space-y-6">
    <x-slot name="header">Equipamentos (Desktops & Notebooks)</x-slot>

    @livewire('historico-modal')

    <!-- Top Action & Filter Bar -->
    <div class="flex flex-col md:flex-row gap-4 items-center justify-between bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xl">
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto flex-1">
            <div class="relative flex-1">
                <input wire:model.live.debounce.300ms="search" type="text" 
                       placeholder="Buscar por serial, marca ou responsável..." 
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-sm placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500">
                <svg class="w-5 h-5 text-slate-400 dark:text-slate-500 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <select wire:model.live="setorFilter" class="px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                <option value="">Todos os Setores</option>
                @foreach($setores as $s)
                    <option value="{{ $s->id }}">{{ $s->nome }}</option>
                @endforeach
            </select>
        </div>

        <button wire:click="novoEquipamento" class="w-full md:w-auto px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 font-bold text-slate-950 text-sm transition shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2 shrink-0 cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Cadastrar Equipamento
        </button>
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
                        <th class="px-6 py-4">Equipamento / Tipo</th>
                        <th class="px-6 py-4">Setor Alocado</th>
                        <th class="px-6 py-4">Série & Marca/Modelo</th>
                        <th class="px-6 py-4">Monitores (1:N)</th>
                        <th class="px-6 py-4 text-center">Kit Locado?</th>
                        <th class="px-6 py-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($equipamentos as $eq)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-emerald-600 dark:text-emerald-400 font-bold border border-slate-200 dark:border-slate-700">
                                        @if($eq->tipo === 'notebook') 💻 @else 🖥️ @endif
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 dark:text-slate-100 block uppercase tracking-wide text-xs">{{ $eq->tipo }}</span>
                                        <span class="text-[11px] text-slate-400 dark:text-slate-500 font-mono">Resp: {{ $eq->responsavel_levantamento ?? 'N/I' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-800 dark:text-slate-200">
                                {{ $eq->setor ? $eq->setor->nome : 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-emerald-600 dark:text-emerald-400 font-bold block">{{ $eq->serial }}</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $eq->marca_modelo ?? 'Marca não especificada' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($eq->monitores->count() > 0)
                                    <div class="space-y-1">
                                        @foreach($eq->monitores as $m)
                                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-[11px] font-mono text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 mr-1">
                                                <span class="text-emerald-600 dark:text-emerald-400 font-bold">#{{ $m->numero }}:</span> {{ $m->serial }}
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
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap items-center justify-end gap-2.5">
                                    <button wire:click="verHistorico({{ $eq->id }})" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold transition cursor-pointer">
                                        Histórico
                                    </button>
                                    <button wire:click="editarEquipamento({{ $eq->id }})" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-semibold transition border border-emerald-500/20 cursor-pointer">
                                        Editar
                                    </button>
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
                <div class="p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase text-emerald-600 dark:text-emerald-400">
                            @if($eq->tipo === 'notebook') 💻 Notebook @else 🖥️ Desktop @endif
                        </span>
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-700">
                            {{ $eq->setor ? $eq->setor->nome : 'N/A' }}
                        </span>
                    </div>

                    <div>
                        <span class="font-mono text-sm text-emerald-600 dark:text-emerald-400 font-bold block">Série: {{ $eq->serial }}</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400 block">{{ $eq->marca_modelo ?? 'Modelo não informado' }}</span>
                        <span class="text-[11px] text-slate-400 dark:text-slate-500 block">Resp. Coleta: {{ $eq->responsavel_levantamento ?? 'N/I' }}</span>
                    </div>

                    @if($eq->monitores->count() > 0)
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800/60">
                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 block mb-1">Monitores Vinculados:</span>
                            <div class="flex flex-wrap gap-1">
                                @foreach($eq->monitores as $m)
                                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-[10px] font-mono text-slate-700 dark:text-slate-300 rounded border border-slate-200 dark:border-slate-700">
                                        #{{ $m->numero }}: {{ $m->serial }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="pt-2 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-xs text-slate-500 dark:text-slate-400">Kit Locado: <strong class="{{ $eq->kit_teclado_mouse_locado ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' }}">{{ $eq->kit_teclado_mouse_locado ? 'Sim' : 'Não' }}</strong></span>
                        <div class="flex flex-wrap items-center gap-2.5">
                            <button wire:click="verHistorico({{ $eq->id }})" class="px-2.5 py-1 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                                Histórico
                            </button>
                            <button wire:click="editarEquipamento({{ $eq->id }})" class="px-2.5 py-1 rounded bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-semibold border border-emerald-500/20">
                                Editar
                            </button>
                            @if(auth()->user()->isDiretor())
                                <button wire:click="excluirEquipamento({{ $eq->id }})" 
                                        wire:confirm="Excluir equipamento?"
                                        class="px-2.5 py-1 rounded bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-semibold border border-red-500/20">
                                    Excluir
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-500 text-xs">
                    Nenhum equipamento cadastrado.
                </div>
            @endforelse
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $equipamentos->links() }}
        </div>
    </div>

    <!-- Modal Form (Create / Edit Equipamento with Monitores) -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-2xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/80 dark:bg-slate-900/80 sticky top-0">
                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">
                    {{ $equipamentoId ? 'Editar Equipamento' : 'Cadastrar Novo Equipamento' }}
                </h3>
                <button type="button" wire:click="$set('showModal', false)" title="Fechar formulário" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100 bg-slate-200/80 hover:bg-slate-300/80 dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-300/60 dark:border-slate-700 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Sair</span>
                </button>
            </div>

            <!-- Modal Body -->
            <form wire:submit="salvar" class="p-6 overflow-y-auto space-y-5 flex-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Setor de Alocação *</label>
                        <select wire:model="setor_id" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                            <option value="">Selecione o Setor...</option>
                            @foreach($setores as $s)
                                <option value="{{ $s->id }}">{{ $s->nome }}</option>
                            @endforeach
                        </select>
                        @error('setor_id') <span class="text-xs text-red-500 dark:text-red-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Tipo de Equipamento *</label>
                        <select wire:model="tipo" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                            <option value="desktop">🖥️ Desktop</option>
                            <option value="notebook">💻 Notebook</option>
                        </select>
                        @error('tipo') <span class="text-xs text-red-500 dark:text-red-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Número de Série (Serial) *</label>
                        <input wire:model="serial" type="text" placeholder="Ex: BRJ123456"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                        @error('serial') <span class="text-xs text-red-500 dark:text-red-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Marca & Modelo</label>
                        <input wire:model="marca_modelo" type="text" placeholder="Ex: Dell OptiPlex 3080"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                        @error('marca_modelo') <span class="text-xs text-red-500 dark:text-red-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Técnico / Responsável Coleta</label>
                        <input wire:model="responsavel_levantamento" type="text" placeholder="Nome do técnico da TI"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                    </div>

                    <div class="pt-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input wire:model="kit_teclado_mouse_locado" type="checkbox" class="w-5 h-5 rounded bg-slate-100 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-emerald-500 focus:ring-emerald-500">
                            <div>
                                <span class="block text-sm font-bold text-slate-800 dark:text-slate-200">Kit Teclado & Mouse Locado</span>
                                <span class="block text-xs text-slate-500 dark:text-slate-400">Possui periféricos de contrato de locação</span>
                            </div>
                        </label>
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
                                <div class="flex items-center gap-3 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 p-3 rounded-xl">
                                    <span class="w-8 h-8 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-emerald-600 dark:text-emerald-400 font-bold text-xs flex items-center justify-center shrink-0">
                                        #{{ $mon['numero'] }}
                                    </span>
                                    <div class="flex-1">
                                        <input wire:model="monitores.{{ $index }}.serial" type="text" placeholder="Número de Série do Monitor *"
                                               class="w-full px-3 py-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 font-mono text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                        @error("monitores.{$index}.serial")
                                            <span class="text-[11px] text-red-500 dark:text-red-400 block mt-0.5 font-medium">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <button type="button" wire:click="removerMonitor({{ $index }})" class="p-2 text-red-500 dark:text-red-400 hover:bg-red-500/10 rounded-lg transition cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
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
                        Sair / Cancelar
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
