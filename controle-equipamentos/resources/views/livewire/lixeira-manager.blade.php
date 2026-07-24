<div class="space-y-6">
    <x-slot name="header">Lixeira & Restauração Segura (Exclusivo Diretor)</x-slot>

    <!-- Top Badge Info -->
    <div class="bg-linear-to-r from-red-500/10 via-red-500/5 to-white dark:from-red-950/40 dark:via-red-900/20 dark:to-slate-900 border border-red-200 dark:border-red-500/30 rounded-2xl p-5 shadow-xl flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-500/15 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0 border border-red-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-red-900 dark:text-red-200">Painel de Gestão de Lixeira (Soft Deletes)</h3>
                <p class="text-xs text-red-700/80 dark:text-red-300/80 mt-0.5">Os itens listados aqui foram desativados. Você pode restaurá-los ou expurgá-los permanentemente.</p>
            </div>
        </div>
    </div>

    @if(session()->has('message'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-sm font-semibold flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('message') }}
        </div>
    @endif

    <!-- Tabs Navigation (Responsive: 2x2 Grid on Mobile, classic horizontal tabs on Desktop) -->
    <!-- Mobile View (2x2 Grid) -->
    <div class="grid grid-cols-2 gap-2 sm:hidden bg-slate-100 dark:bg-slate-800/60 p-1.5 rounded-2xl border border-slate-200 dark:border-slate-800">
        <button wire:click="$set('activeTab', 'equipamentos')"
                class="py-2.5 px-3 text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-1.5 cursor-pointer {{ $activeTab === 'equipamentos' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}">
            <span>Computadores</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 dark:bg-slate-800">{{ $trashedEquipamentos->count() }}</span>
        </button>

        <button wire:click="$set('activeTab', 'setores')"
                class="py-2.5 px-3 text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-1.5 cursor-pointer {{ $activeTab === 'setores' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}">
            <span>Setores</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 dark:bg-slate-800">{{ $trashedSetores->count() }}</span>
        </button>

        <button wire:click="$set('activeTab', 'perifericos')"
                class="py-2.5 px-3 text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-1.5 cursor-pointer {{ $activeTab === 'perifericos' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}">
            <span>Periféricos</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 dark:bg-slate-800">{{ $trashedPerifericos->count() }}</span>
        </button>

        <button wire:click="$set('activeTab', 'monitores')"
                class="py-2.5 px-3 text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-1.5 cursor-pointer {{ $activeTab === 'monitores' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}">
            <span>Monitores</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 dark:bg-slate-800">{{ $trashedMonitores->count() }}</span>
        </button>
    </div>

    <!-- Desktop View (Tabs) -->
    <div class="hidden sm:flex border-b border-slate-200 dark:border-slate-800 space-x-4">
        <button wire:click="$set('activeTab', 'equipamentos')"
                class="py-3 px-4 text-sm font-bold border-b-2 transition-colors flex items-center gap-2 cursor-pointer {{ $activeTab === 'equipamentos' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
            Computadores ({{ $trashedEquipamentos->count() }})
        </button>
        <button wire:click="$set('activeTab', 'setores')"
                class="py-3 px-4 text-sm font-bold border-b-2 transition-colors flex items-center gap-2 cursor-pointer {{ $activeTab === 'setores' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
            Setores ({{ $trashedSetores->count() }})
        </button>
        <button wire:click="$set('activeTab', 'perifericos')"
                class="py-3 px-4 text-sm font-bold border-b-2 transition-colors flex items-center gap-2 cursor-pointer {{ $activeTab === 'perifericos' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
            Periféricos ({{ $trashedPerifericos->count() }})
        </button>
        <button wire:click="$set('activeTab', 'monitores')"
                class="py-3 px-4 text-sm font-bold border-b-2 transition-colors flex items-center gap-2 cursor-pointer {{ $activeTab === 'monitores' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
            Monitores ({{ $trashedMonitores->count() }})
        </button>
    </div>

    <!-- Tab Content Container -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl overflow-hidden">
        @if($activeTab === 'equipamentos')
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-950/60 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Equipamento</th>
                            <th class="px-6 py-4">Setor</th>
                            <th class="px-6 py-4">Serial / Modelo</th>
                            <th class="px-6 py-4">Data de Exclusão</th>
                            <th class="px-6 py-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse($trashedEquipamentos as $eq)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                <td class="px-6 py-4 font-bold text-slate-900 dark:text-slate-100 uppercase">{{ $eq->tipo }}</td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300">{{ $eq->setor->nome ?? 'N/A' }}</td>
                                <td class="px-6 py-4 font-mono text-emerald-600 dark:text-emerald-400">{{ $eq->serial }}</td>
                                <td class="px-6 py-4 text-xs font-mono text-slate-400 dark:text-slate-500">{{ $eq->deleted_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap items-center justify-end gap-2.5">
                                        <button wire:click="restaurar('App\\Models\\Equipamento', {{ $eq->id }})" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-bold border border-emerald-500/20 hover:bg-emerald-500/20 cursor-pointer">
                                            Restaurar
                                        </button>
                                        <button wire:click="abrirModalExpurgo('App\\Models\\Equipamento', {{ $eq->id }}, 'Equipamento Serial {{ $eq->serial }}')" class="px-3 py-1.5 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-bold border border-red-500/20 hover:bg-red-500/20 cursor-pointer">
                                            Expurgar Definitivamente
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Nenhum equipamento na lixeira.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Stacked Cards View -->
            <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($trashedEquipamentos as $eq)
                    <div class="p-5 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-sm text-slate-900 dark:text-slate-100 uppercase">{{ $eq->tipo }}</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">{{ $eq->setor->nome ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="font-mono text-xs text-emerald-600 dark:text-emerald-400 block font-bold">Serial: {{ $eq->serial }}</span>
                            <span class="text-[11px] text-slate-400 block">Excluído em {{ $eq->deleted_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="pt-2 flex flex-wrap items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-800">
                            <button wire:click="restaurar('App\\Models\\Equipamento', {{ $eq->id }})" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-bold border border-emerald-500/20">
                                Restaurar
                            </button>
                            <button wire:click="abrirModalExpurgo('App\\Models\\Equipamento', {{ $eq->id }}, 'Equipamento Serial {{ $eq->serial }}')" class="px-3 py-1.5 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-bold border border-red-500/20">
                                Expurgar
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-xs">Nenhum equipamento na lixeira.</div>
                @endforelse
            </div>

        @elseif($activeTab === 'setores')
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-950/60 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Nome do Setor</th>
                            <th class="px-6 py-4">Data de Exclusão</th>
                            <th class="px-6 py-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse($trashedSetores as $setor)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                <td class="px-6 py-4 font-mono text-slate-400 dark:text-slate-500">#{{ $setor->id }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900 dark:text-slate-100">{{ $setor->nome }}</td>
                                <td class="px-6 py-4 text-xs font-mono text-slate-400 dark:text-slate-500">{{ $setor->deleted_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap items-center justify-end gap-2.5">
                                        <button wire:click="restaurar('App\\Models\\Setor', {{ $setor->id }})" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-bold border border-emerald-500/20 hover:bg-emerald-500/20 cursor-pointer">
                                            Restaurar
                                        </button>
                                        <button wire:click="abrirModalExpurgo('App\\Models\\Setor', {{ $setor->id }}, 'Setor {{ $setor->nome }}')" class="px-3 py-1.5 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-bold border border-red-500/20 hover:bg-red-500/20 cursor-pointer">
                                            Expurgar Definitivamente
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-slate-500">Nenhum setor na lixeira.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Stacked Cards View -->
            <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($trashedSetores as $setor)
                    <div class="p-5 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-xs text-slate-400">#{{ $setor->id }}</span>
                            <span class="font-bold text-sm text-slate-900 dark:text-slate-100">{{ $setor->nome }}</span>
                        </div>
                        <span class="text-[11px] text-slate-400 block">Excluído em {{ $setor->deleted_at->format('d/m/Y H:i') }}</span>
                        <div class="pt-2 flex flex-wrap items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-800">
                            <button wire:click="restaurar('App\\Models\\Setor', {{ $setor->id }})" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-bold border border-emerald-500/20">
                                Restaurar
                            </button>
                            <button wire:click="abrirModalExpurgo('App\\Models\\Setor', {{ $setor->id }}, 'Setor {{ $setor->nome }}')" class="px-3 py-1.5 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-bold border border-red-500/20">
                                Expurgar
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-xs">Nenhum setor na lixeira.</div>
                @endforelse
            </div>

        @elseif($activeTab === 'perifericos')
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-950/60 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Tipo</th>
                            <th class="px-6 py-4">Setor</th>
                            <th class="px-6 py-4">Serial / Patrimônio</th>
                            <th class="px-6 py-4">Data de Exclusão</th>
                            <th class="px-6 py-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse($trashedPerifericos as $per)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                <td class="px-6 py-4 font-bold text-slate-900 dark:text-slate-100">{{ $per->tipo }}</td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300">{{ $per->setor->nome ?? 'N/A' }}</td>
                                <td class="px-6 py-4 font-mono text-emerald-600 dark:text-emerald-400">{{ $per->serial_patrimonio ?? 'S/N' }}</td>
                                <td class="px-6 py-4 text-xs font-mono text-slate-400 dark:text-slate-500">{{ $per->deleted_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap items-center justify-end gap-2.5">
                                        <button wire:click="restaurar('App\\Models\\Periferico', {{ $per->id }})" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-bold border border-emerald-500/20 hover:bg-emerald-500/20 cursor-pointer">
                                            Restaurar
                                        </button>
                                        <button wire:click="abrirModalExpurgo('App\\Models\\Periferico', {{ $per->id }}, 'Periférico {{ $per->tipo }} ({{ $per->serial_patrimonio }})')" class="px-3 py-1.5 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-bold border border-red-500/20 hover:bg-red-500/20 cursor-pointer">
                                            Expurgar Definitivamente
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Nenhum periférico na lixeira.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Stacked Cards View -->
            <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($trashedPerifericos as $per)
                    <div class="p-5 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-sm text-slate-900 dark:text-slate-100">{{ $per->tipo }}</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">{{ $per->setor->nome ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="font-mono text-xs text-emerald-600 dark:text-emerald-400 block font-bold">Serial: {{ $per->serial_patrimonio ?? 'S/N' }}</span>
                            <span class="text-[11px] text-slate-400 block">Excluído em {{ $per->deleted_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="pt-2 flex flex-wrap items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-800">
                            <button wire:click="restaurar('App\\Models\\Periferico', {{ $per->id }})" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-bold border border-emerald-500/20">
                                Restaurar
                            </button>
                            <button wire:click="abrirModalExpurgo('App\\Models\\Periferico', {{ $per->id }}, 'Periférico {{ $per->tipo }} ({{ $per->serial_patrimonio }})')" class="px-3 py-1.5 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-bold border border-red-500/20">
                                Expurgar
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-xs">Nenhum periférico na lixeira.</div>
                @endforelse
            </div>

        @elseif($activeTab === 'monitores')
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-950/60 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Número</th>
                            <th class="px-6 py-4">Serial Monitor</th>
                            <th class="px-6 py-4">Equipamento Pai</th>
                            <th class="px-6 py-4">Data de Exclusão</th>
                            <th class="px-6 py-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse($trashedMonitores as $mon)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                <td class="px-6 py-4 font-bold text-slate-900 dark:text-slate-100">#{{ $mon->numero }}</td>
                                <td class="px-6 py-4 font-mono text-emerald-600 dark:text-emerald-400">{{ $mon->serial }}</td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300">{{ $mon->equipamento ? $mon->equipamento->serial : 'N/A' }}</td>
                                <td class="px-6 py-4 text-xs font-mono text-slate-400 dark:text-slate-500">{{ $mon->deleted_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap items-center justify-end gap-2.5">
                                        <button wire:click="restaurar('App\\Models\\Monitor', {{ $mon->id }})" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-bold border border-emerald-500/20 hover:bg-emerald-500/20 cursor-pointer">
                                            Restaurar
                                        </button>
                                        <button wire:click="abrirModalExpurgo('App\\Models\\Monitor', {{ $mon->id }}, 'Monitor Serial {{ $mon->serial }}')" class="px-3 py-1.5 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-bold border border-red-500/20 hover:bg-red-500/20 cursor-pointer">
                                            Expurgar Definitivamente
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Nenhum monitor na lixeira.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Stacked Cards View -->
            <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($trashedMonitores as $mon)
                    <div class="p-5 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-sm text-slate-900 dark:text-slate-100">Monitor #{{ $mon->numero }}</span>
                            <span class="font-mono text-xs text-emerald-600 dark:text-emerald-400 font-bold">{{ $mon->serial }}</span>
                        </div>
                        <span class="text-[11px] text-slate-400 block">Excluído em {{ $mon->deleted_at->format('d/m/Y H:i') }}</span>
                        <div class="pt-2 flex flex-wrap items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-800">
                            <button wire:click="restaurar('App\\Models\\Monitor', {{ $mon->id }})" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-bold border border-emerald-500/20">
                                Restaurar
                            </button>
                            <button wire:click="abrirModalExpurgo('App\\Models\\Monitor', {{ $mon->id }}, 'Monitor Serial {{ $mon->serial }}')" class="px-3 py-1.5 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-bold border border-red-500/20">
                                Expurgar
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-xs">Nenhum monitor na lixeira.</div>
                @endforelse
            </div>
        @endif
    </div>

    <!-- Security Confirmation Modal ("CONFIRMAR") -->
    @if($showConfirmModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div class="bg-white dark:bg-slate-900 border border-red-500/40 rounded-3xl w-full max-w-md p-6 shadow-2xl space-y-5">
            <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-4 text-red-600 dark:text-red-400">
                <div class="w-10 h-10 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">Confirmação de Exclusão Definitiva</h3>
            </div>

            <div class="space-y-3 text-xs text-slate-700 dark:text-slate-300">
                <p class="font-semibold text-slate-800 dark:text-slate-200">Você está prestes a expurgar permanentemente o seguinte item do banco de dados:</p>
                <div class="p-3 rounded-xl bg-slate-100 dark:bg-slate-950 font-mono text-red-600 dark:text-red-300 font-bold border border-slate-200 dark:border-slate-800">
                    {{ $targetDescription }}
                </div>
                <p class="text-red-600 dark:text-red-400/90 font-bold">⚠️ ESTA AÇÃO NÃO PODERÁ SER DESFEITA!</p>
                <p class="text-slate-500 dark:text-slate-400">Para autorizar o expurgo definitivo, digite <span class="font-bold text-red-600 dark:text-white bg-red-500/20 px-1.5 py-0.5 rounded border border-red-500/30">CONFIRMAR</span> no campo abaixo:</p>
            </div>

            <div>
                <input wire:model="confirmInput" type="text" placeholder="Digite CONFIRMAR"
                       class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white font-mono text-center font-bold tracking-widest text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                @error('confirmInput')
                    <span class="text-xs text-red-500 dark:text-red-400 block mt-1 font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <button wire:click="$set('showConfirmModal', false)" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-sm">
                    Cancelar
                </button>
                <button wire:click="expurgarDefinitivamente" class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-sm shadow-lg shadow-red-600/30">
                    Expurgar Definitivamente
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
