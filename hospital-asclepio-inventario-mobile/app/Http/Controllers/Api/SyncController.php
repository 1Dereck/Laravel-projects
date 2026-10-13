<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Equipamento;
use App\Models\Local;
use App\Models\Monitor;
use App\Models\Periferico;
use App\Models\Secretaria;
use App\Models\Setor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncController extends Controller
{
    public function ping(): JsonResponse
    {
        return response()->json([
            'status' => 'online',
            'servidor' => 'Hospital Asclépio Central',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function obterDadosMestres(): JsonResponse
    {
        return response()->json([
            'secretarias' => Secretaria::select('id_secretarias as id', 'secretaria as nome', 'nome_extenso')->get(),
            'locais' => Local::select('id_local as id', 'secretaria_id', 'local as nome', 'tipo_local')->get(),
            'setores' => Setor::select('id', 'nome')->get(),
            'versao_catalogo' => now()->timestamp,
        ]);
    }

    public function receberLote(Request $request): JsonResponse
    {
        $dadosValidados = $request->validate([
            'dispositivo_id' => 'required|string',
            'operador_email' => 'nullable|email',
            'lote' => 'required|array|min:1',
            'lote.*.uuid' => 'required|string',
            'lote.*.entidade' => 'required|string|in:equipamento,monitor,periferico',
            'lote.*.dados' => 'required|array',
        ]);

        $processados = [];
        $falhas = [];

        DB::beginTransaction();
        try {
            foreach ($dadosValidados['lote'] as $item) {
                try {
                    $uuid = $item['uuid'];
                    $entidade = $item['entidade'];
                    $dados = $item['dados'];

                    match ($entidade) {
                        'equipamento' => $this->processarEquipamento($dados),
                        'monitor' => $this->processarMonitor($dados),
                        'periferico' => $this->processarPeriferico($dados),
                    };

                    activity('inventario_mobile')
                        ->withProperties(['uuid' => $uuid, 'dispositivo' => $dadosValidados['dispositivo_id']])
                        ->log("Sincronização de {$entidade} via coletor móvel");

                    $processados[] = $uuid;
                } catch (\Throwable $e) {
                    $falhas[] = [
                        'uuid' => $item['uuid'],
                        'erro' => $e->getMessage(),
                    ];
                }
            }

            DB::commit();
        } catch (\Throwable $globalEx) {
            DB::rollBack();
            Log::error('Erro crítico na sincronização em lote', ['erro' => $globalEx->getMessage()]);

            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Falha geral ao processar lote no servidor central.',
            ], 500);
        }

        return response()->json([
            'sucesso' => count($falhas) === 0,
            'total_processados' => count($processados),
            'processados_uuids' => $processados,
            'falhas' => $falhas,
        ]);
    }

    /**
     * @param  array<string, mixed>  $dados
     */
    protected function processarEquipamento(array $dados): void
    {
        $serial = $dados['patrimonio'] ?? $dados['serial'] ?? null;
        if (empty($serial)) {
            throw new \InvalidArgumentException('Número de patrimônio ou serial do equipamento não informado.');
        }

        Equipamento::updateOrCreate(
            ['serial' => $serial],
            [
                'setor_id' => $dados['local_id'] ?? $dados['setor_id'] ?? null,
                'tipo' => $dados['tipo'] ?? 'desktop',
                'tipo_desempenho' => $dados['tipo_desempenho'] ?? 'Médio',
                'marca_modelo' => $dados['marca_modelo'] ?? null,
                'responsavel_levantamento' => $dados['responsavel_levantamento'] ?? 'Coletor Mobile',
            ]
        );
    }

    /**
     * @param  array<string, mixed>  $dados
     */
    protected function processarMonitor(array $dados): void
    {
        $serial = $dados['patrimonio'] ?? $dados['serial'] ?? null;
        if (empty($serial)) {
            throw new \InvalidArgumentException('Número de patrimônio ou serial do monitor não informado.');
        }

        $equipamentoId = $dados['equipamento_id'] ?? null;
        if (! $equipamentoId && ! empty($dados['equipamento_serial'])) {
            $equipamentoId = Equipamento::where('serial', $dados['equipamento_serial'])->value('id');
        }

        if (! $equipamentoId) {
            $equipamento = Equipamento::firstOrCreate(
                ['serial' => 'AVULSO-'.($dados['local_id'] ?? 'GERAL')],
                [
                    'setor_id' => $dados['local_id'] ?? $dados['setor_id'] ?? null,
                    'tipo' => 'desktop',
                    'tipo_desempenho' => 'Básico',
                    'responsavel_levantamento' => 'Coletor Mobile',
                ]
            );
            $equipamentoId = $equipamento->id;
        }

        Monitor::updateOrCreate(
            ['serial' => $serial],
            [
                'equipamento_id' => $equipamentoId,
                'numero' => $dados['numero'] ?? 1,
            ]
        );
    }

    /**
     * @param  array<string, mixed>  $dados
     */
    protected function processarPeriferico(array $dados): void
    {
        $patrimonio = $dados['patrimonio'] ?? $dados['serial_patrimonio'] ?? null;

        Periferico::updateOrCreate(
            ['serial_patrimonio' => $patrimonio],
            [
                'setor_id' => $dados['local_id'] ?? $dados['setor_id'] ?? null,
                'tipo' => $dados['tipo_periferico'] ?? $dados['tipo'] ?? 'Outros',
                'observacoes' => $dados['observacoes'] ?? null,
            ]
        );
    }
}
