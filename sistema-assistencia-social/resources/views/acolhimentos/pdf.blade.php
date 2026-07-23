<!DOCTYPE html>
<html lang="pt-BR" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dossiê - {{ $acolhimento->nome_pessoa }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #fff;
            color: #1e293b;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #fff !important;
                color: #000 !important;
                font-size: 11px;
                margin: 0 !important;
                padding: 15mm !important;
            }
            .print-border {
                border: none !important;
                box-shadow: none !important;
            }
            .print-bg {
                background-color: #f8fafc !important;
            }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            @page {
                size: auto;
                margin: 0;
            }
            /* Evita quebra de página dentro de seções importantes */
            .avoid-break {
                page-break-inside: avoid;
                break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 antialiased p-0 md:p-6">

    <!-- Top Action Bar (Screen Only) -->
    <div class="no-print max-w-4xl mx-auto mb-6 bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-850 flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <a href="{{ route('acolhimentos.show', $acolhimento->id_acolhimento) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-250 font-semibold rounded-xl text-sm transition-all duration-200">
                &larr; Voltar para o Dossiê
            </a>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white font-semibold rounded-xl text-sm transition-all duration-200 cursor-pointer shadow-sm shadow-emerald-500/10">
                Imprimir / Salvar como PDF
            </button>
        </div>
    </div>

    <!-- Printable Area Wrapper -->
    <div class="max-w-4xl mx-auto bg-white p-6 md:p-8 md:rounded-3xl md:shadow-md border border-transparent md:border-slate-200/60 print-border">

        <!-- Header (Logo only) -->
        <div class="flex items-center justify-center border-b-2 border-slate-200 pb-6 mb-6">
            <img src="{{ asset('images/icon-acolhimento.png') }}" alt="Logo Acolhimento" class="h-20 w-auto object-contain">
        </div>

        <!-- Main Info Container -->
        <div class="grid grid-cols-4 gap-6 items-start mb-6">

            <!-- Foto do Acolhido -->
            <div class="col-span-1 flex flex-col items-center">
                <div class="w-32 h-32 rounded-xl overflow-hidden border-2 border-slate-200 shadow-sm print-border">
                    @if($acolhimento->nome_cript)
                        <img src="{{ asset('storage/fotos/' . $acolhimento->nome_cript) }}" alt="Foto do Acolhido" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-450">
                            <span class="text-4xl">👤</span>
                        </div>
                    @endif
                </div>
                <div class="text-[10px] text-slate-400 mt-2 text-center">Foto Cadastral</div>
            </div>

            <!-- Dados Principais -->
            <div class="col-span-3 grid grid-cols-2 gap-4 text-xs">
                <div class="col-span-2">
                    <span class="block text-[10px] text-slate-400 font-bold uppercase">Nome Civil</span>
                    <span class="text-sm font-bold text-slate-900">{{ $acolhimento->nome_pessoa }}</span>
                </div>

                @if($acolhimento->nome_social)
                <div class="col-span-2">
                    <span class="block text-[10px] text-slate-400 font-bold uppercase">Nome Social</span>
                    <span class="text-sm font-bold text-emerald-700 italic">{{ $acolhimento->nome_social }}</span>
                </div>
                @endif

                <div>
                    <span class="block text-[10px] text-slate-400 font-bold uppercase">CPF</span>
                    <span class="font-mono text-slate-800 font-bold">{{ $acolhimento->masked_cpf }}</span>
                </div>

                <div>
                    <span class="block text-[10px] text-slate-400 font-bold uppercase">RG</span>
                    <span class="font-semibold text-slate-850">{{ $acolhimento->masked_rg ?: 'Não Informado' }}</span>
                </div>

                <div>
                    <span class="block text-[10px] text-slate-400 font-bold uppercase">Data de Nascimento</span>
                    <span class="font-semibold text-slate-800">
                        {{ $acolhimento->dt_nascimento ? $acolhimento->dt_nascimento->format('d/m/Y') : 'Não Informada' }}
                        @if($acolhimento->dt_nascimento)
                            ({{ $acolhimento->dt_nascimento->age }} anos)
                        @endif
                    </span>
                </div>

                <div>
                    <span class="block text-[10px] text-slate-400 font-bold uppercase">Naturalidade / UF</span>
                    <span class="font-semibold text-slate-800">
                        {{ $acolhimento->naturalidade ?? '-' }} {{ $acolhimento->estado_nasc ? '/ ' . $acolhimento->estado_nasc : '' }}
                    </span>
                </div>

                <div>
                    <span class="block text-[10px] text-slate-400 font-bold uppercase">Data de Cadastro</span>
                    <span class="font-semibold text-slate-800">{{ $acolhimento->dt_cadastro ? $acolhimento->dt_cadastro->format('d/m/Y') : '-' }}</span>
                </div>

                <div>
                    <span class="block text-[10px] text-slate-400 font-bold uppercase">Técnico Responsável</span>
                    <span class="font-semibold text-slate-800">{{ $acolhimento->tecnicoResponsavel->nome_usu ?? 'Não Informado' }}</span>
                </div>
            </div>
        </div>

        <!-- Saúde e Assistência Social -->
        <div class="mb-6 avoid-break">
            <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 border-b border-slate-200 pb-1">
                Saúde e Assistência Social
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div class="p-2 bg-slate-50 rounded-lg print-bg print-border">
                    <span class="block text-[9px] text-slate-400 font-bold uppercase">Necessidade Especial</span>
                    <span class="font-bold text-slate-800">{{ $acolhimento->nec_especial ?? 'Não' }}</span>
                    @if($acolhimento->tipo_nec_especial)
                        <span class="block text-[10px] text-slate-550 mt-0.5">{{ $acolhimento->tipo_nec_especial }}</span>
                    @endif
                </div>

                <div class="p-2 bg-slate-50 rounded-lg print-bg print-border">
                    <span class="block text-[9px] text-slate-400 font-bold uppercase">Dependência Química</span>
                    <span class="font-bold text-slate-800">{{ $acolhimento->depend_quimica ?? 'Não' }}</span>
                    @if($acolhimento->tipo_dep_quimica)
                        <span class="block text-[10px] text-slate-550 mt-0.5">{{ $acolhimento->tipo_dep_quimica }}</span>
                    @endif
                </div>

                <div class="p-2 bg-slate-50 rounded-lg print-bg print-border">
                    <span class="block text-[9px] text-slate-400 font-bold uppercase">Transtorno Mental</span>
                    <span class="font-bold text-slate-800">{{ $acolhimento->transtorno ?? 'Não' }}</span>
                    @if($acolhimento->tipo_transtorno)
                        <span class="block text-[10px] text-slate-550 mt-0.5">{{ $acolhimento->tipo_transtorno }}</span>
                    @endif
                </div>

                <div class="p-2 bg-slate-50 rounded-lg print-bg print-border">
                    <span class="block text-[9px] text-slate-400 font-bold uppercase">Recebe Benefício</span>
                    <span class="font-bold text-slate-800">{{ $acolhimento->recebe_beneficio ?? 'Não' }}</span>
                    @if($acolhimento->tipo_beneficio)
                        <span class="block text-[10px] text-slate-550 mt-0.5">{{ $acolhimento->tipo_beneficio }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Família e Referência Familiar -->
        <div class="mb-6 avoid-break">
            <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 border-b border-slate-200 pb-1">
                Afiliação e Contatos Familiares
            </h2>
            <div class="grid grid-cols-2 gap-4 text-xs mb-3">
                <div>
                    <span class="block text-[10px] text-slate-400 font-bold uppercase">Nome da Mãe</span>
                    <span class="font-semibold text-slate-800">{{ $acolhimento->mae ?? 'Não informado' }}</span>
                </div>
                <div>
                    <span class="block text-[10px] text-slate-400 font-bold uppercase">Nome do Pai</span>
                    <span class="font-semibold text-slate-800">{{ $acolhimento->pai ?? 'Não informado' }}</span>
                </div>
            </div>

            @if($acolhimento->parente_nome || $acolhimento->parente_grau1)
            <div class="grid grid-cols-2 gap-3 text-xs">
                @if($acolhimento->parente_nome)
                <div class="p-3 bg-slate-50 rounded-lg print-bg print-border">
                    <span class="block text-[9px] text-slate-450 font-bold uppercase mb-1">Referência Familiar Principal</span>
                    <div class="mb-0.5"><span class="text-slate-400 font-medium">Nome:</span> <span class="font-semibold text-slate-800">{{ $acolhimento->parente_nome }}</span></div>
                    <div class="mb-0.5"><span class="text-slate-400 font-medium">Parentesco:</span> <span class="text-slate-800 font-semibold">{{ $acolhimento->parente_grau }}</span></div>
                    <div><span class="text-slate-400 font-medium">Endereço/Tel:</span> <span class="text-slate-700 font-medium">{{ $acolhimento->parente_end }}</span></div>
                </div>
                @endif

                @if($acolhimento->parente_grau1)
                <div class="p-3 bg-slate-50 rounded-lg print-bg print-border">
                    <span class="block text-[9px] text-slate-450 font-bold uppercase mb-1">Referência Familiar Adicional</span>
                    <div class="mb-0.5"><span class="text-slate-400 font-medium">Parentesco:</span> <span class="text-slate-800 font-semibold">{{ $acolhimento->parente_grau1 }}</span></div>
                    <div><span class="text-slate-400 font-medium">Endereço/Tel:</span> <span class="text-slate-700 font-medium">{{ $acolhimento->parente_end1 }}</span></div>
                </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Situação de Rua e Observações Iniciais -->
        <div class="mb-6 avoid-break">
            <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 border-b border-slate-200 pb-1">
                Situação de Rua e Monitoramento
            </h2>
            <div class="grid grid-cols-2 gap-4 text-xs mb-3">
                <div>
                    <span class="block text-[10px] text-slate-400 font-bold uppercase">Cidade/Bairro de Situação</span>
                    <span class="font-semibold text-slate-800">{{ $acolhimento->cid_bairro_situacao ?? 'Não informado' }}</span>
                </div>
                <div>
                    <span class="block text-[10px] text-slate-400 font-bold uppercase">Monitoramento Ativo?</span>
                    <span class="font-semibold text-slate-800">{{ $acolhimento->monitoramento ?? 'Não' }}</span>
                </div>
            </div>

            @if($acolhimento->obs_pessoa)
            <div class="mt-2">
                <span class="block text-[10px] text-slate-400 font-bold uppercase mb-1">Observações Iniciais do Prontuário</span>
                <div class="p-3 bg-slate-50 rounded-lg text-slate-700 text-xs whitespace-pre-line border border-slate-150 print-bg print-border leading-relaxed">
                    {{ $acolhimento->obs_pessoa }}
                </div>
            </div>
            @endif
        </div>

        <!-- Histórico e Evoluções -->
        @if($observacoes->isNotEmpty())
        <div class="mb-6 avoid-break">
            <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 border-b border-slate-200 pb-1">
                Histórico & Evoluções
            </h2>
            <div class="space-y-3">
                @foreach ($observacoes as $obs)
                    <div class="p-3 rounded-lg border {{ $obs->tipo === 's' ? 'border-amber-300 bg-amber-50/30' : 'border-slate-200 bg-white' }} print-border">
                        <div class="flex items-center justify-between text-[10px] text-slate-400 mb-1">
                            <div>
                                <span class="font-bold text-slate-755">{{ $obs->usuario->nome_usu ?? 'Sistema' }}</span>
                                <span class="mx-1">•</span>
                                <span>{{ $obs->ultima_data ? $obs->ultima_data->format('d/m/Y H:i') : '-' }}</span>
                            </div>
                            @if($obs->tipo === 's')
                                <span class="text-[9px] font-bold text-amber-700 bg-amber-100 px-1.5 py-0.5 rounded uppercase">Sigiloso</span>
                            @endif
                        </div>
                        <div class="text-xs text-slate-800 whitespace-pre-line leading-relaxed">
                            {{ $obs->descricao }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Documentos Anexados -->
        @if($arquivos->isNotEmpty())
        <div class="mb-6 avoid-break">
            <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 border-b border-slate-200 pb-1">
                Arquivos Anexados
            </h2>
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-250 text-slate-450">
                        <th class="py-1.5 font-bold uppercase text-[10px]">Nome do Arquivo</th>
                        <th class="py-1.5 font-bold uppercase text-[10px]">Observação</th>
                        <th class="py-1.5 font-bold uppercase text-[10px] text-right">Data de Inclusão</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($arquivos as $arq)
                        <tr class="text-slate-850">
                            <td class="py-2 font-semibold">
                                {{ $arq->nome_arquivo }}
                                @if($arq->tipo === 's')
                                    <span class="ml-1 text-[8px] font-extrabold text-amber-700 bg-amber-100 px-1 rounded uppercase">Sigiloso</span>
                                @endif
                            </td>
                            <td class="py-2 text-slate-550 italic">
                                {{ $arq->observacao ? '"' . $arq->observacao . '"' : '-' }}
                            </td>
                            <td class="py-2 text-right text-slate-500">
                                {{ $arq->data_inclusao ? $arq->data_inclusao->format('d/m/Y') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif



    </div>

    <!-- Auto Print Script -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // Trigger browser print interface
            setTimeout(() => {
                window.print();
            }, 600);
        });
    </script>
</body>
</html>
