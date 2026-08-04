<?php

namespace App\Livewire;

use App\Models\Local;
use App\Models\Secretaria;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Relatórios PDF por Setor e Secretaria')]
class BuscaSetor extends Component
{
    public string $tipoRelatorio = 'local'; // 'local' ou 'secretaria'

    public ?int $selectedLocalId = null;

    public ?int $selectedSecretariaId = null;

    public function mount(): void
    {
        $user = auth()->user();

        if ($user?->isUsuario()) {
            $this->tipoRelatorio = 'local';
            $this->selectedLocalId = $user->setor_id;
        } elseif ($user?->isCoordenador() && $user->setor?->secretaria_id) {
            $this->selectedSecretariaId = $user->setor->secretaria_id;
        }
    }

    public function updatedTipoRelatorio(): void
    {
        $user = auth()->user();
        if ($user?->isUsuario()) {
            $this->tipoRelatorio = 'local';
            $this->selectedLocalId = $user->setor_id;

            return;
        }

        $this->selectedLocalId = null;
        if ($user?->isCoordenador() && $user->setor?->secretaria_id) {
            $this->selectedSecretariaId = $user->setor->secretaria_id;
        } else {
            $this->selectedSecretariaId = null;
        }
    }

    public function selecionarLocal(int $id): void
    {
        $user = auth()->user();
        if ($user?->isUsuario()) {
            $this->selectedLocalId = $user->setor_id;

            return;
        }

        $this->selectedLocalId = $id;
    }

    public function render()
    {
        $user = auth()->user();

        if ($user?->isUsuario()) {
            $this->tipoRelatorio = 'local';
            $this->selectedLocalId = $user->setor_id;
        }

        $locaisQuery = Local::query()
            ->withCount(['equipamentos', 'perifericos']);

        if ($user?->isUsuario()) {
            $locaisQuery->where('id_local', $user->setor_id);
        } elseif ($user?->isCoordenador()) {
            $locaisQuery->whereIn('id_local', $user->getSectorLocalIds());
        }

        $locais = $locaisQuery->orderBy('local')->get();

        $secretariasQuery = Secretaria::query()
            ->withCount(['locais', 'equipamentos', 'perifericos']);

        if ($user?->isUsuario()) {
            $secretariasQuery->whereRaw('1 = 0');
        } elseif ($user?->isCoordenador()) {
            $coordSecId = $user->setor?->secretaria_id;
            if ($coordSecId) {
                $secretariasQuery->where('id_secretarias', $coordSecId);
            } else {
                $secretariasQuery->whereRaw('1 = 0');
            }
        }

        $secretarias = $secretariasQuery->orderBy('secretaria')->get();

        $selectedLocal = null;
        if ($this->selectedLocalId) {
            $selectedLocalQuery = Local::with([
                'secretaria',
                'equipamentos.monitores',
                'equipamentos.creator',
                'perifericos.equipamento',
                'perifericos.creator',
            ]);

            if ($user?->isUsuario()) {
                $selectedLocalQuery->where('id_local', $user->setor_id);
            } elseif ($user?->isCoordenador()) {
                $selectedLocalQuery->whereIn('id_local', $user->getSectorLocalIds());
            }

            $selectedLocal = $selectedLocalQuery->find($this->selectedLocalId);
        }

        $selectedSecretaria = null;
        if ($this->selectedSecretariaId && ! $user?->isUsuario()) {
            $selectedSecQuery = Secretaria::with([
                'locais.equipamentos.monitores',
                'locais.perifericos',
            ]);

            if ($user?->isCoordenador()) {
                $coordSecId = $user->setor?->secretaria_id;
                if ($coordSecId) {
                    $selectedSecQuery->where('id_secretarias', $coordSecId);
                }
            }

            $selectedSecretaria = $selectedSecQuery->find($this->selectedSecretariaId);
        }

        return view('livewire.busca-setor', [
            'locais' => $locais,
            'secretarias' => $secretarias,
            'selectedLocal' => $selectedLocal,
            'selectedSecretaria' => $selectedSecretaria,
        ]);
    }
}
