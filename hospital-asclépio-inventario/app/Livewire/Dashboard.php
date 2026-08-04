<?php

namespace App\Livewire;

use App\Models\Equipamento;
use App\Models\Local;
use App\Models\Monitor;
use App\Models\Periferico;
use App\Models\Secretaria;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public function getTrashedState(): string
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->isDiretor()) {
            return '0';
        }

        $trashedCount = Equipamento::onlyTrashed()->count()
            + Monitor::onlyTrashed()->count()
            + Periferico::onlyTrashed()->count();

        if ($trashedCount === 0) {
            return '0';
        }

        $latestDeletedAt = max([
            Equipamento::onlyTrashed()->max('deleted_at') ?? '',
            Monitor::onlyTrashed()->max('deleted_at') ?? '',
            Periferico::onlyTrashed()->max('deleted_at') ?? '',
        ]);

        return $trashedCount.'_'.$latestDeletedAt;
    }

    public function dismissTrashAlert(): void
    {
        session()->put('dismissed_trashed_state', $this->getTrashedState());
    }

    public function showTrashAlert(): void
    {
        session()->forget('dismissed_trashed_state');
        session()->forget('hide_trash_alert');
    }

    public function formatEvent(string $event): string
    {
        return match (strtolower($event)) {
            'created' => 'Cadastrou',
            'updated' => 'Atualizou',
            'deleted' => 'Excluiu',
            'restored' => 'Restaurou',
            'expunged' => 'Expurgou',
            default => ucfirst($event),
        };
    }

    public function getEventBadgeClass(string $event): string
    {
        return match (strtolower($event)) {
            'created' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
            'updated' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
            'deleted', 'expunged' => 'bg-red-500/10 text-red-600 dark:text-red-400 border-red-500/20',
            'restored' => 'bg-teal-500/10 text-teal-600 dark:text-teal-400 border-teal-500/20',
            default => 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20',
        };
    }

    public function formatSubject(Activity $activity): string
    {
        $type = class_basename($activity->subject_type);
        $typeName = match ($type) {
            'Equipamento' => 'Computador',
            'Periferico' => 'Periférico',
            'Monitor' => 'Monitor',
            'Setor' => 'Setor',
            'Local' => 'Local',
            'Secretaria' => 'Secretaria',
            'User' => 'Usuário',
            default => $type,
        };

        $subject = $activity->subject;
        if (! $subject) {
            return "{$typeName} #{$activity->subject_id}";
        }

        $detail = match ($type) {
            'Equipamento' => $subject->serial ?? null,
            'Periferico' => $subject->tipo ?? $subject->serial_patrimonio ?? null,
            'Monitor' => isset($subject->serial) ? "SN: {$subject->serial}" : null,
            'Setor' => $subject->nome ?? null,
            'Local' => $subject->local ?? null,
            'Secretaria' => $subject->nome_extenso ?? $subject->secretaria ?? null,
            'User' => $subject->name ?? null,
            default => null,
        };

        return $detail ? "{$typeName} ({$detail})" : "{$typeName} #{$activity->subject_id}";
    }

    public function render()
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user && $user->isUsuario()) {
            $userSetorId = $user->setor_id;
            $totalEquipamentos = Equipamento::where('setor_id', $userSetorId)->count();
            $totalDesktops = Equipamento::where('setor_id', $userSetorId)->where('tipo', 'desktop')->count();
            $totalNotebooks = Equipamento::where('setor_id', $userSetorId)->where('tipo', 'notebook')->count();
            $totalMonitores = Monitor::whereHas('equipamento', fn ($q) => $q->where('setor_id', $userSetorId))->count();
            $totalPerifericos = Periferico::where('setor_id', $userSetorId)->count();
            $totalLocais = 0;
            $totalSecretarias = 0;
            $recentActivities = collect();
            $trashedCount = 0;
            $topLocais = collect();
        } elseif ($user && $user->isCoordenador()) {
            $secretariaId = $user->setor?->secretaria_id;
            if ($secretariaId) {
                $localIds = Local::where('secretaria_id', $secretariaId)->pluck('id_local');
            } elseif ($user->setor_id) {
                $localIds = collect([$user->setor_id]);
            } else {
                $localIds = collect();
            }

            $totalEquipamentos = Equipamento::whereIn('setor_id', $localIds)->count();
            $totalDesktops = Equipamento::whereIn('setor_id', $localIds)->where('tipo', 'desktop')->count();
            $totalNotebooks = Equipamento::whereIn('setor_id', $localIds)->where('tipo', 'notebook')->count();
            $totalMonitores = Monitor::whereHas('equipamento', fn ($q) => $q->whereIn('setor_id', $localIds))->count();
            $totalPerifericos = Periferico::whereIn('setor_id', $localIds)->count();
            $totalLocais = Local::whereIn('id_local', $localIds)->count();
            $totalSecretarias = $secretariaId ? 1 : 0;

            $recentActivities = Activity::query()
                ->with(['causer', 'subject'])
                ->latest()
                ->take(10)
                ->get();

            $trashedCount = 0;

            $topLocais = Local::query()
                ->whereIn('id_local', $localIds)
                ->withCount('equipamentos')
                ->orderByDesc('equipamentos_count')
                ->take(10)
                ->get();
        } else {
            $totalEquipamentos = Equipamento::query()->count();
            $totalDesktops = Equipamento::query()->where('tipo', 'desktop')->count();
            $totalNotebooks = Equipamento::query()->where('tipo', 'notebook')->count();

            $totalMonitores = Monitor::query()->count();
            $totalPerifericos = Periferico::query()->count();
            $totalLocais = Local::query()->count();
            $totalSecretarias = Secretaria::query()->count();

            // Feed de atividade recente (últimas 10 edições)
            $recentActivities = Activity::query()
                ->with(['causer', 'subject'])
                ->latest()
                ->take(10)
                ->get();

            // Contagem de lixeira (itens soft-deleted) para o alerta do Diretor
            $trashedCount = 0;
            if ($user && $user->isDiretor()) {
                $trashedCount = Equipamento::onlyTrashed()->count()
                    + Monitor::onlyTrashed()->count()
                    + Periferico::onlyTrashed()->count();
            }

            // Top 10 locais com mais equipamentos
            $topLocais = Local::query()
                ->withCount('equipamentos')
                ->orderByDesc('equipamentos_count')
                ->take(10)
                ->get();
        }

        return view('livewire.dashboard', [
            'totalEquipamentos' => $totalEquipamentos,
            'totalDesktops' => $totalDesktops,
            'totalNotebooks' => $totalNotebooks,
            'totalMonitores' => $totalMonitores,
            'totalPerifericos' => $totalPerifericos,
            'totalLocais' => $totalLocais,
            'totalSecretarias' => $totalSecretarias,
            'recentActivities' => $recentActivities,
            'trashedCount' => $trashedCount,
            'isTrashAlertDismissed' => ($this->getTrashedState() !== '0') && (session()->get('dismissed_trashed_state') === $this->getTrashedState() || session()->get('hide_trash_alert', false)),
            'topLocais' => $topLocais,
        ]);
    }
}
