<?php

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
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

    /**
     * @return array<string, array<int, string>>
     */
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

        if (! Auth::attempt(['username' => $this->username, 'password' => $this->password], $this->remember)) {
            throw ValidationException::withMessages([
                'username' => __('As credenciais fornecidas estão incorretas ou o usuário não existe.'),
            ]);
        }

        session()->regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.auth.login');
    }
}
