# 🧪 Guia de Estudo: Testes Modernos com Pest PHP

## 1. Por Que Pest e Por Que Testar Conforme Construímos?

### 👴 A dor dos testes antigos no PHPUnit:
No PHPUnit tradicional, você precisava de muita cerimônia: classes, métodos longos `public function test_it_can_create_a_task()`, `assertDatabaseHas`, `$this->assertTrue(...)`.

### 🌟 A Elegância do Pest:
O **Pest** traz uma sintaxe fluida, expressiva e natural baseada em funções e expectativas (`expect()`):

```php
test('um usuário pode ter o papel de administrador', function () {
    $user = User::factory()->create(['role' => UserRole::Admin]);

    expect($user->role)->toBe(UserRole::Admin)
        ->and($user->role->label())->toBe('Administrador')
        ->and($user->role->canManageProjects())->toBeTrue();
});
```

---

## 2. A Filosofia: "Testar a Cada Passo" (Testes Contínuos)

Não deixaremos os testes para o final do projeto. Em vez disso:
1. **Criamos um Enum?** -> Já criamos o teste dele com Pest (`tests/Unit/Enums/UserRoleTest.php`).
2. **Criamos a Migration e Model com Cast?** -> Testamos se o Cast e o Scope funcionam no banco (`tests/Feature/Models/TaskTest.php`).
3. **Criamos uma Policy?** -> Testamos se o Admin passa e se o usuário comum toma 403 (`tests/Feature/Policies/TaskPolicyTest.php`).
4. **Criamos uma Action / Service?** -> Testamos a execução da regra e transações (`tests/Feature/Actions/CreateTaskActionTest.php`).
5. **Criamos um Componente Livewire?** -> Testamos a reatividade, validação e cliques (`tests/Feature/Livewire/TaskListTest.php`).

---

## 3. As Principais Expectativas do Pest (`expect()`)

O Pest usa a função `expect()` encadeada. Veja as mais comuns no dia a dia:

| Expectativa | O que faz | Exemplo |
| :--- | :--- | :--- |
| `toBe()` | Compara valor e tipo estrito (`===`). | `expect($status)->toBe(TaskStatus::Pending);` |
| `toEqual()` | Compara equivalência de valores (`==`). | `expect($data)->toEqual(['a' => 1]);` |
| `toBeTrue()` / `toBeFalse()` | Verifica booleano. | `expect($user->isAdmin())->toBeTrue();` |
| `toBeNull()` / `not->toBeNull()` | Verifica nulidade. | `expect($task->deadline_at)->not->toBeNull();` |
| `toBeInstanceOf()` | Verifica se é instância da classe. | `expect($task->deadline_at)->toBeInstanceOf(CarbonImmutable::class);` |
| `toHaveCount()` | Verifica quantidade de itens em array/collection. | `expect($project->tasks)->toHaveCount(3);` |
| `toThrow()` | Espera que uma exceção seja lançada. | `expect(fn() => $action->run())->toThrow(DomainException::class);` |

---

## 4. Testando Banco de Dados com Pest

Para testes de banco, usamos a trait `Illuminate\Foundation\Testing\RefreshDatabase;`:

```php
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('salva e recupera tarefa com cast correto de enum e json', function () {
    $user = User::factory()->create();

    $task = Task::create([
        'user_id' => $user->id,
        'title' => 'Implementar Pest',
        'status' => TaskStatus::InProgress,
        'priority' => TaskPriority::High,
        'metadata' => ['browser' => 'Chrome', 'ip' => '127.0.0.1'],
    ]);

    // Verifica se salvou no banco
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'status' => 'in_progress', // Salvo como string no banco
    ]);

    // Recarrega do banco e checa os Casts
    $freshTask = $task->fresh();
    expect($freshTask->status)->toBe(TaskStatus::InProgress)
        ->and($freshTask->metadata['browser'])->toBe('Chrome');
});
```

---

## 5. Testando Policies com Pest

```php
use App\Enums\UserRole;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

test('apenas o criador ou admin pode atualizar a tarefa', function () {
    $owner = User::factory()->create(['role' => UserRole::Developer]);
    $stranger = User::factory()->create(['role' => UserRole::Developer]);
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $task = Task::factory()->for($owner)->create();

    // Owner pode atualizar:
    expect($owner->can('update', $task))->toBeTrue();

    // Stranger NÃO pode atualizar:
    expect($stranger->can('update', $task))->toBeFalse();

    // Admin pode atualizar (bypass da Policy):
    expect($admin->can('update', $task))->toBeTrue();
});
```

---

## 6. Testando Componentes Livewire com Pest

O Pest se integra nativamente ao Livewire para testar digitação, validação e cliques em botões:

```php
use App\Livewire\TaskList;
use App\Models\Project;
use App\Models\User;
use Livewire\Livewire;

test('renderiza lista e cria tarefa com sucesso via livewire', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $this->actingAs($user);

    Livewire::test(TaskList::class, ['projectId' => $project->id])
        ->assertOk()
        ->set('newTitle', 'Minha Nova Tarefa Reativa')
        ->call('createTask')
        ->assertHasNoErrors()
        ->assertDispatched('notify');

    $this->assertDatabaseHas('tasks', [
        'project_id' => $project->id,
        'title' => 'Minha Nova Tarefa Reativa',
    ]);
});

test('valida que o título da tarefa é obrigatório no livewire', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $this->actingAs($user);

    Livewire::test(TaskList::class, ['projectId' => $project->id])
        ->set('newTitle', '')
        ->call('createTask')
        ->assertHasErrors(['newTitle' => 'required']);
});
```

---

## 7. Rodando os Testes no Terminal

```bash
# Rodar todos os testes
php artisan test
# ou diretamente com pest
./vendor/bin/pest

# Rodar apenas um arquivo específico
./vendor/bin/pest tests/Feature/Livewire/TaskListTest.php

# Rodar com filtro de nome de teste
./vendor/bin/pest --filter="cria tarefa"
```
