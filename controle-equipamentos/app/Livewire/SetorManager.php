<?php

namespace App\Livewire;

use App\Models\Setor;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Gerenciamento de Setores')]
class SetorManager extends Component
{
    use AuthorizesRequests, WithPagination;

    #[Url(history: true)]
    public string $search = '';

    public bool $showModal = false;

    public ?int $setorId = null;

    public string $nome = '';

    protected function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255', 'unique:setores,nome,'.$this->setorId],
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function novoSetor(): void
    {
        $this->authorize('create', Setor::class);
        $this->reset(['setorId', 'nome']);
        $this->resetValidation();
        $this->showModal = true;
    }

    public function editarSetor(Setor $setor): void
    {
        $this->authorize('update', $setor);
        $this->setorId = $setor->id;
        $this->nome = $setor->nome;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function salvar(): void
    {
        $this->validate($this->rules());

        if ($this->setorId) {
            $setor = Setor::findOrFail($this->setorId);
            $this->authorize('update', $setor);
            $setor->update([
                'nome' => $this->nome,
            ]);
            session()->flash('message', 'Setor atualizado com sucesso!');
        } else {
            $this->authorize('create', Setor::class);
            Setor::create([
                'nome' => $this->nome,
                'created_by' => auth()->id(),
            ]);
            session()->flash('message', 'Setor cadastrado com sucesso!');
        }

        $this->showModal = false;
        $this->reset(['setorId', 'nome']);
    }

    public function excluirSetor(Setor $setor): void
    {
        $this->authorize('delete', $setor);
        $setor->delete();
        session()->flash('message', 'Setor movido para a Lixeira.');
    }

    public function verHistorico(int $id): void
    {
        $this->dispatch('abrir-historico', modelType: Setor::class, modelId: $id, title: 'Histórico do Setor');
    }

    public function render()
    {
        $setores = Setor::query()
            ->withCount(['equipamentos', 'perifericos'])
            ->when($this->search, fn ($q) => $q->where('nome', 'like', '%'.$this->search.'%'))
            ->latest()
            ->paginate(10);

        return view('livewire.setor-manager', [
            'setores' => $setores,
        ]);
    }
}
