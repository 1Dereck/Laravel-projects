<?php

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

test('o usuario realiza cast de role para o enum userrole', function () {
    $user = User::factory()->create([
        'role' => UserRole::Gerente,
    ]);

    expect($user->role)->toBe(UserRole::Gerente)
        ->and($user->role->label())->toBe('Gerente')
        ->and($user->role->canManageProjects())->toBeTrue()
        ->and($user->role->isGerente())->toBeTrue();
});

test('o usuario possui projetos gerenciados e tarefas atribuidas a ele', function () {
    $user = User::factory()->create([
        'name' => 'Desenvolvedor Pleno',
        'role' => UserRole::Desenvolvedor,
    ]);

    // Cria um projeto pertencente ao usuário
    $project = Project::create([
        'name' => 'Projeto do Dev',
        'description' => 'Descrição do projeto',
        'api_secret' => 'token-12345',
        'user_id' => $user->id,
    ]);

    // Cria duas tarefas atribuídas a este usuário (assigned_to)
    Task::create([
        'project_id' => $project->id,
        'assigned_to' => $user->id,
        'title' => 'Criar Componente Livewire',
        'description' => 'Tabela reativa',
        'status' => TaskStatus::Afazer,
        'priority' => TaskPriority::Alto,
    ]);

    Task::create([
        'project_id' => $project->id,
        'assigned_to' => $user->id,
        'title' => 'Testar Scopes',
        'description' => 'Testes com Pest',
        'status' => TaskStatus::EmAndamento,
        'priority' => TaskPriority::Medio,
    ]);

    expect($user->projects)->toHaveCount(1)
        ->and($user->projects->first()->id)->toBe($project->id)
        ->and($user->assignedTasks)->toHaveCount(2)
        ->and($user->assignedTasks->first()->title)->toBe('Criar Componente Livewire');
});
