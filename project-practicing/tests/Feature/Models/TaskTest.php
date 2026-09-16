<?php

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\CarbonImmutable;

function createTestProject(?User $user = null): Project
{
    $user ??= User::factory()->create();

    return Project::create([
        'name' => 'TaskForge Platform',
        'description' => 'Sistema de gestão inteligente de projetos',
        'api_secret' => 'super-secret-key-12345',
        'user_id' => $user->id,
    ]);
}

test('a tarefa realiza cast correto para enums de status e prioridade', function () {
    $project = createTestProject();

    $task = Task::create([
        'project_id' => $project->id,
        'title' => 'Criar autenticação',
        'description' => 'Configurar Fortify e roles',
        'status' => TaskStatus::Afazer,
        'priority' => TaskPriority::Alto,
    ]);

    expect($task->status)->toBe(TaskStatus::Afazer)
        ->and($task->status->label())->toBe('A Fazer')
        ->and($task->priority)->toBe(TaskPriority::Alto)
        ->and($task->priority->label())->toBe('Alto');
});

test('a tarefa faz cast de deadline_at para carbon immutable', function () {
    $project = createTestProject();

    $task = Task::create([
        'project_id' => $project->id,
        'title' => 'Entregar MVP',
        'description' => 'Finalizar primeira versão',
        'status' => TaskStatus::Afazer,
        'priority' => TaskPriority::Urgente,
        'deadline_at' => '2026-12-31 18:00:00',
    ]);

    expect($task->deadline_at)->toBeInstanceOf(CarbonImmutable::class)
        ->and($task->deadline_at->format('Y-m-d H:i'))->toBe('2026-12-31 18:00');
});

test('a tarefa permite mutação de json in-place usando asarrayobject em metadata', function () {
    $project = createTestProject();

    $task = Task::create([
        'project_id' => $project->id,
        'title' => 'Configurar Docker',
        'description' => 'Ambiente padronizado',
        'status' => TaskStatus::EmAndamento,
        'priority' => TaskPriority::Medio,
        'metadata' => [
            'estimated_hours' => 8,
            'tags' => ['infra', 'devops'],
        ],
    ]);

    // Mutação in-place sem precisar reatribuir todo o array
    $task->metadata['logged_hours'] = 3;
    $task->metadata['tags'][] = 'docker';
    $task->save();

    $reloaded = $task->fresh();

    expect($reloaded->metadata['estimated_hours'])->toBe(8)
        ->and($reloaded->metadata['logged_hours'])->toBe(3)
        ->and($reloaded->metadata['tags'])->toContain('docker');
});

test('a tarefa pertence a um projeto e pode ter um responsavel atribuido', function () {
    $user = User::factory()->create(['name' => 'Dev Responsavel']);
    $project = createTestProject($user);

    $task = Task::create([
        'project_id' => $project->id,
        'assigned_to' => $user->id,
        'title' => 'Implementar Scopes',
        'description' => 'Scopes locais no Eloquent',
        'status' => TaskStatus::Afazer,
        'priority' => TaskPriority::Baixo,
    ]);

    expect($task->project)->toBeInstanceOf(Project::class)
        ->and($task->project->id)->toBe($project->id)
        ->and($task->assigneeUser)->toBeInstanceOf(User::class)
        ->and($task->assigneeUser->name)->toBe('Dev Responsavel');
});

test('scope overdue retorna apenas tarefas atrasadas e nao concluidas', function () {
    $project = createTestProject();

    // Tarefa 1: atrasada e pendente (DEVE aparecer no scope)
    $overdueTask = Task::create([
        'project_id' => $project->id,
        'title' => 'Tarefa Atrasada Pendente',
        'description' => 'Não entregue a tempo',
        'status' => TaskStatus::EmAndamento,
        'priority' => TaskPriority::Alto,
        'deadline_at' => CarbonImmutable::now()->subDays(2),
    ]);

    // Tarefa 2: atrasada mas já CONCLUÍDA (NÃO deve aparecer no scope)
    Task::create([
        'project_id' => $project->id,
        'title' => 'Tarefa Atrasada Concluída',
        'description' => 'Entregue mesmo com atraso',
        'status' => TaskStatus::Concluida,
        'priority' => TaskPriority::Baixo,
        'deadline_at' => CarbonImmutable::now()->subDays(3),
    ]);

    // Tarefa 3: no prazo futuro (NÃO deve aparecer no scope)
    Task::create([
        'project_id' => $project->id,
        'title' => 'Tarefa no Prazo',
        'description' => 'Prazo ainda válido',
        'status' => TaskStatus::Afazer,
        'priority' => TaskPriority::Medio,
        'deadline_at' => CarbonImmutable::now()->addDays(5),
    ]);

    $overdueTasks = Task::overdue()->get();

    expect($overdueTasks)->toHaveCount(1)
        ->and($overdueTasks->first()->id)->toBe($overdueTask->id);
});

test('scope ofPriority filtra tarefas corretamente pelo enum de prioridade', function () {
    $project = createTestProject();

    $urgentTask = Task::create([
        'project_id' => $project->id,
        'title' => 'Bug Crítico em Produção',
        'description' => 'Servidor fora do ar',
        'status' => TaskStatus::Afazer,
        'priority' => TaskPriority::Urgente,
    ]);

    Task::create([
        'project_id' => $project->id,
        'title' => 'Melhoria Visual no Header',
        'description' => 'Mudar cor de botão',
        'status' => TaskStatus::Afazer,
        'priority' => TaskPriority::Baixo,
    ]);

    $urgentTasks = Task::ofPriority(TaskPriority::Urgente)->get();

    expect($urgentTasks)->toHaveCount(1)
        ->and($urgentTasks->first()->id)->toBe($urgentTask->id);
});
