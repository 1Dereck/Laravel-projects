<?php

namespace App\Livewire;

use App\Models\Equipamento;
use App\Models\Monitor;
use App\Models\Periferico;
use App\Models\Setor;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public function dismissTrashAlert(): void
    {
        session()->put('hide_trash_alert', true);
    }

    public function showTrashAlert(): void
    {
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
            'User' => $subject->name ?? null,
            default => null,
        };

        return $detail ? "{$typeName} ({$detail})" : "{$typeName} #{$activity->subject_id}";
    }

    public function render(): View
    {
        $totalEquipamentos = Equipamento::query()->count();
        $totalDesktops = Equipamento::query()->where('tipo', 'desktop')->count();
        $totalNotebooks = Equipamento::query()->where('tipo', 'notebook')->count();

        $totalMonitores = Monitor::query()->count();
        $totalPerifericos = Periferico::query()->count();
        $totalSetores = Setor::query()->count();

        // Feed de atividade recente (últimas 10 edições)
        $recentActivities = Activity::query()
            ->with(['causer', 'subject'])
            ->latest()
            ->take(10)
            ->get();

        // Contagem de lixeira (itens soft-deleted) para o alerta do Diretor
        $trashedCount = 0;
        /** @var User|null $user */
        $user = Auth::user();
        if ($user && $user->isDiretor()) {
            $trashedCount = Setor::onlyTrashed()->count()
                + Equipamento::onlyTrashed()->count()
                + Monitor::onlyTrashed()->count()
                + Periferico::onlyTrashed()->count();
        }

        // Top 10 setores com mais equipamentos
        $topSetores = Setor::query()
            ->withCount('equipamentos')
            ->orderByDesc('equipamentos_count')
            ->take(10)
            ->get();

        return view('livewire.dashboard', [
            'totalEquipamentos' => $totalEquipamentos,
            'totalDesktops' => $totalDesktops,
            'totalNotebooks' => $totalNotebooks,
            'totalMonitores' => $totalMonitores,
            'totalPerifericos' => $totalPerifericos,
            'totalSetores' => $totalSetores,
            'recentActivities' => $recentActivities,
            'trashedCount' => $trashedCount,
            'isTrashAlertDismissed' => session()->get('hide_trash_alert', false),
            'topSetores' => $topSetores,
        ]);
    }
}
