<div class="space-y-6">
    <x-slot name="header">Setores & Secretarias</x-slot>

    @livewire('historico-modal')

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xl">
        <div class="relative w-full sm:w-80">
            <input wire:model.live.debounce.300ms="search" type="text" 
                   placeholder="Buscar setor/secretaria..." 
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-sm placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500">
            <svg class="w-5 h-5 text-slate-400 dark:text-slate-500 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>

        <button wire:click="novoSetor" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 font-bold text-slate-950 text-sm transition shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2 cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Novo Setor
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
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-950/60 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Nome do Setor / Secretaria</th>
                        <th class="px-6 py-4 text-center">Equipamentos</th>
                        <th class="px-6 py-4 text-center">Periféricos</th>
                        <th class="px-6 py-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($setores as $setor)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4 font-mono text-slate-400 dark:text-slate-500 text-xs">#{{ $setor->id }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900 dark:text-slate-100">{{ $setor->nome }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 border border-slate-200 dark:border-slate-700">
                                    {{ $setor->equipamentos_count }} pc(s)
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-teal-600 dark:text-teal-400 border border-slate-200 dark:border-slate-700">
                                    {{ $setor->perifericos_count }} item(ns)
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2.5">
                                    <button wire:click="verHistorico({{ $setor->id }})" title="Linha do Tempo" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold transition cursor-pointer">
                                        Histórico
                                    </button>
                                    <button wire:click="editarSetor({{ $setor->id }})" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-semibold transition border border-emerald-500/20 cursor-pointer">
                                        Editar
                                    </button>
                                    @if(auth()->user()->isDiretor())
                                        <button wire:click="excluirSetor({{ $setor->id }})" 
                                                wire:confirm="Deseja mover o setor '{{ $setor->nome }}' para a Lixeira?"
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
                                Nenhum setor encontrado com os critérios digitados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Stacked Cards view -->
        <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($setores as $setor)
                <div class="p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-xs text-slate-400">#{{ $setor->id }}</span>
                        <div class="flex gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 border border-slate-200 dark:border-slate-700">{{ $setor->equipamentos_count }} PCs</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-teal-600 dark:text-teal-400 border border-slate-200 dark:border-slate-700">{{ $setor->perifericos_count }} Perif.</span>
                        </div>
                    </div>
                    <h4 class="text-base font-bold text-slate-900 dark:text-slate-100">{{ $setor->nome }}</h4>
                    <div class="pt-2 flex flex-wrap items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-800/60">
                        <button wire:click="verHistorico({{ $setor->id }})" class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                            Histórico
                        </button>
                        <button wire:click="editarSetor({{ $setor->id }})" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-semibold border border-emerald-500/20">
                            Editar
                        </button>
                        @if(auth()->user()->isDiretor())
                            <button wire:click="excluirSetor({{ $setor->id }})" 
                                    wire:confirm="Mover setor para a Lixeira?"
                                    class="px-3 py-1.5 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-semibold border border-red-500/20">
                                Excluir
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-500 text-xs">
                    Nenhum setor cadastrado.
                </div>
            @endforelse
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $setores->links() }}
        </div>
    </div>

    <!-- Modal Form (Create / Edit) -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-md p-6 shadow-2xl space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">
                    {{ $setorId ? 'Editar Setor' : 'Novo Setor / Secretaria' }}
                </h3>
            </div>

            <form wire:submit="salvar" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Nome do Setor / Secretaria</label>
                    <input wire:model="nome" type="text" placeholder="Ex: Recepção Central, TI, RH"
                           class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500">
                    @error('nome')
                        <span class="text-xs text-red-500 dark:text-red-400 mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-sm">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold text-sm shadow-lg shadow-emerald-500/20">
                        {{ $setorId ? 'Atualizar' : 'Salvar Setor' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
