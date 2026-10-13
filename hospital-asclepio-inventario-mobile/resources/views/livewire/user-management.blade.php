<div class="space-y-6">
    <x-slot name="header">Gestão de Usuários e Permissões</x-slot>

    <!-- Top Action & Search Bar -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-6 shadow-xl flex flex-col md:flex-row gap-4 items-stretch md:items-center justify-between">
        <form action="#" @submit.prevent="$refs.searchUserInput.blur()" x-data class="flex items-center gap-2 flex-1">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <flux:icon name="magnifying-glass" class="w-5 h-5" />
                </div>
                <input x-ref="searchUserInput"
                       wire:model.live.debounce.300ms="search"
                       type="search"
                       enterkeyhint="search"
                       @keydown.enter="$el.blur()"
                       placeholder="Pesquisar por nome, usuário de acesso ou setor..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition">
            </div>
            <button type="submit"
                    @click="$refs.searchUserInput.blur()"
                    class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 font-bold text-slate-950 text-xs sm:text-sm transition shadow-md shadow-emerald-500/20 shrink-0 flex items-center justify-center gap-1.5 cursor-pointer">
                <flux:icon name="magnifying-glass" class="w-4 h-4" />
                <span>Buscar</span>
            </button>
        </form>

        <button wire:click="novoUsuario" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 font-bold text-slate-950 text-sm transition shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2 cursor-pointer shrink-0">
            <flux:icon name="user-plus" class="w-5 h-5 shrink-0" />
            <span>Novo Usuário</span>
        </button>
    </div>

    @if(session()->has('message'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-sm font-semibold">
            {{ session('message') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-600 dark:text-red-400 text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <!-- Role Filter Tabs -->
    <div class="flex border-b border-slate-200 dark:border-slate-800 gap-2 overflow-x-auto pb-1">
        @if(auth()->user()->isDiretor())
            <button wire:click="setTab('diretor')"
                    class="px-5 py-3 font-bold text-sm border-b-2 transition flex items-center gap-2.5 whitespace-nowrap cursor-pointer {{ $activeTab === 'diretor' ? 'border-amber-500 text-amber-600 dark:text-amber-400 bg-amber-500/10 rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200' }}">
                <span>Diretores</span>
                <span class="px-2 py-0.5 text-xs rounded-full bg-amber-500/20 text-amber-700 dark:text-amber-300 font-extrabold">{{ $countDiretores }}</span>
            </button>
        @endif

        @if(auth()->user()->isDiretor() || auth()->user()->isAdmin())
            <button wire:click="setTab('administrador')"
                    class="px-5 py-3 font-bold text-sm border-b-2 transition flex items-center gap-2.5 whitespace-nowrap cursor-pointer {{ $activeTab === 'administrador' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200' }}">
                <span>Administradores</span>
                <span class="px-2 py-0.5 text-xs rounded-full bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 font-extrabold">{{ $countAdmins }}</span>
            </button>

            <button wire:click="setTab('coordenador')"
                    class="px-5 py-3 font-bold text-sm border-b-2 transition flex items-center gap-2.5 whitespace-nowrap cursor-pointer {{ $activeTab === 'coordenador' ? 'border-purple-500 text-purple-600 dark:text-purple-400 bg-purple-500/10 rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200' }}">
                <span>Coordenadores</span>
                <span class="px-2 py-0.5 text-xs rounded-full bg-purple-500/20 text-purple-700 dark:text-purple-300 font-extrabold">{{ $countCoordenadores }}</span>
            </button>
        @endif

        @if(auth()->user()->isDiretor() || auth()->user()->isCoordenador())
            <button wire:click="setTab('usuario')"
                    class="px-5 py-3 font-bold text-sm border-b-2 transition flex items-center gap-2.5 whitespace-nowrap cursor-pointer {{ $activeTab === 'usuario' ? 'border-blue-500 text-blue-600 dark:text-blue-400 bg-blue-500/10 rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200' }}">
                <span>Usuários</span>
                <span class="px-2 py-0.5 text-xs rounded-full bg-blue-500/20 text-blue-700 dark:text-blue-300 font-extrabold">{{ $countUsuarios }}</span>
            </button>
        @endif
    </div>

    <!-- Users Table (Desktop) & Cards (Mobile) -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl overflow-hidden">
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-950/60 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Nome Completo</th>
                        <th class="px-6 py-4">Usuário de Acesso</th>
                        @if($activeTab === 'usuario' || $activeTab === 'coordenador')
                            <th class="px-6 py-4">Setor / Local Alocado</th>
                        @else
                            <th class="px-6 py-4">Perfil (Role)</th>
                        @endif
                        <th class="px-6 py-4">Data de Cadastro</th>
                        <th class="px-6 py-4 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-linear-to-br from-emerald-400 to-teal-600 text-white font-extrabold flex items-center justify-center text-xs shadow-sm">
                                        {{ $user->initials() }}
                                    </div>
                                    <span class="font-bold text-slate-900 dark:text-slate-100">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-emerald-600 dark:text-emerald-400 font-bold text-xs">{{ $user->username }}</td>
                            @if($activeTab === 'coordenador')
                                <td class="px-6 py-4">
                                    @if($user->setor)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 shadow-xs">
                                            <svg class="w-3.5 h-3.5 text-purple-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            {{ $user->setor->secretaria?->secretaria ?? $user->setor->local }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                            ⚠️ Sem Setor Alocado
                                        </span>
                                    @endif
                                </td>
                            @elseif($activeTab === 'usuario')
                                <td class="px-6 py-4">
                                    @if($user->setor)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 shadow-xs">
                                            <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            {{ $user->setor->local }}
                                            @if($user->setor->secretaria)
                                                <span class="text-[10px] text-slate-400 font-normal">({{ $user->setor->secretaria->secretaria }})</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                            ⚠️ Sem Setor Alocado
                                        </span>
                                    @endif
                                </td>
                            @else
                                <td class="px-6 py-4">
                                    @if($user->isDiretor())
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">Diretor</span>
                                    @elseif($user->isCoordenador())
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20">Coordenador</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">Administrador (T.I)</span>
                                    @endif
                                </td>
                            @endif
                            <td class="px-6 py-4 text-xs text-slate-500 dark:text-slate-400">{{ $user->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="editarUsuario({{ $user->id }})" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-semibold border border-emerald-500/20 cursor-pointer">
                                        Editar
                                    </button>
                                    @if($user->id !== auth()->id())
                                        <button wire:click="inativarUsuario({{ $user->id }})"
                                                @if(auth()->user()->isDiretor())
                                                    wire:confirm="Deseja EXCLUIR DEFINITIVAMENTE (Hard Delete) a conta do usuário '{{ $user->name }}'?"
                                                @else
                                                    wire:confirm="Deseja arquivar/desativar (Soft Delete) a conta do usuário '{{ $user->name }}'?"
                                                @endif
                                                class="px-3 py-1.5 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-semibold border border-red-500/20 cursor-pointer">
                                            Excluir
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500 text-xs">
                                Nenhum usuário encontrado nesta aba.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Stacked Cards View -->
        <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($users as $user)
                <div class="p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-linear-to-br from-emerald-400 to-teal-600 text-white font-extrabold flex items-center justify-center text-xs">
                                {{ $user->initials() }}
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $user->name }}</h4>
                                <span class="font-mono text-xs text-emerald-600 dark:text-emerald-400">{{ $user->username }}</span>
                            </div>
                        </div>
                        @if($user->isDiretor())
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">Diretor</span>
                        @elseif($user->isCoordenador())
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20">Coordenador</span>
                        @elseif($user->isUsuario())
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">Usuário</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">Admin</span>
                        @endif
                    </div>

                    @if($user->isUsuario())
                        <div class="text-xs text-slate-600 dark:text-slate-300 font-medium">
                            Setor: <strong class="text-indigo-600 dark:text-indigo-400">{{ $user->setor?->local ?? 'Sem Setor' }}</strong>
                        </div>
                    @elseif($user->isCoordenador())
                        <div class="text-xs text-slate-600 dark:text-slate-300 font-medium">
                            Setor: <strong class="text-purple-600 dark:text-purple-400">{{ $user->setor?->secretaria?->secretaria ?? $user->setor?->local ?? 'Sem Setor' }}</strong>
                        </div>
                    @endif

                    <div class="pt-2 flex items-center justify-between border-t border-slate-100 dark:border-slate-800">
                        <span class="text-[11px] text-slate-400">Cadastrado em {{ $user->created_at?->format('d/m/Y') }}</span>
                        <div class="flex flex-wrap items-center gap-2.5">
                            <button wire:click="editarUsuario({{ $user->id }})" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-semibold border border-emerald-500/20 cursor-pointer">
                                Editar
                            </button>
                            @if($user->id !== auth()->id())
                                <button wire:click="inativarUsuario({{ $user->id }})"
                                        @if(auth()->user()->isDiretor())
                                            wire:confirm="Deseja EXCLUIR DEFINITIVAMENTE (Hard Delete) a conta do usuário '{{ $user->name }}'?"
                                        @else
                                            wire:confirm="Deseja arquivar/desativar (Soft Delete) a conta do usuário '{{ $user->name }}'?"
                                        @endif
                                        class="px-3 py-1.5 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-semibold border border-red-500/20 cursor-pointer">
                                    Excluir
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-500 text-xs">
                    Nenhum usuário encontrado nesta aba.
                </div>
            @endforelse
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            <flux:pagination :paginator="$users" />
        </div>
    </div>

    <!-- Modal Form (User Creation / Edit) -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-md p-6 shadow-2xl space-y-4">
            <div class="border-b border-slate-100 dark:border-slate-800 pb-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    @if($userId)
                        <flux:icon name="pencil-square" class="w-5 h-5" />
                    @else
                        <flux:icon name="user-plus" class="w-5 h-5" />
                    @endif
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">
                    {{ $userId ? 'Editar Usuário' : 'Novo Usuário' }}
                </h3>
            </div>

            <form wire:submit="salvar" class="space-y-4" autocomplete="off">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Nome Completo *</label>
                    <input wire:model="name" type="text" placeholder="Nome do servidor/técnico" autocomplete="off"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                    @error('name') <span class="text-xs text-red-500 dark:text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Usuário de Acesso *</label>
                    <input wire:model="username" type="text" placeholder="ex: Diretor ou Administrador" autocomplete="off"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                    @error('username') <span class="text-xs text-red-500 dark:text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        {{ $userId ? 'Nova Senha (deixe em branco para manter a atual)' : 'Senha de Acesso *' }}
                    </label>
                    <input wire:model="password" type="password" placeholder="••••••••" autocomplete="new-password"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                    @error('password') <span class="text-xs text-red-500 dark:text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                @if(!auth()->user()->isCoordenador())
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Perfil de Acesso (Role) *</label>
                    <select wire:model.live="role" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                        @if(auth()->user()->isDiretor())
                            <option value="diretor">Diretor</option>
                            <option value="administrador">Administrador</option>
                            <option value="coordenador">Coordenador</option>
                            <option value="usuario">Usuário</option>
                        @elseif(auth()->user()->isAdmin())
                            <option value="administrador">Administrador</option>
                            <option value="coordenador">Coordenador</option>
                        @endif
                    </select>
                </div>
                @endif

                <!-- Campo Setor / Local conforme perfil sendo criado -->
                @if($role === 'administrador')
                    <div class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-xs flex items-center gap-2 font-medium">
                        <flux:icon name="information-circle" class="w-4 h-4 shrink-0 text-emerald-500" />
                        <span>Local de alocação vinculado automaticamente para <strong>"T.I"</strong>.</span>
                    </div>
                @elseif($role === 'coordenador')
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Setor do Coordenador *</label>
                        <flux:select wire:model.live="secretaria_id" variant="select" placeholder="Selecione o Setor / Secretaria...">
                            <flux:select.option value="">Selecione o Setor / Secretaria...</flux:select.option>
                            @foreach($secretarias as $s)
                                <flux:select.option value="{{ $s->id_secretarias }}">{{ $s->secretaria }} ({{ $s->nome_extenso }})</flux:select.option>
                            @endforeach
                        </flux:select>
                        @error('secretaria_id') <span class="text-xs text-red-500 dark:text-red-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                @elseif($role === 'usuario')
                    @if(!auth()->user()->isCoordenador())
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Setor / Secretaria *</label>
                        <flux:select wire:model.live="secretaria_id" variant="select" placeholder="Selecione o Setor / Secretaria...">
                            <flux:select.option value="">Selecione o Setor / Secretaria...</flux:select.option>
                            @foreach($secretarias as $s)
                                <flux:select.option value="{{ $s->id_secretarias }}">{{ $s->secretaria }} ({{ $s->nome_extenso }})</flux:select.option>
                            @endforeach
                        </flux:select>
                        @error('secretaria_id') <span class="text-xs text-red-500 dark:text-red-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                    @endif

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Local de Alocação *</label>
                        <flux:select wire:model.live="setor_id" variant="select" placeholder="Selecione o Local de Alocação...">
                            <flux:select.option value="">Selecione o Local de Alocação...</flux:select.option>
                            @foreach($locais as $l)
                                <flux:select.option value="{{ $l->id_local }}">{{ $l->local }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        @error('setor_id') <span class="text-xs text-red-500 dark:text-red-400 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-sm">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold text-sm shadow-lg shadow-emerald-500/20">
                        {{ $userId ? 'Atualizar Usuário' : 'Cadastrar Usuário' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
