<?php

namespace App\Http\Controllers;

use App\Models\Acolhimento;
use App\Models\Estado;
use App\Models\SolicitacaoArquivo;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AcolhimentoController extends Controller
{
    /**
     * Listagem de acolhidos com pesquisa unificada e paginação.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $query = Acolhimento::query();

        if ($search) {
            $cleanSearch = preg_replace('/[^0-9]/', '', $search);
            $query->where(function ($q) use ($search, $cleanSearch) {
                $q->where('nome_pessoa', 'like', "%{$search}%")
                    ->orWhere('nome_social', 'like', "%{$search}%")
                    ->orWhere('rg', 'like', "%{$search}%");

                if (! empty($cleanSearch)) {
                    $q->orWhere('cpf', 'like', "%{$cleanSearch}%")
                        ->orWhere('cpf', 'like', "%{$search}%");
                }
            });
        } else {
            $query->where('oculto', '!=', 's');
        }

        $acolhimentos = $query->orderBy('nome_pessoa')->paginate(15)->withQueryString();

        return view('acolhimentos.index', compact('acolhimentos', 'search'));
    }

    /**
     * Exibe o formulário de cadastro de acolhido (Admin apenas).
     */
    public function create(): View
    {
        Gate::authorize('edit-data');

        $estados = Estado::orderBy('nome')->get();

        return view('acolhimentos.create', compact('estados'));
    }

    /**
     * Grava o novo acolhido no banco (Admin apenas).
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('edit-data');

        $validated = $request->validate([
            'nome_pessoa' => ['required', 'string', 'max:100'],
            'nome_social' => ['nullable', 'string', 'max:100'],
            'cpf' => ['required', 'string', 'max:14'], // Armazenado no formato enviado
            'rg' => ['nullable', 'string', 'max:100'],
            'dt_nascimento' => ['required', 'date'],
            'naturalidade' => ['nullable', 'string', 'max:50'],
            'estado_nasc' => ['nullable', 'string', 'max:2'],
            'nec_especial' => ['nullable', 'string', 'in:Sim,Não'],
            'tipo_nec_especial' => ['nullable', 'string', 'max:100'],
            'depend_quimica' => ['nullable', 'string', 'in:Sim,Não'],
            'tipo_dep_quimica' => ['nullable', 'string', 'max:100'],
            'transtorno' => ['nullable', 'string', 'in:Sim,Não'],
            'tipo_transtorno' => ['nullable', 'string', 'max:100'],
            'recebe_beneficio' => ['nullable', 'string', 'in:Sim,Não'],
            'tipo_beneficio' => ['nullable', 'string', 'max:150'],
            'cid_bairro_situacao' => ['nullable', 'string', 'max:200'],
            'pai' => ['nullable', 'string', 'max:100'],
            'mae' => ['nullable', 'string', 'max:100'],
            'parente_nome' => ['nullable', 'string', 'max:100'],
            'parente_grau' => ['nullable', 'string', 'max:50'],
            'parente_end' => ['nullable', 'string', 'max:150'],
            'parente_grau1' => ['nullable', 'string', 'max:50'],
            'parente_end1' => ['nullable', 'string', 'max:150'],
            'monitoramento' => ['nullable', 'string', 'in:Sim,Não'],
            'obs_pessoa' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:4096'],
            'webcam_image' => ['nullable', 'string'],
        ]);

        // Trata os campos char(3) do legado que salvam Sim/Não
        $data = $validated;
        // Remove file and webcam fields so they don't get saved in the main create query
        unset($data['foto'], $data['webcam_image']);

        // Remove formatação do CPF para salvar apenas números
        $data['cpf'] = preg_replace('/[^0-9]/', '', $request->cpf);
        if (isset($data['rg'])) {
            $data['rg'] = $data['rg'] !== null ? preg_replace('/[^0-9a-zA-Z]/', '', $data['rg']) : null;
        }

        $acolhimento = new Acolhimento($data);
        $acolhimento->dt_cadastro = now()->format('Y-m-d');
        $acolhimento->id_tecnico_resp = Auth::id();
        $acolhimento->id_usuario_alteracao = Auth::id();
        $acolhimento->save();

        // Processa upload de foto (se houver)
        $filename = 'foto_'.$acolhimento->id_acolhimento.'.jpg';
        $path = 'fotos/'.$filename;
        $updatedFotoData = [];

        if ($request->filled('webcam_image')) {
            $imageData = $request->input('webcam_image');
            if (Str::startsWith($imageData, 'data:image')) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
            }
            $decodedImage = base64_decode($imageData);
            Storage::disk('public')->put($path, $decodedImage);

            $updatedFotoData = [
                'nome_foto' => $filename,
                'nome_cript' => $filename,
            ];
        } elseif ($request->hasFile('foto')) {
            $file = $request->file('foto');
            Storage::disk('public')->putFileAs('fotos', $file, $filename);

            $updatedFotoData = [
                'nome_foto' => $file->getClientOriginalName(),
                'nome_cript' => $filename,
            ];
        }

        if (! empty($updatedFotoData)) {
            $acolhimento->nome_foto = $updatedFotoData['nome_foto'];
            $acolhimento->nome_cript = $updatedFotoData['nome_cript'];
            $acolhimento->save();
        }

        return redirect()->route('acolhimentos.show', $acolhimento->id_acolhimento)
            ->with('success', 'Cadastro realizado com sucesso!');
    }

    /**
     * Exibe o detalhe do acolhimento.
     */
    public function show(int $id): View
    {
        $acolhimento = Acolhimento::findOrFail($id);
        /** @var User $user */
        $user = Auth::user();

        // Controle de acesso sigiloso
        $canSeeSigiloso = ($user->tipo_acesso === 's' || $user->isAdmin());

        // Carrega observações (se não puder ver sigilosas, filtra tipo != 's')
        $observacoesQuery = $acolhimento->observacoes()->with('usuario')->orderBy('ultima_data', 'desc');
        if (! $canSeeSigiloso) {
            $observacoesQuery->where('tipo', '!=', 's');
        }
        $observacoes = $observacoesQuery->get();

        // Carrega arquivos anexos (se não puder ver sigilosos, filtra tipo != 's')
        $arquivosQuery = $acolhimento->arquivos()->where('cancelado', 0)->orderBy('data_inclusao', 'desc');
        if (! $canSeeSigiloso) {
            $arquivosQuery->where('tipo', '!=', 's');
        }
        $arquivos = $arquivosQuery->get();

        return view('acolhimentos.show', compact('acolhimento', 'observacoes', 'arquivos', 'canSeeSigiloso'));
    }

    /**
     * Gera uma visualização otimizada para salvar como PDF (Dossiê).
     */
    public function gerarPdf(int $id): View
    {
        $acolhimento = Acolhimento::findOrFail($id);
        /** @var User $user */
        $user = Auth::user();

        // Controle de acesso sigiloso
        $canSeeSigiloso = ($user->tipo_acesso === 's' || $user->isAdmin());

        // Carrega observações (se não puder ver sigilosas, filtra tipo != 's')
        $observacoesQuery = $acolhimento->observacoes()->with('usuario')->orderBy('ultima_data', 'desc');
        if (! $canSeeSigiloso) {
            $observacoesQuery->where('tipo', '!=', 's');
        }
        $observacoes = $observacoesQuery->get();

        // Carrega arquivos anexos (se não puder ver sigilosos, filtra tipo != 's')
        $arquivosQuery = $acolhimento->arquivos()->where('cancelado', 0)->orderBy('data_inclusao', 'desc');
        if (! $canSeeSigiloso) {
            $arquivosQuery->where('tipo', '!=', 's');
        }
        $arquivos = $arquivosQuery->get();

        return view('acolhimentos.pdf', compact('acolhimento', 'observacoes', 'arquivos', 'canSeeSigiloso'));
    }

    /**
     * Exibe o formulário de edição de acolhido (Admin apenas).
     */
    public function edit(int $id): View
    {
        Gate::authorize('edit-data');

        $acolhimento = Acolhimento::findOrFail($id);
        $estados = Estado::orderBy('nome')->get();
        /** @var User $user */
        $user = Auth::user();

        // Controle de acesso sigiloso
        $canSeeSigiloso = ($user->tipo_acesso === 's' || $user->isAdmin());

        // Carrega observações (se não puder ver sigilosas, filtra tipo != 's')
        $observacoesQuery = $acolhimento->observacoes()->with('usuario')->orderBy('ultima_data', 'desc');
        if (! $canSeeSigiloso) {
            $observacoesQuery->where('tipo', '!=', 's');
        }
        $observacoes = $observacoesQuery->get();

        // Carrega arquivos anexos (se não puder ver sigilosos, filtra tipo != 's')
        $arquivosQuery = $acolhimento->arquivos()->where('cancelado', 0)->orderBy('data_inclusao', 'desc');
        if (! $canSeeSigiloso) {
            $arquivosQuery->where('tipo', '!=', 's');
        }
        $arquivos = $arquivosQuery->get();

        return view('acolhimentos.edit', compact('acolhimento', 'estados', 'observacoes', 'arquivos', 'canSeeSigiloso'));
    }

    /**
     * Atualiza o acolhido no banco (Admin apenas).
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        Gate::authorize('edit-data');

        $acolhimento = Acolhimento::findOrFail($id);

        $validated = $request->validate([
            'nome_pessoa' => ['required', 'string', 'max:100'],
            'nome_social' => ['nullable', 'string', 'max:100'],
            'cpf' => ['required', 'string', 'max:14'],
            'rg' => ['nullable', 'string', 'max:100'],
            'dt_nascimento' => ['required', 'date'],
            'naturalidade' => ['nullable', 'string', 'max:50'],
            'estado_nasc' => ['nullable', 'string', 'max:2'],
            'nec_especial' => ['nullable', 'string', 'in:Sim,Não'],
            'tipo_nec_especial' => ['nullable', 'string', 'max:100'],
            'depend_quimica' => ['nullable', 'string', 'in:Sim,Não'],
            'tipo_dep_quimica' => ['nullable', 'string', 'max:100'],
            'transtorno' => ['nullable', 'string', 'in:Sim,Não'],
            'tipo_transtorno' => ['nullable', 'string', 'max:100'],
            'recebe_beneficio' => ['nullable', 'string', 'in:Sim,Não'],
            'tipo_beneficio' => ['nullable', 'string', 'max:150'],
            'cid_bairro_situacao' => ['nullable', 'string', 'max:200'],
            'pai' => ['nullable', 'string', 'max:100'],
            'mae' => ['nullable', 'string', 'max:100'],
            'parente_nome' => ['nullable', 'string', 'max:100'],
            'parente_grau' => ['nullable', 'string', 'max:50'],
            'parente_end' => ['nullable', 'string', 'max:150'],
            'parente_grau1' => ['nullable', 'string', 'max:50'],
            'parente_end1' => ['nullable', 'string', 'max:150'],
            'monitoramento' => ['nullable', 'string', 'in:Sim,Não'],
            'obs_pessoa' => ['nullable', 'string'],
        ]);

        $data = $validated;
        $data['id_usuario_alteracao'] = Auth::id();
        $data['cpf'] = preg_replace('/[^0-9]/', '', $request->cpf);
        if (isset($data['rg'])) {
            $data['rg'] = $data['rg'] !== null ? preg_replace('/[^0-9a-zA-Z]/', '', $data['rg']) : null;
        }

        $acolhimento->fill($data);
        $acolhimento->id_usuario_alteracao = Auth::id();
        $acolhimento->save();

        return redirect()->route('acolhimentos.show', $acolhimento->id_acolhimento)
            ->with('success', 'Cadastro atualizado com sucesso!');
    }

    /**
     * Processa o upload de foto (seja webcam base64 ou seletor de arquivo) (Admin apenas).
     */
    public function uploadFoto(Request $request, int $id): JsonResponse|RedirectResponse
    {
        Gate::authorize('edit-data');

        $acolhimento = Acolhimento::findOrFail($id);

        // Se houver uma foto antiga salva no disco, nós a removemos
        $filename = 'foto_'.$acolhimento->id_acolhimento.'.jpg';
        $path = 'fotos/'.$filename;

        if ($request->filled('webcam_image')) {
            // Captura da câmera via webcam (base64)
            $imageData = $request->input('webcam_image');

            // Remove o cabeçalho base64 data:image/jpeg;base64,
            if (Str::startsWith($imageData, 'data:image')) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
            }

            $decodedImage = base64_decode($imageData);

            // Salva no storage público
            Storage::disk('public')->put($path, $decodedImage);

            // Atualiza colunas no banco
            $acolhimento->nome_foto = $filename;
            $acolhimento->nome_cript = $filename;
            $acolhimento->id_usuario_alteracao = Auth::id();
            $acolhimento->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Foto atualizada com sucesso via Webcam!',
                'url' => asset('storage/'.$path).'?t='.time(),
            ]);
        }

        if ($request->hasFile('foto')) {
            // Seletor de arquivo tradicional
            if (! extension_loaded('fileinfo')) {
                $validator = Validator::make($request->all(), []);
                $validator->after(function ($validator) use ($request) {
                    $file = $request->file('foto');
                    if (! $file) {
                        $validator->errors()->add('foto', 'O campo foto é obrigatório.');

                        return;
                    }
                    $extension = strtolower($file->getClientOriginalExtension());
                    $allowedExtensions = ['jpeg', 'jpg', 'png'];
                    if (! in_array($extension, $allowedExtensions)) {
                        $validator->errors()->add('foto', 'O arquivo deve ser uma imagem do tipo: jpeg, jpg, png.');
                    }
                    if ($file->getSize() > 4096 * 1024) {
                        $validator->errors()->add('foto', 'O arquivo não pode ser maior que 4MB.');
                    }
                });
                $validator->validate();
            } else {
                $request->validate([
                    'foto' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:4096'],
                ]);
            }

            $file = $request->file('foto');

            // Salva substituindo/sobrescrevendo com nome padronizado
            Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

            $acolhimento->nome_foto = $file->getClientOriginalName();
            $acolhimento->nome_cript = $filename;
            $acolhimento->id_usuario_alteracao = Auth::id();
            $acolhimento->save();

            return back()->with('success', 'Foto atualizada com sucesso!');
        }

        return back()->with('error', 'Nenhuma imagem foi recebida.');
    }

    /**
     * Adiciona anexo de documentos (Admin apenas).
     */
    public function uploadArquivo(Request $request, int $id): RedirectResponse
    {
        Gate::authorize('edit-data');

        $acolhimento = Acolhimento::findOrFail($id);

        if (! extension_loaded('fileinfo')) {
            $validator = Validator::make($request->all(), [
                'observacao' => ['nullable', 'string', 'max:512'],
                'tipo' => ['required', 'string', 'in:s,n'], // s - sigiloso, n - não
            ]);
            $validator->after(function ($validator) use ($request) {
                if (! $request->hasFile('documento')) {
                    $validator->errors()->add('documento', 'O campo documento é obrigatório.');

                    return;
                }
                $file = $request->file('documento');
                $extension = strtolower($file->getClientOriginalExtension());
                $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
                if (! in_array($extension, $allowedExtensions)) {
                    $validator->errors()->add('documento', 'Formatos permitidos: PDF, Word (DOC/DOCX) ou Imagens (JPG/PNG).');
                }
                if ($file->getSize() > 10240 * 1024) {
                    $validator->errors()->add('documento', 'O tamanho máximo permitido para o arquivo é 10MB.');
                }
            });
            $validator->validate();
        } else {
            $request->validate([
                'documento' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
                'observacao' => ['nullable', 'string', 'max:512'],
                'tipo' => ['required', 'string', 'in:s,n'], // s - sigiloso, n - não
            ], [
                'documento.mimes' => 'Formatos permitidos: PDF, Word (DOC/DOCX) ou Imagens (JPG/PNG).',
                'documento.max' => 'O tamanho máximo permitido para o arquivo é 10MB.',
            ]);
        }

        $file = $request->file('documento');
        $originalName = $file->getClientOriginalName();

        // Gera um nome único para evitar colisões
        $cryptName = Str::uuid().'.'.$file->getClientOriginalExtension();

        // Salva no storage (pasta local/documentos)
        Storage::disk('local')->put('documentos/'.$cryptName, file_get_contents($file->getRealPath()));

        $arquivo = new SolicitacaoArquivo;
        $arquivo->id_solicitacao = $acolhimento->id_acolhimento;
        $arquivo->observacao = $request->input('observacao') ?? '';
        $arquivo->nome_arquivo = $originalName;
        $arquivo->tipo_md5 = $cryptName;
        $arquivo->tipo = $request->input('tipo');
        $arquivo->q_enviou = Auth::user()->nome_usu ?? Auth::user()->login;
        $arquivo->cancelado = 0;
        $arquivo->save();

        // Log de alteração do cadastro
        $acolhimento->id_usuario_alteracao = Auth::id();
        $acolhimento->save();

        return back()->with('success', 'Documento anexado com sucesso!');
    }

    /**
     * Faz download de um documento anexado.
     */
    public function downloadArquivo(int $arquivoId): StreamedResponse
    {
        $arquivo = SolicitacaoArquivo::findOrFail($arquivoId);

        // Se for sigiloso, verifica se o usuário tem permissão
        /** @var User $user */
        $user = Auth::user();
        if ($arquivo->tipo === 's' && ! ($user->tipo_acesso === 's' || $user->isAdmin())) {
            abort(403, 'Acesso não autorizado para arquivos sigilosos.');
        }

        $path = 'documentos/'.$arquivo->tipo_md5;

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');
        if (! $disk->exists($path)) {
            abort(404, 'Arquivo não encontrado no servidor.');
        }

        return $disk->download($path, $arquivo->nome_arquivo);
    }

    /**
     * Alterna o estado oculto de um acolhido (Admin ou Diretor apenas).
     */
    public function toggleOculto(int $id): RedirectResponse
    {
        Gate::authorize('edit-data');

        $acolhimento = Acolhimento::findOrFail($id);
        $acolhimento->oculto = $acolhimento->oculto === 's' ? 'n' : 's';
        $acolhimento->id_usuario_alteracao = Auth::id();
        $acolhimento->save();

        $status = $acolhimento->oculto === 's' ? 'ocultado da' : 'restaurado para a';

        return back()->with('success', "Cadastro {$status} listagem com sucesso!");
    }
}
