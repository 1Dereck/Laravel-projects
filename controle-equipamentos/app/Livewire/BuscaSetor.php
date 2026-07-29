<?php

namespace App\Livewire;

use App\Models\Setor;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Busca Inteligente & Relatórios PDF')]
class BuscaSetor extends Component
{
    #[Url(history: true)]
    public string $search = '';

    public ?int $selectedSetorId = null;

    public function selecionarSetor(int $id): void
    {
        $this->selectedSetorId = $id;
    }

    public function render(): View
    {
        $setores = Setor::query()
            ->withCount(['equipamentos', 'perifericos'])
            ->when($this->search, fn ($q) => $q->where('nome', 'like', '%'.$this->search.'%'))
            ->orderBy('nome')
            ->get();

        $selectedSetor = null;
        if ($this->selectedSetorId) {
            $selectedSetor = Setor::with([
                'equipamentos.monitores',
                'equipamentos.creator',
                'perifericos.equipamento',
                'perifericos.creator',
            ])->find($this->selectedSetorId);
        }

        return view('livewire.busca-setor', [
            'setores' => $setores,
            'selectedSetor' => $selectedSetor,
        ]);
    }
}
