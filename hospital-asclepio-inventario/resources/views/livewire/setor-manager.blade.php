<div class="space-y-6">
    <x-slot name="header">Locais & Secretarias Cadastradas</x-slot>

    @livewire('historico-modal')

    <!-- Top Action Bar & Tabs -->
    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xl">
        <form action="#" @submit.prevent="$refs.searchSetorInput.blur()" x-data class="flex items-center gap-2 w-full sm:w-auto flex-1 max-w-lg">
            <div class="relative flex-1">
                <input x-ref="searchSetorInput"
                       wire:model.live.debounce.300ms="search"
                       type="search"
                       enterkeyhint="search"
                       @keydown.enter="$el.blur()"
                       placeholder="Pesquisar local, bairro, rua ou secretaria..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-sm placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500">
                <svg class="w-5 h-5 text-slate-400 dark:text-slate-500 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <button type="submit"
                    @click="$refs.searchSetorInput.blur()"
                    class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 font-bold text-slate-950 text-xs sm:text-sm transition shadow-md shadow-emerald-500/20 shrink-0 flex items-center justify-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span>Buscar</span>
            </button>
        </form>

        <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
            <div class="flex bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700">
                <button wire:click="$set('activeTab', 'locais')" class="px-4 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer {{ $activeTab === 'locais' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-xs' : 'text-slate-600 dark:text-slate-400' }}">
                    Locais ({{ $locais->total() }})
                </button>
                <button wire:click="$set('activeTab', 'secretarias')" class="px-4 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer {{ $activeTab === 'secretarias' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-xs' : 'text-slate-600 dark:text-slate-400' }}">
                    Secretarias ({{ $secretarias->total() }})
                </button>
            </div>
        </div>
    </div>

    <!-- Info Banner -->
    <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-xs font-semibold flex items-center gap-3">
        <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span><strong>Dados Originais Preservados:</strong> O banco de dados mantém os registros originais intactos de <code>database-temporario</code>. A formatação de exibição ajusta a apresentação visual no painel.</span>
    </div>

    <!-- Content Tables -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl overflow-hidden">
        @if($activeTab === 'locais')
            <!-- Locais Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-950/60 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Nome do Local</th>
                            <th class="px-6 py-4">Secretaria Vinculada</th>
                            <th class="px-6 py-4">IP ONU</th>
                            <th class="px-6 py-4">Telefone / Ramais & Endereço</th>
                            <th class="px-6 py-4 text-center">Equipamentos</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse($locais as $loc)
                            @php
                                $rawTel = trim($loc->telefone ?? '');
                                $displayTel = null;
                                $displayRamal = null;

                                if (!empty($rawTel) && $rawTel !== '0' && $rawTel !== '0000' && $rawTel !== '88888' && $rawTel !== '12312') {
                                    if (str_contains($rawTel, 'r')) {
                                        $parts = explode('r', $rawTel);
                                        $main = trim(array_shift($parts));
                                        if (strlen($main) === 4) {
                                            $main = "3627-{$main}";
                                        }
                                        $displayTel = $main;
                                        $displayRamal = 'Ramais: ' . implode(', ', array_map('trim', $parts));
                                    } elseif (preg_match('/^3627-\d{2}-\d{2}$/', $rawTel)) {
                                        $displayTel = str_replace('-', '', substr($rawTel, 0, 7)) . '-' . substr($rawTel, 7);
                                        $displayTel = str_replace('3627', '3627-', $displayTel);
                                    } elseif (strlen($rawTel) <= 4 && is_numeric($rawTel)) {
                                        $displayTel = "Ramal {$rawTel}";
                                    } else {
                                        $displayTel = $rawTel;
                                    }
                                }

                                $rua = trim($loc->rua ?? '');
                                $bairro = trim($loc->bairro ?? '');
                                $numero = (int) ($loc->numero ?? 0);

                                $addressParts = [];
                                if (!empty($rua) && $rua !== '.' && $rua !== '123' && $rua !== 'te') {
                                    $addressParts[] = $rua . ($numero > 0 ? ", nº {$numero}" : ' (S/N)');
                                }
                                if (!empty($bairro) && $bairro !== '.' && $bairro !== '123' && $bairro !== 'te') {
                                    $addressParts[] = "Bairro " . str_replace(['Eucalpitos', 'Naçoes', 'Jardim Venesa'], ['Eucaliptos', 'Nações', 'Jardim Veneza'], $bairro);
                                }
                                $addressFull = implode(' — ', $addressParts);
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-4 font-mono text-slate-400 dark:text-slate-500 text-xs">#{{ $loc->id_local }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900 dark:text-slate-100">{{ $loc->local }}</td>
                                <td class="px-6 py-4 text-xs">
                                    @if($loc->secretaria)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 shadow-xs">
                                            <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            {{ $loc->secretaria->secretaria }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-500/10 text-slate-500 dark:text-slate-400 border border-slate-500/20">
                                            Sem Secretaria Vinculada
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono text-emerald-600 dark:text-emerald-400 text-xs font-bold">{{ (!empty($loc->ip_onu) && !in_array($loc->ip_onu, ['0', '33', '234'])) ? $loc->ip_onu : 'N/I' }}</td>
                                <td class="px-6 py-4 text-xs">
                                    @if($displayTel)
                                        <div class="flex items-center gap-1.5 font-medium text-slate-800 dark:text-slate-200">
                                            <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                            <span>{{ $displayTel }}</span>
                                            @if($displayRamal)
                                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-mono" title="Ramais adicionais">
                                                    {{ $displayRamal }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-500 italic text-[11px]">Sem telefone</span>
                                    @endif

                                    @if(!empty($addressFull))
                                        <span class="block text-slate-500 dark:text-slate-400 text-xs mt-1">{{ $addressFull }}</span>
                                    @else
                                        <span class="block text-slate-400 dark:text-slate-500 italic text-[11px] mt-1">Endereço não cadastrado</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 border border-slate-200 dark:border-slate-700">
                                        {{ $loc->equipamentos_count }} pc(s)
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    Nenhum local encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Locais Stacked Cards view -->
            <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($locais as $loc)
                    @php
                        $rawTel = trim($loc->telefone ?? '');
                        $rua = trim($loc->rua ?? '');
                        $bairro = trim($loc->bairro ?? '');
                        $numero = (int) ($loc->numero ?? 0);
                    @endphp
                    <div class="p-5 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-xs text-slate-400">#{{ $loc->id_local }}</span>
                            <span class="font-mono text-xs text-emerald-600 font-bold">IP: {{ (!empty($loc->ip_onu) && !in_array($loc->ip_onu, ['0', '33', '234'])) ? $loc->ip_onu : 'N/I' }}</span>
                        </div>
                        <h4 class="text-base font-bold text-slate-900 dark:text-slate-100">{{ $loc->local }}</h4>
                        @if($loc->secretaria)
                            <p class="text-xs font-bold text-indigo-600 dark:text-indigo-400">{{ $loc->secretaria->secretaria }}</p>
                        @endif
                        @if(!empty($rawTel) && $rawTel !== '0')
                            <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">📞 {{ $rawTel }}</p>
                        @endif
                        @if(!empty($rua) || !empty($bairro))
                            <p class="text-xs text-slate-500">{{ $rua }} {{ $numero > 0 ? ', '.$numero : '' }} {{ !empty($bairro) ? ' — Bairro '.$bairro : '' }}</p>
                        @endif
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-xs">Nenhum local cadastrado.</div>
                @endforelse
            </div>

            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                <flux:pagination :paginator="$locais" />
            </div>

        @else
            <!-- Secretarias Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-950/60 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Secretaria (Sigla)</th>
                            <th class="px-6 py-4">Nome Extenso</th>
                            <th class="px-6 py-4">Locais Pertencentes</th>
                            <th class="px-6 py-4">Secretário / Função</th>
                            <th class="px-6 py-4">Portaria / Data / Ano</th>
                            <th class="px-6 py-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse($secretarias as $sec)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-4 font-mono text-slate-400 dark:text-slate-500 text-xs">#{{ $sec->id_secretarias }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900 dark:text-slate-100">{{ preg_replace('/\s+/', ' ', trim($sec->secretaria)) }}</td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-800 dark:text-slate-200">{{ preg_replace('/\s+/', ' ', trim($sec->nome_extenso)) }}</td>
                                <td class="px-6 py-4 text-xs">
                                    @php $totalLocais = $sec->locais->count(); @endphp
                                    @if($totalLocais > 0)
                                        <div class="flex flex-wrap gap-1 max-w-xs items-center">
                                            @foreach($sec->locais->take(2) as $secLocal)
                                                <span class="px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-semibold text-[11px] border border-indigo-500/20" title="{{ $secLocal->local }}">
                                                    {{ $secLocal->local }}
                                                </span>
                                            @endforeach
                                            @if($totalLocais > 2)
                                                <span class="px-2 py-0.5 rounded bg-slate-500/10 text-slate-600 dark:text-slate-400 font-bold text-[11px] border border-slate-500/20" title="E mais {{ $totalLocais - 2 }} locais vinculados...">
                                                    +{{ $totalLocais - 2 }} ...
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-500 italic text-[11px]">Nenhum local vinculado</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-600 dark:text-slate-400">
                                    <span class="block font-bold text-slate-900 dark:text-slate-100">{{ !empty($sec->nome_secretario) ? trim($sec->nome_secretario) : 'Não informado' }}</span>
                                    <span class="block text-slate-500 italic">{{ $sec->funcao ?? '—' }}</span>
                                </td>
                                <td class="px-6 py-4 text-xs font-mono text-slate-500">
                                    <span class="block font-bold text-slate-700 dark:text-slate-300">Portaria nº {{ $sec->portaria }}</span>
                                    @if(!empty($sec->data_ext_port))
                                        <span class="block text-[11px] text-slate-400">{{ $sec->data_ext_port }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <button wire:click="abrirDetalhesSecretaria({{ $sec->id_secretarias }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold text-xs border border-emerald-500/20 transition cursor-pointer active:scale-95 shadow-xs">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Detalhes</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                    Nenhuma secretaria encontrada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Secretarias Stacked Cards View -->
            <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($secretarias as $sec)
                    <div class="p-5 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-xs text-slate-400">#{{ $sec->id_secretarias }}</span>
                            <span class="font-mono text-xs text-slate-500">Portaria {{ $sec->portaria }}</span>
                        </div>
                        <h4 class="text-base font-bold text-slate-900 dark:text-slate-100">{{ preg_replace('/\s+/', ' ', trim($sec->secretaria)) }}</h4>
                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ preg_replace('/\s+/', ' ', trim($sec->nome_extenso)) }}</p>
                        <p class="text-xs text-slate-500">Secretário: <strong class="text-slate-800 dark:text-slate-200">{{ !empty($sec->nome_secretario) ? trim($sec->nome_secretario) : 'Não informado' }}</strong></p>

                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-2">
                            <div class="text-xs flex-1 min-w-0">
                                <span class="text-slate-500 text-[11px] block">Locais pertencentes:</span>
                                @php $totalLocais = $sec->locais->count(); @endphp
                                @if($totalLocais > 0)
                                    <div class="flex flex-wrap gap-1 items-center mt-1">
                                        @foreach($sec->locais->take(2) as $secLocal)
                                            <span class="px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-semibold text-[11px]">
                                                {{ $secLocal->local }}
                                            </span>
                                        @endforeach
                                        @if($totalLocais > 2)
                                            <span class="px-2 py-0.5 rounded bg-slate-500/10 text-slate-600 dark:text-slate-400 font-bold text-[11px]">
                                                +{{ $totalLocais - 2 }} ...
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Nenhum local</span>
                                @endif
                            </div>
                            <button wire:click="abrirDetalhesSecretaria({{ $sec->id_secretarias }})"
                                    class="px-3 py-1.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold text-xs border border-emerald-500/20 transition cursor-pointer active:scale-95 shrink-0 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span>Detalhes</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-xs">Nenhuma secretaria cadastrada.</div>
                @endforelse
            </div>

            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                <flux:pagination :paginator="$secretarias" />
            </div>
        @endif
    </div>

    <!-- Modal Detalhes da Secretaria -->
    @if($showSecretariaModal && $selectedSecretaria)
        <div x-data
             @keydown.escape.window="$wire.fecharModal()"
             @click.self="$wire.fecharModal()"
             class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-slate-950/75 backdrop-blur-sm overflow-y-auto">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl w-full max-w-2xl md:max-w-3xl p-5 sm:p-7 shadow-2xl space-y-4 max-h-[85vh] flex flex-col">
                
                <!-- Modal Header -->
                <div class="flex items-start justify-between border-b border-slate-100 dark:border-slate-800 pb-3 shrink-0">
                    <div class="pr-2 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                                #{{ $selectedSecretaria->id_secretarias }}
                            </span>
                            <h3 class="text-base sm:text-xl font-bold text-slate-900 dark:text-slate-100 leading-tight">
                                {{ preg_replace('/\s+/', ' ', trim($selectedSecretaria->secretaria)) }}
                            </h3>
                        </div>
                        <p class="text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-400 mt-1">
                            {{ preg_replace('/\s+/', ' ', trim($selectedSecretaria->nome_extenso)) }}
                        </p>
                    </div>
                    <button wire:click="fecharModal" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body - General Info Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3 shrink-0">
                    <div class="p-3 sm:p-3.5 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800">
                        <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider block">Secretário</span>
                        <span class="text-xs sm:text-sm font-bold text-slate-900 dark:text-slate-100 block mt-0.5 truncate">
                            {{ !empty($selectedSecretaria->nome_secretario) ? trim($selectedSecretaria->nome_secretario) : 'Não informado' }}
                        </span>
                        <span class="text-[10px] sm:text-xs text-slate-500 italic block truncate">{{ $selectedSecretaria->funcao ?? '—' }}</span>
                    </div>

                    <div class="p-3 sm:p-3.5 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800">
                        <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider block">Portaria</span>
                        <span class="text-xs sm:text-sm font-bold text-slate-900 dark:text-slate-100 block mt-0.5 font-mono">
                            Nº {{ $selectedSecretaria->portaria }}
                        </span>
                        @if(!empty($selectedSecretaria->data_ext_port))
                            <span class="text-[10px] sm:text-xs text-slate-500 block truncate mt-0.5">{{ $selectedSecretaria->data_ext_port }}</span>
                        @endif
                    </div>

                    <div class="p-3 sm:p-3.5 rounded-xl bg-indigo-500/5 dark:bg-indigo-500/10 border border-indigo-500/20">
                        <span class="text-[10px] sm:text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block">Resumo</span>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-xs sm:text-sm font-bold text-slate-900 dark:text-slate-100">
                                {{ $selectedSecretaria->locais_count }} {{ $selectedSecretaria->locais_count === 1 ? 'Local' : 'Locais' }}
                            </span>
                            <span class="text-slate-300 dark:text-slate-700">•</span>
                            <span class="text-[11px] sm:text-xs text-slate-600 dark:text-slate-400">
                                {{ $selectedSecretaria->equipamentos_count }} pc(s)
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Section Title for Locais (Fixed above scroll area) -->
                <div class="flex items-center justify-between pt-1 pb-1 border-b border-slate-100 dark:border-slate-800 shrink-0">
                    <h4 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Todos os Locais Atribuídos ({{ $selectedSecretaria->locais->count() }})</span>
                    </h4>
                </div>

                <!-- Modal Body - Full List of Locais (Scrollable with margin top) -->
                <div class="flex-1 overflow-y-auto space-y-2 pr-1 pt-1 min-h-0">
                    @if($selectedSecretaria->locais->count() > 0)
                        <div class="space-y-2">
                            @foreach($selectedSecretaria->locais as $locModal)
                                @php
                                    $rawTel = trim($locModal->telefone ?? '');
                                    $rua = trim($locModal->rua ?? '');
                                    $bairro = trim($locModal->bairro ?? '');
                                    $numero = (int) ($locModal->numero ?? 0);

                                    $addressParts = [];
                                    if (!empty($rua) && $rua !== '.' && $rua !== '123' && $rua !== 'te') {
                                        $addressParts[] = $rua . ($numero > 0 ? ", nº {$numero}" : ' (S/N)');
                                    }
                                    if (!empty($bairro) && $bairro !== '.' && $bairro !== '123' && $bairro !== 'te') {
                                        $addressParts[] = "Bairro " . str_replace(['Eucalpitos', 'Naçoes', 'Jardim Venesa'], ['Eucaliptos', 'Nações', 'Jardim Veneza'], $bairro);
                                    }
                                    $addressFullModal = implode(' — ', $addressParts);
                                @endphp
                                <div class="p-3 sm:p-3.5 rounded-xl border border-slate-100 dark:border-slate-800/80 bg-slate-50/60 dark:bg-slate-950/50 hover:bg-slate-100/70 dark:hover:bg-slate-800/60 transition flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-3">
                                    <div class="space-y-0.5 flex-1 min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-mono text-[10px] sm:text-xs text-slate-400">#{{ $locModal->id_local }}</span>
                                            <h5 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-slate-100 truncate">{{ $locModal->local }}</h5>
                                        </div>
                                        @if(!empty($addressFullModal))
                                            <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 truncate">{{ $addressFullModal }}</p>
                                        @endif
                                        @if(!empty($rawTel) && $rawTel !== '0')
                                            <p class="text-[11px] sm:text-xs text-slate-600 dark:text-slate-300 font-medium">📞 {{ $rawTel }}</p>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0 self-start sm:self-center">
                                        @if(!empty($locModal->ip_onu) && !in_array($locModal->ip_onu, ['0', '33', '234']))
                                            <span class="px-2 py-0.5 rounded-md text-[10px] sm:text-xs font-mono font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                                IP: {{ $locModal->ip_onu }}
                                            </span>
                                        @endif
                                        <span class="px-2 py-0.5 rounded-md text-[10px] sm:text-xs font-bold bg-slate-200/70 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                            {{ $locModal->equipamentos_count }} pc(s) | {{ $locModal->perifericos_count }} perif.
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-center text-slate-500 text-xs italic">
                            Nenhum local atribuído a esta secretaria até o momento.
                        </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end shrink-0">
                    <button wire:click="fecharModal" class="px-4 sm:px-5 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs sm:text-sm transition cursor-pointer">
                        Fechar
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>
