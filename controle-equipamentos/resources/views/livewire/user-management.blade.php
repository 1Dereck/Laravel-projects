<div class="space-y-6">
    <x-slot name="header">Gestão de Usuários e Permissões (Exclusivo Diretor)</x-slot>

    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xl">
        <div>
            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Equipe Cadastrada</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Controle de diretores e administradores técnicos de TI</p>
        </div>

        <button wire:click="novoUsuario" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 font-bold text-slate-950 text-sm transition shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2 cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 0112 0v1H3v-1z" />
            </svg>
            Novo Usuário
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

    <!-- Users Table (Desktop) & Cards (Mobile) -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl overflow-hidden">
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-950/60 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Usuário / Nome</th>
                        <th class="px-6 py-4">Usuário</th>
                        <th class="px-6 py-4">Perfil (Role)</th>
                        <th class="px-6 py-4">Data de Cadastro</th>
                        <th class="px-6 py-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @foreach($users as $user)
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
                            <td class="px-6 py-4">
                                @if($user->isDiretor())
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">Diretor</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">Administrador</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500 dark:text-slate-400">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap items-center justify-end gap-2.5">
                                    <button wire:click="editarUsuario({{ $user->id }})" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-semibold border border-emerald-500/20 cursor-pointer">
                                        Editar
                                    </button>
                                    @if($user->id !== auth()->id())
                                        <button wire:click="inativarUsuario({{ $user->id }})"
                                                wire:confirm="Deseja desativar a conta do usuário '{{ $user->name }}'?"
                                                class="px-3 py-1.5 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-semibold border border-red-500/20 cursor-pointer">
                                            Desativar
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Stacked Cards View -->
        <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
            @foreach($users as $user)
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
                        @else
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">Admin</span>
                        @endif
                    </div>

                    <div class="pt-2 flex items-center justify-between border-t border-slate-100 dark:border-slate-800">
                        <span class="text-[11px] text-slate-400">Cadastrado em {{ $user->created_at->format('d/m/Y') }}</span>
                        <div class="flex flex-wrap items-center gap-2.5">
                            <button wire:click="editarUsuario({{ $user->id }})" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-semibold border border-emerald-500/20 cursor-pointer">
                                Editar
                            </button>
                            @if($user->id !== auth()->id())
                                <button wire:click="inativarUsuario({{ $user->id }})"
                                        wire:confirm="Deseja desativar a conta do usuário '{{ $user->name }}'?"
                                        class="px-3 py-1.5 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-semibold border border-red-500/20 cursor-pointer">
                                    Desativar
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal Form (User Creation / Edit) -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-md p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">
                    {{ $userId ? 'Editar Usuário' : 'Novo Usuário' }}
                </h3>
                <button type="button" wire:click="$set('showModal', false)" title="Fechar formulário" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100 bg-slate-200/80 hover:bg-slate-300/80 dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-300/60 dark:border-slate-700 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Sair</span>
                </button>
            </div>

            <form wire:submit="salvar" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Nome Completo *</label>
                    <input wire:model="name" type="text" placeholder="Nome do servidor/técnico"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                    @error('name') <span class="text-xs text-red-500 dark:text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Usuário de Acesso *</label>
                    <input wire:model="username" type="text" placeholder="ex: dereck ou maciel"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                    @error('username') <span class="text-xs text-red-500 dark:text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        {{ $userId ? 'Nova Senha (deixe em branco para manter)' : 'Senha de Acesso *' }}
                    </label>
                    <input wire:model="password" type="password" placeholder="••••••••"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                    @error('password') <span class="text-xs text-red-500 dark:text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Perfil de Acesso (Role) *</label>
                    <select wire:model="role" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                        <option value="administrador">Administrador</option>
                        <option value="diretor">Diretor</option>
                    </select>
                </div>

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
