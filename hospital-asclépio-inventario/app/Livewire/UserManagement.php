<?php

namespace App\Livewire;

use App\Models\Local;
use App\Models\Secretaria;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Gestão de Usuários')]
class UserManagement extends Component
{
    use AuthorizesRequests, WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $activeTab = 'usuario';

    public bool $showModal = false;

    public ?int $userId = null;

    public string $name = '';

    public string $username = '';

    public string $password = '';

    public string $role = 'usuario';

    public ?int $secretaria_id = null;

    public ?int $setor_id = null;

    public function mount(): void
    {
        $user = auth()->user();

        if ($user->isUsuario()) {
            abort(403, 'Acesso não autorizado ao cadastro de usuários.');
        }

        if ($user->isCoordenador()) {
            $this->activeTab = 'usuario';
        } elseif ($user->isAdmin() && ! in_array($this->activeTab, ['administrador', 'coordenador'], true)) {
            $this->activeTab = 'administrador';
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRole($val): void
    {
        if ($val === 'administrador') {
            $this->secretaria_id = null;
            $this->setor_id = null;
        } elseif ($val === 'coordenador') {
            $this->setor_id = null;
        }
    }

    public function updatedSecretariaId($val): void
    {
        if ($this->setor_id && $val) {
            $local = Local::find($this->setor_id);
            if ($local && $local->secretaria_id && (int) $local->secretaria_id !== (int) $val) {
                $this->setor_id = null;
            }
        }
    }

    public function updatedSetorId($val): void
    {
        if ($val) {
            $local = Local::find($val);
            if ($local && $local->secretaria_id) {
                $this->secretaria_id = (int) $local->secretaria_id;
            }
        }
    }

    public function setTab(string $tab): void
    {
        $user = auth()->user();

        if ($user->isDiretor() && in_array($tab, ['diretor', 'administrador', 'coordenador', 'usuario'], true)) {
            $this->activeTab = $tab;
            $this->resetPage();
        } elseif ($user->isAdmin() && in_array($tab, ['administrador', 'coordenador'], true)) {
            $this->activeTab = $tab;
            $this->resetPage();
        } elseif ($user->isCoordenador() && $tab === 'usuario') {
            $this->activeTab = 'usuario';
            $this->resetPage();
        }
    }

    protected function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,'.$this->userId],
            'password' => [$this->userId ? 'nullable' : 'required', 'string', 'min:6'],
            'role' => ['required', 'in:diretor,administrador,coordenador,usuario'],
        ];

        if ($this->role === 'coordenador') {
            if ($this->userId && ($this->setor_id || $this->secretaria_id)) {
                $rules['secretaria_id'] = ['nullable', 'integer', 'exists:secretarias,id_secretarias'];
            } else {
                $rules['secretaria_id'] = ['required', 'integer', 'exists:secretarias,id_secretarias'];
            }
        } elseif ($this->role === 'usuario') {
            if ($this->userId && $this->setor_id) {
                $rules['secretaria_id'] = ['nullable', 'integer', 'exists:secretarias,id_secretarias'];
            } else {
                $rules['secretaria_id'] = ['required', 'integer', 'exists:secretarias,id_secretarias'];
            }
            $rules['setor_id'] = ['required', 'integer', 'exists:local,id_local'];
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'password.required' => 'Informe a senha de acesso.',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
            'secretaria_id.required' => 'Selecione o Setor do usuário.',
            'setor_id.required' => 'Selecione o Local de Alocação do usuário.',
        ];
    }

    public function novoUsuario(): void
    {
        $this->reset(['userId', 'name', 'username', 'password', 'role', 'secretaria_id', 'setor_id']);
        $user = auth()->user();

        if ($user->isCoordenador()) {
            $this->role = 'usuario';
            $this->secretaria_id = $user->setor?->secretaria_id;
        } elseif ($user->isAdmin()) {
            $this->role = in_array($this->activeTab, ['administrador', 'coordenador'], true) ? $this->activeTab : 'administrador';
        } else {
            $this->role = in_array($this->activeTab, ['diretor', 'administrador', 'coordenador', 'usuario'], true) ? $this->activeTab : 'usuario';
        }

        $this->resetValidation();
        $this->showModal = true;
    }

