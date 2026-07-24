<?php

namespace App\Livewire;

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

    public function fechar(): void
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.historico-modal');
    }
}
