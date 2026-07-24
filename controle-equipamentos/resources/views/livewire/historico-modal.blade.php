<div>
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-2xl max-h-[85vh] flex flex-col shadow-2xl overflow-hidden">
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/80 dark:bg-slate-900/80 sticky top-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">{{ $title }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Linha do tempo auditada de modificações</p>
                    </div>
                </div>
                <button wire:click="fechar" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Timeline Content -->
            <div class="p-6 overflow-y-auto space-y-6 flex-1">
                @forelse($activities as $activity)
                    <div class="relative pl-6 border-l-2 border-slate-200 dark:border-slate-800 space-y-2">
                        <!-- Dot indicator -->
                        <div class="absolute -left-2.25 top-0 w-4 h-4 rounded-full bg-white dark:bg-slate-900 border-2 border-emerald-500"></div>

                        <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                            <span class="font-semibold text-slate-800 dark:text-slate-200">
                                {{ $activity->causer ? $activity->causer->name : 'Sistema/Desconhecido' }}
                            </span>
                            <span>{{ $activity->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>

                        <div class="bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-4 text-xs space-y-2">
                            <p class="font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider text-[10px]">
                                Evento: {{ ucfirst($activity->description) }}
                            </p>

                            @if(!empty($activity->properties['attributes']))
                                <div class="mt-2 space-y-1">
                                    <span class="block text-slate-500 dark:text-slate-400 font-semibold">Valores Atualizados:</span>
                                    <ul class="list-disc list-inside space-y-1 font-mono text-[11px] text-slate-700 dark:text-slate-300">
                                        @foreach($activity->properties['attributes'] as $key => $val)
                                            @if($key !== 'updated_at')
                                                <li>
                                                    <span class="text-slate-500 dark:text-slate-400">{{ $key }}:</span>
                                                    <span class="text-emerald-600 dark:text-emerald-300 font-bold">{{ is_array($val) ? json_encode($val) : ($val ?? 'null') }}</span>
                                                    @if(isset($activity->properties['old'][$key]))
                                                        <span class="text-slate-400 dark:text-slate-500">(antes: {{ is_array($activity->properties['old'][$key]) ? json_encode($activity->properties['old'][$key]) : ($activity->properties['old'][$key] ?? 'null') }})</span>
                                                    @endif
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-slate-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-slate-400 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm font-semibold">Nenhum registro de auditoria encontrado para este item.</p>
                    </div>
                @endforelse
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 flex justify-end">
                <button wire:click="fechar" class="px-5 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 font-semibold text-slate-800 dark:text-slate-200 text-sm transition-colors cursor-pointer">
                    Fechar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