    public function editarUsuario(User $user): void
    {
        $currentUser = auth()->user();

        if ($currentUser->isAdmin() && ! in_array($user->role, ['administrador', 'coordenador'], true)) {
            session()->flash('error', 'Administradores só têm permissão para editar contas de Administrador ou Coordenador.');

            return;
        }

        if ($currentUser->isCoordenador() && (! $user->isUsuario() || ! $currentUser->belongsToSameSector($user))) {
            session()->flash('error', 'Coordenadores só têm permissão para editar contas de Usuários do seu próprio setor.');

            return;
        }

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->role = $user->role;
        $this->setor_id = $user->setor_id ? (int) $user->setor_id : null;

        $secretariaId = $user->setor?->secretaria_id;

        if (! $secretariaId && $user->setor) {
            $localName = trim($user->setor->local ?? '');
            if ($localName !== '') {
                $sec = Secretaria::where('secretaria', $localName)
                    ->orWhere('nome_extenso', $localName)
                    ->first();
                if ($sec) {
                    $secretariaId = $sec->id_secretarias;
                    $user->setor->update(['secretaria_id' => $secretariaId]);
                }
            }
        }

        $this->secretaria_id = $secretariaId ? (int) $secretariaId : null;
        $this->password = '';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function salvar(): void
    {
        $currentUser = auth()->user();

        if ($currentUser->isAdmin()) {
            if ($this->role === 'diretor') {
                abort(403, 'Administradores não possuem permissão para criar contas de Diretor.');
            }
            if (! in_array($this->role, ['administrador', 'coordenador'], true)) {
                $this->role = 'administrador';
            }
        } elseif ($currentUser->isCoordenador()) {
            if ($this->role !== 'usuario') {
                abort(403, 'Coordenadores só possuem permissão para criar contas de Usuário.');
            }
            $this->secretaria_id = $currentUser->setor?->secretaria_id;
        }

        $this->validate($this->rules(), $this->messages());

        $finalSetorId = null;

        if ($this->role === 'administrador') {
            $tiLocal = Local::firstOrCreate(
                ['local' => 'T.I'],
                ['status' => 'Ativo', 'ultima_atualizacao' => now()]
            );
            $finalSetorId = $tiLocal->id_local;
        } elseif ($this->role === 'coordenador') {
            if ($this->setor_id) {
                $local = Local::find($this->setor_id);
                if ($local && $this->secretaria_id && (int) $local->secretaria_id !== (int) $this->secretaria_id) {
                    $local->update(['secretaria_id' => $this->secretaria_id]);
                }
                $finalSetorId = $local?->id_local;
            }

            if (! $finalSetorId && $this->secretaria_id) {
                $local = Local::where('secretaria_id', $this->secretaria_id)->first();
                if (! $local) {
                    $sec = Secretaria::find($this->secretaria_id);
                    $local = Local::create([
                        'local' => $sec ? $sec->secretaria : 'Sede',
                        'secretaria_id' => $this->secretaria_id,
                        'status' => 'Ativo',
                        'ultima_atualizacao' => now(),
                    ]);
                }
                $finalSetorId = $local->id_local;
            }
        } elseif ($this->role === 'usuario') {
            $finalSetorId = $this->setor_id;
            if ($finalSetorId && $this->secretaria_id) {
                $localObj = Local::find($finalSetorId);
                if ($localObj && (int) $localObj->secretaria_id !== (int) $this->secretaria_id) {
                    $localObj->update(['secretaria_id' => $this->secretaria_id]);
                }
            }
        }

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $data = [
                'name' => $this->name,
                'username' => $this->username,
                'role' => $this->role,
                'setor_id' => $finalSetorId,
            ];
            if (! empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }
            $user->update($data);
            session()->flash('message', 'Usuário atualizado com sucesso!');
        } else {
            User::create([
                'name' => $this->name,
                'username' => $this->username,
                'password' => Hash::make($this->password),
                'role' => $this->role,
                'setor_id' => $finalSetorId,
                'created_by' => auth()->id(),
            ]);
            session()->flash('message', 'Novo usuário cadastrado com sucesso!');
        }

        $this->showModal = false;
        $this->reset(['userId', 'name', 'username', 'password', 'role', 'secretaria_id', 'setor_id']);
    }

    public function inativarUsuario(User $user): void
    {
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Você não pode excluir seu próprio usuário.');

            return;
        }

        $currentUser = auth()->user();

        if ($currentUser->isDiretor()) {
            $user->forceDelete();
            session()->flash('message', 'Conta de usuário excluída permanentemente.');
        } elseif ($currentUser->isAdmin()) {
            if (! in_array($user->role, ['administrador', 'coordenador'], true)) {
                session()->flash('error', 'Administradores só têm permissão para excluir contas de Administrador ou Coordenador.');

                return;
            }
            $user->delete();
            session()->flash('message', 'Conta de usuário desativada/arquivada com sucesso.');
        } elseif ($currentUser->isCoordenador()) {
            if (! $user->isUsuario() || ! $currentUser->belongsToSameSector($user)) {
                session()->flash('error', 'Coordenadores só têm permissão para excluir contas de Usuários do seu próprio setor.');

                return;
            }
            $user->delete();
            session()->flash('message', 'Conta de usuário desativada/arquivada com sucesso.');
        } else {
            abort(403, 'Ação não autorizada.');
        }
    }

    public function render()
    {
        $currentUser = auth()->user();

        $query = User::query()
            ->with(['setor.secretaria'])
            ->where('role', $this->activeTab)
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('username', 'like', '%'.$this->search.'%')
                        ->orWhereHas('setor', function ($s) {
                            $s->where('local', 'like', '%'.$this->search.'%')
                                ->orWhereHas('secretaria', fn ($sec) => $sec->where('secretaria', 'like', '%'.$this->search.'%')->orWhere('nome_extenso', 'like', '%'.$this->search.'%'));
                        });
                });
            });

        if ($currentUser->isCoordenador()) {
            $query->whereIn('setor_id', $currentUser->getSectorLocalIds());
        }

        $users = $query->latest()->paginate(10);

        $secretarias = Secretaria::orderBy('secretaria')->get();

        $locaisQuery = Local::orderBy('local');
        if ($this->secretaria_id) {
            $locaisQuery->where(function ($q) {
                $q->where('secretaria_id', $this->secretaria_id)
                    ->orWhereNull('secretaria_id');
                if ($this->setor_id) {
                    $q->orWhere('id_local', $this->setor_id);
                }
            });
        }
        $locais = $locaisQuery->get();

        $countUsuarios = $currentUser->isCoordenador()
            ? User::where('role', 'usuario')->whereIn('setor_id', $currentUser->getSectorLocalIds())->count()
            : User::where('role', 'usuario')->count();

        return view('livewire.user-management', [
            'users' => $users,
            'secretarias' => $secretarias,
            'locais' => $locais,
            'countDiretores' => User::where('role', 'diretor')->count(),
            'countAdmins' => User::where('role', 'administrador')->count(),
            'countCoordenadores' => User::where('role', 'coordenador')->count(),
            'countUsuarios' => $countUsuarios,
        ]);
    }
}
