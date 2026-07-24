<?php

namespace App\Livewire;

use App\Models\Equipamento;
use App\Models\Monitor;
use App\Models\Setor;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Equipamentos (Desktops & Notebooks)')]
class EquipamentoForm extends Component
{
    use AuthorizesRequests, WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public ?int $setorFilter = null;

    public bool $showModal = false;

    public ?int $equipamentoId = null;

    public ?int $setor_id = null;

    public string $tipo = 'desktop';

    public string $serial = '';

    public ?string $marca_modelo = '';

    public bool $kit_teclado_mouse_locado = false;

    public ?string $responsavel_levantamento = '';

    // Array dinâmico para monitores: [['id' => null, 'numero' => 1, 'serial' => '']]
    public array $monitores = [];

    protected function rules(): array
    {
        return [
            'setor_id' => ['required', 'exists:setores,id'],
            'tipo' => ['required', 'in:desktop,notebook'],
            'serial' => ['required', 'string', 'max:255', 'unique:equipamentos,serial,'.$this->equipamentoId],
            'marca_modelo' => ['nullable', 'string', 'max:255'],
            'kit_teclado_mouse_locado' => ['boolean'],
            'responsavel_levantamento' => ['nullable', 'string', 'max:255'],
            'monitores' => ['array'],
            'monitores.*.serial' => ['required', 'string', 'max:255'],
        ];
    }

    protected function messages(): array
    {
        return [
            'monitores.*.serial.required' => 'Informe o número de série para todos os monitores adicionados.',
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

    public function novoEquipamento(): void
    {
        $this->authorize('create', Equipamento::class);
        $this->reset(['equipamentoId', 'setor_id', 'tipo', 'serial', 'marca_modelo', 'kit_teclado_mouse_locado', 'responsavel_levantamento', 'monitores']);
        $this->tipo = 'desktop';
        $this->monitores = [];
        $this->resetValidation();
        $this->showModal = true;
    }

    public function adicionarMonitor(): void
    {
        $proximoNumero = count($this->monitores) + 1;
        $this->monitores[] = [
            'id' => null,
            'numero' => $proximoNumero,
            'serial' => '',
        ];
    }

    public function removerMonitor(int $index): void
    {
        unset($this->monitores[$index]);
        $this->monitores = array_values($this->monitores);

        // Reordenar números dos monitores
        foreach ($this->monitores as $i => &$mon) {
            $mon['numero'] = $i + 1;
        }
    }

    public function editarEquipamento(Equipamento $equipamento): void
    {
        $this->authorize('update', $equipamento);
        $this->equipamentoId = $equipamento->id;
        $this->setor_id = $equipamento->setor_id;
        $this->tipo = $equipamento->tipo;
        $this->serial = $equipamento->serial;
        $this->marca_modelo = $equipamento->marca_modelo;
        $this->kit_teclado_mouse_locado = (bool) $equipamento->kit_teclado_mouse_locado;
        $this->responsavel_levantamento = $equipamento->responsavel_levantamento;

        $this->monitores = $equipamento->monitores()
            ->orderBy('numero')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'numero' => $m->numero,
                'serial' => $m->serial,
            ])
            ->toArray();

        $this->resetValidation();
        $this->showModal = true;
    }

    public function salvar(): void
    {
        $this->validate($this->rules(), $this->messages());

        DB::transaction(function () {
            if ($this->equipamentoId) {
                $equipamento = Equipamento::findOrFail($this->equipamentoId);
                $this->authorize('update', $equipamento);
                $equipamento->update([
                    'setor_id' => $this->setor_id,
                    'tipo' => $this->tipo,
                    'serial' => $this->serial,
                    'marca_modelo' => $this->marca_modelo,
                    'kit_teclado_mouse_locado' => $this->kit_teclado_mouse_locado,
                    'responsavel_levantamento' => $this->responsavel_levantamento,
                ]);

                // Sincronizar monitores
                $existingMonitorIds = collect($this->monitores)->pluck('id')->filter()->toArray();
                $equipamento->monitores()->whereNotIn('id', $existingMonitorIds)->delete();

                foreach ($this->monitores as $m) {
                    if (! empty($m['id'])) {
                        Monitor::where('id', $m['id'])->update([
                            'numero' => $m['numero'],
                            'serial' => $m['serial'],
                        ]);
                    } else {
                        $equipamento->monitores()->create([
                            'numero' => $m['numero'],
                            'serial' => $m['serial'],
                        ]);
                    }
                }

                session()->flash('message', 'Equipamento atualizado com sucesso!');
            } else {
                $this->authorize('create', Equipamento::class);
                $equipamento = Equipamento::create([
                    'setor_id' => $this->setor_id,
                    'tipo' => $this->tipo,
                    'serial' => $this->serial,
                    'marca_modelo' => $this->marca_modelo,
                    'kit_teclado_mouse_locado' => $this->kit_teclado_mouse_locado,
                    'responsavel_levantamento' => $this->responsavel_levantamento,
                    'created_by' => auth()->id(),
                ]);

                foreach ($this->monitores as $m) {
                    $equipamento->monitores()->create([
                        'numero' => $m['numero'],
                        'serial' => $m['serial'],
                    ]);
                }

                session()->flash('message', 'Equipamento e monitores cadastrados com sucesso!');
            }
        });

        $this->showModal = false;
        $this->reset(['equipamentoId', 'setor_id', 'tipo', 'serial', 'marca_modelo', 'kit_teclado_mouse_locado', 'responsavel_levantamento', 'monitores']);
    }

    public function excluirEquipamento(Equipamento $equipamento): void
    {
        $this->authorize('delete', $equipamento);
        DB::transaction(function () use ($equipamento) {
            $equipamento->monitores()->delete();
            $equipamento->delete();
        });
        session()->flash('message', 'Equipamento e monitores associados movidos para a Lixeira.');
    }

    public function verHistorico(int $id): void
    {
        $this->dispatch('abrir-historico', modelType: Equipamento::class, modelId: $id, title: 'Histórico do Equipamento');
    }

    public function render()
    {
        $equipamentos = Equipamento::query()
            ->with(['setor', 'monitores', 'creator'])
            ->when($this->search, function ($q) {
                $q->where('serial', 'like', '%'.$this->search.'%')
                    ->orWhere('marca_modelo', 'like', '%'.$this->search.'%')
                    ->orWhere('responsavel_levantamento', 'like', '%'.$this->search.'%');
            })
            ->when($this->setorFilter, fn ($q) => $q->where('setor_id', $this->setorFilter))
            ->latest()
            ->paginate(10);

        $setores = Setor::orderBy('nome')->get();

        return view('livewire.equipamento-form', [
            'equipamentos' => $equipamentos,
            'setores' => $setores,
        ]);
    }
}
