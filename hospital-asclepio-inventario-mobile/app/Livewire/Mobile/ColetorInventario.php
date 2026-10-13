<?php

namespace App\Livewire\Mobile;

use App\Models\Local;
use App\Models\OfflineDadoMestre;
use App\Models\Setor;
use App\Models\SyncQueue;
use App\Services\MobileSyncService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.mobile')]
#[Title('Asclépio Inventário Mobile')]
class ColetorInventario extends Component
{
    public string $patrimonio = '';

    public string $tipo = 'equipamento'; // equipamento, monitor, periferico

    public ?int $localId = null;

    public ?int $setorId = null;

    public ?string $tipoDesempenho = 'Médio';

    public ?string $observacoes = null;

    public bool $estaOnline = false;

    public int $totalPendentes = 0;

    public ?string $mensagemStatus = null;

    public ?string $mensagemErro = null;

    public string $espacoLocalFormatado = '0 KB';

    public function mount(MobileSyncService $syncService): void
    {
        $this->atualizarStatus($syncService);
    }

    public function atualizarStatus(MobileSyncService $syncService): void
    {
        $this->estaOnline = $syncService->verificarConexao();
        $this->totalPendentes = SyncQueue::pendentes()->count();
        $this->espacoLocalFormatado = $this->calcularTamanhoBancoLocal();
    }

    public function salvarColetaOffline(): void
    {
        $this->mensagemStatus = null;
        $this->mensagemErro = null;

        $this->validate([
            'patrimonio' => ['required', 'string', 'min:2'],
            'tipo' => ['required', 'string', 'in:equipamento,monitor,periferico'],
            'localId' => ['nullable', 'integer'],
            'setorId' => ['nullable', 'integer'],
            'tipoDesempenho' => ['nullable', 'string'],
            'observacoes' => ['nullable', 'string', 'max:500'],
        ]);

        SyncQueue::create([
            'entidade' => $this->tipo,
            'acao' => 'inventario_campo',
            'payload' => [
                'patrimonio' => trim($this->patrimonio),
                'local_id' => $this->localId,
                'setor_id' => $this->setorId,
                'tipo_desempenho' => $this->tipoDesempenho,
                'observacoes' => $this->observacoes,
            ],
            'status' => 'pendente',
            'coletado_em' => now(),
        ]);

        $salvoPatrimonio = trim($this->patrimonio);
        $this->reset(['patrimonio', 'observacoes']);
        $this->totalPendentes = SyncQueue::pendentes()->count();
        $this->espacoLocalFormatado = $this->calcularTamanhoBancoLocal();
        $this->mensagemStatus = "Item {$salvoPatrimonio} registrado no celular com sucesso!";
    }

    public function executarSincronizacao(MobileSyncService $syncService): void
    {
        $this->mensagemStatus = null;
        $this->mensagemErro = null;

        $resultado = $syncService->sincronizarELimpar();
        $this->atualizarStatus($syncService);

        if ($resultado['sucesso']) {
            $enviados = $resultado['itens_enviados'] ?? 0;
            $this->mensagemStatus = "Sincronização concluída com sucesso! {$enviados} itens transferidos e armazenamento do celular compactado.";
        } else {
            $this->mensagemErro = $resultado['motivo'] ?? 'Falha ao sincronizar com o servidor central.';
        }
    }

    public function atualizarDadosMestres(MobileSyncService $syncService): void
    {
        $this->mensagemStatus = null;
        $this->mensagemErro = null;

        if (! $syncService->verificarConexao()) {
            $this->mensagemErro = 'Não foi possível baixar os dados mestres: servidor central offline.';

            return;
        }

        try {
            $servidorUrl = config('services.asclepio_servidor.url', 'http://127.0.0.1:8000');
            $response = Http::timeout(5)->get(rtrim($servidorUrl, '/').'/api/sync/dados-mestres');

            if ($response->successful()) {
                $dados = $response->json();

                OfflineDadoMestre::truncate();

                foreach ($dados['secretarias'] ?? [] as $sec) {
                    OfflineDadoMestre::create([
                        'tipo' => 'secretaria',
                        'servidor_id' => $sec['id'],
                        'nome' => $sec['nome'] ?? $sec['nome_extenso'] ?? 'Secretaria',
                    ]);
                }

                foreach ($dados['locais'] ?? [] as $loc) {
                    OfflineDadoMestre::create([
                        'tipo' => 'local',
                        'servidor_id' => $loc['id'],
                        'nome' => $loc['nome'] ?? 'Local',
                        'pai_id' => $loc['secretaria_id'] ?? null,
                    ]);
                }

                foreach ($dados['setores'] ?? [] as $set) {
                    OfflineDadoMestre::create([
                        'tipo' => 'setor',
                        'servidor_id' => $set['id'],
                        'nome' => $set['nome'] ?? 'Setor',
                    ]);
                }

                $this->mensagemStatus = 'Catálogo de locais e setores atualizado com sucesso!';
            }
        } catch (\Throwable $e) {
            $this->mensagemErro = 'Erro ao atualizar dados mestres: '.$e->getMessage();
        }
    }

    protected function calcularTamanhoBancoLocal(): string
    {
        $caminhos = [
            database_path('database.sqlite'),
            storage_path('database.sqlite'),
        ];

        foreach ($caminhos as $path) {
            if (file_exists($path)) {
                $bytes = filesize($path) ?: 0;

                return $this->formatarBytes($bytes);
            }
        }

        return '120 KB (compactado)';
    }

    protected function formatarBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }

        return number_format($bytes / 1024, 0).' KB';
    }

    public function render(): View
    {
        $locaisCache = OfflineDadoMestre::where('tipo', 'local')->orderBy('nome')->get();
        if ($locaisCache->isEmpty()) {
            $locais = Local::orderBy('local')->select('id_local as id', 'local as nome')->get();
        } else {
            $locais = $locaisCache->map(fn ($item) => (object) ['id' => $item->servidor_id, 'nome' => $item->nome]);
        }

        $setoresCache = OfflineDadoMestre::where('tipo', 'setor')->orderBy('nome')->get();
        if ($setoresCache->isEmpty()) {
            $setores = Setor::orderBy('nome')->select('id', 'nome')->get();
        } else {
            $setores = $setoresCache->map(fn ($item) => (object) ['id' => $item->servidor_id, 'nome' => $item->nome]);
        }

        return view('livewire.mobile.coletor-inventario', [
            'locais' => $locais,
            'setores' => $setores,
            'ultimasColetas' => SyncQueue::latest('id')->limit(5)->get(),
        ]);
    }
}
