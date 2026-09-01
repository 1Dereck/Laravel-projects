# 🏷️ Guia de Estudo: Enums Modernos no PHP e Laravel

## 1. O Problema que os Enums Resolvem

### 👴 Como era feito antigamente:
Antigamente, para representar o status de um pedido ou papel de um usuário, usávamos **Strings Mágicas** ou **Constantes soltas**:

```php
// Jeito antigo (propenso a bugs e sem type-safety)
class User extends Authenticatable {
    const ROLE_ADMIN = 'admin';
    const ROLE_MEMBER = 'member';
}

// No Controller ou Model:
if ($user->role === 'adimin') { // ❌ Um simples erro de digitação passa despercebido pelo PHP!
    // ...
}
```

**Problemas do jeito antigo:**
1. Aceitava qualquer string sem acusar erro no editor ou no runtime.
2. Não dava para anexar comportamentos (ex: "qual é a cor da badge desse status?", "quais são os status seguintes permitidos?").
3. As traduções e labels ficavam espalhadas em `helpers`, `switch/case` em Blade ou arrays na Model.

---

## 2. O Que são Enums no PHP 8.1+?

Enums são tipos de dados que representam um conjunto fixo de valores possíveis. No PHP moderno, eles são **cidadãos de primeira classe** (objetos fortemente tipados).

Existem dois tipos:
1. **Pure Enum:** Apenas o nome do caso (`case Pending; case Approved;`).
2. **Backed Enum (Mais Usado no Laravel):** Cada caso tem um valor primitivo associado (geralmente `string` ou `int`), que é o valor salvo no banco de dados.

### Exemplo Moderno: `UserRole.php`

```php
namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case ProjectManager = 'manager';
    case Developer = 'developer';
    case Client = 'client';

    // ✨ Métodos dentro do Enum: Centraliza regras do negócio!
    public function label(): string
    {
        return match($this) {
            self::Admin => 'Administrador',
            self::ProjectManager => 'Gerente de Projeto',
            self::Developer => 'Desenvolvedor',
            self::Client => 'Cliente',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::Admin => 'red',
            self::ProjectManager => 'purple',
            self::Developer => 'blue',
            self::Client => 'emerald',
        };
    }

    public function canManageProjects(): bool
    {
        return in_array($this, [self::Admin, self::ProjectManager]);
    }
}
```

---

## 3. Integrando Enums no Eloquent (Laravel Moderno)

O Laravel faz a conversão automática (**Casting**) bidirecional:
- Ao ler do banco de dados (onde está a string `'admin'`), o Laravel transforma no objeto `UserRole::Admin`.
- Ao salvar no banco, o Laravel extrai a string `'admin'` automaticamente.

### Na Model `User.php`:

```php
namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    // 🔥 Laravel 11+: Usamos o método casts() em vez da propriedade $casts
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class, // <-- Cast automático para o Enum!
        ];
    }
}
```

### Usando no código do dia a dia:

```php
// Criando usuário
$user = User::create([
    'name' => 'Dereck',
    'email' => 'dereck@example.com',
    'password' => 'secret123',
    'role' => UserRole::Admin, // Passamos o Enum diretamente!
]);

// Verificando papéis com total Type Hint e Autocomplete
if ($user->role === UserRole::Admin) {
    // O autocomplete do editor funciona perfeitamente
}

// Usando os métodos do Enum diretamente no Blade:
// <span class="badge badge-{{ $user->role->badgeColor() }}">
//     {{ $user->role->label() }}
// </span>
```

---

## 4. Validação com Enums em Form Requests / Livewire

Para validar formulários garantindo que o usuário só envie valores válidos do Enum, o Laravel oferece uma regra nativa:

```php
use App\Enums\UserRole;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rule;

// Em um FormRequest ou no Livewire:
$rules = [
    'role' => ['required', Rule::enum(UserRole::class)],
    // ou: 'role' => ['required', new Enum(UserRole::class)],
];
```

Se alguém tentar enviar `"hacker"` ou `"superuser"`, o Laravel rejeita a requisição automaticamente com uma mensagem de erro de validação.

---

## 5. Resumo e Boas Práticas

| Prática | Como fazer |
| :--- | :--- |
| **Onde criar os Enums?** | Na pasta `app/Enums/` (ex: `TaskStatus.php`, `UserRole.php`, `Priority.php`). |
| **Qual tipo usar no banco?** | `string` (para Backed Enums do tipo `string`). |
| **Como converter no Model?** | Declarar `'campo' => MeuEnum::class` no método `casts()`. |
| **Como validar?** | `Rule::enum(MeuEnum::class)` nas regras de validação. |
| **Onde colocar labels e cores?** | Métodos `label()` e `color()` direto dentro do arquivo do Enum. |
