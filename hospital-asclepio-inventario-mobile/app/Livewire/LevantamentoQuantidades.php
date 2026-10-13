<?php

namespace App\Livewire;

use App\Models\Local;
use App\Models\Secretaria;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Levantamento de Quantidades')]
class LevantamentoQuantidades extends Component
{
    public string $modoQuantidades = 'secretaria'; // 'secretaria' ou 'local'

    public ?int $quantidadesSecretariaId = null; // null = Todas as Secretarias

    public ?int $quantidadesLocalId = null; // null = Todos os Locais

    public function mount(): void
    {
        $user = auth()->user();

        if ($user?->isUsuario()) {
            $this->modoQuantidades = 'local';
            $this->quantidadesLocalId = $user->setor_id;
        } elseif ($user?->isCoordenador()) {
            $coordSecId = $user->setor?->secretaria_id;
            if ($coordSecId) {
                $this->quantidadesSecretariaId = $coordSecId;
            }
            $this->quantidadesLocalId = null;
        } else {
            $this->quantidadesSecretariaId = null;
            $this->quantidadesLocalId = null;
        }
    }

    public function verLocalIsolado(int $localId): void
    {
        $user = auth()->user();
        if ($user?->isUsuario()) {
            return;
        }

        if ($user?->isCoordenador() && ! $user->getSectorLocalIds()->contains($localId)) {
            return;
        }

        $this->modoQuantidades = 'local';
        $this->quantidadesLocalId = $localId;
    }

    public function updatedModoQuantidades(): void
    {
        $user = auth()->user();
        if ($user?->isUsuario()) {
            $this->modoQuantidades = 'local';
            $this->quantidadesLocalId = $user->setor_id;
        }
    }

