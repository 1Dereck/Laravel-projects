<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Gestão de Usuários')]
class UserManagement extends Component
{
    use AuthorizesRequests;

    public bool $showModal = false;

    public ?int $userId = null;

    public string $name = '';

    public string $username = '';

    public string $password = '';

    public string $role = 'administrador';

    public function mount(): void
    {
        if (! auth()->user()->isDiretor()) {
            abort(403, 'Acesso restrito ao perfil de Diretor.');
        }
    }

    /**
     * @return array<string, array<int, string>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,'.$this->userId],
            'password' => [$this->userId ? 'nullable' : 'required', 'string', 'min:6'],
            'role' => ['required', 'in:diretor,administrador'],
        ];
    }

    public function novoUsuario(): void
    {
        $this->reset(['userId', 'name', 'username', 'password', 'role']);
        $this->role = 'administrador';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function editarUsuario(User $user): void
    {
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->role = $user->role;
        $this->password = '';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function salvar(): void
    {
        $this->validate($this->rules());

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $data = [
                'name' => $this->name,
                'username' => $this->username,
                'role' => $this->role,
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
                'created_by' => auth()->id(),
            ]);
            session()->flash('message', 'Novo usuário cadastrado com sucesso!');
        }

        $this->showModal = false;
        $this->reset(['userId', 'name', 'username', 'password', 'role']);
    }

    public function inativarUsuario(User $user): void
    {
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Você não pode desativar seu próprio usuário.');

            return;
        }

        $user->delete();
        session()->flash('message', 'Conta de usuário desativada com sucesso.');
    }

    public function render(): View
    {
        $users = User::latest()->get();

        return view('livewire.user-management', [
            'users' => $users,
        ]);
    }
}
