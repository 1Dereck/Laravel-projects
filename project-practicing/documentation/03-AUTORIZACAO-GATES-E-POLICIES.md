# 🛡️ Guia de Estudo: Autorização com Gates e Policies no Laravel

## 1. Autenticação vs Autorização: Qual a Diferença?

- **Autenticação (AuthN):** *"Quem é você?"* (Login, Senha, Sessão, Token, 2FA).
- **Autorização (AuthZ):** *"Você tem permissão para fazer ESTA ação específica NESTE recurso?"* (Criar projeto, editar a tarefa de outro usuário, deletar um cliente).

---

## 2. Por Que Usar Policies?

### ❌ O Jeito Antigo e Desorganizado:
Antigamente, as verificações de permissão ficavam espalhadas e duplicadas em vários lugares:
```php
// No Controller:
if ($user->role !== 'admin' && $task->user_id !== $user->id) {
    abort(403);
}

// No Blade:
@if(auth()->user()->role === 'admin' || $task->user_id === auth()->id())
    <button>Editar</button>
@endif

// No Livewire:
// ... mesma condição repetida pela 3ª vez!
```
**Se a regra de negócio mudasse, você teria que caçar todos os `if`s do projeto.**

### ✅ A Solução Moderna: **Policies**
Uma **Policy** centraliza toda a lógica de permissões de um determinado Model em uma única classe limpa.

---

## 3. Gates vs Policies: Quando Usar Cada Um?

| Recurso | Quando Usar? | Exemplo |
| :--- | :--- | :--- |
| **Gate** | Ações globais que **NÃO** dependem de um Model específico. | "Pode acessar o painel admin?", "Pode visualizar logs do sistema?". |
| **Policy** | Ações específicas atreladas a um **Model** (CRUD e ações de negócio). | "Pode editar este `$project`?", "Pode aprovar esta `$task`?". |

---

## 4. Criando e Estruturando uma Policy Moderna

Para criar uma Policy vinculada a um Model:

```bash
php artisan make:policy TaskPolicy --model=Task
```

> **Dica Laravel 11+:** As Policies são **auto-descobertas** pelo Laravel automaticamente (não precisa mais registrá-las manualmente no `AuthServiceProvider`!).

### Exemplo: `app/Policies/TaskPolicy.php`

```php
namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Hook 'before': Executa antes de qualquer outro método da policy.
     * Útil para Super Admins terem acesso total irrestrito.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === UserRole::Admin) {
            return true; // Admin pode tudo sem checagens extras
        }

        return null; // Deixa o fluxo continuar para o método específico
    }

    /**
     * Determina se o usuário pode listar as tarefas.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determina se o usuário pode visualizar uma tarefa específica.
     */
    public function view(User $user, Task $task): bool
    {
        // Gerentes ou o criador ou o responsável pela tarefa
        return $user->role === UserRole::ProjectManager
            || $task->user_id === $user->id
            || $task->assigned_to_id === $user->id;
    }

    /**
     * Determina se o usuário pode criar tarefas.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::Admin, UserRole::ProjectManager, UserRole::Developer]);
    }

    /**
     * Determina se o usuário pode atualizar a tarefa.
     */
    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->user_id 
            || $user->id === $task->assigned_to_id;
    }

    /**
     * Determina se o usuário pode deletar a tarefa.
     * Retornando uma Resposta Customizada com mensagem explicativa:
     */
    public function delete(User $user, Task $task): Response
    {
        return $user->id === $task->user_id
            ? Response::allow()
            : Response::deny('Você só pode excluir tarefas criadas por você.');
    }
}
```

---

## 5. Como Usar as Policies nos Diferentes Pontos da Aplicação

### A) Em Controllers:
```php
use Illuminate\Support\Facades\Gate;

public function update(Request $request, Task $task)
{
    // Lança HTTP 403 automaticamente se não autorizado:
    Gate::authorize('update', $task);

    // ... lógica de atualização ...
}
```

### B) Em Form Requests:
```php
class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        $task = $this->route('task');
        return $this->user()->can('update', $task);
    }
}
```

### C) No Blade:
```blade
@can('update', $task)
    <button class="btn btn-primary">Editar Tarefa</button>
@elsecan('view', $task)
    <button class="btn btn-secondary">Apenas Visualizar</button>
@endcan

@cannot('delete', $task)
    <span class="text-muted">Exclusão não permitida</span>
@endcannot
```

### D) No Livewire (Essencial para Segurança!):
Componentes Livewire recebem requisições diretas do front-end. Você **sempre** deve autorizar as ações dentro do componente:

```php
namespace App\Livewire;

use App\Models\Task;
use Livewire\Component;

class TaskCard extends Component
{
    public Task $task;

    public function deleteTask(): void
    {
        // 🔒 Protege a ação contra requisições forjadas
        $this->authorize('delete', $this->task);

        $this->task->delete();
        $this->dispatch('task-deleted');
    }
}
```

---

## 6. Resumo das Melhores Práticas

1. **Nunca confie apenas em esconder o botão no front-end (`@can`):** O usuário pode inspecionar o código ou fazer a requisição HTTP direta. A Policy **deve** ser chamada no Back-end / Livewire Action.
2. **Combine Enums com Policies:** Fica expressivo e auto-explicativo (`$user->role === UserRole::Admin`).
3. **Use `Response::deny('Mensagem')`** quando quiser dar um feedback amigável ao usuário do porquê a ação foi bloqueada.
