<div class="space-y-6">
    <x-slot name="header">Busca Inteligente & Exportação de PDF</x-slot>

    <!-- Search input -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl shadow-xl space-y-4">
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="relative w-full">
                <input wire:model.live.debounce.300ms="search" type="text"
                       placeholder="Digite o nome de uma secretaria ou minisetor para pesquisar..."
                       class="w-full pl-12 pr-4 py-3.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-base placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                <svg class="w-6 h-6 text-slate-400 dark:text-slate-500 absolute left-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <!-- Quick Select Pills -->
        <div class="flex flex-wrap gap-2 pt-2">
            @foreach($setores as $s)
                <button wire:click="selecionarSetor({{ $s->id }})"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all border cursor-pointer {{ $selectedSetorId === $s->id ? 'bg-emerald-500 text-slate-950 border-emerald-400 shadow-md font-bold' : 'bg-slate-100 dark:bg-slate-950 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:bg-slate-200 dark:hover:bg-slate-800' }}">
                    {{ $s->nome }}
                    <span class="ml-1 opacity-70">({{ $s->equipamentos_count }} PCs)</span>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Selected Setor Details Card -->
    @if($selectedSetor)
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xl space-y-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-slate-100 dark:border-slate-800 pb-5 gap-4">
                <div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Secretaria / Setor Selecionado</span>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 mt-1">{{ $selectedSetor->nome }}</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Cadastrado em {{ $selectedSetor->created_at->format('d/m/Y') }}</p>
                </div>
                <a href="{{ route('relatorios.pdf', $selectedSetor) }}" target="_blank" class="px-5 py-3 rounded-xl bg-linear-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 font-bold text-slate-950 text-sm transition shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Gerar Relatório em PDF
                </a>
            </div>

            <!-- Computadores do Setor -->
            <div class="space-y-4">
                <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
                    Computadores Instalados ({{ $selectedSetor->equipamentos->count() }})
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($selectedSetor->equipamentos as $eq)
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

                            @if($eq->monitores->count() > 0)
                                <div class="pt-2 border-t border-slate-200 dark:border-slate-800">
                                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 block mb-1">Monitores Conectados ({{ $eq->monitores->count() }}):</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($eq->monitores as $m)
                                            <span class="px-2 py-0.5 bg-white dark:bg-slate-900 font-mono text-[10px] text-teal-600 dark:text-teal-300 rounded border border-slate-200 dark:border-slate-800">
                                                #{{ $m->numero }}: {{ $m->serial }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-2 p-6 text-center text-slate-500 text-xs bg-slate-50 dark:bg-slate-950/40 rounded-xl border border-dashed border-slate-200 dark:border-slate-800">
                            Nenhum computador cadastrado neste setor.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Periféricos do Setor -->
            <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyan-500 dark:bg-cyan-400"></span>
                    Periféricos Avulsos Mapeados ({{ $selectedSetor->perifericos->count() }})
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @forelse($selectedSetor->perifericos as $per)
                        <div class="bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl p-4 space-y-1">
                            <h4 class="font-bold text-slate-900 dark:text-slate-100 text-sm">{{ $per->tipo }}</h4>
                            <p class="font-mono text-xs text-emerald-600 dark:text-emerald-400">Patrimônio: {{ $per->serial_patrimonio ?? 'S/N' }}</p>
                            @if($per->observacoes)
                                <p class="text-xs text-slate-500 dark:text-slate-400 italic pt-1">{{ $per->observacoes }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-3 p-6 text-center text-slate-500 text-xs bg-slate-50 dark:bg-slate-950/40 rounded-xl border border-dashed border-slate-200 dark:border-slate-800">
                            Nenhum periférico cadastrado neste setor.
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
            <p class="text-sm font-semibold">Selecione ou busque uma secretaria acima para visualizar o inventário e exportar o relatório PDF.</p>
        </div>
    @endif
</div>
