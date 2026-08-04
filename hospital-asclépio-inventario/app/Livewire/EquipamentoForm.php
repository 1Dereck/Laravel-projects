<?php

namespace App\Livewire;

use App\Models\Equipamento;
use App\Models\Local;
use App\Models\Monitor;
use App\Models\Secretaria;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
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
    public ?int $secretariaFilter = null;

    #[Url(history: true)]
    public ?int $setorFilter = null;

    public bool $showModal = false;

    public ?int $equipamentoId = null;

    public ?int $secretaria_id = null;

    public ?int $setor_id = null;

    public string $tipo = 'desktop';

    public string $tipo_desempenho = 'administrativo';

    public string $serial = '';

    public ?string $marca_modelo = '';

    public bool $kit_teclado_mouse_locado = false;

    public ?string $responsavel_levantamento = '';

    public array $monitores = [];

    public function mount(): void
    {
        $user = auth()->user();
        if ($user?->isUsuario()) {
            $this->setorFilter = $user->setor_id;
        } elseif ($user?->isCoordenador()) {
            $this->secretariaFilter = $user->setor?->secretaria_id;
        }
    }

    protected function rules(): array
    {
        return [
            'secretaria_id' => ['required', 'integer', 'exists:secretarias,id_secretarias'],
            'setor_id' => ['required', 'integer', 'exists:local,id_local'],
            'tipo' => ['required', 'in:desktop,notebook'],
            'tipo_desempenho' => ['required', 'in:administrativo,avancado'],
            'serial' => ['required', 'string', 'max:255', 'unique:equipamentos,serial,'.$this->equipamentoId],
            'marca_modelo' => ['required', 'string', 'max:255'],
            'kit_teclado_mouse_locado' => ['boolean'],
            'responsavel_levantamento' => ['required', 'string', 'max:255'],
            'monitores' => ['array'],
            'monitores.*.serial' => ['required', 'string', 'max:255'],
            'monitores.*.marca_modelo' => ['required', 'string', 'max:255'],
        ];
    }

    protected function messages(): array
    {
        return [
            'secretaria_id.required' => 'Selecione a Secretaria do equipamento.',
            'setor_id.required' => 'Selecione o Local de Alocação do equipamento.',
            'marca_modelo.required' => 'Informe a marca e modelo do equipamento.',
            'responsavel_levantamento.required' => 'Informe o técnico / responsável pela coleta.',
            'monitores.*.serial.required' => 'Informe o número de série para todos os monitores adicionados.',
            'monitores.*.marca_modelo.required' => 'Informe a marca e modelo para todos os monitores adicionados.',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSecretariaFilter(): void
    {
        $this->setorFilter = null;
        $this->resetPage();
    }

    public function updatedSetorFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSecretariaId(?int $val): void
    {
        if ($val && $this->setor_id) {
            $local = Local::find($this->setor_id);
            if ($local && $local->secretaria_id != $val) {
                $this->setor_id = null;
            }
        }
    }

    public function updatedSetorId(?int $val): void
    {
        if ($val) {
            $local = Local::find($val);
            if ($local && $local->secretaria_id && ! auth()->user()?->isCoordenador()) {
                $this->secretaria_id = $local->secretaria_id;
            }
        }
    }

    public function novoEquipamento(): void
    {
        $this->authorize('create', Equipamento::class);
        $this->reset(['equipamentoId', 'secretaria_id', 'setor_id', 'tipo', 'tipo_desempenho', 'serial', 'marca_modelo', 'kit_teclado_mouse_locado', 'responsavel_levantamento', 'monitores']);
        $user = auth()->user();
        if ($user?->isCoordenador()) {
            $this->secretaria_id = $user->setor?->secretaria_id;
        }
        $this->tipo = 'desktop';
        $this->tipo_desempenho = 'administrativo';
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
            'marca_modelo' => '',
        ];
    }

    public function removerMonitor(int $index): void
    {
        unset($this->monitores[$index]);
        $this->monitores = array_values($this->monitores);

        foreach ($this->monitores as $i => &$mon) {
            $mon['numero'] = $i + 1;
        }
    }

    public function editarEquipamento(Equipamento $equipamento): void
    {
        $this->authorize('update', $equipamento);
        $this->equipamentoId = $equipamento->id;
        $this->setor_id = $equipamento->setor_id;
        $user = auth()->user();
        if ($user?->isCoordenador()) {
            $this->secretaria_id = $user->setor?->secretaria_id;
        } else {
            $this->secretaria_id = $equipamento->local?->secretaria_id;
        }
        $this->tipo = $equipamento->tipo;
        $this->tipo_desempenho = $equipamento->tipo_desempenho ?? 'administrativo';
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
                'marca_modelo' => $m->marca_modelo,
            ])->toArray();

        $this->resetValidation();
        $this->showModal = true;
    }

    public function salvar(): void
    {
        $user = auth()->user();
        if ($user?->isCoordenador()) {
            $this->secretaria_id = $user->setor?->secretaria_id;
        }

        $this->validate($this->rules(), $this->messages());

        DB::transaction(function () {
            if ($this->setor_id && $this->secretaria_id) {
                $localObj = Local::find($this->setor_id);
                if ($localObj && $localObj->secretaria_id !== $this->secretaria_id) {
                    $localObj->update(['secretaria_id' => $this->secretaria_id]);
                }
            }

            if ($this->equipamentoId) {
                $equipamento = Equipamento::findOrFail($this->equipamentoId);
                $this->authorize('update', $equipamento);
                $equipamento->update([
                    'setor_id' => $this->setor_id,
                    'tipo' => $this->tipo,
                    'tipo_desempenho' => $this->tipo_desempenho,
                    'serial' => $this->serial,
                    'marca_modelo' => $this->marca_modelo,
                    'kit_teclado_mouse_locado' => $this->kit_teclado_mouse_locado,
                    'responsavel_levantamento' => $this->responsavel_levantamento,
                ]);

                $existingMonitorIds = collect($this->monitores)->pluck('id')->filter()->toArray();
                $equipamento->monitores()->whereNotIn('id', $existingMonitorIds)->delete();

                foreach ($this->monitores as $m) {
                    if (! empty($m['id'])) {
                        Monitor::where('id', $m['id'])->update([
                            'numero' => $m['numero'],
                            'serial' => $m['serial'],
                            'marca_modelo' => $m['marca_modelo'] ?? null,
                        ]);
                    } else {
                        $equipamento->monitores()->create([
                            'numero' => $m['numero'],
                            'serial' => $m['serial'],
                            'marca_modelo' => $m['marca_modelo'] ?? null,
                        ]);
                    }
                }

                session()->flash('message', 'Equipamento atualizado com sucesso!');
            } else {
                $this->authorize('create', Equipamento::class);
                $equipamento = Equipamento::create([
                    'setor_id' => $this->setor_id,
                    'tipo' => $this->tipo,
                    'tipo_desempenho' => $this->tipo_desempenho,
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
                        'marca_modelo' => $m['marca_modelo'] ?? null,
                    ]);
                }

                session()->flash('message', 'Equipamento e monitores cadastrados com sucesso!');
            }
        });

        $this->showModal = false;
        $this->reset(['equipamentoId', 'secretaria_id', 'setor_id', 'tipo', 'tipo_desempenho', 'serial', 'marca_modelo', 'kit_teclado_mouse_locado', 'responsavel_levantamento', 'monitores']);
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
        $user = auth()->user();
        if ($user?->isUsuario()) {
            $this->setorFilter = $user->setor_id;
        } elseif ($user?->isCoordenador()) {
            $this->secretariaFilter = $user->setor?->secretaria_id;
        }

        $equipamentos = Equipamento::query()
            ->with(['local.secretaria', 'monitores', 'creator'])
            ->when($user?->isUsuario(), fn ($q) => $q->where('setor_id', $user->setor_id))
            ->when($user?->isCoordenador(), fn ($q) => $q->whereIn('setor_id', $user->getSectorLocalIds()))
            ->when(! $user?->isUsuario() && ! $user?->isCoordenador() && $this->secretariaFilter, fn ($q) => $q->whereHas('local', fn ($lq) => $lq->where('secretaria_id', $this->secretariaFilter)))
            ->when(! $user?->isUsuario() && $this->setorFilter, fn ($q) => $q->where('setor_id', $this->setorFilter))
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('serial', 'like', '%'.$this->search.'%')
                        ->orWhere('marca_modelo', 'like', '%'.$this->search.'%')
                        ->orWhere('responsavel_levantamento', 'like', '%'.$this->search.'%')
                        ->orWhereHas('monitores', function ($mQuery) {
                            $mQuery->where('serial', 'like', '%'.$this->search.'%')
                                ->orWhere('marca_modelo', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->latest()
            ->paginate(10);

        $secretarias = Secretaria::orderBy('secretaria')->get();

        $locaisQuery = Local::query();
        if ($user?->isUsuario()) {
            $locaisQuery->where('id_local', $user->setor_id);
        } elseif ($user?->isCoordenador()) {
            $locaisQuery->whereIn('id_local', $user->getSectorLocalIds());
        } elseif ($this->secretariaFilter) {
            $locaisQuery->where('secretaria_id', $this->secretariaFilter);
        }
        $locais = $this->filterLocaisForSecretaria($locaisQuery->orderBy('local')->get(), $this->secretariaFilter);

        $modalLocaisQuery = Local::query();
        if ($user?->isCoordenador()) {
            $coordSecId = $user->setor?->secretaria_id;
            if ($coordSecId) {
                $modalLocaisQuery->where(function ($q) use ($coordSecId) {
                    $q->where('secretaria_id', $coordSecId)
                        ->orWhereNull('secretaria_id');
                    if ($this->setor_id) {
                        $q->orWhere('id_local', $this->setor_id);
                    }
                });
            }
        } elseif ($this->secretaria_id) {
            $modalLocaisQuery->where(function ($q) {
                $q->where('secretaria_id', $this->secretaria_id)
                    ->orWhereNull('secretaria_id');
                if ($this->setor_id) {
                    $q->orWhere('id_local', $this->setor_id);
                }
            });
        }
        $modalSecId = $user?->isCoordenador() ? $user->setor?->secretaria_id : $this->secretaria_id;
        $modalLocais = $this->filterLocaisForSecretaria($modalLocaisQuery->orderBy('local')->get(), $modalSecId);

        return view('livewire.equipamento-form', [
            'equipamentos' => $equipamentos,
            'secretarias' => $secretarias,
            'locais' => $locais,
            'modalLocais' => $modalLocais,
        ]);
    }

    /**
     * @param  Collection<int, Local>  $locais
     * @return Collection<int, Local>
     */
    private function filterLocaisForSecretaria(Collection $locais, ?int $secretariaId): Collection
    {
        if (! $secretariaId) {
            return $locais;
        }

        $sec = Secretaria::find($secretariaId);
        if (! $sec) {
            return $locais;
        }

        $secName = mb_strtolower(trim($sec->secretaria));
        $secExt = mb_strtolower(trim($sec->nome_extenso));
        $cleanSec = preg_replace('/^(s\.m\.\s*|secretaria\s+municipal\s+de\s+|secretaria\s+municipal\s+do\s+|secretaria\s+municipal\s+da\s+|secretaria\s+)/iu', '', $secName);

        return $locais->reject(function ($l) use ($secName, $secExt, $cleanSec) {
            $locName = mb_strtolower(trim($l->local));
            $cleanLoc = preg_replace('/^(sec\.\s*|secretaria\s+)/iu', '', $locName);

            return $locName === $secName
                || $locName === $secExt
                || ($cleanSec !== '' && $cleanLoc === $cleanSec);
        })->values();
    }
}
