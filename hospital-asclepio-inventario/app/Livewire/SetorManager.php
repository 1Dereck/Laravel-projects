<?php

namespace App\Livewire;

use App\Models\Local;
use App\Models\Secretaria;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Consulta de Locais & Secretarias')]
class SetorManager extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    public string $activeTab = 'locais';

    public ?int $selectedSecretariaId = null;

    public bool $showSecretariaModal = false;

    public function mount(): void
    {
        if (! auth()->user()?->isDiretor() && ! auth()->user()?->isAdmin()) {
            abort(403, 'Acesso não autorizado ao cadastro de setores.');
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage('locaisPage');
        $this->resetPage('secretariasPage');
    }

    public function abrirDetalhesSecretaria(int $id): void
    {
        $this->selectedSecretariaId = $id;
        $this->showSecretariaModal = true;
    }

    public function fecharModal(): void
    {
        $this->showSecretariaModal = false;
        $this->selectedSecretariaId = null;
    }

    public function render()
    {
        $locais = Local::query()
            ->with(['secretaria'])
            ->withCount(['equipamentos', 'perifericos'])
            ->when($this->search, fn ($q) => $q->where('local', 'like', '%'.$this->search.'%')
                ->orWhere('bairro', 'like', '%'.$this->search.'%')
                ->orWhere('ip_onu', 'like', '%'.$this->search.'%')
                ->orWhereHas('secretaria', fn ($sq) => $sq->where('secretaria', 'like', '%'.$this->search.'%')->orWhere('nome_extenso', 'like', '%'.$this->search.'%')))
            ->orderBy('local')
            ->paginate(10, ['*'], 'locaisPage');

        $secretarias = Secretaria::query()
            ->with(['locais'])
            ->withCount(['locais', 'equipamentos', 'perifericos'])
            ->when($this->search, fn ($q) => $q->where('secretaria', 'like', '%'.$this->search.'%')
                ->orWhere('nome_extenso', 'like', '%'.$this->search.'%')
                ->orWhere('nome_secretario', 'like', '%'.$this->search.'%'))
            ->orderBy('secretaria')
            ->paginate(10, ['*'], 'secretariasPage');

        $selectedSecretaria = null;
        if ($this->showSecretariaModal && $this->selectedSecretariaId) {
            $selectedSecretaria = Secretaria::query()
                ->with(['locais' => function ($q) {
                    $q->withCount(['equipamentos', 'perifericos'])->orderBy('local');
                }])
                ->withCount(['locais', 'equipamentos', 'perifericos'])
                ->find($this->selectedSecretariaId);
        }

        return view('livewire.setor-manager', [
            'locais' => $locais,
            'secretarias' => $secretarias,
            'selectedSecretaria' => $selectedSecretaria,
        ]);
    }
}
