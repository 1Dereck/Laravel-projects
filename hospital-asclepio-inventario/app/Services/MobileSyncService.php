<?php

namespace App\Services;

use App\Models\SyncQueue;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MobileSyncService
{
    protected string $servidorUrl;

    public function __construct(?string $servidorUrl = null)
    {
        $this->servidorUrl = rtrim($servidorUrl ?? (string) config('services.asclepio_servidor.url', 'http://127.0.0.1:8000'), '/');
    }

    /**
     * Verifica se o servidor central está acessível na rede local.
     */
    public function verificarConexao(): bool
    {
        try {
            $resposta = Http::timeout(2)->get("{$this->servidorUrl}/api/sync/ping");

            return $resposta->successful() && ($resposta->json('status') === 'online');
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Transmite os itens pendentes e realiza a limpeza no celular.
     *
     * @return array{sucesso: bool, itens_enviados?: int, motivo?: string, mensagem?: string, falhas?: array<int, mixed>}
     */
    public function sincronizarELimpar(): array
    {
        if (! $this->verificarConexao()) {
            return [
                'sucesso' => false,
                'motivo' => 'Servidor central inacessível. O app continuará no modo offline.',
            ];
        }

        $itensPendentes = SyncQueue::pendentes()->limit(100)->get();

        if ($itensPendentes->isEmpty()) {
            return [
                'sucesso' => true,
                'mensagem' => 'Nenhum item pendente para envio.',
                'itens_enviados' => 0,
            ];
        }

        $uuids = $itensPendentes->pluck('uuid')->toArray();
        SyncQueue::whereIn('uuid', $uuids)->update(['status' => 'enviando']);

        $user = Auth::user();
        $operadorEmail = $user instanceof User
            ? "{$user->username}@asclepio.org.br"
            : 'operador@asclepio.org.br';

        $payload = [
            'dispositivo_id' => gethostname() ?: 'MOBILE-DISPOSITIVO',
            'operador_email' => $operadorEmail,
            'lote' => $itensPendentes->map(fn (SyncQueue $item): array => [
                'uuid' => $item->uuid,
                'entidade' => $item->entidade,
                'acao' => $item->acao,
                'coletado_em' => $item->coletado_em?->toIso8601String() ?? now()->toIso8601String(),
                'dados' => $item->payload,
            ])->toArray(),
        ];

        try {
            $resposta = Http::timeout(30)->post("{$this->servidorUrl}/api/sync/enviar-lote", $payload);

            if (! $resposta->successful()) {
                SyncQueue::whereIn('uuid', $uuids)->update(['status' => 'pendente']);

                return [
                    'sucesso' => false,
                    'motivo' => 'Servidor recusou o lote ou retornou erro HTTP '.$resposta->status(),
                ];
            }

            /** @var array{processados_uuids?: list<string>, falhas?: list<mixed>} $resultado */
            $resultado = $resposta->json() ?? [];
            $uuidsConfirmados = $resultado['processados_uuids'] ?? [];

            // =========================================================================
            // ROTINA DE PURGE (LIMPEZA PARA NÃO PESAR O CELULAR DO USUÁRIO)
            // =========================================================================
            $this->executarLimpezaLocal($uuidsConfirmados);

            return [
                'sucesso' => true,
                'itens_enviados' => count($uuidsConfirmados),
                'falhas' => $resultado['falhas'] ?? [],
                'mensagem' => 'Dados enviados com sucesso e celular limpo.',
            ];
        } catch (\Throwable $e) {
            SyncQueue::whereIn('uuid', $uuids)->update([
                'status' => 'pendente',
                'erro_mensagem' => $e->getMessage(),
            ]);

            return [
                'sucesso' => false,
                'motivo' => 'Exceção durante transmissão: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Remove os registros transmitidos e recompacta o SQLite no celular.
     *
     * @param  list<string>  $uuidsConfirmados
     */
    public function executarLimpezaLocal(array $uuidsConfirmados): void
    {
        if (empty($uuidsConfirmados)) {
            return;
        }

        // 1. Deleta do banco local os itens confirmados com ACK
        SyncQueue::whereIn('uuid', $uuidsConfirmados)->delete();

        // 2. Executa VACUUM se estiver em conexão SQLite para devolver o espaço alocado em disco
        try {
            $driver = DB::connection()->getDriverName();
            if ($driver === 'sqlite') {
                DB::statement('VACUUM;');
                Log::info('Limpeza VACUUM do SQLite executada com sucesso após sincronização.');
            }
        } catch (\Throwable $e) {
            Log::warning('Não foi possível executar VACUUM no SQLite: '.$e->getMessage());
        }
    }
}
