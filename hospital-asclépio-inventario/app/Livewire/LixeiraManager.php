<?php

namespace App\Livewire;

use App\Models\Equipamento;
use App\Models\Monitor;
use App\Models\Periferico;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Lixeira & Restauração Segura')]
class LixeiraManager extends Component
{
    public string $activeTab = 'equipamentos';

    // Propriedades para Modal de Confirmação Definitiva
    public bool $showConfirmModal = false;

    public string $confirmInput = '';

    public ?string $targetModelType = null;

    public ?int $targetModelId = null;

    public string $targetDescription = '';

    public function mount(): void
    {
        if (! auth()->user()->isDiretor()) {
            abort(403, 'Acesso restrito ao perfil de Diretor.');
        }
    }

    public function restaurar(string $modelType, int $id): void
    {
        if (! auth()->user()->isDiretor()) {
            abort(403);
        }

        $model = $modelType::onlyTrashed()->findOrFail($id);
        $model->restore();

        session()->flash('message', 'Item restaurado com sucesso para a lista de ativos!');
    }

    public function abrirModalExpurgo(string $modelType, int $id, string $description): void
    {
        $this->targetModelType = $modelType;
        $this->targetModelId = $id;
        $this->targetDescription = $description;
        $this->confirmInput = '';
        $this->showConfirmModal = true;
    }

    public function expurgarDefinitivamente(): void
    {
        if (! auth()->user()->isDiretor()) {
            abort(403);
        }

        if (trim($this->confirmInput) !== 'CONFIRMAR') {
            $this->addError('confirmInput', 'Digite a palavra exata CONFIRMAR em maiúsculas para autorizar o expurgo definitivo.');

            return;
        }

        $model = $this->targetModelType::onlyTrashed()->findOrFail($this->targetModelId);
        $model->forceDelete();

        $this->showConfirmModal = false;
        $this->reset(['targetModelType', 'targetModelId', 'targetDescription', 'confirmInput']);

        session()->flash('message', 'Registro excluído permanentemente do banco de dados.');
    }

    public function render()
    {
        $trashedEquipamentos = Equipamento::onlyTrashed()->with(['local', 'monitores'])->latest('deleted_at')->get();
        $trashedMonitores = Monitor::onlyTrashed()->with('equipamento')->latest('deleted_at')->get();
        $trashedPerifericos = Periferico::onlyTrashed()->with('local')->latest('deleted_at')->get();

        return view('livewire.lixeira-manager', [
            'trashedEquipamentos' => $trashedEquipamentos,
            'trashedMonitores' => $trashedMonitores,
            'trashedPerifericos' => $trashedPerifericos,
        ]);
    }
}
