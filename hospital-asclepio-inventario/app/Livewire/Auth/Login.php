<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.guest')]
#[Title('Acesso ao Sistema')]
class Login extends Component
{
    public string $username = '';

    public string $password = '';

    public bool $remember = false;

    protected function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function login(): void
    {
        $this->validate($this->rules());

        $authenticated = Auth::attempt(['username' => $this->username, 'password' => $this->password], $this->remember);

        if (! $authenticated) {
            $user = User::where(function ($query) {
                $query->whereRaw('LOWER(username) = ?', [mb_strtolower($this->username)])
                    ->orWhereRaw("REPLACE(REPLACE(LOWER(username), 'á', 'a'), 'ã', 'a') = ?", [mb_strtolower(str_replace(['á', 'ã'], 'a', $this->username))]);
            })->first();

            if ($user && Auth::attempt(['username' => $user->username, 'password' => $this->password], $this->remember)) {
                $authenticated = true;
            }
        }

        if (! $authenticated) {
            throw ValidationException::withMessages([
                'username' => ('Usuário ou senha incorretos'),
            ]);
        }

        session()->regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
