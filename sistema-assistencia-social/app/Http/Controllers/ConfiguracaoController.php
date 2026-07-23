<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ConfiguracaoController extends Controller
{
    /**
     * Exibe a página de configurações.
     */
    public function index(): View
    {
        return view('configuracoes.index');
    }

    /**
     * Altera a senha do próprio usuário.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'senha_atual' => ['required', 'string'],
            'nova_senha' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'nova_senha.min' => 'A nova senha deve ter no mínimo :min caracteres.',
            'nova_senha.confirmed' => 'A confirmação da nova senha não confere.',
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (! Hash::check($request->senha_atual, $user->senha)) {
            throw ValidationException::withMessages([
                'senha_atual' => ['A senha atual informada está incorreta.'],
            ]);
        }

        $user->senha = Hash::make($request->nova_senha);
        $user->save();

        return redirect()->route('configuracoes')->with('success', 'Senha alterada com sucesso!');
    }

    /**
     * Altera o tema na sessão (chamado via AJAX/Fetch).
     */
    public function updateTheme(Request $request)
    {
        $request->validate([
            'theme' => ['required', 'string', 'in:light,dark'],
        ]);

        session(['theme' => $request->theme]);

        return response()->json([
            'status' => 'success',
            'theme' => $request->theme,
        ]);
    }
}
