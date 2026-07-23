<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Exibe o formulário de login.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Processa a tentativa de autenticação.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'senha' => ['required', 'string'],
        ]);

        // Verificamos se o usuário existe e se está ativo ('s')
        // Passamos 'senha' como a chave que o Laravel usará para recuperar a senha plana,
        // mas note que como o EloquentUserProvider espera o campo 'password' por padrão,
        // podemos mapear para 'password' internamente no Auth::attempt.
        $attempt = Auth::attempt([
            'login' => $credentials['login'],
            'password' => $credentials['senha'],
            'ativo' => 's',
        ], $request->boolean('remember'));

        if ($attempt) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        throw ValidationException::withMessages([
            'login' => [__('auth.failed')],
        ]);
    }

    /**
     * Finaliza a sessão do usuário.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
