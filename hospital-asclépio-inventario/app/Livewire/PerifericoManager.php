<?php

namespace App\Livewire;

use App\Models\Equipamento;
use App\Models\Local;
use App\Models\Periferico;
use App\Models\Secretaria;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
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
    public ?int $secretariaFilter = null;

    #[Url(history: true)]
    public ?int $setorFilter = null;

    public bool $showModal = false;

    public ?int $perifericoId = null;

    public ?int $secretaria_id = null;

    public ?int $setor_id = null;

    public ?int $equipamento_id = null;

    public string $tipo = '';

    public ?string $serial_patrimonio = '';

    public ?string $observacoes = '';

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
            'equipamento_id' => ['nullable'],
            'tipo' => ['required', 'string', 'max:255'],
            'serial_patrimonio' => ['nullable', 'string', 'max:255'],
            'observacoes' => ['nullable', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
            'secretaria_id.required' => 'Selecione a Secretaria do periférico.',
            'setor_id.required' => 'Selecione o Local Destino do periférico.',
            'tipo.required' => 'Informe o tipo do periférico.',
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
                $this->equipamento_id = null;
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
        } else {
            $this->equipamento_id = null;
        }
    }

    public function novoPeriferico(): void
    {
        $this->authorize('create', Periferico::class);
        $this->reset(['perifericoId', 'secretaria_id', 'setor_id', 'equipamento_id', 'tipo', 'serial_patrimonio', 'observacoes']);
        $user = auth()->user();
        if ($user?->isCoordenador()) {
            $this->secretaria_id = $user->setor?->secretaria_id;
        }
        $this->resetValidation();
        $this->showModal = true;
    }

    public function editarPeriferico(Periferico $periferico): void
    {
        $this->authorize('update', $periferico);
        $this->perifericoId = $periferico->id;
        $this->setor_id = $periferico->setor_id;
        $user = auth()->user();
        if ($user?->isCoordenador()) {
            $this->secretaria_id = $user->setor?->secretaria_id;
        } else {
            $this->secretaria_id = $periferico->local?->secretaria_id;
        }
        $this->equipamento_id = $periferico->equipamento_id;
        $this->tipo = $periferico->tipo;
        $this->serial_patrimonio = $periferico->serial_patrimonio;
        $this->observacoes = $periferico->observacoes;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function salvar(): void
    {
        $user = auth()->user();
        if ($user?->isCoordenador()) {
            $this->secretaria_id = $user->setor?->secretaria_id;
        }

        $this->validate($this->rules());

        if ($this->setor_id && $this->secretaria_id) {
            $localObj = Local::find($this->setor_id);
            if ($localObj && $localObj->secretaria_id !== $this->secretaria_id) {
                $localObj->update(['secretaria_id' => $this->secretaria_id]);
            }
        }

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
        $this->reset(['perifericoId', 'secretaria_id', 'setor_id', 'equipamento_id', 'tipo', 'serial_patrimonio', 'observacoes']);
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
        $user = auth()->user();
        if ($user?->isUsuario()) {
            $this->setorFilter = $user->setor_id;
        } elseif ($user?->isCoordenador()) {
            $this->secretariaFilter = $user->setor?->secretaria_id;
        }

        $perifericos = Periferico::query()
            ->with(['local.secretaria', 'equipamento', 'creator'])
            ->when($user?->isUsuario(), fn ($q) => $q->where('setor_id', $user->setor_id))
            ->when($user?->isCoordenador(), fn ($q) => $q->whereIn('setor_id', $user->getSectorLocalIds()))
            ->when(! $user?->isUsuario() && ! $user?->isCoordenador() && $this->secretariaFilter, fn ($q) => $q->whereHas('local', fn ($lq) => $lq->where('secretaria_id', $this->secretariaFilter)))
            ->when(! $user?->isUsuario() && $this->setorFilter, fn ($q) => $q->where('setor_id', $this->setorFilter))
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('tipo', 'like', '%'.$this->search.'%')
                        ->orWhere('serial_patrimonio', 'like', '%'.$this->search.'%')
                        ->orWhere('observacoes', 'like', '%'.$this->search.'%');
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

        $equipamentos = $this->setor_id
            ? Equipamento::where('setor_id', $this->setor_id)->orderBy('serial')->get()
            : collect();

        return view('livewire.periferico-manager', [
            'perifericos' => $perifericos,
            'secretarias' => $secretarias,
            'locais' => $locais,
            'modalLocais' => $modalLocais,
            'equipamentos' => $equipamentos,
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