    public function render()
    {
        $user = auth()->user();

        // Trava de perfil
        if ($user?->isUsuario()) {
            $this->modoQuantidades = 'local';
            $this->quantidadesLocalId = $user->setor_id;
        } elseif ($user?->isCoordenador()) {
            $coordSecId = $user->setor?->secretaria_id;
            if ($coordSecId) {
                $this->quantidadesSecretariaId = $coordSecId;
            }
            if ($this->quantidadesLocalId && ! $user->getSectorLocalIds()->contains($this->quantidadesLocalId)) {
                $this->quantidadesLocalId = null;
            }
        }

        // Secretarias disponiveis
        $quantidadesSecretariasQuery = Secretaria::query();
        if ($user?->isUsuario()) {
            $quantidadesSecretariasQuery->whereRaw('1 = 0');
        } elseif ($user?->isCoordenador()) {
            $coordSecId = $user->setor?->secretaria_id;
            if ($coordSecId) {
                $quantidadesSecretariasQuery->where('id_secretarias', $coordSecId);
            } else {
                $quantidadesSecretariasQuery->whereRaw('1 = 0');
            }
        }
        $quantidadesSecretarias = $quantidadesSecretariasQuery->orderBy('secretaria')->get();

        // Locais disponiveis
        $quantidadesLocaisQuery = Local::query();
        if ($user?->isUsuario()) {
            $quantidadesLocaisQuery->where('id_local', $user->setor_id);
        } elseif ($user?->isCoordenador()) {
            $quantidadesLocaisQuery->whereIn('id_local', $user->getSectorLocalIds());
        } elseif ($this->quantidadesSecretariaId && $this->modoQuantidades === 'secretaria') {
            $quantidadesLocaisQuery->where('secretaria_id', $this->quantidadesSecretariaId);
        }
        $quantidadesLocais = $quantidadesLocaisQuery->orderBy('local')->get();

        // Estatisticas do Modo Secretaria
        $quantidadesSecretaria = null;
        $secStats = [
            'desktops' => 0,
            'notebooks' => 0,
            'total_pcs' => 0,
            'locais_count' => 0,
        ];
        $locaisBreakdown = [];

        if ($this->modoQuantidades === 'secretaria' && ! $user?->isUsuario()) {
            if ($this->quantidadesSecretariaId) {
                $secQuery = Secretaria::with([
                    'locais.secretaria',
                    'locais.equipamentos',
                ]);

                if ($user?->isCoordenador()) {
                    $coordSecId = $user->setor?->secretaria_id;
                    if ($coordSecId) {
                        $secQuery->where('id_secretarias', $coordSecId);
                    }
                }

                $quantidadesSecretaria = $secQuery->find($this->quantidadesSecretariaId);

                if ($quantidadesSecretaria) {
                    $allLocais = $quantidadesSecretaria->locais;
                    $secStats['locais_count'] = $allLocais->count();

                    foreach ($allLocais as $loc) {
                        $desktops = $loc->equipamentos->where('tipo', 'desktop')->count();
                        $notebooks = $loc->equipamentos->where('tipo', 'notebook')->count();
                        $totalPcs = $loc->equipamentos->count();

                        $secStats['desktops'] += $desktops;
                        $secStats['notebooks'] += $notebooks;
                        $secStats['total_pcs'] += $totalPcs;

                        $locaisBreakdown[] = [
                            'local' => $loc,
                            'desktops' => $desktops,
                            'notebooks' => $notebooks,
                            'total_pcs' => $totalPcs,
                        ];
                    }
                }
            } else {
                // "Todas as Secretarias" (Padrao)
                $locsQuery = Local::with(['secretaria', 'equipamentos']);
                if ($user?->isCoordenador()) {
                    $locsQuery->whereIn('id_local', $user->getSectorLocalIds());
                }
                $allLocais = $locsQuery->orderBy('local')->get();

                $secStats['locais_count'] = $allLocais->count();

                foreach ($allLocais as $loc) {
                    $desktops = $loc->equipamentos->where('tipo', 'desktop')->count();
                    $notebooks = $loc->equipamentos->where('tipo', 'notebook')->count();
                    $totalPcs = $loc->equipamentos->count();

                    $secStats['desktops'] += $desktops;
                    $secStats['notebooks'] += $notebooks;
                    $secStats['total_pcs'] += $totalPcs;

                    $locaisBreakdown[] = [
                        'local' => $loc,
                        'desktops' => $desktops,
                        'notebooks' => $notebooks,
                        'total_pcs' => $totalPcs,
                    ];
                }
            }
        }

        // Estatisticas do Modo Local
        $quantidadesLocal = null;
        $locStats = [
            'desktops' => 0,
            'notebooks' => 0,
            'total_pcs' => 0,
        ];

        if ($this->modoQuantidades === 'local') {
            if ($this->quantidadesLocalId) {
                $locQuery = Local::with([
                    'secretaria',
                    'equipamentos',
                ]);

                if ($user?->isUsuario()) {
                    $locQuery->where('id_local', $user->setor_id);
                } elseif ($user?->isCoordenador()) {
                    $locQuery->whereIn('id_local', $user->getSectorLocalIds());
                }

                $quantidadesLocal = $locQuery->find($this->quantidadesLocalId);

                if ($quantidadesLocal) {
                    $locStats['desktops'] = $quantidadesLocal->equipamentos->where('tipo', 'desktop')->count();
                    $locStats['notebooks'] = $quantidadesLocal->equipamentos->where('tipo', 'notebook')->count();
                    $locStats['total_pcs'] = $quantidadesLocal->equipamentos->count();
                }
            } else {
                // "Todos os Locais" (Padrao)
                $locsQuery = Local::with([
                    'secretaria',
                    'equipamentos',
                ]);

                if ($user?->isCoordenador()) {
                    $locsQuery->whereIn('id_local', $user->getSectorLocalIds());
                }

                $allLocs = $locsQuery->orderBy('local')->get();

                foreach ($allLocs as $l) {
                    $locStats['desktops'] += $l->equipamentos->where('tipo', 'desktop')->count();
                    $locStats['notebooks'] += $l->equipamentos->where('tipo', 'notebook')->count();
                    $locStats['total_pcs'] += $l->equipamentos->count();
                }
            }
        }

        return view('livewire.levantamento-quantidades', [
            'quantidadesSecretarias' => $quantidadesSecretarias,
            'quantidadesLocais' => $quantidadesLocais,
            'quantidadesSecretaria' => $quantidadesSecretaria,
            'secStats' => $secStats,
            'locaisBreakdown' => $locaisBreakdown,
            'quantidadesLocal' => $quantidadesLocal,
            'locStats' => $locStats,
        ]);
    }
}
