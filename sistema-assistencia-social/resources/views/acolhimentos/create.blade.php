@extends('layouts.app')

@section('title', 'Novo Cadastro')
@section('subtitle', 'Registrar Novo Acolhimento')

@section('content')
<div class="w-full max-w-[1600px] mx-auto space-y-6">

    <!-- Header Section -->
    <div class="flex items-center space-x-4 border-b border-slate-200 dark:border-slate-800 pb-4">
        <a href="{{ route('acolhimentos.index') }}" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-850 dark:text-slate-100">Novo Acolhido</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Preencha os campos abaixo para cadastrar a pessoa em situação de rua.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-red-50 dark:bg-red-950/20 border-l-4 border-red-500 rounded-xl text-red-700 dark:text-red-400 text-sm">
            <div class="font-bold mb-1">Por favor, corrija os seguintes erros:</div>
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('acolhimentos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- CARD FOTO -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-250/50 dark:border-slate-800/50 space-y-4">
            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 pb-2 border-b border-slate-100 dark:border-slate-850 flex items-center space-x-2">
                <span class="h-5 w-1 bg-emerald-500 rounded-full"></span>
                <span>Foto do Acolhido</span>
            </h3>

            <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                <!-- Preview da Foto -->
                <div class="relative w-40 h-40 group shrink-0">
                    <div class="w-full h-full rounded-full overflow-hidden border-2 border-slate-200 dark:border-slate-750 shadow-md">
                        <div id="profile-photo-placeholder" class="w-full h-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                            <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <img id="profile-photo-preview" class="w-full h-full object-cover hidden" alt="Preview da Foto">
                    </div>
                </div>

                <!-- Controles -->
                <div class="flex-1 w-full space-y-4">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Adicione uma foto ao cadastro selecionando um arquivo local ou tirando uma foto na hora utilizando a sua câmera.</p>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <!-- Botão Escolher Arquivo -->
                        <label class="flex-1 flex items-center justify-center px-4 py-2.5 bg-slate-50 hover:bg-slate-100 dark:bg-slate-950 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl border border-slate-300 dark:border-slate-800 cursor-pointer text-sm font-semibold transition-colors duration-200 text-center">
                            <span>Escolher Arquivo</span>
                            <input type="file" name="foto" id="foto-input" accept="image/*" class="hidden">
                        </label>

                        <!-- Botão Webcam -->
                        <button type="button" id="start-webcam" 
                            class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white rounded-xl text-sm font-bold transition-all duration-200 cursor-pointer flex items-center justify-center space-x-1 shadow-sm hover:shadow-md active:scale-[0.98]">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Tirar Foto (Câmera)</span>
                        </button>
                    </div>

                    <!-- Input Oculto para Imagem da Webcam em Base64 -->
                    <input type="hidden" name="webcam_image" id="webcam_image_input">

                    <!-- Painel da Webcam (oculto por padrão) -->
                    <div id="webcam-panel" class="hidden space-y-3 border border-slate-200 dark:border-slate-800 p-4 rounded-xl bg-slate-50 dark:bg-slate-950">
                        <div class="relative max-w-sm mx-auto aspect-square rounded-lg overflow-hidden border border-slate-300 dark:border-slate-800 bg-black">
                            <video id="webcam-preview" autoplay playsinline class="w-full h-full object-cover"></video>
                        </div>
                        <canvas id="webcam-canvas" class="hidden"></canvas>

                        <div class="flex gap-3 max-w-sm mx-auto">
                            <button type="button" id="capture-webcam"
                                class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white rounded-lg text-xs font-bold cursor-pointer transition-colors">
                                Tirar Foto
                            </button>
                            <button type="button" id="close-webcam"
                                class="flex-1 py-2 bg-slate-300 hover:bg-slate-450 dark:bg-slate-800 dark:hover:bg-slate-750 text-slate-800 dark:text-slate-200 rounded-lg text-xs font-bold cursor-pointer transition-colors">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 1: DADOS PESSOAIS -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-250/50 dark:border-slate-800/50 space-y-4">
            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 pb-2 border-b border-slate-100 dark:border-slate-850 flex items-center space-x-2">
                <span class="h-5 w-1 bg-emerald-500 rounded-full"></span>
                <span>1. Dados Pessoais</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nome_pessoa" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">Nome Completo <span class="text-red-500">*</span></label>
                    <input type="text" name="nome_pessoa" id="nome_pessoa" required value="{{ old('nome_pessoa') }}"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 text-slate-850 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                        placeholder="Ex: José de Oliveira">
                </div>

                <div>
                    <label for="nome_social" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">Nome Social</label>
                    <input type="text" name="nome_social" id="nome_social" value="{{ old('nome_social') }}"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 text-slate-850 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                        placeholder="Nome como a pessoa prefere ser chamada">
                </div>

                <div>
                    <label for="cpf" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">CPF <span class="text-red-500">*</span></label>
                    <input type="text" name="cpf" id="cpf" required value="{{ old('cpf') }}"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 text-slate-855 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm font-mono"
                        placeholder="Digite seu CPF">
                </div>

                <div>
                    <label for="rg" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">RG (Registro Geral)</label>
                    <input type="text" name="rg" id="rg" value="{{ old('rg') }}"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 text-slate-855 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                        placeholder="Número do documento de identidade">
                </div>

                <div>
                    <label for="dt_nascimento" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">Data de Nascimento <span class="text-red-500">*</span></label>
                    <input type="date" name="dt_nascimento" id="dt_nascimento" required value="{{ old('dt_nascimento') }}"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 text-slate-850 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label for="naturalidade" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">Naturalidade</label>
                        <input type="text" name="naturalidade" id="naturalidade" value="{{ old('naturalidade') }}"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 text-slate-855 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                            placeholder="Ex: Curitiba">
                    </div>
                    <div>
                        <label for="estado_nasc" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">UF</label>
                        <select name="estado_nasc" id="estado_nasc"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 text-slate-850 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                            <option value="">Selecione...</option>
                            @foreach ($estados as $est)
                                <option value="{{ $est->sigla }}" {{ old('estado_nasc') === $est->sigla ? 'selected' : '' }}>
                                    {{ $est->sigla }} - {{ $est->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 2: DADOS FAMILIARES -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-250/50 dark:border-slate-800/50 space-y-4">
            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 pb-2 border-b border-slate-100 dark:border-slate-850 flex items-center space-x-2">
                <span class="h-5 w-1 bg-emerald-500 rounded-full"></span>
                <span>2. Histórico Social e Familiar</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="pai" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">Nome do Pai</label>
                    <input type="text" name="pai" id="pai" value="{{ old('pai') }}"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 text-slate-850 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                        placeholder="Nome do pai">
                </div>

                <div>
                    <label for="mae" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">Nome da Mãe</label>
                    <input type="text" name="mae" id="mae" value="{{ old('mae') }}"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 text-slate-850 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                        placeholder="Nome da mãe">
                </div>
            </div>

            <!-- Contatos de Emergência/Parente -->
            <div class="space-y-4 pt-2">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Contatos Familiares</h4>
                    <button type="button" id="btn-add-parente"
                        class="px-3 py-1.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-950/60 font-semibold rounded-lg text-xs flex items-center space-x-1 transition-all duration-200 shadow-sm border border-emerald-200/50 dark:border-emerald-800/30 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                        </svg>
                        <span>Adicionar Outro Contato</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Parente 1 -->
                    <div id="parente1-card" class="md:col-span-2 p-4 bg-slate-50 dark:bg-slate-950 rounded-xl space-y-3 border border-slate-200/60 dark:border-slate-800/80 transition-all duration-300">
                        <h5 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Contato Familiar</h5>
                        <div>
                            <label for="parente_nome" class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Nome do Parente</label>
                            <input type="text" name="parente_nome" id="parente_nome" value="{{ old('parente_nome') }}"
                                class="w-full px-3 py-1.5 bg-white dark:bg-slate-900 text-slate-850 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="parente_grau" class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Grau de Parentesco</label>
                                <input type="text" name="parente_grau" id="parente_grau" value="{{ old('parente_grau') }}"
                                    class="w-full px-3 py-1.5 bg-white dark:bg-slate-900 text-slate-855 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm"
                                    placeholder="Ex: Irmão, Tio">
                            </div>
                            <div>
                                <label for="parente_end" class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Endereço / Telefone</label>
                                <input type="text" name="parente_end" id="parente_end" value="{{ old('parente_end') }}"
                                    class="w-full px-3 py-1.5 bg-white dark:bg-slate-900 text-slate-855 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Parente 2 -->
                    <div id="parente2-card" class="hidden p-4 bg-slate-50 dark:bg-slate-950 rounded-xl space-y-3 border border-slate-200/60 dark:border-slate-800/80 relative transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <h5 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Contato Familiar Adicional</h5>
                            <button type="button" id="btn-remove-parente" class="text-xs font-semibold text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 transition-colors cursor-pointer flex items-center space-x-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"></path>
                                </svg>
                                <span>Remover</span>
                            </button>
                        </div>
                        <div>
                            <label for="parente_grau1" class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Grau de Parentesco</label>
                            <input type="text" name="parente_grau1" id="parente_grau1" value="{{ old('parente_grau1') }}"
                                class="w-full px-3 py-1.5 bg-white dark:bg-slate-900 text-slate-855 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm"
                                placeholder="Ex: Primo, Mãe">
                        </div>
                        <div>
                            <label for="parente_end1" class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Endereço / Telefone</label>
                            <input type="text" name="parente_end1" id="parente_end1" value="{{ old('parente_end1') }}"
                                class="w-full px-3 py-1.5 bg-white dark:bg-slate-900 text-slate-855 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 3: CONDIÇÕES ESPECIAIS, SAÚDE E BENEFÍCIOS -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-250/50 dark:border-slate-800/50 space-y-4">
            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 pb-2 border-b border-slate-100 dark:border-slate-850 flex items-center space-x-2">
                <span class="h-5 w-1 bg-emerald-500 rounded-full"></span>
                <span>3. Condições Especiais, Saúde e Benefícios</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Necessidades Especiais -->
                <div class="space-y-3">
                    <div class="grid grid-cols-3 gap-2">
                        <div class="col-span-1">
                            <label for="nec_especial" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">Nec. Especial?</label>
                            <select name="nec_especial" id="nec_especial"
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 text-slate-850 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                                <option value="Não" {{ old('nec_especial') === 'Não' ? 'selected' : '' }}>Não</option>
                                <option value="Sim" {{ old('nec_especial') === 'Sim' ? 'selected' : '' }}>Sim</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="tipo_nec_especial" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">Qual?</label>
                            <input type="text" name="tipo_nec_especial" id="tipo_nec_especial" value="{{ old('tipo_nec_especial') }}"
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 text-slate-855 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                                placeholder="Descreva a necessidade especial">
                        </div>
                    </div>
                </div>

                <!-- Dependência Química -->
                <div class="space-y-3">
                    <div class="grid grid-cols-3 gap-2">
                        <div class="col-span-1">
                            <label for="depend_quimica" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">Dep. Química?</label>
                            <select name="depend_quimica" id="depend_quimica"
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 text-slate-850 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                                <option value="Não" {{ old('depend_quimica') === 'Não' ? 'selected' : '' }}>Não</option>
                                <option value="Sim" {{ old('depend_quimica') === 'Sim' ? 'selected' : '' }}>Sim</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="tipo_dep_quimica" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">Substâncias</label>
                            <input type="text" name="tipo_dep_quimica" id="tipo_dep_quimica" value="{{ old('tipo_dep_quimica') }}"
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 text-slate-855 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                                placeholder="Álcool, Crack, Cocaína, etc.">
                        </div>
                    </div>
                </div>

                <!-- Transtorno Mental -->
                <div class="space-y-3">
                    <div class="grid grid-cols-3 gap-2">
                        <div class="col-span-1">
                            <label for="transtorno" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">Transtorno?</label>
                            <select name="transtorno" id="transtorno"
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 text-slate-850 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                                <option value="Não" {{ old('transtorno') === 'Não' ? 'selected' : '' }}>Não</option>
                                <option value="Sim" {{ old('transtorno') === 'Sim' ? 'selected' : '' }}>Sim</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="tipo_transtorno" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">Diagnóstico / Sintomas</label>
                            <input type="text" name="tipo_transtorno" id="tipo_transtorno" value="{{ old('tipo_transtorno') }}"
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 text-slate-855 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                                placeholder="Depressão, Esquizofrenia, etc.">
                        </div>
                    </div>
                </div>

                <!-- Benefícios Sociais -->
                <div class="space-y-3">
                    <div class="grid grid-cols-3 gap-2">
                        <div class="col-span-1">
                            <label for="recebe_beneficio" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">Benefício?</label>
                            <select name="recebe_beneficio" id="recebe_beneficio"
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 text-slate-850 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                                <option value="Não" {{ old('recebe_beneficio') === 'Não' ? 'selected' : '' }}>Não</option>
                                <option value="Sim" {{ old('recebe_beneficio') === 'Sim' ? 'selected' : '' }}>Sim</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="tipo_beneficio" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">Qual Benefício?</label>
                            <input type="text" name="tipo_beneficio" id="tipo_beneficio" value="{{ old('tipo_beneficio') }}"
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 text-slate-855 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                                placeholder="BPC, Bolsa Família, etc.">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 4: MONITORAMENTO E SITUAÇÃO -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-250/50 dark:border-slate-800/50 space-y-4">
            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 pb-2 border-b border-slate-100 dark:border-slate-850 flex items-center space-x-2">
                <span class="h-5 w-1 bg-emerald-500 rounded-full"></span>
                <span>4. Situação de Rua e Monitoramento</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label for="cid_bairro_situacao" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">Cidade / Bairro onde se encontra</label>
                    <input type="text" name="cid_bairro_situacao" id="cid_bairro_situacao" value="{{ old('cid_bairro_situacao') }}"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 text-slate-850 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                        placeholder="Onde a pessoa reside atualmente">
                </div>

                <div>
                    <label for="monitoramento" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">Monitoramento Ativo?</label>
                    <select name="monitoramento" id="monitoramento"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 text-slate-850 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                        <option value="Não" {{ old('monitoramento') === 'Não' ? 'selected' : '' }}>Não</option>
                        <option value="Sim" {{ old('monitoramento') === 'Sim' ? 'selected' : '' }}>Sim</option>
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label for="obs_pessoa" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">Observações Gerais / Histórico Inicial</label>
                    <textarea name="obs_pessoa" id="obs_pessoa" rows="4"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 text-slate-850 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                        placeholder="Observações iniciais sobre as demandas, encaminhamentos ou situação física e psicológica do acolhido.">{{ old('obs_pessoa') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 pt-2">
            <a href="{{ route('acolhimentos.index') }}"
                class="px-5 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-750 text-slate-750 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all duration-200">
                Cancelar
            </a>
            <button type="submit"
                class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white font-semibold rounded-xl text-sm shadow-md shadow-emerald-500/20 transition-all duration-200 cursor-pointer">
                Salvar Cadastro
            </button>
        </div>
    </form>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cpfInput = document.getElementById('cpf');
        if (cpfInput) {
            const formatValue = (value) => {
                let numbers = value.replace(/\D/g, '');
                if (numbers.length > 11) numbers = numbers.substring(0, 11);
                let formatted = '';
                if (numbers.length > 0) formatted += numbers.substring(0, 3);
                if (numbers.length > 3) formatted += '.' + numbers.substring(3, 6);
                if (numbers.length > 6) formatted += '.' + numbers.substring(6, 9);
                if (numbers.length > 9) formatted += '-' + numbers.substring(9, 11);
                return formatted;
            };

            // Format on load
            if (cpfInput.value) {
                cpfInput.value = formatValue(cpfInput.value);
            }

            // Format on input
            cpfInput.addEventListener('input', function (e) {
                e.target.value = formatValue(e.target.value);
            });
        }

        // RG formatting
        const rgInput = document.getElementById('rg');
        if (rgInput) {
            const formatRgValue = (value) => {
                let chars = value.replace(/[^0-9a-zA-Z]/g, '');
                if (chars.length > 9) chars = chars.substring(0, 9);
                
                let formatted = '';
                if (chars.length > 0) {
                    if (chars.length <= 2) {
                        formatted = chars;
                    } else if (chars.length <= 5) {
                        formatted = chars.substring(0, 2) + '.' + chars.substring(2);
                    } else if (chars.length <= 8) {
                        formatted = chars.substring(0, 2) + '.' + chars.substring(2, 5) + '.' + chars.substring(5);
                    } else {
                        formatted = chars.substring(0, 2) + '.' + chars.substring(2, 5) + '.' + chars.substring(5, 8) + '-' + chars.substring(8);
                    }
                }
                return formatted;
            };

            // Format on load
            if (rgInput.value) {
                rgInput.value = formatRgValue(rgInput.value);
                let clean = rgInput.value.replace(/[^0-9a-zA-Z]/g, '');
                if (clean.length === 8) {
                    rgInput.value = clean.substring(0, 1) + '.' + clean.substring(1, 4) + '.' + clean.substring(4, 7) + '-' + clean.substring(7);
                }
            }

            // Format on input
            rgInput.addEventListener('input', function (e) {
                e.target.value = formatRgValue(e.target.value);
            });

            // Adjust on blur for 8 digit RGs
            rgInput.addEventListener('blur', function (e) {
                let clean = e.target.value.replace(/[^0-9a-zA-Z]/g, '');
                if (clean.length === 8) {
                    e.target.value = clean.substring(0, 1) + '.' + clean.substring(1, 4) + '.' + clean.substring(4, 7) + '-' + clean.substring(7);
                }
            });
        }

        // Familiar contacts dynamic form
        const btnAddParente = document.getElementById('btn-add-parente');
        const btnRemoveParente = document.getElementById('btn-remove-parente');
        const parente1Card = document.getElementById('parente1-card');
        const parente2Card = document.getElementById('parente2-card');
        const parenteGrau1 = document.getElementById('parente_grau1');
        const parenteEnd1 = document.getElementById('parente_end1');

        function showParente2() {
            parente2Card.classList.remove('hidden');
            parente1Card.classList.remove('md:col-span-2');
            parente1Card.classList.add('md:col-span-1');
            btnAddParente.classList.add('hidden');
        }

        function hideParente2() {
            parente2Card.classList.add('hidden');
            parente1Card.classList.remove('md:col-span-1');
            parente1Card.classList.add('md:col-span-2');
            btnAddParente.classList.remove('hidden');
            parenteGrau1.value = '';
            parenteEnd1.value = '';
        }

        if (btnAddParente && btnRemoveParente) {
            btnAddParente.addEventListener('click', showParente2);
            btnRemoveParente.addEventListener('click', hideParente2);

            // Check if there's pre-filled data to show second card on load
            if (parenteGrau1.value || parenteEnd1.value) {
                showParente2();
            }
        }

        // Photo preview & webcam scripting
        const fotoInput = document.getElementById('foto-input');
        const profilePhotoPreview = document.getElementById('profile-photo-preview');
        const placeholderPhoto = document.getElementById('profile-photo-placeholder');
        const webcamImageInput = document.getElementById('webcam_image_input');

        if (fotoInput) {
            fotoInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        profilePhotoPreview.src = e.target.result;
                        profilePhotoPreview.classList.remove('hidden');
                        if (placeholderPhoto) placeholderPhoto.classList.add('hidden');

                        // Clear webcam image if file is chosen
                        webcamImageInput.value = '';
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }

        // Webcam Controls
        const startWebcamBtn = document.getElementById('start-webcam');
        const webcamPanel = document.getElementById('webcam-panel');
        const webcamPreview = document.getElementById('webcam-preview');
        const webcamCanvas = document.getElementById('webcam-canvas');
        const captureWebcamBtn = document.getElementById('capture-webcam');
        const closeWebcamBtn = document.getElementById('close-webcam');

        let stream = null;

        if (startWebcamBtn) {
            startWebcamBtn.addEventListener('click', async () => {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ video: { width: 400, height: 400 } });
                    webcamPreview.srcObject = stream;
                    webcamPanel.classList.remove('hidden');
                    startWebcamBtn.classList.add('hidden');
                } catch (err) {
                    alert('Erro ao acessar a webcam. Por favor, verifique se deu permissão no seu navegador.');
                    console.error(err);
                }
            });
        }

        if (closeWebcamBtn) {
            closeWebcamBtn.addEventListener('click', () => {
                stopWebcam();
            });
        }

        function stopWebcam() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
            webcamPreview.srcObject = null;
            webcamPanel.classList.add('hidden');
            if (startWebcamBtn) {
                startWebcamBtn.classList.remove('hidden');
            }
        }

        if (captureWebcamBtn) {
            captureWebcamBtn.addEventListener('click', () => {
                const context = webcamCanvas.getContext('2d');
                webcamCanvas.width = 400;
                webcamCanvas.height = 400;

                // Draw preview to canvas
                context.drawImage(webcamPreview, 0, 0, 400, 400);

                // Get base64 URL
                const dataUrl = webcamCanvas.toDataURL('image/jpeg', 0.85);

                // Set in hidden input
                webcamImageInput.value = dataUrl;

                // Set image preview
                profilePhotoPreview.src = dataUrl;
                profilePhotoPreview.classList.remove('hidden');
                if (placeholderPhoto) {
                    placeholderPhoto.classList.add('hidden');
                }

                // Clear file input
                fotoInput.value = '';

                stopWebcam();
            });
        }
    });
</script>
@endsection
