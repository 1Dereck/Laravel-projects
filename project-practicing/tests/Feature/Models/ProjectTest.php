<?php

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

test('o projeto armazena api_secret de forma criptografada no banco e descriptografa no model', function () {
    $user = User::factory()->create();
    $rawSecret = 'webhook-super-secret-token-xyz-123';

    $project = Project::create([
        'name' => 'Sistema Integrador',
        'description' => 'Integração de pagamentos',
        'api_secret' => $rawSecret,
        'user_id' => $user->id,
    ]);

    // 1. Verificação no banco bruto (raw): O valor gravado NÃO pode ser o texto puro
    $databaseRawSecret = DB::table('projects')->where('id', $project->id)->value('api_secret');

    expect($databaseRawSecret)->not->toBe($rawSecret)
        ->and(strlen($databaseRawSecret))->toBeGreaterThan(30);

    // 2. Verificação no Model Eloquent: O cast 'encrypted' descriptografa automaticamente
    $reloaded = Project::find($project->id);

    expect($reloaded->api_secret)->toBe($rawSecret);
});

test('o projeto permite mutação in-place no campo settings usando asarrayobject', function () {
    $user = User::factory()->create();

    $project = Project::create([
        'name' => 'TaskForge Platform',
        'description' => 'Gestão ágil de projetos',
        'api_secret' => 'secret-123',
        'user_id' => $user->id,
        'settings' => [
            'theme' => 'dark',
            'notifications' => [
                'email' => true,
                'slack' => false,
            ],
        ],
    ]);

    // Mutação in-place direta
    $project->settings['notifications']['slack'] = true;
    $project->settings['max_members'] = 15;
    $project->save();

    $freshProject = $project->fresh();

    expect($freshProject->settings['notifications']['slack'])->toBeTrue()
        ->and($freshProject->settings['max_members'])->toBe(15)
        ->and($freshProject->settings['theme'])->toBe('dark');
});

test('o projeto pertence a um usuario e possui varias tarefas', function () {
    $user = User::factory()->create(['name' => 'Tech Lead']);

    $project = Project::create([
        'name' => 'TaskForge Platform',
        'description' => 'Gestão de tarefas',
        'api_secret' => 'secret-123',
        'user_id' => $user->id,
    ]);

    Task::create([
        'project_id' => $project->id,
        'title' => 'Primeira Tarefa',
        'description' => 'Descrição 1',
        'status' => TaskStatus::Afazer,
        'priority' => TaskPriority::Medio,
    ]);

    Task::create([
        'project_id' => $project->id,
        'title' => 'Segunda Tarefa',
        'description' => 'Descrição 2',
        'status' => TaskStatus::EmAndamento,
        'priority' => TaskPriority::Alto,
    ]);

    expect($project->user)->toBeInstanceOf(User::class)
        ->and($project->user->id)->toBe($user->id)
        ->and($project->tasks)->toHaveCount(2)
        ->and($project->tasks->first())->toBeInstanceOf(Task::class);
});
