<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Exibe a página de gerenciamento de usuários.
     */
    public function index(Request $request): View
    {
        Gate::authorize('manage-users');

        /** @var User $userLogado */
        $userLogado = Auth::user();
        $diretores = [];
        $users = [];

        if ($userLogado->isDiretor()) {
            $diretores = User::where('permissao', 'd')->orderBy('nome_usu')->get();
            $users = User::where('permissao', 'a')->orderBy('nome_usu')->get();
        }

        $search = $request->input('search_user');

        if ($search) {
            $query = User::whereNotIn('permissao', ['a', 'd']);
            $query->where(function ($q) use ($search) {
                $q->where('nome_usu', 'like', "%{$search}%")
                    ->orWhere('login', 'like', "%{$search}%");
            });
            $commonUsers = $query->orderBy('nome_usu')->paginate(10, ['*'], 'users_page')->withQueryString();
        } else {
            // Em caso de não haver pesquisa, retorna paginação vazia
            $commonUsers = User::whereRaw('1 = 0')->paginate(10, ['*'], 'users_page');
        }

        return view('usuarios.index', compact('diretores', 'users', 'commonUsers', 'search'));
    }

    /**
     * Cria um novo usuário no sistema.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-users');

        $allowedPermissions = ['n'];
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if ($currentUser->isDiretor()) {
            $allowedPermissions = ['d', 'a', 'n'];
        }

        $request->validate([
            'login' => ['required', 'string', 'max:50', 'unique:users,login'],
            'nome_usu' => ['required', 'string', 'max:150'],
            'senha' => ['required', 'string', 'min:6'],
            'permissao' => ['required', 'string', 'in:'.implode(',', $allowedPermissions)],
            'tipo_acesso' => ['required', 'string', 'in:s,n'],
        ], [
            'login.unique' => 'Este login já está em uso.',
            'senha.min' => 'A senha deve ter pelo menos :min caracteres.',
            'permissao.in' => 'Nível de permissão inválido para o seu perfil.',
        ]);

        $user = new User;
        $user->login = $request->login;
        $user->nome_usu = $request->nome_usu;
        $user->senha = Hash::make($request->senha);
        $user->permissao = $request->permissao;
        $user->tipo_acesso = $request->tipo_acesso;
        $user->id_usuario_alteracao = Auth::id();
        $user->ativo = 's';
        $user->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Exclui um usuário do sistema (apenas Diretor pode excluir, e não pode excluir a si mesmo).
     */
    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('delete-user');

        if ($user->id_usuario === Auth::id()) {
            return redirect()->route('usuarios.index')->with('error', 'Você não pode excluir a sua própria conta.');
        }

        $user->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuário excluído com sucesso!');
    }
}
