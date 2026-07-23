@extends('layouts.app')

@section('title', $acolhimento->nome_pessoa)
@section('subtitle', 'Dossiê do Acolhido')

@section('content')
<div class="w-full max-w-[1600px] mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('acolhimentos.index') }}" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <div class="flex items-center space-x-2 flex-wrap gap-2">
                    <h2 class="text-2xl font-bold text-slate-850 dark:text-slate-100">{{ $acolhimento->nome_pessoa }}</h2>
                    @if($acolhimento->oculto === 's')
                        <span class="text-xs font-bold uppercase bg-red-100 text-red-700 dark:bg-red-950/30 dark:text-red-450 px-2.5 py-0.5 rounded-full select-none">
                            Ocultado da Listagem
                        </span>
                    @endif
                </div>
                @if($acolhimento->nome_social)
                    <p class="text-sm text-emerald-600 dark:text-emerald-450 italic">Nome Social: {{ $acolhimento->nome_social }}</p>
                @else
                    <p class="text-sm text-slate-500 dark:text-slate-400">Cadastrado em {{ $acolhimento->dt_cadastro ? $acolhimento->dt_cadastro->format('d/m/Y') : '-' }}</p>
                @endif
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('acolhimentos.pdf', $acolhimento->id_acolhimento) }}" target="_blank"
               class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white font-semibold rounded-xl text-sm transition-all duration-200 cursor-pointer shadow-sm active:scale-[0.98] flex items-center space-x-1">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Gerar PDF</span>
            </a>

            @can('edit-data')
                @if($acolhimento->oculto === 's')
                    <form action="{{ route('acolhimentos.toggle-oculto', $acolhimento->id_acolhimento) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white font-semibold rounded-xl text-sm transition-all duration-200 cursor-pointer shadow-sm active:scale-[0.98]">
                            Exibir na Listagem
                        </button>
                    </form>
                @else
                    <form action="{{ route('acolhimentos.toggle-oculto', $acolhimento->id_acolhimento) }}" method="POST" class="inline"
                          onsubmit="return confirm('Tem certeza de que deseja ocultar este acolhido da listagem padrão? Ele ainda poderá ser localizado através de pesquisa direta.')">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white font-semibold rounded-xl text-sm transition-all duration-200 cursor-pointer shadow-sm active:scale-[0.98]">
                            Ocultar da Listagem
                        </button>
                    </form>
                @endif
                <a href="{{ route('acolhimentos.edit', $acolhimento->id_acolhimento) }}"
                   class="px-4 py-2.5 bg-slate-850 hover:bg-slate-750 dark:bg-slate-700 dark:hover:bg-slate-650 text-white font-semibold rounded-xl text-sm transition-all duration-200 cursor-pointer">
                    Editar Cadastro
                </a>
            @endcan
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- LEFT COLUMN: FOTO E RESUMO -->
        <div class="space-y-6">

            <!-- Foto Card -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-250/50 dark:border-slate-800/50 flex flex-col items-center">
                <!-- Avatar / Photo Display -->
                <div class="relative w-40 h-40 mb-4 group">
                    <div class="w-full h-full rounded-full overflow-hidden border-2 border-slate-200 dark:border-slate-750 shadow-md">
                        @if($acolhimento->nome_cript)
                            <img id="profile-photo" src="{{ asset('storage/fotos/' . $acolhimento->nome_cript) }}" alt="{{ $acolhimento->nome_pessoa }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div id="profile-photo-placeholder" class="w-full h-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <img id="profile-photo" class="w-full h-full object-cover hidden">
                        @endif
                    </div>
                </div>
            </div>

            <!-- Resumo Cadastral -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-250/50 dark:border-slate-800/50 space-y-4">
                <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Identificação Geral</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <div class="text-xs text-slate-400">CPF</div>
                        <div class="font-mono text-slate-800 dark:text-slate-250 font-semibold">{{ $acolhimento->masked_cpf }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400">RG</div>
                        <div class="text-slate-800 dark:text-slate-250 font-semibold">{{ $acolhimento->masked_rg ?: '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400">Data de Nascimento</div>
                        <div class="text-slate-800 dark:text-slate-250 font-semibold">
                            {{ $acolhimento->dt_nascimento ? $acolhimento->dt_nascimento->format('d/m/Y') : '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400">Naturalidade / UF</div>
                        <div class="text-slate-800 dark:text-slate-250 font-semibold">
                            {{ $acolhimento->naturalidade ?? '-' }} {{ $acolhimento->estado_nasc ? '/ ' . $acolhimento->estado_nasc : '' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400">Técnico Responsável</div>
                        <div class="text-slate-800 dark:text-slate-250 font-semibold">
                            {{ $acolhimento->tecnicoResponsavel->nome_usu ?? 'Não informado' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400">Última Alteração por</div>
                        <div class="text-slate-800 dark:text-slate-250 font-semibold">
                            {{ $acolhimento->usuarioAlteracao->nome_usu ?? 'Não informado' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: ABAS DE DETALHES, EVOLUÇÕES E ANEXOS -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Tabs Navigation -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-250/50 dark:border-slate-800/50 p-2 flex space-x-1">
                <button type="button" onclick="switchTab('detalhes')" id="tab-btn-detalhes"
                    class="flex-1 py-2.5 px-4 rounded-xl text-sm font-semibold transition-all duration-200 tab-btn cursor-pointer bg-slate-100 dark:bg-slate-850 text-emerald-600 dark:text-emerald-400">
                    Detalhes do Cadastro
                </button>
                <button type="button" onclick="switchTab('evolucao')" id="tab-btn-evolucao"
                    class="flex-1 py-2.5 px-4 rounded-xl text-sm font-semibold transition-all duration-200 tab-btn cursor-pointer text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-250">
                    Histórico & Evoluções ({{ $observacoes->count() }})
                </button>
                <button type="button" onclick="switchTab('anexos')" id="tab-btn-anexos"
                    class="flex-1 py-2.5 px-4 rounded-xl text-sm font-semibold transition-all duration-200 tab-btn cursor-pointer text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-250">
                    Documentos ({{ $arquivos->count() }})
                </button>
            </div>

            <!-- TAB CONTENT: DETALHES -->
            <div id="tab-content-detalhes" class="tab-pane space-y-6">
                <!-- Saúde & Benefícios -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-250/50 dark:border-slate-800/50 space-y-4">
                    <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider pb-2 border-b border-slate-100 dark:border-slate-850">
                        Saúde e Assistência Social
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div class="bg-slate-50/50 dark:bg-slate-950/20 p-4 rounded-xl border border-slate-100 dark:border-slate-850">
                            <div class="text-xs text-slate-400">Necessidade Especial?</div>
                            <div class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $acolhimento->nec_especial ?? 'Não' }}</div>
                            @if($acolhimento->tipo_nec_especial)
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $acolhimento->tipo_nec_especial }}</div>
                            @endif
                        </div>

                        <div class="bg-slate-50/50 dark:bg-slate-950/20 p-4 rounded-xl border border-slate-100 dark:border-slate-850">
                            <div class="text-xs text-slate-400">Dependência Química?</div>
                            <div class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $acolhimento->depend_quimica ?? 'Não' }}</div>
                            @if($acolhimento->tipo_dep_quimica)
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $acolhimento->tipo_dep_quimica }}</div>
                            @endif
                        </div>

                        <div class="bg-slate-50/50 dark:bg-slate-950/20 p-4 rounded-xl border border-slate-100 dark:border-slate-850">
                            <div class="text-xs text-slate-400">Transtorno Mental?</div>
                            <div class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $acolhimento->transtorno ?? 'Não' }}</div>
                            @if($acolhimento->tipo_transtorno)
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $acolhimento->tipo_transtorno }}</div>
                            @endif
                        </div>

                        <div class="bg-slate-50/50 dark:bg-slate-950/20 p-4 rounded-xl border border-slate-100 dark:border-slate-850">
                            <div class="text-xs text-slate-400">Recebe Benefício?</div>
                            <div class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $acolhimento->recebe_beneficio ?? 'Não' }}</div>
                            @if($acolhimento->tipo_beneficio)
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $acolhimento->tipo_beneficio }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Família e Contatos -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-250/50 dark:border-slate-800/50 space-y-4">
                    <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider pb-2 border-b border-slate-100 dark:border-slate-850">
                        Família e Referência Familiar
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-4">
                        <div>
                            <div class="text-xs text-slate-400">Nome da Mãe</div>
                            <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $acolhimento->mae ?? 'Não informado' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400">Nome do Pai</div>
                            <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $acolhimento->pai ?? 'Não informado' }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <!-- Parente 1 -->
                        @if($acolhimento->parente_nome)
                            <div class="p-4 bg-slate-50 dark:bg-slate-950 rounded-xl space-y-1 text-sm border border-slate-100 dark:border-slate-850">
                                <h4 class="text-xs font-bold text-slate-400 uppercase">Contato Familiar</h4>
                                <div><span class="text-slate-400 text-xs">Parente:</span> <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $acolhimento->parente_nome }}</span></div>
                                <div><span class="text-slate-400 text-xs">Parentesco:</span> <span class="text-slate-800 dark:text-slate-200">{{ $acolhimento->parente_grau }}</span></div>
                                <div><span class="text-slate-400 text-xs">Endereço/Tel:</span> <span class="text-slate-850 dark:text-slate-300">{{ $acolhimento->parente_end }}</span></div>
                            </div>
                        @endif

                        <!-- Parente 2 -->
                        @if($acolhimento->parente_grau1)
                            <div class="p-4 bg-slate-50 dark:bg-slate-950 rounded-xl space-y-1 text-sm border border-slate-100 dark:border-slate-850">
                                <h4 class="text-xs font-bold text-slate-400 uppercase">Contato Familiar Adicional</h4>
                                <div><span class="text-slate-400 text-xs">Parentesco:</span> <span class="text-slate-800 dark:text-slate-200">{{ $acolhimento->parente_grau1 }}</span></div>
                                <div><span class="text-slate-400 text-xs">Endereço/Tel:</span> <span class="text-slate-850 dark:text-slate-300">{{ $acolhimento->parente_end1 }}</span></div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Histórico e Situação de Rua -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-250/50 dark:border-slate-800/50 space-y-4">
                    <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider pb-2 border-b border-slate-100 dark:border-slate-850">
                        Situação de Rua e Monitoramento
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-4">
                        <div>
                            <div class="text-xs text-slate-400">Cidade/Bairro de Situação</div>
                            <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $acolhimento->cid_bairro_situacao ?? 'Não informado' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400">Monitoramento Ativo?</div>
                            <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $acolhimento->monitoramento ?? 'Não' }}</div>
                        </div>
                    </div>

                    @if($acolhimento->obs_pessoa)
                        <div class="pt-2">
                            <div class="text-xs text-slate-400 mb-1">Observações Iniciais do Prontuário</div>
                            <div class="p-4 bg-slate-50 dark:bg-slate-950 rounded-xl text-slate-700 dark:text-slate-300 text-sm whitespace-pre-line border border-slate-100 dark:border-slate-850">
                                {{ $acolhimento->obs_pessoa }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- TAB CONTENT: EVOLUÇÕES -->
            <div id="tab-content-evolucao" class="tab-pane hidden space-y-6">
                <!-- Listagem das Evoluções em Formato Timeline -->
                <div class="relative border-l border-slate-200 dark:border-slate-800 ml-4 pl-6 space-y-8 my-4">
                    @forelse ($observacoes as $obs)
                        <div class="relative animate-fade-in-up">
                            <!-- Timeline Bullet -->
                            <div class="absolute left-[-31px] top-1.5 h-4 w-4 rounded-full border-2 border-white dark:border-slate-950 {{ $obs->tipo === 's' ? 'bg-amber-500 shadow-md shadow-amber-500/30' : 'bg-emerald-500 shadow-md shadow-emerald-500/30' }}"></div>

                            <div class="p-6 bg-white dark:bg-slate-900 rounded-2xl shadow-sm border {{ $obs->tipo === 's' ? 'border-amber-500/30 bg-amber-500/5 dark:bg-amber-950/5' : 'border-slate-200/60 dark:border-slate-800/80' }} space-y-3 relative overflow-hidden hover:scale-[1.01] hover:shadow-md transition-all duration-300">
                                @if($obs->tipo === 's')
                                    <div class="absolute top-0 right-0 bg-linear-to-r from-amber-600 to-amber-500 text-white font-extrabold text-[8px] uppercase tracking-widest px-3.5 py-1.5 rounded-bl-xl">
                                        Sigiloso
                                    </div>
                                @endif

                                <div class="flex items-center justify-between text-xs text-slate-400">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-extrabold text-slate-700 dark:text-slate-300">{{ $obs->usuario->nome_usu ?? 'Sistema' }}</span>
                                        <span>•</span>
                                        <span class="font-semibold">{{ $obs->ultima_data ? $obs->ultima_data->format('d/m/Y \à\s H:i') : '-' }}</span>
                                    </div>
                                </div>
                                <div class="text-sm text-slate-800 dark:text-slate-200 whitespace-pre-line leading-relaxed">
                                    {{ $obs->descricao }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="absolute -left-4 pl-4 w-full">
                            <div class="p-8 bg-white dark:bg-slate-900 rounded-2xl border border-slate-250/50 dark:border-slate-800/50 text-center text-slate-500 dark:text-slate-450">
                                Nenhuma evolução registrada para este acolhido.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- TAB CONTENT: DOCUMENTOS -->
            <div id="tab-content-anexos" class="tab-pane hidden space-y-6">
                <!-- Listagem dos Anexos -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @forelse ($arquivos as $arq)
                        <div class="p-5 bg-white dark:bg-slate-900 rounded-2xl shadow-sm border {{ $arq->tipo === 's' ? 'border-amber-500/35 bg-amber-500/5 dark:bg-amber-950/5' : 'border-slate-200/60 dark:border-slate-800/80' }} flex flex-col justify-between space-y-4 hover:scale-[1.01] hover:shadow-md transition-all duration-300 relative group">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="h-10 w-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-lg shadow-inner">
                                        📄
                                    </div>
                                    @if($arq->tipo === 's')
                                        <span class="bg-linear-to-r from-amber-600 to-amber-500 text-white text-[8px] uppercase tracking-widest px-2 py-1 rounded font-extrabold shadow-sm shadow-amber-500/20">
                                            Sigiloso
                                        </span>
                                    @endif
                                </div>
                                <div class="font-bold text-slate-800 dark:text-slate-100 wrap-break-word text-sm group-hover:text-emerald-600 dark:group-hover:text-emerald-450 transition-colors">{{ $arq->nome_arquivo }}</div>
                                @if($arq->observacao)
                                    <div class="text-xs text-slate-500 dark:text-slate-450 mt-2 bg-slate-50 dark:bg-slate-950/50 p-2.5 rounded-lg border border-slate-100 dark:border-slate-850/60 italic">
                                        "{{ $arq->observacao }}"
                                    </div>
                                @endif
                            </div>

                            <div class="border-t border-slate-100 dark:border-slate-850/60 pt-3.5 flex items-center justify-between text-xs text-slate-400">
                                <div>
                                    <div class="font-semibold text-slate-550 dark:text-slate-400">Enviado por: {{ $arq->q_enviou }}</div>
                                    <div class="mt-0.5 font-medium text-slate-450">{{ $arq->data_inclusao ? $arq->data_inclusao->format('d/m/Y') : '-' }}</div>
                                </div>

                                <a href="{{ route('acolhimentos.arquivos.download', $arq->id_solicitacao_arquivo) }}"
                                   class="px-3.5 py-2 bg-slate-100 hover:bg-emerald-600 hover:text-white dark:bg-slate-800 dark:hover:bg-emerald-500 dark:hover:text-white text-slate-700 dark:text-slate-200 rounded-xl font-bold transition-all duration-200 cursor-pointer shadow-sm">
                                    Baixar
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="md:col-span-2 p-10 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/60 dark:border-slate-800/80 text-center text-slate-500 dark:text-slate-450">
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <span class="text-3xl">📂</span>
                                <span class="text-sm font-semibold">Nenhum arquivo anexado a este prontuário.</span>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Camera & Tab Script -->
<script>
    // Toggle Tabs
    function switchTab(tabId) {
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(el => {
            el.classList.remove('bg-slate-100', 'dark:bg-slate-850', 'text-emerald-600', 'dark:text-emerald-400');
            el.classList.add('text-slate-500', 'hover:text-slate-800', 'dark:text-slate-400', 'dark:hover:text-slate-250');
        });

        document.getElementById('tab-content-' + tabId).classList.remove('hidden');

        const btn = document.getElementById('tab-btn-' + tabId);
        btn.classList.remove('text-slate-500', 'hover:text-slate-800', 'dark:text-slate-400', 'dark:hover:text-slate-250');
        btn.classList.add('bg-slate-100', 'dark:bg-slate-850', 'text-emerald-600', 'dark:text-emerald-400');
    }

</script>
@endsection
