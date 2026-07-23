@extends('layouts.app')

@section('title', 'Acolhimentos')
@section('subtitle', 'Pesquisa e Listagem de Acolhidos')

@section('content')
<div class="w-full max-w-[1600px] mx-auto space-y-8 animate-fade-in-up">

    <!-- Top Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <h2 class="text-3xl font-extrabold tracking-tight text-slate-850 dark:text-slate-100">Acolhidos</h2>
            <p class="text-sm text-slate-500 dark:text-slate-450 mt-1">Consulte e gerencie o histórico de pessoas atendidas.</p>
        </div>

        @can('edit-data')
        <div>
            <a href="{{ route('acolhimentos.create') }}"
               class="px-5 py-3 bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white font-bold rounded-xl text-sm shadow-sm transition-all duration-200 cursor-pointer inline-flex items-center space-x-2">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Novo Cadastro</span>
            </a>
        </div>
        @endcan
    </div>


    <!-- Search Form Card -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800/80">
        <form action="{{ route('acolhimentos.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" name="search" value="{{ $search }}"
                    class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-950 text-slate-850 dark:text-slate-100 border border-slate-250 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-450 focus:border-transparent text-sm transition-all duration-200"
                    placeholder="Pesquisar por Nome, CPF ou RG...">
            </div>
            <button type="submit"
                class="px-6 py-3 bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-650 text-white font-bold rounded-xl text-sm transition-all duration-200 cursor-pointer shadow-sm active:scale-[0.98]">
                Pesquisar
            </button>
            @if($search)
                <a href="{{ route('acolhimentos.index') }}"
                   class="px-6 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-200 rounded-xl text-sm font-bold transition-all duration-200 flex items-center justify-center">
                    Limpar
                </a>
            @endif
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-950/40 border-b border-slate-200 dark:border-slate-800 text-slate-400 dark:text-slate-455 font-bold text-xs uppercase tracking-wider">
                        <th class="py-4.5 px-6">Acolhido</th>
                        <th class="py-4.5 px-6">CPF</th>
                        <th class="py-4.5 px-6">Técnico Resp.</th>
                        <th class="py-4.5 px-6 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-850/60">
                    @forelse ($acolhimentos as $acolhido)
                        <tr class="hover:bg-slate-50/30 dark:hover:bg-slate-900/30 transition-colors duration-150 group">

                            <!-- Acolhido Info -->
                            <td class="py-4.5 px-6">
                                <div class="flex items-center space-x-2 flex-wrap gap-1">
                                    <div class="font-bold text-slate-800 dark:text-slate-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-455 transition-colors">
                                        {{ $acolhido->nome_pessoa }}
                                    </div>
                                    @if($acolhido->oculto === 's')
                                        <span class="text-[9px] bg-red-100 text-red-700 dark:bg-red-950/30 dark:text-red-400 font-bold px-1.5 py-0.5 rounded-full uppercase tracking-wider select-none">
                                            Ocultado
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- CPF -->
                            <td class="py-4.5 px-6 text-slate-655 dark:text-slate-400 font-mono">
                                {{ $acolhido->masked_cpf }}
                            </td>



                            <!-- Técnico Responsável -->
                            <td class="py-4.5 px-6">
                                <div class="text-slate-700 dark:text-slate-300 font-medium flex items-center space-x-2">
                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                    <span>{{ $acolhido->tecnicoResponsavel->nome_usu ?? 'Não informado' }}</span>
                                </div>
                            </td>

                            <!-- Ações -->
                            <td class="py-4.5 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('acolhimentos.show', $acolhido->id_acolhimento) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 active:scale-[0.98] text-white font-semibold rounded-lg text-xs shadow-xs transition-all duration-200 cursor-pointer">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Visualizar</span>
                                    </a>

                                    @can('edit-data')
                                    <a href="{{ route('acolhimentos.edit', $acolhido->id_acolhimento) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 active:scale-[0.98] text-white font-semibold rounded-lg text-xs shadow-xs transition-all duration-200 cursor-pointer">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span>Editar</span>
                                    </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-16 text-center text-slate-500 dark:text-slate-450">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <div class="h-16 w-16 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-850 rounded-2xl flex items-center justify-center text-slate-400">
                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-semibold">Nenhum acolhido cadastrado ou encontrado.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        @if ($acolhimentos->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-850/60">
                {{ $acolhimentos->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
