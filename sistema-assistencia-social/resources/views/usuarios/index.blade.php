@extends('layouts.app')

@section('title', 'Gerenciamento de Usuários')
@section('subtitle', 'Gerenciar Contas de Acesso')

@section('content')
<div class="w-full max-w-[1600px] mx-auto space-y-8 animate-fade-in-up">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <h2 class="text-3xl font-extrabold tracking-tight text-slate-850 dark:text-slate-100">Contas de Usuários</h2>
            <p class="text-sm text-slate-500 dark:text-slate-455 mt-1">Crie, pesquise e remova contas de acesso ao sistema.</p>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Coluna Esquerda (1/3): Formulário de Criação -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800/80 space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center space-x-2">
                        <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ Auth::user()->isDiretor() ? 'Criar Nova Conta' : 'Criar Novo Usuário' }}</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Insira as credenciais do novo colaborador.</p>
                </div>

                @if ($errors->any())
                    <div class="p-3 bg-red-50 dark:bg-red-950/20 border-l-4 border-red-500 rounded text-red-700 dark:text-red-400 text-xs">
                        <ul class="list-disc pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('usuarios.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="nome_usu" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">Nome Completo</label>
                        <input type="text" name="nome_usu" id="nome_usu" required value="{{ old('nome_usu') }}"
                            class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 text-slate-850 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm"
                            placeholder="Ex: João da Silva">
                    </div>
                    
                    <div>
                        <label for="login" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">Usuário / Login</label>
                        <input type="text" name="login" id="login" required value="{{ old('login') }}"
                            class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 text-slate-855 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm"
                            placeholder="Ex: joao.silva">
                    </div>

                    <div>
                        <label for="senha" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">Senha Provisória</label>
                        <div class="relative">
                            <input type="password" name="senha" id="senha" required
                                class="w-full pl-3.5 pr-10 py-2.5 bg-slate-50 dark:bg-slate-950 text-slate-850 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm"
                                placeholder="Mínimo 6 caracteres">
                            <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer focus:outline-none">
                                <svg id="eye-icon-open" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eye-icon-closed" class="h-4.5 w-4.5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    @if(Auth::user()->isDiretor())
                        <div>
                            <label for="permissao" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">Nível de Permissão</label>
                            <select name="permissao" id="permissao" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 text-slate-850 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                                <option value="n" {{ old('permissao') === 'n' ? 'selected' : '' }}>Usuário (Leitura apenas)</option>
                                <option value="a" {{ old('permissao') === 'a' ? 'selected' : '' }}>Administrador (Acesso total)</option>
                                <option value="d" {{ old('permissao') === 'd' ? 'selected' : '' }}>Diretor</option>
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="permissao" value="n">
                    @endif

                    <div>
                        <label for="tipo_acesso" class="block text-xs font-semibold text-slate-655 dark:text-slate-350 mb-1">Acesso a Informações Sigilosas?</label>
                        <select name="tipo_acesso" id="tipo_acesso" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 text-slate-850 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                            <option value="n" {{ old('tipo_acesso') === 'n' ? 'selected' : '' }}>Não</option>
                            <option value="s" {{ old('tipo_acesso') === 's' ? 'selected' : '' }}>Sim</option>
                        </select>
                    </div>
                    
                    <button type="submit"
                        class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white font-bold rounded-xl text-sm shadow-md shadow-emerald-500/20 transition-all duration-200 cursor-pointer">
                        Criar Conta
                    </button>
                </form>
            </div>
        </div>

        <!-- Coluna Direita (2/3): Listagens e Busca -->
        <div class="lg:col-span-2 space-y-6">
            
            @if(Auth::user()->isDiretor())
                <!-- Tabs para Diretor -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800/80 p-2 flex space-x-1">
                    <button type="button" onclick="switchConfigTab('diretores')" id="tab-btn-diretores"
                        class="flex-1 py-2.5 px-4 rounded-xl text-sm font-bold transition-all duration-200 tab-btn cursor-pointer bg-slate-100 dark:bg-slate-850 text-emerald-600 dark:text-emerald-400">
                        Diretores ({{ count($diretores) }})
                    </button>
                    <button type="button" onclick="switchConfigTab('admins')" id="tab-btn-admins"
                        class="flex-1 py-2.5 px-4 rounded-xl text-sm font-bold transition-all duration-200 tab-btn cursor-pointer text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-255">
                        Administradores ({{ count($users) }})
                    </button>
                    <button type="button" onclick="switchConfigTab('usuarios')" id="tab-btn-usuarios"
                        class="flex-1 py-2.5 px-4 rounded-xl text-sm font-bold transition-all duration-200 tab-btn cursor-pointer text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-255">
                        Usuários
                    </button>
                </div>

                <!-- Tab: Diretores -->
                <div id="tab-content-diretores" class="config-tab-pane space-y-6">
                    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800/80">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-4 flex items-center space-x-2">
                            <span class="h-2 w-2 bg-emerald-500 rounded-full"></span>
                            <span>Contas de Diretores</span>
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-semibold">
                                        <th class="pb-3">Nome / Login</th>
                                        <th class="pb-3">Sigiloso?</th>
                                        <th class="pb-3 text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-850">
                                    @foreach ($diretores as $u)
                                        <tr>
                                            <td class="py-3">
                                                <div class="font-semibold text-slate-800 dark:text-slate-100">{{ $u->nome_usu }}</div>
                                                <div class="text-xs text-slate-400">{{ $u->login }}</div>
                                            </td>
                                            <td class="py-3">
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $u->tipo_acesso === 's' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                                    {{ $u->tipo_acesso === 's' ? 'Sim' : 'Não' }}
                                                </span>
                                            </td>
                                            <td class="py-3 text-center">
                                                @if ($u->id_usuario !== Auth::id())
                                                    <form action="{{ route('usuarios.destroy', $u->id_usuario) }}" method="POST"
                                                        onsubmit="return confirm('Tem certeza que deseja excluir esta conta de Diretor? Esta ação não pode ser desfeita.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-500 hover:text-red-400 font-semibold text-xs cursor-pointer">
                                                            Excluir
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-xs text-slate-400 italic">Sua Conta</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tab: Administradores -->
                <div id="tab-content-admins" class="config-tab-pane hidden space-y-6">
                    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800/80">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-4 flex items-center space-x-2">
                            <span class="h-2 w-2 bg-emerald-500 rounded-full"></span>
                            <span>Contas de Administradores</span>
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-semibold">
                                        <th class="pb-3">Nome / Login</th>
                                        <th class="pb-3">Sigiloso?</th>
                                        <th class="pb-3 text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-850">
                                    @foreach ($users as $u)
                                        <tr>
                                            <td class="py-3">
                                                <div class="font-semibold text-slate-800 dark:text-slate-100">{{ $u->nome_usu }}</div>
                                                <div class="text-xs text-slate-400">{{ $u->login }}</div>
                                            </td>
                                            <td class="py-3">
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $u->tipo_acesso === 's' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                                    {{ $u->tipo_acesso === 's' ? 'Sim' : 'Não' }}
                                                </span>
                                            </td>
                                            <td class="py-3 text-center">
                                                @if ($u->id_usuario !== Auth::id())
                                                    <form action="{{ route('usuarios.destroy', $u->id_usuario) }}" method="POST"
                                                        onsubmit="return confirm('Tem certeza que deseja excluir esta conta de Administrador? Esta ação não pode ser desfeita.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-500 hover:text-red-400 font-semibold text-xs cursor-pointer">
                                                            Excluir
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-xs text-slate-400 italic">Sua Conta</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Área de Busca de Usuários (Comum para Diretores e Admins) -->
            <div id="tab-content-usuarios" class="config-tab-pane @if(Auth::user()->isDiretor()) hidden @endif space-y-6">
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800/80">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2 flex items-center space-x-2">
                        <span class="h-2 w-2 bg-emerald-500 rounded-full"></span>
                        <span>Pesquisa de Usuários</span>
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-slate-450 mb-6">Busque contas com nível de acesso de usuário digitando o nome completo ou login.</p>

                    <!-- Form de Busca -->
                    <form action="{{ route('usuarios.index') }}" method="GET" class="flex gap-2">
                        @if(Auth::user()->isDiretor())
                            <input type="hidden" name="active_tab" value="usuarios">
                        @endif
                        <input type="text" name="search_user" value="{{ $search }}" placeholder="Buscar usuário por nome ou login..." 
                            class="flex-1 px-4 py-2.5 bg-slate-50 dark:bg-slate-950 text-slate-850 dark:text-slate-100 border border-slate-300 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white font-bold rounded-xl text-sm shadow transition-all duration-200 cursor-pointer">
                            Buscar
                        </button>
                        @if($search)
                            <a href="{{ route('usuarios.index') }}{{ Auth::user()->isDiretor() ? '?active_tab=usuarios' : '' }}" class="px-4 py-2.5 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold rounded-xl text-sm transition-all duration-200 flex items-center justify-center">
                                Limpar
                            </a>
                        @endif
                    </form>

                    <!-- Lista de Resultados (Modo de Pesquisa) -->
                    <div class="mt-8">
                        @if(!$search)
                            <div class="p-8 text-center text-slate-400 dark:text-slate-500 border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl flex flex-col items-center justify-center space-y-2">
                                <span class="text-3xl">🔍</span>
                                <span class="text-sm font-semibold">Digite uma busca no campo acima para pesquisar usuários.</span>
                            </div>
                        @elseif($commonUsers->isEmpty())
                            <div class="p-8 text-center text-slate-400 dark:text-slate-500 border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl flex flex-col items-center justify-center space-y-2">
                                <span class="text-3xl">❌</span>
                                <span class="text-sm font-semibold">Nenhum usuário encontrado para a busca "{{ $search }}".</span>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-sm">
                                    <thead>
                                        <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-semibold">
                                            <th class="pb-3">Nome / Login</th>
                                            <th class="pb-3">Perfil</th>
                                            <th class="pb-3">Sigiloso?</th>
                                            <th class="pb-3 text-center">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-850">
                                        @foreach ($commonUsers as $u)
                                            <tr>
                                                <td class="py-3">
                                                    <div class="font-semibold text-slate-800 dark:text-slate-100">{{ $u->nome_usu }}</div>
                                                    <div class="text-xs text-slate-400">{{ $u->login }}</div>
                                                </td>
                                                <td class="py-3">
                                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                        Usuário
                                                    </span>
                                                </td>
                                                <td class="py-3">
                                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $u->tipo_acesso === 's' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                                        {{ $u->tipo_acesso === 's' ? 'Sim' : 'Não' }}
                                                    </span>
                                                </td>
                                                <td class="py-3 text-center">
                                                    @if (Auth::user()->isDiretor())
                                                        <form action="{{ route('usuarios.destroy', $u->id_usuario) }}" method="POST"
                                                            onsubmit="return confirm('Tem certeza que deseja excluir esta conta de usuário? Esta ação não pode ser desfeita.')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-500 hover:text-red-400 font-semibold text-xs cursor-pointer">
                                                                Excluir
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-xs text-slate-400 italic">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Paginação -->
                            <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-100 dark:border-slate-850 pt-4">
                                <div class="text-xs text-slate-500">
                                    Mostrando {{ $commonUsers->firstItem() ?? 0 }} a {{ $commonUsers->lastItem() ?? 0 }} de {{ $commonUsers->total() }} usuários
                                </div>
                                <div class="flex justify-center">
                                    {{ $commonUsers->links() }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle de visualização da senha provisória
        const togglePasswordBtn = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('senha');
        if (togglePasswordBtn && passwordInput) {
            togglePasswordBtn.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                document.getElementById('eye-icon-open').classList.toggle('hidden');
                document.getElementById('eye-icon-closed').classList.toggle('hidden');
            });
        }

        // Mantém a aba ativa se enviada via busca
        @if(Auth::user()->isDiretor())
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('active_tab') || urlParams.get('users_page') ? 'usuarios' : 'diretores';
            switchConfigTab(activeTab);
        @endif
    });

    @if(Auth::user()->isDiretor())
        function switchConfigTab(tabId) {
            document.querySelectorAll('.config-tab-pane').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('bg-slate-100', 'dark:bg-slate-850', 'text-emerald-600', 'dark:text-emerald-400');
                el.classList.add('text-slate-500', 'hover:text-slate-800', 'dark:text-slate-400', 'dark:hover:text-slate-255');
            });

            document.getElementById('tab-content-' + tabId).classList.remove('hidden');

            const btn = document.getElementById('tab-btn-' + tabId);
            btn.classList.remove('text-slate-500', 'hover:text-slate-800', 'dark:text-slate-400', 'dark:hover:text-slate-255');
            btn.classList.add('bg-slate-100', 'dark:bg-slate-850', 'text-emerald-600', 'dark:text-emerald-400');
        }
    @endif
</script>
@endsection
