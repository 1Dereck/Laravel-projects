<?php

namespace App\Http\Controllers;

use App\Models\Acolhimento;
use App\Models\Observacao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ObservacaoController extends Controller
{
    /**
     * Grava uma observação/evolução para o acolhido (Admin apenas).
     */
    public function store(Request $request, int $acolhimentoId): RedirectResponse
    {
        Gate::authorize('edit-data');

        $acolhimento = Acolhimento::findOrFail($acolhimentoId);

        $request->validate([
            'descricao' => ['required', 'string'],
            'tipo' => ['required', 'string', 'in:s,n'], // s - sigiloso, n - não
        ]);

        $observacao = new Observacao;
        $observacao->id_acolhimento = $acolhimento->id_acolhimento;
        $observacao->descricao = $request->input('descricao');
        $observacao->tipo = $request->input('tipo');
        $observacao->id_usuario = Auth::id();
        $observacao->id_assunto = 0; // Padrão legado
        $observacao->save();

        // Atualiza o registro de alteração do acolhido
        $acolhimento->id_usuario_alteracao = Auth::id();
        $acolhimento->save();

        return back()->with('success', 'Evolução registrada com sucesso!');
    }
}
