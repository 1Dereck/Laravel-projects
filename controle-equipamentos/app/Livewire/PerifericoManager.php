<?php

namespace App\Livewire;

use App\Models\Equipamento;
use App\Models\Periferico;
use App\Models\Setor;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Gestão de Periféricos')]
class PerifericoManager extends Component
{
    use AuthorizesRequests, WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public ?int $setorFilter = null;

    public bool $showModal = false;

    public ?int $perifericoId = null;

    public ?int $setor_id = null;

    public ?int $equipamento_id = null;

    public string $tipo = '';

    public ?string $serial_patrimonio = '';

    public ?string $observacoes = '';

    protected function rules(): array
    {
        return [
            'setor_id' => ['required', 'exists:setores,id'],
            'equipamento_id' => ['nullable', 'exists:equipamentos,id'],
            'tipo' => ['required', 'string', 'max:255'],
            'serial_patrimonio' => ['nullable', 'string', 'max:255'],
            'observacoes' => ['nullable', 'string'],
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSetorFilter(): void
    {
        $this->resetPage();
    }

    public function novoPeriferico(): void
    {
        $this->authorize('create', Periferico::class);
        $this->reset(['perifericoId', 'setor_id', 'equipamento_id', 'tipo', 'serial_patrimonio', 'observacoes']);
        $this->resetValidation();
        $this->showModal = true;
    }

    public function editarPeriferico(Periferico $periferico): void
    {
        $this->authorize('update', $periferico);
        $this->perifericoId = $periferico->id;
        $this->setor_id = $periferico->setor_id;
        $this->equipamento_id = $periferico->equipamento_id;
        $this->tipo = $periferico->tipo;
        $this->serial_patrimonio = $periferico->serial_patrimonio;
        $this->observacoes = $periferico->observacoes;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function salvar(): void
    {
        $this->validate($this->rules());

        if ($this->perifericoId) {
            $periferico = Periferico::findOrFail($this->perifericoId);
            $this->authorize('update', $periferico);
            $periferico->update([
                'setor_id' => $this->setor_id,
                'equipamento_id' => $this->equipamento_id,
                'tipo' => $this->tipo,
                'serial_patrimonio' => $this->serial_patrimonio,
                'observacoes' => $this->observacoes,
            ]);
            session()->flash('message', 'Periférico atualizado com sucesso!');
        } else {
            $this->authorize('create', Periferico::class);
            Periferico::create([
                'setor_id' => $this->setor_id,
                'equipamento_id' => $this->equipamento_id,
                'tipo' => $this->tipo,
                'serial_patrimonio' => $this->serial_patrimonio,
                'observacoes' => $this->observacoes,
                'created_by' => auth()->id(),
            ]);
            session()->flash('message', 'Periférico cadastrado com sucesso!');
        }

        $this->showModal = false;
        $this->reset(['perifericoId', 'setor_id', 'equipamento_id', 'tipo', 'serial_patrimonio', 'observacoes']);
    }

    public function excluirPeriferico(Periferico $periferico): void
    {
        $this->authorize('delete', $periferico);
        $periferico->delete();
        session()->flash('message', 'Periférico movido para a Lixeira.');
    }

    public function verHistorico(int $id): void
    {
        $this->dispatch('abrir-historico', modelType: Periferico::class, modelId: $id, title: 'Histórico do Periférico');
    }

    public function render()
    {
        $perifericos = Periferico::query()
            ->with(['setor', 'equipamento', 'creator'])
            ->when($this->search, function ($q) {
                $q->where('tipo', 'like', '%'.$this->search.'%')
                    ->orWhere('serial_patrimonio', 'like', '%'.$this->search.'%')
                    ->orWhere('observacoes', 'like', '%'.$this->search.'%');
            })
            ->when($this->setorFilter, fn ($q) => $q->where('setor_id', $this->setorFilter))
            ->latest()
            ->paginate(10);

        $setores = Setor::orderBy('nome')->get();
        $equipamentos = $this->setor_id
            ? Equipamento::where('setor_id', $this->setor_id)->orderBy('serial')->get()
            : collect();

        return view('livewire.periferico-manager', [
            'perifericos' => $perifericos,
            'setores' => $setores,
            'setoresList' => $setores,
            'equipamentos' => $equipamentos,
            'equipamentosList' => $equipamentos,
        ]);
    }
}
