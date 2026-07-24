<?php

namespace App\Livewire;

use App\Models\Equipamento;
use App\Models\Monitor;
use App\Models\Periferico;
use App\Models\Setor;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

#[Layout('components.layouts.app')]
#[Title('Dashboard Gerencial')]
class Dashboard extends Component
{
    public function render()
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
            'topSetores' => $topSetores,
        ]);
    }
}
