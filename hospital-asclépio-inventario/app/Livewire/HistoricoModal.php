<?php

namespace App\Livewire;

use App\Models\Equipamento;
use App\Models\Setor;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class HistoricoModal extends Component
{
    public bool $showModal = false;

    public string $modelType = '';

    public ?int $modelId = null;

    public string $title = 'Linha do Tempo de Alterações';

    public $activities = [];

    #[On('abrir-historico')]
    public function abrir(string $modelType, int $modelId, string $title = 'Histórico'): void
    {
        $this->modelType = $modelType;
        $this->modelId = $modelId;
        $this->title = $title;
        $this->showModal = true;
        $this->carregarLogs();
    }

    public function carregarLogs(): void
    {
        if (! $this->modelType || ! $this->modelId) {
            $this->activities = [];

            return;
        }

        $this->activities = Activity::query()
            ->where('subject_type', $this->modelType)
            ->where('subject_id', $this->modelId)
            ->with('causer')
            ->latest()
            ->get();
    }

    public function formatEvent(string $event): string
    {
        return match ($event) {
            'created' => 'Cadastro Inicial',
            'updated' => 'Atualização de Dados',
            'deleted' => 'Enviado para a Lixeira',
            'restored' => 'Restaurado da Lixeira',
            default => ucfirst($event),
        };
    }

    public function formatChanges(Activity $activity): array
    {
        $attributes = $activity->properties['attributes'] ?? $activity->attribute_changes['attributes'] ?? $activity->changes['attributes'] ?? [];
        $old = $activity->properties['old'] ?? $activity->attribute_changes['old'] ?? $activity->changes['old'] ?? [];

        $ignored = ['id', 'created_at', 'updated_at', 'deleted_at', 'created_by'];

        $labels = [
            'tipo' => 'Tipo',
            'tipo_desempenho' => 'Tipo de Desempenho',
            'serial' => 'Número de Série',
            'marca_modelo' => 'Marca / Modelo',
            'kit_teclado_mouse_locado' => 'Kit Teclado/Mouse Locado',
            'responsavel_levantamento' => 'Responsável pelo Levantamento',
            'setor_id' => 'Setor',
            'equipamento_id' => 'Equipamento',
            'serial_patrimonio' => 'Serial / Patrimônio',
            'observacoes' => 'Observações',
            'numero' => 'Número do Monitor',
            'nome' => 'Nome',
            'email' => 'E-mail',
            'role' => 'Função / Perfil',
            'name' => 'Nome do Usuário',
        ];

        $formatted = [];
        $setoresMap = Setor::pluck('nome', 'id')->toArray();

        foreach ($attributes as $key => $value) {
            if (in_array($key, $ignored, true)) {
                continue;
            }

            $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));

            $valFormatted = $this->formatSingleValue($key, $value, $setoresMap);
            $oldFormatted = array_key_exists($key, $old) ? $this->formatSingleValue($key, $old[$key], $setoresMap) : null;

            $formatted[] = [
                'label' => $label,
                'value' => $valFormatted,
                'old' => $oldFormatted,
                'has_old' => array_key_exists($key, $old),
            ];
        }

        return $formatted;
    }

    private function formatSingleValue(string $key, mixed $value, array $setoresMap): string
    {
        if (is_bool($value) || $key === 'kit_teclado_mouse_locado') {
            return ((bool) $value) ? 'Sim' : 'Não';
        }

        if ($key === 'setor_id' && ! empty($value)) {
            return $setoresMap[$value] ?? "Setor #{$value}";
        }

        if ($key === 'equipamento_id' && ! empty($value)) {
            $equipamento = Equipamento::find($value);

            return $equipamento ? "{$equipamento->marca_modelo} ({$equipamento->serial})" : "Equipamento #{$value}";
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        if ($value === null || $value === '') {
            return 'Vazio';
        }

        if ($key === 'tipo') {
            return ucfirst((string) $value);
        }

        if ($key === 'tipo_desempenho') {
            return match ((string) $value) {
                'avancado' => 'Avançado',
                'administrativo' => 'Administrativo',
                default => ucfirst((string) $value),
            };
        }

        return (string) $value;
    }

    public function fechar(): void
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.historico-modal');
    }
}
